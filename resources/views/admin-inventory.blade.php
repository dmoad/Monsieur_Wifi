@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = "/{$locale}/shop";
    $adminInventoryJsT = [
        'locale' => $locale,
        'dashboard_url' => "/{$locale}/dashboard",

        'session_expired' => __('admin_inventory.js_session_expired'),
        'no_permission' => __('admin_inventory.js_no_permission'),
        'load_summary_failed' => __('admin_inventory.js_load_summary_failed'),
        'load_inventory_failed' => __('admin_inventory.js_load_inventory_failed'),
        'load_devices_failed' => __('admin_inventory.js_load_devices_failed'),
        'load_devices_failed_prefix' => __('admin_inventory.js_load_devices_failed_prefix'),

        'no_products' => __('admin_inventory.js_no_products'),
        'no_devices' => __('admin_inventory.js_no_devices'),

        'label_in_stock' => __('admin_inventory.js_label_in_stock'),
        'label_reserved' => __('admin_inventory.js_label_reserved'),
        'label_available' => __('admin_inventory.js_label_available'),
        'label_threshold' => __('admin_inventory.js_label_threshold'),

        'btn_add_view_devices' => __('admin_inventory.js_btn_add_view_devices'),
        'btn_add_view_devices_title' => __('admin_inventory.js_btn_add_view_devices_title'),

        'badge_out_of_stock' => __('admin_inventory.js_badge_out_of_stock'),
        'badge_low_stock' => __('admin_inventory.js_badge_low_stock'),
        'badge_in_stock' => __('admin_inventory.js_badge_in_stock'),

        'device_based_tracking' => __('admin_inventory.js_device_based_tracking'),
        'device_based_tracking_desc' => __('admin_inventory.js_device_based_tracking_desc'),
        'label_devices_in_stock' => __('admin_inventory.js_label_devices_in_stock'),
        'label_low_stock_threshold' => __('admin_inventory.js_label_low_stock_threshold'),
        'threshold_hint' => __('admin_inventory.js_threshold_hint'),
        'modify_stock_heading' => __('admin_inventory.js_modify_stock_heading'),
        'btn_add_manage_devices' => __('admin_inventory.js_btn_add_manage_devices'),
        'btn_save_threshold' => __('admin_inventory.js_btn_save_threshold'),
        'btn_cancel' => __('common.cancel'),

        'threshold_updated' => __('admin_inventory.js_threshold_updated'),
        'threshold_update_failed' => __('admin_inventory.js_threshold_update_failed'),
        'save_failed_prefix' => __('admin_inventory.js_save_failed_prefix'),

        'pagination_page' => __('admin_inventory.js_pagination_page'),
        'pagination_of' => __('admin_inventory.js_pagination_of'),
        'pagination_devices' => __('admin_inventory.js_pagination_devices'),
        'btn_previous' => __('admin_inventory.js_btn_previous'),
        'btn_next' => __('admin_inventory.js_btn_next'),

        'devices_modal_desc' => __('admin_inventory.js_devices_modal_desc'),
        'btn_add_device' => __('admin_inventory.js_btn_add_device'),
        'btn_import_csv' => __('admin_inventory.js_btn_import_csv'),

        'col_mac_address' => __('admin_inventory.js_col_mac_address'),
        'col_serial_number' => __('admin_inventory.js_col_serial_number'),
        'col_status' => __('admin_inventory.js_col_status'),
        'col_notes' => __('admin_inventory.js_col_notes'),
        'col_actions' => __('admin_inventory.js_col_actions'),

        'btn_close' => __('common.close'),
        'btn_edit' => __('admin_inventory.js_btn_edit'),
        'btn_delete' => __('admin_inventory.js_btn_delete'),

        'device_status_available' => __('admin_inventory.js_device_status_available'),
        'device_status_reserved' => __('admin_inventory.js_device_status_reserved'),
        'device_status_sold' => __('admin_inventory.js_device_status_sold'),
        'device_status_defective' => __('admin_inventory.js_device_status_defective'),

        'form_add_heading' => __('admin_inventory.js_form_add_heading'),
        'form_edit_heading' => __('admin_inventory.js_form_edit_heading'),
        'mac_formats_hint' => __('admin_inventory.js_mac_formats_hint'),
        'notes_placeholder' => __('admin_inventory.js_notes_placeholder'),
        'form_received_date' => __('admin_inventory.js_form_received_date'),
        'btn_add_device_submit' => __('admin_inventory.js_btn_add_device_submit'),
        'btn_update_device' => __('admin_inventory.js_btn_update_device'),

        'mac_serial_required' => __('admin_inventory.js_mac_serial_required'),
        'device_added' => __('admin_inventory.js_device_added'),
        'add_device_failed' => __('admin_inventory.js_add_device_failed'),
        'add_device_failed_prefix' => __('admin_inventory.js_add_device_failed_prefix'),
        'device_updated' => __('admin_inventory.js_device_updated'),
        'update_device_failed' => __('admin_inventory.js_update_device_failed'),
        'update_device_failed_prefix' => __('admin_inventory.js_update_device_failed_prefix'),
        'confirm_delete_device' => __('admin_inventory.js_confirm_delete_device'),
        'device_deleted' => __('admin_inventory.js_device_deleted'),
        'delete_device_failed' => __('admin_inventory.js_delete_device_failed'),
        'delete_device_failed_prefix' => __('admin_inventory.js_delete_device_failed_prefix'),

        'csv_upload_heading' => __('admin_inventory.js_csv_upload_heading'),
        'btn_download_template' => __('admin_inventory.js_btn_download_template'),
        'csv_format_label' => __('admin_inventory.js_csv_format_label'),
        'csv_format_desc' => __('admin_inventory.js_csv_format_desc'),
        'csv_col_mac_desc' => __('admin_inventory.js_csv_col_mac_desc'),
        'csv_col_serial_desc' => __('admin_inventory.js_csv_col_serial_desc'),
        'csv_col_notes_desc' => __('admin_inventory.js_csv_col_notes_desc'),
        'csv_example_label' => __('admin_inventory.js_csv_example_label'),
        'csv_mac_normalize_note' => __('admin_inventory.js_csv_mac_normalize_note'),
        'csv_select_label' => __('admin_inventory.js_csv_select_label'),
        'csv_max_size' => __('admin_inventory.js_csv_max_size'),
        'csv_skip_duplicates' => __('admin_inventory.js_csv_skip_duplicates'),
        'btn_upload_import' => __('admin_inventory.js_btn_upload_import'),

        'csv_select_file_error' => __('admin_inventory.js_csv_select_file_error'),
        'csv_too_large' => __('admin_inventory.js_csv_too_large'),
        'csv_invalid_file' => __('admin_inventory.js_csv_invalid_file'),
        'csv_uploading' => __('admin_inventory.js_csv_uploading'),
        'csv_processing' => __('admin_inventory.js_csv_processing'),
        'csv_import_failed' => __('admin_inventory.js_csv_import_failed'),
        'csv_upload_failed_prefix' => __('admin_inventory.js_csv_upload_failed_prefix'),

        'import_results_heading' => __('admin_inventory.js_import_results_heading'),
        'stat_imported' => __('admin_inventory.js_stat_imported'),
        'stat_duplicates' => __('admin_inventory.js_stat_duplicates'),
        'stat_errors' => __('admin_inventory.js_stat_errors'),
        'error_details' => __('admin_inventory.js_error_details'),
        'btn_back_to_list' => __('admin_inventory.js_btn_back_to_list'),

        'csv_template_downloaded' => __('admin_inventory.js_csv_template_downloaded'),
    ];
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
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.admin_inventory = @json($adminInventoryJsT);
</script>
<script src="/assets/js/admin-inventory.js?v={{ filemtime(public_path('assets/js/admin-inventory.js')) }}"></script>
@endpush
