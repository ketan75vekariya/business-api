<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $exception) {
            \Log::error('Registration error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    // Login a user
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        try {
            $user = User::where('email', strtolower($request->email))->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $exception) {
            \Log::error('Login error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    //Logout
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'user has been logged out successfully'
        ],200);
    }
}

