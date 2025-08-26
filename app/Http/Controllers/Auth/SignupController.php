<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\RequestSignin;

class SignupController extends Controller
{
    public function showSignupForm(Request $request)
    {
        $type = $request->query('type', 'student');
        $school_id = $request->query('school_id');
        $verified_email = $request->query('verified_email');
        
        return view('signup', compact('type', 'school_id', 'verified_email'));
    }

    public function signup(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:request_signin',
            'email' => 'required|email|unique:request_signin',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'school_id' => 'required|string|unique:request_signin|regex:/^\d{4}-\d{4}$/',
            'course' => 'required|string|in:BSIT,BSHM,BSBA,BSED,BEED',
            'year_level' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'section' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle profile image upload
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            $uploadPath = public_path('uploads/students');
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move uploaded file
            if ($image->move($uploadPath, $imageName)) {
                $profileImage = $imageName;
            }
        }

        // Create new request_signin record
        $requestSignin = RequestSignin::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'full_name' => $request->full_name,
            'school_id' => $request->school_id,
            'course' => $request->course,
            'year_level' => $request->year_level,
            'section' => $request->section,
            'role' => 'student',
            'status' => 'pending',
            'profile_image' => $profileImage
        ]);

        // Do NOT log the user in here

        // Redirect to login with success message
        return redirect()->route('login')->with([
            'success' => 'Registration request submitted! Please wait for admin approval.',
            'registration_success' => true
        ]);
    }

    public function checkDuplicateId(Request $request)
    {
        $school_id = $request->input('school_id');
        if (!$school_id) {
            return response()->json(['exists' => false, 'message' => 'No ID provided.'], 400);
        }
        $existsInUsers = \App\Models\User::where('school_id', $school_id)->exists();
        $existsInRequests = \App\Models\RequestSignin::where('school_id', $school_id)->exists();
        if ($existsInUsers || $existsInRequests) {
            return response()->json(['exists' => true, 'message' => 'School ID already exists.']);
        }
        return response()->json(['exists' => false]);
    }
}