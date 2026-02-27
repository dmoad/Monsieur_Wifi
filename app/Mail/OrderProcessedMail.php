<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderProcessedMail extends Mailable
{
    use Queueable;

    public $order;
    public $locale;

    public function __construct(Order $order, string $locale = 'en')
    {
        // Eager load relationships before serialization
        $order->load(['user', 'items.productModel', 'shippingAddress', 'billingAddress']);
        $this->order = $order;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr' 
                ? 'Confirmation de Commande - Monsieur WiFi' 
                : 'Order Confirmation - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        // Ensure relationships are loaded when rendering
        if (!$this->order->relationLoaded('user')) {
            $this->order->load(['user', 'items.productModel', 'shippingAddress', 'billingAddress']);
        }
        
        return new Content(
            view: $this->locale === 'fr' ? 'emails.order-processed-fr' : 'emails.order-processed-en',
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
