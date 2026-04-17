@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('zone_details.page_title'))

@push('styles')
<link rel="stylesheet" href="/app-assets/vendors/css/forms/select/select2.min.css">
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
    .admin-alert {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .location-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    .location-card:hover {
        border-color: #7367f0;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.15);
    }
    .location-card.primary {
        border: 2px solid #7367f0;
        background: linear-gradient(135deg, rgba(115, 103, 240, 0.05) 0%, rgba(115, 103, 240, 0.02) 100%);
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
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .location-address {
        color: #6c757d;
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
    .add-location-section {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
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
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
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
        border-top: 5px solid #6e6b7b;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
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
    <div class="content-header-right col-md-3 col-12 text-right d-flex justify-content-end gap-2">
        <button class="btn btn-outline-primary" onclick="editZone()">
            <i data-feather="edit"></i> {{ __('zone_details.edit_zone') }}
        </button>
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
        <div id="admin-alert-container"></div>
        
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
                <div id="primary-location-info" class="mb-3"></div>
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
                            <span style="font-size:0.7rem;font-weight:600;background:#7367f0;color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">{{ __('zones.admin_badge') }}</span>
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
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script>
    const ZONE_ID = {{ $zone }};
</script>
<script src="/assets/js/zone-details.js?v=<?php echo time(); ?>"></script>
@endpush
