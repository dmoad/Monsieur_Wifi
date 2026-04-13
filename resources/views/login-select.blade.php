<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi Login — Choose a method</title>
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
            background-image: url('/app-assets/mrwifi-assets/captive-portal/images/background.jpg');
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
            width: 100%;
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
            margin-bottom: 1.5rem;
        }

        /* ── Method list ── */
        .section-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #adb5bd;
            margin-bottom: 10px;
        }

        .method-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 0.5rem;
        }

        .method-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: background 0.18s, border-color 0.18s, box-shadow 0.18s, transform 0.12s;
            width: 100%;
            text-align: left;
            background: #f8f9fa;
            color: #212529;
        }

        .method-btn:hover {
            background: #fff;
            border-color: var(--theme-color);
            box-shadow: 0 0 0 3px var(--theme-color-light);
            transform: translateY(-1px);
        }

        .method-btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .method-btn:disabled {
            cursor: not-allowed;
            opacity: 0.75;
            transform: none;
        }

        .method-btn-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .method-btn-icon-wrap svg {
            width: 20px;
            height: 20px;
        }

        .method-btn-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .method-btn-title {
            font-size: 0.92rem;
            font-weight: 600;
            color: #212529;
            line-height: 1.2;
        }

        .method-btn-subtitle {
            font-size: 0.75rem;
            color: #868e96;
            font-weight: 400;
            line-height: 1.2;
        }

        .method-btn-chevron {
            color: #ced4da;
            flex-shrink: 0;
            transition: color 0.18s;
        }

        .method-btn:hover .method-btn-chevron { color: var(--theme-color); }

        /* Per-method icon tints */
        .icon-click    { background: #e8f5e9; }
        .icon-password { background: #fff3e0; }
        .icon-sms      { background: #e3f2fd; }
        .icon-email    { background: #fce4ec; }
        .icon-google   { background: #fff; border: 1px solid #e8eaed; }
        .icon-facebook { background: #e8f0fe; }

        .spinner-border-sm-inline {
            width: 0.9rem;
            height: 0.9rem;
            border-width: 2px;
            vertical-align: middle;
        }

        /* ── Footer (identical to other portal pages) ── */
        .footer {
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
            text-align: center;
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

        .terms {
            font-size: 0.8rem;
            color: #666;
        }

        .terms a {
            color: var(--theme-color);
            text-decoration: none;
        }

        .terms a:hover { text-decoration: underline; }

        /* ── Language switcher (identical to other portal pages) ── */
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

        .language-switcher:hover { opacity: 1; }

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

    <!-- Language switcher -->
    <div class="language-switcher">
        <button class="language-btn" data-lang="en">English</button>
        <button class="language-btn" data-lang="fr">Français</button>
    </div>

    <div class="portal-container">

        <!-- Location logo -->
        <div class="text-center">
            <div class="location-logo mx-auto" id="location-logo">
                <div style="background:#f0f0f0;width:100%;height:100%;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#666;">
                    Location Logo
                </div>
            </div>
        </div>

        <!-- Welcome message -->
        <div class="welcome-text" id="welcome-text" data-i18n-default="welcomeText">
            Choose how you'd like to connect to the WiFi network.
        </div>

        <!-- Method list -->
        <div class="section-label" id="method-label"></div>
        <div class="method-list" id="method-list">
            <div class="text-center text-muted py-3">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                <span id="loading-text"> Loading…</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-links" style="display:none; margin-bottom:0.5rem;"></div>
            <div class="terms" id="terms-text" data-i18n="footer">Powered by Monsieur WiFi</div>
        </div>
    </div>

    <!-- Terms modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="termsTitle">Terms of Service</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="max-height:60vh;overflow-y:auto;">
                    <p id="terms-content"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-i18n="close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="privacyTitle">Privacy Policy</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="max-height:60vh;overflow-y:auto;">
                    <p id="privacy-content"></p>
                </div>
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
    /* -----------------------------------------------------------------------
     * login-select.blade.php  —  multi-method captive portal chooser page
     *
     * URL shapes:
     *   /login-select/{networkId}/{zoneId}/{mac}   (new)
     *   /login-select/{networkId}/{mac}             (legacy)
     * ----------------------------------------------------------------------- */

    // ── i18n (same keys/pattern as other portal pages) ───────────────────────
    const translations = {
        en: {
            welcomeText:  'Choose how you\'d like to connect to the WiFi network.',
            methodLabel:  'Connect with',
            loading:      'Loading…',
            connecting:   'Connecting…',
            footer:       'Powered by Monsieur WiFi',
            termsText:    'By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>',
            termsTitle:   'Terms of Service',
            privacyTitle: 'Privacy Policy',
            close:        'Close',
            errorMissing: 'Missing network parameters. Please try again.',
            noMethods:    'No login methods configured.',
            // Method labels
            clickThrough: 'Connect for Free',   clickSub: 'No sign-up required',
            password:     'Password',            passwordSub: 'Enter the shared access password',
            sms:          'SMS Verification',    smsSub: 'Receive a one-time code by text',
            email:        'Email Verification',  emailSub: 'Receive a one-time link by email',
            facebook:     'Continue with Facebook', facebookSub: 'Use your Facebook account',
            google:       'Continue with Google',   googleSub: 'Use your Google account',
        },
        fr: {
            welcomeText:  'Choisissez comment vous souhaitez vous connecter au réseau WiFi.',
            methodLabel:  'Se connecter avec',
            loading:      'Chargement…',
            connecting:   'Connexion…',
            footer:       'Propulsé par Monsieur WiFi',
            termsText:    'En vous connectant, vous acceptez nos <a href="#" data-toggle="modal" data-target="#termsModal">Conditions de service</a> et notre <a href="#" data-toggle="modal" data-target="#privacyModal">Politique de confidentialité</a>',
            termsTitle:   'Conditions de service',
            privacyTitle: 'Politique de confidentialité',
            close:        'Fermer',
            errorMissing: 'Paramètres réseau manquants. Veuillez réessayer.',
            noMethods:    'Aucune méthode de connexion configurée.',
            clickThrough: 'Connexion gratuite',    clickSub: 'Aucune inscription requise',
            password:     'Mot de passe',          passwordSub: 'Saisissez le mot de passe d\'accès partagé',
            sms:          'Vérification par SMS',  smsSub: 'Recevez un code à usage unique par SMS',
            email:        'Vérification par e-mail', emailSub: 'Recevez un lien à usage unique par e-mail',
            facebook:     'Continuer avec Facebook', facebookSub: 'Utilisez votre compte Facebook',
            google:       'Continuer avec Google',   googleSub: 'Utilisez votre compte Google',
        },
    };

    function getLanguage() {
        const stored = localStorage.getItem('wifiPortalLanguage');
        if (stored === 'en' || stored === 'fr') return stored;
        const lang = (navigator.language || navigator.userLanguage || '').toLowerCase().split('-')[0];
        return lang === 'fr' ? 'fr' : 'en';
    }

    function applyTranslations(lang) {
        const t = translations[lang];
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (t[key]) el.textContent = t[key];
        });
        document.querySelectorAll('[data-i18n-default]').forEach(el => {
            const key = el.getAttribute('data-i18n-default');
            if (el.getAttribute('data-is-custom') !== 'true' && t[key]) el.textContent = t[key];
        });
        document.querySelectorAll('.language-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
        });
        const termsLinks = document.getElementById('terms-links');
        if (termsLinks && termsLinks.style.display !== 'none') {
            termsLinks.innerHTML = t.termsText;
        }
        const labelEl = document.getElementById('method-label');
        if (labelEl) labelEl.textContent = t.methodLabel;
        const loadingEl = document.getElementById('loading-text');
        if (loadingEl) loadingEl.textContent = ' ' + t.loading;
    }

    // Apply immediately (before DOM ready) to avoid flicker
    applyTranslations(getLanguage());

    // ── SVG icons ─────────────────────────────────────────────────────────────
    const ICONS = {
        wifi:     `<svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>`,
        lock:     `<svg viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,
        sms:      `<svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,
        mail:     `<svg viewBox="0 0 24 24" fill="none" stroke="#ad1457" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>`,
        google:   `<svg viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.14 0 5.95 1.08 8.17 2.86l6.1-6.1C34.36 3.07 29.46 1 24 1 14.82 1 7.07 6.48 3.62 14.24l7.1 5.52C12.42 13.57 17.76 9.5 24 9.5z"/><path fill="#4285F4" d="M46.52 24.5c0-1.63-.15-3.2-.42-4.72H24v8.93h12.68c-.55 2.93-2.2 5.41-4.69 7.08l7.18 5.58C43.36 37.42 46.52 31.4 46.52 24.5z"/><path fill="#FBBC05" d="M10.72 28.24A14.54 14.54 0 0 1 9.5 24c0-1.47.25-2.9.72-4.24l-7.1-5.52A23.95 23.95 0 0 0 .5 24c0 3.84.91 7.48 2.52 10.7l7.7-6.46z"/><path fill="#34A853" d="M24 47c5.46 0 10.04-1.81 13.38-4.91l-7.18-5.58c-1.83 1.23-4.17 1.96-6.2 1.96-6.24 0-11.58-4.07-13.28-9.72l-7.7 6.46C7.07 41.52 14.82 47 24 47z"/></svg>`,
        facebook: `<svg viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>`,
        chevron:  `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>`,
    };

    // ── URL helpers ───────────────────────────────────────────────────────────
    function resolveUrlParams() {
        const segs = window.location.pathname.replace(/^\/|\/$/g, '').split('/');
        // segs[0] = "login-select", [1] = networkId, [2] = zoneId or mac, [3] = mac
        const hasZone = segs.length >= 4;
        return {
            networkId:  segs[1] || null,
            zoneId:     hasZone ? segs[2] : '0',
            macAddress: hasZone ? segs[3] : segs[2],
        };
    }

    // ── Design theming (same logic as other portal pages) ────────────────────
    function createDarkerColor(hexColor) {
        hexColor = hexColor.replace('#', '');
        let r = Math.max(0, parseInt(hexColor.substr(0, 2), 16) - 25);
        let g = Math.max(0, parseInt(hexColor.substr(2, 2), 16) - 25);
        let b = Math.max(0, parseInt(hexColor.substr(4, 2), 16) - 25);
        return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
    }

    function applyDesignSettings(settings, design) {
        // theme_color → CSS custom property (+ derived dark shade for hover states)
        const themeColor = design.theme_color || settings.theme_color
            || getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim();
        if (themeColor) {
            document.documentElement.style.setProperty('--theme-color', themeColor);
            document.documentElement.style.setProperty('--theme-color-dark', createDarkerColor(themeColor));
        }

        // background_image_path → full-page background
        if (design.background_image_path) {
            document.body.style.backgroundImage = `url('/storage/${design.background_image_path}')`;
        }

        // background_color_gradient_start / _end → portal card background
        if (design.background_color_gradient_start && design.background_color_gradient_end) {
            $('.portal-container').css({
                'background':       `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`,
                'background-image': `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`,
            });
        } else if (design.background_color_gradient_start || design.background_color_gradient_end) {
            const color = design.background_color_gradient_start || design.background_color_gradient_end;
            $('.portal-container').css({ 'background': color, 'background-image': 'none' });
        }

        // location_logo_path → location logo at the top of the card
        if (design.location_logo_path) {
            $('#location-logo').html(`<img src="/storage/${design.location_logo_path}" alt="Location Logo">`);
        }

        // welcome_message + login_instructions → card header text
        const welcomeMsg = design.welcome_message || settings.welcome_message;
        if (welcomeMsg) {
            $('#welcome-text').text(welcomeMsg).attr('data-is-custom', 'true');
            if (design.login_instructions && design.login_instructions.length > 1) {
                $('#welcome-text').append(`<p class="mt-2">${design.login_instructions}</p>`);
            }
        }

        // terms_content / privacy_content → modal body
        if (design.terms_content)    $('#terms-content').html(design.terms_content);
        if (design.privacy_content)  $('#privacy-content').html(design.privacy_content);
        // legacy field names kept for backward compat
        if (design.terms_of_service) $('#terms-content').html(design.terms_of_service);
        if (design.privacy_policy)   $('#privacy-content').html(design.privacy_policy);

        // show_terms → terms / privacy links above footer text
        const showTerms = design.show_terms === true || settings.terms_enabled === true;
        const lang = getLanguage();
        if (showTerms) {
            $('#terms-links').html(translations[lang].termsText).show();
        } else {
            $('#terms-links').hide();
        }

        applyTranslations(lang);
    }

    // ── redirectToMethod — mirrors loading.js ────────────────────────────────
    function redirectToMethod(method, settings, networkId, zoneId, macAddress) {
        switch (method) {
            case 'email':
                window.location.href = `/email-login/${networkId}/${zoneId}/${macAddress}`; break;
            case 'sms':
                window.location.href = `/sms-login/${networkId}/${zoneId}/${macAddress}`; break;
            case 'social':
                if ((settings.captive_social_auth_method || '').includes('facebook')) {
                    window.location.href = `/social-login/facebook/${networkId}/${zoneId}/${macAddress}`;
                } else {
                    window.location.href = `/social-login/google/${networkId}/${zoneId}/${macAddress}`;
                }
                break;
            case 'password':
                window.location.href = `/password-login/${networkId}/${zoneId}/${macAddress}`; break;
            case 'click-through':
            default:
                window.location.href = `/click-login/${networkId}/${zoneId}/${macAddress}`; break;
        }
    }

    // ── Build a single method button ─────────────────────────────────────────
    function buildMethodButton(method, settings, networkId, zoneId, macAddress, lang) {
        const t = translations[lang];
        let title, subtitle, iconKey, iconClass;

        switch (method) {
            case 'click-through':
                title = t.clickThrough; subtitle = t.clickSub;
                iconKey = 'wifi';  iconClass = 'icon-click';    break;
            case 'password':
                title = t.password;     subtitle = t.passwordSub;
                iconKey = 'lock';  iconClass = 'icon-password'; break;
            case 'sms':
                title = t.sms;          subtitle = t.smsSub;
                iconKey = 'sms';   iconClass = 'icon-sms';      break;
            case 'email':
                title = t.email;        subtitle = t.emailSub;
                iconKey = 'mail';  iconClass = 'icon-email';    break;
            case 'social': {
                const provider = (settings.captive_social_auth_method || '').toLowerCase();
                if (provider.includes('google')) {
                    title = t.google;   subtitle = t.googleSub;
                    iconKey = 'google';   iconClass = 'icon-google';
                } else {
                    title = t.facebook; subtitle = t.facebookSub;
                    iconKey = 'facebook'; iconClass = 'icon-facebook';
                }
                break;
            }
            default: return null;
        }

        const btn = document.createElement('button');
        btn.className = 'method-btn';
        btn.innerHTML = `
            <div class="method-btn-icon-wrap ${iconClass}">${ICONS[iconKey]}</div>
            <div class="method-btn-text">
                <span class="method-btn-title">${title}</span>
                <span class="method-btn-subtitle">${subtitle}</span>
            </div>
            <span class="method-btn-chevron">${ICONS.chevron}</span>`;

        btn.addEventListener('click', function () {
            btn.innerHTML = `
                <div class="method-btn-icon-wrap ${iconClass}">${ICONS[iconKey]}</div>
                <div class="method-btn-text">
                    <span class="method-btn-title">${title}</span>
                    <span class="method-btn-subtitle" style="display:flex;align-items:center;gap:6px;">
                        <span class="spinner-border spinner-border-sm-inline" role="status"></span>
                        ${t.connecting}
                    </span>
                </div>`;
            btn.disabled = true;
            redirectToMethod(method, settings, networkId, zoneId, macAddress);
        });
        return btn;
    }

    // ── Main init ─────────────────────────────────────────────────────────────
    $(document).ready(function () {
        const { networkId, zoneId, macAddress } = resolveUrlParams();

        const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
        const settings     = locationData.settings || {};
        const design       = locationData.design   || {};

        // Pre-fill modal bodies before applyDesignSettings (same order as click-login)
        $('#terms-content').html(design.terms_content || '');
        $('#privacy-content').html(design.privacy_content || '');

        applyDesignSettings(settings, design);

        // Determine enabled methods once — used both on init and on language switch
        const methods = (settings.captive_auth_methods && settings.captive_auth_methods.length)
            ? settings.captive_auth_methods
            : [settings.captive_auth_method || 'click-through'];

        function renderMethodList() {
            const lang  = getLanguage();
            const $list = document.getElementById('method-list');
            $list.innerHTML = '';

            if (!networkId || !macAddress) {
                $list.innerHTML = `<p class="text-danger text-center mt-2">${translations[lang].errorMissing}</p>`;
                return;
            }

            methods.forEach(function (method) {
                const btn = buildMethodButton(method, settings, networkId, zoneId, macAddress, lang);
                if (btn) $list.appendChild(btn);
            });

            if (!$list.children.length) {
                $list.innerHTML = `<p class="text-muted text-center mt-2">${translations[lang].noMethods}</p>`;
            }
        }

        renderMethodList();

        // Language switcher — also rebuilds method buttons so labels update too
        document.querySelectorAll('.language-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const lang = this.getAttribute('data-lang');
                localStorage.setItem('wifiPortalLanguage', lang);
                applyTranslations(lang);
                renderMethodList();
            });
        });
    });
    </script>
</body>
</html>
