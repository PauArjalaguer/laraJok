<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\News;
use App\Models\User;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view(
            'main',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'matchesListNext' => Matches::matchesListNext(),
                'matchesListLastWithResults' => Matches::matchesListLastWithResults(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'newsListTop'=>News::orderBy('newsDateTime','desc')->where('website','jokcat')->limit(4)->get()
                
            ]
        );
    }
}
