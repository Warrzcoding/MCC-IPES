<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\Staff;
use App\Models\Question;
use App\Models\AcademicYear;
use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin login form.
     */
    public function showLoginForm()
    {
        // If already authenticated as super admin, redirect to home page
        if (session()->has('super_admin_id')) {
            return redirect()->route('superadmin.home');
        }

        return view('s_admin.superlogin');
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
            // Successful login - reset failed attempts
            $superAdmin->resetFailedAttempts();

            // Store in session
            session(['super_admin_id' => $superAdmin->id]);
            $request->session()->regenerate();

            // Update last login timestamp
            $superAdmin->update(['last_login' => now()]);

            return redirect()->route('superadmin.home')->with('login_success', true);
        }

        // Failed login attempt
        if ($superAdmin) {
            $superAdmin->incrementFailedAttempts();

            // Check if just locked
            if ($superAdmin->is_locked) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('account_locked', true)
                    ->with('locked_time', 900)
                    ->withErrors([
                        'email' => 'Account locked due to 3 failed login attempts. Please try again in 15 minutes.',
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

        return view('s_admin.superadminhome', [
            'superAdmin' => $superAdmin,
            'instructorCount' => $instructorCount,
            'studentCount' => $studentCount,
            'questionCount' => $questionCount,
            'nonTeachingCount' => $nonTeachingCount
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
}