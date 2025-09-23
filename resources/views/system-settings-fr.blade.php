<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Paramètres globaux pour les administrateurs réseau">
    <meta name="keywords" content="wifi, réseau, paramètres, portail captif, radius, image de marque, tableau de bord, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Paramètres globaux - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-wizard.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->

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

        /* Custom styling for settings sections */
        .setting-section {
            padding: 1.5rem;
            border-radius: 0.428rem;
            border: 1px solid #ebe9f1;
            margin-bottom: 1.5rem;
        }
        
        .setting-section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .setting-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0;
        }

        .bs-stepper .bs-stepper-header .step.active .step-trigger .bs-stepper-box {
            background-color: #7367f0;
            color: #fff;
        }

        .bs-stepper .bs-stepper-header .step .step-trigger .bs-stepper-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0.5em 0;
            font-weight: 500;
            color: #6e6b7b;
            background-color: rgba(115, 103, 240, 0.12);
            border-radius: 0.35rem;
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
                        <i class="flag-icon flag-icon-fr"></i>
                        <span class="selected-language">Français</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="/en/system-settings" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/system-settings" data-language="fr">
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
                <li class="nav-item">
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
                    <a class="d-flex align-items-center" href="/analytics">
                        <i data-feather="bar-chart-2"></i>
                        <span class="menu-title text-truncate">Usage Analytics</span>
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
                <li class="nav-item active only_admin hidden">
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
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Paramètres globaux</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a>
                                    </li>
                                    <li class="breadcrumb-item active">Paramètres
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Settings Tabs -->
                <section id="settings-tabs">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills mb-3" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="captive-portal-tab" data-toggle="pill" href="#captive-portal" aria-expanded="true" role="tab" aria-selected="true">
                                                <i data-feather="wifi" class="mr-50"></i>
                                                <span class="font-weight-bold">Portail captif</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="radius-tab" data-toggle="pill" href="#radius" aria-expanded="false" role="tab" aria-selected="false">
                                                <i data-feather="shield" class="mr-50"></i>
                                                <span class="font-weight-bold">Configuration RADIUS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="branding-tab" data-toggle="pill" href="#branding" aria-expanded="false" role="tab" aria-selected="false">
                                                <i data-feather="image" class="mr-50"></i>
                                                <span class="font-weight-bold">Image de marque</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" aria-expanded="false" role="tab" aria-selected="false">
                                                <i data-feather="server" class="mr-50"></i>
                                                <span class="font-weight-bold">Système</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <!-- Captive Portal Tab -->
                                        <div role="tabpanel" class="tab-pane active" id="captive-portal" aria-labelledby="captive-portal-tab" aria-expanded="true">
                                            <form class="validate-form">
                                                <div class="setting-section">
                                                    <div class="setting-section-header">
                                                        <div class="avatar bg-light-primary p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="wifi"></i>
                                                            </div>
                                                        </div>
                                                        <h3 class="setting-section-title">Paramètres WiFi par défaut</h3>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="default_essid">ESSID par défaut</label>
                                                                <input type="text" id="default_essid" class="form-control" name="default_essid" placeholder="MrWiFi-Guest" value="MrWiFi-Guest" />
                                                                <small>Cet ESSID sera utilisé par défaut pour tous les nouveaux points d'accès</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="default_guest_essid">ESSID invité par défaut</label>
                                                                <input type="text" id="default_guest_essid" class="form-control" name="default_guest_essid" placeholder="MrWiFi-Guest" value="MrWiFi-Guest" />
                                                                <small>Cet ESSID sera utilisé par défaut pour les réseaux invités</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="default_password">Mot de passe par défaut</label>
                                                                <div class="input-group input-group-merge form-password-toggle">
                                                                    <input type="password" id="default_password" class="form-control" name="default_password" placeholder="············" value="Welcome123!" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                    </div>
                                                                </div>
                                                                <small>Mot de passe par défaut pour les nouveaux points d'accès (minimum 8 caractères)</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                        </div>
                                                    </div>
                                                    
                                                </div>

                                                <div class="setting-section">
                                                    <div class="setting-section-header">
                                                        <div class="avatar bg-light-info p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="layout"></i>
                                                            </div>
                                                        </div>
                                                        <h3 class="setting-section-title">Comportement du portail captif</h3>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="portal_timeout">Délai d'expiration de session par défaut</label>
                                                                <div class="input-group">
                                                                    <input type="number" id="portal_timeout" class="form-control" name="portal_timeout" value="24" min="1" max="168" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Heures</span>
                                                                    </div>
                                                                </div>
                                                                <small>Durée pendant laquelle les utilisateurs restent authentifiés avant de devoir se reconnecter</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="idle_timeout">Délai d'inactivité par défaut</label>
                                                                <div class="input-group">
                                                                    <input type="number" id="idle_timeout" class="form-control" name="idle_timeout" value="30" min="5" max="180" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Minutes</span>
                                                                    </div>
                                                                </div>
                                                                <small>Déconnecter les utilisateurs inactifs après cette période</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="bandwidth_limit">Limite de bande passante par défaut</label>
                                                                <div class="input-group">
                                                                    <input type="number" id="bandwidth_limit" class="form-control" name="bandwidth_limit" value="5" min="1" max="1000" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Mbps</span>
                                                                    </div>
                                                                </div>
                                                                <small>Limite de bande passante par défaut par utilisateur</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="user_limit">Nombre maximum d'utilisateurs par défaut</label>
                                                                <input type="number" id="user_limit" class="form-control" name="user_limit" value="50" min="1" max="500" />
                                                                <small>Nombre maximum d'utilisateurs simultanés par point d'accès</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="custom-control custom-switch custom-control-inline">
                                                                    <input type="checkbox" class="custom-control-input" id="enable_terms" name="enable_terms" checked />
                                                                    <label class="custom-control-label" for="enable_terms">Afficher les Conditions d'utilisation</label>
                                                                </div>
                                                                <small>Exiger l'acceptation des Conditions d'utilisation avant la connexion</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                                        <!-- <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button> -->
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- RADIUS Configuration Tab -->
                                        <div class="tab-pane" id="radius" role="tabpanel" aria-labelledby="radius-tab" aria-expanded="false">
                                            <form class="validate-form">
                                                <div class="setting-section">
                                                    <div class="setting-section-header">
                                                        <div class="avatar bg-light-primary p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="shield"></i>
                                                            </div>
                                                        </div>
                                                        <h3 class="setting-section-title">Serveur RADIUS principal</h3>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="radius_ip">Adresse IP du serveur</label>
                                                                <input type="text" id="radius_ip" class="form-control" name="radius_ip" placeholder="192.168.1.100" value="192.168.1.100" />
                                                                <small>Adresse IP de votre serveur RADIUS principal</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="radius_port">Port d'authentification</label>
                                                                <input type="number" id="radius_port" class="form-control" name="radius_port" placeholder="1812" value="1812" min="1" max="65535" />
                                                                <small>Port utilisé pour l'authentification RADIUS (par défaut : 1812)</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="radius_secret">Secret partagé</label>
                                                                <div class="input-group input-group-merge form-password-toggle">
                                                                    <input type="password" id="radius_secret" class="form-control" name="radius_secret" placeholder="············" value="RadiusSecret123" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                    </div>
                                                                </div>
                                                                <small>Secret partagé pour l'authentification RADIUS</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="accounting_port">Port de comptabilité</label>
                                                                <input type="number" id="accounting_port" class="form-control" name="accounting_port" placeholder="1813" value="1813" min="1" max="65535" />
                                                                <small>Port utilisé pour la comptabilité RADIUS (par défaut : 1813)</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                                        <!-- <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button> -->
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                                                                <!-- Branding Tab -->
                                                                                <div class="tab-pane" id="branding" role="tabpanel" aria-labelledby="branding-tab" aria-expanded="false">
                                                                                    <form class="validate-form">
                                                                                        <div class="setting-section">
                                                                                            <div class="setting-section-header">
                                                                                                <div class="avatar bg-light-primary p-50 mr-1">
                                                                                                    <div class="avatar-content">
                                                                                                        <i data-feather="type"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <h3 class="setting-section-title">Informations de l'entreprise</h3>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="company_name">Nom de l'entreprise</label>
                                                                        <input type="text" id="company_name" class="form-control" name="company_name" placeholder="monsieur-wifi" value="monsieur-wifi" />
                                                                        <small>Le nom de votre entreprise tel qu'affiché sur le portail captif</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="company_website">Site web de l'entreprise</label>
                                                                        <input type="url" id="company_website" class="form-control" name="company_website" placeholder="https://www.example.com" value="https://www.mrwifi.com" />
                                                                        <small>L'URL du site web de votre entreprise</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="contact_email">Email de contact</label>
                                                                        <input type="email" id="contact_email" class="form-control" name="contact_email" placeholder="support@example.com" value="support@mrwifi.com" />
                                                                        <small>Email de contact affiché sur le portail captif</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="support_phone">Téléphone de support</label>
                                                                        <input type="tel" id="support_phone" class="form-control" name="support_phone" placeholder="+1 (555) 123-4567" value="+1 (555) 123-4567" />
                                                                        <small>Numéro de téléphone de support affiché sur le portail captif</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                        
                                                                                        <div class="setting-section">
                                                                                            <div class="setting-section-header">
                                                                                                <div class="avatar bg-light-info p-50 mr-1">
                                                                                                    <div class="avatar-content">
                                                                                                        <i data-feather="image"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <h3 class="setting-section-title">Logo et images</h3>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="logo-upload">Logo de l'entreprise</label>
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" id="logo-upload" accept="image/*" />
                                                                            <label class="custom-file-label" for="logo-upload">Choisir un fichier</label>
                                                                        </div>
                                                                        <small>Taille recommandée : 300px x 100px (PNG ou SVG avec transparence)</small>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label>Logo actuel</label>
                                                                                                        <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                                                                            <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Current logo" height="50" />
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="favicon-upload">Favicon</label>
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" id="favicon-upload" accept="image/x-icon,image/png,image/gif" />
                                                                            <label class="custom-file-label" for="favicon-upload">Choisir un fichier</label>
                                                                        </div>
                                                                        <small>Taille recommandée : 32px x 32px (ICO, PNG ou GIF)</small>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label>Favicon actuel</label>
                                                                                                        <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                                                                            <img src="/app-assets/mrwifi-assets/MrWifi.png" alt="Current favicon" height="32" />
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="splash-background">Arrière-plan du portail captif</label>
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" id="splash-background" accept="image/*" />
                                                                            <label class="custom-file-label" for="splash-background">Choisir un fichier</label>
                                                                        </div>
                                                                        <small>Taille recommandée : 1920px x 1080px (JPG ou PNG)</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                        
                                                                                        <div class="setting-section">
                                                                                            <div class="setting-section-header">
                                                                                                <div class="avatar bg-light-warning p-50 mr-1">
                                                                                                    <div class="avatar-content">
                                                                                                        <i data-feather="layers"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <h3 class="setting-section-title">Personnalisation du portail</h3>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="primary_color">Couleur principale</label>
                                                                                                        <div class="input-group">
                                                                                                            <input type="color" id="primary_color" class="form-control" name="primary_color" value="#7367f0" />
                                                                                                            <div class="input-group-append">
                                                                                                                <span class="input-group-text">#7367f0</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <small>Couleur principale pour les boutons et les mises en évidence</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="secondary_color">Couleur secondaire</label>
                                                                                                        <div class="input-group">
                                                                                                            <input type="color" id="secondary_color" class="form-control" name="secondary_color" value="#82868b" />
                                                                                                            <div class="input-group-append">
                                                                                                                <span class="input-group-text">#82868b</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <small>Couleur secondaire pour les accents et les éléments alternatifs</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="font_family">Police principale</label>
                                                                                                        <select id="font_family" class="form-control" name="font_family">
                                                                                                            <option value="montserrat" selected>Montserrat</option>
                                                                                                            <option value="roboto">Roboto</option>
                                                                                                            <option value="open-sans">Open Sans</option>
                                                                                                            <option value="lato">Lato</option>
                                                                                                            <option value="poppins">Poppins</option>
                                                                                                        </select>
                                                                                                        <small>Famille de police utilisée dans tout le portail</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="portal_theme">Thème du portail</label>
                                                                        <select id="portal_theme" class="form-control" name="portal_theme">
                                                                            <option value="light" selected>Clair</option>
                                                                            <option value="dark">Sombre</option>
                                                                            <option value="auto">Automatique (préférence système)</option>
                                                                        </select>
                                                                        <small>Thème par défaut pour le portail captif</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                                                                                <!-- <button type="reset" class="btn btn-outline-secondary">Reset</button> -->
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                        
                                                                                <!-- System Tab -->
                                                                                <div class="tab-pane" id="system" role="tabpanel" aria-labelledby="system-tab" aria-expanded="false">
                                                                                    <form class="validate-form">
                                                                                        <div class="setting-section">
                                                                                            <div class="setting-section-header">
                                                                                                <div class="avatar bg-light-warning p-50 mr-1">
                                                                                                    <div class="avatar-content">
                                                                                                        <i data-feather="mail"></i>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <h3 class="setting-section-title">Configuration email</h3>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="smtp_server">Serveur SMTP</label>
                                                                        <input type="text" id="smtp_server" class="form-control" name="smtp_server" placeholder="smtp.example.com" value="smtp.gmail.com" />
                                                                        <small>Serveur SMTP pour l'envoi de notifications par email</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="smtp_port">Port SMTP</label>
                                                                        <input type="number" id="smtp_port" class="form-control" name="smtp_port" placeholder="587" value="587" min="1" max="65535" />
                                                                        <small>Port pour la connexion au serveur SMTP</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                        <label for="sender_email">Email expéditeur</label>
                                                                        <input type="email" id="sender_email" class="form-control" name="sender_email" placeholder="notifications@example.com" value="notifications@mrwifi.com" />
                                                                        <small>Adresse email à partir de laquelle les notifications sont envoyées</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div class="form-group">
                                                                                                        <label for="smtp_password">Mot de passe SMTP</label>
                                                                                                        <div class="input-group input-group-merge form-password-toggle">
                                                                                                            <input type="password" id="smtp_password" class="form-control" name="smtp_password" placeholder="············" />
                                                                                                            <div class="input-group-append">
                                                                                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <small>Mot de passe pour l'authentification avec le serveur SMTP</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                            <div class="row">
                                                                                                <div class="col-12">
                                                                                                    <button type="button" class="btn btn-outline-primary btn-sm">
                                                                                                        <i data-feather="send" class="mr-25"></i>
                                                                                                        <span>Envoyer un email de test</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                                                                                <!-- <button type="reset" class="btn btn-outline-secondary">Reset</button> -->
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
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
                                        
                                            <div class="sidenav-overlay"></div>
                                            <div class="drag-target"></div>
                                        
                                            <!-- BEGIN: Footer-->
                                            <footer class="footer footer-static footer-light">
                                                <p class="clearfix mb-0">
                    <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="#" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, Tous droits réservés</span></span>
                    <span class="float-md-right d-none d-md-block">Conçu avec soin & Créé avec<i data-feather="heart"></i></span>
                                                </p>
                                            </footer>
                                            <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
                                            <!-- END: Footer-->
                                        
                                            <!-- BEGIN: Vendor JS-->
                                            <script src="/app-assets/vendors/js/vendors.min.js"></script>
                                            <!-- BEGIN Vendor JS-->
                                        
                                            <!-- BEGIN: Page Vendor JS-->
                                            <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
                                            <script src="/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
                                            <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
                                            <script src="/app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>
                                            <!-- END: Page Vendor JS-->
                                        
                                            <!-- BEGIN: Theme JS-->
                                            <script src="/app-assets/js/core/app-menu.js"></script>
                                            <script src="/app-assets/js/core/app.js"></script>
                                            <!-- END: Theme JS-->
                                        
                                            <!-- BEGIN: Page JS-->
                                            <script src="/app-assets/js/scripts/forms/form-validation.js"></script>
                                            <!-- Include config.js before other custom scripts -->
                                            <script src="/assets/js/config.js?v=1"></script>
                                            <!-- END: Page JS-->
                                        
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
                                                    
                                                    // Initialize select2
                                                    if ($.fn.select2) {
                                                        $('#security-type, #timezone, #log-level, #portal-theme, #font-family').select2({
                                                            minimumResultsForSearch: Infinity
                                                        });
                                                    }

                                                    var profile_picture = localStorage.getItem('profile_picture');
                                                    $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
                                                    
                                                    // Initialize form validation
                                                    $('.validate-form').each(function() {
                                                        $(this).validate({
                                                            rules: {
                                                                'default_essid': {
                                                                    required: true
                                                                },
                                                                'default_guest_essid': {
                                                                    required: true
                                                                },
                                                                'default_password': {
                                                                    required: true,
                                                                    minlength: 8
                                                                },
                                                                'radius_ip': {
                                                                    required: function() {
                                                                        return $('#security-type').val() === 'wpa2-enterprise';
                                                                    },
                                                                    ipv4: true
                                                                },
                                                                'radius_secret': {
                                                                    required: function() {
                                                                        return $('#security-type').val() === 'wpa2-enterprise';
                                                                    },
                                                                    minlength: 8
                                                                },
                                                                'company_name': {
                                                                    required: true
                                                                },
                                                                'company_website': {
                                                                    url: true
                                                                },
                                                                'contact_email': {
                                                                    email: true
                                                                }
                                                            }
                                                        });
                                                    });
                                                    
                                                    // Custom file input label
                                                    $('.custom-file-input').on('change', function() {
                                                        var fileName = $(this).val().split('\\').pop();
                                                        $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
                                                    });
                                                    
                                                    // Handle primary color input
                                                    $('#primary_color').on('input', function() {
                                                        $(this).next('.input-group-append').find('.input-group-text').text($(this).val());
                                                    });
                                                    
                                                    // Handle secondary color input
                                                    $('#secondary_color').on('input', function() {
                                                        $(this).next('.input-group-append').find('.input-group-text').text($(this).val());
                                                    });
                                                    
                                                    // Toggle password visibility
                                                    $('.form-password-toggle .input-group-text').on('click', function(e) {
                                                        e.preventDefault();
                                                        var $this = $(this),
                                                            passwordInput = $this.closest('.form-password-toggle').find('input');
                                                        
                                                        if (passwordInput.attr('type') === 'text') {
                                                            passwordInput.attr('type', 'password');
                                                            $this.find('svg').replaceWith(feather.icons['eye'].toSvg());
                                                        } else if (passwordInput.attr('type') === 'password') {
                                                            passwordInput.attr('type', 'text');
                                                            $this.find('svg').replaceWith(feather.icons['eye-off'].toSvg());
                                                        }
                                                    });
                                                    
                                                    // Update conditional fields visibility based on security type
                                                    $('#security-type').on('change', function() {
                                                        var securityType = $(this).val();
                                                        
                                                        if (securityType === 'wpa2-enterprise') {
                                                            $('#radius-tab').parent().show();
                                                        } else {
                                                            // Only hide if not currently active
                                                            if (!$('#radius-tab').hasClass('active')) {
                                                                $('#radius-tab').parent().hide();
                                                            }
                                                        }
                                                    });
                                                    
                                                    // Form submission handlers
                                                    $('.validate-form').on('submit', function(e) {
                                                        e.preventDefault();
                                                        
                                                        var form = $(this);
                                                        if (form.valid()) {
                                                            // Show saving indicator
                                                            var submitBtn = form.find('button[type="submit"]');
                                                            var originalText = submitBtn.html();
                                                            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...');
                                                            submitBtn.attr('disabled', true);
                                                            
                                                            // Simulate API call
                                                            setTimeout(function() {
                                                                // Success notification
                                                                if (typeof toastr !== 'undefined') {
                                                                    toastr.success('Vos paramètres ont été enregistrés avec succès.', 'Paramètres enregistrés', {
                                                                        closeButton: true,
                                                                        tapToDismiss: false,
                                                                        timeOut: 5000
                                                                    });
                                                                } else {
                                                                    alert('Paramètres enregistrés avec succès !');
                                                                }
                                                                
                                                                // Restore button state
                                                                submitBtn.html(originalText);
                                                                submitBtn.attr('disabled', false);
                                                                
                                                                // Add to notification center
                                                                var section = form.closest('.tab-pane').attr('id');
                                                                var notificationTitle = '';
                                                                
                                                                switch(section) {
                                                                    case 'captive-portal':
                                                                        notificationTitle = 'Paramètres du portail captif mis à jour';
                                                                        break;
                                                                    case 'radius':
                                                                        notificationTitle = 'Paramètres RADIUS mis à jour';
                                                                        break;
                                                                    case 'branding':
                                                                        notificationTitle = 'Paramètres d\'image de marque mis à jour';
                                                                        break;
                                                                    case 'system':
                                                                        notificationTitle = 'Paramètres système mis à jour';
                                                                        break;
                                                                    default:
                                                                        notificationTitle = 'Paramètres mis à jour';
                                                                }
                                                                
                                                                var newNotification = `
                                                                    <a class="d-flex" href="javascript:void(0)">
                                                                        <div class="media d-flex align-items-start">
                                                                            <div class="media-left">
                                                                                <div class="avatar bg-light-success">
                                                                                    <div class="avatar-content"><i class="avatar-icon" data-feather="check"></i></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="media-body">
                                                                                <p class="media-heading"><span class="font-weight-bolder">${notificationTitle}</span></p>
                                                                                <small class="notification-text">Les paramètres ont été enregistrés avec succès</small>
                                                                                <small class="notification-text text-muted mt-1"><i data-feather="clock"></i> Just now</small>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                `;
                                                                
                                                                $('.scrollable-container.media-list').prepend(newNotification);
                                                                feather.replace({
                                                                    width: 14,
                                                                    height: 14
                                                                });
                                                                
                                                                // Update notification counter
                                                                var counter = $('.badge-up');
                                                                var count = parseInt(counter.text());
                                                                counter.text(count + 1);
                                                                
                                                            }, 1500);
                                                        }
                                                    });
                                                    
                                                    // Test email button
                                                    $('button:contains("Send Test Email")').on('click', function() {
                                                        var btn = $(this);
                                                        var originalText = btn.html();
                                                        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Envoi en cours...');
                                                        btn.attr('disabled', true);
                                                        
                                                        // Simulate sending test email
                                                        setTimeout(function() {
                                                            if (typeof toastr !== 'undefined') {
                                                                toastr.info('Un email de test a été envoyé à ' + $('#sender_email').val(), 'Email envoyé', {
                                                                    closeButton: true,
                                                                    tapToDismiss: false,
                                                                    timeOut: 5000
                                                                });
                                                            } else {
                                                                alert('Email de test envoyé à ' + $('#sender_email').val());
                                                            }
                                                            
                                                            btn.html(originalText);
                                                            btn.attr('disabled', false);
                                                        }, 2000);
                                                    });
                                                    
                                                    // Handle color picker updates
                                                    function updateColorText(input) {
                                                        var colorValue = $(input).val();
                                                        $(input).next('.input-group-append').find('.input-group-text').text(colorValue);
                                                    }
                                                    
                                                    $('#primary_color, #secondary_color').on('change', function() {
                                                        updateColorText(this);
                                                    });
                                                    
                                                    // Apply initial tab state based on URL hash if present
                                                    var hash = window.location.hash;
                                                    if (hash) {
                                                        $('.nav-pills a[href="' + hash + '"]').tab('show');
                                                    }
                                                    
                                                    // Update URL hash when tabs are switched
                                                    $('.nav-pills a').on('shown.bs.tab', function (e) {
                                                        if (history.pushState) {
                                                            history.pushState(null, null, e.target.hash);
                                                        } else {
                                                            window.location.hash = e.target.hash;
                                                        }
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
                                                    
                                                    // Update user display in the top right dropdown
                                                    $('.user-name').text(user.name);
                                                    $('.user-status').text(user.role);
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
    
    // Update user display in the top right dropdown
    $('.user-name').text(user.name);
    $('.user-status').text(user.role);
    
    // Load settings via AJAX
    loadSettings();
    
    // Set up form submission handlers
    setupFormHandlers();
    
    // Setup test email button
    setupTestEmailButton();
});

function loadSettings() {
    // Show loading spinner
    const loadingHtml = `
        <div class="d-flex justify-content-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `;
    $('.tab-content').prepend(loadingHtml);
    
    // Make AJAX call to get settings
    $.ajax({
        url: '/api/system-settings',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + UserManager.getToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.status === 'success') {
                // Remove loading spinner
                $('.tab-content .spinner-border').parent().remove();
                
                // Populate form fields with settings
                populateFormFields(response.settings);
            }
        },
        error: function(xhr) {
            // Remove loading spinner
            $('.tab-content .spinner-border').parent().remove();
            
            // Show error message
            if (typeof toastr !== 'undefined') {
                        toastr.error('Échec du chargement des paramètres. Veuillez réessayer.', 'Erreur');
            } else {
                        alert('Échec du chargement des paramètres. Veuillez réessayer.');
            }
            
            console.error('Failed to load settings:', xhr.responseText);
        }
    });
}

function populateFormFields(settings) {
    // Captive Portal Tab
    $('#default_essid').val(settings.default_essid);
    $('#default_guest_essid').val(settings.default_guest_essid);
    $('#default_password').val(settings.default_password);
    $('#portal_timeout').val(settings.portal_timeout);
    $('#idle_timeout').val(settings.idle_timeout);
    $('#bandwidth_limit').val(settings.bandwidth_limit);
    $('#user_limit').val(settings.user_limit);
    $('#enable_terms').prop('checked', settings.enable_terms);
    
    // RADIUS Tab
    $('#radius_ip').val(settings.radius_ip);
    $('#radius_port').val(settings.radius_port);
    $('#radius_secret').val(settings.radius_secret);
    $('#accounting_port').val(settings.accounting_port);
    
    // Branding Tab
    $('#company_name').val(settings.company_name);
    $('#company_website').val(settings.company_website);
    $('#contact_email').val(settings.contact_email);
    $('#support_phone').val(settings.support_phone);
    
    // Update logo preview if exists
    if (settings.logo_path) {
        $('.col-md-6 .form-group:contains("Current Logo") img').attr('src', settings.logo_path);
    }
    
    // Update favicon preview if exists
    if (settings.favicon_path) {
        $('.col-md-6 .form-group:contains("Current Favicon") img').attr('src', settings.favicon_path);
    }
    
    // Portal Customization
    $('#primary_color').val(settings.primary_color);
    $('#primary_color').next('.input-group-append').find('.input-group-text').text(settings.primary_color);
    
    $('#secondary_color').val(settings.secondary_color);
    $('#secondary_color').next('.input-group-append').find('.input-group-text').text(settings.secondary_color);
    
    $('#font_family').val(settings.font_family).trigger('change');
    $('#portal_theme').val(settings.portal_theme).trigger('change');
    
    // System Tab
    $('#smtp_server').val(settings.smtp_server);
    $('#smtp_port').val(settings.smtp_port);
    $('#sender_email').val(settings.sender_email);
    $('#smtp_password').val(settings.smtp_password);
}

function setupFormHandlers() {
    $('.validate-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        if (form.valid()) {
            // Show saving indicator
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            submitBtn.attr('disabled', true);
            
            // Create FormData object to handle file uploads
            var formData = new FormData();
            
            // Get the tab ID to determine which settings we're updating
            var tabId = form.closest('.tab-pane').attr('id');
            
            // Add all form inputs to FormData based on the active tab
            switch(tabId) {
                case 'captive-portal':
                    formData.append('default_essid', $('#default_essid').val());
                    formData.append('default_guest_essid', $('#default_guest_essid').val());
                    formData.append('default_password', $('#default_password').val());
                    formData.append('portal_timeout', $('#portal_timeout').val());
                    formData.append('idle_timeout', $('#idle_timeout').val());
                    formData.append('bandwidth_limit', $('#bandwidth_limit').val());
                    formData.append('user_limit', $('#user_limit').val());
                    formData.append('enable_terms', $('#enable_terms').is(':checked') ? 1 : 0);
                    break;
                    
                case 'radius':
                    formData.append('radius_ip', $('#radius_ip').val());
                    formData.append('radius_port', $('#radius_port').val());
                    formData.append('radius_secret', $('#radius_secret').val());
                    formData.append('accounting_port', $('#accounting_port').val());
                    break;
                    
                case 'branding':
                    formData.append('company_name', $('#company_name').val());
                    formData.append('company_website', $('#company_website').val());
                    formData.append('contact_email', $('#contact_email').val());
                    formData.append('support_phone', $('#support_phone').val());
                    formData.append('primary_color', $('#primary_color').val());
                    formData.append('secondary_color', $('#secondary_color').val());
                    formData.append('font_family', $('#font_family').val());
                    formData.append('portal_theme', $('#portal_theme').val());
                    
                    // Handle file uploads
                    if ($('#logo-upload')[0].files[0]) {
                        formData.append('logo', $('#logo-upload')[0].files[0]);
                    }
                    if ($('#favicon-upload')[0].files[0]) {
                        formData.append('favicon', $('#favicon-upload')[0].files[0]);
                    }
                    if ($('#splash-background')[0].files[0]) {
                        formData.append('splash_background', $('#splash-background')[0].files[0]);
                    }
                    break;
                    
                case 'system':
                    formData.append('smtp_server', $('#smtp_server').val());
                    formData.append('smtp_port', $('#smtp_port').val());
                    formData.append('sender_email', $('#sender_email').val());
                    formData.append('smtp_password', $('#smtp_password').val());
                    break;
                    
                default:
                    // If we can't determine the tab, include all form inputs
                    form.find('input, select, textarea').each(function() {
                        let input = $(this);
                        let name = input.attr('name');
                        
                        if (name) {
                            // Convert from HTML naming style to API naming style
                            name = name.replace(/-/g, '_');
                            
                            if (input.attr('type') === 'checkbox') {
                                formData.append(name, input.is(':checked') ? 1 : 0);
                            } else if (input.attr('type') === 'file') {
                                if (input[0].files[0]) {
                                    formData.append(name, input[0].files[0]);
                                }
                            } else {
                                formData.append(name, input.val());
                            }
                        }
                    });
            }
            
            // Send AJAX request
            $.ajax({
                url: '/api/system-settings',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Vos paramètres ont été enregistrés avec succès.', 'Paramètres enregistrés', {
                            closeButton: true,
                            tapToDismiss: false,
                            timeOut: 5000
                        });
                    } else {
                        alert('Paramètres enregistrés avec succès !');
                    }
                    
                    // Reload settings to ensure we have the latest data
                    if (response.settings) {
                        populateFormFields(response.settings);
                    }
                    
                    // Restore button state
                    submitBtn.html(originalText);
                    submitBtn.attr('disabled', false);
                    
                    // Add to notification center
                    var notificationTitle = '';
                    
                    switch(tabId) {
                        case 'captive-portal':
                            notificationTitle = 'Captive Portal settings updated';
                            break;
                        case 'radius':
                            notificationTitle = 'RADIUS settings updated';
                            break;
                        case 'branding':
                            notificationTitle = 'Branding settings updated';
                            break;
                        case 'system':
                            notificationTitle = 'System settings updated';
                            break;
                        default:
                            notificationTitle = 'Settings updated';
                    }
                    
                    var newNotification = `
                        <a class="d-flex" href="javascript:void(0)">
                            <div class="media d-flex align-items-start">
                                <div class="media-left">
                                    <div class="avatar bg-light-success">
                                        <div class="avatar-content"><i class="avatar-icon" data-feather="check"></i></div>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading"><span class="font-weight-bolder">${notificationTitle}</span></p>
                                    <small class="notification-text">Les paramètres ont été enregistrés avec succès</small>
                                    <small class="notification-text text-muted mt-1"><i data-feather="clock"></i> Just now</small>
                                </div>
                            </div>
                        </a>
                    `;
                    
                    $('.scrollable-container.media-list').prepend(newNotification);
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                    
                    // Update notification counter
                    var counter = $('.badge-up');
                    var count = parseInt(counter.text());
                    counter.text(count + 1);
                },
                error: function(xhr) {
                    let errorMessage = 'Échec de l\'enregistrement des paramètres. Veuillez réessayer.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const firstError = Object.values(errors)[0][0];
                            errorMessage = firstError;
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage, 'Error', {
                            closeButton: true,
                            tapToDismiss: false,
                            timeOut: 5000
                        });
                    } else {
                        alert('Erreur : ' + errorMessage);
                    }
                    
                    // Restore button state
                    submitBtn.html(originalText);
                    submitBtn.attr('disabled', false);
                }
            });
        }
    });
}

function setupTestEmailButton() {
    $('button:contains("Send Test Email")').on('click', function() {
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        btn.attr('disabled', true);
        
        $.ajax({
            url: '/api/system-settings/test-email',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                email: $('#sender_email').val()
            }),
            success: function(response) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Test email has been sent to ' + $('#sender_email').val(), 'Email Sent', {
                        closeButton: true,
                        tapToDismiss: false,
                        timeOut: 5000
                    });
                } else {
                    alert('Test email sent to ' + $('#sender_email').val());
                }
                
                btn.html(originalText);
                btn.attr('disabled', false);
            },
            error: function(xhr) {
                let errorMessage = 'Échec de l\'envoi de l\'email de test. Veuillez vérifier vos paramètres SMTP.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage, 'Error', {
                        closeButton: true,
                        tapToDismiss: false,
                        timeOut: 5000
                    });
                } else {
                    alert('Erreur : ' + errorMessage);
                }
                
                btn.html(originalText);
                btn.attr('disabled', false);
            }
        });
    });
}
</script>
                                        </body>
</html>