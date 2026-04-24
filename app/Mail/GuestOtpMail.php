<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class GuestOtpMail extends Mailable
{
    use Queueable;

    public string $otp;
    public string $brandName;

    public function __construct(string $otp, string $brandName = 'Monsieur WiFi', string $emailLocale = 'en')
    {
        $this->otp = $otp;
        $this->brandName = $brandName;
        $this->locale = $emailLocale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails/guest-otp.subject', ['brand' => $this->brandName]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-otp',
            with: [
                'otp' => $this->otp,
                'brandName' => $this->brandName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
