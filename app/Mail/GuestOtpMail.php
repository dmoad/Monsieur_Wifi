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
    public string $emailLocale;

    public function __construct(string $otp, string $brandName = 'Monsieur WiFi', string $emailLocale = 'en')
    {
        $this->otp         = $otp;
        $this->brandName   = $brandName;
        $this->emailLocale = $emailLocale;
    }

    public function envelope(): Envelope
    {
        $subject = $this->emailLocale === 'fr'
            ? "Votre code d'accès WiFi – {$this->brandName}"
            : "Your WiFi access code – {$this->brandName}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: $this->emailLocale === 'fr' ? 'emails.guest-otp-fr' : 'emails.guest-otp-en',
            with: [
                'otp'       => $this->otp,
                'brandName' => $this->brandName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
