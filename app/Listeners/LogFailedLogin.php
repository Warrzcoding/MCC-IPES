<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\LoginAttempt;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        LoginAttempt::create([
            'user_id'    => null,
            'email'      => $event->credentials['email'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status'     => 'failed',
        ]);
    }
}