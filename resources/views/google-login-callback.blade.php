<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social WiFi Login with Google</title>
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
            margin-bottom: 2rem;
            color: #333;
            line-height: 1.6;
        }

        .success-message, .error-message {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            line-height: 1.6;
        }

        .success-icon {
            font-size: 4rem;
            color: #28c76f;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #ea5455;
            margin-bottom: 1rem;
            text-align: center;
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
            cursor: pointer;
        }

        .login-button:hover {
            background-color: var(--theme-color-dark);
        }

        .google-button {
            background-color: #ffffff;
            color: #3c4043;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 14px 28px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin: 1rem auto;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: 500;
        }

        .google-button:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .google-button:active {
            background-color: #f1f3f4;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .google-button i {
            margin-right: 12px;
            font-size: 1.2rem;
            color: #4285f4;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
        }
        
        .input-group-text {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            cursor: pointer;
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

        .login-info {
            font-size: 0.85rem;
            color: #666;
            margin-top: 1rem;
            text-align: center;
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

        .status-box {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.7);
        }

        .status-box.success {
            border-left: 4px solid #28c76f;
        }

        .status-box.error {
            border-left: 4px solid #ea5455;
        }

        .status-box.loading {
            /*border-left: 4px solid var(--theme-color);*/
        }

        .spinner-border {
            display: inline-block;
            width: 3rem;
            height: 3rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
            color: var(--theme-color);
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        @keyframes spinner-border {
            100% {
                transform: rotate(360deg);
            }
        }

        .debug-info {
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            text-align: left;
        }

        .debug-info h5 {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .debug-info p {
            margin-bottom: 0.3rem;
        }

        .debug-info .label {
            font-weight: 500;
            color: #555;
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
            .portal-container {
                padding: 1.5rem;
            }
            
            .location-logo {
                height: 60px;
            }
            
            .brand-logo {
                height: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Language Switcher -->
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

        <!-- Status Container (Loading, Success, Error) -->
        <div id="status-container">
            <!-- Loading State (Default) -->
            <div id="loading-status" class="status-box loading">
                <div class="spinner-border mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h4 class="mb-2" data-i18n="processingTitle">Processing Your Connection</h4>
                <p class="mb-0" data-i18n="processingMsg">Please wait while we authenticate your WiFi access...</p>
            </div>
            
            <!-- Success State (Hidden by Default) -->
            <div id="success-status" class="status-box loading" style="display: none;">
                <div class="success-icon mb-3">
                    <i class="fa fa-check-circle" style="font-size: 48px; color: #28c76f;"></i>
                </div>
                <h4 class="mb-2" id="success-title">Authentication Successful</h4>
                <p class="mb-3" id="success-message">Your Google login was verified successfully.<br>Connecting to WiFi network...</p>
                <div id="redirect-container"></div>
            </div>
            
            <!-- Error State (Hidden by Default) -->
            <div id="error-status" class="status-box error" style="display: none;">
                <div class="error-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
                <h4 class="mb-2" id="error-title">Connection Failed</h4>
                <p id="error-message" class="mb-0">There was a problem connecting to the WiFi network.</p>
            </div>
        </div>
        
        <!-- Debug Information (Hidden by Default) -->
        <div id="debug-info" class="debug-info" style="display: none;">
            <h5>Connection Details</h5>
            <p><span class="label">Location ID:</span> <span id="location-id">--</span></p>
            <p><span class="label">MAC Address:</span> <span id="mac-address">--</span></p>
            <p><span class="label">Challenge:</span> <span id="challenge">--</span></p>
            <p><span class="label">Response:</span> <span id="api-response">--</span></p>
        </div>

        <!-- Footer with Brand Logo and Terms -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-text" data-i18n-default="footer">
                Powered by Monsieur WiFi
            </div>
        </div>
    </div>

    <!-- Modals for Terms and Privacy -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="terms-content">
                        By accessing this WiFi service, you agree to comply with all applicable laws and the network's acceptable use policy.
                        We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="privacy-content">
                        We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data.
                        This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements.
                        We do not sell your personal information to third parties.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script>
        // Translations
        const translations = {
            en: {
                processingTitle: 'Processing Your Connection',
                processingMsg: 'Please wait while we authenticate your WiFi access...',
                authSuccess: 'Authentication Successful',
                authSuccessMsg: 'Your Google login was verified successfully.',
                connectingWifi: 'Connecting to WiFi',
                connectingWifiMsg: 'Initializing your connection to the WiFi network...',
                continueWifi: 'Continue to WiFi',
                connectionFailed: 'Connection Failed',
                footer: 'Powered by Monsieur WiFi'
            },
            fr: {
                processingTitle: 'Traitement de Votre Connexion',
                processingMsg: 'Veuillez patienter pendant que nous authentifions votre accès WiFi...',
                authSuccess: 'Authentification Réussie',
                authSuccessMsg: 'Votre connexion Google a été vérifiée avec succès.',
                connectingWifi: 'Connexion au WiFi',
                connectingWifiMsg: 'Initialisation de votre connexion au réseau WiFi...',
                continueWifi: 'Continuer vers le WiFi',
                connectionFailed: 'Échec de la Connexion',
                footer: 'Propulsé par Monsieur WiFi'
            }
        };

        // Get user's language preference
        function getLanguage() {
            const stored = localStorage.getItem('wifiPortalLanguage');
            if (stored) return stored;
            
            const browserLang = navigator.language || navigator.userLanguage;
            return browserLang.startsWith('fr') ? 'fr' : 'en';
        }

        function guestPortalClientHints() {
            try {
                return {
                    os: localStorage.getItem('wifiPortalOs') || '',
                    device_type: localStorage.getItem('wifiPortalDeviceType') || '',
                };
            } catch (_e) {
                return { os: '', device_type: '' };
            }
        }

        // Apply translations
        function applyTranslations(lang) {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    el.textContent = translations[lang][key];
                }
            });
            
            document.querySelectorAll('[data-i18n-default]').forEach(el => {
                const key = el.getAttribute('data-i18n-default');
                if (translations[lang] && translations[lang][key] && !el.hasAttribute('data-is-custom')) {
                    el.textContent = translations[lang][key];
                }
            });
            
            // Update active language button
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
            });
        }

        // Switch language
        function switchLanguage(lang) {
            localStorage.setItem('wifiPortalLanguage', lang);
            applyTranslations(lang);
        }

        // Initialize language
        document.addEventListener('DOMContentLoaded', function() {
            const currentLang = getLanguage();
            applyTranslations(currentLang);
            
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchLanguage(this.getAttribute('data-lang'));
                });
            });
        });

        $(document).ready(function() {
            console.log('Google callback page loaded');
            
            // Function to apply theme color
            function applyThemeColor(themeColor) {
                if (themeColor) {
                    document.documentElement.style.setProperty('--theme-color', themeColor);
                    
                    // Create a darker version for hover states
                    const darkerColor = createDarkerColor(themeColor);
                    document.documentElement.style.setProperty('--theme-color-dark', darkerColor);
                }
            }
            
            // Apply theme color from localStorage if available
            const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
            if (locationData.design && locationData.design.theme_color) {
                applyThemeColor(locationData.design.theme_color);
            }
            
            // Apply logo from localStorage if available
            if (locationData.design && locationData.design.location_logo_path) {
                $('#location-logo').html(`<img src="/storage/${locationData.design.location_logo_path}" alt="Location Logo">`);
            }
            
            // Apply terms from localStorage if available
            if (locationData.settings && locationData.settings.terms_enabled) {
                $('#terms-text').html('By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>');
            }
            
            // Set custom terms and privacy content if available
            if (locationData.design && locationData.design.terms_of_service) {
                $('#terms-content').html(locationData.design.terms_of_service);
            }
            
            if (locationData.design && locationData.design.privacy_policy) {
                $('#privacy-content').html(locationData.design.privacy_policy);
            }
            
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const state = urlParams.get('state');
            const code = urlParams.get('code');
            const error = urlParams.get('error');
            
            console.log('Code present:', !!code);
            console.log('State present:', !!state);
            console.log('Error present:', !!error);
            
            // Handle error from OAuth provider
            if (error) {
                showError('Authentication Failed', 'There was an error during Google authentication: ' + error);
                return;
            }
            
            // Check if required parameters are present
            if (!state || !code) {
                showError('Missing Parameters', 'Required authentication parameters are missing.');
                return;
            }
            
            try {
                // Parse the state parameter (contains the original login URL with MAC and location)
                const stateObj = JSON.parse(decodeURIComponent(state));
                console.log('State object:', stateObj);
                
                if (!stateObj.login_url) {
                    showError('Invalid State', 'The authentication state parameter is invalid.');
                    return;
                }
                
                // Extract location and MAC from login_url
                // Expected format: /social-login/google/{location_id}/{mac_address}
                const loginUrlPath = new URL(stateObj.login_url).pathname;
                const pathParts = loginUrlPath.split('/').filter(part => part.length > 0);
                
                console.log('Login URL path:', loginUrlPath);
                console.log('Path parts:', pathParts);
                
                let locationId, macAddress;
                
                if (pathParts.length >= 4 && pathParts[0] === 'social-login' && pathParts[1] === 'google') {
                    locationId = pathParts[2];
                    macAddress = pathParts[3];
                    console.log('Extracted from social-login path - Location:', locationId, 'MAC:', macAddress);
                } else if (pathParts.length >= 3 && pathParts[0] === 'google-login') {
                    locationId = pathParts[1];
                    macAddress = pathParts[2];
                    console.log('Extracted from google-login path - Location:', locationId, 'MAC:', macAddress);
                }
                
                // Update debug info
                $('#location-id').text(locationId || 'Not found');
                $('#mac-address').text(macAddress || 'Not found');
                
                if (!locationId || !macAddress) {
                    showError('Information Missing', 'Could not determine your location or device information.');
                    $('#debug-info').show();
                    return;
                }
                
                // Get challenge from localStorage
                const challenge = localStorage.getItem('challenge');
                console.log('Challenge from localStorage:', challenge);
                $('#challenge').text(challenge || 'Not found');
                
                if (!challenge) {
                    console.log('Challenge not found in localStorage, fetching location info...');
                    // If challenge is missing, try to get location information first
                    $.ajax({
                        url: `/api/captive-portal/${locationId}/info`,
                        type: 'GET',
                        data: { mac_address: macAddress },
                        headers: { 'Accept': 'application/json' },
                        success: function(locationInfo) {
                            console.log('Location info response:', locationInfo);
                            
                            if (locationInfo.success && locationInfo.location) {
                                // Store the challenge and other important data
                                localStorage.setItem('location_data', JSON.stringify(locationInfo.location));
                                localStorage.setItem('challenge', locationInfo.location.challenge);
                                
                                const updatedChallenge = locationInfo.location.challenge;
                                $('#challenge').text(updatedChallenge);
                                
                                // Apply location-specific design if available
                                if (locationInfo.location.design) {
                                    if (locationInfo.location.design.location_logo_path) {
                                        $('#location-logo').html(`<img src="/storage/${locationInfo.location.design.location_logo_path}" alt="Location Logo">`);
                                    }
                                    
                                    if (locationInfo.location.design.theme_color) {
                                        applyThemeColor(locationInfo.location.design.theme_color);
                                    }
                                }
                                
                                // Now that we have the challenge, proceed with guest login
                                proceedWithGuestLogin(locationId, macAddress, updatedChallenge, code);
                            } else {
                                showError('Location Not Found', 'Could not retrieve location information for WiFi access.');
                                $('#debug-info').show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching location info:', error);
                            showError('Connection Error', 'Failed to retrieve location information. Please try again.');
                            $('#debug-info').show();
                            $('#api-response').text(xhr.responseText || error);
                        }
                    });
                } else {
                    // We have the challenge, proceed with guest login
                    proceedWithGuestLogin(locationId, macAddress, challenge, code);
                }
                
            } catch (e) {
                console.error('Error processing callback parameters:', e);
                showError('Invalid Parameters', 'Could not process the authentication response.');
                $('#api-response').text(e.toString());
                $('#debug-info').show();
            }
            
            // Function to proceed with guest login using Google authentication
            function proceedWithGuestLogin(locationId, macAddress, challenge, googleCode) {
                console.log('Proceeding with guest login:');
                console.log('- Location ID:', locationId);
                console.log('- MAC Address:', macAddress);
                console.log('- Challenge:', challenge);
                console.log('- Google Code:', googleCode ? 'Present (length: ' + googleCode.length + ')' : 'Missing');
                var ip_address = localStorage.getItem('nas_ip');
                // Call the guest/login API with social auth (Google)
                $.ajax({
                    url: '/api/guest/login',
                    type: 'POST',
                    data: $.extend({
                        location_id: parseInt(localStorage.getItem('location_id') || '0', 10),
                        mac_address: macAddress, 
                        challenge: challenge,
                        login_method: 'social',
                        social_platform: 'google',
                        social_auth_code: googleCode,
                        ip_address: ip_address
                    }, guestPortalClientHints()),
                    headers: { 'Accept': 'application/json' },
                    success: function(response) {
                        console.log('Guest login response:', response);
                        $('#api-response').text(JSON.stringify(response));
                        
                        const lang = getLanguage();
                        if (response.success) {
                            // First part: Show authentication success
                            $('#loading-status').hide();
                            $('#error-status').hide();
                            $('#success-status').show();
                            
                            // Set initial success message
                            $('#success-title').text(translations[lang].authSuccess);
                            $('#success-message').text(translations[lang].authSuccessMsg);
                            
                            // After a short delay, show the second part
                            setTimeout(function() {
                                // Update to connection message
                                $('#success-title').text(translations[lang].connectingWifi);
                                $('#success-message').text(translations[lang].connectingWifiMsg);
                                
                                // Change icon to WiFi
                                $('#success-status .success-icon i').removeClass('fa-check-circle').addClass('fa-wifi');
                                
                                // If there's a redirect URL in the response, handle it after another delay
                                if (response.login_url || response.redirect_url) {
                                    const redirectUrl = response.login_url || response.redirect_url;
                                    console.log('Redirect URL provided:', redirectUrl);
                                    
                                    // Add continue button if needed
                                    if (!$('#redirect-container').html()) {
                                        $('#redirect-container').html(`
                                            <a href="${redirectUrl}" class="login-button btn-success">
                                                <i class="fa fa-wifi"></i> ${translations[lang].continueWifi}
                                            </a>
                                        `);
                                    }
                                    
                                    // Set auto-redirect
                                    setTimeout(function() {
                                        window.location.href = redirectUrl;
                                    }, 1500);
                                }
                            }, 1500);
                        } else {
                            // Show error with the message from the response
                            showError(translations[lang].connectionFailed, response.message || 'There was an error connecting to the WiFi network.');
                            $('#debug-info').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Guest login error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMessage = 'There was an error processing your request.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                            $('#api-response').text(JSON.stringify(response));
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                            $('#api-response').text(xhr.responseText || error);
                        }
                        
                        showError('Connection Failed', errorMessage);
                        $('#debug-info').show();
                    }
                });
            }
            
            // Function to show error state
            function showError(title, message) {
                $('#loading-status').hide();
                $('#success-status').hide();
                $('#error-title').text(title);
                $('#error-message').text(message);
                $('#error-status').show();
            }
            
            // Helper function to create a darker color for hover states
            function createDarkerColor(hexColor) {
                // Remove # if present
                hexColor = hexColor.replace('#', '');
                
                // Parse the hex color
                let r = parseInt(hexColor.substr(0, 2), 16);
                let g = parseInt(hexColor.substr(2, 2), 16);
                let b = parseInt(hexColor.substr(4, 2), 16);
                
                // Make it darker by reducing each component
                r = Math.max(0, r - 25);
                g = Math.max(0, g - 25);
                b = Math.max(0, b - 25);
                
                // Convert back to hex
                return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
            }
            
            // Apply background image from localStorage if available
            if (locationData.design && locationData.design.background_image_path) {
                document.body.style.backgroundImage = `url('/storage/${locationData.design.background_image_path}')`;
            }
        });
    </script>
</body>
</html>