@php
    $teachingEvaluated = $teachingEvaluated ?? 0;
    $nonTeachingEvaluated = $nonTeachingEvaluated ?? 0;
    $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
    $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();
    
    // Get the active academic year
    $currentAcademicYear = $currentAcademicYear ?? \App\Models\AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
    $activeAcademicYearId = $currentAcademicYear ? $currentAcademicYear->id : null;

    $isOpen = $isOpen ?? ($activeAcademicYearId ? \App\Models\Question::where('academic_year_id', $activeAcademicYearId)->where('is_open', 1)->exists() : false);
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
.tab-content::before,
.form-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.03"/><circle cx="90" cy="40" r="0.5" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}
.form-gradient {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}
.section-title {
    color: #2d3748;
    font-weight: 800;
    font-size: 0.95rem;
    margin-bottom: 15px;
    position: relative;
    padding-left: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 16px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
}
.question-text {
    font-size: 0.9rem;
    line-height: 1.4;
    color: #2d3748;
    margin-bottom: 8px;
}
.question-number {
    color: #667eea;
    font-weight: 800;
    margin-right: 5px;
}
.transform-hover {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.transform-hover:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4) !important;
}

/* Status Badges */
.status-badges {
    gap: 0.75rem !important;
    flex-wrap: nowrap !important;
}
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

.staff-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 15px;
    overflow: hidden;
}
.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.staff-card.selected {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
}
.staff-card.evaluated {
    border-color: #48bb78;
    background-color: rgba(72, 187, 120, 0.05);
    opacity: 0.8;
}

.response-options-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 0.35rem !important;
    justify-content: flex-start !important;
    flex-wrap: nowrap;
}
@media (max-width: 768px) {
    .response-options-group {
        flex-direction: column !important;
        align-items: stretch !important;
        flex-wrap: wrap;
        gap: 0.25rem !important;
    }
}
.form-check-label {
    padding: 1px 6px;
    border-radius: 8px;
    font-size: 0.7rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    margin-bottom: 0;
}
.form-check-label:hover {
    background: #f7fafc;
    transform: translateY(-1px);
}
.form-check-label.selected-rating {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 15px;
    font-weight: 600;
    transform: scale(1.02);
    transition: all 0.3s ease;
}
.form-check-input {
    margin-right: 2px;
    width: 0.7em;
    height: 0.7em;
}

/* Form Heading & Button Reductions */
#evaluationFormSection h2 {
    font-size: 1.2rem !important;
    margin-bottom: 2px !important;
}
#evaluationFormSection .h5 {
    font-size: 0.95rem !important;
}
#evaluationFormSection .badge {
    font-size: 0.7rem !important;
    padding: 4px 10px !important;
}
#evaluationFormSection .btn-outline-primary {
    font-size: 0.8rem !important;
    padding: 5px 15px !important;
}
#selectedStaffSubjectLabel {
    font-size: 0.8rem !important;
}
#submitEvalBtn {
    font-size: 0.9rem !important;
    padding: 0.6rem 1.8rem !important;
    letter-spacing: 0.4px;
}
.staff-info-header .bg-white {
    width: 50px !important;
    height: 50px !important;
    border-width: 1px !important;
}
.staff-info-header i.fa-2x {
    font-size: 1.2rem !important;
}
.evaluation-card .card-body {
    padding: 1.5rem !important;
}
@media (max-width: 768px) {
    .evaluation-card .card-body {
        padding: 1rem !important;
    }
}

/* Completion Overlay Styles */
.completion-overlay {
    position: fixed;
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
.evaluated-popover-content {
    font-size: 1.08rem;
    color: #333;
    min-width: 180px;
    padding: 0.5em 0.2em;
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

.header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px 20px 0 0;
    position: relative;
    overflow: hidden;
}

.header-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

@media (max-width: 576px) {
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
    .custom-nav-tabs {
        flex-direction: row !important;
        padding: 4px;
    }
    .custom-nav-tabs .nav-link {
        padding: 8px 6px;
        font-size: 0.8rem;
        gap: 6px;
    }
    .tab-icon {
        font-size: 1rem;
    }
}
</style>

@php
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
    
    $allEvaluationsCompleted = false;
    $totalLockedCount = $lockedSelections['teaching']->count() + $lockedSelections['non-teaching']->count();
    $totalEvaluatedStaff = count($distinctStaffIds);
    
    if ($totalLockedCount > 0 && $totalEvaluatedStaff >= $totalLockedCount) {
        $allEvaluationsCompleted = true;
    }
@endphp

<div class="row page-full-width evaluations-page justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm evaluation-card" style="position: relative;">
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
                
                <!-- Closed notification message -->
                @if(!$isOpen)
                    <div class="w-100 mb-3">
                        <div class="alert alert-warning mb-0 text-center">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Questions are temporarily closed by the admin.</strong> Please wait for them to open to start evaluation.
                        </div>
                    </div>
                @endif
                
                <!-- Status and Badge Container -->
                <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap status-badges">
                    <!-- Notification badge for evaluated staff -->
                    <button type="button" class="btn position-relative p-0" id="evaluatedStaffBadge" data-bs-toggle="popover" data-bs-trigger="focus" title="Evaluated Staff Breakdown" data-bs-html="true" data-bs-content="
                        <div class='evaluated-popover-content'>
                            <div class='evaluated-popover-label'><i class='fas fa-chalkboard-teacher'></i> <span>Teaching Staff:</span> <strong>{{ $teachingCount }}</strong></div>
                            <div class='evaluated-popover-label'><i class='fas fa-users-cog'></i> <span>Non-Teaching Staff:</span> <strong>{{ $nonTeachingCount }}</strong></div>
                        </div>">
                        <span class="badge evaluated-badge-red">
                            {{ count($distinctStaffIds) }}
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
                    <div class="text-center py-5">
                        <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                        <h5>Evaluation is currently closed</h5>
                        <p class="text-muted">Please check back later once the administrator opens the evaluation period.</p>
                    </div>
                @else
                    <div id="lockedCardsContainer">
                        <div class="alert alert-success d-flex align-items-center rounded-pill mb-4 shadow-sm mx-auto" style="max-width: 600px;">
                            <i class="fas fa-lock me-2 ms-2"></i>
                            <span>Your instructor selection is locked. You can now proceed with the evaluation.</span>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill ms-auto me-2" onclick="unlockSelection()">
                                <i class="fas fa-unlock me-1"></i>Unlock
                            </button>
                        </div>

                        <div id="staffListSection">
                            <div class="tab-content">
                                <div class="row g-3">
                                    @foreach($lockedSelections['teaching'] as $selection)
                                        @php 
                                            $staff = $selection->staff;
                                            if (!$staff) continue;
                                            $staffId = $staff->id;
                                            $instructorName = $staff->full_name;
                                            $evaluated = in_array($staffId, $evaluatedTeachingIds);
                                            
                                            // Find subjects for this instructor
                                            $subjects = $studentSubjects->where('assign_instructor', $instructorName);
                                            $subjectNames = $subjects->pluck('sub_name')->unique()->implode(', ');
                                            if (empty($subjectNames)) $subjectNames = "No subject assigned";
                                        @endphp
                                        <div class="col-md-6 staff-item">
                                            <div class="card staff-card h-100 selected {{ $evaluated ? 'evaluated' : '' }}" 
                                                 onclick="{{ $evaluated ? "Swal.fire({icon:'info', title:'Already Evaluated', text:'You have already evaluated this instructor.', confirmButtonColor:'#667eea'})" : "openEvaluation($staffId, 'teaching', '" . addslashes($instructorName) . "', '" . addslashes($subjectNames) . "')" }}">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                                                                @if($staff->profile_image)
                                                                    <img src="{{ asset('storage/' . $staff->profile_image) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                                                @else
                                                                    <i class="fas fa-user-tie text-primary fa-lg"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0 fw-bold text-dark">{{ $instructorName }}</h6>
                                                            <small class="text-muted d-flex align-items-center">
                                                                <i class="fas fa-book me-1"></i>
                                                                <span class="text-truncate" style="max-width: 180px; display: inline-block;">{{ $subjectNames }}</span>
                                                            </small>
                                                        </div>
                                                        @if($evaluated)
                                                            <div class="ms-2">
                                                                <span class="badge bg-success text-white rounded-pill shadow-sm">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-auto">
                                                        <button type="button" class="btn btn-sm btn-primary w-100 rounded-pill py-2 fw-bold btn-open-evaluation" 
                                                            {{ $evaluated ? 'disabled' : '' }}
                                                            onclick="event.stopPropagation(); openEvaluation({{ $staffId }}, 'teaching', '{{ addslashes($instructorName) }}', '{{ addslashes($subjectNames) }}')">
                                                            <i class="fas fa-file-alt me-1"></i> {{ $evaluated ? 'Evaluated' : 'Open Evaluation Form' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach($lockedSelections['non-teaching'] as $selection)
                                        @php 
                                            $staff = $selection->staff;
                                            if (!$staff) continue;
                                            $staffId = $staff->id;
                                            $staffName = $staff->full_name;
                                            $evaluated = in_array($staffId, $evaluatedNonTeachingIds);
                                        @endphp
                                        <div class="col-md-6 staff-item">
                                            <div class="card staff-card h-100 selected {{ $evaluated ? 'evaluated' : '' }}" 
                                                 onclick="{{ $evaluated ? "Swal.fire({icon:'info', title:'Already Evaluated', text:'You have already evaluated this instructor.', confirmButtonColor:'#667eea'})" : "openEvaluation($staffId, 'non-teaching', '" . addslashes($staffName) . "')" }}">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                                                                @if($staff->profile_image)
                                                                    <img src="{{ asset('storage/' . $staff->profile_image) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                                                @else
                                                                    <i class="fas fa-user-cog text-success fa-lg"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0 fw-bold text-dark">{{ $staffName }}</h6>
                                                            <p class="mb-0 text-muted"><small>Non-Teaching Staff</small></p>
                                                        </div>
                                                        @if($evaluated)
                                                            <div class="ms-2">
                                                                <span class="badge bg-success text-white rounded-pill shadow-sm">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-auto">
                                                        <button type="button" class="btn btn-sm btn-success w-100 rounded-pill py-2 fw-bold btn-open-evaluation" 
                                                            {{ $evaluated ? 'disabled' : '' }}
                                                            onclick="event.stopPropagation(); openEvaluation({{ $staffId }}, 'non-teaching', '{{ addslashes($staffName) }}')">
                                                            <i class="fas fa-file-alt me-1"></i> {{ $evaluated ? 'Evaluated' : 'Open Evaluation Form' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div id="evaluationFormSection" style="display: none;">
                            <div class="card evaluation-card border-0 shadow-lg form-gradient">
                                <div class="p-2 px-3 border-bottom bg-white bg-opacity-50">
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1 shadow-sm me-3" onclick="showStaffList()" style="font-size: 0.75rem;">
                                            <i class="fas fa-arrow-left me-1"></i> Back
                                        </button>
                                        
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-dark" id="selectedStaffNameDisplay" style="font-size: 0.9rem; line-height: 1.1;">Instructor Name</h6>
                                            <div id="selectedStaffSubjectLabel" class="text-muted" style="font-size: 0.7rem;">
                                                <i class="fas fa-book me-1"></i> Subjects
                                            </div>
                                            <!-- Hidden elements to maintain JS functionality -->
                                            <span id="selectedStaffTypeLabel" style="display:none;"></span>
                                            <i id="selectedStaffIcon" style="display:none;"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-4 p-md-5">
                                    <form method="POST" action="{{ route('evaluations.submit') }}" id="irregularEvalForm">
                                        @csrf
                                        <input type="hidden" name="staff_id" id="formStaffId">
                                        <input type="hidden" name="staff_type" id="formStaffType">
                                        
                                        <div id="teachingQuestionsContainer">
                                            @foreach($teachingQuestions->groupBy('title') as $title => $questionsGroup)
                                                <div class="mb-4">
                                                    <label class="section-title mb-3">{{ $title }}</label>
                                                    @foreach($questionsGroup as $question)
                                                        <div class="mb-3 ms-1 p-2 px-3 bg-white bg-opacity-40 rounded-3 shadow-sm border border-white">
                                                            <div class="question-text">
                                                                <span class="question-number">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</span>
                                                                {{ $question->question_text }}
                                                            </div>
                                                            @if($question->description)
                                                                <p class="text-muted small mb-3 ms-4 fst-italic">{{ $question->description }}</p>
                                                            @endif
                                                            <div class="d-flex flex-wrap justify-content-center response-options-group mt-3">
                                                                @php $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get(); @endphp
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
                                        </div>

                                        <div id="nonTeachingQuestionsContainer" style="display: none;">
                                            @foreach($nonTeachingQuestions->groupBy('title') as $title => $questionsGroup)
                                                <div class="mb-4">
                                                    <label class="section-title mb-3">{{ $title }}</label>
                                                    @foreach($questionsGroup as $question)
                                                        <div class="mb-3 ms-1 p-2 px-3 bg-white bg-opacity-40 rounded-3 shadow-sm border border-white">
                                                            <div class="question-text">
                                                                <span class="question-number">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</span>
                                                                {{ $question->question_text }}
                                                            </div>
                                                            @if($question->description)
                                                                <p class="text-muted small mb-3 ms-4 fst-italic">{{ $question->description }}</p>
                                                            @endif
                                                            <div class="d-flex flex-wrap justify-content-center response-options-group mt-3">
                                                                @php $options = \App\Models\ResponseOption::where('response_type', $question->response_type)->orderBy('option_order')->get(); @endphp
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
                                        </div>

                                        <div class="mb-4 bg-white bg-opacity-50 p-4 rounded-3 border border-white shadow-sm">
                                            <label class="form-label fw-bold text-dark h5 mb-3">Comments & Recommendations <span class="text-muted" style="font-weight:normal;">(Optional)</span></label>
                                            <textarea name="comments" class="form-control border-2 bg-white" rows="4" placeholder="Provide any additional comments or recommendations..."></textarea>
                                        </div>

                                        <div class="text-center mt-5">
                                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-lg rounded-pill transform-hover" id="submitEvalBtn">
                                                <i class="fas fa-paper-plane me-2"></i>SUBMIT EVALUATION
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($allEvaluationsCompleted)
                    <div id="completionOverlay" class="completion-overlay">
                        <div class="completion-modal">
                            <div class="content">
                                <div class="completion-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h2 class="completion-title">🎉 Congratulations!</h2>
                                <p class="completion-message">You have successfully completed all evaluations.</p>
                                <div class="completion-stats">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            <h3 class="fw-bold">{{ $totalEvaluatedStaff }}</h3>
                                            <small>Total Evaluated</small>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mt-4 rounded-pill px-4" onclick="document.getElementById('completionOverlay').style.display='none'">
                                    Dismiss
                                </button>
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
let activeStaffId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Form submission
    const evalForm = document.getElementById('irregularEvalForm');
    if (evalForm) {
        evalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Submit Evaluation?',
                text: "You are about to submit your evaluation. This action cannot be undone.",
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
                    evalForm.submit();
                }
            });
        });
    }

    @if(session('message'))
        Swal.fire({
            icon: '{{ session('message_type') === 'success' ? 'success' : (session('message_type') === 'danger' ? 'error' : 'info') }}',
            title: '{{ session('message_type') === 'success' ? 'Success!' : (session('message_type') === 'danger' ? 'Error!' : 'Info') }}',
            text: '{{ session('message') }}',
            confirmButtonColor: '#667eea'
        });
    @endif

    // Handle radio button selection styling
    document.querySelectorAll('.response-options-group input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const group = this.closest('.response-options-group');
            group.querySelectorAll('.form-check-label').forEach(label => {
                label.classList.remove('selected-rating');
            });
            if (this.checked) {
                this.parentElement.classList.add('selected-rating');
            }
        });
    });
});

function openEvaluation(id, type, name, subjects) {
    activeStaffId = id;
    document.getElementById('selectedStaffNameDisplay').textContent = name;
    document.getElementById('formStaffId').value = id;
    document.getElementById('formStaffType').value = type;
    
    // Clear previous responses and styling
    const form = document.getElementById('irregularEvalForm');
    form.querySelectorAll('input[type="radio"]').forEach(r => {
        r.checked = false;
        r.parentElement.classList.remove('selected-rating');
    });
    form.querySelector('textarea').value = '';
    
    // Update labels and icons based on type
    const typeLabel = document.getElementById('selectedStaffTypeLabel');
    const typeIcon = document.getElementById('selectedStaffIcon');
    const subjectLabel = document.getElementById('selectedStaffSubjectLabel');
    const teachingContainer = document.getElementById('teachingQuestionsContainer');
    const nonTeachingContainer = document.getElementById('nonTeachingQuestionsContainer');

    if (subjects) {
        subjectLabel.innerHTML = `<i class="fas fa-book me-1"></i> ${subjects}`;
        subjectLabel.style.display = 'inline-block';
    } else {
        subjectLabel.style.display = 'none';
    }

    if (type === 'teaching') {
        typeLabel.textContent = 'Instructor';
        typeIcon.className = 'fas fa-user-tie text-primary fa-2x';
        teachingContainer.style.display = 'block';
        nonTeachingContainer.style.display = 'none';
        
        // Enable required attributes for teaching questions
        teachingContainer.querySelectorAll('input[type="radio"]').forEach(r => r.required = true);
        nonTeachingContainer.querySelectorAll('input[type="radio"]').forEach(r => r.required = false);
    } else {
        typeLabel.textContent = 'Non-Teaching Staff';
        typeIcon.className = 'fas fa-user-cog text-success fa-2x';
        teachingContainer.style.display = 'none';
        nonTeachingContainer.style.display = 'block';
        
        // Enable required attributes for non-teaching questions
        teachingContainer.querySelectorAll('input[type="radio"]').forEach(r => r.required = false);
        nonTeachingContainer.querySelectorAll('input[type="radio"]').forEach(r => r.required = true);
    }
    
    document.getElementById('staffListSection').style.display = 'none';
    document.getElementById('evaluationFormSection').style.display = 'block';
    
    window.scrollTo({ top: document.querySelector('.evaluation-card').offsetTop - 20, behavior: 'smooth' });
}

function showStaffList() {
    document.getElementById('evaluationFormSection').style.display = 'none';
    document.getElementById('staffListSection').style.display = 'block';
    window.scrollTo({ top: document.querySelector('.evaluation-card').offsetTop - 20, behavior: 'smooth' });
}

function unlockSelection() {
    Swal.fire({
        title: 'Unlock Selection?',
        text: "This will allow you to change your instructor selection. All pending evaluations for these instructors will be preserved, but you can add or remove instructors.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4d4f',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Yes, unlock!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send request to unlock
            fetch("{{ route('selection.unlock') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    Swal.fire('Error', data.message || 'Failed to unlock selection', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred while unlocking selection', 'error');
            });
        }
    });
}
</script>
