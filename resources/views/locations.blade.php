@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('locations.page_title'))

@push('styles')
<style>
/* Location card layout */
.location-card { margin-bottom: var(--mw-space-md); }

.lc-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: var(--mw-space-lg) var(--mw-space-xl);
    gap: var(--mw-space-lg);
}
.lc-info { flex: 1; min-width: 0; }
.lc-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--mw-text-primary);
    margin-bottom: 4px;
}
.lc-meta {
    display: flex;
    align-items: center;
    gap: var(--mw-space-lg);
    flex-wrap: wrap;
}
.lc-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: var(--mw-text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 340px;
}
.lc-meta-item [data-feather] { width: 13px !important; height: 13px !important; }

.lc-head-right {
    display: flex;
    align-items: center;
    gap: var(--mw-space-sm);
    flex-shrink: 0;
}

/* Status pill — soft tint + leading dot (matches zone-details Online chip) */
.lc-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px 3px 8px;
    border-radius: var(--mw-radius-full);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.2px;
    line-height: 1.2;
}
.lc-status::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}
.lc-status-online {
    background: rgba(22, 163, 74, 0.12);
    color: var(--mw-success);
}
.lc-status-offline {
    background: rgba(220, 38, 38, 0.1);
    color: var(--mw-danger);
}

/* Kebab menu */
.lc-kebab-wrap { position: relative; }
.lc-kebab-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--mw-border);
    background: var(--mw-bg-surface);
    border-radius: var(--mw-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--mw-text-secondary);
    cursor: pointer;
    transition: background 0.12s, color 0.12s, border-color 0.12s;
    padding: 0;
}
.lc-kebab-btn:hover {
    background: var(--mw-primary-tint);
    border-color: var(--mw-primary);
    color: var(--mw-primary);
}
.lc-menu {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    background: var(--mw-bg-surface);
    border: 1px solid var(--mw-border);
    border-radius: var(--mw-radius-md);
    box-shadow: var(--mw-shadow-elevated);
    min-width: 140px;
    z-index: 100;
    padding: 4px 0;
    animation: mw-fade-in 0.1s ease;
}
.lc-menu.open { display: block; }
.lc-menu-item {
    display: flex;
    align-items: center;
    gap: var(--mw-space-sm);
    width: 100%;
    padding: 7px 14px;
    border: none;
    background: transparent;
    font-size: 13px;
    color: var(--mw-text-secondary);
    cursor: pointer;
    text-align: left;
    transition: background 0.1s, color 0.1s;
    font-family: var(--mw-font);
}
.lc-menu-item:hover { background: var(--mw-bg-hover); color: var(--mw-text-primary); }
.lc-menu-danger { color: var(--mw-danger) !important; }
.lc-menu-danger:hover { background: rgba(220,38,38,0.06) !important; }
.lc-menu-divider { height: 1px; background: var(--mw-border-light); margin: 3px 0; }

/* Stat row */
.lc-stats {
    display: flex;
    align-items: stretch;
    border-top: 1px solid var(--mw-border-light);
}
.lc-stat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--mw-space-md) var(--mw-space-lg);
    gap: 3px;
}
.lc-stat-divider {
    width: 1px;
    background: var(--mw-border-light);
    flex-shrink: 0;
}
.lc-stat-val {
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: -0.3px;
}
.lc-stat-val [data-feather] { width: 17px !important; height: 17px !important; }
.lc-stat-lbl { font-size: 11px; color: var(--mw-text-muted); }
.lc-p { color: var(--mw-primary); }
.lc-i { color: var(--mw-info); }

/* Summary stat cards (top row) */
.lc-summary-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--mw-space-lg) var(--mw-space-xl);
}
.lc-summary-num {
    font-size: 22px;
    font-weight: 700;
    color: var(--mw-text-primary);
    line-height: 1.1;
    margin-bottom: 2px;
}
.lc-summary-lbl {
    font-size: 12px;
    color: var(--mw-text-muted);
}

/* List-header bar (title + filter controls on one row) */
.lc-filter-card {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: var(--mw-space-md);
    padding: var(--mw-space-md) var(--mw-space-lg);
    flex-wrap: wrap;
    margin-bottom: var(--mw-space-md);
}
.lc-list-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--mw-text-primary);
}
.lc-filter-controls {
    display: flex;
    gap: var(--mw-space-sm);
    align-items: center;
    flex-wrap: wrap;
}
.lc-filter-controls #status-filter { width: 150px; }
.lc-filter-controls #search-locations { width: 220px; }

/* Empty state */
.lc-empty {
    text-align: center;
    padding: 3rem 2rem;
}
.lc-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    background: var(--mw-primary-tint);
    color: var(--mw-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--mw-bg-surface);
    border-radius: var(--mw-radius-md);
    box-shadow: var(--mw-shadow-card);
}
.pagination-info { color: var(--mw-text-muted); font-size: 0.9rem; }
.pagination-buttons { display: flex; gap: 0.5rem; align-items: center; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('locations.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('locations.heading') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add-location-modal">
                <i data-feather="plus" class="mr-50"></i>
                <span>{{ __('locations.add_location') }}</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Summary cards -->
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="lc-summary-card">
                    <div>
                        <div class="lc-summary-num" id="total-locations">—</div>
                        <div class="lc-summary-lbl">{{ __('locations.total_locations') }}</div>
                    </div>
                    <div class="mw-stat-icon mw-stat-icon-primary">
                        <i data-feather="map-pin"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="lc-summary-card">
                    <div>
                        <div class="lc-summary-num" id="online-locations">—</div>
                        <div class="lc-summary-lbl">{{ __('locations.online_locations') }}</div>
                    </div>
                    <div class="mw-stat-icon mw-stat-icon-success">
                        <i data-feather="check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="lc-summary-card">
                    <div>
                        <div class="lc-summary-num" id="total-users">—</div>
                        <div class="lc-summary-lbl">{{ __('locations.total_users') }}</div>
                    </div>
                    <div class="mw-stat-icon mw-stat-icon-info">
                        <i data-feather="users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="lc-summary-card">
                    <div>
                        <div class="lc-summary-num" id="total-data">—</div>
                        <div class="lc-summary-lbl">{{ __('locations.total_data_usage') }}</div>
                    </div>
                    <div class="mw-stat-icon mw-stat-icon-warning">
                        <i data-feather="download"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List header: title + filter controls -->
    <div class="card lc-filter-card">
        <div class="lc-list-title">{{ __('locations.locations_list') }}</div>
        <div class="lc-filter-controls">
            <div class="per-page-selector d-flex align-items-center" style="gap: 8px;">
                <label for="items-per-page" class="mb-0 text-muted" style="font-size: 13px;">{{ __('locations.items_per_page') }}</label>
                <select id="items-per-page" class="form-control form-control-sm" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <select class="form-control form-control-sm" id="status-filter">
                <option value="">{{ __('locations.all_status') }}</option>
                <option value="online">{{ __('locations.status_online') }}</option>
                <option value="offline">{{ __('locations.status_offline') }}</option>
            </select>
            <input type="text" class="form-control form-control-sm" id="search-locations" placeholder="{{ __('locations.search_placeholder') }}">
        </div>
    </div>

    <!-- Loading / list / pagination -->
    <div id="locations-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('common.loading') }}</span>
        </div>
    </div>

    <div id="locations-list"></div>

    <div id="pagination-container"></div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="add-location-modal" tabindex="-1" role="dialog" aria-labelledby="add-location-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-location-title">{{ __('locations.add_new_location') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-location-form">
                    <div class="form-group" id="owner-select-group" style="display: none;">
                        <label for="owner-select">{{ __('locations.owner_label') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="owner-select">
                            <option value="">{{ __('locations.loading_users') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('locations.owner_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="location-name">{{ __('locations.location_name_label') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location-name" placeholder="{{ __('locations.location_name_placeholder') }}">
                    </div>
                    <div class="form-group">
                        <label for="location-address">{{ __('locations.address_label') }}</label>
                        <input type="text" class="form-control" id="location-address" placeholder="{{ __('locations.address_placeholder') }}">
                    </div>
                    <div class="form-group">
                        <label for="device-select">{{ __('locations.select_device_label') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="device-select" required>
                            <option value="">{{ __('locations.select_device_placeholder') }}</option>
                        </select>
                        <small class="form-text text-muted" id="device-select-hint">{{ __('locations.select_device_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="location-notes">{{ __('locations.description_label') }}</label>
                        <textarea class="form-control" id="location-notes" rows="3" placeholder="{{ __('locations.description_placeholder') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="add-location-btn">{{ __('locations.add_location') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $locationsT = [
        'status_online' => __('locations.status_online'),
        'status_offline' => __('locations.status_offline'),
        'col_users' => __('locations.col_users'),
        'col_data_usage' => __('locations.col_data_usage'),
        'action_view' => __('locations.action_view'),
        'action_delete' => __('locations.action_delete'),
        'actions' => __('locations.actions'),
        'empty_title' => __('locations.empty_title'),
        'empty_desc' => __('locations.empty_desc'),
        'error_loading' => __('locations.error_loading'),
        'confirm_delete' => __('locations.confirm_delete'),
        'location_deleted' => __('locations.location_deleted'),
        'error_deleting' => __('locations.error_deleting'),
        'unit_tb' => __('locations.unit_tb'),
        'unit_gb' => __('locations.unit_gb'),
        'add_location' => __('locations.add_location'),
        'adding_location' => __('locations.adding_location'),
        'error_creating_location' => __('locations.error_creating_location'),
        'location_created' => __('locations.location_created'),
        'assigned_firmware_prefix' => __('locations.assigned_firmware_prefix'),
        'location_name_required' => __('locations.location_name_required'),
        'device_required' => __('locations.device_required'),
        'select_owner_first_option' => __('locations.select_owner_first_option'),
        'select_owner_above_first' => __('locations.select_owner_above_first'),
        'select_owner_first_hint' => __('locations.select_owner_first_hint'),
        'select_device_help' => __('locations.select_device_help'),
        'loading_devices' => __('locations.loading_devices'),
        'select_a_device' => __('locations.select_a_device'),
        'available_devices_group' => __('locations.available_devices_group'),
        'available_suffix' => __('locations.available_suffix'),
        'devices_assigned_elsewhere_group' => __('locations.devices_assigned_elsewhere_group'),
        'assigned_to_prefix' => __('locations.assigned_to_prefix'),
        'unknown_location' => __('locations.unknown_location'),
        'no_devices_found' => __('locations.no_devices_found'),
        'error_loading_devices' => __('locations.error_loading_devices'),
    ];
@endphp

@push('scripts')
<script>
    window.LOCATIONS_T = {!! json_encode($locationsT) !!};
</script>
<script src="/assets/js/locations.js?v={{ filemtime(public_path('assets/js/locations.js')) }}"></script>
@endpush
