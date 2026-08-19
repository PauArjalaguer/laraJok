<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoChannel extends Model
{
    use HasFactory;

    protected $table = 'video_channels';

    protected $fillable = [
        'name',
        'type',
        'identifier',
        'channel_id',
        'playlist_id',
        'url',
        'avatar_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class, 'video_channel_id');
    }
}
