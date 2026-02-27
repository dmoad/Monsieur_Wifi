// Checkout page (French)
const LOCALE = 'fr';
let cart = null;
let shippingRates = [];
let selectedShipping = null;
let stripe = null;
let cardElement = null;
let currentOrderNumber = null;

document.addEventListener('DOMContentLoaded', function() {
    const token = UserManager.getToken();
    if (!token) {
        toastr.warning('Veuillez vous connecter pour passer à la caisse');
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
                toastr.warning('Votre panier est vide');
                window.location.href = '/fr/boutique';
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
                <span>${rate.name_fr} - ${rate.estimated_days_min}-${rate.estimated_days_max} jours</span>
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
        toastr.error('Session expirée. Veuillez vous reconnecter.');
        window.location.href = '/login';
        return;
    }
    
    const sameAsShipping = document.getElementById('same_as_shipping').checked;
    
    document.getElementById('place-order-btn').disabled = true;
    document.getElementById('place-order-btn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Traitement...';
    
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
                toastr.error('Session expirée. Veuillez vous reconnecter.');
                setTimeout(() => window.location.href = '/login', 1500);
                return;
            }
            const error = await shippingResponse.json();
            throw new Error(error.message || 'Échec de l\'enregistrement de l\'adresse de livraison');
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
                    toastr.error('Session expirée. Veuillez vous reconnecter.');
                    setTimeout(() => window.location.href = '/login', 1500);
                    return;
                }
                const error = await billingResponse.json();
                throw new Error(error.message || 'Échec de l\'enregistrement de l\'adresse de facturation');
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
                toastr.error('Session expirée. Veuillez vous reconnecter.');
                setTimeout(() => window.location.href = '/login', 1500);
                return;
            }
            const orderResult = await orderResponse.json();
            throw new Error(orderResult.message || 'Échec de la commande');
        }
        
        const orderResult = await orderResponse.json();
        console.log('Order created successfully:', orderResult);
        console.log('Payment mode:', orderResult.payment_mode);
        
        currentOrderNumber = orderResult.order_number;
        
        // Check payment mode
        if (orderResult.payment_mode === 'stripe') {
            // Initialize Stripe payment
            await initializeStripePayment(orderResult.order_number);
        } else {
            // Mock payment mode - redirect to order page
            toastr.success('Commande passée avec succès! Votre commande est en attente de confirmation de paiement.');
            setTimeout(() => {
                window.location.href = `/fr/commandes/${orderResult.order_number}`;
            }, 500);
        }
    } catch (error) {
        console.error('Error placing order:', error);
        toastr.error(error.message || 'Échec de la commande');
        document.getElementById('place-order-btn').disabled = false;
        document.getElementById('place-order-btn').innerHTML = 'Passer la Commande';
    }
}

async function initializeStripePayment(orderNumber) {
    try {
        const token = UserManager.getToken();
        
        // Fetch payment intent
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders/${orderNumber}/payment-intent`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Échec de l\'initialisation du paiement');
        }
        
        const data = await response.json();
        
        // Initialize Stripe
        stripe = Stripe(data.publishable_key);
        const elements = stripe.elements();
        cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#ea5455'
                }
            }
        });
        
        // Show payment modal
        document.getElementById('payment-order-number').textContent = orderNumber;
        document.getElementById('payment-total-amount').textContent = document.getElementById('order-total').textContent;
        document.getElementById('payment-modal').style.display = 'block';
        
        // Mount card element
        cardElement.mount('#card-element');
        
        // Handle card errors
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle payment form submission
        const paymentForm = document.getElementById('payment-form');
        paymentForm.onsubmit = async (e) => {
            e.preventDefault();
            await handlePaymentSubmit(data.client_secret);
        };
        
        // Re-enable place order button
        document.getElementById('place-order-btn').disabled = false;
        document.getElementById('place-order-btn').innerHTML = 'Passer la Commande';
        
    } catch (error) {
        console.error('Error initializing Stripe payment:', error);
        toastr.error('Échec de l\'initialisation du paiement. Veuillez réessayer.');
        document.getElementById('place-order-btn').disabled = false;
        document.getElementById('place-order-btn').innerHTML = 'Passer la Commande';
    }
}

async function handlePaymentSubmit(clientSecret) {
    const submitBtn = document.getElementById('submit-payment-btn');
    const paymentForm = document.getElementById('payment-form');
    const processingDiv = document.getElementById('payment-processing');
    
    // Show processing state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Traitement...';
    
    try {
        const {error, paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
            }
        });
        
        if (error) {
            // Show error
            document.getElementById('card-errors').textContent = error.message;
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Payer Maintenant';
            toastr.error(error.message);
        } else if (paymentIntent.status === 'succeeded') {
            // Payment successful - verify and confirm with backend
            paymentForm.style.display = 'none';
            processingDiv.style.display = 'block';
            
            toastr.success('Paiement réussi! Confirmation de votre commande...');
            
            // Verify payment with backend
            try {
                const token = UserManager.getToken();
                const verifyResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/orders/${currentOrderNumber}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const verifyResult = await verifyResponse.json();
                
                if (verifyResponse.ok && verifyResult.success) {
                    toastr.success('Commande confirmée! Redirection...');
                    setTimeout(() => {
                        window.location.href = `/fr/commandes/${currentOrderNumber}`;
                    }, 1000);
                } else {
                    console.error('Payment verification failed:', verifyResult);
                    toastr.warning('Paiement effectué mais confirmation en attente. Veuillez contacter le support si nécessaire.');
                    setTimeout(() => {
                        window.location.href = `/fr/commandes/${currentOrderNumber}`;
                    }, 2000);
                }
            } catch (verifyError) {
                console.error('Error verifying payment:', verifyError);
                toastr.warning('Paiement effectué. Redirection vers votre commande...');
                setTimeout(() => {
                    window.location.href = `/fr/commandes/${currentOrderNumber}`;
                }, 2000);
            }
        } else {
            // Handle other statuses
            toastr.warning('Le paiement est en cours de traitement. Veuillez vérifier le statut de votre commande.');
            setTimeout(() => {
                window.location.href = `/fr/commandes/${currentOrderNumber}`;
            }, 2000);
        }
    } catch (error) {
        console.error('Payment error:', error);
        toastr.error('Le paiement a échoué. Veuillez réessayer.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Payer Maintenant';
    }
}

function closePaymentModal() {
    if (confirm('Êtes-vous sûr de vouloir annuler le paiement? Votre commande restera en attente.')) {
        document.getElementById('payment-modal').style.display = 'none';
        if (currentOrderNumber) {
            window.location.href = `/fr/commandes/${currentOrderNumber}`;
        }
    }
}
