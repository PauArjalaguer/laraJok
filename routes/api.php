<?php

use App\Http\Controllers\ScrapingController;
use App\Models\Agenda;
use App\Models\Classifications;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\News;
use App\Models\Players;
use App\Models\Teams;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get("/search/{search}", function ($search) {
    $teams = Teams::join('categories', 'categories.idCategory', '=', 'teams.idCategory')->where('teamName', 'like', '%' . $search . '%')->limit(100)->orderBy('idTeam', 'desc')->get();
    $clubs = Clubs::where('clubName', 'like', '%' . $search . '%')->limit(100)->get();
    $players = Players::where('playerName', 'like', '%' . $search . '%')
        ->limit(100)
        ->orderBy('playerName', 'desc')->get();
    $leagues = Leagues::where('leagueName', 'like', '%' . $search . '%')->limit(100)->get();

    return ['teams' => $teams, 'clubs' => $clubs, 'players' => $players, 'leagues' => $leagues];
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
Route::get("main/matchesListNext/{top}", function () {
    return Matches::matchesListNext("",0);
});
Route::get("main/matchesListLastWithResults/{top}", function () {
    return Matches::matchesListLastWithResults("",0);
});
Route::get("clubs/clubsList", function () {
    return Clubs::clubsList();
});
Route::get("clubs/teamsList/{idClub}", function ($idClub) {
    return Teams::teamsByIdClub($idClub);
});
Route::get("team/{idTeam}", function ($idTeam) {
    $teamInfo = Teams::teamInfo($idTeam);
    $playersList =   Players::playersByIdTeam($idTeam);
    $teamsLeaguesList = Teams::teamLeaguesList($idTeam);
    return ['teamInfo' => $teamInfo, 'playersList' => $playersList, 'teamLeaguesList' => $teamsLeaguesList];
});
Route::get("player/{idPlayer}", function ($idPlayer) {
    $playerInfo =  Players::where('idPlayer', $idPlayer)->get();
    $playerMatchesList = Matches::matchesListFromIdPlayer($idPlayer);
    $playerStats = Players::playerStats($idPlayer);
    return ['playerInfo' => $playerInfo, 'playerMatchesList' => $playerMatchesList, 'playerStats' => $playerStats];
});
Route::get("competicio/{idCompetition}", function ($idCompetition) {
    $matchesList = Matches::matchesListFromIdLeague($idCompetition);
    $classification = Classifications::classificationGetByIdGroup($idCompetition);
    $bestGoalsMade = Classifications::classificationGetBestGoalsMadeByIdLeague($idCompetition);
    $leastGoalsReceived = Classifications::classificationGetLeastGoalsReceived($idCompetition);
    $maxGoalsPerLeague = Players::maxGoalsPerLeague($idCompetition);
    $totalPlayed = Leagues::totalPlayed($idCompetition);
    return ['matchesList' => $matchesList, 'classification' => $classification, 'bestGoalsMade' => $bestGoalsMade, 'leastGoalsReceived' => $leastGoalsReceived, 'maxGoalsPerLeague' => $maxGoalsPerLeague, 'totalPlayed' => $totalPlayed];
});
Route::get("competicio/acta/{idMatch}", function ($idMatch) {
    $matchGetInfoById = Matches::matchGetInfoById($idMatch);
    return ['matchGetInfoById' => $matchGetInfoById];
});
Route::get("merchandising", function () {
    return Merchandisings::merchandisingReturnFiveRandomItems();
});
Route::get("agenda", function () {
    return Agenda::get();
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
