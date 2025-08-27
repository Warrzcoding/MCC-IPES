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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Office Instructors Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow:
                0 2px 12px 0 rgba(0, 212, 255, 0.10), /* soft subtle cyan */
                0 8px 32px 0 rgba(60, 80, 120, 0.08), /* soft blue-gray */
                0 1.5px 6px 0 rgba(0,0,0,0.07);
            padding: 45px;
            max-width: 450px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .login-card:hover, .login-card:focus-within {
            transform: translateY(-8px) scale(1.03);
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
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        /* Enhanced input styling with green hover and spinning gradient focus */
        .form-control, .form-select {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-size: 16px;
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
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 15px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
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
            border-radius: 15px;
            padding: 12px 20px;
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
        font-size: 15px !important;
        padding: 10px 12px !important;
        border-radius: 10px !important;
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
        font-size: 15px !important;
        padding: 12px 10px !important;
        border-radius: 10px !important;
    }
    
    /* Mobile responsive button layout */
    .d-flex.justify-content-between {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100% !important;
    }
    .student-info, .alert {
        padding: 12px !important;
        font-size: 15px !important;
        border-radius: 10px !important;
    }
    .signup-link {
        font-size: 14px !important;
        padding-top: 10px !important;
    }
    .countdown-timer {
        font-size: 1.2rem !important;
        padding: 10px !important;
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
            padding: 18px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: 18px;
            color: white;
            transition: all 0.3s ease;
            display: none !important; /* Hidden by default */
            cursor: pointer;
        }
        
        /* Only show mobile button when explicitly enabled */
        .mobile-student-btn.show-mobile {
            display: block !important;
        }
        .mobile-student-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .mobile-student-btn i {
            margin-right: 10px;
        }

        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .mobile-student-btn.show-mobile {
                display: block !important;
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
                gap: 8px !important;
            }
            
            /* Make student login buttons more compact and appealing on mobile */
            .d-flex.justify-content-between .btn-secondary,
            .d-flex.justify-content-between .btn-outline-info {
                flex: 1 !important;
                padding: 10px 8px !important;
                font-size: 13px !important;
                border-radius: 12px !important;
                font-weight: 600 !important;
            }
            
            /* Enhance back button styling */
            .btn-secondary {
                background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
                border: none !important;
                color: white !important;
                transition: all 0.3s ease !important;
            }
            
            .btn-secondary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3) !important;
                color: white !important;
            }
            
            /* Enhance forgot password button styling */
            .btn-outline-info {
                background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%) !important;
                border: 2px solid #17a2b8 !important;
                color: #17a2b8 !important;
                transition: all 0.3s ease !important;
            }
            
            .btn-outline-info:hover {
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
    <!--Id checking Alert-->
    @if (session('id_error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'ID Not Found',
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
                showConfirmButton: false
            });
        </script>
    @endif
    <!--ends of Id check alert-->
        <!--Bubbles bg-->
       <div class="bg-decorations">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
       </div>

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
                <img src="{{ asset('images/mcclogo.png') }}" alt="MCC Logo" class="login-logo-img" style="width: 120px; height: 120px; object-fit: contain; padding: 5px; max-width: 100%; height: auto;">
            </div>
            <h2 style="margin-top: 0;">MCC | IPES</h2>
            <p>Office Instructor's Evaluation System</p>
        </div>

        

            @if (!$show_student_form && !$show_login_form)

                <!-- Mobile Student Login Button (Visible only on mobile) -->
                <button type="button" class="mobile-student-btn" onclick="startMobileStudentLogin()">
                    <i class="fas fa-graduation-cap"></i> Start Student Login
                </button>

                <!-- Desktop User Type Selection (Hidden on mobile) -->
                <div class="desktop-user-select">
                    <form method="POST" id="userTypeForm">
                        <div class="mb-4">
                            <label for="user_type" class="form-label">
                                <i class="fas fa-user-tag"></i> Select User Type
                            </label>
                            <select class="form-select" id="user_type" name="user_type" onchange="handleUserTypeChange()" required>
                                <option value="">Choose your role...</option>
                                <option value="student">Student</option>
                                <option value="admin">Administrator</option>
                                <!--<option value="staff">Staff</option>-->
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
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <!-- Student Signup Link -->
                    <div class="signup-link">
                        <p>Don't have an account? <a href="{{ route('pre_signup') }}">
                            <i class="fas fa-user-plus"></i> Sign up here
                        </a></p>
                    </div>
                </form>

                <!-- Admin Login Form (Initially Hidden) -->
                <form method="POST" id="adminLoginForm" style="display: none;" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="user_type" value="admin">
                      <div id="adminErrorBox" class="alert alert-danger d-none" style="border-radius:12px; padding:10px; margin-bottom:15px; display: flex; align-items: center; justify-content: center; text-align: center; font-weight: 600;"></div>                 
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                            <input type="email" class="form-control" id="admin_email" name="email"
                                   placeholder="Enter your email" value="" autocomplete="off">
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
                                   placeholder="Enter your password" value="" autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login as Administrator
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </form>

                <!-- Staff Login Form (Initially Hidden) -->
                <form method="POST" id="staffLoginForm" style="display: none;" action="{{ route('login.submit') }}">
                    @csrf
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
                    </div>

                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login as Staff
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </form>
            @endif

            @if ($show_login_form && $student_data)
            
                <!-- Student Login Form -->
                <form method="POST" id="studentID" action="{{ route('login.submit') }}">
                    @csrf
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
                                   placeholder="firstname.lastname@mcclawis.edu.ph" 
                                   value="{{ session('verified_student_email', '') }}" autocomplete="off"
                                   pattern="^[a-zA-Z]+\.[a-zA-Z]+@mcclawis\.edu\.ph$"
                                   title="Email must be in the format firstname.lastname@mcclawis.edu.ph">
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
                                   placeholder="Enter your password" value="" autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login as Student
                    </button>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="{{ route('clear.student.verification') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('password.request') }}" class="btn btn-outline-info">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>
                </form>

                <!-- Student Signup Link -->
                <div class="signup-link">
                   <p>Don't have an account? <a href="{{ route('pre_signup', ['type' => 'student', 'school_id' => $student_data['school_id']]) }}">
                        <i class="fas fa-user-plus"></i> Sign up here
                    </a></p>
                </div>
            @endif      
    </div>

    @if(session('lockout_timer'))
        <div id="lockoutOverlay" style="
            position: fixed; z-index: 9999; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(102,126,234,0.95); display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: #fff; font-size: 1.5rem; text-align: center;">
            <div style="background: rgba(255,255,255,0.1); padding: 40px 30px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
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

    @if (request('login_success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Login Successful!',
    text: 'Welcome!',
    timer: 1800,
    timerProgressBar: true,
    showConfirmButton: false,
    willClose: () => {
        window.location.href = '{{ route("dashboard") }}';
    }
});
</script>
@endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      

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

        // SweetAlert for login errors
        @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '{{ session('error') }}',
            confirmButtonColor: '#667eea',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: true
        });
        @endif

        // Add form submission handling for better UX
        document.addEventListener('DOMContentLoaded', function() {
        // Custom validation for admin login
        const adminLoginForm = document.getElementById('adminLoginForm');
        if (adminLoginForm) {
            adminLoginForm.addEventListener('submit', function(e) {
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
            
            const loginForms = document.querySelectorAll('form[action*="login"]');
            
            loginForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                    submitBtn.disabled = true;
                    
                    // Re-enable button after 5 seconds if form doesn't submit
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                });
            });
        });

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
                userTypeForm.style.display = 'none';
                studentIdForm.style.display = 'block';
                adminLoginForm.style.display = 'none';
                staffLoginForm.style.display = 'none';
                document.getElementById('school_id').focus();
            } else if (userType === 'admin') {
                userTypeForm.style.display = 'none';
                adminLoginForm.style.display = 'block';
                staffLoginForm.style.display = 'none';
                studentIdForm.style.display = 'none';
                document.getElementById('admin_email').focus();
            } else if (userType === 'staff') {
                userTypeForm.style.display = 'none';
                staffLoginForm.style.display = 'block';
                adminLoginForm.style.display = 'none';
                studentIdForm.style.display = 'none';
                document.getElementById('staff_email').focus();
            }
        }

        function clearAllForms() {
            // Clear all input fields
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], select');
            inputs.forEach(input => {
                input.value = '';
            });
            
            // Reset select dropdowns
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });
        }

        function resetForm() {
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
            adminEmailInput.setAttribute('maxlength', '30');
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
                // Limit to 20 chars
                if (value.length > 30) value = value.substring(0, 30);
                this.value = value;
            });
        }

        // Student Email Input Sanitization and Auto-complete
        const studentEmailInput = document.getElementById('email');
        if (studentEmailInput) {
            studentEmailInput.setAttribute('maxlength', '40');
            studentEmailInput.addEventListener('input', function(e) {
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
                // Auto-complete @mcclawis.edu.ph
                if (value.endsWith('@')) {
                    value += 'mcclawis.edu.ph';
                }
                // Limit to 20 chars
                if (value.length > 40) value = value.substring(0, 40);
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
    </script>
  <script src="{{ asset('js/dev-tools-security.js') }}?v=<?php echo time(); ?>"></script>
</body>
</html>