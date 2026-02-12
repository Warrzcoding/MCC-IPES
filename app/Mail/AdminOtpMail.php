<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $adminName;
    public $expiryMinutes;

    public function __construct(string $otpCode, ?string $adminName = null, int $expiryMinutes = 5)
    {
        $this->otpCode = $otpCode;
        $this->adminName = $adminName;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Administrator Login Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-otp',
            with: [
                'otpCode' => $this->otpCode,
                'adminName' => $this->adminName,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
