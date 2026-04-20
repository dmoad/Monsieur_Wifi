<?php

return [
    'page_title' => 'Captive Portal Designer - Monsieur WiFi',
    'heading' => 'Captive Portal Designer',
    'breadcrumb' => 'Captive Portals',

    // Onboarding timeline
    'timeline_step1_label' => 'I design my portal',
    'timeline_step1_sub' => 'Custom captive portal',
    'timeline_step2_label' => 'I subscribe',
    'timeline_step2_sub' => 'Choose plan & payment',
    'timeline_step3_label' => 'I receive my device',
    'timeline_step3_sub' => 'Delivery + setup assistance',

    // Designs list
    'your_designs_title' => 'Your Captive Portal Designs',
    'create_new_design' => 'Create New Design',
    'back_to_designs' => 'Back to Designs',

    // Designer
    'designer_title' => 'Design Your Login Page',
    'tab_general' => 'General',
    'tab_branding' => 'Branding',
    'save_design' => 'Save Design',

    // General tab
    'section_basic_info' => 'Basic Information',
    'label_portal_name' => 'Portal Name',
    'placeholder_portal_name' => 'Enter a name for this login page',
    'label_description' => 'Description',
    'placeholder_description' => 'Brief description of this design',
    'label_theme_color' => 'Theme Color',

    'section_portal_content' => 'Portal Content',
    'label_welcome_message' => 'Welcome Message',
    'label_button_text' => 'Button Text',
    'label_login_instructions' => 'Login Instructions',
    'label_show_terms' => 'Show Terms & Conditions Link',

    'section_legal_content' => 'Legal Content',
    'label_terms_content' => 'Terms of Service Content',
    'placeholder_terms_content' => 'Enter your terms of service content',
    'label_privacy_content' => 'Privacy Policy Content',
    'placeholder_privacy_content' => 'Enter your privacy policy content',

    // Branding tab
    'label_location_logo' => 'Location Logo',
    'upload_location_logo' => 'Drop your location logo here or click to browse',
    'recommended_logo' => 'Recommended: PNG or SVG, 200x100px',
    'note_location_logo' => 'Your location logo will appear at the top of the login page.',
    'label_background_image' => 'Background Image',
    'upload_background' => 'Drop your background image here or click to browse',
    'recommended_background' => 'Recommended: JPG or PNG, 1920x1080px',
    'note_background' => 'This image will be displayed as the page background.',

    'section_gradient' => 'Background Gradient (Alternative to Image)',
    'note_gradient' => 'Create a gradient background instead of using an image. This will override the background image if both are set.',
    'label_gradient_start' => 'Gradient Start Color',
    'label_gradient_end' => 'Gradient End Color',
    'btn_clear_gradient' => 'Clear Gradient',
    'btn_preset_blue_purple' => 'Blue to Purple',
    'btn_preset_orange_pink' => 'Orange to Pink',
    'btn_test_gradient' => 'Test Gradient',

    // Preview panel
    'preview_title' => 'Preview',
    'alt_location_logo' => 'Location Logo',
    'alt_brand_logo' => 'Brand Logo',
    'placeholder_email' => 'Email Address',
    'powered_by' => 'Powered by Monsieur WiFi',

    // Modals
    'modal_terms_title' => 'Terms of Service',
    'modal_privacy_title' => 'Privacy Policy',
    'modal_delete_title' => 'Confirm Deletion',
    'modal_delete_body' => 'Are you sure you want to delete this design? This action cannot be undone.',
    'modal_delete_confirm' => 'Delete',
    'modal_change_owner_title' => 'Change Design Owner',
    'modal_change_owner_body' => 'Select a new owner for this captive portal design:',
    'label_new_owner' => 'New Owner',
    'loading_users' => 'Loading users...',
    'note_change_owner_html' => '<strong>Note:</strong> This action will transfer ownership of the design to the selected user. The original creator information will be preserved.',
    'btn_change_owner' => 'Change Owner',

    // Defaults reused as blade initial values (and by JS in a later commit)
    'none' => 'None',
    'welcome_default' => 'Welcome to our WiFi',
    'button_default' => 'Connect to WiFi',
    'instructions_default' => 'Enter your email to connect to our WiFi network',
    'terms_default' => "By accessing this WiFi service, you agree to comply with all applicable laws and the network's acceptable use policy. We reserve the right to monitor traffic and content accessed through our network, and to terminate access for violations of these terms.",
    'privacy_default' => 'We collect limited information when you use our WiFi service, including device identifiers, connection times, and usage data. This information is used to improve our service, troubleshoot technical issues, and comply with legal requirements. We do not sell your personal information to third parties.',
];
