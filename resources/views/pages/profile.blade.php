@if(session('message'))

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageType = '{{ session("message_type", "info") }}';
            let icon, title;
            switch(messageType) {
                case 'success':
                    icon = 'success';
                    title = 'Success';
                    break;
                case 'error':
                case 'danger':
                    icon = 'error';
                    title = 'Error';
                    break;
                case 'warning':
                    icon = 'warning';
                    title = 'Warning';
                    break;
                default:
                    icon = 'info';
                    title = 'Info';
            }
            Swal.fire({
                icon: icon,
                title: title,
                text: @json(session('message')),
                confirmButtonColor: '#667eea',
                customClass: {
                    popup: 'success-alert-popup'
                },
                didOpen: function() {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                        popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                    }
                }
            });
        });
    </script>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<style>
    /* Compact styling for profile page (matches login-monitor adjustments) */
    .compact-scale {
        transform-origin: top center;
        transform: scale(0.92);
        max-width: 100%;
    }
    .compact-scale .card { border-radius: 12px; }
    .compact-scale .card-body { padding: 0.9rem; }
    .compact-scale .rounded-circle { width: 110px !important; height: 110px !important; }
    .compact-scale .card-title { font-size: 1.05rem; font-weight: 700; }
    .compact-scale .form-label { font-size: 0.92rem; }
    .compact-scale .form-control, .compact-scale .form-select { padding: 0.45rem 0.6rem; font-size: 0.92rem; }
    .compact-scale .btn { padding: 0.38rem 0.7rem; font-size: 0.9rem; }
    .compact-scale .profile-action-btn { padding: 0.28rem 0.5rem; font-size: 0.78rem; }
    .compact-scale .profile-action-btn i { font-size: 0.85rem; }
    .compact-scale #sidebarFeatureFilterButton .sidebar-feature-label { font-size: 0.78rem; }
    .compact-scale #sidebarFeatureFilterButton i { font-size: 0.85rem; }
    .compact-scale #sidebarFeatureCount { font-size: 0.7rem; padding: 0.15rem 0.35rem; }
    .compact-scale #sidebarFeatureDropdown .form-check-label { font-size: 0.78rem; }
    .compact-scale .text-muted.small { font-size: 0.78rem; }
    .compact-scale .badge { font-size: 0.78rem; padding: 0.28rem 0.5rem; }
    .compact-scale .table th, .compact-scale .table td { padding: 0.4rem 0.5rem; font-size: 0.86rem; }
    .compact-scale .btn-sm { padding: 0.22rem 0.45rem; font-size: 0.78rem; }
    .compact-scale .card.border-0.shadow-sm { box-shadow: 0 8px 22px rgba(13,20,30,0.06); border-left: 4px solid rgba(102,126,234,0.10); }

    /* Admin table specific tweaks */
    .compact-scale #adminsTable .table-avatar {
        width: 32px !important;
        height: 32px !important;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #667eea;
        display: inline-block;
    }

    .compact-scale .btn-icon {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        line-height: 1;
    }
    .compact-scale .btn-icon i { margin: 0; }
    .compact-scale .btn-icon.btn-outline-primary:hover {
        background-color: rgba(102,126,234,0.08);
    }
    .compact-scale .btn-icon.btn-outline-danger:hover {
        background-color: rgba(220,53,69,0.06);
    }

    /* Status indicator: colored dot + label */
    .compact-scale .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(255,255,255,0.6);
    }
    .compact-scale .status-text { font-size: 0.88rem; }
    .compact-scale .status-success { background-color: #28a745; }
    .compact-scale .status-danger { background-color: #dc3545; }

    /* Modal tweaks: reduce sizes for admin modals only (keeps maps/full screens unaffected) */
    .modal .modal-content { font-size: 0.95rem; }
    .modal .modal-title { font-size: 1rem; }
    .modal .modal-body .form-label { font-size: 0.9rem; }

    /* Specific compact rules for admin modals (further reduced) */
    #addAdminModal .modal-dialog,
    #editAdminModal .modal-dialog { max-width: 760px; }
    #addAdminModal .modal-content,
    #editAdminModal .modal-content,
    .admin-modal .modal-content {
        font-size: 0.86rem;
    }
    #addAdminModal .modal-title,
    #editAdminModal .modal-title,
    .admin-modal .modal-title { font-size: 0.92rem; }
    #addAdminModal .form-label,
    #editAdminModal .form-label,
    .admin-modal .form-label { font-size: 0.82rem; }
    #addAdminModal .form-control,
    #editAdminModal .form-control,
    .admin-modal .form-control,
    #addAdminModal .form-select,
    #editAdminModal .form-select,
    .admin-modal .form-select {
        padding: 0.28rem 0.4rem;
        font-size: 0.84rem; /* tighter inputs */
    }
    #addAdminModal .modal-body .row > [class*="col-"],
    #editAdminModal .modal-body .row > [class*="col-"] {
        margin-bottom: 0.3rem;
    }

    /* Smaller preview images inside modals */
    #addAdminModal #adminPreviewImg {
        max-width: 110px;
        max-height: 110px;
        border-radius: 6px;
    }
    #editAdminModal #editAdminPreviewImg,
    #editAdminModal .rounded-circle {
        width: 70px !important;
        height: 70px !important;
        object-fit: cover;
        border: 2px solid #667eea !important;
    }

    Tighten modal footer buttons for admin modals
    #addAdminModal .modal-footer .btn,
    #editAdminModal .modal-footer .btn {
        padding: 0.28rem 0.48rem;
        font-size: 0.82rem;
    }
    @media (max-width:576px) {
        .compact-scale { transform: scale(0.98); }
        .compact-scale .rounded-circle { width: 120px !important; height: 120px !important; }
    }
</style>

<div class="compact-scale">
<div class="row page-full-width">
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-4">
                    @php
                        $profileImage = Auth::user()->profile_image;
                        $imagePath = '';
                        
                        if ($profileImage && file_exists(public_path('uploads/students/' . $profileImage))) {
                            $imagePath = asset('uploads/students/' . $profileImage);
                        } elseif ($profileImage && file_exists(public_path('uploads/staff/' . $profileImage))) {
                            $imagePath = asset('uploads/staff/' . $profileImage);
                        } else {
                            $imagePath = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name) . '&background=667eea&color=fff&size=200';
                        }
                    @endphp
                    
                    <img src="{{ $imagePath }}" 
                         alt="{{ Auth::user()->full_name }}" 
                         class="rounded-circle border"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #667eea !important;"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=667eea&color=fff&size=200'">
                </div>
                <h5 class="card-title">{{ Auth::user()->full_name }}</h5>
                <p class="text-muted">{{ ucfirst(Auth::user()->role) }}</p>
                @if(Auth::user()->role === 'student')
                    <p class="text-muted small mb-1">{{ Auth::user()->course }} - {{ Auth::user()->year_level ?? 'N/A' }}</p>
                @endif
                <span class="badge bg-{{ Auth::user()->status === 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst(Auth::user()->status) }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Profile Informationcccc
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ url('/dashboard/update-profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                   name="full_name" value="{{ old('full_name', Auth::user()->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   name="username" value="{{ old('username', Auth::user()->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            @if(Auth::user()->role === 'student')
                                <input type="email" class="form-control" 
                                       value="{{ old('email', Auth::user()->email) }}" 
                                       disabled>
                            @else
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', Auth::user()->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        @if(Auth::user()->role === 'student')
                        <div class="col-md-6 mb-3">
                            <label class="form-label">School ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control school-id-input" 
                                   value="{{ old('school_id', Auth::user()->school_id) }}" 
                                   disabled>
                        </div>
                        @endif
                    </div>
                    
                    @if(Auth::user()->role === 'student')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select" disabled>
                                <option value="">Select Course</option>
                                <option value="BSIT" {{ Auth::user()->course === 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSHM" {{ Auth::user()->course === 'BSHM' ? 'selected' : '' }}>BSHM</option>
                                <option value="BSBA" {{ Auth::user()->course === 'BSBA' ? 'selected' : '' }}>BSBA</option>
                                <option value="BSED" {{ Auth::user()->course === 'BSED' ? 'selected' : '' }}>BSED</option>
                                <option value="BEED" {{ Auth::user()->course === 'BEED' ? 'selected' : '' }}>BEED</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select class="form-select" disabled>
                                <option value="">Select Year Level</option>
                                <option value="1st Year" {{ Auth::user()->year_level === '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ Auth::user()->year_level === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ Auth::user()->year_level === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ Auth::user()->year_level === '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" 
                                   value="{{ old('section', Auth::user()->section) }}" 
                                   disabled>
                        </div>
                    </div>
                    <!-- Hidden inputs to preserve values for disabled fields -->
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                    <input type="hidden" name="school_id" value="{{ Auth::user()->school_id }}">
                    <input type="hidden" name="course" value="{{ Auth::user()->course }}">
                    <input type="hidden" name="year_level" value="{{ Auth::user()->year_level }}">
                    <input type="hidden" name="section" value="{{ Auth::user()->section }}">
                    @endif
                    
                    @if(Auth::user()->role === 'admin')
                    <div class="mb-3">
                        <label class="form-label">Position/Department <span class="text-danger">*</span></label>
                        <select class="form-select @error('course') is-invalid @enderror" name="course" required>
                            <option value="">Select Position/Department</option>
                            <option value="IT Department" {{ old('course', Auth::user()->course) === 'IT Department' ? 'selected' : '' }}>IT Department</option>
                            <option value="Computer Science Department" {{ old('course', Auth::user()->course) === 'Computer Science Department' ? 'selected' : '' }}>Computer Science Department</option>
                            <option value="Civil Engineering Department" {{ old('course', Auth::user()->course) === 'Civil Engineering Department' ? 'selected' : '' }}>Civil Engineering Department</option>
                            <option value="Electrical Engineering Department" {{ old('course', Auth::user()->course) === 'Electrical Engineering Department' ? 'selected' : '' }}>Electrical Engineering Department</option>
                            <option value="Mechanical Engineering Department" {{ old('course', Auth::user()->course) === 'Mechanical Engineering Department' ? 'selected' : '' }}>Mechanical Engineering Department</option>
                            <option value="Administration" {{ old('course', Auth::user()->course) === 'Administration' ? 'selected' : '' }}>Administration</option>
                            <option value="Student Affairs" {{ old('course', Auth::user()->course) === 'Student Affairs' ? 'selected' : '' }}>Student Affairs</option>
                            <option value="Academic Affairs" {{ old('course', Auth::user()->course) === 'Academic Affairs' ? 'selected' : '' }}>Academic Affairs</option>
                        </select>
                        @error('course')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control @error('profile_image') is-invalid @enderror" 
                               name="profile_image" accept="image/*" id="profileImageInput">
                        <small class="text-muted">Leave empty to keep current image. Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid #667eea;">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Change Password (Optional)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" 
                                   name="new_password" autocomplete="new-password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="passwordRequirements" class="form-text mt-1" style="{{ $errors->has('new_password') ? '' : 'display:none;' }}">
                                <ul class="mb-0 small">
                                    <li id="req-length" class="text-danger">At least 8 characters</li>
                                    <li id="req-upper" class="text-danger">At least 1 uppercase letter</li>
                                    <li id="req-lower" class="text-danger">At least 1 lowercase letter</li>
                                    <li id="req-number" class="text-danger">At least 1 number</li>
                                    <li id="req-symbol" class="text-danger">At least 1 symbol</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                               name="new_password_confirmation" autocomplete="new-password">
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="passwordMatchText" class="small mt-1"></div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary profile-action-btn">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const newPw = document.getElementById('new_password');
        const confirmPw = document.getElementById('new_password_confirmation');
        const matchText = document.getElementById('passwordMatchText');
        const reqBox = document.getElementById('passwordRequirements');
        const reqs = {
            length: document.getElementById('req-length'),
            upper: document.getElementById('req-upper'),
            lower: document.getElementById('req-lower'),
            number: document.getElementById('req-number'),
            symbol: document.getElementById('req-symbol')
        };

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

        function updateMatch() {
            if (!newPw || !confirmPw || !matchText) return;
            const a = newPw.value;
            const b = confirmPw.value;
            if (!a && !b) {
                matchText.textContent = '';
                return;
            }
            if (a === b) {
                matchText.textContent = 'Passwords match';
                matchText.classList.remove('text-danger');
                matchText.classList.add('text-success');
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.classList.remove('text-success');
                matchText.classList.add('text-danger');
            }
        }

        if (newPw) {
            newPw.addEventListener('focus', () => {
                if (reqBox && newPw.value) reqBox.style.display = '';
            });
            newPw.addEventListener('blur', () => {
                if (reqBox) reqBox.style.display = 'none';
            });
            newPw.addEventListener('input', () => {
                updateRequirements(newPw.value);
                updateMatch();
            });
        }
        if (confirmPw) {
            confirmPw.addEventListener('input', updateMatch);
        }

        // Handle profile form submission

    })();
</script>
@endpush

@php
    $adminSidebarFeatures = [
        ['key' => 'students', 'label' => 'Students'],
        ['key' => 'staff', 'label' => 'Staff'],
        ['key' => 'subject-management', 'label' => 'Subject'],
        ['key' => 'academicyear', 'label' => 'Academic Year'],
        ['key' => 'questionnaires', 'label' => 'Questionnaires'],
        ['key' => 'staff-ratings', 'label' => 'Results: Individual'],
        ['key' => 'department-ratings', 'label' => 'Results: Department'],
        ['key' => 'overall-ratings', 'label' => 'Results: Overall Ranking'],
        ['key' => 'save-evaluations', 'label' => 'Results: Save Evaluations'],
    ];
@endphp
<div id="sidebarFeatureConfig" data-features='@json($adminSidebarFeatures)' style="display: none;"></div>

@if(Auth::user()->canManageAdmins())
<!-- Admin Management Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-users-cog me-2"></i>
                    Admin Management
                </h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center profile-action-btn" type="button" id="sidebarFeatureFilterButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-2"></i>
                            <span class="sidebar-feature-label">Sidebar Filter</span>
                            <span class="badge bg-primary ms-2" id="sidebarFeatureCount" style="display: none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="sidebarFeatureFilterButton" id="sidebarFeatureDropdown" data-features='@json($adminSidebarFeatures)'>
                            <div class="text-muted small mb-2">Hidden items affect all non-main admins.</div>
                            <div class="feature-options">
                                @foreach($adminSidebarFeatures as $feature)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input sidebar-feature-option" type="checkbox" value="{{ $feature['key'] }}" id="sidebar-feature-{{ $feature['key'] }}">
                                        <label class="form-check-label" for="sidebar-feature-{{ $feature['key'] }}">{{ $feature['label'] }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="sidebarFeatureClear">Clear</button>
                                <button type="button" class="btn btn-sm btn-primary" id="sidebarFeatureConfirm">Confirm</button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success profile-action-btn" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="fas fa-plus me-2"></i>Add New Admin
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(isset($admins) && $admins->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="adminsTable">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-image me-1"></i>Image</th>
                                    <th><i class="fas fa-user me-1"></i>Full Name</th>
                                    <th><i class="fas fa-at me-1"></i>Username</th>
                                    <th><i class="fas fa-envelope me-1"></i>Email</th>
                                    <th><i class="fas fa-building me-1"></i>Department</th>
                                    <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                                    <th><i class="fas fa-clock me-1"></i>Last Active</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                    <tr>
                                        <td>
                                            @php
                                                $adminImage = $admin->profile_image;
                                                $adminImagePath = '';
                                                
                                                if ($adminImage && file_exists(public_path('uploads/staff/' . $adminImage))) {
                                                    $adminImagePath = asset('uploads/staff/' . $adminImage);
                                                } else {
                                                    $adminImagePath = 'https://ui-avatars.com/api/?name=' . urlencode($admin->full_name) . '&background=667eea&color=fff&size=50';
                                                }
                                            @endphp
                                    <img src="{{ $adminImagePath }}" 
                                        alt="{{ $admin->full_name }}" 
                                        class="table-avatar"
                                        style="width:32px; height:32px; object-fit: cover;"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($admin->full_name) }}&background=667eea&color=fff&size=50'">
                                        </td>
                                        <td><strong>{{ $admin->full_name }}</strong></td>
                                        <td>{{ $admin->username }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->course ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $hasActivity = !empty($admin->last_active_at) || !empty($admin->last_login);
                                                $isActive = ($admin->status === 'active') && $hasActivity; // Only show Active when status is active AND has activity
                                                $statusLabel = $isActive ? 'Active' : 'Inactive';
                                                $statusClass = $isActive ? 'success' : 'danger';
                                                $statusIcon = $isActive ? 'fa-check-circle' : 'fa-times-circle';
                                            @endphp
                                            <span class="d-inline-flex align-items-center">
                                                <span class="status-dot {{ $isActive ? 'status-success' : 'status-danger' }} me-2" aria-hidden="true"></span>
                                                <span class="status-text">{{ $statusLabel }}</span>
                                            </span>
                                            @if($admin->id === Auth::id())
                                                <span class="badge bg-primary ms-1">
                                                    <i class="fas fa-user me-1"></i>You
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($admin->last_active_at)
                                                <small class="text-muted">
                                                    {{ $admin->last_active_at->diffForHumans() }}
                                                </small>
                                            @elseif($admin->last_login)
                                                <small class="text-muted">
                                                    {{ $admin->last_login->diffForHumans() }}
                                                </small>
                                            @else
                                                <small class="text-muted">Never</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-icon" data-bs-toggle="modal" data-bs-target="#editAdminModal" 
                                                        onclick="loadAdminData({{ $admin->id }}, '{{ addslashes($admin->full_name) }}', '{{ addslashes($admin->username) }}', '{{ addslashes($admin->email) }}', '{{ addslashes($admin->course ?? '') }}')"
                                                        title="Edit Admin" {{ $admin->id === Auth::id() ? 'disabled' : '' }}>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-icon ms-2" data-bs-toggle="modal" data-bs-target="#deleteAdminModal" 
                                                        onclick="deleteAdmin({{ $admin->id }}, '{{ addslashes($admin->full_name) }}')"
                                                        title="Delete Admin" {{ $admin->id === Auth::id() ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No admins found. Start by adding your first admin!</p>
                        <button class="btn btn-success profile-action-btn" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="fas fa-plus me-2"></i>Add Your First Admin
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('admin_full_name') is-invalid @enderror" 
                                   name="admin_full_name" value="{{ old('admin_full_name') }}" required>
                            @error('admin_full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('admin_username') is-invalid @enderror" 
                                   name="admin_username" value="{{ old('admin_username') }}" required>
                            @error('admin_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   name="admin_email" value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position/Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('admin_course') is-invalid @enderror" name="admin_course" required>
                                <option value="">Select Position/Department</option>
                                <option value="IT Department" {{ old('admin_course') === 'IT Department' ? 'selected' : '' }}>IT Department</option>
                                <option value="Computer Science Department" {{ old('admin_course') === 'Computer Science Department' ? 'selected' : '' }}>Computer Science Department</option>
                                <option value="Civil Engineering Department" {{ old('admin_course') === 'Civil Engineering Department' ? 'selected' : '' }}>Civil Engineering Department</option>
                                <option value="Electrical Engineering Department" {{ old('admin_course') === 'Electrical Engineering Department' ? 'selected' : '' }}>Electrical Engineering Department</option>
                                <option value="Mechanical Engineering Department" {{ old('admin_course') === 'Mechanical Engineering Department' ? 'selected' : '' }}>Mechanical Engineering Department</option>
                                <option value="Administration" {{ old('admin_course') === 'Administration' ? 'selected' : '' }}>Administration</option>
                                <option value="Student Affairs" {{ old('admin_course') === 'Student Affairs' ? 'selected' : '' }}>Student Affairs</option>
                                <option value="Academic Affairs" {{ old('admin_course') === 'Academic Affairs' ? 'selected' : '' }}>Academic Affairs</option>
                            </select>
                            @error('admin_course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                   name="admin_password" required>
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('admin_password_confirmation') is-invalid @enderror" 
                                   name="admin_password_confirmation" required>
                            @error('admin_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control @error('admin_profile_image') is-invalid @enderror" 
                               name="admin_profile_image" accept="image/*" id="adminImageInput">
                        <small class="text-muted">Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                        @error('admin_profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Admin Image Preview -->
                        <div id="adminImagePreview" class="mt-2" style="display: none;">
                            <img id="adminPreviewImg" src="" alt="Preview" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid #667eea;">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i>Add Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Profile Image Preview for Edit Admin Modal (Top Center, Circle) -->
                <div id="editAdminImagePreview" class="w-100 d-flex justify-content-center align-items-center mb-3" style="display: none;">
                    <img id="editAdminPreviewImg" src="" alt="Preview"
                         class="rounded-circle border"
                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #667eea !important;">
                </div>
                <div class="mb-3 text-center">
                    <label class="form-label">Profile Image</label>
                    <input type="file" class="form-control d-inline-block w-auto" name="admin_profile_image" accept="image/*" id="editAdminImageInput">
                    <small class="text-muted d-block">Leave empty to keep current image</small>
                </div>
                <form action="{{ url('/dashboard/update-admin') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="admin_id" id="editAdminId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editAdminFullName" name="admin_full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editAdminUsername" name="admin_username" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="editAdminEmail" name="admin_email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position/Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="editAdminCourse" name="admin_course" required>
                                <option value="">Select Position/Department</option>
                                <option value="IT Department">IT Department</option>
                                <option value="Computer Science Department">Computer Science Department</option>
                                <option value="Civil Engineering Department">Civil Engineering Department</option>
                                <option value="Electrical Engineering Department">Electrical Engineering Department</option>
                                <option value="Mechanical Engineering Department">Mechanical Engineering Department</option>
                                <option value="Administration">Administration</option>
                                <option value="Student Affairs">Student Affairs</option>
                                <option value="Academic Affairs">Academic Affairs</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Admin Modal -->
<div class="modal fade" id="deleteAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete admin <strong id="adminName"></strong>?</p>
                <p class="text-muted small"><i class="fas fa-info-circle me-1"></i>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ url('/dashboard/delete-admin') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="admin_id" id="deleteAdminId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const featureConfigElement = document.getElementById('sidebarFeatureConfig');
    const isMainAdmin = @json(Auth::user()->isMainAdmin());
    const isAdminRole = @json(Auth::user()->isAdmin());
    let featureConfig = [];
    if (featureConfigElement) {
        try {
            const parsed = JSON.parse(featureConfigElement.dataset.features || '[]');
            if (Array.isArray(parsed)) {
                featureConfig = parsed;
            }
        } catch (e) {}
    }
    const featureMap = featureConfig.reduce((acc, item) => {
        if (item.key) {
            acc[item.key] = item.label || item.key;
        }
        return acc;
    }, {});
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
    const featureDropdown = document.getElementById('sidebarFeatureDropdown');
    const featureOptions = featureDropdown ? Array.from(featureDropdown.querySelectorAll('.sidebar-feature-option')) : [];
    const featureConfirmBtn = document.getElementById('sidebarFeatureConfirm');
    const featureClearBtn = document.getElementById('sidebarFeatureClear');

    const featureCount = document.getElementById('sidebarFeatureCount');
    let currentKeys = [];

    function loadStoredKeys() {
        // Load from server via AJAX
        return fetch('{{ route("sidebar.settings.get") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.disabled_features && Array.isArray(data.disabled_features)) {
                return Array.from(new Set(data.disabled_features.filter(key => featureMap[key])));
            }
            return [];
        })
        .catch(error => {
            console.error('Error loading sidebar settings:', error);
            return [];
        });
    }

    function persistKeys(keys) {
        // Save to server via AJAX
        return fetch('{{ route("sidebar.settings.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                disabled_features: keys
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to save settings');
            }
        })
        .catch(error => {
            console.error('Error saving sidebar settings:', error);
            throw error;
        });
    }
    function updateCheckboxes(keys) {
        if (!featureOptions.length) return;
        featureOptions.forEach(option => {
            option.checked = keys.includes(option.value);
        });
    }

    function applyVisibility(keys) {
        if (!isAdminRole) return;
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
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
            if (!isMainAdmin) {
                const items = Array.from(resultsDropdown.querySelectorAll('.nav-dropdown-menu .nav-dropdown-item'));
                const visible = items.some(item => item && item.style.display !== 'none');
                if (toggle) {
                    toggle.style.display = visible ? '' : 'none';
                }
            } else if (toggle) {
                toggle.style.display = '';
            }
        }
    }
    function commitKeys(keys) {
        currentKeys = Array.from(new Set(keys.filter(key => featureMap[key])));
        updateCheckboxes(currentKeys);
        applyVisibility(currentKeys);

        // Persist to server asynchronously
        persistKeys(currentKeys)
            .then(() => {
                // Show success message and reload page
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Sidebar settings have been saved successfully.',
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                console.error('Failed to save sidebar settings:', error);
                // Show error message to user
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save sidebar settings. Please try again.'
                });
            });
    }

    // Initialize settings on page load
    loadStoredKeys().then(keys => {
        currentKeys = keys;
        updateCheckboxes(currentKeys);
        applyVisibility(currentKeys);
    });
    if (featureDropdown) {
        featureDropdown.addEventListener('show.bs.dropdown', function() {
            updateCheckboxes(currentKeys);
        });
    }
    if (featureConfirmBtn) {
        featureConfirmBtn.addEventListener('click', function() {
            const keys = featureOptions.filter(option => option.checked).map(option => option.value);
            const selectedLabels = keys.map(key => featureMap[key] || key);

            if (keys.length === 0) {
                // If no features selected, confirm clearing all restrictions
                Swal.fire({
                    title: 'Clear All Restrictions?',
                    text: 'This will make all sidebar features visible to non-main admins. Are you sure?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Clear All',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        commitKeys(keys);
                        // Close the dropdown
                        const dropdown = bootstrap.Dropdown.getInstance(featureDropdown.closest('.dropdown'));
                        if (dropdown) dropdown.hide();
                    }
                });
            } else {
                // If features selected, confirm hiding them
                Swal.fire({
                    title: 'Confirm Sidebar Restrictions',
                    html: `The following features will be hidden from non-main admins:<br><br><strong>${selectedLabels.join(', ')}</strong><br><br>Are you sure you want to apply these changes?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Apply Changes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        commitKeys(keys);
                        // Close the dropdown
                        const dropdown = bootstrap.Dropdown.getInstance(featureDropdown.closest('.dropdown'));
                        if (dropdown) dropdown.hide();
                    }
                });
            }
        });
    }
    if (featureClearBtn) {
        featureClearBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Clear All Restrictions?',
                text: 'This will make all sidebar features visible to non-main admins. Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Clear All',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    commitKeys([]);
                    // Close the dropdown
                    const dropdown = bootstrap.Dropdown.getInstance(featureDropdown.closest('.dropdown'));
                    if (dropdown) dropdown.hide();
                }
            });
        });
    }


    // Profile image preview functionality
    const profileImageInput = document.getElementById('profileImageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
    
    // Admin image preview functionality
    const adminImageInput = document.getElementById('adminImageInput');
    const adminImagePreview = document.getElementById('adminImagePreview');
    const adminPreviewImg = document.getElementById('adminPreviewImg');
    
    if (adminImageInput) {
        adminImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    adminImagePreview.style.display = 'none';
                    return;
                }
                
                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    this.value = '';
                    adminImagePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    adminPreviewImg.src = e.target.result;
                    adminImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                adminImagePreview.style.display = 'none';
            }
        });
    }

    // Edit Admin image preview functionality
    const editAdminImageInput = document.getElementById('editAdminImageInput');
    const editAdminImagePreview = document.getElementById('editAdminImagePreview');
    const editAdminPreviewImg = document.getElementById('editAdminPreviewImg');
    if (editAdminImageInput) {
        editAdminImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    editAdminImagePreview.style.display = 'none';
                    return;
                }
                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    this.value = '';
                    editAdminImagePreview.style.display = 'none';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    editAdminPreviewImg.src = e.target.result;
                    editAdminImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                // If no file selected, keep the current image
                if (editAdminPreviewImg && editAdminPreviewImg.src) {
                    editAdminImagePreview.style.display = 'block';
                } else {
                    editAdminImagePreview.style.display = 'none';
                }
            }
        });
    }
});

function loadAdminData(id, fullName, username, email, course) {
    document.getElementById('editAdminId').value = id;
    document.getElementById('editAdminFullName').value = fullName;
    document.getElementById('editAdminUsername').value = username;
    document.getElementById('editAdminEmail').value = email;
    document.getElementById('editAdminCourse').value = course;

    // Set the profile image preview for edit modal
    let editAdminImagePreview = document.getElementById('editAdminImagePreview');
    let editAdminPreviewImg = document.getElementById('editAdminPreviewImg');
    if (editAdminImagePreview && editAdminPreviewImg) {
        // Try to get the image from the admins table row (if available)
        // Fallback to avatar if not found
        let row = document.querySelector(`#adminsTable tr td button[onclick*='${id}']`);
        let imgSrc = '';
        if (row) {
            let img = row.closest('tr').querySelector('img');
            if (img) {
                imgSrc = img.src;
            }
        }
        if (!imgSrc) {
            // fallback avatar
            imgSrc = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&background=667eea&color=fff&size=200';
        }
        editAdminPreviewImg.src = imgSrc;
        editAdminImagePreview.style.display = 'block';
    }
    // Reset file input
    let editAdminImageInput = document.getElementById('editAdminImageInput');
    if (editAdminImageInput) {
        editAdminImageInput.value = '';
    }
}

function deleteAdmin(id, name) {
    document.getElementById('deleteAdminId').value = id;
    document.getElementById('adminName').textContent = name;
}

document.querySelectorAll('.school-id-input').forEach(function(input) {
    input.addEventListener('input', function(e) {
        // Only allow numbers and a single dash at the 5th position
        let value = this.value.replace(/[^0-9-]/g, '');

        // Enforce format: 0000-0000
        if (value.length > 9) value = value.slice(0, 9);

        // Only allow dash at position 5
        if (value.length > 4) {
            value = value.slice(0, 4) + '-' + value.slice(5).replace(/-/g, '');
        } else {
            value = value.replace(/-/g, '');
        }

        this.value = value;
    });
});
</script> 