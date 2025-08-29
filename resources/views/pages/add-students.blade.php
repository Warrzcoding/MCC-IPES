 @if(session('message') && session('message_type') != 'success')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
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

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
              <h5 class="mb-0">
                    <i class="fas fa-user-graduate me-2"></i>
                     Students Management
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Student Management:</strong> This section allows administrators to add and manage student accounts.
                </div>
                
                <!-- Evaluation Statistics -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $students->where('evaluation_count', 0)->count() }}</h5>
                                <p class="card-text">Never Evaluated</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $students->filter(function($s) { return $s->evaluation_count > 0 && !str_contains($s->evaluation_status, 'Done'); })->count() }}</h5>
                                <p class="card-text">In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-white" style="background-color: #90EE90;">
                            <div class="card-body text-center">
                                @php
                                    $instructorCompletedCount = 0;
                                    foreach($students as $student) {
                                        // Get current academic year and active semester
                                        $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
                                        $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;
                                        
                                        // Get total available instructors for this student with semester filtering
                                        $instructorNames = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($student->course))])
                                            ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($student->year_level))])
                                            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($student->section))])
                                            ->when($activeSemester, function ($q) use ($activeSemester) {
                                                $sem = strtolower(trim((string) $activeSemester));
                                                $aliases = in_array($sem, ['2','2nd','second','second semester','sem 2','semester 2'])
                                                    ? ['2','2nd','second','second semester','sem 2','semester 2']
                                                    : ['1','1st','first','first semester','sem 1','semester 1'];
                                                $q->where(function ($qq) use ($aliases) {
                                                    foreach ($aliases as $a) {
                                                        $qq->orWhereRaw('LOWER(TRIM(semester)) = ?', [$a]);
                                                    }
                                                });
                                            })
                                            ->whereNotNull('assign_instructor')
                                            ->where('assign_instructor', '!=', '')
                                            ->distinct('assign_instructor')
                                            ->pluck('assign_instructor');
                                        
                                        $totalInstructors = \App\Models\Staff::whereIn('full_name', $instructorNames)
                                            ->where('staff_type', 'teaching')
                                            ->count();
                                        
                                        // Get evaluated instructors count
                                        $evaluations = \App\Models\Evaluation::where('user_id', $student->id)->get();
                                        $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
                                        $evaluatedInstructors = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'teaching')->count();
                                        
                                        // Check if all instructors are evaluated
                                        if ($totalInstructors > 0 && $evaluatedInstructors >= $totalInstructors) {
                                            $instructorCompletedCount++;
                                        }
                                    }
                                @endphp
                                <h5 class="card-title">{{ $instructorCompletedCount }}</h5>
                                <p class="card-text">Instructor Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                @php
                                    $nonTeachingCompletedCount = 0;
                                    foreach($students as $student) {
                                        // Get total non-teaching staff
                                        $totalNonTeaching = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
                                        
                                        // Get evaluated non-teaching staff count
                                        $evaluations = \App\Models\Evaluation::where('user_id', $student->id)->get();
                                        $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
                                        $evaluatedNonTeaching = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'non-teaching')->count();
                                        
                                        // Check if all non-teaching staff are evaluated
                                        if ($totalNonTeaching > 0 && $evaluatedNonTeaching >= $totalNonTeaching) {
                                            $nonTeachingCompletedCount++;
                                        }
                                    }
                                @endphp
                                <h5 class="card-title">{{ $nonTeachingCompletedCount }}</h5>
                                <p class="card-text">Non-Teaching Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                @php
                                    $fullyCompletedCount = 0;
                                    foreach($students as $student) {
                                        // Get current academic year and active semester
                                        $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
                                        $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;
                                        
                                        // Get total available instructors for this student with semester filtering
                                        $instructorNames = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($student->course))])
                                            ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($student->year_level))])
                                            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($student->section))])
                                            ->when($activeSemester, function ($q) use ($activeSemester) {
                                                $sem = strtolower(trim((string) $activeSemester));
                                                $aliases = in_array($sem, ['2','2nd','second','second semester','sem 2','semester 2'])
                                                    ? ['2','2nd','second','second semester','sem 2','semester 2']
                                                    : ['1','1st','first','first semester','sem 1','semester 1'];
                                                $q->where(function ($qq) use ($aliases) {
                                                    foreach ($aliases as $a) {
                                                        $qq->orWhereRaw('LOWER(TRIM(semester)) = ?', [$a]);
                                                    }
                                                });
                                            })
                                            ->whereNotNull('assign_instructor')
                                            ->where('assign_instructor', '!=', '')
                                            ->distinct('assign_instructor')
                                            ->pluck('assign_instructor');
                                        
                                        $totalInstructors = \App\Models\Staff::whereIn('full_name', $instructorNames)
                                            ->where('staff_type', 'teaching')
                                            ->count();
                                        $totalNonTeaching = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
                                        
                                        // Get evaluated staff counts
                                        $evaluations = \App\Models\Evaluation::where('user_id', $student->id)->get();
                                        $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
                                        $evaluatedInstructors = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'teaching')->count();
                                        $evaluatedNonTeaching = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'non-teaching')->count();
                                        
                                        // Check if both instructors and non-teaching staff are fully evaluated
                                        $instructorsComplete = ($totalInstructors > 0 && $evaluatedInstructors >= $totalInstructors);
                                        $nonTeachingComplete = ($totalNonTeaching > 0 && $evaluatedNonTeaching >= $totalNonTeaching);
                                        
                                        if ($instructorsComplete && $nonTeachingComplete) {
                                            $fullyCompletedCount++;
                                        }
                                    }
                                @endphp
                                <h5 class="card-title">{{ $fullyCompletedCount }}</h5>
                                <p class="card-text">Fully Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $students->count() }}</h5>
                                <p class="card-text">Total Students</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<style>
@keyframes shake {
  0% { transform: translateX(0); }
  20% { transform: translateX(-4px); }
  40% { transform: translateX(4px); }
  60% { transform: translateX(-4px); }
  80% { transform: translateX(4px); }
  100% { transform: translateX(0); }
}
.shake-animate {
  animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
  animation-iteration-count: infinite;
}

/* Evaluation Status Column Styling - Compact Vertical */
.evaluation-status-compact {
  min-width: 230px;
  width: 230px;
  padding: 0.5rem 0.5rem;
}

.evaluation-status-compact .status-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.25rem;
}

/* Allow evaluation status column to wrap */
.evaluation-status-compact {
  white-space: normal !important;
}

/* Allow evaluation status column to wrap */
.evaluation-status-compact {
  white-space: normal !important;
}

.evaluation-status-compact .badge {
  font-size: 0.75rem;
  padding: 0.25em 0.5em;
  line-height: 1.2;
}

.evaluation-status-compact .status-counts {
  display: flex;
  gap: 0.5rem;
  font-size: 0.65rem;
  line-height: 1;
}

.evaluation-status-compact .count-item {
  white-space: nowrap;
}

.evaluation-status-compact .count-item i {
  width: 10px;
  text-align: center;
  font-size: 0.6rem;
  margin-right: 0.15rem;
}

/* Fixed width for status badges to ensure alignment */
.status-badge-fixed-width {
  display: inline-block;
  min-width: 210px;
  width: 210px;
  text-align: center;
  font-weight: 600;
  font-size: 0.75rem;
  padding: 0.5em 1em;
  line-height: 1.2;
  white-space: nowrap;
}

/* Dual badge layout for separate instructor/non-teaching status */
.dual-badge-container {
  display: flex;
  gap: 2px;
  margin-bottom: 0.25rem;
}

.dual-badge-item {
  flex: 1;
  display: flex;
  justify-content: center;
}

.dual-badge {
  display: block;
  width: 100%;
  text-align: center;
  font-weight: 600;
  font-size: 0.7rem;
  padding: 0.4em 0.2em;
  line-height: 1.1;
  white-space: nowrap;
  border-radius: 0.25rem;
}

.dual-count-container {
  display: flex;
  gap: 2px;
}

.dual-count-item {
  flex: 1;
  font-size: 0.65rem;
  line-height: 1.2;
  text-align: center;
}

/* Statistics Cards Styling */
.card-body .small {
  font-size: 0.85rem;
  opacity: 0.9;
}

.card-body .small div {
  margin-bottom: 2px;
}

.card-body .small i {
  width: 16px;
  text-align: center;
}

/* Action buttons styling - Keep buttons on one line */
.actions-column {
  min-width: 140px !important;
  width: 140px !important;
  white-space: nowrap !important;
  padding: 0.5rem 0.25rem !important;
}

.btn-group-actions {
  min-width: 130px;
  justify-content: center !important;
}

.action-btn {
  min-width: 35px !important;
  width: 35px !important;
  height: 35px !important;
  padding: 0.375rem 0.25rem !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}

.action-btn i {
  font-size: 0.875rem;
}

/* Table responsive improvements */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

#studentsTable {
  min-width: 1300px; /* Ensure minimum width for all columns */
  table-layout: auto; /* Allow natural column sizing */
}

#studentsTable th,
#studentsTable td {
  vertical-align: middle;
  padding: 0.75rem 0.5rem;
}

/* Only truncate the MS Email column (4th column) */
#studentsTable th:nth-child(4),
#studentsTable td:nth-child(4) {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Specific column widths */
#studentsTable th:nth-child(1), /* Profile */
#studentsTable td:nth-child(1) {
  width: 80px;
  min-width: 80px;
}

#studentsTable th:nth-child(2), /* Full Name */
#studentsTable td:nth-child(2) {
  min-width: 150px;
}

#studentsTable th:nth-child(3), /* Username */
#studentsTable td:nth-child(3) {
  min-width: 100px;
}

#studentsTable th:nth-child(4), /* MS Email - Keep fixed width for truncation */
#studentsTable td:nth-child(4) {
  width: 140px;
  min-width: 140px;
  max-width: 140px;
}

/* Email truncation for better display */
.email-truncate {
  display: inline-block;
  max-width: 130px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  vertical-align: middle;
}

#studentsTable th:nth-child(5), /* School ID */
#studentsTable td:nth-child(5) {
  min-width: 100px;
}

#studentsTable th:nth-child(6), /* Course */
#studentsTable td:nth-child(6) {
  min-width: 80px;
}

#studentsTable th:nth-child(7), /* Year Level */
#studentsTable td:nth-child(7) {
  min-width: 90px;
}

#studentsTable th:nth-child(8), /* Section */
#studentsTable td:nth-child(8) {
  min-width: 80px;
}

#studentsTable th:nth-child(11), /* Actions */
#studentsTable td:nth-child(11) {
  width: 140px;
  min-width: 140px;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
  .action-btn {
    min-width: 30px !important;
    width: 30px !important;
    height: 30px !important;
    padding: 0.25rem !important;
  }
  
  .action-btn i {
    font-size: 0.75rem;
  }
  
  .actions-column {
    min-width: 120px !important;
    width: 120px !important;
  }
  
  .btn-group-actions {
    min-width: 110px;
    gap: 0.25rem !important;
  }
}

@media (max-width: 576px) {
  .action-btn {
    min-width: 28px !important;
    width: 28px !important;
    height: 28px !important;
  }
  
  .actions-column {
    min-width: 100px !important;
    width: 100px !important;
  }
  
  .btn-group-actions {
    min-width: 90px;
    gap: 0.125rem !important;
  }
}

/* Additional fixes to prevent button wrapping */
.btn-group-actions .action-btn {
  flex: 0 0 auto !important;
  white-space: nowrap !important;
}

/* Ensure table doesn't break layout */
.table-responsive {
  border-radius: 0.375rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Fix for very small screens - stack buttons vertically if needed */
@media (max-width: 480px) {
  .actions-column {
    min-width: 80px !important;
    width: 80px !important;
  }
  
  .btn-group-actions {
    flex-direction: column !important;
    min-width: 70px;
    gap: 0.125rem !important;
  }
  
  .action-btn {
    min-width: 26px !important;
    width: 26px !important;
    height: 26px !important;
    margin: 0 !important;
  }
  
  .action-btn i {
    font-size: 0.7rem;
  }
}
</style>

<div class="row">
    <!-- Students List -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
                              <div class="d-flex align-items-center gap-2">
                  @php
    $pendingCount = isset($pendingRequestsCount) ? $pendingRequestsCount : 0;
@endphp
<a href="{{ route('dashboard', ['page' => 'pending-requests']) }}" class="btn btn-warning position-relative me-2" id="requestNotificationBtn" type="button">
    <i class="fas fa-bell {{ $pendingCount > 0 ? 'shake-animate' : '' }}"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $pendingCount > 0 ? 'shake-animate' : '' }}" style="font-size: 0.75em;">
        {{ $pendingCount }}
    </span>
</a>
                    <select class="form-select me-2" id="statusFilter" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="Never Evaluated">Never Evaluated</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Done">Done</option>
                    </select>
                    <select class="form-select me-2" id="departmentFilter" style="width: auto;">
                        <option value="">All Departments</option>
                        <option value="BSIT">BSIT </option>
                        <option value="BSHM">BSHM </option>
                        <option value="BSBA">BSBA </option>
                        <option value="BSED">BSED </option>
                        <option value="BEED">BEED </option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if($students->isEmpty())
                    <p class="text-muted text-center py-4">No students found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>MS Email Account</th>
                                    <th>School ID</th>
                                    <th>Course</th>
                                    <th>Year Level</th>
                                    <th>Section</th>
                                    <th>Evaluation Status</th>
                                    <th>Added</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td class="text-center">
                                            @php
                                                $imageUrl = '';
                                                if ($student->profile_image) {
                                                    if (file_exists(public_path('uploads/students/' . $student->profile_image))) {
                                                        $imageUrl = asset('uploads/students/' . $student->profile_image);
                                                    } elseif (file_exists(public_path('uploads/staff/' . $student->profile_image))) {
                                                        $imageUrl = asset('uploads/staff/' . $student->profile_image);
                                                    } else {
                                                        $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=667eea&color=fff&size=50';
                                                    }
                                                } else {
                                                    $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=667eea&color=fff&size=50';
                                                }
                                            @endphp
                                            <img src="{{ $imageUrl }}" 
                                                 alt="Student Avatar" 
                                                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; background-color: #f8f9fa;"
                                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->full_name) }}&background=667eea&color=fff&size=50'">
                                        </td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->username }}</td>
                                        <td><span class="email-truncate" title="{{ $student->email }}">{{ $student->email }}</span></td>
                                        <td>{{ $student->school_id }}</td>
                                        <td>{{ $student->course }}</td>
                                        <td>{{ $student->year_level ?? 'N/A' }}</td>
                                        <td>{{ $student->section ?? 'N/A' }}</td>
                                        <td class="evaluation-status-compact">
                                            @php
                                                // Calculate evaluation counts using same logic as evaluates.blade.php
                                                $evaluations = \App\Models\Evaluation::where('user_id', $student->id)->get();
                                                $distinctStaffIds = $evaluations->pluck('staff_id')->unique();
                                                $teachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'teaching')->count();
                                                $nonTeachingCount = \App\Models\Staff::whereIn('id', $distinctStaffIds)->where('staff_type', 'non-teaching')->count();
                                                
                                                // Get total available staff for this student
                                                $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
                                                $totalTeachingStaff = 0;
                                                $totalNonTeachingStaff = 0;
                                                
                                                if ($currentAcademicYear) {
                                                    // Get active semester for filtering
                                                    $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;
                                                    
                                                    // Get instructor names for student's specific course, year level, section, and active semester
                                                    $instructorNames = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($student->course))])
                                                        ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($student->year_level))])
                                                        ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($student->section))])
                                                        ->when($activeSemester, function ($q) use ($activeSemester) {
                                                            $sem = strtolower(trim((string) $activeSemester));
                                                            $aliases = in_array($sem, ['2','2nd','second','second semester','sem 2','semester 2'])
                                                                ? ['2','2nd','second','second semester','sem 2','semester 2']
                                                                : ['1','1st','first','first semester','sem 1','semester 1'];
                                                            $q->where(function ($qq) use ($aliases) {
                                                                foreach ($aliases as $a) {
                                                                    $qq->orWhereRaw('LOWER(TRIM(semester)) = ?', [$a]);
                                                                }
                                                            });
                                                        })
                                                        ->whereNotNull('assign_instructor')
                                                        ->where('assign_instructor', '!=', '')
                                                        ->distinct('assign_instructor')
                                                        ->pluck('assign_instructor');
                                                    
                                                    // Count actual teaching staff records that match these instructor names
                                                    $totalTeachingStaff = \App\Models\Staff::whereIn('full_name', $instructorNames)
                                                        ->where('staff_type', 'teaching')
                                                        ->count();
                                                    $totalNonTeachingStaff = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
                                                }
                                                
                                                // Determine completion status for each category
                                                $instructorsComplete = ($totalTeachingStaff > 0 && $teachingCount >= $totalTeachingStaff);
                                                $nonTeachingComplete = ($totalNonTeachingStaff > 0 && $nonTeachingCount >= $totalNonTeachingStaff);
                                                $fullyComplete = $instructorsComplete && $nonTeachingComplete;
                                            @endphp
                                            <div class="status-container">
                                                <table style="width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed;">
                                                    <tr>
                                                        <td style="width: 50%; padding: 0 1px 2px 0; text-align: center;">
                                                            @if($instructorsComplete)
                                                                <span class="badge" style="background-color: #90EE90; color: #000; font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">Done</span>
                                                            @elseif($teachingCount > 0)
                                                                <span class="badge bg-warning" style="font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">In Progress</span>
                                                            @else
                                                                <span class="badge bg-primary" style="font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">Never Evaluated</span>
                                                            @endif
                                                        </td>
                                                        <td style="width: 50%; padding: 0 0 2px 1px; text-align: center;">
                                                            @if($nonTeachingComplete)
                                                                <span class="badge bg-success" style="font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">Done</span>
                                                            @elseif($nonTeachingCount > 0)
                                                                <span class="badge bg-warning" style="font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">In Progress</span>
                                                            @else
                                                                <span class="badge bg-primary" style="font-size: 0.7rem; padding: 0.3em 0.2em; width: 100%; display: block; min-width: 80px; box-sizing: border-box;">Never Evaluated</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 0 1px 0 0; text-align: center; font-size: 0.65rem; white-space: nowrap;">
                                                            <span class="text-info">
                                                                <i class="fas fa-chalkboard-teacher"></i> {{ $teachingCount }}/{{ $totalTeachingStaff }} Instructors
                                                            </span>
                                                        </td>
                                                        <td style="padding: 0 0 0 1px; text-align: center; font-size: 0.65rem; white-space: nowrap;">
                                                            <span class="text-secondary">
                                                                <i class="fas fa-users"></i> {{ $nonTeachingCount }}/{{ $totalNonTeachingStaff }} Non-teaching
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td>{{ $student->created_at ? $student->created_at->format('Y-m-d') : '' }}</td>
                                        <td class="actions-column">
                                            @php
                                                $editImageUrl = '';
                                                if ($student->profile_image) {
                                                    if (file_exists(public_path('uploads/students/' . $student->profile_image))) {
                                                        $editImageUrl = asset('uploads/students/' . $student->profile_image);
                                                    } elseif (file_exists(public_path('uploads/staff/' . $student->profile_image))) {
                                                        $editImageUrl = asset('uploads/staff/' . $student->profile_image);
                                                    } else {
                                                        $editImageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=667eea&color=fff&size=100';
                                                    }
                                                } else {
                                                    $editImageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=667eea&color=fff&size=100';
                                                }
                                            @endphp
                                            <div class="btn-group-actions d-flex flex-nowrap justify-content-center gap-1">
                                                <button class="btn btn-sm btn-outline-primary action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    onclick="loadStudentData({{ $student->id }}, '{{ addslashes($student->username) }}', '{{ addslashes($student->email) }}', '{{ addslashes($student->full_name) }}', '{{ addslashes($student->school_id) }}', '{{ $student->course }}', '{{ $student->year_level ?? 'N/A' }}', '{{ addslashes($student->section ?? '') }}', '{{ $editImageUrl }}')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteStudent({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info action-btn" 
                                                        onclick="viewStudentData('{{ addslashes($student->full_name) }}', '{{ addslashes($student->username) }}', '{{ addslashes($student->email) }}', '{{ addslashes($student->school_id) }}', '{{ $student->course }}', '{{ $student->year_level ?? 'N/A' }}', '{{ addslashes($student->section ?? '') }}', '{{ $imageUrl }}', '{{ $student->created_at ? $student->created_at->format('Y-m-d') : '' }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



<!-- Edit Student Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="{{ route('students.update') }}">
                    @csrf
                    <input type="hidden" name="student_id" id="editStudentId">
                    
                    <div class="mb-3 text-center">
                        <div class="mb-2">
                            <img id="editImagePreview" src="https://ui-avatars.com/api/?name=Student&background=667eea&color=fff&size=100" alt="Preview" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; background-color: #f8f9fa;">
                        </div>
                        <label for="editImage" class="form-label">Student Photo</label>
                        <input type="file" class="form-control" id="editImage" name="image" accept="image/*" onchange="previewEditImage(this)">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">MS Email Account</label>
                        <input type="email" class="form-control" readonly="true" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editFullName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSchoolId" class="form-label">School ID</label>
                        <input type="text" class="form-control" id="editSchoolId" name="school_id" required 
                               pattern="[0-9]{4}-[0-9]{4}" maxlength="9" 
                               placeholder="0000-0000" title="Format: 0000-0000">
                        <div class="form-text">Format: 0000-0000 (e.g., 2024-0001)</div>
                    </div>
                    <div class="mb-3">
                        <label for="editCourse" class="form-label">Course</label>
                        <select class="form-select" id="editCourse" name="course" required>
                            <option value="BSIT">BSIT</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSBA">BSBA</option>
                            <option value="BSED">BSED</option>
                            <option value="BEED">BEED</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editYearLevel" class="form-label">Year Level</label>
                        <select class="form-select" id="editYearLevel" name="year_level" required>
                            <option value="">Select year level...</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editSection" class="form-label">Section</label>
                        <select class="form-select" id="editSection" name="section" disabled>
                            <option value="">Select section...</option>
                        </select>
                        <div class="form-text">Section will be populated based on course and year level selection</div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="studentName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('students.delete') }}" id="deleteStudentForm" style="display: inline;">
                    @csrf
                    <input type="hidden" name="student_id" id="deleteStudentId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function deleteStudent(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to delete "${name}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteStudentId').value = id;
            document.getElementById('studentName').textContent = name;
            
            // Submit the delete form
            const deleteForm = document.getElementById('deleteStudentForm');
            if (deleteForm) {
                deleteForm.submit();
            }
        }
    });
}

function viewStudentData(fullName, username, email, schoolId, course, yearLevel, section, image, createdAt) {
    // Use SweetAlert for viewing student details
    const imageUrl = image && image.trim() !== '' ? image : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&background=667eea&color=fff&size=150';
    
    Swal.fire({
        html: `
            <div class="text-center mb-3">
                <img src="${imageUrl}" alt="Student Avatar" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #dee2e6; background-color: #f8f9fa;"
                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=667eea&color=fff&size=100'">
                <h5 class="mt-2 mb-3">${fullName}</h5>
            </div>
            <div class="text-start">
                <p><strong>Username:</strong> ${username}</p>
                <p><strong>MS Email Account:</strong> ${email}</p>
                <p><strong>School ID:</strong> ${schoolId}</p>
                <p><strong>Course:</strong> ${course}</p>
                <p><strong>Year Level:</strong> ${yearLevel}</p>
                <p><strong>Section:</strong> ${section || 'N/A'}</p>
                <p><strong>Member Since:</strong> ${createdAt}</p>
            </div>
        `,
        confirmButtonText: 'Close',
        confirmButtonColor: '#3085d6',
        width: '500px'
    });
}

function loadStudentData(id, username, email, fullName, schoolId, course, yearLevel, section, image) {
    document.getElementById('editStudentId').value = id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editSchoolId').value = schoolId;
    document.getElementById('editCourse').value = course;
    document.getElementById('editYearLevel').value = yearLevel;
    
    // Populate sections based on course and year level, and select the current section
    const currentSection = section || '';
    populateEditSections(currentSection);

    // Set image preview - fix for image display
    const imagePreview = document.getElementById('editImagePreview');
    if (image && image.trim() !== '') {
        imagePreview.src = image;
    } else {
        imagePreview.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(fullName) + '&background=667eea&color=fff&size=100';
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editImagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Section data mapping (same as in signup.blade.php)
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

// Function to populate sections in edit modal based on course and year level
function populateEditSections(selectedSection = '') {
    const courseSelect = document.getElementById('editCourse');
    const yearLevelSelect = document.getElementById('editYearLevel');
    const sectionSelect = document.getElementById('editSection');
    
    const course = courseSelect.value;
    const yearLevel = yearLevelSelect.value;
    
    // Clear existing options
    sectionSelect.innerHTML = '<option value="">Select section...</option>';
    
    if (course && yearLevel && sectionData[course] && sectionData[course][yearLevel]) {
        const sections = sectionData[course][yearLevel];
        sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.value;
            option.textContent = section.label;
            
            // Check if this is the selected section
            if (section.value === selectedSection) {
                option.selected = true;
            }
            
            sectionSelect.appendChild(option);
        });
        
        // Enable the section dropdown
        sectionSelect.disabled = false;
    } else {
        // Disable the section dropdown if course or year level is not selected
        sectionSelect.disabled = true;
    }
}

// School ID formatting function
function formatSchoolId(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Limit to 8 digits
    if (value.length > 8) {
        value = value.substr(0, 8);
    }
    
    // Add dash after 4 digits
    if (value.length >= 4) {
        value = value.substr(0, 4) + '-' + value.substr(4);
    }
    
    input.value = value;
}

    // Initialize search functionality and school ID formatting
    document.addEventListener('DOMContentLoaded', function() {
        // Full-width search functionality
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mb-0'; // Remove mb-3 for better alignment
        searchInput.placeholder = 'Search students...';
        searchInput.style.height = '40px'; // Match button height

        // Create refresh button
        const refreshButton = document.createElement('button');
        refreshButton.type = 'button';
        refreshButton.className = 'btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced';
        refreshButton.style.height = '40px';
        refreshButton.style.fontWeight = 'bold';
        refreshButton.style.fontSize = '1rem';
        refreshButton.innerHTML = '<i class="fas fa-sync-alt fa-spin-on-hover"></i> <span>Refresh</span>';
        refreshButton.onclick = function() {
            location.reload();
        };

        const table = document.getElementById('studentsTable');
        if (table) {
            // Create a container for search and refresh button
            const searchContainer = document.createElement('div');
            searchContainer.className = 'd-flex align-items-center mb-3';
            searchContainer.appendChild(searchInput);
            searchContainer.appendChild(refreshButton);
            
            // Insert search container before the table
            table.parentNode.insertBefore(searchContainer, table);

        // Function to filter table rows
        function filterTable() {
            const searchFilter = searchInput.value.toUpperCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const departmentFilter = document.getElementById('departmentFilter').value;
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName('td');
                let found = false;
                let statusMatch = true;
                let departmentMatch = true;

                // Check search filter (treat empty search as match-all)
                if (!searchFilter) {
                    found = true;
                } else {
                    for (let j = 0; j < cells.length - 2; j++) { // -2 to exclude Actions column
                        if (cells[j] && cells[j].textContent.toUpperCase().indexOf(searchFilter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                // Check status filter (independent of search)
                if (statusFilter) {
                    const statusCell = cells[8]; // Evaluation Status column (moved due to Section column)
                    if (statusCell) {
                        const statusText = statusCell.textContent.trim().toLowerCase();
                        if (statusFilter === 'Done') {
                            statusMatch = statusText.includes('done');
                        } else if (statusFilter === 'In Progress') {
                            statusMatch = statusText.includes('in progress');
                        } else if (statusFilter === 'Never Evaluated') {
                            statusMatch = statusText.includes('never evaluated');
                        }
                    }
                }

                // Check department filter (independent of search)
                if (departmentFilter) {
                    const departmentCell = cells[5]; // Course column as department
                    if (departmentCell) {
                        departmentMatch = departmentCell.textContent.trim().startsWith(departmentFilter);
                    }
                }

                row.style.display = (found && statusMatch && departmentMatch) ? '' : 'none';
            }
        }

        searchInput.addEventListener('keyup', filterTable);
        
        // Add status filter event listener
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', filterTable);
        }
        // Add department filter event listener
        const departmentFilter = document.getElementById('departmentFilter');
        if (departmentFilter) {
            departmentFilter.addEventListener('change', filterTable);
        }
        
        // Run once on load to apply default filters
        filterTable();
    }

    // Add event listeners for school ID formatting
    const schoolIdInputs = document.querySelectorAll('#school_id, #editSchoolId');
    schoolIdInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            formatSchoolId(this);
        });
        
        // Prevent non-numeric characters except dash
        input.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9\-]/.test(char) && e.which !== 8 && e.which !== 46) {
                e.preventDefault();
            }
        });
    });

    // Add event listeners for edit modal course and year level changes
    const editCourseSelect = document.getElementById('editCourse');
    const editYearLevelSelect = document.getElementById('editYearLevel');
    
    if (editCourseSelect) {
        editCourseSelect.addEventListener('change', function() {
            populateEditSections(); // Reset section when course changes
        });
    }
    if (editYearLevelSelect) {
        editYearLevelSelect.addEventListener('change', function() {
            populateEditSections(); // Reset section when year level changes
        });
    }
});

@if(session('message') && session('message_type') == 'success')
document.addEventListener('DOMContentLoaded', function() {
    // Show success message with SweetAlert
    const message = '{{ session('message') }}';
    let successText = 'Operation completed successfully!';
    
    if (message.toLowerCase().includes('added')) {
        successText = 'Student successfully added!';
    } else if (message.toLowerCase().includes('updated')) {
        successText = 'Student successfully updated!';
    } else if (message.toLowerCase().includes('deleted')) {
        successText = 'Student successfully deleted!';
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

// Form submission handling with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit form submission
    const editStudentForm = document.querySelector('form[action*="update-students"]');
    if (editStudentForm) {
        editStudentForm.addEventListener('submit', function(e) {
            // Allow normal form submission to let Laravel handle redirects/validation
            
            Swal.fire({
                title: 'Updating Student...',
                text: 'Please wait while we update the student information.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(editStudentForm.action, {
                method: 'POST',
                body: new FormData(editStudentForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Student updated successfully!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update student. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
    
    // Handle delete form submission
    const deleteStudentForm = document.getElementById('deleteStudentForm');
    if (deleteStudentForm) {
        deleteStudentForm.addEventListener('submit', function(e) {
            // Allow normal form submission to let Laravel handle redirects/validation
            
            Swal.fire({
                title: 'Deleting Student...',
                text: 'Please wait while we delete the student.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(deleteStudentForm.action, {
                method: 'POST',
                body: new FormData(deleteStudentForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Student Deleted!',
                    text: 'The student has been successfully removed from the system.',
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
                    text: 'Failed to delete student. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});
</script> 