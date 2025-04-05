<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Password;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendVerificationEmail()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }

    public function sendPasswordResetEmail()
    {
        $token = Password::createToken($this);
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }
}
