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

    public function __construct(User $user, array $subscriptionData, string $locale = 'en')
    {
        $this->user = $user;
        $this->subscriptionData = $subscriptionData;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails/subscription-confirmed.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-confirmed',
            with: [
                'user' => $this->user,
                'subscriptionData' => $this->subscriptionData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
