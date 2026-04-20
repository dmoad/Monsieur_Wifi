@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = $locale === 'fr' ? '/fr/boutique' : '/en/shop';
    $ordersBase = $locale === 'fr' ? '/fr/commandes' : '/en/orders';
    $ordersJsT = [
        'locale' => $locale,
        'date_locale' => $locale === 'fr' ? 'fr-FR' : 'en-US',
        'orders_base' => $ordersBase,
        'toast_login_required' => __('orders.js_toast_login_required'),
        'toast_load_failed' => __('orders.js_toast_load_failed'),
        'order_number_prefix' => __('orders.js_order_number_prefix'),
        'label_ordered' => __('orders.js_label_ordered'),
        'label_delivered_on' => __('orders.js_label_delivered_on'),
        'label_status' => __('orders.js_label_status'),
        'label_total' => __('orders.js_label_total'),
        'label_tax' => __('orders.js_label_tax'),
        'btn_view_details' => __('orders.js_btn_view_details'),
        'btn_invoice' => __('orders.js_btn_invoice'),
        'tracking_html' => __('orders.js_tracking_html'),
        'status_cancelled' => __('orders.js_status_cancelled'),
        'status_awaiting_payment' => __('orders.js_status_awaiting_payment'),
        'status_delivered' => __('orders.js_status_delivered'),
        'status_shipped' => __('orders.js_status_shipped'),
        'status_paid' => __('orders.js_status_paid'),
        'toast_session_expired' => __('orders.js_toast_session_expired'),
        'toast_invoice_failed' => __('orders.js_toast_invoice_failed'),
        'toast_invoice_downloaded' => __('orders.js_toast_invoice_downloaded'),
        'invoice_filename' => __('orders.js_invoice_filename'),
    ];
@endphp

@section('title', __('orders.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('orders.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('orders.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="{{ $shopUrl }}" class="btn btn-primary">
            <i data-feather="shopping-bag"></i> {{ __('orders.btn_continue_shopping') }}
        </a>
    </div>
</div>
<div class="content-body">
    <div id="orders-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    </div>
    <div id="orders-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="inbox" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>{{ __('orders.empty_title') }}</h4>
                <p>{{ __('orders.empty_subtitle') }}</p>
                <a href="{{ $shopUrl }}" class="btn btn-primary">{{ __('orders.btn_shop_now') }}</a>
            </div>
        </div>
    </div>
    <div id="orders-list"></div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.orders = @json($ordersJsT);
</script>
<script src="/assets/js/orders.js?v={{ time() }}"></script>
@endpush
