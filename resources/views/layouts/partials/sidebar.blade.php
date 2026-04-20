@php
    $locale = app()->getLocale();
    $pathWithoutLocale = preg_replace('/^(en|fr)\//', '', request()->path());
@endphp

<aside class="mw-sb" id="mwSidebar">

    {{-- Logo / collapse toggle --}}
    <div class="mw-sb-head" id="mwSbToggle" title="{{ __('sidebar.toggle') }}">
        <div class="mw-sb-logo">
            <img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="Monsieur WiFi">
            <span class="mw-sb-logo-text">
                <span class="mw-sb-m">Monsieur</span>&nbsp;<span class="mw-sb-w">WiFi</span>
            </span>
        </div>
        <svg class="mw-sb-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </div>

    {{-- Main nav --}}
    <nav class="mw-sb-nav">
        <a class="mw-si {{ request()->is('*/dashboard') ? 'active' : '' }}"
           href="/{{ $locale }}/dashboard"
           title="{{ __('sidebar.dashboard') }}">
            <i data-feather="home"></i>
            <span class="mw-si-label">{{ __('sidebar.dashboard') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/zones*') ? 'active' : '' }}"
           href="/{{ $locale }}/zones"
           title="{{ __('sidebar.zones') }}">
            <i data-feather="layers"></i>
            <span class="mw-si-label">{{ __('sidebar.zones') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/locations*') ? 'active' : '' }}"
           href="/{{ $locale }}/locations"
           title="{{ __('sidebar.locations') }}">
            <i data-feather="map-pin"></i>
            <span class="mw-si-label">{{ __('sidebar.locations') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/captive-portals') ? 'active' : '' }}"
           href="/{{ $locale }}/captive-portals"
           title="{{ __('sidebar.captive_portals') }}">
            <i data-feather="layout"></i>
            <span class="mw-si-label">{{ __('sidebar.captive_portals') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/devices') ? 'active' : '' }}"
           href="/{{ $locale }}/devices"
           title="{{ __('sidebar.devices') }}">
            <i data-feather="hard-drive"></i>
            <span class="mw-si-label">{{ __('sidebar.devices') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/shop', '*/cart', '*/checkout') ? 'active' : '' }}"
           href="/{{ $locale }}/shop"
           title="{{ __('sidebar.shop') }}">
            <i data-feather="shopping-bag"></i>
            <span class="mw-si-label">{{ __('sidebar.shop') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/cart') ? 'active' : '' }}"
           href="/{{ $locale }}/cart"
           title="{{ __('navbar.my_cart') }}">
            <i data-feather="shopping-cart"></i>
            <span class="mw-si-label">{{ __('navbar.my_cart') }}</span>
            <span class="mw-cart-badge cart-item-count" style="display: none;">0</span>
        </a>

        <div class="mw-sb-section admin_and_above hidden">
            <span>{{ __('sidebar.section_admin') }}</span>
        </div>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/accounts') ? 'active' : '' }}"
           href="/{{ $locale }}/accounts"
           title="{{ __('sidebar.accounts') }}">
            <i data-feather="users"></i>
            <span class="mw-si-label">{{ __('sidebar.accounts') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/domain-blocking') ? 'active' : '' }}"
           href="/{{ $locale }}/domain-blocking"
           title="{{ __('sidebar.domain_blocking') }}">
            <i data-feather="slash"></i>
            <span class="mw-si-label">{{ __('sidebar.domain_blocking') }}</span>
        </a>

        <a class="mw-si only_superadmin hidden {{ request()->is('*/qos-settings') ? 'active' : '' }}"
           href="/{{ $locale }}/qos-settings"
           title="{{ __('sidebar.traffic_priority') }}">
            <i data-feather="sliders"></i>
            <span class="mw-si-label">{{ __('sidebar.traffic_priority') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/models') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/models"
           title="{{ __('sidebar.manage_models') }}">
            <i data-feather="cpu"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_models') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/inventory') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/inventory"
           title="{{ __('sidebar.manage_inventory') }}">
            <i data-feather="box"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_inventory') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/orders') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/orders"
           title="{{ __('sidebar.manage_orders') }}">
            <i data-feather="package"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_orders') }}</span>
        </a>

        <div class="mw-sb-section only_superadmin hidden">
            <span>{{ __('sidebar.section_superadmin') }}</span>
        </div>

        <a class="mw-si only_superadmin hidden {{ request()->is('*/firmware') ? 'active' : '' }}"
           href="/{{ $locale }}/firmware"
           title="{{ __('sidebar.firmware') }}">
            <i data-feather="download"></i>
            <span class="mw-si-label">{{ __('sidebar.firmware') }}</span>
        </a>

        <a class="mw-si only_superadmin hidden {{ request()->is('*/system-settings') ? 'active' : '' }}"
           href="/{{ $locale }}/system-settings"
           title="{{ __('sidebar.system_settings') }}">
            <i data-feather="settings"></i>
            <span class="mw-si-label">{{ __('sidebar.system_settings') }}</span>
        </a>
    </nav>

    {{-- Bottom: avatar --}}
    <div class="mw-sb-bot">
        {{-- Avatar trigger: circle + (expanded only) name + email --}}
        <button class="mw-av-trigger" id="mwAvBtn" title="{{ __('common.profile') }}">
            <span class="mw-av">
                <span class="mw-av-initials">?</span>
            </span>
            <span class="mw-av-text">
                <span class="mw-av-display-name"></span>
                <span class="mw-av-display-email"></span>
            </span>
        </button>

        {{-- Avatar dropdown (opens upward) --}}
        <div class="mw-av-menu" id="mwAvMenu">
            <div class="mw-av-info">
                <div class="mw-av-name user-name"></div>
                <div class="mw-av-role user-status"></div>
            </div>
            <div class="mw-av-divider"></div>

            <a class="mw-av-item" href="/{{ $locale }}/profile">
                <i data-feather="user"></i>
                {{ __('common.profile') }}
            </a>
            <a class="mw-av-item {{ request()->is('*/orders') ? 'active' : '' }}"
               href="/{{ $locale }}/orders">
                <i data-feather="list"></i>
                {{ __('sidebar.my_orders') }}
            </a>

            <div class="mw-av-divider"></div>

            {{-- Language switcher --}}
            <button class="mw-av-item mw-av-lang-btn" id="mwLangTrigger" type="button">
                <i data-feather="globe"></i>
                {{ __('navbar.language') }} · <strong>{{ strtoupper($locale) }}</strong>
            </button>

            {{-- Theme toggle --}}
            <button class="mw-av-item mw-av-theme-btn" id="mwThemeToggle" type="button">
                <i data-feather="moon" id="mwThemeIcon"></i>
                <span id="mwThemeLabel">{{ __('navbar.toggle_theme') }}</span>
            </button>

            <div class="mw-av-divider"></div>

            <a class="mw-av-item mw-av-logout logout-button" href="/logout">
                <i data-feather="power"></i>
                {{ __('common.logout') }}
            </a>
        </div>
    </div>

</aside>

{{-- Mobile overlay backdrop --}}
<div class="mw-sb-backdrop" id="mwSbBackdrop"></div>

{{-- Language picker modal --}}
<div class="mw-modal-backdrop" id="mwLangModalBackdrop" style="display:none;position:fixed;inset:0;z-index:1050;background:rgba(0,0,0,0.45);"></div>
<div class="mw-modal" id="mwLangModal" role="dialog" aria-labelledby="mwLangModalTitle" aria-modal="true"
     style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1051;
            background:#fff;border-radius:12px;width:320px;max-width:calc(100vw - 2rem);box-shadow:0 16px 40px rgba(0,0,0,0.18);">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem 0.75rem;border-bottom:1px solid #eee;">
        <h6 id="mwLangModalTitle" style="margin:0;font-weight:600;display:flex;align-items:center;gap:0.5rem;">
            <i data-feather="globe" style="width:16px;height:16px;color:var(--mw-primary);"></i>
            {{ __('navbar.choose_language') }}
        </h6>
        <button id="mwLangModalClose" type="button" style="background:none;border:none;cursor:pointer;padding:0;line-height:1;font-size:1.2rem;color:#888;">&times;</button>
    </div>
    <div style="padding:0.75rem 1rem 1rem;">
        <a href="/en/{{ $pathWithoutLocale }}"
           style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0.75rem;border-radius:8px;text-decoration:none;color:inherit;margin-bottom:0.35rem;
                  {{ $locale === 'en' ? 'background:rgba(99,102,241,0.08);' : '' }}transition:background 0.15s;">
            <span class="flag-icon flag-icon-us" style="font-size:1.4rem;flex-shrink:0;"></span>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:0.9rem;">English</div>
                <div style="font-size:0.75rem;color:#999;">EN</div>
            </div>
            @if($locale === 'en')
                <i data-feather="check" style="width:16px;height:16px;color:var(--mw-primary);flex-shrink:0;"></i>
            @endif
        </a>
        <a href="/fr/{{ $pathWithoutLocale }}"
           style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0.75rem;border-radius:8px;text-decoration:none;color:inherit;
                  {{ $locale === 'fr' ? 'background:rgba(99,102,241,0.08);' : '' }}transition:background 0.15s;">
            <span class="flag-icon flag-icon-fr" style="font-size:1.4rem;flex-shrink:0;"></span>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:0.9rem;">Français</div>
                <div style="font-size:0.75rem;color:#999;">FR</div>
            </div>
            @if($locale === 'fr')
                <i data-feather="check" style="width:16px;height:16px;color:var(--mw-primary);flex-shrink:0;"></i>
            @endif
        </a>
    </div>
</div>

<script>
(function () {
    // ── Language modal ──────────────────────────────────────────────
    const langTrigger = document.getElementById('mwLangTrigger');
    const langModal   = document.getElementById('mwLangModal');
    const langBackdrop= document.getElementById('mwLangModalBackdrop');
    const langClose   = document.getElementById('mwLangModalClose');

    function openLangModal() {
        langModal.style.display   = 'block';
        langBackdrop.style.display= 'block';
        if (typeof feather !== 'undefined') feather.replace();
    }
    function closeLangModal() {
        langModal.style.display   = 'none';
        langBackdrop.style.display= 'none';
    }

    if (langTrigger) langTrigger.addEventListener('click', function () {
        // Close avatar menu first
        const avMenu = document.getElementById('mwAvMenu');
        if (avMenu) avMenu.classList.remove('open');
        openLangModal();
    });
    if (langClose)   langClose.addEventListener('click', closeLangModal);
    if (langBackdrop)langBackdrop.addEventListener('click', closeLangModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLangModal();
    });

    // ── Theme toggle ────────────────────────────────────────────────
    const DARK_KEY    = 'mwColorScheme';
    const html        = document.documentElement;
    const themeToggle = document.getElementById('mwThemeToggle');
    const themeIcon   = document.getElementById('mwThemeIcon');
    const themeLabel  = document.getElementById('mwThemeLabel');

    const darkLabel = '{{ __("navbar.switch_to_light") }}';
    const lightLabel= '{{ __("navbar.switch_to_dark") }}';

    function applyTheme(dark) {
        html.classList.toggle('dark-layout', dark);
        if (themeIcon) themeIcon.setAttribute('data-feather', dark ? 'sun' : 'moon');
        if (themeLabel) themeLabel.textContent = dark ? darkLabel : lightLabel;
        if (typeof feather !== 'undefined') feather.replace();
    }

    applyTheme(localStorage.getItem(DARK_KEY) === 'dark');

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const next = !html.classList.contains('dark-layout');
            localStorage.setItem(DARK_KEY, next ? 'dark' : 'light');
            applyTheme(next);
        });
    }
})();
</script>

{{-- Mobile top bar (hidden on desktop via CSS) --}}
<div class="mw-mobile-bar" id="mwMobileBar">
    <button class="mw-mobile-hamburger" id="mwHamburger" title="{{ __('sidebar.toggle') }}" aria-label="{{ __('sidebar.toggle') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>
</div>
