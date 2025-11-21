<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Sign Up - Office Performance Evaluation System</title>
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
        body {
            background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            perspective: 1000px; /* adds depth for 3D transforms */
            position: relative;
            overflow: hidden;
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
        .signup-card {
            --signup-scale: 0.8;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(102,126,234,0.10), 0 1.5px 8px rgba(0,0,0,0.06);
            border: 1.5px solid #e9ecef;
            padding: calc(32px * var(--signup-scale)) calc(24px * var(--signup-scale)) calc(28px * var(--signup-scale)) calc(24px * var(--signup-scale));
            max-width: calc(420px * var(--signup-scale));
            width: min(100%, calc(420px * var(--signup-scale)));
            margin: 0 auto;
            min-height: calc(100vh - 48px);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative; /* ensure it layers above background */
            font-size: calc(1rem * var(--signup-scale));
        }
        @media (min-width: 992px) {
            .signup-card {
                --signup-scale: 0.8;
                min-height: auto;
                max-height: none;
                height: auto;
                overflow: hidden;
            }
        }
        .signup-header {
            text-align: center;
            margin-bottom: calc(18px * var(--signup-scale));
        }
        .signup-header .logo {
            width: calc(44px * var(--signup-scale));
            height: calc(44px * var(--signup-scale));
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto calc(10px * var(--signup-scale));
            font-size: calc(1.3rem * var(--signup-scale));
            color: white;
        }
        .signup-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: calc(6px * var(--signup-scale));
            font-size: calc(1.2rem * var(--signup-scale));
        }
        .signup-header p {
            color: #666;
            font-size: calc(13px * var(--signup-scale));
        }
        .step-indicator-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            position: relative;
            margin-bottom: calc(0.5rem * var(--signup-scale));
            width: 100%;
            max-width: calc(140px * var(--signup-scale)); /* reduced for closer circles */
            margin-left: auto;
            margin-right: auto;
        }
        .step-circle {
            width: calc(22px * var(--signup-scale));
            height: calc(22px * var(--signup-scale));
            border-radius: 50%;
            background: #e9ecef;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: calc(0.98rem * var(--signup-scale));
            transition: background 0.2s, color 0.2s;
            position: relative;
            z-index: 2;
        }
        .step-circle.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 1px 4px rgba(102,126,234,0.13);
        }
        .step-connector {
            flex: 1 1 0;
            height: calc(3px * var(--signup-scale));
            background: #e0e6f7;
            margin: 0 calc(1px * var(--signup-scale));
            position: relative;
            top: 0;
            z-index: 1;
            border-radius: calc(2px * var(--signup-scale));
            transition: background 0.3s;
        }
        .step-connector.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-horizontal {
            width: 100%;
            max-width: calc(360px * var(--signup-scale));
            margin: 0 auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: calc(1rem * var(--signup-scale));
        }
        .form-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: calc(12px * var(--signup-scale));
            width: 100%;
            margin-top: calc(1.2rem * var(--signup-scale));
            pointer-events: auto;
        }
        .form-navigation .nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: calc(6px * var(--signup-scale));
            font-weight: 600;
            letter-spacing: calc(0.2px * var(--signup-scale));
            border-radius: calc(10px * var(--signup-scale));
            padding: calc(0.4rem * var(--signup-scale)) calc(0.95rem * var(--signup-scale));
            min-height: calc(46px * var(--signup-scale));
            flex: 0 0 auto;
            min-width: calc(108px * var(--signup-scale));
            transition: all 0.2s ease;
            pointer-events: auto;
            cursor: pointer;
        }
        .form-navigation .nav-btn i {
            font-size: calc(1rem * var(--signup-scale));
        }
        .form-navigation .nav-btn-icon-only {
            display: inline-flex;
            width: calc(44px * var(--signup-scale));
            min-width: calc(44px * var(--signup-scale));
            height: calc(44px * var(--signup-scale));
            padding: 0;
            border-radius: 50%;
            flex: 0 0 calc(44px * var(--signup-scale));
        }
        .form-navigation .nav-btn-fill {
            flex: 1 1 auto;
            min-width: 0;
        }
        .form-navigation .nav-btn-submit {
            padding: calc(0.35rem * var(--signup-scale)) calc(0.85rem * var(--signup-scale));
            min-height: calc(44px * var(--signup-scale));
            font-size: calc(0.96rem * var(--signup-scale));
            pointer-events: auto;
            cursor: pointer;
            touch-action: manipulation;
        }
        .form-horizontal .form-label {
            font-size: calc(0.98rem * var(--signup-scale));
            font-weight: 600;
            color: #333;
            margin-bottom: calc(0.2rem * var(--signup-scale));
        }
        .form-horizontal .form-control, .form-horizontal .form-select {
            border-radius: calc(10px * var(--signup-scale));
            border: 1.5px solid #e9ecef;
            padding: calc(14px * var(--signup-scale)) calc(16px * var(--signup-scale));
            font-size: calc(15px * var(--signup-scale));
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .form-horizontal .form-control:focus, .form-horizontal .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 calc(0.15rem * var(--signup-scale)) rgba(102, 126, 234, 0.15), 0 4px 8px rgba(0,0,0,0.05);
            outline: none;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            font-size: calc(15px * var(--signup-scale));
            padding: calc(14px * var(--signup-scale)) 0;
            border-radius: calc(10px * var(--signup-scale));
            margin-bottom: calc(8px * var(--signup-scale));
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            box-shadow: 0 1px 4px rgba(102,126,234,0.10);
        }
        .btn-primary:active {
            background: #667eea;
        }
        .btn-secondary {
            background: #f1f3f5;
            color: #333;
            border: none;
        }
        .signup-link {
            font-size: calc(0.98rem * var(--signup-scale));
            margin-top: calc(10px * var(--signup-scale));
            padding-top: calc(10px * var(--signup-scale));
            border-top: 1px solid #e9ecef;
            color: #667eea;
            width: 100%;
            text-align: center;
        }
        .signup-link a {
            color: #667eea;
            font-weight: 600;
        }
        .signup-link a:hover {
            color: #764ba2;
        }
        .alert {
            font-size: calc(0.98rem * var(--signup-scale));
            border-radius: calc(8px * var(--signup-scale));
            padding: calc(8px * var(--signup-scale)) calc(10px * var(--signup-scale));
            margin-bottom: calc(8px * var(--signup-scale));
            box-shadow: 0 1px 3px rgba(102,126,234,0.07);
        }
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%);
            color: #fff;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: #fff;
        }
        .compact-image-upload {
            display: flex;
            align-items: center;
            gap: calc(15px * var(--signup-scale));
            margin-bottom: 0;
        }
        .image-preview-compact {
            flex-shrink: 0;
        }
        .image-preview-compact img {
            width: calc(60px * var(--signup-scale)) !important;
            height: calc(60px * var(--signup-scale)) !important;
            border-radius: 50%;
            border: calc(2px * var(--signup-scale)) solid #667eea;
            object-fit: cover;
            background: #f8f9fa;
        }
        .upload-button-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: calc(8px * var(--signup-scale));
        }
        .upload-buttons-row {
            display: flex;
            gap: calc(8px * var(--signup-scale));
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }
        .upload-btn {
            border-color: #667eea;
            color: #667eea;
            font-size: calc(12px * var(--signup-scale));
            padding: calc(6px * var(--signup-scale)) calc(14px * var(--signup-scale));
            border-radius: calc(6px * var(--signup-scale));
            transition: all 0.3s ease;
            cursor: pointer;
            flex: 0 0 auto;
            min-width: calc(160px * var(--signup-scale));
            text-align: center;
        }
        .upload-btn:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .upload-btn i {
            margin-right: calc(4px * var(--signup-scale));
        }

        .password-strength {
            margin-top: calc(6px * var(--signup-scale));
            margin-bottom: calc(2px * var(--signup-scale));
            height: calc(7px * var(--signup-scale));
            width: 100%;
            border-radius: calc(4px * var(--signup-scale));
            background: #e9ecef;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: calc(4px * var(--signup-scale));
            transition: width 0.3s, background 0.3s;
        }
        .password-strength-text {
            font-size: calc(0.92rem * var(--signup-scale));
            margin-top: calc(2px * var(--signup-scale));
            font-weight: 600;
        }
        .password-suggestion {
            font-size: calc(0.85rem * var(--signup-scale));
            color: #b71c1c;
            margin-top: calc(2px * var(--signup-scale));
        }
        .password-match-indicator {
            font-size: calc(0.92rem * var(--signup-scale));
            margin-top: calc(4px * var(--signup-scale));
            font-weight: 600;
            min-height: calc(18px * var(--signup-scale));
        }
        .password-match-indicator.match {
            color: #51cf66;
        }
        .password-match-indicator.mismatch {
            color: #ff6b6b;
        }
        .validation-feedback {
            font-size: calc(0.92rem * var(--signup-scale));
            margin-top: calc(4px * var(--signup-scale));
            font-weight: 600;
            min-height: calc(18px * var(--signup-scale));
        }
        .validation-feedback.valid {
            color: #51cf66;
        }
        .validation-feedback.invalid {
            color: #ff6b6b;
        }
        .form-control.is-valid {
            border-color: #51cf66;
        }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #ff6b6b;
        }
        .form-control.validation-error, .form-select.validation-error {
            border-color: #ff6b6b !important;
            box-shadow: 0 0 0 0.15rem rgba(255, 107, 107, 0.15) !important;
        }
        
        /* Tablet and medium screens */
        @media (max-width: 768px) and (min-width: 481px) {
            .signup-card {
                --signup-scale: 0.8;
                padding: 16px 12.8px;
                max-width: 90vw;
                padding-bottom: 80px;
                overflow-y: scroll;
            }
            .form-horizontal {
                max-width: 100%;
            }
            .form-horizontal .form-control, .form-horizontal .form-select {
                padding: 9.6px 11.2px;
            }
            .form-navigation {
                pointer-events: auto !important;
                z-index: 100;
            }
            .form-navigation .nav-btn,
            .form-navigation .nav-btn-submit {
                pointer-events: auto !important;
                cursor: pointer !important;
                touch-action: manipulation !important;
            }
        }
        
        @media (max-width: 480px) {
            .signup-card {
                --signup-scale: 0.8;
                padding: 12.8px 9.6px;
                max-width: 95vw;
                min-height: 240px;
                margin: 8px auto;
                padding-bottom: 72px;
                overflow-y: scroll;
            }
            .form-navigation {
                pointer-events: auto !important;
                z-index: 100;
            }
            .form-navigation .nav-btn,
            .form-navigation .nav-btn-submit {
                pointer-events: auto !important;
                cursor: pointer !important;
                touch-action: manipulation !important;
                -webkit-user-select: none !important;
                user-select: none !important;
            }
            .signup-header .logo {
                width: 25.6px;
                height: 25.6px;
                font-size: 0.8rem;
                margin-bottom: 6.4px;
            }
            .signup-header h2 {
                font-size: 0.8rem;
                margin-bottom: 3.2px;
            }
            .signup-header p {
                font-size: 9.6px;
            }
            .step-indicator-wrapper {
                max-width: 96px;
            }
            .step-circle {
                width: 14.4px;
                height: 14.4px;
                font-size: 0.6rem;
            }
            .step-connector {
                height: 1.6px;
                margin: 0 1.6px;
            }
            .form-horizontal {
                max-width: 100%;
                gap: 0.64rem;
            }
            .form-horizontal .form-label {
                font-size: 0.72rem;
                margin-bottom: 0.24rem;
            }
            .form-horizontal .form-control, .form-horizontal .form-select {
                font-size: 11.2px;
                padding: 9.6px 11.2px;
                border-radius: 6.4px;
            }
            .btn-primary, .btn-secondary {
                font-size: 11.2px;
                padding: 9.6px 0;
                border-radius: 6.4px;
            }
            .image-preview-compact img {
                width: 40px !important;
                height: 40px !important;
            }
            .upload-btn {
                font-size: 8.8px;
                padding: 4px 6.4px;
            }
            .upload-buttons-row {
                gap: 6px;
            }
            .camera-container {
                padding: 15px;
                max-width: 95%;
            }
            .camera-video {
                max-width: 250px;
                height: 150px;
            }
            .camera-controls {
                flex-direction: column;
                gap: 8px;
            }
            .camera-btn {
                width: 100%;
            }
        }
        
        /* Extra small screens (phones under 360px) */
        @media (max-width: 360px) {
            .signup-card {
                --signup-scale: 0.8;
                overflow-y: scroll;
            }
            .form-navigation {
                pointer-events: auto !important;
                z-index: 100;
            }
            .form-navigation .nav-btn,
            .form-navigation .nav-btn-submit {
                pointer-events: auto !important;
                cursor: pointer !important;
                touch-action: manipulation !important;
            }
            .upload-buttons-row {
                gap: 4px;
            }
            .camera-video {
                max-width: 200px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-cubes">
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
    <div class="signup-card">
        <div class="signup-header">
            <div class="logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Sign Up</h2>
            <p>Create your account for the Instructors Performance Evaluation System</p>
        </div>

        <!-- Step Indicator -->
        <div class="mb-4 text-center">
            <div class="step-indicator-wrapper" id="stepIndicator">
                <div class="step-circle" id="stepCircle1">1</div>
                <div class="step-connector" id="stepConnector1"></div>
                <div class="step-circle" id="stepCircle2">2</div>
                <div class="step-connector" id="stepConnector2"></div>
                <div class="step-circle" id="stepCircle3">3</div>
            </div>
            <div style="margin-top: calc(6px * var(--signup-scale)); font-size: calc(0.98rem * var(--signup-scale)); color: #888;">
                <span id="stepLabel">Personal Info</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('signup.submit') }}" enctype="multipart/form-data" class="form-horizontal" id="signupForm">
            @csrf
            <!-- Step 1: Personal Info -->
            <div class="step-group" id="step1">
                <div class="mb-3">
                    <!-- Compact Image Upload -->
                    <div class="compact-image-upload">
                        <div id="imagePreview" class="image-preview-compact">
                            <img id="previewImg" src="https://ui-avatars.com/api/?name=Profile&background=cccccc&color=555555&rounded=true&size=70" alt="Preview">
                        </div>
                        <div class="upload-button-container">
                              <p class="text-muted small mb-0" style="line-height: 1.4;">Upload photo (JPEG or PNG, max 2 MB).</p>                                                              
                            <div class="upload-buttons-row">
                                <label for="profileImageInput" class="btn btn-outline-primary btn-sm upload-btn">
                                    <i class="fas fa-folder-open"></i> Choose File
                                </label>
                            </div>
                          
                            <input type="file" class="d-none @error('profile_image') is-invalid @enderror"  name="profile_image" accept="image/*" id="profileImageInput">
                            @error('profile_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                           id="full_name" name="full_name" value="{{ old('full_name') }}" required
                           pattern="[A-Za-z\s]+" maxlength="30"
                           placeholder="Full Name" title="Only letters (including capital letters) and spaces allowed (max 30 characters)">
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user-tag"></i> Username
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                           id="username" name="username" value="{{ old('username') }}" required
                           pattern="[A-Za-z0-9]+" maxlength="20"
                           placeholder="Username" title="Only letters (including capital letters) and numbers allowed (max 20 characters)">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> {{ isset($verified_email) && $verified_email ? 'MS Account' : 'Email' }}
                    </label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ isset($verified_email) && $verified_email ? $verified_email : old('email') }}"
                           required
                           placeholder="Email Address"
                           @if(isset($verified_email) && $verified_email) readonly @endif>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Step 2: School Info -->
            <div class="step-group" id="step2" style="display:none;">
                <div class="mb-3">
                    <label for="school_id" class="form-label">
                        <i class="fas fa-id-card"></i> School ID
                    </label>
                    <input type="text" class="form-control @error('school_id') is-invalid @enderror"
                           id="school_id" name="school_id" value="{{ old('school_id') }}" required
                           pattern="\d{4}-\d{4}" maxlength="9" inputmode="numeric"
                           placeholder="School ID" title="Format: 0000-0000 (numbers only)">
                    @error('school_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label">
                        <i class="fas fa-book"></i> Course
                    </label>
                    <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                        <option value="">Select course...</option>
                        <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BSHM" {{ old('course') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                        <option value="BSBA" {{ old('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                        <option value="BSED" {{ old('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                        <option value="BEED" {{ old('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                    </select>
                    @error('course')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="year_level" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Year Level
                    </label>
                    <select class="form-select @error('year_level') is-invalid @enderror" id="year_level" name="year_level" required>
                        <option value="">Select year level...</option>
                        <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">
                        <i class="fas fa-users"></i> Section
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
            
            <!-- Step 3: Password -->
            <div class="step-group" id="step3" style="display:none;">
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required maxlength="25"
                           placeholder="Password">
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                    <div class="password-suggestion" id="passwordSuggestion" style="display:none;">Must have 8+ characters, uppercase, lowercase, numbers, and symbols (max 25).</div>
                    <div id="passwordRequirements" class="form-text mt-1" style="display:none;">
                        <ul class="mb-0 small">
                            <li id="req-length" class="text-danger">At least 8 characters</li>
                            <li id="req-upper" class="text-danger">At least 1 uppercase letter</li>
                            <li id="req-lower" class="text-danger">At least 1 lowercase letter</li>
                            <li id="req-number" class="text-danger">At least 1 number</li>
                            <li id="req-symbol" class="text-danger">At least 1 symbol</li>
                        </ul>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation" required
                           placeholder="Confirm Password">
                    <div id="passwordMatchIndicator" class="password-match-indicator"></div>
                </div>
            </div>
            <!-- Navigation Buttons -->
            <div class="form-navigation">
                <button type="button" class="btn btn-secondary nav-btn" id="prevBtn" aria-label="Go to previous step">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-primary nav-btn nav-btn-fill" id="nextBtn" aria-label="Go to next step">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-primary nav-btn nav-btn-fill nav-btn-submit" id="submitBtn">
                    <i class="fas fa-user-plus"></i> Submit Request
                </button>
            </div>
        </form>
        <div class="signup-link">
            <p>Already have an account? <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i> Login here
            </a></p>
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
            // Profile image preview functionality
            const profileImageInput = document.getElementById('profileImageInput');
            const imagePreview = document.getElementById('imagePreview');
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
                        if (!file.type.match('image.*')) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Please select an image file (JPG, PNG, GIF)',
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
                    } else {
                        // Reset to default avatar
                        previewImg.src = 'https://ui-avatars.com/api/?name=Profile&background=cccccc&color=555555&rounded=true&size=70';
                    }
                });
            }

            // Camera functionality

            const schoolIdInput = document.getElementById('school_id');
            let schoolIdDuplicate = false;
            if (schoolIdInput) {
                schoolIdInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9-]/g, '');
                    if (value.length > 9) value = value.slice(0, 9);
                    value = value.replace(/^([0-9]{4})(?!-)/, '$1-');
                    value = value.replace(/(.*-).*-/, '$1');
                    this.value = value;
                });
                // AJAX check for duplicate on blur
                schoolIdInput.addEventListener('blur', function() {
                    const schoolId = this.value;
                    if (schoolId.length === 9) {
                        fetch('/check-duplicate-id', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ school_id: schoolId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.exists) {
                                schoolIdDuplicate = true;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Duplicate School ID',
                                    text: 'This School ID is already registered or pending approval.',
                                    confirmButtonColor: '#667eea',
                                });
                            } else {
                                schoolIdDuplicate = false;
                            }
                        });
                    }
                });
            }
            // Multi-step form logic
            let currentStep = 1;
            const totalSteps = 3;
            const stepGroups = [
                document.getElementById('step1'),
                document.getElementById('step2'),
                document.getElementById('step3')
            ];
            const stepCircles = [
                document.getElementById('stepCircle1'),
                document.getElementById('stepCircle2'),
                document.getElementById('stepCircle3')
            ];
            const stepConnectors = [
                document.getElementById('stepConnector1'),
                document.getElementById('stepConnector2')
            ];
            const stepLabels = ['Personal Info', 'School Info', 'Password'];
            const stepLabel = document.getElementById('stepLabel');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            function showStep(step) {
                stepGroups.forEach((group, idx) => {
                    group.style.display = (idx === step - 1) ? '' : 'none';
                });
                stepCircles.forEach((circle, idx) => {
                    if (idx === step - 1) {
                        circle.classList.add('active');
                    } else {
                        circle.classList.remove('active');
                    }
                });
                // Color connectors
                stepConnectors.forEach((connector, idx) => {
                    if (step - 1 > idx) {
                        connector.classList.add('active');
                    } else {
                        connector.classList.remove('active');
                    }
                });
                stepLabel.textContent = stepLabels[step - 1];

                const isIntermediateStep = step > 1 && step < totalSteps;

                // Previous button
                const showPrev = step > 1;
                prevBtn.style.display = showPrev ? 'inline-flex' : 'none';
                prevBtn.classList.toggle('nav-btn-icon-only', showPrev);
                prevBtn.classList.remove('nav-btn-fill');
                prevBtn.classList.toggle('btn-outline-primary', showPrev);
                prevBtn.classList.toggle('btn-secondary', !showPrev);
                prevBtn.innerHTML = showPrev
                    ? '<i class="fas fa-arrow-left"></i>'
                    : '<i class="fas fa-arrow-left"></i> Back';

                // Next button
                const showNext = step < totalSteps;
                nextBtn.style.display = showNext ? 'inline-flex' : 'none';
                nextBtn.classList.toggle('nav-btn-icon-only', isIntermediateStep);
                nextBtn.classList.toggle('nav-btn-fill', !isIntermediateStep && showNext);
                nextBtn.setAttribute('aria-label', showNext ? 'Go to next step' : 'Next step hidden');
                nextBtn.innerHTML = isIntermediateStep
                    ? '<i class="fas fa-arrow-right"></i>'
                    : 'Next <i class="fas fa-arrow-right"></i>';

                // Submit button
                const showSubmit = step === totalSteps;
                submitBtn.style.display = showSubmit ? 'inline-flex' : 'none';
                submitBtn.classList.remove('nav-btn-icon-only');
                submitBtn.classList.toggle('nav-btn-fill', showSubmit);
            }
            prevBtn.addEventListener('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            nextBtn.addEventListener('click', function() {
                if (currentStep < totalSteps) {
                    // Validate current step before proceeding
                    if (validateCurrentStep()) {
                        currentStep++;
                        showStep(currentStep);
                    }
                }
            });
            showStep(currentStep);

            // Password strength meter
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            const suggestion = document.getElementById('passwordSuggestion');
            const confirmInput = document.getElementById('password_confirmation');
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
                let requirements = {
                    length: pw.length >= 8 && pw.length <= 25,
                    uppercase: /[A-Z]/.test(pw),
                    lowercase: /[a-z]/.test(pw),
                    numbers: /[0-9]/.test(pw),
                    symbols: /[^A-Za-z0-9]/.test(pw)
                };
                
                // All requirements must be met for strong password
                if (requirements.length) score++;
                if (requirements.uppercase) score++;
                if (requirements.lowercase) score++;
                if (requirements.numbers) score++;
                if (requirements.symbols) score++;
                
                return {
                    score: score,
                    requirements: requirements,
                    isStrong: score === 5 // All 5 requirements must be met
                };
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
                const pw = passwordInput.value;
                const result = checkPasswordStrength(pw);
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
                
                // Calculate width based on score (0-5)
                width = (result.score / 5 * 100) + '%';
                
                if (result.score <= 2) {
                    color = '#ff6b6b';
                    text = 'Weak';
                    suggestion.style.display = '';
                } else if (result.score <= 4) {
                    color = '#ffd43b';
                    text = 'Medium';
                    suggestion.style.display = '';
                } else if (result.isStrong) {
                    color = '#51cf66';
                    text = 'Strong';
                    suggestion.style.display = 'none';
                } else {
                    color = '#ffd43b';
                    text = 'Medium';
                    suggestion.style.display = '';
                }
                
                strengthBar.style.width = width;
                strengthBar.style.background = color;
                strengthText.textContent = text;
                strengthText.style.color = color;
            }
            function updatePasswordMatch() {
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
                passwordInput.addEventListener('input', (e) => {
                    updateStrengthMeter();
                    updatePasswordMatch();
                    updateRequirements(passwordInput.value);
                    // Limit password length to 25 characters
                    if (e.target.value.length > 25) {
                        e.target.value = e.target.value.substring(0, 25);
                    }
                });
            }
            if (confirmInput) {
                confirmInput.addEventListener('input', updatePasswordMatch);
            }

            // Full Name input filtering - only allow letters and spaces
            const fullNameInput = document.getElementById('full_name');
            
            function filterFullNameInput(e) {
                // Allow backspace, delete, tab, escape, enter
                if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Ensure that it is a letter (including capital letters) or space and stop the keypress
                if ((e.keyCode < 65 || e.keyCode > 90) && e.keyCode !== 32) {
                    e.preventDefault();
                }
            }

            function filterFullNamePaste(e) {
                // Get pasted data
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                // Filter out non-letters (including capital letters) and non-spaces
                paste = paste.replace(/[^A-Za-z\s]/g, '');
                // Limit to 30 characters
                if (paste.length > 30) {
                    paste = paste.substring(0, 30);
                }
                // Set the filtered value
                e.target.value = paste;
                e.preventDefault();
            }

            // Username input filtering - only allow letters and numbers
            const usernameInput = document.getElementById('username');
            
            function filterUsernameInput(e) {
                // Allow backspace, delete, tab, escape, enter
                if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Ensure that it is a letter (including capital letters) or number and stop the keypress
                if ((e.keyCode < 48 || e.keyCode > 90) || (e.keyCode > 57 && e.keyCode < 65)) {
                    e.preventDefault();
                }
            }

            function filterUsernamePaste(e) {
                // Get pasted data
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                // Filter out non-alphanumeric characters (including capital letters)
                paste = paste.replace(/[^A-Za-z0-9]/g, '');
                // Limit to 20 characters
                if (paste.length > 20) {
                    paste = paste.substring(0, 20);
                }
                // Set the filtered value
                e.target.value = paste;
                e.preventDefault();
            }

            // Step validation function
            function validateCurrentStep() {
                let emptyFields = [];
                let firstEmptyField = null;
                let fieldsToCheck = [];

                // Clear previous validation errors
                document.querySelectorAll('.validation-error').forEach(field => {
                    field.classList.remove('validation-error');
                });

                if (currentStep === 1) {
                    // Step 1: Personal Info validation
                    const fullName = document.getElementById('full_name');
                    const username = document.getElementById('username');
                    const email = document.getElementById('email');

                    fieldsToCheck = [
                        { field: fullName, name: 'Full Name' },
                        { field: username, name: 'Username' },
                        { field: email, name: 'Email' }
                    ];
                } else if (currentStep === 2) {
                    // Step 2: School Info validation
                    const schoolId = document.getElementById('school_id');
                    const course = document.getElementById('course');
                    const yearLevel = document.getElementById('year_level');
                    const section = document.getElementById('section');

                    fieldsToCheck = [
                        { field: schoolId, name: 'School ID' },
                        { field: course, name: 'Course' },
                        { field: yearLevel, name: 'Year Level' },
                        { field: section, name: 'Section' }
                    ];
                } else if (currentStep === 3) {
                    // Step 3: Password validation
                    const password = document.getElementById('password');
                    const passwordConfirmation = document.getElementById('password_confirmation');

                    fieldsToCheck = [
                        { field: password, name: 'Password' },
                        { field: passwordConfirmation, name: 'Confirm Password' }
                    ];
                }

                // Check each field for empty values
                fieldsToCheck.forEach(item => {
                    if (!item.field.value.trim()) {
                        emptyFields.push(item.name);
                        item.field.classList.add('validation-error');
                        if (!firstEmptyField) firstEmptyField = item.field;
                    }
                });

                // If there are empty fields, show error and focus on first empty field
                if (emptyFields.length > 0) {
                    let errorMessage = emptyFields.length === 1 
                        ? `Please fill in the ${emptyFields[0]} field.`
                        : `Please fill in the following fields: ${emptyFields.join(', ')}.`;

                    Swal.fire({
                        icon: 'error',
                        title: 'Empty Fields Error',
                        text: errorMessage,
                        confirmButtonColor: '#667eea',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then(() => {
                        // Auto-focus on the first empty field with a small delay
                        setTimeout(() => {
                            if (firstEmptyField) {
                                firstEmptyField.focus();
                                firstEmptyField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 100);
                    });
                    return false;
                }

                return true;
            }

            // Simple validation functions for form submission
            function validateFullName() {
                const value = fullNameInput.value.trim();
                return /^[A-Za-z\s]+$/.test(value) && value.length <= 30 && value.length > 0;
            }

            function validateUsername() {
                const value = usernameInput.value.trim();
                return /^[A-Za-z0-9]+$/.test(value) && value.length <= 20 && value.length > 0;
            }

            // Function to remove validation error styling
            function removeValidationError(field) {
                field.classList.remove('validation-error');
            }

            // Add event listeners for input filtering and validation error removal
            if (fullNameInput) {
                fullNameInput.addEventListener('keydown', filterFullNameInput);
                fullNameInput.addEventListener('paste', filterFullNamePaste);
                fullNameInput.addEventListener('input', function(e) {
                    // Remove validation error when user starts typing
                    removeValidationError(e.target);
                    // Limit length during input
                    if (e.target.value.length > 30) {
                        e.target.value = e.target.value.substring(0, 30);
                    }
                });
            }
            
            if (usernameInput) {
                usernameInput.addEventListener('keydown', filterUsernameInput);
                usernameInput.addEventListener('paste', filterUsernamePaste);
                usernameInput.addEventListener('input', function(e) {
                    // Remove validation error when user starts typing
                    removeValidationError(e.target);
                    // Limit length during input
                    if (e.target.value.length > 20) {
                        e.target.value = e.target.value.substring(0, 20);
                    }
                });
            }

            // Section data mapping
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
                        { value: 'FM-1K', label: 'FM-1K' }
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
                        { value: 'FM-2K', label: 'FM-2K' }
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
                        { value: 'FM-3K', label: 'FM-3K' }
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
                        { value: 'FM-4K', label: 'FM-4K' }
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

            // Function to populate sections based on course and year level
            function populateSections() {
                const courseSelect = document.getElementById('course');
                const yearLevelSelect = document.getElementById('year_level');
                const sectionSelect = document.getElementById('section');
                
                const course = courseSelect.value;
                const yearLevel = yearLevelSelect.value;
                
                // Clear existing options
                sectionSelect.innerHTML = '<option value="">Select section...</option>';
                
                if (course && yearLevel && sectionData[course] && sectionData[course][yearLevel]) {
                    const sections = sectionData[course][yearLevel];
                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.value;
                        option.textContent = section.label;
                        
                        // Check if this was the previously selected value
                        if (section.value === '{{ old("section") }}') {
                            option.selected = true;
                        }
                        
                        sectionSelect.appendChild(option);
                    });
                    
                    // Enable the section dropdown
                    sectionSelect.disabled = false;
                } else {
                    // Disable the section dropdown if course or year level is not selected
                    sectionSelect.disabled = true;
                }
            }

            // Add validation error removal for other fields
            const emailInput = document.getElementById('email');
            const courseSelect = document.getElementById('course');
            const yearLevelSelect = document.getElementById('year_level');
            const sectionSelect = document.getElementById('section');
            const passwordConfirmationInput = document.getElementById('password_confirmation');

            // Add event listeners for course and year level changes
            if (courseSelect) {
                courseSelect.addEventListener('change', populateSections);
            }
            if (yearLevelSelect) {
                yearLevelSelect.addEventListener('change', populateSections);
            }

            // Initialize sections on page load if course and year level are already selected
            populateSections();

            [emailInput, schoolIdInput, courseSelect, yearLevelSelect, sectionSelect, passwordInput, passwordConfirmationInput].forEach(field => {
                if (field) {
                    field.addEventListener('input', function(e) {
                        removeValidationError(e.target);
                    });
                    field.addEventListener('change', function(e) {
                        removeValidationError(e.target);
                    });
                }
            });

            // reCAPTCHA v3 Integration
            @if(config('services.recaptcha.site_key_v3'))
            function executeRecaptchaV3(form, action) {
                return new Promise((resolve, reject) => {
                    // Check if grecaptcha is loaded
                    if (typeof grecaptcha === 'undefined') {
                        console.error('reCAPTCHA script not loaded');
                        reject(new Error('reCAPTCHA script not loaded. Please check your internet connection.'));
                        return;
                    }

                    // Add timeout to prevent hanging
                    const timeout = setTimeout(() => {
                        reject(new Error('reCAPTCHA verification timeout'));
                    }, 10000); // 10 second timeout

                    grecaptcha.ready(function() {
                        console.log('reCAPTCHA ready, executing...');
                        grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', {action: action})
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
                                reject(error);
                            });
                    });
                });
            }
            @else
            // Fallback function when reCAPTCHA v3 is not configured
            function executeRecaptchaV3(form, action) {
                return new Promise((resolve) => {
                    console.log('reCAPTCHA v3 not configured, proceeding without verification');
                    resolve();
                });
            }
            @endif

            // Prevent form submission if validation fails
            const signupForm = document.getElementById('signupForm');
            if (signupForm) {
                signupForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate full name
                    if (!validateFullName()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Full Name',
                            text: 'Please enter a valid full name (only letters including capital letters and spaces, max 30 characters).',
                            confirmButtonColor: '#667eea',
                        });
                        fullNameInput.focus();
                        return false;
                    }
                    
                    // Validate username
                    if (!validateUsername()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Username',
                            text: 'Please enter a valid username (only letters including capital letters and numbers, max 20 characters).',
                            confirmButtonColor: '#667eea',
                        });
                        usernameInput.focus();
                        return false;
                    }
                    
                    const pw = passwordInput.value;
                    const result = checkPasswordStrength(pw);
                    if (!result.isStrong) {
                        let missingRequirements = [];
                        if (!result.requirements.length) missingRequirements.push('8-25 characters');
                        if (!result.requirements.uppercase) missingRequirements.push('uppercase letter');
                        if (!result.requirements.lowercase) missingRequirements.push('lowercase letter');
                        if (!result.requirements.numbers) missingRequirements.push('number');
                        if (!result.requirements.symbols) missingRequirements.push('special character/symbol');
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Requirements Not Met',
                            html: `Password must include:<br>• ${missingRequirements.join('<br>• ')}<br><br>Only <strong>Strong</strong> passwords are accepted.`,
                            confirmButtonColor: '#667eea',
                        });
                        passwordInput.focus();
                        return false;
                    }
                    // Check for duplicate School ID before submit
                    const schoolId = schoolIdInput.value;
                    if (schoolIdDuplicate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate School ID',
                            text: 'This School ID is already registered or pending approval.',
                            confirmButtonColor: '#667eea',
                        });
                        schoolIdInput.focus();
                        return false;
                    }

                    // All validations passed, execute reCAPTCHA and submit
                    const submitBtn = document.getElementById('submitBtn');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                    submitBtn.disabled = true;
                    
                    // Execute reCAPTCHA v3
                    executeRecaptchaV3(signupForm, 'signup')
                        .then(() => {
                            // Submit the form
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                            signupForm.submit();
                        })
                        .catch((error) => {
                            console.error('reCAPTCHA verification failed:', error);
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Security Verification Failed',
                                text: 'Please refresh the page and try again.',
                                confirmButtonColor: '#667eea'
                            });
                        });
                });
            }
        });
    </script>
     <script src="{{ asset('js/dev-tools-security.js') }}"></script>
</body>
</html>