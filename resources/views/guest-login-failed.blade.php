<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi Login Failed</title>
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

        .error-container {
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
            text-align: center;
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

        .error-icon {
            font-size: 64px;
            color: #ea5455;
            margin: 16px 0;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 16px;
        }

        .error-message {
            color: #666;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .error-details {
            background-color: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 24px;
            text-align: left;
            font-size: 14px;
            color: #666;
        }

        .action-button {
            background-color: var(--theme-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            margin: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .action-button:hover {
            background-color: var(--theme-color-dark);
            color: white;
            text-decoration: none;
        }

        .action-button.secondary {
            background-color: #6c757d;
        }

        .action-button.secondary:hover {
            background-color: #5a6268;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1rem;
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
            .error-container {
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

    <div class="error-container">
        <!-- Header with Location Logo -->
        <div class="text-center">
            <div class="location-logo mx-auto" id="location-logo">
                <div style="background: #f0f0f0; width: 100%; height: 100%; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666;">
                    Location Logo
                </div>
            </div>
        </div>
        
        <div class="error-icon">
            <i class="fa fa-times-circle"></i>
        </div>
        <h1 class="error-title" data-i18n="errorTitle">Connection Failed</h1>
        <p class="error-message" id="error-message" data-i18n-default="errorMessage">We couldn't connect you to the WiFi network. This might be due to authentication issues or network problems.</p>
        
        <div id="error-details" class="error-details" style="display: none;">
            <div id="error-detail-message" data-i18n="noErrorDetails">No specific error details available.</div>
        </div>
        
        <div class="buttons">
            <a href="#" id="try-again-btn" class="action-button">
                <i class="fa fa-refresh"></i> <span data-i18n="tryAgain">Try Again</span>
            </a>
            <!-- <a href="#" id="show-details-btn" class="action-button secondary">
                <i class="fa fa-info-circle"></i> Show Details
            </a> -->
        </div>
        
        <!-- Footer with Brand Logo and Terms -->
        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Brand Logo">
            </div>
            <div class="terms" id="terms-text" data-i18n-default="footer">
                Powered by Mr WiFi
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script>
        // Language system - Initialize before DOM ready
        const translations = {
            en: {
                errorTitle: 'Connection Failed',
                errorMessage: 'We couldn\'t connect you to the WiFi network. This might be due to authentication issues or network problems.',
                noErrorDetails: 'No specific error details available.',
                tryAgain: 'Try Again',
                footer: 'Powered by Monsieur WiFi',
                termsText: 'By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>'
            },
            fr: {
                errorTitle: 'Échec de la connexion',
                errorMessage: 'Nous n\'avons pas pu vous connecter au réseau WiFi. Cela peut être dû à des problèmes d\'authentification ou de réseau.',
                noErrorDetails: 'Aucun détail d\'erreur spécifique disponible.',
                tryAgain: 'Réessayer',
                footer: 'Propulsé par Monsieur WiFi',
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
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
            document.querySelectorAll('[data-i18n-default]').forEach(element => {
                const key = element.getAttribute('data-i18n-default');
                const isDefault = element.getAttribute('data-is-custom') !== 'true';
                if (isDefault && translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchLanguage(this.getAttribute('data-lang'));
                });
            });
        });

        const currentLang = getLanguage();
        applyTranslations(currentLang);

        $(document).ready(function() {
            // Get location data from localStorage
            const locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
            const designData = locationData.design || {};
            var ip_address;

            // Get $_GET['uamip']
            ip_address = '{{ $_GET['uamip'] }}';
            var try_again_btn = 'http://' + ip_address + ':3990/prelogin';
            $('#try-again-btn').attr('href', try_again_btn);
            
            console.log('Location data:', locationData);
            console.log('Design data:', designData);
            
            // Apply design settings
            applyDesignSettings(locationData.settings || {}, designData);
            
            // Get error message from localStorage if available
            const errorMessage = localStorage.getItem('login_error') || 'No specific error details available.';
            $('#error-detail-message').text(errorMessage);
            
            // Toggle error details visibility
            $('#show-details-btn').on('click', function(e) {
                e.preventDefault();
                
                const $errorDetails = $('#error-details');
                const $detailsBtn = $(this);
                
                // if ($errorDetails.is(':visible')) {
                //     $errorDetails.slideUp();
                //     $detailsBtn.html('<i class="fa fa-info-circle"></i> Show Details');
                // } else {
                //     $errorDetails.slideDown();
                //     $detailsBtn.html('<i class="fa fa-times-circle"></i> Hide Details');
                // }
            });
            
            // Check if there's a redirect URL in localStorage for the "Try Again" button
            // const loginUrl = localStorage.getItem('login_url') || '/';
            // $('#try-again-btn').attr('href', loginUrl);
            
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
                
                // Set location logo from full design data
                if (design.location_logo_path) {
                    $('#location-logo').html(`<img src="/storage/${design.location_logo_path}" alt="Location Logo">`);
                }
                
                // Set custom error message if available
                if (design.error_message) {
                    $('#error-message').text(design.error_message).attr('data-is-custom', 'true');
                }
                
                // Set terms visibility from full design data, fallback to settings
                const showTerms = design.show_terms === true || settings.terms_enabled === true;
                if (showTerms) {
                    const lang = getLanguage();
                    $('#terms-text').html(translations[lang].termsText);
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
        });
    </script>
</body>
</html> 