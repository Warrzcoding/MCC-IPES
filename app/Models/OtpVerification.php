<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'type',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Generate a new OTP for the given email and type
     */
    public static function generateOtp($email, $type = 'pre_signup', $expiryMinutes = 5)
    {
        // Delete any existing unused OTPs for this email and type
        self::where('email', $email)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create new OTP record
        return self::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'is_used' => false
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verifyOtp($email, $otpCode, $type = 'pre_signup')
    {
        $otp = self::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            // Mark as used
            $otp->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanupExpired()
    {
        self::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }
}
