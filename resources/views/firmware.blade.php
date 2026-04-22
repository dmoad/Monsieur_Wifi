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
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/file-uploaders/dropzone.min.css">
<link rel="stylesheet" type="text/css" href="/assets/vendors/css/forms/select/select2.min.css">
<style>
    .badge-status-stable  { background-color: rgba(40,199,111,0.12); color: #28c76f; }
    .badge-status-beta    { background-color: rgba(255,159,67,0.12); color: #ff9f43; }
    .badge-status-deprecated { background-color: rgba(234,84,85,0.12); color: #ea5455; }

    /* Firmware table — matches locations-list .lc-table pattern */
    .fw-list-card { overflow: hidden; margin-bottom: var(--mw-space-md); }
    .datatables-firmware { width: 100% !important; border-collapse: collapse; font-size: 13px; }
    .datatables-firmware thead th {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--mw-text-muted);
        text-align: left;
        padding: 10px var(--mw-space-lg) !important;
        background: var(--mw-bg-page);
        border-bottom: 1px solid var(--mw-border-light);
        border-top: 1px solid var(--mw-border-light);
    }
    .datatables-firmware tbody tr {
        border-bottom: 1px solid var(--mw-border-light);
        cursor: pointer;
        transition: background 0.12s;
    }
    .datatables-firmware tbody tr:last-child { border-bottom: none; }
    .datatables-firmware tbody tr:hover { background: var(--mw-bg-hover); }
    .datatables-firmware td {
        padding: var(--mw-space-md) var(--mw-space-lg) !important;
        vertical-align: middle;
        color: var(--mw-text-secondary);
        border-top: none !important;
    }

    .fw-name-cell { display: flex; align-items: center; gap: var(--mw-space-md); }
    .fw-icon-chip {
        width: 30px; height: 30px;
        background: var(--mw-primary-tint);
        color: var(--mw-primary);
        border-radius: var(--mw-radius-md);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .fw-icon-chip [data-feather] { width: 14px !important; height: 14px !important; }
    .fw-name-main { font-size: 13px; font-weight: 700; color: var(--mw-text-primary); }
    .fw-name-sub  { font-size: 11px; color: var(--mw-text-muted); margin-top: 1px; }

    .fw-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 3px 10px 3px 8px;
        border-radius: var(--mw-radius-full);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.2px;
        line-height: 1.2;
    }
    .fw-pill::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .fw-pill-enabled  { background: rgba(22,163,74,0.12); color: var(--mw-success); }
    .fw-pill-disabled { background: var(--mw-bg-muted);   color: var(--mw-text-muted); }
    .fw-pill-default  { background: var(--mw-primary-tint); color: var(--mw-primary); }
    .fw-pill-muted    { background: var(--mw-bg-muted);   color: var(--mw-text-muted); }

    /* Kebab button — matches .lc-kebab-btn */
    .datatables-firmware .dropdown-toggle.hide-arrow {
        width: 32px; height: 32px;
        border: 1px solid var(--mw-border) !important;
        background: var(--mw-bg-surface) !important;
        border-radius: var(--mw-radius-sm) !important;
        display: inline-flex; align-items: center; justify-content: center;
        color: var(--mw-text-secondary) !important;
        padding: 0 !important;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
    }
    .datatables-firmware .dropdown-toggle.hide-arrow:hover {
        background: var(--mw-primary-tint) !important;
        border-color: var(--mw-primary) !important;
        color: var(--mw-primary) !important;
    }
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
    <div class="alert alert-info py-2 px-3 mb-3" role="alert">
        <i data-feather="info" class="mr-50" style="width:16px;height:16px;vertical-align:text-bottom;"></i>
        {!! __('firmware.alert_sysupgrade') !!}
    </div>

    <!-- Firmware Table -->
    <section id="basic-datatable">
        <div class="card fw-list-card">
            <div class="card-datatable">
                <table class="datatables-firmware">
                    <thead>
                        <tr>
                            <th>{{ __('firmware.col_name') }}</th>
                            <th>{{ __('firmware.col_status') }}</th>
                            <th>{{ __('firmware.col_model') }}</th>
                            <th>{{ __('firmware.col_default') }}</th>
                            <th>{{ __('firmware.col_size') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
<script src="/assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/assets/vendors/js/file-uploaders/dropzone.min.js"></script>

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
                if (response.status === 'success') { firmwareData = response.data; updateFirmwareTable(); }
            },
            error: function() { showToast(T.load_error, 'error'); }
        });
    }

    function updateFirmwareTable() {
        const table = $('.datatables-firmware').DataTable();
        table.clear();
        const sorted = [...firmwareData].sort((a, b) => a.created_at && b.created_at ? new Date(b.created_at) - new Date(a.created_at) : b.id - a.id);
        sorted.forEach(function(fw) {
            const statusPill  = fw.is_enabled
                ? `<span class="fw-pill fw-pill-enabled">${T.badge_enabled}</span>`
                : `<span class="fw-pill fw-pill-disabled">${T.badge_disabled}</span>`;
            const defaultPill = fw.default_model_firmware
                ? `<span class="fw-pill fw-pill-default">${T.badge_default}</span>`
                : '<span class="fw-pill fw-pill-muted">—</span>';
            const nameCell = `<div class="fw-name-cell" data-firmware-id="${fw.id}"><span class="fw-icon-chip"><i data-feather="hard-drive"></i></span><div><div class="fw-name-main">${fw.name}</div><div class="fw-name-sub">${fw.description || ''}</div></div></div>`;
            const actions = `<div class="dropdown"><button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown"><i data-feather="more-vertical"></i></button><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item firmware-edit" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="edit-2" class="mr-50"></i><span>${T.action_edit}</span></a><a class="dropdown-item firmware-download" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="download" class="mr-50"></i><span>${T.action_download}</span></a>${!fw.default_model_firmware ? `<a class="dropdown-item firmware-set-default" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="star" class="mr-50"></i><span>${T.action_set_default}</span></a>` : ''}<a class="dropdown-item firmware-delete" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="trash" class="mr-50"></i><span>${T.action_delete}</span></a></div></div>`;
            const row = table.row.add([nameCell, statusPill, getModelName(fw.model), defaultPill, formatFileSize(fw.file_size), actions]).node();
            row.dataset.firmwareId = fw.id;
        });
        table.draw();
        if (feather) feather.replace({ width: 14, height: 14 });
    }

    // Row click → open edit modal (but let the kebab dropdown / menu items handle their own clicks)
    $(document).on('click', '.datatables-firmware tbody tr', function(e) {
        if ($(e.target).closest('.dropdown').length) return;
        const id = parseInt(this.dataset.firmwareId, 10);
        if (!isNaN(id)) editFirmware(id);
    });

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
