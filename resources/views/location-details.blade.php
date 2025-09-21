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
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">

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

        .pppoe_display {
            display: none;
        }

        .static_ip_display {
            display: none;
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
                                <span class="font-weight-bold"><span class="router_model_updated"></span></span>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Reboot Count:</span>
                                        <span class="font-weight-bold"><span class="reboot_count">0</span></span>
                                    </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary btn-sm" id="device-restart-btn">
                                    <i data-feather="refresh-cw" class="mr-50"></i>
                                    <span>Restart</span>
                                </button>
                                <button class="btn btn-outline-primary btn-sm" id="update-firmware-btn">
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
                                                    <h4 class="font-weight-bolder mb-0" id="download-usage">0 GB</h4>
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
                                                    <h4 class="font-weight-bolder mb-0" id="upload-usage">0 GB</h4>
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
                                                    <h4 class="font-weight-bolder mb-0" id="current-users">0</h4>
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
                                                    <h4 class="font-weight-bolder mb-0" id="avg-session-time">0 hrs</h4>
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
                                                        <div class="ip-details" id="wan-interface-details">
                                                            <h6 class="mb-1">IP Configuration</h6>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Connection Type:</span>
                                                                <span class="font-weight-bold" id="wan-type-display"></span>
                                                            </div>
                                                            <div class="wan-static-ip-display_div hidden">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">IP Address:</span>
                                                                    <span class="font-weight-bold" id="wan-ip-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Subnet Mask:</span>
                                                                    <span class="font-weight-bold" id="wan-subnet-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Gateway:</span>
                                                                    <span class="font-weight-bold" id="wan-gateway-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Primary DNS:</span>
                                                                    <span class="font-weight-bold" id="wan-dns1-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Secondary DNS:</span>
                                                                    <span class="font-weight-bold" id="wan-dns2-display">-</span>
                                                                </div>
                                                            </div>
                                                            <div class="wan-pppoe-display_div hidden">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Username:</span>
                                                                    <span class="font-weight-bold" id="wan-pppoe-username">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Password:</span>
                                                                    <span class="font-weight-bold" id="wan-pppoe-password">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Service Name:</span>
                                                                    <span class="font-weight-bold" id="wan-pppoe-service-name">-</span>
                                                                </div>
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
                                                        <div class="ip-details" id="captive-portal-details">
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
                                                        <div class="ip-details" id="password-wifi-details">
                                                            <h6 class="mb-1">IP Configuration</h6>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted">Connection Type:</span>
                                                                <span class="font-weight-bold" id="password-wifi-ip-type-display"></span>
                                                            </div>
                                                            <div class="hidden password-ip-assignment-display_div">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">IP Address:</span>
                                                                    <span class="font-weight-bold" id="password-ip-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Subnet Mask:</span>
                                                                    <span class="font-weight-bold" id="password-netmask-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">Gateway:</span>
                                                                    <span class="font-weight-bold" id="password-gateway-display">-</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="text-muted">DHCP Server:</span>
                                                                    <span class="font-weight-bold" id="password-dhcp-status-display">-</span>
                                                                </div>
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
                                                    <button class="btn btn-primary save-captive-portal">
                                                        <i data-feather="save" class="mr-1"></i> Save Settings
                                                    </button>
                                                </div>
                                    
                                                <div class="card-body">
                                                    <!-- Basic Settings Section -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="portal-ssid">Network Name (SSID)</label>
                                                                <input type="text" class="form-control" id="captive-portal-ssid" placeholder="Guest WiFi">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="portal-visibility">Network Visibility</label>
                                                                <select class="form-control" id="captive-portal-visible">
                                                                    <option value="1" selected>Visible (Broadcast SSID)</option>
                                                                    <option value="0">Hidden (Don't Broadcast SSID)</option>
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

                                                            <div class="form-group hidden" id="password-auth-options">
                                                                <label for="captive_portal_password">Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" class="form-control form-control-sm" id="captive_portal_password" value="">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-captive-password">
                                                                            <i data-feather="eye"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group hidden" id="social-auth-options">
                                                                <label for="captive-social-auth-method">Social Media Logins</label>
                                                                <select class="form-control" id="captive-social-auth-method">
                                                                    <option value="facebook">Facebook</option>
                                                                    <option value="google">Google</option>
                                                                </select>
                                                            </div>
                                                    
                                                            <!-- Session Settings -->
                                                            <div class="row">
                                                                <div class="col-6">
                                                                <div class="form-group">
                                                                        <label for="captive-session-timeout">Session (mins)</label>
                                                                        <select class="form-control" id="captive-session-timeout">
                                                                            <option value="60">1 Hr</option>
                                                                            <option value="120">2 Hrs</option>
                                                                            <option value="180">3 Hrs</option>
                                                                            <option value="240">4 Hrs</option>
                                                                            <option value="300">5 Hrs</option>
                                                                            <option value="360">6 Hrs</option>
                                                                            <option value="720">12 Hrs</option>
                                                                            <option value="1440">1 Day</option>
                                                                            <option value="2880">2 Days</option>
                                                                            <option value="4320">3 Days</option>
                                                                            <option value="5760">4 Days</option>
                                                                            <option value="7200">5 Days</option>
                                                                            <option value="8640">6 Days</option>
                                                                            <option value="10080">1 Week</option>
                                                                            <option value="11520">2 Weeks</option>
                                                                            <option value="12960">3 Weeks</option>
                                                                            <option value="14400">1 Month</option>
                                                                            <option value="28800">2 Months</option>
                                                                            <option value="43200">3 Months</option>
                                                                            <option value="57600">4 Months</option>
                                                                            <option value="72000">5 Months</option>
                                                                            <option value="86400">6 Months</option>
                                                                            <option value="100800">7 Months</option>
                                                                            <option value="115200">8 Months</option>
                                                                            <option value="129600">9 Months</option>
                                                                            <option value="144000">10 Months</option>
                                                                            <option value="158400">11 Months</option>
                                                                            <option value="172800">1 Year</option>
                                                                            <option value="345600">2 Years</option>
                                                                            <option value="604800">3 Years</option>
                                                                            <option value="1209600">4 Years</option>
                                                                        </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="captive-idle-timeout">Idle (mins)</label>
                                                                <select class="form-control" id="captive-idle-timeout">
                                                                    <option value="15">15 Mins</option>
                                                                    <option value="30">30 Mins</option>
                                                                    <option value="45">45 Mins</option>
                                                                    <option value="60">1 Hr</option>
                                                                    <option value="120">2 Hrs</option>
                                                                    <option value="180">3 Hrs</option>
                                                                    <option value="240">4 Hrs</option>
                                                                    <option value="360">6 Hrs</option>
                                                                    <option value="720">12 Hrs</option>
                                                                    <option value="1440">1 Day</option>
                                                                </select>
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
                                                                <select class="form-control form-control-sm" id="captive-download-limit">
                                                                    <option value="">Select Download Limit</option>
                                                                    <option value="1">1 Mbps</option>
                                                                    <option value="2">2 Mbps</option>
                                                                    <option value="5">5 Mbps</option>
                                                                    <option value="10">10 Mbps</option>
                                                                    <option value="15">15 Mbps</option>
                                                                    <option value="20">20 Mbps</option>
                                                                    <option value="25">25 Mbps</option>
                                                                    <option value="30">30 Mbps</option>
                                                                    <option value="35">35 Mbps</option>
                                                                    <option value="40">40 Mbps</option>
                                                                    <option value="45">45 Mbps</option>
                                                                    <option value="50">50 Mbps</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="captive-upload-limit">Upload (Mbps)</label>
                                                                <select class="form-control form-control-sm" id="captive-upload-limit">
                                                                    <option value="0">Select Upload Limit</option>
                                                                    <option value="1">1 Mbps</option>
                                                                    <option value="2">2 Mbps</option>
                                                                    <option value="5">5 Mbps</option>
                                                                    <option value="10">10 Mbps</option>
                                                                    <option value="15">15 Mbps</option>
                                                                    <option value="20">20 Mbps</option>
                                                                    <option value="25">25 Mbps</option>
                                                                    <option value="30">30 Mbps</option>
                                                                    <option value="35">35 Mbps</option>
                                                                    <option value="40">40 Mbps</option>
                                                                    <option value="45">45 Mbps</option>
                                                                    <option value="50">50 Mbps</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="mb-2">Portal Configuration</label>
                                                        <select class="form-control form-control-sm" id="captive-portal-design">
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
                                            <button class="btn btn-primary save-password-network" id="save-secured-wifi">
                                                <i data-feather="save" class="mr-1"></i> Save Settings
                                            </button>
                                            </div>
                                        
                                                <div class="card-body">
                                            <!-- Basic Settings Section -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="wifi-ssid">Network Name (SSID)</label>
                                                        <input type="text" class="form-control" id="password-wifi-ssid" placeholder="Home WiFi">
                                                </div>
                                            </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="password-wifi-password">WiFi Password</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="password-wifi-password" placeholder="Password">
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
                                                        <select class="form-control" id="password-wifi-security">
                                                            <option value="wpa2-psk" selected>WPA2-PSK (Recommended)</option>
                                                            <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixed</option>
                                                            <option value="wpa3-psk">WPA3-PSK (Most Secure)</option>
                                                            <option value="wep">WEP (Legacy, Not Recommended)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="password_wifi_cipher_suites">Cipher Suites</label>
                                                        <select class="form-control" id="password_wifi_cipher_suites">
                                                            <option value="CCMP" selected>CCMP</option>
                                                            <option value="TKIP">TKIP</option>
                                                            <option value="TKIP+CCMP">TKIP+CCMP</option>
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
                            <label for="router-model-select">Router Model</label>
                            <select class="form-control" id="router-model-select">
                                <option value="">Select Router Model</option>
                                <option value="820AX">820AX</option>
                                <option value="835AX">835AX</option>
                            </select>
                            <small class="text-muted">Choose the router model installed at this location.</small>
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
                            <option value="STATIC" selected>Static IP</option>
                            <option value="DHCP">DHCP Client</option>
                        </select>
                        <small class="text-muted">When using DHCP Client, DHCP Server will be automatically disabled.</small>
                    </div>
                    
                    <div id="password-static-fields" class="hidden">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" id="password-ip" value="">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" placeholder="255.255.255.0" id="password-netmask" value="">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" placeholder="192.168.1.1" id="password-gateway" value="">
                        </div>
                        <div class="form-group">
                            <label>Broadcast IP</label>
                            <input type="text" class="form-control" placeholder="192.168.1.255" id="password-broadcast" value="">
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" placeholder="8.8.8.8" id="password-primary-dns" value="">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" placeholder="1.1.1.1" id="password-secondary-dns" value="">
                        </div>

                        <div class="form-group mt-3 pt-2 border-top">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password-dhcp-server-toggle" checked>
                                <label class="custom-control-label" for="password-dhcp-server-toggle">Enable DHCP Server</label>
                            </div>
                            <small class="text-muted">Provides automatic IP addressing for connected clients.</small>
                        </div>
                        
                        <div id="password-dhcp-server-fields" class="hidden">
                            <div class="form-group">
                                <label>DHCP Range Start</label>
                                <input type="text" class="form-control" placeholder="192.168.1.100" id="password-dhcp-start" value="192.168.1.100">
                            </div>
                            <div class="form-group">
                                <label>DHCP Range End</label>
                                <input type="text" class="form-control" placeholder="192.168.1.200" id="password-dhcp-end" value="192.168.1.200">
                            </div>
                            <div class="form-group">
                                <label>Lease Time (hours)</label>
                                <input type="number" class="form-control" placeholder="24" id="password-lease-time" value="24">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary save-password-network">Save Changes</button>
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
                            <option value="DHCP">DHCP</option>
                            <option value="STATIC">Static IP</option>
                            <option value="PPPOE">PPPoE</option>
                        </select>
                    </div>
                    
                    <div id="wan-static-fields" class="hidden">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" class="form-control" id="wan-ip-address" placeholder="203.0.113.10" value="203.0.113.10">
                        </div>
                        <div class="form-group">
                            <label>Netmask</label>
                            <input type="text" class="form-control" id="wan-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                        </div>
                        <div class="form-group">
                            <label>Gateway</label>
                            <input type="text" class="form-control" id="wan-gateway" placeholder="203.0.113.1" value="203.0.113.1">
                        </div>
                        <div class="form-group">
                            <label>Primary DNS</label>
                            <input type="text" class="form-control" id="wan-primary-dns" placeholder="8.8.8.8" value="8.8.8.8">
                        </div>
                        <div class="form-group">
                            <label>Secondary DNS</label>
                            <input type="text" class="form-control" id="wan-secondary-dns" placeholder="1.1.1.1" value="1.1.1.1">
                        </div>
                    </div>
                    
                    <div id="wan-pppoe-fields" style="display: none;">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" id="wan-pppoe-username" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="wan-pppoe-password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label>Service Name (Optional)</label>
                            <input type="text" class="form-control" id="wan-pppoe-service-name" placeholder="Service Name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary save-wan-settings">Save Changes</button>
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
                    <button type="button" class="btn btn-primary save-lan-settings">Save Changes</button>
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
                    <button type="button" class="btn btn-primary save-captive-portal" id="save-captive-portal-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Device Restart Confirmation Modal -->
    <div class="modal fade" id="restart-confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="restart-confirmation-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restart-confirmation-modal-title">
                        <i data-feather="refresh-cw" class="mr-2"></i>Restart Device
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="alert-body">
                            <i data-feather="alert-triangle" class="mr-2"></i>
                            <strong>Warning:</strong> This action will restart the device and temporarily interrupt internet access.
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3">
                            <div class="avatar-content">
                                <i data-feather="hard-drive" class="font-medium-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Device Information</h6>
                            <p class="card-text text-muted mb-0">Location: <span class="location_name font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                        </div>
                    </div>
                    
                    <div class="bg-light-secondary p-2 rounded mb-3">
                        <h6 class="mb-2">What happens during restart:</h6>
                        <ul class="mb-0 pl-3">
                            <li>WiFi networks will be temporarily unavailable (2-3 minutes)</li>
                            <li>Connected users will be disconnected</li>
    
                        </ul>
                    </div>
                    
                    <p class="text-muted">Are you sure you want to restart this device?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-restart-btn">
                        <i data-feather="refresh-cw" class="mr-1"></i>
                        <span>Restart Device</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firmware Update Modal -->
    <div class="modal fade" id="firmware-update-modal" tabindex="-1" role="dialog" aria-labelledby="firmware-update-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="firmware-update-modal-title">
                        <i data-feather="download" class="mr-2"></i>Update Firmware
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="alert-body">
                            <i data-feather="info" class="mr-2"></i>
                            <strong>Important:</strong> Firmware update will restart the device and may take 5-10 minutes to complete.
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3">
                            <div class="avatar-content">
                                <i data-feather="hard-drive" class="font-medium-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Current Device Information</h6>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Current Firmware: <span class="router_firmware font-weight-bold"></span></p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="firmware-version-select">Available Firmware Versions</label>
                        <select class="form-control" id="firmware-version-select">
                            <option value="">Loading firmware versions...</option>
                        </select>
                        <small class="text-muted">Select a firmware version compatible with your device model.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="firmware-description">Firmware Description</label>
                        <div class="card">
                            <div class="card-body p-2">
                                <div id="firmware-description">
                                    <p class="text-muted mb-0">Select a firmware version to view details.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light-warning p-2 rounded mb-3">
                        <h6 class="mb-2">During firmware update:</h6>
                        <ul class="mb-0 pl-3">
                            <li>Device will reboot automatically</li>
                            <li>WiFi networks will be unavailable for 5-10 minutes</li>
                            <li>All connected users will be disconnected</li>
                            <li>Do not power off the device during update</li>
                        </ul>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="start-firmware-update-btn" disabled>
                        <i data-feather="download" class="mr-1"></i>
                        <span>Update Firmware</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firmware Update Progress Modal -->
    <div class="modal fade" id="firmware-progress-modal" tabindex="-1" role="dialog" aria-labelledby="firmware-progress-modal-title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="firmware-progress-modal-title">
                        <i data-feather="download" class="mr-2"></i>Updating Firmware
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="alert-body">
                            <i data-feather="alert-triangle" class="mr-2"></i>
                            <strong>Do not close this window or power off the device during update.</strong>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="firmware-progress-bar"></div>
                    </div>
                    
                    <div class="text-center">
                        <h6 id="firmware-progress-status">Preparing firmware update...</h6>
                        <p class="text-muted mb-0" id="firmware-progress-description">This may take several minutes to complete.</p>
                    </div>
                    
                    <div class="timeline mt-3">
                        <div class="timeline-item">
                            <div class="timeline-point-indicator timeline-point-primary" id="step-1-indicator"></div>
                            <div class="timeline-event">
                                <h6>Uploading Firmware</h6>
                                <p class="text-muted mb-0">Transferring firmware to device</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point-indicator" id="step-2-indicator"></div>
                            <div class="timeline-event">
                                <h6>Installing Update</h6>
                                <p class="text-muted mb-0">Writing firmware to device memory</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point-indicator" id="step-3-indicator"></div>
                            <div class="timeline-event">
                                <h6>Rebooting Device</h6>
                                <p class="text-muted mb-0">Device will restart with new firmware</p>
                            </div>
                        </div>
                    </div>
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
    <script src="/assets/js/location-details.js?time=<?php echo time(); ?>"></script>
    <script>
        // Firmware Update Modal functionality
        $(document).ready(function() {
            // Show firmware update modal when button is clicked
            $('#update-firmware-btn').on('click', function() {
                // Check if router model is set
                const routerModel = $('.router_model_updated').text().trim();
                if (!routerModel) {
                    toastr.error('Please set the router model first in Location Details before updating firmware.');
                    return;
                }
                
                $('#firmware-update-modal').modal('show');
                loadFirmwareVersions();
            });

            // Load firmware versions based on router model
            function loadFirmwareVersions() {
                // Get router model from the dropdown selection or current device
                const routerModel = $('#router-model-select').val() || $('.router_model_updated').text();
                const $select = $('#firmware-version-select');
                
                console.log('Loading firmware for model:', routerModel);
                
                // Clear existing options
                $select.html('<option value="">Loading firmware versions...</option>');
                
                // Make API call to get firmware versions based on model
                getFirmwareByModel(routerModel)
                
                    .then(function(firmwareVersions) {
                        console.log('Received firmware versions:', firmwareVersions);
                        
                        $select.empty();
                        if (firmwareVersions.length === 0) {
                            $select.html('<option value="">No firmware versions available for this model</option>');
                            $('#firmware-description').html('<div class="alert alert-warning mb-0"><i data-feather="alert-triangle" class="mr-1"></i>This device model (' + routerModel + ') is not supported for firmware updates. Only 820AX and 835AX models are supported.</div>');
                            return;
                        }
                        
                        $select.append('<option value="">Select firmware version...</option>');
                        
                        firmwareVersions.forEach(function(firmware) {
                            console.log('Processing firmware:', firmware);
                            const option = `<option value="${firmware.id}" 
                                            data-name="${firmware.name}"
                                            data-version="${firmware.version}"
                                            data-description="${firmware.description}"
                                            data-release-date="${firmware.release_date}"
                                            data-file-size="${firmware.file_size}"
                                            data-changelog="${firmware.changelog}"
                                            data-model="${firmware.model}"
                                            data-file-name="${firmware.file_name}"
                                            data-md5sum="${firmware.md5sum}">
                                            ${firmware.name}
                                            ${firmware.is_latest ? ' (Latest)' : ''}
                                            ${firmware.is_current ? ' (Current)' : ''}
                                        </option>`;
                            $select.append(option);
                        });
                        
                        // Pre-select current firmware if device data is available
                        if (window.currentDeviceData && window.currentDeviceData.firmware_id) {
                            console.log('Pre-selecting current firmware ID:', window.currentDeviceData.firmware_id);
                            $select.val(window.currentDeviceData.firmware_id);
                            $select.trigger('change'); // Trigger change event to show firmware details
                        } else if (window.currentDeviceData && window.currentDeviceData.firmware_version) {
                            // If no firmware_id but we have firmware_version, try to match by name
                            console.log('Trying to pre-select by firmware version name:', window.currentDeviceData.firmware_version);
                            $select.find('option').each(function() {
                                if ($(this).data('name') === window.currentDeviceData.firmware_version) {
                                    $select.val($(this).val());
                                    $select.trigger('change');
                                    return false; // Break the loop
                                }
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Error loading firmware versions:', error);
                        $select.html('<option value="">Error loading firmware versions</option>');
                        $('#firmware-description').html('<div class="alert alert-danger mb-0"><i data-feather="alert-circle" class="mr-1"></i>Failed to load firmware versions. Please try again later.</div>');
                        toastr.error('Failed to load firmware versions');
                    });
            }

            // API call to get firmware by model
            function getFirmwareByModel(model) {

                console.log('Getting firmware by model:', model);
                return new Promise(function(resolve, reject) {
                    // Check if model is supported
                    if (!model || (model !== '820AX' && model !== '835AX')) {
                        resolve([]);
                        return;
                    }

                    $.ajax({
                        url: '/api/firmware/model/' + model,
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log("firmware response:::::", response);
                            // Transform API response to match expected format
                            let firmwareList = [];
                            
                            if (response.data && Array.isArray(response.data)) {
                                firmwareList = response.data.map(function(firmware) {
                                    return {
                                        id: firmware.id,
                                        name: firmware.name || 'Unnamed Firmware',
                                        version: firmware.version || firmware.name || 'Unknown Version',
                                        description: firmware.description || 'No description available',
                                        release_date: firmware.created_at ? firmware.created_at.split('T')[0] : 'Unknown',
                                        file_size: firmware.file_size ? (firmware.file_size + ' bytes') : 'Unknown',
                                        changelog: firmware.description || 'No changelog available',
                                        is_latest: false, // You may need to determine this logic
                                        is_current: false, // You may need to determine this logic
                                        model: firmware.model,
                                        file_name: firmware.file_name,
                                        md5sum: firmware.md5sum,
                                        is_enabled: firmware.is_enabled
                                    };
                                });
                            } else if (response && Array.isArray(response)) {
                                // Handle direct array response
                                firmwareList = response.map(function(firmware) {
                                    return {
                                        id: firmware.id,
                                        name: firmware.name || 'Unnamed Firmware',
                                        version: firmware.version || firmware.name || 'Unknown Version',
                                        description: firmware.description || 'No description available',
                                        release_date: firmware.created_at ? firmware.created_at.split('T')[0] : 'Unknown',
                                        file_size: firmware.file_size ? (firmware.file_size + ' bytes') : 'Unknown',
                                        changelog: firmware.description || 'No changelog available',
                                        is_latest: false,
                                        is_current: false,
                                        model: firmware.model,
                                        file_name: firmware.file_name,
                                        md5sum: firmware.md5sum,
                                        is_enabled: firmware.is_enabled
                                    };
                                });
                            }
                            
                            resolve(firmwareList);
                        },
                        error: function(xhr, status, error) {
                            console.error('API Error:', xhr.responseText);
                            reject(error);
                        }
                    });
                });
            }

            // Handle firmware version selection
            $('#firmware-version-select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const $button = $('#start-firmware-update-btn');
                const $description = $('#firmware-description');

                if (selectedOption.val()) {
                    // Enable update button
                    $button.prop('disabled', false);
                    
                    // Show firmware details
                    const details = `
                        <div class="row">
                            <div class="col-6">
                                <strong>Name:</strong> ${selectedOption.data('name')}<br>
                                <strong>Version:</strong> ${selectedOption.data('version')}<br>
                                <strong>Release Date:</strong> ${selectedOption.data('release-date')}<br>
                                <strong>File Size:</strong> ${selectedOption.data('file-size')}
                            </div>
                            <div class="col-6">
                                <strong>Model:</strong> ${selectedOption.data('model')}<br>
                                <strong>File Name:</strong> ${selectedOption.data('file-name')}<br>
                                <strong>MD5 Checksum:</strong><br>
                                <small class="text-muted">${selectedOption.data('md5sum')}</small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <p class="mb-0">${selectedOption.data('description')}</p>
                    `;
                    $description.html(details);
                } else {
                    // Disable update button
                    $button.prop('disabled', true);
                    $description.html('<p class="text-muted mb-0">Select a firmware version to view details.</p>');
                }
            });

            // Handle firmware update start
            $('#start-firmware-update-btn').on('click', function() {
                const selectedOption = $('#firmware-version-select option:selected');
                
                if (!selectedOption.val()) {
                    toastr.error('Please select a firmware version to update.');
                    return;
                }

                const firmwareId = selectedOption.val();
                const firmwareName = selectedOption.data('name');
                const locationId = getLocationId();
                
                console.log('Initiating firmware update:', {
                    locationId: locationId,
                    firmwareId: firmwareId,
                    firmwareName: firmwareName
                });
                
                if (!locationId) {
                    toastr.error('Location ID not found');
                    return;
                }

                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Initiating Update...').prop('disabled', true);

                // Make API call to update firmware
                $.ajax({
                    url: `/api/locations/${locationId}/update-firmware`,
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        firmware_id: firmwareId,
                        firmware_version: firmwareName
                    }),
                    success: function(response) {
                        console.log('Firmware update API response:', response);
                        
                        // Hide selection modal
                        $('#firmware-update-modal').modal('hide');
                        
                        // Show success message
                        toastr.success('Firmware update initiated successfully! The device will be upgraded in 5-10 minutes. Please do not power off the device during this time.', 'Firmware Update Started', {
                            timeOut: 8000,
                            extendedTimeOut: 3000,
                            closeButton: true,
                            progressBar: true
                        });
                        
                        // Update the displayed firmware version
                        if (response.data && response.data.device && response.data.device.firmware_version) {
                            $('.router_firmware').text(response.data.device.firmware_version);
                            // Update the stored device data
                            if (window.currentDeviceData) {
                                window.currentDeviceData.firmware_version = response.data.device.firmware_version;
                                window.currentDeviceData.firmware_id = response.data.device.firmware_id;
                            }
                        } else {
                            // If no firmware version in response, use the firmware name we sent
                            $('.router_firmware').text(firmwareName);
                            if (window.currentDeviceData) {
                                window.currentDeviceData.firmware_version = firmwareName;
                                window.currentDeviceData.firmware_id = firmwareId;
                            }
                        }
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Firmware update failed:', xhr.responseText);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Handle API error
                        handleApiError(xhr, status, error, 'updating device firmware');
                    }
                });
            });
        });

        // Function to get location ID (moved outside of document ready block for global access)
        function getLocationId() {
            // Option 1: From URL path (e.g., /locations/123/details or /location-details?id=123)
            const pathParts = window.location.pathname.split('/');
            console.log('URL path parts:', pathParts);
            
            // Check for locations/ID pattern
            const locationIndex = pathParts.indexOf('locations');
            if (locationIndex !== -1 && pathParts[locationIndex + 1]) {
                const locationId = pathParts[locationIndex + 1];
                console.log('Found location ID from path:', locationId);
                return locationId;
            }
            
            // Option 2: From URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const locationId = urlParams.get('location_id') || urlParams.get('id');
            if (locationId) {
                console.log('Found location ID from URL params:', locationId);
                return locationId;
            }
            
            // Option 3: From breadcrumb text (as fallback) - "Location 14"
            const breadcrumbText = $('.breadcrumb-item.active').text();
            console.log('Breadcrumb text:', breadcrumbText);
            const locationMatch = breadcrumbText.match(/Location (\d+)/);
            if (locationMatch) {
                const locationId = locationMatch[1];
                console.log('Found location ID from breadcrumb:', locationId);
                return locationId;
            }
            
            // Option 4: From data attribute or global variable
            if (window.currentLocationId) {
                console.log('Found location ID from global variable:', window.currentLocationId);
                return window.currentLocationId;
            }
            
            console.log('No location ID found');
            return null;
        }

        // Helper function to handle API errors consistently
        function handleApiError(xhr, status, error, context) {
            console.error(`API Error in ${context}:`, error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            if (xhr.status === 401) {
                console.error('Unauthorized - redirecting to login');
                toastr.error('Session expired. Please log in again.');
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
                return;
            }
            
            let errorMessage = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || response.error || errorMessage;
                } catch (e) {
                    errorMessage = error || errorMessage;
                }
            }
            
            toastr.error(`${context}: ${errorMessage}`);
        }

        // Router Model Selection functionality
        $(document).ready(function() {
            // Check authentication first
            if (!UserManager.getToken()) {
                console.error('No authentication token found, redirecting to login');
                
                // Debug: Let's see what's actually in localStorage
                console.log('localStorage contents:');
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    console.log(`${key}: ${localStorage.getItem(key)}`);
                }
                
                console.log('UserManager.getToken():', UserManager.getToken());
                console.log('Available UserManager methods:', Object.keys(UserManager));
                
                // Don't redirect immediately for debugging
                // window.location.href = '/';
                // return;
            } else {
                console.log('Authentication token found:', UserManager.getToken().substring(0, 20) + '...');
            }

            // Clear/unset router model on page load
            console.log('Clearing router model on page load');
            $('.router_model_updated').text('');
            $('.router_firmware').text('Unknown');  // Set default text instead of empty
            $('#router-model-select').val('');
            
            // Global variable to store current device data
            window.currentDeviceData = null;

            // Function to load device data from API
            function loadDeviceData() {
                console.log('Loading device data from API');
                
                // Get location ID from URL or data attribute
                const locationId = getLocationId();
                
                if (!locationId) {
                    console.log('No location ID found - cannot load device data');
                    return;
                }

                console.log('Making API call to /api/locations/' + locationId);

                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('API Response received:', response);
                        
                        // Extract device data from response
                        let device = null;
                        if (response.data && response.data.device) {
                            device = response.data.device;
                        } else if (response.device) {
                            device = response.device;
                        } else if (response.data && response.data.devices && response.data.devices.length > 0) {
                            device = response.data.devices[0]; // Take first device if multiple
                        }

                        console.log('Extracted device data:', device);

                        if (device) {
                            // Store device data globally for use in other functions
                            window.currentDeviceData = device;
                            
                            // Update router model if it exists
                            if (device.model) {
                                console.log('Setting router model to:', device.model);
                                $('.router_model_updated').text(device.model);
                                $('#router-model-select').val(device.model);
                            } else {
                                console.log('No device model found, leaving blank');
                                $('.router_model_updated').text('');
                                $('#router-model-select').val('');
                            }

                            // Update firmware version if it exists
                            if (device.firmware_version && device.firmware_version.trim() !== '') {
                                console.log('Setting firmware version to:', device.firmware_version);
                                $('.router_firmware').text(device.firmware_version);
                            } else {
                                console.log('No firmware version found, checking for latest firmware for model:', device.model);
                                $('.router_firmware').text('Not Set');
                                
                                // If device has a model but no firmware, try to get the latest firmware for this model
                                if (device.model && (device.model === '820AX' || device.model === '835AX')) {
                                    loadLatestFirmwareForModel(device.model);
                                }
                            }
                            // Update other device fields
                            if (device.reboot_count !== null && device.reboot_count !== undefined) {
                                console.log('Setting reboot count to:', device.reboot_count);
                                $('.reboot_count').text(device.reboot_count);
                            }
                        } else {
                            console.log('No device data found in response, setting defaults');
                            window.currentDeviceData = null;
                            $('.router_model_updated').text('');
                            $('.router_firmware').text('Unknown');
                        }
                    },
                    error: function(xhr, status, error) {
                        handleApiError(xhr, status, error, 'loading device data');
                    }
                });
            }

            // Function to load latest firmware for a specific model when current firmware is not set
            function loadLatestFirmwareForModel(model) {
                console.log('Loading latest firmware for model:', model);
                
                getFirmwareByModel(model)
                    .then(function(firmwareVersions) {
                        if (firmwareVersions.length > 0) {
                            // Find the latest firmware (you can modify this logic based on your needs)
                            // For now, we'll take the first one or look for one marked as latest
                            let latestFirmware = firmwareVersions.find(fw => fw.is_latest) || firmwareVersions[0];
                            
                            console.log('Found latest firmware:', latestFirmware);
                            $('.router_firmware').text(latestFirmware.version + ' (Latest Available)');
                        } else {
                            console.log('No firmware versions found for model:', model);
                            $('.router_firmware').text('No Firmware Available');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error loading latest firmware for model:', error);
                        $('.router_firmware').text('Error Loading Firmware');
                    });
            }

            // Save location information including router model
            $('#save-location-info').on('click', function() {
                const locationData = {
                    name: $('#location-name').val(),
                    address: $('#location-address').val(),
                    city: $('#location-city').val(),
                    state: $('#location-state').val(),
                    postal_code: $('#location-postal-code').val(),
                    country: $('#location-country').val(),
                    router_model: $('#router-model-select').val(),
                    manager: $('#location-manager').val(),
                    contact_email: $('#location-contact-email').val(),
                    contact_phone: $('#location-contact-phone').val(),
                    status: $('#location-status').val(),
                    description: $('#location-description').val(),
                };

                // Validate required fields
                if (!locationData.name) {
                    toastr.error('Location name is required.');
                    return;
                }

                // Router model is optional now - removed validation
                // if (!locationData.router_model) {
                //     toastr.error('Please select a router model.');
                //     return;
                // }

                // Show loading state
                const $button = $(this);
                const originalText = $button.html();
                $button.html('<i data-feather="loader" class="mr-1"></i> Saving...').prop('disabled', true);

                // Make real API call to save location information
                const locationId = getLocationId();
                if (!locationId) {
                    toastr.error('Location ID not found');
                    $button.html(originalText).prop('disabled', false);
                    return;
                }

                console.log('Saving location data:', locationData);

                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify(locationData),
                    success: function(response) {
                        console.log('Location data saved successfully:', response);
                        
                        // Update UI elements with new data
                        $('.location_name').text(locationData.name);
                        $('.location_address').text(locationData.address + ', ' + locationData.city + ', ' + locationData.state);
                        $('.router_model_updated').text(locationData.router_model);
                        
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                        
                        // Show success message
                        toastr.success('Location information saved successfully!');
                        
                        // Re-initialize feather icons
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                        
                        // Reload device data to verify the update
                        setTimeout(function() {
                            loadDeviceData();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Reset button state first
                        $button.html(originalText).prop('disabled', false);
                        
                        // Then handle the API error
                        handleApiError(xhr, status, error, 'saving location data');
                    }
                });
            });

            // Load existing location data when page loads
            function loadLocationData() {
                console.log('loadLocationData called');
                
                // In a real application, this would load from your backend
                // For now, we'll populate with sample data if fields are empty
                
                if (!$('#location-name').val()) {
                    console.log('Loading sample location data');
                    // Sample data - replace with actual API call
                    const sampleData = {
                        name: '',
                        address: '',
                        city: '',
                        state: '',
                        postal_code: '',
                        country: '',
                        router_model: '', // Always start with blank
                        manager: '',
                        contact_email: '',
                        contact_phone: '',
                        status: 'active',
                        description: ''
                    };
                    
                    // Populate form fields
                    $('#location-name').val(sampleData.name);
                    $('#location-address').val(sampleData.address);
                    $('#location-city').val(sampleData.city);
                    $('#location-state').val(sampleData.state);
                    $('#location-postal-code').val(sampleData.postal_code);
                    $('#location-country').val(sampleData.country);
                    $('#router-model-select').val(sampleData.router_model);
                    $('#location-manager').val(sampleData.manager);
                    $('#location-contact-email').val(sampleData.contact_email);
                    $('#location-contact-phone').val(sampleData.contact_phone);
                    $('#location-status').val(sampleData.status);
                    $('#location-description').val(sampleData.description);
                    
                    // Update UI elements
                    $('.location_name').text(sampleData.name);
                    $('.location_address').text(sampleData.address + ', ' + sampleData.city + ', ' + sampleData.state);
                    $('.router_model_updated').text(sampleData.router_model);
                } else {
                    console.log('Location data already loaded, skipping');
                }
            }

            // Load data when the location settings tab is shown
            $('a[href="#location-settings"]').on('shown.bs.tab', function() {
                loadLocationData();
            });

            // Load data immediately if location settings tab is active
            if ($('#location-settings').hasClass('active')) {
                loadLocationData();
            }

            // Handle router model selection change
            $('#router-model-select').on('change', function() {
                const selectedModel = $(this).val();
                console.log('Router model changed to:', selectedModel);
                
                // Update UI immediately
                $('.router_model_updated').text(selectedModel);
                
                // Update device model via API
                updateDeviceModel(selectedModel);
                
                // Show success message
                if (selectedModel) {
                    toastr.success('Router model updated to ' + selectedModel);
                }
            });

            // Function to update device model via API
            function updateDeviceModel(model) {
                const locationId = getLocationId();
                if (!locationId) {
                    console.log('No location ID found, cannot update device model');
                    return;
                }

                console.log('Updating device model to:', model, 'for location:', locationId);

                $.ajax({
                    url: '/api/locations/' + locationId,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        device: {
                            model: model
                        }
                    }),
                    success: function(response) {
                        console.log('Device model updated successfully:', response);
                        toastr.success('Router model updated successfully');
                        
                        // Reload device data to verify the update
                        setTimeout(function() {
                            loadDeviceData();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        handleApiError(xhr, status, error, 'updating device model');
                    }
                });
            }

            // Initialize router model on page load
            setTimeout(function() {
                const currentRouterModel = $('.router_model').text();
                console.log('Timeout check - current router model:', currentRouterModel);
                
                if (!currentRouterModel || currentRouterModel === '') {
                    const savedModel = localStorage.getItem('router_model');
                    if (savedModel) {
                        console.log('Timeout setting router model to saved:', savedModel);
                        $('.router_model').text(savedModel);
                    } else {
                        console.log('No saved model, leaving blank');
                    }
                } else {
                    console.log('Timeout check - router model already properly set:', currentRouterModel);
                }
            }, 100);

            // Load device data when page loads
            loadDeviceData();
            
            // Test API call for debugging
            console.log('Testing API authentication...');
            $.ajax({
                url: '/api/locations',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('API Test Success:', response);
                },
                error: function(xhr, status, error) {
                    console.log('API Test Failed:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                }
            });
        });
    </script>
    </body>
<!-- END: Body-->
</html>