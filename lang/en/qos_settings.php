<?php

return [
    'page_title' => 'QoS Settings - Monsieur WiFi',
    'heading' => 'Traffic Prioritization (QoS)',
    'breadcrumb' => 'QoS Settings',

    // Info banner
    'info_title' => 'How QoS works:',
    'info_body_html' => 'Traffic is classified by SNI (hostname) on the router and tagged with a DSCP priority. The four classes below are fixed — only their domain lists can be edited here. Per-location enable/disable is configured in the Location Settings page. Unmatched traffic automatically falls into the <strong>Default (BE)</strong> class.',

    // Loading / empty states
    'loading_classes' => 'Loading QoS classes…',
    'load_failed' => 'Failed to load QoS classes.',
    'be_placeholder' => 'No domain rules — all unmatched traffic falls into this class automatically.',
    'no_domains' => 'No domains configured yet.',

    // Add-domain form
    'add_domain_placeholder' => 'e.g. *.example.com',
    'add_btn' => 'Add',
    'remove_title' => 'Remove',

    // Class labels (shown in card header)
    'class_label_EF' => 'Real-Time',
    'class_label_AF41' => 'Streaming',
    'class_label_BE' => 'Default',
    'class_label_CS1' => 'Background',

    // Class priority descriptions
    'priority_desc_EF' => 'Highest priority — lowest latency guaranteed',
    'priority_desc_AF41' => 'High priority — less than Real-Time',
    'priority_desc_BE' => 'Normal priority — unmatched traffic & QoS-disabled locations',
    'priority_desc_CS1' => 'Lowest priority — deferred when congested',

    // Toasts / confirms
    'generic_error' => 'An error occurred.',
    'domain_empty' => 'Domain cannot be empty.',
    'domain_added' => 'Domain added to {class}.',
    'domain_removed' => 'Domain removed from {class}.',
    'confirm_remove' => 'Remove "{domain}" from {class}?',
];
