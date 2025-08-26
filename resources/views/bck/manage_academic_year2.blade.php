@if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
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
        });
    });
    </script>
@endif

@if(session('message') && session('message_type') == 'success')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: '{{ session('message') }}',
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
        });
    });
    </script>
@endif

@if(session('message') && session('message_type') == 'warning')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Warning!',
            text: '{{ session('message') }}',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    });
    </script>
@endif

@if(session('message') && session('message_type') == 'danger')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Error!',
            text: '{{ session('message') }}',
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
    </script>
@endif

@if(session('message') && !session('message_type'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Information',
            text: '{{ session('message') }}',
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#17a2b8',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    });
    </script>
@endif

@if($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Validation Error!',
            html: `<ul class="text-start">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>`,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
    });
    </script>
@endif

@php
if (!function_exists('getRatingStatus')) {
    function getRatingStatus($rating) {
        if ($rating >= 4) return ['status' => 'Excellent', 'color' => '#28a745', 'bg' => '#d4edda'];
        if ($rating >= 3) return ['status' => 'Good', 'color' => '#17a2b8', 'bg' => '#d1ecf1'];
        if ($rating >= 2) return ['status' => 'Average', 'color' => '#ffc107', 'bg' => '#fff3cd'];
        if ($rating >= 1) return ['status' => 'Below Average', 'color' => '#fd7e14', 'bg' => '#ffeaa7'];
        return ['status' => 'Poor', 'color' => '#dc3545', 'bg' => '#f8d7da'];
    }
}
@endphp
<style>
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
    .comment-block.card {
        border-left: 4px solid #007bff;
        border-radius: 0.5rem;
        background: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .comment-block .comment-date {
        font-size: 0.92em;
        color: #888;
        margin-top: 2px;
    }
    .range-bar {
        width: 100%;
        height: 12px;
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
    .range-bar-green { background: #28a745 !important; }
    .range-bar-blue { background: #007bff !important; }
    .range-bar-yellow { background: #ffc107 !important; }
    .range-bar-orange { background: #fd7e14 !important; }
    .range-bar-red { background: #dc3545 !important; }
    
    /* More specific selectors */
    .range-bar-fill.range-bar-green { background-color: #28a745 !important; }
    .range-bar-fill.range-bar-blue { background-color: #007bff !important; }
    .range-bar-fill.range-bar-yellow { background-color: #ffc107 !important; }
    .range-bar-fill.range-bar-orange { background-color: #fd7e14 !important; }
    .range-bar-fill.range-bar-red { background-color: #dc3545 !important; }
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
    #staffProfileModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #fff !important;
    }
    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
</style>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-0">
            <i class="fas fa-cogs me-2"></i>Manage Academic Year: <span class="fw-bold">{{ $year->year }}</span>
        </h4>
    </div>
</div>
<ul class="nav nav-tabs mb-3" id="manageTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab" aria-controls="questions" aria-selected="true">Saved Questions</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="evaluations-tab" data-bs-toggle="tab" data-bs-target="#evaluations" type="button" role="tab" aria-controls="evaluations" aria-selected="false">Saved Evaluation Results</button>
    </li>
</ul>
<div class="tab-content" id="manageTabsContent">
    <!-- Saved Questions Tab -->
    <div class="tab-pane fade show active" id="questions" role="tabpanel" aria-labelledby="questions-tab">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-question-circle me-2"></i>Saved Questions for {{ $year->year }}
            </div>
            <div class="card-body">
                @if($savedQuestions->isEmpty())
                    <p class="text-muted text-center">No saved questions found for this academic year.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Question</th>
                                    <th>Staff Type</th>
                                    <th>Response Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($savedQuestions as $q)
                                    <tr>
                                        <td><strong>{{ $q->title }}</strong></td>
                                        <td class="text-muted">{{ $q->description }}</td>
                                        <td>
                                            <span class="badge {{ $q->staff_type == 'teaching' ? 'bg-primary' : 'bg-success' }}">
                                                {{ ucfirst($q->staff_type) }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ $q->response_type }}</small></td>
                                        <td>
                                            <form method="POST" action="{{ route('question.reuseSaved') }}" style="display:inline;" class="reuse-question-form">
                                                @csrf
                                                <input type="hidden" name="saved_question_id" value="{{ $q->id }}">
                                                <input type="hidden" name="academic_year_id" value="{{ $year->id }}">
                                                <button class="btn btn-sm btn-outline-info" type="submit" title="Reuse this question">
                                                    <i class="fas fa-play"></i> Use Questionnaires
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <form method="POST" action="{{ route('question.reuseAllSaved') }}" style="display:inline;" class="reuse-all-questions-form">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $year->id }}">
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-clone"></i> Reuse All Saved Questions
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="mt-3 text-start">
                <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>
    <!-- Saved Evaluation Results Tab -->
    <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-chart-bar me-2"></i>Saved Evaluation Results for {{ $year->year }}
            </div>
            <div class="card-body">
                @if($staffEvaluations->isEmpty())
                    <p class="text-muted text-center">No saved evaluation results found for this academic year.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover" id="staffEvalTable">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-center">Photo</th>
                                    <th>Staff Details</th>
                                    <th class="text-center">Rating</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffEvaluations as $staff)
                                    @php 
                                        $ratingInfo = getRatingStatus($staff->average_rating);
                                        $starRating = round($staff->average_rating);
                                    @endphp
                                    @php 
                                        // Fetch staff model by id (from save_eval_result.staff_id)
                                        $staffModel = \App\Models\Staff::find($staff->staff_id);
                                        $image_url = $staffModel && $staffModel->image_path && file_exists(public_path($staffModel->image_path)) ? asset($staffModel->image_path) : null;
                                        $staff_id = $staffModel ? $staffModel->id : null;
                                        $staffFullName = $staffModel ? $staffModel->full_name : 'Unknown Staff';
                                    @endphp
                                    <tr class="rating-card">
                                        <td class="text-center align-middle">
                                            @if($image_url)
                                                <img src="{{ $image_url }}" alt="{{ $staffFullName }}" class="staff-image">
                                            @else
                                                <div class="default-avatar">
                                                    {{ strtoupper(substr($staffFullName, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($staffModel)
                                                <div>
                                                    <strong class="text-primary">{{ $staffModel->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-id-badge me-1"></i>{{ $staffModel->staff_id }}
                                                    </small>
                                                    <br>
                                                    <span class="badge bg-secondary">{{ $staffModel->department }}</span>
                                                    <span class="badge bg-info">{{ $staffModel->staff_type }}</span>
                                                </div>
                                            @else
                                                <div class="text-danger">Staff not found</div>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="rating-number" style="color: {{ $ratingInfo['color'] }}">
                                                {{ round($staff->average_rating, 2) }}/5
                                            </div>
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star" style="color: {{ $i <= $starRating ? '#FFD700' : '#e4e5e9' }};"></i>
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
                                            <button class="btn btn-outline-primary action-btn" 
                                                    onclick="viewComments({{ $staff_id }}, '{{ addslashes($staffFullName) }}')"
                                                    {{ $staff->total_comments == 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-comments me-1"></i>
                                                View Comments
                                            </button>
                                            <button class="btn btn-outline-success action-btn ms-1" 
                                                    onclick="viewStaffProfile({{ $staff_id }})">
                                                <i class="fas fa-user-circle me-1"></i>
                                                View Profile & Ratings
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="mt-3 text-start">
                <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header table-header text-white">
                    <h5 class="modal-title" id="commentsModalLabel">
                        <i class="fas fa-comments me-2"></i><span id="commentsStaffName">Staff Comments</span>
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
</div>

    <script>
    function viewComments(staffId, staffName) {
        const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
        const modalTitle = document.getElementById('commentsModalLabel');
        const commentsContent = document.getElementById('commentsContent');
        
        modalTitle.innerHTML = `<i class='fas fa-comments me-2'></i>Comments for <span class='fw-bold'>${staffName}</span>`;
        commentsContent.innerHTML = `
            <div class='text-center'>
                <div class='spinner-border text-primary' role='status'>
                    <span class='visually-hidden'>Loading...</span>
                </div>
            </div>
        `;
        modal.show();
        fetchComments(staffId, staffName);
    }

    function fetchComments(staffId, staffName) {
        fetch('{{ route("academic_year.staff_comments") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: `staff_id=${staffId}&academic_year_id={{ $year->id }}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComments(data.comments, staffName);
            } else {
                document.getElementById('commentsContent').innerHTML = `
                    <div class='alert alert-danger'>
                        <i class='fas fa-exclamation-triangle me-2'></i>
                        Error loading comments: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('commentsContent').innerHTML = `
                <div class='alert alert-danger'>
                    <i class='fas fa-exclamation-triangle me-2'></i>
                    Error loading comments. Please try again.
                </div>
            `;
        });
    }

    function displayComments(comments, staffName) {
        const commentsContent = document.getElementById('commentsContent');
        
        if (comments.length === 0) {
            commentsContent.innerHTML = `
                <div class='no-comments'>
                    <i class='fas fa-comment-slash fa-3x mb-3 text-muted'></i>
                    <h5>No Comments Available</h5>
                    <p>This staff member has no comments yet.</p>
                </div>
            `;
            return;
        }
        
        let html = ``;
        comments.forEach(comment => {
            html += `
                <div class='comment-block card shadow-sm mb-3 p-3'>
                    <div class='comment-text mb-2 fs-6'>${comment.comments}</div>
                    <div class='comment-date text-muted small' style='font-size:0.92em;'>
                        <i class='fas fa-clock me-1'></i>
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
        // Fetch profile and ratings via AJAX (use academic-year/profile-ratings/{staffId}/{yearId})
        fetch('/academic-year/profile-ratings/' + staffId + '/{{ $year->id }}')
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
        // getRatingStatus logic (same as PHP)
        function getRatingStatus(rating) {
            if (rating >= 4) return { status: 'Excellent', color: '#28a745', bg: '#d4edda' };
            if (rating >= 3) return { status: 'Good', color: '#17a2b8', bg: '#d1ecf1' };
            if (rating >= 2) return { status: 'Average', color: '#ffc107', bg: '#fff3cd' };
            if (rating >= 1) return { status: 'Below Average', color: '#fd7e14', bg: '#ffeaa7' };
            return { status: 'Poor', color: '#dc3545', bg: '#f8d7da' };
        }
        let html = `<div class='row mb-4'>
            <div class='col-md-3 text-center'>`;
        if (staff.image_path && staff.image_path.trim() !== '') {
            html += `<img src='/${staff.image_path}' class='staff-image mb-2' style='width:80px;height:80px;'>`;
        } else {
            html += `<div class='default-avatar mb-2' style='width:80px;height:80px;font-size:2rem;'>${staff.full_name.charAt(0).toUpperCase()}</div>`;
        }
        html += `<div><strong>${staff.full_name}</strong><br><span class='badge bg-secondary'>${staff.department}</span><br><span class='badge bg-info'>${staff.staff_type}</span><br><small class='text-muted'>${staff.email}</small></div></div>`;
        html += `<div class='col-md-9'><h6 class='fw-bold mb-3'>Category Ratings</h6>`;
        // LEGEND
        html += `<div class='range-legend mb-2'>
    <div class='legend-item'><span class='color range-bar-green'></span><span class='range-legend-label'>Excellent (4-5)</span></div>
    <div class='legend-item'><span class='color range-bar-blue'></span><span class='range-legend-label'>Good (3-4)</span></div>
    <div class='legend-item'><span class='color range-bar-yellow'></span><span class='range-legend-label'>Average (2-3)</span></div>
    <div class='legend-item'><span class='color range-bar-orange'></span><span class='range-legend-label'>Below Avg (1-2)</span></div>
    <div class='legend-item'><span class='color range-bar-red'></span><span class='range-legend-label'>Poor (&lt;1)</span></div>
</div>`;

        // PER-CATEGORY
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
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.reuse-question-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Reuse this saved question?',
            text: 'Are you sure you want to reuse this saved question?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reuse it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
document.querySelectorAll('.reuse-all-questions-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Reuse ALL saved questions?',
            text: 'Are you sure you want to reuse ALL saved questions?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reuse all!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<script> 