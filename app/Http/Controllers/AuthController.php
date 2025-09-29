<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 *
 * APIs for user authentication, registration, and account management.
 */
class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])
                ->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return $this->validationError(
                    'The provided credentials are incorrect.',
                    'email'
                );
            }

            // Revoke all existing tokens
            $user->tokens()->delete();

            $token     = $user->createToken('auth_token')->plainTextToken;
            $expiresIn = config('sanctum.expiration') ? config('sanctum.expiration') * 60 : null;

            return $this->successItem(
                [
                    'item' => [
                        'user'       => new UserResource($user),
                        'token'      => $token,
                        'expires_in' => $expiresIn,
                    ],
                ],
                'Login successful.',
                200,
                [
                    'auth' => [
                        'token_type'         => 'Bearer',
                        'expires_in_seconds' => $expiresIn,
                    ],
                ]
            );
        } catch (ValidationException $e) {
            return $this->validationError(
                'The provided credentials are incorrect.',
                'email',
                $e->errors()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                422,
                [
                    'error' => 'Login failed due to an internal error.',
                ],
                [
                    'pagination' => null,
                ],
            );
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Create the user
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Generate token for the new user
            $token     = $user->createToken('auth_token')->plainTextToken;
            $expiresIn = config('sanctum.expiration') ? config('sanctum.expiration') * 60 : null;

            return $this->successItem(
                [
                    'item' => [
                        'user'       => new UserResource($user),
                        'token'      => $token,
                        'expires_in' => $expiresIn,
                    ],
                ],
                'Registration successful.',
                201,
                [
                    'auth' => [
                        'token_type'         => 'Bearer',
                        'expires_in_seconds' => $expiresIn,
                    ],
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                422,
                [
                    'error' => 'Registration failed due to an internal error.',
                ],
                [
                    'pagination' => null,
                ]
            );
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return $this->successItem(
                [
                    'item' => new UserResource($user),
                ],
                'User profile retrieved successfully.',
                200,
                [
                    'pagination' => null,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                422,
                [
                    'field'   => 'user',
                    'message' => 'User profile retrieval failed due to an internal error.',
                ]
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successMessage('Logged out successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Logout failed. Please try again later.',
                422,
                [
                    'field'   => 'logout',
                    'message' => 'Logout failed due to an internal error.',
                ]
            );
        }
    }
}
