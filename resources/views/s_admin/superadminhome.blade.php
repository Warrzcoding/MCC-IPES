@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Super Admin Dashboard - MCCIPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mccicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-dark: #0a0e27;
            --primary: #0d1117;
            --secondary-dark: #1a1f3a;
            --accent-green: #00ff41;
            --accent-green-light: #39ff14;
            --accent-green-dark: #00cc34;
            --text-light: #e8f5e9;
            --text-muted: #90ee90;
            --border-color: rgba(0, 255, 65, 0.2);
            --hover-color: rgba(0, 255, 65, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, #1a0a2e 100%);
            color: var(--text-light);
            font-family: 'Courier New', monospace;
            overflow-x: hidden;
            min-height: 100vh;
            zoom: 0.8;
        }

        @supports not (zoom: 0.8) {
            body {
                transform: scale(0.8);
                transform-origin: top center;
                width: 125%;
                min-height: calc(100vh / 0.8);
            }
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 255, 65, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 255, 65, 0.02) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* ==================== TOPBAR ==================== */
        .topbar {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            border-bottom: 2px solid var(--accent-green);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 255, 65, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .toggle-btn {
            background: transparent;
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            transition: var(--transition);
        }

        .toggle-btn:hover {
            background: var(--accent-green);
            color: var(--primary-dark);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
        }

        .topbar-title {
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-green-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-green), var(--accent-green-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--accent-green);
            font-weight: bold;
            color: var(--primary-dark);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.4);
        }

        .logout-btn {
            background: transparent;
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
            font-weight: 600;
        }

        .logout-btn:hover {
           background: #0a4e5a;
            border-color: #fbfaff;
            color: white;
           box-shadow: 0 0 15px rgb(255, 255, 255);
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            border-right: 2px solid var(--accent-green);
            overflow-y: auto;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 70px;
            bottom: 0;
            transition: var(--transition);
            box-shadow: 2px 0 20px rgba(0, 255, 65, 0.1);
            z-index: 150;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
            box-shadow: none;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-menu a:hover {
            background: var(--hover-color);
            border-left: 3px solid var(--accent-green);
            color: var(--accent-green);
            padding-left: 25px;
            text-shadow: 0 0 8px rgba(0, 255, 65, 0.5);
        }

        .sidebar-menu a.active {
            background: var(--hover-color);
            border-left: 3px solid var(--accent-green);
            color: var(--accent-green);
            box-shadow: inset 0 0 10px rgba(0, 255, 65, 0.1);
        }

        .sidebar-menu i {
            width: 20px;
            text-align: center;
            color: var(--accent-green);
        }

        /* ==================== MAIN CONTENT ==================== */
        .main-container {
            margin-left: 260px;
            margin-top: 70px;
            transition: var(--transition);
            min-height: calc(100vh - 70px);
        }

        .main-container.expanded {
            margin-left: 0;
        }

        .content {
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        /* ==================== DASHBOARD CARDS ==================== */
        .card {
            background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(13, 17, 23, 0.8) 100%);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 255, 65, 0.1);
            transition: var(--transition);
            color: var(--text-light);
        }

        .card:hover {
            border-color: var(--accent-green);
            box-shadow: 0 8px 30px rgba(0, 255, 65, 0.2);
            transform: translateY(-5px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            color: var(--accent-green);
            font-weight: bold;
            padding: 15px;
        }

        .card-body {
            padding: 15px;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1) 0%, rgba(0, 255, 65, 0.05) 100%);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .stat-box:hover {
            border-color: var(--accent-green);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
            transform: scale(1.05);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: var(--accent-green);
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
            margin: 10px 0;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ==================== BUTTONS ==================== */
        .btn-primary {
            background: linear-gradient(90deg, var(--accent-green), var(--accent-green-light));
            color: var(--primary-dark);
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-primary:hover {
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            transform: scale(1.05);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: var(--accent-green);
            color: var(--primary-dark);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
        }

        /* ==================== TABLES ==================== */
        .table {
            color: var(--text-light);
            border-color: var(--border-color);
        }

        .table-bordered {
            border: 1px solid var(--border-color);
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--hover-color);
            border-left: 3px solid var(--accent-green);
        }

        .table th {
            background: rgba(0, 255, 65, 0.1);
            border-bottom: 2px solid var(--accent-green);
            color: var(--accent-green);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ==================== ALERTS ==================== */
        .alert {
            border-left: 4px solid var(--accent-green);
            background: rgba(0, 255, 65, 0.1);
            color: var(--text-light);
            border-radius: 6px;
        }

        .alert-success {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .alert-danger {
            border-left-color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
        }

        .alert-warning {
            border-left-color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
        }


        /* ==================== SCROLLBAR ==================== */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--accent-green), var(--accent-green-dark));
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--accent-green-light), var(--accent-green));
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 992px) {
            body {
                zoom: 1;
                transform: none;
                width: 100%;
                min-height: 100vh;
            }

            .sidebar {
                width: min(80vw, 320px);
                top: 70px;
                bottom: 0;
                transform: translateX(-100%);
                box-shadow: 12px 0 30px rgba(0, 0, 0, 0.4);
                z-index: 250;
            }

            .sidebar.hidden {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-container {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                flex-wrap: wrap;
                gap: 10px;
            }

            .topbar-title {
                font-size: 18px;
            }

            .topbar-title,
            .user-details {
                display: none;
            }
        }

        /* ==================== ANIMATIONS ==================== */
        @keyframes pulse-green {
            0%, 100% {
                box-shadow: 0 0 10px rgba(0, 255, 65, 0.4);
            }
            50% {
                box-shadow: 0 0 20px rgba(0, 255, 65, 0.8);
            }
        }

        .pulse {
            animation: pulse-green 2s infinite;
        }

        @keyframes glow {
            0%, 100% {
                text-shadow: 0 0 5px rgba(0, 255, 65, 0.5);
            }
            50% {
                text-shadow: 0 0 15px rgba(0, 255, 65, 0.8);
            }
        }

        .glow-text {
            animation: glow 2s infinite;
        }

        /* Matrix-like background animation */
        @keyframes matrix {
            0% {
                opacity: 0.2;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 0.2;
            }
        }

        .matrix-bg {
            animation: matrix 5s infinite;
        }

        /* Flaming/Glowing animation for Legend Dots */
        @keyframes flame-pulse {
            0% { transform: scale(1); filter: brightness(1) drop-shadow(0 0 2px currentColor); opacity: 0.8; }
            50% { transform: scale(1.2); filter: brightness(1.8) drop-shadow(0 0 10px currentColor); opacity: 1; }
            100% { transform: scale(1); filter: brightness(1) drop-shadow(0 0 2px currentColor); opacity: 0.8; }
        }

        .flame-dot {
            animation: flame-pulse 1.5s infinite ease-in-out;
            display: inline-block;
            vertical-align: middle;
        }

        /* ==================== SWEETALERT SIZING ==================== */
        .swal2-popup {
            width: 320px !important;
            padding: 20px !important;
        }

        .swal2-title {
            font-size: 1.25rem !important;
            margin-bottom: 8px !important;
        }

        .swal2-html-container {
            font-size: 0.9rem !important;
            margin: 8px 0 !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 6px 16px !important;
            font-size: 0.85rem !important;
        }

        .swal2-actions {
            gap: 8px !important;
            margin-top: 12px !important;
        }
    </style>
</head>
<body>
    <!-- ==================== TOPBAR ==================== -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">
                <!--<i class="fas fa-terminal">--></i> MCCIPES SUPER ADMIN
            </div>
        </div>

        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <div style="font-size: 12px; color: var(--text-muted);">{{ $user->name }}</div>
                    <div style="font-size: 11px; color: var(--accent-green);">SUPER ADMIN</div>
                </div>
            </div>
            <form id="logoutForm" action="{{ route('superadmin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="button" id="logoutBtn" class="logout-btn">
                    <i class="fas fa-power-off"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('superadmin.home') }}" class="active">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.users') }}">
                    <i class="fas fa-users"></i>
                    <span>Users Management</span>
                </a>
            </li>
           
            <li>
                <a href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.activity-log') }}">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
          
        </ul>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-container" id="mainContainer">
        <div class="content">
            <!-- Welcome Section -->
            <div class="mb-4">
                <h1 class="glow-text" style="font-size: 32px; margin-bottom: 10px;">
                    <i class="fas fa-network-wired"></i> Welcome to MCCIPES Control Center
                </h1>
                <p style="color: var(--text-muted); font-size: 14px;">
                    Last Login: <span style="color: var(--accent-green);">{{ $user->last_login?->format('M d, Y H:i A') ?? 'First Login' }}</span>
                </p>
            </div>

            <!-- Statistics Row -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-users fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">{{ number_format($instructorCount) }}</div>
                        <div class="stat-label">INSTRUCTORS</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-graduation-cap fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">{{ number_format($studentCount) }}</div>
                        <div class="stat-label">STUDENTS</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-chalkboard-user fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">{{ number_format($questionCount) }}</div>
                        <div class="stat-label">QUESTIONS</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-user-tie fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">{{ number_format($nonTeachingCount) }}</div>
                        <div class="stat-label">NON-TEACHING</div>
                    </div>
                </div>
            </div>

            <!-- Modern Student Analytics Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-lg" style="background: rgba(10, 14, 39, 0.95); border: 1px solid var(--accent-green);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--accent-green);">
                            <span style="color: var(--accent-green); font-weight: bold; letter-spacing: 1px;">
                                <i class="fas fa-chart-column me-2"></i> STUDENT EVALUATION STATISTICS BY DEPARTMENT
                            </span>
                            <span class="badge" style="background: rgba(0, 255, 65, 0.2); color: var(--accent-green); border: 1px solid var(--accent-green);">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i> LIVE ANALYTICS
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div style="height: 380px; position: relative; z-index: 99; background: transparent;">
                                        <canvas id="departmentStatsChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="p-3 rounded" style="background: rgba(0, 255, 65, 0.05); border: 1px solid rgba(0, 255, 65, 0.1);">
                                        <h6 class="mb-3" style="color: var(--accent-green); border-bottom: 1px solid rgba(0, 255, 65, 0.1); padding-bottom: 10px;">
                                            <i class="fas fa-info-circle me-2"></i> Statistics Insight
                                        </h6>
                                        @php
                                            $totalEvaluated = collect($departmentStats)->sum('evaluated');
                                        @endphp
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-2" style="font-size: 0.8rem; color: #39ff14;">
                                                <span class="me-2" style="width: 12px; height: 12px; background: #39ff14; border-radius: 50%; display: inline-block;"></span>
                                                <strong>DONE EVALUATED (COMPLETED)</strong>
                                            </div>
                                            <div class="d-flex align-items-center mb-3" style="font-size: 0.75rem; color: var(--text-muted); padding-left: 20px;">
                                                <span>Overall progress of evaluations</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1" style="padding-left: 20px;">
                                                <small  style="color: #fff200;">TOTAL PROGRESS</small>
                                                <small style="color: #39ff14;">{{ $studentCount > 0 ? round(($totalEvaluated / $studentCount) * 100, 1) : 0 }}%</small>
                                            </div>
                                            <div class="progress ms-3" style="height: 6px; background: rgba(255,255,255,0.1);">
                                                <div class="progress-bar" role="progressbar" 
                                                    style="width: {{ $studentCount > 0 ? ($totalEvaluated / $studentCount) * 100 : 0 }}%; background: #39ff14; box-shadow: 0 0 15px #39ff14;"></div>
                                            </div>
                                        </div>

                                        <h6 class="mb-3 mt-4" style="color: var(--accent-green); border-bottom: 1px solid rgba(0, 255, 65, 0.1); padding-bottom: 10px;">
                                            <i class="fas fa-building me-2"></i> Department Overall Totals
                                        </h6>
                                        <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                            @php
                                                $deptColorMap = [
                                                    'BSIT' => '#1a1a1a',
                                                    'BSHM' => '#800000',
                                                    'BSBA' => '#008000',
                                                    'BSED' => '#000080',
                                                    'BEED' => '#ADD8E6'
                                                ];
                                            @endphp
                                            @foreach($departmentStats as $stat)
                                            <li class="mb-2 d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2" style="width: 10px; height: 10px; background: {{ $deptColorMap[$stat['name']] ?? 'var(--accent-green)' }}; border-radius: 2px; display: inline-block; border: 1px solid rgba(255,255,255,0.1);"></span>
                                                    <span>{{ $stat['name'] }}</span>
                                                </div>
                                                <span style="color: #cbd5e0;">{{ $stat['total'] }} Students</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Main Dashboard Cards -->
            <div class="row">

             <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-database"></i> Database Maintenance
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Backup and optimize your database</p>
                            <button class="btn-primary me-2" id="backupBtn">
                                <i class="fas fa-download"></i> Backup
                            </button>
                            <a href="javascript:void(0)" id="optimizeBtn" class="btn-secondary text-decoration-none d-inline-block">
                                <i class="fas fa-tools"></i> Poject Files
                            </a>
                        </div>
                    </div>
                </div>

                 <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-shield-alt"></i> Security Center
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Monitor and enhance system security</p>
                            <a href="{{ route('superadmin.activity-log') }}" class="btn-primary me-2 text-decoration-none d-inline-block">
                                <i class="fas fa-lock"></i> Review Logs
                            </a>
                            <button class="btn-secondary" id="addIdUserModalTrigger">
                                <i class="fas fa-key"></i> Add ID_USERS
                            </button>
                        </div>
                    </div>
                </div>

               <!-- <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-cog"></i> System Management
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Manage all system configurations and settings</p>
                            <button class="btn-primary me-2">
                                <i class="fas fa-sliders-h"></i> Configure
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line"></i> Analytics & Reports
                        </div>
                        <div class="card-body">
                            <p class="mb-3">View detailed system analytics</p>
                            <button class="btn-primary me-2">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>

    </div>

    <!-- Add ID User Modal -->
    <div class="modal fade" id="addIdUserModal" tabindex="-1" aria-labelledby="addIdUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: rgba(10, 14, 39, 0.98); color: #e8f5e9; border: 1px solid rgba(0, 255, 65, 0.3); border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(0, 255, 65, 0.2);">
                    <h5 class="modal-title" id="addIdUserModalLabel">
                        <i class="fas fa-user-plus me-2 text-primary"></i>Add New ID User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addIdUserForm">
                        @csrf
                        <div class="mb-4">
                            <label for="id_number" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">ID NUMBER</label>
                            <input type="text" class="form-control" id="id_number" name="id_number" required placeholder="0000-0000" maxlength="9"
                                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="fname" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">FIRST NAME</label>
                                <input type="text" class="form-control" id="fname" name="fname" required placeholder="First Name"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label for="mname" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">MIDDLE NAME</label>
                                <input type="text" class="form-control" id="mname" name="mname" placeholder="Middle Name"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label for="lname" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">LAST NAME</label>
                                <input type="text" class="form-control" id="lname" name="lname" required placeholder="Last Name"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="course" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">COURSE</label>
                            <select class="form-select" id="course" name="course" required 
                                style="background-color: #1a1d35; border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                                <option value="" disabled selected style="background-color: #1a1d35; color: #fff;">Select Course</option>
                                <option value="BSIT" style="background-color: #1a1d35; color: #fff;">Bachelor of Science in Information Technology</option>
                                <option value="BSHM" style="background-color: #1a1d35; color: #fff;">Bachelor of Science in Hospitality Management</option>
                                <option value="BSBA" style="background-color: #1a1d35; color: #fff;">Bachelor of Science in Business Administration</option>
                                <option value="BSED" style="background-color: #1a1d35; color: #fff;">Bachelor of Secondary Education</option>
                                <option value="BEED" style="background-color: #1a1d35; color: #fff;">Bachelor of Elementary Education</option>
                            </select>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="year" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">YEAR</label>
                                <select class="form-select" id="year" name="year" required 
                                    style="background-color: #1a1d35; border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                                    <option value="" disabled selected style="background-color: #1a1d35; color: #fff;">Select Year</option>
                                    <option value="1" style="background-color: #1a1d35; color: #fff;">1st Year</option>
                                    <option value="2" style="background-color: #1a1d35; color: #fff;">2nd Year</option>
                                    <option value="3" style="background-color: #1a1d35; color: #fff;">3rd Year</option>
                                    <option value="4" style="background-color: #1a1d35; color: #fff;">4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="section" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">SECTION</label>
                                <input type="text" class="form-control" id="section" name="section" required placeholder="Section (e.g., A, B, C)"
                                    style="background: #1a1d35; border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label text-secondary" style="font-size: 0.9rem; letter-spacing: 0.5px;">GENDER</label>
                                <select class="form-select" id="gender" name="gender" required 
                                    style="background-color: #1a1d35; border: 1px solid rgba(0, 255, 65, 0.2); color: #fff; padding: 12px; border-radius: 8px;">
                                    <option value="" disabled selected style="background-color: #1a1d35; color: #fff;">Select Gender</option>
                                    <option value="Male" style="background-color: #1a1d35; color: #fff;">Male</option>
                                    <option value="Female" style="background-color: #1a1d35; color: #fff;">Female</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(0, 255, 65, 0.2); padding: 20px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 10px 25px; border-radius: 8px;">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveIdUserBtn" style="padding: 10px 25px; border-radius: 8px; background: #00ff41; border: none; color: #000; font-weight: 600;">
                        <i class="fas fa-save me-2"></i>Save ID User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContainer = document.getElementById('mainContainer');

            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('hidden');
                mainContainer.classList.toggle('expanded');
            }
        }

        function handleResponsiveState() {
            const sidebar = document.getElementById('sidebar');
            const mainContainer = document.getElementById('mainContainer');

            if (window.innerWidth > 992) {
                sidebar.classList.remove('show');
            } else {
                sidebar.classList.remove('hidden');
                mainContainer.classList.remove('expanded');
            }
        }

        window.addEventListener('resize', handleResponsiveState);
        handleResponsiveState();

        // Update last login on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('%c[SUPER ADMIN CONSOLE]', 'color: #00ff41; font-size: 16px; font-weight: bold; text-shadow: 0 0 10px #00ff41;');
            console.log('%cWelcome to MCCIPES Super Admin Panel', 'color: #39ff14; font-size: 14px;');
            console.log('%cAll actions are being monitored.', 'color: #90ee90; font-size: 12px;');

            // Show login success alert
            @if(session('login_success'))
                Swal.fire({
                    title: 'Welcome Back!',
                    text: 'You have successfully logged in to the Super Admin Panel',
                    icon: 'success',
                    confirmButtonColor: '#00ff41',
                    background: 'rgba(10, 14, 39, 0.95)',
                    color: '#e8f5e9',
                    allowOutsideClick: false,
                    didOpen: function() {
                        const popup = Swal.getPopup();
                        if (popup) {
                            popup.style.borderRadius = '16px';
                            popup.style.border = '1px solid rgba(0, 255, 65, 0.3)';
                            popup.style.boxShadow = '0 20px 55px rgba(0, 255, 65, 0.15)';
                        }
                    }
                });
            @endif
        });

        // Close sidebar on mobile when clicking a link
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    document.getElementById('sidebar').classList.remove('show');
                }
            });
        });

        // Handle logout confirmation
        document.getElementById('logoutBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to logout from the Super Admin Panel?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff6b6b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel',
                background: 'rgba(10, 14, 39, 0.95)',
                color: '#e8f5e9',
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.border = '1px solid rgba(0, 255, 65, 0.3)';
                        popup.style.boxShadow = '0 20px 55px rgba(0, 255, 65, 0.15)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    document.getElementById('logoutForm').submit();
                }
            });
        });
        // Handle optimize button confirmation
        document.getElementById('optimizeBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Project Files Access',
                text: 'You are about to open and access project files.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#00ff41',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Continue',
                cancelButtonText: 'Cancel',
                background: 'rgba(10, 14, 39, 0.95)',
                color: '#e8f5e9',
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.border = '1px solid rgba(0, 255, 65, 0.3)';
                        popup.style.boxShadow = '0 20px 55px rgba(0, 255, 65, 0.15)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('superadmin.filemanager') }}";
                }
            });
        });

        // Handle backup button confirmation
        document.getElementById('backupBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Download Backup Files',
                text: 'Are you sure you want to download a full backup of the system?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00ff41',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Download',
                cancelButtonText: 'Cancel',
                background: 'rgba(10, 14, 39, 0.95)',
                color: '#e8f5e9',
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.border = '1px solid rgba(0, 255, 65, 0.3)';
                        popup.style.boxShadow = '0 20px 55px rgba(0, 255, 65, 0.15)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('superadmin.backup.download') }}";
                }
            });
        });

        // Handle Add ID User Modal
        const addIdUserModal = new bootstrap.Modal(document.getElementById('addIdUserModal'));
        document.getElementById('addIdUserModalTrigger').addEventListener('click', function() {
            document.getElementById('addIdUserForm').reset();
            addIdUserModal.show();
        });

        // ID Number Formatting (0000-0000)
        document.getElementById('id_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
            if (value.length > 4) {
                value = value.slice(0, 4) + '-' + value.slice(4, 8);
            }
            e.target.value = value;
        });

        document.getElementById('saveIdUserBtn').addEventListener('click', function() {
            const form = document.getElementById('addIdUserForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Confirm Save',
                text: 'Are you sure you want to add this new ID User?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00ff41',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'Cancel',
                background: 'rgba(10, 14, 39, 0.95)',
                color: '#e8f5e9',
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.border = '1px solid rgba(0, 255, 65, 0.3)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);
                    
                    fetch("{{ route('superadmin.add-id-user') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addIdUserModal.hide();
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#00ff41',
                                background: 'rgba(10, 14, 39, 0.95)',
                                color: '#e8f5e9'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Something went wrong',
                                icon: 'error',
                                confirmButtonColor: '#ff6b6b',
                                background: 'rgba(10, 14, 39, 0.95)',
                                color: '#e8f5e9'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to save data. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#ff6b6b',
                            background: 'rgba(10, 14, 39, 0.95)',
                            color: '#e8f5e9'
                        });
                    });
                }
            });
        });

        // Initialize Department Stats Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('departmentStatsChart').getContext('2d');
            const departmentStats = @json($departmentStats);
            
            // Department Color Mapping for "Overall Total" (Vivid/Flaming base)
            const deptColors = {
                'BSIT': { solid: '#1a1a1a', light: '#4d4d4d', flame: '#000000' },
                'BSHM': { solid: '#800000', light: '#ff4d4d', flame: '#4d0000' },
                'BSBA': { solid: '#008000', light: '#32cd32', flame: '#004d00' },
                'BSED': { solid: '#000080', light: '#1e90ff', flame: '#00004d' },
                'BEED': { solid: '#ADD8E6', light: '#f0f8ff', flame: '#87ceeb' }
            };

            const labels = departmentStats.map(stat => stat.name);
            const totalData = departmentStats.map(stat => stat.total);
            const evaluatedData = departmentStats.map(stat => stat.evaluated);

            // Create Animated Flaming Gradient for Overall Total
            let offset = 0;
            const createFlamingGradient = (color, chartArea) => {
                if (!chartArea) return color.solid;
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0, color.flame);
                gradient.addColorStop(Math.max(0, Math.min(1, 0.5 + Math.sin(offset) * 0.1)), color.solid);
                gradient.addColorStop(1, color.light);
                return gradient;
            };

            // Static Light Green for Done Evaluated
            const lightGreenGradient = ctx.createLinearGradient(0, 0, 0, 400);
            lightGreenGradient.addColorStop(0, '#39ff14');
            lightGreenGradient.addColorStop(1, 'rgba(57, 255, 20, 0.2)');

            // Zoom Correction Plugin for Desktop (fix for body { zoom: 0.8 })
            const zoomCorrectionPlugin = {
                id: 'zoomCorrection',
                beforeEvent(chart, args) {
                    // Check if zoom or scale is likely active (Desktop view)
                    if (window.innerWidth > 992) {
                        const isZoomed = document.body.style.zoom === '0.8' || getComputedStyle(document.body).zoom === '0.8';
                        if (isZoomed && !args.event.native.isZoomedFixed) {
                            args.event.x /= 0.8;
                            args.event.y /= 0.8;
                            args.event.native.isZoomedFixed = true;
                        }
                    }
                }
            };

            const chart = new Chart(ctx, {
                type: 'bar',
                plugins: [zoomCorrectionPlugin],
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Overall Total',
                            data: totalData,
                            backgroundColor: labels.map(label => (deptColors[label] || {solid: '#fff'}).solid),
                            borderColor: labels.map(label => (deptColors[label] || {solid: '#fff'}).light),
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7,
                            hoverOffset: 8,
                            hoverBorderWidth: 2
                        },
                        {
                            label: 'Done Evaluated',
                            data: evaluatedData,
                            backgroundColor: lightGreenGradient,
                            borderColor: '#39ff14',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7,
                            hoverOffset: 8,
                            hoverBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                    onHover: (event, chartElement) => {
                        isHovering = chartElement.length > 0;
                    },
                    interaction: {
                        mode: 'index',
                        axis: 'x',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#e8f5e9',
                                font: {
                                    family: "'Courier New', monospace",
                                    size: 11
                                },
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(10, 14, 39, 0.95)',
                            titleColor: '#39ff14',
                            titleAlign: 'center',
                            titleFont: {
                                family: "'Courier New', monospace",
                                size: 14,
                                weight: 'bold'
                            },
                            bodyColor: '#e8f5e9',
                            bodyAlign: 'center',
                            bodyFont: {
                                family: "'Courier New', monospace",
                                size: 12
                            },
                            borderColor: '#39ff14',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            boxPadding: 0,
                            caretSize: 6,
                            caretPadding: 10,
                            cornerRadius: 4,
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 255, 65, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#90ee90',
                                font: {
                                    family: "'Courier New', monospace",
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#90ee90',
                                font: {
                                    family: "'Courier New', monospace",
                                    size: 11
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });

            // Flaming/Glowing Animation Loop
            let isHovering = false;

            function animate() {
                // Check isHovering to pause background updates when interacting
                // This prevents the "shifting" or "flickering" of tooltips
                if (isHovering) {
                    requestAnimationFrame(animate);
                    return;
                }
                
                offset += 0.05;
                const chartArea = chart.chartArea;
                if (chartArea) {
                    try {
                        chart.data.datasets[0].backgroundColor = labels.map(label => 
                            createFlamingGradient(deptColors[label] || {solid: '#fff', light: '#fff', flame: '#fff'}, chartArea)
                        );
                        chart.update('none');
                    } catch (e) {}
                }
                requestAnimationFrame(animate);
            }
            animate();
        });
    </script>
</body>
</html>