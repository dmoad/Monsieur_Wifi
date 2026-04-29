<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Onboarding - Monsieur WiFi</title>
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background-color: #f8f8f8; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .header { padding: 35px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 35px 30px; }
        .step-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
        .details-block { background-color: #f8f8f8; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .details-block h3 { margin-top: 0; font-size: 14px; font-weight: 600; color: #555; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e8e8e8; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #888; }
        .detail-value { color: #333; font-weight: 600; }
        .timeline { display: flex; justify-content: center; gap: 0; margin: 25px 0; position: relative; }
        .timeline-item { text-align: center; flex: 1; position: relative; }
        .timeline-item .circle { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 700; font-size: 14px; }
        .timeline-item .label { font-size: 11px; color: #888; }
        .circle-completed { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
        .circle-active { background: linear-gradient(135deg, #7367f0, #9e95f5); color: white; box-shadow: 0 3px 10px rgba(115, 103, 240, 0.4); }
        .circle-pending { background: #f0f0f0; color: #999; border: 2px solid #ddd; }
        .footer { background-color: #f8f8f8; padding: 25px 30px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        @php
            $stepConfig = [
                'registration' => [
                    'color' => '#7367f0',
                    'gradient' => 'linear-gradient(135deg, #7367f0 0%, #9055ff 100%)',
                    'title' => 'Nouvelle Inscription',
                    'subtitle' => 'Un nouveau client vient de s\'inscrire',
                    'badge' => 'Etape 1/3',
                    'badgeColor' => '#7367f0',
                    'steps' => [1, 0, 0],
                ],
                'portal_created' => [
                    'color' => '#00cfe8',
                    'gradient' => 'linear-gradient(135deg, #00cfe8 0%, #1ce7ff 100%)',
                    'title' => 'Portail Captif Créé',
                    'subtitle' => 'Un client vient de créer son portail captif',
                    'badge' => 'Etape 1/3',
                    'badgeColor' => '#00cfe8',
                    'steps' => [2, 0, 0],
                ],
                'subscription' => [
                    'color' => '#28a745',
                    'gradient' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                    'title' => 'Nouvel Abonnement',
                    'subtitle' => 'Un client vient de souscrire un abonnement',
                    'badge' => 'Etape 2/3',
                    'badgeColor' => '#28a745',
                    'steps' => [2, 1, 0],
                ],
            ];
            $config = $stepConfig[$step] ?? $stepConfig['registration'];
        @endphp

        <div class="header" style="background: {{ $config['gradient'] }};">
            <h1>{{ $config['title'] }}</h1>
            <p>{{ $config['subtitle'] }}</p>
        </div>

        <div class="content">
            <span class="step-badge" style="background: {{ $config['badgeColor'] }}15; color: {{ $config['badgeColor'] }};">
                {{ $config['badge'] }}
            </span>

            <!-- Client Info -->
            <div class="details-block">
                <h3>Informations client</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #888; font-size: 14px;">Nom</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #888; font-size: 14px;">Email</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #888; font-size: 14px;">Date d'inscription</td>
                        <td style="padding: 8px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            @if($step === 'portal_created' && !empty($stepData))
            <div class="details-block">
                <h3>D&eacute;tails du portail</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    @if(!empty($stepData['portal_name']))
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #888; font-size: 14px;">Nom du portail</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $stepData['portal_name'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($stepData['description']))
                    <tr>
                        <td style="padding: 8px 0; color: #888; font-size: 14px;">Entreprise</td>
                        <td style="padding: 8px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $stepData['description'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            @if($step === 'subscription' && !empty($stepData))
            <div class="details-block">
                <h3>D&eacute;tails de l'abonnement</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    @if(!empty($stepData['plan_name']))
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #888; font-size: 14px;">Offre</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #e8e8e8; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $stepData['plan_name'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($stepData['amount']))
                    <tr>
                        <td style="padding: 8px 0; color: #888; font-size: 14px;">Montant</td>
                        <td style="padding: 8px 0; color: #333; font-weight: 600; font-size: 14px; text-align: right;">{{ $stepData['amount'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            <!-- Timeline -->
            <div class="timeline">
                <div class="timeline-item">
                    <div class="circle {{ $config['steps'][0] === 2 ? 'circle-completed' : ($config['steps'][0] === 1 ? 'circle-active' : 'circle-pending') }}">
                        @if($config['steps'][0] === 2)
                            &#10003;
                        @else
                            1
                        @endif
                    </div>
                    <div class="label">Inscription</div>
                </div>
                <div class="timeline-item">
                    <div class="circle {{ $config['steps'][1] === 2 ? 'circle-completed' : ($config['steps'][1] === 1 ? 'circle-active' : 'circle-pending') }}">
                        @if($config['steps'][1] === 2)
                            &#10003;
                        @else
                            2
                        @endif
                    </div>
                    <div class="label">Abonnement</div>
                </div>
                <div class="timeline-item">
                    <div class="circle {{ $config['steps'][2] === 2 ? 'circle-completed' : ($config['steps'][2] === 1 ? 'circle-active' : 'circle-pending') }}">
                        @if($config['steps'][2] === 2)
                            &#10003;
                        @else
                            3
                        @endif
                    </div>
                    <div class="label">Livraison</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Monsieur WiFi - Notification &eacute;quipe commerciale</p>
        </div>
    </div>
</body>
</html>
