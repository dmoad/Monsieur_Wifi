// Devices management JavaScript
const PAGE_LOCALE = document.documentElement.lang || 'en';

const TRANSLATIONS = {
    en: {
        loading: 'Loading...',
        noDevices: 'No devices found',
        noDevicesDesc: 'No devices match your search criteria',
        serial: 'Serial',
        mac: 'MAC Address',
        model: 'Model',
        owner: 'Owner',
        location: 'Location',
        status: 'Status',
        actions: 'Actions',
        unassigned: 'Unassigned',
        assigned: 'Assigned',
        changeOwner: 'Change Owner',
        viewLocation: 'View Location',
        errorLoading: 'Error loading devices',
        errorUpdating: 'Error updating device owner',
        ownerUpdated: 'Device owner updated successfully',
        selectOwner: 'Select owner...',
        ownerRequired: 'Please select a new owner',
        showing: 'Showing',
        of: 'of',
        devices: 'devices',
        previous: 'Previous',
        next: 'Next',
        page: 'Page',
        noOwner: 'No Owner',
        deviceInfo: 'Device Information',
    },
    fr: {
        loading: 'Chargement...',
        noDevices: 'Aucun appareil trouvé',
        noDevicesDesc: 'Aucun appareil ne correspond à vos critères de recherche',
        serial: 'Série',
        mac: 'Adresse MAC',
        model: 'Modèle',
        owner: 'Propriétaire',
        location: 'Emplacement',
        status: 'Statut',
        actions: 'Actions',
        unassigned: 'Non Assigné',
        assigned: 'Assigné',
        changeOwner: 'Changer le Propriétaire',
        viewLocation: 'Voir l\'Emplacement',
        errorLoading: 'Erreur lors du chargement des appareils',
        errorUpdating: 'Erreur lors de la mise à jour du propriétaire de l\'appareil',
        ownerUpdated: 'Propriétaire de l\'appareil mis à jour avec succès',
        selectOwner: 'Sélectionner un propriétaire...',
        ownerRequired: 'Veuillez sélectionner un nouveau propriétaire',
        showing: 'Affichage de',
        of: 'sur',
        devices: 'appareils',
        previous: 'Précédent',
        next: 'Suivant',
        page: 'Page',
        noOwner: 'Aucun Propriétaire',
        deviceInfo: 'Informations sur l\'Appareil',
    }
};

const T = TRANSLATIONS[PAGE_LOCALE];
let currentPage = 1;
let totalPages = 1;
let allUsers = [];


document.addEventListener('DOMContentLoaded', function() {
    if (typeof UserManager === 'undefined') {
        console.error('UserManager not loaded');
        window.location.href = '/';
        return;
    }

    const token = UserManager.getToken();
    const user = UserManager.getUser();

    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    loadUsers();
    loadDevices();

    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') loadDevices();
    });

    document.addEventListener('click', function(e) {
        const actionBtn = e.target.closest('.dc-action-btn[data-action]');
        if (actionBtn) {
            const action = actionBtn.dataset.action;
            if (action === 'change-owner') {
                e.preventDefault();
                showChangeOwnerModal(parseInt(actionBtn.dataset.deviceId));
            }
            // view-location: anchor's href navigates naturally
        }
    });
});

async function loadUsers() {
    if (!UserManager.isAdminOrAbove()) return;
    
    const token = UserManager.getToken();
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            allUsers = data.users || [];
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

async function loadDevices(page = 1) {
    const token = UserManager.getToken();
    const search = document.getElementById('search').value;
    const locationStatus = document.getElementById('location-status-filter').value;
    const loadingEl = document.getElementById('devices-loading');
    const listEl = document.getElementById('devices-list');
    
    loadingEl.style.display = 'block';
    listEl.innerHTML = '';
    
    try {
        let url = `${APP_CONFIG.API.BASE_URL}/v1/devices?page=${page}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (locationStatus) url += `&location_status=${locationStatus}`;
        
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load devices');
        }
        
        const data = await response.json();
        currentPage = data.devices.current_page;
        totalPages = data.devices.last_page;
        
        displayDevices(data.devices.data || []);
        renderPagination(data.devices);
    } catch (error) {
        console.error('Error loading devices:', error);
        toastr.error(T.errorLoading);
        listEl.innerHTML = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-danger">${T.errorLoading}</p>
                </div>
            </div>
        `;
    } finally {
        loadingEl.style.display = 'none';
    }
}

function displayDevices(devices) {
    const container = document.getElementById('devices-list');

    if (!devices || devices.length === 0) {
        container.innerHTML = `
            <div class="card">
                <div class="card-body empty-state">
                    <i data-feather="hard-drive" style="width:48px;height:48px;color:var(--mw-text-muted)"></i>
                    <h5 class="mt-3" style="color:var(--mw-text-primary)">${T.noDevices}</h5>
                    <p style="color:var(--mw-text-muted);font-size:13px">${T.noDevicesDesc}</p>
                </div>
            </div>
        `;
        feather.replace();
        return;
    }

    const canChangeOwner = UserManager.isAdminOrAbove();

    const rows = devices.map(device => {
        const hasLocation = !!device.location;
        const locationUrl = hasLocation ? `/${PAGE_LOCALE}/locations/${device.location.id}` : null;
        const serial = device.serial_number || '—';
        const mac = device.mac_address || '—';

        const ownerBadge = device.owner
            ? `<span class="dc-badge dc-badge-owner">${device.owner.name}</span>`
            : `<span class="dc-badge dc-badge-no-owner">${T.noOwner}</span>`;

        const locationBadge = hasLocation
            ? `<span class="dc-badge dc-badge-assigned">${device.location.name}</span>`
            : `<span class="dc-badge dc-badge-unassigned">${T.unassigned}</span>`;

        const actionBtns = [];
        if (hasLocation) {
            actionBtns.push(`<a class="dc-action-btn" href="${locationUrl}" data-action="view-location" data-toggle="tooltip" title="${T.viewLocation}" aria-label="${T.viewLocation}"><i data-feather="map-pin"></i></a>`);
        }
        if (canChangeOwner) {
            actionBtns.push(`<button type="button" class="dc-action-btn" data-action="change-owner" data-device-id="${device.id}" data-toggle="tooltip" title="${T.changeOwner}" aria-label="${T.changeOwner}"><i data-feather="user"></i></button>`);
        }

        const actionsCell = actionBtns.length > 0
            ? `<div class="dc-row-actions">${actionBtns.join('')}</div>`
            : '';

        return `
            <tr>
                <td><span class="dc-serial">${serial}</span></td>
                <td><span class="dc-mac">${mac}</span></td>
                <td>${ownerBadge}</td>
                <td>${locationBadge}</td>
                <td class="dc-col-actions">${actionsCell}</td>
            </tr>
        `;
    }).join('');

    container.innerHTML = `
        <div class="card dc-table-wrap">
            <table class="dc-table">
                <thead>
                    <tr>
                        <th>${T.serial}</th>
                        <th>${T.mac}</th>
                        <th>${T.owner}</th>
                        <th>${T.location}</th>
                        <th>${T.actions}</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;
    feather.replace();
    $(container).find('[data-toggle="tooltip"]').tooltip({ container: 'body' });
}

function renderPagination(paginationData) {
    const container = document.getElementById('pagination-container');
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    const html = `
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                ${T.showing} ${paginationData.from || 0}-${paginationData.to || 0} ${T.of} ${paginationData.total} ${T.devices}
            </div>
            <div>
                <button class="btn btn-sm btn-outline-secondary" 
                        onclick="loadDevices(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}>
                    ${T.previous}
                </button>
                <span class="mx-2">${T.page} ${currentPage} / ${totalPages}</span>
                <button class="btn btn-sm btn-outline-secondary" 
                        onclick="loadDevices(${currentPage + 1})" 
                        ${currentPage === totalPages ? 'disabled' : ''}>
                    ${T.next}
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

async function showChangeOwnerModal(deviceId) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/devices/${deviceId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load device');
        
        const data = await response.json();
        const device = data.device;
        
        document.getElementById('device-id').value = device.id;
        
        // Populate owner dropdown
        const ownerSelect = document.getElementById('new-owner');
        ownerSelect.innerHTML = `<option value="">${T.selectOwner}</option>` +
            allUsers.map(user => `
                <option value="${user.id}" ${device.owner_id === user.id ? 'selected' : ''}>
                    ${user.name} (${user.email})
                </option>
            `).join('');
        
        // Show device info
        const deviceInfoHtml = `
            <strong>${T.deviceInfo}:</strong><br>
            ${T.serial}: ${device.serial_number}<br>
            ${T.model}: ${device.model}<br>
            ${T.mac}: ${device.mac_address}
        `;
        document.getElementById('device-info').innerHTML = deviceInfoHtml;
        
        $('#change-owner-modal').modal('show');
    } catch (error) {
        console.error('Error loading device:', error);
        toastr.error(T.errorLoading);
    }
}

async function updateDeviceOwner() {
    const deviceId = document.getElementById('device-id').value;
    const newOwnerId = document.getElementById('new-owner').value;
    
    if (!newOwnerId) {
        toastr.error(T.ownerRequired);
        return;
    }
    
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/devices/${deviceId}/owner`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ owner_id: newOwnerId })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Failed to update owner');
        }
        
        toastr.success(T.ownerUpdated);
        $('#change-owner-modal').modal('hide');
        loadDevices(currentPage);
    } catch (error) {
        console.error('Error updating owner:', error);
        toastr.error(T.errorUpdating);
    }
}
