<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Players;
use App\Models\User;


use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->id;
        return view(
            'jugador',
            [
                'playerInfo' => Players::where('idPlayer', $id)->get(),
                'playerMatchesList' => Matches::matchesListFromIdPlayer($id),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'playerStats' => Players::playerStats($id),
                'checkIfSaved' => User::checkIfSaved('jugador', $id),
                'userSavedData' => User::userSavedData()

            ]
        );
    }
}
