<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Set New Password" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, password reset">
    <meta name="author" content="monsieur-wifi">
    <title>Set New Password - Monsieur WiFi</title>
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
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        
        .password-requirements {
            font-size: 12px;
            margin-top: 10px;
        }
        
        .requirement {
            color: #888;
            margin: 3px 0;
        }
        
        .requirement.met {
            color: #28a745;
        }
        
        .requirement.met i {
            color: #28a745;
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
                        <!-- Set New Password v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="/login" class="brand-logo">
                                        <img src="app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="36">
                                        <h2 class="brand-text text-primary ml-1">monsieur-wifi</h2>
                                    </a>
                                    <div class="language-switcher">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span id="current-lang-flag">🇺🇸</span>
                                                <span id="current-lang">EN</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
                                                <a class="dropdown-item language-option" href="#" data-lang="en">
                                                    <span class="flag-icon">🇺🇸</span>
                                                    <span class="lang-text">English</span>
                                                </a>
                                                <a class="dropdown-item language-option" href="#" data-lang="fr">
                                                    <span class="flag-icon">🇫🇷</span>
                                                    <span class="lang-text">Français</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="card-title mb-1">Set New Password 🔒</h4>
                                <p class="card-text mb-2">Your new password must be different from previously used passwords</p>

                                <!-- Alert for showing messages -->
                                <div id="new-password-alert" class="alert alert-danger mt-1" style="display: none;"></div>
                                <div id="new-password-success" class="alert alert-success mt-1" style="display: none;"></div>

                                <!-- New Password Form -->
                                <div class="auth-new-password-form mt-2" id="new-password-form">
                                    <div class="form-group">
                                        <label for="new-password" class="form-label">New Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="new-password" name="password" tabindex="1" placeholder="············" aria-describedby="new-password" autofocus />
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                        <div class="password-strength" id="password-strength"></div>
                                        <div class="password-requirements" id="password-requirements">
                                            <div class="requirement" id="req-length">
                                                <i data-feather="circle" style="width: 12px; height: 12px;"></i>
                                                <span>At least 8 characters</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm-password" class="form-label">Confirm Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="confirm-password" name="password_confirmation" tabindex="2" placeholder="············" aria-describedby="confirm-password" />
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block" tabindex="3" id="new-password-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="new-password-spinner"></span>
                                        <span id="new-password-text">Reset Password</span>
                                    </button>
                                </div>

                                <p class="text-center mt-2">
                                    <a href="/login">
                                        <i data-feather="chevron-left"></i>
                                        <span>Back to login</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <!-- /Set New Password v1 -->
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
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <script>
        // Language support system
        const translations = {
            en: {
                pageTitle: 'Set New Password - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Set a new password for your account',
                title: 'Set New Password 🔒',
                prompt: 'Your new password must be different from previously used passwords',
                newPasswordLabel: 'New Password',
                confirmPasswordLabel: 'Confirm Password',
                passwordPlaceholder: '············',
                resetPassword: 'Reset Password',
                resetting: 'Resetting...',
                backToLogin: 'Back to login',
                resetSuccess: 'Password reset successfully! Redirecting to login...',
                resetError: 'An error occurred. Please try again.',
                passwordMismatch: 'Passwords do not match',
                reqLength: 'At least 8 characters',
                langCode: 'EN',
                flag: '🇺🇸'
            },
            fr: {
                pageTitle: 'Définir un nouveau mot de passe - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Définir un nouveau mot de passe pour votre compte',
                title: 'Définir un nouveau mot de passe 🔒',
                prompt: 'Votre nouveau mot de passe doit être différent des mots de passe précédemment utilisés',
                newPasswordLabel: 'Nouveau mot de passe',
                confirmPasswordLabel: 'Confirmer le mot de passe',
                passwordPlaceholder: '············',
                resetPassword: 'Réinitialiser le mot de passe',
                resetting: 'Réinitialisation...',
                backToLogin: 'Retour à la connexion',
                resetSuccess: 'Mot de passe réinitialisé avec succès! Redirection vers la connexion...',
                resetError: 'Une erreur s\'est produite. Veuillez réessayer.',
                passwordMismatch: 'Les mots de passe ne correspondent pas',
                reqLength: 'Au moins 8 caractères',
                langCode: 'FR',
                flag: '🇫🇷'
            }
        };

        // Language detection and management
        function detectLanguage() {
            const savedLang = localStorage.getItem('preferred_language');
            if (savedLang && (savedLang === 'en' || savedLang === 'fr')) {
                return savedLang;
            }
            
            const browserLang = navigator.language || navigator.userLanguage;
            const langCode = browserLang.substring(0, 2).toLowerCase();
            return langCode === 'fr' ? 'fr' : 'en';
        }

        function applyTranslations(lang) {
            const t = translations[lang];
            
            document.title = t.pageTitle;
            $('#meta-description').attr('content', t.metaDescription);
            
            $('.card-title').text(t.title);
            $('.card-text').text(t.prompt);
            $('label[for="new-password"]').text(t.newPasswordLabel);
            $('label[for="confirm-password"]').text(t.confirmPasswordLabel);
            $('#new-password').attr('placeholder', t.passwordPlaceholder);
            $('#confirm-password').attr('placeholder', t.passwordPlaceholder);
            $('#new-password-text').text(t.resetPassword);
            $('p.text-center.mt-2 a span').text(t.backToLogin);
            $('#req-length span').text(t.reqLength);
            
            $('#current-lang').text(t.langCode);
            $('#current-lang-flag').text(t.flag);
            
            window.currentLang = lang;
            window.currentTranslations = t;
            
            localStorage.setItem('preferred_language', lang);
        }

        function switchLanguage(newLang) {
            applyTranslations(newLang);
        }

        // Get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        const currentLanguage = detectLanguage();
        
        $(window).on('load', function() {
            applyTranslations(currentLanguage);
            
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
                
                $('#smartphone-icon').html(feather.icons['smartphone'].toSvg({ width: 20, height: 20 }));
                $('#tablet-icon').html(feather.icons['tablet'].toSvg({ width: 22, height: 22 }));
                $('#router-icon').html(feather.icons['wifi'].toSvg({ width: 26, height: 26 }));
            }
            
            $('.language-option').on('click', function(e) {
                e.preventDefault();
                const selectedLang = $(this).data('lang');
                if (selectedLang !== window.currentLang) {
                    switchLanguage(selectedLang);
                }
                $('#languageDropdown').dropdown('hide');
            });
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Password strength checker
            $('#new-password').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                updatePasswordStrength(strength);
                checkPasswordRequirements(password);
            });
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 25;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 15;
                if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
                return Math.min(strength, 100);
            }
            
            function updatePasswordStrength(strength) {
                const strengthBar = $('#password-strength');
                strengthBar.css('width', strength + '%');
                
                if (strength < 40) {
                    strengthBar.css('background-color', '#dc3545');
                } else if (strength < 70) {
                    strengthBar.css('background-color', '#ffc107');
                } else {
                    strengthBar.css('background-color', '#28a745');
                }
            }
            
            function checkPasswordRequirements(password) {
                const lengthReq = $('#req-length');
                
                if (password.length >= 8) {
                    lengthReq.addClass('met');
                    lengthReq.find('i').replaceWith(feather.icons['check-circle'].toSvg({ width: 12, height: 12 }));
                } else {
                    lengthReq.removeClass('met');
                    lengthReq.find('svg').replaceWith(feather.icons['circle'].toSvg({ width: 12, height: 12 }));
                }
            }
            
            // Form submission
            $('#new-password-btn').on('click', function(e) {
                e.preventDefault();
                
                const password = $('#new-password').val();
                const confirmPassword = $('#confirm-password').val();
                const token = getUrlParameter('token');
                const email = getUrlParameter('email');
                
                // Basic validation
                if (!password || !confirmPassword) {
                    $('#new-password-alert').text('Please fill in all fields').show();
                    return;
                }
                
                if (password !== confirmPassword) {
                    $('#new-password-alert').text(window.currentTranslations.passwordMismatch).show();
                    return;
                }
                
                if (password.length < 8) {
                    $('#new-password-alert').text('Password must be at least 8 characters').show();
                    return;
                }
                
                // Show spinner
                $('#new-password-spinner').removeClass('d-none');
                $('#new-password-text').text(window.currentTranslations.resetting);
                $('#new-password-btn').attr('disabled', true);
                $('#new-password-alert').hide();
                $('#new-password-success').hide();
                
                // Make AJAX request
                $.ajax({
                    url: '/api/auth/reset-password',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        token: token,
                        email: email,
                        password: password,
                        password_confirmation: confirmPassword
                    },
                    success: function(response) {
                        console.log('Password reset successful');
                        
                        $('#new-password-spinner').addClass('d-none');
                        $('#new-password-text').text(window.currentTranslations.resetPassword);
                        $('#new-password-btn').attr('disabled', false);
                        
                        $('#new-password-success').text(window.currentTranslations.resetSuccess).show();
                        
                        // Redirect to login after 2 seconds
                        setTimeout(function() {
                            window.location.href = '/login';
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('#new-password-spinner').addClass('d-none');
                        $('#new-password-text').text(window.currentTranslations.resetPassword);
                        $('#new-password-btn').attr('disabled', false);
                        
                        var errorMessage = window.currentTranslations.resetError;
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.password) {
                                errorMessage = xhr.responseJSON.password[0];
                            }
                        }
                        $('#new-password-alert').text(errorMessage).show();
                    }
                });
            });
            
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

            // Animation functions
            $('.animated-bg .dot').each(function() {
                animateDot($(this));
            });

            animateDeviceIcon($('#smartphone-icon'), 65, 75, 10000);
            animateDeviceIcon($('#tablet-icon'), 40, 90, 12000);
            animateDeviceIcon($('#router-icon'), 80, 30, 9000);
            
            animateSignalBars();
            
            function animateDot(dot) {
                const xPos = Math.random() * 100;
                const yPos = Math.random() * 100;
                const duration = Math.random() * 15000 + 10000;
                
                dot.animate({
                    top: yPos + '%',
                    left: xPos + '%'
                }, duration, 'linear', function() {
                    animateDot(dot);
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
                        const height = 6 + (barIndex * 4);
                        const delay = barIndex * 150;
                        
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
        });
    </script>
</body>
<!-- END: Body-->

</html>

