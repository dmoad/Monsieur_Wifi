<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Password Reset" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, password reset">
    <meta name="author" content="monsieur-wifi">
    <title>Password Reset - Monsieur WiFi</title>
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
                        <!-- Password Reset v1 -->
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

                                <h4 class="card-title mb-1">Reset Your Password 🔐</h4>
                                <p class="card-text mb-2">Enter your email address and we'll send you a link to reset your password</p>

                                <!-- Alert for showing messages -->
                                <div id="reset-alert" class="alert alert-danger mt-1" style="display: none;"></div>
                                <div id="reset-success" class="alert alert-success mt-1" style="display: none;"></div>

                                <!-- Password Reset Form -->
                                <div class="auth-reset-password-form mt-2" id="reset-form">
                                    <div class="form-group">
                                        <label for="reset-email" class="form-label">Email</label>
                                        <input type="text" class="form-control" id="reset-email" name="email" placeholder="admin@mrwifi.com" aria-describedby="reset-email" tabindex="1" autofocus />
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block" tabindex="2" id="reset-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="reset-spinner"></span>
                                        <span id="reset-text">Send Reset Link</span>
                                    </button>
                                </div>

                                <p class="text-center mt-2">
                                    <span>Remember your password?</span>
                                    <a href="/login">
                                        <span>Sign in</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <!-- /Password Reset v1 -->
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
                pageTitle: 'Password Reset - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Reset your password',
                resetTitle: 'Reset Your Password 🔐',
                resetPrompt: 'Enter your email address and we\'ll send you a link to reset your password',
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                sendResetLink: 'Send Reset Link',
                sending: 'Sending...',
                rememberPassword: 'Remember your password?',
                signIn: 'Sign in',
                resetSuccess: 'Password reset link sent! Please check your email.',
                resetError: 'An error occurred. Please try again.',
                langCode: 'EN',
                flag: '🇺🇸'
            },
            fr: {
                pageTitle: 'Réinitialisation du mot de passe - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Réinitialisez votre mot de passe',
                resetTitle: 'Réinitialisez votre mot de passe 🔐',
                resetPrompt: 'Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe',
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                sendResetLink: 'Envoyer le lien',
                sending: 'Envoi en cours...',
                rememberPassword: 'Vous vous souvenez de votre mot de passe?',
                signIn: 'Se connecter',
                resetSuccess: 'Lien de réinitialisation envoyé! Veuillez vérifier votre e-mail.',
                resetError: 'Une erreur s\'est produite. Veuillez réessayer.',
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
            $('.card-title').text(t.resetTitle);
            $('.card-text').text(t.resetPrompt);
            $('label[for="reset-email"]').text(t.emailLabel);
            $('#reset-email').attr('placeholder', t.emailPlaceholder);
            $('#reset-text').text(t.sendResetLink);
            $('p.text-center.mt-2 > span').first().text(t.rememberPassword);
            $('a[href="/login"] span').text(t.signIn);
            
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
        }

        // Initialize language on page load
        const currentLanguage = detectLanguage();
        
        $(window).on('load', function() {
            // Apply translations before other initializations
            applyTranslations(currentLanguage);
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
                
                // Create device icons with Feather
                $('#smartphone-icon').html(feather.icons['smartphone'].toSvg({ width: 20, height: 20 }));
                $('#tablet-icon').html(feather.icons['tablet'].toSvg({ width: 22, height: 22 }));
                $('#router-icon').html(feather.icons['wifi'].toSvg({ width: 26, height: 26 }));
            }
            
            // Language dropdown event handlers
            $('.language-option').on('click', function(e) {
                e.preventDefault();
                const selectedLang = $(this).data('lang');
                if (selectedLang !== window.currentLang) {
                    switchLanguage(selectedLang);
                }
                // Close dropdown
                $('#languageDropdown').dropdown('hide');
            });
            
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Form validation and submission
            $('#reset-btn').on('click', function(e) {
                e.preventDefault();
                console.log('Reset button clicked');
                
                // Show spinner, hide text
                $('#reset-spinner').removeClass('d-none');
                $('#reset-text').text(window.currentTranslations.sending);
                $('#reset-btn').attr('disabled', true);
                $('#reset-alert').hide();
                $('#reset-success').hide();
                
                // Get form data
                var formData = {
                    email: $('#reset-email').val()
                };
                
                // Make AJAX request to password reset endpoint
                $.ajax({
                    url: '/api/auth/password-reset',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        console.log('Reset email sent successfully');
                        console.log(response);
                        
                        // Reset button
                        $('#reset-spinner').addClass('d-none');
                        $('#reset-text').text(window.currentTranslations.sendResetLink);
                        $('#reset-btn').attr('disabled', false);
                        
                        // Show success message
                        $('#reset-success').text(window.currentTranslations.resetSuccess).show();
                        
                        // Clear the email field
                        $('#reset-email').val('');
                    },
                    error: function(xhr) {
                        // Reset button
                        $('#reset-spinner').addClass('d-none');
                        $('#reset-text').text(window.currentTranslations.sendResetLink);
                        $('#reset-btn').attr('disabled', false);
                        
                        // Show error message
                        var errorMessage = window.currentTranslations.resetError;
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.email) {
                                errorMessage = xhr.responseJSON.email[0];
                            }
                        }
                        $('#reset-alert').text(errorMessage).show();
                    }
                });
            });
            
            // Allow form submission on Enter key
            $('#reset-email').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $('#reset-btn').click();
                }
            });

            // Create random position animations for dots
            $('.animated-bg .dot').each(function() {
                animateDot($(this));
            });

            // Position and animate device icons
            animateDeviceIcon($('#smartphone-icon'), 65, 75, 10000);
            animateDeviceIcon($('#tablet-icon'), 40, 90, 12000);
            animateDeviceIcon($('#router-icon'), 80, 30, 9000);
            
            // Animate signal bars
            animateSignalBars();
            
            // Add reset button click animation
            $('.btn-primary').on('mousedown', function() {
                $(this).addClass('scale-down');
            }).on('mouseup mouseleave', function() {
                $(this).removeClass('scale-down');
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
        });
    </script>
</body>
<!-- END: Body-->

</html>
