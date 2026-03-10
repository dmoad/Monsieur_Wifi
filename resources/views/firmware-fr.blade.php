<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Gestion de firmware pour les administrateurs réseau">
    <meta name="keywords" content="wifi, réseau, firmware, mises à jour, tableau de bord, administrateur, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Gestion de firmware - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/file-uploaders/dropzone.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-file-uploader.css">
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

        /* Badge status */
        .badge-status-stable {
            background-color: rgba(40, 199, 111, 0.12);
            color: #28c76f;
        }
        .badge-status-beta {
            background-color: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
        }
        .badge-status-deprecated {
            background-color: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }
        
        /* Progress bars */
        .progress-bar-success {
            background-color: #28c76f;
        }
        .progress-bar-info {
            background-color: #00cfe8;
        }
        .progress-bar-warning {
            background-color: #ff9f43;
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
                        <a class="dropdown-item" href="/en/firmware" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/firmware" data-language="fr">
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
                <li class="nav-item active only_admin hidden">
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
                            <h2 class="content-header-title float-left mb-0">Gestion de firmware</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a>
                                    </li>
                                    <li class="breadcrumb-item active">Firmware
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
                    <div class="form-group breadcrumb-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-firmware">
                            <i data-feather="upload-cloud" class="mr-25"></i>
                            <span>Télécharger un nouveau firmware</span>
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
                                        <i data-feather="hard-drive"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder firmware-stats total" id="total-firmware">0</h2>
                                <p class="card-text">Total des versions de firmware</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-success p-50 mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="check-circle"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder firmware-stats enabled" id="enabled-firmware">0</h2>
                                <p class="card-text">Firmware activés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-secondary p-50 mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="x-circle"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder firmware-stats disabled" id="disabled-firmware">0</h2>
                                <p class="card-text">Firmware désactivés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="avatar bg-light-info p-50 mb-1">
                                    <div class="avatar-content">
                                        <i data-feather="hard-drive"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder firmware-stats total" id="total-size">0 MB</h2>
                                <p class="card-text">Taille totale</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Firmware Table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Toutes les versions de firmware</h4>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable table-responsive">
                                        <table class="datatables-firmware table">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Statut</th>
                                                    <th>Modèle d'appareil</th>
                                                    <th>Par défaut</th>
                                                    <th>Taille</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Table data will be loaded dynamically via JavaScript -->
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

                <!-- Add New Firmware Modal -->
                <div class="modal fade text-left" id="add-new-firmware" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel33">Télécharger un nouveau firmware</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="firmware-name">Nom du firmware</label>
                                                <input type="text" class="form-control" id="firmware-name" placeholder="ex. v2.1.5 Mise à jour de sécurité" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="status">Statut</label>
                                                <select class="form-control" id="status" required>
                                                    <option value="1">Activer</option>
                                                    <option value="0">Désactiver</option>
                                                </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="model">Modèle d'appareil</label>
                                                <select class="form-control" id="model">
                                                    <option value="">Chargement...</option>
                                                </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="default-firmware">
                                        <label class="custom-control-label" for="default-firmware">Définir comme firmware par défaut pour ce modèle</label>
                                    </div>
                                    <small class="text-muted">Lorsqu'activé, ce firmware sera automatiquement assigné aux nouveaux appareils de ce modèle.</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" id="description" rows="3" placeholder="Description et changelog du firmware"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="firmware-file">Fichier firmware</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="firmware-file" accept=".tar.gz,.tgz,.tar" required>
                                                    <label class="custom-file-label" for="firmware-file">Choisir un fichier</label>
                                    </div>
                                                <small class="form-text text-muted">Taille max : 100MB. Formats acceptés : .tar.gz, .tgz, .tar</small>
                                </div>
                            </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Télécharger le firmware</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Firmware Modal -->
                <div class="modal fade text-left" id="edit-firmware" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel34">Modifier le firmware</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="#">
                                <div class="modal-body">
                                    <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="edit-firmware-name">Nom du firmware</label>
                                                <input type="text" class="form-control" id="edit-firmware-name" value="v2.1.4 Security patch" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="edit-status">Statut</label>
                                                <select class="form-control" id="edit-status">
                                                    <option value="1" selected>Activer</option>
                                                    <option value="0">Désactiver</option>
                                                </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                                <label for="edit-model">Modèle d'appareil</label>
                                                <select class="form-control" id="edit-model">
                                                    <option value="">Chargement...</option>
                                                </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="edit-default-firmware">
                                        <label class="custom-control-label" for="edit-default-firmware">Définir comme firmware par défaut pour ce modèle</label>
                                    </div>
                                    <small class="text-muted">Lorsqu'activé, ce firmware sera automatiquement assigné aux nouveaux appareils de ce modèle.</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                                <label for="edit-description">Description</label>
                                                <textarea class="form-control" id="edit-description" rows="3">Security patch addressing vulnerability CVE-2024-12345. Improved stability for high-density deployments.</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Fichier firmware (Optionnel)</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="edit-firmware-file" accept=".tar.gz,.tgz,.tar">
                                                    <label class="custom-file-label" for="edit-firmware-file">Choisir un fichier firmware</label>
                                                </div>
                                                <small class="form-text text-muted">Formats acceptés : .tar.gz, .tgz, .tar</small>
                                    </div>
                                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
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
                <script src="/app-assets/vendors/js/file-uploaders/dropzone.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
                <script src="/app-assets/js/scripts/forms/form-file-uploader.js"></script>
    <!-- END: Page JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js?v=1"></script>

    <script>
        // Global variables
        let firmwareData = [];
        let currentEditingId = null;

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
            const table = $('.datatables-firmware').DataTable({
                responsive: true,
                order: [[0, 'desc']], // Sort by first column (name/date) descending - latest first
                columnDefs: [
                    {
                        targets: [5],
                        orderable: false
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    },
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                    infoFiltered: "(filtré à partir de _MAX_ entrées totales)",
                    lengthMenu: "Afficher _MENU_ entrées",
                    search: "Rechercher:",
                    zeroRecords: "Aucun enregistrement correspondant trouvé",
                    emptyTable: "Aucune donnée disponible dans le tableau",
                    loadingRecords: "Chargement..."
                },
                drawCallback: function(settings) {
                    // Re-initialize feather icons after each draw/pagination
                    if (feather) {
                        feather.replace({
                            width: 14,
                            height: 14
                        });
                    }   
                    // Re-initialize any dropdowns or tooltips if needed
                    $('[data-toggle="dropdown"]').dropdown();
                }
            });
            
            // Add event delegation for firmware actions to work across pagination
            $(document).on('click', '.firmware-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('firmware-id');
                editFirmware(parseInt(id));
            });
            
            $(document).on('click', '.firmware-download', function(e) {
                e.preventDefault();
                const id = $(this).data('firmware-id');
                downloadFirmware(parseInt(id));
            });
            
            $(document).on('click', '.firmware-set-default', function(e) {
                e.preventDefault();
                const id = $(this).data('firmware-id');
                setAsDefault(parseInt(id));
            });
            
            $(document).on('click', '.firmware-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('firmware-id');
                deleteFirmware(parseInt(id));
            });
            
            // Initialize Select2 dropdowns
            initializeSelect2();
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            
            // Custom file input label
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
            });

            // Load firmware data
            loadFirmwareData();
            loadProductModels();
        });

        function initializeSelect2() {
            // Destroy existing Select2 instances first (only if they exist)
            $('#status, #edit-status, #model, #edit-model').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            
            // Initialize Select2 for all dropdowns
            $('#status, #edit-status').select2({
                minimumResultsForSearch: Infinity,
                placeholder: 'Sélectionner le statut',
                allowClear: false,
                width: '100%'
            });
            
            $('#model, #edit-model').select2({
                minimumResultsForSearch: Infinity,
                placeholder: 'Sélectionner le modèle d\'appareil',
                allowClear: false,
                width: '100%'
            });
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

            // Form submissions
            $('#add-new-firmware form').on('submit', function(e) {
                e.preventDefault();
                uploadFirmware();
            });

            $('#edit-firmware form').on('submit', function(e) {
                e.preventDefault();
                updateFirmware();
            });

            // Reset forms when modals are hidden
            $('#add-new-firmware').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.custom-file-label').text('Choisir un fichier');
                // Reset Select2 dropdowns
                $('#status').val('').trigger('change');
                $('#model').val('').trigger('change');
            });

            $('#edit-firmware').on('hidden.bs.modal', function() {
                currentEditingId = null;
                $(this).find('form')[0].reset();
                $('.custom-file-label').text('Choisir un fichier firmware');
                // Reset Select2 dropdowns  
                $('#edit-status').val('').trigger('change');
                $('#edit-model').val('').trigger('change');
            });

            // Re-initialize Select2 when modals are shown
            $('#add-new-firmware, #edit-firmware').on('shown.bs.modal', function() {
                initializeSelect2();
            });
        });

        // API Functions
        function getAuthHeaders() {
            return {
                'Authorization': 'Bearer ' + UserManager.getToken(),
                'Accept': 'application/json'
            };
        }

        function loadFirmwareData() {
            $.ajax({
                url: '/api/firmware',
                method: 'GET',
                headers: getAuthHeaders(),
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        firmwareData = response.data;
                        updateFirmwareTable();
                        updateStats();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading firmware:', xhr);
                    showToast('Erreur lors du chargement des données de firmware', 'error');
                }
            });
        }

        function updateFirmwareTable() {
            const table = $('.datatables-firmware').DataTable();
            table.clear();

            // Sort firmware data by creation date (latest first)
            const sortedFirmwareData = [...firmwareData].sort((a, b) => {
                // Sort by created_at if available, otherwise by id (assuming higher id = newer)
                if (a.created_at && b.created_at) {
                    return new Date(b.created_at) - new Date(a.created_at);
                }
                return b.id - a.id;
            });

            sortedFirmwareData.forEach(function(firmware) {
                const statusBadge = firmware.is_enabled 
                    ? '<span class="badge badge-pill badge-light-success">Activé</span>'
                    : '<span class="badge badge-pill badge-light-secondary">Désactivé</span>';
                
                const defaultBadge = firmware.default_model_firmware 
                    ? '<span class="badge badge-pill badge-light-primary">Par défaut</span>'
                    : '<span class="badge badge-pill badge-light-secondary">-</span>';
                
                const modelName = getModelName(firmware.model);
                const fileSize = formatFileSize(firmware.file_size);

                table.row.add([
                    `<div class="d-flex align-items-center">
                        <div class="avatar bg-light-primary mr-1 p-25">
                            <div class="avatar-content">
                                <i data-feather="hard-drive"></i>
                            </div>
                        </div>
                        <div>
                            <div class="font-weight-bold">${firmware.name}</div>
                            <div class="small text-truncate text-muted">${firmware.description || ''}</div>
                        </div>
                    </div>`,
                    statusBadge,
                    modelName,
                    defaultBadge,
                    fileSize,
                    `<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                            <i data-feather="more-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item firmware-edit" href="javascript:void(0);" data-firmware-id="${firmware.id}">
                                <i data-feather="edit-2" class="mr-50"></i>
                                <span>Modifier</span>
                            </a>
                            <a class="dropdown-item firmware-download" href="javascript:void(0);" data-firmware-id="${firmware.id}">
                                <i data-feather="download" class="mr-50"></i>
                                <span>Télécharger</span>
                            </a>
                            ${!firmware.default_model_firmware ? `
                            <a class="dropdown-item firmware-set-default" href="javascript:void(0);" data-firmware-id="${firmware.id}">
                                <i data-feather="star" class="mr-50"></i>
                                <span>Définir par défaut</span>
                            </a>` : ''}
                            <a class="dropdown-item firmware-delete" href="javascript:void(0);" data-firmware-id="${firmware.id}">
                                <i data-feather="trash" class="mr-50"></i>
                                <span>Supprimer</span>
                            </a>
                        </div>
                    </div>`
                ]);
            });

            table.draw();
            // Re-initialize feather icons after drawing
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        }

        function updateStats() {
            const total = firmwareData.length;
            const enabled = firmwareData.filter(f => f.is_enabled).length;
            const disabled = firmwareData.filter(f => !f.is_enabled).length;
            const totalSize = firmwareData.reduce((sum, f) => sum + (f.file_size || 0), 0);

            // Update stats cards
            $('#total-firmware').text(total);
            $('#enabled-firmware').text(enabled);
            $('#disabled-firmware').text(disabled);
            $('#total-size').text(formatFileSize(totalSize));
        }

        function uploadFirmware() {
            const formData = new FormData();
            const fileInput = document.getElementById('firmware-file');
            
            if (!fileInput.files[0]) {
                showToast('Veuillez sélectionner un fichier firmware', 'error');
                return;
            }

            formData.append('name', $('#firmware-name').val());
            formData.append('model', $('#model').val());
            formData.append('description', $('#description').val());
            formData.append('is_enabled', $('#status').val());
            formData.append('default_model_firmware', $('#default-firmware').is(':checked') ? 1 : 0);
            formData.append('file', fileInput.files[0]);

            $.ajax({
                url: '/api/firmware',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Accept': 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('Firmware téléchargé avec succès', 'success');
                        $('#add-new-firmware').modal('hide');
                        $('#add-new-firmware form')[0].reset();
                        $('.custom-file-label').text('Choisir un fichier');
                        loadFirmwareData();
                    }
                },
                error: function(xhr) {
                    console.error('Error uploading firmware:', xhr);
                    let message = 'Erreur lors du téléchargement du firmware';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'error');
                }
            });
        }

        function editFirmware(id) {
            const firmware = firmwareData.find(f => f.id === id);
            if (!firmware) return;

            currentEditingId = id;
            
            // Show modal first
            $('#edit-firmware').modal('show');
            
            // Use setTimeout to ensure modal is fully rendered before setting values
            setTimeout(() => {
                $('#edit-firmware-name').val(firmware.name);
                $('#edit-description').val(firmware.description || '');
                
                // Set Select2 values with proper triggering
                const statusValue = firmware.is_enabled ? '1' : '0';
                // firmware.model is already device_type ('820' or '835')
                const modelValue = firmware.model || '';
                
                $('#edit-status').val(statusValue).trigger('change.select2');
                $('#edit-model').val(modelValue).trigger('change.select2');
                
                // Set default firmware checkbox
                $('#edit-default-firmware').prop('checked', firmware.default_model_firmware || false);
                
                // Clear file input
                $('#edit-firmware-file').val('');
                $('.custom-file-label').text('Choisir un fichier firmware');
            }, 300);
        }

        function updateFirmware() {
            if (!currentEditingId) return;

            const formData = new FormData();
            formData.append('name', $('#edit-firmware-name').val());
            formData.append('model', $('#edit-model').val());
            formData.append('description', $('#edit-description').val());
            formData.append('is_enabled', $('#edit-status').val());
            formData.append('default_model_firmware', $('#edit-default-firmware').is(':checked') ? 1 : 0);
            formData.append('_method', 'PUT');

            const fileInput = document.getElementById('edit-firmware-file');
            if (fileInput.files[0]) {
                formData.append('file', fileInput.files[0]);
            }

            $.ajax({
                url: `/api/firmware/${currentEditingId}`,
                method: 'POST', // Laravel handles PUT via _method
                headers: {
                    'Authorization': 'Bearer ' + UserManager.getToken(),
                    'Accept': 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('Firmware mis à jour avec succès', 'success');
                        $('#edit-firmware').modal('hide');
                        loadFirmwareData();
                        currentEditingId = null;
                    }
                },
                error: function(xhr) {
                    console.error('Error updating firmware:', xhr);
                    let message = 'Erreur lors de la mise à jour du firmware';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'error');
                }
            });
        }

        function deleteFirmware(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce firmware ?')) return;

            $.ajax({
                url: `/api/firmware/${id}`,
                method: 'DELETE',
                headers: getAuthHeaders(),
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('Firmware supprimé avec succès', 'success');
                        loadFirmwareData();
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting firmware:', xhr);
                    showToast('Erreur lors de la suppression du firmware', 'error');
                }
            });
        }

        function downloadFirmware(id) {
            const firmware = firmwareData.find(f => f.id === id);
            if (!firmware) return;

            // Create download link
            const downloadUrl = `/api/firmware/${id}/download?token=${UserManager.getToken()}`;
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = firmware.file_name;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function setAsDefault(id) {
            const firmware = firmwareData.find(f => f.id === id);
            if (!firmware) return;

            const modelName = getModelName(firmware.model);
            if (!confirm(`Êtes-vous sûr de vouloir définir "${firmware.name}" comme firmware par défaut pour les appareils ${modelName} ?`)) return;

            $.ajax({
                url: `/api/firmware/${id}/set-default`,
                method: 'POST',
                headers: getAuthHeaders(),
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('Firmware défini par défaut avec succès', 'success');
                        loadFirmwareData();
                    }
                },
                error: function(xhr) {
                    console.error('Error setting firmware as default:', xhr);
                    let message = 'Erreur lors de la définition du firmware par défaut';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'error');
                }
            });
        }

        // Helper functions
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // productModels array loaded from /api/firmware/models
        // Each entry: { id, name, device_type }
        let productModels = [];

        function loadProductModels() {
            $.ajax({
                url: '/api/firmware/models',
                method: 'GET',
                headers: getAuthHeaders(),
                success: function(response) {
                    if (response.status === 'success') {
                        productModels = response.data;
                        populateModelDropdowns();
                    }
                },
                error: function() {
                    console.error('Failed to load device models');
                }
            });
        }

        function populateModelDropdowns() {
            const $selects = $('#model, #edit-model');
            $selects.empty().append('<option value="">Sélectionner un modèle</option>');
            productModels.forEach(function(pm) {
                $selects.append(`<option value="${pm.device_type}">${pm.name}</option>`);
            });
            $selects.trigger('change');
        }

        function getModelName(deviceType) {
            const pm = productModels.find(m => m.device_type === deviceType);
            return pm ? pm.name : (deviceType || 'Non spécifié');
        }

        function getModelId(modelName) {
            // Legacy - now we use device_type directly as value
            if (modelName === '1' || modelName === 1 || modelName === '820AX') {
                return '820';
            } else if (modelName === '2' || modelName === 2 || modelName === '835AX') {
                return '835';
            }
            
            // If no match found, return empty string
            return '';
        }

        function showToast(message, type = 'info') {
            // Simple toast notification
            const toast = $(`
                <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            
            $('body').append(toast);
            
            setTimeout(() => {
                toast.alert('close');
            }, 5000);
        }
    </script>
</body>
</html>