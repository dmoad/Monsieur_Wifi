@extends('layouts.app')

@section('title', 'Global Settings - Monsieur WiFi')

@php $locale = 'en'; @endphp

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-validation.css">
<style>
    .setting-section {
        padding: 1.5rem;
        border-radius: 0.428rem;
        border: 1px solid #ebe9f1;
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
                <h2 class="content-header-title float-left mb-0">Global Settings</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="settings-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="captive-portal-tab" data-toggle="pill" href="#captive-portal" role="tab" aria-selected="true">
                                    <i data-feather="wifi" class="mr-50"></i><span class="font-weight-bold">Captive Portal</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="radius-tab" data-toggle="pill" href="#radius" role="tab" aria-selected="false">
                                    <i data-feather="shield" class="mr-50"></i><span class="font-weight-bold">RADIUS Configuration</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="branding-tab" data-toggle="pill" href="#branding" role="tab" aria-selected="false">
                                    <i data-feather="image" class="mr-50"></i><span class="font-weight-bold">Branding</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" role="tab" aria-selected="false">
                                    <i data-feather="server" class="mr-50"></i><span class="font-weight-bold">System</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Captive Portal Tab -->
                            <div role="tabpanel" class="tab-pane active" id="captive-portal" aria-labelledby="captive-portal-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="wifi"></i></div></div>
                                            <h3 class="setting-section-title">Default WiFi Settings</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_essid">Default ESSID</label>
                                                    <input type="text" id="default_essid" class="form-control" name="default_essid" placeholder="MrWiFi-Guest" />
                                                    <small>This ESSID will be used as default for all new access points</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_guest_essid">Default Guest ESSID</label>
                                                    <input type="text" id="default_guest_essid" class="form-control" name="default_guest_essid" placeholder="MrWiFi-Guest" />
                                                    <small>This ESSID will be used as default for guest networks</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="default_password">Default Password</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="default_password" class="form-control" name="default_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Default password for new access points (minimum 8 characters)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-info p-50 mr-1"><div class="avatar-content"><i data-feather="layout"></i></div></div>
                                            <h3 class="setting-section-title">Captive Portal Behavior</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_timeout">Default Session Timeout</label>
                                                    <div class="input-group">
                                                        <input type="number" id="portal_timeout" class="form-control" name="portal_timeout" min="1" max="168" />
                                                        <div class="input-group-append"><span class="input-group-text">Hours</span></div>
                                                    </div>
                                                    <small>How long users stay authenticated before needing to log in again</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="idle_timeout">Default Idle Timeout</label>
                                                    <div class="input-group">
                                                        <input type="number" id="idle_timeout" class="form-control" name="idle_timeout" min="5" max="180" />
                                                        <div class="input-group-append"><span class="input-group-text">Minutes</span></div>
                                                    </div>
                                                    <small>Disconnect inactive users after this period</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="bandwidth_limit">Default Bandwidth Limit</label>
                                                    <div class="input-group">
                                                        <input type="number" id="bandwidth_limit" class="form-control" name="bandwidth_limit" min="1" max="1000" />
                                                        <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                    </div>
                                                    <small>Default bandwidth limit per user</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="user_limit">Default Maximum Users</label>
                                                    <input type="number" id="user_limit" class="form-control" name="user_limit" min="1" max="500" />
                                                    <small>Maximum concurrent users per access point</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" id="enable_terms" name="enable_terms" />
                                                        <label class="custom-control-label" for="enable_terms">Display Terms &amp; Conditions</label>
                                                    </div>
                                                    <small>Require acceptance of Terms &amp; Conditions before connecting</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- RADIUS Tab -->
                            <div class="tab-pane" id="radius" role="tabpanel" aria-labelledby="radius-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="shield"></i></div></div>
                                            <h3 class="setting-section-title">Primary RADIUS Server</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_ip">Server IP Address</label>
                                                    <input type="text" id="radius_ip" class="form-control" name="radius_ip" placeholder="192.168.1.100" />
                                                    <small>IP address of your primary RADIUS server</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_port">Authentication Port</label>
                                                    <input type="number" id="radius_port" class="form-control" name="radius_port" placeholder="1812" min="1" max="65535" />
                                                    <small>Port used for RADIUS authentication (default: 1812)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="radius_secret">Shared Secret</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="radius_secret" class="form-control" name="radius_secret" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Shared secret for RADIUS authentication</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="accounting_port">Accounting Port</label>
                                                    <input type="number" id="accounting_port" class="form-control" name="accounting_port" placeholder="1813" min="1" max="65535" />
                                                    <small>Port used for RADIUS accounting (default: 1813)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Branding Tab -->
                            <div class="tab-pane" id="branding" role="tabpanel" aria-labelledby="branding-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-primary p-50 mr-1"><div class="avatar-content"><i data-feather="type"></i></div></div>
                                            <h3 class="setting-section-title">Company Information</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_name">Company Name</label>
                                                    <input type="text" id="company_name" class="form-control" name="company_name" placeholder="monsieur-wifi" />
                                                    <small>Your company name as displayed on the captive portal</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_website">Company Website</label>
                                                    <input type="url" id="company_website" class="form-control" name="company_website" placeholder="https://www.example.com" />
                                                    <small>Your company website URL</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="contact_email">Contact Email</label>
                                                    <input type="email" id="contact_email" class="form-control" name="contact_email" placeholder="support@example.com" />
                                                    <small>Contact email displayed on the captive portal</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="support_phone">Support Phone</label>
                                                    <input type="tel" id="support_phone" class="form-control" name="support_phone" placeholder="+1 (555) 123-4567" />
                                                    <small>Support phone number displayed on the captive portal</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-info p-50 mr-1"><div class="avatar-content"><i data-feather="image"></i></div></div>
                                            <h3 class="setting-section-title">Logo &amp; Images</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="logo-upload">Company Logo</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="logo-upload" accept="image/*" />
                                                        <label class="custom-file-label" for="logo-upload">Choose file</label>
                                                    </div>
                                                    <small>Recommended size: 300px x 100px (PNG or SVG with transparency)</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Current Logo</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Current logo" height="50" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="favicon-upload">Favicon</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="favicon-upload" accept="image/x-icon,image/png,image/gif" />
                                                        <label class="custom-file-label" for="favicon-upload">Choose file</label>
                                                    </div>
                                                    <small>Recommended size: 32px x 32px (ICO, PNG, or GIF)</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Current Favicon</label>
                                                    <div class="d-flex justify-content-center p-2 border rounded mb-1">
                                                        <img src="/app-assets/mrwifi-assets/MrWifi.png" alt="Current favicon" height="32" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="splash-background">Captive Portal Background</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="splash-background" accept="image/*" />
                                                        <label class="custom-file-label" for="splash-background">Choose file</label>
                                                    </div>
                                                    <small>Recommended size: 1920px x 1080px (JPG or PNG)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-warning p-50 mr-1"><div class="avatar-content"><i data-feather="layers"></i></div></div>
                                            <h3 class="setting-section-title">Portal Customization</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="primary_color">Primary Color</label>
                                                    <div class="input-group">
                                                        <input type="color" id="primary_color" class="form-control" name="primary_color" value="#7367f0" />
                                                        <div class="input-group-append"><span class="input-group-text">#7367f0</span></div>
                                                    </div>
                                                    <small>Main color for buttons and highlights</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="secondary_color">Secondary Color</label>
                                                    <div class="input-group">
                                                        <input type="color" id="secondary_color" class="form-control" name="secondary_color" value="#82868b" />
                                                        <div class="input-group-append"><span class="input-group-text">#82868b</span></div>
                                                    </div>
                                                    <small>Secondary color for accents and alternate elements</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="font_family">Primary Font</label>
                                                    <select id="font_family" class="form-control" name="font_family">
                                                        <option value="montserrat" selected>Montserrat</option>
                                                        <option value="roboto">Roboto</option>
                                                        <option value="open-sans">Open Sans</option>
                                                        <option value="lato">Lato</option>
                                                        <option value="poppins">Poppins</option>
                                                    </select>
                                                    <small>Font family used throughout the portal</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="portal_theme">Portal Theme</label>
                                                    <select id="portal_theme" class="form-control" name="portal_theme">
                                                        <option value="light" selected>Light</option>
                                                        <option value="dark">Dark</option>
                                                        <option value="auto">Auto (system preference)</option>
                                                    </select>
                                                    <small>Default theme for the captive portal</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- System Tab -->
                            <div class="tab-pane" id="system" role="tabpanel" aria-labelledby="system-tab">
                                <form class="validate-form">
                                    <div class="setting-section">
                                        <div class="setting-section-header">
                                            <div class="avatar bg-light-warning p-50 mr-1"><div class="avatar-content"><i data-feather="mail"></i></div></div>
                                            <h3 class="setting-section-title">Email Configuration</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_server">SMTP Server</label>
                                                    <input type="text" id="smtp_server" class="form-control" name="smtp_server" placeholder="smtp.example.com" />
                                                    <small>SMTP server for sending email notifications</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_port">SMTP Port</label>
                                                    <input type="number" id="smtp_port" class="form-control" name="smtp_port" placeholder="587" min="1" max="65535" />
                                                    <small>Port for SMTP server connection</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="sender_email">Sender Email</label>
                                                    <input type="email" id="sender_email" class="form-control" name="sender_email" placeholder="notifications@example.com" />
                                                    <small>Email address that notifications come from</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="smtp_password">SMTP Password</label>
                                                    <div class="input-group input-group-merge form-password-toggle">
                                                        <input type="password" id="smtp_password" class="form-control" name="smtp_password" placeholder="············" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    <small>Password for authenticating with SMTP server</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" id="send-test-email" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="send" class="mr-25"></i><span>Send Test Email</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
<script src="/app-assets/js/scripts/forms/form-validation.js"></script>

<script>
    $(window).on('load', function() {
        if ($.fn.select2) {
            $('#font_family, #portal_theme').select2({ minimumResultsForSearch: Infinity });
        }

        $('.custom-file-input').on('change', function() {
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || 'Choose file');
        });

        $('#primary_color, #secondary_color').on('input change', function() {
            $(this).closest('.input-group').find('.input-group-text').text($(this).val());
        });

        $('.form-password-toggle .input-group-text').on('click', function(e) {
            e.preventDefault();
            var passwordInput = $(this).closest('.form-password-toggle').find('input');
            if (passwordInput.attr('type') === 'text') {
                passwordInput.attr('type', 'password');
                $(this).find('svg').replaceWith(feather.icons['eye'].toSvg());
            } else {
                passwordInput.attr('type', 'text');
                $(this).find('svg').replaceWith(feather.icons['eye-off'].toSvg());
            }
        });

        var hash = window.location.hash;
        if (hash) $('.nav-pills a[href="' + hash + '"]').tab('show');

        $('.nav-pills a').on('shown.bs.tab', function(e) {
            if (history.pushState) history.pushState(null, null, e.target.hash);
            else window.location.hash = e.target.hash;
        });

        loadSettings();
    });

    $(document).ready(function() {
        $('.validate-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            var tabId = form.closest('.tab-pane').attr('id');

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').attr('disabled', true);

            var formData = new FormData();
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
                    toastr.success('Your settings have been saved successfully.', 'Settings Saved');
                    if (response.settings) populateFormFields(response.settings);
                    submitBtn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : (xhr.responseJSON?.message || 'Failed to save settings.');
                    toastr.error(msg, 'Error');
                    submitBtn.html(originalText).attr('disabled', false);
                }
            });
        });

        $('#send-test-email').on('click', function() {
            var btn = $(this);
            var originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...').attr('disabled', true);
            $.ajax({
                url: '/api/system-settings/test-email', type: 'POST',
                headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                data: JSON.stringify({ email: $('#sender_email').val() }),
                success: function() {
                    toastr.info('Test email has been sent to ' + $('#sender_email').val(), 'Email Sent');
                    btn.html(originalText).attr('disabled', false);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to send test email. Please check your SMTP settings.', 'Error');
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
                        window.location.href = '/en/dashboard';
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Failed to load settings. Please try again.', 'Error');
                if (xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message === 'You are not authorized to view system settings') {
                    window.location.href = '/en/dashboard';
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
