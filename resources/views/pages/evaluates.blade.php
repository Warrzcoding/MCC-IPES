@php
    $teachingEvaluated = $teachingEvaluated ?? 0;
    $nonTeachingEvaluated = $nonTeachingEvaluated ?? 0;
    $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
    $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();
    $evaluations = \App\Models\Evaluation::where('user_id', auth()->id())->get();
    $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
    $teachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'teaching')->count();
    $nonTeachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'non-teaching')->count();
    
    // Calculate completion status
    $totalAvailableTeaching = isset($teachingStaff) ? $teachingStaff->count() : 0;
    $totalAvailableNonTeaching = isset($nonTeachingStaff) ? $nonTeachingStaff->count() : 0;
    $totalAvailableStaff = $totalAvailableTeaching + $totalAvailableNonTeaching;
    $totalEvaluatedStaff = $teachingCount + $nonTeachingCount;
    
    // Check if all evaluations are completed
    $allEvaluationsCompleted = ($totalAvailableStaff > 0) && ($totalEvaluatedStaff >= $totalAvailableStaff);
@endphp

<style>
/* Enhanced Tab Navigation Styles */
.custom-nav-tabs {
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 8px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}
.custom-nav-tabs::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}
.custom-nav-tabs .nav-item {
    flex: 1;
    position: relative;
    z-index: 2;
}
.custom-nav-tabs .nav-link {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 1.1rem;
    padding: 15px 25px;
    border-radius: 10px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    backdrop-filter: blur(10px);
}
.custom-nav-tabs .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s;
}
.custom-nav-tabs .nav-link:hover::before {
    left: 100%;
}
.custom-nav-tabs .nav-link:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}
.custom-nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    color: #4a5568;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transform: translateY(-3px) scale(1.02);
}
.custom-nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}
.tab-icon {
    font-size: 1.3rem;
    transition: transform 0.3s ease;
}
.custom-nav-tabs .nav-link.active .tab-icon {
    transform: rotate(360deg) scale(1.1);
    color: #667eea;
}
.tab-content {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}
.tab-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.03"/><circle cx="90" cy="40" r="0.5" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}
.tab-pane {
    position: relative;
    z-index: 1;
}
.section-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 25px;
    position: relative;
    padding-left: 20px;
}
.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
}
.enhanced-select {
    background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 20px;
    font-size: 1rem;
    color: #4a5568;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    appearance: none;
    cursor: pointer;
}
.enhanced-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}
.enhanced-select:disabled {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    color: #a0aec0;
    cursor: not-allowed;
    opacity: 0.7;
}
.enhanced-select-wrapper::after {
    content: '▼';
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 0.9rem;
    pointer-events: none;
    transition: transform 0.3s ease;
}
.enhanced-select:focus + .enhanced-select-wrapper::after,
.enhanced-select-wrapper:hover::after {
    transform: translateY(-50%) rotate(180deg);
}
.status-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}
.enhanced-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}
.enhanced-status-badge.status-open {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}
.enhanced-status-badge.status-closed {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    color: white;
}
.enhanced-status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}
@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}
.tab-pane {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.tab-pane.show.active {
    opacity: 1;
    transform: translateY(0);
}
.form-check-label.selected-rating {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    transform: scale(1.05);
    transition: all 0.3s ease;
}
.form-check-label {
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    margin-bottom: 0;
}
.form-check-label:hover {
    background: #f7fafc;
    transform: translateY(-1px);
}
.form-check-input {
    margin-right: 4px;
    width: 0.9em;
    height: 0.9em;
}
.evaluation-form-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
}
.evaluation-form-inner {
    max-width: 600px;
    width: 100%;
}
.evaluated-badge-red {
    background: linear-gradient(135deg, #ff4d4f 0%, #ff7875 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(255, 77, 79, 0.25);
    border: 2px solid #fff;
    font-weight: bold;
    font-size: 1.2rem;
    width: 2.4em;
    height: 2.4em;
    min-width: 2.4em;
    min-height: 2.4em;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: transform 0.2s;
    padding: 0;
}
.evaluated-badge-red:hover {
    transform: scale(1.08) rotate(-3deg);
    box-shadow: 0 8px 30px rgba(255, 77, 79, 0.35);
}

/* Enhanced popover content */
.evaluated-popover-content {
    font-size: 1.08rem;
    color: #333;
    min-width: 180px;
    padding: 0.5em 0.2em;
}
.evaluated-popover-content strong {
    color: #ff4d4f;
}
.evaluated-popover-label {
    display: flex;
    align-items: center;
    gap: 0.5em;
    margin-bottom: 0.3em;
}
.evaluated-popover-label i {
    color: #ff7875;
    font-size: 1.1em;
}
@media (max-width: 576px) {
    .response-options-group {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.5rem !important;
        justify-content: flex-start !important;
    }
    .response-options-group .form-check-label {
        width: 100%;
        text-align: left;
        justify-content: flex-start;
    }
    
    /* Mobile tab alignment - keep tabs horizontal */
    .custom-nav-tabs {
        flex-direction: row !important;
        padding: 6px;
    }
    .custom-nav-tabs .nav-item {
        flex: 1;
        min-width: 0;
    }
    .custom-nav-tabs .nav-link {
        padding: 12px 8px;
        font-size: 0.9rem;
        text-align: center;
    }
    .tab-icon {
        font-size: 1.1rem;
    }
    
    /* Mobile header alignment */
    .card-header {
        text-align: center;
    }
    
    /* Mobile status and badge container - keep horizontal */
    .card-header .d-flex.justify-content-center.align-items-center.gap-4 {
        flex-direction: row !important;
        gap: 15px !important;
        justify-content: center !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }
    
    /* Ensure badge and status stay horizontal */
    .card-header .d-flex.justify-content-center.align-items-center.gap-4 > * {
        flex-shrink: 0;
    }
    
    /* Mobile badge styling */
    #evaluatedStaffBadge {
        order: 1;
    }
    
    /* Mobile status badge styling */
    .enhanced-status-badge {
        order: 2;
        font-size: 0.9rem !important;
        padding: 10px 20px !important;
    }
    
    /* Mobile completion overlay styles */
    .completion-modal {
        padding: 1.5rem !important;
        margin: 1rem 0 !important;
        max-width: 95% !important;
        max-height: 85vh !important;
    }
    
    .completion-title {
        font-size: 1.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .completion-message {
        font-size: 1rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .completion-icon i {
        font-size: 2.5rem !important;
    }
    
    .completion-stats .row .col-6 h4 {
        font-size: 1.2rem !important;
    }
    
    .completion-stats .row .col-6 small {
        font-size: 0.8rem !important;
    }
    
    .completion-stats .text-center h3 {
        font-size: 1.5rem !important;
    }
}

/* Additional mobile fix with higher specificity */
@media screen and (max-width: 576px) {
    .card-header > div.d-flex.justify-content-center.align-items-center.gap-4.flex-wrap {
        display: flex !important;
        flex-direction: row !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 15px !important;
        flex-wrap: wrap !important;
    }
}
.privacy-reminder-box {
    background: #e3f0ff;
    border: 1px solid #b6d4fe;
    color: #084298;
    border-radius: 8px;
    padding: 1.5em 1em;
    margin-bottom: 1.5em;
}
.hide {
    display: none !important;
}
.response-options-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 0.5rem !important;
    justify-content: flex-start !important;
    flex-wrap: wrap;
}
.form-check-label {
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    margin-bottom: 0;
}
.form-check-input {
    margin-right: 4px;
    width: 0.9em;
    height: 0.9em;
}

/* Completion Overlay Styles */
.completion-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.5s ease-in-out;
    border-radius: 20px;
}

.completion-modal {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    color: white;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    animation: slideInUp 0.6s ease-out;
    margin: 2rem 0;
}

.completion-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.completion-modal .content {
    position: relative;
    z-index: 1;
}

.completion-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    animation: bounce 1s infinite;
}

.completion-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.completion-message {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.completion-stats {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

/* Remove close button styles - no longer needed */

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from { 
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}
</style>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="position: relative;">
            <div class="card-header bg-transparent border-0">
                <!-- Page Title -->
                <div class="text-center mb-3">
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Evaluation for Academic Year 
                        @if(isset($currentAcademicYear) && $currentAcademicYear && isset($currentAcademicYear->year))
                            {{ $currentAcademicYear->year }}
                            @if(isset($currentAcademicYear->semester) && $currentAcademicYear->semester)
                                | {{ $currentAcademicYear->semester }} Semester
                            @endif
                        @endif
                    </h4>
                </div>
                
                <!-- Closed notification message always visible and separated -->
                @if(!$isOpen)
                    <div class="w-100 mb-3">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Questions are temporarily closed by the admin.</strong> Please wait for them to open to start evaluation.
                            @if(isset($currentAcademicYear) && $currentAcademicYear && isset($currentAcademicYear->year))
                                <br><span class="fw-bold">Academic Year: {{ $currentAcademicYear->year }}
                                @if(isset($currentAcademicYear->semester) && $currentAcademicYear->semester)
                                    | {{ $currentAcademicYear->semester }} Semester
                                @endif
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Status and Badge Container -->
                <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
                    <!-- Notification badge for evaluated staff -->
                    <button type="button" class="btn position-relative p-0" id="evaluatedStaffBadge" data-bs-toggle="popover" data-bs-trigger="focus" title="Evaluated Staff Breakdown" data-bs-html="true" data-bs-content="
                        <div class='evaluated-popover-content'>
                            <div class='evaluated-popover-label'><i class='fas fa-chalkboard-teacher'></i> <span>Teaching Staff:</span> <strong>{{ $teachingCount }}</strong></div>
                            <div class='evaluated-popover-label'><i class='fas fa-users-cog'></i> <span>Non-Teaching Staff:</span> <strong>{{ $nonTeachingCount }}</strong></div>
                        </div>">
                        <span class="badge evaluated-badge-red">
                            {{ $distinctStaffIds->count() }}
                        </span>
                    </button>
                    
                    <!-- Question Status -->
                    <div class="enhanced-status-badge {{ $isOpen ? 'status-open' : 'status-closed' }}">
                        <i class="fas {{ $isOpen ? 'fa-unlock' : 'fa-lock' }} me-2"></i>
                        Questions are {{ $isOpen ? 'Open' : 'Closed' }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(!$isOpen)
                    <!-- Do not show privacy reminder or evaluation tabs when closed -->
                @else
                    <!-- Privacy Reminder Section (only for open status) -->
                    <div id="privacyReminder" class="privacy-reminder-box d-flex flex-column align-items-center justify-content-center" style="max-width: 600px; margin: 0 auto 30px auto;">
                        <div class="mb-3">
                            <i class="fas fa-user-secret fa-2x mb-2"></i>
                            <h5 class="fw-bold">Evaluator Privacy Notice</h5>
                            <p class="mb-0">Your identity and responses are strictly confidential. Please provide honest and constructive feedback. No one will know your answers or comments.</p>
                        </div>
                        <button id="startEvaluationBtn" class="btn btn-success mt-2" type="button">
                            <i class="fas fa-play me-2"></i>
                            @if($distinctStaffIds->count() > 0)
                                Continue Evaluation
                            @else
                                Start Evaluation
                            @endif
                        </button>
                    </div>
                    <!-- Tabs and Forms: hidden by default, shown after clicking Start -->
                    <div id="evaluationTabsWrapper" style="display: none;">
                    <ul class="nav nav-tabs custom-nav-tabs" id="evaluationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="teaching-tab" data-bs-toggle="tab" href="#teaching" role="tab" aria-controls="teaching" aria-selected="true">
                                <i class="fas fa-chalkboard-teacher tab-icon"></i>
                                <span>Teaching Staff</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="non-teaching-tab" data-bs-toggle="tab" href="#non-teaching" role="tab" aria-controls="non-teaching" aria-selected="false">
                                <i class="fas fa-users-cog tab-icon"></i>
                                <span>Non-Teaching Staff</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-4" id="evaluationTabsContent">
                        <div class="tab-pane fade show active" id="teaching" role="tabpanel" aria-labelledby="teaching-tab">
                            <div class="evaluation-form-wrapper">
                                <div class="evaluation-form-inner">
                                    <form method="POST" action="{{ route('evaluations.submit') }}">
                                        @csrf
                                        <input type="hidden" name="staff_type" value="teaching">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Instructor</label>
                                            @if($teachingStaff->count() > 0)
                                                <select name="staff_id" class="form-select enhanced-select" required>
                                                    <option value="">Choose an instructor to evaluate...</option>
                                                    @foreach($teachingStaff as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>No teaching staff found for your department ({{ auth()->user()->course }}).</strong><br>
                                                    Please contact the administrator if you believe this is an error.
                                                </div>
                                            @endif
                                        </div>
                                        <div id="teachingQuestions">
                                            @foreach($teachingQuestions->groupBy('title') as $title => $questionsGroup)
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold section-title">{{ $title }}</label>
                                                    @foreach($questionsGroup as $question)
                                                        <div class="mb-3 ms-3">
                                                            @if($question->description)
                                                                <p class="text-muted small">{{ $question->description }}</p>
                                                            @endif
                                                            <div class="d-flex flex-wrap gap-3 justify-content-center response-options-group">
                                                                @php
                                                                    $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get();
                                                                @endphp
                                                                @foreach($options as $option)
                                                                    <label class="form-check-label">
                                                                        <input type="radio" class="form-check-input" name="responses[{{ $question->id }}]" value="{{ $option->option_value }}" required>
                                                                        {{ $option->option_label }}
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Comments & Recommendations <span class="text-muted" style="font-weight:normal;">(Optional)</span></label>
                                                <textarea name="comments" class="form-control" rows="4" placeholder="Please provide any additional comments or recommendations for this instructor..."></textarea>
                                                <div class="form-text">Your feedback helps improve our educational services.</div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" id="submitTeachingEvaluation">
                                                <i class="fas fa-paper-plane me-2"></i>Submit Evaluation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="non-teaching" role="tabpanel" aria-labelledby="non-teaching-tab">
                            <div class="evaluation-form-wrapper">
                                <div class="evaluation-form-inner">
                                    <form method="POST" action="{{ route('evaluations.submit') }}">
                                        @csrf
                                        <input type="hidden" name="staff_type" value="non-teaching">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Staff Member</label>
                                            <select name="staff_id" class="form-select enhanced-select" required>
                                                <option value="">Choose a staff member to evaluate...</option>
                                                @foreach($nonTeachingStaff as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="nonTeachingQuestions">
                                            @foreach($nonTeachingQuestions->groupBy('title') as $title => $questionsGroup)
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold section-title">{{ $title }}</label>
                                                    @foreach($questionsGroup as $question)
                                                        <div class="mb-3 ms-3">
                                                            @if($question->description)
                                                                <p class="text-muted small">{{ $question->description }}</p>
                                                            @endif
                                                            <div class="d-flex flex-wrap gap-3 justify-content-center response-options-group">
                                                                @php
                                                                    $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get();
                                                                @endphp
                                                                @foreach($options as $option)
                                                                    <label class="form-check-label">
                                                                        <input type="radio" class="form-check-input" name="responses[{{ $question->id }}]" value="{{ $option->option_value }}" required>
                                                                        {{ $option->option_label }}
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Comments & Recommendations <span class="text-muted" style="font-weight:normal;">(Optional)</span></label>
                                                <textarea name="comments" class="form-control" rows="4" placeholder="Optional | provide any additional comments or recommendations for this staff member..."></textarea>
                                                <div class="form-text">Your feedback helps improve our services.</div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" id="submitNonTeachingEvaluation">
                                                <i class="fas fa-paper-plane me-2"></i>Submit Evaluation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div> <!-- end evaluationTabsWrapper -->
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Completion Overlay -->
@if($allEvaluationsCompleted)
<div id="completionOverlay" class="completion-overlay">
    <div class="completion-modal">
        <div class="content">
            <div class="completion-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h2 class="completion-title">🎉 Congratulations!</h2>
            <p class="completion-message">
                You have successfully completed all evaluations for this academic year
                @if(isset($currentAcademicYear) && $currentAcademicYear && isset($currentAcademicYear->year))
                    <br><strong>[{{ $currentAcademicYear->year }}]</strong>
                    @if(isset($currentAcademicYear->semester) && $currentAcademicYear->semester)
                        | <strong>{{ $currentAcademicYear->semester }} Semester</strong>
                    @endif
                @else
                    <br><strong>[{{ date('Y') }}-{{ date('Y') + 1 }}]</strong>
                @endif
            </p>
            <div class="completion-stats">
                <div class="row text-center">
                    <div class="col-6">
                        <h4>{{ $teachingCount }}</h4>
                        <small>Teaching Staff Evaluated</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ $nonTeachingCount }}</h4>
                        <small>Non-Teaching Staff Evaluated</small>
                    </div>
                </div>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 1rem 0;">
                <div class="text-center">
                    <h3>{{ $totalEvaluatedStaff }}</h3>
                    <small>Total Staff Members Evaluated</small>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                    <i class="fas fa-info-circle me-1"></i>
                    All evaluations completed for this academic year
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle session messages with SweetAlert - ensure this runs first
    @if(session('message'))
        // Wait for SweetAlert to be fully loaded
        let alertAttempts = 0;
        function showSessionAlert() {
            alertAttempts++;
            if (typeof Swal !== 'undefined') {
                @if(session('message_type') === 'success')
                    Swal.fire({
                        icon: 'success',
                        title: 'Evaluation Submitted Successfully!',
                        text: '{{ session('message') }}',
                        confirmButtonColor: '#667eea',
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    }).catch(function(error) {
                        console.error('SweetAlert error:', error);
                        alert('Evaluation submitted successfully!');
                    });
                @elseif(session('message_type') === 'error' || session('message_type') === 'danger')
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '{{ session('message') }}',
                        confirmButtonColor: '#667eea',
                        showConfirmButton: true,
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    }).catch(function(error) {
                        console.error('SweetAlert error:', error);
                        alert('Error: {{ session('message') }}');
                    });
                @else
                    Swal.fire({
                        icon: 'info',
                        title: 'Information',
                        text: '{{ session('message') }}',
                        confirmButtonColor: '#667eea',
                        showConfirmButton: true,
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    }).catch(function(error) {
                        console.error('SweetAlert error:', error);
                        alert('{{ session('message') }}');
                    });
                @endif
            } else if (alertAttempts < 50) {
                // Retry after 100ms if SweetAlert is not loaded yet (max 5 seconds)
                setTimeout(showSessionAlert, 100);
            } else {
                // Fallback to native alert after 5 seconds
                console.warn('SweetAlert failed to load, using native alert');
                @if(session('message_type') === 'success')
                    alert('Evaluation submitted successfully!\n{{ session('message') }}');
                @else
                    alert('{{ session('message') }}');
                @endif
            }
        }
        
        // Start trying to show the alert - ensure page is fully loaded
        if (document.readyState === 'complete') {
            setTimeout(showSessionAlert, 200);
        } else {
            window.addEventListener('load', function() {
                setTimeout(showSessionAlert, 200);
            });
        }
    @endif
    // Privacy reminder logic
    var privacyReminder = document.getElementById('privacyReminder');
    var evaluationTabsWrapper = document.getElementById('evaluationTabsWrapper');
    var startBtn = document.getElementById('startEvaluationBtn');
    if (privacyReminder && evaluationTabsWrapper && startBtn) {
        evaluationTabsWrapper.style.display = 'none';
        privacyReminder.style.display = 'block';
        startBtn.addEventListener('click', function() {
            privacyReminder.classList.add('hide');
            evaluationTabsWrapper.style.display = 'block';
        });
    } else if (evaluationTabsWrapper) {
        // fallback: if privacy reminder not present, show tabs
        evaluationTabsWrapper.style.display = 'block';
    }
    // Teaching tab
    const teachingStaffSelect = document.querySelector('select[name="staff_id"][form]:not([form="nonTeachingForm"])') || document.querySelector('#teaching select[name="staff_id"]');
    const teachingQuestions = document.getElementById('teachingQuestions');
    const teachingSubmit = document.getElementById('submitTeachingEvaluation');
    if (teachingStaffSelect && teachingQuestions && teachingSubmit) {
        teachingQuestions.style.display = 'none';
        teachingSubmit.style.display = 'none';
        teachingStaffSelect.addEventListener('change', function() {
            if (this.value) {
                teachingQuestions.style.display = 'block';
                teachingSubmit.style.display = 'inline-block';
            } else {
                teachingQuestions.style.display = 'none';
                teachingSubmit.style.display = 'none';
            }
        });
    }
    // Non-teaching tab
    const nonTeachingStaffSelect = document.querySelector('#non-teaching select[name="staff_id"]');
    const nonTeachingQuestions = document.getElementById('nonTeachingQuestions');
    const nonTeachingSubmit = document.getElementById('submitNonTeachingEvaluation');
    if (nonTeachingStaffSelect && nonTeachingQuestions && nonTeachingSubmit) {
        nonTeachingQuestions.style.display = 'none';
        nonTeachingSubmit.style.display = 'none';
        nonTeachingStaffSelect.addEventListener('change', function() {
            if (this.value) {
                nonTeachingQuestions.style.display = 'block';
                nonTeachingSubmit.style.display = 'inline-block';
            } else {
                nonTeachingQuestions.style.display = 'none';
                nonTeachingSubmit.style.display = 'none';
            }
        });
    }
    // Bootstrap popover for evaluated staff badge
    var popoverTrigger = document.getElementById('evaluatedStaffBadge');
    if (popoverTrigger && window.bootstrap) {
        new bootstrap.Popover(popoverTrigger);
    }
    
    // Add form submission handlers to ensure proper submission
    const teachingForm = document.querySelector('form[action="{{ route('evaluations.submit') }}"][method="POST"]:has(input[name="staff_type"][value="teaching"])');
    const nonTeachingForm = document.querySelector('form[action="{{ route('evaluations.submit') }}"][method="POST"]:has(input[name="staff_type"][value="non-teaching"])');
    
    if (teachingForm) {
        teachingForm.addEventListener('submit', function(e) {
            console.log('Teaching form submitted');
            // Add loading state to prevent double submission
            const submitBtn = this.querySelector('#submitTeachingEvaluation');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            }
        });
    }
    
    if (nonTeachingForm) {
        nonTeachingForm.addEventListener('submit', function(e) {
            console.log('Non-teaching form submitted');
            // Add loading state to prevent double submission
            const submitBtn = this.querySelector('#submitNonTeachingEvaluation');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            }
        });
    }
    
    // Debug: Log session data and test SweetAlert
    @if(session('message'))
        console.log('Session message found:', '{{ session('message') }}');
        console.log('Session message type:', '{{ session('message_type') }}');
        console.log('SweetAlert available:', typeof Swal !== 'undefined');
    @else
        console.log('No session message found');
    @endif
    
    // Debug: Log completion data
    console.log('Completion Debug:', {
        totalAvailableTeaching: {{ $totalAvailableTeaching }},
        totalAvailableNonTeaching: {{ $totalAvailableNonTeaching }},
        totalAvailableStaff: {{ $totalAvailableStaff }},
        teachingCount: {{ $teachingCount }},
        nonTeachingCount: {{ $nonTeachingCount }},
        totalEvaluatedStaff: {{ $totalEvaluatedStaff }},
        allEvaluationsCompleted: {{ $allEvaluationsCompleted ? 'true' : 'false' }}
    });
    
    // Test SweetAlert availability
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded!');
    } else {
        console.log('SweetAlert2 is loaded and ready');
        

    }
    
    // Completion overlay functions - no close function needed as overlay should stay permanent
    
    // Check for completion after successful evaluation
    @if(session('message_type') === 'success')
        // After showing success alert, check if all evaluations are completed
        setTimeout(function() {
            const totalAvailable = {{ $totalAvailableStaff }};
            const totalEvaluated = {{ $totalEvaluatedStaff }};
            
            console.log('Completion check:', {
                totalAvailable: totalAvailable,
                totalEvaluated: totalEvaluated,
                completed: totalEvaluated >= totalAvailable && totalAvailable > 0
            });
            
            if (totalEvaluated >= totalAvailable && totalAvailable > 0) {
                // Show completion overlay after a delay
                setTimeout(function() {
                    showCompletionOverlay();
                }, 2000);
            }
        }, 1000);
    @endif
    
    // Function to dynamically show completion overlay
    window.showCompletionOverlay = function() {
        if (document.getElementById('completionOverlay')) {
            return; // Already exists
        }
        
        const overlay = document.createElement('div');
        overlay.id = 'completionOverlay';
        overlay.className = 'completion-overlay';
        overlay.innerHTML = `
            <div class="completion-modal">
                <div class="content">
                    <div class="completion-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h2 class="completion-title">🎉 Congratulations!</h2>
                    <p class="completion-message">
                        You have successfully completed all evaluations for this academic year
                        @if(isset($currentAcademicYear) && $currentAcademicYear && isset($currentAcademicYear->year))
                            <br><strong>[{{ $currentAcademicYear->year }}]</strong>
                            @if(isset($currentAcademicYear->semester) && $currentAcademicYear->semester)
                                | <strong>{{ $currentAcademicYear->semester }} Semester</strong>
                            @endif
                        @else
                            <br><strong>[{{ date('Y') }}-{{ date('Y') + 1 }}]</strong>
                        @endif
                    </p>
                    <div class="completion-stats">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4>{{ $teachingCount }}</h4>
                                <small>Teaching Staff Evaluated</small>
                            </div>
                            <div class="col-6">
                                <h4>{{ $nonTeachingCount }}</h4>
                                <small>Non-Teaching Staff Evaluated</small>
                            </div>
                        </div>
                        <hr style="border-color: rgba(255,255,255,0.3); margin: 1rem 0;">
                        <div class="text-center">
                            <h3>{{ $totalEvaluatedStaff }}</h3>
                            <small>Total Staff Members Evaluated</small>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            All evaluations completed for this academic year
                        </p>
                    </div>
                </div>
            </div>
        `;
        
        // Append to the card instead of body to position it relative to the evaluation area
        const card = document.querySelector('.card.border-0.shadow-sm');
        if (card) {
            card.appendChild(overlay);
        } else {
            document.body.appendChild(overlay);
        }
    };
});
</script>
