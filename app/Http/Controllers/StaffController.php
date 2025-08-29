<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderBy('full_name')->get();
        return view('pages.add-staff', compact('staff'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|max:255|unique:staff,staff_id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'staff_type' => 'required|in:teaching,non-teaching',
            'staff_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }

        // Handle image upload
        $image_path = null;
        if ($request->hasFile('staff_image')) {
            $upload_dir = 'uploads/staff/';
            $upload_path = public_path($upload_dir);
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            $file = $request->file('staff_image');
            $file_extension = strtolower($file->getClientOriginalExtension());
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = $request->staff_id . '_' . time() . '.' . $file_extension;
                $full_upload_path = $upload_path . $new_filename;
                
                if (move_uploaded_file($file->getPathname(), $full_upload_path)) {
                    $image_path = $upload_dir . $new_filename;
                }
            }
        }

        try {
            Staff::create([
                'staff_id' => $request->staff_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'staff_type' => $request->staff_type,
                'image_path' => $image_path
            ]);

            return redirect()->back()
                ->with('message', 'Staff added successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error adding staff. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $request->original_staff_id . ',staff_id',
            'phone' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'staff_type' => 'required|in:teaching,non-teaching',
            'staff_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }

        $staff = Staff::where('staff_id', $request->original_staff_id)->first();
        
        if (!$staff) {
            return redirect()->back()
                ->with('message', 'Staff not found.')
                ->with('message_type', 'danger');
        }

        // Handle image upload for update
        $image_path = $staff->image_path;
        $update_image = false;
        
        if ($request->hasFile('staff_image')) {
            $upload_dir = 'uploads/staff/';
            $upload_path = public_path($upload_dir);
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            $file = $request->file('staff_image');
            $file_extension = strtolower($file->getClientOriginalExtension());
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = $request->staff_id . '_' . time() . '.' . $file_extension;
                $full_upload_path = $upload_path . $new_filename;
                
                if (move_uploaded_file($file->getPathname(), $full_upload_path)) {
                    // Delete old image if exists
                    if ($staff->image_path && file_exists(public_path($staff->image_path))) {
                        unlink(public_path($staff->image_path));
                    }
                    $image_path = $upload_dir . $new_filename;
                    $update_image = true;
                }
            }
        }

        try {
            $updateData = [
                'staff_id' => $request->staff_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'staff_type' => $request->staff_type
            ];

            if ($update_image) {
                $updateData['image_path'] = $image_path;
            }

            $staff->update($updateData);

            return redirect()->back()
                ->with('message', 'Staff updated successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error updating staff. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $staff = Staff::where('staff_id', $request->staff_id)->first();
        
        if (!$staff) {
            return redirect()->back()
                ->with('message', 'Staff not found.')
                ->with('message_type', 'danger');
        }

        try {
            // Delete image file if exists
            if ($staff->image_path && file_exists(public_path($staff->image_path))) {
                unlink(public_path($staff->image_path));
            }
            
            $staff->delete();

            return redirect()->back()
                ->with('message', 'Staff deleted successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting staff. Please try again.')
                ->with('message_type', 'danger');
        }
    }

    /**
     * Get staff comments for general staff ratings page (uses evaluations table)
     */
    public function getStaffComments(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|integer|exists:staff,id'
        ]);

        try {
            // Get the staff record
            $staff = Staff::find($request->staff_id);
            if (!$staff) {
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }
            
            // Get unique comments per user from evaluations table
            $comments = DB::table('evaluations as e')
                ->select(
                    'e.id',
                    'e.comments',
                    'e.created_at',
                    'e.user_id',
                    'u.full_name as user_name'
                )
                ->join('users as u', 'e.user_id', '=', 'u.id')
                ->where('e.staff_id', $staff->id)
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

    /**
     * Get staff profile and ratings for general staff ratings page (uses evaluations table)
     */
    public function getStaffProfileRatings($staffId)
    {
        try {
            $staff = Staff::find($staffId);
            if (!$staff) {
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }

            // Get categories from questions table (current evaluation questions)
            $categories = DB::table('questions')
                ->where('staff_type', $staff->staff_type)
                ->select('title')
                ->distinct()
                ->pluck('title');

            $averages = [];
            foreach ($categories as $category) {
                // Find all questions for this staff type and category title
                $questionIds = DB::table('questions')
                    ->where('staff_type', $staff->staff_type)
                    ->where('title', $category)
                    ->pluck('id');

                $avg = DB::table('evaluations')
                    ->where('staff_id', $staff->id)
                    ->whereIn('question_id', $questionIds)
                    ->avg('response_score');

                $averages[$category] = $avg ? round($avg, 2) : 0;
            }

            // Calculate overall average
            $overall_average = DB::table('evaluations')
                ->where('staff_id', $staff->id)
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get staff detailed evaluations for general staff ratings page (uses evaluations table)
     */
    public function getStaffDetailedEvaluations($staffId)
    {
        try {
            $staff = Staff::find($staffId);
            if (!$staff) {
                return response()->json(['success' => false, 'message' => 'Staff not found.']);
            }

            // Get all questions for this staff_type with their evaluations
            $questions = DB::table('questions')
                ->where('staff_type', $staff->staff_type)
                ->orderBy('title')
                ->orderBy('description')
                ->get();

            // For each question, calculate the average response_score for this staff from evaluations
            $evaluations = [];
            foreach ($questions as $question) {
                $avg = DB::table('evaluations')
                    ->where('staff_id', $staff->id)
                    ->where('question_id', $question->id)
                    ->avg('response_score');

                $evaluations[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->description,
                    'category' => $question->title,
                    'average_rating' => $avg ? round($avg, 2) : 0,
                ];
            }

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
            return response()->json([
                'success' => false,
                'message' => 'Error loading detailed evaluations: ' . $e->getMessage()
            ], 500);
        }
    }
} 