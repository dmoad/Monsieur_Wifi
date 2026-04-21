@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('location_details.page_title'))

@push('styles')
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/charts/apexcharts.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/maps/leaflet.min.css">
<link rel="stylesheet" type="text/css" href="/assets/css/location-details.css">
<style>
/* Page-level tab nav (matches zone-details .zd-tabs pattern) */
.ld-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--mw-border);
    margin-bottom: var(--mw-space-xl);
}
.ld-tab {
    padding: 10px var(--mw-space-xl);
    font-size: 13.5px;
    font-weight: 500;
    color: var(--mw-text-muted);
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    cursor: pointer;
    transition: color 0.15s, border-color 0.15s;
}
.ld-tab:hover { color: var(--mw-text-primary); }
.ld-tab.active {
    color: var(--mw-primary);
    font-weight: 600;
    border-bottom-color: var(--mw-primary);
}
.ld-panel { display: none; }
.ld-panel.active { display: block; }

/* Overview cards — flat tokenised look (override .stat-card gradient/hover-lift) */
#ld-panel-overview .stat-card {
    background: var(--mw-bg-surface);
    border: 1px solid var(--mw-border-light);
    border-left-width: 1px;
    border-left-color: var(--mw-border-light);
    box-shadow: var(--mw-shadow-card);
    padding: var(--mw-space-xl);
    transition: none;
}
#ld-panel-overview .stat-card:hover {
    transform: none;
    box-shadow: var(--mw-shadow-card);
}

/* Device card pieces */
.ld-ov-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--mw-space-md);
    margin-bottom: var(--mw-space-md);
}
.ld-ov-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--mw-primary);
    line-height: 1.25;
    margin-bottom: 2px;
}
.ld-ov-sub, .ld-ov-sub-zone {
    font-size: 12px;
    color: var(--mw-text-muted);
}
.ld-ov-sub-zone { margin-top: 2px; }
.ld-ov-sub-zone strong {
    color: var(--mw-text-secondary);
    font-weight: 500;
}

/* Device status pill — soft-tint + leading dot, scoped override of .status-badge */
#ld-panel-overview .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px 3px 8px;
    border-radius: var(--mw-radius-full);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    line-height: 1.2;
    text-transform: uppercase;
    box-shadow: none;
    flex-shrink: 0;
    background: transparent;
    color: inherit;
    transition: none;
}
#ld-panel-overview .status-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}
#ld-panel-overview .status-badge.status-online {
    background: rgba(22, 163, 74, 0.12);
    color: var(--mw-success);
}
#ld-panel-overview .status-badge.status-offline {
    background: rgba(220, 38, 38, 0.1);
    color: var(--mw-danger);
}

/* MAC chip row */
.ld-ov-mac-row {
    display: flex;
    align-items: center;
    gap: var(--mw-space-sm);
    margin: var(--mw-space-md) 0;
}
.ld-ov-mac-chip {
    font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace;
    font-size: 11.5px;
    color: var(--mw-text-muted);
    background: var(--mw-bg-muted);
    padding: 3px 8px;
    border-radius: var(--mw-radius-sm);
    border: 1px solid var(--mw-border-light);
}

/* Details key/value grid */
.ld-ov-details {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 6px var(--mw-space-xl);
    font-size: 13px;
    margin: 0 0 var(--mw-space-xl);
}
.ld-ov-details dt {
    color: var(--mw-text-muted);
    font-weight: 400;
    margin: 0;
}
.ld-ov-details dd {
    font-weight: 600;
    color: var(--mw-text-primary);
    margin: 0;
    text-align: right;
}

/* Action buttons row */
.ld-ov-actions {
    display: flex;
    gap: var(--mw-space-sm);
}
.ld-ov-actions .btn {
    flex: 1;
    justify-content: center;
}
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('location_details.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/locations">{{ __('locations.heading') }}</a></li>
                        <li class="breadcrumb-item active"><span class="location_name">{{ __('common.loading') }}</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right d-flex align-items-center justify-content-end">
            <button id="clone-location-btn" class="btn btn-outline-secondary">
                <i data-feather="copy" class="mr-1"></i>{{ __('location_details.clone_button') }}
            </button>
        </div>
    </div>
</div>

<div class="ld-tabs">
    <button type="button" class="ld-tab active" data-tab="overview">{{ __('location_details.tab_overview') }}</button>
    <button type="button" class="ld-tab" data-tab="settings">{{ __('location_details.tab_location_details') }}</button>
    <button type="button" class="ld-tab" data-tab="router">{{ __('location_details.tab_router_settings') }}</button>
</div>

<div class="content-body">

<div class="ld-panel active" id="ld-panel-overview">

    <!-- Location Overview -->
    <div class="stats-grid">
        <!-- Device card -->
        <div class="stat-card">
            <div class="ld-ov-head">
                <div>
                    <div class="ld-ov-name location_name"></div>
                    <div class="ld-ov-sub location_address"></div>
                    <div class="ld-ov-sub-zone" id="location-zone-line" style="display:none;">{{ __('location_details.zone_label') }}: <strong class="location_zone"></strong></div>
                </div>
                <span class="status-badge status-offline">{{ __('common.offline') }}</span>
            </div>
            <div class="ld-ov-mac-row">
                <span class="ld-ov-mac-chip">{{ __('location_details.mac_prefix') }} <span class="router_mac_address_header">{{ __('common.loading') }}</span></span>
                <button class="btn btn-sm btn-outline-secondary" id="edit-mac-btn" style="font-size: 11px;">
                    <i data-feather="edit" class="mr-1" style="width: 12px; height: 12px;"></i>{{ __('location_details.edit_button') }}
                </button>
            </div>
            <dl class="ld-ov-details">
                <dt>{{ __('location_details.router_model') }}</dt><dd class="router_model_updated"></dd>
                <dt>{{ __('location_details.mac_address') }}</dt><dd class="router_mac_address"></dd>
                <dt>{{ __('location_details.firmware') }}</dt><dd class="router_firmware"></dd>
                <dt>{{ __('location_details.total_users') }}</dt><dd class="connected_users"></dd>
                <dt>{{ __('location_details.daily_usage') }}</dt><dd class="daily_usage"></dd>
                <dt>{{ __('location_details.uptime') }}</dt><dd class="uptime"></dd>
            </dl>
            <div class="ld-ov-actions">
                <button class="btn btn-primary btn-sm" id="device-restart-btn"><i data-feather="refresh-cw" class="mr-1"></i>{{ __('location_details.restart_button') }}</button>
                <button class="btn btn-outline-secondary btn-sm" id="update-firmware-btn"><i data-feather="download" class="mr-1"></i>{{ __('location_details.update_button') }}</button>
            </div>
        </div>

        <!-- Usage Stats -->
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('location_details.current_usage') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" id="usage-period-btn">{{ __('location_details.period_today') }}</button>
                    <div class="dropdown-menu dropdown-menu-right" id="usage-period-dropdown">
                        <a class="dropdown-item" href="javascript:void(0);" data-period="today">{{ __('location_details.period_today') }}</a>
                        <a class="dropdown-item" href="javascript:void(0);" data-period="7days">{{ __('location_details.period_7days') }}</a>
                        <a class="dropdown-item" href="javascript:void(0);" data-period="30days">{{ __('location_details.period_30days') }}</a>
                    </div>
                </div>
            </div>
            <div id="usage-loading" class="text-center py-3" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">{{ __('common.loading') }}</span></div>
                <small class="d-block mt-2 text-muted">{{ __('location_details.loading_usage_data') }}</small>
            </div>
            <div class="row text-center" id="usage-data">
                <div class="col-6">
                    <div class="mb-3"><div class="stat-value text-primary" id="download-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_download') }}</div></div>
                    <div><div class="stat-value text-info" id="users-sessions-count"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_users_sessions') }}</div></div>
                </div>
                <div class="col-6">
                    <div class="mb-3"><div class="stat-value text-success" id="upload-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_upload') }}</div></div>
                    <div><div class="stat-value text-warning" id="avg-session-time"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_avg_session') }}</div></div>
                </div>
            </div>
            <div class="text-center mt-3"><small class="text-muted" id="usage-last-updated">{{ __('location_details.loading_data') }}</small></div>
        </div>

        <!-- Map Card -->
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('location_details.location_map_title') }}</h5>
                <small class="text-muted" id="map-coordinates" style="display: none;"></small>
            </div>
            <div id="location-map" class="location-map"></div>
        </div>
    </div>

    <!-- Analytics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{ __('location_details.analytics_title') }}</h4></div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="analytics-chart-card">
                                <div class="chart-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="chart-icon" style="background: rgba(99,102,241,0.12); width:50px; height:50px; border-radius:15px; display:flex; align-items:center; justify-content:center;">
                                                <i data-feather="bar-chart-2" style="color:var(--mw-primary);"></i>
                                            </div>
                                            <div>
                                                <h5 style="margin:0; font-weight:600; color:#2c3e50;">{{ __('location_details.daily_usage_analytics') }}</h5>
                                                <p style="margin:0; color:#6c757d; font-size:0.9rem;">{{ __('location_details.captive_portal_activity') }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex" style="background:rgba(0,0,0,0.05); border-radius:10px; padding:4px; border:1px solid rgba(0,0,0,0.1);">
                                            <button class="period-btn active" data-period="7" style="padding:8px 16px; border:none; background:var(--mw-primary); color:white; border-radius:8px; cursor:pointer;">7D</button>
                                            <button class="period-btn" data-period="30" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">30D</button>
                                            <button class="period-btn" data-period="90" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">90D</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-stats">
                                    <div class="stat-item"><div class="stat-icon stat-users"><i data-feather="users"></i></div><div><span class="stat-value" id="total-users">-</span><span class="stat-label d-block">{{ __('location_details.total_users') }}</span></div></div>
                                    <div class="stat-item"><div class="stat-icon stat-sessions"><i data-feather="activity"></i></div><div><span class="stat-value" id="total-sessions">-</span><span class="stat-label d-block">{{ __('location_details.stat_sessions') }}</span></div></div>
                                    <div class="stat-item"><div class="stat-icon stat-avg"><i data-feather="trending-up"></i></div><div><span class="stat-value" id="avg-daily">-</span><span class="stat-label d-block">{{ __('location_details.stat_daily_avg') }}</span></div></div>
                                </div>
                                <div class="chart-container"><div id="daily-usage-chart"></div></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="online-users-card">
                                <div class="users-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="users-icon" style="background: rgba(99,102,241,0.12);">
                                                <i data-feather="wifi" style="color:var(--mw-primary);"></i>
                                            </div>
                                            <div><h5 style="margin:0; font-weight:600;">{{ __('location_details.live_users') }}</h5><p style="margin:0; color:#6c757d; font-size:0.9rem;">{{ __('location_details.currently_connected') }}</p></div>
                                        </div>
                                        <button class="refresh-btn" id="refresh-online-users"><i data-feather="refresh-cw"></i></button>
                                    </div>
                                    <div class="users-count">
                                        <span class="count-number" id="online-count">0</span>
                                        <span style="color:#6c757d; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px;">{{ __('location_details.online_label') }}</span>
                                        <span id="count-range" style="display:none; font-size:0.75rem; color:#6c757d;"></span>
                                    </div>
                                </div>
                                <div class="users-container">
                                    <div id="online-users-list">
                                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 20px; text-align:center;">
                                            <i data-feather="loader" style="width:40px; height:40px; color:var(--mw-primary); animation:spin 1s linear infinite; margin-bottom:15px;"></i>
                                            <p>{{ __('location_details.loading_online_users') }}</p>
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
                        <h4><i data-feather="wifi" style="width:24px;height:24px;margin-right:10px;vertical-align:middle;"></i> {{ __('location_details.wifi_networks') }}</h4>
                        <p>{{ __('location_details.wifi_networks_description') }}</p>
                        <div id="zone-network-notice" class="alert alert-info py-1 px-2 mb-2" style="display:none;font-size:0.85rem;">
                            <i data-feather="layers" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i>
                            {{ __('location_details.zone_networks_notice') }}
                        </div>
                        <div id="network-summary-badges">
                            <span class="network-summary-badge"><i data-feather="loader" style="width:12px;height:12px;"></i> {{ __('common.loading') }}</span>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                        <a id="manage-networks-btn" href="#" class="btn btn-light btn-lg">
                            <i data-feather="settings" class="mr-2"></i> {{ __('location_details.manage_networks_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /ld-panel-overview -->

<div class="ld-panel" id="ld-panel-settings">
                            <form id="location-info-form" novalidate>

                                <!-- Panel 1: Identity & Address -->
                                <div class="loc-panel panel-location">
                                    <div class="loc-panel-header">
                                        <span class="loc-panel-icon">
                                            <i data-feather="map-pin" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                                        </span>
                                        <h6 class="loc-panel-title">{{ __('location_details.panel_identity_address') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">
                                        <div class="panel-sub-label">{{ __('location_details.sublabel_identity') }}</div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="location-name">{{ __('location_details.location_name') }} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="location-name" placeholder="{{ __('location_details.location_name_placeholder') }}" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group admin-only-field" style="display:none;">
                                                    <label for="router-model-select">{{ __('location_details.router_model') }}</label>
                                                    <select class="form-control" id="router-model-select">
                                                        <option value="">{{ __('common.loading') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="location-status">{{ __('location_details.status_label') }}</label>
                                                    <select class="form-control" id="location-status">
                                                        <option value="active">{{ __('common.active') }}</option>
                                                        <option value="inactive">{{ __('common.inactive') }}</option>
                                                        <option value="maintenance">{{ __('common.maintenance') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel-sub-section">
                                            <div class="panel-sub-label">{{ __('location_details.sublabel_address') }}</div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="location-address">{{ __('location_details.street_address') }}</label>
                                                        <input type="text" class="form-control" id="location-address" placeholder="{{ __('location_details.street_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="location-city">{{ __('location_details.city') }}</label>
                                                        <input type="text" class="form-control" id="location-city" placeholder="{{ __('location_details.city_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="location-state">{{ __('location_details.state_province') }}</label>
                                                        <input type="text" class="form-control" id="location-state" placeholder="{{ __('location_details.state_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="location-postal-code">{{ __('location_details.postal') }}</label>
                                                        <input type="text" class="form-control" id="location-postal-code" placeholder="{{ __('location_details.postal_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="location-country">{{ __('location_details.country') }}</label>
                                                        <input type="text" class="form-control" id="location-country" placeholder="{{ __('location_details.country_placeholder') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel-sub-section">
                                            <div class="panel-sub-label">{{ __('location_details.sublabel_notes') }}</div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="location-description">{{ __('location_details.description_label') }} <small class="text-muted font-weight-normal">{{ __('location_details.description_optional') }}</small></label>
                                                        <textarea class="form-control" id="location-description" rows="2" placeholder="{{ __('location_details.description_placeholder') }}" maxlength="500"></textarea>
                                                        <small class="text-muted"><span id="description-counter">0</span>{{ __('location_details.char_counter_suffix') }}</small>
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
                                        <h6 class="loc-panel-title">{{ __('location_details.panel_contact_ownership') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-manager">{{ __('location_details.manager_name') }}</label>
                                                    <input type="text" class="form-control" id="location-manager" placeholder="{{ __('location_details.manager_name_placeholder') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-contact-email">{{ __('location_details.email') }}</label>
                                                    <input type="email" class="form-control" id="location-contact-email" placeholder="{{ __('location_details.email_placeholder') }}">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-contact-phone">{{ __('location_details.phone') }}</label>
                                                    <input type="tel" class="form-control" id="location-contact-phone" placeholder="{{ __('location_details.phone_placeholder') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="location-owner-group" data-admin-only="true">
                                                <div class="form-group">
                                                    <label for="location-owner">
                                                        {{ __('location_details.owner') }}
                                                        <span style="font-size:0.7rem;background:rgba(var(--mw-primary-rgb,99,102,241),0.12);color:var(--mw-primary);border-radius:10px;padding:1px 7px;font-weight:600;margin-left:4px;">{{ __('location_details.admin_badge') }}</span>
                                                    </label>
                                                    <select class="form-control" id="location-owner"><option value="">{{ __('location_details.select_owner_option') }}</option></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="location-shared-users-group" data-admin-only="true">
                                                <div class="form-group">
                                                    <label for="location-shared-users">
                                                        {{ __('location_details.shared_access') }}
                                                        <span style="font-size:0.7rem;background:rgba(var(--mw-primary-rgb,99,102,241),0.12);color:var(--mw-primary);border-radius:10px;padding:1px 7px;font-weight:600;margin-left:4px;">{{ __('location_details.admin_badge') }}</span>
                                                    </label>
                                                    <select class="select2 form-control" id="location-shared-users" multiple="multiple">
                                                        <!-- populated by JS -->
                                                    </select>
                                                    <small class="form-text text-muted">{{ __('location_details.shared_access_help') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action bar -->
                                <div class="form-action-bar">
                                    <button type="button" id="save-location-info" class="btn custom-btn">
                                        <i data-feather="save" class="mr-1"></i> {{ __('location_details.save_location_info') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetLocationForm()">
                                        <i data-feather="refresh-ccw" class="mr-1"></i> {{ __('common.reset') }}
                                    </button>
                                </div>

                            </form>
</div><!-- /ld-panel-settings -->

<div class="ld-panel" id="ld-panel-router">
                            <!-- WAN -->
                            <div class="content-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title">{{ __('location_details.wan_connection') }}</h5>
                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#wan-settings-modal"><i data-feather="edit" class="mr-1"></i>{{ __('location_details.edit_wan_settings') }}</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="interface-detail"><span class="interface-label">{{ __('location_details.connection_type') }}</span><span class="interface-value" id="wan-type-display">DHCP</span></div>
                                    </div>
                                    <div class="col-md-9 wan-static-ip-display_div hidden">
                                        <div class="row">
                                            <div class="col-md-3"><div class="interface-detail"><span class="interface-label">{{ __('location_details.ip_address') }}</span><span class="interface-value" id="wan-ip-display">-</span></div></div>
                                            <div class="col-md-3"><div class="interface-detail"><span class="interface-label">{{ __('location_details.subnet_mask') }}</span><span class="interface-value" id="wan-subnet-display">-</span></div></div>
                                            <div class="col-md-3"><div class="interface-detail"><span class="interface-label">{{ __('location_details.gateway') }}</span><span class="interface-value" id="wan-gateway-display">-</span></div></div>
                                            <div class="col-md-3"><div class="interface-detail"><span class="interface-label">{{ __('location_details.primary_dns') }}</span><span class="interface-value" id="wan-dns1-display">-</span></div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-9 wan-pppoe-display_div hidden">
                                        <div class="row">
                                            <div class="col-md-6"><div class="interface-detail"><span class="interface-label">{{ __('location_details.username') }}</span><span class="interface-value" id="wan-pppoe-username">-</span></div></div>
                                            <div class="col-md-6"><div class="interface-detail"><span class="interface-label">{{ __('location_details.service_name') }}</span><span class="interface-value" id="wan-pppoe-service-name">-</span></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Radio Settings -->
                            <div class="content-section">
                                <div class="section-header"><h5 class="section-title">{{ __('location_details.wifi_radio_channel') }}</h5></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="wifi-country">{{ __('location_details.country_region') }}</label>
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
                                            <label for="power-level-2g">{{ __('location_details.power_2g') }}</label>
                                            <select class="form-control" id="power-level-2g">
                                                <option value="20">Maximum (20 dBm)</option>
                                                <option value="17">High (17 dBm)</option>
                                                <option value="15" selected>Medium (15 dBm)</option>
                                                <option value="12">Low (12 dBm)</option>
                                                <option value="10">Minimum (10 dBm)</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="power-level-5g">{{ __('location_details.power_5g') }}</label>
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
                                            <label for="channel-width-2g">{{ __('location_details.width_2g') }}</label>
                                            <select class="form-control" id="channel-width-2g"><option value="20">20 MHz</option><option value="40" selected>40 MHz</option></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="channel-width-5g">{{ __('location_details.width_5g') }}</label>
                                            <select class="form-control" id="channel-width-5g"><option value="20">20 MHz</option><option value="40">40 MHz</option><option value="80" selected>80 MHz</option><option value="160">160 MHz</option></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="channel-2g">{{ __('location_details.channel_2g') }}</label>
                                            <select class="form-control" id="channel-2g">
                                                <option value="1">Ch 1 (2412)</option><option value="2">Ch 2</option><option value="3">Ch 3</option><option value="4">Ch 4</option><option value="5">Ch 5</option>
                                                <option value="6" selected>Ch 6 (2437)</option><option value="7">Ch 7</option><option value="8">Ch 8</option><option value="9">Ch 9</option><option value="10">Ch 10</option>
                                                <option value="11">Ch 11</option><option value="12">Ch 12</option><option value="13">Ch 13</option><option value="14">Ch 14 (2484)</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="channel-5g">{{ __('location_details.channel_5g') }}</label>
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
                                            <label class="mb-0">{{ __('location_details.channel_optimization') }}</label>
                                            <button class="btn btn-outline-primary btn-sm" id="scan-channels-btn"><i data-feather="wifi" class="mr-1"></i>{{ __('location_details.scan_button') }}</button>
                                        </div>
                                        <div class="alert alert-info mb-3" id="scan-status-alert">
                                            <div class="alert-body"><i data-feather="info" class="mr-2"></i><span id="scan-status-text">{{ __('location_details.scan_default_status') }}</span></div>
                                        </div>
                                        <div class="row text-center mb-3">
                                            <div class="col-6"><div class="stat-value text-primary" id="last-optimal-2g">--</div><div class="stat-label">{{ __('location_details.best_2g') }}</div></div>
                                            <div class="col-6"><div class="stat-value text-success" id="last-optimal-5g">--</div><div class="stat-label">{{ __('location_details.best_5g') }}</div></div>
                                        </div>
                                        <div class="text-center mb-2"><small class="text-muted" id="last-scan-timestamp">{{ __('location_details.no_scan_yet') }}</small></div>
                                        <button class="btn btn-success btn-block btn-sm" id="save-channels-btn" disabled><i data-feather="check" class="mr-1"></i>{{ __('location_details.apply_optimal') }}</button>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button class="btn custom-btn" id="save-radio-settings"><i data-feather="save" class="mr-2"></i>{{ __('location_details.save_all_radio') }}</button>
                                </div>
                            </div>

                            <!-- Traffic Prioritization (QoS) -->
                            <div class="content-section" id="qos-settings-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title">{{ __('location_details.qos_title') }}</h5>
                                    <button type="button" class="btn custom-btn btn-sm" id="save-qos-settings"><i data-feather="save" class="mr-1"></i>{{ __('location_details.save_qos') }}</button>
                                </div>

                                <div id="zone-qos-notice" class="alert alert-info py-2 px-3 mb-3" style="display:none;">
                                    <i data-feather="layers" class="mr-50" style="width:16px;height:16px;vertical-align:text-bottom;"></i>
                                    {{ __('location_details.qos_zone_notice') }}
                                </div>

                                <div class="loc-panel panel-qos mb-0">
                                    <div class="loc-panel-header">
                                        <span class="loc-panel-icon">
                                            <i data-feather="git-merge" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                                        </span>
                                        <h6 class="loc-panel-title">{{ __('location_details.qos_classification') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">
                                        <div class="row align-items-start">
                                            <div class="col-md-7">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="mb-0 font-weight-bold">{{ __('location_details.qos_enable') }}</span>
                                                    <div class="custom-control custom-switch custom-control-primary">
                                                        <input type="checkbox" class="custom-control-input" id="qos-enabled">
                                                        <label class="custom-control-label" for="qos-enabled"></label>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0 small">{{ __('location_details.qos_enable_help') }}</p>
                                            </div>
                                            <div class="col-md-5 mt-3 mt-md-0 qos-classify-preview-col">
                                                <div class="panel-sub-label mb-2">{{ __('location_details.qos_active_classes') }}</div>
                                                <div id="qos-classes-preview" class="pt-0">
                                                    <span class="text-muted small">{{ __('common.loading') }}</span>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">{{ __('location_details.qos_managed_globally') }}</small>
                                            </div>
                                        </div>

                                        <div class="panel-sub-section qos-bandwidth-subsection">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="loc-panel-icon mr-2 mb-0" style="width:28px;height:28px;">
                                                    <i data-feather="bar-chart-2" style="color:var(--mw-primary);width:14px;height:14px;"></i>
                                                </span>
                                                <span class="panel-sub-label mb-0" style="font-size:0.8rem;">{{ __('location_details.qos_bandwidth_limits') }}</span>
                                            </div>
                                            <p class="small text-muted mb-3 mb-md-2">{!! __('location_details.qos_bandwidth_intro') !!}</p>

                                            <div id="qos-wan-override-group" class="rounded border px-3 py-2 mb-3 bg-light" style="display:none;">
                                                <div class="custom-control custom-checkbox mb-0">
                                                    <input type="checkbox" class="custom-control-input" id="qos-wan-use-local">
                                                    <label class="custom-control-label" for="qos-wan-use-local">{{ __('location_details.qos_wan_use_local') }}</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">{{ __('location_details.qos_wan_use_local_help') }}</small>
                                            </div>

                                            <div class="row qos-bw-split-row align-items-start">
                                            <div class="col-12 col-md-6">
                                                <div class="panel-sub-label">{{ __('location_details.qos_wan_capacity') }}</div>
                                                <div class="mb-2">
                                                    <label class="small text-muted mb-1 d-block" for="qos-wan-down-kbps">{{ __('location_details.stat_download') }}</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-wan-input" id="qos-wan-down-kbps" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="small text-muted mb-1 d-block" for="qos-wan-up-kbps">{{ __('location_details.stat_upload') }}</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-wan-input" id="qos-wan-up-kbps" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 qos-bw-min-col">
                                                <div class="panel-sub-label">{{ __('location_details.qos_min_per_class') }}</div>
                                                <div class="mb-2">
                                                    <label class="small d-block mb-1" for="qos-voip-bw">{{ __('location_details.qos_voip') }} <span class="text-muted">(EF)</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-voip-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small d-block mb-1" for="qos-streaming-bw">{{ __('location_details.qos_streaming') }} <span class="text-muted">(AF41)</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-streaming-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small d-block mb-1" for="qos-be-bw">{{ __('location_details.qos_best_effort') }} <span class="text-muted">(BE)</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-be-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="small d-block mb-1" for="qos-bulk-bw">{{ __('location_details.qos_bulk') }} <span class="text-muted">(CS1)</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-bulk-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Web Filter -->
                            <div class="content-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title">{{ __('location_details.web_content_filtering') }}</h5>
                                    <button class="btn custom-btn" id="save-web-filter-settings"><i data-feather="save" class="mr-2"></i>{{ __('location_details.save_web_filter') }}</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="mb-0">{{ __('location_details.enable_content_filtering') }}</label>
                                                <div class="custom-control custom-switch custom-control-primary">
                                                    <input type="checkbox" class="custom-control-input" id="global-web-filter">
                                                    <label class="custom-control-label" for="global-web-filter"></label>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ __('location_details.web_filter_help') }}</small>
                                            <div id="web-filter-propagation-notice" class="alert alert-warning mt-2 mb-0 py-2 px-3" style="display:none; font-size:0.85rem;">
                                                <i data-feather="clock" style="width:14px;height:14px;vertical-align:middle;" class="mr-1"></i>
                                                {!! __('location_details.web_filter_propagation') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="global-filter-categories">{{ __('location_details.block_categories') }}</label>
                                            <select class="select2 form-control" id="global-filter-categories" multiple="multiple"></select>
                                            <small class="text-muted">{{ __('location_details.block_categories_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" id="wan-dns-row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label for="wan-dns1">{{ __('location_details.wan_primary_dns') }}</label>
                                            <input type="text" class="form-control" id="wan-dns1" placeholder="8.8.8.8">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label for="wan-dns2">{{ __('location_details.wan_secondary_dns') }} <small class="text-muted font-weight-normal">{{ __('location_details.description_optional') }}</small></label>
                                            <input type="text" class="form-control" id="wan-dns2" placeholder="8.8.4.4">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <small class="text-muted" id="wan-dns-hint">{{ __('location_details.wan_dns_hint') }}</small>
                                    </div>
                                </div>
                            </div>
                        <!-- end router content -->

</div><!-- /ld-panel-router -->

</div><!-- end .content-body -->

<!-- ============================================================
     MODALS
============================================================ -->

<!-- WAN Settings Modal -->
<div class="modal fade" id="wan-settings-modal" tabindex="-1" role="dialog" aria-labelledby="wan-settings-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wan-settings-modal-title">{{ __('location_details.modal_wan_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('location_details.connection_type') }}</label>
                    <select class="form-control" id="wan-connection-type">
                        <option value="DHCP">DHCP</option>
                        <option value="STATIC">{{ __('location_details.modal_wan_static_ip') }}</option>
                        <option value="PPPOE">PPPoE</option>
                    </select>
                </div>
                <div id="wan-static-fields" style="display:none;">
                    <div class="form-group"><label>{{ __('location_details.ip_address') }}</label><input type="text" class="form-control" id="wan-ip-address" placeholder="203.0.113.10"></div>
                    <div class="form-group"><label>{{ __('location_details.modal_wan_netmask') }}</label><input type="text" class="form-control" id="wan-netmask" placeholder="255.255.255.0"></div>
                    <div class="form-group"><label>{{ __('location_details.gateway') }}</label><input type="text" class="form-control" id="wan-gateway" placeholder="203.0.113.1"></div>
                    <div class="form-group"><label>{{ __('location_details.primary_dns') }}</label><input type="text" class="form-control" id="wan-primary-dns" placeholder="8.8.8.8"></div>
                    <div class="form-group"><label>{{ __('location_details.modal_wan_secondary_dns') }}</label><input type="text" class="form-control" id="wan-secondary-dns" placeholder="1.1.1.1"></div>
                </div>
                <div id="wan-pppoe-fields" style="display:none;">
                    <div class="form-group"><label>{{ __('location_details.username') }}</label><input type="text" class="form-control" id="wan-pppoe-username-modal" placeholder="{{ __('location_details.username') }}"></div>
                    <div class="form-group"><label>{{ __('location_details.modal_wan_password') }}</label><input type="password" class="form-control" id="wan-pppoe-password" placeholder="{{ __('location_details.modal_wan_password') }}"></div>
                    <div class="form-group"><label>{{ __('location_details.modal_wan_service_name_optional') }}</label><input type="text" class="form-control" id="wan-pppoe-service-name-modal" placeholder="{{ __('location_details.service_name') }}"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn custom-btn save-wan-settings">{{ __('location_details.modal_save_changes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Device Restart Modal -->
<div class="modal fade" id="restart-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i data-feather="refresh-cw" class="mr-2"></i>{{ __('location_details.modal_restart_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Tab switcher -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-restart-tab="now" href="#">
                            <i data-feather="zap" class="mr-1"></i>{{ __('location_details.modal_restart_tab_now') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-restart-tab="schedule" href="#">
                            <i data-feather="clock" class="mr-1"></i>{{ __('location_details.modal_restart_tab_schedule') }}
                        </a>
                    </li>
                </ul>

                <!-- Reboot Now tab -->
                <div id="reboot-now-section">
                    <div class="alert alert-warning mb-3"><div class="alert-body"><i data-feather="alert-triangle" class="mr-2"></i><strong>{{ __('location_details.modal_warning_label') }}</strong> {{ __('location_details.modal_restart_warning') }}</div></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar p-50 mr-3" style="background:rgba(99,102,241,0.12);"><div class="avatar-content"><i data-feather="hard-drive" class="font-medium-4"></i></div></div>
                        <div>
                            <h6 class="mb-0">{{ __('location_details.modal_device_info') }}</h6>
                            <p class="card-text text-muted mb-0">{{ __('location_details.modal_location_prefix') }} <span class="location_name font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">{{ __('location_details.modal_model_prefix') }} <span class="router_model font-weight-bold"></span></p>
                            <p class="card-text text-muted mb-0">{{ __('location_details.modal_mac_prefix') }} <span class="router_mac_address font-weight-bold"></span></p>
                        </div>
                    </div>
                    <p class="text-muted">{{ __('location_details.modal_restart_confirm') }}</p>
                </div>

                <!-- Schedule tab -->
                <div id="schedule-reboot-section" style="display:none;">
                    <div class="alert alert-info mb-3"><div class="alert-body"><i data-feather="info" class="mr-2"></i>{{ __('location_details.modal_schedule_info') }}</div></div>
                    <div class="form-group">
                        <label for="scheduled-reboot-time">{{ __('location_details.modal_reboot_datetime') }}</label>
                        <input type="datetime-local" class="form-control" id="scheduled-reboot-time">
                        <small class="text-muted">{{ __('location_details.modal_reboot_datetime_help') }}</small>
                    </div>
                    <div id="scheduled-reboot-current" class="text-muted mb-2" style="font-size:0.85rem; display:none;">
                        <i data-feather="clock" style="width:13px;height:13px;vertical-align:middle;"></i>
                        {{ __('location_details.modal_currently_scheduled') }} <strong id="scheduled-reboot-current-value"></strong>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-reboot-schedule-btn">
                        <i data-feather="x" class="mr-1"></i>{{ __('location_details.modal_clear_schedule') }}
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirm-restart-btn"><i data-feather="refresh-cw" class="mr-1"></i><span>{{ __('location_details.modal_restart_now_btn') }}</span></button>
                <button type="button" class="btn btn-primary" id="save-reboot-schedule-btn" style="display:none;"><i data-feather="save" class="mr-1"></i><span>{{ __('location_details.modal_save_schedule_btn') }}</span></button>
            </div>
        </div>
    </div>
</div>

<!-- Firmware Update Modal -->
<div class="modal fade" id="firmware-update-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i data-feather="download" class="mr-2"></i>{{ __('location_details.modal_firmware_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3"><div class="alert-body"><i data-feather="info" class="mr-2"></i><strong>{{ __('location_details.modal_important_label') }}</strong> {{ __('location_details.modal_firmware_info') }}</div></div>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar p-50 mr-3" style="background:rgba(99,102,241,0.12);"><div class="avatar-content"><i data-feather="hard-drive" class="font-medium-4"></i></div></div>
                    <div>
                        <h6 class="mb-0">{{ __('location_details.modal_current_device') }}</h6>
                        <p class="card-text text-muted mb-0">{{ __('location_details.modal_model_prefix') }} <span class="router_model font-weight-bold"></span></p>
                        <p class="card-text text-muted mb-0">{{ __('location_details.modal_firmware_prefix') }} <span class="router_firmware font-weight-bold"></span></p>
                        <p class="card-text text-muted mb-0">{{ __('location_details.mac_prefix') }} <span class="router_mac_address font-weight-bold"></span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="firmware-version-select">{{ __('location_details.modal_available_versions') }}</label>
                    <select class="form-control" id="firmware-version-select"><option value="">{{ __('location_details.modal_loading_firmware_versions') }}</option></select>
                </div>
                <div class="form-group">
                    <label>{{ __('location_details.description_label') }}</label>
                    <div class="card"><div class="card-body p-2"><div id="firmware-description"><p class="text-muted mb-0">{{ __('location_details.modal_firmware_desc_placeholder') }}</p></div></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn custom-btn" id="start-firmware-update-btn" disabled><i data-feather="download" class="mr-1"></i><span>{{ __('location_details.modal_update_firmware_btn') }}</span></button>
            </div>
        </div>
    </div>
</div>

<!-- Firmware Progress Modal -->
<div class="modal fade" id="firmware-progress-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i data-feather="download" class="mr-2"></i>{{ __('location_details.modal_firmware_progress_title') }}</h5></div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3"><div class="alert-body"><i data-feather="alert-triangle" class="mr-2"></i><strong>{{ __('location_details.modal_firmware_progress_warning') }}</strong></div></div>
                <div class="text-center mb-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">{{ __('common.loading') }}</span></div></div>
                <div class="progress progress-bar-primary mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="firmware-progress-bar"></div>
                </div>
                <div class="text-center">
                    <h6 id="firmware-progress-status">{{ __('location_details.modal_firmware_preparing') }}</h6>
                    <p class="text-muted mb-0" id="firmware-progress-description">{{ __('location_details.modal_firmware_taking_minutes') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Channel Scan Modal -->
<div class="modal fade" id="channel-scan-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--mw-primary);">
                <h5 class="modal-title" style="color:white;"><i data-feather="wifi" class="mr-2"></i>{{ __('location_details.modal_scan_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true" style="color:white;">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="scan-progress-view">
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="timeline">
                        <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-initiated-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>{{ __('location_details.modal_scan_step_1_title') }}</h6><span class="text-muted">{{ __('location_details.modal_scan_step_num', ['num' => 1]) }}</span></div><p>{{ __('location_details.modal_scan_step_1_desc') }}</p></div></div>
                        <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-started-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>{{ __('location_details.modal_scan_step_2_title') }}</h6><span class="text-muted">{{ __('location_details.modal_scan_step_num', ['num' => 2]) }}</span></div><p>{{ __('location_details.modal_scan_step_2_desc') }}</p></div></div>
                        <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-2g-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>{{ __('location_details.modal_scan_step_3_title') }}</h6><span class="text-muted">{{ __('location_details.modal_scan_step_num', ['num' => 3]) }}</span></div><p>{{ __('location_details.modal_scan_step_3_desc') }}</p></div></div>
                        <div class="timeline-item"><div class="timeline-point"><div class="timeline-point-indicator" id="step-5g-indicator"></div></div><div class="timeline-event"><div class="d-flex justify-content-between"><h6>{{ __('location_details.modal_scan_step_4_title') }}</h6><span class="text-muted">{{ __('location_details.modal_scan_step_num', ['num' => 4]) }}</span></div><p>{{ __('location_details.modal_scan_step_4_desc') }}</p></div></div>
                    </div>
                </div>
                <div id="scan-results-view" style="display: none;">
                    <div class="alert alert-success mb-2"><div class="alert-body"><i data-feather="check-circle" class="mr-1"></i><span>{{ __('location_details.modal_scan_complete') }}</span></div></div>
                    <div class="row mb-2">
                        <div class="col-md-6"><div class="card mb-0" style="background:rgba(99,102,241,0.08);"><div class="card-body"><h5 class="card-title">2.4 GHz</h5><div class="d-flex justify-content-between align-items-center"><span>{{ __('location_details.modal_scan_recommended') }}</span><h3 class="mb-0" id="result-channel-2g">6</h3></div></div></div></div>
                        <div class="col-md-6"><div class="card mb-0" style="background:rgba(99,102,241,0.08);"><div class="card-body"><h5 class="card-title">5 GHz</h5><div class="d-flex justify-content-between align-items-center"><span>{{ __('location_details.modal_scan_recommended') }}</span><h3 class="mb-0" id="result-channel-5g">36</h3></div></div></div></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="nearby-networks-table">
                            <thead><tr><th>{{ __('location_details.modal_scan_col_band') }}</th><th>{{ __('location_details.modal_scan_col_channel') }}</th><th>{{ __('location_details.modal_scan_col_ssid') }}</th><th>{{ __('location_details.modal_scan_col_signal') }}</th><th>{{ __('location_details.modal_scan_col_interference') }}</th><th>{{ __('location_details.modal_scan_col_status') }}</th></tr></thead>
                            <tbody id="nearby-networks-tbody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button class="btn custom-btn" id="apply-scan-results"><i data-feather="check" class="mr-1"></i> {{ __('location_details.modal_scan_apply_settings') }}</button>
                        <button class="btn btn-outline-primary" id="back-to-scan-btn"><i data-feather="refresh-cw" class="mr-1"></i> {{ __('location_details.modal_scan_again') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clone Location Modal -->
<div class="modal fade" id="clone-location-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i data-feather="copy" class="mr-2"></i>{{ __('location_details.modal_clone_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <div class="alert-body"><i data-feather="info" class="mr-2"></i>{{ __('location_details.modal_clone_info') }}</div>
                </div>
                <div id="clone-owner-group" style="display:none;">
                    <div class="form-group">
                        <label for="clone-owner-select">{{ __('location_details.modal_clone_assign_to_user') }}</label>
                        <select class="form-control" id="clone-owner-select">
                            <option value="">{{ __('location_details.modal_clone_assign_to_self') }}</option>
                        </select>
                        <small class="text-muted">{{ __('location_details.modal_clone_assign_help') }}</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn custom-btn" id="confirm-clone-btn">
                    <i data-feather="copy" class="mr-1"></i><span>{{ __('location_details.modal_clone_location_btn') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Device Assignment Modal -->
<div class="modal fade" id="mac-address-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i data-feather="hard-drive" class="mr-2"></i>{{ __('location_details.modal_assign_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3"><div class="alert-body"><i data-feather="info" class="mr-2"></i><strong>{{ __('location_details.modal_assign_note_label') }}</strong> {{ __('location_details.modal_assign_info') }}</div></div>
                <div class="form-group">
                    <label for="device-select">{{ __('location_details.modal_select_ap') }}</label>
                    <select class="form-control" id="device-select">
                        <option value="">{{ __('location_details.modal_loading_devices') }}</option>
                    </select>
                    <small class="text-muted">{{ __('location_details.modal_unassigned_first') }}</small>
                </div>
                <div class="form-group" id="device-mac-preview-group" style="display:none;">
                    <label>{{ __('location_details.mac_address') }}</label>
                    <div id="device-mac-preview-view" class="d-flex align-items-center">
                        <span id="device-mac-preview" class="form-control-plaintext bg-light p-2 rounded font-weight-bold mr-2" style="flex:1;">-</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="edit-device-mac-btn" title="{{ __('location_details.modal_edit_mac_title') }}" style="display:none;">
                            <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                        </button>
                    </div>
                    <div id="device-mac-edit-inline" style="display:none;">
                        <input type="text" class="form-control form-control-sm mb-1"
                               id="device-mac-input" placeholder="AA:BB:CC:DD:EE:FF" maxlength="17">
                        <div id="device-mac-input-error" class="invalid-feedback" style="display:none;"></div>
                        <div class="d-flex mt-1">
                            <button type="button" class="btn btn-sm btn-primary mr-1" id="save-device-mac-btn">{{ __('location_details.modal_save_btn') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-device-mac-btn">{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('location_details.modal_currently_assigned') }}</label>
                    <div class="form-control-plaintext bg-light p-2 rounded"><span id="current-mac-display">-</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn custom-btn" id="save-mac-address-btn" disabled><i data-feather="save" class="mr-1"></i><span>{{ __('location_details.modal_assign_device_btn') }}</span></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/vendors/js/charts/apexcharts.min.js"></script>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/assets/vendors/js/maps/leaflet.min.js"></script>
<script>
    window.APP_CONFIG_V5 = {
        apiBase: '{{ rtrim(config("app.url"), "/") }}/api'
    };
</script>
<script src="/assets/js/location-details.js?v={{ filemtime(public_path('assets/js/location-details.js')) }}"></script>
@endpush
