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
let networkSourceLocationId = null; // may differ from location_id when location is a non-primary zone member
/** Whether this location may edit zone-wide settings (QoS, networks); false for non-primary zone members. */
let locationIsPrimaryOrStandalone = true;
/** Primary location normalized qos_bw (GET); used for zone member WAN/class display. */
let qosBwZonePrimary = null;
/** Last loaded local settings.qos_bw (merged defaults); WAN may differ when override is on. */
let lastLoadedLocalQosBw = null;

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

function buildNetworksUrl(locId) {
    const parts = window.location.pathname.split('/');
    const lang = ['en', 'fr'].includes(parts[1]) ? parts[1] : 'en';
    return `/${lang}/locations/${locId}/networks`;
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
        loadNetworkSummary();
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

    // Header & breadcrumb
    $('.location_name').text(location.name || '');
    $('.location_address').text([location.address, location.city, location.country].filter(Boolean).join(', '));

    // Manage networks links — for non-primary zone members, point to primary location
    const isPrimaryOrStandalone = !location.zone_id || location.is_primary_in_zone;
    const primaryLocationId = location.primary_location_id || location_id;
    const canAccessPrimary = location.can_access_primary !== false;

    networkSourceLocationId = primaryLocationId;

    const netUrl = buildNetworksUrl(primaryLocationId);
    $('#manage-networks-btn, #manage-networks-header-btn')
        .attr('href', canAccessPrimary ? netUrl : '#')
        .removeClass('disabled')
        .removeAttr('title tabindex');

    locationIsPrimaryOrStandalone = isPrimaryOrStandalone;

    if (!isPrimaryOrStandalone) {
        $('#zone-network-notice').show();
        $('#zone-qos-notice').show();
        if (!canAccessPrimary) {
            $('#manage-networks-btn, #manage-networks-header-btn')
                .addClass('disabled')
                .attr('title', 'Networks are managed by the zone\'s primary location — you do not have access to that location')
                .attr('tabindex', '-1');
        }
    } else {
        $('#zone-network-notice').hide();
        $('#zone-qos-notice').hide();
    }
    applyQosZoneLock();

    console.log("location:::::", location.device);
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
    if (location.device && location.device.product_model_id) {
        $('#router-model-select').val(location.device.product_model_id);
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
        if (UserManager.isAdminOrAbove()) {
            data.owner_id = $('#location-owner').val() || null;
            const selectedIds = $('#location-shared-users').val() || [];
            data.shared_users = selectedIds.map(id => ({
                user_id: parseInt(id, 10),
                access_level: 'full',
            }));
        }
        const modelVal = $('#router-model-select').val();
        if (modelVal) data.product_model_id = parseInt(modelVal, 10);

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

// ============================================================================
// CLONE LOCATION
// ============================================================================

async function openCloneModal() {
    if (UserManager.isAdminOrAbove()) {
        // Load users into the clone owner dropdown
        try {
            const res = await apiFetch(`${API}/accounts/users`);
            const users = res.users || res.data || [];
            const $select = $('#clone-owner-select');
            $select.empty().append('<option value="">Assign to self</option>');
            users.forEach(u => {
                $select.append(`<option value="${u.id}">${u.name} (${u.email})</option>`);
            });
        } catch (err) {
            console.error('Error loading user list for clone:', err);
        }
        $('#clone-owner-group').show();
    } else {
        $('#clone-owner-group').hide();
    }
    $('#clone-location-modal').modal('show');
}

async function cloneLocation() {
    const $btn = $('#confirm-clone-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Cloning…');

    try {
        const body = {};
        if (UserManager.isAdminOrAbove()) {
            const ownerId = $('#clone-owner-select').val();
            if (ownerId) body.owner_id = ownerId;
        }

        const res = await apiFetch(`${API}/locations/${location_id}/clone`, {
            method: 'POST',
            body: JSON.stringify(body),
        });

        if (res.success) {
            $('#clone-location-modal').modal('hide');
            toastr.success('Location cloned successfully. Redirecting…');
            const parts = window.location.pathname.split('/');
            const lang = ['en', 'fr'].includes(parts[1]) ? parts[1] : 'en';
            setTimeout(() => {
                window.location.href = `/${lang}/locations/${res.location.id}`;
            }, 1200);
        }
    } catch (err) {
        handleApiError(err, 'cloneLocation');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
    }
}

// Role badge colours (matches sidebar pill style)
const ROLE_BADGE = {
    superadmin: { bg: 'rgba(234,84,85,0.12)',   color: '#ea5455', label: 'Super Admin' },
    admin:      { bg: 'rgba(255,159,67,0.12)',   color: '#ff9f43', label: 'Admin' },
    operator:   { bg: 'rgba(0,207,232,0.12)',    color: '#00cfe8', label: 'Operator' },
    viewer:     { bg: 'rgba(40,199,111,0.12)',   color: '#28c76f', label: 'Viewer' },
    partner:    { bg: 'rgba(115,103,240,0.12)',  color: '#7367f0', label: 'Partner' },
};

function roleBadgeHtml(role) {
    const r = ROLE_BADGE[role] || { bg: 'rgba(110,107,123,0.12)', color: '#6e6b7b', label: role || 'User' };
    return `<span style="font-size:0.68rem;background:${r.bg};color:${r.color};border-radius:10px;padding:1px 7px;font-weight:600;margin-left:6px;vertical-align:middle;">${r.label}</span>`;
}

async function loadUserDropdowns(currentOwnerId, sharedUsers = []) {
    try {
        const res = await apiFetch(`${API}/accounts/users`);
        const users = res.users || res.data || [];

        // ── Owner dropdown (plain select) ────────────────────────────────────
        const $owner = $('#location-owner');
        $owner.empty().append('<option value="">Select Owner</option>');
        users.forEach(u => {
            const roleLabel = (ROLE_BADGE[u.role] || {}).label || (u.role || '');
            $owner.append(`<option value="${u.id}" ${u.id == currentOwnerId ? 'selected' : ''}>${u.name} (${u.email}) — ${roleLabel}</option>`);
        });

        // ── Shared-access Select2 multi-select ───────────────────────────────
        const sharedUserIds = (sharedUsers || []).map(e => parseInt(e.user_id, 10));
        const $shared = $('#location-shared-users');

        // Destroy any existing Select2 instance before rebuilding
        if ($shared.hasClass('select2-hidden-accessible')) {
            $shared.select2('destroy');
        }
        $shared.empty();

        users.forEach(u => {
            if (u.id == currentOwnerId) return; // owner excluded from shared list
            const isSelected = sharedUserIds.includes(parseInt(u.id, 10));
            // Store role on the option so templateResult can read it
            const $opt = $(new Option(
                `${u.name} (${u.email})`,
                u.id,
                isSelected,
                isSelected
            ));
            $opt.data('role', u.role || '');
            $shared.append($opt);
        });

        $shared.select2({
            placeholder: 'Search users…',
            allowClear: true,
            width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text; // placeholder
                const role = $(option.element).data('role') || '';
                return $(`<span>${option.text}${roleBadgeHtml(role)}</span>`);
            },
            templateSelection: function (option) {
                if (!option.id) return option.text;
                const role = $(option.element).data('role') || '';
                return $(`<span>${option.text}${roleBadgeHtml(role)}</span>`);
            },
        });
    } catch (err) {
        console.error('Error loading user dropdowns:', err);
    }
}

// Keep backward-compat alias (used in clone modal path)
async function loadOwnerDropdown(currentOwnerId) {
    return loadUserDropdowns(currentOwnerId, []);
}

// ============================================================================
// LOCATION SETTINGS (WAN, Radio, Web Filter)
// ============================================================================

/** API stores kbps; UI shows Mbps (1 Mbps = 1000 kbps). */
function kbpsToMbpsDisplay(kbps) {
    const k = Math.max(0, Number(kbps) || 0);
    if (k === 0) return '0';
    const m = k / 1000;
    const s = m.toFixed(3).replace(/\.?0+$/, '');
    return s === '' ? '0' : s;
}

/** Parse Mbps from input → kbps for API (0–10_000_000). */
function parseQosMbpsInput($el) {
    const raw = String($el.val()).trim();
    if (raw === '') return 0;
    const m = parseFloat(raw);
    if (!Number.isFinite(m) || m < 0) return 0;
    const kbps = Math.round(m * 1000);
    return Math.min(10000000, Math.max(0, kbps));
}

function applyQosZoneLock() {
    const zoneMember = !locationIsPrimaryOrStandalone;
    const qosOn = $('#qos-enabled').is(':checked');
    $('#qos-wan-override-group').toggle(zoneMember);
    $('.qos-bandwidth-subsection').toggleClass('qos-bandwidth-disabled', !qosOn);

    if (!zoneMember) {
        $('#qos-enabled, #save-qos-settings').prop('disabled', false);
        $('.qos-bw-input').prop('disabled', !qosOn);
        $('#qos-wan-use-local').prop('disabled', !qosOn);
        return;
    }
    $('#qos-enabled, #save-qos-settings').prop('disabled', false);
    $('.qos-bw-class-input').prop('disabled', true);
    $('#qos-wan-use-local').prop('disabled', !qosOn);
    const useLocal = $('#qos-wan-use-local').is(':checked');
    $('.qos-wan-input').prop('disabled', !qosOn || !useLocal);
}

async function loadLocationSettings() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/settings`);
        if (!res.success) return;
        const s = res.data.settings;

        // WAN display — reset both detail rows first so type changes are reflected cleanly
        const wanType = (s.wan_connection_type || 'dhcp').toUpperCase();
        $('#wan-type-display').text(wanType);
        $('.wan-static-ip-display_div').addClass('hidden');
        $('.wan-pppoe-display_div').addClass('hidden');
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
        $('#last-optimal-2g').text(s.channel_2g || 6);
        $('#last-optimal-5g').text(s.channel_5g || 36);

        // Web filter
        const filterOn = !!s.web_filter_enabled;
        $('#global-web-filter').prop('checked', filterOn);
        $('#web-filter-propagation-notice').toggle(filterOn);
        loadWebFilterCategories(s.web_filter_categories || []);

        // WAN DNS (active when web filter is on)
        $('#wan-dns1').val(s.wan_dns1 || '');
        $('#wan-dns2').val(s.wan_dns2 || '');
        syncDnsFieldStates(filterOn);

        // QoS
        $('#qos-enabled').prop('checked', !!s.qos_enabled);
        qosBwZonePrimary = res.data.qos_bw_zone_primary || null;
        const bw = Object.assign(
            { wan_up_kbps: 0, wan_down_kbps: 0, voip_bw: 0, streaming_bw: 0, be_bw: 0, bulk_bw: 0 },
            s.qos_bw || {}
        );
        lastLoadedLocalQosBw = { ...bw };

        if (!locationIsPrimaryOrStandalone) {
            $('#qos-wan-use-local').prop('checked', !!s.qos_bw_wan_use_local);
            if (qosBwZonePrimary) {
                const zp = Object.assign(
                    { wan_up_kbps: 0, wan_down_kbps: 0, voip_bw: 0, streaming_bw: 0, be_bw: 0, bulk_bw: 0 },
                    qosBwZonePrimary
                );
                $('#qos-voip-bw').val(kbpsToMbpsDisplay(zp.voip_bw));
                $('#qos-streaming-bw').val(kbpsToMbpsDisplay(zp.streaming_bw));
                $('#qos-be-bw').val(kbpsToMbpsDisplay(zp.be_bw));
                $('#qos-bulk-bw').val(kbpsToMbpsDisplay(zp.bulk_bw));
                if (s.qos_bw_wan_use_local) {
                    $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(bw.wan_down_kbps));
                    $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(bw.wan_up_kbps));
                } else {
                    $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(zp.wan_down_kbps));
                    $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(zp.wan_up_kbps));
                }
            } else {
                $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(bw.wan_down_kbps));
                $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(bw.wan_up_kbps));
                $('#qos-voip-bw').val(kbpsToMbpsDisplay(bw.voip_bw));
                $('#qos-streaming-bw').val(kbpsToMbpsDisplay(bw.streaming_bw));
                $('#qos-be-bw').val(kbpsToMbpsDisplay(bw.be_bw));
                $('#qos-bulk-bw').val(kbpsToMbpsDisplay(bw.bulk_bw));
            }
        } else {
            $('#qos-wan-use-local').prop('checked', false);
            $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(bw.wan_down_kbps));
            $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(bw.wan_up_kbps));
            $('#qos-voip-bw').val(kbpsToMbpsDisplay(bw.voip_bw));
            $('#qos-streaming-bw').val(kbpsToMbpsDisplay(bw.streaming_bw));
            $('#qos-be-bw').val(kbpsToMbpsDisplay(bw.be_bw));
            $('#qos-bulk-bw').val(kbpsToMbpsDisplay(bw.bulk_bw));
        }
        loadQosClassesPreview();
        applyQosZoneLock();

        reRenderFeather();
    } catch (err) {
        handleApiError(err, 'loadLocationSettings');
    }
}

function toggleWanFields(type) {
    const t = (type || '').toUpperCase();
    $('#wan-static-fields').toggle(t === 'STATIC');
    $('#wan-pppoe-fields').toggle(t === 'PPPOE');
}

function isValidIPv4(val) {
    return /^(\d{1,3}\.){3}\d{1,3}$/.test(val) &&
        val.split('.').every(n => +n >= 0 && +n <= 255);
}

function syncDnsFieldStates(filterOn) {
    $('#wan-dns1, #wan-dns2')
        .prop('disabled', !filterOn)
        .closest('.form-group')
        .toggleClass('text-muted', !filterOn);
    $('#wan-dns-row').toggleClass('opacity-50', !filterOn);
}

async function saveWanSettings() {
    const $btn = $('.save-wan-settings');
    const origHtml = $btn.html();

    const connType = $('#wan-connection-type').val();

    // Validate required fields before touching the button state
    if (connType === 'STATIC') {
        const ip      = $('#wan-ip-address').val().trim();
        const netmask = $('#wan-netmask').val().trim();
        const gateway = $('#wan-gateway').val().trim();
        const dns1    = $('#wan-primary-dns').val().trim();
        const dns2    = $('#wan-secondary-dns').val().trim();
        if (!ip || !isValidIPv4(ip)) {
            toastr.warning('A valid IP address is required for Static IP mode.');
            $('#wan-ip-address').focus();
            return;
        }
        if (!netmask || !isValidIPv4(netmask)) {
            toastr.warning('A valid netmask is required for Static IP mode.');
            $('#wan-netmask').focus();
            return;
        }
        if (!gateway || !isValidIPv4(gateway)) {
            toastr.warning('A valid gateway address is required for Static IP mode.');
            $('#wan-gateway').focus();
            return;
        }
        if (dns1 && !isValidIPv4(dns1)) {
            toastr.warning('Primary DNS must be a valid IPv4 address.');
            $('#wan-primary-dns').focus();
            return;
        }
        if (dns2 && !isValidIPv4(dns2)) {
            toastr.warning('Secondary DNS must be a valid IPv4 address.');
            $('#wan-secondary-dns').focus();
            return;
        }
    } else if (connType === 'PPPOE') {
        if (!$('#wan-pppoe-username-modal').val().trim()) {
            toastr.warning('A username is required for PPPoE mode.');
            $('#wan-pppoe-username-modal').focus();
            return;
        }
        if (!$('#wan-pppoe-password').val()) {
            toastr.warning('A password is required for PPPoE mode.');
            $('#wan-pppoe-password').focus();
            return;
        }
    }

    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const data = { wan_connection_type: connType };

        if (connType === 'STATIC') {
            data.wan_ip_address      = $('#wan-ip-address').val().trim();
            data.wan_netmask         = $('#wan-netmask').val().trim();
            data.wan_gateway         = $('#wan-gateway').val().trim();
            data.wan_primary_dns     = $('#wan-primary-dns').val().trim() || null;
            data.wan_secondary_dns   = $('#wan-secondary-dns').val().trim() || null;
            // Clear any stale PPPoE credentials
            data.wan_pppoe_username  = null;
            data.wan_pppoe_password  = null;
            data.wan_pppoe_service_name = null;
        } else if (connType === 'PPPOE') {
            data.wan_pppoe_username     = $('#wan-pppoe-username-modal').val().trim();
            data.wan_pppoe_password     = $('#wan-pppoe-password').val();
            data.wan_pppoe_service_name = $('#wan-pppoe-service-name-modal').val().trim() || null;
            // Clear any stale static IP fields
            data.wan_ip_address    = null;
            data.wan_netmask       = null;
            data.wan_gateway       = null;
            data.wan_primary_dns   = null;
            data.wan_secondary_dns = null;
        } else {
            // DHCP — clear both sets of type-specific fields
            data.wan_ip_address      = null;
            data.wan_netmask         = null;
            data.wan_gateway         = null;
            data.wan_primary_dns     = null;
            data.wan_secondary_dns   = null;
            data.wan_pppoe_username  = null;
            data.wan_pppoe_password  = null;
            data.wan_pppoe_service_name = null;
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

    const filterEnabled = $('#global-web-filter').is(':checked');

    const wan_dns1 = $('#wan-dns1').val().trim() || null;
    const wan_dns2 = $('#wan-dns2').val().trim() || null;

    if (wan_dns1 && !isValidIPv4(wan_dns1)) {
        toastr.warning('Invalid WAN Primary DNS address.');
        $btn.prop('disabled', false).html(origHtml);
        return;
    }
    if (wan_dns2 && !isValidIPv4(wan_dns2)) {
        toastr.warning('Invalid WAN Secondary DNS address.');
        $btn.prop('disabled', false).html(origHtml);
        return;
    }
    if (wan_dns2 && !wan_dns1) {
        toastr.warning('Set a primary DNS before adding a secondary.');
        $btn.prop('disabled', false).html(origHtml);
        return;
    }

    try {
        const selectedCategories = $('#global-filter-categories').val() || [];
        await apiFetch(`${API}/locations/${location_id}/settings`, {
            method: 'PUT',
            body: JSON.stringify({
                web_filter_enabled: filterEnabled,
                web_filter_categories: selectedCategories.map(Number),
                wan_dns1,
                wan_dns2,
            }),
        });

        if (filterEnabled) {
            toastr.success(
                '<strong>Domain blocking is now enabled.</strong><br>It will take <strong>2–5 minutes</strong> to go live on the router.',
                'Web Filter Settings Saved',
                { timeOut: 8000, extendedTimeOut: 3000, enableHtml: true }
            );
        } else {
            toastr.success('Web filter settings saved.');
        }
    } catch (err) {
        handleApiError(err, 'saveWebFilterSettings');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

async function saveQosSettings() {
    const $btn = $('#save-qos-settings');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const payload = {
            enabled: $('#qos-enabled').is(':checked'),
            qos_bw: {
                wan_down_kbps: parseQosMbpsInput($('#qos-wan-down-kbps')),
                wan_up_kbps: parseQosMbpsInput($('#qos-wan-up-kbps')),
                voip_bw: parseQosMbpsInput($('#qos-voip-bw')),
                streaming_bw: parseQosMbpsInput($('#qos-streaming-bw')),
                be_bw: parseQosMbpsInput($('#qos-be-bw')),
                bulk_bw: parseQosMbpsInput($('#qos-bulk-bw')),
            },
        };
        if (!locationIsPrimaryOrStandalone) {
            payload.qos_bw_wan_use_local = $('#qos-wan-use-local').is(':checked');
        }
        await apiFetch(`${API}/locations/${location_id}/settings/qos`, {
            method: 'PUT',
            body: JSON.stringify(payload),
        });
        toastr.success('QoS settings saved.');
        await loadLocationSettings();
    } catch (err) {
        handleApiError(err, 'saveQosSettings');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        applyQosZoneLock();
        reRenderFeather();
    }
}

const CLASS_BADGE_COLORS = {
    EF:   'rgba(234,84,85,0.12)',
    AF41: 'rgba(255,159,67,0.12)',
    BE:   'rgba(40,199,111,0.12)',
    CS1:  'rgba(130,128,255,0.12)',
};
const CLASS_TEXT_COLORS = {
    EF:   '#ea5455',
    AF41: '#ff9f43',
    BE:   '#28c76f',
    CS1:  '#7367f0',
};

async function loadQosClassesPreview() {
    const $preview = $('#qos-classes-preview');
    try {
        const res = await apiFetch('/api/qos/classes');
        const classes = res.data || [];
        $preview.empty();
        classes.forEach(cls => {
            const bg   = CLASS_BADGE_COLORS[cls.id] || 'rgba(100,100,100,0.1)';
            const text = CLASS_TEXT_COLORS[cls.id]  || '#555';
            const count = cls.domains ? cls.domains.length : 0;
            const pill = `<span class="badge mr-1 mb-1" style="background:${bg};color:${text};font-size:0.75rem;padding:4px 8px;border-radius:12px;">
                ${cls.id} — ${cls.label}${cls.id !== 'BE' ? ` (${count} domain${count !== 1 ? 's' : ''})` : ''}
            </span>`;
            $preview.append(pill);
        });
    } catch (err) {
        $preview.html('<span class="text-muted" style="font-size:0.85rem;">Unable to load classes.</span>');
    }
}

// ============================================================================
// NETWORK SUMMARY (shortcut card badges)
// ============================================================================

async function loadNetworkSummary() {
    try {
        const srcId = networkSourceLocationId || location_id;
        const res = await apiFetch(`${API}/locations/${srcId}/networks`);
        const networks = res.data.networks || [];
        const $container = $('#network-summary-badges');
        $container.empty();

        if (!networks.length) {
            $container.html('<span class="network-summary-badge"><i data-feather="wifi-off" style="width:12px;height:12px;margin-right:4px;"></i> No networks configured</span>');
            reRenderFeather();
            return;
        }

        const TYPE_BADGE_CLASS = {
            password:       'badge-password',
            captive_portal: 'badge-captive',
            open:           'badge-open',
        };
        const TYPE_LABELS = { password: 'Password', captive_portal: 'Captive Portal', open: 'Open' };

        networks.forEach(net => {
            const badgeClass = TYPE_BADGE_CLASS[net.type] || '';
            const disabledClass = net.enabled ? '' : ' badge-disabled';
            const label = TYPE_LABELS[net.type] || net.type;
            const disabledTag = net.enabled ? '' : ' <small>(off)</small>';
            $container.append(`
                <span class="network-summary-badge ${badgeClass}${disabledClass}">
                    <i data-feather="wifi" style="width:12px;height:12px;"></i>
                    ${escapeHtml(net.ssid || 'Network')} &mdash; ${label}${disabledTag}
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
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        L.tileLayer(
            dark
                ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: dark
                    ? '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                    : '&copy; OpenStreetMap contributors',
                subdomains: dark ? 'abcd' : 'abc',
                maxZoom: 19
            }
        ).addTo(locationMap);
        L.marker([lat, lng]).addTo(locationMap).bindPopup(label || '').openPopup();
        $('#map-coordinates').text(`${lat.toFixed(4)}, ${lng.toFixed(4)}`).show();
    } catch (e) {
        console.warn('Map init failed', e);
    }
}

// ============================================================================
// MAC ADDRESS EDIT
// ============================================================================

async function loadDevicesForAssignment() {
    try {
        const res = await apiFetch(`${API}/v1/devices/available-for-location`);
        const unassigned = res.unassigned || [];
        const assigned = res.assigned || [];
        const $select = $('#device-select');
        const currentDeviceId = currentDeviceData?.id || null;
        $select.empty().append('<option value="">— Select an AP —</option>');
        if (unassigned.length) {
            const $group = $('<optgroup label="Unassigned APs"></optgroup>');
            unassigned.forEach(d => {
                const label = [d.name || d.serial_number, d.mac_address].filter(Boolean).join(' — ');
                $group.append(`<option value="${d.id}" data-mac="${d.mac_address}">${label}</option>`);
            });
            $select.append($group);
        }
        if (assigned.length) {
            const $group = $('<optgroup label="Already Assigned APs"></optgroup>');
            assigned.forEach(d => {
                const locationName = d.location?.name || '';
                const label = [d.name || d.serial_number, d.mac_address, locationName ? `(${locationName})` : ''].filter(Boolean).join(' — ');
                $group.append(`<option value="${d.id}" data-mac="${d.mac_address}">${label}</option>`);
            });
            $select.append($group);
        }
        if (!unassigned.length && !assigned.length) {
            $select.append('<option value="" disabled>No devices found</option>');
        }
        // Pre-select the device currently assigned to this location
        if (currentDeviceId) {
            $select.val(currentDeviceId).trigger('change');
        }
    } catch (err) {
        $('#device-select').html('<option value="">Failed to load devices</option>');
    }
}

async function saveDeviceAssignment() {
    const $btn = $('#save-mac-address-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const deviceId = $('#device-select').val();
        if (!deviceId) {
            toastr.error('Please select a device.');
            return;
        }
        const $selectedOpt = $('#device-select option:selected');
        const mac = ($selectedOpt.data('mac') || '').replace(/-/g, ':');
        await apiFetch(`${API}/locations/${location_id}/update-mac-address`, {
            method: 'POST',
            body: JSON.stringify({ device_id: parseInt(deviceId) }),
        });
        // Update currentDeviceData so the modal label is correct next time it opens
        if (currentDeviceData) {
            currentDeviceData.mac_address = $selectedOpt.data('mac') || mac;
            currentDeviceData.serial_number = ($selectedOpt.text().split(' — ')[0] || '').trim();
            currentDeviceData.name = currentDeviceData.serial_number;
        }
        $('.router_mac_address, .router_mac_address_header').text(mac);
        $('#mac-address-edit-modal').modal('hide');
        toastr.success('Device assigned successfully.');
    } catch (err) {
        handleApiError(err, 'saveDeviceAssignment');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

function showMacInputError(msg) {
    $('#device-mac-input').addClass('is-invalid');
    $('#device-mac-input-error').text(msg).show();
}

function clearMacInputError() {
    $('#device-mac-input').removeClass('is-invalid');
    $('#device-mac-input-error').text('').hide();
}

async function saveDeviceMacAddress() {
    if (!UserManager.isAdminOrAbove()) return;

    const $btn = $('#save-device-mac-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>');
    clearMacInputError();

    const lang = document.documentElement.lang;
    const newMac = $('#device-mac-input').val().trim();
    const macRegex = /^([0-9A-Fa-f]{2}[:\-]){5}([0-9A-Fa-f]{2})$/;
    if (!macRegex.test(newMac)) {
        showMacInputError(lang === 'fr'
            ? 'Format invalide. Utilisez AA:BB:CC:DD:EE:FF'
            : 'Invalid format. Use AA:BB:CC:DD:EE:FF');
        $btn.prop('disabled', false).html(origHtml);
        return;
    }

    try {
        const res = await apiFetch(`${API}/locations/${location_id}/update-mac-address`, {
            method: 'POST',
            body: JSON.stringify({ mac_address: newMac }),
        });
        if (res.success) {
            const normalised = newMac.toUpperCase().replace(/-/g, ':');
            const normalisedDash = newMac.toUpperCase().replace(/:/g, '-');
            $('#device-mac-preview').text(normalised);
            $('.router_mac_address, .router_mac_address_header').text(normalised);
            if (currentDeviceData) {
                currentDeviceData.mac_address = normalisedDash;
            }
            $('#device-mac-edit-inline').hide();
            $('#device-mac-preview-view').show();
            reRenderFeather();
            toastr.success(lang === 'fr' ? 'Adresse MAC mise à jour.' : 'MAC address updated.');
        } else {
            // Show the server error inline (e.g. "MAC address is already in use by another device")
            showMacInputError(res.message || (lang === 'fr' ? 'Échec de la mise à jour.' : 'Failed to update MAC address.'));
        }
    } catch (err) {
        // 409 conflict and 422 validation errors carry a message we can show inline
        const serverMsg = err?.body?.message || err?.body?.errors?.mac_address?.[0] || null;
        if (serverMsg) {
            showMacInputError(serverMsg);
        } else {
            handleApiError(err, 'saveDeviceMacAddress');
        }
    } finally {
        $btn.prop('disabled', false).html(origHtml);
    }
}

// ============================================================================
// FIRMWARE UPDATE
// ============================================================================

async function loadFirmwareVersions() {
    const deviceType = currentDeviceData?.product_model?.device_type
        || (currentDeviceData?.product_model_id
            ? routerModels.find(m => String(m.id) === String(currentDeviceData.product_model_id))?.device_type
            : null);
    if (!deviceType) {
        const noModelMsg = window.APP_CONFIG_V5?.locale === 'fr'
            ? 'Aucun modèle d\'appareil trouvé'
            : 'No device model found';
        $('#firmware-version-select').html(`<option value="">${noModelMsg}</option>`);
        return;
    }
    try {
        const res = await apiFetch(`/api/firmware/model/${deviceType}`);
        const versions = res.data || [];
        const $select = $('#firmware-version-select').empty().append('<option value="">Select version…</option>');
        versions.forEach(v => $select.append(`<option value="${v.id}" data-description="${v.description || ''}">${v.version || v.name || 'v' + v.id}</option>`));
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
// SCHEDULED REBOOT
// ============================================================================

function populateRebootSchedule(location) {
    if (location.scheduled_reboot_time) {
        // Convert "2026-03-27 03:00:00" → "2026-03-27T03:00" for datetime-local input
        const datetimeLocal = location.scheduled_reboot_time.substring(0, 16).replace(' ', 'T');
        $('#scheduled-reboot-time').val(datetimeLocal);

        // Show "currently scheduled" label
        const display = location.scheduled_reboot_time.substring(0, 16);
        $('#scheduled-reboot-current-value').text(display);
        $('#scheduled-reboot-current').show();
    } else {
        $('#scheduled-reboot-time').val('');
        $('#scheduled-reboot-current').hide();
    }
}

async function saveRebootSchedule() {
    const $btn = $('#save-reboot-schedule-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    // datetime-local gives "2026-03-27T03:00", API expects "2026-03-27 03:00"
    const rawVal = $('#scheduled-reboot-time').val();
    const time = rawVal ? rawVal.replace('T', ' ') : null;

    try {
        const res = await apiFetch(`${API}/locations/${location_id}`, {
            method: 'PUT',
            body: JSON.stringify({
                settings_type: 'reboot_schedule',
                scheduled_reboot_time: time,
            }),
        });
        if (res.success) {
            if (time) {
                $('#scheduled-reboot-current-value').text(time);
                $('#scheduled-reboot-current').show();
            } else {
                $('#scheduled-reboot-current').hide();
            }
            toastr.success(document.documentElement.lang === 'fr' ? 'Planification du redémarrage enregistrée.' : 'Reboot schedule saved.');
            $('#restart-confirmation-modal').modal('hide');
        } else {
            toastr.error(res.message || 'Failed to save reboot schedule.');
        }
    } catch (err) {
        handleApiError(err, 'saveRebootSchedule');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
    }
}

async function clearRebootSchedule() {
    const $btn = $('#clear-reboot-schedule-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>…');

    try {
        const res = await apiFetch(`${API}/locations/${location_id}`, {
            method: 'PUT',
            body: JSON.stringify({
                settings_type: 'reboot_schedule',
                scheduled_reboot_time: null,
            }),
        });
        if (res.success) {
            $('#scheduled-reboot-time').val('');
            $('#scheduled-reboot-current').hide();
            toastr.success(document.documentElement.lang === 'fr' ? 'Planification supprimée.' : 'Reboot schedule cleared.');
        } else {
            toastr.error(res.message || 'Failed to clear schedule.');
        }
    } catch (err) {
        handleApiError(err, 'clearRebootSchedule');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
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
        const optimal2g = results.optimal_channel_2g || '--';
        const optimal5g = results.optimal_channel_5g || '--';
        $('#result-channel-2g').text(optimal2g);
        $('#result-channel-5g').text(optimal5g);
        renderNearbyNetworks(results.scan_results_2g || [], results.scan_results_5g || [], results);
        $('#scan-progress-view').hide();
        $('#scan-results-view').show();
        $('#last-optimal-2g').text(optimal2g);
        $('#last-optimal-5g').text(optimal5g);
        const ts = results.completed_at ? new Date(results.completed_at).toLocaleString() : new Date().toLocaleString();
        $('#last-scan-timestamp').text('Last scan: ' + ts);
        $('#scan-status-text').text('Scan complete. Optimal channels identified.');
        $('#save-channels-btn').prop('disabled', false);
    } catch (err) {
        handleApiError(err, 'loadScanResults');
    }
}

function renderNearbyNetworks(networks2g, networks5g, results) {
    const all = [
        ...( networks2g || []).map(n => ({ ...n, band: '2.4 GHz' })),
        ...(networks5g || []).map(n => ({ ...n, band: '5 GHz' })),
    ];
    if (!all.length) {
        $('#nearby-networks-tbody').html('<tr><td colspan="6" class="text-center text-muted">No nearby networks detected</td></tr>');
        return;
    }
    const optimal2g = results?.optimal_channel_2g;
    const optimal5g = results?.optimal_channel_5g;
    const interference2g = results?.interference_level_2g || '-';
    const interference5g = results?.interference_level_5g || '-';
    const rows = all.map(n => {
        const isOptimal = (n.band === '2.4 GHz' && n.channel == optimal2g) || (n.band === '5 GHz' && n.channel == optimal5g);
        const interference = n.band === '2.4 GHz' ? interference2g : interference5g;
        const ssidLabel = n.ssid ? n.ssid : '<em class="text-muted">Hidden</em>';
        return `<tr>
            <td>${n.band}</td>
            <td>${n.channel || '-'}</td>
            <td>${ssidLabel}</td>
            <td>${n.signal != null ? n.signal + ' dBm' : '-'}</td>
            <td>${interference}</td>
            <td><span class="badge badge-${isOptimal ? 'success' : 'secondary'}">${isOptimal ? 'Optimal' : 'Used'}</span></td>
        </tr>`;
    }).join('');
    $('#nearby-networks-tbody').html(rows);
}

async function applyOptimalChannels() {
    if (!optimalScanResults) return;
    $('#channel-2g').val(optimalScanResults.optimal_channel_2g);
    $('#channel-5g').val(optimalScanResults.optimal_channel_5g);
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

    // Show propagation notice when filter is toggled on
    $('#global-web-filter').on('change', function () {
        const on = $(this).is(':checked');
        $('#web-filter-propagation-notice').toggle(on);
        syncDnsFieldStates(on);
        reRenderFeather();
    });

    // Save QoS
    $('#save-qos-settings').on('click', saveQosSettings);
    $('#qos-enabled').on('change', function () {
        applyQosZoneLock();
        reRenderFeather();
    });
    $('#qos-wan-use-local').on('change', function () {
        if (!locationIsPrimaryOrStandalone && qosBwZonePrimary && lastLoadedLocalQosBw) {
            const zp = Object.assign(
                { wan_up_kbps: 0, wan_down_kbps: 0, voip_bw: 0, streaming_bw: 0, be_bw: 0, bulk_bw: 0 },
                qosBwZonePrimary
            );
            const bw = lastLoadedLocalQosBw;
            if ($(this).is(':checked')) {
                $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(bw.wan_down_kbps));
                $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(bw.wan_up_kbps));
            } else {
                $('#qos-wan-down-kbps').val(kbpsToMbpsDisplay(zp.wan_down_kbps));
                $('#qos-wan-up-kbps').val(kbpsToMbpsDisplay(zp.wan_up_kbps));
            }
        }
        applyQosZoneLock();
        reRenderFeather();
    });

    // Save WAN
    $(document).on('click', '.save-wan-settings', saveWanSettings);
    $('#wan-connection-type').on('change', function () { toggleWanFields($(this).val()); });

    // Restart device — reset modal to "Reboot Now" tab each time it opens
    $('#device-restart-btn').on('click', function () {
        // Reset to "Reboot Now" tab
        $('[data-restart-tab]').removeClass('active');
        $('[data-restart-tab="now"]').addClass('active');
        $('#reboot-now-section').show();
        $('#schedule-reboot-section').hide();
        $('#confirm-restart-btn').show();
        $('#save-reboot-schedule-btn').hide();
        $('#restart-confirmation-modal').modal('show');
    });
    $('#confirm-restart-btn').on('click', restartDevice);
    $('#save-reboot-schedule-btn').on('click', saveRebootSchedule);
    $('#clear-reboot-schedule-btn').on('click', clearRebootSchedule);

    // Restart modal tab switching
    $(document).on('click', '[data-restart-tab]', function (e) {
        e.preventDefault();
        const tab = $(this).data('restart-tab');
        $('[data-restart-tab]').removeClass('active');
        $(this).addClass('active');
        if (tab === 'now') {
            $('#reboot-now-section').show();
            $('#schedule-reboot-section').hide();
            $('#confirm-restart-btn').show();
            $('#save-reboot-schedule-btn').hide();
        } else {
            $('#reboot-now-section').hide();
            $('#schedule-reboot-section').show();
            $('#confirm-restart-btn').hide();
            $('#save-reboot-schedule-btn').show();
        }
    });

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

    // Device assignment modal
    $('#edit-mac-btn').on('click', function () {
        const d = currentDeviceData;
        const currentLabel = d
            ? [(d.name || d.serial_number), (d.mac_address || '').replace(/-/g, ':')].filter(Boolean).join(' — ')
            : '-';
        $('#current-mac-display').text(currentLabel);
        $('#device-select').val('').html('<option value="">Loading devices...</option>');
        $('#device-mac-preview-group').hide();
        $('#device-mac-preview').text('-');
        $('#save-mac-address-btn').prop('disabled', true);
        $('#mac-address-edit-modal').modal('show');
        loadDevicesForAssignment();
    });
    $(document).on('change', '#device-select', function () {
        const $opt = $(this).find('option:selected');
        const mac = ($opt.data('mac') || '').replace(/-/g, ':');
        if (mac) {
            $('#device-mac-preview').text(mac);
            $('#device-mac-edit-inline').hide();
            $('#device-mac-preview-view').show();
            clearMacInputError();
            // Only show the edit button for admin / superadmin
            if (UserManager.isAdminOrAbove()) {
                $('#edit-device-mac-btn').show();
            }
            $('#device-mac-preview-group').show();
            $('#save-mac-address-btn').prop('disabled', false);
            reRenderFeather();
        } else {
            $('#device-mac-preview-group').hide();
            $('#device-mac-preview').text('-');
            $('#save-mac-address-btn').prop('disabled', true);
        }
    });
    $('#save-mac-address-btn').on('click', saveDeviceAssignment);
    $('#mac-address-edit-modal').on('hidden.bs.modal', function () {
        $('#device-select').val('');
        $('#device-mac-preview-group').hide();
        $('#current-mac-display').text('-');
        $('#save-mac-address-btn').prop('disabled', true);
        // Reset inline MAC edit state
        $('#device-mac-edit-inline').hide();
        $('#device-mac-preview-view').show();
        $('#edit-device-mac-btn').hide();
        clearMacInputError();
    });

    // Inline MAC address edit (admin / superadmin only)
    $(document).on('click', '#edit-device-mac-btn', function () {
        if (!UserManager.isAdminOrAbove()) return;
        const currentMac = $('#device-mac-preview').text().trim();
        $('#device-mac-input').val(currentMac !== '-' ? currentMac : '');
        clearMacInputError();
        $('#device-mac-preview-view').hide();
        $('#device-mac-edit-inline').show();
        $('#device-mac-input').focus();
    });
    $(document).on('click', '#cancel-device-mac-btn', function () {
        clearMacInputError();
        $('#device-mac-edit-inline').hide();
        $('#device-mac-preview-view').show();
    });
    $(document).on('click', '#save-device-mac-btn', saveDeviceMacAddress);
    // Clear inline error as the user types
    $(document).on('input', '#device-mac-input', clearMacInputError);

    // Online users pagination
    $('#refresh-online-users').on('click', loadOnlineUsers);
    $('#prev-page').on('click', function () {
        if (onlineUsersPage > 1) { onlineUsersPage--; renderOnlineUsers(); }
    });
    $('#next-page').on('click', function () {
        const total = Math.ceil(allOnlineUsers.length / USERS_PER_PAGE);
        if (onlineUsersPage < total) { onlineUsersPage++; renderOnlineUsers(); }
    });

    // Clone location
    $('#clone-location-btn').on('click', openCloneModal);
    $('#confirm-clone-btn').on('click', cloneLocation);
    $('#clone-location-modal').on('hidden.bs.modal', function () {
        $('#clone-owner-select').empty();
    });

    // User dropdown (navbar)
    const user = UserManager.getUser();
    if (user) UserManager.updateUserUI(user);
}
