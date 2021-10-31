<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken($request->device_name)->plainTextToken,
            'message' => 'Login successful.'
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'regex:/(^([a-zA-z ]+)(\d+)?$)/u', 'min:5'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'], PasswordRules::min(8)->letters()->numbers(),
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'data' => $user,
                'message' => 'Registration successful.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'data' => auth()->user(),
            'message' => 'User info retrieved.'
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = auth()->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $response = Password::sendResetLink($credentials);

            $message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully.' : GLOBAL_SOMETHING_WENT_WRONG;
        } else {
            $message = 'No account associated with this email.';
        }

        return response()->json(['message' => $message]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRules::min(8)->letters()->numbers()],
        ]);

        $resetPasswordStatus = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($resetPasswordStatus == Password::INVALID_TOKEN) {
            return response()->json(['message' => 'Invalid token provided.'], 400);
        }

        return response()->json(['message' => 'Password has been successfully changed.']);
    }
}
