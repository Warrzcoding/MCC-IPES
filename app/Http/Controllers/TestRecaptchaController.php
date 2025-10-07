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

    /**
     * Test reCAPTCHA v3 verification with a token
     */
    public function testVerification(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No token provided',
                'help' => 'Send a POST request with a "token" parameter containing the reCAPTCHA v3 token'
            ], 400);
        }

        $result = $this->recaptchaService->verifyV3($token, 'login');

        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'verification_result' => $result,
            'interpretation' => [
                'is_valid' => $result['success'],
                'score' => $result['score'] ?? 0,
                'score_interpretation' => $this->interpretScore($result['score'] ?? 0),
                'error_codes' => $result['error_codes'] ?? [],
                'error_explanation' => $this->explainErrorCodes($result['error_codes'] ?? [])
            ]
        ]);
    }

    private function interpretScore($score)
    {
        if ($score >= 0.9) return 'Excellent - Very likely a human';
        if ($score >= 0.7) return 'Good - Likely a human';
        if ($score >= 0.5) return 'Fair - Possibly a human';
        if ($score >= 0.3) return 'Poor - Possibly a bot';
        return 'Very Poor - Likely a bot';
    }

    private function explainErrorCodes($errorCodes)
    {
        if (empty($errorCodes)) {
            return 'No errors';
        }

        $explanations = [];
        foreach ($errorCodes as $code) {
            switch ($code) {
                case 'missing-input-secret':
                    $explanations[] = 'The secret parameter is missing';
                    break;
                case 'invalid-input-secret':
                    $explanations[] = 'The secret parameter is invalid or malformed';
                    break;
                case 'missing-input-response':
                    $explanations[] = 'The response parameter is missing';
                    break;
                case 'invalid-input-response':
                    $explanations[] = 'The response parameter is invalid or malformed';
                    break;
                case 'bad-request':
                    $explanations[] = 'The request is invalid or malformed';
                    break;
                case 'timeout-or-duplicate':
                    $explanations[] = 'The response is no longer valid: either is too old or has been used previously';
                    break;
                default:
                    $explanations[] = "Unknown error code: {$code}";
            }
        }

        return implode('; ', $explanations);
    }
}