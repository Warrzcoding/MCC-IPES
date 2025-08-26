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

@if(session('message'))
    <div id="successNotification" class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">
        <div class="card border-0 shadow-lg" style="min-width: 350px; max-width: 450px;">
            <div class="card-header bg-success text-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div id="notificationIcon" class="me-3" style="font-size: 1.5rem;">
                            @if(session('message_type') == 'success')
                                <i class="fas fa-spinner fa-spin"></i>
                            @else
                                <i class="fas fa-info-circle"></i>
                            @endif
                        </div>
                        <strong class="fs-5">
                            @if(session('message_type') == 'success')
                                Processing...
                            @else
                                {{ ucfirst(session('message_type', 'info')) }}
                            @endif
                        </strong>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
            </div>
            <div class="card-body py-4 text-center">
                <div id="notificationText" class="fs-6 mb-0">{{ session('message') }}</div>
            </div>
        </div>
    </div>
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
                                                    onclick="printStaffReport({{ $staff->id }})">
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
                    <button id="saveAllResultsBtn" class="btn btn-primary fw-bold rounded-pill save-all-btn">
                        <i class="fas fa-save me-2"></i>Save All Results
                    </button>
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
@if(session('message') && session('message_type') == 'success')
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('successNotification');
    const icon = document.getElementById('notificationIcon');
    const text = document.getElementById('notificationText');
    const header = notification.querySelector('.card-header strong');
    
    if (notification) {
        // Add entrance animation
        notification.style.opacity = '0';
        notification.style.transform = 'translate(-50%, -50%) scale(0.8)';
        notification.style.transition = 'all 0.3s ease-out';
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translate(-50%, -50%) scale(1)';
        }, 100);
        
        // Show spinner for 2 seconds
        setTimeout(function() {
            // Add success animation
            icon.style.transition = 'all 0.3s ease-out';
            icon.style.transform = 'scale(1.2)';
            
            setTimeout(() => {
                // Change icon to check mark with animation
                icon.innerHTML = '<i class=\"fas fa-check-circle text-success\" style=\"font-size: 2rem;\"></i>';
                icon.style.transform = 'scale(1)';
                
                // Change header text
                header.textContent = 'Success!';
                header.style.transition = 'all 0.3s ease-out';
                
                // Update message text based on the action
                const message = text.textContent;
                if (message.toLowerCase().includes('added')) {
                    text.textContent = 'Successfully added!';
                } else if (message.toLowerCase().includes('updated')) {
                    text.textContent = 'Successfully updated!';
                } else if (message.toLowerCase().includes('deleted')) {
                    text.textContent = 'Successfully deleted!';
                } else if (message.toLowerCase().includes('saved')) {
                    text.textContent = 'Results successfully saved!';
                } else {
                    text.textContent = 'Operation completed successfully!';
                }
                
                // Add success pulse effect
                notification.style.boxShadow = '0 0 30px rgba(40, 167, 69, 0.3)';
                
                // Auto-hide after 4 seconds with exit animation
                setTimeout(function() {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translate(-50%, -50%) scale(0.8)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 4000);
                
            }, 300);
            
        }, 2000);
    }
});
@endif

document.addEventListener('DOMContentLoaded', function() {
    const saveAllResultsBtn = document.getElementById('saveAllResultsBtn');
    if (saveAllResultsBtn) {
        saveAllResultsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Save & Clear All Results?',
                html: "<span style='font-size:1.1rem;'>This will save all current evaluations and <b>clear all entries</b> from the list.<br><br>Are you sure?</span>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class=\"fas fa-save me-1\"></i>Yes, Save & Clear!',
                cancelButtonText: '<i class=\"fas fa-times me-1\"></i>Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    title: 'fw-bold',
                    confirmButton: 'px-4 py-2',
                    cancelButton: 'px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trigger the original save logic
                    saveAllResultsBtn.disabled = true;
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
                        saveAllResultsBtn.disabled = false;
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'All evaluations have been saved and entries cleared successfully!',
                                confirmButtonColor: '#28a745',
                                customClass: { popup: 'rounded-4 shadow-lg', title: 'fw-bold', confirmButton: 'px-4 py-2' }
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving evaluations: ' + data.message,
                                confirmButtonColor: '#dc3545',
                                customClass: { popup: 'rounded-4 shadow-lg', title: 'fw-bold', confirmButton: 'px-4 py-2' }
                            });
                        }
                    })
                    .catch(error => {
                        saveAllResultsBtn.disabled = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving evaluations. Please try again.',
                            confirmButtonColor: '#dc3545',
                            customClass: { popup: 'rounded-4 shadow-lg', title: 'fw-bold', confirmButton: 'px-4 py-2' }
                        });
                    });
                }
            });
        });
    }
});
</script> 