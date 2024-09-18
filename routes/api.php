<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');



// Admin routes
Route::group(['middleware' => ['role:admin']], function () {
    Route::resource('products', 'ProductController');
    Route::resource('users', 'UserController');
});

// User routes
Route::group(['middleware' => ['role:user']], function () {
    Route::get('products', 'ProductController@index');
    Route::post('buy', 'OrderController@store');
});
