<?php

namespace App\Http\Controllers;

use App\Services\RecaptchaService;
use Illuminate\Http\Request;

class TestRecaptchaController extends Controller
{
    protected $recaptchaService;

    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->recaptchaService = $recaptchaService;
    }

    /**
     * Test reCAPTCHA configuration
     */
    public function testConfig()
    {
        $config = [
            'v2_site_key' => config('services.recaptcha.site_key_v2'),
            'v2_secret_key' => config('services.recaptcha.secret_key_v2') ? 'Set' : 'Not Set',
            'v3_site_key' => config('services.recaptcha.site_key_v3'),
            'v3_secret_key' => config('services.recaptcha.secret_key_v3') ? 'Set' : 'Not Set',
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'reCAPTCHA configuration check',
            'config' => $config,
            'recommendations' => [
                'v2_configured' => !empty(config('services.recaptcha.site_key_v2')),
                'v3_configured' => !empty(config('services.recaptcha.site_key_v3')),
                'ready_for_production' => !empty(config('services.recaptcha.site_key_v2')) && !empty(config('services.recaptcha.site_key_v3'))
            ]
        ]);
    }

    /**
     * Test reCAPTCHA type determination
     */
    public function testCaptchaType(Request $request)
    {
        $failedAttempts = $request->get('failed_attempts', 0);
        $userRole = $request->get('user_role', 'student');
        
        $captchaType = $this->recaptchaService->determineCaptchaType($failedAttempts, $userRole);
        $scoreThreshold = $this->recaptchaService->getScoreThreshold($userRole);

        return response()->json([
            'status' => 'success',
            'failed_attempts' => $failedAttempts,
            'user_role' => $userRole,
            'captcha_type' => $captchaType,
            'score_threshold' => $scoreThreshold,
            'explanation' => $this->getCaptchaExplanation($captchaType, $failedAttempts, $userRole)
        ]);
    }

    private function getCaptchaExplanation($captchaType, $failedAttempts, $userRole)
    {
        if ($captchaType === 'checkbox') {
            return "Checkbox reCAPTCHA required due to {$failedAttempts} failed attempts for {$userRole} role";
        } else {
            return "Invisible reCAPTCHA v3 for {$userRole} role with {$failedAttempts} failed attempts";
        }
    }
}