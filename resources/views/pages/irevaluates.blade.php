@php
    $teachingEvaluated = $teachingEvaluated ?? 0;
    $nonTeachingEvaluated = $nonTeachingEvaluated ?? 0;
    $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
    $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();
    
    // Get the active academic year
    $currentAcademicYear = $currentAcademicYear ?? \App\Models\AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
    $activeAcademicYearId = $currentAcademicYear ? $currentAcademicYear->id : null;

    $isOpen = $isOpen ?? ($activeAcademicYearId ? \App\Models\Question::where('academic_year_id', $activeAcademicYearId)->where('is_open', 1)->exists() : false);

    // For specific evaluated IDs
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
    
    $hasLockedSelection = $hasLockedSelection ?? false;
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
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.custom-nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    color: #4a5568;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transform: translateY(-3px) scale(1.02);
}

@media (max-width: 576px) {
    .custom-nav-tabs {
        padding: 4px;
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
    }
    .custom-nav-tabs::-webkit-scrollbar {
        display: none;
    }
    .custom-nav-tabs .nav-item {
        flex: 1 1 auto;
    }
    .custom-nav-tabs .nav-link {
        padding: 8px 10px;
        font-size: 0.75rem;
        gap: 5px;
        white-space: nowrap;
        border-radius: 8px;
    }
    .custom-nav-tabs .nav-link i {
        font-size: 0.85rem;
        margin-right: 2px !important;
    }
    .staff-card .card-body {
        padding: 12px;
    }
    .staff-card h6 {
        font-size: 0.85rem;
    }
    .staff-card small {
        font-size: 0.75rem;
    }
    .staff-card small.text-truncate {
        max-width: 100px !important;
    }
    .staff-card button small {
        font-size: 0.65rem !important;
    }
}

.min-width-0 {
    min-width: 0;
}

.tab-content {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.staff-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    height: 100%;
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
}

@media (max-width: 576px) {
    .custom-nav-tabs {
        flex-direction: row !important;
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
                    <div id="privacyReminder" class="privacy-reminder-box d-flex flex-column align-items-center justify-content-center text-center p-4 mb-4 bg-light rounded-3 border" style="max-width: 600px; margin: 0 auto;">
                        <div class="mb-3">
                            <i class="fas fa-user-secret fa-2x mb-2 text-primary"></i>
                            <h5 class="fw-bold">Evaluator Privacy Notice</h5>
                            <p class="mb-0">Your identity and responses are strictly confidential. Please provide honest and constructive feedback.</p>
                        </div>
                        <button id="startEvaluationBtn" class="btn btn-success px-4 py-2 fw-bold rounded-pill" type="button">
                            <i class="fas fa-play me-2"></i>Select Specific Instructors
                        </button>
                    </div>

                    <div id="selectionAndEvaluationWrapper" style="display: none;">
                        <div id="navigationSection" class="mb-4">
                            <ul class="nav nav-tabs custom-nav-tabs" id="staffTypeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="teaching-tab" data-bs-toggle="tab" data-bs-target="#teaching-content" type="button" role="tab" onclick="setStaffType('teaching')">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>Instructors
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="non-teaching-tab" type="button" role="tab">
                                        <i class="fas fa-users-cog me-2"></i>Non-Teaching
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div id="staffListSection">
                            <div class="staff-search-container position-relative mb-4 mx-auto" style="max-width: 500px;">
                                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="staffSearch" class="form-control rounded-pill ps-5 border-2" placeholder="Search by name or ID...">
                            </div>

                            <div class="tab-content" id="staffTypeTabsContent">
                                <div class="tab-pane fade show active" id="teaching-content" role="tabpanel">
                                    <div class="row g-3" id="teachingList">
                                        @foreach($studentSubjects->groupBy('assign_instructor') as $instructorName => $subjects)
                                            @php 
                                                $staff = $teachingStaff->where('full_name', $instructorName)->first();
                                                $staffId = $staff ? $staff->id : null;
                                                $evaluated = $staffId ? in_array($staffId, $evaluatedTeachingIds, true) : false;
                                                $subjectNames = $subjects->pluck('sub_name')->implode(', ');
                                            @endphp
                                            <div class="col-md-6 staff-item" data-name="{{ strtolower($instructorName) }}" data-id="{{ strtolower($subjectNames) }}">
                                                <div class="card staff-card h-100 {{ $evaluated ? 'opacity-75' : '' }}" 
                                                    onclick="{{ $staffId ? "handleStaffSelection(event, this, $staffId, 'teaching', '" . addslashes($instructorName) . "', " . ($evaluated ? 'true' : 'false') . ", '" . addslashes($subjectNames) . "')" : "Swal.fire({icon:'warning', title:'Staff Not Found', text:'This instructor is not yet registered.'})" }}">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3 checkbox-wrapper">
                                                                <div class="form-check">
                                                                    <input class="form-check-input select-staff-checkbox" type="checkbox" {{ $evaluated || !$staffId ? 'disabled' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 min-width-0">
                                                                <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $instructorName }}</h6>
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <small class="text-muted d-block text-truncate flex-grow-1" style="max-width: 150px;">{{ $subjectNames }}</small>
                                                                    @if($subjects->count() > 1 || strlen($subjectNames) > 25)
                                                                        <button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none ms-2" 
                                                                            onclick="event.stopPropagation(); showAllSubjects('{{ addslashes($instructorName) }}', '{{ addslashes($subjectNames) }}')">
                                                                            <small class="fw-bold" style="font-size: 0.7rem;">See More</small>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                                @if($evaluated)
                                                                    <span class="badge bg-success-soft text-success rounded-pill mt-1">Evaluated</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="non-teaching-content" role="tabpanel">
                                    <div class="alert alert-info text-center">Non-teaching evaluation is currently not available.</div>
                                    <div class="text-center mt-4" id="selectionControlsNonTeaching" style="display: none;">
                                        <div id="initialControlsNonTeaching">
                                            <button type="button" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm" id="doneNonTeachingBtn" disabled>Done Selection</button>
                                        </div>
                                        <div id="reviewControlsNonTeaching" style="display: none;">
                                            <button type="button" class="btn btn-outline-secondary px-4 fw-bold rounded-pill" onclick="unlockSelection()">Edit</button>
                                            <button type="button" class="btn btn-success px-4 fw-bold rounded-pill" onclick="finalConfirmSelection()">Confirm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStaffType = 'teaching';
let selectedStaff = [];

document.addEventListener('DOMContentLoaded', function() {
    // Session messages
    @if(session('message'))
        Swal.fire({
            icon: '{{ session('message_type') === 'success' ? 'success' : 'error' }}',
            title: '{{ session('message_type') === 'success' ? 'Success!' : 'Error!' }}',
            text: '{{ session('message') }}',
            confirmButtonColor: '#667eea'
        });
    @endif

    // Privacy reminder
    const startBtn = document.getElementById('startEvaluationBtn');
    const privacyReminder = document.getElementById('privacyReminder');
    const wrapper = document.getElementById('selectionAndEvaluationWrapper');
    
    if (startBtn && privacyReminder && wrapper) {
        startBtn.addEventListener('click', function() {
            privacyReminder.classList.add('d-none');
            wrapper.style.display = 'block';
        });
    }

    // Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Search
    const searchInput = document.getElementById('staffSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const activeTab = document.querySelector('.tab-pane.active');
            const items = activeTab.querySelectorAll('.staff-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const id = item.getAttribute('data-id');
                item.classList.toggle('d-none', !(name.includes(searchTerm) || id.includes(searchTerm)));
            });
        });
    }

    // Non-teaching tab
    const nonTeachingTab = document.getElementById('non-teaching-tab');
    if (nonTeachingTab) {
        nonTeachingTab.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Unavailable', text: 'Non-Teaching evaluation is currently not available.', confirmButtonColor: '#667eea' });
        });
    }
});

function setStaffType(type) {
    currentStaffType = type;
    document.getElementById('selectionControls').style.display = type === 'teaching' ? 'block' : 'none';
    document.getElementById('selectionControlsNonTeaching').style.display = type === 'teaching' ? 'none' : 'block';
}

function handleStaffSelection(ev, cardElement, id, type, name, evaluated, subject) {
    if (evaluated) {
        Swal.fire({ icon: 'info', title: 'Already Evaluated', text: 'You have already evaluated ' + name, confirmButtonColor: '#667eea' });
        return;
    }

    const checkbox = cardElement.querySelector('.select-staff-checkbox');
    if (checkbox && !checkbox.disabled) {
        checkbox.checked = !checkbox.checked;
        cardElement.classList.toggle('selected', checkbox.checked);
        if (checkbox.checked) {
            selectedStaff.push({ id, type, name, subject });
        } else {
            selectedStaff = selectedStaff.filter(s => s.id !== id);
        }
        const doneBtn = currentStaffType === 'teaching' ? document.getElementById('doneSelectionBtn') : document.getElementById('doneNonTeachingBtn');
        if (doneBtn) doneBtn.disabled = selectedStaff.length === 0;
    }
}

function showAllSubjects(name, subjects) {
    const subjectList = subjects.split(',').map(s => `<div class="p-2 mb-2 bg-light rounded border-start border-4 border-primary text-start">${s.trim()}</div>`).join('');
    Swal.fire({
        title: `<div class="fs-5 fw-bold text-dark mb-1">${name}</div><div class="fs-6 text-muted">Handled Subjects</div>`,
        html: `<div class="mt-3" style="max-height: 300px; overflow-y: auto;">${subjectList}</div>`,
        confirmButtonText: 'Close',
        confirmButtonColor: '#667eea',
        showCloseButton: true,
        customClass: {
            container: 'subject-overlay-container',
            popup: 'rounded-4 shadow-lg border-0',
            confirmButton: 'rounded-pill px-4'
        }
    });
}

function confirmSelection() {
    if (selectedStaff.length === 0) return;
    const items = document.querySelectorAll('.staff-item');
    items.forEach(item => {
        const card = item.querySelector('.staff-card');
        const isSelected = card.classList.contains('selected');
        item.classList.toggle('d-none', !isSelected);
        if (isSelected) item.querySelector('.checkbox-wrapper').style.display = 'none';
    });
    if (currentStaffType === 'teaching') {
        document.getElementById('initialControls').style.display = 'none';
        document.getElementById('reviewControls').style.display = 'block';
    } else {
        document.getElementById('initialControlsNonTeaching').style.display = 'none';
        document.getElementById('reviewControlsNonTeaching').style.display = 'block';
    }
    document.querySelector('.staff-search-container').style.display = 'none';
}

function unlockSelection() {
    const items = document.querySelectorAll('.staff-item');
    items.forEach(item => {
        item.classList.remove('d-none');
        item.querySelector('.checkbox-wrapper').style.display = 'block';
    });
    if (currentStaffType === 'teaching') {
        document.getElementById('initialControls').style.display = 'block';
        document.getElementById('reviewControls').style.display = 'none';
    } else {
        document.getElementById('initialControlsNonTeaching').style.display = 'block';
        document.getElementById('reviewControlsNonTeaching').style.display = 'none';
    }
    document.querySelector('.staff-search-container').style.display = 'block';
}

function finalConfirmSelection() {
    Swal.fire({
        title: 'Finalize Selection?',
        text: "This will lock your instructor selection for evaluation.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Yes, lock it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("selection.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ staff_ids: selectedStaff.map(s => s.id), staff_type: currentStaffType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Selection Locked', text: 'Redirecting...', timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            });
        }
    });
}
</script>