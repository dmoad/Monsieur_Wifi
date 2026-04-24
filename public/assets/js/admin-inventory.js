// Admin inventory management
const t = window.APP_I18N.admin_inventory;

document.addEventListener('DOMContentLoaded', function() {
    // Wait for UserManager to be available
    if (typeof UserManager === 'undefined') {
        console.error('UserManager not loaded');
        window.location.href = '/';
        return;
    }

    const token = UserManager.getToken();
    const user = UserManager.getUser();

    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    // Check if user is admin or superadmin
    if (!UserManager.isAdminOrAbove()) {
        toastr.error(t.no_permission);
        window.location.href = t.dashboard_url;
        return;
    }

    loadSummary();
    loadInventory();
});

async function loadSummary() {
    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/summary`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (!response.ok) {
            throw new Error('Failed to load summary');
        }

        const data = await response.json();
        const summary = data.summary;

        document.getElementById('total-products').textContent = summary.total_products;
        document.getElementById('out-of-stock').textContent = summary.out_of_stock;
        document.getElementById('low-stock').textContent = summary.low_stock;
        document.getElementById('total-value').textContent = `€${parseFloat(summary.total_inventory_value).toFixed(2)}`;
    } catch (error) {
        console.error('Error loading summary:', error);
        toastr.error(t.load_summary_failed);
    }
}

async function loadInventory() {
    const token = UserManager.getToken();
    const stockStatus = document.getElementById('stock-status-filter').value;
    const search = document.getElementById('search').value;

    let url = `${APP_CONFIG.API.BASE_URL}/v1/admin/inventory?`;
    if (stockStatus) url += `stock_status=${stockStatus}&`;
    if (search) url += `search=${search}&`;

    try {
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (response.status === 403) {
            toastr.error(t.no_permission);
            window.location.href = t.dashboard_url;
            return;
        }

        if (!response.ok) {
            throw new Error('Failed to load inventory');
        }

        const data = await response.json();
        const products = data.products?.data || data.products || [];
        displayInventory(products);
    } catch (error) {
        console.error('Error loading inventory:', error);
        document.getElementById('inventory-loading').style.display = 'none';
        toastr.error(t.load_inventory_failed.replace('{message}', error.message));
    }
}

function displayInventory(products) {
    document.getElementById('inventory-loading').style.display = 'none';
    const container = document.getElementById('inventory-list');

    if (products.length === 0) {
        container.innerHTML = `<div class="card"><div class="card-body text-center">${t.no_products}</div></div>`;
        return;
    }

    container.innerHTML = products.map(product => {
        const inventory = product.inventory || { quantity: 0, reserved_quantity: 0, available_quantity: 0, low_stock_threshold: 5 };
        const stockStatus = getStockStatus(inventory);
        const primaryImage = product.images && product.images.length > 0 ? product.images[0].image_url : '/assets/placeholder.jpg';

        return `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-1">
                        <img src="${primaryImage}" alt="${product.name}" class="img-fluid rounded" style="max-height: 60px;">
                    </div>
                    <div class="col-md-3">
                        <strong>${product.name}</strong><br>
                        <small class="text-muted">SKU: ${product.sku || 'N/A'}</small>
                    </div>
                    <div class="col-md-2">
                        <strong>${t.label_in_stock}</strong> ${inventory.quantity}<br>
                        <small class="text-muted">${t.label_reserved} ${inventory.reserved_quantity}</small>
                    </div>
                    <div class="col-md-2">
                        <strong>${t.label_available}</strong> ${inventory.available_quantity}<br>
                        <small class="text-muted">${t.label_threshold} ${inventory.low_stock_threshold}</small>
                    </div>
                    <div class="col-md-2">
                        ${stockStatus}
                    </div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-sm btn-success" onclick="viewDevices(${product.id}, '${product.name}')" title="${t.btn_add_view_devices_title}">
                            <i data-feather="plus-circle"></i> ${t.btn_add_view_devices}
                        </button>

                    </div>
                </div>
            </div>
        </div>
    `}).join('');

    // Re-initialize feather icons
    if (typeof feather !== 'undefined') feather.replace();
}

function getStockStatus(inventory) {
    if (inventory.quantity <= 0) {
        return `<span class="badge badge-danger">${t.badge_out_of_stock}</span>`;
    } else if (inventory.quantity <= inventory.low_stock_threshold) {
        return `<span class="badge badge-warning">${t.badge_low_stock}</span>`;
    } else {
        return `<span class="badge badge-success">${t.badge_in_stock}</span>`;
    }
}

function showUpdateModal(productId, productName, currentStock, currentThreshold) {
    document.getElementById('modal-content').innerHTML = `
        <div class="inventory-settings-modal">
            <h5 class="mb-3">${productName}</h5>

            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i data-feather="info" style="width: 20px; height: 20px;" class="mr-2"></i>
                    <div>
                        <strong>${t.device_based_tracking}</strong><br>
                        <small>${t.device_based_tracking_desc}</small>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i data-feather="package"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">${currentStock}</div>
                            <div class="stat-label">${t.label_devices_in_stock}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i data-feather="alert-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">${currentThreshold}</div>
                            <div class="stat-label">${t.label_low_stock_threshold}</div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="update-inventory-form">
                <input type="hidden" id="product-id" value="${productId}">

                <div class="form-group">
                    <label class="font-weight-bold">${t.label_low_stock_threshold}</label>
                    <input type="number" id="threshold" class="form-control" value="${currentThreshold}" min="0">
                    <small class="text-muted">${t.threshold_hint}</small>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <p class="mb-2"><strong>${t.modify_stock_heading}</strong></p>
                        <button type="button" class="btn btn-primary btn-block" onclick="$('#inventory-modal').modal('hide'); viewDevices(${productId}, '${productName}');">
                            <i data-feather="plus-circle"></i> ${t.btn_add_manage_devices}
                        </button>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">${t.btn_cancel}</button>
                    <button type="button" class="btn btn-success" onclick="saveThreshold()">${t.btn_save_threshold}</button>
                </div>
            </form>
        </div>
    `;

    $('#inventory-modal').modal('show');
    if (typeof feather !== 'undefined') feather.replace();
}

async function saveThreshold() {
    const token = UserManager.getToken();
    const productId = document.getElementById('product-id').value;
    const threshold = document.getElementById('threshold').value;

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/threshold`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ threshold: parseInt(threshold) })
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (response.ok) {
            toastr.success(t.threshold_updated);
            $('#inventory-modal').modal('hide');
            loadSummary();
            loadInventory();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || t.threshold_update_failed);
        }
    } catch (error) {
        console.error('Error saving threshold:', error);
        toastr.error(t.save_failed_prefix.replace('{message}', error.message));
    }
}

// View individual devices for a product
let currentDevicesPage = 1;
let currentDevicesProductId = null;
let currentDevicesProductName = '';
const DEVICES_PER_PAGE = 10;

async function viewDevices(productId, productName, page = 1) {
    const token = UserManager.getToken();
    currentDevicesPage = page;
    currentDevicesProductId = productId;
    currentDevicesProductName = productName;

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (!response.ok) {
            throw new Error('Failed to load devices');
        }

        const data = await response.json();
        const allItems = data.items || [];

        displayDevicesModal(productId, productName, allItems, page);
    } catch (error) {
        console.error('Error loading devices:', error);
        toastr.error(t.load_devices_failed_prefix.replace('{message}', error.message));
    }
}

function displayDevicesModal(productId, productName, allItems, page = 1) {
    const totalItems = allItems.length;
    const totalPages = Math.ceil(totalItems / DEVICES_PER_PAGE);
    const startIndex = (page - 1) * DEVICES_PER_PAGE;
    const endIndex = startIndex + DEVICES_PER_PAGE;
    const items = allItems.slice(startIndex, endIndex);

    const itemsHtml = items.length > 0 ? items.map(item => {
        const statusBadge = getDeviceStatusBadge(item.status);
        return `
            <tr>
                <td><code>${item.mac_address}</code></td>
                <td><code>${item.serial_number}</code></td>
                <td>${statusBadge}</td>
                <td>
                    <small class="text-muted">${item.notes || '-'}</small>
                </td>
                <td>
                    ${item.status !== 'sold' ? `
                        <button class="btn btn-xs btn-primary" onclick="editDevice(${productId}, ${item.id}, '${item.mac_address}', '${item.serial_number}', '${item.status}', '${item.notes || ''}')" title="${t.btn_edit}">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" onclick="deleteDevice(${productId}, ${item.id})" title="${t.btn_delete}">
                            <i data-feather="trash-2"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }).join('') : `<tr><td colspan="5" class="text-center">${t.no_devices}</td></tr>`;

    // Pagination controls
    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml = `
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <div class="text-muted">
                    <small>${t.pagination_page} ${page} ${t.pagination_of} ${totalPages} (${totalItems} ${t.pagination_devices})</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" onclick="viewDevices(${productId}, '${productName}', ${page - 1})" ${page === 1 ? 'disabled' : ''}>
                        <i data-feather="chevron-left" style="width: 14px; height: 14px;"></i> ${t.btn_previous}
                    </button>
                    <button class="btn btn-outline-secondary" onclick="viewDevices(${productId}, '${productName}', ${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                        ${t.btn_next} <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
            </div>
        `;
    }

    document.getElementById('modal-content').innerHTML = `
        <h5>${productName}</h5>
        <p class="text-muted">${t.devices_modal_desc}</p>

        <div class="mb-3">
            <button class="btn btn-success btn-sm mr-2" onclick="showAddDeviceForm(${productId})">
                <i data-feather="plus"></i> ${t.btn_add_device}
            </button>
            <button class="btn btn-info btn-sm" onclick="showCsvUploadForm(${productId})">
                <i data-feather="upload"></i> ${t.btn_import_csv}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>${t.col_mac_address}</th>
                        <th>${t.col_serial_number}</th>
                        <th>${t.col_status}</th>
                        <th>${t.col_notes}</th>
                        <th>${t.col_actions}</th>
                    </tr>
                </thead>
                <tbody id="devices-table-body">
                    ${itemsHtml}
                </tbody>
            </table>
        </div>

        ${paginationHtml}

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">${t.btn_close}</button>
        </div>
    `;

    $('#inventory-modal').modal('show');
    if (typeof feather !== 'undefined') feather.replace();
}

function getDeviceStatusBadge(status) {
    const badges = {
        'available': `<span class="badge badge-success">${t.device_status_available}</span>`,
        'reserved': `<span class="badge badge-warning">${t.device_status_reserved}</span>`,
        'sold': `<span class="badge badge-secondary">${t.device_status_sold}</span>`,
        'defective': `<span class="badge badge-danger">${t.device_status_defective}</span>`
    };
    return badges[status] || status;
}

function showAddDeviceForm(productId) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${t.form_add_heading}</h5>
        <form id="add-device-form">
            <div class="form-group">
                <label>${t.col_mac_address} *</label>
                <input type="text" id="mac-address" class="form-control" required placeholder="00-11-22-33-44-55" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
                <small class="form-text text-muted">${t.mac_formats_hint}</small>
            </div>

            <div class="form-group">
                <label>${t.col_serial_number} *</label>
                <input type="text" id="serial-number" class="form-control" required placeholder="SN123456789">
            </div>

            <div class="form-group">
                <label>${t.col_notes}</label>
                <textarea id="device-notes" class="form-control" rows="2" placeholder="${t.notes_placeholder}"></textarea>
            </div>

            <div class="form-group">
                <label>${t.form_received_date}</label>
                <input type="date" id="received-at" class="form-control" value="${new Date().toISOString().split('T')[0]}">
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${t.btn_cancel}</button>
                <button type="button" class="btn btn-success" onclick="addDevice(${productId})">${t.btn_add_device_submit}</button>
            </div>
        </form>
    `;
}

async function addDevice(productId) {
    const token = UserManager.getToken();
    const macAddress = normalizeMacAddress(document.getElementById('mac-address').value);
    const serialNumber = document.getElementById('serial-number').value;
    const notes = document.getElementById('device-notes').value;
    const receivedAt = document.getElementById('received-at').value;

    if (!macAddress || !serialNumber) {
        toastr.error(t.mac_serial_required);
        return;
    }

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mac_address: macAddress,
                serial_number: serialNumber,
                notes: notes,
                received_at: receivedAt
            })
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        const data = await response.json();

        if (response.ok) {
            toastr.success(t.device_added);
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || t.add_device_failed);
            }
        }
    } catch (error) {
        console.error('Error adding device:', error);
        toastr.error(t.add_device_failed_prefix.replace('{message}', error.message));
    }
}

function editDevice(productId, itemId, macAddress, serialNumber, status, notes) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${t.form_edit_heading}</h5>
        <form id="edit-device-form">
            <input type="hidden" id="item-id" value="${itemId}">

            <div class="form-group">
                <label>${t.col_mac_address} *</label>
                <input type="text" id="mac-address" class="form-control" required value="${macAddress}" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
            </div>

            <div class="form-group">
                <label>${t.col_serial_number} *</label>
                <input type="text" id="serial-number" class="form-control" required value="${serialNumber}">
            </div>

            <div class="form-group">
                <label>${t.col_status}</label>
                <select id="device-status" class="form-control">
                    <option value="available" ${status === 'available' ? 'selected' : ''}>${t.device_status_available}</option>
                    <option value="reserved" ${status === 'reserved' ? 'selected' : ''}>${t.device_status_reserved}</option>
                    <option value="sold" ${status === 'sold' ? 'selected' : ''}>${t.device_status_sold}</option>
                    <option value="defective" ${status === 'defective' ? 'selected' : ''}>${t.device_status_defective}</option>
                </select>
            </div>

            <div class="form-group">
                <label>${t.col_notes}</label>
                <textarea id="device-notes" class="form-control" rows="2">${notes}</textarea>
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${t.btn_cancel}</button>
                <button type="button" class="btn btn-primary" onclick="updateDevice(${productId}, ${itemId})">${t.btn_update_device}</button>
            </div>
        </form>
    `;
}

async function updateDevice(productId, itemId) {
    const token = UserManager.getToken();
    const macAddress = normalizeMacAddress(document.getElementById('mac-address').value);
    const serialNumber = document.getElementById('serial-number').value;
    const status = document.getElementById('device-status').value;
    const notes = document.getElementById('device-notes').value;

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items/${itemId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mac_address: macAddress,
                serial_number: serialNumber,
                status: status,
                notes: notes
            })
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        const data = await response.json();

        if (response.ok) {
            toastr.success(t.device_updated);
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || t.update_device_failed);
            }
        }
    } catch (error) {
        console.error('Error updating device:', error);
        toastr.error(t.update_device_failed_prefix.replace('{message}', error.message));
    }
}

async function deleteDevice(productId, itemId) {
    const ok = await MwConfirm.open({
        title: t.confirm_delete_device_title || 'Delete device?',
        message: t.confirm_delete_device,
        confirmText: t.delete_btn || 'Delete',
        cancelText: (window.APP_I18N && window.APP_I18N.common && window.APP_I18N.common.cancel) || 'Cancel',
        destructive: true,
    });
    if (!ok) return;

    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (response.ok) {
            toastr.success(t.device_deleted);
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const data = await response.json();
            toastr.error(data.message || t.delete_device_failed);
        }
    } catch (error) {
        console.error('Error deleting device:', error);
        toastr.error(t.delete_device_failed_prefix.replace('{message}', error.message));
    }
}

function showCsvUploadForm(productId) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${t.csv_upload_heading}</h5>

        <div class="mb-3">
            <button class="btn btn-outline-primary btn-sm" onclick="downloadCsvTemplate()">
                <i data-feather="download"></i> ${t.btn_download_template}
            </button>
        </div>

        <div class="alert alert-info">
            <strong>${t.csv_format_label}</strong><br>
            ${t.csv_format_desc}
            <ul class="mb-0 mt-2">
                <li><code>mac_address</code> - ${t.csv_col_mac_desc}</li>
                <li><code>serial_number</code> - ${t.csv_col_serial_desc}</li>
                <li><code>notes</code> - ${t.csv_col_notes_desc}</li>
            </ul>
        </div>

        <div class="alert alert-secondary">
            <strong>${t.csv_example_label}</strong><br>
            <code>mac_address,serial_number,notes</code><br>
            <code>00-11-22-33-44-55,SN123456,Device 1</code><br>
            <code>00-11-22-33-44-66,SN123457,Device 2</code><br>
            <small class="text-muted mt-2 d-block">${t.csv_mac_normalize_note}</small>
        </div>

        <form id="csv-upload-form">
            <div class="form-group">
                <label>${t.csv_select_label} *</label>
                <input type="file" id="csv-file" class="form-control-file" accept=".csv" required>
                <small class="form-text text-muted">${t.csv_max_size}</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="skip-duplicates" checked>
                    <label class="custom-control-label" for="skip-duplicates">
                        ${t.csv_skip_duplicates}
                    </label>
                </div>
            </div>

            <div id="csv-upload-progress" style="display: none;">
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <p class="text-center text-muted" id="csv-upload-status"></p>
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${t.btn_cancel}</button>
                <button type="button" class="btn btn-primary" onclick="uploadCsvFile(${productId})" id="upload-csv-btn">
                    <i data-feather="upload"></i> ${t.btn_upload_import}
                </button>
            </div>
        </form>
    `;

    if (typeof feather !== 'undefined') feather.replace();
}

async function uploadCsvFile(productId) {
    const fileInput = document.getElementById('csv-file');
    const skipDuplicates = document.getElementById('skip-duplicates').checked;
    const uploadBtn = document.getElementById('upload-csv-btn');
    const progressDiv = document.getElementById('csv-upload-progress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const statusText = document.getElementById('csv-upload-status');

    if (!fileInput.files || !fileInput.files[0]) {
        toastr.error(t.csv_select_file_error);
        return;
    }

    const file = fileInput.files[0];

    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        toastr.error(t.csv_too_large);
        return;
    }

    // Check file type
    if (!file.name.endsWith('.csv')) {
        toastr.error(t.csv_invalid_file);
        return;
    }

    const token = UserManager.getToken();
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('skip_duplicates', skipDuplicates ? '1' : '0');

    try {
        uploadBtn.disabled = true;
        progressDiv.style.display = 'block';
        progressBar.style.width = '0%';
        statusText.textContent = t.csv_uploading;

        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items/import-csv`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        });

        progressBar.style.width = '50%';
        statusText.textContent = t.csv_processing;

        if (response.status === 401) {
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        const data = await response.json();
        progressBar.style.width = '100%';

        if (response.ok) {
            loadSummary();
            loadInventory();

            // Show detailed results modal
            showCsvImportResults(productId, data);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || t.csv_import_failed);
            }
            uploadBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error uploading CSV:', error);
        toastr.error(t.csv_upload_failed_prefix.replace('{message}', error.message));
        uploadBtn.disabled = false;
        progressDiv.style.display = 'none';
    }
}

function showCsvImportResults(productId, data) {
    const hasErrors = data.error_details && data.error_details.length > 0;
    const statusIcon = data.errors === 0 ? '✅' : '⚠️';

    let resultsHtml = `
        <div class="csv-import-results">
            <div class="alert ${data.errors === 0 ? 'alert-success' : 'alert-warning'}">
                <h5 class="mb-2">${statusIcon} ${t.import_results_heading}</h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-success">${data.imported}</div>
                            <div class="stat-label">${t.stat_imported}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-warning">${data.skipped}</div>
                            <div class="stat-label">${t.stat_duplicates}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-danger">${data.errors}</div>
                            <div class="stat-label">${t.stat_errors}</div>
                        </div>
                    </div>
                </div>
            </div>
    `;

    if (hasErrors) {
        resultsHtml += `
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <strong>${t.error_details}</strong>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <ul class="mb-0 list-unstyled">
                        ${data.error_details.map(err => `
                            <li class="mb-2">
                                <i data-feather="alert-circle" class="text-danger" style="width: 14px; height: 14px;"></i>
                                <code>${err}</code>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    resultsHtml += `
            <div class="text-right mt-3">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '${currentDevicesProductName}')">
                    ${t.btn_back_to_list}
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    ${t.btn_close}
                </button>
            </div>
        </div>
    `;

    document.getElementById('modal-content').innerHTML = resultsHtml;

    if (typeof feather !== 'undefined') feather.replace();
}

function normalizeMacAddress(macAddress) {
    if (!macAddress) return macAddress;
    // Convert to uppercase and replace : with -
    return macAddress.toUpperCase().replace(/:/g, '-');
}

function downloadCsvTemplate() {
    const csvContent = "mac_address,serial_number,notes\n00-11-22-33-44-55,SN123456,Sample device 1\n00-11-22-33-44-66,SN123457,Sample device 2";
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'inventory_import_template.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    toastr.success(t.csv_template_downloaded);
}
