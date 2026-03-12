@extends('layouts.app')

@section('title', 'Paramètres globaux - Monsieur WiFi')

@php $locale = 'fr'; @endphp

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-validation.css">
<style>
    .setting-section {
        padding: 1.5rem;
        border-radius: 0.428rem;
        border: 1px solid #ebe9f1;
        margin-bottom: 1.5rem;
    }
    .setting-section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    .setting-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Paramètres globaux</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Paramètres</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="settings-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="captive-portal-tab" data-toggle="pill" href="#captive-portal" role="tab" aria-selected="true">
                                    <i data-feather="wifi" class="mr-50"></i><span class="font-weight-bold">Portail captif</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="radius-tab" data-toggle="pill" href="#radius" role="tab" aria-selected="false">
                                    <i data-feather="shield" class="mr-50"></i><span class="font-weight-bold">Configuration RADIUS</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="branding-tab" data-toggle="pill" href="#branding" role="tab" aria-selected="false">
                                    <i data-feather="image" class="mr-50"></i><span class="font-weight-bold">Image de marque</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" role="tab" aria-selected="false">
                                    <i data-feather="server" class="mr-50"></i><span class="font-weight-bold">Système</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Portail captif -->
                            <div role="tabpanel" class="tab-pane active" id="captive-portal" aria-labelledby="captive-portal-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="wifi"></i></div></div>
                                            <h3 class="setting-section-title">Paramètres WiFi par défaut</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_essid">ESSID par défaut</label>
                                                    <input type="text" id="default_essid" class="form-control" name="default_essid" placeholder="MrWiFi-Guest" />
                                                    <small>Cet ESSID sera utilisé par défaut pour tous les nouveaux points d'accès</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_guest_essid">ESSID invité par défaut</label>
                                                    <input type="text" id="default_guest_essid" class="form-control" name="default_guest_essid" placeholder="MrWiFi-Guest" />
                                                    <small>Cet ESSID sera utilisé par défaut pour les réseaux invités</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_password">Mot de passe par défaut</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="default_password" class="form-control" name="default_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Mot de passe par défaut pour les nouveaux points d'accès (minimum 8 caractères)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-info p-50 mr-1"><div class="avatar-content"><i data-feather="layout"></i></div></div>
                                            <h3 class="setting-section-title">Comportement du portail captif</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_timeout">Délai d'expiration de session par défaut</label>
                                                    <div class="input-group">
                                                        <input type="number" id="portal_timeout" class="form-control" name="portal_timeout" min="1" max="168" />
                                                        <div class="input-group-append"><span class="input-group-text">Heures</span></div>
                                                    </div>
                                                    <small>Durée pendant laquelle les utilisateurs restent authentifiés avant de devoir se reconnecter</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="idle_timeout">Délai d'inactivité par défaut</label>
                                                    <div class="input-group">
                                                        <input type="number" id="idle_timeout" class="form-control" name="idle_timeout" min="5" max="180" />
                                                        <div class="input-group-append"><span class="input-group-text">Minutes</span></div>
                                                    </div>
                                                    <small>Déconnecter les utilisateurs inactifs après cette période</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="bandwidth_limit">Limite de bande passante par défaut</label>
                                                    <div class="input-group">
                                                        <input type="number" id="bandwidth_limit" class="form-control" name="bandwidth_limit" min="1" max="1000" />
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                    <small>Limite de bande passante par défaut par utilisateur</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="user_limit">Nombre maximum d'utilisateurs par défaut</label>
                                                    <input type="number" id="user_limit" class="form-control" name="user_limit" min="1" max="500" />
                                                    <small>Nombre maximum d'utilisateurs simultanés par point d'accès</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" id="enable_terms" name="enable_terms" />
                                                        <label class="custom-control-label" for="enable_terms">Afficher les Conditions d'utilisation</label>
                                                    </div>
                                                    <small>Exiger l'acceptation des Conditions d'utilisation avant la connexion</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- RADIUS -->
                            <div class="tab-pane" id="radius" role="tabpanel" aria-labelledby="radius-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="shield"></i></div></div>
                                            <h3 class="setting-section-title">Serveur RADIUS principal</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_ip">Adresse IP du serveur</label>
                                                    <input type="text" id="radius_ip" class="form-control" name="radius_ip" placeholder="192.168.1.100" />
                                                    <small>Adresse IP de votre serveur RADIUS principal</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_port">Port d'authentification</label>
                                                    <input type="number" id="radius_port" class="form-control" name="radius_port" placeholder="1812" min="1" max="65535" />
                                                    <small>Port utilisé pour l'authentification RADIUS (par défaut : 1812)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_secret">Secret partagé</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="radius_secret" class="form-control" name="radius_secret" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Secret partagé pour l'authentification RADIUS</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="accounting_port">Port de comptabilité</label>
                                                    <input type="number" id="accounting_port" class="form-control" name="accounting_port" placeholder="1813" min="1" max="65535" />
                                                    <small>Port utilisé pour la comptabilité RADIUS (par défaut : 1813)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                            <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Image de marque -->
                            <div class="tab-pane" id="branding" role="tabpanel" aria-labelledby="branding-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="type"></i></div></div>
                                            <h3 class="setting-section-title">Informations de l'entreprise</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_name">Nom de l'entreprise</label>
                                                    <input type="text" id="company_name" class="form-control" name="company_name" placeholder="monsieur-wifi" />
                                                    <small>Le nom de votre entreprise tel qu'affiché sur le portail captif</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_website">Site web de l'entreprise</label>
                                                    <input type="url" id="company_website" class="form-control" name="company_website" placeholder="https://www.example.com" />
                                                    <small>L'URL du site web de votre entreprise</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="contact_email">Email de contact</label>
                                                    <input type="email" id="contact_email" class="form-control" name="contact_email" placeholder="support@example.com" />
                                                    <small>Email de contact affiché sur le portail captif</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="support_phone">Téléphone de support</label>
                                                    <input type="tel" id="support_phone" class="form-control" name="support_phone" placeholder="+1 (555) 123-4567" />
                                                    <small>Numéro de téléphone de support affiché sur le portail captif</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-info p-50 mr-1"><div class="avatar-content"><i data-feather="image"></i></div></div>
                                            <h3 class="setting-section-title">Logo et images</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="logo-upload">Logo de l'entreprise</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="logo-upload" accept="image/*" />
                                                        <label class="custom-file-label" for="logo-upload">Choisir un fichier</label>
                                                    </div>
                                                    <small>Taille recommandée : 300px x 100px (PNG ou SVG avec transparence)</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Logo actuel</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Logo actuel" height="50" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="favicon-upload">Favicon</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="favicon-upload" accept="image/x-icon,image/png,image/gif" />
                                                        <label class="custom-file-label" for="favicon-upload">Choisir un fichier</label>
                                                    </div>
                                                    <small>Taille recommandée : 32px x 32px (ICO, PNG ou GIF)</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Favicon actuel</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/app-assets/mrwifi-assets/MrWifi.png" alt="Favicon actuel" height="32" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="splash-background">Arrière-plan du portail captif</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="splash-background" accept="image/*" />
                                                        <label class="custom-file-label" for="splash-background">Choisir un fichier</label>
                                                    </div>
                                                    <small>Taille recommandée : 1920px x 1080px (JPG ou PNG)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-warning p-50 mr-1"><div class="avatar-content"><i data-feather="layers"></i></div></div>
                                            <h3 class="setting-section-title">Personnalisation du portail</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="primary_color">Couleur principale</label>
                                                    <div class="input-group">
                                                        <input type="color" id="primary_color" class="form-control" name="primary_color" value="#7367f0" />
                                                        <div class="input-group-append"><span class="input-group-text">#7367f0</span></div>
                                                    </div>
                                                    <small>Couleur principale pour les boutons et les mises en évidence</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="secondary_color">Couleur secondaire</label>
                                                    <div class="input-group">
                                                        <input type="color" id="secondary_color" class="form-control" name="secondary_color" value="#82868b" />
                                                        <div class="input-group-append"><span class="input-group-text">#82868b</span></div>
                                                    </div>
                                                    <small>Couleur secondaire pour les accents et les éléments alternatifs</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="font_family">Police principale</label>
                                                    <select id="font_family" class="form-control" name="font_family">
                                                        <option value="montserrat" selected>Montserrat</option>
                                                        <option value="roboto">Roboto</option>
                                                        <option value="open-sans">Open Sans</option>
                                                        <option value="lato">Lato</option>
                                                        <option value="poppins">Poppins</option>
                                                    </select>
                                                    <small>Famille de police utilisée dans tout le portail</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_theme">Thème du portail</label>
                                                    <select id="portal_theme" class="form-control" name="portal_theme">
                                                        <option value="light" selected>Clair</option>
                                                        <option value="dark">Sombre</option>
                                                        <option value="auto">Automatique (préférence système)</option>
                                                    </select>
                                                    <small>Thème par défaut pour le portail captif</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                            <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Système -->
                            <div class="tab-pane" id="system" role="tabpanel" aria-labelledby="system-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-warning p-50 mr-1"><div class="avatar-content"><i data-feather="mail"></i></div></div>
                                            <h3 class="setting-section-title">Configuration email</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_server">Serveur SMTP</label>
                                                    <input type="text" id="smtp_server" class="form-control" name="smtp_server" placeholder="smtp.example.com" />
                                                    <small>Serveur SMTP pour l'envoi de notifications par email</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_port">Port SMTP</label>
                                                    <input type="number" id="smtp_port" class="form-control" name="smtp_port" placeholder="587" min="1" max="65535" />
                                                    <small>Port pour la connexion au serveur SMTP</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="sender_email">Email expéditeur</label>
                                                    <input type="email" id="sender_email" class="form-control" name="sender_email" placeholder="notifications@example.com" />
                                                    <small>Adresse email à partir de laquelle les notifications sont envoyées</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_password">Mot de passe SMTP</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="smtp_password" class="form-control" name="smtp_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Mot de passe pour l'authentification avec le serveur SMTP</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" id="send-test-email" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="send" class="mr-25"></i><span>Envoyer un email de test</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Enregistrer les modifications</button>
                                            <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
<script src="/app-assets/js/scripts/forms/form-validation.js"></script>

<script>
    $(window).on('load', function() {
        if ($.fn.select2) {
            $('#font_family, #portal_theme').select2({ minimumResultsForSearch: Infinity });
        }

        $('.custom-file-input').on('change', function() {
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || 'Choisir un fichier');
        });

        $('#primary_color, #secondary_color').on('input change', function() {
            $(this).closest('.input-group').find('.input-group-text').text($(this).val());
        });

        $('.form-password-toggle .input-group-text').on('click', function(e) {
            e.preventDefault();
            var passwordInput = $(this).closest('.form-password-toggle').find('input');
            if (passwordInput.attr('type') === 'text') {
                passwordInput.attr('type', 'password');
                $(this).find('svg').replaceWith(feather.icons['eye'].toSvg());
            } else {
                passwordInput.attr('type', 'text');
                $(this).find('svg').replaceWith(feather.icons['eye-off'].toSvg());
            }
        });

        var hash = window.location.hash;
        if (hash) $('.nav-pills a[href="' + hash + '"]').tab('show');

        $('.nav-pills a').on('shown.bs.tab', function(e) {
            if (history.pushState) history.pushState(null, null, e.target.hash);
            else window.location.hash = e.target.hash;
        });

        loadSettings();
    });

    $(document).ready(function() {
        $('.validate-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            var tabId = form.closest('.tab-pane').attr('id');

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...').attr('disabled', true);

            var formData = new FormData();
            switch(tabId) {
                case 'captive-portal':
                    formData.append('default_essid', $('#default_essid').val());
                    formData.append('default_guest_essid', $('#default_guest_essid').val());
                    formData.append('default_password', $('#default_password').val());
                    formData.append('portal_timeout', $('#portal_timeout').val());
                    formData.append('idle_timeout', $('#idle_timeout').val());
                    formData.append('bandwidth_limit', $('#bandwidth_limit').val());
                    formData.append('user_limit', $('#user_limit').val());
                    formData.append('enable_terms', $('#enable_terms').is(':checked') ? 1 : 0);
                    break;
                case 'radius':
                    formData.append('radius_ip', $('#radius_ip').val());
                    formData.append('radius_port', $('#radius_port').val());
                    formData.append('radius_secret', $('#radius_secret').val());
                    formData.append('accounting_port', $('#accounting_port').val());
                    break;
                case 'branding':
                    formData.append('company_name', $('#company_name').val());
                    formData.append('company_website', $('#company_website').val());
                    formData.append('contact_email', $('#contact_email').val());
                    formData.append('support_phone', $('#support_phone').val());
                    formData.append('primary_color', $('#primary_color').val());
                    formData.append('secondary_color', $('#secondary_color').val());
                    formData.append('font_family', $('#font_family').val());
                    formData.append('portal_theme', $('#portal_theme').val());
                    if ($('#logo-upload')[0].files[0])       formData.append('logo', $('#logo-upload')[0].files[0]);
                    if ($('#favicon-upload')[0].files[0])    formData.append('favicon', $('#favicon-upload')[0].files[0]);
                    if ($('#splash-background')[0].files[0]) formData.append('splash_background', $('#splash-background')[0].files[0]);
                    break;
                case 'system':
                    formData.append('smtp_server', $('#smtp_server').val());
                    formData.append('smtp_port', $('#smtp_port').val());
                    formData.append('sender_email', $('#sender_email').val());
                    formData.append('smtp_password', $('#smtp_password').val());
                    break;
            }

            $.ajax({
                url: '/api/system-settings', type: 'POST', data: formData, processData: false, contentType: false,
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
                success: function(response) {
                    toastr.success('Vos paramètres ont été enregistrés avec succès.', 'Paramètres enregistrés');
                    if (response.settings) populateFormFields(response.settings);
                    submitBtn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : (xhr.responseJSON?.message || 'Échec de l\'enregistrement des paramètres.');
                    toastr.error(msg, 'Erreur');
                    submitBtn.html(originalText).attr('disabled', false);
                }
            });
        });

        $('#send-test-email').on('click', function() {
            var btn = $(this);
            var originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Envoi...').attr('disabled', true);
            $.ajax({
                url: '/api/system-settings/test-email', type: 'POST',
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                data: JSON.stringify({ email: $('#sender_email').val() }),
                success: function() {
                    toastr.info('Email de test envoyé à ' + $('#sender_email').val(), 'Email envoyé');
                    btn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Échec de l\'envoi de l\'email de test. Vérifiez vos paramètres SMTP.', 'Erreur');
                    btn.html(originalText).attr('disabled', false);
                }
            });
        });
    });

    function loadSettings() {
        $.ajax({
            url: '/api/system-settings', type: 'GET',
            headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
            success: function(response) {
                if (response.status === 'success'){
                    populateFormFields(response.settings);
                } else {
                    if (response.message && response.message==='You are not authorized to view system settings') {
                        window.location.href = '/fr/dashboard';
                    }
                }
            },
            error: function(xhr) { 
                toastr.error('Impossible de charger les paramètres. Veuillez réessayer.', 'Erreur'); 
                // alert(xhr.responseJSON.message);
                if (xhr.responseJSON.message && xhr.responseJSON.message==='You are not authorized to view system settings') {
                    window.location.href = '/fr/dashboard';
                }
            }
        });
    }

    function populateFormFields(s) {
        $('#default_essid').val(s.default_essid);
        $('#default_guest_essid').val(s.default_guest_essid);
        $('#default_password').val(s.default_password);
        $('#portal_timeout').val(s.portal_timeout);
        $('#idle_timeout').val(s.idle_timeout);
        $('#bandwidth_limit').val(s.bandwidth_limit);
        $('#user_limit').val(s.user_limit);
        $('#enable_terms').prop('checked', s.enable_terms);
        $('#radius_ip').val(s.radius_ip);
        $('#radius_port').val(s.radius_port);
        $('#radius_secret').val(s.radius_secret);
        $('#accounting_port').val(s.accounting_port);
        $('#company_name').val(s.company_name);
        $('#company_website').val(s.company_website);
        $('#contact_email').val(s.contact_email);
        $('#support_phone').val(s.support_phone);
        if (s.primary_color) {
            $('#primary_color').val(s.primary_color);
            $('#primary_color').closest('.input-group').find('.input-group-text').text(s.primary_color);
        }
        if (s.secondary_color) {
            $('#secondary_color').val(s.secondary_color);
            $('#secondary_color').closest('.input-group').find('.input-group-text').text(s.secondary_color);
        }
        if (s.font_family)  $('#font_family').val(s.font_family).trigger('change');
        if (s.portal_theme) $('#portal_theme').val(s.portal_theme).trigger('change');
        $('#smtp_server').val(s.smtp_server);
        $('#smtp_port').val(s.smtp_port);
        $('#sender_email').val(s.sender_email);
        $('#smtp_password').val(s.smtp_password);
    }
</script>
@endpush
