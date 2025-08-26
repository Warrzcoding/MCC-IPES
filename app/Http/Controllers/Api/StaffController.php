<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    public function fullName($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            return response()->json(['full_name' => $staff->full_name]);
        } else {
            return response()->json(['full_name' => null], 404);
        }
    }
} 