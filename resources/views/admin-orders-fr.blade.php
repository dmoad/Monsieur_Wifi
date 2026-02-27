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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
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

@push('styles')
<style>
/* Hero Header */
.order-modal-redesign .order-hero-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 0 0 1.5rem 0;
    padding: 1.5rem 1.5rem;
    color: white;
}

.order-modal-redesign {
    padding: 0 !important;
}

.order-modal-redesign > *:not(.order-hero-header) {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
}

.order-modal-redesign .order-content-grid {
    padding-bottom: 1.25rem;
}

.order-modal-redesign .order-number-badge {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.order-modal-redesign .order-date {
    opacity: 0.9;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.order-modal-redesign .order-status-large .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Quick Actions Toolbar */
.order-modal-redesign .quick-actions-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.order-modal-redesign .quick-actions-toolbar .btn {
    margin: 0;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.order-modal-redesign .quick-actions-toolbar .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.order-modal-redesign .quick-actions-toolbar .text-center {
    width: 100%;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Two Column Grid */
.order-modal-redesign .order-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Info Cards */
.order-modal-redesign .info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    gap: 0.75rem;
    transition: all 0.2s;
}

.order-modal-redesign .info-card:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.order-modal-redesign .info-card-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.order-modal-redesign .info-card-content {
    flex: 1;
}

.order-modal-redesign .info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.order-modal-redesign .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.125rem;
}

.order-modal-redesign .info-value-sm {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.125rem;
}

.order-modal-redesign .info-meta {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.5;
}

.order-modal-redesign .payment-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.order-modal-redesign .mini-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
}

.order-modal-redesign .mini-badge.badge-blue {
    background: #e7f3ff;
    color: #0066cc;
}

.order-modal-redesign .mini-badge.badge-green {
    background: #d4edda;
    color: #155724;
}

.order-modal-redesign .mini-badge.badge-yellow {
    background: #fff3cd;
    color: #856404;
}

.order-modal-redesign .mini-badge.badge-gray {
    background: #e9ecef;
    color: #495057;
}

.order-modal-redesign .tracking-number {
    background: #f8f9fa;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-family: monospace;
    font-size: 0.85rem;
    color: #495057;
    display: inline-block;
    margin-top: 0.25rem;
}

/* Summary Card */
.order-modal-redesign .summary-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.order-modal-redesign .summary-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-modal-redesign .items-list {
    padding: 1rem;
    border-bottom: 2px dashed #e9ecef;
}

.order-modal-redesign .item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.order-modal-redesign .item-row:not(:last-child) {
    border-bottom: 1px solid #f8f9fa;
}

.order-modal-redesign .item-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.order-modal-redesign .item-name {
    font-weight: 500;
    color: #2c3e50;
}

.order-modal-redesign .item-qty {
    font-size: 0.85rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
}

.order-modal-redesign .item-price {
    font-weight: 600;
    color: #2c3e50;
}

.order-modal-redesign .summary-breakdown {
    padding: 1rem;
    background: #f8f9fa;
}

.order-modal-redesign .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.375rem 0;
    font-size: 0.9rem;
    color: #495057;
}

.order-modal-redesign .summary-total {
    border-top: 2px solid #667eea;
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: #667eea;
}

/* Responsive */
@media (max-width: 768px) {
    .order-modal-redesign .order-content-grid {
        grid-template-columns: 1fr;
    }
}

#order-modal .modal-dialog {
    max-width: 900px;
}

#order-modal .modal-body {
    padding: 0;
}

#order-modal .modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.order-modal-redesign {
    padding: 1.25rem;
}

#order-modal .modal-header {
    background: transparent;
    border-bottom: none;
    padding: 0.5rem 1rem;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 1055;
    width: auto;
}

#order-modal .modal-header .modal-title {
    display: none;
}

#order-modal .modal-header .close {
    padding: 0.5rem;
    margin: 0;
    color: white;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    opacity: 0.9;
}

#order-modal .modal-header .close:hover {
    opacity: 1;
    color: white;
}

#order-modal .modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 0.75rem 1.25rem;
}
</style>
@endpush

@push('scripts')
<script src="/assets/js/admin-orders.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
