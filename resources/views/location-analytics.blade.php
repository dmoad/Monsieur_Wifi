<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Mr WiFi - Location Analytics Dashboard">
    <meta name="keywords" content="wifi, location, analytics, dashboard, network, monitoring">
    <meta name="author" content="Mr WiFi">
    <title>Location Analytics - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->

    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>

    <!-- Add custom styles -->
    <style>
        .location-card {
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
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
        
        .network-stat-icon {
            height: 45px;
            width: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            color: white;
        }
        
        .leaflet-map {
            z-index: 1;
        }
        
        .leaflet-container {
            font-family: inherit;
            font-size: inherit;
        }
        
        .leaflet-popup-content {
            margin: 0;
            padding: 0;
        }
        
        .feather {
            height: 14px;
            width: 14px;
            display: inline-block;
            vertical-align: middle;
        }
        
        .custom-div-icon, .marker-icon {
            background: transparent;
            border: none;
        }
        
        .avatar-content svg {
            color: inherit;
            width: 24px !important;
            height: 24px !important;
            stroke-width: 2;
            display: block !important;
        }
        
        [data-feather] {
            display: inline-block !important;
            vertical-align: middle;
        }
        
        .online-users-table {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }
        
        .table-responsive {
            border-radius: 8px;
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
                <li class="nav-item dropdown dropdown-notification mr-25">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge badge-pill badge-primary badge-up">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <!-- Notification content here -->
                    </ul>
                </li>
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name font-weight-bolder"></span>
                            <span class="user-status"></span>
                        </div>
                        <span class="avatar">
                            <img class="round" src="/app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40">
                            <span class="avatar-status-online"></span>
                        </span>
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
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="/dashboard">
                        <span class="brand-logo">
                            <svg viewBox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" height="28">
                                <defs>
                                    <linearGradient id="linearGradient-1" x1="100%" y1="10.5120544%" x2="50%" y2="89.4879456%">
                                        <stop stop-color="#000000" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </linearGradient>
                                    <linearGradient id="linearGradient-2" x1="64.0437835%" y1="46.3276743%" x2="37.373316%" y2="100%">
                                        <stop stop-color="#EEEEEE" stop-opacity="0" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </linearGradient>
                                </defs>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-400.000000, -178.000000)">
                                        <g transform="translate(400.000000, 178.000000)">
                                            <path class="text-primary" d="M-5.68434189e-14,2.84217094e-14 L39.1816085,2.84217094e-14 L69.3453773,32.2519224 L101.428699,2.84217094e-14 L138.784583,2.84217094e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L6.71554594,44.4188507 C2.46876683,39.9813776 0.345377275,35.1089553 0.345377275,29.8015838 C0.345377275,24.4942122 0.230251516,14.560351 -5.68434189e-14,2.84217094e-14 Z" style="fill:currentColor"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
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
                <li class="navigation-header" data-admin-only="true"><span>For Admin</span></li>
                <li class="nav-item" data-admin-only="true">
                    <a class="d-flex align-items-center" href="/accounts">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Accounts</span>
                    </a>
                </li>
                <li class="nav-item" data-admin-only="true">
                    <a class="d-flex align-items-center" href="/domain-blocking">
                        <i data-feather="slash"></i>
                        <span class="menu-title text-truncate">Domain Blocking</span>
                    </a>
                </li>
                <li class="nav-item" data-admin-only="true">
                    <a class="d-flex align-items-center" href="/firmware">
                        <i data-feather="download"></i>
                        <span class="menu-title text-truncate">Firmware</span>
                    </a>
                </li>
                <li class="nav-item" data-admin-only="true">
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
                            <h2 class="content-header-title float-left mb-0">Location Analytics</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/locations">Locations</a></li>
                                    <li class="breadcrumb-item active">Location Analytics</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="download"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="mr-1" data-feather="file-pdf"></i> PDF</a>
                                <a class="dropdown-item" href="#"><i class="mr-1" data-feather="file"></i> Excel</a>
                                <a class="dropdown-item" href="#"><i class="mr-1" data-feather="printer"></i> Print</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Location Analytics Content Starts -->
                <section id="location-analytics">
                    <div class="row match-height">
                        <!-- Location Overview Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card card-congratulation-medal" id="location-overview-card">
                                <div class="card-body">
                                    <h5 id="location-name">Loading Location...</h5>
                                    <p class="card-text font-small-3">Network Status Overview</p>
                                    <h3 class="mb-75 mt-2 pt-50">
                                        <span class="text-primary" id="location-status">Loading...</span>
                                    </h3>
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center mr-2">
                                            <i data-feather="wifi" class="text-success font-medium-2 mr-50"></i>
                                            <span class="font-weight-bold" id="location-uptime">-</span>% Uptime
                                        </div>
                                        <span class="mx-1">|</span>
                                        <div class="d-flex align-items-center ml-1">
                                            <i data-feather="users" class="text-info font-medium-2 mr-50"></i>
                                            <span class="font-weight-bold" id="location-users">-</span> Users
                                        </div>
                                    </div>
                                    <a type="button" class="btn btn-primary mt-1" href="/locations">View All Locations</a>
                                    <img src="/app-assets/images/illustration/badge.svg" class="congratulation-medal" alt="Medal Pic" />
                                </div>
                            </div>
                        </div>
                        <!--/ Location Overview Card -->

                        <!-- Location Statistics Card -->
                        <div class="col-lg-8 col-12">
                            <div class="card card-statistics" id="location-stats">
                                <div class="card-header">
                                    <h4 class="card-title">Location Statistics</h4>
                                    <div class="d-flex align-items-center">
                                        <p class="card-text mr-25 mb-0">Updated just now</p>
                                    </div>
                                </div>
                                <div class="card-body statistics-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="users" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="stats-active-users">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Active Users</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                            <div class="media">
                                                <div class="avatar bg-light-info mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="download" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="stats-data-usage">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Data Usage</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                            <div class="media">
                                                <div class="avatar bg-light-warning mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="wifi" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="stats-devices">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Connected Devices</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-12">
                                            <div class="media">
                                                <div class="avatar bg-light-success mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="activity" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="stats-sessions">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Sessions</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Location Statistics Card -->
                    </div>

                    <div class="row match-height">
                        <!-- Location Map -->
                        <div class="col-lg-8 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Location Map</h4>
                                    <div class="d-flex">
                                        <button id="fullscreen-btn" class="btn btn-sm btn-outline-primary mr-1">
                                            <i data-feather="maximize"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="location-map" style="height: 400px;">
                                        <div class="d-flex align-items-center justify-content-center h-100" id="map-loading">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary mb-2" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <p class="text-muted">Loading location map...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Location Map -->

                        <!-- Data Usage Trends -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Data Usage Trends</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dataUsageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Last 7 Days
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dataUsageDropdown">
                                            <a class="dropdown-item" href="javascript:void(0);">Last 7 Days</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 d-flex flex-column flex-wrap text-center mb-2">
                                            <h1 class="font-weight-bolder mt-2 mb-0" id="total-location-usage">-</h1>
                                            <p class="card-text">Total Usage This Week</p>
                                        </div>
                                    </div>
                                    <div id="location-data-usage-chart" class="mt-2" style="min-height: 270px;"></div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-info mr-1 p-50">
                                                    <div class="avatar-content">
                                                        <i data-feather="download" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0" id="location-download-usage">- GB</h4>
                                                    <p class="card-text font-small-3 mb-0">Download</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-warning mr-1 p-50">
                                                    <div class="avatar-content">
                                                        <i data-feather="upload" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0" id="location-upload-usage">- GB</h4>
                                                    <p class="card-text font-small-3 mb-0">Upload</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Data Usage Trends -->
                    </div>

                    <!-- Currently Connected Users -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Currently Connected Users</h4>
                                    <div class="d-flex align-items-center">
                                        <button id="refresh-users-btn" class="btn btn-sm btn-outline-primary mr-1">
                                            <i data-feather="refresh-cw"></i> Refresh
                                        </button>
                                        <span class="badge badge-light-success" id="online-users-count">0 Users Online</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="online-users-table">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>MAC Address</th>
                                                    <th>IP Address</th>
                                                    <th>Connected At</th>
                                                    <th>Data Usage</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="online-users-tbody">
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2 text-muted">Loading connected users...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Analytics Overview -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card" id="analytics-section">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Location Analytics Overview</h4>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="analyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Last 7 Days
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analyticsDropdown">
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="1">Today</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="7">Last 7 Days</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="30">Last 30 Days</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="90">Last 90 Days</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="analytics-errors"></div>
                                    
                                    <!-- Analytics Chart -->
                                    <div id="location-analytics-chart" style="height: 350px;"></div>
                                    
                                    <!-- Analytics Metrics Row -->
                                    <div class="row mt-3">
                                        <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-primary p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="users" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" id="analytics-total-users">-</h4>
                                                    <span>Total Users</span>
                                                </div>
                                            </div>
                                            <span class="text-muted">Unique users connected</span>
                                        </div>
                                        
                                        <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-info p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="activity" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" id="analytics-data-usage">- GB</h4>
                                                    <span>Data Usage</span>
                                                </div>
                                            </div>
                                            <span class="text-muted">Total bandwidth consumed</span>
                                        </div>
                                        
                                        <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-success p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="wifi" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" id="analytics-uptime">-%</h4>
                                                    <span>Uptime</span>
                                                </div>
                                            </div>
                                            <span class="text-muted">Network availability</span>
                                        </div>
                                        
                                        <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="avatar bg-light-warning p-50 mr-1">
                                                    <div class="avatar-content">
                                                        <i data-feather="monitor" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" id="analytics-sessions">-</h4>
                                                    <span>Total Sessions</span>
                                                </div>
                                            </div>
                                            <span class="text-muted">Connection sessions</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Location Analytics Content Ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="#" target="_blank">Mr WiFi</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span>
            <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js"></script>
    
    <!-- BEGIN: Page JS-->
    <script>
        var locationId = "{{ $location_id }}";
        console.log("Current location ID:", locationId);
        
        // Global variables
        let locationMap;
        let onlineUsersTable;
        let dataUsageChart;
        let analyticsChart;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM content loaded");
            
            // Initialize Feather icons
            if (typeof feather !== 'undefined') {
                console.log("Initializing Feather icons");
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
            
            // Initialize page
            initializeLocationAnalytics();
        });

        // Use window.onload to ensure all resources are loaded
        window.onload = function() {
            console.log("Window loaded - starting chart initialization");
            
            // Check if ApexCharts exists
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts is not loaded. Charts cannot be initialized.");
                return;
            }
            
            // Initialize charts
            initializeCharts();
        };

        async function initializeLocationAnalytics() {
            try {
                // Load location data
                await loadLocationData();
                
                // Load online users
                await loadOnlineUsers();
                
                // Initialize map
                initializeMap();
                
                // Set up auto-refresh for online users
                setInterval(loadOnlineUsers, 30000); // Refresh every 30 seconds
                
            } catch (error) {
                console.error('Error initializing location analytics:', error);
                showError('Failed to load location analytics data');
            }
        }

        async function loadLocationData() {
            try {
                const token = UserManager.getToken();
                if (!token) {
                    window.location.href = '/';
                    return;
                }

                const response = await fetch(`/api/locations/${locationId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load location data');
                }

                const data = await response.json();
                updateLocationDisplay(data);
                
            } catch (error) {
                console.error('Error loading location data:', error);
                showError('Failed to load location information');
            }
        }

        async function loadOnlineUsers() {
            try {
                const token = UserManager.getToken();
                if (!token) return;

                const response = await fetch(`/api/locations/${locationId}/online-users`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load online users');
                }

                const data = await response.json();
                updateOnlineUsersDisplay(data);
                
            } catch (error) {
                console.error('Error loading online users:', error);
                showError('Failed to load online users');
            }
        }

        function updateLocationDisplay(data) {
            // Update location name
            document.getElementById('location-name').textContent = data.name || 'Unknown Location';
            
            // Update location status
            const statusElement = document.getElementById('location-status');
            if (data.status === 'online') {
                statusElement.textContent = 'Online';
                statusElement.className = 'text-success';
            } else {
                statusElement.textContent = 'Offline';
                statusElement.className = 'text-danger';
            }
            
            // Update statistics
            document.getElementById('location-uptime').textContent = data.uptime || '0';
            document.getElementById('location-users').textContent = data.active_users || '0';
            document.getElementById('stats-active-users').textContent = data.active_users || '0';
            document.getElementById('stats-data-usage').textContent = formatBytes(data.data_usage || 0);
            document.getElementById('stats-devices').textContent = data.connected_devices || '0';
            document.getElementById('stats-sessions').textContent = data.total_sessions || '0';
        }

        function updateOnlineUsersDisplay(users) {
            const tbody = document.getElementById('online-users-tbody');
            const countElement = document.getElementById('online-users-count');
            
            // Update count
            countElement.textContent = `${users.length} Users Online`;
            
            // Clear existing rows
            tbody.innerHTML = '';
            
            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i data-feather="wifi-off" class="mb-2"></i>
                            <p>No users currently connected</p>
                        </td>
                    </tr>
                `;
                feather.replace();
                return;
            }
            
            // Add user rows
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar bg-light-primary mr-1" style="background-color: ${getRandomColor()}">
                                ${getInitials(user.username || user.mac_address)}
                            </div>
                            <div>
                                <h6 class="mb-0">${user.username || 'Guest User'}</h6>
                                <small class="text-muted">${user.device_type || 'Unknown Device'}</small>
                            </div>
                        </div>
                    </td>
                    <td><code>${user.mac_address}</code></td>
                    <td><code>${user.ip_address}</code></td>
                    <td>
                        <small class="text-muted">${formatDateTime(user.connected_at)}</small>
                    </td>
                    <td>
                        <div>
                            <small class="text-success">↓ ${formatBytes(user.download_bytes || 0)}</small><br>
                            <small class="text-warning">↑ ${formatBytes(user.upload_bytes || 0)}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light-success">
                            <i data-feather="wifi" class="mr-25"></i>Connected
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="viewUserDetails('${user.mac_address}')">
                                    <i data-feather="eye" class="mr-1"></i>View Details
                                </a>
                                <a class="dropdown-item" href="#" onclick="disconnectUser('${user.mac_address}')">
                                    <i data-feather="x-circle" class="mr-1"></i>Disconnect
                                </a>
                            </div>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Reinitialize feather icons
            feather.replace();
        }

        function initializeMap() {
            // Initialize map centered on a default location
            locationMap = L.map('location-map').setView([51.505, -0.09], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(locationMap);
            
            // Add location marker (this would be dynamic based on location data)
            L.marker([51.505, -0.09])
                .addTo(locationMap)
                .bindPopup('Location: ' + locationId)
                .openPopup();
            
            // Hide loading indicator
            document.getElementById('map-loading').style.display = 'none';
        }

        function initializeCharts() {
            // Data Usage Chart
            const dataUsageOptions = {
                chart: {
                    type: 'donut',
                    height: 270
                },
                colors: ['#7367F0', '#FF9F43'],
                series: [185, 60],
                labels: ['Download', 'Upload'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%'
                        }
                    }
                }
            };
            
            dataUsageChart = new ApexCharts(document.querySelector("#location-data-usage-chart"), dataUsageOptions);
            dataUsageChart.render();
            
            // Analytics Chart
            const analyticsOptions = {
                chart: {
                    type: 'area',
                    height: 350
                },
                colors: ['#7367F0', '#28C76F', '#FF9F43'],
                series: [
                    {
                        name: 'Users',
                        data: [30, 40, 35, 50, 49, 60, 70]
                    },
                    {
                        name: 'Sessions',
                        data: [20, 25, 30, 35, 40, 45, 50]
                    },
                    {
                        name: 'Data Usage (GB)',
                        data: [10, 15, 20, 25, 30, 35, 40]
                    }
                ],
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                },
                stroke: {
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.6,
                        opacityTo: 0.1
                    }
                }
            };
            
            analyticsChart = new ApexCharts(document.querySelector("#location-analytics-chart"), analyticsOptions);
            analyticsChart.render();
        }

        // Event handlers
        document.getElementById('refresh-users-btn').addEventListener('click', function() {
            loadOnlineUsers();
            if (typeof toastr !== 'undefined') {
                toastr.info('Refreshing online users...');
            }
        });

        document.getElementById('fullscreen-btn').addEventListener('click', function() {
            const mapElement = document.getElementById('location-map');
            
            if (!document.fullscreenElement) {
                mapElement.requestFullscreen();
                this.innerHTML = '<i data-feather="minimize"></i>';
            } else {
                document.exitFullscreen();
                this.innerHTML = '<i data-feather="maximize"></i>';
            }
            
            setTimeout(() => feather.replace(), 100);
        });

        // Utility functions
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }

        function getInitials(name) {
            if (!name) return '?';
            const words = name.split(' ');
            if (words.length >= 2) {
                return words[0][0] + words[1][0];
            }
            return name.substring(0, 2);
        }

        function getRandomColor() {
            const colors = ['#7367F0', '#28C76F', '#FF9F43', '#EA5455', '#00CFE8'];
            return colors[Math.floor(Math.random() * colors.length)];
        }

        function showError(message) {
            if (typeof toastr !== 'undefined') {
                toastr.error(message);
            } else {
                console.error(message);
            }
        }

        function viewUserDetails(macAddress) {
            // Implementation for viewing user details
            console.log('View details for:', macAddress);
        }

        function disconnectUser(macAddress) {
            // Implementation for disconnecting user
            console.log('Disconnect user:', macAddress);
        }

        // Authentication and user management
        $(document).ready(function() {
            // Check if user is logged in using UserManager from config.js
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            
            if (!token || !user) {
                // No token or user found, redirect to login page
                window.location.href = '/';
                return;
            }
            
            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            
            // Implement logout functionality using UserManager
            $('.logout-button, a[href="/logout"]').on('click', function(e) {
                e.preventDefault();
                UserManager.logout(true); // true to redirect to login page
            });
            
            // Check user role and show/hide admin menu items
            if (!UserManager.hasRole('admin')) {
                $('[data-admin-only="true"]').hide();
            }
        });
    </script>
</body>
</html>