<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Leagues extends Model
{
    use HasFactory;
    public static function leaguesList()
    {
        $cacheKey = 'leaguesList';
        $ttl = 6000;
        return Cache::remember($cacheKey, $ttl, function () {
            $leaguesList = Leagues::join('seasons', 'leagues.idSeason', '=', 'seasons.idSeason')->join('phases', 'phases.idLeague', '=', 'leagues.idLeague')
                ->select('phases.idGroup as value', DB::raw("CONCAT(' ',groupName,' (',seasonName,')') AS label"))->orderby('leagues.idSeason', 'desc')
                ->orderby('leagues.idCategory', 'asc')->get();
            return $leaguesList;
        });
    }
}
