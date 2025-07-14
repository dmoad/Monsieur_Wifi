<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Mr WiFi - Location Analytics Dashboard">
    <meta name="keywords" content="wifi, location, analytics, dashboard, network, monitoring">
    <meta name="author" content="Mr WiFi">
    <title>Location Analytics - Mr WiFi</title>
    <link rel="/apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/datatables.min.css">
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
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->

    <!-- Ensure these are included at the top of your head section -->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>

    <!-- Add this right before the closing </head> tag -->
    <style>
        /* Ensure feather icons in avatars are visible */
        .avatar-content svg {
            color: inherit;
            width: 24px !important;
            height: 24px !important;
            stroke-width: 2;
            display: block !important;
        }
        
        /* Fix for general feather icons */
        [data-feather] {
            display: inline-block !important;
            vertical-align: middle;
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
                    <!-- Notification dropdown content -->
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <!-- Notification content here -->
                        </ul>
                    </li>
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div><span class="avatar"><img class="round" src="/app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
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
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i>
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
                <!-- Location Selector and Date Picker -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="form-group mb-0 mr-1">
                                        <select class="form-control" id="location-selector">
                                            <option value="london-hq" {{ $location_id == 'london-hq' ? 'selected' : '' }}>London HQ</option>
                                            <option value="new-york-office" {{ $location_id == 'new-york-office' ? 'selected' : '' }}>New York Office</option>
                                            <option value="paris-office" {{ $location_id == 'paris-office' ? 'selected' : '' }}>Paris Office</option>
                                            <option value="tokyo-branch" {{ $location_id == 'tokyo-branch' ? 'selected' : '' }}>Tokyo Branch</option>
                                            <option value="sydney-office" {{ $location_id == 'sydney-office' ? 'selected' : '' }}>Sydney Office</option>
                                            <option value="berlin-office" {{ $location_id == 'berlin-office' ? 'selected' : '' }}>Berlin Office</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-primary active">Day</button>
                                    <button type="button" class="btn btn-outline-primary">Week</button>
                                    <button type="button" class="btn btn-outline-primary">Month</button>
                                    <button type="button" class="btn btn-outline-primary">Year</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Overview - Simplified for one AP per location -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Location Overview: {{ ucfirst(str_replace(['-', '_'], ' ', $location_id)) }}</h4>
                                <span class="badge badge-light-success">Healthy</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Manager</h6>
                                                <small>Sarah Johnson</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="avatar bg-light-success p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Access Point</h6>
                                                <small>AP-LHQ-01 (Active)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Address</h6>
                                                <small>10 Baker Street, London</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row match-height">
                    <!-- Total Users Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start pb-0">
                                <div>
                                    <h2 class="font-weight-bolder">1,250</h2>
                                    <p class="card-text mb-0">Active Users</p>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="line-area-chart-1"></div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-25"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        <span class="font-weight-bold text-success">+12.4%</span>
                                    </div>
                                    <small class="text-muted">vs last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Usage Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start pb-0">
                                <div>
                                    <h2 class="font-weight-bolder">6.2 TB</h2>
                                    <p class="card-text mb-0">Data Usage</p>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="line-area-chart-4"></div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-25"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        <span class="font-weight-bold text-success">+18.2%</span>
                                    </div>
                                    <small class="text-muted">vs last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Connected Devices Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start pb-0">
                                <div>
                                    <h2 class="font-weight-bolder">842</h2>
                                    <p class="card-text mb-0">Connected Devices</p>
                                </div>
                                <div class="avatar bg-light-success p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="line-area-chart-2"></div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-25"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        <span class="font-weight-bold text-success">+5.8%</span>
                                    </div>
                                    <small class="text-muted">current status</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start pb-0">
                                <div>
                                    <h2 class="font-weight-bolder">99.8%</h2>
                                    <p class="card-text mb-0">Uptime</p>
                                </div>
                                <div class="avatar bg-light-danger p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="line-area-chart-3"></div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-25"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        <span class="font-weight-bold text-success">+0.2%</span>
                                    </div>
                                    <small class="text-muted">vs last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traffic Analysis -->
                <div class="row match-height">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Network Traffic</h4>
                                    <small class="text-muted">Download & upload traffic analysis</small>
                                </div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="d-flex align-items-center mr-2">
                                        <span class="bullet bullet-primary bullet-sm mr-50"></span>
                                        <span class="mr-75">Download</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="bullet bullet-warning bullet-sm mr-50"></span>
                                        <span>Upload</span>
                                    </div>
                                    <div class="dropdown chart-dropdown ml-1">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="traffic-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i data-feather="calendar" class="mr-25"></i>Last 7 Days
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="traffic-dropdown">
                                            <a class="dropdown-item" href="#">Last 28 Days</a>
                                            <a class="dropdown-item" href="#">Last Month</a>
                                            <a class="dropdown-item" href="#">Last Year</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="location-network-traffic-chart"></div>
                                <div class="row mt-3">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">3.8 TB</h4>
                                                <p class="card-text text-muted">Total Download</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">2.4 TB</h4>
                                                <p class="card-text text-muted">Total Upload</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-info p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">28%</h4>
                                                <p class="card-text text-muted">Growth Rate</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="row match-height">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">User Activity</h4>
                                <div class="dropdown chart-dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="activity-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Last 7 Days
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="activity-dropdown">
                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">1,250</h4>
                                                <p class="card-text">Total Users</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-success p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">245</h4>
                                                <p class="card-text">New Users</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-info p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">1,005</h4>
                                                <p class="card-text">Returning Users</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">3:42</h4>
                                                <p class="card-text">Avg. Session</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="location-user-activity-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historical Data - Removed Optimization Suggestions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Historical Comparison</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="card-text mb-2">Performance compared to previous periods</p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Metric</th>
                                                        <th>Current</th>
                                                        <th>Previous</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Active Users</td>
                                                        <td>1,250</td>
                                                        <td>1,112</td>
                                                        <td><span class="text-success">+12.4%</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Data Usage</td>
                                                        <td>6.2 TB</td>
                                                        <td>5.4 TB</td>
                                                        <td><span class="text-success">+14.8%</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Connected Devices</td>
                                                        <td>842</td>
                                                        <td>795</td>
                                                        <td><span class="text-success">+5.9%</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Avg. Session Duration</td>
                                                        <td>3:42</td>
                                                        <td>3:18</td>
                                                        <td><span class="text-success">+12.1%</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script src="/app-assets/js/scripts/cards/card-statistics.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->
    <!-- Include config.js before other custom scripts -->
    <script src="assets/js/config.js"></script>
    <!-- BEGIN: Page JS-->
    <script>
        var locationId = "{{ $location_id }}";
        console.log("Current location ID:", locationId);
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

            // Location selector change handler
            $('#location-selector').on('change', function() {
                var selectedLocation = $(this).val();
                console.log("Selected location: " + selectedLocation);
                
                // Only redirect if the selected location is different from current
                if (selectedLocation !== locationId) {
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Loading data for ' + $('#location-selector option:selected').text());
                    }
                    // Redirect to the analytics page for the selected location
                    window.location.href = '/locations/analytics/' + selectedLocation;
                }
            });
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

        function initializeCharts() {
            console.log("Initializing charts");
            
            setTimeout(function() {
                try {
                    // Location Users Chart
                    if (document.querySelector("#location-users-chart")) {
                        var locationUsersChart = new ApexCharts(document.querySelector("#location-users-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#7367F0'],
                            series: [{ data: [950, 1020, 985, 1050, 1100, 1150, 1250] }]
                        });
                        locationUsersChart.render();
                    }

                    // Location Data Usage Chart
                    if (document.querySelector("#location-data-usage-chart")) {
                        var locationDataUsageChart = new ApexCharts(document.querySelector("#location-data-usage-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#FF9F43'],
                            series: [{ data: [4.8, 5.1, 5.3, 5.5, 5.8, 6.0, 6.2] }]
                        });
                        locationDataUsageChart.render();
                    }

                    // Location Connected Devices Chart
                    if (document.querySelector("#location-connected-devices-chart")) {
                        var locationConnectedDevicesChart = new ApexCharts(document.querySelector("#location-connected-devices-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#28C76F'],
                            series: [{ data: [720, 745, 765, 775, 790, 810, 842] }]
                        });
                        locationConnectedDevicesChart.render();
                    }

                    // Location Uptime Chart
                    if (document.querySelector("#location-uptime-chart")) {
                        var locationUptimeChart = new ApexCharts(document.querySelector("#location-uptime-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#EA5455'],
                            series: [{ data: [99.2, 99.3, 99.5, 99.6, 99.7, 99.7, 99.8] }]
                        });
                        locationUptimeChart.render();
                    }

                    // Location Network Traffic Chart
                    if (document.querySelector("#location-network-traffic-chart")) {
                        var locationNetworkTrafficChart = new ApexCharts(document.querySelector("#location-network-traffic-chart"), {
                            chart: { height: 350, type: 'area' },
                            colors: ['#7367F0', '#FF9F43'],
                            series: [
                                { name: 'Download', data: [42, 55, 41, 67, 52, 58, 48] },
                                { name: 'Upload', data: [25, 32, 28, 39, 35, 28, 35] }
                            ],
                            xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth' },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.9,
                                    stops: [0, 90, 100]
                                }
                            }
                        });
                        locationNetworkTrafficChart.render();
                    }

                    // Location User Activity Chart
                    if (document.querySelector("#location-user-activity-chart")) {
                        var locationUserActivityChart = new ApexCharts(document.querySelector("#location-user-activity-chart"), {
                            chart: { height: 300, type: 'bar' },
                            colors: ['#7367F0', '#28C76F'],
                            series: [
                                { name: 'New Users', data: [35, 40, 30, 50, 45, 20, 25] },
                                { name: 'Returning Users', data: [125, 150, 140, 180, 160, 180, 170] }
                            ],
                            xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
                            plotOptions: {
                                bar: {
                                    columnWidth: '60%',
                                    endingShape: 'rounded'
                                }
                            },
                            dataLabels: { enabled: false },
                            legend: {
                                position: 'top'
                            },
                            fill: {
                                opacity: 1
                            }
                        });
                        locationUserActivityChart.render();
                    }

                    // Initialize DataTable if available
                    if (typeof $.fn.DataTable !== 'undefined' && $('.table').length > 0) {
                        $('.table').DataTable({
                            responsive: true,
                            paging: false,
                            info: false, 
                            searching: false
                        });
                    }

                } catch (error) {
                    console.error("Error during chart initialization:", error);
                }
            }, 500); // Small delay to ensure DOM is fully ready
        }

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
            
            // Show welcome toast if toastr is available
            // if (typeof toastr !== 'undefined') {
            //     toastr.options = {
            //         closeButton: true,
            //         tapToDismiss: false,
            //         progressBar: true,
            //         positionClass: 'toast-top-right',
            //         showDuration: '300',
            //         hideDuration: '1000',
            //         timeOut: '5000',
            //         extendedTimeOut: '1000',
            //         showEasing: 'swing',
            //         hideEasing: 'linear',
            //         showMethod: 'fadeIn',
            //         hideMethod: 'fadeOut'
            //     };
                
            //     toastr.success(
            //         'Welcome back to the Location Analytics',
            //         'Hello, ' + user.name + '!'
            //     );
            // }
            
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

    <!-- Enhanced icon initialization specifically for avatar icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // First attempt to fix avatar icons
            fixAvatarIcons();
            
            // Multiple attempts with increasing delays
            setTimeout(fixAvatarIcons, 500);
            setTimeout(fixAvatarIcons, 1500);
            setTimeout(fixAvatarIcons, 3000);
        });
        
        function fixAvatarIcons() {
            console.log("Fixing avatar icons...");
            
            // Backup approach: Use direct SVG replacement for specific icons if feather isn't working
            const iconSvgs = {
                'users': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'database': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',
                'wifi': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>',
                'activity': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>',
                'arrow-down': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>',
                'arrow-up': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>',
                'trending-up': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>',
                'check-circle': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                'server': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>',
                'home': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                'clock': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
            };
            
            // Get all avatar-content containers
            const avatarContainers = document.querySelectorAll('.avatar-content');
            
            // Apply manual replacement
            avatarContainers.forEach(function(container) {
                const iconElement = container.querySelector('[data-feather]');
                if (iconElement) {
                    const iconName = iconElement.getAttribute('data-feather');
                    if (iconSvgs[iconName]) {
                        container.innerHTML = iconSvgs[iconName];
                    }
                }
            });
        }
    </script>
</body>
</html>