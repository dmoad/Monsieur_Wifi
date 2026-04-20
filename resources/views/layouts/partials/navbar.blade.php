<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i data-feather="menu"></i></a></li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">
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
                <a class="nav-link nav-link-style" id="mw-theme-toggle" href="javascript:void(0);" title="{{ __('navbar.toggle_theme') }}">
                    <i class="ficon" data-feather="moon" id="mw-theme-icon"></i>
                </a>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown">
                    <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                    <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.png" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="/{{ $locale }}/profile"><i class="mr-50" data-feather="user"></i> {{ __('common.profile') }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#mw-language-modal">
                        <i class="mr-50" data-feather="globe"></i>
                        {{ __('navbar.language') }} · <strong>{{ strtoupper($locale) }}</strong>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item logout-button" href="/logout"><i class="mr-50" data-feather="power"></i> {{ __('common.logout') }}</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Language picker modal -->
<div class="modal fade" id="mw-language-modal" tabindex="-1" role="dialog" aria-labelledby="mw-language-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mw-language-modal-label">
                    <i data-feather="globe" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"></i>
                    {{ __('navbar.choose_language') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-1">
                @php $pathWithoutLocale = preg_replace('/^(en|fr)\//', '', request()->path()); @endphp
                <a href="/en/{{ $pathWithoutLocale }}"
                   class="d-flex align-items-center p-1 rounded mb-50 {{ $locale === 'en' ? 'bg-light-primary' : '' }}"
                   style="text-decoration:none;color:inherit;">
                    <span class="flag-icon flag-icon-us mr-75" style="font-size:1.4rem;"></span>
                    <div>
                        <div class="font-weight-bold">English</div>
                        <small class="text-muted">EN</small>
                    </div>
                    @if($locale === 'en')
                        <i data-feather="check" class="ml-auto" style="width:16px;height:16px;color:var(--mw-primary);"></i>
                    @endif
                </a>
                <a href="/fr/{{ $pathWithoutLocale }}"
                   class="d-flex align-items-center p-1 rounded {{ $locale === 'fr' ? 'bg-light-primary' : '' }}"
                   style="text-decoration:none;color:inherit;">
                    <span class="flag-icon flag-icon-fr mr-75" style="font-size:1.4rem;"></span>
                    <div>
                        <div class="font-weight-bold">Français</div>
                        <small class="text-muted">FR</small>
                    </div>
                    @if($locale === 'fr')
                        <i data-feather="check" class="ml-auto" style="width:16px;height:16px;color:var(--mw-primary);"></i>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const DARK_KEY = 'mwColorScheme';
    const html = document.documentElement;
    const toggle = document.getElementById('mw-theme-toggle');
    const icon = document.getElementById('mw-theme-icon');

    function applyTheme(dark) {
        if (dark) {
            html.classList.add('dark-layout');
        } else {
            html.classList.remove('dark-layout');
        }
        if (icon) {
            icon.setAttribute('data-feather', dark ? 'sun' : 'moon');
            if (typeof feather !== 'undefined') feather.replace();
        }
    }

    const saved = localStorage.getItem(DARK_KEY);
    applyTheme(saved === 'dark');

    if (toggle) {
        toggle.addEventListener('click', function () {
            const isDark = html.classList.contains('dark-layout');
            const next = !isDark;
            localStorage.setItem(DARK_KEY, next ? 'dark' : 'light');
            applyTheme(next);
        });
    }

    // Re-init feather icons inside the language modal after it opens
    document.addEventListener('shown.bs.modal', function (e) {
        if (e.target && e.target.id === 'mw-language-modal') {
            if (typeof feather !== 'undefined') feather.replace();
        }
    });
    // jQuery bootstrap modal event
    if (typeof $ !== 'undefined') {
        $(document).on('shown.bs.modal', '#mw-language-modal', function () {
            if (typeof feather !== 'undefined') feather.replace();
        });
    }
})();
</script>
