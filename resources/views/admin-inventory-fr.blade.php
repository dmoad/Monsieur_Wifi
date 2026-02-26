@extends('layouts.app')

@section('title', 'Gérer l\'inventaire - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Gérer l'inventaire</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Gérer l'inventaire</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <a href="/fr/admin/modeles" class="btn btn-outline-primary">
            <i data-feather="cpu"></i> Gérer les Modèles
        </a>
    </div>
</div>
<div class="content-body">
    <!-- Summary Cards -->
    <div class="row" id="summary-cards">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total des Produits</h6>
                    <h3 class="mb-0" id="total-products">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">En Rupture de Stock</h6>
                    <h3 class="mb-0 text-danger" id="out-of-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Stock Faible</h6>
                    <h3 class="mb-0 text-warning" id="low-stock">-</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Valeur Totale</h6>
                    <h3 class="mb-0 text-success" id="total-value">-</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filtrer les Produits</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="stock-status-filter" class="form-control">
                        <option value="">Tous les Statuts de Stock</option>
                        <option value="in_stock">En Stock</option>
                        <option value="low">Stock Faible</option>
                        <option value="out">En Rupture de Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="Rechercher des produits...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadInventory()">Appliquer le Filtre</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="inventory-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    
    <div id="inventory-list"></div>
    
    <div id="inventory-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mettre à jour l'inventaire</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="modal-content"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/admin-inventory.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
