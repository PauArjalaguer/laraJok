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
            $videos = Video::where('id', $specificId)->get();
        } else {
            // Obtenim la data del partit mes antic a la base de dades per no processar videos anteriors
            $oldestMatchDate = \Illuminate\Support\Facades\DB::table('matches')
                ->whereNotNull('matchDate')
                ->where('matchDate', '!=', '0000-00-00')
                ->where('matchDate', '>', '1970-01-01')
                ->min('matchDate');

            $minAllowedDate = $oldestMatchDate ? \Carbon\Carbon::parse($oldestMatchDate)->subDays(15)->format('Y-m-d') : null;

            // Nomes processem videos amb idMatch null, que no hagin superat el maxim de reintents i posteriors a la data minima
            $query = Video::whereNull('idMatch')
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
            $this->line("[{$current}/{$total}] Analitzant Video #{$video->id} (intent {$triesCount}/{$maxTries}): '{$video->title}'...");

            try {
                $matchedId = $aiService->matchVideoToMatch($video);

                if ($matchedId) {
                    $video->update([
                        'idMatch' => $matchedId,
                        'match_tries' => $triesCount + 1
                    ]);
                    $this->info("  -> VINCULAT EXITOSAMENT AMB PARTIT ID: {$matchedId}");
                    $matchedCount++;
                } else {
                    $video->increment('match_tries');
                    $newTries = $triesCount + 1;
                    if ($newTries >= $maxTries) {
                        $this->warn("  -> No s'ha trobat partit coincident. Ha assolit el limit de {$maxTries} reintents (no es tornara a provar).");
                    } else {
                        $this->warn("  -> No s'ha trobat partit coincident. Reintents restants: " . ($maxTries - $newTries));
                    }
                }
            } catch (\Exception $e) {
                $video->increment('match_tries');
                $this->error("  -> Error analitzant video #{$video->id}: " . $e->getMessage());
            }

            // Pausa de cortesia per respectar rate limits
            usleep(300000);
        }

        $this->info("Proces completat: {$matchedCount} de {$total} videos vinculats a les seves actes.");

        return 0;
    }
}
