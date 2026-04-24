/**
 * location-details-settings.js
 *
 * Location Details tab (the "Settings" panel):
 *  - Info form (name/address/contact/description + status + product model)
 *  - Owner + shared-users dropdowns (admin-only, Select2 multi-select with role pills)
 *  - Save / Reset form actions
 *  - Clone location modal (Cloner action in the header Actions dropdown)
 *
 * Depends on shell globals: API, i18n, commonI18n, location_id, apiFetch,
 * handleApiError, reRenderFeather, loadLocationDetails (reset-form rehydrates
 * the whole page via the shell's coordinator).
 *
 * `initSettingsHandlers()` wires the settings-specific event handlers and is
 * called from the shell's initEventHandlers().
 */

'use strict';

function populateLocationForm(location) {
    $('#location-name').val(location.name || '');
    $('#location-address').val(location.address || '');
    $('#location-city').val(location.city || '');
    $('#location-state').val(location.state || '');
    $('#location-country').val(location.country || '');
    $('#location-postal-code').val(location.postal_code || '');
    $('#location-manager').val(location.manager_name || '');
    $('#location-contact-email').val(location.contact_email || '');
    $('#location-contact-phone').val(location.contact_phone || '');
    $('#location-status').val(location.status || 'active');
    $('#location-description').val(location.description || '');
    $('#description-counter').text((location.description || '').length);
    if (location.device && location.device.product_model_id) {
        $('#router-model-select').val(location.device.product_model_id);
    }
}

function resetLocationForm() {
    loadLocationDetails();
}

async function saveLocationInfo() {
    const $btn = $('#save-location-info');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin mr-1"></i>${commonI18n.saving || ''}`);

    try {
        const data = {
            name: $('#location-name').val().trim(),
            address: $('#location-address').val().trim(),
            city: $('#location-city').val().trim(),
            state: $('#location-state').val().trim(),
            country: $('#location-country').val().trim(),
            postal_code: $('#location-postal-code').val().trim(),
            manager_name: $('#location-manager').val().trim(),
            contact_email: $('#location-contact-email').val().trim(),
            contact_phone: $('#location-contact-phone').val().trim(),
            status: $('#location-status').val(),
            description: $('#location-description').val().trim(),
        };
        if (UserManager.isAdminOrAbove()) {
            data.owner_id = $('#location-owner').val() || null;
            const selectedIds = $('#location-shared-users').val() || [];
            data.shared_users = selectedIds.map(id => ({
                user_id: parseInt(id, 10),
                access_level: 'full',
            }));
        }
        const modelVal = $('#router-model-select').val();
        if (modelVal) data.product_model_id = parseInt(modelVal, 10);

        await apiFetch(`${API}/locations/${location_id}/general`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
        toastr.success(i18n.location_info_saved);
    } catch (err) {
        handleApiError(err, 'saveLocationInfo');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
        reRenderFeather();
    }
}

// ============================================================================
// CLONE LOCATION
// ============================================================================

async function openCloneModal() {
    if (UserManager.isAdminOrAbove()) {
        // Load users into the clone owner dropdown
        try {
            const res = await apiFetch(`${API}/accounts/users`);
            const users = res.users || res.data || [];
            const $select = $('#clone-owner-select');
            $select.empty().append(`<option value="">${i18n.clone_assign_to_self || ''}</option>`);
            users.forEach(u => {
                $select.append(`<option value="${u.id}">${u.name} (${u.email})</option>`);
            });
        } catch (err) {
            console.error('Error loading user list for clone:', err);
        }
        $('#clone-owner-group').show();
    } else {
        $('#clone-owner-group').hide();
    }
    $('#clone-location-modal').modal('show');
}

async function cloneLocation() {
    const $btn = $('#confirm-clone-btn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm mr-1"></span> ${commonI18n.cloning || ''}`);

    try {
        const body = {};
        if (UserManager.isAdminOrAbove()) {
            const ownerId = $('#clone-owner-select').val();
            if (ownerId) body.owner_id = ownerId;
        }

        const res = await apiFetch(`${API}/locations/${location_id}/clone`, {
            method: 'POST',
            body: JSON.stringify(body),
        });

        if (res.success) {
            $('#clone-location-modal').modal('hide');
            toastr.success(i18n.clone_redirecting);
            const parts = window.location.pathname.split('/');
            const lang = ['en', 'fr'].includes(parts[1]) ? parts[1] : 'en';
            setTimeout(() => {
                window.location.href = `/${lang}/locations/${res.location.id}`;
            }, 1200);
        }
    } catch (err) {
        handleApiError(err, 'cloneLocation');
    } finally {
        $btn.prop('disabled', false).html(origHtml);
    }
}

// Role badge colours (matches sidebar pill style)
const ROLE_BADGE = {
    superadmin: { bg: 'rgba(234,84,85,0.12)',   color: '#ea5455', label: 'Super Admin' },
    admin:      { bg: 'rgba(255,159,67,0.12)',   color: '#ff9f43', label: 'Admin' },
    operator:   { bg: 'rgba(0,207,232,0.12)',    color: '#00cfe8', label: 'Operator' },
    viewer:     { bg: 'rgba(40,199,111,0.12)',   color: '#28c76f', label: 'Viewer' },
    partner:    { bg: 'rgba(115,103,240,0.12)',  color: '#7367f0', label: 'Partner' },
};

function roleBadgeHtml(role) {
    const r = ROLE_BADGE[role] || { bg: 'rgba(110,107,123,0.12)', color: '#6e6b7b', label: role || 'User' };
    return `<span style="font-size:0.68rem;background:${r.bg};color:${r.color};border-radius:10px;padding:1px 7px;font-weight:600;margin-left:6px;vertical-align:middle;">${r.label}</span>`;
}

async function loadUserDropdowns(currentOwnerId, sharedUsers = []) {
    try {
        const res = await apiFetch(`${API}/accounts/users`);
        const users = res.users || res.data || [];

        // ── Owner dropdown (plain select) ────────────────────────────────────
        const $owner = $('#location-owner');
        $owner.empty().append('<option value="">Select Owner</option>');
        users.forEach(u => {
            const roleLabel = (ROLE_BADGE[u.role] || {}).label || (u.role || '');
            $owner.append(`<option value="${u.id}" ${u.id == currentOwnerId ? 'selected' : ''}>${u.name} (${u.email}) — ${roleLabel}</option>`);
        });

        // ── Shared-access Select2 multi-select ───────────────────────────────
        const sharedUserIds = (sharedUsers || []).map(e => parseInt(e.user_id, 10));
        const $shared = $('#location-shared-users');

        // Destroy any existing Select2 instance before rebuilding
        if ($shared.hasClass('select2-hidden-accessible')) {
            $shared.select2('destroy');
        }
        $shared.empty();

        users.forEach(u => {
            if (u.id == currentOwnerId) return; // owner excluded from shared list
            const isSelected = sharedUserIds.includes(parseInt(u.id, 10));
            // Store role on the option so templateResult can read it
            const $opt = $(new Option(
                `${u.name} (${u.email})`,
                u.id,
                isSelected,
                isSelected
            ));
            $opt.data('role', u.role || '');
            $shared.append($opt);
        });

        $shared.select2({
            placeholder: 'Search users…',
            allowClear: true,
            width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text; // placeholder
                const role = $(option.element).data('role') || '';
                return $(`<span>${option.text}${roleBadgeHtml(role)}</span>`);
            },
            templateSelection: function (option) {
                if (!option.id) return option.text;
                const role = $(option.element).data('role') || '';
                return $(`<span>${option.text}${roleBadgeHtml(role)}</span>`);
            },
        });
    } catch (err) {
        console.error('Error loading user dropdowns:', err);
    }
}

// Keep backward-compat alias (used in clone modal path)
async function loadOwnerDropdown(currentOwnerId) {
    return loadUserDropdowns(currentOwnerId, []);
}

// ============================================================================
// EVENT HANDLERS
// ============================================================================

function initSettingsHandlers() {
    // Save location info + description char counter
    $('#save-location-info').on('click', saveLocationInfo);
    $('#location-description').on('input', function () {
        $('#description-counter').text($(this).val().length);
    });

    // Clone location
    $('#clone-location-btn').on('click', openCloneModal);
    $('#confirm-clone-btn').on('click', cloneLocation);
    $('#clone-location-modal').on('hidden.bs.modal', function () {
        $('#clone-owner-select').empty();
    });
}
