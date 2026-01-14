<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - WiFi network management system for administrators and network owners" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, captive portal, radius, management">
    <meta name="author" content="monsieur-wifi">
    <title>Login - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/page-auth.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->

    <!-- Add CSRF token meta tag for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
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
        
        .animated-bg .wifi-wave:nth-child(1) {
            top: 20%;
            left: 15%;
            width: 200px;
            height: 200px;
            animation-delay: 0s;
        }
        
        .animated-bg .wifi-wave:nth-child(2) {
            top: 70%;
            left: 80%;
            width: 300px;
            height: 300px;
            animation-delay: 2s;
        }
        
        .animated-bg .wifi-wave:nth-child(3) {
            top: 40%;
            left: 40%;
            width: 150px;
            height: 150px;
            animation-delay: 4s;
        }
        
        .animated-bg .wifi-wave:nth-child(4) {
            top: 80%;
            left: 20%;
            width: 180px;
            height: 180px;
            animation-delay: 6s;
        }
        
        .animated-bg .wifi-wave:nth-child(5) {
            top: 15%;
            left: 70%;
            width: 250px;
            height: 250px;
            animation-delay: 8s;
        }
        
        .animated-bg .wifi-wave:nth-child(6) {
            top: 50%;
            left: 60%;
            width: 180px;
            height: 180px;
            animation-delay: 10s;
        }
        
        .animated-bg .dot {
            position: absolute;
            background-color: rgba(115, 103, 240, 0.15);
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        
        .animated-bg .dot:nth-child(7) {
            top: 25%;
            left: 20%;
            width: 8px;
            height: 8px;
        }
        
        .animated-bg .dot:nth-child(8) {
            top: 60%;
            left: 85%;
            width: 12px;
            height: 12px;
        }
        
        .animated-bg .dot:nth-child(9) {
            top: 10%;
            left: 60%;
            width: 10px;
            height: 10px;
        }
        
        .animated-bg .dot:nth-child(10) {
            top: 45%;
            left: 30%;
            width: 6px;
            height: 6px;
        }
        
        .animated-bg .dot:nth-child(11) {
            top: 85%;
            left: 40%;
            width: 9px;
            height: 9px;
        }
        
        .animated-bg .dot:nth-child(12) {
            top: 35%;
            left: 85%;
            width: 7px;
            height: 7px;
        }
        
        /* Network Lines */
        .network-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, rgba(115, 103, 240, 0), rgba(115, 103, 240, 0.2), rgba(115, 103, 240, 0));
            animation: networkPulse 10s infinite ease-in-out;
            transform-origin: left center;
        }
        
        .network-line:nth-child(13) {
            top: 30%;
            left: 20%;
            width: 200px;
            transform: rotate(25deg);
            animation-delay: 0s;
        }
        
        .network-line:nth-child(14) {
            top: 60%;
            left: 40%;
            width: 180px;
            transform: rotate(-15deg);
            animation-delay: 2s;
        }
        
        .network-line:nth-child(15) {
            top: 20%;
            left: 50%;
            width: 250px;
            transform: rotate(-35deg);
            animation-delay: 4s;
        }
        
        .network-line:nth-child(16) {
            top: 80%;
            left: 65%;
            width: 150px;
            transform: rotate(10deg);
            animation-delay: 6s;
        }
        
        /* Signal Strength Bars */
        .signal-container {
            position: absolute;
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            height: 24px;
            gap: 2px;
            opacity: 0.15;
        }
        
        .signal-bar {
            width: 4px;
            background-color: #7367f0;
            border-radius: 1px;
        }
        
        .signal-container:nth-child(17) {
            top: 25%;
            left: 70%;
            transform: scale(0.8);
        }
        
        .signal-container:nth-child(18) {
            top: 75%;
            left: 25%;
            transform: scale(0.7) rotate(-15deg);
        }
        
        .signal-container:nth-child(19) {
            top: 40%;
            left: 85%;
            transform: scale(0.9) rotate(10deg);
        }
        
        /* Floating device icons */
        .device-icon {
            position: absolute;
            opacity: 0.2;
            color: #7367f0;
        }
        
        /* Card animations */
        .card {
            animation: cardFloat 6s ease-in-out infinite;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.17);
        }
        
        .auth-inner {
            position: relative;
            z-index: 1;
        }
        
        .brand-logo {
            transition: transform 0.3s ease;
        }
        
        .brand-logo:hover {
            transform: scale(1.05);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(115, 103, 240, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(115, 103, 240, 0.5);
        }
        
        /* Keyframes */
        @keyframes ripple {
            0% {
                width: 0px;
                height: 0px;
                opacity: 0.5;
            }
            100% {
                width: 500px;
                height: 500px;
                opacity: 0;
            }
        }
        
        @keyframes networkPulse {
            0%, 100% {
                opacity: 0;
                width: 0;
            }
            
            50% {
                opacity: 1;
                width: 100%;
            }
        }
        
        @keyframes signalPulse {
            0%, 100% {
                height: 6px;
            }
            50% {
                height: 18px;
            }
        }
        
        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        /* Blinking cursor animation */
        .typed-cursor {
            opacity: 1;
            animation: typedjsBlink 0.7s infinite;
        }
        
        @keyframes typedjsBlink {
            50% {
                opacity: 0.0;
            }
        }
        
        /* Form input animations */
        .form-control {
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(115, 103, 240, 0.2);
        }
        
        /* Language switcher styles */
        .language-switcher #languageDropdown {
            border-radius: 20px;
            transition: all 0.3s ease;
            min-width: 65px;
            font-weight: 600;
            font-size: 12px;
            padding: 0.25rem 0.5rem;
        }
        
        .language-switcher #languageDropdown:hover {
            transform: scale(1.05);
            border-color: #7367f0;
            color: #7367f0;
        }
        
        .language-switcher #languageDropdown:focus {
            box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
            border-color: #7367f0;
        }
        
        .language-switcher .dropdown-menu {
            border-radius: 10px;
            border: 1px solid rgba(115, 103, 240, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-width: 120px;
            padding: 0.5rem 0;
        }
        
        .language-switcher .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            font-size: 14px;
            border: none;
        }
        
        .language-switcher .dropdown-item:hover {
            background-color: rgba(115, 103, 240, 0.05);
            color: #7367f0;
            transform: translateX(2px);
        }
        
        .language-switcher .flag-icon {
            margin-right: 8px;
            font-size: 16px;
        }
        
        .language-switcher .lang-text {
            font-weight: 500;
        }
        
        .language-switcher #current-lang-flag {
            margin-right: 4px;
            font-size: 14px;
        }
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Background Animation -->
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
        
        <!-- Signal Strength Indicators -->
        <div class="signal-container">
            <div class="signal-bar bar-1"></div>
            <div class="signal-bar bar-2"></div>
            <div class="signal-bar bar-3"></div>
            <div class="signal-bar bar-4"></div>
        </div>
        <div class="signal-container">
            <div class="signal-bar bar-1"></div>
            <div class="signal-bar bar-2"></div>
            <div class="signal-bar bar-3"></div>
            <div class="signal-bar bar-4"></div>
        </div>
        <div class="signal-container">
            <div class="signal-bar bar-1"></div>
            <div class="signal-bar bar-2"></div>
            <div class="signal-bar bar-3"></div>
            <div class="signal-bar bar-4"></div>
        </div>
        
        <!-- Device Icons -->
        <!-- <div class="device-icon" id="laptop-icon"></div> -->
        <div class="device-icon" id="smartphone-icon"></div>
        <div class="device-icon" id="tablet-icon"></div>
        <div class="device-icon" id="router-icon"></div>
    </div>
    <!-- END: Background Animation -->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <!-- Login v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="javascript:void(0);" class="brand-logo">
                                        <img src="app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="36">
                                        <h2 class="brand-text text-primary ml-1">monsieur-wifi</h2>
                                    </a>
                                    <div class="language-switcher">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span id="current-lang-flag">🇺🇸</span>
                                                <span id="current-lang">EN</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown" id="languageDropdownMenu">
                                                <a class="dropdown-item language-option" href="javascript:void(0);" data-lang="en">
                                                    <span class="flag-icon">🇺🇸</span>
                                                    <span class="lang-text">English</span>
                                                </a>
                                                <a class="dropdown-item language-option" href="javascript:void(0);" data-lang="fr">
                                                    <span class="flag-icon">🇫🇷</span>
                                                    <span class="lang-text">Français</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="card-title mb-1" data-translate="welcome">Create Your Account 👋</h4>
                                <p class="card-text mb-2" data-translate="signInPrompt">Sign up to get started with monsieur-wifi</p>

                                <!-- Alert for showing messages -->
                                <div id="login-alert" class="alert alert-danger mt-1" style="display: none;"></div>
                                <div id="login-success" class="alert bg-transparent mt-1" style="display: none;"></div>

                                <!-- Registration form -->
                                <form class="auth-login-form mt-2" id="register-form" action="javascript:void(0);" method="post">
                                    <!-- Hidden field for design_id -->
                                    <input type="hidden" id="design_id" name="design_id" value="">
                                    
                                    <div class="form-group">
                                        <label for="register-name" class="form-label" data-translate="fullName">Full Name</label>
                                        <input type="text" class="form-control" id="register-name" name="name" data-placeholder="fullNamePlaceholder" placeholder="John Doe" required autofocus />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="register-email" class="form-label" data-translate="registerEmail">Email</label>
                                        <input type="email" class="form-control" id="register-email" name="email" data-placeholder="registerEmailPlaceholder" placeholder="john@example.com" required />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="register-password" class="form-label" data-translate="registerPassword">Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="register-password" name="password" data-placeholder="registerPasswordPlaceholder" placeholder="············" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="register-password-confirm" class="form-label" data-translate="confirmPassword">Confirm Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="register-password-confirm" name="password_confirmation" data-placeholder="confirmPasswordPlaceholder" placeholder="············" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-block" tabindex="4" id="register-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="register-spinner"></span>
                                        <span id="register-text" data-translate="registerButton">Register</span>
                                    </button>
                                </form>

                                <p class="text-center mt-2">
                                    <span data-translate="alreadyHaveAccount">Already have an account?</span>
                                    <a href="/login">
                                        <span data-translate="login">Login</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <!-- /Login v1 -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/page-auth-login.js"></script>
    <!-- END: Page JS-->

    <!-- Add this right after the Page JS scripts -->
    <script src="/assets/js/config.js?v=3"></script>

    <script>
        // Language support system
        const translations = {
            en: {
                pageTitle: 'Register - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - WiFi network management system for administrators and network owners',
                welcome: 'Create Your Account 👋',
                signInPrompt: 'Sign up to get started with monsieur-wifi',
                typingStrings: ['network management dashboard', 'WiFi control center', 'analytics platform'],
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                passwordLabel: 'Password',
                passwordPlaceholder: '············',
                rememberMe: 'Remember Me',
                signIn: 'Register',
                signingIn: 'Registering...',
                forgotPassword: 'Forgot your password?',
                resetPassword: 'Reset Password',
                loginSuccessful: 'Registration successful!',
                loginError: 'An error occurred during registration.',
                fullName: 'Full Name',
                fullNamePlaceholder: 'John Doe',
                registerEmail: 'Email',
                registerEmailPlaceholder: 'john@example.com',
                registerPassword: 'Password',
                registerPasswordPlaceholder: '············',
                confirmPassword: 'Confirm Password',
                confirmPasswordPlaceholder: '············',
                registerButton: 'Register',
                registering: 'Registering...',
                alreadyHaveAccount: 'Already have an account?',
                login: 'Login',
                langCode: 'EN',
                flag: '🇺🇸'
            },
            fr: {
                pageTitle: 'Inscription - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Système de gestion de réseaux WiFi pour administrateurs et propriétaires de réseaux',
                welcome: 'Créez Votre Compte 👋',
                signInPrompt: 'Inscrivez-vous pour commencer avec monsieur-wifi',
                typingStrings: ['tableau de bord de gestion réseau', 'centre de contrôle WiFi', 'plateforme d\'analytique'],
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                passwordLabel: 'Mot de passe',
                passwordPlaceholder: '············',
                rememberMe: 'Se souvenir de moi',
                signIn: 'S\'inscrire',
                signingIn: 'Inscription en cours...',
                forgotPassword: 'Mot de passe oublié?',
                resetPassword: 'Réinitialiser le mot de passe',
                loginSuccessful: 'Inscription réussie! Redirection vers le tableau de bord...',
                loginError: 'Une erreur s\'est produite lors de l\'inscription.',
                fullName: 'Nom Complet',
                fullNamePlaceholder: 'Jean Dupont',
                registerEmail: 'Email',
                registerEmailPlaceholder: 'jean@exemple.com',
                registerPassword: 'Mot de passe',
                registerPasswordPlaceholder: '············',
                confirmPassword: 'Confirmer le Mot de passe',
                confirmPasswordPlaceholder: '············',
                registerButton: 'S\'inscrire',
                registering: 'Inscription en cours...',
                alreadyHaveAccount: 'Vous avez déjà un compte?',
                login: 'Connexion',
                langCode: 'FR',
                flag: '🇫🇷'
            }
        };

        // Language detection and management
        function detectLanguage() {
            // Check for saved language preference first
            const savedLang = localStorage.getItem('preferred_language');
            if (savedLang && (savedLang === 'en' || savedLang === 'fr')) {
                return savedLang;
            }
            
            // Fallback to browser language detection
            const browserLang = navigator.language || navigator.userLanguage;
            const langCode = browserLang.substring(0, 2).toLowerCase();
            return langCode === 'fr' ? 'fr' : 'en'; // Default to English
        }

        function applyTranslations(lang) {
            const t = translations[lang];
            
            // Update page title and meta description
            document.title = t.pageTitle;
            $('#meta-description').attr('content', t.metaDescription);
            
            // Update static text elements
            $('.card-title').text(t.welcome);
            $('.card-text').text(t.signInPrompt);
            
            // Update all elements with data-translate attribute
            $('[data-translate]').each(function() {
                const key = $(this).attr('data-translate');
                if (t[key]) {
                    if ($(this).is('label')) {
                        // For labels, preserve the required asterisk if present
                        const html = $(this).html();
                        if (html.includes('<span class="text-danger">*</span>')) {
                            $(this).html(t[key] + ' <span class="text-danger">*</span>');
                        } else {
                            $(this).text(t[key]);
                        }
                    } else {
                        $(this).text(t[key]);
                    }
                }
            });
            
            // Update placeholders
            $('[data-placeholder]').each(function() {
                const key = $(this).attr('data-placeholder');
                if (t[key]) {
                    $(this).attr('placeholder', t[key]);
                }
            });
            
            // Update language dropdown button
            $('#current-lang').text(t.langCode);
            $('#current-lang-flag').text(t.flag);
            
            // Store current language for use in other functions
            window.currentLang = lang;
            window.currentTranslations = t;
            
            // Save language preference to localStorage
            localStorage.setItem('preferred_language', lang);
        }

        function switchLanguage(newLang) {
            applyTranslations(newLang);
            
            // Reinitialize Feather icons after language change
            if (feather) {
                feather.replace();
            }
            
            // Reinitialize typing animation with new language strings (if exists)
            if (window.typed && $('.typing-text').length) {
                window.typed.destroy();
                window.typed = new Typed('.typing-text', {
                    strings: window.currentTranslations.typingStrings,
                    typeSpeed: 50,
                    backSpeed: 30,
                    backDelay: 2000,
                    loop: true
                });
            }
        }

        // Initialize language on page load
        const currentLanguage = detectLanguage();
        
        $(document).ready(function() {
            console.log('Document ready - initializing register page');
            
            // Check if user session exists and clear it
            const existingToken = UserManager.getToken();
            const existingUser = UserManager.getUser();
            
            if (existingToken || existingUser) {
                console.log('Existing user session detected - clearing before registration');
                // Clear all authentication data using UserManager
                UserManager.clearAuth();
                console.log('Session cleared successfully - ready for new registration');
            }
            
            // Initialize language immediately
            applyTranslations(currentLanguage);
            
            // Language switcher - attach early with event delegation
            $(document).on('click', '.language-option', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Language option clicked!');
                
                const selectedLang = $(this).data('lang');
                console.log('Selected language:', selectedLang);
                console.log('Current language:', window.currentLang);
                
                if (selectedLang && selectedLang !== window.currentLang) {
                    console.log('Switching language to:', selectedLang);
                    switchLanguage(selectedLang);
                    
                    // Reinitialize Feather icons after language change
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                } else {
                    console.log('Same language selected or invalid language');
                }
                
                // Close dropdown manually
                $('#languageDropdownMenu').removeClass('show');
                $('#languageDropdown').attr('aria-expanded', 'false');
                
                return false;
            });
            
            console.log('Language switcher initialized');
            
            // Attach form handler immediately
            $('#register-form').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Register form submitted - handler called');
                
                // Validate passwords match
                const password = $('#register-password').val();
                const passwordConfirm = $('#register-password-confirm').val();
                console.log('Password validation:', { password: password.length, confirm: passwordConfirm.length });
                
                if (password !== passwordConfirm) {
                    const passwordMismatchMsg = window.currentLang === 'fr' ? 'Les mots de passe ne correspondent pas' : 'Passwords do not match';
                    $('#login-alert').text(passwordMismatchMsg).show();
                    console.log('Password mismatch');
                    return;
                }
                
                console.log('Password match - proceeding with registration');
                
                // Show spinner, hide text
                $('#register-spinner').removeClass('d-none');
                $('#register-text').text(window.currentTranslations ? window.currentTranslations.registering : 'Registering...');
                $('#register-btn').attr('disabled', true);
                $('#login-alert').hide();
                $('#login-success').hide();
                
                // Prepare form data
                var formData = {
                    name: $('#register-name').val(),
                    email: $('#register-email').val(),
                    password: password,
                    password_confirmation: passwordConfirm,
                };
                
                // Get design_id if present
                const designId = $('#design_id').val();
                
                // Function to proceed with registration
                function proceedWithRegistration(finalFormData) {
                    console.log('Form data prepared: ', finalFormData);
                    
                    // Determine which endpoint to use
                    var register_endpoint = '/api/auth/register';
                    if (finalFormData.design_id) {
                        register_endpoint = '/api/auth/register-with-design';
                    }
                    console.log('Making AJAX request to:', register_endpoint);
                    
                    $.ajax({
                        url: register_endpoint,
                        type: 'POST',
                        dataType: 'json',
                        data: finalFormData,
                        success: function(response) {
                            console.log('Registration successful');
                            console.log(response);
                            // Store user info and token using UserManager from config.js
                            UserManager.setToken(response.access_token);
                            
                            if (response.user) {
                                console.log("registered user: ", response.user);
                                UserManager.setUser(response.user);
                            }

                            if (response.user && response.user.profile_picture) {
                                localStorage.setItem('profile_picture', response.user.profile_picture);
                            }
                            
                            // Reset button
                            $('#register-spinner').addClass('d-none');
                            $('#register-text').text(window.currentTranslations ? window.currentTranslations.registerButton : 'Register');
                            $('#register-btn').attr('disabled', false);
                            
                            // Show success message
                            $('#login-success').html(
                                '<span class="text-success text-bold">' + (window.currentTranslations ? window.currentTranslations.loginSuccessful : 'Registration successful!') + '</span>'
                            ).show();

                            // Set a timeout to redirect to dashboard after showing the success message
                            setTimeout(function() {
                                const langPrefix = window.currentLang === 'fr' ? '/fr' : '/en';
                                // If there's a redirect URL from the response, use it
                                var redirectUrl = "";
                                if (response.url) {
                                    redirectUrl = langPrefix + response.url;
                                } else {
                                    redirectUrl = langPrefix + '/dashboard?status=registered';
                                }
                                // alert(redirectUrl);
                                window.location.href = redirectUrl;
                            }, 1500); // Redirect after 1.5 seconds
                        },
                        error: function(xhr, status, error) {
                            console.error('Registration failed:', status, error);
                            console.error('Response:', xhr);
                            
                            // Reset button
                            $('#register-spinner').addClass('d-none');
                            $('#register-text').text(window.currentTranslations ? window.currentTranslations.registerButton : 'Register');
                            $('#register-btn').attr('disabled', false);
                            
                            // Show error message
                            var errorMessage = 'An error occurred during registration.';
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                } else if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.errors) {
                                    // Handle validation errors
                                    const errors = Object.values(xhr.responseJSON.errors).flat();
                                    errorMessage = errors.join('<br>');
                                }
                            }
                            $('#login-alert').html(errorMessage).show();
                        }
                    });
                }
                
                // Check if design_id exists and verify it, otherwise proceed directly
                if (designId && designId.trim() !== '') {
                    console.log('Design ID found, verifying:', designId);
                    $.ajax({
                        url: '/api/temp-captive-portal-designs/' + designId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('Design ID verification response:', response);
                            if (response.success) {
                                console.log('Design ID is valid, adding to form data');
                                formData.design_id = designId;
                                proceedWithRegistration(formData);
                            } else {
                                // Reset button
                                $('#register-spinner').addClass('d-none');
                                $('#register-text').text(window.currentTranslations ? window.currentTranslations.registerButton : 'Register');
                                $('#register-btn').attr('disabled', false);
                                
                                const errorMsg = response.message || 'Invalid design ID';
                                $('#login-alert').html(errorMsg).show();
                                console.error('Design ID validation failed:', errorMsg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to verify design ID:', status, error);
                            console.error('Response:', xhr);
                            
                            // Reset button
                            $('#register-spinner').addClass('d-none');
                            $('#register-text').text(window.currentTranslations ? window.currentTranslations.registerButton : 'Register');
                            $('#register-btn').attr('disabled', false);
                            
                            // Proceed without design_id if verification fails
                            console.log('Design verification failed, proceeding without design_id');
                            proceedWithRegistration(formData);
                        }
                    });
                } else {
                    console.log('No design ID provided, proceeding with normal registration');
                    proceedWithRegistration(formData);
                }
                
                return false;
            });
            
            console.log('Form handler attached');
        });
        
        $(window).on('load', function() {
            console.log('Window loaded - initializing animations and translations');
            
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
                
                // Create device icons with Feather
                // $('#laptop-icon').html(feather.icons['laptop'].toSvg({ width: 24, height: 24 }));
                $('#smartphone-icon').html(feather.icons['smartphone'].toSvg({ width: 20, height: 20 }));
                $('#tablet-icon').html(feather.icons['tablet'].toSvg({ width: 22, height: 22 }));
                $('#router-icon').html(feather.icons['wifi'].toSvg({ width: 26, height: 26 }));
            }
            
            // Initialize typing animation with translated strings
            if (window.currentTranslations && window.currentTranslations.typingStrings) {
                window.typed = new Typed('.typing-text', {
                    strings: window.currentTranslations.typingStrings,
                    typeSpeed: 50,
                    backSpeed: 30,
                    backDelay: 2000,
                    loop: true
                });
            }
            
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Get design_id from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const designId = urlParams.get('design_id');
            if (designId) {
                $('#design_id').val(designId);
            }
            
            // Toggle password visibility
            $('.form-password-toggle .input-group-text').on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    passwordInput = $this.closest('.form-password-toggle').find('input');
                
                if (passwordInput.attr('type') === 'text') {
                    passwordInput.attr('type', 'password');
                    $this.find('svg').replaceWith(feather.icons['eye'].toSvg());
                } else if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    $this.find('svg').replaceWith(feather.icons['eye-off'].toSvg());
                }
            });

            // Create random position animations for dots
            $('.animated-bg .dot').each(function() {
                animateDot($(this));
            });

            // Position and animate device icons
            // animateDeviceIcon($('#laptop-icon'), 15, 25, 8000);
            animateDeviceIcon($('#smartphone-icon'), 65, 75, 10000);
            animateDeviceIcon($('#tablet-icon'), 40, 90, 12000);
            animateDeviceIcon($('#router-icon'), 80, 30, 9000);
            
            // Animate signal bars
            animateSignalBars();
            
            // Add login button click animation
            $('.btn-primary').on('mousedown', function() {
                $(this).addClass('scale-down');
            }).on('mouseup mouseleave', function() {
                $(this).removeClass('scale-down');
            });
            
            // Add event delegation for the show full token button
            $(document).on('click', '#show-full-token', function(e) {
                e.preventDefault();
                var fullToken = UserManager.getToken();
                
                $('.token-display').html(
                    '<div style="max-height: 100px; overflow-y: auto;">' + fullToken + '</div>'
                );
                
                $(this).text('Token Revealed').addClass('btn-secondary').removeClass('btn-outline-success').attr('disabled', true);
            });
            
            // Animation functions
            function animateDot(dot) {
                const xPos = Math.random() * 100;
                const yPos = Math.random() * 100;
                const duration = Math.random() * 15000 + 10000; // 10-25 seconds
                
                dot.animate({
                    top: yPos + '%',
                    left: xPos + '%'
                }, duration, 'linear', function() {
                    animateDot(dot); // Continuous animation
                });
            }
            
            function animateDeviceIcon(icon, topPos, leftPos, duration) {
                icon.css({
                    top: topPos + '%',
                    left: leftPos + '%'
                });
                
                const floatAnimation = function() {
                    const moveX = leftPos + (Math.random() * 10 - 5);
                    const moveY = topPos + (Math.random() * 10 - 5);
                    
                    icon.animate({
                        top: moveY + '%',
                        left: moveX + '%'
                    }, duration, 'linear', floatAnimation);
                };
                
                floatAnimation();
            }
            
            function animateSignalBars() {
                $('.signal-container').each(function(index) {
                    const container = $(this);
                    
                    container.find('.signal-bar').each(function(barIndex) {
                        const bar = $(this);
                        const height = 6 + (barIndex * 4); // Increasing heights
                        const delay = barIndex * 150; // Staggered animation
                        
                        bar.css({
                            height: height + 'px',
                            animationName: 'signalPulse',
                            animationDuration: '1.5s',
                            animationDelay: delay + 'ms',
                            animationIterationCount: 'infinite',
                            animationTimingFunction: 'ease-in-out'
                        });
                    });
                });
            }
            
            // Double-check and clear any remaining session data
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            
            if (token || user) {
                console.log('Session data still present in window.load - clearing again');
                UserManager.clearAuth();
                console.log('Final session clear completed');
            }
        });

        $(".input-group-append").on("click", function() {
            var $this = $(this);
            var passwordInput = $this.closest(".form-password-toggle").find("input");
            if (passwordInput.attr("type") === "text") {
                passwordInput.attr("type", "password");
            } else {
                passwordInput.attr("type", "text");
            }
        });
    </script>
</body>
<!-- END: Body-->

</html>