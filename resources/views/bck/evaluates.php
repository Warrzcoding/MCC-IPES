@php
    $teachingEvaluated = $teachingEvaluated ?? 0;
    $nonTeachingEvaluated = $nonTeachingEvaluated ?? 0;
    $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
    $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();
    $evaluations = \App\Models\Evaluation::where('user_id', auth()->id())->get();
    $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
    $teachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'teaching')->count();
    $nonTeachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'non-teaching')->count();
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
    height: 30px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 2px;
}
.enhanced-select-wrapper {
    position: relative;
    margin-bottom: 30px;
}
.enhanced-select {
    width: 100%;
    padding: 18px 25px 18px 20px;
    font-size: 1.1rem;
    font-weight: 500;
    color: #2d3748;
    background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    outline: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    appearance: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
.enhanced-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 8px 30px rgba(102, 126, 234, 0.15);
    background: #ffffff;
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
    padding: 6px 12px;
    border-radius: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid #e2e8f0;
}
.form-check-label:hover {
    background: #f7fafc;
    transform: translateY(-1px);
}
.form-check-input {
    margin-right: 8px;
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
</style>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center flex-wrap">
                <!-- Closed notification message always visible and separated -->
                @if(!$isOpen)
                    <div class="w-100 mb-2">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Questions are temporarily closed by the admin.</strong> Please wait for them to open to start evaluation.
                            @if(isset($currentAcademicYear) && $currentAcademicYear && isset($currentAcademicYear->year))
                                <br><span class="fw-bold">Academic Year: {{ $currentAcademicYear->year }}</span>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="d-flex gap-3 align-items-center mb-2 mb-md-0">
                    <!-- Notification badge for evaluated staff -->
                    <button type="button" class="btn position-relative" id="evaluatedStaffBadge" data-bs-toggle="popover" data-bs-trigger="focus" title="Evaluated Staff Breakdown" data-bs-html="true" data-bs-content="
                        <div class='evaluated-popover-content'>
                            <div class='evaluated-popover-label'><i class='fas fa-chalkboard-teacher'></i> <span>Teaching Staff:</span> <strong>{{ $teachingCount }}</strong></div>
                            <div class='evaluated-popover-label'><i class='fas fa-users-cog'></i> <span>Non-Teaching Staff:</span> <strong>{{ $nonTeachingCount }}</strong></div>
                        </div>">
                        <span class="badge evaluated-badge-red">
                            {{ $distinctStaffIds->count() }}
                        </span>
                    </button>
                </div>
                <div class="enhanced-status-badge {{ $isOpen ? 'status-open' : 'status-closed' }}">
                    <i class="fas {{ $isOpen ? 'fa-unlock' : 'fa-lock' }} me-2"></i>
                    Questions are {{ $isOpen ? 'Open' : 'Closed' }}
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('message_type', 'info') }} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
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
                                            <select name="staff_id" class="form-select enhanced-select" required>
                                                <option value="">Choose an instructor to evaluate...</option>
                                                @foreach($teachingStaff as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="teachingQuestions">
                                            @foreach($teachingQuestions as $question)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ $question->title }}</label>
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
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Comments & Recommendations</label>
                                                <textarea name="comments" class="form-control" rows="4" placeholder="Please provide any additional comments or recommendations for this instructor..." required></textarea>
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
                                            @foreach($nonTeachingQuestions as $question)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ $question->title }}</label>
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
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">Comments & Recommendations</label>
                                                <textarea name="comments" class="form-control" rows="4" placeholder="Please provide any additional comments or recommendations for this staff member..." required></textarea>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
