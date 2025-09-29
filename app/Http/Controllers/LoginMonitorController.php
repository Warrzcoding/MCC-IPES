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
}