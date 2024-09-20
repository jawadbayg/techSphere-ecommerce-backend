<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        
        $users = User::all();
        
        
        $totalUsers = $users->count();
        
        
        return response()->json([
            'total_users' => $totalUsers,
            'users' => $users
        ], 200);
    }
    
    public function updateProfile(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'sometimes|string|min:8|confirmed', 
        ]);

        
        $user = Auth::user();

        
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user,
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully!'], 200);
    }
    
}
