<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pre-Sign Up - Office Performance Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Enhanced preloading for instant background loading -->
    <link rel="preload" href="{{ asset('images/mainmcc.jpg') }}" as="image" crossorigin>
    <link rel="prefetch" href="{{ asset('images/mainmcc.jpg') }}">
    
    <!-- DNS prefetch for faster external resource loading -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Force immediate image loading -->
    <script>
        // Preload image immediately to ensure it's cached
        const img = new Image();
        img.src = '{{ asset('images/mainmcc.jpg') }}';
        img.onload = function() {
            document.body.classList.add('image-loaded');
        };
        
        // Additional prefetch
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = '{{ asset('images/mainmcc.jpg') }}';
        document.head.appendChild(link);
    </script>
    <style>
        body {
            background: 
                linear-gradient(135deg, rgba(102, 126, 234, 0.4) 0%, rgba(118, 75, 162, 0.4) 50%, rgba(240, 147, 251, 0.3) 100%),
                url('{{ asset('images/mainmcc.jpg') }}') center/cover no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* For mobile vertical centering */
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
        .signup-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 45px;
            max-width: 400px;
            width: 100%;
            transition: all 0.3s ease;
            margin: 0 auto;
        }
        .signup-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.2);
        }
        .signup-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .signup-header .logo {
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
        .signup-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        .signup-header p {
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
        .form-control.valid {
            border-color: #28a745 !important;
            background-color: #f8fff9 !important;
        }
        .form-control.invalid {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }
        .form-control.valid:focus {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.3rem rgba(40, 167, 69, 0.15) !important;
        }
        .form-control.invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.3rem rgba(220, 53, 69, 0.15) !important;
        }
        .btn-primary, .btn-success {
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary, .btn-outline-secondary {
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border-width: 2px;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        
        /* Smooth transitions for step changes */
        #preSignupStep1, #preSignupStep2, #preSignupStep3 {
            transition: all 0.3s ease-in-out;
        }
        
        /* Input reset animation */
        .form-control.reset-animation {
            animation: resetPulse 0.6s ease-in-out;
        }
        
        @keyframes resetPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); border-color: #667eea; }
            100% { transform: scale(1); }
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
            .signup-card {
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
            .signup-header {
                margin-bottom: 10px;
                padding-top: 8px;
            }
            .signup-header .logo {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                margin-bottom: 5px;
                box-shadow: 0 2px 8px rgba(102,126,234,0.13);
            }
            .signup-header h2 {
                font-size: 1rem;
                margin-bottom: 3px;
            }
            
            /* Mobile button adjustments */
            .btn-outline-primary, .btn-outline-secondary {
                font-size: 12px;
                padding: 10px 12px;
            }
            
            /* OTP input mobile styling */
            #otp_code {
                max-width: 180px !important;
                font-size: 1.1em !important;
            }
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



    <div class="signup-card position-relative">
        <div class="signup-header" style="margin-top: 18px;">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2>Pre-Sign Up Verification</h2>
            <p>Verify your Microsoft 365 Account</p>
        </div>
        <div id="preSignupStep1">
            <form id="preSignupEmailForm" method="POST" action="{{ route('pre_signup.send_verification') }}">
                @csrf
                <div class="mb-3 text-center">
                    <label for="ms365_email" class="form-label w-100 text-center">Microsoft 365 Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fab fa-microsoft text-primary"></i></span>
                        <input type="email" class="form-control" id="ms365_email" name="ms365_email" placeholder="firstname.lastname@mcclawis.edu.ph" required autofocus pattern="^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$" title="Email must be in the format firstname.lastname@mcclawis.edu.ph">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Send Verification Code</button>
            </form>
        </div>
        <div id="preSignupStep2" style="display:none;">
            <form id="preSignupOtpForm" method="POST" action="{{ route('pre_signup.verify_otp') }}">
                @csrf
                <input type="hidden" name="ms365_email" id="otp_email">
                <div class="mb-3 text-center">
                    <div class="alert alert-info" id="otpTimerAlert">
                        <i class="fas fa-clock"></i>
                        <span id="otpTimerText">Time remaining: <span id="otpCountdown">05:00</span></span>
                    </div>
                </div>
                <div class="mb-3 text-center">
                    <label for="otp_code" class="form-label">Enter the code sent to your Outlook email</label>
                    <div class="d-flex justify-content-center">
                        <input type="tel" class="form-control text-center" id="otp_code" name="otp_code" maxlength="6" pattern="\d{6}" inputmode="numeric" required 
                               style="letter-spacing: 0.5em; font-size: 1.2em; font-weight: 600; max-width: 200px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-success mb-3" id="verifyOtpBtn">Verify Code</button>
                
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
        <div id="preSignupStep3" style="display:none;">
            <div class="alert alert-success text-center">
                Email verified! You may now sign up.
            </div>
            <a href="{{ route('signup') }}?verified_email=" id="proceedToSignupBtn" class="btn btn-primary">Proceed to Signup</a>
        </div>
        
        <!-- Back to Login button for steps 1 and 3 -->
        <div class="d-flex justify-content-center mt-3" id="backToLoginContainer">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary" style="min-width: 140px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </div>
    <script>
    // Example JS to handle step transitions (replace with backend logic as needed)
    document.addEventListener('DOMContentLoaded', function() {
        const emailForm = document.getElementById('preSignupEmailForm');
        const otpForm = document.getElementById('preSignupOtpForm');
        const step1 = document.getElementById('preSignupStep1');
        const step2 = document.getElementById('preSignupStep2');
        const step3 = document.getElementById('preSignupStep3');
        const otpEmail = document.getElementById('otp_email');
        const proceedBtn = document.getElementById('proceedToSignupBtn');

        // Add these variables at the top of your DOMContentLoaded function:
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const resendOtpBtn = document.getElementById('resendOtpBtn');
        const otpTimerAlert = document.getElementById('otpTimerAlert');
        const otpCountdown = document.getElementById('otpCountdown');
        const backToLoginContainer = document.getElementById('backToLoginContainer');

        let otpTimer;
        let otpTimeLeft = 300; // 5 minutes in seconds
        const OTP_TIMEOUT = 20; // 5 minutes
        
        // Function to manage back button visibility
        function updateBackButtonVisibility() {
            if (step2.style.display !== 'none') {
                // Step 2 (OTP) - hide the separate back button since it's integrated
                backToLoginContainer.style.display = 'none';
            } else {
                // Step 1 or 3 - show the separate back button
                backToLoginContainer.style.display = 'flex';
            }
        }
        
        // Initialize back button visibility on page load
        updateBackButtonVisibility();

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

            if (otpTimeLeft <= 60) {
                otpTimerAlert.className = 'alert alert-danger';
                otpTimerAlert.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">' + timeString + '</span></span>';
                resendOtpBtn.style.display = 'block';
            } else if (otpTimeLeft <= 120) {
                otpTimerAlert.className = 'alert alert-warning';
                otpTimerAlert.innerHTML = '<i class="fas fa-clock"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">' + timeString + '</span></span>';
            } else {
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

        function resetPreSignupForm() {
            // Smooth transition to step 1
            step2.style.display = 'none';
            step3.style.display = 'none';
            step1.style.display = 'block';
            updateBackButtonVisibility();
            
            // Clear the email input with animation
            const emailInput = document.getElementById('ms365_email');
            if (emailInput) {
                emailInput.value = '';
                emailInput.classList.remove('valid', 'invalid');
                
                // Add reset animation
                emailInput.classList.add('reset-animation');
                setTimeout(() => {
                    emailInput.classList.remove('reset-animation');
                    emailInput.focus(); // Focus back to email input for better UX
                }, 600);
            }
            
            // Reset any form validation states
            const form = document.getElementById('preSignupEmailForm');
            if (form) {
                form.classList.remove('was-validated');
            }
            
            // Clear OTP related data
            const otpCodeInput = document.getElementById('otp_code');
            if (otpCodeInput) {
                otpCodeInput.value = '';
            }
            otpEmail.value = '';
            
            // Stop any running timer
            stopOtpTimer();
            
            // Hide resend button
            if (resendOtpBtn) {
                resendOtpBtn.style.display = 'none';
            }
            
            // Reset timer alert to default state
            if (otpTimerAlert) {
                otpTimerAlert.className = 'alert alert-info';
                otpTimerAlert.innerHTML = '<i class="fas fa-clock"></i> <span id="otpTimerText">Time remaining: <span id="otpCountdown">05:00</span></span>';
            }
        }

        // Handle email form submission with AJAX
        if(emailForm) {
            emailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('ms365_email').value;
                const submitBtn = emailForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
                
                // Create FormData object
                const formData = new FormData(emailForm);
                
                // Send AJAX request
                fetch(emailForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Email is valid and not duplicate, proceed to OTP step
                        step1.style.display = 'none';
                        step2.style.display = 'block';
                        otpEmail.value = email;
                        startOtpTimer();
                        updateBackButtonVisibility();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Verification Code Sent!',
                            text: data.message,
                            confirmButtonColor: '#667eea',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Email Already Registered',
                            text: data.message,
                            confirmButtonColor: '#667eea',
                            confirmButtonText: 'Try Again'
                        }).then(() => {
                            // Auto reset form after error alert is dismissed
                            resetPreSignupForm();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to verify email. Please check your connection and try again.',
                        confirmButtonColor: '#667eea'
                    }).then(() => {
                        // Auto reset form after connection error alert is dismissed
                        resetPreSignupForm();
                    });
                })
                .finally(() => {
                    // Re-enable button and restore original text
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        }
        if(otpForm) {
            otpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show success SweetAlert first
                Swal.fire({
                    icon: 'success',
                    title: 'Email Verified!',
                    text: 'Your Microsoft 365 email has been successfully verified. Redirecting to signup...',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    // Stop timer and redirect directly to signup
                    stopOtpTimer();
                    window.location.href = `{{ route('signup') }}?verified_email=${encodeURIComponent(otpEmail.value)}`;
                });
            });
        }

        // Resend OTP button logic:
        if(resendOtpBtn) {
            resendOtpBtn.addEventListener('click', function() {
                const email = otpEmail.value;
                resendOtpBtn.disabled = true;
                resendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resending...';

                // Create form data for resend request
                const formData = new FormData();
                formData.append('ms365_email', email);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // Send AJAX request to resend verification
                fetch('{{ route("pre_signup.send_verification") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        startOtpTimer();
                        document.getElementById('otp_code').value = '';
                        resendOtpBtn.style.display = 'none';

                        Swal.fire({
                            icon: 'success',
                            title: 'Code Resent!',
                            text: data.message,
                            confirmButtonColor: '#667eea'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#667eea'
                        }).then(() => {
                            // Auto reset form after resend error alert is dismissed
                            resetPreSignupForm();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to resend verification code. Please try again.',
                        confirmButtonColor: '#667eea'
                    }).then(() => {
                        // Auto reset form after resend connection error alert is dismissed
                        resetPreSignupForm();
                    });
                })
                .finally(() => {
                    resendOtpBtn.disabled = false;
                    resendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
                });
            });
        }

        const otpInput = document.getElementById('otp_code');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                // Remove any non-digit character and limit to 6 digits
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        }

        // Email input validation and auto-completion
        const emailInput = document.getElementById('ms365_email');
        if (emailInput) {
            emailInput.addEventListener('input', function(e) {
                let value = this.value;
                let cursorPosition = this.selectionStart;
                
                // Remove any characters that are not letters, @ or .
                let filteredValue = value.replace(/[^a-zA-Z@.]/g, '');
                
                // Auto-complete when @ is typed
                if (filteredValue.includes('@') && !filteredValue.includes('@mcclawis.edu.ph')) {
                    const atIndex = filteredValue.indexOf('@');
                    const beforeAt = filteredValue.substring(0, atIndex);
                    filteredValue = beforeAt + '@mcclawis.edu.ph';
                }
                
                // Update the input value
                this.value = filteredValue;
                
                // Restore cursor position if possible
                if (cursorPosition <= filteredValue.length) {
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }
                
                // Real-time validation feedback
                validateEmailFormat(filteredValue);
            });

            emailInput.addEventListener('keydown', function(e) {
                // Allow backspace, delete, tab, escape, enter, and arrow keys
                if ([8, 9, 27, 13, 37, 38, 39, 40, 46].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }
                
                // Only allow letters, @ and .
                if (!/[a-zA-Z@.]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            emailInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                // Filter pasted content to only allow letters, @ and .
                let filteredPaste = paste.replace(/[^a-zA-Z@.]/g, '');
                
                // Insert filtered content at cursor position
                let start = this.selectionStart;
                let end = this.selectionEnd;
                let currentValue = this.value;
                let newValue = currentValue.substring(0, start) + filteredPaste + currentValue.substring(end);
                
                // Auto-complete if @ is in the pasted content
                if (newValue.includes('@') && !newValue.includes('@mcclawis.edu.ph')) {
                    const atIndex = newValue.indexOf('@');
                    const beforeAt = newValue.substring(0, atIndex);
                    newValue = beforeAt + '@mcclawis.edu.ph';
                }
                
                this.value = newValue;
                validateEmailFormat(newValue);
            });
        }

        function validateEmailFormat(email) {
            const emailPattern = /^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$/;
            const emailInput = document.getElementById('ms365_email');
            
            // Remove existing validation classes
            emailInput.classList.remove('valid', 'invalid');
            
            if (email === '') {
                // Reset styling for empty input
                return;
            }
            
            if (emailPattern.test(email)) {
                // Valid format - add valid class
                emailInput.classList.add('valid');
            } else {
                // Invalid format - add invalid class
                emailInput.classList.add('invalid');
            }
        }
    });
    </script>
   <script src="{{ asset('js/dev-tools-security.js') }}"></script>
</body>
</html> 