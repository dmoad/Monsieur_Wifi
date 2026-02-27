<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $locale;

    public function __construct(Order $order, string $locale = 'en')
    {
        // Eager load relationships before serialization
        $order->load(['user', 'items.productModel']);
        $this->order = $order;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr' 
                ? 'Échec du Paiement - Monsieur WiFi' 
                : 'Payment Failed - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        // Ensure relationships are loaded when rendering
        if (!$this->order->relationLoaded('user')) {
            $this->order->load(['user', 'items.productModel']);
        }
        
        return new Content(
            view: $this->locale === 'fr' ? 'emails.payment-failed-fr' : 'emails.payment-failed-en',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
