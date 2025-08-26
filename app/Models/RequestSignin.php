<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSignin extends Model
{
    use HasFactory;

    protected $table = 'request_signin';

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'school_id',
        'role',
        'profile_image',
        'course',
        'year_level',
        'section',
        'status',
        'is_main_admin',
        'last_login',
        'last_active_at',
        'email_verified_at',
        'remember_token',
    ];
} 