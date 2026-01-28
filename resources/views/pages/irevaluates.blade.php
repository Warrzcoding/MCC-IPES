@php
    $teachingEvaluated = $teachingEvaluated ?? 0;
    $nonTeachingEvaluated = $nonTeachingEvaluated ?? 0;
    $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
    $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();
    
    // Get the active academic year
    $currentAcademicYear = $currentAcademicYear ?? \App\Models\AcademicYear::where('is_active', 1)->first();
    $activeAcademicYearId = $currentAcademicYear ? $currentAcademicYear->id : null;

    // For specific evaluated IDs (needed for the "Evaluated" badge on cards)
    $evaluatedTeachingIds = \App\Models\Evaluation::where('user_id', auth()->id())
        ->where('academic_year_id', $activeAcademicYearId)
        ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
        ->pluck('staff_id')
        ->unique()
        ->toArray();
    $evaluatedNonTeachingIds = \App\Models\Evaluation::where('user_id', auth()->id())
        ->where('academic_year_id', $activeAcademicYearId)
        ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
        ->pluck('staff_id')
        ->unique()
        ->toArray();
    
    $teachingCount = count($evaluatedTeachingIds);
    $nonTeachingCount = count($evaluatedNonTeachingIds);
    $distinctStaffIds = array_unique(array_merge($evaluatedTeachingIds, $evaluatedNonTeachingIds));
    
    // Get locked selections from database - with defaults
    $lockedSelections = isset($lockedSelections) ? $lockedSelections : [
        'teaching' => collect(),
        'non-teaching' => collect()
    ];
    $hasLockedSelection = $hasLockedSelection ?? false;
    
    // Calculate completion status
    $totalAvailableTeaching = isset($teachingStaff) ? $teachingStaff->count() : 0;
    $totalAvailableNonTeaching = isset($nonTeachingStaff) ? $nonTeachingStaff->count() : 0;
    $totalAvailableStaff = $totalAvailableTeaching + $totalAvailableNonTeaching;
    $totalEvaluatedStaff = $teachingCount + $nonTeachingCount;
    
    // Check if all evaluations are completed
    $allEvaluationsCompleted = ($totalAvailableStaff > 0) && ($totalEvaluatedStaff >= $totalAvailableStaff);
@endphp

<style>
/* Base Styles for Evaluation Page */
.evaluations-page {
    font-size: 0.95rem;
}
.evaluations-page .card-header h4 {
    font-size: 1.2rem;
}
.evaluation-card {
    max-width: 920px;
    margin-left: auto;
    margin-right: auto;
}

/* Enhanced Tab Navigation Styles (from evaluates.blade) */
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
    font-size: 1rem;
    padding: 12px 20px;
    border-radius: 10px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
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
    padding: 30px;
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

/* Disable Non-Teaching Tab */
#non-teaching-content {
    display: none !important;
}
#non-teaching-tab {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    position: relative;
}

/* Section Title (from evaluates.blade) */
.section-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 20px;
    position: relative;
    padding-left: 16px;
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

/* Response Options Group (from evaluates.blade) */
.response-options-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 0.5rem !important;
    justify-content: flex-start !important;
    flex-wrap: nowrap;
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
.form-check-label.selected-rating {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    padding: 8px 16px !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    transform: scale(1.05);
    transition: all 0.3s ease;
    border-color: #667eea !important;
}

/* Status Badges */
.enhanced-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.45px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
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
.enhanced-status-badge.status-open {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}
.enhanced-status-badge.status-closed {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    color: white;
}

/* Evaluated Staff Badge */
.evaluated-badge-red {
    background: linear-gradient(135deg, #ff4d4f 0%, #ff7875 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(255, 77, 79, 0.25);
    border: 2px solid #fff;
    font-weight: bold;
    font-size: 1.05rem;
    width: 2.1em;
    height: 2.1em;
    min-width: 2.1em;
    min-height: 2.1em;
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

/* Completion Overlay (from evaluates.blade) */
.completion-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
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
}
.completion-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
}
.completion-stats {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

/* Instructor Tabs for Irregular Selection */
.instructor-tabs .nav-link {
    color: #4a5568;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    margin-right: 8px;
    background-color: #f8fafc;
    border-radius: 12px 12px 0 0;
    padding: 10px 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.instructor-tabs .nav-link:hover {
    background-color: #edf2f7;
    transform: translateY(-2px);
}
.instructor-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
}
.instructor-tabs .nav-link.completed {
    border-bottom: 3px solid #48bb78;
}
.instructor-tabs .nav-link.completed .status-dot {
    background-color: #48bb78;
    box-shadow: 0 0 8px rgba(72, 187, 120, 0.5);
}
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    background-color: #cbd5e0;
    margin-right: 6px;
}

.evaluation-form-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
}
.evaluation-form-inner {
    max-width: 800px;
    width: 100%;
}

.bg-success-soft {
    background-color: rgba(72, 187, 120, 0.15) !important;
}

.status-badges {
    gap: 0.75rem !important;
    flex-wrap: nowrap !important;
}

#evaluatedStaffBadge,
.status-badges .enhanced-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.instructor-tabs {
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 5px;
}
.instructor-tabs::-webkit-scrollbar {
    display: none;
}
.instructor-tabs .nav-tabs {
    flex-wrap: nowrap;
    border-bottom: 2px solid #e2e8f0 !important;
}

.staff-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    height: 100%;
}
.staff-card .flex-grow-1 {
    min-width: 0;
}
.cursor-pointer {
    cursor: pointer;
}
.staff-card small.text-muted {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}
.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    border-color: #667eea;
}
.staff-card.selected {
    border-color: #667eea;
    background-color: #f0f4ff;
    box-shadow: 0 0 0 2px #667eea;
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
    /* Center the card on mobile with equal margins */
    .evaluation-card {
        width: 98%;
        margin: 0 auto;
    }

    .evaluations-page {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .evaluations-page > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Center the cards container on mobile */
    .staff-container {
        justify-content: center !important;
        display: flex !important;
        flex-wrap: wrap !important;
    }

    /* Ensure cards are centered on mobile */
    .staff-item {
        display: flex !important;
        justify-content: center !important;
    }

    /* When hidden, respect d-none even with flex !important */
    .staff-item.d-none {
        display: none !important;
    }

    .staff-card {
        width: 100% !important;
        max-width: 400px !important;
        margin: 0 auto !important;
    }

    /* Reduce padding on tab-content for mobile */
    .tab-content {
        padding: 15px;
    }

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
        padding: 4px;
    }
    .custom-nav-tabs .nav-item {
        flex: 1;
        min-width: 0;
    }
    .custom-nav-tabs .nav-link {
        padding: 8px 6px;
        font-size: 0.8rem;
        text-align: center;
        gap: 6px;
    }
    .tab-icon {
        font-size: 1rem;
    }
    
    /* Mobile header alignment */
    .card-header {
        text-align: center;
    }
    
    /* Mobile status and badge container - keep horizontal */
    .status-badges {
        flex-direction: row !important;
        gap: 15px !important;
        justify-content: center !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }

    /* Ensure badge and status stay horizontal */
    .status-badges > * {
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
    
    #submitEvalBtn, #startEvaluationBtn, #doneSelectionBtn, #doneNonTeachingBtn, #lockedControls .btn, #lockedControlsNonTeaching .btn {
        width: 100%;
        padding: 12px 20px !important;
        font-size: 0.9rem !important;
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

    /* Disabled Evaluate Button Styling */
    .evaluate-btn-wrapper button:disabled {
        background: linear-gradient(135deg, #cbd5e0 0%, #a0aec0 100%) !important;
        color: #4a5568 !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    }
    .evaluate-btn-wrapper button:disabled:hover {
        transform: none !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    }

    /* Make content wider on mobile with equal spacing */
    .evaluation-form-inner {
        max-width: 100%;
    }

    /* Ensure search bar is centered and full width on mobile */
    .staff-search-container {
        max-width: 100% !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* Center privacy notice on mobile */
    .privacy-reminder-box {
        max-width: 100% !important;
        margin: 0 auto 20px auto !important;
        width: 100% !important;
    }
}
</style>

<div class="row page-full-width evaluations-page justify-content-center">
     <div class="col-12 col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm evaluation-card" style="position: relative;">
            <div class="card-header bg-transparent border-0 text-center">
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
                
                @if(!$isOpen)
                    <div class="w-100 mb-3">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Questions are temporarily closed by the admin.</strong> Please wait for them to open to start evaluation.
                        </div>
                    </div>
                @endif
                
                <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap status-badges">
                    <button type="button" class="btn position-relative p-0" id="evaluatedStaffBadge" data-bs-toggle="popover" data-bs-trigger="click" title="Evaluated Staff Breakdown" data-bs-html="true" data-bs-content="
                        <div class='evaluated-popover-content'>
                            <div class='evaluated-popover-label'><i class='fas fa-chalkboard-teacher'></i> <span>Teaching Staff:</span> <strong>{{ $teachingCount }}</strong></div>
                            <div class='evaluated-popover-label'><i class='fas fa-users-cog'></i> <span>Non-Teaching Staff:</span> <strong>{{ $nonTeachingCount }}</strong></div>
                        </div>">
                        <span class="badge evaluated-badge-red">
                            {{ count($distinctStaffIds) }}
                        </span>
                    </button>
                    
                    <div class="enhanced-status-badge {{ $isOpen ? 'status-open' : 'status-closed' }}">
                        <i class="fas {{ $isOpen ? 'fa-unlock' : 'fa-lock' }} me-2"></i>
                        Questions are {{ $isOpen ? 'Open' : 'Closed' }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if(!$isOpen)
                    <div class="text-center py-5">
                        <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                        <h5>Evaluation is currently closed</h5>
                        <p class="text-muted">Please check back later once the administrator opens the evaluation period.</p>
                    </div>
                @else
                    <!-- Privacy Notice (from evaluates.blade) -->
                    <div id="privacyReminder" class="privacy-reminder-box d-flex flex-column align-items-center justify-content-center text-center p-4 mb-4 bg-light rounded-3 border" style="max-width: 600px; margin: 0 auto;">
                        <div class="mb-3">
                            <i class="fas fa-user-secret fa-2x mb-2 text-primary"></i>
                            <h5 class="fw-bold">Evaluator Privacy Notice</h5>
                            <p class="mb-0">Your identity and responses are strictly confidential. Please provide honest and constructive feedback. No one will know your answers or comments.</p>
                        </div>
                        <button id="startEvaluationBtn" class="btn btn-success px-3 px-md-4 py-2 fw-bold rounded-pill" type="button">
                            <i class="fas fa-play me-2"></i>Select Specific Instructorssss
                        </button>
                    </div>

                    <div id="selectionAndEvaluationWrapper" style="display: none;">
                        <!-- Navigation Tabs (evaluates.blade style) -->
                        <div id="navigationSection" class="mb-4">
                            <ul class="nav nav-tabs custom-nav-tabs" id="staffTypeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="teaching-tab" data-bs-toggle="tab" data-bs-target="#teaching-content" type="button" role="tab" onclick="setStaffType('teaching')">
                                        <i class="fas fa-chalkboard-teacher tab-icon me-2"></i>Instructors
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="non-teaching-tab" type="button" role="tab">
                                        <i class="fas fa-users-cog tab-icon me-2"></i>Non-Teaching
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Search Section -->
                        <div id="staffListSection">
                            <div class="staff-search-container position-relative mb-4 mx-auto" style="max-width: 500px;">
                                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="staffSearch" class="form-control rounded-pill ps-5 border-2" placeholder="Search by name or ID...">
                            </div>

                            <div class="tab-content" id="staffTypeTabsContent">
                                <!-- Teaching Staff Tab -->
                                <div class="tab-pane fade show active" id="teaching-content" role="tabpanel">
                                    <div class="row g-3 staff-container justify-content-end" id="teachingList" style="padding-left: 1rem;">
                                        @foreach($studentSubjects->groupBy('assign_instructor') as $instructorName => $subjects)
                                            @php 
                                                $staff = $teachingStaff->where('full_name', $instructorName)->first();
                                                $staffId = $staff ? $staff->id : null;
                                                $evaluated = $staffId ? in_array($staffId, $evaluatedTeachingIds, true) : false;
                                                $subjectNames = $subjects->pluck('sub_name')->implode(', ');
                                            @endphp
                                            <div class="col-md-6 staff-item" data-name="{{ strtolower($instructorName) }}" data-id="{{ strtolower($subjectNames) }}">
                                                <div class="card staff-card h-100 {{ $evaluated ? 'evaluated' : '' }} {{ !$staffId ? 'opacity-75' : '' }}" 
                                                    onclick="{{ $staffId ? "handleStaffSelection(this, $staffId, 'teaching', '$instructorName', " . ($evaluated ? 'true' : 'false') . ", '" . addslashes($subjectNames) . "')" : "Swal.fire({icon:'warning', title:'Staff Not Found', text:'This instructor is not yet registered in the system. Please contact the administrator.', confirmButtonColor:'#667eea'})" }}">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="flex-shrink-0 me-3 checkbox-wrapper">
                                                                <div class="form-check">
                                                                    <input class="form-check-input select-staff-checkbox" type="checkbox" name="selected_staff[]" 
                                                                        {{ $evaluated || !$staffId ? 'disabled' : '' }} 
                                                                        data-staff-id="{{ $staffId }}" 
                                                                        data-staff-name="{{ $instructorName }}">
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 fw-bold text-dark">{{ $instructorName }}</h6>
                                                                <small class="text-muted d-flex align-items-center mb-1" title="{{ $subjectNames }}">
                                                                    <i class="fas fa-book me-1"></i>
                                                                    <span class="text-truncate" style="max-width: 150px;">{{ $subjectNames }}</span>
                                                                    <i class="fas fa-info-circle ms-1 text-primary cursor-pointer subject-popover" 
                                                                       style="font-size: 0.8rem;"
                                                                       onclick="event.stopPropagation()"
                                                                       tabindex="0"
                                                                       data-bs-toggle="popover" 
                                                                       data-bs-trigger="click" 
                                                                       data-bs-placement="top"
                                                                       title="Full Subject List"
                                                                       data-bs-content="{{ $subjectNames }}"></i>
                                                                </small>
                                                                <div>
                                                                    @if($evaluated)
                                                                        <span class="badge bg-success-soft text-success rounded-pill">
                                                                            <i class="fas fa-check-circle me-1"></i>Evaluated
                                                                        </span>
                                                                    @endif
                                                                    <div class="completion-status-badge" style="display: none;">
                                                                        <span class="badge bg-success rounded-pill">
                                                                            <i class="fas fa-check me-1"></i>Ready
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="evaluate-btn-wrapper mt-2" style="display: none;">
                                                            <button type="button" class="btn btn-sm btn-primary w-100 rounded-pill py-2 fw-bold" 
                                                                {{ $evaluated ? 'disabled' : '' }}
                                                                onclick="event.stopPropagation(); {{ $evaluated ? "Swal.fire({icon:'info', title:'Already Evaluated', text:'You have already submitted an evaluation for this instructor.', confirmButtonColor:'#667eea'})" : "openEvaluation($staffId, 'teaching', '" . addslashes($instructorName) . "')" }}">
                                                                <i class="fas fa-file-alt me-1"></i> {{ $evaluated ? 'Already Evaluated' : 'Open Evaluation Form' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($studentSubjects->isEmpty())
                                            <div class="col-12 text-center py-4">
                                                <p class="text-muted">No instructors found for your course.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-center mt-4" id="selectionControls">
                                        <div id="initialControls">
                                            <button type="button" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm" id="doneSelectionBtn" disabled onclick="confirmSelection()">
                                                Done Selection
                                            </button>
                                        </div>
                                        <div id="reviewControls" style="display: none;">
                                            <div class="d-flex justify-content-center gap-3">
                                                <button type="button" class="btn btn-outline-secondary px-4 fw-bold rounded-pill" onclick="unlockSelection()">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </button>
                                                <button type="button" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm" id="confirmSelectionBtn" onclick="finalConfirmSelection()">
                                                    <i class="fas fa-check-double me-2"></i>Confirm
                                                </button>
                                            </div>
                                        </div>
                                        <div id="lockedControls" style="display: none;">
                                            <div class="alert alert-success d-inline-block px-4 py-2 rounded-pill mb-0 shadow-sm">
                                                <i class="fas fa-lock me-2"></i>Selection Locked - You can now start the evaluation
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Non-Teaching Staff Tab -->
                                <div class="tab-pane fade" id="non-teaching-content" role="tabpanel">
                                    <div class="alert alert-info mb-4 border-0 shadow-sm rounded-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Non-teaching evaluation is currently not available.</strong>
                                    </div>
                                    <div class="row g-3 staff-container justify-content-end" id="nonTeachingList" style="opacity: 0.7; pointer-events: none; padding-right: 2rem;">
                                        @foreach($nonTeachingStaff as $staff)
                                            @php $evaluated = in_array($staff->id, $evaluatedNonTeachingIds, true); @endphp
                                            <div class="col-md-6 staff-item" data-name="{{ strtolower($staff->full_name) }}" data-id="{{ strtolower($staff->staff_id) }}">
                                                <div class="card staff-card h-100 {{ $evaluated ? 'evaluated' : '' }}" onclick="handleStaffSelection(this, {{ $staff->id }}, 'non-teaching', '{{ $staff->full_name }}', {{ $evaluated ? 'true' : 'false' }})">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="flex-shrink-0 me-3 checkbox-wrapper">
                                                                <div class="form-check">
                                                                    <input class="form-check-input select-staff-checkbox" type="checkbox" name="selected_non_teaching[]" 
                                                                        {{ $evaluated ? 'disabled' : '' }} 
                                                                        data-staff-id="{{ $staff->id }}" 
                                                                        data-staff-name="{{ $staff->full_name }}">
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 fw-bold text-dark">{{ $staff->full_name }}</h6>
                                                                <small class="text-muted mb-1 d-block">{{ $staff->staff_id }}</small>
                                                                <div>
                                                                    @if($evaluated)
                                                                        <span class="badge bg-success-soft text-success rounded-pill">
                                                                            <i class="fas fa-check-circle me-1"></i>Evaluated
                                                                        </span>
                                                                    @endif
                                                                    <div class="completion-status-badge" style="display: none;">
                                                                        <span class="badge bg-success rounded-pill">
                                                                            <i class="fas fa-check me-1"></i>Ready
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="evaluate-btn-wrapper mt-2" style="display: none;">
                                                            <button type="button" class="btn btn-sm btn-success w-100 rounded-pill py-2 fw-bold" 
                                                                {{ $evaluated ? 'disabled' : '' }}
                                                                onclick="event.stopPropagation(); {{ $evaluated ? "Swal.fire({icon:'info', title:'Already Evaluated', text:'You have already submitted an evaluation for this staff member.', confirmButtonColor:'#667eea'})" : "openEvaluation($staff->id, 'non-teaching', '" . addslashes($staff->full_name) . "')" }}">
                                                                <i class="fas fa-file-alt me-1"></i> {{ $evaluated ? 'Already Evaluated' : 'Open Evaluation Form' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($nonTeachingStaff->isEmpty())
                                            <div class="col-12 text-center py-4">
                                                <p class="text-muted">No non-teaching staff found.</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-center mt-4" id="selectionControlsNonTeaching" style="display: none;">
                                        <div id="initialControlsNonTeaching" style="display: none;">
                                            <button type="button" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm" id="doneNonTeachingBtn" disabled onclick="confirmSelection()">
                                                Done Selection
                                            </button>
                                        </div>
                                        <div id="reviewControlsNonTeaching" style="display: none;">
                                            <div class="d-flex justify-content-center gap-3">
                                                <button type="button" class="btn btn-outline-secondary px-4 fw-bold rounded-pill" onclick="unlockSelection()">
                                                    <i class="fas fa-edit me-2"></i>Edit Selection
                                                </button>
                                                <button type="button" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm" id="confirmSelectionBtnNonTeaching" onclick="finalConfirmSelection()">
                                                    <i class="fas fa-check-double me-2"></i>Confirm Selection
                                                </button>
                                            </div>
                                        </div>
                                        <div id="lockedControlsNonTeaching" style="display: none;">
                                            <div class="alert alert-success d-inline-block px-4 py-2 rounded-pill mb-0 shadow-sm">
                                                <i class="fas fa-lock me-2"></i>Selection Locked - You can now start the evaluation
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluation Form Section (Hidden by default) -->
                        <div id="evaluationFormSection" style="display: none;" class="tab-content">
                            <div class="evaluation-form-wrapper">
                                <div class="evaluation-form-inner">
                                    <button type="button" class="btn btn-link text-decoration-none mb-3 p-0" onclick="showStaffList()">
                                        <i class="fas fa-arrow-left me-1"></i> Back to selection
                                    </button>
                                    
                                    <div class="selected-staff-info mb-4 p-4 bg-white rounded-3 d-flex align-items-center shadow-sm border">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 65px; height: 65px; border: 2px solid #e2e8f0;">
                                            <i class="fas fa-user-tie text-primary fa-lg" id="selectedStaffIcon"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 fw-bold text-dark" id="selectedStaffName">Name</h5>
                                            <p class="mb-0 text-muted fw-medium" id="selectedStaffTypeLabel">Teaching Staff</p>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('evaluations.submit') }}" id="irregularEvalForm">
                                @csrf
                                <input type="hidden" name="staff_id" id="formStaffId">
                                <input type="hidden" name="staff_type" id="formStaffType">
                                
                                <!-- Questions for Teaching -->
                                <div id="teachingQuestionsContainer">
                                    @foreach($teachingQuestions->groupBy('title') as $title => $questionsGroup)
                                        <div class="mb-4">
                                            <h6 class="fw-bold section-title">{{ $title }}</h6>
                                            @foreach($questionsGroup as $question)
                                                <div class="mb-4 ms-3">
                                                    @if($question->description)
                                                        <p class="text-muted small mb-2">{{ $question->description }}</p>
                                                    @else
                                                        <p class="mb-3 fw-bold text-dark">{{ $loop->parent->iteration }}.{{ $loop->iteration }} {{ $question->question_text }}</p>
                                                    @endif
                                                    <div class="d-flex flex-wrap gap-3 justify-content-center response-options-group">
                                                        @php $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get(); @endphp
                                                        @foreach($options as $option)
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input" name="responses[{{ $question->id }}]" id="tq{{ $question->id }}_o{{ $option->id }}" value="{{ $option->option_value }}" required disabled>
                                                                {{ $option->option_label }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Questions for Non-Teaching -->
                                <div id="nonTeachingQuestionsContainer" style="display: none;">
                                    @foreach($nonTeachingQuestions->groupBy('title') as $title => $questionsGroup)
                                        <div class="mb-4">
                                            <h6 class="fw-bold section-title">{{ $title }}</h6>
                                            @foreach($questionsGroup as $question)
                                                <div class="mb-4 ms-3">
                                                    @if($question->description)
                                                        <p class="text-muted small mb-2">{{ $question->description }}</p>
                                                    @else
                                                        <p class="mb-3 fw-bold text-dark">{{ $loop->parent->iteration }}.{{ $loop->iteration }} {{ $question->question_text }}</p>
                                                    @endif
                                                    <div class="d-flex flex-wrap gap-3 justify-content-center response-options-group">
                                                        @php $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get(); @endphp
                                                        @foreach($options as $option)
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input" name="responses[{{ $question->id }}]" id="ntq{{ $question->id }}_o{{ $option->id }}" value="{{ $option->option_value }}" required disabled>
                                                                {{ $option->option_label }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Comments & Recommendations <span class="text-muted" style="font-weight:normal;">(Optional)</span></label>
                                    <textarea name="comments" class="form-control border-2" rows="4" placeholder="Provide any additional comments or recommendations..."></textarea>
                                    <div class="form-text mt-2 text-muted">Your feedback helps improve our services.</div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-3 px-md-5 py-2 py-md-3 fw-bold shadow-sm rounded-pill" id="submitEvalBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Evaluation
                                    </button>
                                </div>
                            </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Overlay (from evaluates.blade) -->
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
                                    @endif
                                </p>
                                <div class="completion-stats">
                                    <div class="row text-center">
                                        <div class="col-6 border-end border-white border-opacity-25">
                                            <h4 class="fw-bold">{{ $teachingCount }}</h4>
                                            <small>Teaching Staff</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="fw-bold">{{ $nonTeachingCount }}</h4>
                                            <small>Non-Teaching</small>
                                        </div>
                                    </div>
                                    <hr class="my-3 opacity-25">
                                    <div class="text-center">
                                        <h3 class="fw-bold">{{ $totalEvaluatedStaff }}</h3>
                                        <small>Total Evaluated</small>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <p class="mb-0 opacity-75 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        All evaluations completed for this academic year
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStaffType = 'teaching';
let selectedStaff = [];
let activeStaffId = null;

// Data from database (Blade PHP variables)
const dbHasLockedSelection = {{ (isset($hasLockedSelection) && $hasLockedSelection) ? 'true' : 'false' }};
const dbLockedSelections = {
    teaching: {{ isset($lockedSelections) && isset($lockedSelections['teaching']) ? json_encode($lockedSelections['teaching']->map(function($s) { return ['id' => $s->staff_id, 'name' => $s->staff->full_name ?? '']; })->values()->toArray()) : '[]' }},
    'non-teaching': {{ isset($lockedSelections) && isset($lockedSelections['non-teaching']) ? json_encode($lockedSelections['non-teaching']->map(function($s) { return ['id' => $s->staff_id, 'name' => $s->staff->full_name ?? '']; })->values()->toArray()) : '[]' }}
};

document.addEventListener('DOMContentLoaded', function() {
    // Check if user has locked selection from database (persistent across devices)
    if (dbHasLockedSelection) {
        restoreLockedSelectionFromDatabase();
    } else {
        restoreSelectionState();
    }
    
    // Setup non-teaching tab disable globally (always active)
    const nonTeachingTab = document.getElementById('non-teaching-tab');
    if (nonTeachingTab) {
        // Disable the button completely
        nonTeachingTab.disabled = true;
        nonTeachingTab.style.opacity = '0.5';
        nonTeachingTab.style.cursor = 'not-allowed';
        
        // Remove Bootstrap tab functionality
        nonTeachingTab.removeAttribute('data-bs-toggle');
        nonTeachingTab.removeAttribute('data-bs-target');
        nonTeachingTab.classList.remove('active');
        
        // Use mousedown to capture before Bootstrap's click
        nonTeachingTab.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('Non-teaching tab clicked - showing alert');
            Swal.fire({
                icon: 'warning',
                title: 'Unavailable',
                text: 'Non-Teaching evaluation is currently not available.',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
            return false;
        }, true); // Use capture phase
        
        // Also add click listener as backup
        nonTeachingTab.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
    }
    
    // Setup INSTRUCTORS tab to restore locked state when clicked
    const teachingTab = document.getElementById('teaching-tab');
    const staffTypeTabs = document.getElementById('staffTypeTabs');
    
    // Listen to Bootstrap tab events on the entire tab container
    if (staffTypeTabs) {
        staffTypeTabs.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
            
            // If teaching tab is shown and we have locked selection, restore locked state
            if (targetId === '#teaching-content' && dbHasLockedSelection) {
                currentStaffType = 'teaching';
                setTimeout(function() {
                    applyLockedUIState();
                    console.log('Restored locked state for teaching tab after Bootstrap tab switch');
                }, 50);
            }
        });
    }
    
    if (teachingTab) {
        // Also handle click as backup
        if (dbHasLockedSelection) {
            teachingTab.addEventListener('click', function(e) {
                // Restore locked UI state after a brief delay to allow Bootstrap to process
                setTimeout(function() {
                    if (currentStaffType === 'teaching') {
                        applyLockedUIState();
                        console.log('Restored locked state for teaching tab on click');
                    }
                }, 100);
            });
        }
    }
    
    // Handle session messages
    @if(session('message'))
        Swal.fire({
            icon: '{{ session('message_type') === 'success' ? 'success' : 'error' }}',
            title: '{{ session('message_type') === 'success' ? 'Success!' : 'Error!' }}',
            text: '{{ session('message') }}',
            confirmButtonColor: '#667eea'
        });
    @endif

    // Privacy reminder logic
    const startBtn = document.getElementById('startEvaluationBtn');
    const privacyReminder = document.getElementById('privacyReminder');
    const wrapper = document.getElementById('selectionAndEvaluationWrapper');
    
    if (startBtn && privacyReminder && wrapper) {
        startBtn.addEventListener('click', function() {
            privacyReminder.classList.remove('d-flex');
            privacyReminder.classList.add('d-none');
            wrapper.classList.remove('d-none');
            wrapper.style.display = 'block'; // Keep for compatibility but d-none will override if present
        });
    }

    // Initialize all popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Close popovers when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('[data-bs-toggle="popover"]') && !e.target.closest('.popover')) {
            popoverList.forEach(function (p) {
                p.hide();
            });
        }
    });

    // Handle radio button styling
    document.addEventListener('change', function(e) {
        if (e.target.type === 'radio' && e.target.name.startsWith('responses[')) {
            const container = e.target.closest('.response-options-group');
            if (container) {
                container.querySelectorAll('.form-check-label').forEach(label => {
                    label.classList.remove('selected-rating');
                });
                e.target.closest('.form-check-label').classList.add('selected-rating');
            }
        }
    });

    // Search functionality
    const searchInput = document.getElementById('staffSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const activeTab = document.querySelector('.tab-pane.active');
            const items = activeTab.querySelectorAll('.staff-item');
            
            // Check if we're in locked state
            const isLocked = dbHasLockedSelection && selectedStaff.length > 0;
            
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const id = item.getAttribute('data-id');
                const checkbox = item.querySelector('.select-staff-checkbox');
                const staffId = checkbox ? parseInt(checkbox.getAttribute('data-staff-id')) : null;
                
                // In locked state, only show items that are in selectedStaff
                if (isLocked) {
                    const isSelected = selectedStaff.some(s => s.id === staffId);
                    if (!isSelected) {
                        item.classList.add('d-none');
                        return;
                    }
                }
                
                // Then apply search filter
                if (name.includes(searchTerm) || id.includes(searchTerm)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    }

    // Form submission confirmation
    const evalForm = document.getElementById('irregularEvalForm');
    if (evalForm) {
        evalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation: Ensure all questions for the CURRENT staff are answered
            const currentContainer = activeStaffId ? (!document.getElementById('teachingQuestionsContainer').classList.contains('d-none') ? 
                document.getElementById('teachingQuestionsContainer') : 
                document.getElementById('nonTeachingQuestionsContainer')) : null;
            
            if (currentContainer) {
                const questionGroups = currentContainer.querySelectorAll('.response-options-group');
                let answeredCount = 0;
                questionGroups.forEach(group => {
                    if (group.querySelector('input[type="radio"]:checked')) {
                        answeredCount++;
                    }
                });

                const staff = selectedStaff.find(s => s.id == activeStaffId);
                const actualQuestionCount = staff.type === 'teaching' ? {{ $teachingQuestions->count() }} : {{ $nonTeachingQuestions->count() }};

                if (answeredCount < actualQuestionCount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Evaluation',
                        text: `Please answer all ${actualQuestionCount} questions for ${staff.name}.`,
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
            }
            
            Swal.fire({
                title: 'Submit Evaluation?',
                text: "You are about to submit your evaluation for this staff member. This action cannot be undone.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, submit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = document.getElementById('submitEvalBtn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                    
                    // No need for prepareBulkSubmission, we submit one by one like evaluates.blade
                    this.submit();
                }
            });
        });
    }
    
    // Watch for any DOM changes when in locked mode - restore locked state if cards visibility changes
    if (dbHasLockedSelection) {
        const observer = new MutationObserver(function(mutations) {
            // Check if any staff-item visibility has changed unexpectedly
            const container = document.getElementById('staffListSection');
            if (container) {
                const items = container.querySelectorAll('.staff-item');
                let visibleCount = 0;
                items.forEach(item => {
                    if (!item.classList.contains('d-none')) {
                        visibleCount++;
                    }
                });
                
                // If more cards are visible than selected staff, restore locked state
                if (visibleCount > selectedStaff.length && selectedStaff.length > 0) {
                    console.log('Detected unexpected card visibility change - restoring locked state');
                    applyLockedUIState();
                }
            }
        });
        
        const staffListSection = document.getElementById('staffListSection');
        if (staffListSection) {
            observer.observe(staffListSection, {
                attributes: true,
                attributeFilter: ['class', 'style'],
                subtree: true,
                childList: false
            });
        }
    }
});


function saveSelectionState(locked = false) {
    localStorage.setItem('ireval_selected_staff', JSON.stringify(selectedStaff));
    localStorage.setItem('ireval_selection_locked', locked ? 'true' : 'false');
    localStorage.setItem('ireval_staff_type', currentStaffType);
    updateStartButtonText();
}

function restoreLockedSelectionFromDatabase() {
    // Determine which staff type has locked selections
    const hasTeachingLocked = dbLockedSelections.teaching && dbLockedSelections.teaching.length > 0;
    const hasNonTeachingLocked = dbLockedSelections['non-teaching'] && dbLockedSelections['non-teaching'].length > 0;
    
    console.log('Restoring from DB - Teaching:', hasTeachingLocked, 'NonTeaching:', hasNonTeachingLocked);
    console.log('dbLockedSelections:', dbLockedSelections);
    
    if (hasTeachingLocked) {
        currentStaffType = 'teaching';
        selectedStaff = dbLockedSelections.teaching.map(s => ({
            id: parseInt(s.id), // Ensure it's a number
            type: 'teaching',
            name: s.name
        }));
        console.log('Selected Teaching Staff:', selectedStaff);
    } else if (hasNonTeachingLocked) {
        currentStaffType = 'non-teaching';
        selectedStaff = dbLockedSelections['non-teaching'].map(s => ({
            id: parseInt(s.id), // Ensure it's a number
            type: 'non-teaching',
            name: s.name
        }));
        console.log('Selected NonTeaching Staff:', selectedStaff);
    } else {
        console.log('No locked selections found in database');
        return; // No locked selection
    }

    // Apply locked UI state immediately
    applyLockedUIState();
}

function applyLockedUIState() {
    // Hide privacy reminder and show wrapper
    const privacyReminder = document.getElementById('privacyReminder');
    const wrapper = document.getElementById('selectionAndEvaluationWrapper');
    if (privacyReminder && wrapper) {
        privacyReminder.classList.add('d-none');
        wrapper.classList.remove('d-none');
        wrapper.style.display = 'block';
    }

    // Switch to correct tab if non-teaching
    if (currentStaffType === 'non-teaching') {
        const nonTeachingTab = document.getElementById('non-teaching-tab');
        if (nonTeachingTab) {
            try {
                const bootstrapTab = new bootstrap.Tab(nonTeachingTab);
                bootstrapTab.show();
            } catch(e) {
                console.warn('Bootstrap Tab error:', e);
                nonTeachingTab.click();
            }
        }
    }

    // Filter UI: Show only locked/selected items
    // Only process items in the currently active tab
    const activeTabPane = document.querySelector('.tab-pane.active');
    if (!activeTabPane) {
        console.warn('No active tab pane found');
        return;
    }
    
    const items = activeTabPane.querySelectorAll('.staff-item');
    console.log('Total items in active tab:', items.length, 'Selected staff:', selectedStaff);
    
    items.forEach(item => {
        const card = item.querySelector('.staff-card');
        const checkbox = item.querySelector('.select-staff-checkbox');
        const staffId = checkbox ? parseInt(checkbox.getAttribute('data-staff-id')) : null;
        
        // Check if this staff ID is in selected array
        const isSelected = selectedStaff.some(s => {
            const match = s.id === staffId;
            if (match) console.log('Matched staff ID:', staffId);
            return match;
        });

        if (isSelected) {
            item.classList.remove('d-none');
            if (card) card.classList.add('selected');
            
            // Show evaluate button and ENABLE it (locked state)
            const evalBtnWrapper = item.querySelector('.evaluate-btn-wrapper');
            if (evalBtnWrapper) {
                evalBtnWrapper.style.display = 'block';
                const btn = evalBtnWrapper.querySelector('button');
                if (btn) btn.disabled = false;
            }
            // Hide checkbox
            const cbWrapper = item.querySelector('.checkbox-wrapper');
            if (cbWrapper) cbWrapper.style.display = 'none';
        } else {
            item.classList.add('d-none');
        }
    });

    // Hide search container
    const searchContainer = document.querySelector('.staff-search-container');
    if (searchContainer) {
        searchContainer.classList.add('d-none');
        searchContainer.style.display = 'none';
    }

    // Toggle to LOCKED state (hide review controls, show locked controls)
    if (currentStaffType === 'teaching') {
        const initialControls = document.getElementById('initialControls');
        const reviewControls = document.getElementById('reviewControls');
        const lockedControls = document.getElementById('lockedControls');
        if (initialControls) initialControls.style.display = 'none';
        if (reviewControls) reviewControls.style.display = 'none';
        if (lockedControls) lockedControls.style.display = 'block';
    } else {
        const initialControlsNT = document.getElementById('initialControlsNonTeaching');
        const reviewControlsNT = document.getElementById('reviewControlsNonTeaching');
        const lockedControlsNT = document.getElementById('lockedControlsNonTeaching');
        if (initialControlsNT) initialControlsNT.style.display = 'none';
        if (reviewControlsNT) reviewControlsNT.style.display = 'none';
        if (lockedControlsNT) lockedControlsNT.style.display = 'block';
    }

    // Update start button text
    updateStartButtonText();
    console.log('Locked UI state applied successfully for', currentStaffType);
}

function clearSelectionState() {
    localStorage.removeItem('ireval_selected_staff');
    localStorage.removeItem('ireval_selection_locked');
    localStorage.removeItem('ireval_staff_type');
    updateStartButtonText();
}

function updateStartButtonText() {
    const startBtn = document.getElementById('startEvaluationBtn');
    if (!startBtn) return;
    
    const isLocked = localStorage.getItem('ireval_selection_locked') === 'true';
    if (isLocked) {
        startBtn.innerHTML = '<i class=\"fas fa-play me-2\"></i>Continue Evaluation';
        startBtn.className = 'btn btn-primary px-3 px-md-4 py-2 fw-bold rounded-pill';
    } else {
        startBtn.innerHTML = '<i class=\"fas fa-play me-2\"></i>Select Specific Instructors';
        startBtn.className = 'btn btn-success px-3 px-md-4 py-2 fw-bold rounded-pill';
    }
}

function restoreSelectionState() {
    const savedStaff = localStorage.getItem('ireval_selected_staff');
    const isLocked = localStorage.getItem('ireval_selection_locked') === 'true';
    const savedType = localStorage.getItem('ireval_staff_type') || 'teaching';

    if (savedStaff) {
        selectedStaff = JSON.parse(savedStaff);
        currentStaffType = savedType;
        
        // Update checkboxes and cards visual state
        selectedStaff.forEach(staff => {
            const checkbox = document.querySelector(`.select-staff-checkbox[data-staff-id=\"${staff.id}\"]`);
            if (checkbox) {
                checkbox.checked = true;
                const card = checkbox.closest('.staff-card');
                if (card) card.classList.add('selected');
            }
        });

        // Update staff type tab
        if (savedType === 'non-teaching') {
            const nonTeachingTab = document.getElementById('non-teaching-tab');
            if (nonTeachingTab) nonTeachingTab.click();
        }

        if (isLocked) {
            // Apply locked UI state
            const container = document.getElementById('staffListSection');
            const items = container.querySelectorAll('.staff-item');
            
            items.forEach(item => {
                const checkbox = item.querySelector('.select-staff-checkbox');
                const staffId = checkbox ? checkbox.getAttribute('data-staff-id') : null;
                const isSelected = selectedStaff.some(s => s.id == staffId);

                if (isSelected) {
                    item.classList.remove('d-none');
                    const evalBtnWrapper = item.querySelector('.evaluate-btn-wrapper');
                    if (evalBtnWrapper) {
                        evalBtnWrapper.style.display = 'block';
                        const btn = evalBtnWrapper.querySelector('button');
                        if (btn) btn.disabled = false;
                    }
                    const cbWrapper = item.querySelector('.checkbox-wrapper');
                    if (cbWrapper) cbWrapper.style.display = 'none';
                } else {
                    item.classList.add('d-none');
                }
            });

            // Toggle controls to Locked state
            if (currentStaffType === 'teaching') {
                document.getElementById('initialControls').style.display = 'none';
                document.getElementById('reviewControls').style.display = 'none';
                document.getElementById('lockedControls').style.display = 'block';
                document.getElementById('lockedControls').classList.remove('d-none');
            } else {
                document.getElementById('initialControlsNonTeaching').style.display = 'none';
                document.getElementById('reviewControlsNonTeaching').style.display = 'none';
                document.getElementById('lockedControlsNonTeaching').style.display = 'block';
                document.getElementById('lockedControlsNonTeaching').classList.remove('d-none');
            }

            // Hide search
            const searchContainer = document.querySelector('.staff-search-container');
            if (searchContainer) searchContainer.style.display = 'none';

            // Auto-open the selection wrapper and hide privacy notice
            const privacyReminder = document.getElementById('privacyReminder');
            const wrapper = document.getElementById('selectionAndEvaluationWrapper');
            if (privacyReminder && wrapper) {
                privacyReminder.classList.add('d-none');
                wrapper.classList.remove('d-none');
                wrapper.style.display = 'block';
            }
        }
        
        // Enable Done button if not locked
        const doneBtn = currentStaffType === 'teaching' ? document.getElementById('doneSelectionBtn') : document.getElementById('doneNonTeachingBtn');
        if (doneBtn) doneBtn.disabled = selectedStaff.length === 0;
    }
    updateStartButtonText();
}
function loadEvaluation(staffId) {
    const container = document.getElementById('evaluationFormSection');
    // Clear all radios and textareas first
    container.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
        radio.closest('.form-check-label')?.classList.remove('selected-rating');
    });
    container.querySelector('textarea[name="comments"]').value = '';
}

function setStaffType(type) {
    if (selectedStaff.length > 0 && document.getElementById('initialControls').classList.contains('d-none')) {
        // Selection is locked, don't allow switching types unless they edit
        Swal.fire({
            icon: 'warning',
            title: 'Selection Locked',
            text: 'Please click "Edit Selection" to change staff type or modify your selection.',
            confirmButtonColor: '#667eea'
        });
        
        // Switch back visual state
        const teachingTab = document.getElementById('teaching-tab');
        const nonTeachingTab = document.getElementById('non-teaching-tab');
        if (currentStaffType === 'teaching') {
            teachingTab.classList.add('active');
            nonTeachingTab.classList.remove('active');
            document.getElementById('teaching-content').classList.add('show', 'active');
            document.getElementById('non-teaching-content').classList.remove('show', 'active');
        } else {
            teachingTab.classList.remove('active');
            nonTeachingTab.classList.add('active');
            document.getElementById('teaching-content').classList.remove('show', 'active');
            document.getElementById('non-teaching-content').classList.add('show', 'active');
        }
        return;
    }

    currentStaffType = type;
    
    // Check if we have locked selection from database
    const isLocked = dbHasLockedSelection && 
                     ((type === 'teaching' && dbLockedSelections.teaching && dbLockedSelections.teaching.length > 0) ||
                      (type === 'non-teaching' && dbLockedSelections['non-teaching'] && dbLockedSelections['non-teaching'].length > 0));
    
    // If switching to teaching tab and we have locked selection, restore it
    if (type === 'teaching' && isLocked && dbHasLockedSelection) {
        // Restore locked state after tab switch completes
        setTimeout(function() {
            applyLockedUIState();
            console.log('Restored locked state in setStaffType for teaching');
        }, 100);
        // Don't clear search or show all items if locked
    } else {
        // Clear search when switching tabs (only if not locked)
        const searchInput = document.getElementById('staffSearch');
        if (searchInput) {
            searchInput.value = '';
            const allItems = document.querySelectorAll('.staff-item');
            allItems.forEach(item => item.classList.remove('d-none'));
        }
    }

    // Show/hide selection controls based on type
    if (type === 'teaching') {
        document.getElementById('selectionControls').classList.remove('d-none');
        document.getElementById('selectionControls').style.display = 'block';
        document.getElementById('selectionControlsNonTeaching').classList.add('d-none');
        document.getElementById('selectionControlsNonTeaching').style.display = 'none';
    } else {
        document.getElementById('selectionControls').classList.add('d-none');
        document.getElementById('selectionControls').style.display = 'none';
        document.getElementById('selectionControlsNonTeaching').classList.remove('d-none');
        document.getElementById('selectionControlsNonTeaching').style.display = 'block';
    }
}

function handleStaffSelection(cardElement, id, type, name, evaluated, subject = '') {
    if (evaluated) {
        Swal.fire({
            icon: 'info',
            title: 'Already Evaluated',
            text: 'You have already submitted an evaluation for ' + name + ' in the current semester.',
            confirmButtonColor: '#667eea'
        });
        return;
    }

    if (document.getElementById('initialControls').classList.contains('d-none') && 
        document.getElementById('initialControlsNonTeaching').classList.contains('d-none')) {
        // Selection is locked
        return;
    }

    // Find the checkbox inside this card
    const checkbox = cardElement.querySelector('.select-staff-checkbox');
    if (checkbox && !checkbox.disabled) {
        checkbox.checked = !checkbox.checked;
        
        // Update selected visual state
        if (checkbox.checked) {
            cardElement.classList.add('selected');
            selectedStaff.push({ id, type, name, subject }); saveSelectionState();
        } else {
            cardElement.classList.remove('selected');
            selectedStaff = selectedStaff.filter(s => s.id !== id); saveSelectionState();
        }

        // Enable/Disable Done button
        const doneBtn = currentStaffType === 'teaching' ? document.getElementById('doneSelectionBtn') : document.getElementById('doneNonTeachingBtn');
        if (doneBtn) doneBtn.disabled = selectedStaff.length === 0;
    }
}

function confirmSelection() {
    if (selectedStaff.length === 0) return;

    // Filter UI: Show only selected, hide others
    const container = document.getElementById('staffListSection');
    const items = container.querySelectorAll('.staff-item');
    
    items.forEach(item => {
        const card = item.querySelector('.staff-card');
        if (card.classList.contains('selected')) {
            item.classList.remove('d-none');
            // Show evaluate button but keep it DISABLED for now
            const evalBtnWrapper = item.querySelector('.evaluate-btn-wrapper');
            if (evalBtnWrapper) {
                evalBtnWrapper.style.display = 'block';
                const btn = evalBtnWrapper.querySelector('button');
                if (btn) btn.disabled = true;
            }
            // Hide checkbox
            const cbWrapper = item.querySelector('.checkbox-wrapper');
            if (cbWrapper) cbWrapper.style.display = 'none';
        } else {
            item.classList.add('d-none');
        }
    });

    // Toggle controls to Review state
    if (currentStaffType === 'teaching') {
        document.getElementById('initialControls').classList.add('d-none');
        document.getElementById('initialControls').style.display = 'none';
        document.getElementById('reviewControls').classList.remove('d-none');
        document.getElementById('reviewControls').style.display = 'block';
        document.getElementById('lockedControls').classList.add('d-none');
        document.getElementById('lockedControls').style.display = 'none';
    } else {
        document.getElementById('initialControlsNonTeaching').classList.add('d-none');
        document.getElementById('initialControlsNonTeaching').style.display = 'none';
        document.getElementById('reviewControlsNonTeaching').classList.remove('d-none');
        document.getElementById('reviewControlsNonTeaching').style.display = 'block';
        document.getElementById('lockedControlsNonTeaching').classList.add('d-none');
        document.getElementById('lockedControlsNonTeaching').style.display = 'none';
    }

    // Hide search
    const searchContainer = document.querySelector('.staff-search-container');
    if (searchContainer) {
        searchContainer.classList.add('d-none');
        searchContainer.style.display = 'none';
    }
}

function finalConfirmSelection() {
    Swal.fire({
        title: 'Finalize Selection?',
        text: "This will lock your instructor selection for evaluation. You won't be able to edit the selection after this.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Yes, lock it!',
        cancelButtonText: 'Wait, let me check'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to save locked selection to database
            const staffIds = selectedStaff.map(s => s.id);
            
            fetch('{{ route("selection.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    staff_ids: staffIds,
                    staff_type: currentStaffType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Enable all evaluation buttons
                    const container = document.getElementById('staffListSection');
                    const evalBtns = container.querySelectorAll('.evaluate-btn-wrapper button');
                    evalBtns.forEach(btn => btn.disabled = false);

                    // Toggle controls to Locked state
                    if (currentStaffType === 'teaching') {
                        document.getElementById('reviewControls').classList.add('d-none');
                        document.getElementById('reviewControls').style.display = 'none';
                        document.getElementById('lockedControls').classList.remove('d-none');
                        document.getElementById('lockedControls').style.display = 'block';
                    } else {
                        document.getElementById('reviewControlsNonTeaching').classList.add('d-none');
                        document.getElementById('reviewControlsNonTeaching').style.display = 'none';
                        document.getElementById('lockedControlsNonTeaching').classList.remove('d-none');
                        document.getElementById('lockedControlsNonTeaching').style.display = 'block';
                    }

                    // Save locked state to localStorage
                    saveSelectionState(true);

                    Swal.fire({
                        icon: 'success',
                        title: 'Selection Locked',
                        text: 'You can now proceed with the evaluations. Your selection is saved and will persist across reloads.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to confirm selection',
                        confirmButtonColor: '#667eea'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while confirming your selection.',
                    confirmButtonColor: '#667eea'
                });
            });
        }
    });
}

function unlockSelection() {
    Swal.fire({
        title: 'Unlock Selection?',
        text: "This will allow you to edit your instructor selection. You'll need to confirm again before evaluating.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Yes, unlock it!',
        cancelButtonText: 'Keep it locked'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to unlock selection
            fetch('{{ route("selection.unlock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear local state
                    selectedStaff = [];
                    clearSelectionState();
                    
                    // Show all items
                    const container = document.getElementById('staffListSection');
                    const items = container.querySelectorAll('.staff-item');
                    
                    items.forEach(item => {
                        item.classList.remove('d-none');
                        const card = item.querySelector('.staff-card');
                        card.classList.remove('selected');
                        
                        // Hide evaluate button
                        const evalBtnWrapper = item.querySelector('.evaluate-btn-wrapper');
                        if (evalBtnWrapper) {
                            evalBtnWrapper.style.display = 'none';
                            const btn = evalBtnWrapper.querySelector('button');
                            if (btn) btn.disabled = true;
                        }
                        // Show checkbox
                        const cbWrapper = item.querySelector('.checkbox-wrapper');
                        if (cbWrapper) {
                            cbWrapper.style.display = 'block';
                            const checkbox = cbWrapper.querySelector('input[type="checkbox"]');
                            if (checkbox) checkbox.checked = false;
                        }
                    });

                    // Toggle controls back to Initial state
                    if (currentStaffType === 'teaching') {
                        document.getElementById('initialControls').classList.remove('d-none');
                        document.getElementById('initialControls').style.display = 'block';
                        document.getElementById('reviewControls').classList.add('d-none');
                        document.getElementById('reviewControls').style.display = 'none';
                        document.getElementById('lockedControls').classList.add('d-none');
                        document.getElementById('lockedControls').style.display = 'none';
                    } else {
                        document.getElementById('initialControlsNonTeaching').classList.remove('d-none');
                        document.getElementById('initialControlsNonTeaching').style.display = 'block';
                        document.getElementById('reviewControlsNonTeaching').classList.add('d-none');
                        document.getElementById('reviewControlsNonTeaching').style.display = 'none';
                        document.getElementById('lockedControlsNonTeaching').classList.add('d-none');
                        document.getElementById('lockedControlsNonTeaching').style.display = 'none';
                    }

                    // Show search
                    const searchContainer = document.querySelector('.staff-search-container');
                    if (searchContainer) {
                        searchContainer.classList.remove('d-none');
                        searchContainer.style.display = 'block';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Selection Unlocked',
                        text: 'You can now edit your instructor selection.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to unlock selection',
                        confirmButtonColor: '#667eea'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while unlocking your selection.',
                    confirmButtonColor: '#667eea'
                });
            });
        }
    });
}

function openEvaluation(id, type, name) {
    activeStaffId = id;
    
    // Set form data
    updateFormStaffData(id, type, name);
    
    // Switch sections
    document.getElementById('navigationSection').classList.add('d-none');
    document.getElementById('navigationSection').style.display = 'none';
    document.getElementById('staffListSection').classList.add('d-none');
    document.getElementById('staffListSection').style.display = 'none';
    document.getElementById('evaluationFormSection').classList.remove('d-none');
    document.getElementById('evaluationFormSection').style.display = 'block';
    
    // Scroll to top
    window.scrollTo({ top: document.querySelector('.evaluation-card').offsetTop - 20, behavior: 'smooth' });
}

function updateFormStaffData(id, type, name) {
    activeStaffId = id;
    document.getElementById('selectedStaffName').textContent = name;
    document.getElementById('selectedStaffTypeLabel').textContent = type === 'teaching' ? 'Instructor' : 'Non-Teaching Staff';
    
    const icon = document.getElementById('selectedStaffIcon');
    icon.className = type === 'teaching' ? 'fas fa-chalkboard-teacher text-primary fa-lg' : 'fas fa-users-cog text-success fa-lg';

    // Show/Hide question containers
    const teachingContainer = document.getElementById('teachingQuestionsContainer');
    const nonTeachingContainer = document.getElementById('nonTeachingQuestionsContainer');
    
    if (type === 'teaching') {
        teachingContainer.classList.remove('d-none');
        teachingContainer.style.display = 'block';
        nonTeachingContainer.classList.add('d-none');
        nonTeachingContainer.style.display = 'none';
        toggleInputs(teachingContainer, true);
        toggleInputs(nonTeachingContainer, false);
    } else {
        teachingContainer.classList.add('d-none');
        teachingContainer.style.display = 'none';
        nonTeachingContainer.classList.remove('d-none');
        nonTeachingContainer.style.display = 'block';
        toggleInputs(teachingContainer, false);
        toggleInputs(nonTeachingContainer, true);
    }

    // Load saved evaluation for this staff
    loadEvaluation(id);
    
    // Update the hidden input for staff IDs (the form submission handles bulk via dynamic inputs in prepareBulkSubmission)
    // But we need to make sure the main staff_id and staff_type are set for the form
    document.getElementById('formStaffId').value = id;
    document.getElementById('formStaffType').value = type;
}

function showStaffList() {
    // We stay in the "Selection Locked" view if it was already confirmed
    const isLocked = document.getElementById('initialControls').classList.contains('d-none') || 
                     document.getElementById('initialControlsNonTeaching').classList.contains('d-none');
    
    if (!isLocked) {
        document.getElementById('navigationSection').classList.remove('d-none');
        document.getElementById('navigationSection').style.display = 'block';
    }
    
    document.getElementById('staffListSection').classList.remove('d-none');
    document.getElementById('staffListSection').style.display = 'block';
    document.getElementById('evaluationFormSection').classList.add('d-none');
    document.getElementById('evaluationFormSection').style.display = 'none';
}

function toggleInputs(container, enabled) {
    const inputs = container.querySelectorAll('input');
    inputs.forEach(input => {
        input.disabled = !enabled;
    });
}
</script>
