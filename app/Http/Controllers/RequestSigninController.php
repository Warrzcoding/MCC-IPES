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

    // Approve a request: move to users table, then delete from request_signin
    public function approve($id)
    {
        $request = RequestSignin::findOrFail($id);
        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'username' => $request->username,
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

    // Reject a request: mark as rejected
    public function reject($id)
    {
        $request = RequestSignin::findOrFail($id);
        $request->status = 'rejected';
        $request->save();
        return redirect()->back()->with('message', 'Request rejected.')->with('message_type', 'warning');
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