<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent', 'status', 'latitude', 'longitude', 'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}