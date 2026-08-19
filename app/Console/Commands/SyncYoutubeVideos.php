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
        $this->info('Starting YouTube videos sync...');

        $totalSynced = $service->syncAll();

        // Auto-fix any stream videos whose published_at was temporarily set to today
        $streamsToFix = Video::where('description', 'like', '%Retransmissió en directe%')
            ->whereDate('published_at', now()->toDateString())
            ->get();

        if ($this->option('fix-dates')) {
            $streamsToFix = Video::where('description', 'like', '%Retransmissió en directe%')->get();
        }

        if ($streamsToFix->count() > 0) {
            $this->info("Fixing dates for {$streamsToFix->count()} live stream videos...");
            foreach ($streamsToFix as $stream) {
                $realDate = $service->fetchRealVideoDate($stream->youtube_id);
                if ($realDate) {
                    $stream->update(['published_at' => $realDate]);
                    $this->line(" - Fixed Video #{$stream->id} ({$stream->title}): {$realDate}");
                }
            }
        }

        $this->info("Completed YouTube videos sync. Total imported/updated: {$totalSynced} videos.");

        return Command::SUCCESS;
    }
}
