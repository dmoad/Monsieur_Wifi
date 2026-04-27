// Domain Blocking Management JavaScript with Bilingual Support

const TRANSLATIONS = {
    en: {
        domainCount: 'domains',
        enabled: 'enabled',
        disabled: 'disabled',
        successfully: 'successfully',
        failedUpdateCategory: 'Failed to update category',
        addDomain: 'Add Domain',
        edit: 'Edit',
        delete: 'Delete',
        confirmDelete: 'Are you sure you want to delete',
        confirmDeleteSuffix: 'from the block list?',
        confirmDeleteTitle: 'Delete blocked domain?',
        deleteBtn: 'Delete',
        failedLoadDomainData: 'Failed to load domain data',
        failedAddDomain: 'Failed to add domain',
        failedUpdateDomain: 'Failed to update domain',
        failedDeleteDomain: 'Failed to delete domain',
        exportStarted: 'Export started! Check your downloads folder.',
        blockedDomains: 'Blocked Domains',
        allBlockedDomains: 'All Blocked Domains',
        adultContent: 'Adult Content',
        gambling: 'Gambling',
        malware: 'Malware',
        socialMedia: 'Social Media',
        streaming: 'Streaming',
        customList: 'Custom List',
        noDomains: 'No domains found',
    },
    fr: {
        domainCount: 'domaines',
        enabled: 'activé',
        disabled: 'désactivé',
        successfully: 'avec succès',
        failedUpdateCategory: 'Échec de la mise à jour de la catégorie',
        addDomain: 'Ajouter un domaine',
        edit: 'Modifier',
        delete: 'Supprimer',
        confirmDelete: 'Êtes-vous sûr de vouloir supprimer',
        confirmDeleteSuffix: 'de la liste de blocage ?',
        confirmDeleteTitle: 'Supprimer le domaine bloqué ?',
        deleteBtn: 'Supprimer',
        failedLoadDomainData: 'Échec du chargement des données du domaine',
        failedAddDomain: 'Échec de l\'ajout du domaine',
        failedUpdateDomain: 'Échec de la mise à jour du domaine',
        failedDeleteDomain: 'Échec de la suppression du domaine',
        exportStarted: 'Exportation lancée ! Vérifiez votre dossier de téléchargements.',
        blockedDomains: 'Domaines bloqués',
        allBlockedDomains: 'Tous les domaines bloqués',
        adultContent: 'Contenu adulte',
        gambling: 'Jeux d\'argent',
        malware: 'Logiciels malveillants',
        socialMedia: 'Réseaux sociaux',
        streaming: 'Streaming',
        customList: 'Liste personnalisée',
        noDomains: 'Aucun domaine trouvé',
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

let domainsData = [];
let dbCurrentPage = 1;
let dbItemsPerPage = 25;

const _dbDotsSvg  = `<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>`;
const _dbEditSvg  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;
const _dbTrashSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>`;

function getCategoryBadgeClass(slug) {
    switch (slug) {
        case 'adult-content': return 'badge-category-adult';
        case 'gambling':      return 'badge-category-gambling';
        case 'malware':       return 'badge-category-malware';
        case 'social-media':  return 'badge-category-social';
        case 'streaming':     return 'badge-category-streaming';
        case 'custom-list':   return 'badge-category-custom';
        default:              return 'badge-category-custom';
    }
}

function getCategoryAvatarClass(slug) {
    switch (slug) {
        case 'adult-content': return 'bg-light-danger';
        case 'gambling':      return 'bg-light-warning';
        case 'malware':       return 'bg-light-primary';
        case 'social-media':  return 'bg-light-info';
        case 'streaming':     return 'bg-light-success';
        case 'custom-list':   return 'bg-light-secondary';
        default:              return 'bg-light-secondary';
    }
}


function closeAllDbMenus() {
    document.querySelectorAll('.db-menu.open').forEach(m => m.classList.remove('open'));
}

function loadDomains() {
    $.ajax({
        url: '/api/blocked-domains?per_page=500',
        type: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + UserManager.getToken(),
        },
        success: function(json) {
            domainsData = json.data || [];
            renderDomains();
        },
        error: function() {
            $('#db-domains-tbody').html(`<tr><td colspan="5" class="text-center py-4 text-danger">${t.failedLoadDomainData}</td></tr>`);
        }
    });
}

function renderDomains() {
    const $tbody = $('#db-domains-tbody');
    const $pg = $('#db-pagination');
    const query  = ($('#db-search').val() || '').trim().toLowerCase();
    const catId  = window.selectedCategoryId || null;

    let filtered = domainsData;
    if (query) {
        filtered = filtered.filter(d =>
            (d.domain || '').toLowerCase().includes(query) ||
            ((d.category && d.category.name) || '').toLowerCase().includes(query)
        );
    }
    if (catId) {
        filtered = filtered.filter(d => d.category && String(d.category.id) === String(catId));
    }

    if (!filtered.length) {
        $tbody.html(`<tr><td colspan="5" class="text-center py-4" style="color:var(--mw-text-muted)">${t.noDomains}</td></tr>`);
        $pg.empty();
        return;
    }

    const totalItems = filtered.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / dbItemsPerPage));
    if (dbCurrentPage > totalPages) dbCurrentPage = totalPages;
    const startIdx = (dbCurrentPage - 1) * dbItemsPerPage;
    const endIdx = Math.min(startIdx + dbItemsPerPage, totalItems);
    const list = filtered.slice(startIdx, endIdx);

    const rows = list.map(function(domain) {
        const slug        = domain.category ? domain.category.slug : 'custom-list';
        const catName     = domain.category ? domain.category.name : '—';
        const badgeClass  = getCategoryBadgeClass(slug);
        const avatarClass = getCategoryAvatarClass(slug);
        const addedDate   = domain.created_at
            ? new Date(domain.created_at).toLocaleDateString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : '—';
        const updatedDate = domain.updated_at
            ? new Date(domain.updated_at).toLocaleDateString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : '—';
        return `<tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar ${avatarClass} mr-1 p-25">
                        <div class="avatar-content"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                    </div>
                    <span>${domain.domain}</span>
                </div>
            </td>
            <td><span class="badge badge-pill ${badgeClass}">${catName}</span></td>
            <td>${addedDate}</td>
            <td>${updatedDate}</td>
            <td class="db-col-actions">
                <div class="db-kebab-wrap">
                    <button type="button" class="db-kebab-btn db-kebab-toggle" data-domain-id="${domain.id}">${_dbDotsSvg}</button>
                    <div class="db-menu" id="db-menu-${domain.id}">
                        <button type="button" class="db-menu-item edit-domain-btn" data-id="${domain.id}">${_dbEditSvg} ${t.edit}</button>
                        <div class="db-menu-divider"></div>
                        <button type="button" class="db-menu-item db-menu-danger delete-domain-btn" data-id="${domain.id}" data-domain="${domain.domain}">${_dbTrashSvg} ${t.delete}</button>
                    </div>
                </div>
            </td>
        </tr>`;
    }).join('');
    $tbody.html(rows);
    renderDbPagination(totalItems, totalPages, startIdx, endIdx);
}

function renderDbPagination(totalItems, totalPages, startIdx, endIdx) {
    const $pg = $('#db-pagination');
    if (totalPages <= 1) { $pg.empty(); return; }
    const localeStr = (PAGE_LOCALE === 'fr')
        ? `Affichage ${startIdx + 1}-${endIdx} sur ${totalItems}`
        : `Showing ${startIdx + 1}-${endIdx} of ${totalItems}`;
    let buttons = `<button class="btn btn-sm btn-outline-primary" onclick="goToDbPage(${dbCurrentPage - 1})" ${dbCurrentPage === 1 ? 'disabled' : ''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>`;
    const maxBtns = 5;
    let startP = Math.max(1, dbCurrentPage - Math.floor(maxBtns / 2));
    let endP = Math.min(totalPages, startP + maxBtns - 1);
    if (endP - startP < maxBtns - 1) startP = Math.max(1, endP - maxBtns + 1);
    if (startP > 1) {
        buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToDbPage(1)">1</button>`;
        if (startP > 2) buttons += `<span class="mx-2">...</span>`;
    }
    for (let i = startP; i <= endP; i++) {
        buttons += `<button class="btn btn-sm ${i === dbCurrentPage ? 'btn-primary' : 'btn-outline-primary'}" onclick="goToDbPage(${i})">${i}</button>`;
    }
    if (endP < totalPages) {
        if (endP < totalPages - 1) buttons += `<span class="mx-2">...</span>`;
        buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToDbPage(${totalPages})">${totalPages}</button>`;
    }
    buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToDbPage(${dbCurrentPage + 1})" ${dbCurrentPage === totalPages ? 'disabled' : ''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    $pg.html(`<div class="pagination-controls"><div class="pagination-info">${localeStr}</div><div class="pagination-buttons">${buttons}</div></div>`);
}

function goToDbPage(p) {
    dbCurrentPage = p;
    renderDomains();
}

$(window).on('load', function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
        $('.avatar-icon').each(function() {
            $(this).css({ 'width': '24px', 'height': '24px' });
        });
    }

    const profile_picture = localStorage.getItem('profile_picture');
    $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);

    loadCategoriesData();
    loadDomains();

    // Kebab toggle
    $(document).on('click', function(e) {
        const toggleBtn = $(e.target).closest('.db-kebab-toggle');
        if (toggleBtn.length) {
            const id = toggleBtn.data('domain-id');
            const $menu = $(`#db-menu-${id}`);
            const wasOpen = $menu.hasClass('open');
            closeAllDbMenus();
            if (!wasOpen) $menu.addClass('open');
            return;
        }
        if (!$(e.target).closest('.db-kebab-wrap').length) closeAllDbMenus();
    });

    // Search
    $('#db-search').on('input', function() { dbCurrentPage = 1; renderDomains(); });
    $('#db-items-per-page').on('change', function() { dbItemsPerPage = parseInt($(this).val(), 10) || 25; dbCurrentPage = 1; renderDomains(); });

    function loadCategoriesData() {
        $.ajax({
            url: '/api/categories',
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) { updateCategoryCounters(response); },
            error: function(xhr, status, error) { console.error('Failed to load categories:', error); }
        });
    }

    function updateCategoryCounters(categories) {
        let categoryArray = categories.categories || categories;
        if (!Array.isArray(categoryArray)) return;

        categoryArray.forEach(function(category) {
            if (!category.slug) return;
            const checkbox = $('#category-' + category.slug);
            if (!checkbox.length) return;
            const $catcard = checkbox.closest('.db-catcard');
            if (!$catcard.length) return;

            $catcard.find('.db-catcard-count').text(`${category.active_blocked_domains_count || 0} ${t.domainCount}`);
            checkbox.prop('checked', category.is_enabled);
            category.is_enabled ? $catcard.addClass('border-primary') : $catcard.removeClass('border-primary');
        });
    }

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });

    $(document).on('change', '.category-toggle', function() {
        const checkbox   = $(this);
        const $catcard   = checkbox.closest('.db-catcard');
        const categoryId = checkbox.data('category-id');
        const catName    = $catcard.find('.db-catcard-title').text().trim();
        const isEnabled  = checkbox.is(':checked');

        if (!categoryId) {
            checkbox.prop('checked', !isEnabled);
            return;
        }

        checkbox.prop('disabled', true);

        $.ajax({
            url: `/api/categories/${categoryId}/toggle`,
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    isEnabled ? $catcard.addClass('border-primary') : $catcard.removeClass('border-primary');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`${catName} ${isEnabled ? t.enabled : t.disabled} ${t.successfully}`);
                    }
                    setTimeout(function() {
                        loadCategoriesData();
                        loadDomains();
                    }, 500);
                } else {
                    checkbox.prop('checked', !isEnabled);
                    if (typeof toastr !== 'undefined') toastr.error(response.message || t.failedUpdateCategory);
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', !isEnabled);
                let errorMessage = t.failedUpdateCategory;
                if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                if (typeof toastr !== 'undefined') toastr.error(errorMessage);
            },
            complete: function() { checkbox.prop('disabled', false); }
        });
    });

    $('#add-new-domain form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/api/blocked-domains',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            data: JSON.stringify({
                domain: $('#domain-name').val(),
                category_id: $('#domain-category').val(),
                notes: $('#domain-notes').val(),
                block_subdomains: true
            }),
            success: function(response) {
                if (response.success) {
                    loadDomains();
                    $('#add-new-domain form').trigger('reset');
                    $('#add-new-domain').modal('hide');
                    loadCategoriesData();
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let msg = t.failedAddDomain;
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof toastr !== 'undefined') toastr.error(msg);
            }
        });
    });

    $(document).on('click', '.edit-domain-btn', function() {
        const domainId = $(this).data('id');
        closeAllDbMenus();

        $.ajax({
            url: `/api/blocked-domains/${domainId}`,
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) {
                if (response.success) {
                    const domain = response.domain;
                    $('#edit-domain-name').val(domain.domain);
                    $('#edit-domain-category').val(domain.category_id).trigger('change');
                    $('#edit-domain-notes').val(domain.notes || '');
                    $('#edit-block-subdomains').prop('checked', domain.block_subdomains);
                    $('#edit-domain').data('domain-id', domainId).modal('show');
                }
            },
            error: function() { toastr.error(t.failedLoadDomainData); }
        });
    });

    $('#edit-domain form').on('submit', function(e) {
        e.preventDefault();

        const domainId = $('#edit-domain').data('domain-id');

        $.ajax({
            url: `/api/blocked-domains/${domainId}`,
            type: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            data: JSON.stringify({
                category_id: $('#edit-domain-category').val(),
                notes: $('#edit-domain-notes').val(),
                block_subdomains: $('#edit-block-subdomains').is(':checked')
            }),
            success: function(response) {
                if (response.success) {
                    loadDomains();
                    $('#edit-domain').modal('hide');
                    loadCategoriesData();
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                }
            },
            error: function(xhr) {
                let msg = t.failedUpdateDomain;
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                toastr.error(msg);
            }
        });
    });

    $(document).on('click', '.delete-domain-btn', async function() {
        const domainId = $(this).data('id');
        const domainName = $(this).data('domain');
        closeAllDbMenus();

        const ok = await MwConfirm.open({
            title: t.confirmDeleteTitle || 'Delete domain?',
            message: `${t.confirmDelete} "${domainName}" ${t.confirmDeleteSuffix}`,
            confirmText: t.deleteBtn || 'Delete',
            cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
            destructive: true,
        });
        if (!ok) return;

        $.ajax({
            url: `/api/blocked-domains/${domainId}`,
            type: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) {
                if (response.success) {
                    loadDomains();
                    loadCategoriesData();
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                }
            },
            error: function() { toastr.error(t.failedDeleteDomain); }
        });
    });

    $('.card.cursor-pointer').addClass('category-card');

    $(document).on('click', '#view-all-domains', function() {
        window.selectedCategoryId = null;
        dbCurrentPage = 1;
        renderDomains();
    });

    $(document).on('click', '#export-all-domains', function() {
        const button = $(this);
        const originalText = button.html();
        button.prop('disabled', true).html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="margin-right:4px"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.28"/></svg>Exporting...');

        let exportUrl = '/api/blocked-domains/export?format=txt&active_only=true';
        if (window.selectedCategoryId) {
            exportUrl += `&category_id=${window.selectedCategoryId}`;
        }

        const link = document.createElement('a');
        link.href = exportUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        setTimeout(function() { button.prop('disabled', false).html(originalText); }, 1000);
        if (typeof toastr !== 'undefined') toastr.success(t.exportStarted);
    });

    feather.replace({ width: 14, height: 14 });
});

$(document).ready(function() {
    const user = UserManager.getUser();
    const token = UserManager.getToken();

    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    $('.user-name').text(user.name);
    $('.user-status').text(user.role);
});
