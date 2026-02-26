@extends('layouts.app')

@section('title', 'Commander - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Commander</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item"><a href="/fr/panier">Panier</a></li>
                        <li class="breadcrumb-item active">Commander</li>
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
                    <h4 class="card-title">Informations de Livraison</h4>
                </div>
                <div class="card-body">
                    <form id="checkout-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_first_name">Prénom *</label>
                                    <input type="text" class="form-control" id="shipping_first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_last_name">Nom *</label>
                                    <input type="text" class="form-control" id="shipping_last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_company">Entreprise</label>
                            <input type="text" class="form-control" id="shipping_company">
                        </div>
                        <div class="form-group">
                            <label for="shipping_address_line1">Adresse Ligne 1 *</label>
                            <input type="text" class="form-control" id="shipping_address_line1" required>
                        </div>
                        <div class="form-group">
                            <label for="shipping_address_line2">Adresse Ligne 2</label>
                            <input type="text" class="form-control" id="shipping_address_line2">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_city">Ville *</label>
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
                                    <label for="shipping_postal_code">Code Postal *</label>
                                    <input type="text" class="form-control" id="shipping_postal_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_country">Pays *</label>
                                    <input type="text" class="form-control" id="shipping_country" value="Canada" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_phone">Téléphone *</label>
                            <input type="tel" class="form-control" id="shipping_phone" required>
                        </div>
                        
                        <hr class="my-3">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="same_as_shipping" checked>
                                <label class="custom-control-label" for="same_as_shipping">Adresse de facturation identique à la livraison</label>
                            </div>
                        </div>
                        
                        <div id="billing-section" style="display: none;">
                            <h5 class="mt-3">Informations de Facturation</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_first_name">Prénom *</label>
                                        <input type="text" class="form-control" id="billing_first_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_last_name">Nom *</label>
                                        <input type="text" class="form-control" id="billing_last_name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_company">Entreprise</label>
                                <input type="text" class="form-control" id="billing_company">
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line1">Adresse Ligne 1 *</label>
                                <input type="text" class="form-control" id="billing_address_line1">
                            </div>
                            <div class="form-group">
                                <label for="billing_address_line2">Adresse Ligne 2</label>
                                <input type="text" class="form-control" id="billing_address_line2">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_city">Ville *</label>
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
                                        <label for="billing_postal_code">Code Postal *</label>
                                        <input type="text" class="form-control" id="billing_postal_code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_country">Pays *</label>
                                        <input type="text" class="form-control" id="billing_country" value="Canada">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_phone">Téléphone *</label>
                                <input type="tel" class="form-control" id="billing_phone">
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h5>Méthode de Livraison</h5>
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
                    <h4 class="card-title">Résumé de la Commande</h4>
                </div>
                <div class="card-body">
                    <div id="order-items"></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Sous-total:</span>
                        <span id="order-subtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Livraison:</span>
                        <span id="order-shipping">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taxe:</span>
                        <span id="order-tax">$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary" id="order-total">$0.00</strong>
                    </div>
                    <button type="submit" form="checkout-form" class="btn btn-primary btn-block" id="place-order-btn">
                        Passer la Commande
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/checkout-fr.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
