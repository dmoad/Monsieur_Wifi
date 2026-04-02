<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OnboardingStepNotification extends Mailable
{
    use Queueable;

    public $user;
    public $step;
    public $stepData;

    /**
     * @param User $user
     * @param string $step  'registration' | 'portal_created' | 'subscription'
     * @param array $stepData  Additional data for the step
     */
    public function __construct(User $user, string $step, array $stepData = [])
    {
        $this->user = $user;
        $this->step = $step;
        $this->stepData = $stepData;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'registration' => 'Nouvelle inscription',
            'portal_created' => 'Nouveau portail captif créé',
            'subscription' => 'Nouvel abonnement souscrit',
        ];

        $subject = ($subjects[$this->step] ?? 'Nouvelle étape') . ' - ' . $this->user->name;

        return new Envelope(subject: $subject . ' - Monsieur WiFi');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-step',
            with: [
                'user' => $this->user,
                'step' => $this->step,
                'stepData' => $this->stepData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
