<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandlerMiddleware
{
    /**
     * Handle an incoming request and format all exceptions
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Handle and format exceptions
     */
    private function handleException(Throwable $e, Request $request): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException       => $this->formatValidationException($e),
            $e instanceof AuthenticationException   => $this->formatAuthenticationException($e),
            $e instanceof ThrottleRequestsException => $this->formatRateLimitException($e),
            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException => $this->formatNotFoundException($e),
            default                             => $this->formatGenericException($e),
        };
    }

    private function formatValidationException(ValidationException $e): JsonResponse
    {
        $errors = collect($e->errors())->map(function ($messages, $field) {
            return [
                'field'   => $field,
                'message' => \is_array($messages) ? $messages[0] : $messages,
            ];
        })->values()->toArray();

        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'The given data was invalid.',
            'data'    => ['item' => null],
            'errors'  => $errors,
            'meta'    => ['pagination' => null],
        ], 422);
    }

    private function formatAuthenticationException(AuthenticationException $e): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'code'    => 401,
            'message' => 'Unauthenticated.',
            'data'    => ['item' => null],
            'errors'  => [
                [
                    'field'   => 'auth',
                    'message' => 'Authentication credentials are missing or invalid.',
                ],
            ],
            'meta' => ['pagination' => null],
        ], 401);
    }

    private function formatRateLimitException(ThrottleRequestsException $e): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'code'    => 429,
            'message' => 'Too Many Requests.',
            'data'    => ['item' => null],
            'errors'  => [
                [
                    'field'   => 'rate_limit',
                    'message' => 'Too many requests. Please try again later.',
                ],
            ],
            'meta' => [
                'pagination' => null,
                'rate_limit' => [
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                ],
            ],
        ], 429);
    }

    /**
     * Format not found exceptions
     *
     * @param ModelNotFoundException<\Illuminate\Database\Eloquent\Model>|NotFoundHttpException $e
     */
    /**
     * @phpstan-ignore-next-line missingType.generics
     */
    private function formatNotFoundException(ModelNotFoundException|NotFoundHttpException $e): JsonResponse
    {
        $message = $e instanceof ModelNotFoundException
            ? 'Resource not found.'
            : ($e->getMessage() ?: 'The requested resource was not found.');

        return response()->json([
            'status'  => 'error',
            'code'    => 404,
            'message' => 'Not Found.',
            'data'    => ['item' => null],
            'errors'  => [
                [
                    'field'   => 'resource',
                    'message' => $message,
                ],
            ],
            'meta' => ['pagination' => null],
        ], 404);
    }

    private function formatGenericException(Throwable $e): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => $e->getMessage(),
            'data'    => ['item' => null],
            'errors'  => [
                [
                    'field'   => 'system',
                    'message' => 'An unexpected error occurred.',
                ],
            ],
            'meta' => ['pagination' => null],
        ], 422);
    }
}
