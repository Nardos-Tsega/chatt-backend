<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Log;
class AuthController extends Controller
{
    public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password'=> 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
        ]);

        $token = $user->createToken('')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function login(Request $request)
{
    try {
        Log::info('Incoming login request:', $request->all());
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to log in the user
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Invalid login details', $request->only('email')); // Log invalid login attempt
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        // Retrieve the authenticated user and generate a token
        $user = Auth::user();
        $token = $user->createToken('')->plainTextToken;

        Log::info('Login successful for user:', ['email' => $user->email]);
        // Return the generated token
        return response()->json(['token' => $token], 200);

    } catch (\Exception $e) {
        Log::error('Login error:', ['error' => $e->getMessage()]);
        // Handle any exceptions that may occur
        return response()->json([
            'message' => 'An error occurred during login. Please try again later.',
            'error' => $e->getMessage(),  // Optionally include the error message for debugging
        ], 500);
    }
}
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message'=> 'Logged out successfully']);
    }
}
