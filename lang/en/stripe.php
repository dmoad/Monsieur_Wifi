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
];
