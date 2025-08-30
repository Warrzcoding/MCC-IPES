<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Verify reCAPTCHA v2 token
     */
    public function verifyV2($token, $remoteIp = null)
    {
        $secretKey = config('services.recaptcha.secret_key_v2');
        
        if (!$secretKey) {
            Log::warning('reCAPTCHA v2 secret key not configured');
            return ['success' => false, 'error' => 'reCAPTCHA not configured'];
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);

            $result = $response->json();
            
            return [
                'success' => $result['success'] ?? false,
                'error_codes' => $result['error-codes'] ?? [],
                'challenge_ts' => $result['challenge_ts'] ?? null,
                'hostname' => $result['hostname'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA v2 verification failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Verification failed'];
        }
    }

    /**
     * Verify reCAPTCHA v3 token and return score
     */
    public function verifyV3($token, $action = 'login', $remoteIp = null)
    {
        $secretKey = config('services.recaptcha.secret_key_v3');
        
        if (!$secretKey) {
            Log::warning('reCAPTCHA v3 secret key not configured');
            return ['success' => false, 'score' => 0, 'error' => 'reCAPTCHA not configured'];
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);

            $result = $response->json();
            
            return [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? null,
                'challenge_ts' => $result['challenge_ts'] ?? null,
                'hostname' => $result['hostname'] ?? null,
                'error_codes' => $result['error-codes'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA v3 verification failed: ' . $e->getMessage());
            return ['success' => false, 'score' => 0, 'error' => 'Verification failed'];
        }
    }

    /**
     * Determine which reCAPTCHA type to use based on context
     */
    public function determineCaptchaType($failedAttempts, $userRole, $isPasswordReset = false)
    {
        // Check if reCAPTCHA keys are configured
        $hasV2Key = !empty(config('services.recaptcha.site_key_v2'));
        $hasV3Key = !empty(config('services.recaptcha.site_key_v3'));
        
        // If no keys are configured, return null (no captcha)
        if (!$hasV2Key && !$hasV3Key) {
            Log::info('reCAPTCHA: No keys configured, skipping verification');
            return null;
        }

        // For password reset, always use checkbox for security if available
        if ($isPasswordReset) {
            return $hasV2Key ? 'checkbox' : ($hasV3Key ? 'v3' : null);
        }

        // For admin/staff, use more strict security
        if (in_array($userRole, ['admin', 'staff'])) {
            if ($failedAttempts >= 1 && $hasV2Key) {
                Log::info("reCAPTCHA: Using checkbox for {$userRole} with {$failedAttempts} failed attempts");
                return 'checkbox';
            }
            if ($hasV3Key) {
                Log::info("reCAPTCHA: Using v3 for {$userRole} with {$failedAttempts} failed attempts");
                return 'v3';
            }
            return $hasV2Key ? 'checkbox' : null;
        }

        // For students, be more lenient
        if ($userRole === 'student') {
            if ($failedAttempts >= 2 && $hasV2Key) {
                Log::info("reCAPTCHA: Using checkbox for {$userRole} with {$failedAttempts} failed attempts");
                return 'checkbox';
            }
            if ($hasV3Key) {
                Log::info("reCAPTCHA: Using v3 for {$userRole} with {$failedAttempts} failed attempts");
                return 'v3';
            }
            return $hasV2Key ? 'checkbox' : null;
        }

        // Default to v3 if available, otherwise v2, otherwise null
        if ($hasV3Key) {
            return 'v3';
        }
        return $hasV2Key ? 'checkbox' : null;
    }

    /**
     * Get minimum score threshold based on user role and context
     */
    public function getScoreThreshold($userRole, $isPasswordReset = false)
    {
        if ($isPasswordReset) {
            return 0.7; // Higher threshold for password reset
        }

        switch ($userRole) {
            case 'admin':
                return 0.7; // High security for admin
            case 'staff':
                return 0.6; // Medium-high security for staff
            case 'student':
                return 0.5; // Lower threshold for students
            default:
                return 0.5;
        }
    }
}