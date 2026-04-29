// Profile Management JavaScript with Bilingual Support

const TRANSLATIONS = {
    en: {
        savingChanges: 'Saving Changes...',
        validationError: 'Please fill in all required fields',
        passwordsNotMatch: 'Passwords do not match',
        invalidFileType: 'Please select a valid image file (JPG or PNG)',
        fileTooLarge: 'File size must be less than 2MB',
        profileUpdatedSuccess: 'Profile updated successfully!',
        failedUpdateProfile: 'Failed to update profile. Please try again.',
        uploadingPhoto: 'Uploading Photo...',
        subscriptionActive: 'Active',
        subscriptionTrialing: 'Free trial',
        subscriptionCanceled: 'Cancelled',
        subscriptionNone: 'No active subscription',
        subscriptionPlan: 'Plan',
        subscriptionStatus: 'Status',
        subscriptionManage: 'Manage Subscription',
        subscriptionSubscribe: 'Subscribe now',
        subscriptionCancelConfirm: 'Are you sure you want to cancel your subscription?',
        subscriptionOnGracePeriod: 'Your subscription is cancelled but active until'
    },
    fr: {
        savingChanges: 'Enregistrement des modifications...',
        validationError: 'Veuillez remplir tous les champs obligatoires',
        passwordsNotMatch: 'Les mots de passe ne correspondent pas',
        invalidFileType: 'Veuillez sélectionner un fichier image valide (JPG ou PNG)',
        fileTooLarge: 'La taille du fichier doit être inférieure à 2 Mo',
        profileUpdatedSuccess: 'Profil mis à jour avec succès !',
        failedUpdateProfile: 'Échec de la mise à jour du profil. Veuillez réessayer.',
        uploadingPhoto: 'Téléchargement de la photo...',
        subscriptionActive: 'Actif',
        subscriptionTrialing: 'Période d\'essai',
        subscriptionCanceled: 'Annulé',
        subscriptionNone: 'Aucun abonnement actif',
        subscriptionPlan: 'Plan',
        subscriptionStatus: 'Statut',
        subscriptionManage: 'Gérer l\'abonnement',
        subscriptionSubscribe: 'S\'abonner',
        subscriptionCancelConfirm: 'Êtes-vous sûr de vouloir annuler votre abonnement ?',
        subscriptionOnGracePeriod: 'Votre abonnement est annulé mais actif jusqu\'au'
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
});

// Reset manage-subscription button when page is restored from back/forward cache
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        const $btn = $('#manage-subscription-btn');
        if ($btn.length) {
            $btn.prop('disabled', false).html(`<i data-feather="settings" style="width: 16px; height: 16px;"></i> ${t.subscriptionManage}`);
            if (typeof feather !== 'undefined') feather.replace();
        }
    }
});

$(document).ready(function() {
    const user = UserManager.getUser();
    const token = UserManager.getToken();

    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    $('#account-name').val(user.name);
    $('#account-e-mail').val(user.email);
    
    const profile_picture = localStorage.getItem('profile_picture');
    if (profile_picture && profile_picture !== 'null') {
        $('#account-upload-img').attr('src', '/uploads/profile_pictures/' + profile_picture);
        $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
    } else {
        $('#account-upload-img').attr('src', '/assets/avatar-default.jpg');
        $('.user-profile-picture').attr('src', '/assets/avatar-default.jpg');
    }
    
    $('.user-name').text(user.name);
    $('.user-status').text(user.role);

    $('#account-upload').on('change', function() {
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
                $('#account-upload-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('.validate-form').on('submit', function(e) {
        e.preventDefault();
        
        const name = $('#account-name').val();
        const email = $('#account-e-mail').val();
        const newPassword = $('#account-new-password1').val();
        const confirmPassword = $('#account-retype-new-password1').val();
        
        if (!name || !email) {
            toastr.error(t.validationError, 'Validation Error');
            return;
        }
        
        if (newPassword && newPassword !== confirmPassword) {
            $('#password-error-message').removeClass('hidden');
            setTimeout(function() {
                $('#password-error-message').addClass('hidden');
            }, 3000);
            return;
        } else {
            $('#password-error-message').addClass('hidden');
        }
        
        const $button = $('#save-profile-btn');
        const originalText = $button.html();
        $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.savingChanges}`).prop('disabled', true);
        
        const userData = {
            name: name,
            email: email
        };
        
        if (newPassword && newPassword.trim() !== '') {
            userData.password = newPassword;
            userData.confirm_password = confirmPassword;
        }
        
        $.ajax({
            url: '/api/auth/update-profile',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(userData),
            success: function(response) {
                console.log('Profile updated successfully:', response);
                
                const profileFile = $('#account-upload').prop('files')[0];
                if (profileFile) {
                    const formData = new FormData();
                    formData.append('file', profileFile);
                    
                    const uploadButton = $button.clone();
                    $button.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${t.uploadingPhoto}`);
                    
                    $.ajax({
                        url: '/api/auth/upload-profile-picture',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        processData: false,
                        contentType: false,
                        success: function(uploadResponse) {
                            console.log('Profile picture uploaded successfully');
                            if (uploadResponse.profile_picture) {
                                localStorage.setItem('profile_picture', uploadResponse.profile_picture);
                                $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + uploadResponse.profile_picture);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error uploading profile picture:', error);
                        },
                        complete: function() {
                            $button.html(originalText).prop('disabled', false);
                            toastr.success(t.profileUpdatedSuccess, 'Success');
                        }
                    });
                } else {
                    $button.html(originalText).prop('disabled', false);
                    toastr.success(t.profileUpdatedSuccess, 'Success');
                }
                
                if (response.user) {
                    UserManager.updateUser(response.user);
                    $('.user-name').text(response.user.name);
                }
                
                $('#account-new-password1').val('');
                $('#account-retype-new-password1').val('');
            },
            error: function(xhr, status, error) {
                console.error('Error updating profile:', error);
                let errorMessage = t.failedUpdateProfile;
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.email && xhr.responseJSON.email[0]) {
                        errorMessage = xhr.responseJSON.email[0];
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                
                toastr.error(errorMessage, 'Error');
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    // Load subscription status
    loadSubscriptionStatus();
});

function loadSubscriptionStatus() {
    const token = UserManager.getToken();
    if (!token) return;

    $.ajax({
        url: '/api/subscription/status',
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function(response) {
            renderSubscription(response);
        },
        error: function() {
            $('#subscription-section').html(`
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 text-muted">${t.subscriptionNone}</p>
                    <a href="/${PAGE_LOCALE}/pricing" class="btn btn-primary">
                        <i data-feather="shopping-bag"></i> ${t.subscriptionSubscribe}
                    </a>
                </div>
            `);
            if (typeof feather !== 'undefined') feather.replace();
        }
    });
}

function renderSubscription(data) {
    const section = $('#subscription-section');

    if (!data.has_subscription) {
        section.html(`
            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0 text-muted">${t.subscriptionNone}</p>
                <a href="/${PAGE_LOCALE}/pricing" class="btn btn-primary d-flex align-items-center" style="gap: 0.4rem;">
                    <i data-feather="shopping-bag" style="width: 16px; height: 16px;"></i> ${t.subscriptionSubscribe}
                </a>
            </div>
        `);
    } else {
        const sub = data.subscription;
        let statusBadge;
        if (sub.stripe_status === 'active') {
            statusBadge = `<span class="badge badge-light-success">${t.subscriptionActive}</span>`;
        } else if (sub.stripe_status === 'trialing') {
            statusBadge = `<span class="badge badge-light-info">${t.subscriptionTrialing}</span>`;
        } else {
            statusBadge = `<span class="badge badge-light-danger">${t.subscriptionCanceled}</span>`;
        }

        let graceNote = '';
        if (sub.on_grace_period && sub.ends_at) {
            const endDate = new Date(sub.ends_at).toLocaleDateString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US');
            graceNote = `<p class="text-warning mt-1 mb-0"><i data-feather="alert-circle" style="width: 14px; height: 14px;"></i> ${t.subscriptionOnGracePeriod} ${endDate}</p>`;
        }

        section.html(`
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-50" style="gap: 0.75rem;">
                        <span class="font-weight-bold" style="font-size: 1.1rem;">${sub.name || 'Standard'}</span>
                        ${statusBadge}
                    </div>
                    ${graceNote}
                </div>
                <button class="btn btn-outline-primary d-flex align-items-center" id="manage-subscription-btn" style="gap: 0.4rem;">
                    <i data-feather="settings" style="width: 16px; height: 16px;"></i> ${t.subscriptionManage}
                </button>
            </div>
        `);

        $('#manage-subscription-btn').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '/api/subscription/billing-portal?locale=' + PAGE_LOCALE,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken() },
                success: function(response) {
                    if (response.url) {
                        window.location.href = response.url;
                    }
                },
                error: function() {
                    toastr.error('Failed to open billing portal');
                    $btn.prop('disabled', false).html(`<i data-feather="settings" style="width: 16px; height: 16px;"></i> ${t.subscriptionManage}`);
                    if (typeof feather !== 'undefined') feather.replace();
                }
            });
        });
    }

    if (typeof feather !== 'undefined') feather.replace();
}
