<?php

use App\Http\Middleware\ApiExceptionHandlerMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            RateLimiter::for('api', function (Request $request) {
                return [Limit::perMinute(config('variables.rate_limiting.api'))->by($request->ip())];
            });

            RateLimiter::for('short', function (Request $request) {
                return [Limit::perMinute(config('variables.rate_limiting.short'))->by($request->user()?->id ?: $request->ip())];
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => $e->getMessage()   ,
                    'data'    => ['item' => null],
                    'errors'  => [
                        [
                            'field'   => 'system',      
                            'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.',
                        ],
                    ],
                    'meta'    => ['pagination' => null],
                ], 500);
            }
        });         
    })->create();   
