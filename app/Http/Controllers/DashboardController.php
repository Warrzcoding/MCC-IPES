<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Question;
use App\Models\Evaluation;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use App\Models\RequestSignin;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pendingRequestsCount = 0; // Always define to avoid undefined variable error
        // Get current page from URL parameter
        $page = $request->get('page', 'dashboard');
        $allowed_pages = [
            'dashboard', 'add-students', 'add-staff', 'subject-management', 'academicyear',
            'questionnaires', 'staff-ratings', 'department-ratings', 'overall-ratings', 'profile', 'staff-list',
            'evaluates', 'pending-requests', 'rejected-requests' // <-- Ensure this is included
        ];

        if (!in_array($page, $allowed_pages)) {
            $page = 'dashboard';
        }

        // Role-based access control
        $user = Auth::user();

        // --- Analytics Data for Dashboard Charts (always set for admin) ---
        if ($user->isAdmin()) {
            $studentsPerCourse = \App\Models\User::where('role', 'student')
                ->select('course', \DB::raw('count(*) as total'))
                ->groupBy('course')
                ->pluck('total', 'course');

            $evaluatedStudentsPerCourse = \App\Models\Evaluation::join('users', 'evaluations.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->select('users.course', \DB::raw('count(distinct evaluations.user_id) as evaluated'))
                ->groupBy('users.course')
                ->pluck('evaluated', 'users.course');

            $staffByType = \App\Models\Staff::select('staff_type', \DB::raw('count(*) as total'))
                ->groupBy('staff_type')
                ->pluck('total', 'staff_type');

            $avgScorePerYear = \App\Models\Evaluation::join('academic_years', 'evaluations.academic_year_id', '=', 'academic_years.id')
                ->select('academic_years.year', \DB::raw('avg(evaluations.response_score) as avg_score'))
                ->groupBy('academic_years.year')
                ->orderBy('academic_years.year')
                ->pluck('avg_score', 'academic_years.year');

            // Approach 1 & 3: Staff performance improvement and distribution per academic year (from save_eval_result)
            // Get data grouped by academic year only (for yearly view)
            $staffPerformanceStatsPerYear = \DB::table('save_eval_result')
                ->join('academic_years', 'save_eval_result.academic_year_id', '=', 'academic_years.id')
                ->select(
                    'academic_years.year',
                    \DB::raw('avg(save_eval_result.response_score) as avg_score'),
                    \DB::raw('min(save_eval_result.response_score) as min_score'),
                    \DB::raw('max(save_eval_result.response_score) as max_score')
                )
                ->groupBy('academic_years.year')
                ->orderBy('academic_years.year')
                ->get();

            // Get data grouped by academic year and semester (for semester view)
            // First, let's get all unique academic years that have evaluation data
            $academicYearsWithData = \DB::table('save_eval_result')
                ->join('academic_years', 'save_eval_result.academic_year_id', '=', 'academic_years.id')
                ->select('academic_years.year')
                ->distinct()
                ->pluck('academic_years.year');

            // Now get semester data, ensuring we have both semesters for each year
            $staffPerformanceStatsPerSemester = collect();
            
            foreach ($academicYearsWithData as $year) {
                // Get data for both semesters of this year
                for ($semester = 1; $semester <= 2; $semester++) {
                    $semesterData = \DB::table('save_eval_result')
                        ->join('academic_years', 'save_eval_result.academic_year_id', '=', 'academic_years.id')
                        ->where('academic_years.year', $year)
                        ->where('academic_years.semester', $semester)
                        ->select(
                            \DB::raw("'{$year}' as year"),
                            \DB::raw("'{$semester}' as semester"),
                            \DB::raw('avg(save_eval_result.response_score) as avg_score'),
                            \DB::raw('min(save_eval_result.response_score) as min_score'),
                            \DB::raw('max(save_eval_result.response_score) as max_score')
                        )
                        ->first();
                    
                    if ($semesterData && $semesterData->avg_score !== null) {
                        $semesterLabel = $semester == 1 ? '1st Sem' : '2nd Sem';
                        $semesterData->period_label = "{$year} - {$semesterLabel}";
                        $staffPerformanceStatsPerSemester->push($semesterData);
                    }
                }
            }
            
            // If no semester-specific data found, fall back to the original query
            if ($staffPerformanceStatsPerSemester->isEmpty()) {
                $staffPerformanceStatsPerSemester = \DB::table('save_eval_result')
                    ->join('academic_years', 'save_eval_result.academic_year_id', '=', 'academic_years.id')
                    ->select(
                        'academic_years.year',
                        \DB::raw('COALESCE(academic_years.semester, 1) as semester'),
                        \DB::raw('avg(save_eval_result.response_score) as avg_score'),
                        \DB::raw('min(save_eval_result.response_score) as min_score'),
                        \DB::raw('max(save_eval_result.response_score) as max_score'),
                        \DB::raw('CONCAT(academic_years.year, " - ", 
                            CASE 
                                WHEN COALESCE(academic_years.semester, 1) = 1 THEN "1st Sem"
                                WHEN COALESCE(academic_years.semester, 1) = 2 THEN "2nd Sem"
                                ELSE CONCAT(COALESCE(academic_years.semester, 1), "th Sem")
                            END
                        ) as period_label')
                    )
                    ->groupBy('academic_years.year', 'academic_years.semester')
                    ->orderBy('academic_years.year')
                    ->orderBy('academic_years.semester')
                    ->get();
            }

            // Debug: Let's also check what academic years we have
            $debugAcademicYears = \DB::table('academic_years')
                ->select('id', 'year', 'semester')
                ->orderBy('year')
                ->orderBy('semester')
                ->get();
            
            // Debug: Let's check what evaluation data we have
            $debugEvaluationData = \DB::table('save_eval_result')
                ->join('academic_years', 'save_eval_result.academic_year_id', '=', 'academic_years.id')
                ->select('academic_years.year', 'academic_years.semester', \DB::raw('count(*) as eval_count'))
                ->groupBy('academic_years.year', 'academic_years.semester')
                ->orderBy('academic_years.year')
                ->orderBy('academic_years.semester')
                ->get();

            // Temporary debug - you can remove this after checking
            \Log::info('Debug Academic Years:', $debugAcademicYears->toArray());
            \Log::info('Debug Evaluation Data:', $debugEvaluationData->toArray());
            \Log::info('Staff Performance Stats Per Year:', $staffPerformanceStatsPerYear->toArray());
            \Log::info('Staff Performance Stats Per Semester:', $staffPerformanceStatsPerSemester->toArray());
        } else {
            $studentsPerCourse = collect();
            $evaluatedStudentsPerCourse = collect();
            $staffByType = collect();
            $avgScorePerYear = collect();
            $staffPerformanceStatsPerYear = collect();
            $staffPerformanceStatsPerSemester = collect();
        }
        
        // Students can only access: dashboard, staff-list, evaluates, profile
        if ($user->isStudent()) {
            $student_pages = ['dashboard', 'staff-list', 'evaluates', 'profile'];
            if (!in_array($page, $student_pages)) {
                $page = 'dashboard';
            }
        }
        
        // Admins can access all pages
        if ($user->isAdmin()) {
            // All pages are allowed for admin
        }

        // Set page title based on current page
        $page_titles = [
            'dashboard' => 'Dashboard',
            'add-students' => 'Add Students',
            'add-staff' => 'Add Staff',
            'subject-management' => 'Subject Management',
            'academicyear' => 'Academic Year',
            'questionnaires' => 'Questionnaires',
            'staff-ratings' => 'Individual Ratings',
            'department-ratings' => 'Department Ratings',
            'overall-ratings' => 'Overall Ratings',
            'profile' => 'Profile',
            'staff-list' => 'Staff List',
            'evaluates' => 'Evaluates Staff',
            'pending-requests' => 'Pending Requests',
            'rejected-requests' => 'Rejected Requests' // <-- Add this
        ];

        $current_title = $page_titles[$page];

        // Load specific data based on page
        $staff = null;
        $students = null;
        $subjects = null;
        $questionnaires_data = null;
        $admins = null;
        $years = null;
        
        if ($page === 'add-staff') {
            $staff = Staff::orderBy('full_name')->get();
        }
        
        if ($page === 'subject-management') {
            $subjects = \App\Models\Subject::orderBy('sub_name')->get();
            
            // Add instructor_staff_id to each subject for edit functionality
            $subjects->each(function ($subject) {
                if ($subject->assign_instructor) {
                    $staff = Staff::where('full_name', $subject->assign_instructor)->first();
                    $subject->instructor_staff_id = $staff ? $staff->staff_id : null;
                } else {
                    $subject->instructor_staff_id = null;
                }
            });
            
            // Load staff data for instructor assignment dropdown
            $staff = Staff::where('staff_type', 'teaching')->orderBy('full_name')->get();
        }
        
        if ($page === 'add-students') {
            // Get the active academic year (where is_active = 1)
            $currentAcademicYear = AcademicYear::where('is_active', 1)->first();
            
            // Get all students with evaluation status
            $students = User::where('role', 'student')
                ->orderBy('full_name')
                ->get()
                ->map(function ($student) use ($currentAcademicYear) {
                    // Get evaluation count for current academic year
                    $evaluationCount = 0;
                    $evaluationStatus = 'Never Evaluated';
                    
                    if ($currentAcademicYear) {
                        // Get questions for current academic year
                        $academicYearQuestions = \App\Models\Question::where('academic_year_id', $currentAcademicYear->id)->pluck('id');
                        
                        if ($academicYearQuestions->count() > 0) {
                            // Count distinct staff evaluated by this student in current academic year
                            $evaluationCount = \App\Models\Evaluation::where('user_id', $student->id)
                                ->whereIn('question_id', $academicYearQuestions)
                                ->distinct('staff_id')
                                ->count('staff_id');
                            
                            // Get instructor names for student's specific course, year level, and section
                            $instructorNames = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($student->course))])
                                ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($student->year_level))])
                                ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($student->section))])
                                ->whereNotNull('assign_instructor')
                                ->where('assign_instructor', '!=', '')
                                ->distinct('assign_instructor')
                                ->pluck('assign_instructor');
                            
                            // Count actual teaching staff records that match these instructor names
                            $instructorsCount = \App\Models\Staff::whereIn('full_name', $instructorNames)
                                ->where('staff_type', 'teaching')
                                ->count();
                            
                            // Get all non-teaching staff count
                            $nonTeachingStaffCount = \App\Models\Staff::where('staff_type', 'non-teaching')->count();
                            
                            // Total staff count = instructors for student's course/year + all non-teaching staff
                            $totalStaffCount = $instructorsCount + $nonTeachingStaffCount;
                            
                            if ($evaluationCount > 0) {
                                if ($evaluationCount >= $totalStaffCount) {
                                    $evaluationStatus = "Done ({$evaluationCount}/{$totalStaffCount} staff)";
                                } else {
                                    $evaluationStatus = "In Progress ({$evaluationCount}/{$totalStaffCount} staff)";
                                }
                            }
                        }
                    }
                    
                    $student->evaluation_count = $evaluationCount;
                    $student->evaluation_status = $evaluationStatus;
                    
                    return $student;
                });
            // Get count of pending signup requests
            $pendingRequestsCount = RequestSignin::where('status', 'pending')->count();
        }
        
        if ($page === 'questionnaires') {
            // Get current academic year (active)
            $currentAcademicYear = AcademicYear::where('is_active', true)->first();

            // --- Auto open/close logic for schedule ---
            if ($currentAcademicYear && ($currentAcademicYear->open_at || $currentAcademicYear->close_at)) {
                $now = now();
                $openAt = $currentAcademicYear->open_at ? \Carbon\Carbon::parse($currentAcademicYear->open_at) : null;
                $closeAt = $currentAcademicYear->close_at ? \Carbon\Carbon::parse($currentAcademicYear->close_at) : null;
                $shouldBeOpen = true;
                if ($openAt && $now->lt($openAt)) {
                    $shouldBeOpen = false;
                }
                if ($closeAt && $now->gte($closeAt)) {
                    $shouldBeOpen = false;
                }
                $questionsToCheck = Question::where('academic_year_id', $currentAcademicYear->id)->get();
                if ($questionsToCheck->isNotEmpty() && $questionsToCheck->first()->is_open !== $shouldBeOpen) {
                    Question::where('academic_year_id', $currentAcademicYear->id)->update(['is_open' => $shouldBeOpen]);
                }
            }
            // --- End auto open/close logic ---

            // Get all questions for the current academic year
            $questions = [];
            $teachingCount = 0;
            $nonTeachingCount = 0;
            $questionnaireStatus = true;
            if ($currentAcademicYear) {
                $questions = Question::where('academic_year_id', $currentAcademicYear->id)
                    ->orderBy('staff_type')
                    ->get()
                    ->toArray();
                // Count questions by staff type
                foreach ($questions as $question) {
                    if ($question['staff_type'] == 'teaching') {
                        $teachingCount++;
                    } else {
                        $nonTeachingCount++;
                    }
                }
                // Get questionnaire status (from first question)
                $questionnaireStatus = !empty($questions) ? $questions[0]['is_open'] : true;
            }
            // Response type options
            $responseTypes = [
                'rating_5' => 'Rating Scale (Poor, Fair, Good, Very Good, Excellent)',
                'frequency_4' => 'Frequency (Always, Most of the Time, Sometimes, Rarely)',
                'agreement_5' => 'Agreement (Strongly Disagree, Disagree, Neutral, Agree, Strongly Agree)',
                'satisfaction_5' => 'Satisfaction (Very Dissatisfied, Dissatisfied, Neutral, Satisfied, Very Satisfied)',
                'yes_no' => 'Yes/No',
                'text' => 'Text Response'
            ];
            $questionnaires_data = [
                'current_academic_year' => $currentAcademicYear ? $currentAcademicYear->toArray() : null,
                'questions' => $questions,
                'teaching_count' => $teachingCount,
                'non_teaching_count' => $nonTeachingCount,
                'questionnaire_status' => $questionnaireStatus,
                'response_types' => $responseTypes
            ];
        }
        
        if ($page === 'academicyear') {
            $years = \App\Models\AcademicYear::orderByDesc('year')->get();
        }
        
        if ($page === 'profile' && $user->canManageAdmins()) {
            $admins = User::where('role', 'admin')->orderBy('full_name')->get();
        }

        if ($page === 'evaluates') {
            // Get the active academic year (where is_active = 1)
            $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
            $isOpen = false;
            $teachingQuestions = collect();
            $nonTeachingQuestions = collect();

            if ($currentAcademicYear) {
                // Get questions that belong to the active academic year and are open
                $openQuestions = \App\Models\Question::where('academic_year_id', $currentAcademicYear->id)
                    ->where('is_open', 1)
                    ->get();

                $isOpen = $openQuestions->count() > 0;
                $teachingQuestions = $openQuestions->where('staff_type', 'teaching');
                $nonTeachingQuestions = $openQuestions->where('staff_type', 'non-teaching');
            }

            // Filter teaching staff based on student's course, year level, and section from subjects table
            $user = Auth::user();
            $studentCourse = $user->course;
            $studentYearLevel = $user->year_level;
            $studentSection = $user->section;
            
            // Get teaching staff directly from subjects table with proper filtering including semester
            // First get the instructor names from subjects table
            $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;
            $instructorNames = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($studentCourse))])
                ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($studentYearLevel))])
                ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($studentSection))])
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
                ->distinct()
                ->pluck('assign_instructor')
                ->toArray();
            
            // Then get the staff records that match these instructor names and are teaching staff
            $teachingStaff = \App\Models\Staff::whereIn('full_name', $instructorNames)
                ->where('staff_type', 'teaching')
                ->get();
                
            $nonTeachingStaff = \App\Models\Staff::where('staff_type', 'non-teaching')->get();

            // DEBUG: Log the filtering results (remove this after testing)
            \Log::info('Student Evaluation Filter Debug (DashboardController)', [
                'student_course' => $studentCourse,
                'student_year' => $studentYearLevel, 
                'student_section' => $studentSection,
                'active_semester' => $activeSemester,
                'instructor_names_from_subjects' => $instructorNames,
                'teaching_staff_count' => $teachingStaff->count(),
                'teaching_staff_names' => $teachingStaff->pluck('full_name')->toArray(),
                'non_teaching_staff_count' => $nonTeachingStaff->count(),
                'non_teaching_staff_names' => $nonTeachingStaff->pluck('full_name')->toArray()
            ]);

            // Pass all variables to the dashboard view, not directly to the page
            return view('dashboard', array_merge(
                compact(
                    'isOpen',
                    'teachingQuestions',
                    'nonTeachingQuestions',
                    'teachingStaff',
                    'nonTeachingStaff',
                    'currentAcademicYear',
                    // Analytics data:
                    'studentsPerCourse',
                    'evaluatedStudentsPerCourse',
                    'staffByType',
                    'avgScorePerYear'
                ),
                [
                    'page' => $page,
                    'current_title' => $current_title,
                    'page_titles' => $page_titles,
                    'staff' => $staff,
                    'students' => $students,
                    'subjects' => $subjects,
                    'questionnaires_data' => $questionnaires_data,
                    'admins' => $admins,
                    'years' => $years,
                    'pendingRequestsCount' => $pendingRequestsCount, // pass to view
                ]
            ));
        }

        if ($page === 'staff-ratings' || $page === 'department-ratings') {
            // Fetch ALL staff members WITH evaluations (both teaching and non-teaching)
            // Use INNER JOIN to exclude staff without evaluations
            $staffRatings = \App\Models\Staff::select(
                'staff.id',
                'staff.staff_id',
                'staff.full_name',
                'staff.email',
                'staff.department',
                'staff.staff_type',
                'staff.image_path',
                \DB::raw('AVG(evaluations.response_score) AS average_rating'),
                \DB::raw('COUNT(DISTINCT evaluations.user_id) AS total_evaluations'),
                \DB::raw('COUNT(CASE WHEN evaluations.comments IS NOT NULL AND evaluations.comments != "" THEN 1 END) AS total_comments')
            )
            ->join('evaluations', 'staff.id', '=', 'evaluations.staff_id')
            // Removed the filter to include both teaching and non-teaching staff
            ->groupBy('staff.id', 'staff.staff_id', 'staff.full_name', 'staff.email', 'staff.department', 'staff.staff_type', 'staff.image_path')
            ->orderBy('average_rating', 'desc')
            ->orderBy('staff.full_name', 'asc') // Secondary sort by name
            ->get();

            return view('dashboard', compact(
                'page',
                'current_title',
                'page_titles',
                'staff',
                'students',
                'subjects',
                'questionnaires_data',
                'admins',
                'staffRatings',
                'years',
                // Analytics variables
                'studentsPerCourse',
                'evaluatedStudentsPerCourse',
                'staffByType',
                'avgScorePerYear'
            ));
        }

        if ($page === 'overall-ratings') {
            // Fetch TEACHING staff members WITH evaluations, sorted by rating (highest to lowest)
            $teachingStaffRatings = \App\Models\Staff::select(
                'staff.id',
                'staff.staff_id',
                'staff.full_name',
                'staff.email',
                'staff.department',
                'staff.staff_type',
                'staff.image_path',
                \DB::raw('AVG(evaluations.response_score) AS average_rating'),
                \DB::raw('COUNT(DISTINCT evaluations.user_id) AS total_evaluations'),
                \DB::raw('COUNT(CASE WHEN evaluations.comments IS NOT NULL AND evaluations.comments != "" THEN 1 END) AS total_comments')
            )
            ->join('evaluations', 'staff.id', '=', 'evaluations.staff_id')
            ->where('staff.staff_type', 'teaching')
            ->groupBy('staff.id', 'staff.staff_id', 'staff.full_name', 'staff.email', 'staff.department', 'staff.staff_type', 'staff.image_path')
            ->orderBy('average_rating', 'desc')
            ->orderBy('staff.full_name', 'asc') // Secondary sort by name
            ->get();

            // Fetch NON-TEACHING staff members WITH evaluations, sorted by rating (highest to lowest)
            $nonTeachingStaffRatings = \App\Models\Staff::select(
                'staff.id',
                'staff.staff_id',
                'staff.full_name',
                'staff.email',
                'staff.department',
                'staff.staff_type',
                'staff.image_path',
                \DB::raw('AVG(evaluations.response_score) AS average_rating'),
                \DB::raw('COUNT(DISTINCT evaluations.user_id) AS total_evaluations'),
                \DB::raw('COUNT(CASE WHEN evaluations.comments IS NOT NULL AND evaluations.comments != "" THEN 1 END) AS total_comments')
            )
            ->join('evaluations', 'staff.id', '=', 'evaluations.staff_id')
            ->where('staff.staff_type', 'non-teaching')
            ->groupBy('staff.id', 'staff.staff_id', 'staff.full_name', 'staff.email', 'staff.department', 'staff.staff_type', 'staff.image_path')
            ->orderBy('average_rating', 'desc')
            ->orderBy('staff.full_name', 'asc') // Secondary sort by name
            ->get();

            // Combine both collections for backward compatibility (if needed elsewhere)
            $allStaffRatings = $teachingStaffRatings->merge($nonTeachingStaffRatings);

            return view('dashboard', compact(
                'page',
                'current_title',
                'page_titles',
                'staff',
                'students',
                'subjects',
                'questionnaires_data',
                'admins',
                'allStaffRatings',
                'teachingStaffRatings',
                'nonTeachingStaffRatings',
                'years',
                // Analytics variables
                'studentsPerCourse',
                'evaluatedStudentsPerCourse',
                'staffByType',
                'avgScorePerYear'
            ));
        }

        // Pending requests logic
        $queryPending = \App\Models\RequestSignin::where('status', 'pending');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $queryPending->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('school_id', 'like', "%$search%") ;
            });
        }
        $pendingRequests = $queryPending->orderBy('created_at', 'desc')->paginate(10, ['*'], 'pending_page')->withQueryString();

        // Rejected requests logic
        $queryRejected = \App\Models\RequestSignin::where('status', 'rejected');
        if ($request->filled('search_rejected')) {
            $searchRejected = $request->input('search_rejected');
            $queryRejected->where(function($q) use ($searchRejected) {
                $q->where('full_name', 'like', "%$searchRejected%")
                  ->orWhere('username', 'like', "%$searchRejected%")
                  ->orWhere('email', 'like', "%$searchRejected%")
                  ->orWhere('school_id', 'like', "%$searchRejected%") ;
            });
        }
        $rejectedRequests = $queryRejected->orderBy('created_at', 'desc')->paginate(10, ['*'], 'rejected_page')->withQueryString();

        return view('dashboard', compact(
            'page',
            'current_title',
            'page_titles',
            'staff',
            'students',
            'subjects',
            'questionnaires_data',
            'admins',
            'years',
            // Analytics variables
            'studentsPerCourse',
            'evaluatedStudentsPerCourse',
            'staffByType',
            'avgScorePerYear',
            'staffPerformanceStatsPerYear', // pass to view
            'staffPerformanceStatsPerSemester', // pass semester data to view
            'pendingRequestsCount',
            'pendingRequests',
            'rejectedRequests' // Always include this
        ));
    }

    public function updateProfile(Request $request)
    {
        \Log::info('Profile update method called', ['user_id' => Auth::id(), 'request_data' => $request->all()]);
        $user = Auth::user();
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'school_id' => $user->role === 'student' ? 'required|string|max:255' : 'nullable|string|max:255',
            'course' => $user->role === 'student' || $user->role === 'admin' ? 'required|string|max:255' : 'nullable|string|max:255',
            'year_level' => $user->role === 'student' ? 'required|string|in:1st Year,2nd Year,3rd Year,4th Year' : 'nullable|string',
            'section' => $user->role === 'student' ? 'required|string|max:255' : 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        try {
            // Update basic information
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->email = $request->email;
            
            if ($user->role === 'student') {
                $user->school_id = $request->school_id;
                $user->course = $request->course;
                $user->year_level = $request->year_level;
                $user->section = $request->section;
            } elseif ($user->role === 'admin') {
                $user->course = $request->course;
            }
            
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Determine upload directory based on user role
                $uploadDir = $user->role === 'student' ? 'uploads/students' : 'uploads/staff';
                $uploadPath = public_path($uploadDir);
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                $image->move($uploadPath, $imageName);
                
                // Delete old image if exists
                if ($user->profile_image && file_exists(public_path($uploadDir . '/' . $user->profile_image))) {
                    unlink(public_path($uploadDir . '/' . $user->profile_image));
                }
                
                $user->profile_image = $imageName;
            }
            
            // Handle password change
            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('message', 'Current password is incorrect.')
                        ->with('message_type', 'danger');
                }
                
                $user->password = Hash::make($request->new_password);
            }
            
            $user->save();
            
            return redirect()->back()
                ->with('message', 'Profile updated successfully!')
                ->with('message_type', 'success');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Error updating profile. Please try again.')
                ->with('message_type', 'danger');
        }
    }

    public function addAdmin(Request $request)
    {
        // Check if current user is main admin
        if (!Auth::user()->canManageAdmins()) {
            return redirect()->back()
                ->with('message', 'Only main admin can manage other admins.')
                ->with('message_type', 'danger');
        }
        
        // Debug: Log the request data
        \Log::info('Add Admin Request Data:', $request->all());
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'admin_full_name' => 'required|string|max:255',
            'admin_username' => 'required|string|max:255|unique:users,username',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_course' => 'required|string|max:255',
            'admin_password' => 'required|string|min:8',
            'admin_password_confirmation' => 'required|same:admin_password',
            'admin_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        try {
            // Create new admin user
            $user = new User();
            $user->full_name = $request->admin_full_name;
            $user->username = $request->admin_username;
            $user->email = $request->admin_email;
            $user->course = $request->admin_course;
            // Ensure Argon2id is used (configured via config/hashing.php)
$user->password = Hash::make($request->admin_password);
            $user->role = 'admin';
            $user->status = 'active';
            // Laravel will automatically set created_at and updated_at timestamps
            
            // Handle profile image upload
            if ($request->hasFile('admin_profile_image') && $request->file('admin_profile_image')->isValid()) {
                $image = $request->file('admin_profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                $uploadPath = public_path('uploads/staff');
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                $image->move($uploadPath, $imageName);
                $user->profile_image = $imageName;
            }
            
            $user->save();
            
            \Log::info('Admin created successfully:', ['id' => $user->id, 'username' => $user->username]);
            
            return redirect()->back()
                ->with('message', 'Admin added successfully!')
                ->with('message_type', 'success');
                
        } catch (\Exception $e) {
            \Log::error('Error adding admin: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('message', 'Error adding admin: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    public function updateAdmin(Request $request)
    {
        // Check if current user is main admin
        if (!Auth::user()->canManageAdmins()) {
            return redirect()->back()
                ->with('message', 'Only main admin can manage other admins.')
                ->with('message_type', 'danger');
        }
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:users,id',
            'admin_full_name' => 'required|string|max:255',
            'admin_username' => 'required|string|max:255|unique:users,username,' . $request->admin_id,
            'admin_email' => 'required|email|max:255|unique:users,email,' . $request->admin_id,
            'admin_course' => 'required|string|max:255',
            'admin_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        try {
            $user = User::findOrFail($request->admin_id);
            
            // Prevent admin from editing themselves through this method
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('message', 'You cannot edit your own admin account through this form.')
                    ->with('message_type', 'warning');
            }
            
            $user->full_name = $request->admin_full_name;
            $user->username = $request->admin_username;
            $user->email = $request->admin_email;
            $user->course = $request->admin_course;
            
            // Handle profile image upload
            if ($request->hasFile('admin_profile_image')) {
                $image = $request->file('admin_profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                $uploadPath = public_path('uploads/staff');
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                $image->move($uploadPath, $imageName);
                
                // Delete old image if exists
                if ($user->profile_image && file_exists(public_path('uploads/staff/' . $user->profile_image))) {
                    unlink(public_path('uploads/staff/' . $user->profile_image));
                }
                
                $user->profile_image = $imageName;
            }
            
            $user->save();
            
            return redirect()->back()
                ->with('message', 'Admin updated successfully!')
                ->with('message_type', 'success');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Error updating admin. Please try again.')
                ->with('message_type', 'danger');
        }
    }

    public function deleteAdmin(Request $request)
    {
        // Check if current user is main admin
        if (!Auth::user()->canManageAdmins()) {
            return redirect()->back()
                ->with('message', 'Only main admin can manage other admins.')
                ->with('message_type', 'danger');
        }
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid admin ID.')
                ->with('message_type', 'danger');
        }
        
        try {
            $user = User::findOrFail($request->admin_id);
            
            // Prevent admin from deleting themselves
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('message', 'You cannot delete your own admin account.')
                    ->with('message_type', 'warning');
            }
            
            // Delete profile image if exists
            if ($user->profile_image && file_exists(public_path('uploads/staff/' . $user->profile_image))) {
                unlink(public_path('uploads/staff/' . $user->profile_image));
            }
            
            $user->delete();
            
            return redirect()->back()
                ->with('message', 'Admin deleted successfully!')
                ->with('message_type', 'success');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting admin. Please try again.')
                ->with('message_type', 'danger');
        }
    }

    public function profileRatingsAjax($id)
    {
        try {
            \Log::info("profileRatingsAjax called with ID: {$id}");
            
            $staff = \App\Models\Staff::find($id);
            if (!$staff) {
                \Log::warning("Staff not found with ID: {$id}");
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }
            
            \Log::info("Staff found: {$staff->full_name} (Type: {$staff->staff_type})");
        
        // Get current academic year (active)
        $currentAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        \Log::info("Current academic year: " . ($currentAcademicYear ? $currentAcademicYear->year : 'None'));
        
        // Get distinct categories (titles) for this staff_type and current academic year
        $categories = \App\Models\Question::where('staff_type', $staff->staff_type)
            ->when($currentAcademicYear, function($q) use ($currentAcademicYear) {
                $q->where('academic_year_id', $currentAcademicYear->id);
            })
            ->select('title')
            ->distinct()
            ->pluck('title');
            
        \Log::info("Categories found: " . $categories->count() . " - " . $categories->implode(', '));
        
        // For each category, calculate the average response_score for this staff
        $averages = [];
        foreach ($categories as $category) {
            // Get all question IDs for this category
            $questionIds = \App\Models\Question::where('staff_type', $staff->staff_type)
                ->where('title', $category)
                ->when($currentAcademicYear, function($q) use ($currentAcademicYear) {
                    $q->where('academic_year_id', $currentAcademicYear->id);
                })
                ->pluck('id');
            
            // Calculate average rating for all questions in this category
            $avg = \App\Models\Evaluation::where('staff_id', $staff->id)
                ->whereIn('question_id', $questionIds)
                ->avg('response_score');
            
            $averages[$category] = $avg ? round($avg, 2) : 0;
        }
        
            \Log::info("Returning successful response for staff {$id}");
            
            return response()->json([
                'success' => true,
                'staff' => [
                    'id' => $staff->id,
                    'full_name' => $staff->full_name,
                    'email' => $staff->email,
                    'department' => $staff->department,
                    'staff_type' => $staff->staff_type,
                    'image_path' => $staff->image_path,
                ],
                'categories' => $categories->map(function($category) {
                    return [
                        'title' => $category,
                    ];
                })->values(),
                'averages' => $averages,
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in profileRatingsAjax for staff ID {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while loading staff data. Please try again.'
            ], 500);
        }
    }

    public function detailedEvaluationsAjax($id)
    {
        try {
            \Log::info("detailedEvaluationsAjax called with ID: {$id}");
            
            $staff = \App\Models\Staff::find($id);
            if (!$staff) {
                \Log::warning("Staff not found with ID: {$id}");
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }
            
            \Log::info("Staff found: {$staff->full_name} (Type: {$staff->staff_type})");
        
            // Get current academic year (active)
            $currentAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();
            \Log::info("Current academic year: " . ($currentAcademicYear ? $currentAcademicYear->year : 'None'));
            
            // Get all questions for this staff_type and current academic year with their evaluations
            $questions = \App\Models\Question::where('staff_type', $staff->staff_type)
                ->when($currentAcademicYear, function($q) use ($currentAcademicYear) {
                    $q->where('academic_year_id', $currentAcademicYear->id);
                })
                ->orderBy('title')
                ->orderBy('description')
                ->get();
                
            \Log::info("Questions found: " . $questions->count());
            
            // For each question, calculate the average response_score for this staff
            $evaluations = [];
            foreach ($questions as $question) {
                $avg = \App\Models\Evaluation::where('staff_id', $staff->id)
                    ->where('question_id', $question->id)
                    ->avg('response_score');
                
                $evaluations[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->description,
                    'category' => $question->title,
                    'average_rating' => $avg ? round($avg, 2) : 0,
                ];
            }
            
            \Log::info("Returning successful response for staff {$id} with " . count($evaluations) . " evaluations");
            
            return response()->json([
                'success' => true,
                'staff' => [
                    'id' => $staff->id,
                    'staff_id' => $staff->staff_id,
                    'full_name' => $staff->full_name,
                    'email' => $staff->email,
                    'department' => $staff->department,
                    'staff_type' => $staff->staff_type,
                    'image_path' => $staff->image_path,
                ],
                'evaluations' => $evaluations,
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in detailedEvaluationsAjax for staff ID {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while loading staff evaluation data. Please try again.'
            ], 500);
        }
    }

    // Subject Management Methods
    public function addSubject(Request $request)
    {
        // Check for duplicate subject code and section combination
        $existingSubject = \App\Models\Subject::where('sub_code', $request->sub_code)
            ->where('section', $request->section)
            ->first();

        if ($existingSubject) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A subject with code "' . $request->sub_code . '" already exists for section "' . $request->section . '". Please use a different section or subject code.',
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors(['sub_code' => 'A subject with code "' . $request->sub_code . '" already exists for section "' . $request->section . '". Please use a different section or subject code.'])
                ->withInput();
        }

        // Validate request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'sub_code' => 'required|string|max:25',
            'sub_name' => 'required|string|max:50',
            'sub_department' => 'required|string',
            'sub_year' => 'required|string',
            'section' => 'required|string|max:10',
            'semester' => 'required|string|in:1,2',
            'assign_instructor' => 'nullable|string',
            'subject_type' => 'required|string|in:Major,Minor,Bridging',
        ], [
            'sub_code.required' => 'Subject code is required.',
            'sub_code.max' => 'Subject code cannot exceed 25 characters.',
            'sub_name.required' => 'Subject name is required.',
            'sub_name.max' => 'Subject name cannot exceed 50 characters.',
            'sub_department.required' => 'Department is required.',
            'sub_year.required' => 'Year is required.',
            'section.required' => 'Section is required.',
            'section.max' => 'Section cannot exceed 10 characters.',
            'semester.required' => 'Semester is required.',
            'semester.in' => 'Semester must be 1 or 2.',
            'subject_type.required' => 'Subject type is required.',
            'subject_type.in' => 'Subject type must be Major, Minor, or Bridging.',
        ]);

        if ($validator->fails()) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                $errors = $validator->errors();
                
                // Get the first error message
                $errorMessage = $errors->first();
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $errors
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get instructor full name if staff_id is provided
            $instructorName = null;
            if ($request->assign_instructor) {
                $staff = Staff::where('staff_id', $request->assign_instructor)->first();
                $instructorName = $staff ? $staff->full_name : null;
            }

            Subject::create([
                'sub_code' => $request->sub_code,
                'sub_name' => $request->sub_name,
                'sub_department' => $request->sub_department,
                'sub_year' => $request->sub_year,
                'section' => $request->section,
                'semester' => $request->semester,
                'assign_instructor' => $instructorName,
                'subject_type' => $request->subject_type,
            ]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject added successfully!'
                ]);
            }

            return redirect()->route('dashboard', ['page' => 'subject-management'])
                ->with('message', 'Subject added successfully!')
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding subject. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('message', 'Error adding subject. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function updateSubject(Request $request)
    {
        // Find the current subject being updated using both sub_code and section
        $query = \App\Models\Subject::where('sub_code', $request->original_subject_code);
        
        // Handle null/empty sections properly
        if (empty($request->original_section)) {
            $query->whereNull('section');
        } else {
            $query->where('section', $request->original_section);
        }
        
        $currentSubject = $query->first();
        
        if (!$currentSubject) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found. It may have been deleted or modified by another user.',
                ], 404);
            }
            
            return redirect()->back()
                ->withErrors(['sub_code' => 'Subject not found. It may have been deleted or modified by another user.'])
                ->withInput();
        }
        
        // Check for duplicate subject code and section combination (excluding the current record)
        $duplicateQuery = \App\Models\Subject::where('sub_code', $request->sub_code)
            ->where('id', '!=', $currentSubject->id);
            
        // Handle null/empty sections properly for duplicate check
        if (empty($request->section)) {
            $duplicateQuery->whereNull('section');
        } else {
            $duplicateQuery->where('section', $request->section);
        }
        
        $existingSubject = $duplicateQuery->first();

        if ($existingSubject) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A subject with code "' . $request->sub_code . '" already exists for section "' . $request->section . '". Please use a different section or subject code.',
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors(['sub_code' => 'A subject with code "' . $request->sub_code . '" already exists for section "' . $request->section . '". Please use a different section or subject code.'])
                ->withInput();
        }

        // Validate request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'original_subject_code' => 'required|string',
            'original_section' => 'nullable|string',
            'sub_code' => 'required|string|max:25',
            'sub_name' => 'required|string|max:50',
            'sub_department' => 'required|string',
            'sub_year' => 'required|string',
            'section' => 'required|string|max:10',
            'semester' => 'required|string|in:1,2',
            'assign_instructor' => 'nullable|string',
            'subject_type' => 'required|string|in:Major,Minor,Bridging',
        ], [
            'original_subject_code.required' => 'Original subject code is required.',
            'sub_code.required' => 'Subject code is required.',
            'sub_code.max' => 'Subject code cannot exceed 25 characters.',
            'sub_name.required' => 'Subject name is required.',
            'sub_name.max' => 'Subject name cannot exceed 50 characters.',
            'sub_department.required' => 'Department is required.',
            'sub_year.required' => 'Year is required.',
            'section.required' => 'Section is required.',
            'section.max' => 'Section cannot exceed 10 characters.',
            'semester.required' => 'Semester is required.',
            'semester.in' => 'Semester must be 1 or 2.',
            'subject_type.required' => 'Subject type is required.',
            'subject_type.in' => 'Subject type must be Major, Minor, or Bridging.',
        ]);

        if ($validator->fails()) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                $errors = $validator->errors();
                
                // Get the first error message
                $errorMessage = $errors->first();
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $errors
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Use the already found currentSubject instead of searching again
            $subject = $currentSubject;
            
            // Get instructor full name if staff_id is provided
            $instructorName = null;
            if ($request->assign_instructor) {
                $staff = Staff::where('staff_id', $request->assign_instructor)->first();
                $instructorName = $staff ? $staff->full_name : null;
            }
            
            $subject->update([
                'sub_code' => $request->sub_code,
                'sub_name' => $request->sub_name,
                'sub_department' => $request->sub_department,
                'sub_year' => $request->sub_year,
                'section' => $request->section,
                'semester' => $request->semester,
                'assign_instructor' => $instructorName,
                'subject_type' => $request->subject_type,
            ]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject updated successfully!'
                ]);
            }

            return redirect()->route('dashboard', ['page' => 'subject-management'])
                ->with('message', 'Subject updated successfully!')
                ->with('message_type', 'success');

        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating subject. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('message', 'Error updating subject. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function deleteSubject(Request $request)
    {
        // Validate request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'subject_code' => 'required|string',
            'section' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid subject data.')
                ->with('message_type', 'danger');
        }

        try {
            // Find subject by both subject_code and section for precise deletion
            $query = Subject::where('sub_code', $request->subject_code);
            
            // Add section condition if provided
            if ($request->section) {
                $query->where('section', $request->section);
            } else {
                $query->whereNull('section');
            }
            
            $subject = $query->firstOrFail();
            $subjectName = $subject->sub_name;
            $subjectSection = $subject->section ?? 'N/A';
            
            $subject->delete();

            return redirect()->route('dashboard', ['page' => 'subject-management'])
                ->with('message', "Subject '{$subjectName}' (Section: {$subjectSection}) deleted successfully!")
                ->with('message_type', 'success');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('message', 'Subject not found.')
                ->with('message_type', 'danger');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting subject. Please try again.')
                ->with('message_type', 'danger');
        }
    }
} 