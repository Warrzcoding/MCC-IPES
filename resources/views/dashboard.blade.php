<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <title>{{ $current_title }} -Instructors Performance Evaluation System</title>

     <!-- Favicon -->
     <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
      
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
             --accent-color: #f093fb;
            --sidebar-width: 195px;
            --header-height: 50px;
            --footer-height: 40px;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --border-light: #e9ecef;
            --shadow-light: 0 2px 10px rgba(102, 126, 234, 0.08);
            --shadow-medium: 0 4px 20px rgba(102, 126, 234, 0.12);
            --shadow-heavy: 0 8px 30px rgba(102, 126, 234, 0.15);
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
            --gradient-card: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: var(--footer-height);
        }
        
        /* Header Styles */
        .header {
            background: var(--gradient-primary);
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: var(--shadow-heavy);
            backdrop-filter: blur(10px);
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .header .logo {
            color: white;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            margin-right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .header .logo:hover {
            transform: scale(1.05);
            color: white;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
        }
        
        .header .logo i {
            font-size: 22px;
            background: linear-gradient(45deg, #fff, #f0f8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }
        
        .logo-subtitle {
            font-size: 9px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.2;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: opacity 0.3s ease;
        }
        
        .header .toggle-btn {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            font-size: 14px;
            margin-right: 20px;
            cursor: pointer;
            padding: 8px 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }
        
        .header .toggle-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255,255,255,0.2);
        }
        
        .header .user-menu {
            margin-left: auto;
            position: relative;
            z-index: 1;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 30px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            color: white;
        }
        
        .user-info.dropdown-toggle::after {
            display: none; /* Hide default Bootstrap dropdown arrow */
        }
        
        .user-info:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(255,255,255,0.2);
        }
        
        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.8);
            object-fit: cover;
            box-shadow: 0 3px 12px rgba(0,0,0,0.18);
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            border-color: white;
            box-shadow: 0 5px 18px rgba(0,0,0,0.28);
        }
        
        .user-details {
            color: white;
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 10px;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        
        .user-role {
            font-size: 9px;
            opacity: 0.85;
            margin: 0;
            font-weight: 400;
        }
        
        /* Enhanced Dropdown */
        .dropdown-menu {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            box-shadow: var(--shadow-heavy);
            padding: 10px 0;
            margin-top: 8px;
            min-width: 170px;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            color: var(--text-dark);
            transition: all 0.3s ease;
            border-radius: 0;
            font-size: 12px;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            margin: 0 6px;
            border-radius: 6px;
        }
        
        .dropdown-header {
            padding: 10px 16px 8px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 6px;
            font-size: 11px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: var(--gradient-card);
            box-shadow: var(--shadow-medium);
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1025;
            overflow-y: auto;
            border-right: 1px solid var(--border-light);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .sidebar-brand {
            padding: 20px 16px;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
        }
        
        .nav-section {
            padding: 16px 0;
        }
        
        .nav-section-title {
            padding: 0 20px 8px;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar .nav-link {
            padding: 10px 16px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin: 2px 10px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 11px;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-left-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateX(5px);
            box-shadow: var(--shadow-light);
        }
        
        .sidebar .nav-link.active {
            background: var(--gradient-primary);
            border-left-color: transparent;
            color: white;
            box-shadow: var(--shadow-medium);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
            font-size: 16px;
        }
        
        /* Sidebar Dropdown Styles */
        .nav-dropdown {
            position: relative;
        }
        
        .nav-dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .nav-dropdown-toggle .dropdown-arrow {
            transition: transform 0.3s ease;
            font-size: 12px;
            margin-left: auto;
        }
        
        .nav-dropdown-toggle.collapsed .dropdown-arrow {
            transform: rotate(-90deg);
        }
        
        .nav-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(102, 126, 234, 0.05);
            margin: 5px 15px 0;
            border-radius: 8px;
        }
        
        .nav-dropdown-menu.show {
            max-height: 280px;
        }
        
        .nav-dropdown-item {
            padding: 8px 14px 8px 32px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 3px;
            font-weight: 500;
            font-size: 10px;
        }
        
        .nav-dropdown-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
            color: var(--primary-color);
            transform: translateX(5px);
            text-decoration: none;
        }
        
        .nav-dropdown-item.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-light);
        }
        
        .nav-dropdown-item i {
            width: 16px;
            margin-right: 10px;
            text-align: center;
            font-size: 14px;
        }
        

        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 19px;
            min-height: calc(100vh - var(--header-height) - var(--footer-height));
            transition: margin-left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Content Card */
        .content-card {
            background: var(--gradient-card);
            border-radius: 16px;
            box-shadow: var(--shadow-medium);
            padding: 19px;
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.2);

        }
        
        .page-full-width {
            width: 100%;
            max-width: 100%;
        }
        
        .page-full-width .container,
        .page-full-width .container-fluid,
        .page-full-width .row {
            max-width: 100%;
        }
        
        .page-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border-light);
        }

        #page-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-date {
            color: var(--text-light);
            font-weight: 500;
        }
        
        /* Enhanced Footer */
        .footer {
            background: var(--gradient-primary);
            color: white;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 12px 0;
            box-shadow: 0 -4px 20px rgba(102, 126, 234, 0.15);
            backdrop-filter: blur(10px);
        }
        
        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 25px;
        }
        
        .footer-text {
            margin: 0;
            font-weight: 500;
            text-align: center;
            word-break: break-word;
            font-size: 12px;
        }
        
        .footer-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .developer-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 14px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
        }
        
        .developer-link i {
            font-size: 12px !important;
            margin-right: 4px;
        }
        
        .developer-link:hover {
            background: rgba(255,255,255,0.25);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255,255,255,0.2);
        }
        
        .developer-float-container {
            position: fixed;
            left: 50%;
            bottom: 80px;
            transform: translateX(-50%);
            display: flex;
            gap: 30px;
            z-index: 2000;
            pointer-events: none;
        }
        .dev-float-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: auto;
        }
        .dev-float-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 8px 24px rgba(102,126,234,0.18);
            object-fit: cover;
            background: #f5f7fa;
            animation: floatY 2.5s ease-in-out infinite;
            animation-delay: var(--float-delay, 0s);
            transition: transform 0.2s;
        }
        .dev-float-img:hover {
            transform: scale(1.08) rotate(-2deg);
            box-shadow: 0 12px 32px rgba(102,126,234,0.25);
        }
        .dev-float-label {
            margin-top: 8px;
            background: rgba(102,126,234,0.9);
            color: #fff;
            padding: 2px 8px;
            border-radius: 16px;
            font-size: 10px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(102,126,234,0.10);
            text-align: center;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        @keyframes floatY {
            0% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
            100% { transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .developer-float-container {
                flex-direction: row !important;
                gap: 8px;
                bottom: 110px;
                justify-content: center;
                align-items: flex-end;
            }
            .dev-float-img {
                width: 44px;
                height: 44px;
            }
            .dev-float-label {
                font-size: 10px;
                padding: 1px 4px;
            }
            .dev-float-name {
                font-size: 9px;
                padding: 1px 4px 0px 4px;
            }
        }
        @media (max-width: 480px) {
            .developer-float-container {
                flex-direction: row !important;
                gap: 8px;
                bottom: 110px;
                justify-content: center;
                align-items: flex-end;
            }
            .dev-float-img {
                width: 44px;
                height: 44px;
            }
            .dev-float-label {
                font-size: 10px;
                padding: 1px 4px;
            }
            .dev-float-name {
                font-size: 9px;
                padding: 1px 4px 0px 4px;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
            }
            
            .header {
                padding: 0 15px;
            }
            
            .user-details {
                display: none;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }
            .footer-text {
                text-align: center;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .content-card {
                padding: 20px;
                border-radius: 15px;
            }

            .page-title {
                font-size: 24px;
            }

            .header .logo {
                font-size: 24px;
            }

            .social-links {
                justify-content: center;
            }

            /* Center content on mobile for both admin and student dashboards */
            .content-card.page-full-width {
                width: 98%;
                margin: 0 auto;
            }

            /* Remove any inherited margins that might cause centering issues */
            .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            /* Remove column padding for included pages */
            .main-content > .content-card.page-full-width > [class*="col-"] {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1020;
            backdrop-filter: blur(5px);
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Pulse Animation for Active Elements */
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }

        /* Hide logo text by default on mobile/collapsed */
.logo .logo-text {
    opacity: 1;
    width: auto;
    display: inline-block;
    transition: opacity 0.3s, width 0.3s;
}
.logo.hide-text .logo-text {
    opacity: 0;
    width: 0;
    overflow: hidden;
    display: inline-block;
}
@media (max-width: 768px) {
    .logo .logo-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
        display: inline-block;
    }
    .sidebar.show ~ .header .logo .logo-text {
        opacity: 1 !important;
        width: auto !important;
    }
    
    /* Hide footer for students on mobile devices only */
    @if(Auth::user()->isStudent())
        .footer {
            display: none !important;
        }
        
        /* Adjust main content padding since footer is hidden */
        body {
            padding-bottom: 0 !important;
        }
        
        .main-content {
            min-height: calc(100vh - var(--header-height)) !important;
        }
    @endif
}
.header {
    flex-direction: row;
}
.toggle-btn {
    margin-right: 8px;
    margin-left: 0;
    padding: 10px 16px;
    font-size: 14px;
}
.school-logo {
    height: 43px;
    width: 46px;
    object-fit: cover;
    margin-right: 10px;
    box-shadow: 0 2px 8px rgba(102,126,234,0.15);
    vertical-align: middle;
    transition: transform 0.2s;
}
.school-logo:hover {
    transform: scale(1.08) rotate(-2deg);
    box-shadow: 0 6px 18px rgba(102,126,234,0.25);
}
.dev-float-name {
    color: #fff !important;
    font-size: 10px;
    font-weight: 600;
    display: block;
    margin-top: 2px;
    margin-bottom: 2px;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 2px 8px 1px 8px;
    box-shadow: 0 2px 8px rgba(102,126,234,0.10);
    letter-spacing: 0.2px;
    transition: background 0.3s;
}

/* SweetAlert2 Mobile-Friendly Customization - Default Styles */
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
        
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <button class="toggle-btn me-2" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="logo" id="mainLogo" style="transition:width 0.3s,opacity 0.3s;">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="school-logo">
            <div class="logo-container">
                <span class="logo-text" style="transition:opacity 0.3s;">MCC-IPES</span>
            </div>
        </a>
        <div class="user-menu">
            <div class="dropdown">
                <button class="user-info dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $profileImage = Auth::user()->profile_image;
                        $imagePath = '';

                        if ($profileImage && file_exists(public_path('uploads/students/' . $profileImage))) {
                            $imagePath = asset('uploads/students/' . $profileImage);
                        } elseif ($profileImage && file_exists(public_path('uploads/staff/' . $profileImage))) {
                            $imagePath = asset('uploads/staff/' . $profileImage);
                        } else {
                            $imagePath = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'User') . '&background=667eea&color=fff&size=200';
                        }
                    @endphp
                    <img src="{{ $imagePath }}" 
                         alt="User Avatar" 
                         class="user-avatar"
                         style="object-fit:cover;">
                    
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->full_name ?? 'User' }}</div>
                        <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="margin-left: 10px; font-size: 12px;"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <strong>{{ Auth::user()->full_name ?? 'User' }}</strong><br>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role ?? 'user') }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard', ['page' => 'profile']) }}">
                        <i class="fas fa-user me-2"></i> Profile
                    </a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a></li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    @php
        // Main admins always see all features, only non-main admins have restrictions
        $isMainAdmin = Auth::user()->isMainAdmin();
        $disabledSidebarFeatures = [];
        try {
            $disabledSidebarFeatures = $isMainAdmin ? [] : Auth::user()->getDisabledSidebarFeatures();
        } catch (\Exception $e) {
            // Log the error but don't break the page
            \Log::error('Error loading sidebar settings: ' . $e->getMessage());
            $disabledSidebarFeatures = [];
        }

        // Debug: Uncomment to check values
        // echo "<!-- DEBUG: isMainAdmin=$isMainAdmin, role=" . Auth::user()->role . ", is_main_admin=" . Auth::user()->is_main_admin . ", disabledFeatures=" . json_encode($disabledSidebarFeatures) . " -->";
    @endphp

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="nav-section">
            <div class="nav-section-title">Main Navigation</div>
            <a href="{{ route('dashboard', ['page' => 'dashboard']) }}" class="nav-link {{ $page === 'dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            @if(Auth::user()->isAdmin())
                <div class="nav-section-title" style="margin-top: 20px;">Administration</div>
                @if(!in_array('students', $disabledSidebarFeatures))
                <a href="{{ route('dashboard', ['page' => 'add-students']) }}" class="nav-link {{ $page === 'add-students' ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Students
                </a>
                @endif

                @if(!in_array('staff', $disabledSidebarFeatures))
                <a href="{{ route('dashboard', ['page' => 'add-staff']) }}" class="nav-link {{ $page === 'add-staff' ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i> Staff
                </a>
                @endif

                @if(!in_array('subject-management', $disabledSidebarFeatures))
                <a href="{{ route('dashboard', ['page' => 'subject-management']) }}" class="nav-link {{ $page === 'subject-management' ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Subject
                </a>
                @endif

                @if(!in_array('academicyear', $disabledSidebarFeatures))
                <a href="{{ route('dashboard', ['page' => 'academicyear']) }}" class="nav-link {{ $page === 'academicyear' ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Academic Year
                </a>
                @endif
                @if(!in_array('questionnaires', $disabledSidebarFeatures))
                <a href="{{ route('dashboard', ['page' => 'questionnaires']) }}" class="nav-link {{ $page === 'questionnaires' ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Questionnaires
                </a>
                @endif

                @php
                    $resultsItemsEnabled = !in_array('staff-ratings', $disabledSidebarFeatures) ||
                                          !in_array('department-ratings', $disabledSidebarFeatures) ||
                                          !in_array('overall-ratings', $disabledSidebarFeatures) ||
                                          !in_array('save-evaluations', $disabledSidebarFeatures);
                @endphp

                @if($resultsItemsEnabled)
                <div class="nav-dropdown">
                    <div class="nav-link nav-dropdown-toggle {{ in_array($page, ['staff-ratings', 'department-ratings', 'overall-ratings']) ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-star"></i>Results
                        </div>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="nav-dropdown-menu">
                        @if(!in_array('staff-ratings', $disabledSidebarFeatures))
                        <a href="{{ route('dashboard', ['page' => 'staff-ratings']) }}" class="nav-dropdown-item {{ $page === 'staff-ratings' ? 'active' : '' }}">
                            <i class="fas fa-user"></i> Individual
                        </a>
                        @endif
                        @if(!in_array('department-ratings', $disabledSidebarFeatures))
                        <a href="{{ route('dashboard', ['page' => 'department-ratings']) }}" class="nav-dropdown-item {{ $page === 'department-ratings' ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Department
                        </a>
                        @endif
                        @if(!in_array('overall-ratings', $disabledSidebarFeatures))
                        <a href="{{ route('dashboard', ['page' => 'overall-ratings']) }}" class="nav-dropdown-item {{ $page === 'overall-ratings' ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Overall Ranking
                        </a>
                        @endif
                        @if(!in_array('save-evaluations', $disabledSidebarFeatures))
                        <div style="border-top: 1px solid #e9ecef; margin: 8px 0;"></div>
                        <a href="#" class="nav-dropdown-item" id="sidebarSaveEvaluationsBtn" onclick="handleSaveEvaluations(event)">
                            <i class="fas fa-save"></i> Save Evaluations
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            @endif
            
            @if(Auth::user()->isStudent())
                <div class="nav-section-title" style="margin-top: 20px;">Evaluation</div>
               <!--<a href="{{ route('dashboard', ['page' => 'staff-list']) }}" class="nav-link {{ $page === 'staff-list' ? 'active' : '' }}">
                    <i class="fas fa-users"></i> All Staff
                </a>-->
                
                <a href="{{ route('dashboard', ['page' => 'evaluates']) }}" class="nav-link {{ $page === 'evaluates' ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i> Evaluate Staff
                </a>
            @endif
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-card page-full-width">
             <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 id="page-title" class="mb-0">{{ $current_title }}</h1>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt"></i> {{ date('F d, Y') }}
                </div>
            </div>
            
            @php
                // Include the appropriate page content
                $page_file = "pages.{$page}";
            @endphp
            
            @if(View::exists($page_file))
                @if($page === 'questionnaires')
                    @include($page_file, ['questionnaires_data' => $questionnaires_data])
                @elseif($page === 'evaluates')
                    @include($page_file, [
                        'totalEvaluated' => $totalEvaluated ?? 0,
                        'teachingEvaluated' => $teachingEvaluated ?? 0,
                        'nonTeachingEvaluated' => $nonTeachingEvaluated ?? 0,
                        'teachingEvaluatedStaff' => $teachingEvaluatedStaff ?? collect(),
                        'nonTeachingEvaluatedStaff' => $nonTeachingEvaluatedStaff ?? collect(),
                        'isOpen' => $isOpen ?? false,
                        'teachingQuestions' => $teachingQuestions ?? collect(),
                        'nonTeachingQuestions' => $nonTeachingQuestions ?? collect(),
                        'teachingStaff' => $teachingStaff ?? collect(),
                        'nonTeachingStaff' => $nonTeachingStaff ?? collect()
                    ])
                @else
                    @include($page_file)
                @endif
            @else
                <div class='alert alert-warning'>
                    <i class='fas fa-exclamation-triangle me-2'></i>Page not found.
                </div>
            @endif
        </div>

         <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-text">
                &copy; {{ date('Y') }} MCC | Instructor's Performance Evaluation System.
            </div>
            
            <div class="footer-links">
                <a href="" class="developer-link" id="developerBtn">
                    <i class="fas fa-code"></i>
                    <span>Developer</span>
                </a>
                
        </div>
    </div>
    <!-- Developer Floating Images Glass Box (outside footer-content) -->
    <div class="developer-float-container" id="developerFloat" style="display:none;">
        <div class="dev-float-item" style="--float-delay:0s; display: flex; flex-direction: column; align-items: center;">
            <img src="{{ asset('images/developers/dev1.png') }}" alt="Dev1" class="dev-float-img">
            <span class="dev-float-name">Warren J. Ilustrisimo</span>
            <span class="dev-float-label">Programmer</span>
        </div>
        <div class="dev-float-item" style="--float-delay:0.2s; display: flex; flex-direction: column; align-items: center;">
            <img src="{{ asset('images/developers/dev2.png') }}" alt="Dev2" class="dev-float-img">
            <span class="dev-float-name">Jenford Albaciete</span>
            <span class="dev-float-label">UI/UX Designer</span>
        </div>
        <div class="dev-float-item" style="--float-delay:0.4s; display: flex; flex-direction: column; align-items: center;">
            <img src="{{ asset('images/developers/dev3.png') }}" alt="Dev3" class="dev-float-img">
            <span class="dev-float-name">Jerry M. Nasol</span>
            <span class="dev-float-label">Researcher</span>
        </div>
        <div class="dev-float-item" style="--float-delay:0.6s; display: flex; flex-direction: column; align-items: center;">
            <img src="{{ asset('images/developers/dev4.png') }}" alt="Dev4" class="dev-float-img">
            <span class="dev-float-name">Cristina J. Ilustrisimo</span>
            <span class="dev-float-label">Researcher</span>
        </div>
    </div>
    </footer>
    </main>
    
   
    
    
    <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storageKey = 'ipes.disabledSidebarFeatures';
        const isMainAdmin = @json(Auth::user()->isMainAdmin());
        const isAdminRole = @json(Auth::user()->isAdmin());
        if (!isAdminRole) return;
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        let keys = [];
        try {
            const raw = localStorage.getItem(storageKey);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) {
                    keys = Array.from(new Set(parsed));
                }
            }
        } catch (e) {}
        const featureSelectors = {
            students: "a.nav-link[href*=\"page=add-students\"]",
            staff: "a.nav-link[href*=\"page=add-staff\"]",
            'subject-management': "a.nav-link[href*=\"page=subject-management\"]",
            academicyear: "a.nav-link[href*=\"page=academicyear\"]",
            questionnaires: "a.nav-link[href*=\"page=questionnaires\"]",
            'staff-ratings': ".nav-dropdown-menu a.nav-dropdown-item[href*=\"page=staff-ratings\"]",
            'department-ratings': ".nav-dropdown-menu a.nav-dropdown-item[href*=\"page=department-ratings\"]",
            'overall-ratings': ".nav-dropdown-menu a.nav-dropdown-item[href*=\"page=overall-ratings\"]",
            'save-evaluations': "#sidebarSaveEvaluationsBtn"
        };
        Object.entries(featureSelectors).forEach(([key, selector]) => {
            const element = sidebar.querySelector(selector);
            if (!element) return;
            if (!isMainAdmin && keys.includes(key)) {
                element.style.display = 'none';
            } else {
                element.style.display = '';
            }
        });
        const resultsDropdown = sidebar.querySelector('.nav-dropdown');
        if (resultsDropdown) {
            const toggle = resultsDropdown.querySelector('.nav-dropdown-toggle');
            if (!isMainAdmin && toggle) {
                const items = Array.from(resultsDropdown.querySelectorAll('.nav-dropdown-menu .nav-dropdown-item'));
                const hasVisible = items.some(item => item && item.style.display !== 'none');
                toggle.style.display = hasVisible ? '' : 'none';
            } else if (toggle) {
                toggle.style.display = '';
            }
        }
    });
</script>

<script>
    // SweetAlert2 Logout Confirmation
    document.addEventListener('DOMContentLoaded', function() {
      
        // Attach event to logout button
        document.querySelectorAll('[data-bs-target="#logoutModal"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Get user role for message
                let role = "{{ ucfirst(Auth::user()->role ?? 'User') }}";
                let logoutUrl = "{{ route('logout') }}";

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to logout as ${role}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Logout',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show success message before logout
                        Swal.fire({
                            icon: 'success',
                            title: 'Logout Successful!',
                            text: 'You have been successfully logged out.',
                            confirmButtonColor: '#667eea',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            // Create and submit logout form after success message
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = logoutUrl;
                            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                            document.body.appendChild(form);
                            form.submit();
                        });
                    }
                });
            });
        });
    });
</script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let sidebarCollapsed = false;
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.querySelector('.sidebar-overlay');
            const logo = document.getElementById('mainLogo');

            if (window.innerWidth <= 768) {
                // Mobile: show/hide sidebar and logo text
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                if (sidebar.classList.contains('show')) {
                    logo.classList.remove('hide-text');
                } else {
                    logo.classList.add('hide-text');
                }
            } else {
                // Desktop: collapse/expand sidebar and logo text
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                if (sidebar.classList.contains('collapsed')) {
                    logo.classList.add('hide-text');
                } else {
                    logo.classList.remove('hide-text');
                }
            }
        }
        
        // On page load, hide logo text if sidebar is collapsed or on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('mainLogo');
            if (window.innerWidth <= 768 || sidebar.classList.contains('collapsed')) {
                logo.classList.add('hide-text');
            }
        });
        
        // On resize, adjust logo text visibility
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('mainLogo');
            if (window.innerWidth <= 768) {
                if (!sidebar.classList.contains('show')) {
                    logo.classList.add('hide-text');
                }
            } else {
                if (sidebar.classList.contains('collapsed')) {
                    logo.classList.add('hide-text');
                } else {
                    logo.classList.remove('hide-text');
                }
            }
        });
        
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Initialize Bootstrap dropdowns manually if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all Bootstrap dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
          
        });

        // Developer button floating images toggle
        const devBtn = document.getElementById('developerBtn');
        const devFloat = document.getElementById('developerFloat');
        let devFloatVisible = false;
        if (devBtn && devFloat) {
            devBtn.addEventListener('click', function(e) {
                e.preventDefault();
                devFloatVisible = !devFloatVisible;
                devFloat.style.display = devFloatVisible ? 'flex' : 'none';
                if (devFloatVisible) {
                    setTimeout(() => {
                        devFloat.classList.add('show');
                    }, 10);
                } else {
                    devFloat.classList.remove('show');
                }
            });
            // Hide on scroll or click outside
            document.addEventListener('click', function(event) {
                if (devFloatVisible && !devBtn.contains(event.target) && !devFloat.contains(event.target)) {
                    devFloatVisible = false;
                    devFloat.style.display = 'none';
                    devFloat.classList.remove('show');
                }
            });
            window.addEventListener('scroll', function() {
                if (devFloatVisible) {
                    devFloatVisible = false;
                    devFloat.style.display = 'none';
                    devFloat.classList.remove('show');
                }
            });
        }
        
        // Sidebar dropdown toggle function
        function toggleDropdown(element) {
            const dropdownMenu = element.nextElementSibling;
            const arrow = element.querySelector('.dropdown-arrow');
            
            // Toggle the dropdown menu
            dropdownMenu.classList.toggle('show');
            
            // Toggle the arrow rotation
            element.classList.toggle('collapsed');
        }
        
        // Initialize dropdown state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if any dropdown item is active and expand the dropdown
            const activeDropdownItem = document.querySelector('.nav-dropdown-item.active');
            if (activeDropdownItem) {
                const dropdown = activeDropdownItem.closest('.nav-dropdown');
                const dropdownToggle = dropdown.querySelector('.nav-dropdown-toggle');
                const dropdownMenu = dropdown.querySelector('.nav-dropdown-menu');
                
                dropdownMenu.classList.add('show');
                dropdownToggle.classList.add('collapsed');
            }
        });
        
        // Handle Save Evaluations from sidebar
        function handleSaveEvaluations(event) {
            event.preventDefault();
            
            // First check if there are any evaluations to save
            fetch('/admin/check-evaluations-exist', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Evaluation check response:', data);
                if (!data.hasEvaluations) {
                    // No evaluations to save
                    Swal.fire({
                        title: 'Nothing to Save',
                        text: 'There are no evaluation results to save. All evaluations have already been saved or no evaluations exist.',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#667eea',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                    return;
                }
                
                // If evaluations exist, check if questions table is empty before proceeding
                fetch('/admin/check-questions-empty', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.empty) {
                        // Questions table is not empty - show warning
                        Swal.fire({
                            title: 'Cannot Save Results',
                            text: 'There are still active questionnaires in the system. Please save all questionnaires first before saving evaluation results.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f39c12',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        });
                        return;
                    }
                    
                    // Questions table is empty - proceed with saving
                    Swal.fire({
                        title: 'Save & Clear All Results?',
                        html: "<span style='font-size:0.95rem;'>Save all evaluation results and clear existing entries?</span>",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Save & Clear',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-danger btn-sm',
                            cancelButton: 'btn btn-secondary btn-sm',
                            actions: 'swal-actions-compact'
                        },
                        buttonsStyling: false,
                        didOpen: () => {
                            const style = document.createElement('style');
                            style.textContent = `
                                .swal-actions-compact {
                                    gap: 8px !important;
                                    justify-content: center !important;
                                }
                                .swal2-actions .btn {
                                    margin: 0 !important;
                                    min-width: auto !important;
                                    padding: 0.25rem 0.6rem !important;
                                    font-size: 0.72rem !important;
                                }
                            `;
                            document.head.appendChild(style);
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading alert
                            Swal.fire({
                                title: 'Saving Results...',
                                text: 'Please wait while we save all evaluation results.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Make the save request
                            fetch('{{ route("evaluations.saveAndClearAll") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: ''
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success alert with auto-dismiss
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'All evaluations have been saved and entries cleared successfully!',
                                        icon: 'success',
                                        timer: 3000,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error saving evaluations: ' + data.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#dc3545',
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error saving evaluations. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545',
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp'
                                    }
                                });
                            });
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error checking questionnaire status. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                });
            })
            .catch(error => {
                console.error('Error checking evaluations:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error checking evaluation status. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            });
        }
    </script>
          <script src="{{ asset('js/dev-tools-security.js') }}"></script>
</body>
</html>