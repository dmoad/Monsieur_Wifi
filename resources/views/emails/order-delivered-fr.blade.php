<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #28c76f 0%, #1e9f59 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>✓ Commande Livrée!</h1></div>
        <div class="content">
            <p>Bonjour {{ $order->user->name }},</p>
            <p>Votre commande <strong><a href="{{ url('/fr/commandes/' . $order->order_number) }}">#{{ $order->order_number }}</a></strong> a été livrée avec succès!</p>
            <p>Nous espérons que vous apprécierez votre achat. Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            <p>Merci d'avoir choisi Monsieur WiFi!</p>
        </div>
        <div class="footer"><p>&copy; {{ date('Y') }} Monsieur WiFi. Tous droits réservés.</p></div>
    </div>
</body>
</html>
