<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
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
                'regex:/^[a-zA-Z0-9._%+-]+@mcclawis\.(edu|edi)\.ph$/i'
            ]
        ], [
            'ms365_email.required' => 'Microsoft 365 email is required.',
            'ms365_email.email' => 'Please enter a valid email address.',
            'ms365_email.regex' => 'Email must end with @mcclawis.edu.ph or @mcclawis.edi.ph'
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

        // Dispatch job to send OTP email asynchronously
        \App\Jobs\SendPreSignupOtpJob::dispatch($otp, $request->ms365_email);

        \Log::info("Pre-signup OTP queued for {$request->ms365_email}: {$otp}");

        return response()->json([
            'status' => 'success',
            'message' => 'Verification code sent to your Microsoft 365 email. Please check your inbox.'
        ]);
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

    // Show the ID check form
    public function showIdCheckForm()
    {
        return view('idcheck');
    }

    // Handle checking the school ID
    public function checkId(Request $request)
    {
        $request->validate([
            'school_id' => 'required|string|regex:/^[0-9]{4}-[0-9]{4}$/'
        ], [
            'school_id.required' => 'School ID is required.',
            'school_id.regex' => 'School ID must be in format: 0000-0000 (e.g., 2024-0001)'
        ]);

        // Check if the school ID exists in the users table
        $user = User::where('school_id', $request->school_id)->first();

        if ($user) {
            // Store the verified school ID in session for later use
            Session::put('checked_school_id', $request->school_id);
            Session::put('checked_user_type', $user->role);

            return redirect()->route('idcheck')->with('success', 'School ID found! You can now proceed with registration.');
        } else {
            return redirect()->route('idcheck')->with('error', 'School ID not found. Please check your ID or contact support.');
        }
    }
}
