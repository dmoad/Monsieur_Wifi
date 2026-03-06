// Shop listing page (English)
const LOCALE = 'en';
let currentCart = null;

document.addEventListener('DOMContentLoaded', function() {
    loadCartData().then(() => {
        loadProducts();
    });
    updateCartCount();
});

async function loadCartData() {
    const token = UserManager.getToken();
    if (!token) {
        currentCart = { items: [] };
        return;
    }

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            currentCart = data.cart || data; // API returns {success, cart, total}, use cart object
            console.log('Cart loaded:', currentCart);
        } else {
            currentCart = { items: [] };
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        currentCart = { items: [] };
    }
}

function getCartQuantityForProduct(productId) {
    if (!currentCart || !currentCart.items) return 0;
    const item = currentCart.items.find(item => item.product_id === productId || item.product_model_id === productId);
    return item ? item.quantity : 0;
}

function getCartItemForProduct(productId) {
    if (!currentCart || !currentCart.items) return null;
    return currentCart.items.find(item => item.product_id === productId || item.product_model_id === productId);
}

async function loadProducts() {
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/shop/products`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load products');
        }

        const data = await response.json();
        const products = data.products.data || data.products || [];
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('products-grid').innerHTML = `
            <div class="col-12 text-center">
                <p class="text-danger">Failed to load products. Please try again later.</p>
            </div>
        `;
    }
}

function displayProducts(products) {
    const grid = document.getElementById('products-grid');

    if (products.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <p>No products available at the moment.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = products.map(product => {
        const cartQty = getCartQuantityForProduct(product.id);
        const availableInventory = (product.inventory && product.inventory.available_quantity) || 0;
        const totalAvailable = availableInventory + cartQty; // Total including what's already reserved
        const canAddMore = product.is_in_stock && cartQty < totalAvailable;

        console.log(`Product ${product.id}: cartQty=${cartQty}, available=${availableInventory}, total=${totalAvailable}, canAddMore=${canAddMore}`);

        return `
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <a href="/en/shop/${product.slug}" class="product-card-link">
                <div class="product-card ${!product.is_in_stock ? 'out-of-stock' : ''}">
                    <div class="product-image-wrapper">
                        <img src="${product.primary_image || '/app-assets/images/placeholder.png'}"
                             alt="${product.name}"
                             class="product-image">
                        ${!product.is_in_stock
                            ? '<span class="stock-badge badge-danger">Out of Stock</span>'
                            : (product.inventory && totalAvailable <= (product.inventory.low_stock_threshold || 0)
                                ? `<span class="stock-badge badge-warning">Low Stock</span>`
                                : `<span class="stock-badge badge-success">In Stock</span>`)}
                        ${cartQty > 0
                            ? `<span class="cart-qty-badge">${cartQty} in cart</span>`
                            : ''}
                    </div>
                    <div class="product-body">
                        <h5 class="product-title">${product.name}</h5>
                        <p class="product-description">${product.description_en || 'High-quality WiFi equipment for your network needs.'}</p>
                        <div class="product-footer">
                            <h3 class="product-price">€${parseFloat(product.price).toFixed(2)}</h3>
                            <div class="product-actions">
                                ${cartQty > 0
                                    ? `<div class="qty-controls" onclick="event.preventDefault(); event.stopPropagation();">
                                        <button onclick="event.preventDefault(); event.stopPropagation(); decreaseCartQuantity(${product.id})" class="btn btn-outline-secondary btn-sm qty-btn" title="Decrease quantity">
                                            <i data-feather="minus" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <span class="qty-display">${cartQty}</span>
                                        <button onclick="event.preventDefault(); event.stopPropagation(); increaseCartQuantity(${product.id})" class="btn btn-outline-secondary btn-sm qty-btn"
                                            ${!canAddMore ? 'disabled title="Maximum quantity reached"' : 'title="Increase quantity"'}>
                                            <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>`
                                    : (product.is_in_stock
                                        ? `<button onclick="event.preventDefault(); event.stopPropagation(); addToCart(${product.id})" class="btn btn-success btn-sm product-btn" title="Add to Cart">
                                            <i data-feather="shopping-cart" style="width: 14px; height: 14px;"></i>
                                        </button>`
                                        : '')}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    `;
    }).join('');

    // Re-initialize feather icons for the new content
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

async function addToCart(productId) {
    const token = UserManager.getToken();

    if (!token) {
        toastr.warning('Please login to add items to cart');
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });

        const data = await response.json();

        if (response.ok) {
            toastr.success('Product added to cart! <a href="/en/cart">View cart</a>');
            updateCartCount();
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
            // Reload cart data and products to update UI
            await loadCartData();
            await loadProducts();
        } else {
            // Show validation errors if present
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || 'Failed to add to cart');
            }
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        toastr.error('Failed to add to cart');
    }
}

async function increaseCartQuantity(productId) {
    const currentQty = getCartQuantityForProduct(productId);
    await updateCartItemQuantity(productId, currentQty + 1);
}

async function decreaseCartQuantity(productId) {
    const currentQty = getCartQuantityForProduct(productId);
    if (currentQty > 1) {
        await updateCartItemQuantity(productId, currentQty - 1);
    } else {
        await removeFromCart(productId);
    }
}

async function updateCartItemQuantity(productId, newQuantity) {
    const token = UserManager.getToken();
    if (!token) return;

    const cartItem = getCartItemForProduct(productId);
    if (!cartItem) return;

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items/${cartItem.id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: newQuantity
            })
        });

        const data = await response.json();

        if (response.ok) {
            toastr.success('Cart updated!');
            updateCartCount();
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
            await loadCartData();
            await loadProducts();
        } else {
            toastr.error(data.message || 'Failed to update cart');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        toastr.error('Failed to update cart');
    }
}

async function removeFromCart(productId) {
    const token = UserManager.getToken();
    if (!token) return;

    const cartItem = getCartItemForProduct(productId);
    if (!cartItem) return;

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items/${cartItem.id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            toastr.success('Item removed from cart');
            updateCartCount();
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
            await loadCartData();
            await loadProducts();
        } else {
            const data = await response.json();
            toastr.error(data.message || 'Failed to remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        toastr.error('Failed to remove item');
    }
}

async function updateCartCount() {
    const token = UserManager.getToken();
    if (!token) {
        return;
    }

    const cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) {
        return; // Element doesn't exist, skip update
    }

    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const cart = await response.json();
            const count = cart.items ? cart.items.reduce((sum, item) => sum + item.quantity, 0) : 0;
            cartCountElement.textContent = count;
        }
    } catch (error) {
        console.error('Error loading cart count:', error);
    }
}
