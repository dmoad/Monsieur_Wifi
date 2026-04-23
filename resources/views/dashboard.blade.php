@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('dashboard.page_title'))

@push('styles')
<!-- Dashboard-specific CSS -->
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/charts/apexcharts.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/maps/leaflet.min.css">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css">

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

/* Top-row summary cards — larger stacked layout (icon top-left, value + label below) */
.db-summary-card {
    display: flex;
    flex-direction: column;
    gap: var(--mw-space-lg);
    padding: var(--mw-space-xl);
    min-height: 150px;
}
.db-summary-card .mw-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
}
.db-summary-card .mw-stat-icon [data-feather] {
    width: 22px !important;
    height: 22px !important;
}
.db-summary-num {
    font-size: 28px;
    font-weight: 700;
    color: var(--mw-text-primary);
    line-height: 1.1;
    margin-bottom: 4px;
}
.db-summary-suffix {
    font-size: 16px;
    font-weight: 500;
    color: var(--mw-text-muted);
    margin-left: 4px;
}
.db-summary-lbl {
    font-size: 13px;
    color: var(--mw-text-muted);
}

/* Right-column analytics list (colored icon + label/sub + value) */
#analytics-section .card-body { display: flex; flex-direction: column; }
.db-metric-list {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1;
    gap: var(--mw-space-sm);
}
.db-metric-row {
    display: flex;
    align-items: center;
    gap: var(--mw-space-md);
    padding: var(--mw-space-md) 0;
    border-bottom: 1px solid var(--mw-border-light);
}
.db-metric-row:last-child { border-bottom: none; padding-bottom: 0; }
.db-metric-row:first-child { padding-top: 0; }
.db-metric-row .mw-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    flex-shrink: 0;
}
.db-metric-row .mw-stat-icon [data-feather] { width: 18px !important; height: 18px !important; }
.db-metric-body { flex: 1; min-width: 0; }
.db-metric-name { font-size: 13px; font-weight: 600; color: var(--mw-text-primary); }
.db-metric-sub  { font-size: 11px; color: var(--mw-text-muted); margin-top: 2px; }
.db-metric-val  { font-size: 20px; font-weight: 700; color: var(--mw-text-primary); flex-shrink: 0; text-align: right; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="col-12 mb-2">
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
</div>

<div class="content-body">
    <!-- Dashboard Content Starts -->
    <section id="dashboard-analytics">
        <!-- Network statistics summary cards -->
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="db-summary-card">
                        <div class="mw-stat-icon mw-stat-icon-primary">
                            <i data-feather="wifi"></i>
                        </div>
                        <div class="mt-auto">
                            <div class="db-summary-num" id="routers-online-count">—</div>
                            <div class="db-summary-lbl">{{ __('dashboard.routers_online') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="db-summary-card">
                        <div class="mw-stat-icon mw-stat-icon-info">
                            <i data-feather="users"></i>
                        </div>
                        <div class="mt-auto">
                            <div class="db-summary-num" id="active-users-count">—</div>
                            <div class="db-summary-lbl">{{ __('dashboard.active_users') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="db-summary-card">
                        <div class="mw-stat-icon mw-stat-icon-warning">
                            <i data-feather="download"></i>
                        </div>
                        <div class="mt-auto">
                            <div class="db-summary-num" id="data-used-count">—</div>
                            <div class="db-summary-lbl">{{ __('dashboard.data_used') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="db-summary-card">
                        <div class="mw-stat-icon mw-stat-icon-success">
                            <i data-feather="activity"></i>
                        </div>
                        <div class="mt-auto">
                            <div class="db-summary-num" id="uptime-percentage">—%</div>
                            <div class="db-summary-lbl">{{ __('dashboard.uptime') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="dashboard-errors"></div>

        <!-- Data Usage Trends + Traffic by Location donut -->
        <div class="row match-height">
            <div class="col-lg-8 col-12">
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
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-secondary);">{{ __('dashboard.download') }}</p>
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
                                        <p class="card-text mb-0" style="font-size:12px;color:var(--mw-text-secondary);">{{ __('dashboard.upload') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card h-100" id="analytics-section">
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
                        <div class="db-metric-list">
                            <div class="db-metric-row">
                                <div class="mw-stat-icon mw-stat-icon-primary"><i data-feather="users"></i></div>
                                <div class="db-metric-body">
                                    <div class="db-metric-name">{{ __('dashboard.total_users') }}</div>
                                    <div class="db-metric-sub">{{ __('dashboard.unique_users_connected') }}</div>
                                </div>
                                <div class="db-metric-val" id="analytics-total-users">—</div>
                            </div>
                            <div class="db-metric-row">
                                <div class="mw-stat-icon mw-stat-icon-info"><i data-feather="activity"></i></div>
                                <div class="db-metric-body">
                                    <div class="db-metric-name">{{ __('dashboard.data_usage') }}</div>
                                    <div class="db-metric-sub">{{ __('dashboard.total_bandwidth_consumed') }}</div>
                                </div>
                                <div class="db-metric-val" id="analytics-data-usage">—</div>
                            </div>
                            <div class="db-metric-row">
                                <div class="mw-stat-icon mw-stat-icon-success"><i data-feather="wifi"></i></div>
                                <div class="db-metric-body">
                                    <div class="db-metric-name">{{ __('dashboard.uptime') }}</div>
                                    <div class="db-metric-sub">{{ __('dashboard.network_availability') }}</div>
                                </div>
                                <div class="db-metric-val" id="analytics-uptime">—</div>
                            </div>
                            <div class="db-metric-row">
                                <div class="mw-stat-icon mw-stat-icon-warning"><i data-feather="monitor"></i></div>
                                <div class="db-metric-body">
                                    <div class="db-metric-name">{{ __('dashboard.total_sessions') }}</div>
                                    <div class="db-metric-sub">{{ __('dashboard.connection_sessions') }}</div>
                                </div>
                                <div class="db-metric-val" id="analytics-sessions">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Data Usage Trends + Analytics list -->

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

        <!-- Network Map -->
        <div class="row">
            <div class="col-12">
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
<script src="/assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/assets/vendors/js/maps/leaflet.min.js"></script>

<!-- Charts -->
<script src="/assets/vendors/js/charts/apexcharts.min.js"></script>

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

