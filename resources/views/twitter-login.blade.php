<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social WiFi Login with Twitter/X</title>
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

        .twitter-button {
            background-color: #000;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px 28px;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
            width: 100%;
            margin: 1rem auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .twitter-button:hover {
            background-color: #333;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .twitter-button:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .twitter-button i {
            margin-right: 10px;
            font-size: 1.2rem;
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

        <!-- Welcome Text -->
        <div class="welcome-text" id="welcome-text" data-i18n-default="welcomeText">
            Connect to our WiFi network using your Twitter/X account.
        </div>

        <!-- Alert for messages -->
        <div id="alert-container" style="display: none;"></div>

        <!-- Twitter Login -->
        <div id="login-container" class="login-container text-center mt-4">
            <!-- Twitter/X Login Button -->
            <button id="twitter-login-button" class="twitter-button">
                <i class="fa fa-twitter"></i> <span data-i18n="connectTwitter">Connect with Twitter/X</span>
            </button>
        </div>

        <!-- Footer with Brand Logo and Terms -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-text" data-i18n-default="footer">
                Powered by Monsieur WiFi
            </div>
        </div>
    </div>

    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    
    <script>
        // Translations
        const translations = {
            en: {
                welcomeText: 'Connect to our WiFi network using your Twitter/X account.',
                connectTwitter: 'Connect with Twitter/X',
                footer: 'Powered by Monsieur WiFi',
                connecting: 'Connecting...',
                missingParams: 'Missing required parameters (location ID or MAC address)',
                errorMissing: 'Required information is missing. Please check your connection or contact support.'
            },
            fr: {
                welcomeText: 'Connectez-vous à notre réseau WiFi en utilisant votre compte Twitter/X.',
                connectTwitter: 'Se connecter avec Twitter/X',
                footer: 'Propulsé par Monsieur WiFi',
                connecting: 'Connexion...',
                missingParams: 'Paramètres requis manquants (ID de localisation ou adresse MAC)',
                errorMissing: 'Informations requises manquantes. Veuillez vérifier votre connexion ou contacter le support.'
            }
        };

        // Get user's language preference
        function getLanguage() {
            const stored = localStorage.getItem('wifiPortalLanguage');
            if (stored) return stored;
            
            const browserLang = navigator.language || navigator.userLanguage;
            return browserLang.startsWith('fr') ? 'fr' : 'en';
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
            console.log('Document ready, initializing Twitter login page');
            console.log('Full URL:', window.location.href);
            console.log('Path:', window.location.pathname);
            
            // Quick check for social-login pattern
            const path = window.location.pathname;
            const isSocialLogin = path.includes('social-login/twitter');
            console.log('Is social login pattern:', isSocialLogin);
            
            // Get location data from localStorage
            const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
            const locationSettings = locationData.settings || {};
            
            // Get design data - use the full design object from the response if available
            const designData = locationData.design || {};
            console.log('Location data:', locationData);
            console.log('Design data:', designData);
            
            // Get URL parameters (for mac address, etc.)
            const urlParams = new URLSearchParams(window.location.search);
            let macAddress = urlParams.get('mac') || getPathParameter('mac_address');
            let locationId = getPathParameter('location');
            
            // Fallback: Extract location and MAC directly from URL path segments
            if (!locationId || !macAddress) {
                const pathSegments = path.split('/').filter(segment => segment.length > 0);
                console.log('Path segments:', pathSegments);
                
                // Special handling for social-login/twitter pattern
                if (path.includes('social-login/twitter') && pathSegments.length >= 4) {
                    locationId = locationId || pathSegments[2];
                    macAddress = macAddress || pathSegments[3];
                    console.log('Detected social-login pattern. Location:', locationId, 'MAC:', macAddress);
                } else if (pathSegments.length >= 2) {
                    // Typically the format would be /twitter-login/{location}/{mac}
                    locationId = locationId || pathSegments[1];
                    macAddress = macAddress || pathSegments[2];
                }
                
                // If still not found, try to get MAC from query string
                if (!macAddress) {
                    const queryString = window.location.search;
                    const urlParamsAll = new URLSearchParams(queryString);
                    macAddress = urlParamsAll.get('mac') || urlParamsAll.get('client_mac') || macAddress;
                }
            }
            
            console.log('Location ID:', locationId);
            console.log('MAC Address:', macAddress);
            
            // Apply design settings
            applyDesignSettings(locationSettings, designData);
            
            // Handle Twitter login button click
            $('#twitter-login-button').on('click', function () {
                console.log('Twitter login button clicked');

                if (!locationId || !macAddress) {
                    const lang = getLanguage();
                    showAlert(translations[lang].missingParams, 'danger');
                    console.error('Missing parameters - Location ID:', locationId, 'MAC Address:', macAddress);
                    return;
                }

                const $button = $(this);
                const originalText = $button.html();
                const lang = getLanguage();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + translations[lang].connecting)
                    .prop('disabled', true);

                // Get current URL as login_url for callback redirection
                let login_url = window.location.href;
                // Remove any fragments from the URL
                login_url = login_url.split('#')[0];
                
                console.log('Setting login_url in state:', login_url);
                
                // Simple state object with just the login_url
                const loginData = {
                    login_url: login_url
                };

                console.log('Twitter login state data:', loginData);
                
                // Construct Twitter OAuth URL
                const twitterLoginUrl = 'https://twitter.com/i/oauth2/authorize' +
                '?response_type=code' +
                '&client_id=OUdHSEZhaXNXMTlBR0MtOXJMb0Q6MTpjaQ' +
                '&redirect_uri=https%3A%2F%2Fmrwifi.cnctdwifi.com%2Fsocial-login%2Ftwitter-callback' +
                '&scope=tweet.read%20users.read%20offline.access' +
                '&state=' + encodeURIComponent(JSON.stringify(loginData)) + 
                '&code_challenge=challenge123' + 
                '&code_challenge_method=plain';

                console.log('Redirecting to Twitter auth URL:', twitterLoginUrl);
                window.location.href = twitterLoginUrl;
            });
            
            // Verify Twitter button is present
            if ($('#twitter-login-button').length) {
                console.log('Twitter button found in DOM');
            } else {
                console.error('Twitter button not found in DOM');
            }
            
            // Function to show alerts
            function showAlert(message, type) {
                $('#alert-container').html(`
                    <div class="alert alert-${type}" role="alert">
                        ${message}
                    </div>
                `).show();
                
                // Auto-hide success alerts after 5 seconds
                if (type === 'success') {
                    setTimeout(function() {
                        $('#alert-container').fadeOut();
                    }, 5000);
                }
            }
            
            // Function to apply design settings
            function applyDesignSettings(settings, design) {
                // Set theme color from full design data first, fallback to settings
                const themeColor = design.theme_color || settings.theme_color || getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim();
                if (themeColor) {
                    document.documentElement.style.setProperty('--theme-color', themeColor);
                    
                    // Create a darker version for hover states
                    const darkerColor = createDarkerColor(themeColor);
                    document.documentElement.style.setProperty('--theme-color-dark', darkerColor);
                }
                
                // Set background image from full design data
                if (design.background_image_path) {
                    document.body.style.backgroundImage = `url('/storage/${design.background_image_path}')`;
                }
                
                // Apply gradient to portal container if gradient colors are available
                if (design.background_color_gradient_start && design.background_color_gradient_end) {
                    $('.portal-container').css({
                        'background': `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`,
                        'background-image': `linear-gradient(135deg, ${design.background_color_gradient_start} 0%, ${design.background_color_gradient_end} 100%)`
                    });
                } else if (design.background_color_gradient_start || design.background_color_gradient_end) {
                    // If only one gradient color is set, use solid color
                    const color = design.background_color_gradient_start || design.background_color_gradient_end;
                    $('.portal-container').css({
                        'background': color,
                        'background-image': 'none'
                    });
                }
                
                // Set location logo from full design data
                if (design.location_logo_path) {
                    $('#location-logo').html(`<img src="/storage/${design.location_logo_path}" alt="Location Logo">`);
                }
                
                // Set welcome message from full design data, fallback to settings
                const welcomeMessage = design.welcome_message || settings.welcome_message;
                if (welcomeMessage) {
                    $('#welcome-text').text(welcomeMessage).attr('data-is-custom', 'true');
                    
                    // Add login instructions if available
                    const loginInstructions = design.login_instructions || '';
                    if (loginInstructions) {
                        $('#welcome-text').append(`<p class="mt-2">${loginInstructions}</p>`);
                    }
                }
                
                // Set terms visibility from full design data, fallback to settings
                const showTerms = design.show_terms || settings.terms_enabled;
                if (showTerms) {
                    $('#terms-text').html('By connecting, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>');
                }
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
            
            // Helper function to get parameter from URL path
            function getPathParameter(param) {
                const path = window.location.pathname;
                const pathParts = path.split('/');
                
                console.log('URL path:', path);
                console.log('Path parts:', pathParts);
                
                // Check for social-login/twitter pattern
                if (path.includes('social-login/twitter')) {
                    if (param === 'location') {
                        // URL pattern: /social-login/twitter/{location_id}/{mac_address}
                        return pathParts[3] || '';
                    } else if (param === 'mac_address') {
                        // URL pattern: /social-login/twitter/{location_id}/{mac_address}
                        return pathParts[4] || '';
                    }
                } else if (path.includes('twitter-login')) {
                    if (param === 'location') {
                        // Assuming URL pattern like /twitter-login/{location}/{mac_address}
                        return pathParts[2] || '';
                    } else if (param === 'mac_address') {
                        // Assuming URL pattern like /twitter-login/{location}/{mac_address}
                        return pathParts[3] || '';
                    }
                } else {
                    // Handle other route patterns
                    if (param === 'location') {
                        return pathParts[2] || '';
                    } else if (param === 'mac_address') {
                        return pathParts[3] || '';
                    }
                }
                
                return '';
            }
            
            // If location_id or mac_address is missing, show error
            if (!locationId || !macAddress) {
                const lang = getLanguage();
                $('.portal-container').html(`
                    <div class="text-center">
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Error</h4>
                            <p>${translations[lang].errorMissing}</p>
                        </div>
                    </div>
                `);
            }
            
            // Get location information
            $.ajax({
                url: `/api/captive-portal/${locationId}/info`,
                type: 'GET',
                data: { mac_address: macAddress },
                headers: { 'Accept': 'application/json' },
                success: function(locationInfo) {
                    console.log('Location info:', locationInfo);
                    
                    // Store location data in localStorage for future use
                    if (locationInfo.success && locationInfo.location) {
                        // Store the challenge and other important data
                        localStorage.setItem('location_data', JSON.stringify(locationInfo.location));
                        localStorage.setItem('challenge', locationInfo.location.challenge);
                        
                        // Apply design settings again with fresh data
                        applyDesignSettings(
                            locationInfo.location.settings || {}, 
                            locationInfo.location.design || {}
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching location info:', error);
                }
            });
        });
    </script>
</body>
</html>