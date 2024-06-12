<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\Players;
use App\Models\Teams;

use App\Models\User;

class TeamsController extends Controller
{


    public function index(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        return view(
            'equip',
            [
                'leaguesList' => Leagues::leaguesList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'clubsList' => Clubs::clubsList(),
                'teamLeaguesList' => Teams::teamLeaguesList($id),
                'teamInfo' => Teams::teamInfo($id),
                'playersList' => Players::playersByIdTeam($id),
                'checkIfSaved' => User::checkIfSaved('equip', $id),
                'userSavedData' => User::userSavedData()
            ]
        );
    }
    public static function teamFormat($string)
    {
        if (strlen($string) > 20) {
            $string = str_replace("PHC", "", $string);
            $string = str_replace("ES MOU", "", $string);
            $string = str_replace("CLUB HOQUEI", "CH", $string);
            $string = str_replace("HOQUEI CLUB", "HC", $string);
            $string = str_replace("SANT JOSEP SANT SADURNI", "SANT JOSEP", $string);
            $string = str_replace("RENOVABLES", "", $string);
            $string = str_replace("PHC", "", $string);
            $string = str_replace("JOIERIA", "JOI", $string);
            $string = str_replace("CLUB PATIN", "CP", $string);
            $string = str_replace("CLUB PATÍ", "CP", $string);
            $string = str_replace("CLUB ESPORTIU OLESA", "CLUB OLESA", $string);
            $string = str_replace("PREVINTEGRAL", "", $string);
            $string = str_replace("UNIO ESPORTIVA", "UE", $string);
        }
        $string =  mb_convert_case(mb_strtolower($string), MB_CASE_TITLE);
        return $string;
    }
}
