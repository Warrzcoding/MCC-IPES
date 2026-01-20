<meta name="csrf-token" content="{{ csrf_token() }}">

@php
// Function to get rating status and color
function getRatingStatus($rating) {
    if ($rating >= 4.51) return ['status' => 'Outstanding', 'color' => '#28a745', 'bg' => '#d4edda'];
    if ($rating >= 3.51) return ['status' => 'Very Satisfactory', 'color' => '#17a2b8', 'bg' => '#d1ecf1'];
    if ($rating >= 2.51) return ['status' => 'Satisfactory', 'color' => '#ffc107', 'bg' => '#fff3cd'];
    if ($rating >= 1.51) return ['status' => 'Unsatisfactory', 'color' => '#fd7e14', 'bg' => '#ffeaa7'];
    return ['status' => 'Poor', 'color' => '#dc3545', 'bg' => '#f8d7da'];
}

// Function to get adjectival descriptive rating
function getAdjectivalRating($rating) {
    if ($rating >= 4.51) return 'Outstanding';
    if ($rating >= 3.51) return 'Very Satisfactory';
    if ($rating >= 2.51) return 'Satisfactory';
    if ($rating >= 1.51) return 'Unsatisfactory';
    return 'Poor';
}
@endphp

<style>
    .rating-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .rating-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .staff-image {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    .default-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        color: white;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    .staff-info {
        font-size: 0.72rem;
        line-height: 1.35;
    }
    .staff-info .staff-name {
        display: inline-block;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.1rem;
    }
    .staff-info .staff-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.66rem;
        color: #6c757d;
    }
    .staff-info .staff-meta i {
        font-size: 0.62rem;
    }
    .staff-info .staff-badge {
        font-size: 0.58rem;
        padding: 0.2em 0.5em;
        border-radius: 10px;
        letter-spacing: 0.3px;
    }
    .rating-badge {
        font-size: 0.68rem;
        padding: 0.28em 0.6em;
        border-radius: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .rating-stars {
        color: #ffc107;
        font-size: 0.78rem;
    }
    .rating-number {
        font-size: 1rem;
        font-weight: 600;
    }
    .stats-item {
        text-align: center;
        padding: 0.35rem;
    }
    .stats-number {
        font-size: 0.84rem;
        font-weight: 600;
        color: #007bff;
    }
    .stats-label {
        font-size: 0.58rem;
        color: #6c757d;
        text-transform: uppercase;
    }
    .search-box {
        margin-bottom: 20px;
        position: relative;
    }
    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    #searchInput {
        padding-left: 34px;
        border-radius: 18px;
        border: 1px solid #e7ebf2;
        font-size: 0.78rem;
        height: 1.95rem;
    }
    #searchInput::placeholder {
        font-size: 0.7rem;
        color: #9aa5b5;
    }
    #searchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.16rem rgba(0,123,255,.2);
    }
    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.78rem;
    }
    .action-btn {
        padding: 0.18rem 0.4rem;
        font-size: 0.68rem;
        border-radius: 12px;
    }
    .comments-modal .modal-body {
        max-height: 400px;
        overflow-y: auto;
    }
    .comment-item {
        border-left: 4px solid #007bff;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        border-radius: 0 8px 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .comment-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 8px;
    }
    .comment-date {
        font-size: 0.8em;
        color: #6c757d;
    }
    .no-comments {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    .range-bar {
        width: 100%;
        height: 8px;
        border-radius: 6px;
        background: #e9ecef;
        margin-top: 6px;
        margin-bottom: 2px;
        position: relative;
    }
    .range-bar-fill {
        height: 100%;
        border-radius: 6px;
        position: absolute;
        left: 0;
        top: 0;
    }
    .range-bar-green { background: #28a745; }
    .range-bar-blue { background: #17a2b8; }
    .range-bar-yellow { background: #ffc107; }
    .range-bar-orange { background: #fd7e14; }
    .range-bar-red { background: #dc3545; }
    .range-legend {
        font-size: 1em;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 18px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .range-legend span.color {
        display: inline-block;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        border: 2px solid #fff;
        outline: 1.5px solid #bbb;
    }
    .range-legend-label {
        font-weight: 600;
        color: #333;
        letter-spacing: 0.5px;
        font-size: 0.98em;
    }
    @media print {
        /* Hide everything except our custom print area */
        body * { visibility: hidden !important; }
        #customPrintArea, #customPrintArea * { visibility: visible !important; }
        #customPrintArea { 
            position: absolute !important; 
            left: 0 !important; 
            top: 0 !important; 
            width: 100% !important; 
            padding: 20px !important;
            background: white !important;
        }
        /* Remove browser headers/footers */
        @page { 
            margin: 0 !important; 
            size: A4;
        }
    }
    .report-header {
        width: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 2.2em 0 1.2em 0;
        margin-bottom: 2em;
        text-align: center;
        border-radius: 0.7em 0.7em 0 0;
        box-shadow: 0 2px 12px rgba(102,126,234,0.08);
    }
    .report-logo {
        width: 110px;
        height: auto;
        margin-bottom: 1em;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .report-title {
        font-size: 2.1em;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 0.2em;
        color: #fff;
        text-shadow: 0 2px 8px rgba(44,62,80,0.08);
    }
    .report-date {
        color: #e0e0e0;
        font-size: 1em;
        margin-bottom: 1.2em;
    }
    .report-section-title {
        font-size: 1.2em;
        font-weight: 600;
        margin-top: 2em;
        margin-bottom: 0.7em;
        color: #444;
        border-bottom: 2px solid #eee;
        padding-bottom: 0.2em;
    }
    #reportTable th, #reportTable td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.62rem;
        padding: 0.3rem 0.35rem;
    }
    #reportTable th.name-col, #reportTable td.name-col {
        text-align: left;
        padding-left: 0.75em;
    }
    #reportTable th {
        background: #f8fafc;
        color: #333;
        font-weight: 700;
        border-bottom: 2px solid #dee2e6;
    }
    #reportTable tr {
        page-break-inside: avoid;
    }
    .generate-reports-btn {
        transition: background 0.2s, box-shadow 0.2s;
        padding: 0.35rem 1.2rem;
        font-size: 0.85rem;
    }
    .generate-reports-btn:hover, .generate-reports-btn:focus {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #fff;
        box-shadow: 0 4px 15px rgba(25,135,84,0.12);
    }

    /* Department Tabs Ultra Stylish Design */
    #departmentTabs {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 8px;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        flex-wrap: nowrap !important;
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    #departmentTabs::-webkit-scrollbar {
        display: none;
    }
    
    #departmentTabs .nav-link {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        font-size: 0.68rem;
        padding: 8px 13px;
        margin: 0 2px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        backdrop-filter: blur(10px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }
    
    #departmentTabs .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    #departmentTabs .nav-link:hover::before {
        left: 100%;
    }
    
    #departmentTabs .nav-link:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2), 
                    inset 0 1px 0 rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    #departmentTabs .nav-link.active {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        color: #667eea;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
    }
    
    #departmentTabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }
    
    #departmentTabs .nav-link.active:hover {
        background: linear-gradient(135deg, #fff 0%, #f1f3f4 100%);
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.9);
        color: #5a67d8;
    }
    
    #departmentTabs .nav-link i {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-right: 6px;
        font-size: 0.9em;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
    }
    
    #departmentTabs .nav-link:hover i {
        transform: scale(1.2) rotate(5deg);
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }
    
    #departmentTabs .nav-link.active i {
        color: #667eea;
        transform: scale(1.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #departmentTabs .nav-link {
            padding: 10px 15px;
            font-size: 0.75rem;
            margin: 0 1px;
        }
        
        #departmentTabs .nav-link i {
            font-size: 1em;
            margin-right: 6px;
        }
    }
    
    @media (max-width: 576px) {
        #departmentTabs {
            padding: 6px;
            border-radius: 12px;
        }
        
        #departmentTabs .nav-link {
            padding: 8px 12px;
            font-size: 0.7rem;
            border-radius: 8px;
        }
        
        #departmentTabs .nav-link i {
            margin-right: 4px;
        }
    }

    /* Report Generation Modal Styles */
    #reportGenerationModal .modal-body {
        padding: 1.5rem;
    }
    
    #reportGenerationModal .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
        border-radius: 8px;
    }
    
    #reportGenerationModal .btn-success {
        background-color: #198754;
        border-color: #198754;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    #reportGenerationModal .btn-success:hover {
        background-color: #157347;
        border-color: #157347;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25,135,84,0.15);
    }


</style>

<div class="container-fluid py-4">
<div class="row page-full-width">
    <div class="col-12">
        <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Department Filter Tabs (Teaching only) -->
                    <div class="mb-4">
                       
                        <div class="nav nav-pills d-flex w-100" id="departmentTabs" role="tablist" style="gap: 0;">
                            @php
                                $knownDepartments = ['BSIT','BSBA','BSHM','EDUC','GEC'];
                                // Always show all five tabs regardless of existing data
                                $departments = $knownDepartments;
                            @endphp
                            @foreach($departments as $index => $dept)
                                <button class="nav-link flex-fill text-center {{ $index === 0 ? 'active' : '' }}" data-department="{{ $dept }}" type="button" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" style="border-radius: 0; {{ $index === 0 ? 'border-top-left-radius: 0.375rem;' : '' }} {{ $index === count($departments) - 1 ? 'border-top-right-radius: 0.375rem;' : '' }}">
                                    <i class="fas fa-building me-2"></i>{{ $dept }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Teaching Staff Section -->
                    <div class="mb-4">
                        @php
                            $hasEvaluations = false;
                            foreach ($staffRatings as $staff) {
                                if ($staff->total_evaluations > 0) {
                                    $hasEvaluations = true;
                                    break;
                                }
                            }
                        @endphp
                        <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 gap-md-3 mb-3">
                            <div class="search-box mb-0 flex-grow-1" style="min-width:220px;">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchInput" onkeyup="searchStaff()" 
                                       placeholder="Search teaching staff by name, department, or email..." 
                                       class="form-control" style="height: 38px;">
                            </div>
                            
                            @if($hasEvaluations)
                            <button id="generateReportsBtn" class="btn btn-success fw-bold rounded-pill generate-reports-btn d-flex align-items-center justify-content-center gap-2 px-3" style="height: 38px; font-size:0.82rem; white-space: nowrap;">
                                <i class="fas fa-file-alt"></i> <span>Generate Reports</span>
                            </button>
                            @endif

                            <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-pill refresh-btn-enhanced px-3"
                                    style="height: 38px; font-weight:600; font-size:0.82rem; white-space: nowrap;" onclick="location.reload();">
                                <i class="fas fa-sync-alt"></i> <span>Refresh</span>
                            </button>
                        </div>
                            
                            <div class="table-responsive">
                        <table class="table table-hover" id="staffTable">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-center">Photo</th>
                                    <th>Staff Details</th>
                                    <th class="text-center">Rating</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Statistics</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $teachingStaff = $staffRatings->where('staff_type', 'teaching'); @endphp
                                @forelse($teachingStaff as $staff)
                                    @php 
                                        $ratingInfo = getRatingStatus($staff->average_rating);
                                        $starRating = round($staff->average_rating);
                                    @endphp
                                                                         @php $rowDept = in_array($staff->department, ['BSED','BEED']) ? 'EDUC' : $staff->department; @endphp
                                            <tr class="rating-card" data-department="{{ $rowDept }}">
                                        <td class="text-center align-middle">
                                            @if(!empty($staff->image_path) && file_exists(public_path($staff->image_path)))
                                                <img src="{{ asset($staff->image_path) }}" 
                                                     alt="{{ $staff->full_name }}" 
                                                     class="staff-image">
                                            @else
                                                <div class="default-avatar">
                                                    {{ strtoupper(substr($staff->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td class="align-middle">
                                            <div class="staff-info">
                                                <span class="staff-name text-primary">{{ $staff->full_name }}</span>
                                                <br>
                                                <span class="staff-meta">
                                                    <i class="fas fa-id-badge"></i>{{ $staff->staff_id }}
                                                </span>
                                                <br>
                                                <span class="staff-meta">
                                                    <i class="fas fa-envelope"></i>{{ $staff->email }}
                                                </span>
                                                <br>
                                                <span class="badge bg-secondary staff-badge">{{ $rowDept }}</span>
                                                <span class="badge bg-info staff-badge">{{ $staff->staff_type }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            <div class="rating-number" style="color: {{ $ratingInfo['color'] }}">
                                                {{ round($staff->average_rating, 2) }}/5
                                            </div>
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $starRating ? '' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            <span class="rating-badge" 
                                                  style="background-color: {{ $ratingInfo['bg'] }}; color: {{ $ratingInfo['color'] }}">
                                                {{ $ratingInfo['status'] }}
                                            </span>
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            <div class="stats-item">
                                                <div class="stats-number">{{ $staff->total_evaluations }}</div>
                                                <div class="stats-label">Reviews</div>
                                            </div>
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            <button class="btn btn-outline-primary action-btn" 
                                                    onclick="viewComments('{{ $staff->id }}', '{{ addslashes($staff->full_name) }}')"
                                                    {{ $staff->total_comments == 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-comments me-1"></i>
                                                View Comments
                                            </button>
                                            <button class="btn btn-outline-success action-btn ms-1" 
                                                    onclick="viewStaffProfile({{ $staff->id }})">
                                                <i class="fas fa-user-circle me-1"></i>
                                                View Profile & Ratings
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-chalkboard-teacher fa-2x mb-3"></i>
                                                <h5>No Teaching Staff Ratings Available</h5>
                                                <p>No evaluations have been submitted for teaching staff yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                        </div>
                    </div>


                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert" style="position: fixed; top: 30px; right: 30px; z-index: 1055; min-width: 300px;">
                  <i class="fas fa-check-circle me-2"></i>
                  <span id="successAlertMsg">Action completed successfully!</span>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>




                <!-- Report Generation Modal -->
                <div class="modal fade" id="reportGenerationModal" tabindex="-1" aria-labelledby="reportGenerationModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="reportGenerationModalLabel">
                            <i class="fas fa-file-alt me-2"></i>Generate Department Report
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="alert alert-info mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            This will generate and print the report for the currently selected department:
                            <strong id="reportDeptName">All Departments</strong>.
                      </div>
                        <p class="mb-0 text-muted">Only staff with evaluations will be included. Current search and department filters will be applied.</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="generateReportBtn">
                            <i class="fas fa-print me-2"></i>Print Now
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments Modal -->
<div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header table-header text-white">
                <h5 class="modal-title" id="commentsModalLabel">
                    <i class="fas fa-comments me-2"></i>Staff Comments
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="commentsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

<!-- Staff Profile & Ratings Modal -->
<div class="modal fade" id="staffProfileModal" tabindex="-1" aria-labelledby="staffProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header table-header text-white">
                <h5 class="modal-title" id="staffProfileModalLabel">
                    <i class="fas fa-user-circle me-2"></i>Staff Profile & Ratings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="staffProfileContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function searchStaff() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#staffTable tbody tr');
        const activeTab = document.querySelector('#departmentTabs .nav-link.active');
        const activeDepartment = activeTab ? activeTab.getAttribute('data-department') : null;

        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) {
                row.style.display = 'none';
                return;
            }
            const staffDetails = row.cells[1].textContent.toLowerCase();
            const matchesSearch = staffDetails.includes(input);
            const matchesDept = !activeDepartment || row.getAttribute('data-department') === activeDepartment;
            row.style.display = matchesSearch && matchesDept ? '' : 'none';
        });

        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
        const emptyRow = document.querySelector('#staffTable tbody tr td[colspan]')?.parentElement;
        if (emptyRow) {
            emptyRow.style.display = visibleRows.length === 0 ? '' : 'none';
        }
    }

    // Tab click handler to filter by department
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('#departmentTabs .nav-link');
        if (tabs.length) {
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    searchStaff();
                });
            });
            // Trigger initial filter to first tab
            searchStaff();
        }
    });

    function viewComments(staffId, staffName) {
        console.log('viewComments called with staffId:', staffId, 'staffName:', staffName);
        
        const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
        const modalTitle = document.getElementById('commentsModalLabel');
        const commentsContent = document.getElementById('commentsContent');
        
        modalTitle.innerHTML = `<i class="fas fa-comments me-2"></i>Comments for ${staffName}`;
        commentsContent.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Fetch comments via AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            commentsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    CSRF token not found. Please refresh the page and try again.
                </div>
            `;
            return;
        }

        console.log('Making fetch request to:', '{{ route("staff.comments.general") }}', 'with staffId:', staffId);
        
        fetch('{{ route("staff.comments.general") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: `staff_id=${staffId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                displayComments(data.comments);
            } else {
                commentsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading comments: ${data.message || 'Unknown error occurred'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching comments:', error);
            commentsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading comments. Please try again. ${error.message ? `(${error.message})` : ''}
                </div>
            `;
        });
    }

    function displayComments(comments) {
        const commentsContent = document.getElementById('commentsContent');
        
        if (comments.length === 0) {
            commentsContent.innerHTML = `
                <div class="no-comments">
                    <i class="fas fa-comment-slash fa-3x mb-3 text-muted"></i>
                    <h5>No Comments Available</h5>
                    <p>This staff member has no comments yet.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        comments.forEach(comment => {
            html += `
                <div class="comment-item">
                    <div class="comment-text mb-2">${comment.comments}</div>
                    <div class="comment-date">
                        <i class="fas fa-clock me-1"></i>
                        ${new Date(comment.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </div>
                </div>
            `;
        });
        
        commentsContent.innerHTML = html;
    }

    function viewStaffProfile(staffId) {
        const modal = new bootstrap.Modal(document.getElementById('staffProfileModal'));
        const modalTitle = document.getElementById('staffProfileModalLabel');
        const profileContent = document.getElementById('staffProfileContent');
        modalTitle.innerHTML = `<i class='fas fa-user-circle me-2'></i>Staff Profile & Ratings`;
        profileContent.innerHTML = `<div class='text-center'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div>`;
        modal.show();
        // Fetch profile and ratings via AJAX
        fetch(`{{ url('/staff/profile-ratings') }}/${staffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayStaffProfileRatings(data);
                } else {
                    profileContent.innerHTML = `<div class='alert alert-danger'><i class='fas fa-exclamation-triangle me-2'></i>Error loading profile: ${data.message}</div>`;
                }
            })
            .catch(error => {
                profileContent.innerHTML = `<div class='alert alert-danger'><i class='fas fa-exclamation-triangle me-2'></i>Error loading profile. Please try again.</div>`;
            });
    }

    function displayStaffProfileRatings(data) {
        const staff = data.staff;
        const categories = data.categories;
        const averages = data.averages;
        let html = `<div class='row mb-4'>
            <div class='col-md-3 text-center'>`;
        if (staff.image_path) {
            html += `<img src='/${staff.image_path}' class='staff-image mb-2' style='width:80px;height:80px;'>`;
        } else {
            html += `<div class='default-avatar mb-2' style='width:80px;height:80px;font-size:2rem;'>${staff.full_name.charAt(0).toUpperCase()}</div>`;
        }
        html += `<div><strong>${staff.full_name}</strong><br><span class='badge bg-secondary'>${staff.department}</span><br><span class='badge bg-info'>${staff.staff_type}</span><br><small class='text-muted'>${staff.email}</small></div></div>`;
        html += `<div class='col-md-9'><h6 class='fw-bold mb-3'>Category Ratings</h6>`;
        html += `<div class='range-legend mb-2'>
    <div class='legend-item'><span class='color range-bar-green' style='background-color: #28a745 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Outstanding (4.51-5.00)</span></div>
    <div class='legend-item'><span class='color range-bar-blue' style='background-color: #17a2b8 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Very Satisfactory (3.51-4.50)</span></div>
    <div class='legend-item'><span class='color range-bar-yellow' style='background-color: #ffc107 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Satisfactory (2.51-3.50)</span></div>
    <div class='legend-item'><span class='color range-bar-orange' style='background-color: #fd7e14 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Unsatisfactory (1.51-2.50)</span></div>
    <div class='legend-item'><span class='color range-bar-red' style='background-color: #dc3545 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Poor (Below 1.51)</span></div>
</div>`;
        if (categories.length === 0) {
            html += `<div class='text-muted'>No categories found for this staff type.</div>`;
        } else {
            html += `<div class='list-group'>`;
            categories.forEach(category => {
                const avg = averages[category.title] !== undefined ? averages[category.title] : 0;
                let colorClass = 'range-bar-red';
                if (avg >= 4.51) colorClass = 'range-bar-green';
                else if (avg >= 3.51) colorClass = 'range-bar-blue';
                else if (avg >= 2.51) colorClass = 'range-bar-yellow';
                else if (avg >= 1.51) colorClass = 'range-bar-orange';
                
                const percent = ((avg-1)/4)*100; // 1-5 scale
                let backgroundColor = '#dc3545'; // default red
                if (avg >= 4.51) backgroundColor = '#28a745'; // green
                else if (avg >= 3.51) backgroundColor = '#007bff'; // blue
                else if (avg >= 2.51) backgroundColor = '#ffc107'; // yellow
                else if (avg >= 1.51) backgroundColor = '#fd7e14'; // orange
                html += `<div class='mb-3'><div class='d-flex justify-content-between'><span>${category.title}</span><span class='fw-bold'>${avg.toFixed(2)}/5</span></div><div class='range-bar' style='width: 100%; height: 8px; border-radius: 6px; background: #e9ecef; margin-top: 6px; margin-bottom: 2px; position: relative;'><div class='range-bar-fill ${colorClass}' style='width:${percent}%; height: 100%; border-radius: 6px; position: absolute; left: 0; top: 0; background-color: ${backgroundColor} !important;'></div></div></div>`;
            });
            html += `</div>`;
        }
        html += `</div></div>`;
        document.getElementById('staffProfileContent').innerHTML = html;
    }

    // Add printStaffReport function
    // Replace direct printStaffReport call with SweetAlert confirmation
    function showPrintConfirmModal(staffId) {
        Swal.fire({
            title: 'Confirm Print',
            text: 'Are you sure you want to print the report for this staff member?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-print me-1"></i>Yes, Print',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary',
                actions: 'swal-actions-spaced'
            },
            buttonsStyling: false,
            didOpen: () => {
                // Add custom spacing styles
                const style = document.createElement('style');
                style.textContent = `
                    .swal-actions-spaced {
                        gap: 25px !important;
                        justify-content: center !important;
                    }
                    .swal2-actions .btn {
                        margin: 0 12px !important;
                        min-width: 120px !important;
                        padding: 10px 20px !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                printStaffReport(staffId);
            }
        });
    }

    function printStaffReport(staffId) {
        console.log('printStaffReport called for staffId:', staffId);
        // Fetch staff profile and ratings via AJAX (reuse profileRatingsAjax endpoint)
        fetch(`{{ url('/staff/profile-ratings') }}/${staffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Build print-friendly HTML
                    let staff = data.staff;
                    let categories = data.categories;
                    let averages = data.averages;
                    let html = `<div style='padding:32px;font-family:sans-serif;'>`;
                    html += `<div style='text-align:center;margin-bottom:2em;'>`;
                    html += `<img src='/images/logo.png' style='width:100px;margin-bottom:1em;'>`;
                    html += `<h2 style='margin-bottom:0.2em;'>Staff Evaluation Report</h2>`;
                    html += `<div style='color:#888;font-size:1.1em;'>${new Date().toLocaleString()}</div>`;
                    html += `</div>`;
                    html += `<div style='display:flex;gap:32px;align-items:center;margin-bottom:2em;'>`;
                    if (staff.image_path) {
                        html += `<img src='/${staff.image_path}' style='width:80px;height:80px;border-radius:50%;border:2px solid #eee;'>`;
                    } else {
                        html += `<div style='width:80px;height:80px;border-radius:50%;background:#764ba2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:2.5em;font-weight:bold;'>${staff.full_name.charAt(0).toUpperCase()}</div>`;
                    }
                    html += `<div><strong style='font-size:1.3em;'>${staff.full_name}</strong><br><span style='color:#555;'>${staff.department}</span><br><span style='color:#007bff;'>${staff.staff_type}</span><br><span style='color:#888;'>${staff.email}</span></div>`;
                    html += `</div>`;
                    html += `<h4 style='margin-bottom:1em;'>Category Ratings</h4>`;
                    if (categories.length === 0) {
                        html += `<div style='color:#888;'>No categories found for this staff type.</div>`;
                    } else {
                        html += `<table style='width:100%;border-collapse:collapse;margin-bottom:2em;'><thead><tr><th style='text-align:left;padding:8px 12px;background:#f8fafc;'>Category</th><th style='text-align:right;padding:8px 12px;background:#f8fafc;'>Average Rating</th></tr></thead><tbody>`;
                        categories.forEach(category => {
                            const avg = averages[category.title] !== undefined ? averages[category.title] : 0;
                            html += `<tr><td style='padding:8px 12px;border-bottom:1px solid #eee;'>${category.title}</td><td style='padding:8px 12px;text-align:right;border-bottom:1px solid #eee;'><strong>${avg.toFixed(2)}/5</strong></td></tr>`;
                        });
                        html += `</tbody></table>`;
                    }
                    html += `</div>`;
                    
                    // Open print window (back to original approach but with success alert)
                    const printWindow = window.open('', '', 'width=900,height=700');
                    printWindow.document.write('<html><head><title>Staff Evaluation Report</title>');
                    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(html);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.focus();
                    
                    // Show success alert immediately after print dialog opens
                    setTimeout(() => { 
                        printWindow.print(); 
                        printWindow.close(); 
                    }, 500);
                    
                    // Show success alert after a short delay
                    setTimeout(() => {
                        console.log('Showing success alert for staff report');
                        Swal.fire({
                            title: 'Report Printed Successfully!',
                            text: 'The staff evaluation report has been sent to the printer.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }, 1000);
                } else {
                    alert('Error loading staff report: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error loading staff report. Please try again.');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const generateReportsBtn = document.getElementById('generateReportsBtn');
        if (generateReportsBtn) {
            generateReportsBtn.addEventListener('click', function() {
                const activeTab = document.querySelector('#departmentTabs .nav-link.active');
                const deptName = activeTab ? activeTab.getAttribute('data-department') : 'All Departments';
                        Swal.fire({
                    title: 'Generate Department Report',
                    html: `This will generate and print the report for <b>${deptName}</b>.` +
                          `<br/><small class="text-muted">Only evaluated staff currently visible will be included.</small>`,
                    icon: 'question',
                            showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print me-1"></i>Print Now',
                    cancelButtonText: 'Cancel',
                            reverseButtons: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                        generateDepartmentReport();
                    }
                });
            });
        }

        // Handle report generation button click
        const generateReportBtn = document.getElementById('generateReportBtn');
        if (generateReportBtn) {
            generateReportBtn.addEventListener('click', function() {
                generateDepartmentReport();
            });
        }
    });

    function generateDepartmentReport() {
        const activeTab = document.querySelector('#departmentTabs .nav-link.active');
        const department = activeTab ? activeTab.getAttribute('data-department') : 'all';
        
                                // Show loading alert
                                Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait while we prepare your report.',
                                    icon: 'info',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                
        // Get current staff data from the table
        const staffData = getCurrentStaffData(department);
        
        if (staffData.length === 0) {
                                        Swal.fire({
                title: 'No Data Available',
                text: 'No staff members found for the selected department.',
                icon: 'warning',
                                            confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        // Generate and print the report
        setTimeout(() => {
            generateAndPrintReport(staffData, department);
            Swal.close();
        }, 1000);
    }

    function getCurrentStaffData(department) {
        const rows = document.querySelectorAll('#staffTable tbody tr');
        const staffData = [];
        
        rows.forEach(row => {
            // Skip placeholder empty rows
            if (row.querySelector('td[colspan]')) return;
            // Respect current filter visibility (search + department tab)
            if (row.style.display === 'none') return;
            
            const rowDepartment = row.getAttribute('data-department');
            if (department === 'all' || rowDepartment === department) {
                const nameEl = row.querySelector('.staff-info .staff-name');
                const ratingEl = row.querySelector('td:nth-child(3) .rating-number');
                const reviewsEl = row.querySelector('td:nth-child(5) .stats-number');
                if (!nameEl || !ratingEl || !reviewsEl) return;
                
                const name = nameEl.textContent.trim();
                const rating = parseFloat((ratingEl.textContent || '0').split('/')[0]);
                const reviews = parseInt(reviewsEl.textContent || '0', 10);
                
                // Include only evaluated staff (with at least 1 review)
                if (reviews > 0 && !isNaN(rating)) {
                    staffData.push({ name: name, rating: rating });
                }
            }
        });
        
        return staffData.sort((a, b) => b.rating - a.rating);
    }

    function generateAndPrintReport(staffData, department) {
        
        const departmentTitle = department === 'all' ? 'All Departments' : (department === 'EDUC' ? 'CoEd' : department);
        const currentDate = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        let recipientName = '';
        let programHead = '';
        
        if (department === 'BSIT') {
            recipientName = 'DR. DINO L. ILUSTRISIMO';
            programHead = 'Program Head - BSIT';
        } else if (department === 'EDUC') {
            recipientName = 'DR. PRECILLA CANOY';
            programHead = 'Dean - College of Education';
        } else if (department === 'BSBA') {
            recipientName = 'DR. ISRAEL N. ABARRATIGUE';
            programHead = 'Dean - BSBA-FM and BSHM';
        }else if (department === 'BSHM') {
            recipientName = 'DR. ISRAEL N. ABARRATIGUE';
             programHead = 'Dean - BSBA-FM and BSHM';
        }else {
            recipientName = 'DR. ISRAEL N. ABARRATIGUE';
            programHead = 'GEC Facilitator';
        }

        let html = `
            <div style="padding: 0 1in; font-family: Arial, sans-serif; font-size:10pt; max-width: 900px; margin: 0 auto; line-height: 1.15;">
                <div class='header-section' style='text-align:center; margin-bottom:0.2em; padding-bottom:0;'>
                    <div style='display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:2.15em;'>
                        <img src='/images/cgs.jpg' alt='Left Logo' style='width:80px;height:80px;flex-shrink:0;margin-right:5px;' onerror='this.style.display="none"'>
                        <div style='text-align:center; flex:0 0 auto;'>   
                            <strong style='font-size:12pt; font-family: "Arial Black", Gadget, sans-serif;'>MADRIDEJOS COMMUNITY COLLEGE</strong><br>                                   
                            <strong style='font-size:11.5pt; font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;'>CENTER FOR GUIDANCE SERVICES</strong><br>
                            <span style='font-size:8pt;'>Crossing Bunakan, Madridejos, Cebu</span><br>
                            <span style='font-size:8pt; color: blue; text-decoration: none; font-family: "Century Gothic", sans-serif; font-weight: 300;'>
                             <i class='fas fa-envelope'></i> mcc.cgsofficial@gmail.com<br>
                             <i class='fab fa-facebook'></i> fb.com/MCCCenterforGuidanceService 
                            </span><br><br>
                             <strong style='font-size:10pt; font-family: "Century Gothic", sans-serif;'>MCC Department's Performance Evaluation Results</strong><br>
                           <span style='font-size:10pt; font-family: "Century Gothic", sans-serif; font-weight: normal;'>S.Y {{ $currentAcademicYear?->year }} - {{ $currentAcademicYear?->semester == 1 ? 'First' : ($currentAcademicYear?->semester == 2 ? 'Second' : $currentAcademicYear?->semester) }} Sem </span>
                        </div>
                        <img src='/images/logo.png' alt='Right Logo' style='width:100px;height:100px;flex-shrink:0;margin-left:5px;' onerror='this.style.display="none"'>
                    </div>
                </div>
                
                <div style="text-align: left; margin-bottom: 0.8em; font-size: 10pt; line-height: 1.2;">
                    <p style="margin-bottom: 0.15em;">To:<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;${recipientName}</strong></p>
                    <p style="margin-bottom: 0.3em; margin-left: 2em;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;${programHead}</p>
                    
                     <p style="margin-bottom: 0.3em;">From:&nbsp;&nbsp;&nbsp;&nbsp;Center for Guidance Services</p>
                    
                   <p style="margin-bottom: 1em;">Subject:&nbsp;Endorsement of Performance Evaluation   of ${departmentTitle} Instructors</p>
                    
                    <p style="margin-bottom: 1em;">Greetings of Peace!</p>
                    
                    <p style="margin-bottom: 0.7em;">I am writing to formally endorse the Results of Performance Evaluation of ${departmentTitle} instructors for Academic Year {{ $currentAcademicYear?->year }} - {{ $currentAcademicYear?->semester == 1 ? 'First' : ($currentAcademicYear?->semester == 2 ? 'Second' : $currentAcademicYear?->semester) }} Semester.</p>
                    
                    <p style="margin-bottom: 0.7em;">Please be advised that the following instructors have been evaluated by all MCC Students.</p>
                    
                    <p style="margin-bottom: 0.6em;">Enclosed with this letter, you will find a detailed report highlighting the evaluation results for each instructor.</p>
                </div>
                
                <div style="margin-bottom: 0.8em;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 0.4em;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: left; font-weight: bold; font-size: 10pt;">NAME OF INSTRUCTORS</th>
                                <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; font-size: 10pt;">AVERAGE SCORES</th>
                                <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; font-size: 10pt;">ADJECTIVAL DESCRIPTIVE</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
        `;

        staffData.forEach((staff, index) => {
            const rowColor = index % 2 === 0 ? '#ffffff' : '#f8f9fa';
            const adjective = adjectivalFromLegend(staff.rating);
            html += `
                <tr style="background-color: ${rowColor};">
                    <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: left; font-size: 9.5pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${staff.name}</td>
                    <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; color: #232527; font-size: 9.5pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${staff.rating.toFixed(2)}</td>
                    <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; color: #080908; font-size: 9.5pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${adjective}</td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>

                <!-- Signature Section -->
                <div style='margin-top:4em;margin-bottom:0em;text-align:left; font-size:10pt; line-height:1.3;'>
                    <div style='margin-bottom:1em;'>
                        Prepared by:
                    </div>
                    <div style='margin-bottom:0.1em;'>
                        <strong>DHINA B. DALISAY</strong>
                    </div>
                    <div style='margin-bottom:2em;'>
                        Guidance Advocate
                    </div>

                    <div style='text-align:left;'>
                        <div style='margin-bottom:1em;'>
                            Reviewed and Noted by:
                        </div>
                        <div style='margin-bottom:0.1em;'>
                            <strong>DR. LIZA D. GARCIA, RGC</strong>
                        </div>
                        <div>
                            Guidance Counselor
                        </div>
                    </div>

                    <div style='margin-top: 1em; display: flex; justify-content: flex-end; padding-right: 1em;'>
                        <div style='text-align:left;'>
                            <div style='display: flex; align-items: flex-start;'>
                                <div style='white-space: nowrap;'>Received by: &nbsp;</div>
                                <div style='display: flex; flex-direction: column;'>
                                    <span>_________________________</span>
                                    <strong style='margin-top: 0.1em;'>${recipientName}</strong>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Use in-page printing to avoid browser headers/footers
        const existingArea = document.getElementById('customPrintArea');
        if (existingArea) existingArea.remove();
        
        const printArea = document.createElement('div');
        printArea.id = 'customPrintArea';
        printArea.innerHTML = html;
        document.body.appendChild(printArea);

        // Clear document title to minimize header text
        const originalTitle = document.title;
        document.title = '';

        const onAfterPrint = () => {
            window.removeEventListener('afterprint', onAfterPrint);
            const pa = document.getElementById('customPrintArea');
            if (pa) pa.remove();
            document.title = originalTitle;
            
            Swal.fire({
                title: 'Report Generated Successfully!',
                text: 'Your department evaluation report has been generated and sent to the printer.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        };
        
        window.addEventListener('afterprint', onAfterPrint);
        setTimeout(() => { window.print(); }, 100);
    }

    function adjectivalFromLegend(rating) {
         if (rating >= 4.51) return 'Outstanding';
        if (rating >= 3.51) return 'Very Satisfactory';
        if (rating >= 2.51) return 'Satisfactory';
        if (rating >= 1.51) return 'Unsatisfactory';
        return 'Poor';
    }



</script>