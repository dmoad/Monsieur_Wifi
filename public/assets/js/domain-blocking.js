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

function getCategoryIdByName(categoryName) {
    const categoryMapping = {
        'Adult Content': '1',
        'Gambling': '2',
        'Malware': '3',
        'Social Media': '4',
        'Streaming': '5',
        'Custom List': '6',
        'Contenu adulte': '1',
        'Jeux d\'argent': '2',
        'Logiciels malveillants': '3',
        'Réseaux sociaux': '4',
        'Liste personnalisée': '6'
    };
    return categoryMapping[categoryName] || null;
}

function closeAllDbMenus() {
    document.querySelectorAll('.db-menu.open').forEach(m => m.classList.remove('open'));
}

function loadDomains() {
    $.ajax({
        url: '/api/blocked-domains',
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
    const query  = ($('#db-search').val() || '').trim().toLowerCase();
    const cat    = window.selectedCategory || null;

    let list = domainsData;
    if (query) {
        list = list.filter(d =>
            (d.domain || '').toLowerCase().includes(query) ||
            ((d.category && d.category.name) || '').toLowerCase().includes(query)
        );
    }
    if (cat) {
        list = list.filter(d => d.category && d.category.name === cat);
    }

    if (!list.length) {
        $tbody.html(`<tr><td colspan="5" class="text-center py-4" style="color:var(--mw-text-muted)">${t.noDomains}</td></tr>`);
        return;
    }

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
    $('#db-search').on('input', function() { renderDomains(); });

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

        const categoryCheckboxMap = {
            1: '#category-adult',
            2: '#category-gambling',
            3: '#category-malware',
            4: '#category-social',
            5: '#category-streaming',
            6: '#category-custom'
        };

        categoryArray.forEach(function(category) {
            const checkboxId = categoryCheckboxMap[category.id];
            if (!checkboxId) return;
            const checkbox = $(checkboxId);
            if (!checkbox.length) return;
            const categoryCard = checkbox.closest('.card');
            if (!categoryCard.length) return;

            const countSpan = categoryCard.find('h4').next('span');
            countSpan.text(`${category.active_blocked_domains_count || 0} ${t.domainCount}`);
            checkbox.prop('checked', category.is_enabled);
            category.is_enabled ? categoryCard.addClass('border-primary') : categoryCard.removeClass('border-primary');
        });
    }

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });

    $('.custom-switch input[type="checkbox"]').on('change', function() {
        const categoryCard = $(this).closest('.card');
        const categoryName = categoryCard.find('h4').text().trim();
        const isEnabled = $(this).is(':checked');
        const checkbox = $(this);

        let categoryId = getCategoryIdByName(categoryName);
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
                    isEnabled ? categoryCard.addClass('border-primary') : categoryCard.removeClass('border-primary');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`${categoryName} ${isEnabled ? t.enabled : t.disabled} ${t.successfully}`);
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
            error: function() { alert(t.failedLoadDomainData); }
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
                alert('Error: ' + msg);
            }
        });
    });

    $(document).on('click', '.delete-domain-btn', function() {
        const domainId = $(this).data('id');
        const domainName = $(this).data('domain');
        closeAllDbMenus();

        if (confirm(`${t.confirmDelete} "${domainName}" ${t.confirmDeleteSuffix}`)) {
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
                error: function() { alert(t.failedDeleteDomain); }
            });
        }
    });

    $('.card.cursor-pointer').addClass('category-card');

    $(document).on('click', '#view-all-domains', function() {
        window.selectedCategory = null;
        renderDomains();
    });

    $(document).on('click', '#export-all-domains', function() {
        const button = $(this);
        const originalText = button.html();
        button.prop('disabled', true).html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="margin-right:4px"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.28"/></svg>Exporting...');

        let exportUrl = '/api/blocked-domains/export?format=txt&active_only=true';
        if (window.selectedCategory) {
            const categoryId = getCategoryIdByName(window.selectedCategory);
            if (categoryId) exportUrl += `&category_id=${categoryId}`;
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
