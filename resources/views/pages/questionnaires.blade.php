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
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Questionnaires Management
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Questionnaires:</strong> This section allows administrators to manage evaluation questions and academic years.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Questions List -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <h6 class="m-0 font-weight-bold text-primary">Questions List</h6>
                    @if(isset($questionnaires_data['current_academic_year']['year']))
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info ms-2">
                                {{ $questionnaires_data['current_academic_year']['year'] }}
                                @if(isset($questionnaires_data['current_academic_year']['semester']))
                                    - Semester {{ $questionnaires_data['current_academic_year']['semester'] }}
                                @endif
                            </span>
                            @if(isset($questionnaires_data['current_academic_year']['is_active']) && $questionnaires_data['current_academic_year']['is_active'])
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @endif
                        </div>
                    @endif
                    @php
                        $questions = collect($questionnaires_data['questions'] ?? [])->map(function($q) {
                            return (object) $q;
                        });
                    @endphp
                    @php
                        $questionnaire_status = $questions->isNotEmpty() ? $questions->first()->is_open : false;
                    @endphp        
                    @php
                        $hasActiveAcademicYear = isset($questionnaires_data['current_academic_year']) && $questionnaires_data['current_academic_year'] && $questionnaires_data['current_academic_year']['is_active'] == 1;
                    @endphp
                </div>
                <div class="d-flex align-items-center gap-2">
                    @php
                        $openAt = $questionnaires_data['current_academic_year']['open_at'] ?? null;
                        $closeAt = $questionnaires_data['current_academic_year']['close_at'] ?? null;
                        $now = \Carbon\Carbon::now();
                        $hasSchedule = $openAt || $closeAt;
                    @endphp
                    
                    @if($hasSchedule)
                        <!-- Countdown Timer Display -->
                        <div class="d-flex align-items-center gap-2 me-3">
                            <div class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 d-flex align-items-center gap-2">
                                <i class="fas fa-clock"></i>
                                <span id="countdown-label" class="fw-semibold"></span>
                                <span id="countdown-timer" class="fw-bold"></span>
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($questionnaires_data['current_academic_year']))
                        <!-- Dropdown Button for Close/Open and Schedule -->
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-pill px-4 shadow-sm fw-semibold align-middle d-flex align-items-center gap-2 btn-{{ $questionnaire_status ? 'danger' : 'success' }} dropdown-toggle"
                                    type="button" id="questionnaireDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas {{ $questionnaire_status ? 'fa-lock' : 'fa-unlock' }}"></i>
                                <span>{{ $questionnaire_status ? 'Close Question' : 'Open Question' }}</span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="questionnaireDropdown">
                                <li>
                                    <form method="POST" action="{{ url('/dashboard/toggle-questionnaire-status') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="academic_year_id" value="{{ $questionnaires_data['current_academic_year']['id'] }}">
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                            <i class="fas {{ $questionnaire_status ? 'fa-lock' : 'fa-unlock' }}"></i>
                                            <span>{{ $questionnaire_status ? 'Close Now' : 'Open Now' }}</span>
                                        </button>
                                    </form>
                                </li>
                                @if(!$questionnaire_status)
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Set Schedule</span>
                                    </button>
                                </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    <button class="btn btn-primary ms-2
                                @if($questionnaire_status || !$hasActiveAcademicYear)
                                    disabled-custom
                                @endif"
                            id="addQuestionBtn"
                            title="@if($questionnaire_status)You can only add questions when the questionnaire is closed.@elseif(!$hasActiveAcademicYear)No active academic year. Please set another.@endif">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($questions->isEmpty())
                    <p class="text-muted text-center py-4">No questions found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered" id="questionsTable">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Staff Type</th>
                                    <th>Response Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td><strong>{{ $question->title }}</strong></td>
                                        <td class="text-muted">{{ $question->description }}</td>
                                        <td>
                                            <span class="badge {{ $question->staff_type == 'teaching' ? 'bg-primary' : 'bg-success' }}">
                                                {{ ucfirst($question->staff_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $question->response_type }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $question->is_open ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $question->is_open ? 'Open' : 'Closed' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary @if($questionnaire_status) disabled-custom @endif"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editQuestionModal"
                                                    @if($questionnaire_status) disabled tabindex="-1" aria-disabled="true" @else onclick="loadQuestionData({{ $question->id }}, '{{ addslashes($question->title) }}', '{{ addslashes($question->description) }}', '{{ addslashes($question->response_type) }}')" @endif
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger @if($questionnaire_status) disabled-custom @endif" 
                                                    onclick="confirmDeleteQuestion({{ $question->id }}, '{{ addslashes($question->title) }}');"
                                                    @if($questionnaire_status) disabled tabindex="-1" aria-disabled="true" @endif
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <form id="saveAllQuestionsForm" method="POST" action="{{ route('questions.saveAll') }}" style="display:inline;">
                            @csrf
                            <button
                                type="button"
                                id="saveAllQuestionsBtn"
                                class="btn btn-warning
                                    @if($questionnaire_status)
                                        disabled-custom
                                    @endif"
                                title="@if($questionnaire_status)You can only archive questions when the questionnaire is closed.@endif"
                            >
                                <i class="fas fa-save me-1"></i>Save & Archive All Questions
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/dashboard/add-question') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Question</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="staff_type" class="form-label">Staff Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="staff_type" name="staff_type" required>
                                    <option value="">Select Staff Type</option>
                                    <option value="teaching" {{ old('staff_type') == 'teaching' ? 'selected' : '' }}>Teaching Staff</option>
                                    <option value="non-teaching" {{ old('staff_type') == 'non-teaching' ? 'selected' : '' }}>Non-Teaching Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="response_type" class="form-label">Response Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="response_type" name="response_type" required>
                                    <option value="">Select Response Type</option>
                                    <option value="Rating_Scale" {{ old('response_type') == 'Rating_Scale' ? 'selected' : '' }}>Rating Scale (Poor, Fair, Good, Very Good, Excellent)</option>
                                    <option value="Frequency" {{ old('response_type') == 'Frequency' ? 'selected' : '' }}>Frequency (Rarely, Sometimes, Most of the Time, Always)</option>
                                    <option value="Frequency" {{ old('response_type') == 'Agreement' ? 'selected' : '' }}>Agreement (Strongly Disagree, Disagree, Neutral, Agree, Strongly Agree)</option>
                                    <option value="Satisfaction" {{ old('response_type') == 'Satisfaction' ? 'selected' : '' }}>Satisfaction (Very Dissatisfied, Dissatisfied, Neutral, Satisfied, Very Satisfied)</option>
                                    <option value="Yes_No" {{ old('response_type') == 'Yes_No' ? 'selected' : '' }}>Yes/No (Yes, No)</option>
                                    <option value="Text" {{ old('response_type') == 'Text' ? 'selected' : '' }}>Text Response (Free text input)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/dashboard/update-question') }}">
                    @csrf
                    <input type="hidden" name="question_id" id="editQuestionId">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Question</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editResponseType" class="form-label">Response Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="editResponseType" name="response_type" required>
                            <option value="">Select Response Type</option>
                            <option value="Rating_Scale">Rating Scale (Poor, Fair, Good, Very Good, Excellent)</option>
                            <option value="Frequency">Frequency (Rarely, Sometimes, Most of the Time, Always)</option>
                            <option value="Agreement">Agreement (Strongly Disagree, Disagree, Neutral, Agree, Strongly Agree)</option>
                            <option value="Satisfaction">Satisfaction (Very Dissatisfied, Dissatisfied, Neutral, Satisfied, Very Satisfied)</option>
                            <option value="Yes_No">Yes/No (Yes, No)</option>
                            <option value="Text">Text Response (Free text input)</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info bg-opacity-10">
                <h5 class="modal-title"><i class="fas fa-calendar-alt me-2 text-info"></i>Set Questionnaire Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ url('/dashboard/set-questionnaire-schedule') }}">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $questionnaires_data['current_academic_year']['id'] ?? '' }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="open_at" class="form-label">Open At</label>
                        <input type="datetime-local" class="form-control" id="open_at" name="open_at" value="{{ $questionnaires_data['current_academic_year']['open_at'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="close_at" class="form-label">Close At</label>
                        <input type="datetime-local" class="form-control" id="close_at" name="close_at" value="{{ $questionnaires_data['current_academic_year']['close_at'] ?? '' }}">
                    </div>
                    @if(!empty($questionnaires_data['current_academic_year']['open_at']) || !empty($questionnaires_data['current_academic_year']['close_at']))
                        <div class="alert alert-info d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            <span>
                                Current schedule:
                                @if(!empty($questionnaires_data['current_academic_year']['open_at']))
                                    <b>Opens:</b> {{ \Carbon\Carbon::parse($questionnaires_data['current_academic_year']['open_at'])->format('M d, Y H:i') }}<br>
                                @endif
                                @if(!empty($questionnaires_data['current_academic_year']['close_at']))
                                    <b>Closes:</b> {{ \Carbon\Carbon::parse($questionnaires_data['current_academic_year']['close_at'])->format('M d, Y H:i') }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if(!empty($questionnaires_data['current_academic_year']['open_at']) || !empty($questionnaires_data['current_academic_year']['close_at']))
                        <a href="{{ url('/dashboard/clear-questionnaire-schedule?academic_year_id=' . ($questionnaires_data['current_academic_year']['id'] ?? '')) }}" class="btn btn-outline-danger me-auto">
                            <i class="fas fa-times-circle me-1"></i>Clear Schedule
                        </a>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-save me-1"></i>Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.disabled-custom {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: auto !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadQuestionData(id, title, description, responseType) {
    document.getElementById('editQuestionId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editDescription').value = description;
    document.getElementById('editResponseType').value = responseType;
}

function confirmDeleteQuestion(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<span style='font-size:1.1rem;'>You are about to <b>delete</b> the question:<br><span class='badge bg-danger text-white mt-2 mb-2'>${name}</span></span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash-alt me-1"></i>Yes, delete it!',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4 shadow-lg',
            title: 'fw-bold',
            confirmButton: 'px-4 py-2',
            cancelButton: 'px-4 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("/dashboard/delete-question") }}';
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="question_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmSaveAllQuestions() {
    Swal.fire({
        title: 'Save & Archive All Questions?',
        html: "<span style='font-size:1.1rem;'>This will save all current questions to the archive for the active academic year and <b>clear all questions</b> from the list.<br><br>Are you sure?</span>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e42',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-save me-1"></i>Yes, Save & Clear!',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4 shadow-lg',
            title: 'fw-bold',
            confirmButton: 'px-4 py-2',
            cancelButton: 'px-4 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('saveAllQuestionsForm').submit();
        }
    });
}

function showCloseQuestionnaireAlert() {
    Swal.fire({
        icon: 'info',
        title: 'Please close the questionnaire first',
        text: 'You can only archive questions when the questionnaire is closed.',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-4 shadow-lg',
            title: 'fw-bold',
            confirmButton: 'px-4 py-2'
        }
    });
}

function showQuestionnaireOpenAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Questions are being used',
        text: 'Questions are open, please close it first before adding new questions.',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-4 shadow-lg',
            title: 'fw-bold',
            confirmButton: 'px-4 py-2'
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle Add Question button click
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    if (addQuestionBtn) {
        // Remove any existing event listeners
        addQuestionBtn.replaceWith(addQuestionBtn.cloneNode(true));
        const newAddQuestionBtn = document.getElementById('addQuestionBtn');
        
        newAddQuestionBtn.addEventListener('click', function(e) {
            console.log('Add Question button clicked');
            console.log('Has disabled-custom class:', this.classList.contains('disabled-custom'));
            
            if (this.classList.contains('disabled-custom')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                console.log('Showing questionnaire open alert');
                if (!@json($hasActiveAcademicYear)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Active Academic Year',
                        text: 'No active academic year, please set another.',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-4 shadow-lg',
                            title: 'fw-bold',
                            confirmButton: 'px-4 py-2'
                        }
                    });
                } else {
                    showQuestionnaireOpenAlert();
                }
                return false;
            } else {
                console.log('Opening modal');
                // Open the modal manually when button is enabled
                const modal = new bootstrap.Modal(document.getElementById('addQuestionModal'));
                modal.show();
            }
        });
    }

    // Handle Save & Archive All Questions button click
    const saveAllQuestionsBtn = document.getElementById('saveAllQuestionsBtn');
    if (saveAllQuestionsBtn) {
        saveAllQuestionsBtn.addEventListener('click', function(e) {
            if (this.classList.contains('disabled-custom')) {
                e.preventDefault();
                e.stopPropagation();
                showCloseQuestionnaireAlert();
            } else {
                confirmSaveAllQuestions();
            }
        });
    }

    // Prevent modal from opening if questionnaire is open
    const addQuestionModal = document.getElementById('addQuestionModal');
    if (addQuestionModal) {
        addQuestionModal.addEventListener('show.bs.modal', function(e) {
            const addQuestionBtn = document.getElementById('addQuestionBtn');
            if (addQuestionBtn && addQuestionBtn.classList.contains('disabled-custom')) {
                e.preventDefault();
                showQuestionnaireOpenAlert();
            }
        });
    }
    
    // Enhanced Countdown Timer for Schedule
    @if($hasSchedule)
        function startEnhancedCountdown() {
            const openAt = @json($openAt ? \Carbon\Carbon::parse($openAt)->format('Y-m-d H:i:s') : null);
            const closeAt = @json($closeAt ? \Carbon\Carbon::parse($closeAt)->format('Y-m-d H:i:s') : null);
            const isOpen = @json($questionnaire_status);
            
            function updateCountdown() {
                const now = new Date().getTime();
                let targetTime, labelText, isCountingToOpen;
                
                // Determine what we're counting down to
                if (openAt && !isOpen) {
                    // If questionnaire is closed and we have an open time
                    const openTime = new Date(openAt).getTime();
                    if (now < openTime) {
                        targetTime = openTime;
                        labelText = 'Opens in:';
                        isCountingToOpen = true;
                    }
                } 
                
                if (closeAt && isOpen) {
                    // If questionnaire is open and we have a close time
                    const closeTime = new Date(closeAt).getTime();
                    if (now < closeTime) {
                        targetTime = closeTime;
                        labelText = 'Closes in:';
                        isCountingToOpen = false;
                    }
                }
                
                // If no active countdown, show schedule info
                if (!targetTime) {
                    const labelEl = document.getElementById('countdown-label');
                    const timerEl = document.getElementById('countdown-timer');
                    if (labelEl && timerEl) {
                        if (openAt && closeAt) {
                            labelEl.textContent = 'Scheduled:';
                            const openDate = new Date(openAt);
                            const closeDate = new Date(closeAt);
                            timerEl.textContent = openDate.toLocaleDateString() + ' - ' + closeDate.toLocaleDateString();
                        } else if (openAt) {
                            labelEl.textContent = 'Opens:';
                            timerEl.textContent = new Date(openAt).toLocaleString();
                        } else if (closeAt) {
                            labelEl.textContent = 'Closes:';
                            timerEl.textContent = new Date(closeAt).toLocaleString();
                        }
                    }
                    return;
                }
                
                const distance = targetTime - now;
                const labelEl = document.getElementById('countdown-label');
                const timerEl = document.getElementById('countdown-timer');
                
                if (!labelEl || !timerEl) return;
                
                if (distance < 0) {
                    labelEl.textContent = isCountingToOpen ? 'Opening...' : 'Closing...';
                    timerEl.textContent = 'Now';
                    
                    // Clear the interval to stop the countdown
                    if (window.countdownInterval) {
                        clearInterval(window.countdownInterval);
                    }
                    
                    // Show a brief notification and refresh immediately
                    if (isCountingToOpen) {
                        timerEl.textContent = 'Opening Now!';
                        timerEl.className += ' text-success';
                    } else {
                        timerEl.textContent = 'Closing Now!';
                        timerEl.className += ' text-danger';
                    }
                    
                    // Refresh page immediately to update status
                    setTimeout(() => {
                        window.location.reload(true); // Force reload from server
                    }, 1000);
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let timeStr = '';
                if (days > 0) timeStr += days + 'd ';
                if (hours > 0 || days > 0) timeStr += hours + 'h ';
                if (minutes > 0 || hours > 0 || days > 0) timeStr += minutes + 'm ';
                timeStr += seconds + 's';
                
                labelEl.textContent = labelText;
                timerEl.textContent = timeStr;
            }
            
            updateCountdown();
            return setInterval(updateCountdown, 1000);
        }
        
        // Start the enhanced countdown
        window.countdownInterval = startEnhancedCountdown();
        
        // Additional safety mechanism: Check for status changes every 30 seconds
        // This handles cases where server time might be slightly different from client time
        window.statusCheckInterval = setInterval(() => {
            const now = new Date().getTime();
            const openAt = @json($openAt ? \Carbon\Carbon::parse($openAt)->format('Y-m-d H:i:s') : null);
            const closeAt = @json($closeAt ? \Carbon\Carbon::parse($closeAt)->format('Y-m-d H:i:s') : null);
            const isOpen = @json($questionnaire_status);
            
            let shouldRefresh = false;
            
            // Check if questionnaire should have opened
            if (openAt && !isOpen) {
                const openTime = new Date(openAt).getTime();
                if (now >= openTime) {
                    shouldRefresh = true;
                }
            }
            
            // Check if questionnaire should have closed
            if (closeAt && isOpen) {
                const closeTime = new Date(closeAt).getTime();
                if (now >= closeTime) {
                    shouldRefresh = true;
                }
            }
            
            if (shouldRefresh) {
                // Clear intervals before refresh
                if (window.countdownInterval) clearInterval(window.countdownInterval);
                if (window.statusCheckInterval) clearInterval(window.statusCheckInterval);
                
                // Show notification
                const labelEl = document.getElementById('countdown-label');
                const timerEl = document.getElementById('countdown-timer');
                if (labelEl && timerEl) {
                    labelEl.textContent = 'Updating...';
                    timerEl.textContent = 'Please wait';
                }
                
                // Refresh page
                window.location.reload(true);
            }
        }, 30000); // Check every 30 seconds
    @endif
});
</script> 

 