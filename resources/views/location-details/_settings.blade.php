<div class="ld-panel" id="ld-panel-settings">
                            <form id="location-info-form" novalidate>

                                <!-- Panel 1: Identity & Address -->
                                <div class="loc-panel panel-location">
                                    <div class="loc-panel-header">
                                        <span class="loc-panel-icon">
                                            <i data-feather="map-pin" style="color:var(--mw-primary);width:16px;height:16px;"></i>
                                        </span>
                                        <h6 class="loc-panel-title">{{ __('location_details.panel_identity_address') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">
                                        <div class="panel-sub-label">{{ __('location_details.sublabel_identity') }}</div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="location-name">{{ __('location_details.location_name') }} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="location-name" placeholder="{{ __('location_details.location_name_placeholder') }}" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group admin-only-field" style="display:none;">
                                                    <label for="router-model-select">{{ __('location_details.router_model') }}</label>
                                                    <select class="form-control" id="router-model-select">
                                                        <option value="">{{ __('common.loading') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="location-status">{{ __('location_details.status_label') }}</label>
                                                    <select class="form-control" id="location-status">
                                                        <option value="active">{{ __('common.active') }}</option>
                                                        <option value="inactive">{{ __('common.inactive') }}</option>
                                                        <option value="maintenance">{{ __('common.maintenance') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel-sub-section">
                                            <div class="panel-sub-label">{{ __('location_details.sublabel_address') }}</div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="location-address">{{ __('location_details.street_address') }}</label>
                                                        <input type="text" class="form-control" id="location-address" placeholder="{{ __('location_details.street_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="location-city">{{ __('location_details.city') }}</label>
                                                        <input type="text" class="form-control" id="location-city" placeholder="{{ __('location_details.city_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="location-state">{{ __('location_details.state_province') }}</label>
                                                        <input type="text" class="form-control" id="location-state" placeholder="{{ __('location_details.state_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="location-postal-code">{{ __('location_details.postal') }}</label>
                                                        <input type="text" class="form-control" id="location-postal-code" placeholder="{{ __('location_details.postal_placeholder') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="location-country">{{ __('location_details.country') }}</label>
                                                        <input type="text" class="form-control" id="location-country" placeholder="{{ __('location_details.country_placeholder') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel-sub-section">
                                            <div class="panel-sub-label">{{ __('location_details.sublabel_notes') }}</div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="location-description">{{ __('location_details.description_label') }} <small class="text-muted font-weight-normal">{{ __('location_details.description_optional') }}</small></label>
                                                        <textarea class="form-control" id="location-description" rows="2" placeholder="{{ __('location_details.description_placeholder') }}" maxlength="500"></textarea>
                                                        <small class="text-muted"><span id="description-counter">0</span>{{ __('location_details.char_counter_suffix') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel 2: Contact -->
                                <div class="loc-panel panel-contact">
                                    <div class="loc-panel-header">
                                        <span class="loc-panel-icon">
                                            <i data-feather="user" style="width:16px;height:16px;"></i>
                                        </span>
                                        <h6 class="loc-panel-title">{{ __('location_details.panel_contact_ownership') }}</h6>
                                    </div>
                                    <div class="loc-panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-manager">{{ __('location_details.manager_name') }}</label>
                                                    <input type="text" class="form-control" id="location-manager" placeholder="{{ __('location_details.manager_name_placeholder') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-contact-email">{{ __('location_details.email') }}</label>
                                                    <input type="email" class="form-control" id="location-contact-email" placeholder="{{ __('location_details.email_placeholder') }}">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="location-contact-phone">{{ __('location_details.phone') }}</label>
                                                    <input type="tel" class="form-control" id="location-contact-phone" placeholder="{{ __('location_details.phone_placeholder') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="location-owner-group" data-admin-only="true">
                                                <div class="form-group">
                                                    <label for="location-owner">
                                                        {{ __('location_details.owner') }}
                                                        <span style="font-size:0.7rem;background:rgba(var(--mw-primary-rgb,99,102,241),0.12);color:var(--mw-primary);border-radius:10px;padding:1px 7px;font-weight:600;margin-left:4px;">{{ __('location_details.admin_badge') }}</span>
                                                    </label>
                                                    <select class="form-control" id="location-owner"><option value="">{{ __('location_details.select_owner_option') }}</option></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="location-shared-users-group" data-admin-only="true">
                                                <div class="form-group">
                                                    <label for="location-shared-users">
                                                        {{ __('location_details.shared_access') }}
                                                        <span style="font-size:0.7rem;background:rgba(var(--mw-primary-rgb,99,102,241),0.12);color:var(--mw-primary);border-radius:10px;padding:1px 7px;font-weight:600;margin-left:4px;">{{ __('location_details.admin_badge') }}</span>
                                                    </label>
                                                    <select class="select2 form-control" id="location-shared-users" multiple="multiple">
                                                        <!-- populated by JS -->
                                                    </select>
                                                    <small class="form-text text-muted">{{ __('location_details.shared_access_help') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action bar -->
                                <div class="form-action-bar">
                                    <button type="button" id="save-location-info" class="btn btn-primary">
                                        <i data-feather="save" class="mr-1"></i> {{ __('location_details.save_location_info') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetLocationForm()">
                                        <i data-feather="refresh-ccw" class="mr-1"></i> {{ __('common.reset') }}
                                    </button>
                                </div>

                            </form>
</div><!-- /ld-panel-settings -->
