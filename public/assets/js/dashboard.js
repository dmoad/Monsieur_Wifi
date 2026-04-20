const dashLocale = document.documentElement.lang || 'en';

let allLocations = [];
let currentLocationFilter = 'all';
let networkMap = null;
let dataUsageChart = null;

// ── API calls ──────────────────────────────────────────────────────────────

function loadDashboardOverview() {
    const token = UserManager.getToken();
    if (!token) return;

    $.ajax({
        url: '/api/dashboard/overview',
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        success: function(response) {
            if (response.success) {
                updateOverviewDisplay(response.data);
                updateLocationCards(response.data.locations.data);
            } else {
                showOverviewError();
            }
        },
        error: function() { showOverviewError(); }
    });
}

function loadAnalytics(period) {
    period = period || '7';
    const token = UserManager.getToken();
    if (!token) return;

    $.ajax({
        url: '/api/dashboard/analytics',
        method: 'GET',
        data: { period: period },
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        success: function(response) {
            if (response.success) {
                updateAnalyticsDisplay(response.data);
            } else {
                showAnalyticsError();
            }
        },
        error: function() { showAnalyticsError(); }
    });
}

function loadDataUsageTrends(period) {
    period = period || '7';
    const token = UserManager.getToken();
    if (!token) return;

    showDataUsageLoading(true);

    $.ajax({
        url: '/api/dashboard/data-usage-trends',
        method: 'GET',
        data: { period: period },
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        success: function(response) {
            if (response.success) {
                updateDataUsageChart(response.data);
                updateDataUsageStats(response.data);
            } else {
                showDataUsageError();
            }
        },
        error: function() { showDataUsageError(); },
        complete: function() { showDataUsageLoading(false); }
    });
}

// ── Display updaters ───────────────────────────────────────────────────────

function updateOverviewDisplay(data) {
    const loc = data.locations;
    const ns = data.network_stats;

    $('#welcome-total-locations').text(loc.total + ' Location' + (loc.total !== 1 ? 's' : ''));
    $('#welcome-active-count').text(loc.online);
    $('#welcome-offline-count').text(loc.offline);
    $('#routers-online-count').text(ns.routers_online + '/' + ns.routers_total);
    $('#active-users-count').text(ns.active_users.toLocaleString());
    $('#data-used-count').text(ns.data_used_tb + 'TB');
    $('#uptime-percentage').text(ns.uptime_percentage + '%');

    initializeNetworkMap(loc.data);
}

function updateAnalyticsDisplay(data) {
    const a = data.analytics;
    $('#analytics-total-users').text(a.total_users.toLocaleString());
    $('#analytics-data-usage').text(a.data_usage_gb + ' GB');
    $('#analytics-uptime').text(a.uptime + '%');
    $('#analytics-sessions').text(a.total_sessions.toLocaleString());
}

function updateDataUsageStats(data) {
    const fmt = function(gb) { return gb >= 1024 ? (gb / 1024).toFixed(1) + ' TB' : gb.toFixed(1) + ' GB'; };
    $('#total-bandwidth-used').text(fmt(data.total_usage_gb));
    $('#download-usage').text(fmt(data.total_download_gb));
    $('#upload-usage').text(fmt(data.total_upload_gb));
}

// ── Location cards ─────────────────────────────────────────────────────────

function updateLocationCards(locations) {
    allLocations = locations;
    renderLocationCards();
}

function filterLocations(filter) {
    currentLocationFilter = filter;
    renderLocationCards();
}

function renderLocationCards() {
    const $container = $('#locations-container');
    if (!$container.length) return;

    let filtered = allLocations;
    if (currentLocationFilter === 'online') {
        filtered = allLocations.filter(function(l) { return l.online_status === 'online'; });
    } else if (currentLocationFilter === 'offline') {
        filtered = allLocations.filter(function(l) { return l.online_status !== 'online'; });
    }

    if (filtered.length === 0) {
        $container.html('<div style="padding:var(--mw-space-xl);text-align:center;color:var(--mw-text-muted);grid-column:1/-1;">' +
            '<i data-feather="map-pin" style="width:24px;height:24px;margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;"></i>' +
            '<span>' + (currentLocationFilter === 'online' ? 'No online locations' : currentLocationFilter === 'offline' ? 'No offline locations' : 'No locations found') + '</span>' +
            '</div>');
        if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
        return;
    }

    const cards = filtered.slice(0, 6).map(function(loc) {
        const online = loc.online_status === 'online';
        const iconBg = online ? 'rgba(22,163,74,0.12)' : 'rgba(220,38,38,0.10)';
        const iconColor = online ? 'var(--mw-success)' : 'var(--mw-danger)';
        const badgeClass = online ? 'status-badge status-online' : 'status-badge status-offline';
        const badgeLabel = online ? 'Online' : 'Offline';
        const users = (loc.users || 0).toLocaleString();
        const gb = (loc.data_usage_gb || 0).toFixed(1);
        const detailUrl = '/' + dashLocale + '/locations/' + loc.id;

        return '<div class="card location-card" style="cursor:pointer;margin:0;" onclick="window.location.href=\'' + detailUrl + '\'">' +
            '<div class="card-body" style="padding:var(--mw-space-lg);">' +
            '<div style="display:flex;align-items:center;gap:var(--mw-space-sm);margin-bottom:var(--mw-space-md);">' +
            '<div style="width:32px;height:32px;background:' + iconBg + ';border-radius:var(--mw-radius-md);display:flex;align-items:center;justify-content:center;color:' + iconColor + ';flex-shrink:0;">' +
            '<i data-feather="wifi" style="width:16px;height:16px;"></i></div>' +
            '<div style="font-size:13px;font-weight:600;color:var(--mw-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + loc.name + '</div>' +
            '</div>' +
            '<span class="' + badgeClass + '">' + badgeLabel + '</span>' +
            '<div style="font-size:11px;color:var(--mw-text-muted);margin-top:var(--mw-space-sm);">' + users + ' users &middot; ' + gb + ' GB</div>' +
            '</div></div>';
    }).join('');

    $container.html(cards);
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
}

// ── Leaflet map ────────────────────────────────────────────────────────────

function initializeNetworkMap(locations) {
    if (typeof L === 'undefined' || !document.getElementById('network-map')) return;

    const loadingEl = document.getElementById('map-loading');
    if (loadingEl) loadingEl.style.display = 'none';

    if (networkMap) { networkMap.remove(); networkMap = null; }

    const withCoords = locations.filter(function(l) {
        return l.latitude && l.longitude &&
               !isNaN(parseFloat(l.latitude)) && !isNaN(parseFloat(l.longitude));
    });

    if (withCoords.length === 0) {
        document.getElementById('network-map').innerHTML =
            '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--mw-text-muted);">No locations with coordinates</div>';
        return;
    }

    networkMap = L.map('network-map').setView([0, 0], 2);
    let mapTileLayer = null;
    function applyMapTiles(map) {
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        if (mapTileLayer) map.removeLayer(mapTileLayer);
        mapTileLayer = L.tileLayer(
            dark ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                 : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: dark ? '&copy; OpenStreetMap contributors &copy; CARTO' : '&copy; OpenStreetMap contributors',
              subdomains: dark ? 'abcd' : 'abc', maxZoom: 19 }
        ).addTo(map);
    }
    applyMapTiles(networkMap);
    new MutationObserver(() => applyMapTiles(networkMap))
        .observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

    const mkIcon = function(color) {
        return L.divIcon({
            className: 'marker-icon',
            html: '<div style="background:' + color + ';width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
            iconSize: [20, 20], iconAnchor: [10, 10]
        });
    };
    const onlineIcon = mkIcon('#16A34A');
    const offlineIcon = mkIcon('#DC2626');

    const markers = withCoords.map(function(loc) {
        const online = loc.online_status === 'online';
        const marker = L.marker([parseFloat(loc.latitude), parseFloat(loc.longitude)],
                                { icon: online ? onlineIcon : offlineIcon }).addTo(networkMap);
        marker.bindPopup(
            '<div style="min-width:180px;padding:8px;">' +
            '<strong>' + loc.name + '</strong><br>' +
            '<small style="color:#666;">' + (loc.address || '') + '</small><br><br>' +
            '<span style="font-size:11px;">' + (online ? '✅ Online' : '🔴 Offline') + '</span><br>' +
            '<a href="/' + dashLocale + '/locations/' + loc.id + '" style="font-size:12px;">View details →</a>' +
            '</div>'
        );
        return marker;
    });

    if (markers.length === 1) {
        const ll = markers[0].getLatLng();
        networkMap.setView([ll.lat, ll.lng], 13);
    } else {
        networkMap.fitBounds(new L.featureGroup(markers).getBounds(), { padding: [20, 20], maxZoom: 15 });
    }
}

// ── ApexCharts data usage chart ────────────────────────────────────────────

function updateDataUsageChart(data) {
    if (typeof ApexCharts === 'undefined') return;

    const container = document.querySelector('#data-usage-chart');
    if (!container) return;

    const daily = (data && data.daily_usage && data.daily_usage.length) ? data.daily_usage : [];
    const downloadData = daily.map(function(d) { return parseFloat(d.download_gb) || 0; });
    const uploadData = daily.map(function(d) { return parseFloat(d.upload_gb) || 0; });
    const categories = daily.map(function(d) {
        return new Date(d.date).toLocaleDateString('en-US', { weekday: 'short' });
    });

    if (dataUsageChart) { try { dataUsageChart.destroy(); } catch(e) {} dataUsageChart = null; }
    container.innerHTML = '';

    dataUsageChart = new ApexCharts(container, {
        chart: { height: 270, type: 'area', toolbar: { show: false } },
        colors: ['var(--mw-primary)', '#EA8B09'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        series: [{ name: 'Download', data: downloadData }, { name: 'Upload', data: uploadData }],
        xaxis: { categories: categories, labels: { style: { fontSize: '12px' } } },
        yaxis: { labels: { formatter: function(v) { return v.toFixed(1) + ' GB'; } } },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
        grid: { borderColor: 'var(--mw-border)', strokeDashArray: 5 },
        legend: { position: 'top', horizontalAlign: 'left' },
        tooltip: { y: { formatter: function(v) { return v.toFixed(2) + ' GB'; } } }
    });
    dataUsageChart.render();
}

// ── Loading / error states ─────────────────────────────────────────────────

function showDataUsageLoading(show) {
    if (show) {
        $('#data-usage-chart').html('<div style="display:flex;align-items:center;justify-content:center;height:270px;"><div class="spinner-border spinner-border-sm" style="color:var(--mw-primary);" role="status"></div></div>');
    }
}

function showOverviewError() {
    $('#dashboard-errors').html('<div style="padding:12px 16px;background:rgba(220,38,38,.08);border-left:3px solid var(--mw-danger);border-radius:var(--mw-radius-md);color:var(--mw-danger);margin-bottom:16px;">Failed to load dashboard data. Please refresh.</div>');
}

function showAnalyticsError() {
    $('#analytics-errors').html('<div style="padding:12px 16px;background:rgba(234,139,9,.08);border-left:3px solid var(--mw-warning);border-radius:var(--mw-radius-md);color:var(--mw-warning);">Failed to load analytics data.</div>');
}

function showDataUsageError() {
    $('#data-usage-chart').html('<div style="display:flex;align-items:center;justify-content:center;height:270px;color:var(--mw-text-muted);">Failed to load data</div>');
    $('#total-bandwidth-used, #download-usage, #upload-usage').text('—');
}

// ── Event listeners ────────────────────────────────────────────────────────

function setupEventListeners() {
    $(document).on('click', '[data-analytics-period]', function(e) {
        e.preventDefault();
        const period = $(this).data('analytics-period').toString();
        loadAnalytics(period);
        $(this).closest('.dropdown').find('.dropdown-toggle').text($(this).text());
    });

    $(document).on('click', '[data-location-filter]', function(e) {
        e.preventDefault();
        const filter = $(this).data('location-filter');
        filterLocations(filter);
        $(this).closest('.dropdown').find('.dropdown-toggle').text($(this).text());
    });

    $(document).on('click', '#dataUsageDropdown + .dropdown-menu .dropdown-item', function(e) {
        e.preventDefault();
        const text = $(this).text();
        let period = '7';
        if (text.includes('30') || text.toLowerCase().includes('month')) period = '30';
        else if (text.toLowerCase().includes('year')) period = '365';
        loadDataUsageTrends(period);
        $('#dataUsageDropdown').text(text);
    });
}

// ── Init ───────────────────────────────────────────────────────────────────

$(document).ready(function() {
    const token = UserManager.getToken();
    const user = UserManager.getUser();
    if (!token || !user) { window.location.href = '/'; return; }

    showDataUsageLoading(true);
    loadDashboardOverview();
    loadAnalytics('7');
    loadDataUsageTrends('7');
    setupEventListeners();
});
