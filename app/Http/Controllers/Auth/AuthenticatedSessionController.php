<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken; // Generate token using Laravel Sanctum

            // Return a success response with user data and token
            return response()->json([
                'message' => 'User logged in successfully.',
                'user' => $user,
                'token' => $token
            ], 200);
        }

        // Return an error response if authentication fails
        return response()->json([
            'message' => 'Login failed.',
            'error' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Revoke the user's token
        $request->user()->currentAccessToken()->delete();
    
        return response()->json(['message' => 'Logged out successfully']);
    }
    
}
