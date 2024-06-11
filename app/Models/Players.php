<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Players extends Model
{
    use HasFactory;
    public static function playersByIdTeam($id)
    {
        $result = Players::distinct("playerName")->select('players.idPlayer', 'playerName')->join("player_match", "player_match.idPlayer", "=", "players.idPlayer")->orderBy('playerName', 'asc')
            ->where('idTeam', $id)->whereIn('player_match.idMatch', function ($q) use ($id) {
                $q->from('matches')->select('idMatch')->where('idLocal', '=', $id)->orwhere('idVisitor',  $id)->toSql();
            })->get();
        return $result;
    }

    public static function playerStats($id)
    {
        $results = DB::table('player_match as pm')
            ->join('matches as m', 'm.idMatch', '=', 'pm.idMatch')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->join('seasons as s', 's.idSeason', '=', 'l.idSeason')
            ->select(
                DB::raw('SUM(pm.goals) as total_goals'),
                DB::raw('COUNT(*) as match_count'),
                DB::raw('SUM(pm.blue) as total_blue'),
                DB::raw('SUM(pm.red) as total_red'),
                's.seasonName'
            )
            ->where('pm.idPlayer', $id)
            ->groupBy('l.idSeason')
            ->get();
        return $results;
    }
    public static function maxGoalsPerLeague($idLeague)
    {
        $results = DB::table('player_match as pm')
            ->select(
                DB::raw('SUM(goals) AS goals'),
                'pm.idPlayer',
                DB::raw('(SELECT playerName FROM players WHERE idPlayer = pm.idPlayer LIMIT 1) AS playerName')
            )
            ->join('matches as m', 'm.idMatch', '=', 'pm.idMatch')
            ->where('m.idGroup', $idLeague)
            ->groupBy('pm.idPlayer')
            ->orderBy('goals', 'DESC')
            ->limit(5)
            ->get();
        return $results;
    }
   
}
