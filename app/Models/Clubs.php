<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Clubs extends Model
{
    use HasFactory;

    public static function clubsList()
    {
        $cacheKey = 'clubsList';
        $ttl = 10000;
        return Cache::remember($cacheKey, $ttl, function () {
            $clubsList = Clubs::select('idClub as value', 'clubName as label','clubImage')
                ->orderby('clubName')
                ->where('idClub', '>', 1)
                ->where('numberOfTeams', '>=', 1)->get();
            return $clubsList;
        });
    }
}
