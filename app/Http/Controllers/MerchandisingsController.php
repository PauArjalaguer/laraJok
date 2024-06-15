<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\User;



use Illuminate\Http\Request;

class MerchandisingsController extends Controller
{
   public function index(){
    return view(
        'merchandising',
        [
            'clubsList' => Clubs::clubsList(),
            'leaguesList' => Leagues::leaguesList(),                  
            'merchandisingListAll' => Merchandisings::orderBy('assetName','asc')->get(),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
            'userSavedData' => User::userSavedData()
        ]
    );
   }
}
