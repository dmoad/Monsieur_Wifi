@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('access_points.page_title'))

@push('styles')
<style>
.ap-preview-banner {
    background: var(--mw-primary-tint);
    color: var(--mw-text-primary);
    border: 1px solid var(--mw-primary-faint, rgba(108, 92, 231, 0.18));
    border-radius: var(--mw-radius-md);
    padding: 10px 14px;
    margin-bottom: var(--mw-space-lg);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ap-preview-banner [data-feather] {
    width: 16px; height: 16px; color: var(--mw-primary); flex-shrink: 0;
}

/* Filter bar: counts on the left, chips in the middle, search on the right.
   Wraps gracefully on narrow viewports. */
.ap-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--mw-space-md);
    margin-bottom: var(--mw-space-md);
}
.ap-summary {
    font-size: 12.5px;
    color: var(--mw-text-muted);
    font-weight: 500;
    flex-shrink: 0;
}
.ap-summary strong {
    color: var(--mw-text-primary);
    font-weight: 700;
}
.ap-filter-chips {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    flex: 1 1 auto;
    justify-content: center;
}
.ap-chip {
    border: 1px solid var(--mw-border-light);
    background: var(--mw-bg-surface);
    color: var(--mw-text-secondary);
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: var(--mw-radius-full);
    cursor: pointer;
    transition: background 0.12s, color 0.12s, border-color 0.12s;
    line-height: 1.4;
    white-space: nowrap;
}
.ap-chip:hover {
    color: var(--mw-text-primary);
    border-color: var(--mw-border);
}
.ap-chip.active {
    color: var(--mw-primary);
    background: var(--mw-primary-tint);
    border-color: transparent;
}
.ap-search-input {
    max-width: 280px;
    font-size: 13px;
    flex-shrink: 0;
}

/* Table reuses the .lc-table look from locations.blade.php — keep
   the prototype self-contained so the production page can stay untouched. */
.ap-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: var(--mw-bg-surface);
}
.ap-table thead th {
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: var(--mw-text-muted);
    text-align: left;
    padding: 10px var(--mw-space-lg);
    border-bottom: 1px solid var(--mw-border-light);
    background: var(--mw-bg-surface);
}
.ap-table tbody tr {
    border-bottom: 1px solid var(--mw-border-light);
    cursor: pointer;
    transition: background 0.12s;
}
.ap-table tbody tr:last-child { border-bottom: none; }
.ap-table tbody tr:hover {
    background: var(--mw-bg-hover);
    box-shadow: inset 3px 0 0 var(--mw-primary);
}
.ap-table td {
    padding: var(--mw-space-md) var(--mw-space-lg);
    vertical-align: middle;
    color: var(--mw-text-secondary);
}

.ap-name-cell { display: flex; align-items: center; gap: var(--mw-space-md); }
.ap-icon-chip {
    width: 30px; height: 30px;
    background: var(--mw-primary-tint);
    color: var(--mw-primary);
    border-radius: var(--mw-radius-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ap-icon-chip [data-feather] { width: 14px !important; height: 14px !important; }
.ap-name-main { font-size: 13px; font-weight: 700; color: var(--mw-text-primary); }
.ap-name-sub { font-size: 11px; color: var(--mw-text-muted); margin-top: 1px; }

/* Status pill */
.ap-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: var(--mw-radius-full);
    line-height: 1.4;
}
.ap-status::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.ap-status-online  { color: #1ea672; background: rgba(30,166,114,0.10); }
.ap-status-offline { color: var(--mw-text-muted); background: var(--mw-bg-page); }

/* Empty / loading states */
.ap-empty {
    padding: 60px 20px;
    text-align: center;
    color: var(--mw-text-muted);
    font-size: 13px;
}

.ap-table-card {
    background: var(--mw-bg-surface);
    border: 1px solid var(--mw-border-light);
    border-radius: var(--mw-radius-lg);
    overflow: hidden;
    box-shadow: var(--mw-shadow-card);
}

/* Summary cards — same shape as locations.blade.php's lc-summary-card */
.ap-summary-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem;
}
.ap-summary-num {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--mw-text-primary);
    line-height: 1.1;
}
.ap-summary-lbl {
    font-size: 0.85rem;
    color: var(--mw-text-muted);
    margin-top: 4px;
}

/* Row action buttons — match existing .lc-action-btn used on /locations */
.ap-col-actions { text-align: right; width: 1%; white-space: nowrap; }
.ap-row-actions {
    display: inline-flex;
    gap: 4px;
    justify-content: flex-end;
}
.ap-action-btn {
    width: 32px; height: 32px;
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
.ap-action-btn:hover {
    background: var(--mw-primary-tint);
    border-color: var(--mw-primary);
    color: var(--mw-primary);
}
.ap-action-btn.ap-action-danger:hover {
    background: rgba(220,38,38,0.06);
    border-color: var(--mw-danger);
    color: var(--mw-danger);
}
.ap-action-btn [data-feather],
.ap-action-btn svg { width: 14px !important; height: 14px !important; }

/* Tab panels — own class so the global .mw-panel handler doesn't
   collide with the page-level mw-tab convention */
.ap-panel { display: none; }
.ap-panel.active { display: block; }

.ap-count-pill {
    display: inline-block;
    margin-left: 6px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--mw-text-muted);
    background: var(--mw-bg-page);
    border-radius: var(--mw-radius-full);
}
.mw-tab.active .ap-count-pill { color: var(--mw-primary); background: var(--mw-primary-tint); }

/* Top-right action buttons */
.ap-actions {
    display: inline-flex;
    gap: 8px;
    align-items: center;
    justify-content: flex-end;
}
.ap-actions .btn { white-space: nowrap; }

/* ============================================================
   Grouped-by-zone view — visual hierarchy is the explanation.
   Zone header rows, indented member rows with tree-line
   connectors, primary indicator.
   ============================================================ */

/* Each zone (and the standalone bucket) is its own tbody so we
   can collapse/expand independently. */
.ap-group {
    border-bottom: 1px solid var(--mw-border-light);
}
.ap-group:last-child { border-bottom: none; }

.ap-group-head {
    background: transparent;
    cursor: pointer;
    user-select: none;
    transition: background 0.12s;
}
.ap-group-head:hover { background: var(--mw-primary-tint); }
.ap-group-head:hover .ap-group-name { text-decoration: underline; }
.ap-group-head td {
    padding: 11px var(--mw-space-lg);
    border-bottom: 1px solid var(--mw-border-light);
    color: var(--mw-text-primary);
}

/* Standalone group: same shape as zones but visually distinct.
   Adds a top divider so it doesn't merge into the zone above. */
.ap-group-standalone .ap-group-head {
    background: transparent;
    cursor: default;
    border-top: 1px solid var(--mw-border-light);
}
.ap-group-standalone .ap-group-head:hover { background: transparent; }
.ap-group-standalone .ap-group-head td { color: var(--mw-text-muted); }
.ap-group-standalone .ap-group-icon {
    background: var(--mw-bg-page);
    color: var(--mw-text-muted);
}

/* Status stripe on the left edge of the zone header — quick health
   scan when you have many zones. Pastel register, matches the rollup
   pills and the rest of the app's tinted style. */
.ap-group-health-online   .ap-group-head td:first-child { box-shadow: inset 3px 0 0 #4ade80; }
.ap-group-health-offline  .ap-group-head td:first-child { box-shadow: inset 3px 0 0 #f87171; }
.ap-group-health-mixed    .ap-group-head td:first-child { box-shadow: inset 3px 0 0 #fbbf24; }

.ap-group-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
}
.ap-group-chevron {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px; height: 24px;
    margin: -4px 2px -4px -4px;
    border-radius: var(--mw-radius-md);
    color: var(--mw-text-muted);
    cursor: pointer;
    transition: background 0.12s, color 0.12s;
    flex-shrink: 0;
}
.ap-group-chevron:hover {
    background: rgba(0,0,0,0.05);
    color: var(--mw-text-primary);
}
.ap-group-chevron [data-feather] {
    width: 14px !important; height: 14px !important;
    transition: transform 0.18s ease;
}
.ap-group.collapsed .ap-group-chevron [data-feather] { transform: rotate(-90deg); }
.ap-group-standalone .ap-group-chevron { visibility: hidden; }

.ap-group-icon {
    width: 26px; height: 26px;
    background: var(--mw-primary-tint);
    color: var(--mw-primary);
    border-radius: var(--mw-radius-md);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.ap-group-icon [data-feather] { width: 13px !important; height: 13px !important; }
.ap-group-standalone .ap-group-icon { background: transparent; color: var(--mw-text-muted); }

.ap-group-meta {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--mw-text-muted);
}
/* Rollup pills — same pastel register as filter chips: tinted bg
   + softer text colour, no leading dot (the colour already tells you). */
.ap-rollup-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: var(--mw-radius-full);
    font-size: 11px;
    font-weight: 600;
    line-height: 1.5;
}
.ap-rollup-online  { color: #16a34a; background: #ECFDF5; }
.ap-rollup-offline { color: #dc2626; background: #FEF2F2; }

.ap-meta-sep {
    color: var(--mw-text-muted);
    opacity: 0.5;
    font-weight: 400;
}

/* Right-edge "open zone" cue — visual hint that the whole header is a
   drill-in target. Pure decoration; the row's own click navigates. */
.ap-zone-open {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    margin-left: 4px;
    color: var(--mw-text-muted);
    transition: color 0.12s, transform 0.12s;
}
.ap-group-head:hover .ap-zone-open {
    color: var(--mw-primary);
    transform: translateX(2px);
}
.ap-zone-open [data-feather] { width: 14px !important; height: 14px !important; }

/* Make the whole zone name look like a link (underline on header hover) */
.ap-group-name {
    text-decoration: underline transparent;
    text-underline-offset: 3px;
    transition: text-decoration-color 0.12s;
}

/* Member rows (APs nested inside a zone or in standalone) */
.ap-group.collapsed .ap-member { display: none; }

.ap-member td:first-child {
    padding-left: 56px !important;
    position: relative;
}
/* Tree connector — vertical line + horizontal stub */
.ap-group:not(.ap-group-standalone) .ap-member td:first-child::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 1px;
    background: var(--mw-border-light);
}
.ap-group:not(.ap-group-standalone) .ap-member td:first-child::after {
    content: '';
    position: absolute;
    left: 30px;
    top: 50%;
    width: 14px;
    height: 1px;
    background: var(--mw-border-light);
}
/* Last member: cut the vertical line at the elbow so it doesn't extend down */
.ap-group:not(.ap-group-standalone) .ap-member.ap-member-last td:first-child::before {
    bottom: 50%;
}

/* Primary AP badge — sits next to the AP name in a zone */
.ap-primary-pill {
    display: inline-block;
    margin-left: 8px;
    padding: 1px 8px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--mw-primary);
    background: var(--mw-primary-tint);
    border-radius: var(--mw-radius-full);
    line-height: 1.6;
    vertical-align: middle;
    cursor: help;
}
</style>
@endpush

@section('content')
<div id="ap-page">
    <div class="content-header row">
        <div class="content-header-left col-md-7 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ __('access_points.heading') }}</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('access_points.heading') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-right col-md-5 col-12 d-md-block d-none">
            <div class="ap-actions">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="apActions.createZone()">
                    <i data-feather="layers" class="mr-50"></i>{{ __('access_points.create_zone') }}
                </button>
                <a href="/{{ $locale }}/locations" class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="mr-50"></i>{{ __('access_points.add_ap') }}
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="ap-preview-banner">
            <i data-feather="info"></i>
            <span>{{ __('access_points.preview_banner') }}</span>
        </div>

        {{-- Summary cards (totals across the fleet, never filtered) --}}
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="ap-summary-card">
                        <div>
                            <div class="ap-summary-num" id="ap-metric-total">—</div>
                            <div class="ap-summary-lbl">{{ __('access_points.metric_total_aps') }}</div>
                        </div>
                        <div class="mw-stat-icon mw-stat-icon-primary"><i data-feather="wifi"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="ap-summary-card">
                        <div>
                            <div class="ap-summary-num" id="ap-metric-online">—</div>
                            <div class="ap-summary-lbl">{{ __('access_points.metric_online_aps') }}</div>
                        </div>
                        <div class="mw-stat-icon mw-stat-icon-success"><i data-feather="check-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="ap-summary-card">
                        <div>
                            <div class="ap-summary-num" id="ap-metric-users">—</div>
                            <div class="ap-summary-lbl">{{ __('access_points.metric_total_users') }}</div>
                        </div>
                        <div class="mw-stat-icon mw-stat-icon-info"><i data-feather="users"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="ap-summary-card">
                        <div>
                            <div class="ap-summary-num" id="ap-metric-data">—</div>
                            <div class="ap-summary-lbl">{{ __('access_points.metric_total_data') }}</div>
                        </div>
                        <div class="mw-stat-icon mw-stat-icon-warning"><i data-feather="download"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mw-tabs">
            <button type="button" class="mw-tab active" data-ap-tab="aps">
                {{ __('access_points.tab_aps') }}<span class="ap-count-pill" id="ap-aps-tab-count">—</span>
            </button>
            <button type="button" class="mw-tab" data-ap-tab="zones">
                {{ __('access_points.tab_zones') }}<span class="ap-count-pill" id="ap-zones-tab-count">—</span>
            </button>
        </div>

        <div class="ap-filter-bar">
            <div class="ap-summary" id="ap-summary">—</div>

            <div class="ap-filter-chips" role="tablist">
                <button type="button" class="ap-chip active" data-filter="all">{{ __('access_points.filter_all') }}</button>
                <button type="button" class="ap-chip" data-filter="online">{{ __('access_points.filter_online') }}</button>
                <button type="button" class="ap-chip" data-filter="offline">{{ __('access_points.filter_offline') }}</button>
            </div>

            <input type="text" class="form-control form-control-sm ap-search-input" id="ap-search-aps"
                   placeholder="{{ __('access_points.search_aps') }}">
        </div>

        {{-- Tab 1: APs without a zone (flat list, default landing) --}}
        <div class="ap-panel active" id="ap-panel-aps">
            <div class="ap-table-card">
                <table class="ap-table" id="ap-aps-table">
                    <thead>
                        <tr>
                            <th>{{ __('access_points.col_name') }}</th>
                            <th>{{ __('access_points.col_status') }}</th>
                            <th>{{ __('access_points.col_address') }}</th>
                            <th>{{ __('access_points.col_users') }}</th>
                            <th>{{ __('access_points.col_data') }}</th>
                            <th>{{ __('access_points.col_last_seen') }}</th>
                            <th class="ap-col-actions">{{ __('access_points.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" class="ap-empty">{{ __('common.loading') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab 2: Zones (grouped, collapsed by default) --}}
        <div class="ap-panel" id="ap-panel-zones">
            <div class="ap-table-card">
                <table class="ap-table" id="ap-zones-table">
                    <thead>
                        <tr>
                            <th>{{ __('access_points.col_name') }}</th>
                            <th>{{ __('access_points.col_status') }}</th>
                            <th>{{ __('access_points.col_address') }}</th>
                            <th>{{ __('access_points.col_users') }}</th>
                            <th>{{ __('access_points.col_data') }}</th>
                            <th>{{ __('access_points.col_last_seen') }}</th>
                            <th class="ap-col-actions">{{ __('access_points.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" class="ap-empty">{{ __('common.loading') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const locale = @json($locale);
    const T = {
        standalone:      @json(__('access_points.standalone')),
        no_address:      @json(__('access_points.no_address')),
        never_seen:      @json(__('access_points.never_seen')),
        status_online:   @json(__('access_points.status_online')),
        status_offline:  @json(__('access_points.status_offline')),
        no_aps:          @json(__('access_points.no_aps')),
        no_aps_match:    @json(__('access_points.no_aps_match')),
        primary:         @json(__('access_points.primary')),
        primary_pill:    @json(__('access_points.primary_pill')),
        open_zone:       @json(__('access_points.open_zone')),
        ap_singular:     @json(__('access_points.ap_singular')),
        ap_plural:       @json(__('access_points.ap_plural')),
        zone_singular:   @json(__('access_points.zone_singular')),
        zone_plural:     @json(__('access_points.zone_plural')),
        meta_online:     @json(__('access_points.meta_online')),
        meta_offline:    @json(__('access_points.meta_offline')),
        action_clone:    @json(__('access_points.action_clone')),
        action_delete:   @json(__('access_points.action_delete')),
        action_edit:     @json(__('access_points.action_edit')),
        confirm_delete_ap:   @json(__('access_points.confirm_delete_ap')),
        confirm_delete_zone: @json(__('access_points.confirm_delete_zone')),
        ap_deleted:      @json(__('access_points.ap_deleted')),
        ap_cloned:       @json(__('access_points.ap_cloned')),
        zone_deleted:    @json(__('access_points.zone_deleted')),
        zone_created:    @json(__('access_points.zone_created')),
        create_zone_prompt: @json(__('access_points.create_zone_prompt')),
        action_failed:   @json(__('access_points.action_failed')),
    };

    // localStorage: track which zones the user has *expanded*. Default empty
    // = all zones collapsed (matches the new tab-based design).
    const EXPAND_KEY = 'ap_preview_expanded_zones';
    const loadExpanded = () => {
        try { return new Set(JSON.parse(localStorage.getItem(EXPAND_KEY) || '[]')); }
        catch { return new Set(); }
    };
    const saveExpanded = set => {
        try { localStorage.setItem(EXPAND_KEY, JSON.stringify([...set])); } catch {}
    };
    let expandedZones = loadExpanded();

    // UI state — initial tab from ?tab= query param ('zones' switches to
    // the grouped view; default 'aps' shows the flat list)
    const urlTab = new URL(window.location.href).searchParams.get('tab');
    let activeTab = urlTab === 'zones' ? 'zones' : 'aps';
    let activeFilter = 'all';       // 'all' | 'online' | 'offline'

    const escapeHtml = s => String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    let allAps = [];
    let allZones = [];

    // --- Render APs (grouped by zone) -------------------------------------
    // Each zone renders as its own <tbody> with a clickable header row +
    // nested member rows. A trailing "Standalone" tbody holds APs with no
    // zone. The visual hierarchy itself communicates what a Zone is.
    function apMatchesSearch(ap, q) {
        if (!q) return true;
        const hay = [
            ap.name, ap.address, ap.city,
            ap.device?.mac_address, ap.device?.serial_number,
            ap.zone?.name,
        ].filter(Boolean).join(' ').toLowerCase();
        return hay.includes(q);
    }

    function apMatchesChip(ap) {
        if (activeFilter === 'all')     return true;
        if (activeFilter === 'online')  return ap.online_status === 'online';
        if (activeFilter === 'offline') return ap.online_status !== 'online';
        return true;
    }

    function pluralize(n, sing, plur) {
        return `${n} ${n === 1 ? sing : plur}`;
    }

    function renderApRow(ap, isPrimary, isLast) {
        const status = ap.online_status === 'online' ? 'online' : 'offline';
        const statusLbl = status === 'online' ? T.status_online : T.status_offline;
        const addr = [ap.city, ap.country].filter(Boolean).join(', ') || T.no_address;
        const lastSeen = ap.last_seen ? new Date(ap.last_seen).toLocaleString() : T.never_seen;
        const users = Number(ap.users) || 0;
        const dataUsage = (ap.data_usage_gb != null)
            ? `${Number(ap.data_usage_gb).toFixed(2)} GB`
            : (ap.data_usage != null ? `${Number(ap.data_usage).toFixed(0)} MB` : '—');
        const primaryBadge = isPrimary
            ? `<span class="ap-primary-pill" title="${escapeHtml(T.primary)}">${escapeHtml(T.primary_pill)}</span>`
            : '';
        const lastClass = isLast ? ' ap-member-last' : '';
        const safeName = JSON.stringify(ap.name || '').replace(/"/g, '&quot;');
        // When the row is rendered inside a zone group, attach ?from=zone-X
        // so the location detail breadcrumb traces back through the zone.
        const fromParam = ap.zone?.id ? `?from=zone-${ap.zone.id}` : '';
        return `
            <tr class="ap-member${lastClass}" onclick="window.location.href='/${locale}/locations/${ap.id}${fromParam}'">
                <td>
                    <div class="ap-name-cell">
                        <span class="ap-icon-chip"><i data-feather="wifi"></i></span>
                        <div>
                            <div class="ap-name-main">${escapeHtml(ap.name || '—')}${primaryBadge}</div>
                            <div class="ap-name-sub">${escapeHtml(ap.device?.mac_address || '')}</div>
                        </div>
                    </div>
                </td>
                <td><span class="ap-status ap-status-${status}">${escapeHtml(statusLbl)}</span></td>
                <td>${escapeHtml(addr)}</td>
                <td>${users}</td>
                <td>${escapeHtml(dataUsage)}</td>
                <td>${escapeHtml(lastSeen)}</td>
                <td class="ap-col-actions" onclick="event.stopPropagation()">
                    <div class="ap-row-actions">
                        <button type="button" class="ap-action-btn" onclick="apActions.cloneAp(${ap.id}, ${safeName})" title="${escapeHtml(T.action_clone)}" aria-label="${escapeHtml(T.action_clone)}">
                            <i data-feather="copy"></i>
                        </button>
                        <button type="button" class="ap-action-btn ap-action-danger" onclick="apActions.deleteAp(${ap.id}, ${safeName})" title="${escapeHtml(T.action_delete)}" aria-label="${escapeHtml(T.action_delete)}">
                            <i data-feather="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
    }

    function renderGroup(opts) {
        const { title, icon, members, primaryId, standalone, zoneId } = opts;
        const onlineCount  = members.filter(m => m.online_status === 'online').length;
        const offlineCount = members.length - onlineCount;

        // Zone health for the left-edge stripe
        let healthClass = '';
        if (!standalone) {
            if (offlineCount === 0)      healthClass = 'ap-group-health-online';
            else if (onlineCount === 0)  healthClass = 'ap-group-health-offline';
            else                         healthClass = 'ap-group-health-mixed';
        }

        // Rollup with clear labels — colored pills carry meaning
        const countLabel = pluralize(members.length, T.ap_singular, T.ap_plural);
        let rollupSegments = `<span>${countLabel}</span>`;
        if (!standalone) {
            if (onlineCount) {
                rollupSegments += `<span class="ap-meta-sep">·</span>` +
                    `<span class="ap-rollup-pill ap-rollup-online">${onlineCount}&nbsp;${escapeHtml(T.meta_online)}</span>`;
            }
            if (offlineCount) {
                rollupSegments += `<span class="ap-meta-sep">·</span>` +
                    `<span class="ap-rollup-pill ap-rollup-offline">${offlineCount}&nbsp;${escapeHtml(T.meta_offline)}</span>`;
            }
        }
        const meta = standalone
            ? `<span class="ap-group-meta"><span>${countLabel}</span></span>`
            : `<span class="ap-group-meta">
                ${rollupSegments}
                <span class="ap-row-actions" onclick="event.stopPropagation()">
                    <button type="button" class="ap-action-btn ap-action-danger" onclick="apActions.deleteZone(${zoneId})" title="${escapeHtml(T.action_delete)}" aria-label="${escapeHtml(T.action_delete)}">
                        <i data-feather="trash-2"></i>
                    </button>
                </span>
                <span class="ap-zone-open" aria-hidden="true">
                    <i data-feather="chevron-right"></i>
                </span>
            </span>`;

        const memberRows = members.map((ap, i) =>
            renderApRow(ap, !standalone && ap.id === primaryId, i === members.length - 1)
        ).join('');
        // When a search/filter is active, force-expand so matches are visible.
        // Otherwise honour the user's per-zone preference (default = collapsed).
        const filterActive = !!opts.filterActive;
        const userExpanded = !standalone && zoneId && expandedZones.has(zoneId);
        const isCollapsed = !standalone && zoneId && !userExpanded && !filterActive;
        const groupClasses = [
            'ap-group',
            standalone ? 'ap-group-standalone' : '',
            healthClass,
            isCollapsed ? 'collapsed' : '',
        ].filter(Boolean).join(' ');
        const dataAttr = !standalone && zoneId ? ` data-zone-id="${zoneId}"` : '';
        return `
            <tbody class="${groupClasses}"${dataAttr}>
                <tr class="ap-group-head">
                    <td colspan="7">
                        <div class="ap-group-title">
                            <span class="ap-group-chevron" role="button" aria-label="toggle"><i data-feather="chevron-down"></i></span>
                            <span class="ap-group-icon"><i data-feather="${icon}"></i></span>
                            <span class="ap-group-name">${escapeHtml(title)}</span>
                            ${meta}
                        </div>
                    </td>
                </tr>
                ${memberRows}
            </tbody>`;
    }

    function renderSummary() {
        const totalAps   = allAps.length;
        const totalZones = new Set(allAps.filter(a => a.zone?.id).map(a => a.zone.id)).size;
        document.getElementById('ap-summary').innerHTML =
            `<strong>${totalZones}</strong> ${escapeHtml(totalZones === 1 ? T.zone_singular : T.zone_plural)}` +
            ` <span class="ap-meta-sep">·</span> ` +
            `<strong>${totalAps}</strong> ${escapeHtml(totalAps === 1 ? T.ap_singular : T.ap_plural)}`;
    }

    function renderMetrics() {
        const onlineCount = allAps.filter(a => a.online_status === 'online').length;
        const totalUsers  = allAps.reduce((s, a) => s + (Number(a.users) || 0), 0);
        const totalDataGb = allAps.reduce((s, a) => s + (Number(a.data_usage_gb) || 0), 0);
        document.getElementById('ap-metric-total').textContent  = allAps.length;
        document.getElementById('ap-metric-online').textContent = onlineCount;
        document.getElementById('ap-metric-users').textContent  = totalUsers;
        document.getElementById('ap-metric-data').textContent   = totalDataGb < 1
            ? `${(totalDataGb * 1024).toFixed(0)} MB`
            : `${totalDataGb.toFixed(2)} GB`;
    }

    // Action handlers exposed globally for inline onclick. Plain prompts/
    // confirms keep this prototype lean; production would use the existing
    // SweetAlert + clone-modal infrastructure.
    window.apActions = {
        async deleteAp(id, name) {
            const msg = T.confirm_delete_ap.replace('{name}', name);
            if (!confirm(msg)) return;
            const token = (typeof UserManager !== 'undefined') ? UserManager.getToken() : null;
            try {
                const res = await fetch(APP_CONFIG.API.BASE_URL + '/locations/' + id, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                if (typeof toastr !== 'undefined') toastr.success(T.ap_deleted);
                allAps = allAps.filter(a => a.id !== id);
                renderMetrics(); renderSummary(); rerender(searchInput.value);
            } catch (err) {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error(T.action_failed);
            }
        },
        async cloneAp(id, name) {
            const token = (typeof UserManager !== 'undefined') ? UserManager.getToken() : null;
            try {
                const res = await fetch(APP_CONFIG.API.BASE_URL + '/locations/' + id + '/clone', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                if (typeof toastr !== 'undefined') toastr.success(T.ap_cloned);
                await loadData();   // refresh full list
            } catch (err) {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error(T.action_failed);
            }
        },
        async deleteZone(zoneId) {
            if (!confirm(T.confirm_delete_zone)) return;
            const token = (typeof UserManager !== 'undefined') ? UserManager.getToken() : null;
            try {
                const res = await fetch(APP_CONFIG.API.BASE_URL + '/v1/zones/' + zoneId, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                if (typeof toastr !== 'undefined') toastr.success(T.zone_deleted);
                await loadData();
            } catch (err) {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error(T.action_failed);
            }
        },
        async createZone() {
            const name = (prompt(T.create_zone_prompt) || '').trim();
            if (!name) return;
            const token = (typeof UserManager !== 'undefined') ? UserManager.getToken() : null;
            try {
                const res = await fetch(APP_CONFIG.API.BASE_URL + '/v1/zones', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name }),
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                if (typeof toastr !== 'undefined') toastr.success(T.zone_created);
                // Switch to Zones tab so the user sees the result
                activeTab = 'zones';
                document.querySelectorAll('#ap-page .mw-tab[data-ap-tab]').forEach(t =>
                    t.classList.toggle('active', t.dataset.apTab === 'zones'));
                document.querySelectorAll('#ap-page .ap-panel').forEach(p =>
                    p.classList.toggle('active', p.id === 'ap-panel-zones'));
                await loadData();
            } catch (err) {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error(T.action_failed);
            }
        },
    };

    // --- Zones tab: grouped, collapsed-by-default ------------------------
    function renderZonesTab(filter = '') {
        const table = document.getElementById('ap-zones-table');
        const q = filter.trim().toLowerCase();
        const filterActive = !!q || activeFilter !== 'all';

        // Bucket grouped APs by zone (skip standalone — they live in the other tab)
        const buckets = new Map();
        for (const ap of allAps) {
            if (!ap.zone || !ap.zone.id) continue;
            if (!apMatchesSearch(ap, q)) continue;
            if (!apMatchesChip(ap)) continue;
            if (!buckets.has(ap.zone.id)) {
                buckets.set(ap.zone.id, { zone: ap.zone, members: [] });
            }
            buckets.get(ap.zone.id).members.push(ap);
        }

        // Total zone count for the tab pill = zones the user owns regardless
        // of filtering, so the badge reads as fleet shape, not result count.
        const totalZones = new Set(allAps.filter(a => a.zone?.id).map(a => a.zone.id)).size;
        document.getElementById('ap-zones-tab-count').textContent = totalZones;

        // Strip current tbodies
        Array.from(table.querySelectorAll('tbody')).forEach(tb => tb.remove());

        const matched = Array.from(buckets.values()).reduce((s, b) => s + b.members.length, 0);
        if (!matched) {
            const empty = document.createElement('tbody');
            empty.innerHTML = `<tr><td colspan="7" class="ap-empty">${filterActive ? T.no_aps_match : T.no_aps}</td></tr>`;
            table.appendChild(empty);
            return;
        }

        const primaryByZone = new Map();
        for (const z of allZones) {
            if (z.primary_location_id) primaryByZone.set(z.id, z.primary_location_id);
        }

        const zoneGroups = Array.from(buckets.values())
            .sort((a, b) => (a.zone.name || '').localeCompare(b.zone.name || ''));

        const html = zoneGroups.map(g => renderGroup({
            title: g.zone.name || '—',
            icon: 'layers',
            members: g.members,
            primaryId: primaryByZone.get(g.zone.id),
            zoneId: g.zone.id,
            filterActive,
        })).join('');

        table.insertAdjacentHTML('beforeend', html);

        // Wire up zone header clicks (chevron = expand toggle, rest = drill)
        table.querySelectorAll('.ap-group').forEach(group => {
            const head    = group.querySelector('.ap-group-head');
            const chevron = group.querySelector('.ap-group-chevron');
            const zoneId  = group.dataset.zoneId;
            if (!head || !zoneId) return;

            if (chevron) {
                chevron.addEventListener('click', e => {
                    e.stopPropagation();
                    const nowCollapsed = group.classList.toggle('collapsed');
                    // Track *expanded* zones: empty set = all collapsed (default)
                    if (nowCollapsed) expandedZones.delete(zoneId);
                    else              expandedZones.add(zoneId);
                    saveExpanded(expandedZones);
                });
            }
            head.addEventListener('click', () => {
                window.location.href = `/${locale}/zones/${zoneId}`;
            });
        });

        if (window.feather) window.feather.replace();
    }

    // --- APs tab: flat list of standalone APs (no zone) ------------------
    function renderApsTab(filter = '') {
        const table = document.getElementById('ap-aps-table');
        const q = filter.trim().toLowerCase();
        const filterActive = !!q || activeFilter !== 'all';

        const standalone = allAps.filter(ap => (!ap.zone || !ap.zone.id)
            && apMatchesSearch(ap, q) && apMatchesChip(ap));
        const totalStandalone = allAps.filter(a => !a.zone || !a.zone.id).length;
        document.getElementById('ap-aps-tab-count').textContent = totalStandalone;

        Array.from(table.querySelectorAll('tbody')).forEach(tb => tb.remove());

        if (!standalone.length) {
            const empty = document.createElement('tbody');
            empty.innerHTML = `<tr><td colspan="7" class="ap-empty">${filterActive ? T.no_aps_match : T.no_aps}</td></tr>`;
            table.appendChild(empty);
            return;
        }

        const tbody = document.createElement('tbody');
        tbody.innerHTML = standalone.map((ap, i) =>
            renderApRow(ap, false, i === standalone.length - 1)
                // strip the tree-line indentation since these aren't in a group
                .replace('class="ap-member', 'class="ap-flat-member')
        ).join('');
        table.appendChild(tbody);

        if (window.feather) window.feather.replace();
    }

    // Re-render the active tab (called by chip / search / tab switches)
    function rerender(filter = '') {
        if (activeTab === 'zones') renderZonesTab(filter);
        else                       renderApsTab(filter);
    }

    // --- Fetch + wire up search -------------------------------------------
    // Zones are fetched purely to resolve each zone's primary AP — they're
    // not rendered as a separate list (the grouped view already exposes them).
    async function loadData() {
        const token = (typeof UserManager !== 'undefined') ? UserManager.getToken() : null;
        if (!token) { window.location.href = '/'; return; }
        const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

        try {
            const [apsRes, zonesRes] = await Promise.all([
                fetch(APP_CONFIG.API.BASE_URL + '/locations',     { headers }),
                fetch(APP_CONFIG.API.BASE_URL + '/v1/zones',      { headers }),
            ]);
            const apsJson   = await apsRes.json();
            const zonesJson = await zonesRes.json();
            allAps   = apsJson.locations || apsJson.data || [];
            allZones = zonesJson.zones    || zonesJson.data || [];
            renderMetrics();
            renderSummary();
            // Render both tabs once so their badge counts populate; the
            // CSS hides the inactive one. Subsequent re-renders only touch
            // the active tab via rerender().
            renderApsTab(document.getElementById('ap-search-aps').value);
            renderZonesTab(document.getElementById('ap-search-aps').value);
        } catch (err) {
            console.error('Access Points page: load error', err);
            const table = document.getElementById('ap-aps-table');
            Array.from(table.querySelectorAll('tbody')).forEach(tb => tb.remove());
            const fallback = document.createElement('tbody');
            fallback.innerHTML = `<tr><td colspan="7" class="ap-empty">${T.no_aps}</td></tr>`;
            table.appendChild(fallback);
        }
    }

    const searchInput = document.getElementById('ap-search-aps');
    searchInput.addEventListener('input', e => rerender(e.target.value));

    // Filter chips — single-select, mutually exclusive
    document.querySelectorAll('#ap-page .ap-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            activeFilter = chip.dataset.filter || 'all';
            document.querySelectorAll('#ap-page .ap-chip').forEach(c =>
                c.classList.toggle('active', c === chip));
            rerender(searchInput.value);
        });
    });

    // Tab switching — scoped to this page (data-ap-tab, not data-tab, so
    // the global .mw-tab handler from other pages doesn't collide)
    document.querySelectorAll('#ap-page .mw-tab[data-ap-tab]').forEach(tab => {
        tab.addEventListener('click', () => {
            activeTab = tab.dataset.apTab;
            document.querySelectorAll('#ap-page .mw-tab[data-ap-tab]').forEach(t =>
                t.classList.toggle('active', t === tab));
            document.querySelectorAll('#ap-page .ap-panel').forEach(p =>
                p.classList.toggle('active', p.id === 'ap-panel-' + activeTab));
            rerender(searchInput.value);
            // Reflect tab in URL so reloads + redirects from /zones keep state
            const url = new URL(window.location.href);
            if (activeTab === 'zones') url.searchParams.set('tab', 'zones');
            else url.searchParams.delete('tab');
            history.replaceState(null, '', url);
        });
    });

    // Sync the visible tab with the URL-derived activeTab on initial load
    if (activeTab !== 'aps') {
        document.querySelectorAll('#ap-page .mw-tab[data-ap-tab]').forEach(t =>
            t.classList.toggle('active', t.dataset.apTab === activeTab));
        document.querySelectorAll('#ap-page .ap-panel').forEach(p =>
            p.classList.toggle('active', p.id === 'ap-panel-' + activeTab));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadData);
    } else {
        loadData();
    }
})();
</script>
@endpush
