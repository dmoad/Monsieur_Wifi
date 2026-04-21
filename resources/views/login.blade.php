<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - WiFi network management system for administrators and network owners" id="meta-description">
    <meta name="keywords" content="wifi, network, dashboard, admin, monsieur-wifi, captive portal, radius, management">
    <meta name="author" content="monsieur-wifi">
    <title>Login - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/mrwifi-assets/MrWifi.png">
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

    <!-- Add CSRF token meta tag for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Design tokens — mrwifi.css not loaded here (incompatible with blank-page layout) */
        :root {
            --mw-primary:        #6366F1;
            --mw-primary-hover:  #4F46E5;
            --mw-bg-page:        #EDEEF2;
            --mw-bg-surface:     #FFFFFF;
            --mw-bg-muted:       #F0F2F5;
            --mw-text-primary:   #1A1A2E;
            --mw-text-secondary: #5C6370;
            --mw-text-muted:     #8B919A;
            --mw-border:         #D5D9E0;
            --mw-shadow-modal:   0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
        }

        body {
            background-color: var(--mw-bg-page);
        }

        /* Remove Vuexy decorative corner images */
        .auth-wrapper.auth-v1 .auth-inner::before,
        .auth-wrapper.auth-v1 .auth-inner::after {
            content: none;
        }

        /* Undo Vuexy JS-injected padding on the content wrapper */
        .app-content.content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .auth-wrapper.auth-v1 {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.5);
        }

        .form-control:focus {
            border-color: var(--mw-primary, #6366f1);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.2);
        }

        #lang-trigger {
            font-size: 12px;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }

        #lang-trigger:hover {
            border-color: var(--mw-primary, #6366f1);
            color: var(--mw-primary, #6366f1);
        }
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <!-- Login v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="javascript:void(0);" class="brand-logo">
                                        <img src="app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="36">
                                        <h2 class="brand-text text-primary ml-1">monsieur-wifi</h2>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="lang-trigger">
                                        <i data-feather="globe" style="width:13px;height:13px;vertical-align:middle;margin-right:3px;"></i>
                                        <span id="current-lang">EN</span>
                                    </button>
                                </div>

                                <h4 class="card-title mb-1">Welcome to monsieur-wifi! 👋</h4>
                                <p class="card-text mb-2" id="sign-in-prompt">Please sign-in to access your network management dashboard</p>

                                <div id="login-alert" class="alert alert-danger mt-1" style="display: none;"></div>

                                <form class="auth-login-form mt-2" id="login-form" autocomplete="on">
                                    <div class="form-group">
                                        <label for="login-email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="login-email" name="email" placeholder="admin@mrwifi.com" autocomplete="username" tabindex="1" />
                                    </div>

                                    <div class="form-group">
                                        <label for="login-password">Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="login-password" name="password" autocomplete="current-password" tabindex="2" placeholder="············" />
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="remember-me" name="remember" tabindex="3" />
                                            <label class="custom-control-label" for="remember-me"> Remember Me </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" tabindex="4" id="login-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="login-spinner"></span>
                                        <span id="login-text">Sign in</span>
                                    </button>
                                </form>

                                <p class="text-center mt-2">
                                    <span>Forgot your password?</span>
                                    <a href="/password-reset">
                                        <span>Reset Password</span>
                                    </a>
                                </p>
                                <p class="text-center mt-1" id="signup-link">
                                    <span id="no-account-text">Don't have an account?</span>
                                    <a href="/register">
                                        <span id="signup-text">Sign up</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <!-- /Login v1 -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->


    <!-- Add this right after the Page JS scripts -->
    <script src="/assets/js/config.js?v={{ filemtime(public_path('assets/js/config.js')) }}"></script>

    <script>
        // Language support system
        const translations = {
            en: {
                pageTitle: 'Login - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - WiFi network management system for administrators and network owners',
                welcome: 'Welcome to monsieur-wifi! 👋',
                signInPrompt: 'Please sign-in to access your network management dashboard',
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                passwordLabel: 'Password',
                passwordPlaceholder: '············',
                rememberMe: 'Remember Me',
                signIn: 'Sign in',
                signingIn: 'Signing in...',
                forgotPassword: 'Forgot your password ?',
                resetPassword: 'Reset Password',
                noAccount: "Don't have an account ?",
                signUp: 'Sign up',
                loginSuccessful: 'Login successful!',
                loginError: 'An error occurred during login.',
                langCode: 'EN',
                flag: '🇺🇸'
            },
            fr: {
                pageTitle: 'Connexion - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Système de gestion de réseaux WiFi pour administrateurs et propriétaires de réseaux',
                welcome: 'Bienvenue sur monsieur-wifi! 👋',
                signInPrompt: 'Veuillez vous connecter pour accéder à votre tableau de bord de gestion réseau',
                emailLabel: 'Email',
                emailPlaceholder: 'admin@mrwifi.com',
                passwordLabel: 'Mot de passe',
                passwordPlaceholder: '············',
                rememberMe: 'Se souvenir de moi',
                signIn: 'Se connecter',
                signingIn: 'Connexion en cours...',
                forgotPassword: 'Mot de passe oublié ?',
                resetPassword: 'Réinitialiser le mot de passe',
                noAccount: 'Pas de compte ?',
                signUp: 'S\'inscrire',
                loginSuccessful: 'Connexion réussie!',
                loginError: 'Une erreur s\'est produite lors de la connexion.',
                langCode: 'FR',
                flag: '🇫🇷'
            }
        };

        // Language detection and management
        function detectLanguage() {
            // Check for saved language preference first
            const savedLang = localStorage.getItem('preferred_language');
            if (savedLang && (savedLang === 'en' || savedLang === 'fr')) {
                return savedLang;
            }
            
            // Fallback to browser language detection
            const browserLang = navigator.language || navigator.userLanguage;
            const langCode = browserLang.substring(0, 2).toLowerCase();
            return langCode === 'fr' ? 'fr' : 'en'; // Default to English
        }

        function applyTranslations(lang) {
            const t = translations[lang];
            
            // Update page title and meta description
            document.title = t.pageTitle;
            $('#meta-description').attr('content', t.metaDescription);
            
            // Update static text elements
            $('.card-title').text(t.welcome);
            $('#sign-in-prompt').text(t.signInPrompt);
            $('label[for="login-email"]').text(t.emailLabel);
            $('#login-email').attr('placeholder', t.emailPlaceholder);
            $('label[for="login-password"]').text(t.passwordLabel);
            $('#login-password').attr('placeholder', t.passwordPlaceholder);
            $('.custom-control-label[for="remember-me"]').text(t.rememberMe);
            $('#login-text').text(t.signIn);
            $('p.text-center.mt-2 > span').first().text(t.forgotPassword);
            $('a[href="forgot-password.html"] span').text(t.resetPassword);
            $('#no-account-text').text(t.noAccount);
            $('#signup-text').text(t.signUp);
            
            $('#current-lang').text(t.langCode);
            
            // Store current language for use in other functions
            window.currentLang = lang;
            window.currentTranslations = t;
            
            // Save language preference to localStorage
            localStorage.setItem('preferred_language', lang);
        }

        function switchLanguage(newLang) {
            applyTranslations(newLang);
        }

        // Initialize language on page load
        const currentLanguage = detectLanguage();
        
        $(window).on('load', function() {
            // Apply translations before other initializations
            applyTranslations(currentLanguage);
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
                
            }
            
            // Language modal — vanilla JS, same pattern as sidebar
            const langModal    = document.getElementById('mwLangModal');
            const langBackdrop = document.getElementById('mwLangModalBackdrop');

            function updateLangModal(lang) {
                const isEn = lang === 'en';
                document.getElementById('mwLangEn').style.border    = '1.5px solid ' + (isEn  ? 'var(--mw-primary)' : 'var(--mw-border)');
                document.getElementById('mwLangFr').style.border    = '1.5px solid ' + (!isEn ? 'var(--mw-primary)' : 'var(--mw-border)');
                document.getElementById('mwLangEn').style.background = isEn  ? 'rgba(99,102,241,0.06)' : 'transparent';
                document.getElementById('mwLangFr').style.background = !isEn ? 'rgba(99,102,241,0.06)' : 'transparent';
                document.getElementById('mwLangEn').querySelector('span').style.background = isEn  ? 'var(--mw-primary)' : 'var(--mw-bg-muted)';
                document.getElementById('mwLangEn').querySelector('span').style.color      = isEn  ? '#fff' : 'var(--mw-text-secondary)';
                document.getElementById('mwLangFr').querySelector('span').style.background = !isEn ? 'var(--mw-primary)' : 'var(--mw-bg-muted)';
                document.getElementById('mwLangFr').querySelector('span').style.color      = !isEn ? '#fff' : 'var(--mw-text-secondary)';
                document.getElementById('mwLangEnCheck').style.display = isEn  ? 'block' : 'none';
                document.getElementById('mwLangFrCheck').style.display = !isEn ? 'block' : 'none';
            }

            function openLangModal() {
                updateLangModal(window.currentLang);
                langModal.style.display    = 'block';
                langBackdrop.style.display = 'block';
            }
            function closeLangModal() {
                langModal.style.display    = 'none';
                langBackdrop.style.display = 'none';
            }

            document.getElementById('lang-trigger').addEventListener('click', openLangModal);
            langBackdrop.addEventListener('click', closeLangModal);
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLangModal(); });

            document.getElementById('mwLangEn').addEventListener('click', function(e) {
                e.preventDefault(); switchLanguage('en'); closeLangModal();
            });
            document.getElementById('mwLangFr').addEventListener('click', function(e) {
                e.preventDefault(); switchLanguage('fr'); closeLangModal();
            });

            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Form validation and submission
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                // Show spinner, hide text
                $('#login-spinner').removeClass('d-none');
                $('#login-text').text(window.currentTranslations.signingIn);
                $('#login-btn').attr('disabled', true);
                $('#login-alert').hide();
                
                // Get form data
                var formData = {
                    email: $('#login-email').val(),
                    password: $('#login-password').val(),
                    remember: $('#remember-me').is(':checked')
                };
                
                // Make AJAX request to login endpoint
                $.ajax({
                    url: '/api/auth/login',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        UserManager.setToken(response.access_token);

                        if (response.user) {
                            UserManager.setUser(response.user);
                            localStorage.setItem('profile_picture', response.user.profile_picture);
                        }

                        const langPrefix = window.currentLang === 'fr' ? '/fr' : '/en';
                        window.location.href = langPrefix + '/dashboard?status=login';
                    },
                    error: function(xhr) {
                        // Reset button
                        $('#login-spinner').addClass('d-none');
                        $('#login-text').text(window.currentTranslations.signIn);
                        $('#login-btn').attr('disabled', false);
                        
                        // Show error message
                        var errorMessage = window.currentTranslations.loginError;
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.email) {
                                errorMessage = xhr.responseJSON.email[0];
                            }
                        }
                        $('#login-alert').text(errorMessage).show();
                    }
                });
            });
            
            // Toggle password visibility
            $('.form-password-toggle .input-group-text').on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    passwordInput = $this.closest('.form-password-toggle').find('input');
                
                if (passwordInput.attr('type') === 'text') {
                    passwordInput.attr('type', 'password');
                    $this.find('svg').replaceWith(feather.icons['eye'].toSvg());
                } else if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    $this.find('svg').replaceWith(feather.icons['eye-off'].toSvg());
                }
            });

        });

    </script>

    <!-- Language picker modal (same pattern as sidebar) -->
    <div id="mwLangModalBackdrop" style="display:none;position:fixed;inset:0;z-index:1050;background:rgba(0,0,0,0.45);"></div>
    <div id="mwLangModal" role="dialog" aria-labelledby="mwLangModalTitle" aria-modal="true"
         style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1051;
                background:var(--mw-bg-surface);border-radius:16px;width:300px;max-width:calc(100vw - 2rem);
                box-shadow:var(--mw-shadow-modal);padding:1.25rem;">
        <h6 id="mwLangModalTitle" style="margin:0 0 1rem;font-weight:700;font-size:1rem;color:var(--mw-text-primary);text-align:center;">
            Language / Langue
        </h6>
        <div style="display:flex;flex-direction:column;gap:0.5rem;">
            <a href="#" id="mwLangEn"
               style="display:flex;align-items:center;gap:0.75rem;padding:0.7rem 0.9rem;border-radius:10px;
                      text-decoration:none;transition:border-color 0.15s,background 0.15s;">
                <span style="display:inline-flex;align-items:center;justify-content:center;
                             width:36px;height:36px;border-radius:8px;flex-shrink:0;font-size:0.7rem;font-weight:700;letter-spacing:0.02em;">EN</span>
                <span style="font-weight:600;font-size:0.92rem;color:var(--mw-text-primary);flex:1;">English</span>
                <svg id="mwLangEnCheck" style="width:16px;height:16px;color:var(--mw-primary);flex-shrink:0;display:none;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </a>
            <a href="#" id="mwLangFr"
               style="display:flex;align-items:center;gap:0.75rem;padding:0.7rem 0.9rem;border-radius:10px;
                      text-decoration:none;transition:border-color 0.15s,background 0.15s;">
                <span style="display:inline-flex;align-items:center;justify-content:center;
                             width:36px;height:36px;border-radius:8px;flex-shrink:0;font-size:0.7rem;font-weight:700;letter-spacing:0.02em;">FR</span>
                <span style="font-weight:600;font-size:0.92rem;color:var(--mw-text-primary);flex:1;">Français</span>
                <svg id="mwLangFrCheck" style="width:16px;height:16px;color:var(--mw-primary);flex-shrink:0;display:none;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </a>
        </div>
    </div>
</body>
<!-- END: Body-->

</html>