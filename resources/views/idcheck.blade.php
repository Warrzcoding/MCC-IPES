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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow:
                0 2px 12px 0 rgba(0, 212, 255, 0.10),
                0 8px 32px 0 rgba(60, 80, 120, 0.08),
                0 1.5px 6px 0 rgba(0,0,0,0.07);
            padding: 24px;
            max-width: 320px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .login-card:hover, .login-card:focus-within {
            transform: none;
            box-shadow:
                0 4px 24px 0 rgba(0, 212, 255, 0.13),
                0 12px 48px 0 rgba(60, 80, 120, 0.10),
                0 2px 8px 0 rgba(0,0,0,0.10);
            animation: none;
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
            margin-bottom: 8px;
            font-size: 1.44rem;
        }

        .login-header p {
            color: #666;
            font-size: 11px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 13px;
            transition: all 0.3s ease;
            position: relative;
            background: #fff;
        }

        .form-control:hover, .form-select:hover,
        .input-group:hover .form-control {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15) !important;
            transform: translateY(-1px);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 12px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }

        .btn-back-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #6c757d;
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
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 15px 0 0 15px;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .input-group:hover .input-group-text {
            border-color: #28a745 !important;
            background: rgba(40, 167, 69, 0.05) !important;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
            margin-bottom: 0;
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

        /* Responsive Design for Mobile/Desktop */
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
            <div class="logo" style="box-shadow: 0 4px 16px rgb(253, 253, 253); display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <img src="{{ asset('images/mccicin.jpg') }}" alt="MCC Logo" style="width: 120px; height: 120px; object-fit: contain; padding: 5px; max-width: 100%; height: auto;">
            </div>
        
            <p>Instructors Performance Evaluation System</p>
        </div>

        <form method="POST" action="{{ route('idcheck.submit') }}">
            @csrf
            <div class="mb-3">
                
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
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Check ID
            </button>
            <div class="mt-2">
                <a href="{{ route('login') }}" class="btn-back-icon" aria-label="Back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Footer - Only visible on mobile -->
    <div class="mobile-footer">
        <a href="{{ route('superadmin.login') }}" style="color: #ffffffff; text-decoration: none; font-weight: 600;"><p>&copy;{{ date('Y') }} MCC | Instructors Performance Evaluation System | Developed by: Warren Ilustrisimo | Jenford Albaciete | Jerry Nasol | Cristina Ilustrisimo </p></a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('school_id').addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove all non-numeric characters except dashes
            value = value.replace(/[^0-9-]/g, '');

            // Remove all dashes first to get clean numeric string
            let cleanValue = value.replace(/-/g, '');

            // Format as 0000-0000 by inserting dash after 4 digits
            if (cleanValue.length > 4) {
                value = cleanValue.substring(0, 4) + '-' + cleanValue.substring(4);
            } else {
                value = cleanValue;
            }

            // Limit to 9 characters (4 digits + dash + 4 digits)
            value = value.substring(0, 9);

            e.target.value = value;
        });

        // Prevent pasting invalid characters
        document.getElementById('school_id').addEventListener('paste', function(e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            paste = paste.replace(/[^0-9-]/g, '');
            this.value = paste;
            this.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>