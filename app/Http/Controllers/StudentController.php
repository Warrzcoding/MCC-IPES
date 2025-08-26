<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use App\Models\AcademicYear;
use App\Models\Question;
use App\Models\RequestSignin;
use App\Models\Subject;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
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
                    $academicYearQuestions = Question::where('academic_year_id', $currentAcademicYear->id)->pluck('id');
                    
                    if ($academicYearQuestions->count() > 0) {
                        // Get instructor names for student's specific course, year level, and section
                        $instructorNames = Subject::whereRaw('LOWER(TRIM(sub_department)) = ?', [strtolower(trim($student->course))])
                            ->whereRaw('LOWER(TRIM(sub_year)) = ?', [strtolower(trim($student->year_level))])
                            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($student->section))])
                            ->whereNotNull('assign_instructor')
                            ->where('assign_instructor', '!=', '')
                            ->distinct('assign_instructor')
                            ->pluck('assign_instructor');
                        
                        // Get teaching staff IDs that match the instructor names
                        $teachingStaffIds = Staff::whereIn('full_name', $instructorNames)
                            ->where('staff_type', 'teaching')
                            ->pluck('id');
                        
                        // Get all non-teaching staff IDs
                        $nonTeachingStaffIds = Staff::where('staff_type', 'non-teaching')->pluck('id');
                        
                        // Count evaluated instructors by this student
                        $evaluatedInstructorsCount = Evaluation::where('user_id', $student->id)
                            ->whereIn('question_id', $academicYearQuestions)
                            ->whereIn('staff_id', $teachingStaffIds)
                            ->distinct('staff_id')
                            ->count('staff_id');
                        
                        // Count evaluated non-teaching staff by this student
                        $evaluatedNonTeachingCount = Evaluation::where('user_id', $student->id)
                            ->whereIn('question_id', $academicYearQuestions)
                            ->whereIn('staff_id', $nonTeachingStaffIds)
                            ->distinct('staff_id')
                            ->count('staff_id');
                        
                        // Total counts
                        $instructorsCount = $teachingStaffIds->count();
                        $nonTeachingStaffCount = $nonTeachingStaffIds->count();
                        $evaluationCount = $evaluatedInstructorsCount + $evaluatedNonTeachingCount;
                        $totalStaffCount = $instructorsCount + $nonTeachingStaffCount;
                        
                        // Store detailed breakdown data
                        $student->evaluated_instructors = $evaluatedInstructorsCount;
                        $student->total_instructors = $instructorsCount;
                        $student->evaluated_nonteaching = $evaluatedNonTeachingCount;
                        $student->total_nonteaching = $nonTeachingStaffCount;
                        
                        if ($evaluationCount > 0) {
                            if ($evaluationCount >= $totalStaffCount) {
                                $evaluationStatus = "Done";
                            } else {
                                $evaluationStatus = "In Progress";
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
        
        return view('pages.add-students', compact('students', 'pendingRequestsCount'));
    }

    public function store(Request $request)
    {
        // Removed: Add student logic (no longer needed)
        abort(404);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:users,id',
            'username' => 'required|string|max:255|unique:users,username,' . $request->student_id,
            'email' => 'required|email|unique:users,email,' . $request->student_id,
            'full_name' => 'required|string|max:255',
            'school_id' => 'required|string|max:255|unique:users,school_id,' . $request->student_id,
            'course' => 'required|string|in:BSIT,BSHM,BSBA,BSED,BEED',
            'year_level' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'section' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }

        $student = User::find($request->student_id);
        
        if (!$student || $student->role !== 'student') {
            return redirect()->back()
                ->with('message', 'Student not found.')
                ->with('message_type', 'danger');
        }

        // Handle image upload for update
        $image_path = $student->profile_image;
        $update_image = false;
        
        if ($request->hasFile('image')) {
            $upload_dir = 'uploads/students';
            $upload_path = public_path($upload_dir);
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            
            $file = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            
            if ($file->move($upload_path, $imageName)) {
                // Delete old image if exists
                if ($student->profile_image && file_exists(public_path($upload_dir . '/' . $student->profile_image))) {
                    unlink(public_path($upload_dir . '/' . $student->profile_image));
                }
                $image_path = $imageName;
                $update_image = true;
            }
        }

        try {
            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'full_name' => $request->full_name,
                'school_id' => $request->school_id,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'section' => $request->section
            ];

            if ($update_image) {
                $updateData['profile_image'] = $image_path;
            }

            $student->update($updateData);

            return redirect()->back()
                ->with('message', 'Student updated successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error updating student. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $student = User::find($request->student_id);
        
        if (!$student || $student->role !== 'student') {
            return redirect()->back()
                ->with('message', 'Student not found.')
                ->with('message_type', 'danger');
        }

        // Check if student has evaluations
      //  $evaluationCount = \App\Models\Evaluation::where('student_id', $student->id)->count();
        
       // if ($evaluationCount > 0) {
         //   return redirect()->back()
          //      ->with('message', 'Cannot delete student with existing evaluations.')
           //     ->with('message_type', 'warning');
       // }

        try {
            // Delete image file if exists
            if ($student->profile_image && file_exists(public_path('uploads/students/' . $student->profile_image))) {
                unlink(public_path('uploads/students/' . $student->profile_image));
            }
            
            $student->delete();

            return redirect()->back()
                ->with('message', 'Student deleted successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting student. Please try again.')
                ->with('message_type', 'danger');
        }
    }
} 