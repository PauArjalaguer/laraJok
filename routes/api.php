<?php

use App\Models\Clubs;
use App\Models\Players;
use App\Models\Teams;
use App\Models\Leagues;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    return Players::where('playerName', 'like', '%' . $search . '%')->limit(100)->orderBy('playerName', 'desc')->get();
});

Route::get("/leagues", function () {
    return Leagues::all();
});
Route::get("/news/{website}", function ($website) {
    return News::where('website', 'like', '%' . $website . '%')->limit(100)->orderBy('newsDateTime', 'desc')->get();
});
Route::get("/news/{website}/{id}", function ($website,$id) {
    return News::where('idNew',  $id)->get();
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
