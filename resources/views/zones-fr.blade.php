@extends('layouts.app')

@section('title', 'Zones - Monsieur WiFi')

@push('styles')
<style>
    .zone-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }
    .zone-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(115, 103, 240, 0.2);
    }
    .zone-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .zone-body {
        padding: 1.5rem;
    }
    .zone-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .zone-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .zone-stats {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
    }
    .zone-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .zone-stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .zone-actions {
        display: flex;
        gap: 0.5rem;
    }
    .admin-alert {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Zones</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Zones</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <button class="btn btn-primary" onclick="showZoneModal()">
            <i data-feather="plus"></i> Créer une Zone
        </button>
    </div>
</div>

<div class="content-body">
    <div id="admin-alert-container"></div>
    
    <div id="zones-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div>
    
    <div id="zones-list"></div>
</div>

<!-- Zone Modal -->
<div class="modal fade" id="zone-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zone-modal-title">Créer une Zone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="primary-location-info-edit" class="mb-3"></div>
                <form id="zone-form">
                    <input type="hidden" id="zone-id">
                    <div class="form-group">
                        <label for="zone-name">Nom de la Zone *</label>
                        <input type="text" class="form-control" id="zone-name" required placeholder="Entrez le nom de la zone">
                    </div>
                    <div class="form-group">
                        <label for="zone-description">Description</label>
                        <textarea class="form-control" id="zone-description" rows="3" placeholder="Entrez la description de la zone (optionnel)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveZone()">Enregistrer la Zone</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/zones.js?v=<?php echo time(); ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
