<?php

return [
    'page_title' => 'Checkout - Monsieur WiFi',
    'heading' => 'Checkout',
    'breadcrumb' => 'Checkout',

    // Shipping info section
    'section_shipping_info' => 'Shipping Information',
    'label_first_name' => 'First Name *',
    'label_last_name' => 'Last Name *',
    'label_company' => 'Company',
    'label_address_1' => 'Address Line 1 *',
    'label_address_2' => 'Address Line 2',
    'label_city' => 'City *',
    'label_province' => 'Province *',
    'label_postal_code' => 'Postal Code *',
    'label_country' => 'Country *',
    'label_phone' => 'Phone *',

    // Billing
    'checkbox_same_as_shipping' => 'Billing address same as shipping',
    'section_billing_info' => 'Billing Information',

    // Shipping method
    'section_shipping_method' => 'Shipping Method',

    // Order summary
    'section_order_summary' => 'Order Summary',
    'subtotal_label' => 'Subtotal:',
    'shipping_label' => 'Shipping:',
    'tax_label' => 'Tax:',
    'total_label' => 'Total:',
    'btn_place_order' => 'Place Order',

    // Payment modal
    'modal_complete_payment' => 'Complete Payment',
    'order_number_label' => 'Order Number:',
    'total_amount_label' => 'Total Amount:',
    'label_card' => 'Credit or Debit Card',
    'btn_pay_now' => 'Pay Now',
    'processing_payment' => 'Processing your payment...',
    'processing_payment_subtitle' => 'Please do not close this window',

    // JS-only strings (consumed via window.APP_I18N.checkout)
    'js_toast_login_required' => 'Please login to checkout',
    'js_toast_cart_empty' => 'Your cart is empty',
    'js_shipping_days_suffix' => 'days',
    'js_toast_session_expired' => 'Session expired. Please login again.',
    'js_processing' => 'Processing...',
    'js_error_save_shipping' => 'Failed to save shipping address',
    'js_error_save_billing' => 'Failed to save billing address',
    'js_error_place_order' => 'Failed to place order',
    'js_toast_order_success' => 'Order placed successfully! Your order is pending payment confirmation.',
    'js_error_init_payment' => 'Failed to initialize payment',
    'js_toast_init_payment_failed' => 'Failed to initialize payment. Please try again.',
    'js_toast_payment_success' => 'Payment successful! Confirming your order...',
    'js_toast_order_confirmed' => 'Order confirmed! Redirecting...',
    'js_toast_payment_confirmation_pending' => 'Payment processed but confirmation pending. Please contact support if needed.',
    'js_toast_payment_processed' => 'Payment processed. Redirecting to your order...',
    'js_toast_payment_processing' => 'Payment is being processed. Please check your order status.',
    'js_toast_payment_failed' => 'Payment failed. Please try again.',
    'js_confirm_cancel_payment' => 'Are you sure you want to cancel the payment? Your order will remain pending.',
];
