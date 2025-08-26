<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderByDesc('year')->get();
        return view('pages.academicyear', compact('years'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:255|unique:academic_years,year',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('message', 'Validation failed.')->with('message_type', 'danger');
        }
        $year = AcademicYear::create([
            'year' => $request->year,
            'is_active' => false
        ]);
        return redirect()->back()->with('message', 'Academic year added!')->with('message_type', 'success');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:255|unique:academic_years,year,' . $id,
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('message', 'Validation failed.')->with('message_type', 'danger');
        }
        $year = AcademicYear::findOrFail($id);
        $year->update(['year' => $request->year]);
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
        $current_title = 'Manage Academic Year: ' . $year->year;

        return view('dashboard', compact('page', 'current_title', 'year', 'savedQuestions', 'savedEvaluations', 'staffEvaluations'));
    }

    public function getStaffCommentsForYear(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $comments = \DB::table('save_eval_result as e')
            ->select('e.id', 'e.comments', 'e.created_at', 'e.user_id', 'u.full_name as user_name')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->where('e.staff_id', $request->staff_id)
            ->where('e.academic_year_id', $request->academic_year_id)
            ->whereNotNull('e.comments')
            ->where('e.comments', '!=', '')
            ->orderBy('e.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    public function profileRatingsForYearAjax($staffId, $academicYearId)
    {
        $staff = \App\Models\Staff::find($staffId);
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found.']);
        }

        $categories = \App\Models\Question::where('staff_type', $staff->staff_type)
            ->where('academic_year_id', $academicYearId)
            ->select('title')
            ->distinct()
            ->pluck('title');

        $averages = [];
        foreach ($categories as $category) {
            $questionIds = \App\Models\Question::where('staff_type', $staff->staff_type)
                ->where('title', $category)
                ->where('academic_year_id', $academicYearId)
                ->pluck('id');

            $avg = \DB::table('save_eval_result')
                ->where('staff_id', $staff->id)
                ->whereIn('question_id', $questionIds)
                ->where('academic_year_id', $academicYearId)
                ->avg('response_score');

            $averages[$category] = $avg ? round($avg, 2) : 0;
        }

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
    }
} 