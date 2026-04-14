<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\Staff;
use App\Models\Question;
use App\Models\AcademicYear;
use App\Models\BackupLog;
use App\Models\IdChecker;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\SuperAdminOtpMail;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin login form.
     */
    public function showLoginForm(Request $request)
    {
        // If already authenticated as super admin, redirect to home page
        if (session()->has('super_admin_id')) {
            return redirect()->route('superadmin.home');
        }

        // Always forget temporary access on a fresh GET request to force access code modal
        // unless we are in the middle of an OTP verification or there are validation errors
        if ($request->isMethod('get') && !session('super_admin_otp_pending') && !session()->has('errors')) {
            session()->forget('temp_access_verified');
        }

        $pendingEmail = null;
        if (session('super_admin_otp_pending') && session('pending_super_admin_id')) {
            $pendingAdmin = SuperAdmin::find(session('pending_super_admin_id'));
            if ($pendingAdmin) {
                $pendingEmail = $pendingAdmin->email;
            }
        }

        return view('s_admin.superlogin', [
            'pendingEmail' => $pendingEmail
        ]);
    }

    /**
     * Handle an incoming super admin authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Find super admin by email
        $superAdmin = SuperAdmin::where('email', $credentials['email'])->first();

        // Check if account is locked
        if ($superAdmin && $superAdmin->isAccountLocked()) {
            $remainingTime = $superAdmin->getRemainingLockTime();
            return back()
                ->withInput($request->only('email'))
                ->with('account_locked', true)
                ->with('locked_time', $remainingTime)
                ->withErrors([
                    'email' => 'This account is temporarily locked due to multiple failed login attempts.',
                ]);
        }

        if ($superAdmin && Hash::check($credentials['password'], $superAdmin->password)) {
            // Successful credentials - Generate and send OTP
            $superAdmin->resetFailedAttempts();

            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $superAdmin->otp_code = Hash::make($otp);
            $superAdmin->otp_expires_at = now()->addMinutes(5);
            $superAdmin->otp_attempts = 0;
            $superAdmin->otp_last_sent_at = now();
            $superAdmin->save();

            // Send OTP email using gmail_admin mailer
            file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Attempting to send Super Admin OTP email to: ' . $superAdmin->email . "\n", FILE_APPEND);
            try {
                Mail::mailer('gmail_admin')->to($superAdmin->email)->send(new SuperAdminOtpMail($otp, $superAdmin->name, 5));
                file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP email sent successfully.' . "\n", FILE_APPEND);
            } catch (\Exception $e) {
                file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP Email failed: ' . $e->getMessage() . "\n", FILE_APPEND);
                try {
                    file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Attempting fallback mailer for Super Admin OTP.' . "\n", FILE_APPEND);
                    Mail::to($superAdmin->email)->send(new SuperAdminOtpMail($otp, $superAdmin->name, 5));
                    file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP fallback email sent.' . "\n", FILE_APPEND);
                } catch (\Exception $e2) {
                    file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP Fallback Email failed: ' . $e2->getMessage() . "\n", FILE_APPEND);
                }
            }

            // Store in session for verification
            Session::put('super_admin_otp_pending', true);
            Session::put('pending_super_admin_id', $superAdmin->id);

            return back()->with('otp_sent', true)->with('email', $superAdmin->email);
        }

        // Failed login attempt
        if ($superAdmin) {
            $superAdmin->incrementFailedAttempts();

            // Check if just locked
            if ($superAdmin->is_locked) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('account_locked', true)
                    ->with('locked_time', 60) // 1 minute as per incrementFailedAttempts
                    ->withErrors([
                        'email' => 'Account locked due to 3 failed login attempts. Please try again in 1 minute.',
                    ]);
            }

            $attemptsLeft = 3 - $superAdmin->failed_login_attempts;
            return back()
                ->withInput($request->only('email'))
                ->with('login_failed', true)
                ->with('attempts_left', $attemptsLeft)
                ->withErrors([
                    'email' => "Invalid credentials. {$attemptsLeft} attempts remaining.",
                ]);
        }

        return back()->withErrors([
            'email' => __('These credentials do not match our records.'),
        ])->onlyInput('email');
    }

    /**
     * Verify OTP for super admin login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ]);

        $superAdminId = Session::get('pending_super_admin_id');

        if (!$superAdminId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
            ], 422);
        }

        $superAdmin = SuperAdmin::find($superAdminId);

        if (!$superAdmin || !$superAdmin->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Verification unavailable. Please login again.',
            ], 422);
        }

        // Check expiry
        if ($superAdmin->otp_expires_at && now()->greaterThan($superAdmin->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired. Please request a new one.',
            ], 422);
        }

        // Check code
        if (!Hash::check($request->otp_code, $superAdmin->otp_code)) {
            $superAdmin->increment('otp_attempts');
            
            if ($superAdmin->otp_attempts >= 5) {
                $this->cancelOtp($request);
                return response()->json([
                    'success' => false,
                    'message' => 'Too many invalid attempts. Please login again.',
                ], 422);
            }

            $remaining = 5 - $superAdmin->otp_attempts;
            return response()->json([
                'success' => false,
                'message' => "Invalid code. {$remaining} attempts remaining.",
            ], 422);
        }

        // Success - Log in the super admin
        $superAdmin->otp_code = null;
        $superAdmin->otp_expires_at = null;
        $superAdmin->otp_attempts = 0;
        $superAdmin->otp_last_sent_at = null;
        $superAdmin->last_login = now();
        $superAdmin->save();

        // Clear OTP session and set super admin session
        Session::forget(['super_admin_otp_pending', 'pending_super_admin_id']);
        Session::put('super_admin_id', $superAdmin->id);
        Session::flash('login_success', true);
        Session::flash('super_admin_login_success', true); // Specific flag for super admin
        $request->session()->regenerate();

        return response()->json([
            'status' => 'success',
            'message' => 'Verification successful.',
            'redirect' => route('superadmin.home')
        ]);
    }

    /**
     * Resend OTP for super admin.
     */
    public function resendOtp(Request $request)
    {
        $superAdminId = Session::get('pending_super_admin_id');

        if (!$superAdminId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
            ], 422);
        }

        $superAdmin = SuperAdmin::find($superAdminId);

        if (!$superAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Account unavailable. Please login again.',
            ], 422);
        }

        // Rate limiting: wait 60 seconds
        if ($superAdmin->otp_last_sent_at && $superAdmin->otp_last_sent_at->greaterThan(now()->subSeconds(60))) {
            $wait = 60 - now()->diffInSeconds($superAdmin->otp_last_sent_at);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$wait} seconds before requesting a new code.",
            ], 429);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $superAdmin->otp_code = Hash::make($otp);
        $superAdmin->otp_expires_at = now()->addMinutes(5);
        $superAdmin->otp_attempts = 0;
        $superAdmin->otp_last_sent_at = now();
        $superAdmin->save();

        file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Resending Super Admin OTP email to: ' . $superAdmin->email . "\n", FILE_APPEND);
        try {
            Mail::mailer('gmail_admin')->to($superAdmin->email)->send(new SuperAdminOtpMail($otp, $superAdmin->name, 5));
            file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP email resent successfully.' . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP Resend failed: ' . $e->getMessage() . "\n", FILE_APPEND);
            try {
                Mail::to($superAdmin->email)->send(new SuperAdminOtpMail($otp, $superAdmin->name, 5));
                file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP resend fallback email sent.' . "\n", FILE_APPEND);
            } catch (\Exception $e2) {
                file_put_contents(storage_path('logs/debug_mail.log'), '['.now().'] Super Admin OTP Resend Fallback Email failed: ' . $e2->getMessage() . "\n", FILE_APPEND);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent to your email.',
        ]);
    }

    /**
     * Cancel OTP process.
     */
    public function cancelOtp(Request $request)
    {
        $superAdminId = Session::get('pending_super_admin_id');

        if ($superAdminId) {
            $superAdmin = SuperAdmin::find($superAdminId);
            if ($superAdmin) {
                $superAdmin->otp_code = null;
                $superAdmin->otp_expires_at = null;
                $superAdmin->otp_attempts = 0;
                $superAdmin->otp_last_sent_at = null;
                $superAdmin->save();
            }
        }

        Session::forget(['super_admin_otp_pending', 'pending_super_admin_id', 'temp_access_verified']);

        return response()->json(['success' => true]);
    }

    /**
     * Display the super admin home/dashboard.
     */
    public function home()
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));

        if (!$superAdmin) {
            session()->forget('super_admin_id');
            return redirect()->route('superadmin.login');
        }

        // Fetch counts for dashboard
        $instructorCount = Staff::where('staff_type', 'teaching')->count();
        $studentCount = User::where('role', 'student')->count();
        $questionCount = Question::count();
        $nonTeachingCount = Staff::where('staff_type', 'non-teaching')->count();
        
        // Fetch current academic year
        $currentAY = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();

        // Departments/Courses to track
        $departments = ['BSIT', 'BSHM', 'BSBA', 'BSED', 'BEED'];
        
        $departmentStats = [];
        foreach ($departments as $dept) {
            $totalStudents = User::where('role', 'student')
                ->where('course', $dept)
                ->count();
                
            $evaluatedStudents = Evaluation::join('users', 'evaluations.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->where('users.course', $dept)
                ->when($currentAY, function($query) use ($currentAY) {
                    return $query->where('evaluations.academic_year_id', $currentAY->id);
                })
                ->distinct('evaluations.user_id')
                ->count('evaluations.user_id');
                
            $departmentStats[] = [
                'name' => $dept,
                'total' => $totalStudents,
                'evaluated' => $evaluatedStudents
            ];
        }

        return view('s_admin.superadminhome', [
            'superAdmin' => $superAdmin,
            'instructorCount' => $instructorCount,
            'studentCount' => $studentCount,
            'questionCount' => $questionCount,
            'nonTeachingCount' => $nonTeachingCount,
            'departmentStats' => $departmentStats
        ]);
    }

    /**
     * Display User Management for Students
     */
    public function userManagement()
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));
        $students = User::where('role', 'student')->get();

        return view('s_admin.user_management', [
            'superAdmin' => $superAdmin,
            'students' => $students
        ]);
    }

    /**
     * Update user password by super admin.
     */
    public function updatePassword(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }

    /**
     * Update student details by super admin.
     */
    public function updateStudent(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:users,id',
            'username' => 'required|string|max:255|unique:users,username,' . $request->student_id,
            'email' => 'required|email|unique:users,email,' . $request->student_id,
            'full_name' => 'required|string|max:255',
            'school_id' => 'required|string|max:255|unique:users,school_id,' . $request->student_id,
            'course' => 'required|string|in:BSIT,BSHM,BSBA,BSED,BEED',
            'year_level' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'section' => 'nullable|string|max:255',
            'student_status' => 'required|string|in:Regular,Irregular',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        $student = User::find($request->student_id);
        
        if (!$student || $student->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // Handle image upload
        $image_path = $student->profile_image;
        $update_image = false;
        
        if ($request->hasFile('image')) {
            $upload_dir = 'uploads/students';
            $upload_path = public_path($upload_dir);
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            
            $file = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            
            if ($file->move($upload_path, $imageName)) {
                // Delete old image if exists
                if ($student->profile_image && file_exists(public_path($upload_dir . '/' . $student->profile_image))) {
                    @unlink(public_path($upload_dir . '/' . $student->profile_image));
                }
                $image_path = $imageName;
                $update_image = true;
            }
        }

        try {
            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'full_name' => $request->full_name,
                'school_id' => $request->school_id,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'section' => $request->section,
                'student_status' => $request->student_status
            ];

            if ($update_image) {
                $updateData['profile_image'] = $image_path;
            }

            $student->update($updateData);

            return response()->json(['success' => true, 'message' => 'Student updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating student: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle logout for super admin.
     */
    public function logout(Request $request)
    {
        session()->forget('super_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')
            ->with('success', 'You have been logged out successfully.')
            ->with('logout_success', true);
    }

    /**
     * Display activity logs for login attempts.
     */
    public function activityLog(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));
        $search = $request->input('search');
        
        // Fetch all login attempts, sorted by time (newest first)
        $query = \App\Models\LoginAttempt::with('user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $loginAttempts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('s_admin.activitylog', [
            'superAdmin' => $superAdmin,
            'loginAttempts' => $loginAttempts,
            'search' => $search
        ]);
    }

    /**
     * Delete an activity log/login attempt.
     */
    public function deleteActivityLog($id)
    {
        if (!session()->has('super_admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $attempt = \App\Models\LoginAttempt::find($id);
            
            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login attempt not found.'
                ], 404);
            }

            $email = $attempt->email;
            $attempt->delete();

            return response()->json([
                'success' => true,
                'message' => "Login attempt for {$email} has been deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting login attempt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a full database backup.
     */
    public function downloadBackup()
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));
        if (!$superAdmin) {
            abort(403);
        }

        $jobName = 'Super Admin Manual Backup - ' . now()->format('Y-m-d H:i:s');
        $initiatedBy = $superAdmin->full_name ?? 'Super Admin';

        // Create backup log entry
        $backupLog = BackupLog::create([
            'job_name' => $jobName,
            'status' => 'running',
            'initiated_by' => $initiatedBy,
            'started_at' => now(),
        ]);

        try {
            // Database connection details
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Generate backup file path
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $backupFile = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';

            // Create database backup using PHP
            $pdo = new \PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8", $dbUser, $dbPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Get all tables
            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

            $sql = "-- Database backup created on " . now()->format('Y-m-d H:i:s') . "\n";
            $sql .= "-- Host: {$dbHost}\n";
            $sql .= "-- Database: {$dbName}\n";
            $sql .= "-- Generated by MCC-IPES System (Super Admin)\n\n";

            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                // Get CREATE TABLE statement
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $sql .= "-- Table structure for table `{$table}`\n";
                $sql .= $createTable['Create Table'] . ";\n\n";

                // Get table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $sql .= "-- Dumping data for table `{$table}`\n";
                    $sql .= "INSERT INTO `{$table}` VALUES ";

                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = $pdo->quote($value);
                            }
                        }
                        $values[] = "(" . implode(", ", $rowValues) . ")";
                    }

                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            // Write to file
            if (file_put_contents($backupFile, $sql) === false) {
                throw new \Exception('Failed to write backup file');
            }

            if (!file_exists($backupFile)) {
                throw new \Exception('Backup file was not created');
            }

            // Get file size
            $sizeBytes = filesize($backupFile);
            $sizeMB = round($sizeBytes / 1048576, 2); // Convert to MB

            // Update log
            $backupLog->update([
                'status' => 'completed',
                'storage_path' => $backupFile,
                'size_mb' => $sizeMB,
                'completed_at' => now(),
                'duration_seconds' => now()->diffInSeconds($backupLog->started_at),
                'notes' => 'Backup completed successfully by Super Admin',
            ]);

            $filename = basename($backupFile);
            return response()->download($backupFile, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            $backupLog->update([
                'status' => 'failed',
                'completed_at' => now(),
                'duration_seconds' => now()->diffInSeconds($backupLog->started_at),
                'notes' => 'Backup failed: ' . $e->getMessage(),
            ]);

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the file manager.
     */
    public function fileManager()
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));

        if (!$superAdmin) {
            session()->forget('super_admin_id');
            return redirect()->route('superadmin.login');
        }

        return view('s_admin.ipes_filemanager', [
            'superAdmin' => $superAdmin
        ]);
    }

    /**
     * Verify access code before showing login form.
     */
    public function verifyAccessCode(Request $request)
    {
        $request->validate([
            'accesscode' => ['required', 'string'],
        ]);

        $superAdmin = SuperAdmin::first();

        if ($superAdmin && Hash::check($request->accesscode, $superAdmin->accesscode)) {
            session(['temp_access_verified' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid access code.'], 401);
    }

    /**
     * Add new ID User to idchecker table.
     */
    public function addIdUser(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_number' => 'required|string|max:255|unique:idchecker,id_number',
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year' => 'required|string|in:1,2,3,4',
            'section' => 'required|string|max:255',
            'gender' => 'required|string|in:Male,Female'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            IdChecker::create([
                'id_number' => $request->id_number,
                'fname' => $request->fname,
                'mname' => $request->mname,
                'lname' => $request->lname,
                'course' => $request->course,
                'year' => $request->year,
                'section' => $request->section,
                'gender' => $request->gender
            ]);

            return response()->json(['success' => true, 'message' => 'ID User added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error adding ID User: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check ID number in idchecker table.
     */
    public function checkIdNumber(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $idNumber = $request->input('id_number');

        if (!$idNumber) {
            return response()->json(['success' => false, 'message' => 'ID Number is required.'], 400);
        }

        $record = IdChecker::where('id_number', $idNumber)->first();

        if ($record) {
            return response()->json([
                'success' => true,
                'data' => $record
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No record found for this ID number.'
        ]);
    }

    /**
     * Display Admin Management
     */
    public function adminManagement()
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $superAdmin = SuperAdmin::find(session('super_admin_id'));
        $admins = User::where('role', 'admin')->get();

        return view('s_admin.admin_management', [
            'superAdmin' => $superAdmin,
            'admins' => $admins
        ]);
    }

    /**
     * Store a new admin.
     */
    public function storeAdmin(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'course' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }

        try {
            $user = new User();
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->course = $request->course;
            $user->is_main_admin = 0;
            $user->password = Hash::make($request->password);
            $user->role = 'admin';
            $user->status = 'active';

            if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $uploadPath = public_path('uploads/staff');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $user->profile_image = $imageName;
            }

            $user->save();

            return redirect()->route('superadmin.admin-management')
                ->with('message', 'Admin added successfully!')
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Error adding admin: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    /**
     * Update an admin.
     */
    public function updateAdmin(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:users,id',
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $request->admin_id,
            'email' => 'required|email|max:255|unique:users,email,' . $request->admin_id,
            'course' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }

        try {
            $user = User::findOrFail($request->admin_id);
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->course = $request->course;
            $user->is_main_admin = 0;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $uploadPath = public_path('uploads/staff');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                if ($user->profile_image && file_exists(public_path('uploads/staff/' . $user->profile_image))) {
                    unlink(public_path('uploads/staff/' . $user->profile_image));
                }
                $user->profile_image = $imageName;
            }

            $user->save();

            return redirect()->route('superadmin.admin-management')
                ->with('message', 'Admin updated successfully!')
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Error updating admin: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    /**
     * Delete an admin.
     */
    public function deleteAdmin(Request $request)
    {
        if (!session()->has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid admin ID.')
                ->with('message_type', 'danger');
        }

        try {
            $user = User::findOrFail($request->admin_id);
            if ($user->profile_image && file_exists(public_path('uploads/staff/' . $user->profile_image))) {
                unlink(public_path('uploads/staff/' . $user->profile_image));
            }
            $user->delete();

            return redirect()->route('superadmin.admin-management')
                ->with('message', 'Admin deleted successfully!')
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting admin: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }
}
