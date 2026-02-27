<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Office Performance Evaluation System</title>
     <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Preload school image for instant loading -->
    <link rel="preload" href="{{ asset('images/mainmcc.jpg') }}" as="image">
    
    <!-- reCAPTCHA v3 Scripts -->
    @if(config('services.recaptcha.site_key_v3'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key_v3') }}" async defer></script>
    @endif
    
    <style>
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
    </style>
    <style>
        body {
            background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100dvh;
            position: relative;
            perspective: 1000px;
            overflow: hidden;
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
            transform-style: preserve-3d;
        }
        .cube {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 5px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25), inset 0 0 10px rgba(255,255,255,0.25);
            animation: floatCube 18s infinite ease-in-out;
            transform: translateZ(-200px) rotateX(15deg) rotateY(20deg);
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






        
        .reset-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 36px;
            max-width: 320px;
            width: 80%;
            transition: all 0.3s ease;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .reset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.2);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .reset-header .logo {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at 30% 20%, #ffffff 0%, #f3f5ff 45%, #e3e7ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.6rem;
            color: white;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        
        .reset-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1.2rem;
        }
        
        .reset-header p {
            color: #666;
            font-size: 12px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.45rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .btn-primary, .btn-success, .btn-warning {
            border-radius: 15px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 12px 16px;
            margin-bottom: 16px;
            animation: fadeIn 0.5s ease;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%);
            color: white;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
            color: white;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }
        
        .btn-outline-primary, .btn-outline-secondary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            text-decoration: none;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
            text-decoration: none;
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
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 15px 0 0 15px;
            color: #667eea;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }
        
        @media (max-width: 767.98px) {
            body {
                padding: 0;
                min-height: 100dvh;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
                position: relative;
                display: flex;
                height: 100dvh;
            }
            
            .reset-card {
                padding: 5vw 2vw 5vw 2vw;
                max-width: 340px;
                min-width: 0;
                width: 98vw;
                border-radius: 16px;
                box-shadow: 0 6px 24px rgba(102,126,234,0.13), 0 1.5px 8px rgba(0,0,0,0.08);
                margin: 0 auto;
                border: 1.5px solid #e9ecef;
                background: rgba(255,255,255,0.97);
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .reset-header {
                margin-bottom: 10px;
                padding-top: 8px;
            }
            
            .reset-header .logo {
                width: 80px;
                height: 80px;
                font-size: 1rem;
                margin-bottom: 5px;
                box-shadow: 0 2px 8px rgba(102,126,234,0.13);
            }
            
            .reset-header h2 {
                font-size: 1rem;
                margin-bottom: 3px;
            }
            
            .alert {
                font-size: 14px !important;
                padding: 10px 15px !important;
            }
            
            .btn-outline-primary, .btn-outline-secondary {
                font-size: 12px !important;
                padding: 10px 12px !important;
            }
            
            /* OTP input mobile styling */
            #otp_code {
                max-width: 180px !important;
                font-size: 1em !important;
            }

            /* Ensure back button stays left-aligned on mobile */
            #backToLoginContainer {
                align-self: flex-start;
            }
        }
        .password-strength {
            margin-top: 6px;
            margin-bottom: 2px;
            height: 7px;
            width: 100%;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }
        .password-strength-text {
            font-size: 0.92rem;
            margin-top: 2px;
            font-weight: 600;
        }
        .password-suggestion {
            font-size: 0.85rem;
            color: #b71c1c;
            margin-top: 2px;
        }
        .password-match-indicator {
            font-size: 0.92rem;
            margin-top: 4px;
            font-weight: 600;
            min-height: 18px;
        }
        .password-match-indicator.match {
            color: #51cf66;
        }
        .password-match-indicator.mismatch {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#667eea'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#667eea'
                });
            @endif
        });
    </script>
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
    <div class="reset-card position-relative">
        <div class="reset-header" style="margin-top: 18px;">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h2>Reset Password</h2>
            <p>Verify your Account</p>
        </div>
        
        <!-- Step 1: Email Verification -->
        <div id="resetStep1">
            <form id="resetEmailForm" method="POST" action="{{ route('password.reset.send_verification') }}">
                @csrf
                <div class="mb-3 text-center">
                    <label for="ms365_email" class="form-label w-100 text-center">MS Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-envelope text-primary"></i></span>
                        <input type="email" class="form-control" id="ms365_email" name="ms365_email"
                               placeholder="your.msemail@mcclawis.edu.ph" required autofocus
                               inputmode="email"
                               autocomplete="email">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="sendVerificationBtn">
                    <i class="fas fa-paper-plane"></i> Send Verification Code
                </button>
            </form>
        </div>
        
        <!-- Step 2: OTP Verification -->
        <div id="resetStep2" style="display:none;">
            <form id="resetOtpForm" method="POST" action="{{ route('password.reset.verify_otp') }}">
                @csrf
                <input type="hidden" name="ms365_email" id="otp_email">
                
                <!-- OTP Timer Display -->
                <div class="mb-3 text-center">
                    <div class="alert alert-info" id="otpTimerAlert">
                        <i class="fas fa-clock"></i>
                        <span id="otpTimerText">Time remaining: <span id="otpCountdown">05:00</span></span>
                    </div>
                </div>
                
                <div class="mb-3 text-center">
                    <label for="otp_code" class="form-label">Enter the code sent to your Outlook email</label>
                    <div class="d-flex justify-content-center">
                        <input type="text" class="form-control text-center" id="otp_code" name="otp_code" 
                               maxlength="6" required placeholder="Enter 6-digit code"
                               style="letter-spacing: 0.3em; font-size: 1.1em; font-weight: 600; max-width: 200px;">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success mb-3" id="verifyOtpBtn">
                    <i class="fas fa-check"></i> Verify Code
                </button>
                
                <!-- Horizontal button layout -->
                <div class="d-flex justify-content-between gap-2 mt-2">
                   
                    <button type="button" class="btn btn-outline-primary flex-fill" id="resendOtpBtn" style="display:none; min-width: 0;">
                        <i class="fas fa-redo me-1"></i> Resend Code
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Step 3: New Password -->
        <div id="resetStep3" style="display:none;">
            <form id="resetPasswordForm" method="POST" action="{{ route('password.reset.update') }}">
                @csrf
                <input type="hidden" name="ms365_email" id="password_email">
                <input type="hidden" name="otp_code" id="password_otp">
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-lock text-primary"></i></span>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               required minlength="8" placeholder="Enter new password">
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                    <div class="password-suggestion" id="passwordSuggestion" style="display:none;">Use at least 8 characters, mix of letters, numbers, and symbols.</div>
                    <div id="passwordRequirements" class="form-text mt-1" style="display:none;">
                        <ul class="mb-0 small">
                            <li id="req-length" class="text-danger">At least 8 characters</li>
                            <li id="req-upper" class="text-danger">At least 1 uppercase letter</li>
                            <li id="req-lower" class="text-danger">At least 1 lowercase letter</li>
                            <li id="req-number" class="text-danger">At least 1 number</li>
                            <li id="req-symbol" class="text-danger">At least 1 symbol</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-lock text-primary"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               required minlength="8" placeholder="Confirm new password">
                    </div>
                    <div id="passwordMatchIndicator" class="password-match-indicator"></div>
                </div>
                
                <button type="submit" class="btn btn-success" id="resetPasswordBtn">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>
        </div>
        
        <!-- Step 4: Success Message -->
        <div id="resetStep4" style="display:none;">
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h5>Password Reset Successful!</h5>
                <p>Your password has been updated successfully. You can now login with your new password.</p>
            </div>
          
        </div>
        
             <!-- Back to Login button for steps 1, 3, and 4 -->
               <div id="backToLoginContainer" style="text-align: left; margin-top: 24px;">
            <a href="{{ route('login') }}" class="btn-back-icon" aria-label="Back to Login">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailForm = document.getElementById('resetEmailForm');
            const otpForm = document.getElementById('resetOtpForm');
            const passwordForm = document.getElementById('resetPasswordForm');
            const step1 = document.getElementById('resetStep1');
            const emailInput = document.getElementById('ms365_email');
            const allowedEmailPattern = /^[a-zA-Z0-9._%+-]+@mcclawis\.edu\.ph$/i;
            const step2 = document.getElementById('resetStep2');
            const step3 = document.getElementById('resetStep3');
            const step4 = document.getElementById('resetStep4');
            const otpEmail = document.getElementById('otp_email');
            const passwordEmail = document.getElementById('password_email');
            const passwordOtp = document.getElementById('password_otp');
            const sendVerificationBtn = document.getElementById('sendVerificationBtn');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const resetPasswordBtn = document.getElementById('resetPasswordBtn');
            const resendOtpBtn = document.getElementById('resendOtpBtn');
            const otpTimerAlert = document.getElementById('otpTimerAlert');
            const otpCountdown = document.getElementById('otpCountdown');
            const backToLoginContainer = document.getElementById('backToLoginContainer');
            
            // OTP Timer variables
            let otpTimer;
            let otpTimeLeft = 300; // 5 minutes in seconds
            const OTP_TIMEOUT = 300; // 5 minutes in seconds
            
            // Function to manage back button visibility
            function updateBackButtonVisibility() {
                if (step2.style.display !== 'none') {
                    // Step 2 (OTP) - hide the separate back button since it's integrated
                    backToLoginContainer.style.display = 'none';
                } else {
                    // Step 1, 3, or 4 - show the separate back button
                    backToLoginContainer.style.display = 'flex';
                }
            }
            
            // Initialize back button visibility on page load
            updateBackButtonVisibility();

            // Timer functions
            function startOtpTimer() {
                otpTimeLeft = OTP_TIMEOUT;
                updateOtpTimerDisplay();
                
                otpTimer = setInterval(() => {
                    otpTimeLeft--;
                    updateOtpTimerDisplay();
                    
                    if (otpTimeLeft <= 0) {
                        clearInterval(otpTimer);
                        otpTimeExpired();
                    }
                }, 1000);
            }
            
            function updateOtpTimerDisplay() {
                const minutes = Math.floor(otpTimeLeft / 60);
                const seconds = otpTimeLeft % 60;
                const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                otpCountdown.textContent = timeString;
                
                // Change alert color based on time remaining
                if (otpTimeLeft <= 60) { // Last minute - red
                    otpTimerAlert.className = 'alert alert-danger';
                    otpTimerAlert.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">' + timeString + '</span></span>';
                    // Show resend button in last minute
                    resendOtpBtn.style.display = 'block';
                } else if (otpTimeLeft <= 120) { // Last 2 minutes - orange
                    otpTimerAlert.className = 'alert alert-warning';
                    otpTimerAlert.innerHTML = '<i class="fas fa-clock"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">' + timeString + '</span></span>';
                } else { // Normal - blue
                    otpTimerAlert.className = 'alert alert-info';
                    otpTimerAlert.innerHTML = '<i class="fas fa-clock"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">' + timeString + '</span></span>';
                }
            }
            
            function otpTimeExpired() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Time Expired',
                    text: 'The verification code has expired. Please request a new one.',
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    // Return to email verification step
                    step2.style.display = 'none';
                    step1.style.display = 'block';
                    document.getElementById('otp_code').value = '';
                    resendOtpBtn.style.display = 'none';
                    updateBackButtonVisibility();
                });
            }
            
            function stopOtpTimer() {
                if (otpTimer) {
                    clearInterval(otpTimer);
                }
            }



            function enforceEmailPattern(event) {
                const input = event.target;
                // Allow only letters, numbers, dot, at, and ñ/Ñ
                let value = input.value.replace(/[^a-zA-Z0-9.@ñÑ]/g, '');
                input.value = value;

                if (value && !allowedEmailPattern.test(value)) {
                    input.setCustomValidity('Email must use @mcclawis.edu.ph format');
                } else {
                    input.setCustomValidity('');
                }
            }

            function validateEmailBeforeSubmit(inputElement) {
                const value = inputElement.value.trim();
                if (!allowedEmailPattern.test(value)) {
                    inputElement.setCustomValidity('Email must use @mcclawis.edu.ph format');
                    inputElement.reportValidity();
                    return false;
                }
                inputElement.setCustomValidity('');
                return true;
            }

            if (emailInput) {
                emailInput.addEventListener('input', enforceEmailPattern, { passive: true });
                
                emailInput.addEventListener('input', function() {
                    const value = this.value;
                    const atIndex = value.indexOf('@');
                    
                    if (atIndex !== -1) {
                        const beforeAt = value.substring(0, atIndex);
                        this.value = beforeAt + '@mcclawis.edu.ph';
                    }
                });

                // Prevent spaces
                emailInput.addEventListener('keydown', function(e) {
                    if (e.key === ' ') {
                        e.preventDefault();
                    }
                });
            }

            // reCAPTCHA v3 Integration
            @if(config('services.recaptcha.site_key_v3'))
            function executeRecaptchaV3(form, action) {
                return new Promise((resolve, reject) => {
                    if (typeof grecaptcha === 'undefined') {
                        reject(new Error('reCAPTCHA not loaded'));
                        return;
                    }
                    const timeout = setTimeout(() => reject(new Error('reCAPTCHA timeout')), 10000);
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', {action: action})
                            .then(function(token) {
                                clearTimeout(timeout);
                                let tokenInput = form.querySelector('input[name="recaptcha_token"]');
                                if (!tokenInput) {
                                    tokenInput = document.createElement('input');
                                    tokenInput.type = 'hidden';
                                    tokenInput.name = 'recaptcha_token';
                                    form.appendChild(tokenInput);
                                }
                                tokenInput.value = token;
                                resolve();
                            })
                            .catch(function(error) {
                                clearTimeout(timeout);
                                reject(error);
                            });
                    });
                });
            }
            @else
            function executeRecaptchaV3(form, action) {
                return Promise.resolve();
            }
            @endif

            // Step 1: Send verification email
            if(emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!validateEmailBeforeSubmit(emailInput)) {
                        return;
                    }
                    const email = document.getElementById('ms365_email').value;
                    
                    // Show loading state
                    sendVerificationBtn.disabled = true;
                    sendVerificationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                    
                    // Execute reCAPTCHA first
                    executeRecaptchaV3(emailForm, 'reset_password')
                        .then(() => {
                            sendVerificationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                            
                            // Actual AJAX implementation
                            return fetch(emailForm.action, {
                                method: 'POST',
                                body: new FormData(emailForm),
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                        })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if(data.status === 'success') {
                            step1.style.display = 'none';
                            step2.style.display = 'block';
                            otpEmail.value = email;
                            updateBackButtonVisibility();
                            
                            // Start OTP timer
                            startOtpTimer();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Verification Code Sent!',
                                text: 'Please check your Outlook email for the verification code. You have 5 minutes to enter the code.',
                                confirmButtonColor: '#667eea'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.title || 'Action Failed',
                                text: data.message || 'Failed to send verification code.',
                                confirmButtonColor: '#667eea'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonColor: '#667eea'
                        });
                    })
                    .finally(() => {
                        sendVerificationBtn.disabled = false;
                        sendVerificationBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Verification Code';
                    });
                });
            }

            // Resend OTP functionality
            if(resendOtpBtn) {
                resendOtpBtn.addEventListener('click', function() {
                    const email = otpEmail.value;
                    
                    // Show loading state
                    resendOtpBtn.disabled = true;
                    resendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resending...';
                    
                    // Create form data for resend request
                    const formData = new FormData();
                    formData.append('ms365_email', email);
                    
                    fetch('{{ route("password.reset.send_verification") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            // Restart timer
                            startOtpTimer();
                            
                            // Clear OTP input
                            document.getElementById('otp_code').value = '';
                            
                            // Hide resend button
                            resendOtpBtn.style.display = 'none';
                            resendOtpBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Resend Code';
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Code Resent!',
                                text: 'A new verification code has been sent. You have 5 minutes to enter it.',
                                confirmButtonColor: '#667eea'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.title || 'Resend Failed',
                                text: data.message || 'Failed to resend verification code.',
                                confirmButtonColor: '#667eea'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonColor: '#667eea'
                        });
                    })
                    .finally(() => {
                        resendOtpBtn.disabled = false;
                        resendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
                    });
                });
            }

            // Step 2: Verify OTP
            if(otpForm) {
                otpForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const otp = document.getElementById('otp_code').value;
                    
                    // Show loading state
                    verifyOtpBtn.disabled = true;
                    verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                    
                    // Send AJAX request to verify OTP
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // OTP is correct - proceed to password reset
                            stopOtpTimer();
                            
                            step2.style.display = 'none';
                            step3.style.display = 'block';
                            passwordEmail.value = otpEmail.value;
                            passwordOtp.value = otp;
                            updateBackButtonVisibility();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Verified!',
                                text: 'Please enter your new password.',
                                confirmButtonColor: '#667eea'
                            });
                        } else {
                            // OTP is incorrect - show error
                            Swal.fire({
                                icon: 'error',
                                title: 'Incorrect OTP',
                                text: data.message,
                                confirmButtonColor: '#667eea',
                                confirmButtonText: 'Try Again'
                            });
                            
                            // Clear the OTP input and focus it
                            document.getElementById('otp_code').value = '';
                            document.getElementById('otp_code').focus();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Error',
                            text: 'Unable to verify OTP. Please check your connection and try again.',
                            confirmButtonColor: '#667eea'
                        });
                    })
                    .finally(() => {
                        verifyOtpBtn.disabled = false;
                        verifyOtpBtn.innerHTML = '<i class="fas fa-check"></i> Verify Code';
                    });
                });
            }

            // Step 3: Reset Password (Temporary bypass for testing)
            if(passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    const msEmail = passwordEmail.value;

                    if(newPassword !== confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Mismatch',
                            text: 'New password and confirm password do not match.',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }

                    if(newPassword.length < 8) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Too Short',
                            text: 'Password must be at least 8 characters long.',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }

                    resetPasswordBtn.disabled = true;
                    resetPasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';

                    // Build form data with only the required fields
                    const formData = new FormData();
                    formData.append('ms365_email', msEmail);
                    formData.append('new_password', newPassword);
                    formData.append('confirm_password', confirmPassword);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            step3.style.display = 'none';
                            step4.style.display = 'block';
                            updateBackButtonVisibility();
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Reset Successful!',
                                text: 'Your password has been updated successfully.',
                                confirmButtonColor: '#667eea',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to reset password.',
                                confirmButtonColor: '#667eea'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonColor: '#667eea'
                        });
                    })
                    .finally(() => {
                        resetPasswordBtn.disabled = false;
                        resetPasswordBtn.innerHTML = '<i class="fas fa-save"></i> Reset Password';
                    });
                });
            }

            // OTP input formatting
            const otpInput = document.getElementById('otp_code');
            if(otpInput) {
                otpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
                });
            }

            // Password strength and match logic for Step 3
            const passwordInput = document.getElementById('new_password');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            const suggestion = document.getElementById('passwordSuggestion');
            const confirmInput = document.getElementById('confirm_password');
            const matchIndicator = document.getElementById('passwordMatchIndicator');
            const reqBox = document.getElementById('passwordRequirements');
            const reqs = {
                length: document.getElementById('req-length'),
                upper: document.getElementById('req-upper'),
                lower: document.getElementById('req-lower'),
                number: document.getElementById('req-number'),
                symbol: document.getElementById('req-symbol')
            };

            function checkPasswordStrength(pw) {
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[A-Z]/.test(pw)) score++;
                if (/[a-z]/.test(pw)) score++;
                if (/[0-9]/.test(pw)) score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;
                if (pw.length >= 12) score++;
                return score;
            }
            function updateRequirements(pw) {
                const checks = {
                    length: pw.length >= 8,
                    upper: /[A-Z]/.test(pw),
                    lower: /[a-z]/.test(pw),
                    number: /[0-9]/.test(pw),
                    symbol: /[^A-Za-z0-9]/.test(pw)
                };
                let allOk = true;
                Object.entries(checks).forEach(([key, ok]) => {
                    if (reqs[key]) {
                        reqs[key].classList.toggle('text-success', ok);
                        reqs[key].classList.toggle('text-danger', !ok);
                    }
                    if (!ok) allOk = false;
                });
                if (reqBox) {
                    if (!pw) {
                        reqBox.style.display = 'none';
                    } else {
                        reqBox.style.display = allOk ? 'none' : '';
                    }
                }
            }
            function updateStrengthMeter() {
                if (!passwordInput) return;
                const pw = passwordInput.value;
                const score = checkPasswordStrength(pw);
                let width = '0%';
                let color = '#e9ecef';
                let text = '';
                if (!pw) {
                    strengthBar.style.width = width;
                    strengthBar.style.background = color;
                    strengthText.textContent = '';
                    suggestion.style.display = 'none';
                    return;
                }
                if (score <= 2) {
                    width = '33%';
                    color = '#ff6b6b';
                    text = 'Weak';
                    suggestion.style.display = '';
                } else if (score <= 4) {
                    width = '66%';
                    color = '#ffd43b';
                    text = 'Medium';
                    suggestion.style.display = 'none';
                } else {
                    width = '100%';
                    color = '#51cf66';
                    text = 'Strong';
                    suggestion.style.display = 'none';
                }
                strengthBar.style.width = width;
                strengthBar.style.background = color;
                strengthText.textContent = text;
                strengthText.style.color = color;
            }
            function updatePasswordMatch() {
                if (!passwordInput || !confirmInput) return;
                const pw = passwordInput.value;
                const confirm = confirmInput.value;
                if (!confirm) {
                    matchIndicator.textContent = '';
                    matchIndicator.className = 'password-match-indicator';
                    return;
                }
                if (pw === confirm) {
                    matchIndicator.textContent = 'Passwords match';
                    matchIndicator.className = 'password-match-indicator match';
                } else {
                    matchIndicator.textContent = 'Passwords do not match';
                    matchIndicator.className = 'password-match-indicator mismatch';
                }
            }
            if (passwordInput) {
                passwordInput.addEventListener('focus', () => {
                    if (reqBox && passwordInput.value) reqBox.style.display = '';
                });
                passwordInput.addEventListener('blur', () => {
                    if (reqBox) reqBox.style.display = 'none';
                });
                passwordInput.addEventListener('input', () => {
                    updateStrengthMeter();
                    updatePasswordMatch();
                    updateRequirements(passwordInput.value);
                });
            }
            if (confirmInput) {
                confirmInput.addEventListener('input', updatePasswordMatch);
            }
        });
    </script>
     <script src="{{ asset('js/dev-tools-security.js') }}"></script>
</body>
</html> 