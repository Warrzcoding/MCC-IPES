<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin login form.
     */
    public function showLoginForm()
    {
        // If already authenticated as super admin, redirect to dashboard or desired page
        if (Auth::check() && Auth::user()->role === 'super-admin') {
            return redirect()->route('dashboard');
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

        $credentials['role'] = 'super-admin';

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('These credentials do not match our records or you do not have super admin access.'),
        ])->onlyInput('email');
    }
}