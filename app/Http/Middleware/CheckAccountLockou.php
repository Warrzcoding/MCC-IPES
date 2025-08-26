<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CheckAccountLockout
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('lockout_time')) {
            $lockoutTime = Carbon::parse(Session::get('lockout_time'));
            $currentTime = Carbon::now();
            
            if ($currentTime->lt($lockoutTime)) {
                $remainingSeconds = $currentTime->diffInSeconds($lockoutTime);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Account is locked. Please wait {$remainingSeconds} seconds before trying again.",
                        'remaining_seconds' => $remainingSeconds
                    ], 429);
                }
                
                return redirect()->route('login')
                    ->with('error', "Account is locked. Please wait {$remainingSeconds} seconds before trying again.");
            }
            
            // Lockout period has expired
            Session::forget(['lockout_time', 'failed_attempts']);
        }
        
        return $next($request);
    }
} 