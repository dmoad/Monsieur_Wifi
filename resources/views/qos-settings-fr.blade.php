@extends('layouts.app')

@section('title', 'Paramètres QoS - Monsieur WiFi')

@push('styles')
<style>
    .qos-class-card {
        border-radius: 10px;
        border: 1px solid #e4e9f0;
        background: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .qos-class-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e4e9f0;
        background: #f9fafb;
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
    .qos-badge-cs1  { background: rgba(130,128,255,0.12);color: #7367f0; }
    .qos-class-meta { flex: 1; min-width: 0; }
    .qos-class-meta h6 { margin: 0 0 1px; font-weight: 700; font-size: 1rem; }
    .qos-class-meta .priority-desc { color: #6e6b7b; font-size: 0.78rem; margin-bottom: 3px; }
    .qos-class-meta .tech-ids { font-size: 0.7rem; color: #aaa; letter-spacing: 0.3px; }
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
        background: #f9fafb;
        border: 1px solid #e4e9f0;
        margin-bottom: 0.4rem;
    }
    .domain-list li .btn-remove {
        background: none;
        border: none;
        color: #aaa;
        cursor: pointer;
        padding: 0 4px;
        font-size: 1rem;
        line-height: 1;
        transition: color 0.15s;
    }
    .domain-list li .btn-remove:hover { color: #ea5455; }
    .add-domain-form { display: flex; gap: 0.5rem; }
    .add-domain-form input { flex: 1; }
    .be-placeholder {
        color: #6e6b7b;
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
                <h2 class="content-header-title float-left mb-0">Priorisation du trafic (QoS)</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/fr/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Paramètres QoS</li>
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
                        <strong>Comment fonctionne la QoS :</strong> Le trafic est classifié par SNI (nom d'hôte) sur le routeur et marqué avec une priorité DSCP.
                        Les quatre classes ci-dessous sont fixes — seules leurs listes de domaines peuvent être modifiées ici.
                        L'activation/désactivation par emplacement se configure dans la page Paramètres de l'emplacement.
                        Le trafic non classifié tombe automatiquement dans la classe <strong>Par défaut (BE)</strong>.
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>

            <!-- Class cards -->
            <div id="qos-classes-container">
                <div class="text-center py-5" id="qos-loading">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Chargement des classes QoS…</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/assets/js/config.js"></script>
<script>
const API      = '/api';
const IS_SUPER = (typeof UserManager !== 'undefined' && UserManager.getUser()?.role === 'superadmin');

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
    const msg = err?.body?.message || err?.message || 'Une erreur est survenue.';
    console.error(ctx, err);
    toastr.error(msg);
}

// ── Class metadata ────────────────────────────────────────────────────────────
const CLASS_META = {
    EF:   { badge: 'qos-badge-ef',   priorityDesc: 'Priorité maximale — latence minimale garantie',         techIds: 'EF · DSCP 46' },
    AF41: { badge: 'qos-badge-af41', priorityDesc: 'Haute priorité — inférieure au Temps réel',             techIds: 'AF41 · DSCP 34' },
    BE:   { badge: 'qos-badge-be',   priorityDesc: 'Priorité normale — trafic non classifié &amp; QoS désactivée', techIds: 'BE · DSCP 0' },
    CS1:  { badge: 'qos-badge-cs1',  priorityDesc: 'Priorité minimale — différé en cas de congestion',      techIds: 'CS1 · DSCP 8' },
};

// French labels for class IDs (fallback if API doesn't return translated labels)
const CLASS_LABELS_FR = {
    EF:   'Temps réel',
    AF41: 'Streaming',
    BE:   'Par défaut',
    CS1:  'Arrière-plan',
};

// ── Render ────────────────────────────────────────────────────────────────────
function renderClasses(classes) {
    const $container = $('#qos-classes-container');
    $container.empty();

    const $row = $('<div class="row"></div>');
    const isSuperAdmin = typeof UserManager !== 'undefined' && UserManager.getUser()?.role === 'superadmin';

    classes.forEach(cls => {
        const meta       = CLASS_META[cls.id] || {};
        const isBE       = cls.id === 'BE';
        const canEdit    = isSuperAdmin && !isBE;
        const label      = CLASS_LABELS_FR[cls.id] || cls.label;

        const domainItems = isBE
            ? `<p class="be-placeholder">Aucune règle de domaine — tout le trafic non classifié tombe dans cette classe automatiquement.</p>`
            : (cls.domains.length
                ? cls.domains.map(d => `
                    <li data-domain="${escHtml(d)}">
                        <span>${escHtml(d)}</span>
                        ${canEdit ? `<button class="btn-remove" data-class="${cls.id}" data-domain="${escHtml(d)}" title="Supprimer">×</button>` : ''}
                    </li>`).join('')
                : `<p class="text-muted" style="font-size:0.85rem;font-style:italic;padding:0.25rem 0;">Aucun domaine configuré.</p>`
            );

        const addForm = canEdit ? `
            <div class="add-domain-form mt-2">
                <input type="text" class="form-control form-control-sm add-domain-input"
                       placeholder="ex. *.exemple.com" data-class="${cls.id}">
                <button class="btn btn-primary btn-sm add-domain-btn" data-class="${cls.id}">
                    <i data-feather="plus" style="width:14px;height:14px;"></i> Ajouter
                </button>
            </div>` : '';

        const card = `
            <div class="col-md-6 col-xl-3">
                <div class="qos-class-card">
                    <div class="qos-class-header">
                        <div class="qos-class-badge ${meta.badge}">${escHtml(label.charAt(0))}</div>
                        <div class="qos-class-meta">
                            <h6>${escHtml(label)}</h6>
                            <div class="priority-desc">${meta.priorityDesc || ''}</div>
                            <div class="tech-ids">${meta.techIds || ''}</div>
                        </div>
                    </div>
                    <div class="qos-class-body">
                        <ul class="domain-list" id="domain-list-${cls.id}">${domainItems}</ul>
                        ${addForm}
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
        $('#qos-loading').html('<p class="text-danger">Impossible de charger les classes QoS.</p>');
    }
}

// ── Add domain ────────────────────────────────────────────────────────────────
async function addDomain(classId, domain) {
    if (!domain.trim()) { toastr.warning('Le domaine ne peut pas être vide.'); return; }
    try {
        await apiFetch(`${API}/qos/classes/${classId}/domains`, {
            method: 'POST',
            body: JSON.stringify({ domain: domain.trim() }),
        });
        toastr.success(`Domaine ajouté à ${classId}.`);
        loadClasses();
    } catch (err) {
        handleApiError(err, 'addDomain');
    }
}

// ── Remove domain ─────────────────────────────────────────────────────────────
async function removeDomain(classId, domain) {
    try {
        await apiFetch(`${API}/qos/classes/${classId}/domains/${encodeURIComponent(domain)}`, {
            method: 'DELETE',
        });
        toastr.success(`Domaine supprimé de ${classId}.`);
        loadClasses();
    } catch (err) {
        handleApiError(err, 'removeDomain');
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────
$(document).on('click', '.add-domain-btn', function () {
    const classId = $(this).data('class');
    const $input  = $(`.add-domain-input[data-class="${classId}"]`);
    addDomain(classId, $input.val());
});

$(document).on('keydown', '.add-domain-input', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const classId = $(this).data('class');
        addDomain(classId, $(this).val());
    }
});

$(document).on('click', '.btn-remove', function () {
    const classId = $(this).data('class');
    const domain  = $(this).data('domain');
    if (confirm(`Supprimer "${domain}" de ${classId} ?`)) {
        removeDomain(classId, domain);
    }
});

// ── Init ──────────────────────────────────────────────────────────────────────
$(document).ready(function () {
    loadClasses();
});
</script>
@endpush
