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
    
    // Non-teaching Staff (count staff where staff_type is non-teaching)
    $stats['non_teaching_staff'] = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
    
    // Department Instructors (count unique instructors teaching subjects that match student's course, year_level, section, and active semester)
    // Get the active academic year to filter by semester
    $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
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

<div class="row">
    @if(Auth::user()->isAdmin())
        <!-- Admin Dashboard -->
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
        
        <!-- DATA ANALYTICS CHARTS (Admin Only) -->
        @if(Auth::user()->isAdmin())
        <div class="row mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e3e6f3 100%); border-radius: 18px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.10); padding: 18px 8px 8px 8px;">
           
            <div class="col-lg-6 col-md-12 mb-4 d-flex align-items-stretch">
                <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 320px; max-height: 340px; border-radius: 18px; transition: box-shadow 0.3s;">
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
                <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 320px; max-height: 340px; border-radius: 18px; transition: box-shadow 0.3s;">
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
            
           <!-- Staff Performance Improvement per Academic Year/Semester (Enhanced Line Chart) -->
           <div class="col-12 mb-4 d-flex align-items-stretch">
                <div class="card shadow-lg w-100 h-100 border-0 analytics-card" style="min-height: 340px; max-height: 400px; border-radius: 18px; transition: box-shadow 0.3s;">
                    <div class="card-header py-2 bg-white border-0 d-flex justify-content-between align-items-center" style="border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold text-primary" id="staffPerformanceTitle">Staff Performance Improvement (Avg. Score per Academic Year, Archived)</h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="yearlyViewBtn" onclick="toggleStaffPerformanceView('yearly')">
                                <i class="fas fa-calendar-alt"></i> Yearly
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="semesterViewBtn" onclick="toggleStaffPerformanceView('semester')">
                                <i class="fas fa-calendar"></i> Semester
                            </button>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center p-2" style="height: 220px; width: 100%;">
                        <canvas id="staffPerformanceStatsPerYearChart" height="140" style="width: 100% !important;"></canvas>
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
            <div class="row justify-content-center">
                <div class="col-6 col-sm-4 col-md-3 d-flex justify-content-center mb-3">
                    <div class="circular-card shadow student-card" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; position: relative; overflow: hidden;">
                        <div class="circular-icon mb-1">
                            <i class="fas fa-clipboard-check fa-2x" style="opacity: 0.9;"></i>
                        </div>
                        <div class="circular-content">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem; opacity: 0.9;">Staff Evaluated</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['my_evaluations'] }}</div>
                        </div>
                        <div class="circular-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
                    </div>
                </div>
                
                    <div class="col-6 col-sm-4 col-md-3 d-flex justify-content-center mb-3">
                    <div class="circular-card shadow student-card" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #ff7f2f 0%, #ffcf3e 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; position: relative; overflow: hidden;">
                        <div class="circular-icon mb-1">
                            <i class="fas fa-chalkboard-teacher fa-2x" style="opacity: 0.9;"></i>
                        </div>
                        <div class="circular-content">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem; opacity: 0.9;">Department Instructors</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['department_instructors'] }}</div>
                        </div>
                        <div class="circular-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
                    </div>
                </div>


                <div class="col-6 col-sm-4 col-md-3 d-flex justify-content-center mb-3">
                    <div class="circular-card shadow student-card" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; position: relative; overflow: hidden;">
                        <div class="circular-icon mb-1">
                            <i class="fas fa-users fa-2x" style="opacity: 0.9;"></i>
                        </div>
                        <div class="circular-content">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem; opacity: 0.9;">Non-teaching Staff</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['non_teaching_staff'] }}</div>
                        </div>
                        <div class="circular-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
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
                                                    @if($rating >= 4.5)
                                                        <span class="badge bg-success w-100">Excellent Performance</span>
                                                    @elseif($rating >= 3.5)
                                                        <span class="badge bg-primary w-100">Very Good</span>
                                                    @elseif($rating >= 2.5)
                                                        <span class="badge bg-warning w-100">Good</span>
                                                    @else
                                                        <span class="badge bg-danger w-100">Needs Improvement</span>
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

<!-- Welcome Message -->
<div class="row">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
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
// Average Evaluation Score per Academic Year Line Chart

// Staff Performance Improvement per Academic Year/Semester (Enhanced Line Chart)
// Data for both views
const staffStatsYearly = {!! json_encode($staffPerformanceStatsPerYear) !!};
const staffStatsSemester = {!! json_encode($staffPerformanceStatsPerSemester) !!};

// Debug: Let's see what data we have
console.log('Yearly Data:', staffStatsYearly);
console.log('Semester Data:', staffStatsSemester);

// Global variables for chart management
let staffPerformanceChart;
let currentStaffView = 'yearly';

// Initialize the chart
function initStaffPerformanceChart() {
    const ctxStaff = document.getElementById('staffPerformanceStatsPerYearChart').getContext('2d');
    const gradient = ctxStaff.createLinearGradient(0, 0, ctxStaff.canvas.width, 0);
    gradient.addColorStop(0, '#ff3e9e');
    gradient.addColorStop(1, '#3e9eff');

    // Get initial data (yearly view)
    const initialData = getStaffChartData('yearly');

    staffPerformanceChart = new Chart(ctxStaff, {
        type: 'line',
        data: {
            labels: initialData.labels,
            datasets: [
                {
                    label: 'Avg. Staff Score',
                    data: initialData.avgData,
                    borderColor: gradient,
                    backgroundColor: 'rgba(62, 158, 255, 0.10)',
                    fill: true,
                    tension: 0.45,
                    pointRadius: initialData.avgData.map((v, i) => (i === initialData.maxIdx || i === initialData.minIdx) ? 8 : 5),
                    pointBackgroundColor: initialData.avgData.map((v, i) => i === initialData.maxIdx ? '#ff3e9e' : (i === initialData.minIdx ? '#3e9eff' : gradient)),
                    borderWidth: 5,
                    order: 2,
                    z: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    right: 80,
                    top: 20
                }
            },
            plugins: {
                legend: { display: false },
                datalabels: {
                    color: '#fff',
                    backgroundColor: function(context) {
                        const idx = context.dataIndex;
                        if (idx === initialData.maxIdx) return '#ff3e9e';
                        if (idx === initialData.minIdx) return '#3e9eff';
                        return 'rgba(62,158,255,0.7)';
                    },
                    borderRadius: 8,
                    font: { weight: 'bold', size: 14 },
                    padding: 6,
                    align: 'top',
                    anchor: 'center',
                    formatter: function(value, context) {
                        const idx = context.dataIndex;
                        let label = value.toFixed(2);
                        if (idx === initialData.maxIdx) label += ' ↑';
                        if (idx === initialData.minIdx) label += ' ↓';
                        // Add arrow for increase/decrease
                        if (idx > 0) {
                            const diff = initialData.changeData[idx];
                            if (diff > 0.01) label += ' ▲';
                            else if (diff < -0.01) label += ' ▼';
                        }
                        return label;
                    }
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const viewType = currentStaffView === 'yearly' ? 'Academic Year' : 'Period';
                            return viewType + ': ' + context[0].label;
                        },
                        label: function(context) {
                            const idx = context.dataIndex;
                            let change = '';
                            if (idx > 0) {
                                const diff = initialData.changeData[idx];
                                change = ` (${diff > 0 ? '+' : ''}${diff.toFixed(2)} from prev)`;
                            }
                            return `Avg: ${initialData.avgData[idx].toFixed(2)}${change}\nMin: ${initialData.minData[idx]}, Max: ${initialData.maxData[idx]}`;
                        }
                    }
                },
            },
            scales: {
                y: { beginAtZero: true, max: 5 }
            },
            elements: {
                line: {
                    borderJoinStyle: 'round',
                    shadowOffsetX: 0,
                    shadowOffsetY: 4,
                    shadowBlur: 12,
                    shadowColor: 'rgba(62,158,255,0.25)'
                }
            }
        },
        plugins: [ChartDataLabels, {
            // Custom plugin to draw min/max area
            id: 'minMaxArea',
            afterDatasetsDraw(chart) {
                const {ctx, chartArea: area} = chart;
                const data = getStaffChartData(currentStaffView);
                ctx.save();
                ctx.globalAlpha = 0.18;
                ctx.fillStyle = gradient;
                for (let i = 0; i < data.labels.length - 1; i++) {
                    const x1 = chart.scales.x.getPixelForValue(data.labels[i]);
                    const x2 = chart.scales.x.getPixelForValue(data.labels[i+1]);
                    const yMin1 = chart.scales.y.getPixelForValue(data.minData[i]);
                    const yMin2 = chart.scales.y.getPixelForValue(data.minData[i+1]);
                    const yMax1 = chart.scales.y.getPixelForValue(data.maxData[i]);
                    const yMax2 = chart.scales.y.getPixelForValue(data.maxData[i+1]);
                    ctx.beginPath();
                    ctx.moveTo(x1, yMin1);
                    ctx.lineTo(x2, yMin2);
                    ctx.lineTo(x2, yMax2);
                    ctx.lineTo(x1, yMax1);
                    ctx.closePath();
                    ctx.fill();
                }
                ctx.restore();
            }
        }]
    });
}

// Function to get chart data based on view type
function getStaffChartData(viewType) {
    let data, labels, avgData, minData, maxData;
    
    if (viewType === 'yearly') {
        data = staffStatsYearly;
        labels = data.map(item => item.year);
        avgData = data.map(item => parseFloat(item.avg_score));
        minData = data.map(item => parseFloat(item.min_score));
        maxData = data.map(item => parseFloat(item.max_score));
    } else {
        data = staffStatsSemester;
        labels = data.map(item => item.period_label);
        avgData = data.map(item => parseFloat(item.avg_score));
        minData = data.map(item => parseFloat(item.min_score));
        maxData = data.map(item => parseFloat(item.max_score));
    }
    
    // Calculate period-over-period change
    const changeData = avgData.map((val, idx, arr) => idx === 0 ? 0 : val - arr[idx-1]);
    
    // Find max and min points
    const maxIdx = avgData.indexOf(Math.max(...avgData));
    const minIdx = avgData.indexOf(Math.min(...avgData));
    
    return {
        labels,
        avgData,
        minData,
        maxData,
        changeData,
        maxIdx,
        minIdx
    };
}

// Function to toggle between yearly and semester view
function toggleStaffPerformanceView(viewType) {
    currentStaffView = viewType;
    
    // Update button states
    document.getElementById('yearlyViewBtn').classList.toggle('active', viewType === 'yearly');
    document.getElementById('semesterViewBtn').classList.toggle('active', viewType === 'semester');
    
    // Update chart title
    const title = viewType === 'yearly' 
        ? 'Staff Performance Improvement (Avg. Score per Academic Year, Archived)'
        : 'Staff Performance Improvement (Avg. Score per Semester, Archived)';
    document.getElementById('staffPerformanceTitle').textContent = title;
    
    // Get new data
    const newData = getStaffChartData(viewType);
    
    // Update chart data
    staffPerformanceChart.data.labels = newData.labels;
    staffPerformanceChart.data.datasets[0].data = newData.avgData;
    staffPerformanceChart.data.datasets[0].pointRadius = newData.avgData.map((v, i) => (i === newData.maxIdx || i === newData.minIdx) ? 8 : 5);
    staffPerformanceChart.data.datasets[0].pointBackgroundColor = newData.avgData.map((v, i) => {
        const gradient = staffPerformanceChart.ctx.createLinearGradient(0, 0, staffPerformanceChart.ctx.canvas.width, 0);
        gradient.addColorStop(0, '#ff3e9e');
        gradient.addColorStop(1, '#3e9eff');
        return i === newData.maxIdx ? '#ff3e9e' : (i === newData.minIdx ? '#3e9eff' : gradient);
    });
    
    // Update chart
    staffPerformanceChart.update('active');
}

// Initialize the chart when page loads
initStaffPerformanceChart();
</script>
@endif