/**
 * location-details-v5.js
 *
 * Main page script for Location Details v5.
 * Network settings have been moved to a dedicated page (/locations/{id}/networks).
 *
 * Responsibilities:
 *  - Overview stats (device info, usage, map)
 *  - Analytics chart + live users
 *  - Networks shortcut card (summary badges, link to networks page)
 *  - Location info form
 *  - Router / WAN / Radio / Web-filter settings
 *  - MAC address edit, Firmware update, Channel scan, Device restart
 */

'use strict';

// ============================================================================
// GLOBALS
// ============================================================================

let location_id = null;
let currentUsagePeriod = 'today';
let currentDeviceData = null;
let analyticsChart = null;
let locationMap = null;
let optimalScanResults = null;

const API = window.APP_CONFIG_V5?.apiBase || (window.APP_NETWORK_CONFIG?.apiBase) || '/api';

// ============================================================================
// UTILITY HELPERS
// ============================================================================

function authHeaders() {
    return { Authorization: 'Bearer ' + UserManager.getToken() };
}

async function apiFetch(url, options = {}) {
    const res = await fetch(url, {
        headers: { 'Content-Type': 'application/json', ...authHeaders(), ...(options.headers || {}) },
        ...options,
    });
    if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw Object.assign(new Error(body.message || `HTTP ${res.status}`), { status: res.status, body });
    }
    return res.json();
}

function handleApiError(err, context = '') {
    console.error('API Error' + (context ? ` [${context}]` : ''), err);
    const msg = err?.body?.message || err?.message || 'An unexpected error occurred.';
    if (typeof toastr !== 'undefined') toastr.error(msg, 'Error');
    else alert('Error: ' + msg);
    if (err?.status === 401) window.location.href = '/login';
}

function reRenderFeather() {
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDuration(seconds) {
    if (!seconds) return '0s';
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    if (h > 0) return `${h}h ${m}m`;
    if (m > 0) return `${m}m ${s}s`;
    return `${s}s`;
}

function networksPageUrl() {
    // Derive language prefix from current path (/en/ or /fr/)
    const parts = window.location.pathname.split('/');
    const lang = ['en', 'fr'].includes(parts[1]) ? parts[1] : 'en';
    return `/${lang}/locations/${location_id}/networks`;
}

// ============================================================================
// PAGE INIT
// ============================================================================

$(window).on('load', function () {
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
    $('[data-toggle="tooltip"]').tooltip();

    // Extract location ID from URL: /en/locations/4  → 4
    const parts = window.location.pathname.split('/');
    location_id = parts[parts.length - 1];

    initPage();
});

async function initPage() {
    try {
        await loadLocationDetails();
        loadCurrentUsage(currentUsagePeriod);
        loadOnlineUsers();
        loadNetworkSummary();
        initEventHandlers();
    } catch (err) {
        handleApiError(err, 'initPage');
    }
}

// ============================================================================
// LOCATION DETAILS
// ============================================================================

async function loadLocationDetails() {
    const res = await apiFetch(`${API}/locations/${location_id}`);
    if (!res.success) {
        if (res.message === 'Unauthorized access') window.location.href = '/dashboard';
        return;
    }

    const location = res.data;
    currentDeviceData = location.device || null;

    // Header & breadcrumb
    $('.location_name').text(location.name || '');
    $('.location_address').text([location.address, location.city, location.country].filter(Boolean).join(', '));

    // Manage networks links
    const netUrl = networksPageUrl();
    $('#manage-networks-btn, #manage-networks-header-btn').attr('href', netUrl);

    // Status badge
    const isOnline = location.device && location.device.last_seen &&
        (new Date() - new Date(location.device.last_seen)) < 5 * 60 * 1000;
    const $badge = $('.status-badge');
    $badge.removeClass('status-online status-offline status-warning')
        .addClass(isOnline ? 'status-online' : 'status-offline')
        .text(isOnline ? 'Online' : 'Offline');

    // Device info
    const device = location.device || {};
    $('.router_model, .router_model_updated').text(device.model || 'Unknown');
    const macFormatted = (device.mac_address || 'N/A').replace(/-/g, ':');
    $('.router_mac_address').text(macFormatted);
    $('.router_mac_address_header').text(macFormatted);
    $('.router_firmware').text(device.firmware_version || 'Unknown');
    $('.uptime').text(device.uptime ? formatDuration(device.uptime) : 'N/A');

    // Map
    if (location.latitude && location.longitude) {
        initMap(parseFloat(location.latitude), parseFloat(location.longitude), location.name);
    }

    // Location info form
    populateLocationForm(location);

    // Load settings & dropdowns
    loadLocationSettings();
    if (UserManager.isAdminOrAbove()) {
        loadOwnerDropdown(location.owner_id);
    } else {
        $('#location-owner-group').hide();
    }

    reRenderFeather();
}

function populateLocationForm(location) {
    $('#location-name').val(location.name || '');
    $('#location-address').val(location.address || '');
    $('#location-city').val(location.city || '');
    $('#location-state').val(location.state || '');
    $('#location-country').val(location.country || '');
    $('#location-postal-code').val(location.postal_code || '');
    $('#location-manager').val(location.manager_name || '');
    $('#location-contact-email').val(location.contact_email || '');
    $('#location-contact-phone').val(location.contact_phone || '');
    $('#location-status').val(location.status || 'active');
    $('#location-description').val(location.description || '');
    $('#description-counter').text((location.description || '').length);
    if (location.device && location.device.model) {
        $('#router-model-select').val(location.device.model);
    }
}

function resetLocationForm() {
    loadLocationDetails();
}

async function saveLocationInfo() {
    const $btn = $('#save-location-info');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const data = {
            name: $('#location-name').val().trim(),
            address: $('#location-address').val().trim(),
            city: $('#location-city').val().trim(),
            state: $('#location-state').val().trim(),
            country: $('#location-country').val().trim(),
            postal_code: $('#location-postal-code').val().trim(),
            manager_name: $('#location-manager').val().trim(),
            contact_email: $('#location-contact-email').val().trim(),
            contact_phone: $('#location-contact-phone').val().trim(),
            status: $('#location-status').val(),
            description: $('#location-description').val().trim(),
        };
        if (UserManager.isAdminOrAbove()) data.owner_id = $('#location-owner').val() || null;
        const modelVal = $('#router-model-select').val();
        if (modelVal) data.model = modelVal;

        await apiFetch(`${API}/locations/${location_id}/general`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
        toastr.success('Location information saved successfully.');
    } catch (err) {
        handleApiError(err, 'saveLocationInfo');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

async function loadOwnerDropdown(currentOwnerId) {
    try {
        const res = await apiFetch(`${API}/accounts/users`);
        const users = res.data || [];
        const $select = $('#location-owner');
        $select.empty().append('<option value="">Select Owner</option>');
        users.forEach(u => {
            $select.append(`<option value="${u.id}" ${u.id == currentOwnerId ? 'selected' : ''}>${u.name} (${u.email})</option>`);
        });
    } catch (err) {
        console.error('Error loading owner list:', err);
    }
}

// ============================================================================
// LOCATION SETTINGS (WAN, Radio, Web Filter)
// ============================================================================

async function loadLocationSettings() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/settings`);
        if (!res.success) return;
        const s = res.data.settings;

        // WAN display
        const wanType = (s.wan_connection_type || 'dhcp').toUpperCase();
        $('#wan-type-display').text(wanType);
        if (wanType === 'STATIC') {
            $('.wan-static-ip-display_div').removeClass('hidden');
            $('#wan-ip-display').text(s.wan_ip_address || '-');
            $('#wan-subnet-display').text(s.wan_netmask || '-');
            $('#wan-gateway-display').text(s.wan_gateway || '-');
            $('#wan-dns1-display').text(s.wan_primary_dns || '-');
        } else if (wanType === 'PPPOE') {
            $('.wan-pppoe-display_div').removeClass('hidden');
            $('#wan-pppoe-username').text(s.wan_pppoe_username || '-');
            $('#wan-pppoe-service-name').text(s.wan_pppoe_service_name || '-');
        }

        // WAN modal defaults
        $('#wan-connection-type').val(s.wan_connection_type || 'DHCP');
        $('#wan-ip-address').val(s.wan_ip_address || '');
        $('#wan-netmask').val(s.wan_netmask || '');
        $('#wan-gateway').val(s.wan_gateway || '');
        $('#wan-primary-dns').val(s.wan_primary_dns || '');
        $('#wan-secondary-dns').val(s.wan_secondary_dns || '');
        $('#wan-pppoe-username-modal').val(s.wan_pppoe_username || '');
        $('#wan-pppoe-password').val(s.wan_pppoe_password || '');
        $('#wan-pppoe-service-name-modal').val(s.wan_pppoe_service_name || '');
        toggleWanFields($('#wan-connection-type').val());

        // Radio
        $('#wifi-country').val(s.country_code || 'US');
        $('#power-level-2g').val(s.transmit_power_2g || 15);
        $('#power-level-5g').val(s.transmit_power_5g || 17);
        $('#channel-width-2g').val(s.channel_width_2g || 40);
        $('#channel-width-5g').val(s.channel_width_5g || 80);
        $('#channel-2g').val(s.channel_2g || 6);
        $('#channel-5g').val(s.channel_5g || 36);

        // Web filter
        $('#global-web-filter').prop('checked', !!s.web_filter_enabled);
        loadWebFilterCategories(s.web_filter_categories || []);

        reRenderFeather();
    } catch (err) {
        handleApiError(err, 'loadLocationSettings');
    }
}

function toggleWanFields(type) {
    const t = (type || '').toUpperCase();
    if (t === 'STATIC') {
        $('#wan-static-fields').removeClass('hidden').show();
        $('#wan-pppoe-fields').hide();
    } else if (t === 'PPPOE') {
        $('#wan-static-fields').addClass('hidden').hide();
        $('#wan-pppoe-fields').show();
    } else {
        $('#wan-static-fields').addClass('hidden').hide();
        $('#wan-pppoe-fields').hide();
    }
}

async function saveWanSettings() {
    const $btn = $('.save-wan-settings');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const connType = $('#wan-connection-type').val();
        const data = { wan_connection_type: connType };
        if (connType === 'STATIC') {
            data.wan_ip_address = $('#wan-ip-address').val();
            data.wan_netmask = $('#wan-netmask').val();
            data.wan_gateway = $('#wan-gateway').val();
            data.wan_primary_dns = $('#wan-primary-dns').val();
            data.wan_secondary_dns = $('#wan-secondary-dns').val();
        } else if (connType === 'PPPOE') {
            data.wan_pppoe_username = $('#wan-pppoe-username-modal').val();
            data.wan_pppoe_password = $('#wan-pppoe-password').val();
            data.wan_pppoe_service_name = $('#wan-pppoe-service-name-modal').val();
        }
        await apiFetch(`${API}/locations/${location_id}/settings`, { method: 'PUT', body: JSON.stringify(data) });
        toastr.success('WAN settings saved.');
        $('#wan-settings-modal').modal('hide');
        loadLocationSettings();
    } catch (err) {
        handleApiError(err, 'saveWanSettings');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
    }
}

async function saveRadioSettings() {
    const $btn = $('#save-radio-settings');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const data = {
            country_code: $('#wifi-country').val(),
            transmit_power_2g: parseInt($('#power-level-2g').val()),
            transmit_power_5g: parseInt($('#power-level-5g').val()),
            channel_width_2g: parseInt($('#channel-width-2g').val()),
            channel_width_5g: parseInt($('#channel-width-5g').val()),
            channel_2g: parseInt($('#channel-2g').val()),
            channel_5g: parseInt($('#channel-5g').val()),
        };
        await apiFetch(`${API}/locations/${location_id}/settings`, { method: 'PUT', body: JSON.stringify(data) });
        toastr.success('Radio settings saved.');
    } catch (err) {
        handleApiError(err, 'saveRadioSettings');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

async function loadWebFilterCategories(selectedIds) {
    try {
        const res = await apiFetch('/api/categories/enabled');
        const categories = res.data || [];
        const $select = $('#global-filter-categories');
        if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
        $select.empty();
        categories.forEach(cat => {
            $select.append(new Option(cat.name, cat.id, false, selectedIds.includes(cat.id)));
        });
        $select.select2({ placeholder: 'Select categories to block', allowClear: true });
    } catch (err) {
        console.error('Error loading web filter categories:', err);
    }
}

async function saveWebFilterSettings() {
    const $btn = $('#save-web-filter-settings');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const selectedCategories = $('#global-filter-categories').val() || [];
        await apiFetch(`${API}/locations/${location_id}/settings`, {
            method: 'PUT',
            body: JSON.stringify({
                web_filter_enabled: $('#global-web-filter').is(':checked'),
                web_filter_categories: selectedCategories.map(Number),
            }),
        });
        toastr.success('Web filter settings saved.');
    } catch (err) {
        handleApiError(err, 'saveWebFilterSettings');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

// ============================================================================
// NETWORK SUMMARY (shortcut card badges)
// ============================================================================

async function loadNetworkSummary() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/networks`);
        const networks = res.data.networks || [];
        const $container = $('#network-summary-badges');
        $container.empty();

        if (!networks.length) {
            $container.html('<span class="network-summary-badge"><i data-feather="wifi-off" style="width:12px;height:12px;margin-right:4px;"></i> No networks configured</span>');
            reRenderFeather();
            return;
        }

        const TYPE_COLORS = {
            password: 'rgba(115,103,240,0.25)',
            captive_portal: 'rgba(40,199,111,0.25)',
            open: 'rgba(255,159,67,0.25)',
        };
        const TYPE_LABELS = { password: 'Password', captive_portal: 'Captive Portal', open: 'Open' };

        networks.forEach(net => {
            const color = TYPE_COLORS[net.type] || 'rgba(255,255,255,0.2)';
            const label = TYPE_LABELS[net.type] || net.type;
            const enabled = net.enabled ? '' : ' <small style="opacity:0.7;">(disabled)</small>';
            $container.append(`
                <span class="network-summary-badge" style="background:${color};">
                    <i data-feather="wifi" style="width:12px;height:12px;"></i>
                    ${escapeHtml(net.ssid || 'Network')} &mdash; ${label}${enabled}
                </span>`);
        });

        reRenderFeather();
    } catch (err) {
        $('#network-summary-badges').html('<span class="network-summary-badge"><i data-feather="alert-circle" style="width:12px;height:12px;"></i> Could not load networks</span>');
        reRenderFeather();
    }
}

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

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
    const options = {
        chart: { type: 'area', height: 300, toolbar: { show: false } },
        series, xaxis: { categories },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
        colors: ['#667eea'],
        dataLabels: { enabled: false },
        grid: { borderColor: '#f1f1f1' },
        tooltip: { theme: 'light' },
    };
    if (analyticsChart) {
        analyticsChart.updateOptions({ series, xaxis: { categories } });
    } else {
        analyticsChart = new ApexCharts(document.querySelector('#daily-usage-chart'), options);
        analyticsChart.render();
    }
}

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
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
        }).addTo(locationMap);
        L.marker([lat, lng]).addTo(locationMap).bindPopup(label || '').openPopup();
        $('#map-coordinates').text(`${lat.toFixed(4)}, ${lng.toFixed(4)}`).show();
    } catch (e) {
        console.warn('Map init failed', e);
    }
}

// ============================================================================
// MAC ADDRESS EDIT
// ============================================================================

async function saveMacAddress() {
    const $btn = $('#save-mac-address-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const mac = $('#mac-address-input').val().trim();
        if (!/^([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})$/.test(mac)) {
            toastr.error('Invalid MAC address format. Use XX-XX-XX-XX-XX-XX');
            return;
        }
        await apiFetch(`${API}/locations/${location_id}/update-mac-address`, {
            method: 'POST',
            body: JSON.stringify({ mac_address: mac }),
        });
        $('.router_mac_address, .router_mac_address_header').text(mac.replace(/-/g, ':'));
        $('#mac-address-edit-modal').modal('hide');
        toastr.success('MAC address updated successfully.');
    } catch (err) {
        handleApiError(err, 'saveMacAddress');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

// ============================================================================
// FIRMWARE UPDATE
// ============================================================================

async function loadFirmwareVersions() {
    const model = currentDeviceData?.model;
    if (!model) {
        $('#firmware-version-select').html('<option value="">No device model found</option>');
        return;
    }
    try {
        const res = await apiFetch(`/api/firmware/model/${model}`);
        const versions = res.data || [];
        const $select = $('#firmware-version-select').empty().append('<option value="">Select version…</option>');
        versions.forEach(v => $select.append(`<option value="${v.id}" data-description="${v.description || ''}">${v.version}</option>`));
        $select.on('change', function () {
            const desc = $(this).find(':selected').data('description') || 'No description.';
            $('#firmware-description').html(`<p class="mb-0">${desc}</p>`);
            $('#start-firmware-update-btn').prop('disabled', !$(this).val());
        });
    } catch (err) {
        handleApiError(err, 'loadFirmwareVersions');
    }
}

async function startFirmwareUpdate() {
    const firmwareId = $('#firmware-version-select').val();
    if (!firmwareId) return;

    $('#firmware-update-modal').modal('hide');
    $('#firmware-progress-modal').modal('show');
    $('#firmware-progress-bar').css('width', '0%');
    $('#firmware-progress-status').text('Starting firmware update…');

    try {
        await apiFetch(`/api/locations/${location_id}/update-firmware`, {
            method: 'POST',
            body: JSON.stringify({ firmware_id: firmwareId }),
        });
        let pct = 0;
        const interval = setInterval(() => {
            pct = Math.min(pct + 10, 90);
            $('#firmware-progress-bar').css('width', pct + '%');
            if (pct === 90) {
                clearInterval(interval);
                $('#firmware-progress-status').text('Device rebooting…');
            }
        }, 2000);
        setTimeout(() => {
            clearInterval(interval);
            $('#firmware-progress-bar').css('width', '100%');
            $('#firmware-progress-status').text('Update complete! Device restarting…');
            setTimeout(() => $('#firmware-progress-modal').modal('hide'), 3000);
        }, 22000);
    } catch (err) {
        $('#firmware-progress-modal').modal('hide');
        handleApiError(err, 'startFirmwareUpdate');
    }
}

// ============================================================================
// DEVICE RESTART
// ============================================================================

async function restartDevice() {
    const $btn = $('#confirm-restart-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Restarting…');

    try {
        await apiFetch(`${API}/locations/${location_id}`, {
            method: 'PUT',
            body: JSON.stringify({ settings_type: 'restart' }),
        });
        toastr.success('Device restart initiated. It will be back online in 2-3 minutes.');
        $('#restart-confirmation-modal').modal('hide');
    } catch (err) {
        handleApiError(err, 'restartDevice');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

// ============================================================================
// CHANNEL SCAN
// ============================================================================

let currentScanId = null;
let scanPollInterval = null;

async function initiateScan() {
    $('#channel-scan-modal').modal('show');
    $('#scan-progress-view').show();
    $('#scan-results-view').hide();
    resetScanStepIndicators();

    try {
        const res = await apiFetch(`${API}/locations/${location_id}/scan/initiate`, { method: 'POST' });
        currentScanId = res.data?.scan_id;
        markScanStep('initiated');
        pollScanStatus();
    } catch (err) {
        handleApiError(err, 'initiateScan');
        $('#channel-scan-modal').modal('hide');
    }
}

function resetScanStepIndicators() {
    ['#step-initiated-indicator', '#step-started-indicator', '#step-2g-indicator', '#step-5g-indicator'].forEach(id => {
        $(id).removeClass('timeline-point-primary timeline-point-success');
    });
    $('#channel-scan-modal .progress-bar').css('width', '0%');
}

function markScanStep(step) {
    const map = { initiated: '#step-initiated-indicator', started: '#step-started-indicator', scanning_2g: '#step-2g-indicator', scanning_5g: '#step-5g-indicator' };
    const stepOrder = ['initiated', 'started', 'scanning_2g', 'scanning_5g'];
    const idx = stepOrder.indexOf(step);
    stepOrder.slice(0, idx + 1).forEach((s, i) => {
        $(map[s]).addClass(i < idx ? 'timeline-point-success' : 'timeline-point-primary');
    });
    $('#channel-scan-modal .progress-bar').css('width', ((idx + 1) / stepOrder.length) * 100 + '%');
}

function pollScanStatus() {
    if (scanPollInterval) clearInterval(scanPollInterval);
    scanPollInterval = setInterval(async () => {
        try {
            const res = await apiFetch(`${API}/locations/${location_id}/scan/${currentScanId}/status`);
            const status = res.data?.status;
            if (status) markScanStep(status);
            if (status === 'completed') {
                clearInterval(scanPollInterval);
                loadScanResults();
            } else if (status === 'failed') {
                clearInterval(scanPollInterval);
                toastr.error('Channel scan failed.');
                $('#channel-scan-modal').modal('hide');
            }
        } catch (e) { /* swallow poll errors */ }
    }, 3000);
}

async function loadScanResults() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/scan-results/latest`);
        const results = res.data || {};
        optimalScanResults = results;
        $('#result-channel-2g').text(results.optimal_2g || '--');
        $('#result-channel-5g').text(results.optimal_5g || '--');
        renderNearbyNetworks(results.networks || []);
        $('#scan-progress-view').hide();
        $('#scan-results-view').show();
        $('#last-optimal-2g').text(results.optimal_2g || '--');
        $('#last-optimal-5g').text(results.optimal_5g || '--');
        const ts = results.completed_at ? new Date(results.completed_at).toLocaleString() : new Date().toLocaleString();
        $('#last-scan-timestamp').text('Last scan: ' + ts);
        $('#scan-status-text').text('Scan complete. Optimal channels identified.');
        $('#save-channels-btn').prop('disabled', false);
    } catch (err) {
        handleApiError(err, 'loadScanResults');
    }
}

function renderNearbyNetworks(networks) {
    if (!networks.length) {
        $('#nearby-networks-tbody').html('<tr><td colspan="6" class="text-center text-muted">No nearby networks detected</td></tr>');
        return;
    }
    const rows = networks.map(n => `
        <tr>
            <td>${n.band || '-'}</td><td>${n.channel || '-'}</td><td>${n.count || 0}</td>
            <td>${n.signal_strength || '-'}</td><td>${n.interference_level || '-'}</td>
            <td><span class="badge badge-${n.is_optimal ? 'success' : 'secondary'}">${n.is_optimal ? 'Optimal' : 'Used'}</span></td>
        </tr>`).join('');
    $('#nearby-networks-tbody').html(rows);
}

async function applyOptimalChannels() {
    if (!optimalScanResults) return;
    $('#channel-2g').val(optimalScanResults.optimal_2g);
    $('#channel-5g').val(optimalScanResults.optimal_5g);
    $('#channel-scan-modal').modal('hide');
    await saveRadioSettings();
    toastr.success('Optimal channels applied.');
}

// ============================================================================
// EVENT HANDLERS
// ============================================================================

function initEventHandlers() {
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

    // Save location info
    $('#save-location-info').on('click', saveLocationInfo);
    $('#location-description').on('input', function () {
        $('#description-counter').text($(this).val().length);
    });

    // Save radio
    $('#save-radio-settings').on('click', saveRadioSettings);

    // Save web filter
    $('#save-web-filter-settings').on('click', saveWebFilterSettings);

    // Save WAN
    $(document).on('click', '.save-wan-settings', saveWanSettings);
    $('#wan-connection-type').on('change', function () { toggleWanFields($(this).val()); });

    // Restart device
    $('#device-restart-btn').on('click', function () { $('#restart-confirmation-modal').modal('show'); });
    $('#confirm-restart-btn').on('click', restartDevice);

    // Firmware update
    $('#update-firmware-btn').on('click', function () {
        loadFirmwareVersions();
        $('#firmware-update-modal').modal('show');
    });
    $('#start-firmware-update-btn').on('click', startFirmwareUpdate);

    // Channel scan
    $('#scan-channels-btn').on('click', initiateScan);
    $('#apply-scan-results, #save-channels-btn').on('click', applyOptimalChannels);
    $('#back-to-scan-btn').on('click', function () {
        $('#scan-progress-view').show();
        $('#scan-results-view').hide();
        resetScanStepIndicators();
        initiateScan();
    });

    // MAC address edit
    $('#edit-mac-btn').on('click', function () {
        const currentMac = $('.router_mac_address').text();
        $('#current-mac-display').text(currentMac);
        $('#mac-address-edit-modal').modal('show');
    });
    $('#save-mac-address-btn').on('click', saveMacAddress);
    $('#mac-address-edit-modal').on('hidden.bs.modal', function () {
        $('#mac-address-input').val('');
        $('#current-mac-display').text('-');
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

    // User dropdown (navbar)
    const user = UserManager.getUser();
    if (user) UserManager.updateUserUI(user);
}
