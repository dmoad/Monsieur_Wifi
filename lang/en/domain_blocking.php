<?php

return [
    'page_title' => 'Domain Blocking - Monsieur WiFi',
    'heading' => 'Domain Blocking',

    // Header button
    'info_btn' => 'Info',

    // Blocking categories card
    'categories_title' => 'Blocking Categories',
    'categories_help' => 'Toggle categories to enable or disable domain blocking by category.',
    'cat_adult' => 'Adult Content',
    'cat_gambling' => 'Gambling',
    'cat_malware' => 'Malware',
    'cat_social' => 'Social Media',
    'cat_streaming' => 'Streaming',
    'cat_custom' => 'Custom List',
    'domains_suffix' => 'domains',

    // Table
    'blocked_domains_title' => 'Blocked Domains',
    'add_domain' => 'Add Domain',
    'col_domain' => 'Domain',
    'col_category' => 'Category',
    'col_added_date' => 'Added Date',
    'col_last_updated' => 'Last Updated',
    'col_actions' => 'Actions',

    // Add modal
    'add_modal_title' => 'Add New Domain',
    'domain_label' => 'Domain',
    'domain_placeholder' => 'example.com',
    'domain_help' => 'Enter a domain without http:// or https://',
    'category_label' => 'Category',
    'notes_label' => 'Notes',
    'notes_placeholder' => 'Enter any notes',
    'add_btn' => 'Add Domain',

    // Edit modal
    'edit_modal_title' => 'Edit Domain',
    'block_all_subdomains' => 'Block all subdomains',
    'block_subdomains_help' => 'All subdomains will be blocked automatically if the domain is blocked.',
    'save_changes' => 'Save Changes',

    // Info modal
    'info_modal_title' => 'How Domain Blocking Works',
    'what_is_title' => 'What is Domain Blocking?',
    'what_is_body' => 'Domain blocking prevents users on your network from accessing specific websites by blocking their domain names. When a user tries to visit a blocked domain, the request is intercepted and denied, protecting your network from unwanted content, security threats, or productivity distractions.',
    'how_to_add_title' => 'How to Add Domains',
    'how_to_single' => 'Single Domain:',
    'how_to_single_body' => 'Click "Add Domain" button to add individual websites',
    'how_to_categories' => 'Categories:',
    'how_to_categories_body' => 'Organize domains into predefined categories for better management',
    'why_multiple_title' => 'Why Multiple Domains Are Needed',
    'why_multiple_body' => 'Many websites use multiple domains to deliver content, avoid blocking, or improve performance. To effectively block a service, you often need to block several related domains:',
    'service_col' => 'Service',
    'domains_to_block_col' => 'Domains to Block',
    'best_practices_title' => 'Best Practices',
    'bp_use_cats' => 'Use Categories:',
    'bp_use_cats_body' => 'Group related domains for easier management',
    'bp_research' => 'Research Thoroughly:',
    'bp_research_body' => 'Look up all domains used by a service before blocking',
    'bp_test' => 'Test Blocking:',
    'bp_test_body' => 'Verify that the blocking works as expected',
    'bp_updates' => 'Regular Updates:',
    'bp_updates_body' => 'Keep your block lists updated as services change domains',
    'pro_tip' => 'Pro Tip:',
    'pro_tip_body' => 'Use browser developer tools (F12) to inspect network requests and identify all domains used by a website. This helps ensure comprehensive blocking.',
    'got_it' => 'Got It!',
];
