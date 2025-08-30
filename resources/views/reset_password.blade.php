<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Office Performance Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Preload school image for instant loading -->
    <link rel="preload" href="{{ asset('images/mainmcc.jpg') }}" as="image">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100dvh;
        }
        
        /* Enhanced animated background with school image */
        .bg-decorations {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            top: 0;
            left: 0;
            background: url('{{ asset('images/mainmcc.jpg') }}') center/cover no-repeat;
            filter: blur(5px);
            transform: scale(1.05);
            pointer-events: none;
        }

        .bg-decorations::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.4) 0%, rgba(118, 75, 162, 0.4) 50%, rgba(240, 147, 251, 0.3) 100%);
            z-index: 0;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            animation: float 10s linear infinite;
            pointer-events: none;
            z-index: 1;
            bottom: -120px;
        }

        .bubble:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-duration: 11s; animation-delay: 0s; }
        .bubble:nth-child(2) { width: 100px; height: 100px; left: 20%; animation-duration: 12s; animation-delay: 1s; }
        .bubble:nth-child(3) { width: 60px; height: 60px; left: 35%; animation-duration: 9s; animation-delay: 2s; }
        .bubble:nth-child(4) { width: 120px; height: 120px; left: 50%; animation-duration: 13s; animation-delay: 0.5s; }
        .bubble:nth-child(5) { width: 90px; height: 90px; left: 65%; animation-duration: 10s; animation-delay: 1.5s; }
        .bubble:nth-child(6) { width: 70px; height: 70px; left: 80%; animation-duration: 11.5s; animation-delay: 2.5s; }

        @keyframes float {
            0% { transform: translateY(0) scale(0.8); opacity: 0; }
            10% { opacity: 0.7; }
            50% { opacity: 0.9; }
            90% { opacity: 0.7; }
            100% { transform: translateY(-110vh) scale(1.1); opacity: 0; }
        }
        
        .reset-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 45px;
            max-width: 400px;
            width: 100%;
            transition: all 0.3s ease;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .reset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.2);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .reset-header .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        
        .reset-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        
        .reset-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .btn-primary, .btn-success, .btn-warning {
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
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
            padding: 15px 20px;
            margin-bottom: 20px;
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
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
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
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                width: 40px;
                height: 40px;
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
    <!--Bubbles bg-->
    <div class="bg-decorations">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="reset-card position-relative">
        <div class="reset-header" style="margin-top: 18px;">
            <div class="logo">
                <i class="fas fa-key"></i>
            </div>
            <h2>Reset Password</h2>
            <p>Verify your Microsoft 365 Account</p>
        </div>
        
        <!-- Step 1: Email Verification -->
        <div id="resetStep1">
            <form id="resetEmailForm" method="POST" action="{{ route('password.reset.send_verification') }}">
                @csrf
                <div class="mb-3 text-center">
                    <label for="ms365_email" class="form-label w-100 text-center">Microsoft 365 Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fab fa-microsoft text-primary"></i></span>
                        <input type="email" class="form-control" id="ms365_email" name="ms365_email" 
                               placeholder="firstname.lastname@mcclawis.edu.ph" required autofocus 
                               pattern="^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$" 
                               title="Email must be in the format firstname.lastname@mcclawis.edu.ph">
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
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary flex-fill" style="min-width: 0;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </a>
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
                
                <button type="submit" class="btn btn-warning" id="resetPasswordBtn">
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
        <div class="d-flex justify-content-center mt-3" id="backToLoginContainer">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary" style="min-width: 140px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailForm = document.getElementById('resetEmailForm');
            const otpForm = document.getElementById('resetOtpForm');
            const passwordForm = document.getElementById('resetPasswordForm');
            const step1 = document.getElementById('resetStep1');
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

            // Step 1: Send verification email (Temporary bypass for testing)
            if(emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = document.getElementById('ms365_email').value;
                    
                    // Show loading state
                    sendVerificationBtn.disabled = true;
                    sendVerificationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                    
                    // Actual AJAX implementation
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
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
                                title: 'Email Not Found',
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
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Code Resent!',
                                text: 'A new verification code has been sent. You have 5 minutes to enter it.',
                                confirmButtonColor: '#667eea'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
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
                passwordInput.addEventListener('input', updateStrengthMeter);
                passwordInput.addEventListener('input', updatePasswordMatch);
            }
            if (confirmInput) {
                confirmInput.addEventListener('input', updatePasswordMatch);
            }
        });
    </script>
     <script src="{{ asset('js/dev-tools-security.js') }}"></script>
</body>
</html> 