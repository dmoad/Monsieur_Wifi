@extends('layouts.app')

@section('title', 'Profile - Monsieur WiFi')

@push('styles')
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/select/select2.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-pickadate.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/forms/form-validation.css">

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
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Profile</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="page-account-settings">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                <div class="media">
                                    <a href="javascript:void(0);" class="mr-25">
                                        <img src="" id="account-upload-img" class="rounded mr-50" alt="profile image" height="80" width="80" />
                                    </a>
                                    <div class="media-body mt-75 ml-1">
                                        <label for="account-upload" class="btn btn-sm btn-primary mb-75 mr-75">Upload New Photo</label>
                                        <input type="file" id="account-upload" hidden accept="image/*" />
                                        <p>Allowed JPG or PNG. Max size of 2MB</p>
                                    </div>
                                </div>

                                <form class="validate-form mt-2">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-name">Full Name</label>
                                                <input type="text" class="form-control" id="account-name" name="name" placeholder="Name" value="Your Name" />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-e-mail">Email</label>
                                                <input type="email" class="form-control" id="account-e-mail" name="email" placeholder="Email" value="your@email.com" />
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <small class="form-text text-muted">Name and email are managed by your identity provider.</small>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <a href="{{ config('zitadel.issuer') }}/ui/console/users/me" target="_blank" class="btn btn-outline-primary mr-1">
                                                <i data-feather="external-link" class="mr-50"></i> Manage Account
                                            </a>
                                            <a href="/auth/switch" class="btn btn-outline-secondary">
                                                <i data-feather="repeat" class="mr-50"></i> Switch Account
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Section -->
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i data-feather="credit-card" class="mr-50"></i> Subscription</h4>
                    </div>
                    <div class="card-body" id="subscription-section">
                        <div class="text-center py-2">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/app-assets/js/scripts/pages/page-account-settings.js"></script>

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/profile.js?v={{ time() }}"></script>
@endpush

@php
    $locale = 'en';
@endphp
