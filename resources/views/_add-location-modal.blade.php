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
    };
</script>
<script src="/assets/js/add-location-modal.js?v={{ filemtime(public_path('assets/js/add-location-modal.js')) }}"></script>
@endpush
