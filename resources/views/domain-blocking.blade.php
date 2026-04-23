@extends('layouts.app')

@php
    $locale = app()->getLocale();
@endphp

@section('title', __('domain_blocking.page_title'))

@push('styles')
<style>
    [data-feather] {
        display: inline-block !important;
        vertical-align: middle;
    }

    .badge-category-adult {
        background-color: rgba(234, 84, 85, 0.12);
        color: #ea5455;
    }
    .badge-category-gambling {
        background-color: rgba(255, 159, 67, 0.12);
        color: #ff9f43;
    }
    .badge-category-malware {
        background-color: rgba(130, 28, 128, 0.12);
        color: #821c80;
    }
    .badge-category-social {
        background-color: rgba(0, 137, 255, 0.12);
        color: #0089ff;
    }
    .badge-category-streaming {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
    }
    .badge-category-custom {
        background-color: var(--mw-bg-muted);
        color: var(--mw-text-secondary);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Compact category cards — tighter than Bootstrap card-in-card, matches the mockup's .catcard sizing. */
    .db-catcard {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: var(--mw-bg-surface);
        border: 1px solid var(--mw-border-light);
        border-radius: var(--mw-radius-md);
        margin-bottom: 12px;
    }
    .db-catcard-text {
        flex: 1;
        min-width: 0;
    }
    .db-catcard-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--mw-text-primary);
        line-height: 1.3;
    }
    .db-catcard-count {
        font-size: 11px;
        color: var(--mw-text-muted);
        line-height: 1.3;
        margin-top: 2px;
    }

    /* Domain list table */
    .db-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .db-table thead th {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--mw-text-muted);
        text-align: left;
        padding: 10px var(--mw-space-lg);
        border-bottom: 1px solid var(--mw-border-light);
    }
    .db-table tbody tr {
        border-bottom: 1px solid var(--mw-border-light);
        cursor: pointer;
        transition: background 0.12s;
    }
    .db-table tbody tr:last-child { border-bottom: none; }
    .db-table tbody tr:hover { background: var(--mw-bg-hover); }
    .db-table td { padding: var(--mw-space-md) var(--mw-space-lg); vertical-align: middle; color: var(--mw-text-secondary); }
    .db-table td.db-col-actions { text-align: right; width: 1%; white-space: nowrap; }

    .db-list-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--mw-space-md) var(--mw-space-xl);
        border-bottom: 1px solid var(--mw-border-light);
    }
    .db-list-title { font-size: 15px; font-weight: 600; color: var(--mw-text-primary); }
    .db-list-tools { display: flex; align-items: center; gap: var(--mw-space-sm); }
    .db-search {
        width: 220px;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid var(--mw-border-light);
        border-radius: var(--mw-radius-sm);
        background: var(--mw-bg-surface);
        color: var(--mw-text-primary);
    }
    .db-search:focus { outline: none; border-color: var(--mw-primary); }

    .db-kebab-wrap { position: relative; display: inline-block; }
    .db-kebab-btn {
        width: 32px; height: 32px;
        border: 1px solid var(--mw-border);
        background: var(--mw-bg-surface);
        border-radius: var(--mw-radius-sm);
        display: flex; align-items: center; justify-content: center;
        color: var(--mw-text-secondary);
        cursor: pointer;
        transition: background 0.12s, color 0.12s, border-color 0.12s;
        padding: 0;
    }
    .db-kebab-btn:hover { background: var(--mw-primary-tint); border-color: var(--mw-primary); color: var(--mw-primary); }
    .db-menu {
        display: none; position: absolute; top: calc(100% + 4px); right: 0;
        background: var(--mw-bg-surface); border: 1px solid var(--mw-border);
        border-radius: var(--mw-radius-md); box-shadow: var(--mw-shadow-elevated);
        min-width: 140px; z-index: 100; padding: 4px 0;
    }
    .db-menu.open { display: block; }
    .db-menu-item {
        display: flex; align-items: center; gap: var(--mw-space-sm);
        width: 100%; padding: 7px 14px; border: none; background: transparent;
        font-size: 13px; color: var(--mw-text-secondary); cursor: pointer;
        text-align: left; transition: background 0.1s, color 0.1s; font-family: inherit;
    }
    .db-menu-item:hover { background: var(--mw-bg-hover); color: var(--mw-text-primary); }
    .db-menu-danger { color: var(--mw-danger) !important; }
    .db-menu-danger:hover { background: rgba(220,38,38,0.06) !important; }
    .db-menu-divider { height: 1px; background: var(--mw-border-light); margin: 3px 0; }

</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('domain_blocking.heading') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('domain_blocking.heading') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#domain-blocking-info">
                <i data-feather="info" class="mr-25"></i>
                <span>{{ __('domain_blocking.info_btn') }}</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Blocking Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('domain_blocking.categories_title') }}</h4>
                    <p class="card-text">{{ __('domain_blocking.categories_help') }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-danger"><i data-feather="octagon"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_adult') }}</div>
                                    <div class="db-catcard-count">1,024 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-adult" checked>
                                    <label class="custom-control-label" for="category-adult"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-warning"><i data-feather="dollar-sign"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_gambling') }}</div>
                                    <div class="db-catcard-count">856 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-gambling" checked>
                                    <label class="custom-control-label" for="category-gambling"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-primary"><i data-feather="shield-off"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_malware') }}</div>
                                    <div class="db-catcard-count">2,345 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-malware" checked>
                                    <label class="custom-control-label" for="category-malware"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-info"><i data-feather="users"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_social') }}</div>
                                    <div class="db-catcard-count">342 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-social">
                                    <label class="custom-control-label" for="category-social"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-success"><i data-feather="film"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_streaming') }}</div>
                                    <div class="db-catcard-count">128 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-streaming">
                                    <label class="custom-control-label" for="category-streaming"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="db-catcard">
                                <span class="mw-stat-icon mw-stat-icon-muted"><i data-feather="tag"></i></span>
                                <div class="db-catcard-text">
                                    <div class="db-catcard-title">{{ __('domain_blocking.cat_custom') }}</div>
                                    <div class="db-catcard-count">43 {{ __('domain_blocking.domains_suffix') }}</div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="category-custom" checked>
                                    <label class="custom-control-label" for="category-custom"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain List Table -->
    <div class="card">
        <div class="db-list-head">
            <span class="db-list-title">{{ __('domain_blocking.blocked_domains_title') }}</span>
            <div class="db-list-tools">
                <input type="text" id="db-search" class="db-search" placeholder="{{ __('domain_blocking.search_placeholder') }}" autocomplete="off">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-new-domain">
                    <i data-feather="plus" class="mr-25"></i>
                    <span>{{ __('domain_blocking.add_domain') }}</span>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="db-table">
                <thead>
                    <tr>
                        <th>{{ __('domain_blocking.col_domain') }}</th>
                        <th>{{ __('domain_blocking.col_category') }}</th>
                        <th>{{ __('domain_blocking.col_added_date') }}</th>
                        <th>{{ __('domain_blocking.col_last_updated') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="db-domains-tbody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add New Domain Modal -->
<div class="modal fade text-left" id="add-new-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel34">{{ __('domain_blocking.add_modal_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="domain-name">{{ __('domain_blocking.domain_label') }}</label>
                        <input type="text" class="form-control" id="domain-name" placeholder="{{ __('domain_blocking.domain_placeholder') }}" />
                        <small class="form-text text-muted">{{ __('domain_blocking.domain_help') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="domain-category">{{ __('domain_blocking.category_label') }}</label>
                        <select class="form-control" id="domain-category">
                            <option value="1">{{ __('domain_blocking.cat_adult') }}</option>
                            <option value="2">{{ __('domain_blocking.cat_gambling') }}</option>
                            <option value="3">{{ __('domain_blocking.cat_malware') }}</option>
                            <option value="4">{{ __('domain_blocking.cat_social') }}</option>
                            <option value="5">{{ __('domain_blocking.cat_streaming') }}</option>
                            <option value="6">{{ __('domain_blocking.cat_custom') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="domain-notes">{{ __('domain_blocking.notes_label') }}</label>
                        <textarea class="form-control" id="domain-notes" rows="3" placeholder="{{ __('domain_blocking.notes_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('domain_blocking.add_btn') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Domain Modal -->
<div class="modal fade text-left" id="edit-domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel35">{{ __('domain_blocking.edit_modal_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-domain-name">{{ __('domain_blocking.domain_label') }}</label>
                        <input type="text" class="form-control" id="edit-domain-name" readonly />
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-category">{{ __('domain_blocking.category_label') }}</label>
                        <select class="form-control" id="edit-domain-category">
                            <option value="1">{{ __('domain_blocking.cat_adult') }}</option>
                            <option value="2">{{ __('domain_blocking.cat_gambling') }}</option>
                            <option value="3">{{ __('domain_blocking.cat_malware') }}</option>
                            <option value="4">{{ __('domain_blocking.cat_social') }}</option>
                            <option value="5">{{ __('domain_blocking.cat_streaming') }}</option>
                            <option value="6">{{ __('domain_blocking.cat_custom') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-domain-notes">{{ __('domain_blocking.notes_label') }}</label>
                        <textarea class="form-control" id="edit-domain-notes" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox hidden">
                            <input type="checkbox" class="custom-control-input" id="edit-block-subdomains" checked>
                            <label class="custom-control-label" for="edit-block-subdomains">{{ __('domain_blocking.block_all_subdomains') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('domain_blocking.block_subdomains_help') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('domain_blocking.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Domain Blocking Info Modal -->
<div class="modal fade text-left" id="domain-blocking-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel37" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel37">
                    <i data-feather="info" class="mr-1"></i>
                    {{ __('domain_blocking.info_modal_title') }}
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-none border-left-primary">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="shield" class="mr-1"></i>
                                    {{ __('domain_blocking.what_is_title') }}
                                </h5>
                                <p class="card-text">{{ __('domain_blocking.what_is_body') }}</p>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-info mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="plus-circle" class="mr-1"></i>
                                    {{ __('domain_blocking.how_to_add_title') }}
                                </h5>
                                <ol class="mb-0">
                                    <li><strong>{{ __('domain_blocking.how_to_single') }}</strong> {{ __('domain_blocking.how_to_single_body') }}</li>
                                    <li><strong>{{ __('domain_blocking.how_to_categories') }}</strong> {{ __('domain_blocking.how_to_categories_body') }}</li>
                                </ol>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-warning mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="alert-triangle" class="mr-1"></i>
                                    {{ __('domain_blocking.why_multiple_title') }}
                                </h5>
                                <p class="card-text">{{ __('domain_blocking.why_multiple_body') }}</p>

                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('domain_blocking.service_col') }}</th>
                                                <th>{{ __('domain_blocking.domains_to_block_col') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Facebook</strong></td>
                                                <td>facebook.com, fb.com, fbcdn.net, fb.me, messenger.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>YouTube</strong></td>
                                                <td>youtube.com, youtu.be, ytimg.com, googlevideo.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Instagram</strong></td>
                                                <td>instagram.com, cdninstagram.com, ig.me</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Twitter/X</strong></td>
                                                <td>twitter.com, x.com, t.co, twimg.com</td>
                                            </tr>
                                            <tr>
                                                <td><strong>TikTok</strong></td>
                                                <td>tiktok.com, tiktokv.com, tiktokcdn.com, musical.ly</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-none border-left-success mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i data-feather="check-circle" class="mr-1"></i>
                                    {{ __('domain_blocking.best_practices_title') }}
                                </h5>
                                <ul class="mb-0">
                                    <li><strong>{{ __('domain_blocking.bp_use_cats') }}</strong> {{ __('domain_blocking.bp_use_cats_body') }}</li>
                                    <li><strong>{{ __('domain_blocking.bp_research') }}</strong> {{ __('domain_blocking.bp_research_body') }}</li>
                                    <li><strong>{{ __('domain_blocking.bp_test') }}</strong> {{ __('domain_blocking.bp_test_body') }}</li>
                                    <li><strong>{{ __('domain_blocking.bp_updates') }}</strong> {{ __('domain_blocking.bp_updates_body') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <div class="alert-body">
                                <i data-feather="zap" class="mr-1"></i>
                                <strong>{{ __('domain_blocking.pro_tip') }}</strong> {{ __('domain_blocking.pro_tip_body') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('domain_blocking.got_it') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const locale = '{{ $locale }}';
</script>
<script src="/assets/js/domain-blocking.js?v={{ filemtime(public_path('assets/js/domain-blocking.js')) }}"></script>
@endpush
