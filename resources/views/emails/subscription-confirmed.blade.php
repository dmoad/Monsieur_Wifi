<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails/subscription-confirmed.title') }}</title>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #333333; margin-bottom: 20px; }
        .subscription-details { background-color: #f8f8f8; padding: 25px; border-radius: 8px; margin: 25px 0; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .info-text { font-size: 14px; color: #666; margin: 15px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('emails/subscription-confirmed.heading') }}</h1>
            <p>{{ __('emails/subscription-confirmed.subheading') }}</p>
        </div>
        <div class="content">
            <div class="greeting">{{ __('emails/subscription-confirmed.greeting') }} {{ $user->name }},</div>
            <p>{{ __('emails/subscription-confirmed.intro') }}</p>

            <div class="subscription-details">
                <h3 style="margin-top: 0; color: #7367f0;">{{ __('emails/subscription-confirmed.details_heading') }}</h3>

                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">{{ __('emails/subscription-confirmed.label_plan') }}</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['plan_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">{{ __('emails/subscription-confirmed.label_amount') }}</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['amount'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">{{ __('emails/subscription-confirmed.label_billing_period') }}</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['interval'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-size: 14px;">{{ __('emails/subscription-confirmed.label_start_date') }}</td>
                        <td style="padding: 10px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['start_date'] }}</td>
                    </tr>
                </table>
            </div>

            @if(!empty($subscriptionData['shipping_address']))
            <div class="subscription-details">
                <h3 style="margin-top: 0; color: #7367f0;">{{ __('emails/subscription-confirmed.shipping_heading') }}</h3>
                <p style="color: #333; font-size: 14px; line-height: 1.8; margin: 0;">
                    {{ $subscriptionData['shipping_address']['name'] }}<br>
                    {{ $subscriptionData['shipping_address']['line1'] }}
                    @if($subscriptionData['shipping_address']['line2'])<br>{{ $subscriptionData['shipping_address']['line2'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['postal_code'] }} {{ $subscriptionData['shipping_address']['city'] }}
                    @if($subscriptionData['shipping_address']['state']), {{ $subscriptionData['shipping_address']['state'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['country'] }}
                </p>
            </div>
            @endif

            <p class="info-text">{{ __('emails/subscription-confirmed.manage_hint') }}</p>

            <div style="text-align: center;">
                <a href="{{ url('/' . app()->getLocale() . '/profile') }}" class="button">{{ __('emails/subscription-confirmed.btn_dashboard') }}</a>
            </div>

            <p class="info-text">{!! __('emails/subscription-confirmed.support_hint_html', ['email' => 'support@monsieur-wifi.com']) !!}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Monsieur WiFi. {{ __('emails/subscription-confirmed.footer_rights') }}</p>
            <p>{{ __('emails/subscription-confirmed.footer_note') }}</p>
        </div>
    </div>
</body>
</html>
