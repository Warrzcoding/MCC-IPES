@php
// Get statistics based on user role
if (Auth::user()->isAdmin()) {
    // Admin dashboard statistics
    $stats = [];
    
    // Total Students
    $stats['students'] = \App\Models\User::where('role', 'student')->count();
    
    // Total Instructors (Teaching Staff)
    $stats['instructors'] = \App\Models\Staff::where('staff_type', 'teaching')->count();
    
    // Total Non-Teaching Staff
    $stats['non_teaching_staff'] = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
    
    // Total Subjects (distinct by subject name)
    $stats['subjects'] = \App\Models\Subject::distinct('sub_name')->count('sub_name');

    // Staff breakdown by department (for teaching staff) and include non-teaching staff
    $staffByDepartment = \App\Models\Staff::select('department', \DB::raw('COUNT(*) as count'))
        ->where('staff_type', 'teaching')
        ->groupBy('department')
        ->pluck('count', 'department');
    
    // Add non-teaching staff as a single category
    $nonTeachingCount = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
    
    // Combine teaching staff by department with non-teaching staff
    $staffByType = collect($staffByDepartment->toArray());
    if ($nonTeachingCount > 0) {
        $staffByType->put('Non-Teaching Staff', $nonTeachingCount);
    }

} else {
    // Student dashboard statistics
    $stats = [];
    
    // My Evaluations (count of distinct staff evaluated by this student)
    $stats['my_evaluations'] = \App\Models\Evaluation::where('user_id', Auth::id())->distinct('staff_id')->count('staff_id');
    
    $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
    $isIrregular = (strtolower(trim(Auth::user()->student_status ?? '')) === 'irregular');

    if ($isIrregular && $currentAcademicYear) {
        // Irregular: totals = locked instructor_selections count for this user
        $lockedByType = \App\Models\InstructorSelection::getLockedSelectionByType(Auth::id(), $currentAcademicYear->id);
        $lockedTeaching = $lockedByType['teaching'] ?? collect();
        $lockedNonTeaching = $lockedByType['non-teaching'] ?? collect();
        $stats['department_instructors'] = $lockedTeaching->count();
        $stats['non_teaching_staff'] = $lockedNonTeaching->count();
        $lockedTeachingStaffIds = $lockedTeaching->pluck('staff_id')->toArray();
        $lockedNonTeachingStaffIds = $lockedNonTeaching->pluck('staff_id')->toArray();
        $evaluatedDepartmentInstructors = $lockedTeachingStaffIds
            ? \App\Models\Evaluation::where('user_id', Auth::id())->whereIn('staff_id', $lockedTeachingStaffIds)->distinct('staff_id')->count('staff_id')
            : 0;
        $evaluatedNonTeachingStaff = $lockedNonTeachingStaffIds
            ? \App\Models\Evaluation::where('user_id', Auth::id())->whereIn('staff_id', $lockedNonTeachingStaffIds)->distinct('staff_id')->count('staff_id')
            : 0;
    } else {
        // Regular: same as before — department instructors from Subject, non-teaching from Staff
        $stats['non_teaching_staff'] = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
        $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;

        $stats['department_instructors'] = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim(Auth::user()->course))])
            ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim(Auth::user()->year_level))])
            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim(Auth::user()->section))])
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
            ->count('assign_instructor');

        $departmentInstructorIds = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim(Auth::user()->course))])
            ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim(Auth::user()->year_level))])
            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim(Auth::user()->section))])
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
            ->pluck('assign_instructor')
            ->unique()
            ->values();

        $departmentStaffIds = \App\Models\Staff::whereIn('full_name', $departmentInstructorIds)
            ->where('staff_type', 'teaching')
            ->pluck('id');

        $evaluatedDepartmentInstructors = \App\Models\Evaluation::where('user_id', Auth::id())
            ->whereIn('staff_id', $departmentStaffIds)
            ->distinct('staff_id')
            ->count('staff_id');

        $nonTeachingStaffIds = \App\Models\Staff::where('staff_type', 'non-teaching')->pluck('id');

        $evaluatedNonTeachingStaff = \App\Models\Evaluation::where('user_id', Auth::id())
            ->whereIn('staff_id', $nonTeachingStaffIds)
            ->distinct('staff_id')
            ->count('staff_id');
    }

    // Calculate completion percentages (same for both regular and irregular)
    $departmentInstructorCompletion = $stats['department_instructors'] > 0
        ? ($evaluatedDepartmentInstructors / $stats['department_instructors']) * 100
        : 0;

    $nonTeachingStaffCompletion = $stats['non_teaching_staff'] > 0
        ? ($evaluatedNonTeachingStaff / $stats['non_teaching_staff']) * 100
        : 0;

    // My Recent Evaluations with staff details
    $my_evaluations = \App\Models\Evaluation::select(
        'staff.full_name as staff_name',
        'staff.staff_type',
        \DB::raw('MAX(evaluations.created_at) as created_at'),
        \DB::raw('AVG(evaluations.response_score) as avg_rating')
    )
    ->join('staff', 'evaluations.staff_id', '=', 'staff.id')
    ->where('evaluations.user_id', Auth::id())
    ->groupBy('evaluations.staff_id', 'staff.full_name', 'staff.staff_type')
    ->orderBy('created_at', 'desc')
    // ->limit(5) // Removed limit to show all evaluated staff
    ->get();

    // Function to format date
    function formatTimeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $timeDiff = time() - $timestamp;

        if ($timeDiff < 60) {
            return $timeDiff . ' sec ago';
        } elseif ($timeDiff < 3600) {
            return floor($timeDiff / 60) . ' min ago';
        } else {
            return date('F j, Y, g:i a', $timestamp); // Full date format
        }
    }
}
@endphp

<style>
    .page-full-width {
        width: 100%;
    }
    .page-full-width .card-body {
        padding: 0.75rem 1rem;
    }
    .page-full-width .text-xs {
        font-size: 0.65rem;
    }
    .page-full-width .h5,
    .page-full-width .h5.mb-0,
    .page-full-width .h5.mb-0.font-weight-bold {
        font-size: 1.05rem;
    }
    .page-full-width h6,
    .page-full-width .card-header h6 {
        font-size: 0.9rem;
    }
    .page-full-width .btn,
    .page-full-width .btn-group .btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
    }
    .page-full-width .fa-2x {
        font-size: 1.65rem;
    }
    .page-full-width .analytics-card {
        min-height: 260px;
        max-width: 98%;
        margin: 0 auto;
    }
    .page-full-width .analytics-card .card-body {
        padding: 0.65rem;
    }
    .page-full-width .analytics-card .card-header h6 {
        font-size: 0.82rem;
    }
    .page-full-width .analytics-section {
        max-width: 98%;
        margin: 0 auto;
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    .page-full-width .circular-card {
        width: 100px !important;
        height: 100px !important;
    }
    .page-full-width .circular-card .h5,
    .page-full-width .circular-card .h5.mb-0 {
        font-size: 0.95rem;
    }
    .page-full-width .circular-card .text-xs {
        font-size: 0.55rem;
    }
    .welcome-alert {
        font-size: 0.85rem;
    }
    .welcome-alert .alert-heading {
        font-size: 1rem;
    }
    @media (max-width: 992px) {
        .page-full-width {
            max-width: 100%;
        }
    }
    @media (max-width: 768px) {
        .page-full-width {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .page-full-width > .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .page-full-width .row.justify-content-center .row.justify-content-center {
            padding-left: 0;
            padding-right: 0;
        }
        .page-full-width .row.justify-content-center .row.justify-content-center .col-xl-3 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .page-full-width .row.justify-content-center .analytics-section {
            padding-left: 0;
            padding-right: 0;
        }
        .page-full-width .row.justify-content-center .analytics-section .col-lg-6,
        .page-full-width .row.justify-content-center .analytics-section .col-12 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        /* Student dashboard mobile centering */
        .page-full-width .row.justify-content-center .circular-card {
            margin: 0 auto !important;
            max-width: 120px;
        }

        .page-full-width .row.justify-content-center .col-12.col-md-8 .card {
            margin: 0 auto !important;
            max-width: 95% !important;
        }

        .page-full-width .row.justify-content-center .col-6.col-sm-6.col-md-4 {
            padding-left: 0.625rem !important;
            padding-right: 0.25rem !important;
        }

        /* Additional mobile centering for student dashboard */
        .page-full-width .row.justify-content-center .col-12.mb-4 .row.justify-content-center {
            margin-left: 1.25rem;
            margin-right: 0;
        }

        /* Evaluation progress section mobile centering */
        .page-full-width .row.justify-content-center .col-12.col-md-8 {
            padding-left: 0.75rem !important;
            padding-right: 0.25rem !important;
        }
    }
</style>

<!-- Welcome Message -->
<div class="row">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible fade show welcome-alert" role="alert">
            <h4 class="alert-heading">Welcome, {{ Auth::user()->full_name }}!</h4>
            <p class="mb-0">
                @if(Auth::user()->isAdmin())
                    You are logged in as an Administrator. You can manage students, instructors, questionnaires, and view evaluation results.
                @else
                    You are logged in as a Student. You can view all staff members and evaluate their performance using the evaluation forms.
                @endif
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</div>

<div class="row page-full-width justify-content-center">
    <div class="col-12">
        @if(Auth::user()->isAdmin())
        <!-- Admin Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="row" style="margin-left: 2%;">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #667eea;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Students</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['students'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2" style="border-left: 4px solid #17a2b8;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Instructors</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['instructors'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #28a745;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Non-Teaching Staff</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['non_teaching_staff'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #ffcf3e;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Subjects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['subjects'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- DATA ANALYTICS CHARTS (Admin Only) -->
        @if(Auth::user()->isAdmin())
        <div class="row mb-4">
            <div class="col-12">
                <div class="row analytics-section" style="background: linear-gradient(135deg, #f8fafc 0%, #e3e6f3 100%); border-radius: 18px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.10); padding: 18px 24px 8px 24px;">

                    <div class="col-lg-6 col-md-12 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 320px; max-height: 340px; border-radius: 18px; transition: box-shadow 0.3s; ">
                            <div class="card-header py-2 bg-white border-0" style="border-radius: 18px 18px 0 0;">
                                <h6 class="m-0 font-weight-bold text-primary">Students per Course (and Evaluated)</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end p-2" style="height: 280px;">
                                <div style="height: 240px; display: flex; align-items: center; justify-content: center; width: 100%;">
                                    <canvas id="studentsPerCourseChart" style="width: 100% !important; height: 100% !important; max-width: 100%; max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Staff by Type Doughnut Chart -->
                     <div class="col-lg-6 col-md-12 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 320px; max-height: 340px; border-radius: 18px; transition: box-shadow 0.3s; ">
                            <div class="card-header py-2 bg-white border-0" style="border-radius: 18px 18px 0 0;">
                                <h6 class="m-0 font-weight-bold text-primary">Teaching Staff by Department & Non-Teaching</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center p-2" style="overflow: hidden; height: 180px; min-height: 120px;">
                                <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                                    <canvas id="staffByTypeChart" style="width: 100% !important; height: 100% !important; max-width: 100%; max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- Categorical Distributions Analysis (Instructor Ratings) -->
                    <div class="col-12 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 340px; max-height: 400px; border-radius: 18px; transition: box-shadow 0.3s; ">
                            <div class="card-header py-2 bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 18px 18px 0 0;">
                                <h6 class="m-0 font-weight-bold text-primary">Live Categorical Distributions Analysis (Instructor Ratings)</h6>
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <div class="badge badge-success px-3 py-2" style="border-radius: 12px; cursor: help;" title="Average score of all instructors in this period expressed as a percentage of the maximum possible score (5.0)">
                                        <i class="fas fa-chart-line mr-1"></i> Overall Performance: {{ $activeOverallPerformancePct }}%
                                    </div>
                                    <div class="badge badge-info px-3 py-2" style="border-radius: 12px;">
                                        <i class="fas fa-users mr-1"></i> Total Instructors: {{ array_sum($instructorRatingDistribution) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center p-3" style="height: 250px;">
                                <div style="height: 220px; width: 100%;">
                                    <canvas id="instructorRatingDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div> 

                   <!-- Staff Performance Improvement per Academic Year/Semester (Enhanced Line Chart) -->
                   <div class="col-12 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 340px; max-height: 400px; border-radius: 18px; transition: box-shadow 0.3s; ">
                            <div class="card-header py-2 bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 18px 18px 0 0;">
                                <h6 class="m-0 font-weight-bold text-primary" id="staffPerformanceTitle">Staff Performance Distribution</h6>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <span id="overallPerfBadge" class="font-weight-bold text-success" style="font-size: 0.88rem;">
                                        Overall Performance: 0%
                                    </span>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" id="prevSemesterBtn" onclick="navigateSemester('prev')">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="nextSemesterBtn" onclick="navigateSemester('next')">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center p-2" style="height: 220px; ">
                                <canvas id="staffPerformanceStatsPerYearChart" height="140" style="width: 100% !important;"></canvas>
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>
        <style>
        .analytics-card {
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.18), 0 1.5px 8px rgba(0,0,0,0.08);
            border-radius: 18px;
            background: #fff;
        }
        .analytics-card:hover {
            box-shadow: 0 16px 48px rgba(102, 126, 234, 0.22), 0 2px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px) scale(1.01);
        }
        .analytics-card .card-header {
            background: linear-gradient(90deg, #e3e6f3 0%, #f8fafc 100%);
        }
        
        /* Circular Card Styles */
        .circular-card {
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }
        
        .circular-card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.25);
        }
        
        .circular-card:hover .circular-overlay {
            background: rgba(255,255,255,0.2);
        }
        
        .circular-card .circular-icon {
            transition: transform 0.3s ease;
        }
        
        .circular-card:hover .circular-icon {
            transform: scale(1.1);
        }
        
        /* Student Card Specific Styles */
        .student-card {
            width: 120px !important;
            height: 120px !important;
        }
        
        /* Evaluation Gallery Styles */
        .evaluation-gallery {
            scrollbar-width: thin;
            scrollbar-color: #667eea #f1f1f1;
        }
        
        .evaluation-gallery::-webkit-scrollbar {
            height: 6px;
        }
        
        .evaluation-gallery::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .evaluation-gallery::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
        }
        
        .evaluation-gallery::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #764ba2, #667eea);
        }
        
        .evaluation-card .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .evaluation-card .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        .avatar-circle {
            transition: transform 0.2s ease;
        }
        
        .evaluation-card:hover .avatar-circle {
            transform: scale(1.1);
        }
        
        /* Completion Badge Styles */
        .completion-badge {
            animation: completionPulse 2s infinite;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .completion-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        @keyframes completionPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Enhanced circular card hover effects */
        .circular-card:hover .completion-badge {
            animation-duration: 1s;
        }
        
        @media (max-width: 768px) {
            .student-card {
                width: 100px !important;
                height: 100px !important;
            }
            .student-card .fa-2x {
                font-size: 1.5rem !important;
            }
            .student-card .h5 {
                font-size: 1rem !important;
            }
            .student-card .text-xs {
                font-size: 0.5rem !important;
            }
            
            .evaluation-card {
                min-width: 250px !important;
                max-width: 270px !important;
            }
        }
        
        @media (max-width: 576px) {
            .student-card {
                width: 90px !important;
                height: 90px !important;
            }
            .student-card .fa-2x {
                font-size: 1.2rem !important;
            }
            .student-card .h5 {
                font-size: 0.9rem !important;
            }
            
            .evaluation-card {
                min-width: 220px !important;
                max-width: 240px !important;
            }
        }
        </style>
        @endif
        <!-- END DATA ANALYTICS CHARTS -->
        
    @else
        <!-- Student Dashboard -->
        <div class="col-12 mb-4">
            <div class="row justify-content-center" style="margin-left: -3px;">
                <div class="col-6 col-sm-6 col-md-4 mb-3" style="display: flex; justify-content: center; align-items: center;">
                    <div class="circular-card shadow student-card" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #ee94e9ff 0%, #630e55ff 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; position: relative; overflow: hidden;" 
                         title="Department Instructors: {{ $evaluatedDepartmentInstructors }} out of {{ $stats['department_instructors'] }} evaluated ({{ round($departmentInstructorCompletion) }}% complete)">
                        <!-- Completion Status Indicator -->
                        @if($evaluatedDepartmentInstructors >= $stats['department_instructors'] && $stats['department_instructors'] > 0)
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="All department instructors evaluated! ✓">
                                <i class="fas fa-check" style="font-size: 0.7rem; color: white;"></i>
                            </div>
                        @elseif($evaluatedDepartmentInstructors > 0)
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="{{ round($departmentInstructorCompletion) }}% completed - {{ $stats['department_instructors'] - $evaluatedDepartmentInstructors }} more to go!">
                                <span style="font-size: 0.6rem; font-weight: bold; color: white;">{{ round($departmentInstructorCompletion) }}%</span>
                            </div>
                        @else
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="No evaluations completed yet - Start evaluating your department instructors!">
                                <i class="fas fa-exclamation" style="font-size: 0.7rem; color: white;"></i>
                            </div>
                        @endif
                        
                        <div class="circular-icon mb-1">
                            <i class="fas fa-chalkboard-teacher fa-2x" style="opacity: 0.9;"></i>
                        </div>
                        <div class="circular-content">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem; opacity: 0.9;">Subject Instructors</div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $evaluatedDepartmentInstructors }}/{{ $stats['department_instructors'] }}
                            </div>
                        </div>
                        <div class="circular-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
                    </div>
                </div>


                <div class="col-6 col-sm-6 col-md-4 mb-3" style="display: flex; justify-content: center; align-items: center;">
                    <div class="circular-card shadow student-card" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; position: relative; overflow: hidden;" 
                         title="Non-Teaching Staff: {{ $evaluatedNonTeachingStaff }} out of {{ $stats['non_teaching_staff'] }} evaluated ({{ round($nonTeachingStaffCompletion) }}% complete)">
                        <!-- Completion Status Indicator -->
                        @if($evaluatedNonTeachingStaff >= $stats['non_teaching_staff'] && $stats['non_teaching_staff'] > 0)
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="All non-teaching staff evaluated! ✓">
                                <i class="fas fa-check" style="font-size: 0.7rem; color: white;"></i>
                            </div>
                        @elseif($evaluatedNonTeachingStaff > 0)
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="{{ round($nonTeachingStaffCompletion) }}% completed - {{ $stats['non_teaching_staff'] - $evaluatedNonTeachingStaff }} more to go!">
                                <span style="font-size: 0.6rem; font-weight: bold; color: white;">{{ round($nonTeachingStaffCompletion) }}%</span>
                            </div>
                        @else
                            <div class="completion-badge" style="position: absolute; top: -2px; right: -2px; width: 28px; height: 28px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 10;" 
                                 title="No evaluations completed yet - Start evaluating non-teaching staff!">
                                <i class="fas fa-exclamation" style="font-size: 0.7rem; color: white;"></i>
                            </div>
                        @endif
                        
                        <div class="circular-icon mb-1">
                            <i class="fas fa-users fa-2x" style="opacity: 0.9;"></i>
                        </div>
                        <div class="circular-content">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem; opacity: 0.9;">Non-teaching</div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $evaluatedNonTeachingStaff }}/{{ $stats['non_teaching_staff'] }}
                            </div>
                        </div>
                        <div class="circular-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
                    </div>
                </div>
                
               
            </div>
            
            <!-- Evaluation Status Summary -->
            <div class="row justify-content-center mt-3" style="margin-left: -3px;">
                <div class="col-12 col-md-8" style="display: flex; justify-content: center;">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px;">
                        <div class="card-body p-3">
                            <h6 class="text-center mb-3 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Your Evaluation Progress!
                            </h6>
                            <div class="row text-center">
                                <!-- Department Instructors Status -->
                                <div class="col-6">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #72ad24ff 0%, #ffcf3e 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                            <i class="fas fa-chalkboard-teacher" style="font-size: 0.7rem; color: white;"></i>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">Instructors</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                @if($evaluatedDepartmentInstructors >= $stats['department_instructors'] && $stats['department_instructors'] > 0)
                                                    <span class="text-success"><i class="fas fa-check-circle"></i> Completed</span>
                                                @elseif($evaluatedDepartmentInstructors > 0)
                                                    <span class="text-warning"><i class="fas fa-clock"></i> In Progress</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Not Started</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Non-Teaching Staff Status -->
                                <div class="col-6">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                            <i class="fas fa-users" style="font-size: 0.7rem; color: white;"></i>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">Non-Teaching</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                @if($evaluatedNonTeachingStaff >= $stats['non_teaching_staff'] && $stats['non_teaching_staff'] > 0)
                                                    <span class="text-success"><i class="fas fa-check-circle"></i> Completed</span>
                                                @elseif($evaluatedNonTeachingStaff > 0)
                                                    <span class="text-warning"><i class="fas fa-clock"></i> In Progress</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Not Started</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Recent Evaluations -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Recent Evaluations</h6>
                </div>
                <div class="card-body p-2">
                    @if($my_evaluations->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't completed any evaluations yet.</p>
                            <small class="text-muted">Start evaluating staff members to see your history here.</small>
                        </div>
                    @else
                        <!-- Mobile-friendly scrollable card gallery -->
                        <div class="evaluation-gallery" style="display: flex; overflow-x: auto; gap: 15px; padding: 10px 5px; scroll-behavior: smooth;">
                            @foreach($my_evaluations as $evaluation)
                                <div class="evaluation-card" style="min-width: 280px; max-width: 300px; flex-shrink: 0;">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <div class="card-body p-3">
                                            <!-- Staff Info Header -->
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-circle me-3" style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem;">
                                                    {{ strtoupper(substr($evaluation->staff_name, 0, 1)) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $evaluation->staff_name }}</h6>
                                                    <span class="badge badge-{{ $evaluation->staff_type === 'teaching' ? 'info' : 'secondary' }}" style="font-size: 0.7rem;">
                                                        {{ ucfirst($evaluation->staff_type) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Rating Section -->
                                            <div class="rating-section mb-3 p-2" style="background: rgba(255,255,255,0.7); border-radius: 10px;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">My Rating:</span>
                                                    <div class="rating-display">
                                                        <span class="h5 mb-0 font-weight-bold text-primary">{{ number_format($evaluation->avg_rating, 1) }}</span>
                                                        <span class="text-muted">/5.0</span>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    @php 
                                                    $rating = $evaluation->avg_rating;
                                                    @endphp
                                                    @if($rating >= 4.51)
                                                        <span class="badge bg-success w-100">Outstanding</span>
                                                    @elseif($rating >= 3.51)
                                                        <span class="badge bg-info w-100" style="color: #fff;">Very Satisfactory</span>
                                                    @elseif($rating >= 2.51)
                                                        <span class="badge bg-warning w-100" style="color: #000;">Satisfactory</span>
                                                    @elseif($rating >= 1.51)
                                                        <span class="badge w-100" style="background-color: #fd7e14; color: white;">Unsatisfactory</span>
                                                    @else
                                                        <span class="badge bg-danger w-100">Poor</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Date Info -->
                                            <div class="date-info text-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ formatTimeAgo($evaluation->created_at) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Scroll indicator for mobile -->
                        <div class="text-center mt-2 d-md-none">
                            <small class="text-muted">
                                <i class="fas fa-arrows-alt-h me-1"></i>
                                Swipe to see more evaluations
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

<!-- Chart.js CDN and Chart Scripts -->
@if(Auth::user()->isAdmin())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Students per Course Bar Chart
const studentsPerCourseLabels = {!! json_encode(array_keys($studentsPerCourse->toArray())) !!};
const studentsPerCourseData = {!! json_encode(array_values($studentsPerCourse->toArray())) !!};
const evaluatedStudentsPerCourseData = studentsPerCourseLabels.map(label => {
    return {!! json_encode($evaluatedStudentsPerCourse->toArray()) !!}[label] || 0;
});
// Custom bar colors for each department
const barColors = studentsPerCourseLabels.map(label => {
    switch(label.toUpperCase()) {
        case 'BSIT': return '#222'; // black
        case 'BSBA': return '#28a745'; // green
        case 'BSHM': return '#800000'; // maroon
        case 'BSED': return '#003366'; // dark blue
        case 'BEED': return '#4fc3f7'; // light blue
        default: return '#888'; // default gray
    }
});
const evaluatedBarColors = studentsPerCourseLabels.map(label => {
    switch(label.toUpperCase()) {
        case 'BSIT': return 'rgba(34,34,34,0.5)';
        case 'BSBA': return 'rgba(40,167,69,0.5)';
        case 'BSHM': return 'rgba(128,0,0,0.5)';
        case 'BSED': return 'rgba(0,51,102,0.5)';
        case 'BEED': return 'rgba(79,195,247,0.5)';
        default: return 'rgba(136,136,136,0.5)';
    }
});
new Chart(document.getElementById('studentsPerCourseChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: studentsPerCourseLabels,
        datasets: [
            {
                label: 'Total Students',
                data: studentsPerCourseData,
                backgroundColor: barColors,
                barPercentage: 0.7,
                categoryPercentage: 0.6,
                order: 1
            },
            {
                label: 'Evaluated Students',
                data: evaluatedStudentsPerCourseData,
                backgroundColor: evaluatedBarColors,
                barPercentage: 0.32, // even thinner
                categoryPercentage: 0.6,
                order: 2 // overlay on top
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } },
        // Overlay mode for closer bars
        interaction: { mode: 'index', intersect: false },
    }
});
// Teaching Staff by Department & Non-Teaching Doughnut Chart
const staffByTypeLabels = {!! json_encode(array_keys($staffByType->toArray())) !!};
const staffByTypeData = {!! json_encode(array_values($staffByType->toArray())) !!};

// Generate specific colors for departments and non-teaching staff
const departmentColorMap = {
    'BSIT': '#000000',      // Black
    'BSBA': '#28a745',      // Green
    'BSHM': '#800000',      // Maroon
    'BSED': '#000080',      // Dark Blue
    'BEED': '#87CEEB',      // Sky Blue
    'Non-Teaching Staff': '#FFC0CB'  // Pink
};

// Fallback colors for any unexpected departments
const fallbackColors = ['#667eea', '#ffcf3e', '#ff7f2f', '#17a2b8', '#6f42c1', '#e83e8c'];

const staffByTypeColors = staffByTypeLabels.map((label, index) => {
    // Check if we have a specific color for this department/category
    if (departmentColorMap[label]) {
        return departmentColorMap[label];
    }
    // Use fallback colors for any unexpected departments
    return fallbackColors[index % fallbackColors.length];
});

new Chart(document.getElementById('staffByTypeChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: staffByTypeLabels,
        datasets: [{
            data: staffByTypeData,
            backgroundColor: staffByTypeColors,
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: { 
            legend: { 
                position: 'bottom',
                align: 'center',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: {
                        size: 10
                    },
                    boxWidth: 12,
                    boxHeight: 12
                },
                maxWidth: 400,
                fullSize: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
// Staff Performance Distribution (by Semester)
const staffStatsSemester = {!! json_encode($staffPerformanceStatsPerSemester) !!};

let staffPerformanceChart;
let currentSemesterIndex = staffStatsSemester.length > 0 ? staffStatsSemester.length - 1 : 0; // Default to latest

function initStaffPerformanceChart() {
    const canvas = document.getElementById('staffPerformanceStatsPerYearChart');
    if (!canvas) return;
    const ctxStaff = canvas.getContext('2d');
    const initialData = getStaffChartData(currentSemesterIndex);

    staffPerformanceChart = new Chart(ctxStaff, {
        type: 'line',
        data: {
            labels: initialData.labels,
            datasets: [
                {
                    label: 'Instructor Distribution',
                    data: initialData.counts,
                    borderColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        
                        const gradient = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                        // Outstanding - Green
                        gradient.addColorStop(0, '#28a745');
                        gradient.addColorStop(0.1, '#28a745');
                        
                        // Very Satisfactory - Blue (around 25%)
                        gradient.addColorStop(0.2, '#17a2b8');
                        gradient.addColorStop(0.3, '#17a2b8');
                        
                        // Satisfactory - Yellow (around 50%)
                        gradient.addColorStop(0.45, '#ffc107');
                        gradient.addColorStop(0.55, '#ffc107');
                        
                        // Unsatisfactory - Orange (around 75%)
                        gradient.addColorStop(0.7, '#fd7e14');
                        gradient.addColorStop(0.8, '#fd7e14');
                        
                        // Poor - Red (around 100%)
                        gradient.addColorStop(0.9, '#dc3545');
                        gradient.addColorStop(1, '#dc3545');
                        
                        return gradient;
                    },
                    backgroundColor: 'rgba(40, 167, 69, 0.05)',
                    fill: true,
                    tension: 0.45,
                    borderWidth: 5,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545'],
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    display: true,
                    align: 'top',
                    offset: 8,
                    color: '#444',
                    font: { weight: 'bold', size: 11 },
                    formatter: function(value, context) {
                        const data = getStaffChartData(currentSemesterIndex);
                        const pct = data.percentages[context.dataIndex];
                        return `${value} (${pct}%)`;
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label;
                            const value = context.parsed.y;
                            const data = getStaffChartData(currentSemesterIndex);
                            const pct = data.percentages[context.dataIndex];
                            return `${label}: ${value} Instructors (${pct}%)`;
                        }
                    }
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: '600' } }
                },
                y: { 
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    title: { display: true, text: 'No. of Instructors', font: { size: 10 } }
                }
            }
        }
    });
    
    updateSemesterChart();
}

function getStaffChartData(index) {
    if (!staffStatsSemester || staffStatsSemester.length === 0) {
        return {
            labels: ['Outstanding', 'Very Satisfactory', 'Satisfactory', 'Unsatisfactory', 'Poor'],
            counts: [0, 0, 0, 0, 0],
            percentages: [0, 0, 0, 0, 0],
            period: 'No Data',
            overall: 0
        };
    }

    const data = staffStatsSemester[index];
    return {
        labels: ['Outstanding', 'Very Satisfactory', 'Satisfactory', 'Unsatisfactory', 'Poor'],
        counts: [data.Outstanding, data.Very_Satisfactory, data.Satisfactory, data.Unsatisfactory, data.Poor],
        percentages: [data.outstanding_pct, data.very_satisfactory_pct, data.satisfactory_pct, data.unsatisfactory_pct, data.poor_pct],
        period: data.period_label,
        overall: data.overall_performance_pct
    };
}

function navigateSemester(direction) {
    if (direction === 'prev' && currentSemesterIndex > 0) {
        currentSemesterIndex--;
    } else if (direction === 'next' && currentSemesterIndex < staffStatsSemester.length - 1) {
        currentSemesterIndex++;
    }
    
    updateSemesterChart();
}

function updateSemesterChart() {
    const newData = getStaffChartData(currentSemesterIndex);
    
    // Update UI elements
    const titleElem = document.getElementById('staffPerformanceTitle');
    if (titleElem) titleElem.textContent = `Staff Performance Distribution (${newData.period})`;
    
    const perfBadge = document.getElementById('overallPerfBadge');
    if (perfBadge) {
        perfBadge.textContent = `Overall Performance: ${newData.overall}%`;
    }
    
    // Update chart
    if (staffPerformanceChart) {
        staffPerformanceChart.data.datasets[0].data = newData.counts;
        staffPerformanceChart.update();
    }
    
    updateSemesterNavigation();
}

function updateSemesterNavigation() {
    const prevBtn = document.getElementById('prevSemesterBtn');
    const nextBtn = document.getElementById('nextSemesterBtn');
    if (prevBtn) prevBtn.disabled = currentSemesterIndex <= 0;
    if (nextBtn) nextBtn.disabled = currentSemesterIndex >= staffStatsSemester.length - 1;
}

initStaffPerformanceChart();

// Instructor Rating Distribution Chart
const distData = {!! json_encode($instructorRatingDistribution) !!};
const distLabels = Object.keys(distData);
const distValues = Object.values(distData);
const totalInstructorsCount = distValues.reduce((a, b) => a + b, 0);

const distColors = {
    'Outstanding': '#28a745',
    'Very Satisfactory': '#17a2b8',
    'Satisfactory': '#ffc107',
    'Unsatisfactory': '#fd7e14',
    'Poor': '#dc3545'
};

new Chart(document.getElementById('instructorRatingDistributionChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: distLabels,
        datasets: [{
            label: 'Number of Instructors',
            data: distValues,
            backgroundColor: distLabels.map(label => distColors[label] || '#888'),
            borderRadius: 8,
            barPercentage: 0.6
        }]
    },
    options: {
        indexAxis: 'y', // Horizontal bar chart
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            datalabels: {
                color: '#444',
                anchor: 'end',
                align: 'right',
                offset: 5,
                font: { weight: 'bold', size: 11 },
                formatter: function(value, context) {
                    if (value === 0) return '';
                    const percentage = totalInstructorsCount > 0 ? ((value / totalInstructorsCount) * 100).toFixed(1) : 0;
                    return `${value} (${percentage}%)`;
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.parsed.x;
                        const percentage = totalInstructorsCount > 0 ? ((value / totalInstructorsCount) * 100).toFixed(1) : 0;
                        return `${value} Instructors (${percentage}%)`;
                    }
                }
            }
        },
        layout: {
            padding: {
                right: 70 // extra space for data labels
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { 
                    precision: 0,
                    stepSize: 1
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});
</script>
@endif