<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>Network Settings - monsieur-wifi Controller</title>
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

    <style>
        .custom-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; color: white !important; box-shadow: 0 2px 8px rgba(102,126,234,0.3) !important; }
        .custom-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(115,103,240,0.4) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; background: #fff; margin-bottom: 1.5rem; }
        .card-header { background: linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%); border-bottom: 1px solid rgba(0,0,0,0.05); border-radius: 12px 12px 0 0 !important; padding: 1.5rem; }
        .card-title { font-weight: 600; color: #2c3e50; margin-bottom: 0; font-size: 1.1rem; }
        .card-body { padding: 1.5rem; }
        .form-control { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease; }
        .form-control:focus { border-color: #7367f0; box-shadow: 0 0 0 0.2rem rgba(115,103,240,0.15); outline: none; }
        select.form-control { height: 50px; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px 12px; padding-right: 40px; }
        textarea.form-control { display: block; resize: vertical; min-height: 80px; }
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

        /* Network tabs header with Add button */
        .networks-header-bar { display: flex; align-items: center; justify-content: space-between; background: #f8f9fa; border-radius: 12px; padding: 12px 16px; margin-bottom: 1.5rem; }
        .networks-header-bar .nav-tabs { margin-bottom: 0; background: transparent; padding: 0; flex: 1; }
        #add-network-btn { white-space: nowrap; margin-left: 1rem; flex-shrink: 0; }

        /* Back breadcrumb */
        .back-btn { display: inline-flex; align-items: center; gap: 6px; color: #7367f0; font-weight: 500; text-decoration: none; margin-bottom: 0; }
        .back-btn:hover { color: #9c88ff; text-decoration: none; }

        /* VLAN global toggle */
        .vlan-global-bar { background: linear-gradient(135deg,#f0f0ff 0%,#e8e8ff 100%); border-radius: 10px; padding: 14px 20px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(115,103,240,0.15); }

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

        /* Schedule */
        .schedule-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; padding: 0 1rem; }
        .schedule-grid { display: grid; grid-template-columns: 120px repeat(24,60px); min-height: 400px; min-width: 1592px; gap: 1px; background-color: #e5e7eb; }
        .time-cell { background: white; min-height: 60px; position: relative; cursor: pointer; transition: all 0.2s ease; border-right: 1px solid #f1f5f9; }
        .time-slot { position: absolute; top: 8px; bottom: 8px; left: 2px; background: linear-gradient(135deg,#10b981 0%,#059669 100%); border-radius: 6px; color: white; font-weight: 600; font-size: clamp(0.5rem,calc(0.8rem + 0.5vw),0.875rem); display: flex; align-items: center; justify-content: center; cursor: move; user-select: none; z-index: 10; min-width: calc(100% - 4px); box-sizing: border-box; }
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
            <!-- Page header -->
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Network Settings</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/locations">Locations</a></li>
                                    <li class="breadcrumb-item"><a id="breadcrumb-location-link" href="#">Loading...</a></li>
                                    <li class="breadcrumb-item active">Networks</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <a id="back-to-location-btn" href="#" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back to Location
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
                                <h5 class="mb-0 location_name" style="font-weight:700;">Loading...</h5>
                                <small class="text-muted location_address"></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <!-- VLAN global toggle -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0 font-weight-600" style="font-size:0.9rem;">VLAN Support</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                                    <label class="custom-control-label" for="vlan-enabled"></label>
                                </div>
                            </div>
                            <button class="btn custom-btn btn-sm" id="add-network-btn" disabled>
                                <i data-feather="plus" class="mr-1"></i> Add Network
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Network tabs nav -->
                <ul class="nav nav-tabs" role="tablist" id="network-tabs-nav">
                    <li class="nav-item" id="network-tabs-loading">
                        <span class="nav-link disabled"><i class="fas fa-spinner fa-spin mr-1"></i>Loading networks…</span>
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
                <span class="network-tab-label">Network</span>
                <span class="network-type-badge network-type-__TYPE__">__TYPE_LABEL__</span>
            </a>
            <button class="btn btn-sm btn-link text-danger p-0 ml-1 network-delete-btn" data-network-id="__ID__" title="Delete network" style="line-height:1;">
                <i data-feather="x-circle"></i>
            </button>
        </li>
    </template>

    <!-- Tab pane template -->
    <template id="network-pane-tpl">
        <div class="tab-pane fade" id="network-pane-__ID__" role="tabpanel" data-network-id="__ID__">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><span class="network-pane-title">Network</span></h4>
                    <button class="btn custom-btn network-save-btn" data-network-id="__ID__">
                        <i data-feather="save" class="mr-1"></i> Save Settings
                    </button>
                </div>
                <div class="card-body">

                    <!-- Common: type, SSID, visibility, enabled -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Network Type</label>
                                <select class="form-control network-type-select" data-network-id="__ID__">
                                    <option value="password">Password WiFi</option>
                                    <option value="captive_portal">Captive Portal</option>
                                    <option value="open">Open ESSID</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Network Name (SSID)</label>
                                <input type="text" class="form-control network-ssid" placeholder="My WiFi">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Network Visibility</label>
                                <select class="form-control network-visible">
                                    <option value="1">Visible (Broadcast SSID)</option>
                                    <option value="0">Hidden</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch mt-4">
                                    <input type="checkbox" class="custom-control-input network-enabled" id="network-enabled-__ID__" checked>
                                    <label class="custom-control-label" for="network-enabled-__ID__">Enabled</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PASSWORD section -->
                    <div class="network-section network-section-password">
                        <h5 class="border-bottom pb-1">Security</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>WiFi Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control network-password" placeholder="Minimum 8 characters">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary network-toggle-password" type="button"><i data-feather="eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Security Type</label>
                                    <select class="form-control network-security">
                                        <option value="wpa2-psk" selected>WPA2-PSK (Recommended)</option>
                                        <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixed</option>
                                        <option value="wpa3-psk">WPA3-PSK (Most Secure)</option>
                                        <option value="wep">WEP (Legacy)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cipher Suites</label>
                                    <select class="form-control network-cipher-suites">
                                        <option value="CCMP" selected>CCMP</option>
                                        <option value="TKIP">TKIP</option>
                                        <option value="TKIP+CCMP">TKIP+CCMP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CAPTIVE PORTAL section -->
                    <div class="network-section network-section-captive_portal">
                        <h5 class="border-bottom pb-1">Authentication</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Authentication Method</label>
                                    <select class="form-control network-auth-method">
                                        <option value="click-through" selected>Click-through (No Authentication)</option>
                                        <option value="password">Password-based</option>
                                        <option value="sms">SMS Verification</option>
                                        <option value="email">Email Verification</option>
                                        <option value="social">Social Media Login</option>
                                    </select>
                                </div>
                                <div class="form-group network-captive-password-group" style="display:none;">
                                    <label>Shared Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control network-portal-password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary network-toggle-portal-password" type="button"><i data-feather="eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group network-social-group" style="display:none;">
                                    <label>Social Media Login</label>
                                    <select class="form-control network-social-method">
                                        <option value="facebook">Facebook</option>
                                        <option value="google">Google</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Session (mins)</label>
                                            <select class="form-control network-session-timeout">
                                                <option value="60">1 Hr</option><option value="120">2 Hrs</option><option value="180">3 Hrs</option>
                                                <option value="240">4 Hrs</option><option value="300">5 Hrs</option><option value="360">6 Hrs</option>
                                                <option value="720">12 Hrs</option><option value="1440">1 Day</option><option value="10080">1 Week</option>
                                                <option value="43200">3 Months</option><option value="172800">1 Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Idle (mins)</label>
                                            <select class="form-control network-idle-timeout">
                                                <option value="15">15 Mins</option><option value="30">30 Mins</option><option value="45">45 Mins</option>
                                                <option value="60">1 Hr</option><option value="120">2 Hrs</option><option value="240">4 Hrs</option>
                                                <option value="720">12 Hrs</option><option value="1440">1 Day</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Redirect URL (optional)</label>
                                            <input type="url" class="form-control network-redirect-url" placeholder="https://example.com/welcome">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Captive Portal Design</label>
                                            <select class="form-control network-portal-design-id">
                                                <option value="">Default Design</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-1 mt-2">Bandwidth Limits</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Download Limit (Mbps)</label>
                                    <input type="number" class="form-control network-download-limit" placeholder="Unlimited" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Upload Limit (Mbps)</label>
                                    <input type="number" class="form-control network-upload-limit" placeholder="Unlimited" min="0">
                                </div>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-1 mt-2">Working Hours</h5>
                        <div class="row">
                            <div class="col-12">
                                <div class="py-4 px-2">
                                    <div class="schedule-container network-schedule-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OPEN section -->
                    <div class="network-section network-section-open">
                        <div class="alert alert-info">
                            <div class="alert-body">
                                <i data-feather="info" class="mr-2"></i>
                                Open ESSID — no password or captive portal authentication. Anyone within range can connect.
                            </div>
                        </div>
                    </div>

                    <!-- Shared: IP / DHCP Settings -->
                    <h5 class="border-bottom pb-1 mt-3">IP &amp; DHCP Settings</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>IP Mode</label>
                                <select class="form-control network-ip-mode">
                                    <option value="static">Static IP</option>
                                    <option value="dhcp">DHCP Client</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>IP Address</label>
                                <input type="text" class="form-control network-ip-address" placeholder="192.168.x.1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Netmask</label>
                                <input type="text" class="form-control network-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gateway</label>
                                <input type="text" class="form-control network-gateway" placeholder="Auto">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Primary DNS</label>
                                <input type="text" class="form-control network-dns1" placeholder="8.8.8.8" value="8.8.8.8">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Secondary DNS</label>
                                <input type="text" class="form-control network-dns2" placeholder="8.8.4.4" value="8.8.4.4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch mt-4">
                                    <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                    <label class="custom-control-label" for="network-dhcp-__ID__">DHCP Server</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>DHCP Range</label>
                                <div class="input-group">
                                    <input type="text" class="form-control network-dhcp-start" placeholder="x.x.x.100">
                                    <div class="input-group-prepend input-group-append"><span class="input-group-text">–</span></div>
                                    <input type="text" class="form-control network-dhcp-end" placeholder="x.x.x.200">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shared: VLAN -->
                    <h5 class="border-bottom pb-1 mt-2">VLAN</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>VLAN ID (optional, 1–4094)</label>
                                <input type="number" class="form-control network-vlan-id" placeholder="None" min="1" max="4094" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>VLAN Tagging</label>
                                <select class="form-control network-vlan-tagging" disabled>
                                    <option value="disabled">Disabled</option>
                                    <option value="tagged">Tagged</option>
                                    <option value="untagged">Untagged</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Shared: MAC Filtering -->
                    <h5 class="border-bottom pb-1 mt-2">MAC Address Filtering</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Filter Mode</label>
                                <select class="form-control network-mac-filter-mode">
                                    <option value="allow-all">Allow All</option>
                                    <option value="allow-listed">Allow Listed Only</option>
                                    <option value="block-listed">Block Listed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Add MAC Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control network-mac-input" placeholder="00:11:22:33:44:55">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary network-mac-add-btn" type="button">Add</button>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded" style="max-height: 200px; overflow-y: auto; min-height: 50px;">
                                <div class="network-mac-list"></div>
                                <div class="text-center text-muted p-3 network-mac-empty">
                                    <small>No MAC addresses configured</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                    <i data-feather="save" class="mr-1"></i> Save MAC Filter
                                </button>
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

    <!-- App JS -->
    <script src="/assets/js/config.js?v=1"></script>

    <script>
        window.APP_CONFIG_V5 = {
            maxNetworks: {{ (int) env('MAX_NETWORKS_PER_LOCATION', 4) }},
            apiBase: '{{ rtrim(config("app.url"), "/") }}/api'
        };
    </script>

    <script src="/assets/js/location-networks-v5.js?v=1"></script>

</body>
</html>
