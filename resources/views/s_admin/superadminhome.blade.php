@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - MCCIPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mccicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
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
            background: #ff4444;
            border-color: #ff4444;
            color: white;
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.5);
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            border-right: 2px solid var(--accent-green);
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 70px;
            transition: var(--transition);
            box-shadow: 2px 0 20px rgba(0, 255, 65, 0.1);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
            box-shadow: none;
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

        /* ==================== FOOTER ==================== */
        .footer {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            border-top: 2px solid var(--accent-green);
            padding: 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 40px;
            margin-left: 260px;
            transition: var(--transition);
        }

        .footer.expanded {
            margin-left: 0;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a {
            color: var(--accent-green);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--accent-green-light);
            text-shadow: 0 0 8px rgba(0, 255, 65, 0.5);
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
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: 100vh;
                top: 70px;
                border-right: none;
                border-bottom: 2px solid var(--accent-green);
                z-index: 99;
            }

            .sidebar.hidden {
                transform: translateX(-100%);
            }

            .main-container {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .footer {
                margin-left: 0;
            }

            .topbar {
                flex-wrap: wrap;
                gap: 10px;
            }

            .topbar-title {
                font-size: 18px;
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
                <i class="fas fa-terminal"></i> MCCIPES SUPER ADMIN
            </div>
        </div>

        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-muted);">{{ $user->name }}</div>
                    <div style="font-size: 11px; color: var(--accent-green);">SUPER ADMIN</div>
                </div>
            </div>
            <form action="{{ route('superadmin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">
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
                <a href="#">
                    <i class="fas fa-users"></i>
                    <span>Users Management</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-university"></i>
                    <span>Academic Years</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-tasks"></i>
                    <span>Questionnaires</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
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
                        <div class="stat-number">1,247</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-graduation-cap fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">342</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-chalkboard-user fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">89</div>
                        <div class="stat-label">Staff Members</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-box pulse">
                        <i class="fas fa-calendar-alt fa-2x" style="color: var(--accent-green);"></i>
                        <div class="stat-number">5</div>
                        <div class="stat-label">Academic Years</div>
                    </div>
                </div>
            </div>

            <!-- Alerts Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <strong>System Status:</strong> All systems operational and running smoothly.
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Cards -->
            <div class="row">
                <div class="col-md-6 mb-4">
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
                            <i class="fas fa-database"></i> Database Maintenance
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Backup and optimize your database</p>
                            <button class="btn-primary me-2">
                                <i class="fas fa-download"></i> Backup
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-tools"></i> Optimize
                            </button>
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
                            <button class="btn-primary me-2">
                                <i class="fas fa-lock"></i> Review Logs
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-key"></i> Update Keys
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
                </div>
            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <footer class="footer" id="footer">
            <p><strong style="color: var(--accent-green);">MCC-IPES Control Center</strong></p>
            <p>&copy; {{ date('Y') }} <a href="#">Instructors Performance Evaluation System</a></p>
            <p>Developed by: <span style="color: var(--accent-green);">Warren Ilustrisimo | Jenford Albaciete | Jerry Nasol | Cristina Ilustrisimo</span></p>
            <p style="margin-top: 10px; font-size: 11px; color: var(--text-muted);">
                <i class="fas fa-shield-alt"></i> All activities are monitored and logged for security purposes.
            </p>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContainer = document.getElementById('mainContainer');
            const footer = document.getElementById('footer');

            sidebar.classList.toggle('hidden');
            mainContainer.classList.toggle('expanded');
            footer.classList.toggle('expanded');
        }

        // Update last login on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('%c[SUPER ADMIN CONSOLE]', 'color: #00ff41; font-size: 16px; font-weight: bold; text-shadow: 0 0 10px #00ff41;');
            console.log('%cWelcome to MCCIPES Super Admin Panel', 'color: #39ff14; font-size: 14px;');
            console.log('%cAll actions are being monitored.', 'color: #90ee90; font-size: 12px;');
        });

        // Close sidebar on mobile when clicking a link
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    document.getElementById('sidebar').classList.add('hidden');
                    document.getElementById('mainContainer').classList.remove('expanded');
                    document.getElementById('footer').classList.remove('expanded');
                });
            });
        }
    </script>
</body>
</html>