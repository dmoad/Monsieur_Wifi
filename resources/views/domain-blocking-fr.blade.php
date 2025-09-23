<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="monsieur-wifi - Gestion du blocage de domaines pour les administrateurs réseau">
    <meta name="keywords" content="wifi, réseau, blocage de domaines, filtrage de contenu, tableau de bord, administrateur, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Blocage de domaines - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
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

        /* Badge status */
        .badge-category-adult {
            background-color: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }
        .badge-category-gambling {
            background-color: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
        }
        .badge-category-malware {
            background-color: rgba(130, 28, 128, 0.12);
            color: #821c80;
        }
        .badge-category-social {
            background-color: rgba(0, 137, 255, 0.12);
            color: #0089ff;
        }
        .badge-category-streaming {
            background-color: rgba(40, 199, 111, 0.12);
            color: #28c76f;
        }
        .badge-category-custom {
            background-color: rgba(45, 45, 45, 0.12);
            color: #2d2d2d;
        }

        /* Category cards */
        .cursor-pointer {
            cursor: pointer;
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
                        <a class="dropdown-item" href="/en/domain-blocking" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/domain-blocking" data-language="fr">
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
                <li class="nav-item active only_admin hidden">
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
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Blocage de domaines</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a>
                                    </li>
                                    <li class="breadcrumb-item active">Blocage de domaines
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
                    <div class="form-group breadcrumb-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#domain-blocking-info">
                            <i data-feather="info" class="mr-25"></i>
                            <span>Info</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Blocking Categories -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Catégories de blocage</h4>
                                <p class="card-text">Activez ou désactivez les catégories pour activer ou désactiver le blocage de domaines par catégorie.</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-danger p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="octagon"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Contenu adulte</h4>
                                                            <span>1,024 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-adult" checked>
                                                    <label class="custom-control-label" for="category-adult"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-warning p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="dollar-sign"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Jeux d'argent</h4>
                                                            <span>856 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-gambling" checked>
                                                    <label class="custom-control-label" for="category-gambling"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-primary p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="shield-off"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Logiciels malveillants</h4>
                                                            <span>2,345 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-malware" checked>
                                                    <label class="custom-control-label" for="category-malware"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-info p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="users"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Réseaux sociaux</h4>
                                                            <span>342 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-social">
                                                    <label class="custom-control-label" for="category-social"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-success p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="film"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Streaming</h4>
                                                            <span>128 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-streaming">
                                                    <label class="custom-control-label" for="category-streaming"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="card cursor-pointer border shadow-none">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-secondary p-50 mr-1">
                                                            <div class="avatar-content">
                                                                <i data-feather="tag"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">Liste personnalisée</h4>
                                                            <span>43 domaines</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="category-custom" checked>
                                                    <label class="custom-control-label" for="category-custom"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Domain List Table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Domaines bloqués</h4>
                                    <div>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-domain">
                                            <i data-feather="plus" class="mr-25"></i>
                                            <span>Ajouter un domaine</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable table-responsive">
                                        <table class="datatables-domains table">
                                            <thead>
                                                <tr>
                                                    <th>Domaine</th>
                                                    <th>Catégorie</th>
                                                    <th>Date d'ajout</th>
                                                    <th>Dernière mise à jour</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be loaded via AJAX -->
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

    <!-- Add New Category Modal -->
    <div class="modal fade text-left" id="add-new-category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Ajouter une nouvelle catégorie</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category-name">Nom de la catégorie</label>
                            <input type="text" class="form-control" id="category-name" placeholder="Entrez le nom de la catégorie" />
                        </div>
                        <div class="form-group">
                            <label for="category-description">Description</label>
                            <textarea class="form-control" id="category-description" rows="3" placeholder="Entrez la description de la catégorie"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category-icon">Icône</label>
                            <select class="form-control" id="category-icon">
                                <option value="tag">Tag</option>
                                <option value="shield-off">Shield Off</option>
                                <option value="x-octagon">X Octagon</option>
                                <option value="dollar-sign">Dollar Sign</option>
                                <option value="users">Users</option>
                                <option value="film">Film</option>
                                <option value="play">Play</option>
                                <option value="shopping-cart">Shopping Cart</option>
                                <option value="briefcase">Briefcase</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category-color">Couleur</label>
                            <select class="form-control" id="category-color">
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                                <option value="success">Success</option>
                                <option value="danger">Danger</option>
                                <option value="warning">Warning</option>
                                <option value="info">Info</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="category-enabled" checked>
                                <label class="custom-control-label" for="category-enabled">Activé</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter la catégorie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add New Domain Modal -->
    <div class="modal fade text-left" id="add-new-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Ajouter un nouveau domaine</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="domain-name">Domaine</label>
                            <input type="text" class="form-control" id="domain-name" placeholder="exemple.com" />
                            <small class="form-text text-muted">Entrez un domaine sans http:// ou https://</small>
                        </div>
                        <div class="form-group">
                            <label for="domain-category">Catégorie</label>
                            <select class="form-control" id="domain-category">
                                <option value="1">Contenu adulte</option>
                                <option value="2">Jeux d'argent</option>
                                <option value="3">Logiciels malveillants</option>
                                <option value="4">Réseaux sociaux</option>
                                <option value="5">Streaming</option>
                                <option value="6">Liste personnalisée</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="domain-notes">Notes</label>
                            <textarea class="form-control" id="domain-notes" rows="3" placeholder="Entrez des notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter le domaine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Domain Modal -->
    <div class="modal fade text-left" id="edit-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel35">Modifier le domaine</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-domain-name">Domaine</label>
                            <input type="text" class="form-control" id="edit-domain-name" value="adultsite.example.com" readonly />
                        </div>
                        <div class="form-group">
                            <label for="edit-domain-category">Catégorie</label>
                            <select class="form-control" id="edit-domain-category">
                                <option value="1">Contenu adulte</option>
                                <option value="2">Jeux d'argent</option>
                                <option value="3">Logiciels malveillants</option>
                                <option value="4">Réseaux sociaux</option>
                                <option value="5">Streaming</option>
                                <option value="6">Liste personnalisée</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-domain-notes">Notes</label>
                            <textarea class="form-control" id="edit-domain-notes" rows="3">Ajouté à des fins de filtrage de contenu.</textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="edit-block-subdomains" checked>
                                <label class="custom-control-label" for="edit-block-subdomains">Bloquer tous les sous-domaines</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Domain Blocking Info Modal -->
    <div class="modal fade text-left" id="domain-blocking-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel37" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel37">
                        <i data-feather="info" class="mr-1"></i>
                        Comment fonctionne le blocage de domaines
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <!-- What is Domain Blocking -->
                            <div class="card shadow-none border-left-primary">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i data-feather="shield" class="mr-1"></i>
                                        Qu'est-ce que le blocage de domaines ?
                                    </h5>
                                    <p class="card-text">
                                        Le blocage de domaines empêche les utilisateurs de votre réseau d'accéder à des sites web spécifiques en bloquant leurs noms de domaine. 
                                        Lorsqu'un utilisateur tente de visiter un domaine bloqué, la demande est interceptée et refusée, protégeant votre réseau contre 
                                        le contenu indésirable, les menaces de sécurité ou les distractions de productivité.
                                    </p>
                                </div>
                            </div>

                            <!-- How to Add Domains -->
                            <div class="card shadow-none border-left-info mt-2">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i data-feather="plus-circle" class="mr-1"></i>
                                        Comment ajouter des domaines
                                    </h5>
                                    <ol class="mb-0">
                                        <li><strong>Domaine unique :</strong> Cliquez sur le bouton "Ajouter un domaine" pour ajouter des sites web individuels</li>
                                        <li><strong>Catégories :</strong> Organisez les domaines en catégories prédéfinies pour une meilleure gestion</li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Comprehensive Blocking -->
                            <div class="card shadow-none border-left-warning mt-2">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i data-feather="alert-triangle" class="mr-1"></i>
                                        Pourquoi plusieurs domaines sont nécessaires
                                    </h5>
                                    <p class="card-text">
                                        De nombreux sites web utilisent plusieurs domaines pour diffuser du contenu, éviter le blocage ou améliorer les performances. 
                                        Pour bloquer efficacement un service, vous devez souvent bloquer plusieurs domaines liés :
                                    </p>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Domaines à bloquer</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Facebook</strong></td>
                                                    <td>facebook.com, fb.com, fbcdn.net, fb.me, messenger.com</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>YouTube</strong></td>
                                                    <td>youtube.com, youtu.be, ytimg.com, googlevideo.com</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Instagram</strong></td>
                                                    <td>instagram.com, cdninstagram.com, ig.me</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Twitter/X</strong></td>
                                                    <td>twitter.com, x.com, t.co, twimg.com</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>TikTok</strong></td>
                                                    <td>tiktok.com, tiktokv.com, tiktokcdn.com, musical.ly</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Best Practices -->
                            <div class="card shadow-none border-left-success mt-2">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i data-feather="check-circle" class="mr-1"></i>
                                        Meilleures pratiques
                                    </h5>
                                    <ul class="mb-0">
                                        <li><strong>Utilisez les catégories :</strong> Regroupez les domaines connexes pour une gestion plus facile</li>
                                        <li><strong>Recherchez minutieusement :</strong> Recherchez tous les domaines utilisés par un service avant de le bloquer</li>
                                        <li><strong>Testez le blocage :</strong> Vérifiez que le blocage fonctionne comme prévu</li>
                                        <li><strong>Mises à jour régulières :</strong> Maintenez vos listes de blocage à jour car les services changent de domaines</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="alert alert-info mt-3">
                                <div class="alert-body">
                                    <i data-feather="zap" class="mr-1"></i>
                                    <strong>Conseil de pro :</strong> Utilisez les outils de développement du navigateur (F12) pour inspecter les requêtes réseau et identifier 
                                    tous les domaines utilisés par un site web. Cela aide à assurer un blocage complet.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Compris !</button>
                </div>
            </div>
        </div>
    </div>

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
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/forms/form-select2.js"></script>
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

            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            
            // Load categories and update counters
            loadCategoriesData();

            
            // Initialize DataTable with server-side data
            var domainsTable = $('.datatables-domains').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/api/blocked-domains',
                    type: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                    },
                    dataSrc: function(json) {
                        console.log("blocked-domains", json);
                        return json.data;
                    }
                },
                columns: [
                    { 
                        data: 'domain',
                        render: function(data, type, row) {
                            var avatarClass = getCategoryAvatarClass(row.category.slug);
                            return `
                                <div class="d-flex align-items-center">
                                    <div class="avatar ${avatarClass} mr-1 p-25">
                                        <div class="avatar-content">
                                            <i data-feather="globe"></i>
                                        </div>
                                    </div>
                                    <span>${data}</span>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'category',
                        render: function(data, type, row) {
                            var badgeClass = getCategoryBadgeClass(data.slug);
                            return `<span class="badge badge-pill ${badgeClass}">${data.name}</span>`;
                        }
                    },
                    { 
                        data: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                year: 'numeric' 
                            });
                        }
                    },
                    { 
                        data: 'updated_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                year: 'numeric' 
                            });
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit-domain-btn" href="javascript:void(0);" data-id="${data}">
                                            <i data-feather="edit-2" class="mr-50"></i>
                                            <span>Modifier</span>
                                        </a>
                                        <a class="dropdown-item delete-domain-btn" href="javascript:void(0);" data-id="${data}">
                                            <i data-feather="trash" class="mr-50"></i>
                                            <span>Supprimer</span>
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                responsive: true,
                columnDefs: [
                    {
                        targets: [4],
                        orderable: false
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    }
                },
                drawCallback: function() {
                    // Re-initialize feather icons after each draw
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            });
            
            // Helper functions for category styling
            function getCategoryBadgeClass(slug) {
                switch(slug) {
                    case 'adult-content': return 'badge-category-adult';
                    case 'gambling': return 'badge-category-gambling';
                    case 'malware': return 'badge-category-malware';
                    case 'social-media': return 'badge-category-social';
                    case 'streaming': return 'badge-category-streaming';
                    case 'custom-list': return 'badge-category-custom';
                    default: return 'badge-category-custom';
                }
            }
            
            function getCategoryAvatarClass(slug) {
                switch(slug) {
                    case 'adult-content': return 'bg-light-danger';
                    case 'gambling': return 'bg-light-warning';
                    case 'malware': return 'bg-light-primary';
                    case 'social-media': return 'bg-light-info';
                    case 'streaming': return 'bg-light-success';
                    case 'custom-list': return 'bg-light-secondary';
                    default: return 'bg-light-secondary';
                }
            }
            
            // Load categories data and update counters
            function loadCategoriesData() {
                $.ajax({
                    url: '/api/categories',
                    type: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                    },
                    success: function(response) {
                        updateCategoryCounters(response.data || response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load categories:', error);
                    }
                });
            }

            // Update category counters
            function updateCategoryCounters(categories) {
                console.log("categories", categories);
                categories = categories.categories;
                
                // Map English category names to French category names
                const categoryNameMap = {
                    'Adult Content': 'Contenu adulte',
                    'Gambling': 'Jeux d\'argent',
                    'Malware': 'Logiciels malveillants',
                    'Social Media': 'Réseaux sociaux',
                    'Streaming': 'Streaming',
                    'Custom List': 'Liste personnalisée'
                };
                
                categories.forEach(function(category) {
                    // Use the French name for matching
                    const frenchName = categoryNameMap[category.name] || category.name;
                    var categoryCard = $(`.card h4:contains("${frenchName}")`).closest('.card');
                    if (categoryCard.length) {
                        // alert(category.blocked_domains_count);
                        console.log("category", category);
                        categoryCard.find('span:first').text(`${category.blocked_domains_count || 0} domaines`);

                        // Update checkbox state
                        var checkbox = categoryCard.find('.custom-control-input');
                        checkbox.prop('checked', category.is_enabled);

                        // Update border
                        if (category.is_enabled) {
                            categoryCard.addClass('border-primary');
                        } else {
                            categoryCard.removeClass('border-primary');
                        }
                    }
                });
            }

            // Initialize select2
            if ($.fn.select2) {
                $('#domain-category, #edit-domain-category, #category-icon, #category-color').select2({
                    dropdownParent: $('#domain-category, #edit-domain-category').closest('.modal'),
                    minimumResultsForSearch: Infinity
                });
            }
            
            // Custom file input label
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choose file');
            });

            // Helper function to get category ID by name
            function getCategoryIdByName(categoryName) {
                const categoryMapping = {
                        'Contenu adulte': '1',
                        'Jeux d\'argent': '2',
                        'Logiciels malveillants': '3',
                        'Réseaux sociaux': '4',
                        'Streaming': '5',
                        'Liste personnalisée': '6'
                };
                return categoryMapping[categoryName] || null;
            }

            // Handle category toggles
            $('.custom-switch input[type="checkbox"]').on('change', function() {
                const categoryCard = $(this).closest('.card');
                const categoryName = categoryCard.find('h4').text();
                const isEnabled = $(this).is(':checked');
                const checkbox = $(this);

                // Find category ID based on name
                var categoryId = getCategoryIdByName(categoryName);
                
                if (!categoryId) {
                    console.error('Category ID not found for:', categoryName);
                    // Revert checkbox state
                    checkbox.prop('checked', !isEnabled);
                    return;
                }

                // Show loading state
                checkbox.prop('disabled', true);
                
                // API call to toggle category
                $.ajax({
                    url: `/api/categories/${categoryId}/toggle`,
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("response", response);
                        if (response.success) {
                            // Update visual state
                            if (isEnabled) {
                                categoryCard.addClass('border-primary');
                            } else {
                                categoryCard.removeClass('border-primary');
                            }

                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(`${categoryName} ${isEnabled ? 'enabled' : 'disabled'} successfully`);
                            }

                            console.log(`Category "${categoryName}" toggled to: ${isEnabled}`);
                        } else {
                            // Revert checkbox state on failure
                            checkbox.prop('checked', !isEnabled);
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to update category');
                            } else {
                                alert('Error: ' + (response.message || 'Failed to update category'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // Revert checkbox state on error
                        checkbox.prop('checked', !isEnabled);
                        
                        var errorMessage = 'Failed to update category';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                        console.error('Category toggle error:', error);
                    },
                    complete: function() {
                        // Re-enable checkbox
                        checkbox.prop('disabled', false);
                    }
                });
            });
            
            // Handle domain addition
            $('#add-new-domain form').on('submit', function(e) {
                e.preventDefault();
                
                const domainName = $('#domain-name').val();
                const categoryId = $('#domain-category').val();
                const notes = $('#domain-notes').val();
                const blockSubdomains = true; // Always block subdomains
                
                // API call to add domain
                $.ajax({
                    url: '/api/blocked-domains',
                    type: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                    },
                    data: JSON.stringify({
                        domain: domainName,
                        category_id: categoryId,
                        notes: notes,
                        block_subdomains: blockSubdomains
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Reload the DataTable
                            domainsTable.ajax.reload();
                            
                            // Reset form and close modal
                            $('#add-new-domain form').trigger('reset');
                            $('#add-new-domain').modal('hide');
                            
                            // Reload categories to update counters
                            loadCategoriesData();
                            
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert('Error: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Failed to add domain';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    }
                });
            });
            
            // Handle domain editing
            $(document).on('click', '.edit-domain-btn', function() {
                const domainId = $(this).data('id');
                
                // Get domain data
                $.ajax({
                    url: `/api/blocked-domains/${domainId}`,
                    type: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                    },
                    success: function(response) {
                        if (response.success) {
                            const domain = response.domain;
                            
                            // Populate edit modal
                            $('#edit-domain-name').val(domain.domain);
                            $('#edit-domain-category').val(domain.category_id).trigger('change');
                            $('#edit-domain-notes').val(domain.notes || '');
                            $('#edit-block-subdomains').prop('checked', domain.block_subdomains);
                            
                            // Store domain ID for update
                            $('#edit-domain').data('domain-id', domainId);
                            
                            // Show modal
                            $('#edit-domain').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to load domain data');
                    }
                });
            });
            
            // Handle domain update
            $('#edit-domain form').on('submit', function(e) {
                e.preventDefault();
                
                const domainId = $('#edit-domain').data('domain-id');
                const categoryId = $('#edit-domain-category').val();
                const notes = $('#edit-domain-notes').val();
                const blockSubdomains = $('#edit-block-subdomains').is(':checked');
                
                $.ajax({
                    url: `/api/blocked-domains/${domainId}`,
                    type: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + UserManager.getToken(),
                    },
                    data: JSON.stringify({
                        category_id: categoryId,
                        notes: notes,
                        block_subdomains: blockSubdomains
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Reload the DataTable
                            domainsTable.ajax.reload();
                            
                            // Close modal
                            $('#edit-domain').modal('hide');
                            
                            // Reload categories to update counters
                            loadCategoriesData();
                            
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Failed to update domain';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMessage);
                    }
                });
            });
            
            // Handle domain deletion
            $(document).on('click', '.delete-domain-btn', function() {
                const domainId = $(this).data('id');
                const row = $(this).closest('tr');
                const domain = row.find('td:first span').text();
                
                if (confirm(`Êtes-vous sûr de vouloir supprimer "${domain}" de la liste de blocage ?`)) {
                    $.ajax({
                        url: `/api/blocked-domains/${domainId}`,
                        type: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Authorization': 'Bearer ' + UserManager.getToken(),
                        },
                        success: function(response) {
                            if (response.success) {
                                // Reload the DataTable
                                domainsTable.ajax.reload();
                                
                                // Reload categories to update counters
                                loadCategoriesData();
                                
                                // Show success message
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(response.message);
                                } else {
                                    alert(response.message);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Failed to delete domain');
                        }
                    });
                }
            });
            
            // Add category click handler to view domains in that category
            $(document).on('click', '.category-card', function(e) {
                // Don't trigger if clicking on the switch
                if ($(e.target).hasClass('custom-control-input') || $(e.target).hasClass('custom-control-label')) {
                    return;
                }
                
                const categoryName = $(this).find('h4').text();
                const categoryCount = parseInt($(this).find('span').text()) || 0;
                
                // Map French category names back to English for search
                const frenchToEnglishMap = {
                    'Contenu adulte': 'Adult Content',
                    'Jeux d\'argent': 'Gambling', 
                    'Logiciels malveillants': 'Malware',
                    'Réseaux sociaux': 'Social Media',
                    'Streaming': 'Streaming',
                    'Liste personnalisée': 'Custom List'
                };
                
                const englishCategoryName = frenchToEnglishMap[categoryName] || categoryName;
                
                            // Update the blocked domains card title to show category
                                $('.card-title:contains("Domaines bloqués")').html(`${categoryName} Domaines bloqués <span class="text-muted font-small-3">(${categoryCount} domaines)</span>`);
                
                // Filter the datatable to show only this category (search for English name)
                domainsTable.search(englishCategoryName).draw();
                
                // Scroll to the domains section
                $('html, body').animate({
                    scrollTop: $("#basic-datatable").offset().top - 100
                }, 500);
                
                // Store selected category for add domain button
                window.selectedCategory = categoryName;
            });
            
            // Pre-select category when adding domain
            $('#add-new-domain').on('show.bs.modal', function() {
                if (window.selectedCategory) {
                    // Find the category value in the dropdown
                    let categoryValue;
                    switch(window.selectedCategory.toLowerCase()) {
                        case 'contenu adulte': categoryValue = '1'; break;
                        case 'jeux d\'argent': categoryValue = '2'; break;
                        case 'logiciels malveillants': categoryValue = '3'; break;
                        case 'réseaux sociaux': categoryValue = '4'; break;
                        case 'streaming': categoryValue = '5'; break;
                        case 'liste personnalisée': categoryValue = '6'; break;
                    }
                    if (categoryValue) {
                        $('#domain-category').val(categoryValue).trigger('change');
                    }
                }
            });

            // "View All Domains" button click
            $(document).on('click', '#view-all-domains', function() {
                $('.card-title:contains("Domaines bloqués")').html('Tous les domaines bloqués');
                domainsTable.search('').draw();
                window.selectedCategory = null;
            });

            // "Export All Domains" button click
            $(document).on('click', '#export-all-domains', function() {
                const button = $(this);
                const originalText = button.html();
                
                // Show loading state
                button.prop('disabled', true).html('<i data-feather="loader" class="mr-25"></i>Exportation en cours...');
                feather.replace();
                
                // Prepare export URL with parameters
                let exportUrl = '/api/blocked-domains/export?format=txt&active_only=true';
                
                // If a specific category is selected, include it
                if (window.selectedCategory) {
                    const categoryId = getCategoryIdByName(window.selectedCategory);
                    if (categoryId) {
                        exportUrl += `&category_id=${categoryId}`;
                    }
                }
                
                // Create a temporary link to trigger download
                const link = document.createElement('a');
                link.href = exportUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Restore button state after a short delay
                setTimeout(function() {
                    button.prop('disabled', false).html(originalText);
                    feather.replace();
                }, 1000);
                
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Exportation démarrée ! Vérifiez votre dossier de téléchargements.');
                } else {
                    alert('Exportation démarrée ! Vérifiez votre dossier de téléchargements.');
                }
            });

            // Initialize all category cards
            $('.card.cursor-pointer').addClass('category-card');

            // Add the "All Domains" option at the top
            $('.card-title:contains("Domaines bloqués")').after(`
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-primary mr-1" id="view-all-domains">
                        <i data-feather="list" class="mr-25"></i>Voir tous les domaines
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="export-all-domains">
                        <i data-feather="download" class="mr-25"></i>Tout exporter
                    </button>
                </div>
            `);

            // Replace Feather icons in new elements
            feather.replace({
                width: 14,
                height: 14
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
    </script>
</body>
</html>