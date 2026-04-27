@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('locations.page_title'))

@push('styles')
<style>
/* List card wraps header + table */
.lc-list-card { overflow: hidden; margin-bottom: var(--mw-space-md); }

/* Locations table */
.lc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.lc-table thead th {
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: var(--mw-text-muted);
    text-align: left;
    padding: 10px var(--mw-space-lg);
    border-bottom: 1px solid var(--mw-border-light);
}
.lc-table tbody tr {
    border-bottom: 1px solid var(--mw-border-light);
    cursor: pointer;
    transition: background 0.12s;
}
.lc-table tbody tr:last-child { border-bottom: none; }
.lc-table tbody tr:hover { background: var(--mw-bg-hover); box-shadow: inset 3px 0 0 var(--mw-primary); }
.lc-table td {
    padding: var(--mw-space-md) var(--mw-space-lg);
    vertical-align: middle;
    color: var(--mw-text-secondary);
}
.lc-table td.lc-col-actions { text-align: right; width: 1%; white-space: nowrap; }

/* Location name cell: icon chip + name/subtitle */
.lc-name-cell {
    display: flex;
    align-items: center;
    gap: var(--mw-space-md);
}
.lc-icon-chip {
    width: 30px;
    height: 30px;
    background: var(--mw-primary-tint);
    color: var(--mw-primary);
    border-radius: var(--mw-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.lc-icon-chip [data-feather] { width: 14px !important; height: 14px !important; }
.lc-name-main {
    font-size: 13px;
    font-weight: 700;
    color: var(--mw-text-primary);
    display: flex;
    align-items: center;
    gap: var(--mw-space-sm);
}
.lc-name-sub {
    font-size: 11px;
    color: var(--mw-text-muted);
    margin-top: 1px;
}

/* Primary pill (matches zone-details 489eeb6 pattern) */
.lc-primary-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px 2px 7px;
    font-size: 10.5px;
    font-weight: 600;
    color: var(--mw-primary);
    background: var(--mw-primary-tint);
    border-radius: var(--mw-radius-full);
    line-height: 1.2;
}
.lc-primary-pill::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--mw-primary);
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

/* Inline row actions */
.lc-row-actions { display: inline-flex; gap: 4px; }
.lc-action-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--mw-border);
    background: var(--mw-bg-surface);
    border-radius: var(--mw-radius-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--mw-text-secondary);
    cursor: pointer;
    transition: background 0.12s, color 0.12s, border-color 0.12s;
    padding: 0;
}
.lc-action-btn:hover {
    background: var(--mw-primary-tint);
    border-color: var(--mw-primary);
    color: var(--mw-primary);
}
.lc-action-danger:hover { background: rgba(220,38,38,0.06); border-color: var(--mw-danger); color: var(--mw-danger); }

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

/* List-header bar (title + filter controls on one row, inside .lc-list-card) */
.lc-filter-bar {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: var(--mw-space-md);
    padding: var(--mw-space-md) var(--mw-space-lg);
    flex-wrap: wrap;
    border-bottom: 1px solid var(--mw-border-light);
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

    <!-- List card: header (title + filter controls) + table -->
    <div class="card lc-list-card">
        <div class="lc-filter-bar">
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

        <div id="locations-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>

        <div id="locations-list"></div>
    </div>

    <div id="pagination-container"></div>
</div>

<!-- Clone Location Modal -->
<div class="modal fade" id="clone-location-modal" tabindex="-1" role="dialog" aria-labelledby="clone-location-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clone-location-modal-title"><i data-feather="copy" class="mr-2"></i>{{ __('location_details.modal_clone_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <div class="alert-body"><i data-feather="info" class="mr-2"></i>{{ __('location_details.modal_clone_info') }}</div>
                </div>
                <p class="mb-3"><strong id="clone-location-name-display"></strong></p>
                <div id="clone-owner-group" style="display:none;">
                    <div class="form-group">
                        <label for="clone-owner-select">{{ __('location_details.modal_clone_assign_to_user') }}</label>
                        <select class="form-control" id="clone-owner-select">
                            <option value="">{{ __('location_details.modal_clone_assign_to_self') }}</option>
                        </select>
                        <small class="text-muted">{{ __('location_details.modal_clone_assign_help') }}</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-clone-btn">
                    <i data-feather="copy" class="mr-1"></i><span>{{ __('location_details.modal_clone_location_btn') }}</span>
                </button>
            </div>
        </div>
    </div>
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
        'col_location' => __('locations.col_location'),
        'col_address' => __('locations.col_address'),
        'col_users' => __('locations.col_users'),
        'col_data_usage' => __('locations.col_data_usage'),
        'col_status' => __('locations.col_status'),
        'primary_label' => __('locations.primary_label'),
        'action_view' => __('locations.action_view'),
        'action_clone' => __('locations.action_clone'),
        'action_delete' => __('locations.action_delete'),
        'location_cloned' => __('locations.location_cloned'),
        'error_cloning' => __('locations.error_cloning'),
        'cloning' => __('locations.cloning'),
        'modal_clone_assign_to_self' => __('location_details.modal_clone_assign_to_self'),
        'actions' => __('locations.actions'),
        'empty_title' => __('locations.empty_title'),
        'empty_desc' => __('locations.empty_desc'),
        'error_loading' => __('locations.error_loading'),
        'confirm_delete' => __('locations.confirm_delete'),
        'confirm_delete_title' => __('locations.confirm_delete_title'),
        'delete_btn' => __('locations.delete_btn'),
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
