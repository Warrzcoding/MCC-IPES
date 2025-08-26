<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderByDesc('year')->orderByDesc('semester')->get();
        return view('pages.academicyear', compact('years'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:255',
            'semester' => 'required|integer|in:1,2',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('message', 'Validation failed.')->with('message_type', 'danger');
        }
        
        // Check for unique combination of year and semester
        $exists = AcademicYear::where('year', $request->year)
                             ->where('semester', $request->semester)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Academic Year ' . $request->year . ' with Semester ' . $request->semester . ' already exists!')
                ->with('message_type', 'error');
        }
        
        $year = AcademicYear::create([
            'year' => $request->year,
            'semester' => $request->semester,
            'is_active' => false
        ]);
        return redirect()->back()->with('message', 'Academic year added!')->with('message_type', 'success');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:255',
            'semester' => 'required|integer|in:1,2',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('message', 'Validation failed.')->with('message_type', 'danger');
        }
        
        // Check for unique combination of year and semester, excluding current record
        $exists = AcademicYear::where('year', $request->year)
                             ->where('semester', $request->semester)
                             ->where('id', '!=', $id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Academic Year ' . $request->year . ' with Semester ' . $request->semester . ' already exists!')
                ->with('message_type', 'error');
        }
        
        $year = AcademicYear::findOrFail($id);
        $year->update([
            'year' => $request->year,
            'semester' => $request->semester
        ]);
        return redirect()->back()->with('message', 'Academic year updated!')->with('message_type', 'success');
    }

    public function destroy($id)
    {
        \Log::info('Request method: ' . request()->method());
        $year = AcademicYear::findOrFail($id);
        if ($year->is_active) {
            return redirect()->back()->with('message', 'Cannot delete the active academic year.')->with('message_type', 'danger');
        }
        $year->delete();
        return redirect()->back()->with('message', 'Academic year deleted!')->with('message_type', 'success');
    }

    public function toggleActive($id)
    {
        $year = AcademicYear::findOrFail($id);
        if (!$year->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            $year->is_active = true;
            $year->save();
            return redirect()->back()->with('message', 'Academic year set as active!')->with('message_type', 'success');
        }
        return redirect()->back()->with('message', 'Academic year is already active.')->with('message_type', 'info');
    }

    public function manage($id)
    {
        $year = \App\Models\AcademicYear::findOrFail($id);
        $savedQuestions = \App\Models\SavedQuestion::where('academic_year_id', $id)->get();
        $savedEvaluations = \DB::table('save_eval_result')->where('academic_year_id', $id)->get();

        // Group evaluations by staff_id and calculate stats
        $staffEvaluations = \DB::table('save_eval_result')
            ->select(
                'staff_id',
                \DB::raw('AVG(response_score) as average_rating'),
                \DB::raw('COUNT(*) as total_evaluations')
            )
            ->where('academic_year_id', $id)
            ->groupBy('staff_id')
            ->get()
            ->map(function($row) {
                $staff = \App\Models\Staff::where('staff_id', $row->staff_id)->first();
                $total_comments = \DB::table('save_eval_result')
                    ->where('staff_id', $row->staff_id)
                    ->whereNotNull('comments')
                    ->where('comments', '!=', '')
                    ->count();
                return (object)[
                    'id' => $staff ? $staff->id : null, // <-- numeric id
                    'full_name' => $staff ? $staff->full_name : $row->staff_id,
                    'staff_id' => $row->staff_id,
                    'email' => $staff ? $staff->email : '',
                    'department' => $staff ? $staff->department : '',
                    'staff_type' => $staff ? $staff->staff_type : '',
                    'image_path' => $staff ? $staff->image_path : '',
                    'average_rating' => round($row->average_rating, 2),
                    'total_evaluations' => $row->total_evaluations,
                    'total_comments' => $total_comments,
                ];
            });

        // Set up for dashboard subpage
        $page = 'manage_academic_year';
        $current_title = 'Manage Academic Year: ' . $year->year . ' - Semester ' . ($year->semester ?? 1);

        return view('dashboard', compact('page', 'current_title', 'year', 'savedQuestions', 'savedEvaluations', 'staffEvaluations'));
    }

    public function getStaffCommentsForYear(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        try {
            // Get the staff record to get the staff_id (string)
            $staff = \App\Models\Staff::find($request->staff_id);
            if (!$staff) {
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }
            
            // Get unique comments per user using raw SQL to avoid duplicates
            $comments = \DB::table('save_eval_result as e')
                ->select(
                    'e.id',
                    'e.comments',
                    'e.created_at',
                    'e.user_id',
                    'u.full_name as user_name'
                )
                ->join('users as u', 'e.user_id', '=', 'u.id')
                ->where('e.staff_id', $staff->id)  // Use numeric staff ID
                ->where('e.academic_year_id', $request->academic_year_id)
                ->whereNotNull('e.comments')
                ->where('e.comments', '!=', '')
                ->whereRaw('e.id = (
                    SELECT MIN(e2.id) 
                    FROM save_eval_result e2 
                    WHERE e2.staff_id = e.staff_id 
                    AND e2.user_id = e.user_id 
                    AND e2.academic_year_id = e.academic_year_id
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

    public function profileRatingsForYearAjax($staffId, $academicYearId)
    {
        $staff = \App\Models\Staff::find($staffId);
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found.']);
        }



        // Get categories from saved_questions
        $categories = \App\Models\SavedQuestion::where('staff_type', $staff->staff_type)
            ->where('academic_year_id', $academicYearId)
            ->select('title')
            ->distinct()
            ->pluck('title');

        $averages = [];
        foreach ($categories as $category) {
            // Find all saved_questions for this academic year, staff type, and category title
            $savedQuestionIds = \App\Models\SavedQuestion::where('staff_type', $staff->staff_type)
                ->where('title', $category)
                ->where('academic_year_id', $academicYearId)
                ->pluck('id');



            $avg = \DB::table('save_eval_result')
                ->where('staff_id', $staff->id)  // Use numeric staff ID
                ->whereIn('question_id', $savedQuestionIds)
                ->where('academic_year_id', $academicYearId)
                ->avg('response_score');

            $averages[$category] = $avg ? round($avg, 2) : 0;
        }

        // Calculate overall average (same as in staffEvaluations for the table)
        $overall_average = \DB::table('save_eval_result')
            ->where('staff_id', $staff->id)  // Use numeric staff ID
            ->where('academic_year_id', $academicYearId)
            ->avg('response_score');
        $overall_average = $overall_average ? round($overall_average, 2) : 0;

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
            'overall_average' => $overall_average,
        ]);
    }

    public function detailedEvaluationsForYearAjax($staffId, $academicYearId)
    {
        try {
            \Log::info("detailedEvaluationsForYearAjax called with Staff ID: {$staffId}, Academic Year ID: {$academicYearId}");
            
            $staff = \App\Models\Staff::find($staffId);
            if (!$staff) {
                \Log::warning("Staff not found with ID: {$staffId}");
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }
            
            $academicYear = \App\Models\AcademicYear::find($academicYearId);
            if (!$academicYear) {
                \Log::warning("Academic year not found with ID: {$academicYearId}");
                return response()->json(['success' => false, 'message' => 'Academic year not found.']);
            }
            
            \Log::info("Staff found: {$staff->full_name} (ID: {$staff->id}, Staff_ID: {$staff->staff_id}, Type: {$staff->staff_type})");
            \Log::info("Academic year: {$academicYear->year}");
        
            // Get all saved questions for this staff_type and academic year with their evaluations
            $savedQuestions = \App\Models\SavedQuestion::where('staff_type', $staff->staff_type)
                ->where('academic_year_id', $academicYearId)
                ->orderBy('title')
                ->orderBy('description')
                ->get();
                
            \Log::info("Saved questions found: " . $savedQuestions->count());
            
            // For each saved question, calculate the average response_score for this staff from save_eval_result
            $evaluations = [];
            foreach ($savedQuestions as $question) {
                $avg = \DB::table('save_eval_result')
                    ->where('staff_id', $staff->id)  // Use numeric staff ID
                    ->where('question_id', $question->id)
                    ->where('academic_year_id', $academicYearId)
                    ->avg('response_score');
                
                $evaluations[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->description,
                    'category' => $question->title,
                    'average_rating' => $avg ? round($avg, 2) : 0,
                ];
            }
            
            \Log::info("Returning successful response for staff {$staffId} with " . count($evaluations) . " evaluations");
            
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
            \Log::error("Error in detailedEvaluationsForYearAjax for staff ID {$staffId}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while loading staff evaluation data. Please try again.'
            ], 500);
        }
    }
} 