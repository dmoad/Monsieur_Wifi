@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('accounts.page_title'))

@push('styles')
<style>
    .ac-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .ac-table thead th {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--mw-text-muted);
        text-align: left;
        padding: 10px var(--mw-space-lg);
        border-bottom: 1px solid var(--mw-border-light);
    }
    .ac-table tbody tr {
        border-bottom: 1px solid var(--mw-border-light);
        transition: background 0.12s;
    }
    .ac-table tbody tr:last-child { border-bottom: none; }
    .ac-table tbody tr:hover { background: var(--mw-bg-page); }
    .ac-table td { padding: 10px var(--mw-space-lg); vertical-align: middle; }

    .ac-list-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--mw-space-md) var(--mw-space-xl);
        border-bottom: 1px solid var(--mw-border-light);
    }
    .ac-list-title { font-size: 15px; font-weight: 600; color: var(--mw-text-primary); }
    .ac-search {
        width: 220px;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid var(--mw-border-light);
        border-radius: var(--mw-radius-sm);
        background: var(--mw-bg-surface);
        color: var(--mw-text-primary);
    }
    .ac-search:focus { outline: none; border-color: var(--mw-primary); }

    .ac-user-cell { display: flex; align-items: center; gap: 10px; }
    .ac-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        object-fit: cover; flex-shrink: 0;
        background: var(--mw-bg-muted);
    }
    .ac-name { font-weight: 500; color: var(--mw-text-primary); }
    .ac-email { font-size: 12px; color: var(--mw-text-secondary); }

    .badge-role-admin      { background: rgba(99,102,241,0.12); color: var(--mw-primary); }
    .badge-role-owner      { background: rgba(40,199,111,0.12);  color: #28c76f; }
    .badge-role-superadmin { background: rgba(234,84,85,0.12);   color: #ea5455; }
    .badge-role-user       { background: rgba(108,117,125,0.12); color: #6c757d; }
    .ac-role-badge {
        font-size: 11px; font-weight: 600; padding: 3px 8px;
        border-radius: 20px; white-space: nowrap;
    }

    .ac-empty-row td { text-align: center; padding: 32px; color: var(--mw-text-secondary); }
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
    <div class="card">
        <div class="ac-list-head">
            <span class="ac-list-title">{{ __('accounts.card_title') }}</span>
            <input type="text" id="search-accounts" class="ac-search" placeholder="{{ __('accounts.search_placeholder') }}">
        </div>
        <div class="table-responsive">
            <table class="ac-table">
                <thead>
                    <tr>
                        <th>{{ __('accounts.col_name') }}</th>
                        <th>{{ __('accounts.col_role') }}</th>
                        <th>{{ __('accounts.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody id="accounts-tbody">
                    <tr class="ac-empty-row"><td colspan="3">{{ __('accounts.loading') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
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
<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/accounts.js?v={{ filemtime(public_path('assets/js/accounts.js')) }}"></script>
@endpush
