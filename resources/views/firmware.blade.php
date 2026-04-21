@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $firmwareT = [
        'select_status_placeholder' => __('firmware.select_status_placeholder'),
        'select_model_placeholder' => __('firmware.select_model_placeholder'),
        'select_model_option' => __('firmware.select_model_option'),
        'loading' => __('firmware.loading'),
        'choose_file' => __('firmware.choose_file'),
        'choose_firmware_file' => __('firmware.choose_firmware_file'),
        'badge_enabled' => __('firmware.badge_enabled'),
        'badge_disabled' => __('firmware.badge_disabled'),
        'badge_default' => __('firmware.badge_default'),
        'action_edit' => __('firmware.action_edit'),
        'action_download' => __('firmware.action_download'),
        'action_set_default' => __('firmware.action_set_default'),
        'action_delete' => __('firmware.action_delete'),
        'please_select_file' => __('firmware.please_select_file'),
        'upload_success' => __('firmware.upload_success'),
        'upload_error' => __('firmware.upload_error'),
        'update_success' => __('firmware.update_success'),
        'update_error' => __('firmware.update_error'),
        'delete_confirm' => __('firmware.delete_confirm'),
        'delete_success' => __('firmware.delete_success'),
        'delete_error' => __('firmware.delete_error'),
        'set_default_confirm' => __('firmware.set_default_confirm'),
        'set_default_success' => __('firmware.set_default_success'),
        'set_default_error' => __('firmware.set_default_error'),
        'load_error' => __('firmware.load_error'),
        'model_not_specified' => __('firmware.model_not_specified'),
        'dt_info' => __('firmware.dt_info'),
        'dt_info_empty' => __('firmware.dt_info_empty'),
        'dt_info_filtered' => __('firmware.dt_info_filtered'),
        'dt_length_menu' => __('firmware.dt_length_menu'),
        'dt_search' => __('firmware.dt_search'),
        'dt_zero_records' => __('firmware.dt_zero_records'),
        'dt_empty_table' => __('firmware.dt_empty_table'),
        'dt_loading_records' => __('firmware.dt_loading_records'),
    ];
@endphp

@section('title', __('firmware.page_title'))

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/file-uploaders/dropzone.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-file-uploader.css">
<style>
    .badge-status-stable  { background-color: rgba(40,199,111,0.12); color: #28c76f; }
    .badge-status-beta    { background-color: rgba(255,159,67,0.12); color: #ff9f43; }
    .badge-status-deprecated { background-color: rgba(234,84,85,0.12); color: #ea5455; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('firmware.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('firmware.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-firmware">
                <i data-feather="upload-cloud" class="mr-25"></i>
                <span>{{ __('firmware.upload_new') }}</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <span class="mw-stat-icon mw-stat-icon-primary mb-1"><i data-feather="hard-drive"></i></span>
                    <h2 class="font-weight-bolder" id="total-firmware">0</h2>
                    <p class="card-text">{{ __('firmware.total_versions') }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <span class="mw-stat-icon mw-stat-icon-success mb-1"><i data-feather="check-circle"></i></span>
                    <h2 class="font-weight-bolder" id="enabled-firmware">0</h2>
                    <p class="card-text">{{ __('firmware.enabled_firmware') }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <span class="mw-stat-icon mw-stat-icon-muted mb-1"><i data-feather="x-circle"></i></span>
                    <h2 class="font-weight-bolder" id="disabled-firmware">0</h2>
                    <p class="card-text">{{ __('firmware.disabled_firmware') }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <span class="mw-stat-icon mw-stat-icon-info mb-1"><i data-feather="hard-drive"></i></span>
                    <h2 class="font-weight-bolder" id="total-size">0 MB</h2>
                    <p class="card-text">{{ __('firmware.total_size') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmware Table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">{{ __('firmware.table_card_title') }}</h4></div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-firmware table">
                                <thead>
                                    <tr>
                                        <th>{{ __('firmware.col_name') }}</th>
                                        <th>{{ __('firmware.col_status') }}</th>
                                        <th>{{ __('firmware.col_model') }}</th>
                                        <th>{{ __('firmware.col_default') }}</th>
                                        <th>{{ __('firmware.col_size') }}</th>
                                        <th>{{ __('firmware.col_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add New Firmware Modal -->
<div class="modal fade text-left" id="add-new-firmware" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('firmware.modal_upload_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="firmware-name">{{ __('firmware.name_label') }}</label>
                                <input type="text" class="form-control" id="firmware-name" placeholder="{{ __('firmware.name_placeholder') }}" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="status">{{ __('firmware.status_label') }}</label>
                                <select class="form-control" id="status" required>
                                    <option value="1">{{ __('firmware.status_enable') }}</option>
                                    <option value="0">{{ __('firmware.status_disable') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="model">{{ __('firmware.model_label') }}</label>
                                <select class="form-control" id="model"><option value="">{{ __('firmware.loading') }}</option></select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="default-firmware">
                                    <label class="custom-control-label" for="default-firmware">{{ __('firmware.default_checkbox') }}</label>
                                </div>
                                <small class="text-muted">{{ __('firmware.default_help') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">{{ __('firmware.description_label') }}</label>
                                <textarea class="form-control" id="description" rows="3" placeholder="{{ __('firmware.description_placeholder') }}"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="firmware-file">{{ __('firmware.file_label') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="firmware-file" accept=".tar.gz,.tgz,.tar" required>
                                    <label class="custom-file-label" for="firmware-file">{{ __('firmware.choose_file') }}</label>
                                </div>
                                <small class="form-text text-muted">{{ __('firmware.file_help_upload') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('firmware.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('firmware.upload_btn') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Firmware Modal -->
<div class="modal fade text-left" id="edit-firmware" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('firmware.modal_edit_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-firmware-name">{{ __('firmware.name_label') }}</label>
                                <input type="text" class="form-control" id="edit-firmware-name" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-status">{{ __('firmware.status_label') }}</label>
                                <select class="form-control" id="edit-status">
                                    <option value="1">{{ __('firmware.status_enable') }}</option>
                                    <option value="0">{{ __('firmware.status_disable') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-model">{{ __('firmware.model_label') }}</label>
                                <select class="form-control" id="edit-model"><option value="">{{ __('firmware.loading') }}</option></select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit-default-firmware">
                                    <label class="custom-control-label" for="edit-default-firmware">{{ __('firmware.default_checkbox') }}</label>
                                </div>
                                <small class="text-muted">{{ __('firmware.default_help') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit-description">{{ __('firmware.description_label') }}</label>
                                <textarea class="form-control" id="edit-description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('firmware.file_optional_label') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit-firmware-file" accept=".tar.gz,.tgz,.tar">
                                    <label class="custom-file-label" for="edit-firmware-file">{{ __('firmware.choose_firmware_file') }}</label>
                                </div>
                                <small class="form-text text-muted">{{ __('firmware.file_help_edit') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('firmware.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('firmware.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/app-assets/vendors/js/file-uploaders/dropzone.min.js"></script>
<script src="/app-assets/js/scripts/forms/form-file-uploader.js"></script>

<script>
    window.FIRMWARE_T = {!! json_encode($firmwareT) !!};
    const T = window.FIRMWARE_T;

    let firmwareData = [];
    let currentEditingId = null;
    let productModels = [];

    $(window).on('load', function() {
        if (feather) feather.replace({ width: 14, height: 14 });

        const table = $('.datatables-firmware').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            columnDefs: [{ targets: [5], orderable: false }],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: { previous: '&nbsp;', next: '&nbsp;' },
                info: T.dt_info,
                infoEmpty: T.dt_info_empty,
                infoFiltered: T.dt_info_filtered,
                lengthMenu: T.dt_length_menu,
                search: T.dt_search,
                zeroRecords: T.dt_zero_records,
                emptyTable: T.dt_empty_table,
                loadingRecords: T.dt_loading_records
            },
            drawCallback: function() {
                if (feather) feather.replace({ width: 14, height: 14 });
                $('[data-toggle="dropdown"]').dropdown();
            }
        });

        $(document).on('click', '.firmware-edit',        function(e) { e.preventDefault(); editFirmware(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-download',    function(e) { e.preventDefault(); downloadFirmware(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-set-default', function(e) { e.preventDefault(); setAsDefault(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-delete',      function(e) { e.preventDefault(); deleteFirmware(parseInt($(this).data('firmware-id'))); });

        initializeSelect2();

        $('.custom-file-input').on('change', function() {
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || T.choose_file);
        });

        loadProductModels();
        loadFirmwareData();
    });

    $(document).ready(function() {
        $('#add-new-firmware form').on('submit', function(e) { e.preventDefault(); uploadFirmware(); });
        $('#edit-firmware form').on('submit',    function(e) { e.preventDefault(); updateFirmware(); });

        $('#add-new-firmware').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('.custom-file-label').text(T.choose_file);
            $('#status, #model').val('').trigger('change');
        });
        $('#edit-firmware').on('hidden.bs.modal', function() {
            currentEditingId = null;
            $(this).find('form')[0].reset();
            $('.custom-file-label').text(T.choose_firmware_file);
            $('#edit-status, #edit-model').val('').trigger('change');
        });
        $('#add-new-firmware, #edit-firmware').on('shown.bs.modal', function() { initializeSelect2(); });
    });

    function initializeSelect2() {
        $('#status, #edit-status, #model, #edit-model').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
        });
        $('#status, #edit-status').select2({ minimumResultsForSearch: Infinity, placeholder: T.select_status_placeholder, allowClear: false, width: '100%' });
        $('#model, #edit-model').select2({ minimumResultsForSearch: Infinity, placeholder: T.select_model_placeholder, allowClear: false, width: '100%' });
    }

    function getAuthHeaders() {
        return { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' };
    }

    function loadFirmwareData() {
        $.ajax({
            url: '/api/firmware', method: 'GET', headers: getAuthHeaders(),
            success: function(response) {
                if (response.status === 'success') { firmwareData = response.data; updateFirmwareTable(); updateStats(); }
            },
            error: function() { showToast(T.load_error, 'error'); }
        });
    }

    function updateFirmwareTable() {
        const table = $('.datatables-firmware').DataTable();
        table.clear();
        const sorted = [...firmwareData].sort((a, b) => a.created_at && b.created_at ? new Date(b.created_at) - new Date(a.created_at) : b.id - a.id);
        sorted.forEach(function(fw) {
            const statusBadge  = fw.is_enabled ? `<span class="badge badge-pill badge-light-success">${T.badge_enabled}</span>` : `<span class="badge badge-pill badge-light-secondary">${T.badge_disabled}</span>`;
            const defaultBadge = fw.default_model_firmware ? `<span class="badge badge-pill badge-light-primary">${T.badge_default}</span>` : '<span class="badge badge-pill badge-light-secondary">-</span>';
            table.row.add([
                `<div class="d-flex align-items-center"><span class="mw-stat-icon mw-stat-icon-primary mr-1"><i data-feather="hard-drive"></i></span><div><div class="font-weight-bold">${fw.name}</div><div class="small text-truncate text-muted">${fw.description || ''}</div></div></div>`,
                statusBadge, getModelName(fw.model), defaultBadge, formatFileSize(fw.file_size),
                `<div class="dropdown"><button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown"><i data-feather="more-vertical"></i></button><div class="dropdown-menu"><a class="dropdown-item firmware-edit" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="edit-2" class="mr-50"></i><span>${T.action_edit}</span></a><a class="dropdown-item firmware-download" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="download" class="mr-50"></i><span>${T.action_download}</span></a>${!fw.default_model_firmware ? `<a class="dropdown-item firmware-set-default" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="star" class="mr-50"></i><span>${T.action_set_default}</span></a>` : ''}<a class="dropdown-item firmware-delete" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="trash" class="mr-50"></i><span>${T.action_delete}</span></a></div></div>`
            ]);
        });
        table.draw();
        if (feather) feather.replace({ width: 14, height: 14 });
    }

    function updateStats() {
        $('#total-firmware').text(firmwareData.length);
        $('#enabled-firmware').text(firmwareData.filter(f => f.is_enabled).length);
        $('#disabled-firmware').text(firmwareData.filter(f => !f.is_enabled).length);
        $('#total-size').text(formatFileSize(firmwareData.reduce((s, f) => s + (f.file_size || 0), 0)));
    }

    function uploadFirmware() {
        const fileInput = document.getElementById('firmware-file');
        if (!fileInput.files[0]) { showToast(T.please_select_file, 'error'); return; }
        const formData = new FormData();
        formData.append('name', $('#firmware-name').val());
        formData.append('model', $('#model').val());
        formData.append('description', $('#description').val());
        formData.append('is_enabled', $('#status').val());
        formData.append('default_model_firmware', $('#default-firmware').is(':checked') ? 1 : 0);
        formData.append('file', fileInput.files[0]);
        $.ajax({
            url: '/api/firmware', method: 'POST',
            headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
            data: formData, processData: false, contentType: false,
            success: function(response) {
                if (response.status === 'success') { showToast(T.upload_success, 'success'); $('#add-new-firmware').modal('hide'); $('#add-new-firmware form')[0].reset(); $('.custom-file-label').text(T.choose_file); loadFirmwareData(); }
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || T.upload_error, 'error'); }
        });
    }

    function editFirmware(id) {
        const fw = firmwareData.find(f => f.id === id);
        if (!fw) return;
        currentEditingId = id;
        $('#edit-firmware').modal('show');
        setTimeout(() => {
            $('#edit-firmware-name').val(fw.name);
            $('#edit-description').val(fw.description || '');
            $('#edit-status').val(fw.is_enabled ? '1' : '0').trigger('change.select2');
            $('#edit-model').val(fw.model || '').trigger('change.select2');
            $('#edit-default-firmware').prop('checked', fw.default_model_firmware || false);
            $('#edit-firmware-file').val('');
            $('.custom-file-label').text(T.choose_firmware_file);
        }, 300);
    }

    function updateFirmware() {
        if (!currentEditingId) return;
        const formData = new FormData();
        formData.append('name', $('#edit-firmware-name').val());
        formData.append('model', $('#edit-model').val());
        formData.append('description', $('#edit-description').val());
        formData.append('is_enabled', $('#edit-status').val());
        formData.append('default_model_firmware', $('#edit-default-firmware').is(':checked') ? 1 : 0);
        formData.append('_method', 'PUT');
        const fileInput = document.getElementById('edit-firmware-file');
        if (fileInput.files[0]) formData.append('file', fileInput.files[0]);
        $.ajax({
            url: `/api/firmware/${currentEditingId}`, method: 'POST',
            headers: { 'Authorization': 'Bearer ' + UserManager.getToken(), 'Accept': 'application/json' },
            data: formData, processData: false, contentType: false,
            success: function(response) {
                if (response.status === 'success') { showToast(T.update_success, 'success'); $('#edit-firmware').modal('hide'); loadFirmwareData(); currentEditingId = null; }
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || T.update_error, 'error'); }
        });
    }

    function deleteFirmware(id) {
        if (!confirm(T.delete_confirm)) return;
        $.ajax({
            url: `/api/firmware/${id}`, method: 'DELETE', headers: getAuthHeaders(),
            success: function(response) { if (response.status === 'success') { showToast(T.delete_success, 'success'); loadFirmwareData(); } },
            error: function() { showToast(T.delete_error, 'error'); }
        });
    }

    function downloadFirmware(id) {
        const fw = firmwareData.find(f => f.id === id);
        if (!fw) return;
        const link = document.createElement('a');
        link.href = `/api/firmware/${id}/download?token=${UserManager.getToken()}`;
        link.download = fw.file_name;
        document.body.appendChild(link); link.click(); document.body.removeChild(link);
    }

    function setAsDefault(id) {
        const fw = firmwareData.find(f => f.id === id);
        if (!fw) return;
        const confirmMsg = T.set_default_confirm
            .replace('{name}', fw.name)
            .replace('{model}', getModelName(fw.model));
        if (!confirm(confirmMsg)) return;
        $.ajax({
            url: `/api/firmware/${id}/set-default`, method: 'POST', headers: getAuthHeaders(),
            success: function(response) { if (response.status === 'success') { showToast(T.set_default_success, 'success'); loadFirmwareData(); } },
            error: function(xhr) { showToast(xhr.responseJSON?.message || T.set_default_error, 'error'); }
        });
    }

    function loadProductModels() {
        $.ajax({
            url: '/api/firmware/models', method: 'GET', headers: getAuthHeaders(),
            success: function(response) { if (response.status === 'success') { productModels = response.data; populateModelDropdowns(); } },
            error: function() { console.error('Failed to load device models'); }
        });
    }

    function populateModelDropdowns() {
        const $selects = $('#model, #edit-model');
        $selects.empty().append(`<option value="">${T.select_model_option}</option>`);
        productModels.forEach(pm => $selects.append(`<option value="${pm.device_type}">${pm.name}</option>`));
        $selects.trigger('change');
    }

    function getModelName(deviceType) {
        const pm = productModels.find(m => m.device_type === deviceType);
        return pm ? pm.name : (deviceType || T.model_not_specified);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 Bytes';
        const k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showToast(message, type = 'info') {
        if (typeof toastr !== 'undefined') {
            type === 'success' ? toastr.success(message) : toastr.error(message);
        } else {
            const toast = $(`<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert" style="position:fixed;top:20px;right:20px;z-index:9999;">${message}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>`);
            $('body').append(toast);
            setTimeout(() => toast.alert('close'), 5000);
        }
    }
</script>
@endpush
