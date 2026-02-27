// Orders list page (French)
const LOCALE = 'fr';

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Veuillez vous connecter pour voir vos commandes');
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
        toastr.error('Échec du chargement des commandes');
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
                        <h6>Commande #${order.order_number}</h6>
                        <p class="text-muted mb-0"><small>Commandé: ${new Date(order.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })}</small></p>
                        ${order.delivered_at ? `<p class="text-success mb-0 mt-1"><small><i data-feather="check-circle" style="width: 14px; height: 14px;"></i> Livré: ${new Date(order.delivered_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })}</small></p>` : ''}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Statut</small>
                        <p class="mb-0">${getStatusBadge(order)}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Total</small>
                        <p class="mb-0"><strong>$${parseFloat(order.total).toFixed(2)}</strong></p>
                        ${order.tax_amount ? `<small class="text-muted">(Taxe: $${parseFloat(order.tax_amount).toFixed(2)})</small>` : ''}
                    </div>
                    <div class="col-md-3 text-right">
                        <a href="/fr/commandes/${order.order_number}" class="btn btn-primary btn-sm">Voir Détails</a>
                        ${order.payment_status === 'succeeded' ? `<button onclick="downloadInvoice('${order.order_number}')" class="btn btn-outline-secondary btn-sm mt-1"><i data-feather="download" style="width: 14px; height: 14px;"></i> Facture</button>` : ''}
                    </div>
                </div>
                ${order.tracking_id ? `
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted"><i data-feather="truck" style="width: 14px; height: 14px;"></i> Suivi: <strong>${order.shipping_provider}</strong> - ${order.tracking_id}</small>
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
        return '<span class="badge badge-danger">Annulée</span>';
    }
    
    // If not paid, show awaiting payment
    if (!isPaid) {
        return '<span class="badge badge-warning">En attente de paiement</span>';
    }
    
    // If delivered, show delivered
    if (status === 'delivered') {
        return '<span class="badge badge-success">Livrée</span>';
    }
    
    // If shipped, show shipped
    if (status === 'shipped') {
        return '<span class="badge badge-primary">Expédiée</span>';
    }
    
    // Otherwise, payment received (paid but not shipped)
    return '<span class="badge badge-success">Paiement reçu</span>';
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
            toastr.error('Session expirée. Veuillez vous reconnecter.');
            UserManager.logout(true);
            return;
        }
        
        if (!response.ok) {
            const error = await response.json();
            toastr.error(error.message || 'Échec du téléchargement de la facture');
            return;
        }
        
        // Get the blob from response
        const blob = await response.blob();
        
        // Create a download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `facture-${orderNumber}.pdf`;
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        toastr.success('Facture téléchargée avec succès');
    } catch (error) {
        console.error('Error downloading invoice:', error);
        toastr.error('Échec du téléchargement de la facture');
    }
}
