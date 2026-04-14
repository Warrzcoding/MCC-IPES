@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Management - MCCIPES</title>
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

        .hacker-glow {
            text-shadow: 0 0 8px var(--accent-green);
        }

        .card {
            background: rgba(10, 14, 39, 0.8) !important;
            backdrop-filter: blur(10px);
            border: 1px solid var(--accent-green) !important;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.1);
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

        .table {
            color: var(--text-light);
            vertical-align: middle;
            background: #000000;
        }

        .table th {
            background: #000000 !important;
            color: var(--text-light) !important;
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
            color: var(--text-light) !important;
        }

        .table-hover tbody tr:hover {
            background: rgba(0, 255, 65, 0.15) !important;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--accent-green);
            object-fit: cover;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 5px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-view { border: 1px solid var(--accent-green); color: var(--accent-green); background: transparent; }
        .btn-edit { border: 1px solid #00ffff; color: #00ffff; background: transparent; }
        .btn-delete { border: 1px solid #ff4d4d; color: #ff4d4d; background: transparent; }

        .btn-view:hover { background: var(--accent-green); color: var(--primary-dark); }
        .btn-edit:hover { background: #00ffff; color: var(--primary-dark); }
        .btn-delete:hover { background: #ff4d4d; color: white; }

        .btn-primary-hacker {
            background: transparent;
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: bold;
            transition: var(--transition);
        }

        .btn-primary-hacker:hover {
            background: var(--accent-green);
            color: var(--primary-dark);
            box-shadow: 0 0 15px var(--accent-green);
        }

        /* Modal Styling */
        .modal-content {
            background: var(--secondary-dark);
            border: 1px solid var(--accent-green);
            color: var(--text-light);
        }

        .modal-header { border-bottom: 1px solid var(--border-color); }
        .modal-footer { border-top: 1px solid var(--border-color); }

        .form-control {
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--accent-green);
        }

        .form-control:focus {
            background: var(--primary-dark);
            border-color: var(--accent-green);
            color: var(--accent-green);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.2);
        }

        label { color: var(--accent-green); margin-bottom: 5px; font-size: 13px; }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--primary-dark); }
        ::-webkit-scrollbar-thumb { background: var(--accent-green); }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); z-index: 1000; }
            .sidebar.show { transform: translateX(0); }
            .main-container { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- ==================== TOPBAR ==================== -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">ADMIN MANAGEMENT</div>
        </div>

        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr($user->name ?? $user->full_name, 0, 1)) }}</div>
                <div class="user-details d-none d-md-block">
                    <div style="font-size: 12px; color: var(--text-muted);">{{ $user->name ?? $user->full_name }}</div>
                    <div style="font-size: 11px; color: var(--accent-green);">SUPER ADMIN</div>
                </div>
            </div>
            <form action="{{ route('superadmin.logout') }}" method="POST">
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
            <li><a href="{{ route('superadmin.home') }}"><i class="fas fa-dashboard"></i><span>Dashboard</span></a></li>
            <li><a href="{{ route('superadmin.users') }}"><i class="fas fa-users"></i><span>Users Management</span></a></li>
            <li><a href="{{ route('superadmin.admin-management') }}" class="active"><i class="fas fa-user-shield"></i><span>Admin Management</span></a></li>
            <li><a href="{{ route('superadmin.activity-log') }}"><i class="fas fa-history"></i><span>Activity Logs</span></a></li>
        </ul>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-container" id="mainContainer">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 hacker-glow"><i class="fas fa-user-shield me-2"></i>ADMIN ACCOUNTS</h5>
                <button class="btn-primary-hacker" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fas fa-plus me-2"></i>ADD NEW ADMIN
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>AVATAR</th>
                                <th>FULL NAME</th>
                                <th>USERNAME</th>
                                <th>EMAIL</th>
                                <th>DEPARTMENT</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                            <tr>
                                <td>
                                    @if($admin->profile_image)
                                        <img src="{{ asset('uploads/staff/' . $admin->profile_image) }}" class="profile-img" alt="Profile">
                                    @else
                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">{{ strtoupper(substr($admin->full_name, 0, 1)) }}</div>
                                    @endif
                                </td>
                                <td>{{ $admin->full_name }}</td>
                                <td><span class="school-id-code">{{ $admin->username }}</span></td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->course }}</td>
                                <td>
                                    <span class="badge" style="background: rgba(0, 255, 65, 0.1); color: var(--accent-green); border: 1px solid var(--accent-green);">{{ strtoupper($admin->status) }}</span>
                                </td>
                                <td>
                                    <button class="btn-action btn-view view-btn" 
                                            data-admin="{{ json_encode($admin) }}"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-edit edit-btn" 
                                            data-admin="{{ json_encode($admin) }}"
                                            title="Edit Admin">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete delete-btn" 
                                            data-id="{{ $admin->id }}"
                                            data-name="{{ $admin->full_name }}"
                                            title="Delete Admin">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @if($admins->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No admin accounts found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title hacker-glow">ADD NEW ADMIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <form action="{{ route('superadmin.admin-management.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}">
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label>Department/Course</label>
                            <select name="course" class="form-control" required>
                                <option value="">Select Department</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSHM">BSHM</option>
                                <option value="BSBA">BSBA</option>
                                <option value="BSED">BSED</option>
                                <option value="BEED">BEED</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Profile Image (Optional)</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-hacker">Save Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title hacker-glow">EDIT ADMIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <form action="{{ route('superadmin.admin-management.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="admin_id" id="edit_admin_id">
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div id="edit_image_preview_container">
                                <img id="edit_image_preview" src="" class="profile-img" style="width: 100px; height: 100px; margin-bottom: 10px; display: none;">
                                <div id="edit_avatar_placeholder" class="user-avatar mx-auto" style="width: 100px; height: 100px; font-size: 40px; margin-bottom: 10px; display: none;"></div>
                            </div>
                            <label class="btn btn-outline-success btn-sm mt-2">
                                <i class="fas fa-camera me-1"></i> Change Photo
                                <input type="file" name="profile_image" id="edit_profile_image" class="form-control d-none" accept="image/*">
                            </label>
                        </div>
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        
                        <hr style="border-color: var(--border-color);">
                        <div class="mb-3">
                            <label>New Password (Optional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-hacker">Update Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Admin Modal -->
    <div class="modal fade" id="viewAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title hacker-glow">ADMIN DETAILS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="view_avatar_container" class="mb-4"></div>
                    <h4 id="view_full_name" class="hacker-glow mb-1"></h4>
                    <p id="view_username" class="text-muted mb-4"></p>
                    <hr style="border-color: var(--border-color);">
                    <div class="row text-start px-3">
                        <div class="col-6 mb-3">
                            <label class="d-block text-muted">Email</label>
                            <span id="view_email"></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="d-block text-muted">Department</label>
                            <span id="view_course"></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="d-block text-muted">Status</label>
                            <span id="view_status" class="badge bg-success"></span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="d-block text-muted">Role</label>
                            <span>ADMIN</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form -->
    <form id="deleteAdminForm" action="{{ route('superadmin.admin-management.delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" id="delete_admin_id">
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('mainContainer').classList.toggle('expanded');
        }

        $(document).ready(function() {
            // SweetAlert Alerts
            @if(session('message'))
                Swal.fire({
                    icon: '{{ session('message_type') == 'danger' ? 'error' : 'success' }}',
                    title: '{{ session('message_type') == 'danger' ? 'Error' : 'Success' }}',
                    text: '{{ session('message') }}',
                    background: '#1a1f3a',
                    color: '#ffffff',
                    confirmButtonColor: 'var(--accent-green)'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    background: '#1a1f3a',
                    color: '#ffffff',
                    confirmButtonColor: 'var(--accent-green)'
                });
            @endif

            // View Admin
            $('.view-btn').on('click', function() {
                const admin = $(this).data('admin');
                $('#view_full_name').text(admin.full_name);
                $('#view_username').text('@' + admin.username);
                $('#view_email').text(admin.email);
                $('#view_course').text(admin.course);
                $('#view_status').text(admin.status.toUpperCase());
                
                let avatarHtml = '';
                if (admin.profile_image) {
                    avatarHtml = `<img src="/uploads/staff/${admin.profile_image}" style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid var(--accent-green); object-fit: cover;">`;
                } else {
                    avatarHtml = `<div class="user-avatar mx-auto" style="width: 100px; height: 100px; font-size: 40px;">${admin.full_name.charAt(0).toUpperCase()}</div>`;
                }
                $('#view_avatar_container').html(avatarHtml);
                
                $('#viewAdminModal').modal('show');
            });

            // Edit Admin
            $('.edit-btn').on('click', function() {
                const admin = $(this).data('admin');
                $('#edit_admin_id').val(admin.id);
                $('#edit_full_name').val(admin.full_name);
                $('#edit_username').val(admin.username);
                $('#edit_email').val(admin.email);
                
                // Set initial preview
                if (admin.profile_image) {
                    $('#edit_image_preview').attr('src', `/uploads/staff/${admin.profile_image}`).show();
                    $('#edit_avatar_placeholder').hide();
                } else {
                    $('#edit_image_preview').hide();
                    $('#edit_avatar_placeholder').text(admin.full_name.charAt(0).toUpperCase()).show();
                }
                
                $('#editAdminModal').modal('show');
            });

            // Image Preview for Edit Modal
            $('#edit_profile_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#edit_image_preview').attr('src', e.target.result).show();
                        $('#edit_avatar_placeholder').hide();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Delete Admin
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete admin: ${name}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4d4d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    background: '#1a1f3a',
                    color: '#ffffff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete_admin_id').val(id);
                        $('#deleteAdminForm').submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
