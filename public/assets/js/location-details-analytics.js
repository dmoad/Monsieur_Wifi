/**
 * location-details-analytics.js
 *
 * Analytics tab of the Location Details page:
 *  - Leaflet map (coordinates from shell's currentDeviceData)
 *  - Hourly bandwidth line chart (last 24 h, from UserDeviceLoginSession)
 *  - Per-day download/upload area chart (7D/30D/90D, reuses captive-portal/daily-usage)
 *  - Device-type donut chart (GuestNetworkUser)
 *  - Paginated guest-user table (GuestNetworkUser)
 *
 * Depends on shell globals: API, location_id, currentDeviceData, apiFetch,
 *   formatBytes, formatDuration, reRenderFeather.
 *
 * `initAnalyticsHandlers()` is called from the shell's initEventHandlers().
 */

'use strict';

// ============================================================================
// MODULE STATE
// ============================================================================

let analyticsHourlyChart        = null;
let analyticsUserSessionsChart  = null;
let analyticsDailyChart         = null;
let analyticsDeviceChart        = null;
let analyticsLoaded        = false;
let analyticsCurrentPeriod = '7days';
let analyticsUsersPage     = 1;
let analyticsUsersTotal    = 0;
let analyticsUsersLastPage = 1;
let analyticsUsersSearch   = '';
let analyticsUsersPerPage  = 10;
let analyticsSearchTimer   = null;

// ============================================================================
// I18N HELPER (mirrors ldOverviewT pattern)
// ============================================================================

/** @param {string} key */
function ldAnalyticsT(key) {
    const ld = typeof window.APP_I18N !== 'undefined' ? window.APP_I18N.location_details : null;
    return (ld && ld[key]) ? ld[key] : key;
}

// ============================================================================
// ENTRY POINT — called once when Analytics tab is first activated
// ============================================================================

function loadAnalyticsTab() {
    if (analyticsLoaded) return;
    analyticsLoaded = true;

    const perSel = document.getElementById('analytics-users-per-page');
    if (perSel && perSel.value) {
        analyticsUsersPerPage = parseInt(perSel.value, 10) || 10;
    }

    loadHourlyBandwidth();
    loadDailyBandwidth(analyticsCurrentPeriod);
    loadDeviceTypes();
    loadAnalyticsUsers(1, '');
}

// ============================================================================
// HOURLY BANDWIDTH CHART
// ============================================================================

async function loadHourlyBandwidth() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/analytics/hourly-bandwidth`);
        if (res.success) {
            renderHourlyChart(res.data || []);
            const ts = new Date().toLocaleTimeString();
            const el = document.getElementById('analytics-hourly-updated');
            if (el) el.textContent = ts;
        }
    } catch (err) {
        console.error('Hourly bandwidth load error:', err);
    }
}

function renderHourlyChart(buckets) {
    const categories = buckets.map(b => b.hour);

    // Convert total bytes accumulated over one hour → average Mbps for that hour:
    //   bytes  ×  8 bits/byte  ÷  3600 s/hour  ÷  1,000,000 bits/Mbit
    const BYTES_TO_MBPS = 8 / (3600 * 1_000_000);
    const dlMbps = buckets.map(b => parseFloat(((b.download || 0) * BYTES_TO_MBPS).toFixed(3)));
    const ulMbps = buckets.map(b => parseFloat(((b.upload   || 0) * BYTES_TO_MBPS).toFixed(3)));

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const series = [
        { name: ldAnalyticsT('analytics_series_download'), data: dlMbps },
        { name: ldAnalyticsT('analytics_series_upload'),   data: ulMbps },
    ];

    const options = {
        theme:       { mode: dark ? 'dark' : 'light' },
        chart:       { type: 'line', height: 220, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:       { categories, labels: { rotate: -45, style: { fontSize: '10px' } } },
        stroke:      { curve: 'smooth', width: 2 },
        colors:      ['#667eea', '#43d39e'],
        dataLabels:  { enabled: false },
        legend:      { show: true, position: 'top' },
        grid:        { borderColor: gridColor },
        tooltip:     { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                       y: { formatter: val => `${val} Mbps` } },
        yaxis:       { labels: { formatter: val => `${val}` }, title: { text: 'Mbps', style: { fontSize: '11px' } } },
        markers:     { size: 3 },
    };

    if (analyticsHourlyChart) {
        analyticsHourlyChart.updateOptions({
            series,
            xaxis:   { categories },
            colors:  ['#667eea', '#43d39e'],
            grid:    { borderColor: gridColor },
            theme:   { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                       y: { formatter: val => `${val} Mbps` } },
        });
    } else {
        const el = document.querySelector('#analytics-hourly-chart');
        if (!el) return;
        analyticsHourlyChart = new ApexCharts(el, options);
        analyticsHourlyChart.render();
    }
}

// ============================================================================
// DAILY BANDWIDTH CHART (reuses captive-portal/daily-usage endpoint)
// ============================================================================

async function loadDailyBandwidth(period) {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/captive-portal/daily-usage?period=${period}`);
        if (res.data && res.data.daily_stats) {
            renderDailyChart(res.data.daily_stats);
            renderUsersSessionsChart(res.data.daily_stats);
        }
    } catch (err) {
        console.error('Daily bandwidth load error:', err);
    }
}

function renderDailyChart(dailyStats) {
    const categories = dailyStats.map(d => d.date);
    const dlData     = dailyStats.map(d => d.total_download || 0);
    const ulData     = dailyStats.map(d => d.total_upload   || 0);

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const fmtBytes = val => {
        if (!val) return '0 B';
        const k = 1024, units = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.min(Math.floor(Math.log(val) / Math.log(k)), units.length - 1);
        return Math.round(val / Math.pow(k, i)) + units[i];
    };

    const series = [
        { name: ldAnalyticsT('analytics_series_download'), data: dlData },
        { name: ldAnalyticsT('analytics_series_upload'),   data: ulData },
    ];

    const options = {
        theme:      { mode: dark ? 'dark' : 'light' },
        chart:      { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:      { categories },
        stroke:     { curve: 'smooth', width: 2 },
        fill:       { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
        colors:     ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend:     { show: true, position: 'top' },
        grid:       { borderColor: gridColor },
        tooltip:    { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                      y: { formatter: fmtBytes } },
        yaxis:      { labels: { formatter: fmtBytes } },
    };

    if (analyticsDailyChart) {
        analyticsDailyChart.updateOptions({
            series,
            xaxis:   { categories },
            colors:  ['#667eea', '#43d39e'],
            fill:    { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
            grid:    { borderColor: gridColor },
            theme:   { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                       y: { formatter: fmtBytes } },
        });
    } else {
        const el = document.querySelector('#analytics-daily-chart');
        if (!el) return;
        analyticsDailyChart = new ApexCharts(el, options);
        analyticsDailyChart.render();
    }
}

// ============================================================================
// USERS & SESSIONS CHART (shares daily_stats from captive-portal/daily-usage)
// ============================================================================

function renderUsersSessionsChart(dailyStats) {
    const categories     = dailyStats.map(d => d.date);
    const seriesUsers    = dailyStats.map(d => d.unique_users || 0);
    const seriesSessions = dailyStats.map(d => d.sessions     || 0);

    const usersLabel    = ldAnalyticsT('analytics_series_users');
    const sessionsLabel = ldAnalyticsT('analytics_series_sessions');
    const series        = [
        { name: usersLabel,    data: seriesUsers },
        { name: sessionsLabel, data: seriesSessions },
    ];

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const options = {
        theme:      { mode: dark ? 'dark' : 'light' },
        chart:      { type: 'area', height: 220, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:      { categories },
        stroke:     { curve: 'smooth', width: 2 },
        fill:       { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
        colors:     ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend:     { show: true, position: 'top' },
        grid:       { borderColor: gridColor },
        tooltip:    { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        yaxis:      { labels: { formatter: val => Math.round(val) } },
    };

    if (analyticsUserSessionsChart) {
        analyticsUserSessionsChart.updateOptions({
            series,
            xaxis:   { categories },
            colors:  ['#667eea', '#43d39e'],
            fill:    { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
            grid:    { borderColor: gridColor },
            theme:   { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        });
    } else {
        const el = document.querySelector('#analytics-users-sessions-chart');
        if (!el) return;
        analyticsUserSessionsChart = new ApexCharts(el, options);
        analyticsUserSessionsChart.render();
    }
}

// ============================================================================
// DEVICE TYPE DONUT CHART
// ============================================================================

async function loadDeviceTypes() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/analytics/device-types`);
        if (res.success) {
            renderDeviceTypeChart(res.data || []);
        }
    } catch (err) {
        console.error('Device types load error:', err);
    }
}

function renderDeviceTypeChart(data) {
    const emptyEl = document.getElementById('analytics-device-type-empty');
    const chartEl = document.getElementById('analytics-device-type-chart');

    if (!data || data.length === 0) {
        if (chartEl) chartEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
        return;
    }

    if (emptyEl) emptyEl.style.display = 'none';
    if (chartEl) chartEl.style.display = 'block';

    const labels  = data.map(d => d.type);
    const counts  = data.map(d => d.count);
    const dark    = document.documentElement.getAttribute('data-theme') === 'dark';

    const options = {
        theme:  { mode: dark ? 'dark' : 'light' },
        chart:  { type: 'donut', height: 300, background: 'transparent' },
        series: counts,
        labels,
        colors: ['#667eea', '#43d39e', '#f7971e', '#ee0979', '#36d1dc', '#5b86e5', '#a8edea'],
        legend: { show: true, position: 'bottom', fontSize: '12px', horizontalAlign: 'center' },
        dataLabels: { enabled: true, formatter: (val) => `${Math.round(val)}%`,
                      dropShadow: { enabled: false } },
        plotOptions: { pie: { donut: { size: '55%' }, expandOnClick: false } },
        tooltip: { theme: dark ? 'dark' : 'light',
                   y: { formatter: (val, { seriesIndex, w }) => `${val} (${w.globals.seriesNames[seriesIndex]})` } },
    };

    if (analyticsDeviceChart) {
        analyticsDeviceChart.updateOptions({
            series: counts,
            labels,
            theme:  { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light' },
        });
    } else {
        if (!chartEl) return;
        analyticsDeviceChart = new ApexCharts(chartEl, options);
        analyticsDeviceChart.render();
    }
}

// ============================================================================
// GUEST USER TABLE
// ============================================================================

async function loadAnalyticsUsers(page, search) {
    analyticsUsersPage   = page;
    analyticsUsersSearch = search;

    const loadingEl = document.getElementById('analytics-users-loading');
    if (loadingEl) loadingEl.style.display = 'block';

    try {
        let url = `${API}/locations/${location_id}/analytics/users?page=${page}&per_page=${analyticsUsersPerPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        const res = await apiFetch(url);
        if (res.success && res.data) {
            const { data, current_page, last_page, total, per_page } = res.data;
            analyticsUsersTotal    = total;
            analyticsUsersLastPage = last_page;
            if (typeof per_page === 'number') analyticsUsersPerPage = per_page;
            renderUsersTable(data || []);
            renderUsersPagination(current_page, last_page, total, per_page);
        }
    } catch (err) {
        console.error('Analytics users load error:', err);
        const tbody = document.getElementById('analytics-users-tbody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4"><small>${ldAnalyticsT('analytics_users_error')}</small></td></tr>`;
        }
    } finally {
        if (loadingEl) loadingEl.style.display = 'none';
    }
}

function renderUsersTable(users) {
    const tbody = document.getElementById('analytics-users-tbody');
    if (!tbody) return;

    if (users.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4"><small>${ldAnalyticsT('analytics_users_empty')}</small></td></tr>`;
        return;
    }

    const escHtml = s => {
        if (s == null || s === '') return '—';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const fmtDate = s => {
        if (!s) return '—';
        try { return new Date(s).toLocaleDateString(); } catch { return escHtml(s); }
    };

    tbody.innerHTML = users.map(u => {
        const badge = u.blocked
            ? `<span class="badge badge-danger">${ldAnalyticsT('analytics_status_blocked')}</span>`
            : `<span class="badge badge-success">${ldAnalyticsT('analytics_status_active')}</span>`;

        return `<tr>
            <td>${escHtml(u.name)}</td>
            <td><code style="font-size:0.75rem;">${escHtml(u.mac_address)}</code></td>
            <td>${escHtml(u.email)}</td>
            <td>${escHtml(u.device_type)}</td>
            <td>${escHtml(u.os)}</td>
            <td>${u.session_count || 0}</td>
            <td>${fmtDate(u.last_seen)}</td>
            <td>${badge}</td>
        </tr>`;
    }).join('');

    reRenderFeather();
}

function renderUsersPagination(currentPage, lastPage, total, perPage) {
    const paginationEl = document.getElementById('analytics-users-pagination');
    const countEl      = document.getElementById('analytics-users-count-range');
    const pageInfoEl   = document.getElementById('analytics-users-page-info');
    const prevBtn      = document.getElementById('analytics-users-prev');
    const nextBtn      = document.getElementById('analytics-users-next');
    const totalEl      = document.getElementById('analytics-users-total');

    if (totalEl) totalEl.textContent = total || '';

    if (!paginationEl) return;

    const pp = typeof perPage === 'number' && perPage > 0 ? perPage : analyticsUsersPerPage;

    if (!total || total <= 0) {
        paginationEl.style.display = 'none';
        return;
    }

    paginationEl.style.display = 'flex';
    const start = (currentPage - 1) * pp + 1;
    const end   = Math.min(currentPage * pp, total);
    if (countEl) countEl.textContent = `${start}–${end} / ${total}`;
    if (pageInfoEl) pageInfoEl.textContent = `${currentPage} / ${lastPage}`;
    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= lastPage;
}

// ============================================================================
// THEME OBSERVER (keeps all three charts in sync)
// ============================================================================

new MutationObserver(function () {
    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const theme     = { mode: dark ? 'dark' : 'light' };
    const tooltip   = { theme: dark ? 'dark' : 'light' };
    const grid      = { borderColor: dark ? 'var(--mw-border)' : '#f1f1f1' };

    if (analyticsHourlyChart) {
        analyticsHourlyChart.updateOptions({ theme, tooltip, grid });
    }
    if (analyticsUserSessionsChart) {
        analyticsUserSessionsChart.updateOptions({ theme, tooltip, grid });
    }
    if (analyticsDailyChart) {
        analyticsDailyChart.updateOptions({ theme, tooltip, grid });
    }
    if (analyticsDeviceChart) {
        analyticsDeviceChart.updateOptions({ theme, tooltip });
    }
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

// ============================================================================
// EVENT HANDLERS (wired by shell's initEventHandlers)
// ============================================================================

function initAnalyticsHandlers() {
    // Period buttons for daily chart
    $(document).on('click', '.analytics-period-btn', function () {
        $('.analytics-period-btn').css({ background: 'transparent', color: '#6c757d' });
        $(this).css({ background: 'var(--mw-primary)', color: 'white' });
        const days = $(this).data('period');
        analyticsCurrentPeriod = days + 'days';
        loadDailyBandwidth(analyticsCurrentPeriod);
    });

    // User search — debounced
    $(document).on('input', '#analytics-user-search', function () {
        clearTimeout(analyticsSearchTimer);
        const val = $(this).val().trim();
        analyticsSearchTimer = setTimeout(() => {
            loadAnalyticsUsers(1, val);
        }, 350);
    });

    $(document).on('change', '#analytics-users-per-page', function () {
        analyticsUsersPerPage = parseInt($(this).val(), 10) || 10;
        loadAnalyticsUsers(1, analyticsUsersSearch);
    });

    // Refresh users
    $(document).on('click', '#analytics-users-refresh', function () {
        loadAnalyticsUsers(1, analyticsUsersSearch);
        loadHourlyBandwidth();
        loadDeviceTypes();
        loadDailyBandwidth(analyticsCurrentPeriod);
    });

    // Pagination
    $(document).on('click', '#analytics-users-prev', function () {
        if (analyticsUsersPage > 1) {
            loadAnalyticsUsers(analyticsUsersPage - 1, analyticsUsersSearch);
        }
    });

    $(document).on('click', '#analytics-users-next', function () {
        if (analyticsUsersPage < analyticsUsersLastPage) {
            loadAnalyticsUsers(analyticsUsersPage + 1, analyticsUsersSearch);
        }
    });
}
