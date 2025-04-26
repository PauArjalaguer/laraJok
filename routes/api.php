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
use App\Models\User;
use App\Models\Pavellons;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/* cerca */

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

/* main */
Route::get("/main", function () {
    $userSavedData = User::userSavedData();
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'matchesListNext' => Matches::matchesListNext($userSavedData),
        'matchesListLastWithResults' => Matches::matchesListLastWithResults($userSavedData),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => $userSavedData,
        'newsListTop' => News::orderBy('newsDateTime', 'desc')->where('website', 'jokcat')->limit(4)->get()
    ]);
});

/*competicio*/
Route::get("/competicio/{id_league}/{round}", function ($id_league, $round = 1) {
    return response()->json([
        'leaguesList' => Leagues::leaguesList(),
        'clubsList' => Clubs::clubsList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'matchesList' => Matches::matchesListFromIdLeague($id_league),
        'classification' => Classifications::classificationGetByIdGroup($id_league),
        'bestGoalsMade' => Classifications::classificationGetBestGoalsMadeByIdLeague($id_league),
        'leastGoalsReceived' => Classifications::classificationGetLeastGoalsReceived($id_league),
        'maxGoalsPerLeague' => Players::maxGoalsPerLeague($id_league),
        'totalPlayed' => Leagues::totalPlayed($id_league),
        'checkIfSaved' => User::checkIfSaved('competicio', $id_league),
        'userSavedData' => User::userSavedData(),
        'round' => $round
    ]);
});

/*clubs*/
Route::get("/club/{id_club", function ($id_club) {
    $userSavedData = "";
    return response()->json([
        'leaguesList' => Leagues::leaguesList(),
        'clubsList' => Clubs::clubsList(),
        'clubInfo' => Clubs::where('idClub', $id_club)->get(),
        'teamsList' => Teams::teamsByIdClub($id_club),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'checkIfSaved' => User::checkIfSaved('club', $id_club),
        'userSavedData' => User::userSavedData(),
        'classifications' => Classifications::classificationGetByIdClub($id_club),
        'matchesListNext' => Matches::matchesListNext($userSavedData, $id_club),
        'matchesListLastWithResults' => Matches::matchesListLastWithResults($userSavedData, $id_club)
    ]);
});
/*merchandising*/
Route::get("/merchandising", function () {
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingListAll' => Merchandisings::whereNotNull('assetCategory')->orderBy('assetName', 'asc')->get(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => User::userSavedData(),
        'merchandisingReturnCategories' => Merchandisings::merchandisingReturnCategories()
    ]);
});

/*news*/
Route::get("/news/{id_new}", function ($id_new) {
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => User::userSavedData(),
        'newsDetail' => News::where('idNew', $id_new)->get()
    ]);
});
Route::get("/news", function () {
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => User::userSavedData(),
        'newsListTop' => News::orderBy('newsDateTime', 'desc')->where('website', 'jokcat')->simplePaginate(5)
    ]);
});
Route::get("/pavellons/{id_pavello}", function ($id_pavello) {
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => User::userSavedData(),
        'partits_pavello' => Matches::matchesListFromIdPavello($id_pavello)
    ]);
});

Route::get("/pavellons", function () {
    return response()->json([
        'clubsList' => Clubs::clubsList(),
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'userSavedData' => User::userSavedData(),
        'pavellons' => Pavellons::whereNotNull('lat')->with('matches')->get()
    ]);
});

/* players */
Route::get("/jugadors/{id_player}", function ($id_player) {
    return response()->json([
        'leaguesList' => Leagues::leaguesList(),
        'clubsList' => Clubs::clubsList(),
        'playerInfo' => Players::where('idPlayer', $id_player)->get(),
        'playerMatchesList' => Matches::matchesListFromIdPlayer($id_player),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'playerStats' => Players::playerStats($id_player),
        'checkIfSaved' => User::checkIfSaved('jugador', $id_player),
        'userSavedData' => User::userSavedData()
    ]);
});
/*equips*/
Route::get("/equips/{id_team}", function ($id_team) {
    return response()->json([
        'leaguesList' => Leagues::leaguesList(),
        'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        'clubsList' => Clubs::clubsList(),
        'teamLeaguesList' => Teams::teamLeaguesList($id_team),
        'teamInfo' => Teams::teamInfo($id_team),
        'teamGoals'=>Teams::teamGoals($id_team),
        'playersList' => Players::playersByIdTeam($id_team),
        'checkIfSaved' => User::checkIfSaved('equip', $id_team),
        'userSavedData' => User::userSavedData()
    ]);
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
