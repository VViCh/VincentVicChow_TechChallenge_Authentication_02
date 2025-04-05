<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'getRegisterPage'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('/login', [AuthController::class, 'getLoginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::get('/forgot-password', [AuthController::class, 'getForgotPasswordPage'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'getResetPasswordPage'])->name('auth.reset.page');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/otp/{user}', [AuthController::class, 'getOtpPage'])->name('auth.otp.page');
    Route::post('/otp/{user}', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->name('verification.resend');
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect()->route('home')->with('status', 'Email verified!');
})->middleware(['signed'])->name('verification.verify');
