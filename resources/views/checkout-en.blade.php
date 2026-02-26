<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Checkout - Monsieur WiFi</title>
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" href="/app-assets/css/colors.css">
    <link rel="stylesheet" href="/app-assets/css/components.css">
    <link rel="stylesheet" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" href="/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" href="/app-assets/css/themes/semi-dark-layout.css">
    <link rel="stylesheet" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
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
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="/en/checkout"><i class="flag-icon flag-icon-us"></i> English</a>
                        <a class="dropdown-item" href="/fr/commander"><i class="flag-icon flag-icon-fr"></i> Français</a>
                    </div>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a>
                </li>
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                        <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="/en/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item logout-button" href="/logout"><i class="mr-50" data-feather="power"></i> Logout</a>
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
                    <a class="navbar-brand" href="/en/dashboard">
                        <span class="brand-logo"><img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="logo"></span>
                        <h2 class="brand-text">monsieur-wifi</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="navigation-header"><span>Management</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">Locations</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">Captive Portals</span></a>
                </li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/en/shop"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">Shop</span></a>
                </li>
                
                <li class="navigation-header only_admin hidden"><span>For Admin</span></li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">Accounts</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">Domain Blocking</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Firmware</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">System Settings</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/admin/orders"><i data-feather="package"></i><span class="menu-title text-truncate">Manage Orders</span></a>
                </li>
                
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/profile"><i data-feather="user"></i><span class="menu-title text-truncate">Profile</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center logout-button" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">Logout</span></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Checkout</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                                    <li class="breadcrumb-item"><a href="/en/cart">Cart</a></li>
                                    <li class="breadcrumb-item active">Checkout</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Shipping Information</h4>
                            </div>
                            <div class="card-body">
                                <form id="checkout-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_first_name">First Name *</label>
                                                <input type="text" class="form-control" id="shipping_first_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_last_name">Last Name *</label>
                                                <input type="text" class="form-control" id="shipping_last_name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_company">Company</label>
                                        <input type="text" class="form-control" id="shipping_company">
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_address_line1">Address Line 1 *</label>
                                        <input type="text" class="form-control" id="shipping_address_line1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_address_line2">Address Line 2</label>
                                        <input type="text" class="form-control" id="shipping_address_line2">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_city">City *</label>
                                                <input type="text" class="form-control" id="shipping_city" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_province">Province *</label>
                                                <input type="text" class="form-control" id="shipping_province" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_postal_code">Postal Code *</label>
                                                <input type="text" class="form-control" id="shipping_postal_code" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_country">Country *</label>
                                                <input type="text" class="form-control" id="shipping_country" value="Canada" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="shipping_phone">Phone *</label>
                                        <input type="tel" class="form-control" id="shipping_phone" required>
                                    </div>
                                    
                                    <hr class="my-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="same_as_shipping" checked>
                                            <label class="custom-control-label" for="same_as_shipping">Billing address same as shipping</label>
                                        </div>
                                    </div>
                                    
                                    <div id="billing-section" style="display: none;">
                                        <h5 class="mt-3">Billing Information</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_first_name">First Name *</label>
                                                    <input type="text" class="form-control" id="billing_first_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_last_name">Last Name *</label>
                                                    <input type="text" class="form-control" id="billing_last_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="billing_company">Company</label>
                                            <input type="text" class="form-control" id="billing_company">
                                        </div>
                                        <div class="form-group">
                                            <label for="billing_address_line1">Address Line 1 *</label>
                                            <input type="text" class="form-control" id="billing_address_line1">
                                        </div>
                                        <div class="form-group">
                                            <label for="billing_address_line2">Address Line 2</label>
                                            <input type="text" class="form-control" id="billing_address_line2">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_city">City *</label>
                                                    <input type="text" class="form-control" id="billing_city">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_province">Province *</label>
                                                    <input type="text" class="form-control" id="billing_province">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_postal_code">Postal Code *</label>
                                                    <input type="text" class="form-control" id="billing_postal_code">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing_country">Country *</label>
                                                    <input type="text" class="form-control" id="billing_country" value="Canada">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="billing_phone">Phone *</label>
                                            <input type="tel" class="form-control" id="billing_phone">
                                        </div>
                                    </div>
                                    
                                    <hr class="my-3">
                                    <h5>Shipping Method</h5>
                                    <div id="shipping-methods-loading">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </div>
                                    <div id="shipping-methods"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Order Summary</h4>
                            </div>
                            <div class="card-body">
                                <div id="order-items"></div>
                                <hr>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal:</span>
                                    <span id="order-subtotal">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Shipping:</span>
                                    <span id="order-shipping">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tax:</span>
                                    <span id="order-tax">$0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary" id="order-total">$0.00</strong>
                                </div>
                                <button type="submit" form="checkout-form" class="btn btn-primary btn-block" id="place-order-btn">
                                    Place Order
                                </button>
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

    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT © 2025<a class="ml-25" href="https://mrwifi.com" target="_blank">monsieur-wifi</a></span></p>
    </footer>

    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/assets/js/config.js"></script>
    <script src="/assets/js/checkout.js?v=<?php echo time() + 3; ?>"></script>
    <script>
        if (typeof feather !== 'undefined') feather.replace();
        
        // Authentication check and user display
        $(document).ready(function() {
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            
            if (!token || !user) {
                window.location.href = '/';
                return;
            }
            
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            
            $('.logout-button').on('click', function(e) {
                e.preventDefault();
                UserManager.logout(true);
            });
            
            if (UserManager.hasRole('admin')) {
                $('.only_admin').removeClass('hidden');
            }
        });
    </script>
</body>
</html>
