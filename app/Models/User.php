<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

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
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'last_active_at' => 'datetime',
        'is_main_admin' => 'boolean',
        'password' => 'hashed',
    ];

    // Helper method to check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Helper method to check if user is student
    public function isStudent()
    {
        return $this->role === 'student';
    }

    // Helper method to check if user is main admin
    public function isMainAdmin()
    {
        return $this->is_main_admin === true;
    }

    // Helper method to check if user can manage admins (only main admin can)
    public function canManageAdmins()
    {
        return $this->isMainAdmin();
    }

    // Update last active time
    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
    }
}