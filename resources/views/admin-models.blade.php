@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $shopUrl = "/{$locale}/shop";
    $adminModelsJsT = [
        'locale' => $locale,
        'dashboard_url' => "/{$locale}/dashboard",

        'access_denied' => __('admin_models.js_access_denied'),
        'error_load_models' => __('admin_models.js_error_load_models'),
        'no_models' => __('admin_models.js_no_models'),

        'col_image' => __('admin_models.js_col_image'),
        'col_name' => __('admin_models.js_col_name'),
        'col_type' => __('admin_models.js_col_type'),
        'col_price' => __('admin_models.js_col_price'),
        'col_stock' => __('admin_models.js_col_stock'),
        'col_status' => __('admin_models.js_col_status'),
        'col_actions' => __('admin_models.js_col_actions'),

        'badge_out_of_stock' => __('admin_models.js_badge_out_of_stock'),
        'btn_edit' => __('admin_models.js_btn_edit'),
        'btn_delete' => __('admin_models.js_btn_delete'),

        'modal_add_title' => __('admin_models.modal_add_title'),
        'modal_edit_title' => __('admin_models.js_modal_edit_title'),
        'error_load_model' => __('admin_models.js_error_load_model'),

        'no_images' => __('admin_models.js_no_images'),
        'badge_primary' => __('admin_models.js_badge_primary'),
        'btn_set_primary' => __('admin_models.js_btn_set_primary'),

        'fill_required' => __('admin_models.js_fill_required'),
        'saved' => __('admin_models.js_saved'),
        'error_save' => __('admin_models.js_error_save'),

        'confirm_delete_model' => __('admin_models.js_confirm_delete_model'),
        'deleted' => __('admin_models.js_deleted'),
        'error_delete_model' => __('admin_models.js_error_delete_model'),

        'status_updated' => __('admin_models.js_status_updated'),
        'error_update_status' => __('admin_models.js_error_update_status'),

        'image_too_large' => __('admin_models.js_image_too_large'),
        'save_model_first' => __('admin_models.js_save_model_first'),
        'image_uploaded' => __('admin_models.js_image_uploaded'),
        'error_upload_image' => __('admin_models.js_error_upload_image'),

        'confirm_delete_image' => __('admin_models.js_confirm_delete_image'),
        'image_deleted' => __('admin_models.js_image_deleted'),
        'error_delete_image' => __('admin_models.js_error_delete_image'),

        'primary_set' => __('admin_models.js_primary_set'),
        'error_update_image' => __('admin_models.js_error_update_image'),
    ];
@endphp

@section('title', __('admin_models.page_title'))

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('admin_models.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $shopUrl }}">{{ __('shop.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('admin_models.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-3 col-12 text-right">
        <button class="btn btn-primary" onclick="showModelModal()">
            <i data-feather="plus"></i> {{ __('admin_models.btn_add_new') }}
        </button>
    </div>
</div>
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('admin_models.filter_heading') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select id="type-filter" class="form-control">
                        <option value="">{{ __('admin_models.filter_all_types') }}</option>
                        <option value="820">{{ __('admin_models.filter_type_820') }}</option>
                        <option value="835">{{ __('admin_models.filter_type_835') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="active-filter" class="form-control">
                        <option value="">{{ __('admin_models.filter_all_status') }}</option>
                        <option value="1">{{ __('common.active') }}</option>
                        <option value="0">{{ __('common.inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="{{ __('admin_models.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="loadModels()">{{ __('admin_models.btn_apply_filter') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="models-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div id="models-list"></div>

    <div id="model-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">{{ __('admin_models.modal_add_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="model-form">
                        <input type="hidden" id="model-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model-name">{{ __('admin_models.form_model_name') }} *</label>
                                    <input type="text" class="form-control" id="model-name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="model-type">{{ __('admin_models.form_device_type') }} *</label>
                                    <select class="form-control" id="model-type" required>
                                        <option value="">{{ __('admin_models.form_select_type') }}</option>
                                        <option value="820">820</option>
                                        <option value="835">835</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="model-price">{{ __('admin_models.form_price') }} *</label>
                                    <input type="number" class="form-control" id="model-price" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description-en">{{ __('admin_models.form_description_en') }} *</label>
                            <textarea class="form-control" id="description-en" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description-fr">{{ __('admin_models.form_description_fr') }} *</label>
                            <textarea class="form-control" id="description-fr" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="model-active" checked>
                                <label class="custom-control-label" for="model-active">{{ __('common.active') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sort-order">{{ __('admin_models.form_sort_order') }}</label>
                            <input type="number" class="form-control" id="sort-order" value="0" min="0">
                        </div>
                    </form>

                    <div id="edit-images-section" style="display: none;">
                        <hr>
                        <h5>{{ __('admin_models.form_product_images') }}</h5>
                        <div class="form-group">
                            <label for="image-upload">{{ __('admin_models.form_upload_image') }}</label>
                            <input type="file" class="form-control" id="image-upload" accept="image/*">
                            <small class="text-muted">{{ __('admin_models.form_upload_hint') }}</small>
                        </div>
                        <div id="images-list" class="row mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" onclick="saveModel()">{{ __('admin_models.btn_save_model') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.APP_I18N = window.APP_I18N || {};
    window.APP_I18N.admin_models = @json($adminModelsJsT);
</script>
<script src="/assets/js/admin-models.js?v={{ filemtime(public_path('assets/js/admin-models.js')) }}"></script>
@endpush
