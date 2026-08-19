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

        // Fix dates for videos
        if ($this->option('fix-dates')) {
            $query = Video::orderBy('id', 'asc');
        } else {
            $query = Video::where(function ($q) {
                $q->whereDate('published_at', now()->toDateString())
                  ->orWhere('description', 'like', '%Retransmissió en directe%');
            });
        }

        $count = $query->count();
        if ($count > 0) {
            $this->info("Fixing dates for {$count} videos...");
            $current = 0;
            foreach ($query->cursor() as $stream) {
                $current++;
                $realDate = $service->fetchRealVideoDate($stream->youtube_id);
                if ($realDate) {
                    $existingDate = $stream->published_at ? $stream->published_at->format('Y-m-d H:i:s') : null;
                    if ($existingDate !== $realDate) {
                        $stream->update(['published_at' => $realDate]);
                        $this->line(" - [{$current}/{$count}] Updated Video #{$stream->id} ({$stream->title}): {$existingDate} -> {$realDate}");
                    } else {
                        $this->line(" - [{$current}/{$count}] OK Video #{$stream->id}: {$realDate}");
                    }
                } else {
                    $this->line(" - [{$current}/{$count}] Checked Video #{$stream->id}: date unchanged");
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
