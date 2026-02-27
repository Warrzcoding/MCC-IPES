<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RequestSignin;

class PasswordResetController extends Controller
{
    // Show the password reset form
    public function showResetForm()
    {
        return view('reset_password');
    }

    // Handle sending the verification code to the Microsoft 365 email
    public function sendVerification(Request $request)
    {
        try {
            \Log::info("Password reset attempt for email: " . $request->ms365_email);

            $request->validate([
                'ms365_email' => 'required|email'
            ]);

            $email = $request->ms365_email;
            \Log::info("Email passed validation: " . $email);

            // Rate Limiting Logic: 2 requests per day, 30 seconds cooldown
            $dailyKey = 'password-reset-daily:' . $email;
            $cooldownKey = 'password-reset-cooldown:' . $email;

            // Check Cooldown (30 seconds)
            if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
                $seconds = RateLimiter::availableIn($cooldownKey);
                return response()->json([
                    'status' => 'error',
                    'title' => 'Too Many Requests',
                    'message' => "Please wait {$seconds} seconds before requesting another code."
                ]);
            }

            // Check Daily Limit (2 requests)
            if (RateLimiter::tooManyAttempts($dailyKey, 2)) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Daily Limit Reached',
                    'message' => 'You have reached the daily limit for password reset requests. Please try again tomorrow.'
                ]);
            }

            // Check if user exists in User table (approved users) or RequestSignin table (pending approval)
            $user = User::where('email', $email)->first();
            $pendingUser = RequestSignin::where('email', $email)->first();

            \Log::info("User found in users table: " . ($user ? 'YES' : 'NO'));
            \Log::info("User found in request_signin table: " . ($pendingUser ? 'YES' : 'NO'));

            if (!$user && !$pendingUser) {
                \Log::info("No user found for email: " . $email);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No account found with this Microsoft 365 email address. Please complete registration first.'
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation error for email '" . $request->ms365_email . "': " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Unexpected error in sendVerification: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.'
            ]);
        }

        // Generate OTP (6 digits)
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in session with expiration (5 minutes)
        Session::put('reset_otp', $otp);
        Session::put('reset_email', $email);
        Session::put('reset_otp_expires', now()->addMinutes(5));

        // Send email with OTP (HTML template)
        try {
            \Illuminate\Support\Facades\Mail::mailer('gmail_student')->to($email)
                ->send(new \App\Mail\OtpVerificationMail($otp, $email, 5));

            \Log::info("Password reset OTP sent to {$email}: {$otp}");

            // Increment rate limits on successful send
            RateLimiter::hit($dailyKey, 86400); // 24 hours
            RateLimiter::hit($cooldownKey, 30); // 30 seconds

            return response()->json([
                'status' => 'success',
                'message' => 'Verification code sent to your Microsoft 365 email. Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error sending OTP email: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification code. Please try again.'
            ]);
        }
    }

    // Handle verifying the OTP
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'ms365_email' => 'required|email',
                'otp_code' => 'required|string|size:6'
            ]);

            $email = $request->ms365_email;
            $otp = $request->otp_code;

            // Check if OTP exists and is not expired
            $storedOtp = Session::get('reset_otp');
            $storedEmail = Session::get('reset_email');
            $otpExpires = Session::get('reset_otp_expires');

            if (!$storedOtp || $email !== $storedEmail || now()->isAfter($otpExpires)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired verification code. Please request a new code.'
                ]);
            }

            if ($otp !== $storedOtp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid verification code. Please check and try again.'
                ]);
            }

            // OTP is valid, mark as verified
            Session::put('reset_otp_verified', true);
            
            \Log::info("OTP verification successful for email: {$email}");

            return response()->json([
                'status' => 'success',
                'message' => 'Verification code verified successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error("OTP verification error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during verification. Please try again.'
            ]);
        }
    }

    // Handle password reset
    public function update(Request $request)
    {
        try {
            $request->validate([
                'ms365_email' => 'required|email',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password',
            ]);

            // Check if OTP was verified (for security)
            if (!Session::get('reset_otp_verified')) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'OTP verification required before password reset.'
                ]);
            }

            // Check if user exists in User table (approved users) or RequestSignin table (pending approval)
            $user = User::where('email', $request->ms365_email)->first();
            $pendingUser = \App\Models\RequestSignin::where('email', $request->ms365_email)->first();

            if (!$user && !$pendingUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No account found with this email address. Please complete registration first.'
                ]);
            }

            // Use the user record if it exists, otherwise use pending user data for password reset
            $targetUser = $user ?: $pendingUser;

            // Update the password on the appropriate table
            $targetUser->password = Hash::make($request->new_password);
            $targetUser->save();

            // Clear the session data
            Session::forget(['reset_otp', 'reset_email', 'reset_otp_expires', 'reset_otp_verified']);

            \Log::info("Password reset successful for " . ($user ? "approved user" : "pending user") . ": {$request->ms365_email}");

            return response()->json([
                'status' => 'success', 
                'message' => 'Password reset successful.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ]);
        } catch (\Exception $e) {
            \Log::error("Password reset error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while resetting the password.'
            ]);
        }
    }
} 