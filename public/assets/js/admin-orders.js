// Admin orders management
const PAGE_LOCALE = document.documentElement.lang || 'en';

const TRANSLATIONS = {
    en: {
        addShipping: 'Add Shipping Information',
        updateShipping: 'Update Shipping Information',
        shippingProvider: 'Shipping Provider',
        selectProvider: 'Select a provider...',
        majorCarriers: 'Major European Carriers',
        otherProviders: 'Other',
        otherProviderName: 'Other Provider Name',
        enterProvider: 'Enter provider name',
        trackingId: 'Tracking ID',
        enterTracking: 'Enter tracking number',
        cancel: 'Cancel',
        saveTracking: 'Save Tracking',
        selectProviderError: 'Please select a shipping provider',
        enterProviderError: 'Please enter provider name',
        enterTrackingError: 'Please enter tracking ID',
        trackingUpdated: 'Tracking information updated successfully'
    },
    fr: {
        addShipping: 'Ajouter les informations d\'expédition',
        updateShipping: 'Mettre à jour les informations d\'expédition',
        shippingProvider: 'Transporteur',
        selectProvider: 'Sélectionnez un transporteur...',
        majorCarriers: 'Principaux transporteurs européens',
        otherProviders: 'Autre',
        otherProviderName: 'Nom du transporteur',
        enterProvider: 'Entrez le nom du transporteur',
        trackingId: 'Numéro de suivi',
        enterTracking: 'Entrez le numéro de suivi',
        cancel: 'Annuler',
        saveTracking: 'Enregistrer le suivi',
        selectProviderError: 'Veuillez sélectionner un transporteur',
        enterProviderError: 'Veuillez entrer le nom du transporteur',
        enterTrackingError: 'Veuillez entrer le numéro de suivi',
        trackingUpdated: 'Informations de suivi mises à jour avec succès'
    }
};

const t = TRANSLATIONS[PAGE_LOCALE] || TRANSLATIONS.en;

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
    
    // Optional: Check if user is admin
    if (!UserManager.hasRole('admin')) {
        toastr.error('You do not have permission to access this page.');
        window.location.href = '/en/dashboard';
        return;
    }
    
    loadOrders();
});

async function loadOrders() {
    const token = UserManager.getToken();
    const status = document.getElementById('status-filter').value;
    const search = document.getElementById('search').value;
    
    let url = `${APP_CONFIG.API.BASE_URL}/v1/admin/orders?`;
    if (status) url += `status=${status}&`;
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
            throw new Error('Failed to load orders');
        }
        
        const data = await response.json();
        const orders = data.orders?.data || data.orders || [];
        displayOrders(orders);
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-loading').style.display = 'none';
        toastr.error('Failed to load orders: ' + error.message);
    }
}

function displayOrders(orders) {
    document.getElementById('orders-loading').style.display = 'none';
    const container = document.getElementById('orders-list');
    
    if (orders.length === 0) {
        container.innerHTML = '<div class="card"><div class="card-body text-center">No orders found</div></div>';
        return;
    }
    
    container.innerHTML = orders.map(order => `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <strong>#${order.order_number}</strong><br>
                        <small class="text-muted">${new Date(order.created_at).toLocaleDateString()}</small>
                    </div>
                    <div class="col-md-2">
                        ${order.user.name}<br>
                        <small class="text-muted">${order.user.email}</small>
                    </div>
                    <div class="col-md-2">
                        ${getStatusBadge(order.status)}
                    </div>
                    <div class="col-md-2">
                        <strong>$${parseFloat(order.total).toFixed(2)}</strong>
                    </div>
                    <div class="col-md-2">
                        ${order.tracking_id ? `<small>${order.tracking_id}</small>` : '<small class="text-muted">No tracking</small>'}
                    </div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('${order.order_number}')">
                            ${PAGE_LOCALE === 'fr' ? 'Voir' : 'View'}
                        </button>
                        ${order.payment_status === 'succeeded' && order.status !== 'cancelled' && order.status !== 'delivered' 
                            ? `<button class="btn btn-sm btn-info" onclick="showTrackingModal('${order.order_number}')">
                                ${PAGE_LOCALE === 'fr' ? 'Suivi' : 'Tracking'}
                            </button>` 
                            : ''}
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

async function viewOrder(orderNumber) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}`, {
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
        
        if (!response.ok) throw new Error('Failed to load order');
        
        const data = await response.json();
        const order = data.order || data;
        
        document.getElementById('modal-content').innerHTML = `
            <h6>Order Information</h6>
            <table class="table">
                <tr><td>Order Number:</td><td>${order.order_number}</td></tr>
                <tr><td>Status:</td><td>${getStatusBadge(order.status)}</td></tr>
                <tr><td>Total:</td><td>$${parseFloat(order.total).toFixed(2)}</td></tr>
                <tr><td>Customer:</td><td>${order.user.name} (${order.user.email})</td></tr>
            </table>
            
            <h6>Items</h6>
            <table class="table">
                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>${item.product_model.name}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                            <td>$${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            
            <h6>Shipping Address</h6>
            <p>
                ${order.shipping_address.first_name} ${order.shipping_address.last_name}<br>
                ${order.shipping_address.address_line1}<br>
                ${order.shipping_address.city}, ${order.shipping_address.province} ${order.shipping_address.postal_code}
            </p>
            
            <h6>${PAGE_LOCALE === 'fr' ? 'Informations de suivi' : 'Tracking Information'}</h6>
            <p>
                ${order.shipping_provider 
                    ? `<strong>${PAGE_LOCALE === 'fr' ? 'Transporteur' : 'Provider'}:</strong> ${order.shipping_provider}<br><strong>${PAGE_LOCALE === 'fr' ? 'Suivi' : 'Tracking'}:</strong> ${order.tracking_id || 'N/A'}` 
                    : `<span class="text-muted">${PAGE_LOCALE === 'fr' ? 'Pas encore ajouté' : 'Not yet added'}</span>`}
            </p>
            
            <div class="mt-3">
                ${getOrderActionButtons(order)}
            </div>
        `;
        
        $('#order-modal').modal('show');
    } catch (error) {
        console.error('Error loading order:', error);
        toastr.error('Failed to load order details');
    }
}

function showTrackingModal(orderNumber) {
    document.getElementById('modal-content').innerHTML = `
        <h5>${t.addShipping}</h5>
        <form id="tracking-form">
            <div class="form-group">
                <label>${t.shippingProvider} *</label>
                <select id="shipping-provider" class="form-control" required>
                    <option value="">${t.selectProvider}</option>
                    <optgroup label="${t.majorCarriers}">
                        <option value="DHL Express">DHL Express</option>
                        <option value="DPD">DPD (Dynamic Parcel Distribution)</option>
                        <option value="UPS">UPS (United Parcel Service)</option>
                        <option value="FedEx">FedEx</option>
                        <option value="TNT">TNT Express</option>
                        <option value="GLS">GLS (General Logistics Systems)</option>
                        <option value="Hermes">Hermes</option>
                        <option value="DHL Parcel">DHL Parcel</option>
                        <option value="PostNL">PostNL</option>
                        <option value="Colissimo">Colissimo (La Poste)</option>
                        <option value="Chronopost">Chronopost</option>
                        <option value="Royal Mail">Royal Mail</option>
                        <option value="Parcelforce">Parcelforce</option>
                        <option value="Deutsche Post">Deutsche Post</option>
                        <option value="DB Schenker">DB Schenker</option>
                    </optgroup>
                    <optgroup label="${t.otherProviders}">
                        <option value="Other">${t.otherProviders}</option>
                    </optgroup>
                </select>
            </div>
            
            <div class="form-group" id="other-provider-group" style="display: none;">
                <label>${t.otherProviderName} *</label>
                <input type="text" id="other-provider" class="form-control" placeholder="${t.enterProvider}">
            </div>
            
            <div class="form-group">
                <label>${t.trackingId} *</label>
                <input type="text" id="tracking-id" class="form-control" required placeholder="${t.enterTracking}">
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">${t.cancel}</button>
                <button type="button" class="btn btn-primary" onclick="submitTracking('${orderNumber}')">${t.saveTracking}</button>
            </div>
        </form>
    `;
    
    // Show/hide other provider input
    document.getElementById('shipping-provider').addEventListener('change', function() {
        const otherGroup = document.getElementById('other-provider-group');
        otherGroup.style.display = this.value === 'Other' ? 'block' : 'none';
    });
    
    $('#order-modal').modal('show');
    if (typeof feather !== 'undefined') feather.replace();
}

function submitTracking(orderNumber) {
    let provider = document.getElementById('shipping-provider').value;
    const trackingId = document.getElementById('tracking-id').value;
    
    if (!provider) {
        toastr.error(t.selectProviderError);
        return;
    }
    
    if (provider === 'Other') {
        provider = document.getElementById('other-provider').value;
        if (!provider) {
            toastr.error(t.enterProviderError);
            return;
        }
    }
    
    if (!trackingId) {
        toastr.error(t.enterTrackingError);
        return;
    }
    
    updateTracking(orderNumber, provider, trackingId);
}

async function updateTracking(orderNumber, provider, trackingId) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}/tracking`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                shipping_provider: provider,
                tracking_id: trackingId
            })
        });
        
        if (response.status === 401) {
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.ok) {
            toastr.success(t.trackingUpdated);
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || 'Failed to update tracking');
        }
    } catch (error) {
        console.error('Error updating tracking:', error);
        toastr.error('Failed to update tracking');
    }
}

async function updateStatus(orderNumber, status) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        
        if (response.status === 401) {
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Statut de la commande mis à jour' : 'Order status updated');
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || (PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour du statut' : 'Failed to update status'));
        }
    } catch (error) {
        console.error('Error updating status:', error);
        toastr.error('Failed to update status');
    }
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge badge-warning">Pending</span>',
        'processing': '<span class="badge badge-info">Processing</span>',
        'completed': '<span class="badge badge-success">Completed</span>',
        'shipped': '<span class="badge badge-primary">Shipped</span>',
        'delivered': '<span class="badge badge-success">Delivered</span>',
        'cancelled': '<span class="badge badge-danger">Cancelled</span>',
        'payment_failed': '<span class="badge badge-danger">Payment Failed</span>'
    };
    return badges[status] || `<span class="badge badge-secondary">${status}</span>`;
}

function getOrderActionButtons(order) {
    const isPaid = order.payment_status === 'succeeded';
    const isCancelled = order.status === 'cancelled';
    const isDelivered = order.status === 'delivered';
    const isShipped = order.status === 'shipped';
    const hasTracking = order.tracking_id && order.shipping_provider;
    
    let buttons = [];
    
    // Mark as Paid button - only for unpaid, non-cancelled orders
    if (!isPaid && !isCancelled) {
        buttons.push(`
            <button class="btn btn-warning btn-sm" onclick="if(confirm('${PAGE_LOCALE === 'fr' ? 'Confirmer que le paiement a été reçu?' : 'Confirm payment has been received?'}')) markAsPaid('${order.order_number}')">
                <i data-feather="dollar-sign"></i> ${PAGE_LOCALE === 'fr' ? 'Marquer comme payé' : 'Mark as Paid'}
            </button>
        `);
    }
    
    // Add/Update Tracking button - only for paid, non-cancelled, non-delivered orders
    if (isPaid && !isCancelled && !isDelivered) {
        buttons.push(`
            <button class="btn btn-info btn-sm" onclick="$('#order-modal').modal('hide'); setTimeout(() => showTrackingModal('${order.order_number}'), 300);">
                <i data-feather="truck"></i> ${hasTracking ? (PAGE_LOCALE === 'fr' ? 'Modifier le suivi' : 'Update Tracking') : (PAGE_LOCALE === 'fr' ? 'Ajouter le suivi' : 'Add Tracking')}
            </button>
        `);
    }
    
    // Mark as Shipped button - only for paid orders with tracking that aren't shipped/delivered/cancelled
    if (isPaid && hasTracking && !isShipped && !isDelivered && !isCancelled) {
        buttons.push(`
            <button class="btn btn-success btn-sm" onclick="updateStatus('${order.order_number}', 'shipped')">
                ${PAGE_LOCALE === 'fr' ? 'Marquer comme expédié' : 'Mark as Shipped'}
            </button>
        `);
    }
    
    // Mark as Delivered button - only for shipped orders
    if (isShipped && !isDelivered) {
        buttons.push(`
            <button class="btn btn-primary btn-sm" onclick="updateStatus('${order.order_number}', 'delivered')">
                ${PAGE_LOCALE === 'fr' ? 'Marquer comme livré' : 'Mark as Delivered'}
            </button>
        `);
    }
    
    // Cancel button - only for orders that aren't already cancelled or delivered
    if (!isCancelled && !isDelivered) {
        buttons.push(`
            <button class="btn btn-danger btn-sm" onclick="if(confirm('${PAGE_LOCALE === 'fr' ? 'Êtes-vous sûr de vouloir annuler cette commande?' : 'Are you sure you want to cancel this order?'}')) updateStatus('${order.order_number}', 'cancelled')">
                ${PAGE_LOCALE === 'fr' ? 'Annuler la commande' : 'Cancel Order'}
            </button>
        `);
    }
    
    // Show status info if no actions available
    if (buttons.length === 0) {
        if (isCancelled) {
            return `<p class="text-muted"><i data-feather="x-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Commande annulée - aucune action disponible' : 'Order cancelled - no actions available'}</p>`;
        }
        if (isDelivered) {
            return `<p class="text-muted"><i data-feather="check-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Commande livrée - terminée' : 'Order delivered - completed'}</p>`;
        }
    }
    
    return buttons.join(' ');
}

async function markAsPaid(orderNumber) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders/${orderNumber}/success`, {
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
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Commande marquée comme payée' : 'Order marked as paid');
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour du paiement' : 'Failed to update payment'));
        }
    } catch (error) {
        console.error('Error marking as paid:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Échec de la mise à jour du paiement' : 'Failed to update payment');
    }
}
