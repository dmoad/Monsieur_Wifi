// Admin inventory management
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
        toastr.error('You do not have permission to access this page.');
        window.location.href = '/en/dashboard';
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
        document.getElementById('total-value').textContent = `$${parseFloat(summary.total_inventory_value).toFixed(2)}`;
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
        container.innerHTML = '<div class="card"><div class="card-body text-center">No products found</div></div>';
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
                        <button class="btn btn-sm btn-info" onclick="viewDevices(${product.id}, '${product.name}')" title="View Devices">
                            <i data-feather="list"></i> Devices
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="showUpdateModal(${product.id}, '${product.name}', ${inventory.quantity}, ${inventory.low_stock_threshold})">
                            <i data-feather="edit"></i> Update
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
        return '<span class="badge badge-danger">Out of Stock</span>';
    } else if (inventory.quantity <= inventory.low_stock_threshold) {
        return '<span class="badge badge-warning">Low Stock</span>';
    } else {
        return '<span class="badge badge-success">In Stock</span>';
    }
}

function showUpdateModal(productId, productName, currentStock, currentThreshold) {
    document.getElementById('modal-content').innerHTML = `
        <form id="update-inventory-form">
            <input type="hidden" id="product-id" value="${productId}">
            
            <div class="mb-3">
                <strong>Product:</strong> ${productName}
            </div>
            
            <div class="form-group">
                <label>Update Type:</label>
                <select id="update-type" class="form-control" onchange="toggleUpdateFields()">
                    <option value="set">Set Quantity</option>
                    <option value="adjust">Adjust Quantity</option>
                </select>
            </div>
            
            <div id="set-quantity-field" class="form-group">
                <label>New Quantity in Stock:</label>
                <input type="number" id="new-quantity" class="form-control" value="${currentStock}" min="0">
                <small class="text-muted">Current: ${currentStock}</small>
            </div>
            
            <div id="adjust-quantity-field" class="form-group" style="display: none;">
                <label>Adjust By:</label>
                <input type="number" id="adjustment" class="form-control" value="0">
                <small class="text-muted">Use positive numbers to add stock, negative to remove. Current: ${currentStock}</small>
            </div>
            
            <div class="form-group">
                <label>Low Stock Threshold:</label>
                <input type="number" id="threshold" class="form-control" value="${currentThreshold}" min="0">
            </div>
            
            <div class="form-group">
                <label>Note (Optional):</label>
                <textarea id="note" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveInventory()">Save</button>
            </div>
        </form>
    `;
    
    $('#inventory-modal').modal('show');
}

function toggleUpdateFields() {
    const updateType = document.getElementById('update-type').value;
    const setField = document.getElementById('set-quantity-field');
    const adjustField = document.getElementById('adjust-quantity-field');
    
    if (updateType === 'set') {
        setField.style.display = 'block';
        adjustField.style.display = 'none';
    } else {
        setField.style.display = 'none';
        adjustField.style.display = 'block';
    }
}

async function saveInventory() {
    const token = UserManager.getToken();
    const productId = document.getElementById('product-id').value;
    const updateType = document.getElementById('update-type').value;
    const threshold = document.getElementById('threshold').value;
    const note = document.getElementById('note').value;
    
    try {
        // Update threshold first
        await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/threshold`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ threshold: parseInt(threshold) })
        });
        
        // Update or adjust quantity
        let response;
        if (updateType === 'set') {
            const newQuantity = document.getElementById('new-quantity').value;
            response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/quantity`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    quantity: parseInt(newQuantity),
                    note: note
                })
            });
        } else {
            const adjustment = document.getElementById('adjustment').value;
            response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${productId}/adjust`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    adjustment: parseInt(adjustment),
                    note: note
                })
            });
        }
        
        if (response.status === 401) {
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.ok) {
            toastr.success('Inventory updated successfully');
            $('#inventory-modal').modal('hide');
            loadSummary();
            loadInventory();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || 'Failed to update inventory');
        }
    } catch (error) {
        console.error('Error saving inventory:', error);
        toastr.error('Failed to save inventory: ' + error.message);
    }
}

// View individual devices for a product
async function viewDevices(productId, productName) {
    const token = UserManager.getToken();
    
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
        const items = data.items || [];
        
        displayDevicesModal(productId, productName, items);
    } catch (error) {
        console.error('Error loading devices:', error);
        toastr.error('Failed to load devices: ' + error.message);
    }
}

function displayDevicesModal(productId, productName, items) {
    const itemsHtml = items.length > 0 ? items.map(item => {
        const statusBadge = getDeviceStatusBadge(item.status);
        return `
            <tr>
                <td>${item.mac_address}</td>
                <td>${item.serial_number}</td>
                <td>${statusBadge}</td>
                <td>
                    <small class="text-muted">${item.notes || '-'}</small>
                </td>
                <td>
                    ${item.status !== 'sold' ? `
                        <button class="btn btn-xs btn-primary" onclick="editDevice(${productId}, ${item.id}, '${item.mac_address}', '${item.serial_number}', '${item.status}', '${item.notes || ''}')">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" onclick="deleteDevice(${productId}, ${item.id})">
                            <i data-feather="trash-2"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }).join('') : '<tr><td colspan="5" class="text-center">No devices found</td></tr>';
    
    document.getElementById('modal-content').innerHTML = `
        <h5>${productName}</h5>
        
        <div class="mb-3">
            <button class="btn btn-success btn-sm" onclick="showAddDeviceForm(${productId})">
                <i data-feather="plus"></i> Add Device
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>MAC Address</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="devices-table-body">
                    ${itemsHtml}
                </tbody>
            </table>
        </div>
        
        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    `;
    
    $('#inventory-modal').modal('show');
    if (typeof feather !== 'undefined') feather.replace();
}

function getDeviceStatusBadge(status) {
    const badges = {
        'available': '<span class="badge badge-success">Available</span>',
        'reserved': '<span class="badge badge-warning">Reserved</span>',
        'sold': '<span class="badge badge-secondary">Sold</span>',
        'defective': '<span class="badge badge-danger">Defective</span>'
    };
    return badges[status] || status;
}

function showAddDeviceForm(productId) {
    document.getElementById('modal-content').innerHTML = `
        <h5>Add New Device</h5>
        <form id="add-device-form">
            <div class="form-group">
                <label>MAC Address *</label>
                <input type="text" id="mac-address" class="form-control" required placeholder="00:11:22:33:44:55">
            </div>
            
            <div class="form-group">
                <label>Serial Number *</label>
                <input type="text" id="serial-number" class="form-control" required placeholder="SN123456789">
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea id="device-notes" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label>Received Date</label>
                <input type="date" id="received-at" class="form-control" value="${new Date().toISOString().split('T')[0]}">
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">Cancel</button>
                <button type="button" class="btn btn-success" onclick="addDevice(${productId})">Add Device</button>
            </div>
        </form>
    `;
}

async function addDevice(productId) {
    const token = UserManager.getToken();
    const macAddress = document.getElementById('mac-address').value;
    const serialNumber = document.getElementById('serial-number').value;
    const notes = document.getElementById('device-notes').value;
    const receivedAt = document.getElementById('received-at').value;
    
    if (!macAddress || !serialNumber) {
        toastr.error('MAC Address and Serial Number are required');
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
            toastr.success('Device added successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, '');
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || 'Failed to add device');
            }
        }
    } catch (error) {
        console.error('Error adding device:', error);
        toastr.error('Failed to add device: ' + error.message);
    }
}

function editDevice(productId, itemId, macAddress, serialNumber, status, notes) {
    document.getElementById('modal-content').innerHTML = `
        <h5>Edit Device</h5>
        <form id="edit-device-form">
            <input type="hidden" id="item-id" value="${itemId}">
            
            <div class="form-group">
                <label>MAC Address *</label>
                <input type="text" id="mac-address" class="form-control" required value="${macAddress}">
            </div>
            
            <div class="form-group">
                <label>Serial Number *</label>
                <input type="text" id="serial-number" class="form-control" required value="${serialNumber}">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select id="device-status" class="form-control">
                    <option value="available" ${status === 'available' ? 'selected' : ''}>Available</option>
                    <option value="reserved" ${status === 'reserved' ? 'selected' : ''}>Reserved</option>
                    <option value="sold" ${status === 'sold' ? 'selected' : ''}>Sold</option>
                    <option value="defective" ${status === 'defective' ? 'selected' : ''}>Defective</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea id="device-notes" class="form-control" rows="2">${notes}</textarea>
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="viewDevices(${productId}, '')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateDevice(${productId}, ${itemId})">Update Device</button>
            </div>
        </form>
    `;
}

async function updateDevice(productId, itemId) {
    const token = UserManager.getToken();
    const macAddress = document.getElementById('mac-address').value;
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
            toastr.success('Device updated successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, '');
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(err => toastr.error(err[0]));
            } else {
                toastr.error(data.message || 'Failed to update device');
            }
        }
    } catch (error) {
        console.error('Error updating device:', error);
        toastr.error('Failed to update device: ' + error.message);
    }
}

async function deleteDevice(productId, itemId) {
    if (!confirm('Are you sure you want to delete this device?')) {
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
            toastr.success('Device deleted successfully');
            loadSummary();
            loadInventory();
            viewDevices(productId, '');
        } else {
            const data = await response.json();
            toastr.error(data.message || 'Failed to delete device');
        }
    } catch (error) {
        console.error('Error deleting device:', error);
        toastr.error('Failed to delete device: ' + error.message);
    }
}
