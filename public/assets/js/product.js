// Product detail page — translations injected by blade (lang/{en,fr}/product.php)
const t = window.APP_I18N.product;
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
                <p class="text-danger">${t.not_found}</p>
                <a href="${t.shop_url}" class="btn btn-primary">${t.btn_back_to_shop}</a>
            </div>
        `;
    }
}

function displayProduct(product) {
    document.getElementById('product-name').textContent = product.name;
    document.getElementById('product-price').textContent = `€${parseFloat(product.price).toFixed(2)}`;
    document.getElementById('product-description').innerHTML = product['description_' + t.locale] || '';
    
    const mainImage = product.primary_image || '/app-assets/images/placeholder.png';
    document.getElementById('main-image').src = mainImage;
    
    if (product.images && product.images.length > 1) {
        const thumbnails = document.getElementById('thumbnails');
        thumbnails.innerHTML = product.images.map((img, index) => `
            <img src="${img.image_url}" alt="${t.alt_thumbnail}" class="thumbnail ${index === 0 ? 'active' : ''}"
                 onclick="changeImage('${img.image_url}', this)">
        `).join('');
    }
    
    const stockStatus = document.getElementById('stock-status');
    if (product.is_in_stock) {
        stockStatus.innerHTML = `<span class="badge badge-success">${t.badge_in_stock_html.replace('{n}', product.available_quantity)}</span>`;
        document.getElementById('quantity').max = product.available_quantity;
    } else {
        stockStatus.innerHTML = `<span class="badge badge-danger">${t.badge_out_of_stock}</span>`;
        document.getElementById('add-to-cart-btn').disabled = true;
    }
    
    document.getElementById('product-loading').style.display = 'none';
    document.getElementById('product-details').style.display = 'flex';
    feather.replace();
}

function changeImage(url, thumbnail) {
    document.getElementById('main-image').src = url;
    document.querySelectorAll('.thumbnail').forEach(el => el.classList.remove('active'));
    thumbnail.classList.add('active');
}

async function addToCart() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning(t.toast_login_required);
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
            toastr.success(t.toast_added);
            // Refresh navbar cart if function exists
            if (typeof loadNavbarCart === 'function') {
                loadNavbarCart();
            }
            setTimeout(() => window.location.href = t.cart_url, 1000);
        } else {
            // Show validation errors if present
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || t.toast_add_failed);
            }
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        toastr.error(t.toast_add_failed);
    }
}
