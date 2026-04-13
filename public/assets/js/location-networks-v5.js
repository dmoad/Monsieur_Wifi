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
    macBadgeBypass:    'Bypass',
    macBadgeBlock:     'Block',
    pageOf:            'Page {page} of {total}',
    invalidSsid:       'SSID cannot be empty.',
    ssidTooLong:       'SSID must be 32 characters or fewer (802.11 limit).',
    passwordRequired:       'Password is required for password-type networks.',
    passwordTooShort:       'Password must be at least 8 characters long.',
    portalPasswordRequired: 'A shared password is required when the Password login method is enabled.',
    savingSchedule:    'Saving…',
    macFilterHintPassword: 'Only blocking is available on password-protected networks. Bypassing authentication is not applicable here.',
    macFilterHintOpen:     'Only blocking is available on open networks. There is no portal or password to bypass.',
    macFilterHintCaptive:  'Both block (deny access) and bypass (allow through the portal without authentication) are available for captive portal networks.',
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

/**
 * Returns true if `ip` belongs to the subnet defined by `networkIp` and `netmask`.
 * Works by converting all three addresses to 32-bit integers and ANDing with the mask.
 */
function ipToInt(ip) {
    return ip.split('.').reduce((acc, octet) => (acc << 8) | (+octet & 0xff), 0) >>> 0;
}
function ipInSubnet(ip, networkIp, netmask) {
    const maskInt = ipToInt(netmask);
    return (ipToInt(ip) & maskInt) === (ipToInt(networkIp) & maskInt);
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

        // Set bridge_lan_dhcp_mode BEFORE showTypeSections so the captive portal
        // guard inside it reads the correct saved value, not the default.
        $pane.find('.network-bridge-lan-dhcp-mode').val(net.bridge_lan_dhcp_mode || 'dhcp_client');

        showTypeSections($pane, net.type);
        syncTypePills($pane, net.type);

        // Password fields
        $pane.find('.network-password').val(net.password || '');
        $pane.find('.network-security').val(net.security || 'wpa2-psk');
        $pane.find('.network-cipher-suites').val(net.cipher_suites || 'CCMP');

        // Captive portal fields
        // auth_methods (array) takes precedence over the legacy single auth_method string
        const loadedAuthMethods = (net.auth_methods && net.auth_methods.length)
            ? net.auth_methods
            : [net.auth_method || 'click-through'];
        setActiveAuthMethods($pane, loadedAuthMethods);
        $pane.find('.network-portal-password').val(net.portal_password || '');
        $pane.find('.network-social-method').val(net.social_auth_method || 'facebook');
        $pane.find('.network-session-timeout').val(net.session_timeout || 60);
        $pane.find('.network-idle-timeout').val(net.idle_timeout || 15);
        $pane.find('.network-redirect-url').val(net.redirect_url || '');
        $pane.find('.network-download-limit').val(net.download_limit || '');
        $pane.find('.network-upload-limit').val(net.upload_limit || '');

        // Portal design
        const $designSelect = $pane.find('.network-portal-design-id');
        $designSelect.empty().append('<option value="">Default Design</option>');
        captivePortalDesigns.forEach(d => {
            $designSelect.append(`<option value="${d.id}" ${d.id == net.portal_design_id ? 'selected' : ''}>${escapeHtml(d.name)}</option>`);
        });

        // IP / DHCP
        const loadedIpMode = net.ip_mode || 'static';
        const loadedBridgeLanDhcpMode = net.bridge_lan_dhcp_mode || 'dhcp_client';
        const noDhcpServer = loadedIpMode === 'bridge'
            || (loadedIpMode === 'bridge_lan' && loadedBridgeLanDhcpMode === 'dhcp_client');
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

        // MAC filter & IP reservations
        renderMacList($pane, net.mac_filter_list || [], 1);
        renderReservationList($pane, net.dhcp_reservations || [], 1);
        applyReservationsVisibility($pane);
        applyMacFilterBypassUi($pane);

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

    // Bridge to WAN is not available for captive portal networks.
    // bridge_lan + dhcp_client is also not available for captive portal (portal needs a routable IP).
    const $ipModeSelect      = $pane.find('.network-ip-mode');
    const $bridgeOption      = $ipModeSelect.find('option[value="bridge"]');
    const $dhcpClientOption  = $pane.find('.network-bridge-lan-dhcp-mode option[value="dhcp_client"]');
    const $bridgeLanSubMode  = $pane.find('.network-bridge-lan-dhcp-mode');

    if (type === 'captive_portal') {
        if ($ipModeSelect.val() === 'bridge') {
            $ipModeSelect.val('static');
        }
        $bridgeOption.hide().prop('disabled', true);

        // Force dhcp_server if currently on dhcp_client
        if ($bridgeLanSubMode.val() === 'dhcp_client') {
            $bridgeLanSubMode.val('dhcp_server');
        }
        $dhcpClientOption.hide().prop('disabled', true);
    } else {
        $bridgeOption.show().prop('disabled', false);
        $dhcpClientOption.show().prop('disabled', false);
    }
    applyIpModeState($pane, $ipModeSelect.val());
    applyMacFilterBypassUi($pane);
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
    const isBridge    = mode === 'bridge';
    const isBridgeLan = mode === 'bridge_lan';

    // Show/hide the bridge_lan DHCP sub-mode selector
    $pane.find('.network-bridge-lan-dhcp-mode-wrap').toggle(isBridgeLan);

    const bridgeLanSubMode = $pane.find('.network-bridge-lan-dhcp-mode').val() || 'dhcp_client';
    const noDhcpServer = isBridge
        || (isBridgeLan && bridgeLanSubMode === 'dhcp_client');

    // IP address / network fields:
    //   bridge (WAN)              → hidden (no manual IP)
    //   bridge_lan + dhcp_client  → hidden (IP obtained from upstream)
    //   bridge_lan + dhcp_server  → visible (device needs a gateway IP for the subnet)
    //   static                    → visible
    const hideIpFields = isBridge || (isBridgeLan && bridgeLanSubMode === 'dhcp_client');
    $pane.find('.network-ip-address, .network-netmask, .network-gateway, .network-dns1, .network-dns2')
        .closest('.form-group')
        .toggle(!hideIpFields);

    // DHCP server row always visible; just enable/disable based on mode
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

    applyReservationsVisibility($pane);
}

function syncTypePills($pane, type) {
    $pane.find('.network-type-pill')
        .removeClass('active-password active-captive_portal active-open')
        .filter(`[data-type="${type}"]`)
        .addClass(`active-${type}`);
}

/**
 * Show/hide captive portal sub-fields based on the active methods array.
 * Accepts either a string (single method, legacy) or an array of method keys.
 */
function showAuthSubFields($pane, methods) {
    const active = Array.isArray(methods) ? methods : [methods];
    $pane.find('.network-captive-password-group').toggle(active.includes('password'));
    $pane.find('.network-social-group').toggle(active.includes('social'));
}

/**
 * Read which auth-method pills are currently active in the pane.
 * Returns an array of method keys in DOM order.
 */
function getActiveAuthMethods($pane) {
    const active = [];
    $pane.find('.network-auth-method-pill.active').each(function () {
        active.push($(this).data('method'));
    });
    return active.length ? active : ['click-through'];
}

/**
 * Activate the specified method pills and update sub-field visibility.
 * @param {jQuery} $pane
 * @param {string[]} methods  Array of method keys to mark active.
 */
function setActiveAuthMethods($pane, methods) {
    const active = Array.isArray(methods) && methods.length ? methods : ['click-through'];
    $pane.find('.network-auth-method-pill').each(function () {
        const m = $(this).data('method');
        $(this).toggleClass('active btn-primary', active.includes(m))
               .toggleClass('btn-outline-secondary', !active.includes(m));
    });
    showAuthSubFields($pane, active);
}

const MAC_PAGE_SIZE = 5;
const RES_PAGE_SIZE = 5;

/**
 * Returns true only for captive_portal networks, which are the only type where
 * bypassing the portal makes sense.  Password and open networks have no portal
 * to bypass, so only blocking is meaningful there.
 */
function macFilterAllowsBypass(networkType) {
    return networkType === 'captive_portal';
}

/**
 * Coerce any bypass entries to block for network types that do not support bypass.
 */
function coerceMacFilterListForType(list, networkType) {
    if (macFilterAllowsBypass(networkType)) return list;
    return list.map(e => e.type === 'bypass' ? { ...e, type: 'block' } : e);
}

/**
 * Update the MAC filter UI to reflect what the current network type supports:
 * - Show/hide the bypass option in the type dropdown.
 * - Coerce any existing bypass entries in the cache to block and re-render the list.
 * - Update the contextual hint text.
 */
function applyMacFilterBypassUi($pane) {
    const networkType = $pane.find('.network-type-select').val();
    const allowsBypass = macFilterAllowsBypass(networkType);

    // Enable/disable the bypass option in the add-row dropdown
    const $typeSelect   = $pane.find('.network-mac-type-select');
    const $bypassOption = $typeSelect.find('option[value="bypass"]');
    $bypassOption.prop('disabled', !allowsBypass).toggle(allowsBypass);
    // Always force block when bypass is not allowed — handles initial render where
    // the browser may select the first option in DOM order before JS runs.
    if (!allowsBypass) {
        $typeSelect.val('block');
    }

    // Coerce any bypass entries in the cache and re-render
    const netId = $pane.data('network-id');
    const net   = networks.find(n => n.id == netId);
    if (net && !allowsBypass) {
        const before = (net.mac_filter_list || []).map(normaliseMacEntry);
        const after  = coerceMacFilterListForType(before, networkType);
        net.mac_filter_list = after;
        const curPage = parseInt($pane.find('.network-mac-list').attr('data-mac-page')) || 1;
        renderMacList($pane, after, curPage);
    }

    // Update hint text
    const hintKey = networkType === 'password' ? 'macFilterHintPassword'
                  : networkType === 'open'     ? 'macFilterHintOpen'
                  :                              'macFilterHintCaptive';
    $pane.find('.network-mac-filter-hint').text(MSG[hintKey] || '');
}

/**
 * Normalise a mac_filter_list entry to { mac, type } shape.
 * Handles legacy formats: plain string → { mac, type: 'block' }
 */
function normaliseMacEntry(entry) {
    if (typeof entry === 'string') return { mac: entry.toUpperCase(), type: 'block' };
    return {
        mac:  (entry.mac || entry.address || '').toUpperCase(),
        type: entry.type === 'bypass' ? 'bypass' : 'block',
    };
}

function renderMacList($pane, list, page) {
    const normList   = (list || []).map(normaliseMacEntry).filter(e => !!e.mac);
    const total      = normList.length;
    const totalPages = total === 0 ? 0 : Math.ceil(total / MAC_PAGE_SIZE);
    const safePage   = total === 0 ? 1 : Math.min(Math.max(page || 1, 1), totalPages);
    const start      = (safePage - 1) * MAC_PAGE_SIZE;
    const pageItems  = normList.slice(start, start + MAC_PAGE_SIZE);

    const $tbody      = $pane.find('.network-mac-list');
    const $emptyRow   = $pane.find('.network-mac-empty');
    const $pagination = $pane.find('.mac-list-pagination');
    const $pageInfo   = $pane.find('.mac-page-info');

    $tbody.find('tr:not(.rl-empty-row)').remove();
    $emptyRow.toggle(total === 0);
    $tbody.attr('data-mac-page', safePage);

    if (total > 0) {
        pageItems.forEach((entry, pageIdx) => {
            const realIdx  = start + pageIdx;
            const isBypass = entry.type === 'bypass';
            $tbody.append(`
                <tr class="rl-row">
                    <td><span class="mac-badge ${isBypass ? 'mac-badge-bypass' : 'mac-badge-block'}">${isBypass ? MSG.macBadgeBypass : MSG.macBadgeBlock}</span></td>
                    <td class="rl-mono">${escapeHtml(entry.mac)}</td>
                    <td class="rl-action">
                        <button class="btn btn-sm btn-link text-danger p-0 network-mac-remove-btn" data-mac-index="${realIdx}" title="Remove">
                            <i data-feather="x" style="width:13px;height:13px;"></i>
                        </button>
                    </td>
                </tr>`);
        });

        $pageInfo.text(MSG.pageOf.replace('{page}', safePage).replace('{total}', totalPages));
        $pagination.show();
        $pagination.find('.mac-page-btn[data-dir="prev"]').prop('disabled', safePage <= 1);
        $pagination.find('.mac-page-btn[data-dir="next"]').prop('disabled', safePage >= totalPages);
    } else {
        $pagination.hide();
    }
    reRenderFeather();
}

function renderReservationList($pane, list, page) {
    const safeList   = (list || []).filter(e => e && e.mac && e.ip);
    const total      = safeList.length;
    const totalPages = total === 0 ? 0 : Math.ceil(total / RES_PAGE_SIZE);
    const safePage   = total === 0 ? 1 : Math.min(Math.max(page || 1, 1), totalPages);
    const start      = (safePage - 1) * RES_PAGE_SIZE;
    const pageItems  = safeList.slice(start, start + RES_PAGE_SIZE);

    const $tbody      = $pane.find('.network-reservation-list');
    const $emptyRow   = $pane.find('.network-reservation-empty');
    const $pagination = $pane.find('.res-list-pagination');
    const $pageInfo   = $pane.find('.res-page-info');

    $tbody.find('tr:not(.rl-empty-row)').remove();
    $emptyRow.toggle(total === 0);
    $tbody.attr('data-res-page', safePage);

    if (total > 0) {
        pageItems.forEach((entry, pageIdx) => {
            const realIdx = start + pageIdx;
            $tbody.append(`
                <tr class="rl-row">
                    <td class="rl-mono">${escapeHtml(entry.mac || '')}</td>
                    <td class="rl-mono rl-ip">${escapeHtml(entry.ip || '')}</td>
                    <td class="rl-action">
                        <button class="btn btn-sm btn-link text-danger p-0 network-reservation-remove-btn" data-reservation-index="${realIdx}" title="Remove">
                            <i data-feather="x" style="width:13px;height:13px;"></i>
                        </button>
                    </td>
                </tr>`);
        });

        $pageInfo.text(MSG.pageOf.replace('{page}', safePage).replace('{total}', totalPages));
        $pagination.show();
        $pagination.find('.res-page-btn[data-dir="prev"]').prop('disabled', safePage <= 1);
        $pagination.find('.res-page-btn[data-dir="next"]').prop('disabled', safePage >= totalPages);
    } else {
        $pagination.hide();
    }
    reRenderFeather();
}

function collectMacList(netId) {
    const net = networks.find(n => n.id == netId);
    return (net?.mac_filter_list || []).map(normaliseMacEntry);
}

/**
 * Derive the legacy mac_filter_mode from the unified list for backward compatibility.
 * If all entries are 'bypass' → 'allow-listed'
 * If any entry is 'block'   → 'block-listed'  (mixed list: block takes precedence in mode field)
 * If empty                  → 'none'
 */
function deriveMacFilterMode(netId) {
    const list = collectMacList(netId);
    if (!list.length) return 'none';
    const hasBlock  = list.some(e => e.type === 'block');
    const hasBypass = list.some(e => e.type === 'bypass');
    if (hasBlock && hasBypass) return 'mixed';
    if (hasBlock)  return 'block-listed';
    return 'allow-listed';
}

function collectReservations($pane) {
    const netId = $pane.data('network-id');
    const net   = networks.find(n => n.id == netId);
    return (net?.dhcp_reservations || []).filter(r => r.mac && r.ip);
}

function applyReservationsVisibility($pane) {
    const type    = $pane.find('.network-type-select').val();
    const ipMode  = $pane.find('.network-ip-mode').val();
    const subMode = $pane.find('.network-bridge-lan-dhcp-mode').val() || 'dhcp_client';
    const dhcpOn  = $pane.find('.network-dhcp-enabled').is(':checked');

    const dhcpServerActive = (ipMode === 'static' && dhcpOn)
        || (ipMode === 'bridge_lan' && subMode === 'dhcp_server');

    const showReservations = dhcpServerActive && type !== 'captive_portal';
    $pane.find('.dhcp-reservations-section').toggle(showReservations);
    $pane.find('.mac-res-grid').toggleClass('mac-res-grid--dual', showReservations);
}

// ============================================================================
// FORM DATA COLLECTION
// ============================================================================

function getFormData(netId, $pane) {
    const type = $pane.find('.network-type-select').val();
    const ipMode = $pane.find('.network-ip-mode').val();
    const isBridge    = ipMode === 'bridge';
    const isBridgeLan = ipMode === 'bridge_lan';
    const bridgeLanDhcpMode = $pane.find('.network-bridge-lan-dhcp-mode').val() || 'dhcp_client';
    // IP fields are hidden (and should be nulled) for bridge (WAN) and bridge_lan + dhcp_client
    const noManualIp  = isBridge || (isBridgeLan && bridgeLanDhcpMode === 'dhcp_client');
    // DHCP server is suppressed for bridge (WAN) and bridge_lan + dhcp_client
    const noDhcpServer = isBridge || (isBridgeLan && bridgeLanDhcpMode === 'dhcp_client');

    const data = {
        type,
        ssid: $pane.find('.network-ssid').val().trim(),
        visible: $pane.find('.network-visible').val() === '1',
        enabled: $pane.find('.network-enabled').is(':checked'),
        ip_mode: ipMode,
        bridge_lan_dhcp_mode: isBridgeLan ? bridgeLanDhcpMode : null,
        ip_address: noManualIp ? null : ($pane.find('.network-ip-address').val().trim() || null),
        netmask: noManualIp ? null : ($pane.find('.network-netmask').val().trim() || null),
        gateway: noManualIp ? null : ($pane.find('.network-gateway').val().trim() || null),
        dns1: noManualIp ? null : ($pane.find('.network-dns1').val().trim() || null),
        dns2: noManualIp ? null : ($pane.find('.network-dns2').val().trim() || null),
        dhcp_enabled: noDhcpServer ? false : $pane.find('.network-dhcp-enabled').is(':checked'),
        dhcp_start: noDhcpServer ? null : ($pane.find('.network-dhcp-start').val().trim() || null),
        dhcp_end: noDhcpServer ? null : (() => {
            const raw = $pane.find('.network-dhcp-end').val();
            if (raw === '' || raw === undefined || raw === null) return null;
            const n = parseInt(String(raw).trim(), 10);
            return Number.isNaN(n) ? null : n;
        })(),
        vlan_id: parseInt($pane.find('.network-vlan-id').val()) || null,
        vlan_tagging: $pane.find('.network-vlan-tagging').val(),
        mac_filter_list: coerceMacFilterListForType(collectMacList(netId), type),
        mac_filter_mode: deriveMacFilterMode(netId),
        dhcp_reservations: (noDhcpServer || type === 'captive_portal')
            ? null
            : collectReservations($pane),
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
        const activeMethods = getActiveAuthMethods($pane);
        data.auth_methods  = activeMethods;
        data.auth_method   = activeMethods[0] || 'click-through'; // backward compat
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

        // For password-type networks: validate the WiFi password field
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

        // For captive portal networks with "password" method: require the shared portal password
        if (data.type === 'captive_portal' && Array.isArray(data.auth_methods) && data.auth_methods.includes('password')) {
            if (!data.portal_password) {
                toastr.warning(MSG.portalPasswordRequired);
                $btn.prop('disabled', false).html(origHtml);
                $pane.find('.network-portal-password').focus();
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
        const type = $pane.find('.network-type-select').val();
        const ipMode = $pane.find('.network-ip-mode').val();
        const subMode = $pane.find('.network-bridge-lan-dhcp-mode').val() || 'dhcp_client';
        const dhcpOn = $pane.find('.network-dhcp-enabled').is(':checked');
        const noDhcpServer = ipMode === 'bridge' || (ipMode === 'bridge_lan' && subMode === 'dhcp_client');
        const includeReservations = !noDhcpServer && type !== 'captive_portal';

        const coercedList = coerceMacFilterListForType(collectMacList(netId), type);
        const res = await apiFetch(`${API}/locations/${location_id}/networks/${netId}`, {
            method: 'PUT',
            body: JSON.stringify({
                mac_filter_mode: deriveMacFilterMode(netId),
                mac_filter_list: coercedList,
                dhcp_reservations: includeReservations ? collectReservations($pane) : null,
            }),
        });

        // Update local cache
        const idx = networks.findIndex(n => n.id == netId);
        if (idx >= 0) networks[idx] = res.data.network;

        toastr.success(MSG.macFilterSaved);
        if (res.data.config_version_incremented) {
            toastr.info(MSG.routerReconfigure, '', { timeOut: 5000 });
        }
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

    // IP mode change → apply field state for static / bridge_lan / bridge
    $(document).off('change.nmgr', '.network-ip-mode').on('change.nmgr', '.network-ip-mode', function () {
        applyIpModeState($(this).closest('.tab-pane'), $(this).val());
    });

    // bridge_lan DHCP sub-mode change → re-apply field state
    $(document).off('change.nmgr', '.network-bridge-lan-dhcp-mode').on('change.nmgr', '.network-bridge-lan-dhcp-mode', function () {
        applyIpModeState($(this).closest('.tab-pane'), $(this).closest('.tab-pane').find('.network-ip-mode').val());
    });

    // DHCP enabled toggle → update reservations visibility
    $(document).off('change.nmgr', '.network-dhcp-enabled').on('change.nmgr', '.network-dhcp-enabled', function () {
        applyReservationsVisibility($(this).closest('.tab-pane'));
    });

    // (mac-filter-mode select removed — mode is now derived from the list)

    // Add IP reservation
    $(document).off('click.nmgr', '.network-reservation-add-btn').on('click.nmgr', '.network-reservation-add-btn', function () {
        const $pane = $(this).closest('.tab-pane');
        const mac   = $pane.find('.network-reservation-mac').val().trim().toUpperCase().replace(/-/g, ':');
        const ip    = $pane.find('.network-reservation-ip').val().trim();

        // — MAC validation
        if (!/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/.test(mac)) {
            toastr.warning(MSG.invalidMac || 'Invalid MAC address. Use format 00:11:22:33:44:55.');
            return;
        }

        // — Basic IPv4 syntax check
        if (!isValidIPv4(ip)) {
            toastr.warning('Invalid IP address.');
            return;
        }

        // — Must belong to this network's subnet
        const netId  = $pane.data('network-id');
        const net    = networks.find(n => n.id == netId);
        if (!net) return;

        const gatewayIp = net.ip_address || $pane.find('.network-ip-address').val().trim();
        const netmask   = net.netmask    || $pane.find('.network-netmask').val().trim();

        if (gatewayIp && netmask) {
            if (!ipInSubnet(ip, gatewayIp, netmask)) {
                toastr.warning(`IP ${ip} is outside this network's subnet (${gatewayIp} / ${netmask}).`);
                return;
            }
            // Reserved IP must not be the gateway itself
            if (ip === gatewayIp) {
                toastr.warning('Reserved IP cannot be the gateway address.');
                return;
            }
        }

        // — Duplicate checks
        net.dhcp_reservations = net.dhcp_reservations || [];
        const normMac = mac.toUpperCase();
        if (net.dhcp_reservations.some(r => r.mac.toUpperCase() === normMac)) {
            toastr.warning('A reservation for this MAC address already exists.');
            return;
        }
        if (net.dhcp_reservations.some(r => r.ip === ip)) {
            toastr.warning('This IP address is already reserved for another device.');
            return;
        }

        net.dhcp_reservations.push({ mac, ip });
        const validResCount = net.dhcp_reservations.filter(e => e && e.mac && e.ip).length;
        const lastResPage   = Math.ceil(validResCount / RES_PAGE_SIZE);
        renderReservationList($pane, net.dhcp_reservations, lastResPage);
        $pane.find('.network-reservation-mac, .network-reservation-ip').val('');
    });

    // Remove IP reservation
    $(document).off('click.nmgr', '.network-reservation-remove-btn').on('click.nmgr', '.network-reservation-remove-btn', function () {
        const $pane = $(this).closest('.tab-pane');
        const netId = $pane.data('network-id');
        const idx   = parseInt($(this).data('reservation-index'));
        const net   = networks.find(n => n.id == netId);
        if (!net || !net.dhcp_reservations) return;
        net.dhcp_reservations.splice(idx, 1);
        const curPage    = parseInt($pane.find('.network-reservation-list').attr('data-res-page')) || 1;
        const remaining  = (net.dhcp_reservations || []).filter(e => e && e.mac && e.ip).length;
        const totalPages = remaining === 0 ? 1 : Math.ceil(remaining / RES_PAGE_SIZE);
        renderReservationList($pane, net.dhcp_reservations, Math.min(curPage, totalPages));
    });

    // Paginate IP reservation list
    $(document).off('click.nmgr', '.res-page-btn').on('click.nmgr', '.res-page-btn', function () {
        const $pane  = $(this).closest('.tab-pane');
        const netId  = $pane.data('network-id');
        const net    = networks.find(n => n.id == netId);
        if (!net) return;
        const dir     = $(this).data('dir');
        const curPage = parseInt($pane.find('.network-reservation-list').attr('data-res-page')) || 1;
        const newPage = dir === 'next' ? curPage + 1 : curPage - 1;
        renderReservationList($pane, net.dhcp_reservations || [], newPage);
    });

    // Auth method pill toggle
    $(document).off('click.nmgr', '.network-auth-method-pill').on('click.nmgr', '.network-auth-method-pill', function () {
        const $pane   = $(this).closest('.tab-pane');
        const isActive = $(this).hasClass('active');
        // Toggle active state on the clicked pill
        $(this).toggleClass('active btn-primary', !isActive)
               .toggleClass('btn-outline-secondary', isActive);
        // Ensure at least one method stays active — revert if this was the last one
        if (!$pane.find('.network-auth-method-pill.active').length) {
            $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        }
        showAuthSubFields($pane, getActiveAuthMethods($pane));
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

    // Add MAC entry (unified bypass/block list)
    $(document).off('click.nmgr', '.network-mac-add-btn').on('click.nmgr', '.network-mac-add-btn', function () {
        const $pane       = $(this).closest('.tab-pane');
        const rawMac      = $pane.find('.network-mac-input').val().trim();
        const mac         = rawMac.toUpperCase().replace(/-/g, ':');
        const networkType = $pane.find('.network-type-select').val();
        const rawType     = $pane.find('.network-mac-type-select').val() || 'block';
        // Bypass is only meaningful for captive portal — silently coerce to block otherwise
        const type        = macFilterAllowsBypass(networkType) ? rawType : 'block';

        if (!/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/.test(mac)) {
            toastr.warning(MSG.invalidMac);
            return;
        }
        const netId = $pane.data('network-id');
        const net   = networks.find(n => n.id == netId);
        if (!net) return;

        net.mac_filter_list = (net.mac_filter_list || []).map(normaliseMacEntry);

        if (net.mac_filter_list.some(e => e.mac === mac)) {
            toastr.warning('This MAC address is already in the list.');
            return;
        }

        net.mac_filter_list.push({ mac, type });

        // Jump to last page so the new entry is visible
        const validCount = net.mac_filter_list.filter(e => normaliseMacEntry(e).mac).length;
        const lastPage   = Math.ceil(validCount / MAC_PAGE_SIZE);
        renderMacList($pane, net.mac_filter_list, lastPage);
        $pane.find('.network-mac-input').val('');
    });

    // Remove MAC entry
    $(document).off('click.nmgr', '.network-mac-remove-btn').on('click.nmgr', '.network-mac-remove-btn', function () {
        const $pane = $(this).closest('.tab-pane');
        const netId = $pane.data('network-id');
        const idx   = parseInt($(this).data('mac-index'));
        const net   = networks.find(n => n.id == netId);
        if (!net || !net.mac_filter_list) return;

        net.mac_filter_list = net.mac_filter_list.map(normaliseMacEntry);
        net.mac_filter_list.splice(idx, 1);

        const curPage    = parseInt($pane.find('.network-mac-list').attr('data-mac-page')) || 1;
        const remaining  = net.mac_filter_list.filter(e => normaliseMacEntry(e).mac).length;
        const totalPages = remaining === 0 ? 1 : Math.ceil(remaining / MAC_PAGE_SIZE);
        renderMacList($pane, net.mac_filter_list, Math.min(curPage, totalPages));
    });

    // Paginate MAC list
    $(document).off('click.nmgr', '.mac-page-btn').on('click.nmgr', '.mac-page-btn', function () {
        const $pane  = $(this).closest('.tab-pane');
        const netId  = $pane.data('network-id');
        const net    = networks.find(n => n.id == netId);
        if (!net) return;
        const dir    = $(this).data('dir');
        const curPage = parseInt($pane.find('.network-mac-list').attr('data-mac-page')) || 1;
        const newPage = dir === 'next' ? curPage + 1 : curPage - 1;
        renderMacList($pane, net.mac_filter_list || [], newPage);
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
