// Captive Portal Designer JavaScript
let token;
let currentDesignId = null;

// Update onboarding timeline when designs are loaded
function updateOnboardingTimeline(hasDesigns) {
    const circle1 = document.getElementById('timeline-circle-1');
    const circle2 = document.getElementById('timeline-circle-2');
    const label2 = document.getElementById('timeline-label-2');
    const sub2 = document.getElementById('timeline-sub-2');
    if (!circle1) return;

    if (hasDesigns) {
        // Step 1 completed: green check
        circle1.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        circle1.style.boxShadow = '0 4px 12px rgba(40, 167, 69, 0.3)';
        circle1.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

        // Step 2 active: purple + clickable
        circle2.style.background = 'linear-gradient(135deg, #7367f0, #9e95f5)';
        circle2.style.color = 'white';
        circle2.style.border = 'none';
        circle2.style.boxShadow = '0 4px 15px rgba(115, 103, 240, 0.4)';
        label2.style.color = '#333';
        sub2.style.color = '#888';

        // Hover effect on step 2
        const step2 = document.getElementById('timeline-step-2');
        step2.style.transition = 'transform 0.2s ease';
        circle2.style.transition = 'box-shadow 0.2s ease, transform 0.2s ease';
        step2.addEventListener('mouseenter', function() {
            step2.style.transform = 'translateY(-4px)';
            circle2.style.boxShadow = '0 8px 25px rgba(115, 103, 240, 0.5)';
            circle2.style.transform = 'scale(1.1)';
        });
        step2.addEventListener('mouseleave', function() {
            step2.style.transform = 'translateY(0)';
            circle2.style.boxShadow = '0 4px 15px rgba(115, 103, 240, 0.4)';
            circle2.style.transform = 'scale(1)';
        });
    }
}

// Translation bundle injected by the blade (lang/{en,fr}/captive_portals.php)
const t = window.APP_I18N.captive_portals;

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
    const welcomeText = $('#welcome-message').val() || t.welcome_default;
    const instructions = $('#login-instructions').val() || t.instructions_default;
    const buttonText = $('#button-text').val() || t.button_default;
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
        $('#preview-terms-container').html(`<small>${t.terms_link_html}</small>`).show();
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
                <span class="sr-only">${t.loading_spinner}</span>
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
                
                $('#portal-name').val(design.name || t.design_default);
                $('#portal-description').val(design.description || '');
                $('#theme-color').val(design.theme_color || '#7367f0');
                $('.color-preview').css('background-color', design.theme_color || '#7367f0');
                $('.color-value').text(design.theme_color || '#7367f0');
                $('#welcome-message').val(design.welcome_message || t.welcome_default);
                $('#login-instructions').val(design.login_instructions || t.instructions_default);
                $('#button-text').val(design.button_text || t.button_default);
                $('#show-terms').prop('checked', design.show_terms === undefined ? true : !!design.show_terms);
                
                $('#terms-of-service').val(design.terms_content || t.terms_default);
                $('#privacy-policy').val(design.privacy_content || t.privacy_default);
                
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
                    $('#gradient-start-value').text(t.none_image_active);
                    $('#gradient-end-value').text(t.none_image_active);
                } else {
                    $('#gradient-start').data('disabled', false);
                    $('#gradient-end').data('disabled', false);
                }
                
                $('#preview-welcome').text(design.welcome_message || t.welcome_default);
                $('#preview-instructions').text(design.login_instructions || t.instructions_default);
                $('#preview-button').text(design.button_text || t.button_default);
                $('#preview-button').css({
                    'background-color': design.theme_color || '#7367f0',
                    'border-color': design.theme_color || '#7367f0'
                });
                
                const showTermsPreview = design.show_terms === undefined ? true : !!design.show_terms;
                if (showTermsPreview) {
                    $('#preview-terms-container').html(`<small>${t.terms_link_html}</small>`).show();
                } else {
                    $('#preview-terms-container').hide();
                }
                
                $('#preview-terms-content').text(design.terms_content || t.terms_default);
                $('#preview-privacy-content').text(design.privacy_content || t.privacy_default);
                
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
                toastr.error(t.error_invalid_response);
            }
            
            $('.loading-overlay').remove();
        },
        error: function(xhr) {
            console.error('Error fetching design details:', xhr.responseText);
            toastr.error(t.error_loading_details);
            $('.loading-overlay').remove();
            $('#captive-portal-designer').hide();
            $('#captive-portal-designs-list').show();
        }
    });
}

function checkUserSubscription() {
    const ctaBannerHtml = `
        <div class="alert alert-primary d-flex align-items-center justify-content-between mb-2" style="border-left: 4px solid #7367f0; background: linear-gradient(135deg, rgba(115,103,240,0.08), rgba(115,103,240,0.02)); border-radius: 8px; padding: 1.25rem 1.5rem;">
            <div class="d-flex align-items-center">
                <div style="background: rgba(115,103,240,0.15); border-radius: 50%; padding: 0.75rem; margin-right: 1rem;">
                    <i data-feather="wifi" style="width: 24px; height: 24px; color: #7367f0;"></i>
                </div>
                <div>
                    <h5 class="mb-0" style="color: #7367f0;">${t.cta_title}</h5>
                    <p class="mb-0 text-muted">${t.cta_text}</p>
                </div>
            </div>
            <a href="/pricing" class="btn btn-primary ml-2 d-flex align-items-center" style="white-space: nowrap; padding: 0.6rem 1.5rem; font-size: 1rem; font-weight: 600; border-radius: 8px; gap: 0.4rem;">
                <i data-feather="shopping-bag" style="width: 16px; height: 16px;"></i> ${t.cta_button}
            </a>
        </div>
    `;

    $.ajax({
        url: '/api/subscription/status',
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            if (!response.has_subscription) {
                $('#device-cta-banner').html(ctaBannerHtml).show();
                if (typeof feather !== 'undefined') feather.replace();
            }
        },
        error: function() {
            // If subscription check fails, show CTA as fallback
            $('#device-cta-banner').html(ctaBannerHtml).show();
            if (typeof feather !== 'undefined') feather.replace();
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
            
            // Update timeline based on whether user has designs
            updateOnboardingTimeline(response.data && response.data.length > 0);

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
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDesign(${design.id})" title="${t.delete_button_title}">
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
                                                <img src="/assets/images/Mr-Wifi.PNG" alt="Mr WiFi" style="max-height: 15px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <h5 class="mb-0">${design.name}</h5>
                                        <small class="text-muted">${t.last_modified}: ${formattedDate}</small>
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
                            <h4 class="mt-2">${t.no_designs}</h4>
                            <p>${t.create_first}</p>
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
                    <div class="alert alert-danger">${t.error_loading}</div>
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
            toastr.success(t.saved_success);
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
                    let errorMessage = t.error_validation;
                    for (const field in responseObj.errors) {
                        errorMessage += `- ${responseObj.errors[field][0]}<br>`;
                    }
                    toastr.error(errorMessage);
                } else {
                    toastr.error(responseObj.message || t.error_saving);
                }
            } catch (e) {
                toastr.error(t.error_saving);
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
    $('#portal-name').val(t.design_default);
    $('#portal-description').val('');
    $('#theme-color').val('#7367f0');
    $('.color-preview').css('background-color', '#7367f0');
    $('.color-value').text('#7367f0');
    $('#welcome-message').val(t.welcome_default);
    $('#login-instructions').val(t.instructions_default);
    $('#button-text').val(t.button_default);
    $('#show-terms').prop('checked', true);
    
    $('#terms-of-service').val(t.terms_default);
    $('#privacy-policy').val(t.privacy_default);
    
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
    
    $('#preview-welcome').text(t.welcome_default);
    $('#preview-instructions').text(t.instructions_default);
    $('#preview-button').text(t.button_default);
    $('#preview-button').css({ 'background-color': '#7367f0', 'border-color': '#7367f0' });
    $('#preview-terms-container').html(`<small>${t.terms_link_html}</small>`).show();
    $('#preview-logo').attr('src', '').hide();
    
    updatePreviewBackground();
}

function deleteDesign(designId) {
    if (!designId) {
        toastr.error(t.invalid_design_id);
        return;
    }
    $('#deleteDesignModal').data('designId', designId);
    $('#deleteDesignModal').modal('show');
}

function showChangeOwnerModal(designId, currentOwnerName, currentOwnerId) {
    $('#changeOwnerModal').data('designId', designId);
    
    $('#changeOwnerText').text(t.change_owner_body_with_owner.replace('{name}', currentOwnerName));
    
    loadUsersForOwnerChange(currentOwnerId);
    $('#changeOwnerModal').modal('show');
}

function loadUsersForOwnerChange(currentOwnerId) {
    const $select = $('#newOwnerSelect');
    $select.html(`<option value="">${t.loading_users}</option>`);
    
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
                    const label = isCurrentOwner ? ` - ${t.current_owner}` : '';
                    
                    $select.append(`
                        <option value="${user.id}" ${selected} ${disabled}>
                            ${user.name} (${user.email})${label}
                        </option>
                    `);
                });
            } else {
                $select.append(`<option value="">${t.no_users_found}</option>`);
            }
        },
        error: function(xhr) {
            console.error('Error loading users:', xhr.responseText);
            $select.html(`<option value="">${t.error_loading_users}</option>`);
            toastr.error(t.failed_load_users);
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
            toastr.error(t.logged_out);
        }
    } catch (e) {
        console.error("Error getting token:", e);
        token = localStorage.getItem('jwt_token');
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const fromRegistration = urlParams.get('from') === 'registration';
    fetchDesigns(fromRegistration);
    checkUserSubscription();

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
        $('#preview-welcome').text($(this).val() || t.welcome_default);
    });

    $('#login-instructions').on('input', function() {
        $('#preview-instructions').text($(this).val() || t.instructions_default);
    });

    $('#button-text').on('input', function() {
        $('#preview-button').text($(this).val() || t.button_default);
    });

    $('#theme-color').on('change', function() {
        const color = $(this).val();
        $('.color-preview').css('background-color', color);
        $('.color-value').text(color);
        $('#preview-button').css({ 'background-color': color, 'border-color': color });
    });

    $('#show-terms').on('change', function() {
        if (this.checked) {
            $('#preview-terms-container').html(`<small>${t.terms_link_html}</small>`).show();
        } else {
            $('#preview-terms-container').hide();
        }
    });
    
    $('#terms-of-service').on('input', function() {
        $('#preview-terms-content').text($(this).val() || t.terms_default);
    });
    
    $('#privacy-policy').on('input', function() {
        $('#preview-privacy-content').text($(this).val() || t.privacy_default);
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
                    $('#gradient-start-value, #gradient-end-value').text(t.none_image_active);
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
            errorMessages.push(t.error_required_name);
            $('#portal-name').addClass('is-invalid');
        } else {
            $('#portal-name').removeClass('is-invalid');
        }
        
        if (!themeColor) {
            hasErrors = true;
            errorMessages.push(t.error_required_theme);
            $('#theme-color').addClass('is-invalid');
        } else {
            $('#theme-color').removeClass('is-invalid');
        }
        
        if (!welcomeMessage) {
            hasErrors = true;
            errorMessages.push(t.error_required_welcome);
            $('#welcome-message').addClass('is-invalid');
        } else {
            $('#welcome-message').removeClass('is-invalid');
        }
        
        if (!buttonText) {
            hasErrors = true;
            errorMessages.push(t.error_required_button);
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
                toastr.success(t.deleted_success);
                fetchDesigns();
            },
            error: function(xhr) {
                try {
                    const responseObj = JSON.parse(xhr.responseText);
                    toastr.error(responseObj.message || t.error_deleting);
                } catch (e) {
                    toastr.error(t.error_deleting);
                }
                designCard.removeClass('opacity-50').find('.position-absolute').remove();
            }
        });
    });
    
    $('#confirmChangeOwnerBtn').on('click', function() {
        const designId = $('#changeOwnerModal').data('designId');
        const newOwnerId = $('#newOwnerSelect').val();
        
        if (!newOwnerId) {
            toastr.error(t.select_new_owner);
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
                toastr.success(t.owner_changed_success);
                $('#changeOwnerModal').modal('hide');
                fetchDesigns();
            },
            error: function(xhr) {
                try {
                    const responseObj = JSON.parse(xhr.responseText);
                    toastr.error(responseObj.message || t.error_changing_owner);
                } catch (e) {
                    toastr.error(t.error_changing_owner);
                }
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
});
