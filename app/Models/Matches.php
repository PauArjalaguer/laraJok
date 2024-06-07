<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Matches extends Model
{
    use HasFactory;
    protected $casts = [
        'matchDate'  => 'date:d-m-Y',

    ];
    protected $dateFormat = 'U';
    public static  function matchesListFromToday()
    {
        $matches = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2')
            ->whereNull('localResult')
            ->where('matchDate', date('Y/m/d'))
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->limit(5)
            ->get();
        return $matches;
    }
    public static  function matchesListFromTomorrow()
    {
        $matchesFromTomorrow = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2')
            ->whereNull('localResult')
            ->where('matchDate', date("Y-m-d", strtotime('tomorrow')))
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->limit(5)
            ->get();
        return $matchesFromTomorrow;
    }

    public static  function matchesListNext()
    {
        $matchesFromTomorrow = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2')
            ->where('matchDate', date("Y-m-d", strtotime('tomorrow')))
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->limit(5)
            ->get();
        return $matchesFromTomorrow;
    }

    public static  function matchesListLastWithResults()
    {
        $matchesFromTomorrow = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2', 'localResult', 'visitorResult')
            // ->where('idSeason', 135)
            ->where('localResult', '<>', '', 'and')
            ->orderBy('matchDate', 'desc')
            ->orderBy('matchHour', 'desc')
            ->limit(5)
            ->get();
        return $matchesFromTomorrow;
    }
    public static  function matchesListFromIdLeague($idGroup)
    {
        $matchesFromTomorrow = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
           ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2', 'localResult', 'visitorResult')
            // ->where('idSeason', 135)
            ->where('idGroup', $idGroup)
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')

            ->get();

        return $matchesFromTomorrow;
    }

    public static  function matchesListFromIdPlayer($idPlayer)
    {
        $matchesListFromIdPlayer = DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->join('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('player_match', 'matches.idMatch', "=", 'player_match.idMatch')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName','idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2', 'localResult', 'visitorResult')
     
            ->where("player_match.idPlayer", $idPlayer)
            ->orderBy('matchDate', 'desc')
            ->orderBy('matchHour', 'asc')

            ->get();

        return $matchesListFromIdPlayer;
    }
}
