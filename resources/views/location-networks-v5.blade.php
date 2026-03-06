@extends('layouts.app')

@section('title', 'Network Settings - monsieur-wifi Controller')

@php $locale = 'en'; @endphp

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
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
    .network-type-password { background: rgba(115,103,240,0.15); color: #7367f0; border: 1px solid rgba(115,103,240,0.2); }
    .network-type-captive_portal { background: rgba(40,199,111,0.15); color: #28c76f; border: 1px solid rgba(40,199,111,0.2); }
    .network-type-open { background: rgba(255,159,67,0.15); color: #ff9f43; border: 1px solid rgba(255,159,67,0.2); }
    /* Active tab — badge needs solid, opaque colours to show on white gradient */
    .nav-link.active .network-type-password { background: #5e50d6; color: #fff; border-color: #4e40c6; }
    .nav-link.active .network-type-captive_portal { background: #1fab62; color: #fff; border-color: #199955; }
    .nav-link.active .network-type-open { background: #e08a28; color: #fff; border-color: #c97820; }
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
    .panel-password { border-color: rgba(115,103,240,0.25); }
    .panel-password .network-type-panel-header { background: rgba(115,103,240,0.05); border-bottom-color: rgba(115,103,240,0.15); }
    .panel-password .network-type-panel-icon { background: rgba(115,103,240,0.12); }
    .panel-password .network-type-panel-title { color: #7367f0; }
    .panel-captive { border-color: rgba(40,199,111,0.25); }
    .panel-captive .network-type-panel-header { background: rgba(40,199,111,0.05); border-bottom-color: rgba(40,199,111,0.15); }
    .panel-captive .network-type-panel-icon { background: rgba(40,199,111,0.12); }
    .panel-captive .network-type-panel-title { color: #28c76f; }
    .panel-open { border-color: rgba(255,159,67,0.25); }
    .panel-open .network-type-panel-header { background: rgba(255,159,67,0.05); border-bottom-color: rgba(255,159,67,0.15); }
    .panel-open .network-type-panel-icon { background: rgba(255,159,67,0.12); }
    .panel-open .network-type-panel-title { color: #ff9f43; }
    .panel-sub-section { border-top: 1px solid #f1f3f4; padding-top: 1rem; margin-top: 1rem; }
    .panel-sub-label { font-size: 0.75rem; font-weight: 700; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem; }
    .panel-ip { border-color: rgba(23,162,184,0.25); }
    .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.05); border-bottom-color: rgba(23,162,184,0.15); }
    .panel-ip .network-type-panel-icon { background: rgba(23,162,184,0.12); }
    .panel-ip .network-type-panel-title { color: #17a2b8; }
    .panel-mac { border-color: rgba(234,84,85,0.25); }
    .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.05); border-bottom-color: rgba(234,84,85,0.15); }
    .panel-mac .network-type-panel-icon { background: rgba(234,84,85,0.12); }
    .panel-mac .network-type-panel-title { color: #ea5455; }
    .mac-list-box { border: 1px solid #e9ecef; border-radius: 8px; max-height: 180px; overflow-y: auto; min-height: 48px; }
    .mac-address-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 14px; border-bottom: 1px solid #f1f3f4; font-size: 0.88rem; font-family: monospace; }
    .mac-address-item:last-child { border-bottom: none; }
    .input-group-append .btn, .input-group-prepend .btn { height: 50px; }

    /* Auth method pills */
    .auth-method-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 1rem; }
    .auth-method-pill { border: 1.5px solid #e9ecef; background: #fff; border-radius: 20px; padding: 5px 14px; font-size: 0.8rem; font-weight: 600; color: #6c757d; cursor: pointer; transition: all 0.2s; }
    .auth-method-pill:hover { border-color: #28c76f; color: #28c76f; }
    .auth-method-pill.active { border-color: #28c76f; background: rgba(40,199,111,0.1); color: #28c76f; }

    /* Dark mode */
    .dark-layout .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.08); }
    .dark-layout .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.08); }
    .dark-layout .mac-list-box { border-color: #3b4253; }
    .dark-layout .mac-address-item { border-bottom-color: #3b4253; }
    .dark-layout .panel-sub-section { border-top-color: #3b4253; }
    .semi-dark-layout .panel-ip .network-type-panel-header { background: rgba(23,162,184,0.08); }
    .semi-dark-layout .panel-mac .network-type-panel-header { background: rgba(234,84,85,0.08); }
    .semi-dark-layout .mac-list-box { border-color: #3b4253; }
    .semi-dark-layout .mac-address-item { border-bottom-color: #3b4253; }
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
                    <button class="btn btn-outline-danger btn-sm network-delete-btn" data-network-id="__ID__">
                        <i data-feather="trash-2" class="mr-1"></i> Delete Network
                    </button>
                    <button class="btn custom-btn network-save-btn" data-network-id="__ID__">
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
                        <input type="text" class="form-control network-ssid" placeholder="Network name (SSID)">
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
                        <div class="custom-control custom-switch mb-0">
                            <input type="checkbox" class="custom-control-input network-enabled" id="network-enabled-__ID__" checked>
                            <label class="custom-control-label" for="network-enabled-__ID__">Enabled</label>
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
                                        <label>Method</label>
                                        <select class="form-control network-auth-method">
                                            <option value="click-through" selected>Click-through (No Auth)</option>
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
                                            <input type="text" class="form-control network-dns1" placeholder="8.8.8.8" value="8.8.8.8">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Alt DNS</label>
                                            <input type="text" class="form-control network-dns2" placeholder="8.8.4.4" value="8.8.4.4">
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-sub-section">
                                    <div class="panel-sub-label">DHCP Server</div>
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="d-flex align-items-center" style="height:50px;">
                                                    <div class="custom-control custom-switch mb-0">
                                                        <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                                        <label class="custom-control-label" for="network-dhcp-__ID__">Enable DHCP</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
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

                <!-- Collapsible: MAC Address Filtering -->
                <div class="collapsible-section" data-collapse-id="mac-filter-__ID__">
                    <div class="collapsible-section-header" data-target="mac-filter-__ID__">
                        <h6 class="collapsible-section-title">
                            <span class="section-icon" style="background:rgba(234,84,85,0.12);">
                                <i data-feather="shield" style="color:#ea5455;width:14px;height:14px;"></i>
                            </span>
                            MAC Address Filtering
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="mac-filter-__ID__" style="display:none;">
                        <div class="network-type-panel panel-mac mt-2">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="shield" style="color:#ea5455;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">MAC Address Filtering</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="panel-sub-label">Filter Mode</div>
                                        <div class="form-group">
                                            <select class="form-control network-mac-filter-mode">
                                                <option value="allow-all">Allow All Devices</option>
                                                <option value="allow-listed">Allow Listed Only</option>
                                                <option value="block-listed">Block Listed Devices</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="panel-sub-label">MAC Address List</div>
                                        <div class="form-group mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control network-mac-input" placeholder="00:11:22:33:44:55">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary network-mac-add-btn" type="button">
                                                        <i data-feather="plus" style="width:14px;height:14px;"></i> Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mac-list-box">
                                            <div class="network-mac-list"></div>
                                            <div class="text-center text-muted p-3 network-mac-empty">
                                                <i data-feather="inbox" style="width:18px;height:18px;margin-bottom:4px;display:block;margin-left:auto;margin-right:auto;"></i>
                                                <small>No MAC addresses added</small>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                                <i data-feather="save" class="mr-1"></i> Save MAC Filter
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
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
<script src="/working-hours/interactive-schedule.js"></script>
<script>
    window.APP_CONFIG_V5 = {
        maxNetworks: {{ (int) env('MAX_NETWORKS_PER_LOCATION', 4) }},
        apiBase: '{{ rtrim(config("app.url"), "/") }}/api'
    };
</script>
<script src="/assets/js/location-networks-v5.js?v=1"></script>
@endpush
