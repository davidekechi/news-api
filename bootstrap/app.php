<?php

use App\Http\Middleware\ApiExceptionHandlerMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [ApiExceptionHandlerMiddleware::class]);
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
