@extends('layouts.app')

@section('title', 'Détails du Produit - Monsieur WiFi')

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
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Détails du Produit</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Produit</li>
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
                <span class="sr-only">Chargement...</span>
            </div>
        </div>
    </div>
    <div id="product-details" class="row" style="display: none;">
        <div class="col-lg-6">
            <img id="main-image" src="" alt="Image du Produit" class="product-main-image mb-3">
            <div id="thumbnails" class="d-flex gap-2"></div>
        </div>
        <div class="col-lg-6">
            <h2 id="product-name" class="mb-2"></h2>
            <h3 class="text-primary mb-3" id="product-price"></h3>
            <div id="stock-status" class="mb-3"></div>
            <div id="product-description" class="mb-4"></div>
            <div class="mb-4">
                <label for="quantity" class="form-label"><strong>Quantité:</strong></label>
                <input type="number" id="quantity" class="form-control quantity-input" value="1" min="1">
            </div>
            <div class="d-flex gap-2">
                <button id="add-to-cart-btn" class="btn btn-primary btn-lg">
                    <i data-feather="shopping-cart"></i> Ajouter au Panier
                </button>
                <a href="/fr/boutique" class="btn btn-outline-secondary btn-lg">
                    <i data-feather="arrow-left"></i> Retour à la Boutique
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/product-fr.js?v=<?php echo time() + 2; ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
