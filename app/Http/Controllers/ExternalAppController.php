<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExternalAppController extends Controller
{
    private function truncateTeamName(string $name): string
    {
        $name = str_replace(["CLUB PATÍ", "CLUB PATI"], "CP", $name);
        $name = str_replace("HOQUEI CLUB", "HC", $name);
        $name = str_replace("UNIÓ ESPORTIVA", "UE", $name);
        $name = str_replace("SOLIDEO", "", $name);
        return trim($name);
    }

    /**
     * GET /api/external/propers_partits
     */
    public function propersPartits(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $top = (int)$request->input('top', 15);

        $query = DB::table('matches as m')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('places as cl', 'cl.idPlace', '=', 'm.idPlace')
            ->join('phases as ph', 'ph.idGroup', '=', 'm.idGroup')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->leftJoin('categories as cat', 'cat.idCategory', '=', 'l.idCategory')
            ->join('clubs as c1', 'c1.idClub', '=', 't1.idClub')
            ->join('clubs as c2', 'c2.idClub', '=', 't2.idClub')
            ->select([
                'm.idMatch as matchId',
                'm.matchDate',
                'm.matchHour',
                't1.teamName as local',
                't2.teamName as visitor',
                DB::raw('COALESCE(c1.clubImage, "") as localImage'),
                DB::raw('COALESCE(c2.clubImage, "") as visitorImage'),
                'cl.placeName as complexName',
                'cl.placeAddress as complexAddress',
                'cat.categoryName as divisionName',
                't1.idTeam as localId',
                't2.idTeam as visitorId',
                'l.idLeague',
                'ph.groupName',
                'l.leagueName',
                't1.idClub as localClub',
                't2.idClub as visitorClub',
            ])
            ->whereNull('m.localResult')
            ->where(function ($q) use ($idClub) {
                $q->where('t1.idClub', $idClub)
                  ->orWhere('t2.idClub', $idClub);
            });

        if ($request->filled('idLeague')) {
            $query->where('m.idLeague', $request->input('idLeague'));
        }

        if ($request->filled('idTeam')) {
            $idTeam = $request->input('idTeam');
            $query->where(function ($q) use ($idTeam) {
                $q->where('m.idLocal', $idTeam)->orWhere('m.idVisitor', $idTeam);
            });
        }

        if ($request->filled('teamFilter')) {
            $rawFilter = rtrim($request->input('teamFilter'), ',');
            $teamIds = array_map('intval', explode(',', $rawFilter));
            $query->where(function ($q) use ($teamIds) {
                $q->whereIn('m.idLocal', $teamIds)->orWhereIn('m.idVisitor', $teamIds);
            });
        } else {
            $query->where('m.matchDate', '>=', date('Y-m-d', strtotime('yesterday')));
        }

        $results = $query->orderBy('m.matchDate', 'asc')
            ->orderBy('m.matchHour', 'asc')
            ->limit($top)
            ->get();

        if ($results->isEmpty()) {
            $fallbackQuery = DB::table('matches as m')
                ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
                ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
                ->leftJoin('places as cl', 'cl.idPlace', '=', 'm.idPlace')
                ->join('phases as ph', 'ph.idGroup', '=', 'm.idGroup')
                ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
                ->leftJoin('categories as cat', 'cat.idCategory', '=', 'l.idCategory')
                ->join('clubs as c1', 'c1.idClub', '=', 't1.idClub')
                ->join('clubs as c2', 'c2.idClub', '=', 't2.idClub')
                ->select([
                    'm.idMatch as matchId',
                    'm.matchDate',
                    'm.matchHour',
                    't1.teamName as local',
                    't2.teamName as visitor',
                    DB::raw('COALESCE(c1.clubImage, "") as localImage'),
                    DB::raw('COALESCE(c2.clubImage, "") as visitorImage'),
                    'cl.placeName as complexName',
                    'cl.placeAddress as complexAddress',
                    'cat.categoryName as divisionName',
                    't1.idTeam as localId',
                    't2.idTeam as visitorId',
                    'l.idLeague',
                    'ph.groupName',
                    'l.leagueName',
                    't1.idClub as localClub',
                    't2.idClub as visitorClub',
                ])
                ->whereNull('m.localResult')
                ->where(function ($q) use ($idClub) {
                    $q->where('t1.idClub', $idClub)
                      ->orWhere('t2.idClub', $idClub);
                });

            if ($request->filled('idLeague')) {
                $fallbackQuery->where('m.idLeague', $request->input('idLeague'));
            }

            if ($request->filled('idTeam')) {
                $idTeam = $request->input('idTeam');
                $fallbackQuery->where(function ($q) use ($idTeam) {
                    $q->where('m.idLocal', $idTeam)->orWhere('m.idVisitor', $idTeam);
                });
            }

            $results = $fallbackQuery->orderBy('m.matchDate', 'asc')
                ->orderBy('m.matchHour', 'asc')
                ->limit($top)
                ->get();
        }

        $formatted = $results->map(function ($row) {
            $row = (array)$row;
            $row['matchHour'] = substr($row['matchHour'] ?? '00:00', 0, 5);
            if (!empty($row['matchDate'])) {
                $d = explode('-', $row['matchDate']);
                if (count($d) === 3) {
                    $row['matchDate'] = $d[2] . '-' . $d[1] . '-' . substr($d[0], 2, 2);
                }
            }
            $row['local'] = str_replace(["\t", "\n"], '', $row['local'] ?? '');
            $row['visitor'] = str_replace(["\t", "\n"], '', $row['visitor'] ?? '');
            $row['truncatedLocal'] = $this->truncateTeamName($row['local']);
            $row['truncatedVisitor'] = $this->truncateTeamName($row['visitor']);
            $row['distance'] = null;
            $row['travelTime'] = null;
            $row['meteo'] = null;
            $row['meteoIcon'] = null;

            if (strlen($row['complexName'] ?? '') > 34) {
                $row['complexName'] = str_replace('Municipal', '', $row['complexName']);
            }
            return $row;
        });

        return response()->json($formatted);
    }

    /**
     * GET /api/external/resultats
     */
    public function resultats(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $top = (int)$request->input('top', 12);

        $results = DB::table('matches as m')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('places as cl', 'cl.idPlace', '=', 'm.idPlace')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->leftJoin('categories as d', 'd.idCategory', '=', 'l.idCategory')
            ->join('clubs as c1', 'c1.idClub', '=', 't1.idClub')
            ->join('clubs as c2', 'c2.idClub', '=', 't2.idClub')
            ->select([
                'm.idMatch as matchId',
                't1.teamName as local',
                't2.teamName as visitor',
                'c1.clubImage as localImage',
                'c2.clubImage as visitorImage',
                'm.matchDate',
                'm.matchHour',
                'cl.placeName as complexName',
                'cl.placeAddress as complexAddress',
                'm.localResult',
                'm.visitorResult',
                'd.categoryName as divisionName',
                't1.idTeam as localId',
                't2.idTeam as visitorId',
                DB::raw('WEEK(m.matchDate, 1) as week')
            ])
            ->whereNotNull('m.localResult')
            ->where(function ($q) use ($idClub) {
                $q->where('t1.idClub', $idClub)
                  ->orWhere('t2.idClub', $idClub);
            })
            ->orderBy('m.matchDate', 'desc')
            ->orderBy('m.matchHour', 'desc')
            ->limit($top)
            ->get();

        $formatted = $results->map(function ($row) {
            $row = (array)$row;
            $row['matchHour'] = substr($row['matchHour'] ?? '00:00', 0, 5);
            if (!empty($row['matchDate'])) {
                $d = explode('-', $row['matchDate']);
                if (count($d) === 3) {
                    $row['matchDate'] = $d[2] . '-' . $d[1] . '-' . substr($d[0], 2, 2);
                }
            }
            return $row;
        });

        return response()->json($formatted);
    }

    /**
     * GET /api/external/tots_partits
     */
    public function totsElsPartits(Request $request)
    {
        $idClub = $request->input('idClub', 270);

        $query = DB::table('matches as m')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('places as c', 'c.idPlace', '=', 'm.idPlace')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->join('phases as ph', 'ph.idGroup', '=', 'm.idGroup')
            ->leftJoin('categories as d', 'd.idCategory', '=', 'l.idCategory')
            ->join('clubs as c1', 'c1.idClub', '=', 't1.idClub')
            ->join('clubs as c2', 'c2.idClub', '=', 't2.idClub')
            ->select([
                'm.updated as lastupdate',
                DB::raw('NOW() as now'),
                'm.idMatch as matchid',
                'm.matchDate',
                'm.matchHour',
                't1.teamName as local',
                't2.teamName as visitor',
                'c.placeName as complexName',
                'c.placeAddress as complexAddress',
                'm.idRound as fixture',
                't1.idTeam as localId',
                't2.idTeam as visitorId',
                't1.idClub as localClub',
                't2.idClub as visitorClub',
                'c1.clubImage as localImage',
                'c2.clubImage as visitorImage',
                'l.idLeague',
                'l.leagueName',
                'ph.groupName',
                'm.localResult',
                'm.visitorResult',
                'ph.startDate',
                'd.categoryName as divisionName',
                DB::raw('0 as isDeleted'),
                'm.idMatch as fecapaId',
                DB::raw('NULL as distance'),
                DB::raw('NULL as travelTime'),
                DB::raw('NULL as meteo'),
            ]);

        if ($request->filled('idClubOnly') && $request->input('idClubOnly') == 1) {
            $query->where(function ($q) use ($idClub) {
                $q->where('t1.idClub', $idClub)->orWhere('t2.idClub', $idClub);
            });
        }

        if ($request->filled('idLeague')) {
            $query->where('m.idLeague', $request->input('idLeague'));
        }

        if ($request->filled('datetime')) {
            $query->where('m.updated', '>', urldecode($request->input('datetime')));
        }

        if ($request->input('orderByRound') == 1) {
            $query->orderByRaw('CAST(m.idRound AS UNSIGNED) ASC')->orderBy('m.matchDate', 'asc');
        } else {
            $query->orderBy('m.matchDate', 'asc')->orderBy('m.matchHour', 'asc');
        }

        $results = $query->limit(1500)->get();

        $formatted = $results->map(function ($row) {
            $row = (array)$row;
            $row['fixture'] = trim($row['fixture'] ?? '');
            $row['local'] = str_replace(["\t", "\n"], '', $row['local'] ?? '');
            $row['visitor'] = str_replace(["\t", "\n"], '', $row['visitor'] ?? '');
            if (strlen($row['complexName'] ?? '') > 34) {
                $row['complexName'] = str_ireplace('Municipal', '', $row['complexName']);
            }
            return $row;
        });

        return response()->json($formatted);
    }

    /**
     * GET /api/external/partits
     */
    public function partits(Request $request)
    {
        $idLeague = $request->input('idLeague');
        $idGroup = $request->input('groupName') ?? $request->input('idGroup');

        $query = DB::table('matches as m')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('places as c', 'c.idPlace', '=', 'm.idPlace')
            ->select([
                'm.idMatch as matchid',
                'm.matchDate',
                'm.matchHour',
                DB::raw('TRIM(t1.teamName) as local'),
                DB::raw('TRIM(t2.teamName) as visitor'),
                'c.placeName as complexName',
                'm.idRound as fixture',
                't1.idTeam as localId',
                't2.idTeam as visitorId',
                't1.idClub as localClub',
                't2.idClub as visitorClub',
            ]);

        if ($idLeague) {
            $query->where('m.idLeague', $idLeague);
        }
        if ($idGroup) {
            $query->where('m.idGroup', $idGroup);
        }

        $results = $query->get();
        return response()->json($results);
    }

    /**
     * GET /api/external/classificacions
     */
    public function classificacions(Request $request)
    {
        $idLeague = $request->input('idLeague');
        $idGroup = $request->input('idGroup');

        $query = DB::table('classifications as c')
            ->join('phases as ph', 'ph.idGroup', '=', 'c.idGroup')
            ->join('leagues as l', 'l.idLeague', '=', 'ph.idLeague')
            ->join('teams as t', 't.idTeam', '=', 'c.idTeam')
            ->join('clubs as cl', 'cl.idClub', '=', 't.idClub')
            ->select([
                DB::raw('CONCAT(c.idLeague, c.idTeam) as classId'),
                'c.idLeague',
                'ph.groupName',
                'c.position',
                'c.points',
                'c.played',
                'c.won',
                'c.draw',
                'c.lost',
                'l.leagueName',
                't.teamName',
                'cl.clubImage as teamImgSrc',
                'c.updateddate as updated',
                DB::raw('0 as isDeleted')
            ]);

        if ($idLeague) {
            $query->where('c.idLeague', $idLeague);
        }
        if ($idGroup) {
            $query->where('c.idGroup', $idGroup);
        }

        $results = $query->orderByRaw('CAST(c.position AS UNSIGNED) ASC')->get();

        if ($results->isEmpty() && $idLeague) {
            return response()->json([[
                'classId' => 1,
                'idLeague' => $idLeague,
                'groupName' => '',
                'position' => '1',
                'points' => 0,
                'won' => 0,
                'draw' => 0,
                'lost' => 0,
                'leagueName' => '',
                'teamName' => 'No disponible'
            ]]);
        }

        $formatted = $results->map(function ($row) {
            $row = (array)$row;
            $row['teamName'] = str_replace(["\t", "\n"], '', $row['teamName'] ?? '');
            return $row;
        });

        return response()->json($formatted);
    }

    /**
     * GET /api/external/equips
     */
    public function equips(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $tString = $request->input('tString', '');

        $teams = DB::table('teams as t')
            ->join('clubs as c', 'c.idClub', '=', 't.idClub')
            ->select([
                't.idTeam as teamId',
                't.teamName',
                't.idClub',
                't.idCategory',
                't.idSeason',
                'c.clubImage as image',
                'c.clubImage as teamImgSrc'
            ])
            ->where('t.idClub', $idClub)
            ->orderBy('t.teamName', 'asc')
            ->get();

        $formatted = $teams->map(function ($row) use ($tString) {
            $row = (array)$row;
            if ($tString && strpos($tString, (string)$row['teamId']) !== false) {
                $row['isActive'] = 1;
            } else {
                $row['isActive'] = 0;
            }
            return $row;
        });

        return response()->json($formatted);
    }

    /**
     * GET /api/external/equip
     */
    public function equip(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $idTeam = $request->input('idTeam');

        $query = DB::table('players as p')
            ->join('player_match as pm', 'pm.idPlayer', '=', 'p.idPlayer')
            ->join('teams as t', 't.idTeam', '=', 'pm.idTeam')
            ->select([
                'p.idPlayer',
                DB::raw('LOWER(p.playerName) as playerName'),
                'p.number',
                't.idTeam',
                'p.updated as updatedate'
            ])
            ->where('t.idClub', $idClub);

        if ($idTeam) {
            $query->where('pm.idTeam', $idTeam);
        }

        $players = $query->distinct()->orderBy('p.playerName', 'asc')->get();

        return response()->json($players);
    }

    /**
     * GET /api/external/lligues
     */
    public function lligues(Request $request)
    {
        $idClub = $request->input('idClub', 270);

        $query = DB::table('leagues as l')
            ->join('phases as ph', 'ph.idLeague', '=', 'l.idLeague')
            ->leftJoin('categories as d', 'd.idCategory', '=', 'l.idCategory')
            ->join('seasons as s', 's.idSeason', '=', 'l.idSeason')
            ->select([
                'l.idLeague',
                'ph.groupName',
                'l.leagueName',
                'd.categoryName as divisionName',
                DB::raw('1 as isActive'),
                's.seasonName',
                'ph.startDate as seasonDateStart',
                'ph.endDate as seasonDateEnd'
            ]);

        if ($request->filled('idTeam')) {
            $rawTeams = rtrim($request->input('idTeam'), ',');
            $teamIds = array_map('intval', explode(',', $rawTeams));
            $query->whereIn('ph.idGroup', function ($sub) use ($teamIds) {
                $sub->select('idGroup')->from('matches')->whereIn('idLocal', $teamIds)->orWhereIn('idVisitor', $teamIds);
            });
        } elseif ($idClub) {
            $query->whereIn('ph.idGroup', function ($sub) use ($idClub) {
                $sub->select('m.idGroup')
                    ->from('matches as m')
                    ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
                    ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
                    ->where('t1.idClub', $idClub)
                    ->orWhere('t2.idClub', $idClub);
            });
        }

        $results = $query->orderBy('l.idSeason', 'desc')
            ->orderBy('l.leagueName', 'asc')
            ->get();

        return response()->json($results);
    }

    /**
     * GET /api/external/noticies
     */
    public function noticies(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $top = (int)$request->input('top', 5);
        $id = $request->input('id');
        $headline = $request->input('headline');

        $query = DB::table('news')
            ->select([
                'idNew as id',
                'newsTitle as title',
                'newsSubtitle as subtitle',
                'newsImage as pathImage',
                DB::raw('DATEDIFF(NOW(), newsDatetime) as time'),
                'newsDatetime as UpdateDate',
                'website'
            ]);

        if ($headline != 1) {
            $query->addSelect('newsContent as content');
        }

        if ($id) {
            $query->where('idNew', $id);
        }

        if ($idClub) {
            $query->where(function ($q) use ($idClub) {
                $q->where('idClub', $idClub)->orWhereNull('idClub');
            });
        }

        $news = $query->orderBy('idNew', 'desc')->limit($top)->get();

        return response()->json($news);
    }

    /**
     * GET /api/external/croniques
     */
    public function croniques(Request $request)
    {
        $idClub = $request->input('idClub', 270);
        $top = (int)$request->input('top', 5);

        $results = DB::table('news as n')
            ->join('matches as m', 'm.fecapaId', '=', 'n.idNew')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->leftJoin('places as cl', 'cl.idPlace', '=', 'm.idPlace')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->join('phases as ph', 'ph.idGroup', '=', 'm.idGroup')
            ->leftJoin('categories as d', 'd.idCategory', '=', 'l.idCategory')
            ->join('clubs as c1', 'c1.idClub', '=', 't1.idClub')
            ->join('clubs as c2', 'c2.idClub', '=', 't2.idClub')
            ->select([
                'n.idNew as id',
                'n.newsTitle as title',
                'n.newsSubtitle as subtitle',
                'm.idMatch as matchId',
                'm.matchDate',
                'm.matchHour',
                't1.teamName as local',
                't2.teamName as visitor',
                'c1.clubImage as localImage',
                'c2.clubImage as visitorImage',
                'cl.placeName as complexName',
                'cl.placeAddress as complexAddress',
                'd.categoryName as divisionName',
                't1.idTeam as localId',
                't2.idTeam as visitorId',
                'l.idLeague',
                'ph.groupName',
                'l.leagueName',
                'm.localResult',
                'm.visitorResult',
                'n.newsContent as content',
                'n.newsImage as pathImage',
                DB::raw('DATEDIFF(NOW(), n.newsDatetime) as time'),
                'n.newsDatetime as UpdateDate'
            ])
            ->where(function ($q) use ($idClub) {
                $q->where('t1.idClub', $idClub)->orWhere('t2.idClub', $idClub);
            })
            ->orderBy('n.idNew', 'desc')
            ->limit($top)
            ->get();

        return response()->json($results);
    }

    /**
     * GET /api/external/search
     */
    public function search(Request $request)
    {
        $s = $request->input('search', '');

        $news = DB::table('news')
            ->select(['idNew as id', 'newsTitle as title', 'newsContent as content', 'newsSubtitle as subtitle', 'newsImage as pathImage', 'newsDatetime as updateDate'])
            ->where('newsTitle', 'like', "%{$s}%")
            ->orWhere('newsContent', 'like', "%{$s}%")
            ->limit(20)
            ->get()
            ->map(function ($r) {
                $r = (array)$r;
                $r['type'] = 'news';
                $r['content'] = substr(strip_tags($r['content'] ?? ''), 0, 100);
                return $r;
            });

        $matches = DB::table('matches as m')
            ->join('teams as t1', 't1.idTeam', '=', 'm.idLocal')
            ->join('teams as t2', 't2.idTeam', '=', 'm.idVisitor')
            ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
            ->join('seasons as s_table', 's_table.idSeason', '=', 'l.idSeason')
            ->select([
                'm.matchDate',
                DB::raw('CONCAT(t1.teamName, " - ", t2.teamName) as matchName'),
                'l.leagueName',
                'l.idLeague',
                's_table.seasonName'
            ])
            ->where(function ($q) use ($s) {
                $q->where('t1.teamName', 'like', "%{$s}%")->orWhere('t2.teamName', 'like', "%{$s}%");
            })
            ->orderBy('m.matchDate', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($r) {
                $r = (array)$r;
                $r['type'] = 'matches';
                return $r;
            });

        $leagues = DB::table('leagues')
            ->where('leagueName', 'like', "%{$s}%")
            ->limit(20)
            ->get()
            ->map(function ($r) {
                $r = (array)$r;
                $r['type'] = 'leagues';
                return $r;
            });

        return response()->json([
            'news' => $news,
            'matches' => $matches,
            'multimedia' => [],
            'leagues' => $leagues
        ]);
    }
}
