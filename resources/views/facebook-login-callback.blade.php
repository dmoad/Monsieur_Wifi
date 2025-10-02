<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social WiFi Login with Facebook</title>
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

        .status-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 1rem;
            color: var(--theme-color);
        }

        .welcome-text, .status-message {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            line-height: 1.6;
            font-size: 1.2rem;
        }

        .facebook-button {
            background-color: #1877f2;
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

        .footer {
            margin-top: 3rem;
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

        <!-- Alert for messages -->
        <div id="alert-container" style="display: none;"></div>

        <!-- Status Container -->
        <div class="status-container">
            <div class="status-icon">
                <i class="spinner-border text-primary"></i>
            </div>
            <div class="status-message" id="status-message" data-i18n="connecting">
                Connecting you to our WiFi network...
            </div>
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
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    
    <script>
        // Translations
        const translations = {
            en: {
                connecting: 'Connecting you to our WiFi network...',
                authSuccess: 'Authentication Successful',
                authSuccessMsg: 'Authentication successful. Connecting to WiFi...',
                connectingWifi: 'Connecting to WiFi Network',
                footer: 'Powered by Monsieur WiFi',
                authFailed: 'Authentication failed',
                missingUrl: 'Error: Missing WiFi activation URL'
            },
            fr: {
                connecting: 'Connexion à notre réseau WiFi...',
                authSuccess: 'Authentification Réussie',
                authSuccessMsg: 'Authentification réussie. Connexion au WiFi...',
                connectingWifi: 'Connexion au Réseau WiFi',
                footer: 'Propulsé par Monsieur WiFi',
                authFailed: 'Échec de l\'authentification',
                missingUrl: 'Erreur : URL d\'activation WiFi manquante'
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
            console.log('Document ready, initializing Facebook callback page');
            
            // Extract parameters from URL
            const urlParams = new URLSearchParams(window.location.search);
            const fbCode = urlParams.get('code');
            
            // Extract state parameter and parse it
            let stateParam = urlParams.get('state');
            let locationId, macAddress;
            
            // Try to parse the state as JSON
            if (stateParam) {
                try {
                    // Decode URI encoded state and parse as JSON
                    const stateObj = JSON.parse(decodeURIComponent(stateParam));
                    console.log('State object:', stateObj);
                    
                    // Extract login_url which contains location_id and mac_address
                    if (stateObj && stateObj.login_url) {
                        const loginUrl = stateObj.login_url;
                        
                        // Parse login_url to extract location_id and mac_address
                        // Expected format: /social-login/facebook/{location_id}/{mac_address}
                        const urlParts = loginUrl.split('/');
                        if (urlParts.length >= 2) {
                            // Get the last two segments of the URL
                            locationId = urlParts[urlParts.length - 2];
                            macAddress = urlParts[urlParts.length - 1];
                        }
                    }
                } catch (e) {
                    console.error('Error parsing state parameter:', e);
                }
            }
            
            console.log('Extracted parameters:', { 
                fbCode, 
                locationId, 
                macAddress,
                stateParam
            });
            
            // Get stored data from localStorage
            const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
            console.log('Location data:', locationData);
            $('#terms-content').html(locationData.design.terms_content);
            $('#privacy-content').html(locationData.design.privacy_content);
            const challenge = localStorage.getItem('challenge');
            const ipAddress = localStorage.getItem('nas_ip');
            
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
            
            // Update status message
            function updateStatus(message, isError = false) {
                $('#status-message').text(message).removeAttr('data-i18n');
                if (isError) {
                    $('.status-icon').html('<i class="fa fa-times-circle text-danger" style="font-size: 48px;"></i>');
                    showAlert(message, 'danger');
                } else {
                    $('.status-icon').html('<i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>');
                }
            }
            
            // Check if we have all required parameters
            if (!fbCode || !locationId || !macAddress || !challenge || !ipAddress) {
                updateStatus('Missing required parameters. Please try again.', true);
                console.error('Missing parameters:', { 
                    fbCode, locationId, macAddress, challenge, ipAddress 
                });
                return;
            }
            
            // Prepare login data for Facebook social login
            const loginData = {
                location_id: locationId,
                mac_address: macAddress,
                login_method: 'social',
                social_platform: 'facebook',
                social_token: fbCode,
                name: '', // We could fetch this from Facebook API if needed
                email: '', // We could fetch this from Facebook API if needed
                challenge: challenge,
                ip_address: ipAddress
            };
            
            console.log('Login data:', loginData);
            
            // Call the login API with Facebook authentication
            $.ajax({
                url: '/api/guest/login',
                method: 'POST',
                data: loginData,
                success: function(response) {
                    console.log('Login response:', response);
                    const lang = getLanguage();
                    if (response.success) {
                        // First part: Show initial success verification message
                        updateStatus(translations[lang].authSuccess);
                        $('.status-icon').html('<i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>');
                        
                        // Show alert for facebook auth success
                        showAlert(translations[lang].authSuccessMsg, 'success');
                        
                        // After a short delay, show the second part of the message
                        setTimeout(function() {
                            // Second part: Update to connection message
                            updateStatus(translations[lang].connectingWifi);
                            $('.status-icon').html('<i class="fa fa-wifi text-primary" style="font-size: 48px;"></i>');
                            
                            // Redirect to success page or Internet after a further delay
                            setTimeout(function() {
                                const redirectUrl = response.login_url;
                                if (redirectUrl) {
                                    // Final update before redirecting
                                    updateStatus(lang === 'fr' ? 'Redirection vers le réseau WiFi...' : 'Redirecting to WiFi network...');
                                    
                                    console.log('Redirecting to WiFi authentication URL:', redirectUrl);
                                    window.location.href = redirectUrl;
                                } else {
                                    updateStatus(translations[lang].missingUrl, true);
                                }
                            }, 1500);
                        }, 1500);
                    } else {
                        updateStatus(translations[lang].authFailed + ': ' + (response.message || 'Unknown error'), true);
                    }
                },
                error: function(xhr) {
                    const lang = getLanguage();
                    let errorMessage = translations[lang].authFailed;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    updateStatus(errorMessage, true);
                    console.error('Error:', xhr);
                }
            });
            
            // Apply design settings
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
                
                // Set location logo from full design data
                if (design.location_logo_path) {
                    $('#location-logo').html(`<img src="/storage/${design.location_logo_path}" alt="Location Logo">`);
                }
                
                // Set terms visibility from full design data, fallback to settings
                const showTerms = design.show_terms || settings.terms_enabled;
                if (showTerms) {
                    $('#terms-text').html('By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>');
                }
                
                // Set custom terms and privacy content if available
                if (design.terms_of_service) {
                    $('#terms-content').html(design.terms_of_service);
                }
                
                if (design.privacy_policy) {
                    $('#privacy-content').html(design.privacy_policy);
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
            
            // Apply design settings if available
            if (locationData && locationData.settings) {
                applyDesignSettings(
                    locationData.settings || {}, 
                    locationData.design || {}
                );
            }
            
            // Get location information if needed
            if (locationId) {
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
            }
        });
    </script>
</body>
</html>