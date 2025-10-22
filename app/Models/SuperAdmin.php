<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    protected $table = 'super_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login',
        'failed_login_attempts',
        'locked_until',
        'is_locked',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'locked_until' => 'datetime',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if account is currently locked
     */
    public function isAccountLocked()
    {
        if (!$this->is_locked) {
            return false;
        }

        // Check if lock has expired
        if ($this->locked_until && now()->isAfter($this->locked_until)) {
            $this->update([
                'is_locked' => false,
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
            return false;
        }

        return $this->is_locked;
    }

    /**
     * Get remaining lock time in seconds
     */
    public function getRemainingLockTime()
    {
        if (!$this->locked_until) {
            return 0;
        }

        $remaining = now()->diffInSeconds($this->locked_until, false);
        return max(0, $remaining);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts()
    {
        $this->increment('failed_login_attempts');

        // Lock account if 3 attempts reached
        if ($this->failed_login_attempts >= 3) {
            $this->update([
                'is_locked' => true,
                'locked_until' => now()->addMinutes(1),
            ]);
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'is_locked' => false,
            'locked_until' => null,
        ]);
    }
}
