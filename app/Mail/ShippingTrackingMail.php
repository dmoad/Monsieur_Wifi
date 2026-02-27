<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ShippingTrackingMail extends Mailable
{
    use Queueable;

    public $order;
    public $locale;

    public function __construct(Order $order, string $locale = 'en')
    {
        // Eager load relationships before serialization
        $order->load(['user', 'shippingAddress']);
        $this->order = $order;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr' 
                ? 'Votre Commande a été Expédiée - Monsieur WiFi' 
                : 'Your Order Has Been Shipped - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        // Ensure relationships are loaded when rendering
        if (!$this->order->relationLoaded('user')) {
            $this->order->load(['user', 'shippingAddress']);
        }
        
        return new Content(
            view: $this->locale === 'fr' ? 'emails.shipping-tracking-fr' : 'emails.shipping-tracking-en',
            with: [
                'order' => $this->order,
                'locale' => $this->locale,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
