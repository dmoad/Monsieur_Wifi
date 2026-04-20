// Order success page — translations injected by blade (lang/{en,fr}/order_success.php)
const t = window.APP_I18N.order_success;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Order success page loaded');
    console.log('Current URL:', window.location.href);
    console.log('UserManager available:', typeof UserManager !== 'undefined');

    const token = UserManager.getToken();
    console.log('Token exists:', !!token);
    console.log('Token value:', token ? token.substring(0, 20) + '...' : 'null');

    if (!token) {
        console.error('No token found, redirecting to login');
        toastr.warning(t.toast_login_required);
        window.location.href = '/login';
        return;
    }

    const orderNumber = window.location.pathname.split('/').pop();
    console.log('Loading order:', orderNumber);
    loadOrder(orderNumber);
});

async function loadOrder(orderNumber) {
    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders/${orderNumber}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Order not found');
        }

        const order = await response.json();
        displayOrder(order);
    } catch (error) {
        console.error('Error loading order:', error);
        document.getElementById('order-loading').innerHTML = `
            <div class="col-12 text-center">
                <p class="text-danger">${t.error_not_found}</p>
                <a href="${t.orders_url}" class="btn btn-primary">${t.btn_view_orders}</a>
            </div>
        `;
    }
}

function displayOrder(order) {
    document.getElementById('order-number').textContent = order.order_number;
    document.getElementById('info-order-number').textContent = order.order_number;
    document.getElementById('info-date').textContent = new Date(order.created_at).toLocaleDateString(t.date_locale);
    document.getElementById('info-status').innerHTML = getStatusBadge(order.status);
    document.getElementById('info-total').textContent = `€${parseFloat(order.total).toFixed(2)}`;

    const address = order.shipping_address;
    document.getElementById('shipping-address').innerHTML = `
        <p class="mb-0">${address.first_name} ${address.last_name}</p>
        ${address.company ? `<p class="mb-0">${address.company}</p>` : ''}
        <p class="mb-0">${address.address_line1}</p>
        ${address.address_line2 ? `<p class="mb-0">${address.address_line2}</p>` : ''}
        <p class="mb-0">${address.city}, ${address.province} ${address.postal_code}</p>
        <p class="mb-0">${address.country}</p>
        <p class="mb-0">${address.phone}</p>
    `;

    const itemsContainer = document.getElementById('order-items');
    itemsContainer.innerHTML = order.items.map(item => `
        <div class="d-flex justify-content-between mb-2">
            <span>${item.product_model.name} × ${item.quantity}</span>
            <span>€${parseFloat(item.subtotal).toFixed(2)}</span>
        </div>
    `).join('');

    document.getElementById('summary-subtotal').textContent = `€${parseFloat(order.product_amount).toFixed(2)}`;
    document.getElementById('summary-shipping').textContent = `€${parseFloat(order.shipping_cost).toFixed(2)}`;
    document.getElementById('summary-tax').textContent = `€${parseFloat(order.tax_amount).toFixed(2)}`;
    document.getElementById('summary-total').textContent = `€${parseFloat(order.total).toFixed(2)}`;

    document.getElementById('order-loading').style.display = 'none';
    document.getElementById('order-details').style.display = 'block';
    feather.replace();
}

function getStatusBadge(status) {
    const badges = {
        'pending': `<span class="badge badge-warning">${t.status_pending}</span>`,
        'processing': `<span class="badge badge-info">${t.status_processing}</span>`,
        'completed': `<span class="badge badge-success">${t.status_completed}</span>`,
        'shipped': `<span class="badge badge-primary">${t.status_shipped}</span>`,
        'delivered': `<span class="badge badge-success">${t.status_delivered}</span>`,
        'cancelled': `<span class="badge badge-danger">${t.status_cancelled}</span>`,
        'payment_failed': `<span class="badge badge-danger">${t.status_payment_failed}</span>`
    };
    return badges[status] || `<span class="badge badge-secondary">${status}</span>`;
}
