<?php

return [
    'page_title' => 'Locations',
    'heading' => 'Locations',
    'preview_banner' => 'A zone groups multiple locations for seamless Wi-Fi roaming: one primary location holds the configuration, the others inherit it automatically.',

    // Tabs
    'tab_aps' => 'Locations',
    'tab_zones' => 'Zones',

    // Search / filters
    'search_aps' => 'Search by name, address, MAC…',
    'search_zones' => 'Search zones…',

    // AP table columns
    'col_name' => 'Name',
    'col_status' => 'Status',
    'col_address' => 'Address',
    'col_zone' => 'Zone',
    'col_last_seen' => 'Last seen',
    'col_actions' => 'Actions',

    // Zone table columns
    'col_zone_name' => 'Zone name',
    'col_ap_count' => 'Locations',
    'col_primary_ap' => 'Primary location',
    'col_owner' => 'Owner',

    // Cell values
    'standalone' => 'No zone',
    'no_address' => '—',
    'no_primary' => '—',
    'no_owner' => '—',
    'never_seen' => 'Never',
    'status_online' => 'Online',
    'status_offline' => 'Offline',

    // Empty states
    'no_aps' => 'No locations yet.',
    'no_zones' => 'No zones yet. Locations are running standalone.',
    'no_aps_match' => 'No locations match your search.',
    'no_zones_match' => 'No zones match your search.',
    'all_in_zones' => 'All your locations are inside zones. Switch to the Zones tab to see them.',

    // Actions
    'action_open' => 'Open',
    'action_open_zone' => 'View zone',

    // Grouped view
    'primary' => 'Primary location — others in this zone share its config',
    'primary_pill' => 'Primary',
    'open_zone' => 'Open zone',
    'ap_singular' => 'location',
    'ap_plural' => 'locations',

    // Header action buttons
    'add_ap' => 'Add location',
    'create_zone' => 'Create zone',

    // Filter chips
    'filter_all' => 'All',
    'filter_online' => 'Online',
    'filter_offline' => 'Offline',

    // Summary + rollup
    'zone_singular' => 'zone',
    'zone_plural' => 'zones',
    'meta_online' => 'online',
    'meta_offline' => 'offline',

    // Summary cards (top of page)
    'metric_total_aps' => 'Locations',
    'metric_online_aps' => 'Online',
    'metric_total_users' => 'Connected Users',
    'metric_total_data' => 'Data Usage',

    // New columns
    'col_users' => 'Users',
    'col_data' => 'Data',

    // Row actions
    'action_clone' => 'Clone',
    'action_delete' => 'Delete',
    'action_edit' => 'Edit',
    'confirm_delete_ap' => 'Delete location "{name}"? This cannot be undone.',
    'confirm_delete_zone' => 'Delete this zone? Its locations will be ungrouped.',
    'ap_deleted' => 'Location deleted',
    'ap_cloned' => 'Location cloned',
    'zone_deleted' => 'Zone deleted',
    'zone_created' => 'Zone created',
    'create_zone_prompt' => 'Name for the new zone:',
    'action_failed' => 'Action failed — please try again',
];
