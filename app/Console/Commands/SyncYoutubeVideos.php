<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\YoutubeVideoService;
use Illuminate\Console\Command;

class SyncYoutubeVideos extends Command
{
    protected $signature = 'videos:sync {--fix-dates : Force fix stream dates by inspecting YouTube watch pages}';

    protected $description = 'Sync videos from configured YouTube channels and playlists';

    public function handle(YoutubeVideoService $service): int
    {
        @ini_set('memory_limit', '512M');
        if (class_exists(\Illuminate\Support\Facades\DB::class)) {
            \Illuminate\Support\Facades\DB::disableQueryLog();
        }

        $this->info('Starting YouTube videos sync...');

        $totalSynced = $service->syncAll();

        // Auto-fix any stream videos whose published_at was set to today
        $query = Video::where('description', 'like', '%Retransmissió en directe%');
        if (!$this->option('fix-dates')) {
            $query->whereDate('published_at', now()->toDateString());
        }

        $count = $query->count();
        if ($count > 0) {
            $this->info("Fixing dates for {$count} live stream videos...");
            foreach ($query->cursor() as $stream) {
                $realDate = $service->fetchRealVideoDate($stream->youtube_id);
                if ($realDate) {
                    $stream->update(['published_at' => $realDate]);
                    $this->line(" - Fixed Video #{$stream->id} ({$stream->title}): {$realDate}");
                }
                unset($stream);
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
        }

        $this->info("Completed YouTube videos sync. Total imported/updated: {$totalSynced} videos.");

        return Command::SUCCESS;
    }
}
