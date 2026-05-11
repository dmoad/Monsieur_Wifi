/**
 * add-location-modal.js
 *
 * Self-contained controller for the create-location modal partial
 * (resources/views/_add-location-modal.blade.php).
 *
 * Open it from anywhere:
 *   AddLocationModal.open({ onSuccess: () => refreshMyData() });
 *
 * Reads localized strings from window.ADD_LOCATION_T (set by the partial).
 */

const AddLocationModal = (function () {
    const T = window.ADD_LOCATION_T || {};
    let onSuccessCallback = null;
    let wired = false;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function showFieldError(fieldId, message) {
        $(`#${fieldId}`)
            .addClass('is-invalid')
            .after(`<div class="invalid-feedback form-error">${message}</div>`);
    }

    async function loadUsers() {
        if (!UserManager.isAdminOrAbove()) return;
        const token = UserManager.getToken();
        try {
            const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Failed to load users');
            const data = await response.json();
            const select = $('#owner-select');

            let options = `<option value="">${T.select_owner_first_option || 'Select owner...'}</option>`;
            (data.users || []).forEach(u => {
                options += `<option value="${u.id}">${escapeHtml(u.name)} (${escapeHtml(u.email)})</option>`;
            });
            select.html(options);
            $('#owner-select-group').show();

            $('#device-select')
                .html(`<option value="">${T.select_owner_above_first || 'Select owner first'}</option>`)
                .prop('disabled', true);
            $('#device-select-hint').text(T.select_owner_first_hint || '');
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    async function loadAvailableDevices(ownerId = null) {
        const token = UserManager.getToken();
        $('#device-select')
            .html(`<option value="">${T.loading_devices || 'Loading devices...'}</option>`)
            .prop('disabled', true);

        let url = `${APP_CONFIG.API.BASE_URL}/v1/devices/available-for-location`;
        if (ownerId) url += `?owner_id=${ownerId}`;

        try {
            const response = await fetch(url, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Failed to load devices');
            const data = await response.json();
            const select = $('#device-select');

            let options = `<option value="">${T.select_a_device || 'Select a device...'}</option>`;

            if (data.unassigned && data.unassigned.length > 0) {
                options += `<optgroup label="${T.available_devices_group || 'Available Devices'}">`;
                data.unassigned.forEach(d => {
                    options += `<option value="${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.available_suffix || 'Available'}</option>`;
                });
                options += '</optgroup>';
            }

            if (data.assigned && data.assigned.length > 0) {
                options += `<optgroup label="${T.devices_assigned_elsewhere_group || 'Assigned to Other Locations'}">`;
                data.assigned.forEach(d => {
                    const locationName = d.location ? d.location.name : (T.unknown_location || 'Unknown');
                    options += `<option value="${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.assigned_to_prefix || 'Assigned to:'} ${escapeHtml(locationName)}</option>`;
                });
                options += '</optgroup>';
            }

            if ((!data.unassigned || data.unassigned.length === 0) && (!data.assigned || data.assigned.length === 0)) {
                options = `<option value="">${T.no_devices_found || 'No devices found'}</option>`;
            }

            select.html(options).prop('disabled', false);
            $('#device-select-hint').text(T.select_device_help || '');
        } catch (error) {
            console.error('Error loading devices:', error);
            $('#device-select')
                .html(`<option value="">${T.error_loading_devices || 'Error loading devices'}</option>`)
                .prop('disabled', false);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        const user = UserManager.getUser();
        const token = UserManager.getToken();
        const btn = document.getElementById('add-location-btn');

        btn.innerHTML = `<i data-feather="loader" class="mr-2"></i>${T.adding_location || 'Adding...'}`;
        btn.disabled = true;
        if (typeof feather !== 'undefined') feather.replace();

        $('.form-error').remove();
        $('.is-invalid').removeClass('is-invalid');

        const locationData = {
            name: $('#location-name').val(),
            address: $('#location-address').val(),
            device_id: $('#device-select').val(),
            description: $('#location-notes').val()
        };

        if (UserManager.isAdminOrAbove() && $('#owner-select').val()) {
            locationData.owner_id = $('#owner-select').val();
        } else {
            locationData.owner_id = user.id;
        }

        let hasErrors = false;
        if (!locationData.name) {
            showFieldError('location-name', T.location_name_required || 'Name required');
            hasErrors = true;
        }
        if (!locationData.device_id) {
            showFieldError('device-select', T.device_required || 'Device required');
            hasErrors = true;
        }

        if (hasErrors) {
            btn.innerHTML = T.add_location || 'Add Location';
            btn.disabled = false;
            return;
        }

        $.ajax({
            url: APP_CONFIG.API.BASE_URL + '/locations',
            type: 'POST',
            data: locationData,
            headers: { 'Authorization': 'Bearer ' + token },
            success: function (response) {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                let successMessage = T.location_created || 'Location created';
                if (response.firmware) {
                    successMessage += `<br><small>${T.assigned_firmware_prefix || 'Assigned firmware:'} ${escapeHtml(response.firmware.name)}</small>`;
                }
                btn.innerHTML = successMessage;

                setTimeout(function () {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = T.add_location || 'Add Location';
                    btn.disabled = false;
                    $('#add-location-modal').modal('hide');
                    document.getElementById('add-location-form').reset();
                    $('.form-error').remove();
                    $('.is-invalid').removeClass('is-invalid');
                    if (typeof onSuccessCallback === 'function') onSuccessCallback(response);
                }, 2500);
            },
            error: function (xhr) {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-danger');
                btn.innerHTML = T.error_creating_location || 'Error creating location';
                setTimeout(function () {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = T.add_location || 'Add Location';
                    btn.disabled = false;
                }, 3000);
                console.error('Error creating location:', xhr);
            }
        });
    }

    function wireOnce() {
        if (wired) return;
        wired = true;

        $('#add-location-modal').on('show.bs.modal', function () {
            if (typeof UserManager === 'undefined') return;
            if (UserManager.isAdminOrAbove()) {
                loadUsers();
            } else {
                loadAvailableDevices();
            }
        });

        $('#owner-select').on('change', function () {
            const ownerId = $(this).val();
            if (ownerId) {
                loadAvailableDevices(ownerId);
            } else {
                $('#device-select')
                    .html(`<option value="">${T.select_owner_above_first || 'Select owner first'}</option>`)
                    .prop('disabled', true);
            }
        });

        document.getElementById('add-location-btn').addEventListener('click', handleSubmit);
    }

    function open(opts = {}) {
        wireOnce();
        onSuccessCallback = opts.onSuccess || null;
        $('#add-location-modal').modal('show');
    }

    document.addEventListener('DOMContentLoaded', wireOnce);

    return { open };
})();
