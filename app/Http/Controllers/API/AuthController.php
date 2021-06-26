<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use PDOException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
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

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required', 'regex:/(^([a-zA-z ]+)(\d+)?$)/u', 'min:5',
            'email' => 'required|email|unique:users',
            'password' => 'required', Password::min(8)->letters()->numbers(),
        ]);

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'data' => $user,
                'message' => 'Registration successful.'
            ], 201);
        } catch (PDOException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function user(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => auth()->user(),
            'message' => 'User info retrieved'
        ]);
    }
}
