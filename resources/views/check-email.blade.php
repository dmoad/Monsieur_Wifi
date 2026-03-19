<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Vérifiez votre email" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, email verification">
    <meta name="author" content="monsieur-wifi">
    <title id="page-title">Vérifiez votre email - Monsieur WiFi</title>
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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
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

        .card {
            animation: cardFloat 6s ease-in-out infinite;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.17);
        }

        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(115, 103, 240, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(115, 103, 240, 0.5);
        }

        .btn-outline-secondary {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        @keyframes ripple {
            0% { width: 0px; height: 0px; opacity: 0.5; }
            100% { width: 500px; height: 500px; opacity: 0; }
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .email-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .user-email {
            font-weight: 600;
            color: #7367f0;
        }

        .tips-list {
            text-align: left;
            margin: 20px auto;
            max-width: 300px;
        }

        .tips-list li {
            margin-bottom: 8px;
            color: #6e6b7b;
        }

        .divider {
            border-top: 1px solid #ebe9f1;
            margin: 20px 0;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- Background Animation -->
    <div class="animated-bg">
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
        <div class="wifi-wave"></div>
    </div>

    <!-- Content -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <div class="card mb-0">
                            <div class="card-body text-center">
                                <a href="/" class="brand-logo mb-2">
                                    <img src="app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="36">
                                    <h2 class="brand-text text-primary ml-1">monsieur-wifi</h2>
                                </a>

                                <!-- Email Icon -->
                                <div class="email-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#7367f0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>

                                <h3 class="card-title mb-1" id="title">Vérifiez votre boîte mail</h3>

                                <p class="card-text mb-1" id="subtitle">
                                    Nous avons envoyé un email de confirmation à :
                                </p>

                                <p class="user-email mb-2" id="user-email">votre@email.com</p>

                                <p class="card-text text-muted mb-2" id="instruction">
                                    Cliquez sur le lien dans l'email pour activer votre compte et accéder à votre tableau de bord.
                                </p>

                                <div class="divider"></div>

                                <p class="card-text mb-1" id="tips-title"><strong>Vous n'avez pas reçu l'email ?</strong></p>

                                <ul class="tips-list" id="tips-list">
                                    <li id="tip-1">Vérifiez votre dossier spam ou courrier indésirable</li>
                                    <li id="tip-2">Assurez-vous que l'adresse email est correcte</li>
                                    <li id="tip-3">Attendez quelques minutes, l'email peut prendre du temps</li>
                                </ul>

                                <button type="button" class="btn btn-primary btn-block mb-1" id="resend-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" id="resend-spinner"></span>
                                    <span id="resend-text">Renvoyer l'email de vérification</span>
                                </button>

                                <div id="resend-alert" class="alert mt-1" style="display: none;"></div>

                                <div class="divider"></div>

                                <a href="/login" class="btn btn-outline-secondary btn-block" id="login-link">
                                    <i data-feather="arrow-left" class="mr-50"></i>
                                    <span id="back-text">Retour à la connexion</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>

    <script>
        // Translations
        const translations = {
            en: {
                pageTitle: 'Check your email - Monsieur WiFi',
                title: 'Check your inbox',
                subtitle: 'We have sent a confirmation email to:',
                instruction: 'Click the link in the email to activate your account and access your dashboard.',
                tipsTitle: "Didn't receive the email?",
                tip1: 'Check your spam or junk folder',
                tip2: 'Make sure your email address is correct',
                tip3: 'Wait a few minutes, the email may take some time',
                resend: 'Resend verification email',
                resending: 'Sending...',
                back: 'Back to login',
                emailSent: 'Verification email sent! Please check your inbox.',
                waitMessage: 'Please wait before requesting another email.',
                errorMessage: 'An error occurred. Please try again.'
            },
            fr: {
                pageTitle: 'Vérifiez votre email - Monsieur WiFi',
                title: 'Vérifiez votre boîte mail',
                subtitle: 'Nous avons envoyé un email de confirmation à :',
                instruction: 'Cliquez sur le lien dans l\'email pour activer votre compte et accéder à votre tableau de bord.',
                tipsTitle: 'Vous n\'avez pas reçu l\'email ?',
                tip1: 'Vérifiez votre dossier spam ou courrier indésirable',
                tip2: 'Assurez-vous que l\'adresse email est correcte',
                tip3: 'Attendez quelques minutes, l\'email peut prendre du temps',
                resend: 'Renvoyer l\'email de vérification',
                resending: 'Envoi en cours...',
                back: 'Retour à la connexion',
                emailSent: 'Email de vérification envoyé ! Vérifiez votre boîte de réception.',
                waitMessage: 'Veuillez patienter avant de demander un autre email.',
                errorMessage: 'Une erreur est survenue. Veuillez réessayer.'
            }
        };

        function getUrlParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name) || '';
        }

        function detectLanguage() {
            const savedLang = localStorage.getItem('preferred_language');
            if (savedLang && (savedLang === 'en' || savedLang === 'fr')) return savedLang;
            const browserLang = (navigator.language || navigator.userLanguage).substring(0, 2).toLowerCase();
            return browserLang === 'fr' ? 'fr' : 'en';
        }

        const lang = detectLanguage();
        const t = translations[lang];
        const email = getUrlParameter('email') || localStorage.getItem('pending_verification_email') || '';

        $(document).ready(function() {
            if (feather) feather.replace({ width: 14, height: 14 });

            // Apply translations
            document.title = t.pageTitle;
            $('#title').text(t.title);
            $('#subtitle').text(t.subtitle);
            $('#instruction').text(t.instruction);
            $('#tips-title').html('<strong>' + t.tipsTitle + '</strong>');
            $('#tip-1').text(t.tip1);
            $('#tip-2').text(t.tip2);
            $('#tip-3').text(t.tip3);
            $('#resend-text').text(t.resend);
            $('#back-text').text(t.back);

            // Show user email
            if (email) {
                $('#user-email').text(email);
            } else {
                $('#user-email').text('---');
            }

            // Resend verification email
            $('#resend-btn').on('click', function() {
                if (!email) {
                    $('#resend-alert')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .text('Email address not found.')
                        .show();
                    return;
                }

                $('#resend-spinner').removeClass('d-none');
                $('#resend-text').text(t.resending);
                $('#resend-btn').attr('disabled', true);
                $('#resend-alert').hide();

                $.ajax({
                    url: '/api/auth/resend-verification',
                    type: 'POST',
                    dataType: 'json',
                    data: { email: email },
                    success: function(response) {
                        $('#resend-spinner').addClass('d-none');
                        $('#resend-text').text(t.resend);
                        $('#resend-btn').attr('disabled', false);
                        $('#resend-alert')
                            .removeClass('alert-danger')
                            .addClass('alert-success')
                            .text(t.emailSent)
                            .show();
                    },
                    error: function(xhr) {
                        $('#resend-spinner').addClass('d-none');
                        $('#resend-text').text(t.resend);
                        $('#resend-btn').attr('disabled', false);

                        let errorMsg = t.errorMessage;
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                            if (xhr.responseJSON.wait_seconds) {
                                errorMsg = t.waitMessage + ' (' + xhr.responseJSON.wait_seconds + 's)';
                            }
                        }
                        $('#resend-alert')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .text(errorMsg)
                            .show();
                    }
                });
            });
        });
    </script>
</body>
</html>
