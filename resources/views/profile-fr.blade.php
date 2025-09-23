<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi - Gestion du profil utilisateur pour les administrateurs réseau">
    <meta name="keywords" content="wifi, réseau, profil, tableau de bord, administrateur, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Profil - Monsieur WiFi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-pickadate.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-validation.css">
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
                        <a class="dropdown-item" href="/en/profile" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/profile" data-language="fr">
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
                <li class="nav-item dropdown dropdown-notification mr-25">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge badge-pill badge-primary badge-up">5</span>
                    </a>
                    <!-- Notification dropdown content -->
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <!-- Notification content here -->
                        </ul>
                    </li>
                
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
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/fr/system-settings">
                        <i data-feather="settings"></i>
                        <span class="menu-title text-truncate">Paramètres système</span>
                    </a>
                </li>
                <!-- Account Section -->
                <li class="navigation-header"><span>Compte</span></li>
                <li class="nav-item active">
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
                            <h2 class="content-header-title float-left mb-0">Mon profil</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a>
                                    </li>
                                    <li class="breadcrumb-item active">Profil
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- account setting page -->
                <section id="page-account-settings">
                    <div class="row">
                       
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- general tab -->
                                        <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                            <!-- header media -->
                                            <div class="media">
                                                <a href="javascript:void(0);" class="mr-25">
                                                    <img src="" id="account-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                                </a>
                                                <!-- upload and reset button -->
                                                <div class="media-body mt-75 ml-1">
                                                    <label for="account-upload" class="btn btn-sm btn-primary mb-75 mr-75">Télécharger une nouvelle photo</label>
                                                    <input type="file" id="account-upload" hidden accept="image/*" />
                                                    <p>JPG ou PNG autorisés. Taille maximale de 2 Mo</p>
                                                </div>
                                                <!--/ upload and reset button -->
                                            </div>
                                            <!--/ header media -->

                                            <!-- form -->
                                            <form class="validate-form mt-2">
                                                <div class="row">
                                                    <!-- <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-username">Username</label>
                                                            <input type="text" class="form-control" id="account-username" name="username" placeholder="Username" value="jsmith" />
                                                        </div>
                                                    </div> -->
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-name">Nom complet</label>
                                                            <input type="text" class="form-control" id="account-name" name="name" placeholder="Nom" value="Votre nom" />
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-e-mail">Email</label>
                                                            <input type="email" class="form-control" id="account-e-mail" name="email" placeholder="Email" value="votre@email.com" />
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-company">Company</label>
                                                            <input type="text" class="form-control" id="account-company" name="company" placeholder="Company name" value="monsieur-wifi Networks" />
                                                        </div>
                                                    </div> -->
                                                    <!-- <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-phone">Phone</label>
                                                            <input type="text" class="form-control" id="account-phone" name="phone" placeholder="Phone number" value="+1 (555) 123-4567" />
                                                            </div>
                                                    </div> -->
                                                    <!-- <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-role">Role</label>
                                                            <select class="form-control" id="account-role" name="role">
                                                                <option value="admin" selected>Administrator</option>
                                                                <option value="manager">Network Owner</option>
                                                            </select>
                                                        </div>
                                                    </div> -->
                                                    <!-- <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="account-bio">Bio</label>
                                                            <textarea class="form-control" id="account-bio" rows="3" placeholder="Your bio data">Network administrator with 8+ years of experience in setting up and managing enterprise-level WiFi networks. Specializing in high-density deployments and security optimization.</textarea>
                                                        </div>
                                                    </div> -->
                                                    
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-new-password1">Nouveau mot de passe</label>
                                                            <div class="input-group form-password-toggle input-group-merge">
                                                                <input type="password" id="account-new-password1" name="new-password1" class="form-control" placeholder="Nouveau mot de passe" />
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text cursor-pointer">
                                                                        <i data-feather="eye"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <small class="form-text text-bold ">Laisser vide si vous ne voulez pas changer votre mot de passe</small>
                                                            <small class="form-text text-muted">Minimum 8 caractères, doit inclure des lettres, des chiffres et des caractères spéciaux</small>
                                                            <small class="form-text text-danger hidden" id="password-error-message">Les mots de passe ne correspondent pas</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-retype-new-password1">Confirmer le nouveau mot de passe</label>
                                                            <div class="input-group form-password-toggle input-group-merge">
                                                                <input type="password" class="form-control" id="account-retype-new-password1" name="confirm-new-password1" placeholder="Confirmer le nouveau mot de passe" />
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text cursor-pointer"><i data-feather="eye"></i></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mt-1 mr-1" id="save-profile-btn">Enregistrer les modifications</button>
                                                        <!-- <button type="reset" class="btn btn-outline-secondary mt-1">Annuler</button> -->
                                                    </div>
                                                </div>
                                            </form>
                                            <!--/ form -->
                                        </div>
                                        <!--/ general tab -->

                                        <!-- change password -->
                                        <div class="tab-pane fade" id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                                            <!-- form -->
                                            <form class="validate-form">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-old-password">Mot de passe actuel</label>
                                                            <div class="input-group form-password-toggle input-group-merge">
                                                                <input type="password" class="form-control" id="account-old-password" name="password" placeholder="Mot de passe actuel" />
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text cursor-pointer">
                                                                        <i data-feather="eye"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mr-1 mt-1">Mettre à jour le mot de passe</button>
                                                        <button type="reset" class="btn btn-outline-secondary mt-1">Annuler</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <!--/ form -->
                                        </div>
                                        <!--/ change password -->

                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ right content section -->
                    </div>
                </section>
                <!-- / account setting page -->
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
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/pages/page-account-settings.js"></script>
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
                
                // Initialize any profile-specific functions here
                if (typeof initSelect2 === 'function') {
                    initSelect2(); // Initialize select2 if available
                }
                
                // Initialize role dropdown with select2
                if ($('#account-role').length) {
                    $('#account-role').select2({
                        minimumResultsForSearch: Infinity
                    });
                }
            }
        });
        $(document).ready(function() {
            
            console.log("User Manager:::::", UserManager);
            // Check if user is logged in using UserManager from config.js
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            console.log("User Manager:::::", user, token);
            
            if (!token || !user) {
                // No token or user found, redirect to login page
                window.location.href = '/';
                return;
            }
            
            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);

            // Make API call to get user details
            $.ajax({
                url: '/api/auth/me',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log("User details:::::", response);
                    $('#account-name').val(response.name);
                    $('#account-e-mail').val(response.email);
                    $('#account-upload-img').attr('src', '/uploads/profile_pictures/'+response.profile_picture);
                },
                error: function(xhr, status, error) {
                        toastr.error('Erreur lors de la récupération des détails utilisateur', 'Erreur', {
                        timeOut: 8000,
                        extendedTimeOut: 3000,
                        closeButton: true,
                        progressBar: true
                    });
                        console.error("Erreur lors de la récupération des détails utilisateur:", error);
                }
            });

            // Add event listener to save profile button
            $('#save-profile-btn').on('click', function() {
                    console.log("Bouton de sauvegarde du profil cliqué");
                var name = $('#account-name').val();
                var email = $('#account-e-mail').val();
                var password = $('#account-new-password1').val();
                var confirm_password = $('#account-retype-new-password1').val();

                if (password !== confirm_password && password !== '') {
                    console.error("Les mots de passe ne correspondent pas");
                    $('#password-error-message').removeClass('hidden');
                    setTimeout(function() {
                        $('#password-error-message').addClass('hidden');
                    }, 3000);
                    return;
                }

                // console.log("Perofile picture:::::", perofile_picture);
                var data = {
                    name: name,
                    email: email,
                    password: password,
                    confirm_password: confirm_password
                }
                // Make API call to update user details
                $.ajax({
                    url: '/api/auth/me',
                    type: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: data,
                    success: function(response) {
                        console.log("Détails utilisateur mis à jour avec succès:::::", response);
                        toastr.success('Profil mis à jour avec succès', 'Profil mis à jour', {
                            timeOut: 8000,
                            extendedTimeOut: 3000,
                            closeButton: true,
                            progressBar: true
                        });
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Erreur lors de la mise à jour des détails utilisateur', 'Erreur', {
                            timeOut: 8000,
                            extendedTimeOut: 3000,
                            closeButton: true,
                            progressBar: true
                        });
                        console.error("Erreur lors de la mise à jour des détails utilisateur:", error);
                    }
                });
            });

            // Add event listener to upload profile picture button
            $('#account-upload').on('change', function() {
                const file = $(this).prop('files')[0];
                if (file) {
                    var formData = new FormData();
                    formData.append('file', file);
                    
                    $.ajax({
                        url: '/api/auth/upload-profile-picture',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        processData: false, // Prevent jQuery from processing the data
                        contentType: false, // Prevent jQuery from setting content type
                        success: function(response) {
                            console.log("Photo de profil téléchargée avec succès:", response);
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Erreur lors du téléchargement de la photo de profil', 'Erreur', {
                                timeOut: 8000,
                                extendedTimeOut: 3000,
                                closeButton: true,
                                progressBar: true
                            });
                            console.error("Erreur lors du téléchargement de la photo de profil:", error);
                        }
                    });
                } else {
                    console.error("Aucun fichier sélectionné.");
                }
            });
        });

       
    </script>
</body>
<!-- END: Body-->
</html>