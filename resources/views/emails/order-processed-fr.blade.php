<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande - Monsieur WiFi</title>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #333333; margin-bottom: 20px; }
        .order-number { font-size: 24px; color: #7367f0; font-weight: 600; margin: 20px 0; }
        .order-details { background-color: #f8f8f8; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .order-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .total { font-size: 20px; font-weight: 600; color: #333; margin-top: 20px; text-align: right; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Commande Confirmée!</h1>
            <p>Merci pour votre commande</p>
        </div>
        <div class="content">
            <div class="greeting">Bonjour {{ $order->user->name }},</div>
            <p>Votre commande a été passée avec succès et est maintenant en cours de traitement.</p>
            <div class="order-number">Commande #{{ $order->order_number }}</div>
            
            <div class="order-details">
                <h3 style="margin-top: 0;">Résumé de la Commande</h3>
                @foreach($order->items as $item)
                <div class="order-item">
                    <span>{{ $item->product_model->name }} × {{ $item->quantity }}</span>
                    <span>${{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
                <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #7367f0;">
                    <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                        <span>Sous-total:</span>
                        <span>${{ number_format($order->product_amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                        <span>Livraison:</span>
                        <span>${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                        <span>Taxes:</span>
                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="total">Total: ${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
            
            <h3>Adresse de Livraison</h3>
            <p>
                {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
                {{ $order->shippingAddress->address_line1 }}<br>
                @if($order->shippingAddress->address_line2){{ $order->shippingAddress->address_line2 }}<br>@endif
                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }} {{ $order->shippingAddress->postal_code }}<br>
                {{ $order->shippingAddress->country }}
            </p>
            
            <div style="text-align: center;">
                <a href="{{ url('/fr/commandes/' . $order->order_number) }}" class="button">Voir les Détails</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Monsieur WiFi. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
