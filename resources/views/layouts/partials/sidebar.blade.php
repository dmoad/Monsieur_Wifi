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

        <a class="mw-si {{ request()->is('*/devices', '*/appareils') ? 'active' : '' }}"
           href="/{{ $locale }}/devices"
           title="{{ __('sidebar.devices') }}">
            <i data-feather="hard-drive"></i>
            <span class="mw-si-label">{{ __('sidebar.devices') }}</span>
        </a>

        <a class="mw-si {{ request()->is('*/shop', '*/boutique', '*/cart', '*/panier', '*/checkout', '*/commander') ? 'active' : '' }}"
           href="/{{ $locale }}/shop"
           title="{{ __('sidebar.shop') }}">
            <i data-feather="shopping-bag"></i>
            <span class="mw-si-label">{{ __('sidebar.shop') }}</span>
        </a>

        <div class="mw-si-sep admin_and_above hidden"></div>

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

        <a class="mw-si only_superadmin hidden {{ request()->is('*/qos-settings', '*/parametres-qos') ? 'active' : '' }}"
           href="/{{ $locale }}/qos-settings"
           title="{{ __('sidebar.traffic_priority') }}">
            <i data-feather="sliders"></i>
            <span class="mw-si-label">{{ __('sidebar.traffic_priority') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/models', '*/admin/modeles') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/models"
           title="{{ __('sidebar.manage_models') }}">
            <i data-feather="cpu"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_models') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/inventory', '*/admin/inventaire') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/inventory"
           title="{{ __('sidebar.manage_inventory') }}">
            <i data-feather="box"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_inventory') }}</span>
        </a>

        <a class="mw-si admin_and_above hidden {{ request()->is('*/admin/orders', '*/admin/commandes') ? 'active' : '' }}"
           href="/{{ $locale }}/admin/orders"
           title="{{ __('sidebar.manage_orders') }}">
            <i data-feather="package"></i>
            <span class="mw-si-label">{{ __('sidebar.manage_orders') }}</span>
        </a>

        <div class="mw-si-sep only_superadmin hidden"></div>

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

    {{-- Bottom: cart shortcut + avatar --}}
    <div class="mw-sb-bot">
        <a class="mw-si {{ request()->is('*/cart', '*/panier') ? 'active' : '' }}"
           href="/{{ $locale }}/cart"
           title="{{ __('navbar.my_cart') }}">
            <i data-feather="shopping-cart"></i>
            <span class="mw-si-label">{{ __('navbar.my_cart') }}</span>
            <span class="mw-cart-badge cart-item-count" style="display: none;">0</span>
        </a>

        {{-- Avatar trigger --}}
        <button class="mw-av" id="mwAvBtn" title="{{ __('common.profile') }}">
            <span class="mw-av-initials">?</span>
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
            <a class="mw-av-item {{ request()->is('*/orders', '*/commandes') ? 'active' : '' }}"
               href="/{{ $locale }}/orders">
                <i data-feather="list"></i>
                {{ __('sidebar.my_orders') }}
            </a>

            <div class="mw-av-divider"></div>

            {{-- Language switcher --}}
            <a class="mw-av-item {{ $locale === 'en' ? 'active' : '' }}" href="/en/{{ $pathWithoutLocale }}">
                <i data-feather="globe"></i>
                English
            </a>
            <a class="mw-av-item {{ $locale === 'fr' ? 'active' : '' }}" href="/fr/{{ $pathWithoutLocale }}">
                <i data-feather="globe"></i>
                Français
            </a>

            <div class="mw-av-divider"></div>

            <a class="mw-av-item mw-av-logout logout-button" href="/logout">
                <i data-feather="power"></i>
                {{ __('common.logout') }}
            </a>
        </div>
    </div>

</aside>
