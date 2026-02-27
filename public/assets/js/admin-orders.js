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
    
    // Check if user is admin or superadmin
    if (!UserManager.isAdminOrAbove()) {
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
                        ${getStatusBadge(order)}
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
        
        console.log('Order data:', order);
        console.log('Payment status:', order.payment_status);
        console.log('Order status:', order.status);
        
        document.getElementById('modal-content').innerHTML = `
            <h6>Order Information</h6>
            <table class="table">
                <tr><td>Order Number:</td><td>${order.order_number}</td></tr>
                <tr><td>Status:</td><td>${getStatusBadge(order.status)}</td></tr>
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Méthode de paiement' : 'Payment Method'}:</td><td><span class="badge badge-${order.payment_method === 'stripe' ? 'primary' : 'secondary'}">${order.payment_method === 'stripe' ? 'Stripe' : (order.payment_method || 'N/A')}</span></td></tr>
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Statut du paiement' : 'Payment Status'}:</td><td><span class="badge badge-${order.payment_status === 'succeeded' ? 'success' : 'warning'}">${order.payment_status || 'pending'}</span></td></tr>
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Date de commande' : 'Order Date'}:</td><td>${new Date(order.created_at).toLocaleString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { dateStyle: 'medium', timeStyle: 'short' })}</td></tr>
                ${order.delivered_at ? `<tr><td>${PAGE_LOCALE === 'fr' ? 'Date de livraison' : 'Delivered Date'}:</td><td><strong class="text-success">${new Date(order.delivered_at).toLocaleString(PAGE_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { dateStyle: 'medium', timeStyle: 'short' })}</strong></td></tr>` : ''}
                <tr><td>Customer:</td><td>${order.user.name} (${order.user.email})</td></tr>
            </table>
            
            <h6>${PAGE_LOCALE === 'fr' ? 'Résumé de la commande' : 'Order Summary'}</h6>
            <table class="table">
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Sous-total' : 'Subtotal'}:</td><td>$${parseFloat(order.product_amount || 0).toFixed(2)}</td></tr>
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Frais de livraison' : 'Shipping Cost'}:</td><td>$${parseFloat(order.shipping_cost || 0).toFixed(2)}</td></tr>
                <tr><td>${PAGE_LOCALE === 'fr' ? 'Taxe' : 'Tax'}:</td><td>$${parseFloat(order.tax_amount || 0).toFixed(2)}</td></tr>
                <tr class="font-weight-bold"><td>${PAGE_LOCALE === 'fr' ? 'Total' : 'Total'}:</td><td>$${parseFloat(order.total).toFixed(2)}</td></tr>
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
            
            ${order.payment_status === 'succeeded' ? `
            <div class="mb-3">
                <button class="btn btn-outline-primary btn-sm" onclick="downloadInvoice('${order.order_number}')">
                    <i data-feather="download"></i> ${PAGE_LOCALE === 'fr' ? 'Télécharger la facture' : 'Download Invoice'}
                </button>
            </div>
            ` : ''}
            
            <div class="mt-3">
                ${getOrderActionButtons(order)}
            </div>
        `;
        
        $('#order-modal').modal('show');
        
        // Refresh feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    } catch (error) {
        console.error('Error loading order:', error);
        toastr.error('Failed to load order details');
    }
}

function showTrackingModalFromOrderView(orderNumber) {
    // Use Bootstrap modal event to wait for modal to fully hide before showing tracking form
    $('#order-modal').one('hidden.bs.modal', function() {
        showTrackingModal(orderNumber);
    });
    $('#order-modal').modal('hide');
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

function getStatusBadge(order) {
    const isPaid = order.payment_status === 'succeeded';
    const status = order.status;
    
    // Cancelled orders always show as cancelled
    if (status === 'cancelled') {
        return PAGE_LOCALE === 'fr' 
            ? '<span class="badge badge-danger">Annulée</span>'
            : '<span class="badge badge-danger">Cancelled</span>';
    }
    
    // If not paid, show awaiting payment
    if (!isPaid) {
        return PAGE_LOCALE === 'fr'
            ? '<span class="badge badge-warning">En attente de paiement</span>'
            : '<span class="badge badge-warning">Awaiting payment</span>';
    }
    
    // If shipped or delivered, show shipped
    if (status === 'shipped' || status === 'delivered') {
        return PAGE_LOCALE === 'fr'
            ? '<span class="badge badge-primary">Expédiée</span>'
            : '<span class="badge badge-primary">Shipped</span>';
    }
    
    // Otherwise, payment received (paid but not shipped)
    return PAGE_LOCALE === 'fr'
        ? '<span class="badge badge-success">Paiement reçu</span>'
        : '<span class="badge badge-success">Payment received</span>';
}

function getOrderActionButtons(order) {
    const isPaid = order.payment_status === 'succeeded';
    const isCancelled = order.status === 'cancelled';
    const isDelivered = order.status === 'delivered';
    const isShipped = order.status === 'shipped';
    const hasTracking = order.tracking_id && order.shipping_provider;
    
    console.log('getOrderActionButtons:', { isPaid, isCancelled, isDelivered, isShipped, hasTracking });
    console.log('Should show Confirm Payment?', !isPaid && !isCancelled);
    
    let buttons = [];
    
    // Confirm Payment button - only for unpaid, non-cancelled orders
    if (!isPaid && !isCancelled) {
        console.log('Adding Confirm Payment button');
        const isStripe = order.payment_method === 'stripe';
        buttons.push(`
            <button class="btn btn-warning btn-sm" onclick="if(confirm('${PAGE_LOCALE === 'fr' ? 'Confirmer que le paiement a été reçu?' : 'Confirm payment has been received?'}')) markAsPaid('${order.order_number}', ${isStripe})">
                <i data-feather="check-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Confirmer le paiement' : 'Confirm Payment'}${isStripe ? ' (Stripe)' : ''}
            </button>
        `);
    } else {
        console.log('Not adding Confirm Payment button - isPaid:', isPaid, 'isCancelled:', isCancelled);
    }
    
    // Assign/Update Inventory button - only for paid orders
    if (isPaid && !isCancelled) {
        const hasInventoryAssigned = order.items && order.items.some(item => 
            item.inventory_items && item.inventory_items.length > 0 && item.inventory_items.some(inv => inv.device_id)
        );
        
        buttons.push(`
            <button class="btn btn-secondary btn-sm" onclick="showAssignInventoryModalFromOrderView('${order.order_number}')">
                <i data-feather="package"></i> ${hasInventoryAssigned 
                    ? (PAGE_LOCALE === 'fr' ? 'Mettre à jour l\'inventaire' : 'Update Inventory')
                    : (PAGE_LOCALE === 'fr' ? 'Assigner l\'inventaire' : 'Assign Inventory')
                }
            </button>
        `);
    }
    
    // Add/Update Tracking button - only for paid, non-cancelled, non-delivered orders
    if (isPaid && !isCancelled && !isDelivered) {
        buttons.push(`
            <button class="btn btn-info btn-sm" onclick="showTrackingModalFromOrderView('${order.order_number}')">
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
    console.log('Total buttons:', buttons.length);
    
    if (buttons.length === 0) {
        if (isCancelled) {
            return `<p class="text-muted"><i data-feather="x-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Commande annulée - aucune action disponible' : 'Order cancelled - no actions available'}</p>`;
        }
        if (isDelivered) {
            return `<p class="text-muted"><i data-feather="check-circle"></i> ${PAGE_LOCALE === 'fr' ? 'Commande livrée - terminée' : 'Order delivered - completed'}</p>`;
        }
    }
    
    const buttonsHtml = buttons.join(' ');
    console.log('Buttons HTML:', buttonsHtml);
    return buttonsHtml;
}

async function markAsPaid(orderNumber, isStripe = false) {
    const token = UserManager.getToken();
    
    // Use the appropriate endpoint based on payment method
    const endpoint = isStripe 
        ? `${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}/confirm-stripe-payment`
        : `${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}/confirm-payment`;
    
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
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
            const message = isStripe
                ? (PAGE_LOCALE === 'fr' ? 'Paiement Stripe confirmé avec succès' : 'Stripe payment confirmed successfully')
                : (PAGE_LOCALE === 'fr' ? 'Paiement confirmé avec succès' : 'Payment confirmed successfully');
            toastr.success(message);
            
            if (isStripe && data.payment_intent_id) {
                console.log('Stripe payment intent ID:', data.payment_intent_id);
            }
            
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Échec de la confirmation du paiement' : 'Failed to confirm payment'));
        }
    } catch (error) {
        console.error('Error confirming payment:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Échec de la confirmation du paiement' : 'Failed to confirm payment');
    }
}

// Assign Inventory Functions
let currentOrderForInventory = null;

function showAssignInventoryModalFromOrderView(orderNumber) {
    // Use Bootstrap modal event to wait for order modal to fully hide before showing inventory modal
    $('#order-modal').one('hidden.bs.modal', function() {
        showAssignInventoryModal(orderNumber);
    });
    $('#order-modal').modal('hide');
}

async function showAssignInventoryModal(orderNumber) {
    const token = UserManager.getToken();
    currentOrderForInventory = orderNumber;
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load order');
        
        const data = await response.json();
        const order = data.order;
        
        // Load available inventory for each product
        const inventoryPromises = order.items.map(async item => {
            const invResponse = await fetch(
                `${APP_CONFIG.API.BASE_URL}/v1/admin/inventory/${item.product_model_id}/items`,
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            );
            const invData = await invResponse.json();
            // Filter for items with 'available' status (not yet assigned), or already assigned to this order item
            return {
                orderItem: item,
                availableInventory: (invData.items || []).filter(inv => 
                    inv.status === 'available' || inv.order_item_id == item.id
                )
            };
        });
        
        const inventoryData = await Promise.all(inventoryPromises);
        
        // Build modal content
        let modalContent = `
            <div class="alert alert-info">
                ${PAGE_LOCALE === 'fr' 
                    ? 'Sélectionnez les articles d\'inventaire à assigner à chaque article de commande. Le nombre d\'articles doit correspondre à la quantité commandée.' 
                    : 'Select inventory items to assign to each order item. The number of items must match the ordered quantity.'}
            </div>
        `;
        
        inventoryData.forEach(({ orderItem, availableInventory }) => {
            modalContent += `
                <div class="mb-4 p-3 border rounded">
                    <h5>${orderItem.product_model.name}</h5>
                    <p class="text-muted">
                        ${PAGE_LOCALE === 'fr' ? 'Quantité' : 'Quantity'}: ${orderItem.quantity} | 
                        ${PAGE_LOCALE === 'fr' ? 'Articles disponibles' : 'Available items'}: ${availableInventory.length}
                    </p>
                    <div class="form-group">
                        <label>${PAGE_LOCALE === 'fr' ? 'Sélectionner les articles (exactement ' + orderItem.quantity + ')' : 'Select items (exactly ' + orderItem.quantity + ')'}</label>
                        <div class="inventory-checkbox-list border rounded p-2" style="max-height: 200px; overflow-y: auto;" data-order-item-id="${orderItem.id}" data-required="${orderItem.quantity}">
                            ${availableInventory.map(inv => `
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input inventory-checkbox" id="inv-${inv.id}" value="${inv.id}" ${inv.order_item_id == orderItem.id ? 'checked' : ''}>
                                    <label class="custom-control-label" for="inv-${inv.id}">
                                        <code>${inv.serial_number}</code> - <small>${inv.mac_address}</small>
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                        <small class="form-text text-muted">
                            ${PAGE_LOCALE === 'fr' ? 'Cliquez pour sélectionner/désélectionner les articles' : 'Click to select/deselect items'}
                        </small>
                    </div>
                </div>
            `;
        });
        
        document.getElementById('assign-inventory-content').innerHTML = modalContent;
        $('#assign-inventory-modal').modal('show');
    } catch (error) {
        console.error('Error loading order for inventory assignment:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur lors du chargement de la commande' : 'Error loading order');
    }
}

async function assignInventoryToOrder() {
    const token = UserManager.getToken();
    const checkboxLists = document.querySelectorAll('.inventory-checkbox-list');
    const assignments = [];
    let hasError = false;
    
    // Validate and collect assignments
    checkboxLists.forEach(list => {
        const orderItemId = list.dataset.orderItemId;
        const required = parseInt(list.dataset.required);
        const checkboxes = list.querySelectorAll('.inventory-checkbox:checked');
        const selected = Array.from(checkboxes).map(cb => parseInt(cb.value));
        
        if (selected.length !== required) {
            toastr.error(
                PAGE_LOCALE === 'fr' 
                    ? `Vous devez sélectionner exactement ${required} article(s)`
                    : `You must select exactly ${required} item(s)`
            );
            hasError = true;
            return;
        }
        
        assignments.push({
            order_item_id: parseInt(orderItemId),
            inventory_item_ids: selected
        });
    });
    
    if (hasError || assignments.length === 0) return;
    
    try {
        const response = await fetch(
            `${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${currentOrderForInventory}/assign-inventory`,
            {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ assignments })
            }
        );
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Failed to assign inventory');
        }
        
        toastr.success(
            PAGE_LOCALE === 'fr' 
                ? 'Inventaire assigné et appareils créés avec succès'
                : 'Inventory assigned and devices created successfully'
        );
        $('#assign-inventory-modal').modal('hide');
        loadOrders();
    } catch (error) {
        console.error('Error assigning inventory:', error);
        toastr.error(error.message || (PAGE_LOCALE === 'fr' ? 'Erreur lors de l\'assignation de l\'inventaire' : 'Error assigning inventory'));
    }
}

async function downloadInvoice(orderNumber) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/orders/${orderNumber}/invoice`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/pdf'
            }
        });
        
        if (response.status === 401) {
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (!response.ok) {
            const error = await response.json();
            toastr.error(error.message || (PAGE_LOCALE === 'fr' ? 'Échec du téléchargement de la facture' : 'Failed to download invoice'));
            return;
        }
        
        // Get the blob from response
        const blob = await response.blob();
        
        // Create a download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `invoice-${orderNumber}.pdf`;
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        toastr.success(PAGE_LOCALE === 'fr' ? 'Facture téléchargée avec succès' : 'Invoice downloaded successfully');
    } catch (error) {
        console.error('Error downloading invoice:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Échec du téléchargement de la facture' : 'Failed to download invoice');
    }
}
