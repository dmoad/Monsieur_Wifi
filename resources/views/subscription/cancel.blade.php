<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Monsieur WiFi - Paiement annulé">
    <title id="page-title">Paiement annulé - Monsieur WiFi</title>
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cancel-card {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .cancel-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .cancel-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
        }

        .cancel-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .cancel-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7367f0, #9e95f5);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(115, 103, 240, 0.4);
        }

        .btn-outline {
            background: white;
            color: #7367f0;
            border: 2px solid #7367f0;
        }

        .btn-outline:hover {
            background: #7367f0;
            color: white;
        }
    </style>
</head>

<body>
    <div class="cancel-card">
        <div class="cancel-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h1 class="cancel-title" id="title">Paiement annulé</h1>
        <p class="cancel-message" id="message">
            Votre paiement a été annulé. Aucun montant n'a été débité de votre compte.
            Vous pouvez réessayer quand vous le souhaitez.
        </p>
        <div class="buttons">
            <a href="/pricing" class="btn btn-primary" id="btn-retry">Réessayer</a>
            <a href="/en/dashboard" class="btn btn-outline" id="btn-dashboard">Retour au tableau de bord</a>
        </div>
    </div>

    <script>
        const translations = {
            en: {
                pageTitle: 'Payment cancelled - Monsieur WiFi',
                title: 'Payment cancelled',
                message: 'Your payment has been cancelled. No amount has been charged to your account. You can try again whenever you want.',
                retry: 'Try again',
                dashboard: 'Back to dashboard'
            },
            fr: {
                pageTitle: 'Paiement annulé - Monsieur WiFi',
                title: 'Paiement annulé',
                message: 'Votre paiement a été annulé. Aucun montant n\'a été débité de votre compte. Vous pouvez réessayer quand vous le souhaitez.',
                retry: 'Réessayer',
                dashboard: 'Retour au tableau de bord'
            }
        };

        function detectLanguage() {
            const savedLang = localStorage.getItem('preferred_language');
            if (savedLang && (savedLang === 'en' || savedLang === 'fr')) return savedLang;
            const browserLang = (navigator.language || navigator.userLanguage).substring(0, 2).toLowerCase();
            return browserLang === 'fr' ? 'fr' : 'en';
        }

        const lang = detectLanguage();
        const t = translations[lang];

        document.title = t.pageTitle;
        document.getElementById('title').textContent = t.title;
        document.getElementById('message').textContent = t.message;
        document.getElementById('btn-retry').textContent = t.retry;
        document.getElementById('btn-dashboard').textContent = t.dashboard;
        document.getElementById('btn-dashboard').href = '/' + lang + '/dashboard';
    </script>
</body>
</html>
