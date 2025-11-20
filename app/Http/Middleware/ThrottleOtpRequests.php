<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ThrottleOtpRequests
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 15): Response
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            Log::warning("OTP rate limit exceeded for IP: " . $request->ip() . ", Key: " . $key);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many OTP requests. Please try again later.',
                    'retry_after' => $this->limiter->availableIn($key)
                ], 429);
            }

            return redirect()->back()->with('error', 'Too many OTP requests. Please try again in ' . $this->limiter->availableIn($key) . ' seconds.');
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response;
    }

    protected function resolveRequestSignature(Request $request): string
    {
        // Use IP + route + email (if provided) for rate limiting
        $signature = $request->ip() . '|' . $request->route()->getName();

        if ($request->has('email') || $request->has('ms365_email')) {
            $email = $request->input('email', $request->input('ms365_email'));
            $signature .= '|' . md5($email); // Hash email for privacy
        }

        return $signature;
    }
}