<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #ea5455 0%, #ff6b6b 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>{{ __('emails/payment-failed.heading') }}</h1></div>
        <div class="content">
            <p>{{ __('emails/payment-failed.greeting') }} {{ $order->user->name }},</p>
            <p>{!! __('emails/payment-failed.intro_html', ['order_number' => e($order->order_number)]) !!}</p>
            <p>{{ __('emails/payment-failed.body_retry') }}</p>
            <div style="text-align: center;">
                <a href="{{ url('/' . app()->getLocale() . '/orders/' . $order->order_number) }}" class="button">{{ __('emails/payment-failed.btn_retry') }}</a>
            </div>
        </div>
        <div class="footer"><p>&copy; {{ date('Y') }} Monsieur WiFi. {{ __('emails/payment-failed.footer_rights') }}</p></div>
    </div>
</body>
</html>
