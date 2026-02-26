@extends('layouts.app')

@section('title', 'Shopping Cart - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Shopping Cart</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                        <li class="breadcrumb-item active">Cart</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12">
        <a href="/en/shop" class="btn btn-outline-primary">
            <i data-feather="arrow-left"></i> Continue Shopping
        </a>
    </div>
</div>
<div class="content-body">
    <div id="cart-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div id="cart-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="shopping-cart" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>Your cart is empty</h4>
                <p>Add some products to get started!</p>
                <a href="/en/shop" class="btn btn-primary">Shop Now</a>
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
                    <h4 class="card-title">Order Summary</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="cart-subtotal">$0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary" id="cart-total">$0.00</strong>
                    </div>
                    <a href="/en/checkout" class="btn btn-primary btn-block">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/cart.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
