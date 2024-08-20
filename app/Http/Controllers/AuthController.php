<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to log in the user
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        // Retrieve the authenticated user and generate a token
        $user = Auth::user();
        $token = $user->createToken('')->plainTextToken;

        // Return the generated token
        return response()->json(['token' => $token], 200);

    } catch (\Exception $e) {
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
