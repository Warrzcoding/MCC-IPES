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
        'admin_otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'last_active_at' => 'datetime',
        'admin_otp_expires_at' => 'datetime',
        'admin_otp_last_sent_at' => 'datetime',
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
        return (bool) $this->is_main_admin;
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

    /**
     * Get disabled sidebar features for this admin
     */
    public function getDisabledSidebarFeatures(): array
    {
        try {
            if (!$this->isMainAdmin()) {
                // Non-main admins should check the main admin's settings
                $mainAdmin = self::where('is_main_admin', true)->first();
                if ($mainAdmin) {
                    return SidebarSetting::getDisabledFeaturesForAdmin($mainAdmin->id);
                }
                return [];
            }

            // Main admin gets their own settings
            return SidebarSetting::getDisabledFeaturesForAdmin($this->id);
        } catch (\Exception $e) {
            \Log::error('Error in getDisabledSidebarFeatures: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Set disabled sidebar features for this admin (only main admin can do this)
     */
    public function setDisabledSidebarFeatures(array $features): void
    {
        if ($this->isMainAdmin()) {
            SidebarSetting::setDisabledFeaturesForAdmin($this->id, $features);
        }
    }
}