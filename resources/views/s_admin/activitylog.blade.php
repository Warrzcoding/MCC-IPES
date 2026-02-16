@php
    $user = $superAdmin ?? auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activity Logs - MCCIPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mccicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
            background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(13, 17, 23, 0.8) 100%) !important;
            border: 1px solid var(--accent-green) !important;
            border-radius: 8px;
            color: var(--text-light);
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.1);
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
            color: #00ffff;
        }

        .table-hover tbody tr:hover {
            background: rgba(0, 255, 65, 0.15) !important;
            box-shadow: inset 0 0 10px rgba(0, 255, 65, 0.2);
        }

        .table-hover tbody tr:hover td {
            background: rgba(0, 255, 65, 0.1) !important;
            color: var(--accent-green-light);
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-success {
            background: rgba(0, 255, 65, 0.1);
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }

        .status-failed {
            background: rgba(255, 77, 77, 0.1);
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }

        .btn-action {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            margin-right: 3px;
            transition: var(--transition);
            background: transparent;
            cursor: pointer;
        }

        .btn-view {
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
        }

        .btn-view:hover {
            background: var(--accent-green);
            color: var(--primary-dark);
            box-shadow: 0 0 10px var(--accent-green);
        }

        .btn-delete {
            border: 1px solid #ff4d4d;
            color: #ff4d4d;
        }

        .btn-delete:hover {
            background: #ff4d4d;
            color: white;
            box-shadow: 0 0 10px #ff4d4d;
        }

        .border-accent-green {
            border-color: var(--accent-green) !important;
        }

        /* Modal Styling */
        .modal-content {
            background: linear-gradient(135deg, var(--secondary-dark) 0%, var(--primary-dark) 100%);
            border: 2px solid var(--accent-green);
            color: var(--text-light);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            background: rgba(0, 255, 65, 0.05);
        }

        .modal-header .modal-title {
            color: var(--accent-green);
            font-weight: bold;
            letter-spacing: 1px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .info-label {
            color: var(--accent-green);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .info-value {
            color: white;
            font-size: 14px;
            margin-bottom: 15px;
            word-break: break-all;
        }

        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--accent-green);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.1);
        }

        .profile-img-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid var(--accent-green);
            object-fit: cover;
            margin-bottom: 15px;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
        }

        /* Pagination Styling */
        .pagination {
            gap: 5px;
        }

        .page-link {
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--accent-green);
            font-size: 12px;
            padding: 5px 10px;
        }

        .page-link:hover {
            background: var(--accent-green);
            color: var(--primary-dark);
            border-color: var(--accent-green);
        }

        .page-item.active .page-link {
            background: var(--accent-green);
            color: var(--primary-dark);
            border-color: var(--accent-green);
        }

        .page-item.disabled .page-link {
            background: rgba(10, 14, 39, 0.5);
            color: rgba(0, 255, 65, 0.3);
            border-color: var(--border-color);
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--primary-dark); }
        ::-webkit-scrollbar-thumb { background: var(--accent-green); }

        @media (max-width: 992px) {
            .main-container { margin-left: 0; padding: 15px; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
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
                MCCIPES SUPER ADMIN
            </div>
        </div>

        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-details d-none d-md-block">
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
                <a href="{{ route('superadmin.home') }}">
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
                <a href="{{ route('superadmin.activity-log') }}" class="active">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-container" id="mainContainer">
        <div class="mb-4">
            <h2 class="hacker-glow"><i class="fas fa-terminal me-2"></i> ACTIVITY LOGS MONITORING</h2>
            <p style="color: var(--text-muted); font-size: 14px;">Tracking all user login attempts and authentication activities across the system.</p>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="fas fa-list me-2 text-accent-green"></i>
                    <span>Login History</span>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <form action="{{ route('superadmin.activity-log') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control bg-transparent text-white border-accent-green" 
                                placeholder="Search email, IP, status..." value="{{ $search ?? '' }}" 
                                style="border: 1px solid var(--accent-green); font-size: 12px;">
                            <button class="btn-action btn-view" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <button class="btn-action btn-view" onclick="window.location.href='{{ route('superadmin.activity-log') }}'">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginAttempts as $attempt)
                                <tr id="attempt-{{ $attempt->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                {{ strtoupper(substr($attempt->user->full_name ?? ($attempt->email ?? 'U'), 0, 1)) }}
                                            </div>
                                            <span>{{ $attempt->user->full_name ?? 'Guest/Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $attempt->email }}</td>
                                    <td><span class="school-id-code">{{ $attempt->ip_address }}</span></td>
                                    <td>
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        {{ $attempt->location ?: 'Unknown Location' }}
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $attempt->status === 'success' ? 'status-success' : 'status-failed' }}">
                                            {{ $attempt->status }}
                                        </span>
                                    </td>
                                    <td>{{ $attempt->created_at->format('M d, Y | h:i A') }}</td>
                                    <td class="text-center">
                                        <button class="btn-action btn-view view-details" 
                                            data-id="{{ $attempt->id }}"
                                            data-user="{{ $attempt->user->full_name ?? 'Guest' }}"
                                            data-email="{{ $attempt->email }}"
                                            data-ip="{{ $attempt->ip_address }}"
                                            data-location="{{ $attempt->location }}"
                                            data-status="{{ $attempt->status }}"
                                            data-time="{{ $attempt->created_at->format('F d, Y - h:i A') }}"
                                            data-ua="{{ $attempt->user_agent }}"
                                            data-lat="{{ $attempt->latitude }}"
                                            data-lng="{{ $attempt->longitude }}"
                                            data-profile="{{ ($attempt->user && $attempt->user->profile_image) ? asset('uploads/students/' . $attempt->user->profile_image) : asset('images/hack.png') }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-action btn-delete delete-attempt" data-id="{{ $attempt->id }}" data-email="{{ $attempt->email }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No activity logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($loginAttempts->hasPages())
                <div class="card-footer bg-transparent border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $loginAttempts->firstItem() }} to {{ $loginAttempts->lastItem() }} of {{ $loginAttempts->total() }} entries
                        </div>
                        <div>
                            {{ $loginAttempts->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Attempt Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="text-center mb-3">
                                <img id="modal-profile-img" src="{{ asset('images/hack.png') }}" class="profile-img-large" alt="User Profile">
                            </div>
                            <div class="mb-4">
                                <div class="info-label">User Information</div>
                                <div class="info-value" id="modal-user-display">Zencoder AI</div>
                                
                                <div class="info-label">Email Address</div>
                                <div class="info-value" id="modal-email-display">user@example.com</div>
                                
                                <div class="info-label">Network IP</div>
                                <div class="info-value"><span class="school-id-code" id="modal-ip-display">127.0.0.1</span></div>
                                
                                <div class="info-label">Status</div>
                                <div id="modal-status-display">
                                    <span class="status-badge status-success">SUCCESS</span>
                                </div>
                                <div class="mt-3"></div>

                                <div class="info-label">Timestamp</div>
                                <div class="info-value" id="modal-time-display">Feb 13, 2026 04:13 PM</div>

                                <div class="info-label">User Agent</div>
                                <div class="info-value" style="font-size: 11px; line-height: 1.4;" id="modal-ua-display">
                                    Mozilla/5.0...
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="info-label mb-2"><i class="fas fa-map-marked-alt me-1"></i> Accurate Geolocation Mapping</div>
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action btn-view px-4" data-bs-dismiss="modal">Close Terminal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

        let map;
        let marker;

        $(document).ready(function() {
            // View Details and Show Map
            $('.view-details').on('click', function() {
                const btn = $(this);
                const lat = parseFloat(btn.data('lat'));
                const lng = parseFloat(btn.data('lng'));
                
                // Populate Modal Data
                $('#modal-user-display').text(btn.data('user'));
                $('#modal-email-display').text(btn.data('email'));
                $('#modal-ip-display').text(btn.data('ip'));
                $('#modal-time-display').text(btn.data('time'));
                $('#modal-ua-display').text(btn.data('ua'));
                $('#modal-profile-img').attr('src', btn.data('profile'));
                
                const status = btn.data('status');
                const statusHtml = `<span class="status-badge ${status === 'success' ? 'status-success' : 'status-failed'}">${status}</span>`;
                $('#modal-status-display').html(statusHtml);

                // Show Modal
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();

                // Initialize Map after modal is shown
                setTimeout(() => {
                    initMap(lat, lng, btn.data('location'));
                }, 400);
            });

            function initMap(lat, lng, locationName) {
                // Clear existing map instance
                if (map) {
                    map.remove();
                }

                // Default coordinates if none provided
                const defaultLat = 11.151593;
                const defaultLng = 123.797949;
                
                const currentLat = isNaN(lat) ? defaultLat : lat;
                const currentLng = isNaN(lng) ? defaultLng : lng;

                map = L.map('map').setView([currentLat, currentLng], 15);

                const streetView = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                });

                const satelliteView = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EBP, and the GIS User Community'
                });

                // Add default view
                streetView.addTo(map);

                // Add Layer Control
                const baseMaps = {
                    "Street View": streetView,
                    "Satellite View": satelliteView
                };
                L.control.layers(baseMaps).addTo(map);

                marker = L.marker([currentLat, currentLng]).addTo(map)
                    .bindPopup(`<b>Location:</b><br>${locationName || 'Saved Pin'}`)
                    .openPopup();
                
                // Fix map display issue in modal
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            }

            // Delete Attempt
            $('.delete-attempt').on('click', function() {
                const id = $(this).data('id');
                const email = $(this).data('email');
                
                Swal.fire({
                    title: 'CONFIRM DELETION',
                    text: `Are you sure you want to delete the log for ${email}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4d4d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'YES, DELETE IT',
                    background: '#0a0e27',
                    color: '#ffffff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/superadmin/activity-log/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'DELETED',
                                        text: response.message,
                                        icon: 'success',
                                        background: '#0a0e27',
                                        color: '#ffffff'
                                    });
                                    $(`#attempt-${id}`).fadeOut(300, function() {
                                        $(this).remove();
                                        if ($('tbody tr').length === 0) {
                                            window.location.reload();
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'ERROR',
                                    text: 'Failed to delete the log. Try again.',
                                    icon: 'error',
                                    background: '#0a0e27',
                                    color: '#ffffff'
                                });
                            }
                        });
                    }
                });
            });

            // Handle Logout
            $('#logoutBtn').on('click', function() {
                Swal.fire({
                    title: 'LOGOUT',
                    text: 'Are you sure you want to terminate the session?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4d4d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'TERMINATE',
                    background: '#0a0e27',
                    color: '#ffffff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#logoutForm').submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
