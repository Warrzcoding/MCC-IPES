<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuperAdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $adminName;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otpCode, ?string $adminName = null, int $expiryMinutes = 5)
    {
        $this->otpCode = $otpCode;
        $this->adminName = $adminName;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CRITICAL ACCESS: Super Admin Authorization Token',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.superadmin-otp',
            with: [
                'otpCode' => $this->otpCode,
                'adminName' => $this->adminName,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
