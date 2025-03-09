<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Matches extends Model
{
    use HasFactory;
    protected $casts = [
        'matchDate'  => 'date:d-m-Y',

    ];
    protected $dateFormat = 'U';

    private static function baseMatchesQuery()
    {
        return DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->leftJoin('categories', 'leagues.idCategory', '=', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', '=', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', '=', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', '=', 'leagues.idSeason')
            ->join('phases', 'phases.idGroup', '=', 'matches.idGroup')
            ->leftJoin('places', 'places.idPlace', '=', 'matches.idPlace')
            ->select([
                'seasons.seasonName',
                'phases.groupName',
                'matches.idGroup',
                'idLocal',
                'idVisitor',
                'idRound',
                'matches.idMatch',
                'matches.matchDate',
                'matches.matchHour',
                'teams.teamName as localTeam',
                't2.teamName as visitorTeam',
                'categories.categoryName',
                'leagues.leagueName',
                'club1.clubImage as clubImage1',
                'club2.clubImage as clubImage2',
                'localResult',
                'visitorResult',
                'placeAddress',
                'lat',
                'lon'
            ]);
    }

    public static  function matchesListNext($userSavedData, $idClub = 0)
    {
        $idsTeams = User::userTeamsSelected($userSavedData);

        return self::baseMatchesQuery()
            ->whereNull('localResult')
            ->where('matchDate', '>', date("Y-m-d", strtotime('yesterday')))
            ->whereTime('matchHour', '>', Carbon::now()->format('H:i'))
            ->when(!empty($idsTeams), function ($query) use ($idsTeams) {
                return $query->where(function ($subQuery) use ($idsTeams) {
                    $subQuery->whereIn('matches.idLocal', $idsTeams)
                        ->orWhereIn('matches.idVisitor', $idsTeams);
                });
            })
            ->when($idClub != 0, function ($query) use ($idClub) {
                return $query->where(function ($subQuery) use ($idClub) {
                    $subQuery->where('club1.idClub', $idClub)
                        ->orWhere('club2.idClub', $idClub);
                });
            })
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->limit(10)
            ->get();
    }

    public static  function matchesListLastWithResults($userSavedData, $idClub = 0)
    {
        $idsTeams = User::userTeamsSelected($userSavedData);

        return self::baseMatchesQuery()
            ->whereNotNull('localResult')
            ->when(!empty($idsTeams), function ($query) use ($idsTeams) {
                return $query->where(function ($subQuery) use ($idsTeams) {
                    $subQuery->whereIn('matches.idLocal', $idsTeams)
                        ->orWhereIn('matches.idVisitor', $idsTeams);
                });
            })
            ->when($idClub != 0, function ($query) use ($idClub) {
                return $query->where(function ($subQuery) use ($idClub) {
                    $subQuery->where('club1.idClub', $idClub)
                        ->orWhere('club2.idClub', $idClub);
                });
            })
            ->orderBy('matchDate', 'desc')
            ->orderBy('matchHour', 'desc')
            ->limit(10)
            ->get();
    }
    public static  function matchesListFromIdLeague($idGroup)
    {
        return self::baseMatchesQuery()
            ->where('matches.idGroup', $idGroup)
            ->orderByRaw('CONVERT(idRound, SIGNED) asc')
            ->orderBy('idRound', 'asc')
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->get();
    }

    public static  function matchesListFromIdPlayer($idPlayer)
    {
        return DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->leftJoin('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('player_match', 'matches.idMatch', "=", 'player_match.idMatch')
            ->join("phases", "phases.idGroup", "=", "matches.idGroup")
            ->leftJoin("places", "places.idPlace", "=", "matches.idPlace")
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->select('seasons.seasonName', 'groupName', 'matches.idGroup', 'idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2', 'localResult', 'visitorResult', 'placeAddress', 'lat', 'lon')
            ->where("player_match.idPlayer", $idPlayer)
            ->orderBy('matchDate', 'desc')
            ->orderBy('matchHour', 'desc')
            ->get();
    }

    public static function matchGetInfoById($idMatch)
    {
        return  DB::table('matches')
            ->leftJoin('player_match', 'player_match.idMatch', "=", 'matches.idMatch')
            ->join("teams as t1", "t1.idTeam", "=", "matches.idLocal")
            ->join("clubs as c1", "t1.idClub", "=", "c1.idClub")
            ->join("teams as t2", "t2.idTeam", "=", "matches.idVisitor")
            ->join("clubs as c2", "t2.idClub", "=", "c2.idClub")
            ->leftJoin("players", "players.idPlayer", "=", "player_match.idPlayer")
            ->join("phases", "phases.idGroup", "=", "matches.idGroup")
            ->select("matches.idMatch", "matches.idGroup", "idRound", "groupName", "idLocal", "idVisitor", "matchDate", "matchHour", "idRound", "localResult", "visitorResult", "localFaults", "visitorFaults", "referee", "player_match.idPlayer", "players.playerName", "goals", "blue", "red", "directes", "penalti", "player_match.idTeam", "t1.teamName", "t2.teamName as teamName2", "c1.clubImage as clubImage1", "c2.clubImage as clubImage2")
            ->orderBy('player_match.idTeam')
            ->orderBy('players.playerName')
            ->where("matches.idMatch", $idMatch)->get();
    }

    public static  function matchesListFromIdPavello($idPavello)
    {
        return  DB::table('matches')
            ->join('leagues', 'matches.idLeague', '=', 'leagues.idLeague')
            ->leftJoin('categories', 'leagues.idCategory', 'categories.idCategory')
            ->join('teams', 'matches.idLocal', '=', 'teams.idTeam')
            ->join('teams as t2', 'matches.idVisitor', '=', 't2.idTeam')
            ->join('clubs as club1', 'club1.idClub', 'teams.idClub')
            ->join('clubs as club2', 'club2.idClub', 't2.idClub')
            ->join('seasons', 'seasons.idSeason', 'leagues.idSeason')
            ->join("phases", "phases.idGroup", "=", "matches.idGroup")
            ->leftJoin("places", "places.idPlace", "=", "matches.idPlace")
            ->select('seasons.seasonName', 'phases.groupName', 'localResult', 'visitorResult', 'matches.idGroup', 'idLocal', 'idVisitor', 'idRound', 'matches.idMatch', 'matches.matchDate', 'matches.matchHour', 'teams.teamName as localTeam', 't2.teamName as visitorTeam', 'categories.categoryName', 'leagues.leagueName', 'club1.clubImage as clubImage1', 'club2.clubImage as clubImage2', 'placeAddress', 'lat', 'lon')
            ->where('matchDate', '>', date("Y-m-d", strtotime('yesterday')))
            ->where('places.idPlace', $idPavello)
            ->orderBy('matchDate', 'asc')
            ->orderBy('matchHour', 'asc')
            ->get();
    }
}
