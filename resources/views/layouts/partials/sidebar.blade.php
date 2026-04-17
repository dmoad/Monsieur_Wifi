<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="/{{ $locale }}/dashboard">
                    <span class="brand-logo"><img src="/app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="logo"></span>
                    <h2 class="brand-text">monsieur-wifi</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="navigation-header"><span>{{ __('sidebar.section_management') }}</span></li>
            <li class="nav-item {{ request()->is('*/dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">{{ __('sidebar.dashboard') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/zones*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/zones"><i data-feather="layers"></i><span class="menu-title text-truncate">{{ __('sidebar.zones') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/devices', '*/appareils') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/devices"><i data-feather="hard-drive"></i><span class="menu-title text-truncate">{{ __('sidebar.devices') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/locations') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">{{ __('sidebar.locations') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/captive-portals') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">{{ __('sidebar.captive_portals') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/shop', '*/boutique', '*/cart', '*/panier', '*/checkout', '*/commander') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/shop"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">{{ __('sidebar.shop') }}</span></a>
            </li>


            <li class="navigation-header admin_and_above hidden"><span>{{ __('sidebar.section_admin') }}</span></li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/accounts') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">{{ __('sidebar.accounts') }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/domain-blocking') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">{{ __('sidebar.domain_blocking') }}</span></a>
            </li>
            <li class="nav-item only_superadmin hidden {{ request()->is('*/qos-settings', '*/parametres-qos') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/qos-settings"><i data-feather="sliders"></i><span class="menu-title text-truncate">{{ __('sidebar.traffic_priority') }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/models', '*/admin/modeles') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/admin/models"><i data-feather="cpu"></i><span class="menu-title text-truncate">{{ __('sidebar.manage_models') }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/inventory', '*/admin/inventaire') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/admin/inventory"><i data-feather="box"></i><span class="menu-title text-truncate">{{ __('sidebar.manage_inventory') }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/orders', '*/admin/commandes') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/admin/orders"><i data-feather="package"></i><span class="menu-title text-truncate">{{ __('sidebar.manage_orders') }}</span></a>
            </li>

            <li class="navigation-header only_superadmin hidden"><span>{{ __('sidebar.section_superadmin') }}</span></li>
            <li class="nav-item only_superadmin hidden {{ request()->is('*/firmware') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">{{ __('sidebar.firmware') }}</span></a>
            </li>
            <li class="nav-item only_superadmin hidden {{ request()->is('*/system-settings') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">{{ __('sidebar.system_settings') }}</span></a>
            </li>

            <li class="navigation-header"><span>{{ __('sidebar.section_account') }}</span></li>
            <li class="nav-item {{ request()->is('*/profile') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/profile"><i data-feather="user"></i><span class="menu-title text-truncate">{{ __('common.profile') }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/orders', '*/commandes') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/orders"><i data-feather="list"></i><span class="menu-title text-truncate">{{ __('sidebar.my_orders') }}</span></a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center logout-button" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">{{ __('common.logout') }}</span></a>
            </li>
        </ul>
    </div>
</div>
