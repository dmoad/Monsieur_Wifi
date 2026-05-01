<div class="mw-panel active" id="ld-panel-overview">

    <!-- Location Overview -->
    <div class="stats-grid">
        <!-- Device card -->
        <div class="stat-card">
            <div class="ld-ov-head">
                <div>
                    <div class="ld-ov-name location_name"></div>
                    <div class="ld-ov-sub location_address"></div>
                    <div class="ld-ov-sub-zone" id="location-zone-line" style="display:none;">{{ __('location_details.zone_label') }}: <strong class="location_zone"></strong></div>
                </div>
                <span class="status-badge status-offline">{{ __('common.offline') }}</span>
            </div>
            <div class="ld-ov-mac-row">
                <span class="ld-ov-mac-chip">{{ __('location_details.mac_prefix') }} <span class="router_mac_address_header">{{ __('common.loading') }}</span></span>
                <button class="btn btn-sm btn-outline-secondary" id="edit-mac-btn" style="font-size: 11px;">
                    <i data-feather="edit" class="mr-1" style="width: 12px; height: 12px;"></i>{{ __('location_details.edit_button') }}
                </button>
            </div>
            <dl class="ld-ov-details">
                <dt>{{ __('location_details.router_model') }}</dt><dd class="router_model_updated"></dd>
                <dt>{{ __('location_details.mac_address') }}</dt><dd class="router_mac_address"></dd>
                <dt>{{ __('location_details.firmware') }}</dt><dd class="router_firmware"></dd>
                <dt>{{ __('location_details.total_users') }}</dt><dd class="connected_users"></dd>
                <dt>{{ __('location_details.daily_usage') }}</dt><dd class="daily_usage"></dd>
                <dt>{{ __('location_details.uptime') }}</dt><dd class="uptime"></dd>
            </dl>
            <div class="ld-ov-actions">
                <button class="btn btn-primary btn-sm" id="device-restart-btn"><i data-feather="refresh-cw" class="mr-1"></i>{{ __('location_details.restart_button') }}</button>
                <button class="btn btn-outline-secondary btn-sm" id="update-firmware-btn"><i data-feather="download" class="mr-1"></i>{{ __('location_details.update_button') }}</button>
            </div>
        </div>

        <!-- Usage Stats -->
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('location_details.current_usage') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" id="usage-period-btn">{{ __('location_details.period_7days') }}</button>
                    <div class="dropdown-menu dropdown-menu-right" id="usage-period-dropdown">
                        <a class="dropdown-item" href="javascript:void(0);" data-period="today">{{ __('location_details.period_today') }}</a>
                        <a class="dropdown-item" href="javascript:void(0);" data-period="7days">{{ __('location_details.period_7days') }}</a>
                        <a class="dropdown-item" href="javascript:void(0);" data-period="30days">{{ __('location_details.period_30days') }}</a>
                    </div>
                </div>
            </div>
            <div id="usage-loading" class="text-center py-3" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">{{ __('common.loading') }}</span></div>
                <small class="d-block mt-2 text-muted">{{ __('location_details.loading_usage_data') }}</small>
            </div>
            <div class="row text-center" id="usage-data">
                <div class="col-6">
                    <div class="mb-3"><div class="stat-value text-primary" id="download-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_download') }}</div></div>
                    <div><div class="stat-value text-info" id="users-sessions-count"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_users_sessions') }}</div></div>
                </div>
                <div class="col-6">
                    <div class="mb-3"><div class="stat-value text-success" id="upload-usage"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_upload') }}</div></div>
                    <div><div class="stat-value text-warning" id="avg-session-time"><i class="fas fa-spinner fa-spin" style="font-size:1rem;"></i></div><div class="stat-label">{{ __('location_details.stat_avg_session') }}</div></div>
                </div>
            </div>
            <div class="text-center mt-3"><small class="text-muted" id="usage-last-updated">{{ __('location_details.loading_data') }}</small></div>
        </div>

        <!-- Map Card -->
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('location_details.location_map_title') }}</h5>
                <small class="text-muted" id="map-coordinates" style="display: none;"></small>
            </div>
            <div id="location-map" class="location-map"></div>
        </div>
    </div>

    <!-- Analytics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{ __('location_details.analytics_title') }}</h4></div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="analytics-chart-card">
                                <div class="chart-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="chart-icon" style="background: rgba(99,102,241,0.12); width:50px; height:50px; border-radius:15px; display:flex; align-items:center; justify-content:center;">
                                                <i data-feather="bar-chart-2" style="color:var(--mw-primary);"></i>
                                            </div>
                                            <div>
                                                <h5 style="margin:0; font-weight:600; color:var(--mw-text-primary);">{{ __('location_details.daily_usage_analytics') }}</h5>
                                                <p style="margin:0; color:var(--mw-text-muted); font-size:0.9rem;">{{ __('location_details.captive_portal_activity') }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex" style="background:rgba(0,0,0,0.05); border-radius:10px; padding:4px; border:1px solid rgba(0,0,0,0.1);">
                                            <button class="period-btn active" data-period="7" style="padding:8px 16px; border:none; background:var(--mw-primary); color:white; border-radius:8px; cursor:pointer;">7D</button>
                                            <button class="period-btn" data-period="30" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">30D</button>
                                            <button class="period-btn" data-period="90" style="padding:8px 16px; border:none; background:transparent; color:#6c757d; border-radius:8px; cursor:pointer;">90D</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-stats">
                                    <div class="stat-item"><div class="stat-icon stat-users"><i data-feather="users"></i></div><div><span class="stat-value" id="total-users">-</span><span class="stat-label d-block">{{ __('location_details.total_users') }}</span></div></div>
                                    <div class="stat-item"><div class="stat-icon stat-sessions"><i data-feather="activity"></i></div><div><span class="stat-value" id="total-sessions">-</span><span class="stat-label d-block">{{ __('location_details.stat_sessions') }}</span></div></div>
                                    <div class="stat-item"><div class="stat-icon stat-avg"><i data-feather="trending-up"></i></div><div><span class="stat-value" id="avg-daily">-</span><span class="stat-label d-block">{{ __('location_details.stat_daily_avg') }}</span></div></div>
                                </div>
                                <div class="chart-container"><div id="daily-usage-chart"></div></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="online-users-card">
                                <div class="users-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="users-icon" style="background: rgba(99,102,241,0.12);">
                                                <i data-feather="wifi" style="color:var(--mw-primary);"></i>
                                            </div>
                                            <div><h5 style="margin:0; font-weight:600; color:var(--mw-text-primary);">{{ __('location_details.live_users') }}</h5><p style="margin:0; color:var(--mw-text-muted); font-size:0.9rem;">{{ __('location_details.currently_connected') }}</p></div>
                                        </div>
                                        <button class="refresh-btn" id="refresh-online-users"><i data-feather="refresh-cw"></i></button>
                                    </div>
                                    <div class="users-count">
                                        <span class="count-number" id="online-count">0</span>
                                        <span style="color:var(--mw-text-muted); font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px;">{{ __('location_details.online_label') }}</span>
                                        <span id="count-range" class="ms-2 text-nowrap" style="display:none; font-size:0.75rem; color:var(--mw-text-muted);"></span>
                                    </div>
                                </div>
                                <div class="users-container">
                                    <div id="online-users-list">
                                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 20px; text-align:center;">
                                            <i data-feather="loader" style="width:40px; height:40px; color:var(--mw-primary); animation:spin 1s linear infinite; margin-bottom:15px;"></i>
                                            <p>{{ __('location_details.loading_online_users') }}</p>
                                        </div>
                                    </div>
                                    <div class="pagination-container" id="users-pagination" style="display: none;">
                                        <div class="pagination-controls">
                                            <button class="pagination-btn" id="prev-page" disabled><i data-feather="chevron-left"></i></button>
                                            <div class="d-flex align-items-center gap-1" id="page-numbers"></div>
                                            <button class="pagination-btn" id="next-page" disabled><i data-feather="chevron-right"></i></button>
                                        </div>
                                        <div class="text-center mt-2"><span style="font-size:0.85rem; color:var(--mw-text-muted);" id="page-info">1 / 1</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div><!-- /ld-panel-overview -->
