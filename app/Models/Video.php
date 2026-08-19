<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        'video_channel_id',
        'youtube_id',
        'title',
        'description',
        'thumbnail_url',
        'url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(VideoChannel::class, 'video_channel_id');
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeByChannel($query, $channelId)
    {
        if (empty($channelId)) {
            return $query;
        }

        return $query->where('video_channel_id', $channelId);
    }

    public function scopeByDateFilter($query, $filter)
    {
        if (empty($filter)) {
            return $query;
        }

        $now = now();

        switch ($filter) {
            case 'week':
                return $query->where('published_at', '>=', $now->copy()->subDays(7));
            case 'month':
                return $query->where('published_at', '>=', $now->copy()->subDays(30));
            case '3months':
                return $query->where('published_at', '>=', $now->copy()->subMonths(3));
            case 'year':
                return $query->where('published_at', '>=', $now->copy()->subYear());
            default:
                return $query;
        }
    }

    /**
     * Get videos associated with a club by matching core town/club keywords in title, description, or channel.
     */
    public static function getVideosByClubName(string $clubName, int $limit = 8)
    {
        // 1. Remove punctuation (commas, dots, dashes, quotes)
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clubName);
        
        // 2. Remove single letter abbreviations and generic club prefixes/suffixes
        $clean = preg_replace('/\b(Club|Hoquei|Patí|Pati|Associació|Esportiva|Secció|CP|HC|CE|CH|de|del|dels|d|i|esportiu|esportiva|oficial|femení|femeni|masculí|masculi|C|E|H|P|A|B|D|LES|LOS|LAS|EL|LA)\b/iu', ' ', $clean);
        
        // 3. Extract words >= 3 chars
        $words = array_values(array_filter(explode(' ', trim(preg_replace('/\s+/', ' ', $clean))), fn($w) => mb_strlen($w) >= 3));

        if (empty($words)) {
            $fallback = trim(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clubName));
            $keywords = [$fallback];
        } else {
            $keywords = [implode(' ', $words)];
            foreach ($words as $w) {
                if (!in_array($w, $keywords)) {
                    $keywords[] = $w;
                }
            }
        }

        return self::with('channel')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $kw) {
                    $query->orWhere('title', 'like', "%{$kw}%")
                          ->orWhere('description', 'like', "%{$kw}%")
                          ->orWhereHas('channel', function ($q) use ($kw) {
                              $q->where('name', 'like', "%{$kw}%");
                          });
                }
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
