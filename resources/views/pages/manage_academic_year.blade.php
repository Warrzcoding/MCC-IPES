<meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    /* Additional styles for ranking layout */
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }
    .section-title {
        font-size: 1.3em;
        font-weight: bold;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-count {
        background: rgba(255,255,255,0.2);
        padding: 0.2em 0.6em;
        border-radius: 15px;
        font-size: 0.8em;
    }
    .staff-list {
        max-height: 600px;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    .staff-item {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .staff-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-left-color: #667eea;
    }
    .staff-rank {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9em;
        margin-right: 15px;
        flex-shrink: 0;
        position: relative;
    }
    .staff-rank.top-3 {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    }
    .staff-rank.rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
    }
    .staff-rank.rank-2 {
        background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
        box-shadow: 0 0 10px rgba(192, 192, 192, 0.4);
    }
    .staff-rank.rank-3 {
        background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%);
        box-shadow: 0 0 10px rgba(205, 127, 50, 0.4);
    }
    .rank-label {
        position: absolute;
        top: -8px;
        right: -25px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        font-size: 0.6em;
        padding: 2px 6px;
        border-radius: 10px;
        white-space: nowrap;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    }
    .staff-name {
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
        font-size: 1.1em;
    }
    .staff-info-line {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .staff-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 0;
    }
    .staff-badges .badge {
        font-size: 0.75em;
        padding: 0.3em 0.7em;
        border-radius: 20px;
        font-weight: 600;
    }
    .rating-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 0;
        flex-wrap: nowrap;
        justify-content: space-between;
    }
    .rating-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
        margin-top: 0;
        margin-left: auto;
    }
    .action-buttons .btn {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
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
    .rating-number {
        font-size: 1.3em;
        font-weight: bold;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        white-space: nowrap;
    }
    .rating-stars {
        color: #ffc107;
        font-size: 1em;
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .rating-badge {
        font-size: 0.75em;
        padding: 0.3em 0.6em;
        border-radius: 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }
    
    /* Animation styles for Saved Evaluation Results header */
    .evaluation-header::before {
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
    
    .evaluation-header::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
        pointer-events: none;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: scale(1.1) translateY(0); }
        40% { transform: scale(1.1) translateY(-8px); }
        60% { transform: scale(1.1) translateY(-4px); }
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
            flex-wrap: nowrap;
        }
        .rating-info {
            gap: 6px;
        }
        .section-title {
            font-size: 1.1em;
        }
        .action-buttons {
            gap: 5px;
        }
        .action-buttons .btn {
            font-size: 0.75em;
            padding: 0.3em 0.6em;
        }
    }
    
    /* Print styles */
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
        
        /* Ensure proper table formatting */
        #customPrintArea table {
            page-break-inside: avoid !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        #customPrintArea tr {
            page-break-inside: avoid !important;
        }
        
        #customPrintArea th, #customPrintArea td {
            border: 1px solid #333 !important;
            padding: 12px !important;
            vertical-align: top !important;
        }
        
        #customPrintArea th {
            background: #f8f9fa !important;
            font-weight: bold !important;
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
        @if($staffEvaluations->isEmpty())
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-bar me-2"></i>Saved Evaluation Results for {{ $year->year }}
                </div>
                <div class="card-body">
                    <p class="text-muted text-center">No saved evaluation results found for this academic year.</p>
                </div>
                <div class="mt-3 text-start">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
                </div>
            </div>
        @else
            @php
                // Separate and sort staff by type and rating
                $teachingStaff = collect();
                $nonTeachingStaff = collect();
                
                foreach($staffEvaluations as $staff) {
                    $staffModel = \App\Models\Staff::find($staff->staff_id);
                    if ($staffModel) {
                        $staff->staffModel = $staffModel;
                        $staff->image_url = $staffModel->image_path && file_exists(public_path($staffModel->image_path)) ? asset($staffModel->image_path) : null;
                        $staff->staffFullName = $staffModel->full_name;
                        
                        if ($staffModel->staff_type === 'teaching') {
                            $teachingStaff->push($staff);
                        } else {
                            $nonTeachingStaff->push($staff);
                        }
                    }
                }
                
                // Sort by average_rating descending (highest to lowest)
                $teachingStaff = $teachingStaff->sortByDesc('average_rating')->values();
                $nonTeachingStaff = $nonTeachingStaff->sortByDesc('average_rating')->values();
            @endphp
            
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header evaluation-header text-white text-center" style="background: linear-gradient(145deg, #1e3c72 0%, #2a5298 50%, #667eea 100%); border-radius: 20px; padding: 1.5rem 2rem; position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(30, 60, 114, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2), inset 0 -1px 0 rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h4 class="mb-0" style="font-size: 2.5em; font-weight: 800; color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.3), 0 4px 8px rgba(0,0,0,0.2), 0 0 20px rgba(255,255,255,0.1); letter-spacing: -0.5px; line-height: 1.1; position: relative; z-index: 2;">
                        <i class="fas fa-trophy me-2" style="color: #ffd700; filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.6)); transform: scale(1.1); display: inline-block; animation: bounce 2s ease-in-out infinite;"></i>Staff Performance Rankings for {{ $year->year }}
                    </h4>
                    <p class="mb-0 mt-2" style="font-size: 1.3em; opacity: 0.9; text-shadow: 0 1px 3px rgba(0,0,0,0.3); font-weight: 400; color: rgba(255, 255, 255, 0.95); line-height: 1.4; position: relative; z-index: 2;">Evaluation results ranked from highest to lowest performance</p>
                </div>
            </div>

            <div class="row">
                <!-- Teaching Staff Column (Left) -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Teaching Staff Ranking</span>
                                <span class="section-count">{{ $teachingStaff->count() }}</span>
                            </div>
                        </div>
                        <div class="staff-list">
                            @if($teachingStaff->count() > 0)
                                @foreach($teachingStaff as $index => $staff)
                                    @php 
                                        $ratingInfo = getRatingStatus($staff->average_rating);
                                        $starRating = round($staff->average_rating);
                                        $teachingRank = $index + 1;
                                    @endphp
                                    <div class="staff-item">
                                        <div class="d-flex align-items-start">
                                            <div class="staff-rank {{ $teachingRank <= 3 ? 'top-3' : '' }} {{ $teachingRank == 1 ? 'rank-1' : '' }} {{ $teachingRank == 2 ? 'rank-2' : '' }} {{ $teachingRank == 3 ? 'rank-3' : '' }}">
                                                {{ $teachingRank }}
                                                <span class="rank-label {{ $teachingRank == 1 ? 'rank-1-label' : '' }} {{ $teachingRank == 2 ? 'rank-2-label' : '' }} {{ $teachingRank == 3 ? 'rank-3-label' : '' }}">
                                                    Rank {{ $teachingRank }}
                                                </span>
                                            </div>
                                            
                                            <div class="me-3">
                                                @if($staff->image_url)
                                                    <img src="{{ $staff->image_url }}" 
                                                         alt="{{ $staff->staffFullName }}" 
                                                         class="staff-image">
                                                @else
                                                    <div class="default-avatar">
                                                        {{ strtoupper(substr($staff->staffFullName, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="staff-details">
                                                <div class="staff-name">{{ $staff->staffFullName }}</div>
                                                <div class="staff-info-line">
                                                    <div class="staff-badges">
                                                        <span class="badge bg-secondary">{{ $staff->staffModel->department }}</span>
                                                        <span class="badge bg-primary">Teaching</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="rating-section">
                                                    <div class="rating-info">
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
                                                    
                                                    <div class="action-buttons">
                                                        <button class="btn btn-outline-primary btn-sm" 
                                                                onclick="viewComments({{ $staff->staffModel->id }}, '{{ addslashes($staff->staffFullName) }}')"
                                                                {{ $staff->total_comments == 0 ? 'disabled' : '' }}
                                                                title="View Comments">
                                                            <i class="fas fa-comments"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success btn-sm" 
                                                                onclick="showPrintConfirmModal({{ $staff->staffModel->id }})"
                                                                title="Print Report">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-staff-message">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <h5>No Teaching Staff Evaluations</h5>
                                    <p>No evaluations have been submitted for teaching staff yet.</p>
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
                                <span class="section-count">{{ $nonTeachingStaff->count() }}</span>
                            </div>
                        </div>
                        <div class="staff-list">
                            @if($nonTeachingStaff->count() > 0)
                                @foreach($nonTeachingStaff as $index => $staff)
                                    @php 
                                        $ratingInfo = getRatingStatus($staff->average_rating);
                                        $starRating = round($staff->average_rating);
                                        $nonTeachingRank = $index + 1;
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
                                                @if($staff->image_url)
                                                    <img src="{{ $staff->image_url }}" 
                                                         alt="{{ $staff->staffFullName }}" 
                                                         class="staff-image">
                                                @else
                                                    <div class="default-avatar">
                                                        {{ strtoupper(substr($staff->staffFullName, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="staff-details">
                                                <div class="staff-name">{{ $staff->staffFullName }}</div>
                                                <div class="staff-info-line">
                                                    <div class="staff-badges">
                                                        <span class="badge bg-secondary">{{ $staff->staffModel->department }}</span>
                                                        <span class="badge bg-info">Non-Teaching</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="rating-section">
                                                    <div class="rating-info">
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
                                                    
                                                    <div class="action-buttons">
                                                        <button class="btn btn-outline-primary btn-sm" 
                                                                onclick="viewComments({{ $staff->staffModel->id }}, '{{ addslashes($staff->staffFullName) }}')"
                                                                {{ $staff->total_comments == 0 ? 'disabled' : '' }}
                                                                title="View Comments">
                                                            <i class="fas fa-comments"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success btn-sm" 
                                                                onclick="showPrintConfirmModal({{ $staff->staffModel->id }})"
                                                                title="Print Report">
                                                            <i class="fas fa-print"></i>
                                                        </button>
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
            
            <!-- Back Button -->
            <div class="row">
                <div class="col-12">
                    <div class="mt-3 text-start">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
                    </div>
                </div>
            </div>
        @endif
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

    // Print functionality
    function showPrintConfirmModal(staffId) {
        Swal.fire({
            title: 'Confirm Print',
            text: 'Are you sure you want to print the evaluation report for this staff member?',
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
        fetch(`/academic-year/detailed-evaluations/${staffId}/{{ $year->id }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    const evaluations = data.evaluations;
                    
                    // Build the detailed report HTML
                    let html = `
                        <div style="padding: 32px; font-family: 'Times New Roman', serif; font-size: 12pt; max-width: 800px; margin: 0 auto;">
                            <div style="text-align: center; margin-bottom: 0.5em; padding-bottom: 0.5em;">
                                <div style='display:flex;align-items:center;justify-content:center;margin-bottom:1em;'>
                                    <img src='/images/mcclogo.png' alt='Left Logo' style='width:70px;height:70px;margin-right:3em;' onerror='this.style.display="none"'>
                                    <div style='text-align:center;'>
                                        <h2 style='margin:0;font-size:11pt;line-height:1.3;font-family:"Times New Roman", serif;'>Republic of the Philippines<br>
                                        Region VII, Central Visayas<br>
                                        Commission on Higher Education<br>
                                        <strong>Madridejos Community College</strong><br>
                                        Crossing Bunakan, Madridejos, Cebu<br>
                                        <br>
                                        <strong>Center For Guidance Services</strong><br>
                                        <strong style='font-size:11pt;'>Staff Evaluation Report</strong><br>
                                        Academic Year: {{ $year->year }}</h2>
                                    </div>
                                    <img src='/images/madlogo.png' alt='Right Logo' style='width:70px;height:70px;margin-left:3em;' onerror='this.style.display="none"'>
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
                                    This staff member has not been evaluated yet for this academic year.
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
                    // Clean up any existing print areas more thoroughly
                    const existingAreas = document.querySelectorAll('#customPrintArea');
                    existingAreas.forEach(area => area.remove());
                    
                    const printArea = document.createElement('div');
                    printArea.id = 'customPrintArea';
                    printArea.innerHTML = html;
                    printArea.style.cssText = 'position: absolute; left: -9999px; top: -9999px; visibility: hidden;';
                    document.body.appendChild(printArea);

                    // Clear document title to minimize header text
                    const originalTitle = document.title;
                    document.title = '';

                    const onAfterPrint = () => {
                        window.removeEventListener('afterprint', onAfterPrint);
                        // Clean up all print areas thoroughly
                        const printAreas = document.querySelectorAll('#customPrintArea');
                        printAreas.forEach(area => area.remove());
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
    </script>

<!-- HTML2PDF CDN for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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