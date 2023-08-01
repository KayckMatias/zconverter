<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();

            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid credentials'
        ]);
    }
    public function logout()
    {
        if (auth()->check()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            Auth::logout();
        }

        return response()->noContent();
    }
}
