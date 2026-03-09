// Captive Portal Designer JavaScript
let token;
let currentDesignId = null;

// Translations for bilingual support
const TRANSLATIONS = {
    en: {
        welcomeDefault: 'Welcome to our WiFi',
        instructionsDefault: 'Enter your email to connect to our WiFi network',
        buttonDefault: 'Connect to WiFi',
        designDefault: 'New Design',
        termsDefault: 'By accessing this WiFi service, you agree to comply with all applicable laws and the network\'s acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.',
        privacyDefault: 'We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.',
        termsLink: 'By connecting, you agree to our <a href="#" data-toggle="modal" data-target="#previewTermsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#previewPrivacyModal">Privacy Policy</a>.',
        saving: 'Saving...',
        deleting: 'Deleting...',
        changing: 'Changing...',
        loading: 'Loading designs...',
        loadingSpinner: 'Loading...',
        noDesigns: 'No captive portal designs found',
        createFirst: 'Create your first design to get started',
        errorLoading: 'Failed to load designs. Please try again later.',
        savedSuccess: 'Captive portal design saved successfully',
        deletedSuccess: 'Design deleted successfully',
        ownerChangedSuccess: 'Owner changed successfully',
        invalidDesignId: 'Invalid design ID',
        selectNewOwner: 'Please select a new owner',
        loadingUsers: 'Loading users...',
        noUsersFound: 'No users found',
        errorLoadingUsers: 'Error loading users',
        failedLoadUsers: 'Failed to load users list',
        noneImageActive: 'None (Image Active)',
        none: 'None',
        errorRequired: {
            name: 'Portal name is required',
            theme: 'Theme color is required',
            welcome: 'Welcome message is required',
            button: 'Button text is required'
        },
        errorSaving: 'Failed to save design. Please try again.',
        errorDeleting: 'Failed to delete design. Please try again.',
        errorChangingOwner: 'Failed to change owner. Please try again.',
        errorLoadingDetails: 'Failed to load design details. Please try again.',
        errorValidation: 'Please correct the following errors:<br>',
        errorInvalidResponse: 'Could not load design details. Invalid response format.',
        owner: 'Owner',
        creator: 'Creator',
        currentOwner: 'Current Owner',
        lastModified: 'Last modified',
        edit: 'Edit',
        loggedOut: 'You appear to be logged out. Please refresh the page and log in again.',
        ctaTitle: 'Ready to use your captive portal?',
        ctaText: 'Get a MrWiFi device to deploy your custom captive portal on your WiFi network.',
        ctaButton: 'See our offers'
    },
    fr: {
        welcomeDefault: 'Bienvenue sur notre WiFi',
        instructionsDefault: 'Entrez votre adresse e-mail pour vous connecter à notre réseau WiFi',
        buttonDefault: 'Se connecter au WiFi',
        designDefault: 'Nouvelle conception',
        termsDefault: 'En accédant à ce service WiFi, vous acceptez de vous conformer à toutes les lois applicables et à la politique d\'utilisation acceptable du réseau. Nous nous réservons le droit de surveiller le trafic et le contenu accessible via notre réseau, et de résilier l\'accès en cas de violations de ces conditions.',
        privacyDefault: 'Nous collectons des informations limitées lorsque vous utilisez notre service WiFi, y compris les identifiants d\'appareils, les heures de connexion et les données d\'utilisation. Ces informations sont utilisées pour améliorer notre service, résoudre les problèmes techniques et respecter les exigences légales. Nous ne vendons pas vos informations personnelles à des tiers.',
        termsLink: 'En vous connectant, vous acceptez nos <a href="#" data-toggle="modal" data-target="#previewTermsModal">Conditions de service</a> et notre <a href="#" data-toggle="modal" data-target="#previewPrivacyModal">Politique de confidentialité</a>.',
        saving: 'Enregistrement...',
        deleting: 'Suppression...',
        changing: 'Changement...',
        loading: 'Chargement des conceptions...',
        loadingSpinner: 'Chargement...',
        noDesigns: 'Aucune conception de portail captif trouvée',
        createFirst: 'Créez votre première conception pour commencer',
        errorLoading: 'Échec du chargement des conceptions. Veuillez réessayer plus tard.',
        savedSuccess: 'Conception de portail captif enregistrée avec succès',
        deletedSuccess: 'Conception supprimée avec succès',
        ownerChangedSuccess: 'Propriétaire changé avec succès',
        invalidDesignId: 'ID de conception invalide',
        selectNewOwner: 'Veuillez sélectionner un nouveau propriétaire',
        loadingUsers: 'Chargement des utilisateurs...',
        noUsersFound: 'Aucun utilisateur trouvé',
        errorLoadingUsers: 'Erreur lors du chargement des utilisateurs',
        failedLoadUsers: 'Échec du chargement de la liste des utilisateurs',
        noneImageActive: 'Aucun (Image active)',
        none: 'Aucun',
        errorRequired: {
            name: 'Le nom du portail est requis',
            theme: 'La couleur du thème est requise',
            welcome: 'Le message de bienvenue est requis',
            button: 'Le texte du bouton est requis'
        },
        errorSaving: 'Échec de l\'enregistrement de la conception. Veuillez réessayer.',
        errorDeleting: 'Échec de la suppression de la conception. Veuillez réessayer.',
        errorChangingOwner: 'Échec du changement de propriétaire. Veuillez réessayer.',
        errorLoadingDetails: 'Échec du chargement des détails de la conception. Veuillez réessayer.',
        errorValidation: 'Veuillez corriger les erreurs suivantes :<br>',
        errorInvalidResponse: 'Impossible de charger les détails de la conception. Format de réponse invalide.',
        owner: 'Propriétaire',
        creator: 'Créateur',
        currentOwner: 'Propriétaire actuel',
        lastModified: 'Dernière modification',
        edit: 'Modifier',
        loggedOut: 'Vous semblez être déconnecté. Veuillez actualiser la page et vous reconnecter.',
        ctaTitle: 'Prêt à utiliser votre portail captif ?',
        ctaText: 'Procurez-vous un boîtier MrWiFi pour déployer votre portail captif personnalisé sur votre réseau WiFi.',
        ctaButton: 'Voir nos offres'
    }
};

// Get locale from page
const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

function updatePreviewBackground() {
    const startColor = $('#gradient-start').val();
    const endColor = $('#gradient-end').val();
    const backgroundImage = $('#background-preview').attr('src');
    
    const gradientDisabled = $('#gradient-start').data('disabled') === true;
    
    console.log('updatePreviewBackground called:', { startColor, endColor, backgroundImage, gradientDisabled });
    
    const hasStartColor = startColor && startColor.length > 0 && !gradientDisabled;
    const hasEndColor = endColor && endColor.length > 0 && !gradientDisabled;
    const hasBackgroundImage = backgroundImage && backgroundImage !== '';
    
    if (hasStartColor && hasEndColor) {
        const $preview = $('.portal-preview');
        $preview.removeClass('has-background-image');
        $preview[0].style.cssText = '';
        
        const gradientCSS = `linear-gradient(135deg, ${startColor} 0%, ${endColor} 100%)`;
        $preview.addClass('has-gradient');
        $preview[0].style.setProperty('background', gradientCSS, 'important');
        $preview[0].style.setProperty('--gradient-bg', gradientCSS);
    } else if (hasStartColor || hasEndColor) {
        const color = startColor || endColor;
        const $preview = $('.portal-preview');
        $preview.removeClass('has-background-image').addClass('has-gradient');
        $preview[0].style.cssText = '';
        $preview[0].style.setProperty('background', color, 'important');
        $preview[0].style.setProperty('--gradient-bg', color);
    } else if (hasBackgroundImage) {
        const $preview = $('.portal-preview');
        $preview.removeClass('has-gradient').addClass('has-background-image');
        $preview[0].style.cssText = '';
        $preview[0].style.backgroundImage = `url(${backgroundImage})`;
        $preview[0].style.backgroundSize = 'cover';
        $preview[0].style.backgroundPosition = 'center';
        $preview[0].style.backgroundRepeat = 'no-repeat';
        $preview[0].style.backgroundColor = '#fff';
    } else {
        $('.portal-preview').removeClass('has-background-image has-gradient').css({
            'background': '#fff',
            'background-image': 'none'
        });
    }
}

function initializePreview() {
    const welcomeText = $('#welcome-message').val() || t.welcomeDefault;
    const instructions = $('#login-instructions').val() || t.instructionsDefault;
    const buttonText = $('#button-text').val() || t.buttonDefault;
    const themeColor = $('#theme-color').val() || '#7367f0';
    const showTerms = $('#show-terms').is(':checked');
    
    $('#preview-welcome').text(welcomeText);
    $('#preview-instructions').text(instructions);
    $('#preview-button').text(buttonText);
    $('#preview-button').css({
        'background-color': themeColor,
        'border-color': themeColor
    });
    
    if (showTerms) {
        $('#preview-terms-container').html(`<small>${t.termsLink}</small>`).show();
    } else {
        $('#preview-terms-container').hide();
    }
    
    const logoPreview = $('#location-logo-preview');
    if (logoPreview.attr('src') && logoPreview.css('display') !== 'none') {
        $('#preview-logo').attr('src', logoPreview.attr('src')).show();
    } else {
        $('#preview-logo').hide();
    }
    
    const bgPreview = $('#background-preview');
    if (bgPreview.attr('src') && bgPreview.css('display') !== 'none') {
        $('.portal-preview').css({
            'background-image': `url(${bgPreview.attr('src')})`,
            'background-size': 'cover',
            'background-position': 'center',
            'background-repeat': 'no-repeat'
        });
    }
    
    if ($('#previewTermsModal').parent()[0] !== document.body) {
        $('#previewTermsModal, #previewPrivacyModal').appendTo('body');
    }
}

function fetchDesignDetails(designId) {
    console.log("Fetching design details for ID:", designId);
    
    $('#captive-portal-designer').prepend(
        `<div class="loading-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">${t.loadingSpinner}</span>
            </div>
        </div>`
    );
    
    $('#captive-portal-designs-list').hide();
    $('#captive-portal-designer').show();
    
    $.ajax({
        url: `/api/captive-portal-designs/${designId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Design details received:', response);
            
            currentDesignId = designId;
            
            if (response.success && response.data) {
                const design = response.data;
                
                $('#portal-name').val(design.name || t.designDefault);
                $('#portal-description').val(design.description || '');
                $('#theme-color').val(design.theme_color || '#7367f0');
                $('.color-preview').css('background-color', design.theme_color || '#7367f0');
                $('.color-value').text(design.theme_color || '#7367f0');
                $('#welcome-message').val(design.welcome_message || t.welcomeDefault);
                $('#login-instructions').val(design.login_instructions || t.instructionsDefault);
                $('#button-text').val(design.button_text || t.buttonDefault);
                $('#show-terms').prop('checked', design.show_terms === undefined ? true : !!design.show_terms);
                
                $('#terms-of-service').val(design.terms_content || t.termsDefault);
                $('#privacy-policy').val(design.privacy_content || t.privacyDefault);
                
                const hasBackgroundImage = design.background_image_path || design.background_image_url;
                
                if (design.background_color_gradient_start) {
                    $('#gradient-start').val(design.background_color_gradient_start);
                    $('#gradient-start-preview').css('background-color', design.background_color_gradient_start);
                    $('#gradient-start-value').text(design.background_color_gradient_start);
                } else {
                    $('#gradient-start').val('');
                    $('#gradient-start-preview').css('background-color', 'transparent');
                    $('#gradient-start-value').text(t.none);
                }
                
                if (design.background_color_gradient_end) {
                    $('#gradient-end').val(design.background_color_gradient_end);
                    $('#gradient-end-preview').css('background-color', design.background_color_gradient_end);
                    $('#gradient-end-value').text(design.background_color_gradient_end);
                } else {
                    $('#gradient-end').val('');
                    $('#gradient-end-preview').css('background-color', 'transparent');
                    $('#gradient-end-value').text(t.none);
                }
                
                if (hasBackgroundImage) {
                    $('#gradient-start').data('disabled', true);
                    $('#gradient-end').data('disabled', true);
                    $('#gradient-start-value').text(t.noneImageActive);
                    $('#gradient-end-value').text(t.noneImageActive);
                } else {
                    $('#gradient-start').data('disabled', false);
                    $('#gradient-end').data('disabled', false);
                }
                
                $('#preview-welcome').text(design.welcome_message || t.welcomeDefault);
                $('#preview-instructions').text(design.login_instructions || t.instructionsDefault);
                $('#preview-button').text(design.button_text || t.buttonDefault);
                $('#preview-button').css({
                    'background-color': design.theme_color || '#7367f0',
                    'border-color': design.theme_color || '#7367f0'
                });
                
                const showTermsPreview = design.show_terms === undefined ? true : !!design.show_terms;
                if (showTermsPreview) {
                    $('#preview-terms-container').html(`<small>${t.termsLink}</small>`).show();
                } else {
                    $('#preview-terms-container').hide();
                }
                
                $('#preview-terms-content').text(design.terms_content || t.termsDefault);
                $('#preview-privacy-content').text(design.privacy_content || t.privacyDefault);
                
                if (design.location_logo_url) {
                    $('#location-logo-preview').attr('src', design.location_logo_url).show();
                    $('#preview-logo').attr('src', design.location_logo_url).show();
                } else if (design.location_logo_path) {
                    const logoUrl = `/storage/${design.location_logo_path}`;
                    $('#location-logo-preview').attr('src', logoUrl).show();
                    $('#preview-logo').attr('src', logoUrl).show();
                }
                
                if (design.background_image_path) {
                    const bgUrl = `/storage/${design.background_image_path}`;
                    $('#background-preview').attr('src', bgUrl).show();
                }
                
                updatePreviewBackground();
            } else {
                toastr.error(t.errorInvalidResponse);
            }
            
            $('.loading-overlay').remove();
        },
        error: function(xhr) {
            console.error('Error fetching design details:', xhr.responseText);
            toastr.error(t.errorLoadingDetails);
            $('.loading-overlay').remove();
            $('#captive-portal-designer').hide();
            $('#captive-portal-designs-list').show();
        }
    });
}

function checkUserDevices() {
    $.ajax({
        url: '/api/devices',
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            const devices = response.data || response.devices || response;
            const hasDevices = Array.isArray(devices) && devices.length > 0;
            if (!hasDevices) {
                $('#device-cta-banner').html(`
                    <div class="alert alert-primary d-flex align-items-center justify-content-between mb-2" style="border-left: 4px solid #7367f0; background: linear-gradient(135deg, rgba(115,103,240,0.08), rgba(115,103,240,0.02)); border-radius: 8px; padding: 1.25rem 1.5rem;">
                        <div class="d-flex align-items-center">
                            <div style="background: rgba(115,103,240,0.15); border-radius: 50%; padding: 0.75rem; margin-right: 1rem;">
                                <i data-feather="wifi" style="width: 24px; height: 24px; color: #7367f0;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="color: #7367f0;">${t.ctaTitle}</h5>
                                <p class="mb-0 text-muted">${t.ctaText}</p>
                            </div>
                        </div>
                        <a href="/pricing" class="btn btn-primary btn-sm ml-2" style="white-space: nowrap;">
                            <i data-feather="shopping-bag" style="width: 14px; height: 14px;"></i> ${t.ctaButton}
                        </a>
                    </div>
                `).show();
                if (typeof feather !== 'undefined') feather.replace();
            }
        },
        error: function() {
            // Silently fail - don't block the page if device check fails
        }
    });
}

function fetchDesigns(openFirstDesign = false) {
    $('#portal-designs-container').html(
        `<div class="col-12 text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">${t.loading}</span>
            </div>
        </div>`
    );
    
    $.ajax({
        url: '/api/captive-portal-designs',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            $('#portal-designs-container').empty();
            
            if (response.data && response.data.length > 0) {
                const isAdmin = response.is_admin || false;
                
                if (openFirstDesign) {
                    const firstDesignId = response.data[0].id;
                    const newUrl = window.location.pathname + window.location.hash;
                    window.history.replaceState({}, document.title, newUrl);
                    fetchDesignDetails(firstDesignId);
                    return;
                }
                
                response.data.forEach(function(design) {
                    const bgColorClass = getRandomBgColorClass();
                    const formattedDate = new Date(design.updated_at).toISOString().split('T')[0];
                    
                    let ownerInfo = '';
                    if (isAdmin && design.owner_name) {
                        ownerInfo = `<small class="text-info d-block">${t.owner}: ${design.owner_name}</small>`;
                        if (design.creator_name && design.creator_name !== design.owner_name) {
                            ownerInfo += `<small class="text-muted d-block">${t.creator}: ${design.creator_name}</small>`;
                        }
                    }
                    
                    let actionButtons = '';
                    if (isAdmin) {
                        actionButtons += `
                            <button class="btn btn-sm btn-outline-info" onclick="showChangeOwnerModal(${design.id}, '${design.owner_name || design.creator_name}', ${design.current_owner_id || design.user_id})" title="${t.owner}">
                                <i data-feather="user-check"></i>
                            </button>
                        `;
                    }
                    actionButtons += `
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDesign(${design.id})" title="Delete">
                            <i data-feather="trash-2"></i>
                        </button>
                    `;
                    
                    const logoHtml = design.location_logo_url ? 
                        `<img src="${design.location_logo_url}" alt="${design.name}" style="max-height: 20px;">` : 
                        (design.location_logo_path ? 
                        `<img src="/storage/${design.location_logo_path}" alt="${design.name}" style="max-height: 20px;">` :
                        '<span>Logo</span>');
                    
                    const designCard = `
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="card design-card">
                                <div class="card-body p-2">
                                    <div class="design-preview ${bgColorClass}">
                                        <div class="preview-content">
                                            <div class="location-logo-mini">${logoHtml}</div>
                                            <div class="login-area-mini">${design.name}</div>
                                            <div class="brand-logo-mini">
                                                <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Mr WiFi" style="max-height: 15px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <h5 class="mb-0">${design.name}</h5>
                                        <small class="text-muted">${t.lastModified}: ${formattedDate}</small>
                                        ${ownerInfo}
                                    </div>
                                    <div class="design-actions mt-1 d-flex justify-content-between align-items-center">
                                        <button class="btn btn-sm btn-outline-primary edit-design" data-id="${design.id}">
                                            <i data-feather="edit-2" class="mr-25"></i> ${t.edit}
                                        </button>
                                        <div class="btn-group">${actionButtons}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#portal-designs-container').append(designCard);
                });
            } else {
                $('#portal-designs-container').html(
                    `<div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i data-feather="layout" style="height: 64px; width: 64px; color: #d0d0d0;"></i>
                            <h4 class="mt-2">${t.noDesigns}</h4>
                            <p>${t.createFirst}</p>
                        </div>
                    </div>`
                );
            }
            
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        },
        error: function(xhr) {
            console.error('Error fetching designs:', xhr.responseText);
            $('#portal-designs-container').html(
                `<div class="col-12 text-center py-3">
                    <div class="alert alert-danger">${t.errorLoading}</div>
                </div>`
            );
        }
    });
}

function saveDesign(formData, url) {
    const saveBtn = $('#save-design');
    const originalText = saveBtn.html();
    saveBtn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.saving}`);
    saveBtn.attr('disabled', true);
    
    const isUpdate = !url.includes('/create');
    if (isUpdate) {
        formData.append('_method', 'PUT');
    }
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            toastr.success(t.savedSuccess);
            $('#captive-portal-designer').hide();
            $('#captive-portal-designs-list').show();
            fetchDesigns();
            resetDesignForm();
            $('.portal-preview').css({
                'background-image': 'none',
                'background-color': '#fff'
            });
        },
        error: function(xhr) {
            console.error('Error saving design:', xhr.responseText);
            
            try {
                const responseObj = JSON.parse(xhr.responseText);
                if (xhr.status === 422 && responseObj.errors) {
                    let errorMessage = t.errorValidation;
                    for (const field in responseObj.errors) {
                        errorMessage += `- ${responseObj.errors[field][0]}<br>`;
                    }
                    toastr.error(errorMessage);
                } else {
                    toastr.error(responseObj.message || t.errorSaving);
                }
            } catch (e) {
                toastr.error(t.errorSaving);
            }
        },
        complete: function() {
            saveBtn.html(originalText);
            saveBtn.attr('disabled', false);
        }
    });
}

function getRandomBgColorClass() {
    const colorClasses = ['bg-light-primary', 'bg-light-success', 'bg-light-danger', 'bg-light-warning', 'bg-light-info'];
    return colorClasses[Math.floor(Math.random() * colorClasses.length)];
}

function resetDesignForm() {
    $('#portal-name').val(t.designDefault);
    $('#portal-description').val('');
    $('#theme-color').val('#7367f0');
    $('.color-preview').css('background-color', '#7367f0');
    $('.color-value').text('#7367f0');
    $('#welcome-message').val(t.welcomeDefault);
    $('#login-instructions').val(t.instructionsDefault);
    $('#button-text').val(t.buttonDefault);
    $('#show-terms').prop('checked', true);
    
    $('#terms-of-service').val(t.termsDefault);
    $('#privacy-policy').val(t.privacyDefault);
    
    $('#location-logo-file').val('');
    $('#background-file').val('');
    $('#location-logo-preview').attr('src', '').hide();
    $('#background-preview').attr('src', '').hide();
    
    $('#gradient-start').val('');
    $('#gradient-end').val('');
    $('#gradient-start-preview').css('background-color', 'transparent');
    $('#gradient-end-preview').css('background-color', 'transparent');
    $('#gradient-start-value').text(t.none);
    $('#gradient-end-value').text(t.none);
    
    $('.portal-preview').css({
        'background-image': 'none',
        'background-color': '#fff',
        'background': '#fff'
    });
    
    $('#preview-welcome').text(t.welcomeDefault);
    $('#preview-instructions').text(t.instructionsDefault);
    $('#preview-button').text(t.buttonDefault);
    $('#preview-button').css({ 'background-color': '#7367f0', 'border-color': '#7367f0' });
    $('#preview-terms-container').html(`<small>${t.termsLink}</small>`).show();
    $('#preview-logo').attr('src', '').hide();
    
    updatePreviewBackground();
}

function deleteDesign(designId) {
    if (!designId) {
        toastr.error(t.invalidDesignId);
        return;
    }
    $('#deleteDesignModal').data('designId', designId);
    $('#deleteDesignModal').modal('show');
}

function showChangeOwnerModal(designId, currentOwnerName, currentOwnerId) {
    $('#changeOwnerModal').data('designId', designId);
    
    const ownerText = PAGE_LOCALE === 'fr' 
        ? `Sélectionnez un nouveau propriétaire pour cette conception de portail captif (actuellement détenue par ${currentOwnerName}) :`
        : `Select a new owner for this captive portal design (currently owned by ${currentOwnerName}):`;
    $('#changeOwnerText').text(ownerText);
    
    loadUsersForOwnerChange(currentOwnerId);
    $('#changeOwnerModal').modal('show');
}

function loadUsersForOwnerChange(currentOwnerId) {
    const $select = $('#newOwnerSelect');
    $select.html(`<option value="">${t.loadingUsers}</option>`);
    
    $.ajax({
        url: '/api/accounts/users',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            $select.empty();
            
            if (response.users && response.users.length > 0) {
                response.users.forEach(function(user) {
                    const isCurrentOwner = user.id == currentOwnerId;
                    const selected = isCurrentOwner ? 'selected' : '';
                    const disabled = isCurrentOwner ? 'disabled' : '';
                    const label = isCurrentOwner ? ` - ${t.currentOwner}` : '';
                    
                    $select.append(`
                        <option value="${user.id}" ${selected} ${disabled}>
                            ${user.name} (${user.email})${label}
                        </option>
                    `);
                });
            } else {
                $select.append(`<option value="">${t.noUsersFound}</option>`);
            }
        },
        error: function(xhr) {
            console.error('Error loading users:', xhr.responseText);
            $select.html(`<option value="">${t.errorLoadingUsers}</option>`);
            toastr.error(t.failedLoadUsers);
        }
    });
}

// Initialize on document ready
$(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    try {
        token = UserManager.getToken();
        if (!token) {
            token = localStorage.getItem('jwt_token');
        }
        if (!token) {
            toastr.error(t.loggedOut);
        }
    } catch (e) {
        console.error("Error getting token:", e);
        token = localStorage.getItem('jwt_token');
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const fromRegistration = urlParams.get('from') === 'registration';
    fetchDesigns(fromRegistration);
    checkUserDevices();

    initializePreview();
    updatePreviewBackground();
    
    // Event handlers
    $(document).on('click', '.preview-terms a[data-toggle="modal"]', function(e) {
        e.preventDefault();
        $($(this).data('target')).modal('show');
    });
    
    $('[data-action="expand"]').on('click', function(e) {
        e.preventDefault();
        const $previewCard = $(this).closest('.card');
        
        if ($previewCard.hasClass('card-fullscreen')) {
            $previewCard.removeClass('card-fullscreen');
            $(this).find('i').replaceWith(feather.icons['maximize'].toSvg());
        } else {
            $previewCard.addClass('card-fullscreen');
            $(this).find('i').replaceWith(feather.icons['minimize'].toSvg());
            $('#previewTermsModal, #previewPrivacyModal').appendTo('body');
        }
        feather.replace();
    });

    $('#welcome-message').on('input', function() {
        $('#preview-welcome').text($(this).val() || t.welcomeDefault);
    });

    $('#login-instructions').on('input', function() {
        $('#preview-instructions').text($(this).val() || t.instructionsDefault);
    });

    $('#button-text').on('input', function() {
        $('#preview-button').text($(this).val() || t.buttonDefault);
    });

    $('#theme-color').on('change', function() {
        const color = $(this).val();
        $('.color-preview').css('background-color', color);
        $('.color-value').text(color);
        $('#preview-button').css({ 'background-color': color, 'border-color': color });
    });

    $('#show-terms').on('change', function() {
        if (this.checked) {
            $('#preview-terms-container').html(`<small>${t.termsLink}</small>`).show();
        } else {
            $('#preview-terms-container').hide();
        }
    });
    
    $('#terms-of-service').on('input', function() {
        $('#preview-terms-content').text($(this).val() || t.termsDefault);
    });
    
    $('#privacy-policy').on('input', function() {
        $('#preview-privacy-content').text($(this).val() || t.privacyDefault);
    });

    $('#gradient-start, #gradient-end').on('change', function() {
        const isStart = $(this).attr('id') === 'gradient-start';
        const color = $(this).val();
        $('#gradient-start, #gradient-end').data('disabled', false);
        $(`#${isStart ? 'gradient-start' : 'gradient-end'}-preview`).css('background-color', color);
        $(`#${isStart ? 'gradient-start' : 'gradient-end'}-value`).text(color);
        updatePreviewBackground();
    });

    $('#clear-gradient').on('click', function() {
        $('#gradient-start, #gradient-end').data('disabled', true).val('');
        $('#gradient-start-preview, #gradient-end-preview').css('background-color', 'transparent');
        $('#gradient-start-value, #gradient-end-value').text(t.none);
        updatePreviewBackground();
    });

    $('#preset-gradient-1').on('click', function() {
        $('#gradient-start, #gradient-end').data('disabled', false);
        $('#gradient-start').val('#667eea').trigger('change');
        $('#gradient-end').val('#764ba2').trigger('change');
    });

    $('#preset-gradient-2').on('click', function() {
        $('#gradient-start, #gradient-end').data('disabled', false);
        $('#gradient-start').val('#f093fb').trigger('change');
        $('#gradient-end').val('#f5576c').trigger('change');
    });

    function readURL(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`#${previewId}`);
                preview.attr('src', e.target.result).show();
                
                if (previewId === 'location-logo-preview') {
                    $('#preview-logo').attr('src', e.target.result).show();
                } else if (previewId === 'background-preview') {
                    $('#gradient-start, #gradient-end').data('disabled', true);
                    $('#gradient-start-preview, #gradient-end-preview').css('background-color', 'transparent');
                    $('#gradient-start-value, #gradient-end-value').text(t.noneImageActive);
                    setTimeout(() => updatePreviewBackground(), 50);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('location-logo-upload').addEventListener('click', () => document.getElementById('location-logo-file').click());
    document.getElementById('background-upload').addEventListener('click', () => document.getElementById('background-file').click());
    
    $('#location-logo-file').on('change', function() { readURL(this, 'location-logo-preview'); });
    $('#background-file').on('change', function() { readURL(this, 'background-preview'); });

    $(document).on('click', '.edit-design', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fetchDesignDetails($(this).data('id'));
    });

    $('#back-to-list').on('click', function() {
        $('#captive-portal-designer').hide();
        $('#captive-portal-designs-list').show();
        resetDesignForm();
        currentDesignId = null;
    });

    $('#create-new-design').on('click', function() {
        $('#captive-portal-designs-list').hide();
        $('#captive-portal-designer').show();
        currentDesignId = null;
        resetDesignForm();
        if (typeof feather !== 'undefined') feather.replace();
    });
    
    $(document).on('click', '#save-design', function() {
        const name = $('#portal-name').val().trim();
        const themeColor = $('#theme-color').val().trim();
        const welcomeMessage = $('#welcome-message').val().trim();
        const buttonText = $('#button-text').val().trim();
        
        let hasErrors = false;
        let errorMessages = [];
        
        if (!name) {
            hasErrors = true;
            errorMessages.push(t.errorRequired.name);
            $('#portal-name').addClass('is-invalid');
        } else {
            $('#portal-name').removeClass('is-invalid');
        }
        
        if (!themeColor) {
            hasErrors = true;
            errorMessages.push(t.errorRequired.theme);
            $('#theme-color').addClass('is-invalid');
        } else {
            $('#theme-color').removeClass('is-invalid');
        }
        
        if (!welcomeMessage) {
            hasErrors = true;
            errorMessages.push(t.errorRequired.welcome);
            $('#welcome-message').addClass('is-invalid');
        } else {
            $('#welcome-message').removeClass('is-invalid');
        }
        
        if (!buttonText) {
            hasErrors = true;
            errorMessages.push(t.errorRequired.button);
            $('#button-text').addClass('is-invalid');
        } else {
            $('#button-text').removeClass('is-invalid');
        }
        
        if (hasErrors) {
            toastr.error(errorMessages.join('<br>'));
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('description', $('#portal-description').val());
        formData.append('theme_color', themeColor);
        formData.append('welcome_message', welcomeMessage);
        formData.append('login_instructions', $('#login-instructions').val());
        formData.append('button_text', buttonText);
        formData.append('show_terms', $('#show-terms').is(':checked') ? 1 : 0);
        formData.append('terms_content', $('#terms-of-service').val());
        formData.append('privacy_content', $('#privacy-policy').val());
        formData.append('background_color_gradient_start', $('#gradient-start').val() || '');
        formData.append('background_color_gradient_end', $('#gradient-end').val() || '');
        
        if ($('#location-logo-file')[0].files[0]) {
            formData.append('location_logo', $('#location-logo-file')[0].files[0]);
        }
        if ($('#background-file')[0].files[0]) {
            formData.append('background_image', $('#background-file')[0].files[0]);
        }
        
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        const url = currentDesignId ? `/api/captive-portal-designs/${currentDesignId}` : '/api/captive-portal-designs/create';
        saveDesign(formData, url);
    });

    function setupDragAndDrop(dropAreaId, fileInputId) {
        const dropArea = document.getElementById(dropAreaId);
        const fileInput = document.getElementById(fileInputId);
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false);
        });
        
        dropArea.addEventListener('drop', function(e) {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                $(fileInput).trigger('change');
            }
        }, false);
    }
    
    setupDragAndDrop('location-logo-upload', 'location-logo-file');
    setupDragAndDrop('background-upload', 'background-file');
    
    $('#confirmDeleteBtn').on('click', function() {
        const designId = $('#deleteDesignModal').data('designId');
        $('#deleteDesignModal').modal('hide');
        
        const designCard = $(`.edit-design[data-id="${designId}"]`).closest('.design-card');
        designCard.addClass('opacity-50').append(`
            <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="top: 0; left: 0; background: rgba(255,255,255,0.7); z-index: 5;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">${t.deleting}</span>
                </div>
            </div>
        `);

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: `/api/captive-portal-designs/${designId}`,
            method: 'DELETE',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                toastr.success(t.deletedSuccess);
                fetchDesigns();
            },
            error: function(xhr) {
                try {
                    const responseObj = JSON.parse(xhr.responseText);
                    toastr.error(responseObj.message || t.errorDeleting);
                } catch (e) {
                    toastr.error(t.errorDeleting);
                }
                designCard.removeClass('opacity-50').find('.position-absolute').remove();
            }
        });
    });
    
    $('#confirmChangeOwnerBtn').on('click', function() {
        const designId = $('#changeOwnerModal').data('designId');
        const newOwnerId = $('#newOwnerSelect').val();
        
        if (!newOwnerId) {
            toastr.error(t.selectNewOwner);
            return;
        }
        
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text(t.changing).prop('disabled', true);
        
        $.ajax({
            url: `/api/captive-portal-designs/${designId}/change-owner`,
            method: 'POST',
            data: {
                owner_id: newOwnerId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                toastr.success(t.ownerChangedSuccess);
                $('#changeOwnerModal').modal('hide');
                fetchDesigns();
            },
            error: function(xhr) {
                try {
                    const responseObj = JSON.parse(xhr.responseText);
                    toastr.error(responseObj.message || t.errorChangingOwner);
                } catch (e) {
                    toastr.error(t.errorChangingOwner);
                }
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
});
