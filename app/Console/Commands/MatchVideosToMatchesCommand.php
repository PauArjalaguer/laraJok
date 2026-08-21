<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\AiService;
use Illuminate\Console\Command;

class MatchVideosToMatchesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:match-to-acta 
                            {--limit=300 : Limit de videos a processar} 
                            {--max-tries=1 : Nombre maxim de reintents per video abans de descartar-lo} 
                            {--reset-tries : Reinicia el comptador de reintents de tots els videos pendents} 
                            {--id= : ID de video concret a processar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigna videos de YouTube pendents al seu partit/acta corresponent mitjancant IA (amb control de reintents)';

    /**
     * Execute the console command.
     */
    public function handle(AiService $aiService): int
    {
        $specificId = $this->option('id');
        $limit = (int)$this->option('limit');
        $maxTries = (int)$this->option('max-tries');

        // Opcio per reiniciar reintents
        if ($this->option('reset-tries')) {
            $resetCount = Video::whereNull('idMatch')->where('match_tries', '>', 0)->update(['match_tries' => 0]);
            $this->info("S'ha reiniciat el comptador de reintents per a {$resetCount} videos.");
        }

        if ($specificId) {
            $videos = Video::with('channel')->where('id', $specificId)->get();
        } else {
            // Obtenim la data del partit mes antic a la base de dades per no processar videos anteriors
            $oldestMatchDate = \Illuminate\Support\Facades\DB::table('matches')
                ->whereNotNull('matchDate')
                ->where('matchDate', '!=', '0000-00-00')
                ->where('matchDate', '>', '1970-01-01')
                ->min('matchDate');

            $minAllowedDate = $oldestMatchDate ? \Carbon\Carbon::parse($oldestMatchDate)->subDays(15)->format('Y-m-d') : null;

            // Nomes processem videos amb idMatch null, que no hagin superat el maxim de reintents i posteriors a la data minima
            $query = Video::with('channel')
                ->whereNull('idMatch')
                ->where('match_tries', '<', $maxTries);

            if ($minAllowedDate) {
                $query->where(function ($q) use ($minAllowedDate) {
                    $q->where('published_at', '>=', $minAllowedDate)
                      ->orWhereNull('published_at');
                });
            }

            $videos = $query->orderBy('published_at', 'asc')
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->get();
        }

        $total = $videos->count();
        if ($total === 0) {
            $this->info("No hi ha videos pendents per assignar a actes (o tots han superat el limit de {$maxTries} reintents).");
            return 0;
        }

        $this->info("Iniciant assignacio amb IA per a {$total} videos (maxim {$maxTries} reintents per video)...");

        $matchedCount = 0;

        foreach ($videos as $index => $video) {
            $current = $index + 1;
            $triesCount = (int)$video->match_tries;
            $channelLabel = $video->channel ? " (Canal: {$video->channel->name})" : "";
            $this->line("\n==================================================");
            $this->line("[{$current}/{$total}] Analitzant Video #{$video->id}: '{$video->title}'{$channelLabel}");

            $trace = [];

            try {
                $matchedId = $aiService->matchVideoToMatch($video, $trace);

                // Mostrem el trace de la lògica pas a pas
                $ext = $trace['extracted'] ?? [];
                $extStr = [];
                if (!empty($ext['local'])) $extStr[] = "Local: '{$ext['local']}'";
                if (!empty($ext['visitor'])) $extStr[] = "Visitant: '{$ext['visitor']}'";
                if (!empty($ext['group'])) $extStr[] = "Grup/Cat: '{$ext['group']}'";
                if (isset($ext['localResult']) && $ext['localResult'] !== null) $extStr[] = "Resultat: {$ext['localResult']}-{$ext['visitorResult']}";
                if (!empty($ext['round'])) $extStr[] = "Jornada: {$ext['round']}";
                if (!empty($ext['date'])) $extStr[] = "Data: {$ext['date']}";

                $this->comment("  ├─ [IA Extracció] " . (!empty($extStr) ? implode(' | ', $extStr) : "No s'han extret dades estructurades"));
                if (!empty($trace['vision_used'])) {
                    $this->comment("  ├─ [Visió Multimodal] S'ha analitzat la miniatura (thumbnail) per desambiguar la categoria.");
                }

                $fil = $trace['filters'] ?? [];
                $filStr = [];
                if (!empty($fil['local_key'])) $filStr[] = "local LIKE '%{$fil['local_key']}%'";
                if (!empty($fil['visitor_key'])) $filStr[] = "visitor LIKE '%{$fil['visitor_key']}%'";
                if (!empty($fil['exact_date'])) $filStr[] = "data: {$fil['exact_date']}";
                if (!empty($fil['date_range'])) $filStr[] = "rang: {$fil['date_range']}";
                if (!empty($fil['category_token'])) $filStr[] = "cat: '{$fil['category_token']}'";
                if (!empty($fil['score'])) $filStr[] = "marcador: {$fil['score']}";

                $this->comment("  ├─ [Cerca SQL] " . (!empty($filStr) ? implode(', ', $filStr) : "Sense filtres SQL suficients"));

                $candidates = $trace['candidates'] ?? [];
                $candCount = count($candidates);
                $this->comment("  ├─ [Candidats BD] {$candCount} partits trobats a la base de dades:");
                foreach ($candidates as $c) {
                    $this->line("  │    • #{$c['idMatch']}: {$c['match']} | Data: {$c['date']} | Grup: {$c['group']}");
                }

                if ($matchedId) {
                    $video->update([
                        'idMatch' => $matchedId,
                        'match_tries' => $triesCount + 1
                    ]);
                    $this->info("  └─ [ÈXIT] Vinculat automàticament amb el partit ID: #{$matchedId}");
                    $matchedCount++;
                } else {
                    $video->increment('match_tries');
                    $newTries = $triesCount + 1;
                    if ($newTries >= $maxTries) {
                        $this->warn("  └─ [DESCARTAT] No s'ha trobat coincidència unívoca. Ha assolit el límit de {$maxTries} intent/s.");
                    } else {
                        $this->warn("  └─ [SENSE PARTIT] No s'ha trobat coincidència unívoca. Reintents restants: " . ($maxTries - $newTries));
                    }
                }

                \Illuminate\Support\Facades\Log::info("MatchVideoToActa Trace Video #{$video->id}", $trace);

            } catch (\Exception $e) {
                $video->increment('match_tries');
                $this->error("  └─ [ERROR] " . $e->getMessage());
                \Illuminate\Support\Facades\Log::error("MatchVideoToActa Error Video #{$video->id}: " . $e->getMessage(), ['trace' => $trace]);
            }

            // Pausa de cortesia per respectar rate limits
            usleep(300000);
        }

        $this->line("\n==================================================");
        $this->info("Procés completat: {$matchedCount} de {$total} vídeos vinculats a les seves actes.");

        return 0;
    }
}
