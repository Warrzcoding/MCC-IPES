<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (in_array($user->role, ['admin', 'super-admin'], true)) {
                $fingerprint = hash('sha256', ($request->ip() ?? '0.0.0.0') . '|' . ($request->userAgent() ?? ''));
                $key = 'admin_session_fingerprint';

                if (!$request->session()->has($key)) {
                    $request->session()->put($key, $fingerprint);
                    return $next($request);
                }

                if ($request->session()->get($key) === $fingerprint) {
                    return $next($request);
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    abort(403);
                }

                return redirect()->route('login')->with('message', 'Session validation failed.')->with('message_type', 'danger');
            }
        }

        if ($request->expectsJson()) {
            abort(403);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Unauthorized access.')->with('message_type', 'danger');
    }
}
