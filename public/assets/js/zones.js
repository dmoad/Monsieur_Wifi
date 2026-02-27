// Zones management JavaScript
const PAGE_LOCALE = document.documentElement.lang || 'en';

const TRANSLATIONS = {
    en: {
        loading: 'Loading...',
        noZones: 'No zones found',
        noZonesDesc: 'Create your first zone to group locations together',
        createZone: 'Create Zone',
        editZone: 'Edit Zone',
        locations: 'Locations',
        owner: 'Owner',
        viewDetails: 'View Details',
        edit: 'Edit',
        delete: 'Delete',
        adminAlert: 'Note: Each zone can only contain locations from the same owner. When managing zones, ensure all locations belong to the zone\'s owner.',
        confirmDelete: 'Are you sure you want to delete this zone? All locations will be decoupled.',
        zoneDeleted: 'Zone deleted successfully',
        zoneSaved: 'Zone saved successfully',
        errorLoading: 'Error loading zones',
        errorSaving: 'Error saving zone',
        errorDeleting: 'Error deleting zone',
        nameRequired: 'Zone name is required',
        createZoneTitle: 'Create Zone',
        editZoneTitle: 'Edit Zone',
    },
    fr: {
        loading: 'Chargement...',
        noZones: 'Aucune zone trouvée',
        noZonesDesc: 'Créez votre première zone pour regrouper des emplacements',
        createZone: 'Créer une Zone',
        editZone: 'Modifier la Zone',
        locations: 'Emplacements',
        owner: 'Propriétaire',
        viewDetails: 'Voir les Détails',
        edit: 'Modifier',
        delete: 'Supprimer',
        adminAlert: 'Note: Chaque zone ne peut contenir que des emplacements du même propriétaire. Lors de la gestion des zones, assurez-vous que tous les emplacements appartiennent au propriétaire de la zone.',
        confirmDelete: 'Êtes-vous sûr de vouloir supprimer cette zone? Tous les emplacements seront découplés.',
        zoneDeleted: 'Zone supprimée avec succès',
        zoneSaved: 'Zone enregistrée avec succès',
        errorLoading: 'Erreur lors du chargement des zones',
        errorSaving: 'Erreur lors de l\'enregistrement de la zone',
        errorDeleting: 'Erreur lors de la suppression de la zone',
        nameRequired: 'Le nom de la zone est requis',
        createZoneTitle: 'Créer une Zone',
        editZoneTitle: 'Modifier la Zone',
    }
};

const T = TRANSLATIONS[PAGE_LOCALE];
let currentZoneId = null;

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
    
    renderAdminAlert();
    loadZones();
});

function renderAdminAlert() {
    const user = UserManager.getUser();
    const container = document.getElementById('admin-alert-container');
    
    if (UserManager.isAdminOrAbove()) {
        container.innerHTML = `
            <div class="admin-alert">
                <i data-feather="info" style="width: 24px; height: 24px;"></i>
                <div>${T.adminAlert}</div>
            </div>
        `;
        feather.replace();
    }
}

async function loadZones() {
    const token = UserManager.getToken();
    const loadingEl = document.getElementById('zones-loading');
    const listEl = document.getElementById('zones-list');
    
    loadingEl.style.display = 'block';
    listEl.innerHTML = '';
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load zones');
        }
        
        const data = await response.json();
        displayZones(data.zones || []);
    } catch (error) {
        console.error('Error loading zones:', error);
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

function displayZones(zones) {
    const listEl = document.getElementById('zones-list');
    const isAdmin = UserManager.isAdminOrAbove();
    
    if (zones.length === 0) {
        listEl.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="layers" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h4>${T.noZones}</h4>
                        <p class="text-muted">${T.noZonesDesc}</p>
                        <button class="btn btn-primary mt-3" onclick="showZoneModal()">
                            <i data-feather="plus"></i> ${T.createZone}
                        </button>
                    </div>
                </div>
            </div>
        `;
        feather.replace();
        return;
    }
    
    const locale = PAGE_LOCALE === 'fr' ? 'fr' : 'en';
    
    listEl.innerHTML = zones.map(zone => {
        const locationCount = zone.location_count || zone.locations?.length || 0;
        const ownerName = zone.owner ? `${zone.owner.name} (${zone.owner.email})` : '';
        
        return `
            <div class="zone-card">
                <div class="zone-header">
                    <div class="flex-grow-1">
                        <h3 class="zone-name">${zone.name}</h3>
                        ${zone.description ? `<p class="zone-description">${zone.description}</p>` : ''}
                        ${isAdmin && zone.owner ? `<small class="text-muted"><i data-feather="user" style="width: 14px; height: 14px;"></i> ${ownerName}</small>` : ''}
                    </div>
                    <div class="zone-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='/${locale}/zones/${zone.id}'" title="${T.viewDetails}">
                            <i data-feather="eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="showZoneModal(${zone.id})" title="${T.edit}">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteZone(${zone.id})" title="${T.delete}">
                            <i data-feather="trash-2"></i>
                        </button>
                    </div>
                </div>
                <div class="zone-body">
                    <div class="zone-stats">
                        <div class="zone-stat">
                            <div class="zone-stat-icon bg-light-primary text-primary">
                                <i data-feather="map-pin"></i>
                            </div>
                            <div>
                                <small class="text-muted">${T.locations}</small>
                                <h5 class="mb-0">${locationCount}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    feather.replace();
}

function showZoneModal(zoneId = null) {
    currentZoneId = zoneId;
    const modal = $('#zone-modal');
    const title = document.getElementById('zone-modal-title');
    
    document.getElementById('zone-form').reset();
    document.getElementById('zone-id').value = '';
    
    // Clear primary location info
    const primaryInfoContainer = document.getElementById('primary-location-info-edit');
    if (primaryInfoContainer) {
        primaryInfoContainer.innerHTML = '';
    }
    
    if (zoneId) {
        title.textContent = T.editZoneTitle;
        loadZoneForEdit(zoneId);
    } else {
        title.textContent = T.createZoneTitle;
    }
    
    modal.modal('show');
}

async function loadZoneForEdit(zoneId) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${zoneId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load zone');
        }
        
        const data = await response.json();
        const zone = data.zone;
        
        document.getElementById('zone-id').value = zone.id;
        document.getElementById('zone-name').value = zone.name;
        document.getElementById('zone-description').value = zone.description || '';
        
        // Show primary location info if available
        const primaryInfoContainer = document.getElementById('primary-location-info-edit');
        if (primaryInfoContainer) {
            if (zone.primary_location) {
                const settingsUrl = `/${PAGE_LOCALE}/locations/${zone.primary_location.id}`;
                const inheritanceMessage = PAGE_LOCALE === 'fr' 
                    ? `Les paramètres réseau, sécurité et configuration sont hérités de l'emplacement principal. Toute modification appliquée à l'emplacement principal sera automatiquement propagée à tous les autres emplacements de cette zone.`
                    : `Network, security, and configuration settings are inherited from the Primary Location. Any changes applied to the Primary Location will automatically propagate to all other locations in this zone.`;
                
                const inheritanceTitle = PAGE_LOCALE === 'fr' ? 'Héritage des Paramètres' : 'Settings Inheritance';
                primaryInfoContainer.innerHTML = `
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);">
                        <div class="d-flex align-items-start">
                            <div style="background: rgba(255,255,255,0.2); border-radius: 10px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 1rem;">
                                <i data-feather="settings" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 0.25rem;">
                                            ${PAGE_LOCALE === 'fr' ? 'Emplacement Principal' : 'Primary Location'}
                                        </div>
                                        <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">
                                            ${zone.primary_location.name}
                                        </div>
                                        <div style="font-size: 0.875rem; opacity: 0.85;">
                                            <i data-feather="map-pin" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i>
                                            ${zone.primary_location.address || 'N/A'}
                                        </div>
                                    </div>
                                </div>
                                <div style="background: rgba(255,255,255,0.15); border-left: 3px solid rgba(255,255,255,0.5); padding: 0.75rem; border-radius: 6px; margin-bottom: 0.75rem;">
                                    <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.35rem;">
                                        <i data-feather="info" style="width: 16px; height: 16px; margin-right: 0.25rem;"></i>
                                        ${inheritanceTitle}
                                    </div>
                                    <div style="font-size: 0.8125rem; line-height: 1.5; opacity: 0.95;">
                                        ${inheritanceMessage}
                                    </div>
                                </div>
                                <a href="${settingsUrl}" class="btn btn-light btn-sm" style="font-weight: 500;">
                                    <i data-feather="settings" style="width: 16px; height: 16px;"></i>
                                    ${PAGE_LOCALE === 'fr' ? 'Gérer les Paramètres de la Zone' : 'Manage Zone Settings'}
                                </a>
                            </div>
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
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; color: white; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.2);">
                        <div class="d-flex align-items-center">
                            <div style="background: rgba(255,255,255,0.2); border-radius: 10px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 1rem;">
                                <i data-feather="alert-triangle" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 1rem; margin-bottom: 0.35rem;">
                                    ${noPrimaryTitle}
                                </div>
                                <div style="font-size: 0.875rem; opacity: 0.95; line-height: 1.4;">
                                    ${noPrimaryMessage}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                feather.replace();
            }
        }
    } catch (error) {
        console.error('Error loading zone:', error);
        toastr.error(T.errorLoading);
    }
}

async function saveZone() {
    const zoneId = document.getElementById('zone-id').value;
    const name = document.getElementById('zone-name').value.trim();
    const description = document.getElementById('zone-description').value.trim();
    
    if (!name) {
        toastr.error(T.nameRequired);
        return;
    }
    
    const token = UserManager.getToken();
    const isEdit = zoneId !== '';
    const url = isEdit 
        ? `${APP_CONFIG.API.BASE_URL}/v1/zones/${zoneId}`
        : `${APP_CONFIG.API.BASE_URL}/v1/zones`;
    
    try {
        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, description })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                throw new Error(data.message || 'Failed to save zone');
            }
            return;
        }
        
        toastr.success(T.zoneSaved);
        $('#zone-modal').modal('hide');
        loadZones();
    } catch (error) {
        console.error('Error saving zone:', error);
        toastr.error(T.errorSaving);
    }
}

async function deleteZone(zoneId) {
    if (!confirm(T.confirmDelete)) {
        return;
    }
    
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/zones/${zoneId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Failed to delete zone');
        }
        
        toastr.success(T.zoneDeleted);
        loadZones();
    } catch (error) {
        console.error('Error deleting zone:', error);
        toastr.error(T.errorDeleting);
    }
}
