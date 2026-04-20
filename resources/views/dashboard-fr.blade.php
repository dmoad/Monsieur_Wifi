@extends('layouts.app')

@section('title', 'Tableau de bord - Contrôleur Monsieur WiFi')

@push('styles')
<!-- Dashboard-specific CSS -->
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/pages/dashboard-ecommerce.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/charts/chart-apex.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css">

<style>
.dataTables_paginate {
    margin-top: 1rem !important;
    padding: 1rem !important;
}

.pagination {
    display: flex;
    justify-content: flex-end;
}

.page-link {
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    border: 1px solid #ddd;
    color: #7367f0;
}

.page-item.active .page-link {
    background-color: #7367f0;
    border-color: #7367f0;
    color: #fff;
}

.page-item.disabled .page-link {
    color: #b9b9c3;
    pointer-events: none;
    background-color: #fff;
    border-color: #ddd;
}

.location-card {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.location-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 5px 10px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-online {
    background-color: rgba(40, 199, 111, 0.12);
    color: #28c76f;
}

.status-offline {
    background-color: rgba(234, 84, 85, 0.12);
    color: #ea5455;
}

.status-warning {
    background-color: rgba(255, 159, 67, 0.12);
    color: #ff9f43;
}

.network-stat-icon {
    height: 45px;
    width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.marker-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.leaflet-map {
    z-index: 1;
}

.leaflet-container {
    font-family: inherit;
    font-size: inherit;
}

.leaflet-popup-content {
    margin: 0;
    padding: 0;
}

.custom-div-icon, .marker-icon {
    background: transparent;
    border: none;
}
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Tableau de bord</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Tableau de bord</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <div class="dropdown">
                <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="grid"></i></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="/fr/locations"><i class="mr-1" data-feather="plus"></i><span class="align-middle">Ajouter un emplacement</span></a>
                    <a class="dropdown-item" href="/fr/accounts"><i class="mr-1" data-feather="user-plus"></i><span class="align-middle">Ajouter un utilisateur</span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Dashboard Content Starts -->
    <section id="dashboard-analytics">
        <div class="row match-height">
            <!-- Welcome Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-congratulation-medal" id="welcome-card">
                    <div class="card-body">
                        <h5>Bienvenue sur le tableau de bord monsieur-wifi</h5>
                        <p class="card-text font-small-3">Aperçu de l'état du réseau</p>
                        <h3 class="mb-75 mt-2 pt-50">
                            <span class="text-primary" id="welcome-total-locations">Chargement...</span>
                        </h3>
                        <div class="d-flex">
                            <div class="d-flex align-items-center mr-2">
                                <i data-feather="check-circle" class="text-success font-medium-2 mr-50"></i>
                                <span class="font-weight-bold" id="welcome-active-count">-</span> Actif
                            </div>
                            <span class="mx-1">|</span>
                            <div class="d-flex align-items-center ml-1">
                                <i data-feather="x-circle" class="text-danger font-medium-2 mr-50"></i>
                                <span class="font-weight-bold" id="welcome-offline-count">-</span> Hors ligne
                            </div>
                        </div>
                        <a type="button" class="btn btn-primary mt-1" href="/fr/locations">Voir les détails</a>
                        <img src="/app-assets/images/illustration/badge.svg" class="congratulation-medal" alt="Médaille" />
                    </div>
                </div>
            </div>
            <!--/ Welcome Card -->

            <!-- Statistics Card -->
            <div class="col-lg-8 col-12">
                <div class="card card-statistics" id="network-stats">
                    <div class="card-header">
                        <h4 class="card-title">Statistiques du réseau</h4>
                        <div class="d-flex align-items-center">
                            <p class="card-text mr-25 mb-0">Mis à jour à l'instant</p>
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                <div class="media">
                                    <div class="avatar bg-light-primary mr-2">
                                        <div class="avatar-content">
                                            <i data-feather="wifi" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="font-weight-bolder mb-0" id="routers-online-count">-/-</h4>
                                        <p class="card-text font-small-3 mb-0">Routeurs en ligne</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                <div class="media">
                                    <div class="avatar bg-light-info mr-2">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="font-weight-bolder mb-0" id="active-users-count">-</h4>
                                        <p class="card-text font-small-3 mb-0">Utilisateurs actifs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <div class="media">
                                    <div class="avatar bg-light-warning mr-2">
                                        <div class="avatar-content">
                                            <i data-feather="download" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="font-weight-bolder mb-0" id="data-used-count">-</h4>
                                        <p class="card-text font-small-3 mb-0">Données utilisées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="media">
                                    <div class="avatar bg-light-success mr-2">
                                        <div class="avatar-content">
                                            <i data-feather="activity" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="font-weight-bolder mb-0" id="uptime-percentage">-%</h4>
                                        <p class="card-text font-small-3 mb-0">Temps de fonctionnement</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics Card -->
        </div>

        <div class="row match-height">
            <!-- Network Map -->
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Carte du réseau</h4>
                        <div class="d-flex">
                            <button id="fullscreen-btn" class="btn btn-sm btn-outline-primary mr-1">
                                <i data-feather="maximize"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="network-map" style="height: 400px;">
                            <div class="d-flex align-items-center justify-content-center h-100" id="map-loading">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="sr-only">Chargement...</span>
                                    </div>
                                    <p class="text-muted">Chargement de la carte du réseau...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Network Map -->

            <!-- Data Usage Trends -->
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Tendances d'utilisation des données</h4>
                        <div class="dropdown chart-dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dataUsageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                7 derniers jours
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dataUsageDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">7 derniers jours</a>
                                <a class="dropdown-item" href="javascript:void(0);">Mois dernier</a>
                                <a class="dropdown-item" href="javascript:void(0);">Année dernière</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 d-flex flex-column flex-wrap text-center mb-2">
                                <h1 class="font-weight-bolder mt-2 mb-0" id="total-bandwidth-used">-</h1>
                                <p class="card-text">Utilisation totale cette semaine</p>
                            </div>
                        </div>
                        <div id="data-usage-chart" class="mt-2" style="min-height: 270px;"></div>
                        
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light-info mr-1 p-50">
                                        <div class="avatar-content">
                                            <i data-feather="download" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-weight-bolder mb-0" id="download-usage">185 GB</h4>
                                        <p class="card-text font-small-3 mb-0">Téléchargement</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light-warning mr-1 p-50">
                                        <div class="avatar-content">
                                            <i data-feather="upload" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-weight-bolder mb-0" id="upload-usage">60 GB</h4>
                                        <p class="card-text font-small-3 mb-0">Upload</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Data Usage Trends -->
        </div>

        <div id="dashboard-errors"></div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aperçu des emplacements</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="locationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Tous les emplacements
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="locationDropdown">
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="all">Tous les emplacements</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="online">En ligne seulement</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="offline">Hors ligne seulement</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="locations-container">
                            <!-- Location cards will be dynamically populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network Analytics Overview -->
        <div class="row" style="width: 100%; margin: 0; padding: 0;">
            <div class="col-12" style="width: 100%; max-width: 100%; flex: 0 0 100%;">
                <div class="card" id="analytics-section" style="width: 100% !important; max-width: 100% !important;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Aperçu des analyses du réseau</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="analyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                7 derniers jours
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analyticsDropdown">
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="1">Aujourd'hui</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="7">7 derniers jours</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="30">30 derniers jours</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="90">90 derniers jours</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="analytics-errors"></div>
                        
                        <div class="row mb-2">
                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-light-primary p-50 mr-1">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-total-users">-</h4>
                                        <span>Total utilisateurs</span>
                                    </div>
                                </div>
                                <span class="text-muted">Utilisateurs uniques connectés</span>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-light-info p-50 mr-1">
                                        <div class="avatar-content">
                                            <i data-feather="activity" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-data-usage">- GB</h4>
                                        <span>Utilisation des données</span>
                                    </div>
                                </div>
                                <span class="text-muted">Bande passante totale consommée</span>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-light-success p-50 mr-1">
                                        <div class="avatar-content">
                                            <i data-feather="wifi" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-uptime">-%</h4>
                                        <span>Temps de fonctionnement</span>
                                    </div>
                                </div>
                                <span class="text-muted">Disponibilité du réseau</span>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-light-warning p-50 mr-1">
                                        <div class="avatar-content">
                                            <i data-feather="monitor" class="font-medium-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-sessions">-</h4>
                                        <span>Total sessions</span>
                                    </div>
                                </div>
                                <span class="text-muted">Sessions de connexion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Content Ends -->
</div>
@endsection

@push('scripts')
<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- DataTables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
<script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>

<!-- Charts -->
<script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
<script src="/app-assets/js/scripts/pages/dashboard-ecommerce.js"></script>

<!-- Dashboard JS -->
<script src="/assets/js/dashboard-fr.js?v=11"></script>

<script>
$(window).on('load', function() {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }

    // Initialize small charts
    var usersOptions = {
        chart: {
            type: 'line',
            height: 40,
            sparkline: { enabled: true },
            toolbar: { show: false }
        },
        colors: ['#7367F0'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Utilisateurs',
            data: [2100, 2300, 2500, 2700, 2600, 2800, 2856]
        }],
        tooltip: { fixed: { enabled: false } }
    };

    var devicesOptions = {
        chart: {
            type: 'line',
            height: 40,
            sparkline: { enabled: true },
            toolbar: { show: false }
        },
        colors: ['#28C76F'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Appareils',
            data: [42, 43, 45, 44, 45, 43, 45]
        }],
        tooltip: { fixed: { enabled: false } }
    };

    var bandwidthOptions = {
        chart: {
            type: 'line',
            height: 40,
            sparkline: { enabled: true },
            toolbar: { show: false }
        },
        colors: ['#FF9F43'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Bande passante',
            data: [1.8, 2.0, 2.2, 2.3, 2.1, 2.4, 2.4]
        }],
        tooltip: { fixed: { enabled: false } }
    };

    var alertsOptions = {
        chart: {
            type: 'line',
            height: 40,
            sparkline: { enabled: true },
            toolbar: { show: false }
        },
        colors: ['#EA5455'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Alertes',
            data: [5, 4, 3, 5, 4, 3, 3]
        }],
        tooltip: { fixed: { enabled: false } }
    };

    // Initialize charts
    if (document.querySelector('#users-online-chart')) {
        new ApexCharts(document.querySelector('#users-online-chart'), usersOptions).render();
    }
    if (document.querySelector('#devices-online-chart')) {
        new ApexCharts(document.querySelector('#devices-online-chart'), devicesOptions).render();
    }
    if (document.querySelector('#bandwidth-chart')) {
        new ApexCharts(document.querySelector('#bandwidth-chart'), bandwidthOptions).render();
    }
    if (document.querySelector('#alerts-chart')) {
        new ApexCharts(document.querySelector('#alerts-chart'), alertsOptions).render();
    }
});

// Fullscreen button functionality
$(document).ready(function() {
    if (document.getElementById('fullscreen-btn')) {
        document.getElementById('fullscreen-btn').addEventListener('click', function() {
            var mapElement = document.getElementById('network-map');
            
            if (!document.fullscreenElement) {
                if (mapElement.requestFullscreen) {
                    mapElement.requestFullscreen();
                } else if (mapElement.mozRequestFullScreen) {
                    mapElement.mozRequestFullScreen();
                } else if (mapElement.webkitRequestFullscreen) {
                    mapElement.webkitRequestFullscreen();
                } else if (mapElement.msRequestFullscreen) {
                    mapElement.msRequestFullscreen();
                }
                this.innerHTML = '<i data-feather="minimize-2"></i> Quitter plein écran';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                this.innerHTML = '<i data-feather="maximize-2"></i> Plein écran';
            }
            
            setTimeout(function() {
                if (typeof feather !== 'undefined') {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            }, 100);
        });
    }
});
</script>
@endpush

@php
    $locale = 'fr';
@endphp
