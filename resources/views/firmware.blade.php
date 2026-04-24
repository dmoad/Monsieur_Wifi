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
        'no_firmware' => __('firmware.no_firmware'),
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
    ];
@endphp

@section('title', __('firmware.page_title'))

@push('styles')
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
        border-bottom: 1px solid var(--mw-border-light);
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

    .fw-list-head {
        display: flex;
        align-items: center;
        gap: var(--mw-space-md);
        padding: var(--mw-space-md) var(--mw-space-xl);
        border-bottom: 1px solid var(--mw-border-light);
    }
    .fw-list-title { font-size: 15px; font-weight: 600; color: var(--mw-text-primary); flex-shrink: 0; }
    .fw-list-tools { display: flex; align-items: center; gap: var(--mw-space-md); margin-left: auto; }
    .fw-per-page { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mw-text-muted); }
    .fw-per-page select { width: auto; }

    .pagination-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding: 1rem;
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-md);
        box-shadow: var(--mw-shadow-card);
    }
    .pagination-info { color: var(--mw-text-muted); font-size: 0.9rem; }
    .pagination-buttons { display: flex; gap: 0.5rem; align-items: center; }
    .fw-search {
        width: 220px;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid var(--mw-border-light);
        border-radius: var(--mw-radius-sm);
        background: var(--mw-bg-surface);
        color: var(--mw-text-primary);
    }
    .fw-search:focus { outline: none; border-color: var(--mw-primary); }

    .datatables-firmware td.fw-col-actions { text-align: right; width: 1%; white-space: nowrap; }
    .fw-kebab-wrap { position: relative; display: inline-block; }
    .fw-kebab-btn {
        width: 32px; height: 32px;
        border: 1px solid var(--mw-border);
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-sm);
        display: inline-flex; align-items: center; justify-content: center;
        color: var(--mw-text-secondary);
        cursor: pointer;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
        padding: 0;
    }
    .fw-kebab-btn:hover { background: var(--mw-primary-tint); border-color: var(--mw-primary); color: var(--mw-primary); }
    .fw-menu {
        display: none; position: absolute; top: calc(100% + 4px); right: 0;
        background: var(--mw-bg-surface); border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-md); box-shadow: var(--mw-shadow-elevated);
        min-width: 160px; z-index: 100; padding: 4px 0;
    }
    .fw-menu.open { display: block; }
    .fw-menu-item {
        display: flex; align-items: center; gap: var(--mw-space-sm);
        width: 100%; padding: 7px 14px; border: none; background: transparent;
        font-size: 13px; color: var(--mw-text-secondary); cursor: pointer;
        text-align: left; transition: background 0.1s, color 0.1s; font-family: var(--mw-font);
        text-decoration: none !important;
    }
    .fw-menu-item:hover { background: var(--mw-bg-hover); color: var(--mw-text-primary); }
    .fw-menu-danger { color: var(--mw-danger) !important; }
    .fw-menu-danger:hover { background: rgba(220,38,38,0.06) !important; }
    .fw-menu-divider { height: 1px; background: var(--mw-border-light); margin: 3px 0; }
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
    <div class="card fw-list-card">
        <div class="fw-list-head">
            <span class="fw-list-title">{{ __('firmware.card_title') }}</span>
            <div class="fw-list-tools">
                <div class="fw-per-page">
                    <label for="fw-items-per-page" class="mb-0">{{ __('common.items_per_page') }}</label>
                    <select id="fw-items-per-page" class="form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <input type="text" id="fw-search" class="fw-search" placeholder="{{ __('firmware.search_placeholder') }}" autocomplete="off">
            </div>
        </div>
        <div class="table-responsive">
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
    <div id="fw-pagination"></div>
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
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>

<script>
    window.FIRMWARE_T = {!! json_encode($firmwareT) !!};
    const T = window.FIRMWARE_T;
    const PAGE_LOCALE = document.documentElement.lang || 'en';

    let firmwareData = [];
    let currentEditingId = null;
    let productModels = [];
    let fwCurrentPage = 1;
    let fwItemsPerPage = 25;

    const _fwDotsSvg = `<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>`;
    const _fwEditSvg  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;
    const _fwDlSvg    = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>`;
    const _fwStarSvg  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
    const _fwTrashSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>`;
    const _fwHddSvg   = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="22" y1="12" x2="2" y2="12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/><line x1="6" y1="16" x2="6.01" y2="16"/><line x1="10" y1="16" x2="10.01" y2="16"/></svg>`;

    function closeAllFwMenus() {
        document.querySelectorAll('.fw-menu.open').forEach(m => m.classList.remove('open'));
    }

    $(document).ready(function() {
        if (feather) feather.replace({ width: 14, height: 14 });

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

        $('.custom-file-input').on('change', function() {
            $(this).next('.custom-file-label').html($(this).val().split('\\').pop() || T.choose_file);
        });

        $('#fw-search').on('input', function() { fwCurrentPage = 1; renderFirmwareTable(); });
        $('#fw-items-per-page').on('change', function() { fwItemsPerPage = parseInt($(this).val(), 10) || 25; fwCurrentPage = 1; renderFirmwareTable(); });

        $(document).on('click', function(e) {
            const toggleBtn = $(e.target).closest('.fw-kebab-toggle');
            if (toggleBtn.length) {
                const id = toggleBtn.data('fw-id');
                const $menu = $(`#fw-menu-${id}`);
                const wasOpen = $menu.hasClass('open');
                closeAllFwMenus();
                if (!wasOpen) $menu.addClass('open');
                return;
            }
            if (!$(e.target).closest('.fw-kebab-wrap').length) closeAllFwMenus();
        });

        $(document).on('click', '.firmware-edit',        function(e) { e.preventDefault(); closeAllFwMenus(); editFirmware(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-download',    function(e) { e.preventDefault(); closeAllFwMenus(); downloadFirmware(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-set-default', function(e) { e.preventDefault(); closeAllFwMenus(); setAsDefault(parseInt($(this).data('firmware-id'))); });
        $(document).on('click', '.firmware-delete',      function(e) { e.preventDefault(); closeAllFwMenus(); deleteFirmware(parseInt($(this).data('firmware-id'))); });

        $(document).on('click', '.datatables-firmware tbody tr', function(e) {
            if ($(e.target).closest('.fw-kebab-wrap').length) return;
            const id = parseInt(this.dataset.firmwareId, 10);
            if (!isNaN(id)) editFirmware(id);
        });

        initializeSelect2();
        loadProductModels();
        loadFirmwareData();
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
                if (response.status === 'success') { firmwareData = response.data; renderFirmwareTable(); }
            },
            error: function() { showToast(T.load_error, 'error'); }
        });
    }

    function renderFirmwareTable() {
        const $tbody = $('.datatables-firmware tbody');
        const query = ($('#fw-search').val() || '').trim().toLowerCase();
        const sorted = [...firmwareData].sort((a, b) =>
            a.created_at && b.created_at ? new Date(b.created_at) - new Date(a.created_at) : b.id - a.id
        );
        const filtered = query
            ? sorted.filter(fw =>
                (fw.name || '').toLowerCase().includes(query) ||
                (fw.description || '').toLowerCase().includes(query) ||
                getModelName(fw.model).toLowerCase().includes(query))
            : sorted;

        if (!filtered.length) {
            $tbody.html(`<tr><td colspan="6" class="text-center py-4" style="color:var(--mw-text-muted)">${T.no_firmware}</td></tr>`);
            $('#fw-pagination').empty();
            return;
        }

        const totalItems = filtered.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / fwItemsPerPage));
        if (fwCurrentPage > totalPages) fwCurrentPage = totalPages;
        const startIdx = (fwCurrentPage - 1) * fwItemsPerPage;
        const endIdx = Math.min(startIdx + fwItemsPerPage, totalItems);
        const list = filtered.slice(startIdx, endIdx);

        const rows = list.map(fw => {
            const statusPill  = fw.is_enabled
                ? `<span class="fw-pill fw-pill-enabled">${T.badge_enabled}</span>`
                : `<span class="fw-pill fw-pill-disabled">${T.badge_disabled}</span>`;
            const defaultPill = fw.default_model_firmware
                ? `<span class="fw-pill fw-pill-default">${T.badge_default}</span>`
                : '<span class="fw-pill fw-pill-muted">—</span>';
            const nameCell = `<div class="fw-name-cell"><span class="fw-icon-chip">${_fwHddSvg}</span><div><div class="fw-name-main">${fw.name}</div><div class="fw-name-sub">${fw.description || ''}</div></div></div>`;
            const setDefaultItem = !fw.default_model_firmware
                ? `<button type="button" class="fw-menu-item firmware-set-default" data-firmware-id="${fw.id}">${_fwStarSvg} ${T.action_set_default}</button>`
                : '';
            const actions = `<div class="fw-kebab-wrap">
                <button type="button" class="fw-kebab-btn fw-kebab-toggle" data-fw-id="${fw.id}">${_fwDotsSvg}</button>
                <div class="fw-menu" id="fw-menu-${fw.id}">
                    <button type="button" class="fw-menu-item firmware-edit" data-firmware-id="${fw.id}">${_fwEditSvg} ${T.action_edit}</button>
                    <button type="button" class="fw-menu-item firmware-download" data-firmware-id="${fw.id}">${_fwDlSvg} ${T.action_download}</button>
                    ${setDefaultItem}
                    <div class="fw-menu-divider"></div>
                    <button type="button" class="fw-menu-item fw-menu-danger firmware-delete" data-firmware-id="${fw.id}">${_fwTrashSvg} ${T.action_delete}</button>
                </div>
            </div>`;
            return `<tr data-firmware-id="${fw.id}">
                <td>${nameCell}</td>
                <td>${statusPill}</td>
                <td>${getModelName(fw.model)}</td>
                <td>${defaultPill}</td>
                <td>${formatFileSize(fw.file_size)}</td>
                <td class="fw-col-actions">${actions}</td>
            </tr>`;
        }).join('');
        $tbody.html(rows);
        renderFwPagination(totalItems, totalPages, startIdx, endIdx);
    }

    function renderFwPagination(totalItems, totalPages, startIdx, endIdx) {
        const $pg = $('#fw-pagination');
        if (totalPages <= 1) { $pg.empty(); return; }
        const localeStr = (PAGE_LOCALE === 'fr')
            ? `Affichage ${startIdx + 1}-${endIdx} sur ${totalItems}`
            : `Showing ${startIdx + 1}-${endIdx} of ${totalItems}`;
        let buttons = `<button class="btn btn-sm btn-outline-primary" onclick="goToFwPage(${fwCurrentPage - 1})" ${fwCurrentPage === 1 ? 'disabled' : ''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>`;
        const maxBtns = 5;
        let startP = Math.max(1, fwCurrentPage - Math.floor(maxBtns / 2));
        let endP = Math.min(totalPages, startP + maxBtns - 1);
        if (endP - startP < maxBtns - 1) startP = Math.max(1, endP - maxBtns + 1);
        if (startP > 1) {
            buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToFwPage(1)">1</button>`;
            if (startP > 2) buttons += `<span class="mx-2">...</span>`;
        }
        for (let i = startP; i <= endP; i++) {
            buttons += `<button class="btn btn-sm ${i === fwCurrentPage ? 'btn-primary' : 'btn-outline-primary'}" onclick="goToFwPage(${i})">${i}</button>`;
        }
        if (endP < totalPages) {
            if (endP < totalPages - 1) buttons += `<span class="mx-2">...</span>`;
            buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToFwPage(${totalPages})">${totalPages}</button>`;
        }
        buttons += `<button class="btn btn-sm btn-outline-primary" onclick="goToFwPage(${fwCurrentPage + 1})" ${fwCurrentPage === totalPages ? 'disabled' : ''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button>`;
        $pg.html(`<div class="pagination-controls"><div class="pagination-info">${localeStr}</div><div class="pagination-buttons">${buttons}</div></div>`);
    }

    function goToFwPage(p) {
        const totalPages = Math.max(1, Math.ceil(firmwareData.length / fwItemsPerPage));
        if (p < 1 || p > totalPages) return;
        fwCurrentPage = p;
        renderFirmwareTable();
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
