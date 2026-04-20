@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = $locale === 'fr' ? '/fr/boutique' : '/en/shop';
@endphp

@section('title', __('product.page_title'))

@push('styles')
<style>
    .product-main-image {
        width: 100%;
        height: 450px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .thumbnail:hover, .thumbnail.active {
        border-color: #7367f0;
        transform: scale(1.05);
    }
    .quantity-input {
        max-width: 120px;
    }
    .product-actions-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .product-actions-row .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .product-actions-row .btn svg {
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('product.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('product.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div id="product-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    </div>
    <div id="product-details" class="row" style="display: none;">
        <div class="col-lg-6">
            <img id="main-image" src="" alt="{{ __('product.alt_product_image') }}" class="product-main-image mb-3">
            <div id="thumbnails" class="d-flex gap-2"></div>
        </div>
        <div class="col-lg-6">
            <h2 id="product-name" class="mb-2"></h2>
            <h3 class="text-primary mb-3" id="product-price"></h3>
            <div id="stock-status" class="mb-3"></div>
            <div id="product-description" class="mb-4"></div>
            <div class="mb-4">
                <label for="quantity" class="form-label"><strong>{{ __('product.label_quantity') }}</strong></label>
                <input type="number" id="quantity" class="form-control quantity-input" value="1" min="1">
            </div>
            <div class="product-actions-row">
                <button id="add-to-cart-btn" class="btn btn-primary btn-lg">
                    <i data-feather="shopping-cart"></i> {{ __('product.btn_add_to_cart') }}
                </button>
                <a href="{{ $shopUrl }}" class="btn btn-outline-secondary btn-lg">
                    <i data-feather="arrow-left"></i> {{ __('product.btn_back_to_shop') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/product{{ $locale === 'fr' ? '-fr' : '' }}.js?v={{ time() + 2 }}"></script>
@endpush
