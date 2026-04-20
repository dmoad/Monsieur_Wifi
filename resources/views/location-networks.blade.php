@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('location_networks.page_title'))

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
                <h2 class="content-header-title float-left mb-0">{{ __('location_networks.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/locations">{{ __('locations.heading') }}</a></li>
                        <li class="breadcrumb-item"><a id="breadcrumb-location-link" href="#">{{ __('common.loading') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('location_networks.breadcrumb_networks') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <a id="back-to-location-btn" href="#" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="mr-1"></i> {{ __('location_networks.back_to_location') }}
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
                    <h5 class="mb-0 location_name" style="font-weight:700; padding-left:10px;">{{ __('common.loading') }}</h5>
                    <small class="text-muted location_address" style="padding-left:10px;"></small>
                </div>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <div class="d-flex align-items-center" style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:6px 14px;gap:10px;">
                    <i data-feather="layers" style="width:14px;height:14px;color:#7367f0;flex-shrink:0;"></i>
                    <span style="font-size:0.85rem;font-weight:600;color:#495057;white-space:nowrap;">{{ __('location_networks.vlan_support') }}</span>
                    <div class="custom-control custom-switch mb-0">
                        <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                        <label class="custom-control-label" for="vlan-enabled"></label>
                    </div>
                </div>
                <button class="btn custom-btn btn-sm" id="add-network-btn" disabled style="height:36px;white-space:nowrap;">
                    <i data-feather="plus" class="mr-1"></i> {{ __('location_networks.add_network') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Network tabs nav -->
    <ul class="nav nav-tabs" role="tablist" id="network-tabs-nav">
        <li class="nav-item" id="network-tabs-loading">
            <span class="nav-link disabled"><i class="fas fa-spinner fa-spin mr-1"></i>{{ __('location_networks.loading_networks') }}</span>
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
            <span class="network-tab-label">{{ __('location_networks.tab_label_default') }}</span>
            <span class="network-type-badge network-type-__TYPE__">__TYPE_LABEL__</span>
        </a>
    </li>
</template>

<!-- Tab pane template -->
<template id="network-pane-tpl">
    <div class="tab-pane fade" id="network-pane-__ID__" role="tabpanel" data-network-id="__ID__">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><span class="network-pane-title">{{ __('location_networks.pane_title_default') }}</span></h4>
                <div class="d-flex align-items-center gap-2" style="gap:0.5rem;">
                    <button type="button" class="btn btn-outline-danger btn-sm network-delete-btn" data-network-id="__ID__">
                        <i data-feather="trash-2" class="mr-1"></i> {{ __('location_networks.delete_network') }}
                    </button>
                    <button type="button" class="btn custom-btn network-save-btn" data-network-id="__ID__">
                        <i data-feather="save" class="mr-1"></i> {{ __('location_networks.save_settings') }}
                    </button>
                </div>
            </div>
            <div class="card-body">

                <!-- Network identity bar: type pills + SSID + visibility + enabled -->
                <div class="network-identity-bar mt-1">
                    <select class="network-type-select d-none" data-network-id="__ID__">
                        <option value="password">{{ __('location_networks.type_password_wifi') }}</option>
                        <option value="captive_portal">{{ __('location_networks.type_captive_portal') }}</option>
                        <option value="open">{{ __('location_networks.type_open_essid') }}</option>
                    </select>
                    <div class="network-type-pill-group">
                        <button type="button" class="network-type-pill" data-type="password">
                            <i data-feather="lock" style="width:13px;height:13px;"></i> {{ __('location_networks.pill_password') }}
                        </button>
                        <button type="button" class="network-type-pill" data-type="captive_portal">
                            <i data-feather="layout" style="width:13px;height:13px;"></i> {{ __('location_networks.pill_captive_portal') }}
                        </button>
                        <button type="button" class="network-type-pill" data-type="open">
                            <i data-feather="wifi" style="width:13px;height:13px;"></i> {{ __('location_networks.pill_open') }}
                        </button>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-ssid-wrap">
                        <i data-feather="wifi" class="ssid-icon" style="width:15px;height:15px;"></i>
                        <input type="text" class="form-control network-ssid" placeholder="{{ __('location_networks.ssid_placeholder') }}" maxlength="32">
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-visibility-wrap">
                        <i data-feather="eye" style="width:14px;height:14px;color:#adb5bd;flex-shrink:0;"></i>
                        <select class="network-visible">
                            <option value="1">{{ __('location_networks.visibility_broadcast') }}</option>
                            <option value="0">{{ __('location_networks.visibility_hidden') }}</option>
                        </select>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-enabled-wrap">
                        <input type="checkbox" class="network-toggle-checkbox network-enabled" id="network-enabled-__ID__" checked>
                        <label class="network-toggle-btn" for="network-enabled-__ID__">
                            <i data-feather="power" style="width:13px;height:13px;"></i>
                            <span class="network-toggle-label-on">{{ __('common.active') }}</span>
                            <span class="network-toggle-label-off">{{ __('common.inactive') }}</span>
                        </label>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-qos-wrap">
                        <input type="checkbox" class="network-toggle-checkbox network-qos-policy" id="network-qos-__ID__">
                        <label class="network-toggle-btn" for="network-qos-__ID__">
                            <i data-feather="zap" style="width:13px;height:13px;"></i>
                            <span>{{ __('location_networks.full_qos') }}</span>
                        </label>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-radio-wrap">
                        <i data-feather="radio" style="width:14px;height:14px;color:#adb5bd;"></i>
                        <select class="network-radio">
                            <option value="all">{{ __('location_networks.band_all') }}</option>
                            <option value="2.4">{{ __('location_networks.band_2_4_only') }}</option>
                            <option value="5">{{ __('location_networks.band_5_only') }}</option>
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
                            <h6 class="network-type-panel-title">{{ __('location_networks.panel_security_encryption') }}</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.wifi_password') }}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control network-password" placeholder="{{ __('location_networks.wifi_password_placeholder') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary network-toggle-password" type="button"><i data-feather="eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.security_protocol') }}</label>
                                        <select class="form-control network-security">
                                            <option value="wpa2-psk" selected>{{ __('location_networks.security_wpa2_psk_rec') }}</option>
                                            <option value="wpa-wpa2-psk">{{ __('location_networks.security_wpa_wpa2_mixed') }}</option>
                                            <option value="wpa3-psk">{{ __('location_networks.security_wpa3_psk_secure') }}</option>
                                            <option value="wep">{{ __('location_networks.security_wep_legacy') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.cipher_suites') }}</label>
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
                            <h6 class="network-type-panel-title">{{ __('location_networks.panel_captive_portal_config') }}</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="panel-sub-label">{{ __('location_networks.sub_authentication') }}</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.login_methods') }} <small class="text-muted font-weight-normal">{{ __('location_networks.login_methods_hint') }}</small></label>
                                        <div class="network-auth-method-pills d-flex flex-wrap" style="gap:6px;">
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="click-through">
                                                <i data-feather="wifi" style="width:13px;height:13px;"></i> {{ __('location_networks.method_click_through') }}
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="password">
                                                <i data-feather="lock" style="width:13px;height:13px;"></i> {{ __('location_networks.method_password') }}
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="sms">
                                                <i data-feather="message-circle" style="width:13px;height:13px;"></i> {{ __('location_networks.method_sms') }}
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="email">
                                                <i data-feather="mail" style="width:13px;height:13px;"></i> {{ __('location_networks.method_email') }}
                                            </button>
                                            <button type="button" class="network-auth-method-pill btn btn-sm btn-outline-secondary" data-method="social">
                                                <i data-feather="share-2" style="width:13px;height:13px;"></i> {{ __('location_networks.method_social') }}
                                            </button>
                                        </div>
                                        <small class="form-text text-muted mt-1">{{ __('location_networks.multiple_methods_hint') }}</small>
                                    </div>
                                    <div class="form-group network-captive-password-group" style="display:none;">
                                        <label>{{ __('location_networks.shared_password') }}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control network-portal-password">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary network-toggle-portal-password" type="button"><i data-feather="eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group network-email-otp-group" style="display:none;">
                                        <div class="custom-control custom-switch mb-0">
                                            <input type="checkbox" class="custom-control-input network-email-require-otp" id="network-email-otp-__ID__" checked>
                                            <label class="custom-control-label" for="network-email-otp-__ID__">Require email verification code (OTP)</label>
                                        </div>
                                        <small class="form-text text-muted mt-1">When disabled, guests enter their email without receiving a verification code.</small>
                                    </div>
                                    <div class="form-group network-social-group" style="display:none;">
                                        <label>{{ __('location_networks.social_provider') }}</label>
                                        <select class="form-control network-social-method">
                                            <option value="facebook">Facebook</option>
                                            <option value="google">Google</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.portal_design') }}</label>
                                        <select class="form-control network-portal-design-id">
                                            <option value="">{{ __('location_networks.default_design') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('location_networks.redirect_url') }} <small class="text-muted font-weight-normal">{{ __('common.optional') }}</small></label>
                                        <input type="url" class="form-control network-redirect-url" placeholder="{{ __('location_networks.redirect_url_placeholder') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('location_networks.session_timeout') }}</label>
                                        <select class="form-control network-session-timeout">
                                            <option value="60">{{ __('location_networks.dur_1_hour') }}</option><option value="120">{{ __('location_networks.dur_2_hours') }}</option><option value="180">{{ __('location_networks.dur_3_hours') }}</option>
                                            <option value="240">{{ __('location_networks.dur_4_hours') }}</option><option value="300">{{ __('location_networks.dur_5_hours') }}</option><option value="360">{{ __('location_networks.dur_6_hours') }}</option>
                                            <option value="720">{{ __('location_networks.dur_12_hours') }}</option><option value="1440">{{ __('location_networks.dur_1_day') }}</option><option value="10080">{{ __('location_networks.dur_1_week') }}</option>
                                            <option value="43200">{{ __('location_networks.dur_3_months') }}</option><option value="172800">{{ __('location_networks.dur_1_year') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('location_networks.idle_timeout') }}</label>
                                        <select class="form-control network-idle-timeout">
                                            <option value="15">{{ __('location_networks.dur_15_min') }}</option><option value="30">{{ __('location_networks.dur_30_min') }}</option><option value="45">{{ __('location_networks.dur_45_min') }}</option>
                                            <option value="60">{{ __('location_networks.dur_1_hour') }}</option><option value="120">{{ __('location_networks.dur_2_hours') }}</option><option value="240">{{ __('location_networks.dur_4_hours') }}</option>
                                            <option value="720">{{ __('location_networks.dur_12_hours') }}</option><option value="1440">{{ __('location_networks.dur_1_day') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-sub-section">
                                <div class="panel-sub-label">{{ __('location_networks.sub_bandwidth_limits') }}</div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="download" style="width:13px;height:13px;margin-right:4px;"></i>{{ __('location_networks.download_mbps') }}</label>
                                            <input type="number" class="form-control network-download-limit" placeholder="{{ __('location_networks.unlimited') }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="upload" style="width:13px;height:13px;margin-right:4px;"></i>{{ __('location_networks.upload_mbps') }}</label>
                                            <input type="number" class="form-control network-upload-limit" placeholder="{{ __('location_networks.unlimited') }}" min="0">
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
                            {{ __('location_networks.working_hours') }}
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
                            <h6 class="network-type-panel-title">{{ __('location_networks.panel_open_network') }}</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="d-flex align-items-start" style="gap:12px;">
                                <i data-feather="alert-triangle" style="width:20px;height:20px;color:#ff9f43;flex-shrink:0;margin-top:1px;"></i>
                                <div>
                                    <p class="mb-1" style="font-weight:600;color:#ff9f43;font-size:0.95rem;">{{ __('location_networks.no_auth_required') }}</p>
                                    <p class="mb-0 text-muted" style="font-size:0.88rem;">{{ __('location_networks.open_network_warning') }}</p>
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
                            {{ __('location_networks.section_ip_dhcp') }}
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="ip-dhcp-__ID__" style="display:none;">
                        <div class="network-type-panel panel-ip mt-2">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="globe" style="color:#17a2b8;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">{{ __('location_networks.panel_ip_config') }}</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="panel-sub-label">{{ __('location_networks.sub_addressing') }}</div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.ip_mode') }}</label>
                                            <select class="form-control network-ip-mode">
                                                <option value="static">{{ __('location_networks.ip_mode_static') }}</option>
                                                <option value="bridge_lan">{{ __('location_networks.ip_mode_bridge_lan') }}</option>
                                                <option value="bridge">{{ __('location_networks.ip_mode_bridge') }}</option>
                                            </select>
                                            <small class="form-text text-muted network-ip-mode-bridge-hint" style="display:none;"></small>
                                        </div>
                                    </div>
                                    <div class="network-bridge-lan-dhcp-mode-wrap col-md-3" style="display:none;">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.lan_dhcp_mode') }}</label>
                                            <select class="form-control network-bridge-lan-dhcp-mode">
                                                <option value="dhcp_client">{{ __('location_networks.lan_dhcp_client') }}</option>
                                                <option value="dhcp_server">{{ __('location_networks.lan_dhcp_server') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.ip_address') }}</label>
                                            <input type="text" class="form-control network-ip-address" placeholder="{{ __('location_networks.ip_address_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.netmask') }}</label>
                                            <input type="text" class="form-control network-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.gateway') }}</label>
                                            <input type="text" class="form-control network-gateway" placeholder="{{ __('location_networks.gateway_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.primary_dns') }}</label>
                                            <div class="dns-field-wrapper" title="{{ __('location_networks.dns_field_title') }}">
                                                <input type="text" class="form-control network-dns1" placeholder="8.8.8.8">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>{{ __('location_networks.alt_dns') }}</label>
                                            <div class="dns-field-wrapper" title="{{ __('location_networks.dns_field_title') }}">
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
                                                <div class="dhcp-address-pool-panel-title">{{ __('location_networks.dhcp_pool_title') }}</div>
                                                <div class="dhcp-address-pool-panel-sub">{{ __('location_networks.dhcp_pool_desc') }}</div>
                                            </div>
                                        </div>
                                        <div class="row align-items-end">
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group mb-md-0">
                                                    <label class="dhcp-address-pool-switch-label">{{ __('location_networks.dhcp_server_label') }}</label>
                                                    <div class="custom-control custom-switch mb-0">
                                                        <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                                        <label class="custom-control-label" for="network-dhcp-__ID__">{{ __('location_networks.enable_dhcp') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row dhcp-address-pool-fields">
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group">
                                                    <label>{{ __('location_networks.start_ip') }}</label>
                                                    <input type="text" class="form-control network-dhcp-start" placeholder="{{ __('location_networks.start_ip_placeholder') }}" autocomplete="off">
                                                    <small class="form-text text-muted">{{ __('location_networks.start_ip_hint') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-5">
                                                <div class="form-group">
                                                    <label>{{ __('location_networks.pool_size') }}</label>
                                                    <input type="number" class="form-control network-dhcp-end" placeholder="{{ __('location_networks.pool_size_placeholder') }}" min="1" max="16777216" step="1">
                                                    <small class="form-text text-muted">{{ __('location_networks.pool_size_hint') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-sub-section">
                                    <div class="panel-sub-label">{{ __('location_networks.sub_vlan') }}</div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{ __('location_networks.vlan_id') }} <small class="text-muted font-weight-normal">{{ __('location_networks.vlan_id_range') }}</small></label>
                                                <input type="number" class="form-control network-vlan-id" placeholder="{{ __('location_networks.vlan_none') }}" min="1" max="4094" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{ __('location_networks.tagging') }}</label>
                                                <select class="form-control network-vlan-tagging" disabled>
                                                    <option value="disabled">{{ __('location_networks.tagging_disabled') }}</option>
                                                    <option value="tagged">{{ __('location_networks.tagging_tagged') }}</option>
                                                    <option value="untagged">{{ __('location_networks.tagging_untagged') }}</option>
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
                            {{ __('location_networks.section_mac_filter_reservations') }}
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
                                    <span class="mac-res-panel-title">{{ __('location_networks.mac_filtering') }}</span>
                                </div>
                                <div class="mac-res-panel-body">
                                    <!-- Per-network-type hint (set by JS) -->
                                    <p class="network-mac-filter-hint text-muted mb-2" style="font-size:0.82rem;"></p>
                                    <!-- Add row -->
                                    <div class="mac-add-row">
                                        <input type="text" class="form-control form-control-sm network-mac-input mac-add-input" placeholder="00:11:22:33:44:55">
                                        <select class="form-control form-control-sm network-mac-type-select mac-add-type">
                                            <option value="block" selected>{{ __('location_networks.mac_add_type_block') }}</option>
                                            <option value="bypass">{{ __('location_networks.mac_add_type_bypass') }}</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary network-mac-add-btn" type="button">
                                            <i data-feather="plus" style="width:13px;height:13px;"></i> {{ __('common.add') }}
                                        </button>
                                    </div>

                                    <!-- Table -->
                                    <div class="rl-table-wrap">
                                        <table class="rl-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('location_networks.table_col_type') }}</th>
                                                    <th>{{ __('location_networks.table_col_mac') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="network-mac-list">
                                                <tr class="rl-empty-row network-mac-empty">
                                                    <td colspan="3">{{ __('location_networks.mac_list_empty') }}</td>
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
                                    <span class="mac-res-panel-title">{{ __('location_networks.dhcp_reservations') }}</span>
                                </div>
                                <div class="mac-res-panel-body">
                                    <!-- Add row -->
                                    <div class="mac-add-row mb-2">
                                        <input type="text" class="form-control form-control-sm network-reservation-mac mac-add-input" placeholder="{{ __('location_networks.reservation_mac_placeholder') }}">
                                        <input type="text" class="form-control form-control-sm network-reservation-ip" placeholder="{{ __('location_networks.reservation_ip_placeholder') }}" style="width:130px;flex-shrink:0;">
                                        <button class="btn btn-sm btn-outline-info network-reservation-add-btn" type="button">
                                            <i data-feather="plus" style="width:13px;height:13px;"></i> {{ __('common.add') }}
                                        </button>
                                    </div>

                                    <!-- Table -->
                                    <div class="rl-table-wrap">
                                        <table class="rl-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('location_networks.table_col_mac') }}</th>
                                                    <th>{{ __('location_networks.table_col_reserved_ip') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="network-reservation-list">
                                                <tr class="rl-empty-row network-reservation-empty">
                                                    <td colspan="3">{{ __('location_networks.reservation_list_empty') }}</td>
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
                                {{ __('location_networks.mac_save_hint') }}
                            </span>
                            <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                <i data-feather="save" style="width:13px;height:13px;" class="mr-1"></i> {{ __('location_networks.mac_save_btn') }}
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
        messages: @json(__('location_networks.js_messages')),
        schedulerLabels: @json(__('location_networks.js_scheduler')),
        typeLabels: @json(__('location_networks.js_type_labels')),
    };
</script>
<script src="/assets/js/location-networks.js?v=1"></script>
@endpush
