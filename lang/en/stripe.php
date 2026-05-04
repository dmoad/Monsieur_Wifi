<?php

return [
    // Stripe Checkout line items
    'deposit_name' => 'WiFi access point deposit',
    'deposit_desc' => 'Refundable deposit for the Monsieur WiFi access point',
    'shipping_name' => 'Shipping fee',
    'shipping_desc' => 'Delivery of the WiFi access point',

    // Custom text shown on Stripe Checkout. Placeholders:
    //   :deposit  formatted deposit amount in euros
    //   :shipping formatted shipping fee in euros
    //   :days     trial period length in days
    'submit_message' => 'Your subscription includes: a WiFi access point with pre-configured captive portal and setup assistance. Today you only pay the deposit (:deposit€) + shipping fee (:shipping€). Your subscription is free for :days days, then billed automatically.',

    // :url terms-and-conditions URL
    'terms_message' => 'I accept the [Terms and Conditions](:url)',

    // /subscription/cancel page (Stripe checkout-cancelled redirect)
    'cancel' => [
        'page_title' => 'Payment cancelled - Monsieur WiFi',
        'title' => 'Payment cancelled',
        'message' => 'Your payment has been cancelled. No amount has been charged to your account. You can try again whenever you want.',
        'retry' => 'Try again',
        'dashboard' => 'Back to dashboard',
    ],

    // /subscription/success page (Stripe checkout-completed redirect)
    'success' => [
        'page_title' => 'Payment successful - Monsieur WiFi',
        'title' => 'Payment successful!',
        'message' => 'Congratulations! Your subscription is now active. You can start using all the features of your plan.',
        'dashboard' => 'Go to dashboard',
    ],
];
