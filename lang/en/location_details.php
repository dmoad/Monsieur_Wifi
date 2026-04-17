<?php

return [
    'page_title' => 'Location Details - monsieur-wifi Controller',
    'heading' => 'Location Details',

    // Header buttons
    'clone_button' => 'Clone',
    'networks_button' => 'Networks',

    // Router info card
    'mac_prefix' => 'MAC:',
    'edit_button' => 'Edit',
    'router_model' => 'Router Model',
    'mac_address' => 'MAC Address',
    'firmware' => 'Firmware',
    'total_users' => 'Total Users',
    'daily_usage' => 'Daily Usage',
    'uptime' => 'Uptime',
    'restart_button' => 'Restart',
    'update_button' => 'Update',

    // Current usage card
    'current_usage' => 'Current Usage',
    'period_today' => 'Today',
    'period_7days' => 'Last 7 Days',
    'period_30days' => 'Last 30 Days',
    'loading_usage_data' => 'Loading usage data...',
    'stat_download' => 'Download',
    'stat_upload' => 'Upload',
    'stat_users_sessions' => 'Users / Sessions',
    'stat_avg_session' => 'Avg. Session',
    'loading_data' => 'Loading data...',

    // Map card
    'location_map_title' => 'Location',

    // Analytics section
    'analytics_title' => 'Analytics',
    'daily_usage_analytics' => 'Daily Usage Analytics',
    'captive_portal_activity' => 'Captive Portal User Activity',
    'stat_sessions' => 'Sessions',
    'stat_daily_avg' => 'Daily Avg',
    'live_users' => 'Live Users',
    'currently_connected' => 'Currently Connected',
    'online_label' => 'Online',
    'loading_online_users' => 'Loading online users...',

    // WiFi networks shortcut
    'wifi_networks' => 'WiFi Networks',
    'wifi_networks_description' => "Manage all WiFi networks associated with this location — add, remove, or configure each network's security, captive portal, IP settings, and more.",
    'zone_networks_notice' => "Networks are managed by the zone's primary location.",
    'manage_networks_button' => 'Manage Networks',

    // Location Configuration card
    'config_title' => 'Location Configuration',
    'tab_location_details' => 'Location Details',
    'tab_router_settings' => 'Router Settings',

    // Location Details tab - Identity & Address panel
    'panel_identity_address' => 'Location Identity & Address',
    'sublabel_identity' => 'Identity',
    'location_name' => 'Location Name',
    'location_name_placeholder' => 'e.g. Downtown Café',
    'status_label' => 'Status',
    'sublabel_address' => 'Address',
    'street_address' => 'Street Address',
    'street_placeholder' => '123 Main St',
    'city' => 'City',
    'city_placeholder' => 'City',
    'state_province' => 'State / Province',
    'state_placeholder' => 'State',
    'postal' => 'Postal',
    'postal_placeholder' => 'Code',
    'country' => 'Country',
    'country_placeholder' => 'Country',
    'sublabel_notes' => 'Notes',
    'description_label' => 'Description',
    'description_optional' => '(optional)',
    'description_placeholder' => 'Brief description of this location…',
    'char_counter_suffix' => '/500 characters',

    // Location Details tab - Contact & Ownership panel
    'panel_contact_ownership' => 'Contact & Ownership',
    'manager_name' => 'Manager Name',
    'manager_name_placeholder' => 'Full name',
    'email' => 'Email',
    'email_placeholder' => 'contact@example.com',
    'phone' => 'Phone',
    'phone_placeholder' => '+1 555 000 0000',
    'owner' => 'Owner',
    'admin_badge' => 'Admin',
    'select_owner_option' => 'Select Owner',
    'shared_access' => 'Shared Access',
    'shared_access_help' => "Search and select users who will have full access to this location's settings.",

    // Action bar
    'save_location_info' => 'Save Location Information',

    // Router Settings tab - WAN Connection
    'wan_connection' => 'WAN Connection',
    'edit_wan_settings' => 'Edit WAN Settings',
    'connection_type' => 'Connection Type',
    'ip_address' => 'IP Address',
    'subnet_mask' => 'Subnet Mask',
    'gateway' => 'Gateway',
    'primary_dns' => 'Primary DNS',
    'username' => 'Username',
    'service_name' => 'Service Name',

    // Router Settings tab - Radio & Channel
    'wifi_radio_channel' => 'WiFi Radio & Channel Configuration',
    'country_region' => 'Country/Region',
    'power_2g' => '2.4 GHz Power',
    'power_5g' => '5 GHz Power',
    'width_2g' => '2.4 GHz Channel Width',
    'width_5g' => '5 GHz Channel Width',
    'channel_2g' => '2.4 GHz Channel',
    'channel_5g' => '5 GHz Channel',
    'channel_optimization' => 'Channel Optimization',
    'scan_button' => 'Scan',
    'scan_default_status' => 'Click Scan to analyze optimal channels.',
    'best_2g' => 'Best 2.4G',
    'best_5g' => 'Best 5G',
    'no_scan_yet' => 'No scan performed yet',
    'apply_optimal' => 'Apply Optimal',
    'save_all_radio' => 'Save All Radio Settings',

    // Router Settings tab - Traffic Prioritization (QoS)
    'qos_title' => 'Traffic Prioritization (QoS)',
    'save_qos' => 'Save QoS',
    'qos_zone_notice' => "QoS is managed by the zone's primary location.",
    'qos_classification' => 'Classification',
    'qos_enable' => 'Enable traffic prioritization',
    'qos_enable_help' => 'Classify traffic by hostname (SNI) and apply DSCP-based priority. Requires compatible router firmware.',
    'qos_active_classes' => 'Active priority classes',
    'qos_managed_globally' => 'Managed globally by SuperAdmin.',
    'qos_bandwidth_limits' => 'Bandwidth limits',
    'qos_bandwidth_intro' => 'All values in <strong>Mbps</strong>. Set WAN capacity and optional reserved minimums per traffic class.',
    'qos_wan_use_local' => "Use this location's WAN speeds (instead of the zone default)",
    'qos_wan_use_local_help' => 'Class minimums follow the zone primary; only download/upload can differ here.',
    'qos_wan_capacity' => 'WAN capacity',
    'qos_min_per_class' => 'Minimum per class',
    'qos_voip' => 'VoIP',
    'qos_streaming' => 'Streaming',
    'qos_best_effort' => 'Best effort',
    'qos_bulk' => 'Bulk',

    // Router Settings tab - Web Content Filtering
    'web_content_filtering' => 'Web Content Filtering',
    'save_web_filter' => 'Save Web Filter Settings',
    'enable_content_filtering' => 'Enable Content Filtering',
    'web_filter_help' => 'Apply content filtering to all WiFi networks.',
    'web_filter_propagation' => '<strong>Please note:</strong> After saving, it takes <strong>2–5 minutes</strong> for domain blocking to go live on the router.',
    'block_categories' => 'Block Categories',
    'block_categories_help' => 'Select content categories to block across all networks.',
    'wan_primary_dns' => 'WAN Primary DNS',
    'wan_secondary_dns' => 'WAN Secondary DNS',
    'wan_dns_hint' => 'Used as DNS server upstream when web filter is active. Leave empty to fall back to 8.8.8.8 / 8.8.4.4.',
];
