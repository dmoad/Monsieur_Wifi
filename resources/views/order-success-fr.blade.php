@extends('layouts.app')

@section('title', 'Confirmation de Commande - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Confirmation de Commande</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item"><a href="/fr/commandes">Mes Commandes</a></li>
                        <li class="breadcrumb-item active">Confirmation</li>
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
                <span class="sr-only">Chargement...</span>
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
                        <h2 class="text-success">Commande Confirmée!</h2>
                        <p class="lead">Merci pour votre commande</p>
                        <h4 class="mb-4">Commande #<span id="order-number"></span></h4>
                        <p>Un email de confirmation a été envoyé à votre adresse email.</p>
                        <div class="mt-4">
                            <a href="/fr/commandes" class="btn btn-primary mr-2">Voir Mes Commandes</a>
                            <a href="/fr/boutique" class="btn btn-outline-primary">Continuer mes Achats</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informations de Commande</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Numéro de Commande:</strong></td>
                                <td id="info-order-number"></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td id="info-date"></td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
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
                        <h4 class="card-title">Adresse de Livraison</h4>
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
                        <h4 class="card-title">Articles de la Commande</h4>
                    </div>
                    <div class="card-body">
                        <div id="order-items"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Sous-total:</span>
                                    <span id="summary-subtotal"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Livraison:</span>
                                    <span id="summary-shipping"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Taxe:</span>
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
<script src="/assets/js/order-success-fr.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
