@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
     <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .btn-edit {
            background: transparent;
            border: 1px solid #00ffff;
            color: #00ffff;
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
        .btn-edit:hover { background: #00ffff; color: var(--primary-dark); }
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
                                <th>Course</th>
                                <th>Section</th>
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
                                <td class="text-cyan">{{ $student->course }}</td>
                                <td class="text-purple">{{ $student->section }}</td>
                                <td class="text-center">
                                    <button class="btn-action btn-view" title="View Profile" onclick="viewUserProfile('{{ $student->full_name }}', '{{ $student->school_id }}', '{{ $student->email }}', '{{ $student->profile_image ? asset('uploads/students/' . $student->profile_image) : asset('images/hack.png') }}', '{{ $student->created_at->format('M d, Y') }}', '{{ $student->course }}', '{{ $student->section }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-copy" title="Copy Info" onclick="copyUserInfo('{{ $student->full_name }}', '{{ $student->school_id }}', '{{ $student->email }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-action btn-edit" title="Edit Student" onclick="loadStudentData('{{ $student->id }}', '{{ $student->username }}', '{{ $student->email }}', '{{ $student->full_name }}', '{{ $student->school_id }}', '{{ $student->course }}', '{{ $student->year_level }}', '{{ $student->section }}', '{{ $student->profile_image ? asset('uploads/students/' . $student->profile_image) : asset('images/hack.png') }}', '{{ $student->student_status }}')">
                                        <i class="fas fa-edit"></i>
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
                <div style="flex: 0 0 240px; text-align: center;">
                    <div style="width: 240px; height: 240px; border: 2px solid var(--accent-green); border-radius: 8px; overflow: hidden; box-shadow: 0 0 20px rgba(0,255,65,0.3); margin-bottom: 15px;">
                        <img id="profilePreviewImg" src="" alt="Profile Large" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="background: rgba(0, 255, 65, 0.1); padding: 8px; border: 1px solid var(--accent-green); border-radius: 4px;">
                        <label style="color: var(--accent-green); font-size: 10px; display: block; margin-bottom: 2px; opacity: 0.7;">SCHOOL_ID_NUMBER</label>
                        <div id="profileSchoolId" style="color: var(--text-light); font-size: 18px; font-weight: bold; letter-spacing: 1px;"></div>
                    </div>
                </div>

                <!-- User Data -->
                <div style="flex: 1; font-family: 'Courier New', monospace;">
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">FULL_NAME</label>
                        <div id="profileFullName" style="color: var(--text-light); font-size: 20px; font-weight: bold; text-shadow: 0 0 5px rgba(255,255,255,0.2);"></div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">EMAIL_ADDRESS</label>
                        <div id="profileEmail" style="color: var(--text-light); font-size: 14px;"></div>
                    </div>
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">COURSE</label>
                            <div id="profileCourse" style="color: var(--text-light); font-size: 14px; font-weight: bold;"></div>
                        </div>
                        <div style="flex: 1;">
                            <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">SECTION</label>
                            <div id="profileSection" style="color: var(--text-light); font-size: 14px; font-weight: bold;"></div>
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 2px; opacity: 0.7;">DATE_JOINED</label>
                        <div id="profileJoined" style="color: var(--text-light); font-size: 14px;"></div>
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

    <!-- EDIT USER MODAL (Overlay) -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; backdrop-filter: blur(5px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: var(--secondary-dark); border: 2px solid var(--accent-green); border-radius: 8px; box-shadow: 0 0 30px rgba(0,255,65,0.3); padding: 25px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h6 style="color: var(--accent-green); margin: 0; letter-spacing: 1px;">
                    <i class="fas fa-edit me-2"></i>EDIT USER DATA
                </h6>
            </div>
            
            <form id="editUserForm" method="POST" enctype="multipart/form-data" action="{{ route('superadmin.update-student') }}">
                @csrf
                <input type="hidden" name="student_id" id="editStudentId">
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="margin-bottom: 10px;">
                        <img id="editImagePreview" src="{{ asset('images/hack.png') }}" alt="Preview" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-green); background-color: #000;">
                    </div>
                    <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">USER_PHOTO</label>
                    <input type="file" name="image" id="editImage" accept="image/*" onchange="previewEditImage(this)"
                           style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 5px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 11px;">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">USERNAME</label>
                        <input type="text" name="username" id="editUsername" required 
                               style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">SCHOOL_ID</label>
                        <input type="text" name="school_id" id="editSchoolId" required 
                               style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">FULL_NAME</label>
                    <input type="text" name="full_name" id="editFullName" required 
                           style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                </div>

                <div class="mb-3">
                    <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">EMAIL_ADDRESS</label>
                    <input type="email" name="email" id="editEmail" required
                           style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">COURSE</label>
                        <select name="course" id="editCourse" required onchange="populateEditSections()"
                                style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                            <option value="BSIT">BSIT</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSBA">BSBA</option>
                            <option value="BSED">BSED</option>
                            <option value="BEED">BEED</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">YEAR_LEVEL</label>
                        <select name="year_level" id="editYearLevel" required onchange="populateEditSections()"
                                style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">SECTION</label>
                        <select name="section" id="editSection" required
                                style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                            <option value="">Select section...</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label style="color: var(--accent-green); font-size: 11px; display: block; margin-bottom: 5px;">STUDENT_STATUS</label>
                        <select name="student_status" id="editStudentStatus" required
                                style="width: 100%; background: #000; border: 1px solid var(--accent-green); color: var(--accent-green); padding: 8px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px;">
                            <option value="Regular">Regular</option>
                            <option value="Irregular">Irregular</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeEditModal()" 
                            style="flex: 1; background: transparent; border: 1px solid #6c757d; color: #6c757d; padding: 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: var(--transition);">
                        CANCEL
                    </button>
                    <button type="submit" 
                            style="flex: 2; background: var(--accent-green); border: none; color: var(--primary-dark); padding: 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; transition: var(--transition); box-shadow: 0 0 10px rgba(0,255,65,0.4);">
                        UPDATE_DATA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        // Profile Modal Functionality
        function viewUserProfile(name, id, email, imgSrc, joined, course, section) {
            document.getElementById('profileFullName').textContent = name;
            document.getElementById('profileSchoolId').textContent = id;
            document.getElementById('profileEmail').textContent = email;
            document.getElementById('profileJoined').textContent = joined;
            document.getElementById('profileCourse').textContent = course;
            document.getElementById('profileSection').textContent = section;
            document.getElementById('profilePreviewImg').src = imgSrc;
            document.getElementById('profileModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Edit Modal Functionality
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
                    { value: 'BSHM-1A', label: 'BSHM-1A' }, { value: 'BSHM-1B', label: 'BSHM-1B' }, { value: 'BSHM-1C', label: 'BSHM-1C' }, { value: 'BSHM-1D', label: 'BSHM-1D' },
                    { value: 'BSHM-1E', label: 'BSHM-1E' }, { value: 'BSHM-1F', label: 'BSHM-1F' }, { value: 'BSHM-1G', label: 'BSHM-1G' }, { value: 'BSHM-1H', label: 'BSHM-1H' },
                    { value: 'BSHM-1I', label: 'BSHM-1I' }, { value: 'BSHM-1J', label: 'BSHM-1J' }, { value: 'BSHM-1K', label: 'BSHM-1K' }, { value: 'BSHM-1L', label: 'BSHM-1L' }
                ],
                '2nd Year': [
                    { value: 'BSHM-2A', label: 'BSHM-2A' }, { value: 'BSHM-2B', label: 'BSHM-2B' }, { value: 'BSHM-2C', label: 'BSHM-2C' }, { value: 'BSHM-2D', label: 'BSHM-2D' },
                    { value: 'BSHM-2E', label: 'BSHM-2E' }, { value: 'BSHM-2F', label: 'BSHM-2F' }, { value: 'BSHM-2G', label: 'BSHM-2G' }, { value: 'BSHM-2H', label: 'BSHM-2H' },
                    { value: 'BSHM-2I', label: 'BSHM-2I' }, { value: 'BSHM-2J', label: 'BSHM-2J' }, { value: 'BSHM-2K', label: 'BSHM-2K' }, { value: 'BSHM-2L', label: 'BSHM-2L' }
                ],
                '3rd Year': [
                    { value: 'BSHM-3A', label: 'BSHM-3A' }, { value: 'BSHM-3B', label: 'BSHM-3B' }, { value: 'BSHM-3C', label: 'BSHM-3C' }, { value: 'BSHM-3D', label: 'BSHM-3D' },
                    { value: 'BSHM-3E', label: 'BSHM-3E' }, { value: 'BSHM-3F', label: 'BSHM-3F' }, { value: 'BSHM-3G', label: 'BSHM-3G' }, { value: 'BSHM-3H', label: 'BSHM-3H' },
                    { value: 'BSHM-3I', label: 'BSHM-3I' }, { value: 'BSHM-3J', label: 'BSHM-3J' }, { value: 'BSHM-3K', label: 'BSHM-3K' }, { value: 'BSHM-3L', label: 'BSHM-3L' }
                ],
                '4th Year': [
                    { value: 'BSHM-4A', label: 'BSHM-4A' }, { value: 'BSHM-4B', label: 'BSHM-4B' }, { value: 'BSHM-4C', label: 'BSHM-4C' }, { value: 'BSHM-4D', label: 'BSHM-4D' },
                    { value: 'BSHM-4E', label: 'BSHM-4E' }, { value: 'BSHM-4F', label: 'BSHM-4F' }, { value: 'BSHM-4G', label: 'BSHM-4G' }, { value: 'BSHM-4H', label: 'BSHM-4H' },
                    { value: 'BSHM-4I', label: 'BSHM-4I' }, { value: 'BSHM-4J', label: 'BSHM-4J' }, { value: 'BSHM-4K', label: 'BSHM-4K' }, { value: 'BSHM-4L', label: 'BSHM-4L' }
                ]
            },
            'BSBA': {
                '1st Year': [
                    { value: 'FM-1A', label: 'FM-1A' }, { value: 'FM-1B', label: 'FM-1B' }, { value: 'FM-1C', label: 'FM-1C' }, { value: 'FM-1D', label: 'FM-1D' },
                    { value: 'FM-1E', label: 'FM-1E' }, { value: 'FM-1F', label: 'FM-1F' }, { value: 'FM-1G', label: 'FM-1G' }, { value: 'FM-1H', label: 'FM-1H' },
                    { value: 'FM-1I', label: 'FM-1I' }, { value: 'FM-1J', label: 'FM-1J' }, { value: 'FM-1K', label: 'FM-1K' }
                ],
                '2nd Year': [
                    { value: 'FM-2A', label: 'FM-2A' }, { value: 'FM-2B', label: 'FM-2B' }, { value: 'FM-2C', label: 'FM-2C' }, { value: 'FM-2D', label: 'FM-2D' },
                    { value: 'FM-2E', label: 'FM-2E' }, { value: 'FM-2F', label: 'FM-2F' }, { value: 'FM-2G', label: 'FM-2G' }, { value: 'FM-2H', label: 'FM-2H' },
                    { value: 'FM-2I', label: 'FM-2I' }, { value: 'FM-2J', label: 'FM-2J' }, { value: 'FM-2K', label: 'FM-2K' }
                ],
                '3rd Year': [
                    { value: 'FM-3A', label: 'FM-3A' }, { value: 'FM-3B', label: 'FM-3B' }, { value: 'FM-3C', label: 'FM-3C' }, { value: 'FM-3D', label: 'FM-3D' },
                    { value: 'FM-3E', label: 'FM-3E' }, { value: 'FM-3F', label: 'FM-3F' }, { value: 'FM-3G', label: 'FM-3G' }, { value: 'FM-3H', label: 'FM-3H' },
                    { value: 'FM-3I', label: 'FM-3I' }, { value: 'FM-3J', label: 'FM-3J' }, { value: 'FM-3K', label: 'FM-3K' }
                ],
                '4th Year': [
                    { value: 'FM-4A', label: 'FM-4A' }, { value: 'FM-4B', label: 'FM-4B' }, { value: 'FM-4C', label: 'FM-4C' }, { value: 'FM-4D', label: 'FM-4D' },
                    { value: 'FM-4E', label: 'FM-4E' }, { value: 'FM-4F', label: 'FM-4F' }, { value: 'FM-4G', label: 'FM-4G' }, { value: 'FM-4H', label: 'FM-4H' },
                    { value: 'FM-4I', label: 'FM-4I' }, { value: 'FM-4J', label: 'FM-4J' }, { value: 'FM-4K', label: 'FM-4K' }
                ]
            },
            'BSED': {
                '1st Year': [
                    { value: '1-A', label: '1-A' }, { value: '1-B', label: '1-B' }, { value: '1-C', label: '1-C' }, { value: '1-M', label: '1-M' },
                    { value: '1-N', label: '1-N' }, { value: '1-FR', label: '1-FR' }, { value: '1-SP', label: '1-SP' }, { value: '1-GERMAN', label: '1-GERMAN' }, { value: '1-TODDLER', label: '1-TODDLER' }
                ],
                '2nd Year': [
                    { value: '2-A', label: '2-A' }, { value: '2-B', label: '2-B' }, { value: '2-C', label: '2-C' }, { value: '2-M', label: '2-M' },
                    { value: '2-N', label: '2-N' }, { value: '2-FR', label: '2-FR' }, { value: '2-SP', label: '2-SP' }, { value: '2-GERMAN', label: '2-GERMAN' }, { value: '2-TODDLER', label: '2-TODDLER' }
                ],
                '3rd Year': [
                    { value: '3-A', label: '3-A' }, { value: '3-B', label: '3-B' }, { value: '3-C', label: '3-C' }, { value: '3-M', label: '3-M' },
                    { value: '3-N', label: '3-N' }, { value: '3-FR', label: '3-FR' }, { value: '3-SP', label: '3-SP' }, { value: '3-GERMAN', label: '3-GERMAN' }, { value: '3-TODDLER', label: '3-TODDLER' }
                ],
                '4th Year': [
                    { value: '4-A', label: '4-A' }, { value: '4-B', label: '4-B' }, { value: '4-C', label: '4-C' }, { value: '4-M', label: '4-M' },
                    { value: '4-N', label: '4-N' }, { value: '4-FR', label: '4-FR' }, { value: '4-SP', label: '4-SP' }, { value: '4-GERMAN', label: '4-GERMAN' }, { value: '4-TODDLER', label: '4-TODDLER' }
                ]
            },
            'BEED': {
                '1st Year': [
                    { value: '1-A', label: '1-A' }, { value: '1-B', label: '1-B' }, { value: '1-C', label: '1-C' }, { value: '1-D', label: '1-D' },
                    { value: '1-PRESCHOOLER', label: '1-PRESCHOOLER' }, { value: '1-TODDLER', label: '1-TODDLER' }, { value: '1-PR', label: '1-PR' }
                ],
                '2nd Year': [
                    { value: '2-A', label: '2-A' }, { value: '2-B', label: '2-B' }, { value: '2-C', label: '2-C' }, { value: '2-D', label: '2-D' },
                    { value: '2-PRESCHOOLER', label: '2-PRESCHOOLER' }, { value: '2-TODDLER', label: '2-TODDLER' }, { value: '2-PR', label: '2-PR' }
                ],
                '3rd Year': [
                    { value: '3-A', label: '3-A' }, { value: '3-B', label: '3-B' }, { value: '3-C', label: '3-C' }, { value: '3-D', label: '3-D' },
                    { value: '3-PRESCHOOLER', label: '3-PRESCHOOLER' }, { value: '3-TODDLER', label: '3-TODDLER' }, { value: '3-PR', label: '3-PR' }
                ],
                '4th Year': [
                    { value: '4-A', label: '4-A' }, { value: '4-B', label: '4-B' }, { value: '4-C', label: '4-C' }, { value: '4-D', label: '4-D' },
                    { value: '4-PRESCHOOLER', label: '4-PRESCHOOLER' }, { value: '4-TODDLER', label: '4-TODDLER' }, { value: '4-PR', label: '4-PR' }
                ]
            }
        };

        function populateEditSections(selectedSection = '') {
            const course = document.getElementById('editCourse').value;
            const yearLevel = document.getElementById('editYearLevel').value;
            const sectionSelect = document.getElementById('editSection');
            
            sectionSelect.innerHTML = '<option value="">Select section...</option>';
            
            if (course && yearLevel && sectionData[course] && sectionData[course][yearLevel]) {
                sectionData[course][yearLevel].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.value;
                    option.textContent = section.label;
                    if (section.value === selectedSection) option.selected = true;
                    sectionSelect.appendChild(option);
                });
            }
        }

        function loadStudentData(id, username, email, fullName, schoolId, course, yearLevel, section, image, studentStatus) {
            document.getElementById('editStudentId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editFullName').value = fullName;
            document.getElementById('editSchoolId').value = schoolId;
            document.getElementById('editCourse').value = course;
            document.getElementById('editYearLevel').value = yearLevel;
            document.getElementById('editStudentStatus').value = studentStatus || 'Regular';
            document.getElementById('editImagePreview').src = image;
            
            populateEditSections(section);
            
            document.getElementById('editModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function previewEditImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('editImagePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Handle Edit User Form Submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const modal = document.getElementById('editModal');
            
            // Hide modal temporarily to show confirmation
            modal.style.display = 'none';

            Swal.fire({
                title: 'CONFIRM_UPDATE?',
                text: "Are you sure you want to update this user's information?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--accent-green)',
                cancelButtonColor: '#ff4d4d',
                confirmButtonText: 'YES, UPDATE',
                cancelButtonText: 'CANCEL',
                background: 'var(--secondary-dark)',
                color: 'var(--text-light)'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'PROCESS_INITIATED',
                        text: 'Updating user database record...',
                        icon: 'info',
                        background: 'var(--secondary-dark)',
                        color: 'var(--accent-green)',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'DATA_SYNCHRONIZED',
                                text: 'User information updated successfully.',
                                background: 'var(--secondary-dark)',
                                color: 'var(--accent-green)',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Validation failed.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'UPDATE_FAILED',
                            text: error.message || 'Failed to update user information.',
                            background: 'var(--secondary-dark)',
                            color: '#ff4d4d'
                        }).then(() => {
                            modal.style.display = 'block';
                        });
                    });
                } else {
                    // Show modal again if cancelled
                    modal.style.display = 'block';
                }
            });
        });

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
            const editModal = document.getElementById('editModal');
            if (event.target == passwordModal) {
                closePasswordModal();
            }
            if (event.target == profileModal) {
                closeProfileModal();
            }
            if (event.target == editModal) {
                closeEditModal();
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