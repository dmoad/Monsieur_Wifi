// Product detail page (French)
const LOCALE = 'fr';
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
                <p class="text-danger">Produit non trouvé.</p>
                <a href="/fr/boutique" class="btn btn-primary">Retour à la Boutique</a>
            </div>
        `;
    }
}

function displayProduct(product) {
    document.getElementById('product-name').textContent = product.name;
    document.getElementById('product-price').textContent = `$${parseFloat(product.price).toFixed(2)}`;
    document.getElementById('product-description').innerHTML = product.description_fr || '';
    
    const mainImage = product.primary_image || '/app-assets/images/placeholder.png';
    document.getElementById('main-image').src = mainImage;
    
    if (product.images && product.images.length > 1) {
        const thumbnails = document.getElementById('thumbnails');
        thumbnails.innerHTML = product.images.map((img, index) => `
            <img src="${img.url}" alt="Miniature" class="thumbnail ${index === 0 ? 'active' : ''}" 
                 onclick="changeImage('${img.url}', this)">
        `).join('');
    }
    
    const stockStatus = document.getElementById('stock-status');
    if (product.is_in_stock) {
        stockStatus.innerHTML = `<span class="badge badge-success">En Stock (${product.available_stock} disponibles)</span>`;
        document.getElementById('quantity').max = product.available_stock;
    } else {
        stockStatus.innerHTML = `<span class="badge badge-danger">En Rupture de Stock</span>`;
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
        toastr.warning('Veuillez vous connecter pour ajouter des articles au panier');
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
            toastr.success('Produit ajouté au panier!');
            setTimeout(() => window.location.href = '/fr/panier', 1000);
        } else {
            // Show validation errors if present
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || 'Échec de l\'ajout au panier');
            }
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        toastr.error('Échec de l\'ajout au panier');
    }
}
