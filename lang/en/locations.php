<?php

return [
    'page_title' => 'Locations - Monsieur WiFi',
    'heading' => 'Locations',

    // Header action + button states
    'add_location' => 'Add Location',
    'adding_location' => 'Adding Location...',
    'error_creating_location' => 'Error creating location',

    // Stats cards
    'total_locations' => 'Total Locations',
    'online_locations' => 'Online Locations',
    'total_users' => 'Total Users',
    'total_data_usage' => 'Total Data Usage',

    // List card + filter + search
    'locations_list' => 'Locations List',
    'all_status' => 'All Status',
    'status_online' => 'Online',
    'status_offline' => 'Offline',
    'search_placeholder' => 'Search locations...',

    // Table headers
    'col_location' => 'Location',
    'col_address' => 'Address',
    'col_users' => 'Users',
    'col_data_usage' => 'Data Usage',
    'col_status' => 'Status',
    'col_actions' => 'Actions',
    'action_view' => 'View',
    'action_delete' => 'Delete',
    'actions' => 'Actions',
    'primary_label' => 'Primary',
    'marker_icon_alt' => 'Marker Icon',

    // Card list states
    'items_per_page' => 'Items per page:',
    'empty_title' => 'No locations found',
    'empty_desc' => 'Add your first location to get started.',
    'error_loading' => 'Error loading locations',
    'confirm_delete' => 'Delete location "{name}"? This cannot be undone.',
    'location_deleted' => 'Location deleted successfully',
    'error_deleting' => 'Error deleting location',

    // Add-location modal
    'add_new_location' => 'Add New Location',
    'owner_label' => 'Owner',
    'loading_users' => 'Loading users...',
    'select_owner_first_option' => 'Select owner first...',
    'owner_help' => 'Select the owner for this location. Devices will load after selection.',
    'location_name_label' => 'Location Name',
    'location_name_placeholder' => 'Enter location name',
    'address_label' => 'Address',
    'address_placeholder' => 'Enter address',
    'select_device_label' => 'Select Device',
    'select_device_placeholder' => '— select a device —',
    'select_device_help' => 'Select an existing device to assign to this location.',
    'description_label' => 'Description',
    'description_placeholder' => 'Enter additional notes or description',

    // Dynamic device-select states (driven by JS)
    'select_owner_above_first' => 'Select an owner above first',
    'select_owner_first_hint' => 'Please select an owner first — devices will load automatically.',
    'loading_devices' => 'Loading devices...',
    'select_a_device' => 'Select a device...',
    'available_devices_group' => 'Available Devices',
    'available_suffix' => 'Available',
    'devices_assigned_elsewhere_group' => 'Devices Assigned to Other Locations',
    'assigned_to_prefix' => 'Assigned to:',
    'unknown_location' => 'Unknown Location',
    'no_devices_found' => 'No devices found',
    'error_loading_devices' => 'Error loading devices',

    // Validation + action feedback
    'location_name_required' => 'Location name is required',
    'device_required' => 'Device selection is required',
    'location_created' => 'Location created successfully',
    'assigned_firmware_prefix' => 'Assigned firmware:',

    // Units
    'unit_tb' => 'TB',
    'unit_gb' => 'GB',
];
