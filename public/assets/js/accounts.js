// Accounts Management JavaScript with Bilingual Support
// Uses authz service for role/permission management (users are managed via Zitadel)

const TRANSLATIONS = {
    en: {
        org_owner: 'Org Owner',
        location_admin: 'Location Admin',
        viewer: 'Viewer',
        edit: 'Edit',
        revoke: 'Revoke Access',
        assigning: 'Assigning...',
        updating: 'Updating...',
        roleAssignedSuccess: 'Role assigned successfully!',
        roleUpdatedSuccess: 'Role updated successfully!',
        roleRevokedSuccess: 'Access revoked successfully!',
        failedFetchUsers: 'Failed to fetch users',
        validationError: 'Please fill in all required fields',
        failedAssignRole: 'Failed to assign role. Please try again.',
        failedUpdateRole: 'Failed to update role. Please try again.',
        failedRevokeAccess: 'Failed to revoke access. Please try again.',
        confirmRevoke: 'Are you sure you want to revoke all access for',
        confirmRevokeSuffix: '? They will no longer be able to use the platform.',
        failedFetchRoles: 'Failed to fetch available roles'
    },
    fr: {
        org_owner: 'Propriétaire Org',
        location_admin: 'Admin Site',
        viewer: 'Lecteur',
        edit: 'Modifier',
        revoke: 'Révoquer',
        assigning: 'Attribution...',
        updating: 'Mise à jour...',
        roleAssignedSuccess: 'Rôle attribué avec succès !',
        roleUpdatedSuccess: 'Rôle mis à jour avec succès !',
        roleRevokedSuccess: 'Accès révoqué avec succès !',
        failedFetchUsers: 'Échec de la récupération des utilisateurs',
        validationError: 'Veuillez remplir tous les champs obligatoires',
        failedAssignRole: 'Échec de l\'attribution du rôle. Veuillez réessayer.',
        failedUpdateRole: 'Échec de la mise à jour du rôle. Veuillez réessayer.',
        failedRevokeAccess: 'Échec de la révocation. Veuillez réessayer.',
        confirmRevoke: 'Êtes-vous sûr de vouloir révoquer l\'accès pour',
        confirmRevokeSuffix: ' ? Cette personne ne pourra plus utiliser la plateforme.',
        failedFetchRoles: 'Échec de la récupération des rôles disponibles'
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

// Available authz roles (loaded from API)
let availableRoles = [];

$(window).on('load', function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
        $('.avatar-icon').each(function() {
            $(this).css({ 'width': '24px', 'height': '24px' });
        });
    }
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

    if (!$.fn.DataTable.isDataTable('#accounts-table')) {
        $('#accounts-table').DataTable({
            responsive: true,
            columnDefs: [{ targets: [3], orderable: false }],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: { previous: '&nbsp;', next: '&nbsp;' }
            }
        });
    }

    // Load available roles from authz
    loadAvailableRoles();

    // Load users
    loadUsersData();

    // Invite user form submission (assign role to email)
    $('#add-account-form').on('submit', function(e) {
        e.preventDefault();

        const email = $('#new-account-email').val().trim();
        const roleId = parseInt($('#new-account-role').val());

        if (!email || !roleId) {
            toastr.error(t.validationError, 'Validation Error');
            return;
        }

        const $button = $('#create-account-btn');
        const originalText = $button.html();
        $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.assigning}`).prop('disabled', true);

        $.ajax({
            url: `/api/accounts/users/${encodeURIComponent(email)}/roles`,
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                role_id: roleId,
                target: 'mrwifi:org',
                target_id: '*'
            }),
            success: function() {
                $('#add-account-form')[0].reset();
                $('#add-new-account').modal('hide');
                toastr.success(t.roleAssignedSuccess, 'Success');
                loadUsersData();
            },
            error: function(xhr) {
                let errorMessage = t.failedAssignRole;
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                toastr.error(errorMessage, 'Error');
            },
            complete: function() {
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    // Edit user role
    $(document).on('click', '.edit-user-btn', function() {
        const email = $(this).data('email');
        const currentRole = $(this).data('role');
        const currentRoleId = $(this).data('role-id');

        $('#edit-user-modal').data('email', email);
        $('#edit-user-modal').data('current-role-id', currentRoleId);
        $('#edit-user-email-display').text(email);
        $('#edit-user-role').val(currentRoleId);

        $('#edit-user-modal').modal('show');
    });

    // Edit user form submission
    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();

        const email = $('#edit-user-modal').data('email');
        const oldRoleId = parseInt($('#edit-user-modal').data('current-role-id'));
        const newRoleId = parseInt($('#edit-user-role').val());

        if (oldRoleId === newRoleId) {
            $('#edit-user-modal').modal('hide');
            return;
        }

        const $button = $('#update-user-btn');
        const originalText = $button.html();
        $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.updating}`).prop('disabled', true);

        // Revoke old role, then assign new one
        $.ajax({
            url: `/api/accounts/users/${encodeURIComponent(email)}/roles`,
            type: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                role_id: oldRoleId,
                target: 'mrwifi:org',
                target_id: '*'
            }),
            success: function() {
                // Now assign new role
                $.ajax({
                    url: `/api/accounts/users/${encodeURIComponent(email)}/roles`,
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        role_id: newRoleId,
                        target: 'mrwifi:org',
                        target_id: '*'
                    }),
                    success: function() {
                        $('#edit-user-modal').modal('hide');
                        toastr.success(t.roleUpdatedSuccess, 'Success');
                        loadUsersData();
                    },
                    error: function() {
                        toastr.error(t.failedUpdateRole, 'Error');
                    },
                    complete: function() {
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            },
            error: function() {
                toastr.error(t.failedUpdateRole, 'Error');
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    // Revoke access (delete all roles for a user)
    $(document).on('click', '.revoke-user-btn', function() {
        const email = $(this).data('email');
        const permissions = $(this).data('permissions');

        if (!confirm(`${t.confirmRevoke} "${email}"${t.confirmRevokeSuffix}`)) {
            return;
        }

        const $button = $(this);
        const originalHtml = $button.html();
        $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

        // Revoke each permission entry
        const revokePromises = permissions.map(function(perm) {
            return $.ajax({
                url: `/api/accounts/users/${encodeURIComponent(email)}/roles`,
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    role_id: perm.role_id,
                    target: perm.target,
                    target_id: perm.target_id
                })
            });
        });

        $.when.apply($, revokePromises)
            .done(function() {
                toastr.success(`${email} ${t.roleRevokedSuccess}`, 'Success');
                loadUsersData();
            })
            .fail(function() {
                toastr.error(t.failedRevokeAccess, 'Error');
                $button.html(originalHtml).prop('disabled', false);
            });
    });

    function loadAvailableRoles() {
        $.ajax({
            url: '/api/accounts/roles',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                availableRoles = response.roles || [];
                populateRoleDropdowns();
            },
            error: function() {
                console.error(t.failedFetchRoles);
            }
        });
    }

    function populateRoleDropdowns() {
        const $newRole = $('#new-account-role');
        const $editRole = $('#edit-user-role');

        [$newRole, $editRole].forEach(function($select) {
            $select.empty();
            availableRoles.forEach(function(role) {
                $select.append(`<option value="${role.id}">${role.name}</option>`);
            });
        });
    }

    function loadUsersData() {
        $.ajax({
            url: '/api/accounts/users',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.status === 'success') {
                    const users = response.users;
                    const table = $('#accounts-table').DataTable();

                    table.clear();

                    for (let i = 0; i < users.length; i++) {
                        const email = users[i].email;
                        const name = users[i].name || email;
                        const role = users[i].role || 'viewer';
                        const roleNames = (users[i].roles || []).join(', ');

                        const roleBadge = getRoleBadge(role);

                        const permissionsJson = JSON.stringify(users[i].permissions || []).replace(/"/g, '&quot;');
                        const roleId = (users[i].permissions && users[i].permissions[0]) ? users[i].permissions[0].role_id : 0;

                        const actions = `
                            <button class="btn btn-sm btn-primary edit-user-btn"
                                data-email="${email}"
                                data-role="${role}"
                                data-role-id="${roleId}">
                                <i data-feather="edit-2"></i> ${t.edit}
                            </button>
                            <button class="btn btn-sm btn-danger revoke-user-btn"
                                data-email="${email}"
                                data-permissions='${JSON.stringify(users[i].permissions || [])}'>
                                <i data-feather="user-x"></i> ${t.revoke}
                            </button>`;

                        table.row.add([i + 1, name, email, roleBadge, actions]).draw();
                    }

                    $('#total-accounts').text(response.total);
                    feather.replace();
                } else {
                    toastr.error(t.failedFetchUsers, 'Error');
                }
            },
            error: function(xhr) {
                console.error('Failed to load users:', xhr.responseText);
                toastr.error(t.failedFetchUsers, 'Error');
            }
        });
    }

    function getRoleBadge(role) {
        const roleLabels = {
            org_owner: { label: t.org_owner, css: 'badge-role-superadmin' },
            location_admin: { label: t.location_admin, css: 'badge-role-admin' },
            viewer: { label: t.viewer, css: 'badge-light-secondary' }
        };

        const info = roleLabels[role] || { label: role, css: 'badge-light-secondary' };
        return `<span class="badge ${info.css}">${info.label}</span>`;
    }
});
