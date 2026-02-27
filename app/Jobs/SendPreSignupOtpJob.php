<?php

namespace App\Jobs;

use App\Mail\OtpVerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendPreSignupOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $otp;
    public $email;
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Retry delays in seconds

    public function __construct(string $otp, string $email)
    {
        $this->otp = $otp;
        $this->email = $email;
    }

    public function handle()
    {
        try {
            Mail::mailer('gmail_student')->to($this->email)->send(new OtpVerificationMail($this->otp, $this->email, 5));
            Log::info("Pre-signup OTP email sent successfully to {$this->email}");
        } catch (\Throwable $exception) {
            Log::error('Pre-signup OTP mail failed: ' . $exception->getMessage());

            // BACKUP: Log OTP to file for manual retrieval
            Log::emergency("PRESIGNUP OTP BACKUP - Email: {$this->email}, OTP: {$this->otp}, Time: " . now());
            Storage::append('presignup_otp_backup.log', now() . " - Email: {$this->email} - OTP: {$this->otp}\n");

            // Re-throw to mark job as failed
            throw $exception;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical("SendPreSignupOtpJob failed permanently for {$this->email}: " . $exception->getMessage());
    }
}