// Accounts Management JavaScript

const TRANSLATIONS = {
    en: {
        admin: 'Admin',
        superadmin: 'Super Admin',
        user: 'User',
        edit: 'Edit',
        delete: 'Delete',
        creating: 'Creating...',
        updating: 'Updating...',
        accountCreatedSuccess: 'Account created successfully!',
        accountCreatedVerificationSent: 'Account created. Verification email sent.',
        accountUpdatedSuccess: 'User account updated successfully!',
        accountDeletedSuccess: 'has been deleted successfully!',
        failedFetchUsers: 'Failed to fetch users',
        validationError: 'Please fill in all required fields',
        passwordsNotMatch: 'Passwords do not match',
        invalidFileType: 'Please select a valid image file (JPG or PNG)',
        fileTooLarge: 'File size must be less than 2MB',
        partialSuccess: 'User updated but profile picture upload failed',
        failedCreateAccount: 'Failed to create account. Please try again.',
        failedUpdateAccount: 'Failed to update user account. Please try again.',
        failedDeleteAccount: 'Failed to delete user account. Please try again.',
        confirmDelete: 'Are you sure you want to delete the user account for',
        confirmDeleteSuffix: '? This action cannot be undone.',
        confirmDeleteTitle: 'Delete account?',
        deleteBtn: 'Delete',
        loading: 'Loading accounts...',
        noAccounts: 'No accounts found',
    },
    fr: {
        admin: 'Administrateur',
        superadmin: 'Super Administrateur',
        user: 'Utilisateur',
        edit: 'Modifier',
        delete: 'Supprimer',
        creating: 'Création...',
        updating: 'Mise à jour...',
        accountCreatedSuccess: 'Compte créé avec succès !',
        accountCreatedVerificationSent: 'Compte créé. E-mail de vérification envoyé.',
        accountUpdatedSuccess: 'Compte utilisateur mis à jour avec succès !',
        accountDeletedSuccess: 'a été supprimé avec succès !',
        failedFetchUsers: 'Échec de la récupération des utilisateurs',
        validationError: 'Veuillez remplir tous les champs obligatoires',
        passwordsNotMatch: 'Les mots de passe ne correspondent pas',
        invalidFileType: 'Veuillez sélectionner un fichier image valide (JPG ou PNG)',
        fileTooLarge: 'La taille du fichier doit être inférieure à 2 Mo',
        partialSuccess: 'Utilisateur mis à jour mais échec du téléchargement de la photo de profil',
        failedCreateAccount: 'Échec de la création du compte. Veuillez réessayer.',
        failedUpdateAccount: 'Échec de la mise à jour du compte utilisateur. Veuillez réessayer.',
        failedDeleteAccount: 'Échec de la suppression du compte utilisateur. Veuillez réessayer.',
        confirmDelete: 'Êtes-vous sûr de vouloir supprimer le compte utilisateur pour',
        confirmDeleteSuffix: ' ? Cette action ne peut pas être annulée.',
        confirmDeleteTitle: 'Supprimer le compte ?',
        deleteBtn: 'Supprimer',
        loading: 'Chargement des comptes...',
        noAccounts: 'Aucun compte trouvé',
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

function escapeHtml(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function roleBadge(role) {
    const label = t[role] || role;
    return `<span class="ac-role-badge badge-role-${role}">${escapeHtml(label)}</span>`;
}

const _kebabSvg = `<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>`;
const _editSvg  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;
const _trashSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>`;

function closeAllAcMenus() {
    $('.ac-menu.open').removeClass('open');
}

function renderRows(users, currentUser) {
    const $tbody = $('#accounts-tbody');
    if (!users.length) {
        $tbody.html(`<tr class="ac-empty-row"><td colspan="3">${t.noAccounts}</td></tr>`);
        return;
    }

    const canDelete = currentUser.role === 'superadmin';

    const html = users.map(u => {
        const avatarSrc = u.profile_picture
            ? `/uploads/profile_pictures/${escapeHtml(u.profile_picture)}`
            : '/assets/avatar-default.jpg';
        const role = u.role || 'user';
        const deleteItem = canDelete
            ? `<div class="ac-menu-divider"></div>
               <button class="ac-menu-item ac-menu-danger" data-action="delete"
                   data-user-id="${u.id}" data-user-name="${escapeHtml(u.name)}" data-user-role="${role}">
                   ${_trashSvg} ${t.delete}
               </button>`
            : '';
        return `
            <tr data-search="${escapeHtml((u.name + ' ' + u.email + ' ' + role).toLowerCase())}">
                <td>
                    <div class="ac-user-cell">
                        <img src="${avatarSrc}" alt="" class="ac-avatar">
                        <div>
                            <div class="ac-name">${escapeHtml(u.name)}</div>
                            <div class="ac-email">${escapeHtml(u.email)}</div>
                        </div>
                    </div>
                </td>
                <td>${roleBadge(role)}</td>
                <td class="ac-col-actions">
                    <div class="ac-kebab-wrap">
                        <button class="ac-kebab-btn ac-kebab-toggle"
                            data-user-id="${u.id}" data-name="${escapeHtml(u.name)}"
                            data-email="${escapeHtml(u.email)}" data-role="${role}"
                            data-profile-picture="${avatarSrc}">
                            ${_kebabSvg}
                        </button>
                        <div class="ac-menu" id="ac-menu-${u.id}">
                            <button class="ac-menu-item" data-action="edit"
                                data-user-id="${u.id}" data-name="${escapeHtml(u.name)}"
                                data-email="${escapeHtml(u.email)}" data-role="${role}"
                                data-profile-picture="${avatarSrc}">
                                ${_editSvg} ${t.edit}
                            </button>
                            ${deleteItem}
                        </div>
                    </div>
                </td>
            </tr>`;
    }).join('');

    $tbody.html(html);
}

$(window).on('load', function() {
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
});

$(document).ready(function() {
    const user = UserManager.getUser();
    const token = UserManager.getToken();

    if (!token || !user) { window.location.href = '/'; return; }

    if (user.role !== 'admin' && user.role !== 'superadmin') {
        window.location.href = `/${PAGE_LOCALE}/dashboard`;
        return;
    }

    if (user.role === 'superadmin') $('.superadmin-only').show();

    // Password visibility toggles
    $('.form-password-toggle .input-group-text').on('click', function() {
        const $input = $(this).closest('.form-password-toggle').parent().find('input');
        const isText = $input.attr('type') === 'text';
        $input.attr('type', isText ? 'password' : 'text');
        if (typeof feather !== 'undefined') {
            $(this).find('svg, i').replaceWith(
                feather.icons[isText ? 'eye' : 'eye-off'].toSvg({ class: 'font-small-4' })
            );
        }
    });

    function loadUsersData() {
        $('#accounts-tbody').html(`<tr class="ac-empty-row"><td colspan="3">${t.loading}</td></tr>`);
        $.ajax({
            url: '/api/accounts/users',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.status === 'success') {
                    renderRows(response.users, user);
                } else {
                    toastr.error(t.failedFetchUsers);
                }
            },
            error: function() { toastr.error(t.failedFetchUsers); }
        });
    }

    loadUsersData();

    // Search filter
    $('#search-accounts').on('input', function() {
        const q = $(this).val().trim().toLowerCase();
        $('#accounts-tbody tr[data-search]').each(function() {
            $(this).toggle(!q || $(this).data('search').indexOf(q) !== -1);
        });
    });

    // Row click → open edit (skip if clicking inside the kebab area)
    $(document).on('click', '#accounts-tbody tr[data-search]', function(e) {
        if ($(e.target).closest('.ac-kebab-wrap').length) return;
        const $editBtn = $(this).find('.ac-menu-item[data-action="edit"]');
        if ($editBtn.length) openEditModal($editBtn);
    });

    // Kebab toggle
    $(document).on('click', '.ac-kebab-toggle', function() {
        const $menu = $('#ac-menu-' + $(this).data('user-id'));
        const wasOpen = $menu.hasClass('open');
        closeAllAcMenus();
        if (!wasOpen) $menu.addClass('open');
    });

    // Close menus on outside click (but not when clicking the kebab itself)
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.ac-kebab-wrap').length) closeAllAcMenus();
    });

    // Profile picture previews
    $('#new-account-upload').on('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            toastr.error(t.invalidFileType); return;
        }
        if (file.size > 2 * 1024 * 1024) { toastr.error(t.fileTooLarge); return; }
        const reader = new FileReader();
        reader.onload = e => $('#new-account-upload-img').attr('src', e.target.result);
        reader.readAsDataURL(file);
    });

    $('#edit-user-upload').on('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            toastr.error(t.invalidFileType); return;
        }
        if (file.size > 2 * 1024 * 1024) { toastr.error(t.fileTooLarge); return; }
        const reader = new FileReader();
        reader.onload = e => $('#edit-user-upload-img').attr('src', e.target.result);
        reader.readAsDataURL(file);
    });

    // Password method toggle
    $(document).on('change', 'input[name="password-method"]', function() {
        if ($(this).val() === 'email') {
            $('#manual-password-fields').hide();
            $('#new-account-password, #new-account-confirm-password').removeAttr('required');
        } else {
            $('#manual-password-fields').show();
            $('#new-account-password, #new-account-confirm-password').attr('required', true);
        }
    });

    $('#add-new-account').on('hidden.bs.modal', function() {
        $('input[name="password-method"][value="manual"]').prop('checked', true)
            .closest('label').addClass('active').siblings('label').removeClass('active');
        $('#manual-password-fields').show();
        $('#new-account-password, #new-account-confirm-password').attr('required', true);
        $('#new-password-error-message').addClass('hidden');
    });

    // Create account
    $('#add-account-form').on('submit', function(e) {
        e.preventDefault();
        const name = $('#new-account-name').val().trim();
        const email = $('#new-account-email').val().trim();
        const sendVerification = $('input[name="password-method"]:checked').val() === 'email';

        if (!name || !email) { toastr.error(t.validationError); return; }

        let userData = { name, email, send_verification: sendVerification };
        if (!sendVerification) {
            const password = $('#new-account-password').val();
            const confirm = $('#new-account-confirm-password').val();
            if (!password || !confirm) { toastr.error(t.validationError); return; }
            if (password !== confirm) {
                $('#new-password-error-message').removeClass('hidden');
                setTimeout(() => $('#new-password-error-message').addClass('hidden'), 3000);
                return;
            }
            $('#new-password-error-message').addClass('hidden');
            userData.password = password;
            userData.password_confirmation = confirm;
        }

        const $btn = $('#create-account-btn');
        const orig = $btn.html();
        $btn.html(`<span class="spinner-border spinner-border-sm"></span> ${t.creating}`).prop('disabled', true);

        $.ajax({
            url: '/api/accounts/users', type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
            data: JSON.stringify(userData),
            success: function(response) {
                const profileFile = $('#new-account-upload').prop('files')[0];
                if (profileFile && response.user) {
                    const fd = new FormData();
                    fd.append('file', profileFile);
                    fd.append('user_id', response.user.id);
                    $.ajax({ url: '/api/auth/upload-profile-picture', type: 'POST',
                        data: fd, headers: { 'Authorization': 'Bearer ' + token },
                        processData: false, contentType: false });
                }
                $('#add-account-form')[0].reset();
                $('#new-account-upload-img').attr('src', '/assets/avatar-default.jpg');
                $('#add-new-account').modal('hide');
                toastr.success(sendVerification ? t.accountCreatedVerificationSent : t.accountCreatedSuccess);
                loadUsersData();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.email?.[0] || xhr.responseJSON?.message || t.failedCreateAccount;
                toastr.error(msg);
            },
            complete: () => $btn.html(orig).prop('disabled', false)
        });
    });

    function openEditModal($btn) {
        const userId   = $btn.data('user-id');
        const userName = $btn.data('name');
        const userEmail= $btn.data('email');
        const userRole = $btn.data('role');
        const userPic  = $btn.data('profile-picture');

        if (userRole === 'superadmin' && user.role !== 'superadmin') {
            toastr.error('Only Super Admin can edit Super Admin accounts'); return;
        }

        $('#edit-user-modal').data('user-id', userId).data('original-role', userRole);
        $('#edit-user-name').val(userName);
        $('#edit-user-email').val(userEmail);
        $('#edit-user-role').val(userRole);
        $('#edit-user-password, #edit-user-confirm-password').val('');

        if (user.role === 'superadmin') {
            $('#edit-user-role').closest('.form-group').show();
            $('#edit-user-role option.superadmin-only').show();
        } else {
            $('#edit-user-role').closest('.form-group').hide();
        }

        $('#edit-user-upload-img').attr('src',
            (userPic && userPic !== 'null' && userPic !== '') ? userPic : '/assets/avatar-default.jpg'
        );
        $('#edit-user-modal').modal('show');
    }

    // Menu actions
    $(document).on('click', '.ac-menu-item[data-action="delete"]', async function(e) {
        e.stopPropagation();
        closeAllAcMenus();
        if (user.role !== 'superadmin') {
            toastr.error('Only Super Admin can delete accounts'); return;
        }
        const userId   = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userRole = $(this).data('user-role');
        const ok = await MwConfirm.open({
            title: t.confirmDeleteTitle || 'Delete account?',
            message: `${t.confirmDelete} "${userName}"${t.confirmDeleteSuffix}`,
            confirmText: t.deleteBtn || 'Delete',
            cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
            destructive: true,
        });
        if (!ok) return;

        const $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: `/api/accounts/users/${userId}`, type: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function() {
                toastr.success(`${userName} ${t.accountDeletedSuccess}`);
                loadUsersData();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || t.failedDeleteAccount);
                $btn.prop('disabled', false);
            }
        });
    });

    // Edit menu item
    $(document).on('click', '.ac-menu-item[data-action="edit"]', function() {
        closeAllAcMenus();
        openEditModal($(this));
    });

    // Update account
    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        const userId   = $('#edit-user-modal').data('user-id');
        const origRole = $('#edit-user-modal').data('original-role');
        const name     = $('#edit-user-name').val();
        const email    = $('#edit-user-email').val();
        const role     = $('#edit-user-role').val();
        const password = $('#edit-user-password').val();
        const confirm  = $('#edit-user-confirm-password').val();

        if (origRole === 'superadmin' && user.role !== 'superadmin') {
            toastr.error('Only Super Admin can edit Super Admin accounts'); return;
        }
        if (!name || !email || !role) { toastr.error(t.validationError); return; }
        if (password && password !== confirm) {
            $('#edit-password-error-message').removeClass('hidden');
            setTimeout(() => $('#edit-password-error-message').addClass('hidden'), 3000);
            return;
        }
        $('#edit-password-error-message').addClass('hidden');

        const userData = { name, email };
        if (user.role === 'superadmin') userData.role = role;
        if (password) { userData.password = password; userData.confirm_password = confirm; }

        const $btn = $('#update-user-btn');
        const orig = $btn.html();
        $btn.html(`<span class="spinner-border spinner-border-sm"></span> ${t.updating}`).prop('disabled', true);

        $.ajax({
            url: `/api/accounts/users/${userId}`, type: 'PUT',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
            data: JSON.stringify(userData),
            success: function() {
                const profileFile = $('#edit-user-upload').prop('files')[0];
                if (profileFile) {
                    const fd = new FormData();
                    fd.append('file', profileFile);
                    fd.append('user_id', userId);
                    $.ajax({ url: '/api/auth/upload-profile-picture', type: 'POST',
                        data: fd, headers: { 'Authorization': 'Bearer ' + token },
                        processData: false, contentType: false,
                        error: () => toastr.warning(t.partialSuccess) });
                }
                $('#edit-user-form')[0].reset();
                $('#edit-user-modal').modal('hide');
                toastr.success(t.accountUpdatedSuccess);
                loadUsersData();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.email?.[0] || xhr.responseJSON?.message || t.failedUpdateAccount;
                toastr.error(msg);
            },
            complete: () => $btn.html(orig).prop('disabled', false)
        });
    });

    // Delete account
    $(document).on('click', '.delete-user-btn', async function() {
        if (user.role !== 'superadmin') {
            toastr.error('Only Super Admin can delete accounts'); return;
        }
        const userId   = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userRole = $(this).data('user-role');

        const ok = await MwConfirm.open({
            title: t.confirmDeleteTitle || 'Delete account?',
            message: `${t.confirmDelete} "${userName}"${t.confirmDeleteSuffix}`,
            confirmText: t.deleteBtn || 'Delete',
            cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
            destructive: true,
        });
        if (!ok) return;

        const $btn = $(this);
        const orig = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: `/api/accounts/users/${userId}`, type: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function() {
                toastr.success(`${userName} ${t.accountDeletedSuccess}`);
                loadUsersData();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || t.failedDeleteAccount;
                toastr.error(msg);
                $btn.html(orig).prop('disabled', false);
            }
        });
    });
});
