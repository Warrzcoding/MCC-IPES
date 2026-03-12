<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\IdChecker;
use App\Models\RequestSignin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

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

        // Email is valid and not registered - bypass OTP, mark as verified immediately
        Session::put('pre_signup_email', $request->ms365_email);
        Session::put('pre_signup_otp_verified', true);

        \Log::info("Pre-signup email verified for {$request->ms365_email} (OTP bypass)");

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully. Proceeding to signup...'
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

    // AJAX endpoint for checking ID from idchecker table
    public function checkIdAjax(Request $request)
    {
        try {
            $request->validate([
                'id_number' => 'required|string|regex:/^[0-9]{4}-[0-9]{4}$/'
            ], [
                'id_number.required' => 'ID number is required.',
                'id_number.regex' => 'ID number must be in format: 0000-0000'
            ]);

            $idRecord = IdChecker::where('id_number', $request->id_number)->first();

            if ($idRecord) {
                // Check if the ID is already registered in users table
                $existingUser = User::where('school_id', $request->id_number)->first();
                if ($existingUser) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'This ID number is already registered. Please login or contact support.'
                    ], 422);
                }

                // Construct fullname: fname mname_initial. lname
                $mInitial = $idRecord->mname ? strtoupper(substr($idRecord->mname, 0, 1)) . '.' : '';
                $fullName = trim($idRecord->fname . ($mInitial ? ' ' . $mInitial : '') . ' ' . $idRecord->lname);
                
                return response()->json([
                    'status' => 'found',
                    'data' => [
                        'id_number' => $idRecord->id_number,
                        'firstname' => $idRecord->fname,
                        'middlename' => $idRecord->mname,
                        'lastname' => $idRecord->lname,
                        'fullname' => $fullName,
                        'course' => $idRecord->course,
                        'year' => (string)$idRecord->year,
                        'section' => $idRecord->section,
                        'gender' => $idRecord->gender
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'not_found'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking ID.'
            ], 500);
        }
    }

    // Send OTP for ID check verification
    public function sendIdCheckOtp(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'ms365_email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@mcclawis\.(edu|edi)\.ph$/i'],
                'id_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ]);
            }

            $email = $request->ms365_email;
            $schoolId = $request->id_number;

            // 1. Check User table (Existing/Registered users)
            if (User::where('email', $email)->exists() || User::where('school_id', $schoolId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This ID Number or Email is already used/registered.'
                ]);
            }

            // 2. Check RequestSignin table (Pending approval)
            if (RequestSignin::where('email', $email)->exists() || RequestSignin::where('school_id', $schoolId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This ID Number or Email has already requested sign-in. Please wait for admin approval.'
                ]);
            }

            // Generate OTP (6 digits)
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP in session with expiration (5 minutes)
            Session::put('idcheck_otp', $otp);
            Session::put('idcheck_email', $email);
            Session::put('idcheck_otp_expires', now()->addMinutes(5));

            // Send email with OTP using gmail_student mailer
            try {
                Mail::mailer('gmail_student')->to($email)->send(new \App\Mail\RegistrationOtpMail($otp, $email, 5));
                \Log::info("ID Check OTP sent to {$email} for ID {$schoolId}");
            } catch (\Exception $mailEx) {
                \Log::error("Mail sending failed: " . $mailEx->getMessage());
                // Fallback to default mailer if gmail_student fails
                Mail::to($email)->send(new \App\Mail\RegistrationOtpMail($otp, $email, 5));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Verification code sent to your Microsoft 365 email.'
            ]);

        } catch (\Exception $e) {
            \Log::error("ID Check OTP Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send Verification Code. ' . $e->getMessage()
            ]);
        }
    }

    // Verify OTP for ID check
    public function verifyIdCheckOtp(Request $request)
    {
        try {
            $request->validate([
                'ms365_email' => 'required|email',
                'otp_code' => 'required|string|size:6'
            ]);

            $email = $request->ms365_email;
            $otp = $request->otp_code;

            // Check if OTP exists and is not expired
            $storedOtp = Session::get('idcheck_otp');
            $storedEmail = Session::get('idcheck_email');
            $otpExpires = Session::get('idcheck_otp_expires');

            if (!$storedOtp || $email !== $storedEmail || now()->isAfter($otpExpires)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired verification code.'
                ]);
            }

            if ($otp !== $storedOtp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Incorrect verification code.'
                ]);
            }

            // OTP is valid
            Session::put('idcheck_otp_verified', true);
            
            \Log::info("ID check OTP verified for email: {$email}");

            return response()->json([
                'status' => 'success',
                'message' => 'Verification code verified successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error("ID check OTP verification error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Verification failed. Please try again.'
            ]);
        }
    }

    // Store the verified ID information in session
    public function storeVerifiedId(Request $request)
    {
        try {
            // Check if OTP was verified
            if (!Session::get('idcheck_otp_verified')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email verification required.'
                ], 403);
            }

            \Log::info('ID Check Verification Attempt', [
                'ip' => $request->ip(),
                'data' => $request->all()
            ]);

            // Optional reCAPTCHA verification if token is provided
            if ($request->has('recaptcha_token')) {
                $recaptchaService = app(\App\Services\RecaptchaService::class);
                $verification = $recaptchaService->verifyV3($request->recaptcha_token, 'idcheck_verify');
                
                \Log::info('reCAPTCHA Verification Result', $verification);

                if (!$verification['success'] || $verification['score'] < 0.3) {
                    \Log::warning('Security verification failed for ID check', [
                        'score' => $verification['score'] ?? 'N/A',
                        'errors' => $verification['error_codes'] ?? []
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Security verification failed. Please try again.'
                    ], 403);
                }
            }

            $data = $request->validate([
                'id_number' => 'required|string',
                'fullname' => 'required|string',
                'firstname' => 'nullable|string',
                'middlename' => 'nullable|string',
                'lastname' => 'nullable|string',
                'course' => 'nullable|string',
                'year' => 'nullable',
                'section' => 'nullable|string',
                'gender' => 'nullable|string',
                'ms365_email' => 'required|email'
            ]);

            Session::put('verified_id_info', $data);
            Session::put('pre_signup_email', $data['ms365_email']);
            Session::put('pre_signup_otp_verified', true);
            
            \Log::info('ID Verification Success', ['id_number' => $data['id_number']]);

            return response()->json([
                'status' => 'success',
                'message' => 'ID information stored successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('ID Verification Validation Failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('ID Verification System Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store ID information.'
            ], 500);
        }
    }
}
