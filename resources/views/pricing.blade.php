<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Monsieur WiFi - Choisissez votre abonnement" id="meta-description">
    <meta name="keywords" content="wifi, pricing, subscription, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title id="page-title">Tarifs - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <!-- END: Theme CSS-->

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background animation styles - same as login page */
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
                radial-gradient(at 40% 20%, rgba(99, 102, 241, 0.03) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(23, 193, 232, 0.03) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 100%, rgba(23, 193, 232, 0.03) 0px, transparent 50%);
        }

        .animated-bg .wifi-wave {
            position: absolute;
            border: 2px solid rgba(99, 102, 241, 0.05);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: ripple 15s linear infinite;
            opacity: 0;
        }

        .animated-bg .wifi-wave:nth-child(1) { top: 20%; left: 15%; width: 200px; height: 200px; animation-delay: 0s; }
        .animated-bg .wifi-wave:nth-child(2) { top: 70%; left: 80%; width: 300px; height: 300px; animation-delay: 2s; }
        .animated-bg .wifi-wave:nth-child(3) { top: 40%; left: 40%; width: 150px; height: 150px; animation-delay: 4s; }
        .animated-bg .wifi-wave:nth-child(4) { top: 80%; left: 20%; width: 180px; height: 180px; animation-delay: 6s; }
        .animated-bg .wifi-wave:nth-child(5) { top: 15%; left: 70%; width: 250px; height: 250px; animation-delay: 8s; }
        .animated-bg .wifi-wave:nth-child(6) { top: 50%; left: 60%; width: 180px; height: 180px; animation-delay: 10s; }

        .animated-bg .dot {
            position: absolute;
            background-color: rgba(99, 102, 241, 0.15);
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .animated-bg .dot:nth-child(7) { top: 25%; left: 20%; width: 8px; height: 8px; }
        .animated-bg .dot:nth-child(8) { top: 60%; left: 85%; width: 12px; height: 12px; }
        .animated-bg .dot:nth-child(9) { top: 10%; left: 60%; width: 10px; height: 10px; }
        .animated-bg .dot:nth-child(10) { top: 45%; left: 30%; width: 6px; height: 6px; }
        .animated-bg .dot:nth-child(11) { top: 85%; left: 40%; width: 9px; height: 9px; }
        .animated-bg .dot:nth-child(12) { top: 35%; left: 85%; width: 7px; height: 7px; }

        /* Network Lines */
        .network-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, rgba(99,102,241,0), rgba(99,102,241,0.2), rgba(99,102,241,0));
            animation: networkPulse 10s infinite ease-in-out;
            transform-origin: left center;
        }

        .network-line:nth-child(13) { top: 30%; left: 20%; width: 200px; transform: rotate(25deg); animation-delay: 0s; }
        .network-line:nth-child(14) { top: 60%; left: 40%; width: 180px; transform: rotate(-15deg); animation-delay: 2s; }
        .network-line:nth-child(15) { top: 20%; left: 50%; width: 250px; transform: rotate(-35deg); animation-delay: 4s; }
        .network-line:nth-child(16) { top: 80%; left: 65%; width: 150px; transform: rotate(10deg); animation-delay: 6s; }

        @keyframes ripple {
            0% { width: 0px; height: 0px; opacity: 0.5; }
            100% { width: 500px; height: 500px; opacity: 0; }
        }

        @keyframes networkPulse {
            0%, 100% { opacity: 0; width: 0; }
            50% { opacity: 1; width: 100%; }
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Header */
        .pricing-header {
            text-align: center;
            padding: 50px 20px 30px;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .brand-logo img {
            height: 45px;
            margin-right: 10px;
        }

        .brand-logo h2 {
            color: var(--mw-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .pricing-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .pricing-header p {
            font-size: 1rem;
            color: #666;
        }

        /* Pricing Cards Container */
        .pricing-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            padding: 30px 20px 50px;
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Pricing Card */
        .pricing-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 35px 30px;
            width: 100%;
            max-width: 480px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            animation: cardFloat 6s ease-in-out infinite;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pricing-card:nth-child(2) {
            animation-delay: 0.5s;
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 45px rgba(99, 102, 241, 0.15);
        }

        .pricing-card.popular::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--mw-primary);
        }

        .popular-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--mw-primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .plan-name {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--mw-primary);
            margin-bottom: 15px;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            margin-bottom: 5px;
        }

        .plan-price .amount {
            font-size: 3.5rem;
            font-weight: 700;
            color: #333;
            line-height: 1;
        }

        .plan-price .currency {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-left: 5px;
        }

        .plan-subtitle {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 8px;
        }

        .premium-intro {
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 10px;
            border-left: 3px solid var(--mw-primary);
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 25px 0;
        }

        .plan-features li {
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features li::before {
            content: "";
            display: inline-block;
            width: 20px;
            height: 20px;
            background: var(--mw-primary);
            border-radius: 50%;
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
            position: relative;
        }

        .plan-features li::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 11px;
            font-weight: bold;
            margin-left: -16px;
            margin-top: 4px;
        }

        .btn-subscribe {
            width: 100%;
            padding: 16px 30px;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-subscribe.primary {
            background: var(--mw-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
        }

        .btn-subscribe.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45);
        }

        .btn-subscribe.outline {
            background: white;
            color: var(--mw-primary);
            border: 2px solid var(--mw-primary);
        }

        .btn-subscribe.outline:hover {
            background: var(--mw-primary);
            color: white;
        }

        .btn-subscribe:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .loading-spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        .btn-subscribe.outline .loading-spinner {
            border-color: rgba(99, 102, 241, 0.3);
            border-top-color: var(--mw-primary);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Back Link */
        .back-link {
            text-align: center;
            padding: 20px 20px 50px;
            position: relative;
            z-index: 1;
        }

        .back-link a {
            color: var(--mw-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            color: #5e50ee;
            transform: translateX(-5px);
        }

        /* Timeline */
        .timeline-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 20px 10px;
            position: relative;
            z-index: 1;
        }

        .timeline {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 24px;
            left: calc(16.66% + 20px);
            right: calc(16.66% + 20px);
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .timeline-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .timeline-step.completed .timeline-circle {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .timeline-step.active .timeline-circle {
            background: var(--mw-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            animation: pulse-ring 2s ease-in-out infinite;
        }

        .timeline-step.pending .timeline-circle {
            background: #f0f0f0;
            color: #999;
            border: 2px solid #ddd;
        }

        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); }
            50% { box-shadow: 0 4px 25px rgba(99, 102, 241, 0.6); }
        }

        .timeline-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            max-width: 140px;
        }

        .timeline-sublabel {
            font-size: 0.75rem;
            color: #888;
            text-align: center;
            max-width: 140px;
            margin-top: 4px;
        }

        .timeline-step.pending .timeline-label {
            color: #999;
        }

        .timeline-step.pending .timeline-sublabel {
            color: #bbb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pricing-header h1 {
                font-size: 1.8rem;
            }

            .pricing-card {
                padding: 30px 25px;
            }

            .plan-price .amount {
                font-size: 3rem;
            }

            .pricing-container {
                padding: 20px 15px 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Background Animation -->
    <div class="animated-bg">
        <!-- WiFi Waves -->
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>

        <!-- Floating Dots -->
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>

        <!-- Network Connection Lines -->
        <div class="network-line"></div>
        <div class="network-line"></div>
        <div class="network-line"></div>
        <div class="network-line"></div>
    </div>

    <!-- Header -->
    <div class="pricing-header">
        <a href="/" class="brand-logo">
            <img src="assets/images/Mr-Wifi.PNG" alt="Monsieur WiFi">
            <h2>monsieur-wifi</h2>
        </a>

        <!-- Timeline -->
        <div class="timeline-container" style="padding-top:10px; padding-bottom:20px;">
            <div class="timeline">
                <div class="timeline-step completed">
                    <div class="timeline-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="timeline-label" id="timeline-label-1">Je design mon portail</div>
                    <div class="timeline-sublabel" id="timeline-sub-1">Portail captif personnalisé</div>
                </div>
                <div class="timeline-step active">
                    <div class="timeline-circle">2</div>
                    <div class="timeline-label" id="timeline-label-2">Je m'abonne</div>
                    <div class="timeline-sublabel" id="timeline-sub-2">Choix de l'offre et paiement</div>
                </div>
                <div class="timeline-step pending">
                    <div class="timeline-circle">3</div>
                    <div class="timeline-label" id="timeline-label-3">Je reçois ma borne</div>
                    <div class="timeline-sublabel" id="timeline-sub-3">Livraison + assistance mise en service</div>
                </div>
            </div>
        </div>

        <h1 id="header-title">Choisissez votre abonnement</h1>
        <p id="header-subtitle">Des solutions WiFi adaptées à vos besoins</p>

        <!-- Billing Toggle -->
        <div class="billing-toggle" style="display:inline-flex; align-items:center; gap:12px; margin-top:25px; background:rgba(255,255,255,0.95); padding:8px 20px; border-radius:50px; box-shadow:0 2px 15px rgba(0,0,0,0.08);">
            <span id="toggle-monthly" class="toggle-label active" style="font-size:0.9rem; font-weight:600; color:var(--mw-primary); cursor:pointer; transition:color 0.3s;">Mensuel</span>
            <label style="position:relative; display:inline-block; width:50px; height:28px; cursor:pointer;">
                <input type="checkbox" id="billing-toggle" style="opacity:0; width:0; height:0;">
                <span style="position:absolute; top:0; left:0; right:0; bottom:0; background:#ccc; border-radius:28px; transition:0.3s;"></span>
                <span id="toggle-knob" style="position:absolute; top:3px; left:3px; width:22px; height:22px; background:white; border-radius:50%; transition:0.3s; box-shadow:0 1px 3px rgba(0,0,0,0.2);"></span>
            </label>
            <span id="toggle-annual" class="toggle-label" style="font-size:0.9rem; font-weight:600; color:#999; cursor:pointer; transition:color 0.3s;">
                Annuel <span id="save-badge" style="background:linear-gradient(135deg,#28a745,#20c997); color:white; font-size:0.7rem; padding:2px 8px; border-radius:10px; margin-left:4px;">-15%</span>
            </span>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="pricing-loader" style="display:none; text-align:center; padding:80px 20px;">
        <div style="display:inline-block; width:40px; height:40px; border:3px solid rgba(99,102,241,0.2); border-top-color:var(--mw-primary); border-radius:50%; animation:spin 0.8s linear infinite;"></div>
        <p style="margin-top:15px; color:#888; font-size:0.95rem;" id="loader-text">Chargement...</p>
    </div>

    <!-- Pricing Cards -->
    <div class="pricing-container" id="pricing-cards" style="opacity: 0; transition: opacity 0.3s ease;">
        <!-- Standard Plan -->
        <div class="pricing-card" data-plan="standard">
            <h3 class="plan-name">Standard</h3>
            <div class="plan-price">
                <span class="amount" data-monthly="29" data-annual="299">29</span>
                <span class="currency">€</span>
            </div>
            <div class="plan-subtitle" id="price-subtitle-1">/mois, HT et sans engagement</div>

            <ul class="plan-features">
                <li id="feature-s1">Accès WiFi Clients</li>
                <li id="feature-s2">Authentification par email/SMS/Facebook/Twitter/Google</li>
                <li id="feature-s3">Garantie conformité RGPD</li>
                <li id="feature-s4">Portail personnalisable</li>
                <li id="feature-s5">Possibilité d'accès au WiFi et au menu par QR code</li>
                <li id="feature-s6">Gestion des horaires d'ouverture du hotspot</li>
                <li id="feature-s7">Gestion de la durée d'utilisation</li>
                <li id="feature-s8">1 borne WiFi Plug&Play</li>
                <li id="feature-s9">1 câble RJ45 de 3m pour la connexion à votre box</li>
                <li id="feature-s10">Garantie matériel incluse</li>
                <li id="feature-s11">Relancez vos visiteurs pour atteindre les 5 étoiles sur Facebook et TripAdvisor</li>
                <li id="feature-s12">Relancez chaque semaine vos clients avec des offres promotionnelles</li>
            </ul>

            <button class="btn-subscribe outline" data-plan="standard" data-price="{{ env('STRIPE_PRICE_STANDARD', env('STRIPE_PRICE_STARTER')) }}" data-price-monthly="{{ env('STRIPE_PRICE_STANDARD', env('STRIPE_PRICE_STARTER')) }}" data-price-annual="{{ env('STRIPE_PRICE_STANDARD_YEARLY') }}">
                <span class="loading-spinner d-none"></span>
                <span class="btn-text" id="btn-standard">Adhérez au Standard</span>
            </button>
        </div>

        <!-- Premium Plan -->
        <div class="pricing-card popular" data-plan="premium">
            <span class="popular-badge" id="popular-badge">Populaire</span>
            <h3 class="plan-name">Premium</h3>
            <div class="plan-price">
                <span class="amount" data-monthly="49" data-annual="499">49</span>
                <span class="currency">€</span>
            </div>
            <div class="plan-subtitle" id="price-subtitle-2">/mois, HT et sans engagement</div>

            <p class="premium-intro" id="premium-intro">Bénéficiez de l'offre Standard +</p>

            <ul class="plan-features">
                <li id="feature-p1">Validation, confirmation des e-mails et lien avec vos outils d'e-mailing</li>
                <li id="feature-p2">Assistance personnalisée</li>
            </ul>

            <button class="btn-subscribe primary" data-plan="premium" data-price="{{ env('STRIPE_PRICE_PREMIUM') }}" data-price-monthly="{{ env('STRIPE_PRICE_PREMIUM') }}" data-price-annual="{{ env('STRIPE_PRICE_PREMIUM_YEARLY') }}">
                <span class="loading-spinner d-none"></span>
                <span class="btn-text" id="btn-premium">Adhérez au Premium</span>
            </button>
        </div>
    </div>

    <!-- Back Link -->
    <div class="back-link">
        <a href="/login" id="back-text">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span id="back-label">Retour à la connexion</span>
        </a>
    </div>

    <!-- Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="/assets/js/config.js"></script>

    <script>
        // Translations
        const translations = {
            en: {
                pageTitle: 'Pricing - Monsieur WiFi',
                headerTitle: 'Choose your subscription',
                headerSubtitle: 'WiFi solutions tailored to your needs',
                priceSubtitle: '/month, excl. VAT, no commitment',
                premiumIntro: 'Get the Standard offer +',
                btnStandard: 'Subscribe to Standard',
                btnPremium: 'Subscribe to Premium',
                popularBadge: 'Popular',
                back: 'Back to login',
                backDashboard: 'Back to dashboard',
                processing: 'Processing...',
                loginRequired: 'Please login first to subscribe',
                toggleMonthly: 'Monthly',
                toggleAnnual: 'Annual',
                saveBadge: '-15%',
                priceSubtitleAnnual: '/year, excl. VAT, no commitment',
                timeline: {
                    label1: 'I design my portal',
                    sub1: 'Custom captive portal',
                    label2: 'I subscribe',
                    sub2: 'Choose plan & payment',
                    label3: 'I receive my device',
                    sub3: 'Delivery + setup assistance',
                },
                features: {
                    s1: 'WiFi Client Access',
                    s2: 'Authentication via email/SMS/Facebook/Twitter/Google',
                    s3: 'GDPR compliance guaranteed',
                    s4: 'Customizable portal',
                    s5: 'WiFi and menu access via QR code',
                    s6: 'Hotspot opening hours management',
                    s7: 'Usage time management',
                    s8: '1 Plug&Play WiFi hotspot',
                    s9: '1 RJ45 cable (3m) for box connection',
                    s10: 'Hardware warranty included',
                    s11: 'Reach 5 stars on Facebook and TripAdvisor',
                    s12: 'Weekly promotional offers to your customers',
                    p1: 'Email validation, confirmation and integration with your emailing tools',
                    p2: 'Personalized support'
                }
            },
            fr: {
                pageTitle: 'Tarifs - Monsieur WiFi',
                headerTitle: 'Choisissez votre abonnement',
                headerSubtitle: 'Des solutions WiFi adaptées à vos besoins',
                priceSubtitle: '/mois, HT et sans engagement',
                premiumIntro: 'Bénéficiez de l\'offre Standard +',
                btnStandard: 'Adhérez au Standard',
                btnPremium: 'Adhérez au Premium',
                popularBadge: 'Populaire',
                back: 'Retour à la connexion',
                backDashboard: 'Retour au tableau de bord',
                processing: 'Traitement...',
                loginRequired: 'Veuillez vous connecter pour vous abonner',
                toggleMonthly: 'Mensuel',
                toggleAnnual: 'Annuel',
                saveBadge: '-15%',
                priceSubtitleAnnual: '/an, HT et sans engagement',
                timeline: {
                    label1: 'Je design mon portail',
                    sub1: 'Portail captif personnalisé',
                    label2: 'Je m\'abonne',
                    sub2: 'Choix de l\'offre et paiement',
                    label3: 'Je reçois ma borne',
                    sub3: 'Livraison + assistance mise en service',
                },
                features: {
                    s1: 'Accès WiFi Clients',
                    s2: 'Authentification par email/SMS/Facebook/Twitter/Google',
                    s3: 'Garantie conformité RGPD',
                    s4: 'Portail personnalisable',
                    s5: 'Possibilité d\'accès au WiFi et au menu par QR code',
                    s6: 'Gestion des horaires d\'ouverture du hotspot',
                    s7: 'Gestion de la durée d\'utilisation',
                    s8: '1 borne WiFi Plug&Play',
                    s9: '1 câble RJ45 de 3m pour la connexion à votre box',
                    s10: 'Garantie matériel incluse',
                    s11: 'Relancez vos visiteurs pour atteindre les 5 étoiles sur Facebook et TripAdvisor',
                    s12: 'Relancez chaque semaine vos clients avec des offres promotionnelles',
                    p1: 'Validation, confirmation des e-mails et lien avec vos outils d\'e-mailing',
                    p2: 'Assistance personnalisée'
                }
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

        // Apply translations
        document.title = t.pageTitle;
        document.getElementById('header-title').textContent = t.headerTitle;
        document.getElementById('header-subtitle').textContent = t.headerSubtitle;
        document.getElementById('price-subtitle-1').textContent = t.priceSubtitle;
        document.getElementById('price-subtitle-2').textContent = t.priceSubtitle;
        document.getElementById('premium-intro').textContent = t.premiumIntro;
        document.getElementById('btn-standard').textContent = t.btnStandard;
        document.getElementById('btn-premium').textContent = t.btnPremium;
        document.getElementById('popular-badge').textContent = t.popularBadge;
        document.getElementById('back-label').textContent = t.back;

        // Update timeline
        document.getElementById('timeline-label-1').textContent = t.timeline.label1;
        document.getElementById('timeline-sub-1').textContent = t.timeline.sub1;
        document.getElementById('timeline-label-2').textContent = t.timeline.label2;
        document.getElementById('timeline-sub-2').textContent = t.timeline.sub2;
        document.getElementById('timeline-label-3').textContent = t.timeline.label3;
        document.getElementById('timeline-sub-3').textContent = t.timeline.sub3;

        // Update features
        Object.keys(t.features).forEach(key => {
            const el = document.getElementById('feature-' + key);
            if (el) el.textContent = t.features[key];
        });

        // Update back link based on auth status
        const token = localStorage.getItem('mrwifi_auth_token');
        const backLabel = document.getElementById('back-label');
        const backLink = document.getElementById('back-text');
        if (token) {
            backLabel.textContent = t.backDashboard;
            backLink.href = '/' + lang + '/dashboard';

            // Show loader while checking subscription
            const loader = document.getElementById('pricing-loader');
            loader.style.display = 'block';
            document.getElementById('loader-text').textContent = lang === 'fr' ? 'Chargement...' : 'Loading...';

            // Check if user already has a subscription
            fetch('/api/subscription/status', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('pricing-cards');
                if (data.success && data.has_subscription) {
                    // User already subscribed — replace pricing cards with message
                    const alreadyMsg = lang === 'fr'
                        ? {
                            title: 'Vous êtes déjà abonné',
                            text: 'Vous avez déjà un abonnement actif. Vous pouvez gérer votre abonnement depuis votre profil.',
                            btn: 'Aller au profil'
                          }
                        : {
                            title: 'You are already subscribed',
                            text: 'You already have an active subscription. You can manage your subscription from your profile.',
                            btn: 'Go to profile'
                          };
                    container.innerHTML = `
                        <div style="text-align:center; max-width:500px; background:rgba(255,255,255,0.95); border-radius:20px; padding:50px 40px; box-shadow:0 8px 32px rgba(31,38,135,0.12);">
                            <div style="width:80px;height:80px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 25px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <h2 style="font-size:1.6rem;font-weight:700;color:#333;margin-bottom:12px;">${alreadyMsg.title}</h2>
                            <p style="font-size:1rem;color:#666;margin-bottom:30px;line-height:1.6;">${alreadyMsg.text}</p>
                            <a href="/${lang}/profile" style="display:inline-block;background:var(--mw-primary);color:white;padding:14px 35px;border-radius:10px;text-decoration:none;font-weight:600;font-size:1rem;box-shadow:0 4px 15px rgba(99,102,241,0.35);">${alreadyMsg.btn}</a>
                        </div>
                    `;
                }
                loader.style.display = 'none';
                container.style.opacity = '1';
            })
            .catch(err => {
                console.error('Error checking subscription:', err);
                document.getElementById('pricing-loader').style.display = 'none';
                document.getElementById('pricing-cards').style.opacity = '1';
            });
        } else {
            backLabel.textContent = t.back;
            document.getElementById('pricing-cards').style.opacity = '1';
        }

        // Animate dots
        document.querySelectorAll('.animated-bg .dot').forEach(dot => {
            animateDot(dot);
        });

        function animateDot(dot) {
            const xPos = Math.random() * 100;
            const yPos = Math.random() * 100;
            const duration = Math.random() * 15000 + 10000;

            dot.style.transition = `top ${duration}ms linear, left ${duration}ms linear`;
            dot.style.top = yPos + '%';
            dot.style.left = xPos + '%';

            setTimeout(() => animateDot(dot), duration);
        }

        // Apply toggle translations
        document.getElementById('toggle-monthly').textContent = t.toggleMonthly;
        document.getElementById('toggle-annual').innerHTML = t.toggleAnnual + ' <span id="save-badge" style="background:linear-gradient(135deg,#28a745,#20c997); color:white; font-size:0.7rem; padding:2px 8px; border-radius:10px; margin-left:4px;">' + t.saveBadge + '</span>';

        // Billing toggle logic
        const billingToggle = document.getElementById('billing-toggle');
        const toggleKnob = document.getElementById('toggle-knob');
        const toggleMonthly = document.getElementById('toggle-monthly');
        const toggleAnnual = document.getElementById('toggle-annual');

        function updatePricing(isAnnual) {
            document.querySelectorAll('.plan-price .amount').forEach(el => {
                el.textContent = isAnnual ? el.dataset.annual : el.dataset.monthly;
            });

            document.getElementById('price-subtitle-1').textContent = isAnnual ? t.priceSubtitleAnnual : t.priceSubtitle;
            document.getElementById('price-subtitle-2').textContent = isAnnual ? t.priceSubtitleAnnual : t.priceSubtitle;

            // Update button price IDs
            document.querySelectorAll('.btn-subscribe').forEach(btn => {
                btn.dataset.price = isAnnual ? btn.dataset.priceAnnual : btn.dataset.priceMonthly;
            });

            // Toggle visual state
            if (isAnnual) {
                toggleKnob.style.left = '25px';
                toggleKnob.parentElement.querySelector('span:first-of-type').style.background = 'var(--mw-primary)';
                toggleMonthly.style.color = '#999';
                toggleMonthly.style.fontWeight = '500';
                toggleAnnual.style.color = 'var(--mw-primary)';
                toggleAnnual.style.fontWeight = '600';
            } else {
                toggleKnob.style.left = '3px';
                toggleKnob.parentElement.querySelector('span:first-of-type').style.background = '#ccc';
                toggleMonthly.style.color = 'var(--mw-primary)';
                toggleMonthly.style.fontWeight = '600';
                toggleAnnual.style.color = '#999';
                toggleAnnual.style.fontWeight = '500';
            }
        }

        billingToggle.addEventListener('change', function() {
            updatePricing(this.checked);
        });

        toggleMonthly.addEventListener('click', function() {
            billingToggle.checked = false;
            updatePricing(false);
        });

        toggleAnnual.addEventListener('click', function() {
            billingToggle.checked = true;
            updatePricing(true);
        });

        // Handle subscription button clicks
        document.querySelectorAll('.btn-subscribe').forEach(button => {
            button.addEventListener('click', async function() {
                const plan = this.dataset.plan;
                const priceId = this.dataset.price;
                const btnText = this.querySelector('.btn-text');
                const spinner = this.querySelector('.loading-spinner');

                // Check if price ID exists
                if (!priceId) {
                    alert('This plan is not yet available. Please contact support.');
                    return;
                }

                // Check if user is logged in
                const token = localStorage.getItem('mrwifi_auth_token');
                if (!token) {
                    alert(t.loginRequired);
                    window.location.href = '/login?redirect=/pricing';
                    return;
                }

                // Show loading state
                const originalText = btnText.textContent;
                btnText.textContent = t.processing;
                spinner.classList.remove('d-none');
                this.disabled = true;

                try {
                    const response = await fetch('/api/subscription/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + token,
                        },
                        body: JSON.stringify({
                            price_id: priceId,
                            plan_name: plan,
                        }),
                    });

                    const data = await response.json();

                    if (data.success && data.checkout_url) {
                        // Redirect to Stripe Checkout
                        window.location.href = data.checkout_url;
                    } else {
                        throw new Error(data.error || 'Failed to create checkout session');
                    }

                } catch (error) {
                    console.error('Checkout error:', error);
                    alert(error.message);

                    // Reset button
                    btnText.textContent = originalText;
                    spinner.classList.add('d-none');
                    this.disabled = false;
                }
            });
        });
    </script>
</body>
</html>
