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
        
        const orders = await response.json();
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
                        <p class="text-muted mb-0">${new Date(order.created_at).toLocaleDateString()}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Statut</small>
                        <p class="mb-0">${getStatusBadge(order.status)}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Total</small>
                        <p class="mb-0"><strong>$${parseFloat(order.total).toFixed(2)}</strong></p>
                    </div>
                    <div class="col-md-3 text-right">
                        <a href="/fr/commandes/${order.order_number}" class="btn btn-primary btn-sm">Voir Détails</a>
                    </div>
                </div>
                ${order.tracking_id ? `
                    <div class="mt-3">
                        <small class="text-muted">Suivi: ${order.shipping_provider} - ${order.tracking_id}</small>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge badge-warning">En Attente</span>',
        'processing': '<span class="badge badge-info">En Traitement</span>',
        'completed': '<span class="badge badge-success">Terminée</span>',
        'shipped': '<span class="badge badge-primary">Expédiée</span>',
        'delivered': '<span class="badge badge-success">Livrée</span>',
        'cancelled': '<span class="badge badge-danger">Annulée</span>',
        'payment_failed': '<span class="badge badge-danger">Échec du Paiement</span>'
    };
    return badges[status] || `<span class="badge badge-secondary">${status}</span>`;
}
