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
        
        /* Terms and Conditions Styles */
        .terms-checkbox-container {
            margin: 20px 0;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .terms-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        .terms-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .btn-primary:disabled {
            background: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        /* Modal Styles */
        .modal-dialog {
            margin: 1rem auto;
            max-width: 90%;
            width: 90%;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            border-bottom: none;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        .modal-body {
            padding: 30px;
            max-height: 60vh;
            overflow-y: auto;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 20px 30px;
        }
        
        .terms-content {
            line-height: 1.6;
            color: #333;
        }
        
        .terms-content h4 {
            color: #667eea;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        
        .terms-content h4:first-child {
            margin-top: 0;
        }
        
        /* Accept Terms Button Styling */
        #acceptTermsBtn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        #acceptTermsBtn:hover {
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            transform: translateY(-2px);
        }
        
        /* Mobile Modal Responsive Styles */
        @media (max-width: 767.98px) {
            .modal-dialog {
                margin: 0.5rem auto;
                max-width: 85%;
                width: 85%;
            }
            
            .modal-content {
                border-radius: 15px;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
            }
            
            .modal-header {
                padding: 15px 20px;
                border-radius: 15px 15px 0 0;
            }
            
            .modal-header .modal-title {
                font-size: 1.1rem;
            }
            
            .modal-body {
                padding: 20px;
                max-height: calc(90vh - 140px);
                overflow-y: auto;
                flex: 1;
            }
            
            .modal-footer {
                padding: 15px 20px;
                flex-shrink: 0;
            }
            
            .terms-content {
                font-size: 14px;
                line-height: 1.5;
            }
            
            .terms-content h4 {
                font-size: 1rem;
                margin-top: 20px;
                margin-bottom: 10px;
            }
            
            .terms-content ul {
                padding-left: 20px;
            }
            
            .terms-content li {
                margin-bottom: 5px;
            }
            
            .modal-footer .btn {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 575.98px) {
            .modal-dialog {
                margin: 0.5rem auto;
                max-width: 80%;
                width: 80%;
            }
            
            .modal-body {
                padding: 15px;
            }
            
            .modal-footer {
                padding: 12px 15px;
            }
            
            .terms-content {
                font-size: 13px;
            }
            
            .modal-footer .btn {
                font-size: 13px;
                padding: 8px 12px;
            }
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
            
            /* Mobile terms checkbox styling */
            .terms-checkbox-container {
                margin: 15px 0;
                padding: 12px;
            }
            
            .form-check-label {
                font-size: 13px;
                line-height: 1.4;
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
                
                <!-- Terms and Conditions Checkbox -->
                <div class="terms-checkbox-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="acceptTerms" name="accept_terms" required>
                        <label class="form-check-label" for="acceptTerms">
                            I agree to the <span class="terms-link" id="termsLink">Terms and Conditions</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="sendVerificationBtn" disabled>Send Verification Code</button>
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
                        <input type="tel" class="form-control text-center"   id="otp_code" name="otp_code" maxlength="6" pattern="\d{6}" inputmode="numeric" required 
                               style="letter-spacing: 0.5em; font-size: 1.2em; font-weight: 600; max-width: 200px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-success mb-3" id="verifyOtpBtn">Verify Code</button>
                
                <!-- Horizontal button layout -->
                <div class="d-flex justify-content-between gap-2 mt-2">
                   
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

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">
                        <i class="fas fa-file-contract me-2"></i>Terms and Conditions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="terms-content">
                        <h4>1. Acceptance of Terms</h4>
                        <p>By accessing and using the Instructors Performance Evaluation System (IPES) at MCC, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                        
                        <h4>2. System Purpose</h4>
                        <p>OPES is designed exclusively for educational and administrative purposes within Madridejos Community College. The system facilitates performance evaluation, data management, and academic administration for authorized users only.</p>
                        
                        <h4>3. User Eligibility</h4>
                        <p>Access to this system is restricted to:</p>
                        <ul>
                            <li>Current students</li>
                            <li>Users with valid @mcclawis.edu.ph email addresses</li>
                            <li>Individuals authorized by the institution's administration</li>
                        </ul>
                        
                        <h4>4. Data Privacy and Protection</h4>
                        <p>We are committed to protecting your privacy and personal information:</p>
                        <ul>
                            <li>Personal data is collected only for legitimate educational purposes</li>
                            <li>Information is stored securely and accessed only by authorized personnel</li>
                            <li>Data sharing complies with applicable privacy laws and institutional policies</li>
                            <li>Microsoft 365 integration follows Microsoft's privacy standards</li>
                        </ul>
                        
                        <h4>5. Acceptable Use Policy</h4>
                        <p>Users must:</p>
                        <ul>
                            <li>Use the system only for its intended educational purposes</li>
                            <li>Maintain the confidentiality of login credentials</li>
                            <li>Report any security vulnerabilities or unauthorized access</li>
                            <li>Respect the intellectual property rights of the institution</li>
                        </ul>
                        
                        <p>Users must NOT:</p>
                        <ul>
                            <li>Share account credentials with unauthorized individuals</li>
                            <li>Attempt to access restricted areas or data</li>
                            <li>Use the system for commercial or non-educational purposes</li>
                            <li>Engage in any activity that could harm the system or other users</li>
                        </ul>
                        
                        <h4>6. System Availability</h4>
                        <p>While we strive to maintain system availability, MCC reserves the right to:</p>
                        <ul>
                            <li>Perform scheduled maintenance that may temporarily limit access</li>
                            <li>Modify system features and functionality as needed</li>
                            <li>Suspend access for security or administrative reasons</li>
                        </ul>
                        
                        <h4>7. Limitation of Liability</h4>
                        <p>Madridejos Community College and its representatives shall not be liable for:</p>
                        <ul>
                            <li>Temporary system outages or technical difficulties</li>
                            <li>Data loss due to user error or system failures</li>
                            <li>Unauthorized access resulting from user negligence</li>
                        </ul>
                        
                        <h4>8. Account Termination</h4>
                        <p>MCC Lawis reserves the right to terminate user accounts for:</p>
                        <ul>
                            <li>Violation of these terms and conditions</li>
                            <li>Misuse of system resources or data</li>
                            <li>End of enrollment or employment with the institution</li>
                        </ul>
                        
                        <h4>9. Changes to Terms</h4>
                        <p>These terms may be updated periodically. Users will be notified of significant changes and continued use constitutes acceptance of modified terms.</p>
                        
                        <h4>10. Contact Information</h4>
                        <p>For questions about these terms or the system, please contact the MCC Lawis IT Department or administration.</p>
                        
                        <p class="mt-4"><strong>Last Updated:</strong> {{ date('F d, Y') }}</p>
                        
                        <!-- Accept Terms Button at the end of content -->
                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-primary btn-lg" id="acceptTermsBtn">
                                <i class="fas fa-check me-2"></i>I Accept These Terms and Conditions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
        
        // Terms and Conditions elements
        const acceptTermsCheckbox = document.getElementById('acceptTerms');
        const sendVerificationBtn = document.getElementById('sendVerificationBtn');
        const acceptTermsBtn = document.getElementById('acceptTermsBtn');
        const termsModalElement = document.getElementById('termsModal');
        const termsLink = document.getElementById('termsLink');

        let otpTimer;
        let otpTimeLeft = 300; // 5 minutes in seconds
        const OTP_TIMEOUT = 300; // 5 minutes in seconds
        
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
        
        // Simple Terms and Conditions functionality
        let termsAccepted = false;
        
        // Simple function to show modal
        function showModal() {
            if (termsModalElement) {
                termsModalElement.style.display = 'flex';
                termsModalElement.style.alignItems = 'center';
                termsModalElement.style.justifyContent = 'center';
                termsModalElement.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modal-backdrop';
                document.body.appendChild(backdrop);
            }
        }
        
        // Simple function to hide modal
        function hideModal() {
            if (termsModalElement) {
                termsModalElement.style.display = 'none';
                termsModalElement.classList.remove('show');
                document.body.style.overflow = '';
                
                // Remove backdrop
                const backdrop = document.getElementById('modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        }
        
        // Handle checkbox click
        if (acceptTermsCheckbox) {
            acceptTermsCheckbox.addEventListener('click', function(e) {
                if (!termsAccepted) {
                    e.preventDefault();
                    this.checked = false;
                    showModal();
                } else if (this.checked === false) {
                    // If user unchecks the checkbox after accepting terms
                    termsAccepted = false;
                    sendVerificationBtn.disabled = true;
                }
            });
            
            // Also handle change event for unchecking
            acceptTermsCheckbox.addEventListener('change', function() {
                if (!this.checked && termsAccepted) {
                    // User unchecked the checkbox
                    termsAccepted = false;
                    sendVerificationBtn.disabled = true;
                }
            });
        }
        
        // Handle terms link click
        if (termsLink) {
            termsLink.addEventListener('click', function(e) {
                e.preventDefault();
                showModal();
            });
        }
        
        // Handle accept button in modal
        if (acceptTermsBtn) {
            acceptTermsBtn.addEventListener('click', function() {
                termsAccepted = true;
                acceptTermsCheckbox.checked = true;
                sendVerificationBtn.disabled = false;
                hideModal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Terms Accepted',
                    text: 'Thank you for accepting the terms and conditions.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
        }
        
        // Handle modal close button (only the X button in header)
        const closeButton = termsModalElement?.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                if (!termsAccepted) {
                    acceptTermsCheckbox.checked = false;
                    sendVerificationBtn.disabled = true;
                }
                hideModal();
            });
        }
        
        // Handle clicking outside modal to close
        if (termsModalElement) {
            termsModalElement.addEventListener('click', function(e) {
                if (e.target === termsModalElement) {
                    if (!termsAccepted) {
                        acceptTermsCheckbox.checked = false;
                        sendVerificationBtn.disabled = true;
                    }
                    hideModal();
                }
            });
        }
        
        // Initialize - button should be disabled
        if (sendVerificationBtn) {
            sendVerificationBtn.disabled = true;
        }

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
            
            // Reset terms checkbox and acceptance
            if (acceptTermsCheckbox) {
                acceptTermsCheckbox.checked = false;
                termsAccepted = false;
                if (sendVerificationBtn) {
                    sendVerificationBtn.disabled = true;
                }
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
                
                const otpCode = document.getElementById('otp_code').value;
                const email = otpEmail.value;
                const submitBtn = otpForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                // Create FormData object
                const formData = new FormData(otpForm);
                
                // Send AJAX request to verify OTP
                fetch(otpForm.action, {
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
                        // OTP is correct - show success and redirect
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
                            // Stop timer and redirect to signup
                            stopOtpTimer();
                            window.location.href = `{{ route('signup') }}?verified_email=${encodeURIComponent(email)}`;
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
                    // Re-enable button and restore original text
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
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