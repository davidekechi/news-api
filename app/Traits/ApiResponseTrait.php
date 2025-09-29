<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    /**
     * Success response for collections with pagination
     *
     * @template TValue
     * @param LengthAwarePaginator<int, TValue> $paginator
     * @param array<int|string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function successCollection(
        LengthAwarePaginator $paginator,
        array $data,
        string $message = 'Data retrieved successfully.',
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => $message,
            'data'    => $data,
            'errors'  => [],
            'meta'    => \array_merge([
                'pagination' => [
                    'total'       => $paginator->total(),
                    'perPage'     => $paginator->perPage(),
                    'currentPage' => $paginator->currentPage(),
                    'lastPage'    => $paginator->lastPage(),
                    'totalPages'  => $paginator->lastPage(),
                    'hasNextPage' => $paginator->hasMorePages(),
                    'hasPrevPage' => $paginator->currentPage() > 1,
                    'from'        => $paginator->firstItem(),
                    'to'          => $paginator->lastItem(),
                ],
            ], $meta),
        ], 200);
    }

    /**
     * Success response with custom data
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    protected function successItem(
        array $data,
        string $message = 'Operation completed successfully.',
        int $statusCode = 200,
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'status'  => 'success',
            'code'    => $statusCode,
            'message' => $message,
            'data'    => $data,
            'errors'  => [],
            'meta'    => \array_merge(['pagination' => null], $meta),
        ], $statusCode);
    }

    /**
     * Success response with no data
     */
    protected function successMessage(
        string $message = 'Operation completed successfully.',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'status'  => 'success',
            'code'    => $statusCode,
            'message' => $message,
            'data'    => ['item' => null],
            'errors'  => [],
            'meta'    => ['pagination' => null],
        ], $statusCode);
    }

    /**
     * Error response
     *
     * @param array<string, mixed> $errors
     * @param array<string, mixed> $meta
     */
    protected function errorResponse(
        string|null $message,
        int $statusCode = 400,
        array $errors = [],
        array $meta = [],
    ): JsonResponse {
        return response()->json([
            'status'  => 'error',
            'code'    => $statusCode,
            'message' => $message,
            'data'    => ['item' => null],
            'errors'  => $errors,
            'meta'    => \array_merge(['pagination' => null], $meta),
        ], $statusCode);
    }

    /**
     * Validation error response
     *
     * @param mixed $data
     */
    protected function validationError(
        string $message,
        string $field,
        mixed $data = null,
    ): JsonResponse {
        return $this->errorResponse(
            $message,
            422,
            [
                'field'   => $field,
                'message' => $message,
                'data'    => $data,
            ],
        );
    }

    /**
     * Not found error response
     */
    protected function notFoundError(string $resource, string $id): JsonResponse
    {
        return $this->errorResponse(
            "{$resource} not found.",
            404,
            [
                'field'   => 'id',
                'message' => "No {$resource} with ID '{$id}' exists.",
            ]
        );
    }
}
