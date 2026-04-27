@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('zone_details.page_title'))

@push('styles')
<link rel="stylesheet" href="/assets/vendors/css/forms/select/select2.min.css">
<style>
    .zone-info-card {
        background: var(--mw-bg-surface);
        border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-lg);
        box-shadow: var(--mw-shadow-card);
        padding: var(--mw-space-xl);
        margin-top: var(--mw-space-lg);
        margin-bottom: var(--mw-space-lg);
    }
    .zone-info-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: var(--mw-space-lg);
    }
    .zone-info-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--mw-text-primary);
        margin-bottom: 4px;
    }
    .zone-info-description {
        color: var(--mw-text-muted);
        font-size: 13px;
        margin-bottom: 0;
    }
    .zone-info-meta {
        display: flex;
        gap: var(--mw-space-xl);
        flex-wrap: wrap;
        margin-top: var(--mw-space-lg);
        padding-top: var(--mw-space-lg);
        border-top: 1px solid var(--mw-border-light);
        align-items: center;
    }
    .zone-info-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--mw-text-secondary);
    }
    .zone-info-item [data-feather] { width: 14px !important; height: 14px !important; color: var(--mw-text-muted); }

    /* Zone Settings card — dedicated card in the Information tab that
       surfaces the primary-location pointer and Manage Settings CTA.
       Separate card so the user isn't confused about what Manage
       Settings applies to. */
    .zone-settings-card .zone-settings-desc {
        color: var(--mw-text-secondary);
        font-size: 13px;
        margin: 0 0 var(--mw-space-lg);
        line-height: 1.5;
    }
    .zone-settings-primary {
        display: flex;
        align-items: center;
        gap: var(--mw-space-md);
        padding: var(--mw-space-md) var(--mw-space-lg);
        background: var(--mw-primary-tint);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: var(--mw-radius-md);
    }
    .zone-settings-primary .primary-loc-body { flex: 1; min-width: 0; }
    .zone-settings-primary .btn {
        flex-shrink: 0;
    }
    .admin-alert {
        display: flex;
        align-items: flex-start;
        gap: var(--mw-space-sm);
        padding: var(--mw-space-md) var(--mw-space-lg);
        background: rgba(234,139,9,0.08);
        border: 1px solid rgba(234,139,9,0.2);
        border-left: 3px solid var(--mw-warning);
        border-radius: var(--mw-radius-md);
        color: var(--mw-text-secondary);
        font-size: 13px;
        margin-bottom: var(--mw-space-lg);
    }

    .location-card {
        display: flex;
        align-items: center;
        gap: var(--mw-space-md);
        border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-md);
        padding: var(--mw-space-md) var(--mw-space-lg);
        margin-bottom: var(--mw-space-sm);
        background: var(--mw-bg-surface);
        transition: box-shadow 0.15s, border-color 0.15s;
    }
    .location-card:hover {
        border-color: var(--mw-primary);
        box-shadow: var(--mw-shadow-elevated);
    }
    .location-card.primary {
        border: 2px solid var(--mw-primary);
    }
    .lc-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--mw-radius-md);
        background: var(--mw-bg-muted);
        color: var(--mw-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .lc-icon [data-feather] { width: 20px !important; height: 20px !important; }
    .location-card.primary .lc-icon {
        background: var(--mw-primary);
        color: #fff;
    }
    .lc-body { flex: 1; min-width: 0; }
    .lc-name-row {
        display: flex;
        align-items: center;
        gap: var(--mw-space-sm);
        flex-wrap: wrap;
    }
    .location-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--mw-text-primary);
    }
    .lc-badge-primary {
        font-size: 11px;
        font-weight: 600;
        background: var(--mw-primary-tint);
        color: var(--mw-primary);
        border-radius: var(--mw-radius-full);
        padding: 3px 10px 3px 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        line-height: 1.2;
    }
    .lc-badge-primary::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--mw-primary);
        flex-shrink: 0;
    }
    .location-address {
        color: var(--mw-text-muted);
        font-size: 12px;
        margin-top: 2px;
    }
    /* Inline row actions (zone header + location cards) */
    .lz-row-actions {
        display: inline-flex;
        gap: 4px;
        flex-shrink: 0;
    }
    .lz-action-btn {
        width: 32px;
        height: 32px;
        border: 1px solid var(--mw-border);
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--mw-text-secondary);
        cursor: pointer;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
        padding: 0;
    }
    .lz-action-btn:hover {
        background: var(--mw-primary-tint);
        border-color: var(--mw-primary);
        color: var(--mw-primary);
    }
    .lz-action-btn svg,
    .lz-action-btn [data-feather] { width: 14px !important; height: 14px !important; }
    .lz-action-danger:hover { background: rgba(220, 38, 38, 0.06); border-color: var(--mw-danger); color: var(--mw-danger); }

    .add-location-section {
        background: var(--mw-bg-muted);
        border: 2px dashed var(--mw-border);
        border-radius: var(--mw-radius-lg);
        padding: 2rem;
        text-align: center;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: var(--mw-bg-muted);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mw-text-muted);
    }
    /* Owner dropdown: hide the broken Select2 arrow and use a clean CSS chevron instead */
    #edit-zone-owner ~ .select2-container .select2-selection__arrow b,
    #s2id_edit-zone-owner .select2-selection__arrow b {
        display: none !important;
    }
    #edit-zone-owner ~ .select2-container .select2-selection__arrow,
    .select2-container[aria-owns] .select2-selection__arrow {
        display: none !important;
    }
    #edit-zone-owner-group .select2-container {
        position: relative;
    }
    #edit-zone-owner-group .select2-container .select2-selection--single {
        position: relative;
        padding-right: 2rem;
    }
    #edit-zone-owner-group .select2-container .select2-selection--single::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid var(--mw-text-muted);
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('zone_details.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/zones">{{ __('zones.heading') }}</a></li>
                        <li class="breadcrumb-item active" id="zone-breadcrumb">{{ __('common.loading') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <div id="zone-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('common.loading') }}</span>
        </div>
    </div>
    
    <div id="zone-content" style="display: none;">
        <div class="mw-tabs" role="tablist">
            <button class="mw-tab active" data-tab="info" role="tab">{{ __('zone_details.tab_information') }}</button>
            <button class="mw-tab" data-tab="locations" role="tab">{{ __('zone_details.tab_locations') }}</button>
        </div>

        <div class="mw-panel active" id="zd-panel-info" role="tabpanel">
            <div id="zone-info-container"></div>
            <div id="zone-settings-container"></div>
        </div>

        <div class="mw-panel" id="zd-panel-locations" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('zone_details.locations_in_zone') }}</h4>
                </div>
                <div class="card-body">
                    <div id="locations-list"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('zone_details.add_location_to_zone') }}</h4>
                </div>
                <div class="card-body">
                    <div id="available-locations-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Zone Modal -->
<div class="modal fade" id="edit-zone-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('zone_details.edit_zone') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-zone-form">
                    <div class="form-group">
                        <label for="edit-zone-name">{{ __('zones.zone_name') }} *</label>
                        <input type="text" class="form-control" id="edit-zone-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-zone-description">Description</label>
                        <textarea class="form-control" id="edit-zone-description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="d-flex align-items-center mb-1">
                            <label class="mb-0 font-weight-bold mr-2" for="edit-zone-roaming-enabled">{{ __('zones.roaming') }}</label>
                            <div class="custom-control custom-switch custom-control-primary">
                                <input type="checkbox" class="custom-control-input" id="edit-zone-roaming-enabled">
                                <label class="custom-control-label" for="edit-zone-roaming-enabled"></label>
                            </div>
                        </div>
                        <p class="text-muted mb-0 small">{{ __('zones.roaming_help') }}</p>
                    </div>
                    <div class="form-group" id="edit-zone-owner-group" style="display:none;">
                        <label for="edit-zone-owner">
                            {{ __('zones.owner') }}
                            <span style="font-size:0.7rem;font-weight:600;background:#ea5455;color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">{{ __('zones.admin_badge') }}</span>
                        </label>
                        <select class="select2" id="edit-zone-owner" style="width:100%"></select>
                        <small class="form-text text-muted">{{ __('zone_details.change_owner_help') }}</small>
                    </div>
                    <div class="form-group" id="edit-zone-shared-users-group" style="display:none;">
                        <label for="edit-zone-shared-users">
                            {{ __('zones.shared_access') }}
                            <span style="font-size:0.7rem;font-weight:600;background:var(--mw-primary);color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">{{ __('zones.admin_badge') }}</span>
                        </label>
                        <select class="select2 form-control" id="edit-zone-shared-users" multiple="multiple"></select>
                        <small class="form-text text-muted">{{ __('zones.shared_users_help') }}</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="updateZoneInfo()">{{ __('zone_details.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
<script>
    const ZONE_ID = {{ $zone }};
</script>
<script src="/assets/js/zone-details.js?v={{ filemtime(public_path('assets/js/zone-details.js')) }}"></script>
@endpush
