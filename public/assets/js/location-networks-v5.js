/**
 * location-networks-v5.js
 *
 * Dedicated network settings page for v5.
 * Handles all WiFi network CRUD operations for a given location.
 *
 * URL pattern: /{lang}/locations/{id}/networks
 */

'use strict';

// ============================================================================
// GLOBALS
// ============================================================================

let location_id = null;
let locationData = null;
let networks = [];
let captivePortalDesigns = [];
let webFilterEnabled = false;
const schedulers = {}; // netId → InteractiveScheduler instance

const API = window.APP_CONFIG_V5?.apiBase || '/api';
const MAX_NETWORKS = window.APP_CONFIG_V5?.maxNetworks || 4;

// i18n: falls back to English defaults if no messages object provided
const MSG = Object.assign({
    networkSaved:      'Network settings saved.',
    routerReconfigure: 'Router configuration updated — device will reconfigure shortly.',
    workingHoursSaved: 'Working hours saved.',
    macFilterSaved:    'MAC filter settings saved.',
    networkAdded:      'Network added.',
    networkDeleted:    'Network deleted.',
    invalidMac:        'Invalid MAC address format.',
    invalidSsid:       'SSID cannot be empty.',
    ssidTooLong:       'SSID must be 32 characters or fewer (802.11 limit).',
    passwordRequired:  'Password is required for password-type networks.',
    passwordTooShort:  'Password must be at least 8 characters long.',
    savingSchedule:    'Saving…',
}, window.APP_CONFIG_V5?.messages || {});

const TYPE_LABELS = Object.assign(
    { password: 'Password WiFi', captive_portal: 'Captive Portal', open: 'Open ESSID' },
    window.APP_CONFIG_V5?.typeLabels || {}
);

// ============================================================================
// UTILITY
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

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function locationPageUrl() {
    const parts = window.location.pathname.split('/');
    const lang = ['en', 'fr'].includes(parts[1]) ? parts[1] : 'en';
    return `/${lang}/locations/${location_id}`;
}

function isValidIPv4(val) {
    return /^(\d{1,3}\.){3}\d{1,3}$/.test(val) &&
        val.split('.').every(n => +n >= 0 && +n <= 255);
}

function applyDnsFieldState($pane, filterOn) {
    const $dns1 = $pane.find('.network-dns1');
    const $dns2 = $pane.find('.network-dns2');
    const $groups = $dns1.add($dns2).closest('.form-group');

    $dns1.prop('disabled', filterOn);
    $dns2.prop('disabled', filterOn);
    $groups.toggleClass('text-muted', filterOn);

    if (filterOn) {
        $pane.find('.net-dns-hint').remove();
        $pane.find('.dns-field-wrapper').each(function () {
            $(this).attr('data-toggle', 'tooltip').attr('data-placement', 'top');
            if (!$(this).attr('title')) {
                $(this).attr('title', 'DNS is managed by the web filter. Disable the web filter to set per-network DNS.');
            }
            // Bootstrap tooltip
            if ($.fn.tooltip) $(this).tooltip();
        });
    } else {
        $pane.find('.dns-field-wrapper').each(function () {
            if ($.fn.tooltip) $(this).tooltip('dispose');
        });
        if (!$pane.find('.net-dns-hint').length) {
            $dns2.closest('.form-group').append(
                '<small class="text-muted net-dns-hint">Leave empty to use the router as DNS server (no filtering applied).</small>'
            );
        }
    }
}

// ============================================================================
// PAGE INIT
// ============================================================================

$(window).on('load', function () {
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });

    // URL: /{lang}/locations/{id}/networks  → parts[-2] = location id
    const parts = window.location.pathname.replace(/\/$/, '').split('/');
    location_id = parts[parts.length - 2];

    initPage();
});

async function initPage() {
    try {
        // Auth
        const user = UserManager.getUser();
        if (!user) { window.location.href = '/login'; return; }
        UserManager.updateUserUI(user);

        // Back links
        const backUrl = locationPageUrl();
        $('#back-to-location-btn').attr('href', backUrl);
        $('#breadcrumb-location-link').attr('href', backUrl);

        // Load in parallel
        const [locRes, designRes] = await Promise.all([
            apiFetch(`${API}/locations/${location_id}`),
            apiFetch('/api/captive-portal-designs', { method: 'POST' }).catch(() => ({ data: [] })),
        ]);

        if (!locRes.success) {
            if (locRes.message === 'Unauthorized access') window.location.href = '/dashboard';
            return;
        }

        locationData = locRes.data;
        captivePortalDesigns = designRes.data || [];

        // Populate location info strip
        $('.location_name').text(locationData.name || '');
        $('.location_address').text([locationData.address, locationData.city, locationData.country].filter(Boolean).join(', '));
        $('#breadcrumb-location-link').text(locationData.name || 'Location');

        // VLAN global state (from location settings)
        const settingsRes = await apiFetch(`${API}/locations/${location_id}/settings`).catch(() => null);
        if (settingsRes?.success) {
            const vlanEnabled = !!settingsRes.data.settings.vlan_enabled;
            webFilterEnabled = !!settingsRes.data.settings.web_filter_enabled;
            $('#vlan-enabled').prop('checked', vlanEnabled);
        }

        // Load networks
        await loadNetworks();
        initEventHandlers();

        reRenderFeather();
    } catch (err) {
        handleApiError(err, 'initPage');
    }
}

// ============================================================================
// NETWORK LOADING & RENDERING
// ============================================================================

async function loadNetworks() {
    try {
        const res = await apiFetch(`${API}/locations/${location_id}/networks`);
        networks = res.data.networks || [];
        const canAdd = res.data.can_add ?? (networks.length < MAX_NETWORKS);
        renderTabs(networks, canAdd);
    } catch (err) {
        handleApiError(err, 'loadNetworks');
        $('#network-tabs-loading').html('<span class="nav-link text-danger"><small>Failed to load networks</small></span>');
    }
}

function renderTabs(nets, canAdd) {
    $('#network-tabs-loading').remove();
    $('.dynamic-network-tab').remove();
    $('.dynamic-network-pane').remove();

    nets.forEach((net, idx) => {
        const isFirst = idx === 0;
        $('#network-tabs-nav').append(buildTabHtml(net, isFirst));
        $('#network-tabs-content').append(buildPaneHtml(net, isFirst));
    });

    $('#add-network-btn').prop('disabled', !canAdd);

    if (!nets.length) {
        $('#network-tabs-content').html(`
            <div class="text-center py-5 text-muted">
                <i data-feather="wifi-off" style="width:56px;height:56px;margin-bottom:16px;color:#adb5bd;"></i>
                <h5>No networks configured</h5>
                <p>Click <strong>Add Network</strong> to create your first WiFi network for this location.</p>
            </div>`);
    }

    populatePaneData(nets);
    reRenderFeather();
    bindPaneEvents();
}

function buildTabHtml(net, isActive) {
    const typeLabel = TYPE_LABELS[net.type] || net.type;
    return `
        <li class="nav-item dynamic-network-tab" data-network-id="${net.id}">
            <a class="nav-link${isActive ? ' active' : ''}" id="network-tab-${net.id}" data-toggle="tab" href="#network-pane-${net.id}" role="tab">
                <i data-feather="wifi" class="mr-1"></i>
                <span class="network-tab-label">${escapeHtml(net.ssid || 'Network')}</span>
                <span class="network-type-badge network-type-${net.type}">${typeLabel}</span>
            </a>
        </li>`;
}

function buildPaneHtml(net, isActive) {
    const tpl = document.getElementById('network-pane-tpl');
    if (!tpl) return '';
    let html = tpl.innerHTML
        .replace(/__ID__/g, net.id)
        .replace(/__TYPE__/g, net.type)
        .replace(/__TYPE_LABEL__/g, TYPE_LABELS[net.type] || net.type);

    const div = document.createElement('div');
    div.innerHTML = html;
    const pane = div.firstElementChild;
    if (pane) {
        pane.classList.add('dynamic-network-pane');
        if (isActive) pane.classList.add('show', 'active');
        return pane.outerHTML;
    }
    return html;
}

function populatePaneData(nets) {
    const vlanEnabled = $('#vlan-enabled').is(':checked');

    nets.forEach(net => {
        const $pane = $(`#network-pane-${net.id}`);
        if (!$pane.length) return;

        $pane.find('.network-pane-title').text(net.ssid || 'Network');
        $pane.find('.network-type-select').val(net.type);
        $pane.find('.network-ssid').val(net.ssid || '');
        $pane.find('.network-visible').val(net.visible ? '1' : '0');
        $pane.find('.network-enabled').prop('checked', !!net.enabled);
        $pane.find('.network-qos-policy').prop('checked', (net.qos_policy || 'scavenger') === 'full');
        $pane.find('.network-radio').val(net.radio || 'all');

        showTypeSections($pane, net.type);
        syncTypePills($pane, net.type);

        // Password fields
        $pane.find('.network-password').val(net.password || '');
        $pane.find('.network-security').val(net.security || 'wpa2-psk');
        $pane.find('.network-cipher-suites').val(net.cipher_suites || 'CCMP');

        // Captive portal fields
        $pane.find('.network-auth-method').val(net.auth_method || 'click-through');
        $pane.find('.network-portal-password').val(net.portal_password || '');
        $pane.find('.network-social-method').val(net.social_auth_method || 'facebook');
        $pane.find('.network-session-timeout').val(net.session_timeout || 60);
        $pane.find('.network-idle-timeout').val(net.idle_timeout || 15);
        $pane.find('.network-redirect-url').val(net.redirect_url || '');
        $pane.find('.network-download-limit').val(net.download_limit || '');
        $pane.find('.network-upload-limit').val(net.upload_limit || '');
        showAuthSubFields($pane, net.auth_method || 'click-through');

        // Portal design
        const $designSelect = $pane.find('.network-portal-design-id');
        $designSelect.empty().append('<option value="">Default Design</option>');
        captivePortalDesigns.forEach(d => {
            $designSelect.append(`<option value="${d.id}" ${d.id == net.portal_design_id ? 'selected' : ''}>${escapeHtml(d.name)}</option>`);
        });

        // IP / DHCP
        const loadedIpMode = net.ip_mode || 'static';
        const noDhcpServer = loadedIpMode === 'bridge' || loadedIpMode === 'dhcp';
        $pane.find('.network-ip-mode').val(loadedIpMode);
        $pane.find('.network-ip-address').val(net.ip_address || '');
        $pane.find('.network-netmask').val(net.netmask || '255.255.255.0');
        $pane.find('.network-gateway').val(net.gateway || '');
        $pane.find('.network-dns1').val(net.dns1 || '');
        $pane.find('.network-dns2').val(net.dns2 || '');
        applyDnsFieldState($pane, webFilterEnabled);
        if (noDhcpServer) {
            $pane.find('.network-dhcp-enabled').prop('checked', false);
            $pane.find('.network-dhcp-start').val('');
            $pane.find('.network-dhcp-end').val('');
        }
        applyIpModeState($pane, loadedIpMode);

        // VLAN
        $pane.find('.network-vlan-id').prop('disabled', !vlanEnabled).val(net.vlan_id || '');
        $pane.find('.network-vlan-tagging').prop('disabled', !vlanEnabled).val(net.vlan_tagging || 'disabled');

        // MAC filter
        renderMacList($pane, net.mac_filter_list || []);
        $pane.find('.network-mac-filter-mode').val(net.mac_filter_mode || 'allow-all');

        // Working hours scheduler (captive portal only)
        if (net.type === 'captive_portal') {
            initScheduler(net.id, net.working_hours || []);
        }
    });
}

function initScheduler(netId, scheduleData) {
    const wrapperId = `schedule-wrapper-${netId}`;
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper || typeof InteractiveScheduler === 'undefined') return;

    // Destroy any existing instance
    if (schedulers[netId]) {
        schedulers[netId].destroy();
        delete schedulers[netId];
    }

    wrapper.innerHTML = '';
    const innerDiv = document.createElement('div');
    innerDiv.id = `schedule-container-${netId}`;
    wrapper.appendChild(innerDiv);

    schedulers[netId] = new InteractiveScheduler({
        container: `#schedule-container-${netId}`,
        initialData: scheduleData || [],
        labels: window.APP_CONFIG_V5?.schedulerLabels || {},
        onSave: async (data) => {
            const $btn = $(`#schedule-wrapper-${netId}`).find('[data-action="save"]');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin mr-1"></i>${MSG.savingSchedule}`);
            try {
                await apiFetch(`${API}/locations/${location_id}/networks/${netId}`, {
                    method: 'PUT',
                    body: JSON.stringify({ working_hours: data }),
                });
                const net = networks.find(n => n.id == netId);
                if (net) net.working_hours = data;
                toastr.success(MSG.workingHoursSaved);
            } catch (err) {
                handleApiError(err, 'saveSchedule');
            } finally {
                $btn.prop('disabled', false).html(origHtml);
            }
        },
    });
}

function showTypeSections($pane, type) {
    $pane.find('.network-section').hide();
    $pane.find(`.network-section-${type}`).show();
    const netId = $pane.data('network-id');
    $(`#network-tab-${netId} .network-type-badge`)
        .removeClass('network-type-password network-type-captive_portal network-type-open')
        .addClass(`network-type-${type}`)
        .text(TYPE_LABELS[type] || type);

    // Bridge to WAN is not available for captive portal networks
    const $ipModeSelect = $pane.find('.network-ip-mode');
    const $bridgeOption = $ipModeSelect.find('option[value="bridge"]');
    if (type === 'captive_portal') {
        if ($ipModeSelect.val() === 'bridge') {
            $ipModeSelect.val('static');
        }
        $bridgeOption.hide().prop('disabled', true);
        applyIpModeState($pane, $ipModeSelect.val());
    } else {
        $bridgeOption.show().prop('disabled', false);
        applyIpModeState($pane, $ipModeSelect.val());
    }
}

/**
 * Restore DHCP switch + start/pool from the in-memory `networks` cache (last API load / save).
 */
function applyDhcpFieldsFromCache($pane) {
    const netId = $pane.data('network-id');
    const net = networks.find(n => n.id == netId);
    if (!net) return;
    $pane.find('.network-dhcp-enabled').prop('checked', net.dhcp_enabled !== false);
    $pane.find('.network-dhcp-start').val(net.dhcp_start || '');
    $pane.find('.network-dhcp-end').val(net.dhcp_end != null && net.dhcp_end !== '' ? String(net.dhcp_end) : '');
}

function applyIpModeState($pane, mode) {
    const isBridge = mode === 'bridge';
    const isDhcp   = mode === 'dhcp';
    const noDhcpServer = isBridge || isDhcp;

    // IP address / network fields row: hide for bridge and dhcp client
    $pane.find('.network-ip-address, .network-netmask, .network-gateway, .network-dns1, .network-dns2')
        .closest('.form-group')
        .toggle(!isBridge && !isDhcp);

    // DHCP server: same for bridge and dhcp client — off, locked, blank range
    $pane.find('.network-dhcp-enabled').closest('.form-group').show();
    $pane.find('.network-dhcp-start, .network-dhcp-end').closest('.form-group').show();

    if (noDhcpServer) {
        $pane.find('.network-dhcp-enabled').prop('checked', false).prop('disabled', true);
        $pane.find('.network-dhcp-start, .network-dhcp-end').val('').prop('disabled', true);
    } else {
        $pane.find('.network-dhcp-enabled').prop('disabled', false);
        $pane.find('.network-dhcp-start, .network-dhcp-end').prop('disabled', false);
        applyDhcpFieldsFromCache($pane);
    }
}

function syncTypePills($pane, type) {
    $pane.find('.network-type-pill')
        .removeClass('active-password active-captive_portal active-open')
        .filter(`[data-type="${type}"]`)
        .addClass(`active-${type}`);
}

function showAuthSubFields($pane, method) {
    $pane.find('.network-captive-password-group').toggle(method === 'password');
    $pane.find('.network-social-group').toggle(method === 'social');
}

function renderMacList($pane, list) {
    const $container = $pane.find('.network-mac-list');
    const $empty = $pane.find('.network-mac-empty');
    $container.empty();
    if (!list.length) {
        $empty.show();
    } else {
        $empty.hide();
        list.forEach((entry, i) => {
            const mac = typeof entry === 'object' ? (entry.mac || entry.address || '') : entry;
            $container.append(`
                <div class="mac-address-item">
                    <span>${escapeHtml(mac)}</span>
                    <button class="btn btn-sm btn-link text-danger p-0 network-mac-remove-btn" data-mac-index="${i}">
                        <i data-feather="x"></i>
                    </button>
                </div>`);
        });
    }
    reRenderFeather();
}

// ============================================================================
// FORM DATA COLLECTION
// ============================================================================

function getFormData(netId, $pane) {
    const type = $pane.find('.network-type-select').val();
    const ipMode = $pane.find('.network-ip-mode').val();
    const isBridge = ipMode === 'bridge';
    const isDhcp   = ipMode === 'dhcp';
    const noManualIp = isBridge || isDhcp;

    const data = {
        type,
        ssid: $pane.find('.network-ssid').val().trim(),
        visible: $pane.find('.network-visible').val() === '1',
        enabled: $pane.find('.network-enabled').is(':checked'),
        ip_mode: ipMode,
        ip_address: noManualIp ? null : ($pane.find('.network-ip-address').val().trim() || null),
        netmask: noManualIp ? null : ($pane.find('.network-netmask').val().trim() || null),
        gateway: noManualIp ? null : ($pane.find('.network-gateway').val().trim() || null),
        dns1: noManualIp ? null : ($pane.find('.network-dns1').val().trim() || null),
        dns2: noManualIp ? null : ($pane.find('.network-dns2').val().trim() || null),
        dhcp_enabled: noManualIp ? false : $pane.find('.network-dhcp-enabled').is(':checked'),
        dhcp_start: noManualIp ? null : ($pane.find('.network-dhcp-start').val().trim() || null),
        dhcp_end: noManualIp ? null : (() => {
            const raw = $pane.find('.network-dhcp-end').val();
            if (raw === '' || raw === undefined || raw === null) return null;
            const n = parseInt(String(raw).trim(), 10);
            return Number.isNaN(n) ? null : n;
        })(),
        vlan_id: parseInt($pane.find('.network-vlan-id').val()) || null,
        vlan_tagging: $pane.find('.network-vlan-tagging').val(),
        mac_filter_mode: $pane.find('.network-mac-filter-mode').val(),
        qos_policy: $pane.find('.network-qos-policy').is(':checked') ? 'full' : 'scavenger',
        radio: $pane.find('.network-radio').val() || 'all',
    };

    if (type === 'password') {
        const pwdVal = $pane.find('.network-password').val().trim();
        data.password = pwdVal; // always include so saveNetwork validation can catch empty/short
        // Backend strips empty password before fill() to avoid wiping the DB value,
        // but frontend now blocks saving with an empty field entirely.
        data.security = $pane.find('.network-security').val();
        data.cipher_suites = $pane.find('.network-cipher-suites').val();
    } else if (type === 'captive_portal') {
        data.auth_method = $pane.find('.network-auth-method').val();
        data.portal_password = $pane.find('.network-portal-password').val() || null;
        data.social_auth_method = $pane.find('.network-social-method').val() || null;
        data.session_timeout = parseInt($pane.find('.network-session-timeout').val()) || 60;
        data.idle_timeout = parseInt($pane.find('.network-idle-timeout').val()) || 15;
        data.redirect_url = $pane.find('.network-redirect-url').val().trim() || null;
        data.portal_design_id = $pane.find('.network-portal-design-id').val() || null;
        data.download_limit = parseInt($pane.find('.network-download-limit').val()) || null;
        data.upload_limit = parseInt($pane.find('.network-upload-limit').val()) || null;
        data.working_hours = schedulers[netId] ? schedulers[netId].getScheduleData() : [];
    }

    return data;
}

// ============================================================================
// CRUD OPERATIONS
// ============================================================================

async function saveNetwork(netId) {
    if (!netId) { console.error('saveNetwork called without a valid netId'); return; }
    const $pane = $(`#network-pane-${netId}`);
    const $btn = $pane.find('.network-save-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const data = getFormData(netId, $pane);

        if (!data.ssid) {
            toastr.warning(MSG.invalidSsid);
            $btn.prop('disabled', false).html(origHtml);
            $pane.find('.network-ssid').focus();
            return;
        }
        if (data.ssid.length > 32) {
            toastr.warning(MSG.ssidTooLong);
            $btn.prop('disabled', false).html(origHtml);
            $pane.find('.network-ssid').focus();
            return;
        }

        // For password-type networks: validate the password field
        if (data.type === 'password') {
            if (data.password !== undefined && data.password.length < 8) {
                // A value was typed but it's too short
                toastr.warning(MSG.passwordTooShort);
                $btn.prop('disabled', false).html(origHtml);
                $pane.find('.network-password').focus();
                return;
            }
            if (!data.password) {
                // Field is empty — always block, password is required
                toastr.warning(MSG.passwordRequired);
                $btn.prop('disabled', false).html(origHtml);
                $pane.find('.network-password').focus();
                return;
            }
        }

        // DNS validation (only relevant when web filter is off; skip if disabled)
        if (data.dns1 && !isValidIPv4(data.dns1)) {
            toastr.warning('Invalid Primary DNS address.');
            $btn.prop('disabled', false).html(origHtml);
            $pane.find('.network-dns1').focus();
            return;
        }
        if (data.dns2 && !isValidIPv4(data.dns2)) {
            toastr.warning('Invalid Secondary DNS address.');
            $btn.prop('disabled', false).html(origHtml);
            $pane.find('.network-dns2').focus();
            return;
        }
        if (data.dns2 && !data.dns1) {
            toastr.warning('Set a primary DNS before adding a secondary.');
            $btn.prop('disabled', false).html(origHtml);
            $pane.find('.network-dns1').focus();
            return;
        }

        const res = await apiFetch(`${API}/locations/${location_id}/networks/${netId}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });

        // Update local cache
        const idx = networks.findIndex(n => n.id == netId);
        if (idx >= 0) networks[idx] = res.data.network;

        toastr.success(MSG.networkSaved);
        if (res.data.config_version_incremented) {
            toastr.info(MSG.routerReconfigure, '', { timeOut: 5000 });
        }

        // Update tab label
        $(`#network-tab-${netId} .network-tab-label`).text(data.ssid || 'Network');
        $pane.find('.network-pane-title').text(data.ssid || 'Network');
    } catch (err) {
        handleApiError(err, 'saveNetwork');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

async function saveMacFilter(netId, $pane) {
    const $btn = $pane.find('.network-mac-save-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving…');

    try {
        const net = networks.find(n => n.id == netId);
        await apiFetch(`${API}/locations/${location_id}/networks/${netId}`, {
            method: 'PUT',
            body: JSON.stringify({
                mac_filter_mode: $pane.find('.network-mac-filter-mode').val(),
                mac_filter_list: net?.mac_filter_list || [],
            }),
        });
        toastr.success(MSG.macFilterSaved);
    } catch (err) {
        handleApiError(err, 'saveMacFilter');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

async function addNetwork() {
    const $btn = $('#add-network-btn');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Adding…');

    try {
        const nextOrder = networks.length;
        const ipOctet = 10 + nextOrder;
        const res = await apiFetch(`${API}/locations/${location_id}/networks`, {
            method: 'POST',
            body: JSON.stringify({
                type: 'password',
                ssid: 'New Network',
                enabled: true,
                ip_address: `192.168.${ipOctet}.1`,
                dhcp_start: `192.168.${ipOctet}.100`,
                dhcp_end: 101,
            }),
        });
        toastr.success(MSG.networkAdded);
        await loadNetworks();
        const newNetId = res.data.network.id;
        $(`#network-tab-${newNetId}`).tab('show');
    } catch (err) {
        handleApiError(err, 'addNetwork');
    } finally {
        $btn.prop('disabled', networks.length >= MAX_NETWORKS).html('<i data-feather="plus" class="mr-1"></i> Add Network');
        reRenderFeather();
    }
}

async function deleteNetwork(netId) {
    if (!confirm('Delete this network? This cannot be undone and will reconfigure the router.')) return;
    try {
        await apiFetch(`${API}/locations/${location_id}/networks/${netId}`, { method: 'DELETE' });
        toastr.success(MSG.networkDeleted);
        await loadNetworks();
    } catch (err) {
        handleApiError(err, 'deleteNetwork');
    }
}

// ============================================================================
// PANE EVENT BINDING
// ============================================================================

function bindPaneEvents() {
    // Network type pill click → sync hidden select → trigger type change
    $(document).off('click.nmgr', '.network-type-pill').on('click.nmgr', '.network-type-pill', function () {
        const $pane = $(this).closest('.tab-pane');
        const type = $(this).data('type');
        $pane.find('.network-type-select').val(type).trigger('change.nmgr');
        syncTypePills($pane, type);
    });

    // Network type change (from hidden select)
    $(document).off('change.nmgr', '.network-type-select').on('change.nmgr', '.network-type-select', function () {
        const $pane = $(this).closest('.tab-pane');
        const type = $(this).val();
        showTypeSections($pane, type);
        syncTypePills($pane, type);
        const netId = $pane.data('network-id');
        $(`#network-tab-${netId} .network-tab-label`).text($pane.find('.network-ssid').val() || 'Network');
        if (type === 'captive_portal' && !schedulers[netId]) {
            const net = networks.find(n => n.id == netId);
            initScheduler(netId, net?.working_hours || []);
        }
    });

    // IP mode change → apply field state for static / dhcp / bridge
    $(document).off('change.nmgr', '.network-ip-mode').on('change.nmgr', '.network-ip-mode', function () {
        applyIpModeState($(this).closest('.tab-pane'), $(this).val());
    });

    // Auth method change
    $(document).off('change.nmgr', '.network-auth-method').on('change.nmgr', '.network-auth-method', function () {
        showAuthSubFields($(this).closest('.tab-pane'), $(this).val());
    });

    // SSID input → update tab label live; block Enter to avoid accidental saves
    $(document).off('input.nmgr', '.network-ssid').on('input.nmgr', '.network-ssid', function () {
        const $pane = $(this).closest('.tab-pane');
        $(`#network-tab-${$pane.data('network-id')} .network-tab-label`).text($(this).val() || 'Network');
    });
    $(document).off('keydown.nmgr', '.network-ssid').on('keydown.nmgr', '.network-ssid', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Save network
    $(document).off('click.nmgr', '.network-save-btn').on('click.nmgr', '.network-save-btn', async function () {
        await saveNetwork($(this).data('network-id'));
    });

    // Delete network (pane header button)
    $(document).off('click.nmgr', '.network-delete-btn').on('click.nmgr', '.network-delete-btn', async function () {
        await deleteNetwork($(this).data('network-id'));
    });

    // Collapsible section toggle
    $(document).off('click.nmgr', '.collapsible-section-header').on('click.nmgr', '.collapsible-section-header', function () {
        const targetId = $(this).data('target');
        const $section = $(this).closest('.collapsible-section');
        const $body = $('#' + targetId);
        const isOpen = $section.hasClass('is-open');
        if (isOpen) {
            $body.slideUp(200);
            $section.removeClass('is-open');
        } else {
            $body.slideDown(200, function () {
                // After slide-down completes, refresh the scheduler if this is a Working Hours section
                if (targetId && targetId.startsWith('working-hours-')) {
                    const netId = targetId.replace('working-hours-', '');
                    const scheduler = schedulers[netId];
                    if (scheduler) {
                        scheduler.calculateCellWidth();
                        scheduler.refresh();
                    }
                }
                reRenderFeather();
            });
            $section.addClass('is-open');
        }
    });

    // Toggle password visibility
    $(document).off('click.nmgr', '.network-toggle-password, .network-toggle-portal-password').on('click.nmgr', '.network-toggle-password, .network-toggle-portal-password', function () {
        const $input = $(this).closest('.input-group').find('input');
        const isText = $input.attr('type') === 'text';
        $input.attr('type', isText ? 'password' : 'text');
        $(this).find('i[data-feather]').attr('data-feather', isText ? 'eye' : 'eye-off');
        reRenderFeather();
    });

    // Add MAC address
    $(document).off('click.nmgr', '.network-mac-add-btn').on('click.nmgr', '.network-mac-add-btn', function () {
        const $pane = $(this).closest('.tab-pane');
        const mac = $pane.find('.network-mac-input').val().trim();
        if (!/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(mac)) {
            toastr.warning(MSG.invalidMac);
            return;
        }
        const netId = $pane.data('network-id');
        const net = networks.find(n => n.id == netId);
        if (!net) return;
        net.mac_filter_list = net.mac_filter_list || [];
        net.mac_filter_list.push(mac);
        renderMacList($pane, net.mac_filter_list);
        $pane.find('.network-mac-input').val('');
    });

    // Remove MAC address
    $(document).off('click.nmgr', '.network-mac-remove-btn').on('click.nmgr', '.network-mac-remove-btn', function () {
        const $pane = $(this).closest('.tab-pane');
        const netId = $pane.data('network-id');
        const idx = parseInt($(this).data('mac-index'));
        const net = networks.find(n => n.id == netId);
        if (!net || !net.mac_filter_list) return;
        net.mac_filter_list.splice(idx, 1);
        renderMacList($pane, net.mac_filter_list);
    });

    // Save MAC filter
    $(document).off('click.nmgr', '.network-mac-save-btn').on('click.nmgr', '.network-mac-save-btn', async function () {
        const $pane = $(this).closest('.tab-pane');
        await saveMacFilter($pane.data('network-id'), $pane);
    });
}

// ============================================================================
// STATIC EVENT HANDLERS
// ============================================================================

function initEventHandlers() {
    // Add network
    $('#add-network-btn').on('click', addNetwork);

    // VLAN global toggle
    $('#vlan-enabled').on('change', async function () {
        const enabled = $(this).is(':checked');
        $('.network-vlan-id, .network-vlan-tagging').prop('disabled', !enabled);
        // Persist to location settings
        try {
            await apiFetch(`${API}/locations/${location_id}/settings`, {
                method: 'PUT',
                body: JSON.stringify({ vlan_enabled: enabled }),
            });
        } catch (err) {
            console.error('Failed to save VLAN setting:', err);
        }
    });

    // Navbar user
    const user = UserManager.getUser();
    if (user) UserManager.updateUserUI(user);
}
