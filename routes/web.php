<?php

use App\Http\Controllers\ClubsController;
use App\Http\Controllers\CompeticioController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MerchandisingsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Models\News;

use Illuminate\Support\Facades\Auth;


Route::get('/', [MainController::class, 'index'])->name("main");
Route::get('/equip/{id}/{label}', [TeamsController::class, 'index']);
Route::get('/club/{id}/{label}', [ClubsController::class, 'index']);
Route::get('/jugador/{id}/{label}', [PlayersController::class, 'index']);
Route::get('/competicio/{id}/{label}', [CompeticioController::class, 'index']);
Route::get('/acta/{id}/{label}', [CompeticioController::class, 'acta']);
Route::get('/desa/{item}/{id}', [UserController::class, 'store'])->middleware(['auth', 'verified']);

Route::get("/merchandising", [MerchandisingsController::class, 'index']);

Route::get("/noticies/detall/{id}/{label}", [NewsController::class, 'detall']);
Route::get("/noticies", [NewsController::class, 'index']);


Route::get('upload-ui', [FileUploadController::class, 'dropzoneUi' ]);
Route::post('file-upload', [FileUploadController::class, 'dropzoneFileUpload' ])->name('dropzoneFileUpload');

Route::get('/dashboard/news/edit/{id}', [NewsController::class,'edit'])->middleware(['auth', 'verified'])->name('dashboard.news.edit');
Route::put('/dashboard/news/save/{id}', [NewsController::class,'update'])->middleware(['auth', 'verified']);
Route::get('/dashboard/news/delete/{id}', [NewsController::class,'delete'])->middleware(['auth', 'verified'])->name('dashboard.news.delete');;
Route::get('/dashboard/news', function () { return view('dashboardNews',['newsList'=>News::orderBy('idNew','desc')->get()]);})->middleware(['auth', 'verified'])->name('dashboard.news');



Route::get('/dashboard/merchandising', function () {
    return view('dashboard');
    // return redirect()->route('main');
})->middleware(['auth', 'verified'])->name('dashboard/merchandising');

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
