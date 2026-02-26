<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Confirmation de Commande - Monsieur WiFi</title>
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
                        <i class="flag-icon flag-icon-fr"></i>
                        <span class="selected-language">Français</span>
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
                        <a class="dropdown-item" href="/fr/profile"><i class="mr-50" data-feather="user"></i> Profil</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item logout-button" href="/logout"><i class="mr-50" data-feather="power"></i> Déconnexion</a>
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
                    <a class="navbar-brand" href="/fr/dashboard">
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
                <li class="navigation-header"><span>Gestion</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Tableau de bord</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">Emplacements</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">Portails Captifs</span></a>
                </li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/fr/boutique"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">Boutique</span></a>
                </li>
                
                <li class="navigation-header only_admin hidden"><span>Admin</span></li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">Comptes</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">Blocage de domaines</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Micrologiciel</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">Paramètres</span></a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/admin/commandes"><i data-feather="package"></i><span class="menu-title text-truncate">Gérer les commandes</span></a>
                </li>
                
                <li class="navigation-header"><span>Compte</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/profile"><i data-feather="user"></i><span class="menu-title text-truncate">Profil</span></a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center logout-button" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">Déconnexion</span></a>
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
                            <h2 class="content-header-title float-left mb-0">Confirmation de Commande</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                                    <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                                    <li class="breadcrumb-item"><a href="/fr/commandes">Mes Commandes</a></li>
                                    <li class="breadcrumb-item active">Confirmation</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div id="order-loading" class="row">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
                <div id="order-details" style="display: none;">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i data-feather="check-circle" style="width: 80px; height: 80px; color: #28c76f;"></i>
                                    </div>
                                    <h2 class="text-success">Commande Confirmée!</h2>
                                    <p class="lead">Merci pour votre commande</p>
                                    <h4 class="mb-4">Commande #<span id="order-number"></span></h4>
                                    <p>Un courriel de confirmation a été envoyé à votre adresse électronique.</p>
                                    <div class="mt-4">
                                        <a href="/fr/commandes" class="btn btn-primary mr-2">Voir Mes Commandes</a>
                                        <a href="/fr/boutique" class="btn btn-outline-primary">Continuer les Achats</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Informations de Commande</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Numéro de Commande:</strong></td>
                                            <td id="info-order-number"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td id="info-date"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td id="info-status"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td id="info-total"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Adresse de Livraison</h4>
                                </div>
                                <div class="card-body">
                                    <div id="shipping-address"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Articles de la Commande</h4>
                                </div>
                                <div class="card-body">
                                    <div id="order-items"></div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Sous-total:</span>
                                                <span id="summary-subtotal"></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Livraison:</span>
                                                <span id="summary-shipping"></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Taxes:</span>
                                                <span id="summary-tax"></span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong>Total:</strong>
                                                <strong class="text-primary" id="summary-total"></strong>
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
    <script src="/assets/js/order-success-fr.js?v=<?php echo time(); ?>"></script>
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
