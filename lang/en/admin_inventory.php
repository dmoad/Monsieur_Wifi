<?php

return [
    'page_title' => 'Manage Inventory - Monsieur WiFi',
    'heading' => 'Manage Inventory',
    'breadcrumb' => 'Manage Inventory',

    'btn_manage_models' => 'Manage Models',

    'summary_total_products' => 'Total Products',
    'summary_out_of_stock' => 'Out of Stock',
    'summary_low_stock' => 'Low Stock',
    'summary_total_value' => 'Total Value',

    'section_heading' => 'Inventory Management',
    'info_hint' => 'Click <strong>"Add/View Devices"</strong> button to add inventory items. MAC addresses are automatically normalized to UPPERCASE with "-" delimiter.',

    'stock_filter_all' => 'All Stock Status',
    'stock_in_stock' => 'In Stock',
    'stock_low' => 'Low Stock',
    'stock_out' => 'Out of Stock',

    'search_placeholder' => 'Search products...',
    'btn_apply_filter' => 'Apply Filter',

    'modal_update_title' => 'Update Inventory',

    // JS-only strings (consumed via window.APP_I18N.admin_inventory)
    'js_session_expired' => 'Session expired. Please login again.',
    'js_no_permission' => 'You do not have permission to access this page.',
    'js_load_summary_failed' => 'Failed to load inventory summary',
    'js_load_inventory_failed' => 'Failed to load inventory: {message}',
    'js_load_devices_failed' => 'Failed to load devices',
    'js_load_devices_failed_prefix' => 'Failed to load devices: {message}',

    'js_no_products' => 'No products found',
    'js_no_devices' => 'No devices found',

    'js_label_in_stock' => 'In Stock:',
    'js_label_reserved' => 'Reserved:',
    'js_label_available' => 'Available:',
    'js_label_threshold' => 'Threshold:',

    'js_btn_add_view_devices' => 'Add/View Devices',
    'js_btn_add_view_devices_title' => 'View/Add Individual Devices',

    'js_badge_out_of_stock' => 'Out of Stock',
    'js_badge_low_stock' => 'Low Stock',
    'js_badge_in_stock' => 'In Stock',

    'js_device_based_tracking' => 'Device-Based Tracking',
    'js_device_based_tracking_desc' => 'Inventory quantity is automatically calculated based on individual devices you add with MAC addresses and serial numbers.',
    'js_label_devices_in_stock' => 'Devices in Stock',
    'js_label_low_stock_threshold' => 'Low Stock Threshold',
    'js_threshold_hint' => 'You will be alerted when available device count is at or below this threshold.',
    'js_modify_stock_heading' => 'To modify stock quantity:',
    'js_btn_add_manage_devices' => 'Add/Manage Individual Devices',
    'js_btn_save_threshold' => 'Save Threshold',

    'js_threshold_updated' => 'Stock threshold updated successfully',
    'js_threshold_update_failed' => 'Failed to update threshold',
    'js_save_failed_prefix' => 'Failed to save: {message}',

    'js_pagination_page' => 'Page',
    'js_pagination_of' => 'of',
    'js_pagination_devices' => 'devices',
    'js_btn_previous' => 'Previous',
    'js_btn_next' => 'Next',

    'js_devices_modal_desc' => 'Manage individual devices with their MAC addresses and serial numbers',
    'js_btn_add_device' => 'Add Device',
    'js_btn_import_csv' => 'Import CSV',

    'js_col_mac_address' => 'MAC Address',
    'js_col_serial_number' => 'Serial Number',
    'js_col_status' => 'Status',
    'js_col_notes' => 'Notes',
    'js_col_actions' => 'Actions',

    'js_btn_close' => 'Close',
    'js_btn_edit' => 'Edit',
    'js_btn_delete' => 'Delete',

    'js_device_status_available' => 'Available',
    'js_device_status_reserved' => 'Reserved',
    'js_device_status_sold' => 'Sold',
    'js_device_status_defective' => 'Defective',

    'js_form_add_heading' => 'Add New Device',
    'js_form_edit_heading' => 'Edit Device',
    'js_mac_formats_hint' => 'Accepted formats: 00-11-22-33-44-55 or 00:11:22:33:44:55 (auto-normalized)',
    'js_notes_placeholder' => 'Optional notes about this device',
    'js_form_received_date' => 'Received Date',
    'js_btn_add_device_submit' => 'Add Device',
    'js_btn_update_device' => 'Update Device',

    'js_mac_serial_required' => 'MAC Address and Serial Number are required',
    'js_device_added' => 'Device added successfully',
    'js_add_device_failed' => 'Failed to add device',
    'js_add_device_failed_prefix' => 'Failed to add device: {message}',
    'js_device_updated' => 'Device updated successfully',
    'js_update_device_failed' => 'Failed to update device',
    'js_update_device_failed_prefix' => 'Failed to update device: {message}',
    'js_confirm_delete_device' => 'Are you sure you want to delete this device?',
    'js_device_deleted' => 'Device deleted successfully',
    'js_delete_device_failed' => 'Failed to delete device',
    'js_delete_device_failed_prefix' => 'Failed to delete device: {message}',

    'js_csv_upload_heading' => 'Import Devices from CSV',
    'js_btn_download_template' => 'Download CSV Template',
    'js_csv_format_label' => 'CSV file format:',
    'js_csv_format_desc' => 'File must contain the following columns (with header):',
    'js_csv_col_mac_desc' => 'MAC Address (accepted formats: 00-11-22-33-44-55 or 00:11:22:33:44:55)',
    'js_csv_col_serial_desc' => 'Serial Number (required)',
    'js_csv_col_notes_desc' => 'Notes (optional)',
    'js_csv_example_label' => 'Example:',
    'js_csv_mac_normalize_note' => 'Note: MAC addresses are automatically normalized (UPPERCASE, - delimiter)',
    'js_csv_select_label' => 'Select CSV File',
    'js_csv_max_size' => 'Max size: 5MB',
    'js_csv_skip_duplicates' => 'Skip duplicates (existing MAC/Serial)',
    'js_btn_upload_import' => 'Upload & Import',

    'js_csv_select_file_error' => 'Please select a CSV file',
    'js_csv_too_large' => 'File is too large (max 5MB)',
    'js_csv_invalid_file' => 'Please select a valid CSV file',
    'js_csv_uploading' => 'Uploading...',
    'js_csv_processing' => 'Processing...',
    'js_csv_import_failed' => 'Failed to import',
    'js_csv_upload_failed_prefix' => 'Failed to upload: {message}',

    'js_import_results_heading' => 'Import Results',
    'js_stat_imported' => 'Imported',
    'js_stat_duplicates' => 'Duplicates (Skipped)',
    'js_stat_errors' => 'Errors',
    'js_error_details' => 'Error Details',
    'js_btn_back_to_list' => 'Back to List',

    'js_csv_template_downloaded' => 'CSV template downloaded',
];
