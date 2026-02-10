<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
use App\Models\User;
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

        return view('s_admin.superadminhome', ['superAdmin' => $superAdmin]);
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