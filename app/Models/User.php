<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function updateUserSavedData($idItem, $category)
    {
        $idUser =  Auth::user() ? Auth::user()->id : 0;
        $count = DB::table('usersaved')
            ->where('idUser', $idUser)
            ->where('category', $category)
            ->where('idItem', $idItem)
            ->count();

        if ($count == 1) {
            DB::table('usersaved')->where('idUser', $idUser)
                ->where('category', $category)
                ->where('idItem', $idItem)->delete();
        } else {
            DB::table('usersaved')->updateOrInsert(
                // Valores de las claves
                ['idUser' => $idUser, 'category' => $category, 'idItem' => $idItem],
                ['toDelete' => 0],
            );
        }
    }

    public static function userSavedData()
    {

        $idUser =  Auth::user() ? Auth::user()->id : 0;

        $results = DB::table('usersaved')->where('idUser', $idUser)
            ->leftJoin('phases', 'usersaved.idItem', 'phases.idGroup')
            ->leftJoin('teams', 'usersaved.idItem', 'teams.idTeam')
            ->leftJoin('clubs', 'usersaved.idItem', 'clubs.idClub')
            ->leftJoin('players', 'usersaved.idItem', 'players.idPlayer')->get();
        return $results;
    }

    public static function checkIfSaved($category, $idItem)
    {
        $idUser =  Auth::user() ? Auth::user()->id : 0;
        $count = DB::table('usersaved')
            ->where('idUser', $idUser)
            ->where('category', $category)
            ->where('idItem', $idItem)
            ->count();

            return $count;
    }

    public static function userTeamsSelected($userSavedData){
        $idSeason = Leagues::orderBy("idSeason", "desc")->limit(1)->first()->idSeason;
       
        $idsTeams = [];
        $idsTeams = collect($userSavedData)
            ->where('category', 'equip')
            ->pluck('idItem')
            ->toArray();

        $idsPlayers = collect($userSavedData)
            ->where('category', 'jugador')
            ->pluck('idItem')
            ->toArray();

        //Todo: fer la relació correctament       
        foreach ($idsPlayers as $idPlayer) {          
            $q = Matches::select("player_match.idTeam")->distinct("player_match.idTeam")->join("player_match", "matches.idMatch", "player_match.idMatch")
                ->join("leagues", "leagues.idLeague", "matches.idLeague")
                ->where("idPlayer", $idPlayer)
                ->where("idSeason", $idSeason)
                ->get();
            foreach ($q as $team) {
                array_push($idsTeams, $team->idTeam);
            }
        } 
        $idsClubs = collect($userSavedData)
            ->where('category', 'club')
            ->pluck('idItem')
            ->toArray();

        foreach ($idsClubs as $idClub) {         
            $q = Teams::select("idTeam")->distinct("idTeam")
                ->where("idClub", $idClub)                
                ->get();
            foreach ($q as $team) {
                array_push($idsTeams, $team->idTeam);
            }
        }
        return $idsTeams;
    }

    public static function userGroupsSelected($userSavedData){
      
        $idsGroups = collect($userSavedData)
            ->where('category', 'competicio')
            ->pluck('idItem')
            ->toArray();

       
      
        return $idsGroups;
    }
}
