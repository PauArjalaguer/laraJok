<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Agenda extends Model
{
    use HasFactory;

    public static function get(?string $search = null, ?int $perPage = null)
    {
        $maxSeason = DB::table('leagues')->max('idSeason');

        $query = DB::table('matches as m')
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
                'c1.clubImage as clubImage1',
                't2.idTeam as visitorTeamId',
                't2.teamName as visitorTeamName',
                't2.idClub as visitorClubId', 
                'c2.clubImage as clubImage2',
                'm.updated',
            ])
            ->join('phases as p', 'p.idGroup', '=', 'm.idGroup')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('clubs as c1', 'c1.idClub', '=', 't1.idClub')
            ->leftJoin('clubs as c2', 'c2.idClub', '=', 't2.idClub')
            ->where('l.idSeason', $maxSeason)
            ->where('m.matchDate', '>=', date('Y-m-d'))
            ->when($search, function ($q, $s) {
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $s, $m)) {
                    $s = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})$/', $s, $m)) {
                    $s = sprintf('-%02d-%02d', $m[2], $m[1]);
                }
                $q->where(function ($sub) use ($s) {
                    $sub->where('t1.teamName', 'like', "%{$s}%")
                        ->orWhere('t2.teamName', 'like', "%{$s}%")
                        ->orWhere('p.groupName', 'like', "%{$s}%")
                        ->orWhere('m.matchDate', 'like', "%{$s}%");
                });
            })
            ->orderBy('m.matchDate', 'asc')
            ->orderBy('m.matchHour', 'asc');

        return $perPage ? $query->simplePaginate($perPage)->withQueryString() : $query->get();
    }
}
