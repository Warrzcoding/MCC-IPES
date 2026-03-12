<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RegistrationOtpMail extends Mailable
{

    public $otpCode;
    public $email;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode, $email, $expiryMinutes = 5)
    {
        $this->otpCode = $otpCode;
        $this->email = $email;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MCC-IPES Registration Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-otp',
            with: [
                'otpCode' => $this->otpCode,
                'email' => $this->email,
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
