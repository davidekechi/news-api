<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Full name is required.',
            'name.min'           => 'Full name must be at least 2 characters.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please provide a valid email address.',
            'email.unique'       => 'This email address is already registered.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

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
