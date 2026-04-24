<?php

return [
    'page_title' => 'Network Settings - monsieur-wifi Controller',
    'heading' => 'Network Settings',

    // Breadcrumb + back button
    'breadcrumb_networks' => 'Networks',
    'back_to_location' => 'Back to Location',

    // Location info bar
    'vlan_support' => 'VLAN Support',
    'add_network' => 'Add Network',

    // Tab nav
    'loading_networks' => 'Loading networks…',
    'tab_label_default' => 'Network',

    // Per-network pane header
    'pane_title_default' => 'Network',
    'delete_network' => 'Delete Network',
    'save_settings' => 'Save Settings',

    // Network type select options (full names)
    'type_password_wifi' => 'Password WiFi',
    'type_captive_portal' => 'Captive Portal',
    'type_open_essid' => 'Open ESSID',

    // Network type pills (short labels next to icon)
    'pill_password' => 'Password',
    'pill_captive_portal' => 'Captive Portal',
    'pill_open' => 'Open',

    // SSID + visibility
    'ssid_placeholder' => 'Network name (SSID)',
    'visibility_broadcast' => 'Broadcast SSID',
    'visibility_hidden' => 'Hidden SSID',

    // QoS + radio band
    'full_qos' => 'Full QoS',
    'band_all' => '2.4 & 5 GHz',
    'band_2_4_only' => '2.4 GHz only',
    'band_5_only' => '5 GHz only',

    // Security & Encryption panel (password networks)
    'panel_security_encryption' => 'Security & Encryption',
    'wifi_password' => 'WiFi Password',
    'wifi_password_placeholder' => 'Minimum 8 characters',
    'security_protocol' => 'Security Protocol',
    'security_wpa2_psk_rec' => 'WPA2-PSK (Recommended)',
    'security_wpa_wpa2_mixed' => 'WPA/WPA2-PSK Mixed',
    'security_wpa3_psk_secure' => 'WPA3-PSK (Most Secure)',
    'security_wep_legacy' => 'WEP (Legacy)',
    'cipher_suites' => 'Cipher Suites',

    // Captive Portal Configuration panel
    'panel_captive_portal_config' => 'Captive Portal Configuration',
    'sub_authentication' => 'Authentication',
    'login_methods' => 'Login Methods',
    'login_methods_hint' => '(select one or more)',
    'method_click_through' => 'Click-through',
    'method_password' => 'Password',
    'method_sms' => 'SMS',
    'method_email' => 'Email',
    'method_social' => 'Social',
    'multiple_methods_hint' => 'When multiple methods are selected, guests will choose at login.',
    'shared_password' => 'Shared Password',
    'social_provider' => 'Social Provider',
    'portal_design' => 'Portal Design',
    'default_design' => 'Default Design',
    'redirect_url' => 'Redirect URL',
    'redirect_url_placeholder' => 'https://example.com',
    'session_timeout' => 'Session Timeout',
    'idle_timeout' => 'Idle Timeout',

    // Timeout / duration options
    'dur_15_min' => '15 Minutes',
    'dur_30_min' => '30 Minutes',
    'dur_45_min' => '45 Minutes',
    'dur_1_hour' => '1 Hour',
    'dur_2_hours' => '2 Hours',
    'dur_3_hours' => '3 Hours',
    'dur_4_hours' => '4 Hours',
    'dur_5_hours' => '5 Hours',
    'dur_6_hours' => '6 Hours',
    'dur_12_hours' => '12 Hours',
    'dur_1_day' => '1 Day',
    'dur_1_week' => '1 Week',
    'dur_3_months' => '3 Months',
    'dur_1_year' => '1 Year',

    // Bandwidth
    'sub_bandwidth_limits' => 'Bandwidth Limits',
    'download_mbps' => 'Download (Mbps)',
    'upload_mbps' => 'Upload (Mbps)',
    'unlimited' => 'Unlimited',

    // Working hours + Open panel
    'working_hours' => 'Working Hours',
    'panel_open_network' => 'Open Network',
    'no_auth_required' => 'No authentication required',
    'open_network_warning' => 'Anyone within range can connect without a password or portal. Use only in trusted environments.',

    // IP & DHCP Settings collapsible
    'section_ip_dhcp' => 'IP & DHCP Settings',
    'panel_ip_config' => 'IP Configuration',
    'sub_addressing' => 'Addressing',
    'ip_mode' => 'IP Mode',
    'ip_mode_static' => 'Static IP',
    'ip_mode_bridge_lan' => 'Bridge to LAN Port',
    'ip_mode_bridge' => 'Bridge to WAN',
    'lan_dhcp_mode' => 'LAN DHCP Mode',
    'lan_dhcp_client' => 'DHCP Client',
    'lan_dhcp_server' => 'DHCP Server',
    'lan_dhcp_client_not_captive' => 'DHCP Client is not available for Captive Portal networks.',
    'ip_address' => 'IP Address',
    'ip_address_placeholder' => '192.168.x.1',
    'netmask' => 'Netmask',
    'gateway' => 'Gateway',
    'gateway_placeholder' => 'Auto',
    'primary_dns' => 'Primary DNS',
    'alt_dns' => 'Alt DNS',
    'dns_field_title' => 'DNS is managed by the web filter. Disable the web filter to set per-network DNS.',

    // DHCP address pool
    'dhcp_pool_title' => 'DHCP address pool',
    'dhcp_pool_desc' => 'Assign LAN IPs to client devices on this network.',
    'dhcp_server_label' => 'DHCP server',
    'enable_dhcp' => 'Enable DHCP',
    'start_ip' => 'Start IP',
    'start_ip_placeholder' => 'e.g. 192.168.1.100',
    'start_ip_hint' => 'First address in the pool (IPv4).',
    'pool_size' => 'Pool size',
    'pool_size_placeholder' => 'e.g. 101',
    'pool_size_hint' => 'Number of addresses (must fit within your subnet).',

    // VLAN
    'sub_vlan' => 'VLAN',
    'vlan_id' => 'VLAN ID',
    'vlan_id_range' => '(1–4094)',
    'vlan_none' => 'None',
    'tagging' => 'Tagging',
    'tagging_disabled' => 'Disabled',
    'tagging_tagged' => 'Tagged',
    'tagging_untagged' => 'Untagged',

    // MAC Filtering & IP Reservations
    'section_mac_filter_reservations' => 'MAC Filtering & IP Reservations',
    'mac_filtering' => 'MAC Address Filtering',
    'mac_add_type_block' => 'Block',
    'mac_add_type_bypass' => 'Bypass Auth',
    'table_col_type' => 'Type',
    'table_col_mac' => 'MAC Address',
    'mac_list_empty' => 'No MAC rules added',
    'dhcp_reservations' => 'DHCP IP Reservations',
    'reservation_mac_placeholder' => 'MAC  00:11:22:33:44:55',
    'reservation_ip_placeholder' => 'IP  192.168.1.50',
    'table_col_reserved_ip' => 'Reserved IP',
    'reservation_list_empty' => 'No reservations added',
];
