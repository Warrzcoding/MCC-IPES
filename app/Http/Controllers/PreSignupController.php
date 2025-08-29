<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PreSignupController extends Controller
{
    // Show the pre-signup form (not used, but for completeness)
    public function showForm()
    {
        return view('pre_signup');
    }

    // Handle sending the verification code to the Microsoft 365 email
    public function sendVerification(Request $request)
    {
        // Validate the email format
        $validator = Validator::make($request->all(), [
            'ms365_email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$/'
            ]
        ], [
            'ms365_email.required' => 'Microsoft 365 email is required.',
            'ms365_email.email' => 'Please enter a valid email address.',
            'ms365_email.regex' => 'Email must be in the format firstname.lastname@mcclawis.edu.ph'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first('ms365_email')
            ], 422);
        }

        // Check if the email already exists in the users table
        $existingUser = User::where('email', $request->ms365_email)->first();
        
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'This Microsoft 365 email is already registered. Please use a different email or try logging in.'
            ], 422);
        }

        // If email doesn't exist, proceed with sending verification
        // Generate OTP (6 digits)
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP and email in session with expiration (5 minutes)
        Session::put('pre_signup_otp', $otp);
        Session::put('pre_signup_email', $request->ms365_email);
        Session::put('pre_signup_otp_expires', now()->addMinutes(5));

        // Send email with OTP
        try {
            \Illuminate\Support\Facades\Mail::raw("🎓 Welcome to MCC-IPES!

Your verification code is: {$otp}

This code will expire in 5 minutes.

Please enter this code to complete your registration.

If you didn't request this registration, please ignore this email.

Time: " . now() . "
System: MCC-IPES", function ($message) use ($request) {
                $message->to($request->ms365_email)
                        ->subject('MCC-IPES Registration - Verification Code');
            });
            
            \Log::info("Pre-signup OTP sent to {$request->ms365_email}: {$otp}");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Verification code sent to your Microsoft 365 email. Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error sending pre-signup OTP email: " . $e->getMessage());
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
            $storedOtp = Session::get('pre_signup_otp');
            $storedEmail = Session::get('pre_signup_email');
            $otpExpires = Session::get('pre_signup_otp_expires');

            // Log verification attempt
            \Log::info("Pre-signup OTP verification attempt for email: {$email}");

            if (!$storedOtp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No verification code found. Please request a new code.'
                ]);
            }

            if ($email !== $storedEmail) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email mismatch. Please request a new code.'
                ]);
            }

            if ($otpExpires && now()->isAfter($otpExpires)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Verification code has expired. Please request a new code.'
                ]);
            }

            if ($otp !== $storedOtp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid verification code. Please check and try again.'
                ]);
            }

            // OTP is valid, mark as verified
            Session::put('pre_signup_otp_verified', true);
            
            \Log::info("Pre-signup OTP verification successful for email: {$email}");

            return response()->json([
                'status' => 'success',
                'message' => 'Verification code verified successfully. You can now complete your registration.'
            ]);

        } catch (\Exception $e) {
            \Log::error("Pre-signup OTP verification error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during verification. Please try again.'
            ]);
        }
    }
}
