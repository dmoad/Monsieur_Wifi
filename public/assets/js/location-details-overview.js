/**
 * location-details-overview.js
 *
 * Overview tab of the Location Details page:
 *  - Current usage summary cards (download / upload / users·sessions / avg session)
 *  - Daily usage analytics chart (ApexCharts, theme-aware)
 *  - Live online users list + pagination
 *  - Location map (Leaflet, theme-aware tiles)
 *
 * Owns these globals so the shell no longer has to:
 *   analyticsChart, locationMap, onlineUsersPage, USERS_PER_PAGE, allOnlineUsers
 *
 * Depends on shell globals: API, location_id, i18n, apiFetch, formatBytes,
 * formatDuration, reRenderFeather.
 *
 * `initOverviewHandlers()` wires the overview-specific event handlers and is
 * called from the shell's initEventHandlers().
 */

'use strict';

let analyticsChart = null;
let locationMap = null;

/** @param {string} key */
function ldOverviewT(key) {
    const ld = typeof window.APP_I18N !== 'undefined' ? window.APP_I18N.location_details : null;
    return (ld && ld[key]) ? ld[key] : key;
}

function escapeHtmlOverview(str) {
    if (str == null || str === '') {
        return '';
    }
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/** @param {object} u */
function wifiBandLabel(u) {
    if (!u || !u.band) return '—';
    if (u.band === '5g') return '5 GHz';
    if (u.band === '2g') return '2.4 GHz';
    return String(u.band);
}

/** Known AP payloads: slots[].network_type e.g. password, captive_portal */
const WIFI_NET_TYPE_I18N = {
    password: 'live_users_net_type_password',
    captive_portal: 'live_users_net_type_captive_portal',
    open: 'live_users_net_type_open',
};

/**
 * Label for captive vs password-style networks in Live Users (wifi_stats + legacy).
 * @param {object} u
 * @returns {string} plain text before escape (caller escapes for HTML)
 */
function liveUserNetworkTypePlain(u) {
    if (!u) return '';
    let raw = u.network_type;
    if (raw == null || raw === '') {
        if (u.source === 'online_network_user') {
            raw = u.network === 'captive' ? 'captive_portal' : 'password';
        } else {
            return '';
        }
    }
    const key = String(raw).trim().toLowerCase();
    const i18nKey = WIFI_NET_TYPE_I18N[key];
    if (i18nKey) {
        const t = ldOverviewT(i18nKey);
        if (t !== i18nKey) {
            return t;
        }
    }
    return String(raw).replace(/_/g, ' ');
}

/** @param {number} rowStart inclusive 1-based */
function liveUsersCountRangeLabel(rowStart, rowEnd, total) {
    return ldOverviewT('live_users_count_range')
        .replace(':start', String(rowStart))
        .replace(':end', String(rowEnd))
        .replace(':total', String(total));
}

/** @param {number} totalPages */
function buildOnlineUsersPageButtonsHtml(totalPages, currentPage) {
    if (totalPages <= 1) return '';
    const maxWindow = 7;
    let winEnd = Math.min(totalPages, currentPage + Math.floor(maxWindow / 2));
    let winStart = Math.max(1, winEnd - maxWindow + 1);
    winEnd = Math.min(totalPages, winStart + maxWindow - 1);
    winStart = Math.max(1, winEnd - maxWindow + 1);

    const parts = [];
    const btn = (p) => {
        const active = p === currentPage;
        const cls = 'pagination-btn pagination-btn--page ld-online-users-page' + (active ? ' pagination-btn--active' : '');
        const attr = active ? ' disabled aria-current="page"' : '';
        return `<button type="button" class="${cls}" data-page="${p}"${attr}>${p}</button>`;
    };

    if (winStart > 1) {
        parts.push(btn(1));
        if (winStart > 2) {
            parts.push('<span class="ld-online-users-pages-ellipsis">…</span>');
        }
    }
    for (let p = winStart; p <= winEnd; p++) {
        parts.push(btn(p));
    }
    if (winEnd < totalPages) {
        if (winEnd < totalPages - 1) {
            parts.push('<span class="ld-online-users-pages-ellipsis">…</span>');
        }
        parts.push(btn(totalPages));
    }
    return parts.join('');
}

function scrollOnlineUsersListTop() {
    const el = document.getElementById('online-users-list');
    if (el) el.scrollTop = 0;
}

/**
 * @param {object} u
 * @param {number} serial 1-based index including pagination offset
 */
function buildLiveUserCardHtml(u, serial) {
    const mac = escapeHtmlOverview(u.mac_address || u.mac || '—');
    const ip = escapeHtmlOverview(u.ip || '—');
    const host = (u.hostname && String(u.hostname).trim()) ? String(u.hostname).trim() : '';
    const user = (u.username && String(u.username).trim()) ? String(u.username).trim() : '';
    const deviceName = escapeHtmlOverview(host || user || ldOverviewT('live_users_unknown_device'));
    const networkLabel = u.source === 'wifi_stats'
        ? escapeHtmlOverview(u.ssid || u.network_label || '—')
        : escapeHtmlOverview(u.network_label || u.network || '—');
    const networkTypePlain = liveUserNetworkTypePlain(u);
    const networkTypeHtml = networkTypePlain
        ? `<span class="ld-live-user__net-type" title="${escapeHtmlOverview(ldOverviewT('live_users_network_type_abbr'))}">${escapeHtmlOverview(networkTypePlain)}</span>`
        : '';

    let durationRight = '';
    if (u.source === 'wifi_stats' && u.session_time != null) {
        durationRight = formatDuration(u.session_time);
    } else if (u.connected_time) {
        durationRight = escapeHtmlOverview(u.connected_time);
    }

    const moreLabel = escapeHtmlOverview(ldOverviewT('live_users_more'));
    const wifiMode = u.source === 'wifi_stats';

    let expandInner = '';
    if (wifiMode) {
        const bandEscaped = escapeHtmlOverview(wifiBandLabel(u));
        const rssiAvg = u.signal_avg_dbm != null ? `${u.signal_avg_dbm} dBm` : '—';
        const snr = u.snr_db != null ? `${u.snr_db} dB` : '—';
        const idleMs = u.inactive_time_ms != null ? Number(u.inactive_time_ms) : 0;
        let idleStr;
        if (idleMs >= 1000) {
            idleStr = formatDuration(Math.floor(idleMs / 1000));
        } else {
            idleStr = `${idleMs} ms`;
        }

        const rssiInst = u.signal_dbm != null ? `${u.signal_dbm} dBm` : '—';
        const retries = u.tx_retries != null ? String(u.tx_retries) : '—';
        const failed = u.tx_failed != null ? String(u.tx_failed) : '—';

        expandInner = `
<div class="ld-live-user__expand-inner">
    <p class="ld-live-user__expand-line">
        <span class="ld-live-user__expand-k">${escapeHtmlOverview(ldOverviewT('live_users_radio'))}:</span> ${bandEscaped}</p>
    <p class="ld-live-user__expand-line">${escapeHtmlOverview(ldOverviewT('live_users_wifi_metric_rssi'))}: ${escapeHtmlOverview(rssiAvg)}
        <span class="ld-live-user__sep">·</span> ${escapeHtmlOverview(ldOverviewT('live_users_wifi_metric_snr'))}: ${escapeHtmlOverview(snr)}
        <span class="ld-live-user__sep">·</span> ${escapeHtmlOverview(ldOverviewT('live_users_wifi_metric_idle'))}: ${escapeHtmlOverview(idleStr)}</p>
    <p class="ld-live-user__expand-line">${escapeHtmlOverview(ldOverviewT('live_users_instant_rssi'))}: ${escapeHtmlOverview(rssiInst)}</p>
    <p class="ld-live-user__expand-line">${escapeHtmlOverview(ldOverviewT('live_users_retries'))}: ${escapeHtmlOverview(retries)}
        <span class="ld-live-user__sep">·</span> ${escapeHtmlOverview(ldOverviewT('live_users_failures'))}: ${escapeHtmlOverview(failed)}</p>
</div>`;
    } else {
        expandInner = `
<div class="ld-live-user__expand-inner">
    <p class="ld-live-user__expand-line text-muted">${escapeHtmlOverview(ldOverviewT('live_users_legacy_sync_hint'))}</p>
</div>`;
    }

    const serialDisp = escapeHtmlOverview(String(serial).padStart(2, '0'));

    return `
<div class="ld-live-user">
    <div class="ld-live-user__row">
        <span class="ld-live-user__serial" title="${escapeHtmlOverview(ldOverviewT('live_users_serial_abbr'))}">${serialDisp}</span>
        <div class="ld-live-user__main">
            <div class="ld-live-user__line1">
                <span class="ld-live-user__name">${deviceName}</span>
                <span class="ld-live-user__duration">${durationRight}</span>
            </div>
            <div class="ld-live-user__meta">
                <span class="ld-live-user__mono">${mac}</span>
                <span class="ld-live-user__sep">·</span>
                <span class="ld-live-user__mono">${ip}</span>
                <span class="ld-live-user__sep">·</span>
                <span class="ld-live-user__ssid" title="${networkLabel}">${networkLabel}</span>
                ${networkTypeHtml ? `<span class="ld-live-user__sep">·</span>${networkTypeHtml}` : ''}
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary ld-live-user__more" aria-expanded="false">${moreLabel}</button>
    </div>
    <div class="ld-live-user__expand">${expandInner}</div>
</div>`;
}

// ============================================================================
// USAGE STATS
// ============================================================================

/**
 * Compact byte label for Current Usage: whole numbers only, KB/MB/GB/TB, length ≤ 6 chars.
 *
 * @param {number} bytes
 * @returns {string}
 */
function formatCurrentUsageBytes(bytes) {
    const x = Number(bytes);
    if (!Number.isFinite(x) || x <= 0) {
        return '0 B';
    }
    const n = Math.floor(x);
    if (n === 0) {
        return '0 B';
    }

    const k = 1024;
    const UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = Math.min(Math.floor(Math.log(n) / Math.log(k)), UNITS.length - 1);
    if (i < 0) {
        i = 0;
    }

    for (;;) {
        let v = Math.round(n / Math.pow(k, i));
        while (v >= k && i < UNITS.length - 1) {
            i += 1;
            v = Math.round(n / Math.pow(k, i));
        }
        const u = UNITS[i];
        const s = u === 'B' ? `${v} B` : `${v}${u}`;
        if (s.length <= 6) {
            return s;
        }
        if (i < UNITS.length - 1) {
            i += 1;
            continue;
        }
        let tv = v;
        while (`${tv}${u}`.length > 6 && tv >= 10) {
            tv = Math.floor(tv / 10);
        }
        return `${tv}${u}`;
    }
}

async function loadCurrentUsage(period) {
    $('#usage-loading').show();
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/captive-portal/daily-usage?period=${period}`);
        const data = res.data || {};

        $('#download-usage').text(formatCurrentUsageBytes(data.total_download || 0));
        $('#upload-usage').text(formatCurrentUsageBytes(data.total_upload || 0));
        $('#users-sessions-count').text(`${data.unique_users || 0} / ${data.total_sessions || 0}`);
        $('#avg-session-time').text(formatDuration(data.avg_session_seconds || 0));
        $('#usage-last-updated').text('Updated: ' + new Date().toLocaleTimeString());

        if (data.daily_stats) renderAnalyticsChart(data.daily_stats);
        $('#total-users').text(data.unique_users || '-');
        $('#total-sessions').text(data.total_sessions || '-');
        $('#avg-daily').text(data.avg_daily_users || '-');
        $('.connected_users').text(data.unique_users || 0);
        $('.daily_usage').text(formatBytes((data.total_download || 0) + (data.total_upload || 0)));
    } catch (err) {
        console.error('Usage load error:', err);
        $('#download-usage, #upload-usage, #users-sessions-count, #avg-session-time').text('N/A');
    } finally {
        $('#usage-loading').hide();
    }
}

function renderAnalyticsChart(dailyStats) {
    const categories = dailyStats.map(d => d.date);
    const seriesUsers    = dailyStats.map(d => d.unique_users || 0);
    const seriesSessions = dailyStats.map(d => d.sessions || 0);
    const usersLabel    = ldOverviewT('chart_series_users');
    const sessionsLabel = ldOverviewT('chart_series_sessions');

    const series = [
        { name: usersLabel,    data: seriesUsers },
        { name: sessionsLabel, data: seriesSessions },
    ];
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';
    const options = {
        theme: { mode: dark ? 'dark' : 'light' },
        chart: { type: 'area', height: 300, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis: { categories },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
        colors: ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend: { show: true, position: 'top' },
        grid: { borderColor: gridColor },
        tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        yaxis: { labels: { formatter: val => Math.round(val) } },
    };
    if (analyticsChart) {
        analyticsChart.updateOptions({
            series,
            xaxis: { categories },
            colors: ['#667eea', '#43d39e'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
            grid: { borderColor: gridColor },
            theme: { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        });
    } else {
        analyticsChart = new ApexCharts(document.querySelector('#daily-usage-chart'), options);
        analyticsChart.render();
    }
}

new MutationObserver(function () {
    if (!analyticsChart) return;
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    analyticsChart.updateOptions({
        theme: { mode: dark ? 'dark' : 'light' },
        tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        grid: { borderColor: dark ? 'var(--mw-border)' : '#f1f1f1' },
    });
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

// ============================================================================
// ONLINE USERS
// ============================================================================

let onlineUsersPage = 1;
const USERS_PER_PAGE = 10;
let allOnlineUsers = [];

async function loadOnlineUsers() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/online-users`);
        const payload = res.data || {};
        allOnlineUsers = Array.isArray(payload.online_users) ? payload.online_users : [];
        onlineUsersPage = 1;
        renderOnlineUsers();
    } catch (err) {
        $('#online-users-list').html('<div class="text-center text-muted p-3"><small>Could not load online users</small></div>');
        $('#users-pagination').hide();
        $('#page-numbers').empty();
        $('#count-range').hide().text('');
    }
}

function renderOnlineUsers() {
    const start = (onlineUsersPage - 1) * USERS_PER_PAGE;
    const pageUsers = allOnlineUsers.slice(start, start + USERS_PER_PAGE);
    const total = allOnlineUsers.length;

    $('#online-count').text(total);

    if (total === 0) {
        $('#online-users-list').html(
            `<div class="text-center text-muted p-3"><i data-feather="wifi-off" style="width:30px;height:30px;margin-bottom:8px;"></i>` +
            `<div><small>${escapeHtmlOverview(ldOverviewT('live_users_empty'))}</small></div></div>`
        );
        $('#users-pagination').hide();
        $('#page-numbers').empty();
        $('#count-range').hide().text('');
        reRenderFeather();
        return;
    }

    const totalPages = Math.ceil(total / USERS_PER_PAGE);
    const html = pageUsers.map((u, idx) => buildLiveUserCardHtml(u, start + idx + 1)).join('');

    $('#online-users-list').html(html);
    scrollOnlineUsersListTop();

    const fromRow = start + 1;
    const toRow = Math.min(start + USERS_PER_PAGE, total);
    $('#count-range').text(liveUsersCountRangeLabel(fromRow, toRow, total)).show();

    $('#page-info').text(
        ldOverviewT('live_users_page_of').replace(':page', String(onlineUsersPage)).replace(':pages', String(totalPages))
    );
    $('#page-numbers').html(buildOnlineUsersPageButtonsHtml(totalPages, onlineUsersPage));
    $('#prev-page').prop('disabled', onlineUsersPage === 1);
    $('#next-page').prop('disabled', onlineUsersPage === totalPages);

    if (totalPages > 1) {
        $('#users-pagination').show();
    } else {
        $('#users-pagination').hide();
        $('#page-numbers').empty();
    }
    reRenderFeather();
}

// ============================================================================
// MAP
// ============================================================================

function initMap(lat, lng, label) {
    if (locationMap) { locationMap.remove(); locationMap = null; }
    try {
        locationMap = L.map('location-map', { zoomControl: true }).setView([lat, lng], 13);
        let locTileLayer = null;
        function applyLocTiles() {
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (locTileLayer) locationMap.removeLayer(locTileLayer);
            locTileLayer = L.tileLayer(
                dark ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                     : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                { attribution: dark ? '&copy; OpenStreetMap contributors &copy; CARTO' : '&copy; OpenStreetMap contributors',
                  subdomains: dark ? 'abcd' : 'abc', maxZoom: 19 }
            ).addTo(locationMap);
        }
        applyLocTiles();
        new MutationObserver(applyLocTiles)
            .observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        L.marker([lat, lng]).addTo(locationMap).bindPopup(label || '').openPopup();
        $('#map-coordinates').text(`${lat.toFixed(4)}, ${lng.toFixed(4)}`).show();
    } catch (e) {
        console.warn('Map init failed', e);
    }
}

// ============================================================================
// EVENT HANDLERS
// ============================================================================

function initOverviewHandlers() {
    // Usage period
    $('#usage-period-dropdown .dropdown-item').on('click', function (e) {
        e.preventDefault();
        currentUsagePeriod = $(this).data('period');
        $('#usage-period-btn').text($(this).text());
        loadCurrentUsage(currentUsagePeriod);
    });

    // Analytics period buttons
    $(document).on('click', '.period-btn', function () {
        $('.period-btn').css({ background: 'transparent', color: '#6c757d' });
        $(this).css({ background: 'linear-gradient(135deg,#667eea,#764ba2)', color: 'white' });
        const days = $(this).data('period');
        const period = days + 'days';
        currentUsagePeriod = period;
        // Sync dropdown label if there's a matching item, otherwise show the period
        const $match = $(`#usage-period-dropdown .dropdown-item[data-period="${period}"]`);
        if ($match.length) {
            $('#usage-period-btn').text($match.text());
        } else {
            $('#usage-period-btn').text(days + 'D');
        }
        loadCurrentUsage(period);
    });

    // Online users pagination
    $('#refresh-online-users').on('click', loadOnlineUsers);
    $('#prev-page').on('click', function () {
        if (onlineUsersPage > 1) { onlineUsersPage--; renderOnlineUsers(); }
    });
    $('#next-page').on('click', function () {
        const total = Math.ceil(allOnlineUsers.length / USERS_PER_PAGE);
        if (onlineUsersPage < total) { onlineUsersPage++; renderOnlineUsers(); }
    });

    $(document).on('click', '.ld-online-users-page', function (e) {
        e.preventDefault();
        const $btn = $(this);
        if ($btn.prop('disabled')) return;
        const p = parseInt($btn.attr('data-page'), 10);
        if (!Number.isFinite(p)) return;
        const totalPages = Math.ceil(allOnlineUsers.length / USERS_PER_PAGE);
        if (p < 1 || p > totalPages) return;
        onlineUsersPage = p;
        renderOnlineUsers();
    });

    $(document).on('click', '#online-users-list .ld-live-user__more', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const $card = $btn.closest('.ld-live-user');
        const open = $card.toggleClass('is-open').hasClass('is-open');
        $btn.attr('aria-expanded', open ? 'true' : 'false');
        $btn.text(open ? ldOverviewT('live_users_less') : ldOverviewT('live_users_more'));
    });
}
