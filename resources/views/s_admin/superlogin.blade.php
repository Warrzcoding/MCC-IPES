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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        body {
            height: 100vh;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #050d1d;
            background-image: radial-gradient(circle at top, #173d7a, #07142a 55%, #050d1d 100%);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            overflow-x: hidden;
            overflow-y: hidden;
            position: relative;
            padding: clamp(24px, 8vw, 48px) clamp(18px, 6vw, 32px);
        }

        body::before {
            content: '';
            position: absolute;
            inset: -20%;
            background: url('{{ asset('images/mccicon.jpg') }}') center/320px 320px no-repeat;
            filter: blur(140px);
            opacity: 0.35;
            z-index: 0;
        }

        .background-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .background-glow::before,
        .background-glow::after {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(160px);
            opacity: 0.6;
        }

        .background-glow::before {
            background: rgba(31, 138, 255, 0.35);
            top: -80px;
            right: -110px;
        }

        .background-glow::after {
            background: rgba(40, 70, 160, 0.5);
            bottom: -100px;
            left: -120px;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: min(360px, 100%);
            margin: 0 auto;
        }

        .login-card {
            background: var(--card-bg);
            border-radius: 16px;
            backdrop-filter: blur(15px);
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 55px rgba(5, 15, 35, 0.45);
            padding: 26px 24px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(31, 138, 255, 0.22), rgba(9, 24, 52, 0.7));
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 10px 28px rgba(10, 28, 63, 0.32);
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-header h1 {
            font-size: 1.28rem;
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
            color: rgba(232, 241, 255, 0.78);
            margin-bottom: 4px;
            font-size: 0.78rem;
        }

        .form-control {
            background: rgba(12, 29, 60, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: var(--text-light);
            padding: 10px 14px;
            font-size: 0.88rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-light);
            box-shadow: 0 0 0 0.2rem rgba(31, 138, 255, 0.25);
            outline: none;
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: rgba(232, 241, 255, 0.4);
        }

        .forgot-password-container {
            text-align: center;
            margin-bottom: 18px;
        }

        .forgot-password-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.76rem;
            color: rgba(232, 241, 255, 0.6);
            text-decoration: none;
            letter-spacing: 0.04em;
            transition: color 0.3s ease;
        }

        .forgot-password-link i {
            font-size: 0.7rem;
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
                text-shadow: 0 0 10px rgba(96, 169, 255, 0.45);
                transform: translateY(-1px);
            }
            100% {
                text-shadow: 0 0 6px rgba(96, 169, 255, 0.3);
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: rgba(31, 138, 255, 0.85);
            border: 1px solid rgba(96, 169, 255, 0.65);
            border-radius: 12px;
            padding: 11px 14px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.03em;
            color: #f4f9ff;
            box-shadow: 0 12px 30px rgba(18, 98, 208, 0.32);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: rgba(31, 138, 255, 0.95);
            transform: translateY(-1px);
            box-shadow: 0 16px 36px rgba(18, 98, 208, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: rgba(232, 241, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 10px 16px;
            font-weight: 500;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            color: rgba(232, 241, 255, 0.7);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--accent-light);
        }

        .footer-note {
            margin-top: 28px;
            text-align: center;
            font-size: 0.82rem;
            color: rgba(232, 241, 255, 0.45);
            letter-spacing: 0.02em;
        }

        @media (max-width: 768px) {
            body {
                align-items: flex-start;
            }

            .login-container {
                width: 100%;
            }

            .background-glow::before,
            .background-glow::after {
                width: 320px;
                height: 320px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: clamp(24px, 12vw, 36px) clamp(16px, 8vw, 24px);
            }

            .login-card {
                padding: 28px clamp(18px, 8vw, 24px);
            }

            .brand-logo {
                width: 60px;
                height: 60px;
            }

            .brand-header h1 {
                font-size: 1.24rem;
            }

            .brand-header p {
                font-size: 0.85rem;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 10px 12px;
            }

            .btn-primary {
                font-size: 0.95rem;
                padding: 11px 14px;
            }

            .forgot-password-link {
                font-size: 0.78rem;
            }

            .back-link {
                font-size: 0.92rem;
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
                padding: 24px 14px;
            }

            .login-card {
                padding: 24px 16px;
            }

            .brand-header h1 {
                font-size: 1.15rem;
            }

            .brand-header p {
                font-size: 0.8rem;
            }

            .btn-primary {
                font-size: 0.9rem;
            }

            .forgot-password-link {
                font-size: 0.72rem;
            }

            #countdownDisplay {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="background-glow"></div>
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
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
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

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        placeholder="Enter secure password"
                        required
                    >
                </div>

                <div class="forgot-password-container">
                    <a href="{{ route('password.request') }}" class="forgot-password-link">
                        <i class="fas fa-question-circle"></i>
                        Forgot password?
                    </a>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span>Login</span>
                    </button>
                </div>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to main login
            </a>

        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        @if(session('login_success'))
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
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>