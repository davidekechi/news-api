<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RetrieveArticlesRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'query'        => 'sometimes|string|min:2|max:255',
            'sources'      => 'sometimes|array',
            'sources.*'    => 'uuid|exists:sources,uuid',
            'categories'   => 'sometimes|array',
            'categories.*' => 'uuid|exists:categories,uuid',
            'from_date'    => 'sometimes|date',
            'to_date'      => 'sometimes|date|after_or_equal:from_date',
            'page'         => 'sometimes|integer|min:1',
            'per_page'     => 'sometimes|integer|min:1|max:100',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = [];
        foreach ($validator->errors()->getMessages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field'   => $field,
                    'message' => $message,
                ];
            }
        }

        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'Validation failed.',
            'data'    => [
                'item' => null,
            ],
            'errors' => $errors,
            'meta'   => [
                'pagination' => null,
            ],
        ], 422));
    }
}
