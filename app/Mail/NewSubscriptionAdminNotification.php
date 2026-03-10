<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewSubscriptionAdminNotification extends Mailable
{
    use Queueable;

    public $user;
    public $subscriptionData;

    public function __construct(User $user, array $subscriptionData)
    {
        $this->user = $user;
        $this->subscriptionData = $subscriptionData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvel abonnement - ' . $this->user->name . ' - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-subscription-admin',
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
