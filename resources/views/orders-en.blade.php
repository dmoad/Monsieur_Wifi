@extends('layouts.app')

@section('title', 'My Orders - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">My Orders</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                        <li class="breadcrumb-item active">My Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="/en/shop" class="btn btn-primary">
            <i data-feather="shopping-bag"></i> Continue Shopping
        </a>
    </div>
</div>
<div class="content-body">
    <div id="orders-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div id="orders-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="inbox" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>No orders yet</h4>
                <p>Start shopping to see your orders here!</p>
                <a href="/en/shop" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
    </div>
    <div id="orders-list"></div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/orders.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
