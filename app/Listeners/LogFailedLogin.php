<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\LoginAttempt;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $role = $event->user?->role ?? $event->credentials['user_type'] ?? null;

        if ($role === 'student') {
            return;
        }

        LoginAttempt::create([
            'user_id'    => null,
            'email'      => $event->credentials['email'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status'     => 'failed',
        ]);
    }
}