<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Classifications extends Model
{
    use HasFactory;

    public static function classificationGetByIdGroup($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->where('idGroup', $id)
            ->orderByRaw('CAST(position AS UNSIGNED) ASC')
            ->orderByRaw('CAST(points AS UNSIGNED) DESC')
            ->get();
    }
    public static function classificationGetBestGoalsMadeByIdLeague($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsMade', 'clubImage', 'teams.idTeam')->orderBy('goalsMade', 'desc')->limit(1)->get();
    }
    public static function classificationGetLeastGoalsReceived($id){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsReceived', 'clubImage', 'teams.idTeam')->orderBy('goalsReceived', 'asc', 'clubImage')->limit(1)->get();
    }
    public static function classificationGetByIdClub($idClub){
        return Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')
            ->join('clubs as club', 'club.idClub', 'teams.idClub')
            ->join('phases','phases.idGroup','classifications.idGroup')
            ->where('teams.idClub', $idClub)
            ->where("phases.startDate","<",DB::raw('NOW()'))
            ->where("phases.endDate",">",DB::raw('NOW()'))
            ->select('classifications.idGroup','groupName','classifications.idTeam','teamName', 'position','points','played','won','draw','lost', 'goalsReceived', 'clubImage', 'teams.idTeam')
            ->orderBy('teams.idTeam', 'asc')->get();
    }
}
