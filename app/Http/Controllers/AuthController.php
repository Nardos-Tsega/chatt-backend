<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
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

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=> 'required|string|email',
            'password'=> 'required|string',
        ]);

        if(!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message'=> 'Invalid login details',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('')->plainTextToken;

        return response()->json(['token'=> $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message'=> 'Logged out successfully']);
    }
}
