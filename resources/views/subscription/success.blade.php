<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Monsieur WiFi - Paiement réussi">
    <title id="page-title">Paiement réussi - Monsieur WiFi</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        /* Background animation styles */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background-color: #f8f8f8;
            background-image:
                radial-gradient(at 40% 20%, rgba(115, 103, 240, 0.03) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(23, 193, 232, 0.03) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(115, 103, 240, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 100%, rgba(23, 193, 232, 0.03) 0px, transparent 50%);
        }

        .animated-bg .wifi-wave {
            position: absolute;
            border: 2px solid rgba(115, 103, 240, 0.05);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: ripple 15s linear infinite;
            opacity: 0;
        }

        .animated-bg .wifi-wave:nth-child(1) { top: 20%; left: 15%; width: 200px; height: 200px; animation-delay: 0s; }
        .animated-bg .wifi-wave:nth-child(2) { top: 70%; left: 80%; width: 300px; height: 300px; animation-delay: 2s; }
        .animated-bg .wifi-wave:nth-child(3) { top: 40%; left: 40%; width: 150px; height: 150px; animation-delay: 4s; }
        .animated-bg .wifi-wave:nth-child(4) { top: 80%; left: 20%; width: 180px; height: 180px; animation-delay: 6s; }

        @keyframes ripple {
            0% { width: 0px; height: 0px; opacity: 0.5; }
            100% { width: 500px; height: 500px; opacity: 0; }
        }

        .success-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            position: relative;
            z-index: 10;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .success-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-dashboard {
            display: inline-block;
            background: linear-gradient(135deg, #7367f0, #9e95f5);
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(115, 103, 240, 0.35);
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(115, 103, 240, 0.4);
            color: white;
        }

        /* Timeline */
        .timeline-container { max-width:700px; margin:0 auto 30px; padding:0 20px; }
        .timeline { display:flex; align-items:flex-start; justify-content:center; position:relative; }
        .timeline::before { content:''; position:absolute; top:24px; left:calc(16.66% + 20px); right:calc(16.66% + 20px); height:3px; background:#e0e0e0; z-index:0; }
        .timeline-step { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; z-index:1; }
        .timeline-circle { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; margin-bottom:12px; }
        .timeline-step.completed .timeline-circle { background:linear-gradient(135deg,#28a745,#20c997); color:white; box-shadow:0 4px 12px rgba(40,167,69,0.3); }
        .timeline-step.active .timeline-circle { background:linear-gradient(135deg,#7367f0,#9e95f5); color:white; box-shadow:0 4px 15px rgba(115,103,240,0.4); animation:pulse-ring 2s ease-in-out infinite; }
        .timeline-step.pending .timeline-circle { background:#f0f0f0; color:#999; border:2px solid #ddd; }
        @keyframes pulse-ring { 0%,100%{box-shadow:0 4px 15px rgba(115,103,240,0.4);} 50%{box-shadow:0 4px 25px rgba(115,103,240,0.6);} }
        .timeline-label { font-size:0.85rem; font-weight:600; color:#333; text-align:center; max-width:140px; }
        .timeline-sublabel { font-size:0.75rem; color:#888; text-align:center; max-width:140px; margin-top:4px; }

        /* Confetti styles */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -20px;
            z-index: 1000;
            opacity: 0;
            animation: confetti-fall linear forwards;
        }

        .confetti.square {
            border-radius: 0;
        }

        .confetti.circle {
            border-radius: 50%;
        }

        .confetti.ribbon {
            width: 8px;
            height: 20px;
            border-radius: 2px;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 1;
            }
            25% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg) scale(0.5);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Background Animation -->
    <div class="animated-bg">
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
    </div>

    <!-- Timeline -->
    <div class="timeline-container" style="position:relative; z-index:10;">
        <div class="timeline">
            <div class="timeline-step completed">
                <div class="timeline-circle">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="timeline-label" id="timeline-label-1">Je design mon portail</div>
                <div class="timeline-sublabel" id="timeline-sub-1">Portail captif personnalisé</div>
            </div>
            <div class="timeline-step completed">
                <div class="timeline-circle">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="timeline-label" id="timeline-label-2">Je m'abonne</div>
                <div class="timeline-sublabel" id="timeline-sub-2">Choix de l'offre et paiement</div>
            </div>
            <div class="timeline-step active">
                <div class="timeline-circle">3</div>
                <div class="timeline-label" id="timeline-label-3">Je reçois ma borne</div>
                <div class="timeline-sublabel" id="timeline-sub-3">Livraison + assistance mise en service</div>
            </div>
        </div>
    </div>

    <div class="success-card">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h1 class="success-title" id="title">Paiement réussi !</h1>
        <p class="success-message" id="message">
            Félicitations ! Votre abonnement est maintenant actif.
            Vous pouvez commencer à utiliser toutes les fonctionnalités de votre plan.
        </p>
        <a href="/en/dashboard" class="btn-dashboard" id="btn-dashboard">Accéder au tableau de bord</a>
    </div>

    <script>
        const translations = {
            en: {
                pageTitle: 'Payment successful - Monsieur WiFi',
                title: 'Payment successful!',
                message: 'Congratulations! Your subscription is now active. You can start using all the features of your plan.',
                button: 'Go to dashboard',
                timeline: { label1: 'I design my portal', sub1: 'Custom captive portal', label2: 'I subscribe', sub2: 'Choose plan & payment', label3: 'I receive my device', sub3: 'Delivery + setup assistance' }
            },
            fr: {
                pageTitle: 'Paiement réussi - Monsieur WiFi',
                title: 'Paiement réussi !',
                message: 'Félicitations ! Votre abonnement est maintenant actif. Vous pouvez commencer à utiliser toutes les fonctionnalités de votre plan.',
                button: 'Accéder au tableau de bord',
                timeline: { label1: 'Je design mon portail', sub1: 'Portail captif personnalisé', label2: 'Je m\'abonne', sub2: 'Choix de l\'offre et paiement', label3: 'Je reçois ma borne', sub3: 'Livraison + assistance mise en service' }
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
        document.getElementById('btn-dashboard').textContent = t.button;
        document.getElementById('btn-dashboard').href = '/' + lang + '/dashboard';

        // Update timeline
        document.getElementById('timeline-label-1').textContent = t.timeline.label1;
        document.getElementById('timeline-sub-1').textContent = t.timeline.sub1;
        document.getElementById('timeline-label-2').textContent = t.timeline.label2;
        document.getElementById('timeline-sub-2').textContent = t.timeline.sub2;
        document.getElementById('timeline-label-3').textContent = t.timeline.label3;
        document.getElementById('timeline-sub-3').textContent = t.timeline.sub3;

        // Confetti effect
        function createConfetti() {
            const colors = ['#7367f0', '#28a745', '#ffc107', '#17a2b8', '#e83e8c', '#6610f2', '#fd7e14'];
            const shapes = ['square', 'circle', 'ribbon'];
            const confettiCount = 80;

            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    const shape = shapes[Math.floor(Math.random() * shapes.length)];
                    confetti.className = 'confetti ' + shape;
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDuration = (Math.random() * 2 + 3) + 's';
                    confetti.style.animationDelay = '0s';

                    document.body.appendChild(confetti);

                    // Remove confetti after animation
                    setTimeout(() => {
                        confetti.remove();
                    }, 5000);
                }, i * 50); // Stagger the confetti creation
            }
        }

        // Start confetti on page load
        createConfetti();

        // Create another wave of confetti after 2 seconds
        setTimeout(createConfetti, 2000);

        // Confirm subscription with backend
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sessionId = urlParams.get('session_id');
            const token = localStorage.getItem('mrwifi_auth_token');

            if (sessionId && token) {
                fetch('/api/subscription/confirm', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ session_id: sessionId })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        console.log('Subscription confirmed');
                    } else {
                        console.error('Subscription confirmation failed:', data.error);
                    }
                })
                .catch(err => console.error('Error confirming subscription:', err));
            }
        })();
    </script>
</body>
</html>
