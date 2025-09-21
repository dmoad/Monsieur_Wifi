<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="monsieur-wifi - Network Analytics Dashboard">
    <meta name="keywords" content="wifi, analytics, dashboard, network, monitoring">
    <meta name="author" content="monsieur-wifi">
    <title>Analytics - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/tables/datatable/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../../../assets/css/style.css">
    <!-- END: Custom CSS-->

    <!-- Ensure these are included at the top of your head section -->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/charts/chart-apex.css">
    <script src="app-assets/vendors/js/jquery/jquery.min.js"></script>
    <script src="app-assets/vendors/js/charts/apexcharts.min.js"></script>

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
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/locations">
                        <i data-feather="map-pin"></i>
                        <span class="menu-title text-truncate">Locations</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                            <h2 class="content-header-title float-left mb-0">Analytics</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Home</a></li>
                                    <li class="breadcrumb-item active">Analytics</li>
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
                <!-- Date Picker -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    
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

                <!-- Stats Cards - Redesigned with interactive elements and modern styling -->
                <div class="row match-height">
                    <!-- Total Users Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card border-left-primary position-relative overflow-hidden">
                            <div class="bg-light-primary position-absolute rounded-circle d-none d-md-block" style="width:150px;height:150px;right:-50px;top:-50px;opacity:0.1;"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                        <h5 class="text-muted">Total Users</h5>
                                        <h2 class="font-weight-bolder mb-0">26,759</h2>
                                </div>
                                    <div class="avatar bg-light-primary rounded p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <div id="line-area-chart-1"></div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="d-flex align-items-center">
                                        <div class="badge badge-light-success p-75 rounded">
                                            <i data-feather="trending-up" class="font-medium-1"></i>
                                            <span class="font-weight-bold ml-25">+18.2%</span>
                                        </div>
                                    </div>
                                    <a href="#" class="font-small-3 text-muted">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Usage Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card border-left-warning position-relative overflow-hidden">
                            <div class="bg-light-warning position-absolute rounded-circle d-none d-md-block" style="width:150px;height:150px;right:-50px;top:-50px;opacity:0.1;"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                        <h5 class="text-muted">Data Usage</h5>
                                        <h2 class="font-weight-bolder mb-0">15.2 TB</h2>
                                </div>
                                    <div class="avatar bg-light-warning rounded p-50">
                                    <div class="avatar-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                    </div>
                                </div>
                            </div>
                                <div id="line-area-chart-4"></div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="d-flex align-items-center">
                                        <div class="badge badge-light-success p-75 rounded">
                                            <i data-feather="trending-up" class="font-medium-1"></i>
                                            <span class="font-weight-bold ml-25">+24.6%</span>
                                        </div>
                                    </div>
                                    <a href="#" class="font-small-3 text-muted">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Devices Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card border-left-success position-relative overflow-hidden">
                            <div class="bg-light-success position-absolute rounded-circle d-none d-md-block" style="width:150px;height:150px;right:-50px;top:-50px;opacity:0.1;"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <h5 class="text-muted">Active Devices</h5>
                                        <h2 class="font-weight-bolder mb-0">4,207</h2>
                                    </div>
                                    <div class="avatar bg-light-success rounded p-50">
                                        <div class="avatar-content">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                        </div>
                                    </div>
                                </div>
                                <div id="line-area-chart-2"></div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="d-flex align-items-center">
                                        <div class="badge badge-light-success p-75 rounded">
                                            <i data-feather="trending-up" class="font-medium-1"></i>
                                            <span class="font-weight-bold ml-25">+5.2%</span>
                                        </div>
                                    </div>
                                    <a href="#" class="font-small-3 text-muted">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card border-left-danger position-relative overflow-hidden">
                            <div class="bg-light-danger position-absolute rounded-circle d-none d-md-block" style="width:150px;height:150px;right:-50px;top:-50px;opacity:0.1;"></div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <h5 class="text-muted">Uptime</h5>
                                        <h2 class="font-weight-bolder mb-0">98.7%</h2>
                                    </div>
                                    <div class="avatar bg-light-danger rounded p-50">
                                        <div class="avatar-content">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                                <div id="line-area-chart-3"></div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <div class="d-flex align-items-center">
                                        <div class="badge badge-light-success p-75 rounded">
                                            <i data-feather="trending-up" class="font-medium-1"></i>
                                            <span class="font-weight-bold ml-25">+0.3%</span>
                                        </div>
                                    </div>
                                    <a href="#" class="font-small-3 text-muted">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traffic Analysis - Updated with improved design -->
                <div class="row match-height">
                    <div class="col-lg-8 col-12">
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
                                <div id="network-traffic-chart"></div>
                                <div class="row mt-3">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">8.5 TB</h4>
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
                                                <h4 class="font-weight-bolder mb-0">3.8 TB</h4>
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
                                                <h4 class="font-weight-bolder mb-0">22%</h4>
                                                <p class="card-text text-muted">Growth Rate</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h4 class="card-title mb-0">Network Health</h4>
                                    <small class="text-muted">Overall system performance</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h1 class="font-large-2 font-weight-bolder mb-0">98.7%</h1>
                                        <p class="card-text">System Uptime</p>
                                    </div>
                                    <div class="avatar bg-light-success p-75">
                                        <div class="avatar-content">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                                <div id="network-health-chart" class="mb-3"></div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <p class="card-text mb-0">Uptime Last 30 Days</p>
                                            <div class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success mr-50"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                <span class="font-weight-bold">98.7%</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-bar-success mb-2">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="98.7" aria-valuemin="0" aria-valuemax="100" style="width: 98.7%"></div>
                                        </div>
                                        <small class="text-muted">All systems operating normally</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="row match-height">
                    <div class="col-lg-7 col-12">
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
                                                <h4 class="font-weight-bolder mb-0">12,456</h4>
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
                                                <h4 class="font-weight-bolder mb-0">2,415</h4>
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
                                                <h4 class="font-weight-bolder mb-0">10,041</h4>
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
                                <div id="user-activity-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">User Retention</h4>
                                <div class="dropdown chart-dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="retention-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Last 7 Days
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="retention-dropdown">
                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="user-retention-chart"></div>
                                <div class="row text-center mt-2">
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">First-time Users</span>
                                            <h3 class="mb-0">19.4%</h3>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">Return Rate</span>
                                            <h3 class="mb-0">80.6%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Performance & Bandwidth Usage -->
                <div class="row match-height">
                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Location Performance</h4>
                                <div class="dropdown chart-dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="location-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Last 7 Days
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="location-dropdown">
                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Active Users</th>
                                                <th>Data Usage</th>
                                                <th>Uptime</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Paris Office</td>
                                                <td>845</td>
                                                <td>4.7 TB</td>
                                                <td>99.2%</td>
                                                <td><span class="badge badge-light-success">Healthy</span></td>
                                            </tr>
                                            <tr>
                                                <td>London HQ</td>
                                                <td>1250</td>
                                                <td>6.2 TB</td>
                                                <td>99.8%</td>
                                                <td><span class="badge badge-light-success">Healthy</span></td>
                                            </tr>
                                            <tr>
                                                <td>New York Office</td>
                                                <td>756</td>
                                                <td>3.8 TB</td>
                                                <td>98.4%</td>
                                                <td><span class="badge badge-light-success">Healthy</span></td>
                                            </tr>
                                            <tr>
                                                <td>Tokyo Branch</td>
                                                <td>432</td>
                                                <td>2.1 TB</td>
                                                <td>97.9%</td>
                                                <td><span class="badge badge-light-warning">Issues</span></td>
                                            </tr>
                                            <tr>
                                                <td>Sydney Office</td>
                                                <td>321</td>
                                                <td>1.5 TB</td>
                                                <td>96.5%</td>
                                                <td><span class="badge badge-light-warning">Issues</span></td>
                                            </tr>
                                            <tr>
                                                <td>Berlin Office</td>
                                                <td>250</td>
                                                <td>1.1 TB</td>
                                                <td>93.2%</td>
                                                <td><span class="badge badge-light-danger">Problems</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Bandwidth Analysis</h4>
                                <div class="dropdown chart-dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="bandwidth-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Last 7 Days
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="bandwidth-dropdown">
                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-success p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="arrow-down" class="font-medium-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">9.8 TB</h4>
                                                <p class="card-text">Download</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="arrow-up" class="font-medium-4"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">5.4 TB</h4>
                                                <p class="card-text">Upload</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="bandwidth-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Weekly & Monthly Reports -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Analytics Reports</h4>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="reportsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="download" class="mr-25"></i>Download
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="reportsDropdown">
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <i data-feather="file-text" class="mr-50"></i>Weekly Report
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <i data-feather="file-text" class="mr-50"></i>Monthly Report
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <i data-feather="file-text" class="mr-50"></i>Quarterly Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">Weekly Report</h4>
                                                <p class="card-text text-muted">Last updated: May 12, 2023</p>
                                            </div>
                                            <div class="avatar bg-light-primary p-50">
                                                <div class="avatar-content">
                                                    <i data-feather="calendar" class="font-medium-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p>Summary of network performance metrics and user engagement for the past week.</p>
                                        <button class="btn btn-primary">View Report</button>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h4 class="font-weight-bolder mb-0">Monthly Report</h4>
                                                <p class="card-text text-muted">Last updated: April 30, 2023</p>
                                            </div>
                                            <div class="avatar bg-light-warning p-50">
                                                <div class="avatar-content">
                                                    <i data-feather="trending-up" class="font-medium-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p>Comprehensive analysis of monthly trends, including bandwidth usage and user retention.</p>
                                        <button class="btn btn-primary">View Report</button>
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
            <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="#" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span>
            <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="../../../app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="../../../app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="../../../app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/cards/card-statistics.js"></script>
    <!-- END: Theme JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="assets/js/config.js"></script>
    
    <!-- BEGIN: Page JS-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM content loaded");

            // First, try to initialize Feather icons
            if (typeof feather !== 'undefined') {
                console.log("Initializing Feather icons");
                feather.replace({
                    width: 14,
                    height: 14
                });
            } else {
                console.error("Feather is not defined. Icons may not appear.");
            }
        });

        // Use window.onload to ensure all resources (images, styles, etc.) are loaded
        window.onload = function() {
            console.log("Window loaded - starting chart initialization");
            
            // Check if ApexCharts exists
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts is not loaded. Charts cannot be initialized.");
                
                // Try to load ApexCharts dynamically as a fallback
                var script = document.createElement('script');
                script.src = 'app-assets/vendors/js/charts/apexcharts.min.js';
                script.onload = initializeCharts;
                document.head.appendChild(script);
                return;
            }
            
            // If ApexCharts exists, initialize charts
            initializeCharts();
        };

        function initializeCharts() {
            console.log("Initializing charts");
            
            setTimeout(function() {
                try {
                    // Basic charts with minimal configurations
                    // Total Users Chart
                    if (document.querySelector("#total-users-chart")) {
                        var totalUsersChart = new ApexCharts(document.querySelector("#total-users-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#7367F0'],
                            series: [{ data: [18000, 19500, 20200, 19800, 22000, 23000, 24500, 25800, 26759] }]
                        });
                        totalUsersChart.render();
                        console.log("Total Users chart rendered");
                    }

                    // Data Usage Chart
                    if (document.querySelector("#data-usage-chart")) {
                        var dataUsageChart = new ApexCharts(document.querySelector("#data-usage-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#FF9F43'],
                            series: [{ data: [10.2, 11.5, 12.3, 12.8, 13.2, 13.9, 14.5, 15.0, 15.2] }]
                        });
                        dataUsageChart.render();
                        console.log("Data Usage chart rendered");
                    }

                    // Active Devices Chart
                    if (document.querySelector("#active-devices-chart")) {
                        var activeDevicesChart = new ApexCharts(document.querySelector("#active-devices-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#28C76F'],
                            series: [{ data: [3200, 3400, 3600, 3750, 3950, 4100, 4200, 4207] }]
                        });
                        activeDevicesChart.render();
                        console.log("Active Devices chart rendered");
                    }

                    // Uptime Chart
                    if (document.querySelector("#uptime-chart")) {
                        var uptimeChart = new ApexCharts(document.querySelector("#uptime-chart"), {
                            chart: { type: 'line', height: 100, sparkline: { enabled: true } },
                            colors: ['#EA5455'],
                            series: [{ data: [97.5, 98.0, 97.8, 98.2, 98.5, 98.7, 98.7, 98.7] }]
                        });
                        uptimeChart.render();
                        console.log("Uptime chart rendered");
                    }

                    // Network Traffic Chart
                    if (document.querySelector("#network-traffic-chart")) {
                        var networkTrafficChart = new ApexCharts(document.querySelector("#network-traffic-chart"), {
                            chart: { height: 350, type: 'area' },
                            colors: ['#7367F0', '#FF9F43'],
                            series: [
                                { name: 'Download', data: [28, 40, 36, 52, 38, 60, 55] },
                                { name: 'Upload', data: [14, 25, 20, 34, 28, 42, 46] }
                            ],
                            xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] }
                        });
                        networkTrafficChart.render();
                        console.log("Network Traffic chart rendered");
                    }

                    // Network Health Chart
                    if (document.querySelector("#network-health-chart")) {
                        var networkHealthChart = new ApexCharts(document.querySelector("#network-health-chart"), {
                            chart: { height: 250, type: 'radialBar' },
                            plotOptions: { radialBar: { hollow: { size: '70%' } } },
                            colors: ['#28C76F'],
                            series: [98.7],
                            labels: ['Network Uptime']
                        });
                        networkHealthChart.render();
                        console.log("Network Health chart rendered");
                    }

                    // User Activity Chart
                    if (document.querySelector("#user-activity-chart")) {
                        var userActivityChart = new ApexCharts(document.querySelector("#user-activity-chart"), {
                            chart: { height: 300, type: 'bar' },
                            colors: ['#7367F0', '#28C76F'],
                            series: [
                                { name: 'New Users', data: [450, 520, 380, 610, 780, 510, 620] },
                                { name: 'Returning Users', data: [720, 680, 820, 930, 850, 910, 940] }
                            ],
                            xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] }
                        });
                        userActivityChart.render();
                        console.log("User Activity chart rendered");
                    }

                    // User Retention Chart
                    if (document.querySelector("#user-retention-chart")) {
                        var userRetentionChart = new ApexCharts(document.querySelector("#user-retention-chart"), {
                            chart: { height: 300, type: 'donut' },
                            colors: ['#7367F0', '#28C76F'],
                            series: [19.4, 80.6],
                            labels: ['First-time', 'Returning']
                        });
                        userRetentionChart.render();
                        console.log("User Retention chart rendered");
                    }

                    // Bandwidth Chart
                    if (document.querySelector("#bandwidth-chart")) {
                        var bandwidthChart = new ApexCharts(document.querySelector("#bandwidth-chart"), {
                            chart: { height: 300, type: 'bar', stacked: true },
                            colors: ['#28C76F', '#FF9F43'],
                            series: [
                                { name: 'Download', data: [1.2, 1.4, 1.3, 1.8, 1.5, 1.6, 1.0] },
                                { name: 'Upload', data: [0.6, 0.7, 0.8, 0.9, 0.8, 0.9, 0.7] }
                            ],
                            xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] }
                        });
                        bandwidthChart.render();
                        console.log("Bandwidth chart rendered");
                    }

                    // Hourly Usage Chart
                    if (document.querySelector("#hourly-usage-chart")) {
                        var hourlyUsageChart = new ApexCharts(document.querySelector("#hourly-usage-chart"), {
                            chart: { height: 300, type: 'line' },
                            colors: ['#7367F0'],
                            series: [{
                                name: 'Active Users',
                                data: [120, 90, 70, 40, 30, 50, 100, 180, 250, 350, 480, 520, 650, 700, 680, 620, 580, 550, 520, 480, 420, 350, 280, 200]
                            }],
                            xaxis: {
                                categories: ['12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
                                    '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM']
                            }
                        });
                        hourlyUsageChart.render();
                        console.log("Hourly Usage chart rendered");
                    }

                    // Session Duration Chart
                    if (document.querySelector("#session-duration-chart")) {
                        var sessionDurationChart = new ApexCharts(document.querySelector("#session-duration-chart"), {
                            chart: { height: 300, type: 'pie' },
                            colors: ['#7367F0', '#28C76F', '#FF9F43'],
                            series: [28, 42, 30],
                            labels: ['< 5 Min', '5-30 Min', '> 30 Min']
                        });
                        sessionDurationChart.render();
                        console.log("Session Duration chart rendered");
                    }

                    // Initialize DataTable if available
                    if (typeof $.fn.DataTable !== 'undefined' && $('.table').length > 0) {
                        $('.table').DataTable({
                            responsive: true,
                            paging: false,
                            info: false, 
                            searching: false
                        });
                        console.log("DataTable initialized");
                    }

                    // Re-initialize Feather icons after all charts are rendered
                    if (typeof feather !== 'undefined') {
                        console.log("Re-initializing Feather icons after charts");
                        feather.replace({
                            width: 14,
                            height: 14
                        });
                    }
                } catch (error) {
                    console.error("Error during chart initialization:", error);
                }
            }, 500); // Small delay to ensure DOM is fully ready
        }
    </script>

    <!-- Also add this to fix icons specifically -->
    <script>
        // Additional icon fix - try to initialize feather icons multiple times
        // Sometimes timing issues can prevent proper icon rendering
        document.addEventListener('DOMContentLoaded', function() {
            // First attempt on DOMContentLoaded
            tryInitIcons();
            
            // Second attempt after a short delay
            setTimeout(tryInitIcons, 1000);
            
            // Third attempt after a longer delay
            setTimeout(tryInitIcons, 3000);
        });

        function tryInitIcons() {
            if (typeof feather !== 'undefined') {
                console.log("Attempting to initialize feather icons");
                feather.replace({
                    width: 14,
                    height: 14
                });
            } else {
                console.log("Feather not available for icon initialization");
                // Try to load feather.js dynamically if it's missing
                if (!document.querySelector('script[src*="feather"]')) {
                    var script = document.createElement('script');
                    script.src = 'app-assets/vendors/js/feather-icons/feather.min.js';
                    script.onload = function() {
                        if (typeof feather !== 'undefined') {
                            feather.replace({
                                width: 14,
                                height: 14
                            });
                        }
                    };
                    document.head.appendChild(script);
                }
            }
        }
    </script>

    <!-- Additional script for reliable Feather icon loading -->
    <script>
        // Enhanced icon initialization function
        document.addEventListener('DOMContentLoaded', function() {
            // Check if feather.js is loaded
            if (typeof feather === 'undefined') {
                console.log("Feather not loaded, loading it now...");
                // Load feather.js
                var featherScript = document.createElement('script');
                featherScript.src = '../../../app-assets/vendors/js/feather-icons/feather.min.js';
                featherScript.onload = function() {
                    console.log("Feather.js loaded successfully");
                    initializeFeatherIcons();
                };
                document.head.appendChild(featherScript);
            } else {
                initializeFeatherIcons();
            }
        });

        function initializeFeatherIcons() {
            console.log("Initializing Feather icons");
            // Try initializing with different approaches
            try {
                // Standard initialization
                feather.replace({
                    width: 20,  // Slightly larger size for better visibility
                    height: 20  // Consistent size for all icons
                });
                
                // For icons in avatars, apply specific sizes
                document.querySelectorAll('.avatar-content i').forEach(function(icon) {
                    icon.setAttribute('width', '24');
                    icon.setAttribute('height', '24');
                });
                
                console.log("Feather icons initialized successfully");
            } catch (error) {
                console.error("Error initializing Feather icons:", error);
            }
            
            // Schedule another attempt after a delay to catch any icons
            // that might have been added dynamically
            setTimeout(function() {
                try {
                    feather.replace();
                    console.log("Feather icons re-initialized after delay");
                } catch (error) {
                    console.error("Error in delayed icon initialization:", error);
                }
            }, 1000);
        }
        
        // Also add an observer to refresh icons when DOM changes
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
            
            // Start observing once the DOM is fully loaded
            window.addEventListener('load', function() {
                observer.observe(document.body, { 
                    childList: true, 
                    subtree: true 
                });
                console.log("MutationObserver started for Feather icons");
            });
        }
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
            
            // Get all avatar-content containers
            const avatarContainers = document.querySelectorAll('.avatar-content');
            
            avatarContainers.forEach(function(container) {
                // Find the data-feather element inside
                const iconElement = container.querySelector('[data-feather]');
                
                if (iconElement) {
                    // Get the icon name
                    const iconName = iconElement.getAttribute('data-feather');
                    
                    if (iconName) {
                        // Remove the existing element
                        container.innerHTML = '';
                        
                        // Create a new icon element
                        const newIcon = document.createElement('i');
                        newIcon.setAttribute('data-feather', iconName);
                        container.appendChild(newIcon);
                        
                        // Apply feather replacement directly
                        if (typeof feather !== 'undefined') {
                            feather.replace(newIcon, {
                                width: 24,
                                height: 24,
                                'stroke-width': 2
                            });
                        }
                    }
                }
            });
            
            // Backup approach: Use direct SVG replacement for specific icons if feather isn't working
            if (avatarContainers.length > 0 && !document.querySelector('.avatar-content svg')) {
                console.log("Fallback to manual SVG replacement");
                
                // Map of common icons used in your avatars
                const iconSvgs = {
                    'users': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                    'database': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',
                    'wifi': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>',
                    'activity': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>',
                    'arrow-down': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>',
                    'arrow-up': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>',
                    'trending-up': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>',
                    'check-circle': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                    'user-check': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>',
                    'user-plus': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>',
                    'repeat': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>',
                    'clock': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
                };
                
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
        });
    </script>
    </body>
</html>