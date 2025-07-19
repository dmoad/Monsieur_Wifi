<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - User account management for network administrators">
    <meta name="keywords" content="wifi, network, accounts, dashboard, administrator, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Accounts - monsieur-wifi</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
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
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->
    
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

        /* Avatar styling */
        .avatar-sm {
            height: 32px;
            width: 32px;
        }

        /* Badge roles */
        .badge-role-admin {
            background-color: rgba(115, 103, 240, 0.12);
            color: #7367f0;
        }
        .badge-role-owner {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
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
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div><span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
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
                <!-- <li class="nav-item">
                    <a class="d-flex align-items-center" href="/analytics">
                        <i data-feather="bar-chart-2"></i>
                        <span class="menu-title text-truncate">Usage Analytics</span>
                    </a>
                </li> -->

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Captive Portals</span>
                    </a>
                </li>
                
                <!-- For Admin Section -->
                <li class="navigation-header"><span>For Admin</span></li>
                <li class="nav-item active">
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
                            <h2 class="content-header-title float-left mb-0">User Accounts</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Accounts
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
                    <div class="form-group breadcrumb-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-account">
                            <i data-feather="user-plus" class="mr-25"></i>
                            <span>Add New Account</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Statistics Cards -->
                    <div class="row">
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-primary p-50 mb-1">
                                        <div class="avatar-content">
                                        <i data-feather="users"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder" id="total-accounts">12</h2>
                                <p class="card-text">Total Accounts</p>
                            </div>
                        </div>
                                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-info p-50 mb-1">
                                        <div class="avatar-content">
                                        <i data-feather="user-check"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder" id="active-accounts">10</h2>
                                <p class="card-text">Active Accounts</p>
                            </div>
                        </div>
                                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-danger p-50 mb-1">
                                        <div class="avatar-content">
                                        <i data-feather="user-x"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder" id="inactive-accounts">2</h2>
                                <p class="card-text">Inactive Accounts</p>
                            </div>
                        </div>
                                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-warning p-50 mb-1">
                                        <div class="avatar-content">
                                        <i data-feather="shield"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder" id="admin-accounts">4</h2>
                                <p class="card-text">Admin Accounts</p>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Accounts Table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">All User Accounts</h4>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable table-responsive">
                                        <table class="datatables-accounts table">
                                        <thead>
                                            <tr>
                                                    <th>User</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                <th>Status</th>
                                                    <th>Company</th>
                                                    <th>Last Login</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                            <div class="avatar mr-1">
                                                                <img src="app-assets/images/portrait/small/avatar-s-11.jpg" alt="Avatar" width="32" height="32">
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold">John Smith</div>
                                                                <div class="small text-truncate text-muted">@jsmith</div>
                                                            </div>
                                                    </div>
                                                </td>
                                                    <td>john.smith@mrwifi.com</td>
                                                    <td><span class="badge badge-pill badge-role-admin">Administrator</span></td>
                                                    <td><span class="badge badge-pill badge-light-success">Active</span></td>
                                                    <td>monsieur-wifi Networks</td>
                                                    <td>Today at 10:30 AM</td>
                                                    <td>
                                                        <a href="profile.html" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                            <div class="avatar mr-1">
                                                                <img src="app-assets/images/portrait/small/avatar-s-3.jpg" alt="Avatar" width="32" height="32">
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold">Sarah Johnson</div>
                                                                <div class="small text-truncate text-muted">@sjohnson</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                    <td>sarah.j@mrwifi.com</td>
                                                    <td><span class="badge badge-pill badge-role-owner">Network Owner</span></td>
                                                    <td><span class="badge badge-pill badge-light-success">Active</span></td>
                                                    <td>Downtown Cafe</td>
                                                    <td>Yesterday at 5:25 PM</td>
                                                    <td>
                                                        <a href="profile.html" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                            <div class="avatar mr-1">
                                                                <img src="app-assets/images/portrait/small/avatar-s-4.jpg" alt="Avatar" width="32" height="32">
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold">Michael Chen</div>
                                                                <div class="small text-truncate text-muted">@mchen</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                    <td>m.chen@mrwifi.com</td>
                                                    <td><span class="badge badge-pill badge-role-admin">Administrator</span></td>
                                                    <td><span class="badge badge-pill badge-light-success">Active</span></td>
                                                    <td>monsieur-wifi Networks</td>
                                                    <td>Jan 12, 2025</td>
                                                    <td>
                                                        <a href="profile.html" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                            <div class="avatar mr-1">
                                                                <img src="app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" width="32" height="32">
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold">Robert Wilson</div>
                                                                <div class="small text-truncate text-muted">@rwilson</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                    <td>r.wilson@mrwifi.com</td>
                                                    <td><span class="badge badge-pill badge-role-owner">Network Owner</span></td>
                                                    <td><span class="badge badge-pill badge-light-danger">Inactive</span></td>
                                                    <td>Wilson Hotels</td>
                                                    <td>Dec 25, 2024</td>
                                                    <td>
                                                        <a href="profile.html" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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

    <!-- Add New Account Modal -->
    <div class="modal fade text-left" id="add-new-account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add New Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" class="form-control" id="firstname" placeholder="First Name" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" placeholder="Last Name" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" placeholder="Username" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Email" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                        <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="password" placeholder="Password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Minimum 8 characters, must include letters, numbers and special characters</small>
                                </div>
                        </div>
                            <div class="col-12 col-sm-6">
                        <div class="form-group">
                                    <label for="confirm-password">Confirm Password</label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="confirm-password" placeholder="Confirm Password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                        </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role">
                                        <option value="">Select Role</option>
                                        <option value="admin">Administrator</option>
                                        <option value="owner">Network Owner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <input type="text" class="form-control" id="company" placeholder="Company Name" />
                            </div>
                        </div>
                            <div class="col-12 col-sm-6">
                        <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" placeholder="Phone Number" />
                                </div>
                        </div>
                            <div class="col-12 col-sm-6">
                        <div class="form-group">
                                    <label>Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" checked />
                                        <label class="custom-control-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="#" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span>
            <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span>
        </p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/app-user-list.js"></script>
    <!-- END: Page JS-->
     <!-- Include config.js before other custom scripts -->
    <script src="assets/js/config.js"></script>

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
                
                // Fix for avatar container icons
                $('.avatar-icon').each(function() {
                    $(this).css({
                        'width': '24px',
                        'height': '24px'
                    });
                });
            }

            // Initialize DataTable
            $('.datatables-accounts').DataTable({
                responsive: true,
                columnDefs: [
                    {
                        targets: [6],
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
            
            // Initialize role select2
            if ($.fn.select2) {
                $('#role').select2({
                    dropdownParent: $('#add-new-account'),
                    minimumResultsForSearch: Infinity
                });
            }
            
            // Toggle password visibility
            $('.form-password-toggle .input-group-text').on('click', function() {
                var $this = $(this),
                    inputGroupText = $this.closest('.form-password-toggle'),
                    formPasswordToggleIcon = $this.find('i'),
                    formPasswordToggleInput = inputGroupText.parent().find('input');

                if (formPasswordToggleInput.attr('type') === 'text') {
                    formPasswordToggleInput.attr('type', 'password');
                    if (feather) {
                        formPasswordToggleIcon.replaceWith(feather.icons.eye.toSvg({ class: 'font-small-4' }));
                    }
                } else if (formPasswordToggleInput.attr('type') === 'password') {
                    formPasswordToggleInput.attr('type', 'text');
                    if (feather) {
                        formPasswordToggleIcon.replaceWith(feather.icons['eye-off'].toSvg({ class: 'font-small-4' }));
                    }
                }
            });
            
            // Update status text when toggle is clicked
            $('#status').on('change', function() {
                $(this).next('label').text($(this).prop('checked') ? 'Active' : 'Inactive');
            });
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
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);

        });
    </script>
</body>
</html>