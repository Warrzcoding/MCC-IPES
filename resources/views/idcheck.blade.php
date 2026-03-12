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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @if(config('services.recaptcha.site_key_v3'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key_v3') }}" async defer></script>
    @endif

    <style>
        :root {
            --purple: #5a189a;
            --pink: #d0006f;
            --card-bg: rgba(255,255,255,0.97);
            --text-dark: #111827;
            --text-mid: #4b5563;
            --text-light: #9ca3af;
            --border: #e5e7eb;
            --input-bg: #f8f9ff;
            --accent: #6366f1;
            --accent2: #ec4899;
            --green: #16a34a;
            --red: #dc2626;
            --radius-card: 26px;
            --radius-input: 14px;
            --radius-btn: 999px;
            --shadow-card: 0 20px 60px rgba(90,24,154,0.22), 0 2px 8px rgba(0,0,0,0.08);
            --shadow-btn: 0 6px 24px rgba(99,102,241,0.38);
        }

        * { box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', 'Segoe UI', sans-serif;
            perspective: 1000px;
            position: relative;
            overflow: hidden;
        }

        /* ── Floating Cubes ── */
        .floating-cubes {
            position: absolute; inset: 0;
            z-index: 0; overflow: hidden;
            pointer-events: none;
            transform-style: preserve-3d;
        }
        .cube {
            position: absolute; width: 20px; height: 20px;
            background: rgba(255,255,255,0.15);
            border-radius: 5px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25), inset 0 0 10px rgba(255,255,255,0.25);
            animation: floatCube 18s infinite ease-in-out;
            transform: translateZ(-200px) rotateX(15deg) rotateY(20deg);
        }
        .cube:nth-child(1)  { top:10%;  left:10%;  animation-delay:0s;  width:25px; height:25px; background:rgba(142,45,226,0.2); }
        .cube:nth-child(2)  { top:20%;  right:10%; animation-delay:2s;  width:20px; height:20px; background:rgba(255,20,147,0.2); }
        .cube:nth-child(3)  { bottom:30%; left:20%; animation-delay:4s; width:30px; height:30px; background:rgba(255,255,255,0.2); }
        .cube:nth-child(4)  { bottom:10%; right:20%; animation-delay:6s; width:22px; height:22px; background:rgba(218,112,214,0.2); }
        .cube:nth-child(5)  { top:50%;  left:50%;  animation-delay:8s;  width:28px; height:28px; background:rgba(142,45,226,0.25); }
        .cube:nth-child(6)  { top:70%;  right:30%; animation-delay:10s; width:18px; height:18px; background:rgba(255,20,147,0.18); }
        .cube:nth-child(7)  { bottom:50%; left:70%; animation-delay:12s; width:26px; height:26px; background:rgba(255,255,255,0.22); }
        .cube:nth-child(8)  { top:30%;  left:80%;  animation-delay:14s; width:24px; height:24px; background:rgba(218,112,214,0.2); }
        .cube:nth-child(9)  { top:5%;   right:5%;  animation-delay:16s; width:20px; height:20px; background:rgba(74,0,224,0.2); }
        .cube:nth-child(10) { bottom:5%; left:5%;  animation-delay:18s; width:32px; height:32px; background:rgba(255,20,147,0.25); }
        .cube:nth-child(11) { top:80%;  left:30%;  animation-delay:20s; width:19px; height:19px; background:rgba(255,255,255,0.2); }
        .cube:nth-child(12) { bottom:20%; right:50%; animation-delay:22s; width:27px; height:27px; background:rgba(142,45,226,0.2); }
        @keyframes floatCube {
            0%,100% { transform: translateZ(-200px) translateY(0) translateX(0) rotateX(15deg) rotateY(20deg) rotateZ(0deg); }
            25%      { transform: translateZ(-220px) translateY(-30px) translateX(20px) rotateX(35deg) rotateY(60deg) rotateZ(90deg); }
            50%      { transform: translateZ(-260px) translateY(-60px) translateX(0) rotateX(75deg) rotateY(120deg) rotateZ(180deg); }
            75%      { transform: translateZ(-220px) translateY(-30px) translateX(-20px) rotateX(35deg) rotateY(200deg) rotateZ(270deg); }
        }

        /* ── Login Card ── */
        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(18px);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            padding: 30px 26px 24px;
            max-width: 360px;
            width: 100%;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255,255,255,0.6);
        }
        .login-card:hover, .login-card:focus-within {
            transform: translateY(-4px);
            box-shadow: 0 28px 70px rgba(90,24,154,0.28), 0 2px 8px rgba(0,0,0,0.1);
        }

        /* ── Header ── */
        .login-header { text-align: center; margin-bottom: 20px; }
        .login-header .logo {
            width: 84px; height: 84px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            box-shadow: 0 8px 32px rgba(90,24,154,0.28), 0 4px 12px rgba(0,0,0,0.12);
            position: relative; overflow: hidden;
        }
        .login-header .logo::after { display: none; }
        .login-header h2 {
            color: var(--text-dark);
            font-weight: 800; font-size: 1.3rem;
            margin-bottom: 3px; letter-spacing: 0.02em;
        }
        .login-header p {
            color: var(--text-light); font-size: 0.65rem;
            margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.18em;
            font-weight: 500;
        }

        /* ── Divider with subtitle ── */
        .form-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 0 0 18px;
        }
        .form-divider::before, .form-divider::after {
            content: ''; flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0e7ff, transparent);
        }
        .form-divider span {
            font-size: 0.68rem; color: var(--text-light);
            letter-spacing: 0.05em;
            font-weight: 500; text-align: center;
            line-height: 1.4;
        }

        /* ── Input Section Label ── */
        .field-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-mid);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .field-label i { color: var(--accent); font-size: 0.65rem; }

        /* ── Input Group ── */
        .input-wrap {
            position: relative;
            margin-bottom: 6px;
        }
        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 0.82rem;
            z-index: 2;
            transition: color 0.2s;
        }
        .id-input {
            width: 100%;
            padding: 13px 16px 13px 40px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-input);
            background: var(--input-bg);
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            text-align: center;
            letter-spacing: 0.12em;
            transition: all 0.22s ease;
            outline: none;
            -webkit-appearance: none;
        }
        .id-input::placeholder {
            color: var(--text-light);
            font-weight: 400;
            letter-spacing: 0.04em;
            font-size: 0.82rem;
        }
        .id-input:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12), 0 4px 16px rgba(99,102,241,0.1);
        }
        .id-input:hover:not(:focus) {
            border-color: #818cf8;
            background: #fff;
            box-shadow: 0 4px 12px rgba(129,140,248,0.14);
        }
        .input-hint {
            font-size: 0.67rem;
            color: var(--text-light);
            text-align: center;
            margin-top: 5px;
            margin-bottom: 14px;
        }

        /* ── Terms Checkbox ── */
        .terms-block {
            background: #f8f9ff;
            border: 1.5px solid #e0e7ff;
            border-radius: 12px;
            padding: 11px 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .terms-block:has(#acceptTerms:checked) {
            border-color: rgba(99,102,241,0.4);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
            background: #f0f1ff;
        }
        .terms-block .form-check-input {
            width: 17px; height: 17px;
            margin: 0; flex-shrink: 0;
            cursor: pointer;
            border: 2px solid #c7d2fe;
            border-radius: 5px;
            margin-top: 1px;
            transition: all 0.2s;
        }
        .terms-block .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .terms-block-text {
            font-size: 0.74rem;
            color: var(--text-mid);
            line-height: 1.45;
        }
        .terms-link {
            color: var(--accent);
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px dashed rgba(99,102,241,0.4);
            transition: color 0.2s, border-color 0.2s;
        }
        .terms-link:hover { color: var(--pink); border-color: var(--pink); }

        /* ── Primary Button ── */
        .btn-primary {
            width: 100%;
            padding: 13px 18px;
            border: none;
            border-radius: var(--radius-btn);
            background: linear-gradient(120deg, #4f46e5 0%, #6366f1 50%, #ec4899 100%);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: var(--shadow-btn);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 14px;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(120deg, rgba(255,255,255,0.15), transparent 60%);
            opacity: 0;
            transition: opacity 0.25s;
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(99,102,241,0.48);
            filter: brightness(1.05);
        }
        .btn-primary:hover:not(:disabled)::before { opacity: 1; }
        .btn-primary:active:not(:disabled) {
            transform: translateY(0) scale(0.99);
            box-shadow: 0 4px 14px rgba(99,102,241,0.3);
        }
        .btn-primary:disabled {
            background: linear-gradient(120deg, #c4b5fd, #e9d5ff);
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* ── Back Button ── */
        .btn-back-icon {
            display: inline-flex;
            align-items: center; justify-content: center;
            width: 38px; height: 38px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #4b5563;
            border: 1.5px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-back-icon:hover {
            transform: translateY(-2px);
            background: #111827;
            color: #f9fafb;
            border-color: #111827;
            box-shadow: 0 6px 18px rgba(17,24,39,0.2);
        }
        .btn-back-icon i { font-size: 0.8rem; }

        /* ── grecaptcha ── */
        .grecaptcha-badge {
            position: fixed !important;
            top: 10px !important; right: 10px !important;
            z-index: 9999 !important;
            width: 70px !important;
            overflow: hidden !important;
            transition: width 0.3s ease !important;
            transform: scale(0.85); transform-origin: 100% 0;
        }
        .grecaptcha-badge:hover, .grecaptcha-badge:focus-within { width: 256px !important; }

        /* ── Mobile Footer ── */
        @media (max-width: 767px) {
            .mobile-footer {
                position: fixed; bottom: 10px; left: 50%; transform: translateX(-50%);
                width: 90%; max-width: 350px; text-align: center;
                padding: 6px 10px; color: rgba(255,255,255,0.75);
                font-size: 9px; line-height: 1.2; z-index: 10;
                border-radius: 8px; transition: all 0.3s; cursor: pointer;
            }
            .mobile-footer:hover { background: rgba(0,0,0,0.28); backdrop-filter: blur(5px); }
            .mobile-footer p { margin: 0; }
            .mobile-footer a { color: inherit; text-decoration: none; }
        }
        @media (min-width: 768px) { .mobile-footer { display: none; } }

        /* ──────────────────────────────
           PROFILE CONFIRM MODAL
        ────────────────────────────── */
        .glass-modal .modal-content {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,0.25);
        }

        /* Profile header banner */
        .modal-profile-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #c026d3 100%);
            padding: 28px 20px 48px;
            text-align: center;
            color: white;
            position: relative;
        }
        .modal-profile-header::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 32px;
            background: rgba(255,255,255,0.97);
            border-radius: 22px 22px 0 0;
        }
        .modal-profile-header .badge-label {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 999px;
            padding: 3px 12px;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        /* Avatar circle */
        .profile-avatar-wrapper {
            width: 68px; height: 68px;
            background: linear-gradient(135deg, #e0e7ff, #fff);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto;
            position: absolute;
            bottom: -34px; left: 50%; transform: translateX(-50%);
            box-shadow: 0 8px 24px rgba(90,24,154,0.18), 0 0 0 4px #fff;
            border: 3px solid #fff;
            z-index: 2;
        }
        .profile-avatar-wrapper i { font-size: 28px; color: #7c3aed; }

        /* Modal body */
        .modal-body-content {
            padding: 50px 20px 12px;
            text-align: center;
        }
        .user-name {
            font-size: 1.15rem; font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 5px;
            letter-spacing: 0.01em;
        }
        .user-id-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: linear-gradient(120deg, #4f46e5, #7c3aed);
            color: #fff;
            padding: 3px 12px; border-radius: 999px;
            font-weight: 700; font-size: 0.72rem;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }

        /* Info cards grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 0 4px 14px;
        }
        .info-card {
            background: #f8f9ff;
            border: 1.5px solid #e0e7ff;
            border-radius: 14px;
            padding: 12px 10px 11px;
            text-align: center;
            transition: all 0.22s;
        }
        .info-card:hover {
            background: #fff;
            border-color: #c7d2fe;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(99,102,241,0.1);
        }
        .info-card-icon {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 7px;
        }
        .info-card-icon i { font-size: 0.65rem; color: #4f46e5; }
        .info-label {
            display: block;
            font-size: 0.58rem; color: var(--text-light);
            text-transform: uppercase; letter-spacing: 0.12em;
            font-weight: 600; margin-bottom: 4px;
        }
        .info-value {
            display: block;
            font-size: 1rem; font-weight: 800;
            color: var(--text-dark); line-height: 1.2;
            letter-spacing: 0.01em;
        }

        /* Course card spans full width */
        .info-card.full-width { grid-column: 1 / -1; }

        /* Confirm question */
        .confirm-question {
            font-size: 0.75rem; color: var(--text-mid);
            margin: 0 0 0; font-weight: 500;
        }

        /* Modal footer */
        .modal-footer-custom {
            display: flex; gap: 8px;
            padding: 0 16px 18px;
            border: none;
        }

        /* NOT ME button */
        .btn-not-me {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid #fecaca;
            border-radius: var(--radius-btn);
            background: #fff5f5;
            color: var(--red);
            font-family: 'Outfit', sans-serif;
            font-size: 0.72rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: all 0.2s;
        }
        .btn-not-me:hover {
            background: #fee2e2; border-color: #fca5a5;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(220,38,38,0.18);
        }

        /* THIS IS ME button */
        .btn-this-is-me {
            flex: 1.4;
            padding: 10px 14px;
            border: none;
            border-radius: var(--radius-btn);
            background: linear-gradient(120deg, #16a34a, #15803d);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.72rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            box-shadow: 0 4px 18px rgba(22,163,74,0.36);
            transition: all 0.2s;
        }
        .btn-this-is-me:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22,163,74,0.44);
            filter: brightness(1.06);
        }

        /* ──────────────────────────────
           TERMS MODAL
        ────────────────────────────── */
        #termsModal .modal-dialog { max-width: 460px; }
        #termsModal .modal-content {
            border-radius: 20px; border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.22);
            overflow: hidden;
        }
        #termsModal .modal-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #c026d3 100%);
            color: white; border: none;
            padding: 14px 20px;
        }
        #termsModal .modal-header .modal-title {
            font-size: 0.9rem; font-weight: 700;
            display: flex; align-items: center; gap: 8px;
        }
        #termsModal .modal-header .modal-title i {
            background: rgba(255,255,255,0.2);
            width: 28px; height: 28px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; flex-shrink: 0;
        }
        #termsModal .modal-body {
            padding: 20px 22px;
            max-height: 58vh;
            overflow-y: auto;
        }
        #termsModal .modal-body::-webkit-scrollbar { width: 5px; }
        #termsModal .modal-body::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 999px; }
        #termsModal .modal-body::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 999px; }

        /* Terms content styling */
        .terms-section {
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .terms-section:last-of-type { border-bottom: none; }
        .terms-section-header {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px;
        }
        .terms-section-num {
            width: 22px; height: 22px; flex-shrink: 0;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.62rem; font-weight: 800;
        }
        .terms-section h4 {
            font-size: 0.82rem; font-weight: 700;
            color: var(--text-dark); margin: 0;
        }
        .terms-section p, .terms-section ul li {
            font-size: 0.75rem; color: var(--text-mid); line-height: 1.55;
            margin: 0;
        }
        .terms-section ul {
            padding-left: 16px; margin: 4px 0 0;
        }
        .terms-section ul li { margin-bottom: 3px; }
        .terms-section ul li::marker { color: var(--accent); }

        /* Last updated */
        .terms-meta {
            font-size: 0.68rem; color: var(--text-light);
            margin-top: 4px; font-style: italic;
        }

        /* Accept button */
        .terms-accept-block {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1.5px solid #e0e7ff;
            text-align: center;
        }
        .btn-accept-terms {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 28px;
            border: none; border-radius: var(--radius-btn);
            background: linear-gradient(120deg, #16a34a, #15803d);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.78rem; font-weight: 700;
            letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 5px 18px rgba(22,163,74,0.3);
            transition: all 0.22s;
        }
        .btn-accept-terms:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22,163,74,0.4);
            filter: brightness(1.06);
        }

        /* ── Responsive ── */
        @media (max-width: 575.98px) {
            .login-card {
                padding: 22px 18px 18px;
                max-width: 88vw;
                border-radius: 20px;
            }
            .login-header .logo { width: 68px; height: 68px; }
            .login-header h2 { font-size: 1.15rem; }
            .id-input { font-size: 0.95rem; padding: 12px 14px 12px 38px; }
            .btn-primary { font-size: 0.78rem; padding: 12px 16px; }

            /* Modal adjustments */
            .glass-modal .modal-dialog { margin: 12px; max-width: calc(100% - 24px) !important; }
            .info-grid { grid-template-columns: 1fr; }
            .info-card.full-width { grid-column: auto; }
            .modal-body-content { padding: 46px 14px 10px; }
            .user-name { font-size: 1rem; }
            .modal-footer-custom { padding: 0 12px 16px; gap: 6px; }

            #termsModal .modal-dialog { max-width: 92vw; margin: 8px auto; }
            #termsModal .modal-body { max-height: 52vh; padding: 16px; }
            .terms-section h4 { font-size: 0.78rem; }
            .terms-section p, .terms-section ul li { font-size: 0.72rem; }
            .btn-accept-terms { font-size: 0.72rem; padding: 10px 20px; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .login-card { animation: fadeIn 0.5s ease both; }

        /* ── Verification Inputs ── */
        .verification-wrapper {
            margin-bottom: 12px;
        }

        .underline-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border-bottom: 2px solid var(--border);
            padding: 8px 0;
            transition: all 0.3s ease;
        }

        .underline-input-wrapper:focus-within {
            border-bottom-color: var(--accent);
        }

        .underline-input {
            border: none;
            background: transparent !important;
            width: 100%;
            padding: 4px 8px 4px 30px;
            font-size: 0.9rem;
            color: var(--text-dark);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 5px;
            color: var(--text-light);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .underline-input-wrapper:focus-within .input-icon {
            color: var(--accent);
        }

        #verificationOtp {
            letter-spacing: 4px;
            font-weight: 600;
        }

        .swal-small { width: 320px !important; border-radius: 20px !important; }
    </style>
</head>
<body>
    <div class="floating-cubes" aria-hidden="true">
        <div class="cube"></div><div class="cube"></div><div class="cube"></div>
        <div class="cube"></div><div class="cube"></div><div class="cube"></div>
        <div class="cube"></div><div class="cube"></div><div class="cube"></div>
        <div class="cube"></div><div class="cube"></div><div class="cube"></div>
    </div>

    @if($error)
        <script>
            Swal.fire({ icon:'error', title:'Error', text:'{{ $error }}', confirmButtonColor:'#667eea' });
        </script>
    @endif
    @if($success)
        <script>
            Swal.fire({ icon:'success', title:'Success', text:'{{ $success }}', confirmButtonColor:'#667eea' });
        </script>
    @endif

    <!-- ── MAIN CARD ── -->
    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="logo">
                <img src="{{ asset('images/mccicin.jpg') }}" alt="MCC Logo"
                     style="width:60%;height:60%;object-fit:cover;border-radius:10%;">
            </div>
            <h2>ID Verification</h2>
           <!-- <p>Student Access Check</p>-->
        </div>

        <!-- Divider -->
        <div class="form-divider"><span>Enter your school ID to verify your eligibility before proceeding to registration.</span></div>

        <form id="idCheckForm">
            @csrf

            <!-- ID Input -->
            <div class="field-label">
                <i class="fas fa-id-card-alt"></i> School ID Number
            </div>
            <div class="input-wrap" style="margin-bottom:4px;">
                <i class="fas fa-hashtag input-icon"></i>
                <input type="text" class="id-input" id="school_id" name="id_number"
                       placeholder="0000-0000" required
                       pattern="[0-9]{4}-[0-9]{4}" maxlength="9"
                       title="Format: 0000-0000 (e.g., 2024-0001)"
                       autocomplete="off" inputmode="numeric">
            </div>        

            <!-- Terms Checkbox -->
            <div class="terms-block">
                <input class="form-check-input" type="checkbox" id="acceptTerms" name="accept_terms" required>
                <div class="terms-block-text">
                    I have read and accept the
                    <span class="terms-link" id="termsLink">Terms &amp; Conditions</span>
                    of the MCC Instructors Performance Evaluation System.
                </div>
            </div>

            <!-- Check ID Button -->
            <button type="button" class="btn-primary" id="checkIdBtn" disabled>
                <i class="fas fa-magnifying-glass"></i> Check My ID
            </button>

            <!-- Back -->
            <div class="d-flex justify-content-start">
                <a href="{{ route('login') }}" class="btn-back-icon" aria-label="Back to login">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Footer -->
    <div class="mobile-footer">
        <a href="{{ route('superadmin.login') }}">
            <p>&copy;{{ date('Y') }} MCC | Instructors Performance Evaluation System | Developed by: Warren Ilustrisimo | Jenford Albaciete | Jerry Nasol | Cristina Ilustrisimo</p>
        </a>
    </div>

    <!-- ══════════════════════════════════════
         PROFILE CONFIRM MODAL
    ══════════════════════════════════════ -->
    <div class="modal fade glass-modal" id="idConfirmModal" tabindex="-1"
         aria-labelledby="idConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
            <div class="modal-content">

                <!-- Banner -->
                <div class="modal-profile-header">
                    <span class="badge-label">
                        <i class="fas fa-circle-check" style="font-size:0.65rem;"></i> Profile Found
                    </span>
                    <!-- Avatar -->
                    <div class="profile-avatar-wrapper">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body-content">
                    <h2 class="user-name" id="modalFullName"></h2>
                    <div class="user-id-badge">
                        <i class="fas fa-id-card" style="font-size:0.65rem;opacity:0.8;"></i>
                        <span id="modalIdNumber"></span>
                    </div>

                    <div class="info-grid">
                        <div class="info-card full-width">
                            <div class="info-card-icon"><i class="fas fa-book-open"></i></div>
                            <div class="info-label">Program / Course</div>
                            <div class="info-value" id="modalCourse"></div>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon"><i class="fas fa-layer-group"></i></div>
                            <div class="info-label">Year Level</div>
                            <div class="info-value" id="modalYear"></div>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon"><i class="fas fa-users-viewfinder"></i></div>
                            <div class="info-label">Section</div>
                            <div class="info-value" id="modalSection"></div>
                        </div>
                    </div>

                    <p class="confirm-question mb-2">Is this your correct information?</p>
                    <div class="p-2 rounded-3" style="font-size: 0.68rem; background: rgba(220, 38, 38, 0.05); border: 1px solid rgba(220, 38, 38, 0.1); color: #dc2626; line-height: 1.4;">
                        <i class="fas fa-circle-exclamation me-1"></i>
                        <strong>Honesty Notice:</strong> Please use your real data. Misuse or providing false information will lead to sanctions or account termination. Activity are tracked by the TEAM for any misuse.
                    </div>

                    <!-- Email Verification Section (Initially Hidden) -->
                    <div id="emailVerificationSection" style="display: none; margin-top: 15px;">
                        <div class="verification-wrapper">
                            <label class="info-label mb-1" style="display: block; text-align: left;">Please provide your valid Ms365 Email</label>
                            <div class="underline-input-wrapper">
                                <i class="fab fa-microsoft input-icon"></i>
                                <input type="email" id="verificationEmail" class="underline-input" placeholder="your.email@mcclawis.edu.ph" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- OTP Verification Section (Initially Hidden) -->
                    <div id="otpVerificationSection" style="display: none; margin-top: 15px;">
                        <div class="verification-wrapper">
                            <label class="info-label mb-1" style="display: block; text-align: left;">Enter Verification Code</label>
                            <div class="underline-input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="text" id="verificationOtp" class="underline-input text-center" placeholder="000000" maxlength="6" autocomplete="off">
                            </div>
                            <div id="otpTimer" class="mt-2 text-muted" style="font-size: 0.65rem;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="modal-footer-custom" id="modalFooter">
                    <button type="button" class="btn-not-me" id="cancelBtn" data-bs-dismiss="modal">
                        <i class="fas fa-xmark"></i> Not Me
                    </button>
                    <button type="button" class="btn-this-is-me" id="thisIsMeBtn">
                        <i class="fas fa-check"></i> This Is Me
                    </button>
                    <!-- Verify/Send Button (Initially Hidden) -->
                    <button type="button" class="btn-this-is-me" id="verifyBtn" style="display: none; width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Verification
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         TERMS & CONDITIONS MODAL
    ══════════════════════════════════════ -->
    <div class="modal fade" id="termsModal" tabindex="-1"
         aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">
                        <i class="fas fa-file-contract"></i>
                        Terms &amp; Conditions
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">1</div>
                            <h4>Acceptance of Terms</h4>
                        </div>
                        <p>By accessing the MCC Instructors Performance Evaluation System (IPES), you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">2</div>
                            <h4>System Purpose</h4>
                        </div>
                        <p>IPES is designed exclusively for educational and administrative purposes within Madridejos Community College, facilitating performance evaluation and academic administration for authorized users only.</p>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">3</div>
                            <h4>User Eligibility</h4>
                        </div>
                        <p>Access is restricted to:</p>
                        <ul>
                            <li>Current enrolled students of MCC</li>
                            <li>Users with valid @mcclawis.edu.ph email addresses</li>
                            <li>Individuals authorized by the institution's administration</li>
                        </ul>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">4</div>
                            <h4>Data Privacy &amp; Protection</h4>
                        </div>
                        <ul>
                            <li>Personal data is collected only for legitimate educational purposes</li>
                            <li>Information is stored securely, accessed only by authorized personnel</li>
                            <li>Data sharing complies with applicable privacy laws and institutional policies</li>
                            <li>Microsoft 365 integration follows Microsoft's privacy standards</li>
                        </ul>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">5</div>
                            <h4>Acceptable Use Policy</h4>
                        </div>
                        <p>Users <strong>must</strong>:</p>
                        <ul>
                            <li>Use the system only for its intended educational purposes</li>
                            <li>Maintain the confidentiality of login credentials</li>
                            <li>Report any security vulnerabilities or unauthorized access</li>
                        </ul>
                        <p style="margin-top:6px;">Users must <strong>NOT</strong>:</p>
                        <ul>
                            <li>Share account credentials with unauthorized individuals</li>
                            <li>Attempt to access restricted areas or data</li>
                            <li>Use the system for commercial or non-educational purposes</li>
                        </ul>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">6</div>
                            <h4>System Availability</h4>
                        </div>
                        <p>MCC reserves the right to perform maintenance, modify features, or suspend access for security or administrative reasons.</p>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">7</div>
                            <h4>Limitation of Liability</h4>
                        </div>
                        <p>MCC and its representatives shall not be liable for system outages, data loss due to user error, or unauthorized access resulting from user negligence.</p>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">8</div>
                            <h4>Account Termination</h4>
                        </div>
                        <p>MCC reserves the right to terminate accounts for violation of these terms, misuse of resources, or end of enrollment/employment.</p>
                    </div>

                    <div class="terms-section">
                        <div class="terms-section-header">
                            <div class="terms-section-num">9</div>
                            <h4>Changes to Terms</h4>
                        </div>
                        <p>These terms may be updated periodically. Continued use of the system constitutes acceptance of any modified terms.</p>
                    </div>

                    <div class="terms-section" style="border:none;margin-bottom:0;padding-bottom:0;">
                        <div class="terms-section-header">
                            <div class="terms-section-num">10</div>
                            <h4>Contact Information</h4>
                        </div>
                        <p>For questions about these terms, please contact the MCC IT Department/IPES team or Administration .</p>
                        <p class="terms-meta">Last Updated: {{ date('F d, Y') }}</p>
                    </div>

                    <!-- Accept Button -->
                    <div class="terms-accept-block">
                        <button type="button" class="btn-accept-terms" id="acceptTermsBtn">
                            <i class="fas fa-circle-check"></i> Accept &amp; Continue
                        </button>
                    </div>

                </div><!-- /modal-body -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ── Terms & Conditions logic ──
        document.addEventListener('DOMContentLoaded', function () {
            const acceptTermsCheckbox = document.getElementById('acceptTerms');
            const checkIdBtn          = document.getElementById('checkIdBtn');
            const acceptTermsBtn      = document.getElementById('acceptTermsBtn');
            const termsModalElement   = document.getElementById('termsModal');
            const termsLink           = document.getElementById('termsLink');
            const termsModal          = new bootstrap.Modal(termsModalElement);
            let termsAccepted = false;

            if (acceptTermsCheckbox) {
                acceptTermsCheckbox.addEventListener('click', function (e) {
                    if (!termsAccepted) {
                        e.preventDefault();
                        this.checked = false;
                        termsModal.show();
                    }
                });
                acceptTermsCheckbox.addEventListener('change', function () {
                    if (!this.checked) { termsAccepted = false; checkIdBtn.disabled = true; }
                });
            }

            if (termsLink) {
                termsLink.addEventListener('click', function (e) { e.preventDefault(); termsModal.show(); });
            }

            if (acceptTermsBtn) {
                acceptTermsBtn.addEventListener('click', function () {
                    termsAccepted = true;
                    acceptTermsCheckbox.checked = true;
                    checkIdBtn.disabled = false;
                    termsModal.hide();
                    Swal.fire({
                        icon: 'success', title: 'Terms Accepted',
                        text: 'Thank you for accepting the terms and conditions.',
                        timer: 2000, timerProgressBar: true, showConfirmButton: false,
                        customClass: { popup: 'swal-small' }
                    });
                });
            }
        });

        // ── reCAPTCHA ──
        @if(config('services.recaptcha.site_key_v3'))
        function executeRecaptchaV3(action) {
            return new Promise((resolve, reject) => {
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key_v3') }}', { action }).then(resolve).catch(reject);
                });
            });
        }
        @endif

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentIdData = null;

        // ── ID input formatting ──
        document.getElementById('school_id').addEventListener('input', function (e) {
            let v = e.target.value.replace(/[^0-9-]/g, '');
            let clean = v.replace(/-/g, '');
            if (clean.length > 4) v = clean.substring(0, 4) + '-' + clean.substring(4);
            else v = clean;
            e.target.value = v.substring(0, 9);
        });

        document.getElementById('school_id').addEventListener('paste', function (e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9-]/g, '');
            this.value = paste;
            this.dispatchEvent(new Event('input'));
        });

        // ── OTP input formatting ──
        document.getElementById('verificationOtp').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });

        document.getElementById('verificationOtp').addEventListener('paste', function (e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 6);
            this.value = paste;
        });

        // ── ID Check ──
        async function checkIdNumber() {
            const checkIdBtn = document.getElementById('checkIdBtn');
            if (checkIdBtn.disabled) {
                Swal.fire({ icon:'warning', title:'Terms Required', text:'Please accept the terms and conditions to continue.', confirmButtonColor:'#667eea' });
                return;
            }

            const idNumber = document.getElementById('school_id').value.trim();
            if (!idNumber) {
                Swal.fire({ icon:'warning', title:'Required', text:'Please enter your ID number.', confirmButtonColor:'#667eea' });
                return;
            }
            if (!/^\d{4}-\d{4}$/.test(idNumber)) {
                Swal.fire({ icon:'warning', title:'Invalid Format', text:'ID must be in format: 0000-0000', confirmButtonColor:'#667eea' });
                return;
            }

            try {
                const response = await fetch('{{ route("idcheck.verify") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ id_number: idNumber })
                });
                const result = await response.json();

                if (result.status === 'found') {
                    currentIdData = result.data;
                    document.getElementById('modalIdNumber').textContent = result.data.id_number;
                    document.getElementById('modalFullName').textContent = result.data.fullname;
                    document.getElementById('modalCourse').textContent   = result.data.course;
                    document.getElementById('modalYear').textContent     = result.data.year + ' Year';
                    document.getElementById('modalSection').textContent  = result.data.section;
                    
                    // Reset modal state
                    document.getElementById('emailVerificationSection').style.display = 'none';
                    document.getElementById('otpVerificationSection').style.display = 'none';
                    document.getElementById('thisIsMeBtn').style.display = 'flex';
                    document.getElementById('cancelBtn').style.display = 'flex';
                    document.getElementById('verifyBtn').style.display = 'none';
                    document.getElementById('verificationEmail').value = '';
                    document.getElementById('verificationOtp').value = '';
                    
                    new bootstrap.Modal(document.getElementById('idConfirmModal')).show();
                } else if (result.status === 'not_found') {
                    Swal.fire({ icon:'error', title:'Not Found', text:'ID not found. Please check your ID number.', confirmButtonColor:'#667eea' });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text: result.message || 'An error occurred.', confirmButtonColor:'#667eea' });
                }
            } catch (err) {
                console.error('Error:', err);
                Swal.fire({ icon:'error', title:'Error', text:'An error occurred. Please try again.', confirmButtonColor:'#667eea' });
            }
        }

        document.getElementById('checkIdBtn').addEventListener('click', function (e) { e.preventDefault(); checkIdNumber(); });
        document.getElementById('school_id').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); checkIdNumber(); } });

        // ── This Is Me (Initial click) ──
        document.getElementById('thisIsMeBtn').addEventListener('click', function() {
            // Show email verification section
            document.getElementById('emailVerificationSection').style.display = 'block';
            // Hide initial buttons
            document.getElementById('thisIsMeBtn').style.display = 'none';
            document.getElementById('cancelBtn').style.display = 'none';
            // Show Verify/Send button
            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.style.display = 'block';
            verifyBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Verification';
        });

        // ── Verification Button (Send OTP / Verify OTP) ──
        document.getElementById('verifyBtn').addEventListener('click', async function () {
            const emailInput = document.getElementById('verificationEmail');
            const otpInput = document.getElementById('verificationOtp');
            const verifyBtn = document.getElementById('verifyBtn');
            const emailSection = document.getElementById('emailVerificationSection');
            const otpSection = document.getElementById('otpVerificationSection');

            // Flow 1: Send OTP
            if (otpSection.style.display === 'none') {
                const email = emailInput.value.trim();
                const emailPattern = /^[a-zA-Z0-9._%+-]+@mcclawis\.(edu|edi)\.ph$/i;

                if (!email) {
                    Swal.fire({ icon:'warning', title:'Required', text:'Please enter your MS 365 email.', confirmButtonColor:'#667eea' });
                    return;
                }
                if (!emailPattern.test(email)) {
                    Swal.fire({ icon:'warning', title:'Invalid Format', text:'Email must end with @mcclawis.edu.ph or @mcclawis.edi.ph', confirmButtonColor:'#667eea' });
                    return;
                }

                try {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                    const formData = new FormData();
                    formData.append('ms365_email', email);
                    formData.append('id_number', currentIdData.id_number);

                    @if(config('services.recaptcha.site_key_v3'))
                    try {
                        const token = await executeRecaptchaV3('idcheck_send_otp');
                        formData.append('recaptcha_token', token);
                    } catch (err) { console.error('reCAPTCHA error:', err); }
                    @endif

                    const response = await fetch('{{ route("idcheck.send_otp") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server error response:', errorText);
                        throw new Error(`Server Error (${response.status}). Please check logs.`);
                    }

                    const result = await response.json();

                    if (result.status === 'success') {
                        Swal.fire({ icon:'success', title:'OTP Sent', text: result.message, confirmButtonColor:'#667eea', timer: 2000, showConfirmButton: false });
                        
                        // Switch to OTP input
                        emailSection.style.display = 'none';
                        otpSection.style.display = 'block';
                        verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify OTP';
                    } else {
                        // Show the actual error message from backend (e.g. ID/Email used)
                        Swal.fire({ icon:'error', title:'Verification Error', text: result.message || 'Failed to send OTP.', confirmButtonColor:'#667eea' });
                    }
                } catch (err) {
                    Swal.fire({ icon:'error', title:'Error', text: err.message, confirmButtonColor:'#667eea' });
                } finally {
                    verifyBtn.disabled = false;
                }
            } 
            // Flow 2: Verify OTP and Finalize
            else {
                const otp = otpInput.value.trim();
                const email = emailInput.value.trim();

                if (otp.length !== 6) {
                    Swal.fire({ icon:'warning', title:'Invalid Code', text:'Please enter the 6-digit verification code.', confirmButtonColor:'#667eea' });
                    return;
                }

                try {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

                    // 1. Verify OTP
                    const verifyFormData = new FormData();
                    verifyFormData.append('ms365_email', email);
                    verifyFormData.append('otp_code', otp);

                    const verifyResponse = await fetch('{{ route("idcheck.verify_otp") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: verifyFormData
                    });
                    const verifyResult = await verifyResponse.json();

                    if (verifyResult.status !== 'success') {
                        throw new Error(verifyResult.message || 'OTP verification failed.');
                    }

                    // 2. Store session
                    const storeFormData = new FormData();
                    for (const key in currentIdData) {
                        storeFormData.append(key, currentIdData[key]);
                    }
                    storeFormData.append('ms365_email', email);

                    @if(config('services.recaptcha.site_key_v3'))
                    try {
                        const token = await executeRecaptchaV3('idcheck_verify');
                        storeFormData.append('recaptcha_token', token);
                    } catch (err) { console.error('reCAPTCHA error:', err); }
                    @endif

                    const storeResponse = await fetch('{{ route("idcheck.store_session") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: storeFormData
                    });
                    const storeResult = await storeResponse.json();

                    if (storeResult.status === 'success') {
                        Swal.fire({
                            icon: 'success', title: 'Verified!',
                            text: `Welcome ${currentIdData.fullname}! Proceeding to registration…`,
                            confirmButtonColor: '#667eea', timer: 2000, showConfirmButton: false
                        }).then(() => { window.location.href = '{{ route("signup") }}'; });
                    } else {
                        throw new Error(storeResult.message || 'Failed to process verification.');
                    }
                } catch (err) {
                    Swal.fire({ icon:'error', title:'Error', text: err.message, confirmButtonColor:'#667eea' });
                } finally {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify OTP';
                }
            }
        });

        // ── Email Input Auto-complete (similar to pre_signup) ──
        document.getElementById('verificationEmail').addEventListener('input', function() {
            let v = this.value;
            if (v.includes('@') && !v.includes('@mcclawis.edu.ph') && !v.includes('@mcclawis.edi.ph')) {
                const atIndex = v.indexOf('@');
                this.value = v.substring(0, atIndex) + '@mcclawis.edu.ph';
            }
        });
    </script>
</body>
</html>