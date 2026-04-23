// Captive Portal Designer JavaScript
let token;
let currentDesignId = null;

// Translation bundle injected by the blade (lang/{en,fr}/captive_portals.php)
const t = window.APP_I18N.captive_portals;

function escapeHtml(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// URL sync: use clean /{id} path so breadcrumb and refresh work correctly.
const _captiveBase = window.CAPTIVE_BASE_URL || '/en/captive-portals';

function setDesignUrl(designId) {
    window.history.pushState({ designId }, '', _captiveBase + '/' + designId);
}
function clearDesignUrl() {
    window.history.pushState({}, '', _captiveBase);
}

function setBreadcrumbDesign(name) {
    const $portals = $('#bc-portals');
    const $design  = $('#bc-design');
    if (!$portals.length) return;
    if (!$portals.find('a').length) {
        // currently plain text — convert to link
        $portals.html('<a href="' + _captiveBase + '">' + $portals.text() + '</a>');
    }
    $portals.removeClass('active');
    if ($design.length) {
        $design.text(name);
    } else {
        $portals.after('<li class="breadcrumb-item active" id="bc-design">' + escapeHtml(name) + '</li>');
    }
}
function clearBreadcrumbDesign() {
    const $portals = $('#bc-portals');
    const $design  = $('#bc-design');
    $design.remove();
    // restore plain text (no link)
    $portals.addClass('active').text($portals.find('a').text() || $portals.text());
}

// Reset designer tabs to "General" active (called when opening designer)
function resetDesignerTabs() {
    const $designer = $('#captive-portal-designer');
    $designer.find('.mw-tab').removeClass('active');
    $designer.find('.mw-tab[data-tab="general"]').addClass('active');
    $designer.find('.mw-panel').removeClass('active');
    $('#general').addClass('active');
}

function updateGradientBar() {
    const disabled = $('#gradient-start').data('disabled') === true;
    const start = $('#gradient-start').val();
    const end   = $('#gradient-end').val();
    const $bar  = $('#gradient-preview-bar');
    if (disabled || !start || !end) {
        $bar.addClass('is-disabled').css('background', '');
    } else {
        $bar.removeClass('is-disabled').css('background', `linear-gradient(135deg, ${start} 0%, ${end} 100%)`);
    }
}

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

    setDesignUrl(designId);
    resetDesignerTabs();

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
            $('#open-full-preview').show();

            if (response.success && response.data) {
                const design = response.data;
                setBreadcrumbDesign(design.name || t.design_default);

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
                    $('#gradient-start-value').text(design.background_color_gradient_start);
                } else {
                    $('#gradient-start').val('');
                    $('#gradient-start-value').text(t.none);
                }

                if (design.background_color_gradient_end) {
                    $('#gradient-end').val(design.background_color_gradient_end);
                    $('#gradient-end-value').text(design.background_color_gradient_end);
                } else {
                    $('#gradient-end').val('');
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
                updateGradientBar();

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
            clearDesignUrl();
            $('.loading-overlay').remove();
            $('#captive-portal-designer').hide();
            $('#captive-portal-designs-list').show();
        }
    });
}

function fetchDesigns(openFirstDesign = false) {
    $('#portal-designs-container').html(`
        <tr class="cp-empty-row">
            <td colspan="4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">${t.loading}</span>
                </div>
            </td>
        </tr>
    `);
    
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
                    const formattedDate = new Date(design.updated_at).toISOString().split('T')[0];
                    const themeColor = (design.theme_color && /^#[0-9a-fA-F]{6}$/.test(design.theme_color)) ? design.theme_color : '';
                    const swatchStyle = themeColor ? ` style="background:${themeColor}"` : '';

                    let subLine = '';
                    if (isAdmin && design.owner_name) {
                        subLine = `${t.owner}: ${escapeHtml(design.owner_name)}`;
                        if (design.creator_name && design.creator_name !== design.owner_name) {
                            subLine += ` · ${t.creator}: ${escapeHtml(design.creator_name)}`;
                        }
                    }

                    const searchKey = (design.name + ' ' + (design.owner_name || '') + ' ' + (design.creator_name || '')).toLowerCase();

                    let menuItems = `
                        <button type="button" class="cp-menu-item" data-action="edit" data-id="${design.id}">
                            <i data-feather="edit-2"></i>${t.edit}
                        </button>`;
                    if (isAdmin) {
                        const ownerArg = (design.owner_name || design.creator_name || '').replace(/'/g, "\\'");
                        const ownerId = design.current_owner_id || design.user_id;
                        menuItems += `
                        <button type="button" class="cp-menu-item" data-action="change-owner" data-id="${design.id}" data-owner-name="${escapeHtml(ownerArg)}" data-owner-id="${ownerId}">
                            <i data-feather="user-check"></i>${t.change_owner}
                        </button>`;
                    }
                    menuItems += `
                        <button type="button" class="cp-menu-item cp-menu-item-danger" data-action="delete" data-id="${design.id}">
                            <i data-feather="trash-2"></i>${t.delete_button_title}
                        </button>`;

                    const row = `
                        <tr class="cp-row" data-id="${design.id}" data-search="${escapeHtml(searchKey)}">
                            <td class="cp-col-preview">
                                <div class="cp-preview-swatch"${swatchStyle}><i data-feather="wifi"></i></div>
                            </td>
                            <td>
                                <div class="cp-name-main">${escapeHtml(design.name)}</div>
                                ${subLine ? `<div class="cp-name-sub">${subLine}</div>` : ''}
                            </td>
                            <td class="cp-col-modified">${formattedDate}</td>
                            <td class="cp-col-actions">
                                <div class="cp-kebab-wrap">
                                    <button type="button" class="cp-kebab-btn" data-kebab="${design.id}" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="cp-menu" data-menu="${design.id}">
                                        ${menuItems}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#portal-designs-container').append(row);
                });
            } else {
                $('#portal-designs-container').html(`
                    <tr class="cp-empty-row">
                        <td colspan="4">
                            <i data-feather="layout"></i>
                            <h5 class="mb-1">${t.no_designs}</h5>
                            <p class="mb-0">${t.create_first}</p>
                        </td>
                    </tr>
                `);
            }
            
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        },
        error: function(xhr) {
            console.error('Error fetching designs:', xhr.responseText);
            $('#portal-designs-container').html(`
                <tr class="cp-empty-row">
                    <td colspan="4"><div class="alert alert-danger mb-0">${t.error_loading}</div></td>
                </tr>
            `);
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
            clearDesignUrl();
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
    
    // Soft brand-indigo wash as default gradient (indigo-50 → indigo-200).
    // Black empty-state is a `<input type="color">` quirk — browser coerces
    // empty values to #000000; an explicit brand default avoids that pitfall
    // and matches app palette.
    const defaultGradStart = '#EEF2FF';
    const defaultGradEnd   = '#C7D2FE';
    $('#gradient-start').val(defaultGradStart).data('disabled', false);
    $('#gradient-end').val(defaultGradEnd).data('disabled', false);
    $('#gradient-start-value').text(defaultGradStart);
    $('#gradient-end-value').text(defaultGradEnd);
    updateGradientBar();
    
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

    // Server-injected edit ID: /{id} route sets window.CAPTIVE_EDIT_ID.
    if (window.CAPTIVE_EDIT_ID) {
        fetchDesignDetails(window.CAPTIVE_EDIT_ID);
    }

    // Back/forward button: if state has no designId, return to list view.
    window.addEventListener('popstate', function(e) {
        if (!e.state || !e.state.designId) {
            clearBreadcrumbDesign();
            $('#captive-portal-designer').hide();
            $('#captive-portal-designs-list').show();
            $('#open-full-preview').hide();
            resetDesignForm();
            currentDesignId = null;
        }
    });

    initializePreview();
    updatePreviewBackground();
    
    // Event handlers
    $(document).on('click', '.preview-terms a[data-toggle="modal"]', function(e) {
        e.preventDefault();
        $($(this).data('target')).modal('show');
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
        $(`#${isStart ? 'gradient-start' : 'gradient-end'}-value`).text(color);
        updatePreviewBackground();
        updateGradientBar();
    });

    $('#clear-gradient').on('click', function() {
        $('#gradient-start, #gradient-end').data('disabled', true).val('');
        $('#gradient-start-value, #gradient-end-value').text(t.none);
        updatePreviewBackground();
        updateGradientBar();
    });

    $(document).on('click', '.cp-gradient-preset', function() {
        const start = $(this).data('start');
        const end   = $(this).data('end');
        $('#gradient-start, #gradient-end').data('disabled', false);
        $('#gradient-start').val(start).trigger('change');
        $('#gradient-end').val(end).trigger('change');
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
                    $('#gradient-start-value, #gradient-end-value').text(t.none_image_active);
                    setTimeout(() => { updatePreviewBackground(); updateGradientBar(); }, 50);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('location-logo-upload').addEventListener('click', () => document.getElementById('location-logo-file').click());
    document.getElementById('background-upload').addEventListener('click', () => document.getElementById('background-file').click());
    
    $('#location-logo-file').on('change', function() { readURL(this, 'location-logo-preview'); });
    $('#background-file').on('change', function() { readURL(this, 'background-preview'); });

    // Row click → open editor (excluding kebab/menu clicks)
    $(document).on('click', 'tr.cp-row', function(e) {
        if ($(e.target).closest('.cp-kebab-wrap').length) return;
        fetchDesignDetails($(this).data('id'));
    });

    // Designer tab switching (.mw-tab / .mw-panel pattern)
    $(document).on('click', '#captive-portal-designer .mw-tab', function() {
        const key = $(this).data('tab');
        $('#captive-portal-designer .mw-tab').removeClass('active');
        $(this).addClass('active');
        $('#captive-portal-designer .mw-panel').removeClass('active');
        $('#' + key).addClass('active');
    });

    // Kebab toggle
    $(document).on('click', '.cp-kebab-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('kebab');
        const menu = $(`.cp-menu[data-menu="${id}"]`);
        const isOpen = menu.hasClass('open');
        $('.cp-menu.open').removeClass('open');
        if (!isOpen) menu.addClass('open');
    });

    // Close any open kebab menu on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.cp-menu, .cp-kebab-btn').length) {
            $('.cp-menu.open').removeClass('open');
        }
    });

    // Kebab menu actions
    $(document).on('click', '.cp-menu-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const action = $(this).data('action');
        const id = $(this).data('id');
        $('.cp-menu.open').removeClass('open');
        if (action === 'edit') {
            fetchDesignDetails(id);
        } else if (action === 'change-owner') {
            showChangeOwnerModal(id, $(this).data('owner-name') || '', $(this).data('owner-id'));
        } else if (action === 'delete') {
            deleteDesign(id);
        }
    });

    // Search filter (client-side, matches name + owner + creator)
    $('#cp-search-input').on('input', function() {
        const q = this.value.trim().toLowerCase();
        $('#portal-designs-container tr.cp-row').each(function() {
            const key = $(this).data('search') || '';
            $(this).toggle(!q || key.indexOf(q) !== -1);
        });
    });

    $('#designer-cancel').on('click', function() {
        clearDesignUrl();
        clearBreadcrumbDesign();
        $('#captive-portal-designer').hide();
        $('#captive-portal-designs-list').show();
        $('#open-full-preview').hide();
        resetDesignForm();
        currentDesignId = null;
    });

    $('#open-full-preview').on('click', function() {
        const locale = document.documentElement.lang || 'en';
        const gradDisabled = $('#gradient-start').data('disabled') === true;
        const draft = {
            name:             $('#portal-name').val(),
            theme_color:      $('#theme-color').val(),
            welcome_message:  $('#portal-welcome').val(),
            login_instructions: $('#login-instructions').val(),
            button_text:      $('#portal-button-text').val(),
            show_terms:       $('#show-terms').is(':checked'),
            terms_of_service: $('#terms-of-service').val(),
            privacy_policy:   $('#privacy-policy').val(),
            background_color_gradient_start: gradDisabled ? null : $('#gradient-start').val(),
            background_color_gradient_end:   gradDisabled ? null : $('#gradient-end').val(),
        };
        localStorage.setItem('cp_preview_draft', JSON.stringify(draft));
        const base = currentDesignId ? ('/' + locale + '/captive-portals/' + currentDesignId + '/preview') : ('/' + locale + '/captive-portals/preview/new');
        window.open(base, '_blank');
    });

    $('#create-new-design').on('click', function() {
        clearDesignUrl();
        clearBreadcrumbDesign();
        resetDesignerTabs();
        $('#captive-portal-designs-list').hide();
        $('#captive-portal-designer').show();
        $('#open-full-preview').show();
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
        
        const row = $(`tr.cp-row[data-id="${designId}"]`);
        row.addClass('opacity-50').css('pointer-events', 'none');

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
