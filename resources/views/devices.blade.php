@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('devices.page_title'))

@push('styles')
<style>
    .dc-filters {
        display: flex;
        gap: var(--mw-space-md);
        flex-wrap: wrap;
        padding: var(--mw-space-lg) var(--mw-space-xl);
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-lg);
        border: 1px solid var(--mw-border);
        box-shadow: var(--mw-shadow-card);
        margin-bottom: var(--mw-space-lg);
    }
    .dc-filters input,
    .dc-filters select { flex: 1; min-width: 160px; }

    /* Table inside card */
    .dc-table-wrap { overflow: hidden; }
    .dc-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dc-table thead th {
        padding: 10px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--mw-text-muted);
        background: var(--mw-bg-muted);
        border-bottom: 1px solid var(--mw-border);
        white-space: nowrap;
    }
    .dc-table tbody tr {
        border-bottom: 1px solid var(--mw-border-light);
        transition: background 0.1s;
    }
    .dc-table tbody tr:last-child { border-bottom: none; }
    .dc-table tbody tr:hover { background: var(--mw-bg-hover); }
    .dc-table td { padding: 11px 16px; vertical-align: middle; }
    .dc-serial { font-weight: 700; color: var(--mw-text-primary); }
    .dc-mac { font-family: 'SF Mono','Fira Code',monospace; font-size: 12px; color: var(--mw-text-muted); }
    .dc-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 600; padding: 2px 8px;
        border-radius: var(--mw-radius-badge); white-space: nowrap;
    }
    .dc-badge-assigned   { background: rgba(22,163,74,0.1);   color: var(--mw-success); }
    .dc-badge-unassigned { background: rgba(234,139,9,0.1);   color: var(--mw-warning); }
    .dc-badge-owner      { background: var(--mw-primary-tint); color: var(--mw-primary); }
    .dc-badge-no-owner   { background: var(--mw-bg-muted);    color: var(--mw-text-muted); }
    .dc-actions { display: flex; gap: var(--mw-space-sm); align-items: center; }
    .dc-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: var(--mw-radius-sm);
        font-size: 12px; font-weight: 500; cursor: pointer;
        border: 1px solid var(--mw-border); background: var(--mw-bg-surface);
        color: var(--mw-text-secondary); text-decoration: none !important;
        transition: background 0.1s, color 0.1s;
    }
    .dc-btn:hover { background: var(--mw-bg-hover); color: var(--mw-text-primary); }
    .dc-btn [data-feather] { width: 11px !important; height: 11px !important; }

    .empty-state { text-align: center; padding: 3rem 2rem; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('devices.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('devices.heading') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="dc-filters">
        <input type="text" id="search" class="form-control" placeholder="{{ __('devices.search_placeholder') }}">
        <select id="location-status-filter" class="form-control">
            <option value="">{{ __('devices.filter_all') }}</option>
            <option value="unassigned">{{ __('devices.filter_unassigned') }}</option>
            <option value="assigned">{{ __('devices.filter_assigned') }}</option>
        </select>
        <button class="btn btn-primary" onclick="loadDevices()">
            <i data-feather="search"></i> {{ __('devices.search_btn') }}
        </button>
    </div>

    <div id="devices-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('common.loading') }}</span>
        </div>
    </div>

    <div id="devices-list"></div>
    <div id="pagination-container"></div>
</div>

<!-- Change Owner Modal -->
<div class="modal fade" id="change-owner-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('devices.modal_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="device-id">
                <div class="form-group">
                    <label for="new-owner">{{ __('devices.new_owner_label') }}</label>
                    <select id="new-owner" class="form-control">
                        <option value="">{{ __('devices.select_owner_option') }}</option>
                    </select>
                </div>
                <div id="device-info" class="alert alert-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="updateDeviceOwner()">{{ __('devices.update_btn') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/devices.js?v={{ filemtime(public_path('assets/js/devices.js')) }}"></script>
@endpush
