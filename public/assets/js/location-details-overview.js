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

// ============================================================================
// USAGE STATS
// ============================================================================

async function loadCurrentUsage(period) {
    $('#usage-loading').show();
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/captive-portal/daily-usage?period=${period}`);
        const data = res.data || {};

        $('#download-usage').text(formatBytes(data.total_download || 0));
        $('#upload-usage').text(formatBytes(data.total_upload || 0));
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
    const series = [{ name: 'Users', data: dailyStats.map(d => d.unique_users || 0) }];
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    const options = {
        theme: { mode: dark ? 'dark' : 'light' },
        chart: { type: 'area', height: 300, toolbar: { show: false }, background: 'transparent' },
        series, xaxis: { categories },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
        colors: ['#667eea'],
        dataLabels: { enabled: false },
        grid: { borderColor: dark ? 'var(--mw-border)' : '#f1f1f1' },
        tooltip: { theme: dark ? 'dark' : 'light' },
    };
    if (analyticsChart) {
        analyticsChart.updateOptions({ series, xaxis: { categories } });
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
        tooltip: { theme: dark ? 'dark' : 'light' },
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
        allOnlineUsers = res.data || [];
        onlineUsersPage = 1;
        renderOnlineUsers();
    } catch (err) {
        $('#online-users-list').html('<div class="text-center text-muted p-3"><small>Could not load online users</small></div>');
    }
}

function renderOnlineUsers() {
    const start = (onlineUsersPage - 1) * USERS_PER_PAGE;
    const pageUsers = allOnlineUsers.slice(start, start + USERS_PER_PAGE);
    const total = allOnlineUsers.length;

    $('#online-count').text(total);

    if (total === 0) {
        $('#online-users-list').html('<div class="text-center text-muted p-3"><i data-feather="wifi-off" style="width:30px;height:30px;margin-bottom:8px;"></i><div><small>No users currently connected</small></div></div>');
        reRenderFeather();
        return;
    }

    const totalPages = Math.ceil(total / USERS_PER_PAGE);
    const html = pageUsers.map(u => `
        <div class="user-item">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:14px;margin-right:12px;">
                        ${(u.username || u.mac_address || 'U').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h6 style="margin:0;font-size:1rem;font-weight:600;">${u.username || u.mac_address || 'Unknown'}</h6>
                        <small style="color:#7f8c8d;">${u.mac_address || ''}</small>
                    </div>
                </div>
                <small style="color:#7f8c8d;">${u.session_time ? formatDuration(u.session_time) : ''}</small>
            </div>
        </div>`).join('');

    $('#online-users-list').html(html);

    if (totalPages > 1) {
        $('#users-pagination').show();
        $('#page-info').text(`${onlineUsersPage} / ${totalPages}`);
        $('#prev-page').prop('disabled', onlineUsersPage === 1);
        $('#next-page').prop('disabled', onlineUsersPage === totalPages);
    } else {
        $('#users-pagination').hide();
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
        loadCurrentUsage(days + 'days');
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
}
