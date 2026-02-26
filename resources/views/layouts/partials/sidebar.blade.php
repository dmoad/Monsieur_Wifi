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
            <li class="navigation-header"><span>{{ $locale === 'fr' ? 'Gestion' : 'Management' }}</span></li>
            <li class="nav-item {{ request()->is('*/dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/dashboard"><i data-feather="home"></i><span class="menu-title text-truncate">Dashboard</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/locations') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/locations"><i data-feather="map-pin"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Emplacements' : 'Locations' }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/captive-portals') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/captive-portals"><i data-feather="layout"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Portails Captifs' : 'Captive Portals' }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/shop', '*/boutique', '*/cart', '*/panier', '*/checkout', '*/commander') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale === 'fr' ? 'fr/boutique' : 'en/shop' }}"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Boutique' : 'Shop' }}</span></a>
            </li>
            <li class="nav-item {{ request()->is('*/orders', '*/commandes') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale === 'fr' ? 'fr/commandes' : 'en/orders' }}"><i data-feather="list"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Mes Commandes' : 'My Orders' }}</span></a>
            </li>
            
            <li class="navigation-header admin_and_above hidden"><span>{{ $locale === 'fr' ? 'Pour Admin' : 'For Admin' }}</span></li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/accounts') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/accounts"><i data-feather="users"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Comptes' : 'Accounts' }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/domain-blocking') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/domain-blocking"><i data-feather="slash"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Blocage de Domaine' : 'Domain Blocking' }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/models', '*/admin/modeles') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale === 'fr' ? 'fr/admin/modeles' : 'en/admin/models' }}"><i data-feather="cpu"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Gérer les Modèles' : 'Manage Models' }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/inventory', '*/admin/inventaire') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale === 'fr' ? 'fr/admin/inventaire' : 'en/admin/inventory' }}"><i data-feather="box"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Gérer l\'Inventaire' : 'Manage Inventory' }}</span></a>
            </li>
            <li class="nav-item admin_and_above hidden {{ request()->is('*/admin/orders', '*/admin/commandes') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale === 'fr' ? 'fr/admin/commandes' : 'en/admin/orders' }}"><i data-feather="package"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Gérer les Commandes' : 'Manage Orders' }}</span></a>
            </li>
            
            <li class="navigation-header only_superadmin hidden"><span>{{ $locale === 'fr' ? 'Super Admin' : 'Super Admin' }}</span></li>
            <li class="nav-item only_superadmin hidden {{ request()->is('*/firmware') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/firmware"><i data-feather="download"></i><span class="menu-title text-truncate">Firmware</span></a>
            </li>
            <li class="nav-item only_superadmin hidden {{ request()->is('*/system-settings') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/system-settings"><i data-feather="settings"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Paramètres Système' : 'System Settings' }}</span></a>
            </li>
            
            <li class="navigation-header"><span>{{ $locale === 'fr' ? 'Compte' : 'Account' }}</span></li>
            <li class="nav-item {{ request()->is('*/profile') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="/{{ $locale }}/profile"><i data-feather="user"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Profil' : 'Profile' }}</span></a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center logout-button" href="/logout"><i data-feather="power"></i><span class="menu-title text-truncate">{{ $locale === 'fr' ? 'Déconnexion' : 'Logout' }}</span></a>
            </li>
        </ul>
    </div>
</div>
