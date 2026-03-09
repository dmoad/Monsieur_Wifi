// Cart page (English)
const LOCALE = 'en';
let currentCart = null;

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Please login to view your cart');
        window.location.href = '/login';
        return;
    }
    
    loadCart();
});

async function loadCart() {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load cart');
        }
        
        const data = await response.json();
        currentCart = data.cart;
        displayCart(data);
    } catch (error) {
        console.error('Error loading cart:', error);
        document.getElementById('cart-loading').style.display = 'none';
        toastr.error('Failed to load cart');
    }
}

function displayCart(data) {
    document.getElementById('cart-loading').style.display = 'none';
    
    const cart = data.cart;
    if (!cart.items || cart.items.length === 0) {
        document.getElementById('cart-empty').style.display = 'block';
        return;
    }
    
    const itemsContainer = document.getElementById('cart-items');
    itemsContainer.innerHTML = cart.items.map(item => `
        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
            <img src="${item.product_model.primary_image || '/app-assets/images/placeholder.png'}" 
                 alt="${item.product_model.name}" 
                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
            <div class="ml-3 flex-grow-1">
                <h6 class="mb-1">${item.product_model.name}</h6>
                <p class="text-muted mb-0">€${parseFloat(item.price_at_add).toFixed(2)} each</p>
            </div>
            <div class="d-flex align-items-center">
                <input type="number" class="form-control" style="width: 80px;"
                       value="${item.quantity}" min="1" max="${item.product_model.available_quantity}"
                       onchange="updateQuantity(${item.id}, this.value)">
                <button class="btn btn-danger ml-2 d-flex align-items-center justify-content-center" style="height: 38px; width: 38px; padding: 0;" onclick="removeItem(${item.id})">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
            <div class="ml-3 text-right" style="min-width: 100px;">
                <strong>€${parseFloat(item.price_at_add * item.quantity).toFixed(2)}</strong>
            </div>
        </div>
    `).join('');
    
    document.getElementById('cart-subtotal').textContent = `€${parseFloat(data.total).toFixed(2)}`;
    document.getElementById('cart-total').textContent = `€${parseFloat(data.total).toFixed(2)}`;
    
    document.getElementById('cart-content').style.display = 'flex';
    feather.replace();
}

async function updateQuantity(itemId, quantity) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items/${itemId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: parseInt(quantity) })
        });
        
        if (response.ok) {
            loadCart();
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
        } else {
            const data = await response.json();
            toastr.error(data.message || 'Failed to update quantity');
            loadCart();
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        toastr.error('Failed to update quantity');
    }
}

async function removeItem(itemId) {
    const token = UserManager.getToken();
    
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            toastr.success('Item removed from cart');
            loadCart();
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
        } else {
            toastr.error('Failed to remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        toastr.error('Failed to remove item');
    }
}
