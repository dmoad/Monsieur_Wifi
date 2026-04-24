<?php

return [
    'page_title' => 'Firmware Management - Monsieur WiFi',
    'heading' => 'Firmware Management',
    'breadcrumb' => 'Firmware',

    // Header action
    'upload_new' => 'Upload New Firmware',

    // Inline warning banner
    'alert_sysupgrade' => 'Updates flash via <strong>OpenWrt sysupgrade</strong>. Devices will reboot — clients briefly disconnect.',

    // Table
    'card_title' => 'All Firmware',
    'search_placeholder' => 'Search firmware...',
    'no_firmware' => 'No firmware found',
    'col_name' => 'Name',
    'col_status' => 'Status',
    'col_model' => 'Device Model',
    'col_default' => 'Default',
    'col_size' => 'Size',
    'col_actions' => 'Actions',

    // Upload modal
    'modal_upload_title' => 'Upload New Firmware',
    'name_label' => 'Firmware Name',
    'name_placeholder' => 'e.g. v2.1.5 Security Update',
    'status_label' => 'Status',
    'status_enable' => 'Enable',
    'status_disable' => 'Disable',
    'model_label' => 'Device Model',
    'default_checkbox' => 'Set as default firmware for this model',
    'default_help' => 'When enabled, this firmware will be automatically assigned to new devices of this model.',
    'description_label' => 'Description',
    'description_placeholder' => 'Firmware description and changelog',
    'file_label' => 'Firmware File',
    'choose_file' => 'Choose file',
    'file_help_upload' => 'Max file size: 100MB. Accepted formats: .tar.gz, .tgz, .tar',
    'cancel' => 'Cancel',
    'upload_btn' => 'Upload Firmware',

    // Edit modal
    'modal_edit_title' => 'Edit Firmware',
    'file_optional_label' => 'Firmware File (Optional)',
    'choose_firmware_file' => 'Choose firmware file',
    'file_help_edit' => 'Accepted formats: .tar.gz, .tgz, .tar',
    'save_changes' => 'Save Changes',

    // JS: Select2 placeholders
    'select_status_placeholder' => 'Select status',
    'select_model_placeholder' => 'Select device model',
    'select_model_option' => 'Select Model',
    'loading' => 'Loading...',

    // JS: badges
    'badge_enabled' => 'Enable',
    'badge_disabled' => 'Disable',
    'badge_default' => 'Default',

    // JS: row action dropdown
    'action_edit' => 'Edit',
    'action_download' => 'Download',
    'action_set_default' => 'Set as Default',
    'action_delete' => 'Delete',

    // JS: toasts + dialogs
    'please_select_file' => 'Please select a firmware file',
    'upload_success' => 'Firmware uploaded successfully',
    'upload_error' => 'Error uploading firmware',
    'update_success' => 'Firmware updated successfully',
    'update_error' => 'Error updating firmware',
    'delete_confirm' => 'Are you sure you want to delete this firmware?',
    'delete_success' => 'Firmware deleted successfully',
    'delete_error' => 'Error deleting firmware',
    'set_default_confirm' => 'Are you sure you want to set "{name}" as the default firmware for {model} devices?',
    'set_default_success' => 'Firmware set as default successfully',
    'set_default_error' => 'Error setting firmware as default',
    'load_error' => 'Error loading firmware data',
    'model_not_specified' => 'Not specified',

    // JS: DataTable language pack
    'dt_info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
    'dt_info_empty' => 'Showing 0 to 0 of 0 entries',
    'dt_info_filtered' => '(filtered from _MAX_ total entries)',
    'dt_length_menu' => 'Show _MENU_ entries',
    'dt_search' => 'Search:',
    'dt_zero_records' => 'No matching records found',
    'dt_empty_table' => 'No data available in table',
    'dt_loading_records' => 'Loading...',
];
