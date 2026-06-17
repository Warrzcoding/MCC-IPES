<meta name="csrf-token" content="{{ csrf_token() }}">

@php
// Function to get rating status and color
function getRatingStatus($rating) {
    if ($rating >= 4.51) return ['status' => 'Outstanding', 'color' => '#28a745', 'bg' => '#d4edda'];
    if ($rating >= 3.51) return ['status' => 'Very Satisfactory', 'color' => '#17a2b8', 'bg' => '#d1ecf1'];
    if ($rating >= 2.51) return ['status' => 'Satisfactory', 'color' => '#ffc107', 'bg' => '#fff3cd'];
    if ($rating >= 1.51) return ['status' => 'Unsatisfactory', 'color' => '#fd7e14', 'bg' => '#ffeaa7'];
    return ['status' => 'Poor', 'color' => '#dc3545', 'bg' => '#e3e8ea'];
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
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 15px;
        background: #fff;
    }
    .rating-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .staff-image {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    .default-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        color: white;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
    }
    .rating-badge {
        font-size: 0.62rem;
        padding: 0.24em 0.55em;
        border-radius: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }
    .rating-stars {
        color: #ffc107;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 1px;
    }
    .rating-number {
        font-size: 0.98rem;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.08);
        white-space: nowrap;
    }

    .section-header {
        background: linear-gradient(135deg, #5a67d8 0%, #4c51bf 100%);
        color: white;
        padding: 0.55rem 0.85rem;
        border-radius: 10px 10px 0 0;
        margin-bottom: 0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.18);
    }
    .section-title {
        font-size: 0.92rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .section-title i {
        background: rgba(255,255,255,0.18);
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .section-title span {
        display: inline-flex;
        align-items: center;
    }
    .section-count {
        background: rgba(255,255,255,0.18);
        padding: 0.16em 0.48em;
        border-radius: 13px;
        font-size: 0.62rem;
    }
    .staff-list {
        max-height: 600px;
        overflow-y: auto;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    .staff-item {
        background: white;
        padding: 14px;
        border-radius: 10px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .staff-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-left-color: #667eea;
    }
    .staff-rank {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.68rem;
        margin-right: 12px;
        flex-shrink: 0;
        position: relative;
    }
    .staff-rank.top-3 {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    }
    .staff-rank.rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
    }
    .staff-rank.rank-2 {
        background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
        box-shadow: 0 0 7px rgba(192, 192, 192, 0.35);
    }
    .staff-rank.rank-3 {
        background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%);
        box-shadow: 0 0 7px rgba(205, 127, 50, 0.35);
    }
    .rank-label {
        position: absolute;
        top: -6px;
        right: -20px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        font-size: 0.5em;
        padding: 2px 5px;
        border-radius: 10px;
        white-space: nowrap;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .rank-label.rank-1-label {
        background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
        color: #333;
        font-weight: bold;
    }
    .rank-label.rank-2-label {
        background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
        color: #333;
    }
    .rank-label.rank-3-label {
        background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%);
        color: white;
    }
    .staff-details {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        font-size: 0.72rem;
        line-height: 1.35;
    }
    .staff-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        font-size: 0.82rem;
    }
    .staff-info-line {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 0;
    }
    .staff-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 0;
    }
    .staff-badges .badge {
        font-size: 0.6rem;
        padding: 0.22em 0.55em;
        border-radius: 14px;
        font-weight: 600;
    }
    .rating-section {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 0;
        flex-wrap: wrap;
    }
    .no-staff-message {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .no-staff-message i {
        font-size: 3em;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    .overall-header {
        background: linear-gradient(145deg, #1e3c72 0%, #2a5298 50%, #667eea 100%);
        color: white;
        padding: 0.9rem 1.6rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 14px 28px rgba(30, 60, 114, 0.26),
            inset 0 1px 0 rgba(255, 255, 255, 0.18),
            inset 0 -1px 0 rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .overall-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
        pointer-events: none;
    }
    
    .overall-header::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,0.04) 0%, transparent 50%);
        pointer-events: none;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .overall-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        position: relative;
        z-index: 2;
        color: #ffffff;
        text-shadow: 
            0 2px 4px rgba(0,0,0,0.25),
            0 4px 8px rgba(0,0,0,0.18),
            0 0 16px rgba(255,255,255,0.08);
        letter-spacing: -0.4px;
        line-height: 1.05;
    }
    
    .overall-title i {
        color: #ffd700;
        margin-right: 10px;
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
        transform: scale(1);
        display: inline-block;
        animation: bounce 2s ease-in-out infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: scale(1) translateY(0); }
        40% { transform: scale(1) translateY(-6px); }
        60% { transform: scale(1) translateY(-3px); }
    }
    
    .overall-subtitle {
        font-size: 0.98rem;
        opacity: 0.9;
        margin: 0;
        position: relative;
        z-index: 2;
        text-shadow: 0 1px 3px rgba(0,0,0,0.25);
        font-weight: 400;
        color: rgba(255, 255, 255, 0.92);
        max-width: 560px;
        margin: 0 auto;
        line-height: 1.35;
    }

    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .staff-item {
            padding: 15px;
        }
        .staff-rank {
            width: 30px;
            height: 30px;
            font-size: 0.8em;
            margin-right: 10px;
        }
        .rank-label {
            font-size: 0.5em;
            padding: 1px 4px;
            right: -20px;
        }
        .staff-name {
            font-size: 1em;
        }
        .staff-info-line {
            gap: 10px;
            flex-wrap: wrap;
        }
        .rating-number {
            font-size: 1.1em;
        }
        .rating-section {
            gap: 8px;
            flex-wrap: wrap;
        }
        .section-title {
            font-size: 1.1em;
        }
        .overall-title {
            font-size: 2em;
            letter-spacing: -0.3px;
        }
        .overall-header {
            padding: 1.2rem 1.5rem;
            border-radius: 16px;
        }
        .overall-subtitle {
            font-size: 1.1em;
        }
        .overall-title i {
            margin-right: 10px;
        }
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
            padding: 0 !important;
            background: white !important;
            margin: 0 !important;
        }
        /* Proper margins for every page - set to 0 to hide browser headers/footers */
        @page { 
            margin: 0 !important; 
            size: A4;
        }
        
        /* Ensure table headers repeat and rows don't break */
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
    }
</style>

<div class="container-fluid page-full-width py-4">
    <!-- Overall Header -->
    <div class="overall-header">
        <div class="overall-title">
            <i class="fas fa-trophy me-3"></i>Overall Instructors Performance Rankings
        </div>
        <p class="overall-subtitle">
            Comprehensive evaluation results for all staff members, ranked by performance
        </p>
    </div>

    <div class="row">
        <!-- Instructors Column (Left) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="section-header d-flex align-items-center justify-content-between">
                    <div class="section-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instructors Ranking</span>
                        @php 
                            $instructors = isset($teachingStaffRatings) ? $teachingStaffRatings : collect();
                        @endphp
                        <span class="section-count">{{ $instructors->count() }}</span>
                    </div>
                    <button class="btn btn-sm btn-light fw-bold rounded-pill shadow-sm px-3" onclick="confirmGenerateReport('teaching')" style="font-size: 0.75rem;">
                        <i class="fas fa-file-alt me-1"></i> Report
                    </button>
                </div>
                <div class="staff-list">
                    @if($instructors->count() > 0)
                        @foreach($instructors as $index => $staff)
                            @php 
                                $ratingInfo = getRatingStatus($staff->average_rating);
                                $starRating = round($staff->average_rating);
                                $instructorRank = $index + 1; // Separate ranking for instructors
                            @endphp
                            <div class="staff-item">
                                <div class="d-flex align-items-start">
                                    <div class="staff-rank {{ $instructorRank <= 3 ? 'top-3' : '' }} {{ $instructorRank == 1 ? 'rank-1' : '' }} {{ $instructorRank == 2 ? 'rank-2' : '' }} {{ $instructorRank == 3 ? 'rank-3' : '' }}">
                                        {{ $instructorRank }}
                                        <span class="rank-label {{ $instructorRank == 1 ? 'rank-1-label' : '' }} {{ $instructorRank == 2 ? 'rank-2-label' : '' }} {{ $instructorRank == 3 ? 'rank-3-label' : '' }}">
                                            Rank {{ $instructorRank }}
                                        </span>
                                    </div>
                                    
                                    <div class="me-3">
                                        @if(!empty($staff->image_path) && file_exists(public_path($staff->image_path)))
                                            <img src="{{ asset($staff->image_path) }}" 
                                                 alt="{{ $staff->full_name }}" 
                                                 class="staff-image">
                                        @else
                                            <div class="default-avatar">
                                                {{ strtoupper(substr($staff->full_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="staff-details">
                                        <div class="staff-name">{{ $staff->full_name }}</div>
                                        <div class="staff-info-line">
                                            <div class="staff-badges">
                                                <span class="badge bg-secondary">{{ $staff->department }}</span>
                                                <span class="badge bg-primary">Instructor</span>
                                            </div>
                                            
                                            <div class="rating-section">
                                                <div class="rating-number" data-rating="{{ $staff->average_rating }}" style="color: {{ $ratingInfo['color'] }}">
                                                    {{ round($staff->average_rating, 2) }}/5
                                                </div>
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $starRating ? '' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="rating-badge" 
                                                      style="background-color: {{ $ratingInfo['bg'] }}; color: {{ $ratingInfo['color'] }}">
                                                    {{ $ratingInfo['status'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-staff-message">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <h5>No Instructor Evaluations</h5>
                            <p>No evaluations have been submitted for instructors yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Non-Teaching Staff Column (Right) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="section-header d-flex align-items-center justify-content-between">
                    <div class="section-title">
                        <i class="fas fa-users-cog"></i>
                        <span>Non-Teaching Staff Ranking</span>
                        @php 
                            $nonTeachingStaff = isset($nonTeachingStaffRatings) ? $nonTeachingStaffRatings : collect();
                        @endphp
                        <span class="section-count">{{ $nonTeachingStaff->count() }}</span>
                    </div>
                    <button class="btn btn-sm btn-light fw-bold rounded-pill shadow-sm px-3" onclick="confirmGenerateReport('non-teaching')" style="font-size: 0.75rem;">
                        <i class="fas fa-file-alt me-1"></i> Report
                    </button>
                </div>
                <div class="staff-list">
                    @if($nonTeachingStaff->count() > 0)
                        @foreach($nonTeachingStaff as $index => $staff)
                            @php 
                                $ratingInfo = getRatingStatus($staff->average_rating);
                                $starRating = round($staff->average_rating);
                                $nonTeachingRank = $index + 1; // Separate ranking for non-teaching staff
                            @endphp
                            <div class="staff-item">
                                <div class="d-flex align-items-start">
                                    <div class="staff-rank {{ $nonTeachingRank <= 3 ? 'top-3' : '' }} {{ $nonTeachingRank == 1 ? 'rank-1' : '' }} {{ $nonTeachingRank == 2 ? 'rank-2' : '' }} {{ $nonTeachingRank == 3 ? 'rank-3' : '' }}">
                                        {{ $nonTeachingRank }}
                                        <span class="rank-label {{ $nonTeachingRank == 1 ? 'rank-1-label' : '' }} {{ $nonTeachingRank == 2 ? 'rank-2-label' : '' }} {{ $nonTeachingRank == 3 ? 'rank-3-label' : '' }}">
                                            Rank {{ $nonTeachingRank }}
                                        </span>
                                    </div>
                                    
                                    <div class="me-3">
                                        @if(!empty($staff->image_path) && file_exists(public_path($staff->image_path)))
                                            <img src="{{ asset($staff->image_path) }}" 
                                                 alt="{{ $staff->full_name }}" 
                                                 class="staff-image">
                                        @else
                                            <div class="default-avatar">
                                                {{ strtoupper(substr($staff->full_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="staff-details">
                                        <div class="staff-name">{{ $staff->full_name }}</div>
                                        <div class="staff-info-line">
                                            <div class="staff-badges">
                                                <span class="badge bg-secondary">{{ $staff->department }}</span>
                                                <span class="badge bg-info">Non-Teaching</span>
                                            </div>
                                            
                                            <div class="rating-section">
                                                <div class="rating-number" data-rating="{{ $staff->average_rating }}" style="color: {{ $ratingInfo['color'] }}">
                                                    {{ round($staff->average_rating, 2) }}/5
                                                </div>
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $starRating ? '' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="rating-badge" 
                                                      style="background-color: {{ $ratingInfo['bg'] }}; color: {{ $ratingInfo['color'] }}">
                                                    {{ $ratingInfo['status'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-staff-message">
                            <i class="fas fa-users-cog"></i>
                            <h5>No Non-Teaching Staff Evaluations</h5>
                            <p>No evaluations have been submitted for non-teaching staff yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments Modal (reused from staff-ratings) -->
<div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="commentsModalLabel">
                    <i class="fas fa-comments me-2"></i>Comments for <span id="staffNameInModal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body comments-modal">
                <div id="commentsContent">
                    <!-- Comments will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Staff Profile Modal (reused from staff-ratings) -->
<div class="modal fade" id="staffProfileModal" tabindex="-1" aria-labelledby="staffProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="staffProfileModalLabel">
                    <i class="fas fa-user-circle me-2"></i>Staff Profile & Detailed Ratings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="staffProfileContent">
                    <!-- Profile content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to handle report generation confirmation
function confirmGenerateReport(type) {
    const title = type === 'teaching' ? 'Instructors' : 'Non-Teaching Staff';
    Swal.fire({
        title: 'Generate Performance Report',
        html: `Are you sure you want to generate the ranking report for <b>${title}</b>?<br><small class="text-muted">This will prepare a printable version of the current rankings.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-print me-1"></i> Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            generateOverallReport(type);
        }
    });
}

function generateOverallReport(type) {
    const title = type === 'teaching' ? 'Instructors' : 'Non-Teaching Staff';
    
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

    const staffData = getCurrentOverallStaffData(type);

    if (staffData.length === 0) {
        Swal.fire({
            title: 'No Data Available',
            text: `No ${title.toLowerCase()} found to generate a report.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    setTimeout(() => {
        printOverallReport(staffData, type);
        Swal.close();
    }, 1000);
}

function getCurrentOverallStaffData(type) {
    const staffData = [];
    // Select correct column based on type
    const columnSelector = type === 'teaching' ? '.col-lg-6:first-child' : '.col-lg-6:last-child';
    const items = document.querySelectorAll(`${columnSelector} .staff-item`);

    items.forEach(item => {
        const nameEl = item.querySelector('.staff-name');
        const ratingEl = item.querySelector('.rating-number');
        const deptEl = item.querySelector('.badge.bg-secondary');

        if (nameEl && ratingEl) {
            const name = nameEl.textContent.trim();
            const rating = parseFloat(ratingEl.getAttribute('data-rating') || ratingEl.textContent.split('/')[0]);
            const department = deptEl ? deptEl.textContent.trim() : 'N/A';

            if (!isNaN(rating)) {
                staffData.push({
                    name: name,
                    rating: rating,
                    department: department
                });
            }
        }
    });

    return staffData;
}

function printOverallReport(staffData, type) {
    const title = type === 'teaching' ? 'Instructors' : 'Non-Teaching Staff';
    const adjectivalLabel = type === 'teaching' ? 'ADJECTIVAL DESCRIPTIVE' : 'ADJECTIVAL DESCRIPTION';
    
    let html = `
        <style>
            @media print {
                body > *:not(#customPrintArea) { display: none !important; }
                #customPrintArea { display: block !important; }
                .print-row { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            }
        </style>
        <div style="padding: 0.5in 0.5in 0.5in 0.5in; font-family: Arial, sans-serif; font-size:10pt; max-width: 1000px; margin: 0 auto; line-height: 1.15;">
            <div class='header-section' style='text-align:center; margin-top:0; margin-bottom:0.2em; padding-bottom:0;'>
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
                         <strong style='font-size:10pt; font-family: "Century Gothic", sans-serif;'>MCC Instructor's Performance Evaluation Results</strong><br>
                       <span style='font-size:10pt; font-family: "Century Gothic", sans-serif; font-weight: normal;'>S.Y {{ $currentAcademicYear?->year }} - {{ $currentAcademicYear?->semester == 1 ? 'First' : ($currentAcademicYear?->semester == 2 ? 'Second' : $currentAcademicYear?->semester) }} Sem </span>
                    </div>
                    <img src='/images/logo.png' alt='Right Logo' style='width:100px;height:100px;flex-shrink:0;margin-left:5px;' onerror='this.style.display="none"'>
                </div>
            </div>
            
            <div style="text-align: left; margin-bottom: 0.1em; font-size: 10pt; line-height: 1.2;">
                <p style="margin-bottom: 0.15em;">To:<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DR. FLORIPIS A. MONTECILLO</strong></p>
                <p style="margin-bottom: 0.3em; margin-left: 2em;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;College President</p>        
                <p style="margin-bottom: 0.3em;">From:&nbsp;&nbsp;&nbsp;&nbsp;Center for Guidance Services</p>
                <p style="margin-bottom: 1em;">Subject:&nbsp;Endorsement of Performance Evaluation Results of ${title}</p>   
                
               <p style="margin-bottom: 1em;">Greetings of Peace!</p>
     
                <p style="margin-bottom: 0.7em;">I am writing to formally endorse the Results of the Performance Evaluation of all MCC ${title} for Academic Year {{ $currentAcademicYear?->year }} - {{ $currentAcademicYear?->semester == 1 ? 'First' : ($currentAcademicYear?->semester == 2 ? 'Second' : $currentAcademicYear?->semester) }} Semester.</p>
                
                <p style="margin-bottom: 0.7em;">Please be advised that the following ${title.toLowerCase()} were evaluated by all MCC Students.</p>
            </div>
            
            <div style="margin-bottom: 0.8em;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 0.4em;">
                    <thead>
                        <tr style="height: 40px; border: none !important;"><th colspan="4" style="border: none !important;"></th></tr>
                        <tr style="background-color: #f8f9fa;">
                            <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: left; font-weight: bold; font-size: 9.5pt;">${type === 'teaching' ? 'NAME OF INSTRUCTORS' : 'NAME OF STAFF'}</th>
                            <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; font-size: 9.5pt;">AVERAGE SCORES</th>
                            <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; font-size: 9.5pt;">${adjectivalLabel}</th>
                            <th style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; font-size: 9.5pt;">DEPARTMENT</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    staffData.forEach((staff, index) => {
        const adjective = adjectivalFromLegend(staff.rating);
        let rowColor = index % 2 === 0 ? '#ffffff' : '#f8f9fa';
        
        if (adjective.toUpperCase() === 'OUTSTANDING') {
            rowColor = '#d2f4ddec';
        } else if (adjective.toUpperCase() === 'SATISFACTORY') {
            rowColor = '#ffcccc'; // light red
        } else if (adjective.toUpperCase() === 'UNSATISFACTORY') {
            rowColor = '#f29f5f'; // dark red
        }

        html += `
            <tr class="print-row" style="background-color: ${rowColor};">
                <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: left; font-size: 9pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${staff.name}</td>
                <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; color: #232527; font-size: 9pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${staff.rating.toFixed(2)}</td>
                <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-weight: bold; color: #080908; font-size: 9pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${adjective}</td>
                <td style="border: 1px solid #ddd; padding: 4px 6px; text-align: center; font-size: 9pt; font-family: 'Century Gothic', sans-serif; text-transform: uppercase;">${staff.department}</td>
            </tr>
        `;
    });

    html += `
                    </tbody>
                </table>
            </div>

            <!-- Signature Section update margintop-->
            <div style='margin-top:3em;margin-bottom:0em;text-align:left; font-size:9pt; line-height:1.3;'>
            <br>
                <div style='margin-bottom:1em;'>
                    Prepared by:
                </div>
                <div style='margin-bottom:0.1em;'>
                    <strong>DHINA B. DALISAY</strong>
                </div>
                <div style='margin-bottom:2em;'>
                    Guidance Advocate
                </div>

                <div style='display: flex; justify-content: space-between; align-items: flex-start;'>
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

                    <div style='text-align:left; padding-right: 1in;'>
                        <div style='display: flex; align-items: flex-start;'>
                            <div style='white-space: nowrap;'>Received by: &nbsp;</div>
                            <div style='display: flex; flex-direction: column;'>
                                <span>___________________________</span>
                                <strong style='margin-top: 0.1em;'>DR. FLORIPIS A. MONTECILLO</strong>
                                <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;College President</span>
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
            title: 'Report Generated!',
            text: `The overall ${title.toLowerCase()} report has been prepared for printing.`,
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

// Function to view comments (reused from staff-ratings)
function viewComments(staffId, staffName) {
    document.getElementById('staffNameInModal').textContent = staffName;
    
    // Show loading
    document.getElementById('commentsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading comments...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
    modal.show();
    
    // Fetch comments
    fetch(`/api/staff/${staffId}/comments`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let commentsHtml = '';
            if (data.comments && data.comments.length > 0) {
                data.comments.forEach(comment => {
                    commentsHtml += `
                        <div class="comment-item">
                            <div class="comment-header">
                                <strong>Anonymous Student</strong>
                                <span class="comment-date">${new Date(comment.created_at).toLocaleDateString()}</span>
                            </div>
                            <div class="comment-text mt-2">${comment.comments}</div>
                        </div>
                    `;
                });
            } else {
                commentsHtml = `
                    <div class="no-comments">
                        <i class="fas fa-comment-slash fa-3x mb-3"></i>
                        <h5>No Comments Available</h5>
                        <p>This staff member has not received any comments yet.</p>
                    </div>
                `;
            }
            document.getElementById('commentsContent').innerHTML = commentsHtml;
        } else {
            document.getElementById('commentsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading comments: ${data.message || 'Unknown error'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('commentsContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading comments. Please try again.
            </div>
        `;
    });
}

// Function to view staff profile (reused from staff-ratings)
function viewStaffProfile(staffId) {
    // Show loading
    document.getElementById('staffProfileContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading staff profile...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('staffProfileModal'));
    modal.show();
    
    // Fetch staff profile
    fetch(`/api/staff/${staffId}/profile`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('staffProfileContent').innerHTML = data.html;
        } else {
            document.getElementById('staffProfileContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading profile: ${data.message || 'Unknown error'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('staffProfileContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading profile. Please try again.
            </div>
        `;
    });
}
</script>