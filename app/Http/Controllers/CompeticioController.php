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
        $round = $request->round;
        return view(
            'competicio',            [

                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'matchesList' => Matches::matchesListFromIdLeague($id),
                'classification' => Classifications::classificationGetByIdGroup($id),
                'bestGoalsMade' => Classifications::classificationGetBestGoalsMadeByIdLeague($id),
                'leastGoalsReceived' => Classifications::classificationGetLeastGoalsReceived($id),
                'maxGoalsPerLeague' => Players::maxGoalsPerLeague($id),
                'totalPlayed' => Leagues::totalPlayed($id),
                'checkIfSaved' => User::checkIfSaved('competicio', $id),
                'userSavedData' => User::userSavedData(),
                'round'=>$round
            ]
        );
    }

    public static function acta(Request $request)
    {
        $id = $request->id;
        return view(
            'acta',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'matchGetInfoById' => Matches::matchGetInfoById($id)
            ]
        );
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
}
