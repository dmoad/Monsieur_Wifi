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
        confirmRemoveTitle: 'Remove location from zone?',
        removeBtn: 'Remove',
        confirmSetPrimary: 'Set this location as the primary for the zone? Settings from this location will be used by all other locations in the zone.',
        confirmSetPrimaryTitle: 'Set as primary location?',
        setPrimaryBtn: 'Set Primary',
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
        editZone: 'Edit',
        deleteZone: 'Delete',
        confirmDeleteZone: 'Are you sure you want to delete this zone? Locations will be un-grouped.',
        confirmDeleteZoneTitle: 'Delete zone?',
        deleteBtn: 'Delete',
        zoneDeleted: 'Zone deleted',
        errorDeletingZone: 'Error deleting zone',
        actions: 'Actions',
        settingsCardTitle: 'Zone Settings',
        settingsCardDesc: 'Network, security, and configuration settings for this zone are defined by its primary location and inherited by every other location in the zone.',
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
        confirmRemoveTitle: 'Retirer l\'emplacement de la zone ?',
        removeBtn: 'Retirer',
        confirmSetPrimary: 'Définir cet emplacement comme principal pour la zone? Les paramètres de cet emplacement seront utilisés par tous les autres emplacements de la zone.',
        confirmSetPrimaryTitle: 'Définir comme emplacement principal ?',
        setPrimaryBtn: 'Définir principal',
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
        editZone: 'Modifier',
        deleteZone: 'Supprimer',
        confirmDeleteZone: 'Êtes-vous sûr de vouloir supprimer cette zone ? Les emplacements seront dissociés.',
        confirmDeleteZoneTitle: 'Supprimer la zone ?',
        deleteBtn: 'Supprimer',
        zoneDeleted: 'Zone supprimée',
        errorDeletingZone: 'Erreur lors de la suppression de la zone',
        actions: 'Actions',
        settingsCardTitle: 'Paramètres de la Zone',
        settingsCardDesc: 'Les paramètres réseau, sécurité et configuration de cette zone sont définis par son emplacement principal et hérités par tous les autres emplacements de la zone.',
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

    // ── Card 1: Zone facts (name, description, meta, Edit/Delete kebab)
    const infoHtml = `
        <div class="zone-info-card">
            <div class="zone-info-head">
                <div>
                    <div class="zone-info-title">${zone.name}</div>
                    <div class="zone-info-description">${zone.description || ''}</div>
                </div>
                <div class="lz-row-actions">
                    <button type="button" class="lz-action-btn" onclick="editZone()" data-toggle="tooltip" title="${T.editZone}" aria-label="${T.editZone}">
                        <i data-feather="edit"></i>
                    </button>
                    <button type="button" class="lz-action-btn lz-action-danger" onclick="deleteZone()" data-toggle="tooltip" title="${T.deleteZone}" aria-label="${T.deleteZone}">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
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
            </div>
        </div>
    `;
    document.getElementById('zone-info-container').innerHTML = infoHtml;

    // ── Card 2: Zone Settings (primary-location pointer + inheritance + CTA)
    let settingsHtml;
    if (primaryLocation) {
        const settingsUrl = `/${PAGE_LOCALE}/locations/${primaryLocation.id}`;
        const eyebrow = PAGE_LOCALE === 'fr' ? 'Emplacement Principal' : 'Primary Location';
        const manageLabel = PAGE_LOCALE === 'fr' ? 'Gérer les Paramètres' : 'Manage Settings';
        settingsHtml = `
            <div class="card zone-settings-card">
                <div class="card-header">
                    <h4 class="card-title">${T.settingsCardTitle}</h4>
                </div>
                <div class="card-body">
                    <p class="zone-settings-desc">${T.settingsCardDesc}</p>
                    <div class="zone-settings-primary">
                        <div class="primary-loc-icon"><i data-feather="home"></i></div>
                        <div class="primary-loc-body">
                            <div class="primary-loc-eyebrow">${eyebrow}</div>
                            <div class="primary-loc-name">${primaryLocation.name}</div>
                            <div class="primary-loc-addr">
                                <i data-feather="map-pin"></i>
                                ${primaryLocation.address || 'N/A'}
                            </div>
                        </div>
                        <a href="${settingsUrl}" class="btn btn-sm btn-primary">
                            <i data-feather="settings"></i> ${manageLabel}
                        </a>
                    </div>
                </div>
            </div>
        `;
    } else {
        const noPrimaryTitle = PAGE_LOCALE === 'fr' ? 'Aucun Emplacement Principal' : 'No Primary Location';
        const noPrimaryMessage = PAGE_LOCALE === 'fr'
            ? `Pour gérer les paramètres de cette zone, ouvrez l'onglet Emplacements, ajoutez un emplacement et désignez-le comme principal.`
            : `To manage settings for this zone, open the Locations tab, add a location, and designate it as primary.`;
        settingsHtml = `
            <div class="card zone-settings-card">
                <div class="card-header">
                    <h4 class="card-title">${T.settingsCardTitle}</h4>
                </div>
                <div class="card-body">
                    <div class="no-primary-warn">
                        <div class="no-primary-warn-icon"><i data-feather="alert-triangle"></i></div>
                        <div>
                            <div class="no-primary-warn-title">${noPrimaryTitle}</div>
                            <div class="no-primary-warn-body">${noPrimaryMessage}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    document.getElementById('zone-settings-container').innerHTML = settingsHtml;

    feather.replace();
    $('[data-toggle="tooltip"]').tooltip({ container: 'body' });
}

async function deleteZone() {
    const ok = await MwConfirm.open({
        title: T.confirmDeleteZoneTitle || 'Delete zone?',
        message: T.confirmDeleteZone,
        confirmText: T.deleteBtn || 'Delete',
        cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
        destructive: true,
    });
    if (!ok) return;
    const token = UserManager.getToken();
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}`, {
            method: 'DELETE',
            headers: {'Authorization': `Bearer ${token}`, 'Accept': 'application/json'}
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Failed to delete zone');
        toastr.success(T.zoneDeleted);
        setTimeout(() => { window.location.href = `/${PAGE_LOCALE}/zones`; }, 600);
    } catch (error) {
        console.error('Error deleting zone:', error);
        toastr.error(T.errorDeletingZone);
    }
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
            <div class="location-card ${isPrimary ? 'primary' : ''}" onclick="window.location.href='/${PAGE_LOCALE}/locations/${location.id}?from=zone-${ZONE_ID}'">
                <div class="lc-icon"><i data-feather="wifi"></i></div>
                <div class="lc-body">
                    <div class="lc-name-row">
                        <div class="location-name">${location.name}</div>
                        ${isPrimary ? `<span class="lc-badge-primary">${T.primary}</span>` : ''}
                    </div>
                    <div class="location-address">${location.address || 'N/A'}</div>
                </div>
                <div class="lz-row-actions" onclick="event.stopPropagation()">
                    ${!isPrimary ? `
                        <button type="button" class="lz-action-btn" onclick="setPrimary(${location.id})" data-toggle="tooltip" title="${T.setPrimary}" aria-label="${T.setPrimary}">
                            <i data-feather="home"></i>
                        </button>
                    ` : ''}
                    <button type="button" class="lz-action-btn lz-action-danger" onclick="removeLocation(${location.id})" data-toggle="tooltip" title="${T.removeLocation}" aria-label="${T.removeLocation}">
                        <i data-feather="x"></i>
                    </button>
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
    $(container).find('[data-toggle="tooltip"]').tooltip({ container: 'body' });
}

function changeLocationsPage(delta) {
    currentLocationsPage += delta;
    displayLocations(currentZone.locations || []);
}


// Tab switching for the zone-details page
document.addEventListener('click', function(e) {
    const tab = e.target.closest('.mw-tab');
    if (!tab) return;
    const key = tab.dataset.tab;
    if (!key) return;
    document.querySelectorAll('.mw-tab').forEach(t => t.classList.toggle('active', t === tab));
    document.querySelectorAll('.mw-panel').forEach(p => p.classList.toggle('active', p.id === 'zd-panel-' + key));
    if (key === 'analytics') {
        loadZoneAnalyticsTab();
    }
});

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
    const ok = await MwConfirm.open({
        title: T.confirmRemoveTitle || 'Remove location?',
        message: T.confirmRemove,
        confirmText: T.removeBtn || 'Remove',
        cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
        destructive: true,
    });
    if (!ok) return;
    
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
    const ok = await MwConfirm.open({
        title: T.confirmSetPrimaryTitle || 'Set as primary?',
        message: T.confirmSetPrimary,
        confirmText: T.setPrimaryBtn || 'Set Primary',
        cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
    });
    if (!ok) return;
    
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
    
    $('#edit-zone-modal').modal('show');
}

// ============================================================================
// ZONE ANALYTICS TAB
// ============================================================================

let zdAnalyticsLoaded       = false;
let zdHourlyChart           = null;
let zdUserSessionsChart     = null;
let zdDailyChart            = null;
let zdDeviceChart           = null;
let zdAnalyticsPeriod       = '7days';
let zdUsersPage             = 1;
let zdUsersLastPage         = 1;
let zdUsersSearch           = '';
let zdUsersSearchTimer      = null;

// ---- helpers ----------------------------------------------------------------

function zdApiBase() {
    return `${APP_CONFIG.API.BASE_URL}/v1/zones/${ZONE_ID}`;
}

function zdAuthHeaders() {
    return { 'Authorization': `Bearer ${UserManager.getToken()}`, 'Accept': 'application/json' };
}

async function zdFetch(url) {
    const res = await fetch(url, { headers: zdAuthHeaders() });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

function zdFmtBytes(bytes) {
    if (!bytes) return '0 B';
    const k = 1024, units = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(Math.floor(Math.log(bytes) / Math.log(k)), units.length - 1);
    return Math.round(bytes / Math.pow(k, i)) + ' ' + units[i];
}

function zdFmtDate(s) {
    if (!s) return '—';
    try { return new Date(s).toLocaleDateString(); } catch (e) { return s; }
}

function zdEsc(s) {
    if (s == null || s === '') return '—';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ---- entry point ------------------------------------------------------------

function loadZoneAnalyticsTab() {
    if (zdAnalyticsLoaded) return;
    zdAnalyticsLoaded = true;

    zdLoadHourlyBandwidth();
    zdLoadDailyUsage(zdAnalyticsPeriod);
    zdLoadDeviceTypes();
    zdLoadUsers(1, '');
}

// ---- 1. Hourly bandwidth ----------------------------------------------------

async function zdLoadHourlyBandwidth() {
    try {
        const res = await zdFetch(`${zdApiBase()}/analytics/hourly-bandwidth`);
        if (res.success) {
            zdRenderHourlyChart(res.data || []);
            const el = document.getElementById('zd-hourly-updated');
            if (el) el.textContent = new Date().toLocaleTimeString();
        }
    } catch (err) {
        console.error('Zone hourly bandwidth error:', err);
    }
}

function zdRenderHourlyChart(buckets) {
    const categories   = buckets.map(b => b.hour);
    // bytes * 8 bits/byte ÷ 3600 s/hour ÷ 1,000,000 bits/Mbit → average Mbps
    const FACTOR = 8 / (3600 * 1_000_000);
    const dlMbps = buckets.map(b => parseFloat(((b.download || 0) * FACTOR).toFixed(3)));
    const ulMbps = buckets.map(b => parseFloat(((b.upload   || 0) * FACTOR).toFixed(3)));

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const series = [
        { name: 'Download', data: dlMbps },
        { name: 'Upload',   data: ulMbps },
    ];

    const options = {
        theme:      { mode: dark ? 'dark' : 'light' },
        chart:      { type: 'line', height: 220, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:      { categories, labels: { rotate: -45, style: { fontSize: '10px' } } },
        stroke:     { curve: 'smooth', width: 2 },
        colors:     ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend:     { show: true, position: 'top' },
        grid:       { borderColor: gridColor },
        tooltip:    { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                      y: { formatter: val => `${val} Mbps` } },
        yaxis:      { labels: { formatter: val => `${val}` }, title: { text: 'Mbps', style: { fontSize: '11px' } } },
        markers:    { size: 3 },
    };

    if (zdHourlyChart) {
        zdHourlyChart.updateOptions({
            series, xaxis: { categories }, colors: ['#667eea', '#43d39e'],
            grid: { borderColor: gridColor }, theme: { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false, y: { formatter: val => `${val} Mbps` } },
        });
    } else {
        const el = document.getElementById('zd-hourly-chart');
        if (!el) return;
        zdHourlyChart = new ApexCharts(el, options);
        zdHourlyChart.render();
    }
}

// ---- 2. Daily usage (download / upload + users & sessions) ------------------

async function zdLoadDailyUsage(period) {
    try {
        const res = await zdFetch(`${zdApiBase()}/analytics/daily-usage?period=${period}`);
        if (res.success && res.data && res.data.daily_stats) {
            zdRenderDailyChart(res.data.daily_stats);
            zdRenderUserSessionsChart(res.data.daily_stats);
        }
    } catch (err) {
        console.error('Zone daily usage error:', err);
    }
}

function zdRenderDailyChart(dailyStats) {
    const categories = dailyStats.map(d => d.date);
    const dlData     = dailyStats.map(d => d.total_download || 0);
    const ulData     = dailyStats.map(d => d.total_upload   || 0);

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const series = [
        { name: 'Download', data: dlData },
        { name: 'Upload',   data: ulData },
    ];

    const options = {
        theme:      { mode: dark ? 'dark' : 'light' },
        chart:      { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:      { categories },
        stroke:     { curve: 'smooth', width: 2 },
        fill:       { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
        colors:     ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend:     { show: true, position: 'top' },
        grid:       { borderColor: gridColor },
        tooltip:    { theme: dark ? 'dark' : 'light', shared: true, intersect: false,
                      y: { formatter: zdFmtBytes } },
        yaxis:      { labels: { formatter: zdFmtBytes } },
    };

    if (zdDailyChart) {
        zdDailyChart.updateOptions({
            series, xaxis: { categories }, colors: ['#667eea', '#43d39e'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
            grid: { borderColor: gridColor }, theme: { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false, y: { formatter: zdFmtBytes } },
        });
    } else {
        const el = document.getElementById('zd-daily-chart');
        if (!el) return;
        zdDailyChart = new ApexCharts(el, options);
        zdDailyChart.render();
    }
}

function zdRenderUserSessionsChart(dailyStats) {
    const categories     = dailyStats.map(d => d.date);
    const seriesUsers    = dailyStats.map(d => d.unique_users || 0);
    const seriesSessions = dailyStats.map(d => d.sessions     || 0);

    const dark      = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'var(--mw-border)' : '#f1f1f1';

    const series = [
        { name: 'Users',    data: seriesUsers },
        { name: 'Sessions', data: seriesSessions },
    ];

    const options = {
        theme:      { mode: dark ? 'dark' : 'light' },
        chart:      { type: 'area', height: 220, toolbar: { show: false }, background: 'transparent' },
        series,
        xaxis:      { categories },
        stroke:     { curve: 'smooth', width: 2 },
        fill:       { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
        colors:     ['#667eea', '#43d39e'],
        dataLabels: { enabled: false },
        legend:     { show: true, position: 'top' },
        grid:       { borderColor: gridColor },
        tooltip:    { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        yaxis:      { labels: { formatter: val => Math.round(val) } },
    };

    if (zdUserSessionsChart) {
        zdUserSessionsChart.updateOptions({
            series, xaxis: { categories }, colors: ['#667eea', '#43d39e'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
            grid: { borderColor: gridColor }, theme: { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light', shared: true, intersect: false },
        });
    } else {
        const el = document.getElementById('zd-user-sessions-chart');
        if (!el) return;
        zdUserSessionsChart = new ApexCharts(el, options);
        zdUserSessionsChart.render();
    }
}

// ---- 3. Device type donut ---------------------------------------------------

async function zdLoadDeviceTypes() {
    try {
        const res = await zdFetch(`${zdApiBase()}/analytics/device-types`);
        if (res.success) {
            zdRenderDeviceTypeChart(res.data || []);
        }
    } catch (err) {
        console.error('Zone device types error:', err);
    }
}

function zdRenderDeviceTypeChart(data) {
    const emptyEl = document.getElementById('zd-device-type-empty');
    const chartEl = document.getElementById('zd-device-type-chart');

    if (!data || data.length === 0) {
        if (chartEl) chartEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
        return;
    }
    if (emptyEl) emptyEl.style.display = 'none';
    if (chartEl) chartEl.style.display = 'block';

    const labels  = data.map(d => d.type);
    const counts  = data.map(d => d.count);
    const dark    = document.documentElement.getAttribute('data-theme') === 'dark';

    const options = {
        theme:  { mode: dark ? 'dark' : 'light' },
        chart:  { type: 'donut', height: 300, background: 'transparent' },
        series: counts,
        labels,
        colors: ['#667eea', '#43d39e', '#f7971e', '#ee0979', '#36d1dc', '#5b86e5', '#a8edea'],
        legend: { show: true, position: 'bottom', fontSize: '12px', horizontalAlign: 'center' },
        dataLabels: { enabled: true, formatter: val => `${Math.round(val)}%`, dropShadow: { enabled: false } },
        plotOptions: { pie: { donut: { size: '55%' }, expandOnClick: false } },
        tooltip: { theme: dark ? 'dark' : 'light' },
    };

    if (zdDeviceChart) {
        zdDeviceChart.updateOptions({
            series: counts, labels,
            theme:  { mode: dark ? 'dark' : 'light' },
            tooltip: { theme: dark ? 'dark' : 'light' },
        });
    } else {
        if (!chartEl) return;
        zdDeviceChart = new ApexCharts(chartEl, options);
        zdDeviceChart.render();
    }
}

// ---- 4. Guest user table ----------------------------------------------------

async function zdLoadUsers(page, search) {
    zdUsersPage   = page;
    zdUsersSearch = search;

    const loadingEl = document.getElementById('zd-users-loading');
    if (loadingEl) loadingEl.style.display = 'block';

    try {
        let url = `${zdApiBase()}/analytics/users?page=${page}&per_page=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        const res = await zdFetch(url);
        if (res.success && res.data) {
            const { data, current_page, last_page, total } = res.data;
            zdUsersLastPage = last_page;
            zdRenderUsersTable(data || []);
            zdRenderUsersPagination(current_page, last_page, total);
        }
    } catch (err) {
        console.error('Zone users load error:', err);
        const tbody = document.getElementById('zd-users-tbody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4"><small>Error loading users</small></td></tr>`;
        }
    } finally {
        if (loadingEl) loadingEl.style.display = 'none';
    }
}

function zdRenderUsersTable(users) {
    const tbody = document.getElementById('zd-users-tbody');
    if (!tbody) return;

    if (users.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4"><small>No users found</small></td></tr>`;
        return;
    }

    tbody.innerHTML = users.map(u => {
        const badge = u.blocked
            ? `<span class="badge badge-danger">Blocked</span>`
            : `<span class="badge badge-success">Active</span>`;

        return `<tr>
            <td>${zdEsc(u.name)}</td>
            <td><code style="font-size:0.75rem;">${zdEsc(u.mac_address)}</code></td>
            <td>${zdEsc(u.email)}</td>
            <td>${zdEsc(u.device_type)}</td>
            <td>${zdEsc(u.os)}</td>
            <td>${u.session_count || 0}</td>
            <td>${zdFmtDate(u.last_seen)}</td>
            <td>${badge}</td>
        </tr>`;
    }).join('');

    if (typeof feather !== 'undefined') feather.replace();
}

function zdRenderUsersPagination(currentPage, lastPage, total) {
    const paginationEl = document.getElementById('zd-users-pagination');
    const countEl      = document.getElementById('zd-users-count-range');
    const pageInfoEl   = document.getElementById('zd-users-page-info');
    const prevBtn      = document.getElementById('zd-users-prev');
    const nextBtn      = document.getElementById('zd-users-next');
    const totalEl      = document.getElementById('zd-users-total');

    if (totalEl) totalEl.textContent = total || '';

    if (!paginationEl) return;
    if (lastPage <= 1) {
        paginationEl.style.setProperty('display', 'none', 'important');
    } else {
        paginationEl.style.setProperty('display', 'flex', 'important');
        const start = (currentPage - 1) * 15 + 1;
        const end   = Math.min(currentPage * 15, total);
        if (countEl) countEl.textContent = `${start}–${end} / ${total}`;
        if (pageInfoEl) pageInfoEl.textContent = `${currentPage} / ${lastPage}`;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= lastPage;
    }
}

// ---- 5. Theme observer ------------------------------------------------------

new MutationObserver(function () {
    const dark    = document.documentElement.getAttribute('data-theme') === 'dark';
    const theme   = { mode: dark ? 'dark' : 'light' };
    const tooltip = { theme: dark ? 'dark' : 'light' };
    const grid    = { borderColor: dark ? 'var(--mw-border)' : '#f1f1f1' };

    if (zdHourlyChart)       zdHourlyChart.updateOptions({ theme, tooltip, grid });
    if (zdUserSessionsChart) zdUserSessionsChart.updateOptions({ theme, tooltip, grid });
    if (zdDailyChart)        zdDailyChart.updateOptions({ theme, tooltip, grid });
    if (zdDeviceChart)       zdDeviceChart.updateOptions({ theme, tooltip });
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

// ---- 6. Period buttons + search + pagination (delegated) --------------------

document.addEventListener('click', function (e) {
    // Period buttons
    const periodBtn = e.target.closest('.zd-period-btn');
    if (periodBtn) {
        document.querySelectorAll('.zd-period-btn').forEach(b => {
            b.style.background = 'transparent';
            b.style.color      = '#6c757d';
        });
        periodBtn.style.background = 'var(--mw-primary)';
        periodBtn.style.color      = 'white';
        const days = periodBtn.dataset.period;
        zdAnalyticsPeriod = days + 'days';
        zdLoadDailyUsage(zdAnalyticsPeriod);
        return;
    }

    // Pagination
    if (e.target.closest('#zd-users-prev')) {
        if (zdUsersPage > 1) zdLoadUsers(zdUsersPage - 1, zdUsersSearch);
        return;
    }
    if (e.target.closest('#zd-users-next')) {
        if (zdUsersPage < zdUsersLastPage) zdLoadUsers(zdUsersPage + 1, zdUsersSearch);
        return;
    }

    // Refresh
    if (e.target.closest('#zd-users-refresh')) {
        zdLoadHourlyBandwidth();
        zdLoadDailyUsage(zdAnalyticsPeriod);
        zdLoadDeviceTypes();
        zdLoadUsers(1, zdUsersSearch);
    }
});

document.addEventListener('input', function (e) {
    if (e.target && e.target.id === 'zd-user-search') {
        clearTimeout(zdUsersSearchTimer);
        const val = e.target.value.trim();
        zdUsersSearchTimer = setTimeout(() => zdLoadUsers(1, val), 350);
    }
});

// ============================================================================
// END OF ANALYTICS SECTION
// ============================================================================

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
