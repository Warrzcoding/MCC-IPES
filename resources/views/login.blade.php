@php
    use Illuminate\Support\Facades\Session;

    $error = session('error', '');
    $success = session('success', '');
    $show_student_form = session('show_student_form', false);
    $show_login_form = session('show_login_form', false);
    $student_data = session('student_data', null);
    $account_locked = session('account_locked', false);
    $failed_attempts = session('failed_attempts', 0);
    $lockout_time = session('lockout_time', 0);
    $lockout_duration = session('lockout_duration', 0);
    $login_success = session('login_success', false);
    $redirect_url = session('redirect_url', '');
    $force_admin_form = session('force_admin_form', false);
    $admin_otp_pending = session('admin_otp_pending', false);
    $pending_admin_email = session('pending_admin_email', '');
    $admin_otp_message = session('admin_otp_message', '');
    $adminOtpOverlayEnabled = $admin_otp_pending && !empty($admin_otp_message);
    $adminOtpCooldown = session('admin_otp_cooldown', 0);

    $remaining_lockout_seconds = 0;
    if ($account_locked && $lockout_time) {
        $remaining_lockout_seconds = ($lockout_time + $lockout_duration) - time();
        if ($remaining_lockout_seconds <= 0) {
            $remaining_lockout_seconds = 0;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Instructors Performance Evaluation System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- reCAPTCHA v3 Scripts -->
    @if(config('services.recaptcha.site_key_v3'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key_v3') }}" async defer></script>
    @endif
    <style>
        body {
            background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex; 
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            perspective: 1000px;
            position: relative;
            overflow: hidden;
        }

        .otp-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100000;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .otp-overlay.active {
            display: flex;
        }
        .otp-modal {
            width: min(calc(100vw - 40px), 340px);
            padding: 22px 20px;
            display: grid;
            gap: 16px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            text-align: center;
            margin: auto;
        }
        .otp-modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4c6ef5, #5f3dc4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2px;
            color: #ffffff;
            font-size: 1.35rem;
            box-shadow: 0 8px 18px rgba(76, 110, 245, 0.32);
        }
        .otp-modal-title {
            margin: 0;
            font-size: 1.14rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.2px;
            color: #0f172a;
        }
        .otp-modal-subtitle {
            margin: 0;
            font-size: 0.85rem;
            text-align: center;
            color: #475569;
            line-height: 1.45;
        }
        .otp-email {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(79, 70, 229, 0.1);
            color: #4338ca;
        }
        .otp-email::before {
            content: "@";
            font-size: 0.78rem;
            color: rgba(79, 70, 229, 0.6);
        }
        .otp-input-group {
            display: grid;
            grid-template-columns: repeat(6, minmax(38px, 1fr));
            gap: 8px;
        }
        .otp-input-group input {
            height: 44px;
            border-radius: 10px;
            border: 1px solid #cbd5f5;
            font-size: 1.15rem;
            font-weight: 600;
            text-align: center;
            color: #1e293b;
            background: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .otp-input-group input:focus {
            outline: none;
            border-color: #4c6ef5;
            box-shadow: 0 0 0 3px rgba(76, 110, 245, 0.2);
            background: #ffffff;
        }
        .otp-input-group input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
        }
        .otp-error {
            display: none;
            text-align: center;
            font-size: 0.8rem;
            color: #b91c1c;
            background: #fee2e2;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .otp-error.show {
            display: block;
        }
        .otp-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .otp-overlay .btn {
            width: 100%;
        }
        .otp-utility-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            width: 100%;
        }
        .otp-utility-actions button {
            background: none;
            border: none;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 2px;
            text-decoration: none;
            transition: color 0.2s ease;
            position: relative;
            cursor: pointer;
        }
        .otp-utility-actions button::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4c6ef5;
            transition: width 0.3s ease;
            border-radius: 1px;
        }
        .otp-utility-actions button:hover,
        .otp-utility-actions button:focus {
            color: #4c6ef5;
            outline: none;
        }
        .otp-utility-actions button:hover::after,
        .otp-utility-actions button:focus::after {
            width: 100%;
        }
        .otp-utility-actions button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .otp-primary-btn {
            background: #4c6ef5 !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            box-shadow: none;
            transition: transform 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .otp-primary-btn:hover {
            transform: translateY(-1px);
        }
        .otp-primary-btn:disabled {
            opacity: 0.75;
        }
        .otp-secondary-btn {
            border: 1px solid #cbd5f5 !important;
            color: #4c6ef5 !important;
            background: transparent !important;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 600;
            transition: border-color 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .otp-secondary-btn:hover {
            border-color: #4c6ef5 !important;
        }
        .otp-status {
            text-align: center;
            font-size: 0.78rem;
            color: #64748b;
            min-height: 16px;
        }
        .otp-cancel-link {
            background: none;
            border: none;
            color: #334155;
            font-weight: 600;
            font-size: 0.78rem;
            text-decoration: underline;
            margin: 0 auto;
            transition: color 0.2s ease;
        }
        .otp-cancel-link:hover {
            color: #1e293b;
        }
        .otp-cancel-link:disabled {
            opacity: 0.6;
        }
        @media (max-width: 480px) {
            .otp-modal {
                padding: 22px 18px;
                gap: 12px;
            }
            .otp-input-group {
                grid-template-columns: repeat(6, minmax(38px, 1fr));
                gap: 6px;
            }
            .otp-input-group input {
                height: 44px;
                font-size: 1.1rem;
            }
        }

        /* Removed previous gradient animation */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow:
                0 2px 12px 0 rgba(0, 212, 255, 0.10), /* soft subtle cyan */
                0 8px 32px 0 rgba(60, 80, 120, 0.08), /* soft blue-gray */
                0 1.5px 6px 0 rgba(0,0,0,0.07);
            padding: 24px; /* reduced padding */
            max-width: 320px; /* further reduced width */
            width: 100%;
            transition: all 0.3s ease;
        }
        .login-card:hover, .login-card:focus-within {
            transform: none; /* removed translate/scale to avoid zoom effect */
            box-shadow:
                0 4px 24px 0 rgba(0, 212, 255, 0.13),
                0 12px 48px 0 rgba(60, 80, 120, 0.10),
                0 2px 8px 0 rgba(0,0,0,0.10);
            animation: none;
        }
        @keyframes smoke-gradient-blow-cyan {
            0% {
                box-shadow:
                    0 0 0 4px rgba(0,255,255,0.28),
                    0 0 16px 8px rgba(0,212,255,0.18),
                    0 0 48px 24px rgba(180,255,255,0.12),
                    0 0 96px 48px rgba(255,255,255,0.22),
                    0 2px 8px 0 rgba(0,0,0,0.10);
            }
            50% {
                box-shadow:
                    0 0 0 8px rgba(0,255,255,0.38),
                    0 0 32px 16px rgba(0,212,255,0.22),
                    0 0 80px 40px rgba(200,255,255,0.16),
                    0 0 160px 64px rgba(255,255,255,0.28),
                    0 4px 16px 0 rgba(0,0,0,0.13);
            }
            100% {
                box-shadow:
                    0 0 0 4px rgba(0,255,255,0.28),
                    0 0 16px 8px rgba(0,212,255,0.18),
                    0 0 48px 24px rgba(180,255,255,0.12),
                    0 0 96px 48px rgba(255,255,255,0.22),
                    0 2px 8px 0 rgba(0,0,0,0.10);
            }
        }
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg,rgb(251, 251, 255) 0%,rgb(241, 239, 243) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        .login-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1.44rem;
        }
        .login-header p {
            color: #666;
            font-size: 11px;
        }
        /* Enhanced input styling with green hover and spinning gradient focus */
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 13px;
            transition: all 0.3s ease;
            position: relative;
            background: #fff;
        }

        /* Green hover effect - Enhanced with higher specificity */
        .form-control:hover, .form-select:hover,
        .input-group:hover .form-control {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
            transform: translateY(-1px);
        }

        /* Force green hover on all input types */
        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover,
        select:hover {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
        }

        /* Additional specificity for input groups */
        .input-group:hover input[type="text"],
        .input-group:hover input[type="email"],
        .input-group:hover input[type="password"] {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
        }

        /* Rainbow spinning border effect on focus - only when not hovering */
        .form-control:focus:not(:hover), .form-select:focus:not(:hover) {
            outline: none;
            border: 2px solid transparent;
            background: #fff;
            position: relative;
            z-index: 1;
        }

        .form-control:focus:not(:hover)::before, .form-select:focus:not(:hover)::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: conic-gradient(
                from 0deg,
                #ff0000, #ff1a1a, #ff3333, #ff4d4d, #ff6666, #ff8080, #ff9999, #ffb3b3,
                #ffcc00, #ffd633, #ffe066, #ffeb99, #ffff00, #ffff33, #ffff66, #ffff99,
                #00ff00, #33ff33, #66ff66, #99ff99, #00ffff, #33ffff, #66ffff, #99ffff,
                #0000ff, #3333ff, #6666ff, #9999ff, #ff00ff, #ff33ff, #ff66ff, #ff99ff,
                #ff0000
            );
            border-radius: 18px;
            z-index: -1;
            animation: rainbowSpin 1.5s linear infinite;
            filter: drop-shadow(0 0 8px rgba(255, 0, 0, 0.3)) 
                    drop-shadow(0 0 12px rgba(0, 255, 0, 0.3)) 
                    drop-shadow(0 0 16px rgba(0, 0, 255, 0.3));
        }

        @keyframes rainbowSpin {
            0% {
                transform: rotate(0deg) scale(1);
            }
            25% {
                transform: rotate(90deg) scale(1.02);
            }
            50% {
                transform: rotate(180deg) scale(1);
            }
            75% {
                transform: rotate(270deg) scale(1.02);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        /* Input group styling */
        .input-group .form-control:focus:not(:hover) {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }

        .input-group .form-control:focus:not(:hover)::before {
            border-radius: 0 17px 17px 0;
        }

        /* Enhanced input group hover effects */
        .input-group:hover .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }
        
        /* Center text in Student ID input field */
        #school_id {
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 12px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
        
        /* Icon-only back button */
        .btn-back-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #6c757d; /* same as secondary */
            color: #fff;
            border: none;
            box-shadow: 0 6px 12px rgba(108, 117, 125, 0.25);
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }
        .btn-back-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(108, 117, 125, 0.35);
            background: #5b646b;
            color: #fff;
        }
        .btn-back-icon i { font-size: 16px; }
        /* Avoid stretching when inside Bootstrap .d-grid containers */
        .d-grid .btn-back-icon { justify-self: start; }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(40, 167, 69, 0.4);
        }
        .btn-outline-info {
            border: 2px solid #17a2b8;
            color: #17a2b8;
            background: transparent;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-outline-info:hover {
            background: #17a2b8;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(23, 162, 184, 0.3);
            text-decoration: none;
        }
        .student-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
            animation: fadeIn 0.5s ease;
        }
        .student-info h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .student-info p {
            color: #666;
            margin: 5px 0;
        }
        .alert {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
        }
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%);
            color: white;
        }
        .alert-warning {
            background: linear-gradient(135deg, #ffd93d 0%, #ffe066 100%);
            color: #333;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* reCAPTCHA v3 Badge - Collapsed by default, expands on hover/focus */
        .grecaptcha-badge {
            position: fixed !important;
            top: 10px !important;
            right: 10px !important;
            z-index: 9999 !important;
            width: 70px !important;
            overflow: hidden !important;
            transition: width 0.3s ease !important;
            transform: scale(0.85);
            transform-origin: 100% 0;
        }
        
        .grecaptcha-badge:hover,
        .grecaptcha-badge:focus-within {
            width: 256px !important;
        }

        /* SweetAlert2 Mobile-Friendly Customization */
        .swal2-popup {
            width: 280px !important;
            padding: 20px !important;
            font-size: 0.875rem !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
            padding: 0 0 10px 0 !important;
        }
        
        .swal2-html-container {
            font-size: 0.813rem !important;
            margin: 0 0 15px 0 !important;
        }
        
        .swal2-icon {
            width: 60px !important;
            height: 60px !important;
            margin: 10px auto 15px !important;
            border-width: 3px !important;
        }
        
        .swal2-icon .swal2-icon-content {
            font-size: 2.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Success icon checkmark */
        .swal2-icon.swal2-success {
            border-color: #a5dc86 !important;
            border-radius: 50% !important;
            width: 60px !important;
            height: 60px !important;
        }
        
        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            height: 3px !important;
            background-color: #a5dc86 !important;
        }
        
        .swal2-icon.swal2-success .swal2-success-line-tip {
            width: 15px !important;
            left: 10px !important;
            top: 30px !important;
        }
        
        .swal2-icon.swal2-success .swal2-success-line-long {
            width: 28px !important;
            right: 10px !important;
            top: 26px !important;
        }
        
        .swal2-icon.swal2-success .swal2-success-ring {
            width: 60px !important;
            height: 60px !important;
            border: 3px solid rgba(165, 220, 134, 0.3) !important;
            border-radius: 50% !important;
        }
        
        .swal2-icon.swal2-success .swal2-success-fix {
            width: 4px !important;
            height: 60px !important;
            left: 28px !important;
        }
        
        /* Error icon X mark */
        .swal2-icon.swal2-error {
            border-color: #f27474 !important;
        }
        
        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            height: 3px !important;
            width: 30px !important;
            background-color: #f27474 !important;
            top: 28px !important;
        }
        
        .swal2-icon.swal2-error .swal2-x-mark-line-left {
            left: 15px !important;
        }
        
        .swal2-icon.swal2-error .swal2-x-mark-line-right {
            right: 15px !important;
        }
        
        /* Warning icon */
        .swal2-icon.swal2-warning {
            border-color: #facea8 !important;
            color: #f8bb86 !important;
            font-size: 2.5rem !important;
            line-height: 60px !important;
        }
        
        /* Info icon */
        .swal2-icon.swal2-info {
            border-color: #9de0f6 !important;
            color: #3fc3ee !important;
            font-size: 2.5rem !important;
            line-height: 60px !important;
        }
        
        /* Question icon */
        .swal2-icon.swal2-question {
            border-color: #c9dae1 !important;
            color: #87adbd !important;
            font-size: 2.5rem !important;
            line-height: 60px !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            font-size: 0.813rem !important;
            padding: 8px 20px !important;
        }
        
        .swal2-timer-progress-bar {
            height: 3px !important;
        }
        
        /* Responsive adjustments for very small screens */
        @media (max-width: 360px) {
            .swal2-popup {
                width: 260px !important;
                padding: 18px !important;
            }
            
            .swal2-title {
                font-size: 1.125rem !important;
            }
            
            .swal2-icon {
                width: 50px !important;
                height: 50px !important;
            }
            
            .swal2-icon.swal2-success {
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
            }
            
            .swal2-icon.swal2-success .swal2-success-line-tip {
                width: 13px !important;
                left: 8px !important;
                top: 25px !important;
            }
            
            .swal2-icon.swal2-success .swal2-success-line-long {
                width: 23px !important;
                right: 8px !important;
                top: 22px !important;
            }
            
            .swal2-icon.swal2-success .swal2-success-ring {
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
            }
            
            .swal2-icon.swal2-success .swal2-success-fix {
                left: 23px !important;
                height: 50px !important;
            }
            
            .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
                width: 25px !important;
                top: 23px !important;
            }
            
            .swal2-icon.swal2-error .swal2-x-mark-line-left {
                left: 12px !important;
            }
            
            .swal2-icon.swal2-error .swal2-x-mark-line-right {
                right: 12px !important;
            }
            
            .swal2-icon.swal2-warning,
            .swal2-icon.swal2-info,
            .swal2-icon.swal2-question {
                font-size: 2rem !important;
                line-height: 50px !important;
            }
        }

        /* Animated Forgot Password link */
        .forgot-link {
            position: relative;
            display: inline-block;
            color: #17a2b8;
            font-weight: 600;
            text-decoration: none;
            transition: color .25s ease, transform .2s ease;
        }
        .forgot-link::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -3px;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #17a2b8, #20c997, #667eea);
            border-radius: 2px;
            transition: width .25s ease, left .25s ease;
        }
        .forgot-link:hover {
            color: #138496;
            transform: translateY(-1px);
        }
        .forgot-link:hover::after {
            width: 100%;
            left: 0;
        }
        .forgot-link:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(23,162,184,.25);
            border-radius: 6px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 15px 0 0 15px;
            color: #667eea;
            transition: all 0.3s ease;
        }
        
        /* Green hover effect for input group text */
        .input-group:hover .input-group-text {
            border-color: #28a745 !important;
            background: rgba(40, 167, 69, 0.05) !important;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
            margin-bottom: 0;
        }

        /* Additional hover effects for better visual feedback */
        .form-control:hover, .form-select:hover {
            transition: all 0.3s ease;
        }

        /* Ensure hover effects work on all input types */
        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover,
        select:hover {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* reCAPTCHA Styling */
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
        
        /* reCAPTCHA responsive styling */
        @media (max-width: 576px) {
            .g-recaptcha {
                transform: scale(0.85);
                transform-origin: center;
            }
        }
        .lock-warning {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
        }

        /* Floating Cubes Animation */
        .floating-cubes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            transform-style: preserve-3d; /* allow children to render in 3D space */
        }
        .cube {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 5px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25), inset 0 0 10px rgba(255,255,255,0.25);
            animation: floatCube 18s infinite ease-in-out;
            transform: translateZ(-200px) rotateX(15deg) rotateY(20deg); /* push back slightly */
        }
        .cube:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; width: 25px; height: 25px; background: rgba(142, 45, 226, 0.2); }
        .cube:nth-child(2) { top: 20%; right: 10%; animation-delay: 2s; width: 20px; height: 20px; background: rgba(255, 20, 147, 0.2); }
        .cube:nth-child(3) { bottom: 30%; left: 20%; animation-delay: 4s; width: 30px; height: 30px; background: rgba(255, 255, 255, 0.2); }
        .cube:nth-child(4) { bottom: 10%; right: 20%; animation-delay: 6s; width: 22px; height: 22px; background: rgba(218, 112, 214, 0.2); }
        .cube:nth-child(5) { top: 50%; left: 50%; animation-delay: 8s; width: 28px; height: 28px; background: rgba(142, 45, 226, 0.25); }
        .cube:nth-child(6) { top: 70%; right: 30%; animation-delay: 10s; width: 18px; height: 18px; background: rgba(255, 20, 147, 0.18); }
        .cube:nth-child(7) { bottom: 50%; left: 70%; animation-delay: 12s; width: 26px; height: 26px; background: rgba(255, 255, 255, 0.22); }
        .cube:nth-child(8) { top: 30%; left: 80%; animation-delay: 14s; width: 24px; height: 24px; background: rgba(218, 112, 214, 0.2); }
        .cube:nth-child(9) { top: 5%; right: 5%; animation-delay: 16s; width: 20px; height: 20px; background: rgba(74, 0, 224, 0.2); }
        .cube:nth-child(10) { bottom: 5%; left: 5%; animation-delay: 18s; width: 32px; height: 32px; background: rgba(255, 20, 147, 0.25); }
        .cube:nth-child(11) { top: 80%; left: 30%; animation-delay: 20s; width: 19px; height: 19px; background: rgba(255, 255, 255, 0.2); }
        .cube:nth-child(12) { bottom: 20%; right: 50%; animation-delay: 22s; width: 27px; height: 27px; background: rgba(142, 45, 226, 0.2); }
        @keyframes floatCube {
            0%, 100% {
                transform: translateZ(-200px) translateY(0) translateX(0) rotateX(15deg) rotateY(20deg) rotateZ(0deg);
            }
            25% {
                transform: translateZ(-220px) translateY(-30px) translateX(20px) rotateX(35deg) rotateY(60deg) rotateZ(90deg);
            }
            50% {
                transform: translateZ(-260px) translateY(-60px) translateX(0) rotateX(75deg) rotateY(120deg) rotateZ(180deg);
            }
            75% {
                transform: translateZ(-220px) translateY(-30px) translateX(-20px) rotateX(35deg) rotateY(200deg) rotateZ(270deg);
            }
        }
        .countdown-timer {
            font-size: 2rem;
            font-weight: bold;
            margin: 15px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .lockout-progress {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            margin: 15px 0;
            overflow: hidden;
        }
        .lockout-progress-bar {
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            transition: width 1s linear;
        }
        

        /* Add this at the end of your <style> tag in login.blade.php */
@media (max-width: 575.98px) {
    .login-card {
        padding: 18px !important;
        max-width: 70vw !important;
        border-radius: 14px !important;
    }
    .login-header .logo {
        width: 60px !important;
        height: 60px !important;
        font-size: 1.3rem !important;
    }
    .login-header h2 {
        font-size: 1.2rem !important;
    }
    .form-control, .form-select {
        font-size: 12px !important;
        padding: 8px 10px !important;
        border-radius: 8px !important;
    }
    
    /* Mobile responsive focus effects */
    .form-control:focus:not(:hover)::before, .form-select:focus:not(:hover)::before {
        border-radius: 12px !important;
    }
    
    .input-group .form-control:focus:not(:hover)::before {
        border-radius: 0 12px 12px 0 !important;
    }

    /* Mobile responsive hover effects */
    .form-control:hover, .form-select:hover,
    input[type="text"]:hover,
    input[type="email"]:hover,
    input[type="password"]:hover,
    select:hover {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
    }
    .btn, .btn-primary, .btn-success, .btn-secondary, .btn-outline-info {
        font-size: 12px !important;
        padding: 10px 8px !important;
        border-radius: 8px !important;
    }
    
    /* Mobile responsive button layout - exclude student login form buttons */
    .d-flex.justify-content-between:not(.align-items-center) {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .d-flex.justify-content-between:not(.align-items-center) .btn {
        width: 100% !important;
    }
    .student-info, .alert {
        padding: 10px !important;
        font-size: 12px !important;
        border-radius: 8px !important;
    }
    .signup-link {
        font-size: 11px !important;
        padding-top: 8px !important;
    }
    .countdown-timer {
        font-size: 1.2rem !important;
        padding: 10px !important;
    }
}

/* Mobile Footer - Only visible on mobile */
@media (max-width: 767px) {
    .mobile-footer {
        position: fixed;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 350px;
        text-align: center;
        padding: 8px 10px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 9px;
        line-height: 1.2;
        z-index: 10;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .mobile-footer:hover,
    .mobile-footer:focus {
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
    }
    
    .mobile-footer p {
        margin: 0;
    }
    
    .mobile-footer a {
        pointer-events: all;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .mobile-footer a:hover {
        color: #ffff99 !important;
        text-decoration: underline;
    }
}

/* Hide mobile footer on desktop */
@media (min-width: 768px) {
    .mobile-footer {
        display: none;
    }
}
@media (max-width: 576px) {
    .login-logo-img {
        width: 70px !important;
        height: 70px !important;
        max-width: 80vw !important;
        padding: 2px !important;
    }
    .login-header .logo {
        width: 80px !important;
        height: 80px !important;
        min-width: 0 !important;
        min-height: 0 !important;
    }
}

        /* Responsive Design for Mobile/Desktop Login */
        .mobile-student-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 15px;
            padding: 16px 20px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: clamp(14px, 4vw, 18px);
            line-height: 1.1;
            color: white;
            transition: all 0.3s ease;
            display: none !important;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .mobile-student-btn.show-mobile {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .mobile-student-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .mobile-student-btn i {
            flex-shrink: 0;
        }

        @media (max-width: 360px) {
            .mobile-student-btn {
                padding: 14px 16px;
            }
            .mobile-student-btn.show-mobile {
                gap: 6px;
            }
        }

        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .mobile-student-btn.show-mobile {
                display: inline-flex !important;
            }
            .desktop-user-select {
                display: none !important;
            }
        }

        /* Desktop-specific styles */
        @media (min-width: 769px) {
            .mobile-student-btn {
                display: none !important;
            }
            .desktop-user-select {
                display: block !important;
            }
            
            /* Enhanced desktop button styling for student login */
            .d-flex.justify-content-between .btn-secondary,
            .d-flex.justify-content-between .btn-outline-info {
                padding: 12px 20px !important;
                font-weight: 600 !important;
                border-radius: 15px !important;
                transition: all 0.3s ease !important;
            }
            
            .btn-secondary {
                background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
                border: none !important;
            }
            
            .btn-secondary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3) !important;
            }
            
            .btn-outline-info:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 10px 25px rgba(23, 162, 184, 0.3) !important;
            }
        }

        /* Enhanced mobile layout for student login form buttons */
        @media (max-width: 768px) {
            /* Keep student login buttons horizontal but make them more mobile-friendly */
            .d-flex.justify-content-between.align-items-center {
                gap: 6px !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
            }
            
            /* Make student login buttons more compact and appealing on mobile */
            .d-flex.justify-content-between.align-items-center .btn-secondary,
            .d-flex.justify-content-between.align-items-center .btn-outline-info {
                flex: 1 !important;
                padding: 10px 8px !important;
                font-size: 13px !important;
                border-radius: 12px !important;
                font-weight: 600 !important;
                white-space: nowrap !important;
                min-width: 0 !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            /* Adjust Back button to be slightly smaller */
            .d-flex.justify-content-between.align-items-center .btn-secondary {
                flex: 0.8 !important;
            }
            
            /* Reset Password button gets more space */
            .d-flex.justify-content-between.align-items-center .btn-outline-info {
                flex: 1.2 !important;
            }
            
            /* Keep icon visible for forgot password button */
            
            /* For very small screens, adjust sizing */
            @media (max-width: 400px) {
                .d-flex.justify-content-between.align-items-center .btn-secondary,
                .d-flex.justify-content-between.align-items-center .btn-outline-info {
                    font-size: 12px !important;
                    padding: 9px 6px !important;
                }
                
                .d-flex.justify-content-between.align-items-center {
                    gap: 4px !important;
                }
            }
            
            @media (max-width: 350px) {
                .d-flex.justify-content-between.align-items-center .btn-secondary,
                .d-flex.justify-content-between.align-items-center .btn-outline-info {
                    font-size: 11px !important;
                    padding: 8px 4px !important;
                }
                
                .d-flex.justify-content-between.align-items-center {
                    gap: 3px !important;
                }
            }
            
            /* Enhance back button styling */
            .d-flex.justify-content-between.align-items-center .btn-secondary {
                background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
                border: none !important;
                color: white !important;
                transition: all 0.3s ease !important;
            }
            
            .d-flex.justify-content-between.align-items-center .btn-secondary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3) !important;
                color: white !important;
            }
            
            /* Enhance forgot password button styling */
            .d-flex.justify-content-between.align-items-center .btn-outline-info {
                background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%) !important;
                border: 2px solid #17a2b8 !important;
                color: #17a2b8 !important;
                transition: all 0.3s ease !important;
            }
            
            .d-flex.justify-content-between.align-items-center .btn-outline-info:hover {
                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3) !important;
                color: white !important;
                border-color: #17a2b8 !important;
            }
        }
    </style>
</head>
<body>
    <!-- 3D Cube background container -->
    <div class="floating-cubes" aria-hidden="true">
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
    </div>

    <!--Id checking Alert-->
    @if (session('id_error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '{{ session('id_error_title', 'ID Not Found') }}',
                text: '{{ session('id_error') }}',
                confirmButtonColor: '#667eea'
            });
        </script>
    @endif

    @if (session('id_verified'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'ID Verified!',
                text: 'Student ID found. Please login with your credentials.',
                confirmButtonColor: '#667eea',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'success-alert-popup'
                },
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                        popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                    }
                }
            });
        </script>
    @endif
    <!--ends of Id check alert-->
    <!-- Add after the bubbles background, before .login-card -->
    <div id="idLoadingOverlay" style="
        display: none;
        position: fixed;
        z-index: 99999;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(102,126,234,0.85);
        align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem; text-align: center;
    ">
        <div style="background: rgba(255,255,255,0.12); padding: 40px 30px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); display: flex; flex-direction: column; align-items: center;">
            <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div>Checking ID...</div>
        </div>
    </div>

    <div class="login-card">
        <div class="login-header">
            <div class="logo" style="box-shadow: 0 4px 16px rgb(253, 253, 253); display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="login-logo-img" style="width: 120px; height: 120px; object-fit: contain; padding: 5px; max-width: 100%; height: auto;">
            </div>
            <h2 style="margin-top: 0;">MCC | IPES</h2>
            <p>Instructors Performance Evaluation System</p>
        </div>

        

             @if (!$show_student_form && !$show_login_form)

                <!-- Mobile Student Login Button (Visible only on mobile) -->
                <button type="button" class="mobile-student-btn" onclick="startMobileStudentLogin()" style="{{ ($force_admin_form && $adminOtpOverlayEnabled) ? 'display: none;' : '' }}">
                    <i class="fas fa-graduation-cap"></i> Start Student Login
                </button>

                <!-- Desktop User Type Selection (Hidden on mobile) -->
                <div class="desktop-user-select" style="{{ ($force_admin_form && $adminOtpOverlayEnabled) ? 'display: none;' : '' }}">
                    <form method="POST" id="userTypeForm">
                        <div class="mb-4">
                            <label for="user_type" class="form-label">
                                <i class="fas fa-user-tag"></i> Select User Types
                            </label>
                            <select class="form-select" id="user_type" name="user_type" onchange="handleUserTypeChange()" required>
                                <option value="">Choose your role...</option>
                                <option value="student">Students</option>
                                <option value="admin">Administrator</option>
                                <!--<option value="staff">CSVLoader</option>-->
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Student ID Form (Initially Hidden) -->
                <form method="POST" id="studentIdForm" style="display: none;" action="{{ route('verify.student.id') }}">
                    @csrf
                    <input type="hidden" name="user_type" value="student">
                    <div class="mb-3">
                        <label for="school_id" class="form-label">
                            <i class="fas fa-id-card"></i> School ID
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <input type="text" class="form-control" id="school_id" name="school_id"
                                   placeholder="Enter your School ID" required
                                   pattern="[0-9]{4}-[0-9]{4}" maxlength="9"
                                   title="Format: 0000-0000 (e.g., 2024-0001)" value="" autocomplete="off">
                        </div>
                    </div>
                    <button type="submit" name="verify_student_id" class="btn btn-success" id="verifyStudentIdBtn">
                        <i class="fas fa-search"></i> Verify Student ID
                    </button>
                    <div class="mt-2">
                        <button type="button" class="btn-back-icon" onclick="resetForm()" aria-label="Back">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>


                    <!-- Student Signup Link -->
                   <div class="signup-link">
                    <!-- <p>Second Semester Evaluation is Coming!!!</p>-->
                    <!--  <p>Don't have an account?<a href="{{ route('pre_signup') }}">
                            <i class="fas fa-user-plus"></i> Sign up here
                        </a></p>-->
                      <p>Don't have an account?<a href="{{ route('idcheck') }}">
                            <i class="fas fa-user-plus"></i> Sign up here
                        </a></p>
                    </div>

                    
                </form>
                
                <!-- Admin Login Form (Initially Hidden) -->
                <form method="POST" id="adminLoginForm" style="{{ ($force_admin_form && $adminOtpOverlayEnabled) ? '' : 'display: none;' }}" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="latitude" id="admin-login-latitude">
                    <input type="hidden" name="longitude" id="admin-login-longitude">
                    <input type="hidden" name="user_type" value="admin">
                      <div id="adminErrorBox" class="alert alert-danger d-none" style="border-radius:12px; padding:10px; margin-bottom:15px; display: flex; align-items: center; justify-content: center; text-align: center; font-weight: 600;"></div>                 
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Adddress
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                            <input type="email" class="form-control" id="admin_email" name="email"
                                   placeholder="Enter your email" value="{{ old('email', $pending_admin_email) }}" autocomplete="off" {{ ($admin_otp_pending && $adminOtpOverlayEnabled) ? 'readonly' : '' }}>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="admin_password" name="password"
                                   placeholder="Enter your password" value="" autocomplete="new-password" {{ ($admin_otp_pending && $adminOtpOverlayEnabled) ? 'readonly' : '' }}>
                        </div>
                        <div class="d-flex align-items-center justify-content-end mt-2">
                            <label class="form-check-label me-2 small" for="showAdminPassword" style="font-size: 0.875rem; color: #666;">
                                Show password
                            </label>
                            <input class="form-check-input" type="checkbox" id="showAdminPassword" style="transform: scale(0.8);">
                        </div>
                    </div>

                    <!-- reCAPTCHA for Admin -->
                    @if(isset($captchaType) && $captchaType === 'checkbox' && config('services.recaptcha.site_key_v2'))
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key_v2') }}"></div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-primary" {{ ($admin_otp_pending && $adminOtpOverlayEnabled) ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i> Login as Administrator
                        </button>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn-back-icon admin-back-btn" onclick="resetForm()" aria-label="Back" {{ ($admin_otp_pending && $adminOtpOverlayEnabled) ? 'disabled' : '' }}>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                </form>

                <!-- Staff Login Form (Initially Hidden) -->
                <form method="POST" id="staffLoginForm" style="display: none;" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="latitude" id="staff-login-latitude">
                    <input type="hidden" name="longitude" id="staff-login-longitude">
                    <input type="hidden" name="user_type" value="staff">
                    <div class="mb-3">
                        <label for="staff_email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                            <input type="email" class="form-control" id="staff_email" name="email"
                                   placeholder="Enter your email" required value="" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="staff_password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="staff_password" name="password"
                                   placeholder="Enter your password" required value="" autocomplete="new-password">
                        </div>
                    </div>s

                    <!-- reCAPTCHA for Staff -->
                    @if(isset($captchaType) && $captchaType === 'checkbox' && config('services.recaptcha.site_key_v2'))
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key_v2') }}"></div>
                        </div>
                    @endif

                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login as CSvsss
                    </button>
                    <button type="button" class="btn-back-icon" onclick="resetForm()" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </form>
            @endif

            @if ($show_login_form && $student_data)
            
                <!-- Student Login Form -->
                <form method="POST" id="studentID" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="latitude" id="student-login-latitude">
                    <input type="hidden" name="longitude" id="student-login-longitude">
                    <input type="hidden" name="user_type" value="student">
                    <div id="studentErrorBox" class="alert alert-danger d-none" style="border-radius:12px; padding:10px; margin-bottom:15px; display: flex; align-items: center; justify-content: center; text-align: center; font-weight: 600;"></div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fab fa-microsoft text-primary"></i> MS Email Account
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fab fa-microsoft text-primary"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="your.email@mcclawis.edu.ph"
                                value="{{ session('verified_student_email', '') }}" autocomplete="off"
                                pattern="^[A-Za-z0-9._\-]+@mcclawis\.([eE][dD][uU]|[eE][dD][iI])\.ph$"
                                title="Local part may contain letters, numbers, dot (.), hyphen (-) and underscore (_); must end with @mcclawis.edu.ph or @mcclawis.edi.ph">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Enter your mccipes password" value="" autocomplete="new-password">
                        </div>
                        <div class="d-flex align-items-center justify-content-end mt-2">
                            <label class="form-check-label me-2 small" for="showPassword" style="font-size: 0.875rem;">
                                Show password
                            </label>
                            <input class="form-check-input" type="checkbox" id="showPassword" style="transform: scale(0.8);">
                        </div>
                    </div>

                    <!-- reCAPTCHA for Student -->
                    @if(isset($captchaType) && $captchaType === 'checkbox' && config('services.recaptcha.site_key_v2'))
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key_v2') }}"></div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login as Student
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <a href="{{ route('clear.student.verification') }}" class="btn-back-icon" aria-label="Back">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                      <!--  <a href="{{ route('password.request') }}" class="forgot-link">
                            Forgot Password?
                        </a>-->
                       
                    </div>
                </form>

                <!-- Student Signup Link -->
                <div class="signup-link">
                   <p>Sorry!!Forgot Password is unavailable!<!-- <a href="{{ route('pre_signup', ['type' => 'student', 'school_id' => $student_data['school_id']]) }}">
                        <i class="fas fa-user-plus"></i> Sign up here
                    </a>--></p>
                </div>
            @endif      
    </div>

    <div class="otp-overlay {{ $adminOtpOverlayEnabled ? 'active' : '' }}" id="adminOtpOverlay" role="dialog" aria-modal="true">
        <div class="otp-modal" role="document">
           
            <div class="otp-modal-title">Administrator Verification</div>
            <p class="otp-modal-subtitle">Enter the 6digit code sent to <span class="otp-email" id="adminOtpEmail">{{ $pending_admin_email }}</span></p>
            <div class="otp-error" id="adminOtpError" role="alert"></div>
            <div class="otp-input-group" id="adminOtpInputs">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 1">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 2">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 3">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 4">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 5">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="Digit 6">
            </div>
            <div class="otp-actions">
                <button type="button" class="btn otp-primary-btn" id="adminOtpSubmitButton">
                    Verify Code
                </button>
                <div class="otp-utility-actions">
                    <button type="button" id="adminOtpResendButton">Resend Code</button>
                </div>
            </div>
            <div class="otp-status" id="adminOtpStatus" role="status">{{ $admin_otp_message }}</div>
            <button type="button" class="otp-cancel-link" id="adminOtpBackButton">Back to Login</button>
        </div>
    </div>

    <!-- Mobile Footer - Only visible on mobile -->
    <div class="mobile-footer">
        <a id="superloginFooterLink" href="{{ route('superadmin.login') }}" data-href="{{ route('superadmin.login') }}" style="color: #ffffffff; text-decoration: none; font-weight: 600;"><p>&copy;{{ date('Y') }} MCC | Instructors Performance Evaluation System |Capstone Project Developed by: Warren Ilustrisimo | Jenford Albaciete | Jerry Nasol | Cristina Ilustrisimo </p></a>
        
    </div>
            
    @if(session('lockout_timer'))
        <div id="lockoutOverlay" style="
            position: fixed; z-index: 9999; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(102,126,234,0.95); display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: #fff; font-size: 1.5rem; text-align: center;">
            <div style="background: rgba(255,255,255,0.1); padding: clamp(20px, 5vw, 36px) clamp(18px, 6vw, 28px); border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); width: min(88vw, 420px); max-width: 420px;">
                <i class="fas fa-lock fa-3x mb-3"></i>
                <h3 class="mb-3">Account Locked</h3>
                <p class="mb-2">Too many failed login attempts.<br>
                Please wait <span id="lockoutCountdown">{{ session('lockout_timer') }}</span> seconds before trying again.</p>
            </div>
        </div>
        <script>
            let countdown = {{ session('lockout_timer') }};
            const countdownEl = document.getElementById('lockoutCountdown');
            const overlay = document.getElementById('lockoutOverlay');
            const interval = setInterval(() => {
                countdown--;
                if (countdownEl) countdownEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    // After timer, reload to user type selection
                    window.location.href = "{{ route('login') }}";
                }
            }, 1000);
        </script>
    @endif



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const adminOtpController = (function () {
            const overlay = document.getElementById('adminOtpOverlay');
            if (!overlay) return null;
            const inputs = Array.from(overlay.querySelectorAll('#adminOtpInputs input'));
            const submitButton = document.getElementById('adminOtpSubmitButton');
            const resendButton = document.getElementById('adminOtpResendButton');
            const backButton = document.getElementById('adminOtpBackButton');
            const errorBox = document.getElementById('adminOtpError');
            const statusBox = document.getElementById('adminOtpStatus');
            const emailDisplay = document.getElementById('adminOtpEmail');
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
            const verifyUrl = @json(route('admin.otp.verify'));
            const resendUrl = @json(route('admin.otp.resend'));
            const loginUrl = @json(route('login'));
            const submitDefault = submitButton ? submitButton.innerHTML : '';
            const resendDefault = resendButton ? resendButton.innerHTML : '';
            let resendTimer = null;
            let resendCountdown = 0;
            function numeric(value) {
                return value.replace(/[^0-9]/g, '');
            }
            function getValue() {
                return inputs.map((input) => input.value).join('');
            }
            function clearInputs() {
                inputs.forEach((input) => {
                    input.value = '';
                    input.classList.remove('error');
                });
                if (inputs.length) inputs[0].focus();
                updateSubmitState();
            }
            function setError(message) {
                if (!errorBox) return;
                if (message) {
                    errorBox.textContent = message;
                    errorBox.classList.add('show');
                    inputs.forEach((input) => input.classList.add('error'));
                } else {
                    errorBox.textContent = '';
                    errorBox.classList.remove('show');
                    inputs.forEach((input) => input.classList.remove('error'));
                }
            }
            function setStatus(message) {
                if (!statusBox) return;
                statusBox.textContent = message || '';
            }
            function toggleLoadingState(isLoading) {
                if (backButton) {
                    backButton.disabled = isLoading;
                }
                if (isLoading) {
                    document.body.classList.add('admin-otp-loading');
                } else {
                    document.body.classList.remove('admin-otp-loading');
                }
            }
            function updateSubmitState() {
                if (!submitButton) return;
                submitButton.disabled = getValue().length !== inputs.length;
            }
            function setSubmitLoading(isLoading) {
                if (!submitButton) return;
                if (isLoading) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
                } else {
                    submitButton.innerHTML = submitDefault;
                    updateSubmitState();
                }
            }
            function updateResendView() {
                if (!resendButton) return;
                if (resendCountdown > 0) {
                    resendButton.disabled = true;
                    resendButton.innerHTML = '<i class="fas fa-hourglass-half"></i> Resend in ' + resendCountdown + 's';
                } else {
                    resendButton.disabled = false;
                    resendButton.innerHTML = resendDefault;
                }
            }
            function startResendCountdown(seconds) {
                clearInterval(resendTimer);
                resendCountdown = seconds;
                updateResendView();
                resendTimer = setInterval(() => {
                    resendCountdown -= 1;
                    if (resendCountdown <= 0) {
                        clearInterval(resendTimer);
                        resendTimer = null;
                        resendCountdown = 0;
                    }
                    updateResendView();
                }, 1000);
            }
            async function submitOtp() {
                const code = getValue();
                if (code.length !== inputs.length) {
                    setError('Enter the complete code.');
                    return;
                }
                setError('');
                setStatus('');
                setSubmitLoading(true);
                toggleLoadingState(true);
                try {
                    const response = await fetch(verifyUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json'
                        },
                        body: JSON.stringify({ otp_code: code })
                    });
                    const payload = await response.json();
                    if (response.ok && payload.status === 'success') {
                        // Success - Show SweetAlert and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Verification Successful',
                            text: 'You have been successfully verified. Redirecting...',
                            confirmButtonColor: '#667eea',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'success-alert-popup'
                            },
                            didOpen: function() {
                                const popup = Swal.getPopup();
                                if (popup) {
                                    popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                                    popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                                }
                            }
                        }).then(() => {
                            if (payload.redirect) {
                                window.location.href = payload.redirect;
                            }
                        });
                    } else {
                        const message = payload.message || 'Unable to verify code.';
                        setError(message);
                        
                        if (response.status === 422) {
                            // Invalid code or session expired
                            if (message.toLowerCase().includes('please login again')) {
                                // Session expired
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: message,
                                    confirmButtonColor: '#667eea',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                }).then(() => {
                                    window.location.href = loginUrl;
                                });
                            } else if (message.toLowerCase().includes('expired')) {
                                // OTP expired
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Code Expired',
                                    text: message + ' Please request a new code.',
                                    confirmButtonColor: '#667eea',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                            } else {
                                // Wrong code - show remaining attempts
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Incorrect Code',
                                    text: message,
                                    confirmButtonColor: '#667eea',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                                clearInputs();
                            }
                        } else if (response.status === 423) {
                            // Too many failed attempts
                            Swal.fire({
                                icon: 'error',
                                title: 'Too Many Attempts',
                                text: message,
                                confirmButtonColor: '#667eea',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                window.location.href = loginUrl;
                            });
                        } else {
                            clearInputs();
                        }
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Please check your connection and try again.',
                        confirmButtonColor: '#667eea',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    setError('Network error. Please try again.');
                } finally {
                    setSubmitLoading(false);
                    toggleLoadingState(false);
                }
            }
            async function resendOtp() {
                if (!resendButton || resendButton.disabled) return;
                setError('');
                setStatus('');
                resendButton.disabled = true;
                toggleLoadingState(true);
                resendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                try {
                    const response = await fetch(resendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json'
                        },
                        body: JSON.stringify({})
                    });
                    const payload = await response.json();
                    if (response.ok && payload.status === 'success') {
                        // Code resent successfully
                        Swal.fire({
                            icon: 'success',
                            title: 'Code Sent',
                            text: 'A new verification code has been sent to your email.',
                            confirmButtonColor: '#667eea',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'success-alert-popup'
                            },
                            didOpen: function() {
                                const popup = Swal.getPopup();
                                if (popup) {
                                    popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                                    popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                                }
                            }
                        });
                        setStatus(payload.message || '');
                        startResendCountdown(60);
                    } else {
                        const message = payload.message || 'Unable to send code.';
                        setError(message);
                        if (response.status === 422) {
                            if (message.toLowerCase().includes('please login again')) {
                                // Session expired
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: message,
                                    confirmButtonColor: '#667eea',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                }).then(() => {
                                    window.location.href = loginUrl;
                                });
                            } else {
                                // Other error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Resend Failed',
                                    text: message,
                                    confirmButtonColor: '#667eea'
                                });
                            }
                        } else if (response.status === 429) {
                            // Rate limited
                            const waitMatch = message.match(/\d+/);
                            const waitSeconds = waitMatch ? parseInt(waitMatch[0], 10) : 60;
                            Swal.fire({
                                icon: 'info',
                                title: 'Please Wait',
                                text: message,
                                confirmButtonColor: '#667eea'
                            });
                            startResendCountdown(waitSeconds);
                        } else {
                            // Generic error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message,
                                confirmButtonColor: '#667eea'
                            });
                        }
                    }
                } catch (error) {
                    // Network error
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Please check your connection and try again.',
                        confirmButtonColor: '#667eea'
                    });
                    setError('Network error. Please try again.');
                } finally {
                    toggleLoadingState(false);
                    if (resendCountdown === 0) {
                        resendButton.disabled = false;
                        resendButton.innerHTML = resendDefault;
                    }
                }
            }
            function handlePaste(event) {
                event.preventDefault();
                const text = numeric((event.clipboardData || window.clipboardData).getData('text')).slice(0, inputs.length);
                inputs.forEach((input, index) => {
                    input.value = text[index] || '';
                });
                updateSubmitState();
                const nextEmpty = inputs.find((input) => input.value === '');
                if (nextEmpty) {
                    nextEmpty.focus();
                } else if (submitButton) {
                    submitButton.focus();
                }
            }
            function bindEvents() {
                inputs.forEach((input, index) => {
                    input.addEventListener('input', (event) => {
                        const value = numeric(event.target.value);
                        event.target.value = value.charAt(0) || '';
                        if (value && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                            inputs[index + 1].select();
                        }
                        updateSubmitState();
                    });
                    input.addEventListener('keydown', (event) => {
                        if (event.key === 'Backspace' && !input.value && index > 0) {
                            event.preventDefault();
                            inputs[index - 1].focus();
                            inputs[index - 1].value = '';
                        }
                    });
                    input.addEventListener('paste', handlePaste);
                    input.addEventListener('focus', () => input.select());
                });
                if (submitButton) {
                    submitButton.addEventListener('click', submitOtp);
                }
                if (resendButton) {
                    resendButton.addEventListener('click', resendOtp);
                }
                if (backButton) {
                    backButton.addEventListener('click', () => {
                        window.location.href = loginUrl;
                    });
                }
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && overlay.classList.contains('active')) {
                        event.preventDefault();
                        submitOtp();
                    }
                    if (event.key === 'Escape' && overlay.classList.contains('active')) {
                        event.preventDefault();
                        window.location.href = loginUrl;
                    }
                });
            }
            function open() {
                if (!overlay) return;
                overlay.classList.add('active');
                if (inputs.length) {
                    inputs[0].focus();
                    inputs[0].select();
                }
            }
            function prefillEmail(email) {
                if (emailDisplay && email) {
                    emailDisplay.textContent = email;
                }
            }
            bindEvents();
            updateSubmitState();
            if (overlay.classList.contains('active')) {
                open();
            }
            if (statusBox && statusBox.textContent) {
                setStatus(statusBox.textContent);
            }
            if (resendButton && typeof window.adminOtpCooldown === 'number' && window.adminOtpCooldown > 0) {
                startResendCountdown(window.adminOtpCooldown);
            }
            if (submitButton) {
                updateSubmitState();
            }
            if (!overlay.classList.contains('active') && window.adminOtpOverlayEnabled) {
                open();
            }
            return {
                open,
                prefillEmail,
                setStatus,
                startResendCountdown
            };
        })();
        const loginGeolocationManager = (function () {
            const coordinateTargets = [
                { latId: 'admin-login-latitude', lngId: 'admin-login-longitude' },
                { latId: 'staff-login-latitude', lngId: 'staff-login-longitude' },
                { latId: 'student-login-latitude', lngId: 'student-login-longitude' }
            ];
            let isRequesting = false;
            let storedCoordinates = null;
            let errorNotified = false;

            function applyToInputs(lat, lng) {
                console.log('Applying coordinates to form inputs:', { lat, lng });
                let appliedCount = 0;
                coordinateTargets.forEach((target) => {
                    const latitudeField = document.getElementById(target.latId);
                    const longitudeField = document.getElementById(target.lngId);
                    if (latitudeField) {
                        latitudeField.value = lat;
                        appliedCount++;
                        console.log(`Applied latitude to #${target.latId}:`, lat);
                    }
                    if (longitudeField) {
                        longitudeField.value = lng;
                        appliedCount++;
                        console.log(`Applied longitude to #${target.lngId}:`, lng);
                    }
                });
                console.log('Coordinate fields updated:', appliedCount);
            }

            function requestCoordinates(forceRequest = false) {
                return new Promise((resolve, reject) => {
                    const isForcedAttempt = Boolean(forceRequest);
                    const canUseGeolocation = window.isSecureContext || ['localhost', '127.0.0.1'].includes(window.location.hostname);

                    console.log('requestCoordinates called:', { forceRequest: isForcedAttempt, canUseGeolocation });

                    if (!canUseGeolocation) {
                        if (isForcedAttempt && !errorNotified) {
                            errorNotified = true;
                            console.warn('Geolocation requires HTTPS or localhost.');
                        }
                        resolve(); // Resolve even if geolocation not available
                        return;
                    }

                    if (isRequesting && !isForcedAttempt) {
                        console.log('Already requesting coordinates, skipping duplicate request');
                        resolve();
                        return;
                    }

                    if (storedCoordinates && !isForcedAttempt) {
                        console.log('Using stored coordinates:', storedCoordinates);
                        applyToInputs(storedCoordinates.lat, storedCoordinates.lng);
                        resolve();
                        return;
                    }

                    if (!navigator.geolocation) {
                        console.warn('Geolocation not available');
                        resolve();
                        return;
                    }

                    isRequesting = true;
                    console.log('Requesting device geolocation...');
                    
                    let retryCount = 0;
                    const maxRetries = 2;
                    
                    const attemptGeolocation = () => {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                isRequesting = false;
                                const lat = position.coords.latitude.toString();
                                const lng = position.coords.longitude.toString();
                                const accuracy = Math.round(position.coords.accuracy);
                                console.log('Geolocation success:', { lat, lng, accuracy });
                                storedCoordinates = { lat, lng };
                                applyToInputs(lat, lng);
                                document.dispatchEvent(new CustomEvent('login-geolocation:success', { detail: { lat, lng, accuracy } }));
                                resolve();
                            },
                            (error) => {
                                isRequesting = false;
                                console.warn('Geolocation error:', error.code, error.message);
                                
                                // Retry on timeout (error code 3)
                                if (error.code === 3 && retryCount < maxRetries) {
                                    retryCount++;
                                    console.log(`Geolocation timeout, retrying... (${retryCount}/${maxRetries})`);
                                    setTimeout(() => attemptGeolocation(), 1000);
                                    return;
                                }
                                
                                document.dispatchEvent(new CustomEvent('login-geolocation:failed', { detail: { code: error.code, message: error.message } }));

                                // Show notifications based on error type
                                if (error.code === 1) { // PERMISSION_DENIED
                                    console.warn('Geolocation permission denied');
                                } else if (error.code === 3) { // TIMEOUT
                                    console.warn('Geolocation request timed out after retries');
                                }

                                resolve(); // Resolve even on error - will fall back to IP
                            },
                            { 
                                enableHighAccuracy: true, 
                                timeout: 15000,  // Reduced from 20s for faster fallback
                                maximumAge: 60000  // Use cached position if < 1 minute old
                            }
                        );
                    };
                    
                    attemptGeolocation();
                    document.dispatchEvent(new CustomEvent('login-geolocation:requested'));
                });
            }

            return {
                request: requestCoordinates,
                applyStoredCoordinates: function () {
                    if (storedCoordinates) {
                        console.log('Applying stored coordinates');
                        applyToInputs(storedCoordinates.lat, storedCoordinates.lng);
                    }
                },
                watch: function (callback) {
                    if (typeof callback === 'function') {
                        document.addEventListener('login-geolocation:requested', () => callback('requested'));
                        document.addEventListener('login-geolocation:failed', () => callback('failed'));
                    }
                }
            };
        })();

        document.addEventListener('DOMContentLoaded', function () {
            loginGeolocationManager.request().catch(() => {}); // Ignore errors on page load

            const adminForm = document.getElementById('adminLoginForm');
            if (adminForm) {
                adminForm.addEventListener('focusin', loginGeolocationManager.applyStoredCoordinates);
            }

            const staffForm = document.getElementById('staffLoginForm');
            if (staffForm) {
                staffForm.addEventListener('focusin', loginGeolocationManager.applyStoredCoordinates);
            }

            const studentForm = document.getElementById('studentID');
            if (studentForm) {
                studentForm.addEventListener('focusin', loginGeolocationManager.applyStoredCoordinates);
            }

        });

        // SweetAlert for successful registration
        @if (session('success') && session('registration_success'))
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#667eea',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
        @endif

        // SweetAlert for Admin OTP Sent Message
        @if (session('admin_otp_message'))
        Swal.fire({
            icon: 'success',
            title: 'Verification Code Sent',
            html: '<div style="font-size: 0.95rem; color: #4b5563;">' +
                  '{{ session("admin_otp_message") }}<br><br>' +
                  '<small style="color: #9ca3af;">Check your email for the 6-digit verification code.</small>' +
                  '</div>',
            confirmButtonColor: '#667eea',
            timer: 3500,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            // OTP modal will already be visible due to the 'active' class
            if (adminOtpController) {
                adminOtpController.open();
                adminOtpController.prefillEmail('{{ session("pending_admin_email") }}');
            }
        });
        @endif

        // SweetAlert for login errors
        @if (session('error'))
        const isStudentLoginView = {{ ($show_login_form && $student_data) ? 'true' : 'false' }};
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '{{ session('error') }}',
            confirmButtonColor: '#667eea',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: true
        }).then(() => {
            const lastRole = localStorage.getItem('lastRole');
            if (!isStudentLoginView && lastRole === 'student-id') {
                // Student ID check error: return to role selection
                resetForm();
            } else if (!isStudentLoginView) {
                // Admin or unknown: keep admin form visible and focus email
                const userTypeForm = document.getElementById('userTypeForm');
                const adminForm = document.getElementById('adminLoginForm');
                const studentIdForm = document.getElementById('studentIdForm');
                const staffForm = document.getElementById('staffLoginForm');
                if (userTypeForm && adminForm && studentIdForm && staffForm) {
                    userTypeForm.style.display = 'none';
                    adminForm.style.display = 'block';
                    studentIdForm.style.display = 'none';
                    staffForm.style.display = 'none';
                }
                const desktopSel = document.querySelector('.desktop-user-select');
                if (desktopSel) desktopSel.style.display = 'none';
                const mobileBtn = document.querySelector('.mobile-student-btn');
                if (mobileBtn) mobileBtn.classList.remove('show-mobile');
                const emailEl = document.getElementById('admin_email');
                if (emailEl) emailEl.focus();
                if (adminOtpController && window.adminOtpOverlayEnabled) {
                    adminOtpController.prefillEmail('{{ $pending_admin_email }}');
                    adminOtpController.open();
                }
            }
        });
        @endif

        // SweetAlert for login success - Show on login form, then redirect to dashboard
        @if (session('login_success'))
        Swal.fire({
            icon: 'success',
            title: 'Login Successful!',
            text: 'Welcome back, {{ session("user_name") }}!',
            confirmButtonColor: '#667eea',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            // Redirect to dashboard after alert closes
            window.location.href = '{{ route("dashboard") }}';
        });
        @endif

        // Add form submission handling for better UX
        document.addEventListener('DOMContentLoaded', function() {
        // Remember last role on page load, but only persist when returning from an error/failed submit
        const lastRole = localStorage.getItem('lastRole');
        const hasError = @json(session()->has('error') || session()->has('id_error'));
        const isInitialSelect = @json(!$show_student_form && !$show_login_form);
        if (lastRole === 'admin' && (hasError || !isInitialSelect)) {
            const userTypeForm = document.getElementById('userTypeForm');
            const adminForm = document.getElementById('adminLoginForm');
            const studentIdForm = document.getElementById('studentIdForm');
            const staffForm = document.getElementById('staffLoginForm');
            if (userTypeForm && adminForm && studentIdForm && staffForm) {
                userTypeForm.style.display = 'none';
                adminForm.style.display = 'block';
                studentIdForm.style.display = 'none';
                staffForm.style.display = 'none';
            }
            const desktopSel = document.querySelector('.desktop-user-select');
            if (desktopSel) desktopSel.style.display = 'none';
            const mobileBtn = document.querySelector('.mobile-student-btn');
            if (mobileBtn) mobileBtn.classList.remove('show-mobile');
            const emailEl = document.getElementById('admin_email');
            if (emailEl) emailEl.focus();
        } else if (isInitialSelect) {
            // Fresh visit: clear any leftover choice to show role selection
            localStorage.removeItem('lastRole');
        }

        // Custom validation for admin login
        const adminLoginForm = document.getElementById('adminLoginForm');
        if (adminLoginForm) {
            adminLoginForm.addEventListener('submit', function(e) {
                localStorage.setItem('lastRole', 'admin');
                const email = document.getElementById('admin_email').value.trim();
                const password = document.getElementById('admin_password').value.trim();
                const errorBox = document.getElementById('adminErrorBox');
                let errorMsg = '';
                if (!email && !password) {
                    errorMsg = 'Please enter your email and password.';
                } else if (!email) {
                    errorMsg = 'Please enter your email.';
                } else if (!password) {
                    errorMsg = 'Please enter your password.';
                }
                if (errorMsg) {
                    e.preventDefault();
                    errorBox.textContent = errorMsg;
                    errorBox.classList.remove('d-none');
                    // Keep admin form shown and focus the first invalid field
                    const firstInvalid = !email ? document.getElementById('admin_email') : document.getElementById('admin_password');
                    if (firstInvalid) firstInvalid.focus();
                } else {
                    errorBox.classList.add('d-none');
                }
            });
        }

        // Custom validation for student login   id="studentID"
        const studentID = document.getElementById('studentID');
        if (studentID) {
           studentID.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                const errorBox = document.getElementById('studentErrorBox');
                let errorMsg = '';
                if (!email && !password) {
                    errorMsg = 'Please enter your email and password.';
                } else if (!email) {
                    errorMsg = 'Please enter your email.';
                } else if (!password) {
                    errorMsg = 'Please enter your password.';
                }
                if (errorMsg) {
                    e.preventDefault();
                    errorBox.textContent = errorMsg;
                    errorBox.classList.remove('d-none');
                } else {
                    errorBox.classList.add('d-none');
                }
            });
        }
            // Clear all forms on page load
            clearAllForms();
        });

        window.adminOtpCooldown = @json($adminOtpCooldown);
window.adminOtpOverlayEnabled = @json($adminOtpOverlayEnabled);

        function handleUserTypeChange() {
            const userType = document.getElementById('user_type').value;
            const userTypeForm = document.getElementById('userTypeForm');
            const studentIdForm = document.getElementById('studentIdForm');
            const adminLoginForm = document.getElementById('adminLoginForm');
            const staffLoginForm = document.getElementById('staffLoginForm');

            // Clear all forms first
            clearAllForms();

            // Hide mobile button and desktop form when any option is selected
            document.querySelector('.mobile-student-btn').classList.remove('show-mobile');
            document.querySelector('.desktop-user-select').style.display = 'none';

            if (userType === 'student') {
                localStorage.setItem('lastRole', 'student-id');
                userTypeForm.style.display = 'none';
                studentIdForm.style.display = 'block';
                adminLoginForm.style.display = 'none';
                staffLoginForm.style.display = 'none';
                document.getElementById('school_id').focus();
            } else if (userType === 'admin') {
                localStorage.setItem('lastRole', 'admin');
                userTypeForm.style.display = 'none';
                adminLoginForm.style.display = 'block';
                staffLoginForm.style.display = 'none';
                studentIdForm.style.display = 'none';
                document.getElementById('admin_email').focus();
                if (adminOtpController) {
                    adminOtpController.prefillEmail('{{ $pending_admin_email }}');
                }
            } else if (userType === 'staff') {
                localStorage.setItem('lastRole', 'staff');
                userTypeForm.style.display = 'none';
                staffLoginForm.style.display = 'block';
                adminLoginForm.style.display = 'none';
                studentIdForm.style.display = 'none';
                document.getElementById('staff_email').focus();
            }
        }

        function clearAllForms() {
            // Preserve admin input on errors; only clear when explicitly resetting view
            const adminEmail = document.getElementById('admin_email');
            const adminPassword = document.getElementById('admin_password');
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], select');
            inputs.forEach(input => {
                if (adminEmail && input === adminEmail) return;
                if (adminPassword && input === adminPassword) return;
                input.value = '';
            });
            
            // Reset select dropdowns
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });
        }

        function resetForm() {
            localStorage.removeItem('lastRole');
            document.getElementById('userTypeForm').style.display = 'block';
            document.getElementById('studentIdForm').style.display = 'none';
            document.getElementById('adminLoginForm').style.display = 'none';
            document.getElementById('staffLoginForm').style.display = 'none';
            clearAllForms();
        }

        function resetStudentForm() {
            window.location.href = '{{ route("clear.student.verification") }}';
        }

        // Mobile Student Login Function
        function startMobileStudentLogin() {
            // Hide mobile button and desktop form
            document.querySelector('.mobile-student-btn').classList.remove('show-mobile');
            document.querySelector('.desktop-user-select').style.display = 'none';
            
            // Show student ID form
            document.getElementById('studentIdForm').style.display = 'block';
            document.getElementById('school_id').focus();
            
            // Clear any existing forms
            clearAllForms();
        }

        // Enhanced resetForm function to handle responsive behavior
        function resetForm() {
            // Check if we're on mobile or desktop
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                // Mobile: Show mobile button, hide desktop form
                document.querySelector('.mobile-student-btn').classList.add('show-mobile');
                document.querySelector('.desktop-user-select').style.display = 'none';
            } else {
                // Desktop: Show desktop form, hide mobile button
                document.querySelector('.mobile-student-btn').classList.remove('show-mobile');
                document.querySelector('.desktop-user-select').style.display = 'block';
                document.getElementById('userTypeForm').style.display = 'block';
            }
            
            // Hide all other forms
            document.getElementById('studentIdForm').style.display = 'none';
            document.getElementById('adminLoginForm').style.display = 'none';
            document.getElementById('staffLoginForm').style.display = 'none';
            clearAllForms();
        }

        // Handle window resize to maintain responsive behavior
        window.addEventListener('resize', function() {
            const isMobile = window.innerWidth <= 768;
            const mobileBtn = document.querySelector('.mobile-student-btn');
            const desktopForm = document.querySelector('.desktop-user-select');
            const studentIdForm = document.getElementById('studentIdForm');
            const adminForm = document.getElementById('adminLoginForm');
            const staffForm = document.getElementById('staffLoginForm');
            
            // Only adjust if we're on the initial selection screen (no forms are active)
            if (studentIdForm.style.display === 'none' && 
                adminForm.style.display === 'none' && 
                staffForm.style.display === 'none') {
                
                if (isMobile) {
                    mobileBtn.classList.add('show-mobile');
                    desktopForm.style.display = 'none';
                } else {
                    mobileBtn.classList.remove('show-mobile');
                    desktopForm.style.display = 'block';
                }
            } else {
                // If any form is active, ensure mobile button stays hidden
                mobileBtn.classList.remove('show-mobile');
            }
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert.classList.contains('fade')) {
                        alert.classList.remove('show');
                    }
                }, 5000);
            });
        });

        // Restrict School ID input to numbers and dash, and auto-format
        const schoolIdInput = document.getElementById('school_id');
        if (schoolIdInput) {
            schoolIdInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9-]/g, '');
                // Only allow one dash and only at position 5
                value = value.replace(/(.*)-(.*)-+/, '$1-$2');
                if (value.length > 9) value = value.substr(0, 9);
                // Auto-insert dash after 4 digits
                value = value.replace(/^(\d{4})(\d{0,4})$/, (m, p1, p2) => p2 ? p1 + '-' + p2 : p1);
                this.value = value;
            });
            schoolIdInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                // Allow only numbers and dash
                if (!/[0-9-]/.test(char) && e.which !== 8 && e.which !== 46) {
                    e.preventDefault();
                }
                // Prevent more than one dash
                if (char === '-' && this.value.includes('-')) {
                    e.preventDefault();
                }
                // Prevent dash at wrong position
                if (char === '-' && this.value.length !== 4) {
                    e.preventDefault();
                }
            });

            // Submit Student ID form when pressing Enter
            schoolIdInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const form = document.getElementById('studentIdForm');
                    const submitBtn = document.getElementById('verifyStudentIdBtn');
                    // Use HTML5 validation before submitting
                    if (form && (typeof form.reportValidity !== 'function' || form.reportValidity())) {
                        if (submitBtn) submitBtn.click();
                        else form.submit();
                    }
                }
            });
        }

        // Show spinner inside button on student ID form submit
        const studentIdForm = document.getElementById('studentIdForm');
        if (studentIdForm) {
            studentIdForm.addEventListener('submit', function(e) {
                const btn = document.getElementById('verifyStudentIdBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Verifying...';
                }
            });
        }

        // Force hover effects with JavaScript as backup
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('mouseenter', function() {
                    this.style.borderColor = '#28a745';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.15)';
                    this.style.transform = 'translateY(-1px)';
                });
                
                input.addEventListener('mouseleave', function() {
                    if (!this.matches(':focus')) {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                        this.style.transform = '';
                    }
                });
            });
        });

        // Admin Email Input Sanitization and Auto-complete
        const adminEmailInput = document.getElementById('admin_email');
        if (adminEmailInput) {
            adminEmailInput.setAttribute('maxlength', '50');
            adminEmailInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');
                // Only allow one @
                const atCount = (value.match(/@/g) || []).length;
                if (atCount > 1) value = value.replace(/@/g, (m, i) => i === value.indexOf('@') ? '@' : '');
                // Only allow . after @
                if (value.includes('@')) {
                    let parts = value.split('@');
                    if (parts[1].includes('.')) {
                        parts[1] = parts[1].replace(/[^a-zA-Z0-9.]/g, '');
                    } else {
                        parts[1] = parts[1].replace(/[^a-zA-Z0-9]/g, '');
                    }
                    value = parts.join('@');
                }
                // Auto-complete @gmail.com
                if (value.endsWith('@')) {
                    value += 'gmail.com';
                }
                // Limit to 50 chars
                if (value.length > 50) value = value.substring(0, 50);
                this.value = value;
            });
        }

        // Student Email Input Sanitization and Auto-complete
        const studentEmailInput = document.getElementById('email');
        if (studentEmailInput) {
            studentEmailInput.setAttribute('maxlength', '100');
            studentEmailInput.addEventListener('input', function(e) {
                // Allow only letters, numbers, dot, hyphen and underscore in the local part and @ and dot for the domain
                let value = this.value.replace(/[^A-Za-z0-9@._\-]/g, '');
                // Only allow one @
                const atCount = (value.match(/@/g) || []).length;
                if (atCount > 1) value = value.replace(/@/g, (m, i) => i === value.indexOf('@') ? '@' : '');
                // If there's a domain part, ensure it only contains letters and dots (no digits)
                if (value.includes('@')) {
                    let parts = value.split('@');
                    if (parts[1].includes('.')) {
                        parts[1] = parts[1].replace(/[^A-Za-z.]/g, '');
                    } else {
                        parts[1] = parts[1].replace(/[^A-Za-z0-9._\-]/g, '');
                    }
                    value = parts.join('@');
                }
                // Auto-complete @mcclawis.edu.ph when user types '@'
                if (value.endsWith('@')) {
                    value += 'mcclawis.edu.ph';
                }
                // Limit to 100 chars
                if (value.length > 100) value = value.substring(0, 100);
                this.value = value;
            });
        }

        // Initialize responsive behavior on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isMobile = window.innerWidth <= 768;
            const mobileBtn = document.querySelector('.mobile-student-btn');
            const desktopForm = document.querySelector('.desktop-user-select');
            
            if (isMobile) {
                mobileBtn.classList.add('show-mobile');
                desktopForm.style.display = 'none';
            } else {
                mobileBtn.classList.remove('show-mobile');
                desktopForm.style.display = 'block';
            }
        });

        // Mobile device detection
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                   window.innerWidth <= 768 ||
                   ('ontouchstart' in window);
        }

        // reCAPTCHA v3 Integration (v3 Only)
        @if(config('services.recaptcha.site_key_v3'))
        function executeRecaptchaV3(form, retryCount = 0) {
            return new Promise((resolve, reject) => {
                // Check if grecaptcha is loaded
                if (typeof grecaptcha === 'undefined') {
                    console.error('reCAPTCHA script not loaded');
                    reject(new Error('reCAPTCHA script not loaded. Please check your internet connection.'));
                    return;
                }

                const isMobile = isMobileDevice();
                const timeoutDuration = isMobile ? 20000 : 10000; // 20s for mobile, 10s for desktop
                console.log(`reCAPTCHA: Using ${timeoutDuration}ms timeout (${isMobile ? 'mobile' : 'desktop'})`);

                // Add timeout to prevent hanging
                const timeout = setTimeout(() => {
                    reject(new Error(`reCAPTCHA verification timeout (${isMobile ? 'mobile' : 'desktop'})`));
                }, timeoutDuration);

                grecaptcha.ready(function() {
                    console.log('reCAPTCHA ready, executing...');
                    grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', {action: 'login'})
                        .then(function(token) {
                            clearTimeout(timeout);
                            console.log('reCAPTCHA token received:', token.substring(0, 20) + '...');

                            // Add token to form
                            let tokenInput = form.querySelector('input[name="recaptcha_token"]');
                            if (!tokenInput) {
                                tokenInput = document.createElement('input');
                                tokenInput.type = 'hidden';
                                tokenInput.name = 'recaptcha_token';
                                form.appendChild(tokenInput);
                            }
                            tokenInput.value = token;
                            console.log('Token added to form successfully');
                            resolve();
                        })
                        .catch(function(error) {
                            clearTimeout(timeout);
                            console.error('reCAPTCHA v3 execution error:', error);

                            // Retry logic for mobile devices (max 2 retries)
                            if (isMobile && retryCount < 2) {
                                console.log(`reCAPTCHA failed on mobile, retrying... (attempt ${retryCount + 1}/2)`);
                                setTimeout(() => {
                                    executeRecaptchaV3(form, retryCount + 1)
                                        .then(resolve)
                                        .catch(reject);
                                }, 2000); // Wait 2 seconds before retry
                            } else {
                                // After all retries failed, provide fallback for mobile
                                if (isMobile) {
                                    console.warn('reCAPTCHA failed on mobile after retries, using fallback verification');
                                    // Update loading message to show fallback is being used
                                    const submitBtn = form.querySelector('button[type="submit"]');
                                    if (submitBtn) {
                                        submitBtn.innerHTML = '<i class="fas fa-shield-alt"></i> Using mobile verification...';
                                    }

                                    // Add a dummy token to indicate mobile fallback
                                    let tokenInput = form.querySelector('input[name="recaptcha_token"]');
                                    if (!tokenInput) {
                                        tokenInput = document.createElement('input');
                                        tokenInput.type = 'hidden';
                                        tokenInput.name = 'recaptcha_token';
                                        form.appendChild(tokenInput);
                                    }
                                    tokenInput.value = 'mobile-fallback-' + Date.now();
                                    resolve();
                                } else {
                                    reject(error);
                                }
                            }
                        });
                });
            });
        }
        @else
        // Fallback function when reCAPTCHA v3 is not configured
        function executeRecaptchaV3(form) {
            return new Promise((resolve) => {
                console.log('reCAPTCHA v3 not configured, proceeding without verification');
                resolve();
            });
        }
        @endif

        // Enhanced form submission with reCAPTCHA v3 and geolocation
        document.addEventListener('DOMContentLoaded', function() {
            const loginForms = document.querySelectorAll('form[action*="login"]');

            loginForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
                    submitBtn.disabled = true;

                    // Request fresh geolocation coordinates before submitting
                    loginGeolocationManager.request(true).then(() => {
                        // Show loading state for reCAPTCHA (device-specific message)
                        const isMobile = isMobileDevice();
                        const verifyingText = isMobile ? '<i class="fas fa-spinner fa-spin"></i> Verifying (mobile)...' : '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        submitBtn.innerHTML = verifyingText;

                        // Execute reCAPTCHA v3
                        executeRecaptchaV3(form)
                            .then(() => {
                                // Submit the form
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                                form.submit();
                            })
                            .catch((error) => {
                                console.error('reCAPTCHA verification failed:', error);
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;

                                // Show device-specific error message
                                const isMobile = isMobileDevice();
                                const errorTitle = 'Security Verification Failed';
                                let errorText = 'Please refresh the page and try again.';

                                if (isMobile) {
                                    errorText = 'Mobile verification failed. Please check your internet connection and try again. If the problem persists, try using a desktop browser.';
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: errorTitle,
                                    text: errorText,
                                    confirmButtonColor: '#667eea'
                                });
                            });
                    }).catch(() => {
                        // Geolocation failed, but continue with IP-based location
                        console.warn('Geolocation failed, proceeding with IP-based location');

                        // Show loading state for reCAPTCHA (device-specific message)
                        const isMobile = isMobileDevice();
                        const verifyingText = isMobile ? '<i class="fas fa-spinner fa-spin"></i> Verifying (mobile)...' : '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        submitBtn.innerHTML = verifyingText;

                        executeRecaptchaV3(form)
                            .then(() => {
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                                form.submit();
                            })
                            .catch((error) => {
                                console.error('reCAPTCHA verification failed:', error);
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;

                                // Show device-specific error message
                                const isMobile = isMobileDevice();
                                const errorTitle = 'Security Verification Failed';
                                let errorText = 'Please refresh the page and try again.';

                                if (isMobile) {
                                    errorText = 'Mobile verification failed. Please check your internet connection and try again. If the problem persists, try using a desktop browser.';
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: errorTitle,
                                    text: errorText,
                                    confirmButtonColor: '#667eea'
                                });
                            });
                    });
                });
            });
        });     

        const superLoginFooterLink = document.getElementById('superloginFooterLink');
        if (superLoginFooterLink) {
            let tapCount = 0;
            let tapTimer;
            const resetTapState = () => {
                tapCount = 0;
                if (tapTimer) {
                    clearTimeout(tapTimer);
                    tapTimer = null;
                }
            };
            const handleTap = (event) => {
                event.preventDefault();
                tapCount += 1;
                if (tapTimer) {
                    clearTimeout(tapTimer);
                }
                if (tapCount >= 3) {
                    resetTapState();
                    window.location.href = superLoginFooterLink.dataset.href;
                    return;
                }
                tapTimer = setTimeout(resetTapState, 600);
            };
            superLoginFooterLink.addEventListener('click', handleTap);
        }

        // Mobile App Download Modal - Shows on every domain load unless "Don't ask again" is checked
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('dontAskMobileAppAgain') !== 'true') {
                setTimeout(() => {
                    let selectedType = 'android';
                    
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-glass {
                            background: rgba(255, 255, 255, 0.25) !important;
                            backdrop-filter: blur(10px) !important;
                            -webkit-backdrop-filter: blur(10px) !important;
                            border: 1px solid rgba(255, 255, 255, 0.4) !important;
                            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1) !important;
                        }
                        .swal2-popup.swal2-glass {
                            position: fixed !important;
                            top: 50% !important;
                            left: 50% !important;
                            transform: translate(-50%, -50%) !important;
                            max-width: 340px !important;
                            width: 90% !important;
                            padding: 20px 24px !important;
                            max-height: 85vh !important;
                            overflow-y: auto !important;
                        }
                        .swal2-glass .swal2-title {
                            color: #2d3436 !important;
                            font-size: 1.2em !important;
                            font-weight: 700 !important;
                            margin-bottom: 12px !important;
                        }
                        .swal2-glass .swal2-html-container {
                            color: #2d3436 !important;
                            padding: 0 !important;
                        }
                        .swal2-glass .swal2-actions {
                            gap: 8px !important;
                        }
                        .swal2-glass .swal2-actions button {
                            font-size: 0.9em !important;
                            padding: 8px 16px !important;
                        }
                        .swal2-glass .swal2-footer {
                            margin-top: 15px !important;
                            text-align: center !important;
                        }
                    `;
                    document.head.appendChild(style);
                    
                    Swal.fire({
                        title: 'IPES Mobile Application',
                        html: `
                            <div style="text-align: left; padding: 20px 0;">
                                <p style="font-size: 1.1em; margin-bottom: 20px; color: #2d3436;">
                                    <strong>Available for testing</strong>
                                </p>
                                <div style="background: rgba(102, 126, 234, 0.1); backdrop-filter: blur(5px); padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid rgba(102, 126, 234, 0.2);">
                                    <label style="display: flex; align-items: center; margin-bottom: 15px; cursor: pointer;">
                                        <input type="radio" name="downloadType" value="android" checked style="margin-right: 10px; cursor: pointer; accent-color: #667eea;">
                                        <span style="font-weight: 500; color: #2d3436;">📱 Android APK</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="radio" name="downloadType" value="ios" style="margin-right: 10px; cursor: pointer; accent-color: #667eea;">
                                        <span style="font-weight: 500; color: #2d3436;">🍎 iOS IPA</span>
                                    </label>
                                </div>
                            </div>
                        `,
                        footer: `<label style="display: flex; align-items: center; cursor: pointer; color: #667eea; gap: 8px; justify-content: center;"><input type="checkbox" id="dontAskAgain" style="cursor: pointer; accent-color: #667eea;"><span style="font-size: 0.9em;">Don't ask again</span></label>`,
                        showCancelButton: true,
                        showCloseButton: true,
                        confirmButtonColor: '#667eea',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Download',
                        cancelButtonText: 'Cancel',
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        didOpen: (modal) => {
                            modal.classList.add('swal2-glass');
                            const radioButtons = modal.querySelectorAll('input[name="downloadType"]');
                            radioButtons.forEach(radio => {
                                radio.addEventListener('change', function() {
                                    selectedType = this.value;
                                });
                            });
                        }
                    }).then((result) => {
                        const dontAskCheckbox = Swal.getPopup().querySelector('#dontAskAgain');
                        if (dontAskCheckbox && dontAskCheckbox.checked) {
                            localStorage.setItem('dontAskMobileAppAgain', 'true');
                        }
                        
                        if (result.isConfirmed) {
                            let downloadUrl = '';
                            let appType = '';
                            
                            if (selectedType === 'android') {
                                downloadUrl = '{{ asset('apk/android/students_ipes.apk') }}';
                                appType = 'Android';
                            } else if (selectedType === 'ios') {
                                downloadUrl = '{{ asset('apk/ios/students_ipes.ipa') }}';
                                appType = 'iOS';
                            }
                            
                            fetch(downloadUrl, { method: 'HEAD' })
                                .then(response => {
                                    if (response.ok) {
                                        window.location.href = downloadUrl;
                                    } else {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Not Available',
                                            text: `application is not yet available for ${appType.toLowerCase()}`,
                                            confirmButtonColor: '#667eea'
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Not Available',
                                        text: `application is not yet available for ${appType.toLowerCase()}`,
                                        confirmButtonColor: '#667eea'
                                    });
                                });
                        }
                    });
                }, 800);
            }
        });

        // Password visibility toggle
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            if (this.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });

    </script>
  <script src="{{ asset('js/dev-tools-security.js') }}?v=<?php echo time(); ?>"></script>
</body>
</html>
