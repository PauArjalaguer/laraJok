<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Teams extends Model
{
    use HasFactory;

    public static function teamInfo($id)
    {
       return Teams::join('clubs', 'clubs.idClub', '=', 'teams.idClub')->leftjoin('categories', 'categories.idCategory', '=', 'teams.idCategory')->where('idTeam', $id)->get();
    }

    public static function teamLeaguesList($id)
    {
        return Phases::whereIn('idGroup', function ($q) use ($id) {
            $q->from('matches')->select('idGroup')->where('idLocal', '=', $id)->orwhere('idVisitor',  $id)->toSql();
        })->orderBy('startDate', 'desc')->get();
      
    }
 
    public static function teamsByIdClub($id){
        return Teams::join('categories', 'categories.idCategory', '=', 'teams.idCategory')->join('seasons', 'teams.idSeason', '=', 'seasons.idSeason')
        ->where('idClub', $id)->orderby('seasonName', 'desc')->orderby('categories.idCategory', 'asc')->orderby('teams.teamName', 'asc')->get();
       
    }

    public static function teamGoals($id){
        return DB::table('player_match as pm')
        ->selectRaw('SUM(goals) as goals, pl.playerName, pl.idPlayer')
        ->join('players as pl', 'pl.idPlayer', '=', 'pm.idPlayer')
        ->join('matches as m', 'm.idMatch', '=', 'pm.idMatch')
        ->join('phases as p', 'p.idGroup', '=', 'm.idGroup')
        ->join('leagues as l', 'l.idLeague', '=', 'p.idLeague')
        ->where('pm.idTeam', $id)
        ->groupBy('pl.idPlayer', 'pl.playerName')
        ->orderBy('goals', 'desc')
        ->get();
    }
}
