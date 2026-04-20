<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CartAbandonmentMail extends Mailable
{
    use Queueable;

    public $cart;

    public function __construct(Cart $cart, string $locale = 'en')
    {
        $this->cart = $cart;
        $this->locale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails/cart-abandonment.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cart-abandonment',
            with: [
                'cart' => $this->cart,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
