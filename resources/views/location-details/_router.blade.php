<div class="mw-panel" id="ld-panel-router">
                            <!-- WAN -->
                            <div class="content-section wan-section">
                                <div class="wan-head">
                                    <div class="wan-title-row">
                                        <h5 class="section-title">{{ __('location_details.wan_connection') }}</h5>
                                        <span class="wan-type-chip" id="wan-type-display">DHCP</span>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#wan-settings-modal"><i data-feather="edit" class="mr-1"></i>{{ __('location_details.edit_wan_settings') }}</button>
                                </div>
                                <div class="wan-details wan-static-ip-display_div hidden">
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.ip_address') }}</span><span class="interface-value" id="wan-ip-display">-</span></div>
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.subnet_mask') }}</span><span class="interface-value" id="wan-subnet-display">-</span></div>
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.gateway') }}</span><span class="interface-value" id="wan-gateway-display">-</span></div>
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.primary_dns') }}</span><span class="interface-value" id="wan-dns1-display">-</span></div>
                                </div>
                                <div class="wan-details wan-pppoe-display_div hidden">
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.username') }}</span><span class="interface-value" id="wan-pppoe-username">-</span></div>
                                    <div class="wan-detail"><span class="interface-label">{{ __('location_details.service_name') }}</span><span class="interface-value" id="wan-pppoe-service-name">-</span></div>
                                </div>
                            </div>

                            <!-- Radio Settings -->
                            <div class="content-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title mb-0">{{ __('location_details.wifi_radio_channel') }}</h5>
                                    <button class="btn btn-outline-primary btn-sm" id="scan-channels-btn">
                                        <i data-feather="wifi" class="mr-1"></i>{{ __('location_details.scan_button') }}
                                    </button>
                                </div>

                                <div class="alert alert-info py-2 px-3 mb-3" id="scan-status-alert">
                                    <i data-feather="info" class="mr-2" style="width:14px;height:14px;vertical-align:text-bottom;"></i>
                                    <span id="scan-status-text">{{ __('location_details.scan_default_status') }}</span>
                                    <span id="scan-results-inline" style="display:none;">
                                        <span class="ml-3">
                                            <strong>{{ __('location_details.best_2g') }}:</strong> <span id="last-optimal-2g">--</span>
                                            <span class="mx-2">•</span>
                                            <strong>{{ __('location_details.best_5g') }}:</strong> <span id="last-optimal-5g">--</span>
                                        </span>
                                        <button class="btn btn-sm btn-success float-right" id="save-channels-btn">
                                            <i data-feather="check" class="mr-1"></i>{{ __('location_details.apply_optimal') }}
                                        </button>
                                        <small class="d-block mt-1 text-muted" id="last-scan-timestamp"></small>
                                    </span>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="wifi-country">{{ __('location_details.country_region') }}</label>
                                            <select class="form-control" id="wifi-country">
                                                <option value="US" selected>United States (US)</option>
                                                <option value="CA">Canada (CA)</option>
                                                <option value="GB">United Kingdom (GB)</option>
                                                <option value="FR">France (FR)</option>
                                                <option value="DE">Germany (DE)</option>
                                                <option value="IT">Italy (IT)</option>
                                                <option value="ES">Spain (ES)</option>
                                                <option value="AU">Australia (AU)</option>
                                                <option value="JP">Japan (JP)</option>
                                                <option value="CN">China (CN)</option>
                                                <option value="IN">India (IN)</option>
                                                <option value="BR">Brazil (BR)</option>
                                                <option value="ZA">South Africa (ZA)</option>
                                                <option value="AE">United Arab Emirates (AE)</option>
                                                <option value="SG">Singapore (SG)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="radio-band-card" id="radio-band-card-2g">
                                            <div class="radio-band-card-head">
                                                <div class="radio-band-card-title-wrap">
                                                    <i data-feather="wifi" class="mr-2"></i>
                                                    <span class="radio-band-card-title">{{ __('location_details.band_24_title') }}</span>
                                                </div>
                                            </div>
                                            <div class="radio-band-card-body">
                                                <div class="form-group">
                                                    <label for="power-level-2g">{{ __('location_details.band_power') }}</label>
                                                    <select class="form-control" id="power-level-2g">
                                                        <option value="20">Maximum (20 dBm)</option>
                                                        <option value="17">High (17 dBm)</option>
                                                        <option value="15" selected>Medium (15 dBm)</option>
                                                        <option value="12">Low (12 dBm)</option>
                                                        <option value="10">Minimum (10 dBm)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="channel-width-2g">{{ __('location_details.band_channel_width') }}</label>
                                                    <select class="form-control" id="channel-width-2g"><option value="20">20 MHz</option><option value="40" selected>40 MHz</option></select>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label for="channel-2g">{{ __('location_details.band_channel') }}</label>
                                                    <select class="form-control" id="channel-2g">
                                                        <option value="1">Ch 1 (2412)</option><option value="2">Ch 2</option><option value="3">Ch 3</option><option value="4">Ch 4</option><option value="5">Ch 5</option>
                                                        <option value="6" selected>Ch 6 (2437)</option><option value="7">Ch 7</option><option value="8">Ch 8</option><option value="9">Ch 9</option><option value="10">Ch 10</option>
                                                        <option value="11">Ch 11</option><option value="12">Ch 12</option><option value="13">Ch 13</option><option value="14">Ch 14 (2484)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="radio-band-card" id="radio-band-card-5g">
                                            <div class="radio-band-card-head">
                                                <div class="radio-band-card-title-wrap">
                                                    <i data-feather="wifi" class="mr-2"></i>
                                                    <span class="radio-band-card-title">{{ __('location_details.band_5_title') }}</span>
                                                </div>
                                            </div>
                                            <div class="radio-band-card-body">
                                                <div class="form-group">
                                                    <label for="power-level-5g">{{ __('location_details.band_power') }}</label>
                                                    <select class="form-control" id="power-level-5g">
                                                        <option value="23">Maximum (23 dBm)</option>
                                                        <option value="20">High (20 dBm)</option>
                                                        <option value="17" selected>Medium (17 dBm)</option>
                                                        <option value="14">Low (14 dBm)</option>
                                                        <option value="10">Minimum (10 dBm)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="channel-width-5g">{{ __('location_details.band_channel_width') }}</label>
                                                    <select class="form-control" id="channel-width-5g"><option value="20">20 MHz</option><option value="40">40 MHz</option><option value="80" selected>80 MHz</option><option value="160">160 MHz</option></select>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label for="channel-5g">{{ __('location_details.band_channel') }}</label>
                                                    <select class="form-control" id="channel-5g">
                                                        <option value="36" selected>Ch 36</option><option value="40">Ch 40</option><option value="44">Ch 44</option><option value="48">Ch 48</option>
                                                        <option value="52">Ch 52</option><option value="56">Ch 56</option><option value="60">Ch 60</option><option value="64">Ch 64</option>
                                                        <option value="100">Ch 100</option><option value="104">Ch 104</option><option value="108">Ch 108</option><option value="112">Ch 112</option>
                                                        <option value="116">Ch 116</option><option value="120">Ch 120</option><option value="124">Ch 124</option><option value="128">Ch 128</option>
                                                        <option value="132">Ch 132</option><option value="136">Ch 136</option><option value="140">Ch 140</option><option value="149">Ch 149</option>
                                                        <option value="153">Ch 153</option><option value="157">Ch 157</option><option value="161">Ch 161</option><option value="165">Ch 165</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button class="btn btn-primary" id="save-radio-settings"><i data-feather="save" class="mr-2"></i>{{ __('location_details.save_all_radio') }}</button>
                                </div>
                            </div>

                            <!-- VLAN Support -->
                            <div class="content-section">
                                <div class="section-header">
                                    <h5 class="section-title">{{ __('location_details.vlan_section_title') }}</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-0">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="mb-0" for="router-vlan-enabled">{{ __('location_details.vlan_enabled_label') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="router-vlan-enabled">
                                                    <label class="custom-control-label" for="router-vlan-enabled"></label>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ __('location_details.vlan_enabled_hint') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Traffic Prioritization (QoS) -->
                            <div class="content-section" id="qos-settings-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title">{{ __('location_details.qos_title') }}</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="save-qos-settings"><i data-feather="save" class="mr-1"></i>{{ __('location_details.save_qos') }}</button>
                                </div>

                                <div id="zone-qos-notice" class="alert alert-info py-2 px-3 mb-3" style="display:none;">
                                    <i data-feather="layers" class="mr-50" style="width:16px;height:16px;vertical-align:text-bottom;"></i>
                                    {{ __('location_details.qos_zone_notice') }}
                                </div>

                                <div class="loc-panel panel-qos mb-0">
                                    <div class="loc-panel-header">
                                        <span class="loc-panel-icon">
                                            <i data-feather="git-merge" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                                        </span>
                                        <h6 class="loc-panel-title">{{ __('location_details.qos_classification') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">

                                        {{-- Enable toggle + class preview (always visible) --}}
                                        <div class="row align-items-start mb-3">
                                            <div class="col-md-7">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="mb-0 font-weight-bold">{{ __('location_details.qos_enable') }}</span>
                                                    <div class="custom-control custom-switch custom-control-primary">
                                                        <input type="checkbox" class="custom-control-input" id="qos-enabled">
                                                        <label class="custom-control-label" for="qos-enabled"></label>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0 small">{{ __('location_details.qos_enable_help') }}</p>
                                            </div>
                                            <div class="col-md-5 mt-3 mt-md-0 qos-classify-preview-col">
                                                <div class="panel-sub-label mb-2">{{ __('location_details.qos_active_classes') }}</div>
                                                <div id="qos-classes-preview" class="pt-0">
                                                    <span class="text-muted small">{{ __('common.loading') }}</span>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">{{ __('location_details.qos_managed_globally') }}</small>
                                            </div>
                                        </div>

                                        {{-- Inner tabs --}}
                                        <ul class="nav nav-tabs" id="qos-inner-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="qos-tab-bw-link" data-toggle="tab" href="#qos-pane-bw" role="tab">
                                                    <i data-feather="bar-chart-2" style="width:13px;height:13px;vertical-align:text-bottom;" class="mr-1"></i>{{ __('location_details.qos_tab_bandwidth') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="qos-tab-sni-link" data-toggle="tab" href="#qos-pane-sni" role="tab">
                                                    <i data-feather="tag" style="width:13px;height:13px;vertical-align:text-bottom;" class="mr-1"></i>{{ __('location_details.qos_tab_sni') }}
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content pt-3">
                                            {{-- Tab 1: Bandwidth Limits --}}
                                            <div class="tab-pane fade show active" id="qos-pane-bw" role="tabpanel">
                                                <p class="small text-muted mb-3 mb-md-2">{!! __('location_details.qos_bandwidth_intro') !!}</p>

                                                <div id="qos-wan-override-group" class="rounded border px-3 py-2 mb-3 bg-light" style="display:none;">
                                                    <div class="custom-control custom-checkbox mb-0">
                                                        <input type="checkbox" class="custom-control-input" id="qos-wan-use-local">
                                                        <label class="custom-control-label" for="qos-wan-use-local">{{ __('location_details.qos_wan_use_local') }}</label>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('location_details.qos_wan_use_local_help') }}</small>
                                                </div>

                                                <div class="row qos-bw-split-row align-items-start">
                                                    <div class="col-12 col-md-6">
                                                        <div class="panel-sub-label">{{ __('location_details.qos_wan_capacity') }}</div>
                                                        <div class="mb-2">
                                                            <label class="small text-muted mb-1 d-block" for="qos-wan-down-kbps">{{ __('location_details.stat_download') }}</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-wan-input" id="qos-wan-down-kbps" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="small text-muted mb-1 d-block" for="qos-wan-up-kbps">{{ __('location_details.stat_upload') }}</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-wan-input" id="qos-wan-up-kbps" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 qos-bw-min-col">
                                                        <div class="panel-sub-label">{{ __('location_details.qos_min_per_class') }}</div>
                                                        <small class="text-muted d-block mb-2" style="font-size:0.8rem;">{{ __('location_details.qos_min_per_class_help') }}</small>
                                                        <div class="mb-2">
                                                            <label class="small d-block mb-1" for="qos-voip-bw">{{ __('location_details.qos_voip') }} <span class="text-muted">(EF)</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-voip-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="small d-block mb-1" for="qos-streaming-bw">{{ __('location_details.qos_streaming') }} <span class="text-muted">(AF41)</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-streaming-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="small d-block mb-1" for="qos-be-bw">{{ __('location_details.qos_best_effort') }} <span class="text-muted">(BE)</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-be-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="small d-block mb-1" for="qos-bulk-bw">{{ __('location_details.qos_bulk') }} <span class="text-muted">(CS1)</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" class="form-control qos-bw-input qos-bw-class-input" id="qos-bulk-bw" min="0" max="10000" step="0.001" inputmode="decimal" placeholder="0">
                                                                <div class="input-group-append"><span class="input-group-text">Mbps</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tab 2: Traffic Classification (SNI) --}}
                                            <div class="tab-pane fade" id="qos-pane-sni" role="tabpanel">
                                                <p class="small text-muted mb-3">{{ __('location_details.router_qos_domains_help') }}</p>
                                                @foreach(['EF' => 'router_qos_domain_class_ef', 'AF41' => 'router_qos_domain_class_af41', 'CS1' => 'router_qos_domain_class_cs1'] as $qclass => $labelKey)
                                                    <div class="ld-router-qos-class-block mb-3" data-qos-class="{{ $qclass }}">
                                                        <div class="small font-weight-bold mb-1">{{ __('location_details.' . $labelKey) }}</div>
                                                        <ul class="list-unstyled small mb-2" id="ld-router-qos-list-{{ $qclass }}"></ul>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control" id="ld-router-qos-input-{{ $qclass }}" maxlength="253" placeholder="{{ __('location_details.networks_qos_domain_placeholder') }}" data-qos-class="{{ $qclass }}" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-primary ld-router-qos-add-btn" data-qos-class="{{ $qclass }}">{{ __('location_details.networks_qos_domain_add') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>{{-- /.loc-panel-body --}}
                                </div>{{-- /.loc-panel --}}
                            </div>{{-- /#qos-settings-section --}}

                            <!-- Web Filter -->
                            <div class="content-section">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title">{{ __('location_details.web_content_filtering') }}</h5>
                                    <button class="btn btn-primary" id="save-web-filter-settings"><i data-feather="save" class="mr-2"></i>{{ __('location_details.save_web_filter') }}</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="mb-0">{{ __('location_details.enable_content_filtering') }}</label>
                                                <div class="custom-control custom-switch custom-control-primary">
                                                    <input type="checkbox" class="custom-control-input" id="global-web-filter">
                                                    <label class="custom-control-label" for="global-web-filter"></label>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ __('location_details.web_filter_help') }}</small>
                                            <div id="web-filter-propagation-notice" class="alert alert-warning mt-2 mb-0 py-2 px-3" style="display:none; font-size:0.85rem;">
                                                <i data-feather="clock" style="width:14px;height:14px;vertical-align:middle;" class="mr-1"></i>
                                                {!! __('location_details.web_filter_propagation') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="global-filter-categories">{{ __('location_details.block_categories') }}</label>
                                            <select class="select2 form-control" id="global-filter-categories" multiple="multiple"></select>
                                            <small class="text-muted">{{ __('location_details.block_categories_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" id="wan-dns-row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label for="wan-dns1">{{ __('location_details.wan_primary_dns') }}</label>
                                            <input type="text" class="form-control" id="wan-dns1" placeholder="8.8.8.8">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label for="wan-dns2">{{ __('location_details.wan_secondary_dns') }} <small class="text-muted font-weight-normal">{{ __('location_details.description_optional') }}</small></label>
                                            <input type="text" class="form-control" id="wan-dns2" placeholder="8.8.4.4">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <small class="text-muted" id="wan-dns-hint">{{ __('location_details.wan_dns_hint') }}</small>
                                    </div>
                                </div>
                            </div>
                        <!-- end router content -->

</div><!-- /ld-panel-router -->
