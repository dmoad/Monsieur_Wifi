<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">WiFi Login with Email</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/fonts/font-awesome/css/font-awesome.min.css">
    <style>
        :root {
            --theme-color: #7367f0;
            --theme-color-light: #7367f015;
            --theme-color-dark: #5e50ee;
        }

        body {
            min-height: 100vh;
            background-image: url('/assets/images/captive-portal/images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .portal-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 420px;
            display: flex;
            flex-direction: column;
            padding: 2rem;
            margin: 0 auto;
        }

        .location-logo {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .location-logo img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .welcome-text {
            text-align: center;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .login-button {
            background-color: var(--theme-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-button:hover {
            background-color: var(--theme-color-dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
        }

        .otp-input {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            border-radius: 8px;
        }

        .resend-container {
            text-align: center;
            margin-top: 0.75rem;
            font-size: 0.85rem;
            color: #666;
        }

        .resend-link {
            color: var(--theme-color);
            cursor: pointer;
            text-decoration: none;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .brand-logo {
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            width: 100%;
            max-width: 200px;
        }

        .brand-logo img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .footer {
            margin-top: 3rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
            text-align: center;
        }

        .terms {
            font-size: 0.8rem;
            color: #666;
        }

        .terms a {
            color: var(--theme-color);
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .language-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 4px;
            opacity: 0.7;
            transition: opacity 0.2s;
            z-index: 1000;
        }

        .language-switcher:hover {
            opacity: 1;
        }

        .language-btn {
            padding: 6px 12px;
            border: none;
            background: transparent;
            color: #999;
            cursor: pointer;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 400;
            transition: all 0.2s;
        }

        .language-btn:hover {
            color: #666;
            background: rgba(0, 0, 0, 0.05);
        }

        .language-btn.active {
            color: #3B82F6;
            background: rgba(59, 130, 246, 0.1);
        }

        @media (max-width: 576px) {
            .portal-container { padding: 1.5rem; }
            .location-logo    { height: 60px; }
            .brand-logo       { height: 24px; }
        }
    </style>
</head>
<body>
    <div class="language-switcher">
        <button class="language-btn" data-lang="en">English</button>
        <button class="language-btn" data-lang="fr">Français</button>
    </div>

    <div class="portal-container">
        <!-- Header with Location Logo -->
        <div class="text-center">
            <div class="location-logo mx-auto" id="location-logo">
                <div style="background: #f0f0f0; width: 100%; height: 100%; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666;">
                    Location Logo
                </div>
            </div>
        </div>

        <!-- Welcome Text -->
        <div class="welcome-text" id="welcome-text" data-i18n-default="welcomeText">
            Please enter your email address to receive a one-time verification code and connect to our WiFi network.
        </div>

        <!-- Alert for messages -->
        <div id="alert-container" style="display: none;"></div>

        <!-- Step 1: Email form -->
        <div id="email-step">
            <form id="email-form">
                <div class="form-group">
                    <label for="email" data-i18n="emailLabel">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        data-i18n-placeholder="emailPlaceholder"
                        placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label for="name" data-i18n="nameLabel">Name (Optional)</label>
                    <input type="text" class="form-control" id="name" name="name"
                        data-i18n-placeholder="namePlaceholder"
                        placeholder="Enter your name">
                </div>
                <button type="submit" class="login-button" id="send-code-button" data-i18n="sendCode">
                    Send Verification Code
                </button>
            </form>
        </div>

        <!-- Step 2: OTP verification form (hidden initially) -->
        <div id="otp-step" style="display: none;">
            <div class="sent-to-bar" id="sent-to-bar" style="
                display: flex; align-items: center; justify-content: center; gap: 6px;
                font-size: 0.85rem; color: #555; margin-bottom: 1.25rem;
                background: #f4f6f9; border-radius: 8px; padding: 8px 12px;">
                <span data-i18n="sentTo">Sent to</span>
                <strong id="sent-to-email" style="color: #333;"></strong>
                <span style="color: #ccc;">·</span>
                <a href="#" id="edit-email-link" style="color: var(--theme-color); text-decoration: none; font-weight: 500;" data-i18n="edit">Edit</a>
            </div>
            <form id="verify-otp-form">
                <div class="form-group">
                    <label for="otp-1" data-i18n="verificationCodeLabel">Verification Code</label>
                    <div class="d-flex justify-content-between">
                        <input type="text" class="form-control text-center otp-input" style="width: 22%; margin-right: 4%;" id="otp-1" maxlength="1" required>
                        <input type="text" class="form-control text-center otp-input" style="width: 22%; margin-right: 4%;" id="otp-2" maxlength="1" required>
                        <input type="text" class="form-control text-center otp-input" style="width: 22%; margin-right: 4%;" id="otp-3" maxlength="1" required>
                        <input type="text" class="form-control text-center otp-input" style="width: 22%;"                   id="otp-4" maxlength="1" required>
                    </div>
                    <input type="hidden" id="otp" name="otp" value="">
                </div>
                <div class="resend-container">
                    <span id="timer-text">
                        <span data-i18n="requestAgainIn">Request code again in</span>
                        <span id="timer">05:00</span>
                    </span>
                    <div id="resend-action" style="display: none;">
                        <span>
                            <span data-i18n="didntReceive">Didn't receive the code?</span>
                            <a class="resend-link" id="resend-link" data-i18n="resend">Resend</a>
                        </span>
                    </div>
                </div>
                <button type="submit" class="login-button" id="verify-otp-button" data-i18n-default="connectButton">
                    Connect to WiFi
                </button>
            </form>
        </div>

        <!-- Footer with Brand Logo and Terms -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-links" style="display: none; margin-bottom: 0.5rem;">
                <!-- Terms links inserted by JS when show_terms is enabled -->
            </div>
            <div class="terms" id="terms-text" data-i18n="footer">
                Powered by Monsieur WiFi
            </div>
        </div>
    </div>

    <!-- Terms modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel" data-i18n="termsTitle">Terms of Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"><p id="terms-content"></p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-i18n="close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel" data-i18n="privacyTitle">Privacy Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"><p id="privacy-content"></p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-i18n="close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>

    <script>
        // ── i18n ──────────────────────────────────────────────────────────────
        const translations = {
            en: {
                welcomeText:          'Please enter your email address to receive a one-time verification code and connect to our WiFi network.',
                welcomeTextNoOtp:     'Please enter your email address to connect to our WiFi network.',
                emailLabel:           'Email Address',
                emailPlaceholder:     'Enter your email address',
                nameLabel:            'Name (Optional)',
                namePlaceholder:      'Enter your name',
                sendCode:             'Send Verification Code',
                connectDirectly:      'Connect to WiFi',
                verificationCodeLabel:'Verification Code',
                requestAgainIn:       'Request code again in',
                didntReceive:         "Didn't receive the code?",
                resend:               'Resend',
                connectButton:        'Connect to WiFi',
                footer:               'Powered by Monsieur WiFi',
                sending:              'Sending...',
                verifying:            'Verifying...',
                verifiedSuccess:      'Verified!',
                connectingWifi:       'Connecting to WiFi...',
                verificationFailed:   'Verification Failed',
                connectionError:      'Connection Error',
                codeSent:             'Verification code sent to your email address',
                failedToSend:         'Failed to send verification code. Please try again.',
                newCodeSent:          'New verification code sent to your email address',
                finalAttempt:         ' (final attempt — limit reached)',
                failedToResend:       'Failed to resend verification code',
                enterValid4Digit:     'Please enter a valid 4-digit verification code',
                enterValidEmail:      'Please enter a valid email address',
                maxResendLimit:       'Maximum resend limit reached (5). Please try again later.',
                errorLoading:         'Error loading WiFi information. Please refresh the page or contact support.',
                errorMissing:         'Required information is missing. Please check your connection or contact support.',
                termsText:            'By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>',
                termsTitle:           'Terms of Service',
                privacyTitle:         'Privacy Policy',
                close:                'Close',
                pageTitle:            'WiFi Login with Email',
                errorHeading:         'Error',
                sentTo:               'Sent to',
                edit:                 'Edit',
            },
            fr: {
                welcomeText:          'Veuillez entrer votre adresse e-mail pour recevoir un code de vérification unique et vous connecter à notre réseau WiFi.',
                welcomeTextNoOtp:     'Veuillez entrer votre adresse e-mail pour vous connecter à notre réseau WiFi.',
                emailLabel:           'Adresse e-mail',
                emailPlaceholder:     'Entrez votre adresse e-mail',
                nameLabel:            'Nom (Optionnel)',
                namePlaceholder:      'Entrez votre nom',
                sendCode:             'Envoyer le code de vérification',
                connectDirectly:      'Se connecter au WiFi',
                verificationCodeLabel:'Code de vérification',
                requestAgainIn:       'Demander à nouveau dans',
                didntReceive:         'Code non reçu ?',
                resend:               'Renvoyer',
                connectButton:        'Se connecter au WiFi',
                footer:               'Propulsé par Monsieur WiFi',
                sending:              'Envoi...',
                verifying:            'Vérification...',
                verifiedSuccess:      'Vérifié !',
                connectingWifi:       'Connexion au WiFi...',
                verificationFailed:   'Échec de la vérification',
                connectionError:      'Erreur de connexion',
                codeSent:             'Code de vérification envoyé à votre adresse e-mail',
                failedToSend:         "Échec de l'envoi du code de vérification. Veuillez réessayer.",
                newCodeSent:          'Nouveau code de vérification envoyé à votre adresse e-mail',
                finalAttempt:         ' (dernière tentative — limite atteinte)',
                failedToResend:       'Échec du renvoi du code de vérification',
                enterValid4Digit:     'Veuillez entrer un code de vérification valide à 4 chiffres',
                enterValidEmail:      'Veuillez entrer une adresse e-mail valide',
                maxResendLimit:       "Limite maximale d'envoi atteinte (5). Veuillez réessayer plus tard.",
                errorLoading:         'Erreur de chargement des informations WiFi. Veuillez actualiser la page ou contacter le support.',
                errorMissing:         'Informations requises manquantes. Veuillez vérifier votre connexion ou contacter le support.',
                termsText:            'En vous connectant, vous acceptez nos <a href="#" data-toggle="modal" data-target="#termsModal">Conditions de service</a> et notre <a href="#" data-toggle="modal" data-target="#privacyModal">Politique de confidentialité</a>',
                termsTitle:           'Conditions de service',
                privacyTitle:         'Politique de confidentialité',
                close:                'Fermer',
                pageTitle:            'Connexion WiFi par e-mail',
                errorHeading:         'Erreur',
                sentTo:               'Envoyé à',
                edit:                 'Modifier',
            },
        };

        function getLanguage() {
            const stored = localStorage.getItem('wifiPortalLanguage');
            if (stored === 'en' || stored === 'fr') return stored;
            const lang = (navigator.language || navigator.userLanguage || '').toLowerCase().split('-')[0];
            return lang === 'fr' ? 'fr' : 'en';
        }

        function applyTranslations(lang) {
            // Update <html lang> and <title>
            document.getElementById('html-root').setAttribute('lang', lang);
            document.getElementById('page-title').textContent = translations[lang].pageTitle;

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                // Skip buttons that are mid-flight (disabled = spinner injected)
                if (el.tagName === 'BUTTON' && el.disabled) return;
                if (translations[lang]?.[key]) el.textContent = translations[lang][key];
            });
            document.querySelectorAll('[data-i18n-default]').forEach(el => {
                const key = el.getAttribute('data-i18n-default');
                // Skip buttons that are mid-flight (disabled = spinner injected)
                if (el.tagName === 'BUTTON' && el.disabled) return;
                if (el.getAttribute('data-is-custom') !== 'true' && translations[lang]?.[key]) {
                    el.textContent = translations[lang][key];
                }
            });
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (translations[lang]?.[key]) el.placeholder = translations[lang][key];
            });
            const termsLinks = document.getElementById('terms-links');
            if (termsLinks && termsLinks.style.display !== 'none') {
                termsLinks.innerHTML = translations[lang].termsText;
            }
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
            });
        }

        function switchLanguage(lang) {
            if (lang === 'en' || lang === 'fr') {
                localStorage.setItem('wifiPortalLanguage', lang);
                applyTranslations(lang);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.addEventListener('click', function () { switchLanguage(this.getAttribute('data-lang')); });
            });
        });

        // Apply immediately to avoid flicker
        const currentLang = getLanguage();
        applyTranslations(currentLang);

        // Whether this network requires an OTP for email login (default: true)
        window.emailRequireOtp = true;

        /**
         * Switch the page between OTP-required and email-only modes.
         * Called once the network settings are fetched from the API.
         */
        function applyEmailOtpMode(requireOtp) {
            window.emailRequireOtp = requireOtp;
            const lang = getLanguage();
            if (!requireOtp) {
                const welcomeEl = document.getElementById('welcome-text');
                if (welcomeEl && welcomeEl.getAttribute('data-is-custom') !== 'true') {
                    welcomeEl.textContent = translations[lang].welcomeTextNoOtp;
                }
                $('#send-code-button').text(translations[lang].connectDirectly);
            }
        }

        // ── Main logic ────────────────────────────────────────────────────────
        $(document).ready(function () {
            const locationData     = JSON.parse(localStorage.getItem('location_data') || '{}');
            const locationSettings = locationData.settings || {};
            const designData       = locationData.design   || {};

            $('#terms-content').html(designData.terms_content || '');
            $('#privacy-content').html(designData.privacy_content || '');

            // button_text is a function so it always returns the right language
            function getButtonText() {
                return designData.button_text || translations[getLanguage()].connectButton;
            }
            var button_text = getButtonText();
            if (designData.button_text) {
                $('#verify-otp-button').text(button_text).attr('data-is-custom', 'true');
            } else {
                $('#verify-otp-button').text(button_text);
            }

            const urlParams  = new URLSearchParams(window.location.search);
            const networkId  = getPathParameter('location');
            const zoneId     = getPathParameter('zone_id');
            const macAddress = urlParams.get('mac') || getPathParameter('mac_address');

            applyDesignSettings(locationSettings, designData);

            let timerInterval;
            let secondsRemaining = 300; // 5 minutes
            let sendCount = 0;
            const MAX_SENDS = 5;

            // ── OTP input navigation ─────────────────────────────────────────
            $('.otp-input').on('input', function () {
                if ($(this).val().length === 1) $(this).next('.otp-input').focus();
                combineOtpValues();
            });

            $('.otp-input').on('keydown', function (e) {
                if (e.keyCode === 8 && $(this).val() === '') $(this).prev('.otp-input').focus();
            });

            function combineOtpValues() {
                $('#otp').val($('#otp-1').val() + $('#otp-2').val() + $('#otp-3').val() + $('#otp-4').val());
            }

            // ── Edit email — go back to step 1 ───────────────────────────────
            $('#edit-email-link').on('click', function (e) {
                e.preventDefault();
                const lang = getLanguage();
                clearInterval(timerInterval);
                // Reset OTP step state
                $('#otp-step').hide();
                $('#otp-1,#otp-2,#otp-3,#otp-4').val('');
                $('#otp').val('');
                $('#resend-action').hide();
                $('#timer-text').show();
                secondsRemaining = 300;
                updateTimerDisplay();
                // Restore send button to its original enabled state
                $('#send-code-button')
                    .prop('disabled', false)
                    .html(translations[lang].sendCode);
                $('#email-step').show();
                $('#email').focus();
                $('#alert-container').hide();
            });

            // ── Step 1: Send verification code (or connect directly) ─────────
            $('#email-form').on('submit', function (e) {
                e.preventDefault();
                const lang  = getLanguage();
                const email = $('#email').val().trim();
                const name  = $('#name').val().trim();

                if (!email) {
                    showAlert(translations[lang].enterValidEmail, 'danger');
                    return;
                }

                const $btn     = $('#send-code-button');
                const origHtml = $btn.html();

                // ── No-OTP path: connect directly without sending a code ──────
                if (!window.emailRequireOtp) {
                    const challenge  = localStorage.getItem('challenge');
                    const ipAddress  = localStorage.getItem('nas_ip');
                    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + translations[lang].verifying).prop('disabled', true);

                    $.ajax({
                        url: '/api/guest/login',
                        method: 'POST',
                        data: {
                            network_id:   networkId,
                            zone_id:      zoneId,
                            mac_address:  macAddress,
                            login_method: 'email',
                            email:        email,
                            name:         name,
                            challenge:    challenge,
                            ip_address:   ipAddress,
                        },
                        success: function (response) {
                            const lang = getLanguage();
                            if (response.success) {
                                $btn.removeClass('btn-primary').addClass('btn-success')
                                    .html(translations[lang].verifiedSuccess + ' <i class="fa fa-check"></i>')
                                    .prop('disabled', true);
                                setTimeout(function () {
                                    $btn.html(translations[lang].connectingWifi + ' <i class="fa fa-wifi"></i>');
                                    setTimeout(function () { window.location.href = response.login_url; }, 1500);
                                }, 1500);
                            } else {
                                $btn.html(origHtml).prop('disabled', false);
                                showAlert(response.message || translations[lang].connectionError, 'danger');
                            }
                        },
                        error: function (xhr) {
                            const lang = getLanguage();
                            $btn.html(origHtml).prop('disabled', false);
                            showAlert(xhr.responseJSON?.message || translations[lang].connectionError, 'danger');
                        },
                    });
                    return;
                }

                // ── OTP path: send code then transition to step 2 ────────────
                if (sendCount >= MAX_SENDS) {
                    showAlert(translations[lang].maxResendLimit, 'warning');
                    return;
                }

                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + translations[lang].sending).prop('disabled', true);

                $.ajax({
                    url: '/api/guest/request-email-otp',
                    method: 'POST',
                    data: { network_id: networkId, email: email, mac_address: macAddress, locale: lang },
                    success: function (response) {
                        const lang = getLanguage();
                        if (response.success) {
                            sendCount++;
                            // Transition to OTP step
                            $('#sent-to-email').text(email);
                            $('#email-step').hide();
                            $('#otp-1,#otp-2,#otp-3,#otp-4').val('');
                            $('#otp').val('');
                            $('#otp-step').show();
                            $('#otp-1').focus();
                            startTimer();
                            showAlert(translations[lang].codeSent, 'success');
                        } else {
                            $btn.html(origHtml).prop('disabled', false);
                            showAlert(response.message || translations[lang].failedToSend, 'danger');
                        }
                    },
                    error: function (xhr) {
                        const lang = getLanguage();
                        $btn.html(origHtml).prop('disabled', false);
                        const msg = xhr.responseJSON?.message || translations[lang].failedToSend;
                        showAlert(msg, 'danger');
                    },
                });
            });

            // ── Step 2: Verify OTP and login ─────────────────────────────────
            $('#verify-otp-form').on('submit', function (e) {
                e.preventDefault();
                const lang       = getLanguage();
                const otp        = $('#otp').val();
                const challenge  = localStorage.getItem('challenge');
                const ipAddress  = localStorage.getItem('nas_ip');
                const email      = $('#email').val().trim();
                const name       = $('#name').val().trim();

                if (!otp || otp.length !== 4) {
                    showAlert(translations[lang].enterValid4Digit, 'danger');
                    return;
                }

                const $btn = $('#verify-otp-button');
                const origBgColor = $btn.css('background-color');
                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + translations[lang].verifying).prop('disabled', true);

                const loginData = {
                    network_id:   networkId,
                    zone_id:      zoneId,
                    mac_address:  macAddress,
                    login_method: 'email',
                    email:        email,
                    name:         name,
                    otp:          otp,
                    challenge:    challenge,
                    ip_address:   ipAddress,
                };

                $.ajax({
                    url: '/api/guest/login',
                    method: 'POST',
                    data: loginData,
                    success: function (response) {
                        const lang = getLanguage();
                        if (response.success) {
                            clearInterval(timerInterval);
                            $btn.removeClass('btn-primary').addClass('btn-success')
                                .html(translations[lang].verifiedSuccess + ' <i class="fa fa-check"></i>')
                                .prop('disabled', true);
                            setTimeout(function () {
                                $btn.html(translations[lang].connectingWifi + ' <i class="fa fa-wifi"></i>');
                                setTimeout(function () {
                                    window.location.href = response.login_url;
                                }, 1500);
                            }, 1500);
                        } else {
                            $btn.removeClass('btn-primary').addClass('btn-danger')
                                .html(translations[lang].verificationFailed)
                                .prop('disabled', false);
                            setTimeout(function () {
                                $btn.html(getButtonText()).removeClass('btn-danger').css('background-color', origBgColor);
                            }, 1500);
                        }
                    },
                    error: function (xhr) {
                        const lang = getLanguage();
                        $btn.removeClass('btn-primary').addClass('btn-danger')
                            .html('<i class="fa fa-exclamation-circle"></i> ' + translations[lang].connectionError)
                            .prop('disabled', false);
                        const msg = xhr.responseJSON?.message || translations[lang].verificationFailed;
                        showAlert(msg, 'danger');
                        setTimeout(function () {
                            $btn.html(getButtonText()).removeClass('btn-danger').css('background-color', origBgColor);
                        }, 1500);
                    },
                });
            });

            // ── Resend code ───────────────────────────────────────────────────
            $('#resend-link').on('click', function () {
                const lang = getLanguage();
                if (sendCount >= MAX_SENDS) {
                    showAlert(translations[lang].maxResendLimit, 'warning');
                    return;
                }

                secondsRemaining = 300;
                updateTimerDisplay();
                $('#timer-text').show();
                $('#resend-action').hide();
                startTimer();

                const $link    = $(this);
                const origText = $link.text();
                $link.text(translations[lang].sending).css('pointer-events', 'none');

                $.ajax({
                    url: '/api/guest/request-email-otp',
                    method: 'POST',
                    data: {
                        network_id:  networkId,
                        email:       $('#email').val().trim(),
                        mac_address: macAddress,
                        locale:      lang,
                    },
                    success: function (response) {
                        const lang = getLanguage();
                        $link.text(origText).css('pointer-events', 'auto');
                        if (response.success) {
                            sendCount++;
                            let msg = translations[lang].newCodeSent;
                            if (sendCount >= MAX_SENDS) {
                                msg += translations[lang].finalAttempt;
                                $link.addClass('disabled').css({ color: '#999', cursor: 'not-allowed', 'text-decoration': 'none' });
                            }
                            showAlert(msg, 'success');
                        } else {
                            showAlert(response.message || translations[lang].failedToResend, 'danger');
                        }
                    },
                    error: function (xhr) {
                        const lang = getLanguage();
                        $link.text(origText).css('pointer-events', 'auto');
                        showAlert(xhr.responseJSON?.message || translations[lang].failedToResend, 'danger');
                    },
                    complete: function () {
                        $link.css('pointer-events', 'auto');
                    },
                });
            });

            // ── Timer ─────────────────────────────────────────────────────────
            function startTimer() {
                if (timerInterval) clearInterval(timerInterval);
                updateTimerDisplay();
                timerInterval = setInterval(function () {
                    secondsRemaining--;
                    if (secondsRemaining <= 0) {
                        clearInterval(timerInterval);
                        $('#timer-text').hide();
                        $('#resend-action').show();
                    } else {
                        updateTimerDisplay();
                    }
                }, 1000);
            }

            function updateTimerDisplay() {
                const m = Math.floor(secondsRemaining / 60);
                const s = secondsRemaining % 60;
                $('#timer').text(`${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`);
            }

            // ── Helpers ───────────────────────────────────────────────────────
            function showAlert(message, type) {
                $('#alert-container').html(`<div class="alert alert-${type}" role="alert">${message}</div>`).show();
                if (type === 'success') {
                    setTimeout(function () { $('#alert-container').fadeOut(); }, 5000);
                }
            }

            function applyDesignSettings(settings, design) {
                const themeColor = design.theme_color || settings.theme_color
                    || getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim();
                if (themeColor) {
                    document.documentElement.style.setProperty('--theme-color', themeColor);
                    document.documentElement.style.setProperty('--theme-color-dark', createDarkerColor(themeColor));
                }
                if (design.background_image_path) {
                    document.body.style.backgroundImage = `url('/storage/${design.background_image_path}')`;
                }
                if (design.background_color_gradient_start && design.background_color_gradient_end) {
                    $('.portal-container').css({
                        'background':       `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`,
                        'background-image': `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`,
                    });
                } else if (design.background_color_gradient_start || design.background_color_gradient_end) {
                    const color = design.background_color_gradient_start || design.background_color_gradient_end;
                    $('.portal-container').css({ 'background': color, 'background-image': 'none' });
                }
                if (design.location_logo_path) {
                    $('#location-logo').html(`<img src="/storage/${design.location_logo_path}" alt="Location Logo">`);
                }
                const welcomeMsg = design.welcome_message || settings.welcome_message;
                if (welcomeMsg) {
                    $('#welcome-text').text(welcomeMsg).attr('data-is-custom', 'true');
                    if (design.login_instructions && design.login_instructions.length > 1) {
                        $('#welcome-text').append(`<p class="mt-2">${design.login_instructions}</p>`);
                    }
                }
                if (design.button_text) {
                    $('#verify-otp-button').text(design.button_text).attr('data-is-custom', 'true');
                    designData.button_text = design.button_text;
                }
                const showTerms = design.show_terms === true || settings.terms_enabled === true;
                const lang = getLanguage();
                if (showTerms) {
                    $('#terms-links').html(translations[lang].termsText).show();
                } else {
                    $('#terms-links').hide();
                }
                if (design.terms_of_service) $('#terms-content').html(design.terms_of_service);
                if (design.privacy_policy)   $('#privacy-content').html(design.privacy_policy);
                if (design.terms_content)    $('#terms-content').html(design.terms_content);
                if (design.privacy_content)  $('#privacy-content').html(design.privacy_content);
                applyTranslations(lang);
            }

            function createDarkerColor(hexColor) {
                hexColor = hexColor.replace('#', '');
                let r = Math.max(0, parseInt(hexColor.substr(0, 2), 16) - 25);
                let g = Math.max(0, parseInt(hexColor.substr(2, 2), 16) - 25);
                let b = Math.max(0, parseInt(hexColor.substr(4, 2), 16) - 25);
                return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
            }

            function getPathParameter(param) {
                const parts = window.location.pathname.split('/');
                const hasZone = parts.length >= 5;
                if (param === 'location')    return parts[2] || '';
                if (param === 'zone_id')     return hasZone ? (parts[3] || '0') : '0';
                if (param === 'mac_address') return hasZone ? (parts[4] || '') : (parts[3] || '');
                return '';
            }

            // Show error if critical params are missing
            if (!networkId || !macAddress) {
                const lang = getLanguage();
                $('.portal-container').html(`
                    <div class="text-center">
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">${translations[lang].errorHeading}</h4>
                            <p>${translations[lang].errorMissing}</p>
                        </div>
                    </div>`);
            }

            // Refresh design from the API (same pattern as all other portal pages)
            $.ajax({
                url: `/api/captive-portal/${networkId}/info`,
                type: 'GET',
                data: { mac_address: macAddress },
                headers: { 'Accept': 'application/json' },
                success: function (locationInfo) {
                    if (locationInfo.success && locationInfo.location) {
                        localStorage.setItem('location_data', JSON.stringify(locationInfo.location));
                        localStorage.setItem('challenge', locationInfo.location.challenge);
                        if (locationInfo.location.design) {
                            if (locationInfo.location.design.terms_content) {
                                $('#terms-content').html(locationInfo.location.design.terms_content);
                            }
                            if (locationInfo.location.design.privacy_content) {
                                $('#privacy-content').html(locationInfo.location.design.privacy_content);
                            }
                        }
                        applyDesignSettings(locationInfo.location.settings || {}, locationInfo.location.design || {});
                        const requireOtp = locationInfo.location.settings?.email_require_otp !== false;
                        applyEmailOtpMode(requireOtp);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching location info:', error);
                    const lang = getLanguage();
                    showAlert(translations[lang].errorLoading, 'danger');
                },
            });
        });
    </script>
</body>
</html>
