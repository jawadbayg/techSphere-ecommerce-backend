<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        // Get all users
        $users = User::all();
        
        // Count the total number of users
        $totalUsers = $users->count();
        
        // Return the total number of users and the details of all users
        return response()->json([
            'total_users' => $totalUsers,
            'users' => $users
        ], 200);
    }
}
