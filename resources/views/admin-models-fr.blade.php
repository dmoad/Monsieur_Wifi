@extends('layouts.app')

@section('title', 'Gérer les Modèles - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Gérer les Modèles</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Gérer les Modèles</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <button class="btn btn-primary" onclick="showModelModal()">
            <i data-feather="plus"></i> Ajouter un Modèle
        </button>
    </div>
</div>
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filtrer les Modèles</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="type-filter" class="form-control">
                        <option value="">Tous les Types</option>
                        <option value="820">Type 820</option>
                        <option value="835">Type 835</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="active-filter" class="form-control">
                        <option value="">Tous les Statuts</option>
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="Rechercher des modèles...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadModels()">Appliquer le Filtre</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="models-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    
    <div id="models-list"></div>
    
    <div id="model-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Ajouter un Modèle</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="model-form">
                        <input type="hidden" id="model-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model-name">Nom du Modèle *</label>
                                    <input type="text" class="form-control" id="model-name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="model-type">Type d'Appareil *</label>
                                    <select class="form-control" id="model-type" required>
                                        <option value="">Sélectionner le Type</option>
                                        <option value="820">820</option>
                                        <option value="835">835</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="model-price">Prix ($) *</label>
                                    <input type="number" class="form-control" id="model-price" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description-en">Description (Anglais) *</label>
                            <textarea class="form-control" id="description-en" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description-fr">Description (Français) *</label>
                            <textarea class="form-control" id="description-fr" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="model-active" checked>
                                <label class="custom-control-label" for="model-active">Actif</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sort-order">Ordre de Tri</label>
                            <input type="number" class="form-control" id="sort-order" value="0" min="0">
                        </div>
                    </form>
                    
                    <div id="edit-images-section" style="display: none;">
                        <hr>
                        <h5>Images du Produit</h5>
                        <div class="form-group">
                            <label for="image-upload">Télécharger une Image</label>
                            <input type="file" class="form-control" id="image-upload" accept="image/*">
                            <small class="text-muted">JPEG, PNG, JPG, GIF (max 2MB)</small>
                        </div>
                        <div id="images-list" class="row mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="saveModel()">Enregistrer le Modèle</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/admin-models.js?v=<?php echo time() + 1; ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
