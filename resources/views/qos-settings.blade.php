@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $qosT = [
        'be_placeholder' => __('qos_settings.be_placeholder'),
        'no_domains' => __('qos_settings.no_domains'),
        'add_domain_placeholder' => __('qos_settings.add_domain_placeholder'),
        'add_btn' => __('qos_settings.add_btn'),
        'remove_title' => __('qos_settings.remove_title'),
        'load_failed' => __('qos_settings.load_failed'),
        'generic_error' => __('qos_settings.generic_error'),
        'domain_empty' => __('qos_settings.domain_empty'),
        'domain_added' => __('qos_settings.domain_added'),
        'domain_removed' => __('qos_settings.domain_removed'),
        'confirm_remove' => __('qos_settings.confirm_remove'),
        'per_network_hint' => __('qos_settings.per_network_hint'),
        'class_labels' => [
            'EF' => __('qos_settings.class_label_EF'),
            'AF41' => __('qos_settings.class_label_AF41'),
            'BE' => __('qos_settings.class_label_BE'),
            'CS1' => __('qos_settings.class_label_CS1'),
        ],
        'priority_desc' => [
            'EF' => __('qos_settings.priority_desc_EF'),
            'AF41' => __('qos_settings.priority_desc_AF41'),
            'BE' => __('qos_settings.priority_desc_BE'),
            'CS1' => __('qos_settings.priority_desc_CS1'),
        ],
    ];
@endphp

@section('title', __('qos_settings.page_title'))

@push('styles')
<style>
    .qos-class-card {
        border-radius: 10px;
        border: 1px solid var(--mw-border-light);
        background: var(--mw-bg-surface);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .qos-class-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--mw-border-light);
    }
    .qos-class-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }
    .qos-badge-ef   { background: rgba(234,84,85,0.12);  color: #ea5455; }
    .qos-badge-af41 { background: rgba(255,159,67,0.12); color: #ff9f43; }
    .qos-badge-be   { background: rgba(40,199,111,0.12); color: #28c76f; }
    .qos-badge-cs1  { background: rgba(99,102,241,0.12);color: var(--mw-primary); }
    .qos-class-meta { flex: 1; min-width: 0; }
    .qos-class-meta h6 { margin: 0 0 1px; font-weight: 700; font-size: 1rem; }
    .qos-class-meta .priority-desc { color: var(--mw-text-secondary); font-size: 0.78rem; margin-bottom: 3px; }
    .qos-class-meta .tech-ids { font-size: 0.7rem; color: var(--mw-text-muted); letter-spacing: 0.3px; }
    .qos-dscp-pill { display: none; }
    .qos-class-body { padding: 1rem 1.25rem; }
    .domain-list { list-style: none; padding: 0; margin: 0 0 1rem; }
    .domain-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-family: 'SFMono-Regular', Consolas, monospace;
        background: var(--mw-bg-muted);
        border: 1px solid var(--mw-border-light);
        margin-bottom: 0.4rem;
    }
    .domain-list li .btn-remove {
        background: none;
        border: none;
        color: var(--mw-text-muted);
        cursor: pointer;
        padding: 0 4px;
        font-size: 1rem;
        line-height: 1;
        transition: color 0.15s;
    }
    .domain-list li .btn-remove:hover { color: var(--mw-danger); }
    .add-domain-form { display: flex; gap: 0.5rem; }
    .add-domain-form input { flex: 1; }
    .be-placeholder {
        color: var(--mw-text-secondary);
        font-size: 0.85rem;
        font-style: italic;
        padding: 0.5rem 0;
    }
    .spinner-overlay { display: none; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ __('qos_settings.heading') }}</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('qos_settings.breadcrumb') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="row">
        <div class="col-12">

            <!-- Info banner -->
            <div class="alert alert-info alert-dismissible" role="alert">
                <div class="d-flex align-items-start">
                    <i data-feather="info" class="mr-2 mt-1" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <div>
                        <strong>{{ __('qos_settings.info_title') }}</strong> {!! __('qos_settings.info_body_html') !!}
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>

            <!-- Class cards -->
            <div id="qos-classes-container">
                <div class="text-center py-5" id="qos-loading">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">{{ __('qos_settings.loading_classes') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.QOS_T = {!! json_encode($qosT) !!};
const T = window.QOS_T;

const API      = '/api';

// ── Helpers ──────────────────────────────────────────────────────────────────
function reRenderFeather() {
    if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
}

function authHeaders() {
    return { Authorization: 'Bearer ' + (typeof UserManager !== 'undefined' ? UserManager.getToken() : '') };
}

async function apiFetch(url, opts = {}) {
    const res = await fetch(url, {
        headers: { 'Content-Type': 'application/json', ...authHeaders(), ...(opts.headers || {}) },
        ...opts,
    });
    if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw Object.assign(new Error(body.message || `HTTP ${res.status}`), { status: res.status, body });
    }
    return res.json();
}

function handleApiError(err, ctx) {
    const msg = err?.body?.message || err?.message || T.generic_error;
    console.error(ctx, err);
    toastr.error(msg);
}

// ── Class metadata (technical IDs only; labels and priority descriptions come from T) ──
const CLASS_META = {
    EF:   { badge: 'qos-badge-ef',   techIds: 'EF · DSCP 46' },
    AF41: { badge: 'qos-badge-af41', techIds: 'AF41 · DSCP 34' },
    BE:   { badge: 'qos-badge-be',   techIds: 'BE · DSCP 0' },
    CS1:  { badge: 'qos-badge-cs1',  techIds: 'CS1 · DSCP 8' },
};

// ── Render ────────────────────────────────────────────────────────────────────
function renderClasses(classes) {
    const $container = $('#qos-classes-container');
    $container.empty();

    const $row = $('<div class="row"></div>');

    classes.forEach(cls => {
        const meta       = CLASS_META[cls.id] || {};
        const isBE       = cls.id === 'BE';
        const label      = T.class_labels[cls.id] || cls.label;
        const priorityDesc = T.priority_desc[cls.id] || '';

        const domainItems = isBE
            ? `<p class="be-placeholder">${escHtml(T.be_placeholder)}</p>`
            : `<p class="text-muted" style="font-size:0.85rem;padding:0.25rem 0;">${escHtml(T.per_network_hint)}</p>`;

        const card = `
            <div class="col-md-6 col-xl-3">
                <div class="qos-class-card">
                    <div class="qos-class-header">
                        <div class="qos-class-badge ${meta.badge}">${escHtml(label.charAt(0))}</div>
                        <div class="qos-class-meta">
                            <h6>${escHtml(label)}</h6>
                            <div class="priority-desc">${escHtml(priorityDesc)}</div>
                            <div class="tech-ids">${meta.techIds || ''}</div>
                        </div>
                    </div>
                    <div class="qos-class-body">
                        <div class="domain-list-readonly">${domainItems}</div>
                    </div>
                </div>
            </div>`;

        $row.append(card);
    });

    $container.append($row);
    reRenderFeather();
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Load classes ──────────────────────────────────────────────────────────────
async function loadClasses() {
    try {
        const res = await apiFetch(`${API}/qos/classes`);
        $('#qos-loading').remove();
        renderClasses(res.data);
    } catch (err) {
        handleApiError(err, 'loadClasses');
        $('#qos-loading').html(`<p class="text-danger">${escHtml(T.load_failed)}</p>`);
    }
}

// ── Init ──────────────────────────────────────────────────────────────────────
$(document).ready(function () {
    loadClasses();
});
</script>
@endpush
