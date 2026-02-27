// Admin inventory management
const PAGE_LOCALE = document.documentElement.lang || 'en';

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
        console.log('No authentication found, redirecting...');
        window.location.href = '/';
        return;
    }
    
    // Check if user is admin or superadmin
    if (!UserManager.isAdminOrAbove()) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Vous n\'avez pas la permission d\'accéder à cette page.' : 'You do not have permission to access this page.');
        window.location.href = `/${PAGE_LOCALE}/dashboard`;
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
            toastr.error('Session expired. Please login again.');
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
        toastr.error('Failed to load inventory summary');
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.status === 403) {
            toastr.error('You do not have permission to access this page.');
            window.location.href = '/en/dashboard';
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
        toastr.error('Failed to load inventory: ' + error.message);
    }
}

function displayInventory(products) {
    document.getElementById('inventory-loading').style.display = 'none';
    const container = document.getElementById('inventory-list');
    
    if (products.length === 0) {
        container.innerHTML = `<div class="card"><div class="card-body text-center">${PAGE_LOCALE === 'fr' ? 'Aucun produit trouvé' : 'No products found'}</div></div>`;
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
                        <strong>In Stock:</strong> ${inventory.quantity}<br>
                        <small class="text-muted">Reserved: ${inventory.reserved_quantity}</small>
                    </div>
                    <div class="col-md-2">
                        <strong>Available:</strong> ${inventory.available_quantity}<br>
                        <small class="text-muted">Threshold: ${inventory.low_stock_threshold}</small>
                    </div>
                    <div class="col-md-2">
                        ${stockStatus}
                    </div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-sm btn-success" onclick="viewDevices(${product.id}, '${product.name}')" title="${PAGE_LOCALE === 'fr' ? 'Voir/Ajouter des appareils individuels' : 'View/Add Individual Devices'}">
                            <i data-feather="plus-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Ajouter/Voir Appareils' : 'Add/View Devices'}
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
        return `<span class="badge badge-danger">${PAGE_LOCALE === 'fr' ? 'Rupture de Stock' : 'Out of Stock'}</span>`;
    } else if (inventory.quantity <= inventory.low_stock_threshold) {
        return `<span class="badge badge-warning">${PAGE_LOCALE === 'fr' ? 'Stock Faible' : 'Low Stock'}</span>`;
    } else {
        return `<span class="badge badge-success">${PAGE_LOCALE === 'fr' ? 'En Stock' : 'In Stock'}</span>`;
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
                        <strong>${PAGE_LOCALE === 'fr' ? 'Suivi basé sur les appareils' : 'Device-Based Tracking'}</strong><br>
                        <small>${PAGE_LOCALE === 'fr' 
                            ? 'La quantité d\'inventaire est automatiquement calculée en fonction des appareils individuels que vous ajoutez avec des adresses MAC et des numéros de série.'
                            : 'Inventory quantity is automatically calculated based on individual devices you add with MAC addresses and serial numbers.'}</small>
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
                            <div class="stat-label">${PAGE_LOCALE === 'fr' ? 'Appareils en Stock' : 'Devices in Stock'}</div>
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
                            <div class="stat-label">${PAGE_LOCALE === 'fr' ? 'Seuil de Stock Faible' : 'Low Stock Threshold'}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="update-inventory-form">
                <input type="hidden" id="product-id" value="${productId}">
                
                <div class="form-group">
                    <label class="font-weight-bold">${PAGE_LOCALE === 'fr' ? 'Seuil de Stock Faible' : 'Low Stock Threshold'}</label>
                    <input type="number" id="threshold" class="form-control" value="${currentThreshold}" min="0">
                    <small class="text-muted">${PAGE_LOCALE === 'fr' 
                        ? 'Vous serez alerté lorsque le nombre d\'appareils disponibles est inférieur ou égal à ce seuil.'
                        : 'You will be alerted when available device count is at or below this threshold.'}</small>
                </div>
                
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <p class="mb-2"><strong>${PAGE_LOCALE === 'fr' ? 'Pour modifier la quantité en stock :' : 'To modify stock quantity:'}</strong></p>
                        <button type="button" class="btn btn-primary btn-block" onclick="$('#inventory-modal').modal('hide'); viewDevices(${productId}, '${productName}');">
                            <i data-feather="plus-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Ajouter/Gérer les Appareils Individuels' : 'Add/Manage Individual Devices'}
                        </button>
                    </div>
                </div>
                
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">${PAGE_LOCALE === 'fr' ? 'Annuler' : 'Cancel'}</button>
                    <button type="button" class="btn btn-success" onclick="saveThreshold()">${PAGE_LOCALE === 'fr' ? 'Enregistrer le Seuil' : 'Save Threshold'}</button>
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Seuil de stock mis à jour avec succès' : 'Stock threshold updated successfully');
            $('#inventory-modal').modal('hide');
            loadSummary();
            loadInventory();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || (PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour du seuil' : 'Failed to update threshold'));
        }
    } catch (error) {
        console.error('Error saving threshold:', error);
        toastr.error((PAGE_LOCALE === 'fr' ? 'Échec de l\'enregistrement: ' : 'Failed to save: ') + error.message);
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
            toastr.error('Session expired. Please login again.');
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
        toastr.error('Failed to load devices: ' + error.message);
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
                        <button class="btn btn-xs btn-primary" onclick="editDevice(${productId}, ${item.id}, '${item.mac_address}', '${item.serial_number}', '${item.status}', '${item.notes || ''}')" title="${PAGE_LOCALE === 'fr' ? 'Modifier' : 'Edit'}">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" onclick="deleteDevice(${productId}, ${item.id})" title="${PAGE_LOCALE === 'fr' ? 'Supprimer' : 'Delete'}">
                            <i data-feather="trash-2"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }).join('') : `<tr><td colspan="5" class="text-center">${PAGE_LOCALE === 'fr' ? 'Aucun appareil trouvé' : 'No devices found'}</td></tr>`;
    
    // Pagination controls
    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml = `
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <div class="text-muted">
                    <small>${PAGE_LOCALE === 'fr' ? 'Page' : 'Page'} ${page} ${PAGE_LOCALE === 'fr' ? 'sur' : 'of'} ${totalPages} (${totalItems} ${PAGE_LOCALE === 'fr' ? 'appareils' : 'devices'})</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" onclick="viewDevices(${productId}, '${productName}', ${page - 1})" ${page === 1 ? 'disabled' : ''}>
                        <i data-feather="chevron-left" style="width: 14px; height: 14px;"></i> ${PAGE_LOCALE === 'fr' ? 'Précédent' : 'Previous'}
                    </button>
                    <button class="btn btn-outline-secondary" onclick="viewDevices(${productId}, '${productName}', ${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                        ${PAGE_LOCALE === 'fr' ? 'Suivant' : 'Next'} <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    document.getElementById('modal-content').innerHTML = `
        <h5>${productName}</h5>
        <p class="text-muted">${PAGE_LOCALE === 'fr' ? 'Gérez les appareils individuels avec leurs adresses MAC et numéros de série' : 'Manage individual devices with their MAC addresses and serial numbers'}</p>
        
        <div class="mb-3">
            <button class="btn btn-success btn-sm mr-2" onclick="showAddDeviceForm(${productId})">
                <i data-feather="plus"></i> ${PAGE_LOCALE === 'fr' ? 'Ajouter un Appareil' : 'Add Device'}
            </button>
            <button class="btn btn-info btn-sm" onclick="showCsvUploadForm(${productId})">
                <i data-feather="upload"></i> ${PAGE_LOCALE === 'fr' ? 'Importer CSV' : 'Import CSV'}
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>${PAGE_LOCALE === 'fr' ? 'Adresse MAC' : 'MAC Address'}</th>
                        <th>${PAGE_LOCALE === 'fr' ? 'Numéro de Série' : 'Serial Number'}</th>
                        <th>${PAGE_LOCALE === 'fr' ? 'Statut' : 'Status'}</th>
                        <th>${PAGE_LOCALE === 'fr' ? 'Notes' : 'Notes'}</th>
                        <th>${PAGE_LOCALE === 'fr' ? 'Actions' : 'Actions'}</th>
                    </tr>
                </thead>
                <tbody id="devices-table-body">
                    ${itemsHtml}
                </tbody>
            </table>
        </div>
        
        ${paginationHtml}
        
        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">${PAGE_LOCALE === 'fr' ? 'Fermer' : 'Close'}</button>
        </div>
    `;
    
    $('#inventory-modal').modal('show');
    if (typeof feather !== 'undefined') feather.replace();
}

function getDeviceStatusBadge(status) {
    const badges = PAGE_LOCALE === 'fr' ? {
        'available': '<span class="badge badge-success">Disponible</span>',
        'reserved': '<span class="badge badge-warning">Réservé</span>',
        'sold': '<span class="badge badge-secondary">Vendu</span>',
        'defective': '<span class="badge badge-danger">Défectueux</span>'
    } : {
        'available': '<span class="badge badge-success">Available</span>',
        'reserved': '<span class="badge badge-warning">Reserved</span>',
        'sold': '<span class="badge badge-secondary">Sold</span>',
        'defective': '<span class="badge badge-danger">Defective</span>'
    };
    return badges[status] || status;
}

function showAddDeviceForm(productId) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${PAGE_LOCALE === 'fr' ? 'Ajouter un Nouvel Appareil' : 'Add New Device'}</h5>
        <form id="add-device-form">
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Adresse MAC' : 'MAC Address'} *</label>
                <input type="text" id="mac-address" class="form-control" required placeholder="00-11-22-33-44-55" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
                <small class="form-text text-muted">${PAGE_LOCALE === 'fr' ? 'Formats acceptés: 00-11-22-33-44-55 ou 00:11:22:33:44:55 (normalisé automatiquement)' : 'Accepted formats: 00-11-22-33-44-55 or 00:11:22:33:44:55 (auto-normalized)'}</small>
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Numéro de Série' : 'Serial Number'} *</label>
                <input type="text" id="serial-number" class="form-control" required placeholder="SN123456789">
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Notes' : 'Notes'}</label>
                <textarea id="device-notes" class="form-control" rows="2" placeholder="${PAGE_LOCALE === 'fr' ? 'Notes optionnelles sur cet appareil' : 'Optional notes about this device'}"></textarea>
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Date de Réception' : 'Received Date'}</label>
                <input type="date" id="received-at" class="form-control" value="${new Date().toISOString().split('T')[0]}">
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${PAGE_LOCALE === 'fr' ? 'Annuler' : 'Cancel'}</button>
                <button type="button" class="btn btn-success" onclick="addDevice(${productId})">${PAGE_LOCALE === 'fr' ? 'Ajouter l\'Appareil' : 'Add Device'}</button>
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
        toastr.error(PAGE_LOCALE === 'fr' ? 'L\'adresse MAC et le numéro de série sont requis' : 'MAC Address and Serial Number are required');
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Appareil ajouté avec succès' : 'Device added successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de l\'ajout de l\'appareil' : 'Failed to add device'));
            }
        }
    } catch (error) {
        console.error('Error adding device:', error);
        toastr.error((PAGE_LOCALE === 'fr' ? 'Échec de l\'ajout de l\'appareil: ' : 'Failed to add device: ') + error.message);
    }
}

function editDevice(productId, itemId, macAddress, serialNumber, status, notes) {
    const statusOptions = PAGE_LOCALE === 'fr' ? {
        'available': 'Disponible',
        'reserved': 'Réservé',
        'sold': 'Vendu',
        'defective': 'Défectueux'
    } : {
        'available': 'Available',
        'reserved': 'Reserved',
        'sold': 'Sold',
        'defective': 'Defective'
    };
    
    document.getElementById('modal-content').innerHTML = `
        <h5>${PAGE_LOCALE === 'fr' ? 'Modifier l\'Appareil' : 'Edit Device'}</h5>
        <form id="edit-device-form">
            <input type="hidden" id="item-id" value="${itemId}">
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Adresse MAC' : 'MAC Address'} *</label>
                <input type="text" id="mac-address" class="form-control" required value="${macAddress}" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Numéro de Série' : 'Serial Number'} *</label>
                <input type="text" id="serial-number" class="form-control" required value="${serialNumber}">
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Statut' : 'Status'}</label>
                <select id="device-status" class="form-control">
                    <option value="available" ${status === 'available' ? 'selected' : ''}>${statusOptions.available}</option>
                    <option value="reserved" ${status === 'reserved' ? 'selected' : ''}>${statusOptions.reserved}</option>
                    <option value="sold" ${status === 'sold' ? 'selected' : ''}>${statusOptions.sold}</option>
                    <option value="defective" ${status === 'defective' ? 'selected' : ''}>${statusOptions.defective}</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Notes' : 'Notes'}</label>
                <textarea id="device-notes" class="form-control" rows="2">${notes}</textarea>
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${PAGE_LOCALE === 'fr' ? 'Annuler' : 'Cancel'}</button>
                <button type="button" class="btn btn-primary" onclick="updateDevice(${productId}, ${itemId})">${PAGE_LOCALE === 'fr' ? 'Mettre à Jour' : 'Update Device'}</button>
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Appareil mis à jour avec succès' : 'Device updated successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour de l\'appareil' : 'Failed to update device'));
            }
        }
    } catch (error) {
        console.error('Error updating device:', error);
        toastr.error((PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour de l\'appareil: ' : 'Failed to update device: ') + error.message);
    }
}

async function deleteDevice(productId, itemId) {
    if (!confirm(PAGE_LOCALE === 'fr' ? 'Êtes-vous sûr de vouloir supprimer cet appareil?' : 'Are you sure you want to delete this device?')) {
        return;
    }
    
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Appareil supprimé avec succès' : 'Device deleted successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, currentDevicesProductName || '', currentDevicesPage);
        } else {
            const data = await response.json();
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de la suppression de l\'appareil' : 'Failed to delete device'));
        }
    } catch (error) {
        console.error('Error deleting device:', error);
        toastr.error((PAGE_LOCALE === 'fr' ? 'Échec de la suppression de l\'appareil: ' : 'Failed to delete device: ') + error.message);
    }
}

function showCsvUploadForm(productId) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${PAGE_LOCALE === 'fr' ? 'Importer des Appareils depuis CSV' : 'Import Devices from CSV'}</h5>
        
        <div class="mb-3">
            <button class="btn btn-outline-primary btn-sm" onclick="downloadCsvTemplate()">
                <i data-feather="download"></i> ${PAGE_LOCALE === 'fr' ? 'Télécharger le Modèle CSV' : 'Download CSV Template'}
            </button>
        </div>
        
        <div class="alert alert-info">
            <strong>${PAGE_LOCALE === 'fr' ? 'Format du fichier CSV :' : 'CSV file format:'}</strong><br>
            ${PAGE_LOCALE === 'fr' ? 'Le fichier doit contenir les colonnes suivantes (avec en-tête) :' : 'File must contain the following columns (with header):'}
            <ul class="mb-0 mt-2">
                <li><code>mac_address</code> - ${PAGE_LOCALE === 'fr' ? 'Adresse MAC (formats acceptés: 00-11-22-33-44-55 ou 00:11:22:33:44:55)' : 'MAC Address (accepted formats: 00-11-22-33-44-55 or 00:11:22:33:44:55)'}</li>
                <li><code>serial_number</code> - ${PAGE_LOCALE === 'fr' ? 'Numéro de série (requis)' : 'Serial Number (required)'}</li>
                <li><code>notes</code> - ${PAGE_LOCALE === 'fr' ? 'Notes (optionnel)' : 'Notes (optional)'}</li>
            </ul>
        </div>
        
        <div class="alert alert-secondary">
            <strong>${PAGE_LOCALE === 'fr' ? 'Exemple :' : 'Example:'}</strong><br>
            <code>mac_address,serial_number,notes</code><br>
            <code>00-11-22-33-44-55,SN123456,Device 1</code><br>
            <code>00-11-22-33-44-66,SN123457,Device 2</code><br>
            <small class="text-muted mt-2 d-block">${PAGE_LOCALE === 'fr' ? 'Note: Les adresses MAC sont automatiquement normalisées (MAJUSCULES, délimiteur -)' : 'Note: MAC addresses are automatically normalized (UPPERCASE, - delimiter)'}</small>
        </div>
        
        <form id="csv-upload-form">
            <div class="form-group">
                <label>${PAGE_LOCALE === 'fr' ? 'Sélectionner le fichier CSV' : 'Select CSV File'} *</label>
                <input type="file" id="csv-file" class="form-control-file" accept=".csv" required>
                <small class="form-text text-muted">${PAGE_LOCALE === 'fr' ? 'Taille maximale: 5MB' : 'Max size: 5MB'}</small>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="skip-duplicates" checked>
                    <label class="custom-control-label" for="skip-duplicates">
                        ${PAGE_LOCALE === 'fr' ? 'Ignorer les doublons (MAC/Série existants)' : 'Skip duplicates (existing MAC/Serial)'}
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
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">${PAGE_LOCALE === 'fr' ? 'Annuler' : 'Cancel'}</button>
                <button type="button" class="btn btn-primary" onclick="uploadCsvFile(${productId})" id="upload-csv-btn">
                    <i data-feather="upload"></i> ${PAGE_LOCALE === 'fr' ? 'Télécharger et Importer' : 'Upload & Import'}
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
        toastr.error(PAGE_LOCALE === 'fr' ? 'Veuillez sélectionner un fichier CSV' : 'Please select a CSV file');
        return;
    }
    
    const file = fileInput.files[0];
    
    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Le fichier est trop volumineux (max 5MB)' : 'File is too large (max 5MB)');
        return;
    }
    
    // Check file type
    if (!file.name.endsWith('.csv')) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Veuillez sélectionner un fichier CSV valide' : 'Please select a valid CSV file');
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
        statusText.textContent = PAGE_LOCALE === 'fr' ? 'Téléchargement...' : 'Uploading...';
        
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/items/import-csv`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        progressBar.style.width = '50%';
        statusText.textContent = PAGE_LOCALE === 'fr' ? 'Traitement...' : 'Processing...';
        
        if (response.status === 401) {
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        const data = await response.json();
        progressBar.style.width = '100%';
        
        if (response.ok) {
            const successMsg = PAGE_LOCALE === 'fr' 
                ? `Importation réussie! ${data.imported} appareil(s) ajouté(s)${data.skipped > 0 ? `, ${data.skipped} ignoré(s)` : ''}${data.errors > 0 ? `, ${data.errors} erreur(s)` : ''}`
                : `Import successful! ${data.imported} device(s) added${data.skipped > 0 ? `, ${data.skipped} skipped` : ''}${data.errors > 0 ? `, ${data.errors} error(s)` : ''}`;
            
            loadSummary();
            loadInventory();
            
            // Show detailed results modal
            showCsvImportResults(productId, data);
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de l\'importation' : 'Failed to import'));
            }
            uploadBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error uploading CSV:', error);
        toastr.error((PAGE_LOCALE === 'fr' ? 'Échec du téléchargement: ' : 'Failed to upload: ') + error.message);
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
                <h5 class="mb-2">${statusIcon} ${PAGE_LOCALE === 'fr' ? 'Résultats de l\'importation' : 'Import Results'}</h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-success">${data.imported}</div>
                            <div class="stat-label">${PAGE_LOCALE === 'fr' ? 'Importés' : 'Imported'}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-warning">${data.skipped}</div>
                            <div class="stat-label">${PAGE_LOCALE === 'fr' ? 'Doublons (Ignorés)' : 'Duplicates (Skipped)'}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="result-stat">
                            <div class="stat-number text-danger">${data.errors}</div>
                            <div class="stat-label">${PAGE_LOCALE === 'fr' ? 'Erreurs' : 'Errors'}</div>
                        </div>
                    </div>
                </div>
            </div>
    `;
    
    if (hasErrors) {
        resultsHtml += `
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <strong>${PAGE_LOCALE === 'fr' ? 'Détails des Erreurs' : 'Error Details'}</strong>
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
                    ${PAGE_LOCALE === 'fr' ? 'Retour à la Liste' : 'Back to List'}
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    ${PAGE_LOCALE === 'fr' ? 'Fermer' : 'Close'}
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
    
    toastr.success(PAGE_LOCALE === 'fr' ? 'Modèle CSV téléchargé' : 'CSV template downloaded');
}
