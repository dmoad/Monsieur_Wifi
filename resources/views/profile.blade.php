@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('profile.page_title'))

@push('styles')
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
                <h2 class="content-header-title float-left mb-0">{{ __('profile.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('profile.heading') }}</li>
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
                                        <img src="" id="account-upload-img" class="rounded mr-50" alt="{{ __('profile.profile_image_alt') }}" height="80" width="80" />
                                    </a>
                                    <div class="media-body mt-75 ml-1">
                                        <label for="account-upload" class="btn btn-sm btn-primary mb-75 mr-75">{{ __('profile.upload_new_photo') }}</label>
                                        <input type="file" id="account-upload" hidden accept="image/*" />
                                        <p>{{ __('profile.photo_help') }}</p>
                                    </div>
                                </div>

                                <form class="validate-form mt-2">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-name">{{ __('profile.full_name_label') }}</label>
                                                <input type="text" class="form-control" id="account-name" name="name" placeholder="{{ __('profile.full_name_placeholder') }}" value="{{ __('profile.full_name_value') }}" />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-e-mail">{{ __('profile.email_label') }}</label>
                                                <input type="email" class="form-control" id="account-e-mail" name="email" placeholder="{{ __('profile.email_placeholder') }}" value="{{ __('profile.email_value') }}" />
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-new-password1">{{ __('profile.new_password_label') }}</label>
                                                <div class="input-group form-password-toggle input-group-merge">
                                                    <input type="password" id="account-new-password1" name="new-password1" class="form-control" placeholder="{{ __('profile.new_password_placeholder') }}" />
                                                    <div class="input-group-append">
                                                        <div class="input-group-text cursor-pointer">
                                                            <i data-feather="eye"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="form-text text-bold">{{ __('profile.new_password_help_blank') }}</small>
                                                <small class="form-text text-muted">{{ __('profile.new_password_help_rules') }}</small>
                                                <small class="form-text text-danger hidden" id="password-error-message">{{ __('profile.passwords_do_not_match') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="account-retype-new-password1">{{ __('profile.confirm_new_password_label') }}</label>
                                                <div class="input-group form-password-toggle input-group-merge">
                                                    <input type="password" class="form-control" id="account-retype-new-password1" name="confirm-new-password1" placeholder="{{ __('profile.confirm_new_password_placeholder') }}" />
                                                    <div class="input-group-append">
                                                        <div class="input-group-text cursor-pointer"><i data-feather="eye"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mt-1 mr-1" id="save-profile-btn">{{ __('profile.save_changes') }}</button>
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
                        <h4 class="card-title"><i data-feather="credit-card" class="mr-50"></i> {{ __('profile.subscription_heading') }}</h4>
                    </div>
                    <div class="card-body" id="subscription-section">
                        <div class="text-center py-2">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">{{ __('common.loading') }}</span>
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

<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/profile.js?v={{ time() }}"></script>
@endpush
