<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Configure your captive portal design">
    <title>Configure Captive Portal Design - Monsieur WiFi</title>
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
    </style>
</head>

<body class="vertical-layout vertical-menu-modern blank-page">
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="design-form-container">
                    <div class="text-center mb-3">
                        <img src="app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo" height="48">
                        <h2 class="mt-2">Configure Your Captive Portal Design</h2>
                        <p class="text-muted">Customize your WiFi login page before registration</p>
                    </div>
                    
                    <div id="alert-container"></div>
                    
                    <form id="design-form" enctype="multipart/form-data">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h5>Basic Information</h5>
                            <div class="form-group">
                                <label for="name">Design Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="My WiFi Portal">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of your WiFi portal"></textarea>
                            </div>
                        </div>
                        
                        <!-- Theme Settings -->
                        <div class="form-section">
                            <h5>Theme Settings</h5>
                            <div class="form-group">
                                <label for="theme_color">Theme Color <span class="text-danger">*</span></label>
                                <input type="color" class="form-control" id="theme_color" name="theme_color" value="#7367f0" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_color_gradient_start">Gradient Start Color</label>
                                        <input type="color" class="form-control" id="background_color_gradient_start" name="background_color_gradient_start">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_color_gradient_end">Gradient End Color</label>
                                        <input type="color" class="form-control" id="background_color_gradient_end" name="background_color_gradient_end">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Settings -->
                        <div class="form-section">
                            <h5>Content Settings</h5>
                            <div class="form-group">
                                <label for="welcome_message">Welcome Message <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="welcome_message" name="welcome_message" required value="Welcome to our WiFi" placeholder="Welcome to our WiFi">
                            </div>
                            <div class="form-group">
                                <label for="login_instructions">Login Instructions</label>
                                <textarea class="form-control" id="login_instructions" name="login_instructions" rows="3" placeholder="Instructions for users on how to connect"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="button_text">Button Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="button_text" name="button_text" required value="Connect to WiFi" placeholder="Connect to WiFi">
                            </div>
                        </div>
                        
                        <!-- Branding -->
                        <div class="form-section">
                            <h5>Branding</h5>
                            <div class="form-group">
                                <label for="location_logo">Location Logo</label>
                                <div class="upload-area" id="logo-upload-area">
                                    <i data-feather="upload-cloud" style="width: 48px; height: 48px;"></i>
                                    <p class="mt-2 mb-0">Click to upload or drag and drop</p>
                                    <small class="text-muted">PNG, JPG up to 2MB</small>
                                </div>
                                <input type="file" id="location_logo" name="location_logo" class="d-none" accept="image/*">
                                <img id="logo-preview" class="image-preview" alt="Logo preview">
                            </div>
                            <div class="form-group">
                                <label for="background_image">Background Image</label>
                                <div class="upload-area" id="bg-upload-area">
                                    <i data-feather="image" style="width: 48px; height: 48px;"></i>
                                    <p class="mt-2 mb-0">Click to upload or drag and drop</p>
                                    <small class="text-muted">PNG, JPG up to 5MB</small>
                                </div>
                                <input type="file" id="background_image" name="background_image" class="d-none" accept="image/*">
                                <img id="bg-preview" class="image-preview" alt="Background preview">
                            </div>
                        </div>
                        
                        <!-- Terms & Privacy -->
                        <div class="form-section">
                            <h5>Terms & Privacy</h5>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="show_terms" name="show_terms" value="1" checked>
                                    <label class="custom-control-label" for="show_terms">Show Terms & Conditions</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="terms_content">Terms & Conditions Content</label>
                                <textarea class="form-control" id="terms_content" name="terms_content" rows="4" placeholder="Enter terms and conditions text"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="privacy_content">Privacy Policy Content</label>
                                <textarea class="form-control" id="privacy_content" name="privacy_content" rows="4" placeholder="Enter privacy policy text"></textarea>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <span class="spinner-border spinner-border-sm d-none" id="submit-spinner"></span>
                                <span id="submit-text">Continue to Registration</span>
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
            submitText.text('Creating Design...');
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
                submitText.text('Continue to Registration');
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
