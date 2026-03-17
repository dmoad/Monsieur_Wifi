// Team Management JavaScript with Bilingual Support
// Three tabs: Members, Permissions (ACL), Roles

const TRANSLATIONS = {
    en: {
        pageTitle: 'Team',
        breadcrumbHome: 'Home',
        breadcrumbCurrent: 'Team',
        tabMembers: 'Members',
        tabPermissions: 'Permissions',
        tabRoles: 'Roles',
        inviteMember: 'Invite Member',
        addPermission: 'Add Permission',
        inviteModalTitle: 'Invite Team Member',
        addPermTitle: 'Add Permission',
        role: 'Role',
        scope: 'Scope',
        name: 'Name',
        user: 'User',
        target: 'Target',
        targetId: 'Resource',
        actions: 'Actions',
        cancel: 'Cancel',
        invite: 'Send Invite',
        add: 'Add',
        edit: 'Edit',
        revoke: 'Revoke',
        filterScope: 'Filter by scope:',
        filterAll: 'All scopes',
        owner: 'Owner',
        admin: 'Admin',
        operator: 'Operator',
        viewer: 'Viewer',
        partner: 'Partner',
        none: 'No Access',
        inviting: 'Inviting...',
        adding: 'Adding...',
        roleAssignedSuccess: 'Member invited successfully!',
        permissionAddedSuccess: 'Permission added!',
        roleRevokedSuccess: 'Permission revoked!',
        failedFetchUsers: 'Failed to fetch team members',
        failedFetchPermissions: 'Failed to fetch permissions',
        failedAssignRole: 'Failed to invite member',
        failedAddPermission: 'Failed to add permission',
        failedRevokeAccess: 'Failed to revoke permission',
        confirmRevoke: 'Revoke this permission?',
        confirmRevokeAll: 'Revoke ALL permissions for this user?',
        validationError: 'Please fill in all required fields',
        viewAcl: 'View ACL',
        hideAcl: 'Hide ACL',
        noPermissions: 'No ACL entries for this user.',
        firstName: 'First Name',
        lastName: 'Last Name',
        roleLabel: 'Role',
        scopeLabel: 'Scope',
        scopeOrgWide: 'Organization (all access)',
        selectUser: 'Select a user...',
        userLabel: 'User',
        linkExisting: 'Link Account',
        linkingExisting: 'Linking...',
        userLinkedSuccess: 'Existing user linked successfully!',
        failedLinkUser: 'Failed to link user',
        noAccountInvite: 'No account found — send invite',
        emailHint: 'Type an email to search existing accounts or invite a new user',
        allResources: 'All',
        rolesDescription: 'Role definitions determine what actions each role can perform on each resource type.',
        roleActionsHeader: 'Allowed actions per target',
        'mrwifi:org': 'Organization',
        'mrwifi:zone': 'Zone',
        'mrwifi:location': 'Location',
        'mrwifi:device': 'Device',
    },
    fr: {
        pageTitle: 'Equipe',
        breadcrumbHome: 'Accueil',
        breadcrumbCurrent: 'Equipe',
        tabMembers: 'Membres',
        tabPermissions: 'Permissions',
        tabRoles: 'Roles',
        inviteMember: 'Inviter un membre',
        addPermission: 'Ajouter une permission',
        inviteModalTitle: 'Inviter un membre',
        addPermTitle: 'Ajouter une permission',
        role: 'Role',
        scope: 'Portee',
        name: 'Nom',
        user: 'Utilisateur',
        target: 'Cible',
        targetId: 'Ressource',
        actions: 'Actions',
        cancel: 'Annuler',
        invite: 'Envoyer',
        add: 'Ajouter',
        edit: 'Modifier',
        revoke: 'Revoquer',
        filterScope: 'Filtrer par portee :',
        filterAll: 'Toutes les portees',
        owner: 'Proprietaire',
        admin: 'Administrateur',
        operator: 'Operateur',
        viewer: 'Lecteur',
        partner: 'Partenaire',
        none: 'Aucun acces',
        inviting: 'Invitation...',
        adding: 'Ajout...',
        roleAssignedSuccess: 'Membre invite avec succes !',
        permissionAddedSuccess: 'Permission ajoutee !',
        roleRevokedSuccess: 'Permission revoquee !',
        failedFetchUsers: 'Echec de la recuperation des membres',
        failedFetchPermissions: 'Echec de la recuperation des permissions',
        failedAssignRole: 'Echec de l\'invitation',
        failedAddPermission: 'Echec de l\'ajout de permission',
        failedRevokeAccess: 'Echec de la revocation',
        confirmRevoke: 'Revoquer cette permission ?',
        confirmRevokeAll: 'Revoquer TOUTES les permissions de cet utilisateur ?',
        validationError: 'Veuillez remplir tous les champs obligatoires',
        viewAcl: 'Voir ACL',
        hideAcl: 'Masquer ACL',
        noPermissions: 'Aucune entree ACL pour cet utilisateur.',
        firstName: 'Prenom',
        lastName: 'Nom',
        roleLabel: 'Role',
        scopeLabel: 'Portee',
        scopeOrgWide: 'Organisation (acces complet)',
        selectUser: 'Selectionner un utilisateur...',
        userLabel: 'Utilisateur',
        linkExisting: 'Lier le compte',
        linkingExisting: 'Liaison...',
        userLinkedSuccess: 'Utilisateur existant lie avec succes !',
        failedLinkUser: 'Echec de la liaison',
        noAccountInvite: 'Aucun compte trouve — envoyer une invitation',
        emailHint: 'Saisissez un email pour chercher un compte existant ou inviter un utilisateur',
        allResources: 'Toutes',
        rolesDescription: 'Les definitions de roles determinent les actions autorisees pour chaque type de ressource.',
        roleActionsHeader: 'Actions autorisees par cible',
        'mrwifi:org': 'Organisation',
        'mrwifi:zone': 'Zone',
        'mrwifi:location': 'Site',
        'mrwifi:device': 'Appareil',
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

const ROLE_BADGES = {
    owner:    { css: 'badge-role-owner',    label: () => t.owner },
    admin:    { css: 'badge-role-admin',     label: () => t.admin },
    operator: { css: 'badge-role-operator',  label: () => t.operator },
    viewer:   { css: 'badge-role-viewer',    label: () => t.viewer },
    partner:  { css: 'badge-role-partner',   label: () => t.partner },
    none:     { css: 'badge-light-secondary', label: () => t.none },
};

const ROLES = [
    { id: 1, name: 'owner' },
    { id: 2, name: 'admin' },
    { id: 3, name: 'operator' },
    { id: 4, name: 'viewer' },
    { id: 5, name: 'partner' },
];

function roleBadge(roleName) {
    const info = ROLE_BADGES[roleName] || ROLE_BADGES.none;
    return '<span class="badge ' + info.css + '">' + info.label() + '</span>';
}

function targetBadge(target) {
    const label = t[target] || target || '-';
    return '<span class="badge badge-target">' + label + '</span>';
}

$(document).ready(function () {
    const user = UserManager.getUser();
    const token = UserManager.getToken();

    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    // Set translated labels
    $('#page-title').text(t.pageTitle);
    $('#breadcrumb-home').text(t.breadcrumbHome);
    $('#breadcrumb-current').text(t.breadcrumbCurrent);
    $('#tab-members-label').text(t.tabMembers);
    $('#tab-permissions-label').text(t.tabPermissions);
    $('#tab-roles-label').text(t.tabRoles);
    $('#th-name').text(t.name);
    $('#th-role').text(t.role);
    $('#th-actions').text(t.actions);
    $('#th-perm-user').text(t.user);
    $('#th-perm-role').text(t.role);
    $('#th-perm-target').text(t.target);
    $('#th-perm-target-id').text(t.targetId);
    $('#th-perm-actions').text(t.actions);
    $('#filter-scope-label').text(t.filterScope);
    $('#filter-all-option').text(t.filterAll);

    // Invite modal labels
    $('#invite-modal-title').text(t.inviteModalTitle);
    $('#invite-first-name-label').text(t.firstName);
    $('#invite-last-name-label').text(t.lastName);
    $('#invite-role-label').text(t.roleLabel);
    $('#invite-scope-label').text(t.scopeLabel);
    $('#scope-org-option').text(t.scopeOrgWide);
    $('#btn-cancel').text(t.cancel);
    $('#btn-invite-submit').text(t.invite);
    $('#invite-email-hint').text(t.emailHint);

    // Reset modal state when it closes
    function resetInviteModal() {
        $('#invite-mode').val('');
        $('#invite-name-fields').hide();
        $('#invite-selected-user').hide();
        $('#invite-email').val('').prop('readonly', false);
        $('#invite-email-results').hide().empty();
        $('#invite-email-hint').show();
        $('#btn-invite-submit').text(t.invite).prop('disabled', true);
    }

    $('#invite-member-modal').on('hidden.bs.modal', function () {
        resetInviteModal();
    });

    $('#invite-member-modal').on('shown.bs.modal', function () {
        resetInviteModal();
        $('#invite-email').focus();
    });

    // Email autocomplete — always active, searches Zitadel
    let searchTimeout = null;
    $('#invite-email').on('input', function () {
        const query = $(this).val().trim();
        const $results = $('#invite-email-results');

        // Reset mode when user changes email
        $('#invite-mode').val('');
        $('#invite-name-fields').hide();
        $('#invite-selected-user').hide();
        $('#btn-invite-submit').prop('disabled', true);

        if (query.length < 3) {
            $results.hide().empty();
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            $.ajax({
                url: '/api/accounts/search?email=' + encodeURIComponent(query),
                headers: { 'Authorization': 'Bearer ' + token },
                success: function (response) {
                    $results.empty();
                    const users = response.users || [];

                    var stateLabels = {
                        active:  { text: 'Active',        bg: 'badge-light-success' },
                        initial: { text: 'Pending setup', bg: 'badge-light-warning' },
                        inactive:{ text: 'Inactive',      bg: 'badge-light-secondary' },
                        locked:  { text: 'Locked',        bg: 'badge-light-danger' },
                        deleted: { text: 'Deleted',       bg: 'badge-light-danger' }
                    };

                    if (users.length > 0) {
                        users.forEach(function (u) {
                            const name = ((u.first_name || '') + ' ' + (u.last_name || '')).trim();
                            const label = name ? name + ' &mdash; ' + u.email : u.email;
                            const rawState = u.state || 'unknown';
                            const si = stateLabels[rawState] || { text: rawState, bg: 'badge-light-secondary' };
                            $results.append(
                                '<div class="ac-item ac-user" data-email="' + u.email + '" data-name="' + name + '">' +
                                '<i data-feather="user-check" style="width:14px;height:14px;" class="mr-50 text-success"></i>' +
                                label + ' <span class="badge ' + si.bg + '" style="font-size:.7rem;font-weight:500;">' + si.text + '</span></div>'
                            );
                        });
                    }

                    // Always show "invite new" option
                    $results.append(
                        '<div class="ac-item ac-invite" data-email="' + query + '">' +
                        '<i data-feather="user-plus" style="width:14px;height:14px;" class="mr-50 text-primary"></i>' +
                        '<strong>' + t.noAccountInvite + '</strong> <small>' + query + '</small></div>'
                    );

                    $results.show();
                    feather.replace();
                }
            });
        }, 400);
    });

    // Select existing user from autocomplete
    $(document).on('click', '#invite-email-results .ac-user', function () {
        const email = $(this).data('email');
        const name = $(this).data('name');
        $('#invite-email').val(email).prop('readonly', true);
        $('#invite-mode').val('link');
        $('#invite-name-fields').hide();
        $('#invite-selected-user').show();
        $('#invite-selected-name').html('<i data-feather="user-check" style="width:14px;height:14px;" class="mr-50"></i>' + (name || email));
        feather.replace();
        $('#invite-email-results').hide().empty();
        $('#invite-email-hint').hide();
        $('#btn-invite-submit').text(t.linkExisting).prop('disabled', false);
    });

    // Select "invite new" from autocomplete
    $(document).on('click', '#invite-email-results .ac-invite', function () {
        const email = $(this).data('email');
        $('#invite-email').val(email);
        $('#invite-mode').val('invite');
        $('#invite-name-fields').show();
        $('#invite-first-name').attr('required', 'required').focus();
        $('#invite-last-name').attr('required', 'required');
        $('#invite-selected-user').hide();
        $('#invite-email-results').hide().empty();
        $('#invite-email-hint').hide();
        $('#btn-invite-submit').text(t.invite).prop('disabled', false);
    });

    // Clear selection
    $('#invite-clear-selection').on('click', function () {
        $('#invite-email').val('').prop('readonly', false).focus();
        $('#invite-mode').val('');
        $('#invite-selected-user').hide();
        $('#invite-name-fields').hide();
        $('#invite-email-hint').show();
        $('#btn-invite-submit').prop('disabled', true);
    });

    // Hide results when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.email-autocomplete').length) {
            $('#invite-email-results').hide();
        }
    });

    // Add permission modal labels
    $('#add-perm-title').text(t.addPermTitle);
    $('#perm-user-label').text(t.userLabel);
    $('#perm-user-placeholder').text(t.selectUser);
    $('#perm-role-label').text(t.roleLabel);
    $('#perm-scope-label').text(t.scopeLabel);
    $('#perm-scope-org-option').text(t.scopeOrgWide);
    $('#perm-btn-cancel').text(t.cancel);
    $('#perm-btn-submit').text(t.add);

    // Populate role dropdowns
    populateRoleSelect('#invite-role');
    populateRoleSelect('#perm-role');

    // Load scope options
    loadScopeOptions();

    // Cached users list for the permission modal dropdown
    let cachedUsers = [];

    function refreshUserDropdown() {
        $.ajax({
            url: '/api/accounts/users',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                cachedUsers = response.users || [];
                const $select = $('#perm-user-select');
                $select.find('option:not(#perm-user-placeholder)').remove();
                cachedUsers.forEach(function (u) {
                    const label = (u.name || u.subject_id) + (u.email ? ' (' + u.email + ')' : '');
                    $select.append('<option value="' + u.subject_id + '">' + label + '</option>');
                });
            }
        });
    }

    // Populate user dropdown when opening the Add Permission modal
    $('#add-permission-modal').on('show.bs.modal', function () {
        refreshUserDropdown();
    });

    // Header action buttons (change per tab)
    function setHeaderAction(tab) {
        const $container = $('#header-actions');
        $container.empty();
        if (tab === 'members') {
            $container.html(
                '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#invite-member-modal">' +
                '<i data-feather="user-plus" class="mr-25"></i> ' + t.inviteMember + '</button>'
            );
        } else if (tab === 'permissions') {
            $container.html(
                '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-permission-modal">' +
                '<i data-feather="plus" class="mr-25"></i> ' + t.addPermission + '</button>'
            );
        }
        feather.replace();
    }

    setHeaderAction('members');

    // Tab switching
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target === '#panel-members') {
            setHeaderAction('members');
            loadMembers();
        } else if (target === '#panel-permissions') {
            setHeaderAction('permissions');
            loadPermissions();
        } else if (target === '#panel-roles') {
            setHeaderAction(null);
            loadRoles();
        }
    });

    // Init DataTables
    const membersTable = $('#members-table').DataTable({
        responsive: true,
        columnDefs: [
            { targets: [2], type: 'html' },
            { targets: [3], orderable: false }
        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { paginate: { previous: '&nbsp;', next: '&nbsp;' } }
    });

    const permissionsTable = $('#permissions-table').DataTable({
        responsive: true,
        columnDefs: [
            { targets: 0, type: 'html', width: '28%' },
            { targets: 1, type: 'html', width: '14%' },
            { targets: 2, type: 'html', width: '14%' },
            { targets: 3, type: 'html', width: '28%' },
            { targets: 4, orderable: false, width: '16%' }
        ],
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { paginate: { previous: '&nbsp;', next: '&nbsp;' } }
    });

    // Scope filter for permissions tab
    $('#scope-filter').on('change', function () {
        loadPermissions();
    });

    // Load initial data
    loadMembers();

    // ─── Members Tab ────────────────────────────────────────────────────

    function loadMembers() {
        $.ajax({
            url: '/api/accounts/users',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                if (response.status !== 'success') { toastr.error(t.failedFetchUsers); return; }

                const table = $('#members-table').DataTable();
                table.clear();

                response.users.forEach(function (member) {
                    const name = member.name || member.subject_id;
                    const email = member.email || '-';
                    const role = member.role || 'none';
                    const permCount = (member.permissions || []).length;

                    const actions =
                        '<button class="btn btn-sm btn-outline-info view-acl-btn mr-50" ' +
                        'data-subject-id="' + member.subject_id + '">' +
                        '<i data-feather="eye" style="width:14px;height:14px;"></i> ' + t.viewAcl + '</button>' +
                        '<button class="btn btn-sm btn-outline-danger revoke-all-btn" ' +
                        'data-subject-id="' + member.subject_id + '" ' +
                        'data-name="' + name + '" ' +
                        "data-permissions='" + JSON.stringify(member.permissions || []).replace(/'/g, '&#39;') + "'>" +
                        '<i data-feather="user-x" style="width:14px;height:14px;"></i> ' + t.revoke + '</button>';

                    table.row.add([
                        name,
                        email,
                        roleBadge(role) + ' <small class="text-muted">(' + permCount + ' ACL)</small>',
                        actions
                    ]);
                });

                table.draw();
                feather.replace();
            },
            error: function () { toastr.error(t.failedFetchUsers); }
        });
    }

    // ─── Permissions (ACL) Tab ──────────────────────────────────────────

    function loadPermissions() {
        const filterTarget = $('#scope-filter').val();
        const params = filterTarget ? '?target=' + encodeURIComponent(filterTarget) : '';

        $.ajax({
            url: '/api/accounts/permissions' + params,
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                if (response.status !== 'success') { toastr.error(t.failedFetchPermissions); return; }

                const table = $('#permissions-table').DataTable();
                table.clear();

                response.permissions.forEach(function (p) {
                    if (!p.target || !p.role_id) return;

                    const userName = p.name || p.subject_id;
                    const emailSuffix = p.email ? ' <small class="text-muted">' + p.email + '</small>' : '';
                    var targetIdDisplay;
                    if (p.target_id === '*') {
                        targetIdDisplay = '<span class="badge badge-light-secondary" style="font-weight:400;">' + t.allResources + '</span>';
                    } else if (p.target_name) {
                        targetIdDisplay = p.target_name + ' <small class="text-muted">(#' + p.target_id + ')</small>';
                    } else {
                        targetIdDisplay = '#' + p.target_id;
                    }

                    const actions =
                        '<button class="btn btn-sm btn-outline-danger revoke-perm-btn" ' +
                        'data-subject-id="' + p.subject_id + '" ' +
                        'data-role-id="' + p.role_id + '" ' +
                        'data-target="' + p.target + '" ' +
                        'data-target-id="' + p.target_id + '">' +
                        '<i data-feather="x" style="width:14px;height:14px;"></i> ' + t.revoke + '</button>';

                    table.row.add([
                        userName + emailSuffix,
                        roleBadge(p.role_name || 'none'),
                        targetBadge(p.target),
                        targetIdDisplay,
                        actions
                    ]);
                });

                table.draw();
                feather.replace();
            },
            error: function () { toastr.error(t.failedFetchPermissions); }
        });
    }

    // ─── Roles Tab ──────────────────────────────────────────────────────

    function loadRoles() {
        $.ajax({
            url: '/api/accounts/roles',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                const $container = $('#roles-content');
                $container.empty();
                $container.append('<p class="text-muted mb-2">' + t.rolesDescription + '</p>');

                (response.roles || []).forEach(function (role) {
                    const badge = roleBadge(role.name);
                    const card =
                        '<div class="card border mb-1">' +
                        '<div class="card-body py-1 px-2">' +
                        '<div class="d-flex align-items-center justify-content-between">' +
                        '<div>' + badge + ' <strong class="ml-50">' + role.alias + '</strong></div>' +
                        '<small class="text-muted">' + (role.description || '') + '</small>' +
                        '</div></div></div>';
                    $container.append(card);
                });
            }
        });
    }

    // ─── Invite member ──────────────────────────────────────────────────

    $('#invite-member-form').on('submit', function (e) {
        e.preventDefault();

        const mode = $('#invite-mode').val();
        const email = $('#invite-email').val().trim();
        const roleId = parseInt($('#invite-role').val());
        const scopeVal = $('#invite-scope').val();

        if (!mode || !email || !roleId || !scopeVal) {
            toastr.error(t.validationError);
            return;
        }

        const [target, targetId] = scopeVal.split('|');
        const $btn = $('#btn-invite-submit');
        const origText = $btn.text();

        if (mode === 'link') {
            // Link existing Zitadel user (no invite email)
            $btn.html('<span class="spinner-border spinner-border-sm"></span> ' + t.linkingExisting).prop('disabled', true);

            $.ajax({
                url: '/api/accounts/link',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({ email: email, role_id: roleId, target: target, target_id: targetId }),
                success: function () {
                    $('#invite-member-modal').modal('hide');
                    toastr.success(t.userLinkedSuccess);
                    loadMembers();
                },
                error: function (xhr) { toastr.error(xhr.responseJSON?.error || t.failedLinkUser); },
                complete: function () { $btn.text(origText).prop('disabled', false); }
            });
        } else {
            // Invite new user (creates in Zitadel + sends email)
            const firstName = $('#invite-first-name').val().trim();
            const lastName = $('#invite-last-name').val().trim();

            if (!firstName || !lastName) {
                toastr.error(t.validationError);
                return;
            }

            $btn.html('<span class="spinner-border spinner-border-sm"></span> ' + t.inviting).prop('disabled', true);

            $.ajax({
                url: '/api/accounts/invite',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({ email: email, first_name: firstName, last_name: lastName, role_id: roleId, target: target, target_id: targetId }),
                success: function () {
                    $('#invite-member-modal').modal('hide');
                    toastr.success(t.roleAssignedSuccess);
                    loadMembers();
                },
                error: function (xhr) { toastr.error(xhr.responseJSON?.error || t.failedAssignRole); },
                complete: function () { $btn.text(origText).prop('disabled', false); }
            });
        }
    });

    // ─── Add permission (from ACL tab) ──────────────────────────────────

    $('#add-permission-form').on('submit', function (e) {
        e.preventDefault();

        const subjectId = $('#perm-user-select').val();
        const roleId = parseInt($('#perm-role').val());
        const scopeVal = $('#perm-scope').val();

        if (!subjectId || !roleId || !scopeVal) {
            toastr.error(t.validationError);
            return;
        }

        const [target, targetId] = scopeVal.split('|');
        const $btn = $('#perm-btn-submit');
        const origText = $btn.text();
        $btn.html('<span class="spinner-border spinner-border-sm"></span> ' + t.adding).prop('disabled', true);

        $.ajax({
            url: '/api/accounts/users/' + encodeURIComponent(subjectId) + '/roles',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
            data: JSON.stringify({ role_id: roleId, target: target, target_id: targetId }),
            success: function () {
                $('#add-permission-form')[0].reset();
                $('#add-permission-modal').modal('hide');
                toastr.success(t.permissionAddedSuccess);
                loadPermissions();
            },
            error: function (xhr) { toastr.error(xhr.responseJSON?.error || t.failedAddPermission); },
            complete: function () { $btn.text(origText).prop('disabled', false); }
        });
    });

    // ─── Revoke single permission (from ACL tab) ────────────────────────

    $(document).on('click', '.revoke-perm-btn', function () {
        if (!confirm(t.confirmRevoke)) return;

        const $btn = $(this);
        const subjectId = $btn.data('subject-id');
        const roleId = $btn.data('role-id');
        const target = $btn.data('target');
        const targetId = $btn.data('target-id');

        // Detect if we're inside a Members tab child row or the Permissions tab
        const inMembersChild = $btn.closest('#panel-members').length > 0;

        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: '/api/accounts/users/' + encodeURIComponent(subjectId) + '/roles',
            type: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
            data: JSON.stringify({ role_id: roleId, target: target, target_id: targetId }),
            success: function () {
                toastr.success(t.roleRevokedSuccess);
                if (inMembersChild) {
                    // Re-fetch and re-render the child row for this user
                    loadMembers();
                } else {
                    loadPermissions();
                }
            },
            error: function () {
                toastr.error(t.failedRevokeAccess);
                $btn.html('<i data-feather="x"></i> ' + t.revoke).prop('disabled', false);
                feather.replace();
            }
        });
    });

    // ─── View ACL for a user (expandable detail row) ───────────────────

    $(document).on('click', '.view-acl-btn', function () {
        const $btn = $(this);
        const subjectId = $btn.data('subject-id');
        const table = $('#members-table').DataTable();
        const row = table.row($btn.closest('tr'));

        // Toggle: if already open, close it
        if (row.child.isShown()) {
            row.child.hide();
            $btn.html('<i data-feather="eye" style="width:14px;height:14px;"></i> ' + t.viewAcl);
            $btn.removeClass('btn-outline-secondary').addClass('btn-outline-info');
            feather.replace();
            return;
        }

        // Show loading
        row.child('<div class="p-1 text-muted"><span class="spinner-border spinner-border-sm mr-50"></span> Loading...</div>').show();
        $btn.html('<i data-feather="eye-off" style="width:14px;height:14px;"></i> ' + t.hideAcl);
        $btn.removeClass('btn-outline-info').addClass('btn-outline-secondary');
        feather.replace();

        $.ajax({
            url: '/api/accounts/users/' + encodeURIComponent(subjectId) + '/permissions',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                const perms = response.permissions || [];
                if (perms.length === 0) {
                    row.child('<div class="p-1 text-muted">' + t.noPermissions + '</div>').show();
                    return;
                }

                let html = '<table class="table table-sm table-bordered mb-0 ml-2" style="width:auto;background:#fafafa;">';
                html += '<thead><tr><th>' + t.role + '</th><th>' + t.target + '</th><th>' + t.targetId + '</th><th></th></tr></thead><tbody>';

                perms.forEach(function (p) {
                    var targetIdDisplay;
                    if (p.target_id === '*') {
                        targetIdDisplay = '<span class="badge badge-light-secondary" style="font-weight:400;">' + t.allResources + '</span>';
                    } else if (p.target_name) {
                        targetIdDisplay = p.target_name + ' <small class="text-muted">(#' + p.target_id + ')</small>';
                    } else {
                        targetIdDisplay = '#' + p.target_id;
                    }
                    html += '<tr>';
                    html += '<td>' + roleBadge(p.role_name || 'none') + '</td>';
                    html += '<td>' + targetBadge(p.target) + '</td>';
                    html += '<td>' + targetIdDisplay + '</td>';
                    html += '<td>';
                    if (p.role_id && p.target && p.target_id) {
                        html += '<button class="btn btn-sm btn-outline-danger revoke-perm-btn" ' +
                            'data-subject-id="' + subjectId + '" ' +
                            'data-role-id="' + p.role_id + '" ' +
                            'data-target="' + p.target + '" ' +
                            'data-target-id="' + p.target_id + '">' +
                            '<i data-feather="x" style="width:12px;height:12px;"></i></button>';
                    }
                    html += '</td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';
                row.child('<div class="p-1">' + html + '</div>').show();
                feather.replace();
            },
            error: function () {
                row.child('<div class="p-1 text-danger">Failed to load permissions</div>').show();
            }
        });
    });

    // ─── Revoke all permissions for a user (from Members tab) ───────────

    $(document).on('click', '.revoke-all-btn', function () {
        const subjectId = $(this).data('subject-id');
        const name = $(this).data('name');
        const permissions = $(this).data('permissions');

        if (!confirm(t.confirmRevokeAll + ' (' + name + ')')) return;

        const $btn = $(this);
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        const revokePromises = (permissions || []).filter(function (perm) {
            return perm.role_id && perm.target && perm.target_id;
        }).map(function (perm) {
            return $.ajax({
                url: '/api/accounts/users/' + encodeURIComponent(subjectId) + '/roles',
                type: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({ role_id: perm.role_id, target: perm.target, target_id: perm.target_id })
            });
        });

        $.when.apply($, revokePromises)
            .done(function () {
                toastr.success(t.roleRevokedSuccess);
                loadMembers();
            })
            .fail(function () {
                toastr.error(t.failedRevokeAccess);
                $btn.html('<i data-feather="user-x"></i> ' + t.revoke).prop('disabled', false);
                feather.replace();
            });
    });

    // ─── Helpers ─────────────────────────────────────────────────────────

    function populateRoleSelect(selector) {
        const $select = $(selector);
        $select.empty();
        ROLES.forEach(function (role) {
            const label = t[role.name] || role.name;
            $select.append('<option value="' + role.id + '">' + label + '</option>');
        });
    }

    function loadScopeOptions() {
        $.ajax({
            url: '/api/v1/zones',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                const zones = response.data || response.zones || response || [];
                if (Array.isArray(zones)) {
                    zones.forEach(function (zone) {
                        const label = (t['mrwifi:zone'] || 'Zone') + ': ' + zone.name;
                        const val = 'mrwifi:zone|' + zone.id;
                        $('#invite-scope, #perm-scope').append('<option value="' + val + '">' + label + '</option>');
                    });
                }
            }
        });

        $.ajax({
            url: '/api/locations',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                const locations = response.data || response.locations || response || [];
                if (Array.isArray(locations)) {
                    locations.forEach(function (loc) {
                        const label = (t['mrwifi:location'] || 'Location') + ': ' + loc.name;
                        const val = 'mrwifi:location|' + loc.id;
                        $('#invite-scope, #perm-scope').append('<option value="' + val + '">' + label + '</option>');
                    });
                }
            }
        });
    }
});
