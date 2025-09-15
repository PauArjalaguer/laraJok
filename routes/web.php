<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ClubsController;
use App\Http\Controllers\CompeticioController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\MerchandisingsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PavellonsController;
use App\Http\Controllers\PlayersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UserController;
use App\Models\Merchandisings;
use Illuminate\Support\Facades\Route;

use App\Models\News;

use Illuminate\Support\Facades\Auth;


Route::get('/', [MainController::class, 'index'])->name("main");
Route::get('/equip/{id}/{label}', [TeamsController::class, 'index']);
Route::get('/club/{id}/{label}', [ClubsController::class, 'index']);
Route::get('/jugador/{id}/{label}', [PlayersController::class, 'index']);
Route::get('/competicio/{id}/{label}', [CompeticioController::class, 'index']);
Route::get('/competicio/{id}/{label}/{round}', [CompeticioController::class, 'index']);
Route::get('/competicions', [CompeticioController::class, 'llistat']);
Route::get('/acta/{id}/{label}', [CompeticioController::class, 'acta']);
Route::get('/desa/{item}/{id}', [UserController::class, 'store'])->middleware(['auth', 'verified']);

Route::get("/merchandising", [MerchandisingsController::class, 'index']);

Route::get("/noticies/detall/{id}/{label}", [NewsController::class, 'detall']);
Route::get("/noticies", [NewsController::class, 'index']);
Route::get("/agenda", [AgendaController::class, 'index']);
Route::get("/calendari", [AgendaController::class, 'calendari']);
Route::get("/pavellons/{id}/{label}", [PavellonsController::class, 'detall']);
Route::get("/pavellons", [PavellonsController::class, 'index']);

//dashboard noticies
Route::get('/dashboard/news/new/', [NewsController::class,'create'])->middleware(['auth', 'verified'])->name('dashboard.news.new');
Route::get('/dashboard/news/edit/{id}', [NewsController::class,'edit'])->middleware(['auth', 'verified'])->name('dashboard.news.edit');
Route::put('/dashboard/news/save/{id}', [NewsController::class,'update'])->middleware(['auth', 'verified']);
Route::get('/dashboard/news/delete/{id}', [NewsController::class,'delete'])->middleware(['auth', 'verified'])->name('dashboard.news.delete');
Route::get('/dashboard/news', function () { return view('dashboard_news',['newsList'=>News::orderBy('idNew','desc')->get()]);})->middleware(['auth', 'verified'])->name('dashboard.news');

Route::get('upload-ui', [FileUploadController::class, 'dropzoneUi' ]);
Route::post('file-upload', [FileUploadController::class, 'dropzoneFileUpload' ])->name('dropzoneFileUpload');

Route::get('/dashboard/merchandising/new/', [MerchandisingsController::class,'create'])->middleware(['auth', 'verified'])->name('dashboard.merchandising.new');
Route::get('/dashboard/merchandising/edit/{id}', [MerchandisingsController::class,'edit'])->middleware(['auth', 'verified'])->name('dashboard.merchandising.edit');
Route::put('/dashboard/merchandising/save/{id}', [MerchandisingsController::class,'update'])->middleware(['auth', 'verified']);
Route::get('/dashboard/merchandising/delete/{id}', [MerchandisingsController::class,'delete'])->middleware(['auth', 'verified'])->name('dashboard.merchandising.delete');
Route::get('/dashboard/merchandising', function () { return view('dashboard_merchandising',['merchandisingList'=>Merchandisings::orderBy('idAsset','desc')->get()]);})->middleware(['auth', 'verified'])->name('dashboard.merchandising');
Route::get("/privacitat", function(){
    return view("privacitat");
 });

Route::get("/efsmasquefa", function (){
    return view('masquefa_legal');});

Route::get('/dashboard', function () {
    return view('dashboard');
    // return redirect()->route('main');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});





require __DIR__ . '/auth.php';

