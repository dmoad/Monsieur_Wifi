@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = "/{$locale}/shop";
    $ordersUrl = "/{$locale}/orders";
    $orderSuccessJsT = [
        'locale' => $locale,
        'date_locale' => $locale === 'fr' ? 'fr-FR' : 'en-US',
        'orders_url' => $ordersUrl,
        'btn_view_orders' => __('order_success.btn_view_orders'),
        'toast_login_required' => __('order_success.js_toast_login_required'),
        'error_not_found' => __('order_success.js_error_not_found'),
        'status_pending' => __('order_success.js_status_pending'),
        'status_processing' => __('order_success.js_status_processing'),
        'status_completed' => __('order_success.js_status_completed'),
        'status_shipped' => __('order_success.js_status_shipped'),
        'status_delivered' => __('order_success.js_status_delivered'),
        'status_cancelled' => __('order_success.js_status_cancelled'),
        'status_payment_failed' => __('order_success.js_status_payment_failed'),
    ];
@endphp

@section('title', __('order_success.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('order_success.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $ordersUrl }}">{{ __('orders.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('order_success.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div id="order-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    </div>
    <div id="order-details" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i data-feather="check-circle" style="width: 80px; height: 80px; color: #28c76f;"></i>
                        </div>
                        <h2 class="text-success">{{ __('order_success.confirmed_title') }}</h2>
                        <p class="lead">{{ __('order_success.thank_you') }}</p>
                        <h4 class="mb-4">{{ __('order_success.order_number_prefix') }}<span id="order-number"></span></h4>
                        <p>{{ __('order_success.email_sent_notice') }}</p>
                        <div class="mt-4">
                            <a href="{{ $ordersUrl }}" class="btn btn-primary mr-2">{{ __('order_success.btn_view_orders') }}</a>
                            <a href="{{ $shopUrl }}" class="btn btn-outline-primary">{{ __('order_success.btn_continue_shopping') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('order_success.info_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>{{ __('order_success.label_order_number') }}</strong></td>
                                <td id="info-order-number"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('order_success.label_date') }}</strong></td>
                                <td id="info-date"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('order_success.label_status') }}</strong></td>
                                <td id="info-status"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('order_success.label_total') }}</strong></td>
                                <td id="info-total"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('order_success.shipping_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <div id="shipping-address"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('order_success.items_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <div id="order-items"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ __('order_success.label_subtotal') }}</span>
                                    <span id="summary-subtotal"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ __('order_success.label_shipping') }}</span>
                                    <span id="summary-shipping"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ __('order_success.label_tax') }}</span>
                                    <span id="summary-tax"></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>{{ __('order_success.label_total') }}</strong>
                                    <strong class="text-primary" id="summary-total"></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.order_success = @json($orderSuccessJsT);
</script>
<script src="/assets/js/order-success.js?v={{ time() }}"></script>
@endpush
