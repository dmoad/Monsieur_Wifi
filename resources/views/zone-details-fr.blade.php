@extends('layouts.app')

@section('title', 'Détails de la Zone - Monsieur WiFi')

@push('styles')
<link rel="stylesheet" href="/app-assets/vendors/css/forms/select/select2.min.css">
<style>
    .zone-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .zone-info-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .zone-info-description {
        opacity: 0.9;
        margin-bottom: 1rem;
    }
    .zone-info-meta {
        display: flex;
        gap: 2rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    .zone-info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .admin-alert {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .location-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    .location-card:hover {
        border-color: #7367f0;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.15);
    }
    .location-card.primary {
        border: 2px solid #7367f0;
        background: linear-gradient(135deg, rgba(115, 103, 240, 0.05) 0%, rgba(115, 103, 240, 0.02) 100%);
    }
    .location-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .location-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .location-address {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .location-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    .location-actions {
        display: flex;
        gap: 0.5rem;
    }
    .add-location-section {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Détails de la Zone</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/zones">Zones</a></li>
                        <li class="breadcrumb-item active" id="zone-breadcrumb">Chargement...</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right d-flex justify-content-end gap-2">
        <button class="btn btn-outline-primary" onclick="editZone()">
            <i data-feather="edit"></i> Modifier la Zone
        </button>
    </div>
</div>

<div class="content-body">
    <div id="zone-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div>
    
    <div id="zone-content" style="display: none;">
        <div id="zone-info-container"></div>
        <div id="admin-alert-container"></div>
        
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Emplacements dans la Zone</h4>
            </div>
            <div class="card-body">
                <div id="locations-list"></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ajouter un Emplacement à la Zone</h4>
            </div>
            <div class="card-body">
                <div id="available-locations-container"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Zone Modal -->
<div class="modal fade" id="edit-zone-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la Zone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="primary-location-info" class="mb-3"></div>
                <form id="edit-zone-form">
                    <div class="form-group">
                        <label for="edit-zone-name">Nom de la Zone *</label>
                        <input type="text" class="form-control" id="edit-zone-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-zone-description">Description</label>
                        <textarea class="form-control" id="edit-zone-description" rows="3"></textarea>
                    </div>
                    <div class="form-group" id="edit-zone-shared-users-group" style="display:none;">
                        <label for="edit-zone-shared-users">
                            Accès partagé
                            <span style="font-size:0.7rem;font-weight:600;background:#7367f0;color:#fff;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;">Admin</span>
                        </label>
                        <select class="select2 form-control" id="edit-zone-shared-users" multiple="multiple"></select>
                        <small class="form-text text-muted">Recherchez et sélectionnez les utilisateurs qui auront un accès complet à cette zone.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="updateZoneInfo()">Enregistrer les Modifications</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script>
    const ZONE_ID = {{ $zone }};
</script>
<script src="/assets/js/zone-details.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
