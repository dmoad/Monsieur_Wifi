@extends('layouts.app')

@section('title', 'Accounts - Monsieur WiFi')

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
                <h2 class="content-header-title float-left mb-0">User Accounts</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Accounts</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-account">
                <i data-feather="user-plus" class="mr-25"></i>
                <span>Add New Account</span>
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
                        <h4 class="card-title">All User Accounts</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-accounts table" id="accounts-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Profile Picture</th>
                                        <th>Actions</th>
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
                <h4 class="modal-title" id="myModalLabel33">Add New Account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                    <label for="new-account-upload" class="btn btn-sm btn-primary mb-75 mr-75">Upload Profile Picture</label>
                                    <input type="file" id="new-account-upload" hidden accept="image/*" />
                                    <p class="mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="new-account-name" placeholder="Full Name" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="new-account-email" placeholder="Email" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-password">Password <span class="text-danger">*</span></label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="new-account-password" placeholder="Password" required />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Minimum 8 characters, must include letters, numbers and special characters</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-confirm-password">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="new-account-confirm-password" placeholder="Confirm Password" required />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-danger hidden" id="new-password-error-message">Passwords do not match</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="new-account-role">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="new-account-role" required>
                                    <option value="">Select Role</option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin" class="superadmin-only" style="display:none;">Super Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="create-account-btn">Create Account</button>
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
                <h4 class="modal-title" id="editUserModalLabel">Edit User Account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                    <label for="edit-user-upload" class="btn btn-sm btn-primary mb-75 mr-75">Upload Profile Picture</label>
                                    <input type="file" id="edit-user-upload" hidden accept="image/*" />
                                    <p class="mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-user-name" placeholder="Full Name" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit-user-email" placeholder="Email" required />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-password">New Password</label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="edit-user-password" placeholder="Leave blank to keep current password" />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-confirm-password">Confirm New Password</label>
                                <div class="input-group form-password-toggle">
                                    <input type="password" class="form-control" id="edit-user-confirm-password" placeholder="Confirm new password" />
                                    <div class="input-group-append">
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-danger hidden" id="edit-password-error-message">Passwords do not match</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="edit-user-role">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit-user-role" required>
                                    <option value="">Select Role</option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin" class="superadmin-only" style="display:none;">Super Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-user-btn">Update Account</button>
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
<script src="/app-assets/js/scripts/pages/app-user-list.js"></script>

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/accounts.js?v={{ time() }}"></script>
@endpush

@php
    $locale = 'en';
@endphp
