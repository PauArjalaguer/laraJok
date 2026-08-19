<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideoChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Exception;
use Normalizer;

class YoutubeVideoService
{
    /**
     * Normalize Unicode pseudo-font styles (Mathematical Bold, Italic, etc.) into standard text characters.
     */
    public function cleanUnicodeText(string $text): string
    {
        if (class_exists(Normalizer::class)) {
            $normalized = Normalizer::normalize($text, Normalizer::FORM_KC);
            if ($normalized !== false) {
                return $normalized;
            }
        }
        return $text;
    }

    /**
     * Extract exact published date from YouTube page HTML.
     */
    public function extractPublishedDate(string $html): ?string
    {
        if (preg_match('/itemprop="datePublished"\s+content="([^"]+)"/', $html, $m)) {
            return date('Y-m-d H:i:s', strtotime($m[1]));
        }
        if (preg_match('/itemprop="uploadDate"\s+content="([^"]+)"/', $html, $m)) {
            return date('Y-m-d H:i:s', strtotime($m[1]));
        }
        if (preg_match('/"publishDate":"([^"]+)"/', $html, $m)) {
            return date('Y-m-d H:i:s', strtotime($m[1]));
        }
        if (preg_match('/"uploadDate":"([^"]+)"/', $html, $m)) {
            return date('Y-m-d H:i:s', strtotime($m[1]));
        }
        return null;
    }

    /**
     * Fetch real published date for a specific YouTube video ID via watch page HTML.
     */
    public function fetchRealVideoDate(string $youtubeId): ?string
    {
        try {
            $url = "https://www.youtube.com/watch?v={$youtubeId}";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return $this->extractPublishedDate($response->body());
            }
        } catch (Exception $e) {
            Log::warning("Error fetching real date for video {$youtubeId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Scrape video IDs directly from channel /videos and /streams pages to bypass the 15-item RSS limit.
     */
    public function fetchChannelPageVideos(VideoChannel $channel): int
    {
        if ($channel->type !== 'channel') {
            return 0;
        }

        $handleOrId = $channel->identifier ?: $channel->channel_id;
        if (empty($handleOrId)) {
            return 0;
        }

        $base = str_starts_with($handleOrId, '@')
            ? "https://www.youtube.com/{$handleOrId}"
            : "https://www.youtube.com/channel/{$handleOrId}";

        $pages = ["{$base}/videos", "{$base}/streams"];
        $videoIds = [];

        foreach ($pages as $url) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                    'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
                ])->timeout(12)->get($url);

                if ($response->successful()) {
                    $html = $response->body();
                    if (preg_match_all('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $m)) {
                        foreach ($m[1] as $id) {
                            $videoIds[$id] = true;
                        }
                    }
                }
            } catch (Exception $e) {
                Log::warning("Error scraping page {$url}: " . $e->getMessage());
            }
        }

        $imported = 0;
        foreach (array_keys($videoIds) as $videoId) {
            $existing = Video::where('youtube_id', $videoId)->first();
            if ($existing && $existing->title && $existing->published_at && !$existing->published_at->isToday()) {
                continue;
            }

            try {
                $watchUrl = "https://www.youtube.com/watch?v={$videoId}";
                $wResp = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                    'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
                ])->timeout(8)->get($watchUrl);

                if ($wResp->successful()) {
                    $html = $wResp->body();
                    $title = null;

                    if (preg_match('/<title>(.*?)<\/title>/i', $html, $tm)) {
                        $title = str_replace([' - YouTube', ' - Youtube'], '', $tm[1]);
                        $title = $this->cleanUnicodeText(html_entity_decode($title));
                    }

                    if (empty($title) || $title === 'YouTube') {
                        $oembed = Http::get("https://www.youtube.com/oembed?url={$watchUrl}&format=json")->json();
                        if (!empty($oembed['title'])) {
                            $title = $this->cleanUnicodeText($oembed['title']);
                        }
                    }

                    if (empty($title)) {
                        continue;
                    }

                    $publishedAt = $this->extractPublishedDate($html);
                    if (!$publishedAt && $existing) {
                        $publishedAt = $existing->published_at;
                    }
                    if (!$publishedAt) {
                        $publishedAt = now();
                    }

                    Video::updateOrCreate(
                        ['youtube_id' => $videoId],
                        [
                            'video_channel_id' => $channel->id,
                            'title' => $title,
                            'description' => $existing ? $existing->description : "Vídeo de {$channel->name}",
                            'thumbnail_url' => "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg",
                            'url' => $watchUrl,
                            'published_at' => $publishedAt,
                        ]
                    );

                    $imported++;
                }
            } catch (Exception $e) {
                Log::warning("Error importing video {$videoId}: " . $e->getMessage());
            }
        }

        return $imported;
    }

    /**
     * Resolve channel avatar image URL from YouTube page HTML.
     */
    public function resolveChannelAvatar(VideoChannel $channel): ?string
    {
        if ($channel->avatar_url) {
            return $channel->avatar_url;
        }

        $url = $channel->url;
        if (empty($url) && $channel->identifier) {
            $handle = ltrim($channel->identifier, '@');
            $url = "https://www.youtube.com/@{$handle}";
        }

        if (empty($url)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $html = $response->body();
                $avatarUrl = null;

                if (preg_match('/<meta property="og:image" content="([^"]+)"/', $html, $m)) {
                    $avatarUrl = $m[1];
                } elseif (preg_match('/"avatar":\{"thumbnails":\[\{"url":"([^"]+)"/', $html, $m)) {
                    $avatarUrl = $m[1];
                }

                if ($avatarUrl) {
                    $channel->update(['avatar_url' => $avatarUrl]);
                    return $avatarUrl;
                }
            }
        } catch (Exception $e) {
            Log::warning("Error resolving avatar for channel #{$channel->id}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Resolve channel_id (UC...) from a YouTube handle (@handle) or channel URL.
     */
    public function resolveChannelId(string $handleOrUrl): ?string
    {
        // If it's already a channel ID starting with UC
        if (preg_match('/^UC[a-zA-Z0-9_-]{22}$/', $handleOrUrl)) {
            return $handleOrUrl;
        }

        $url = $handleOrUrl;
        if (!str_starts_with($url, 'http')) {
            $handle = ltrim($handleOrUrl, '@');
            $url = "https://www.youtube.com/@{$handle}";
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
            ])->timeout(12)->get($url);

            if ($response->successful()) {
                $html = $response->body();

                if (preg_match('/itemprop="channelId"\s+content="(UC[a-zA-Z0-9_-]+)"/', $html, $matches)) {
                    return $matches[1];
                }
                if (preg_match('/"externalId":"(UC[a-zA-Z0-9_-]+)"/', $html, $matches)) {
                    return $matches[1];
                }
                if (preg_match('/"channelId":"(UC[a-zA-Z0-9_-]+)"/', $html, $matches)) {
                    return $matches[1];
                }
                if (preg_match('/"browseId":"(UC[a-zA-Z0-9_-]+)"/', $html, $matches)) {
                    return $matches[1];
                }
                if (preg_match('/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]+)/', $html, $matches)) {
                    return $matches[1];
                }
            }
        } catch (Exception $e) {
            Log::error("Error resolving YouTube channel ID for {$handleOrUrl}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if a channel has an active or scheduled live stream.
     */
    public function checkLiveStream(VideoChannel $channel): int
    {
        if ($channel->type !== 'channel') {
            return 0;
        }

        $handleOrId = $channel->identifier ?: $channel->channel_id;
        if (empty($handleOrId)) {
            return 0;
        }

        $url = str_starts_with($handleOrId, '@') 
            ? "https://www.youtube.com/{$handleOrId}/live" 
            : "https://www.youtube.com/channel/{$handleOrId}/live";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $html = $response->body();
                $youtubeId = null;

                if (preg_match('/<link rel="canonical" href="https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})"/', $html, $m)) {
                    $youtubeId = $m[1];
                } elseif (preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $m)) {
                    $youtubeId = $m[1];
                }

                if ($youtubeId) {
                    $existingVideo = Video::where('youtube_id', $youtubeId)->first();
                    $publishedAt = null;

                    if ($existingVideo && $existingVideo->published_at && !$existingVideo->published_at->isToday()) {
                        $publishedAt = $existingVideo->published_at;
                    } else {
                        $publishedAt = $this->fetchRealVideoDate($youtubeId);
                    }

                    if (!$publishedAt && $existingVideo) {
                        $publishedAt = $existingVideo->published_at;
                    }

                    if (!$publishedAt) {
                        $publishedAt = now();
                    }

                    $title = null;
                    if (preg_match('/<title>(.*?)<\/title>/i', $html, $tm)) {
                        $title = str_replace([' - YouTube', ' - Youtube'], '', $tm[1]);
                        $title = $this->cleanUnicodeText(html_entity_decode($title));
                    }

                    if (empty($title) || $title === 'YouTube') {
                        $title = "Directe - {$channel->name}";
                    }

                    Video::updateOrCreate(
                        ['youtube_id' => $youtubeId],
                        [
                            'video_channel_id' => $channel->id,
                            'title' => $title,
                            'description' => "Retransmissió en directe / emesa per {$channel->name}",
                            'thumbnail_url' => "https://i.ytimg.com/vi/{$youtubeId}/hqdefault.jpg",
                            'url' => "https://www.youtube.com/watch?v={$youtubeId}",
                            'published_at' => $publishedAt,
                        ]
                    );
                    return 1;
                }
            }
        } catch (Exception $e) {
            Log::warning("Error checking live stream for {$channel->name}: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Sync videos for a specific VideoChannel.
     */
    public function syncChannel(VideoChannel $channel): int
    {
        if (!$channel->is_active) {
            return 0;
        }

        // Fetch channel avatar if missing
        if (empty($channel->avatar_url)) {
            $this->resolveChannelAvatar($channel);
        }

        $totalCount = 0;
        $feedUrl = null;

        if ($channel->type === 'playlist' || !empty($channel->playlist_id)) {
            $playlistId = $channel->playlist_id ?: $channel->identifier;
            $feedUrl = "https://www.youtube.com/feeds/videos.xml?playlist_id={$playlistId}";
        } else {
            $channelId = $channel->channel_id;

            if (empty($channelId)) {
                $channelId = $this->resolveChannelId($channel->identifier ?: $channel->url);
                if ($channelId) {
                    $channel->update(['channel_id' => $channelId]);
                }
            }

            if ($channelId) {
                $feedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";
            }
        }

        // 1. Fetch RSS feed first
        if ($feedUrl) {
            $totalCount += $this->fetchAndSaveRssFeed($feedUrl, $channel);
        }

        // 2. Scrape channel /videos and /streams pages to bypass 15-item RSS limit
        if ($channel->type === 'channel') {
            $totalCount += $this->fetchChannelPageVideos($channel);
            $totalCount += $this->checkLiveStream($channel);
        }

        return $totalCount;
    }

    /**
     * Fetch and parse YouTube RSS XML feed.
     */
    public function fetchAndSaveRssFeed(string $feedUrl, ?VideoChannel $channel = null): int
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'ca,es;q=0.9,en;q=0.8',
                'Cookie' => 'SOCS=CAESEwgDEgk2OTg5MjMwMTQaAmNhIAEaBgiA_LyaBg; CONSENT=YES+1',
            ])->timeout(15)->get($feedUrl);

            if (!$response->successful()) {
                Log::error("Failed to fetch RSS feed from {$feedUrl}: HTTP {$response->status()}");
                return 0;
            }

            $xmlString = $response->body();
            $xml = new SimpleXMLElement($xmlString);

            $ytNs = 'http://www.youtube.com/xml/yt/1.0';
            $mediaNs = 'http://search.yahoo.com/mrss/';

            $importedCount = 0;

            foreach ($xml->entry as $entry) {
                $ytChildren = $entry->children($ytNs);
                $youtubeId = (string) $ytChildren->videoId;

                if (empty($youtubeId)) {
                    $youtubeId = (string) $entry->id;
                    $youtubeId = str_replace('yt:video:', '', $youtubeId);
                }

                if (empty($youtubeId)) {
                    continue;
                }

                $title = $this->cleanUnicodeText((string) $entry->title);
                $published = (string) $entry->published;
                $publishedAt = date('Y-m-d H:i:s', strtotime($published));

                $mediaGroup = $entry->children($mediaNs)->group;
                $description = null;
                $thumbnailUrl = "https://i.ytimg.com/vi/{$youtubeId}/hqdefault.jpg";

                if ($mediaGroup) {
                    if (isset($mediaGroup->description)) {
                        $description = $this->cleanUnicodeText((string) $mediaGroup->description);
                    }
                    if (isset($mediaGroup->thumbnail)) {
                        $attributes = $mediaGroup->thumbnail->attributes();
                        if (isset($attributes['url'])) {
                            $thumbnailUrl = (string) $attributes['url'];
                        }
                    }
                }

                $videoUrl = "https://www.youtube.com/watch?v={$youtubeId}";

                Video::updateOrCreate(
                    ['youtube_id' => $youtubeId],
                    [
                        'video_channel_id' => $channel ? $channel->id : null,
                        'title' => $title,
                        'description' => $description,
                        'thumbnail_url' => $thumbnailUrl,
                        'url' => $videoUrl,
                        'published_at' => $publishedAt,
                    ]
                );

                $importedCount++;
            }

            return $importedCount;

        } catch (Exception $e) {
            Log::error("Exception parsing YouTube RSS feed ({$feedUrl}): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sync all active channels.
     */
    public function syncAll(): int
    {
        $channels = VideoChannel::where('is_active', true)->get();
        $total = 0;

        foreach ($channels as $channel) {
            $count = $this->syncChannel($channel);
            $total += $count;
        }

        return $total;
    }
}
