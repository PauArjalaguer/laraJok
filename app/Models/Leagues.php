<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Matches;
use Illuminate\Support\Facades\Log;

class Leagues extends Model
{
    use HasFactory;
    public static function leaguesList()
    {
        $cacheKey = 'leaguesList';
        $ttl = 10000;
        return Cache::remember($cacheKey, $ttl, function () {
            return  Leagues::join('seasons', 'leagues.idSeason', '=', 'seasons.idSeason')->join('phases', 'phases.idLeague', '=', 'leagues.idLeague')
                ->join('categories', 'leagues.idCategory', '=', 'categories.idCategory')
                ->select('seasonName', 'categoryName', 'leagueName as leagueName', 'phases.idGroup as value', DB::raw("CONCAT(' ',groupName,' (',seasonName,')') AS label"))->where('phases.numberofmatches', '!=', 0)->orderby('leagues.idSeason', 'desc')
                ->orderby('leagues.idCategory', 'asc')->get();
        });
    }

    public static function totalPlayed($idLeague)
    {
        $totalMatches =  Matches::where('idGroup', $idLeague)->count();

        $playedMatches = Matches::where('idGroup', $idLeague)
            ->whereNotNull('localResult')
            ->count();

        $percentagePlayed = $totalMatches > 0 ? ($playedMatches / $totalMatches) * 100 : 0;

        return [
            'total' => $totalMatches,
            'played' => $playedMatches,
            'percentage_played' => round($percentagePlayed),
        ];
    }

    public static function cleanSheets($idLeague)
    {
        $cleanSheets = DB::select("SELECT 
    idTeam,
    teamName,
    clubImage,
    COUNT(*) AS cleanSheets
FROM (
    SELECT
        CASE
            WHEN m.visitorResult = 0 THEN t1.idTeam      -- local deixa porteria a zero
            WHEN m.localResult = 0 THEN t2.idTeam        -- visitant deixa porteria a zero
        END AS idTeam,
        CASE
            WHEN m.visitorResult = 0 THEN t1.teamName
            WHEN m.localResult = 0 THEN t2.teamName
        END AS teamName,
        CASE
            WHEN m.visitorResult = 0 THEN c1.clubImage
            WHEN m.localResult = 0 THEN c2.clubImage
        END AS clubImage
    FROM matches m
    JOIN phases ph ON ph.idGroup = m.idGroup
    JOIN teams t1 ON t1.idTeam = m.idLocal
    JOIN clubs c1 ON c1.idClub = t1.idClub
    JOIN teams t2 ON t2.idTeam = m.idVisitor
    JOIN clubs c2 ON c2.idClub = t2.idClub
    WHERE (m.localResult = 0 OR m.visitorResult = 0)
      AND m.idGroup = $idLeague
) AS sub
WHERE idTeam IS NOT NULL
GROUP BY idTeam, teamName, clubImage
ORDER BY cleanSheets DESC;");
        return $cleanSheets;
    }
}
