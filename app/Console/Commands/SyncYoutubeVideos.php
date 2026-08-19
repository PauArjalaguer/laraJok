<?php

namespace App\Console\Commands;

use App\Services\YoutubeVideoService;
use Illuminate\Console\Command;

class SyncYoutubeVideos extends Command
{
    protected $signature = 'videos:sync';

    protected $description = 'Sync videos from configured YouTube channels and playlists';

    public function handle(YoutubeVideoService $service): int
    {
        $this->info('Starting YouTube videos sync...');

        $totalSynced = $service->syncAll();

        $this->info("Completed YouTube videos sync. Total imported/updated: {$totalSynced} videos.");

        return Command::SUCCESS;
    }
}
