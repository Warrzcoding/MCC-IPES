<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

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
            $request->validate([
                'ms365_email' => 'required|email|regex:/^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$/'
            ]);

            $email = $request->ms365_email;

            // TEMPORARY: Skip user validation for testing - always allow OTP sending
            // TODO: Re-enable user validation in production
            /*
            // Check if user exists with student role
            $user = User::where('email', $email)->where('role', 'student')->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No student account found with this Microsoft 365 email address. Please contact your administrator.'
                ]);
            }
            */

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation error: " . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email format. Please use the format: firstname.lastname@mcclawis.edu.ph'
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

        // Send email with OTP
        try {
            Mail::raw("🔐 MCC-IPES Password Reset Verification

Your verification code is: {$otp}

This code will expire in 5 minutes.

If you didn't request this password reset, please ignore this email.

Time: " . now() . "
System: MCC-IPES", function ($message) use ($email) {
                $message->to($email)
                        ->subject('MCC-IPES Password Reset - Verification Code');
            });
            
            \Log::info("Password reset OTP sent to {$email}: {$otp}");
            
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

            // TEMPORARY: Skip user validation for testing - find any user with this email
            $user = User::where('email', $request->ms365_email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'User not found with this email address.'
                ]);
            }

            // Update the password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Clear the session data
            Session::forget(['reset_otp', 'reset_email', 'reset_otp_expires', 'reset_otp_verified']);

            \Log::info("Password reset successful for user: {$request->ms365_email}");

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