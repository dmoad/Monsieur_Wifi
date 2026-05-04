/**
 * IP Log — paginated flow_sessions for the location's assigned device.
 */
'use strict';

const PAGE_LOCALE = typeof window.IP_LOG_PAGE_LOCALE !== 'undefined' ? window.IP_LOG_PAGE_LOCALE : 'en';
const API = window.APP_CONFIG_V5?.apiBase || '/api';
const locationId = window.IP_LOG_LOCATION_ID;

let flowPage = 1;
let flowLastPage = 1;
let flowTotal = 0;
let flowPerPage = 10;
let flowSearchTimer = null;

/** @param {string} key */
function ipLogT(key) {
    const ld = window.APP_I18N?.location_details;
    return (ld && ld[key]) ? ld[key] : key;
}

async function ipLogFetchJson(url) {
    const res = await fetch(url, {
        headers: {
            Authorization: 'Bearer ' + UserManager.getToken(),
            Accept: 'application/json',
        },
    });
    const body = await res.json().catch(() => ({}));
    if (res.status === 401) {
        window.location.href = '/';
        throw new Error('Unauthorized');
    }
    if (!res.ok || body.success === false) {
        throw new Error(body.message || `HTTP ${res.status}`);
    }
    return body;
}

async function loadIpLogLocationName() {
    try {
        const res = await ipLogFetchJson(`${API}/locations/${locationId}`);
        if (res.success && res.data?.name) {
            const el = document.getElementById('ip-log-location-name');
            if (el) el.textContent = res.data.name;
        }
    } catch (e) {
        console.error(e);
        const el = document.getElementById('ip-log-location-name');
        if (el) el.textContent = '#' + locationId;
    }
}

function escHtml(s) {
    if (s == null || s === '') return '—';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function fmtDt(iso) {
    if (!iso) return '—';
    try {
        return new Date(iso).toLocaleString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US');
    } catch {
        return escHtml(iso);
    }
}

/** Uppercase MAC with hyphen-separated octets (e.g. AA-BB-CC-DD-EE-FF). */
function fmtMac(mac) {
    if (mac == null || mac === '') return '—';
    const cleaned = String(mac).replace(/[^0-9a-fA-F]/g, '').toUpperCase();
    if (cleaned.length !== 12) {
        return escHtml(String(mac).trim().toUpperCase());
    }
    const octets = cleaned.match(/.{2}/g);
    return escHtml(octets ? octets.join('-') : cleaned);
}

/**
 * Normalize MAC for API search: DB stores lowercase colon-separated octets (see ProcessFlowBatch).
 * Strips separators, keeps hex only, lowercases, rejoins pairs with ':'.
 */
function normalizeMacForSearch(raw) {
    const trimmed = String(raw).trim();
    if (!trimmed) return '';
    const hex = trimmed.replace(/[^0-9a-fA-F]/g, '').toLowerCase();
    if (!hex) return trimmed.toLowerCase();
    const parts = [];
    for (let i = 0; i < hex.length; i += 2) {
        parts.push(hex.slice(i, i + 2));
    }
    return parts.join(':');
}

function renderFlowRows(rows) {
    const tbody = document.getElementById('ip-log-tbody');
    if (!tbody) return;

    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4"><small>${escHtml(ipLogT('ip_log_empty'))}</small></td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(r => `<tr>
        <td><code style="font-size:0.75rem;">${fmtMac(r.mac)}</code></td>
        <td>${escHtml(r.src_ip)}</td>
        <td>${escHtml(r.dst_ip)}</td>
        <td>${fmtDt(r.first_at)}</td>
        <td>${fmtDt(r.last_at)}</td>
        <td>${Number(r.hits) || 0}</td>
    </tr>`).join('');
}

function renderFlowPagination(currentPage, lastPage, total, perPage) {
    const paginationEl = document.getElementById('ip-log-pagination');
    const countEl = document.getElementById('ip-log-count-range');
    const pageInfoEl = document.getElementById('ip-log-page-info');
    const prevBtn = document.getElementById('ip-log-prev');
    const nextBtn = document.getElementById('ip-log-next');

    if (!paginationEl) return;

    const pp = typeof perPage === 'number' && perPage > 0 ? perPage : flowPerPage;

    if (!total || total <= 0) {
        paginationEl.style.display = 'none';
        return;
    }

    paginationEl.style.display = 'flex';
    const start = (currentPage - 1) * pp + 1;
    const end = Math.min(currentPage * pp, total);
    if (countEl) countEl.textContent = `${start}–${end} / ${total}`;
    if (pageInfoEl) pageInfoEl.textContent = `${currentPage} / ${lastPage}`;
    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= lastPage;
}

async function loadFlowSessions(page) {
    flowPage = page;

    const loadingEl = document.getElementById('ip-log-loading');
    const noDeviceEl = document.getElementById('ip-log-no-device');
    if (loadingEl) loadingEl.style.display = 'block';
    if (noDeviceEl) noDeviceEl.style.display = 'none';

    const searchFieldEl = document.getElementById('ip-log-search-field');
    const searchInputEl = document.getElementById('ip-log-search-input');
    const searchField = searchFieldEl?.value || 'mac';
    let searchVal = (searchInputEl?.value || '').trim();
    if (searchField === 'mac' && searchVal) {
        searchVal = normalizeMacForSearch(searchVal);
    }

    try {
        let url = `${API}/locations/${locationId}/flow-sessions?page=${page}&per_page=${flowPerPage}`
            + `&search_field=${encodeURIComponent(searchField)}`;
        if (searchVal) url += `&search=${encodeURIComponent(searchVal)}`;

        const res = await ipLogFetchJson(url);
        const payload = res.data || {};
        const {
            data: rows,
            current_page: currentPage,
            last_page: lastPage,
            total,
            per_page: perPage,
            message,
        } = payload;

        if (message && typeof message === 'string') {
            if (noDeviceEl) noDeviceEl.style.display = 'block';
        }

        flowTotal = total || 0;
        flowLastPage = lastPage || 1;
        if (typeof perPage === 'number') flowPerPage = perPage;

        renderFlowRows(rows || []);
        renderFlowPagination(currentPage || 1, flowLastPage, flowTotal, flowPerPage);
    } catch (err) {
        console.error(err);
        const tbody = document.getElementById('ip-log-tbody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4"><small>${escHtml(ipLogT('ip_log_error'))}</small></td></tr>`;
        }
        const pagEl = document.getElementById('ip-log-pagination');
        if (pagEl) pagEl.style.display = 'none';
        if (typeof toastr !== 'undefined') toastr.error(ipLogT('ip_log_error'));
    } finally {
        if (loadingEl) loadingEl.style.display = 'none';
        if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
    }
}

$(document).ready(function () {
    const token = UserManager.getToken();
    const user = UserManager.getUser();
    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    const perSel = document.getElementById('ip-log-per-page');
    if (perSel?.value) flowPerPage = parseInt(perSel.value, 10) || 10;

    loadIpLogLocationName();
    loadFlowSessions(1);

    $('#ip-log-search-input').on('input', function () {
        clearTimeout(flowSearchTimer);
        flowSearchTimer = setTimeout(() => loadFlowSessions(1), 400);
    });

    $('#ip-log-search-field').on('change', () => loadFlowSessions(1));

    $('#ip-log-per-page').on('change', function () {
        flowPerPage = parseInt($(this).val(), 10) || 10;
        loadFlowSessions(1);
    });

    $('#ip-log-refresh').on('click', () => loadFlowSessions(1));

    $('#ip-log-prev').on('click', () => {
        if (flowPage > 1) loadFlowSessions(flowPage - 1);
    });

    $('#ip-log-next').on('click', () => {
        if (flowPage < flowLastPage) loadFlowSessions(flowPage + 1);
    });
});
