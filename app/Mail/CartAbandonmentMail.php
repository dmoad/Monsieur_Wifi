<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartAbandonmentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $cart;
    public $locale;

    public function __construct(Cart $cart, string $locale = 'en')
    {
        $this->cart = $cart;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->locale === 'fr' 
                ? 'Vous avez oublié quelque chose - Monsieur WiFi' 
                : 'You Left Something Behind - Monsieur WiFi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->locale === 'fr' ? 'emails.cart-abandonment-fr' : 'emails.cart-abandonment-en',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
