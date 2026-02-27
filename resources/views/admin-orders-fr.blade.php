@extends('layouts.app')

@section('title', 'Gérer les Commandes - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Gérer les Commandes</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Gérer les Commandes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filtrer les Commandes</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="status-filter" class="form-control">
                        <option value="">Tous les Statuts</option>
                        <option value="pending">En attente</option>
                        <option value="processing">En traitement</option>
                        <option value="shipped">Expédiée</option>
                        <option value="delivered">Livrée</option>
                        <option value="cancelled">Annulée</option>
                        <option value="payment_failed">Paiement échoué</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="Rechercher un numéro de commande...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadOrders()">Appliquer le Filtre</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="orders-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    
    <div id="orders-list"></div>
    
    <div id="order-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la Commande</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="modal-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Assign Inventory Modal -->
<div class="modal fade" id="assign-inventory-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner l'Inventaire à la Commande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="assign-inventory-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="assignInventoryToOrder()">Assigner et Créer les Appareils</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="/assets/js/admin-orders.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
