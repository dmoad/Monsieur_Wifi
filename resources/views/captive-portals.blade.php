<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Mr WiFi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, mrwifi">
    <meta name="author" content="Mr WiFi">
    <title>Captive Portal Designer - Mr WiFi Controller</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/toastr.min.css">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/dashboard-ecommerce.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <!-- Custom styles for captive portal designer -->
    <style>
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .upload-area:hover {
            border-color: #7367f0;
            background-color: rgba(115, 103, 240, 0.05);
        }
        
        .upload-area.highlight {
            border-color: #7367f0;
            background-color: rgba(115, 103, 240, 0.1);
            transform: scale(1.02);
        }
        
        .upload-icon {
            font-size: 2.5rem;
            color: #7367f0;
            margin-bottom: 1rem;
        }
        
        .color-picker-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .image-preview {
            width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
        }
        
        .preview-container {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-header {
            background-color: #f8f9fa;
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .portal-preview {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 400px;
        }
        
        /* Add a semi-transparent overlay for better text readability when a background image is present */
        .portal-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            z-index: -1;
        }

        .preview-main {
            width: 100%;
            max-width: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }
        
        .preview-logo {
            max-height: 80px;
            max-width: 200px;
            object-fit: contain;
        }
        
        #preview-welcome {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        #preview-instructions {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }
        
        .input-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .preview-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .preview-button {
            width: 100%;
            padding: 12px 20px;
            background-color: #7367f0;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .preview-terms {
            font-size: 12px;
            color: #666;
            margin-top: 15px;
            text-align: center;
        }
        
        .preview-terms a {
            color: #7367f0;
            text-decoration: none;
        }
        
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .location-logo {
            height: 64px;
            width: auto;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
            padding: 0 16px;
        }
        
        .welcome-text {
            color: #333;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 24px 0 32px;
            text-align: center;
        }
        
        .login-placeholder {
            background: #f8f8f8;
            border: 2px dashed #ddd;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            color: #666;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .login-placeholder-footer {
            background: #ffffff;
            border: 0px;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            color: #666;
            margin-bottom: 10px;
            flex-grow: 1;
        }
        
        .footer {
            margin-top: auto;
            /*text-align: center;
            border-top: 1px solid #eee;*/
            padding-top: 24px;
            margin-left: 0px;
            margin-right: 0px;
        }
        
        .brand-logo {
            height: 32px;
            width: auto;
            margin: 0 auto 16px;
            background: #f0f0f0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
            padding: 0 12px;
        }
        
        .terms {
            font-size: 0.8rem;
            color: #666;
        }
        
        .terms a {
            color: #007bff;
            text-decoration: none;
        }
        
        /* Design cards styles */
        .design-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #e0e0e0;
        }
        
        .design-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .design-preview {
            height: 180px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        
        .design-preview .preview-content {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .location-logo-mini {
            height: 24px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .login-area-mini {
            flex-grow: 1;
            background: rgba(248, 248, 248, 0.8);
            border: 1px dashed #ddd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
            margin: 8px 0;
        }
        
        .brand-logo-mini {
            height: 20px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }
        
        .design-card:hover .design-preview {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        #preview-button {
            transition: all 0.3s ease;
            padding: 10px 20px;
            font-size: 14px;
        }

        .terms-container {
            margin-top: 15px;
            text-align: center;
        }

        .section-label {
            font-weight: 600;
            color: #5e5873;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .design-card {
            transition: all 0.3s ease;
            border: 1px solid #ebe9f1;
        }

        .design-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        }

        .design-preview {
            height: 160px;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .preview-content {
            padding: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .design-actions {
            padding-top: 0.5rem;
            border-top: 1px solid #ebe9f1;
        }

        .badge {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .bg-light-primary {
            background-color: rgba(115, 103, 240, 0.12) !important;
        }

        .bg-light-success {
            background-color: rgba(40, 199, 111, 0.12) !important;
        }

        .bg-light-danger {
            background-color: rgba(234, 84, 85, 0.12) !important;
        }

        .tab-content h6 {
            color: #5e5873;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ebe9f1;
            padding-bottom: 0.5rem;
        }

        .color-picker-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 300px;
        }

        .form-control-color {
            width: 60px;
            padding: 0.2rem;
            height: 38px;
        }   

        .card-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            z-index: 2000 !important;
            background: #fff;
        }

        /* Fix for modals that need to display over fullscreen cards */
        .modal {
            z-index: 2100 !important;
        }
        
        .modal-backdrop {
            z-index: 2050 !important;
        }

        .card-fullscreen .card-body {
            height: calc(100% - 60px); /* Adjust for card header height */
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .card-fullscreen .portal-preview {
            max-width: 100%; /* Or your desired width */
            width: 90%;
            margin: 0 auto;
            height: auto;
            min-height: 500px;
        }

        /* Loading overlay styles */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            border-radius: 0.428rem;
        }

        /* Empty state styles */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: #6e6b7b;
        }
        
        /* Responsive styles for preview */
        @media (max-width: 991px) {
            .preview-container {
                margin-top: 2rem;
            }
            
            .portal-preview {
                min-height: 350px;
            }
        }
        
        @media (max-width: 767px) {
            .portal-preview {
                padding: 20px;
                min-height: 320px;
            }
            
            #preview-welcome {
                font-size: 20px;
            }
            
            #preview-instructions {
                font-size: 14px;
            }
            
            .preview-input, .preview-button {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 575px) {
            .preview-main {
                max-width: 100%;
            }
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ml-auto">
                <!-- Language dropdown -->
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="javascript:void(0);" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="fr">
                            <i class="flag-icon flag-icon-fr"></i> French
                        </a>
                    </div>
                </li>
                
                <!-- Dark mode toggle -->
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style">
                        <i class="ficon" data-feather="moon"></i>
                    </a>
                </li>
                
                <!-- Notifications -->
                <!-- <li class="nav-item dropdown dropdown-notification mr-25">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge badge-pill badge-primary badge-up">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        
                    </ul>
                </li> -->
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                        <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="mr-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="dashboard.html">
                        <span class="brand-logo">
                            <img src="../../../app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Mr WiFi logo">
                        </span>
                        <h2 class="brand-text">Mr WiFi</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc" data-ticon="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <!-- Management Section -->
                <li class="navigation-header"><span>Management</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/dashboard">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/locations">
                        <i data-feather="map-pin"></i>
                        <span class="menu-title text-truncate">Locations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/analytics">
                        <i data-feather="bar-chart-2"></i>
                        <span class="menu-title text-truncate">Usage Analytics</span>
                    </a>
                </li>

                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Captive Portals</span>
                    </a>
                </li>
                
                <!-- For Admin Section -->
                <li class="navigation-header"><span>For Admin</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/accounts">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Accounts</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/domain-blocking">
                        <i data-feather="slash"></i>
                        <span class="menu-title text-truncate">Domain Blocking</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/firmware">
                        <i data-feather="download"></i>
                        <span class="menu-title text-truncate">Firmware</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/system-settings">
                        <i data-feather="settings"></i>
                        <span class="menu-title text-truncate">System Settings</span>
                    </a>
                </li>
                <!-- Account Section -->
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/profile">
                         <i data-feather="user"></i>
                         <span class="menu-title text-truncate">Profile</span>
                     </a>
                </li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/logout">
                         <i data-feather="power"></i>
                         <span class="menu-title text-truncate">Logout</span>
                     </a>
                </li> 
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Captive Portal Designer</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Captive Portals
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="content-body">
                <!-- Captive Portal Designs List -->
                <section id="captive-portal-designs-list">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Your Captive Portal Designs</h4>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li>
                                                <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="create-new-design">
                                                    <i data-feather="plus" class="mr-50"></i>
                                                    <span>Create New Design</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="portal-designs-container">
                                        <!-- Design Cards will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Captive Portal Design Content Starts - Initially hidden -->
                <section id="captive-portal-designer" style="display: none;">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <button class="btn btn-outline-secondary waves-effect" id="back-to-list">
                                <i data-feather="arrow-left" class="mr-50"></i>
                                <span>Back to Designs</span>
                            </button>
                        </div>
                    </div>
                    <div class="row match-height">
                        <div class="col-lg-8 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Design Your Login Page</h4>
                                    <div class="heading-elements">
                                        
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" aria-controls="general" role="tab" aria-selected="true">
                                                <i data-feather="settings" class="mr-25"></i>
                                                <span class="font-weight-bold">General</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="branding-tab" data-toggle="tab" href="#branding" aria-controls="branding" role="tab" aria-selected="false">
                                                <i data-feather="image" class="mr-25"></i>
                                                <span class="font-weight-bold">Branding</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <!-- General Tab (now includes content settings) -->
                                        <div class="tab-pane active" id="general" aria-labelledby="general-tab" role="tabpanel">
                                            <form class="mt-2">
                                                <div class="row">
                                                    <div class="col-12 mb-1">
                                                        <h6 class="mb-1">Basic Information</h6>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="portal-name">Portal Name</label>
                                                            <input type="text" class="form-control" id="portal-name" placeholder="Enter a name for this login page">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="portal-description">Description</label>
                                                            <textarea class="form-control" id="portal-description" rows="2" placeholder="Brief description of this design"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="theme-color">Theme Color</label>
                                                            <div class="color-picker-container">
                                                                <input type="color" class="form-control form-control-color" id="theme-color" value="#7367f0">
                                                                <div class="color-preview" style="background-color: #7367f0;"></div>
                                                                <span class="color-value">#7367f0</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-2 mb-1">
                                                        <h6 class="mb-1">Portal Content</h6>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="welcome-message">Welcome Message</label>
                                                            <input type="text" class="form-control" id="welcome-message" placeholder="Welcome to our WiFi" value="Welcome to our WiFi">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="button-text">Button Text</label>
                                                            <input type="text" class="form-control" id="button-text" placeholder="Connect to WiFi" value="Connect to WiFi">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="login-instructions">Login Instructions</label>
                                                            <textarea class="form-control" id="login-instructions" rows="2" placeholder="Enter your email to connect to our WiFi network">Enter your email to connect to our WiFi network</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="show-terms" checked>
                                                                <label class="custom-control-label" for="show-terms">Show Terms & Conditions Link</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-2 mb-1">
                                                        <h6 class="mb-1">Legal Content</h6>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="terms-of-service">Terms of Service Content</label>
                                                            <textarea class="form-control" id="terms-of-service" rows="3" placeholder="Enter your terms of service content">By accessing this WiFi service, you agree to comply with all applicable laws and the network's acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-1">
                                                        <div class="form-group">
                                                            <label for="privacy-policy">Privacy Policy Content</label>
                                                            <textarea class="form-control" id="privacy-policy" rows="3" placeholder="Enter your privacy policy content">We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        
                                        <!-- Branding Tab -->
                                        <div class="tab-pane" id="branding" aria-labelledby="branding-tab" role="tabpanel">
                                            <form class="mt-2">
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="location-logo">Location Logo</label>
                                                            <div class="upload-area" id="location-logo-upload">
                                                                <i data-feather="upload-cloud" class="upload-icon"></i>
                                                                <h5 class="upload-text">Drop your location logo here or click to browse</h5>
                                                                <p class="text-muted small">Recommended: PNG or SVG, 200x100px</p>
                                                            </div>
                                                            <input type="file" id="location-logo-file" name="location_logo" class="d-none" accept="image/*">
                                                            <img src="" id="location-logo-preview" class="image-preview">
                                                            <p class="note">Your location logo will appear at the top of the login page.</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="background-image">Background Image</label>
                                                            <div class="upload-area" id="background-upload">
                                                                <i data-feather="image" class="upload-icon"></i>
                                                                <h5 class="upload-text">Drop your background image here or click to browse</h5>
                                                                <p class="text-muted small">Recommended: JPG or PNG, 1920x1080px</p>
                                                            </div>
                                                            <input type="file" id="background-file" name="background_image" class="d-none" accept="image/*">
                                                            <img src="" id="background-preview" class="image-preview">
                                                            <p class="note">This image will be displayed as the page background.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 d-flex justify-content-end">
                                            <button id="save-design" class="btn btn-primary">
                                                <i data-feather="save" class="mr-50"></i>Save Design
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add this new preview column -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Preview</h4>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li>
                                                <a data-action="reload"><i data-feather="rotate-cw"></i></a>
                                            </li>
                                            <li>
                                                <a data-action="expand" id="expand-preview"><i data-feather="maximize"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="portal-preview">
                                        <div class="preview-main">
                                            <div class="logo-container">
                                                <img src="{{ asset('img/wifi-placeholder.png') }}" alt="Location Logo" id="preview-logo" class="preview-logo">
                                            </div>
                                            <h2 id="preview-welcome">Welcome to our WiFi</h2>
                                            <p id="preview-instructions">Enter your email to connect to our WiFi network</p>
                                            <div class="input-container">
                                                <input type="text" class="preview-input" placeholder="Email Address">
                                                <button id="preview-button" class="preview-button">Connect to WiFi</button>
                                            </div>
                                            <div id="preview-terms-container" class="preview-terms">
                                                <small>By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#previewTermsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#previewPrivacyModal">Privacy Policy</a>.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- Preview Modals -->
    <div class="modal fade" id="previewTermsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="preview-terms-content">By accessing this WiFi service, you agree to comply with all applicable laws and the network's acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewPrivacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="preview-privacy-content">We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteDesignModal" tabindex="-1" aria-labelledby="deleteDesignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDesignModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this design? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- BEGIN: Vendor JS-->
    <!-- Make sure jQuery is loaded first -->
    <script src="{{ asset('app-assets/vendors/js/jquery/jquery.min.js') }}"></script>
    <!-- Then load Bootstrap bundle which includes Popper.js -->
    <script src="{{ asset('app-assets/vendors/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- Load other vendor scripts -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <!-- END: Vendor JS-->

    <!-- BEGIN: Theme JS -->
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- END: Theme JS -->

    <script>
        // Define token as a global variable
        let token;
        
        // Add this to ensure DOM is fully loaded before running scripts
        $(document).ready(function() {
            // Initialize feather icons
            if (typeof feather !== 'undefined') {
                    feather.replace();
            }
            try {
                token = UserManager.getToken();
                console.log("Token from UserManager:", token ? "Present" : "Missing");
                
                // Fallback to localStorage if needed
                if (!token) {
                    token = localStorage.getItem('jwt_token');
                    console.log("Token from localStorage:", token ? "Present" : "Missing");
                }
                
                if (!token) {
                    console.error("No authentication token found");
                    toastr.error("You appear to be logged out. Please refresh the page and log in again.");
                }
            } catch (e) {
                console.error("Error getting token:", e);
                // Try to get token from localStorage as fallback
                token = localStorage.getItem('jwt_token');
                console.log("Fallback token from localStorage:", token ? "Present" : "Missing");
            }
            
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            // Initial page load - fetch all designs
            console.log("Fetching designs on page load...");
            fetchDesigns();

            // Check if user object exists before trying to access it
            if (typeof user !== 'undefined') {
                $('.user-name').text(user.name);
                $('.user-status').text(user.role);
                
            }
            
            // Make sure preview displays properly on initial load
            initializePreview();
            
            // Fix for modal links in the preview to ensure they work in both normal and fullscreen modes
            $(document).on('click', '.preview-terms a[data-toggle="modal"]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('target');
                $(modalId).modal('show');
            });
            
            // Handle expand preview functionality with fixed modal behavior
            $('[data-action="expand"]').on('click', function(e) {
                e.preventDefault();
                const $previewCard = $(this).closest('.card');
                
                if ($previewCard.hasClass('card-fullscreen')) {
                    // Exit fullscreen
                    $previewCard.removeClass('card-fullscreen');
                    $(this).find('i').replaceWith(feather.icons['maximize'].toSvg());
                } else {
                    // Enter fullscreen
                    $previewCard.addClass('card-fullscreen');
                    $(this).find('i').replaceWith(feather.icons['minimize'].toSvg());
                    
                    // Ensure modals are at body level for proper z-index stacking
                    $('#previewTermsModal, #previewPrivacyModal').appendTo('body');
                    
                    // Reinitialize modal links in fullscreen mode
                    $previewCard.find('.preview-terms a[data-toggle="modal"]').each(function() {
                        const targetModal = $(this).data('target');
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();
                            $(targetModal).modal('show');
                        });
                    });
                }
                
                // Re-initialize feather icons
                feather.replace();
            });

            // Live preview updates
            $('#welcome-message').on('input', function() {
                $('#preview-welcome').text($(this).val() || 'Welcome to our WiFi');
            });

            $('#login-instructions').on('input', function() {
                $('#preview-instructions').text($(this).val() || 'Enter your email to connect to our WiFi network');
            });

            $('#button-text').on('input', function() {
                $('#preview-button').text($(this).val() || 'Connect to WiFi');
            });

            $('#theme-color').on('change', function() {
                const color = $(this).val();
                $('.color-preview').css('background-color', color);
                $('.color-value').text(color);
                $('#preview-button').css({
                    'background-color': color,
                    'border-color': color
                });
            });

            $('#show-terms').on('change', function() {
                $('#preview-terms-container').toggle(this.checked);
            });
            
            $('#terms-of-service').on('input', function() {
                $('#preview-terms-content').text($(this).val() || 'By accessing this WiFi service, you agree to comply with all applicable laws and the network\'s acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.');
            });
            
            $('#privacy-policy').on('input', function() {
                $('#preview-privacy-content').text($(this).val() || 'We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.');
            });

            // Update preview when uploading images
            function readURL(input, previewId) {
                console.log('readURL called for', previewId);
                if (input.files && input.files[0]) {
                    console.log('File found:', input.files[0].name);
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        console.log('File loaded');
                        const preview = $(`#${previewId}`);
                        preview.attr('src', e.target.result);
                        preview.css('display', 'block');
                        
                        if (previewId === 'location-logo-preview') {
                            $('#preview-logo').attr('src', e.target.result).show();
                        } else if (previewId === 'background-preview') {
                            // Set the background image of the portal preview container
                            $('.portal-preview').css({
                                'background-image': `url(${e.target.result})`,
                                'background-size': 'cover',
                                'background-position': 'center',
                                'background-repeat': 'no-repeat'
                            });
                        }
                    }
                    
                    reader.onerror = function(error) {
                        console.error('Error reading file:', error);
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                } else {
                    console.log('No file found in input');
                }
            }

            // Upload area click handlers - update to a more direct approach
            document.getElementById('location-logo-upload').addEventListener('click', function() {
                console.log('Location logo upload area clicked');
                document.getElementById('location-logo-file').click();
            });
            
            document.getElementById('background-upload').addEventListener('click', function() {
                console.log('Background upload area clicked');
                document.getElementById('background-file').click();
            });
            
            // Make sure file inputs are initialized properly
            $('#location-logo-file').on('change', function(e) {
                console.log('Location logo file selected:', e.target.files);
                readURL(this, 'location-logo-preview');
            });

            $('#background-file').on('change', function(e) {
                console.log('Background file selected:', e.target.files);
                readURL(this, 'background-preview');
            });

            // Edit design buttons - now using event delegation for dynamically created elements
            $(document).on('click', '.edit-design', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // alert('edit design');
                const designId = $(this).data('id');
                fetchDesignDetails(designId);
            });

            // Back to Designs button handler
            $('#back-to-list').on('click', function() {
                $('#captive-portal-designer').hide();
                $('#captive-portal-designs-list').show();
                
                // Reset for next use
                resetDesignForm();
                currentDesignId = null;
            });

            // Create New Design button handler
            $('#create-new-design').on('click', function() {
                $('#captive-portal-designs-list').hide();
                $('#captive-portal-designer').show();
                
                // Reset the current design ID when creating new
                currentDesignId = null;
                
                // Set default values
                resetDesignForm();
                
                console.log("Creating new design, form reset with default values");
                
                // Initialize any event handlers or components that need it
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
            
            // Save design button
            $(document).on('click', '#save-design', function() {
                // Validate required fields
                const name = $('#portal-name').val().trim();
                const themeColor = $('#theme-color').val().trim();
                const welcomeMessage = $('#welcome-message').val().trim();
                const buttonText = $('#button-text').val().trim();
                
                // Check for required fields
                let hasErrors = false;
                let errorMessages = [];
                
                if (!name) {
                    hasErrors = true;
                    errorMessages.push('Portal name is required');
                    $('#portal-name').addClass('is-invalid');
                } else {
                    $('#portal-name').removeClass('is-invalid');
                }
                
                if (!themeColor) {
                    hasErrors = true;
                    errorMessages.push('Theme color is required');
                    $('#theme-color').addClass('is-invalid');
                } else {
                    $('#theme-color').removeClass('is-invalid');
                }
                
                if (!welcomeMessage) {
                    hasErrors = true;
                    errorMessages.push('Welcome message is required');
                    $('#welcome-message').addClass('is-invalid');
                } else {
                    $('#welcome-message').removeClass('is-invalid');
                }
                
                if (!buttonText) {
                    hasErrors = true;
                    errorMessages.push('Button text is required');
                    $('#button-text').addClass('is-invalid');
                } else {
                    $('#button-text').removeClass('is-invalid');
                }
                
                if (hasErrors) {
                    toastr.error(errorMessages.join('<br>'));
                    return;
                }

                const formData = new FormData();

                // Collect form data
                formData.append('name', name);
                formData.append('description', $('#portal-description').val());
                formData.append('theme_color', themeColor);
                formData.append('welcome_message', welcomeMessage);
                formData.append('login_instructions', $('#login-instructions').val());
                formData.append('button_text', buttonText);
                formData.append('show_terms', $('#show-terms').is(':checked') ? 1 : 0);
                
                // Add terms of service and privacy policy content
                formData.append('terms_content', $('#terms-of-service').val());
                formData.append('privacy_content', $('#privacy-policy').val());
                
                console.log('formData:', formData);
                // Add files if selected
                if ($('#location-logo-file')[0].files[0]) {
                    formData.append('location_logo', $('#location-logo-file')[0].files[0]);
                }
                
                if ($('#background-file')[0].files[0]) {
                    formData.append('background_image', $('#background-file')[0].files[0]);
                }
                
                // Add CSRF token for Laravel
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                console.log('Current design ID:', currentDesignId);
                console.log('Form data prepared for submission:');
                
                // Log form data for debugging (can't directly console.log FormData)
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
                }
                
                if (currentDesignId) {
                    // Update existing design
                    saveDesign(formData, `/api/captive-portal-designs/${currentDesignId}`);
                } else {
                    // Create new design
                    saveDesign(formData, '/api/captive-portal-designs/create');
                }
            });

            // Add drag and drop functionality
            function setupDragAndDrop(dropAreaId, fileInputId) {
                const dropArea = document.getElementById(dropAreaId);
                const fileInput = document.getElementById(fileInputId);
                
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, unhighlight, false);
                });
                
                function highlight() {
                    dropArea.classList.add('highlight');
                }
                
                function unhighlight() {
                    dropArea.classList.remove('highlight');
                }
                
                dropArea.addEventListener('drop', handleDrop, false);
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    
                    if (files.length) {
                        console.log('File dropped:', files[0].name);
                        fileInput.files = files;
                        $(fileInput).trigger('change');
                    }
                }
            }
            
            // Initialize drag and drop
            setupDragAndDrop('location-logo-upload', 'location-logo-file');
            setupDragAndDrop('background-upload', 'background-file');
        });

        // Function to initialize the preview with default values
        function initializePreview() {
            // Get initial values from form
            const welcomeText = $('#welcome-message').val() || 'Welcome to our WiFi';
            const instructions = $('#login-instructions').val() || 'Enter your email to connect to our WiFi network';
            const buttonText = $('#button-text').val() || 'Connect to WiFi';
            const themeColor = $('#theme-color').val() || '#7367f0';
            const showTerms = $('#show-terms').is(':checked');
            
            // Update preview elements
            $('#preview-welcome').text(welcomeText);
            $('#preview-instructions').text(instructions);
            $('#preview-button').text(buttonText);
            $('#preview-button').css({
                'background-color': themeColor,
                'border-color': themeColor
            });
            $('#preview-terms-container').toggle(showTerms);
            
            // Check if we have a logo in preview
            const logoPreview = $('#location-logo-preview');
            if (logoPreview.attr('src') && logoPreview.css('display') !== 'none') {
                $('#preview-logo').attr('src', logoPreview.attr('src')).show();
            } else {
                $('#preview-logo').hide();
            }
            
            // Check if we have a background image in preview
            const bgPreview = $('#background-preview');
            if (bgPreview.attr('src') && bgPreview.css('display') !== 'none') {
                $('.portal-preview').css({
                    'background-image': `url(${bgPreview.attr('src')})`,
                    'background-size': 'cover',
                    'background-position': 'center',
                    'background-repeat': 'no-repeat'
                });
            }
            
            // Move modals to body to ensure they work with proper z-index
            if ($('#previewTermsModal').parent()[0] !== document.body) {
                $('#previewTermsModal, #previewPrivacyModal').appendTo('body');
            }
        }

        // Global variable to store the current design ID being edited
        let currentDesignId = null;

        // Function to load a design for editing - now fetches from API
        function fetchDesignDetails(designId) {
            console.log("Fetching design details for ID:", designId);
            
            // Show loading state
            $('#captive-portal-designer').prepend(
                `<div class="loading-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>`
            );
            
            // Hide the designs list and show the designer
            $('#captive-portal-designs-list').hide();
            $('#captive-portal-designer').show();
            
            // Fetch design details from API
            $.ajax({
                url: `/api/captive-portal-designs/${designId}`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log('Design details received:', response);
                    
                    // Store the current design ID
                    currentDesignId = designId;
                    
                    if (response.success && response.data) {
                        const design = response.data;
                        console.log('Populating form with design data:', design);
                        
                        // Populate form fields with default fallbacks
                        $('#portal-name').val(design.name || 'New Design');
                        $('#portal-description').val(design.description || '');
                        $('#theme-color').val(design.theme_color || '#7367f0');
                        $('.color-preview').css('background-color', design.theme_color || '#7367f0');
                        $('.color-value').text(design.theme_color || '#7367f0');
                        $('#welcome-message').val(design.welcome_message || 'Welcome to our WiFi');
                        $('#login-instructions').val(design.login_instructions || 'Enter your email to connect to our WiFi network');
                        $('#button-text').val(design.button_text || 'Connect to WiFi');
                        $('#show-terms').prop('checked', design.show_terms === undefined ? true : !!design.show_terms);
                        
                        // Load terms and privacy content if available
                        $('#terms-of-service').val(design.terms_content || 'By accessing this WiFi service, you agree to comply with all applicable laws and the network\'s acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.');
                        $('#privacy-policy').val(design.privacy_content || 'We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.');
                        
                        // Update preview values
                        $('#preview-welcome').text(design.welcome_message || 'Welcome to our WiFi');
                        $('#preview-instructions').text(design.login_instructions || 'Enter your email to connect to our WiFi network');
                        $('#preview-button').text(design.button_text || 'Connect to WiFi');
                        $('#preview-button').css({
                            'background-color': design.theme_color || '#7367f0',
                            'border-color': design.theme_color || '#7367f0'
                        });
                        $('#preview-terms-container').toggle(design.show_terms === undefined ? true : !!design.show_terms);
                        
                        // Update modal content for terms and privacy policy
                        $('#preview-terms-content').text(design.terms_content || 'By accessing this WiFi service, you agree to comply with all applicable laws and the network\'s acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.');
                        $('#preview-privacy-content').text(design.privacy_content || 'We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.');
                        
                        // Handle logo preview if available
                        if (design.location_logo_url) {
                            const logoUrl = design.location_logo_url;
                            $('#location-logo-preview').attr('src', logoUrl).show();
                            $('#preview-logo').attr('src', logoUrl).show();
                        } else if (design.location_logo_path) {
                            const logoUrl = `/storage/${design.location_logo_path}`;
                            $('#location-logo-preview').attr('src', logoUrl).show();
                            $('#preview-logo').attr('src', logoUrl).show();
                        }
                        
                        // Handle background preview if available
                        if (design.background_image_path) {
                            const bgUrl = `/storage/${design.background_image_path}`;
                            $('#background-preview').attr('src', bgUrl).show();
                            
                            // Apply background to preview container
                            $('.portal-preview').css({
                                'background-image': `url(${bgUrl})`,
                                'background-size': 'cover',
                                'background-position': 'center',
                                'background-repeat': 'no-repeat'
                            });
                        }
                    } else {
                        console.error('Invalid response format or missing data');
                        toastr.error('Could not load design details. Invalid response format.');
                    }
                    
                    // Remove loading overlay
                    $('.loading-overlay').remove();
                },
                error: function(xhr) {
                    console.error('Error fetching design details:', xhr.responseText);
                    toastr.error('Failed to load design details. Please try again.');
                    
                    // Remove loading overlay
                    $('.loading-overlay').remove();
                    
                    // Go back to list view
                    $('#captive-portal-designer').hide();
                    $('#captive-portal-designs-list').show();
                }
            });
        }
        
        // Function to fetch all designs and populate the list
        function fetchDesigns() {
            console.log("fetchDesigns called");
            
            // Show loading state
            $('#portal-designs-container').html(
                `<div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading designs...</span>
                    </div>
                </div>`
            );
            
            // Fetch designs from API
            $.ajax({
                url: '/api/captive-portal-designs',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log("Designs received:", response);
                    // Clear container
                    $('#portal-designs-container').empty();
                    
                    if (response.data && response.data.length > 0) {
                        // Populate designs
                        response.data.forEach(function(design) {
                            const bgColorClass = getRandomBgColorClass();
                            const formattedDate = new Date(design.updated_at).toISOString().split('T')[0];
                            
                            const designCard = `
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="card design-card">
                                        <div class="card-body p-2">
                                            <div class="design-preview ${bgColorClass}">
                                                <div class="preview-content">
                                                    <div class="location-logo-mini">
                                                        ${design.location_logo_url ? 
                                                          `<img src="${design.location_logo_url}" alt="${design.name}" style="max-height: 20px;">` : 
                                                          (design.location_logo_path ? 
                                                          `<img src="/storage/${design.location_logo_path}" alt="${design.name}" style="max-height: 20px;">` :
                                                          '<span>Logo</span>')}
                                                    </div>
                                                    <div class="login-area-mini">${design.name}</div>
                                                    <div class="brand-logo-mini">
                                                        <img src="{{ asset('app-assets/mrwifi-assets/Mr-Wifi.PNG') }}" alt="Mr WiFi" style="max-height: 15px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <h5 class="mb-0">${design.name}</h5>
                                                <small class="text-muted">Last modified: ${formattedDate}</small>
                                            </div>
                                            <div class="design-actions mt-1 d-flex justify-content-between align-items-center">
                                                <button class="btn btn-sm btn-outline-primary edit-design" data-id="${design.id}">
                                                    <i data-feather="edit-2" class="mr-25"></i> Edit
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-outline-secondary" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteDesign(${design.id})">
                                                            <i data-feather="trash-2" class="mr-50"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            $('#portal-designs-container').append(designCard);
                        });
                    } else {
                        // No designs found
                        $('#portal-designs-container').html(
                            `<div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <i data-feather="layout" style="height: 64px; width: 64px; color: #d0d0d0;"></i>
                                    <h4 class="mt-2">No captive portal designs found</h4>
                                    <p>Create your first design to get started</p>
                                </div>
                            </div>`
                        );
                    }
                    
                    // Re-initialize feather icons for the newly added elements
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching designs:', xhr.responseText);
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        console.error('Error details:', errorResponse);
                    } catch (e) {
                        console.error('Could not parse error response');
                    }
                    
                    $('#portal-designs-container').html(
                        `<div class="col-12 text-center py-3">
                            <div class="alert alert-danger">
                                Failed to load designs. Please try again later.
                            </div>
                        </div>`
                    );
                }
            });
        }

        // Function to save a design (create or update)
        function saveDesign(formData, url) {
            // Show loading state
            const saveBtn = $('#save-design');
            const originalText = saveBtn.html();
            saveBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            saveBtn.attr('disabled', true);

            console.log('formData #2:', formData);
            
            // Determine the HTTP method based on whether it's a create or update operation
            const isUpdate = !url.includes('/create');
            const method = isUpdate ? 'POST' : 'POST'; // Always use POST, but use method spoofing for updates
            
            // If it's an update, add the _method field for Laravel method spoofing
            if (isUpdate) {
                formData.append('_method', 'PUT');
            }
            
            console.log('Request method:', method);
            console.log('Is update operation:', isUpdate);
            console.log('URL:', url);
            
            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log('Save response:', response);
                    toastr.success('Captive portal design saved successfully');
                    
                    // Return to the designs list and refresh
                    $('#captive-portal-designer').hide();
                    $('#captive-portal-designs-list').show();
                    fetchDesigns();
                    
                    // Reset form for next use
                    resetDesignForm();
                    
                    // Clear background image
                    $('.portal-preview').css({
                        'background-image': 'none',
                        'background-color': '#fff'
                    });
                },
                error: function(xhr) {
                    console.error('Error saving design:', xhr.responseText);
                    
                    try {
                        const responseObj = JSON.parse(xhr.responseText);
                        if (responseObj.debug) {
                            console.log('Debug info:', responseObj.debug);
                        }
                        
                        if (xhr.status === 422 && responseObj.errors) {
                            // Validation errors
                            let errorMessage = 'Please correct the following errors:<br>';
                            
                            for (const field in responseObj.errors) {
                                errorMessage += `- ${responseObj.errors[field][0]}<br>`;
                            }
                            
                            toastr.error(errorMessage);
                        } else {
                            toastr.error(responseObj.message || 'Failed to save design. Please try again.');
                        }
                    } catch (e) {
                        toastr.error('Failed to save design. Please try again.');
                    }
                },
                complete: function() {
                    // Reset button state
                    saveBtn.html(originalText);
                    saveBtn.attr('disabled', false);
                }
            });
        }

        // Helper function to get random background color class for design cards
        function getRandomBgColorClass() {
            const colorClasses = [
                'bg-light-primary',
                'bg-light-success',
                'bg-light-danger',
                'bg-light-warning',
                'bg-light-info'
            ];
            return colorClasses[Math.floor(Math.random() * colorClasses.length)];
        }

        // Function to reset the design form to default values
        function resetDesignForm() {
            // Set default values for required fields
            $('#portal-name').val('New Design');
            $('#portal-description').val('');
            $('#theme-color').val('#7367f0');
            $('.color-preview').css('background-color', '#7367f0');
            $('.color-value').text('#7367f0');
            $('#welcome-message').val('Welcome to our WiFi');
            $('#login-instructions').val('Enter your email to connect to our WiFi network');
            $('#button-text').val('Connect to WiFi');
            $('#show-terms').prop('checked', true);
            
            // Reset terms and privacy policy to default values
            $('#terms-of-service').val('By accessing this WiFi service, you agree to comply with all applicable laws and the network\'s acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.');
            $('#privacy-policy').val('We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.');
            
            // Reset file inputs
            $('#location-logo-file').val('');
            $('#background-file').val('');
            
            // Hide image previews
            $('#location-logo-preview').attr('src', '').hide();
            $('#background-preview').attr('src', '').hide();
            
            // Reset background of preview
            $('.portal-preview').css({
                'background-image': 'none',
                'background-color': '#fff'
            });
            
            // Update preview with default values
            $('#preview-welcome').text('Welcome to our WiFi');
            $('#preview-instructions').text('Enter your email to connect to our WiFi network');
            $('#preview-button').text('Connect to WiFi');
            $('#preview-button').css({
                'background-color': '#7367f0',
                'border-color': '#7367f0'
            });
            $('#preview-terms-container').show();
            $('#preview-logo').attr('src', '').hide();
        }

        // Function to delete a captive portal design
        function deleteDesign(designId) {
            if (!designId) {
                toastr.error('Invalid design ID');
                return;
            }

            // Store the design ID in the modal for reference
            $('#deleteDesignModal').data('designId', designId);
            
            // Show the modal
            $('#deleteDesignModal').modal('show');
        }

        // Set up the delete confirmation button
        $(document).ready(function() {
            $('#confirmDeleteBtn').on('click', function() {
                // Get the design ID from the modal's data
                const designId = $('#deleteDesignModal').data('designId');
                
                // Hide the modal
                $('#deleteDesignModal').modal('hide');
                
                // Show loading state on the design card
                const designCard = $(`.edit-design[data-id="${designId}"]`).closest('.design-card');
                designCard.addClass('opacity-50');
                designCard.append(`
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="top: 0; left: 0; background: rgba(255,255,255,0.7); z-index: 5;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">Deleting...</span>
                        </div>
                    </div>
                `);

                // Create form data with method spoofing for Laravel
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Send delete request to the API
                $.ajax({
                    url: `/api/captive-portal-designs/${designId}`,
                    method: 'DELETE', // Always use POST with _method field for Laravel
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        console.log('Delete response:', response);
                        toastr.success('Design deleted successfully');
                        
                        // Refresh the designs list
                        fetchDesigns();
                    },
                    error: function(xhr) {
                        console.error('Error deleting design:', xhr.responseText);
                        
                        try {
                            const responseObj = JSON.parse(xhr.responseText);
                            toastr.error(responseObj.message || 'Failed to delete design. Please try again.');
                        } catch (e) {
                            toastr.error('Failed to delete design. Please try again.');
                        }
                        
                        // Remove loading state
                        designCard.removeClass('opacity-50');
                        designCard.find('.position-absolute').remove();
                    }
                });
            });
        });
    </script>
</body>
</html>