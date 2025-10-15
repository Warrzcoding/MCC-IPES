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
</style>

<div class="container-fluid page-full-width py-4">
    <!-- Overall Header -->
    <div class="overall-header">
        <div class="overall-title">
            <i class="fas fa-trophy me-3"></i>Overall Staff Performance Rankings
        </div>
        <p class="overall-subtitle">
            Comprehensive evaluation results for all staff members, ranked by performance
        </p>
    </div>

    <div class="row">
        <!-- Instructors Column (Left) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instructors Ranking</span>
                        @php 
                            $instructors = isset($teachingStaffRatings) ? $teachingStaffRatings : collect();
                        @endphp
                        <span class="section-count">{{ $instructors->count() }}</span>
                    </div>
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
                                                <div class="rating-number" style="color: {{ $ratingInfo['color'] }}">
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
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-users-cog"></i>
                        <span>Non-Teaching Staff Ranking</span>
                        @php 
                            $nonTeachingStaff = isset($nonTeachingStaffRatings) ? $nonTeachingStaffRatings : collect();
                        @endphp
                        <span class="section-count">{{ $nonTeachingStaff->count() }}</span>
                    </div>
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
                                                <div class="rating-number" style="color: {{ $ratingInfo['color'] }}">
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