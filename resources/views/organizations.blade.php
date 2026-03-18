@extends('layouts.app')

@section('title', $locale === 'fr' ? 'Organisations - Monsieur WiFi' : 'Organizations - Monsieur WiFi')

@push('styles')
<style>
    .org-card {
        border: 1px solid #ebe9f1;
        border-radius: 8px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }
    .org-card:hover { border-color: #7367f0; box-shadow: 0 2px 8px rgba(115,103,240,.1); }
    .org-card.active { border-color: #7367f0; background: #7367f014; }
    .org-card .org-icon {
        width: 40px; height: 40px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; flex-shrink: 0; color: #fff; line-height: 1;
    }
    .org-card .org-info { min-width: 0; flex: 1; }
    .org-card .org-info h6 { margin: 0; font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .org-card .org-info small { font-size: 12px; color: #a0a0a0; }
    .org-card .org-role { font-size: 11px; font-weight: 600; text-transform: capitalize; flex-shrink: 0; }
    .org-card .org-badge-current { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; padding: 2px 6px; border-radius: 4px; background: #7367f014; color: #7367f0; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ $locale === 'fr' ? 'Organisations' : 'Organizations' }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/{{ $locale }}/dashboard">{{ $locale === 'fr' ? 'Accueil' : 'Home' }}</a></li>
                        <li class="breadcrumb-item active">{{ $locale === 'fr' ? 'Organisations' : 'Organizations' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
        <div class="form-group breadcrumb-right">
            <button type="button" class="btn btn-primary" id="btn-create-org" data-toggle="modal" data-target="#create-org-modal">
                <i data-feather="plus" class="mr-25"></i> {{ $locale === 'fr' ? 'Nouvelle organisation' : 'New organization' }}
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    {{-- Current org card --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ $locale === 'fr' ? 'Organisation active' : 'Current organization' }}</h4>
        </div>
        <div class="card-body" id="current-org-card">
            <div class="text-muted">{{ $locale === 'fr' ? 'Chargement...' : 'Loading...' }}</div>
        </div>
    </div>

    {{-- All orgs --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ $locale === 'fr' ? 'Toutes les organisations' : 'All organizations' }}</h4>
            <input type="text" class="form-control" id="org-filter" placeholder="{{ $locale === 'fr' ? 'Filtrer...' : 'Filter...' }}" style="max-width:220px;font-size:13px;height:34px;">
        </div>
        <div class="card-body">
            <div class="row" id="org-grid"></div>
        </div>
    </div>
</div>

{{-- Create org modal --}}
<div class="modal fade" id="create-org-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $locale === 'fr' ? 'Nouvelle organisation' : 'New organization' }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="create-org-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ $locale === 'fr' ? 'Nom' : 'Name' }}</label>
                        <input type="text" class="form-control" id="new-org-name" required placeholder="{{ $locale === 'fr' ? 'Ex: Mon entreprise' : 'Ex: My company' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat-secondary" data-dismiss="modal">{{ $locale === 'fr' ? 'Annuler' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-org">{{ $locale === 'fr' ? 'Créer' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Rename org modal --}}
<div class="modal fade" id="rename-org-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $locale === 'fr' ? 'Renommer l\'organisation' : 'Rename organization' }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="rename-org-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ $locale === 'fr' ? 'Nouveau nom' : 'New name' }}</label>
                        <input type="text" class="form-control" id="rename-org-name" required>
                        <input type="hidden" id="rename-org-id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat-secondary" data-dismiss="modal">{{ $locale === 'fr' ? 'Annuler' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-rename">{{ $locale === 'fr' ? 'Enregistrer' : 'Save' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const LOCALE = '{{ $locale }}';
const ROLE_COLORS = { owner: '#7367f0', admin: '#ff9f43', operator: '#28c76f', viewer: '#82868b', partner: '#00cfe8', none: '#b0b0b0' };
const ORG_PALETTE = ['#7367f0','#ff9f43','#28c76f','#00cfe8','#ea5455','#a855f7','#3b82f6','#f97316'];
const T = {
    current: LOCALE === 'fr' ? 'Active' : 'Current',
    switchTo: LOCALE === 'fr' ? 'Basculer' : 'Switch',
    rename: LOCALE === 'fr' ? 'Renommer' : 'Rename',
    owner: LOCALE === 'fr' ? 'Propriétaire' : 'Owner',
    admin: LOCALE === 'fr' ? 'Administrateur' : 'Admin',
    operator: LOCALE === 'fr' ? 'Opérateur' : 'Operator',
    viewer: LOCALE === 'fr' ? 'Lecteur' : 'Viewer',
    partner: LOCALE === 'fr' ? 'Partenaire' : 'Partner',
    noOrgs: LOCALE === 'fr' ? 'Aucune organisation trouvée.' : 'No organizations found.',
    created: LOCALE === 'fr' ? 'Organisation créée !' : 'Organization created!',
    renamed: LOCALE === 'fr' ? 'Organisation renommée !' : 'Organization renamed!',
    switched: LOCALE === 'fr' ? 'Organisation changée !' : 'Organization switched!',
    error: LOCALE === 'fr' ? 'Une erreur est survenue.' : 'Something went wrong.',
};

function orgInitials(name) {
    const w = (name || '?').trim().split(/\s+/);
    return w.length >= 2 ? (w[0][0] + w[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();
}
function orgColor(id) { return ORG_PALETTE[(parseInt(id) || 0) % ORG_PALETTE.length]; }
function roleLabel(role) { return T[role] || role; }

$(document).ready(function() {
    const token = UserManager.getToken();
    const headers = { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' };
    let allOrgs = [];

    function loadOrgs() {
        $.ajax({
            url: '/api/organizations',
            headers: headers,
            success: function(res) {
                allOrgs = res.organizations || [];
                const currentId = res.current_id;
                renderCurrentOrg(allOrgs, currentId);
                renderOrgGrid(allOrgs, currentId);
            },
            error: function() { toastr.error(T.error); }
        });
    }

    function renderCurrentOrg(orgs, currentId) {
        const org = orgs.find(function(o) { return o.id == currentId; });
        const $el = $('#current-org-card');
        if (!org) { $el.html('<p class="text-muted">' + T.noOrgs + '</p>'); return; }
        const bg = orgColor(org.id);
        $el.html(
            '<div class="d-flex align-items-center" style="gap:14px;">' +
                '<span class="org-icon" style="width:48px;height:48px;border-radius:10px;background:' + bg + ';color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;">' + orgInitials(org.name) + '</span>' +
                '<div style="flex:1;min-width:0;">' +
                    '<h5 style="margin:0;font-size:16px;font-weight:600;">' + org.name + '</h5>' +
                    '<span style="font-size:12px;color:' + (ROLE_COLORS[org.role] || '#999') + ';font-weight:500;text-transform:capitalize;">' + roleLabel(org.role) + '</span>' +
                    ' <span style="font-size:11px;color:#aaa;margin-left:4px;">' + (org.slug || '') + '</span>' +
                '</div>' +
                '<button class="btn btn-sm btn-flat-primary rename-btn" data-id="' + org.id + '" data-name="' + org.name.replace(/"/g, '&quot;') + '"><i data-feather="edit-2" style="width:14px;height:14px;"></i> ' + T.rename + '</button>' +
            '</div>'
        );
        feather.replace();
    }

    function renderOrgGrid(orgs, currentId, filter) {
        const $grid = $('#org-grid');
        $grid.empty();
        const q = (filter || '').toLowerCase();
        const filtered = orgs.filter(function(o) { return !q || o.name.toLowerCase().indexOf(q) !== -1; });
        if (filtered.length === 0) {
            $grid.html('<div class="col-12"><p class="text-muted">' + T.noOrgs + '</p></div>');
            return;
        }
        filtered.forEach(function(org) {
            const isActive = org.id == currentId;
            const bg = orgColor(org.id);
            const roleColor = ROLE_COLORS[org.role] || '#999';
            const card =
                '<div class="col-md-6 col-lg-4 mb-1">' +
                    '<div class="org-card' + (isActive ? ' active' : '') + '" data-org-id="' + org.id + '">' +
                        '<span class="org-icon" style="background:' + bg + ';">' + orgInitials(org.name) + '</span>' +
                        '<div class="org-info">' +
                            '<h6>' + org.name + '</h6>' +
                            '<small style="color:' + roleColor + ';font-weight:500;text-transform:capitalize;">' + roleLabel(org.role) + '</small>' +
                        '</div>' +
                        (isActive
                            ? '<span class="org-badge-current">' + T.current + '</span>'
                            : '<button class="btn btn-sm btn-flat-primary switch-org-btn" data-id="' + org.id + '">' + T.switchTo + '</button>') +
                    '</div>' +
                '</div>';
            $grid.append(card);
        });
        feather.replace();
    }

    // Filter
    $('#org-filter').on('input', function() {
        const currentId = UserManager.getUser()?.current_org_id;
        renderOrgGrid(allOrgs, currentId, $(this).val());
    });

    // Switch org
    $(document).on('click', '.switch-org-btn', function(e) {
        e.stopPropagation();
        const orgId = $(this).data('id');
        UserManager.switchOrg(orgId);
    });

    // Create org
    $('#create-org-form').on('submit', function(e) {
        e.preventDefault();
        const name = $('#new-org-name').val().trim();
        if (!name) return;
        const $btn = $('#btn-submit-org');
        $btn.prop('disabled', true);
        $.ajax({
            url: '/api/organizations',
            type: 'POST',
            headers: headers,
            data: JSON.stringify({ name: name }),
            success: function() {
                $('#create-org-modal').modal('hide');
                $('#new-org-name').val('');
                toastr.success(T.created);
                loadOrgs();
            },
            error: function(xhr) { toastr.error(xhr.responseJSON?.error || T.error); },
            complete: function() { $btn.prop('disabled', false); }
        });
    });

    // Rename org
    $(document).on('click', '.rename-btn', function() {
        $('#rename-org-id').val($(this).data('id'));
        $('#rename-org-name').val($(this).data('name'));
        $('#rename-org-modal').modal('show');
    });

    $('#rename-org-form').on('submit', function(e) {
        e.preventDefault();
        const orgId = $('#rename-org-id').val();
        const name = $('#rename-org-name').val().trim();
        if (!name) return;
        const $btn = $('#btn-submit-rename');
        $btn.prop('disabled', true);
        $.ajax({
            url: '/api/organizations/' + orgId,
            type: 'PUT',
            headers: headers,
            data: JSON.stringify({ name: name }),
            success: function() {
                $('#rename-org-modal').modal('hide');
                toastr.success(T.renamed);
                // Update localStorage
                const user = UserManager.getUser();
                if (user && user.current_org_id == orgId) {
                    user.current_org_name = name;
                    localStorage.setItem('mrwifi_user', JSON.stringify(user));
                }
                loadOrgs();
            },
            error: function(xhr) { toastr.error(xhr.responseJSON?.error || T.error); },
            complete: function() { $btn.prop('disabled', false); }
        });
    });

    loadOrgs();
});
</script>
@endpush
