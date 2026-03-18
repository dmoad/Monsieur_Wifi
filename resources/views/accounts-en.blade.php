@extends('layouts.app')

@section('title', $locale === 'fr' ? 'Equipe - Monsieur WiFi' : 'Team - Monsieur WiFi')

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">

<style>
    .badge-role-owner    { background-color: rgba(234, 84, 85, 0.12); color: #ea5455; }
    .badge-role-admin    { background-color: rgba(115, 103, 240, 0.12); color: #7367f0; }
    .badge-role-operator { background-color: rgba(255, 159, 67, 0.12); color: #ff9f43; }
    .badge-role-viewer   { background-color: rgba(108, 117, 125, 0.12); color: #6c757d; }
    .badge-role-partner  { background-color: rgba(0, 207, 232, 0.12); color: #00cfe8; }
    .badge-scope         { background-color: rgba(0, 207, 232, 0.12); color: #00cfe8; }
    .badge-target        { background-color: rgba(40, 199, 111, 0.12); color: #28c76f; }
    .nav-tabs .nav-link.active { font-weight: 600; }
    .role-desc-table td { padding: 0.4rem 0.75rem; }
    .email-autocomplete { position: relative; }
    .email-autocomplete-results {
        position: absolute; z-index: 1050; width: 100%; max-height: 200px; overflow-y: auto;
        background: #fff; border: 1px solid #d8d6de; border-top: 0; border-radius: 0 0 .357rem .357rem;
        display: none;
    }
    .email-autocomplete-results .ac-item {
        padding: .5rem .75rem; cursor: pointer; border-bottom: 1px solid #f0f0f0;
    }
    .email-autocomplete-results .ac-item:hover { background: #f8f8f8; }
    .email-autocomplete-results .ac-item small { color: #999; }
    .email-autocomplete-results .ac-empty { padding: .5rem .75rem; color: #999; }
    /* Fix doubled sort icons: vendor CSS sets text content, Vuexy uses SVG background-image.
       Reset vendor text content so only the Vuexy feather icons render. */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after { content: '' !important; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0" id="page-title"></h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard" id="breadcrumb-home"></a></li>
                        <li class="breadcrumb-item active" id="breadcrumb-current"></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right" id="header-actions"></div>
    </div>
</div>

<div class="content-body">
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-members" data-toggle="tab" href="#panel-members" role="tab">
                                    <i data-feather="users" style="width:16px;height:16px;" class="mr-50"></i>
                                    <span id="tab-members-label"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-permissions" data-toggle="tab" href="#panel-permissions" role="tab">
                                    <i data-feather="shield" style="width:16px;height:16px;" class="mr-50"></i>
                                    <span id="tab-permissions-label"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-roles" data-toggle="tab" href="#panel-roles" role="tab">
                                    <i data-feather="key" style="width:16px;height:16px;" class="mr-50"></i>
                                    <span id="tab-roles-label"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Members Tab -->
                            <div class="tab-pane fade show active" id="panel-members" role="tabpanel">
                                <div class="card-datatable table-responsive pt-1">
                                    <table class="table" id="members-table">
                                        <thead>
                                            <tr>
                                                <th id="th-name"></th>
                                                <th>Email</th>
                                                <th id="th-role"></th>
                                                <th id="th-actions"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Permissions (ACL) Tab -->
                            <div class="tab-pane fade" id="panel-permissions" role="tabpanel">
                                <div class="d-flex align-items-center mb-1 pt-1">
                                    <label class="mr-1 mb-0 font-weight-bold" id="filter-scope-label"></label>
                                    <select class="form-control form-control-sm" id="scope-filter" style="width:auto;">
                                        <option value="" id="filter-all-option"></option>
                                        <option value="mrwifi:org">Organization</option>
                                        <option value="mrwifi:zone">Zone</option>
                                        <option value="mrwifi:location">Location</option>
                                        <option value="mrwifi:device">Device</option>
                                    </select>
                                </div>
                                <div class="card-datatable table-responsive">
                                    <table class="table" id="permissions-table">
                                        <thead>
                                            <tr>
                                                <th id="th-perm-user"></th>
                                                <th id="th-perm-role"></th>
                                                <th id="th-perm-target"></th>
                                                <th id="th-perm-target-id"></th>
                                                <th id="th-perm-actions"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Roles Tab -->
                            <div class="tab-pane fade" id="panel-roles" role="tabpanel">
                                <div id="roles-content" class="pt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Invite Member Modal -->
<div class="modal fade" id="invite-member-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="invite-modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="invite-member-form">
                <input type="hidden" id="invite-mode" value="" />
                <div class="modal-body">
                    <div class="form-group email-autocomplete">
                        <label for="invite-email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="invite-email" required autocomplete="off" placeholder="" id-placeholder="invite-email-placeholder" />
                        <div class="email-autocomplete-results" id="invite-email-results"></div>
                        <small class="text-muted" id="invite-email-hint"></small>
                    </div>
                    <div id="invite-selected-user" class="alert alert-success py-50 px-1 mb-1" style="display:none;">
                        <span id="invite-selected-name"></span>
                        <button type="button" class="close" id="invite-clear-selection"><span>&times;</span></button>
                    </div>
                    <div id="invite-name-fields" style="display:none;">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="invite-first-name" id="invite-first-name-label"></label>
                                    <input type="text" class="form-control" id="invite-first-name" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="invite-last-name" id="invite-last-name-label"></label>
                                    <input type="text" class="form-control" id="invite-last-name" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="invite-role" id="invite-role-label"></label>
                        <select class="form-control" id="invite-role" required></select>
                    </div>
                    <div class="form-group">
                        <label for="invite-scope" id="invite-scope-label"></label>
                        <select class="form-control" id="invite-scope" required>
                            <option value="mrwifi:org|*" id="scope-org-option"></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal" id="btn-cancel"></button>
                    <button type="submit" class="btn btn-primary" id="btn-invite-submit"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Permission Modal -->
<div class="modal fade" id="add-permission-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="add-perm-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="add-permission-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="perm-user-select" id="perm-user-label"></label>
                        <select class="form-control" id="perm-user-select" required>
                            <option value="" id="perm-user-placeholder"></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="perm-role" id="perm-role-label"></label>
                        <select class="form-control" id="perm-role" required></select>
                    </div>
                    <div class="form-group">
                        <label for="perm-scope" id="perm-scope-label"></label>
                        <select class="form-control" id="perm-scope" required>
                            <option value="mrwifi:org|*" id="perm-scope-org-option"></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal" id="perm-btn-cancel"></button>
                    <button type="submit" class="btn btn-primary" id="perm-btn-submit"></button>
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

<script>const locale = '{{ $locale }}';</script>
<script src="/assets/js/team.js?v={{ time() }}"></script>
@endpush
