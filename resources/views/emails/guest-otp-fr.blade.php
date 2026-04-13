<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 560px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #7367f0 0%, #5e50ee 100%); padding: 36px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.02em; }
        .header p  { margin: 8px 0 0; font-size: 14px; opacity: 0.85; }
        .content { padding: 36px 30px; color: #333333; }
        .content p { margin: 0 0 16px; font-size: 15px; }
        .otp-box {
            display: block;
            width: fit-content;
            margin: 24px auto;
            background: #f4f6f9;
            border: 2px dashed #7367f0;
            border-radius: 12px;
            padding: 16px 40px;
            text-align: center;
        }
        .otp-code { font-size: 40px; font-weight: 700; letter-spacing: 12px; color: #7367f0; }
        .otp-label { font-size: 12px; color: #888; margin-top: 4px; }
        .note { font-size: 13px; color: #888888; background: #f9f9f9; border-left: 3px solid #7367f0; padding: 10px 14px; border-radius: 4px; }
        .footer { background-color: #f4f6f9; padding: 24px 30px; text-align: center; font-size: 12px; color: #aaaaaa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Code d'accès WiFi</h1>
            <p>{{ $brandName }}</p>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Utilisez le code à usage unique ci-dessous pour finaliser votre connexion WiFi. Il est valable <strong>5 minutes</strong>.</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">Code d'accès unique</div>
            </div>

            <p class="note">Si vous n'avez pas demandé ce code, vous pouvez ignorer cet e-mail. Quelqu'un a peut-être saisi votre adresse par erreur.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $brandName }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
