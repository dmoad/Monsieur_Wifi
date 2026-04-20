@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('accounts.page_title'))

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">

<style>
    .avatar-content svg {
        color: inherit;
        width: 24px !important;
        height: 24px !important;
        stroke-width: 2;
        display: block !important;
    }

    [data-feather] {
        display: inline-block !important;
        vertical-align: middle;
    }

    .avatar-sm {
        height: 32px;
        width: 32px;
    }

    .badge-role-admin {
        background-color: rgba(115, 103, 240, 0.12);
        color: #7367f0;
    }

    .badge-role-owner {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
    }

    .badge-light-secondary {
        background-color: rgba(108, 117, 125, 0.12);
        color: #6c757d;
    }

    .badge-role-superadmin {
        background-color: rgba(234, 84, 85, 0.12);
        color: #ea5455;
    }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('accounts.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('accounts.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-account">
                <i data-feather="user-plus" class="mr-25"></i>
                <span>{{ __('accounts.add_new_account') }}</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Accounts Table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('accounts.card_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-accounts table" id="accounts-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('accounts.col_name') }}</th>
                                        <th>{{ __('accounts.email_label') }}</th>
                                        <th>{{ __('accounts.col_role') }}</th>
                                        <th>{{ __('accounts.col_profile_picture') }}</th>
                                        <th>{{ __('accounts.col_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add New Account Modal -->
<div class="modal fade text-left" id="add-new-account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{ __('accounts.add_new_account') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="add-account-form">
                <div class="modal-body">
                    <!-- Profile Picture Upload Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="media">
                                <a href="javascript:void(0);" class="mr-25">
                                    <img src="/assets/avatar-default.jpg" id="new-account-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                </a>
                                <div class="media-body mt-75 ml-1">
                                    <label for="new-account-upload" class="btn btn-sm btn-primary mb-75 mr-75">{{ __('accounts.upload_profile_picture') }}</label>
                                    <input type="file" id="new-account-upload" hidden accept="image/*" />
                                    <p class="mb-0">{{ __('accounts.image_help') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-name">{{ __('accounts.full_name_label') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="new-account-name" placeholder="{{ __('accounts.full_name_placeholder') }}" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-email">{{ __('accounts.email_label') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="new-account-email" placeholder="{{ __('accounts.email_placeholder') }}" required />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('accounts.password_setup') }}</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons" id="password-method-toggle">
                                    <label class="btn btn-outline-primary active">
                                        <input type="radio" name="password-method" value="manual" checked> {{ __('accounts.set_password_btn') }}
                                    </label>
                                    <label class="btn btn-outline-primary">
                                        <input type="radio" name="password-method" value="email"> {{ __('accounts.send_verification_email_btn') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted mt-50">{{ __('accounts.password_method_help') }}</small>
                            </div>
                        </div>
                        <div id="manual-password-fields" class="col-12 row px-0 mx-0">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-password">{{ __('accounts.password_label') }} <span class="text-danger">*</span></label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="new-account-password" placeholder="{{ __('accounts.password_placeholder') }}" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">{{ __('accounts.password_min') }}</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="new-account-confirm-password">{{ __('accounts.confirm_password_label') }} <span class="text-danger">*</span></label>
                                    <div class="input-group form-password-toggle">
                                        <input type="password" class="form-control" id="new-account-confirm-password" placeholder="{{ __('accounts.confirm_password_placeholder') }}" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer">
                                                <i data-feather="eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-danger hidden" id="new-password-error-message">{{ __('accounts.passwords_do_not_match') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="create-account-btn">{{ __('accounts.create_account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade text-left" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editUserModalLabel">{{ __('accounts.edit_modal_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="edit-user-form">
                <div class="modal-body">
                    <!-- Profile Picture Upload Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="media">
                                <a href="javascript:void(0);" class="mr-25">
                                    <img src="/assets/avatar-default.jpg" id="edit-user-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                </a>
                                <div class="media-body mt-75 ml-1">
                                    <label for="edit-user-upload" class="btn btn-sm btn-primary mb-75 mr-75">{{ __('accounts.upload_profile_picture') }}</label>
                                    <input type="file" id="edit-user-upload" hidden accept="image/*" />
                                    <p class="mb-0">{{ __('accounts.image_help') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-name">{{ __('accounts.full_name_label') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-user-name" placeholder="{{ __('accounts.full_name_placeholder') }}" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-email">{{ __('accounts.email_label') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit-user-email" placeholder="{{ __('accounts.email_placeholder') }}" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-password">{{ __('accounts.new_password_label') }}</label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="edit-user-password" placeholder="{{ __('accounts.new_password_placeholder') }}" />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">{{ __('accounts.new_password_help') }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-confirm-password">{{ __('accounts.confirm_new_password_label') }}</label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="edit-user-confirm-password" placeholder="{{ __('accounts.confirm_new_password_placeholder') }}" />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-danger hidden" id="edit-password-error-message">{{ __('accounts.passwords_do_not_match') }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-role">{{ __('accounts.role_label') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit-user-role" required>
                                    <option value="">{{ __('accounts.select_role_option') }}</option>
                                    <option value="user">{{ __('accounts.role_user') }}</option>
                                    <option value="admin">{{ __('accounts.role_admin') }}</option>
                                    <option value="superadmin" class="superadmin-only" style="display:none;">{{ __('accounts.role_superadmin') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="update-user-btn">{{ __('accounts.update_account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/accounts.js?v={{ filemtime(public_path('assets/js/accounts.js')) }}"></script>
@endpush
