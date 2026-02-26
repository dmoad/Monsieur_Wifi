<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #00cfe8 0%, #0097c2 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .tracking-box { background-color: #f8f8f8; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .tracking-number { font-size: 24px; color: #00cfe8; font-weight: 600; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>📦 Your Order Has Been Shipped!</h1></div>
        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Great news! Your order <strong>#{{ $order->order_number }}</strong> has been shipped and is on its way to you.</p>
            <div class="tracking-box">
                <p style="margin: 0 0 10px 0;"><strong>Shipping Provider:</strong> {{ $order->shipping_provider }}</p>
                <p style="margin: 0 0 10px 0;"><strong>Tracking Number:</strong></p>
                <div class="tracking-number">{{ $order->tracking_id }}</div>
            </div>
            <div style="text-align: center;">
                <a href="{{ url('/en/orders/' . $order->order_number) }}" class="button">Track Your Order</a>
            </div>
        </div>
        <div class="footer"><p>&copy; {{ date('Y') }} Monsieur WiFi. All rights reserved.</p></div>
    </div>
</body>
</html>
