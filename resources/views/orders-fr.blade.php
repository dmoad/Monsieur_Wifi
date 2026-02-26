@extends('layouts.app')

@section('title', 'Mes Commandes - Monsieur WiFi')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Mes Commandes</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/boutique">Boutique</a></li>
                        <li class="breadcrumb-item active">Mes Commandes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12  text-right">
        <a href="/fr/boutique" class="btn btn-primary">
            <i data-feather="shopping-bag"></i> Continuer mes Achats
        </a>
    </div>
</div>
<div class="content-body">
    <div id="orders-loading" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
        </div>
    </div>
    <div id="orders-empty" style="display: none;">
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-feather="inbox" class="mb-3" style="width: 64px; height: 64px;"></i>
                <h4>Aucune commande pour le moment</h4>
                <p>Commencez vos achats pour voir vos commandes ici!</p>
                <a href="/fr/boutique" class="btn btn-primary">Acheter Maintenant</a>
            </div>
        </div>
    </div>
    <div id="orders-list"></div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/orders-fr.js?v=<?php echo time() + 3; ?>"></script>
@endpush

@php
    $locale = 'fr';
@endphp
