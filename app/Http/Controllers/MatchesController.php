<?php

namespace App\Http\Controllers;

use App\Models\Classifications;
use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Players;
use App\Models\PlayersMatch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MatchesController extends Controller
{
    private static function predict_by_players($match)
    {
        $matches_local = DB::table('matches as m')
            ->join('player_match as pm', function ($join) use ($match) {
                $join->on('pm.idMatch', '=', 'm.idMatch')
                    ->whereIn('idPlayer', function ($subquery) use ($match) {
                        $subquery->select('idPlayer')
                            ->from('player_match')
                            ->where('idTeam', $match['idLocal']);
                    });
            })
            ->selectRaw('DISTINCT m.idMatch, m.localResult, m.visitorResult, m.idLocal, m.idVisitor,
        (SELECT GROUP_CONCAT(DISTINCT idTeam ORDER BY idTeam SEPARATOR ",")
         FROM player_match
         WHERE idPlayer = pm.idPlayer) AS teams')
            ->where('m.idMatch', '<>', $match['idMatch'])
            ->whereNotNull('localResult')
            ->get();


        $matches_visitor = DB::table('matches as m')
            ->join('player_match as pm', function ($join) use ($match) {
                $join->on('pm.idMatch', '=', 'm.idMatch')
                    ->whereIn('idPlayer', function ($subquery) use ($match) {
                        $subquery->select('idPlayer')
                            ->from('player_match')
                            ->where('idTeam', $match['idVisitor']);
                    });
            })
            ->selectRaw('DISTINCT m.idMatch, m.localResult, m.visitorResult, m.idLocal, m.idVisitor,
        (SELECT GROUP_CONCAT(DISTINCT idTeam ORDER BY idTeam SEPARATOR ",")
         FROM player_match
         WHERE idPlayer = pm.idPlayer) AS teams')
            ->where('m.idMatch', '<>', $match['idMatch'])
            ->whereNotNull('localResult')
            ->get();
        $points_local = 0;
        $points_visitor = 0;

        // Indexem els partits per idMatch per buscar coincidències fàcilment
        $visitor_by_match = collect($matches_visitor)->keyBy('idMatch');
        //Log::info("PARTIT " . $match['idMatch']);
        foreach ($matches_local as $match_local) {

            $idMatch = $match_local->idMatch ?? $match_local['idMatch'];

            // Si el partit també està a l’històric del jugador visitant
            if (isset($visitor_by_match[$idMatch])) {
                $match_visitor = $visitor_by_match[$idMatch];
                // Log::info("coincideix " . json_encode($match_visitor));

                $localResult = $match_local->localResult ?? $match_local['localResult'];
                $visitorResult = $match_local->visitorResult ?? $match_local['visitorResult'];
                $idLocal = $match_local->idLocal ?? $match_local['idLocal'];
                $idVisitor = $match_local->idVisitor ?? $match_local['idVisitor'];

                // Equips on han jugat els jugadors
                $teams_local = explode(',', $match_local->teams ?? '');
                $teams_visitor = explode(',', $match_visitor->teams ?? '');

                // Determinem el guanyador del partit
                if ($localResult > $visitorResult) {
                    // Guanya el local
                    if (in_array($idLocal, $teams_local)) {
                        $points_local++;
                    } elseif (in_array($idLocal, $teams_visitor)) {
                        $points_visitor++;
                    }
                } elseif ($visitorResult > $localResult) {
                    // Guanya el visitant
                    if (in_array($idVisitor, $teams_local)) {
                        $points_local++;
                    } elseif (in_array($idVisitor, $teams_visitor)) {
                        $points_visitor++;
                    }
                }
            }
        }
        return [$points_local, $points_visitor];
    }

    private static function predict_by_classification($match)
    {
        $classification = Classifications::whereIn('idTeam', [$match['idLocal'], $match['idVisitor']])->get();

        $local = $classification->firstWhere('idTeam', $match['idLocal']);
        $visitor = $classification->firstWhere('idTeam', $match['idVisitor']);

        $points_local = 1;
        $points_visitor = 1;
        if ($local && $visitor) {
            $diff = abs($local->position - $visitor->position);
            if ($local->position < $visitor->position) {
                // Local va millor classificat
                $points_local = $diff + 1;
            } elseif ($visitor->position < $local->position) {
                // Visitant va millor classificat
                $points_visitor = $diff + 1;
            }
        }
        return [$points_local, $points_visitor];
    }
    private static function predict_by_matches($match)
    {
        $teamA = $match['idLocal'];
        $teamB = $match['idVisitor'];

        $matchesA = Matches::where('idLocal',   $teamA)
            ->orWhere('visitorTeam', $teamA)
            ->get();
        $matchesB = Matches::where('idLocal', $teamB)
            ->orWhere('visitorTeam',  $teamB)
            ->get();
        // Partits d'A i B
        $matchesA = Matches::where('localTeam', $teamA)
            ->orWhere('visitorTeam', $teamA)
            ->get();

        $matchesB = Matches::where('localTeam', $teamB)
            ->orWhere('visitorTeam', $teamB)
            ->get();

        // Tots els rivals que han jugat contra A
        $rivalsA = $matchesA->map(function ($m) use ($teamA) {
            return $m->localTeam == $teamA ? $m->visitorTeam : $m->localTeam;
        })->unique();

        // Tots els rivals que han jugat contra B
        $rivalsB = $matchesB->map(function ($m) use ($teamB) {
            return $m->localTeam == $teamB ? $m->visitorTeam : $m->localTeam;
        })->unique();

        // Rivals comuns
        $common_rivals = $rivalsA->intersect($rivalsB);

        // Inicialitzem punts
        $pointsA = 0;
        $pointsB = 0;

        // Compareu els resultats contra cada rival comú
        foreach ($common_rivals as $rival) {
            $matchA = $matchesA->first(fn($m) => $m->localTeam == $rival || $m->visitorTeam == $rival);
            $matchB = $matchesB->first(fn($m) => $m->localTeam == $rival || $m->visitorTeam == $rival);

            // Funció auxiliar per saber qui guanya
            $winnerA = null;
            if ($matchA->localResult != $matchA->visitorResult) {
                $winnerA = $matchA->localResult > $matchA->visitorResult ? $matchA->localTeam : $matchA->visitorTeam;
            }

            $winnerB = null;
            if ($matchB->localResult != $matchB->visitorResult) {
                $winnerB = $matchB->localResult > $matchB->visitorResult ? $matchB->localTeam : $matchB->visitorTeam;
            }

            // Determinem els punts
            if ($winnerA == $teamA && $winnerB != $teamB) {
                $pointsA++;
            } elseif ($winnerB == $teamB && $winnerA != $teamA) {
                $pointsB++;
            } elseif ($winnerA == $teamA && $winnerB == $teamB) {
                $pointsA++;
                $pointsB++;
            }
        }
        return [$pointsA, $pointsB];
    }


    public static function predict($id_match)
    {
        $match = Matches::where('idMatch', $id_match)
            ->whereNull('localResult')
            ->first();
        if (!$match) return;
        [$class_local, $class_visitor] = self::predict_by_classification($match);
        [$players_local, $players_visitor] = self::predict_by_players($match);
        [$matches_local, $matches_visitor] = self::predict_by_matches($match);

        // Ponderacions
        $weight_classification = 0.4;
        $weight_players = 0.2;
        $weight_matches = 0.4;

        // Càlcul ponderat
        $points_local = ($class_local * $weight_classification) + ($players_local * $weight_players) + ($matches_local * $weight_matches);
        $points_visitor = ($class_visitor * $weight_classification) + ($players_visitor * $weight_players) + ($matches_visitor * $weight_matches);

        $total = $points_local + $points_visitor;
        $diff = $points_local - $points_visitor;

        // Percentatge màxim per la barra (50% del contenedor)
        $maxWidth = 50;

        // Calculem la mida proporcional
        $barWidth = $total > 0 ? (abs($diff) / $total) * $maxWidth : 0;

        // Direcció i color
        if ($diff > 0) {
            $translate = -$barWidth; // cap a l'esquerra
            $gradient = 'linear-gradient(to left, #999, #ddd)';
        } elseif ($diff < 0) {
            $translate = $barWidth; // cap a la dreta
            $gradient = 'linear-gradient(to right, #999, #ddd)';
        } else {
            $translate = 0;
            $gradient = '#A1A1AA';
        }
        if ($total == 0) {
            echo "<div class=\"h-3\">&nbsp;</div>";
            return;
        }
        echo '
<div class="w-full  mx-auto mt-3 border-neutral-400">
    <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
        <div
            class="absolute top-0 left-1/2 h-full rounded-full transition-all duration-700 animate-grow"
            style="
                --bar-width:' . $barWidth . '%;
                width: var(--bar-width);
                transform: translateX(-50%) translateX(' . $translate . '%);
                background:' . $gradient . ';
            "
          
        ></div>
    </div>
</div>
';
    }
}
