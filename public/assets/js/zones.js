// Zones management JavaScript
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

let allUsersCache = [];

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
let allZones = [];
let currentPage = 1;
let itemsPerPage = 25;

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
    
    // Load users for admin/superadmin
    if (UserManager.isAdminOrAbove()) {
        loadUsersForZones();
    }
});

function renderAdminAlert() {
    const user = UserManager.getUser();
    const container = document.getElementById('admin-alert-container');
    
    if (UserManager.isAdminOrAbove()) {
        container.innerHTML = `
            <div class="admin-alert">
                <i data-feather="info"></i>
                <div>${T.adminAlert}</div>
            </div>
        `;
        feather.replace();
    }
}

async function loadUsersForZones(preselectedSharedUsers = []) {
    if (!UserManager.isAdminOrAbove()) return;

    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to load users');

        const data = await response.json();
        allUsersCache = data.users || [];

        // Populate owner dropdown
        const ownerSelect = document.getElementById('zone-owner-select');
        if (ownerSelect) {
            let options = `<option value="">${PAGE_LOCALE === 'fr' ? 'Sélectionner le propriétaire...' : 'Select owner...'}</option>`;
            allUsersCache.forEach(u => {
                options += `<option value="${u.id}">${u.name} (${u.email})</option>`;
            });
            ownerSelect.innerHTML = options;
        }

        // Populate shared users Select2
        initSharedUsersSelect2(preselectedSharedUsers);
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

function initSharedUsersSelect2(preselectedSharedUsers = []) {
    const $el = $('#zone-shared-users');
    if (!$el.length) return;

    // Destroy existing instance to avoid duplicates
    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.empty();

    // Build options — exclude current owner if set
    const currentOwner = parseInt(document.getElementById('zone-owner-select')?.value || '0');
    allUsersCache.forEach(u => {
        if (u.id === currentOwner) return;
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

    // Pre-select shared users
    const preselectedIds = (preselectedSharedUsers || []).map(e => String(e.user_id ?? e));
    $el.val(preselectedIds).trigger('change');
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
        allZones = data.zones || [];
        currentPage = 1;
        displayZones();
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

function displayZones() {
    const listEl = document.getElementById('zones-list');
    const paginationEl = document.getElementById('pagination-container');
    const isAdmin = UserManager.isAdminOrAbove();
    
    if (allZones.length === 0) {
        listEl.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="layers" style="width: 32px; height: 32px;"></i>
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
        paginationEl.innerHTML = '';
        feather.replace();
        return;
    }
    
    const locale = PAGE_LOCALE === 'fr' ? 'fr' : 'en';
    
    // Calculate pagination
    const totalItems = allZones.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
    const currentZones = allZones.slice(startIndex, endIndex);
    
    // Display zones
    listEl.innerHTML = currentZones.map(zone => {
        const locationCount = zone.location_count ?? (zone.locations?.length || 0);
        const ownerName     = zone.owner ? zone.owner.name : '';
        const managers      = isAdmin && ownerName
            ? `<span class="zc-meta-item"><i data-feather="user"></i> ${ownerName}</span>`
            : '';
        const addressItem   = zone.description
            ? `<span class="zc-meta-item" title="${zone.description}"><i data-feather="map-pin"></i> ${zone.description}</span>`
            : '';

        return `
            <div class="zone-card card card-clickable" onclick="window.location.href='/${locale}/zones/${zone.id}'">
                <div class="zc-head">
                    <div class="zc-info">
                        <div class="zc-name">${zone.name}</div>
                        <div class="zc-meta">${addressItem}${managers}</div>
                    </div>
                    <div class="zc-kebab-wrap" onclick="event.stopPropagation()">
                        <button class="zc-kebab-btn" onclick="toggleZoneMenu(event, ${zone.id})" title="${T.edit}">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                        <div class="zc-menu" id="zc-menu-${zone.id}">
                            <button class="zc-menu-item" onclick="showZoneModal(${zone.id}); closeAllZoneMenus()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                ${T.edit}
                            </button>
                            <div class="zc-menu-divider"></div>
                            <button class="zc-menu-item zc-menu-danger" onclick="deleteZone(${zone.id}); closeAllZoneMenus()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                ${T.delete}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="zc-stats">
                    <div class="zc-stat">
                        <div class="zc-stat-val zc-p"><i data-feather="map-pin"></i> ${locationCount}</div>
                        <div class="zc-stat-lbl">${T.locations}</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Display pagination
    if (totalPages > 1) {
        const showingText = PAGE_LOCALE === 'fr' 
            ? `Affichage ${startIndex + 1}-${endIndex} sur ${totalItems}`
            : `Showing ${startIndex + 1}-${endIndex} of ${totalItems}`;
        
        let paginationButtons = '';
        
        // Previous button
        paginationButtons += `
            <button class="btn btn-sm btn-outline-primary" 
                    onclick="goToPage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''}>
                <i data-feather="chevron-left"></i>
            </button>
        `;
        
        // Page numbers
        const maxPageButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPageButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);
        
        if (endPage - startPage < maxPageButtons - 1) {
            startPage = Math.max(1, endPage - maxPageButtons + 1);
        }
        
        if (startPage > 1) {
            paginationButtons += `
                <button class="btn btn-sm btn-outline-primary" onclick="goToPage(1)">1</button>
            `;
            if (startPage > 2) {
                paginationButtons += `<span class="mx-2">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationButtons += `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}" 
                        onclick="goToPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationButtons += `<span class="mx-2">...</span>`;
            }
            paginationButtons += `
                <button class="btn btn-sm btn-outline-primary" onclick="goToPage(${totalPages})">${totalPages}</button>
            `;
        }
        
        // Next button
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
    
    feather.replace();
}

function goToPage(page) {
    const totalPages = Math.ceil(allZones.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        displayZones();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function changeItemsPerPage() {
    const select = document.getElementById('items-per-page');
    itemsPerPage = parseInt(select.value);
    currentPage = 1;
    displayZones();
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
    
    // Show/hide owner dropdown based on admin status and mode
    console.log('showZoneModal - zoneId:', zoneId);
    console.log('showZoneModal - isAdminOrAbove:', UserManager.isAdminOrAbove());
    
    const ownerGroup = document.getElementById('zone-owner-select-group');
    console.log('ownerGroup element:', ownerGroup);
    
    if (ownerGroup) {
        const shouldShow = UserManager.isAdminOrAbove() && !zoneId;
        ownerGroup.style.display = shouldShow ? 'block' : 'none';
    }

    // Show shared-users group for admin/superadmin (both create and edit)
    const sharedGroup = document.getElementById('zone-shared-users-group');
    if (sharedGroup) {
        sharedGroup.style.display = UserManager.isAdminOrAbove() ? 'block' : 'none';
    }

    if (UserManager.isAdminOrAbove()) {
        // Re-init Select2 with no preselection; loadZoneForEdit will preselect on edit
        if (allUsersCache.length > 0) {
            initSharedUsersSelect2([]);
        } else {
            loadUsersForZones([]);
        }
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
        const roamingEl = document.getElementById('zone-roaming-enabled');
        if (roamingEl) {
            roamingEl.checked = zone.roaming_enabled !== false;
        }

        // Pre-select shared users when admin is editing
        if (UserManager.isAdminOrAbove() && zone.shared_users) {
            initSharedUsersSelect2(zone.shared_users);
        }

        // Show primary location info if available
        const primaryInfoContainer = document.getElementById('primary-location-info-edit');
        if (primaryInfoContainer) {
            if (zone.primary_location) {
                const settingsUrl = `/${PAGE_LOCALE}/locations/${zone.primary_location.id}`;
                const inheritanceMessage = PAGE_LOCALE === 'fr' 
                    ? `Les paramètres réseau, sécurité et configuration sont hérités de l'emplacement principal. Toute modification appliquée à l'emplacement principal sera automatiquement propagée à tous les autres emplacements de cette zone.`
                    : `Network, security, and configuration settings are inherited from the Primary Location. Any changes applied to the Primary Location will automatically propagate to all other locations in this zone.`;
                
                const inheritanceTitle = PAGE_LOCALE === 'fr' ? 'Héritage des Paramètres' : 'Settings Inheritance';
                const eyebrow = PAGE_LOCALE === 'fr' ? 'Emplacement Principal' : 'Primary Location';
                const manageLabel = PAGE_LOCALE === 'fr' ? 'Gérer les Paramètres de la Zone' : 'Manage Zone Settings';
                primaryInfoContainer.innerHTML = `
                    <div class="primary-loc-card">
                        <div class="primary-loc-icon"><i data-feather="settings"></i></div>
                        <div class="primary-loc-body">
                            <div class="primary-loc-eyebrow">${eyebrow}</div>
                            <div class="primary-loc-name">${zone.primary_location.name}</div>
                            <div class="primary-loc-addr">
                                <i data-feather="map-pin"></i>
                                ${zone.primary_location.address || 'N/A'}
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
    
    const roamingEl = document.getElementById('zone-roaming-enabled');
    const payload = {
        name,
        description,
        roaming_enabled: roamingEl ? roamingEl.checked : true,
    };

    // Add owner_id if admin is creating for another user
    if (!isEdit && UserManager.isAdminOrAbove()) {
        const ownerId = document.getElementById('zone-owner-select')?.value;
        if (ownerId) {
            payload.owner_id = ownerId;
        }
    }

    // Add shared_users for admin/superadmin
    if (UserManager.isAdminOrAbove()) {
        const selectedIds = $('#zone-shared-users').val() || [];
        payload.shared_users = selectedIds.map(id => ({ user_id: parseInt(id), access_level: 'full' }));
    }
    
    try {
        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
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
        await loadZones();
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
        await loadZones();
    } catch (error) {
        console.error('Error deleting zone:', error);
        toastr.error(T.errorDeleting);
    }
}

function toggleZoneMenu(e, zoneId) {
    e.stopPropagation();
    const target = document.getElementById('zc-menu-' + zoneId);
    const isOpen = target.classList.contains('open');
    closeAllZoneMenus();
    if (!isOpen) target.classList.add('open');
}

function closeAllZoneMenus() {
    document.querySelectorAll('.zc-menu.open').forEach(m => m.classList.remove('open'));
}

document.addEventListener('click', closeAllZoneMenus);
