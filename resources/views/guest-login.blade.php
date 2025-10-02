<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi Login Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background-color: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .loading-container {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            text-align: center;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 32px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            margin: 24px auto;
            border: 3px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            border-top-color: #3B82F6;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: #666;
            font-size: 16px;
            margin-top: 16px;
        }

        .footer {
            margin-top: 32px;
            font-size: 12px;
            color: #888;
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
    <div class="language-switcher">
        <button class="language-btn" data-lang="en">English</button>
        <button class="language-btn" data-lang="fr">Français</button>
    </div>

    <div class="loading-container">
        <img src="{{ asset('app-assets/mrwifi-assets/Mr-Wifi.PNG') }}" alt="MrWiFi Logo" class="logo">
        <div class="loading-spinner"></div>
        <div class="loading-text" data-i18n="loading"></div>
        <div class="footer" data-i18n="footer"></div>
    </div>
    
    <script>
        // Translations
        const translations = {
            en: {
                loading: 'Loading your WiFi login options...',
                footer: 'Powered by MrWiFi'
            },
            fr: {
                loading: 'Chargement de vos options de connexion WiFi...',
                footer: 'Propulsé par MrWiFi'
            }
        };

        // Get language preference
        function getLanguage() {
            // Check localStorage first
            let lang = localStorage.getItem('wifiPortalLanguage');
            
            if (lang && (lang === 'en' || lang === 'fr')) {
                return lang;
            }
            
            // Detect browser language
            const browserLang = navigator.language || navigator.userLanguage;
            const langCode = browserLang.toLowerCase().split('-')[0];
            
            // Support only French and English, fallback to English
            return (langCode === 'fr') ? 'fr' : 'en';
        }

        // Apply translations
        function applyTranslations(lang) {
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
            // Update active button
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-lang') === lang) {
                    btn.classList.add('active');
                }
            });
        }

        // Switch language
        function switchLanguage(lang) {
            if (lang === 'en' || lang === 'fr') {
                localStorage.setItem('wifiPortalLanguage', lang);
                applyTranslations(lang);
            }
        }

        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentLang = getLanguage();
            applyTranslations(currentLang);
            
            // Add click listeners to language buttons
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchLanguage(this.getAttribute('data-lang'));
                });
            });
        });

        // Apply immediately (before DOMContentLoaded for faster rendering)
        applyTranslations(getLanguage());
    </script>
    
    <script src="{{ asset('app-assets/mrwifi-assets/captive-portal/js/loading.js') }}?v={{ time() }}"></script>
</body>
</html>
