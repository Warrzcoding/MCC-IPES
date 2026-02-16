@php
    $error = session('error', '');
    $success = session('success', '');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ID Check - Instructors Performance Evaluation System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
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

        .login-card {
            background: radial-gradient(circle at top left, rgba(255,255,255,0.97), rgba(245,247,255,0.96));
            backdrop-filter: blur(18px);
            border-radius: 24px;
            box-shadow:
                0 12px 35px rgba(41, 98, 255, 0.22),
                0 0 0 1px rgba(255,255,255,0.6);
            padding: 26px 22px 22px;
            max-width: 340px;
            width: 100%;
            transition: transform 0.28s ease, box-shadow 0.28s ease;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(135deg, rgba(102,126,234,0.18), rgba(118,75,162,0.15));
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        .login-card:hover,
        .login-card:focus-within {
            transform: translateY(-3px);
            box-shadow:
                0 18px 45px rgba(41, 98, 255, 0.3),
                0 0 0 1px rgba(255,255,255,0.7);
        }

        .login-card:hover::before,
        .login-card:focus-within::before {
            opacity: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .login-header .logo {
            width: 86px;
            height: 86px;
            background: radial-gradient(circle at 30% 20%, #ffffff 0%, #f3f5ff 45%, #e3e7ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow:
                0 12px 30px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255,255,255,0.8);
            position: relative;
            overflow: hidden; /* ensure image is clipped to circle */
        }

        .login-header .logo::after {
            content: '';
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            border: 1px dashed rgba(102,126,234,0.45);
        }

        .login-header h2 {
            color: #111827;
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 1.25rem;
            letter-spacing: 0.03em;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.7rem;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .login-subtitle {
            text-align: center;
            font-size: 0.72rem;
            color: #4b5563;
            margin-bottom: 18px;
        }

       .form-control, .form-select {
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 11px 14px;
            margin-bottom: 10px;
            font-size: 0.8rem;
            transition: all 0.22s ease;
            position: relative;
            background: #f9fafb;
        }

        .form-control:hover, .form-select:hover,
        .input-group:hover .form-control {
            border-color: #818cf8 !important;
            box-shadow: 0 8px 22px rgba(129, 140, 248, 0.18) !important;
            transform: translateY(-1px);
            background: #ffffff;
        }

        .input-group .form-control:focus:not(:hover)::before, .form-select:focus:not(:hover)::before {
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

        .input-group .form-control:focus:not(:hover) {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }

        .input-group .form-control:focus:not(:hover)::before {
            border-radius: 0 17px 17px 0;
        }

        .input-group:hover .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }

        #school_id {
            text-align: center;
        }

        .btn-primary {
            background-image: linear-gradient(120deg, #4f46e5 0%, #6366f1 40%, #ec4899 100%);
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: all 0.25s ease;
            color: #f9fafb;
            box-shadow:
                0 12px 25px rgba(79, 70, 229, 0.45),
                0 0 0 1px rgba(255,255,255,0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-primary i {
            font-size: 0.8rem;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 0 0, rgba(255,255,255,0.35), transparent 55%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow:
                0 16px 32px rgba(79, 70, 229, 0.5),
                0 0 0 1px rgba(255,255,255,0.5);
            filter: brightness(1.03);
        }

        .btn-primary:hover::after {
            opacity: 1;
        }

        .btn-primary:active {
            transform: translateY(0) scale(0.99);
            box-shadow:
                0 8px 18px rgba(79, 70, 229, 0.35);
        }
      .btn-back-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #4b5563;
            border: none;
            box-shadow: 0 8px 18px rgba(17, 24, 39, 0.15);
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease, color .2s ease;
        }

        .btn-back-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(17, 24, 39, 0.2);
            background: #111827;
            color: #f9fafb;
        }

        .btn-back-icon i {
            font-size: 0.85rem;
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
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            border-right: none;
            border-radius: 16px 0 0 16px;
            color: #4f46e5;
            transition: all 0.22s ease;
            font-size: 0.8rem;
        }

        .input-group:hover .input-group-text {
            border-color: #818cf8 !important;
            background: #e0e7ff !important;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 16px 16px 0;
            margin-bottom: 0;
        }

        .input-hint {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 4px;
            text-align: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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

        #idConfirmModal .modal-footer .btn:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }

        #idConfirmModal .modal-footer .btn[data-bs-dismiss]:hover {
            background: #d5dbea !important;
        }

        #idConfirmModal .modal-footer #thisIsMeBtn:hover {
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.5) !important;
            transform: translateY(-2px);
        }

        /* Enhanced Modal Styles - Glassmorphism */
        .glass-modal .modal-content {
            background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.06));
            backdrop-filter: blur(22px) saturate(140%);
            -webkit-backdrop-filter: blur(22px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 18px 45px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .modal-profile-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.9) 100%);
            padding: 25px 15px;
            text-align: center;
            color: white;
            position: relative;
        }

        .profile-avatar-wrapper {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: 3px solid #fff;
        }

        .profile-avatar-wrapper i {
            font-size: 30px;
            color: #764ba2;
        }

        .modal-body-content {
            padding-top: 45px;
            padding-bottom: 20px;
            text-align: center;
        }

        .user-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 4px;
        }

        .user-id {
            display: inline-block;
            background: rgba(10, 17, 46, 0.86);
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.75rem;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-top: 15px;
            padding: 0 25px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .info-item:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .info-label {
            display: block;
            font-size: 0.65rem;
            color: #636e72;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: #2d3436;
            font-size: 0.85rem;
        }

        .verification-text {
            margin-top: 20px;
            color: #fcfeff;
            font-size: 0.8rem;
        }

        /* Enhanced Button Styles - Smaller & New Colors */
        .btn-modal {
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 0;
            white-space: nowrap;
        }

        .btn-modal i {
            font-size: 0.75rem;
        }

        /* "NOT ME" – small pill, red outline */
        .btn-not-me {
            background: #fff5f5;
            color: #e03131;
            border: 1px solid #ffc9c9;
        }

        .btn-not-me:hover {
            background: #ffe3e3;
            color: #c92a2a;
            border-color: #ffa8a8;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
        }

        /* "THIS IS ME" – small pill, green gradient */
        .btn-this-is-me {
            background: linear-gradient(135deg, #38b000 0%, #2b9348 100%);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(56, 176, 0, 0.35);
        }

        .btn-this-is-me:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(43, 147, 72, 0.4);
            filter: brightness(1.05);
        }

        /* Responsive Design for Mobile/Desktop */
        @media (max-width: 575.98px) {
            .glass-modal .modal-dialog {
                margin: 15px;
                max-width: calc(100% - 30px) !important;
            }

            /* Keep buttons in one horizontal row on mobile */
            #idConfirmModal .modal-footer {
                flex-direction: row;
                justify-content: center;
                align-items: center;
                padding: 10px 14px 14px !important;
                gap: 8px;
            }

            #idConfirmModal .modal-footer .btn-modal {
                width: auto;
                flex: 1 1 0;
                padding: 8px 6px !important;
                font-size: 0.68rem;
            }

            .info-grid {
                padding: 0 15px;
            }
            .user-name {
                font-size: 1.1rem;
            }
            .modal-profile-header {
                padding: 20px 10px;
            }
            .modal-profile-header .modal-title {
                font-size: 0.9rem !important;
            }

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
            .btn, .btn-primary, .btn-success, .btn-secondary, .btn-outline-info {
                font-size: 12px !important;
                padding: 10px 8px !important;
                border-radius: 8px !important;
            }
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

        /* Terms and Conditions Styles */
        .terms-checkbox-container {
            margin-bottom: 15px;
            text-align: left;
            padding: 0 5px;
        }

        .terms-link {
            color: #4f46e5;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }

        .terms-link:hover {
            color: #d0006f;
            text-decoration: underline;
        }

        .form-check-label {
            font-size: 0.75rem;
            color: #4b5563;
        }

        .btn-primary:disabled {
            background: #9ca3af !important;
            box-shadow: none !important;
            cursor: not-allowed;
            opacity: 0.7;
            transform: none !important;
        }

        .terms-content h4 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 1.2rem;
            color: #111827;
        }

        .terms-content p, .terms-content ul li {
            font-size: 0.8rem;
            color: #4b5563;
            line-height: 1.5;
        }

        .terms-content h5 {
            font-size: 0.85rem;
            font-weight: 700;
        }

        /* Smaller modal width for terms */
        #termsModal .modal-dialog {
            max-width: 450px;
        }

        @media (max-width: 575.98px) {
            #termsModal .modal-dialog {
                max-width: 90vw;
                margin: 10px auto;
            }
            .terms-content p, .terms-content ul li {
                font-size: 0.75rem;
            }
            .terms-content h4 {
                font-size: 0.85rem;
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

    @if($error)
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ $error }}',
                confirmButtonColor: '#667eea'
            });
        </script>
    @endif

    @if($success)
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ $success }}',
                confirmButtonColor: '#667eea'
            });
        </script>
    @endif

     <div class="login-card">
        <div class="login-header">
             <div class="logo">
                <img src="{{ asset('images/mccicin.jpg') }}" alt="MCC Logo"
                     style="width: 60%; height: 60%; object-fit: cover; border-radius: 10%;">
            </div>
            <h2>ID Verification</h2>
            <p>STUDENT ACCESS CHECK</p>
        </div>

        <div class="login-subtitle">
            Enter your school ID to verify your eligibility before proceeding to registration.
        </div>

        <form id="idCheckForm">
            @csrf
            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-id-card-alt"></i>
                    </span>
                    <input type="text" class="form-control" id="school_id" name="id_number"
                           placeholder="Enter School-ID" required
                           pattern="[0-9]{4}-[0-9]{4}" maxlength="9"
                           title="Format: 0000-0000 (e.g., 2024-0001)" value="" autocomplete="off">
                </div>
              
            </div>
            
            <!-- Terms and Conditions Checkbox -->
            <div class="terms-checkbox-container">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="acceptTerms" name="accept_terms" required>
                    <label class="form-check-label" for="acceptTerms">
                        I accept and agree to the <span class="terms-link" id="termsLink">Terms and Conditions</span>
                    </label>
                </div>
            </div>

            <button type="button" class="btn btn-primary" id="checkIdBtn" disabled>
                <i class="fas fa-magnifying-glass"></i>&nbsp; Check ID
            </button>

            <div class="mt-2 d-flex justify-content-start">
                <a href="{{ route('login') }}" class="btn-back-icon" aria-label="Back to login">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Footer - Only visible on mobile -->
    <div class="mobile-footer">
        <a href="{{ route('superadmin.login') }}" style="color: #ffffffff; text-decoration: none; font-weight: 600;"><p>&copy;{{ date('Y') }} MCC | Instructors Performance Evaluation System | Developed by: Warren Ilustrisimo | Jenford Albaciete | Jerry Nasol | Cristina Ilustrisimo </p></a>
    </div>

    <div class="modal fade glass-modal" id="idConfirmModal" tabindex="-1" aria-labelledby="idConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
            <div class="modal-content">
                <div class="modal-profile-header">
                    <h5 class="modal-title" id="idConfirmModalLabel" style="font-weight: 700; letter-spacing: 1px; font-size: 1rem;">PROFILE FOUND</h5>
                    <div class="profile-avatar-wrapper">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                
                <div class="modal-body-content">
                    <h2 class="user-name" id="modalFullName"></h2>
                    <span class="user-id" id="modalIdNumber"></span>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Program / Course</span>
                            <span class="info-value" id="modalCourse"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Year Level</span>
                            <span class="info-value" id="modalYear"></span>
                        </div>
                    </div>
                    
                    <p class="verification-text">Is this your correct information?</p>
                </div>

                <div class="modal-footer" style="border: none; padding: 6px 18px 18px; gap: 6px;">
                    <button type="button" class="btn-modal btn-not-me" data-bs-dismiss="modal" style="flex: 1;">
                        <i class="fas fa-times"></i> NOT ME
                    </button>
                    <button type="button" class="btn-modal btn-this-is-me" id="thisIsMeBtn" style="flex: 1;">
                        <i class="fas fa-check"></i> THIS IS ME
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 12px 20px;">
                    <h5 class="modal-title" id="termsModalLabel" style="font-size: 1rem;">
                        <i class="fas fa-file-contract me-2"></i>Terms and Conditions
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 20px 20px;">
                    <div class="terms-content">
                        <h4>1. Acceptance of Terms</h4>
                        <p>By accessing and using the Instructors Performance Evaluation System (IPES) at MCC, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                        
                        <h4>2. System Purpose</h4>
                        <p>IPES is designed exclusively for educational and administrative purposes within Madridejos Community College. The system facilitates performance evaluation, data management, and academic administration for authorized users only.</p>
                        
                        <h4>3. User Eligibility</h4>
                        <p>Access to this system is restricted to:</p>
                        <ul>
                            <li>Current students of MCC</li>
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
                        <p>MCC reserves the right to terminate user accounts for:</p>
                        <ul>
                            <li>Violation of these terms and conditions</li>
                            <li>Misuse of system resources or data</li>
                            <li>End of enrollment or employment with the institution</li>
                        </ul>
                        
                        <h4>9. Changes to Terms</h4>
                        <p>These terms may be updated periodically. Users will be notified of significant changes and continued use constitutes acceptance of modified terms.</p>
                        
                        <h4>10. Contact Information</h4>
                        <p>For questions about these terms or the system, please contact the MCC | IT Department or Administration.</p>
                        
                        <p class="mt-4" style="font-size: 0.75rem;"><strong>Last Updated:</strong> {{ date('F d, Y') }}</p>
                        
                        <!-- Accept Terms Button at the end of content -->
                        <div class="text-center mt-3 pt-2" style="border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-success" id="acceptTermsBtn" style="border-radius: 999px; padding: 8px 25px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);">
                                <i class="fas fa-check me-2"></i>I Accept and Agree Terms and Conditions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Terms and Conditions functionality
        document.addEventListener('DOMContentLoaded', function() {
            const acceptTermsCheckbox = document.getElementById('acceptTerms');
            const checkIdBtn = document.getElementById('checkIdBtn');
            const acceptTermsBtn = document.getElementById('acceptTermsBtn');
            const termsModalElement = document.getElementById('termsModal');
            const termsLink = document.getElementById('termsLink');
            const termsModal = new bootstrap.Modal(termsModalElement);

            let termsAccepted = false;

            // Handle checkbox click
            if (acceptTermsCheckbox) {
                acceptTermsCheckbox.addEventListener('click', function(e) {
                    if (!termsAccepted) {
                        e.preventDefault();
                        this.checked = false;
                        termsModal.show();
                    }
                });
                
                acceptTermsCheckbox.addEventListener('change', function() {
                    if (!this.checked) {
                        termsAccepted = false;
                        checkIdBtn.disabled = true;
                    }
                });
            }

            // Handle terms link click
            if (termsLink) {
                termsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    termsModal.show();
                });
            }

            // Handle accept button in modal
            if (acceptTermsBtn) {
                acceptTermsBtn.addEventListener('click', function() {
                    termsAccepted = true;
                    acceptTermsCheckbox.checked = true;
                    checkIdBtn.disabled = false;
                    termsModal.hide();
                    
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
        });

        // reCAPTCHA v3 execution function
        @if(config('services.recaptcha.site_key_v3'))
        function executeRecaptchaV3(action) {
            return new Promise((resolve, reject) => {
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', {action: action}).then(function(token) {
                        resolve(token);
                    }).catch(reject);
                });
            });
        }
        @endif

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentIdData = null;

        document.getElementById('school_id').addEventListener('input', function(e) {
            let value = e.target.value;

            value = value.replace(/[^0-9-]/g, '');
            let cleanValue = value.replace(/-/g, '');

            if (cleanValue.length > 4) {
                value = cleanValue.substring(0, 4) + '-' + cleanValue.substring(4);
            } else {
                value = cleanValue;
            }

            value = value.substring(0, 9);
            e.target.value = value;
        });

        document.getElementById('school_id').addEventListener('paste', function(e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            paste = paste.replace(/[^0-9-]/g, '');
            this.value = paste;
            this.dispatchEvent(new Event('input'));
        });

        // Function to handle ID check (used by both button click and Enter key)
        async function checkIdNumber() {
            // Check if terms are accepted
            const checkIdBtn = document.getElementById('checkIdBtn');
            if (checkIdBtn.disabled) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms Required',
                    text: 'Please accept the terms and conditions to continue.',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            const idNumber = document.getElementById('school_id').value.trim();

            if (!idNumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required',
                    text: 'Please enter your ID number',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            if (!/^\d{4}-\d{4}$/.test(idNumber)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Format',
                    text: 'ID must be in format: 0000-0000',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            try {
                const response = await fetch('{{ route("idcheck.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ id_number: idNumber })
                });

                const result = await response.json();

                if (result.status === 'found') {
                    currentIdData = result.data;
                    
                    document.getElementById('modalIdNumber').textContent = result.data.id_number;
                    document.getElementById('modalFullName').textContent = result.data.fullname;
                    document.getElementById('modalCourse').textContent = result.data.course;
                    document.getElementById('modalYear').textContent = `${result.data.year} Year`;
                    
                    const modal = new bootstrap.Modal(document.getElementById('idConfirmModal'));
                    modal.show();
                } else if (result.status === 'not_found') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Not Found',
                        text: 'ID not found in our system. Please check your ID number.',
                        confirmButtonColor: '#667eea'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'An error occurred while checking your ID.',
                        confirmButtonColor: '#667eea'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#667eea'
                });
            }
        }

        // Add click event listener to the button
        document.getElementById('checkIdBtn').addEventListener('click', function(e) {
            e.preventDefault();
            checkIdNumber();
        });

        // Add keydown event listener to the input field to trigger on Enter
        document.getElementById('school_id').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkIdNumber();
            }
        });

        document.getElementById('thisIsMeBtn').addEventListener('click', async function() {
            if (currentIdData) {
                try {
                    let requestData = { ...currentIdData };

                    // Add reCAPTCHA token if configured
                    @if(config('services.recaptcha.site_key_v3'))
                    try {
                        const token = await executeRecaptchaV3('idcheck_verify');
                        requestData.recaptcha_token = token;
                    } catch (err) {
                        console.error('reCAPTCHA error:', err);
                    }
                    @endif

                    const response = await fetch('{{ route("idcheck.store_session") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(requestData)
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified',
                            text: `Welcome ${currentIdData.fullname}! You can now proceed to registration.`,
                            confirmButtonColor: '#667eea',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("signup") }}';
                        });
                    } else {
                        // Display the actual error message from the server if available
                        let errorMessage = result.message || 'Verification failed.';
                        if (result.errors) {
                            const firstError = Object.values(result.errors)[0][0];
                            errorMessage += ' ' + firstError;
                        }
                        throw new Error(errorMessage);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to process verification. Please try again.',
                        confirmButtonColor: '#667eea'
                    });
                }
            }
        });
    </script>
</body>
</html>