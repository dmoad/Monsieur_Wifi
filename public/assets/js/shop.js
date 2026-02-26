// Shop listing page (English)
const LOCALE = 'en';

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    updateCartCount();
});

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
    
    grid.innerHTML = products.map(product => `
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="product-card ${!product.is_in_stock ? 'out-of-stock' : ''}">
                <div class="product-image-wrapper">
                    <img src="${product.primary_image || '/app-assets/images/placeholder.png'}" 
                         alt="${product.name}" 
                         class="product-image">
                    ${product.is_in_stock 
                        ? '<span class="stock-badge badge-success">In Stock</span>' 
                        : '<span class="stock-badge badge-danger">Out of Stock</span>'}
                </div>
                <div class="product-body">
                    <h5 class="product-title">${product.name}</h5>
                    <p class="product-description">${product.description_en || 'High-quality WiFi equipment for your network needs.'}</p>
                    <div class="product-footer">
                        <h3 class="product-price">$${parseFloat(product.price).toFixed(2)}</h3>
                        <div class="product-actions">
                            ${product.is_in_stock 
                                ? `<button onclick="addToCart(${product.id})" class="btn btn-success btn-sm product-btn mr-1" title="Add to Cart">
                                    <i data-feather="shopping-cart" style="width: 14px; height: 14px;"></i>
                                </button>` 
                                : ''}
                            <a href="/en/shop/${product.slug}" class="btn btn-primary btn-sm product-btn" title="View Details">
                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
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
            toastr.success('Product added to cart!');
            updateCartCount();
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
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

async function updateCartCount() {
    const token = UserManager.getToken();
    if (!token) {
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
            const cart = await response.json();
            const count = cart.items ? cart.items.reduce((sum, item) => sum + item.quantity, 0) : 0;
            document.getElementById('cart-count').textContent = count;
        }
    } catch (error) {
        console.error('Error loading cart count:', error);
    }
}
