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

// Function to get adjectival descriptive rating
function getAdjectivalRating($rating) {
    if ($rating >= 4.51) return 'Outstanding';
    if ($rating >= 3.51) return 'Very Satisfactory';
    if ($rating >= 2.52) return 'Satisfactory';
    if ($rating >= 1.51) return 'Unsatisfactory';
    return 'Unsatisfactory';
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
    .staff-details {
        font-size: 0.74rem;
        line-height: 1.25;
    }
    .staff-details .staff-name {
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.15rem;
    }
    .staff-details .staff-meta {
        font-size: 0.68rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .staff-details .staff-meta i {
        font-size: 0.65rem;
    }
    .staff-details .staff-badge {
        font-size: 0.6rem;
        padding: 0.25em 0.55em;
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
        font-size: 0.82rem;
    }
    .rating-number {
        font-size: 1.02rem;
        font-weight: 600;
    }
    .stats-item {
        text-align: center;
        padding: 0.35rem;
    }
    .stats-number {
        font-size: 0.88rem;
        font-weight: 600;
        color: #007bff;
    }
    .stats-label {
        font-size: 0.6rem;
        color: #6c757d;
        text-transform: uppercase;
    }
    .search-box {
        margin-bottom: 16px;
        position: relative;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 0.75rem;
    }
    #searchInput {
        padding-left: 30px;
        border-radius: 16px;
        border: 1px solid #e0e6ef;
        font-size: 0.7rem;
        height: 1.75rem;
    }
    #searchInput::placeholder {
        font-size: 0.64rem;
        color: #9aa5b5;
    }
    #searchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.12rem rgba(0,123,255,.2);
    }
    .search-refresh-wrapper {
        gap: 0.5rem !important;
    }
    .search-refresh-wrapper .search-box {
        flex: 1 1 240px;
        min-width: 210px;
        margin-bottom: 0;
    }
    .search-refresh-wrapper .search-icon {
        left: 14px;
        font-size: 0.7rem;
    }
    .compact-search {
        padding-left: 36px;
        height: 32px;
        border-radius: 999px;
        font-size: 0.7rem;
        border: 1px solid #d7dff1;
        box-shadow: inset 0 1px 1px rgba(102,126,234,0.05);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .compact-search:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 0.12rem rgba(108,92,231,0.18);
    }
    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.78rem;
    }
    .action-btn {
        padding: 0.18rem 0.4rem;
        font-size: 0.7rem;
        border-radius: 12px;
    }
    .comments-modal .modal-body {
        max-height: 320px;
        overflow-y: auto;
    }
    .comment-item {
        border-left: 3px solid #007bff;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        border-radius: 0 6px 6px 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        font-size: 0.74rem;
    }
    .comment-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 5px;
    }
    .comment-date {
        font-size: 0.66rem;
        color: #6c757d;
    }
    .no-comments {
        text-align: center;
        padding: 24px;
        color: #6c757d;
        font-size: 0.72rem;
    }
    .range-bar {
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #e9ecef;
        margin-top: 4px;
        margin-bottom: 2px;
        position: relative;
    }
    .range-bar-fill {
        height: 100%;
        border-radius: 5px;
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

    /* Compact modal adjustments for action modals */
    .action-modal-dialog {
        max-width: 680px;
    }

    .action-modal-content {
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .action-modal-content .modal-title {
        font-size: 1.05rem;
        font-weight: 600;
    }

    .action-modal-body {
        font-size: 0.88rem;
    }

    .action-modal-body .modal-section-title {
        font-size: 0.95rem;
    }

    .action-btn {
        padding: 0.18rem 0.4rem;
        font-size: 0.68rem;
        border-radius: 10px;
    }

    .refresh-btn-enhanced {
        padding: 0.24rem 0.7rem;
        min-height: 28px;
        font-size: 0.64rem;
        font-weight: 600;
        letter-spacing: 0.12px;
        border-radius: 999px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 5px 14px rgba(102,126,234,0.14);
        background: linear-gradient(135deg, #4f8ef7 0%, #6c5ce7 100%);
        border: none;
    }

    .refresh-btn-enhanced i {
        font-size: 0.68rem;
    }

    .refresh-btn-enhanced span {
        font-size: 0.66rem;
        font-weight: 600;
    }

    .refresh-btn-enhanced:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(102,126,234,0.22);
    }

    .refresh-btn-enhanced:active {
        transform: translateY(0);
        box-shadow: 0 5px 12px rgba(102,126,234,0.18);
    }

    @media (max-width: 992px) {
        .action-modal-content {
            font-size: 0.88rem;
        }
        .action-modal-content .modal-title {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .action-modal-dialog {
            margin: 0.5rem;
        }
        .action-modal-content {
            font-size: 0.84rem;
        }
        .action-modal-content .modal-title {
            font-size: 0.95rem;
        }
    }

    /* Additional specificity for modal content */
    .modal-body .range-bar-fill.range-bar-green { background-color: #28a745 !important; }
    .modal-body .range-bar-fill.range-bar-blue { background-color: #007bff !important; }
    .modal-body .range-bar-fill.range-bar-yellow { background-color: #ffc107 !important; }
    .modal-body .range-bar-fill.range-bar-orange { background-color: #fd7e14 !important; }
    .modal-body .range-bar-fill.range-bar-red { background-color: #dc3545 !important; }
    .range-legend {
        font-size: 0.72rem;
        margin-bottom: 12px;
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 12px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .range-legend span.color {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        border: 1px solid #fff;
        outline: 1px solid #bbb;
    }
    .range-legend-label {
        font-weight: 600;
        color: #333;
        letter-spacing: 0.3px;
        font-size: 0.72rem;
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
        padding: 1.5em 0 0.9em 0;
        margin-bottom: 1.4em;
        text-align: center;
        border-radius: 0.6em 0.6em 0 0;
        box-shadow: 0 1px 8px rgba(102,126,234,0.08);
    }
    .report-logo {
        width: 82px;
        height: auto;
        margin-bottom: 0.7em;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .report-title {
        font-size: 1.4em;
        font-weight: 700;
        letter-spacing: 0.6px;
        margin-bottom: 0.15em;
        color: #fff;
        text-shadow: 0 2px 6px rgba(44,62,80,0.08);
    }
    .report-date {
        color: #e0e0e0;
        font-size: 0.78em;
        margin-bottom: 0.8em;
    }
    .report-section-title {
        font-size: 0.92em;
        font-weight: 600;
        margin-top: 1.4em;
        margin-bottom: 0.5em;
        color: #444;
        border-bottom: 2px solid #eee;
        padding-bottom: 0.15em;
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
        font-weight: 600;
        border-bottom: 1px solid #dee2e6;
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
        border: none;
        border-radius: 12px;
        background: #f8f9fa;
        padding: 0.45rem;
        display: inline-flex;
        gap: 0.4rem;
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0.45rem 0.85rem;
        font-weight: 600;
        color: #495057;
        background: transparent;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.82rem;
    }

    .nav-tabs .nav-link i {
        font-size: 0.95rem;
    }

    .nav-tabs .nav-link:hover,
    .nav-tabs .nav-link:focus {
        border-color: #dee2e6;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.08);
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(29, 78, 216, 0.25);
    }

    .nav-tabs .nav-link.active .staff-type-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .staff-type-badge {
        display: inline-block;
        padding: 0.12rem 0.5rem;
        border-radius: 12px;
        font-size: 0.62rem;
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        transition: inherit;
    }


    /* Responsive tab styling */
    @media (max-width: 768px) {
        .nav-tabs {
            padding: 4px 4px 0 4px;
        }
        
        .nav-tabs .nav-link {
            padding: 6px 10px;
            font-size: 0.74rem;
            margin-right: 2px;
        }
        
        .nav-tabs .nav-link i {
            margin-right: 4px;
            font-size: 0.8rem;
        }
        
        .staff-type-badge {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .nav-tabs .nav-link {
            padding: 5px 8px;
            font-size: 0.7rem;
        }
        
        .nav-tabs .nav-link span:first-of-type {
            display: none;
        }
    }
</style>

<div class="container-fluid py-4 staff-ratings-page">
    <div class="row page-full-width">
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
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 search-refresh-wrapper">
                                <div class="search-box mb-0 flex-grow-1">
                                    <div class="search-icon"><i class="fas fa-search"></i></div>
                                    <input type="text" id="searchInputTeaching" onkeyup="searchStaff('teaching')" 
                                           placeholder="Search teaching staff..." 
                                           class="form-control compact-search">
                                </div>
                                <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 refresh-btn-enhanced"
                                        onclick="location.reload();">
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
                                            <div class="staff-details">
                                                <span class="staff-name text-primary">{{ $staff->full_name }}</span>
                                                <br>
                                                <span class="staff-meta text-muted">
                                                    <i class="fas fa-id-badge"></i>
                                                    {{ $staff->staff_id }}
                                                </span>
                                                <br>
                                                <span class="staff-meta text-muted">
                                                    <i class="fas fa-envelope"></i>
                                                    {{ $staff->email }}
                                                </span>
                                                <br>
                                                <span class="badge bg-secondary staff-badge">{{ $staff->department }}</span>
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
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 search-refresh-wrapper">
                                <div class="search-box mb-0 flex-grow-1">
                                    <div class="search-icon"><i class="fas fa-search"></i></div>
                                    <input type="text" id="searchInputNonTeaching" onkeyup="searchStaff('non-teaching')" 
                                           placeholder="Search non-teaching staff..." 
                                           class="form-control compact-search">
                                </div>
                                <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 refresh-btn-enhanced"
                                        onclick="location.reload();">
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
                                                    <div class="staff-details">
                                                        <span class="staff-name text-primary">{{ $staff->full_name }}</span>
                                                        <br>
                                                        <span class="staff-meta text-muted">
                                                            <i class="fas fa-id-badge"></i>
                                                            {{ $staff->staff_id }}
                                                        </span>
                                                        <br>
                                                        <span class="staff-meta text-muted">
                                                            <i class="fas fa-envelope"></i>
                                                            {{ $staff->email }}
                                                        </span>
                                                        <br>
                                                        <span class="badge bg-secondary staff-badge">{{ $staff->department }}</span>
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
    <div class="modal-dialog modal-lg action-modal-dialog">
        <div class="modal-content action-modal-content">
            <div class="modal-header table-header text-white">
                <h5 class="modal-title" id="commentsModalLabel">
                    <i class="fas fa-comments me-2"></i>Staff Comments
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body action-modal-body" id="commentsContent">
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
    <div class="modal-dialog modal-lg action-modal-dialog">
        <div class="modal-content action-modal-content">
            <div class="modal-header table-header text-white">
                <h5 class="modal-title" id="staffProfileModalLabel">
                    <i class="fas fa-user-circle me-2"></i>Instructors Profile & Ratings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body action-modal-body" id="staffProfileContent">
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
            text: 'Are you sure you want to print the report for this instructor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Print',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary',
                actions: 'swal-actions-spaced'
            },
            buttonsStyling: false,
            didOpen: () => {
                // Ensure the custom SweetAlert action styling exists only once
                if (!document.getElementById('swal-actions-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-actions-style';
                    style.textContent = `
                        .swal-actions-spaced {
                            display: flex !important;
                            flex-wrap: nowrap !important;
                            align-items: center !important;
                            justify-content: center !important;
                            gap: 12px !important;
                        }
                        .swal-actions-spaced .btn {
                            margin: 0 !important;
                            min-width: 110px !important;
                            padding: 8px 16px !important;
                            white-space: nowrap !important;
                            font-size: 0.9rem !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
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
        fetch(`{{ url('/staff/detailed-evaluations') }}/${staffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    const evaluations = data.evaluations;
                    
                    // Determine Department Head
                    let deptHead = '';
                    const dept = (staff.department || '').toUpperCase();
                    if (dept === 'BSIT') {
                        deptHead = 'DR. DINO L. ILUSTRISIMO';
                    } else if (dept === 'EDUC') {
                        deptHead = 'DR. PRESCILLA F. CANOY';
                    } else if (dept === 'BSBA') {
                        deptHead = 'DR. ISRAEL N. ABBARATIGUE';
                    } else if (dept === 'BSHM') {
                        deptHead = 'CHRISTY FORSUELO';
                    } else if (dept === 'GEC') {
                        deptHead = 'DR. ISRAEL N. ABARRATIGUE';
                    }
                    
                    // Build the detailed report HTML
                    let html = `
                        <style>
                            @media print {
                                tr { page-break-inside: avoid !important; }
                                td { page-break-after: avoid !important; }
                                .header-section { page-break-after: avoid !important; }
                                .instructor-info { page-break-after: avoid !important; }
                                .questions-table { page-break-before: avoid !important; }
                            }
                        </style>
                        <div style="padding:8px;font-family:'Times New Roman', serif; font-size:9pt; max-width: 900px; margin: 0 auto; line-height: 1.15;">
                            <div class='header-section' style='text-align:center;margin-bottom:0.2em;padding-bottom:0;'>
                                <div style='display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:2.15em;'>
                                    <img src='/images/cgs.jpg' alt='Left Logo' style='width:60px;height:60px;flex-shrink:0;margin-right:5px;' onerror='this.style.display="none"'>
                                    <div style='text-align:center; flex:0 0 auto;'>   
                                        <strong style='font-size:10pt;'>MADRIDEJOS COMMUNITY COLLEGE</strong><br>                                   
                                        <strong style='font-size:9pt;'>Center For Guidance Services</strong><br>
                                        <span style='font-size:8.5pt;'>Crossing Bunakan, Madridejos, Cebu</span><br>
                                        <span style='font-size:7.5pt; color: blue; text-decoration: underline;'>
                                            <i class='fab fa-facebook'></i> fb.com/MCCCenterforGuidanceServices<br>
                                            <i class='fas fa-envelope'></i> mcc.cgsofficial@gmsil.com
                                        </span><br>
                                         <strong style='font-size:9.5pt;'>MCC Instructor's Performance Evaluation Results</strong><br>
                                       <span style='font-size:8.5pt;'>S.Y {{ $currentAcademicYear?->year }} - Semester {{ $currentAcademicYear?->semester }}</span>
                                    </div>
                                    <img src='/images/logo.png' alt='Right Logo' style='width:80px;height:80px;flex-shrink:0;margin-left:5px;' onerror='this.style.display="none"'>
                                </div>
                            </div>
                            
                            <!-- Instructor Information Line -->
                            <div class='instructor-info' style='margin-bottom:0.3em;font-size:9pt;display:flex;justify-content:space-between;align-items:center;'>
                                <di style='text-align:left;'><strong>Name:</strong> ${staff.full_name}</di>
                                <div style='text-align:right;'><strong>Dept:</strong> ${staff.department}</div>
                            </div>

                            <!-- Questions and Ratings Table -->
                            <table class='questions-table' style='width:100%;border-collapse:collapse;margin-bottom:0.4em;border:1px solid #333; font-size:8.5pt;'>
                                <tbody>
                                    <!-- Header Row (as regular tbody row to prevent repetition) -->
                                    <tr style='background:#f8f9fa;page-break-inside:avoid;page-break-after:avoid; height:16px;'>
                                        <td style='border:1px solid #333;padding:2px 3px;text-align:left;font-weight:bold;width:70%; overflow:hidden; white-space:nowrap;'>Questionnaires</td>
                                        <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;width:30%; height:16px;'>Rating</td>
                                    </tr>
                    `;

                    if (evaluations && evaluations.length > 0) {
                        // Group evaluations by category while preserving order
                        const categories = {};
                        const categoryOrder = [];
                        evaluations.forEach(eval => {
                            if (!categories[eval.category]) {
                                categories[eval.category] = [];
                                categoryOrder.push(eval.category);
                            }
                            categories[eval.category].push(eval);
                        });

                        // Display each category with its questions in the order they were encountered
                        categoryOrder.forEach(categoryName => {
                            const categoryEvals = categories[categoryName];
                            
                            // Add category header row
                            html += `
                                <tr style='height:14px;'>
                                    <td colspan="2" style='border:1px solid #333;padding:2px 3px;background:#e3f2fd;font-weight:bold;color:#007bff; font-size:7.5pt;'>
                                        ${categoryName}
                                    </td>
                                </tr>
                            `;
                            
                            // Add questions for this category
                            categoryEvals.forEach(eval => {
                                const rating = parseFloat(eval.average_rating || 0);
                                
                                html += `
                                    <tr style='height:18px;'>
                                        <td style='border:1px solid #333;padding:2px 3px;text-align:left;vertical-align:middle; word-wrap:break-word; font-size:8pt;'>${eval.question_text}</td>
                                        <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold; height:18px; font-size:8pt;'>${rating.toFixed(2)}</td>
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
                            
                            <!-- Summary Evaluation Table - Start on New Page -->
                            <div style='page-break-before:always; margin-top:2em; padding-left:3em; margin-bottom:0.5em;'>
                                <h3 style='font-size:10pt;font-weight:bold;margin-bottom:0.4em;margin-top:0;color:#333;border-bottom:1px solid #ffffff;padding-bottom:0.2em;'>Summary Evaluation</h3>
                                <table style='width:95%;border-collapse:collapse;border:1px solid #333; font-size:8.5pt;'>
                                    <thead>
                                        <tr style='background:#f8f9fa; height:16px;'>
                                            <th style='border:1px solid #333;padding:2px 3px;text-align:left;font-weight:bold;width:25%;'>Criteria</th>
                                            <th style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;width:12%;'>Scores</th>
                                            <th style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;width:18%;'>Verbal Interpretation</th>
                                            <th style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;width:45%;'>Descriptive Explanation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    // Calculate category averages for Summary Evaluation
                    if (evaluations && evaluations.length > 0) {
                        const categories = {};
                        const categoryTotals = {};
                        const categoryCounts = {};
                        
                        evaluations.forEach(eval => {
                            const categoryName = eval.category;
                            const rating = parseFloat(eval.average_rating || 0);
                            
                            if (!categoryTotals[categoryName]) {
                                categoryTotals[categoryName] = 0;
                                categoryCounts[categoryName] = 0;
                            }
                            
                            categoryTotals[categoryName] += rating;
                            categoryCounts[categoryName]++;
                        });
                        
                        // Generate summary rows for each category
                        let totalSum = 0;
                        let totalCount = 0;
                        
                        Object.keys(categoryTotals).forEach(categoryName => {
                            const average = categoryTotals[categoryName] / categoryCounts[categoryName];
                            const verbalInterpretation = getAdjectivalRating(average);
                            const descriptiveExplanation = getDescriptiveExplanation(verbalInterpretation);
                            
                            // Add to total calculation
                            totalSum += average;
                            totalCount++;
                            
                            html += `
                                <tr style='height:16px;'>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:left;vertical-align:middle;'>${categoryName}</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;vertical-align:middle;'>${average.toFixed(2)}</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;vertical-align:middle;'>${verbalInterpretation}</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:left;font-style:italic;vertical-align:middle;line-height:1.2;'>${descriptiveExplanation}</td>
                                </tr>
                            `;
                        });
                        
                        // Add Total row
                        if (totalCount > 0) {
                            const overallAverage = totalSum / totalCount;
                            const overallVerbalInterpretation = getAdjectivalRating(overallAverage);
                            const overallDescriptiveExplanation = getDescriptiveExplanation(overallVerbalInterpretation);
                            
                            html += `
                                <tr style='background:#f0f8ff; height:16px;'>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:left;vertical-align:middle;font-weight:bold;'>Total</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;vertical-align:middle;'>${overallAverage.toFixed(2)}</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:center;font-weight:bold;vertical-align:middle;'>${overallVerbalInterpretation}</td>
                                    <td style='border:1px solid #333;padding:2px 3px;text-align:left;font-weight:bold;vertical-align:middle;line-height:1.2;'>${overallDescriptiveExplanation}</td>
                                </tr>
                            `;
                        }
                    } else {
                        html += `
                            <tr>
                                <td colspan="4" style='border:1px solid #333;padding:2em;text-align:center;color:#666;'>
                                    <strong>No Evaluation Data Available</strong>
                                </td>
                            </tr>
                        `;
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Signature Section -->
                            <div style='margin-top:2em;margin-bottom:0em;text-align:left; font-size:10pt; line-height:1.3; padding-left:3em;'>
                                <div style='margin-bottom:1em;'>
                                    Prepared by:
                                </div>
                                <div style='margin-bottom:0.1em;'>
                                    <strong>DHINA B. DALISAY</strong>
                                </div>
                                <div style='margin-bottom:2em;'>
                                    Guidance Advocate
                                </div>

                                <div style='display:flex; justify-content:space-between; align-items:flex-start;'>
                                    <div style='text-align:left;'>
                                        <div style='margin-bottom:1em;'>
                                            Reviewed and Noted by:
                                        </div>
                                        <div style='margin-bottom:0.1em;'>
                                            <strong>DR. LIZA D. GARCIA</strong>
                                        </div>
                                        <div>
                                            Guidance Counselor
                                        </div>
                                    </div>
                                    <div style='text-align:left; margin-right:4em;'>
                                        <div style='margin-bottom:0.2em;'>
                                            Received by: _____________
                                        </div>
                                        <div style='margin-bottom:0.1em;margin-right:2em;'>
                                            <strong>${deptHead}</strong>
                                        </div>
                                    </div>
                                </div>
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
                            text: 'The evaluation report has been sent to the printer.',
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

    // Function to get adjectival descriptive rating (JavaScript version)
    function getAdjectivalRating(rating) {
        if (rating >= 4.51) return 'Outstanding';
        if (rating >= 3.51) return 'Very Satisfactory';
        if (rating >= 2.52) return 'Satisfactory';
        if (rating >= 1.51) return 'Unsatisfactory';
        return 'Unsatisfactory';
    }

    // Function to get descriptive explanation based on verbal interpretation
    function getDescriptiveExplanation(verbalInterpretation) {
        switch(verbalInterpretation) {
            case 'Outstanding':
                return "The instructor's performance is exceptional<br>and represents extraordinary achievement.";
            case 'Very Satisfactory':
                return "Performance consistently exceeds expectations<br>and demonstrates high competency.";
            case 'Satisfactory':
                return "Performance meets expectations and shows<br>adequate competency in this area.";
            case 'Unsatisfactory':
                return "Performance does not meet the required standards<br>and needs improvement.";
            default:
                return "No evaluation data available<br>for this criteria.";
        }
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
