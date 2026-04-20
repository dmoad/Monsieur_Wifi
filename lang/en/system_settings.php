<?php

return [
    'page_title' => 'Global Settings - Monsieur WiFi',
    'heading' => 'Global Settings',
    'breadcrumb' => 'Settings',

    // Tabs
    'tab_captive_portal' => 'Captive Portal',
    'tab_radius' => 'RADIUS Configuration',
    'tab_branding' => 'Branding',
    'tab_system' => 'System',

    // Buttons
    'save_changes' => 'Save Changes',

    // Captive Portal tab
    'section_default_wifi' => 'Default WiFi Settings',
    'label_default_essid' => 'Default ESSID',
    'help_default_essid' => 'This ESSID will be used as default for all new access points',
    'label_default_guest_essid' => 'Default Guest ESSID',
    'help_default_guest_essid' => 'This ESSID will be used as default for guest networks',
    'label_default_password' => 'Default Password',
    'help_default_password' => 'Default password for new access points (minimum 8 characters)',

    'section_portal_behavior' => 'Captive Portal Behavior',
    'label_portal_timeout' => 'Default Session Timeout',
    'unit_hours' => 'Hours',
    'help_portal_timeout' => 'How long users stay authenticated before needing to log in again',
    'label_idle_timeout' => 'Default Idle Timeout',
    'unit_minutes' => 'Minutes',
    'help_idle_timeout' => 'Disconnect inactive users after this period',
    'label_bandwidth_limit' => 'Default Bandwidth Limit',
    'unit_mbps' => 'Mbps',
    'help_bandwidth_limit' => 'Default bandwidth limit per user',
    'label_user_limit' => 'Default Maximum Users',
    'help_user_limit' => 'Maximum concurrent users per access point',
    'label_enable_terms' => 'Display Terms & Conditions',
    'help_enable_terms' => 'Require acceptance of Terms & Conditions before connecting',

    // RADIUS tab
    'section_primary_radius' => 'Primary RADIUS Server',
    'label_radius_ip' => 'Server IP Address',
    'help_radius_ip' => 'IP address of your primary RADIUS server',
    'label_radius_port' => 'Authentication Port',
    'help_radius_port' => 'Port used for RADIUS authentication (default: 1812)',
    'label_radius_secret' => 'Shared Secret',
    'help_radius_secret' => 'Shared secret for RADIUS authentication',
    'label_accounting_port' => 'Accounting Port',
    'help_accounting_port' => 'Port used for RADIUS accounting (default: 1813)',

    // Branding tab
    'section_company_info' => 'Company Information',
    'label_company_name' => 'Company Name',
    'help_company_name' => 'Your company name as displayed on the captive portal',
    'label_company_website' => 'Company Website',
    'help_company_website' => 'Your company website URL',
    'label_contact_email' => 'Contact Email',
    'help_contact_email' => 'Contact email displayed on the captive portal',
    'label_support_phone' => 'Support Phone',
    'help_support_phone' => 'Support phone number displayed on the captive portal',

    'section_logo_images' => 'Logo & Images',
    'label_logo' => 'Company Logo',
    'choose_file' => 'Choose file',
    'help_logo' => 'Recommended size: 300px x 100px (PNG or SVG with transparency)',
    'label_current_logo' => 'Current Logo',
    'alt_current_logo' => 'Current logo',
    'label_favicon' => 'Favicon',
    'help_favicon' => 'Recommended size: 32px x 32px (ICO, PNG, or GIF)',
    'label_current_favicon' => 'Current Favicon',
    'alt_current_favicon' => 'Current favicon',
    'label_splash_background' => 'Captive Portal Background',
    'help_splash_background' => 'Recommended size: 1920px x 1080px (JPG or PNG)',

    'section_portal_customization' => 'Portal Customization',
    'label_primary_color' => 'Primary Color',
    'help_primary_color' => 'Main color for buttons and highlights',
    'label_secondary_color' => 'Secondary Color',
    'help_secondary_color' => 'Secondary color for accents and alternate elements',
    'label_font_family' => 'Primary Font',
    'help_font_family' => 'Font family used throughout the portal',
    'label_portal_theme' => 'Portal Theme',
    'theme_light' => 'Light',
    'theme_dark' => 'Dark',
    'theme_auto' => 'Auto (system preference)',
    'help_portal_theme' => 'Default theme for the captive portal',

    // System tab
    'section_email_config' => 'Email Configuration',
    'label_smtp_server' => 'SMTP Server',
    'help_smtp_server' => 'SMTP server for sending email notifications',
    'label_smtp_port' => 'SMTP Port',
    'help_smtp_port' => 'Port for SMTP server connection',
    'label_sender_email' => 'Sender Email',
    'help_sender_email' => 'Email address that notifications come from',
    'label_smtp_password' => 'SMTP Password',
    'help_smtp_password' => 'Password for authenticating with SMTP server',
    'send_test_email' => 'Send Test Email',

    // JS: button busy states
    'saving' => 'Saving...',
    'sending' => 'Sending...',

    // JS: toasts
    'toast_saved_title' => 'Settings Saved',
    'toast_saved_body' => 'Your settings have been saved successfully.',
    'toast_save_failed' => 'Failed to save settings.',
    'toast_error_title' => 'Error',
    'toast_test_email_title' => 'Email Sent',
    'toast_test_email_body' => 'Test email has been sent to {email}',
    'toast_test_email_failed' => 'Failed to send test email. Please check your SMTP settings.',
    'toast_load_failed' => 'Failed to load settings. Please try again.',
];
