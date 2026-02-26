@extends('layouts.app')

@section('title', 'Panier - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Panier</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Panier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12">
        <a href="/fr/boutique" class="btn btn-outline-primary">
            <i data-feather="arrow-left"></i> Continuer mes Achats
        </a>
    </div>
</div>
<div class="content-body">
    <div id="cart-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
        </div>
    </div>
    <div id="cart-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="shopping-cart" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>Votre panier est vide</h4>
                <p>Ajoutez des produits pour commencer!</p>
                <a href="/fr/boutique" class="btn btn-primary">Acheter Maintenant</a>
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
                    <h4 class="card-title">Résumé de la Commande</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total:</span>
                        <strong id="cart-subtotal">$0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary" id="cart-total">$0.00</strong>
                    </div>
                    <a href="/fr/commander" class="btn btn-primary btn-block">Passer la Commande</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/cart-fr.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
