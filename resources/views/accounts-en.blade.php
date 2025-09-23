<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - User account management for network administrators">
    <meta name="keywords" content="wifi, network, accounts, dashboard, administrator, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Accounts - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
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
        .badge-light-secondary {
            background-color: rgba(108, 117, 125, 0.12);
            color: #6c757d;
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
                        <a class="dropdown-item" href="/en/accounts" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/accounts" data-language="fr">
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
                </li> -->
                
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
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/locations">
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
                    <a class="d-flex align-items-center" href="/en/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Captive Portals</span>
                    </a>
                </li>
                
                <!-- For Admin Section -->
                <li class="navigation-header only_admin hidden"><span>For Admin</span></li>
                <li class="nav-item active only_admin hidden">
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
                                    <li class="breadcrumb-item"><a href="/en/dashboard">Home</a>
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
                                        <table class="datatables-accounts table" id="accounts-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Profile Picture</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
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
                <form action="#" id="add-account-form">
                <div class="modal-body">
                        <!-- Profile Picture Upload Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="media">
                                    <a href="javascript:void(0);" class="mr-25">
                                        <img src="/assets/avatar-default.jpg" id="new-account-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                    </a>
                                    <div class="media-body mt-75 ml-1">
                                        <label for="new-account-upload" class="btn btn-sm btn-primary mb-75 mr-75">Upload Profile Picture</label>
                                        <input type="file" id="new-account-upload" hidden accept="image/*" />
                                        <p class="mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-account-name" placeholder="Full Name" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="new-account-email" placeholder="Email" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-password">Password <span class="text-danger">*</span></label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="new-account-password" placeholder="Password" required />
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
                                    <label for="new-account-confirm-password">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="new-account-confirm-password" placeholder="Confirm Password" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-danger hidden" id="new-password-error-message">Passwords do not match</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="new-account-role" required>
                                        <option value="">Select Role</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="create-account-btn">Create Account</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade text-left" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editUserModalLabel">Edit User Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#" id="edit-user-form">
                <div class="modal-body">
                        <!-- Profile Picture Upload Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="media">
                                    <a href="javascript:void(0);" class="mr-25">
                                        <img src="/assets/avatar-default.jpg" id="edit-user-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                    </a>
                                    <div class="media-body mt-75 ml-1">
                                        <label for="edit-user-upload" class="btn btn-sm btn-primary mb-75 mr-75">Upload Profile Picture</label>
                                        <input type="file" id="edit-user-upload" hidden accept="image/*" />
                                        <p class="mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="edit-user-name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit-user-name" placeholder="Full Name" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="edit-user-email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="edit-user-email" placeholder="Email" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="edit-user-password">New Password</label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="edit-user-password" placeholder="Leave blank to keep current password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="edit-user-confirm-password">Confirm New Password</label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="edit-user-confirm-password" placeholder="Confirm new password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-danger hidden" id="edit-password-error-message">Passwords do not match</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="edit-user-role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit-user-role" required>
                                        <option value="">Select Role</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-user-btn">Update Account</button>
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
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/pages/app-user-list.js"></script>
    <!-- END: Page JS-->
     <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js?v=1"></script>

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
            var user = UserManager.getUser();
            // if role != admin, then redirect to dashboard
            if (user.role != 'admin') {
                window.location.href = '/en/dashboard';
                return;
            }
            // Initialize DataTable - moved to document ready to avoid conflicts
            
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

            // Initialize DataTable with proper check to avoid reinitialisation
            if (!$.fn.DataTable.isDataTable('#accounts-table')) {
                $('#accounts-table').DataTable({
                    responsive: true,
                    columnDefs: [
                        {
                            targets: [5], // Actions column (0-based index: ID=0, Name=1, Email=2, Role=3, Profile=4, Actions=5)
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
            }

            // Function to load users data into the table
            function loadUsersData() {
                $.ajax({
                url: '/api/accounts/users',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        var users = response.users;
                        var table = $('#accounts-table').DataTable(); // Get the DataTable instance

                        // Clear existing rows if needed
                        table.clear();

                        // Loop through the users and add them to the table
                        for (var i = 0; i < users.length; i++) {
                            var id = i + 1; // Assuming you want to display a sequential ID
                            var name = users[i].name;
                            var email = users[i].email;
                            var role = users[i].role || 'user';
                            var profile_picture = users[i].profile_picture;
                            var profile_picture_path = '/uploads/profile_pictures/' + profile_picture;
                            if (profile_picture === null) {
                                profile_picture_path = '/assets/avatar-default.jpg';
                                profile_picture = `<img src="/assets/avatar-default.jpg" alt="Profile Picture" class="img-fluid" style="width: 50px; height: 50px;">`;
                            }else{
                                profile_picture_path = '/uploads/profile_pictures/' + profile_picture;
                                profile_picture = `<img src="/uploads/profile_pictures/${profile_picture}" alt="Profile Picture" class="img-fluid" style="width: 50px; height: 50px;">`;
                            }
                            
                            // Create role badge
                            var roleBadge = role === 'admin' 
                                ? `<span class="badge badge-role-admin">Admin</span>`
                                : `<span class="badge badge-light-secondary">User</span>`;
                            
                            var userId = users[i].id;
                            var actions = `<button class="btn btn-sm btn-primary edit-user-btn" data-user-id="${userId}" data-name="${name}" data-email="${email}" data-role="${role}" data-profile-picture="${profile_picture_path}">
                                              <i data-feather="edit-2"></i> Edit
                                           </button> 
                                           <button class="btn btn-sm btn-danger delete-user-btn" data-user-id="${userId}">
                                              <i data-feather="trash-2"></i> Delete
                                           </button>`;
                            
                            // Log the data being added
                            console.log("Adding row:", [id, name, email, roleBadge, profile_picture, actions]);

                            // Add the new row to the DataTable
                            table.row.add([id, name, email, roleBadge, profile_picture, actions]).draw();
                        }

                        // Update the total accounts count
                        $('#total-accounts').text(response.total);
                        
                        // Replace feather icons in the action buttons
                        feather.replace();
                    } else {
                        alert('Failed to fetch users');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
            }

            // Load users data initially
            loadUsersData();

            // Handle profile picture upload for new account
            $('#new-account-upload').on('change', function() {
                const file = $(this).prop('files')[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        toastr.error('Please select a valid image file (JPG or PNG)', 'Invalid File');
                        return;
                    }
                    
                    // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('File size must be less than 2MB', 'File Too Large');
                        return;
                    }
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#new-account-upload-img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle add account form submission
            $('#add-account-form').on('submit', function(e) {
                e.preventDefault();
                
                const name = $('#new-account-name').val();
                const email = $('#new-account-email').val();
                const password = $('#new-account-password').val();
                const confirmPassword = $('#new-account-confirm-password').val();
                const role = $('#new-account-role').val();
                
                // Validate required fields
                if (!name || !email || !password || !confirmPassword || !role) {
                    toastr.error('Please fill in all required fields', 'Validation Error');
                    return;
                }
                
                // Validate password match
                if (password !== confirmPassword) {
                    $('#new-password-error-message').removeClass('hidden');
                    setTimeout(function() {
                        $('#new-password-error-message').addClass('hidden');
                    }, 3000);
                    return;
                } else {
                    $('#new-password-error-message').addClass('hidden');
                }
                
                // Show loading state
                const $button = $('#create-account-btn');
                const originalText = $button.html();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...').prop('disabled', true);
                
                // Prepare data
                const userData = {
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: confirmPassword,
                    role: role
                };
                
                // Create user via API
                $.ajax({
                    url: '/api/auth/register',
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(userData),
                    success: function(response) {
                        console.log('User created successfully:', response);
                        
                        // If profile picture is selected, upload it
                        const profileFile = $('#new-account-upload').prop('files')[0];
                        if (profileFile && response.user) {
                            // Upload profile picture for the newly created user
                            const formData = new FormData();
                            formData.append('file', profileFile);
                            formData.append('user_id', response.user.id);
                            
                            $.ajax({
                                url: '/api/auth/upload-profile-picture',
                                type: 'POST',
                                data: formData,
                                headers: {
                                    'Authorization': 'Bearer ' + response.access_token
                                },
                                processData: false,
                                contentType: false,
                                success: function() {
                                    console.log('Profile picture uploaded successfully');
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error uploading profile picture:', error);
                                }
                            });
                        }
                        
                        // Reset form and close modal
                        $('#add-account-form')[0].reset();
                        $('#new-account-upload-img').attr('src', '/assets/avatar-default.jpg');
                        $('#add-new-account').modal('hide');
                        
                        // Show success message
                        toastr.success('Account created successfully!', 'Success');
                        
                        // Refresh the users table data without reloading the page
                        loadUsersData();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error creating user:', error);
                        let errorMessage = 'Failed to create account. Please try again.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.email && xhr.responseJSON.email[0]) {
                                errorMessage = xhr.responseJSON.email[0];
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        
                        toastr.error(errorMessage, 'Error');
                    },
                    complete: function() {
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Handle edit user button clicks (using event delegation)
            $(document).on('click', '.edit-user-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('name');
                const userEmail = $(this).data('email');
                const userRole = $(this).data('role');
                const userProfilePicture = $(this).data('profile-picture');
                
                // Store the user ID for later use
                $('#edit-user-modal').data('user-id', userId);
                
                // Populate the form with current user data
                $('#edit-user-name').val(userName);
                $('#edit-user-email').val(userEmail);
                $('#edit-user-role').val(userRole);
                $('#edit-user-password').val('');
                $('#edit-user-confirm-password').val('');
                
                // Set profile picture
                if (userProfilePicture && userProfilePicture !== 'null' && userProfilePicture !== '') {
                    $('#edit-user-upload-img').attr('src', userProfilePicture);
                } else {
                    $('#edit-user-upload-img').attr('src', '/assets/avatar-default.jpg');
                }
                
                // Show the modal
                $('#edit-user-modal').modal('show');
            });

            // Handle profile picture upload for edit user
            $('#edit-user-upload').on('change', function() {
                const file = $(this).prop('files')[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        toastr.error('Please select a valid image file (JPG or PNG)', 'Invalid File');
                        return;
                    }
                    
                    // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('File size must be less than 2MB', 'File Too Large');
                        return;
                    }
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#edit-user-upload-img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle edit user form submission
            $('#edit-user-form').on('submit', function(e) {
                e.preventDefault();
                
                const userId = $('#edit-user-modal').data('user-id');
                const name = $('#edit-user-name').val();
                const email = $('#edit-user-email').val();
                const role = $('#edit-user-role').val();
                const password = $('#edit-user-password').val();
                const confirmPassword = $('#edit-user-confirm-password').val();
                
                // Validate required fields
                if (!name || !email || !role) {
                    toastr.error('Please fill in all required fields', 'Validation Error');
                    return;
                }
                
                // Validate password match if password is provided
                if (password && password !== confirmPassword) {
                    $('#edit-password-error-message').removeClass('hidden');
                    setTimeout(function() {
                        $('#edit-password-error-message').addClass('hidden');
                    }, 3000);
                    return;
                } else {
                    $('#edit-password-error-message').addClass('hidden');
                }
                
                // Show loading state
                const $button = $('#update-user-btn');
                const originalText = $button.html();
                $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').prop('disabled', true);
                
                // Prepare data
                const userData = {
                    name: name,
                    email: email,
                    role: role
                };
                
                // Only include password if it's provided
                if (password && password.trim() !== '') {
                    userData.password = password;
                    userData.confirm_password = confirmPassword;
                }
                
                // Update user via API
                $.ajax({
                    url: `/api/accounts/users/${userId}`,
                    type: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(userData),
                    success: function(response) {
                        console.log('User updated successfully:', response);
                        
                        // If profile picture is selected, upload it
                        const profileFile = $('#edit-user-upload').prop('files')[0];
                        if (profileFile) {
                            const formData = new FormData();
                            formData.append('file', profileFile);
                            formData.append('user_id', userId); // Pass the user ID for admin upload
                            
                            $.ajax({
                                url: '/api/auth/upload-profile-picture',
                                type: 'POST',
                                data: formData,
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                processData: false,
                                contentType: false,
                                success: function() {
                                    console.log('Profile picture uploaded successfully');
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error uploading profile picture:', error);
                                    toastr.warning('User updated but profile picture upload failed', 'Partial Success');
                                }
                            });
                        }
                        
                        // Reset form and close modal
                        $('#edit-user-form')[0].reset();
                        $('#edit-user-modal').modal('hide');
                        
                        // Show success message
                        toastr.success('User account updated successfully!', 'Success');
                        
                        // Refresh the users table data without reloading the page
                        loadUsersData();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating user:', error);
                        let errorMessage = 'Failed to update user account. Please try again.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.email && xhr.responseJSON.email[0]) {
                                errorMessage = xhr.responseJSON.email[0];
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        
                        toastr.error(errorMessage, 'Error');
                    },
                    complete: function() {
                        // Reset button state
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Handle delete user button clicks (using event delegation)
            $(document).on('click', '.delete-user-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).closest('tr').find('td:nth-child(2)').text(); // Get name from table row
                
                // Show confirmation dialog
                if (confirm(`Are you sure you want to delete the user account for "${userName}"? This action cannot be undone.`)) {
                    // Show loading state on button
                    const $button = $(this);
                    const originalHtml = $button.html();
                    $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                    
                    // Delete user via API
                    $.ajax({
                        url: `/api/accounts/users/${userId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(response) {
                            console.log('User deleted successfully:', response);
                            
                            // Show success message
                            toastr.success(`User account for "${userName}" has been deleted successfully!`, 'User Deleted');
                            
                            // Refresh the users table data without reloading the page
                            loadUsersData();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting user:', error);
                            let errorMessage = 'Failed to delete user account. Please try again.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            toastr.error(errorMessage, 'Error');
                            
                            // Reset button state
                            $button.html(originalHtml).prop('disabled', false);
                        }
                    });
                }
            });

        });
    </script>
</body>
</html>