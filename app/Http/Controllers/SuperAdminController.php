<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin;
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

        if ($superAdmin && Hash::check($credentials['password'], $superAdmin->password)) {
            // Store in session
            session(['super_admin_id' => $superAdmin->id]);
            $request->session()->regenerate();

            // Update last login timestamp
            $superAdmin->update(['last_login' => now()]);

            return redirect()->route('superadmin.home');
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
     * Handle logout for super admin.
     */
    public function logout(Request $request)
    {
        session()->forget('super_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')->with('success', 'You have been logged out successfully.');
    }
}