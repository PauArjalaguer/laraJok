<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Agenda extends Model
{
    use HasFactory;
    public static function get(){
        $agenda = DB::table('matches as m')
        ->select([
            'm.idMatch',
            'm.matchDate',
            'm.matchHour',
            'm.idRound',
            'm.localResult',
            'm.visitorResult',
            'p.idGroup',
            'p.groupName',
            't1.idTeam as localTeamId',
            't1.teamName as localTeamName',
            't1.idClub as localClubId',
            't2.idTeam as visitorTeamId',
            't2.teamName as visitorTeamName',
            't2.idClub as visitorClubId', 
            'm.updated',
        ])
        ->join('phases as p', 'p.idGroup', '=', 'm.idGroup')
        ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
        ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
        ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
        ->where('l.idSeason', 37)
        ->where('m.matchDate', '>=', date('Y-m-d'))
        ->orderBy('m.matchDate')
        ->orderBy('m.matchHour')
        ->get();
        return $agenda;
    }
}
