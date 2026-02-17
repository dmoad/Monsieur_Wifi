<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Bloom WiFi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, bloom">
    <meta name="author" content="Monsieur WiFi">
    <title>Locations - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/dashboard-ecommerce.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <!-- Ensure proper loading of Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>

    <!-- BEGIN: Page Vendor JS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>

    <!-- First, add this in the head section -->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">

    <!-- Add this CSS in the head section -->
    <style>
    .dataTables_paginate {
        margin-top: 1rem !important;
        padding: 1rem !important;
    }

    .pagination {
        display: flex;
        justify-content: flex-end;
    }

    .page-link {
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        border: 1px solid #ddd;
        color: #7367f0;
    }

    .page-item.active .page-link {
        background-color: #7367f0;
        border-color: #7367f0;
        color: #fff;
    }

    .page-item.disabled .page-link {
        color: #b9b9c3;
        pointer-events: none;
        background-color: #fff;
        border-color: #ddd;
    }
    
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
    
    /* Fix for marker icons */
    .marker-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Ensure proper loading of Leaflet map */
    .leaflet-map {
        z-index: 1;
    }
    
    /* Fix for potential CSS conflicts */
    .leaflet-container {
        font-family: inherit;
        font-size: inherit;
    }
    
    /* Ensure proper popup styling */
    .leaflet-popup-content {
        margin: 0;
        padding: 0;
    }
    
    /* Fix for potential icon issues */
    .feather {
        height: 14px;
        width: 14px;
        display: inline-block;
        vertical-align: middle;
    }
    
    /* Ensure proper marker styling */
    .custom-div-icon, .marker-icon {
        background: transparent;
        border: none;
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
                        <a class="dropdown-item" href="/en/locations" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/locations" data-language="fr">
                            <i class="flag-icon flag-icon-fr"></i> Français
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
                    </li>
                 -->
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div><span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/en/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
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
                    <a class="d-flex align-items-center" href="/en/dashboard">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/en/locations">
                        <i data-feather="map-pin"></i>
                        <span class="menu-title text-truncate">Locations</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/analytics">
                        <i data-feather="bar-chart-2"></i>
                        <span class="menu-title text-truncate">Usage Analytics</span>
                    </a>
                </li> -->

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Captive Portals</span>
                    </a>
                </li>
                
                <!-- For Admin Section -->
                <li class="navigation-header only_admin hidden"><span>For Admin</span></li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/accounts">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Accounts</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/domain-blocking">
                        <i data-feather="slash"></i>
                        <span class="menu-title text-truncate">Domain Blocking</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/firmware">
                        <i data-feather="download"></i>
                        <span class="menu-title text-truncate">Firmware</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/system-settings">
                        <i data-feather="settings"></i>
                        <span class="menu-title text-truncate">System Settings</span>
                    </a>
                </li>
                <!-- Account Section -->
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/en/profile">
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
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Locations</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/en/dashboard">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Locations
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#add-location-modal">
                            <i data-feather="plus" class="mr-50"></i>
                            <span>Add Location</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Locations Content Starts -->
                <section id="locations-content">
                    <!-- Locations Stats -->
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0" id="total-locations"></h2>
                                        <p class="card-text">Total Locations</p>
                                    </div>
                                    <div class="avatar bg-light-primary p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                                        <div class="avatar-content">
                                            <i data-feather="map-pin" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0" id="online-locations"></h2>
                                        <p class="card-text">Online Locations</p>
                                    </div>
                                    <div class="avatar bg-light-success p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                                        <div class="avatar-content">
                                            <i data-feather="check-circle" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0" id="total-users"></h2>
                                        <p class="card-text">Total Users</p>
                                    </div>
                                    <div class="avatar bg-light-info p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0" id="total-data"></h2>
                                        <p class="card-text">Total Data Usage</p>
                                    </div>
                                    <div class="avatar bg-light-warning p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                                        <div class="avatar-content">
                                            <i data-feather="download" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Locations Table -->
                    <div class="row" id="table-hover-row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Locations List</h4>
                                    <div class="d-flex align-items-center">
                                        <div class="form-group mb-0 mr-1">
                                            <select class="form-control" id="status-filter" disabled>
                                                <option value="">All Status</option>
                                                <option value="Online">Online</option>
                                                <option value="Offline">Offline</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" id="search-locations" placeholder="Search locations...">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="locations-table">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Address</th>
                                                
                                                <th>Users</th>
                                                <th>Data Usage</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="locations-table-body">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Locations Content Ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- Add Location Modal -->
    <div class="modal fade" id="add-location-modal" tabindex="-1" role="dialog" aria-labelledby="add-location-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-location-title">Add New Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-location-form">
                        <div class="form-group">
                            <label for="location-name">Location Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location-name" placeholder="Enter location name">
                        </div>
                        <div class="form-group">
                            <label for="location-address">Address</label>
                            <input type="text" class="form-control" id="location-address" placeholder="Enter address">
                        </div>
                        <!-- 
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="location-latitude">Latitude</label>
                                <input type="text" class="form-control" id="location-latitude" placeholder="Enter latitude">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location-longitude">Longitude</label>
                                <input type="text" class="form-control" id="location-longitude" placeholder="Enter longitude">
                            </div>
                        </div> -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="location-router">Router MAC Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location-router" placeholder="Format: 00:11:22:33:44:55">
                                <small class="form-text text-muted">This will be used to create a new device for this location.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="device-model">Device Model <span class="text-danger">*</span></label>
                                <select class="form-control" id="device-model" required>
                                    <option value="">Select Device Model</option>
                                    <option value="820AX">820AX</option>
                                    <option value="835AX">835AX</option>
                                </select>
                                <small class="form-text text-muted">Select the model of your WiFi device.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="location-notes">Description</label>
                            <textarea class="form-control" id="location-notes" rows="3" placeholder="Enter additional notes or description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="add-location-btn">Add Location</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="https://mrwifi.com" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/tables/table-datatables-basic.js"></script>
    <!-- END: Page JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js?v=1"></script>

    <!-- Include config.js before other custom scripts -->
    
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

            // Initialize DataTable
            $('.datatables-basic').DataTable({
                responsive: true,
                columnDefs: [
                    {
                        targets: [6],
                        orderable: false
                    }
                ]
            });

            // Initialize Map
            var locationsMap = L.map('locations-map').setView([46.2276, 2.2137], 6);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(locationsMap);

            // Define marker icons
            var onlineIcon = L.icon({
                iconUrl: 'app-assets/images/map/marker-green.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });

            var offlineIcon = L.icon({
                iconUrl: 'app-assets/images/map/marker-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });

            // Add fullscreen button functionality
            document.getElementById('fullscreen-map').addEventListener('click', function() {
                var mapContainer = document.getElementById('locations-map-card');
                if (mapContainer.classList.contains('fullscreen')) {
                    mapContainer.classList.remove('fullscreen');
                    document.getElementById('fullscreen-map').innerHTML = '<i data-feather="maximize-2"></i>';
                    feather.replace();
                } else {
                    mapContainer.classList.add('fullscreen');
                    document.getElementById('fullscreen-map').innerHTML = '<i data-feather="minimize-2"></i>';
                    feather.replace();
                }
                setTimeout(function() {
                    locationsMap.invalidateSize();
                }, 300);
            });

            // Update statistics
            document.getElementById('total-locations').innerText = locations.length;
            document.getElementById('online-locations').innerText = locations.filter(loc => loc.status === "online").length;
            
            var totalUsers = locations.reduce((sum, loc) => sum + loc.users, 0);
            document.getElementById('total-users').innerText = totalUsers;
            
            var totalData = locations.reduce((sum, loc) => {
                return sum + parseFloat(loc.data.replace('TB', ''));
            }, 0);
            document.getElementById('total-data').innerText = totalData.toFixed(1) + 'TB';
        });

        $(document).ready(function() {
            // Check if user is logged in using UserManager from config.js
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            
            if (!token || !user) {
                // No token or user found, redirect to login page
                window.location.href = '/';
                return;
            }

            console.log("token: " + token);
            
            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            
            // Check if user has admin role for location creation
            // if (!UserManager.hasRole('admin')) {
            //     $('#add-location-btn').hide();
            // }

            // Make API call to get locations
            $.ajax({
                url: APP_CONFIG.API.BASE_URL + '/locations',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    var locations = response.locations;
                    var networkTotals = response.network_totals;
                    console.log("Locations list response: ", locations);
                    console.log("Network totals: ", networkTotals);
                    
                    var total_locations = locations.length;
                    var online_locations = locations.filter(loc => loc.online_status == "online").length;
                    var total_users = locations.reduce((sum, loc) => sum + loc.users, 0);
                    var total_data_gb = networkTotals.total_data_gb;
                    var total_data_formatted = total_data_gb > 1024 ? 
                        (total_data_gb / 1024).toFixed(1) + 'TB' : 
                        total_data_gb.toFixed(1) + 'GB';

                    $("#total-locations").text(total_locations);
                    $("#online-locations").text(online_locations);
                    $("#total-users").text(total_users);
                    $("#total-data").text(total_data_formatted);
                    // Populate table with locations data
                    var table_content = "";
                    locations.forEach(function(location) {
                        table_content += '<tr>';
                        table_content += '<td><div class="d-flex align-items-center"><div class="avatar bg-light-primary mr-1"><div class="avatar-content"><img src="/assets/map-icon-1.png" alt="Marker Icon" width="40" height="40"></div></div><span>' + location.name + '</span></div></td>';
                        table_content += '<td>' + location.address + '</td>';
                        table_content += '<td>' + location.users + '</td>';
                        table_content += '<td>' + location.data_usage + '</td>';
                        if (location.online_status == "online") {   
                            table_content += '<td><span class="badge badge-pill badge-light-success">Online</span></td>';
                        } else {
                            table_content += '<td><span class="badge badge-pill badge-light-danger">Offline</span></td>';
                        }
                        table_content += '<td><a href="/en/locations/' + location.id + '" class="btn btn-sm btn-primary">View</a></td>';
                        table_content += '</tr>';
                    });
                    $('#locations-table-body').html(table_content);

                    // After your table content is added to the DOM
                    // Add this code to reinitialize Feather icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    
                    // Initialize or reinitialize DataTable after data is loaded
                    if ($.fn.DataTable.isDataTable('#locations-table')) {
                        // Destroy existing DataTable instance
                        $('#locations-table').DataTable().destroy();
                    }
                    
                    // Initialize DataTable with data
                    $('#locations-table').DataTable({
                        responsive: true,
                        columnDefs: [
                            {
                                targets: [5], // Actions column (index 5, not 6)
                                orderable: false
                            }
                        ],
                        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                        language: {
                            paginate: {
                                previous: '&nbsp;',
                                next: '&nbsp;'
                            }
                        }
                    });
                    
                    // Enable status filter
                    $('#status-filter').prop('disabled', false);
                    // set value of status filter to ""
                    $('#status-filter').val("");
                    
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching locations:', error);
                    // redirect to login page if Unauthorized
                    if (xhr.status === 401) {
                        window.location.href = '/';
                    }
                }
            });
            
            // Handle add location form submission
            $('#add-location-btn').on('click', function(e) {
                e.preventDefault();
                
                // Show loading state
                $(this).html('<i data-feather="loader" class="mr-2"></i>Adding Location...');
                $(this).prop('disabled', true);
                
                // Clear previous error messages
                $('.form-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                
                // Get form data
                const locationData = {
                    name: $('#location-name').val(),
                    address: $('#location-address').val(),
                    mac_address: $('#location-router').val(), // This will be used to create the device
                    model: $('#device-model').val(), // Device model selection
                    description: $('#location-notes').val()
                };

                // Validate required fields
                let hasErrors = false;
                if (!locationData.name) {
                    showFieldError('location-name', 'Location name is required');
                    hasErrors = true;
                }

                if (!locationData.mac_address) {
                    showFieldError('location-router', 'Router MAC address is required');
                    hasErrors = true;
                } else if (!isValidMacAddress(locationData.mac_address)) {
                    showFieldError('location-router', 'Please enter a valid MAC address (e.g., 00:11:22:33:44:55)');
                    hasErrors = true;
                }

                if (!locationData.model) {
                    showFieldError('device-model', 'Device model is required');
                    hasErrors = true;
                }

                if (hasErrors) {
                    // Reset button state
                    submitBtn.html(originalBtnText);
                    submitBtn.attr('disabled', false);
                    return;
                }
                // alert(APP_CONFIG.API.BASE_URL + '/locations');
                // Make API call to create location using ApiService from config.js
                $.ajax({
                    url: APP_CONFIG.API.BASE_URL + '/locations',
                    type: 'POST',
                    data: locationData,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        console.log(response);
                        // Show success message on button
                        $('#add-location-btn').removeClass('btn-primary');
                        $('#add-location-btn').addClass('btn-success');
                        
                        let successMessage = 'Location created successfully';
                        if (response.firmware) {
                            successMessage += `<br><small>Assigned firmware: ${response.firmware.name}</small>`;
                        }

                        $('#add-location-btn').html(successMessage);
                        $('#add-location-btn').prop('disabled', true);
                        
                        // Hide modal after 4 seconds (increased to allow reading firmware info)
                        setTimeout(function() {
                            $('#add-location-btn').removeClass('btn-success');
                            $('#add-location-btn').addClass('btn-primary');
                            $('#add-location-btn').html('Add Location');
                            $('#add-location-btn').prop('disabled', false);
                            $('#add-location-modal').modal('hide');
                            
                            // Clear form
                            $('#add-location-form')[0].reset();
                            $('.form-error').remove();
                            $('.is-invalid').removeClass('is-invalid');
                            
                            // Reload page to show new location
                            window.location.reload();
                        }, 4000);
                        
                        // alert('Location created successfully');
                    },
                    error: function(xhr, status, error) {
                        // Show error message on button
                        $('#add-location-btn').removeClass('btn-primary');
                        $('#add-location-btn').addClass('btn-danger');
                        $('#add-location-btn').html('Error creating location');
                        $('#add-location-btn').prop('disabled', true);

                        setTimeout(function() {
                            $('#add-location-btn').removeClass('btn-danger');
                            $('#add-location-btn').addClass('btn-primary');
                            $('#add-location-btn').html('Add Location');
                            $('#add-location-btn').prop('disabled', false);
                        }, 3000);
                        // alert('Error creating location: ' + error);
                        console.error('Error creating location:', error);
                    }
                });
            });
            
            // Attach submit handler to the Add Location button in modal footer
            // $('.modal-footer .btn-primary').on('click', function() {
            //     $('#add-location-form').submit();
            // });
            
            // Helper function to show field errors
            function showFieldError(fieldId, message) {
                $(`#${fieldId}`)
                    .addClass('is-invalid')
                    .after(`<div class="invalid-feedback form-error">${message}</div>`);
            }
            
            // Helper function to validate MAC address format
            function isValidMacAddress(mac) {
                return /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(mac);
            }
        });

        $('#status-filter').on('change', function() {
            var status = $(this).val();
            var table = $('#locations-table').DataTable();
            
            // Use DataTable's search API instead of jQuery show/hide
            if (status !== '') {
                // Search in the Status column (index 4)
                table.column(4).search(status).draw();
            } else {
                // Clear the search to show all rows
                table.column(4).search('').draw();
            }
        });
    </script>
</body>
</html>