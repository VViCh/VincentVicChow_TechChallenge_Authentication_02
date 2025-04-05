<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }
    
    public function generateOtp()
    {
        $this->otp = rand(100000, 999999);
        $this->expires_at = now()->addMinutes(5);
        $this->save();
    }
}
