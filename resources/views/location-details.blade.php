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
<link rel="stylesheet" type="text/css" href="/assets/css/location-details.css?v={{ filemtime(public_path('assets/css/location-details.css')) }}">
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
    <button type="button" class="ld-tab" data-tab="networks">{{ __('location_details.tab_networks') }}</button>
</div>

<div class="content-body">

@include('location-details._overview')

@include('location-details._settings')

@include('location-details._router')

@include('location-details._networks')

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
                    <div class="form-group"><label>{{ __('location_details.ip_address') }}</label><input type="text" class="form-control" id="wan-ip-address" placeholder="192.168.1.10"></div>
                    <div class="form-group"><label>{{ __('location_details.modal_wan_netmask') }}</label><input type="text" class="form-control" id="wan-netmask" placeholder="255.255.255.0"></div>
                    <div class="form-group"><label>{{ __('location_details.gateway') }}</label><input type="text" class="form-control" id="wan-gateway" placeholder="192.168.1.1"></div>
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
                <button type="button" class="btn btn-primary save-wan-settings">{{ __('location_details.modal_save_changes') }}</button>
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
                <button type="button" class="btn btn-primary" id="start-firmware-update-btn" disabled><i data-feather="download" class="mr-1"></i><span>{{ __('location_details.modal_update_firmware_btn') }}</span></button>
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
                        <button class="btn btn-primary" id="apply-scan-results"><i data-feather="check" class="mr-1"></i> {{ __('location_details.modal_scan_apply_settings') }}</button>
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
                <button type="button" class="btn btn-primary" id="confirm-clone-btn">
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
                <button type="button" class="btn btn-primary" id="save-mac-address-btn" disabled><i data-feather="save" class="mr-1"></i><span>{{ __('location_details.modal_assign_device_btn') }}</span></button>
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
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.location_details = @json(__('location_details'));
    window.APP_I18N.common = @json(__('common'));
</script>
<script src="/assets/js/location-details.js?v={{ filemtime(public_path('assets/js/location-details.js')) }}"></script>
@endpush
