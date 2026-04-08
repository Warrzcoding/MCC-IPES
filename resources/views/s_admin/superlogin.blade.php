@php
    $error = session('error', '') ?? '';
    $status = session('status', '') ?? '';
    $loginFailed = session('login_failed', false);
    $attemptsLeft = session('attempts_left', 0);
    $accountLocked = session('account_locked', false);
    $lockedTime = session('locked_time', 0);
    $logoutSuccess = session('logout_success', false);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mccicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0c1d3c;
            --primary: #102a4f;
            --accent: #1f8aff;
            --accent-light: #60a9ff;
            --text-light: #e8f1ff;
            --card-bg: rgba(11, 21, 41, 0.8);
            --border-color: rgba(255, 255, 255, 0.08);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top, #173d7a, #07142a 55%, #050d1d 100%);
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            overflow: hidden;
            position: relative;
            padding: clamp(16px, 6vw, 32px);
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .login-card {
            width: 100%;
            background: var(--card-bg);
            border-radius: 14px;
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            box-shadow: 0 14px 36px rgba(5, 15, 35, 0.4);
            padding: 20px 18px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(31, 138, 255, 0.22), rgba(9, 24, 52, 0.7));
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 22px rgba(10, 28, 63, 0.3);
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-header h1 {
            font-size: 1.16rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            margin: 0;
            color: var(--text-light);
        }

        .brand-header p {
            color: rgba(232, 241, 255, 0.6);
            font-size: 0.85rem;
            margin: 6px 0 0;
        }

        .form-label {
            font-weight: 600;
            color: rgba(232, 241, 255, 0.8);
            margin-bottom: 3px;
            font-size: 0.78rem;
            letter-spacing: 0.012em;
        }

        .form-control {
            background: rgba(12, 29, 60, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 10px;
            color: var(--text-light);
            padding: 9px 12px;
            font-size: 0.82rem;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
        }

        .form-control:focus {
            border-color: var(--accent-light);
            box-shadow: 0 6px 14px rgba(31, 138, 255, 0.18);
            outline: none;
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: rgba(232, 241, 255, 0.5);
        }

        .input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 14px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: rgba(232, 241, 255, 0.48);
            font-size: 0.78rem;
        }

        .input-icon .form-control {
            padding-left: 34px;
        }

        .forgot-password-container {
            text-align: center;
            margin-bottom: 16px;
        }

        .forgot-password-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.72rem;
            color: rgba(232, 241, 255, 0.58);
            text-decoration: none;
            letter-spacing: 0.035em;
            transition: color 0.26s ease;
        }

        .forgot-password-link i {
            font-size: 0.65rem;
        }

        .forgot-password-link:hover,
        .forgot-password-link:focus {
            color: var(--accent-light);
            animation: linkGlow 0.6s ease forwards;
        }

        @keyframes linkGlow {
            0% {
                text-shadow: 0 0 0 rgba(96, 169, 255, 0);
                transform: translateY(0);
            }
            60% {
                text-shadow: 0 0 8px rgba(96, 169, 255, 0.42);
                transform: translateY(-1px);
            }
            100% {
                text-shadow: 0 0 4px rgba(96, 169, 255, 0.28);
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, rgba(31, 138, 255, 0.7), rgba(75, 170, 255, 0.76));
            border: 1px solid rgba(96, 169, 255, 0.5);
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 0.78rem;
            letter-spacing: 0.022em;
            color: #f4f9ff;
            box-shadow: 0 8px 18px rgba(18, 98, 208, 0.24);
            transition: transform 0.14s ease, box-shadow 0.14s ease, background 0.14s ease;
            display: inline-block;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(135deg, rgba(31, 138, 255, 0.82), rgba(75, 170, 255, 0.84));
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(18, 98, 208, 0.3);
        }

        .form-actions {
            margin-top: 0;
        }

        .exit-link {
            display: inline-block;
            margin-top: 14px;
            color: rgba(232, 241, 255, 0.6);
            text-decoration: none;
            font-size: 0.74rem;
            letter-spacing: 0.026em;
            transition: color 0.16s ease;
        }

        .exit-link:hover,
        .exit-link:focus {
            color: var(--accent-light);
        }

        .alert {
            border-radius: 14px;
            border: none;
            padding: 12px 16px;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.12);
            color: #ff6b81;
            border-left: 4px solid rgba(220, 53, 69, 0.45);
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.12);
            color: #6be6b2;
            border-left: 4px solid rgba(25, 135, 84, 0.45);
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .alert.fade-out {
            animation: fadeOut 0.5s ease forwards;
        }

        .footer-note {
            margin-top: 22px;
            text-align: center;
            font-size: 0.75rem;
            color: rgba(232, 241, 255, 0.4);
            letter-spacing: 0.02em;
        }

        @media (max-width: 768px) {
            body {
                padding: 24px 18px;
            }

            .login-container {
                width: min(300px, 100%);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 16px;
            }

            .login-card {
                padding: 18px 16px;
            }

            .brand-logo {
                width: 60px;
                height: 60px;
            }

            .brand-header h1 {
                font-size: 1.18rem;
            }

            .brand-header p {
                font-size: 0.85rem;
            }

            .form-control {
                font-size: 0.86rem;
                padding: 9px 12px;
            }

            .btn-primary {
                font-size: 0.9rem;
                padding: 10px 12px;
            }

            .forgot-password-link {
                font-size: 0.75rem;
            }

            .modal-dialog {
                margin: 0 18px;
            }

            #lockCountdownModal .modal-body {
                padding: 28px 20px !important;
            }

            #countdownDisplay {
                font-size: 30px;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 18px 14px;
            }

            .login-card {
                padding: 18px 16px;
            }

            .brand-header h1 {
                font-size: 1.15rem;
            }

            .brand-header p {
                font-size: 0.8rem;
            }

            .btn-primary {
                font-size: 0.88rem;
            }

            .forgot-password-link {
                font-size: 0.7rem;
            }

            #countdownDisplay {
                font-size: 26px;
            }
        }

        /* ==================== SWEETALERT SIZING ==================== */
        .swal2-popup {
            width: 320px !important;
            padding: 20px !important;
        }

        .swal2-title {
            font-size: 1.25rem !important;
            margin-bottom: 8px !important;
        }

        .swal2-html-container {
            font-size: 0.9rem !important;
            margin: 8px 0 !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 6px 16px !important;
            font-size: 0.85rem !important;
        }

        .swal2-actions {
            gap: 8px !important;
            margin-top: 12px !important;
        }

        /* ==================== OTP OVERLAY ==================== */
        .otp-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 13, 29, 0.85);
            backdrop-filter: blur(12px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }

        .otp-overlay.active {
            display: flex;
        }

        .otp-modal {
            width: 100%;
            max-width: 320px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .otp-modal-icon {
            font-size: 40px;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .otp-modal-title {
            color: var(--text-light);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .otp-modal-subtitle {
            color: rgba(232, 241, 255, 0.6);
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .otp-email-display {
            color: var(--accent-light);
            font-weight: 600;
        }

        .otp-input-group {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .otp-input-group input {
            width: 40px;
            height: 48px;
            background: rgba(12, 29, 60, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: var(--text-light);
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            transition: all 0.2s ease;
        }

        .otp-input-group input:focus {
            border-color: var(--accent-light);
            box-shadow: 0 0 10px rgba(31, 138, 255, 0.3);
            outline: none;
            background: rgba(12, 29, 60, 0.9);
        }

        .otp-input-group input.error {
            border-color: #ff6b81;
            box-shadow: 0 0 10px rgba(255, 107, 129, 0.2);
        }

        .otp-error-msg {
            color: #ff6b81;
            font-size: 0.8rem;
            margin-bottom: 15px;
            display: none;
        }

        .otp-error-msg.show {
            display: block;
        }

        .otp-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .otp-timer {
            font-size: 0.8rem;
            color: rgba(232, 241, 255, 0.5);
            margin-top: 15px;
        }

        #otpResendBtn {
            background: none;
            border: none;
            color: var(--accent-light);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: underline;
            padding: 0;
            cursor: pointer;
        }

        #otpResendBtn:disabled {
            color: rgba(232, 241, 255, 0.3);
            cursor: not-allowed;
            text-decoration: none;
        }

        #otpCancelBtn {
            background: none;
            border: none;
            color: rgba(232, 241, 255, 0.5);
            font-size: 0.8rem;
            margin-top: 10px;
            cursor: pointer;
        }

        #otpCancelBtn:hover {
            color: #ff6b81;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-header">
                <div class="brand-logo">
                    <img src="{{ asset('images/mccicon.jpg') }}" alt="MCC IPES Logo">
                </div>
                <h1>Super Administrator</h1>
                
            </div>

            @if ($errors->any() && !$loginFailed && !$accountLocked)
                <div id="validationErrorAlert" class="alert alert-danger" role="alert">
                    <strong><i class="fas fa-triangle-exclamation me-2"></i>Validation Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $errorMessage)
                            <li>{{ $errorMessage }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($status)
                <div class="alert alert-success" role="alert">
                    <strong><i class="fas fa-circle-check me-2"></i>{{ $status }}</strong>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.submit') }}" novalidate>
                @csrf
                <div class="input-wrapper">
                    <label for="email" class="form-label">Super Admin Email</label>
                    <div class="input-icon">
                        <i class="fas fa-user-shield"></i>
                        <input
                            id="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter super admin email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="input-wrapper">
                    <label for="password" class="form-label">Security Password</label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter secure password"
                            required
                        >
                    </div>
                </div>

                <div class="forgot-password-container">
                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                        <i class="fas fa-question-circle"></i>
                        Forgot password?
                    </a>
                </div>

                <div class="form-actions d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        Login
                    </button>
                </div>
            </form>

            <a href="{{ route('login') }}" class="exit-link" onclick="confirmExit(event)">
                Exit Super Admin
            </a>

        </div>
    </div>

    <!-- Access Code Verification Modal -->
    <div class="modal fade" id="accessCodeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="accessCodeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 320px; width: 90%; margin: auto;">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px;">
                <div class="modal-body text-center" style="padding: 35px 25px;">
                    <div style="font-size: 45px; margin-bottom: 18px; color: var(--accent);">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h5 class="modal-title mb-2" style="color: var(--text-light); font-weight: 700; font-size: 1.15rem;">Access Code</h5>
                    <p style="color: rgba(232, 241, 255, 0.6); margin-bottom: 18px; font-size: 0.88rem;">
                        Enter code to proceed
                    </p>
                    <div id="accessCodeTimer" style="font-size: 1.3rem; font-weight: 700; color: var(--accent); margin-bottom: 18px; font-family: 'Courier New', monospace;">
                        20s
                    </div>
                    <form id="accessCodeForm" style="margin-bottom: 0;">
                        <div class="input-wrapper" style="margin-bottom: 0;">
                            <input
                                type="text"
                                id="accessCodeInput"
                                class="form-control"
                                placeholder="Code"
                                required
                                maxlength="20"
                                pattern="[a-zA-Z0-9]*"
                                style="text-align: center; letter-spacing: 0.15em; font-size: 1rem; font-weight: 600; padding: 10px 12px;"
                            >
                        </div>
                        <div id="accessCodeError" style="color: #ff6b81; font-size: 0.8rem; margin-top: 10px; display: none;"></div>
                    </form>
                    <button type="button" id="verifyAccessCodeBtn" class="btn btn-primary" style="margin-top: 18px; width: 100%; padding: 8px 12px; font-size: 0.88rem;">
                        <i class="fas fa-arrow-right me-1"></i>Verify
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 480px) {
            .modal-dialog {
                margin: 1rem auto !important;
            }
            #accessCodeModal .modal-dialog {
                max-width: 85% !important;
                width: 85% !important;
            }
            #accessCodeModal .modal-body {
                padding: 30px 20px !important;
            }
            #accessCodeModal .modal-title {
                font-size: 1rem !important;
            }
            #accessCodeTimer {
                font-size: 1.2rem !important;
            }
        }

        @media (max-width: 360px) {
            #accessCodeModal .modal-body {
                padding: 25px 18px !important;
            }
            #accessCodeModal h5 {
                font-size: 1rem !important;
            }
            #accessCodeModal p {
                font-size: 0.8rem !important;
            }
            #accessCodeTimer {
                font-size: 1.1rem !important;
            }
        }
    </style>

    <!-- Countdown Modal for Account Lock -->
    <div class="modal fade" id="lockCountdownModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="lockCountdownLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px;">
                <div class="modal-body text-center" style="padding: 40px 30px;">
                    <div style="font-size: 48px; margin-bottom: 20px; color: #ff6b81;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h5 class="modal-title mb-3" style="color: var(--text-light); font-weight: 700;">Account Temporarily Locked</h5>
                    <p style="color: rgba(232, 241, 255, 0.7); margin-bottom: 24px;">
                        Too many failed login attempts. Please try again in:
                    </p>
                    <div id="countdownDisplay" style="font-size: 36px; font-weight: 700; color: var(--accent); margin-bottom: 30px; font-family: 'Courier New', monospace;">
                        1:00
                    </div>
                    <p style="color: rgba(232, 241, 255, 0.5); font-size: 0.9rem;">
                        Your account will be automatically unlocked after the timer expires.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div id="otpOverlay" class="otp-overlay">
        <div class="otp-modal">
            <div class="otp-modal-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h5 class="otp-modal-title">Verification Required</h5>
            <p class="otp-modal-subtitle">
                Please enter the 6-digit code sent to <br>
                <span id="otpEmailDisplay" class="otp-email-display"></span>
            </p>
            
            <div id="otpInputGroup" class="otp-input-group">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric">
            </div>

            <div id="otpErrorMsg" class="otp-error-msg"></div>

            <div class="otp-actions">
                <button type="button" id="verifyOtpBtn" class="btn btn-primary w-100">
                    Verify Code
                </button>
                
                <div class="otp-timer">
                    Didn't receive the code? 
                    <button type="button" id="otpResendBtn" disabled>Resend</button>
                </div>

                <button type="button" id="otpCancelBtn">
                    Cancel & Return
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        let accessCodeTimerInterval = null;
        let accessCodeModal = null;

        function startAccessCodeTimer() {
            let timeLeft = 20;
            const timerDisplay = document.getElementById('accessCodeTimer');

            if (accessCodeTimerInterval) {
                clearInterval(accessCodeTimerInterval);
            }

            if (timerDisplay) {
                timerDisplay.textContent = timeLeft + 's';

                accessCodeTimerInterval = setInterval(() => {
                    timeLeft--;
                    timerDisplay.textContent = timeLeft + 's';

                    if (timeLeft <= 0) {
                        clearInterval(accessCodeTimerInterval);
                        window.location.href = '{{ route("login") }}';
                    }
                }, 1000);
            }
        }

        // Super Admin OTP Controller
        const superAdminOtpController = (function() {
            const overlay = document.getElementById('otpOverlay');
            const inputs = Array.from(document.querySelectorAll('#otpInputGroup input'));
            const emailDisplay = document.getElementById('otpEmailDisplay');
            const verifyBtn = document.getElementById('verifyOtpBtn');
            const resendBtn = document.getElementById('otpResendBtn');
            const cancelBtn = document.getElementById('otpCancelBtn');
            const errorMsg = document.getElementById('otpErrorMsg');
            
            let resendTimer = null;
            let countdown = 60;

            function init() {
                if (!overlay) return;

                // Input handling
                inputs.forEach((input, index) => {
                    input.addEventListener('input', (e) => {
                        const value = e.target.value;
                        if (value && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                        updateVerifyBtnState();
                    });

                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Backspace' && !e.target.value && index > 0) {
                            inputs[index - 1].focus();
                        }
                        if (e.key === 'Enter' && getOtpValue().length === 6) {
                            verifyOtp();
                        }
                    });

                    input.addEventListener('keypress', (e) => {
                        if (!/[0-9]/.test(e.key)) {
                            e.preventDefault();
                        }
                    });
                });

                if (verifyBtn) verifyBtn.addEventListener('click', verifyOtp);
                if (resendBtn) resendBtn.addEventListener('click', resendOtp);
                if (cancelBtn) cancelBtn.addEventListener('click', cancelOtp);
            }

            function getOtpValue() {
                return inputs.map(input => input.value).join('');
            }

            function updateVerifyBtnState() {
                if (verifyBtn) verifyBtn.disabled = getOtpValue().length !== 6;
            }

            function showLoading(btn, text) {
                if (!btn) return;
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
            }

            function hideLoading(btn) {
                if (!btn) return;
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalText || 'Verify Code';
            }

            async function verifyOtp() {
                const code = getOtpValue();
                if (code.length !== 6) return;

                showLoading(verifyBtn, 'Verifying...');
                if (errorMsg) errorMsg.classList.remove('show');
                inputs.forEach(input => input.classList.remove('error'));

                try {
                    const response = await fetch('{{ route("superadmin.otp.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ otp_code: code })
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        // Success - Show SweetAlert and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Verification Successful',
                            text: 'Welcome back! Accessing Super Admin Portal...',
                            confirmButtonColor: '#1f8aff',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            background: 'rgba(11, 21, 41, 0.95)',
                            color: '#e8f1ff',
                            didOpen: function() {
                                const popup = Swal.getPopup();
                                if (popup) {
                                    popup.style.borderRadius = '16px';
                                    popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                                    popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                                }
                            }
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        if (errorMsg) {
                            errorMsg.textContent = data.message || 'Invalid verification code.';
                            errorMsg.classList.add('show');
                        }
                        inputs.forEach(input => input.classList.add('error'));
                        hideLoading(verifyBtn);
                    }
                } catch (error) {
                    console.error('OTP Verification Error:', error);
                    if (errorMsg) {
                        errorMsg.textContent = 'An error occurred. Please try again.';
                        errorMsg.classList.add('show');
                    }
                    hideLoading(verifyBtn);
                }
            }

            async function resendOtp() {
                if (!resendBtn || resendBtn.disabled) return;

                resendBtn.disabled = true;
                const originalText = resendBtn.innerHTML;
                resendBtn.innerHTML = 'Sending...';

                try {
                    const response = await fetch('{{ route("superadmin.otp.resend") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            title: 'Code Sent',
                            text: 'A new verification code has been sent to your email.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            background: 'rgba(11, 21, 41, 0.95)',
                            color: '#e8f1ff'
                        });
                        startTimer();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Failed to resend code.',
                            icon: 'error',
                            background: 'rgba(11, 21, 41, 0.95)',
                            color: '#e8f1ff'
                        });
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error('OTP Resend Error:', error);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = originalText;
                }
            }

            async function cancelOtp() {
                try {
                    await fetch('{{ route("superadmin.otp.cancel") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                } finally {
                    location.reload();
                }
            }

            function startTimer() {
                if (!resendBtn) return;
                countdown = 60;
                resendBtn.disabled = true;
                
                if (resendTimer) clearInterval(resendTimer);
                
                resendTimer = setInterval(() => {
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(resendTimer);
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = 'Resend';
                    } else {
                        resendBtn.innerHTML = `Resend in ${countdown}s`;
                    }
                }, 1000);
            }

            return {
                init: init,
                open: function(email) {
                    if (emailDisplay) emailDisplay.textContent = email;
                    if (overlay) overlay.classList.add('active');
                    if (inputs.length > 0) inputs[0].focus();
                    startTimer();
                }
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize access code modal
            const modalEl = document.getElementById('accessCodeModal');
            if (modalEl) {
                accessCodeModal = new bootstrap.Modal(modalEl);
                
                // Show modal if not verified AND no OTP is pending
                @if(!session('super_admin_otp_pending') && !session('temp_access_verified'))
                    modalEl.addEventListener('shown.bs.modal', function () {
                        startAccessCodeTimer();
                        const input = document.getElementById('accessCodeInput');
                        if (input) input.focus();
                    });
                    accessCodeModal.show();
                @endif
            }

            // Initialize OTP controller
            superAdminOtpController.init();

            @if(session('otp_sent'))
                superAdminOtpController.open('{{ session("email") }}');
            @elseif(session('super_admin_otp_pending') && isset($pendingEmail))
                superAdminOtpController.open('{{ $pendingEmail }}');
            @endif

            // Handle access code verification
            const verifyAccessBtn = document.getElementById('verifyAccessCodeBtn');
            if (verifyAccessBtn) {
                verifyAccessBtn.addEventListener('click', function() {
                    const accessCodeInput = document.getElementById('accessCodeInput');
                    const accessCode = accessCodeInput ? accessCodeInput.value.trim() : '';
                    const errorDiv = document.getElementById('accessCodeError');

                    if (!accessCode) {
                        if (errorDiv) {
                            errorDiv.textContent = 'Please enter an access code';
                            errorDiv.style.display = 'block';
                        }
                        return;
                    }

                    this.disabled = true;
                    const originalBtnText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';

                    fetch('{{ route("superadmin.verify-accesscode") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            accesscode: accessCode
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json().then(data => {
                                clearInterval(accessCodeTimerInterval);
                                if (accessCodeModal) {
                                    accessCodeModal.hide();
                                    // Robust cleanup of modal state
                                    setTimeout(() => {
                                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                                        document.body.classList.remove('modal-open');
                                        document.body.style.overflow = '';
                                        document.body.style.paddingRight = '';
                                    }, 300);
                                }
                                if (errorDiv) errorDiv.style.display = 'none';
                                if (accessCodeInput) accessCodeInput.value = '';
                            });
                        } else {
                            return response.json().then(data => {
                                if (errorDiv) {
                                    errorDiv.textContent = data.message || 'Invalid access code';
                                    errorDiv.style.display = 'block';
                                }
                                this.disabled = false;
                                this.innerHTML = originalBtnText;
                                startAccessCodeTimer();
                            });
                        }
                    })
                    .catch(error => {
                        if (errorDiv) {
                            errorDiv.textContent = 'An error occurred. Please try again.';
                            errorDiv.style.display = 'block';
                        }
                        this.disabled = false;
                        this.innerHTML = originalBtnText;
                        startAccessCodeTimer();
                    });
                });
            }

            // Validate access code input - allow only alphanumeric characters
            const accessCodeInputEl = document.getElementById('accessCodeInput');
            if (accessCodeInputEl) {
                accessCodeInputEl.addEventListener('input', function(event) {
                    this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
                });

                // Allow Enter key to verify access code
                accessCodeInputEl.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        const btn = document.getElementById('verifyAccessCodeBtn');
                        if (btn) btn.click();
                    }
                });
            }

            // Handle validation error alert timeout (5 seconds)
            const errorAlert = document.getElementById('validationErrorAlert');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.classList.add('fade-out');
                    setTimeout(() => {
                        errorAlert.remove();
                    }, 500); // Wait for animation to complete
                }, 5000); // 5 seconds timeout
            }

            // Handle logout success alert
            @if($logoutSuccess)
                Swal.fire({
                    title: 'Logout Successful',
                    text: 'You have been successfully logged out from the Super Admin Panel',
                    icon: 'success',
                    confirmButtonColor: '#1f8aff',
                    confirmButtonText: 'OK',
                    background: 'rgba(11, 21, 41, 0.95)',
                    color: '#e8f1ff',
                    allowOutsideClick: false,
                    didOpen: function() {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.borderRadius = '16px';
                            popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                            popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                        }
                    }
                });
            @endif

            // Handle account locked state
            @if($accountLocked)
                showLockCountdown({{ $lockedTime }});
            @endif

            // Handle login failed alert
            @if($loginFailed && $attemptsLeft > 0)
                Swal.fire({
                    title: 'Login Failed',
                    html: '<strong>Invalid credentials</strong><br><span style="font-size: 14px;">{{ $attemptsLeft }} attempt(s) remaining before account lock</span>',
                    icon: 'error',
                    confirmButtonColor: '#1f8aff',
                    confirmButtonText: 'Try Again',
                    background: 'rgba(11, 21, 41, 0.95)',
                    color: '#e8f1ff',
                    allowOutsideClick: false,
                    didOpen: function() {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.borderRadius = '16px';
                            popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                            popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                        }
                    }
                });
            @endif
        });

        function showLockCountdown(remainingSeconds) {
            // Show the modal
            const lockModal = new bootstrap.Modal(document.getElementById('lockCountdownModal'));
            lockModal.show();

            let remaining = remainingSeconds;

            // Update countdown every second
            const countdownInterval = setInterval(() => {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                const timeString = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                document.getElementById('countdownDisplay').textContent = timeString;

                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    // Auto-close modal and reload
                    lockModal.hide();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }

                remaining--;
            }, 1000);
        }

        // Handle successful login redirect
        @if(session('super_admin_login_success'))
            window.addEventListener('load', function() {
                Swal.fire({
                    title: 'Access Granted!',
                    text: 'Verification successful. Redirecting to Super Admin Portal...',
                    icon: 'success',
                    confirmButtonColor: '#1f8aff',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    background: 'rgba(11, 21, 41, 0.95)',
                    color: '#e8f1ff',
                    didOpen: function() {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.borderRadius = '16px';
                            popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                            popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                        }
                    }
                }).then(() => {
                    window.location.href = '{{ route("superadmin.home") }}';
                });
            });
        @elseif(session('login_success'))
            window.addEventListener('load', function() {
                Swal.fire({
                    title: 'Welcome Back!',
                    text: 'Login successful. Redirecting...',
                    icon: 'success',
                    confirmButtonColor: '#1f8aff',
                    background: 'rgba(11, 21, 41, 0.95)',
                    color: '#e8f1ff',
                    allowOutsideClick: false,
                    didOpen: function() {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.borderRadius = '16px';
                            popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                            popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                            popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                            popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                        }
                        // Auto redirect after short delay
                        setTimeout(() => {
                            window.location.href = '{{ route("superadmin.home") }}';
                        }, 1000);
                    }
                }).then(() => {
                    window.location.href = '{{ route("superadmin.home") }}';
                });
            });
        @endif

        function confirmExit(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Exit Super Admin Portal?',
                text: 'Are you sure you want to exit super admin secret portal?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Exit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                background: 'rgba(11, 21, 41, 0.95)',
                color: '#e8f1ff',
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                        popup.style.boxShadow = '0 20px 55px rgba(5, 15, 35, 0.45)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("login") }}';
                }
            });
        }

        // Custom styling for SweetAlert modals
        const style = document.createElement('style');
        style.textContent = `
            .swal2-popup {
                border-radius: 16px !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                box-shadow: 0 20px 55px rgba(5, 15, 35, 0.45) !important;
            }
            .swal2-title {
                font-weight: 700 !important;
                font-size: 1.5rem !important;
            }
            .swal2-html-container {
                font-size: 0.95rem !important;
            }
            .swal2-confirm {
                background-color: rgba(31, 138, 255, 0.85) !important;
                border: 1px solid rgba(96, 169, 255, 0.65) !important;
                border-radius: 12px !important;
                padding: 11px 24px !important;
                font-weight: 600 !important;
                box-shadow: 0 12px 30px rgba(18, 98, 208, 0.32) !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease !important;
            }
            .swal2-confirm:hover {
                background-color: rgba(31, 138, 255, 0.95) !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 16px 36px rgba(18, 98, 208, 0.4) !important;
            }
            .swal2-cancel {
                border-radius: 12px !important;
                padding: 11px 24px !important;
                font-weight: 600 !important;
                border: 1px solid rgba(108, 117, 125, 0.5) !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease !important;
            }
            .swal2-cancel:hover {
                transform: translateY(-1px) !important;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>