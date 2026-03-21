@extends('layouts.app')

@section('title', 'Blocage de domaines - Monsieur WiFi')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">

<style>
    .avatar-content svg {
        color: inherit;
        width: 24px !important;
        height: 24px !important;
        stroke-width: 2;
        display: block !important;
    }
    
    [data-feather] {
        display: inline-block !important;
        vertical-align: middle;
    }

    .badge-category-adult {
        background-color: rgba(234, 84, 85, 0.12);
        color: #ea5455;
    }
    .badge-category-gambling {
        background-color: rgba(255, 159, 67, 0.12);
        color: #ff9f43;
    }
    .badge-category-malware {
        background-color: rgba(130, 28, 128, 0.12);
        color: #821c80;
    }
    .badge-category-social {
        background-color: rgba(0, 137, 255, 0.12);
        color: #0089ff;
    }
    .badge-category-streaming {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
    }
    .badge-category-custom {
        background-color: rgba(45, 45, 45, 0.12);
        color: #2d2d2d;
    }

    .cursor-pointer {
        cursor: pointer;
    }

</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Blocage de domaines</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Blocage de domaines</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#domain-blocking-info">
                <i data-feather="info" class="mr-25"></i>
                <span>Info</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Blocking Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Catégories de blocage</h4>
                    <p class="card-text">Activez ou désactivez les catégories pour activer ou désactiver le blocage de domaines par catégorie.</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-danger p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="octagon"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Contenu adulte</h4>
                                                <span>1,024 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-adult" checked>
                                        <label class="custom-control-label" for="category-adult"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-warning p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Jeux d'argent</h4>
                                                <span>856 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-gambling" checked>
                                        <label class="custom-control-label" for="category-gambling"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="shield-off"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Logiciels malveillants</h4>
                                                <span>2,345 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-malware" checked>
                                        <label class="custom-control-label" for="category-malware"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-info p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="users"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Réseaux sociaux</h4>
                                                <span>342 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-social">
                                        <label class="custom-control-label" for="category-social"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-success p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="film"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Streaming</h4>
                                                <span>128 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-streaming">
                                        <label class="custom-control-label" for="category-streaming"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card cursor-pointer border shadow-none">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-secondary p-50 mr-1">
                                                <div class="avatar-content">
                                                    <i data-feather="tag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-0">Liste personnalisée</h4>
                                                <span>43 domaines</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-custom" checked>
                                        <label class="custom-control-label" for="category-custom"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain List Table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Domaines bloqués</h4>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-domain">
                                <i data-feather="plus" class="mr-25"></i>
                                <span>Ajouter un domaine</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-domains table">
                                <thead>
                                    <tr>
                                        <th>Domaine</th>
                                        <th>Catégorie</th>
                                        <th>Date d'ajout</th>
                                        <th>Dernière mise à jour</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add New Domain Modal -->
<div class="modal fade text-left" id="add-new-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel34">Ajouter un nouveau domaine</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="domain-name">Domaine</label>
                        <input type="text" class="form-control" id="domain-name" placeholder="exemple.com" />
                        <small class="form-text text-muted">Entrez un domaine sans http:// ou https://</small>
                    </div>
                    <div class="form-group">
                        <label for="domain-category">Catégorie</label>
                        <select class="form-control" id="domain-category">
                            <option value="1">Contenu adulte</option>
                            <option value="2">Jeux d'argent</option>
                            <option value="3">Logiciels malveillants</option>
                            <option value="4">Réseaux sociaux</option>
                            <option value="5">Streaming</option>
                            <option value="6">Liste personnalisée</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="domain-notes">Notes</label>
                        <textarea class="form-control" id="domain-notes" rows="3" placeholder="Entrez des notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter le domaine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Domain Modal -->
<div class="modal fade text-left" id="edit-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel35">Modifier le domaine</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-domain-name">Domaine</label>
                        <input type="text" class="form-control" id="edit-domain-name" readonly />
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-category">Catégorie</label>
                        <select class="form-control" id="edit-domain-category">
                            <option value="1">Contenu adulte</option>
                            <option value="2">Jeux d'argent</option>
                            <option value="3">Logiciels malveillants</option>
                            <option value="4">Réseaux sociaux</option>
                            <option value="5">Streaming</option>
                            <option value="6">Liste personnalisée</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-notes">Notes</label>
                        <textarea class="form-control" id="edit-domain-notes" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="edit-block-subdomains" checked>
                            <label class="custom-control-label" for="edit-block-subdomains">Bloquer tous les sous-domaines</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Domain Blocking Info Modal -->
<div class="modal fade text-left" id="domain-blocking-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel37" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel37">
                    <i data-feather="info" class="mr-1"></i>
                    Comment fonctionne le blocage de domaines
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-none border-left-primary">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="shield" class="mr-1"></i>
                                    Qu'est-ce que le blocage de domaines ?
                                </h5>
                                <p class="card-text">
                                    Le blocage de domaines empêche les utilisateurs de votre réseau d'accéder à des sites web spécifiques en bloquant leurs noms de domaine. 
                                    Lorsqu'un utilisateur tente de visiter un domaine bloqué, la demande est interceptée et refusée, protégeant votre réseau contre 
                                    le contenu indésirable, les menaces de sécurité ou les distractions de productivité.
                                </p>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-info mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="plus-circle" class="mr-1"></i>
                                    Comment ajouter des domaines
                                </h5>
                                <ol class="mb-0">
                                    <li><strong>Domaine unique :</strong> Cliquez sur le bouton "Ajouter un domaine" pour ajouter des sites web individuels</li>
                                    <li><strong>Catégories :</strong> Organisez les domaines en catégories prédéfinies pour une meilleure gestion</li>
                                </ol>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-warning mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="alert-triangle" class="mr-1"></i>
                                    Pourquoi plusieurs domaines sont nécessaires
                                </h5>
                                <p class="card-text">
                                    De nombreux sites web utilisent plusieurs domaines pour distribuer le contenu, éviter le blocage ou améliorer les performances. 
                                    Pour bloquer efficacement un service, vous devez souvent bloquer plusieurs domaines associés :
                                </p>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Domaines à bloquer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Facebook</strong></td>
                                                <td>facebook.com, fb.com, fbcdn.net, fb.me, messenger.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>YouTube</strong></td>
                                                <td>youtube.com, youtu.be, ytimg.com, googlevideo.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Instagram</strong></td>
                                                <td>instagram.com, cdninstagram.com, ig.me</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Twitter/X</strong></td>
                                                <td>twitter.com, x.com, t.co, twimg.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>TikTok</strong></td>
                                                <td>tiktok.com, tiktokv.com, tiktokcdn.com, musical.ly</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-success mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="check-circle" class="mr-1"></i>
                                    Meilleures pratiques
                                </h5>
                                <ul class="mb-0">
                                    <li><strong>Utiliser les catégories :</strong> Regroupez les domaines associés pour une gestion plus facile</li>
                                    <li><strong>Recherchez minutieusement :</strong> Recherchez tous les domaines utilisés par un service avant de bloquer</li>
                                    <li><strong>Tester le blocage :</strong> Vérifiez que le blocage fonctionne comme prévu</li>
                                    <li><strong>Mises à jour régulières :</strong> Maintenez vos listes de blocage à jour car les services changent de domaines</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <div class="alert-body">
                                <i data-feather="zap" class="mr-1"></i>
                                <strong>Astuce :</strong> Utilisez les outils de développement du navigateur (F12) pour inspecter les requêtes réseau et identifier 
                                tous les domaines utilisés par un site web. Cela permet d'assurer un blocage complet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Compris !</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/domain-blocking.js?v={{ time() }}"></script>
@endpush

@php
    $locale = 'fr';
@endphp
