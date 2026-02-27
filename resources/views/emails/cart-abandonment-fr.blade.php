<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #ff9f43 0%, #ff6b6b 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .product-box { background-color: #f8f8f8; padding: 15px; border-radius: 8px; margin: 10px 0; display: flex; justify-content: space-between; align-items: center; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>🛒 Vous avez oublié quelque chose!</h1></div>
        <div class="content">
            <p>Bonjour {{ $cart->user->name }},</p>
            <p>Nous avons remarqué que vous avez laissé des articles dans votre panier. Ils vous attendent encore!</p>
            @foreach($cart->items as $item)
            <div class="product-box">
                <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                <span>€{{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
            <p style="text-align: center; margin-top: 30px; font-size: 18px; color: #7367f0;">
                <strong>Total: €{{ number_format($cart->getTotal(), 2) }}</strong>
            </p>
            <div style="text-align: center;">
                <a href="{{ url('/fr/panier') }}" class="button">Finaliser Votre Commande</a>
            </div>
        </div>
        <div class="footer"><p>&copy; {{ date('Y') }} Monsieur WiFi. Tous droits réservés.</p></div>
    </div>
</body>
</html>
