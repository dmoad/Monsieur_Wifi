<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Configure your captive portal design" id="meta-description">
    <title>Configure Captive Portal Design - Monsieur WiFi</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->
    
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/page-auth.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .design-form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-section h5 {
            margin-bottom: 1rem;
            color: #7367f0;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 4px;
            display: none;
        }
        .image-preview.show {
            display: block;
        }
        .upload-area {
            border: 2px dashed #7367f0;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover {
            background-color: #f8f9fa;
        }
        .upload-area.dragover {
            background-color: #e7e8ff;
            border-color: #7367f0;
        }
        
        /* Language switcher styles */
        .language-switcher {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        
        .language-switcher #languageDropdown {
            border-radius: 20px;
            transition: all 0.3s ease;
            min-width: 65px;
            font-weight: 600;
            font-size: 12px;
            padding: 0.25rem 0.5rem;
        }
        
        .language-switcher #languageDropdown:hover {
            transform: scale(1.05);
            border-color: #7367f0;
            color: #7367f0;
        }
        
        .language-switcher #languageDropdown:focus {
            box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
            border-color: #7367f0;
        }
        
        .language-switcher .dropdown-menu {
            border-radius: 10px;
            border: 1px solid rgba(115, 103, 240, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-width: 120px;
            padding: 0.5rem 0;
        }
        
        .language-switcher .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            font-size: 14px;
            border: none;
        }
        
        .language-switcher .dropdown-item:hover {
            background-color: rgba(115, 103, 240, 0.05);
            color: #7367f0;
            transform: translateX(2px);
        }
        
        .language-switcher .flag-icon {
            margin-right: 8px;
            font-size: 16px;
        }
        
        .language-switcher .lang-text {
            font-weight: 500;
        }
        
        .language-switcher #current-lang-flag {
            margin-right: 4px;
            font-size: 14px;
        }
        
        .header-section {
            position: relative;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern blank-page">
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="design-form-container">
                    <div class="header-section">
                        <div class="language-switcher">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span id="current-lang-flag">🇺🇸</span>
                                    <span id="current-lang">EN</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
                                    <a class="dropdown-item language-option" href="#" data-lang="en">
                                        <span class="flag-icon">🇺🇸</span>
                                        <span class="lang-text">English</span>
                                    </a>
                                    <a class="dropdown-item language-option" href="#" data-lang="fr">
                                        <span class="flag-icon">🇫🇷</span>
                                        <span class="lang-text">Français</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <img src="assets/images/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="48">
                            <h2 class="mt-2" id="page-title">Configure Your Captive Portal Design</h2>
                            <p class="text-muted" id="page-subtitle">Customize your WiFi login page before registration</p>
                        </div>
                    </div>
                    
                    <div id="alert-container"></div>
                    
                    <form id="design-form" enctype="multipart/form-data">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h5 data-translate="basicInfo">Basic Information</h5>
                            <div class="form-group">
                                <label for="name" data-translate="designName">Design Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required data-placeholder="designNamePlaceholder" placeholder="My WiFi Portal">
                            </div>
                            <div class="form-group">
                                <label for="description" data-translate="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" data-placeholder="descriptionPlaceholder" placeholder="Brief description of your WiFi portal"></textarea>
                            </div>
                        </div>
                        
                        <!-- Theme Settings -->
                        <div class="form-section">
                            <h5 data-translate="themeSettings">Theme Settings</h5>
                            <div class="form-group">
                                <label for="theme_color" data-translate="themeColor">Theme Color <span class="text-danger">*</span></label>
                                <input type="color" class="form-control" id="theme_color" name="theme_color" value="#7367f0" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_color_gradient_start" data-translate="gradientStart">Gradient Start Color</label>
                                        <input type="color" class="form-control" id="background_color_gradient_start" name="background_color_gradient_start">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_color_gradient_end" data-translate="gradientEnd">Gradient End Color</label>
                                        <input type="color" class="form-control" id="background_color_gradient_end" name="background_color_gradient_end">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Settings -->
                        <div class="form-section">
                            <h5 data-translate="contentSettings">Content Settings</h5>
                            <div class="form-group">
                                <label for="welcome_message" data-translate="welcomeMessage">Welcome Message <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="welcome_message" name="welcome_message" required value="Welcome to our WiFi" data-placeholder="welcomeMessagePlaceholder" placeholder="Welcome to our WiFi">
                            </div>
                            <div class="form-group">
                                <label for="login_instructions" data-translate="loginInstructions">Login Instructions</label>
                                <textarea class="form-control" id="login_instructions" name="login_instructions" rows="3" data-placeholder="loginInstructionsPlaceholder" placeholder="Instructions for users on how to connect"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="button_text" data-translate="buttonText">Button Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="button_text" name="button_text" required value="Connect to WiFi" data-placeholder="buttonTextPlaceholder" placeholder="Connect to WiFi">
                            </div>
                        </div>
                        
                        <!-- Branding -->
                        <div class="form-section">
                            <h5 data-translate="branding">Branding</h5>
                            <div class="form-group">
                                <label for="location_logo" data-translate="locationLogo">Location Logo</label>
                                <div class="upload-area" id="logo-upload-area">
                                    <i data-feather="upload-cloud" style="width: 48px; height: 48px;"></i>
                                    <p class="mt-2 mb-0" data-translate="uploadText">Click to upload or drag and drop</p>
                                    <small class="text-muted" data-translate="logoFileSize">PNG, JPG up to 2MB</small>
                                </div>
                                <input type="file" id="location_logo" name="location_logo" class="d-none" accept="image/*">
                                <img id="logo-preview" class="image-preview" alt="Logo preview">
                            </div>
                            <div class="form-group">
                                <label for="background_image" data-translate="backgroundImage">Background Image</label>
                                <div class="upload-area" id="bg-upload-area">
                                    <i data-feather="image" style="width: 48px; height: 48px;"></i>
                                    <p class="mt-2 mb-0" data-translate="uploadText">Click to upload or drag and drop</p>
                                    <small class="text-muted" data-translate="bgFileSize">PNG, JPG up to 5MB</small>
                                </div>
                                <input type="file" id="background_image" name="background_image" class="d-none" accept="image/*">
                                <img id="bg-preview" class="image-preview" alt="Background preview">
                            </div>
                        </div>
                        
                        <!-- Terms & Privacy -->
                        <div class="form-section">
                            <h5 data-translate="termsPrivacy">Terms & Privacy</h5>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="show_terms" name="show_terms" value="1" checked>
                                    <label class="custom-control-label" for="show_terms" data-translate="showTerms">Show Terms & Conditions</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="terms_content" data-translate="termsContent">Terms & Conditions Content</label>
                                <textarea class="form-control" id="terms_content" name="terms_content" rows="4" data-placeholder="termsContentPlaceholder" placeholder="Enter terms and conditions text"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="privacy_content" data-translate="privacyContent">Privacy Policy Content</label>
                                <textarea class="form-control" id="privacy_content" name="privacy_content" rows="4" data-placeholder="privacyContentPlaceholder" placeholder="Enter privacy policy text"></textarea>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <span class="spinner-border spinner-border-sm d-none" id="submit-spinner"></span>
                                <span id="submit-text" data-translate="continueToRegistration">Continue to Registration</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- END: Vendor JS-->
    
    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->
    
    <script>
        // Language support system
        const translations = {
            en: {
                pageTitle: 'Configure Captive Portal Design - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Configure your captive portal design',
                pageTitleText: 'Configure Your Captive Portal Design',
                pageSubtitle: 'Customize your WiFi login page before registration',
                basicInfo: 'Basic Information',
                designName: 'Design Name',
                designNamePlaceholder: 'My WiFi Portal',
                description: 'Description',
                descriptionPlaceholder: 'Brief description of your WiFi portal',
                themeSettings: 'Theme Settings',
                themeColor: 'Theme Color',
                gradientStart: 'Gradient Start Color',
                gradientEnd: 'Gradient End Color',
                contentSettings: 'Content Settings',
                welcomeMessage: 'Welcome Message',
                welcomeMessagePlaceholder: 'Welcome to our WiFi',
                loginInstructions: 'Login Instructions',
                loginInstructionsPlaceholder: 'Instructions for users on how to connect',
                buttonText: 'Button Text',
                buttonTextPlaceholder: 'Connect to WiFi',
                branding: 'Branding',
                locationLogo: 'Location Logo',
                backgroundImage: 'Background Image',
                uploadText: 'Click to upload or drag and drop',
                logoFileSize: 'PNG, JPG up to 2MB',
                bgFileSize: 'PNG, JPG up to 5MB',
                termsPrivacy: 'Terms & Privacy',
                showTerms: 'Show Terms & Conditions',
                termsContent: 'Terms & Conditions Content',
                termsContentPlaceholder: 'Enter terms and conditions text',
                privacyContent: 'Privacy Policy Content',
                privacyContentPlaceholder: 'Enter privacy policy text',
                continueToRegistration: 'Continue to Registration',
                creatingDesign: 'Creating Design...',
                langCode: 'EN',
                flag: '🇺🇸'
            },
            fr: {
                pageTitle: 'Configurer le Design du Portail Captif - Monsieur WiFi',
                metaDescription: 'monsieur-wifi - Configurez votre design de portail captif',
                pageTitleText: 'Configurez Votre Design de Portail Captif',
                pageSubtitle: 'Personnalisez votre page de connexion WiFi avant l\'inscription',
                basicInfo: 'Informations de Base',
                designName: 'Nom du Design',
                designNamePlaceholder: 'Mon Portail WiFi',
                description: 'Description',
                descriptionPlaceholder: 'Brève description de votre portail WiFi',
                themeSettings: 'Paramètres du Thème',
                themeColor: 'Couleur du Thème',
                gradientStart: 'Couleur de Début du Dégradé',
                gradientEnd: 'Couleur de Fin du Dégradé',
                contentSettings: 'Paramètres de Contenu',
                welcomeMessage: 'Message de Bienvenue',
                welcomeMessagePlaceholder: 'Bienvenue sur notre WiFi',
                loginInstructions: 'Instructions de Connexion',
                loginInstructionsPlaceholder: 'Instructions pour les utilisateurs sur la façon de se connecter',
                buttonText: 'Texte du Bouton',
                buttonTextPlaceholder: 'Se Connecter au WiFi',
                branding: 'Image de Marque',
                locationLogo: 'Logo de l\'Emplacement',
                backgroundImage: 'Image de Fond',
                uploadText: 'Cliquez pour télécharger ou glissez-déposez',
                logoFileSize: 'PNG, JPG jusqu\'à 2 Mo',
                bgFileSize: 'PNG, JPG jusqu\'à 5 Mo',
                termsPrivacy: 'Conditions & Confidentialité',
                showTerms: 'Afficher les Conditions Générales',
                termsContent: 'Contenu des Conditions Générales',
                termsContentPlaceholder: 'Entrez le texte des conditions générales',
                privacyContent: 'Contenu de la Politique de Confidentialité',
                privacyContentPlaceholder: 'Entrez le texte de la politique de confidentialité',
                continueToRegistration: 'Continuer vers l\'Inscription',
                creatingDesign: 'Création du Design...',
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
            
            // Update page header
            $('#page-title').text(t.pageTitleText);
            $('#page-subtitle').text(t.pageSubtitle);
            
            // Update all elements with data-translate attribute
            $('[data-translate]').each(function() {
                const key = $(this).attr('data-translate');
                if (t[key]) {
                    if ($(this).is('label')) {
                        // For labels, preserve the required asterisk if present
                        const html = $(this).html();
                        if (html.includes('<span class="text-danger">*</span>')) {
                            $(this).html(t[key] + ' <span class="text-danger">*</span>');
                        } else {
                            $(this).text(t[key]);
                        }
                    } else {
                        $(this).text(t[key]);
                    }
                }
            });
            
            // Update placeholders
            $('[data-placeholder]').each(function() {
                const key = $(this).attr('data-placeholder');
                if (t[key]) {
                    $(this).attr('placeholder', t[key]);
                }
            });
            
            // Update language dropdown button
            $('#current-lang').text(t.langCode);
            $('#current-lang-flag').text(t.flag);
            
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
        
        // Initialize Feather icons
        if (feather) {
            feather.replace();
        }
        
        // Set up CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Apply translations on page load
        $(window).on('load', function() {
            applyTranslations(currentLanguage);
            
            // Reinitialize Feather icons after language change
            if (feather) {
                feather.replace();
            }
            
            // Language dropdown event handlers
            $('.language-option').on('click', function(e) {
                e.preventDefault();
                const selectedLang = $(this).data('lang');
                if (selectedLang !== window.currentLang) {
                    switchLanguage(selectedLang);
                    // Reinitialize Feather icons after language change
                    if (feather) {
                        feather.replace();
                    }
                }
                // Close dropdown
                $('#languageDropdown').dropdown('hide');
            });
        });
        
        // File upload handlers
        $('#logo-upload-area').on('click', function() {
            $('#location_logo').click();
        });
        
        $('#bg-upload-area').on('click', function() {
            $('#background_image').click();
        });
        
        $('#location_logo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#logo-preview').attr('src', e.target.result).addClass('show');
                };
                reader.readAsDataURL(file);
            }
        });
        
        $('#background_image').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#bg-preview').attr('src', e.target.result).addClass('show');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Form submission
        $('#design-form').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submit-btn');
            const submitText = $('#submit-text');
            const submitSpinner = $('#submit-spinner');
            
            // Show loading state
            submitBtn.prop('disabled', true);
            submitText.text(window.currentTranslations.creatingDesign);
            submitSpinner.removeClass('d-none');
            
            // Create FormData
            const formData = new FormData(this);
            
            // Convert checkbox to boolean
            formData.set('show_terms', $('#show_terms').is(':checked') ? '1' : '0');
            
            // Submit form
            $.ajax({
                url: '/api/temp-captive-portal-designs',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success && response.data.design_id) {
                        // Redirect to registration page with design_id
                        window.location.href = '/register?design_id=' + response.data.design_id;
                    } else {
                        showAlert('error', 'Failed to create design. Please try again.');
                        resetButton();
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while creating the design.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('<br>');
                        }
                    }
                    showAlert('error', errorMessage);
                    resetButton();
                }
            });
            
            function resetButton() {
                submitBtn.prop('disabled', false);
                submitText.text(window.currentTranslations.continueToRegistration);
                submitSpinner.addClass('d-none');
            }
        });
        
        function showAlert(type, message) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('#alert-container').html(alertHtml);
        }
    </script>
</body>
</html>
