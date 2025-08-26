<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
} 