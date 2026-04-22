<div class="ld-panel" id="ld-panel-networks">

    <div class="card ld-networks-card">
        <div class="card-header ld-networks-header">
            <div class="ld-networks-header-text">
                <h4 class="ld-networks-title">{{ __('location_details.networks_card_title') }}</h4>
                <div class="ld-networks-subtitle">{{ __('location_details.networks_card_subtitle') }}</div>
            </div>
            <button type="button" id="ld-networks-add-btn" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="mr-1"></i>{{ __('location_details.networks_add') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div id="ld-networks-loading" class="ld-networks-empty-state">{{ __('common.loading') }}</div>
            <div id="ld-networks-empty" class="ld-networks-empty-state" style="display:none;">{{ __('location_details.networks_empty') }}</div>
            <div id="ld-networks-error" class="ld-networks-empty-state ld-networks-error" style="display:none;">{{ __('location_details.networks_load_error') }}</div>
            <ul id="ld-networks-list" class="ld-networks-list list-unstyled mb-0" style="display:none;"></ul>
        </div>
    </div>

    <template id="ld-network-row-tpl">
        <li class="ld-network-row">
            <div class="ld-net-icon"><i data-feather="wifi"></i></div>
            <div class="ld-net-body">
                <div class="ld-net-name-row">
                    <span class="ld-net-name"></span>
                    <span class="ld-net-type-badge"></span>
                    <span class="ld-net-status-badge"></span>
                </div>
                <div class="ld-net-meta">
                    <span class="ld-net-band"></span>
                    <span class="ld-net-vlan" style="display:none;"></span>
                </div>
            </div>
            <i data-feather="chevron-right" class="ld-net-chevron"></i>
        </li>
    </template>

    <aside class="mw-drawer" id="ld-network-drawer" role="dialog" aria-modal="true" aria-labelledby="ld-network-drawer-title">
        <header class="mw-drawer-header">
            <h5 class="mw-drawer-title" id="ld-network-drawer-title"></h5>
            <button type="button" class="mw-drawer-close" aria-label="{{ __('common.close') }}" data-mw-drawer-close>&times;</button>
        </header>
        <div class="mw-drawer-body" id="ld-network-drawer-body">
            <form id="ld-network-drawer-form" novalidate>

                <div class="ld-drawer-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_details.networks_section_identity') }}</h6>

                    <div class="form-group">
                        <label for="ld-net-type">{{ __('location_details.networks_field_type') }}</label>
                        <select class="form-control" id="ld-net-type">
                            <option value="password">{{ __('location_details.networks_type_password') }}</option>
                            <option value="captive_portal">{{ __('location_details.networks_type_captive_portal') }}</option>
                            <option value="open">{{ __('location_details.networks_type_open') }}</option>
                        </select>
                        <small class="form-text text-warning" data-show-for-type="captive_portal">{{ __('location_details.networks_type_warning') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-ssid">{{ __('location_details.networks_field_ssid') }}</label>
                        <input type="text" class="form-control" id="ld-net-ssid" maxlength="32">
                    </div>

                    <div class="form-group">
                        <label for="ld-net-visible">{{ __('location_details.networks_field_visibility') }}</label>
                        <select class="form-control" id="ld-net-visible">
                            <option value="1">{{ __('location_details.networks_visibility_broadcast') }}</option>
                            <option value="0">{{ __('location_details.networks_visibility_hidden') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-radio">{{ __('location_details.networks_field_radio_band') }}</label>
                        <select class="form-control" id="ld-net-radio">
                            <option value="all">{{ __('location_details.networks_band_both') }}</option>
                            <option value="2.4">{{ __('location_details.networks_band_24') }}</option>
                            <option value="5">{{ __('location_details.networks_band_5') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-enabled">
                            <label class="custom-control-label" for="ld-net-enabled">{{ __('location_details.networks_field_enabled') }}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-qos">
                            <label class="custom-control-label" for="ld-net-qos">{{ __('location_details.networks_field_qos_full') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('location_details.networks_field_qos_hint') }}</small>
                    </div>
                </div>

                <div class="ld-drawer-section" data-show-for-type="captive_portal">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.panel_captive_portal_config') }}</h6>

                    <div class="form-group">
                        <label>{{ __('location_networks.login_methods') }}</label>
                        <div class="ld-net-auth-methods">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input ld-net-auth-method" id="ld-net-auth-click-through" value="click-through">
                                <label class="custom-control-label" for="ld-net-auth-click-through">{{ __('location_networks.method_click_through') }}</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input ld-net-auth-method" id="ld-net-auth-password" value="password">
                                <label class="custom-control-label" for="ld-net-auth-password">{{ __('location_networks.method_password') }}</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input ld-net-auth-method" id="ld-net-auth-sms" value="sms">
                                <label class="custom-control-label" for="ld-net-auth-sms">{{ __('location_networks.method_sms') }}</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input ld-net-auth-method" id="ld-net-auth-email" value="email">
                                <label class="custom-control-label" for="ld-net-auth-email">{{ __('location_networks.method_email') }}</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input ld-net-auth-method" id="ld-net-auth-social" value="social">
                                <label class="custom-control-label" for="ld-net-auth-social">{{ __('location_networks.method_social') }}</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('location_networks.multiple_methods_hint') }}</small>
                    </div>

                    <div class="form-group" id="ld-net-portal-pwd-group" style="display:none;">
                        <label for="ld-net-portal-password">{{ __('location_networks.shared_password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="ld-net-portal-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="ld-net-portal-password-toggle" aria-label="{{ __('location_details.networks_password_toggle') }}">
                                    <i data-feather="eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="ld-net-email-otp-group" style="display:none;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-email-otp">
                            <label class="custom-control-label" for="ld-net-email-otp">{{ __('location_details.networks_email_otp') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('location_details.networks_email_otp_hint') }}</small>
                    </div>

                    <div class="form-group" id="ld-net-social-group" style="display:none;">
                        <label for="ld-net-social">{{ __('location_networks.social_provider') }}</label>
                        <select class="form-control" id="ld-net-social">
                            <option value="facebook">Facebook</option>
                            <option value="google">Google</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-portal-design">{{ __('location_networks.portal_design') }}</label>
                        <select class="form-control" id="ld-net-portal-design">
                            <option value="">{{ __('location_networks.default_design') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-redirect-url">{{ __('location_networks.redirect_url') }} <small class="text-muted font-weight-normal">{{ __('common.optional') }}</small></label>
                        <input type="url" class="form-control" id="ld-net-redirect-url" placeholder="{{ __('location_networks.redirect_url_placeholder') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-session-timeout">{{ __('location_networks.session_timeout') }}</label>
                                <select class="form-control" id="ld-net-session-timeout">
                                    <option value="60">{{ __('location_networks.dur_1_hour') }}</option>
                                    <option value="120">{{ __('location_networks.dur_2_hours') }}</option>
                                    <option value="180">{{ __('location_networks.dur_3_hours') }}</option>
                                    <option value="240">{{ __('location_networks.dur_4_hours') }}</option>
                                    <option value="300">{{ __('location_networks.dur_5_hours') }}</option>
                                    <option value="360">{{ __('location_networks.dur_6_hours') }}</option>
                                    <option value="720">{{ __('location_networks.dur_12_hours') }}</option>
                                    <option value="1440">{{ __('location_networks.dur_1_day') }}</option>
                                    <option value="10080">{{ __('location_networks.dur_1_week') }}</option>
                                    <option value="43200">{{ __('location_networks.dur_3_months') }}</option>
                                    <option value="172800">{{ __('location_networks.dur_1_year') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-idle-timeout">{{ __('location_networks.idle_timeout') }}</label>
                                <select class="form-control" id="ld-net-idle-timeout">
                                    <option value="15">{{ __('location_networks.dur_15_min') }}</option>
                                    <option value="30">{{ __('location_networks.dur_30_min') }}</option>
                                    <option value="45">{{ __('location_networks.dur_45_min') }}</option>
                                    <option value="60">{{ __('location_networks.dur_1_hour') }}</option>
                                    <option value="120">{{ __('location_networks.dur_2_hours') }}</option>
                                    <option value="240">{{ __('location_networks.dur_4_hours') }}</option>
                                    <option value="720">{{ __('location_networks.dur_12_hours') }}</option>
                                    <option value="1440">{{ __('location_networks.dur_1_day') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-download-limit">{{ __('location_networks.download_mbps') }}</label>
                                <input type="number" class="form-control" id="ld-net-download-limit" placeholder="{{ __('location_networks.unlimited') }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-upload-limit">{{ __('location_networks.upload_mbps') }}</label>
                                <input type="number" class="form-control" id="ld-net-upload-limit" placeholder="{{ __('location_networks.unlimited') }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ld-drawer-section" data-show-for-type="password">
                    <h6 class="ld-drawer-section-title">{{ __('location_details.networks_section_security') }}</h6>

                    <div class="form-group">
                        <label for="ld-net-password">{{ __('location_networks.wifi_password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="ld-net-password" placeholder="{{ __('location_networks.wifi_password_placeholder') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="ld-net-password-toggle" aria-label="{{ __('location_details.networks_password_toggle') }}">
                                    <i data-feather="eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-security">{{ __('location_networks.security_protocol') }}</label>
                        <select class="form-control" id="ld-net-security">
                            <option value="wpa2-psk">{{ __('location_networks.security_wpa2_psk_rec') }}</option>
                            <option value="wpa-wpa2-psk">{{ __('location_networks.security_wpa_wpa2_mixed') }}</option>
                            <option value="wpa3-psk">{{ __('location_networks.security_wpa3_psk_secure') }}</option>
                            <option value="wep">{{ __('location_networks.security_wep_legacy') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-cipher">{{ __('location_networks.cipher_suites') }}</label>
                        <select class="form-control" id="ld-net-cipher">
                            <option value="CCMP">CCMP</option>
                            <option value="TKIP">TKIP</option>
                            <option value="TKIP+CCMP">TKIP+CCMP</option>
                        </select>
                    </div>
                </div>

            </form>
        </div>
        <footer class="mw-drawer-footer">
            <button type="button" class="btn btn-outline-secondary" data-mw-drawer-close>{{ __('common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="ld-network-drawer-save" disabled>{{ __('location_details.networks_drawer_save') }}</button>
        </footer>
    </aside>

</div>
