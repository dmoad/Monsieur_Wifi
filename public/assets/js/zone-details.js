// Zone Details JavaScript
const PAGE_LOCALE = document.documentElement.lang || 'en';

const ROLE_BADGE = {
    superadmin: { label: 'Super', bg: '#ea5455' },
    admin:      { label: 'Admin', bg: '#7367f0' },
    user:       { label: 'User',  bg: '#28c76f' },
};

function roleBadgeHtml(role) {
    const meta = ROLE_BADGE[role] || { label: role, bg: '#6e6b7b' };
    return `<span style="font-size:0.65rem;font-weight:600;background:${meta.bg};color:#fff;border-radius:3px;padding:1px 5px;margin-left:4px;vertical-align:middle;">${meta.label}</span>`;
}

let allUsersForZoneDetails = [];
const TRANSLATIONS = {
    en: {
        loading: 'Loading...',
        noLocations: 'No locations in this zone',
        noLocationsDesc: 'Add locations from the section below',
        noAvailableLocations: 'No available locations',
        noAvailableDesc: 'All locations from this owner are already in zones',
        primary: 'Primary',
        setPrimary: 'Set as Primary',
        removeLocation: 'Remove from Zone',
        addLocation: 'Add to Zone',
        confirmRemove: 'Are you sure you want to remove this location from the zone?',
        confirmSetPrimary: 'Set this location as the primary for the zone? Settings from this location will be used by all other locations in the zone.',
        selectNewPrimary: 'Select New Primary Location',
        selectNewPrimaryDesc: 'The location you are removing is currently set as primary. Please select a new primary location from the remaining locations:',
        confirmSelection: 'Confirm Selection',
        cancel: 'Cancel',
        locationAdded: 'Location added to zone',
        locationRemoved: 'Location removed from zone',
        primarySet: 'Primary location set',
        zoneUpdated: 'Zone updated successfully',
        errorLoading: 'Error loading zone details',
        errorUpdating: 'Error updating zone',
        errorAddingLocation: 'Error adding location',
        errorRemovingLocation: 'Error removing location',
        errorSettingPrimary: 'Error setting primary location',
        selectNewPrimaryError: 'Please select a new primary location',
        adminAlertPrefix: 'This zone can only contain locations owned by',
        adminAlertSuffix: 'Select locations from this owner when adding to the zone.',
        owner: 'Owner',
        status: 'Status',
        active: 'Active',
        inactive: 'Inactive',
        zoneSettings: 'Zone Settings',
        viewSettings: 'View Settings',
        editZone: 'Edit Zone',
        showingLocations: 'Showing',
        of: 'of',
        locations: 'locations',
        previous: 'Previous',
        next: 'Next',
        roaming: 'Roaming',
        roamingOn: 'On',
        roamingOff: 'Off',
    },
    fr: {
        loading: 'Chargement...',
        noLocations: 'Aucun emplacement dans cette zone',
        noLocationsDesc: 'Ajoutez des emplacements depuis la section ci-dessous',
        noAvailableLocations: 'Aucun emplacement disponible',
        noAvailableDesc: 'Tous les emplacements de ce propriétaire sont déjà dans des zones',
        primary: 'Principal',
        setPrimary: 'Définir comme Principal',
        removeLocation: 'Retirer de la Zone',
        addLocation: 'Ajouter à la Zone',
        confirmRemove: 'Êtes-vous sûr de vouloir retirer cet emplacement de la zone?',
        confirmSetPrimary: 'Définir cet emplacement comme principal pour la zone? Les paramètres de cet emplacement seront utilisés par tous les autres emplacements de la zone.',
        selectNewPrimary: 'Sélectionner un Nouvel Emplacement Principal',
        selectNewPrimaryDesc: 'L\'emplacement que vous supprimez est actuellement défini comme principal. Veuillez sélectionner un nouvel emplacement principal parmi les emplacements restants:',
        confirmSelection: 'Confirmer la Sélection',
        cancel: 'Annuler',
        locationAdded: 'Emplacement ajouté à la zone',
        locationRemoved: 'Emplacement retiré de la zone',
        primarySet: 'Emplacement principal défini',
        zoneUpdated: 'Zone mise à jour avec succès',
        errorLoading: 'Erreur lors du chargement des détails de la zone',
        errorUpdating: 'Erreur lors de la mise à jour de la zone',
        errorAddingLocation: 'Erreur lors de l\'ajout de l\'emplacement',
        errorRemovingLocation: 'Erreur lors du retrait de l\'emplacement',
        errorSettingPrimary: 'Erreur lors de la définition de l\'emplacement principal',
        selectNewPrimaryError: 'Veuillez sélectionner un nouvel emplacement principal',
        adminAlertPrefix: 'Cette zone ne peut contenir que des emplacements appartenant à',
        adminAlertSuffix: 'Sélectionnez des emplacements de ce propriétaire lors de l\'ajout à la zone.',
        owner: 'Propriétaire',
        status: 'Statut',
        active: 'Actif',
        inactive: 'Inactif',
        zoneSettings: 'Paramètres de la Zone',
        viewSettings: 'Voir les Paramètres',
        editZone: 'Modifier la Zone',
        showingLocations: 'Affichage de',
        of: 'sur',
        locations: 'emplacements',
        previous: 'Précédent',
        next: 'Suivant',
        roaming: 'Itinérance',
        roamingOn: 'Activée',
        roamingOff: 'Désactivée',
    }
};

const T = TRANSLATIONS[PAGE_LOCALE];
let currentZone = null;
let currentLocationsPage = 1;
let currentAvailablePage = 1;
const ITEMS_PER_PAGE = 5;
let locationToRemove = null;
let remainingLocationsForPrimary = [];

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
    loadZoneDetails();
});

async function loadZoneDetails() {
    const token = UserManager.getToken();
    const loadingEl = document.getElementById('zone-loading');
    const contentEl = document.getElementById('zone-content');
    loadingEl.style.display = 'block';
    contentEl.style.display = 'none';
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}`, {
            headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
        });
        if (!response.ok) throw new Error('Failed to load zone');
        const data = await response.json();
        currentZone = data.zone;
        displayZoneInfo(currentZone);
        renderOwnershipAlert(currentZone);
        displayLocations(currentZone.locations || []);
        loadAvailableLocations();
        contentEl.style.display = 'block';
    } catch (error) {
        console.error('Error loading zone:', error);
        toastr.error(T.errorLoading);
    } finally {
        loadingEl.style.display = 'none';
    }
}

function displayZoneInfo(zone) {
    document.getElementById('zone-breadcrumb').textContent = zone.name;
    const primaryLocation = zone.primary_location;
    const settingsUrl = primaryLocation 
        ? `/${PAGE_LOCALE}/locations/${primaryLocation.id}` 
        : '#';
    
    const html = `
        <div class="zone-info-card">
            <div class="zone-info-head">
                <div>
                    <div class="zone-info-title">${zone.name}</div>
                    <div class="zone-info-description">${zone.description || ''}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editZone()">
                    <i data-feather="edit-2"></i> ${T.editZone}
                </button>
            </div>
            <div class="zone-info-meta">
                <div class="zone-info-item">
                    <i data-feather="user"></i>
                    <span>${T.owner}: ${zone.owner ? zone.owner.name : 'N/A'}</span>
                </div>
                <div class="zone-info-item">
                    <i data-feather="map-pin"></i>
                    <span>${zone.locations?.length || 0} ${T.locations}</span>
                </div>
                <div class="zone-info-item">
                    <i data-feather="activity"></i>
                    <span>${T.status}: ${zone.is_active ? T.active : T.inactive}</span>
                </div>
                <div class="zone-info-item">
                    <i data-feather="wifi"></i>
                    <span>${T.roaming}: ${zone.roaming_enabled !== false ? T.roamingOn : T.roamingOff}</span>
                </div>
                ${primaryLocation ? `
                <div class="zone-info-item">
                    <a href="${settingsUrl}" class="btn btn-sm btn-light">
                        <i data-feather="settings"></i> ${T.zoneSettings}
                    </a>
                </div>` : ''}
            </div>
        </div>
    `;
    document.getElementById('zone-info-container').innerHTML = html;
    feather.replace();
}

function renderOwnershipAlert(zone) {
    const user = UserManager.getUser();
    const container = document.getElementById('admin-alert-container');
    if (UserManager.isAdminOrAbove() && zone.owner) {
        container.innerHTML = `
            <div class="admin-alert">
                <i data-feather="info" style="width: 24px; height: 24px;"></i>
                <div>${T.adminAlertPrefix} <strong>${zone.owner.name}</strong>. ${T.adminAlertSuffix}</div>
            </div>
        `;
        feather.replace();
    }
}

function displayLocations(locations) {
    const container = document.getElementById('locations-list');
    if (!locations || locations.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon"><i data-feather="map-pin"></i></div>
                <h4>${T.noLocations}</h4>
                <p class="text-muted">${T.noLocationsDesc}</p>
            </div>
        `;
        feather.replace();
        return;
    }
    
    const totalPages = Math.ceil(locations.length / ITEMS_PER_PAGE);
    const startIdx = (currentLocationsPage - 1) * ITEMS_PER_PAGE;
    const endIdx = startIdx + ITEMS_PER_PAGE;
    const paginatedLocations = locations.slice(startIdx, endIdx);
    
    let html = paginatedLocations.map(location => {
        const isPrimary = currentZone.primary_location_id === location.id;
        return `
            <div class="location-card ${isPrimary ? 'primary' : ''}">
                <div class="location-header">
                    <div>
                        <div class="location-name">${location.name}</div>
                        <div class="location-address">${location.address || 'N/A'}</div>
                        <div class="location-badges">
                            ${isPrimary ? `<span class="badge badge-primary">${T.primary}</span>` : ''}
                        </div>
                    </div>
                    <div class="location-actions">
                        ${!isPrimary ? `
                            <button class="btn btn-sm btn-outline-primary" onclick="setPrimary(${location.id})">
                                <i data-feather="star"></i> ${T.setPrimary}
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="removeLocation(${location.id})">
                            <i data-feather="x"></i> ${T.removeLocation}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Add pagination controls
    if (totalPages > 1) {
        html += `
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    ${T.showingLocations} ${startIdx + 1}-${Math.min(endIdx, locations.length)} ${T.of} ${locations.length} ${T.locations}
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" 
                            onclick="changeLocationsPage(-1)" 
                            ${currentLocationsPage === 1 ? 'disabled' : ''}>
                        ${T.previous}
                    </button>
                    <span class="mx-2">${currentLocationsPage} / ${totalPages}</span>
                    <button class="btn btn-sm btn-outline-secondary" 
                            onclick="changeLocationsPage(1)" 
                            ${currentLocationsPage === totalPages ? 'disabled' : ''}>
                        ${T.next}
                    </button>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
    feather.replace();
}

function changeLocationsPage(delta) {
    currentLocationsPage += delta;
    displayLocations(currentZone.locations || []);
}

async function loadAvailableLocations() {
    const token = UserManager.getToken();
    const container = document.getElementById('available-locations-container');
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}/available-locations`, {
            headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
        });
        if (!response.ok) throw new Error('Failed to load available locations');
        const data = await response.json();
        displayAvailableLocations(data.locations || []);
    } catch (error) {
        console.error('Error loading available locations:', error);
        container.innerHTML = `<p class="text-danger">${T.errorLoading}</p>`;
    }
}

function displayAvailableLocations(locations) {
    const container = document.getElementById('available-locations-container');
    if (!locations || locations.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon"><i data-feather="inbox"></i></div>
                <h5>${T.noAvailableLocations}</h5>
                <p class="text-muted">${T.noAvailableDesc}</p>
            </div>
        `;
        feather.replace();
        return;
    }
    
    const totalPages = Math.ceil(locations.length / ITEMS_PER_PAGE);
    const startIdx = (currentAvailablePage - 1) * ITEMS_PER_PAGE;
    const endIdx = startIdx + ITEMS_PER_PAGE;
    const paginatedLocations = locations.slice(startIdx, endIdx);
    
    let html = paginatedLocations.map(location => `
        <div class="location-card">
            <div class="location-header">
                <div>
                    <div class="location-name">${location.name}</div>
                    <div class="location-address">${location.address || 'N/A'}</div>
                </div>
                <div class="location-actions">
                    <button class="btn btn-sm btn-primary" onclick="addLocationToZone(${location.id})">
                        <i data-feather="plus"></i> ${T.addLocation}
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add pagination controls
    if (totalPages > 1) {
        html += `
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    ${T.showingLocations} ${startIdx + 1}-${Math.min(endIdx, locations.length)} ${T.of} ${locations.length} ${T.locations}
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" 
                            onclick="changeAvailablePage(-1)" 
                            ${currentAvailablePage === 1 ? 'disabled' : ''}>
                        ${T.previous}
                    </button>
                    <span class="mx-2">${currentAvailablePage} / ${totalPages}</span>
                    <button class="btn btn-sm btn-outline-secondary" 
                            onclick="changeAvailablePage(1)" 
                            ${currentAvailablePage === totalPages ? 'disabled' : ''}>
                        ${T.next}
                    </button>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
    feather.replace();
}

function changeAvailablePage(delta) {
    currentAvailablePage += delta;
    const token = UserManager.getToken();
    fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}/available-locations`, {
        headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
    })
    .then(res => res.json())
    .then(data => displayAvailableLocations(data.locations || []))
    .catch(error => console.error('Error:', error));
}

async function addLocationToZone(locationId) {
    const token = UserManager.getToken();
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}/locations/${locationId}`, {
            method: 'POST',
            headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Failed to add location');
        toastr.success(T.locationAdded);
        loadZoneDetails();
    } catch (error) {
        console.error('Error adding location:', error);
        toastr.error(T.errorAddingLocation);
    }
}

async function removeLocation(locationId) {
    if (!confirm(T.confirmRemove)) return;
    
    const token = UserManager.getToken();
    const isPrimary = currentZone.primary_location_id === locationId;
    const otherLocations = currentZone.locations.filter(loc => loc.id !== locationId);
    
    // If removing primary and there are other locations, show selection modal
    if (isPrimary && otherLocations.length > 0) {
        locationToRemove = locationId;
        remainingLocationsForPrimary = otherLocations;
        showPrimarySelectionModal(otherLocations);
        return;
    }
    
    // Otherwise, proceed with removal
    await performRemoveLocation(locationId, null);
}

function showPrimarySelectionModal(locations) {
    const modalHtml = `
        <div class="modal fade" id="select-primary-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${T.selectNewPrimary}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>${T.selectNewPrimaryDesc}</p>
                        <div id="primary-selection-list">
                            ${locations.map(loc => `
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="primary-${loc.id}" name="new-primary" value="${loc.id}" class="custom-control-input">
                                    <label class="custom-control-label" for="primary-${loc.id}">
                                        <strong>${loc.name}</strong><br>
                                        <small class="text-muted">${loc.address || 'N/A'}</small>
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">${T.cancel}</button>
                        <button type="button" class="btn btn-primary" onclick="confirmPrimarySelection()">${T.confirmSelection}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#select-primary-modal').remove();
    $('body').append(modalHtml);
    $('#select-primary-modal').modal('show');
}

async function confirmPrimarySelection() {
    const selected = document.querySelector('input[name="new-primary"]:checked');
    if (!selected) {
        toastr.error(T.selectNewPrimaryError);
        return;
    }
    
    const newPrimaryId = selected.value;
    $('#select-primary-modal').modal('hide');
    
    await performRemoveLocation(locationToRemove, newPrimaryId);
}

async function performRemoveLocation(locationId, newPrimaryId) {
    const token = UserManager.getToken();
    try {
        const body = newPrimaryId ? JSON.stringify({ new_primary_id: newPrimaryId }) : undefined;
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        };
        if (body) {
            headers['Content-Type'] = 'application/json';
        }
        
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}/locations/${locationId}`, {
            method: 'DELETE',
            headers: headers,
            body: body
        });
        
        const data = await response.json();
        
        if (data.requires_primary_selection) {
            // Server requires primary selection, show modal
            showPrimarySelectionModal(data.remaining_locations);
            return;
        }
        
        if (!response.ok) throw new Error(data.message || 'Failed to remove location');
        toastr.success(T.locationRemoved);
        loadZoneDetails();
    } catch (error) {
        console.error('Error removing location:', error);
        toastr.error(T.errorRemovingLocation);
    }
}

async function setPrimary(locationId) {
    if (!confirm(T.confirmSetPrimary)) return;
    
    const token = UserManager.getToken();
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}/primary/${locationId}`, {
            method: 'PUT',
            headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Failed to set primary');
        toastr.success(T.primarySet);
        loadZoneDetails();
    } catch (error) {
        console.error('Error setting primary:', error);
        toastr.error(T.errorSettingPrimary);
    }
}

async function loadAndInitSharedUsersSelect2(preselectedSharedUsers = []) {
    if (allUsersForZoneDetails.length === 0) {
        const token = UserManager.getToken();
        try {
            const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Failed to load users');
            const data = await response.json();
            allUsersForZoneDetails = data.users || [];
        } catch (error) {
            console.error('Error loading users for shared access:', error);
            return;
        }
    }

    const $el = $('#edit-zone-shared-users');
    if (!$el.length) return;

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.empty();

    allUsersForZoneDetails.forEach(u => {
        const $opt = $('<option>', { value: u.id, text: `${u.name} (${u.email})` });
        $opt.data('role', u.role || 'user');
        $el.append($opt);
    });

    $el.select2({
        placeholder: PAGE_LOCALE === 'fr' ? 'Rechercher des utilisateurs...' : 'Search users...',
        allowClear: true,
        width: '100%',
        templateResult: function (item) {
            if (!item.id) return item.text;
            const role = $(item.element).data('role') || 'user';
            return $(`<span>${item.text}${roleBadgeHtml(role)}</span>`);
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            const role = $(item.element).data('role') || 'user';
            return $(`<span>${item.text}${roleBadgeHtml(role)}</span>`);
        }
    });

    const preselectedIds = (preselectedSharedUsers || []).map(e => String(e.user_id ?? e));
    $el.val(preselectedIds).trigger('change');
}

async function loadAndInitOwnerSelect2(currentOwnerId) {
    if (allUsersForZoneDetails.length === 0) {
        const token = UserManager.getToken();
        try {
            const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Failed to load users');
            const data = await response.json();
            allUsersForZoneDetails = data.users || [];
        } catch (error) {
            console.error('Error loading users for owner:', error);
            return;
        }
    }

    const $el = $('#edit-zone-owner');
    if (!$el.length) return;

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.empty();

    allUsersForZoneDetails.forEach(u => {
        const $opt = $('<option>', { value: u.id, text: `${u.name} (${u.email})` });
        $opt.data('role', u.role || 'user');
        $el.append($opt);
    });

    $el.select2({
        placeholder: PAGE_LOCALE === 'fr' ? 'Rechercher un propriétaire...' : 'Search owner...',
        allowClear: false,
        width: '100%',
        templateResult: function (item) {
            if (!item.id) return item.text;
            const role = $(item.element).data('role') || 'user';
            return $(`<span>${item.text}${roleBadgeHtml(role)}</span>`);
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            const role = $(item.element).data('role') || 'user';
            return $(`<span>${item.text}${roleBadgeHtml(role)}</span>`);
        }
    });

    $el.val(String(currentOwnerId)).trigger('change');
}

async function editZone() {
    document.getElementById('edit-zone-name').value = currentZone.name;
    document.getElementById('edit-zone-description').value = currentZone.description || '';
    const roamingEdit = document.getElementById('edit-zone-roaming-enabled');
    if (roamingEdit) {
        roamingEdit.checked = currentZone.roaming_enabled !== false;
    }

    // Show/populate owner dropdown and shared users for admin/superadmin
    const ownerGroup = document.getElementById('edit-zone-owner-group');
    const sharedGroup = document.getElementById('edit-zone-shared-users-group');
    if (UserManager.isAdminOrAbove()) {
        if (ownerGroup) {
            ownerGroup.style.display = 'block';
            await loadAndInitOwnerSelect2(currentZone.owner_id);
        }
        if (sharedGroup) {
            sharedGroup.style.display = 'block';
            await loadAndInitSharedUsersSelect2(currentZone.shared_users || []);
        }
    } else {
        if (ownerGroup) ownerGroup.style.display = 'none';
        if (sharedGroup) sharedGroup.style.display = 'none';
    }
    
    // Show primary location info if available
    const primaryInfoContainer = document.getElementById('primary-location-info');
    if (currentZone.primary_location) {
        const settingsUrl = `/${PAGE_LOCALE}/locations/${currentZone.primary_location.id}`;
        const inheritanceTitle = PAGE_LOCALE === 'fr' ? 'Héritage des Paramètres' : 'Settings Inheritance';
        const inheritanceMessage = PAGE_LOCALE === 'fr'
            ? `Les paramètres réseau, sécurité et configuration sont hérités de l'emplacement principal. Toute modification appliquée à l'emplacement principal sera automatiquement propagée à tous les autres emplacements de cette zone.`
            : `Network, security, and configuration settings are inherited from the Primary Location. Any changes applied to the Primary Location will automatically propagate to all other locations in this zone.`;
        const eyebrow = PAGE_LOCALE === 'fr' ? 'Emplacement Principal' : 'Primary Location';
        const manageLabel = PAGE_LOCALE === 'fr' ? 'Gérer les Paramètres de la Zone' : 'Manage Zone Settings';

        primaryInfoContainer.innerHTML = `
            <div class="primary-loc-card">
                <div class="primary-loc-icon"><i data-feather="settings"></i></div>
                <div class="primary-loc-body">
                    <div class="primary-loc-eyebrow">${eyebrow}</div>
                    <div class="primary-loc-name">${currentZone.primary_location.name}</div>
                    <div class="primary-loc-addr">
                        <i data-feather="map-pin"></i>
                        ${currentZone.primary_location.address || 'N/A'}
                    </div>
                    <div class="primary-loc-inherit">
                        <div class="primary-loc-inherit-title">
                            <i data-feather="info"></i>${inheritanceTitle}
                        </div>
                        <div>${inheritanceMessage}</div>
                    </div>
                    <a href="${settingsUrl}" class="btn btn-sm btn-primary primary-loc-cta">
                        <i data-feather="settings"></i> ${manageLabel}
                    </a>
                </div>
            </div>
        `;
        feather.replace();
    } else {
        const noPrimaryTitle = PAGE_LOCALE === 'fr' ? 'Aucun Emplacement Principal' : 'No Primary Location';
        const noPrimaryMessage = PAGE_LOCALE === 'fr'
            ? 'Pour configurer les paramètres de cette zone, vous devez d\'abord ajouter des emplacements et définir l\'un d\'eux comme principal.'
            : 'To configure settings for this zone, you must first add locations and designate one as the primary location.';
        primaryInfoContainer.innerHTML = `
            <div class="no-primary-warn">
                <div class="no-primary-warn-icon"><i data-feather="alert-triangle"></i></div>
                <div>
                    <div class="no-primary-warn-title">${noPrimaryTitle}</div>
                    <div class="no-primary-warn-body">${noPrimaryMessage}</div>
                </div>
            </div>
        `;
        feather.replace();
    }
    
    $('#edit-zone-modal').modal('show');
}

async function updateZoneInfo() {
    const name = document.getElementById('edit-zone-name').value.trim();
    const description = document.getElementById('edit-zone-description').value.trim();
    
    if (!name) {
        toastr.error('Zone name is required');
        return;
    }
    
    const token = UserManager.getToken();
    const roamingEdit = document.getElementById('edit-zone-roaming-enabled');
    const payload = {
        name,
        description,
        is_active: currentZone.is_active,
        roaming_enabled: roamingEdit ? roamingEdit.checked : currentZone.roaming_enabled !== false,
    };

    if (UserManager.isAdminOrAbove()) {
        const newOwnerId = $('#edit-zone-owner').val();
        if (newOwnerId) {
            payload.owner_id = parseInt(newOwnerId);
        }
        const selectedIds = $('#edit-zone-shared-users').val() || [];
        payload.shared_users = selectedIds.map(id => ({ user_id: parseInt(id), access_level: 'full' }));
    }

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}`, {
            method: 'PUT',
            headers: {'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Failed to update zone');
        toastr.success(T.zoneUpdated);
        $('#edit-zone-modal').modal('hide');
        loadZoneDetails();
    } catch (error) {
        console.error('Error updating zone:', error);
        toastr.error(T.errorUpdating);
    }
}
