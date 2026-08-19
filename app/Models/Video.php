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
}
