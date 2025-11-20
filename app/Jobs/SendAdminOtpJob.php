<?php

namespace App\Jobs;

use App\Mail\AdminOtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendAdminOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $otp;
    public $email;
    public $adminName;
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry delays in seconds

    public function __construct(string $otp, string $email, ?string $adminName = null)
    {
        $this->otp = $otp;
        $this->email = $email;
        $this->adminName = $adminName;
    }

    public function handle()
    {
        try {
            // Try admin_smtp first
            Mail::mailer('admin_smtp')->to($this->email)->send(new AdminOtpMail($this->otp, $this->adminName, 5));
            Log::info("Admin OTP email sent successfully to {$this->email}");
        } catch (\Throwable $exception) {
            Log::warning('Admin OTP mailer failed, attempting default transport: ' . $exception->getMessage());
            try {
                // Fallback to default mailer
                Mail::to($this->email)->send(new AdminOtpMail($this->otp, $this->adminName, 5));
                Log::info("Admin OTP email sent via fallback to {$this->email}");
            } catch (\Throwable $fallbackException) {
                Log::error('Admin OTP mail fallback failed: ' . $fallbackException->getMessage());

                // BACKUP: Log OTP to file for manual retrieval
                Log::emergency("ADMIN OTP BACKUP - Email: {$this->email}, OTP: {$this->otp}, Time: " . now());
                Storage::append('admin_otp_backup.log', now() . " - Admin: {$this->email} - OTP: {$this->otp}\n");

                // Re-throw to mark job as failed
                throw $fallbackException;
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical("SendAdminOtpJob failed permanently for {$this->email}: " . $exception->getMessage());
    }
}