<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestSignin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RequestSigninController extends Controller
{
    // Show pending and rejected requests with pagination
    public function index()
    {
        $pendingRequests = RequestSignin::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $rejectedRequests = RequestSignin::where('status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('pages.pending-requests', compact('pendingRequests', 'rejectedRequests'));
    }

    /**
     * Generate a unique username by appending a numeric suffix.
     * Example: 'jdoe' -> 'jdoe1', 'jdoe2', ...
     */
    private function generateUniqueUsername($base)
    {
        $candidate = $base;
        $suffix = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = $base . $suffix;
            $suffix++;
            // safety: avoid infinite loop
            if ($suffix > 1000) break;
        }
        return $candidate;
    }

    // Approve a request: move to users table, then delete from request_signin
    public function approve($id)
    {
        $request = RequestSignin::findOrFail($id);
        
        // Check for duplicate email and school_id
        $existingUser = User::where('email', $request->email)
                            ->orWhere('school_id', $request->school_id)
                            ->first();

        if ($existingUser) {
            return redirect()->back()->with('message', 'User with this email or school ID already exists. Request not approved.')->with('message_type', 'danger');
        }
        
        DB::beginTransaction();
        try {
            // Create user
            $usernameToUse = $request->username;
            if (User::where('username', $usernameToUse)->exists()) {
                $usernameToUse = $this->generateUniqueUsername($usernameToUse);
            }

            $user = User::create([
                'username' => $usernameToUse,
                'email' => $request->email,
                'password' => $request->password, // Already hashed
                'full_name' => $request->full_name,
                'school_id' => $request->school_id,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'section' => $request->section,
                'role' => $request->role === 'admin' ? 'student' : $request->role, // Convert admin to student
                'profile_image' => $request->profile_image,
                'status' => 'active',
                'is_main_admin' => $request->is_main_admin,
                'last_login' => $request->last_login,
                'last_active_at' => $request->last_active_at,
                'email_verified_at' => $request->email_verified_at,
                'remember_token' => $request->remember_token,
            ]);
            // Delete request
            $request->delete();
            DB::commit();
            return redirect()->back()->with('message', 'Request approved and user created!')->with('message_type', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error approving request: ' . $e->getMessage())->with('message_type', 'danger');
        }
    }

    // Approve multiple requests
    public function approveMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('message', 'No requests selected.')->with('message_type', 'info');
        }

        DB::beginTransaction();
        try {
            $requests = RequestSignin::whereIn('id', $ids)->get();
            $approvedCount = 0;
            $skippedCount = 0;

            foreach ($requests as $req) {
                // Check for duplicate email and school_id
                $existingUser = User::where('email', $req->email)
                                    ->orWhere('school_id', $req->school_id)
                                    ->first();

                if ($existingUser) {
                    $skippedCount++;
                    continue;
                }
                // Ensure username uniqueness: if username exists, generate a unique variant
                $usernameToUse = $req->username;
                if (User::where('username', $usernameToUse)->exists()) {
                    $usernameToUse = $this->generateUniqueUsername($usernameToUse);
                }

                // Create user based on request
                User::create([
                    'username' => $usernameToUse,
                    'email' => $req->email,
                    'password' => $req->password, // Already hashed
                    'full_name' => $req->full_name,
                    'school_id' => $req->school_id,
                    'course' => $req->course,
                    'year_level' => $req->year_level,
                    'section' => $req->section,
                    'role' => $req->role === 'admin' ? 'student' : $req->role,
                    'profile_image' => $req->profile_image,
                    'status' => 'active',
                    'is_main_admin' => $req->is_main_admin,
                    'last_login' => $req->last_login,
                    'last_active_at' => $req->last_active_at,
                    'email_verified_at' => $req->email_verified_at,
                    'remember_token' => $req->remember_token,
                ]);

                // Delete request after creating user
                $req->delete();
                $approvedCount++;
            }

            DB::commit();
            $message = "$approvedCount request(s) approved";
            if ($skippedCount > 0) {
                $message .= ", $skippedCount skipped (duplicate email/school_id).";
            } else {
                $message .= "!";
            }
            return redirect()->back()->with('message', $message)->with('message_type', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error approving selected requests: ' . $e->getMessage())->with('message_type', 'danger');
        }
    }

    // Reject a request: mark as rejected
    public function reject($id)
    {
        $request = RequestSignin::findOrFail($id);
        $request->status = 'rejected';
        $request->save();
        return redirect()->back()->with('message', 'Request rejected.')->with('message_type', 'warning');
    }

    // Delete multiple rejected requests permanently
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('message', 'No requests selected.')->with('message_type', 'info');
        }

        DB::beginTransaction();
        try {
            $requests = RequestSignin::whereIn('id', $ids)->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($requests as $req) {
                // Only allow deletion of rejected requests
                if ($req->status !== 'rejected') {
                    $skippedCount++;
                    continue;
                }

                // Delete the profile image if it exists
                if ($req->profile_image) {
                    $imagePath = public_path('uploads/students/' . $req->profile_image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $req->delete();
                $deletedCount++;
            }

            DB::commit();
            $message = "$deletedCount rejected request(s) deleted";
            if ($skippedCount > 0) {
                $message .= ", $skippedCount skipped (not rejected status).";
            } else {
                $message .= "!";
            }
            return redirect()->back()->with('message', $message)->with('message_type', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error deleting selected requests: ' . $e->getMessage())->with('message_type', 'danger');
        }
    }

    // Delete a rejected request permanently
    public function delete($id)
    {
        $request = RequestSignin::findOrFail($id);

        // Only allow deletion of rejected requests
        if ($request->status !== 'rejected') {
            return redirect()->back()
                ->with('message', 'Only rejected requests can be deleted.')->with('message_type', 'danger');
        }

        try {
            // Delete the profile image if it exists
            if ($request->profile_image) {
                $imagePath = public_path('uploads/students/' . $request->profile_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $request->delete();

            return redirect()->back()
                ->with('message', 'Rejected request deleted successfully.')->with('message_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting request: ' . $e->getMessage())->with('message_type', 'danger');
        }
    }
} 