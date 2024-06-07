<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Players extends Model
{
    use HasFactory;


   public static function playerStats ($id){
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
}
