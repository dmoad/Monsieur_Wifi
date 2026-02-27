@extends('layouts.app')

@section('title', 'Checkout - Monsieur WiFi')

@push('styles')
<style>
    .payment-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6);
    }
    .payment-modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    .payment-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }
    .payment-modal-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        line-height: 1;
    }
    .payment-modal-close:hover,
    .payment-modal-close:focus {
        color: #000;
    }
    #card-element {
        border: 1px solid #d8d6de;
        border-radius: 4px;
        padding: 12px;
        margin-bottom: 1rem;
        background: white;
    }
    #card-errors {
        color: #ea5455;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        min-height: 20px;
    }
    .payment-summary {
        background: #f8f8f8;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    .payment-summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .payment-summary-row.total {
        font-weight: 600;
        font-size: 1.1rem;
        color: #7367f0;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #d8d6de;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Checkout</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="/en/shop">Shop</a></li>
                        <li class="breadcrumb-item"><a href="/en/cart">Cart</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Shipping Information</h4>
                </div>
                <div class="card-body">
                    <form id="checkout-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_first_name">First Name *</label>
                                    <input type="text" class="form-control" id="shipping_first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_last_name">Last Name *</label>
                                    <input type="text" class="form-control" id="shipping_last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_company">Company</label>
                            <input type="text" class="form-control" id="shipping_company">
                        </div>
                        <div class="form-group">
                            <label for="shipping_address_line1">Address Line 1 *</label>
                            <input type="text" class="form-control" id="shipping_address_line1" required>
                        </div>
                        <div class="form-group">
                            <label for="shipping_address_line2">Address Line 2</label>
                            <input type="text" class="form-control" id="shipping_address_line2">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_city">City *</label>
                                    <input type="text" class="form-control" id="shipping_city" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_province">Province *</label>
                                    <input type="text" class="form-control" id="shipping_province" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_postal_code">Postal Code *</label>
                                    <input type="text" class="form-control" id="shipping_postal_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_country">Country *</label>
                                    <input type="text" class="form-control" id="shipping_country" value="Canada" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_phone">Phone *</label>
                            <input type="tel" class="form-control" id="shipping_phone" required>
                        </div>
                        
                        <hr class="my-3">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="same_as_shipping" checked>
                                <label class="custom-control-label" for="same_as_shipping">Billing address same as shipping</label>
                            </div>
                        </div>
                        
                        <div id="billing-section" style="display: none;">
                            <h5 class="mt-3">Billing Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_first_name">First Name *</label>
                                        <input type="text" class="form-control" id="billing_first_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_last_name">Last Name *</label>
                                        <input type="text" class="form-control" id="billing_last_name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_company">Company</label>
                                <input type="text" class="form-control" id="billing_company">
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line1">Address Line 1 *</label>
                                <input type="text" class="form-control" id="billing_address_line1">
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line2">Address Line 2</label>
                                <input type="text" class="form-control" id="billing_address_line2">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_city">City *</label>
                                        <input type="text" class="form-control" id="billing_city">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_province">Province *</label>
                                        <input type="text" class="form-control" id="billing_province">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_postal_code">Postal Code *</label>
                                        <input type="text" class="form-control" id="billing_postal_code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_country">Country *</label>
                                        <input type="text" class="form-control" id="billing_country" value="Canada">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_phone">Phone *</label>
                                <input type="tel" class="form-control" id="billing_phone">
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h5>Shipping Method</h5>
                        <div id="shipping-methods-loading">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                        <div id="shipping-methods"></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Summary</h4>
                </div>
                <div class="card-body">
                    <div id="order-items"></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal:</span>
                        <span id="order-subtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Shipping:</span>
                        <span id="order-shipping">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tax:</span>
                        <span id="order-tax">$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary" id="order-total">$0.00</strong>
                    </div>
                    <button type="submit" form="checkout-form" class="btn btn-primary btn-block" id="place-order-btn">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stripe Payment Modal -->
    <div id="payment-modal" class="payment-modal">
        <div class="payment-modal-content">
            <div class="payment-modal-header">
                <h4 class="mb-0">Complete Payment</h4>
                <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
            </div>
            
            <div class="payment-summary">
                <div class="payment-summary-row">
                    <span>Order Number:</span>
                    <strong id="payment-order-number"></strong>
                </div>
                <div class="payment-summary-row total">
                    <span>Total Amount:</span>
                    <strong id="payment-total-amount"></strong>
                </div>
            </div>
            
            <form id="payment-form">
                <div class="form-group">
                    <label for="card-element">Credit or Debit Card</label>
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" id="submit-payment-btn">
                    Pay Now
                </button>
            </form>
            
            <div id="payment-processing" style="display: none; text-align: center; padding: 2rem;">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p>Processing your payment...</p>
                <p class="text-muted small">Please do not close this window</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="/assets/js/checkout.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'en';
@endphp
