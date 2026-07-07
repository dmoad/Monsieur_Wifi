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
    // Parsed contents of an uploaded export file. Kept for later phases
    // (the actual backend import); this phase only renders a preview from it.
    let importPreviewData = null;
    // Users loaded for admins (id/name/email). Used to cross-verify an imported
    // owner email against existing accounts. null = not loaded (e.g. non-admin).
    let loadedUsers = null;
    let currentMode = 'manual'; // 'manual' | 'import'
    let importNeedsPassword = false; // true → a new owner account will be created
    let deviceCheck = null; // null = unchecked/checking; else {exists, type, name}
    let importNeedsModel = false; // true → a new device needs a product model picked
    let productModelsLoaded = false;

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

            loadedUsers = data.users || [];

            let options = `<option value="">${T.select_owner_first_option || 'Select owner...'}</option>`;
            loadedUsers.forEach(u => {
                options += `<option value="${u.id}">${escapeHtml(u.name)} (${escapeHtml(u.email)})</option>`;
            });
            select.html(options);
            $('#owner-select-group').show();

            // If a file was already chosen, re-render so the owner verdict reflects the loaded users.
            if (importPreviewData) renderImportPreview(importPreviewData);

            $('#device-select')
                .html(`<option value="">${T.select_owner_above_first || 'Select owner first'}</option>`)
                .prop('disabled', true);
            $('#device-select-hint').text(T.select_owner_first_hint || '');
        } catch (error) {
            console.error('Error loading users:', error);
            if (typeof toastr !== 'undefined') toastr.error(T.error_loading_users || 'Failed to load users');
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

            // Option values are prefixed so the submit handler can tell whether
            // the picked row is an existing Device row or a yet-to-convert
            // InventoryItem row. Prefix is dropped before sending to the API.
            let options = `<option value="">${T.select_a_device || 'Select a device...'}</option>`;

            if (data.unassigned && data.unassigned.length > 0) {
                options += `<optgroup label="${T.available_devices_group || 'Available Devices'}">`;
                data.unassigned.forEach(d => {
                    options += `<option value="device:${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.available_suffix || 'Available'}</option>`;
                });
                options += '</optgroup>';
            }

            if (data.inventory_stock && data.inventory_stock.length > 0) {
                options += `<optgroup label="${T.inventory_stock_group || 'Available Stock (will be activated)'}">`;
                data.inventory_stock.forEach(it => {
                    const model = it.product_model ? it.product_model.name : '';
                    options += `<option value="inv:${it.id}">${escapeHtml(it.serial_number)} - ${escapeHtml(it.mac_address)}${model ? ` (${escapeHtml(model)})` : ''} - ${T.inventory_stock_suffix || 'Stock'}</option>`;
                });
                options += '</optgroup>';
            }

            if (data.assigned && data.assigned.length > 0) {
                options += `<optgroup label="${T.devices_assigned_elsewhere_group || 'Assigned to Other Locations'}">`;
                data.assigned.forEach(d => {
                    const locationName = d.location ? d.location.name : (T.unknown_location || 'Unknown');
                    options += `<option value="device:${d.id}">${escapeHtml(d.serial_number)} - ${escapeHtml(d.mac_address)} (${escapeHtml(d.model)}) - ${T.assigned_to_prefix || 'Assigned to:'} ${escapeHtml(locationName)}</option>`;
                });
                options += '</optgroup>';
            }

            const empty =
                (!data.unassigned || data.unassigned.length === 0) &&
                (!data.inventory_stock || data.inventory_stock.length === 0) &&
                (!data.assigned || data.assigned.length === 0);
            if (empty) {
                options = `<option value="">${T.no_devices_found || 'No devices found'}</option>`;
            }

            select.html(options).prop('disabled', false);
            $('#device-select-hint').text(T.select_device_help || '');
        } catch (error) {
            console.error('Error loading devices:', error);
            if (typeof toastr !== 'undefined') toastr.error(T.error_loading_devices || 'Error loading devices');
            $('#device-select')
                .html(`<option value="">${T.error_loading_devices || 'Error loading devices'}</option>`)
                .prop('disabled', false);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        if (currentMode === 'import') return; // import commit is a separate (future) flow
        const user = UserManager.getUser();
        const token = UserManager.getToken();
        const btn = document.getElementById('add-location-btn');

        btn.innerHTML = `<i data-feather="loader" class="mr-2"></i>${T.adding_location || 'Adding...'}`;
        btn.disabled = true;
        if (typeof feather !== 'undefined') feather.replace();

        $('.form-error').remove();
        $('.is-invalid').removeClass('is-invalid');

        const selectedValue = $('#device-select').val() || '';
        const locationData = {
            name: $('#location-name').val(),
            address: $('#location-address').val(),
            description: $('#location-notes').val()
        };

        // Selected value is encoded as `device:<id>` or `inv:<id>` — the prefix
        // tells the backend which kind of row we picked. Strip & route here.
        if (selectedValue.startsWith('device:')) {
            locationData.device_id = selectedValue.slice('device:'.length);
        } else if (selectedValue.startsWith('inv:')) {
            locationData.inventory_item_id = selectedValue.slice('inv:'.length);
        }

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

        if (hasErrors) {
            btn.innerHTML = T.add_location || 'Add Location';
            btn.disabled = false;
            return;
        }

        fetch(APP_CONFIG.API.BASE_URL + '/locations', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(locationData),
        })
        .then(async function (res) {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error(data.message || 'Request failed');
                err.status = res.status;
                err.body   = data;
                throw err;
            }
            return data;
        })
        .then(function (response) {
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
        })
        .catch(function (err) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-danger');
            btn.innerHTML = T.error_creating_location || 'Error creating location';
            setTimeout(function () {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-primary');
                btn.innerHTML = T.add_location || 'Add Location';
                btn.disabled = false;
            }, 3000);
            handleApiError(err, 'createLocation');
        });
    }

    // --- JSON import preview -------------------------------------------------

    function showImportError(message) {
        $('#import-error').text(message).show();
        $('#import-preview').hide().empty();
        $('#import-newuser-group').hide();
        importPreviewData = null;
        importNeedsPassword = false;
        refreshImportButton();
    }

    function resetImport() {
        importPreviewData = null;
        const input = document.getElementById('import-file');
        if (input) input.value = '';
        $('#import-error').hide().text('');
        $('#import-preview').hide().empty();
        $('#import-newuser-group').hide();
        $('#import-newuser-password').val('');
        $('#import-device-model-group').hide();
        $('#import-device-model').val('');
        importNeedsPassword = false;
        importNeedsModel = false;
        deviceCheck = null;
    }

    // Switch the modal between the manual-entry and JSON-import flows. The two
    // share a footer button: import provides everything from the file, so the
    // manual fields are hidden and the primary action is disabled until the
    // backend import is wired (next phase).
    function setMode(mode) {
        currentMode = mode;
        const isImport = mode === 'import';
        // Active mode = solid button (white text); inactive = outline. Avoids the
        // Bootstrap outline-active quirk where primary text sits on a primary fill.
        $('#add-location-mode button[data-mode]')
            .removeClass('active btn-primary').addClass('btn-outline-primary');
        $('#add-location-mode button[data-mode="' + mode + '"]')
            .removeClass('btn-outline-primary').addClass('active btn-primary');
        $('#manual-fields').toggle(!isImport);
        $('#import-section').toggle(isImport);

        const btn = document.getElementById('add-location-btn');
        if (btn) {
            if (isImport) {
                btn.innerHTML = T.import_button || 'Import';
                refreshImportButton();
            } else {
                btn.innerHTML = T.add_location || 'Add Location';
                btn.disabled = false;
                btn.title = '';
            }
        }
        if (!isImport) resetImport();
    }

    // Enable the primary button only when a valid file is loaded and, if a new
    // owner account will be created, a password has been entered.
    function refreshImportButton() {
        const btn = document.getElementById('add-location-btn');
        if (!btn || currentMode !== 'import') return;
        const ready = !!importPreviewData &&
            (!importNeedsPassword || !!$('#import-newuser-password').val()) &&
            (!importNeedsModel || !!$('#import-device-model').val());
        btn.disabled = !ready;
    }

    function handleImport(e) {
        e.preventDefault();
        if (!importPreviewData) return;
        const token = UserManager.getToken();
        const btn = document.getElementById('add-location-btn');

        $('.form-error').remove();
        $('.is-invalid').removeClass('is-invalid');

        if (importNeedsPassword && !$('#import-newuser-password').val()) {
            showFieldError('import-newuser-password', T.location_name_required || 'Password required');
            return;
        }
        if (importNeedsModel && !$('#import-device-model').val()) {
            showFieldError('import-device-model', T.location_name_required || 'Model required');
            return;
        }

        btn.innerHTML = `<i data-feather="loader" class="mr-2"></i>${T.adding_location || 'Adding...'}`;
        btn.disabled = true;
        if (typeof feather !== 'undefined') feather.replace();

        const body = { import: importPreviewData };
        if (importNeedsPassword) body.new_user_password = $('#import-newuser-password').val();
        if (importNeedsModel) body.device_product_model_id = $('#import-device-model').val();

        fetch(APP_CONFIG.API.BASE_URL + '/locations/import', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        })
        .then(async function (res) {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error(data.message || 'Request failed');
                err.status = res.status;
                err.body   = data;
                throw err;
            }
            return data;
        })
        .then(function (response) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            btn.innerHTML = T.location_created || 'Location created';
            setTimeout(function () {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.innerHTML = T.import_button || 'Import';
                $('#add-location-modal').modal('hide');
                document.getElementById('add-location-form').reset();
                if (typeof onSuccessCallback === 'function') onSuccessCallback(response);
            }, 2000);
        })
        .catch(function (err) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-danger');
            btn.innerHTML = T.error_creating_location || 'Error creating location';
            setTimeout(function () {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-primary');
                btn.innerHTML = T.import_button || 'Import';
                refreshImportButton();
            }, 3000);
            handleApiError(err, 'importLocation');
        });
    }

    function handlePrimaryClick(e) {
        if (currentMode === 'import') {
            handleImport(e);
        } else {
            handleSubmit(e);
        }
    }

    function handleImportFile(e) {
        const file = e.target.files && e.target.files[0];
        $('#import-error').hide().text('');
        $('#import-preview').hide().empty();
        $('#import-newuser-group').hide();
        $('#import-device-model-group').hide();
        importPreviewData = null;
        importNeedsPassword = false;
        importNeedsModel = false;
        deviceCheck = null;
        refreshImportButton();
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (ev) {
            let parsed;
            try {
                parsed = JSON.parse(ev.target.result);
            } catch (err) {
                showImportError(T.import_invalid_json || 'Invalid JSON file.');
                return;
            }

            if (!parsed || typeof parsed !== 'object' || !parsed.settings || typeof parsed.settings !== 'object') {
                showImportError(T.import_invalid_json || 'Invalid JSON file.');
                return;
            }

            if (parsed.format_version !== '1.0') {
                showImportError(
                    (T.import_unsupported_version || 'Unsupported import format version') +
                    ' (' + escapeHtml(parsed.format_version ?? '—') + ').'
                );
                return;
            }

            importPreviewData = parsed;
            deviceCheck = null;
            renderImportPreview(parsed);
            if (parsed.device && parsed.device.mac_address) {
                fetchDeviceMacStatus(parsed.device.mac_address);
            }
        };
        reader.onerror = function () {
            showImportError(T.import_invalid_json || 'Invalid JSON file.');
        };
        reader.readAsText(file);
    }

    function onOff(value) {
        return value ? (T.on || 'On') : (T.off || 'Off');
    }

    function dash(value) {
        return (value === null || value === undefined || value === '') ? '—' : value;
    }

    function rows(pairs) {
        return pairs.map(function (p) {
            return (
                '<div class="d-flex justify-content-between"><span class="text-muted mr-3">' +
                escapeHtml(p[0]) + '</span><span class="text-right">' + escapeHtml(p[1]) + '</span></div>'
            );
        }).join('');
    }

    function section(title, body) {
        return (
            '<div class="mb-2"><div class="font-weight-bold small text-uppercase text-muted mb-1">' +
            escapeHtml(title) + '</div>' + body + '</div>'
        );
    }

    // Verdict markup for the access point, based on the MAC existence check.
    function deviceStatusMarkup() {
        if (deviceCheck === null) {
            return '<span class="text-muted">' + escapeHtml(T.import_device_checking || 'Checking…') + '</span>';
        }
        if (deviceCheck.exists) {
            return '<span class="text-success font-weight-bold">' +
                escapeHtml(T.import_device_exists || 'Already registered — will be reused (not added again).') + '</span>';
        }
        return '<span class="text-warning font-weight-bold">' +
            escapeHtml(T.import_device_new || 'Will be added to inventory and allocated to the owner.') + '</span>';
    }

    function updateDeviceStatusUI() {
        $('#import-device-status').html(deviceStatusMarkup());
        updateDeviceModelGroup();
    }

    // Lazily load the product models for the device-model dropdown.
    function loadProductModels() {
        if (productModelsLoaded) return;
        productModelsLoaded = true;
        const token = UserManager.getToken();
        fetch(APP_CONFIG.API.BASE_URL + '/firmware/models', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        })
        .then(function (r) { return r.ok ? r.json() : { data: [] }; })
        .then(function (res) {
            const models = res.data || [];
            let opts = `<option value="">${T.import_device_model_select || 'Select model...'}</option>`;
            models.forEach(function (m) {
                opts += `<option value="${m.id}">${escapeHtml(m.name)}${m.device_type ? ' (' + escapeHtml(m.device_type) + ')' : ''}</option>`;
            });
            $('#import-device-model').html(opts);
        })
        .catch(function () {
            productModelsLoaded = false; // allow a retry on the next showing
            $('#import-device-model').html(`<option value="">${T.import_device_model_select || 'Select model...'}</option>`);
        });
    }

    // The model dropdown is only relevant when a NEW device (unknown MAC) will be
    // added — its product model drives firmware, so the user must pick it.
    function updateDeviceModelGroup() {
        const hasNewDevice = !!(importPreviewData && importPreviewData.device &&
            importPreviewData.device.mac_address && deviceCheck && deviceCheck.exists === false);
        importNeedsModel = hasNewDevice;
        if (hasNewDevice) {
            loadProductModels();
            $('#import-device-model-group').show();
        } else {
            $('#import-device-model-group').hide();
            $('#import-device-model').val('');
        }
        refreshImportButton();
    }

    // Ask the backend whether this MAC is already a device/inventory item, so the
    // preview can show that it will be reused rather than added again.
    function fetchDeviceMacStatus(mac) {
        const token = UserManager.getToken();
        fetch(APP_CONFIG.API.BASE_URL + '/v1/devices/check-mac?mac=' + encodeURIComponent(mac), {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        })
        .then(function (r) { return r.ok ? r.json() : { exists: false }; })
        .then(function (d) { deviceCheck = { exists: !!d.exists, type: d.type, name: d.name }; updateDeviceStatusUI(); })
        .catch(function () { deviceCheck = { exists: false }; updateDeviceStatusUI(); });
    }

    function renderImportPreview(parsed) {
        const s = parsed.settings || {};
        const owner = parsed.owner;
        const source = parsed.source || {};
        let ownerWillCreate = false; // true → ask for a password for the new account

        // Owner — cross-verify the imported email against existing accounts.
        let ownerBody;
        if (owner && owner.email) {
            const name = owner.name || owner.email;
            const role = owner.role ? ' · ' + owner.role : '';
            const match = Array.isArray(loadedUsers)
                ? loadedUsers.find(function (u) {
                    return (u.email || '').toLowerCase() === String(owner.email).toLowerCase();
                })
                : null;

            let verdict;
            if (!Array.isArray(loadedUsers)) {
                // No user list available to cross-check (e.g. non-admin) — stay neutral.
                verdict = '<span class="text-muted">' + escapeHtml(T.import_owner_unverified || 'Will be assigned by email.') + '</span>';
            } else if (match) {
                verdict = '<span class="text-success font-weight-bold">' +
                    escapeHtml((T.import_owner_exists || 'Existing user') + ' — ' + (match.name || match.email)) + '</span>';
            } else {
                ownerWillCreate = true;
                verdict = '<span class="text-warning font-weight-bold">' +
                    escapeHtml(T.import_owner_will_create || 'No account — a new user will be created (password requested on confirm).') + '</span>';
            }

            ownerBody = rows([
                [T.import_owner || 'Owner', name + ' <' + owner.email + '>' + role],
            ]) + '<div class="small mt-1">' + verdict + '</div>';
        } else {
            ownerBody = '<div class="text-muted small font-italic">' +
                escapeHtml(T.import_owner_default || 'Will use the selected owner / current admin.') +
                '</div>';
        }

        // Device (access point) — added to inventory and allocated to the owner.
        const device = parsed.device;
        let deviceBody = null;
        if (device && (device.mac_address || device.serial_number)) {
            deviceBody = rows([
                [T.import_field_model || 'Model', dash(device.model)],
                [T.import_field_mac || 'MAC', dash(device.mac_address)],
                [T.import_field_serial || 'Serial', dash(device.serial_number)],
            ]) + '<div class="small mt-1" id="import-device-status">' + deviceStatusMarkup() + '</div>';
        }

        // Networks — interpret the legacy flat shape into our network model
        // (up to 8 networks, each Password / Open / Guest-Captive-Portal).
        // Order mirrors how they are created: captive_portal first, then password.
        const typeLabels = {
            password: T.import_type_password || 'Password (WPA2/WPA3)',
            captive_portal: T.import_type_captive_portal || 'Guest (Captive Portal)',
            open: T.import_type_open || 'Open (No security)',
        };
        const interpreted = [
            {
                type: 'captive_portal',
                enabled: s.captive_portal_enabled,
                rows: [
                    [T.import_field_ssid || 'SSID', dash(s.captive_portal_ssid)],
                    [T.import_field_auth || 'Auth', dash(s.captive_auth_method) + (s.captive_social_auth_method ? ' (' + s.captive_social_auth_method + ')' : '')],
                    [T.import_field_vlan || 'VLAN', dash(s.captive_portal_vlan) + ' · ' + dash(s.captive_portal_vlan_tagging)],
                    [T.import_field_ip || 'IP', dash(s.captive_portal_ip) + ' / ' + dash(s.captive_portal_netmask)],
                    [T.import_field_dhcp || 'DHCP', dash(s.captive_portal_dhcp_start) + ' – ' + dash(s.captive_portal_dhcp_end)],
                ],
            },
            {
                type: 'password',
                enabled: s.password_wifi_enabled,
                rows: [
                    [T.import_field_ssid || 'SSID', dash(s.password_wifi_ssid)],
                    [T.import_field_security || 'Security', dash(s.password_wifi_security)],
                    [T.import_field_vlan || 'VLAN', dash(s.password_wifi_vlan) + ' · ' + dash(s.password_wifi_vlan_tagging)],
                    [T.import_field_ip || 'IP', dash(s.password_wifi_ip) + ' / ' + dash(s.password_wifi_netmask)],
                    [T.import_field_dhcp || 'DHCP', dash(s.password_wifi_dhcp_start) + ' – ' + dash(s.password_wifi_dhcp_end)],
                ],
            },
        ];
        let networksBody =
            '<div class="text-muted small font-italic mb-2">' +
            escapeHtml((T.import_networks_intro || 'Interpreted as networks (up to 8 supported):').replace('{count}', interpreted.length)) +
            '</div>';
        interpreted.forEach(function (net, idx) {
            const badge = net.enabled
                ? '<span class="badge badge-success">' + escapeHtml(T.import_enabled || 'Enabled') + '</span>'
                : '<span class="badge badge-secondary">' + escapeHtml(T.import_disabled || 'Disabled') + '</span>';
            networksBody +=
                '<div class="border rounded p-2 mb-2">' +
                '<div class="d-flex justify-content-between align-items-center mb-1">' +
                '<span class="font-weight-bold small">' + escapeHtml((T.import_network_n || 'Network {n}').replace('{n}', idx + 1)) +
                ' · ' + escapeHtml(typeLabels[net.type]) + '</span>' + badge + '</div>' +
                rows(net.rows) + '</div>';
        });

        // Radio
        const radioBody = rows([
            [T.import_field_country || 'Country', dash(s.country_code)],
            [T.import_field_channel || 'Channel (2.4/5G)', dash(s.channel_2g) + ' / ' + dash(s.channel_5g)],
            [T.import_field_width || 'Width (2.4/5G)', dash(s.channel_width_2g) + ' / ' + dash(s.channel_width_5g)],
            [T.import_field_tx_power || 'TX Power (2.4/5G)', dash(s.transmit_power_2g) + ' / ' + dash(s.transmit_power_5g)],
        ]);

        // WAN + flags
        const wanBody = rows([
            [T.import_field_connection || 'Connection', dash(s.wan_connection_type)],
            [T.import_field_ip || 'IP', dash(s.wan_ip_address) + ' / ' + dash(s.wan_netmask)],
            [T.import_field_gateway || 'Gateway', dash(s.wan_gateway)],
            [T.import_field_dns || 'DNS', dash(s.wan_primary_dns) + ' / ' + dash(s.wan_secondary_dns)],
            [T.import_field_nat || 'NAT', onOff(s.wan_nat_enabled)],
            [T.import_field_mtu || 'MTU', dash(s.wan_mtu)],
            [T.import_field_vlan_enabled || 'VLAN', onOff(s.vlan_enabled)],
            [T.import_field_qos || 'QoS', onOff(s.qos_enabled)],
            [T.import_field_web_filter || 'Web filter', onOff(s.web_filter_enabled)],
        ]);

        // Schedule
        const wh = Array.isArray(parsed.captive_portal_working_hours) ? parsed.captive_portal_working_hours : [];
        const whEnabled = wh.filter(function (d) { return d.start_time && d.end_time; }).length;
        const hs = Array.isArray(parsed.captive_portal_hourly_schedule) ? parsed.captive_portal_hourly_schedule : [];
        const hsEnabled = hs.filter(function (h) { return h.enabled === true; }).length;
        const scheduleBody = rows([
            [T.import_field_working_hours || 'Working hours', whEnabled + '/' + wh.length + ' ' + (T.import_days_unit || 'days')],
            [T.import_field_hourly || 'Hourly schedule', hsEnabled + '/' + hs.length + ' ' + (T.import_hours_unit || 'hours')],
        ]);

        const html =
            '<div class="card"><div class="card-body p-3" style="max-height: 320px; overflow-y: auto;">' +
            '<div class="font-weight-bold mb-2">' + escapeHtml(T.import_preview_title || 'Import preview') + '</div>' +
            section(T.import_section_location || 'Location', rows([[T.import_field_name || 'Name', dash(source.location_name)]])) +
            section(T.import_owner || 'Owner', ownerBody) +
            (deviceBody ? section(T.import_section_device || 'Access Point', deviceBody) : '') +
            section(T.import_section_networks || 'Networks', networksBody) +
            section(T.import_section_radio || 'Radio', radioBody) +
            section(T.import_section_wan || 'WAN', wanBody) +
            section(T.import_section_schedule || 'Captive Portal Schedule', scheduleBody) +
            '</div></div>';

        $('#import-preview').html(html).show();

        // Only ask for a password when a brand-new owner account will be created.
        importNeedsPassword = ownerWillCreate;
        if (ownerWillCreate) {
            const hint = (T.import_newuser_hint || 'A new account will be created for {email}.')
                .replace('{email}', owner.email);
            $('#import-newuser-hint').text(hint);
            $('#import-newuser-group').show();
        } else {
            $('#import-newuser-group').hide();
            $('#import-newuser-password').val('');
        }

        refreshImportButton();
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

        $('#add-location-modal').on('hidden.bs.modal', function () {
            resetImport();
            setMode('manual');
        });

        const importInput = document.getElementById('import-file');
        if (importInput) importInput.addEventListener('change', handleImportFile);

        $('#add-location-mode').on('click', 'button[data-mode]', function () {
            setMode($(this).data('mode'));
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

        document.getElementById('add-location-btn').addEventListener('click', handlePrimaryClick);

        $('#import-newuser-password').on('input', refreshImportButton);
        $('#import-device-model').on('change', refreshImportButton);

        // Prevent ENTER key inside form inputs from submitting the form
        // (which would navigate the page and abort any in-flight requests).
        const form = document.getElementById('add-location-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                handlePrimaryClick(e);
            });
        }
    }

    function open(opts = {}) {
        wireOnce();
        onSuccessCallback = opts.onSuccess || null;
        setMode('manual');
        $('#add-location-modal').modal('show');
    }

    document.addEventListener('DOMContentLoaded', wireOnce);

    return { open };
})();
