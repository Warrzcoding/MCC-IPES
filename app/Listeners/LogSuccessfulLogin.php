<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginAttempt;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        LoginAttempt::create([
            'user_id'    => $event->user?->id,
            'email'      => $event->user?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status'     => 'success',
        ]);
    }
}