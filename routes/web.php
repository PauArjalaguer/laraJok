<?php

use App\Http\Controllers\ClubsController;
use App\Http\Controllers\CompeticioController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MerchandisingsController;
use App\Http\Controllers\PlayersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Players;
use App\Models\Classifications;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', [MainController::class, 'index'])->name("main");
Route::get('/equip/{id}/{label}', [TeamsController::class, 'index']);
Route::get('/club/{id}/{label}', [ClubsController::class, 'index']);
Route::get('/jugador/{id}/{label}', [PlayersController::class, 'index']);
Route::get('/competicio/{id}/{label}', [CompeticioController::class, 'index']);
Route::get('/acta/{id}/{label}', [CompeticioController::class, 'acta']);
Route::get('/desa/{item}/{id}', [UserController::class, 'store'])->middleware(['auth', 'verified']);

Route::get("/merchandising", [MerchandisingsController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
    // return redirect()->route('main');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect()->route('main');
})->name('logout');

require __DIR__ . '/auth.php';
