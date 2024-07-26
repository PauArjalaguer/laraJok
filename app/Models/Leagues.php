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
        $ttl = 60000;
        return Cache::remember($cacheKey, $ttl, function () {
            $leaguesList = Leagues::join('seasons', 'leagues.idSeason', '=', 'seasons.idSeason')->join('phases', 'phases.idLeague', '=', 'leagues.idLeague')
                ->select('leagueName as leagueName','phases.idGroup as value', DB::raw("CONCAT(' ',groupName,' (',seasonName,')') AS label"))->where('phases.numberofmatches','!=', 0)->orderby('leagues.idSeason', 'desc')
                ->orderby('leagues.idCategory', 'asc')->get();
            return $leaguesList;
        });
    }  

    public static function totalPlayed($idLeague)
    {
        $totalMatches = \App\Models\Matches::where('idGroup', $idLeague)->count();

        $playedMatches = \App\Models\Matches::where('idGroup', $idLeague)
            ->whereNotNull('localResult')
            ->count();

        $percentagePlayed = $totalMatches > 0 ? ($playedMatches / $totalMatches) * 100 : 0;

        $result = [
            'total' => $totalMatches,
            'played' => $playedMatches,
            'percentage_played' => round($percentagePlayed),
        ];
        return $result;
    }
}
