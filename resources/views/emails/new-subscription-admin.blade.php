<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel abonnement - Monsieur WiFi</title>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%); padding: 40px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .header p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .details-block { background-color: #f8f8f8; padding: 25px; border-radius: 8px; margin: 20px 0; }
        .details-block h3 { margin-top: 0; color: #7367f0; font-size: 16px; }
        .footer { background-color: #f8f8f8; padding: 30px; text-align: center; font-size: 13px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvel Abonnement</h1>
            <p>Un utilisateur vient de souscrire</p>
        </div>
        <div class="content">
            <div class="details-block">
                <h3>Informations client</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Nom</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Email</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px;">Date d'inscription</td>
                        <td style="padding: 8px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="details-block">
                <h3>D&eacute;tails de l'abonnement</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Offre</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['plan_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">Montant</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['amount'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #666; font-size: 14px;">P&eacute;riode</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['interval'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px;">Date de souscription</td>
                        <td style="padding: 8px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $subscriptionData['start_date'] }}</td>
                    </tr>
                </table>
            </div>

            @if(!empty($subscriptionData['shipping_address']))
            <div class="details-block">
                <h3>Adresse de livraison</h3>
                <p style="color: #333; font-size: 14px; line-height: 1.8; margin: 0;">
                    {{ $subscriptionData['shipping_address']['name'] }}<br>
                    {{ $subscriptionData['shipping_address']['line1'] }}
                    @if($subscriptionData['shipping_address']['line2'])<br>{{ $subscriptionData['shipping_address']['line2'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['postal_code'] }} {{ $subscriptionData['shipping_address']['city'] }}
                    @if($subscriptionData['shipping_address']['state'])<br>{{ $subscriptionData['shipping_address']['state'] }}@endif
                    <br>{{ $subscriptionData['shipping_address']['country'] }}
                </p>
            </div>
            @endif
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Monsieur WiFi - Notification admin</p>
        </div>
    </div>
</body>
</html>
