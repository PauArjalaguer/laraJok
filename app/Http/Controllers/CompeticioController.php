<?php

namespace App\Http\Controllers;

use App\Models\Classifications;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Players;
use App\Models\User;

use Illuminate\Http\Request;

class CompeticioController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->id;
        $round = $request->round ?? $request->jornada;
        return view(
            'competicio',            [

                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'matchesList' => Matches::matchesListFromIdLeague($id),
                'classification' => Classifications::classificationGetByIdGroup($id),
                'bestGoalsMade' => Classifications::classificationGetBestGoalsMadeByIdLeague($id),
                'leastGoalsReceived' => Classifications::classificationGetLeastGoalsReceived($id),
                'maxGoalsPerLeague' => Players::maxGoalsPerLeague($id),
                'cleanSheets' => Leagues::cleanSheets($id),
                'totalPlayed' => Leagues::totalPlayed($id),
                'checkIfSaved' => User::checkIfSaved('competicio', $id),
                'userSavedData' => User::userSavedData(),
                'round'=>$round,
                'lastPlayedMatches' => Matches::lastPlayedMatchesByGroup($id),
                'teamForm' => Matches::getTeamFormByGroup($id),
            ]
        );
    }

    public static function acta(Request $request)
    {
        $id = $request->id;
        $matchGetInfoById = Matches::matchGetInfoById($id);
        $matchVideo = \App\Models\Video::where('idMatch', $id)->first();

        return view(
            'acta',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'matchGetInfoById' => $matchGetInfoById,
                'matchVideo' => $matchVideo
            ]
        );
    }

    public function generarCronica(Request $request, $id)
    {
        $matchGetInfoById = Matches::matchGetInfoById($id);

        if ($matchGetInfoById->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Partit no trobat'], 404);
        }

        $first = $matchGetInfoById->first();

        // 1. Si ja té crònica guardada, la retornem immediatament
        if (!empty(trim($first->cronica ?? ''))) {
            return response()->json([
                'success' => true,
                'cronica' => $first->cronica,
                'html' => \Illuminate\Support\Str::markdown($first->cronica)
            ]);
        }

        // 2. Comprovem si hi ha acta oficial disponible (més d'1 fila i amb dades de jugadors)
        $hasActa = ($matchGetInfoById->count() > 1 && !empty($first->idPlayer));
        $hasScore = ($first->localResult !== null && $first->localResult !== '' && $first->visitorResult !== null && $first->visitorResult !== '');

        if (!$hasActa || !$hasScore) {
            return response()->json([
                'success' => false,
                'message' => 'No hi ha acta disponible o el partit no té resultat.'
            ]);
        }

        // 3. Generem la crònica amb AiService
        try {
            $aiService = app(\App\Services\AiService::class);
            $generatedCronica = $aiService->generateMatchChronicle($matchGetInfoById);

            if (!empty($generatedCronica)) {
                Matches::saveCronica($id, $generatedCronica);
                return response()->json([
                    'success' => true,
                    'cronica' => $generatedCronica,
                    'html' => \Illuminate\Support\Str::markdown($generatedCronica)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No s\'ha pogut generar la crònica.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error generarCronica AJAX partit {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error intern del servidor.'
            ], 500);
        }
    }
    public static function llistat()
    {
        return view(
            'competicions_llistat',
            [
                'leaguesList' => Leagues::leaguesList(),
                'clubsList' => Clubs::clubsList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),

            ]
        );
    }

    public static function arbitre(Request $request, $name)
    {
        $refereeName = urldecode($name);
        $matchesList = Matches::matchesListByReferee($refereeName, 25);

        return view(
            'arbitre',
            [
                'refereeName' => $refereeName,
                'matchesList' => $matchesList,
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
            ]
        );
    }
}
