<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');

  
    
    // Fetch all products (accessible by both admins and regular users)
Route::get('/products-list', [ProductController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/products', function (Request $request) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return app(App\Http\Controllers\ProductController::class)->store($request);
            }
    
            return response()->json(['message' => 'Unauthorized.'], 403);
        });
        
        Route::put('/products/{id}', function (Request $request, $id) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return app(App\Http\Controllers\ProductController::class)->update($request, $id);
            }
    
            return response()->json(['message' => 'Unauthorized.'], 403);
        });
    
        Route::delete('/products/{id}', function ($id) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return app(App\Http\Controllers\ProductController::class)->destroy($id);
            }
    
            return response()->json(['message' => 'Unauthorized.'], 403);
        });

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/users-list', function (Request $request) {
                if (Auth::check() && Auth::user()->role === 'admin') {
                    return app(App\Http\Controllers\UserController::class)->getUsers($request);
                }
        
                return response()->json(['message' => 'Unauthorized.'], 403);
            });
        });

        Route::middleware('auth:sanctum')->group(function () {
            Route::put('/update-profile', function (Request $request) {
                if (Auth::check() && Auth::user()->role === 'user')  {
                    return app(App\Http\Controllers\UserController::class)->updateProfile($request);
                }
        
                return response()->json(['message' => 'Unauthorized.'], 403);
            });
        });

        Route::post('/orders', [OrderController::class, 'store']);
        
        // Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'updateProfile']);

          
    });
    