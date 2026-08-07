<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $code,
        public readonly int    $expiryMinutes = 5,
    ) {}

    public function envelope(): Envelope
    {
        $subject = app()->getLocale() === 'ar'
            ? 'رمز التحقق — Elite Tech Community'
            : 'Verification Code — Elite Tech Community';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(htmlView: 'emails.otp');
    }
}
