<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Tableau de bord du contrôleur WiFi monsieur-wifi pour gérer et surveiller les réseaux WiFi.">
    <meta name="keywords" content="wifi, contrôleur cloud, gestion réseau, monsieur-wifi">
    <meta name="author" content="Monsieur WiFi">
    <title>Tableau de bord - Contrôleur Monsieur WiFi</title>
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
                        <i class="flag-icon flag-icon-fr"></i>
                        <span class="selected-language">Français</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="/en/dashboard" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/dashboard" data-language="fr">
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
                        <a class="dropdown-item" href="/fr/profile"><i class="mr-50" data-feather="user"></i> Profil</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="mr-50" data-feather="power"></i> Déconnexion</a>
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
                <li class="navigation-header"><span>Gestion</span></li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/fr/dashboard">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate">Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/locations">
                        <i data-feather="map-pin"></i>
                        <span class="menu-title text-truncate">Emplacements</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/analytics">
                        <i data-feather="bar-chart-2"></i>
                        <span class="menu-title text-truncate">Analytiques d'utilisation</span>
                    </a>
                </li> -->

                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/fr/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Portails captifs</span>
                    </a>
                </li>
                
               <!-- For Admin Section -->
               <li class="navigation-header only_admin hidden"><span>Administration</span></li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/accounts">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Comptes</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/domain-blocking">
                        <i data-feather="slash"></i>
                        <span class="menu-title text-truncate">Blocage de domaines</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/firmware">
                        <i data-feather="download"></i>
                        <span class="menu-title text-truncate">Firmware</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/system-settings">
                        <i data-feather="settings"></i>
                        <span class="menu-title text-truncate">Paramètres système</span>
                    </a>
                </li>
                <!-- Account Section -->
                <li class="navigation-header"><span>Compte</span></li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/fr/profile">
                         <i data-feather="user"></i>
                         <span class="menu-title text-truncate">Profil</span>
                     </a>
                </li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/logout">
                         <i data-feather="power"></i>
                         <span class="menu-title text-truncate">Déconnexion</span>
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
                            <h2 class="content-header-title float-left mb-0">Tableau de bord</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a>
                                    </li>
                                    <li class="breadcrumb-item active">Tableau de bord
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="grid"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="/fr/locations"><i class="mr-1" data-feather="plus"></i><span class="align-middle">Ajouter un emplacement</span></a>
                                <!-- <a class="dropdown-item" href="/fr/devices"><i class="mr-1" data-feather="hard-drive"></i><span class="align-middle">Ajouter un appareil</span></a> -->
                                <a class="dropdown-item" href="/fr/accounts"><i class="mr-1" data-feather="user-plus"></i><span class="align-middle">Ajouter un utilisateur</span></a>
                                <a class="dropdown-item" href="/fr/analytics"><i class="mr-1" data-feather="bar-chart-2"></i><span class="align-middle">Rapports</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Dashboard Content Starts -->
                <section id="dashboard-analytics">
                    <div class="row match-height">
                        <!-- Welcome Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card card-congratulation-medal" id="welcome-card">
                                <div class="card-body">
                                    <h5>Bienvenue sur le tableau de bord monsieur-wifi</h5>
                                    <p class="card-text font-small-3">Aperçu de l'état du réseau</p>
                                    <h3 class="mb-75 mt-2 pt-50">
                                        <span class="text-primary" id="welcome-total-locations">Chargement...</span>
                                    </h3>
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center mr-2">
                                            <i data-feather="check-circle" class="text-success font-medium-2 mr-50"></i>
                                            <span class="font-weight-bold" id="welcome-active-count">-</span> Actif
                                </div>
                                        <span class="mx-1">|</span>
                                        <div class="d-flex align-items-center ml-1">
                                            <i data-feather="x-circle" class="text-danger font-medium-2 mr-50"></i>
                                            <span class="font-weight-bold" id="welcome-offline-count">-</span> Hors ligne
                            </div>
                        </div>
                                    <a type="button" class="btn btn-primary mt-1" href="/fr/locations">Voir les détails</a>
                                    <img src="/app-assets/images/illustration/badge.svg" class="congratulation-medal" alt="Médaille" />
                                </div>
                            </div>
                        </div>
                        <!--/ Welcome Card -->

                        <!-- Statistics Card -->
                        <div class="col-lg-8 col-12">
                            <div class="card card-statistics" id="network-stats">
                                <div class="card-header">
                                    <h4 class="card-title">Statistiques du réseau</h4>
                                    <div class="d-flex align-items-center">
                                        <p class="card-text mr-25 mb-0">Mis à jour à l'instant</p>
                                    </div>
                                </div>
                                <div class="card-body statistics-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="wifi" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="routers-online-count">-/-</h4>
                                                    <p class="card-text font-small-3 mb-0">Routeurs en ligne</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                            <div class="media">
                                                <div class="avatar bg-light-info mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="users" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="active-users-count">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Utilisateurs actifs</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                            <div class="media">
                                                <div class="avatar bg-light-warning mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="download" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0" id="data-used-count">-</h4>
                                                    <p class="card-text font-small-3 mb-0">Données utilisées</p>
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
                                                    <h4 class="font-weight-bolder mb-0" id="uptime-percentage">-%</h4>
                                                    <p class="card-text font-small-3 mb-0">Temps de fonctionnement</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Statistics Card -->
                    </div>

                    <div class="row match-height">
                        <!-- Network Map -->
                        <div class="col-lg-8 col-12">
                                    <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Carte du réseau</h4>
                                    <div class="d-flex">
                                        <button id="fullscreen-btn" class="btn btn-sm btn-outline-primary mr-1">
                                            <i data-feather="maximize"></i>
                                        </button>
                                        <div class="dropdown chart-dropdown">
                                            <!-- <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="networkMapDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                7 derniers jours
                                            </button> -->
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="networkMapDropdown">
                                                <a class="dropdown-item" href="javascript:void(0);">7 derniers jours</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Mois dernier</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Année dernière</a>
                                        </div>
                                    </div>
                                        </div>
                                        </div>
                                        <div class="card-body">
                                    <div id="network-map" style="height: 400px;">
                                        <!-- Loading indicator for map -->
                                        <div class="d-flex align-items-center justify-content-center h-100" id="map-loading">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary mb-2" role="status">
                                                    <span class="sr-only">Chargement...</span>
                                                </div>
                                                <p class="text-muted">Chargement de la carte du réseau...</p>
                                            </div>
                                        </div>
                                    </div>
                                            </div>
                            </div>
                        </div>
                        <!--/ Network Map -->

                        <!-- Data Usage Trends -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Tendances d'utilisation des données</h4>
                                    <div class="dropdown chart-dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dataUsageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            7 derniers jours
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dataUsageDropdown">
                                            <a class="dropdown-item" href="javascript:void(0);">7 derniers jours</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Mois dernier</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Année dernière</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 d-flex flex-column flex-wrap text-center mb-2">
                                            <h1 class="font-weight-bolder mt-2 mb-0" id="total-bandwidth-used">-</h1>
                                            <p class="card-text">Utilisation totale cette semaine</p>
                                        </div>
                                    </div>
                                    <div id="data-usage-chart" class="mt-2" style="min-height: 270px;"></div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-info mr-1 p-50">
                                                    <div class="avatar-content">
                                                        <i data-feather="download" class="font-medium-4"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="font-weight-bolder mb-0" id="download-usage">185 GB</h4>
                                                    <p class="card-text font-small-3 mb-0">Téléchargement</p>
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
                                                    <h4 class="font-weight-bolder mb-0" id="upload-usage">60 GB</h4>
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

                    <!-- Error Messages Container -->
                    <div id="dashboard-errors"></div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Aperçu des emplacements</h4>
                                                                    <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="locationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Tous les emplacements
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="locationDropdown">
                                        <a class="dropdown-item" href="javascript:void(0);" data-location-filter="all">Tous les emplacements</a>
                                        <a class="dropdown-item" href="javascript:void(0);" data-location-filter="online">En ligne seulement</a>
                                        <a class="dropdown-item" href="javascript:void(0);" data-location-filter="offline">Hors ligne seulement</a>
                                    </div>
                                </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="locations-container">
                                        <!-- Location cards will be dynamically populated here -->
                                    </div>
                                            </div>
                                        </div>
                                        </div>

                    <!-- Network Analytics Overview -->
                    <div class="row" style="width: 100%; margin: 0; padding: 0;">
                        <div class="col-12" style="width: 100%; max-width: 100%; flex: 0 0 100%;">
                            <div class="card" id="analytics-section" style="width: 100% !important; max-width: 100% !important;">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Aperçu des analyses du réseau</h4>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="analyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            7 derniers jours
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analyticsDropdown">
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="1">Aujourd'hui</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="7">7 derniers jours</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="30">30 derniers jours</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="90">90 derniers jours</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Analytics Error Container -->
                                    <div id="analytics-errors"></div>
                                    
                                                        <!-- Analytics Metrics Row -->
                    <div class="row mb-2">
                        <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                            <div class="d-flex align-items-center mb-1">
                                <div class="avatar bg-light-primary p-50 mr-1">
                                    <div class="avatar-content">
                                        <i data-feather="users" class="font-medium-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0" id="analytics-total-users">-</h4>
                                    <span>Total utilisateurs</span>
                                </div>
                            </div>
                            <span class="text-muted"> Utilisateurs uniques connectés</span>
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
                                    <span>Utilisation des données</span>
                                </div>
                            </div>
                            <span class="text-muted"> Bande passante totale consommée</span>
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
                                    <span>Temps de fonctionnement</span>
                                </div>
                            </div>
                            <span class="text-muted"> Disponibilité du réseau</span>
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
                                    <span>Total sessions</span>
                                </div>
                            </div>
                            <span class="text-muted"> Sessions de connexion</span>
                        </div>
                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Dashboard Content Ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="https://mrwifi.com" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, Tous droits réservés</span></span><span class="float-md-right d-none d-md-block">Conçu avec soin & Créé avec<i data-feather="heart"></i></span></p>
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
    <script src="/app-assets/js/scripts/pages/dashboard-ecommerce.js"></script>
    <!-- END: Page JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js?v=1?v=2"></script>
    
    <!-- Include dashboard.js for dynamic data loading -->
    <script src="/assets/js/dashboard.js?v=10"></script>

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

            // Initialize small charts
            var usersOptions = {
                chart: {
                    type: 'line',
                    height: 40,
                    sparkline: { enabled: true },
                    toolbar: { show: false }
                },
                colors: ['#7367F0'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Utilisateurs',
                    data: [2100, 2300, 2500, 2700, 2600, 2800, 2856]
                }],
                tooltip: { fixed: { enabled: false } }
            };

            var devicesOptions = {
                chart: {
                    type: 'line',
                    height: 40,
                    sparkline: { enabled: true },
                    toolbar: { show: false }
                },
                colors: ['#28C76F'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Appareils',
                    data: [42, 43, 45, 44, 45, 43, 45]
                }],
                tooltip: { fixed: { enabled: false } }
            };

            var bandwidthOptions = {
                chart: {
                    type: 'line',
                    height: 40,
                    sparkline: { enabled: true },
                    toolbar: { show: false }
                },
                colors: ['#FF9F43'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Bande passante',
                    data: [1.8, 2.0, 2.2, 2.3, 2.1, 2.4, 2.4]
                }],
                tooltip: { fixed: { enabled: false } }
            };

            var alertsOptions = {
                chart: {
                    type: 'line',
                    height: 40,
                    sparkline: { enabled: true },
                    toolbar: { show: false }
                },
                colors: ['#EA5455'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Alertes',
                    data: [5, 4, 3, 5, 4, 3, 3]
                }],
                tooltip: { fixed: { enabled: false } }
            };

            // Initialize charts
            new ApexCharts(document.querySelector('#users-online-chart'), usersOptions).render();
            new ApexCharts(document.querySelector('#devices-online-chart'), devicesOptions).render();
            new ApexCharts(document.querySelector('#bandwidth-chart'), bandwidthOptions).render();
            new ApexCharts(document.querySelector('#alerts-chart'), alertsOptions).render();

            // Data Usage Trends Chart will be initialized by dashboard.js with real data
        });
    </script>
    
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>

    <script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>

    <script>
        // Map initialization will be handled by dashboard.js when real data is loaded
        // Handle fullscreen button functionality
        $(document).ready(function() {
            // Handle fullscreen button with simplified approach
            if (document.getElementById('fullscreen-btn')) {
                document.getElementById('fullscreen-btn').addEventListener('click', function() {
                    var mapElement = document.getElementById('network-map');
                    
                    if (!document.fullscreenElement) {
                        if (mapElement.requestFullscreen) {
                            mapElement.requestFullscreen();
                        } else if (mapElement.mozRequestFullScreen) {
                            mapElement.mozRequestFullScreen();
                        } else if (mapElement.webkitRequestFullscreen) {
                            mapElement.webkitRequestFullscreen();
                        } else if (mapElement.msRequestFullscreen) {
                            mapElement.msRequestFullscreen();
                        }
                        this.innerHTML = '<i data-feather="minimize-2"></i> Exit Full Screen';
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                        this.innerHTML = '<i data-feather="maximize-2"></i> Full Screen';
                    }
                    
                    // Re-initialize feather icons after DOM changes
                    setTimeout(function() {
                        if (typeof feather !== 'undefined') {
                            feather.replace({
                                width: 14,
                                height: 14
                            });
                        }
                    }, 100);
                });
            }
        });
    </script>

    <script>
        
        // Authentication check and user display
        $(document).ready(function() {
            // Check if user is logged in using UserManager from config.js
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            console.log("user: ", user);
            
            if (!token || !user) {
                // No token or user found, redirect to login page
                window.location.href = '/';
                return;
            }
            
            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            // alert("user profile picture: " + user.profile_picture);
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            
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
<!-- END: Body-->
</html>