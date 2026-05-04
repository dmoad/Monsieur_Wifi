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
     * @param string $locale  Recipient locale; defaults to 'fr' since this
     *                        notification targets the FR commercial team.
     */
    public function __construct(User $user, string $step, array $stepData = [], string $locale = 'fr')
    {
        $this->user = $user;
        $this->step = $step;
        $this->stepData = $stepData;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        $subjectKeys = [
            'registration' => 'emails/onboarding-step.subject_registration',
            'portal_created' => 'emails/onboarding-step.subject_portal_created',
            'subscription' => 'emails/onboarding-step.subject_subscription',
        ];

        $key = $subjectKeys[$this->step] ?? 'emails/onboarding-step.subject_default';

        return new Envelope(subject: __($key, ['name' => $this->user->name]));
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
