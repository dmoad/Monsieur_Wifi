@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = "/{$locale}/shop";
    $adminOrdersJsT = [
        'locale' => $locale,
        'date_locale' => $locale === 'fr' ? 'fr-FR' : 'en-US',
        'dashboard_url' => "/{$locale}/dashboard",
        'session_expired' => __('admin_orders.js_session_expired'),
        'no_permission' => __('admin_orders.js_no_permission'),
        'load_orders_failed' => __('admin_orders.js_load_orders_failed'),
        'no_orders' => __('admin_orders.js_no_orders'),
        'no_tracking' => __('admin_orders.js_no_tracking'),
        'btn_view' => __('admin_orders.js_btn_view'),
        'btn_tracking' => __('admin_orders.js_btn_tracking'),
        'load_details_failed' => __('admin_orders.js_load_details_failed'),
        'label_customer' => __('admin_orders.js_label_customer'),
        'label_payment' => __('admin_orders.js_label_payment'),
        'label_shipping_info' => __('admin_orders.js_label_shipping_info'),
        'label_tracking_info' => __('admin_orders.js_label_tracking_info'),
        'summary_title' => __('admin_orders.js_summary_title'),
        'summary_subtotal' => __('admin_orders.js_summary_subtotal'),
        'summary_shipping' => __('admin_orders.js_summary_shipping'),
        'summary_tax' => __('admin_orders.js_summary_tax'),
        'summary_total' => __('admin_orders.js_summary_total'),
        'badge_cancelled' => __('admin_orders.status_cancelled'),
        'badge_awaiting_payment' => __('admin_orders.js_badge_awaiting_payment'),
        'badge_delivered' => __('admin_orders.status_delivered'),
        'badge_shipped' => __('admin_orders.status_shipped'),
        'badge_paid' => __('admin_orders.js_badge_paid'),
        'btn_invoice' => __('admin_orders.js_btn_invoice'),
        'btn_update_inventory' => __('admin_orders.js_btn_update_inventory'),
        'btn_assign_inventory' => __('admin_orders.js_btn_assign_inventory'),
        'btn_update_tracking' => __('admin_orders.js_btn_update_tracking'),
        'btn_add_tracking' => __('admin_orders.js_btn_add_tracking'),
        'btn_confirm_payment' => __('admin_orders.js_btn_confirm_payment'),
        'btn_mark_shipped' => __('admin_orders.js_btn_mark_shipped'),
        'btn_mark_delivered' => __('admin_orders.js_btn_mark_delivered'),
        'btn_cancel_order' => __('admin_orders.js_btn_cancel_order'),
        'confirm_payment_received' => __('admin_orders.js_confirm_payment_received'),
        'confirm_payment_received_title' => __('admin_orders.js_confirm_payment_received_title'),
        'confirm_btn' => __('admin_orders.js_confirm_btn'),
        'confirm_cancel' => __('admin_orders.js_confirm_cancel'),
        'confirm_cancel_title' => __('admin_orders.js_confirm_cancel_title'),
        'cancel_order_btn' => __('admin_orders.js_cancel_order_btn'),
        'status_info_cancelled' => __('admin_orders.js_status_info_cancelled'),
        'status_info_completed' => __('admin_orders.js_status_info_completed'),
        'payment_stripe_confirmed' => __('admin_orders.js_payment_stripe_confirmed'),
        'payment_confirmed' => __('admin_orders.js_payment_confirmed'),
        'payment_confirm_failed' => __('admin_orders.js_payment_confirm_failed'),
        'tracking_add_title' => __('admin_orders.js_tracking_add_title'),
        'tracking_provider' => __('admin_orders.js_tracking_provider'),
        'tracking_select_provider' => __('admin_orders.js_tracking_select_provider'),
        'tracking_major_carriers' => __('admin_orders.js_tracking_major_carriers'),
        'tracking_other_providers' => __('admin_orders.js_tracking_other_providers'),
        'tracking_other_provider_name' => __('admin_orders.js_tracking_other_provider_name'),
        'tracking_enter_provider' => __('admin_orders.js_tracking_enter_provider'),
        'tracking_id' => __('admin_orders.js_tracking_id'),
        'tracking_enter_tracking' => __('admin_orders.js_tracking_enter_tracking'),
        'tracking_btn_cancel' => __('common.cancel'),
        'tracking_btn_save' => __('admin_orders.js_tracking_btn_save'),
        'tracking_error_select_provider' => __('admin_orders.js_tracking_error_select_provider'),
        'tracking_error_enter_provider' => __('admin_orders.js_tracking_error_enter_provider'),
        'tracking_error_enter_tracking' => __('admin_orders.js_tracking_error_enter_tracking'),
        'tracking_updated' => __('admin_orders.js_tracking_updated'),
        'tracking_update_failed' => __('admin_orders.js_tracking_update_failed'),
        'status_updated' => __('admin_orders.js_status_updated'),
        'status_update_failed' => __('admin_orders.js_status_update_failed'),
        'inv_instructions' => __('admin_orders.js_inv_instructions'),
        'inv_label_quantity' => __('admin_orders.js_inv_label_quantity'),
        'inv_label_available' => __('admin_orders.js_inv_label_available'),
        'inv_select_items' => __('admin_orders.js_inv_select_items'),
        'inv_click_to_select' => __('admin_orders.js_inv_click_to_select'),
        'inv_error_load_order' => __('admin_orders.js_inv_error_load_order'),
        'inv_error_select_exactly' => __('admin_orders.js_inv_error_select_exactly'),
        'inv_assigned_success' => __('admin_orders.js_inv_assigned_success'),
        'inv_error_assign' => __('admin_orders.js_inv_error_assign'),
        'invoice_download_failed' => __('admin_orders.js_invoice_download_failed'),
        'invoice_downloaded' => __('admin_orders.js_invoice_downloaded'),
        'invoice_filename' => __('admin_orders.js_invoice_filename'),
    ];
@endphp

@section('title', __('admin_orders.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('admin_orders.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('admin_orders.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('admin_orders.filter_orders') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="status-filter" class="form-control">
                        <option value="">{{ __('admin_orders.status_all') }}</option>
                        <option value="pending">{{ __('admin_orders.status_pending') }}</option>
                        <option value="processing">{{ __('admin_orders.status_processing') }}</option>
                        <option value="shipped">{{ __('admin_orders.status_shipped') }}</option>
                        <option value="delivered">{{ __('admin_orders.status_delivered') }}</option>
                        <option value="cancelled">{{ __('admin_orders.status_cancelled') }}</option>
                        <option value="payment_failed">{{ __('admin_orders.status_payment_failed') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="{{ __('admin_orders.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadOrders()">{{ __('admin_orders.btn_apply_filter') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="orders-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div id="orders-list"></div>

    <div id="order-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('common.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Assign Inventory Modal -->
<div class="modal fade" id="assign-inventory-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin_orders.modal_assign_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="assign-inventory-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="assignInventoryToOrder()">{{ __('admin_orders.btn_assign_devices') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Hero Header */
.order-modal-redesign .order-hero-header {
    background: var(--mw-primary);
    margin: 0 0 1.5rem 0;
    padding: 1.5rem 1.5rem;
    color: white;
}

.order-modal-redesign {
    padding: 0 !important;
}

.order-modal-redesign > *:not(.order-hero-header) {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
}

.order-modal-redesign .order-content-grid {
    padding-bottom: 1.25rem;
}

.order-modal-redesign .order-number-badge {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.order-modal-redesign .order-date {
    opacity: 0.9;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.order-modal-redesign .order-status-large .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Quick Actions Toolbar */
.order-modal-redesign .quick-actions-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.order-modal-redesign .quick-actions-toolbar .btn {
    margin: 0;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.order-modal-redesign .quick-actions-toolbar .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.order-modal-redesign .quick-actions-toolbar .text-center {
    width: 100%;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Two Column Grid */
.order-modal-redesign .order-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Info Cards */
.order-modal-redesign .info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    gap: 0.75rem;
    transition: all 0.2s;
}

.order-modal-redesign .info-card:hover {
    border-color: var(--mw-primary);
    box-shadow: 0 2px 8px rgba(99,102,241,0.1);
}

.order-modal-redesign .info-card-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background: var(--mw-primary);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.order-modal-redesign .info-card-content {
    flex: 1;
}

.order-modal-redesign .info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.order-modal-redesign .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.125rem;
}

.order-modal-redesign .info-value-sm {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.125rem;
}

.order-modal-redesign .info-meta {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.5;
}

.order-modal-redesign .payment-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.order-modal-redesign .mini-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
}

.order-modal-redesign .mini-badge.badge-blue {
    background: #e7f3ff;
    color: #0066cc;
}

.order-modal-redesign .mini-badge.badge-green {
    background: #d4edda;
    color: #155724;
}

.order-modal-redesign .mini-badge.badge-yellow {
    background: #fff3cd;
    color: #856404;
}

.order-modal-redesign .mini-badge.badge-gray {
    background: #e9ecef;
    color: #495057;
}

.order-modal-redesign .tracking-number {
    background: #f8f9fa;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-family: monospace;
    font-size: 0.85rem;
    color: #495057;
    display: inline-block;
    margin-top: 0.25rem;
}

/* Summary Card */
.order-modal-redesign .summary-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.order-modal-redesign .summary-header {
    background: var(--mw-primary);
    color: white;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-modal-redesign .items-list {
    padding: 1rem;
    border-bottom: 2px dashed #e9ecef;
}

.order-modal-redesign .item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.order-modal-redesign .item-row:not(:last-child) {
    border-bottom: 1px solid #f8f9fa;
}

.order-modal-redesign .item-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.order-modal-redesign .item-name {
    font-weight: 500;
    color: #2c3e50;
}

.order-modal-redesign .item-qty {
    font-size: 0.85rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
}

.order-modal-redesign .item-price {
    font-weight: 600;
    color: #2c3e50;
}

.order-modal-redesign .summary-breakdown {
    padding: 1rem;
    background: #f8f9fa;
}

.order-modal-redesign .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.375rem 0;
    font-size: 0.9rem;
    color: #495057;
}

.order-modal-redesign .summary-total {
    border-top: 2px solid var(--mw-primary);
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--mw-primary);
}

/* Responsive */
@media (max-width: 768px) {
    .order-modal-redesign .order-content-grid {
        grid-template-columns: 1fr;
    }
}

#order-modal .modal-dialog {
    max-width: 900px;
}

#order-modal .modal-body {
    padding: 0;
}

#order-modal .modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.order-modal-redesign {
    padding: 1.25rem;
}

#order-modal .modal-header {
    background: transparent;
    border-bottom: none;
    padding: 0.5rem 1rem;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 1055;
    width: auto;
}

#order-modal .modal-header .modal-title {
    display: none;
}

#order-modal .modal-header .close {
    padding: 0.5rem;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    opacity: 0.9;
}

#order-modal .modal-header .close:hover {
    opacity: 1;
}

#order-modal .modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 0.75rem 1.25rem;
}

/* Tracking Modal Styling */
.tracking-modal-redesign {
    padding: 0 !important;
}

.tracking-modal-redesign .tracking-hero-header {
    background: var(--mw-primary);
    color: white;
    padding: 1.5rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tracking-modal-redesign .tracking-hero-header h5 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tracking-modal-redesign .tracking-form-content {
    padding: 1.5rem;
}

.tracking-modal-redesign .form-label-modern {
    font-size: 0.875rem;
    font-weight: 600;
    color: #5e5873;
    margin-bottom: 0.5rem;
}

.tracking-modal-redesign .form-control-modern {
    border-radius: 6px;
    border: 1px solid #d8d6de;
    padding: 0.65rem 1rem;
    transition: all 0.2s;
}

.tracking-modal-redesign .form-control-modern:focus {
    border-color: var(--mw-primary);
    box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.15);
}

.tracking-modal-redesign .tracking-form-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.tracking-modal-redesign .tracking-form-actions .btn {
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
}
</style>
@endpush

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.admin_orders = @json($adminOrdersJsT);
</script>
<script src="/assets/js/admin-orders.js?v={{ filemtime(public_path('assets/js/admin-orders.js')) }}"></script>
@endpush
