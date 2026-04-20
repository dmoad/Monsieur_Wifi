@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = "/{$locale}/shop";
@endphp

@section('title', __('admin_inventory.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('admin_inventory.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('admin_inventory.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="/{{ $locale }}/admin/models" class="btn btn-outline-primary">
            <i data-feather="cpu"></i> {{ __('admin_inventory.btn_manage_models') }}
        </a>
    </div>
</div>
<div class="content-body">
    <!-- Summary Cards -->
    <div class="row" id="summary-cards">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('admin_inventory.summary_total_products') }}</h6>
                    <h3 class="mb-0" id="total-products">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('admin_inventory.summary_out_of_stock') }}</h6>
                    <h3 class="mb-0 text-danger" id="out-of-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('admin_inventory.summary_low_stock') }}</h6>
                    <h3 class="mb-0 text-warning" id="low-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('admin_inventory.summary_total_value') }}</h6>
                    <h3 class="mb-0 text-success" id="total-value">-</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ __('admin_inventory.section_heading') }}</h4>
            <div class="text-muted">
                <small><i data-feather="info" style="width: 14px; height: 14px;"></i> {!! __('admin_inventory.info_hint') !!}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="stock-status-filter" class="form-control">
                        <option value="">{{ __('admin_inventory.stock_filter_all') }}</option>
                        <option value="in_stock">{{ __('admin_inventory.stock_in_stock') }}</option>
                        <option value="low">{{ __('admin_inventory.stock_low') }}</option>
                        <option value="out">{{ __('admin_inventory.stock_out') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="{{ __('admin_inventory.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadInventory()">{{ __('admin_inventory.btn_apply_filter') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="inventory-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div id="inventory-list"></div>

    <div id="inventory-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin_inventory.modal_update_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="modal-content"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* CSV Import Results */
.csv-import-results .result-stat {
    padding: 0.75rem 0;
}
.csv-import-results .stat-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}
.csv-import-results .stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
.csv-import-results .card-body ul li {
    padding: 0.5rem;
    background: #fff3cd;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}
.csv-import-results .card-body ul li:last-child {
    margin-bottom: 0;
}

/* Inventory Settings Modal */
.inventory-settings-modal .stat-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s;
}
.inventory-settings-modal .stat-card:hover {
    border-color: #7367f0;
    box-shadow: 0 2px 8px rgba(115, 103, 240, 0.1);
}
.inventory-settings-modal .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.inventory-settings-modal .stat-icon.bg-success {
    background: linear-gradient(135deg, #28c76f 0%, #1e9f59 100%);
}
.inventory-settings-modal .stat-icon.bg-warning {
    background: linear-gradient(135deg, #ff9f43 0%, #ff6b35 100%);
}
.inventory-settings-modal .stat-content {
    flex: 1;
}
.inventory-settings-modal .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}
.inventory-settings-modal .stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script src="/assets/js/admin-inventory.js?v=<?php echo time(); ?>"></script>
@endpush
