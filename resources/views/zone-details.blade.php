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

    /* Primary-location subsection nested inside .zone-info-card.
       Reuses the .primary-loc-* typography classes but without their own
       card chrome — flush inside the zone info card, separated by a rule. */
    .zone-info-primary {
        display: flex;
        align-items: flex-start;
        gap: var(--mw-space-md);
        margin-top: var(--mw-space-lg);
        padding-top: var(--mw-space-lg);
        border-top: 1px solid var(--mw-border-light);
    }
    .zone-info-primary .primary-loc-inherit {
        background: var(--mw-bg-muted);
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
        border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-lg);
        padding: var(--mw-space-lg);
        margin-bottom: var(--mw-space-md);
        transition: box-shadow 0.15s, border-color 0.15s;
        background: var(--mw-bg-surface);
    }
    .location-card:hover {
        border-color: var(--mw-primary);
        box-shadow: var(--mw-shadow-elevated);
    }
    .location-card.primary {
        border: 2px solid var(--mw-primary);
        background: var(--mw-primary-tint);
    }
    .location-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .location-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--mw-text-primary);
        margin-bottom: 0.25rem;
    }
    .location-address {
        color: var(--mw-text-muted);
        font-size: 0.9rem;
    }
    .location-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    .location-actions {
        display: flex;
        gap: 0.5rem;
    }
    /* Kebab menu on location cards (Set as Primary / Remove from Zone) */
    .lz-kebab-wrap { position: relative; flex-shrink: 0; }
    .lz-kebab-btn {
        width: 32px;
        height: 32px;
        border: 1px solid var(--mw-border);
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mw-text-secondary);
        cursor: pointer;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
        padding: 0;
    }
    .lz-kebab-btn:hover {
        background: var(--mw-primary-tint);
        border-color: var(--mw-primary);
        color: var(--mw-primary);
    }
    .lz-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        right: 0;
        background: var(--mw-bg-surface);
        border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-md);
        box-shadow: var(--mw-shadow-elevated);
        min-width: 180px;
        z-index: 100;
        padding: 4px 0;
    }
    .lz-menu.open { display: block; }
    .lz-menu-item {
        display: flex;
        align-items: center;
        gap: var(--mw-space-sm);
        width: 100%;
        padding: 7px 14px;
        border: none;
        background: transparent;
        font-size: 13px;
        color: var(--mw-text-secondary);
        cursor: pointer;
        text-align: left;
    }
    .lz-menu-item:hover { background: var(--mw-bg-hover); color: var(--mw-text-primary); }
    .lz-menu-item [data-feather] { width: 14px !important; height: 14px !important; }
    .lz-menu-danger { color: var(--mw-danger) !important; }
    .lz-menu-danger:hover { background: rgba(220, 38, 38, 0.06) !important; }
    .lz-menu-divider { height: 1px; background: var(--mw-border-light); margin: 3px 0; }
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
        <div id="zone-info-container"></div>

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
