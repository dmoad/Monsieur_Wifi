// Product detail page (English)
const LOCALE = 'en';
let currentProduct = null;

document.addEventListener('DOMContentLoaded', function() {
    const slug = window.location.pathname.split('/').pop();
    loadProduct(slug);
    
    document.getElementById('add-to-cart-btn').addEventListener('click', addToCart);
});

async function loadProduct(slug) {
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/shop/products/${slug}`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Product not found');
        }
        
        const data = await response.json();
        currentProduct = data.product || data;
        displayProduct(currentProduct);
    } catch (error) {
        console.error('Error loading product:', error);
        document.getElementById('product-loading').innerHTML = `
            <div class="col-12 text-center">
                <p class="text-danger">Product not found.</p>
                <a href="/en/shop" class="btn btn-primary">Back to Shop</a>
            </div>
        `;
    }
}

function displayProduct(product) {
    document.getElementById('product-name').textContent = product.name;
    document.getElementById('product-price').textContent = `€${parseFloat(product.price).toFixed(2)}`;
    document.getElementById('product-description').innerHTML = product.description_en || '';
    
    const mainImage = product.primary_image || '/app-assets/images/placeholder.png';
    document.getElementById('main-image').src = mainImage;
    
    if (product.images && product.images.length > 1) {
        const thumbnails = document.getElementById('thumbnails');
        thumbnails.innerHTML = product.images.map((img, index) => `
            <img src="${img.image_url}" alt="Thumbnail" class="thumbnail ${index === 0 ? 'active' : ''}" 
                 onclick="changeImage('${img.image_url}', this)">
        `).join('');
    }
    
    const stockStatus = document.getElementById('stock-status');
    if (product.is_in_stock) {
        stockStatus.innerHTML = `<span class="badge badge-success">In Stock (${product.available_quantity} available)</span>`;
        document.getElementById('quantity').max = product.available_quantity;
    } else {
        stockStatus.innerHTML = `<span class="badge badge-danger">Out of Stock</span>`;
        document.getElementById('add-to-cart-btn').disabled = true;
    }
    
    document.getElementById('product-loading').style.display = 'none';
    document.getElementById('product-details').style.display = 'flex';
    feather.replace();
}

function changeImage(url, thumbnail) {
    document.getElementById('main-image').src = url;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}

async function addToCart() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Please login to add items to cart');
        window.location.href = '/login';
        return;
    }
    
    const quantity = parseInt(document.getElementById('quantity').value);
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart/items`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: currentProduct.id,
                quantity: quantity
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success('Product added to cart!');
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
            setTimeout(() => window.location.href = '/en/cart', 1000);
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
