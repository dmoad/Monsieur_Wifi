@extends('layouts.app')

@section('title', 'Order Confirmation - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Order Confirmation</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                        <li class="breadcrumb-item"><a href="/en/orders">My Orders</a></li>
                        <li class="breadcrumb-item active">Order Confirmation</li>
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
                <span class="sr-only">Loading...</span>
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
                        <h2 class="text-success">Order Confirmed!</h2>
                        <p class="lead">Thank you for your order</p>
                        <h4 class="mb-4">Order #<span id="order-number"></span></h4>
                        <p>A confirmation email has been sent to your email address.</p>
                        <div class="mt-4">
                            <a href="/en/orders" class="btn btn-primary mr-2">View My Orders</a>
                            <a href="/en/shop" class="btn btn-outline-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Information</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td id="info-order-number"></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td id="info-date"></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="info-status"></td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td id="info-total"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Shipping Address</h4>
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
                        <h4 class="card-title">Order Items</h4>
                    </div>
                    <div class="card-body">
                        <div id="order-items"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal:</span>
                                    <span id="summary-subtotal"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Shipping:</span>
                                    <span id="summary-shipping"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tax:</span>
                                    <span id="summary-tax"></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
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
<script src="/assets/js/order-success.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
