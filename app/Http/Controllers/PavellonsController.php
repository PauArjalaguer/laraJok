<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\User;
use App\Models\Matches;
use App\Models\Pavellons;
class PavellonsController extends Controller
{
    public function index()
    {
        return view(
            'pavellons',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'pavellons' => Pavellons::whereNotNull('lat')->with('matches') ->get()
            ]
        );
    }
    public function detall($idPavello, $label)
    {
        return view(
            'pavello',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'partits_pavello' => Matches::matchesListFromIdPavello($idPavello)
            ]
        );
    }

}
