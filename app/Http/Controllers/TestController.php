<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function checkGeolocation(Request $request)
    {
        $ip = $request->ip();
        $realIp = $this->getRealClientIp($request);
        $isSecure = $request->isSecure();

        Log::info('Geolocation Test', [
            'ip_from_request' => $ip,
            'real_client_ip' => $realIp,
            'is_secure' => $isSecure,
            'user_agent' => $request->userAgent(),
            'headers' => [
                'CF-Connecting-IP' => $request->header('CF-Connecting-IP'),
                'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                'X-Real-IP' => $request->header('X-Real-IP'),
                'X-Client-IP' => $request->header('X-Client-IP'),
            ]
        ]);

        return response()->json([
            'protocol' => $isSecure ? 'HTTPS' : 'HTTP',
            'ip_from_request' => $ip,
            'real_client_ip' => $realIp,
            'app_url' => config('app.url'),
            'environment' => config('app.env'),
            'headers' => [
                'CF-Connecting-IP' => $request->header('CF-Connecting-IP'),
                'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                'X-Real-IP' => $request->header('X-Real-IP'),
            ]
        ]);
    }

    private function getRealClientIp(Request $request): string
    {
        // Check common proxy headers
        $headers = [
            'CF-Connecting-IP', // Cloudflare
            'X-Forwarded-For',
            'X-Real-IP',
            'X-Client-IP',
            'HTTP_X_FORWARDED_FOR'
        ];

        foreach ($headers as $header) {
            if ($request->header($header)) {
                $ip = trim(explode(',', $request->header($header))[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }
}