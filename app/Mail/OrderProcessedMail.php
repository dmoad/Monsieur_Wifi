<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderProcessedMail extends Mailable implements ShouldQueue
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
                ? 'Confirmation de Commande - Monsieur WiFi' 
                : 'Order Confirmation - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->locale === 'fr' ? 'emails.order-processed-fr' : 'emails.order-processed-en',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
