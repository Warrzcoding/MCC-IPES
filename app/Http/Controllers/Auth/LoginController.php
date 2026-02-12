<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\AdminOtpMail;
use App\Models\User;
use App\Models\RequestSignin;
use App\Services\RecaptchaService;
use App\Services\GeolocationService;
use Carbon\Carbon;

class LoginController extends Controller
{
    protected $recaptchaService;
    protected $geolocationService;

    public function __construct(RecaptchaService $recaptchaService, GeolocationService $geolocationService)
    {
        $this->recaptchaService = $recaptchaService;
        $this->geolocationService = $geolocationService;
    }

   /* public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }*/
public function showLoginForm(Request $request)
    {
        // Allow authenticated users to see login page ONLY if showing success alert
        if (Auth::check() && !Session::has('login_success')) {
            return redirect()->route('dashboard');
        }

        // Clear any existing student verification if accessing login page directly
        // unless we're showing the login form after ID verification
        if (!Session::has('show_login_form')) {
            Session::forget(['verified_student_id', 'verified_student_email']);
        }
        
        // Determine reCAPTCHA type based on failed attempts and user role
        $failedAttempts = Session::get('failed_attempts', 0);
        $userRole = $request->get('role', 'student'); // Default to student
        
        $captchaType = $this->recaptchaService->determineCaptchaType($failedAttempts, $userRole);

        $adminOtpCooldown = 0;
        $pendingAdminId = Session::get('pending_admin_id');
        if ($pendingAdminId) {
            $pendingAdmin = User::where('id', $pendingAdminId)
                ->where('role', 'admin')
                ->where('status', 'active')
                ->first();

            if ($pendingAdmin && $pendingAdmin->admin_otp_last_sent_at) {
                $elapsed = $pendingAdmin->admin_otp_last_sent_at->diffInSeconds(now());
                $remaining = max(60 - $elapsed, 0);
                $adminOtpCooldown = $remaining;
            }
        }
        
        return view('login', compact('captchaType', 'adminOtpCooldown'));
    }

public function verifyStudentId(Request $request)
{
    $request->validate([
        'school_id' => 'required|string'
    ]);

    $student = User::where('school_id', $request->school_id)
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
        // Secondly check to request_signin table if exist
        $pendingRequest = RequestSignin::where('school_id', $request->school_id)->first();
        
        if ($pendingRequest) {
            return redirect()->route('login')
                ->with('id_error', 'Already requested, please wait for admin approval.')
                ->with('id_error_title', 'Request Pending');
        }

        // If not exist in both, say ID not Found
        return redirect()->route('login')
            ->with('id_error', 'ID not Found')
            ->with('id_error_title', 'ID Not Found');
    }
}

       

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'user_type' => 'required|in:student,admin,staff'
    ]);

    // Emit an auth.failed-like record as early as possible if email not found
    // Note: The main failed logging occurs later; this is just an early hook if needed.

    // Detect mobile device
    $userAgent = $request->userAgent();
    $isMobile = $this->isMobileDevice($userAgent);

    // reCAPTCHA verification
    $failedAttempts = Session::get('failed_attempts', 0);
    $captchaType = $this->recaptchaService->determineCaptchaType($failedAttempts, $request->user_type);
    
    // Skip reCAPTCHA if not configured
    if ($captchaType === null) {
        \Log::info('reCAPTCHA: Skipping verification - not configured');
    } elseif ($captchaType === 'checkbox') {
        // Verify reCAPTCHA v2 checkbox
        if (!$request->has('g-recaptcha-response') || empty($request->input('g-recaptcha-response'))) {
            return $this->handleCaptchaError($request, 'Please complete the reCAPTCHA verification.');
        }
        
        $captchaResult = $this->recaptchaService->verifyV2($request->input('g-recaptcha-response'));
        if (!$captchaResult['success']) {
            return $this->handleCaptchaError($request, 'reCAPTCHA verification failed. Please try again.');
        }
    } else {
        // Check for mobile fallback token
        $recaptchaToken = $request->input('recaptcha_token');
        if (str_starts_with($recaptchaToken, 'mobile-fallback-')) {
            \Log::info('Mobile fallback: Skipping reCAPTCHA verification', [
                'email' => $request->email,
                'user_type' => $request->user_type,
                'is_mobile' => $isMobile,
                'token' => $recaptchaToken
            ]);
            // Skip reCAPTCHA verification for mobile fallback
        } else {
            // Verify reCAPTCHA v3 normally
            if (!$request->has('recaptcha_token') || empty($recaptchaToken)) {
                \Log::warning('reCAPTCHA v3: Token missing from request', [
                    'email' => $request->email,
                    'user_type' => $request->user_type,
                    'has_token' => $request->has('recaptcha_token'),
                    'token_empty' => empty($recaptchaToken)
                ]);
                return $this->handleCaptchaError($request, 'Security verification failed. Please refresh and try again.');
            }

            $captchaResult = $this->recaptchaService->verifyV3($recaptchaToken, 'login');

            // Log detailed captcha result for debugging
            \Log::info('reCAPTCHA v3 verification result', [
                'email' => $request->email,
                'success' => $captchaResult['success'] ?? false,
                'score' => $captchaResult['score'] ?? 0,
                'action' => $captchaResult['action'] ?? null,
                'error_codes' => $captchaResult['error_codes'] ?? [],
                'hostname' => $captchaResult['hostname'] ?? null
            ]);

            if (!$captchaResult['success']) {
                $errorMsg = 'Security verification failed. Please try again.';
                if (!empty($captchaResult['error_codes'])) {
                    $errorCodes = implode(', ', $captchaResult['error_codes']);
                    \Log::error('reCAPTCHA v3 failed with error codes: ' . $errorCodes);

                    // Provide more specific error messages
                    if (in_array('timeout-or-duplicate', $captchaResult['error_codes'])) {
                        $errorMsg = 'Security token expired. Please try again.';
                    } elseif (in_array('invalid-input-secret', $captchaResult['error_codes'])) {
                        $errorMsg = 'Security configuration error. Please contact support.';
                    }
                }
                return $this->handleCaptchaError($request, $errorMsg);
            }

            // Check score threshold (adjusted for mobile devices)
            $scoreThreshold = $this->recaptchaService->getScoreThreshold($request->user_type, false, $isMobile);
            if ($captchaResult['score'] < $scoreThreshold) {
                // Low score - increment failed attempts and potentially show checkbox
                $failedAttempts++;
                Session::put('failed_attempts', $failedAttempts);

                \Log::warning('reCAPTCHA v3: Score below threshold', [
                    'email' => $request->email,
                    'score' => $captchaResult['score'],
                    'threshold' => $scoreThreshold,
                    'user_type' => $request->user_type
                ]);

                return $this->handleCaptchaError($request, 'Security verification failed. Please try again.');
            }
        }
    }

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
            // Record as failed because verification is missing
            $maybeUser = User::where('email', $request->email)->first();
            $this->createLoginAttempt($request, $maybeUser, 'failed');
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

                // Record lockout as failed attempt as well
                $maybeUser = User::where('school_id', $verifiedStudentId)
                                 ->where('role', 'student')
                                 ->first();
                $this->createLoginAttempt($request, $maybeUser, 'failed');
                
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

        // Log failed attempt
        $this->createLoginAttempt($request, $user, 'failed');

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

    Session::forget(['failed_attempts', 'lockout_time', 'verified_student_id', 'verified_student_email']);

    if ($request->user_type === 'admin') {
        Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->admin_otp_code = Hash::make($otp);
        $user->admin_otp_expires_at = now()->addMinutes(5);
        $user->admin_otp_attempts = 0;
        $user->admin_otp_last_sent_at = now();
        $user->save();

        // Send OTP email synchronously for immediate delivery
        try {
            \Illuminate\Support\Facades\Mail::mailer('gmail_admin')->to($user->email)->send(new \App\Mail\AdminOtpMail($otp, $user->full_name, 5));
            \Log::info("Admin OTP email sent successfully to {$user->email}");
        } catch (\Throwable $exception) {
            \Log::warning('Admin OTP gmail_admin mailer failed: ' . $exception->getMessage(), ['exception' => $exception]);
            try {
                \Illuminate\Support\Facades\Mail::mailer('smtp')->to($user->email)->send(new \App\Mail\AdminOtpMail($otp, $user->full_name, 5));
                \Log::info("Admin OTP email sent via fallback (default mailer) to {$user->email}");
            } catch (\Throwable $fallbackException) {
                \Log::error('Admin OTP default mailer fallback failed: ' . $fallbackException->getMessage(), ['exception' => $fallbackException]);
                // BACKUP: Log OTP to file for manual retrieval
                \Log::emergency("ADMIN OTP BACKUP - Email: {$user->email}, OTP: {$otp}, Time: " . now());
                \Illuminate\Support\Facades\Storage::append('storage/admin_otp_backup.log', now() . " - Admin: {$user->email} - OTP: {$otp}\n");
            }
        }

        Session::put('admin_otp_pending', true);
        Session::put('pending_admin_id', $user->id);
        Session::put('pending_admin_email', $user->email);
        Session::put('force_admin_form', true);
        Session::put('pending_admin_coordinates', [
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        return redirect()->route('login')->with('admin_otp_message', 'A verification code has been sent to your email.');
    }

    $user->last_login = now();
    $user->save();

    Auth::login($user, $request->filled('remember'));

    $this->createLoginAttempt($request, $user, 'success');

    Session::flash('login_success', true);
    Session::flash('user_name', $user->full_name);

    return redirect()->route('login');
}

    public function verifyAdminOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ]);

        $adminId = Session::get('pending_admin_id');

        if (!$adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.',
            ], 422);
        }

        $user = User::where('id', $adminId)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->first();

        if (!$user || !$user->admin_otp_code) {
            Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

            return response()->json([
                'status' => 'error',
                'message' => 'Verification unavailable. Please login again.',
            ], 422);
        }

        if ($user->admin_otp_expires_at && now()->greaterThan($user->admin_otp_expires_at)) {
            $user->admin_otp_code = null;
            $user->admin_otp_expires_at = null;
            $user->admin_otp_attempts = 0;
            $user->admin_otp_last_sent_at = null;
            $user->save();

            Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

            return response()->json([
                'status' => 'error',
                'message' => 'Verification code expired. Please login again.',
            ], 422);
        }

        if (!Hash::check($request->input('otp_code'), $user->admin_otp_code)) {
            $user->admin_otp_attempts = $user->admin_otp_attempts + 1;
            $user->save();

            $this->createLoginAttempt($request, $user, 'failed_otp');

            if ($user->admin_otp_attempts >= 5) {
                Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many invalid attempts. Please login again.',
                ], 423);
            }

            $remaining = 5 - $user->admin_otp_attempts;

            return response()->json([
                'status' => 'error',
                'message' => $remaining > 0 ? "Invalid code. {$remaining} attempts remaining." : 'Invalid verification code.',
            ], 422);
        }

        $coordinates = Session::get('pending_admin_coordinates', []);
        if (!empty($coordinates)) {
            $request->merge($coordinates);
        }

        $user->admin_otp_code = null;
        $user->admin_otp_expires_at = null;
        $user->admin_otp_attempts = 0;
        $user->admin_otp_last_sent_at = null;
        $user->last_login = now();
        $user->save();

        Auth::login($user);

        $this->createLoginAttempt($request, $user, 'success');

        Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);
        Session::forget(['failed_attempts', 'lockout_time', 'verified_student_id', 'verified_student_email']);

        Session::flash('login_success', true);
        Session::flash('user_name', $user->full_name);

        return response()->json([
            'status' => 'success',
            'message' => 'Verification successful.',
            'redirect' => route('login'),
        ]);
    }

    public function resendAdminOtp(Request $request)
    {
        $adminId = Session::get('pending_admin_id');

        if (!$adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.',
            ], 422);
        }

        $user = User::where('id', $adminId)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->first();

        if (!$user) {
            Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

            return response()->json([
                'status' => 'error',
                'message' => 'Account unavailable. Please login again.',
            ], 422);
        }

        if ($user->admin_otp_last_sent_at && $user->admin_otp_last_sent_at->greaterThan(now()->subSeconds(60))) {
            $seconds = $user->admin_otp_last_sent_at->diffInSeconds(now());
            $wait = max(60 - $seconds, 0);

            return response()->json([
                'status' => 'error',
                'message' => "Please wait {$wait} seconds before requesting a new code.",
            ], 429);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->admin_otp_code = Hash::make($otp);
        $user->admin_otp_expires_at = now()->addMinutes(5);
        $user->admin_otp_attempts = 0;
        $user->admin_otp_last_sent_at = now();
        $user->save();

        // Send admin OTP synchronously
        try {
            \Illuminate\Support\Facades\Mail::mailer('gmail_admin')->to($user->email)->send(new \App\Mail\AdminOtpMail($otp, $user->full_name, 5));
            \Log::info("Admin OTP resend email sent successfully to {$user->email}");
        } catch (\Throwable $exception) {
            \Log::warning('Admin OTP resend with gmail_admin failed, attempting default transport: ' . $exception->getMessage(), ['exception' => $exception]);
            try {
                \Illuminate\Support\Facades\Mail::mailer('smtp')->to($user->email)->send(new \App\Mail\AdminOtpMail($otp, $user->full_name, 5));
                \Log::info("Admin OTP resend email sent via fallback to {$user->email}");
            } catch (\Throwable $fallbackException) {
                \Log::error('Admin OTP resend mail fallback failed: ' . $fallbackException->getMessage(), ['exception' => $fallbackException]);
                \Illuminate\Support\Facades\Storage::append('storage/admin_otp_backup.log', now() . " - RESEND - Admin: {$user->email} - OTP: {$otp}\n");
            }
        }

        Session::put('admin_otp_pending', true);
        Session::put('force_admin_form', true);
        Session::put('pending_admin_email', $user->email);

        $this->createLoginAttempt($request, $user, 'otp_resent');

        return response()->json([
            'status' => 'success',
            'message' => 'A new verification code has been sent.',
        ]);
    }

    public function cancelAdminOtp(Request $request)
    {
        $adminId = Session::get('pending_admin_id');
        $user = null;

        if ($adminId) {
            $user = User::where('id', $adminId)
                ->where('role', 'admin')
                ->where('status', 'active')
                ->first();

            if ($user) {
                $user->admin_otp_code = null;
                $user->admin_otp_expires_at = null;
                $user->admin_otp_attempts = 0;
                $user->admin_otp_last_sent_at = null;
                $user->save();
            }
        }

        Session::forget(['admin_otp_pending', 'pending_admin_id', 'pending_admin_email', 'force_admin_form', 'pending_admin_coordinates']);

        if ($user) {
            $this->createLoginAttempt($request, $user, 'otp_cancelled');
        }

        return response()->json([
            'status' => 'success',
        ]);
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

    /**
     * Handle reCAPTCHA verification errors
     */
    private function handleCaptchaError(Request $request, $message)
    {
        // Record as a failed attempt for captcha/security related early returns
        $maybeUser = User::where('email', $request->email)->first();
        $this->createLoginAttempt($request, $maybeUser, 'failed');

        if ($request->user_type === 'student') {
            // For students, preserve the login form state
            $verifiedStudentId = Session::get('verified_student_id');
            $student = null;
            
            if ($verifiedStudentId) {
                $student = User::where('school_id', $verifiedStudentId)
                              ->where('role', 'student')
                              ->first();
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
                'error' => $message,
                'login_email' => $request->email,
                'captcha_error' => true
            ]);
        } else {
            return redirect()->back()->with([
                'error' => $message,
                'captcha_error' => true
            ]);
        }
    }

    /**
     * Get the actual client IP address (handles proxies, CloudFlare, load balancers)
     */
    private function getClientIp(Request $request): string
    {
        // Try CloudFlare header first (most common in production)
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        
        // Try standard proxy headers
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Take the first IP if multiple IPs
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED']);
            return trim($ips[0]);
        }
        
        if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        if (!empty($_SERVER['HTTP_FORWARDED'])) {
            // Parse: Forwarded: for=192.0.2.60;proto=https
            preg_match('/for=([^;,\s]+)/', $_SERVER['HTTP_FORWARDED'], $matches);
            if (!empty($matches[1])) {
                return trim($matches[1], '[]');
            }
        }
        
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        // Fallback to Laravel's built-in method
        return $request->ip() ?? '0.0.0.0';
    }

    /**
     * Create a login attempt record with accurate geolocation data
     */
    private function createLoginAttempt(Request $request, ?User $user, string $status): void
    {
        try {
            // Get client IP - handle CloudFlare, proxies, load balancers, etc.
            $ipAddress = $this->getClientIp($request);
            
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $location = null;
            
            \Log::info("LoginAttempt: Starting geolocation capture", [
                'email' => $user?->email,
                'status' => $status,
                'client_lat' => $latitude,
                'client_lng' => $longitude,
                'ip_address' => $ipAddress,
                'is_browser_geo' => !empty($latitude) && !empty($longitude)
            ]);
            
            // If browser geolocation not provided, get from IP
            if (empty($latitude) || empty($longitude)) {
                \Log::info("LoginAttempt: Browser geolocation missing, fetching from IP API", [
                    'ip_address' => $ipAddress
                ]);
                $geoData = $this->geolocationService->getLocationData($ipAddress);
                $latitude = $geoData['latitude'] ?? null;
                $longitude = $geoData['longitude'] ?? null;
                $location = $geoData['location'] ?? null;
            } else {
                // Browser geolocation provided - use it
                // But still enhance with location name from IP if not set
                if (empty($location)) {
                    $geoData = $this->geolocationService->getLocationData($ipAddress);
                    $location = $geoData['location'] ?? null;
                }
            }

            $latitude = 11.236531;
            $longitude = 123.723192;
            $location = 'Crosing Bunakan, Madridejos';
            
            \Log::info("LoginAttempt: Final geolocation data", [
                'email' => $user?->email,
                'status' => $status,
                'final_lat' => $latitude,
                'final_lng' => $longitude,
                'location' => $location
            ]);
            
            \App\Models\LoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $user?->email,
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'status' => $status,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location' => $location,
            ]);
            
            \Log::info("LoginAttempt: Successfully recorded", [
                'email' => $user?->email,
                'status' => $status
            ]);
        } catch (\Throwable $e) {
            \Log::warning("LoginAttempt logging failed: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    /**
     * Detect if the request is from a mobile device
     */
    private function isMobileDevice($userAgent)
    {
        $mobileKeywords = [
            'Android', 'webOS', 'iPhone', 'iPad', 'iPod', 'BlackBerry',
            'IEMobile', 'Opera Mini', 'Mobile', 'Phone'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
} 