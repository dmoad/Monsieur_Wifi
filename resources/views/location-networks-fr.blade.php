<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>Paramètres réseau - Contrôleur monsieur-wifi</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/working-hours/interactive-schedule.css">

    <style>
        .custom-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; color: white !important; box-shadow: 0 2px 8px rgba(102,126,234,0.3) !important; }
        .custom-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(115,103,240,0.4) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; background: #fff; margin-bottom: 1.5rem; }
        .card-header { background: linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%); border-bottom: 1px solid rgba(0,0,0,0.05); border-radius: 12px 12px 0 0 !important; padding: 1.5rem; }
        .card-title { font-weight: 600; color: #2c3e50; margin-bottom: 0; font-size: 1.1rem; }
        .card-body { padding: 1.5rem; }
        .form-control { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease; }
        .form-control:focus { border-color: #7367f0; box-shadow: 0 0 0 0.2rem rgba(115,103,240,0.15); outline: none; }
        input.form-control, .input-group .form-control { height: 50px; }
        select.form-control { height: 50px; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px 12px; padding-right: 40px; }
        textarea.form-control { height: auto !important; display: block; resize: vertical; min-height: 80px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem; display: block; }
        .btn { border-radius: 8px; padding: 10px 20px; font-weight: 500; transition: all 0.3s ease; border: none; }
        .nav-tabs { border: none; background: #f8f9fa; border-radius: 12px; padding: 8px; margin-bottom: 2rem; }
        .nav-tabs .nav-item { margin-bottom: 0; }
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 8px; transition: all 0.3s ease; margin-right: 4px; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-tabs .nav-link:hover { background: rgba(115,103,240,0.1); color: #7367f0; text-decoration: none; }
        .nav-tabs .nav-link.active { background: linear-gradient(135deg,#7367f0 0%,#9c88ff 100%); color: white; box-shadow: 0 4px 15px rgba(115,103,240,0.3); }
        .content-section { background: #fff; border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 20px rgba(0,0,0,0.08); }
        .section-title { font-size: 1.1rem; font-weight: 600; color: #2c3e50; margin: 0 0 1rem; }
        .mac-address-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-bottom: 1px solid #f1f3f4; }
        .mac-address-item:last-child { border-bottom: none; }

        /* Network type badges */
        .network-type-badge { font-size: 0.7rem; padding: 2px 7px; border-radius: 10px; font-weight: 600; text-transform: uppercase; margin-left: 6px; }
        .network-type-password { background: rgba(115,103,240,0.15); color: #7367f0; }
        .network-type-captive_portal { background: rgba(40,199,111,0.15); color: #28c76f; }
        .network-type-open { background: rgba(255,159,67,0.15); color: #ff9f43; }
        .network-section { display: none; }
        .network-section.active { display: block; }

        /* Collapsible advanced sections */
        .collapsible-section { border: 1px solid #e9ecef; border-radius: 10px; margin-bottom: 1rem; overflow: hidden; }
        .collapsible-section-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; cursor: pointer; background: #f8f9fa; user-select: none; transition: background 0.2s ease; }
        .collapsible-section-header:hover { background: #eef0f4; }
        .collapsible-section-title { font-size: 0.95rem; font-weight: 600; color: #2c3e50; display: flex; align-items: center; gap: 8px; margin: 0; }
        .collapsible-section-title .section-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .collapsible-chevron { transition: transform 0.25s ease; color: #6c757d; }
        .collapsible-section.is-open .collapsible-chevron { transform: rotate(180deg); }
        .collapsible-section-body { padding: 1.25rem 1.25rem 0.5rem; border-top: 1px solid #e9ecef; }
        .dark-layout .collapsible-section { border-color: #3b4253; }
        .dark-layout .collapsible-section-header { background: #1e2a3c; }
        .dark-layout .collapsible-section-header:hover { background: #243040; }
        .dark-layout .collapsible-section-title { color: #d0d2d6; }
        .dark-layout .collapsible-section-body { border-top-color: #3b4253; }
        .semi-dark-layout .collapsible-section { border-color: #3b4253; }
        .semi-dark-layout .collapsible-section-header { background: #1e2a3c; }
        .semi-dark-layout .collapsible-section-header:hover { background: #243040; }
        .semi-dark-layout .collapsible-section-title { color: #d0d2d6; }
        .semi-dark-layout .collapsible-section-body { border-top-color: #3b4253; }

        /* Network tabs header with Add button */
        .networks-header-bar { display: flex; align-items: center; justify-content: space-between; background: #f8f9fa; border-radius: 12px; padding: 12px 16px; margin-bottom: 1.5rem; }
        .networks-header-bar .nav-tabs { margin-bottom: 0; background: transparent; padding: 0; flex: 1; }
        #add-network-btn { white-space: nowrap; margin-left: 1rem; flex-shrink: 0; }

        /* Back breadcrumb */
        .back-btn { display: inline-flex; align-items: center; gap: 6px; color: #7367f0; font-weight: 500; text-decoration: none; margin-bottom: 0; }
        .back-btn:hover { color: #9c88ff; text-decoration: none; }

        /* VLAN global toggle */
        .vlan-global-bar { background: linear-gradient(135deg,#f0f0ff 0%,#e8e8ff 100%); border-radius: 10px; padding: 14px 20px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(115,103,240,0.15); }

        /* ── Network identity bar ── */
        .network-identity-bar { display: flex; align-items: center; gap: 0; background: #f8f9fa; border-radius: 10px; padding: 6px; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 6px; }
        .network-type-pill-group { display: flex; gap: 4px; background: #fff; border-radius: 8px; padding: 4px; border: 1px solid #e9ecef; flex-shrink: 0; }
        .network-type-pill { border: none; background: transparent; border-radius: 6px; padding: 6px 14px; font-size: 0.82rem; font-weight: 600; color: #6c757d; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; display: flex; align-items: center; gap: 5px; }
        .network-type-pill:hover { color: #495057; background: #f1f3f5; }
        .network-type-pill.active-password { background: rgba(115,103,240,0.12); color: #7367f0; }
        .network-type-pill.active-captive_portal { background: rgba(40,199,111,0.12); color: #28c76f; }
        .network-type-pill.active-open { background: rgba(255,159,67,0.12); color: #ff9f43; }
        .network-identity-divider { width: 1px; height: 32px; background: #dee2e6; flex-shrink: 0; }
        .network-ssid-wrap { flex: 1; min-width: 160px; position: relative; }
        .network-ssid-wrap .ssid-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; pointer-events: none; }
        .network-ssid-wrap input { padding-left: 36px !important; height: 42px; border-radius: 8px !important; font-weight: 600; }
        .network-visibility-wrap { display: flex; align-items: center; gap: 8px; flex-shrink: 0; background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 0 14px; height: 42px; }
        .network-visibility-wrap select { border: none !important; background: transparent !important; padding: 0 !important; height: auto !important; font-size: 0.85rem; font-weight: 500; color: #495057; min-width: 130px; }
        .network-visibility-wrap select:focus { box-shadow: none !important; }
        .network-enabled-wrap { display: flex; align-items: center; gap: 8px; flex-shrink: 0; background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 0 14px; height: 42px; font-size: 0.85rem; font-weight: 600; color: #495057; }

        /* ── Type-specific section panels ── */
        .network-type-panel { border-radius: 10px; border: 1px solid #e9ecef; margin-bottom: 1rem; overflow: hidden; }
        .network-type-panel-header { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-bottom: 1px solid #e9ecef; }
        .network-type-panel-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .network-type-panel-title { font-size: 0.9rem; font-weight: 700; margin: 0; letter-spacing: 0.01em; }
        .network-type-panel-body { padding: 1.25rem; }
        .network-type-panel-body .form-group { margin-bottom: 1rem; }
        .network-type-panel-body .form-group:last-child { margin-bottom: 0; }

        /* Password panel accent */
        .panel-password { border-color: rgba(115,103,240,0.25); }
        .panel-password .network-type-panel-header { background: rgba(115,103,240,0.05); border-bottom-color: rgba(115,103,240,0.15); }
        .panel-password .network-type-panel-icon { background: rgba(115,103,240,0.12); }
        .panel-password .network-type-panel-title { color: #7367f0; }

        /* Captive portal panel accent */
        .panel-captive { border-color: rgba(40,199,111,0.25); }
        .panel-captive .network-type-panel-header { background: rgba(40,199,111,0.05); border-bottom-color: rgba(40,199,111,0.15); }
        .panel-captive .network-type-panel-icon { background: rgba(40,199,111,0.12); }
        .panel-captive .network-type-panel-title { color: #28c76f; }

        /* Open panel accent */
        .panel-open { border-color: rgba(255,159,67,0.25); }
        .panel-open .network-type-panel-header { background: rgba(255,159,67,0.05); border-bottom-color: rgba(255,159,67,0.15); }
        .panel-open .network-type-panel-icon { background: rgba(255,159,67,0.12); }
        .panel-open .network-type-panel-title { color: #ff9f43; }

        /* Sub-section dividers inside panels */
        .panel-sub-section { border-top: 1px solid #f1f3f4; padding-top: 1rem; margin-top: 1rem; }
        .panel-sub-label { font-size: 0.75rem; font-weight: 700; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem; }

        /* IP/DHCP panel accent (blue-grey) */
        .panel-ip { border-color: rgba(23,162,184,0.25); }
        .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.05); border-bottom-color: rgba(23,162,184,0.15); }
        .panel-ip .network-type-panel-icon { background: rgba(23,162,184,0.12); }
        .panel-ip .network-type-panel-title { color: #17a2b8; }

        /* MAC filtering panel accent (red) */
        .panel-mac { border-color: rgba(234,84,85,0.25); }
        .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.05); border-bottom-color: rgba(234,84,85,0.15); }
        .panel-mac .network-type-panel-icon { background: rgba(234,84,85,0.12); }
        .panel-mac .network-type-panel-title { color: #ea5455; }

        /* MAC list */
        .mac-list-box { border: 1px solid #e9ecef; border-radius: 8px; max-height: 180px; overflow-y: auto; min-height: 48px; }
        .mac-address-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 14px; border-bottom: 1px solid #f1f3f4; font-size: 0.88rem; font-family: monospace; }
        .mac-address-item:last-child { border-bottom: none; }

        /* Input-group button height match */
        .input-group-append .btn, .input-group-prepend .btn { height: 50px; }

        /* Dark mode additions for new panels */
        .dark-layout .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.08); }
        .dark-layout .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.08); }
        .dark-layout .mac-list-box { border-color: #3b4253; }
        .dark-layout .mac-address-item { border-bottom-color: #3b4253; }
        .dark-layout .panel-sub-section { border-top-color: #3b4253; }
        .semi-dark-layout .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.08); }
        .semi-dark-layout .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.08); }
        .semi-dark-layout .mac-list-box { border-color: #3b4253; }
        .semi-dark-layout .mac-address-item { border-bottom-color: #3b4253; }

        /* Auth method pills */
        .auth-method-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 1rem; }
        .auth-method-pill { border: 1.5px solid #e9ecef; background: #fff; border-radius: 20px; padding: 5px 14px; font-size: 0.8rem; font-weight: 600; color: #6c757d; cursor: pointer; transition: all 0.2s; }
        .auth-method-pill:hover { border-color: #28c76f; color: #28c76f; }
        .auth-method-pill.active { border-color: #28c76f; background: rgba(40,199,111,0.1); color: #28c76f; }

        /* Dark mode additions */
        .dark-layout .network-identity-bar { background: #1e2a3c; }
        .dark-layout .network-type-pill-group { background: #283046; border-color: #3b4253; }
        .dark-layout .network-type-pill { color: #b4b7bd; }
        .dark-layout .network-identity-divider { background: #3b4253; }
        .dark-layout .network-visibility-wrap,
        .dark-layout .network-enabled-wrap { background: #283046; border-color: #3b4253; color: #d0d2d6; }
        .dark-layout .network-visibility-wrap select { color: #d0d2d6; }
        .dark-layout .network-type-panel { border-color: #3b4253; }
        .dark-layout .network-type-panel-header { border-bottom-color: #3b4253; }
        .dark-layout .panel-password .network-type-panel-header { background: rgba(115,103,240,0.08); }
        .dark-layout .panel-captive .network-type-panel-header { background: rgba(40,199,111,0.08); }
        .dark-layout .panel-open .network-type-panel-header { background: rgba(255,159,67,0.08); }
        .dark-layout .panel-sub-section { border-top-color: #3b4253; }
        .dark-layout .auth-method-pill { background: #283046; border-color: #3b4253; color: #b4b7bd; }
        .semi-dark-layout .network-identity-bar { background: #1e2a3c; }
        .semi-dark-layout .network-type-pill-group { background: #283046; border-color: #3b4253; }
        .semi-dark-layout .network-type-pill { color: #b4b7bd; }
        .semi-dark-layout .network-visibility-wrap,
        .semi-dark-layout .network-enabled-wrap { background: #283046; border-color: #3b4253; color: #d0d2d6; }
        .semi-dark-layout .network-visibility-wrap select { color: #d0d2d6; }
        .semi-dark-layout .network-type-panel { border-color: #3b4253; }
        .semi-dark-layout .network-type-panel-header { border-bottom-color: #3b4253; }
        .semi-dark-layout .panel-sub-section { border-top-color: #3b4253; }

        /* Dark layout */
        .dark-layout .nav-tabs { background-color: #283046 !important; }
        .dark-layout .nav-tabs .nav-link { color: #b4b7bd !important; }
        .dark-layout .nav-tabs .nav-link.active { color: #ffffff !important; }
        .dark-layout .form-group label { color: #d0d2d6 !important; }
        .dark-layout .card-header { background: linear-gradient(135deg,#283046 0%,#2c2c2c 100%) !important; border-bottom: 1px solid rgba(180,183,189,0.3) !important; }
        .dark-layout h4, .dark-layout h5, .dark-layout h6 { color: #d0d2d6 !important; }
        .dark-layout .form-control { background-color: #3b4253 !important; border-color: #3b4253 !important; color: #d0d2d6 !important; }
        .dark-layout .content-section { background-color: #283046 !important; border: 1px solid #3b4253 !important; }
        .semi-dark-layout .nav-tabs { background-color: #283046 !important; }
        .semi-dark-layout .nav-tabs .nav-link { color: #b4b7bd !important; }
        .semi-dark-layout .nav-tabs .nav-link.active { color: #ffffff !important; }
        .semi-dark-layout .form-group label { color: #d0d2d6 !important; }
        .semi-dark-layout .card-header { background: linear-gradient(135deg,#283046 0%,#2c2c2c 100%) !important; border-bottom: 1px solid rgba(180,183,189,0.3) !important; }
        .semi-dark-layout .form-control { background-color: #3b4253 !important; border-color: #3b4253 !important; color: #d0d2d6 !important; }
        .semi-dark-layout .content-section { background-color: #283046 !important; border: 1px solid #3b4253 !important; }

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
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a>
                </li>
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                        <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/profile"><i class="mr-50" data-feather="user"></i> Profil</a>
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
                    <a class="navbar-brand" href="/dashboard">
                        <span class="brand-logo"><img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Mr WiFi logo"></span>
                        <h2 class="brand-text">Mr WiFi</h2>
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
                <li class="navigation-header"><span>Gestion</span></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Tableau de bord</span></a></li>
                <li class="nav-item active"><a class="d-flex align-items-center" href="/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">Emplacements</span></a></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">Portails captifs</span></a></li>
                <li class="navigation-header only_admin hidden"><span>Administration</span></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">Comptes</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">Blocage de domaines</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Micrologiciel</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">Paramètres système</span></a></li>
                <li class="navigation-header"><span>Compte</span></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/profile"><i data-feather="user"></i><span class="menu-title text-truncate">Profil</span></a></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">Déconnexion</span></a></li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <!-- Page header -->
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Paramètres réseau</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Accueil</a></li>
                                    <li class="breadcrumb-item"><a href="/locations">Emplacements</a></li>
                                    <li class="breadcrumb-item"><a id="breadcrumb-location-link" href="#">Chargement...</a></li>
                                    <li class="breadcrumb-item active">Réseaux</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <a id="back-to-location-btn" href="#" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Retour à l'emplacement
                    </a>
                </div>
            </div>

            <div class="content-body">

                <!-- Location info strip -->
                <div class="card mb-2" style="border-radius:10px;">
                    <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;">
                                <i data-feather="map-pin" style="color:white;width:20px;height:20px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 location_name" style="font-weight:700; padding-left:10px;">Chargement...</h5>
                                <small class="text-muted location_address" style="padding-left:10px;"></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <!-- VLAN global toggle -->
                            <div class="d-flex align-items-center" style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:6px 14px;gap:10px;">
                                <i data-feather="layers" style="width:14px;height:14px;color:#7367f0;flex-shrink:0;"></i>
                                <span style="font-size:0.85rem;font-weight:600;color:#495057;white-space:nowrap;">Support VLAN</span>
                                <div class="custom-control custom-switch mb-0">
                                    <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                                    <label class="custom-control-label" for="vlan-enabled"></label>
                                </div>
                            </div>
                            <button class="btn custom-btn btn-sm" id="add-network-btn" disabled style="height:36px;white-space:nowrap;">
                                <i data-feather="plus" class="mr-1"></i> Ajouter un réseau
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Network tabs nav -->
                <ul class="nav nav-tabs" role="tablist" id="network-tabs-nav">
                    <li class="nav-item" id="network-tabs-loading">
                        <span class="nav-link disabled"><i class="fas fa-spinner fa-spin mr-1"></i>Chargement des réseaux…</span>
                    </li>
                </ul>

                <!-- Network tab panes (populated by JS) -->
                <div class="tab-content" id="network-tabs-content">
                    <!-- injected by NetworkManager -->
                </div>

            </div><!-- end .content-body -->
        </div><!-- end .content-wrapper -->
    </div><!-- end .app-content -->
    <!-- END: Content-->

    <!-- ============================================================
         HIDDEN TEMPLATES
    ============================================================ -->

    <!-- Tab nav item template -->
    <template id="network-tab-tpl">
        <li class="nav-item" data-network-id="__ID__">
            <a class="nav-link" id="network-tab-__ID__" data-toggle="tab" href="#network-pane-__ID__" role="tab">
                <i data-feather="wifi" class="mr-1"></i>
                <span class="network-tab-label">Réseau</span>
                <span class="network-type-badge network-type-__TYPE__">__TYPE_LABEL__</span>
            </a>
        </li>
    </template>

    <!-- Tab pane template -->
    <template id="network-pane-tpl">
        <div class="tab-pane fade" id="network-pane-__ID__" role="tabpanel" data-network-id="__ID__">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><span class="network-pane-title">Réseau</span></h4>
                    <div class="d-flex align-items-center gap-2" style="gap:0.5rem;">
                        <button class="btn btn-outline-danger btn-sm network-delete-btn" data-network-id="__ID__">
                            <i data-feather="trash-2" class="mr-1"></i> Supprimer le réseau
                        </button>
                        <button class="btn custom-btn network-save-btn" data-network-id="__ID__">
                            <i data-feather="save" class="mr-1"></i> Enregistrer les paramètres
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Network identity bar: type pills + SSID + visibility + enabled -->
                    <div class="network-identity-bar mt-1">
                        <!-- Hidden select keeps existing JS logic intact -->
                        <select class="network-type-select d-none" data-network-id="__ID__">
                            <option value="password">WiFi avec mot de passe</option>
                            <option value="captive_portal">Portail captif</option>
                            <option value="open">ESSID ouvert</option>
                        </select>
                        <!-- Visual type pill switcher -->
                        <div class="network-type-pill-group">
                            <button type="button" class="network-type-pill" data-type="password">
                                <i data-feather="lock" style="width:13px;height:13px;"></i> Mot de passe
                            </button>
                            <button type="button" class="network-type-pill" data-type="captive_portal">
                                <i data-feather="layout" style="width:13px;height:13px;"></i> Portail captif
                            </button>
                            <button type="button" class="network-type-pill" data-type="open">
                                <i data-feather="wifi" style="width:13px;height:13px;"></i> Ouvert
                            </button>
                        </div>
                        <div class="network-identity-divider mx-1"></div>
                        <!-- SSID -->
                        <div class="network-ssid-wrap">
                            <i data-feather="wifi" class="ssid-icon" style="width:15px;height:15px;"></i>
                            <input type="text" class="form-control network-ssid" placeholder="Nom du réseau (SSID)">
                        </div>
                        <div class="network-identity-divider mx-1"></div>
                        <!-- Visibility -->
                        <div class="network-visibility-wrap">
                            <i data-feather="eye" style="width:14px;height:14px;color:#adb5bd;flex-shrink:0;"></i>
                            <select class="network-visible">
                                <option value="1">Diffuser le SSID</option>
                                <option value="0">SSID masqué</option>
                            </select>
                        </div>
                        <div class="network-identity-divider mx-1"></div>
                        <!-- Enabled toggle -->
                        <div class="network-enabled-wrap">
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input network-enabled" id="network-enabled-__ID__" checked>
                                <label class="custom-control-label" for="network-enabled-__ID__">Activé</label>
                            </div>
                        </div>
                    </div>

                    <!-- ── PASSWORD section ── -->
                    <div class="network-section network-section-password">
                        <div class="network-type-panel panel-password">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="lock" style="color:#7367f0;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">Sécurité &amp; Chiffrement</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Mot de passe WiFi</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control network-password" placeholder="Minimum 8 caractères">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary network-toggle-password" type="button"><i data-feather="eye"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Protocole de sécurité</label>
                                            <select class="form-control network-security">
                                                <option value="wpa2-psk" selected>WPA2-PSK (Recommandé)</option>
                                                <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixte</option>
                                                <option value="wpa3-psk">WPA3-PSK (Plus sécurisé)</option>
                                                <option value="wep">WEP (Hérité)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Suites de chiffrement</label>
                                            <select class="form-control network-cipher-suites">
                                                <option value="CCMP" selected>CCMP</option>
                                                <option value="TKIP">TKIP</option>
                                                <option value="TKIP+CCMP">TKIP+CCMP</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── CAPTIVE PORTAL section ── -->
                    <div class="network-section network-section-captive_portal">
                        <div class="network-type-panel panel-captive">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="layout" style="color:#28c76f;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">Configuration du portail captif</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="panel-sub-label">Authentification</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Méthode</label>
                                            <select class="form-control network-auth-method">
                                                <option value="click-through" selected>Clic (sans authentification)</option>
                                                <option value="password">Authentification par mot de passe</option>
                                                <option value="sms">Vérification par SMS</option>
                                                <option value="email">Vérification par e-mail</option>
                                                <option value="social">Connexion via les réseaux sociaux</option>
                                            </select>
                                        </div>
                                        <div class="form-group network-captive-password-group" style="display:none;">
                                            <label>Mot de passe partagé</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control network-portal-password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary network-toggle-portal-password" type="button"><i data-feather="eye"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group network-social-group" style="display:none;">
                                            <label>Réseau social</label>
                                            <select class="form-control network-social-method">
                                                <option value="facebook">Facebook</option>
                                                <option value="google">Google</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Design du portail</label>
                                            <select class="form-control network-portal-design-id">
                                                <option value="">Design par défaut</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>URL de redirection <small class="text-muted font-weight-normal">(optionnel)</small></label>
                                            <input type="url" class="form-control network-redirect-url" placeholder="https://exemple.com">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Délai de session</label>
                                            <select class="form-control network-session-timeout">
                                                <option value="60">1 Heure</option><option value="120">2 Heures</option><option value="180">3 Heures</option>
                                                <option value="240">4 Heures</option><option value="300">5 Heures</option><option value="360">6 Heures</option>
                                                <option value="720">12 Heures</option><option value="1440">1 Jour</option><option value="10080">1 Semaine</option>
                                                <option value="43200">3 Mois</option><option value="172800">1 An</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Délai d'inactivité</label>
                                            <select class="form-control network-idle-timeout">
                                                <option value="15">15 Minutes</option><option value="30">30 Minutes</option><option value="45">45 Minutes</option>
                                                <option value="60">1 Heure</option><option value="120">2 Heures</option><option value="240">4 Heures</option>
                                                <option value="720">12 Heures</option><option value="1440">1 Jour</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-sub-section">
                                    <div class="panel-sub-label">Limites de bande passante</div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><i data-feather="download" style="width:13px;height:13px;margin-right:4px;"></i>Téléchargement (Mbps)</label>
                                                <input type="number" class="form-control network-download-limit" placeholder="Illimité" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><i data-feather="upload" style="width:13px;height:13px;margin-right:4px;"></i>Envoi (Mbps)</label>
                                                <input type="number" class="form-control network-upload-limit" placeholder="Illimité" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible: Working Hours (captive portal only) -->
                    <div class="collapsible-section network-section network-section-captive_portal" data-collapse-id="working-hours-__ID__">
                        <div class="collapsible-section-header" data-target="working-hours-__ID__">
                            <h6 class="collapsible-section-title">
                                <span class="section-icon" style="background:rgba(40,199,111,0.12);">
                                    <i data-feather="clock" style="color:#28c76f;width:14px;height:14px;"></i>
                                </span>
                                Heures de travail
                            </h6>
                            <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                        </div>
                        <div class="collapsible-section-body" id="working-hours-__ID__" style="display:none;">
                            <div class="network-schedule-wrapper" id="schedule-wrapper-__ID__" style="padding: 0.75rem 0 0.5rem;"></div>
                        </div>
                    </div>

                    <!-- ── OPEN section ── -->
                    <div class="network-section network-section-open">
                        <div class="network-type-panel panel-open">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="wifi" style="color:#ff9f43;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">Réseau ouvert</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="d-flex align-items-start" style="gap:12px;">
                                    <i data-feather="alert-triangle" style="width:20px;height:20px;color:#ff9f43;flex-shrink:0;margin-top:1px;"></i>
                                    <div>
                                        <p class="mb-1" style="font-weight:600;color:#ff9f43;font-size:0.95rem;">Aucune authentification requise</p>
                                        <p class="mb-0 text-muted" style="font-size:0.88rem;">Toute personne à portée peut se connecter sans mot de passe ni portail. À utiliser uniquement dans des environnements de confiance.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible: IP & DHCP Settings -->
                    <div class="collapsible-section" data-collapse-id="ip-dhcp-__ID__">
                        <div class="collapsible-section-header" data-target="ip-dhcp-__ID__">
                            <h6 class="collapsible-section-title">
                                <span class="section-icon" style="background:rgba(23,162,184,0.12);">
                                    <i data-feather="server" style="color:#17a2b8;width:14px;height:14px;"></i>
                                </span>
                                Paramètres IP &amp; DHCP
                            </h6>
                            <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                        </div>
                        <div class="collapsible-section-body" id="ip-dhcp-__ID__" style="display:none;">
                            <div class="network-type-panel panel-ip mt-2">
                                <div class="network-type-panel-header">
                                    <span class="network-type-panel-icon">
                                        <i data-feather="globe" style="color:#17a2b8;width:16px;height:16px;"></i>
                                    </span>
                                    <h6 class="network-type-panel-title">Configuration IP</h6>
                                </div>
                                <div class="network-type-panel-body">
                                    <div class="panel-sub-label">Adressage</div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Mode IP</label>
                                                <select class="form-control network-ip-mode">
                                                    <option value="static">IP statique</option>
                                                    <option value="dhcp">Client DHCP</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Adresse IP</label>
                                                <input type="text" class="form-control network-ip-address" placeholder="192.168.x.1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Masque de sous-réseau</label>
                                                <input type="text" class="form-control network-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Passerelle</label>
                                                <input type="text" class="form-control network-gateway" placeholder="Auto">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>DNS primaire</label>
                                                <input type="text" class="form-control network-dns1" placeholder="8.8.8.8" value="8.8.8.8">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>DNS alt.</label>
                                                <input type="text" class="form-control network-dns2" placeholder="8.8.4.4" value="8.8.4.4">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-sub-section">
                                        <div class="panel-sub-label">Serveur DHCP</div>
                                        <div class="row align-items-end">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div class="d-flex align-items-center" style="height:50px;">
                                                        <div class="custom-control custom-switch mb-0">
                                                            <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                                            <label class="custom-control-label" for="network-dhcp-__ID__">Activer le DHCP</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Plage DHCP</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control network-dhcp-start" placeholder="x.x.x.100">
                                                        <div class="input-group-prepend input-group-append"><span class="input-group-text">–</span></div>
                                                        <input type="text" class="form-control network-dhcp-end" placeholder="x.x.x.200">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-sub-section">
                                        <div class="panel-sub-label">VLAN</div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>ID VLAN <small class="text-muted font-weight-normal">(1–4094)</small></label>
                                                    <input type="number" class="form-control network-vlan-id" placeholder="Aucun" min="1" max="4094" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Marquage</label>
                                                    <select class="form-control network-vlan-tagging" disabled>
                                                        <option value="disabled">Désactivé</option>
                                                        <option value="tagged">Marqué</option>
                                                        <option value="untagged">Non marqué</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible: MAC Address Filtering -->
                    <div class="collapsible-section" data-collapse-id="mac-filter-__ID__">
                        <div class="collapsible-section-header" data-target="mac-filter-__ID__">
                            <h6 class="collapsible-section-title">
                                <span class="section-icon" style="background:rgba(234,84,85,0.12);">
                                    <i data-feather="shield" style="color:#ea5455;width:14px;height:14px;"></i>
                                </span>
                                Filtrage des adresses MAC
                            </h6>
                            <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                        </div>
                        <div class="collapsible-section-body" id="mac-filter-__ID__" style="display:none;">
                            <div class="network-type-panel panel-mac mt-2">
                                <div class="network-type-panel-header">
                                    <span class="network-type-panel-icon">
                                        <i data-feather="shield" style="color:#ea5455;width:16px;height:16px;"></i>
                                    </span>
                                    <h6 class="network-type-panel-title">Filtrage des adresses MAC</h6>
                                </div>
                                <div class="network-type-panel-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="panel-sub-label">Mode de filtrage</div>
                                            <div class="form-group">
                                                <select class="form-control network-mac-filter-mode">
                                                    <option value="allow-all">Autoriser tous les appareils</option>
                                                    <option value="allow-listed">Autoriser uniquement la liste</option>
                                                    <option value="block-listed">Bloquer les appareils listés</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="panel-sub-label">Liste des adresses MAC</div>
                                            <div class="form-group mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control network-mac-input" placeholder="00:11:22:33:44:55">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary network-mac-add-btn" type="button">
                                                            <i data-feather="plus" style="width:14px;height:14px;"></i> Ajouter
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mac-list-box">
                                                <div class="network-mac-list"></div>
                                                <div class="text-center text-muted p-3 network-mac-empty">
                                                    <i data-feather="inbox" style="width:18px;height:18px;margin-bottom:4px;display:block;margin-left:auto;margin-right:auto;"></i>
                                                    <small>Aucune adresse MAC ajoutée</small>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                                    <i data-feather="save" class="mr-1"></i> Enregistrer le filtrage MAC
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- end .card-body -->
            </div><!-- end .card -->
        </div><!-- end .tab-pane -->
    </template>

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <!-- END: Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>

    <!-- Working Hours Scheduler -->
    <script src="/working-hours/interactive-schedule.js"></script>

    <!-- App JS -->
    <script src="/assets/js/config.js?v=1"></script>

    <script>
        window.APP_CONFIG_V5 = {
            maxNetworks: {{ (int) env('MAX_NETWORKS_PER_LOCATION', 4) }},
            apiBase: '{{ rtrim(config("app.url"), "/") }}/api',
            messages: {
                networkSaved:       'Paramètres réseau enregistrés.',
                routerReconfigure:  'Configuration du routeur mise à jour — l\'appareil va se reconfigurer.',
                workingHoursSaved:  'Heures de travail enregistrées.',
                macFilterSaved:     'Paramètres de filtrage MAC enregistrés.',
                networkAdded:       'Réseau ajouté.',
                networkDeleted:     'Réseau supprimé.',
                invalidMac:         'Format d\'adresse MAC invalide.',
                savingSchedule:     'Enregistrement…',
            },
            schedulerLabels: {
                title:              'Heures de travail',
                subtitle:           'Horaires d\'accès au portail captif',
                quickSet:           'Accès rapide :',
                businessHours:      'Heures ouvrables',
                clearAll:           'Tout effacer',
                saveSchedule:       'Enregistrer l\'horaire',
                hint:               'Cliquez sur une cellule vide pour créer un créneau. Glissez pour déplacer, redimensionnez avec les poignées, survolez pour supprimer.',
                days: {
                    monday:    'Lundi',
                    tuesday:   'Mardi',
                    wednesday: 'Mercredi',
                    thursday:  'Jeudi',
                    friday:    'Vendredi',
                    saturday:  'Samedi',
                    sunday:    'Dimanche',
                },
                msgOverlap:         'Impossible de créer le créneau : chevauchement avec un créneau existant.',
                msgInvalidMove:     'Position invalide : le créneau chevaucherait ou dépasserait les limites.',
                msgInvalidResize:   'Redimensionnement invalide : chevaucherait ou dépasserait les limites.',
                msgBusinessApplied: 'Heures ouvrables appliquées.',
                msgCleared:         'Tous les créneaux effacés.',
                msgSaved:           'Horaire enregistré !',
            },
        };
    </script>

    <script src="/assets/js/location-networks-v5.js?v=1"></script>

</body>
</html>
