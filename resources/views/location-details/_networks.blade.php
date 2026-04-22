<div class="ld-panel" id="ld-panel-networks">

    <div class="card ld-networks-card">
        <div class="card-header ld-networks-header">
            <div class="ld-networks-header-text">
                <h4 class="ld-networks-title">{{ __('location_details.networks_card_title') }}</h4>
                <div class="ld-networks-subtitle">{{ __('location_details.networks_card_subtitle') }}</div>
            </div>
            <button type="button" id="ld-networks-add-btn" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="mr-1"></i>{{ __('location_details.networks_add') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div id="ld-networks-loading" class="ld-networks-empty-state">{{ __('common.loading') }}</div>
            <div id="ld-networks-empty" class="ld-networks-empty-state" style="display:none;">{{ __('location_details.networks_empty') }}</div>
            <div id="ld-networks-error" class="ld-networks-empty-state ld-networks-error" style="display:none;">{{ __('location_details.networks_load_error') }}</div>
            <ul id="ld-networks-list" class="ld-networks-list list-unstyled mb-0" style="display:none;"></ul>
        </div>
    </div>

    <template id="ld-network-row-tpl">
        <li class="ld-network-row">
            <div class="ld-net-icon"><i data-feather="wifi"></i></div>
            <div class="ld-net-body">
                <div class="ld-net-name-row">
                    <span class="ld-net-name"></span>
                    <span class="ld-net-type-badge"></span>
                    <span class="ld-net-status-badge"></span>
                </div>
                <div class="ld-net-meta">
                    <span class="ld-net-band"></span>
                    <span class="ld-net-vlan" style="display:none;"></span>
                </div>
            </div>
            <i data-feather="chevron-right" class="ld-net-chevron"></i>
        </li>
    </template>

    <aside class="mw-drawer" id="ld-network-drawer" role="dialog" aria-modal="true" aria-labelledby="ld-network-drawer-title">
        <header class="mw-drawer-header">
            <h5 class="mw-drawer-title" id="ld-network-drawer-title"></h5>
            <button type="button" class="mw-drawer-close" aria-label="{{ __('common.close') }}" data-mw-drawer-close>&times;</button>
        </header>
        <div class="mw-drawer-body" id="ld-network-drawer-body">
            <p class="text-muted">{{ __('location_details.networks_drawer_placeholder') }}</p>
        </div>
        <footer class="mw-drawer-footer">
            <button type="button" class="btn btn-outline-secondary" data-mw-drawer-close>{{ __('common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="ld-network-drawer-save" disabled>{{ __('location_details.networks_drawer_save') }}</button>
        </footer>
    </aside>

</div>
