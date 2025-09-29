<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

// Route::middleware('throttle:api')->middleware('validate.api_key')->prefix('v1')->group(function () {
//     Route::middleware('throttle:short')->prefix('auth')->group(function () {
//         Route::post('login', [AuthController::class, 'login']);
//         Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
//         Route::post('validate-reset-code', [AuthController::class, 'validateResetCode']);
//         Route::post('reset-password', [AuthController::class, 'resetPassword']);
//     });

// Route::middleware('auth:sanctum')->group(function () {
Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/filters', [ArticleController::class, 'filters']);

// Route::post('/logout', [AuthController::class, 'logout']);
// });
// });
