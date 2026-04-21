@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $captivePortalsT = [
        'welcome_default' => __('captive_portals.welcome_default'),
        'button_default' => __('captive_portals.button_default'),
        'instructions_default' => __('captive_portals.instructions_default'),
        'terms_default' => __('captive_portals.terms_default'),
        'privacy_default' => __('captive_portals.privacy_default'),
        'design_default' => __('captive_portals.design_default'),
        'terms_link_html' => __('captive_portals.terms_link_html'),
        'none' => __('captive_portals.none'),
        'none_image_active' => __('captive_portals.js_none_image_active'),
        'saving' => __('captive_portals.js_saving'),
        'deleting' => __('captive_portals.js_deleting'),
        'changing' => __('captive_portals.js_changing'),
        'loading' => __('captive_portals.js_loading'),
        'loading_spinner' => __('captive_portals.js_loading_spinner'),
        'no_designs' => __('captive_portals.js_no_designs'),
        'create_first' => __('captive_portals.js_create_first'),
        'error_loading' => __('captive_portals.js_error_loading'),
        'saved_success' => __('captive_portals.js_saved_success'),
        'deleted_success' => __('captive_portals.js_deleted_success'),
        'owner_changed_success' => __('captive_portals.js_owner_changed_success'),
        'invalid_design_id' => __('captive_portals.js_invalid_design_id'),
        'select_new_owner' => __('captive_portals.js_select_new_owner'),
        'loading_users' => __('captive_portals.js_loading_users'),
        'no_users_found' => __('captive_portals.js_no_users_found'),
        'error_loading_users' => __('captive_portals.js_error_loading_users'),
        'failed_load_users' => __('captive_portals.js_failed_load_users'),
        'error_required_name' => __('captive_portals.js_error_required_name'),
        'error_required_theme' => __('captive_portals.js_error_required_theme'),
        'error_required_welcome' => __('captive_portals.js_error_required_welcome'),
        'error_required_button' => __('captive_portals.js_error_required_button'),
        'error_saving' => __('captive_portals.js_error_saving'),
        'error_deleting' => __('captive_portals.js_error_deleting'),
        'error_changing_owner' => __('captive_portals.js_error_changing_owner'),
        'error_loading_details' => __('captive_portals.js_error_loading_details'),
        'error_validation' => __('captive_portals.js_error_validation'),
        'error_invalid_response' => __('captive_portals.js_error_invalid_response'),
        'owner' => __('captive_portals.js_owner'),
        'creator' => __('captive_portals.js_creator'),
        'current_owner' => __('captive_portals.js_current_owner'),
        'last_modified' => __('captive_portals.js_last_modified'),
        'edit' => __('captive_portals.js_edit'),
        'delete_button_title' => __('captive_portals.js_delete_button_title'),
        'logged_out' => __('captive_portals.js_logged_out'),
        'cta_title' => __('captive_portals.js_cta_title'),
        'cta_text' => __('captive_portals.js_cta_text'),
        'cta_button' => __('captive_portals.js_cta_button'),
        'change_owner_body_with_owner' => __('captive_portals.js_change_owner_body_with_owner'),
    ];
@endphp

@section('title', __('captive_portals.page_title'))

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 1rem;
    }

    .upload-area:hover {
        border-color: var(--mw-primary);
        background-color: rgba(99, 102, 241, 0.05);
    }

    .upload-area.highlight {
        border-color: var(--mw-primary);
        background-color: rgba(99, 102, 241, 0.1);
        transform: scale(1.02);
    }

    .upload-icon {
        font-size: 2.5rem;
        color: var(--mw-primary);
        margin-bottom: 1rem;
    }

    .color-picker-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    .image-preview {
        width: 100%;
        max-height: 150px;
        object-fit: contain;
        margin-top: 10px;
        border-radius: 5px;
        display: none;
    }

    .preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }

    .preview-header {
        background-color: #f8f9fa;
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
    }

    .portal-preview {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 500px;
    }

    .portal-preview.has-background-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 8px;
        z-index: 0;
    }

    .portal-preview.has-background-image > * {
        position: relative;
        z-index: 1;
    }

    .portal-preview.has-gradient {
        background: var(--gradient-bg) !important;
    }

    .preview-main {
        width: 100%;
        max-width: 420px;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .logo-container {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .preview-logo {
        max-height: 80px;
        max-width: 200px;
        object-fit: contain;
    }

    #preview-welcome {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        text-align: center;
        width: 100%;
    }

    #preview-instructions {
        font-size: 16px;
        color: #666;
        margin-bottom: 25px;
        text-align: center;
        line-height: 1.6;
        width: 100%;
    }

    .input-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 1rem;
    }

    .preview-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .preview-button {
        width: 100%;
        padding: 12px 20px;
        background-color: var(--mw-primary);
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .preview-terms {
        font-size: 12px;
        color: #666;
        margin-top: 15px;
        text-align: center;
    }

    .preview-terms a {
        color: var(--mw-primary);
        text-decoration: none;
    }

    .header {
        text-align: center;
        margin-bottom: 32px;
    }

    .location-logo {
        height: 64px;
        width: auto;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #666;
        padding: 0 16px;
    }

    .welcome-text {
        color: #333;
        font-size: 0.65rem;
        line-height: 1.5;
        margin: 24px 0 32px;
        text-align: center;
    }

    .login-placeholder {
        background: #f8f8f8;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
        color: #666;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    .login-placeholder-footer {
        background: #ffffff;
        border: 0px;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        color: #666;
        margin-bottom: 10px;
        flex-grow: 1;
    }

    .portal-preview .footer,
    .preview-main .footer {
        margin-top: auto;
        margin-left: 0 !important;
        margin-right: 0 !important;
        border-top: 1px solid #eee;
        padding-top: 1.5rem;
        padding-left: 0;
        padding-right: 0;
        text-align: center;
        width: 100%;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .brand-logo {
        margin-bottom: 1rem;
        margin-left: 0;
        margin-right: 0;
        display: block;
        width: 100%;
        text-align: center;
    }

    .brand-logo img {
        max-height: 32px;
        max-width: 150px;
        object-fit: contain;
        display: inline-block;
        margin: 0;
    }

    .terms {
        font-size: 0.8rem;
        color: #666;
        width: 100%;
        text-align: center;
        display: block;
        margin-left: 0;
        margin-right: 0;
        padding-left: 0;
        padding-right: 0;
    }

    #preview-terms-container {
        margin-bottom: 0.5rem !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #preview-powered-by {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .terms a {
        color: #007bff;
        text-decoration: none;
    }

    .terms a:hover {
        text-decoration: underline;
    }

    .design-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e0e0e0;
    }

    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .design-preview {
        height: 180px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }

    .design-preview .preview-content {
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .location-logo-mini {
        height: 24px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .login-area-mini {
        flex-grow: 1;
        background: rgba(248, 248, 248, 0.8);
        border: 1px dashed #ddd;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #666;
        margin: 8px 0;
    }

    .brand-logo-mini {
        height: 20px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .design-card:hover .design-preview {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }

    #preview-button {
        transition: all 0.3s ease;
        padding: 10px 20px;
        font-size: 14px;
    }

    .terms-container {
        margin-top: 15px;
        text-align: center;
    }

    .section-label {
        font-weight: 600;
        color: #5e5873;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    .design-card {
        transition: all 0.3s ease;
        border: 1px solid #ebe9f1;
    }

    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }

    .design-preview {
        height: 160px;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }

    .preview-content {
        padding: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .design-actions {
        padding-top: 0.5rem;
        border-top: 1px solid #ebe9f1;
    }

    .design-actions .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .design-actions .btn-group .btn + .btn {
        margin-left: 0;
    }

    .badge {
        font-size: 0.8rem;
        font-weight: 500;
    }


    .tab-content h6 {
        color: #5e5873;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #ebe9f1;
        padding-bottom: 0.5rem;
    }

    .color-picker-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        max-width: 300px;
    }

    .form-control-color {
        width: 60px;
        padding: 0.2rem;
        height: 38px;
    }

    .card-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: 2000 !important;
        background: #fff;
    }

    .modal {
        z-index: 2100 !important;
    }

    .modal-backdrop {
        z-index: 2050 !important;
    }

    .card-fullscreen .card-body {
        height: calc(100% - 60px);
        overflow-y: auto;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .card-fullscreen .portal-preview {
        max-width: 100%;
        width: 90%;
        margin: 0 auto;
        height: auto;
        min-height: 500px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        border-radius: 0.428rem;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        color: #6e6b7b;
    }

    @media (max-width: 991px) {
        .preview-container {
            margin-top: 2rem;
        }

        .portal-preview {
            min-height: 350px;
        }
    }

    @media (max-width: 767px) {
        .portal-preview {
            padding: 20px;
            min-height: 320px;
        }

        #preview-welcome {
            font-size: 20px;
        }

        #preview-instructions {
            font-size: 14px;
        }

        .preview-input, .preview-button {
            padding: 8px 15px;
            font-size: 14px;
        }
    }

    @media (max-width: 575px) {
        .preview-main {
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('captive_portals.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('captive_portals.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Timeline -->
    <div id="onboarding-timeline" style="max-width:700px; margin:0 auto 25px; padding:0 20px;">
        <div style="display:flex; align-items:flex-start; justify-content:center; position:relative;">
            <div style="position:absolute; top:24px; left:calc(16.66% + 20px); right:calc(16.66% + 20px); height:3px; background:#e0e0e0; z-index:0;"></div>
            <div id="timeline-step-1" style="display:flex; flex-direction:column; align-items:center; flex:1; position:relative; z-index:1;">
                <div id="timeline-circle-1" style="width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; margin-bottom:12px; background:var(--mw-primary); color:white; box-shadow:0 4px 15px rgba(99,102,241,0.4);">1</div>
                <div style="font-size:0.85rem; font-weight:600; color:#333; text-align:center;">{{ __('captive_portals.timeline_step1_label') }}</div>
                <div style="font-size:0.75rem; color:#888; text-align:center; margin-top:4px;">{{ __('captive_portals.timeline_step1_sub') }}</div>
            </div>
            <a id="timeline-step-2" href="/pricing" style="display:flex; flex-direction:column; align-items:center; flex:1; position:relative; z-index:1; text-decoration:none; cursor:pointer;">
                <div id="timeline-circle-2" style="width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; margin-bottom:12px; background:#f0f0f0; color:#999; border:2px solid #ddd;">2</div>
                <div id="timeline-label-2" style="font-size:0.85rem; font-weight:600; color:#999; text-align:center;">{{ __('captive_portals.timeline_step2_label') }}</div>
                <div id="timeline-sub-2" style="font-size:0.75rem; color:#bbb; text-align:center; margin-top:4px;">{{ __('captive_portals.timeline_step2_sub') }}</div>
            </a>
            <div id="timeline-step-3" style="display:flex; flex-direction:column; align-items:center; flex:1; position:relative; z-index:1;">
                <div id="timeline-circle-3" style="width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; margin-bottom:12px; background:#f0f0f0; color:#999; border:2px solid #ddd;">3</div>
                <div style="font-size:0.85rem; font-weight:600; color:#999; text-align:center;">{{ __('captive_portals.timeline_step3_label') }}</div>
                <div style="font-size:0.75rem; color:#bbb; text-align:center; margin-top:4px;">{{ __('captive_portals.timeline_step3_sub') }}</div>
            </div>
        </div>
    </div>

    <!-- Device CTA Banner - hidden by default, shown via JS if user has no devices -->
    <div id="device-cta-banner" style="display: none;"></div>

    <!-- Captive Portal Designs List -->
    <section id="captive-portal-designs-list">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('captive_portals.your_designs_title') }}</h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="create-new-design">
                                        <i data-feather="plus" class="mr-50"></i>
                                        <span>{{ __('captive_portals.create_new_design') }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="portal-designs-container">
                            <!-- Design Cards will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Captive Portal Design Content Starts - Initially hidden -->
    <section id="captive-portal-designer" style="display: none;">
        <div class="row">
            <div class="col-12 mb-1">
                <button class="btn btn-outline-secondary waves-effect" id="back-to-list">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    <span>{{ __('captive_portals.back_to_designs') }}</span>
                </button>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('captive_portals.designer_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" aria-controls="general" role="tab" aria-selected="true">
                                    <i data-feather="settings" class="mr-25"></i>
                                    <span class="font-weight-bold">{{ __('captive_portals.tab_general') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="branding-tab" data-toggle="tab" href="#branding" aria-controls="branding" role="tab" aria-selected="false">
                                    <i data-feather="image" class="mr-25"></i>
                                    <span class="font-weight-bold">{{ __('captive_portals.tab_branding') }}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- General Tab -->
                            <div class="tab-pane active" id="general" aria-labelledby="general-tab" role="tabpanel">
                                <form class="mt-2">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <h6 class="mb-1">{{ __('captive_portals.section_basic_info') }}</h6>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="portal-name">{{ __('captive_portals.label_portal_name') }}</label>
                                                <input type="text" class="form-control" id="portal-name" placeholder="{{ __('captive_portals.placeholder_portal_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="portal-description">{{ __('captive_portals.label_description') }}</label>
                                                <textarea class="form-control" id="portal-description" rows="2" placeholder="{{ __('captive_portals.placeholder_description') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="theme-color">{{ __('captive_portals.label_theme_color') }}</label>
                                                <div class="color-picker-container">
                                                    <input type="color" class="form-control form-control-color" id="theme-color" value="#6366f1">
                                                    <div class="color-preview" style="background-color: #6366f1;"></div>
                                                    <span class="color-value">#6366f1</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2 mb-1">
                                            <h6 class="mb-1">{{ __('captive_portals.section_portal_content') }}</h6>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="welcome-message">{{ __('captive_portals.label_welcome_message') }}</label>
                                                <input type="text" class="form-control" id="welcome-message" placeholder="{{ __('captive_portals.welcome_default') }}" value="{{ __('captive_portals.welcome_default') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="button-text">{{ __('captive_portals.label_button_text') }}</label>
                                                <input type="text" class="form-control" id="button-text" placeholder="{{ __('captive_portals.button_default') }}" value="{{ __('captive_portals.button_default') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="login-instructions">{{ __('captive_portals.label_login_instructions') }}</label>
                                                <textarea class="form-control" id="login-instructions" rows="2" placeholder="{{ __('captive_portals.instructions_default') }}">{{ __('captive_portals.instructions_default') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="show-terms" checked>
                                                    <label class="custom-control-label" for="show-terms">{{ __('captive_portals.label_show_terms') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2 mb-1">
                                            <h6 class="mb-1">{{ __('captive_portals.section_legal_content') }}</h6>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="terms-of-service">{{ __('captive_portals.label_terms_content') }}</label>
                                                <textarea class="form-control" id="terms-of-service" rows="3" placeholder="{{ __('captive_portals.placeholder_terms_content') }}">{{ __('captive_portals.terms_default') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <label for="privacy-policy">{{ __('captive_portals.label_privacy_content') }}</label>
                                                <textarea class="form-control" id="privacy-policy" rows="3" placeholder="{{ __('captive_portals.placeholder_privacy_content') }}">{{ __('captive_portals.privacy_default') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Branding Tab -->
                            <div class="tab-pane" id="branding" aria-labelledby="branding-tab" role="tabpanel">
                                <form class="mt-2">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="form-group">
                                                <label for="location-logo">{{ __('captive_portals.label_location_logo') }}</label>
                                                <div class="upload-area" id="location-logo-upload">
                                                    <i data-feather="upload-cloud" class="upload-icon"></i>
                                                    <h5 class="upload-text">{{ __('captive_portals.upload_location_logo') }}</h5>
                                                    <p class="text-muted small">{{ __('captive_portals.recommended_logo') }}</p>
                                                </div>
                                                <input type="file" id="location-logo-file" name="location_logo" class="d-none" accept="image/*">
                                                <img src="" id="location-logo-preview" class="image-preview">
                                                <p class="note">{{ __('captive_portals.note_location_logo') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="form-group">
                                                <label for="background-image">{{ __('captive_portals.label_background_image') }}</label>
                                                <div class="upload-area" id="background-upload">
                                                    <i data-feather="image" class="upload-icon"></i>
                                                    <h5 class="upload-text">{{ __('captive_portals.upload_background') }}</h5>
                                                    <p class="text-muted small">{{ __('captive_portals.recommended_background') }}</p>
                                                </div>
                                                <input type="file" id="background-file" name="background_image" class="d-none" accept="image/*">
                                                <img src="" id="background-preview" class="image-preview">
                                                <p class="note">{{ __('captive_portals.note_background') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <h6 class="mb-1">{{ __('captive_portals.section_gradient') }}</h6>
                                            <p class="text-muted small mb-2">{{ __('captive_portals.note_gradient') }}</p>
                                            <div class="row">
                                                <div class="col-md-6 col-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="gradient-start">{{ __('captive_portals.label_gradient_start') }}</label>
                                                        <div class="color-picker-container">
                                                            <input type="color" class="form-control form-control-color" id="gradient-start">
                                                            <div class="color-preview" id="gradient-start-preview" style="background-color: transparent;"></div>
                                                            <span class="color-value" id="gradient-start-value">{{ __('captive_portals.none') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="gradient-end">{{ __('captive_portals.label_gradient_end') }}</label>
                                                        <div class="color-picker-container">
                                                            <input type="color" class="form-control form-control-color" id="gradient-end">
                                                            <div class="color-preview" id="gradient-end-preview" style="background-color: transparent;"></div>
                                                            <span class="color-value" id="gradient-end-value">{{ __('captive_portals.none') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-gradient">
                                                        <i data-feather="x" class="mr-25"></i>{{ __('captive_portals.btn_clear_gradient') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="preset-gradient-1">
                                                        {{ __('captive_portals.btn_preset_blue_purple') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="preset-gradient-2">
                                                        {{ __('captive_portals.btn_preset_orange_pink') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success ml-1" id="test-gradient">
                                                        {{ __('captive_portals.btn_test_gradient') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 d-flex justify-content-end">
                                <button id="save-design" class="btn btn-primary">
                                    <i data-feather="save" class="mr-50"></i>{{ __('captive_portals.save_design') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Column -->
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('captive_portals.preview_title') }}</h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    <a data-action="reload"><i data-feather="rotate-cw"></i></a>
                                </li>
                                <li>
                                    <a data-action="expand" id="expand-preview"><i data-feather="maximize"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="portal-preview">
                            <div class="preview-main">
                                <!-- Header with Location Logo -->
                                <div class="logo-container">
                                    <img src="/img/wifi-placeholder.png" alt="{{ __('captive_portals.alt_location_logo') }}" id="preview-logo" class="preview-logo">
                                </div>

                                <!-- Welcome Text -->
                                <h2 id="preview-welcome">{{ __('captive_portals.welcome_default') }}</h2>
                                <p id="preview-instructions">{{ __('captive_portals.instructions_default') }}</p>

                                <!-- Login Form -->
                                <div class="input-container">
                                    <input type="text" class="preview-input" placeholder="{{ __('captive_portals.placeholder_email') }}">
                                    <button id="preview-button" class="preview-button">{{ __('captive_portals.button_default') }}</button>
                                </div>

                                <!-- Footer with Brand Logo and Terms -->
                                <div class="footer">
                                    <div class="brand-logo">
                                        <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="{{ __('captive_portals.alt_brand_logo') }}">
                                    </div>
                                    <div class="terms" id="preview-terms-container" style="display: none; margin-bottom: 0.5rem;">
                                        <!-- Terms links will be inserted here when show_terms is enabled -->
                                    </div>
                                    <div class="terms" id="preview-powered-by">
                                        {{ __('captive_portals.powered_by') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Preview Modals -->
<div class="modal fade" id="previewTermsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">{{ __('captive_portals.modal_terms_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="preview-terms-content">{{ __('captive_portals.terms_default') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewPrivacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">{{ __('captive_portals.modal_privacy_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="preview-privacy-content">{{ __('captive_portals.privacy_default') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDesignModal" tabindex="-1" aria-labelledby="deleteDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDesignModalLabel">{{ __('captive_portals.modal_delete_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ __('captive_portals.modal_delete_body') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('captive_portals.modal_delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Owner Modal -->
<div class="modal fade" id="changeOwnerModal" tabindex="-1" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeOwnerModalLabel">{{ __('captive_portals.modal_change_owner_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="changeOwnerText">{{ __('captive_portals.modal_change_owner_body') }}</p>
                <div class="form-group">
                    <label for="newOwnerSelect">{{ __('captive_portals.label_new_owner') }}</label>
                    <select class="form-control" id="newOwnerSelect">
                        <option value="">{{ __('captive_portals.loading_users') }}</option>
                    </select>
                </div>
                <div class="alert alert-info mt-2">
                    {!! __('captive_portals.note_change_owner_html') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirmChangeOwnerBtn">{{ __('captive_portals.btn_change_owner') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.captive_portals = @json($captivePortalsT);
</script>
<script src="/assets/js/captive-portals.js?v={{ filemtime(public_path('assets/js/captive-portals.js')) }}"></script>
@endpush
