<div class="mw-panel" id="ld-panel-networks">

    <div id="ld-networks-zone-notice" class="alert alert-info py-2 px-3 mb-3" style="display:none;">
        <i data-feather="layers" class="mr-50" style="width:16px;height:16px;vertical-align:text-bottom;"></i>
        {{ __('location_details.zone_networks_notice') }}
    </div>

    <div class="card ld-networks-card">
        <div class="card-header ld-networks-header">
            <div class="ld-networks-header-text">
                <h4 class="ld-networks-title">{{ __('location_details.networks_card_title') }}</h4>
                <div class="ld-networks-subtitle">{{ __('location_details.networks_card_subtitle') }}</div>
            </div>
            <button type="button" id="ld-networks-add-btn" class="btn btn-primary btn-sm" disabled>
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
                        <label for="ld-net-ssid">{{ __('location_details.networks_field_ssid') }}</label>
                        <input type="text" class="form-control" id="ld-net-ssid" maxlength="32">
                    </div>

                    <div class="form-group">
                        <label for="ld-net-type">{{ __('location_details.networks_field_type') }}</label>
                        <select class="form-control" id="ld-net-type">
                            <option value="password">{{ __('location_details.networks_type_password') }}</option>
                            <option value="captive_portal">{{ __('location_details.networks_type_captive_portal') }}</option>
                            <option value="open">{{ __('location_details.networks_type_open') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-enabled">
                            <label class="custom-control-label" for="ld-net-enabled">{{ __('location_details.networks_field_enabled') }}</label>
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

                <div class="ld-drawer-section ld-net-schedule-section" data-show-for-type="captive_portal">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.working_hours') }}</h6>

                    <div class="ld-net-schedule-modes">
                        <label class="ld-net-schedule-mode-label">
                            <input type="radio" name="ld-net-schedule-mode" value="always" class="ld-net-schedule-mode">
                            <span class="ld-net-schedule-mode-text">
                                <span class="ld-net-schedule-mode-title">{{ __('location_details.networks_schedule_always') }}</span>
                                <span class="ld-net-schedule-mode-desc">{{ __('location_details.networks_schedule_always_desc') }}</span>
                            </span>
                        </label>
                        <label class="ld-net-schedule-mode-label">
                            <input type="radio" name="ld-net-schedule-mode" value="restricted" class="ld-net-schedule-mode">
                            <span class="ld-net-schedule-mode-text">
                                <span class="ld-net-schedule-mode-title">{{ __('location_details.networks_schedule_restricted') }}</span>
                                <span class="ld-net-schedule-mode-desc">{{ __('location_details.networks_schedule_restricted_desc') }}</span>
                            </span>
                        </label>
                    </div>

                    <div class="ld-net-schedule-editor" id="ld-net-schedule-editor" style="display:none;"></div>

                    <template id="ld-net-schedule-day-tpl">
                        <div class="ld-net-schedule-day">
                            <label class="ld-net-schedule-day-toggle">
                                <input type="checkbox" class="ld-net-schedule-day-enabled">
                                <span class="ld-net-schedule-day-name"></span>
                            </label>
                            <div class="ld-net-schedule-day-ranges"></div>
                            <button type="button" class="btn btn-sm btn-link ld-net-schedule-day-add" style="display:none;">
                                <i data-feather="plus"></i> {{ __('location_details.networks_schedule_add_range') }}
                            </button>
                        </div>
                    </template>

                    <template id="ld-net-schedule-range-tpl">
                        <div class="ld-net-schedule-range">
                            <select class="form-control form-control-sm ld-net-schedule-range-start"></select>
                            <span class="ld-net-schedule-range-sep">→</span>
                            <select class="form-control form-control-sm ld-net-schedule-range-end"></select>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 ld-net-schedule-range-remove" aria-label="Remove">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="ld-drawer-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.panel_ip_config') }}</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ld-net-ip-mode">{{ __('location_networks.ip_mode') }}</label>
                                <select class="form-control" id="ld-net-ip-mode">
                                    <option value="static">{{ __('location_networks.ip_mode_static') }}</option>
                                    <option value="bridge_lan">{{ __('location_networks.ip_mode_bridge_lan') }}</option>
                                    <option value="bridge">{{ __('location_networks.ip_mode_bridge') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 ld-net-bridge-lan-wrap" style="display:none;">
                            <div class="form-group">
                                <label for="ld-net-bridge-lan-mode">{{ __('location_networks.lan_dhcp_mode') }}</label>
                                <select class="form-control" id="ld-net-bridge-lan-mode">
                                    <option value="dhcp_client">{{ __('location_networks.lan_dhcp_client') }}</option>
                                    <option value="dhcp_server">{{ __('location_networks.lan_dhcp_server') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ld-net-ip-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ld-net-ip-address">{{ __('location_networks.ip_address') }}</label>
                                    <input type="text" class="form-control" id="ld-net-ip-address" placeholder="{{ __('location_networks.ip_address_placeholder') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ld-net-netmask">{{ __('location_networks.netmask') }}</label>
                                    <input type="text" class="form-control" id="ld-net-netmask" placeholder="255.255.255.0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ld-net-gateway">{{ __('location_networks.gateway') }}</label>
                                    <input type="text" class="form-control" id="ld-net-gateway" placeholder="{{ __('location_networks.gateway_placeholder') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ld-net-dns1">{{ __('location_networks.primary_dns') }}</label>
                                    <input type="text" class="form-control" id="ld-net-dns1" placeholder="8.8.8.8">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ld-net-dns2">{{ __('location_networks.alt_dns') }}</label>
                                    <input type="text" class="form-control" id="ld-net-dns2" placeholder="8.8.4.4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ld-drawer-section ld-net-dhcp-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.dhcp_pool_title') }}</h6>
                    <p class="text-muted small mb-3">{{ __('location_networks.dhcp_pool_desc') }}</p>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-dhcp-enabled">
                            <label class="custom-control-label" for="ld-net-dhcp-enabled">{{ __('location_networks.enable_dhcp') }}</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-dhcp-start">{{ __('location_networks.start_ip') }}</label>
                                <input type="text" class="form-control" id="ld-net-dhcp-start" placeholder="{{ __('location_networks.start_ip_placeholder') }}" autocomplete="off">
                                <small class="form-text text-muted">{{ __('location_networks.start_ip_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ld-net-dhcp-end">{{ __('location_networks.pool_size') }}</label>
                                <input type="number" class="form-control" id="ld-net-dhcp-end" placeholder="{{ __('location_networks.pool_size_placeholder') }}" min="1" max="16777216" step="1">
                                <small class="form-text text-muted">{{ __('location_networks.pool_size_hint') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ld-drawer-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.sub_vlan') }}</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ld-net-vlan-id">{{ __('location_networks.vlan_id') }} <small class="text-muted font-weight-normal">{{ __('location_networks.vlan_id_range') }}</small></label>
                                <input type="number" class="form-control" id="ld-net-vlan-id" placeholder="{{ __('location_networks.vlan_none') }}" min="1" max="4094">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ld-net-vlan-tagging">{{ __('location_networks.tagging') }}</label>
                                <select class="form-control" id="ld-net-vlan-tagging">
                                    <option value="disabled">{{ __('location_networks.tagging_disabled') }}</option>
                                    <option value="tagged">{{ __('location_networks.tagging_tagged') }}</option>
                                    <option value="untagged">{{ __('location_networks.tagging_untagged') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="ld-net-vlan-hint" style="display:none;">{{ __('location_details.networks_vlan_disabled_hint') }}</small>
                </div>

                <div class="ld-drawer-section ld-net-mac-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_networks.section_mac_filter_reservations') }}</h6>

                    <div class="ld-net-sub-block">
                        <div class="ld-net-sub-title">{{ __('location_networks.mac_filtering') }}</div>
                        <p class="text-muted small mb-2" id="ld-net-mac-hint"></p>
                        <div class="ld-net-add-row">
                            <input type="text" class="form-control form-control-sm ld-net-add-mac" id="ld-net-mac-input" placeholder="00:11:22:33:44:55">
                            <select class="form-control form-control-sm ld-net-add-select" id="ld-net-mac-type">
                                <option value="block">{{ __('location_networks.mac_add_type_block') }}</option>
                                <option value="bypass">{{ __('location_networks.mac_add_type_bypass') }}</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="ld-net-mac-add">
                                <i data-feather="plus"></i> {{ __('common.add') }}
                            </button>
                        </div>
                        <div class="ld-net-rl-wrap">
                            <table class="ld-net-rl-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('location_networks.table_col_type') }}</th>
                                        <th>{{ __('location_networks.table_col_mac') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ld-net-mac-list">
                                    <tr class="ld-net-rl-empty"><td colspan="3">{{ __('location_networks.mac_list_empty') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="ld-net-sub-block ld-net-reservations-block" style="display:none;">
                        <div class="ld-net-sub-title">{{ __('location_networks.dhcp_reservations') }}</div>
                        <div class="ld-net-add-row">
                            <input type="text" class="form-control form-control-sm ld-net-add-mac" id="ld-net-res-mac" placeholder="{{ __('location_networks.reservation_mac_placeholder') }}">
                            <input type="text" class="form-control form-control-sm ld-net-add-ip" id="ld-net-res-ip" placeholder="{{ __('location_networks.reservation_ip_placeholder') }}">
                            <button type="button" class="btn btn-sm btn-outline-info" id="ld-net-res-add">
                                <i data-feather="plus"></i> {{ __('common.add') }}
                            </button>
                        </div>
                        <div class="ld-net-rl-wrap">
                            <table class="ld-net-rl-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('location_networks.table_col_mac') }}</th>
                                        <th>{{ __('location_networks.table_col_reserved_ip') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ld-net-res-list">
                                    <tr class="ld-net-rl-empty"><td colspan="3">{{ __('location_networks.reservation_list_empty') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="ld-drawer-section">
                    <h6 class="ld-drawer-section-title">{{ __('location_details.networks_section_radio') }}</h6>

                    <div class="form-group">
                        <label for="ld-net-radio">{{ __('location_details.networks_field_radio_band') }}</label>
                        <select class="form-control" id="ld-net-radio">
                            <option value="all">{{ __('location_details.networks_band_both') }}</option>
                            <option value="2.4">{{ __('location_details.networks_band_24') }}</option>
                            <option value="5">{{ __('location_details.networks_band_5') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ld-net-visible">{{ __('location_details.networks_field_visibility') }}</label>
                        <select class="form-control" id="ld-net-visible">
                            <option value="1">{{ __('location_details.networks_visibility_broadcast') }}</option>
                            <option value="0">{{ __('location_details.networks_visibility_hidden') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ld-net-qos">
                            <label class="custom-control-label" for="ld-net-qos">{{ __('location_details.networks_field_qos_full') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('location_details.networks_field_qos_hint') }}</small>
                    </div>
                </div>

            </form>
        </div>
        <footer class="mw-drawer-footer">
            <button type="button" class="btn btn-link text-danger mr-auto" id="ld-network-drawer-delete">
                <i data-feather="trash-2" class="mr-1"></i>{{ __('location_details.networks_drawer_delete') }}
            </button>
            <button type="button" class="btn btn-outline-secondary" data-mw-drawer-close>{{ __('common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="ld-network-drawer-save" disabled>{{ __('location_details.networks_drawer_save') }}</button>
        </footer>
    </aside>

</div>
