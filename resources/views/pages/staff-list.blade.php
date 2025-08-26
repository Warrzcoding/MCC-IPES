@php
    // Fetch all staff members
    $staff_members = \App\Models\Staff::orderBy('staff_type')->get();
    
    // Separate teaching and non-teaching staff
    $teaching_staff = $staff_members->where('staff_type', 'teaching');
    $non_teaching_staff = $staff_members->where('staff_type', 'non-teaching');
@endphp

<style>
    .modal-header {
        background-color: #007bff;
        color: white;
    }
    .table thead th {
        background-color: #007bff;
        color: white;
        white-space: nowrap;
    }
    .modal-body {
        text-align: center;
    }
    .staff-image {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
    .default-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ddd;
        color: #6c757d;
        font-weight: bold;
        font-size: 14px;
        margin: 0 auto;
    }
    .modal-staff-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ddd;
        margin-bottom: 15px;
    }
    .modal-default-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ddd;
        color: #6c757d;
        font-weight: bold;
        font-size: 36px;
        margin: 0 auto 15px auto;
    }
    
    /* New styles for enhanced layout */
    .staff-container {
        height: calc(100vh - 200px);
        overflow-y: auto;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
    }
    
    .staff-item {
        display: flex;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(102,126,234,0.18), 0 1.5px 8px rgba(0,0,0,0.10);
        margin-bottom: 15px;
        padding: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .staff-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(102,126,234,0.28), 0 2px 12px rgba(0,0,0,0.13);
    }
    
    .staff-image-container {
        flex-shrink: 0;
        margin-right: 20px;
    }
    
    .staff-details {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .staff-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .staff-email {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .staff-phone {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .staff-department {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .staff-type-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .staff-type-teaching {
        background-color: #d4edda;
        color: #155724;
    }
    
    .staff-type-non-teaching {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .nav-tabs {
        border: none;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 0;
        display: flex;
        justify-content: space-between;
        gap: 4px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-radius: 12px;
        margin: 0 4px;
        padding: 14px 20px;
        font-weight: 600;
        color: #6c757d;
        background: transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        width: 100%;
        min-width: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        white-space: nowrap;
    }
    
    .nav-tabs .nav-link:hover {
        background: rgba(0,123,255,0.1);
        color: #007bff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.2);
    }
    
    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,123,255,0.3);
        position: relative;
    }
    
    .nav-tabs .nav-link.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 12px;
        pointer-events: none;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover i {
        transform: scale(1.1);
    }
    
    .nav-tabs .nav-link.active i {
        transform: scale(1.15);
    }
    
    /* Enhanced badge styling for staff count */
    .staff-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        box-shadow: 0 2px 4px rgba(255,107,107,0.3);
        margin-left: 6px;
        position: relative;
        animation: badgePulse 2s infinite;
    }
    
    .staff-count-badge::before {
        content: '';
        position: absolute;
        top: -1px;
        left: -1px;
        right: -1px;
        bottom: -1px;
        background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
        border-radius: 10px;
        z-index: -1;
    }
    
    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 2px 4px rgba(255,107,107,0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(255,107,107,0.4);
        }
    }
    
    /* Mobile badge styling */
    @media (max-width: 768px) {
        .staff-count-badge {
            font-size: 0.7rem;
            padding: 1px 4px;
            min-width: 18px;
            height: 18px;
            margin-left: 4px;
        }
    }
    
    @media (max-width: 576px) {
        .staff-count-badge {
            font-size: 0.65rem;
            padding: 1px 3px;
            min-width: 16px;
            height: 16px;
            margin-left: 2px;
            border-radius: 8px;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.7rem;
        }
        
        .nav-tabs .nav-link i {
            font-size: 0.75rem;
        }
    }
    
    .tab-content {
        background: white;
        border-radius: 15px;
        padding: 20px;
        min-height: 400px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-top: 15px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .search-container {
        margin-bottom: 20px;
    }
    
    .search-input {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        padding: 12px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    /* Overlay Modal Styles */
    .overlay-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
        backdrop-filter: blur(5px);
    }
    
    .overlay-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .close-overlay {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .close-overlay:hover {
        color: #000;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .staff-item {
            flex-direction: column;
            text-align: center;
        }
        
        .staff-image-container {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .staff-container {
            height: calc(100vh - 250px);
        }
        
        .nav-tabs {
            padding: 6px;
            border-radius: 12px;
        }
        
        .nav-tabs .nav-link {
            padding: 10px 12px;
            font-size: 0.85rem;
            margin: 0 2px;
            border-radius: 8px;
            letter-spacing: 0.2px;
            width: 100%;
            min-width: 0;
            flex: 1;
        }
        
        .nav-tabs .nav-link i {
            font-size: 1rem;
            margin-right: 6px;
        }
    }
    
    @media (max-width: 576px) {
        .staff-container {
            padding: 10px;
        }
        
        .staff-item {
            padding: 12px;
        }
        
        .overlay-content {
            width: 95%;
            padding: 20px;
        }
        
        .nav-tabs {
            padding: 4px;
            border-radius: 10px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 2px;
        }
        
        .nav-tabs .nav-item {
            flex: 1;
            min-width: 0;
        }
        
        .nav-tabs .nav-link {
            padding: 8px 6px;
            font-size: 0.75rem;
            margin: 0;
            border-radius: 6px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            width: 100%;
            min-width: 0;
            flex: 1;
        }
        
        .nav-tabs .nav-link i {
            font-size: 0.8rem;
            margin-right: 0;
        }
        
        .nav-tabs .nav-link:hover {
            transform: translateY(0);
        }
        
        .nav-tabs .nav-link.active {
            transform: translateY(-1px);
        }
    }
    
    @media (max-width: 480px) {
        .nav-tabs .nav-link {
            padding: 6px 4px;
            font-size: 0.7rem;
            width: 100%;
            min-width: 0;
            flex: 1;
        }
        
        .nav-tabs .nav-link i {
            font-size: 0.75rem;
            margin-right: 0;
        }
        
        .nav-tabs .nav-link span:not(.staff-count-badge) {
            font-size: 0.65rem;
        }
        
        .staff-count-badge {
            font-size: 0.6rem;
            padding: 1px 2px;
            min-width: 14px;
            height: 14px;
            margin-left: 1px;
        }
    }
    
    @media (max-width: 360px) {
        .nav-tabs .nav-link {
            padding: 4px 2px;
            font-size: 0.65rem;
            width: 100%;
            min-width: 0;
            flex: 1;
        }
        
        .nav-tabs .nav-link i {
            font-size: 0.7rem;
        }
        
        .nav-tabs .nav-link span:not(.staff-count-badge) {
            font-size: 0.6rem;
        }
        
        .staff-count-badge {
            font-size: 0.55rem;
            padding: 0px 1px;
            min-width: 12px;
            height: 12px;
            margin-left: 1px;
        }
    }
</style>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <h5 class="m-0 font-weight-bold text-primary mb-3">All Staff Members</h5>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Staff Directory:</strong> Click on any staff member to view detailed information.
            </div>
            
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by name, email, department, or phone..." onkeyup="filterStaff()">
            </div>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="staffTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        <i class="fas fa-users"></i>
                        <span>All Staff</span>
                        <span class="staff-count-badge">{{ $staff_members->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="teaching-tab" data-bs-toggle="tab" data-bs-target="#teaching" type="button" role="tab">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Teaching</span>
                        <span class="staff-count-badge">{{ $teaching_staff->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="non-teaching-tab" data-bs-toggle="tab" data-bs-target="#non-teaching" type="button" role="tab">
                        <i class="fas fa-user-tie"></i>
                        <span>Non-Teaching</span>
                        <span class="staff-count-badge">{{ $non_teaching_staff->count() }}</span>
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="staffTabsContent">
                <!-- All Staff Tab -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="staff-container" id="allStaffContainer">
                        @foreach ($staff_members as $staff)
                            @php
                                $imagePath = '';
                                if (!empty($staff->image_path)) {
                                    if (filter_var($staff->image_path, FILTER_VALIDATE_URL)) {
                                        $imagePath = $staff->image_path;
                                    } else {
                                        $possiblePaths = [
                                            $staff->image_path,
                                            'uploads/staff/' . $staff->image_path,
                                            'storage/app/public/' . $staff->image_path,
                                            'storage/' . $staff->image_path
                                        ];
                                        foreach ($possiblePaths as $path) {
                                            $fullPath = public_path($path);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset($path);
                                                break;
                                            }
                                        }
                                        if (empty($imagePath) && !empty($staff->image_path)) {
                                            $filename = basename($staff->image_path);
                                            $fullPath = public_path('uploads/staff/' . $filename);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset('uploads/staff/' . $filename);
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="staff-item" onclick="showStaffDetails('{{ $staff->full_name }}', '{{ $staff->email }}', '{{ $staff->phone }}', '{{ $staff->department }}', '{{ $staff->staff_type }}', '{{ $imagePath }}')">
                                <div class="staff-image-container">
                                    @if ($imagePath)
                                        <img src="{{ $imagePath }}" alt="{{ $staff->full_name }}" class="staff-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="default-avatar" style="display: none;">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @else
                                        <div class="default-avatar">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @endif
                                </div>
                                <div class="staff-details">
                                    <div class="staff-name">{{ $staff->full_name }}</div>
                                    <div class="staff-email">{{ $staff->email }}</div>
                                    <div class="staff-phone">{{ $staff->phone }}</div>
                                    <div class="staff-department">{{ $staff->department }}</div>
                                    <span class="staff-type-badge {{ $staff->staff_type == 'teaching' ? 'staff-type-teaching' : 'staff-type-non-teaching' }}">
                                        {{ ucfirst(str_replace('-', ' ', $staff->staff_type)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Teaching Staff Tab -->
                <div class="tab-pane fade" id="teaching" role="tabpanel">
                    <div class="staff-container" id="teachingStaffContainer">
                        @foreach ($teaching_staff as $staff)
                            @php
                                $imagePath = '';
                                if (!empty($staff->image_path)) {
                                    if (filter_var($staff->image_path, FILTER_VALIDATE_URL)) {
                                        $imagePath = $staff->image_path;
                                    } else {
                                        $possiblePaths = [
                                            $staff->image_path,
                                            'uploads/staff/' . $staff->image_path,
                                            'storage/app/public/' . $staff->image_path,
                                            'storage/' . $staff->image_path
                                        ];
                                        foreach ($possiblePaths as $path) {
                                            $fullPath = public_path($path);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset($path);
                                                break;
                                            }
                                        }
                                        if (empty($imagePath) && !empty($staff->image_path)) {
                                            $filename = basename($staff->image_path);
                                            $fullPath = public_path('uploads/staff/' . $filename);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset('uploads/staff/' . $filename);
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="staff-item" onclick="showStaffDetails('{{ $staff->full_name }}', '{{ $staff->email }}', '{{ $staff->phone }}', '{{ $staff->department }}', '{{ $staff->staff_type }}', '{{ $imagePath }}')">
                                <div class="staff-image-container">
                                    @if ($imagePath)
                                        <img src="{{ $imagePath }}" alt="{{ $staff->full_name }}" class="staff-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="default-avatar" style="display: none;">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @else
                                        <div class="default-avatar">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @endif
                                </div>
                                <div class="staff-details">
                                    <div class="staff-name">{{ $staff->full_name }}</div>
                                    <div class="staff-email">{{ $staff->email }}</div>
                                    <div class="staff-phone">{{ $staff->phone }}</div>
                                    <div class="staff-department">{{ $staff->department }}</div>
                                    <span class="staff-type-badge staff-type-teaching">{{ $staff->staff_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Non-Teaching Staff Tab -->
                <div class="tab-pane fade" id="non-teaching" role="tabpanel">
                    <div class="staff-container" id="nonTeachingStaffContainer">
                        @foreach ($non_teaching_staff as $staff)
                            @php
                                $imagePath = '';
                                if (!empty($staff->image_path)) {
                                    if (filter_var($staff->image_path, FILTER_VALIDATE_URL)) {
                                        $imagePath = $staff->image_path;
                                    } else {
                                        $possiblePaths = [
                                            $staff->image_path,
                                            'uploads/staff/' . $staff->image_path,
                                            'storage/app/public/' . $staff->image_path,
                                            'storage/' . $staff->image_path
                                        ];
                                        foreach ($possiblePaths as $path) {
                                            $fullPath = public_path($path);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset($path);
                                                break;
                                            }
                                        }
                                        if (empty($imagePath) && !empty($staff->image_path)) {
                                            $filename = basename($staff->image_path);
                                            $fullPath = public_path('uploads/staff/' . $filename);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset('uploads/staff/' . $filename);
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="staff-item" onclick="showStaffDetails('{{ $staff->full_name }}', '{{ $staff->email }}', '{{ $staff->phone }}', '{{ $staff->department }}', '{{ $staff->staff_type }}', '{{ $imagePath }}')">
                                <div class="staff-image-container">
                                    @if ($imagePath)
                                        <img src="{{ $imagePath }}" alt="{{ $staff->full_name }}" class="staff-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="default-avatar" style="display: none;">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @else
                                        <div class="default-avatar">{{ $staff->initial ?? strtoupper(substr($staff->full_name, 0, 1)) }}</div>
                                    @endif
                                </div>
                                <div class="staff-details">
                                    <div class="staff-name">{{ $staff->full_name }}</div>
                                    <div class="staff-email">{{ $staff->email }}</div>
                                    <div class="staff-phone">{{ $staff->phone }}</div>
                                    <div class="staff-department">{{ $staff->department }}</div>
                                    <span class="staff-type-badge staff-type-non-teaching">{{ $staff->staff_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Modal -->
<div id="staffOverlay" class="overlay-modal">
    <div class="overlay-content">
        <span class="close-overlay" onclick="closeStaffOverlay()">&times;</span>
        <div class="text-center mb-4">
            <div id="overlayImageContainer" class="mb-3"></div>
            <h3 id="overlayName" class="fw-bold mb-2"></h3>
            <span id="overlayType" class="badge fs-6"></span>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-envelope fa-fw text-primary me-3" style="width: 20px;"></i>
                    <div>
                        <small class="text-muted d-block">Email</small>
                        <span id="overlayEmail" class="fw-semibold"></span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-phone fa-fw text-primary me-3" style="width: 20px;"></i>
                    <div>
                        <small class="text-muted d-block">Phone</small>
                        <span id="overlayPhone" class="fw-semibold"></span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-building fa-fw text-primary me-3" style="width: 20px;"></i>
                    <div>
                        <small class="text-muted d-block">Department</small>
                        <span id="overlayDepartment" class="fw-semibold"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Staff search filter function
function filterStaff() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    
    // Get all active tab containers
    var containers = ['allStaffContainer', 'teachingStaffContainer', 'nonTeachingStaffContainer'];
    
    containers.forEach(function(containerId) {
        var container = document.getElementById(containerId);
        if (container) {
            var items = container.getElementsByClassName("staff-item");
            
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                var name = item.querySelector('.staff-name').textContent || '';
                var email = item.querySelector('.staff-email').textContent || '';
                var phone = item.querySelector('.staff-phone').textContent || '';
                var department = item.querySelector('.staff-department').textContent || '';
                var type = item.querySelector('.staff-type-badge').textContent || '';
                
                var text = (name + ' ' + email + ' ' + phone + ' ' + department + ' ' + type).toLowerCase();
                
                if (text.indexOf(filter) > -1) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            }
        }
    });
}

// Show staff details overlay
function showStaffDetails(name, email, phone, department, type, image) {
    document.getElementById('overlayName').textContent = name;
    document.getElementById('overlayEmail').textContent = email;
    document.getElementById('overlayPhone').textContent = phone;
    document.getElementById('overlayDepartment').textContent = department;
    
    var typeBadge = document.getElementById('overlayType');
    // Format the type for display (capitalize and replace hyphens with spaces)
    var displayType = type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ');
    typeBadge.textContent = displayType;
    typeBadge.className = 'badge fs-6 ' + (type === 'teaching' ? 'bg-success' : 'bg-info');
    
    var imageContainer = document.getElementById('overlayImageContainer');
    if (image && image.trim() !== '') {
        const img = new Image();
        img.onload = function() {
            imageContainer.innerHTML = '<img src="' + image + '" alt="' + name + '" class="modal-staff-image shadow" style="border:4px solid #007bff;">';
        };
        img.onerror = function() {
            const firstLetter = name.charAt(0).toUpperCase();
            imageContainer.innerHTML = '<div class="modal-default-avatar shadow">' + firstLetter + '</div>';
        };
        img.src = image;
    } else {
        const firstLetter = name.charAt(0).toUpperCase();
        imageContainer.innerHTML = '<div class="modal-default-avatar shadow">' + firstLetter + '</div>';
    }
    
    document.getElementById('staffOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Close staff overlay
function closeStaffOverlay() {
    document.getElementById('staffOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close overlay when clicking outside
document.getElementById('staffOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStaffOverlay();
    }
});

// Close overlay with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStaffOverlay();
    }
});

// Initialize Bootstrap tabs
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tabs are already initialized by Bootstrap
    // Just ensure the search works across all tabs
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterStaff);
    }
});
</script> 