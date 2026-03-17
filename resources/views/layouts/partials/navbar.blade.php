<style>
    /* Org switcher — clean styles, no inline overrides */
    .dropdown-org { line-height: 1 !important; }
    .dropdown-org > .dropdown-toggle::after { display: none; }
    .dropdown-org .dropdown-toggle { display: flex; align-items: center; }
    .dropdown-org .org-avatar {
        width: 24px; height: 24px; border-radius: 6px;
        background: #7367f0; color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; flex-shrink: 0;
    }
    .dropdown-org .org-nav {
        display: flex; flex-direction: column; align-items: flex-start;
        margin-left: 0.5rem; margin-right: 0.2rem;
    }
    .dropdown-org .org-nav .org-name {
        font-size: 0.857rem; font-weight: 600;
        max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .dropdown-org .org-switcher-menu {
        width: 272px; padding: 0; border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,.1); border: 1px solid #ebe9f1;
    }
    .dropdown-org .org-switcher-search {
        font-size: 13px; height: 34px; border-radius: 6px;
        border: 1px solid #e4e2ec; box-shadow: none; padding: 0 10px;
    }
    .dropdown-org .org-switcher-label {
        font-size: 11px; text-transform: uppercase; letter-spacing: .5px;
        color: #a8a5b5; font-weight: 600;
    }
    .dropdown-org .org-switcher-list {
        max-height: 232px; overflow-y: auto; padding: 2px 6px 4px;
    }
    .dropdown-org .org-switcher-footer {
        border-top: 1px solid #ebe9f1; padding: 6px;
    }
    .dropdown-org .org-switcher-footer .dropdown-item {
        font-size: 13px; border-radius: 6px; padding: 7px 8px;
        color: #7367f0; display: flex; align-items: center;
    }
    .dropdown-org .org-switcher-footer .dropdown-item i,
    .dropdown-org .org-switcher-footer .dropdown-item svg {
        width: 14px; height: 14px; margin-right: 6px;
    }
</style>
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i data-feather="menu"></i></a></li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">
            <li class="nav-item dropdown dropdown-language">
                <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown">
                    <i class="flag-icon flag-icon-{{ $locale === 'fr' ? 'fr' : 'us' }}"></i>
                    <span class="selected-language">{{ $locale === 'fr' ? 'Français' : 'English' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @php
                        $path = request()->path();
                        $currentLocale = str_starts_with($path, 'fr/') ? 'fr' : 'en';
                        $pathWithoutLocale = preg_replace('/^(en|fr)\//', '', $path);
                        $frToEn = [
                            'boutique'       => 'shop',
                            'panier'         => 'cart',
                            'commander'      => 'checkout',
                            'commandes'      => 'orders',
                            'inventaire'     => 'inventory',
                            'modeles'        => 'models',
                            'parametres-qos' => 'qos-settings',
                            'appareils'      => 'devices',
                            'emplacements'   => 'locations',
                        ];
                        $enToFr = array_flip($frToEn);
                        $enPath = $pathWithoutLocale;
                        foreach ($frToEn as $fr => $en) { $enPath = str_replace($fr, $en, $enPath); }
                        $frPath = $pathWithoutLocale;
                        foreach ($enToFr as $en => $fr) { $frPath = str_replace($en, $fr, $frPath); }
                        $enUrl = '/en/' . $enPath;
                        $frUrl = '/fr/' . $frPath;
                    @endphp
                    <a class="dropdown-item" href="{{ $enUrl }}"><i class="flag-icon flag-icon-us"></i> English</a>
                    <a class="dropdown-item" href="{{ $frUrl }}"><i class="flag-icon flag-icon-fr"></i> Français</a>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-cart mr-25">
                <a class="nav-link" href="javascript:void(0);" id="dropdown-cart" data-toggle="dropdown">
                    <i class="ficon" data-feather="shopping-cart"></i>
                    <span class="badge badge-pill badge-primary badge-up cart-item-count" style="display: none;">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right" style="width: 350px;">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 mr-auto">{{ $locale === 'fr' ? 'Mon Panier' : 'My Cart' }}</h4>
                            <div class="badge badge-pill badge-light-primary cart-item-count">0</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list" id="cart-dropdown-items">
                        <div class="text-center p-2">
                            <p class="text-muted">{{ $locale === 'fr' ? 'Chargement...' : 'Loading...' }}</p>
                        </div>
                    </li>
                    <li class="dropdown-menu-footer">
                        <a class="btn btn-primary btn-block" href="/{{ $locale === 'fr' ? 'fr/panier' : 'en/cart' }}">{{ $locale === 'fr' ? 'Voir le Panier' : 'View Cart' }}</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a>
            </li>
            <li class="nav-item dropdown dropdown-org d-none" id="org-switcher">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" data-toggle="dropdown">
                    <span class="org-avatar" id="org-avatar"></span>
                    <div class="org-nav d-none d-lg-flex">
                        <span class="org-name"></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right org-switcher-menu" id="org-switcher-menu">
                    <div class="p-75">
                        <input type="text" class="form-control org-switcher-search" id="org-search"
                               placeholder="{{ $locale === 'fr' ? 'Rechercher…' : 'Search…' }}">
                    </div>
                    <div class="px-1 pb-25">
                        <span class="org-switcher-label">{{ $locale === 'fr' ? 'Organisations' : 'Organizations' }}</span>
                    </div>
                    <div class="org-switcher-list" id="org-list"></div>
                    <div class="org-switcher-footer">
                        <a href="/{{ $locale }}/settings/organization" class="dropdown-item">
                            <i data-feather="settings"></i>{{ $locale === 'fr' ? 'Gérer les organisations' : 'Manage organizations' }}
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name font-weight-bolder"></span>
                        <span class="user-status"></span>
                    </div>
                    <span class="avatar">
                        <img class="round user-profile-picture" src="/assets/avatar-default.png" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-header" id="user-menu-role" style="padding: .4rem 1.28rem .2rem;"></div>
                    <div class="dropdown-divider" style="margin: .2rem 0;"></div>
                    <a class="dropdown-item" href="/{{ $locale }}/profile"><i class="mr-50" data-feather="user"></i> {{ $locale === 'fr' ? 'Profil' : 'Profile' }}</a>
                    <a class="dropdown-item" href="/auth/switch"><i class="mr-50" data-feather="repeat"></i> {{ $locale === 'fr' ? 'Changer de compte' : 'Switch Account' }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item logout-button" href="/logout"><i class="mr-50" data-feather="power"></i> {{ $locale === 'fr' ? 'Déconnexion' : 'Logout' }}</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
