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
                        $pathWithoutLocale = preg_replace('/^(en|fr)\//', '', request()->path());
                    @endphp
                    <a class="dropdown-item" href="/en/{{ $pathWithoutLocale }}"><i class="flag-icon flag-icon-us"></i> English</a>
                    <a class="dropdown-item" href="/fr/{{ $pathWithoutLocale }}"><i class="flag-icon flag-icon-fr"></i> Français</a>
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
                            <h4 class="notification-title mb-0 mr-auto">{{ __('navbar.my_cart') }}</h4>
                            <div class="badge badge-pill badge-light-primary cart-item-count">0</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list" id="cart-dropdown-items">
                        <div class="text-center p-2">
                            <p class="text-muted">{{ __('common.loading') }}</p>
                        </div>
                    </li>
                    <li class="dropdown-menu-footer">
                        <a class="btn btn-primary btn-block" href="/{{ $locale }}/cart">{{ __('navbar.view_cart') }}</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown">
                    <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                    <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.png" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="/{{ $locale }}/profile"><i class="mr-50" data-feather="user"></i> {{ __('common.profile') }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item logout-button" href="/logout"><i class="mr-50" data-feather="power"></i> {{ __('common.logout') }}</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
