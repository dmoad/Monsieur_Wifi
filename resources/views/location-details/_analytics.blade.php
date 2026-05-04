<div class="mw-panel" id="ld-panel-analytics">

    {{-- ── Row 1: Hourly Bandwidth + Users & Sessions ─────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Hourly Bandwidth (24h) --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="activity" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                        <h5 class="card-title mb-0">{{ __('location_details.analytics_hourly_title') }}</h5>
                    </div>
                    <small class="text-muted" id="analytics-hourly-updated"></small>
                </div>
                <div class="card-body">
                    <div id="analytics-hourly-chart"></div>
                </div>
            </div>
        </div>

        {{-- Users & Sessions (same period as daily bandwidth) --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="users" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                        <h5 class="card-title mb-0">{{ __('location_details.analytics_users_sessions_title') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="analytics-users-sessions-chart"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 2: Per-Day Bandwidth + Device Types ─────────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Per-day download/upload --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="bar-chart-2" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                        <h5 class="card-title mb-0">{{ __('location_details.analytics_daily_title') }}</h5>
                    </div>
                    <div class="d-flex" style="background:rgba(0,0,0,0.05);border-radius:10px;padding:4px;border:1px solid rgba(0,0,0,0.1);">
                        <button class="analytics-period-btn active" data-period="7"
                                style="padding:6px 14px;border:none;background:var(--mw-primary);color:white;border-radius:8px;cursor:pointer;font-size:0.8rem;">7D</button>
                        <button class="analytics-period-btn" data-period="30"
                                style="padding:6px 14px;border:none;background:transparent;color:#6c757d;border-radius:8px;cursor:pointer;font-size:0.8rem;">30D</button>
                        <button class="analytics-period-btn" data-period="90"
                                style="padding:6px 14px;border:none;background:transparent;color:#6c757d;border-radius:8px;cursor:pointer;font-size:0.8rem;">90D</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="analytics-daily-chart"></div>
                </div>
            </div>
        </div>

        {{-- Device types donut --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i data-feather="pie-chart" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                    <h5 class="card-title mb-0">{{ __('location_details.analytics_device_types_title') }}</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="analytics-device-type-chart"></div>
                    <div id="analytics-device-type-empty" class="text-center text-muted py-4" style="display:none;">
                        <i data-feather="inbox" style="width:32px;height:32px;margin-bottom:8px;"></i>
                        <div><small>{{ __('location_details.analytics_no_data') }}</small></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Row 3: Guest User List ───────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="users" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                        <h5 class="card-title mb-0">{{ __('location_details.analytics_users_title') }}
                            <span class="badge badge-secondary ml-2" id="analytics-users-total" style="font-size:0.75rem;"></span>
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                        <label class="mb-0 small text-muted d-none d-sm-inline" for="analytics-users-per-page">{{ __('location_details.analytics_users_per_page') }}</label>
                        <select class="form-control form-control-sm" id="analytics-users-per-page" style="width:auto;min-width:4.5rem;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="100">100</option>
                        </select>
                        <input type="text" class="form-control form-control-sm" id="analytics-user-search"
                               placeholder="{{ __('location_details.analytics_search_placeholder') }}"
                               style="width:200px;">
                        <a href="/{{ $locale }}/locations/{{ $location }}/ip-log" class="btn btn-sm btn-outline-primary">
                            {{ __('location_details.analytics_ip_log_button') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="analytics-users-refresh">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="analytics-users-loading" class="text-center py-4" style="display:none;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <small class="d-block mt-2 text-muted">{{ __('common.loading') }}</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="analytics-users-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('location_details.analytics_col_name') }}</th>
                                    <th>{{ __('location_details.analytics_col_mac') }}</th>
                                    <th>{{ __('location_details.analytics_col_email') }}</th>
                                    <th>{{ __('location_details.analytics_col_device') }}</th>
                                    <th>{{ __('location_details.analytics_col_os') }}</th>
                                    <th>{{ __('location_details.analytics_col_sessions') }}</th>
                                    <th>{{ __('location_details.analytics_col_last_seen') }}</th>
                                    <th>{{ __('location_details.analytics_col_status') }}</th>
                                </tr>
                            </thead>
                            <tbody id="analytics-users-tbody">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <small>{{ __('location_details.analytics_users_loading') }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center px-3 py-2" id="analytics-users-pagination" style="display:none;">
                        <small class="text-muted" id="analytics-users-count-range"></small>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="analytics-users-prev" disabled>
                                <i data-feather="chevron-left"></i>
                            </button>
                            <span id="analytics-users-page-info" class="text-muted" style="font-size:0.8rem;"></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="analytics-users-next" disabled>
                                <i data-feather="chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- #ld-panel-analytics --}}
