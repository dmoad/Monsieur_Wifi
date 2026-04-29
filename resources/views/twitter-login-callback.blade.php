<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Login Callback</title>
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

        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            line-height: 1.6;
        }

        .spinner-container {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
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
    </style>
</head>
<body>
    <!-- Language Switcher -->
    <div class="language-switcher">
        <button class="language-btn" data-lang="en">English</button>
        <button class="language-btn" data-lang="fr">Français</button>
    </div>
    <div class="portal-container">
        <div class="welcome-text">
            <h3 data-i18n="processingTitle">Processing Twitter Login</h3>
            <p data-i18n="processingMsg">Please wait while we connect you to the WiFi network...</p>
        </div>

        <div id="alert-container" style="display: none;"></div>

        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" data-i18n-default="footer">
                Powered by Monsieur WiFi
            </div>
        </div>
    </div>

    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    
    <script>
        // Translations
        const translations = {
            en: {
                processingTitle: 'Processing Twitter Login',
                processingMsg: 'Please wait while we connect you to the WiFi network...',
                footer: 'Powered by Monsieur WiFi',
                authSuccess: 'Successfully authenticated! Connecting to WiFi...',
                authFailed: 'Failed to complete Twitter authentication',
                returnToLogin: 'Return to Login Page'
            },
            fr: {
                processingTitle: 'Traitement de la Connexion Twitter',
                processingMsg: 'Veuillez patienter pendant que nous vous connectons au réseau WiFi...',
                footer: 'Propulsé par Monsieur WiFi',
                authSuccess: 'Authentification réussie! Connexion au WiFi...',
                authFailed: 'Échec de l\'authentification Twitter',
                returnToLogin: 'Retour à la Page de Connexion'
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
            console.log('Twitter callback page loaded');
            
            // Parse URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const stateParam = urlParams.get('state');
            const codeParam = urlParams.get('code');

            console.log('State parameter:', stateParam);
            console.log('Code parameter:', codeParam);
            var client_token = codeParam;

            // Decode the state parameter
            const decodedState = decodeURIComponent(stateParam);
            console.log('Decoded state:', decodedState);

            // Parse login_url from the state
            // Format is like: {login_url:https://mrwifi.cnctdwifi.com/social-login/twitter/10/A4-CF-99-5C-4E-43}
            let login_url = '';
            
            try {
                // Try to parse as JSON first
                const jsonData = JSON.parse(decodedState);
                login_url = jsonData.login_url;
                console.log('Parsed login_url from JSON:', login_url);
            } catch (e) {
                console.log('Not valid JSON, trying alternate parsing:', e);
                
                // Try to extract using regex
                const urlMatch = decodedState.match(/login_url:([^}]+)/);
                if (urlMatch && urlMatch[1]) {
                    login_url = urlMatch[1];
                    console.log('Extracted login_url using regex:', login_url);
                } else {
                    // Last resort, manual parsing
                    const cleanState = decodedState.replace(/[{}]/g, ''); // Remove braces
                    const parts = cleanState.split(':');
                    if (parts.length >= 2 && parts[0] === 'login_url') {
                        login_url = parts.slice(1).join(':'); // Rejoin in case URL has colons
                        console.log('Extracted login_url using manual parsing:', login_url);
                    }
                }
            }
            
            if (!login_url) {
                console.error('Failed to extract login_url from state parameter');
                showAlert('Invalid state parameter', 'danger');
                return;
            }
            
            console.log('Final login_url:', login_url);
            
            // Extract location_id and mac_address from login_url
            try {
                const url = new URL(login_url);
                const pathParts = url.pathname.split('/').filter(part => part.length > 0);
                console.log('Path parts from login_url:', pathParts);
                
                if (pathParts.length >= 4 && pathParts[0] === 'social-login' && pathParts[1] === 'twitter') {
                    // new:    /social-login/twitter/{network_id}/{zone_id}/{mac_address}  (5 parts)
                    // legacy: /social-login/twitter/{network_id}/{mac_address}            (4 parts)
                    const hasZone     = pathParts.length >= 5;
                    const location_id = pathParts[2];
                    const zone_id     = hasZone ? pathParts[3] : '0';
                    const mac_address = hasZone ? pathParts[4] : pathParts[3];

                    console.log('Extracted location_id:', location_id);
                    console.log('Extracted zone_id:', zone_id);
                    console.log('Extracted mac_address:', mac_address);

                    // Now call the API with this information
                    completeTwitterAuth(location_id, zone_id, mac_address, client_token, login_url);
                } else {
                    console.error('Invalid login_url format');
                    showAlert('Invalid login URL format', 'danger');
                }
            } catch (e) {
                console.error('Error parsing login_url:', e);
                showAlert('Error processing login information', 'danger');
                
                // Add a button to go back to login_url if we have it
                if (login_url) {
                    showReturnButton(login_url);
                }
            }
        });
        
        // Function to show alerts
        function showAlert(message, type) {
            $('#alert-container').html(`
                <div class="alert alert-${type}" role="alert">
                    ${message}
                </div>
            `).show();
            
            if (type === 'danger') {
                $('.spinner-container').hide();
            }
        }
        
        // Function to show return button
        function showReturnButton(url) {
            const lang = getLanguage();
            $('#alert-container').append(`
                <div class="text-center mt-3">
                    <a href="${url}" class="btn btn-primary">
                        ${translations[lang].returnToLogin}
                    </a>
                </div>
            `);
        }
        
        // Function to complete Twitter authentication
        function completeTwitterAuth(location_id, zone_id, mac_address, code, login_url) {
            console.log('Completing Twitter auth with data:');
            console.log('Location ID:', location_id);
            console.log('Zone ID:', zone_id);
            console.log('MAC Address:', mac_address);
            console.log('Code:', code);
            var challenge = localStorage.getItem('challenge');
            var nas_ip = localStorage.getItem('nas_ip');
            // Prepare data for the API call
            var login_data = $.extend({
                    location_id: parseInt(localStorage.getItem('location_id') || '0', 10),
                    zone_id:     zone_id,
                    mac_address: mac_address,
                    login_method: 'social',
                    social_platform: 'twitter',
                    code: code,
                    challenge: challenge,
                    ip_address: nas_ip
                }, guestPortalClientHints());
                
                console.log('Login data:', login_data);
                
                // Call the login API with password authentication
                $.ajax({
                    url: '/api/guest/login',
                    method: 'POST',
                    data: login_data,
                    success: function(response) {
                    console.log('Auth completion response:', response);
                    const lang = getLanguage();
                    
                    if (response.success) {
                        showAlert(translations[lang].authSuccess, 'success');
                        
                        // Redirect to success URL or final URL
                        setTimeout(function() {
                            const redirectUrl = response.login_url || response.redirect_url;
                            alert(redirectUrl);
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else {
                                // Fallback to the original login_url
                                window.location.href = login_url;
                            }
                        }, 2000);
                    } else {
                        showAlert(response.message || 'Failed to complete authentication', 'danger');
                        showReturnButton(login_url);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Auth completion error:', xhr.responseJSON || xhr.responseText);
                    const lang = getLanguage();
                    
                    let errorMessage = translations[lang].authFailed;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showAlert(errorMessage, 'danger');
                    showReturnButton(login_url);
                }
            });
        }
    </script>
</body>
</html> 