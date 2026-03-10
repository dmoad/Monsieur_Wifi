<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'abonnement - Monsieur WiFi</title>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #333333; margin-bottom: 20px; }
        .subscription-details { background-color: #f8f8f8; padding: 25px; border-radius: 8px; margin: 25px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #666; font-size: 14px; }
        .detail-value { color: #333; font-weight: 600; font-size: 14px; }
        .total-row { margin-top: 15px; padding-top: 15px; border-top: 2px solid #7367f0; font-size: 20px; font-weight: 600; color: #333; text-align: right; }
        .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .info-text { font-size: 14px; color: #666; margin: 15px 0; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Abonnement Confirm&eacute; !</h1>
            <p>Merci pour votre confiance</p>
        </div>
        <div class="content">
            <div class="greeting">Bonjour {{ $user->name }},</div>
            <p>Votre abonnement Monsieur WiFi a &eacute;t&eacute; activ&eacute; avec succ&egrave;s. Voici le r&eacute;capitulatif de votre souscription :</p>

            <div class="subscription-details">
                <h3 style="margin-top: 0; color: #7367f0;">D&eacute;tails de l'abonnement</h3>

                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Offre</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['plan_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Montant</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['amount'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">P&eacute;riode</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['interval'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-size: 14px;">Date de d&eacute;but</td>
                        <td style="padding: 10px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['start_date'] }}</td>
                    </tr>
                </table>
            </div>

            @if(!empty($subscriptionData['shipping_address']))
            <div class="subscription-details">
                <h3 style="margin-top: 0; color: #7367f0;">Adresse de livraison</h3>
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

            <p class="info-text">Vous pouvez g&eacute;rer votre abonnement &agrave; tout moment depuis votre espace client.</p>

            <div style="text-align: center;">
                <a href="{{ url('/en/profile') }}" class="button">Acc&eacute;der &agrave; mon espace</a>
            </div>

            <p class="info-text">Si vous avez des questions, n'h&eacute;sitez pas &agrave; nous contacter &agrave; <a href="mailto:support@monsieur-wifi.com" style="color: #7367f0;">support@monsieur-wifi.com</a></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Monsieur WiFi. Tous droits r&eacute;serv&eacute;s.</p>
            <p>Cet email a &eacute;t&eacute; envoy&eacute; suite &agrave; votre souscription sur monsieur-wifi.com</p>
        </div>
    </div>
</body>
</html>
