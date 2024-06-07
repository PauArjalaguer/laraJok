<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public static function teamFormat($string){
        $string = str_replace("PHC","",$string);
        $string = str_replace("ES MOU","",$string);
        $string = str_replace("CLUB HOQUEI","CH",$string);
        $string = str_replace("HOQUEI CLUB","HC",$string);
        $string = str_replace("SANT JOSEP SANT SADURNI","SANT JOSEP",$string);
        $string = str_replace("RENOVABLES","",$string);
        $string = str_replace("PHC","",$string);
        $string = str_replace("CLUB PATIN","CP",$string);
        $string = str_replace("CLUB ESPORTIU OLESA","CLUB OLESA",$string);
        return $string;
    }
}
