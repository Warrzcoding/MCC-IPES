<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class RegistrationOtpMail extends Mailable
{
    public $otpCode;
    public $email;
    public $expiryMinutes;

    public function __construct($otpCode, $email, $expiryMinutes = 5)
    {
        $this->otpCode = $otpCode;
        $this->email = $email;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                env('MAIL_STUDENT_FROM_ADDRESS'),
                env('MAIL_STUDENT_FROM_NAME')
            ),
            subject: 'MCC-IPES Registration Verification Code',
        );
    }

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

    public function attachments(): array
    {
        return [];
    }
}