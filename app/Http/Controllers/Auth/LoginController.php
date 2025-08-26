<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
   /* public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }*/
public function showLoginForm()
    {
        // Clear any existing student verification if accessing login page directly
        // unless we're showing the login form after ID verification
        if (!Session::has('show_login_form')) {
            Session::forget(['verified_student_id', 'verified_student_email']);
        }
        
        return view('login');
    }

public function verifyStudentId(Request $request)
{
    $request->validate([
        'school_id' => 'required|string'
    ]);

    $student = \App\Models\User::where('school_id', $request->school_id)
        ->where('role', 'student')
        ->first();

    if ($student) {
        // Store the verified student ID in session to enforce login restriction
        Session::put('verified_student_id', $student->school_id);
        Session::put('verified_student_email', $student->email);
        
        // Pass student data and a flag to show the login form
        return redirect()->route('login')
            ->with([
                'show_login_form' => true,
                'student_data' => [
                    'full_name' => $student->full_name,
                    'school_id' => $student->school_id,
                    'username' => $student->username,
                ],
                'id_verified' => true
            ]);
    } else {
        // Pass error to show SweetAlert
        return redirect()->route('login')
            ->with('id_error', 'School ID not found. Please check your ID or sign up.');
    }
}

       

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'user_type' => 'required|in:student,admin,staff'
    ]);

    // Lockout logic
    $failedAttempts = Session::get('failed_attempts', 0);
    $lockoutTime = Session::get('lockout_time');
    $lockoutDuration = 30; // 30 snds

     if ($lockoutTime) {
        if (now()->lt($lockoutTime)) {
            $remainingSeconds = now()->diffInSeconds($lockoutTime);
            
            if ($request->user_type === 'student') {
                // For students, preserve the login form state during lockout
                $student = User::where('email', $request->email)
                               ->where('role', 'student')
                               ->first();
                return redirect()->route('login')->with([
                    'show_login_form' => true,
                    'student_data' => $student ? [
                        'full_name' => $student->full_name,
                        'school_id' => $student->school_id,
                        'username' => $student->username,
                    ] : [
                        'full_name' => 'Student',
                        'school_id' => 'Unknown',
                        'username' => 'Unknown',
                    ],
                    'error' => "Account is locked. Please wait {$remainingSeconds} seconds before trying again.",
                    'lockout_timer' => $remainingSeconds,
                    'login_email' => $request->email
                ]);
            } else {
                return redirect()->back()->with('error', "Account is locked. Please wait {$remainingSeconds} seconds before trying again.");
            }
        } else {
            // Lockout expired, reset attempts and lockout time
            Session::forget(['failed_attempts', 'lockout_time']);
            $failedAttempts = 0;
        }
    }
    // For student login, check if the email matches the verified student ID
    if ($request->user_type === 'student') {
        $verifiedStudentId = Session::get('verified_student_id');
        $verifiedStudentEmail = Session::get('verified_student_email');
        
        if (!$verifiedStudentId || !$verifiedStudentEmail) {
            return redirect()->route('login')->with('error', 'Please verify your student ID first.');
        }
        
        // Check if the entered email matches the verified student's email
        if ($request->email !== $verifiedStudentEmail) {
            $failedAttempts++;
            Session::put('failed_attempts', $failedAttempts);
            
            if ($failedAttempts >= 3) {
                $lockoutUntil = now()->addSeconds($lockoutDuration);
                Session::put('lockout_time', $lockoutUntil);
                Session::put('permanent_lockout', true);
                
                $student = User::where('school_id', $verifiedStudentId)
                               ->where('role', 'student')
                               ->first();
                return redirect()->route('login')->with([
                    'show_login_form' => true,
                    'student_data' => $student ? [
                        'full_name' => $student->full_name,
                        'school_id' => $student->school_id,
                        'username' => $student->username,
                    ] : [
                        'full_name' => 'Student',
                        'school_id' => 'Unknown',
                        'username' => 'Unknown',
                    ],
                    'error' => "Account locked for {$lockoutDuration} seconds due to multiple failed login attempts.",
                    'lockout_timer' => $lockoutUntil->timestamp - now()->timestamp,
                    'login_email' => $request->email
                ]);
            }
            
            $remaining = 3 - $failedAttempts;
            $student = User::where('school_id', $verifiedStudentId)
                           ->where('role', 'student')
                           ->first();
            
            return redirect()->route('login')->with([
                'show_login_form' => true,
                'student_data' => $student ? [
                    'full_name' => $student->full_name,
                    'school_id' => $student->school_id,
                    'username' => $student->username,
                ] : [
                    'full_name' => 'Student',
                    'school_id' => 'Unknown',
                    'username' => 'Unknown',
                ],
                'error' => "Email/password does not match the verified student ID. {$remaining} attempts remaining.",
                'focus_field' => 'email',
                'login_email' => $request->email
            ]);
        }
    }

    $user = User::where('email', $request->email)
                ->where('role', $request->user_type)
                ->where('status', 'active')
                ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        $failedAttempts++;
        Session::put('failed_attempts', $failedAttempts);

        if ($failedAttempts >= 3) {
            $lockoutUntil = now()->addSeconds($lockoutDuration);
            Session::put('lockout_time', $lockoutUntil);
            Session::put('permanent_lockout', true);
            
            if ($request->user_type === 'student') {
                // For students, preserve the login form state during lockout
                $student = User::where('email', $request->email)
                               ->where('role', 'student')
                               ->first();
                return redirect()->route('login')->with([
                    'show_login_form' => true,
                    'student_data' => $student ? [
                        'full_name' => $student->full_name,
                        'school_id' => $student->school_id,
                        'username' => $student->username,
                    ] : [
                        'full_name' => 'Student',
                        'school_id' => 'Unknown',
                        'username' => 'Unknown',
                    ],
                    'error' => "Account locked for {$lockoutDuration} seconds due to multiple failed login attempts.",
                    'lockout_timer' => $lockoutUntil->timestamp - now()->timestamp,
                    'login_email' => $request->email
                ]);
            } else {
                return redirect()->back()->with([
                'error' => "Account locked for {$lockoutDuration} seconds due to multiple failed login attempts.",
                'lockout_timer' => $lockoutUntil->timestamp - now()->timestamp
                ]);
            }
        }

        // If permanently locked out, prevent login until timer expires
        if (Session::get('permanent_lockout', false)) {
            $lockoutTime = Session::get('lockout_time');
            if ($lockoutTime && now()->lt($lockoutTime)) {
            $remainingSeconds = now()->diffInSeconds($lockoutTime);
            
            if ($request->user_type === 'student') {
                // For students, preserve the login form state during lockout
                $student = User::where('email', $request->email)
                               ->where('role', 'student')
                               ->first();
                return redirect()->route('login')->with([
                    'show_login_form' => true,
                    'student_data' => $student ? [
                        'full_name' => $student->full_name,
                        'school_id' => $student->school_id,
                        'username' => $student->username,
                    ] : [
                        'full_name' => 'Student',
                        'school_id' => 'Unknown',
                        'username' => 'Unknown',
                    ],
                    'error' => "Account is locked. Please wait {$remainingSeconds} seconds before trying again.",
                    'lockout_timer' => $remainingSeconds,
                    'login_email' => $request->email
                ]);
            } else {
                return redirect()->back()->with([
                    'error' => "Account is locked. Please wait {$remainingSeconds} seconds before trying again.",
                    'lockout_timer' => $remainingSeconds
                ]);
            }
            } else {
            // Lockout expired, allow login again
            Session::forget(['failed_attempts', 'lockout_time', 'permanent_lockout']);
            }
        }

        $remaining = 3 - $failedAttempts;
        if ($request->user_type === 'student') {
            // Keep student login form visible and pre-filled
            $student = User::where('email', $request->email)
                           ->where('role', 'student')
                           ->first();
            
            // Determine which field to focus on based on the error
            $focusField = 'password'; // Default to password
            $errorMessage = "Invalid email or password. {$remaining} attempts remaining.";
            
            if (!$student) {
                // Email not found, focus on email field
                $focusField = 'email';
                $errorMessage = "Email not found. Please check your email address. {$remaining} attempts remaining.";
            }
            
            return redirect()->route('login')->with([
                'show_login_form' => true,
                'student_data' => $student ? [
                    'full_name' => $student->full_name,
                    'school_id' => $student->school_id,
                    'username' => $student->username,
                ] : [
                    'full_name' => 'Student',
                    'school_id' => 'Unknown',
                    'username' => 'Unknown',
                ],
                'error' => $errorMessage,
                'focus_field' => $focusField,
                'login_email' => $request->email // Preserve the entered email
            ]);
        } else {
            return redirect()->back()->with('error', "Invalid email or password. {$remaining} attempts remaining.");
        }
    }

    // Success: clear lockout and verified student session data
    Session::forget(['failed_attempts', 'lockout_time', 'verified_student_id', 'verified_student_email']);

    // Update last login time
    $user->last_login = now();
    $user->save();

    Auth::login($user, $request->filled('remember'));

    // Set success message and redirect flag
    $welcomeMessage = 'Welcome back, ' . $user->full_name . '!';
    
    // Redirect based on role with success message
    if ($user->role === 'admin') {
        return redirect()->route('login', ['login_success' => 1]);
    } elseif ($user->role === 'student') {
        return redirect()->route('login', ['login_success' => 1]);
    } elseif ($user->role === 'staff') {
        return redirect()->route('login', ['login_success' => 1]);
    } else {
        return redirect()->route('login', ['login_success' => 1]);
    }
}

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }

    public function clearStudentVerification()
    {
        Session::forget(['verified_student_id', 'verified_student_email', 'show_login_form', 'student_data']);
        return redirect()->route('login');
    }
} 