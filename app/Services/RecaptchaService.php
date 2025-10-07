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
            return ['success' => false, 'score' => 0, 'error' => 'reCAPTCHA not configured', 'error_codes' => ['missing-secret-key']];
        }

        try {
            $requestData = [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ];
            
            Log::info('reCAPTCHA v3: Sending verification request', [
                'action' => $action,
                'ip' => $remoteIp ?? request()->ip(),
                'token_length' => strlen($token)
            ]);
            
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', $requestData);

            $result = $response->json();
            
            // Log the full response for debugging
            Log::info('reCAPTCHA v3: Google API response', [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? null,
                'hostname' => $result['hostname'] ?? null,
                'error_codes' => $result['error-codes'] ?? [],
                'challenge_ts' => $result['challenge_ts'] ?? null
            ]);
            
            return [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? null,
                'challenge_ts' => $result['challenge_ts'] ?? null,
                'hostname' => $result['hostname'] ?? null,
                'error_codes' => $result['error-codes'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA v3 verification exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'score' => 0, 'error' => 'Verification failed', 'error_codes' => ['exception']];
        }
    }

    /**
     * Determine which reCAPTCHA type to use based on context
     * Uses v3 only - no fallback to v2
     */
    public function determineCaptchaType($failedAttempts, $userRole, $isPasswordReset = false)
    {
        // Check if reCAPTCHA v3 key is configured
        $hasV3Key = !empty(config('services.recaptcha.site_key_v3'));

        // If v3 key is not configured, return null (no captcha)
        if (!$hasV3Key) {
            Log::info('reCAPTCHA: v3 key not configured, skipping verification');
            return null;
        }

        // Always use v3 for all cases
        Log::info("reCAPTCHA: Using v3 for {$userRole} with {$failedAttempts} failed attempts");
        return 'v3';
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