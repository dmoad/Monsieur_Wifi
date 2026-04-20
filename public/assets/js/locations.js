// Locations list page — card layout with search, status filter, pagination
const PAGE_LOCALE = document.documentElement.lang || 'en';
const T = window.LOCATIONS_T || {};

let allLocations = [];
let networkTotals = { total_data_gb: 0 };
let filteredLocations = [];
let currentPage = 1;
let itemsPerPage = 25;

document.addEventListener('DOMContentLoaded', function () {
    if (typeof UserManager === 'undefined') {
        window.location.href = '/';
        return;
    }

    const token = UserManager.getToken();
    const user = UserManager.getUser();
    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    loadLocations();

    document.getElementById('search-locations').addEventListener('input', applyFilters);
    document.getElementById('status-filter').addEventListener('change', applyFilters);
    document.getElementById('items-per-page').addEventListener('change', changeItemsPerPage);

    $('#add-location-modal').on('show.bs.modal', function () {
        if (UserManager.isAdminOrAbove()) {
            loadUsers();
        } else {
            loadAvailableDevices();
        }
    });

    $('#owner-select').on('change', function () {
        const ownerId = $(this).val();
        if (ownerId) {
            loadAvailableDevices(ownerId);
        } else {
            $('#device-select')
                .html(`<option value="">${T.select_owner_above_first}</option>`)
                .prop('disabled', true);
        }
    });

    document.getElementById('add-location-btn').addEventListener('click', handleAddLocation);

    // Close kebab menus on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.lc-kebab-wrap')) {
            closeAllLocationMenus();
        }
    });
});

async function loadLocations() {
    const token = UserManager.getToken();
    const loadingEl = document.getElementById('locations-loading');
    const listEl = document.getElementById('locations-list');

    loadingEl.style.display = 'block';
    listEl.innerHTML = '';

    try {
        const response = await $.ajax({
            url: APP_CONFIG.API.BASE_URL + '/locations',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token }
        });

        allLocations = response.locations || [];
        networkTotals = response.network_totals || { total_data_gb: 0 };
        currentPage = 1;
        renderSummary();
        applyFilters();
    } catch (xhr) {
        console.error('Error fetching locations:', xhr);
        if (xhr && xhr.status === 401) {
            window.location.href = '/';
            return;
        }
        listEl.innerHTML = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-danger mb-0">${T.error_loading || 'Error loading locations'}</p>
                </div>
            </div>
        `;
    } finally {
        loadingEl.style.display = 'none';
    }
}

function renderSummary() {
    const total = allLocations.length;
    const online = allLocations.filter(loc => loc.online_status === 'online').length;
    const users = allLocations.reduce((sum, loc) => sum + (Number(loc.users) || 0), 0);
    const dataGb = Number(networkTotals.total_data_gb) || 0;
    const dataFormatted = dataGb > 1024
        ? (dataGb / 1024).toFixed(1) + ' ' + (T.unit_tb || 'TB')
        : dataGb.toFixed(1) + ' ' + (T.unit_gb || 'GB');

    document.getElementById('total-locations').textContent = total;
    document.getElementById('online-locations').textContent = online;
    document.getElementById('total-users').textContent = users;
    document.getElementById('total-data').textContent = dataFormatted;
}

function applyFilters() {
    const query = (document.getElementById('search-locations').value || '').trim().toLowerCase();
    const status = document.getElementById('status-filter').value;

    filteredLocations = allLocations.filter(loc => {
        const matchesQuery = !query
            || (loc.name || '').toLowerCase().includes(query)
            || (loc.address || '').toLowerCase().includes(query);
        const matchesStatus = !status || loc.online_status === status;
        return matchesQuery && matchesStatus;
    });

    currentPage = 1;
    displayLocations();
}

function displayLocations() {
    const listEl = document.getElementById('locations-list');
    const paginationEl = document.getElementById('pagination-container');

    if (filteredLocations.length === 0) {
        listEl.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="lc-empty">
                        <div class="lc-empty-icon">
                            <i data-feather="map-pin" style="width: 32px; height: 32px;"></i>
                        </div>
                        <h4>${T.empty_title || 'No locations found'}</h4>
                        <p class="text-muted">${T.empty_desc || 'Add your first location to get started.'}</p>
                    </div>
                </div>
            </div>
        `;
        paginationEl.innerHTML = '';
        if (typeof feather !== 'undefined') feather.replace();
        return;
    }

    const totalItems = filteredLocations.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
    const pageItems = filteredLocations.slice(startIndex, endIndex);

    listEl.innerHTML = pageItems.map(renderLocationCard).join('');

    if (totalPages > 1) {
        const showingText = PAGE_LOCALE === 'fr'
            ? `Affichage ${startIndex + 1}-${endIndex} sur ${totalItems}`
            : `Showing ${startIndex + 1}-${endIndex} of ${totalItems}`;

        let paginationButtons = '';
        paginationButtons += `
            <button class="btn btn-sm btn-outline-primary"
                    onclick="goToPage(${currentPage - 1})"
                    ${currentPage === 1 ? 'disabled' : ''}>
                <i data-feather="chevron-left"></i>
            </button>
        `;

        const maxPageButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPageButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);
        if (endPage - startPage < maxPageButtons - 1) {
            startPage = Math.max(1, endPage - maxPageButtons + 1);
        }

        if (startPage > 1) {
            paginationButtons += `<button class="btn btn-sm btn-outline-primary" onclick="goToPage(1)">1</button>`;
            if (startPage > 2) paginationButtons += `<span class="mx-2">...</span>`;
        }
        for (let i = startPage; i <= endPage; i++) {
            paginationButtons += `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}"
                        onclick="goToPage(${i})">${i}</button>
            `;
        }
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationButtons += `<span class="mx-2">...</span>`;
            paginationButtons += `<button class="btn btn-sm btn-outline-primary" onclick="goToPage(${totalPages})">${totalPages}</button>`;
        }

        paginationButtons += `
            <button class="btn btn-sm btn-outline-primary"
                    onclick="goToPage(${currentPage + 1})"
                    ${currentPage === totalPages ? 'disabled' : ''}>
                <i data-feather="chevron-right"></i>
            </button>
        `;

        paginationEl.innerHTML = `
            <div class="pagination-controls">
                <div class="pagination-info">${showingText}</div>
                <div class="pagination-buttons">${paginationButtons}</div>
            </div>
        `;
    } else {
        paginationEl.innerHTML = '';
    }

    if (typeof feather !== 'undefined') feather.replace();
}

function renderLocationCard(location) {
    const locale = PAGE_LOCALE === 'fr' ? 'fr' : 'en';
    const isOnline = location.online_status === 'online';
    const statusClass = isOnline ? 'lc-status-online' : 'lc-status-offline';
    const statusLabel = isOnline ? (T.status_online || 'Online') : (T.status_offline || 'Offline');

    const addressHtml = location.address
        ? `<span class="lc-meta-item" title="${escapeHtml(location.address)}"><i data-feather="map-pin"></i> ${escapeHtml(location.address)}</span>`
        : '';

    return `
        <div class="location-card card card-clickable" onclick="window.location.href='/${locale}/locations/${location.id}'">
            <div class="lc-head">
                <div class="lc-info">
                    <div class="lc-name">${escapeHtml(location.name || '')}</div>
                    <div class="lc-meta">${addressHtml}</div>
                </div>
                <div class="lc-head-right" onclick="event.stopPropagation()">
                    <span class="lc-status ${statusClass}">${statusLabel}</span>
                    <div class="lc-kebab-wrap">
                        <button class="lc-kebab-btn" onclick="toggleLocationMenu(event, ${location.id})" title="${T.actions || 'Actions'}">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                        <div class="lc-menu" id="lc-menu-${location.id}">
                            <button class="lc-menu-item" onclick="window.location.href='/${locale}/locations/${location.id}'">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                ${T.action_view || 'View'}
                            </button>
                            <div class="lc-menu-divider"></div>
                            <button class="lc-menu-item lc-menu-danger" onclick="deleteLocation(${location.id}, ${JSON.stringify(location.name || '').replace(/"/g, '&quot;')}); closeAllLocationMenus()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                ${T.action_delete || 'Delete'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lc-stats">
                <div class="lc-stat">
                    <div class="lc-stat-val lc-p"><i data-feather="users"></i> ${Number(location.users) || 0}</div>
                    <div class="lc-stat-lbl">${T.col_users || 'Users'}</div>
                </div>
                <div class="lc-stat-divider"></div>
                <div class="lc-stat">
                    <div class="lc-stat-val lc-i"><i data-feather="download"></i> ${escapeHtml(location.data_usage || '—')}</div>
                    <div class="lc-stat-lbl">${T.col_data_usage || 'Data Usage'}</div>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function toggleLocationMenu(event, locationId) {
    event.stopPropagation();
    const menu = document.getElementById(`lc-menu-${locationId}`);
    const isOpen = menu.classList.contains('open');
    closeAllLocationMenus();
    if (!isOpen) menu.classList.add('open');
}

function closeAllLocationMenus() {
    document.querySelectorAll('.lc-menu.open').forEach(m => m.classList.remove('open'));
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredLocations.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        displayLocations();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function changeItemsPerPage() {
    const select = document.getElementById('items-per-page');
    itemsPerPage = parseInt(select.value, 10) || 25;
    currentPage = 1;
    displayLocations();
}

async function deleteLocation(locationId, locationName) {
    const confirmMsg = (T.confirm_delete || 'Delete location "{name}"? This cannot be undone.')
        .replace('{name}', locationName || '');
    if (!window.confirm(confirmMsg)) return;

    const token = UserManager.getToken();
    try {
        await $.ajax({
            url: APP_CONFIG.API.BASE_URL + '/locations/' + locationId,
            type: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token }
        });
        if (typeof toastr !== 'undefined') {
            toastr.success(T.location_deleted || 'Location deleted');
        }
        loadLocations();
    } catch (xhr) {
        console.error('Error deleting location:', xhr);
        if (typeof toastr !== 'undefined') {
            toastr.error(T.error_deleting || 'Error deleting location');
        }
    }
}

function handleAddLocation(e) {
    e.preventDefault();
    const user = UserManager.getUser();
    const token = UserManager.getToken();
    const btn = document.getElementById('add-location-btn');

    btn.innerHTML = `<i data-feather="loader" class="mr-2"></i>${T.adding_location || 'Adding...'}`;
    btn.disabled = true;
    if (typeof feather !== 'undefined') feather.replace();

    $('.form-error').remove();
    $('.is-invalid').removeClass('is-invalid');

    const locationData = {
        name: $('#location-name').val(),
        address: $('#location-address').val(),
        device_id: $('#device-select').val(),
        description: $('#location-notes').val()
    };

    if (UserManager.isAdminOrAbove() && $('#owner-select').val()) {
        locationData.owner_id = $('#owner-select').val();
    } else {
        locationData.owner_id = user.id;
    }

    let hasErrors = false;
    if (!locationData.name) {
        showFieldError('location-name', T.location_name_required || 'Name required');
        hasErrors = true;
    }
    if (!locationData.device_id) {
        showFieldError('device-select', T.device_required || 'Device required');
        hasErrors = true;
    }

    if (hasErrors) {
        btn.innerHTML = T.add_location || 'Add Location';
        btn.disabled = false;
        return;
    }

    $.ajax({
        url: APP_CONFIG.API.BASE_URL + '/locations',
        type: 'POST',
        data: locationData,
        headers: { 'Authorization': 'Bearer ' + token },
        success: function (response) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            let successMessage = T.location_created || 'Location created';
            if (response.firmware) {
                successMessage += `<br><small>${T.assigned_firmware_prefix || 'Assigned firmware:'} ${escapeHtml(response.firmware.name)}</small>`;
            }
            btn.innerHTML = successMessage;

            setTimeout(function () {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.innerHTML = T.add_location || 'Add Location';
                btn.disabled = false;
                $('#add-location-modal').modal('hide');
                document.getElementById('add-location-form').reset();
                $('.form-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                loadLocations();
            }, 2500);
        },
        error: function (xhr) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-danger');
            btn.innerHTML = T.error_creating_location || 'Error creating location';
            setTimeout(function () {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-primary');
                btn.innerHTML = T.add_location || 'Add Location';
                btn.disabled = false;
            }, 3000);
            console.error('Error creating location:', xhr);
        }
    });
}

function showFieldError(fieldId, message) {
    $(`#${fieldId}`)
        .addClass('is-invalid')
        .after(`<div class="invalid-feedback form-error">${message}</div>`);
}

async function loadUsers() {
    if (!UserManager.isAdminOrAbove()) return;
    const token = UserManager.getToken();
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!response.ok) throw new Error('Failed to load users');
        const data = await response.json();
        const select = $('#owner-select');

        let options = `<option value="">${T.select_owner_first_option || 'Select owner...'}</option>`;
        (data.users || []).forEach(u => {
            options += `<option value="${u.id}">${escapeHtml(u.name)} (${escapeHtml(u.email)})</option>`;
        });
        select.html(options);
        $('#owner-select-group').show();

        $('#device-select')
            .html(`<option value="">${T.select_owner_above_first || 'Select owner first'}</option>`)
            .prop('disabled', true);
        $('#device-select-hint').text(T.select_owner_first_hint || '');
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

async function loadAvailableDevices(ownerId = null) {
    const token = UserManager.getToken();
    $('#device-select')
        .html(`<option value="">${T.loading_devices || 'Loading devices...'}</option>`)
        .prop('disabled', true);

    let url = `${APP_CONFIG.API.BASE_URL}/v1/devices/available-for-location`;
    if (ownerId) url += `?owner_id=${ownerId}`;

    try {
        const response = await fetch(url, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!response.ok) throw new Error('Failed to load devices');
        const data = await response.json();
        const select = $('#device-select');

        let options = `<option value="">${T.select_a_device || 'Select a device...'}</option>`;

        if (data.unassigned && data.unassigned.length > 0) {
            options += `<optgroup label="${T.available_devices_group || 'Available Devices'}">`;
            data.unassigned.forEach(d => {
                options += `<option value="${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.available_suffix || 'Available'}</option>`;
            });
            options += '</optgroup>';
        }

        if (data.assigned && data.assigned.length > 0) {
            options += `<optgroup label="${T.devices_assigned_elsewhere_group || 'Assigned to Other Locations'}">`;
            data.assigned.forEach(d => {
                const locationName = d.location ? d.location.name : (T.unknown_location || 'Unknown');
                options += `<option value="${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.assigned_to_prefix || 'Assigned to:'} ${escapeHtml(locationName)}</option>`;
            });
            options += '</optgroup>';
        }

        if ((!data.unassigned || data.unassigned.length === 0) && (!data.assigned || data.assigned.length === 0)) {
            options = `<option value="">${T.no_devices_found || 'No devices found'}</option>`;
        }

        select.html(options).prop('disabled', false);
        $('#device-select-hint').text(T.select_device_help || '');
    } catch (error) {
        console.error('Error loading devices:', error);
        $('#device-select')
            .html(`<option value="">${T.error_loading_devices || 'Error loading devices'}</option>`)
            .prop('disabled', false);
    }
}
