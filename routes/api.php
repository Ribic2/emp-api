<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', [AuthController::class, 'me']);

    Route::prefix('/movie')->group(function (){
        Route::get('/', [MovieController::class, 'movies']);
        Route::get('/recommended', [MovieController::class, 'getRecommendedMovies']);
        Route::get('/filter', [FilterController::class, 'getFilterData']);
        Route::get('/{id}', [MovieController::class, 'movie']);
        Route::post('/{id}/like', [MovieController::class, 'like']);
        Route::post('/comment', [CommentController::class, 'addComment']);
    });
});
