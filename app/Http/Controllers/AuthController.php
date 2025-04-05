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

class AuthController extends Controller
{
    public function getRegisterPage()
    {
        return view('auth.register');
    }

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

        return redirect()->route('login')
            ->with('status', 'Registration successful! Please verify your email.');
    }

    public function getLoginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->with('status', 'Please verify your email before logging in.');
        }

        $otp = new Otp();
        $otp->user_id = $user->id;
        $otp->generateOtp();

        Mail::to($user->email)->send(new OtpMail($otp->otp));

        session(['otp' => $otp->otp, 'user_id' => $user->id]);

        return redirect()->route('auth.otp.page', $user->id)
            ->with('status', 'OTP sent to your email.');
    }

    public function getOtpPage($userId)
    {
        $user = User::findOrFail($userId);
        return view('auth.verify-otp', compact('user'));
    }

    public function verifyOtp(Request $request, $userId)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::findOrFail($userId);
        $otp = $user->otps()->latest()->first();

        if (!$otp || $otp->otp !== $request->otp || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is invalid or expired.'],
            ]);
        }

        $otp->delete();

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('otp');

        return redirect()->route('home')->with('status', 'Login successful!');
    }

    public function getForgotPasswordPage()
    {
        return view('auth.forgot-password');
    }

    public function sendResetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->sendPasswordResetEmail();

        return redirect()->route('login')->with('status', 'Password reset email sent!');
    }

    public function getResetPasswordPage($token)
    {
        return view('auth.reset-password', ['token' => $token]);
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

        return redirect()->route('auth.login.page')->with('status', 'Password has been reset!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }
}
