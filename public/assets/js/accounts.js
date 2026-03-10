// Accounts Management JavaScript with Bilingual Support

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
        confirmDeleteSuffix: '? This action cannot be undone.'
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
        confirmDeleteSuffix: ' ? Cette action ne peut pas être annulée.'
    }
};

const PAGE_LOCALE = typeof locale !== 'undefined' ? locale : 'en';
const t = TRANSLATIONS[PAGE_LOCALE];

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
    
    const user = UserManager.getUser();
    if (user.role != 'admin' && user.role != 'superadmin') {
        const dashboardUrl = PAGE_LOCALE === 'fr' ? '/fr/dashboard' : '/en/dashboard';
        window.location.href = dashboardUrl;
        return;
    }
    
    // Show superadmin option only if current user is superadmin
    if (user.role === 'superadmin') {
        $('.superadmin-only').show();
    }
    
    if ($.fn.select2) {
        $('#role').select2({
            dropdownParent: $('#add-new-account'),
            minimumResultsForSearch: Infinity
        });
    }
    
    $('.form-password-toggle .input-group-text').on('click', function() {
        const $this = $(this);
        const inputGroupText = $this.closest('.form-password-toggle');
        const formPasswordToggleIcon = $this.find('i');
        const formPasswordToggleInput = inputGroupText.parent().find('input');

        if (formPasswordToggleInput.attr('type') === 'text') {
            formPasswordToggleInput.attr('type', 'password');
            if (feather) {
                formPasswordToggleIcon.replaceWith(feather.icons.eye.toSvg({ class: 'font-small-4' }));
            }
        } else if (formPasswordToggleInput.attr('type') === 'password') {
            formPasswordToggleInput.attr('type', 'text');
            if (feather) {
                formPasswordToggleIcon.replaceWith(feather.icons['eye-off'].toSvg({ class: 'font-small-4' }));
            }
        }
    });
    
    $('#status').on('change', function() {
        $(this).next('label').text($(this).prop('checked') ? 'Active' : 'Inactive');
    });
});

$(document).ready(function() {
    const user = UserManager.getUser();
    const token = UserManager.getToken();
    
    if (!token || !user) {
        window.location.href = '/';
        return;
    }
    
    const profile_picture = localStorage.getItem('profile_picture');
    $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
    $('.user-name').text(user.name);
    $('.user-status').text(user.role);

    if (!$.fn.DataTable.isDataTable('#accounts-table')) {
        $('#accounts-table').DataTable({
            responsive: true,
            columnDefs: [
                {
                    targets: [5],
                    orderable: false
                }
            ],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    previous: '&nbsp;',
                    next: '&nbsp;'
                }
            }
        });
    }

    function loadUsersData() {
        $.ajax({
            url: '/api/accounts/users',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    const users = response.users;
                    const table = $('#accounts-table').DataTable();

                    table.clear();

                    for (let i = 0; i < users.length; i++) {
                        const id = i + 1;
                        const name = users[i].name;
                        const email = users[i].email;
                        const role = users[i].role || 'user';
                        let profile_picture = users[i].profile_picture;
                        let profile_picture_path = '/uploads/profile_pictures/' + profile_picture;
                        
                        if (profile_picture === null) {
                            profile_picture_path = '/assets/avatar-default.jpg';
                            profile_picture = `<img src="/assets/avatar-default.jpg" alt="Profile Picture" class="img-fluid" style="width: 50px; height: 50px;">`;
                        } else {
                            profile_picture_path = '/uploads/profile_pictures/' + profile_picture;
                            profile_picture = `<img src="/uploads/profile_pictures/${profile_picture}" alt="Profile Picture" class="img-fluid" style="width: 50px; height: 50px;">`;
                        }
                        
                        let roleBadge;
                        if (role === 'superadmin') {
                            roleBadge = `<span class="badge badge-role-superadmin">${t.superadmin}</span>`;
                        } else if (role === 'admin') {
                            roleBadge = `<span class="badge badge-role-admin">${t.admin}</span>`;
                        } else {
                            roleBadge = `<span class="badge badge-light-secondary">${t.user}</span>`;
                        }
                        
                        const userId = users[i].id;
                        const canDelete = user.role === 'superadmin';
                        const deleteBtn = canDelete
                            ? `<button class="btn btn-sm btn-danger delete-user-btn" data-user-id="${userId}" data-user-name="${name}" data-user-role="${role}">
                                  <i data-feather="trash-2"></i> ${t.delete}
                               </button>`
                            : '';
                        const actions = `<button class="btn btn-sm btn-primary edit-user-btn" data-user-id="${userId}" data-name="${name}" data-email="${email}" data-role="${role}" data-profile-picture="${profile_picture_path}">
                                          <i data-feather="edit-2"></i> ${t.edit}
                                       </button> ${deleteBtn}`;
                        
                        table.row.add([id, name, email, roleBadge, profile_picture, actions]).draw();
                    }

                    $('#total-accounts').text(response.total);
                    feather.replace();
                } else {
                    alert(t.failedFetchUsers);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    loadUsersData();

    $('#new-account-upload').on('change', function() {
        const file = $(this).prop('files')[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                toastr.error(t.invalidFileType, 'Invalid File');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                toastr.error(t.fileTooLarge, 'File Too Large');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#new-account-upload-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Password method toggle: show/hide manual password fields
    $(document).on('change', 'input[name="password-method"]', function() {
        const method = $(this).val();
        if (method === 'email') {
            $('#manual-password-fields').hide();
            $('#new-account-password, #new-account-confirm-password').removeAttr('required');
        } else {
            $('#manual-password-fields').show();
            $('#new-account-password, #new-account-confirm-password').attr('required', true);
        }
    });

    // Reset modal state when closed
    $('#add-new-account').on('hidden.bs.modal', function() {
        $('input[name="password-method"][value="manual"]').prop('checked', true)
            .closest('label').addClass('active')
            .siblings('label').removeClass('active');
        $('#manual-password-fields').show();
        $('#new-account-password, #new-account-confirm-password').attr('required', true);
        $('#new-password-error-message').addClass('hidden');
    });

    $('#add-account-form').on('submit', function(e) {
        e.preventDefault();

        const name = $('#new-account-name').val().trim();
        const email = $('#new-account-email').val().trim();
        const sendVerification = $('input[name="password-method"]:checked').val() === 'email';

        if (!name || !email) {
            toastr.error(t.validationError, 'Validation Error');
            return;
        }

        let userData = { name, email, send_verification: sendVerification };

        if (!sendVerification) {
            const password = $('#new-account-password').val();
            const confirmPassword = $('#new-account-confirm-password').val();

            if (!password || !confirmPassword) {
                toastr.error(t.validationError, 'Validation Error');
                return;
            }

            if (password !== confirmPassword) {
                $('#new-password-error-message').removeClass('hidden');
                setTimeout(function() { $('#new-password-error-message').addClass('hidden'); }, 3000);
                return;
            }
            $('#new-password-error-message').addClass('hidden');

            userData.password = password;
            userData.password_confirmation = confirmPassword;
        }

        const $button = $('#create-account-btn');
        const originalText = $button.html();
        $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.creating}`).prop('disabled', true);

        $.ajax({
            url: '/api/accounts/users',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(userData),
            success: function(response) {
                const profileFile = $('#new-account-upload').prop('files')[0];
                if (profileFile && response.user) {
                    const formData = new FormData();
                    formData.append('file', profileFile);
                    formData.append('user_id', response.user.id);

                    $.ajax({
                        url: '/api/auth/upload-profile-picture',
                        type: 'POST',
                        data: formData,
                        headers: { 'Authorization': 'Bearer ' + token },
                        processData: false,
                        contentType: false,
                        error: function(xhr, status, error) {
                            console.error('Error uploading profile picture:', error);
                        }
                    });
                }

                $('#add-account-form')[0].reset();
                $('#new-account-upload-img').attr('src', '/assets/avatar-default.jpg');
                $('#add-new-account').modal('hide');

                const successMsg = sendVerification ? t.accountCreatedVerificationSent : t.accountCreatedSuccess;
                toastr.success(successMsg, 'Success');
                loadUsersData();
            },
            error: function(xhr) {
                let errorMessage = t.failedCreateAccount;
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.email && xhr.responseJSON.email[0]) {
                        errorMessage = xhr.responseJSON.email[0];
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                toastr.error(errorMessage, 'Error');
            },
            complete: function() {
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.edit-user-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('name');
        const userEmail = $(this).data('email');
        const userRole = $(this).data('role');
        const userProfilePicture = $(this).data('profile-picture');
        
        // Check if current user can edit this account
        const currentUser = UserManager.getUser();
        if (userRole === 'superadmin' && currentUser.role !== 'superadmin') {
            toastr.error('Only Super Admin can edit Super Admin accounts', 'Permission Denied');
            return;
        }
        
        $('#edit-user-modal').data('user-id', userId);
        $('#edit-user-modal').data('original-role', userRole);
        
        $('#edit-user-name').val(userName);
        $('#edit-user-email').val(userEmail);
        $('#edit-user-role').val(userRole);
        $('#edit-user-password').val('');
        $('#edit-user-confirm-password').val('');
        
        // Show Role field only for superadmin; show/hide superadmin option accordingly
        if (currentUser.role === 'superadmin') {
            $('#edit-user-role').closest('.form-group').show();
            $('#edit-user-role option.superadmin-only').show();
        } else {
            $('#edit-user-role').closest('.form-group').hide();
            $('#edit-user-role option.superadmin-only').hide();
        }
        
        if (userProfilePicture && userProfilePicture !== 'null' && userProfilePicture !== '') {
            $('#edit-user-upload-img').attr('src', userProfilePicture);
        } else {
            $('#edit-user-upload-img').attr('src', '/assets/avatar-default.jpg');
        }
        
        $('#edit-user-modal').modal('show');
    });

    $('#edit-user-upload').on('change', function() {
        const file = $(this).prop('files')[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                toastr.error(t.invalidFileType, 'Invalid File');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                toastr.error(t.fileTooLarge, 'File Too Large');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#edit-user-upload-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        
        const userId = $('#edit-user-modal').data('user-id');
        const originalRole = $('#edit-user-modal').data('original-role');
        const name = $('#edit-user-name').val();
        const email = $('#edit-user-email').val();
        const role = $('#edit-user-role').val();
        const password = $('#edit-user-password').val();
        const confirmPassword = $('#edit-user-confirm-password').val();
        
        // Check if current user can perform this action
        const currentUser = UserManager.getUser();
        if (originalRole === 'superadmin' && currentUser.role !== 'superadmin') {
            toastr.error('Only Super Admin can edit Super Admin accounts', 'Permission Denied');
            return;
        }
        
        if (role === 'superadmin' && currentUser.role !== 'superadmin') {
            toastr.error('Only Super Admin can assign Super Admin role', 'Permission Denied');
            return;
        }
        
        if (!name || !email || !role) {
            toastr.error(t.validationError, 'Validation Error');
            return;
        }
        
        if (password && password !== confirmPassword) {
            $('#edit-password-error-message').removeClass('hidden');
            setTimeout(function() {
                $('#edit-password-error-message').addClass('hidden');
            }, 3000);
            return;
        } else {
            $('#edit-password-error-message').addClass('hidden');
        }
        
        const $button = $('#update-user-btn');
        const originalText = $button.html();
        $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.updating}`).prop('disabled', true);
        
        const userData = {
            name: name,
            email: email,
        };

        // Only superadmin may change roles
        if (currentUser.role === 'superadmin') {
            userData.role = role;
        }
        
        if (password && password.trim() !== '') {
            userData.password = password;
            userData.confirm_password = confirmPassword;
        }
        
        $.ajax({
            url: `/api/accounts/users/${userId}`,
            type: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(userData),
            success: function(response) {
                console.log('User updated successfully:', response);
                
                const profileFile = $('#edit-user-upload').prop('files')[0];
                if (profileFile) {
                    const formData = new FormData();
                    formData.append('file', profileFile);
                    formData.append('user_id', userId);
                    
                    $.ajax({
                        url: '/api/auth/upload-profile-picture',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        processData: false,
                        contentType: false,
                        success: function() {
                            console.log('Profile picture uploaded successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error uploading profile picture:', error);
                            toastr.warning(t.partialSuccess, 'Partial Success');
                        }
                    });
                }
                
                $('#edit-user-form')[0].reset();
                $('#edit-user-modal').modal('hide');
                
                toastr.success(t.accountUpdatedSuccess, 'Success');
                loadUsersData();
            },
            error: function(xhr, status, error) {
                console.error('Error updating user:', error);
                let errorMessage = t.failedUpdateAccount;
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.email && xhr.responseJSON.email[0]) {
                        errorMessage = xhr.responseJSON.email[0];
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                
                toastr.error(errorMessage, 'Error');
            },
            complete: function() {
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.delete-user-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userRole = $(this).data('user-role');
        
        // Only superadmin can delete accounts
        const currentUser = UserManager.getUser();
        if (currentUser.role !== 'superadmin') {
            toastr.error('Only Super Admin can delete accounts', 'Permission Denied');
            return;
        }
        
        if (confirm(`${t.confirmDelete} "${userName}"${t.confirmDeleteSuffix}`)) {
            const $button = $(this);
            const originalHtml = $button.html();
            $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
            
            $.ajax({
                url: `/api/accounts/users/${userId}`,
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    console.log('User deleted successfully:', response);
                    toastr.success(`${userName} ${t.accountDeletedSuccess}`, 'User Deleted');
                    loadUsersData();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting user:', error);
                    let errorMessage = t.failedDeleteAccount;
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    toastr.error(errorMessage, 'Error');
                    $button.html(originalHtml).prop('disabled', false);
                }
            });
        }
    });
});
