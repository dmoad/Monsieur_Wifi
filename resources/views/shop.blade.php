@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopT = [
        'locale' => $locale,
        'cart_url' => "/{$locale}/cart",
        'product_url_base' => "/{$locale}/shop",
        'error_loading_products' => __('shop.js_error_loading_products'),
        'no_products' => __('shop.js_no_products'),
        'badge_out_of_stock' => __('shop.js_badge_out_of_stock'),
        'badge_low_stock' => __('shop.js_badge_low_stock'),
        'badge_in_stock' => __('shop.js_badge_in_stock'),
        'qty_in_cart' => __('shop.js_qty_in_cart'),
        'default_description' => __('shop.js_default_description'),
        'title_decrease_qty' => __('shop.js_title_decrease_qty'),
        'title_max_reached' => __('shop.js_title_max_reached'),
        'title_increase_qty' => __('shop.js_title_increase_qty'),
        'title_add_to_cart' => __('shop.js_title_add_to_cart'),
        'toast_login_required' => __('shop.js_toast_login_required'),
        'toast_added_html' => __('shop.js_toast_added_html'),
        'toast_add_failed' => __('shop.js_toast_add_failed'),
        'toast_cart_updated' => __('shop.js_toast_cart_updated'),
        'toast_update_failed' => __('shop.js_toast_update_failed'),
        'toast_item_removed' => __('shop.js_toast_item_removed'),
        'toast_remove_failed' => __('shop.js_toast_remove_failed'),
    ];
@endphp

@section('title', __('shop.page_title'))

@push('styles')
<style>
    .product-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }
    .product-card-link:hover {
        text-decoration: none;
        color: inherit;
    }
    .product-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(115, 103, 240, 0.2);
    }
    .product-image-wrapper {
        position: relative;
        width: 100%;
        height: 240px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        overflow: hidden;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    .product-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    .product-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #7367f0;
        margin: 0;
    }
    .product-actions {
        display: flex;
        gap: 0.5rem;
    }
    .product-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .product-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.3);
    }
    .product-btn svg {
        display: block;
    }
    .out-of-stock {
        opacity: 0.7;
    }
    .out-of-stock .product-image-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.7);
    }
    .stock-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .badge-success {
        background: linear-gradient(135deg, #28c76f 0%, #1e9f59 100%);
        color: white;
    }
    .badge-warning {
        background: linear-gradient(135deg, #ff9f43 0%, #ff6b35 100%);
        color: white;
    }
    .badge-danger {
        background: linear-gradient(135deg, #ea5455 0%, #c72a2b 100%);
        color: white;
    }
    .cart-qty-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        z-index: 10;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 20px;
        background: linear-gradient(135deg, #7367f0 0%, #5e50ee 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(115, 103, 240, 0.3);
    }
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.25rem;
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .qty-btn:hover:not(:disabled) {
        background: #7367f0;
        border-color: #7367f0;
        color: white;
        transform: scale(1.05);
    }
    .qty-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .qty-display {
        min-width: 32px;
        text-align: center;
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('shop.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('shop.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div id="products-grid" class="row match-height">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    </div>
    <div id="pagination" class="row mt-3"></div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.shop = @json($shopT);
</script>
<script src="/assets/js/shop.js?v={{ time() }}"></script>
@endpush
