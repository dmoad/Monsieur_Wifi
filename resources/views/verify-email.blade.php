<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Email Verification" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, email verification">
    <meta name="author" content="monsieur-wifi">
    <title>Verify Email - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/MrWifi.png">
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

        @keyframes ripple {
            0% { width: 0px; height: 0px; opacity: 0.5; }
            100% { width: 500px; height: 500px; opacity: 0; }
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .verification-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .spinner-large {
            width: 48px;
            height: 48px;
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .success-icon { color: #28a745; }
        .error-icon { color: #dc3545; }
        .warning-icon { color: #ffc107; }
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
                                <a href="/login" class="brand-logo mb-2">
                                    <img src="assets/images/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="36">
                                    <h2 class="brand-text text-primary ml-1">monsieur-wifi</h2>
                                </a>

                                <!-- Loading State -->
                                <div id="loading-state">
                                    <div class="verification-icon">
                                        <div class="spinner-border spinner-large text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <h4 class="card-title mb-1" id="loading-title">Verifying your email...</h4>
                                    <p class="card-text mb-2" id="loading-text">Please wait while we verify your email address.</p>
                                </div>

                                <!-- Set Password State (admin-invite flow) -->
                                <div id="set-password-state" style="display: none;">
                                    <div class="status-icon" style="color:#7367f0;">&#128274;</div>
                                    <h4 class="card-title mb-1" id="set-password-title">Set Your Password</h4>
                                    <p class="card-text mb-2" id="set-password-subtitle">Choose a password to complete your account setup.</p>
                                    <form id="set-password-form" class="text-left mt-2">
                                        <div class="form-group">
                                            <label id="label-new-password">New Password</label>
                                            <input type="password" class="form-control" id="set-password-input" placeholder="Min. 8 characters" required minlength="8" />
                                        </div>
                                        <div class="form-group">
                                            <label id="label-confirm-password">Confirm Password</label>
                                            <input type="password" class="form-control" id="set-password-confirm" placeholder="Repeat password" required minlength="8" />
                                            <small class="text-danger d-none" id="set-password-mismatch">Passwords do not match.</small>
                                        </div>
                                        <div id="set-password-alert" class="alert alert-danger mt-1" style="display:none;"></div>
                                        <button type="submit" class="btn btn-primary btn-block mt-1" id="set-password-btn">
                                            <span class="spinner-border spinner-border-sm d-none mr-50" id="set-password-spinner"></span>
                                            <span id="set-password-btn-text">Set Password &amp; Verify</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- Success State -->
                                <div id="success-state" style="display: none;">
                                    <div class="status-icon success-icon">&#10004;</div>
                                    <h4 class="card-title mb-1 text-success" id="success-title">Email Verified!</h4>
                                    <p class="card-text mb-2" id="success-text">Your email has been verified successfully. Redirecting to dashboard...</p>
                                </div>

                                <!-- Error State -->
                                <div id="error-state" style="display: none;">
                                    <div class="status-icon error-icon">&#10060;</div>
                                    <h4 class="card-title mb-1 text-danger" id="error-title">Verification Failed</h4>
                                    <p class="card-text mb-2" id="error-text">The verification link is invalid or has expired.</p>
                                    <button type="button" class="btn btn-primary mt-2" id="resend-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" id="resend-spinner"></span>
                                        <span id="resend-text">Resend Verification Email</span>
                                    </button>
                                    <div id="resend-alert" class="alert alert-success mt-2" style="display: none;"></div>
                                </div>

                                <!-- Already Verified State -->
                                <div id="already-verified-state" style="display: none;">
                                    <div class="status-icon warning-icon">&#9888;</div>
                                    <h4 class="card-title mb-1" id="already-title">Already Verified</h4>
                                    <p class="card-text mb-2" id="already-text">Your email is already verified. You can login now.</p>
                                </div>

                                <p class="text-center mt-2">
                                    <a href="/login">
                                        <i data-feather="chevron-left"></i>
                                        <span id="back-to-login">Back to login</span>
                                    </a>
                                </p>
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
                verifying: 'Verifying your email...',
                pleaseWait: 'Please wait while we verify your email address.',
                verified: 'Email Verified!',
                verifiedText: 'Your email has been verified successfully. Redirecting to dashboard...',
                failed: 'Verification Failed',
                failedText: 'The verification link is invalid or has expired.',
                resend: 'Resend Verification Email',
                resending: 'Sending...',
                alreadyVerified: 'Already Verified',
                alreadyVerifiedText: 'Your email is already verified. You can login now.',
                backToLogin: 'Back to login',
                emailSent: 'Verification email sent! Please check your inbox.',
                waitMessage: 'Please wait before requesting another email.',
                setPasswordTitle: 'Set Your Password',
                setPasswordSubtitle: 'Choose a password to complete your account setup.',
                labelNewPassword: 'New Password',
                labelConfirmPassword: 'Confirm Password',
                passwordMismatch: 'Passwords do not match.',
                setPasswordBtn: 'Set Password & Verify',
                sending: 'Setting password...',
            },
            fr: {
                verifying: 'Vérification de votre email...',
                pleaseWait: 'Veuillez patienter pendant que nous vérifions votre adresse email.',
                verified: 'Email Vérifié !',
                verifiedText: 'Votre email a été vérifié avec succès. Redirection vers le tableau de bord...',
                failed: 'Échec de la Vérification',
                failedText: 'Le lien de vérification est invalide ou a expiré.',
                resend: 'Renvoyer l\'email de vérification',
                resending: 'Envoi...',
                alreadyVerified: 'Déjà Vérifié',
                alreadyVerifiedText: 'Votre email est déjà vérifié. Vous pouvez vous connecter.',
                backToLogin: 'Retour à la connexion',
                emailSent: 'Email de vérification envoyé ! Vérifiez votre boîte de réception.',
                waitMessage: 'Veuillez patienter avant de demander un autre email.',
                setPasswordTitle: 'Définir votre mot de passe',
                setPasswordSubtitle: 'Choisissez un mot de passe pour finaliser la création de votre compte.',
                labelNewPassword: 'Nouveau mot de passe',
                labelConfirmPassword: 'Confirmer le mot de passe',
                passwordMismatch: 'Les mots de passe ne correspondent pas.',
                setPasswordBtn: 'Définir le mot de passe et vérifier',
                sending: 'Enregistrement...',
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
        const token = getUrlParameter('token');
        const email = getUrlParameter('email');
        const setPassword = getUrlParameter('set_password') === '1';

        function handleVerifySuccess(response) {
            $('#loading-state').hide();
            $('#set-password-state').hide();

            if (response.already_verified) {
                $('#already-verified-state').show();
                setTimeout(() => window.location.href = '/login', 2000);
            } else {
                $('#success-state').show();
                if (response.access_token) {
                    localStorage.setItem('mrwifi_auth_token', response.access_token);
                    localStorage.setItem('mrwifi_user', JSON.stringify(response.user));
                }
                setTimeout(() => {
                    const redirectLang = lang === 'fr' ? 'fr' : 'en';
                    const urlParams = new URLSearchParams(window.location.search);
                    const hasDesign = urlParams.get('has_design') === '1' || localStorage.getItem('pending_design_redirect') === 'true';
                    localStorage.removeItem('pending_design_redirect');
                    if (hasDesign) {
                        window.location.href = '/' + redirectLang + '/captive-portals?from=registration';
                    } else {
                        window.location.href = '/' + redirectLang + '/dashboard';
                    }
                }, 2000);
            }
        }

        function handleVerifyError(xhr) {
            $('#loading-state').hide();
            $('#set-password-state').hide();
            $('#error-state').show();
            if (xhr.responseJSON && xhr.responseJSON.error) {
                $('#error-text').text(xhr.responseJSON.error);
            }
        }

        $(document).ready(function() {
            if (feather) feather.replace({ width: 14, height: 14 });

            // Apply translations
            $('#loading-title').text(t.verifying);
            $('#loading-text').text(t.pleaseWait);
            $('#success-title').text(t.verified);
            $('#success-text').text(t.verifiedText);
            $('#error-title').text(t.failed);
            $('#error-text').text(t.failedText);
            $('#resend-text').text(t.resend);
            $('#already-title').text(t.alreadyVerified);
            $('#already-text').text(t.alreadyVerifiedText);
            $('#back-to-login').text(t.backToLogin);
            $('#set-password-title').text(t.setPasswordTitle);
            $('#set-password-subtitle').text(t.setPasswordSubtitle);
            $('#label-new-password').text(t.labelNewPassword);
            $('#label-confirm-password').text(t.labelConfirmPassword);
            $('#set-password-mismatch').text(t.passwordMismatch);
            $('#set-password-btn-text').text(t.setPasswordBtn);

            if (!token || !email) {
                $('#loading-state').hide();
                $('#error-state').show();
                $('#error-text').text('Invalid verification link. Missing token or email.');
                return;
            }

            if (setPassword) {
                // Admin-invite flow: show password form first
                $('#loading-state').hide();
                $('#set-password-state').show();

                $('#set-password-form').on('submit', function(e) {
                    e.preventDefault();

                    const password = $('#set-password-input').val();
                    const confirm = $('#set-password-confirm').val();

                    if (password !== confirm) {
                        $('#set-password-mismatch').removeClass('d-none');
                        return;
                    }
                    $('#set-password-mismatch').addClass('d-none');
                    $('#set-password-alert').hide();

                    const $btn = $('#set-password-btn');
                    $('#set-password-spinner').removeClass('d-none');
                    $('#set-password-btn-text').text(t.sending);
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: '/api/auth/verify-email',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            token: token,
                            email: email,
                            password: password,
                            password_confirmation: confirm,
                        },
                        success: handleVerifySuccess,
                        error: function(xhr) {
                            $('#set-password-spinner').addClass('d-none');
                            $('#set-password-btn-text').text(t.setPasswordBtn);
                            $btn.prop('disabled', false);

                            let msg = 'An error occurred. Please try again.';
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.password && xhr.responseJSON.password[0]) {
                                    msg = xhr.responseJSON.password[0];
                                } else if (xhr.responseJSON.error) {
                                    msg = xhr.responseJSON.error;
                                }
                            }
                            $('#set-password-alert').text(msg).show();
                        }
                    });
                });
            } else {
                // Normal self-registration flow: auto-verify on page load
                $.ajax({
                    url: '/api/auth/verify-email',
                    type: 'POST',
                    dataType: 'json',
                    data: { token: token, email: email },
                    success: handleVerifySuccess,
                    error: handleVerifyError,
                });
            }

            // Resend verification email
            $('#resend-btn').on('click', function() {
                if (!email) {
                    $('#resend-alert').removeClass('alert-success').addClass('alert-danger')
                        .text('Email address not found in URL.').show();
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
                        $('#resend-alert').removeClass('alert-danger').addClass('alert-success')
                            .text(t.emailSent).show();
                    },
                    error: function(xhr) {
                        $('#resend-spinner').addClass('d-none');
                        $('#resend-text').text(t.resend);
                        $('#resend-btn').attr('disabled', false);

                        let errorMsg = 'An error occurred.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) errorMsg = xhr.responseJSON.error;
                            if (xhr.responseJSON.wait_seconds) {
                                errorMsg = t.waitMessage + ' (' + xhr.responseJSON.wait_seconds + 's)';
                            }
                        }
                        $('#resend-alert').removeClass('alert-success').addClass('alert-danger')
                            .text(errorMsg).show();
                    }
                });
            });
        });
    </script>
</body>
</html>
