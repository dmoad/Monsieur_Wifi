@extends('layouts.app')

@section('title', 'Concepteur de portail captif - Monsieur WiFi')

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 1rem;
    }
    
    .upload-area:hover {
        border-color: #7367f0;
        background-color: rgba(115, 103, 240, 0.05);
    }
    
    .upload-area.highlight {
        border-color: #7367f0;
        background-color: rgba(115, 103, 240, 0.1);
        transform: scale(1.02);
    }
    
    .upload-icon {
        font-size: 2.5rem;
        color: #7367f0;
        margin-bottom: 1rem;
    }
    
    .color-picker-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    
    .image-preview {
        width: 100%;
        max-height: 150px;
        object-fit: contain;
        margin-top: 10px;
        border-radius: 5px;
        display: none;
    }
    
    .preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .preview-header {
        background-color: #f8f9fa;
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .portal-preview {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 500px;
    }
    
    .portal-preview.has-background-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 8px;
        z-index: 0;
    }
    
    .portal-preview.has-background-image > * {
        position: relative;
        z-index: 1;
    }
    
    .portal-preview.has-gradient {
        background: var(--gradient-bg) !important;
    }

    .preview-main {
        width: 100%;
        max-width: 420px;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    
    .logo-container {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }
    
    .preview-logo {
        max-height: 80px;
        max-width: 200px;
        object-fit: contain;
    }
    
    #preview-welcome {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        text-align: center;
        width: 100%;
    }
    
    #preview-instructions {
        font-size: 16px;
        color: #666;
        margin-bottom: 25px;
        text-align: center;
        line-height: 1.6;
        width: 100%;
    }
    
    .input-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 1rem;
    }
    
    .preview-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    
    .preview-button {
        width: 100%;
        padding: 12px 20px;
        background-color: #7367f0;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .preview-terms {
        font-size: 12px;
        color: #666;
        margin-top: 15px;
        text-align: center;
    }
    
    .preview-terms a {
        color: #7367f0;
        text-decoration: none;
    }
    
    .header {
        text-align: center;
        margin-bottom: 32px;
    }
    
    .location-logo {
        height: 64px;
        width: auto;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #666;
        padding: 0 16px;
    }
    
    .welcome-text {
        color: #333;
        font-size: 0.65rem;
        line-height: 1.5;
        margin: 24px 0 32px;
        text-align: center;
    }
    
    .login-placeholder {
        background: #f8f8f8;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
        color: #666;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    .login-placeholder-footer {
        background: #ffffff;
        border: 0px;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        color: #666;
        margin-bottom: 10px;
        flex-grow: 1;
    }
    
    .portal-preview .footer,
    .preview-main .footer {
        margin-top: auto;
        margin-left: 0 !important;
        margin-right: 0 !important;
        border-top: 1px solid #eee;
        padding-top: 1.5rem;
        padding-left: 0;
        padding-right: 0;
        text-align: center;
        width: 100%;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .brand-logo {
        margin-bottom: 1rem;
        margin-left: 0;
        margin-right: 0;
        display: block;
        width: 100%;
        text-align: center;
    }
    
    .brand-logo img {
        max-height: 32px;
        max-width: 150px;
        object-fit: contain;
        display: inline-block;
        margin: 0;
    }
    
    .terms {
        font-size: 0.8rem;
        color: #666;
        width: 100%;
        text-align: center;
        display: block;
        margin-left: 0;
        margin-right: 0;
        padding-left: 0;
        padding-right: 0;
    }
    
    #preview-terms-container {
        margin-bottom: 0.5rem !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    #preview-powered-by {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .terms a {
        color: #007bff;
        text-decoration: none;
    }
    
    .terms a:hover {
        text-decoration: underline;
    }
    
    .design-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e0e0e0;
    }
    
    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .design-preview {
        height: 180px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }
    
    .design-preview .preview-content {
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .location-logo-mini {
        height: 24px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    
    .login-area-mini {
        flex-grow: 1;
        background: rgba(248, 248, 248, 0.8);
        border: 1px dashed #ddd;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #666;
        margin: 8px 0;
    }
    
    .brand-logo-mini {
        height: 20px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }
    
    .design-card:hover .design-preview {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }

    #preview-button {
        transition: all 0.3s ease;
        padding: 10px 20px;
        font-size: 14px;
    }

    .terms-container {
        margin-top: 15px;
        text-align: center;
    }

    .section-label {
        font-weight: 600;
        color: #5e5873;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    .design-card {
        transition: all 0.3s ease;
        border: 1px solid #ebe9f1;
    }

    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }

    .design-preview {
        height: 160px;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }

    .preview-content {
        padding: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .design-actions {
        padding-top: 0.5rem;
        border-top: 1px solid #ebe9f1;
    }
    
    .design-actions .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .design-actions .btn-group .btn + .btn {
        margin-left: 0;
    }

    .badge {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .bg-light-primary {
        background-color: rgba(115, 103, 240, 0.12) !important;
    }

    .bg-light-success {
        background-color: rgba(40, 199, 111, 0.12) !important;
    }

    .bg-light-danger {
        background-color: rgba(234, 84, 85, 0.12) !important;
    }

    .tab-content h6 {
        color: #5e5873;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #ebe9f1;
        padding-bottom: 0.5rem;
    }

    .color-picker-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        max-width: 300px;
    }

    .form-control-color {
        width: 60px;
        padding: 0.2rem;
        height: 38px;
    }   

    .card-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: 2000 !important;
        background: #fff;
    }

    .modal {
        z-index: 2100 !important;
    }
    
    .modal-backdrop {
        z-index: 2050 !important;
    }

    .card-fullscreen .card-body {
        height: calc(100% - 60px);
        overflow-y: auto;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .card-fullscreen .portal-preview {
        max-width: 100%;
        width: 90%;
        margin: 0 auto;
        height: auto;
        min-height: 500px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        border-radius: 0.428rem;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        color: #6e6b7b;
    }

    @media (max-width: 991px) {
        .preview-container {
            margin-top: 2rem;
        }
        
        .portal-preview {
            min-height: 350px;
        }
    }
    
    @media (max-width: 767px) {
        .portal-preview {
            padding: 20px;
            min-height: 320px;
        }
        
        #preview-welcome {
            font-size: 20px;
        }
        
        #preview-instructions {
            font-size: 14px;
        }
        
        .preview-input, .preview-button {
            padding: 8px 15px;
            font-size: 14px;
        }
    }
    
    @media (max-width: 575px) {
        .preview-main {
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Concepteur de portail captif</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Portails captifs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Device CTA Banner - hidden by default, shown via JS if user has no devices -->
    <div id="device-cta-banner" style="display: none;"></div>

    <!-- Captive Portal Designs List -->
    <section id="captive-portal-designs-list">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Vos conceptions de portail captif</h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="create-new-design">
                                        <i data-feather="plus" class="mr-50"></i>
                                        <span>Créer une nouvelle conception</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="portal-designs-container">
                            <!-- Design Cards will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Captive Portal Design Content Starts - Initially hidden -->
    <section id="captive-portal-designer" style="display: none;">
        <div class="row">
            <div class="col-12 mb-1">
                <button class="btn btn-outline-secondary waves-effect" id="back-to-list">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    <span>Retour aux conceptions</span>
                </button>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Concevez votre page de connexion</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" aria-controls="general" role="tab" aria-selected="true">
                                    <i data-feather="settings" class="mr-25"></i>
                                    <span class="font-weight-bold">Général</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="branding-tab" data-toggle="tab" href="#branding" aria-controls="branding" role="tab" aria-selected="false">
                                    <i data-feather="image" class="mr-25"></i>
                                    <span class="font-weight-bold">Image de marque</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- General Tab -->
                            <div class="tab-pane active" id="general" aria-labelledby="general-tab" role="tabpanel">
                                <form class="mt-2">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <h6 class="mb-1">Informations de base</h6>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="portal-name">Nom du portail</label>
                                                <input type="text" class="form-control" id="portal-name" placeholder="Entrez un nom pour cette page de connexion">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="portal-description">Description</label>
                                                <textarea class="form-control" id="portal-description" rows="2" placeholder="Brève description de cette conception"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="theme-color">Couleur du thème</label>
                                                <div class="color-picker-container">
                                                    <input type="color" class="form-control form-control-color" id="theme-color" value="#7367f0">
                                                    <div class="color-preview" style="background-color: #7367f0;"></div>
                                                    <span class="color-value">#7367f0</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2 mb-1">
                                            <h6 class="mb-1">Contenu du portail</h6>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="welcome-message">Message de bienvenue</label>
                                                <input type="text" class="form-control" id="welcome-message" placeholder="Bienvenue sur notre WiFi" value="Bienvenue sur notre WiFi">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="button-text">Texte du bouton</label>
                                                <input type="text" class="form-control" id="button-text" placeholder="Se connecter au WiFi" value="Se connecter au WiFi">
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="login-instructions">Instructions de connexion</label>
                                                <textarea class="form-control" id="login-instructions" rows="2" placeholder="Entrez votre adresse e-mail pour vous connecter à notre réseau WiFi">Entrez votre adresse e-mail pour vous connecter à notre réseau WiFi</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="show-terms" checked>
                                                    <label class="custom-control-label" for="show-terms">Afficher le lien Conditions générales</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2 mb-1">
                                            <h6 class="mb-1">Contenu légal</h6>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="terms-of-service">Contenu des conditions de service</label>
                                                <textarea class="form-control" id="terms-of-service" rows="3" placeholder="Entrez le contenu de vos conditions de service">En accédant à ce service WiFi, vous acceptez de vous conformer à toutes les lois applicables et à la politique d'utilisation acceptable du réseau. Nous nous réservons le droit de surveiller le trafic et le contenu accessible via notre réseau, et de résilier l'accès en cas de violations de ces conditions.</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="privacy-policy">Contenu de la politique de confidentialité</label>
                                                <textarea class="form-control" id="privacy-policy" rows="3" placeholder="Entrez le contenu de votre politique de confidentialité">Nous collectons des informations limitées lorsque vous utilisez notre service WiFi, y compris les identifiants d'appareils, les heures de connexion et les données d'utilisation. Ces informations sont utilisées pour améliorer notre service, résoudre les problèmes techniques et respecter les exigences légales. Nous ne vendons pas vos informations personnelles à des tiers.</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Branding Tab -->
                            <div class="tab-pane" id="branding" aria-labelledby="branding-tab" role="tabpanel">
                                <form class="mt-2">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="form-group">
                                                <label for="location-logo">Logo de l'emplacement</label>
                                                <div class="upload-area" id="location-logo-upload">
                                                    <i data-feather="upload-cloud" class="upload-icon"></i>
                                                    <h5 class="upload-text">Déposez votre logo d'emplacement ici ou cliquez pour parcourir</h5>
                                                    <p class="text-muted small">Recommandé : PNG ou SVG, 200x100px</p>
                                                </div>
                                                <input type="file" id="location-logo-file" name="location_logo" class="d-none" accept="image/*">
                                                <img src="" id="location-logo-preview" class="image-preview">
                                                <p class="note">Votre logo d'emplacement apparaîtra en haut de la page de connexion.</p>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="form-group">
                                                <label for="background-image">Image d'arrière-plan</label>
                                                <div class="upload-area" id="background-upload">
                                                    <i data-feather="image" class="upload-icon"></i>
                                                    <h5 class="upload-text">Déposez votre image d'arrière-plan ici ou cliquez pour parcourir</h5>
                                                    <p class="text-muted small">Recommandé : JPG ou PNG, 1920x1080px</p>
                                                </div>
                                                <input type="file" id="background-file" name="background_image" class="d-none" accept="image/*">
                                                <img src="" id="background-preview" class="image-preview">
                                                <p class="note">Cette image sera affichée comme arrière-plan de la page.</p>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <h6 class="mb-1">Dégradé d'arrière-plan (Alternative à l'image)</h6>
                                            <p class="text-muted small mb-2">Créez un arrière-plan dégradé au lieu d'utiliser une image. Cela remplacera l'image d'arrière-plan si les deux sont définis.</p>
                                            <div class="row">
                                                <div class="col-md-6 col-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="gradient-start">Couleur de début du dégradé</label>
                                                        <div class="color-picker-container">
                                                            <input type="color" class="form-control form-control-color" id="gradient-start">
                                                            <div class="color-preview" id="gradient-start-preview" style="background-color: transparent;"></div>
                                                            <span class="color-value" id="gradient-start-value">Aucun</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="gradient-end">Couleur de fin du dégradé</label>
                                                        <div class="color-picker-container">
                                                            <input type="color" class="form-control form-control-color" id="gradient-end">
                                                            <div class="color-preview" id="gradient-end-preview" style="background-color: transparent;"></div>
                                                            <span class="color-value" id="gradient-end-value">Aucun</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-gradient">
                                                        <i data-feather="x" class="mr-25"></i>Effacer le dégradé
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="preset-gradient-1">
                                                        Bleu vers violet
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="preset-gradient-2">
                                                        Orange vers rose
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success ml-1" id="test-gradient">
                                                        Tester le dégradé
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 d-flex justify-content-end">
                                <button id="save-design" class="btn btn-primary">
                                    <i data-feather="save" class="mr-50"></i>Enregistrer la conception
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Column -->
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aperçu</h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    <a data-action="reload"><i data-feather="rotate-cw"></i></a>
                                </li>
                                <li>
                                    <a data-action="expand" id="expand-preview"><i data-feather="maximize"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="portal-preview">
                            <div class="preview-main">
                                <!-- Header with Location Logo -->
                                <div class="logo-container">
                                    <img src="/img/wifi-placeholder.png" alt="Logo de l'emplacement" id="preview-logo" class="preview-logo">
                                </div>
                                
                                <!-- Welcome Text -->
                                <h2 id="preview-welcome">Bienvenue sur notre WiFi</h2>
                                <p id="preview-instructions">Entrez votre adresse e-mail pour vous connecter à notre réseau WiFi</p>
                                
                                <!-- Login Form -->
                                <div class="input-container">
                                    <input type="text" class="preview-input" placeholder="Adresse e-mail">
                                    <button id="preview-button" class="preview-button">Se connecter au WiFi</button>
                                </div>
                                
                                <!-- Footer with Brand Logo and Terms -->
                                <div class="footer">
                                    <div class="brand-logo">
                                        <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Logo de marque">
                                    </div>
                                    <div class="terms" id="preview-terms-container" style="display: none; margin-bottom: 0.5rem;">
                                        <!-- Terms links will be inserted here when show_terms is enabled -->
                                    </div>
                                    <div class="terms" id="preview-powered-by">
                                        Propulsé par Monsieur WiFi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Preview Modals -->
<div class="modal fade" id="previewTermsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Conditions de service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="preview-terms-content">En accédant à ce service WiFi, vous acceptez de vous conformer à toutes les lois applicables et à la politique d'utilisation acceptable du réseau. Nous nous réservons le droit de surveiller le trafic et le contenu accessible via notre réseau, et de résilier l'accès en cas de violations de ces conditions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewPrivacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Politique de confidentialité</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="preview-privacy-content">Nous collectons des informations limitées lorsque vous utilisez notre service WiFi, y compris les identifiants d'appareils, les heures de connexion et les données d'utilisation. Ces informations sont utilisées pour améliorer notre service, résoudre les problèmes techniques et respecter les exigences légales. Nous ne vendons pas vos informations personnelles à des tiers.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDesignModal" tabindex="-1" aria-labelledby="deleteDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDesignModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette conception ? Cette action ne peut pas être annulée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Owner Modal -->
<div class="modal fade" id="changeOwnerModal" tabindex="-1" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeOwnerModalLabel">Changer le propriétaire de la conception</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="changeOwnerText">Sélectionnez un nouveau propriétaire pour cette conception de portail captif :</p>
                <div class="form-group">
                    <label for="newOwnerSelect">Nouveau propriétaire</label>
                    <select class="form-control" id="newOwnerSelect">
                        <option value="">Chargement des utilisateurs...</option>
                    </select>
                </div>
                <div class="alert alert-info mt-2">
                    <strong>Note :</strong> Cette action transférera la propriété de la conception à l'utilisateur sélectionné. Les informations du créateur original seront préservées.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmChangeOwnerBtn">Changer le propriétaire</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/captive-portals.js?v={{ time() }}"></script>
@endpush

@php
    $locale = 'fr';
@endphp
