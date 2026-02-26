<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDeliveredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $locale;

    public function __construct(Order $order, string $locale = 'en')
    {
        $this->order = $order;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr' 
                ? 'Votre Commande a été Livrée - Monsieur WiFi' 
                : 'Your Order Has Been Delivered - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->locale === 'fr' ? 'emails.order-delivered-fr' : 'emails.order-delivered-en',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
