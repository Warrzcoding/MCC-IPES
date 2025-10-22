<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginAttempt;

class LoginMonitorController extends Controller
{
    public function unreadCount(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $lastSeen = $user->login_monitor_last_seen_at ?? null;
        $count = LoginAttempt::when($lastSeen, function ($q) use ($lastSeen) {
                $q->where('created_at', '>', $lastSeen);
            }, function ($q) {
                $q->where('created_at', '>=', now()->subDay());
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->forceFill(['login_monitor_last_seen_at' => now()])->save();
        }
        return response()->json(['ok' => true]);
    }

    public function deleteAttempt($id)
    {
        try {
            $attempt = LoginAttempt::find($id);
            
            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login attempt not found.'
                ], 404);
            }

            $email = $attempt->email;
            $attempt->delete();

            return response()->json([
                'success' => true,
                'message' => "Login attempt for {$email} has been deleted successfully.",
                'id' => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting login attempt: ' . $e->getMessage()
            ], 500);
        }
    }
}