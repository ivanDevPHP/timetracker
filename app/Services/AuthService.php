<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => "required|string",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|confirmed",
            "is_admin" => "required|boolean"
        ]);

        $user = User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => Hash::make($data["password"]),
            "is_admin" => $data["is_admin"]
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            "user" => $user,
            "token" => $token
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'user' => Auth::user(),
            'token' => $token,
        ]);
    }
}

