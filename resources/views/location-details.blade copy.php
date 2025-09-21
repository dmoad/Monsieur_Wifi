<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Location Details - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/Mr-Wifi.PNG">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <!-- END: Vendor CSS-->
    
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-online {
            background-color: rgba(40, 199, 111, 0.12);
            color: #28c76f;
        }
        
        .status-offline {
            background-color: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }
        
        .status-warning {
            background-color: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
        }
        
        .location-map {
            height: 250px;
            width: 100%;
            border-radius: 0.428rem;
        }
        
        .network-config-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .network-config-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .tab-content {
            padding-top: 20px;
        }
        
        .power-slider .noUi-connect {
            background: #7367F0;
        }
        
        .schedule-table th, .schedule-table td {
            padding: 0.5rem;
        }
        
        .filtered-mac-list {
            max-height: 150px;
            overflow-y: auto;
        }
        
        .filtered-mac-item {
            padding: 5px;
            margin-bottom: 5px;
            background-color: #f8f8f8;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .channel-scan-results {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>

    <!-- Add this right after the existing styles and before closing the head tag -->
    <style>
        /* Collapsible section styles */
        .collapsible-header {
            cursor: pointer;
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .collapsible-header:hover {
            background-color: #eee;
        }

        .collapsible-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .collapsible-content {
            display: none;
            padding: 1rem;
            border-left: 3px solid #7367f0;
            margin-left: 0.5rem;
            margin-bottom: 1.5rem;
            background-color: #fcfcfc;
            border-radius: 0 5px 5px 0;
        }

        /* Card content grouping */
        .card-group-title {
            font-weight: 600;
            color: #5e5873;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Tab organization improvements */
        .config-section {
            margin-bottom: 1.5rem;
        }

        /* Compact form elements */
        .form-compact .form-group {
            margin-bottom: 0.75rem;
        }

        /* Improved navigation */
        .tab-navigation {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .nav-tab-action {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin: 0 0.5rem 0.5rem 0;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-tab-action:hover, .nav-tab-action.active {
            background-color: #7367f0;
            color: white;
        }

        /* Better switch status indicators */
        .switch-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Summary views */
        .summary-view {
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            color: #6e6b7b;
        }

        .summary-value {
            font-weight: 500;
        }

        /* Improved social media options */
        #social-settings {
            background-color: #f8f8f8;
            border-radius: 5px;
            padding: 1rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border-left: 3px solid #7367f0;
        }
    </style>

    <!-- Add this CSS right after the existing styles and before closing the head tag -->
    <style>
        /* Enhanced Channel Scanning Modal Styles */
        .scan-pulse-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
        
        .scan-pulse-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background-color: #7367f0;
            border-radius: 50%;
            z-index: 2;
        }
        
        .scan-pulse-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #7367f0;
            border-radius: 50%;
            opacity: 1;
            z-index: 1;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                width: 30px;
                height: 30px;
                opacity: 1;
            }
            100% {
                width: 80px;
                height: 80px;
                opacity: 0;
            }
        }
        
        /* Timeline styling for scan steps */
        .timeline {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.85rem;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-point {
            position: absolute;
            left: 0;
            top: 0;
        }
        
        .timeline-point-indicator {
            display: inline-block;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: #ebe9f1;
        }
        
        .timeline-point-primary {
            background-color: #7367f0 !important;
        }
        
        .timeline-point-secondary {
            background-color: #82868b !important;
        }
        
        .timeline-point-success {
            background-color: #28c76f !important;
        }
        
        /* Channel recommendation cards */
        .channel-recommendation {
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #7367f0;
        }
        
        .channel-value {
            font-size: 2rem;
            font-weight: 600;
            color: #5e5873;
        }
        
        .interference-meter {
            height: 6px;
            background-color: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        .interference-level {
            height: 100%;
            border-radius: 3px;
            background-color: #28c76f;
        }
        
        .interference-low {
            background-color: #28c76f;
            width: 20%;
        }
        
        .interference-medium {
            background-color: #ff9f43;
            width: 50%;
        }
        
        .interference-high {
            background-color: #ea5455;
            width: 80%;
        }
    </style>

    <!-- Add this right before the closing body tag -->
    <style>
        /* Fix for oscillating progress bar */
        #channel-scan-modal .progress-bar {
            transition: width 0.5s linear;
        }
        
        /* Fix for modal close icon */
        #channel-scan-modal .modal-header .close {
            color: #fff;
            text-shadow: none;
            opacity: 0.8;
            padding: 1rem;
            margin: -1rem;
        }
        
        #channel-scan-modal .modal-header .close:hover {
            opacity: 1;
        }
        
        /* Ensure feather icon in close button is visible */
        #channel-scan-modal .modal-header .close span {
            font-size: 1.5rem;
            display: block;
            line-height: 1;
        }
        
        /* Improve modal header icon alignment */
        #channel-scan-modal .modal-title i {
            vertical-align: middle;
            margin-top: -3px;
        }
    </style>
</head>
<!-- END: Head-->


<!-- BEGIN: Body-->
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
                <li class="nav-item dropdown dropdown-notification mr-25">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge badge-pill badge-primary badge-up">5</span>
                    </a>
                    <!-- Notification dropdown content -->
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <!-- Notification content here -->
                        </ul>
                    </li>
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div><span class="avatar"><img class="round" src="app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
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
                            <img src="../../../app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo">
                        </span>
                        <h2 class="brand-text">monsieur-wifi</h2>
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
                <li class="nav-item active">
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

                <li class="nav-item">
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
                            <h2 class="content-header-title float-left mb-0">Location Details</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.html">Home</a></li>
                                <li class="breadcrumb-item"><a href="locations.html">Locations</a></li>
                                <li class="breadcrumb-item active"><span class="location_name"></span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <a href="location-analytics.html" class="btn btn-primary">Analytics</a>
                    </div>
                </div>
            </div>
            <div class="content-body">
                    <!-- Location Overview -->
            <div class="row">
                        <!-- Location Info Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h4 class="font-weight-bolder mb-0"><span class="location_name"></span></h4>
                                    <p class="card-text text-muted"><span class="location_address"></span></p>
                                        </div>
                                        <span class="status-badge status-offline">Offline</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Router Model:</span>
                                <span class="font-weight-bold"><span class="router_model"></span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Firmware:</span>
                                <span class="font-weight-bold"><span class="router_firmware"></span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Connected Users:</span>
                                <span class="font-weight-bold"><span class="connected_users"></span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Daily Usage:</span>
                                <span class="font-weight-bold"><span class="daily_usage"></span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Uptime:</span>
                                <span class="font-weight-bold"><span class="uptime"></span></span>
                                        </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary btn-sm">
                                    <i data-feather="refresh-cw" class="mr-50"></i>
                                    <span>Restart</span>
                                </button>
                                <button class="btn btn-outline-primary btn-sm" id="update-firmware-btn" style="position: relative; z-index: 1;">
                                    <i data-feather="download" class="mr-50"></i>
                                    <span>Update Firmware</span>
                                </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                <!-- Usage Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Current Usage</h4>
                                    <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Today
                                        </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:void(0);">Today</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Last 7 Days</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Last 30 Days</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                
                            <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-primary p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="arrow-down" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0">5.8 GB</h4>
                                                    <p class="card-text text-muted">Download</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-info p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="arrow-up" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0">2.4 GB</h4>
                                                    <p class="card-text text-muted">Upload</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-primary p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="users" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0">24</h4>
                                                    <p class="card-text text-muted">Users Connected</p>
                                        </div>
                                    </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-info p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="clock" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0">4.2 hrs</h4>
                                                    <p class="card-text text-muted">Avg. Session Time</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Map Card -->
                        <div class="col-lg-4 col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                            <h4 class="card-title">Location</h4>
                                </div>
                        <div class="card-body p-0">
                            <div id="location-map" class="location-map" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Network Configuration Tabs -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Network Configuration</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="location-settings-tab" data-toggle="tab" href="#location-settings" role="tab" aria-controls="location-settings" aria-selected="false">
                                        <i class="fas fa-building mr-2"></i>Location Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " id="router-tab" data-toggle="tab" href="#router" aria-controls="router" role="tab" aria-selected="true">
                                        <i data-feather="hard-drive" class="mr-50"></i>Router Settings
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="captive-portal-tab" data-toggle="tab" href="#captive-portal" aria-controls="captive-portal" role="tab" aria-selected="false">
                                        <i data-feather="layout" class="mr-50"></i>Captive Portal WiFi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="secured-wifi-tab" data-toggle="tab" href="#secured-wifi" aria-controls="secured-wifi" role="tab" aria-selected="false">
                                        <i data-feather="lock" class="mr-50"></i>Password WiFi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="connected-users-tab" data-toggle="tab" href="#connected-users" aria-controls="connected-users" role="tab" aria-selected="false">
                                        <i data-feather="users" class="mr-50"></i>Connected Users
                                    </a>
                                </li>
                                <!-- Add to your tab navigation -->
                               
                            </ul>
                            <div class="tab-content">
                                <!-- Router Settings Tab -->
                                <div class="tab-pane fade" id="router" aria-labelledby="router-tab" role="tabpanel">
                                        <!-- NEW: Network Interface Management -->
                                        <div class="row col-12 mt-2">
                                            <div class="col-md-4 col-12 match-height" id="wan-interface">
                                                <div class="card shadow-none border">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title">WAN Interface</h4>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" aria-expanded="false">
                                                                <i data-feather="more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#wan-settings-modal">
                                                                    <i data-feather="edit" class="mr-50"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- WAN IP Details -->
                                                        <div class="ip-details">
                                                            <h6 class="mb-1">IP Configuration</h6>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Connection Type:</span>
                                                                <span class="font-weight-bold" id="wan-type-display">DHCP</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">IP Address:</span>
                                                                <span class="font-weight-bold" id="wan-ip-display">192.168.1.1</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Subnet Mask:</span>
                                                                <span class="font-weight-bold" id="wan-subnet-display">255.255.255.0</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Gateway:</span>
                                                                <span class="font-weight-bold" id="wan-gateway-display">192.168.1.1</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Primary DNS:</span>
                                                                <span class="font-weight-bold" id="wan-dns1-display">8.8.8.8</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Secondary DNS:</span>
                                                                <span class="font-weight-bold" id="wan-dns2-display">8.8.4.4</span>
                                                            </div>
                                                        </div>
                                                        <div class="interface-actions mt-3">
                                                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#wan-settings-modal">
                                                                <i data-feather="edit" class="mr-50" style="width: 14px; height: 14px;"></i>Edit WAN Settings
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12 match-height" id="captive-portal-interface">
                                                <div class="card shadow-none border">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title">Captive Portal</h4>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" aria-expanded="false">
                                                                <i data-feather="more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#captive-portal-modal">
                                                                    <i data-feather="edit" class="mr-50"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Captive Portal IP Details -->
                                                        <div class="ip-details">
                                                            <h6 class="mb-1">IP Configuration</h6>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Connection Type:</span>
                                                                <span class="font-weight-bold">Static IP</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">IP Address:</span>
                                                                <span class="font-weight-bold" id="captive-ip-display"></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Subnet Mask:</span>
                                                                <span class="font-weight-bold" id="captive-netmask-display"></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Gateway:</span>
                                                                <span class="font-weight-bold" id="captive-gateway-display"></span>
                                                            </div>
                                                        </div>
                                                        <div class="interface-actions mt-3">
                                                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#captive-portal-modal">
                                                                <i data-feather="edit" class="mr-50" style="width: 14px; height: 14px;"></i>Edit Captive Portal Settings
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12 match-height" id="password-wifi-interface">
                                                <div class="card shadow-none border">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title">Password WiFi</h4>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" aria-expanded="false">
                                                                <i data-feather="more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#password-network-modal">
                                                                    <i data-feather="edit" class="mr-50"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Password WiFi IP Details -->
                                                        <div class="ip-details">
                                                            <h6 class="mb-1">IP Configuration</h6>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Connection Type:</span>
                                                                <span class="font-weight-bold" id="password-ip-type-display">Static IP</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">IP Address:</span>
                                                                <span class="font-weight-bold" id="password-ip-display">192.168.1.1</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Subnet Mask:</span>
                                                                <span class="font-weight-bold" id="password-netmask-display">255.255.255.0</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Gateway:</span>
                                                                <span class="font-weight-bold" id="password-gateway-display">192.168.1.1</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">DHCP Server:</span>
                                                                <span class="font-weight-bold" id="password-dhcp-status-display">Enabled</span>
                                                            </div>
                                                        </div>
                                                        <div class="interface-actions mt-3">
                                                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#password-network-modal">
                                                                <i data-feather="edit" class="mr-50" style="width: 14px; height: 14px;"></i>Edit Password Network
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <!-- Power Settings -->
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                <h4 class="card-title">WiFi Radio Settings</h4>
                                            </div>
                                            <div class="card-body">
                                                    <div class="form-group">
                                            <label for="wifi-country">Country</label>
                                            <select class="form-control" id="wifi-country">
                                                <option value="US" selected>United States (US)</option>
                                                <option value="CA">Canada (CA)</option>
                                                <option value="GB">United Kingdom (GB)</option>
                                                <option value="FR">France (FR)</option>
                                                <option value="DE">Germany (DE)</option>
                                                <option value="IT">Italy (IT)</option>
                                                <option value="ES">Spain (ES)</option>
                                                <option value="AU">Australia (AU)</option>
                                                <option value="JP">Japan (JP)</option>
                                                <option value="CN">China (CN)</option>
                                                <option value="IN">India (IN)</option>
                                                <option value="BR">Brazil (BR)</option>
                                                <option value="ZA">South Africa (ZA)</option>
                                                <option value="AE">United Arab Emirates (AE)</option>
                                                <option value="SG">Singapore (SG)</option>
                                            </select>
                                            <small class="text-muted">Sets regional rules for channels and power limits.</small>
                                    </div>
                                        
                                        
                                                    <div class="form-group">
                                            <label for="power-level-2g">Transmit Power (2.4 GHz)</label>
                                            <select class="form-control" id="power-level-2g">
                                                <option value="20">20 dBm (100 mW) - Maximum</option>
                                                <option value="17">17 dBm (50 mW) - High</option>
                                                <option value="15" selected>15 dBm (30 mW) - Medium</option>
                                                <option value="12">12 dBm (15 mW) - Low</option>
                                                <option value="10">10 dBm (10 mW) - Minimum</option>
                                            </select>
                                            <small class="text-muted">Higher power provides better range but uses more energy and may cause interference.</small>
                                </div>
                                        
                                                    <div class="form-group">
                                            <label for="power-level-5g">Transmit Power (5 GHz)</label>
                                            <select class="form-control" id="power-level-5g">
                                                <option value="23">23 dBm (200 mW) - Maximum</option>
                                                <option value="20">20 dBm (100 mW) - High</option>
                                                <option value="17" selected>17 dBm (50 mW) - Medium</option>
                                                <option value="14">14 dBm (25 mW) - Low</option>
                                                <option value="10">10 dBm (10 mW) - Minimum</option>
                                                        </select>
                                            <small class="text-muted">5 GHz signals don't penetrate walls as well as 2.4 GHz but provide faster speeds.</small>
                                        </div>
                                                    
                                        <button class="btn btn-primary btn-block" id="save-power-settings">Save Radio Settings</button>
                                </div>
                            </div>
                        </div>

                                            <!-- Channel Optimization -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                                <div class="card-header">
                                    <h4 class="card-title">Channel Optimization</h4>
                                        </div>
                                                <div class="card-body">
                                    <div class="mb-2">
                                        
                                        
                                        <!-- Channel width for 2.4 GHz -->
                                                    <div class="form-group">
                                            <label for="channel-width-2g">Channel Width (2.4 GHz)</label>
                                            <select class="form-control" id="channel-width-2g">
                                                <option value="20">20 MHz </option>
                                                <option value="40" selected>40 MHz</option>
                                            </select>
                                            </div>
                                        
                                        <!-- Channel width for 5 GHz -->
                                        <div class="form-group">
                                            <label for="channel-width-5g">Channel Width (5 GHz)</label>
                                            <select class="form-control" id="channel-width-5g">
                                                <option value="20">20 MHz </option>
                                                <option value="40">40 MHz </option>
                                                <option value="80" selected>80 MHz </option>
                                                <option value="160">160 MHz </option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary btn-block" id="save-channel-settings">Save Channel Settings</button>
                                        
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header pull-right">
                                    <div class="d-flex justify-content-center mt-2 pull-right">
                                        <button class="btn btn-primary btn-sm" id="scan-channels-btn">
                                            <i data-feather="wifi" class="mr-1"></i>  Channel Scan
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <!-- Channel selection for 2.4 GHz -->
                                                    <div class="form-group">
                                            <label for="channel-2g">Channel (2.4 GHz)</label>
                                            <div class="d-flex align-items-center">
                                                <select class="form-control mr-1" id="channel-2g">
                                                    <option value="1">Channel 1 (2412 MHz)</option>
                                                    <option value="2">Channel 2 (2417 MHz)</option>
                                                    <option value="3">Channel 3 (2422 MHz)</option>
                                                    <option value="4">Channel 4 (2427 MHz)</option>
                                                    <option value="5">Channel 5 (2432 MHz)</option>
                                                    <option value="6">Channel 6 (2437 MHz)</option>
                                                    <option value="7">Channel 7 (2442 MHz)</option>
                                                    <option value="8">Channel 8 (2447 MHz)</option>
                                                    <option value="9">Channel 9 (2452 MHz)</option>
                                                    <option value="10">Channel 10 (2457 MHz)</option>
                                                    <option value="11">Channel 11 (2462 MHz)</option>
                                                </select>
                                                
                                                                </div>
                                            <small class="text-muted">Channels 1, 6, or 11 typically have less interference.</small>
                                                            </div>
                                        
                                       
                                        
                                        <!-- Channel selection for 5 GHz -->
                                        <div class="form-group">
                                            <label for="channel-5g">Channel (5 GHz)</label>
                                            <div class="d-flex align-items-center">
                                                <select class="form-control mr-1" id="channel-5g">
                                                    <option value="36">Channel 36 (5180 MHz)</option>
                                                    <option value="40">Channel 40 (5200 MHz)</option>
                                                    <option value="44">Channel 44 (5220 MHz)</option>
                                                    <option value="48">Channel 48 (5240 MHz)</option>
                                                    <option value="52">Channel 52 (5260 MHz)</option>
                                                    <option value="56">Channel 56 (5280 MHz)</option>
                                                    <option value="60">Channel 60 (5300 MHz)</option>
                                                    <option value="64">Channel 64 (5320 MHz)</option>
                                                    <option value="100">Channel 100 (5500 MHz)</option>
                                                    <option value="104">Channel 104 (5520 MHz)</option>
                                                    <option value="108">Channel 108 (5540 MHz)</option>
                                                    <option value="112">Channel 112 (5560 MHz)</option>
                                                    <option value="116">Channel 116 (5580 MHz)</option>
                                                    <option value="132">Channel 132 (5660 MHz)</option>
                                                    <option value="136">Channel 136 (5680 MHz)</option>
                                                    <option value="140">Channel 140 (5700 MHz)</option>
                                                </select>
                                                
                                                        </div>
                                            <small class="text-muted">5 GHz typically has less interference than 2.4 GHz.</small>
                                                                </div>
                                        
                                      
                                        <div class="d-flex justify-content-center mt-2">
                                            <button class="btn btn-primary" id="save-channels-btn">
                                                <i data-feather="edit" class="mr-1"></i> Save Channel Settings
                                                            </button>
                                                            </div>
                                        
                                                        </div>
                                </div>
                            </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                    <!-- Simplified Captive Portal WiFi Tab Content -->
                                <div class="tab-pane fade" id="captive-portal" role="tabpanel" aria-labelledby="captive-portal-tab">
                                            <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Captive Portal WiFi</h4>
                                            <button class="btn btn-primary" id="save-captive-portal">
                                                <i data-feather="save" class="mr-1"></i> Save Settings
                                                </button>
                                                                </div>
                                    
                                                <div class="card-body">
                                            <!-- Basic Settings Section -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="portal-ssid">Network Name (SSID)</label>
                                                        <input type="text" class="form-control" id="portal-ssid" placeholder="Guest WiFi">
                                                            </div>
                                                        </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="portal-visibility">Network Visibility</label>
                                                        <select class="form-control" id="portal-visibility">
                                                            <option value="visible" selected>Visible (Broadcast SSID)</option>
                                                            <option value="hidden">Hidden (Don't Broadcast SSID)</option>
                                                        </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                            <!-- Authentication Section -->
                                            <h5 class="border-bottom pb-1">Authentication</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="captive-auth-method">Authentication Method</label>
                                                        <select class="form-control" id="captive-auth-method">
                                                            <option value="click-through" selected>Click-through (No Authentication)</option>
                                                            <option value="password">Password-based</option>
                                                            <option value="sms">SMS Verification</option>
                                                            <option value="email">Email Verification</option>
                                                            <option value="social">Social Media Login</option>
                                                        </select>
                                            </div>
                                                    
                                                    <!-- Session Settings -->
                                                    <div class="row">
                                                        <div class="col-6">
                                                        <div class="form-group">
                                                                <label for="captive-session-timeout">Session (mins)</label>
                                                                <input type="number" class="form-control form-control-sm" id="captive-session-timeout" value="60">
                                                    </div>
                                                            </div>
                                                        <div class="col-6">
                                                    <div class="form-group">
                                                                <label for="captive-idle-timeout">Idle (mins)</label>
                                                                <input type="number" class="form-control form-control-sm" id="captive-idle-timeout" value="15">
                                    </div>
                                                        </div>
                                                    </div>
                                                        </div>

                                                <div class="col-md-6">
                                                    <!-- Password Auth Options -->
                                                    <div id="password-auth-options" class="auth-options-section" style="display: none;">
                                                    <div class="form-group">
                                                            <label for="portal-shared-password">Shared Password</label>
                                                            <div class="input-group">
                                                                <input type="password" id="portal-shared-password" class="form-control" placeholder="Enter password">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary" type="button" id="toggle-portal-password">
                                                                        <i data-feather="eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                </div>
                            </div>

                                                    <!-- SMS/Email Auth Options -->
                                                    <div id="sms-auth-options" class="auth-options-section" style="display: none;">
                                                        <div class="alert alert-info mb-0 p-2">SMS verification will be used to authenticate guests.</div>
                        </div>

                                                    <div id="email-auth-options" class="auth-options-section" style="display: none;">
                                                        <div class="alert alert-info mb-0 p-2">Email verification will be used to authenticate guests.</div>
                                </div>

                                                    <!-- Social Login Options -->
                                                    <div id="social-auth-options" class="auth-options-section" style="display: none;">
                                                        <label>Enable Social Login Options</label>
                                                        <div class="d-flex flex-wrap">
                                                            <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                                <input type="checkbox" class="custom-control-input" id="social-facebook" checked>
                                                                <label class="custom-control-label" for="social-facebook">Facebook</label>
                                                            </div>
                                                            <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                                <input type="checkbox" class="custom-control-input" id="social-google" checked>
                                                                <label class="custom-control-label" for="social-google">Google</label>
                                                            </div>
                                                            <div class="custom-control custom-switch custom-control-primary mr-2 mb-1">
                                                                <input type="checkbox" class="custom-control-input" id="social-twitter">
                                                                <label class="custom-control-label" for="social-twitter">Twitter</label>
                                                            </div>
                                                            <div class="custom-control custom-switch custom-control-primary mb-1">
                                                                <input type="checkbox" class="custom-control-input" id="social-apple">
                                                                <label class="custom-control-label" for="social-apple">Apple</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Network Settings Section -->
                                            <!-- <h5 class="border-bottom pb-1 mt-3">Network Settings</h5>
                                                    <div class="row">
                                                <div class="col-md-3">
                                                            <div class="form-group">
                                                        <label for="captive-ip-address">IP Address</label>
                                                        <input type="text" class="form-control" id="captive-ip-address" placeholder="192.168.2.1">
                                                </div>
                                                </div>
                                                <div class="col-md-3">
                                                            <div class="form-group">
                                                        <label for="captive-netmask">Netmask</label>
                                                        <input type="text" class="form-control" id="captive-netmask" placeholder="255.255.255.0">
                                            </div>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-end mb-1">
                                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#captive-network-modal">
                                                        <i data-feather="edit" class="mr-1"></i> Advanced Network Settings
                                                    </button>
                                                </div>
                                            </div> -->
                                            <!-- Bandwidth Section -->
                                            <h5 class="border-bottom pb-1 mt-3">Bandwidth & Portal</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">Bandwidth Limits</label>
                                                           
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                 <label for="captive-download-limit">Download (Mbps)</label>
                                                                <input type="number" class="form-control form-control-sm" id="captive-download-limit" placeholder="Download (Mbps)" value="10">
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="captive-upload-limit">Upload (Mbps)</label>
                                                                <input type="number" class="form-control form-control-sm" id="captive-upload-limit" placeholder="Upload (Mbps)" value="2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="mb-2">Portal Configuration</label>
                                                        <select class="form-control form-control-sm" id="captive-portal-config">
                                                            <!-- Options will be populated dynamically -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Access Control Section -->
                                            <h5 class="border-bottom pb-1 mt-3">Access Control</h5>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <!-- MAC Filtering -->
                                                            <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">MAC Filtering</label>
                                                            <div>
                                                                <select class="form-control form-control-sm" id="portal-mac-filter">
                                                                    <option value="allow-all" selected>Allow All</option>
                                                                    <option value="allow-listed">Allow Listed Only</option>
                                                                    <option value="block-listed">Block Listed</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-1">
                                                            <input type="text" class="form-control" id="captive-mac-address" placeholder="00:11:22:33:44:55">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-sm btn-primary" id="captive-add-mac">Add</button>
                                                            </div>
                                                        </div>
                                                        <div class="filtered-mac-list border p-1 rounded" style="max-height: 100px; overflow-y: auto;">
                                                            <!-- MAC addresses will be added here dynamically -->
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <!-- Web Filtering -->
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">Web Filtering</label>
                                                            <div class="custom-control custom-switch custom-control-primary">
                                                                <input type="checkbox" class="custom-control-input" id="guest-secured-web-filter" checked>
                                                                <label class="custom-control-label" for="guest-secured-web-filter"></label>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-1">
                                                            <select class="select2" id="guest-secured-web-filter" multiple="multiple">
                                                                <option value="adult-content">Adult Content</option>
                                                                <option value="gambling">Gambling</option>
                                                                <option value="social-media">Social Media</option>
                                                                <option value="streaming">Streaming Services</option>
                                                                <option value="malware">Malware & Phishing</option>
                                                            </select>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>

                                <!-- Simplified Password WiFi Tab Content -->
                                <div class="tab-pane fade" id="secured-wifi" role="tabpanel" aria-labelledby="secured-wifi-tab">
                                            <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Password WiFi</h4>
                                            <button class="btn btn-primary" id="save-secured-wifi">
                                                <i data-feather="save" class="mr-1"></i> Save Settings
                                            </button>
                                            </div>
                                        
                                                <div class="card-body">
                                            <!-- Basic Settings Section -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="wifi-ssid">Network Name (SSID)</label>
                                                        <input type="text" class="form-control" id="wifi-ssid" placeholder="Home WiFi">
                                                </div>
                                            </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="wifi-password">WiFi Password</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="wifi-password" placeholder="Password">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                                                    <i data-feather="eye"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="wifi-security">Security Type</label>
                                                        <select class="form-control" id="wifi-security">
                                                            <option value="wpa2-psk" selected>WPA2-PSK (Recommended)</option>
                                                            <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixed</option>
                                                            <option value="wpa3-psk">WPA3-PSK (Most Secure)</option>
                                                            <option value="wep">WEP (Legacy, Not Recommended)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="wifi-security">Cipher Suites</label>
                                                        <select class="form-control" id="wifi-security">
                                                            <option value="wpa2-psk" selected>CCMP</option>
                                                            <option value="wpa-wpa2-psk">TKIP</option>
                                                            <option value="wpa3-psk">TKIP+CCMP</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Network Settings Section -->
                                            <!-- <h5 class="border-bottom pb-1 mt-2">Network Settings</h5>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>IP Configuration</label>
                                                        <div>
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="static-ip-radio" name="password-ip-assignment" class="custom-control-input" checked>
                                                                <label class="custom-control-label" for="static-ip-radio">Static IP</label>
                                                </div>
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="dhcp-client-radio" name="password-ip-assignment" class="custom-control-input">
                                                                <label class="custom-control-label" for="dhcp-client-radio">DHCP Client</label>
                                            </div>
                                                </div>
                                            </div>
                                        </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="static-ip-field">
                                                        <label for="password-ip-address">IP Address</label>
                                                        <input type="text" class="form-control" id="password-ip-address" placeholder="192.168.1.1">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="static-netmask-field">
                                                        <label for="password-netmask">Netmask</label>
                                                        <input type="text" class="form-control" id="password-netmask" placeholder="255.255.255.0">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end mb-1">
                                                    <button class="btn btn-outline-primary btn-sm" id="advanced-network-btn" data-toggle="modal" data-target="#password-network-modal">
                                                        <i data-feather="edit" class="mr-1"></i> Advanced Settings
                                                    </button>
                                        </div>
                                    </div>

                                            <div id="dhcp-client-message" style="display: none;">
                                                <div class="alert alert-info p-2">
                                                    <i data-feather="info" class="mr-1 align-middle"></i>
                                                    <span>In DHCP Client mode, this WiFi interface will request an IP address from an upstream DHCP server.</span>
                                                </div>
                                            </div>
                                             -->
                                            <!-- Access Control Section -->
                                            <h5 class="border-bottom pb-1 mt-3">Access Control</h5>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <!-- MAC Filtering -->
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">MAC Filtering</label>
                                                            <div>
                                                                <select class="form-control form-control-sm" id="secured-mac-filter">
                                                                    <option value="allow-all" selected>Allow All</option>
                                                                    <option value="allow-listed">Allow Listed Only</option>
                                                                    <option value="block-listed">Block Listed</option>
                                                                </select>
                                            </div>
                                                </div>
                                                        <div class="input-group mb-1">
                                                            <input type="text" class="form-control" id="secured-mac-address" placeholder="00:11:22:33:44:55">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-sm btn-primary" id="secured-add-mac">Add</button>
                                            </div>
                                                </div>
                                                        <div class="filtered-mac-list border p-1 rounded" style="max-height: 100px; overflow-y: auto;">
                                                            <!-- MAC addresses will be added here dynamically -->
                                            </div>
                                        </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <!-- Web Filtering -->
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">Web Filtering</label>
                                                            <div class="custom-control custom-switch custom-control-primary">
                                                                <input type="checkbox" class="custom-control-input" id="secured-web-filter" checked>
                                                                <label class="custom-control-label" for="secured-web-filter"></label>
                                                            </div>
                                                        </div>
                                                        <div class="input-group mb-1">
                                                            <select class="select2" id="secured-web-filter" multiple="multiple">
                                                                <option value="adult-content">Adult Content</option>
                                                                <option value="gambling">Gambling</option>
                                                                <option value="social-media">Social Media</option>
                                                                <option value="streaming">Streaming Services</option>
                                                                <option value="malware">Malware & Phishing</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- QoS Section -->
                                            <!-- <h5 class="border-bottom pb-1 mt-3">Quality of Service</h5>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">Enable QoS</label>
                                                            <div class="custom-control custom-switch custom-control-primary">
                                                                <input type="checkbox" class="custom-control-input" id="secured-qos">
                                                                <label class="custom-control-label" for="secured-qos"></label>
                                        </div>
                                    </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="secured-priority">Traffic Priority</label>
                                                        <select class="form-control form-control-sm" id="secured-priority">
                                                            <option value="high" selected>High Priority</option>
                                                            <option value="medium">Medium Priority</option>
                                                            <option value="low">Low Priority</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="secured-bandwidth">Reserved Bandwidth (%)</label>
                                                        <input type="number" class="form-control form-control-sm" id="secured-bandwidth" value="70">
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Connected Users Tab -->
                                <div class="tab-pane" id="connected-users" aria-labelledby="connected-users-tab" role="tabpanel">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Connected Devices</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>User/Device</th>
                                                                    <th>MAC Address</th>
                                                                    <th>IP Address</th>
                                                                    <th>Network</th>
                                                                    <th>Connected Time</th>
                                                                    <th>Data Usage</th>
                                                                    <th>Signal</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Example Row -->
                                                                <tr>
                                                                    <td>John Doe (iPhone 13)</td>
                                                                    <td>00:1A:2B:3C:4D:5E</td>
                                                                    <td>192.168.10.101</td>
                                                                    <td><span class="badge badge-light-info">Captive Portal</span></td>
                                                                    <td>1h 23m</td>
                                                                    <td>125 MB</td>
                                                                    <td>Excellent (85%)</td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-icon btn-outline-danger">
                                                                            <i data-feather="user-x"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                            </div>
                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                

                                <!-- Location Details Tab -->
<div class="tab-pane active" id="location-settings" role="tabpanel" aria-labelledby="location-settings-tab">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Location Information</h4>
        </div>
        <div class="card-body">
            <form id="location-info-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location-name">Location Name</label>
                            <input type="text" class="form-control" id="location-name" placeholder="Enter location name">
                        </div>
                        <div class="form-group">
                            <label for="location-address">Address</label>
                            <input type="text" class="form-control" id="location-address" placeholder="Street address">
                        </div>
                        <div class="form-group">
                            <label for="location-city">City</label>
                            <input type="text" class="form-control" id="location-city" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label for="location-state">State/Province</label>
                            <input type="text" class="form-control" id="location-state" placeholder="State/Province">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location-postal-code">Postal Code</label>
                            <input type="text" class="form-control" id="location-postal-code" placeholder="Postal code">
                        </div>
                        <div class="form-group">
                            <label for="location-country">Country</label>
                            <input type="text" class="form-control" id="location-country" placeholder="Country">
                        </div>
                        <div class="form-group">
                            <label for="location-manager">Manager Name</label>
                            <input type="text" class="form-control" id="location-manager" placeholder="Manager name">
                        </div>
                        <div class="form-group">
                            <label for="location-contact-email">Contact Email</label>
                            <input type="email" class="form-control" id="location-contact-email" placeholder="Contact email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location-contact-phone">Contact Phone</label>
                            <input type="text" class="form-control" id="location-contact-phone" placeholder="Contact phone">
                        </div>
                        <div class="form-group">
                            <label for="location-status">Status</label>
                            <select class="form-control" id="location-status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location-description">Description</label>
                            <textarea class="form-control" id="location-description" rows="3" placeholder="Location description"></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" id="save-location-info" class="btn btn-primary">Save Location Information</button>
            </form>
        </div>
    </div>
</div>
</div><!-- End .tab-content -->
                            </div><!-- End .card-body -->
                        </div><!-- End .card -->
                    </div><!-- End .col-12 -->
                </div><!-- End .row -->
            </div><!-- End .content-body -->
        </div><!-- End .content-wrapper -->
    </div><!-- End .content -->
    <!-- END: Content -->

    <!-- BEGIN: Modals -->
    <!-- Firmware Update Modal -->
    <div class="modal fade" id="firmware-update-modal" tabindex="-1" role="dialog" aria-labelledby="firmware-update-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="firmware-update-modal-title">
                        <i data-feather="download" class="mr-1"></i>
                        Update Device Firmware
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Current Device Info -->
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading mb-1">Current Device Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Device Model:</small>
                                <div class="font-weight-bold" id="current-device-model">Loading...</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Current Firmware:</small>
                                <div class="font-weight-bold" id="current-firmware-version">Loading...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Firmware Selection -->
                    <div class="form-group">
                        <label for="firmware-selection">Select Firmware Version</label>
                        <select class="form-control" id="firmware-selection">
                            <option value="">Loading firmware versions...</option>
                        </select>
                        <small class="text-muted">Only compatible and enabled firmware versions are shown.</small>
                    </div>

                    <!-- Firmware Details -->
                    <div id="firmware-details" style="display: none;">
                        <h6 class="mb-2">Firmware Details</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td id="firmware-name">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Version:</strong></td>
                                        <td id="firmware-version">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Size:</strong></td>
                                        <td id="firmware-size">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Model:</strong></td>
                                        <td id="firmware-model">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td id="firmware-status">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>MD5 Checksum:</strong></td>
                                        <td id="firmware-md5" class="text-break" style="font-family: monospace; font-size: 0.85em;">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td id="firmware-description">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div id="firmware-warning" class="alert alert-warning mt-3" style="display: none;">
                        <h6 class="alert-heading">⚠️ Important Notice</h6>
                        <ul class="mb-0">
                            <li>The device will restart automatically after receiving the firmware update</li>
                            <li>There may be temporary connectivity loss during the update process</li>
                            <li>Do not power off the device during the firmware update</li>
                            <li>The update process typically takes 2-5 minutes to complete</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update-firmware-submit" disabled>
                        <i data-feather="download" class="mr-1"></i>
                        Update Firmware
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Channel Scan Modal with Results View -->
    <div class="modal fade" id="channel-scan-modal" tabindex="-1" role="dialog" aria-labelledby="channel-scan-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="channel-scan-modal-title">Channel Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <!-- Initial view before scan starts -->
                            <div id="pre-scan-view">
                                <div class="alert alert-info">
                                    <div class="alert-body">
                                        <i data-feather="info" class="mr-1 align-middle"></i>
                                        <span>Scanning will analyze nearby WiFi networks and interference to determine optimal channel settings.</span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <h6>Last Scan Results:</h6>
                                        <ul class="list-group mb-2">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>2.4 GHz Best Channel</span>
                                                <span class="badge badge-primary" id="last-best-channel-2g">Channel 6</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>5 GHz Best Channel</span>
                                                <span class="badge badge-primary" id="last-best-channel-5g">Channel 36</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>Scan Time</span>
                                                <span id="last-scan-time">2023-11-05 14:22</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="col-md-6 col-12">
                                        <h6>Nearby Networks:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>2.4 GHz</span>
                                                <span class="badge badge-light" id="nearby-networks-2g">8 networks</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>5 GHz</span>
                                                <span class="badge badge-light" id="nearby-networks-5g">4 networks</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center mt-2">
                                    <button class="btn btn-primary" id="start-scan-btn">
                                        <i data-feather="refresh-cw" class="mr-1"></i> Start New Channel Scan
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Scan in progress view -->
                            <div id="scan-in-progress-view" style="display: none;">
                                <div class="alert alert-warning">
                                    <div class="alert-body">
                                        <i data-feather="clock" class="mr-1 align-middle"></i>
                                        <span>Scanning for available WiFi channels and detecting interference. This may take a minute...</span>
                                    </div>
                                </div>
                                
                                <div class="progress progress-bar-primary mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>
                                
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator timeline-point-primary"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scanning 2.4 GHz Band</h6>
                                                <span class="text-muted">Step 1/3</span>
                                            </div>
                                            <p>Checking channels 1-11 for signal strength and interference</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Scanning 5 GHz Band</h6>
                                                <span class="text-muted">Step 2/3</span>
                                            </div>
                                            <p>Checking channels 36-165 for signal strength and interference</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point-indicator"></div>
                                        <div class="timeline-event">
                                            <div class="d-flex justify-content-between">
                                                <h6>Analyzing Results</h6>
                                                <span class="text-muted">Step 3/3</span>
                                            </div>
                                            <p>Determining optimal channels based on scan data</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Scan results view -->
                            <div id="scan-results-view" style="display: none;">
                                <div class="alert alert-success mb-2">
                                    <div class="alert-body">
                                        <i data-feather="check-circle" class="mr-1 align-middle"></i>
                                        <span>Scan complete! Optimal channels have been determined based on current RF environment.</span>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light-primary mb-0">
                                            <div class="card-body">
                                                <h5 class="card-title">2.4 GHz Results</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Recommended Channel:</span>
                                                    <h3 class="mb-0" id="result-channel-2g">6</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-12">
                                        <div class="card bg-light-primary mb-0">
                                            <div class="card-body">
                                                <h5 class="card-title">5 GHz Results</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Recommended Channel:</span>
                                                    <h3 class="mb-0" id="result-channel-5g">36</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Nearby Networks</h5>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Band</th>
                                                            <th>Channel</th>
                                                            <th>Networks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>2.4 GHz</td>
                                                            <td>1</td>
                                                            <td>3</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2.4 GHz</td>
                                                            <td>6</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2.4 GHz</td>
                                                            <td>11</td>
                                                            <td>2</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5 GHz</td>
                                                            <td>36</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5 GHz</td>
                                                            <td>44</td>
                                                            <td>2</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-primary" id="apply-scan-results">
                                            <i data-feather="check" class="mr-1"></i> Apply These Settings
                                        </button>
                                        <button class="btn btn-outline-primary" id="back-to-scan-btn">
                                            <i data-feather="refresh-cw" class="mr-1"></i> Run Another Scan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Portal Management Modal -->
    <div class="modal fade" id="portal-management-modal" tabindex="-1" role="dialog" aria-labelledby="portal-management-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portal-management-modal-title">Captive Portal Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills mb-2">
                        <li class="nav-item">
                            <a class="nav-link active" id="html-pill" data-toggle="pill" href="#html-editor" aria-expanded="true">HTML</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="css-pill" data-toggle="pill" href="#css-editor" aria-expanded="false">CSS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="js-pill" data-toggle="pill" href="#js-editor" aria-expanded="false">JavaScript</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="social-pill" data-toggle="pill" href="#social-settings" aria-expanded="false">Social Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="preview-pill" data-toggle="pill" href="#preview" aria-expanded="false">Preview</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="html-editor" role="tabpanel" aria-labelledby="html-pill">
                            <textarea class="form-control code-editor" rows="15" style="font-family: monospace;"><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><span class="location_name"></span> WiFi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="ogo">
        </div>
        <h1>Welcome to <span class="location_name"></span> WiFi</h1>
        <p>Please login to access the internet</p>
        
        <form class="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" required>
            </div>
            <div class="checkbox">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the terms and conditions</label>
            </div>
            <button type="submit">Connect</button>
        </form>
        
        <div class="social-login">
            <p>Or connect with</p>
            <div class="social-buttons">
                <button class="facebook">Facebook</button>
                <button class="google">Google</button>
            </div>
        </div>
    </div>
</body>
</html></textarea>
                        </div>
                                        <div class="tab-pane" id="js-editor" role="tabpanel" aria-labelledby="js-pill">
                                            <textarea class="form-control code-editor" rows="15" style="font-family: monospace;">// Optional JavaScript for enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
   // Form submission
   const loginForm = document.querySelector('.login-form');
   if (loginForm) {
       loginForm.addEventListener('submit', function(e) {
           e.preventDefault();
           // Add your form submission logic here
           console.log('Form submitted');
           // You can add AJAX request or other form handling code
       });
   }
});</textarea>
                                        </div>
                        <div class="tab-pane" id="preview" role="tabpanel" aria-labelledby="preview-pill">
                            <div style="border: 1px solid #ddd; border-radius: 4px; padding: 1rem; background-color: #f9f9f9; height: 400px; overflow: auto;">
                                <h5 class="text-center">Preview will be rendered here</h5>
                                <p class="text-center text-muted">This is a placeholder for the live preview of your portal page.</p>
                                <!-- Preview content will be rendered here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Captive Portal Network Settings Modal -->
    <div class="modal fade" id="captive-network-modal" tabindex="-1" role="dialog" aria-labelledby="captive-network-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captive-network-modal-title">Edit Captive Portal Network Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-2">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-50"></i>
                            <span>Captive Portal requires Static IP configuration.</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" class="form-control" placeholder="192.168.10.1" value="192.168.10.1">
                    </div>
                    <div class="form-group">
                        <label>Netmask</label>
                        <input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0">
                    </div>
                    <div class="form-group">
                        <label>Gateway</label>
                        <input type="text" class="form-control" placeholder="192.168.10.1" value="192.168.10.1">
                    </div>
                    <div class="form-group">
                        <label>Broadcast IP</label>
                        <input type="text" class="form-control" placeholder="192.168.10.255" value="192.168.10.255">
                    </div>
                    <div class="form-group">
                        <label>Primary DNS</label>
                        <input type="text" class="form-control" placeholder="8.8.8.8" value="8.8.8.8">
                    </div>
                    <div class="form-group">
                        <label>Secondary DNS</label>
                        <input type="text" class="form-control" placeholder="1.1.1.1" value="1.1.1.1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Password WiFi Network Settings Modal -->
    <div class="modal fade" id="password-network-modal" tabindex="-1" role="dialog" aria-labelledby="password-network-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="password-network-modal-title">Edit Password WiFi Network</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Assignment</label>
                        <select class="form-control" id="password-ip-assignment">
                            <option value="static" selected>Static IP</option>
                            <option value="dhcp">DHCP Client</option>
                        </select>
                        <small class="text-muted">When using DHCP Client, DHCP Server will be automatically disabled.</small>
                    </div>
                    
                    <div id="password-static-fields">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" value="192.168.1.1">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" value="192.168.1.1">
                        </div>
                        <div class="form-group">
                            <label>Broadcast IP</label>
                            <input type="text" class="form-control" placeholder="192.168.1.255" value="192.168.1.255">
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" placeholder="8.8.8.8" value="8.8.8.8">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" placeholder="1.1.1.1" value="1.1.1.1">
                        </div>

                        <div class="form-group mt-3 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password-dhcp-server-toggle" checked>
                                <label class="custom-control-label" for="password-dhcp-server-toggle">Enable DHCP Server</label>
                            </div>
                            <small class="text-muted">Provides automatic IP addressing for connected clients.</small>
                        </div>
                        
                        <div id="password-dhcp-server-fields">
                            <div class="form-group">
                                <label>DHCP Range Start</label>
                                <input type="text" class="form-control" placeholder="192.168.1.100" value="192.168.1.100">
                            </div>
                            <div class="form-group">
                                <label>DHCP Range End</label>
                                <input type="text" class="form-control" placeholder="192.168.1.200" value="192.168.1.200">
                            </div>
                            <div class="form-group">
                                <label>Lease Time (hours)</label>
                                <input type="number" class="form-control" placeholder="24" value="24">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-password-network">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- WAN Settings Modal -->
    <div class="modal fade" id="wan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="wan-settings-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wan-settings-modal-title">Edit WAN Interface Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Connection Type</label>
                        <select class="form-control" id="wan-connection-type">
                            <option value="dhcp" selected>DHCP</option>
                            <option value="static">Static IP</option>
                            <option value="pppoe">PPPoE</option>
                        </select>
                    </div>
                    
                    <div id="wan-static-fields" style="display: none;">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" placeholder="203.0.113.10" value="203.0.113.10">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" placeholder="203.0.113.1" value="203.0.113.1">
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" placeholder="8.8.8.8" value="8.8.8.8">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" placeholder="1.1.1.1" value="1.1.1.1">
                        </div>
                    </div>
                    
                    <div id="wan-pppoe-fields" style="display: none;">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label>Service Name (Optional)</label>
                            <input type="text" class="form-control" placeholder="Service Name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-wan-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- LAN Settings Modal -->
    <div class="modal fade" id="lan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="lan-settings-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lan-settings-modal-title">Edit LAN Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" class="form-control" placeholder="192.168.1.1" value="192.168.1.1">
                    </div>
                    <div class="form-group">
                        <label>Netmask</label>
                        <input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0">
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="dhcp-server-toggle" checked>
                            <label class="custom-control-label" for="dhcp-server-toggle">Enable DHCP Server</label>
                        </div>
                    </div>
                    
                    <div id="dhcp-server-fields">
                        <div class="form-group">
                            <label>DHCP Start Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.100" value="192.168.1.100">
                        </div>
                        <div class="form-group">
                            <label>DHCP End Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.200" value="192.168.1.200">
                        </div>
                        <div class="form-group">
                            <label>Lease Time (hours)</label>
                            <input type="number" class="form-control" placeholder="24" value="24">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Captive Portal Settings Modal -->
    <div class="modal fade" id="captive-portal-modal" tabindex="-1" role="dialog" aria-labelledby="captive-portal-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captive-portal-modal-title">Captive Portal IP Management</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Static IP Settings -->
                    <div class="form-group">
                        <label>IP Assignment</label>
                        <select class="form-control" id="captive-ip-assignment" disabled>
                            <option value="static" selected>Static IP</option>
                        </select>
                        <small class="text-muted">Only Static IP configuration is available for Captive Portal</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="captive-portal-ip">IP Address</label>
                        <input type="text" class="form-control" id="captive-portal-ip" placeholder="192.168.2.1">
                    </div>
                    
                    <div class="form-group">
                        <label for="captive-portal-netmask">Netmask</label>
                        <input type="text" class="form-control" id="captive-portal-netmask" placeholder="255.255.255.0">
                    </div>
                    
                    <div class="form-group">
                        <label for="captive-portal-gateway">Gateway</label>
                        <input type="text" class="form-control" id="captive-portal-gateway" placeholder="192.168.2.1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-captive-portal-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Modals -->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/charts/chart-apex.js"></script>
    <script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
    <script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>
    <!-- END: Page JS-->
    <script src="/assets/js/config.js"></script>

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
            
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Initialize network usage chart
            var networkUsageOptions = {
                chart: {
                    height: 250,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: true
                    }
                },
                colors: ['#7367F0', '#00CFE8'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                series: [{
                    name: 'Download',
                    data: [1.2, 2.5, 1.8, 3.0, 2.4, 1.9, 2.7, 3.5, 3.8, 2.9, 4.2, 5.8]
                }, {
                    name: 'Upload',
                    data: [0.5, 0.8, 0.6, 1.2, 0.9, 0.7, 1.3, 1.5, 1.7, 1.4, 2.3, 2.4]
                }],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 90, 100]
                    }
                },
                tooltip: {
                    x: { show: false }
                },
                xaxis: {
                    labels: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                }
            };
            var networkUsageChart = new ApexCharts(document.querySelector('#network-usage-chart'), networkUsageOptions);
            networkUsageChart.render();
            
            // Initialize location map
            var locationMap = L.map('location-map').setView([48.8566, 2.3522], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(locationMap);
            
            // Add marker for the location
            L.marker([48.8566, 2.3522]).addTo(locationMap)
                .bindPopup('<span class="location_name">Location Name</span><br><span class="location_address">Address</span>')
                .openPopup();
                
            // Country selection handling 
            $('#wifi-country').on('change', function() {
                const country = $(this).val();
                const countryName = $(this).find('option:selected').text().split('(')[0].trim();
                
                // Show notification about country change
                toastr.info(`WiFi settings updated to comply with ${countryName} regulations.`, 'Country Updated', {
                    closeButton: true,
                    tapToDismiss: false
                });
                
                // Some countries might have different available channels
                updateAvailableChannels(country);
            });
            
            // Function to update available channels based on country
            function updateAvailableChannels(country) {
                // This is a simplified example
                // In a real application, you would have a comprehensive database of channel regulations
                
                const channel2g = $('#channel-2g');
                const channel5g = $('#channel-5g');
                
                // Reset to default first
                channel2g.find('option:not(:first)').remove();
                channel5g.find('option:not(:first)').remove();
                
                // Add channels for 2.4GHz (most countries allow 1-11)
                for (let i = 1; i <= 11; i++) {
                    const freq = 2407 + (i * 5);
                    channel2g.append(`<option value="${i}">Channel ${i} (${freq} MHz)</option>`);
                }
                
                // Add channels 12-13 only for certain countries
                if (['FR', 'DE', 'IT', 'ES', 'GB', 'JP', 'CN', 'IN'].includes(country)) {
                    channel2g.append(`<option value="12">Channel 12 (2467 MHz)</option>`);
                    channel2g.append(`<option value="13">Channel 13 (2472 MHz)</option>`);
                }
                
                // Add channel 14 only for Japan
                if (country === 'JP') {
                    channel2g.append(`<option value="14">Channel 14 (2484 MHz)</option>`);
                }
                
                // 5GHz channels (simplified example)
                const baseChannels5g = [36, 40, 44, 48, 52, 56, 60, 64, 100, 104, 108, 112, 116, 132, 136, 140];
                
                baseChannels5g.forEach(ch => {
                    const freq = ch === 36 ? 5180 : (5180 + ((ch - 36) * 5));
                    channel5g.append(`<option value="${ch}">Channel ${ch} (${freq} MHz)</option>`);
                });
                
                // Add high channels for certain countries
                if (['US', 'CA', 'AU'].includes(country)) {
                    const highChannels = [149, 153, 157, 161, 165];
                    highChannels.forEach(ch => {
                        const freq = 5745 + ((ch - 149) * 5);
                        channel5g.append(`<option value="${ch}">Channel ${ch} (${freq} MHz)</option>`);
                    });
                }
            }
            
            // Initialize available channels for default country (US)
            updateAvailableChannels('US');
            
            // Handle channel scan button for 2.4 GHz
            $('#scan-channels-2g').on('click', function() {
                // Show a scanning notification
                toastr.info('Scanning for optimal 2.4 GHz channels...', 'Channel Scan', {
                    closeButton: false,
                    timeOut: 3000
                });
                
                // Simulate scanning delay
                setTimeout(function() {
                    // In a real application, this would be populated with actual scan results
                    const bestChannel = 6; // Example result
                    
                    $('#channel-2g').val(bestChannel);
                    
                    toastr.success(`Best channel for 2.4 GHz: Channel ${bestChannel}`, 'Scan Complete', {
                        closeButton: true,
                        timeOut: 5000
                    });
                }, 3000);
            });
            
            // Handle channel scan button for 5 GHz
            $('#scan-channels-5g').on('click', function() {
                // Show a scanning notification
                toastr.info('Scanning for optimal 5 GHz channels...', 'Channel Scan', {
                    closeButton: false,
                    timeOut: 3000
                });
                
                // Simulate scanning delay
                setTimeout(function() {
                    // In a real application, this would be populated with actual scan results
                    const bestChannel = 36; // Example result
                    
                    $('#channel-5g').val(bestChannel);
                    
                    toastr.success(`Best channel for 5 GHz: Channel ${bestChannel}`, 'Scan Complete', {
                        closeButton: true,
                        timeOut: 5000
                    });
                }, 3000);
            });
            
            // Power level dropdown handling
            $('#save-power-settings').on('click', function() {
                const power2g = $('#power-level-2g').val();
                const power5g = $('#power-level-5g').val();
                const channel2g = $('#channel-2g').val();
                const channel5g = $('#channel-5g').val();
                const width2g = $('#channel-width-2g').val();
                const width5g = $('#channel-width-5g').val();
                
                // Format channel information for display
                const channel2gText = channel2g === 'auto' ? 'Auto (Best Channel)' : `Channel ${channel2g}`;
                const channel5gText = channel5g === 'auto' ? 'Auto (Best Channel)' : `Channel ${channel5g}`;
                
                // Format width information for display
                const width2gText = width2g === '20' ? '20 MHz' : '20/40 MHz';
                let width5gText = '';
                switch(width5g) {
                    case '20': width5gText = '20 MHz'; break;
                    case '40': width5gText = '40 MHz'; break;
                    case '80': width5gText = '80 MHz'; break;
                    case '160': width5gText = '160 MHz'; break;
                    default: width5gText = '80 MHz';
                }
                
                // Get the text representation of the power levels (includes dBm and mW values)
                const power2gText = $('#power-level-2g option:selected').text();
                const power5gText = $('#power-level-5g option:selected').text();
                
                // Show success message
                toastr.success(`WiFi settings updated successfully:
                    <br>2.4 GHz: ${channel2gText}, Width: ${width2gText}, Power: ${power2gText}
                    <br>5 GHz: ${channel5gText}, Width: ${width5gText}, Power: ${power5gText}`, 
                    'Settings Updated', {
                        closeButton: true,
                        tapToDismiss: false,
                        timeOut: 5000
                    }
                );
                
                // Log transmit power change for network monitoring
                console.log(`WiFi settings changed - 2.4 GHz: CH ${channel2g}, ${width2g}MHz, ${power2g}dBm | 5 GHz: CH ${channel5g}, ${width5g}MHz, ${power5g}dBm`);
            });
            
            // Channel width change validation
            $('#channel-width-2g').on('change', function() {
                const selectedWidth = $(this).val();
                const selectedChannel = $('#channel-2g').val();
                
                // Show warning for 40 MHz on 2.4 GHz if not using recommended channels
                if (selectedWidth === '40' && !['auto', '1', '5', '9'].includes(selectedChannel)) {
                    toastr.warning('40 MHz on 2.4 GHz works best with channels 1, 5, or 9 as primary channel.', 'Channel Width Notice', {
                        closeButton: true,
                        timeOut: 5000
                    });
                }
            });
            
            // Channel width change validation for 5 GHz
            $('#channel-width-5g').on('change', function() {
                const selectedWidth = $(this).val();
                const selectedChannel = $('#channel-5g').val();
                
                // Show notice for very wide channels
                if (selectedWidth === '160') {
                    toastr.info('160 MHz channels provide maximum speed but may not be supported by all clients.', 'Channel Width Notice', {
                        closeButton: true,
                        timeOut: 5000
                    });
                }
            });
            
            // Fix the channel scanning functionality
            // Main button to open the scan modal
            $('#scan-channels-btn').on('click', function() {
                console.log("Opening channel scan modal"); // Debug log
                $('#channel-scan-modal').modal('show');
                
                // Show the pre-scan view
                $('#pre-scan-view').show();
                $('#scan-in-progress-view, #scan-results-view').hide();
                
                // Reset the progress bar and timeline
                $('#channel-scan-modal .progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                $('#channel-scan-modal .timeline-point-indicator:not(:first)').removeClass('timeline-point-primary');
                
                // Ensure feather icons are initialized
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            });
            
            // Button inside modal to actually start the scan
            $('#start-scan-btn').on('click', function() {
                console.log("Starting channel scan from modal button"); // Debug log
                
                // Switch views
                $('#pre-scan-view, #scan-results-view').hide();
                $('#scan-in-progress-view').show();
                
                // Ensure feather icons are refreshed in the new view
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
                
                // Start the scan animation
                simulateChannelScan();
            });
            
            // Button to run another scan from the results view
            $('#back-to-scan-btn').on('click', function() {
                // Switch back to the scan progress view
                $('#pre-scan-view, #scan-results-view').hide();
                $('#scan-in-progress-view').show();
                
                // Reset the progress bar and timeline
                $('#channel-scan-modal .progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                $('#channel-scan-modal .timeline-point-indicator:not(:first)').removeClass('timeline-point-primary');
                
                // Start another scan
                simulateChannelScan();
            });
            
            // Button to apply the scan results
            $('#apply-scan-results').on('click', function() {
                // Get only the recommended channel values (not width)
                const channel2g = $('#result-channel-2g').text();
                const channel5g = $('#result-channel-5g').text();
                
                // Update the channel selectors with recommended values
                $('#channel-2g').val(channel2g);
                $('#channel-5g').val(channel5g);
                
                // Close the modal
                $('#channel-scan-modal').modal('hide');
                
                // Show a success message
                toastr.success('Channel settings have been applied. Remember to save your changes.', 'Settings Applied', {
                    closeButton: true,
                    timeOut: 5000
                });
            });
            
            function simulateChannelScan() {
                let progress = 0;
                const progressBar = $('#scan-in-progress-view .progress-bar');
                const timelinePoints = $('#scan-in-progress-view .timeline-point-indicator');
                
                console.log("Channel scan animation started"); // Debug log
                
                const interval = setInterval(function() {
                    progress += 5;
                    progressBar.css('width', progress + '%');
                    progressBar.attr('aria-valuenow', progress);
                    
                    // Update timeline indicators
                    if (progress >= 33) {
                        $(timelinePoints[1]).addClass('timeline-point-primary');
                    }
                    if (progress >= 66) {
                        $(timelinePoints[2]).addClass('timeline-point-primary');
                    }
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        
                        // Simulate some randomness in results for demo purposes
                        const channels2g = [1, 6, 11];
                        const channels5g = [36, 40, 44, 48, 149, 153];
                        
                        // Select random values (in a real app this would be based on actual scan)
                        const randChannel2g = channels2g[Math.floor(Math.random() * channels2g.length)];
                        const randChannel5g = channels5g[Math.floor(Math.random() * channels5g.length)];
                        
                        // Wait a moment then show results
                        setTimeout(function() {
                            // Update the results view with scan data - only channels now
                            $('#result-channel-2g').text(randChannel2g);
                            $('#result-channel-5g').text(randChannel5g);
                            
                            // Update the last scan time for the next view
                            const now = new Date();
                            const formattedDate = now.getFullYear() + '-' + 
                                                 String(now.getMonth() + 1).padStart(2, '0') + '-' +
                                                 String(now.getDate()).padStart(2, '0') + ' ' +
                                                 String(now.getHours()).padStart(2, '0') + ':' +
                                                 String(now.getMinutes()).padStart(2, '0');
                            
                            $('#last-scan-time').text(formattedDate);
                            $('#last-best-channel-2g').text('Channel ' + randChannel2g);
                            $('#last-best-channel-5g').text('Channel ' + randChannel5g);
                            
                            // Switch to the results view
                            $('#pre-scan-view, #scan-in-progress-view').hide();
                            $('#scan-results-view').show();
                            
                            // Ensure feather icons are refreshed in the new view
                            if (feather) {
                                feather.replace({
                                    width: 14,
                                    height: 14
                                });
                            }
                        }, 1000);
                    }
                }, 250);
            }
            
            // WAN Settings Connection Type Toggle
            $('#wan-connection-type').on('change', function() {
                const selectedType = $(this).val();
                
                // Hide all field groups first
                $('#wan-static-fields, #wan-pppoe-fields').hide();
                
                // Show the relevant fields based on selection
                if (selectedType === 'static') {
                    $('#wan-static-fields').show();
                } else if (selectedType === 'pppoe') {
                    $('#wan-pppoe-fields').show();
                }
            });
            
            // DHCP Server Toggle for LAN Settings
            $('#dhcp-server-toggle').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#dhcp-server-fields').show();
                } else {
                    $('#dhcp-server-fields').hide();
                }
            });
            
            // Password WiFi Static/DHCP Toggle (in card)
            $('#static-ip-radio, #dhcp-client-radio').on('change', function() {
                if ($('#static-ip-radio').is(':checked')) {
                    $('#static-ip-config').show();
                    $('#dhcp-client-config').hide();
                } else {
                    $('#static-ip-config').hide();
                    $('#dhcp-client-config').show();
                }
            });
            
            // Password WiFi IP Assignment Toggle (in modal)
            $('#password-ip-assignment').on('change', function() {
                if ($(this).val() === 'static') {
                    $('#password-static-fields').show();
                } else {
                    $('#password-static-fields').hide();
                }
            });
            
            // Initialize match height on load
            $('.match-height').each(function() {
                const rowCards = $(this).find('.card');
                let maxHeight = 0;
                
                // Find max height
                rowCards.each(function() {
                    const cardHeight = $(this).outerHeight();
                    if (cardHeight > maxHeight) {
                        maxHeight = cardHeight;
                    }
                });
                
                // Set all cards to max height
                rowCards.css('height', maxHeight + 'px');
            });
            
            // Add hover effects for cards
            $('.card').hover(
                function() { $(this).addClass('shadow-lg'); },
                function() { $(this).removeClass('shadow-lg'); }
            );
            
            // Toggle password visibility
            $('#toggle-password').on('click', function() {
                const passwordField = $('#wifi-password');
                const fieldType = passwordField.attr('type');
                
                if (fieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).find('i').replaceWith(feather.icons['eye-off'].toSvg({ class: '' }));
                } else {
                    passwordField.attr('type', 'password');
                    $(this).find('i').replaceWith(feather.icons['eye'].toSvg({ class: '' }));
                }
                
                // Re-initialize feather icons
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            });
            
            // Add MAC address functionality
            $('#captive-add-mac, #secured-add-mac').on('click', function() {
                const isCaptive = $(this).attr('id') === 'captive-add-mac';
                const macField = isCaptive ? $('#captive-mac-address') : $('#secured-mac-address');
                const macValue = macField.val().trim();
                
                // Simple MAC address validation
                const macRegex = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
                
                if (macRegex.test(macValue)) {
                    const macList = $(this).closest('.card-body').find('.filtered-mac-list');
                    const newItem = `
                        <div class="filtered-mac-item">
                            <span>${macValue}</span>
                            <button class="btn btn-sm btn-icon btn-outline-danger remove-mac">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                    `;
                    
                    macList.append(newItem);
                    macField.val('');
                    
                    // Re-initialize feather icons
                    if (feather) {
                        feather.replace({
                            width: 14,
                            height: 14
                        });
                    }
                } else {
                    // Show validation error
                    macField.addClass('is-invalid');
                    setTimeout(() => {
                        macField.removeClass('is-invalid');
                    }, 2000);
                }
            });
            
            // Remove MAC address item
            $(document).on('click', '.remove-mac', function() {
                $(this).closest('.filtered-mac-item').remove();
            });
            
            // Add domain functionality
            $('#captive-add-domain, #secured-add-domain').on('click', function() {
                const isCaptive = $(this).attr('id') === 'captive-add-domain';
                const domainField = isCaptive ? $('#captive-domain') : $('#secured-domain');
                const domainValue = domainField.val().trim();
                
                if (domainValue) {
                    const domainList = $(this).closest('.card-body').find('.filtered-domain-list');
                    const newItem = `
                        <div class="filtered-mac-item">
                            <span>${domainValue}</span>
                            <button class="btn btn-sm btn-icon btn-outline-danger remove-domain">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                    `;
                    
                    domainList.append(newItem);
                    domainField.val('');
                    
                    // Re-initialize feather icons
                    if (feather) {
                        feather.replace({
                            width: 14,
                            height: 14
                        });
                    }
                } else {
                    // Show validation error
                    domainField.addClass('is-invalid');
                    setTimeout(() => {
                        domainField.removeClass('is-invalid');
                    }, 2000);
                }
            });
            
            // Remove domain item
            $(document).on('click', '.remove-domain', function() {
                $(this).closest('.filtered-mac-item').remove();
            });
            
            // Ensure proper tab functionality
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // Refresh elements inside the newly shown tab
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
                
                // Adjust card heights for better alignment
                $('.match-height').each(function() {
                    const rowCards = $(this).find('.card');
                    let maxHeight = 0;
                    
                    // Reset heights
                    rowCards.css('height', 'auto');
                    
                    // Find max height
                    rowCards.each(function() {
                        const cardHeight = $(this).outerHeight();
                        if (cardHeight > maxHeight) {
                            maxHeight = cardHeight;
                        }
                    });
                    
                    // Set all cards to max height
                    rowCards.css('height', maxHeight + 'px');
                });
            });
            
            // Fix for match height in network interface cards
            function matchInterfaceCardHeights() {
                // Get all interface cards
                const interfaceCards = $('.card-header:contains("WAN Interface"), .card-header:contains("Captive Portal"), .card-header:contains("Password WiFi")').closest('.card.shadow-none');
                
                // Reset heights first
                interfaceCards.css('height', 'auto');
                
                // Find max height
                let maxHeight = 0;
                interfaceCards.each(function() {
                    const cardHeight = $(this).outerHeight();
                    if (cardHeight > maxHeight) {
                        maxHeight = cardHeight;
                    }
                });
                
                // Set all cards to max height
                interfaceCards.css('height', maxHeight + 'px');
            }
            
            // Apply match height on initial load
            matchInterfaceCardHeights();
        });

        // Handle authentication method selection for captive portal
        $('#captive-auth-method').on('change', function() {
            const selectedMethod = $(this).val();
            
            // Hide all auth options sections first
            $('.auth-options-section').hide();
            
            // Show the relevant section based on selection
            switch(selectedMethod) {
                case 'password':
                    $('#password-auth-options').show();
                    break;
                case 'sms':
                    $('#sms-auth-options').show();
                    break;
                case 'email':
                    $('#email-auth-options').show();
                    break;
                case 'social':
                    $('#social-auth-options').show();
                    break;
                // Click-through doesn't need additional options
            }
            
            // Re-initialize feather icons
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });

        // Toggle portal password visibility
        $('#toggle-portal-password').on('click', function() {
            const passwordField = $('#portal-shared-password');
            const fieldType = passwordField.attr('type');
            
            if (fieldType === 'password') {
                passwordField.attr('type', 'text');
                $(this).find('i').replaceWith(feather.icons['eye-off'].toSvg({ class: '' }));
            } else {
                passwordField.attr('type', 'password');
                $(this).find('i').replaceWith(feather.icons['eye'].toSvg({ class: '' }));
            }
            
            // Re-initialize feather icons
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });

        // Handle social login toggle switches
        $('#social-facebook, #social-google, #social-twitter, #social-apple').on('change', function() {
            const socialId = $(this).attr('id');
            const configId = socialId.replace('social-', '') + '-config';
            
            if ($(this).is(':checked')) {
                $('#' + configId).slideDown();
            } else {
                $('#' + configId).slideUp();
            }
        });

        // Additional handlers for compact layouts
        $(document).ready(function() {
            // DHCP/Static IP Toggle for Password WiFi
            $('#static-ip-radio, #dhcp-client-radio').on('change', function() {
                if ($('#static-ip-radio').is(':checked')) {
                    $('#static-ip-field, #static-netmask-field, #advanced-network-btn').show();
                    $('#dhcp-client-message').hide();
                } else {
                    $('#static-ip-field, #static-netmask-field, #advanced-network-btn').hide();
                    $('#dhcp-client-message').show();
                }
            });
            
            // Convert legacy radio buttons to use the new dropdown selectors
            // For Captive Portal MAC filtering
            $('input[name="portal-mac-filter"]').on('change', function() {
                let value = 'allow-all';
                if ($('#portal-mac-allow-list').is(':checked')) value = 'allow-listed';
                if ($('#portal-mac-block-list').is(':checked')) value = 'block-listed';
                $('#portal-mac-filter').val(value);
            });
            
            $('#portal-mac-filter').on('change', function() {
                let value = $(this).val();
                if (value === 'allow-all') $('#portal-mac-allow-all').prop('checked', true);
                if (value === 'allow-listed') $('#portal-mac-allow-list').prop('checked', true);
                if (value === 'block-listed') $('#portal-mac-block-list').prop('checked', true);
            });
            
            // For Secured WiFi MAC filtering
            $('input[name="secured-mac-filter"]').on('change', function() {
                let value = 'allow-all';
                if ($('#secured-mac-allow-list').is(':checked')) value = 'allow-listed';
                if ($('#secured-mac-block-list').is(':checked')) value = 'block-listed';
                $('#secured-mac-filter').val(value);
            });
            
            $('#secured-mac-filter').on('change', function() {
                let value = $(this).val();
                if (value === 'allow-all') $('#secured-mac-allow-all').prop('checked', true);
                if (value === 'allow-listed') $('#secured-mac-allow-list').prop('checked', true);
                if (value === 'block-listed') $('#secured-mac-block-list').prop('checked', true);
            });
        });
    </script>

    <!-- Location Settings AJAX Functionality -->
    <script>
        $(document).ready(function() {
            // Get location ID from URL
            function getLocationId() {
                // Example from URL: /location-details/123
                const pathParts = window.location.pathname.split('/');
                console.log("pathParts", pathParts);
                const locationId = pathParts[pathParts.length - 1];
                console.log("locationId", locationId);
                
                // If location ID is not in URL, try to get from a data attribute on the page
                // Or default to 1 for testing purposes
                return locationId;
            }
            
            // Load location data including settings
            function loadLocationData() {
                const locationId = getLocationId();
                // alert("locationId", locationId);
                
                // Show loading indicators
                $('#router-tab, #captive-portal-tab, #secured-wifi-tab').append('<div class="spinner-border spinner-border-sm text-primary ml-1" role="status"><span class="sr-only">Loading...</span></div>');

                // Make AJAX request to get location data including settings
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        console.log("response", response);
                        // Remove loading indicators
                        $('.spinner-border').remove();
                        
                        if (response && response.location) {
                            const location = response.location;
                            const settings = location.settings || {};

                            // Update page title and breadcrumb with location name
                            updateLocationName(location.name);

                            // Populate basic location info
                            populateLocationInfo(location);

                            // Populate settings forms
                            if (settings) {
                                populateRouterSettings(settings);
                                populateCaptivePortalSettings(settings);
                                populatePasswordWifiSettings(settings);
                            }

                            console.log('Location data loaded successfully');
                        } else {
                            showNotification('warning', 'Could not load location data. Using default values.');
                        }
                    },
                    error: function(xhr) {
                        // Remove loading indicators
                        $('.spinner-border').remove();

                        console.error('Error loading location data:', xhr);

                        // Show error notification
                        let errorMessage = 'Failed to load location data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showNotification('error', errorMessage);
                    }
                });
            }
            
            // Update location name in title and breadcrumb
            function updateLocationName(locationName) {
                // alert("locationName:: " + locationName);
                if (locationName) {
                    // Update page title
                    document.title = `${locationName} - monsieur-wifi Controller`;
                    $('.location_name').text(locationName);
                    
                    // Update headings
                    $('.content-header-title').text(`${locationName} Details`);
                    
                    // Update breadcrumb
                    $('.breadcrumb-item.active').text(locationName);
                }
            }
            
            // Populate basic location info
            function populateLocationInfo(location) {
                // Location info card
                $('.card-body h4.font-weight-bolder').first().text(location.name || 'Location Name');
                
                // If we have an address from the location object
                if (location.address) {
                    $('.card-body p.card-text.text-muted').first().text(location.address);
                }

                // set location name, address, city, state, country, manager, contact email, contact phone, status, description
                $('#location-name').val(location.name);
                $('#location-address').val(location.address);
                $('#location-city').val(location.city);
                $('#location-state').val(location.state);
                $('#location-country').val(location.country);
                $('#location-manager').val(location.manager_name);
                $('#location-contact-email').val(location.contact_email);
                $('#location-contact-phone').val(location.contact_phone);
                $('#location-status').val(location.status);
                $('#location-description').val(location.description);
                
                
                // If we have a device connected to this location
                if (location.device) {
                    const device = location.device;
                    
                    // Update device status
                    const statusClass = device.is_online ? 'status-online' : 'status-offline';
                    const statusText = device.is_online ? 'Online' : 'Offline';
                    $('.status-badge').removeClass('status-online status-offline status-warning').addClass(statusClass).text(statusText);
                    
                    // Update device info
                    $('.card-body .d-flex:contains("Router Model:")').find('.font-weight-bold').text(device.model || 'Unknown');
                    $('.card-body .d-flex:contains("Firmware:")').find('.font-weight-bold').text(device.firmware_version || 'Unknown');
                    
                    // If we have uptime info
                    if (device.last_seen) {
                        const lastSeen = new Date(device.last_seen);
                        const now = new Date();
                        const diffMs = now - lastSeen;
                        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                        const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        
                        $('.card-body .d-flex:contains("Uptime:")').find('.font-weight-bold').text(`${diffDays} days, ${diffHours} hours`);
                    }
                }
            }

            // Populate Router Settings
            function populateRouterSettings(settings) {
                // WiFi Radio Settings
                $('#wifi-country').val(settings.wifi_country || 'US');
                $('#power-level-2g').val(settings.power_level_2g || '15');
                $('#power-level-5g').val(settings.power_level_5g || '17');
                
                // Channel Settings
                $('#channel-width-2g').val(settings.channel_width_2g || '40');
                $('#channel-width-5g').val(settings.channel_width_5g || '80');
                $('#channel-2g').val(settings.channel_2g || '6');
                $('#channel-5g').val(settings.channel_5g || '36');
            }

            // Populate Captive Portal Settings
            function populateCaptivePortalSettings(settings) {
                // $('#portal-ssid').val(settings.captive_portal_ssid || 'Guest WiFi');
                // $('#portal-visibility').val(settings.captive_portal_visible ? 'visible' : 'hidden');
                $('#portal-ssid').val(settings.captive_portal_ssid || 'Guest WiFi');
                $('#portal-visibility').val(settings.captive_portal_visible ? 'visible' : 'hidden');

                // Authentication settings
                const authMethod = settings.captive_portal_enabled ? 
                                  (settings.social_login_enabled ? 'social' : 
                                   (settings.email_login_enabled ? 'email' : 
                                    (settings.verification_required ? 'sms' : 'click-through'))) : 
                                  'click-through';
                
                $('#captive-auth-method').val(authMethod).trigger('change');

                // Session settings
                $('#captive-session-timeout').val(settings.session_timeout || 60);
                $('#captive-idle-timeout').val(settings.idle_timeout || 15);
                
                // Load captive portal designs from API
                loadCaptivePortalDesigns(settings.captive_portal_design);
                
                // MAC Filtering
                if (settings.access_control_enabled) {
                    // Set the filter mode
                    const macFilterMode = settings.allowed_domains ? 'allow-listed' : 'block-listed';
                    $('#portal-mac-filter').val(macFilterMode);
                    
                    // Populate domains if available
                    if (settings.allowed_domains) {
                        try {
                            const allowedDomains = JSON.parse(settings.allowed_domains);
                            $('.filtered-domain-list').empty();
                            
                            allowedDomains.forEach(domain => {
                                $('.filtered-domain-list').append(`
                                    <div class="filtered-mac-item">
                                        <span>${domain}</span>
                                        <button class="btn btn-sm btn-icon btn-outline-danger remove-domain">
                                            <i data-feather="x"></i>
                                        </button>
                                    </div>
                                `);
                            });
                            
                            // Re-initialize feather icons
                            if (feather) {
                                feather.replace({
                                    width: 14,
                                    height: 14
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing allowed domains:', e);
                        }
                    }
                    
                    // Set web filtering toggle
                    $('#captive-web-filter').prop('checked', settings.access_control_enabled);
                }
                
                // Bandwidth limits
                $('#captive-bandwidth-limit').prop('checked', settings.bandwidth_limit_up > 0 || settings.bandwidth_limit_down > 0);
                $('#captive-download-limit').val(settings.bandwidth_limit_down || 10);
                $('#captive-upload-limit').val(settings.bandwidth_limit_up || 2);
            }
            
            // Function to fetch and populate captive portal designs
            function loadCaptivePortalDesigns(selectedDesignId) {
                $.ajax({
                    url: '/api/captive-portal-designs/',
                    method: 'POST', // Using POST as specified in routes/api.php
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({}), // Empty object as POST body
                    success: function(response) {
                        console.log("captive-portal-designs", response);
                        if (response.success && response.data) {
                            const designs = response.data;
                            const $select = $('#captive-portal-config');
                            
                            // Clear previous options
                            $select.empty();
                            
                            // Add designs as options
                            designs.forEach(function(design) {
                                $select.append(`<option value="${design.id}">${design.name}</option>`);
                            });
                            
                            // Select the saved design if provided
                            if (selectedDesignId) {
                                $select.val(selectedDesignId);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching captive portal designs:', xhr);
                    }
                });
            }
            
            // Populate Password WiFi Settings
            function populatePasswordWifiSettings(settings) {
                $('#wifi-ssid').val(settings.wifi_name || 'Home WiFi');
                $('#wifi-password').val(settings.wifi_password || '');
                $('#wifi-security').val(settings.encryption_type || 'wpa2-psk');
                
                // Web filtering for secured network
                $('#secured-web-filter').prop('checked', settings.access_control_enabled);
                $('#guest-secured-web-filter').prop('checked', settings.access_control_enabled);
            }
            
            // Save Router Settings
            $('#save-power-settings, #save-channel-settings, #save-channels-btn').on('click', function() {
                const locationId = getLocationId();
                
                const settings = {
                    // WiFi Radio Settings
                    wifi_country: $('#wifi-country').val(),
                    power_level_2g: $('#power-level-2g').val(),
                    power_level_5g: $('#power-level-5g').val(),
                    
                    // Channel Settings
                    channel_width_2g: $('#channel-width-2g').val(),
                    channel_width_5g: $('#channel-width-5g').val(),
                    channel_2g: $('#channel-2g').val(),
                    channel_5g: $('#channel-5g').val()
                };
                
                saveSettings(locationId, settings, 'router');
            });
            
            // Save Captive Portal Settings
            $('#save-captive-portal').on('click', function() {
                const locationId = getLocationId();
                
                // Collect all domain items
                const allowedDomains = [];
                $('.filtered-domain-list .filtered-mac-item span').each(function() {
                    allowedDomains.push($(this).text());
                });
                
                // Get captive portal design ID
                const captivePortalDesignId = $('#captive-portal-config').val();
                console.log('Selected captive portal design ID:', captivePortalDesignId);
                
                const settings = {
                    // Basic Settings
                    captive_portal_ssid: $('#portal-ssid').val(),
                    captive_portal_visible: $('#portal-visibility').val() === 'visible',
                    
                    // Captive Portal Settings
                    captive_portal_enabled: true,
                    
                    // Authentication Type
                    social_login_enabled: $('#captive-auth-method').val() === 'social',
                    email_login_enabled: $('#captive-auth-method').val() === 'email',
                    verification_required: $('#captive-auth-method').val() === 'sms',
                    
                    // Session settings
                    session_timeout: parseInt($('#captive-session-timeout').val()) || 60,
                    idle_timeout: parseInt($('#captive-idle-timeout').val()) || 15,
                    
                    // Access Control
                    access_control_enabled: $('#captive-web-filter').is(':checked'),
                    allowed_domains: JSON.stringify(allowedDomains),
                    
                    // Bandwidth
                    bandwidth_limit_up: $('#captive-bandwidth-limit').is(':checked') ? parseInt($('#captive-upload-limit').val()) || 2 : 0,
                    bandwidth_limit_down: $('#captive-bandwidth-limit').is(':checked') ? parseInt($('#captive-download-limit').val()) || 10 : 0,
                    
                    // Captive Portal Design
                    captive_portal_design_id: captivePortalDesignId
                };
                
                console.log('Saving captive portal settings:', settings);
                
                saveSettings(locationId, settings, 'captive');
            });
            
            // Save Password WiFi Settings
            $('#save-secured-wifi').on('click', function() {
                const locationId = getLocationId();
                
                // Collect all domain items for secured wifi
                const blockedDomains = [];
                $('#secured-wifi .filtered-domain-list .filtered-mac-item span').each(function() {
                    blockedDomains.push($(this).text());
                });
                
                const settings = {
                    // Basic WiFi Settings
                    wifi_name: $('#wifi-ssid').val(),
                    wifi_password: $('#wifi-password').val(),
                    encryption_type: $('#wifi-security').val(),
                    
                    // Web filtering for secured network
                    access_control_enabled: $('#secured-web-filter').is(':checked'),
                    blocked_domains: JSON.stringify(blockedDomains)
                };
                
                saveSettings(locationId, settings, 'wifi');
            });
            
            // Function to save settings
            function saveSettings(locationId, settings, settingsType) {
                // Show spinner on the save button
                const $button = settingsType === 'router' ? $('#save-power-settings, #save-channel-settings, #save-channels-btn') : 
                               (settingsType === 'captive' ? $('#save-captive-portal') : $('#save-secured-wifi'));

                // Store original HTML and classes before modifying
                const originalHtml = $button.html();
                const originalClasses = $button.attr('class');
                
                // Show loading state
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                $button.prop('disabled', true);

                // Log the data being sent to the server
                console.log('Saving settings data:', {
                    settings: settings,
                    settings_type: settingsType
                });

                // Make AJAX request to save settings
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({
                        settings: settings,
                        settings_type: settingsType
                    }),
                    success: function(response) {
                        // Restore button to original state
                        $button.html(originalHtml);
                        $button.attr('class', originalClasses); // This restores all original classes
                        $button.prop('disabled', false);

                        // Log the response from the server
                        console.log('Settings saved successfully, response:', response);

                        showNotification('success', 'Settings saved successfully');

                        // Reload the location data to refresh any dependent settings
                        setTimeout(loadLocationData, 1000);
                    },
                    error: function(xhr) {
                        // Restore button to original state
                        $button.html(originalHtml);
                        $button.attr('class', originalClasses); // This restores all original classes
                        $button.prop('disabled', false);
                        
                        console.error('Error saving settings:', xhr);
                        
                        // Show error notification
                        let errorMessage = 'Failed to save settings';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                    }
                });
            }
            
            // Function to display notifications
            function showNotification(type, message) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message, '', {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        timeOut: 5000
                    });
                } else {
                    alert(`${message}`)
                    console.log(`${message}`);
                }
            }
            
            // Load location data when page loads
            loadLocationData();
            
            // Channel scanning functionality
            $('#scan-channels-btn').on('click', function() {
                const locationId = getLocationId();
                
                // Show the scanning modal
                $('#channel-scan-modal').modal('show');
                
                // Hide results view, show progress view
                $('#pre-scan-view').hide();
                $('#scan-results-view').hide();
                $('#scan-in-progress-view').show();
                
                // Start progress bar animation
                let progress = 0;
                const progressBar = $('#scan-in-progress-view .progress-bar');
                
                // Simulate scanning progress
                const progressInterval = setInterval(function() {
                    progress += 5;
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                    
                    // Update timeline indicators based on progress
                    if (progress >= 30) {
                        $('#scan-in-progress-view .timeline-item:nth-child(1) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    if (progress >= 60) {
                        $('#scan-in-progress-view .timeline-item:nth-child(2) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    if (progress >= 90) {
                        $('#scan-in-progress-view .timeline-item:nth-child(3) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        
                        // Make AJAX request to get scan results
                        $.ajax({
                            url: `/api/locations/${locationId}/channel-scan`,
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + localStorage.getItem('token')
                            },
                            success: function(response) {
                                // Show scan results
                                $('#scan-in-progress-view').hide();
                                $('#scan-results-view').show();
                                
                                if (response && response.data) {
                                    const results = response.data;
                                    
                                    // Update result displays
                                    $('#result-channel-2g').text(results.recommended_channel_2g || '6');
                                    $('#result-channel-5g').text(results.recommended_channel_5g || '36');
                                    
                                    // Update nearby networks table if available
                                    if (results.nearby_networks) {
                                        // Clear existing rows
                                        $('#scan-results-view table tbody').empty();
                                        
                                        results.nearby_networks.forEach(network => {
                                            $('#scan-results-view table tbody').append(`
                                                <tr>
                                                    <td>${network.band}</td>
                                                    <td>${network.channel}</td>
                                                    <td>${network.count}</td>
                                                </tr>
                                            `);
                                        });
                                    }
                                } else {
                                    // Show default results if no data available
                                    $('#result-channel-2g').text('6');
                                    $('#result-channel-5g').text('36');
                                }
                            },
                            error: function(xhr) {
                                // Hide scanning progress, show default results
                                $('#scan-in-progress-view').hide();
                                $('#scan-results-view').show();
                                
                                console.error('Error getting scan results:', xhr);
                                showNotification('error', 'Failed to get channel scan results');
                            }
                        });
                    }
                }, 200);
            });
            
            // Apply scan results
            $('#apply-scan-results').on('click', function() {
                const channel2g = $('#result-channel-2g').text();
                const channel5g = $('#result-channel-5g').text();
                
                // Update the channel selectors
                $('#channel-2g').val(channel2g);
                $('#channel-5g').val(channel5g);
                
                // Close the modal
                $('#channel-scan-modal').modal('hide');
                
                // Show success notification
                showNotification('success', 'Channel settings applied');
                
                // Save the settings
                $('#save-channels-btn').trigger('click');
            });
            
            // Back to scan button
            $('#back-to-scan-btn').on('click', function() {
                $('#scan-results-view').hide();
                $('#pre-scan-view').show();
            });
        });

        // Load location information into the form
function loadLocationInfo(locationData) {
    $('#location-name').val(locationData.name || '');
    $('#location-address').val(locationData.address || '');
    $('#location-city').val(locationData.city || '');
    $('#location-state').val(locationData.state || '');
    $('#location-postal-code').val(locationData.postal_code || '');
    $('#location-country').val(locationData.country || '');
    $('#location-manager').val(locationData.manager_name || '');
    $('#location-contact-email').val(locationData.contact_email || '');
    $('#location-contact-phone').val(locationData.contact_phone || '');
    $('#location-description').val(locationData.description || '');
    $('#location-status').val(locationData.status || 'active');
}

// Modify your existing loadLocationData function to call this
// Add this line where you process the location data in your success callback
// function loadLocationData() {
//     // Your existing code...
    
//     $.ajax({
//         // Your existing AJAX call...
//         url: `/api/locations/${locationId}`,
//         method: 'GET',
//         headers: {
//             'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function(response) {
//             // Add this line to load location information
//             loadLocationInfo(response.location);
//         }
//     });
// }

// Handle saving location information
$(document).on('click', '#save-location-info', function() {
    function showNotification(type, message) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message, '', {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        timeOut: 5000
                    });
                } else {
                    alert(`${message}`)
                    console.log(`${message}`);
                }
        }
    const pathParts = window.location.pathname.split('/');
    console.log("pathParts", pathParts);
    const locationId = pathParts[pathParts.length - 1];
    console.log("locationId", locationId);
    // const locationId = getLocationId(); // This should be defined elsewhere in your code
    
    const locationData = {
        name: $('#location-name').val(),
        address: $('#location-address').val(),
        city: $('#location-city').val(),
        state: $('#location-state').val(),
        postal_code: $('#location-postal-code').val(),
        country: $('#location-country').val(),
        manager_name: $('#location-manager').val(),
        contact_email: $('#location-contact-email').val(),
        contact_phone: $('#location-contact-phone').val(),
        description: $('#location-description').val(),
        status: $('#location-status').val()
    };
    
    // Show spinner on the button
    const $button = $('#save-location-info');
    const originalHtml = $button.html();
    const originalClasses = $button.attr('class');
    
    $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
    $button.prop('disabled', true);
    
    // Make AJAX request to save location info
    $.ajax({
        url: `/api/locations/${locationId}/general`,
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify(locationData),
        success: function(response) {
            // Restore button to original state
            $button.html(originalHtml);
            $button.attr('class', originalClasses);
            $button.prop('disabled', false);
            
            showNotification('success', 'Location information saved successfully');
            
            // Reload the location data to refresh
            setTimeout(loadLocationData, 1000);
        },
        error: function(xhr) {
            // Restore button to original state
            $button.html(originalHtml);
            $button.attr('class', originalClasses);
            $button.prop('disabled', false);
            
            console.error('Error saving location information:', xhr);
            
            // Show error notification
            let errorMessage = 'Failed to save location information';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showNotification('error', errorMessage);
        }
    });
});
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select filtering categories",
                allowClear: true
            });
            
            // Helper function to extract location ID from URL
            function getLocationId() {
                const pathSegments = window.location.pathname.split('/');
                // Assuming URL pattern like /locations/{id}/details or /locations/{id}
                for (let i = 0; i < pathSegments.length; i++) {
                    if (pathSegments[i] === 'locations' && i + 1 < pathSegments.length) {
                        return pathSegments[i + 1];
                    }
                }
                return null;
            }
        });
    </script>
    
    <!-- WAN Settings Save Script -->
    <script>
        // Helper function to extract location ID from URL
        function getLocationId() {
            const pathSegments = window.location.pathname.split('/');
            // Assuming URL pattern like /locations/{id}/details or /locations/{id}
            for (let i = 0; i < pathSegments.length; i++) {
                if (pathSegments[i] === 'locations' && i + 1 < pathSegments.length) {
                    return pathSegments[i + 1];
                }
            }
            return null;
        }
        
        $(document).ready(function() {
            // Handle save WAN settings
            $('#save-wan-settings').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const wanData = {
                    settings_type: 'wan',
                    wan_type: $('#wan-connection-type').val(),
                    wan_ip: $('#wan-static-fields input:eq(0)').val(),
                    wan_subnet: $('#wan-static-fields input:eq(1)').val(),
                    wan_gateway: $('#wan-static-fields input:eq(2)').val(),
                    wan_dns1: $('#wan-static-fields input:eq(3)').val(),
                    wan_dns2: $('#wan-static-fields input:eq(4)').val(),
                    pppoe_username: $('#wan-pppoe-fields input:eq(0)').val(),
                    pppoe_password: $('#wan-pppoe-fields input:eq(1)').val(),
                    pppoe_service: $('#wan-pppoe-fields input:eq(2)').val()
                };
                
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: wanData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'WAN settings updated successfully.');
                            $('#wan-settings-modal').modal('hide');
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update WAN settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update WAN settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
            
            // Handle save Captive Portal settings
            $('#save-captive-portal-settings').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const captivePortalData = {
                    settings_type: 'captive',
                    captive_portal_ip: $('#captive-portal-ip').val(),
                    captive_portal_netmask: $('#captive-portal-netmask').val(),
                    captive_portal_gateway: $('#captive-portal-gateway').val()
                };
                console.log("captivePortalData", captivePortalData);
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: captivePortalData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Captive portal IP settings updated successfully.');
                            $('#captive-portal-modal').modal('hide');
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update captive portal settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update captive portal settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
            
            // Handle save Password Network settings
            $('#save-password-network').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const passwordNetworkData = {
                    settings_type: 'wifi',
                    password_network_enabled: 1,
                    password_network_ssid: $('#wifi-ssid').val(),
                    password_network_visible: 1,
                    password_network_password: $('#wifi-password').val(),
                    password_network_encryption: $('#wifi-security').val(),
                    password_network_ip: $('#password-static-fields input:eq(0)').val(),
                    password_network_netmask: $('#password-static-fields input:eq(1)').val(),
                    password_network_gateway: $('#password-static-fields input:eq(2)').val(),
                    password_network_dhcp_enabled: $('#password-dhcp-server-toggle').is(':checked') ? 1 : 0,
                    password_network_dhcp_start: $('#password-dhcp-server-fields input:eq(0)').val(),
                    password_network_dhcp_end: $('#password-dhcp-server-fields input:eq(1)').val(),
                    password_network_dhcp_lease: $('#password-dhcp-server-fields input:eq(2)').val()
                };
                
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: passwordNetworkData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Password network settings updated successfully.');
                            $('#password-network-modal').modal('hide');
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update password network settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update password network settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Helper function to extract location ID from URL
            function getLocationId() {
                const pathSegments = window.location.pathname.split('/');
                // Assuming URL pattern like /locations/{id}/details or /locations/{id}
                for (let i = 0; i < pathSegments.length; i++) {
                    if (pathSegments[i] === 'locations' && i + 1 < pathSegments.length) {
                        return pathSegments[i + 1];
                    }
                }
                return null;
            }
            
            // Load location data and populate interfaces
            function loadLocationData() {
                const locationId = getLocationId();
                if (!locationId) {
                    console.error('Location ID not found in URL');
                    return;
                }
                
                // Show loading spinners
                $('#wan-interface, #captive-portal-interface, #password-wifi-interface').append(
                    '<div class="text-center my-2 loading-spinner"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                );
                
                // Fetch location data
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        // Remove loading spinners
                        $('.loading-spinner').remove();
                        
                        if (response.success && response.data) {
                            const location = response.data;
                            
                            // Update location general info
                            updateLocationGeneralInfo(location);
                            
                            // Update WAN interface
                            if (location.wan_settings) {
                                $('#wan-type-display').text(location.wan_settings.wan_type || 'DHCP');
                                $('#wan-ip-display').text(location.wan_settings.wan_ip || '192.168.1.1');
                                $('#wan-subnet-display').text(location.wan_settings.wan_subnet || '255.255.255.0');
                                $('#wan-gateway-display').text(location.wan_settings.wan_gateway || '192.168.1.1');
                                $('#wan-dns1-display').text(location.wan_settings.wan_dns1 || '8.8.8.8');
                                $('#wan-dns2-display').text(location.wan_settings.wan_dns2 || '8.8.4.4');
                            }
                            console.log("location :::: ", location);
                            // Update Captive Portal interface
                            if (location.settings) {
                                $('#captive-ip-display').text(location.settings.captive_portal_ip || '192.168.2.1');
                                $('#captive-netmask-display').text(location.settings.captive_portal_netmask || '255.255.255.0');
                                $('#captive-gateway-display').text(location.settings.captive_portal_gateway || '192.168.2.1');
                                
                                // Store these values to populate the modal when opened
                                window.captivePortalSettings = {
                                    ip: location.settings.captive_portal_ip || '192.168.2.1',
                                    netmask: location.settings.captive_portal_netmask || '255.255.255.0',
                                    gateway: location.settings.captive_portal_gateway || '192.168.2.1'
                                };
                            }
                            
                            // Update Password WiFi interface
                            if (location.password_network_settings) {
                                const passwordType = location.password_network_settings.password_network_ip_type || 'Static IP';
                                $('#password-ip-type-display').text(passwordType);
                                $('#password-ip-display').text(location.password_network_settings.password_network_ip || '192.168.1.1');
                                $('#password-netmask-display').text(location.password_network_settings.password_network_netmask || '255.255.255.0');
                                $('#password-gateway-display').text(location.password_network_settings.password_network_gateway || '192.168.1.1');
                                
                                const dhcpStatus = location.password_network_settings.password_network_dhcp_enabled ? 'Enabled' : 'Disabled';
                                $('#password-dhcp-status-display').text(dhcpStatus);
                            }
                        } else {
                            console.error('Failed to load location data', response);
                            showNotification('error', 'Failed to load location data');
                        }
                    },
                    error: function(xhr) {
                        // Remove loading spinners
                        $('.loading-spinner').remove();
                        
                        console.error('Error loading location data:', xhr);
                        showNotification('error', 'Error loading location data');
                    }
                });
            }
            
            // Populate the Captive Portal modal fields when it's opened
            $('#captive-portal-modal').on('show.bs.modal', function() {
                if (window.captivePortalSettings) {
                    $('#captive-portal-ip').val(window.captivePortalSettings.ip);
                    $('#captive-portal-netmask').val(window.captivePortalSettings.netmask);
                    $('#captive-portal-gateway').val(window.captivePortalSettings.gateway);
                }
            });
            
            // Helper function to update location general info
            function updateLocationGeneralInfo(location) {
                // Update location name and address
                $('.location_name').text(location.name || 'Unknown Location');
                $('.location_address').text(location.address || 'No address provided');
                
                // Update other location details if needed
            }
            
            // Handle save WAN settings
            $('#save-wan-settings').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const wanData = {
                    settings_type: 'wan',
                    wan_type: $('#wan-connection-type').val(),
                    wan_ip: $('#wan-static-fields input:eq(0)').val(),
                    wan_subnet: $('#wan-static-fields input:eq(1)').val(),
                    wan_gateway: $('#wan-static-fields input:eq(2)').val(),
                    wan_dns1: $('#wan-static-fields input:eq(3)').val(),
                    wan_dns2: $('#wan-static-fields input:eq(4)').val(),
                    pppoe_username: $('#wan-pppoe-fields input:eq(0)').val(),
                    pppoe_password: $('#wan-pppoe-fields input:eq(1)').val(),
                    pppoe_service: $('#wan-pppoe-fields input:eq(2)').val()
                };
                
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: wanData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'WAN settings updated successfully.');
                            $('#wan-settings-modal').modal('hide');
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update WAN settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update WAN settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
            
            // Handle save Captive Portal settings
            $('#save-captive-portal-settings').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const captivePortalData = {
                    settings_type: 'captive',
                    captive_portal_ip: $('#captive-portal-ip').val(),
                    captive_portal_netmask: $('#captive-portal-netmask').val(),
                    captive_portal_gateway: $('#captive-portal-gateway').val()
                };
                
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: captivePortalData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Captive portal IP settings updated successfully.');
                            $('#captive-portal-modal').modal('hide');
                            
                            // Store the updated values
                            window.captivePortalSettings = {
                                ip: captivePortalData.captive_portal_ip,
                                netmask: captivePortalData.captive_portal_netmask,
                                gateway: captivePortalData.captive_portal_gateway
                            };
                            
                            // Update the displayed values
                            $('#captive-ip-display').text(captivePortalData.captive_portal_ip);
                            $('#captive-netmask-display').text(captivePortalData.captive_portal_netmask);
                            $('#captive-gateway-display').text(captivePortalData.captive_portal_gateway);
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update captive portal settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update captive portal settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
            
            // Handle save Password Network settings
            $('#save-password-network').on('click', function() {
                const $button = $(this);
                const originalText = $button.text();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
                
                const locationId = getLocationId();
                const passwordNetworkData = {
                    settings_type: 'wifi',
                    password_network_enabled: 1,
                    password_network_ssid: $('#wifi-ssid').val(),
                    password_network_visible: 1,
                    password_network_password: $('#wifi-password').val(),
                    password_network_encryption: $('#wifi-security').val(),
                    password_network_ip: $('#password-static-fields input:eq(0)').val(),
                    password_network_netmask: $('#password-static-fields input:eq(1)').val(),
                    password_network_gateway: $('#password-static-fields input:eq(2)').val(),
                    password_network_dhcp_enabled: $('#password-dhcp-server-toggle').is(':checked') ? 1 : 0,
                    password_network_dhcp_start: $('#password-dhcp-server-fields input:eq(0)').val(),
                    password_network_dhcp_end: $('#password-dhcp-server-fields input:eq(1)').val(),
                    password_network_dhcp_lease: $('#password-dhcp-server-fields input:eq(2)').val()
                };
                
                $.ajax({
                    url: `/api/locations/${locationId}`,
                    type: 'PUT',
                    data: passwordNetworkData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Password network settings updated successfully.');
                            $('#password-network-modal').modal('hide');
                            
                            setTimeout(function() {
                                location.reload(); // Reload to show updated settings
                            }, 1500);
                        } else {
                            showNotification('error', response.message || 'Failed to update password network settings.');
                            $button.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update password network settings.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Load location data when page loads
            loadLocationData();
            
            // Firmware Update Modal functionality
            let currentDeviceModel = null;
            let availableFirmwares = [];
            
            // Debug: Check if button exists when page loads
            console.log('Firmware button exists:', $('#update-firmware-btn').length > 0);
            console.log('Firmware modal exists:', $('#firmware-update-modal').length > 0);
            console.log('jQuery version:', $.fn.jquery);
            console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
            
            // Add a global test function for debugging
            window.testFirmwareModal = function() {
                console.log('Testing firmware modal...');
                console.log('Modal element found:', $('#firmware-update-modal').length);
                
                // Check modal CSS properties
                const modal = $('#firmware-update-modal');
                console.log('Modal display:', modal.css('display'));
                console.log('Modal z-index:', modal.css('z-index'));
                console.log('Modal visibility:', modal.css('visibility'));
                console.log('Modal opacity:', modal.css('opacity'));
                
                $('#firmware-update-modal').modal('show');
                
                // Check after showing
                setTimeout(() => {
                    console.log('After show - Modal display:', modal.css('display'));
                    console.log('After show - Modal classes:', modal.attr('class'));
                    console.log('After show - Modal backdrop exists:', $('.modal-backdrop').length);
                }, 100);
            };
            
            // Show firmware update modal - use document delegation to handle dynamically loaded content
            $(document).on('click', '#update-firmware-btn', function(e) {
                e.preventDefault();
                console.log('Firmware button clicked!');
                console.log('Modal element:', $('#firmware-update-modal'));
                console.log('Modal length:', $('#firmware-update-modal').length);
                
                // Try to show modal and catch any errors
                try {
                    $('#firmware-update-modal').modal('show');
                    console.log('Modal show command executed');
                } catch (error) {
                    console.error('Error showing modal:', error);
                    
                    // Try alternative methods
                    console.log('Trying alternative modal show methods...');
                    
                    // Method 1: Direct Bootstrap call
                    try {
                        $('#firmware-update-modal').modal({show: true});
                        console.log('Alternative method 1 executed');
                    } catch (e) {
                        console.error('Alternative method 1 failed:', e);
                    }
                    
                    // Method 2: Change display directly
                    try {
                        $('#firmware-update-modal').show().addClass('show');
                        $('.modal-backdrop').remove();
                        $('body').append('<div class="modal-backdrop fade show"></div>');
                        console.log('Alternative method 2 executed');
                    } catch (e) {
                        console.error('Alternative method 2 failed:', e);
                    }
                }
                
                // Also try loading firmware options
                try {
                    loadFirmwareOptions();
                    console.log('loadFirmwareOptions called');
                } catch (error) {
                    console.error('Error loading firmware options:', error);
                }
            });
            
            // Load available firmware options based on device model
            function loadFirmwareOptions() {
                const locationId = getLocationId();
                
                // Reset modal state
                $('#firmware-selection').html('<option value="">Loading firmware versions...</option>');
                $('#firmware-details').hide();
                $('#firmware-warning').hide();
                $('#update-firmware-submit').prop('disabled', true);
                
                // Get current device info from the page
                const deviceModel = $('.router_model').text().trim() || $('.d-flex:contains("Router Model:")').find('.font-weight-bold').text().trim() || 'Unknown';
                const currentFirmware = $('.router_firmware').text().trim() || $('.d-flex:contains("Firmware:")').find('.font-weight-bold').text().trim() || 'Unknown';
                
                // Set current device info
                $('#current-device-model').text(deviceModel);
                $('#current-firmware-version').text(currentFirmware);
                
                // Determine model ID for API call
                let modelFilter = '';
                if (deviceModel.includes('820AX') || deviceModel === '820AX') {
                    modelFilter = '1';
                    currentDeviceModel = '820AX';
                } else if (deviceModel.includes('835AX') || deviceModel === '835AX') {
                    modelFilter = '2';
                    currentDeviceModel = '835AX';
                }
                
                // Fetch available firmware versions
                $.ajax({
                    url: '/api/firmware',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.data) {
                            availableFirmwares = response.data;
                            
                            // Filter firmware by device model and enabled status
                            const compatibleFirmwares = availableFirmwares.filter(firmware => {
                                return firmware.is_enabled && 
                                       (firmware.model == modelFilter || 
                                        firmware.model === currentDeviceModel);
                            });
                            
                            // Populate firmware dropdown
                            $('#firmware-selection').empty();
                            
                            if (compatibleFirmwares.length === 0) {
                                $('#firmware-selection').append('<option value="">No compatible firmware versions available</option>');
                            } else {
                                $('#firmware-selection').append('<option value="">Select a firmware version...</option>');
                                
                                compatibleFirmwares.forEach(firmware => {
                                    const isCurrentVersion = firmware.name === currentFirmware || 
                                                           firmware.version === currentFirmware;
                                    const optionText = `${firmware.name}${firmware.version ? ' (' + firmware.version + ')' : ''}${isCurrentVersion ? ' (Current)' : ''}`;
                                    
                                    $('#firmware-selection').append(
                                        `<option value="${firmware.id}" ${isCurrentVersion ? 'disabled' : ''}>${optionText}</option>`
                                    );
                                });
                            }
                        } else {
                            $('#firmware-selection').html('<option value="">Failed to load firmware versions</option>');
                            showNotification('error', 'Failed to load available firmware versions');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading firmware versions:', xhr);
                        $('#firmware-selection').html('<option value="">Error loading firmware versions</option>');
                        showNotification('error', 'Error loading firmware versions');
                    }
                });
            }
            
            // Handle firmware selection change
            $('#firmware-selection').on('change', function() {
                const selectedFirmwareId = $(this).val();
                
                if (selectedFirmwareId) {
                    const selectedFirmware = availableFirmwares.find(f => f.id == selectedFirmwareId);
                    
                    if (selectedFirmware) {
                        // Show firmware details
                        $('#firmware-name').text(selectedFirmware.name || 'N/A');
                        $('#firmware-version').text(selectedFirmware.version || 'N/A');
                        $('#firmware-size').text(formatFileSize(selectedFirmware.file_size || 0));
                        $('#firmware-model').text(getModelName(selectedFirmware.model) || 'N/A');
                        $('#firmware-status').text(selectedFirmware.is_enabled ? 'Enabled' : 'Disabled');
                        $('#firmware-md5').text(selectedFirmware.md5sum || 'N/A').attr('title', selectedFirmware.md5sum || 'N/A');
                        $('#firmware-description').text(selectedFirmware.description || 'No description available');
                        
                        $('#firmware-details').show();
                        $('#firmware-warning').show();
                        $('#update-firmware-submit').prop('disabled', false);
                    }
                } else {
                    $('#firmware-details').hide();
                    $('#firmware-warning').hide();
                    $('#update-firmware-submit').prop('disabled', true);
                }
            });
            
            // Handle firmware update submission
            $('#update-firmware-submit').on('click', function() {
                console.log('Firmware update submitted');
                const selectedFirmwareId = $('#firmware-selection').val();
                const locationId = getLocationId();
                
                if (!selectedFirmwareId || !locationId) {
                    showNotification('error', 'Please select a firmware version');
                    return;
                }
                
                const selectedFirmware = availableFirmwares.find(f => f.id == selectedFirmwareId);
                
                if (!selectedFirmware) {
                    showNotification('error', 'Selected firmware not found');
                    return;
                }
                
                // Confirm firmware update
                if (!confirm(`Are you sure you want to update the device firmware to "${selectedFirmware.name}"?\n\nThis will restart the device and may cause temporary connectivity loss.`)) {
                    return;
                }
                
                // Show loading state
                const $button = $('#update-firmware-submit');
                const originalHtml = $button.html();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').prop('disabled', true);
                
                // Update device firmware
                $.ajax({
                    url: `/api/locations/${locationId}/update-firmware`,
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('mrwifi_auth_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({
                        firmware_id: selectedFirmwareId,
                        firmware_version: selectedFirmware.name
                    }),
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Firmware update initiated successfully');
                            $('#firmware-update-modal').modal('hide');
                            
                            // Update the displayed firmware version on the page
                            $('.d-flex:contains("Firmware:")').find('.font-weight-bold').text(selectedFirmware.name);
                            
                            // Update the displayed firmware version
                            $('.router_firmware').text(selectedFirmware.name);
                            
                            // Reload the page after a short delay to show updated info
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showNotification('error', response.message || 'Failed to update firmware');
                            $button.html(originalHtml).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error updating firmware:', xhr);
                        
                        let errorMessage = 'Failed to update device firmware';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showNotification('error', errorMessage);
                        $button.html(originalHtml).prop('disabled', false);
                    }
                });
            });
            
            // Helper function to format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            // Helper function to get model name from model ID
            function getModelName(modelId) {
                const modelMap = {
                    '1': '820AX',
                    '2': '835AX',
                    1: '820AX',
                    2: '835AX',
                    '820AX': '820AX',
                    '835AX': '835AX'
                };
                return modelMap[modelId] || modelId;
            }

            // Channel scanning functionality
            $('#scan-channels-btn').on('click', function() {
                const locationId = getLocationId();
                
                // Show the scanning modal
                $('#channel-scan-modal').modal('show');
                
                // Hide results view, show progress view
                $('#pre-scan-view').hide();
                $('#scan-results-view').hide();
                $('#scan-in-progress-view').show();
                
                // Start progress bar animation
                let progress = 0;
                const progressBar = $('#scan-in-progress-view .progress-bar');
                
                // Simulate scanning progress
                const progressInterval = setInterval(function() {
                    progress += 5;
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                    
                    // Update timeline indicators based on progress
                    if (progress >= 30) {
                        $('#scan-in-progress-view .timeline-item:nth-child(1) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    if (progress >= 60) {
                        $('#scan-in-progress-view .timeline-item:nth-child(2) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    if (progress >= 90) {
                        $('#scan-in-progress-view .timeline-item:nth-child(3) .timeline-point-indicator').addClass('timeline-point-primary');
                    }
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        
                        // Make AJAX request to get scan results
                        $.ajax({
                            url: `/api/locations/${locationId}/channel-scan`,
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + localStorage.getItem('token')
                            },
                            success: function(response) {
                                // Show scan results
                                $('#scan-in-progress-view').hide();
                                $('#scan-results-view').show();
                                
                                if (response && response.data) {
                                    const results = response.data;
                                    
                                    // Update result displays
                                    $('#result-channel-2g').text(results.recommended_channel_2g || '6');
                                    $('#result-channel-5g').text(results.recommended_channel_5g || '36');
                                    
                                    // Update nearby networks table if available
                                    if (results.nearby_networks) {
                                        // Clear existing rows
                                        $('#scan-results-view table tbody').empty();
                                        
                                        results.nearby_networks.forEach(network => {
                                            $('#scan-results-view table tbody').append(`
                                                <tr>
                                                    <td>${network.band}</td>
                                                    <td>${network.channel}</td>
                                                    <td>${network.count}</td>
                                                </tr>
                                            `);
                                        });
                                    }
                                } else {
                                    // Show default results if no data available
                                    $('#result-channel-2g').text('6');
                                    $('#result-channel-5g').text('36');
                                }
                            },
                            error: function(xhr) {
                                // Hide scanning progress, show default results
                                $('#scan-in-progress-view').hide();
                                $('#scan-results-view').show();
                                
                                console.error('Error getting scan results:', xhr);
                                showNotification('error', 'Failed to get channel scan results');
                            }
                        });
                    }
                }, 200);
            });
        });
    </script>
    </body>
<!-- END: Body-->
</html>