// Checkout page (English)
const LOCALE = 'en';
let cart = null;
let shippingRates = [];
let selectedShipping = null;

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Please login to checkout');
        window.location.href = '/login';
        return;
    }
    
    loadCart();
    loadShippingRates();
    
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        document.getElementById('billing-section').style.display = this.checked ? 'none' : 'block';
    });
    
    document.getElementById('checkout-form').addEventListener('submit', handleSubmit);
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
        
        if (response.ok) {
            const data = await response.json();
            cart = data;  // Store the whole response with cart, total, item_count
            if (!data.cart.items || data.cart.items.length === 0) {
                toastr.warning('Your cart is empty');
                window.location.href = '/en/shop';
                return;
            }
            displayOrderSummary();
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

async function loadShippingRates() {
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/shop/shipping-rates`, {
            headers: { 'Content-Type': 'application/json' }
        });
        
        if (response.ok) {
            const data = await response.json();
            shippingRates = data.shipping_rates || data;
            displayShippingMethods();
        }
    } catch (error) {
        console.error('Error loading shipping rates:', error);
    }
}

function displayShippingMethods() {
    const container = document.getElementById('shipping-methods');
    document.getElementById('shipping-methods-loading').style.display = 'none';
    
    container.innerHTML = shippingRates.map((rate, index) => `
        <div class="custom-control custom-radio mb-2">
            <input type="radio" class="custom-control-input" id="shipping-${rate.id}" 
                   name="shipping_method" value="${rate.method}" 
                   ${index === 0 ? 'checked' : ''} 
                   onchange="selectShipping(${rate.id}, ${rate.cost})">
            <label class="custom-control-label d-flex justify-content-between w-100" for="shipping-${rate.id}">
                <span>${rate.name_en} - ${rate.estimated_days_min}-${rate.estimated_days_max} days</span>
                <strong>$${parseFloat(rate.cost).toFixed(2)}</strong>
            </label>
        </div>
    `).join('');
    
    if (shippingRates.length > 0) {
        selectShipping(shippingRates[0].id, shippingRates[0].cost);
    }
}

function selectShipping(rateId, cost) {
    selectedShipping = { id: rateId, cost: cost };
    updateTotals();
}

function displayOrderSummary() {
    if (!cart || !cart.cart) return;
    
    const itemsContainer = document.getElementById('order-items');
    itemsContainer.innerHTML = cart.cart.items.map(item => `
        <div class="d-flex justify-content-between mb-2">
            <span>${item.product_model.name} × ${item.quantity}</span>
            <span>$${parseFloat(item.price_at_add * item.quantity).toFixed(2)}</span>
        </div>
    `).join('');
    
    document.getElementById('order-subtotal').textContent = `$${parseFloat(cart.total).toFixed(2)}`;
    updateTotals();
}

function updateTotals() {
    if (!cart || !selectedShipping) return;
    
    const subtotal = parseFloat(cart.total);
    const shipping = parseFloat(selectedShipping.cost);
    const tax = (subtotal + shipping) * 0.13;
    const total = subtotal + shipping + tax;
    
    document.getElementById('order-shipping').textContent = `$${shipping.toFixed(2)}`;
    document.getElementById('order-tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('order-total').textContent = `$${total.toFixed(2)}`;
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const token = UserManager.getToken();
    
    if (!token) {
        toastr.error('Session expired. Please login again.');
        window.location.href = '/login';
        return;
    }
    
    const sameAsShipping = document.getElementById('same_as_shipping').checked;
    
    document.getElementById('place-order-btn').disabled = true;
    document.getElementById('place-order-btn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    
    try {
        // Step 1: Save shipping address
        const shippingAddressData = {
            type: 'shipping',
            first_name: document.getElementById('shipping_first_name').value,
            last_name: document.getElementById('shipping_last_name').value,
            company: document.getElementById('shipping_company').value || null,
            address_line1: document.getElementById('shipping_address_line1').value,
            address_line2: document.getElementById('shipping_address_line2').value || null,
            city: document.getElementById('shipping_city').value,
            province: document.getElementById('shipping_province').value,
            postal_code: document.getElementById('shipping_postal_code').value,
            country: document.getElementById('shipping_country').value,
            phone: document.getElementById('shipping_phone').value
        };
        
        const shippingResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/addresses`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(shippingAddressData)
        });
        
        if (!shippingResponse.ok) {
            if (shippingResponse.status === 401) {
                toastr.error('Session expired. Please login again.');
                setTimeout(() => window.location.href = '/login', 1500);
                return;
            }
            const error = await shippingResponse.json();
            throw new Error(error.message || 'Failed to save shipping address');
        }
        
        const shippingData = await shippingResponse.json();
        const shippingAddressId = shippingData.address.id;
        
        // Step 2: Save billing address (or use shipping)
        let billingAddressId = shippingAddressId;
        
        if (!sameAsShipping) {
            const billingAddressData = {
                type: 'billing',
                first_name: document.getElementById('billing_first_name').value,
                last_name: document.getElementById('billing_last_name').value,
                company: document.getElementById('billing_company').value || null,
                address_line1: document.getElementById('billing_address_line1').value,
                address_line2: document.getElementById('billing_address_line2').value || null,
                city: document.getElementById('billing_city').value,
                province: document.getElementById('billing_province').value,
                postal_code: document.getElementById('billing_postal_code').value,
                country: document.getElementById('billing_country').value,
                phone: document.getElementById('billing_phone').value
            };
            
            const billingResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/addresses`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(billingAddressData)
            });
            
            if (!billingResponse.ok) {
                if (billingResponse.status === 401) {
                    toastr.error('Session expired. Please login again.');
                    setTimeout(() => window.location.href = '/login', 1500);
                    return;
                }
                const error = await billingResponse.json();
                throw new Error(error.message || 'Failed to save billing address');
            }
            
            const billingData = await billingResponse.json();
            billingAddressId = billingData.address.id;
        }
        
        // Step 3: Create order with address IDs
        const orderData = {
            shipping_address_id: shippingAddressId,
            billing_address_id: billingAddressId,
            shipping_method: document.querySelector('input[name="shipping_method"]:checked').value
        };
        
        const orderResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        if (!orderResponse.ok) {
            if (orderResponse.status === 401) {
                toastr.error('Session expired. Please login again.');
                setTimeout(() => window.location.href = '/login', 1500);
                return;
            }
            const orderResult = await orderResponse.json();
            throw new Error(orderResult.message || 'Failed to place order');
        }
        
        const orderResult = await orderResponse.json();
        console.log('Order created successfully:', orderResult);
        console.log('Payment mode:', orderResult.payment_mode);
        console.log('Redirecting to:', `/en/orders/${orderResult.order_number}`);
        console.log('Token exists:', !!UserManager.getToken());
        
        toastr.success('Order placed successfully! Your order is pending payment confirmation.');
        
        setTimeout(() => {
            window.location.href = `/en/orders/${orderResult.order_number}`;
        }, 500);
    } catch (error) {
        console.error('Error placing order:', error);
        toastr.error(error.message || 'Failed to place order');
        document.getElementById('place-order-btn').disabled = false;
        document.getElementById('place-order-btn').innerHTML = 'Place Order';
    }
}
