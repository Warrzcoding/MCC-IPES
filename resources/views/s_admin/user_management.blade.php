@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>User Management - MCCIPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mccicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
           --primary-dark: #0a0e27;
            --primary: #0d1117;
            --secondary-dark: #1a1f3a;
            --accent-green: #00ff41;
            --accent-green-light: #39ff14;
            --accent-green-dark: #00cc34;
            --text-light: #ffffff;
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

        /* Hacker Scanlines */
        body::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), 
                        linear-gradient(90deg, rgba(255, 0, 0, 0.03), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.03));
            z-index: 9999;
            background-size: 100% 3px, 3px 100%;
            pointer-events: none;
            opacity: 0.5;
        }

        /* Matrix Glow Effect */
        .hacker-glow {
            text-shadow: 0 0 8px var(--accent-green);
        }

        .card {
            background: rgba(10, 14, 39, 0.8) !important;
            backdrop-filter: blur(10px);
            border: 1px solid var(--accent-green) !important;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.1);
        }

        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: var(--accent-green) var(--primary-dark);
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
            box-shadow: 0 0 15px rgb(255, 251, 251);
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
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-menu {
            list-style: none;
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

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: var(--hover-color);
            border-left: 3px solid var(--accent-green);
            color: var(--accent-green);
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
            padding: 30px;
        }

        .main-container.expanded {
            margin-left: 0;
        }

        /* ==================== COMPONENTS ==================== */
        .card {
            background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(13, 17, 23, 0.8) 100%);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .card-header {
            background: rgba(0, 255, 65, 0.05);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-box {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .search-box input {
            width: 100%;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--accent-green);
            padding: 8px 15px 8px 35px;
            border-radius: 20px;
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-green);
        }

        .table {
            color: var(--text-light);
            vertical-align: middle;
            background: #000000;
        }

        .table th {
            background: #000000 !important;
            color: var(--accent-green);
            border-bottom: 1px solid var(--accent-green) !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            text-shadow: 0 0 5px var(--accent-green);
            white-space: nowrap;
        }

        .table td {
            border-color: rgba(0, 255, 65, 0.2);
            padding: 8px 12px;
            font-size: 12px;
            white-space: nowrap;
            background: #000000;
        }

        .table-hover tbody tr:hover {
            background: rgba(0, 255, 65, 0.15) !important;
            box-shadow: inset 0 0 10px rgba(0, 255, 65, 0.2);
        }

        .table-hover tbody tr:hover td {
            background: rgba(0, 255, 65, 0.1) !important;
            color: var(--accent-green-light);
        }

        .school-id-code {
            background: rgba(0, 255, 65, 0.05);
            color: var(--accent-green);
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid rgba(0, 255, 65, 0.3);
            font-family: 'Courier New', monospace;
            font-size: 11px;
        }

        .profile-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--accent-green);
            object-fit: cover;
        }

        .btn-action {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            margin-right: 3px;
            transition: var(--transition);
        }

        .btn-view {
            background: transparent;
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
        }

        .btn-copy {
            background: transparent;
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
        }

        .btn-password {
            background: transparent;
            border: 1px solid #ffc107;
            color: #ffc107;
        }

        .btn-delete {
            background: transparent;
            border: 1px solid #ff4d4d;
            color: #ff4d4d;
        }

        .btn-view:hover { background: var(--accent-green); color: var(--primary-dark); }
        .btn-copy:hover { background: var(--accent-green); color: var(--primary-dark); }
        .btn-password:hover { background: #ffc107; color: var(--primary-dark); }
        .btn-delete:hover { background: #ff4d4d; color: white; }

        .text-cyan {
            color: #00ffff !important;
            text-shadow: 0 0 5px rgba(0, 255, 255, 0.3);
        }

        .text-purple {
            color: #bf94ff !important;
            text-shadow: 0 0 5px rgba(191, 148, 255, 0.3);
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--primary-dark); }
        ::-webkit-scrollbar-thumb { background: var(--accent-green); }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 992px) {
            body {
                zoom: 1;
                transform: none;
                width: 100%;
                min-height: 100vh;
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

            .sidebar {
                width: min(80vw, 320px);
                transform: translateX(-100%);
            }

            .main-container {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">MCCIPES SUPER ADMIN</div>
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
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('superadmin.home') }}">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.users') }}" class="active">
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
                <a href="#">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-container" id="mainContainer">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0" style="color: var(--accent-green);">
                    <i class="fas fa-user-graduate me-2"></i>Student Management
                </h5>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="userSearch" placeholder="Search students...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="text-center">Profile</th>
                                <th>Full Name</th>
                                <th>School ID</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Password (Hashed)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $student->profile_image ? asset('uploads/students/' . $student->profile_image) : asset('images/hack.png') }}" 
                                         class="profile-img" alt="Profile" loading="lazy"
                                         onerror="this.src='{{ asset('images/hack.png') }}'">
                                </td>   
                                <td class="text-cyan">{{ $student->full_name }}</td>
                                <td><span class="school-id-code">{{ $student->school_id }}</span></td>
                                <td class="text-cyan">{{ $student->email }}</td>
                                <td class="text-purple">{{ $student->created_at->format('M d, Y') }}</td>
                                <td>
                                    <small class="text-muted" title="{{ $student->password }}" style="font-size: 10px;">
                                        {{ Str::limit($student->password, 12) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <button class="btn-action btn-view" title="View Profile" onclick="viewUserProfile('{{ $student->full_name }}', '{{ $student->school_id }}', '{{ $student->email }}', '{{ $student->profile_image ? asset('uploads/students/' . $student->profile_image) : asset('images/hack.png') }}', '{{ $student->created_at->format('M d, Y') }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-copy" title="Copy Info" onclick="copyUserInfo('{{ $student->full_name }}', '{{ $student->school_id }}', '{{ $student->email }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-action btn-password" title="Change Password" onclick="openPasswordModal('{{ $student->id }}', '{{ $student->full_name }}')">
                                        <i class="fas fa-key"></i>
                                    </button>
                                   
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No students found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- PROFILE PREVIEW MODAL (Overlay) -->
    <div id="profileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 2000; backdrop-filter: blur(8px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 600px; background: var(--secondary-dark); border: 2px solid var(--accent-green); border-radius: 8px; box-shadow: 0 0 40px rgba(0,255,65,0.4); padding: 30px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                <h6 style="color: var(--accent-green); margin: 0; letter-spacing: 2px; font-weight: bold;">
                    <i class="fas fa-user-circle me-2"></i>USER_PROFILE_DATA
                </h6>
    
            </div>

            <div style="display: flex; gap: 30px; align-items: flex-start;">
                <!-- Big Image Box -->
                <div style="flex: 0 0 200px;">
                    <div style="width: 200px; height: 200px; border: 2px solid var(--accent-green); border-radius: 8px; overflow: hidden; box-shadow: 0 0 15px rgba(0,255,65,0.2);">
                        <img id="profilePreviewImg" src="" alt="Profile Large" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- User Data -->
                <div style="flex: 1; font-family: 'Courier New', monospace;">
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">FULL_NAME</label>
                        <div id="profileFullName" style="color: var(--text-light); font-size: 18px; font-weight: bold; text-shadow: 0 0 5px rgba(255,255,255,0.2);"></div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">SCHOOL_ID</label>
                        <div id="profileSchoolId" style="color: var(--accent-green-light); font-size: 16px;"></div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">EMAIL_ADDRESS</label>
                        <div id="profileEmail" style="color: var(--text-light); font-size: 14px;"></div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">DATE_JOINED</label>
                        <div id="profileJoined" class="text-purple" style="font-size: 14px;"></div>
                    </div>
                    <div style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                        <div style="color: var(--accent-green); font-size: 10px; font-style: italic;">
                            >> SYSTEM_STATUS: VERIFIED
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right;">
                <button onclick="closeProfileModal()" 
                        style="background: var(--accent-green); border: none; color: var(--primary-dark); padding: 8px 25px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: var(--transition); box-shadow: 0 0 10px rgba(0,255,65,0.3);">
                    CLOSE_TERMINAL
                </button>
            </div>
        </div>
    </div>

    <!-- PASSWORD UPDATE MODAL (Overlay) -->
    <div id="passwordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; backdrop-filter: blur(5px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background: var(--secondary-dark); border: 2px solid var(--accent-green); border-radius: 8px; box-shadow: 0 0 30px rgba(0,255,65,0.3); padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h6 style="color: var(--accent-green); margin: 0; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2"></i>OVERRIDE PASSWORD
                </h6>
            </div>
            
            <div id="modalUserInfo" style="font-size: 12px; color: var(--accent-green-light); margin-bottom: 15px; font-family: 'Courier New', monospace;">
                >> Target: <span id="targetUserName"></span>
            </div>

            <form id="passwordUpdateForm">
                @csrf
                <input type="hidden" id="targetUserId" name="user_id">
                <div class="mb-3">
                    <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">NEW_PASSWORD</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="newPassword" required 
                               style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; padding-right: 40px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                        <button type="button" onclick="togglePassword('newPassword', this)" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--accent-green); cursor: pointer; text-shadow: 0 0 5px var(--accent-green);">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">CONFIRM_PASSWORD</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="confirmPassword" required
                               style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; padding-right: 40px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                        <button type="button" onclick="togglePassword('confirmPassword', this)" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--accent-green); cursor: pointer; text-shadow: 0 0 5px var(--accent-green);">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closePasswordModal()" 
                            style="flex: 1; background: transparent; border: 1px solid #6c757d; color: #6c757d; padding: 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: var(--transition);">
                        CANCEL
                    </button>
                    <button type="submit" 
                            style="flex: 2; background: var(--accent-green); border: none; color: var(--primary-dark); padding: 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: var(--transition); box-shadow: 0 0 10px rgba(0,255,65,0.4);">
                        EXECUTE_UPDATE
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        // Profile Modal Functionality
        function viewUserProfile(name, id, email, imgSrc, joined) {
            document.getElementById('profileFullName').textContent = name;
            document.getElementById('profileSchoolId').textContent = id;
            document.getElementById('profileEmail').textContent = email;
            document.getElementById('profileJoined').textContent = joined;
            document.getElementById('profilePreviewImg').src = imgSrc;
            document.getElementById('profileModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Search Functionality
        document.getElementById('userSearch').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let rows = document.querySelector("#studentsTable tbody").rows;
            
            for (let i = 0; i < rows.length; i++) {
                let name = rows[i].cells[1].textContent.toUpperCase();
                let id = rows[i].cells[2].textContent.toUpperCase();
                let email = rows[i].cells[3].textContent.toUpperCase();
                let joined = rows[i].cells[4].textContent.toUpperCase();
                
                if (name.indexOf(filter) > -1 || id.indexOf(filter) > -1 || email.indexOf(filter) > -1 || joined.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
 

        
        // Password Modal Functionality
        function openPasswordModal(userId, userName) {
            document.getElementById('targetUserId').value = userId;
            document.getElementById('targetUserName').textContent = userName;
            document.getElementById('passwordModal').style.display = 'block';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            document.body.style.overflow = 'hidden';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const passwordModal = document.getElementById('passwordModal');
            const profileModal = document.getElementById('profileModal');
            if (event.target == passwordModal) {
                closePasswordModal();
            }
            if (event.target == profileModal) {
                closeProfileModal();
            }
        });

        // Handle Password Update Form Submission
        document.getElementById('passwordUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            
            const modal = document.getElementById('passwordModal');
            
            if (password !== confirm) {
                modal.style.display = 'none';
                Swal.fire({
                    icon: 'error',
                    title: 'MISMATCH_DETECTED',
                    text: 'Passwords do not match.',
                    background: 'var(--secondary-dark)',
                    color: 'var(--accent-green)',
                    confirmButtonColor: 'var(--accent-green)'
                }).then(() => {
                    modal.style.display = 'block';
                });
                return;
            }

            if (password.length < 8) {
                modal.style.display = 'none';
                Swal.fire({
                    icon: 'warning',
                    title: 'SECURITY_ALERT',
                    text: 'Password must be at least 8 characters.',
                    background: 'var(--secondary-dark)',
                    color: 'var(--accent-green)',
                    confirmButtonColor: 'var(--accent-green)'
                }).then(() => {
                    modal.style.display = 'block';
                });
                return;
            }

            // Hide the password form before showing confirmation to avoid z-index overlap
            modal.style.display = 'none';

            Swal.fire({
                title: 'CONFIRM_OVERRIDE?',
                text: "Proceeding with password update for target.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--accent-green)',
                cancelButtonColor: '#ff4d4d',
                confirmButtonText: 'YES, EXECUTE',
                background: 'var(--secondary-dark)',
                color: 'var(--text-light)'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    
                    fetch("{{ route('superadmin.update-password') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'DATABASE_UPDATED',
                                text: data.message,
                                background: 'var(--secondary-dark)',
                                color: 'var(--accent-green)'
                            });
                            // No need to call closePasswordModal() as it's already hidden
                            document.body.style.overflow = 'auto';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'UPDATE_FAILED',
                                text: data.message || 'An unknown error occurred.',
                                background: 'var(--secondary-dark)',
                                color: '#ff4d4d'
                            });
                            // Re-show modal on error so user can correct it
                            modal.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'SYSTEM_ERROR',
                            text: 'Failed to communicate with server.',
                            background: 'var(--secondary-dark)',
                            color: '#ff4d4d'
                        });
                        // Re-show modal on error
                        modal.style.display = 'block';
                    });
                } else {
                    // Re-show the password form if cancelled
                    modal.style.display = 'block';
                }
            });
        });

        
        // Copy Functionality
        function copyUserInfo(name, id, email) {
            const text = `Name: ${name}\nID: ${id}\nEmail: ${email}`;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Copied to clipboard',
                    showConfirmButton: false,
                    timer: 2000,
                    background: 'var(--secondary-dark)',
                    color: 'var(--accent-green)'
                });
            });
        }

        // Sidebar Toggle
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

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4d4d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                background: 'var(--secondary-dark)',
                color: 'var(--text-light)'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        });
    </script>
</body>
</html>