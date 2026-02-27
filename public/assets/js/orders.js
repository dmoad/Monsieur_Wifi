// Orders list page (English)
const LOCALE = 'en';

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Please login to view your orders');
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
        toastr.error('Failed to load orders');
    }
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
                        <h6>Order #${order.order_number}</h6>
                        <p class="text-muted mb-0"><small>Ordered: ${new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small></p>
                        ${order.delivered_at ? `<p class="text-success mb-0 mt-1"><small><i data-feather="check-circle" style="width: 14px; height: 14px;"></i> Delivered: ${new Date(order.delivered_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small></p>` : ''}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">${getStatusBadge(order)}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Total</small>
                        <p class="mb-0"><strong>$${parseFloat(order.total).toFixed(2)}</strong></p>
                        ${order.tax_amount ? `<small class="text-muted">(Tax: $${parseFloat(order.tax_amount).toFixed(2)})</small>` : ''}
                    </div>
                    <div class="col-md-3 text-right">
                        <a href="/en/orders/${order.order_number}" class="btn btn-primary btn-sm">View Details</a>
                        ${order.payment_status === 'succeeded' ? `<button onclick="downloadInvoice('${order.order_number}')" class="btn btn-outline-secondary btn-sm mt-1"><i data-feather="download" style="width: 14px; height: 14px;"></i> Invoice</button>` : ''}
                    </div>
                </div>
                ${order.tracking_id ? `
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted"><i data-feather="truck" style="width: 14px; height: 14px;"></i> Tracking: <strong>${order.shipping_provider}</strong> - ${order.tracking_id}</small>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Refresh feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function getStatusBadge(order) {
    const isPaid = order.payment_status === 'succeeded';
    const status = order.status;
    
    // Cancelled orders always show as cancelled
    if (status === 'cancelled') {
        return '<span class="badge badge-danger">Cancelled</span>';
    }
    
    // If not paid, show awaiting payment
    if (!isPaid) {
        return '<span class="badge badge-warning">Awaiting payment</span>';
    }
    
    // If delivered, show delivered
    if (status === 'delivered') {
        return '<span class="badge badge-success">Delivered</span>';
    }
    
    // If shipped, show shipped
    if (status === 'shipped') {
        return '<span class="badge badge-primary">Shipped</span>';
    }
    
    // Otherwise, payment received (paid but not shipped)
    return '<span class="badge badge-success">Payment received</span>';
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
            toastr.error('Session expired. Please login again.');
            UserManager.logout(true);
            return;
        }
        
        if (!response.ok) {
            const error = await response.json();
            toastr.error(error.message || 'Failed to download invoice');
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
        
        toastr.success('Invoice downloaded successfully');
    } catch (error) {
        console.error('Error downloading invoice:', error);
        toastr.error('Failed to download invoice');
    }
}
