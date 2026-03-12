@extends('layouts.app')

@section('title', 'Firmware Management - Monsieur WiFi')

@php $locale = 'en'; @endphp

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
                <h2 class="content-header-title float-left mb-0">Firmware Management</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Firmware</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-firmware">
                <i data-feather="upload-cloud" class="mr-25"></i>
                <span>Upload New Firmware</span>
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
                    <div class="avatar bg-light-primary p-50 mb-1"><div class="avatar-content"><i data-feather="hard-drive"></i></div></div>
                    <h2 class="font-weight-bolder" id="total-firmware">0</h2>
                    <p class="card-text">Total Firmware Versions</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="avatar bg-light-success p-50 mb-1"><div class="avatar-content"><i data-feather="check-circle"></i></div></div>
                    <h2 class="font-weight-bolder" id="enabled-firmware">0</h2>
                    <p class="card-text">Enabled Firmware</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="avatar bg-light-secondary p-50 mb-1"><div class="avatar-content"><i data-feather="x-circle"></i></div></div>
                    <h2 class="font-weight-bolder" id="disabled-firmware">0</h2>
                    <p class="card-text">Disabled Firmware</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="avatar bg-light-info p-50 mb-1"><div class="avatar-content"><i data-feather="hard-drive"></i></div></div>
                    <h2 class="font-weight-bolder" id="total-size">0 MB</h2>
                    <p class="card-text">Total Size</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmware Table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">All Firmware Versions</h4></div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-firmware table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Device Model</th>
                                        <th>Default</th>
                                        <th>Size</th>
                                        <th>Actions</th>
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
                <h4 class="modal-title">Upload New Firmware</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="firmware-name">Firmware Name</label>
                                <input type="text" class="form-control" id="firmware-name" placeholder="e.g. v2.1.5 Security Update" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" required>
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="model">Device Model</label>
                                <select class="form-control" id="model"><option value="">Loading...</option></select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="default-firmware">
                                    <label class="custom-control-label" for="default-firmware">Set as default firmware for this model</label>
                                </div>
                                <small class="text-muted">When enabled, this firmware will be automatically assigned to new devices of this model.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" rows="3" placeholder="Firmware description and changelog"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="firmware-file">Firmware File</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="firmware-file" accept=".tar.gz,.tgz,.tar" required>
                                    <label class="custom-file-label" for="firmware-file">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Max file size: 100MB. Accepted formats: .tar.gz, .tgz, .tar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Firmware</button>
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
                <h4 class="modal-title">Edit Firmware</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-firmware-name">Firmware Name</label>
                                <input type="text" class="form-control" id="edit-firmware-name" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-status">Status</label>
                                <select class="form-control" id="edit-status">
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-model">Device Model</label>
                                <select class="form-control" id="edit-model"><option value="">Loading...</option></select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit-default-firmware">
                                    <label class="custom-control-label" for="edit-default-firmware">Set as default firmware for this model</label>
                                </div>
                                <small class="text-muted">When enabled, this firmware will be automatically assigned to new devices of this model.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit-description">Description</label>
                                <textarea class="form-control" id="edit-description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Firmware File (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit-firmware-file" accept=".tar.gz,.tgz,.tar">
                                    <label class="custom-file-label" for="edit-firmware-file">Choose firmware file</label>
                                </div>
                                <small class="form-text text-muted">Accepted formats: .tar.gz, .tgz, .tar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
            language: { paginate: { previous: '&nbsp;', next: '&nbsp;' } },
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
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || 'Choose file');
        });

        loadProductModels();
        loadFirmwareData();
    });

    $(document).ready(function() {
        $('#add-new-firmware form').on('submit', function(e) { e.preventDefault(); uploadFirmware(); });
        $('#edit-firmware form').on('submit',    function(e) { e.preventDefault(); updateFirmware(); });

        $('#add-new-firmware').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('.custom-file-label').text('Choose file');
            $('#status, #model').val('').trigger('change');
        });
        $('#edit-firmware').on('hidden.bs.modal', function() {
            currentEditingId = null;
            $(this).find('form')[0].reset();
            $('.custom-file-label').text('Choose firmware file');
            $('#edit-status, #edit-model').val('').trigger('change');
        });
        $('#add-new-firmware, #edit-firmware').on('shown.bs.modal', function() { initializeSelect2(); });
    });

    function initializeSelect2() {
        $('#status, #edit-status, #model, #edit-model').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
        });
        $('#status, #edit-status').select2({ minimumResultsForSearch: Infinity, placeholder: 'Select status', allowClear: false, width: '100%' });
        $('#model, #edit-model').select2({ minimumResultsForSearch: Infinity, placeholder: 'Select device model', allowClear: false, width: '100%' });
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
            error: function() { showToast('Error loading firmware data', 'error'); }
        });
    }

    function updateFirmwareTable() {
        const table = $('.datatables-firmware').DataTable();
        table.clear();
        const sorted = [...firmwareData].sort((a, b) => a.created_at && b.created_at ? new Date(b.created_at) - new Date(a.created_at) : b.id - a.id);
        sorted.forEach(function(fw) {
            const statusBadge  = fw.is_enabled ? '<span class="badge badge-pill badge-light-success">Enable</span>' : '<span class="badge badge-pill badge-light-secondary">Disable</span>';
            const defaultBadge = fw.default_model_firmware ? '<span class="badge badge-pill badge-light-primary">Default</span>' : '<span class="badge badge-pill badge-light-secondary">-</span>';
            table.row.add([
                `<div class="d-flex align-items-center"><div class="avatar bg-light-primary mr-1 p-25"><div class="avatar-content"><i data-feather="hard-drive"></i></div></div><div><div class="font-weight-bold">${fw.name}</div><div class="small text-truncate text-muted">${fw.description || ''}</div></div></div>`,
                statusBadge, getModelName(fw.model), defaultBadge, formatFileSize(fw.file_size),
                `<div class="dropdown"><button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown"><i data-feather="more-vertical"></i></button><div class="dropdown-menu"><a class="dropdown-item firmware-edit" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="edit-2" class="mr-50"></i><span>Edit</span></a><a class="dropdown-item firmware-download" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="download" class="mr-50"></i><span>Download</span></a>${!fw.default_model_firmware ? `<a class="dropdown-item firmware-set-default" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="star" class="mr-50"></i><span>Set as Default</span></a>` : ''}<a class="dropdown-item firmware-delete" href="javascript:void(0);" data-firmware-id="${fw.id}"><i data-feather="trash" class="mr-50"></i><span>Delete</span></a></div></div>`
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
        if (!fileInput.files[0]) { showToast('Please select a firmware file', 'error'); return; }
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
                if (response.status === 'success') { showToast('Firmware uploaded successfully', 'success'); $('#add-new-firmware').modal('hide'); $('#add-new-firmware form')[0].reset(); $('.custom-file-label').text('Choose file'); loadFirmwareData(); }
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Error uploading firmware', 'error'); }
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
            $('.custom-file-label').text('Choose firmware file');
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
                if (response.status === 'success') { showToast('Firmware updated successfully', 'success'); $('#edit-firmware').modal('hide'); loadFirmwareData(); currentEditingId = null; }
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Error updating firmware', 'error'); }
        });
    }

    function deleteFirmware(id) {
        if (!confirm('Are you sure you want to delete this firmware?')) return;
        $.ajax({
            url: `/api/firmware/${id}`, method: 'DELETE', headers: getAuthHeaders(),
            success: function(response) { if (response.status === 'success') { showToast('Firmware deleted successfully', 'success'); loadFirmwareData(); } },
            error: function() { showToast('Error deleting firmware', 'error'); }
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
        if (!confirm(`Are you sure you want to set "${fw.name}" as the default firmware for ${getModelName(fw.model)} devices?`)) return;
        $.ajax({
            url: `/api/firmware/${id}/set-default`, method: 'POST', headers: getAuthHeaders(),
            success: function(response) { if (response.status === 'success') { showToast('Firmware set as default successfully', 'success'); loadFirmwareData(); } },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Error setting firmware as default', 'error'); }
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
        $selects.empty().append('<option value="">Select Model</option>');
        productModels.forEach(pm => $selects.append(`<option value="${pm.device_type}">${pm.name}</option>`));
        $selects.trigger('change');
    }

    function getModelName(deviceType) {
        const pm = productModels.find(m => m.device_type === deviceType);
        return pm ? pm.name : (deviceType || 'Not specified');
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
