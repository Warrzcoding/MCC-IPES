<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\MagicLinkMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MagicLinkController extends Controller
{
    /**
     * Send magic link to user email
     */
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Rate Limiting Logic: 2 requests per day, 30 seconds cooldown
        $dailyKey = 'magic-link-daily:' . $email;
        $cooldownKey = 'magic-link-cooldown:' . $email;

        // Check Cooldown (30 seconds)
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return back()->with('error', "Please wait {$seconds} seconds before requesting another link.");
        }

        // Check Daily Limit (2 requests)
        if (RateLimiter::tooManyAttempts($dailyKey, 2)) {
            return back()->with('error', 'You have reached the daily limit for reset link requests. Please try again tomorrow.');
        }

        $token = Str::random(64);

        // Store hashed token in DB
        DB::table('magic_password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'expires_at' => Carbon::now()->addMinutes(60),
                'created_at' => Carbon::now(),
            ]
        );

        // Generate magic link URL
        $url = route('magic.reset', ['token' => $token, 'email' => $email]);

        // Send email using Student Magic Link mailer
        Mail::mailer('gmail_student')->to($email)->send(new MagicLinkMail($url));

        // Increment rate limits on successful send
        RateLimiter::hit($dailyKey, 86400); // 24 hours
        RateLimiter::hit($cooldownKey, 30); // 30 seconds

        return back()->with('success', 'Reset link sent! Please check your email in outlook.');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;

        // Verify token exists and is valid
        $reset = DB::table('magic_password_resets')
            ->where('email', $email)
            ->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
            return redirect()->route('forgotpass')->with('error', 'Invalid or expired token.');
        }

        if (Carbon::parse($reset->expires_at)->isPast()) {
            return redirect()->route('forgotpass')->with('error', 'Token has expired.');
        }

        return view('magic_reset', ['token' => $token, 'email' => $email]);
    }

    /**
     * Update password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = DB::table('magic_password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token) || Carbon::parse($reset->expires_at)->isPast()) {
            return redirect()->route('forgotpass')->with('error', 'Invalid or expired token.');
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('magic_password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successful! You can now login.');
    }
}
