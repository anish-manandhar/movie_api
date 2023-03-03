<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/most_liked_movies', [UserController::class, 'most_liked_movies'])->name('most_liked_movies');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::middleware('admin')->prefix('admin/movie')->group( function (){
        Route::post('/add', [AdminController::class, 'add'])->name('admin.movie.add');
        Route::post('/update', [AdminController::class, 'update'])->name('admin.movie.update');
        Route::post('/publish', [AdminController::class, 'publish'])->name('admin.movie.publish');
        Route::post('/users', [AdminController::class, 'users_fav_movie'])->name('admin.movie.user');
    });

    Route::prefix('user/fav_movie')->group( function (){
        Route::post('/add', [UserController::class, 'listing_fav_movies'])->name('user.fav_movie.add');
        Route::get('/list', [UserController::class, 'users_fav_movie'])->name('user.fav_movie.list');
    });
}); 
