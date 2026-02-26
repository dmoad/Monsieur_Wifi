<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Product Details - Monsieur WiFi</title>
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
    
    <style>
        .product-main-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .thumbnail:hover, .thumbnail.active {
            border-color: #7367f0;
            transform: scale(1.05);
        }
        .quantity-input {
            max-width: 120px;
        }
    </style>
</head>
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
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
                        <a class="dropdown-item" href="/en/shop"><i class="flag-icon flag-icon-us"></i> English</a>
                        <a class="dropdown-item" href="/fr/boutique"><i class="flag-icon flag-icon-fr"></i> Français</a>
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

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Product Details</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                                    <li class="breadcrumb-item active">Product</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div id="product-loading" class="row">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="product-details" class="row" style="display: none;">
                    <div class="col-lg-6">
                        <img id="main-image" src="" alt="Product Image" class="product-main-image mb-3">
                        <div id="thumbnails" class="d-flex gap-2"></div>
                    </div>
                    <div class="col-lg-6">
                        <h2 id="product-name" class="mb-2"></h2>
                        <h3 class="text-primary mb-3" id="product-price"></h3>
                        <div id="stock-status" class="mb-3"></div>
                        <div id="product-description" class="mb-4"></div>
                        <div class="mb-4">
                            <label for="quantity" class="form-label"><strong>Quantity:</strong></label>
                            <input type="number" id="quantity" class="form-control quantity-input" value="1" min="1">
                        </div>
                        <div class="d-flex gap-2">
                            <button id="add-to-cart-btn" class="btn btn-primary btn-lg">
                                <i data-feather="shopping-cart"></i> Add to Cart
                            </button>
                            <a href="/en/shop" class="btn btn-outline-secondary btn-lg">
                                <i data-feather="arrow-left"></i> Back to Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="/assets/js/product.js?v=<?php echo time(); ?>"></script>
    <script>
        if (typeof feather !== 'undefined') feather.replace();
        $(document).ready(function() {
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            if (!token || !user) { window.location.href = '/'; return; }
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            $('.logout-button').on('click', function(e) { e.preventDefault(); UserManager.logout(true); });
            if (UserManager.hasRole('admin')) { $('.only_admin').removeClass('hidden'); }
        });
    </script>
</body>
</html>
