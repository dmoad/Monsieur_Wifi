@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = $locale === 'fr' ? '/fr/boutique' : '/en/shop';
    $checkoutUrl = $locale === 'fr' ? '/fr/commander' : '/en/checkout';
@endphp

@section('title', __('cart.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('cart.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('cart.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="{{ $shopUrl }}" class="btn btn-outline-primary">
            <i data-feather="arrow-left"></i> {{ __('cart.btn_continue_shopping') }}
        </a>
    </div>
</div>
<div class="content-body">
    <div id="cart-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    </div>
    <div id="cart-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="shopping-cart" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>{{ __('cart.empty_title') }}</h4>
                <p>{{ __('cart.empty_subtitle') }}</p>
                <a href="{{ $shopUrl }}" class="btn btn-primary">{{ __('cart.btn_shop_now') }}</a>
            </div>
        </div>
    </div>
    <div id="cart-content" class="row" style="display: none;">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div id="cart-items"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('cart.order_summary') }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('cart.subtotal') }}</span>
                        <strong id="cart-subtotal">$0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>{{ __('cart.total') }}</strong>
                        <strong class="text-primary" id="cart-total">$0.00</strong>
                    </div>
                    <a href="{{ $checkoutUrl }}" class="btn btn-primary btn-block">{{ __('cart.btn_checkout') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/cart{{ $locale === 'fr' ? '-fr' : '' }}.js?v={{ time() }}"></script>
@endpush
