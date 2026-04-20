// Admin orders management — translations injected by blade (lang/{en,fr}/admin_orders.php)
const t = window.APP_I18N.admin_orders;

document.addEventListener('DOMContentLoaded', function() {
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

    if (!UserManager.isAdminOrAbove()) {
        toastr.error(t.no_permission);
        window.location.href = t.dashboard_url;
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
            throw new Error('Failed to load orders');
        }

        const data = await response.json();
        const orders = data.orders?.data || data.orders || [];
        displayOrders(orders);
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-loading').style.display = 'none';
        toastr.error(t.load_orders_failed.replace('{message}', error.message));
    }
}

function displayOrders(orders) {
    document.getElementById('orders-loading').style.display = 'none';
    const container = document.getElementById('orders-list');

    if (orders.length === 0) {
        container.innerHTML = `<div class="card"><div class="card-body text-center">${t.no_orders}</div></div>`;
        return;
    }

    container.innerHTML = orders.map(order => `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <strong>#${order.order_number}</strong><br>
                        <small class="text-muted">${new Date(order.created_at).toLocaleDateString(t.date_locale)}</small>
                    </div>
                    <div class="col-md-2">
                        ${order.user.name}<br>
                        <small class="text-muted">${order.user.email}</small>
                    </div>
                    <div class="col-md-2">
                        ${getStatusBadge(order)}
                    </div>
                    <div class="col-md-2">
                        <strong>€${parseFloat(order.total).toFixed(2)}</strong>
                    </div>
                    <div class="col-md-2">
                        ${order.tracking_id ? `<small>${order.tracking_id}</small>` : `<small class="text-muted">${t.no_tracking}</small>`}
                    </div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('${order.order_number}')">
                            ${t.btn_view}
                        </button>
                        ${order.payment_status === 'succeeded' && order.status !== 'cancelled' && order.status !== 'delivered'
                            ? `<button class="btn btn-sm btn-info" onclick="showTrackingModal('${order.order_number}')">
                                ${t.btn_tracking}
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
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (!response.ok) throw new Error('Failed to load order');

        const data = await response.json();
        const order = data.order || data;

        const createdAt = new Date(order.created_at);
        const dateStr = createdAt.toLocaleDateString(t.date_locale, { month: 'short', day: 'numeric', year: 'numeric' });
        const timeStr = createdAt.toLocaleTimeString(t.date_locale, { hour: '2-digit', minute: '2-digit' });

        document.getElementById('modal-content').innerHTML = `
            <div class="order-modal-redesign">
                <!-- Hero Header Section -->
                <div class="order-hero-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="order-number-badge">${order.order_number}</div>
                            <div class="order-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                ${dateStr} ${timeStr}
                            </div>
                        </div>
                        <div class="order-status-large">
                            ${getStatusBadge(order)}
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Toolbar -->
                <div class="quick-actions-toolbar">
                    ${getOrderActionButtons(order)}
                </div>

                <!-- Main Content Grid -->
                <div class="order-content-grid">
                    <!-- Left Column -->
                    <div class="order-column-left">
                        <!-- Customer Info -->
                        <div class="info-card customer-card">
                            <div class="info-card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </div>
                            <div class="info-card-content">
                                <div class="info-label">${t.label_customer}</div>
                                <div class="info-value">${order.user.name}</div>
                                <div class="info-meta">${order.user.email}</div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="info-card payment-card">
                            <div class="info-card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            </div>
                            <div class="info-card-content">
                                <div class="info-label">${t.label_payment}</div>
                                <div class="payment-badges">
                                    <span class="mini-badge badge-${order.payment_method === 'stripe' ? 'blue' : 'gray'}">${order.payment_method === 'stripe' ? 'Stripe' : (order.payment_method || 'N/A')}</span>
                                    <span class="mini-badge badge-${order.payment_status === 'succeeded' ? 'green' : 'yellow'}">${order.payment_status || 'pending'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="info-card address-card">
                            <div class="info-card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="info-card-content">
                                <div class="info-label">${t.label_shipping_info}</div>
                                <div class="info-value-sm">${order.shipping_address.first_name} ${order.shipping_address.last_name}</div>
                                <div class="info-meta">
                                    ${order.shipping_address.address_line1}${order.shipping_address.address_line2 ? ', ' + order.shipping_address.address_line2 : ''}<br>
                                    ${order.shipping_address.city}, ${order.shipping_address.province} ${order.shipping_address.postal_code}
                                </div>
                            </div>
                        </div>

                        ${order.shipping_provider ? `
                        <!-- Tracking Info -->
                        <div class="info-card tracking-card">
                            <div class="info-card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            </div>
                            <div class="info-card-content">
                                <div class="info-label">${t.label_tracking_info}</div>
                                <div class="info-value-sm">${order.shipping_provider}</div>
                                <div class="tracking-number">${order.tracking_id}</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Right Column -->
                    <div class="order-column-right">
                        <!-- Order Summary Card -->
                        <div class="summary-card">
                            <div class="summary-header">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                <span>${t.summary_title}</span>
                            </div>

                            <div class="items-list">
                                ${order.items.map(item => `
                                    <div class="item-row">
                                        <div class="item-info">
                                            <div class="item-name">${item.product_model.name}</div>
                                            <div class="item-qty">×${item.quantity}</div>
                                        </div>
                                        <div class="item-price">€${parseFloat(item.subtotal).toFixed(2)}</div>
                                    </div>
                                `).join('')}
                            </div>

                            <div class="summary-breakdown">
                                <div class="summary-row">
                                    <span>${t.summary_subtotal}</span>
                                    <span>€${parseFloat(order.product_amount || 0).toFixed(2)}</span>
                                </div>
                                <div class="summary-row">
                                    <span>${t.summary_shipping}</span>
                                    <span>€${parseFloat(order.shipping_cost || 0).toFixed(2)}</span>
                                </div>
                                <div class="summary-row">
                                    <span>${t.summary_tax}</span>
                                    <span>€${parseFloat(order.tax_amount || 0).toFixed(2)}</span>
                                </div>
                                <div class="summary-row summary-total">
                                    <span>${t.summary_total}</span>
                                    <span>€${parseFloat(order.total).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#order-modal').modal('show');

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    } catch (error) {
        console.error('Error loading order:', error);
        toastr.error(t.load_details_failed);
    }
}

function showTrackingModalFromOrderView(orderNumber) {
    $('#order-modal').one('hidden.bs.modal', function() {
        showTrackingModal(orderNumber);
    });
    $('#order-modal').modal('hide');
}

function showTrackingModal(orderNumber) {
    document.getElementById('modal-content').innerHTML = `
        <div class="tracking-modal-redesign">
            <!-- Hero Header -->
            <div class="tracking-hero-header">
                <h5 class="mb-0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    ${t.tracking_add_title}
                </h5>
            </div>

            <div class="tracking-form-content">
                <form id="tracking-form">
                    <div class="form-group">
                        <label class="form-label-modern">${t.tracking_provider} <span class="text-danger">*</span></label>
                        <select id="shipping-provider" class="form-control form-control-modern" required>
                            <option value="">${t.tracking_select_provider}</option>
                            <optgroup label="${t.tracking_major_carriers}">
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
                            <optgroup label="${t.tracking_other_providers}">
                                <option value="Other">${t.tracking_other_providers}</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group" id="other-provider-group" style="display: none;">
                        <label class="form-label-modern">${t.tracking_other_provider_name} <span class="text-danger">*</span></label>
                        <input type="text" id="other-provider" class="form-control form-control-modern" placeholder="${t.tracking_enter_provider}">
                    </div>

                    <div class="form-group">
                        <label class="form-label-modern">${t.tracking_id} <span class="text-danger">*</span></label>
                        <input type="text" id="tracking-id" class="form-control form-control-modern" required placeholder="${t.tracking_enter_tracking}">
                    </div>

                    <div class="tracking-form-actions">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">${t.tracking_btn_cancel}</button>
                        <button type="button" class="btn btn-primary" onclick="submitTracking('${orderNumber}')">${t.tracking_btn_save}</button>
                    </div>
                </form>
            </div>
        </div>
    `;

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
        toastr.error(t.tracking_error_select_provider);
        return;
    }

    if (provider === 'Other') {
        provider = document.getElementById('other-provider').value;
        if (!provider) {
            toastr.error(t.tracking_error_enter_provider);
            return;
        }
    }

    if (!trackingId) {
        toastr.error(t.tracking_error_enter_tracking);
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
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (response.ok) {
            toastr.success(t.tracking_updated);
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || t.tracking_update_failed);
        }
    } catch (error) {
        console.error('Error updating tracking:', error);
        toastr.error(t.tracking_update_failed);
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
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (response.ok) {
            toastr.success(t.status_updated);
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            const errorData = await response.json();
            toastr.error(errorData.message || t.status_update_failed);
        }
    } catch (error) {
        console.error('Error updating status:', error);
        toastr.error(t.status_update_failed);
    }
}

function getStatusBadge(order) {
    const isPaid = order.payment_status === 'succeeded';
    const status = order.status;

    if (status === 'cancelled') {
        return `<span class="badge badge-danger">${t.badge_cancelled}</span>`;
    }

    if (!isPaid) {
        return `<span class="badge badge-warning">${t.badge_awaiting_payment}</span>`;
    }

    if (status === 'delivered') {
        return `<span class="badge badge-success">${t.badge_delivered}</span>`;
    }

    if (status === 'shipped') {
        return `<span class="badge badge-primary">${t.badge_shipped}</span>`;
    }

    return `<span class="badge badge-info">${t.badge_paid}</span>`;
}

function getOrderActionButtons(order) {
    const isPaid = order.payment_status === 'succeeded';
    const isCancelled = order.status === 'cancelled';
    const isDelivered = order.status === 'delivered';
    const isShipped = order.status === 'shipped';
    const hasTracking = order.tracking_id && order.shipping_provider;

    const buttons = [];

    if (isPaid) {
        buttons.push(`
            <button class="btn btn-outline-primary btn-sm" onclick="downloadInvoice('${order.order_number}')">
                <i data-feather="download"></i> ${t.btn_invoice}
            </button>
        `);
    }

    if (isPaid && !isCancelled) {
        const hasInventoryAssigned = order.items && order.items.some(item =>
            item.inventory_items && item.inventory_items.length > 0 && item.inventory_items.some(inv => inv.device_id)
        );

        buttons.push(`
            <button class="btn btn-info btn-sm" onclick="showAssignInventoryModalFromOrderView('${order.order_number}')">
                <i data-feather="package"></i> ${hasInventoryAssigned ? t.btn_update_inventory : t.btn_assign_inventory}
            </button>
        `);
    }

    if (isPaid && !isCancelled && !isDelivered) {
        buttons.push(`
            <button class="btn btn-success btn-sm" onclick="showTrackingModalFromOrderView('${order.order_number}')">
                <i data-feather="truck"></i> ${hasTracking ? t.btn_update_tracking : t.btn_add_tracking}
            </button>
        `);
    }

    if (!isPaid && !isCancelled) {
        const isStripe = order.payment_method === 'stripe';
        buttons.push(`
            <button class="btn btn-warning btn-sm" onclick="if(confirm('${t.confirm_payment_received}')) markAsPaid('${order.order_number}', ${isStripe})">
                <i data-feather="check-circle"></i> ${t.btn_confirm_payment}${isStripe ? ' (Stripe)' : ''}
            </button>
        `);
    }

    if (isPaid && hasTracking && !isShipped && !isDelivered && !isCancelled) {
        buttons.push(`
            <button class="btn btn-primary btn-sm" onclick="updateStatus('${order.order_number}', 'shipped')">
                <i data-feather="send"></i> ${t.btn_mark_shipped}
            </button>
        `);
    }

    if (isShipped && !isDelivered) {
        buttons.push(`
            <button class="btn btn-primary btn-sm" onclick="updateStatus('${order.order_number}', 'delivered')">
                <i data-feather="check"></i> ${t.btn_mark_delivered}
            </button>
        `);
    }

    if (!isCancelled && !isDelivered) {
        buttons.push(`
            <button class="btn btn-outline-danger btn-sm" onclick="if(confirm('${t.confirm_cancel}')) updateStatus('${order.order_number}', 'cancelled')">
                <i data-feather="x"></i> ${t.btn_cancel_order}
            </button>
        `);
    }

    if (buttons.length === 0) {
        if (isCancelled) {
            return `<div class="text-center text-muted py-2"><i data-feather="x-circle"></i> ${t.status_info_cancelled}</div>`;
        }
        if (isDelivered) {
            return `<div class="text-center text-muted py-2"><i data-feather="check-circle"></i> ${t.status_info_completed}</div>`;
        }
    }

    return buttons.join(' ');
}

async function markAsPaid(orderNumber, isStripe = false) {
    const token = UserManager.getToken();

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
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        const data = await response.json();

        if (response.ok) {
            toastr.success(isStripe ? t.payment_stripe_confirmed : t.payment_confirmed);
            $('#order-modal').modal('hide');
            loadOrders();
        } else {
            toastr.error(data.message || t.payment_confirm_failed);
        }
    } catch (error) {
        console.error('Error confirming payment:', error);
        toastr.error(t.payment_confirm_failed);
    }
}

// Assign Inventory Functions
let currentOrderForInventory = null;

function showAssignInventoryModalFromOrderView(orderNumber) {
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
            return {
                orderItem: item,
                availableInventory: (invData.items || []).filter(inv =>
                    inv.status === 'available' || inv.order_item_id == item.id
                )
            };
        });

        const inventoryData = await Promise.all(inventoryPromises);

        let modalContent = `<div class="alert alert-info">${t.inv_instructions}</div>`;

        inventoryData.forEach(({ orderItem, availableInventory }) => {
            modalContent += `
                <div class="mb-4 p-3 border rounded">
                    <h5>${orderItem.product_model.name}</h5>
                    <p class="text-muted">
                        ${t.inv_label_quantity}: ${orderItem.quantity} |
                        ${t.inv_label_available}: ${availableInventory.length}
                    </p>
                    <div class="form-group">
                        <label>${t.inv_select_items.replace('{n}', orderItem.quantity)}</label>
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
                        <small class="form-text text-muted">${t.inv_click_to_select}</small>
                    </div>
                </div>
            `;
        });

        document.getElementById('assign-inventory-content').innerHTML = modalContent;
        $('#assign-inventory-modal').modal('show');
    } catch (error) {
        console.error('Error loading order for inventory assignment:', error);
        toastr.error(t.inv_error_load_order);
    }
}

async function assignInventoryToOrder() {
    const token = UserManager.getToken();
    const checkboxLists = document.querySelectorAll('.inventory-checkbox-list');
    const assignments = [];
    let hasError = false;

    checkboxLists.forEach(list => {
        const orderItemId = list.dataset.orderItemId;
        const required = parseInt(list.dataset.required);
        const checkboxes = list.querySelectorAll('.inventory-checkbox:checked');
        const selected = Array.from(checkboxes).map(cb => parseInt(cb.value));

        if (selected.length !== required) {
            toastr.error(t.inv_error_select_exactly.replace('{n}', required));
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

        toastr.success(t.inv_assigned_success);
        $('#assign-inventory-modal').modal('hide');
        loadOrders();
    } catch (error) {
        console.error('Error assigning inventory:', error);
        toastr.error(error.message || t.inv_error_assign);
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
            toastr.error(t.session_expired);
            UserManager.logout(true);
            return;
        }

        if (!response.ok) {
            const error = await response.json();
            toastr.error(error.message || t.invoice_download_failed);
            return;
        }

        const blob = await response.blob();

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = t.invoice_filename.replace('{order}', orderNumber);
        document.body.appendChild(a);
        a.click();

        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        toastr.success(t.invoice_downloaded);
    } catch (error) {
        console.error('Error downloading invoice:', error);
        toastr.error(t.invoice_download_failed);
    }
}
