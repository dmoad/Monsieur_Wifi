// Profile Management JavaScript with Bilingual Support
// Profile info comes from Zitadel; only profile picture is managed locally.

const TRANSLATIONS = {
    en: {
        invalidFileType: 'Please select a valid image file (JPG or PNG)',
        fileTooLarge: 'File size must be less than 2MB',
        uploadingPhoto: 'Uploading Photo...',
        photoUploadedSuccess: 'Profile picture updated successfully!',
        failedUploadPhoto: 'Failed to upload profile picture. Please try again.',
        subscriptionActive: 'Active',
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
        invalidFileType: 'Veuillez sélectionner un fichier image valide (JPG ou PNG)',
        fileTooLarge: 'La taille du fichier doit être inférieure à 2 Mo',
        uploadingPhoto: 'Téléchargement de la photo...',
        photoUploadedSuccess: 'Photo de profil mise à jour avec succès !',
        failedUploadPhoto: 'Échec du téléchargement de la photo. Veuillez réessayer.',
        subscriptionActive: 'Actif',
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

    // Display user info (read-only, from Zitadel)
    $('#account-name').val(user.name).prop('disabled', true);
    $('#account-e-mail').val(user.email).prop('disabled', true);

    const profile_picture = localStorage.getItem('profile_picture');
    if (profile_picture && profile_picture !== 'null') {
        $('#account-upload-img').attr('src', '/uploads/profile_pictures/' + profile_picture);
        $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
    } else if (user.profile_picture) {
        // Use Zitadel profile picture if available
        $('#account-upload-img').attr('src', user.profile_picture);
        $('.user-profile-picture').attr('src', user.profile_picture);
    } else {
        $('#account-upload-img').attr('src', '/assets/avatar-default.jpg');
        $('.user-profile-picture').attr('src', '/assets/avatar-default.jpg');
    }

    $('.user-name').text(user.name);
    $('.user-status').text(user.role);

    // Profile picture upload
    $('#account-upload').on('change', function() {
        const file = $(this).prop('files')[0];
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            toastr.error(t.invalidFileType, 'Invalid File');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            toastr.error(t.fileTooLarge, 'File Too Large');
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#account-upload-img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        // Upload immediately
        const formData = new FormData();
        formData.append('file', file);

        const $uploadLabel = $('.custom-file-label');
        const originalLabel = $uploadLabel.text();
        $uploadLabel.text(t.uploadingPhoto);

        $.ajax({
            url: '/api/auth/upload-profile-picture',
            type: 'POST',
            data: formData,
            headers: { 'Authorization': 'Bearer ' + token },
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.filename) {
                    localStorage.setItem('profile_picture', response.filename);
                    $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + response.filename);
                }
                toastr.success(t.photoUploadedSuccess, 'Success');
            },
            error: function() {
                toastr.error(t.failedUploadPhoto, 'Error');
            },
            complete: function() {
                $uploadLabel.text(originalLabel);
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
                    <a href="/pricing" class="btn btn-primary">
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
                <a href="/pricing" class="btn btn-primary d-flex align-items-center" style="gap: 0.4rem;">
                    <i data-feather="shopping-bag" style="width: 16px; height: 16px;"></i> ${t.subscriptionSubscribe}
                </a>
            </div>
        `);
    } else {
        const sub = data.subscription;
        const isActive = sub.stripe_status === 'active';
        const statusBadge = isActive
            ? `<span class="badge badge-light-success">${t.subscriptionActive}</span>`
            : `<span class="badge badge-light-danger">${t.subscriptionCanceled}</span>`;

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
                url: '/api/subscription/billing-portal',
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
