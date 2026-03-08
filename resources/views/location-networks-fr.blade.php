@extends('layouts.app')

@section('title', 'Paramètres réseau - Contrôleur monsieur-wifi')

@php $locale = 'fr'; @endphp

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
<link rel="stylesheet" type="text/css" href="/working-hours/interactive-schedule.css">
<link rel="stylesheet" type="text/css" href="/assets/css/location-networks.css">
@endpush

@section('content')
<!-- Page header -->
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Paramètres réseau</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/fr/locations">Emplacements</a></li>
                        <li class="breadcrumb-item"><a id="breadcrumb-location-link" href="#">Chargement...</a></li>
                        <li class="breadcrumb-item active">Réseaux</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <a id="back-to-location-btn" href="#" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="mr-1"></i> Retour à l'emplacement
        </a>
    </div>
</div>

<div class="content-body">

    <!-- Location info strip -->
    <div class="card mb-2" style="border-radius:10px;">
        <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;">
                    <i data-feather="map-pin" style="color:white;width:20px;height:20px;"></i>
                </div>
                <div>
                    <h5 class="mb-0 location_name" style="font-weight:700; padding-left:10px;">Chargement...</h5>
                    <small class="text-muted location_address" style="padding-left:10px;"></small>
                </div>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <div class="d-flex align-items-center" style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:6px 14px;gap:10px;">
                    <i data-feather="layers" style="width:14px;height:14px;color:#7367f0;flex-shrink:0;"></i>
                    <span style="font-size:0.85rem;font-weight:600;color:#495057;white-space:nowrap;">Support VLAN</span>
                    <div class="custom-control custom-switch mb-0">
                        <input type="checkbox" class="custom-control-input" id="vlan-enabled">
                        <label class="custom-control-label" for="vlan-enabled"></label>
                    </div>
                </div>
                <button class="btn custom-btn btn-sm" id="add-network-btn" disabled style="height:36px;white-space:nowrap;">
                    <i data-feather="plus" class="mr-1"></i> Ajouter un réseau
                </button>
            </div>
        </div>
    </div>

    <!-- Network tabs nav -->
    <ul class="nav nav-tabs" role="tablist" id="network-tabs-nav">
        <li class="nav-item" id="network-tabs-loading">
            <span class="nav-link disabled"><i class="fas fa-spinner fa-spin mr-1"></i>Chargement des réseaux…</span>
        </li>
    </ul>

    <!-- Network tab panes (populated by JS) -->
    <div class="tab-content" id="network-tabs-content">
        <!-- injected by NetworkManager -->
    </div>

</div><!-- end .content-body -->

<!-- ============================================================
     HIDDEN TEMPLATES
============================================================ -->

<!-- Tab nav item template -->
<template id="network-tab-tpl">
    <li class="nav-item" data-network-id="__ID__">
        <a class="nav-link" id="network-tab-__ID__" data-toggle="tab" href="#network-pane-__ID__" role="tab">
            <i data-feather="wifi" class="mr-1"></i>
            <span class="network-tab-label">Réseau</span>
            <span class="network-type-badge network-type-__TYPE__">__TYPE_LABEL__</span>
        </a>
    </li>
</template>

<!-- Tab pane template -->
<template id="network-pane-tpl">
    <div class="tab-pane fade" id="network-pane-__ID__" role="tabpanel" data-network-id="__ID__">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><span class="network-pane-title">Réseau</span></h4>
                <div class="d-flex align-items-center gap-2" style="gap:0.5rem;">
                    <button type="button" class="btn btn-outline-danger btn-sm network-delete-btn" data-network-id="__ID__">
                        <i data-feather="trash-2" class="mr-1"></i> Supprimer le réseau
                    </button>
                    <button type="button" class="btn custom-btn network-save-btn" data-network-id="__ID__">
                        <i data-feather="save" class="mr-1"></i> Enregistrer les paramètres
                    </button>
                </div>
            </div>
            <div class="card-body">

                <!-- Network identity bar -->
                <div class="network-identity-bar mt-1">
                    <select class="network-type-select d-none" data-network-id="__ID__">
                        <option value="password">WiFi avec mot de passe</option>
                        <option value="captive_portal">Portail captif</option>
                        <option value="open">ESSID ouvert</option>
                    </select>
                    <div class="network-type-pill-group">
                        <button type="button" class="network-type-pill" data-type="password">
                            <i data-feather="lock" style="width:13px;height:13px;"></i> Mot de passe
                        </button>
                        <button type="button" class="network-type-pill" data-type="captive_portal">
                            <i data-feather="layout" style="width:13px;height:13px;"></i> Portail captif
                        </button>
                        <button type="button" class="network-type-pill" data-type="open">
                            <i data-feather="wifi" style="width:13px;height:13px;"></i> Ouvert
                        </button>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-ssid-wrap">
                        <i data-feather="wifi" class="ssid-icon" style="width:15px;height:15px;"></i>
                        <input type="text" class="form-control network-ssid" placeholder="Nom du réseau (SSID)" maxlength="32">
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-visibility-wrap">
                        <i data-feather="eye" style="width:14px;height:14px;color:#adb5bd;flex-shrink:0;"></i>
                        <select class="network-visible">
                            <option value="1">Diffuser le SSID</option>
                            <option value="0">SSID masqué</option>
                        </select>
                    </div>
                    <div class="network-identity-divider mx-1"></div>
                    <div class="network-enabled-wrap">
                        <div class="custom-control custom-switch mb-0">
                            <input type="checkbox" class="custom-control-input network-enabled" id="network-enabled-__ID__" checked>
                            <label class="custom-control-label" for="network-enabled-__ID__">Activé</label>
                        </div>
                    </div>
                </div>

                <!-- ── PASSWORD section ── -->
                <div class="network-section network-section-password">
                    <div class="network-type-panel panel-password">
                        <div class="network-type-panel-header">
                            <span class="network-type-panel-icon">
                                <i data-feather="lock" style="color:#7367f0;width:16px;height:16px;"></i>
                            </span>
                            <h6 class="network-type-panel-title">Sécurité &amp; Chiffrement</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Mot de passe WiFi</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control network-password" placeholder="Minimum 8 caractères">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary network-toggle-password" type="button"><i data-feather="eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Protocole de sécurité</label>
                                        <select class="form-control network-security">
                                            <option value="wpa2-psk" selected>WPA2-PSK (Recommandé)</option>
                                            <option value="wpa-wpa2-psk">WPA/WPA2-PSK Mixte</option>
                                            <option value="wpa3-psk">WPA3-PSK (Plus sécurisé)</option>
                                            <option value="wep">WEP (Hérité)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Suites de chiffrement</label>
                                        <select class="form-control network-cipher-suites">
                                            <option value="CCMP" selected>CCMP</option>
                                            <option value="TKIP">TKIP</option>
                                            <option value="TKIP+CCMP">TKIP+CCMP</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CAPTIVE PORTAL section ── -->
                <div class="network-section network-section-captive_portal">
                    <div class="network-type-panel panel-captive">
                        <div class="network-type-panel-header">
                            <span class="network-type-panel-icon">
                                <i data-feather="layout" style="color:#28c76f;width:16px;height:16px;"></i>
                            </span>
                            <h6 class="network-type-panel-title">Configuration du portail captif</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="panel-sub-label">Authentification</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Méthode</label>
                                        <select class="form-control network-auth-method">
                                            <option value="click-through" selected>Clic (sans authentification)</option>
                                            <option value="password">Authentification par mot de passe</option>
                                            <option value="sms">Vérification par SMS</option>
                                            <option value="email">Vérification par e-mail</option>
                                            <option value="social">Connexion via les réseaux sociaux</option>
                                        </select>
                                    </div>
                                    <div class="form-group network-captive-password-group" style="display:none;">
                                        <label>Mot de passe partagé</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control network-portal-password">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary network-toggle-portal-password" type="button"><i data-feather="eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group network-social-group" style="display:none;">
                                        <label>Réseau social</label>
                                        <select class="form-control network-social-method">
                                            <option value="facebook">Facebook</option>
                                            <option value="google">Google</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Design du portail</label>
                                        <select class="form-control network-portal-design-id">
                                            <option value="">Design par défaut</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>URL de redirection <small class="text-muted font-weight-normal">(optionnel)</small></label>
                                        <input type="url" class="form-control network-redirect-url" placeholder="https://exemple.com">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Délai de session</label>
                                        <select class="form-control network-session-timeout">
                                            <option value="60">1 Heure</option><option value="120">2 Heures</option><option value="180">3 Heures</option>
                                            <option value="240">4 Heures</option><option value="300">5 Heures</option><option value="360">6 Heures</option>
                                            <option value="720">12 Heures</option><option value="1440">1 Jour</option><option value="10080">1 Semaine</option>
                                            <option value="43200">3 Mois</option><option value="172800">1 An</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Délai d'inactivité</label>
                                        <select class="form-control network-idle-timeout">
                                            <option value="15">15 Minutes</option><option value="30">30 Minutes</option><option value="45">45 Minutes</option>
                                            <option value="60">1 Heure</option><option value="120">2 Heures</option><option value="240">4 Heures</option>
                                            <option value="720">12 Heures</option><option value="1440">1 Jour</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-sub-section">
                                <div class="panel-sub-label">Limites de bande passante</div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="download" style="width:13px;height:13px;margin-right:4px;"></i>Téléchargement (Mbps)</label>
                                            <input type="number" class="form-control network-download-limit" placeholder="Illimité" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i data-feather="upload" style="width:13px;height:13px;margin-right:4px;"></i>Envoi (Mbps)</label>
                                            <input type="number" class="form-control network-upload-limit" placeholder="Illimité" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible: Working Hours (captive portal only) -->
                <div class="collapsible-section network-section network-section-captive_portal" data-collapse-id="working-hours-__ID__">
                    <div class="collapsible-section-header" data-target="working-hours-__ID__">
                        <h6 class="collapsible-section-title">
                            <span class="section-icon" style="background:rgba(40,199,111,0.12);">
                                <i data-feather="clock" style="color:#28c76f;width:14px;height:14px;"></i>
                            </span>
                            Heures de travail
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="working-hours-__ID__" style="display:none;">
                        <div class="network-schedule-wrapper" id="schedule-wrapper-__ID__" style="padding: 0.75rem 0 0.5rem;"></div>
                    </div>
                </div>

                <!-- ── OPEN section ── -->
                <div class="network-section network-section-open">
                    <div class="network-type-panel panel-open">
                        <div class="network-type-panel-header">
                            <span class="network-type-panel-icon">
                                <i data-feather="wifi" style="color:#ff9f43;width:16px;height:16px;"></i>
                            </span>
                            <h6 class="network-type-panel-title">Réseau ouvert</h6>
                        </div>
                        <div class="network-type-panel-body">
                            <div class="d-flex align-items-start" style="gap:12px;">
                                <i data-feather="alert-triangle" style="width:20px;height:20px;color:#ff9f43;flex-shrink:0;margin-top:1px;"></i>
                                <div>
                                    <p class="mb-1" style="font-weight:600;color:#ff9f43;font-size:0.95rem;">Aucune authentification requise</p>
                                    <p class="mb-0 text-muted" style="font-size:0.88rem;">Toute personne à portée peut se connecter sans mot de passe ni portail. À utiliser uniquement dans des environnements de confiance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible: IP & DHCP Settings -->
                <div class="collapsible-section" data-collapse-id="ip-dhcp-__ID__">
                    <div class="collapsible-section-header" data-target="ip-dhcp-__ID__">
                        <h6 class="collapsible-section-title">
                            <span class="section-icon" style="background:rgba(23,162,184,0.12);">
                                <i data-feather="server" style="color:#17a2b8;width:14px;height:14px;"></i>
                            </span>
                            Paramètres IP &amp; DHCP
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="ip-dhcp-__ID__" style="display:none;">
                        <div class="network-type-panel panel-ip mt-2">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="globe" style="color:#17a2b8;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">Configuration IP</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="panel-sub-label">Adressage</div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Mode IP</label>
                                            <select class="form-control network-ip-mode">
                                                <option value="static">IP statique</option>
                                                <option value="dhcp">Client DHCP</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Adresse IP</label>
                                            <input type="text" class="form-control network-ip-address" placeholder="192.168.x.1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Masque de sous-réseau</label>
                                            <input type="text" class="form-control network-netmask" placeholder="255.255.255.0" value="255.255.255.0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Passerelle</label>
                                            <input type="text" class="form-control network-gateway" placeholder="Auto">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>DNS primaire</label>
                                            <input type="text" class="form-control network-dns1" placeholder="8.8.8.8" value="8.8.8.8">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>DNS alt.</label>
                                            <input type="text" class="form-control network-dns2" placeholder="8.8.4.4" value="8.8.4.4">
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-sub-section">
                                    <div class="panel-sub-label">Serveur DHCP</div>
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="d-flex align-items-center" style="height:50px;">
                                                    <div class="custom-control custom-switch mb-0">
                                                        <input type="checkbox" class="custom-control-input network-dhcp-enabled" id="network-dhcp-__ID__" checked>
                                                        <label class="custom-control-label" for="network-dhcp-__ID__">Activer le DHCP</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Plage DHCP</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control network-dhcp-start" placeholder="x.x.x.100">
                                                    <div class="input-group-prepend input-group-append"><span class="input-group-text">–</span></div>
                                                    <input type="text" class="form-control network-dhcp-end" placeholder="x.x.x.200">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-sub-section">
                                    <div class="panel-sub-label">VLAN</div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>ID VLAN <small class="text-muted font-weight-normal">(1–4094)</small></label>
                                                <input type="number" class="form-control network-vlan-id" placeholder="Aucun" min="1" max="4094" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Marquage</label>
                                                <select class="form-control network-vlan-tagging" disabled>
                                                    <option value="disabled">Désactivé</option>
                                                    <option value="tagged">Marqué</option>
                                                    <option value="untagged">Non marqué</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible: MAC Address Filtering -->
                <div class="collapsible-section" data-collapse-id="mac-filter-__ID__">
                    <div class="collapsible-section-header" data-target="mac-filter-__ID__">
                        <h6 class="collapsible-section-title">
                            <span class="section-icon" style="background:rgba(234,84,85,0.12);">
                                <i data-feather="shield" style="color:#ea5455;width:14px;height:14px;"></i>
                            </span>
                            Filtrage des adresses MAC
                        </h6>
                        <i data-feather="chevron-down" class="collapsible-chevron" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="collapsible-section-body" id="mac-filter-__ID__" style="display:none;">
                        <div class="network-type-panel panel-mac mt-2">
                            <div class="network-type-panel-header">
                                <span class="network-type-panel-icon">
                                    <i data-feather="shield" style="color:#ea5455;width:16px;height:16px;"></i>
                                </span>
                                <h6 class="network-type-panel-title">Filtrage des adresses MAC</h6>
                            </div>
                            <div class="network-type-panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="panel-sub-label">Mode de filtrage</div>
                                        <div class="form-group">
                                            <select class="form-control network-mac-filter-mode">
                                                <option value="allow-all">Autoriser tous les appareils</option>
                                                <option value="allow-listed">Autoriser uniquement la liste</option>
                                                <option value="block-listed">Bloquer les appareils listés</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="panel-sub-label">Liste des adresses MAC</div>
                                        <div class="form-group mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control network-mac-input" placeholder="00:11:22:33:44:55">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary network-mac-add-btn" type="button">
                                                        <i data-feather="plus" style="width:14px;height:14px;"></i> Ajouter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mac-list-box">
                                            <div class="network-mac-list"></div>
                                            <div class="text-center text-muted p-3 network-mac-empty">
                                                <i data-feather="inbox" style="width:18px;height:18px;margin-bottom:4px;display:block;margin-left:auto;margin-right:auto;"></i>
                                                <small>Aucune adresse MAC ajoutée</small>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-success network-mac-save-btn" type="button">
                                                <i data-feather="save" class="mr-1"></i> Enregistrer le filtrage MAC
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end .card-body -->
        </div><!-- end .card -->
    </div><!-- end .tab-pane -->
</template>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>
<script src="/working-hours/interactive-schedule.js"></script>
<script>
    window.APP_CONFIG_V5 = {
        maxNetworks: {{ (int) env('MAX_NETWORKS_PER_LOCATION', 4) }},
        apiBase: '{{ rtrim(config("app.url"), "/") }}/api',
        messages: {
            networkSaved:       'Paramètres réseau enregistrés.',
            routerReconfigure:  'Configuration du routeur mise à jour — l\'appareil va se reconfigurer.',
            workingHoursSaved:  'Heures de travail enregistrées.',
            macFilterSaved:     'Paramètres de filtrage MAC enregistrés.',
            networkAdded:       'Réseau ajouté.',
            networkDeleted:     'Réseau supprimé.',
            invalidMac:         'Format d\'adresse MAC invalide.',
            invalidSsid:        'Le SSID ne peut pas être vide.',
            ssidTooLong:        'Le SSID doit contenir 32 caractères maximum (limite 802.11).',
            savingSchedule:     'Enregistrement…',
        },
        schedulerLabels: {
            title:              'Heures de travail',
            subtitle:           'Horaires d\'accès au portail captif',
            quickSet:           'Accès rapide :',
            businessHours:      'Heures ouvrables',
            clearAll:           'Tout effacer',
            saveSchedule:       'Enregistrer l\'horaire',
            hint:               'Cliquez sur une cellule vide pour créer un créneau. Glissez pour déplacer, redimensionnez avec les poignées, survolez pour supprimer.',
            days: {
                monday:    'Lundi',
                tuesday:   'Mardi',
                wednesday: 'Mercredi',
                thursday:  'Jeudi',
                friday:    'Vendredi',
                saturday:  'Samedi',
                sunday:    'Dimanche',
            },
            msgOverlap:         'Impossible de créer le créneau : chevauchement avec un créneau existant.',
            msgInvalidMove:     'Position invalide : le créneau chevaucherait ou dépasserait les limites.',
            msgInvalidResize:   'Redimensionnement invalide : chevaucherait ou dépasserait les limites.',
            msgBusinessApplied: 'Heures ouvrables appliquées.',
            msgCleared:         'Tous les créneaux effacés.',
            msgSaved:           'Horaire enregistré !',
        },
        typeLabels: {
            password:       'Mot de passe WiFi',
            captive_portal: 'Portail captif',
            open:           'Ouvert',
        },
    };
</script>
<script src="/assets/js/location-networks-v5.js?v=1"></script>
@endpush
