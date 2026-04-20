@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = $locale === 'fr' ? '/fr/boutique' : '/en/shop';
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
<script src="/assets/js/orders{{ $locale === 'fr' ? '-fr' : '' }}.js?v={{ time() }}"></script>
@endpush
