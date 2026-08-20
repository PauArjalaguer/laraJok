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

use App\Models\Video;

class ClubsController extends Controller
{
   public function index(Request $request){
    $id = $request->id;
    $userSavedData="";
    $clubInfo = Clubs::where('idClub', $id)->first();
    $clubName = $clubInfo ? $clubInfo->clubName : '';

    return view(
        'club',
        [
            'clubInfo' => $clubInfo ? [$clubInfo] : [],
            'teamsList' => Teams::teamsByIdClub($id),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
            'checkIfSaved' => User::checkIfSaved('club', $id),
            'userSavedData' => User::userSavedData(),
            'classifications' => Classifications::classificationGetByIdClub($id),
            'matchesListNext' => Matches::matchesListNext($userSavedData, $id),
            'matchesListLastWithResults' => Matches::matchesListLastWithResults($userSavedData, $id),
            'clubVideos' => $clubName ? Video::getVideosByClubName($clubName, 100) : collect(),
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
