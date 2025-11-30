<?php

namespace App\Http\Controllers;

use App\Models\Classifications;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Teams;
use App\Models\User;

use Illuminate\Http\Request;

class ClubsController extends Controller
{
   public function index(Request $request){
    $id = $request->id;
    $userSavedData="";
    return view(
        'club',
        [
            'clubInfo' => Clubs::where('idClub', $id)->get(),
            'teamsList' => Teams::teamsByIdClub($id),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
            'checkIfSaved' => User::checkIfSaved('club', $id),
            'userSavedData' => User::userSavedData(),
            'classifications' => Classifications::classificationGetByIdClub($id),
            'matchesListNext' => Matches::matchesListNext( $userSavedData,$id),
            'matchesListLastWithResults' => Matches::matchesListLastWithResults($userSavedData,$id)
        ]
    );


   }
    public function acta_club(Request $request){
        $id = $request->id;
        $matches = Matches::matchesListLastWeekByClub($id);
        $fullMatches = [];
        foreach($matches as $m) {
            $fullMatches[] = Matches::matchGetInfoById($m->idMatch);
        }
        
        return view(
            'acta_club',
            [
                'matches' => $fullMatches,
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
            ]
        );
   }

    public function acta_header(Request $request){
        $id = $request->id;
        $matches = Matches::matchesListLastWeekByClub($id);
        $fullMatches = [];
        foreach($matches as $m) {
            $fullMatches[] = Matches::matchGetInfoById($m->idMatch);
        }
        $clubInfo = Clubs::where('idClub', $id)->first();
        
        return view(
            'acta_header',
            [
                'matches' => $fullMatches,
                'clubInfo' => $clubInfo
            ]
        );
   }
}
