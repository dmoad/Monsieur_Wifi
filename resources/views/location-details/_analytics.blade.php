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

    {{-- ── Row 3: Guest Users & Sessions ──────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0 border-bottom-0">

                    {{-- Tab bar --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 pt-2 border-bottom">
                        <ul class="nav mw-subtabs mb-0" id="analytics-table-tabs" role="tablist" style="border:none;">
                            <li class="nav-item">
                                <a class="nav-link active" id="analytics-tab-users-link" data-toggle="tab" href="#analytics-tab-users" role="tab">
                                    <i data-feather="users" style="width:13px;height:13px;vertical-align:text-bottom;" class="mr-1"></i>
                                    {{ __('location_details.analytics_users_title') }}
                                    <span class="badge badge-secondary ml-1" id="analytics-users-total" style="font-size:0.72rem;"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="analytics-tab-sessions-link" data-toggle="tab" href="#analytics-tab-sessions" role="tab">
                                    <i data-feather="activity" style="width:13px;height:13px;vertical-align:text-bottom;" class="mr-1"></i>
                                    {{ __('location_details.analytics_sessions_title') }}
                                    <span class="badge badge-secondary ml-1" id="analytics-sessions-total" style="font-size:0.72rem;"></span>
                                </a>
                            </li>
                        </ul>

                        {{-- Actions (shared toolbar, content swaps per active tab) --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ml-auto pb-2" id="analytics-toolbar-users">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="/{{ $locale }}/locations/{{ $location }}/ip-log" class="btn btn-outline-primary d-flex align-items-center">
                                    <i data-feather="external-link" style="width:14px;height:14px;"></i>
                                    <span class="ml-50">{{ __('location_details.analytics_ip_log_button') }}</span>
                                </a>
                                <button type="button" class="btn btn-outline-secondary d-flex align-items-center" id="analytics-users-export-csv" title="{{ __('location_details.analytics_export_csv') }}">
                                    <i data-feather="download" style="width:14px;height:14px;"></i>
                                    <span class="ml-50 d-none d-md-inline">{{ __('location_details.analytics_export_csv') }}</span>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" id="analytics-users-refresh" title="{{ __('location_details.analytics_refresh_tooltip') }}">
                                <i data-feather="refresh-cw" style="width:14px;height:14px;"></i>
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ml-auto pb-2" id="analytics-toolbar-sessions" style="display:none!important;">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" id="analytics-sessions-refresh" title="{{ __('location_details.analytics_refresh_tooltip') }}">
                                <i data-feather="refresh-cw" style="width:14px;height:14px;"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="tab-content">

                    {{-- ── Tab 1: Guest Users ───────────────────────────────── --}}
                    <div class="tab-pane fade show active" id="analytics-tab-users" role="tabpanel">
                        {{-- Filters --}}
                        <div class="d-flex flex-wrap align-items-center gap-2 px-3 py-2 bg-light" style="border-bottom:1px solid rgba(0,0,0,0.06);">
                            <label class="mb-0 small text-muted" for="analytics-users-per-page">{{ __('location_details.analytics_users_per_page') }}</label>
                            <select class="form-control form-control-sm" id="analytics-users-per-page" style="width:auto;min-width:4.5rem;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="100">100</option>
                            </select>
                            <input type="search" class="form-control form-control-sm flex-grow-1" id="analytics-user-search"
                                   placeholder="{{ __('location_details.analytics_search_placeholder') }}"
                                   style="min-width:160px;max-width:420px;">
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

                    {{-- ── Tab 2: Guest User Sessions ───────────────────────── --}}
                    <div class="tab-pane fade" id="analytics-tab-sessions" role="tabpanel">
                        {{-- Filters --}}
                        <div class="d-flex flex-wrap align-items-center gap-2 px-3 py-2 bg-light" style="border-bottom:1px solid rgba(0,0,0,0.06);">
                            <label class="mb-0 small text-muted" for="analytics-sessions-per-page">{{ __('location_details.analytics_users_per_page') }}</label>
                            <select class="form-control form-control-sm" id="analytics-sessions-per-page" style="width:auto;min-width:4.5rem;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="100">100</option>
                            </select>
                            <select class="form-control form-control-sm" id="analytics-sessions-status" style="width:auto;min-width:7rem;">
                                <option value="all">{{ __('location_details.analytics_sessions_status_all') }}</option>
                                <option value="active">{{ __('location_details.analytics_sessions_status_active') }}</option>
                                <option value="terminated">{{ __('location_details.analytics_sessions_status_terminated') }}</option>
                            </select>
                            <input type="search" class="form-control form-control-sm flex-grow-1" id="analytics-sessions-search"
                                   placeholder="{{ __('location_details.analytics_sessions_search_placeholder') }}"
                                   style="min-width:160px;max-width:420px;">
                        </div>
                        <div class="card-body p-0">
                            <div id="analytics-sessions-loading" class="text-center py-4" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <small class="d-block mt-2 text-muted">{{ __('common.loading') }}</small>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="analytics-sessions-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('location_details.analytics_col_mac') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_network') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_login_type') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_connect') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_disconnect') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_duration') }}</th>
                                            <th>{{ __('location_details.analytics_col_status') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_download') }}</th>
                                            <th>{{ __('location_details.analytics_sessions_col_upload') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="analytics-sessions-tbody">
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <small>{{ __('location_details.analytics_users_loading') }}</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" id="analytics-sessions-pagination" style="display:none;">
                                <small class="text-muted" id="analytics-sessions-count-range"></small>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="analytics-sessions-prev" disabled>
                                        <i data-feather="chevron-left"></i>
                                    </button>
                                    <span id="analytics-sessions-page-info" class="text-muted" style="font-size:0.8rem;"></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="analytics-sessions-next" disabled>
                                        <i data-feather="chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /.tab-content --}}
            </div>
        </div>
    </div>

</div>{{-- #ld-panel-analytics --}}
