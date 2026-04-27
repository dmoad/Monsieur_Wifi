@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('zones.page_title'))

@push('styles')
<link rel="stylesheet" href="/assets/vendors/css/forms/select/select2.min.css">
<style>
    /* Zone card layout */
    .zone-card { margin-bottom: var(--mw-space-md); }

    .zc-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: var(--mw-space-lg) var(--mw-space-xl);
        gap: var(--mw-space-lg);
    }
    .zc-info { flex: 1; min-width: 0; }
    .zc-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--mw-text-primary);
        margin-bottom: 4px;
    }
    .zc-meta {
        display: flex;
        align-items: center;
        gap: var(--mw-space-lg);
        flex-wrap: wrap;
    }
    .zc-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: var(--mw-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 340px;
    }
    .zc-meta-item [data-feather] { width: 13px !important; height: 13px !important; }

    /* Inline row actions */
    .zc-row-actions {
        display: inline-flex;
        gap: 4px;
        flex-shrink: 0;
        align-self: center;
    }
    .zc-action-btn {
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
    .zc-action-btn:hover {
        background: var(--mw-primary-tint);
        border-color: var(--mw-primary);
        color: var(--mw-primary);
    }
    .zc-action-danger:hover { background: rgba(220,38,38,0.06); border-color: var(--mw-danger); color: var(--mw-danger); }

    /* Stat row */
    .zc-stats {
        display: flex;
        align-items: stretch;
        border-top: 1px solid var(--mw-border-light);
    }
    .zc-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: var(--mw-space-md) var(--mw-space-lg);
        gap: 3px;
    }
    .zc-stat-divider {
        width: 1px;
        background: var(--mw-border-light);
        flex-shrink: 0;
    }
    .zc-stat-val {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
        letter-spacing: -0.3px;
    }
    .zc-stat-val [data-feather] { width: 17px !important; height: 17px !important; }
    .zc-stat-lbl { font-size: 11px; color: var(--mw-text-muted); }
    .zc-p { color: var(--mw-primary); }
    .zc-i { color: var(--mw-info); }

    .admin-alert {
        display: flex;
        align-items: flex-start;
        gap: var(--mw-space-sm);
        padding: var(--mw-space-md) var(--mw-space-lg);
        background: var(--mw-primary-tint);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-left: 3px solid var(--mw-primary);
        border-radius: var(--mw-radius-md);
        color: var(--mw-text-secondary);
        font-size: 13px;
        margin-bottom: var(--mw-space-lg);
    }
    .admin-alert [data-feather] {
        color: var(--mw-primary);
        flex-shrink: 0;
        width: 15px !important;
        height: 15px !important;
        margin-top: 1px;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, var(--mw-primary) 0%, #4F46E5 100%);
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
    
    <div class="per-page-selector d-flex align-items-center mb-3" style="gap: 8px;">
        <label for="items-per-page" class="mb-0 text-muted" style="font-size: 13px;">{{ __('zones.items_per_page') }}</label>
        <select id="items-per-page" class="form-control form-control-sm" style="width: auto;" onchange="changeItemsPerPage()">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
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
                            <span style="font-size:0.7rem;font-weight:600;background:var(--mw-primary);color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">{{ __('zones.admin_badge') }}</span>
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
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/assets/js/zones.js?v={{ filemtime(public_path('assets/js/zones.js')) }}"></script>
@endpush

