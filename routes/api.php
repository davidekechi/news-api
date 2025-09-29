<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->prefix('v1')->group(function () {
    // Articles management
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/filters', [ArticleController::class, 'filters']);
});
