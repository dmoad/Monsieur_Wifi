// Orders list page — translations injected by blade (lang/{en,fr}/orders.php)
const t = window.APP_I18N.orders;

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning(t.toast_login_required);
        window.location.href = '/login';
        return;
    }

    loadOrders();
});

async function loadOrders() {
    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load orders');
        }

        const data = await response.json();
        const orders = data.orders?.data || data.orders || [];
        displayOrders(orders);
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-loading').style.display = 'none';
        toastr.error(t.toast_load_failed);
    }
}

function formatDate(iso) {
    return new Date(iso).toLocaleDateString(t.date_locale, { day: 'numeric', month: 'short', year: 'numeric' });
}

function displayOrders(orders) {
    document.getElementById('orders-loading').style.display = 'none';

    if (!orders || orders.length === 0) {
        document.getElementById('orders-empty').style.display = 'block';
        return;
    }

    const container = document.getElementById('orders-list');
    container.innerHTML = orders.map(order => `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6>${t.order_number_prefix}${order.order_number}</h6>
                        <p class="text-muted mb-0"><small>${t.label_ordered.replace('{date}', formatDate(order.created_at))}</small></p>
                        ${order.delivered_at ? `<p class="text-success mb-0 mt-1"><small><i data-feather="check-circle" style="width: 14px; height: 14px;"></i> ${t.label_delivered_on.replace('{date}', formatDate(order.delivered_at))}</small></p>` : ''}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">${t.label_status}</small>
                        <p class="mb-0">${getStatusBadge(order)}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">${t.label_total}</small>
                        <p class="mb-0"><strong>€${parseFloat(order.total).toFixed(2)}</strong></p>
                        ${order.tax_amount ? `<small class="text-muted">${t.label_tax.replace('{amount}', parseFloat(order.tax_amount).toFixed(2))}</small>` : ''}
                    </div>
                    <div class="col-md-3 d-flex align-items-center justify-content-end" style="gap: 0.5rem;">
                        <a href="${t.orders_base}/${order.order_number}" class="btn btn-primary btn-sm">${t.btn_view_details}</a>
                        ${order.payment_status === 'succeeded' ? `<button onclick="downloadInvoice('${order.order_number}')" class="btn btn-outline-secondary btn-sm"><i data-feather="download" style="width: 14px; height: 14px;"></i> ${t.btn_invoice}</button>` : ''}
                    </div>
                </div>
                ${order.tracking_id ? `
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted"><i data-feather="truck" style="width: 14px; height: 14px;"></i> ${t.tracking_html.replace('{provider}', order.shipping_provider).replace('{id}', order.tracking_id)}</small>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function getStatusBadge(order) {
    const isPaid = order.payment_status === 'succeeded';
    const status = order.status;

    if (status === 'cancelled') {
        return `<span class="badge badge-danger">${t.status_cancelled}</span>`;
    }

    if (!isPaid) {
        return `<span class="badge badge-warning">${t.status_awaiting_payment}</span>`;
    }

    if (status === 'delivered') {
        return `<span class="badge badge-success">${t.status_delivered}</span>`;
    }

    if (status === 'shipped') {
        return `<span class="badge badge-primary">${t.status_shipped}</span>`;
    }

    return `<span class="badge badge-success">${t.status_paid}</span>`;
}

async function downloadInvoice(orderNumber) {
    const token = UserManager.getToken();

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders/${orderNumber}/invoice`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/pdf'
            }
        });

        if (response.status === 401) {
            toastr.error(t.toast_session_expired);
            UserManager.logout(true);
            return;
        }

        if (!response.ok) {
            const error = await response.json();
            toastr.error(error.message || t.toast_invoice_failed);
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

        toastr.success(t.toast_invoice_downloaded);
    } catch (error) {
        console.error('Error downloading invoice:', error);
        toastr.error(t.toast_invoice_failed);
    }
}
