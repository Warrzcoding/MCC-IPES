@if(session('message') && session('message_type') != 'success')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

<style>
/* Department Color Badges */
.dept-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.375rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dept-bsba { background-color: #28a745; } /* Green */
.dept-bshm { background-color: #dc3545; } /* Red */
.dept-bsed { background-color: #003366; } /* Dark Blue */
.dept-beed { background-color: #87ceeb; color: #333; } /* Light Blue with dark text */
.dept-bsit { background-color: #6c757d; } /* Gray */

/* Subject Type Enhanced Colors */
.subject-type-major { background-color: #007bff; } /* Primary Blue */
.subject-type-minor { background-color: #e91e63; } /* Pink/Magenta */
.subject-type-bridging { background-color: #fd7e14; } /* Orange */
</style>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
              <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                     Subject Management
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Subject Management:</strong> This section allows administrators to add and manage academic subjects.
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Subject List -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Subject List</h6>
                <div>
                    <button class="btn btn-success me-2" id="csvUploadBtn" onclick="openCSVUpload()">
                        <i class="fas fa-upload"></i> Upload CSV
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Enhanced Semester Tabs -->
                <div class="mb-4">
                    <ul class="nav nav-tabs" id="semesterTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="semester1-tab" data-bs-toggle="tab" data-bs-target="#semester1" type="button" role="tab" aria-controls="semester1" aria-selected="true" data-semester="Sem 1">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Semester</span>
                                <span class="semester-badge">1st</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="semester2-tab" data-bs-toggle="tab" data-bs-target="#semester2" type="button" role="tab" aria-controls="semester2" aria-selected="false" data-semester="Sem 2">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Semester</span>
                                <span class="semester-badge">2nd</span>
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content" id="semesterTabContent">
                    <!-- Semester 1 Tab -->
                    <div class="tab-pane fade show active" id="semester1" role="tabpanel" aria-labelledby="semester1-tab">
                        <!-- Filter controls for Semester 1 -->
                        <div id="filterContainer1" class="mb-3"></div>
                        
                        @if(isset($subjects) && $subjects->where('semester', '1')->isEmpty())
                            <p class="text-muted text-center py-4">No subjects found for Semester 1.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered" id="subjectTable1">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Department</th>
                                    <th>Year</th>
                                    <th>Section</th>
                                    <th>Assigned Instructor</th>
                                    <th>Subject Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($subjects))
                                    @foreach($subjects->where('semester', '1') as $subject)
                                        <tr data-semester="{{ $subject->semester ?? '1' }}">
                                            <td>{{ $subject->sub_code }}</td>
                                            <td>{{ $subject->sub_name }}</td>
                                            <td>
                                                <span class="dept-badge dept-{{ strtolower($subject->sub_department) }}">
                                                    {{ $subject->sub_department }}
                                                </span>
                                            </td>
                                            <td>{{ $subject->sub_year }}</td>
                                            <td>{{ $subject->section ?? 'N/A' }}</td>
                                            <td>
                                                @if($subject->assign_instructor)
                                                    {{ $subject->assign_instructor }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge subject-type-{{ strtolower($subject->subject_type ?? 'major') }}">
                                                    {{ $subject->subject_type ?? 'Major' }}
                                                </span>
                                            </td>
                                            <td>
                                                                                                 <button class="btn btn-sm btn-outline-primary" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#editModal"
                                                         onclick="loadSubjectData('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ addslashes($subject->sub_department) }}', '{{ $subject->sub_year }}', '{{ $subject->section ?? '' }}', '{{ $subject->semester ?? '' }}', '{{ $subject->instructor_staff_id ?? '' }}', '{{ $subject->subject_type ?? 'Major' }}')">
                                                     <i class="fas fa-edit"></i>
                                                 </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteSubject('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ $subject->section ?? '' }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#addModal" 
                                                         onclick="duplicateSubjectData('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ addslashes($subject->sub_department) }}', '{{ $subject->sub_year }}', '{{ $subject->section ?? '' }}', '{{ $subject->semester ?? '' }}', '{{ $subject->instructor_staff_id ?? '' }}', '{{ $subject->subject_type ?? 'Major' }}')">
                                                     <i class="fas fa-copy"></i>
                                                 </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No subjects found for Semester 1.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
                    </div>
                    
                    <!-- Semester 2 Tab -->
                    <div class="tab-pane fade" id="semester2" role="tabpanel" aria-labelledby="semester2-tab">
                        <!-- Filter controls for Semester 2 -->
                        <div id="filterContainer2" class="mb-3"></div>
                        
                        @if(isset($subjects) && $subjects->where('semester', '2')->isEmpty())
                            <p class="text-muted text-center py-4">No subjects found for Semester 2.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered" id="subjectTable2">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Department</th>
                                            <th>Year</th>
                                            <th>Section</th>
                                            <th>Instructor</th>
                                            <th>Subject Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($subjects))
                                            @foreach($subjects->where('semester', '2') as $subject)
                                                <tr data-semester="{{ $subject->semester ?? '2' }}">
                                                    <td>{{ $subject->sub_code }}</td>
                                                    <td>{{ $subject->sub_name }}</td>
                                                    <td>
                                                <span class="dept-badge dept-{{ strtolower($subject->sub_department) }}">
                                                    {{ $subject->sub_department }}
                                                </span>
                                            </td>
                                                    <td>{{ $subject->sub_year }}</td>
                                                    <td>{{ $subject->section ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($subject->assign_instructor)
                                                            {{ $subject->assign_instructor }}
                                                        @else
                                                            <span class="text-muted">Unassigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge subject-type-{{ strtolower($subject->subject_type ?? 'major') }}">
                                                            {{ $subject->subject_type ?? 'Major' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal"
                                                                onclick="loadSubjectData('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ addslashes($subject->sub_department) }}', '{{ $subject->sub_year }}', '{{ $subject->section ?? '' }}', '{{ $subject->semester ?? '' }}', '{{ $subject->instructor_staff_id ?? '' }}', '{{ $subject->subject_type ?? 'Major' }}')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteSubject('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ $subject->section ?? '' }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#addModal" 
                                                                onclick="duplicateSubjectData('{{ $subject->sub_code }}', '{{ addslashes($subject->sub_name) }}', '{{ addslashes($subject->sub_department) }}', '{{ $subject->sub_year }}', '{{ $subject->section ?? '' }}', '{{ $subject->semester ?? '' }}', '{{ $subject->instructor_staff_id ?? '' }}', '{{ $subject->subject_type ?? 'Major' }}')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No subjects found for Semester 2.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add Subject Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/dashboard/add-subject') }}" id="addSubjectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="sub_department" class="form-label">Department</label>
                        <select class="form-select" id="sub_department" name="sub_department" required>
                            <option value="">Select</option>
                            <option value="BSIT" {{ old('sub_department') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSHM" {{ old('sub_department') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                            <option value="BSBA" {{ old('sub_department') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                            <option value="BSED" {{ old('sub_department') == 'BSED' ? 'selected' : '' }}>BSED</option>
                            <option value="BEED" {{ old('sub_department') == 'BEED' ? 'selected' : '' }}>BEED</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sub_year" class="form-label">Year</label>
                        <select class="form-select" id="sub_year" name="sub_year" required>
                            <option value="">Select</option>
                            <option value="1st Year" {{ old('sub_year') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                            <option value="2nd Year" {{ old('sub_year') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3rd Year" {{ old('sub_year') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4th Year" {{ old('sub_year') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <select class="form-select" id="section" name="section" required>
                            <option value="">Select section...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Select</option>
                            <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>2</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sub_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="sub_code" name="sub_code" value="{{ old('sub_code') }}" 
                               required maxlength="25"
                               placeholder="e.g., CS 101">
                    </div>

                    <div class="mb-3">
                        <label for="sub_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="sub_name" name="sub_name" value="{{ old('sub_name') }}" 
                               required maxlength="50"
                               placeholder="e.g., Programming 101">
                    </div>

                    <div class="mb-3">
                        <label for="assign_instructor" class="form-label">Assign Instructor</label>
                        <select class="form-select" id="assign_instructor" name="assign_instructor">
                            <option value="" {{ old('assign_instructor') == '' || old('assign_instructor') == null ? 'selected' : '' }}>Unassigned</option>
                            @if(isset($staff))
                                @foreach($staff as $staff_member)
                                    @if($staff_member->staff_type == 'teaching')
                                        <option value="{{ $staff_member->staff_id }}" {{ old('assign_instructor') == $staff_member->staff_id ? 'selected' : '' }}>
                                            {{ $staff_member->full_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject_type" class="form-label">Subject Type</label>
                        <select class="form-select" id="subject_type" name="subject_type" required>
                            <option value="">Select Type</option>
                            <option value="Major" {{ old('subject_type') == 'Major' ? 'selected' : '' }}>Major</option>
                            <option value="Minor" {{ old('subject_type') == 'Minor' ? 'selected' : '' }}>Minor</option>
                            <option value="Bridging" {{ old('subject_type') == 'Bridging' ? 'selected' : '' }}>Bridging</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/dashboard/update-subject') }}" id="editSubjectForm">
                    @csrf
                    <input type="hidden" name="original_subject_code" id="originalSubjectCode">
                    <input type="hidden" name="original_section" id="originalSection">
                    
                    <div class="mb-3">
                        <label for="editDepartment" class="form-label">Department</label>
                        <select class="form-select" id="editDepartment" name="sub_department" required>
                            <option value="">Select</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSBA">BSBA</option>
                            <option value="BSED">BSED</option>
                            <option value="BEED">BEED</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editYear" class="form-label">Year</label>
                        <select class="form-select" id="editYear" name="sub_year" required>
                            <option value="">Select</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editSection" class="form-label">Section</label>
                        <select class="form-select" id="editSection" name="section" required>
                            <option value="">Select section...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editSemester" class="form-label">Semester</label>
                        <select class="form-select" id="editSemester" name="semester" required>
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editSubjectCode" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="editSubjectCode" name="sub_code" 
                               required maxlength="25"
                               placeholder="e.g., CS 101">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editSubjectName" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="editSubjectName" name="sub_name" 
                               required maxlength="50"
                               placeholder="e.g., Programming 101">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editInstructor" class="form-label">Assign Instructor</label>
                        <select class="form-select" id="editInstructor" name="assign_instructor">
                            <option value="">Unassigned</option>
                            @if(isset($staff))
                                @foreach($staff as $staff_member)
                                    @if($staff_member->staff_type == 'teaching')
                                        <option value="{{ $staff_member->staff_id }}">
                                            {{ $staff_member->full_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editSubjectType" class="form-label">Subject Type</label>
                        <select class="form-select" id="editSubjectType" name="subject_type" required>
                            <option value="">Select Type</option>
                            <option value="Major">Major</option>
                            <option value="Minor">Minor</option>
                            <option value="Bridging">Bridging</option>
                        </select>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form for SweetAlert2 -->
<form method="POST" action="{{ url('/dashboard/delete-subject') }}" id="deleteSubjectForm" style="display: none;">
    @csrf
    <input type="hidden" name="subject_code" id="deleteSubjectCode">
    <input type="hidden" name="section" id="deleteSubjectSection">
</form>

<!-- CSV Upload Modal -->
<div class="modal fade" id="csvUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload CSV - Assign Instructors</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="csvUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>CSV Format:</strong> Subject Code, Staff ID, Semester, Year, Section<br>
                        <small>Example: BSIT101,STF001,1,1,A</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                        <div class="form-text">Only CSV files are allowed. Maximum file size: 2MB</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Target Semester</label>
                        <select class="form-control" id="targetSemester" name="semester" required>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will update instructor assignments for existing subjects.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-2"></i>Upload & Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<style>
/* Custom validation styles */
.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Filter styling */
.filter-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.search-box input:focus {
    transform: scale(1.02);
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    transform: scale(1.02);
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.fa-spin-on-hover:hover {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateX(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

/* Enhanced Semester Tab Styling */
.nav-tabs {
    border-bottom: 3px solid #e9ecef;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 10px 10px 0 0;
    padding: 8px 8px 0 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-tabs .nav-item {
    margin-bottom: -3px;
}

.nav-tabs .nav-link {
    border: 2px solid transparent;
    border-radius: 8px 8px 0 0;
    padding: 12px 20px;
    font-weight: 600;
    color: #6c757d;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-right: 4px;
}

.nav-tabs .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.nav-tabs .nav-link:hover::before {
    left: 100%;
}

.nav-tabs .nav-link:hover {
    color: #007bff;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.nav-tabs .nav-link.active {
    color: #ffffff;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    transform: translateY(-1px);
}

.nav-tabs .nav-link.active:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
    transform: translateY(-3px);
}

.nav-tabs .nav-link i {
    margin-right: 8px;
    font-size: 1.1em;
    transition: transform 0.3s ease;
}

.nav-tabs .nav-link:hover i {
    transform: scale(1.1) rotate(5deg);
}

.nav-tabs .nav-link.active i {
    transform: scale(1.05);
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

/* Tab content styling */
.tab-content {
    background: #ffffff;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 0;
}

.tab-pane {
    padding: 20px;
    min-height: 400px;
}

/* Active tab indicator */
.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3, #007bff);
    border-radius: 2px;
}

/* Semester badge styling */
.semester-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    margin-left: 8px;
    font-weight: 500;
}

.nav-tabs .nav-link.active .semester-badge {
    background: rgba(255,255,255,0.3);
    color: #ffffff;
}

/* Tab transition effects */
.tab-pane {
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease-in-out;
}

.tab-pane.active {
    opacity: 1;
    transform: translateY(0);
}

.tab-pane.show {
    opacity: 1;
    transform: translateY(0);
}

/* Enhanced focus states for accessibility */
.nav-tabs .nav-link:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
}

/* Loading state for tab content */
.tab-loading {
    position: relative;
    overflow: hidden;
}

.tab-loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #007bff, transparent);
    animation: loading 1.5s infinite;
    z-index: 1000;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Semester count badges */
.semester-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7em;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.nav-tabs .nav-link.active .semester-count {
    background: #28a745;
}

/* Responsive tab styling */
@media (max-width: 768px) {
    .nav-tabs {
        padding: 4px 4px 0 4px;
    }
    
    .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 0.85em;
        margin-right: 2px;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 4px;
        font-size: 0.9em;
    }
    
    .semester-badge {
        display: none;
    }
}

@media (max-width: 576px) {
    .nav-tabs .nav-link {
        padding: 6px 10px;
        font-size: 0.8em;
    }
    
    .nav-tabs .nav-link span:first-of-type {
        display: none;
    }
    
    .nav-tabs .nav-link::after {
        content: attr(data-semester);
        font-weight: 600;
    }
}
</style>

<script>
function confirmDeleteSubject(code, name, section) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to delete "${name}" (${code})${section ? ' - Section: ' + section : ''}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Set the form values
            document.getElementById('deleteSubjectCode').value = code;
            document.getElementById('deleteSubjectSection').value = section || '';
            
            // Trigger the form submission event to use the existing SweetAlert2 handling
            const deleteForm = document.getElementById('deleteSubjectForm');
            if (deleteForm) {
                // Create and dispatch a submit event to trigger the existing event listener
                const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                deleteForm.dispatchEvent(submitEvent);
            }
        }
    });
}

function duplicateSubjectData(subjectCode, subjectName, department, year, section, semester, instructor, subjectType) {
    // Clear the form first
    document.getElementById('addSubjectForm').reset();
    
    // Populate the add modal with the subject data (excluding subject code to avoid duplicates)
    document.getElementById('sub_name').value = subjectName;
    document.getElementById('sub_department').value = department;
    document.getElementById('sub_year').value = year;
    document.getElementById('section').value = ''; // Clear section to allow user to select different section
    document.getElementById('semester').value = semester;
    document.getElementById('assign_instructor').value = instructor || '';
    document.getElementById('subject_type').value = subjectType || 'Major';
    
    // Clear subject code field and focus on it so user can enter new code
    document.getElementById('sub_code').value = '';
    document.getElementById('sub_code').focus();
    
    // Populate sections for add modal based on selected department and year
    populateAddSections();
    
    // Show a notification that this is a duplicate operation
    const modalTitle = document.querySelector('#addModal .modal-title');
    modalTitle.innerHTML = '<i class="fas fa-copy me-2"></i>Duplicate Subject - Enter New Subject Code';
    
    // Reset the modal title when modal is closed
    document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
        modalTitle.innerHTML = '<i class="fas fa-plus me-2"></i>Add New Subject';
    }, { once: true });
}

function loadSubjectData(code, name, department, year, section, semester, instructor, subjectType) {
    document.getElementById('editSubjectCode').value = code;
    document.getElementById('editSubjectName').value = name;
    document.getElementById('editDepartment').value = department;
    document.getElementById('editYear').value = year;
    document.getElementById('editSemester').value = semester || '';
    document.getElementById('editInstructor').value = instructor || '';
    document.getElementById('editSubjectType').value = subjectType || 'Major';
    document.getElementById('originalSubjectCode').value = code;
    document.getElementById('originalSection').value = section || '';

    // First populate section options based on department/year
    populateEditSections();

    // Then preselect the current section value (after options are in place)
    document.getElementById('editSection').value = section || '';
}

// Validation functions
function validateSubjectCode(input) {
    const value = input.value;
    const pattern = /^[A-Za-z0-9\s]+$/;
    
    if (value.length > 25) {
        input.setCustomValidity('Subject code cannot exceed 25 characters');
        input.classList.add('is-invalid');
        return false;
    } else if (!pattern.test(value) && value.length > 0) {
        input.setCustomValidity('Subject code can only contain letters, numbers and spaces');
        input.classList.add('is-invalid');
        return false;
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
}

function validateSubjectName(input) {
    const value = input.value;
    
    if (value.length > 50) {
        input.setCustomValidity('Subject name cannot exceed 50 characters');
        input.classList.add('is-invalid');
        return false;
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
}

// Section data mapping (same as signup.blade.php)
const sectionData = {
    'BSIT': {
        '1st Year': [
            { value: 'NORTH', label: 'NORTH' },
            { value: 'WEST', label: 'WEST' },
            { value: 'SOUTH', label: 'SOUTH' },
            { value: 'EAST', label: 'EAST' },
            { value: 'SOUTHWEST', label: 'SOUTHWEST' },
            { value: 'NORTHWEST', label: 'NORTHWEST' },
            { value: 'SOUTHEAST', label: 'SOUTHEAST' },
            { value: 'NORTHEAST', label: 'NORTHEAST' }
        ],
        '2nd Year': [
            { value: 'NORTH', label: 'NORTH' },
            { value: 'WEST', label: 'WEST' },
            { value: 'SOUTH', label: 'SOUTH' },
            { value: 'EAST', label: 'EAST' },
            { value: 'SOUTHWEST', label: 'SOUTHWEST' },
            { value: 'NORTHWEST', label: 'NORTHWEST' },
            { value: 'SOUTHEAST', label: 'SOUTHEAST' },
            { value: 'NORTHEAST', label: 'NORTHEAST' }
        ],
        '3rd Year': [
            { value: 'NORTH', label: 'NORTH' },
            { value: 'WEST', label: 'WEST' },
            { value: 'SOUTH', label: 'SOUTH' },
            { value: 'EAST', label: 'EAST' },
            { value: 'SOUTHWEST', label: 'SOUTHWEST' },
            { value: 'NORTHWEST', label: 'NORTHWEST' },
            { value: 'SOUTHEAST', label: 'SOUTHEAST' },
            { value: 'NORTHEAST', label: 'NORTHEAST' }
        ],
        '4th Year': [
            { value: 'NORTH', label: 'NORTH' },
            { value: 'WEST', label: 'WEST' },
            { value: 'SOUTH', label: 'SOUTH' },
            { value: 'EAST', label: 'EAST' },
            { value: 'SOUTHWEST', label: 'SOUTHWEST' },
            { value: 'NORTHWEST', label: 'NORTHWEST' },
            { value: 'SOUTHEAST', label: 'SOUTHEAST' },
            { value: 'NORTHEAST', label: 'NORTHEAST' }
        ]
    },
    'BSHM': {
        '1st Year': [
            { value: 'BSHM-1A', label: 'BSHM-1A' },
            { value: 'BSHM-1B', label: 'BSHM-1B' },
            { value: 'BSHM-1C', label: 'BSHM-1C' },
            { value: 'BSHM-1D', label: 'BSHM-1D' },
            { value: 'BSHM-1E', label: 'BSHM-1E' },
            { value: 'BSHM-1F', label: 'BSHM-1F' },
            { value: 'BSHM-1G', label: 'BSHM-1G' },
            { value: 'BSHM-1H', label: 'BSHM-1H' },
            { value: 'BSHM-1I', label: 'BSHM-1I' },
            { value: 'BSHM-1J', label: 'BSHM-1J' },
            { value: 'BSHM-1K', label: 'BSHM-1K' },
            { value: 'BSHM-1L', label: 'BSHM-1L' }
        ],
        '2nd Year': [
            { value: 'BSHM-2A', label: 'BSHM-2A' },
            { value: 'BSHM-2B', label: 'BSHM-2B' },
            { value: 'BSHM-2C', label: 'BSHM-2C' },
            { value: 'BSHM-2D', label: 'BSHM-2D' },
            { value: 'BSHM-2E', label: 'BSHM-2E' },
            { value: 'BSHM-2F', label: 'BSHM-2F' },
            { value: 'BSHM-2G', label: 'BSHM-2G' },
            { value: 'BSHM-2H', label: 'BSHM-2H' },
            { value: 'BSHM-2I', label: 'BSHM-2I' },
            { value: 'BSHM-2J', label: 'BSHM-2J' },
            { value: 'BSHM-2K', label: 'BSHM-2K' },
            { value: 'BSHM-2L', label: 'BSHM-2L' }
        ],
        '3rd Year': [
            { value: 'BSHM-3A', label: 'BSHM-3A' },
            { value: 'BSHM-3B', label: 'BSHM-3B' },
            { value: 'BSHM-3C', label: 'BSHM-3C' },
            { value: 'BSHM-3D', label: 'BSHM-3D' },
            { value: 'BSHM-3E', label: 'BSHM-3E' },
            { value: 'BSHM-3F', label: 'BSHM-3F' },
            { value: 'BSHM-3G', label: 'BSHM-3G' },
            { value: 'BSHM-3H', label: 'BSHM-3H' },
            { value: 'BSHM-3I', label: 'BSHM-3I' },
            { value: 'BSHM-3J', label: 'BSHM-3J' },
            { value: 'BSHM-3K', label: 'BSHM-3K' },
            { value: 'BSHM-3L', label: 'BSHM-3L' }
        ],
        '4th Year': [
            { value: 'BSHM-4A', label: 'BSHM-4A' },
            { value: 'BSHM-4B', label: 'BSHM-4B' },
            { value: 'BSHM-4C', label: 'BSHM-4C' },
            { value: 'BSHM-4D', label: 'BSHM-4D' },
            { value: 'BSHM-4E', label: 'BSHM-4E' },
            { value: 'BSHM-4F', label: 'BSHM-4F' },
            { value: 'BSHM-4G', label: 'BSHM-4G' },
            { value: 'BSHM-4H', label: 'BSHM-4H' },
            { value: 'BSHM-4I', label: 'BSHM-4I' },
            { value: 'BSHM-4J', label: 'BSHM-4J' },
            { value: 'BSHM-4K', label: 'BSHM-4K' },
            { value: 'BSHM-4L', label: 'BSHM-4L' }
        ]
    },
    'BSBA': {
        '1st Year': [
            { value: 'FM-1A', label: 'FM-1A' },
            { value: 'FM-1B', label: 'FM-1B' },
            { value: 'FM-1C', label: 'FM-1C' },
            { value: 'FM-1D', label: 'FM-1D' },
            { value: 'FM-1E', label: 'FM-1E' },
            { value: 'FM-1F', label: 'FM-1F' },
            { value: 'FM-1G', label: 'FM-1G' },
            { value: 'FM-1H', label: 'FM-1H' },
            { value: 'FM-1I', label: 'FM-1I' },
            { value: 'FM-1J', label: 'FM-1J' },
            { value: 'FM-1K', label: 'FM-1K' }
        ],
        '2nd Year': [
            { value: 'FM-2A', label: 'FM-2A' },
            { value: 'FM-2B', label: 'FM-2B' },
            { value: 'FM-2C', label: 'FM-2C' },
            { value: 'FM-2D', label: 'FM-2D' },
            { value: 'FM-2E', label: 'FM-2E' },
            { value: 'FM-2F', label: 'FM-2F' },
            { value: 'FM-2G', label: 'FM-2G' },
            { value: 'FM-2H', label: 'FM-2H' },
            { value: 'FM-2I', label: 'FM-2I' },
            { value: 'FM-2J', label: 'FM-2J' },
            { value: 'FM-2K', label: 'FM-2K' }
        ],
        '3rd Year': [
            { value: 'FM-3A', label: 'FM-3A' },
            { value: 'FM-3B', label: 'FM-3B' },
            { value: 'FM-3C', label: 'FM-3C' },
            { value: 'FM-3D', label: 'FM-3D' },
            { value: 'FM-3E', label: 'FM-3E' },
            { value: 'FM-3F', label: 'FM-3F' },
            { value: 'FM-3G', label: 'FM-3G' },
            { value: 'FM-3H', label: 'FM-3H' },
            { value: 'FM-3I', label: 'FM-3I' },
            { value: 'FM-3J', label: 'FM-3J' },
            { value: 'FM-3K', label: 'FM-3K' }
        ],
        '4th Year': [
            { value: 'FM-4A', label: 'FM-4A' },
            { value: 'FM-4B', label: 'FM-4B' },
            { value: 'FM-4C', label: 'FM-4C' },
            { value: 'FM-4D', label: 'FM-4D' },
            { value: 'FM-4E', label: 'FM-4E' },
            { value: 'FM-4F', label: 'FM-4F' },
            { value: 'FM-4G', label: 'FM-4G' },
            { value: 'FM-4H', label: 'FM-4H' },
            { value: 'FM-4I', label: 'FM-4I' },
            { value: 'FM-4J', label: 'FM-4J' },
            { value: 'FM-4K', label: 'FM-4K' }
        ]
    },
    'BSED': {
        '1st Year': [
            { value: '1-A', label: '1-A' },
            { value: '1-B', label: '1-B' },
            { value: '1-C', label: '1-C' },
            { value: '1-M', label: '1-M' },
            { value: '1-N', label: '1-N' },
            { value: '1-FR', label: '1-FR' },
            { value: '1-SP', label: '1-SP' },
            { value: '1-GERMAN', label: '1-GERMAN' },
            { value: '1-TODDLER', label: '1-TODDLER' }
        ],
        '2nd Year': [
            { value: '2-A', label: '2-A' },
            { value: '2-B', label: '2-B' },
            { value: '2-C', label: '2-C' },
            { value: '2-M', label: '2-M' },
            { value: '2-N', label: '2-N' },
            { value: '2-FR', label: '2-FR' },
            { value: '2-SP', label: '2-SP' },
            { value: '2-GERMAN', label: '2-GERMAN' },
            { value: '2-TODDLER', label: '2-TODDLER' }
        ],
        '3rd Year': [
            { value: '3-A', label: '3-A' },
            { value: '3-B', label: '3-B' },
            { value: '3-C', label: '3-C' },
            { value: '3-M', label: '3-M' },
            { value: '3-N', label: '3-N' },
            { value: '3-FR', label: '3-FR' },
            { value: '3-SP', label: '3-SP' },
            { value: '3-GERMAN', label: '3-GERMAN' },
            { value: '3-TODDLER', label: '3-TODDLER' }
        ],
        '4th Year': [
            { value: '4-A', label: '4-A' },
            { value: '4-B', label: '4-B' },
            { value: '4-C', label: '4-C' },
            { value: '4-M', label: '4-M' },
            { value: '4-N', label: '4-N' },
            { value: '4-FR', label: '4-FR' },
            { value: '4-SP', label: '4-SP' },
            { value: '4-GERMAN', label: '4-GERMAN' },
            { value: '4-TODDLER', label: '4-TODDLER' }
        ]
    },
    'BEED': {
        '1st Year': [
            { value: '1-A', label: '1-A' },
            { value: '1-B', label: '1-B' },
            { value: '1-C', label: '1-C' },
            { value: '1-D', label: '1-D' },
            { value: '1-PRESCHOOLER', label: '1-PRESCHOOLER' },
            { value: '1-TODDLER', label: '1-TODDLER' },
            { value: '1-PR', label: '1-PR' }
        ],
        '2nd Year': [
            { value: '2-A', label: '2-A' },
            { value: '2-B', label: '2-B' },
            { value: '2-C', label: '2-C' },
            { value: '2-D', label: '2-D' },
            { value: '2-PRESCHOOLER', label: '2-PRESCHOOLER' },
            { value: '2-TODDLER', label: '2-TODDLER' },
            { value: '2-PR', label: '2-PR' }
        ],
        '3rd Year': [
            { value: '3-A', label: '3-A' },
            { value: '3-B', label: '3-B' },
            { value: '3-C', label: '3-C' },
            { value: '3-D', label: '3-D' },
            { value: '3-PRESCHOOLER', label: '3-PRESCHOOLER' },
            { value: '3-TODDLER', label: '3-TODDLER' },
            { value: '3-PR', label: '3-PR' }
        ],
        '4th Year': [
            { value: '4-A', label: '4-A' },
            { value: '4-B', label: '4-B' },
            { value: '4-C', label: '4-C' },
            { value: '4-D', label: '4-D' },
            { value: '4-PRESCHOOLER', label: '4-PRESCHOOLER' },
            { value: '4-TODDLER', label: '4-TODDLER' },
            { value: '4-PR', label: '4-PR' }
        ]
    }
};

// Function to populate sections for add modal
function populateSections() {
    const departmentSelect = document.getElementById('sub_department');
    const yearSelect = document.getElementById('sub_year');
    const sectionSelect = document.getElementById('section');
    
    const department = departmentSelect.value;
    const year = yearSelect.value;
    
    // Clear existing options
    sectionSelect.innerHTML = '<option value="">Select section...</option>';
    
    if (department && year && sectionData[department] && sectionData[department][year]) {
        const sections = sectionData[department][year];
        sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.value;
            option.textContent = section.label;
            sectionSelect.appendChild(option);
        });
        
        // Enable the section dropdown
        sectionSelect.disabled = false;
    } else {
        // Disable the section dropdown if department or year is not selected
        sectionSelect.disabled = true;
    }
}

// Function to populate sections for add modal
function populateAddSections() {
    const departmentSelect = document.getElementById('sub_department');
    const yearSelect = document.getElementById('sub_year');
    const sectionSelect = document.getElementById('section');
    
    const department = departmentSelect.value;
    const year = yearSelect.value;
    
    // Clear existing options
    sectionSelect.innerHTML = '<option value="">Select section...</option>';
    
    if (department && year && sectionData[department] && sectionData[department][year]) {
        const sections = sectionData[department][year];
        sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.value;
            option.textContent = section.label;
            sectionSelect.appendChild(option);
        });
        
        // Enable the section dropdown
        sectionSelect.disabled = false;
    } else {
        // Disable the section dropdown if department or year is not selected
        sectionSelect.disabled = true;
    }
}

// Function to populate sections for edit modal (preserves current selection)
function populateEditSections() {
    const departmentSelect = document.getElementById('editDepartment');
    const yearSelect = document.getElementById('editYear');
    const sectionSelect = document.getElementById('editSection');

    const department = departmentSelect.value;
    const year = yearSelect.value;

    // Remember currently set section (e.g., from loadSubjectData)
    const currentValue = sectionSelect.value;

    // Clear existing options
    sectionSelect.innerHTML = '<option value="">Select section...</option>';

    if (department && year && sectionData[department] && sectionData[department][year]) {
        const sections = sectionData[department][year];
        sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.value;
            option.textContent = section.label;
            sectionSelect.appendChild(option);
        });

        // Re-select previously chosen section if it exists in the list
        if (currentValue) {
            sectionSelect.value = currentValue;
        }

        // Enable the section dropdown
        sectionSelect.disabled = false;
    } else {
        // Disable the section dropdown if department or year is not selected
        sectionSelect.disabled = true;
    }
}

// Function to populate sections for filter (legacy - kept for compatibility)
function populateFilterSections() {
    // This function is now handled by populateFilterSectionsForInstance
    // Kept for backward compatibility
}

// Initialize search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add validation event listeners
    const subjectCodeInputs = document.querySelectorAll('#sub_code, #editSubjectCode');
    const subjectNameInputs = document.querySelectorAll('#sub_name, #editSubjectName');
    
    subjectCodeInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateSubjectCode(this);
        });
        input.addEventListener('blur', function() {
            validateSubjectCode(this);
        });
    });
    
    subjectNameInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateSubjectName(this);
        });
        input.addEventListener('blur', function() {
            validateSubjectName(this);
        });
    });
    
    // Add event listeners for department and year changes in add modal
    const addDepartmentSelect = document.getElementById('sub_department');
    const addYearSelect = document.getElementById('sub_year');
    
    if (addDepartmentSelect) {
        addDepartmentSelect.addEventListener('change', populateAddSections);
    }
    if (addYearSelect) {
        addYearSelect.addEventListener('change', populateAddSections);
    }
    
    // Add event listeners for department and year changes in edit modal
    const editDepartmentSelect = document.getElementById('editDepartment');
    const editYearSelect = document.getElementById('editYear');
    
    if (editDepartmentSelect) {
        editDepartmentSelect.addEventListener('change', populateEditSections);
    }
    if (editYearSelect) {
        editYearSelect.addEventListener('change', populateEditSections);
    }
    // --- UI: Flex container for search and filter ---
    const searchFilterWrapper = document.createElement('div');
    searchFilterWrapper.className = 'd-flex flex-wrap align-items-center gap-3 mb-3 filter-container';
    
    // --- Search box with icon ---
    const searchBox = document.createElement('div');
    searchBox.className = 'search-box mb-0';
    searchBox.style.flex = '1 1 250px';
    searchBox.style.minWidth = '220px';
    searchBox.style.position = 'relative';
    
    const searchIcon = document.createElement('i');
    searchIcon.className = 'fas fa-search search-icon';
    searchIcon.style.position = 'absolute';
    searchIcon.style.left = '15px';
    searchIcon.style.top = '50%';
    searchIcon.style.transform = 'translateY(-50%)';
    searchIcon.style.color = '#6c757d';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.id = 'searchInput';
    searchInput.className = 'form-control';
    searchInput.placeholder = 'Search subjects by name, code, department, or year...';
    searchInput.style.paddingLeft = '45px';
    searchInput.style.borderRadius = '25px';
    searchInput.style.border = '2px solid #e9ecef';
    searchInput.style.boxShadow = 'none';
    
    searchInput.addEventListener('focus', function() {
        searchInput.style.borderColor = '#007bff';
        searchInput.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,.25)';
    });
    
    searchInput.addEventListener('blur', function() {
        searchInput.style.borderColor = '#e9ecef';
        searchInput.style.boxShadow = 'none';
    });
    
    searchBox.appendChild(searchIcon);
    searchBox.appendChild(searchInput);
    
    // --- Department filter select ---
    const filterDiv = document.createElement('div');
    filterDiv.className = 'd-flex gap-2';
    filterDiv.style.minWidth = '200px';
    
    const departmentSelect = document.createElement('select');
    departmentSelect.id = 'departmentFilter';
    departmentSelect.className = 'form-select';
    departmentSelect.style.minWidth = '150px';
    departmentSelect.title = 'Filter by department';
    departmentSelect.innerHTML = `
      <option value="">All Departments</option>
      <option value="BSIT">BSIT</option>
      <option value="BSHM">BSHM</option>
      <option value="BSBA">BSBA</option>
      <option value="BSED">BSED</option>
      <option value="BEED">BEED</option>
    `;
    
    // --- Year level filter select (initially hidden) ---
    const yearSelect = document.createElement('select');
    yearSelect.id = 'yearFilter';
    yearSelect.className = 'form-select';
    yearSelect.style.minWidth = '120px';
    yearSelect.style.display = 'none';
    yearSelect.title = 'Filter by year level';
    yearSelect.innerHTML = `
      <option value="">All Years</option>
      <option value="1st Year">1st Year</option>
      <option value="2nd Year">2nd Year</option>
      <option value="3rd Year">3rd Year</option>
      <option value="4th Year">4th Year</option>
    `;
    
    // --- Section filter select (initially hidden) ---
    const sectionSelect = document.createElement('select');
    sectionSelect.id = 'sectionFilter';
    sectionSelect.className = 'form-select';
    sectionSelect.style.minWidth = '150px';
    sectionSelect.style.display = 'none';
    sectionSelect.title = 'Filter by section';
    sectionSelect.innerHTML = `
      <option value="">All Sections</option>
    `;
    
    filterDiv.appendChild(departmentSelect);
    filterDiv.appendChild(yearSelect);
    filterDiv.appendChild(sectionSelect);
    
    // --- Assemble UI ---
    searchFilterWrapper.appendChild(searchBox);
    searchFilterWrapper.appendChild(filterDiv);
    
    // --- Clear filters button ---
    const clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'btn btn-outline-primary ms-2 shadow-sm d-flex align-items-center gap-2';
    clearButton.style.height = '40px';
    clearButton.innerHTML = '<i class="fas fa-times"></i> <span>Clear Filters</span>';
    clearButton.title = 'Clear all filters';
    clearButton.onclick = function() {
        searchInput.value = '';
        departmentSelect.value = '';
        yearSelect.value = '';
        yearSelect.style.display = 'none';
        sectionSelect.value = '';
        sectionSelect.style.display = 'none';
        
        // Reset year filter placeholder text
        const firstOption = yearSelect.querySelector('option[value=""]');
        if (firstOption) {
            firstOption.textContent = 'All Years';
        }
        
        filterRows();
    };
    
    searchFilterWrapper.appendChild(clearButton);
    
    // --- Filter status indicator ---
    const filterStatus = document.createElement('div');
    filterStatus.id = 'filterStatus';
    filterStatus.className = 'mt-2 text-muted small';
    filterStatus.style.display = 'none';
    
    searchFilterWrapper.appendChild(filterStatus);
    
    // --- Insert UI for both semesters ---
    const filterContainer1 = document.getElementById('filterContainer1');
    const filterContainer2 = document.getElementById('filterContainer2');
    
    // Create separate filter instances for each semester
    function createFilterInstance(containerId, tableId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Clone the filter wrapper
        const filterWrapper = searchFilterWrapper.cloneNode(true);
        
        // Update IDs to be unique for this instance
        const searchInput = filterWrapper.querySelector('#searchInput');
        const departmentSelect = filterWrapper.querySelector('#departmentFilter');
        const yearSelect = filterWrapper.querySelector('#yearFilter');
        const sectionSelect = filterWrapper.querySelector('#sectionFilter');
        const clearButton = filterWrapper.querySelector('button');
        const filterStatus = filterWrapper.querySelector('#filterStatus');
        
        // Make IDs unique
        const suffix = containerId === 'filterContainer1' ? '1' : '2';
        searchInput.id = `searchInput${suffix}`;
        departmentSelect.id = `departmentFilter${suffix}`;
        yearSelect.id = `yearFilter${suffix}`;
        sectionSelect.id = `sectionFilter${suffix}`;
        filterStatus.id = `filterStatus${suffix}`;
        
        container.appendChild(filterWrapper);
        
        // Add event listeners for this instance
        setupFilterEventListeners(searchInput, departmentSelect, yearSelect, sectionSelect, clearButton, filterStatus, tableId);
    }
    
    // Create filter instances for both semesters
    createFilterInstance('filterContainer1', 'subjectTable1');
    createFilterInstance('filterContainer2', 'subjectTable2');
    
    // Function to setup event listeners for a filter instance
    function setupFilterEventListeners(searchInput, departmentSelect, yearSelect, sectionSelect, clearButton, filterStatus, tableId) {
        // --- Show/hide year filter based on department selection ---
        departmentSelect.addEventListener('change', function() {
            if (this.value) {
                yearSelect.style.display = 'block';
                yearSelect.style.animation = 'fadeIn 0.3s ease-in';
                yearSelect.value = ''; // Reset year selection when department changes
                sectionSelect.value = ''; // Reset section selection
                sectionSelect.style.display = 'none';
                
                // Update placeholder text to be more specific
                const firstOption = yearSelect.querySelector('option[value=""]');
                if (firstOption) {
                    firstOption.textContent = `All ${this.value} Years`;
                }
            } else {
                yearSelect.style.display = 'none';
                yearSelect.value = '';
                sectionSelect.style.display = 'none';
                sectionSelect.value = '';
                
                // Reset placeholder text
                const firstOption = yearSelect.querySelector('option[value=""]');
                if (firstOption) {
                    firstOption.textContent = 'All Years';
                }
            }
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
        
        // --- Show/hide section filter based on year selection ---
        yearSelect.addEventListener('change', function() {
            if (this.value) {
                sectionSelect.style.display = 'block';
                sectionSelect.style.animation = 'fadeIn 0.3s ease-in';
                sectionSelect.value = ''; // Reset section selection when year changes
                
                // Populate sections based on department and year
                populateFilterSectionsForInstance(departmentSelect, yearSelect, sectionSelect);
            } else {
                sectionSelect.style.display = 'none';
                sectionSelect.value = '';
            }
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
        
        // Clear button functionality
        clearButton.onclick = function() {
            searchInput.value = '';
            departmentSelect.value = '';
            yearSelect.value = '';
            yearSelect.style.display = 'none';
            sectionSelect.value = '';
            sectionSelect.style.display = 'none';
            
            // Reset year filter placeholder text
            const firstOption = yearSelect.querySelector('option[value=""]');
            if (firstOption) {
                firstOption.textContent = 'All Years';
            }
            
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        };
        
        // Add event listeners
        searchInput.addEventListener('keyup', function() {
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
        departmentSelect.addEventListener('change', function() {
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
        yearSelect.addEventListener('change', function() {
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
        sectionSelect.addEventListener('change', function() {
            filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId);
        });
    }
    
    // Function to populate sections for filter instances
    function populateFilterSectionsForInstance(departmentSelect, yearSelect, sectionSelect) {
        const department = departmentSelect.value;
        const year = yearSelect.value;
        
        // Clear existing options
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        
        if (department && year && sectionData[department] && sectionData[department][year]) {
            const sections = sectionData[department][year];
            sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.value;
                option.textContent = section.label;
                sectionSelect.appendChild(option);
            });
        }
    }
    
    // --- Update filter status indicator ---
    function updateFilterStatusForInstance(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus) {
        const input = searchInput.value;
        const department = departmentSelect.value;
        const year = yearSelect.value;
        const section = sectionSelect.value;
        
        let statusText = '';
        let activeFilters = [];
        
        if (input) activeFilters.push(`Search: "${input}"`);
        if (department) activeFilters.push(`Department: ${department}`);
        if (year) activeFilters.push(`Year: ${year}`);
        if (section) activeFilters.push(`Section: ${section}`);
        
        if (activeFilters.length > 0) {
            statusText = `<i class="fas fa-filter me-1"></i>Active filters: ${activeFilters.join(', ')}`;
            filterStatus.innerHTML = statusText;
            filterStatus.style.display = 'block';
        } else {
            filterStatus.style.display = 'none';
        }
    }
    
    // --- Filtering logic for specific table ---
    function filterRowsForTable(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus, tableId) {
        const input = searchInput.value.toLowerCase();
        const department = departmentSelect.value;
        const year = yearSelect.value;
        const section = sectionSelect.value;
        
        const table = document.getElementById(tableId);
        if (!table) return;
        
        // Determine semester from table ID
        const semester = tableId === 'subjectTable1' ? '1' : '2';
        
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;
        let totalCount = 0;
        
        rows.forEach(row => {
            // If this is the empty state row, handle separately
            if (row.querySelector('td[colspan]')) {
                row.style.display = 'none';
                return;
            }
            
            totalCount++;
            
            let found = false;
            // Search in all columns except actions
            for (let j = 0; j < row.cells.length - 1; j++) {
                if (row.cells[j] && row.cells[j].textContent.toLowerCase().indexOf(input) > -1) {
                    found = true;
                    break;
                }
            }
            
            // Department is in the 3rd cell (index 2)
            let rowDepartment = row.cells[2] ? row.cells[2].textContent.trim() : '';
            let matchesDepartment = !department || rowDepartment === department;
            
            // Year is in the 4th cell (index 3)
            let rowYear = row.cells[3] ? row.cells[3].textContent.trim() : '';
            let matchesYear = !year || rowYear === year;
            
            // Section is in the 5th cell (index 4)
            let rowSection = row.cells[4] ? row.cells[4].textContent.trim() : '';
            let matchesSection = !section || rowSection === section;
            
            const shouldShow = found && matchesDepartment && matchesYear && matchesSection;
            row.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) {
                visibleCount++;
            }
        });
        
        // Update filter status
        updateFilterStatusForInstance(searchInput, departmentSelect, yearSelect, sectionSelect, filterStatus);
        
        // Update status with count
        if (filterStatus.style.display !== 'none') {
            filterStatus.innerHTML += ` <span class="badge bg-primary ms-2">${visibleCount} of ${totalCount} subjects</span>`;
        }
        
        // Update tab counts after filtering
        if (typeof updateTabCounts === 'function') {
            setTimeout(updateTabCounts, 100);
        }
        
        // Show empty state if all rows are hidden
        const emptyRow = table.querySelector('tbody tr td[colspan]')?.parentElement;
        if (emptyRow) {
            if (visibleCount === 0) {
                emptyRow.style.display = '';
                // Update empty message based on filters
                const emptyCell = emptyRow.querySelector('td[colspan]');
                if (emptyCell) {
                    if (department && year && section) {
                        emptyCell.innerHTML = `<i class="fas fa-search me-2"></i>No subjects found for <strong>${department} ${year} ${section}</strong> in Semester ${semester}.`;
                    } else if (department && year) {
                        emptyCell.innerHTML = `<i class="fas fa-search me-2"></i>No subjects found for <strong>${department} ${year}</strong> in Semester ${semester}.`;
                    } else if (department) {
                        emptyCell.innerHTML = `<i class="fas fa-search me-2"></i>No subjects found for <strong>${department}</strong> in Semester ${semester}.`;
                    } else if (input) {
                        emptyCell.innerHTML = `<i class="fas fa-search me-2"></i>No subjects found matching <strong>"${searchInput.value}"</strong> in Semester ${semester}.`;
                    } else {
                        emptyCell.innerHTML = `<i class="fas fa-info-circle me-2"></i>No subjects found in Semester ${semester}.`;
                    }
                }
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }
    
    // Enhanced semester tab interactions
    const semesterTabs = document.querySelectorAll('#semesterTabs .nav-link');
    
    // Function to update subject counts in tabs
    function updateTabCounts() {
        const semester1Table = document.getElementById('subjectTable1');
        const semester2Table = document.getElementById('subjectTable2');
        
        if (semester1Table && semester2Table) {
            const semester1Count = semester1Table.querySelectorAll('tbody tr:not([style*="display: none"]):not(:has(td[colspan]))').length;
            const semester2Count = semester2Table.querySelectorAll('tbody tr:not([style*="display: none"]):not(:has(td[colspan]))').length;
            
            // Update tab badges with counts
            const semester1Tab = document.getElementById('semester1-tab');
            const semester2Tab = document.getElementById('semester2-tab');
            
            if (semester1Tab) {
                let countBadge = semester1Tab.querySelector('.semester-count');
                if (!countBadge) {
                    countBadge = document.createElement('span');
                    countBadge.className = 'semester-count';
                    semester1Tab.style.position = 'relative';
                    semester1Tab.appendChild(countBadge);
                }
                countBadge.textContent = semester1Count;
                countBadge.style.display = semester1Count > 0 ? 'flex' : 'none';
            }
            
            if (semester2Tab) {
                let countBadge = semester2Tab.querySelector('.semester-count');
                if (!countBadge) {
                    countBadge = document.createElement('span');
                    countBadge.className = 'semester-count';
                    semester2Tab.style.position = 'relative';
                    semester2Tab.appendChild(countBadge);
                }
                countBadge.textContent = semester2Count;
                countBadge.style.display = semester2Count > 0 ? 'flex' : 'none';
            }
        }
    }
    
    // Initial count update
    updateTabCounts();
    
    semesterTabs.forEach(tab => {
        // Add click animation
        tab.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
                updateTabCounts(); // Update counts after tab switch
            }, 150);
        });
        
        // Enhanced hover effects for inactive tabs
        tab.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 6px 20px rgba(0,123,255,0.15)';
            }
        });
        
        tab.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = '';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            }
        });
        
        // Tab switch animation
        tab.addEventListener('shown.bs.tab', function() {
            // Add a subtle pulse effect when tab becomes active
            this.style.animation = 'pulse 0.6s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
                updateTabCounts(); // Update counts after animation
            }, 600);
        });
    });
    
    // Add pulse animation keyframe
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
});
</script>

<script>
@if(session('message') && session('message_type') == 'success')
document.addEventListener('DOMContentLoaded', function() {
    // Show success message with SweetAlert
    const message = '{{ session('message') }}';
    let successText = 'Operation completed successfully!';
    
    if (message.toLowerCase().includes('added')) {
        successText = 'Subject successfully added!';
    } else if (message.toLowerCase().includes('updated')) {
        successText = 'Subject successfully updated!';
    } else if (message.toLowerCase().includes('deleted')) {
        successText = 'Subject successfully deleted!';
    } else {
        successText = message;
    }
    
    Swal.fire({
        title: 'Success!',
        text: successText,
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
@endif
</script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Form submission handling with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    const addSubjectForm = document.getElementById('addSubjectForm');
    
    if (addSubjectForm) {
        addSubjectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            Swal.fire({
                title: 'Adding Subject...',
                text: 'Please wait while we save the subject.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form via AJAX
            fetch(addSubjectForm.action, {
                method: 'POST',
                body: new FormData(addSubjectForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Close loading dialog
                Swal.close();
                
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didClose: () => {
                            // Reload the page to show the new subject
                            window.location.reload();
                        }
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to add subject. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
    
    // Handle edit form submission
    const editSubjectForm = document.getElementById('editSubjectForm');
    if (editSubjectForm) {
        editSubjectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Updating Subject...',
                text: 'Please wait while we update the subject.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(editSubjectForm.action, {
                method: 'POST',
                body: new FormData(editSubjectForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didClose: () => {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update subject. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
    
    // Handle delete form submission
    const deleteSubjectForm = document.getElementById('deleteSubjectForm');
    if (deleteSubjectForm) {
        deleteSubjectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Deleting Subject...',
                html: `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Please wait while we remove the subject from the system.</p>
                    </div>
                `,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(deleteSubjectForm.action, {
                method: 'POST',
                body: new FormData(deleteSubjectForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Subject Deleted!',
                    text: 'The subject has been successfully removed from the system.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete subject. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});

// Simple CSV Upload Functions (outside DOMContentLoaded)
function openCSVUpload() {
    // Get active semester
    const activeTab = document.querySelector('.nav-link.active');
    const currentSemester = activeTab && activeTab.getAttribute('data-semester') === 'Sem 1' ? '1' : '2';
    
    // Show SweetAlert confirmation dialog first
    Swal.fire({
        title: 'Upload CSV for Instructor Assignment',
        html: `
            <div class="text-start">
                <p><strong>You are about to upload CSV for Semester ${currentSemester}</strong></p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>CSV Format Required:</strong><br>
                    Subject Code, Staff ID, Semester, Year, Section<br>
                    <small class="text-muted">Example: BSIT101,STF001,${currentSemester},1,A</small>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This will update instructor assignments for existing subjects in <strong>Semester ${currentSemester}</strong>.
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-upload me-2"></i>Continue to Upload',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Set the semester dropdown
            document.getElementById('targetSemester').value = currentSemester;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('csvUploadModal'));
            modal.show();
        }
    });
}

// Handle CSV form submission
document.addEventListener('DOMContentLoaded', function() {
    const csvForm = document.getElementById('csvUploadForm');
    if (csvForm) {
        csvForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('csvFile');
            const semesterSelect = document.getElementById('targetSemester');
            
            // Validate file
            if (!fileInput.files[0]) {
                Swal.fire({
                    title: 'No File Selected',
                    text: 'Please select a CSV file to upload.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
            
            if (!fileInput.files[0].name.toLowerCase().endsWith('.csv')) {
                Swal.fire({
                    title: 'Invalid File Type',
                    text: 'Please select a CSV file only.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
            
            if (fileInput.files[0].size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: 'File Too Large',
                    text: 'File size must be less than 2MB.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
            
            // Show confirmation
            const semester = semesterSelect.value;
            Swal.fire({
                title: 'Confirm CSV Upload',
                html: `
                    <div class="text-center">
                        <p>Upload CSV for <strong>Semester ${semester}</strong>?</p>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This will update instructor assignments for existing subjects.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Yes, Upload',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing CSV File...',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-success" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>Please wait while we process your CSV file.</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    
                    // Read and process CSV file (demo mode)
                    const file = fileInput.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const csvContent = e.target.result;
                        const lines = csvContent.split('\n').filter(line => line.trim() !== '');
                        const dataRows = lines.slice(1); // Skip header
                        
                        // Simulate processing time
                        setTimeout(() => {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('csvUploadModal')).hide();
                            
                            // Show success message
                            Swal.fire({
                                title: 'Upload Successful! (Demo Mode)',
                                html: `
                                    <div class="text-center">
                                        <i class="fas fa-check-circle text-success" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                        <p><strong>${dataRows.length}</strong> rows processed successfully.</p>
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Demo Mode:</strong> This is a simulation. No actual data was modified.
                                            <br><small>Backend integration needed for real functionality.</small>
                                        </div>
                                        <div class="mt-3">
                                            <strong>CSV Content Preview:</strong>
                                            <div class="text-start mt-2" style="max-height: 150px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                                                ${lines.slice(0, 5).map(line => `<div>${line}</div>`).join('')}
                                                ${lines.length > 5 ? '<div class="text-muted">... and more rows</div>' : ''}
                                            </div>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745',
                                width: '600px'
                            });
                            
                            // Reset form
                            csvForm.reset();
                        }, 1500); // 1.5 second delay to simulate processing
                    };
                    
                    reader.onerror = function() {
                        Swal.fire({
                            title: 'File Read Error',
                            text: 'Could not read the CSV file. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        });
                    };
                    
                    reader.readAsText(file);
                }
            });
        });
    }
});
</script> 