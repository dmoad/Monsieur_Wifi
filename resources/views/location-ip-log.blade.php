@extends('layouts.app')

@section('title', __('location_details.ip_log_page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('location_details.ip_log_heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/access-points">{{ __('access_points.heading') }}</a></li>
                        <li class="breadcrumb-item">
                            <a href="/{{ $locale }}/locations/{{ $location }}?tab=analytics"><span id="ip-log-location-name">{{ __('common.loading') }}</span></a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('location_details.ip_log_breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="alert alert-warning mb-3" role="alert">
        <div class="alert-body">{{ __('location_details.ip_log_slow_search_notice') }}</div>
    </div>

    <div id="ip-log-no-device" class="alert alert-info mb-3" style="display:none;" role="alert">
        <div class="alert-body">{{ __('location_details.ip_log_no_device') }}</div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('location_details.ip_log_heading') }}</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <label class="mb-0 small text-muted" for="ip-log-search-field">{{ __('location_details.ip_log_search_field_label') }}</label>
                <select class="form-control form-control-sm" id="ip-log-search-field" style="width:auto;min-width:9rem;">
                    <option value="mac">{{ __('location_details.ip_log_search_field_mac') }}</option>
                    <option value="src_ip">{{ __('location_details.ip_log_search_field_src_ip') }}</option>
                    <option value="dst_ip">{{ __('location_details.ip_log_search_field_dst_ip') }}</option>
                </select>
                <input type="text" class="form-control form-control-sm" id="ip-log-search-input"
                       placeholder="{{ __('location_details.ip_log_search_placeholder') }}"
                       style="width:200px;">
                <label class="mb-0 small text-muted d-none d-sm-inline" for="ip-log-per-page">{{ __('location_details.ip_log_per_page') }}</label>
                <select class="form-control form-control-sm" id="ip-log-per-page" style="width:auto;min-width:4.5rem;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="100">100</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="ip-log-refresh">
                    <i data-feather="refresh-cw"></i>
                    <span class="d-none d-md-inline ml-50">{{ __('location_details.ip_log_refresh') }}</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="ip-log-loading" class="text-center py-4" style="display:none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <small class="d-block mt-2 text-muted">{{ __('common.loading') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('location_details.ip_log_col_mac') }}</th>
                            <th>{{ __('location_details.ip_log_col_src_ip') }}</th>
                            <th>{{ __('location_details.ip_log_col_dst_ip') }}</th>
                            <th>{{ __('location_details.ip_log_col_first_seen') }}</th>
                            <th>{{ __('location_details.ip_log_col_last_seen') }}</th>
                            <th>{{ __('location_details.ip_log_col_hits') }}</th>
                            <th>{{ __('location_details.ip_log_col_slot') }}</th>
                        </tr>
                    </thead>
                    <tbody id="ip-log-tbody">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <small>{{ __('location_details.ip_log_loading') }}</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2" id="ip-log-pagination" style="display:none;">
                <small class="text-muted" id="ip-log-count-range"></small>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="ip-log-prev" disabled>
                        <i data-feather="chevron-left"></i>
                    </button>
                    <span id="ip-log-page-info" class="text-muted" style="font-size:0.8rem;"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="ip-log-next" disabled>
                        <i data-feather="chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_CONFIG_V5 = window.APP_CONFIG_V5 || {};
    window.APP_CONFIG_V5.apiBase = '{{ rtrim(config("app.url"), "/") }}/api';
    window.IP_LOG_LOCATION_ID = '{{ $location }}';
    window.IP_LOG_PAGE_LOCALE = '{{ $locale }}';
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.location_details = @json(__('location_details'));
    window.APP_I18N.common = @json(__('common'));
</script>
<script src="/assets/js/location-ip-log.js?v={{ filemtime(public_path('assets/js/location-ip-log.js')) }}"></script>
@endpush
