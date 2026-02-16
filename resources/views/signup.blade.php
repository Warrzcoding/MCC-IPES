<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up - Office Performance Evaluation System</title>
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
           
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .signup-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 1.28rem;
            max-width: 320px;
            width: 80%;
            margin: 0.64rem;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 0.96rem;
        }

        .signup-header h2 {
            margin: 0.4rem 0 0.2rem 0;
            font-size: 1.2rem;
        }

        .signup-header p {
            margin: 0 0 0.4rem 0;
            font-size: 0.72rem;
        }

        .signup-header .logo {
            width: 38.4px;
            height: 38.4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.32rem;
            font-size: 0.96rem;
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.32rem;
            font-size: 0.8rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.48rem;
            font-size: 0.72rem;
            height: 32px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.48rem 1.28rem;
            font-weight: 600;
            width: 100%;
            font-size: 0.8rem;
            height: 32px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        .signup-link {
            text-align: center;
            margin-top: 0.64rem;
            padding-top: 0.64rem;
            border-top: 1px solid #eee;
            font-size: 0.72rem;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            color: #764ba2;
        }

        .image-upload-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.96rem;
            padding: 0.48rem;
            border: 2px dashed #ddd;
            border-radius: 8px;
            font-size: 0.65rem;
        }

        .image-preview {
            width: 51.2px;
            height: 51.2px;
            border-radius: 50%;
            border: 3px solid #667eea;
            object-fit: cover;
            flex-shrink: 0;
        }

        .image-upload-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.15rem;
        }

        .upload-btn {
            background: #f8f9fa;
            border: 1px solid #667eea;
            color: #667eea;
            padding: 0.18rem 0.5rem; /* smaller button */
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.65rem;      /* smaller text */
        }

        .upload-btn:hover {
            background: #667eea;
            color: white;
        }

        .password-strength {
            margin-top: 0.32rem;
        }

        .password-strength-bar {
            height: 2.4px;
            background: #eee;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            transition: width 0.3s ease;
        }

        .password-strength-text {
            font-size: 0.7rem;
            margin-top: 0.1rem;
        }

        .password-weak { background: #dc3545; }
        .password-fair { background: #ffc107; }
        .password-good { background: #28a745; }
        .password-strong { background: #20c997; }

        .mb-2 {
            margin-bottom: 0.48rem !important;
        }
        
        .mt-2 {
            margin-top: 0.48rem !important;
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

        /* Desktop alignment fixes */
        @media (min-width: 768px) {
            .form-control, .form-select {
                width: 100%;
                max-width: 100%;
            }

            .row .col-md-6 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .row .col-md-6:first-child {
                padding-left: 0;
            }

            .row .col-md-6:last-child {
                padding-right: 0;
            }
        }

        @media (max-width: 576px) {
            .signup-container {
                padding: 0.96rem;
                margin: 0.32rem;
                max-width: 90%;
                width: 90%;
            }

            .signup-header {
                margin-bottom: 0.8rem;
            }

            .signup-header h2 {
                font-size: 1rem;
                margin: 0.32rem 0 0.16rem 0;
            }

            .signup-header p {
                font-size: 0.65rem;
                margin: 0 0 0.32rem 0;
            }

            .signup-header .logo {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
                margin: 0 auto 0.24rem;
            }

            .form-label {
                font-size: 0.7rem;
                margin-bottom: 0.24rem;
            }

            .form-control, .form-select {
                height: 28px;
                padding: 0.4rem;
                font-size: 0.68rem;
            }

            .btn-primary {
                height: 28px;
                padding: 0.4rem;
                font-size: 0.72rem;
            }

            .image-preview {
                width: 45px;
                height: 45px;
            }

            .image-upload-section {
                margin-bottom: 0.8rem;
                padding: 0.4rem;
                font-size: 0.6rem;
            }

            .signup-link {
                margin-top: 0.48rem;
                padding-top: 0.48rem;
                font-size: 0.65rem;
            }

            .mb-2 {
                margin-bottom: 0.4rem !important;
            }
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

        /* Password toggle eye icon styles */
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            font-size: 0.8rem;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .password-toggle:focus {
            outline: none;
            color: #667eea;
        }

        /* Password match indicator styles */
        .password-match-indicator {
            font-size: 0.7rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .password-match-indicator.match {
            color: #28a745;
        }

        .password-match-indicator.no-match {
            color: #dc3545;
        }

        .password-match-indicator.hidden {
            display: none;
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




    <div class="signup-container">
        <div class="signup-header">
            <div class="logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Sign Up</h2>
            <p>Create your account for MCCIPES application</p>
        </div>

        <form method="POST" action="{{ route('signup.submit') }}" enctype="multipart/form-data" id="signupForm">
            @csrf

            <!-- Profile Image Upload -->
            <div class="image-upload-section">
                <img id="previewImg"
                     src="https://ui-avatars.com/api/?name=Profile&background=cccccc&color=555555&rounded=true&size=80"
                     alt="Preview"
                     class="image-preview">

                <div class="image-upload-content">
                    <p class="text-muted small mb-0">Upload profile photo (JPEG or PNG, max 2 MB)</p>
                    <label for="profileImageInput" class="upload-btn mb-0">
                        <i class="fas fa-folder-open"></i> Choose File
                    </label>
                    
                    <input type="file"
                           class="d-none @error('profile_image') is-invalid @enderror"
                           name="profile_image"
                           accept="image/*"
                           id="profileImageInput">
                    @error('profile_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Personal Information -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="full_name" class="form-label">
                        <i class="fas fa-user"></i> Full Name *
                    </label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                           id="full_name" name="full_name"   readonly="true"
                           value="{{ isset($verified_id_info) ? $verified_id_info['fullname'] : old('full_name') }}" 
                           required
                           pattern="[A-Za-z\s\.]+" maxlength="50"
                           placeholder="Enter fullname">
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label for="username" class="form-label">
                        <i class="fas fa-user-tag"></i> Username *
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                           id="username" name="username" value="{{ old('username') }}" required
                           pattern="[A-Za-z\.]+" maxlength="50"
                           placeholder="Enter username">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> {{ isset($verified_email) && $verified_email ? 'MS Account' : 'Email' }} *
                    </label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" 
                           value="{{ isset($verified_email) && $verified_email ? $verified_email : old('email') }}"
                           required placeholder="Enter email address"
                           maxlength="50"
                           pattern="^[a-zA-Z0-9._%+-]+@mcclawis\.edu\.ph$"
                           title="Email must end with @mcclawis.edu.ph"
                           @if(isset($verified_email) && $verified_email) readonly @endif>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student Status -->
                <div class="col-md-6 mb-2">
                    <label for="student_status" class="form-label">
                        <i class="fas fa-graduation-cap"></i> Student Status *
                    </label>
                    <select class="form-select @error('student_status') is-invalid @enderror" id="student_status" name="student_status" required>
                        <option value="">Select status...</option>
                        <option value="Regular" {{ old('student_status') == 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Irregular" {{ old('student_status') == 'Irregular' ? 'selected' : '' }}>Irregular</option>
                    </select>
                    @error('student_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- School Information -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="school_id" class="form-label">
                        <i class="fas fa-id-card"></i> School ID *
                    </label>
                    <input type="text" class="form-control @error('school_id') is-invalid @enderror"
                           id="school_id" name="school_id" readonly="true"
                           value="{{ isset($verified_id_info) ? $verified_id_info['id_number'] : (isset($school_id) ? $school_id : old('school_id')) }}" 
                           required
                           pattern="\d{4}-\d{4}" maxlength="9" inputmode="numeric"
                           placeholder="2020-0000" >
                    @error('school_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label for="course" class="form-label">
                        <i class="fas fa-book"></i> Course *
                    </label>
                    @php
                        $selectedCourse = old('course');
                        if (isset($verified_id_info) && $verified_id_info['course']) {
                            $selectedCourse = $verified_id_info['course'];
                        }
                    @endphp
                    <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                        <option value="">Select course...</option>
                        <option value="BSIT" {{ $selectedCourse == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BSHM" {{ $selectedCourse == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                        <option value="BSBA" {{ $selectedCourse == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                        <option value="BSED" {{ $selectedCourse == 'BSED' ? 'selected' : '' }}>BSED</option>
                        <option value="BEED" {{ $selectedCourse == 'BEED' ? 'selected' : '' }}>BEED</option>
                    </select>
                    @error('course')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="year_level" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Year Level *
                    </label>
                    @php
                        $selectedYear = old('year_level');
                        if (isset($verified_id_info) && $verified_id_info['year']) {
                            $year = $verified_id_info['year'];
                            // Normalize year format if needed
                            if ($year == '1' || $year == '1st') $selectedYear = '1st Year';
                            elseif ($year == '2' || $year == '2nd') $selectedYear = '2nd Year';
                            elseif ($year == '3' || $year == '3rd') $selectedYear = '3rd Year';
                            elseif ($year == '4' || $year == '4th') $selectedYear = '4th Year';
                            else $selectedYear = $year; // fallback
                        }
                    @endphp
                    <select class="form-select @error('year_level') is-invalid @enderror" id="year_level" name="year_level" required>
                        <option value="">Select year...</option>
                        <option value="1st Year" {{ $selectedYear == '1st Year' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd Year" {{ $selectedYear == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd Year" {{ $selectedYear == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th Year" {{ $selectedYear == '4th Year' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label for="section" class="form-label">
                        <i class="fas fa-users"></i> Section *
                    </label>
                    <select class="form-select @error('section') is-invalid @enderror" id="section" name="section" required>
                        <option value="">Select section...</option>
                    </select>
                    @error('section')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="role" value="student">

            <!-- Password -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password *
                    </label>
                    <div class="password-container">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required maxlength="25"
                               placeholder="Create password">
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar">
                            <div class="password-strength-fill" id="passwordStrengthFill"></div>
                        </div>
                        <div class="password-strength-text" id="passwordStrengthText"></div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password *
                    </label>
                    <div class="password-container">
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" required
                               placeholder="Confirm password">
                        <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Toggle confirm password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-match-indicator hidden" id="passwordMatchIndicator">
                        <i class="fas fa-check-circle"></i>
                        <span id="passwordMatchText">Passwords match</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-2" id="submitBtn">
                <i class="fas fa-user-plus"></i> Submit Request
            </button>
        </form>

        <div class="signup-link">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        @if (session('success'))
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

        document.addEventListener('DOMContentLoaded', function() {
            // Email input auto-completion and max length handling
            const emailInput = document.getElementById('email');
            if (emailInput && !emailInput.readOnly) {
                emailInput.addEventListener('input', function(e) {
                    let value = this.value;
                    let cursorPosition = this.selectionStart;
                    
                    // Filter allowed characters and limit to 50
                    let filteredValue = value.replace(/[^a-zA-Z0-9._%+-@]/g, '').substring(0, 50);
                    
                    // Auto-complete when @ is typed
                    if (filteredValue.includes('@') && !filteredValue.includes('@mcclawis.edu.ph')) {
                        const atIndex = filteredValue.indexOf('@');
                        const beforeAt = filteredValue.substring(0, atIndex);
                        filteredValue = beforeAt + '@mcclawis.edu.ph';
                    }
                    
                    // Update input value
                    this.value = filteredValue;
                    
                    // Restore cursor position if possible
                    if (cursorPosition <= filteredValue.length) {
                        this.setSelectionRange(cursorPosition, cursorPosition);
                    }
                });

                emailInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let paste = (e.clipboardData || window.clipboardData).getData('text');
                    let filteredPaste = paste.replace(/[^a-zA-Z0-9._%+-@]/g, '');
                    
                    let start = this.selectionStart;
                    let end = this.selectionEnd;
                    let currentValue = this.value;
                    let newValue = currentValue.substring(0, start) + filteredPaste + currentValue.substring(end);
                    
                    if (newValue.includes('@') && !newValue.includes('@mcclawis.edu.ph')) {
                        const atIndex = newValue.indexOf('@');
                        const beforeAt = newValue.substring(0, atIndex);
                        newValue = beforeAt + '@mcclawis.edu.ph';
                    }
                    
                    this.value = newValue.substring(0, 50);
                });
            }

            // Auto-hide server-side validation error messages after 5 seconds
            setTimeout(() => {
                const errorMessages = document.querySelectorAll('.invalid-feedback');
                errorMessages.forEach(error => {
                    // Only hide if it's a direct child of a row or col (server-side errors)
                    // and not the one we manually create with 'custom-error' class
                    if (!error.classList.contains('custom-error')) {
                        error.style.display = 'none';
                        // Find the associated input and remove is-invalid class
                        const input = error.parentNode.querySelector('.form-control, .form-select');
                        if (input) {
                            input.classList.remove('is-invalid');
                        }
                    }
                });
            }, 5000);

            // Profile image preview
            const profileImageInput = document.getElementById('profileImageInput');
            const previewImg = document.getElementById('previewImg');

            if (profileImageInput) {
                profileImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Too Large',
                                text: 'File size must be less than 2MB',
                                confirmButtonColor: '#667eea',
                            });
                            this.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Full Name input sanitization (letters, spaces, and dots only)
            const fullNameInput = document.getElementById('full_name');
            if (fullNameInput) {
                fullNameInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/[^A-Za-z\s\.]/g, '').substring(0, 50);
                    e.target.value = value;
                });
                fullNameInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    let cleanedText = pastedText.replace(/[^A-Za-z\s\.]/g, '').substring(0, 50);
                    e.target.value = cleanedText;
                });
            }

            // Username input sanitization (letters and dots only)
            const usernameInput = document.getElementById('username');
            if (usernameInput) {
                usernameInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/[^A-Za-z\.]/g, '').substring(0, 50);
                    e.target.value = value;
                });
                usernameInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    let cleanedText = pastedText.replace(/[^A-Za-z\.]/g, '').substring(0, 50);
                    e.target.value = cleanedText;
                });
            }

            // School ID auto-formatting
            const schoolIdInput = document.getElementById('school_id');

            if (schoolIdInput) {
                schoolIdInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9-]/g, ''); // Only allow numbers and dash

                    // Remove any existing dashes and limit to 8 digits
                    let digitsOnly = value.replace(/-/g, '');
                    if (digitsOnly.length > 8) {
                        digitsOnly = digitsOnly.substring(0, 8);
                    }

                    // Auto-format: add dash after 4 digits
                    if (digitsOnly.length >= 4) {
                        value = digitsOnly.substring(0, 4) + '-' + digitsOnly.substring(4);
                    } else {
                        value = digitsOnly;
                    }

                    // Prevent more than one dash
                    const dashCount = (value.match(/-/g) || []).length;
                    if (dashCount > 1) {
                        value = value.replace(/-/g, '');
                        if (value.length >= 4) {
                            value = value.substring(0, 4) + '-' + value.substring(4);
                        }
                    }

                    e.target.value = value;
                });

                // Prevent pasting invalid characters
                schoolIdInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanedText = pastedText.replace(/[^0-9]/g, '');
                    let digitsOnly = cleanedText.substring(0, 8);

                    if (digitsOnly.length >= 4) {
                        digitsOnly = digitsOnly.substring(0, 4) + '-' + digitsOnly.substring(4);
                    }

                    e.target.value = digitsOnly;
                });

                // Clear custom error messages when user starts typing
                schoolIdInput.addEventListener('input', function() {
                    const customError = this.parentNode.querySelector('.invalid-feedback.custom-error');
                    if (customError) {
                        customError.remove();
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Password toggle functionality
            const passwordToggle = document.getElementById('passwordToggle');
            const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            function togglePasswordVisibility(input, toggleBtn) {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                const icon = toggleBtn.querySelector('i');
                icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            }

            if (passwordToggle) {
                passwordToggle.addEventListener('click', function() {
                    togglePasswordVisibility(passwordInput, passwordToggle);
                });
            }

            if (confirmPasswordToggle) {
                confirmPasswordToggle.addEventListener('click', function() {
                    togglePasswordVisibility(confirmPasswordInput, confirmPasswordToggle);
                });
            }

            // Password match indicator functionality
            const passwordMatchIndicator = document.getElementById('passwordMatchIndicator');
            const passwordMatchText = document.getElementById('passwordMatchText');

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length === 0) {
                    // Hide indicator if confirm password is empty
                    passwordMatchIndicator.className = 'password-match-indicator hidden';
                    return;
                }

                if (password === confirmPassword) {
                    passwordMatchIndicator.className = 'password-match-indicator match';
                    passwordMatchText.textContent = 'Passwords match';
                    const icon = passwordMatchIndicator.querySelector('i');
                    icon.className = 'fas fa-check-circle';
                } else {
                    passwordMatchIndicator.className = 'password-match-indicator no-match';
                    passwordMatchText.textContent = 'Passwords do not match';
                    const icon = passwordMatchIndicator.querySelector('i');
                    icon.className = 'fas fa-times-circle';
                }
            }

            // Add event listeners to both password fields
            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            // Password strength checker
            const passwordStrengthFill = document.getElementById('passwordStrengthFill');
            const passwordStrengthText = document.getElementById('passwordStrengthText');

            function checkPasswordStrength(password) {
                let strength = 0;
                let feedback = [];

                if (password.length >= 8) strength++;
                else feedback.push('At least 8 characters');

                if (/[A-Z]/.test(password)) strength++;
                else feedback.push('Uppercase letter');

                if (/[a-z]/.test(password)) strength++;
                else feedback.push('Lowercase letter');

                if (/\d/.test(password)) strength++;
                else feedback.push('Number');

                if (/[^A-Za-z\d]/.test(password)) strength++;
                else feedback.push('Special character');

                return { strength, feedback };
            }

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const result = checkPasswordStrength(password);

                passwordStrengthFill.style.width = (result.strength / 5) * 100 + '%';

                if (result.strength === 0) {
                    passwordStrengthFill.className = 'password-strength-fill';
                    passwordStrengthText.textContent = '';
                } else if (result.strength <= 2) {
                    passwordStrengthFill.className = 'password-strength-fill password-weak';
                    passwordStrengthText.textContent = 'Weak';
                } else if (result.strength <= 3) {
                    passwordStrengthFill.className = 'password-strength-fill password-fair';
                    passwordStrengthText.textContent = 'Fair';
                } else if (result.strength <= 4) {
                    passwordStrengthFill.className = 'password-strength-fill password-good';
                    passwordStrengthText.textContent = 'Good';
                } else {
                    passwordStrengthFill.className = 'password-strength-fill password-strong';
                    passwordStrengthText.textContent = 'Strong';
                }
            });

            // Section dropdown population based on course and year
            const courseSelect = document.getElementById('course');
            const yearSelect = document.getElementById('year_level');
            const sectionSelect = document.getElementById('section');

            // Section data structure matching the original implementation
            const sectionData = {
                'BSIT': {
                    '1st Year': [
                        { value: 'NORTH', label: 'NORTH' },
                        { value: 'WEST', label: 'WEST' },
                        { value: 'SOUTH', label: 'SOUTH' },
                        { value: 'EAST', label: 'EAST' },
                        { value: 'SOUTHWEST', label: 'SOUTHWEST' },
                        { value: 'NORTHWEST', label: 'NORTHWEST' },
                        { value: 'SOUTHEAST', label: 'SOUTHEAST' },
                        { value: 'NORTHEAST', label: 'NORTHEAST' }
                    ],
                    '2nd Year': [
                        { value: 'NORTH', label: 'NORTH' },
                        { value: 'WEST', label: 'WEST' },
                        { value: 'SOUTH', label: 'SOUTH' },
                        { value: 'EAST', label: 'EAST' },
                        { value: 'SOUTHWEST', label: 'SOUTHWEST' },
                        { value: 'NORTHWEST', label: 'NORTHWEST' },
                        { value: 'SOUTHEAST', label: 'SOUTHEAST' },
                        { value: 'NORTHEAST', label: 'NORTHEAST' }
                    ],
                    '3rd Year': [
                        { value: 'NORTH', label: 'NORTH' },
                        { value: 'WEST', label: 'WEST' },
                        { value: 'SOUTH', label: 'SOUTH' },
                        { value: 'EAST', label: 'EAST' },
                        { value: 'SOUTHWEST', label: 'SOUTHWEST' },
                        { value: 'NORTHWEST', label: 'NORTHWEST' },
                        { value: 'SOUTHEAST', label: 'SOUTHEAST' },
                        { value: 'NORTHEAST', label: 'NORTHEAST' }
                    ],
                    '4th Year': [
                        { value: 'NORTH', label: 'NORTH' },
                        { value: 'WEST', label: 'WEST' },
                        { value: 'SOUTH', label: 'SOUTH' },
                        { value: 'EAST', label: 'EAST' },
                        { value: 'SOUTHWEST', label: 'SOUTHWEST' },
                        { value: 'NORTHWEST', label: 'NORTHWEST' },
                        { value: 'SOUTHEAST', label: 'SOUTHEAST' },
                        { value: 'NORTHEAST', label: 'NORTHEAST' }
                    ]
                },
                'BSHM': {
                    '1st Year': [
                        { value: 'BSHM-1A', label: 'BSHM-1A' },
                        { value: 'BSHM-1B', label: 'BSHM-1B' },
                        { value: 'BSHM-1C', label: 'BSHM-1C' },
                        { value: 'BSHM-1D', label: 'BSHM-1D' },
                        { value: 'BSHM-1E', label: 'BSHM-1E' },
                        { value: 'BSHM-1F', label: 'BSHM-1F' },
                        { value: 'BSHM-1G', label: 'BSHM-1G' },
                        { value: 'BSHM-1H', label: 'BSHM-1H' },
                        { value: 'BSHM-1I', label: 'BSHM-1I' },
                        { value: 'BSHM-1J', label: 'BSHM-1J' },
                        { value: 'BSHM-1K', label: 'BSHM-1K' },
                        { value: 'BSHM-1L', label: 'BSHM-1L' }
                    ],
                    '2nd Year': [
                        { value: 'BSHM-2A', label: 'BSHM-2A' },
                        { value: 'BSHM-2B', label: 'BSHM-2B' },
                        { value: 'BSHM-2C', label: 'BSHM-2C' },
                        { value: 'BSHM-2D', label: 'BSHM-2D' },
                        { value: 'BSHM-2E', label: 'BSHM-2E' },
                        { value: 'BSHM-2F', label: 'BSHM-2F' },
                        { value: 'BSHM-2G', label: 'BSHM-2G' },
                        { value: 'BSHM-2H', label: 'BSHM-2H' },
                        { value: 'BSHM-2I', label: 'BSHM-2I' },
                        { value: 'BSHM-2J', label: 'BSHM-2J' },
                        { value: 'BSHM-2K', label: 'BSHM-2K' },
                        { value: 'BSHM-2L', label: 'BSHM-2L' }
                    ],
                    '3rd Year': [
                        { value: 'BSHM-3A', label: 'BSHM-3A' },
                        { value: 'BSHM-3B', label: 'BSHM-3B' },
                        { value: 'BSHM-3C', label: 'BSHM-3C' },
                        { value: 'BSHM-3D', label: 'BSHM-3D' },
                        { value: 'BSHM-3E', label: 'BSHM-3E' },
                        { value: 'BSHM-3F', label: 'BSHM-3F' },
                        { value: 'BSHM-3G', label: 'BSHM-3G' },
                        { value: 'BSHM-3H', label: 'BSHM-3H' },
                        { value: 'BSHM-3I', label: 'BSHM-3I' },
                        { value: 'BSHM-3J', label: 'BSHM-3J' },
                        { value: 'BSHM-3K', label: 'BSHM-3K' },
                        { value: 'BSHM-3L', label: 'BSHM-3L' }
                    ],
                    '4th Year': [
                        { value: 'BSHM-4A', label: 'BSHM-4A' },
                        { value: 'BSHM-4B', label: 'BSHM-4B' },
                        { value: 'BSHM-4C', label: 'BSHM-4C' },
                        { value: 'BSHM-4D', label: 'BSHM-4D' },
                        { value: 'BSHM-4E', label: 'BSHM-4E' },
                        { value: 'BSHM-4F', label: 'BSHM-4F' },
                        { value: 'BSHM-4G', label: 'BSHM-4G' },
                        { value: 'BSHM-4H', label: 'BSHM-4H' },
                        { value: 'BSHM-4I', label: 'BSHM-4I' },
                        { value: 'BSHM-4J', label: 'BSHM-4J' },
                        { value: 'BSHM-4K', label: 'BSHM-4K' },
                        { value: 'BSHM-4L', label: 'BSHM-4L' }
                    ]
                },
                'BSBA': {
                    '1st Year': [
                        { value: 'FM-1A', label: 'FM-1A' },
                        { value: 'FM-1B', label: 'FM-1B' },
                        { value: 'FM-1C', label: 'FM-1C' },
                        { value: 'FM-1D', label: 'FM-1D' },
                        { value: 'FM-1E', label: 'FM-1E' },
                        { value: 'FM-1F', label: 'FM-1F' },
                        { value: 'FM-1G', label: 'FM-1G' },
                        { value: 'FM-1H', label: 'FM-1H' },
                        { value: 'FM-1I', label: 'FM-1I' },
                        { value: 'FM-1J', label: 'FM-1J' },
                        { value: 'FM-1K', label: 'FM-1K' },
                        { value: 'FM-1L', label: 'FM-1L' }
                    ],
                    '2nd Year': [
                        { value: 'FM-2A', label: 'FM-2A' },
                        { value: 'FM-2B', label: 'FM-2B' },
                        { value: 'FM-2C', label: 'FM-2C' },
                        { value: 'FM-2D', label: 'FM-2D' },
                        { value: 'FM-2E', label: 'FM-2E' },
                        { value: 'FM-2F', label: 'FM-2F' },
                        { value: 'FM-2G', label: 'FM-2G' },
                        { value: 'FM-2H', label: 'FM-2H' },
                        { value: 'FM-2I', label: 'FM-2I' },
                        { value: 'FM-2J', label: 'FM-2J' },
                        { value: 'FM-2K', label: 'FM-2K' },
                        { value: 'FM-2L', label: 'FM-2L' }
                    ],
                    '3rd Year': [
                        { value: 'FM-3A', label: 'FM-3A' },
                        { value: 'FM-3B', label: 'FM-3B' },
                        { value: 'FM-3C', label: 'FM-3C' },
                        { value: 'FM-3D', label: 'FM-3D' },
                        { value: 'FM-3E', label: 'FM-3E' },
                        { value: 'FM-3F', label: 'FM-3F' },
                        { value: 'FM-3G', label: 'FM-3G' },
                        { value: 'FM-3H', label: 'FM-3H' },
                        { value: 'FM-3I', label: 'FM-3I' },
                        { value: 'FM-3J', label: 'FM-3J' },
                        { value: 'FM-3K', label: 'FM-3K' },
                        { value: 'FM-3L', label: 'FM-3L' }
                    ],
                    '4th Year': [
                        { value: 'FM-4A', label: 'FM-4A' },
                        { value: 'FM-4B', label: 'FM-4B' },
                        { value: 'FM-4C', label: 'FM-4C' },
                        { value: 'FM-4D', label: 'FM-4D' },
                        { value: 'FM-4E', label: 'FM-4E' },
                        { value: 'FM-4F', label: 'FM-4F' },
                        { value: 'FM-4G', label: 'FM-4G' },
                        { value: 'FM-4H', label: 'FM-4H' },
                        { value: 'FM-4I', label: 'FM-4I' },
                        { value: 'FM-4J', label: 'FM-4J' },
                        { value: 'FM-4K', label: 'FM-4K' },
                        { value: 'FM-4L', label: 'FM-4L' }
                    ]
                },
                'BSED': {
                    '1st Year': [
                        { value: '1-A', label: '1-A' },
                        { value: '1-B', label: '1-B' },
                        { value: '1-C', label: '1-C' },
                        { value: '1-M', label: '1-M' },
                        { value: '1-N', label: '1-N' },
                        { value: '1-FR', label: '1-FR' },
                        { value: '1-SP', label: '1-SP' },
                        { value: '1-GERMAN', label: '1-GERMAN' },
                        { value: '1-TODDLER', label: '1-TODDLER' }
                    ],
                    '2nd Year': [
                        { value: '2-A', label: '2-A' },
                        { value: '2-B', label: '2-B' },
                        { value: '2-C', label: '2-C' },
                        { value: '2-M', label: '2-M' },
                        { value: '2-N', label: '2-N' },
                        { value: '2-FR', label: '2-FR' },
                        { value: '2-SP', label: '2-SP' },
                        { value: '2-GERMAN', label: '2-GERMAN' },
                        { value: '2-TODDLER', label: '2-TODDLER' }
                    ],
                    '3rd Year': [
                        { value: '3-A', label: '3-A' },
                        { value: '3-B', label: '3-B' },
                        { value: '3-C', label: '3-C' },
                        { value: '3-M', label: '3-M' },
                        { value: '3-N', label: '3-N' },
                        { value: '3-FR', label: '3-FR' },
                        { value: '3-SP', label: '3-SP' },
                        { value: '3-GERMAN', label: '3-GERMAN' },
                        { value: '3-TODDLER', label: '3-TODDLER' }
                    ],
                    '4th Year': [
                        { value: '4-A', label: '4-A' },
                        { value: '4-B', label: '4-B' },
                        { value: '4-C', label: '4-C' },
                        { value: '4-M', label: '4-M' },
                        { value: '4-N', label: '4-N' },
                        { value: '4-FR', label: '4-FR' },
                        { value: '4-SP', label: '4-SP' },
                        { value: '4-GERMAN', label: '4-GERMAN' },
                        { value: '4-TODDLER', label: '4-TODDLER' }
                    ]
                },
                'BEED': {
                    '1st Year': [
                        { value: '1-A', label: '1-A' },
                        { value: '1-B', label: '1-B' },
                        { value: '1-C', label: '1-C' },
                        { value: '1-D', label: '1-D' },
                        { value: '1-PRESCHOOLER', label: '1-PRESCHOOLER' },
                        { value: '1-TODDLER', label: '1-TODDLER' },
                        { value: '1-PR', label: '1-PR' }
                    ],
                    '2nd Year': [
                        { value: '2-A', label: '2-A' },
                        { value: '2-B', label: '2-B' },
                        { value: '2-C', label: '2-C' },
                        { value: '2-D', label: '2-D' },
                        { value: '2-PRESCHOOLER', label: '2-PRESCHOOLER' },
                        { value: '2-TODDLER', label: '2-TODDLER' },
                        { value: '2-PR', label: '2-PR' }
                    ],
                    '3rd Year': [
                        { value: '3-A', label: '3-A' },
                        { value: '3-B', label: '3-B' },
                        { value: '3-C', label: '3-C' },
                        { value: '3-D', label: '3-D' },
                        { value: '3-PRESCHOOLER', label: '3-PRESCHOOLER' },
                        { value: '3-TODDLER', label: '3-TODDLER' },
                        { value: '3-PR', label: '3-PR' }
                    ],
                    '4th Year': [
                        { value: '4-A', label: '4-A' },
                        { value: '4-B', label: '4-B' },
                        { value: '4-C', label: '4-C' },
                        { value: '4-D', label: '4-D' },
                        { value: '4-PRESCHOOLER', label: '4-PRESCHOOLER' },
                        { value: '4-TODDLER', label: '4-TODDLER' },
                        { value: '4-PR', label: '4-PR' }
                    ]
                }
            };

            function updateSections() {
                const course = courseSelect.value;
                const year = yearSelect.value;

                // Clear existing options
                sectionSelect.innerHTML = '<option value="">Select section...</option>';

                if (course && year && sectionData[course] && sectionData[course][year]) {
                    const sections = sectionData[course][year];
                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.value;
                        option.textContent = section.label;

                        // Check if this was the previously selected value or from verified info
                        const verifiedSection = '{{ isset($verified_id_info) ? $verified_id_info["section"] : "" }}';
                        if (section.value === '{{ old("section") }}' || section.value === verifiedSection) {
                            option.selected = true;
                        }

                        sectionSelect.appendChild(option);
                    });
                }
            }

            courseSelect.addEventListener('change', updateSections);
            yearSelect.addEventListener('change', updateSections);

            // Initial call to populate sections if course and year are pre-filled
            updateSections();

            // Form validation
            const signupForm = document.getElementById('signupForm');
            const submitBtn = document.getElementById('submitBtn');

            let isSubmitting = false;

            signupForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (isSubmitting) return;
                isSubmitting = true;

                // Basic validation
                const fullName = document.getElementById('full_name').value.trim();
                const username = document.getElementById('username').value.trim();
                const email = document.getElementById('email').value.trim();
                const schoolId = document.getElementById('school_id').value.trim();
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirmation').value;

                // Validate full name
                if (!/^[A-Za-z\s\.]+$/.test(fullName) || fullName.length > 50) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Full Name',
                        text: 'Please enter a valid full name (only letters, spaces, and dots, max 50 characters).',
                        confirmButtonColor: '#667eea',
                    });
                    isSubmitting = false;
                    return false;
                }

                // Validate username
                if (!/^[A-Za-z\.]+$/.test(username) || username.length > 50) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Username',
                        text: 'Please enter a valid username (only letters and dots, max 50 characters).',
                        confirmButtonColor: '#667eea',
                    });
                    isSubmitting = false;
                    return false;
                }

                // Validate password strength
                const passwordResult = checkPasswordStrength(password);
                if (passwordResult.strength < 5) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Requirements Not Met',
                        html: `Password must include:<br>• ${passwordResult.feedback.join('<br>• ')}`,
                        confirmButtonColor: '#667eea',
                    });
                    isSubmitting = false;
                    return false;
                }

                // Validate password confirmation
                if (password !== passwordConfirm) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Passwords do not match.',
                        confirmButtonColor: '#667eea',
                    });
                    isSubmitting = false;
                    return false;
                }

                // Check school ID availability in users table
                try {
                    const availabilityResponse = await fetch('{{ route("check.user.id.availability") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            school_id: schoolId
                        })
                    });

                    const availabilityData = await availabilityResponse.json();

                    if (!availabilityData.available) {
                        // Show error on school ID field
                        const schoolIdField = document.getElementById('school_id');
                        schoolIdField.classList.add('is-invalid');

                        // Remove any existing error message
                        const existingError = schoolIdField.parentNode.querySelector('.invalid-feedback.custom-error');
                        if (existingError) {
                            existingError.remove();
                        }

                        // Add error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block custom-error';
                        errorDiv.textContent = availabilityData.message;
                        schoolIdField.parentNode.appendChild(errorDiv);

                        // Timeout to remove the error message after 5 seconds
                        setTimeout(() => {
                            if (errorDiv) {
                                errorDiv.remove();
                                schoolIdField.classList.remove('is-invalid');
                            }
                        }, 5000);

                        // Scroll to the field
                        schoolIdField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        schoolIdField.focus();

                        isSubmitting = false;
                        return false;
                    }
                } catch (error) {
                    console.error('Error checking school ID availability:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to verify school ID availability. Please try again.',
                        confirmButtonColor: '#667eea',
                    });
                    isSubmitting = false;
                    return false;
                }

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                submitBtn.disabled = true;

                // Execute reCAPTCHA if available
                @if(config('services.recaptcha.site_key_v3'))
                executeRecaptchaV3(signupForm, 'signup')
                    .then(() => {
                        signupForm.submit();
                    })
                    .catch((error) => {
                        console.error('reCAPTCHA verification failed:', error);
                        submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                        submitBtn.disabled = false;
                        isSubmitting = false;

                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: 'Please try again.',
                            confirmButtonColor: '#667eea',
                        });
                    });
                @else
                signupForm.submit();
                @endif
            });
        });

        // reCAPTCHA v3 execution function
        @if(config('services.recaptcha.site_key_v3'))
        function executeRecaptchaV3(form, action) {
            return new Promise((resolve, reject) => {
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', {action: action}).then(function(token) {
                        // Add token to form
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'g-recaptcha-response';
                        tokenInput.value = token;
                        form.appendChild(tokenInput);
                        resolve();
                    }).catch(reject);
                });
            });
        }
        @endif
    </script>
</body>
</html>