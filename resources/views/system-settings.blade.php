@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $systemSettingsT = [
        'saving' => __('system_settings.saving'),
        'sending' => __('system_settings.sending'),
        'choose_file' => __('system_settings.choose_file'),
        'toast_saved_title' => __('system_settings.toast_saved_title'),
        'toast_saved_body' => __('system_settings.toast_saved_body'),
        'toast_save_failed' => __('system_settings.toast_save_failed'),
        'toast_error_title' => __('system_settings.toast_error_title'),
        'toast_test_email_title' => __('system_settings.toast_test_email_title'),
        'toast_test_email_body' => __('system_settings.toast_test_email_body'),
        'toast_test_email_failed' => __('system_settings.toast_test_email_failed'),
        'toast_load_failed' => __('system_settings.toast_load_failed'),
    ];
@endphp

@section('title', __('system_settings.page_title'))

@push('styles')
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/forms/select/select2.min.css">
<style>
    .setting-section {
        padding: 1.5rem;
        border-radius: 0.428rem;
        border: 1px solid var(--mw-border-light);
        margin-bottom: 1.5rem;
    }
    .setting-section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    .setting-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('system_settings.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('system_settings.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
                        <div class="mw-tabs" role="tablist">
                            <button type="button" class="mw-tab active" data-tab="captive-portal" role="tab">{{ __('system_settings.tab_captive_portal') }}</button>
                            <button type="button" class="mw-tab" data-tab="radius" role="tab">{{ __('system_settings.tab_radius') }}</button>
                            <button type="button" class="mw-tab" data-tab="branding" role="tab">{{ __('system_settings.tab_branding') }}</button>
                            <button type="button" class="mw-tab" data-tab="system" role="tab">{{ __('system_settings.tab_system') }}</button>
                        </div>

                        <div class="tab-content">
                            <!-- Captive Portal Tab -->
                            <div class="mw-panel active" id="captive-portal" role="tabpanel">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-primary mr-1"><i data-feather="wifi"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_default_wifi') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_essid">{{ __('system_settings.label_default_essid') }}</label>
                                                    <input type="text" id="default_essid" class="form-control" name="default_essid" placeholder="MrWiFi-Guest" />
                                                    <small>{{ __('system_settings.help_default_essid') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_guest_essid">{{ __('system_settings.label_default_guest_essid') }}</label>
                                                    <input type="text" id="default_guest_essid" class="form-control" name="default_guest_essid" placeholder="MrWiFi-Guest" />
                                                    <small>{{ __('system_settings.help_default_guest_essid') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_password">{{ __('system_settings.label_default_password') }}</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="default_password" class="form-control" name="default_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_default_password') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-info mr-1"><i data-feather="layout"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_portal_behavior') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_timeout">{{ __('system_settings.label_portal_timeout') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" id="portal_timeout" class="form-control" name="portal_timeout" min="1" max="168" />
                                                        <div class="input-group-append"><span class="input-group-text">{{ __('system_settings.unit_hours') }}</span></div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_portal_timeout') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="idle_timeout">{{ __('system_settings.label_idle_timeout') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" id="idle_timeout" class="form-control" name="idle_timeout" min="5" max="180" />
                                                        <div class="input-group-append"><span class="input-group-text">{{ __('system_settings.unit_minutes') }}</span></div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_idle_timeout') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="bandwidth_limit">{{ __('system_settings.label_bandwidth_limit') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" id="bandwidth_limit" class="form-control" name="bandwidth_limit" min="1" max="1000" />
                                                        <div class="input-group-append"><span class="input-group-text">{{ __('system_settings.unit_mbps') }}</span></div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_bandwidth_limit') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="user_limit">{{ __('system_settings.label_user_limit') }}</label>
                                                    <input type="number" id="user_limit" class="form-control" name="user_limit" min="1" max="500" />
                                                    <small>{{ __('system_settings.help_user_limit') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" id="enable_terms" name="enable_terms" />
                                                        <label class="custom-control-label" for="enable_terms">{{ __('system_settings.label_enable_terms') }}</label>
                                                    </div>
                                                    <small>{{ __('system_settings.help_enable_terms') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">{{ __('system_settings.save_changes') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- RADIUS Tab -->
                            <div class="mw-panel" id="radius" role="tabpanel">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-primary mr-1"><i data-feather="shield"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_primary_radius') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_ip">{{ __('system_settings.label_radius_ip') }}</label>
                                                    <input type="text" id="radius_ip" class="form-control" name="radius_ip" placeholder="192.168.1.100" />
                                                    <small>{{ __('system_settings.help_radius_ip') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_port">{{ __('system_settings.label_radius_port') }}</label>
                                                    <input type="number" id="radius_port" class="form-control" name="radius_port" placeholder="1812" min="1" max="65535" />
                                                    <small>{{ __('system_settings.help_radius_port') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_secret">{{ __('system_settings.label_radius_secret') }}</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="radius_secret" class="form-control" name="radius_secret" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_radius_secret') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="accounting_port">{{ __('system_settings.label_accounting_port') }}</label>
                                                    <input type="number" id="accounting_port" class="form-control" name="accounting_port" placeholder="1813" min="1" max="65535" />
                                                    <small>{{ __('system_settings.help_accounting_port') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">{{ __('system_settings.save_changes') }}</button>
                                            <button type="reset" class="btn btn-outline-secondary">{{ __('common.reset') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Branding Tab -->
                            <div class="mw-panel" id="branding" role="tabpanel">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-primary mr-1"><i data-feather="type"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_company_info') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_name">{{ __('system_settings.label_company_name') }}</label>
                                                    <input type="text" id="company_name" class="form-control" name="company_name" placeholder="monsieur-wifi" />
                                                    <small>{{ __('system_settings.help_company_name') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_website">{{ __('system_settings.label_company_website') }}</label>
                                                    <input type="url" id="company_website" class="form-control" name="company_website" placeholder="https://www.example.com" />
                                                    <small>{{ __('system_settings.help_company_website') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="contact_email">{{ __('system_settings.label_contact_email') }}</label>
                                                    <input type="email" id="contact_email" class="form-control" name="contact_email" placeholder="support@example.com" />
                                                    <small>{{ __('system_settings.help_contact_email') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="support_phone">{{ __('system_settings.label_support_phone') }}</label>
                                                    <input type="tel" id="support_phone" class="form-control" name="support_phone" placeholder="+1 (555) 123-4567" />
                                                    <small>{{ __('system_settings.help_support_phone') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-info mr-1"><i data-feather="image"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_logo_images') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="logo-upload">{{ __('system_settings.label_logo') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="logo-upload" accept="image/*" />
                                                        <label class="custom-file-label" for="logo-upload">{{ __('system_settings.choose_file') }}</label>
                                                    </div>
                                                    <small>{{ __('system_settings.help_logo') }}</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('system_settings.label_current_logo') }}</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/assets/images/Mr-Wifi.PNG" alt="{{ __('system_settings.alt_current_logo') }}" height="50" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="favicon-upload">{{ __('system_settings.label_favicon') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="favicon-upload" accept="image/x-icon,image/png,image/gif" />
                                                        <label class="custom-file-label" for="favicon-upload">{{ __('system_settings.choose_file') }}</label>
                                                    </div>
                                                    <small>{{ __('system_settings.help_favicon') }}</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('system_settings.label_current_favicon') }}</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/assets/images/MrWifi.png" alt="{{ __('system_settings.alt_current_favicon') }}" height="32" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="splash-background">{{ __('system_settings.label_splash_background') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="splash-background" accept="image/*" />
                                                        <label class="custom-file-label" for="splash-background">{{ __('system_settings.choose_file') }}</label>
                                                    </div>
                                                    <small>{{ __('system_settings.help_splash_background') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-warning mr-1"><i data-feather="layers"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_portal_customization') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="primary_color">{{ __('system_settings.label_primary_color') }}</label>
                                                    <div class="input-group">
                                                        <input type="color" id="primary_color" class="form-control" name="primary_color" value="#6366f1" />
                                                        <div class="input-group-append"><span class="input-group-text">#6366f1</span></div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_primary_color') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="secondary_color">{{ __('system_settings.label_secondary_color') }}</label>
                                                    <div class="input-group">
                                                        <input type="color" id="secondary_color" class="form-control" name="secondary_color" value="#82868b" />
                                                        <div class="input-group-append"><span class="input-group-text">#82868b</span></div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_secondary_color') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="font_family">{{ __('system_settings.label_font_family') }}</label>
                                                    <select id="font_family" class="form-control" name="font_family">
                                                        <option value="montserrat" selected>Montserrat</option>
                                                        <option value="roboto">Roboto</option>
                                                        <option value="open-sans">Open Sans</option>
                                                        <option value="lato">Lato</option>
                                                        <option value="poppins">Poppins</option>
                                                    </select>
                                                    <small>{{ __('system_settings.help_font_family') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_theme">{{ __('system_settings.label_portal_theme') }}</label>
                                                    <select id="portal_theme" class="form-control" name="portal_theme">
                                                        <option value="light" selected>{{ __('system_settings.theme_light') }}</option>
                                                        <option value="dark">{{ __('system_settings.theme_dark') }}</option>
                                                        <option value="auto">{{ __('system_settings.theme_auto') }}</option>
                                                    </select>
                                                    <small>{{ __('system_settings.help_portal_theme') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">{{ __('system_settings.save_changes') }}</button>
                                            <button type="reset" class="btn btn-outline-secondary">{{ __('common.reset') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- System Tab -->
                            <div class="mw-panel" id="system" role="tabpanel">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <span class="mw-stat-icon mw-stat-icon-warning mr-1"><i data-feather="mail"></i></span>
                                            <h3 class="setting-section-title">{{ __('system_settings.section_email_config') }}</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_server">{{ __('system_settings.label_smtp_server') }}</label>
                                                    <input type="text" id="smtp_server" class="form-control" name="smtp_server" placeholder="smtp.example.com" />
                                                    <small>{{ __('system_settings.help_smtp_server') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_port">{{ __('system_settings.label_smtp_port') }}</label>
                                                    <input type="number" id="smtp_port" class="form-control" name="smtp_port" placeholder="587" min="1" max="65535" />
                                                    <small>{{ __('system_settings.help_smtp_port') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="sender_email">{{ __('system_settings.label_sender_email') }}</label>
                                                    <input type="email" id="sender_email" class="form-control" name="sender_email" placeholder="notifications@example.com" />
                                                    <small>{{ __('system_settings.help_sender_email') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_password">{{ __('system_settings.label_smtp_password') }}</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="smtp_password" class="form-control" name="smtp_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>{{ __('system_settings.help_smtp_password') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" id="send-test-email" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="send" class="mr-25"></i><span>{{ __('system_settings.send_test_email') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">{{ __('system_settings.save_changes') }}</button>
                                            <button type="reset" class="btn btn-outline-secondary">{{ __('common.reset') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
</div>
@endsection

@push('scripts')
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/assets/vendors/js/forms/validation/jquery.validate.min.js"></script>

<script>
    const T = @json($systemSettingsT);
    const LOCALE = @json($locale);

    $(window).on('load', function() {
        if ($.fn.select2) {
            $('#font_family, #portal_theme').select2({ minimumResultsForSearch: Infinity });
        }

        $('.custom-file-input').on('change', function() {
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || T.choose_file);
        });

        $('#primary_color, #secondary_color').on('input change', function() {
            $(this).closest('.input-group').find('.input-group-text').text($(this).val());
        });

        $('.form-password-toggle .input-group-text').on('click', function(e) {
            e.preventDefault();
            const passwordInput = $(this).closest('.form-password-toggle').find('input');
            if (passwordInput.attr('type') === 'text') {
                passwordInput.attr('type', 'password');
                $(this).find('svg').replaceWith(feather.icons['eye'].toSvg());
            } else {
                passwordInput.attr('type', 'text');
                $(this).find('svg').replaceWith(feather.icons['eye-off'].toSvg());
            }
        });

        const initialHash = window.location.hash.replace(/^#/, '');
        if (initialHash) activateSysTab(initialHash, { updateHash: false });

        loadSettings();
    });

    function activateSysTab(key, { updateHash = true } = {}) {
        const tab = document.querySelector('.mw-tab[data-tab="' + key + '"]');
        const panel = document.getElementById(key);
        if (!tab || !panel) return;
        document.querySelectorAll('.mw-tab').forEach(t => t.classList.toggle('active', t === tab));
        document.querySelectorAll('.mw-panel').forEach(p => p.classList.toggle('active', p === panel));
        if (updateHash) {
            if (history.pushState) history.pushState(null, null, '#' + key);
            else window.location.hash = '#' + key;
        }
    }

    document.addEventListener('click', function(e) {
        const tab = e.target.closest('.mw-tab');
        if (!tab) return;
        const key = tab.dataset.tab;
        if (key) activateSysTab(key);
    });

    $(document).ready(function() {
        $('.validate-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            const tabId = form.closest('.mw-panel').attr('id');

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + T.saving).attr('disabled', true);

            const formData = new FormData();
            switch(tabId) {
                case 'captive-portal':
                    formData.append('default_essid', $('#default_essid').val());
                    formData.append('default_guest_essid', $('#default_guest_essid').val());
                    formData.append('default_password', $('#default_password').val());
                    formData.append('portal_timeout', $('#portal_timeout').val());
                    formData.append('idle_timeout', $('#idle_timeout').val());
                    formData.append('bandwidth_limit', $('#bandwidth_limit').val());
                    formData.append('user_limit', $('#user_limit').val());
                    formData.append('enable_terms', $('#enable_terms').is(':checked') ? 1 : 0);
                    break;
                case 'radius':
                    formData.append('radius_ip', $('#radius_ip').val());
                    formData.append('radius_port', $('#radius_port').val());
                    formData.append('radius_secret', $('#radius_secret').val());
                    formData.append('accounting_port', $('#accounting_port').val());
                    break;
                case 'branding':
                    formData.append('company_name', $('#company_name').val());
                    formData.append('company_website', $('#company_website').val());
                    formData.append('contact_email', $('#contact_email').val());
                    formData.append('support_phone', $('#support_phone').val());
                    formData.append('primary_color', $('#primary_color').val());
                    formData.append('secondary_color', $('#secondary_color').val());
                    formData.append('font_family', $('#font_family').val());
                    formData.append('portal_theme', $('#portal_theme').val());
                    if ($('#logo-upload')[0].files[0])       formData.append('logo', $('#logo-upload')[0].files[0]);
                    if ($('#favicon-upload')[0].files[0])    formData.append('favicon', $('#favicon-upload')[0].files[0]);
                    if ($('#splash-background')[0].files[0]) formData.append('splash_background', $('#splash-background')[0].files[0]);
                    break;
                case 'system':
                    formData.append('smtp_server', $('#smtp_server').val());
                    formData.append('smtp_port', $('#smtp_port').val());
                    formData.append('sender_email', $('#sender_email').val());
                    formData.append('smtp_password', $('#smtp_password').val());
                    break;
            }

            $.ajax({
                url: '/api/system-settings', type: 'POST', data: formData, processData: false, contentType: false,
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
                success: function(response) {
                    toastr.success(T.toast_saved_body, T.toast_saved_title);
                    if (response.settings) populateFormFields(response.settings);
                    submitBtn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : (xhr.responseJSON?.message || T.toast_save_failed);
                    toastr.error(msg, T.toast_error_title);
                    submitBtn.html(originalText).attr('disabled', false);
                }
            });
        });

        $('#send-test-email').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + T.sending).attr('disabled', true);
            $.ajax({
                url: '/api/system-settings/test-email', type: 'POST',
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                data: JSON.stringify({ email: $('#sender_email').val() }),
                success: function() {
                    toastr.info(T.toast_test_email_body.replace('{email}', $('#sender_email').val()), T.toast_test_email_title);
                    btn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || T.toast_test_email_failed, T.toast_error_title);
                    btn.html(originalText).attr('disabled', false);
                }
            });
        });
    });

    function loadSettings() {
        $.ajax({
            url: '/api/system-settings', type: 'GET',
            headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
            success: function(response) {
                if (response.status === 'success') {
                    populateFormFields(response.settings);
                } else {
                    if (response.message && response.message === 'You are not authorized to view system settings') {
                        window.location.href = '/' + LOCALE + '/dashboard';
                    }
                }
            },
            error: function(xhr) {
                toastr.error(T.toast_load_failed, T.toast_error_title);
                if (xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message === 'You are not authorized to view system settings') {
                    window.location.href = '/' + LOCALE + '/dashboard';
                }
            }
        });
    }

    function populateFormFields(s) {
        $('#default_essid').val(s.default_essid);
        $('#default_guest_essid').val(s.default_guest_essid);
        $('#default_password').val(s.default_password);
        $('#portal_timeout').val(s.portal_timeout);
        $('#idle_timeout').val(s.idle_timeout);
        $('#bandwidth_limit').val(s.bandwidth_limit);
        $('#user_limit').val(s.user_limit);
        $('#enable_terms').prop('checked', s.enable_terms);
        $('#radius_ip').val(s.radius_ip);
        $('#radius_port').val(s.radius_port);
        $('#radius_secret').val(s.radius_secret);
        $('#accounting_port').val(s.accounting_port);
        $('#company_name').val(s.company_name);
        $('#company_website').val(s.company_website);
        $('#contact_email').val(s.contact_email);
        $('#support_phone').val(s.support_phone);
        if (s.primary_color) {
            $('#primary_color').val(s.primary_color);
            $('#primary_color').closest('.input-group').find('.input-group-text').text(s.primary_color);
        }
        if (s.secondary_color) {
            $('#secondary_color').val(s.secondary_color);
            $('#secondary_color').closest('.input-group').find('.input-group-text').text(s.secondary_color);
        }
        if (s.font_family)  $('#font_family').val(s.font_family).trigger('change');
        if (s.portal_theme) $('#portal_theme').val(s.portal_theme).trigger('change');
        $('#smtp_server').val(s.smtp_server);
        $('#smtp_port').val(s.smtp_port);
        $('#sender_email').val(s.sender_email);
        $('#smtp_password').val(s.smtp_password);
    }
</script>
@endpush
