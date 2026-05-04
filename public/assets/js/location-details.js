/**
 * location-details.js
 *
 * Shell for the Location Details page. Owns cross-module concerns only:
 *  - Shared globals (location_id, API, i18n/commonI18n, zone-member flags,
 *    QoS-bw primary/local cache, currentDeviceData, currentUsagePeriod)
 *  - Shared utility helpers (apiFetch, handleApiError, reRenderFeather,
 *    formatBytes, formatDuration, IPv4 validators)
 *  - Page bootstrap: loadRouterModels → loadLocationDetails → initial fetches
 *    → initEventHandlers (which delegates to per-tab init*Handlers)
 *  - loadLocationDetails coordinator: populates header/device/map/info-form
 *    and dispatches into each tab module's load-/populate- helpers
 *  - Tab routing: activateLdTab + ?tab= URL sync + tab click delegation
 *
 * Tab-specific code lives in:
 *   location-details-overview.js  — Overview (usage, chart, online users, map)
 *   location-details-settings.js  — Location Details (info form, clone, users)
 *   location-details-router.js    — Router Settings (WAN/Radio/Filter/QoS/VLAN,
 *                                   firmware, restart, reboot schedule, scan, MAC edit)
 *   location-details-networks.js  — WiFi Networks (SSID list + drawer editor)
 *
 * UI primitives (MwDrawer, MwConfirm) live in mw-primitives.js.
 */

'use strict';

// ============================================================================
// GLOBALS
// ============================================================================

let location_id = (function () {
    const parts = window.location.pathname.split('/').filter(Boolean);
    const last = parts[parts.length - 1];
    return /^\d+$/.test(last) ? last : null;
})();
let currentUsagePeriod = '7days';
let currentDeviceData = null;
let currentLocationData = null;
let networkSourceLocationId = null; // may differ from location_id when location is a non-primary zone member
/** Whether this location may edit zone-wide settings (QoS, networks); false for non-primary zone members. */
let locationIsPrimaryOrStandalone = true;
/** Primary location normalized qos_bw (GET); used for zone member WAN/class display. */
let qosBwZonePrimary = null;
/** Last loaded local settings.qos_bw (merged defaults); WAN may differ when override is on. */
let lastLoadedLocalQosBw = null;

const API = window.APP_CONFIG_V5?.apiBase || (window.APP_NETWORK_CONFIG?.apiBase) || '/api';
const i18n = (window.APP_I18N && window.APP_I18N.location_details) || {};
const commonI18n = (window.APP_I18N && window.APP_I18N.common) || {};

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

// Shared IPv4 helpers (router-tab WAN/DNS + networks-tab DHCP reservations).
function isValidIPv4(val) {
    return /^(\d{1,3}\.){3}\d{1,3}$/.test(val) &&
        val.split('.').every(n => +n >= 0 && +n <= 255);
}

function ipToInt(ip) {
    return ip.split('.').reduce((acc, octet) => (acc << 8) | (+octet & 0xff), 0) >>> 0;
}

function ipInSubnet(ip, networkIp, netmask) {
    const maskInt = ipToInt(netmask);
    return (ipToInt(ip) & maskInt) === (ipToInt(networkIp) & maskInt);
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
        await loadRouterModels();
        await loadLocationDetails();
        loadCurrentUsage(currentUsagePeriod);
        loadOnlineUsers();
        initEventHandlers();
    } catch (err) {
        handleApiError(err, 'initPage');
    }
}

// ============================================================================
// ROUTER MODELS
// ============================================================================

let routerModels = []; // [{id, name, device_type}]

async function loadRouterModels() {
    try {
        const res = await apiFetch(`${API}/firmware/models`);
        if (res.status === 'success') {
            routerModels = res.data;
            const $select = $('#router-model-select');
            $select.empty().append('<option value="">Select Model</option>');
            routerModels.forEach(pm => {
                $select.append(`<option value="${pm.id}">${pm.name}</option>`);
            });
        }
    } catch (err) {
        console.error('Failed to load router models', err);
    }

    // Show the router model dropdown only for admins
    if (UserManager.isAdminOrAbove()) {
        $('.admin-only-field').show();
    }
}

function getRouterModelName(productModelId) {
    if (!productModelId) return 'Unknown';
    const pm = routerModels.find(m => String(m.id) === String(productModelId));
    return pm ? pm.name : productModelId;
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
    currentLocationData = location;

    // Header & breadcrumb
    $('.location_name').text(location.name || '');
    $('.location_address').text([location.address, location.city, location.country].filter(Boolean).join(', '));

    // Zone line on Overview device card — only show when the location belongs to a zone
    const zoneName = (location.zone && location.zone.name) || '';
    if (zoneName) {
        $('.location_zone').text(zoneName);
        $('#location-zone-line').show();
    } else {
        $('#location-zone-line').hide();
    }

    // Zone-member flags — consumed by QoS logic and network-source routing
    const isPrimaryOrStandalone = !location.zone_id || location.is_primary_in_zone;
    const primaryLocationId = location.primary_location_id || location_id;

    networkSourceLocationId = primaryLocationId;
    locationIsPrimaryOrStandalone = isPrimaryOrStandalone;

    if (!isPrimaryOrStandalone) {
        $('#zone-qos-notice').show();
        $('#ld-networks-zone-notice').show();
    } else {
        $('#zone-qos-notice').hide();
        $('#ld-networks-zone-notice').hide();
    }
    applyQosZoneLock();

    // If the networks tab was already loaded before this function completed (race: URL
    // had ?tab=networks so ldNetworks.load() fired on DOMContentLoaded before
    // window.load), reload it now so it uses the correct networkSourceLocationId and
    // renders with the correct zone-lock state (hidden delete buttons, etc.).
    if (typeof ldNetworks !== 'undefined' && ldNetworks.isLoaded()) {
        ldNetworks.load();
    }

    // Status badge
    const isOnline = location.device && location.device.is_online;
    const $badge = $('.status-badge');
    $badge.removeClass('status-online status-offline status-warning')
        .addClass(isOnline ? 'status-online' : 'status-offline')
        .text(isOnline ? 'Online' : 'Offline');

    // Device info
    const device = location.device || {};
    const modelName = device.product_model ? device.product_model.name : getRouterModelName(device.product_model_id);
    $('.router_model, .router_model_updated').text(modelName || 'Unknown');
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
        loadUserDropdowns(location.owner_id, location.shared_users || []);
    } else {
        $('#location-owner-group').hide();
        $('#location-shared-users-group').hide();
    }

    // Pre-populate scheduled reboot fields
    populateRebootSchedule(location);

    reRenderFeather();
}

// ============================================================================
// EVENT HANDLERS
// ============================================================================

function initEventHandlers() {
    initOverviewHandlers();
    initAnalyticsHandlers();
    initSettingsHandlers();
    initRouterHandlers();

    // User dropdown (navbar)
    const user = UserManager.getUser();
    if (user) UserManager.updateUserUI(user);
}

// Page-level tab switching (Overview / Settings / Router / Networks)
function activateLdTab(key, { updateUrl = true } = {}) {
    const tab = document.querySelector(`.mw-tab[data-tab="${key}"]`);
    const panel = document.getElementById('ld-panel-' + key);
    if (!tab || !panel) return;
    document.querySelectorAll('.mw-tab').forEach(t => t.classList.toggle('active', t === tab));
    document.querySelectorAll('.mw-panel').forEach(p => p.classList.toggle('active', p === panel));
    if (updateUrl) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', key);
        history.replaceState(null, '', url);
    }
    if (key === 'analytics') {
        loadAnalyticsTab();
    }
    if (key === 'networks' && !ldNetworks.isLoaded()) {
        ldNetworks.load();
    }
}

document.addEventListener('click', function (e) {
    const tab = e.target.closest('.mw-tab');
    if (!tab) return;
    const key = tab.dataset.tab;
    if (!key) return;
    activateLdTab(key);
});

// Restore active tab from ?tab= on page load
(function restoreTabFromUrl() {
    const key = new URL(window.location.href).searchParams.get('tab');
    if (!key) return;
    const allowed = ['overview', 'analytics', 'settings', 'router', 'networks'];
    if (!allowed.includes(key)) return;
    // Defer one tick so the ldNetworks IIFE + DOM are fully ready
    document.addEventListener('DOMContentLoaded', () => activateLdTab(key, { updateUrl: false }));
    if (document.readyState !== 'loading') activateLdTab(key, { updateUrl: false });
})();
