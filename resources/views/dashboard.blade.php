@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('dashboard.page_title'))

@push('styles')
<!-- Dashboard-specific CSS -->
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
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
.location-card {
    border-radius: var(--mw-radius-lg);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.location-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--mw-shadow-elevated);
}

.marker-icon { display: flex; align-items: center; justify-content: center; }
.leaflet-map { z-index: 1; }
.leaflet-container { font-family: inherit; font-size: inherit; }
.leaflet-popup-content { margin: 0; padding: 0; }
.custom-div-icon, .marker-icon { background: transparent; border: none; }

#locations-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--mw-space-md);
}
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('dashboard.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('dashboard.heading') }}</li>
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
                    <a class="dropdown-item" href="/{{ $locale }}/locations"><i class="mr-1" data-feather="plus"></i><span class="align-middle">{{ __('dashboard.add_location') }}</span></a>
                    <a class="dropdown-item" href="/{{ $locale }}/accounts"><i class="mr-1" data-feather="user-plus"></i><span class="align-middle">{{ __('dashboard.add_user') }}</span></a>
                    <a class="dropdown-item" href="/{{ $locale }}/analytics"><i class="mr-1" data-feather="bar-chart-2"></i><span class="align-middle">{{ __('dashboard.reports') }}</span></a>
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
                <div class="card" id="welcome-card">
                    <div class="card-body">
                        <h5>{{ __('dashboard.welcome_title') }}</h5>
                        <p class="card-text" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.status_overview') }}</p>
                        <h3 class="mb-75 mt-2 pt-50">
                            <span id="welcome-total-locations" style="color:var(--mw-primary);">{{ __('common.loading') }}</span>
                        </h3>
                        <div class="d-flex">
                            <div class="d-flex align-items-center mr-2">
                                <i data-feather="check-circle" class="text-success font-medium-2 mr-50"></i>
                                <span class="font-weight-bold" id="welcome-active-count">-</span>&nbsp;{{ __('common.active') }}
                            </div>
                            <span class="mx-1">|</span>
                            <div class="d-flex align-items-center ml-1">
                                <i data-feather="x-circle" class="text-danger font-medium-2 mr-50"></i>
                                <span class="font-weight-bold" id="welcome-offline-count">-</span>&nbsp;{{ __('common.offline') }}
                            </div>
                        </div>
                        <a type="button" class="btn btn-primary mt-1" href="/{{ $locale }}/locations">{{ __('dashboard.view_details') }}</a>
                    </div>
                </div>
            </div>
            <!--/ Welcome Card -->

            <!-- Statistics Card -->
            <div class="col-lg-8 col-12">
                <div class="card card-statistics" id="network-stats">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.network_statistics') }}</h4>
                        <div class="d-flex align-items-center">
                            <p class="card-text mr-25 mb-0">{{ __('dashboard.updated_just_now') }}</p>
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                <div class="media">
                                    <div class="mw-stat-icon mw-stat-icon-primary mr-2">
                                        <i data-feather="wifi"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="mb-0" id="routers-online-count">-/-</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.routers_online') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                                <div class="media">
                                    <div class="mw-stat-icon mw-stat-icon-info mr-2">
                                        <i data-feather="users"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="mb-0" id="active-users-count">-</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.active_users') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <div class="media">
                                    <div class="mw-stat-icon mw-stat-icon-warning mr-2">
                                        <i data-feather="download"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="mb-0" id="data-used-count">-</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.data_used') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="media">
                                    <div class="mw-stat-icon mw-stat-icon-success mr-2">
                                        <i data-feather="activity"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <h4 class="mb-0" id="uptime-percentage">-%</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.uptime') }}</p>
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
                        <h4 class="card-title">{{ __('dashboard.network_map') }}</h4>
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
                                        <span class="sr-only">{{ __('common.loading') }}</span>
                                    </div>
                                    <p class="text-muted">{{ __('dashboard.loading_network_map') }}</p>
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
                        <h4 class="card-title">{{ __('dashboard.data_usage_trends') }}</h4>
                        <div class="dropdown chart-dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dataUsageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('dashboard.last_7_days') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dataUsageDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">{{ __('dashboard.last_7_days') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);">{{ __('dashboard.last_month') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);">{{ __('dashboard.last_year') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 d-flex flex-column flex-wrap text-center mb-2">
                                <h1 class="mt-2 mb-0" id="total-bandwidth-used">-</h1>
                                <p class="card-text">{{ __('dashboard.total_usage_this_week') }}</p>
                            </div>
                        </div>
                        <div id="data-usage-chart" class="mt-2" style="min-height: 270px;"></div>
                        
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="mw-stat-icon mw-stat-icon-info mr-1">
                                        <i data-feather="download"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="download-usage">185 GB</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.download') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="mw-stat-icon mw-stat-icon-warning mr-1">
                                        <i data-feather="upload"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="upload-usage">60 GB</h4>
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-muted);">{{ __('dashboard.upload') }}</p>
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
                        <h4 class="card-title">{{ __('dashboard.locations_overview') }}</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="locationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('dashboard.all_locations') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="locationDropdown">
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="all">{{ __('dashboard.all_locations') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="online">{{ __('dashboard.online_only') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-location-filter="offline">{{ __('dashboard.offline_only') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="locations-container">
                            <!-- Location cards populated by dashboard.js -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network Analytics Overview -->
        <div class="row">
            <div class="col-12">
                <div class="card" id="analytics-section">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ __('dashboard.analytics_overview') }}</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="analyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('dashboard.last_7_days') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analyticsDropdown">
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="1">{{ __('dashboard.today') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="7">{{ __('dashboard.last_7_days') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="30">{{ __('dashboard.last_30_days') }}</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-analytics-period="90">{{ __('dashboard.last_90_days') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="analytics-errors"></div>
                        
                        <div class="row mb-2">
                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="mw-stat-icon mw-stat-icon-primary mr-1">
                                        <i data-feather="users"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-total-users">-</h4>
                                        <span>{{ __('dashboard.total_users') }}</span>
                                    </div>
                                </div>
                                <span class="text-muted">{{ __('dashboard.unique_users_connected') }}</span>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="mw-stat-icon mw-stat-icon-info mr-1">
                                        <i data-feather="activity"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-data-usage">- GB</h4>
                                        <span>{{ __('dashboard.data_usage') }}</span>
                                    </div>
                                </div>
                                <span class="text-muted">{{ __('dashboard.total_bandwidth_consumed') }}</span>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="mw-stat-icon mw-stat-icon-success mr-1">
                                        <i data-feather="wifi"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-uptime">-%</h4>
                                        <span>{{ __('dashboard.uptime') }}</span>
                                    </div>
                                </div>
                                <span class="text-muted">{{ __('dashboard.network_availability') }}</span>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12 mb-2 mb-xl-0">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="mw-stat-icon mw-stat-icon-warning mr-1">
                                        <i data-feather="monitor"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0" id="analytics-sessions">-</h4>
                                        <span>{{ __('dashboard.total_sessions') }}</span>
                                    </div>
                                </div>
                                <span class="text-muted">{{ __('dashboard.connection_sessions') }}</span>
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

<!-- Dashboard JS -->
<script src="/assets/js/dashboard.js?v={{ filemtime(public_path('assets/js/dashboard.js')) }}"></script>

<script>
$(document).ready(function() {
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            const mapElement = document.getElementById('network-map');
            if (!document.fullscreenElement) {
                (mapElement.requestFullscreen || mapElement.mozRequestFullScreen ||
                 mapElement.webkitRequestFullscreen || mapElement.msRequestFullscreen).call(mapElement);
                this.innerHTML = '<i data-feather="minimize-2"></i> {{ __('dashboard.exit_full_screen') }}';
            } else {
                (document.exitFullscreen || document.mozCancelFullScreen ||
                 document.webkitExitFullscreen || document.msExitFullscreen).call(document);
                this.innerHTML = '<i data-feather="maximize-2"></i> {{ __('dashboard.full_screen') }}';
            }
            setTimeout(function() {
                if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
            }, 100);
        });
    }
});
</script>
@endpush

