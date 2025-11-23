<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Update last active time for authenticated users
        if (Auth::check()) {
            try {
                $user = Auth::user();
                $user->updateLastActive();
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to update last active time', [
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $response;
    }
} 