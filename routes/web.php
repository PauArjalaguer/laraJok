<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Teams;
use App\Models\Players;
use App\Models\Phases;
use App\Models\Classifications;

Route::get('/', function () {
    return view(
        'main',
        [
            'clubsList' => Clubs::clubsList(), 'leaguesList' => Leagues::leaguesList(),
            'matchesListNext' => Matches::matchesListNext(),
            'matchesListLastWithResults' => Matches::matchesListLastWithResults(),
            'merchandisingList' => Merchandisings::inRandomOrder()->get()
        ]
    );
});

Route::get('/equip/{id}/{label}', function ($id) {
    return view(
        'equip',
        [
            'leaguesList' => Leagues::leaguesList(), 'merchandisingList' => Merchandisings::all(),
            'clubsList' => Clubs::clubsList(),
            'teamLeaguesList' => Phases::whereIn('idGroup', function ($q) use ($id) {
                $q->from('matches')->select('idGroup')->where('idLocal', '=', $id)->orwhere('idVisitor',  $id)->toSql();
            })->orderBy('startDate', 'desc')->get(),
            'teamInfo' => Teams::join('clubs', 'clubs.idClub', '=', 'teams.idClub')->join('categories', 'categories.idCategory', '=', 'teams.idCategory')->where('idTeam', $id)->get(),
            'playersList' => Players::distinct("playerName")->select('players.idPlayer', 'playerName')->join("player_match", "player_match.idPlayer", "=", "players.idPlayer")
                ->where('idTeam', $id)->whereIn('player_match.idMatch', function ($q) use ($id) {
                    $q->from('matches')->select('idMatch')->where('idLocal', '=', $id)->orwhere('idVisitor',  $id)->toSql();
                })->get()
        ]
    );
});

Route::get('/club/{id}/{label}', function ($id) {
    return view(
        'club',
        [
            'leaguesList' => Leagues::leaguesList(),
            'clubsList' => Clubs::clubsList(),
            'teamsList' => Teams::join('categories', 'categories.idCategory', '=', 'teams.idCategory')->join('seasons', 'teams.idSeason', '=', 'seasons.idSeason')
            ->where('idClub', $id)->orderby('seasonName', 'desc')->orderby('categories.idCategory', 'asc')->orderby('teams.teamName', 'asc')->get(),
            'clubInfo' => Clubs::where('idClub', $id)->get(),
            'merchandisingList' => Merchandisings::all(),
        ]
    );
});

Route::get('/jugador/{id}/{label}', function ($id) {
    return view(
        'jugador',
        [
            'leaguesList' => Leagues::leaguesList(),
            'clubsList' => Clubs::clubsList(),
            'playerInfo' => Players::where('idPlayer', $id)->get(),
            'playerMatchesList' => Matches::matchesListFromIdPlayer($id),
            'merchandisingList' => Merchandisings::all(),
            'playerStats' => Players::playerStats($id),

        ]
    );
});


Route::get('/competicio/{id}/{label}', function ($id) {
    return view(
        'competicio',
        [
            'leaguesList' => Leagues::leaguesList(),
            'clubsList' => Clubs::clubsList(),
            'merchandisingList' => Merchandisings::all(),
            'matchesList' => Matches::matchesListFromIdLeague($id),
            'classification' => Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->where('idGroup', $id)->orderBy('points', 'desc')->orderBy('position', 'asc')->get(),
            'bestGoalsMade' => Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsMade', 'clubImage', 'teams.idTeam')->orderBy('goalsMade', 'desc')->limit(1)->get(),
            'leastGoalsReceived' => Classifications::join('teams', 'teams.idTeam', '=', 'classifications.idTeam')->join('clubs as club', 'club.idClub', 'teams.idClub')->where('idGroup', $id)->select('teamName', 'goalsReceived', 'clubImage', 'teams.idTeam')->orderBy('goalsReceived', 'asc', 'clubImage')->limit(1)->get(),


        ]
    );
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
