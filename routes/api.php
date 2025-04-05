<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController2;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController2::class, 'register']);
    Route::post('/login', [AuthController2::class, 'login']);
    Route::post('/otp/{user}', [AuthController2::class, 'verifyOtp']);
    Route::post('/forgot-password', [AuthController2::class, 'sendResetEmail']);
    Route::post('/reset-password', [AuthController2::class, 'resetPassword']);
    Route::post('/logout', [AuthController2::class, 'logout']);
});