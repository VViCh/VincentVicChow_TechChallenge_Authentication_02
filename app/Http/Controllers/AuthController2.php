<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController2 extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->sendVerificationEmail();

        return response()->json(['message' => 'Registration successful! Please verify your email.'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Please verify your email before logging in.'], 403);
        }

        $otp = new Otp();
        $otp->user_id = $user->id;
        $otp->generateOtp();

        Mail::to($user->email)->send(new OtpMail($otp->otp));

        return response()->json(['message' => 'OTP sent to your email.', 'user_id' => $user->id], 200);
    }

    public function verifyOtp(Request $request, $userId)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::findOrFail($userId);
        $otp = $user->otps()->latest()->first();

        if (!$otp || $otp->otp !== $request->otp || $otp->isExpired()) {
            return response()->json(['error' => 'The provided OTP is invalid or expired.'], 400);
        }

        $otp->delete();

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Login successful!'], 200);
    }

    public function sendResetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->sendPasswordResetEmail();

        return response()->json(['message' => 'Password reset email sent!'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return response()->json(['message' => 'Password has been reset!'], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'You have been logged out.'], 200);
    }
}