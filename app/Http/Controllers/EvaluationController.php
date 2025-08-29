<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Question;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\SavedQuestion;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function showForm()
    {
        // Get the active academic year (where is_active = 1)
        $currentAcademicYear = AcademicYear::where('is_active', 1)->first();
        $isOpen = false;
        $teachingQuestions = collect();
        $nonTeachingQuestions = collect();
        $teachingEvaluated = 0;
        $nonTeachingEvaluated = 0;
        $teachingEvaluatedStaff = collect();
        $nonTeachingEvaluatedStaff = collect();
        $totalEvaluated = 0;

        if ($currentAcademicYear) {
            // Get all questions for the current active academic year that are open
            $openQuestions = Question::where('academic_year_id', $currentAcademicYear->id)
                ->where('is_open', 1)
                ->get();

            $isOpen = $openQuestions->count() > 0;

            // Separate by staff type
            $teachingQuestions = $openQuestions->where('staff_type', 'teaching');
            $nonTeachingQuestions = $openQuestions->where('staff_type', 'non-teaching');

            // BADGE COUNTS: count unique staff evaluated by user for each type (native PHP logic)
            $userId = auth()->id();
            $evalCounts = DB::table('evaluations as e')
                ->join('staff as s', 'e.staff_id', '=', 's.id')
                ->select('s.staff_type', DB::raw('COUNT(DISTINCT e.staff_id) as count'))
                ->where('e.user_id', $userId)
                ->groupBy('s.staff_type')
                ->get();

            $teachingEvaluated = \App\Models\Evaluation::where('user_id', auth()->id())
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->distinct('staff_id')
                ->count('staff_id');

            $nonTeachingEvaluated = \App\Models\Evaluation::where('user_id', auth()->id())
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->distinct('staff_id')
                ->count('staff_id');

            // For modal: get unique staff objects evaluated by user for each type
            $teachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();
            $nonTeachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();

            // Set totalEvaluated to the count of distinct staff_id for the current user
            $totalEvaluated = \App\Models\Evaluation::where('user_id', auth()->id())
                ->distinct('staff_id')
                ->count('staff_id');
        }

        // Filter teaching staff based on student's course, year level, and section from subjects table
        $user = auth()->user();
        $studentCourse = $user->course;
        $studentYearLevel = $user->year_level;
        $studentSection = $user->section;
        
        // Get teaching staff directly from subjects table with proper filtering
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
            
        // Get non-teaching staff (no semester filter; always include all non-teaching every semester)
        $nonTeachingStaff = \App\Models\Staff::where('staff_type', 'non-teaching')->get();

        // DEBUG: Log the filtering results (remove this after testing)
        \Log::info('Student Evaluation Filter Debug', [
            'student_course' => $studentCourse,
            'student_year' => $studentYearLevel, 
            'student_section' => $studentSection,
            'instructor_names_from_subjects' => $instructorNames,
            'teaching_staff_count' => $teachingStaff->count(),
            'teaching_staff_names' => $teachingStaff->pluck('full_name')->toArray(),
            'non_teaching_staff_count' => $nonTeachingStaff->count(),
            'non_teaching_staff_names' => $nonTeachingStaff->pluck('full_name')->toArray()
        ]);

        $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
        $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();

        // Remove debug statement - questions are now properly fetched based on active academic year

        return view('pages.evaluates', compact(
            'isOpen',
            'teachingQuestions',
            'nonTeachingQuestions',
            'teachingStaff',
            'nonTeachingStaff',
            'teachingEvaluated',
            'nonTeachingEvaluated',
            'teachingEvaluatedStaff',
            'nonTeachingEvaluatedStaff',
            'totalEvaluated',
            'currentAcademicYear'
        ));
    }

    public function showEvaluationForm()
    {
        // Get the active academic year (where is_active = 1)
        $currentAcademicYear = AcademicYear::where('is_active', 1)->first();
        $isOpen = false;
        $teachingQuestions = collect();
        $nonTeachingQuestions = collect();
        $teachingEvaluated = 0;
        $nonTeachingEvaluated = 0;
        $teachingEvaluatedStaff = collect();
        $nonTeachingEvaluatedStaff = collect();
        $totalEvaluated = 0;

        if ($currentAcademicYear) {
            // Get questions that belong to the active academic year and are open
            $openQuestions = Question::where('academic_year_id', $currentAcademicYear->id)
                ->where('is_open', 1)
                ->get();
            $isOpen = $openQuestions->count() > 0;
            $teachingQuestions = $openQuestions->where('staff_type', 'teaching');
            $nonTeachingQuestions = $openQuestions->where('staff_type', 'non-teaching');

            $userId = auth()->id();
            $teachingEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->select('staff_id')
                ->distinct()
                ->count();
            $nonTeachingEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->select('staff_id')
                ->distinct()
                ->count();

            $teachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();
            $nonTeachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();
            $totalEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->distinct('staff_id')
                ->count('staff_id');
        }

        // Filter teaching staff based on student's course, year level, and section from subjects table
        $user = auth()->user();
        $studentCourse = $user->course;
        $studentYearLevel = $user->year_level;
        $studentSection = $user->section;
        
        // Get teaching staff directly from subjects table with proper filtering
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
            
        // Get non-teaching staff (no semester filter; always include all non-teaching every semester)
        $nonTeachingStaff = Staff::where('staff_type', 'non-teaching')->get();

        // DEBUG: Log the filtering results (remove this after testing)
        \Log::info('Student Evaluation Filter Debug (showEvaluationForm)', [
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

        $teachingEvaluatedStaff = $teachingEvaluatedStaff ?? collect();
        $nonTeachingEvaluatedStaff = $nonTeachingEvaluatedStaff ?? collect();

        return view('pages.evaluates', compact(
            'isOpen',
            'teachingQuestions',
            'nonTeachingQuestions',
            'teachingStaff',
            'nonTeachingStaff',
            'teachingEvaluated',
            'nonTeachingEvaluated',
            'teachingEvaluatedStaff',
            'nonTeachingEvaluatedStaff',
            'totalEvaluated',
            'currentAcademicYear'
        ));
    }

    public function submit(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'responses' => 'required|array',
        ]);

        $userId = auth()->id();

        // Get the current active academic year
        $activeAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
        $academicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

        // Prevent duplicate evaluation for the same staff by the same user
        $alreadyEvaluated = \App\Models\Evaluation::where('staff_id', $request->staff_id)
            ->where('user_id', $userId)
            ->exists();
        if ($alreadyEvaluated) {
            return redirect()->back()->with([
                'message' => 'You have already evaluated this staff member.',
                'message_type' => 'danger'
            ]);
        }

        foreach ($request->responses as $questionId => $responseValue) {
            $question = \App\Models\Question::find($questionId);
            $score = null;
            if ($question) {
                $score = \App\Models\ResponseOption::where('response_type', $question->response_type)
                    ->where('option_value', $responseValue)
                    ->value('option_order');
            }
            \App\Models\Evaluation::create([
                'staff_id' => $request->staff_id,
                'question_id' => $questionId,
                'response' => $responseValue,
                'response_score' => $score,
                'user_id' => $userId,
                'comments' => $request->comments,
                'academic_year_id' => $academicYearId,
                'created_at' => now(),
            ]);
        }

        // Log for debugging
        \Log::info('Evaluation submitted successfully', [
            'user_id' => $userId,
            'staff_id' => $request->staff_id,
            'responses_count' => count($request->responses)
        ]);

        return redirect()->back()->with([
            'message' => 'Evaluation submitted successfully!',
            'message_type' => 'success'
        ]);
    }

    public function getStaffComments(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id'
        ]);

        try {
            // Get unique comments per user using raw SQL to avoid duplicates
            $comments = \DB::table('evaluations as e')
                ->select(
                    'e.id',
                    'e.comments',
                    'e.created_at',
                    'e.user_id',
                    'u.full_name as user_name'
                )
                ->join('users as u', 'e.user_id', '=', 'u.id')
                ->where('e.staff_id', $request->staff_id)
                ->whereNotNull('e.comments')
                ->where('e.comments', '!=', '')
                ->whereRaw('e.id = (
                    SELECT MIN(e2.id) 
                    FROM evaluations e2 
                    WHERE e2.staff_id = e.staff_id 
                    AND e2.user_id = e.user_id 
                    AND e2.comments IS NOT NULL 
                    AND e2.comments != ""
                )')
                ->orderBy('e.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading comments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkEvaluationsExist()
    {
        try {
            // Check if there are any evaluations in the evaluations table
            $evaluationCount = DB::table('evaluations')->count();
            
            // Log for debugging
            \Log::info('Checking evaluations exist', [
                'count' => $evaluationCount,
                'hasEvaluations' => $evaluationCount > 0
            ]);
            
            return response()->json([
                'hasEvaluations' => $evaluationCount > 0,
                'count' => $evaluationCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking evaluations exist', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'hasEvaluations' => false,
                'count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkQuestionsEmpty()
    {
        try {
            // Check if there are any questions in the questions table
            $questionCount = DB::table('questions')->count();
            
            // Log for debugging
            \Log::info('Checking questions empty', [
                'count' => $questionCount,
                'empty' => $questionCount === 0
            ]);
            
            return response()->json([
                'empty' => $questionCount === 0,
                'count' => $questionCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking questions empty', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'empty' => true,
                'count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAndClearAllResults(Request $request)
    {
        DB::beginTransaction();
        try {
            // Move evaluations
            $evaluations = DB::table('evaluations')->get();
            foreach ($evaluations as $eval) {
                DB::table('save_eval_result')->insert([
                    'staff_id' => $eval->staff_id,
                    'question_id' => $eval->question_id, // Use the original question_id directly
                    'response' => $eval->response,
                    'user_id' => $eval->user_id,
                    'academic_year_id' => $eval->academic_year_id ?? null,
                    'comments' => $eval->comments,
                    'response_score' => $eval->response_score,
                    'created_at' => $eval->created_at ?? now(),
                    'updated_at' => $eval->updated_at ?? now(),
                ]);
            }
            // Only clear evaluations table
            DB::table('evaluations')->delete();

            // Set current academic year as used and inactive
            $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
            if ($currentAcademicYear) {
                $currentAcademicYear->used = 1;
                $currentAcademicYear->is_active = 0;
                $currentAcademicYear->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'All results saved, academic year closed, and original tables cleared.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 