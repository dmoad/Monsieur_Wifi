/**
 * location-details-networks.js
 *
 * WiFi Networks tab (Tab 4 of the Location Details page):
 *  - SSID list rendering + Add Network (SSID prompt modal) + kebab (QR / Delete)
 *  - Right-side drawer editor: General / Network / Security / Advanced panels
 *  - Scratch buffers for MAC filter entries + DHCP reservations (revert on Cancel)
 *  - Working-hours picker (mode radio + per-day range editor)
 *  - URL binding: ?edit=<ssid-id> opens the drawer on page load
 *
 * Depends on globals from location-details.js (API, i18n, commonI18n, apiFetch,
 * handleApiError, reRenderFeather, isValidIPv4, ipToInt, ipInSubnet,
 * location_id, networkSourceLocationId, currentDeviceData) and on mw-primitives.js
 * (MwDrawer, MwConfirm). Must load after location-details.js script-wise, but
 * ldNetworks.load() is only invoked after $(window).on('load') resolves.
 */

'use strict';

const MAC_REGEX = /^([0-9A-F]{2}:){5}[0-9A-F]{2}$/;

const ldNetworks = (function () {
    let loaded = false;
    let data = [];
    let captiveDesigns = null; // null = not fetched; array once fetched
    let vlanEnabled = false; // location-wide VLAN setting, fetched alongside networks
    // Drawer-local scratch for MAC filter + DHCP reservations (edits revert on Cancel)
    let drawerMac = [];
    let drawerReservations = [];
    // Drawer-local scratch for working-hours picker: { [day]: [{startHour, endHour}, ...] }
    let drawerSchedule = {};
    const SCHEDULE_DAYS = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

    function netLocationId() {
        return (typeof networkSourceLocationId !== 'undefined' && networkSourceLocationId != null)
            ? String(networkSourceLocationId)
            : String(location_id);
    }

    async function ensureCaptiveDesigns() {
        if (captiveDesigns !== null) return captiveDesigns;
        try {
            const res = await apiFetch('/api/captive-portal-designs', { method: 'POST' });
            captiveDesigns = (res && res.data) || [];
        } catch (err) {
            console.warn('ldNetworks: failed to load captive portal designs', err);
            captiveDesigns = [];
        }
        return captiveDesigns;
    }

    function populateCaptiveDesignSelect(selectedId) {
        const select = document.getElementById('ld-net-portal-design');
        if (!select || !Array.isArray(captiveDesigns)) return;
        // Keep the first "Default Design" option, replace the rest
        const defaultOpt = select.querySelector('option[value=""]');
        select.innerHTML = '';
        if (defaultOpt) select.appendChild(defaultOpt);
        captiveDesigns.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.name || '';
            if (String(d.id) === String(selectedId)) opt.selected = true;
            select.appendChild(opt);
        });
        if (!selectedId) select.value = '';
    }

    function getActiveAuthMethods() {
        return [...document.querySelectorAll('.ld-net-auth-method:checked')].map(cb => cb.value);
    }

    function setActiveAuthMethods(methods) {
        const set = new Set((methods || []).map(String));
        document.querySelectorAll('.ld-net-auth-method').forEach(cb => {
            cb.checked = set.has(cb.value);
        });
    }

    function applyAuthMethodVisibility() {
        const methods = getActiveAuthMethods();
        const pwdGroup = document.getElementById('ld-net-portal-pwd-group');
        const otpGroup = document.getElementById('ld-net-email-otp-group');
        const socGroup = document.getElementById('ld-net-social-group');
        if (pwdGroup) pwdGroup.style.display = methods.includes('password') ? '' : 'none';
        if (otpGroup) otpGroup.style.display = methods.includes('email') ? '' : 'none';
        if (socGroup) socGroup.style.display = methods.includes('social') ? '' : 'none';
    }

    function applyIpModeVisibility() {
        const mode = document.getElementById('ld-net-ip-mode').value;
        const subMode = document.getElementById('ld-net-bridge-lan-mode').value || 'dhcp_client';
        const isBridge = mode === 'bridge';
        const isBridgeLan = mode === 'bridge_lan';
        const noDhcpServer = isBridge || (isBridgeLan && subMode === 'dhcp_client');
        const hideIpFields = isBridge || (isBridgeLan && subMode === 'dhcp_client');

        const bridgeLanWrap = document.querySelector('.ld-net-bridge-lan-wrap');
        if (bridgeLanWrap) bridgeLanWrap.style.display = isBridgeLan ? '' : 'none';

        const ipFields = document.querySelector('.ld-net-ip-fields');
        if (ipFields) ipFields.style.display = hideIpFields ? 'none' : '';

        const dhcpSection = document.querySelector('.ld-net-dhcp-section');
        if (dhcpSection) dhcpSection.style.display = noDhcpServer ? 'none' : '';

        // Bridge networks can't be captive_portal (backend doesn't allow bridge for captive)
    }

    function applyVlanGating() {
        const vlanId = document.getElementById('ld-net-vlan-id');
        const vlanTag = document.getElementById('ld-net-vlan-tagging');
        const hint = document.getElementById('ld-net-vlan-hint');
        if (vlanId) vlanId.disabled = !vlanEnabled;
        if (vlanTag) vlanTag.disabled = !vlanEnabled;
        if (hint) hint.style.display = vlanEnabled ? 'none' : '';
    }

    function normaliseMacEntry(entry) {
        if (typeof entry === 'string') return { mac: entry.toUpperCase(), type: 'block' };
        return {
            mac: (entry.mac || entry.address || '').toUpperCase(),
            type: entry.type === 'bypass' ? 'bypass' : 'block',
        };
    }

    function macAllowsBypass(type) {
        return type === 'captive_portal';
    }

    function coerceMacListForType(list, type) {
        if (macAllowsBypass(type)) return list;
        return list.map(e => e.type === 'bypass' ? { ...e, type: 'block' } : e);
    }

    function deriveMacFilterMode(list) {
        if (!list.length) return 'none';
        const hasBlock  = list.some(e => e.type === 'block');
        const hasBypass = list.some(e => e.type === 'bypass');
        if (hasBlock && hasBypass) return 'mixed';
        if (hasBlock) return 'block-listed';
        return 'allow-listed';
    }

    function applyMacBypassUi(type) {
        const allows = macAllowsBypass(type);
        const typeSelect = document.getElementById('ld-net-mac-type');
        if (typeSelect) {
            const bypassOpt = typeSelect.querySelector('option[value="bypass"]');
            if (bypassOpt) {
                bypassOpt.disabled = !allows;
                bypassOpt.style.display = allows ? '' : 'none';
            }
            if (!allows) typeSelect.value = 'block';
        }
        // Coerce any bypass entries in scratch list when type changes
        if (!allows) {
            drawerMac = coerceMacListForType(drawerMac, type);
            renderMacList();
        }
        const hint = document.getElementById('ld-net-mac-hint');
        if (hint) {
            const key = type === 'password' ? 'networks_mac_hint_password'
                      : type === 'open'     ? 'networks_mac_hint_open'
                      :                        'networks_mac_hint_captive';
            hint.textContent = i18n[key] || '';
        }
    }

    function applyReservationsVisibility() {
        const type    = getNetType();
        const ipMode  = document.getElementById('ld-net-ip-mode').value;
        const subMode = document.getElementById('ld-net-bridge-lan-mode').value || 'dhcp_client';
        const dhcpOn  = document.getElementById('ld-net-dhcp-enabled').checked;
        const dhcpServerActive = (ipMode === 'static' && dhcpOn)
            || (ipMode === 'bridge_lan' && subMode === 'dhcp_server');
        const show = dhcpServerActive && type !== 'captive_portal';
        const block = document.querySelector('.ld-net-reservations-block');
        if (block) block.style.display = show ? '' : 'none';
    }

    function renderMacList() {
        const tbody = document.getElementById('ld-net-mac-list');
        if (!tbody) return;
        const list = drawerMac.map(normaliseMacEntry).filter(e => e.mac);
        tbody.innerHTML = '';
        if (!list.length) {
            const tr = document.createElement('tr');
            tr.className = 'ld-net-rl-empty';
            const td = document.createElement('td');
            td.colSpan = 3;
            td.textContent = i18n.networks_mac_list_empty || 'No MAC rules added';
            tr.appendChild(td);
            tbody.appendChild(tr);
            return;
        }
        list.forEach((entry, idx) => {
            const isBypass = entry.type === 'bypass';
            const tr = document.createElement('tr');
            tr.className = 'ld-net-rl-row';
            tr.innerHTML = `
                <td><span class="ld-net-mac-badge ${isBypass ? 'is-bypass' : 'is-block'}">${isBypass ? (i18n.networks_mac_badge_bypass || 'Bypass') : (i18n.networks_mac_badge_block || 'Block')}</span></td>
                <td class="ld-net-rl-mono">${entry.mac}</td>
                <td class="ld-net-rl-action"><button type="button" class="btn btn-link btn-sm text-danger p-0 ld-net-mac-remove" data-idx="${idx}" aria-label="Remove"><i data-feather="x"></i></button></td>`;
            tbody.appendChild(tr);
        });
        if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
    }

    function renderReservations() {
        const tbody = document.getElementById('ld-net-res-list');
        if (!tbody) return;
        const list = (drawerReservations || []).filter(r => r && r.mac && r.ip);
        tbody.innerHTML = '';
        if (!list.length) {
            const tr = document.createElement('tr');
            tr.className = 'ld-net-rl-empty';
            const td = document.createElement('td');
            td.colSpan = 3;
            td.textContent = i18n.networks_reservation_list_empty || 'No reservations added';
            tr.appendChild(td);
            tbody.appendChild(tr);
            return;
        }
        list.forEach((entry, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'ld-net-rl-row';
            tr.innerHTML = `
                <td class="ld-net-rl-mono">${entry.mac}</td>
                <td class="ld-net-rl-mono">${entry.ip}</td>
                <td class="ld-net-rl-action"><button type="button" class="btn btn-link btn-sm text-danger p-0 ld-net-res-remove" data-idx="${idx}" aria-label="Remove"><i data-feather="x"></i></button></td>`;
            tbody.appendChild(tr);
        });
        if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
    }

    function addMacEntry() {
        const inputEl = document.getElementById('ld-net-mac-input');
        const typeEl  = document.getElementById('ld-net-mac-type');
        const networkType = getNetType();
        const mac = (inputEl.value || '').trim().toUpperCase().replace(/-/g, ':');
        const rawType = typeEl.value || 'block';
        const entryType = macAllowsBypass(networkType) ? rawType : 'block';
        if (!MAC_REGEX.test(mac)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_mac_invalid || 'Invalid MAC address.');
            return;
        }
        if (drawerMac.some(e => normaliseMacEntry(e).mac === mac)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_mac_duplicate || 'This MAC is already in the list.');
            return;
        }
        drawerMac.push({ mac, type: entryType });
        inputEl.value = '';
        renderMacList();
    }

    function removeMacEntry(idx) {
        if (idx < 0 || idx >= drawerMac.length) return;
        drawerMac.splice(idx, 1);
        renderMacList();
    }

    function addReservation() {
        const macEl = document.getElementById('ld-net-res-mac');
        const ipEl  = document.getElementById('ld-net-res-ip');
        const mac = (macEl.value || '').trim().toUpperCase().replace(/-/g, ':');
        const ip  = (ipEl.value || '').trim();
        if (!MAC_REGEX.test(mac)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_mac_invalid || 'Invalid MAC address.');
            return;
        }
        if (!isValidIPv4(ip)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_reservation_invalid_ip || 'Invalid IP address.');
            return;
        }
        const gateway = document.getElementById('ld-net-ip-address').value.trim();
        const netmask = document.getElementById('ld-net-netmask').value.trim();
        if (gateway && netmask && isValidIPv4(gateway) && isValidIPv4(netmask)) {
            if (!ipInSubnet(ip, gateway, netmask)) {
                const msg = (i18n.networks_reservation_outside_subnet || 'IP {ip} is outside the subnet ({gateway} / {netmask}).')
                    .replace('{ip}', ip).replace('{gateway}', gateway).replace('{netmask}', netmask);
                if (typeof toastr !== 'undefined') toastr.warning(msg);
                return;
            }
            if (ip === gateway) {
                if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_reservation_is_gateway || 'Reserved IP cannot be the gateway.');
                return;
            }
        }
        const dhcpEnabled = document.getElementById('ld-net-dhcp-enabled').checked;
        const dhcpStart   = document.getElementById('ld-net-dhcp-start').value.trim();
        const dhcpSize    = parseInt(document.getElementById('ld-net-dhcp-end').value, 10);
        if (dhcpEnabled && isValidIPv4(dhcpStart) && dhcpSize >= 1) {
            const startInt = ipToInt(dhcpStart);
            const lastInt  = startInt + dhcpSize - 1;
            const ipInt    = ipToInt(ip);
            if (ipInt < startInt || ipInt > lastInt) {
                const lastIp = [(lastInt >>> 24) & 0xff, (lastInt >>> 16) & 0xff, (lastInt >>> 8) & 0xff, lastInt & 0xff].join('.');
                const msg = (i18n.networks_reservation_outside_pool || 'IP {ip} is outside DHCP pool ({start} – {end}).')
                    .replace('{ip}', ip).replace('{start}', dhcpStart).replace('{end}', lastIp);
                if (typeof toastr !== 'undefined') toastr.warning(msg);
                return;
            }
        }
        if (drawerReservations.some(r => r.mac.toUpperCase() === mac)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_reservation_duplicate_mac || 'Duplicate MAC in reservations.');
            return;
        }
        if (drawerReservations.some(r => r.ip === ip)) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_reservation_duplicate_ip || 'Duplicate IP in reservations.');
            return;
        }
        drawerReservations.push({ mac, ip });
        macEl.value = '';
        ipEl.value = '';
        renderReservations();
    }

    function removeReservation(idx) {
        if (idx < 0 || idx >= drawerReservations.length) return;
        drawerReservations.splice(idx, 1);
        renderReservations();
    }

    function scheduleFromWorkingHours(workingHours) {
        const out = {};
        SCHEDULE_DAYS.forEach(d => { out[d] = []; });
        (workingHours || []).forEach(e => {
            const day = (e.day || '').toLowerCase();
            if (!out[day]) return;
            const start = e.startHour != null ? +e.startHour : parseInt((e.startTime || '').split(':')[0], 10);
            const end   = e.endHour   != null ? +e.endHour   : parseInt((e.endTime   || '').split(':')[0], 10);
            if (!Number.isInteger(start) || !Number.isInteger(end)) return;
            out[day].push({ startHour: start, endHour: end });
        });
        return out;
    }

    function scheduleToWorkingHours(schedule) {
        const out = [];
        SCHEDULE_DAYS.forEach(day => {
            (schedule[day] || []).forEach(({ startHour, endHour }) => {
                if (!Number.isInteger(startHour) || !Number.isInteger(endHour)) return;
                if (endHour <= startHour) return;
                out.push({
                    day,
                    startHour,
                    endHour,
                    startTime: String(startHour).padStart(2,'0') + ':00',
                    endTime:   String(endHour).padStart(2,'0') + ':00',
                });
            });
        });
        return out;
    }

    function buildHourOptions(selectEl, selected, minHour, maxHour) {
        selectEl.innerHTML = '';
        for (let h = minHour; h <= maxHour; h++) {
            const opt = document.createElement('option');
            opt.value = String(h);
            opt.textContent = String(h).padStart(2,'0') + ':00';
            if (h === selected) opt.selected = true;
            selectEl.appendChild(opt);
        }
    }

    function renderScheduleEditor() {
        const editor = document.getElementById('ld-net-schedule-editor');
        if (!editor) return;
        const dayTpl = document.getElementById('ld-net-schedule-day-tpl');
        const rangeTpl = document.getElementById('ld-net-schedule-range-tpl');
        if (!dayTpl || !rangeTpl) return;
        editor.innerHTML = '';
        SCHEDULE_DAYS.forEach(day => {
            const row = dayTpl.content.firstElementChild.cloneNode(true);
            row.dataset.day = day;
            row.querySelector('.ld-net-schedule-day-name').textContent =
                i18n['networks_schedule_day_' + day] || day.charAt(0).toUpperCase() + day.slice(1);
            const ranges = drawerSchedule[day] || [];
            const hasRanges = ranges.length > 0;
            row.querySelector('.ld-net-schedule-day-enabled').checked = hasRanges;
            const rangesEl = row.querySelector('.ld-net-schedule-day-ranges');
            const addBtn = row.querySelector('.ld-net-schedule-day-add');
            if (hasRanges) {
                ranges.forEach((r, idx) => rangesEl.appendChild(buildRangeRow(day, idx, r.startHour, r.endHour, rangeTpl)));
                addBtn.style.display = '';
            }
            editor.appendChild(row);
        });
        if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
    }

    function buildRangeRow(day, idx, startHour, endHour, rangeTpl) {
        const row = rangeTpl.content.firstElementChild.cloneNode(true);
        row.dataset.day = day;
        row.dataset.idx = String(idx);
        const startSel = row.querySelector('.ld-net-schedule-range-start');
        const endSel   = row.querySelector('.ld-net-schedule-range-end');
        buildHourOptions(startSel, startHour, 0, 23);
        buildHourOptions(endSel, endHour, 1, 24);
        return row;
    }

    function applyScheduleMode(mode) {
        const editor = document.getElementById('ld-net-schedule-editor');
        if (!editor) return;
        if (mode === 'always') {
            editor.style.display = 'none';
        } else {
            // Default to business hours if schedule is empty when switching to restricted
            const hasAny = SCHEDULE_DAYS.some(d => (drawerSchedule[d] || []).length > 0);
            if (!hasAny) {
                drawerSchedule = {};
                SCHEDULE_DAYS.forEach(d => { drawerSchedule[d] = []; });
                ['monday','tuesday','wednesday','thursday','friday'].forEach(d => {
                    drawerSchedule[d] = [{ startHour: 9, endHour: 17 }];
                });
            }
            renderScheduleEditor();
            editor.style.display = '';
        }
    }

    function initSchedulePicker(net) {
        drawerSchedule = scheduleFromWorkingHours(net.working_hours);
        const hasAny = SCHEDULE_DAYS.some(d => (drawerSchedule[d] || []).length > 0);
        const mode = hasAny ? 'restricted' : 'always';
        const modeInput = document.querySelector(`.ld-net-schedule-mode[value="${mode}"]`);
        if (modeInput) modeInput.checked = true;
        applyScheduleMode(mode);
    }

    function getScheduleModeValue() {
        const checked = document.querySelector('.ld-net-schedule-mode:checked');
        return checked ? checked.value : 'always';
    }

    function collectSchedulePayload() {
        if (getScheduleModeValue() === 'always') return [];
        // Update drawerSchedule from DOM before serialising
        const editor = document.getElementById('ld-net-schedule-editor');
        if (!editor) return [];
        const next = {};
        SCHEDULE_DAYS.forEach(d => { next[d] = []; });
        editor.querySelectorAll('.ld-net-schedule-day').forEach(row => {
            const day = row.dataset.day;
            const enabled = row.querySelector('.ld-net-schedule-day-enabled').checked;
            if (!enabled) return;
            row.querySelectorAll('.ld-net-schedule-range').forEach(r => {
                const start = parseInt(r.querySelector('.ld-net-schedule-range-start').value, 10);
                const end   = parseInt(r.querySelector('.ld-net-schedule-range-end').value, 10);
                if (Number.isInteger(start) && Number.isInteger(end) && end > start) {
                    next[day].push({ startHour: start, endHour: end });
                }
            });
        });
        drawerSchedule = next;
        return scheduleToWorkingHours(next);
    }

    function toggleDay(day, enabled) {
        if (enabled) {
            if (!drawerSchedule[day] || !drawerSchedule[day].length) {
                drawerSchedule[day] = [{ startHour: 9, endHour: 17 }];
            }
        } else {
            drawerSchedule[day] = [];
        }
        renderScheduleEditor();
    }

    function addRangeToDay(day) {
        drawerSchedule[day] = drawerSchedule[day] || [];
        // Pick a default that doesn't overlap: place at 18 if free, else last-end → last-end+2
        const last = drawerSchedule[day][drawerSchedule[day].length - 1];
        const start = last ? Math.min(last.endHour, 22) : 18;
        const end   = Math.min(start + 2, 24);
        drawerSchedule[day].push({ startHour: start, endHour: end });
        renderScheduleEditor();
    }

    function removeRange(day, idx) {
        if (!drawerSchedule[day]) return;
        drawerSchedule[day].splice(idx, 1);
        renderScheduleEditor();
    }

    function bandLabel(radio) {
        if (radio === '2.4') return i18n.networks_band_24 || '2.4 GHz';
        if (radio === '5')   return i18n.networks_band_5  || '5 GHz';
        return i18n.networks_band_both || '2.4 GHz + 5 GHz';
    }

    function typeLabel(type) {
        return i18n['networks_type_' + type] || type;
    }

    function render() {
        const listEl  = document.getElementById('ld-networks-list');
        const emptyEl = document.getElementById('ld-networks-empty');
        const tpl     = document.getElementById('ld-network-row-tpl');
        if (!listEl || !tpl) return;

        listEl.innerHTML = '';

        if (!data.length) {
            emptyEl.style.display = '';
            listEl.style.display = 'none';
            return;
        }
        emptyEl.style.display = 'none';
        listEl.style.display = '';

        for (const net of data) {
            const row = tpl.content.firstElementChild.cloneNode(true);
            row.dataset.networkId = net.id;
            row.dataset.networkType = net.type;

            row.querySelector('.ld-net-name').textContent = net.ssid || '';

            const typeBadge = row.querySelector('.ld-net-type-badge');
            typeBadge.textContent = typeLabel(net.type);
            typeBadge.classList.add('ld-net-type-' + net.type);

            const statusBadge = row.querySelector('.ld-net-status-badge');
            const active = net.enabled !== false;
            statusBadge.textContent = active
                ? (i18n.networks_status_active || 'Active')
                : (i18n.networks_status_inactive || 'Inactive');
            statusBadge.classList.add(active ? 'is-active' : 'is-inactive');

            row.querySelector('.ld-net-band').textContent = bandLabel(net.radio);

            if (net.vlan_id) {
                const vlanEl = row.querySelector('.ld-net-vlan');
                vlanEl.textContent = (i18n.networks_vlan_label || 'VLAN') + ' ' + net.vlan_id;
                vlanEl.style.display = '';
            }

            listEl.appendChild(row);
        }

        if (typeof feather !== 'undefined') feather.replace({ width: 18, height: 18 });
    }

    async function load() {
        if (!location_id) return;
        const loadingEl = document.getElementById('ld-networks-loading');
        const emptyEl   = document.getElementById('ld-networks-empty');
        const errorEl   = document.getElementById('ld-networks-error');
        const listEl    = document.getElementById('ld-networks-list');
        if (!loadingEl) return;

        loadingEl.style.display = '';
        emptyEl.style.display = 'none';
        errorEl.style.display = 'none';
        listEl.style.display = 'none';

        try {
            const [netsRes, settingsRes] = await Promise.all([
                apiFetch(`${API}/locations/${netLocationId()}/networks`),
                apiFetch(`${API}/locations/${location_id}/settings`).catch(() => null),
            ]);
            data = (netsRes && netsRes.data && netsRes.data.networks) || [];
            vlanEnabled = !!(settingsRes && settingsRes.data && settingsRes.data.settings && settingsRes.data.settings.vlan_enabled);
            render();
            loaded = true;
            const addBtn = document.getElementById('ld-networks-add-btn');
            if (addBtn) addBtn.disabled = false;
            restoreEditFromUrl();
        } catch (err) {
            console.error('ldNetworks.load', err);
            errorEl.style.display = '';
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    async function remove(netId) {
        const idx = data.findIndex(n => String(n.id) === String(netId));
        if (idx < 0) return;
        const net = data[idx];
        const msg = (i18n.networks_delete_confirm || 'Delete "{ssid}"? This cannot be undone.').replace('{ssid}', net.ssid || '');
        const ok = await MwConfirm.open({
            title: i18n.networks_delete_title || 'Delete WiFi network?',
            message: msg,
            confirmText: i18n.networks_drawer_delete || 'Delete',
            cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
            destructive: true,
        });
        if (!ok) return;
        const btn = document.getElementById('ld-network-drawer-delete');
        if (btn) btn.disabled = true;
        try {
            await apiFetch(`${API}/locations/${netLocationId()}/networks/${netId}`, { method: 'DELETE' });
            data.splice(idx, 1);
            render();
            if (typeof toastr !== 'undefined') toastr.success(i18n.networks_delete_success || 'Network deleted.');
            if (typeof MwDrawer !== 'undefined') MwDrawer.close('ld-network-drawer');
        } catch (err) {
            handleApiError(err, 'ldNetworks.remove');
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    function openAddNetworkModal() {
        const input = document.getElementById('ld-network-add-ssid');
        if (input) input.value = '';
        const $m = typeof $ !== 'undefined' ? $('#ld-network-add-modal') : null;
        if ($m && $m.length) {
            $m.one('shown.bs.modal', function () {
                if (input) input.focus();
                if (typeof reRenderFeather === 'function') reRenderFeather();
            });
            $m.modal('show');
        }
    }

    async function add() {
        const input = document.getElementById('ld-network-add-ssid');
        const confirmBtn = document.getElementById('ld-network-add-confirm');
        const btn = document.getElementById('ld-networks-add-btn');
        if (!input) return;

        const ssid = input.value.trim();
        if (!ssid) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_ssid_required || 'SSID is required');
            input.focus();
            return;
        }
        if (ssid.length > 32) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_ssid_too_long || 'SSID must be 32 characters or fewer');
            input.focus();
            return;
        }

        if (btn) btn.disabled = true;
        if (confirmBtn) confirmBtn.disabled = true;
        try {
            const next = data.length;
            const octet = 10 + next;
            const res = await apiFetch(`${API}/locations/${netLocationId()}/networks`, {
                method: 'POST',
                body: JSON.stringify({
                    type: 'password',
                    ssid,
                    enabled: true,
                    ip_address: `192.168.${octet}.1`,
                    dhcp_start: `192.168.${octet}.100`,
                    dhcp_end: 101,
                }),
            });
            if (res && res.data && res.data.network) {
                const newNet = res.data.network;
                data.push(newNet);
                render();
                if (typeof $ !== 'undefined' && $('#ld-network-add-modal').length) {
                    $('#ld-network-add-modal').one('hidden.bs.modal', function () {
                        openForNetwork(newNet.id);
                    });
                    $('#ld-network-add-modal').modal('hide');
                } else {
                    openForNetwork(newNet.id);
                }
            }
        } catch (err) {
            handleApiError(err, 'ldNetworks.add');
        } finally {
            if (btn) btn.disabled = false;
            if (confirmBtn) confirmBtn.disabled = false;
        }
    }

    function applyTypeVisibility(type) {
        const sections = document.querySelectorAll('#ld-network-drawer [data-show-for-type]');
        sections.forEach(el => {
            const allowed = el.getAttribute('data-show-for-type').split(',').map(s => s.trim());
            el.style.display = allowed.includes(type) ? '' : 'none';
        });
    }

    async function openForNetwork(netId) {
        const net = data.find(n => String(n.id) === String(netId));
        if (!net) return;
        const drawer = document.getElementById('ld-network-drawer');
        if (!drawer) return;

        drawer.dataset.networkId = net.id;
        const titlePrefix = (i18n && i18n.networks_drawer_title_prefix) || 'SSID Configuration:';
        document.getElementById('ld-network-drawer-title').textContent = titlePrefix + ' ' + (net.ssid || '');
        // Reset drawer tabs to the first one each time the drawer opens
        activateDrawerTab('general');

        const type = net.type || 'password';
        setNetType(type);
        document.getElementById('ld-net-ssid').value = net.ssid || '';
        document.getElementById('ld-net-visible').value = net.visible === false ? '0' : '1';
        document.getElementById('ld-net-radio').value = net.radio || 'all';
        document.getElementById('ld-net-enabled').checked = net.enabled !== false;
        document.getElementById('ld-net-qos').checked = net.qos_policy === 'full';

        document.getElementById('ld-net-password').value = net.password || '';
        document.getElementById('ld-net-security').value = net.security || 'wpa2-psk';
        document.getElementById('ld-net-cipher').value = net.cipher_suites || 'CCMP';

        // Captive portal fields
        const authMethods = (net.auth_methods && net.auth_methods.length)
            ? net.auth_methods
            : [net.auth_method || 'click-through'];
        setActiveAuthMethods(authMethods);
        document.getElementById('ld-net-portal-password').value = net.portal_password || '';
        document.getElementById('ld-net-email-otp').checked = net.email_require_otp !== false;
        document.getElementById('ld-net-social').value = net.social_auth_method || 'facebook';
        document.getElementById('ld-net-redirect-url').value = net.redirect_url || '';
        document.getElementById('ld-net-session-timeout').value = String(net.session_timeout || 60);
        document.getElementById('ld-net-idle-timeout').value = String(net.idle_timeout || 15);
        document.getElementById('ld-net-download-limit').value = net.download_limit || '';
        document.getElementById('ld-net-upload-limit').value = net.upload_limit || '';
        applyAuthMethodVisibility();

        // Réseau (IP config + DHCP + VLAN)
        const rawIpMode = net.ip_mode || 'static';
        const effectiveIpMode = (type === 'captive_portal' && (rawIpMode === 'bridge' || rawIpMode === 'bridge_lan'))
            ? 'static' : rawIpMode;
        document.getElementById('ld-net-bridge-lan-mode').value = net.bridge_lan_dhcp_mode || 'dhcp_client';
        document.getElementById('ld-net-ip-mode').value = effectiveIpMode;
        document.getElementById('ld-net-ip-address').value = net.ip_address || '';
        document.getElementById('ld-net-netmask').value = net.netmask || '255.255.255.0';
        document.getElementById('ld-net-gateway').value = net.gateway || '';
        document.getElementById('ld-net-dns1').value = net.dns1 || '';
        document.getElementById('ld-net-dns2').value = net.dns2 || '';
        document.getElementById('ld-net-dhcp-enabled').checked = net.dhcp_enabled !== false;
        document.getElementById('ld-net-dhcp-start').value = net.dhcp_start || '';
        document.getElementById('ld-net-dhcp-end').value = net.dhcp_end != null ? net.dhcp_end : '';
        document.getElementById('ld-net-vlan-id').value = net.vlan_id || '';
        document.getElementById('ld-net-vlan-tagging').value = net.vlan_tagging || 'disabled';
        applyIpModeVisibility();
        applyVlanGating();

        // MAC filter + DHCP reservations (scratch copies so Cancel reverts)
        drawerMac = (net.mac_filter_list || []).map(normaliseMacEntry);
        drawerReservations = (net.dhcp_reservations || []).filter(r => r && r.mac && r.ip).map(r => ({ mac: r.mac, ip: r.ip }));
        applyMacBypassUi(type);
        renderMacList();
        renderReservations();
        applyReservationsVisibility();

        applyTypeVisibility(type);

        // Working-hours picker (captive only) — always init so switching type later works
        initSchedulePicker(net);

        document.getElementById('ld-network-drawer-save').disabled = false;

        const body = document.getElementById('ld-network-drawer-body');
        if (body) body.scrollTop = 0;

        setEditUrl(net.id);
        if (typeof MwDrawer !== 'undefined') MwDrawer.open('ld-network-drawer');

        // Portal designs are only needed for captive type — lazy fetch
        if (type === 'captive_portal') {
            await ensureCaptiveDesigns();
            populateCaptiveDesignSelect(net.portal_design_id);
        }

    }

    async function save() {
        const drawer = document.getElementById('ld-network-drawer');
        const netId = drawer && drawer.dataset.networkId;
        if (!netId) return;

        const ssid = document.getElementById('ld-net-ssid').value.trim();
        if (!ssid) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_ssid_required || 'SSID is required');
            document.getElementById('ld-net-ssid').focus();
            return;
        }
        if (ssid.length > 32) {
            if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_ssid_too_long || 'SSID must be 32 characters or fewer');
            document.getElementById('ld-net-ssid').focus();
            return;
        }

        const type = getNetType();
        const payload = {
            type,
            ssid,
            visible: document.getElementById('ld-net-visible').value === '1',
            enabled: document.getElementById('ld-net-enabled').checked,
            radio: document.getElementById('ld-net-radio').value,
            qos_policy: document.getElementById('ld-net-qos').checked ? 'full' : 'scavenger',
        };

        if (type === 'password') {
            const pwd = document.getElementById('ld-net-password').value;
            if (!pwd) {
                if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_password_required || 'WiFi password is required.');
                document.getElementById('ld-net-password').focus();
                return;
            }
            if (pwd.length < 8) {
                if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_password_too_short || 'WiFi password must be at least 8 characters.');
                document.getElementById('ld-net-password').focus();
                return;
            }
            payload.password = pwd;
            payload.security = document.getElementById('ld-net-security').value;
            payload.cipher_suites = document.getElementById('ld-net-cipher').value;
        } else if (type === 'captive_portal') {
            const methods = getActiveAuthMethods();
            if (methods.includes('password') && !document.getElementById('ld-net-portal-password').value) {
                if (typeof toastr !== 'undefined') toastr.warning(i18n.networks_portal_password_required || 'Shared portal password is required when the "password" login method is active.');
                document.getElementById('ld-net-portal-password').focus();
                return;
            }
            payload.auth_methods = methods;
            payload.auth_method = methods[0] || 'click-through';
            payload.portal_password = document.getElementById('ld-net-portal-password').value || null;
            payload.email_require_otp = methods.includes('email')
                ? document.getElementById('ld-net-email-otp').checked
                : null;
            payload.social_auth_method = document.getElementById('ld-net-social').value || null;
            payload.session_timeout = parseInt(document.getElementById('ld-net-session-timeout').value, 10) || 60;
            payload.idle_timeout = parseInt(document.getElementById('ld-net-idle-timeout').value, 10) || 15;
            payload.redirect_url = document.getElementById('ld-net-redirect-url').value.trim() || null;
            payload.portal_design_id = document.getElementById('ld-net-portal-design').value || null;
            payload.download_limit = parseInt(document.getElementById('ld-net-download-limit').value, 10) || null;
            payload.upload_limit = parseInt(document.getElementById('ld-net-upload-limit').value, 10) || null;
            payload.working_hours = collectSchedulePayload();
        }

        // Réseau (IP / DHCP / VLAN) — applies to all types
        const ipMode = document.getElementById('ld-net-ip-mode').value;
        const subMode = document.getElementById('ld-net-bridge-lan-mode').value;
        const isBridge = ipMode === 'bridge';
        const isBridgeLan = ipMode === 'bridge_lan';
        const noManualIp = isBridge || (isBridgeLan && subMode === 'dhcp_client');
        const noDhcpServer = isBridge || (isBridgeLan && subMode === 'dhcp_client');

        payload.ip_mode = ipMode;
        payload.bridge_lan_dhcp_mode = isBridgeLan ? subMode : null;
        payload.ip_address = noManualIp ? null : (document.getElementById('ld-net-ip-address').value.trim() || null);
        payload.netmask = noManualIp ? null : (document.getElementById('ld-net-netmask').value.trim() || null);
        payload.gateway = noManualIp ? null : (document.getElementById('ld-net-gateway').value.trim() || null);
        payload.dns1 = noManualIp ? null : (document.getElementById('ld-net-dns1').value.trim() || null);
        payload.dns2 = noManualIp ? null : (document.getElementById('ld-net-dns2').value.trim() || null);
        payload.dhcp_enabled = noDhcpServer ? false : document.getElementById('ld-net-dhcp-enabled').checked;
        payload.dhcp_start = noDhcpServer ? null : (document.getElementById('ld-net-dhcp-start').value.trim() || null);
        payload.dhcp_end = noDhcpServer ? null : (() => {
            const raw = document.getElementById('ld-net-dhcp-end').value.trim();
            if (!raw) return null;
            const n = parseInt(raw, 10);
            return Number.isNaN(n) ? null : n;
        })();
        payload.vlan_id = parseInt(document.getElementById('ld-net-vlan-id').value, 10) || null;
        payload.vlan_tagging = document.getElementById('ld-net-vlan-tagging').value;

        // MAC filter + DHCP reservations
        const coercedMac = coerceMacListForType(drawerMac.map(normaliseMacEntry).filter(e => e.mac), type);
        payload.mac_filter_list = coercedMac;
        payload.mac_filter_mode = deriveMacFilterMode(coercedMac);
        payload.dhcp_reservations = (noDhcpServer || type === 'captive_portal')
            ? null
            : drawerReservations.filter(r => r && r.mac && r.ip);

        const btn = document.getElementById('ld-network-drawer-save');
        btn.disabled = true;
        try {
            const res = await apiFetch(`${API}/locations/${netLocationId()}/networks/${netId}`, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            const idx = data.findIndex(n => String(n.id) === String(netId));
            if (idx >= 0 && res && res.data && res.data.network) {
                data[idx] = res.data.network;
            }
            render();
            if (typeof toastr !== 'undefined') toastr.success(i18n.networks_save_success || 'Network saved');
            if (typeof MwDrawer !== 'undefined') MwDrawer.close('ld-network-drawer');
        } catch (err) {
            handleApiError(err, 'ldNetworks.save');
        } finally {
            btn.disabled = false;
        }
    }

    function getNetType() {
        const checked = document.querySelector('input[name="ld-net-type"]:checked');
        return checked ? checked.value : 'password';
    }

    function setNetType(value) {
        const radio = document.querySelector('input[name="ld-net-type"][value="' + value + '"]');
        if (radio) radio.checked = true;
    }

    function activateDrawerTab(key) {
        const drawer = document.getElementById('ld-network-drawer');
        if (!drawer) return;
        const tabs = drawer.querySelectorAll('[data-drawer-tab]');
        const panels = drawer.querySelectorAll('[data-drawer-panel]');
        tabs.forEach(t => t.classList.toggle('active', t.dataset.drawerTab === key));
        panels.forEach(p => p.classList.toggle('active', p.dataset.drawerPanel === key));
        const body = document.getElementById('ld-network-drawer-body');
        if (body) body.scrollTop = 0;
    }

    function buildWifiQrString(net) {
        const ssid = (net && net.ssid) || '';
        const escape = s => String(s).replace(/([\\;,:"])/g, '\\$1');
        if (net && net.type === 'password' && net.password) {
            return `WIFI:T:WPA;S:${escape(ssid)};P:${escape(net.password)};;`;
        }
        return `WIFI:T:nopass;S:${escape(ssid)};;`;
    }

    function openQrForNetwork(net) {
        if (typeof QRCode === 'undefined') {
            if (typeof toastr !== 'undefined') toastr.error('QR library not loaded');
            return;
        }
        if (!net) return;
        const ssidDisplay = document.getElementById('ld-network-qr-ssid');
        const canvas = document.getElementById('ld-network-qr-canvas');
        if (ssidDisplay) ssidDisplay.textContent = net.ssid || '—';
        if (canvas) {
            canvas.innerHTML = '';
            canvas.dataset.networkId = net.id;
        }
        new QRCode(canvas, {
            text: buildWifiQrString(net),
            width: 240,
            height: 240,
            correctLevel: QRCode.CorrectLevel.M,
        });
        $('#ld-network-qr-modal').one('shown.bs.modal', function () {
            // Raise the modal-backdrop above the drawer (drawer is at z-index 1060,
            // default Bootstrap backdrop sits at 1040). Keep modal inline-styled at 1080.
            $('.modal-backdrop').last().css('z-index', 1070);
        }).modal('show');
    }

    function openQrModal() {
        const drawer = document.getElementById('ld-network-drawer');
        const netId = drawer && drawer.dataset.networkId;
        const net = netId ? data.find(n => String(n.id) === String(netId)) : null;
        if (net) openQrForNetwork(net);
    }

    function downloadQr() {
        const canvasWrap = document.getElementById('ld-network-qr-canvas');
        const canvas = canvasWrap && canvasWrap.querySelector('canvas');
        if (!canvas) return;
        const netId = canvasWrap.dataset.networkId;
        const net = netId ? data.find(n => String(n.id) === String(netId)) : null;
        const slug = ((net && net.ssid) || 'wifi').replace(/[^a-z0-9]/gi, '_').toLowerCase();
        const link = document.createElement('a');
        link.download = `wifi-qr-${slug}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    document.addEventListener('submit', function (e) {
        if (e.target && e.target.id === 'ld-network-drawer-form') {
            e.preventDefault();
        }
        if (e.target && e.target.id === 'ld-network-add-form') {
            e.preventDefault();
            add();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('#ld-networks-add-btn')) {
            e.preventDefault();
            const headerBtn = document.getElementById('ld-networks-add-btn');
            if (!headerBtn || headerBtn.disabled) return;
            openAddNetworkModal();
            return;
        }
        if (e.target.closest('#ld-network-drawer-save')) {
            e.preventDefault();
            save();
            return;
        }
        if (e.target.closest('#ld-network-qr-download')) {
            e.preventDefault();
            downloadQr();
            return;
        }
        const drawerTab = e.target.closest('[data-drawer-tab]');
        if (drawerTab) {
            e.preventDefault();
            activateDrawerTab(drawerTab.dataset.drawerTab);
            return;
        }
        const pwdToggle = e.target.closest('#ld-net-password-toggle, #ld-net-portal-password-toggle');
        if (pwdToggle) {
            e.preventDefault();
            const input = pwdToggle.closest('.input-group')?.querySelector('input');
            if (!input) return;
            const icon = pwdToggle.querySelector('[data-feather]');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            if (icon) {
                icon.setAttribute('data-feather', isText ? 'eye' : 'eye-off');
                if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
            }
            return;
        }
        if (e.target.closest('#ld-net-mac-add')) {
            e.preventDefault();
            addMacEntry();
            return;
        }
        const macRemoveBtn = e.target.closest('.ld-net-mac-remove');
        if (macRemoveBtn) {
            e.preventDefault();
            removeMacEntry(parseInt(macRemoveBtn.dataset.idx, 10));
            return;
        }
        if (e.target.closest('#ld-net-res-add')) {
            e.preventDefault();
            addReservation();
            return;
        }
        const resRemoveBtn = e.target.closest('.ld-net-res-remove');
        if (resRemoveBtn) {
            e.preventDefault();
            removeReservation(parseInt(resRemoveBtn.dataset.idx, 10));
            return;
        }
        const addRangeBtn = e.target.closest('.ld-net-schedule-day-add');
        if (addRangeBtn) {
            e.preventDefault();
            const day = addRangeBtn.closest('.ld-net-schedule-day').dataset.day;
            // Sync DOM → scratch before mutating to preserve user-edited range values
            collectSchedulePayload();
            addRangeToDay(day);
            return;
        }
        const rangeRemoveBtn = e.target.closest('.ld-net-schedule-range-remove');
        if (rangeRemoveBtn) {
            e.preventDefault();
            const rangeEl = rangeRemoveBtn.closest('.ld-net-schedule-range');
            const dayRow = rangeRemoveBtn.closest('.ld-net-schedule-day');
            collectSchedulePayload();
            removeRange(dayRow.dataset.day, parseInt(rangeEl.dataset.idx, 10));
            return;
        }
        const kebabBtn = e.target.closest('.ld-net-kebab-btn');
        if (kebabBtn) {
            e.preventDefault();
            e.stopPropagation();
            const menu = kebabBtn.nextElementSibling;
            const wasOpen = menu && menu.classList.contains('open');
            document.querySelectorAll('.ld-net-menu.open').forEach(m => m.classList.remove('open'));
            if (!wasOpen && menu) menu.classList.add('open');
            return;
        }

        const menuItem = e.target.closest('.ld-net-menu-item');
        if (menuItem) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.ld-net-menu.open').forEach(m => m.classList.remove('open'));
            const action = menuItem.dataset.action;
            const menuRow = menuItem.closest('.ld-network-row');
            const netId = menuRow && menuRow.dataset.networkId;
            if (!netId) return;
            if (action === 'qr') {
                const net = data.find(n => String(n.id) === String(netId));
                if (net) openQrForNetwork(net);
            } else if (action === 'delete') {
                remove(netId);
            }
            return;
        }

        // Close any open kebab menu on outside click
        if (!e.target.closest('.ld-net-kebab-wrap')) {
            document.querySelectorAll('.ld-net-menu.open').forEach(m => m.classList.remove('open'));
        }

        const row = e.target.closest('.ld-network-row');
        if (row && row.dataset.networkId && !e.target.closest('.ld-net-kebab-wrap')) {
            openForNetwork(row.dataset.networkId);
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target && e.target.name === 'ld-net-type') {
            const newType = e.target.value;
            applyTypeVisibility(newType);
            applyMacBypassUi(newType);
            applyReservationsVisibility();
            // If switching to captive and designs aren't loaded, fetch now
            if (newType === 'captive_portal') {
                ensureCaptiveDesigns().then(() => populateCaptiveDesignSelect(null));
            }
        }
        if (e.target && e.target.classList.contains('ld-net-auth-method')) {
            applyAuthMethodVisibility();
        }
        if (e.target && (e.target.id === 'ld-net-ip-mode' || e.target.id === 'ld-net-bridge-lan-mode')) {
            applyIpModeVisibility();
            applyReservationsVisibility();
        }
        if (e.target && e.target.id === 'ld-net-dhcp-enabled') {
            applyReservationsVisibility();
        }
        if (e.target && e.target.classList && e.target.classList.contains('ld-net-schedule-mode')) {
            applyScheduleMode(e.target.value);
        }
        if (e.target && e.target.classList && e.target.classList.contains('ld-net-schedule-day-enabled')) {
            const dayRow = e.target.closest('.ld-net-schedule-day');
            if (dayRow) {
                // Sync current DOM edits before re-rendering
                collectSchedulePayload();
                toggleDay(dayRow.dataset.day, e.target.checked);
            }
        }
    });

    function setEditUrl(netId) {
        const url = new URL(window.location.href);
        if (url.searchParams.get('edit') === String(netId)) return;
        url.searchParams.set('edit', String(netId));
        history.replaceState(null, '', url);
    }

    function clearEditUrl() {
        const url = new URL(window.location.href);
        if (!url.searchParams.has('edit')) return;
        url.searchParams.delete('edit');
        history.replaceState(null, '', url);
    }

    // Clear ?edit when the drawer closes by any path (X, Cancel, backdrop, Escape, save, delete)
    (function watchDrawerClose() {
        const drawer = document.getElementById('ld-network-drawer');
        if (!drawer) return;
        const mo = new MutationObserver(() => {
            if (!drawer.classList.contains('is-open')) clearEditUrl();
        });
        mo.observe(drawer, { attributes: true, attributeFilter: ['class'] });
    })();

    // On networks data load, auto-open drawer if ?edit=<id> is in the URL
    function restoreEditFromUrl() {
        const editId = new URL(window.location.href).searchParams.get('edit');
        if (!editId) return;
        if (!data.some(n => String(n.id) === String(editId))) {
            clearEditUrl();
            return;
        }
        openForNetwork(editId);
    }

    function setVlanEnabled(enabled) {
        vlanEnabled = !!enabled;
        // If drawer is open, update the gated fields immediately
        if (document.getElementById('ld-network-drawer')?.classList.contains('is-open')) {
            applyVlanGating();
        }
    }

    return {
        load,
        render,
        openForNetwork,
        save,
        isLoaded: () => loaded,
        restoreEditFromUrl,
        setVlanEnabled,
    };
})();
