<?php

return [
    'page_title' => 'My Orders - Monsieur WiFi',
    'heading' => 'My Orders',
    'breadcrumb' => 'My Orders',
    'btn_continue_shopping' => 'Continue Shopping',

    'empty_title' => 'No orders yet',
    'empty_subtitle' => 'Start shopping to see your orders here!',
    'btn_shop_now' => 'Shop Now',

    // JS-only strings (consumed via window.APP_I18N.orders)
    'js_toast_login_required' => 'Please login to view your orders',
    'js_toast_load_failed' => 'Failed to load orders',
    'js_order_number_prefix' => 'Order #',
    'js_label_ordered' => 'Ordered: {date}',
    'js_label_delivered_on' => 'Delivered: {date}',
    'js_label_status' => 'Status',
    'js_label_total' => 'Total',
    'js_label_tax' => '(Tax: €{amount})',
    'js_btn_view_details' => 'View Details',
    'js_btn_invoice' => 'Invoice',
    'js_tracking_html' => 'Tracking: <strong>{provider}</strong> - {id}',
    'js_status_cancelled' => 'Cancelled',
    'js_status_awaiting_payment' => 'Awaiting payment',
    'js_status_delivered' => 'Delivered',
    'js_status_shipped' => 'Shipped',
    'js_status_paid' => 'Payment received',
    'js_toast_session_expired' => 'Session expired. Please login again.',
    'js_toast_invoice_failed' => 'Failed to download invoice',
    'js_toast_invoice_downloaded' => 'Invoice downloaded successfully',
    'js_invoice_filename' => 'invoice-{order}.pdf',
];
