<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Question;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\SavedQuestion;
use App\Models\InstructorSelection;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function showForm()
    {
        return $this->renderEvaluationView();
    }

    public function showEvaluationForm()
    {
        return $this->renderEvaluationView();
    }

    private function renderEvaluationView()
    {
        // Get the active academic year (where is_active = 1)
        $userId = auth()->id();
        $user = auth()->user();

        // Get the active academic year (where is_active = 1)
        $currentAcademicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
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
            
            // Count unique staff evaluated by user for each type
            $teachingEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->distinct('staff_id')
                ->count('staff_id');

            $nonTeachingEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->distinct('staff_id')
                ->count('staff_id');

            // For modal: get unique staff objects evaluated by user for each type
            $teachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();
                
            $nonTeachingEvaluatedStaff = \App\Models\Evaluation::where('user_id', $userId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->whereHas('staff', function($q) { $q->where('staff_type', 'non-teaching'); })
                ->with('staff')
                ->get()
                ->pluck('staff')
                ->unique('id')
                ->values();

            $totalEvaluated = \App\Models\Evaluation::where('user_id', $userId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->distinct('staff_id')
                ->count('staff_id');
        }

        // Filter teaching staff based on student's course, year level, and section from subjects table
        $studentCourse = $user->course;
        $studentYearLevel = $user->year_level;
        $studentSection = $user->section;
        
        $activeSemester = $currentAcademicYear ? (string) $currentAcademicYear->semester : null;
        
        // Get teaching staff from subjects table with proper filtering
        $studentSubjects = \App\Models\Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($studentCourse))])
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
            ->select(\DB::raw('DISTINCT TRIM(assign_instructor) as assign_instructor'), \DB::raw('TRIM(sub_name) as sub_name'))
            ->get();
            
        $instructorNames = $studentSubjects->pluck('assign_instructor')->unique()->toArray();
        
        $teachingStaff = \App\Models\Staff::whereIn('full_name', $instructorNames)
            ->where('staff_type', 'teaching')
            ->get();
            
        $nonTeachingStaff = \App\Models\Staff::where('staff_type', 'non-teaching')->get();

        // Get saved instructor selections for this user and academic year
        $savedSelections = InstructorSelection::where('user_id', $userId)
            ->where('academic_year_id', $currentAcademicYear->id ?? null)
            ->with('staff')
            ->get();

        // Get locked selections specifically (ensure array format)
        $lockedSelectionsData = InstructorSelection::getLockedSelectionByType($userId, $currentAcademicYear->id ?? null);
        $lockedSelections = [
            'teaching' => $lockedSelectionsData['teaching'] ?? collect(),
            'non-teaching' => $lockedSelectionsData['non-teaching'] ?? collect(),
        ];
        $hasLockedSelection = InstructorSelection::hasLockedSelection($userId, $currentAcademicYear->id ?? null);
        
        // Determine which view to return based on user status and locked selection
        $viewName = 'pages.evaluates';
        $studentStatus = strtolower(trim($user->student_status ?? ''));
        $isIrregular = ($studentStatus === 'irregular');
        
        if ($isIrregular) {
            $viewName = $hasLockedSelection ? 'pages.lockedcards' : 'pages.irevaluates';
        }

        return view($viewName, compact(
            'isOpen',
            'teachingQuestions',
            'nonTeachingQuestions',
            'teachingStaff',
            'nonTeachingStaff',
            'studentSubjects',
            'teachingEvaluated',
            'nonTeachingEvaluated',
            'teachingEvaluatedStaff',
            'nonTeachingEvaluatedStaff',
            'totalEvaluated',
            'currentAcademicYear',
            'savedSelections',
            'lockedSelections',
            'hasLockedSelection',
            'isIrregular'
        ));
    }

    public function submit(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'staff_id' => 'required', // Can be single ID or comma-separated IDs or array
            'responses' => 'required|array',
        ]);

        $userId = auth()->id();
        $staffIds = is_array($request->staff_id) ? $request->staff_id : explode(',', $request->staff_id);

        // Get the current active academic year
        $activeAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
        $academicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

        if (!$academicYearId) {
            return redirect()->back()->with([
                'message' => 'No active academic year found.',
                'message_type' => 'danger'
            ]);
        }

        // Questions count for validation
        $teachingQuestionsCount = \App\Models\Question::where('academic_year_id', $academicYearId)->where('staff_type', 'teaching')->where('is_open', 1)->count();
        $nonTeachingQuestionsCount = \App\Models\Question::where('academic_year_id', $academicYearId)->where('staff_type', 'non-teaching')->where('is_open', 1)->count();

        DB::beginTransaction();
        try {
            foreach ($staffIds as $staffId) {
                if (empty($staffId)) continue;

                $staff = \App\Models\Staff::find($staffId);
                if (!$staff) continue;

                // Prevent duplicate evaluation for the same staff by the same user in the same academic year
                $alreadyEvaluated = \App\Models\Evaluation::where('staff_id', $staffId)
                    ->where('user_id', $userId)
                    ->where('academic_year_id', $academicYearId)
                    ->exists();
                
                if ($alreadyEvaluated) {
                    continue; // Skip if already evaluated
                }

                // Support both flat responses [q_id => val] and nested responses [staff_id => [q_id => val]]
                $staffResponses = $request->responses;
                if (isset($staffResponses[$staffId]) && is_array($staffResponses[$staffId])) {
                    $staffResponses = $staffResponses[$staffId];
                }

                // Skip if no responses for this staff member (e.g. in bulk mode if one was skipped)
                if (!is_array($staffResponses) || empty($staffResponses)) {
                    continue;
                }

                // Validation: Ensure all questions are answered for this staff member
                $requiredCount = ($staff->staff_type === 'teaching') ? $teachingQuestionsCount : $nonTeachingQuestionsCount;
                if (count($staffResponses) < $requiredCount) {
                    throw new \Exception("Incomplete evaluation for staff: {$staff->full_name}");
                }

                // Support individual comments per staff if provided
                $comments = $request->comments;
                if (is_array($comments) && isset($comments[$staffId])) {
                    $comments = $comments[$staffId];
                } elseif (is_string($comments)) {
                    // Fallback for single submission mode
                } else {
                    $comments = null;
                }

                foreach ($staffResponses as $questionId => $responseValue) {
                    if (empty($responseValue)) continue;

                    $question = \App\Models\Question::find($questionId);
                    $score = null;
                    if ($question) {
                        $score = \App\Models\ResponseOption::where('response_type', $question->response_type)
                            ->where('option_value', $responseValue)
                            ->value('option_order');
                    }
                    
                    \App\Models\Evaluation::create([
                        'staff_id' => $staffId,
                        'question_id' => $questionId,
                        'response' => $responseValue,
                        'response_score' => $score,
                        'user_id' => $userId,
                        'comments' => $comments,
                        'academic_year_id' => $academicYearId,
                        'created_at' => now(),
                    ]);
                }

                \Log::info('Evaluation submitted successfully', [
                    'user_id' => $userId,
                    'staff_id' => $staffId,
                    'academic_year_id' => $academicYearId
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Evaluation submission failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with([
                'message' => 'Error: ' . $e->getMessage(),
                'message_type' => 'danger'
            ]);
        }

        return redirect()->back()->with([
            'message' => 'Evaluation(s) submitted successfully!',
            'message_type' => 'success'
        ]);
    }

    /**
     * AJAX endpoint to confirm and lock instructor selection
     */
    public function confirmSelection(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'required|integer',
            'staff_type' => 'required|in:teaching,non-teaching',
        ]);

        try {
            $userId = auth()->id();
            $activeAcademicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
            $academicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

            if (!$academicYearId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active academic year found.'
                ], 400);
            }

            // Save selections to database and mark as locked
            InstructorSelection::saveSelection(
                $userId,
                $academicYearId,
                $request->staff_ids,
                $request->staff_type,
                true  // is_locked = true
            );

            return response()->json([
                'success' => true,
                'message' => 'Selection confirmed and locked successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Selection confirmation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint to unlock selection for editing
     */
    public function unlockSelection(Request $request)
    {
        try {
            $userId = auth()->id();
            $activeAcademicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
            $academicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

            if (!$academicYearId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active academic year found.'
                ], 400);
            }

            // Clear selections to allow re-selection
            InstructorSelection::clearSelection($userId, $academicYearId);

            return response()->json([
                'success' => true,
                'message' => 'Selection unlocked for editing!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Selection unlock failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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
            // Clear evaluations and instructor selections tables
            DB::table('evaluations')->delete();
            DB::table('instructor_selections')->delete();

            // Set current academic year as used and inactive
            $currentAcademicYear = \App\Models\AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();
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

    public function saveInstructorSelection(Request $request)
    {
        $request->validate([
            'staff_id' => 'required',
            'staff_type' => 'required|in:teaching,non-teaching',
            'action' => 'required|in:select,deselect'
        ]);

        $userId = auth()->id();
        $academicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();

        if (!$academicYear) {
            return response()->json(['success' => false, 'message' => 'No active academic year found.'], 404);
        }

        try {
            if ($request->action === 'select') {
                InstructorSelection::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'staff_id' => $request->staff_id,
                        'academic_year_id' => $academicYear->id
                    ],
                    [
                        'staff_type' => $request->staff_type,
                        // Maintain stage if updating existing
                    ]
                );
            } else {
                InstructorSelection::where([
                    'user_id' => $userId,
                    'staff_id' => $request->staff_id,
                    'academic_year_id' => $academicYear->id
                ])->delete();
            }

            // Update selection count for all selections of this user/AY
            $count = InstructorSelection::where([
                'user_id' => $userId,
                'academic_year_id' => $academicYear->id,
                'staff_type' => $request->staff_type
            ])->count();

            InstructorSelection::where([
                'user_id' => $userId,
                'academic_year_id' => $academicYear->id,
                'staff_type' => $request->staff_type
            ])->update(['selection_count' => $count]);

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSelectionStage(Request $request)
    {
        $request->validate([
            'staff_type' => 'required|in:teaching,non-teaching',
            'stage' => 'required|in:selection,review,locked'
        ]);

        $userId = auth()->id();
        $academicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();

        if (!$academicYear) {
            return response()->json(['success' => false, 'message' => 'No active academic year found.'], 404);
        }

        try {
            InstructorSelection::where([
                'user_id' => $userId,
                'academic_year_id' => $academicYear->id,
                'staff_type' => $request->staff_type
            ])->update(['selection_stage' => $request->stage]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clearInstructorSelections(Request $request)
    {
        $request->validate([
            'staff_type' => 'required|in:teaching,non-teaching'
        ]);

        $userId = auth()->id();
        $academicYear = AcademicYear::where('is_active', 1)->orderBy('id', 'desc')->first();

        if (!$academicYear) {
            return response()->json(['success' => false, 'message' => 'No active academic year found.'], 404);
        }

        try {
            InstructorSelection::where([
                'user_id' => $userId,
                'academic_year_id' => $academicYear->id,
                'staff_type' => $request->staff_type
            ])->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 