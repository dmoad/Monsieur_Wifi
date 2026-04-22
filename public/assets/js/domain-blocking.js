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
        customList: 'Custom List'
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
        customList: 'Liste personnalisée'
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

let domainsTable;

const CATEGORY_NAMES = {
    en: {
        'Adult Content': 'Adult Content',
        'Gambling': 'Gambling',
        'Malware': 'Malware',
        'Social Media': 'Social Media',
        'Streaming': 'Streaming',
        'Custom List': 'Custom List'
    },
    fr: {
        'Contenu adulte': 'Adult Content',
        'Jeux d\'argent': 'Gambling',
        'Logiciels malveillants': 'Malware',
        'Réseaux sociaux': 'Social Media',
        'Streaming': 'Streaming',
        'Liste personnalisée': 'Custom List'
    }
};

$(window).on('load', function() {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
        
        $('.avatar-icon').each(function() {
            $(this).css({
                'width': '24px',
                'height': '24px'
            });
        });
    }

    const profile_picture = localStorage.getItem('profile_picture');
    $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
    
    loadCategoriesData();
    
    domainsTable = $('.datatables-domains').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/api/blocked-domains',
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            dataSrc: function(json) {
                console.log("blocked-domains", json);
                return json.data;
            }
        },
        columns: [
            { 
                data: 'domain',
                render: function(data, type, row) {
                    const avatarClass = getCategoryAvatarClass(row.category.slug);
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar ${avatarClass} mr-1 p-25">
                                <div class="avatar-content">
                                    <i data-feather="globe"></i>
                                </div>
                            </div>
                            <span>${data}</span>
                        </div>
                    `;
                }
            },
            { 
                data: 'category',
                render: function(data, type, row) {
                    const badgeClass = getCategoryBadgeClass(data.slug);
                    return `<span class="badge badge-pill ${badgeClass}">${data.name}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric' 
                    });
                }
            },
            { 
                data: 'updated_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric' 
                    });
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item edit-domain-btn" href="javascript:void(0);" data-id="${data}">
                                    <i data-feather="edit-2" class="mr-50"></i>
                                    <span>${t.edit}</span>
                                </a>
                                <a class="dropdown-item delete-domain-btn" href="javascript:void(0);" data-id="${data}">
                                    <i data-feather="trash" class="mr-50"></i>
                                    <span>${t.delete}</span>
                                </a>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        responsive: true,
        columnDefs: [
            {
                targets: [4],
                orderable: false
            }
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            paginate: {
                previous: '‹',
                next: '›'
            }
        },
        drawCallback: function() {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });
    
    function getCategoryBadgeClass(slug) {
        switch(slug) {
            case 'adult-content': return 'badge-category-adult';
            case 'gambling': return 'badge-category-gambling';
            case 'malware': return 'badge-category-malware';
            case 'social-media': return 'badge-category-social';
            case 'streaming': return 'badge-category-streaming';
            case 'custom-list': return 'badge-category-custom';
            default: return 'badge-category-custom';
        }
    }
    
    function getCategoryAvatarClass(slug) {
        switch(slug) {
            case 'adult-content': return 'bg-light-danger';
            case 'gambling': return 'bg-light-warning';
            case 'malware': return 'bg-light-primary';
            case 'social-media': return 'bg-light-info';
            case 'streaming': return 'bg-light-success';
            case 'custom-list': return 'bg-light-secondary';
            default: return 'bg-light-secondary';
        }
    }
    
    function loadCategoriesData() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const authToken = UserManager.getToken();
        
        console.log('Loading categories with CSRF:', csrfToken ? 'present' : 'MISSING');
        console.log('Loading categories with Auth:', authToken ? 'present' : 'MISSING');
        
        $.ajax({
            url: '/api/categories',
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Authorization': 'Bearer ' + authToken,
            },
            success: function(response) {
                console.log('Categories API Response:', response);
                updateCategoryCounters(response);
            },
            error: function(xhr, status, error) {
                console.error('Failed to load categories:', error);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    function updateCategoryCounters(categories) {
        console.log("Raw categories data:", categories);
        
        // Handle different response structures
        let categoryArray = categories;
        if (categories.categories) {
            categoryArray = categories.categories;
        }
        
        console.log("Category array:", categoryArray);
        
        if (!Array.isArray(categoryArray)) {
            console.error('Categories is not an array:', categoryArray);
            return;
        }
        
        // Map category IDs to checkbox IDs
        const categoryCheckboxMap = {
            1: '#category-adult',
            2: '#category-gambling',
            3: '#category-malware',
            4: '#category-social',
            5: '#category-streaming',
            6: '#category-custom'
        };
        
        categoryArray.forEach(function(category) {
            console.log(`Processing category: ${category.name} (ID: ${category.id}), enabled: ${category.is_enabled}, count: ${category.blocked_domains_count}`);
            
            const checkboxId = categoryCheckboxMap[category.id];
            if (!checkboxId) {
                console.warn(`No checkbox mapping for category ID: ${category.id}`);
                return;
            }
            
            const checkbox = $(checkboxId);
            if (!checkbox.length) {
                console.warn(`Checkbox not found: ${checkboxId} for category: ${category.name}`);
                return;
            }
            
            const categoryCard = checkbox.closest('.card');
            if (!categoryCard.length) {
                console.warn(`Card not found for checkbox: ${checkboxId}`);
                return;
            }
            
            // Update domain count
            const countSpan = categoryCard.find('h4').next('span');
            countSpan.text(`${category.active_blocked_domains_count || 0} ${t.domainCount}`);

            // Update checkbox state
            const wasChecked = checkbox.prop('checked');
            checkbox.prop('checked', category.is_enabled);
            
            console.log(`${category.name}: checkbox was ${wasChecked}, now set to ${category.is_enabled}`);

            // Update border
            if (category.is_enabled) {
                categoryCard.addClass('border-primary');
            } else {
                categoryCard.removeClass('border-primary');
            }
        });
        
        console.log('All categories updated successfully');
    }

    
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });

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
            'Streaming': '5',
            'Liste personnalisée': '6'
        };
        return categoryMapping[categoryName] || null;
    }

    $('.custom-switch input[type="checkbox"]').on('change', function() {
        const categoryCard = $(this).closest('.card');
        const categoryName = categoryCard.find('h4').text().trim();
        const isEnabled = $(this).is(':checked');
        const checkbox = $(this);

        console.log(`Toggle clicked: ${categoryName}, new state: ${isEnabled}`);

        let categoryId = getCategoryIdByName(categoryName);
        
        if (!categoryId) {
            console.error('Category ID not found for:', categoryName);
            console.error('Available mappings:', Object.keys({
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
                'Streaming': '5',
                'Liste personnalisée': '6'
            }));
            checkbox.prop('checked', !isEnabled);
            return;
        }

        console.log(`Sending toggle request for category ID ${categoryId} to: /api/categories/${categoryId}/toggle`);
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
                console.log("Toggle API response:", response);
                
                if (response.success) {
                    // Update visual state
                    if (isEnabled) {
                        categoryCard.addClass('border-primary');
                    } else {
                        categoryCard.removeClass('border-primary');
                    }

                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`${categoryName} ${isEnabled ? t.enabled : t.disabled} ${t.successfully}`);
                    }

                    console.log(`Category "${categoryName}" (ID: ${categoryId}) successfully toggled to: ${isEnabled}`);
                    
                    // Reload categories to ensure UI is in sync
                    setTimeout(function() {
                        loadCategoriesData();
                        if (domainsTable && typeof domainsTable.ajax !== 'undefined') {
                            domainsTable.ajax.reload(null, false);
                        }
                    }, 500);
                } else {
                    console.error('API returned success: false', response);
                    checkbox.prop('checked', !isEnabled);
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || t.failedUpdateCategory);
                    } else {
                        alert('Error: ' + (response.message || t.failedUpdateCategory));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Toggle API error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                checkbox.prop('checked', !isEnabled);
                
                let errorMessage = t.failedUpdateCategory;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert('Error: ' + errorMessage);
                }
                console.error('Category toggle error:', error);
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    });
    
    $('#add-new-domain form').on('submit', function(e) {
        e.preventDefault();
        
        const domainName = $('#domain-name').val();
        const categoryId = $('#domain-category').val();
        const notes = $('#domain-notes').val();
        const blockSubdomains = true;
        
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
                domain: domainName,
                category_id: categoryId,
                notes: notes,
                block_subdomains: blockSubdomains
            }),
            success: function(response) {
                if (response.success) {
                    domainsTable.ajax.reload();
                    $('#add-new-domain form').trigger('reset');
                    $('#add-new-domain').modal('hide');
                    loadCategoriesData();
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = t.failedAddDomain;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        });
    });
    
    $(document).on('click', '.edit-domain-btn', function() {
        const domainId = $(this).data('id');
        
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
                    
                    $('#edit-domain').data('domain-id', domainId);
                    $('#edit-domain').modal('show');
                }
            },
            error: function(xhr, status, error) {
                alert(t.failedLoadDomainData);
            }
        });
    });
    
    $('#edit-domain form').on('submit', function(e) {
        e.preventDefault();
        
        const domainId = $('#edit-domain').data('domain-id');
        const categoryId = $('#edit-domain-category').val();
        const notes = $('#edit-domain-notes').val();
        const blockSubdomains = $('#edit-block-subdomains').is(':checked');
        
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
                category_id: categoryId,
                notes: notes,
                block_subdomains: blockSubdomains
            }),
            success: function(response) {
                if (response.success) {
                    domainsTable.ajax.reload();
                    $('#edit-domain').modal('hide');
                    loadCategoriesData();
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = t.failedUpdateDomain;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMessage);
            }
        });
    });
    
    $(document).on('click', '.delete-domain-btn', function() {
        const domainId = $(this).data('id');
        const row = $(this).closest('tr');
        const domain = row.find('td:first span').text();
        
        if (confirm(`${t.confirmDelete} "${domain}" ${t.confirmDeleteSuffix}`)) {
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
                        domainsTable.ajax.reload();
                        loadCategoriesData();
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    alert(t.failedDeleteDomain);
                }
            });
        }
    });
    
    $(document).on('click', '.category-card', function(e) {
        if ($(e.target).hasClass('custom-control-input') || $(e.target).hasClass('custom-control-label')) {
            return;
        }
        
        const categoryName = $(this).find('h4').text();
        const categoryCount = parseInt($(this).find('span').text()) || 0;
        
        $(`.card-title:contains("${t.blockedDomains}")`).html(`${categoryName} ${t.blockedDomains} <span class="text-muted font-small-3">(${categoryCount} ${t.domainCount})</span>`);
        
        domainsTable.search(categoryName).draw();
        
        $('html, body').animate({
            scrollTop: $("#basic-datatable").offset().top - 100
        }, 500);
        
        window.selectedCategory = categoryName;
    });
    
    $('#add-new-domain').on('show.bs.modal', function() {
        if (window.selectedCategory) {
            let categoryValue = getCategoryIdByName(window.selectedCategory);
            if (categoryValue) {
                $('#domain-category').val(categoryValue).trigger('change');
            }
        }
    });

    $(document).on('click', '#view-all-domains', function() {
        $(`.card-title:contains("${t.blockedDomains}")`).html(t.allBlockedDomains);
        domainsTable.search('').draw();
        window.selectedCategory = null;
    });

    $(document).on('click', '#export-all-domains', function() {
        const button = $(this);
        const originalText = button.html();
        
        button.prop('disabled', true).html('<i data-feather="loader" class="mr-25"></i>Exporting...');
        feather.replace();
        
        let exportUrl = '/api/blocked-domains/export?format=txt&active_only=true';
        
        if (window.selectedCategory) {
            const categoryId = getCategoryIdByName(window.selectedCategory);
            if (categoryId) {
                exportUrl += `&category_id=${categoryId}`;
            }
        }
        
        const link = document.createElement('a');
        link.href = exportUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(function() {
            button.prop('disabled', false).html(originalText);
            feather.replace();
        }, 1000);
        
        if (typeof toastr !== 'undefined') {
            toastr.success(t.exportStarted);
        } else {
            alert(t.exportStarted);
        }
    });

    $('.card.cursor-pointer').addClass('category-card');

    $(`.card-title:contains("${t.blockedDomains}")`).after(`
        <div class="mb-2">
            <button class="btn btn-sm btn-outline-primary mr-1" id="view-all-domains">
                <i data-feather="list" class="mr-25"></i>View All Domains
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="export-all-domains">
                <i data-feather="download" class="mr-25"></i>Export All
            </button>
        </div>
    `);

    feather.replace({
        width: 14,
        height: 14
    });
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
