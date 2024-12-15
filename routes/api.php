<?php

use App\Http\Controllers\ScrapingController;
use App\Models\Clubs;
use App\Models\Players;
use App\Models\Teams;
use App\Models\Leagues;
use App\Models\News;
use App\Models\Matches;
use App\Models\Phases;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get("/search/teams/{search}", function ($search) {
    return Teams::join('categories', 'categories.idCategory', '=', 'teams.idCategory')->where('teamName', 'like', '%' . $search . '%')->limit(100)->orderBy('idTeam', 'desc')->get();
});
Route::get("/search/clubs/{search}", function ($search) {
    return Clubs::where('clubName', 'like', '%' . $search . '%')->limit(100)->get();
});
Route::get("/search/players/{search}", function ($search) {
    return Players::where('playerName', 'like', '%' . $search . '%')
        ->limit(100)
        ->orderBy('playerName', 'desc')->get();
});

Route::get("/leagues", function () {
    return Leagues::all();
});
Route::get("/news/{website}/{top}", function ($website, $top) {
    return News::select('idNew', 'newsDateTime as news_datetime', 'newsTitle as news_title', 'newsSubtitle as news_subtitle', 'newsContent as news_content', 'newsImage as news_image')
        ->where('website', 'like', '%' . $website . '%')
        ->limit($top)
        ->orderBy('newsDateTime', 'desc')
        ->get();
});
Route::get("/news/{website}/{top}", function ($website, $top) {
    return News::where('website', 'like', '%' . $website . '%')->limit(100)->orderBy('newsDateTime', 'desc')->orderBy('newsDateTime', 'desc')->limit($top)->get();
});
Route::get("/new/{website}/{id}", function ($website, $id) {
    return News::where('idNew',  $id)->get();
});
Route::get("main/matchesListNext/{top}", function ($top) {
    return Matches::join('teams as t1', 't1.idTeam', '=', 'matches.idLocal')
        ->join('clubs as c1', 't1.idClub', '=', 'c1.idClub')
        ->join('teams as t2', 't2.idTeam', '=', 'matches.idVisitor')
        ->join('clubs as c2', 't2.idClub', '=', 'c2.idClub')
        ->join('phases', 'matches.idGroup', '=', 'phases.idGroup')
        ->where(DB::raw("CONCAT(matchDate, ' ', matchHour)"), '>', now())
        ->orderBy('matchDate')
        ->orderBy('matchHour')
        ->select('idMatch', 'groupName', 't1.teamName as localName', 't2.teamName as visitorName', 'c1.clubImage as localImage', 'c2.clubImage as visitorImage', 'matchDate as matchDate', 'matchHour as matchHour')
        ->limit($top)
        ->get();
});
Route::get("main/matchesListLastWithResults/{top}", function ($top) {
    return Matches::join('teams as t1', 't1.idTeam', '=', 'matches.idLocal')
        ->join('clubs as c1', 't1.idClub', '=', 'c1.idClub')
        ->join('teams as t2', 't2.idTeam', '=', 'matches.idVisitor')
        ->join('clubs as c2', 't2.idClub', '=', 'c2.idClub')
        ->join('phases', 'matches.idGroup', '=', 'phases.idGroup')
        ->select('idMatch', 'groupName', 't1.teamName as localNaame', 't2.teamName as visitorName', 'c1.clubImage as localImage', 'c2.clubImage as visitorImage', 'matchDate as matchDate', 'matchHour as matchHour', 'localResult as localResult', 'visitorResult as visitorResult')
        ->whereNot('localResult', null)->orderBy('matchDate', 'desc')
        ->orderBy('matchHour', 'desc')
        ->limit($top)
        ->get(); 
});
Route::get("clubs/clubsList", function () {
    return Clubs::clubsList();
});
Route::get("clubs/teamsList/{idClub}", function ($idClub) {
    return Teams::teamsByIdClub($idClub);
});
Route::get("team/{idTeam}",function($idTeam){
    $teamInfo= Teams::teamInfo($idTeam);
    $playersList =   Players::playersByIdTeam($idTeam);
    $teamsLeaguesList = Phases::whereIn('idGroup', function ($q) use ($idTeam) {
        $q->from('matches')->select('idGroup')->where('idLocal', '=', $idTeam)->orwhere('idVisitor',  $idTeam)->toSql();
    })->orderBy('startDate', 'desc')->get();
    return ['teamInfo'=>$teamInfo,'playersList'=>$playersList,'teamLeaguesList'=>$teamsLeaguesList];
});


Route::get("scraping/fcb", function () {
    return ScrapingController::scrapeFCBarcelona();
});
Route::get("scraping/reus", function () {
    return ScrapingController::scrapeReus();
});
Route::get("scraping/palau", function () {
    return ScrapingController::scrapePalau();
});
Route::get("scraping/cerdanyola", function () {
    return ScrapingController::scrapeCerdanyola();
});
Route::get("scraping/regio", function () {
    return ScrapingController::scrapeRegio();
});

Route::get("scraping/noia", function () {
    return ScrapingController::scrapeNoia();
});
Route::get("scraping/resultats", function () {
    return ScrapingController::scrapeFecapaResults();
});

Route::get(
    "clubs/clubInfo/{idClub}",
    function ($idClub) {
        return Clubs::where('idClub', '=', $idClub)->get();
    }
);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
