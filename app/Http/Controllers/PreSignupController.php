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
        // Store the email in session for later use
        Session::put('pre_signup_email', $request->ms365_email);
        
        // TODO: Implement actual OTP generation and email sending logic here
        // For now, return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Verification code sent to your Microsoft 365 email.'
        ]);
    }

    // Handle verifying the OTP
    public function verifyOtp(Request $request)
    {
        // To be implemented: check OTP
        return response()->json(['status' => 'pending', 'message' => 'Verify OTP logic here.']);
    }
}
