<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('/movies', [MovieController::class, 'movie']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', [AuthController::class, 'me']);
    Route::get('/movies', [MovieController::class, 'movies']);
    Route::get('/movie/{id}', [MovieController::class, 'movie']);
    Route::post('/movie/{id}/like', [MovieController::class, 'like']);
    Route::get('/filters', [FilterController::class, 'getFilterData']);
    Route::post('/movie/{id}/favourite', [MovieController::class, 'favourite']);
    Route::post('/comment/add', [CommentController::class, 'addComment']);
});
