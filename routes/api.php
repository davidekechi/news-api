<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route::middleware('throttle:api')->middleware('validate.api_key')->prefix('v1')->group(function () {
//     Route::middleware('throttle:short')->prefix('auth')->group(function () {
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
//     });

Route::middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('me', [AuthController::class, 'me']);

    // Articles management
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/filters', [ArticleController::class, 'filters']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
// });
