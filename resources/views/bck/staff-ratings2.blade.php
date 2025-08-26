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

<meta name="csrf-token" content="{{ csrf_token() }}">

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
        padding-left: 45px;
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
</style>

<div class="container-fluid py-4">
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
                <div class="card-header table-header d-flex justify-content-between align-items-center" style="color: #333; border-top-left-radius: 1rem; border-top-right-radius: 1rem; background: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line me-2 fa-lg text-primary"></i>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: 1px; text-shadow: 0 2px 8px rgba(0,0,0,0.04);">Staff Performance Dashboard</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <div class="search-box mb-0" style="flex:1 1 250px; min-width:220px;">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" onkeyup="searchStaff()" 
                                   placeholder="Search staff by name, department, or email..." 
                                   class="form-control">
                        </div>
                        <div style="min-width:200px;">
                            <select id="staffTypeFilter" class="form-select" onchange="filterStaffByType()">
                                <option value="">All Staff Types</option>
                                <option value="teaching">Teaching</option>
                                <option value="non-teaching">Non-Teaching</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced"
                                style="height:40px;font-weight:bold;font-size:1rem;" onclick="location.reload();">
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
                                @forelse($staffRatings as $staff)
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
                                                    onclick="viewComments({{ $staff->id }}, '{{ addslashes($staff->full_name) }}')"
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
                                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                                <h5>No Staff Ratings Available</h5>
                                                <p>No evaluations have been submitted yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    @php
                        $hasEvaluations = false;
                        foreach ($staffRatings as $staff) {
                            if ($staff->total_evaluations > 0) {
                                $hasEvaluations = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasEvaluations)
                    <button id="saveAllResultsBtn" class="btn btn-primary fw-bold rounded-pill save-all-btn">
                        <i class="fas fa-save me-2"></i>Save All Results
                    </button>
                    @endif
                </div>

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
<script>
    function searchStaff() {
        filterStaffByType(); // Integrate with filter
    }

    function filterStaffByType() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('staffTypeFilter').value;
        const rows = document.querySelectorAll('#staffTable tbody tr');
        rows.forEach(row => {
            // If this is the empty state row, always show if no results
            if (row.querySelector('td[colspan]')) {
                row.style.display = 'none';
                return;
            }
            const staffDetails = row.cells[1].textContent.toLowerCase();
            const staffType = row.cells[1].querySelector('.badge.bg-info')?.textContent.trim().toLowerCase() || '';
            const matchesSearch = staffDetails.includes(input);
            // Use strict equality for staff type filtering
            const matchesType = !type || staffType === type.toLowerCase();
            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
        // Show empty state if all rows are hidden
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
        const emptyRow = document.querySelector('#staffTable tbody tr td[colspan]')?.parentElement;
        if (emptyRow) {
            emptyRow.style.display = visibleRows.length === 0 ? '' : 'none';
        }
    }

    function viewComments(staffId, staffName) {
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
        fetch('{{ route("staff.comments") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: `staff_id=${staffId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComments(data.comments);
            } else {
                commentsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading comments: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            commentsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading comments. Please try again.
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
    <div class='legend-item'><span class='color range-bar-green'></span><span class='range-legend-label'>Excellent (4-5)</span></div>
    <div class='legend-item'><span class='color range-bar-blue'></span><span class='range-legend-label'>Good (3-4)</span></div>
    <div class='legend-item'><span class='color range-bar-yellow'></span><span class='range-legend-label'>Average (2-3)</span></div>
    <div class='legend-item'><span class='color range-bar-orange'></span><span class='range-legend-label'>Below Avg (1-2)</span></div>
    <div class='legend-item'><span class='color range-bar-red'></span><span class='range-legend-label'>Poor (&lt;1)</span></div>
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
                html += `<div class='mb-3'><div class='d-flex justify-content-between'><span>${category.title}</span><span class='fw-bold'>${avg.toFixed(2)}/5</span></div><div class='range-bar'><div class='range-bar-fill ${colorClass}' style='width:${percent}%;'></div></div></div>`;
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
        console.log('Starting printStaffReport for staffId:', staffId);
        
        // Show loading indicator
        Swal.fire({
            title: 'Loading...',
            text: 'Preparing staff report for printing',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        // Add CSRF token if available
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            console.log('CSRF token found and added to headers');
        } else {
            console.warn('CSRF token not found in meta tag');
        }
        
        console.log('Making request to:', `/staff/profile-ratings/${staffId}`);
        console.log('Request headers:', headers);
        
        // Fetch staff profile and ratings via AJAX (reuse profileRatingsAjax endpoint)
        fetch(`/staff/profile-ratings/${staffId}`, {
            method: 'GET',
            headers: headers
        })
            .then(response => {
                console.log('Response received:', response);
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    console.error('HTTP error! status:', response.status);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
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
                    
                    // Close loading indicator
                    Swal.close();
                    
                    // Open print window
                    const printWindow = window.open('', '', 'width=900,height=700');
                    printWindow.document.write('<html><head><title>Staff Evaluation Report</title>');
                    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(html);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => { printWindow.print(); printWindow.close(); }, 500);
                } else {
                    console.error('Server error:', data);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Loading Staff Report',
                        text: data.message || 'Unable to load staff report data.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to connect to the server. Please check your internet connection and try again.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const saveAllResultsBtn = document.getElementById('saveAllResultsBtn');
        if (saveAllResultsBtn) {
            saveAllResultsBtn.addEventListener('click', function(e) {
                // Check if questions table is empty before proceeding
                fetch('/admin/check-questions-empty', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.empty) {
                        // Show SweetAlert confirmation instead of modal
                        Swal.fire({
                            title: 'Save & Clear All Results?',
                            html: "<span style='font-size:1.1rem;'>This will save all evaluation results and <b>clear all entries</b> from the system.<br><br>Are you sure?</span>",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fas fa-save me-1"></i>Yes, Save & Clear!',
                            cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
                            reverseButtons: true,
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading alert
                                Swal.fire({
                                    title: 'Saving Results...',
                                    text: 'Please wait while we save all evaluation results.',
                                    icon: 'info',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                
                                // Make the save request
                                fetch('{{ route("evaluations.saveAndClearAll") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: ''
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success alert with auto-dismiss
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'All evaluations have been saved and entries cleared successfully!',
                                            icon: 'success',
                                            timer: 3000,
                                            showConfirmButton: false,
                                            timerProgressBar: true,
                                            showClass: {
                                                popup: 'animate__animated animate__fadeInDown'
                                            },
                                            hideClass: {
                                                popup: 'animate__animated animate__fadeOutUp'
                                            }
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'Error saving evaluations: ' + data.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#dc3545',
                                            showClass: {
                                                popup: 'animate__animated animate__fadeInDown'
                                            },
                                            hideClass: {
                                                popup: 'animate__animated animate__fadeOutUp'
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error saving evaluations. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#dc3545',
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                                    });
                                });
                            }
                        });
                    } else {
                        // Show SweetAlert warning
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please save first all the questions',
                            text: 'That means that if the questions are not still saved in academic year used, admin cannot save the evaluation results.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107',
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not check questions status. Please try again.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                });
            });
        }


    });

</script> 