<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Location Details - monsieur-wifi Controller</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <!-- END: Page CSS-->
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->

    <style>
        .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease; }
        .custom-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; color: white !important; box-shadow: 0 2px 8px rgba(102,126,234,0.3) !important; }
        .status-online { background: linear-gradient(45deg,#28c76f,#48da89); color: white; box-shadow: 0 2px 8px rgba(40,199,111,0.3); }
        .status-offline { background: linear-gradient(45deg,#ea5455,#ff6b6b); color: white; box-shadow: 0 2px 8px rgba(234,84,85,0.3); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; background: #fff; margin-bottom: 1.5rem; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .card-header { background: linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%); border-bottom: 1px solid rgba(0,0,0,0.05); border-radius: 12px 12px 0 0 !important; padding: 1.5rem; }
        .card-title { font-weight: 600; color: #2c3e50; margin-bottom: 0; font-size: 1.1rem; }
        .card-body { padding: 1.5rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: linear-gradient(135deg,#fff 0%,#f8f9fa 100%); border-radius: 12px; padding: 1.5rem; border-left: 4px solid #7367f0; transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2rem; font-weight: 700; color: #2c3e50; margin-bottom: 0.5rem; }
        .stat-label { color: #6c757d; font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease; }
        .form-control:focus { border-color: #7367f0; box-shadow: 0 0 0 0.2rem rgba(115,103,240,0.15); outline: none; }
        input.form-control { height: 50px; }
        select.form-control { height: 50px; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px 12px; padding-right: 40px; }
        textarea.form-control { height: auto !important; display: block; resize: vertical; min-height: 80px; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem; display: block; }
        .btn { border-radius: 8px; padding: 10px 20px; font-weight: 500; transition: all 0.3s ease; border: none; }

        /* ── Shared panel design system (mirrors location-networks-v5) ── */
        .loc-panel { border-radius: 10px; border: 1px solid #e9ecef; margin-bottom: 1rem; overflow: hidden; }
        .loc-panel-header { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-bottom: 1px solid #e9ecef; }
        .loc-panel-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .loc-panel-title { font-size: 0.9rem; font-weight: 700; margin: 0; letter-spacing: 0.01em; }
        .loc-panel-body { padding: 1.25rem; }
        .loc-panel-body .form-group { margin-bottom: 1rem; }
        .loc-panel-body .form-group:last-child { margin-bottom: 0; }
        .panel-sub-section { border-top: 1px solid #f1f3f4; padding-top: 1rem; margin-top: 1rem; }
        .panel-sub-label { font-size: 0.75rem; font-weight: 700; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem; }

        /* Location info panel — purple */
        .panel-location { border-color: rgba(115,103,240,0.25); }
        .panel-location .loc-panel-header { background: rgba(115,103,240,0.05); border-bottom-color: rgba(115,103,240,0.15); }
        .panel-location .loc-panel-icon { background: rgba(115,103,240,0.12); }
        .panel-location .loc-panel-title { color: #7367f0; }

        /* Contact panel — teal */
        .panel-contact { border-color: rgba(23,162,184,0.25); }
        .panel-contact .loc-panel-header { background: rgba(23,162,184,0.05); border-bottom-color: rgba(23,162,184,0.15); }
        .panel-contact .loc-panel-icon { background: rgba(23,162,184,0.12); }
        .panel-contact .loc-panel-title { color: #17a2b8; }

        /* Settings panel — green */
        .panel-settings { border-color: rgba(40,199,111,0.25); }
        .panel-settings .loc-panel-header { background: rgba(40,199,111,0.05); border-bottom-color: rgba(40,199,111,0.15); }
        .panel-settings .loc-panel-icon { background: rgba(40,199,111,0.12); }
        .panel-settings .loc-panel-title { color: #28c76f; }

        /* Form action bar */
        .form-action-bar { display: flex; align-items: center; gap: 8px; background: #f8f9fa; border-radius: 10px; padding: 14px 18px; margin-top: 1.25rem; border: 1px solid #e9ecef; }

        /* Dark mode */
        .dark-layout .loc-panel { border-color: #3b4253; }
        .dark-layout .loc-panel-header { border-bottom-color: #3b4253; }
        .dark-layout .panel-location .loc-panel-header { background: rgba(115,103,240,0.08); }
        .dark-layout .panel-contact .loc-panel-header { background: rgba(23,162,184,0.08); }
        .dark-layout .panel-settings .loc-panel-header { background: rgba(40,199,111,0.08); }
        .dark-layout .panel-sub-section { border-top-color: #3b4253; }
        .dark-layout .form-action-bar { background: #1e2a3c; border-color: #3b4253; }
        .semi-dark-layout .loc-panel { border-color: #3b4253; }
        .semi-dark-layout .loc-panel-header { border-bottom-color: #3b4253; }
        .semi-dark-layout .panel-location .loc-panel-header { background: rgba(115,103,240,0.08); }
        .semi-dark-layout .panel-contact .loc-panel-header { background: rgba(23,162,184,0.08); }
        .semi-dark-layout .panel-settings .loc-panel-header { background: rgba(40,199,111,0.08); }
        .semi-dark-layout .panel-sub-section { border-top-color: #3b4253; }
        .semi-dark-layout .form-action-bar { background: #1e2a3c; border-color: #3b4253; }
        .custom-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(115,103,240,0.4) !important; }
        .nav-tabs { border: none; background: #f8f9fa; border-radius: 12px; padding: 8px; margin-bottom: 2rem; }
        .nav-tabs .nav-item { margin-bottom: 0; }
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 8px; transition: all 0.3s ease; margin-right: 4px; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-tabs .nav-link:hover { background: rgba(115,103,240,0.1); color: #7367f0; transform: translateY(-1px); text-decoration: none; }
        .nav-tabs .nav-link.active { background: linear-gradient(135deg,#7367f0 0%,#9c88ff 100%); color: white; box-shadow: 0 4px 15px rgba(115,103,240,0.3); }
        .content-section { background: #fff; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 20px rgba(0,0,0,0.08); }
        .section-header { display: flex; justify-content: between; align-items: center; padding-bottom: 1rem; margin-bottom: 1.5rem; border-bottom: 2px solid #f1f3f4; }
        .section-title { font-size: 1.1rem; font-weight: 600; color: #2c3e50; margin: 0; }
        .interface-detail { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f3f4; }
        .interface-detail:last-child { border-bottom: none; }
        .interface-label { color: #6c757d; font-size: 0.9rem; font-weight: 500; }
        .interface-value { color: #2c3e50; font-weight: 600; }
        .text-gradient { background: linear-gradient(135deg,#7367f0 0%,#9c88ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .location-map { height: 200px; border-radius: 8px; background: #f1f3f4; }
        .pppoe_display { display: none; }
        .static_ip_display { display: none; }

        /* Analytics chart */
        .analytics-chart-card { background: linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%); border-radius: 20px; padding: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); }
        .chart-header { padding: 25px 25px 20px; }
        .chart-container { background: white; margin: 0 25px 25px; border-radius: 15px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        #daily-usage-chart { height: 300px; }
        .stat-item { display: flex; align-items: center; gap: 12px; background: white; padding: 15px; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); flex: 1; }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .stat-users { background: linear-gradient(135deg,#4facfe 0%,#00f2fe 100%); color: white; }
        .stat-sessions { background: linear-gradient(135deg,#43e97b 0%,#38f9d7 100%); color: white; }
        .stat-avg { background: linear-gradient(135deg,#fa709a 0%,#fee140 100%); color: white; }
        .chart-stats { display: flex; gap: 20px; padding: 0 25px 20px; }
        .online-users-card { background: linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%); border-radius: 20px; padding: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); }
        .users-header { padding: 25px 25px 20px; }
        .users-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102,126,234,0.2); }
        .users-container { background: white; margin: 20px 25px 25px; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        #online-users-list { max-height: 350px; overflow-y: auto; flex: 1; }
        .refresh-btn { width: 40px; height: 40px; border: 1px solid rgba(0,0,0,0.1); background: white; border-radius: 10px; color: #667eea; cursor: pointer; transition: all 0.3s ease; }
        .count-number { font-size: 2rem; font-weight: 700; color: #667eea; line-height: 1; }
        .users-count { display: flex; align-items: center; gap: 8px; background: white; padding: 15px; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-top: 15px; }
        .user-item { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .user-item:hover { background: rgba(102,126,234,0.03); }
        .user-item:last-child { border-bottom: none; }
        .pagination-container { padding: 15px 20px; border-top: 1px solid rgba(0,0,0,0.05); background: #f8f9fa; border-radius: 0 0 15px 15px; }
        .pagination-controls { display: flex; justify-content: center; align-items: center; gap: 8px; }
        .pagination-btn { width: 32px; height: 32px; border: 1px solid rgba(0,0,0,0.1); background: white; border-radius: 8px; color: #667eea; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; }
        .pagination-btn:hover:not(:disabled) { background: #667eea; color: white; }
        .pagination-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* Channel scan */
        .scan-pulse-dot { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 20px; height: 20px; background-color: #7367f0; border-radius: 50%; z-index: 2; }
        .scan-pulse-ring { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 40px; height: 40px; border: 3px solid #7367f0; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%{width:30px;height:30px;opacity:1} 100%{width:80px;height:80px;opacity:0} }
        .timeline { padding-left: 0; list-style: none; margin-bottom: 0; }
        .timeline-item { position: relative; padding-left: 2rem; margin-bottom: 0.85rem; }
        .timeline-point { position: absolute; left: 0; top: 0; }
        .timeline-point-indicator { display: inline-block; height: 12px; width: 12px; border-radius: 50%; background-color: #ebe9f1; }
        .timeline-point-primary { background-color: #7367f0 !important; }
        .timeline-point-success { background-color: #28c76f !important; }
        .channel-recommendation { padding: 1rem; background-color: #f8f8f8; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 4px solid #7367f0; }
        .channel-value { font-size: 2rem; font-weight: 600; color: #5e5873; }
        .interference-meter { height: 6px; background-color: #eee; border-radius: 3px; overflow: hidden; margin-top: 4px; }
        .interference-level { height: 100%; border-radius: 3px; }
        .interference-low { background-color: #28c76f; width: 20%; }
        .interference-medium { background-color: #ff9f43; width: 50%; }
        .interference-high { background-color: #ea5455; width: 80%; }
        #channel-scan-modal .progress-bar { transition: width 0.5s linear; }

        /* Networks shortcut card */
        .networks-shortcut-card { border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 8px 30px rgba(102,126,234,0.4); position: relative; overflow: hidden; }
        .networks-shortcut-card::before { content: ''; position: absolute; top: -40px; right: -40px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.08); }
        .networks-shortcut-card::after { content: ''; position: absolute; bottom: -60px; left: -20px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05); }
        .networks-shortcut-card h4 { color: white; font-weight: 700; margin-bottom: 0.5rem; }
        .networks-shortcut-card p { color: rgba(255,255,255,0.85); margin-bottom: 1.5rem; }
        .networks-shortcut-card .btn-light { background: white; color: #764ba2; font-weight: 600; border: none; }
        .networks-shortcut-card .btn-light:hover { background: rgba(255,255,255,0.9); transform: translateY(-2px); }
        .network-summary-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.35); border-radius: 20px; padding: 4px 14px; font-size: 0.82rem; font-weight: 600; margin-right: 8px; margin-bottom: 8px; }
        .network-summary-badge.badge-password { background: rgba(255,255,255,0.95); color: #7367f0; border-color: rgba(255,255,255,0.9); }
        .network-summary-badge.badge-captive { background: rgba(40,199,111,0.85); color: #fff; border-color: rgba(40,199,111,0.6); }
        .network-summary-badge.badge-open { background: rgba(255,159,67,0.9); color: #fff; border-color: rgba(255,159,67,0.6); }
        .network-summary-badge.badge-disabled { opacity: 0.65; }

        /* Dark/semi-dark fixes */
        .dark-layout .nav-tabs { background-color: #283046 !important; }
        .dark-layout .nav-tabs .nav-link { color: #b4b7bd !important; }
        .dark-layout .nav-tabs .nav-link.active { color: #ffffff !important; }
        .dark-layout .form-group label { color: #d0d2d6 !important; }
        .dark-layout .card-header { background: linear-gradient(135deg,#283046 0%,#2c2c2c 100%) !important; border-bottom: 1px solid rgba(180,183,189,0.3) !important; }
        .dark-layout h4, .dark-layout h5, .dark-layout h6 { color: #d0d2d6 !important; }
        .dark-layout .form-control { background-color: #3b4253 !important; border-color: #3b4253 !important; color: #d0d2d6 !important; }
        .dark-layout .form-control:focus { border-color: #7367f0 !important; }
        .dark-layout .content-section { background-color: #283046 !important; border: 1px solid #3b4253 !important; }
        .semi-dark-layout .nav-tabs { background-color: #283046 !important; }
        .semi-dark-layout .nav-tabs .nav-link { color: #b4b7bd !important; }
        .semi-dark-layout .nav-tabs .nav-link.active { color: #ffffff !important; }
        .semi-dark-layout .form-group label { color: #d0d2d6 !important; }
        .semi-dark-layout .card-header { background: linear-gradient(135deg,#283046 0%,#2c2c2c 100%) !important; border-bottom: 1px solid rgba(180,183,189,0.3) !important; }
        .semi-dark-layout h4, .semi-dark-layout h5, .semi-dark-layout h6 { color: #d0d2d6 !important; }
        .semi-dark-layout .form-control { background-color: #3b4253 !important; border-color: #3b4253 !important; color: #d0d2d6 !important; }
        .semi-dark-layout .content-section { background-color: #283046 !important; border: 1px solid #3b4253 !important; }
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
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="javascript:void(0);" data-language="en"><i class="flag-icon flag-icon-us"></i> English</a>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="fr"><i class="flag-icon flag-icon-fr"></i> French</a>
                    </div>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a>
                </li>
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                        <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
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
                <li class="navigation-header"><span>Management</span></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Dashboard</span></a></li>
                <li class="nav-item active"><a class="d-flex align-items-center" href="/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">Locations</span></a></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">Captive Portals</span></a></li>
                <li class="navigation-header only_admin hidden"><span>For Admin</span></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">Accounts</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">Domain Blocking</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Firmware</span></a></li>
                <li class="nav-item only_admin hidden"><a class="d-flex align-items-center" href="/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">System Settings</span></a></li>
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/profile"><i data-feather="user"></i><span class="menu-title text-truncate">Profile</span></a></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">Logout</span></a></li>
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
                            <h2 class="content-header-title float-left mb-0">Location Details</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/locations">Locations</a></li>
                                    <li class="breadcrumb-item active"><span class="location_name">Loading...</span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <a id="manage-networks-header-btn" href="#" class="btn custom-btn">
                            <i data-feather="wifi" class="mr-1"></i> Manage Networks
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-body">

                <!-- Location Overview -->
                <div class="stats-grid">
                    <!-- Location Info Card -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="text-gradient mb-1"><span class="location_name"></span></h4>
                                <p class="text-muted mb-0"><span class="location_address"></span></p>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-muted mr-2">MAC: <span class="router_mac_address_header font-weight-bold">Loading...</span></small>
                                    <button class="btn btn-sm btn-outline-secondary p-1" id="edit-mac-btn" style="font-size: 0.7rem; line-height: 1;">
                                        <i data-feather="edit" class="mr-1" style="width: 12px; height: 12px;"></i>Edit
                                    </button>
                                </div>
                            </div>
                            <span class="status-badge status-offline">Offline</span>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="interface-detail"><span class="interface-label">Router Model</span><span class="interface-value router_model_updated"></span></div>
                                <div class="interface-detail"><span class="interface-label">MAC Address</span><span class="interface-value router_mac_address"></span></div>
                                <div class="interface-detail"><span class="interface-label">Firmware</span><span class="interface-value router_firmware"></span></div>
                                <div class="interface-detail"><span class="interface-label">Total Users</span><span class="interface-value connected_users"></span></div>
                            </div>
                            <div class="col-6">
                                <div class="interface-detail"><span class="interface-label">Daily Usage</span><span class="interface-value daily_usage"></span></div>
                                <div class="interface-detail"><span class="interface-label">Uptime</span><span class="interface-value uptime"></span></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn custom-btn btn-sm flex-fill" id="device-restart-btn"><i data-feather="refresh-cw" class="mr-1"></i> Restart</button>
                            <button class="btn btn-outline-primary btn-sm flex-fill" id="update-firmware-btn"><i data-feather="download" class="mr-1"></i> Update</button>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Current Usage</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" id="usage-period-btn">Today</button>
                                <div class="dropdown-menu dropdown-menu-right" id="usage-period-dropdown">
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="today">Today</a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="7days">Last 7 Days</a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-period="30days">Last 30 Days</a>
                                </div>
                            </div>
                        </div>
                        <div id="usage-loading" class="text-center py-3" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>
                            <small class="d-block mt-2 text-muted">Loading usage data...</small>
                        </div>
                        <div class="row text-center" id="usage-data">
                            <div class="col-6">
                                <div class="mb-3"><div class="stat-value text-primary" id="download-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">Download</div></div>
                                <div><div class="stat-value text-info" id="users-sessions-count"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">Users / Sessions</div></div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3"><div class="stat-value text-success" id="upload-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">Upload</div></div>
                                <div><div class="stat-value text-warning" id="avg-session-time"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">Avg. Session</div></div>
                            </div>
                        </div>
                        <div class="text-center mt-3"><small class="text-muted" id="usage-last-updated">Loading data...</small></div>
                    </div>

                    <!-- Map Card -->
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Location</h5>
                            <small class="text-muted" id="map-coordinates" style="display: none;"></small>
                        </div>
                        <div id="location-map" class="location-map"></div>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Analytics</h4></div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-lg-8">
                                        <div class="analytics-chart-card">
                                            <div class="chart-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="chart-icon" style="background: linear-gradient(135deg,#667eea,#764ba2); width:50px; height:50px; border-radius:15px; display:flex; align-items:center; justify-content:center;">
                                                            <i data-feather="bar-chart-2" style="color:white;"></i>
                                                        </div>
                                                        <div>
                                                            <h5 style="margin:0; font-weight:600; color:#2c3e50;">Daily Usage Analytics</h5>
                                                            <p style="margin:0; color:#6c757d; font-size:0.9rem;">Captive Portal User Activity</p>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex" style="background:rgba(0,0,0,0.05); border-radius:10px; padding:4px; border:1px solid rgba(0,0,0,0.1);">
                                                        <button class="period-btn active" data-period="7" style="padding:8px 16px; border:none; background:linear-gradient(135deg,#667eea,#764ba2); color:white; border-radius:8px; cursor:pointer;">7D</button>
                                                        <button class="period-btn" data-period="30" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">30D</button>
                                                        <button class="period-btn" data-period="90" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">90D</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-stats">
                                                <div class="stat-item"><div class="stat-icon stat-users"><i data-feather="users"></i></div><div><span class="stat-value" id="total-users">-</span><span class="stat-label d-block">Total Users</span></div></div>
                                                <div class="stat-item"><div class="stat-icon stat-sessions"><i data-feather="activity"></i></div><div><span class="stat-value" id="total-sessions">-</span><span class="stat-label d-block">Sessions</span></div></div>
                                                <div class="stat-item"><div class="stat-icon stat-avg"><i data-feather="trending-up"></i></div><div><span class="stat-value" id="avg-daily">-</span><span class="stat-label d-block">Daily Avg</span></div></div>
                                            </div>
                                            <div class="chart-container"><div id="daily-usage-chart"></div></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="online-users-card">
                                            <div class="users-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="users-icon" style="background: linear-gradient(135deg,#667eea,#764ba2);">
                                                            <i data-feather="wifi" style="color:white;"></i>
                                                        </div>
                                                        <div><h5 style="margin:0; font-weight:600;">Live Users</h5><p style="margin:0; color:#6c757d; font-size:0.9rem;">Currently Connected</p></div>
                                                    </div>
                                                    <button class="refresh-btn" id="refresh-online-users"><i data-feather="refresh-cw"></i></button>
                                                </div>
                                                <div class="users-count">
                                                    <span class="count-number" id="online-count">0</span>
                                                    <span style="color:#6c757d; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px;">Online</span>
                                                    <span id="count-range" style="display:none; font-size:0.75rem; color:#6c757d;"></span>
                                                </div>
                                            </div>
                                            <div class="users-container">
                                                <div id="online-users-list">
                                                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 20px; text-align:center;">
                                                        <i data-feather="loader" style="width:40px; height:40px; color:#667eea; animation:spin 1s linear infinite; margin-bottom:15px;"></i>
                                                        <p>Loading online users...</p>
                                                    </div>
                                                </div>
                                                <div class="pagination-container" id="users-pagination" style="display: none;">
                                                    <div class="pagination-controls">
                                                        <button class="pagination-btn" id="prev-page" disabled><i data-feather="chevron-left"></i></button>
                                                        <div class="d-flex align-items-center gap-1" id="page-numbers"></div>
                                                        <button class="pagination-btn" id="next-page" disabled><i data-feather="chevron-right"></i></button>
                                                    </div>
                                                    <div class="text-center mt-2"><span style="font-size:0.85rem; color:#6c757d;" id="page-info">1 / 1</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WiFi Networks Shortcut -->
                <div class="row">
                    <div class="col-12">
                        <div class="networks-shortcut-card">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h4><i data-feather="wifi" style="width:24px;height:24px;margin-right:10px;vertical-align:middle;"></i> WiFi Networks</h4>
                                    <p>Manage all WiFi networks associated with this location — add, remove, or configure each network's security, captive portal, IP settings, and more.</p>
                                    <div id="network-summary-badges">
                                        <span class="network-summary-badge"><i data-feather="loader" style="width:12px;height:12px;"></i> Loading...</span>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                                    <a id="manage-networks-btn" href="#" class="btn btn-light btn-lg">
                                        <i data-feather="settings" class="mr-2"></i> Manage Networks
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Configuration -->
                <div class="row" id="location-configuration">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Location Configuration</h4></div>
                            <div class="card-body">

                                <ul class="nav nav-tabs" role="tablist" id="main-tabs-nav">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="location-settings-tab" data-toggle="tab" href="#location-settings" role="tab">
                                            <i class="fas fa-building mr-2"></i>Location Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="router-tab" data-toggle="tab" href="#router" role="tab">
                                            <i data-feather="hard-drive" class="mr-50"></i>Router Settings
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <!-- ── Location Details Tab ── -->
                                    <div class="tab-pane active show" id="location-settings" role="tabpanel">
                                        <form id="location-info-form" novalidate>

                                            <!-- Panel 1: Identity & Address -->
                                            <div class="loc-panel panel-location">
                                                <div class="loc-panel-header">
                                                    <span class="loc-panel-icon">
                                                        <i data-feather="map-pin" style="color:#7367f0;width:16px;height:16px;"></i>
                                                    </span>
                                                    <h6 class="loc-panel-title">Location Identity &amp; Address</h6>
                                                </div>
                                                <div class="loc-panel-body">
                                                    <div class="panel-sub-label">Identity</div>
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label for="location-name">Location Name <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="location-name" placeholder="e.g. Downtown Café" required>
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="router-model-select">Router Model</label>
                                                                <select class="form-control" id="router-model-select">
                                                                    <option value="">Select Model</option>
                                                                    <option value="820AX">820AX</option>
                                                                    <option value="835AX">835AX</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="location-status">Status</label>
                                                                <select class="form-control" id="location-status">
                                                                    <option value="active">Active</option>
                                                                    <option value="inactive">Inactive</option>
                                                                    <option value="maintenance">Maintenance</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel-sub-section">
                                                        <div class="panel-sub-label">Address</div>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label for="location-address">Street Address</label>
                                                                    <input type="text" class="form-control" id="location-address" placeholder="123 Main St">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="location-city">City</label>
                                                                    <input type="text" class="form-control" id="location-city" placeholder="City">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="location-state">State / Province</label>
                                                                    <input type="text" class="form-control" id="location-state" placeholder="State">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label for="location-postal-code">Postal</label>
                                                                    <input type="text" class="form-control" id="location-postal-code" placeholder="Code">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label for="location-country">Country</label>
                                                                    <input type="text" class="form-control" id="location-country" placeholder="Country">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel-sub-section">
                                                        <div class="panel-sub-label">Notes</div>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="location-description">Description <small class="text-muted font-weight-normal">(optional)</small></label>
                                                                    <textarea class="form-control" id="location-description" rows="2" placeholder="Brief description of this location…" maxlength="500"></textarea>
                                                                    <small class="text-muted"><span id="description-counter">0</span>/500 characters</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Panel 2: Contact -->
                                            <div class="loc-panel panel-contact">
                                                <div class="loc-panel-header">
                                                    <span class="loc-panel-icon">
                                                        <i data-feather="user" style="color:#17a2b8;width:16px;height:16px;"></i>
                                                    </span>
                                                    <h6 class="loc-panel-title">Contact &amp; Ownership</h6>
                                                </div>
                                                <div class="loc-panel-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="location-manager">Manager Name</label>
                                                                <input type="text" class="form-control" id="location-manager" placeholder="Full name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="location-contact-email">Email</label>
                                                                <input type="email" class="form-control" id="location-contact-email" placeholder="contact@example.com">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="location-contact-phone">Phone</label>
                                                                <input type="tel" class="form-control" id="location-contact-phone" placeholder="+1 555 000 0000">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" id="location-owner-group" data-admin-only="true">
                                                            <div class="form-group">
                                                                <label for="location-owner">
                                                                    Owner
                                                                    <span style="font-size:0.7rem;background:rgba(115,103,240,0.12);color:#7367f0;border-radius:10px;padding:1px 7px;font-weight:600;margin-left:4px;">Admin</span>
                                                                </label>
                                                                <select class="form-control" id="location-owner"><option value="">Select Owner</option></select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action bar -->
                                            <div class="form-action-bar">
                                                <button type="button" id="save-location-info" class="btn custom-btn">
                                                    <i data-feather="save" class="mr-1"></i> Save Location Information
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="resetLocationForm()">
                                                    <i data-feather="refresh-ccw" class="mr-1"></i> Reset
                                                </button>
                                            </div>

                                        </form>
                                    </div>

                                    <!-- ── Router Settings Tab ── -->
                                    <div class="tab-pane fade" id="router" role="tabpanel">
                                        <!-- WAN -->
                                        <div class="content-section">
                                            <div class="section-header d-flex justify-content-between align-items-center">
                                                <h5 class="section-title">WAN Connection</h5>
                                                <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#wan-settings-modal"><i data-feather="edit" class="mr-1"></i>Edit WAN Settings</button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="interface-detail"><span class="interface-label">Connection Type</span><span class="interface-value" id="wan-type-display">DHCP</span></div>
                                                </div>
                                                <div class="col-md-9 wan-static-ip-display_div hidden">
                                                    <div class="row">
                                                        <div class="col-md-3"><div class="interface-detail"><span class="interface-label">IP Address</span><span class="interface-value" id="wan-ip-display">-</span></div></div>
                                                        <div class="col-md-3"><div class="interface-detail"><span class="interface-label">Subnet Mask</span><span class="interface-value" id="wan-subnet-display">-</span></div></div>
                                                        <div class="col-md-3"><div class="interface-detail"><span class="interface-label">Gateway</span><span class="interface-value" id="wan-gateway-display">-</span></div></div>
                                                        <div class="col-md-3"><div class="interface-detail"><span class="interface-label">Primary DNS</span><span class="interface-value" id="wan-dns1-display">-</span></div></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9 wan-pppoe-display_div hidden">
                                                    <div class="row">
                                                        <div class="col-md-6"><div class="interface-detail"><span class="interface-label">Username</span><span class="interface-value" id="wan-pppoe-username">-</span></div></div>
                                                        <div class="col-md-6"><div class="interface-detail"><span class="interface-label">Service Name</span><span class="interface-value" id="wan-pppoe-service-name">-</span></div></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Radio Settings -->
                                        <div class="content-section">
                                            <div class="section-header"><h5 class="section-title">WiFi Radio &amp; Channel Configuration</h5></div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="wifi-country">Country/Region</label>
                                                        <select class="form-control" id="wifi-country">
                                                            <option value="US" selected>United States (US)</option>
                                                            <option value="CA">Canada (CA)</option>
                                                            <option value="GB">United Kingdom (GB)</option>
                                                            <option value="FR">France (FR)</option>
                                                            <option value="DE">Germany (DE)</option>
                                                            <option value="IT">Italy (IT)</option>
                                                            <option value="ES">Spain (ES)</option>
                                                            <option value="AU">Australia (AU)</option>
                                                            <option value="JP">Japan (JP)</option>
                                                            <option value="CN">China (CN)</option>
                                                            <option value="IN">India (IN)</option>
                                                            <option value="BR">Brazil (BR)</option>
                                                            <option value="ZA">South Africa (ZA)</option>
                                                            <option value="AE">United Arab Emirates (AE)</option>
                                                            <option value="SG">Singapore (SG)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="power-level-2g">2.4 GHz Power</label>
                                                        <select class="form-control" id="power-level-2g">
                                                            <option value="20">Maximum (20 dBm)</option>
                                                            <option value="17">High (17 dBm)</option>
                                                            <option value="15" selected>Medium (15 dBm)</option>
                                                            <option value="12">Low (12 dBm)</option>
                                                            <option value="10">Minimum (10 dBm)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="power-level-5g">5 GHz Power</label>
                                                        <select class="form-control" id="power-level-5g">
                                                            <option value="23">Maximum (23 dBm)</option>
                                                            <option value="20">High (20 dBm)</option>
                                                            <option value="17" selected>Medium (17 dBm)</option>
                                                            <option value="14">Low (14 dBm)</option>
                                                            <option value="10">Minimum (10 dBm)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="channel-width-2g">2.4 GHz Channel Width</label>
                                                        <select class="form-control" id="channel-width-2g"><option value="20">20 MHz</option><option value="40" selected>40 MHz</option></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="channel-width-5g">5 GHz Channel Width</label>
                                                        <select class="form-control" id="channel-width-5g"><option value="20">20 MHz</option><option value="40">40 MHz</option><option value="80" selected>80 MHz</option><option value="160">160 MHz</option></select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="channel-2g">2.4 GHz Channel</label>
                                                        <select class="form-control" id="channel-2g">
                                                            <option value="1">Ch 1 (2412)</option><option value="2">Ch 2</option><option value="3">Ch 3</option><option value="4">Ch 4</option><option value="5">Ch 5</option>
                                                            <option value="6" selected>Ch 6 (2437)</option><option value="7">Ch 7</option><option value="8">Ch 8</option><option value="9">Ch 9</option><option value="10">Ch 10</option>
                                                            <option value="11">Ch 11</option><option value="12">Ch 12</option><option value="13">Ch 13</option><option value="14">Ch 14 (2484)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="channel-5g">5 GHz Channel</label>
                                                        <select class="form-control" id="channel-5g">
                                                            <option value="36" selected>Ch 36</option><option value="40">Ch 40</option><option value="44">Ch 44</option><option value="48">Ch 48</option>
                                                            <option value="52">Ch 52</option><option value="56">Ch 56</option><option value="60">Ch 60</option><option value="64">Ch 64</option>
                                                            <option value="100">Ch 100</option><option value="104">Ch 104</option><option value="108">Ch 108</option><option value="112">Ch 112</option>
                                                            <option value="116">Ch 116</option><option value="120">Ch 120</option><option value="124">Ch 124</option><option value="128">Ch 128</option>
                                                            <option value="132">Ch 132</option><option value="136">Ch 136</option><option value="140">Ch 140</option><option value="149">Ch 149</option>
                                                            <option value="153">Ch 153</option><option value="157">Ch 157</option><option value="161">Ch 161</option><option value="165">Ch 165</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="mb-0">Channel Optimization</label>
                                                        <button class="btn btn-outline-primary btn-sm" id="scan-channels-btn"><i data-feather="wifi" class="mr-1"></i>Scan</button>
                                                    </div>
                                                    <div class="alert alert-info mb-3" id="scan-status-alert">
                                                        <div class="alert-body"><i data-feather="info" class="mr-2"></i><span id="scan-status-text">Click Scan to analyze optimal channels.</span></div>
                                                    </div>
                                                    <div class="row text-center mb-3">
                                                        <div class="col-6"><div class="stat-value text-primary" id="last-optimal-2g">--</div><div class="stat-label">Best 2.4G</div></div>
                                                        <div class="col-6"><div class="stat-value text-success" id="last-optimal-5g">--</div><div class="stat-label">Best 5G</div></div>
                                                    </div>
                                                    <div class="text-center mb-2"><small class="text-muted" id="last-scan-timestamp">No scan performed yet</small></div>
                                                    <button class="btn btn-success btn-block btn-sm" id="save-channels-btn" disabled><i data-feather="check" class="mr-1"></i>Apply Optimal</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button class="btn custom-btn" id="save-radio-settings"><i data-feather="save" class="mr-2"></i>Save All Radio Settings</button>
                                            </div>
                                        </div>

                                        <!-- Web Filter -->
                                        <div class="content-section">
                                            <div class="section-header d-flex justify-content-between align-items-center">
                                                <h5 class="section-title">Web Content Filtering</h5>
                                                <button class="btn custom-btn" id="save-web-filter-settings"><i data-feather="save" class="mr-2"></i>Save Web Filter Settings</button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="mb-0">Enable Content Filtering</label>
                                                            <div class="custom-control custom-switch custom-control-primary">
                                                                <input type="checkbox" class="custom-control-input" id="global-web-filter">
                                                                <label class="custom-control-label" for="global-web-filter"></label>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Apply content filtering to all WiFi networks.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="global-filter-categories">Block Categories</label>
                                                        <select class="select2 form-control" id="global-filter-categories" multiple="multiple"></select>
                                                        <small class="text-muted">Select content categories to block across all networks.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end #router -->

                                </div><!-- end .tab-content -->

                            </div><!-- end .card-body -->
                        </div><!-- end .card -->
                    </div><!-- end .col-12 -->
                </div><!-- end #location-configuration -->

            </div><!-- end .content-body -->
        </div><!-- end .content-wrapper -->
    </div><!-- end .app-content -->
    <!-- END: Content-->

    <!-- ============================================================
         MODALS
    ============================================================ -->

    <!-- WAN Settings Modal -->
    <div class="modal fade" id="wan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="wan-settings-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wan-settings-modal-title">Edit WAN Interface Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Connection Type</label>
                        <select class="form-control" id="wan-connection-type">
                            <option value="DHCP">DHCP</option>
                            <option value="STATIC">Static IP</option>
                            <option value="PPPOE">PPPoE</option>
                        </select>
                    </div>
                    <div id="wan-static-fields" class="hidden">
                        <div class="form-group"><label>IP Address</label><input type="text" class="form-control" id="wan-ip-address" placeholder="203.0.113.10"></div>
                        <div class="form-group"><label>Netmask</label><input type="text" class="form-control" id="wan-netmask" placeholder="255.255.255.0"></div>
                        <div class="form-group"><label>Gateway</label><input type="text" class="form-control" id="wan-gateway" placeholder="203.0.113.1"></div>
                        <div class="form-group"><label>Primary DNS</label><input type="text" class="form-control" id="wan-primary-dns" placeholder="8.8.8.8"></div>
                        <div class="form-group"><label>Secondary DNS</label><input type="text" class="form-control" id="wan-secondary-dns" placeholder="1.1.1.1"></div>
                    </div>
                    <div id="wan-pppoe-fields" style="display: none;">
                        <div class="form-group"><label>Username</label><input type="text" class="form-control" id="wan-pppoe-username-modal" placeholder="Username"></div>
                        <div class="form-group"><label>Password</label><input type="password" class="form-control" id="wan-pppoe-password" placeholder="Password"></div>
                        <div class="form-group"><label>Service Name (Optional)</label><input type="text" class="form-control" id="wan-pppoe-service-name-modal" placeholder="Service Name"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn save-wan-settings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Restart Modal -->
    <div class="modal fade" id="restart-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i data-feather="refresh-cw" class="mr-2"></i>Restart Device</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3"><div class="alert-body"><i data-feather="alert-triangle" class="mr-2"></i><strong>Warning:</strong> This action will restart the device and temporarily interrupt internet access.</div></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3"><div class="avatar-content"><i data-feather="hard-drive" class="font-medium-4"></i></div></div>
                        <div>
                            <h6 class="mb-0">Device Information</h6>
                            <p class="card-text text-muted mb-0">Location: <span class="location_name font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">MAC Address: <span class="router_mac_address font-weight-bold"></span></p>
                        </div>
                    </div>
                    <p class="text-muted">Are you sure you want to restart this device?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-restart-btn"><i data-feather="refresh-cw" class="mr-1"></i><span>Restart Device</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmware Update Modal -->
    <div class="modal fade" id="firmware-update-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i data-feather="download" class="mr-2"></i>Update Firmware</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3"><div class="alert-body"><i data-feather="info" class="mr-2"></i><strong>Important:</strong> Firmware update will restart the device and may take 5-10 minutes.</div></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-light-primary p-50 mr-3"><div class="avatar-content"><i data-feather="hard-drive" class="font-medium-4"></i></div></div>
                        <div>
                            <h6 class="mb-0">Current Device</h6>
                            <p class="card-text text-muted mb-0">Model: <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">Firmware: <span class="router_firmware font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">MAC: <span class="router_mac_address font-weight-bold"></span></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firmware-version-select">Available Versions</label>
                        <select class="form-control" id="firmware-version-select"><option value="">Loading firmware versions...</option></select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <div class="card"><div class="card-body p-2"><div id="firmware-description"><p class="text-muted mb-0">Select a version to view details.</p></div></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="start-firmware-update-btn" disabled><i data-feather="download" class="mr-1"></i><span>Update Firmware</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmware Progress Modal -->
    <div class="modal fade" id="firmware-progress-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><i data-feather="download" class="mr-2"></i>Updating Firmware</h5></div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3"><div class="alert-body"><i data-feather="alert-triangle" class="mr-2"></i><strong>Do not close this window or power off the device during update.</strong></div></div>
                    <div class="text-center mb-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="firmware-progress-bar"></div>
                    </div>
                    <div class="text-center">
                        <h6 id="firmware-progress-status">Preparing firmware update...</h6>
                        <p class="text-muted mb-0" id="firmware-progress-description">This may take several minutes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Channel Scan Modal -->
    <div class="modal fade" id="channel-scan-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg,#7367f0,#9c88ff);">
                    <h5 class="modal-title" style="color:white;"><i data-feather="wifi" class="mr-2"></i>Channel Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="scan-progress-view">
                        <div class="progress progress-bar-primary mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="timeline">
                            <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-initiated-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>Scan Initiated</h6><span class="text-muted">Step 1/4</span></div><p>Preparing device for channel scanning</p></div></div>
                            <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-started-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>Scan Started</h6><span class="text-muted">Step 2/4</span></div><p>Device is ready and beginning frequency analysis</p></div></div>
                            <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-2g-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>Scanning 2.4 GHz Band</h6><span class="text-muted">Step 3/4</span></div><p>Checking channels 1-11 for signal strength and interference</p></div></div>
                            <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-5g-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>Scanning 5 GHz Band</h6><span class="text-muted">Step 4/4</span></div><p>Checking channels 36-165 for signal strength and interference</p></div></div>
                        </div>
                    </div>
                    <div id="scan-results-view" style="display: none;">
                        <div class="alert alert-success mb-2"><div class="alert-body"><i data-feather="check-circle" class="mr-1"></i><span>Scan complete! Optimal channels have been determined.</span></div></div>
                        <div class="row mb-2">
                            <div class="col-md-6"><div class="card bg-light-primary mb-0"><div class="card-body"><h5 class="card-title">2.4 GHz</h5><div class="d-flex justify-content-between align-items-center"><span>Recommended:</span><h3 class="mb-0" id="result-channel-2g">6</h3></div></div></div></div>
                            <div class="col-md-6"><div class="card bg-light-primary mb-0"><div class="card-body"><h5 class="card-title">5 GHz</h5><div class="d-flex justify-content-between align-items-center"><span>Recommended:</span><h3 class="mb-0" id="result-channel-5g">36</h3></div></div></div></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="nearby-networks-table">
                                <thead><tr><th>Band</th><th>Channel</th><th>Networks</th><th>Signal</th><th>Interference</th><th>Status</th></tr></thead>
                                <tbody id="nearby-networks-tbody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <button class="btn custom-btn" id="apply-scan-results"><i data-feather="check" class="mr-1"></i> Apply Settings</button>
                            <button class="btn btn-outline-primary" id="back-to-scan-btn"><i data-feather="refresh-cw" class="mr-1"></i> Scan Again</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAC Address Edit Modal -->
    <div class="modal fade" id="mac-address-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i data-feather="edit" class="mr-2"></i>Edit MAC Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3"><div class="alert-body"><i data-feather="info" class="mr-2"></i><strong>Note:</strong> This will update the MAC address for the device at this location.</div></div>
                    <div class="form-group"><label for="mac-address-input">MAC Address</label><input type="text" class="form-control" id="mac-address-input" placeholder="XX-XX-XX-XX-XX-XX" maxlength="17"><small class="text-muted">Format: XX-XX-XX-XX-XX-XX</small></div>
                    <div class="form-group"><label>Current MAC Address</label><div class="form-control-plaintext bg-light p-2 rounded"><span id="current-mac-display">-</span></div></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn custom-btn" id="save-mac-address-btn"><i data-feather="save" class="mr-1"></i><span>Save Changes</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- END: Modals -->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
    <!-- END: Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/charts/chart-apex.js"></script>
    <script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
    <script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>
    <!-- END: Page JS-->

    <!-- App JS -->
    <script src="/assets/js/config.js?v=1"></script>

    <!-- Bootstrap config exposed to JS -->
    <script>
        window.APP_CONFIG_V5 = {
            apiBase: '{{ rtrim(config("app.url"), "/") }}/api'
        };
    </script>

    <script src="/assets/js/location-details-v5.js?v=1"></script>

</body>
<!-- END: Body-->
</html>
