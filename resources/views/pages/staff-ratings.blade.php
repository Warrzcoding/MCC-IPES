<meta name="csrf-token" content="{{ csrf_token() }}">

@php
// Function to get rating status and color
function getRatingStatus($rating) {
    if ($rating >= 4) return ['status' => 'Excellent', 'color' => '#28a745', 'bg' => '#d4edda'];
    if ($rating >= 3) return ['status' => 'Good', 'color' => '#17a2b8', 'bg' => '#d1ecf1'];
    if ($rating >= 2) return ['status' => 'Average', 'color' => '#ffc107', 'bg' => '#fff3cd'];
    if ($rating >= 1) return ['status' => 'Below Average', 'color' => '#fd7e14', 'bg' => '#ffeaa7'];
    return ['status' => 'Poor', 'color' => '#dc3545', 'bg' => '#f8d7da'];
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
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .default-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #fff;
        color: white;
        font-weight: bold;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .rating-badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .rating-stars {
        color: #ffc107;
        font-size: 1.2em;
    }
    .rating-number {
        font-size: 1.5em;
        font-weight: bold;
    }
    .stats-item {
        text-align: center;
        padding: 0.5rem;
    }
    .stats-number {
        font-size: 1.2em;
        font-weight: bold;
        color: #007bff;
    }
    .stats-label {
        font-size: 0.8em;
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
        padding-left: 50px;
        border-radius: 25px;
        border: 2px solid #e9ecef;
    }
    #searchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 15px;
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
    .range-bar-blue { background: #007bff; }
    .range-bar-yellow { background: #ffc107; }
    .range-bar-orange { background: #fd7e14; }
    .range-bar-red { background: #dc3545; }
    
    /* Legend color indicators */
    .range-legend span.color.range-bar-green { background: #28a745; }
    .range-legend span.color.range-bar-blue { background: #007bff; }
    .range-legend span.color.range-bar-yellow { background: #ffc107; }
    .range-legend span.color.range-bar-orange { background: #fd7e14; }
    .range-legend span.color.range-bar-red { background: #dc3545; }
    
    /* Ensure range bar fills work in modal context */
    #staffProfileModal .range-bar-fill.range-bar-green { background: #28a745 !important; }
    #staffProfileModal .range-bar-fill.range-bar-blue { background: #007bff !important; }
    #staffProfileModal .range-bar-fill.range-bar-yellow { background: #ffc107 !important; }
    #staffProfileModal .range-bar-fill.range-bar-orange { background: #fd7e14 !important; }
    #staffProfileModal .range-bar-fill.range-bar-red { background: #dc3545 !important; }
    
    /* Additional specificity for modal content */
    .modal-body .range-bar-fill.range-bar-green { background-color: #28a745 !important; }
    .modal-body .range-bar-fill.range-bar-blue { background-color: #007bff !important; }
    .modal-body .range-bar-fill.range-bar-yellow { background-color: #ffc107 !important; }
    .modal-body .range-bar-fill.range-bar-orange { background-color: #fd7e14 !important; }
    .modal-body .range-bar-fill.range-bar-red { background-color: #dc3545 !important; }
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
        .modal-footer, .d-print-none, #reportPreviewModal .modal-header, #reportPreviewModal .modal, .report-title, .report-date { display: none !important; }
        #reportPreviewModal .modal-content, #reportPreviewModal .modal-body { box-shadow: none !important; border: none !important; }
        #reportPreviewContent { padding: 0 !important; }
        .report-logo { display: block !important; margin-bottom: 1.5em !important; width: 110px !important; }
        
        /* Hide everything except print area */
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
    }
    #reportTable th.name-col, #reportTable td.name-col {
        text-align: left;
        padding-left: 1.2em;
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
    .save-all-btn {
        transition: background 0.2s, box-shadow 0.2s;
    }
    .save-all-btn:hover, .save-all-btn:focus {
        background-color: #0a58ca !important;
        border-color: #0a58ca !important;
        color: #fff;
        box-shadow: 0 4px 15px rgba(10,88,202,0.12);
    }

    /* Staff Type Tabs Styling */
    .nav-tabs {
        border-bottom: 3px solid #e9ecef;
        margin-bottom: 0;
        padding: 0 4px;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs .nav-item {
        margin-bottom: -3px;
    }

    .nav-tabs .nav-link {
        border: 2px solid transparent;
        border-radius: 8px 8px 0 0;
        padding: 12px 20px;
        font-weight: 600;
        color: #6c757d;
        background: #ffffff;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-right: 4px;
    }

    .nav-tabs .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .nav-tabs .nav-link:hover::before {
        left: 100%;
    }

    .nav-tabs .nav-link:hover {
        color: #007bff;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.2);
    }

    .nav-tabs .nav-link.active {
        color: #ffffff;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-color: #007bff;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        transform: translateY(-1px);
    }

    .nav-tabs .nav-link.active:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        box-shadow: 0 6px 20px rgba(0,123,255,0.4);
        transform: translateY(-3px);
    }

    .nav-tabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1em;
        transition: transform 0.3s ease;
    }

    .nav-tabs .nav-link:hover i {
        transform: scale(1.1) rotate(5deg);
    }

    .nav-tabs .nav-link.active i {
        transform: scale(1.05);
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    /* Tab content styling */
    .tab-content {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }

    .tab-pane {
        padding: 20px;
        min-height: 400px;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease-in-out;
    }

    .tab-pane.active {
        opacity: 1;
        transform: translateY(0);
    }

    /* Active tab indicator */
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #007bff, #0056b3, #007bff);
        border-radius: 2px;
    }

    /* Staff type badge styling */
    .staff-type-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        margin-left: 8px;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active .staff-type-badge {
        background: rgba(255,255,255,0.3);
        color: #ffffff;
    }

    /* Enhanced focus states for accessibility */
    .nav-tabs .nav-link:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }

    /* Responsive tab styling */
    @media (max-width: 768px) {
        .nav-tabs {
            padding: 4px 4px 0 4px;
        }
        
        .nav-tabs .nav-link {
            padding: 8px 12px;
            font-size: 0.85em;
            margin-right: 2px;
        }
        
        .nav-tabs .nav-link i {
            margin-right: 4px;
            font-size: 0.9em;
        }
        
        .staff-type-badge {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .nav-tabs .nav-link {
            padding: 6px 10px;
            font-size: 0.8em;
        }
        
        .nav-tabs .nav-link span:first-of-type {
            display: none;
        }
    }
</style>

<div class="container-fluid py-4">
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Staff Type Tabs -->
                    <div class="mb-4">
                        <ul class="nav nav-tabs" id="staffTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="teaching-tab" data-bs-toggle="tab" data-bs-target="#teaching" type="button" role="tab" aria-controls="teaching" aria-selected="true" data-staff-type="teaching">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Teaching</span>
                                    <span class="staff-type-badge">Instructor</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="non-teaching-tab" data-bs-toggle="tab" data-bs-target="#non-teaching" type="button" role="tab" aria-controls="non-teaching" aria-selected="false" data-staff-type="non-teaching">
                                    <i class="fas fa-users-cog"></i>
                                    <span>Non-Teaching</span>
                                    <span class="staff-type-badge">Staff</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="staffTypeTabContent">
                        <!-- Teaching Staff Tab -->
                        <div class="tab-pane fade show active" id="teaching" role="tabpanel" aria-labelledby="teaching-tab">
                            @php 
                                $teachingStaffCount = $staffRatings->where('staff_type', 'teaching')->count();
                                $nonTeachingStaffCount = $staffRatings->where('staff_type', 'non-teaching')->count();
                            @endphp
                           
                            <!-- Search controls for Teaching Staff -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                <div class="search-box mb-0" style="flex:1 1 250px; min-width:220px;">
                                  
                                    <input type="text" id="searchInputTeaching" onkeyup="searchStaff('teaching')" 
                                           placeholder="Search teaching staff by name, department, or email..." 
                                           class="form-control">
                                </div>
                                <button type="button" class="btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced"
                                        style="height:40px;font-weight:bold;font-size:1rem;" onclick="location.reload();">
                                    <i class="fas fa-sync-alt"></i> <span>Refresh</span>
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                        <table class="table table-hover" id="teachingStaffTable">
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
                                    <tr class="rating-card">
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
                                            <div>
                                                <strong class="text-primary">{{ $staff->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-id-badge me-1"></i>{{ $staff->staff_id }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>{{ $staff->email }}
                                                </small>
                                                <br>
                                                <span class="badge bg-secondary">{{ $staff->department }}</span>
                                                <span class="badge bg-info">{{ $staff->staff_type }}</span>
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
                                            <button class="btn btn-success action-btn ms-1" 
                                                    onclick="showPrintConfirmModal({{ $staff->id }})">
                                                <i class="fas fa-print me-1"></i>
                                                Print Report
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
                        
                        <!-- Non-Teaching Staff Tab -->
                        <div class="tab-pane fade" id="non-teaching" role="tabpanel" aria-labelledby="non-teaching-tab">
                            <!-- Debug info (remove in production) -->
                           
                            <!-- Search controls for Non-Teaching Staff -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                <div class="search-box mb-0" style="flex:1 1 250px; min-width:220px;">
                              
                                    <input type="text" id="searchInputNonTeaching" onkeyup="searchStaff('non-teaching')" 
                                           placeholder="Search non-teaching staff by name, department, or email..." 
                                           class="form-control">
                                </div>
                                <button type="button" class="btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced"
                                        style="height:40px;font-weight:bold;font-size:1rem;" onclick="location.reload();">
                                    <i class="fas fa-sync-alt"></i> <span>Refresh</span>
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover" id="nonTeachingStaffTable">
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
                                        @php $nonTeachingStaff = $staffRatings->where('staff_type', 'non-teaching'); @endphp
                                        @forelse($nonTeachingStaff as $staff)
                                            @php 
                                                $ratingInfo = getRatingStatus($staff->average_rating);
                                                $starRating = round($staff->average_rating);
                                            @endphp
                                            <tr class="rating-card">
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
                                                    <div>
                                                        <strong class="text-primary">{{ $staff->full_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-id-badge me-1"></i>{{ $staff->staff_id }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>{{ $staff->email }}
                                                        </small>
                                                        <br>
                                                        <span class="badge bg-secondary">{{ $staff->department }}</span>
                                                        <span class="badge bg-info">{{ $staff->staff_type }}</span>
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
                                                    <div class="d-flex justify-content-center gap-3">
                                                        <div class="stats-item">
                                                            <div class="stats-number">{{ $staff->total_evaluations }}</div>
                                                            <div class="stats-label">Evaluations</div>
                                                        </div>
                                                        <div class="stats-item">
                                                            <div class="stats-number">{{ $staff->total_comments }}</div>
                                                            <div class="stats-label">Comments</div>
                                                        </div>
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
                                                    <button class="btn btn-success action-btn ms-1" 
                                                            onclick="showPrintConfirmModal({{ $staff->id }})">
                                                        <i class="fas fa-print me-1"></i>
                                                        Print Report
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-users-cog fa-2x mb-3"></i>
                                                        <h5>No Non-Teaching Staff Ratings Available</h5>
                                                        <p>No evaluations have been submitted for non-teaching staff yet.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <!-- Save All Results button removed - functionality moved to sidebar dropdown -->

                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert" style="position: fixed; top: 30px; right: 30px; z-index: 1055; min-width: 300px;">
                  <i class="fas fa-check-circle me-2"></i>
                  <span id="successAlertMsg">Action completed successfully!</span>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>




                <!-- Save Confirmation Modal -->
                <div class="modal fade" id="saveConfirmModal" tabindex="-1" aria-labelledby="saveConfirmModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="saveConfirmModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Save & Clear</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to <strong>save all current evaluations and clear all entries</strong>? This action cannot be undone.
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmSaveAllBtn">Yes, Save & Clear</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Save Success Modal -->
                <div class="modal fade" id="saveSuccessModal" tabindex="-1" aria-labelledby="saveSuccessModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="saveSuccessModalLabel"><i class="fas fa-check-circle me-2"></i>Success</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        All evaluations have been saved and entries cleared successfully!
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="closeSuccessModalBtn">OK</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Save Error Modal -->
                <div class="modal fade" id="saveErrorModal" tabindex="-1" aria-labelledby="saveErrorModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="saveErrorModalLabel"><i class="fas fa-times-circle me-2"></i>Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body" id="saveErrorModalBody">
                        <!-- Error message will be inserted here -->
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
    function searchStaff(staffType) {
        let inputId, tableId;
        
        if (staffType === 'teaching') {
            inputId = 'searchInputTeaching';
            tableId = 'teachingStaffTable';
        } else if (staffType === 'non-teaching') {
            inputId = 'searchInputNonTeaching';
            tableId = 'nonTeachingStaffTable';
        } else {
            // If no specific type, search in the currently active tab
            const activeTab = document.querySelector('#staffTypeTabs .nav-link.active');
            if (activeTab) {
                const activeStaffType = activeTab.getAttribute('data-staff-type');
                return searchStaff(activeStaffType);
            }
            return;
        }
        
        const input = document.getElementById(inputId).value.toLowerCase();
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        
        rows.forEach(row => {
            // If this is the empty state row, always show if no results
            if (row.querySelector('td[colspan]')) {
                row.style.display = 'none';
                return;
            }
            const staffDetails = row.cells[1].textContent.toLowerCase();
            const matchesSearch = staffDetails.includes(input);
            row.style.display = matchesSearch ? '' : 'none';
        });
        
        // Show empty state if all rows are hidden
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
        const emptyRow = document.querySelector(`#${tableId} tbody tr td[colspan]`)?.parentElement;
        if (emptyRow) {
            emptyRow.style.display = visibleRows.length === 0 ? '' : 'none';
        }
    }

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

        console.log('Making fetch request to:', '{{ route("staff.comments") }}', 'with staffId:', staffId);
        
        fetch('{{ route("staff.comments") }}', {
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
        fetch(`/staff/profile-ratings/${staffId}`)
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
    <div class='legend-item'><span class='color range-bar-green' style='background-color: #28a745 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Excellent (4-5)</span></div>
    <div class='legend-item'><span class='color range-bar-blue' style='background-color: #007bff !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Good (3-4)</span></div>
    <div class='legend-item'><span class='color range-bar-yellow' style='background-color: #ffc107 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Average (2-3)</span></div>
    <div class='legend-item'><span class='color range-bar-orange' style='background-color: #fd7e14 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Below Avg (1-2)</span></div>
    <div class='legend-item'><span class='color range-bar-red' style='background-color: #dc3545 !important; display: inline-block; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; outline: 1.5px solid #bbb;'></span><span class='range-legend-label'>Poor (&lt;1)</span></div>
</div>`;
        if (categories.length === 0) {
            html += `<div class='text-muted'>No categories found for this staff type.</div>`;
        } else {
            html += `<div class='list-group'>`;
            categories.forEach(category => {
                const avg = averages[category.title] !== undefined ? averages[category.title] : 0;
                let colorClass = 'range-bar-red';
                if (avg >= 4) colorClass = 'range-bar-green';
                else if (avg >= 3) colorClass = 'range-bar-blue';
                else if (avg >= 2) colorClass = 'range-bar-yellow';
                else if (avg >= 1) colorClass = 'range-bar-orange';
                const percent = ((avg-1)/4)*100; // 1-5 scale
                let backgroundColor = '#dc3545'; // default red
                if (avg >= 4) backgroundColor = '#28a745'; // green
                else if (avg >= 3) backgroundColor = '#007bff'; // blue
                else if (avg >= 2) backgroundColor = '#ffc107'; // yellow
                else if (avg >= 1) backgroundColor = '#fd7e14'; // orange
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
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-print me-1"></i>Yes, Print',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success',
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
        
        // Show loading state
        Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait while we prepare your detailed report.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch detailed staff evaluation data including questions and ratings
        fetch(`/staff/detailed-evaluations/${staffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    const evaluations = data.evaluations;
                    
                    // Build the detailed report HTML
                    let html = `
                        <div style="padding:32px;font-family:'Times New Roman', serif; font-size:12pt; max-width: 900px; margin: 0 auto;">
                            <div style='text-align:center;margin-bottom:2em;padding-bottom:1em;'>
                                <div style='display:flex;align-items:center;justify-content:center;margin-bottom:1em;'>
                                    <img src='/images/mcclogo.png' alt='Left Logo' style='width:70px;height:70px;margin-right:1.5em;' onerror='this.style.display="none"'>
                                    <div style='text-align:center;'>
                                        <h2 style='margin:0;font-size:12pt;line-height:1.4;font-family:"Times New Roman", serif;'>Republic of the Philippines<br>
                                        Region VII, Central Visayas<br>
                                        Commission on Higher Education<br>
                                        <strong>Madridejos Community College</strong><br>
                                        Crossing Bunakan, Madridejos, Cebu<br>
                                        <br>
                                        <strong>Center For Guidance Services</strong><br>
                                        <strong style='font-size:12pt;'>MCC Instructor's Performance Evaluation Results</strong><br>
                                        S.Y 2024-2025 - Semester 1</h2>
                                    </div>
                                    <img src='/images/madlogo.png' alt='Right Logo' style='width:70px;height:70px;margin-left:1.5em;' onerror='this.style.display="none"'>
                                </div>
                                <div style='color:#888;font-size:12pt;font-family:"Times New Roman", serif;'>Generated on: ${new Date().toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</div>
                            </div>
                            
                            <!-- Instructor Information Line -->
                            <div style='margin-bottom:2em;font-size:12pt;display:flex;justify-content:space-between;align-items:center;'>
                                <div style='text-align:left;'><strong>Name of Instructor:</strong> ${staff.full_name}</div>
                                <div style='text-align:right;'><strong>Department:</strong> ${staff.department}</div>
                            </div>

                            <!-- Questions and Ratings Table -->
                            <table style='width:100%;border-collapse:collapse;margin-bottom:2em;border:1px solid #333;'>
                                <thead>
                                    <tr style='background:#f8f9fa;'>
                                        <th style='border:1px solid #333;padding:12px;text-align:left;font-weight:bold;width:70%;'>Questionnaires</th>
                                        <th style='border:1px solid #333;padding:12px;text-align:center;font-weight:bold;width:30%;'>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (evaluations && evaluations.length > 0) {
                        // Group evaluations by category
                        const categories = {};
                        evaluations.forEach(eval => {
                            if (!categories[eval.category]) {
                                categories[eval.category] = [];
                            }
                            categories[eval.category].push(eval);
                        });

                        // Display each category with its questions
                        Object.keys(categories).forEach(categoryName => {
                            const categoryEvals = categories[categoryName];
                            
                            // Add category header row
                            html += `
                                <tr>
                                    <td colspan="2" style='border:1px solid #333;padding:8px;background:#e3f2fd;font-weight:bold;color:#007bff;'>
                                        ${categoryName}
                                    </td>
                                </tr>
                            `;
                            
                            // Add questions for this category
                            categoryEvals.forEach(eval => {
                                const rating = parseFloat(eval.average_rating || 0);
                                
                                html += `
                                    <tr>
                                        <td style='border:1px solid #333;padding:12px;text-align:left;vertical-align:top;'>${eval.question_text}</td>
                                        <td style='border:1px solid #333;padding:12px;text-align:center;font-weight:bold;'>${rating.toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                        });
                    } else {
                        html += `
                            <tr>
                                <td colspan="2" style='border:1px solid #333;padding:2em;text-align:center;color:#666;'>
                                    <strong>No Evaluation Data Available</strong><br>
                                    This staff member has not been evaluated yet.
                                </td>
                            </tr>
                        `;
                    }

                    html += `
                                </tbody>
                            </table>
                            
                            <div style='margin-top:2em;text-align:center;color:#888;font-size:0.9em;border-top:1px solid #dee2e6;padding-top:1em;'>
                                <p>This report was automatically generated by the Staff Evaluation System</p>
                            </div>
                        </div>
                    `;

                    // Close loading dialog
                    Swal.close();

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
                            title: 'Report Printed Successfully!',
                            text: 'The staff evaluation report has been sent to the printer.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    };
                    
                    window.addEventListener('afterprint', onAfterPrint);
                    setTimeout(() => { window.print(); }, 100);
                } else {
                    Swal.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error loading staff report: ' + (data.message || 'Unknown error occurred'),
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Error loading staff report. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            });
    }



    // Save All Results functionality moved to sidebar dropdown in dashboard.blade.php
    document.addEventListener('DOMContentLoaded', function() {

        // Initialize staff type tabs
        const staffTypeTabs = document.querySelectorAll('#staffTypeTabs .nav-link');
        
        // Add click event listeners to tabs
        staffTypeTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Clear search inputs when switching tabs
                const teachingInput = document.getElementById('searchInputTeaching');
                const nonTeachingInput = document.getElementById('searchInputNonTeaching');
                
                if (teachingInput) teachingInput.value = '';
                if (nonTeachingInput) nonTeachingInput.value = '';
                
                // Reset table visibility
                setTimeout(() => {
                    const activeStaffType = this.getAttribute('data-staff-type');
                    if (activeStaffType === 'teaching') {
                        searchStaff('teaching');
                    } else if (activeStaffType === 'non-teaching') {
                        searchStaff('non-teaching');
                    }
                }, 100);
            });
        });

        // Function to update tab counts (optional enhancement)
        function updateTabCounts() {
            const teachingTable = document.getElementById('teachingStaffTable');
            const nonTeachingTable = document.getElementById('nonTeachingStaffTable');
            
            if (teachingTable && nonTeachingTable) {
                const teachingRows = teachingTable.querySelectorAll('tbody tr:not([style*="display: none"]):not(:has(td[colspan]))').length;
                const nonTeachingRows = nonTeachingTable.querySelectorAll('tbody tr:not([style*="display: none"]):not(:has(td[colspan]))').length;
                
                // You can add count badges to tabs here if needed
                // Example: document.querySelector('#teaching-tab .staff-count').textContent = teachingRows;
            }
        }

        // Initialize tab counts
        updateTabCounts();
    });

</script> 