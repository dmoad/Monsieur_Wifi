@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('zones.page_title'))

@push('styles')
<link rel="stylesheet" href="/app-assets/vendors/css/forms/select/select2.min.css">
<style>
    .zone-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        transition: all 0.2s ease;
        margin-bottom: 0.75rem;
        background: white;
    }
    .zone-card:hover {
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.15);
    }
    .zone-row {
        padding: 0.875rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .zone-info {
        flex: 1;
        min-width: 0;
    }
    .zone-name {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .zone-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .zone-description {
        color: #6c757d;
        font-size: 0.85rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }
    .zone-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .zone-stat svg {
        width: 14px;
        height: 14px;
    }
    .zone-owner {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .zone-actions {
        display: flex;
        gap: 0.35rem;
        flex-shrink: 0;
    }
    .zone-actions .btn {
        padding: 0.375rem 0.5rem;
    }
    .admin-alert {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .pagination-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .pagination-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .per-page-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .per-page-selector label {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .per-page-selector select {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d8d6de;
        border-radius: 4px;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('zones.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('zones.heading') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <button class="btn btn-primary" onclick="showZoneModal()">
            <i data-feather="plus"></i> {{ __('zones.create_zone') }}
        </button>
    </div>
</div>

<div class="content-body">
    <div id="admin-alert-container"></div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="per-page-selector">
                <label for="items-per-page">{{ __('zones.items_per_page') }}</label>
                <select id="items-per-page" class="form-control" onchange="changeItemsPerPage()">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
    
    <div id="zones-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('common.loading') }}</span>
        </div>
    </div>
    
    <div id="zones-list"></div>
    
    <div id="pagination-container"></div>
</div>

<!-- Zone Modal -->
<div class="modal fade" id="zone-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zone-modal-title">{{ __('zones.create_zone') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="primary-location-info-edit" class="mb-3"></div>
                <form id="zone-form">
                    <input type="hidden" id="zone-id">
                    <div class="form-group" id="zone-owner-select-group" style="display: none;">
                        <label for="zone-owner-select">{{ __('zones.owner') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="zone-owner-select">
                            <option value="">{{ __('zones.loading_users') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('zones.select_owner_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="zone-name">{{ __('zones.zone_name') }} *</label>
                        <input type="text" class="form-control" id="zone-name" required placeholder="{{ __('zones.zone_name_placeholder') }}">
                    </div>
                    <div class="form-group">
                        <label for="zone-description">{{ __('zones.description') }}</label>
                        <textarea class="form-control" id="zone-description" rows="3" placeholder="{{ __('zones.description_placeholder') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="d-flex align-items-center mb-1">
                            <label class="mb-0 font-weight-bold mr-2" for="zone-roaming-enabled">{{ __('zones.roaming') }}</label>
                            <div class="custom-control custom-switch custom-control-primary">
                                <input type="checkbox" class="custom-control-input" id="zone-roaming-enabled" name="zone-roaming-enabled" checked>
                                <label class="custom-control-label" for="zone-roaming-enabled"></label>
                            </div>
                        </div>
                        <p class="text-muted mb-0 small">{{ __('zones.roaming_help') }}</p>
                    </div>
                    <div class="form-group" id="zone-shared-users-group" style="display:none;">
                        <label for="zone-shared-users">
                            {{ __('zones.shared_access') }}
                            <span style="font-size:0.7rem;font-weight:600;background:#7367f0;color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">{{ __('zones.admin_badge') }}</span>
                        </label>
                        <select class="select2 form-control" id="zone-shared-users" multiple="multiple"></select>
                        <small class="form-text text-muted">{{ __('zones.shared_users_help') }}</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveZone()">{{ __('zones.save_zone') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/assets/js/zones.js?v=<?php echo time(); ?>"></script>
@endpush

