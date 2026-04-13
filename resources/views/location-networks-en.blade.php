@extends('layouts.app')

@section('title', 'Network Settings - monsieur-wifi Controller')

@php $locale = 'en'; @endphp

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
<link rel="stylesheet" type="text/css" href="/working-hours/interactive-schedule.css">
<link rel="stylesheet" type="text/css" href="/assets/css/location-networks.css?v=6">
@endpush

@section('content')
<!-- Page header -->
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Network Settings</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/locations">Locations</a></li>
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
                    <h5 class="mb-0 location_name" style="font-weight:700; padding-left:10px;">Loading...</h5>
                    <small class="text-muted location_address" style="padding-left:10px;"></small>
                </div>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <div class="d-flex align-items-center" style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:6px 14px;gap:10px;">
                    <i data-feather="layers" style="width:14px;height:14px;color:#7367f0;flex-shrink:0;"></i>
                    <span style="font-size:0.85rem;font-weight:600;color:#495057;white-space:nowrap;">VLAN Support</span>
                    <div class="custom-control custom-switch mb-0">
                        <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                        <label class="custom-control-label" for="vlan-enabled"></label>
                    </div>
                </div>
                <button class="btn custom-btn btn-sm" id="add-network-btn" disabled style="height:36px;white-space:nowrap;">
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
    </li>
</template>

<!-- Tab pane template -->
<template id="network-pane-tpl">
    <div class="tab-pane fade" id="network-pane-__ID__" role="tabpanel" data-network-id="__ID__">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><span class="network-pane-title">Network</span></h4>
                <div class="d-flex align-items-center gap-2" style="gap:0.5rem;">
                    <button type="button" class="btn btn-outline-danger btn-sm network-delete-btn" data-network-id="__ID__">
                        <i data-feather="trash-2" class="mr-1"></i> Delete Network
                    </button>
                    <button type="button" class="btn custom-btn network-save-btn" data-network-id="__ID__">
                        <i data-feather="save" class="mr-1"></i> Save Settings
                    </button>
                </div>
            </div>
            <div class="card-body">

                <!-- Network identity bar: type pills + SSID + visibility + enabled -->
                <div class="network-identity-bar mt-1">
                    <select class="network-type-select d-none" data-network-id="__ID__">
                        <option value="password">Password WiFi</option>
                        <option value="captive_portal">Captive Portal</option>
                        <option value="open">Open ESSID</option>
                    </select>
                    <div class="network-type-pill-group">
                        <button type="button" class="network-type-pill" data-type="password">
                            <i data-feather="lock" style="width:13px;height:13px;"></i> Password
                        </button>
                        <button type="button" class="network-type-pill" data-type="captive_portal">
                            <i data-feather="layout" style="width:13px;height:13px;"></i> Captive Portal
                        </button>
                        <button type="button" class="network-type-pill" data-type="open">
                            <i data-feather="wifi" style="width:13px;height:13px;"></i> Open
                        </button>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-ssid-wrap">
                        <i data-feather="wifi" class="ssid-icon" style="width:15px;height:15px;"></i>
                        <input type="text" class="form-control network-ssid" placeholder="Network name (SSID)" maxlength="32">
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-visibility-wrap">
                        <i data-feather="eye" style="width:14px;height:14px;color:#adb5bd;flex-shrink:0;"></i>
                        <select class="network-visible">
                            <option value="1">Broadcast SSID</option>
                            <option value="0">Hidden SSID</option>
                        </select>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-enabled-wrap">
                        <input type="checkbox" class="network-toggle-checkbox network-enabled" id="network-enabled-__ID__" checked>
                        <label class="network-toggle-btn" for="network-enabled-__ID__">
                            <i data-feather="power" style="width:13px;height:13px;"></i>
                            <span class="network-toggle-label-on">Active</span>
                            <span class="network-toggle-label-off">Inactive</span>
                        </label>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-qos-wrap">
                        <input type="checkbox" class="network-toggle-checkbox network-qos-policy" id="network-qos-__ID__">
                        <label class="network-toggle-btn" for="network-qos-__ID__">
                            <i data-feather="zap" style="width:13px;height:13px;"></i>
                            <span>Full QoS</span>
                        </label>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-radio-wrap">
                        <i data-feather="radio" style="width:14px;height:14px;color:#adb5bd;"></i>
                        <select class="network-radio">
                            <option value="all">2.4 &amp; 5 GHz</option>
                            <option value="2.4">2.4 GHz only</option>
                            <option value="5">5 GHz only</option>
                        </select>
                    </div>
                </div>

                <!-- ── PASSWORD section ── -->
                <div class="network-section network-section-password">
                    <div class="network-type-panel panel-password">
                        <div class="network-type-panel-header">
                            <span class="network-type-panel-icon">
                                <i data-feather="lock" style="color:#7367f0;width:16px;height:16px;"></i>
                            </span>
                            <h6 class="network-type-panel-title">Security &amp; Encryption</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="row">
                                <div class="col-md-5">
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
                                        <label>Security Protocol</label>
                                        <select class="form-control network-security">
                                            <option value="wpa2-psk" selected>WPA2-PSK (Recommended)</option>
                                            <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixed</option>
                                            <option value="wpa3-psk">WPA3-PSK (Most Secure)</option>
                                            <option value="wep">WEP (Legacy)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                    </div>
                </div>

                <!-- ── CAPTIVE PORTAL section ── -->
                <div class="network-section network-section-captive_portal">
                    <div class="network-type-panel panel-captive">
                        <div class="network-type-panel-header">
                            <span class="network-type-panel-icon">
                                <i data-feather="layout" style="color:#28c76f;width:16px;height:16px;"></i>
                            </span>
                            <h6 class="network-type-panel-title">Captive Portal Configuration</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="panel-sub-label">Authentication</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Login Methods <small class="text-muted font-weight-normal">(select one or more)</small></label>
                                        <div class="network-auth-method-pills d-flex flex-wrap" style="gap:6px;">
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="click-through">
                                                <i data-feather="wifi" style="width:13px;height:13px;"></i> Click-through
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="password">
                                                <i data-feather="lock" style="width:13px;height:13px;"></i> Password
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="sms">
                                                <i data-feather="message-circle" style="width:13px;height:13px;"></i> SMS
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="email">
                                                <i data-feather="mail" style="width:13px;height:13px;"></i> Email
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="social">
                                                <i data-feather="share-2" style="width:13px;height:13px;"></i> Social
                                            </button>
                                        </div>
                                        <small class="form-text text-muted mt-1">When multiple methods are selected, guests will choose at login.</small>
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
                                        <label>Social Provider</label>
                                        <select class="form-control network-social-method">
                                            <option value="facebook">Facebook</option>
                                            <option value="google">Google</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Portal Design</label>
                                        <select class="form-control network-portal-design-id">
                                            <option value="">Default Design</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Redirect URL <small class="text-muted font-weight-normal">(optional)</small></label>
                                        <input type="url" class="form-control network-redirect-url" placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Session Timeout</label>
                                        <select class="form-control network-session-timeout">
                                            <option value="60">1 Hour</option><option value="120">2 Hours</option><option value="180">3 Hours</option>
                                            <option value="240">4 Hours</option><option value="300">5 Hours</option><option value="360">6 Hours</option>
                                            <option value="720">12 Hours</option><option value="1440">1 Day</option><option value="10080">1 Week</option>
                                            <option value="43200">3 Months</option><option value="172800">1 Year</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Idle Timeout</label>
                                        <select class="form-control network-idle-timeout">
                                            <option value="15">15 Minutes</option><option value="30">30 Minutes</option><option value="45">45 Minutes</option>
                                            <option value="60">1 Hour</option><option value="120">2 Hours</option><option value="240">4 Hours</option>
                                            <option value="720">12 Hours</option><option value="1440">1 Day</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-sub-section">
                                <div class="panel-sub-label">Bandwidth Limits</div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="download" style="width:13px;height:13px;margin-right:4px;"></i>Download (Mbps)</label>
                                            <input type="number" class="form-control network-download-limit" placeholder="Unlimited" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="upload" style="width:13px;height:13px;margin-right:4px;"></i>Upload (Mbps)</label>
                                            <input type="number" class="form-control network-upload-limit" placeholder="Unlimited" min="0">
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
                            Working Hours
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
                            <h6 class="network-type-panel-title">Open Network</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="d-flex align-items-start" style="gap:12px;">
                                <i data-feather="alert-triangle" style="width:20px;height:20px;color:#ff9f43;flex-shrink:0;margin-top:1px;"></i>
                                <div>
                                    <p class="mb-1" style="font-weight:600;color:#ff9f43;font-size:0.95rem;">No authentication required</p>
                                    <p class="mb-0 text-muted" style="font-size:0.88rem;">Anyone within range can connect without a password or portal. Use only in trusted environments.</p>
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
                            IP &amp; DHCP Settings
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="ip-dhcp-__ID__" style="display:none;">
                        <div class="network-type-panel panel-ip mt-2">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="globe" style="color:#17a2b8;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">IP Configuration</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="panel-sub-label">Addressing</div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>IP Mode</label>
                                            <select class="form-control network-ip-mode">
                                                <option value="static">Static IP</option>
                                                <option value="bridge_lan">Bridge to LAN Port</option>
                                                <option value="bridge">Bridge to WAN</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="network-bridge-lan-dhcp-mode-wrap col-md-3" style="display:none;">
                                        <div class="form-group">
                                            <label>LAN DHCP Mode</label>
                                            <select class="form-control network-bridge-lan-dhcp-mode">
                                                <option value="dhcp_client">DHCP Client</option>
                                                <option value="dhcp_server">DHCP Server</option>
                                            </select>
                                            <small class="form-text text-muted">DHCP Client is not available for Captive Portal networks.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>IP Address</label>
                                            <input type="text" class="form-control network-ip-address" placeholder="192.168.x.1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Netmask</label>
                                            <input type="text" class="form-control network-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Gateway</label>
                                            <input type="text" class="form-control network-gateway" placeholder="Auto">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Primary DNS</label>
                                            <div class="dns-field-wrapper" title="DNS is managed by the web filter. Disable the web filter to set per-network DNS.">
                                                <input type="text" class="form-control network-dns1" placeholder="8.8.8.8">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Alt DNS</label>
                                            <div class="dns-field-wrapper" title="DNS is managed by the web filter. Disable the web filter to set per-network DNS.">
                                                <input type="text" class="form-control network-dns2" placeholder="8.8.4.4">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-sub-section dhcp-address-pool-section">
                                    <div class="dhcp-address-pool-panel">
                                        <div class="dhcp-address-pool-panel-head">
                                            <span class="dhcp-address-pool-panel-icon">
                                                <i data-feather="share-2" style="color:#17a2b8;width:18px;height:18px;"></i>
                                            </span>
                                            <div class="dhcp-address-pool-panel-titles">
                                                <div class="dhcp-address-pool-panel-title">DHCP address pool</div>
                                                <div class="dhcp-address-pool-panel-sub">Assign LAN IPs to client devices on this network.</div>
                                            </div>
                                        </div>
                                        <div class="row align-items-end">
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group mb-md-0">
                                                    <label class="dhcp-address-pool-switch-label">DHCP server</label>
                                                    <div class="custom-control custom-switch mb-0">
                                                        <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                                        <label class="custom-control-label" for="network-dhcp-__ID__">Enable DHCP</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row dhcp-address-pool-fields">
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group">
                                                    <label>Start IP</label>
                                                    <input type="text" class="form-control network-dhcp-start" placeholder="e.g. 192.168.1.100" autocomplete="off">
                                                    <small class="form-text text-muted">First address in the pool (IPv4).</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group">
                                                    <label>Pool size</label>
                                                    <input type="number" class="form-control network-dhcp-end" placeholder="e.g. 101" min="1" max="16777216" step="1">
                                                    <small class="form-text text-muted">Number of addresses (must fit within your subnet).</small>
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
                                                <label>VLAN ID <small class="text-muted font-weight-normal">(1–4094)</small></label>
                                                <input type="number" class="form-control network-vlan-id" placeholder="None" min="1" max="4094" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Tagging</label>
                                                <select class="form-control network-vlan-tagging" disabled>
                                                    <option value="disabled">Disabled</option>
                                                    <option value="tagged">Tagged</option>
                                                    <option value="untagged">Untagged</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible: MAC Filtering & IP Reservations -->
                <div class="collapsible-section" data-collapse-id="mac-filter-__ID__">
                    <div class="collapsible-section-header" data-target="mac-filter-__ID__">
                        <h6 class="collapsible-section-title">
                            <span class="section-icon" style="background:rgba(234,84,85,0.12);">
                                <i data-feather="shield" style="color:#ea5455;width:14px;height:14px;"></i>
                            </span>
                            MAC Filtering &amp; IP Reservations
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="mac-filter-__ID__" style="display:none;">
                        <div class="mac-res-grid mt-2">

                            <!-- ── col 1: MAC Address Filtering ── -->
                            <div class="mac-res-panel mac-res-panel-shield">
                                <div class="mac-res-panel-head">
                                    <span class="mac-res-panel-icon">
                                        <i data-feather="shield" style="width:13px;height:13px;color:#ea5455;"></i>
                                    </span>
                                    <span class="mac-res-panel-title">MAC Address Filtering</span>
                                </div>
                                <div class="mac-res-panel-body">
                                    <!-- Per-network-type hint (set by JS) -->
                                    <p class="network-mac-filter-hint text-muted mb-2" style="font-size:0.82rem;"></p>
                                    <!-- Add row -->
                                    <div class="mac-add-row">
                                        <input type="text" class="form-control form-control-sm network-mac-input mac-add-input" placeholder="00:11:22:33:44:55">
                                        <select class="form-control form-control-sm network-mac-type-select mac-add-type">
                                            <option value="block" selected>Block</option>
                                            <option value="bypass">Bypass Auth</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary network-mac-add-btn" type="button">
                                            <i data-feather="plus" style="width:13px;height:13px;"></i> Add
                                        </button>
                                    </div>

                                    <!-- Table -->
                                    <div class="rl-table-wrap">
                                        <table class="rl-table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>MAC Address</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="network-mac-list">
                                                <tr class="rl-empty-row network-mac-empty">
                                                    <td colspan="3">No MAC rules added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="rl-pagination mac-list-pagination" style="display:none;">
                                        <button class="rl-page-btn mac-page-btn" data-dir="prev" disabled>
                                            <i data-feather="chevron-left" style="width:13px;height:13px;"></i>
                                        </button>
                                        <span class="rl-page-info mac-page-info"></span>
                                        <button class="rl-page-btn mac-page-btn" data-dir="next">
                                            <i data-feather="chevron-right" style="width:13px;height:13px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ── col 2: DHCP IP Reservations ── -->
                            <div class="mac-res-panel mac-res-panel-bookmark dhcp-reservations-section" style="display:none;">
                                <div class="mac-res-panel-head">
                                    <span class="mac-res-panel-icon">
                                        <i data-feather="bookmark" style="width:13px;height:13px;color:#17a2b8;"></i>
                                    </span>
                                    <span class="mac-res-panel-title">DHCP IP Reservations</span>
                                </div>
                                <div class="mac-res-panel-body">
                                    <!-- Add row -->
                                    <div class="mac-add-row mb-2">
                                        <input type="text" class="form-control form-control-sm network-reservation-mac mac-add-input" placeholder="MAC  00:11:22:33:44:55">
                                        <input type="text" class="form-control form-control-sm network-reservation-ip" placeholder="IP  192.168.1.50" style="width:130px;flex-shrink:0;">
                                        <button class="btn btn-sm btn-outline-info network-reservation-add-btn" type="button">
                                            <i data-feather="plus" style="width:13px;height:13px;"></i> Add
                                        </button>
                                    </div>

                                    <!-- Table -->
                                    <div class="rl-table-wrap">
                                        <table class="rl-table">
                                            <thead>
                                                <tr>
                                                    <th>MAC Address</th>
                                                    <th>Reserved IP</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="network-reservation-list">
                                                <tr class="rl-empty-row network-reservation-empty">
                                                    <td colspan="3">No reservations added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="rl-pagination res-list-pagination" style="display:none;">
                                        <button class="rl-page-btn res-page-btn" data-dir="prev" disabled>
                                            <i data-feather="chevron-left" style="width:13px;height:13px;"></i>
                                        </button>
                                        <span class="rl-page-info res-page-info"></span>
                                        <button class="rl-page-btn res-page-btn" data-dir="next">
                                            <i data-feather="chevron-right" style="width:13px;height:13px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.mac-res-grid -->

                        <!-- Save -->
                        <div class="mac-res-save-row">
                            <span class="mac-res-save-hint">
                                <i data-feather="info" style="width:12px;height:12px;"></i>
                                Add or remove entries, then click Save to apply.
                            </span>
                            <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                <i data-feather="save" style="width:13px;height:13px;" class="mr-1"></i> Save
                            </button>
                        </div>
                    </div>
                </div>

            </div><!-- end .card-body -->
        </div><!-- end .card -->
    </div><!-- end .tab-pane -->
</template>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
<script src="/working-hours/interactive-schedule.js"></script>
<script>
    window.APP_CONFIG_V5 = {
        maxNetworks: {{ (int) env('MAX_NETWORKS_PER_LOCATION', 4) }},
        apiBase: '{{ rtrim(config("app.url"), "/") }}/api',
        messages: {
            macFilterHintPassword:   'Only blocking is available on password-protected networks. Bypassing authentication is not applicable here.',
            macFilterHintOpen:       'Only blocking is available on open networks. There is no portal or password to bypass.',
            macFilterHintCaptive:    'Both block (deny access) and bypass (allow through the portal without authentication) are available for captive portal networks.',
            portalPasswordRequired:  'A shared password is required when the Password login method is enabled.',
        }
    };
</script>
<script src="/assets/js/location-networks-v5.js?v=11"></script>
@endpush
