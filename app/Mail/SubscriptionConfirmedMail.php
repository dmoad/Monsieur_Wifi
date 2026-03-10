<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SubscriptionConfirmedMail extends Mailable
{
    use Queueable;

    public $user;
    public $subscriptionData;
    public $locale;

    public function __construct(User $user, array $subscriptionData, string $locale = 'en')
    {
        $this->user = $user;
        $this->subscriptionData = $subscriptionData;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr'
                ? 'Confirmation d\'abonnement - Monsieur WiFi'
                : 'Subscription Confirmation - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->locale === 'fr' ? 'emails.subscription-confirmed-fr' : 'emails.subscription-confirmed-en',
            with: [
                'user' => $this->user,
                'subscriptionData' => $this->subscriptionData,
                'locale' => $this->locale,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
