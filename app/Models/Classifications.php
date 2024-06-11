<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classifications extends Model
{
    use HasFactory;

    public static function classificationGetByIdGroup($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->where('idGroup', $id)->orderBy('points', 'desc')->orderBy('position', 'asc')->get();
    }
    public static function classificationGetBestGoalsMadeByIdLeague($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsMade', 'clubImage', 'teams.idTeam')->orderBy('goalsMade', 'desc')->limit(1)->get();
    }
    public static function classificationGetLeastGoalsReceived($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsReceived', 'clubImage', 'teams.idTeam')->orderBy('goalsReceived', 'asc', 'clubImage')->limit(1)->get();
    }
}
