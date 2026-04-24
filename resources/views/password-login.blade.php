<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi Login with Password</title>
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
            Please enter the password to connect to our WiFi network.
        </div>

        <!-- Alert for messages -->
        <div id="alert-container" style="display: none;"></div>

        <!-- Password Login Form -->
        <div id="password-form" class="password-container">
            <form id="password-login-form">
                <div class="form-group">
                    <label for="password" data-i18n="passwordLabel">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" data-i18n-placeholder="passwordPlaceholder" placeholder="Enter network password" required>
                        <div class="input-group-append">
                            <span class="input-group-text" id="toggle-password">
                                <i class="fa fa-eye-slash" id="eye-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="login-button" id="connect-button" data-i18n-default="connectButton"></button>
            </form>
        </div>

        <!-- Footer with Brand Logo and Terms -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-links" style="display: none; margin-bottom: 0.5rem;">
                <!-- Terms links will be inserted here when show_terms is enabled -->
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

    <!-- Bootstrap JS -->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    
    <script>
        // Language system - Initialize before DOM ready
        const translations = {
            en: {
                welcomeText: 'Please enter the password to connect to our WiFi network.',
                passwordLabel: 'Password',
                passwordPlaceholder: 'Enter network password',
                connectButton: 'Connect to WiFi',
                footer: 'Powered by Monsieur WiFi',
                connecting: 'Connecting...',
                passwordVerified: 'Password Verified!',
                connectingWifi: 'Connecting to WiFi...',
                incorrectPassword: 'Incorrect Password',
                verificationFailed: 'Verification Failed',
                enterPassword: 'Please enter the network password',
                errorMissing: 'Required information is missing. Please check your connection or contact support.',
                termsText: 'By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>'
            },
            fr: {
                welcomeText: 'Veuillez entrer le mot de passe pour vous connecter à notre réseau WiFi.',
                passwordLabel: 'Mot de passe',
                passwordPlaceholder: 'Entrez le mot de passe du réseau',
                connectButton: 'Se connecter au WiFi',
                footer: 'Propulsé par Monsieur WiFi',
                connecting: 'Connexion...',
                passwordVerified: 'Mot de passe vérifié !',
                connectingWifi: 'Connexion au WiFi...',
                incorrectPassword: 'Mot de passe incorrect',
                verificationFailed: 'Échec de la vérification',
                enterPassword: 'Veuillez entrer le mot de passe du réseau',
                errorMissing: 'Informations requises manquantes. Veuillez vérifier votre connexion ou contacter le support.',
                termsText: 'En vous connectant, vous acceptez nos <a href="#" data-toggle="modal" data-target="#termsModal">Conditions de service</a> et notre <a href="#" data-toggle="modal" data-target="#privacyModal">Politique de confidentialité</a>'
            }
        };

        function getLanguage() {
            let lang = localStorage.getItem('wifiPortalLanguage');
            if (lang && (lang === 'en' || lang === 'fr')) {
                return lang;
            }
            const browserLang = navigator.language || navigator.userLanguage;
            const langCode = browserLang.toLowerCase().split('-')[0];
            return (langCode === 'fr') ? 'fr' : 'en';
        }

        function applyTranslations(lang) {
            // Update elements with data-i18n attribute
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
            // Update elements with data-i18n-default (only if no custom content)
            document.querySelectorAll('[data-i18n-default]').forEach(element => {
                const key = element.getAttribute('data-i18n-default');
                const isDefault = element.getAttribute('data-is-custom') !== 'true';
                if (isDefault && translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
            // Update placeholders
            document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
                const key = element.getAttribute('data-i18n-placeholder');
                if (translations[lang] && translations[lang][key]) {
                    element.placeholder = translations[lang][key];
                }
            });
            
            // Update terms links if they are visible
            const termsLinks = document.getElementById('terms-links');
            if (termsLinks && termsLinks.style.display !== 'none') {
                termsLinks.innerHTML = translations[lang].termsText;
            }
            
            // Update active button
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-lang') === lang) {
                    btn.classList.add('active');
                }
            });
        }

        function switchLanguage(lang) {
            if (lang === 'en' || lang === 'fr') {
                localStorage.setItem('wifiPortalLanguage', lang);
                applyTranslations(lang);
            }
        }

        // Initialize language switcher
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchLanguage(this.getAttribute('data-lang'));
                });
            });
        });

        // Apply translations immediately
        const currentLang = getLanguage();
        applyTranslations(currentLang);

        $(document).ready(function() {
            // Get location data from localStorage
            const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
            const locationSettings = locationData.settings || {};
            
            // Get design data - use the full design object from the response if available
            const designData = locationData.design || {};
            console.log('Location data:', locationData);
            console.log('Design data:', designData);
            $('#terms-content').html(designData.terms_content);
            $('#privacy-content').html(designData.privacy_content);
            var button_text = designData.button_text || translations[currentLang].connectButton;
            if (designData.button_text) {
                $('#connect-button').text(button_text).attr('data-is-custom', 'true');
            } else {
                $('#connect-button').text(button_text);
            }
            
            // Get URL parameters (for mac address, etc.)
            const urlParams = new URLSearchParams(window.location.search);
            const networkId  = getPathParameter('location');
            const zoneId     = getPathParameter('zone_id');
            const macAddress = urlParams.get('mac') || getPathParameter('mac_address');
            // Set focus to password input
            $('#password').focus();
            // Apply design settings
            applyDesignSettings(locationSettings, designData);
            
            // Toggle password visibility
            $('#toggle-password').on('click', function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#eye-icon');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
            
            // Handle password form submission
            $('#password-login-form').on('submit', function(e) {
                e.preventDefault();
                
                const password = $('#password').val();
                const lang = getLanguage();
                
                if (!password) {
                    showAlert(translations[lang].enterPassword, 'danger');
                    return;
                }
                
                // Show loading state
                const $button = $('#connect-button');
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + translations[lang].connecting).prop('disabled', true);
                
                // Get location data and challenge
                const challenge = localStorage.getItem('challenge');
                const location_data = JSON.parse(localStorage.getItem('location_data') || '{}');
                const ipAddress = localStorage.getItem('nas_ip');
                
                // Prepare login data for password method
                var login_data = {
                    network_id: networkId,
                    zone_id:    zoneId,
                    mac_address: macAddress,
                    login_method: 'password',
                    password: password,
                    challenge: challenge,
                    ip_address: ipAddress
                }
                
                console.log('Login data:', login_data);
                
                // Call the login API with password authentication
                $.ajax({
                    url: '/api/guest/login',
                    method: 'POST',
                    data: login_data,
                    success: function(response) {
                        console.log('Login response:', response);
                        const lang = getLanguage();
                        var orginal_button_color = $button.css('background-color');
                        if (response.success) {
                            // Show first success part on button
                            $button.removeClass('btn-primary').addClass('btn-success')
                                .html(translations[lang].passwordVerified + ' <i class="fa fa-check"></i>')
                                .prop('disabled', true);
                            
                            // After a short delay, show the second part
                            setTimeout(function() {
                                $button.html(translations[lang].connectingWifi + ' <i class="fa fa-wifi"></i>');
                                
                                // Redirect after another delay
                                setTimeout(function() {
                                    const redirectUrl = response.login_url;
                                    window.location.href = redirectUrl;
                                }, 1500);
                            }, 1500);
                        } else {
                            // Show first error part on button
                            $button.removeClass('btn-primary').addClass('btn-danger')
                                .html('<i class="fa fa-times-circle"></i> ' + translations[lang].incorrectPassword)
                                .prop('disabled', false);
                                
                            // Show error in alert
                            // showAlert(response.message || 'Failed to connect', 'danger');
                            
                            // After a short delay, show the second part
                            setTimeout(function() {
                                $button.html(button_text).removeClass('btn-danger').css('background-color', orginal_button_color);
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        const lang = getLanguage();
                        // Show first error part on button
                        $button.removeClass('btn-primary').addClass('btn-danger')
                            .html('<i class="fa fa-exclamation-circle"></i> ' + translations[lang].verificationFailed)
                            .prop('disabled', false);
                        
                        // Show detailed error in alert
                        let errorMessage = translations[lang].verificationFailed;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        // showAlert(errorMessage, 'danger');
                        
                        // After a short delay, show the second part
                        setTimeout(function() {
                            $button.html(button_text).removeClass('btn-danger').css('background-color', orginal_button_color);
                            
                            // Set button's href to prelogin
                            if (ipAddress) {
                                $button.attr('href', `http://${ipAddress}:3990/prelogin`);
                            }
                        }, 1500);
                    }
                });
            });
            
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
                    const loginInstructions = design.login_instructions;
                    if (loginInstructions && loginInstructions.length > 1) {
                        $('#welcome-text').append(`<p class="mt-2">${loginInstructions}</p>`);
                    }
                }
                
                // Set terms visibility from full design data, fallback to settings
                const showTerms = design.show_terms === true || settings.terms_enabled === true;
                if (showTerms) {
                    const lang = getLanguage();
                    $('#terms-links').html(translations[lang].termsText).show();
                } else {
                    $('#terms-links').hide();
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
            
            // Helper function to get parameter from URL path.
            // Supports both URL patterns:
            //   new:    /password-login/{network_id}/{zone_id}/{mac_address}
            //   legacy: /password-login/{network_id}/{mac_address}
            function getPathParameter(param) {
                const pathParts = window.location.pathname.split('/');
                const hasZoneId = pathParts.length >= 5;

                if (param === 'location') {
                    return pathParts[2] || '';
                } else if (param === 'zone_id') {
                    return hasZoneId ? (pathParts[3] || '0') : '0';
                } else if (param === 'mac_address') {
                    return hasZoneId ? (pathParts[4] || '') : (pathParts[3] || '');
                }

                return '';
            }
            
            // If network_id or mac_address is missing, show error
            if (!networkId || !macAddress) {
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
                url: `/api/captive-portal/${networkId}/info`,
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
                        
                        // Update terms and privacy modal content with fresh data
                        if (locationInfo.location.design) {
                            if (locationInfo.location.design.terms_content) {
                                $('#terms-content').html(locationInfo.location.design.terms_content);
                            }
                            if (locationInfo.location.design.privacy_content) {
                                $('#privacy-content').html(locationInfo.location.design.privacy_content);
                            }
                        }
                        
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