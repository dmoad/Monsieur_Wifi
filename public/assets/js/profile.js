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
        uploadingPhoto: 'Uploading Photo...'
    },
    fr: {
        savingChanges: 'Enregistrement des modifications...',
        validationError: 'Veuillez remplir tous les champs obligatoires',
        passwordsNotMatch: 'Les mots de passe ne correspondent pas',
        invalidFileType: 'Veuillez sélectionner un fichier image valide (JPG ou PNG)',
        fileTooLarge: 'La taille du fichier doit être inférieure à 2 Mo',
        profileUpdatedSuccess: 'Profil mis à jour avec succès !',
        failedUpdateProfile: 'Échec de la mise à jour du profil. Veuillez réessayer.',
        uploadingPhoto: 'Téléchargement de la photo...'
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
});
