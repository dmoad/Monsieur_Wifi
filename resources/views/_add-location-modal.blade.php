{{-- Create-location modal partial. Include with @@include('_add-location-modal'). --}}
{{-- Open from JS: AddLocationModal.open({ onSuccess: () => refreshMyData() }). --}}
<div class="modal fade" id="add-location-modal" tabindex="-1" role="dialog" aria-labelledby="add-location-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-location-title">{{ __('locations.add_new_location') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-location-form">
                    {{-- Mode toggle: create manually vs import a settings JSON. --}}
                    <div class="btn-group btn-block mb-3" role="group" id="add-location-mode">
                        <button type="button" class="btn btn-primary" data-mode="manual">{{ __('locations.mode_manual') }}</button>
                        <button type="button" class="btn btn-outline-primary" data-mode="import">{{ __('locations.mode_import') }}</button>
                    </div>

                    {{-- Import flow: the file is the only input (plus a password when a new owner is created). --}}
                    <div id="import-section" style="display: none;">
                        <div class="form-group" id="import-group">
                            <label for="import-file">{{ __('locations.import_label') }}</label>
                            <input type="file" class="form-control-file" id="import-file" accept=".json,application/json">
                            <small class="form-text text-muted">{{ __('locations.import_help') }}</small>
                            <div id="import-error" class="text-danger small mt-1" style="display: none;"></div>
                        </div>
                        <div id="import-preview" class="mb-3" style="display: none;"></div>
                        <div class="form-group" id="import-device-model-group" style="display: none;">
                            <label for="import-device-model">{{ __('locations.import_device_model_label') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="import-device-model">
                                <option value="">{{ __('locations.import_device_model_loading') }}</option>
                            </select>
                            <small class="form-text text-muted">{{ __('locations.import_device_model_help') }}</small>
                        </div>
                        <div class="form-group" id="import-newuser-group" style="display: none;">
                            <label for="import-newuser-password">{{ __('locations.import_newuser_password_label') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="import-newuser-password" autocomplete="new-password" placeholder="{{ __('locations.import_newuser_password_placeholder') }}">
                            <small class="form-text text-muted" id="import-newuser-hint"></small>
                        </div>
                    </div>

                    {{-- Manual flow --}}
                    <div id="manual-fields">
                    <div class="form-group" id="owner-select-group" style="display: none;">
                        <label for="owner-select">{{ __('locations.owner_label') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="owner-select">
                            <option value="">{{ __('locations.loading_users') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('locations.owner_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="location-name">{{ __('locations.location_name_label') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location-name" placeholder="{{ __('locations.location_name_placeholder') }}">
                    </div>
                    <div class="form-group">
                        <label for="location-address">{{ __('locations.address_label') }}</label>
                        <input type="text" class="form-control" id="location-address" placeholder="{{ __('locations.address_placeholder') }}">
                    </div>
                    <div class="form-group">
                        <label for="device-select">{{ __('locations.select_device_label') }}</label>
                        <select class="form-control" id="device-select">
                            <option value="">{{ __('locations.select_device_placeholder') }}</option>
                        </select>
                        <small class="form-text text-muted" id="device-select-hint">{{ __('locations.select_device_optional_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="location-notes">{{ __('locations.description_label') }}</label>
                        <textarea class="form-control" id="location-notes" rows="3" placeholder="{{ __('locations.description_placeholder') }}"></textarea>
                    </div>
                    </div>{{-- /#manual-fields --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="add-location-btn">{{ __('locations.add_location') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.ADD_LOCATION_T = {
        adding_location:                 @json(__('locations.adding_location')),
        add_location:                    @json(__('locations.add_location')),
        location_created:                @json(__('locations.location_created')),
        error_creating_location:         @json(__('locations.error_creating_location')),
        assigned_firmware_prefix:        @json(__('locations.assigned_firmware_prefix')),
        location_name_required:          @json(__('locations.location_name_required')),
        device_required:                 @json(__('locations.device_required')),
        select_owner_first_option:       @json(__('locations.select_owner_first_option')),
        select_owner_above_first:        @json(__('locations.select_owner_above_first')),
        select_owner_first_hint:         @json(__('locations.select_owner_first_hint')),
        select_device_help:              @json(__('locations.select_device_help')),
        loading_devices:                 @json(__('locations.loading_devices')),
        select_a_device:                 @json(__('locations.select_a_device')),
        available_devices_group:         @json(__('locations.available_devices_group')),
        available_suffix:                @json(__('locations.available_suffix')),
        inventory_stock_group:           @json(__('locations.inventory_stock_group')),
        inventory_stock_suffix:          @json(__('locations.inventory_stock_suffix')),
        devices_assigned_elsewhere_group:@json(__('locations.devices_assigned_elsewhere_group')),
        assigned_to_prefix:              @json(__('locations.assigned_to_prefix')),
        unknown_location:                @json(__('locations.unknown_location')),
        no_devices_found:                @json(__('locations.no_devices_found')),
        error_loading_devices:           @json(__('locations.error_loading_devices')),
        error_loading_users:             @json(__('locations.error_loading_users')),
        import_button:                   @json(__('locations.import_button')),
        import_coming_soon:              @json(__('locations.import_coming_soon')),
        import_newuser_hint:             @json(__('locations.import_newuser_hint')),
        import_invalid_json:             @json(__('locations.import_invalid_json')),
        import_unsupported_version:      @json(__('locations.import_unsupported_version')),
        import_preview_title:            @json(__('locations.import_preview_title')),
        import_owner:                    @json(__('locations.import_owner')),
        import_owner_default:            @json(__('locations.import_owner_default')),
        import_owner_unverified:         @json(__('locations.import_owner_unverified')),
        import_owner_exists:             @json(__('locations.import_owner_exists')),
        import_owner_will_create:        @json(__('locations.import_owner_will_create')),
        import_section_location:         @json(__('locations.import_section_location')),
        import_section_device:           @json(__('locations.import_section_device')),
        import_device_checking:          @json(__('locations.import_device_checking')),
        import_device_exists:            @json(__('locations.import_device_exists')),
        import_device_new:               @json(__('locations.import_device_new')),
        import_device_model_select:      @json(__('locations.import_device_model_select')),
        import_field_model:              @json(__('locations.import_field_model')),
        import_field_mac:                @json(__('locations.import_field_mac')),
        import_field_serial:             @json(__('locations.import_field_serial')),
        import_section_networks:         @json(__('locations.import_section_networks')),
        import_section_radio:            @json(__('locations.import_section_radio')),
        import_section_wan:              @json(__('locations.import_section_wan')),
        import_section_schedule:         @json(__('locations.import_section_schedule')),
        import_networks_intro:           @json(__('locations.import_networks_intro')),
        import_network_n:                @json(__('locations.import_network_n')),
        import_type_password:            @json(__('locations.import_type_password')),
        import_type_captive_portal:      @json(__('locations.import_type_captive_portal')),
        import_type_open:                @json(__('locations.import_type_open')),
        import_enabled:                  @json(__('locations.import_enabled')),
        import_disabled:                 @json(__('locations.import_disabled')),
        import_field_name:               @json(__('locations.import_field_name')),
        import_field_ssid:               @json(__('locations.import_field_ssid')),
        import_field_auth:               @json(__('locations.import_field_auth')),
        import_field_security:           @json(__('locations.import_field_security')),
        import_field_vlan:               @json(__('locations.import_field_vlan')),
        import_field_ip:                 @json(__('locations.import_field_ip')),
        import_field_dhcp:               @json(__('locations.import_field_dhcp')),
        import_field_country:            @json(__('locations.import_field_country')),
        import_field_channel:            @json(__('locations.import_field_channel')),
        import_field_width:              @json(__('locations.import_field_width')),
        import_field_tx_power:           @json(__('locations.import_field_tx_power')),
        import_field_connection:         @json(__('locations.import_field_connection')),
        import_field_gateway:            @json(__('locations.import_field_gateway')),
        import_field_dns:                @json(__('locations.import_field_dns')),
        import_field_nat:                @json(__('locations.import_field_nat')),
        import_field_mtu:                @json(__('locations.import_field_mtu')),
        import_field_vlan_enabled:       @json(__('locations.import_field_vlan_enabled')),
        import_field_qos:                @json(__('locations.import_field_qos')),
        import_field_web_filter:         @json(__('locations.import_field_web_filter')),
        import_field_working_hours:      @json(__('locations.import_field_working_hours')),
        import_field_hourly:             @json(__('locations.import_field_hourly')),
        import_days_unit:                @json(__('locations.import_days_unit')),
        import_hours_unit:               @json(__('locations.import_hours_unit')),
        on:                              @json(__('locations.on')),
        off:                             @json(__('locations.off')),
    };
</script>
<script src="/assets/js/add-location-modal.js?v={{ filemtime(public_path('assets/js/add-location-modal.js')) }}"></script>
@endpush
