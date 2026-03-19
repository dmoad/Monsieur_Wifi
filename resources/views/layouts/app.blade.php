<!DOCTYPE html>
<html class="loading" lang="{{ $locale ?? 'en' }}" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Monsieur WiFi')</title>
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" href="/app-assets/css/colors.css">
    <link rel="stylesheet" href="/app-assets/css/components.css">
    <link rel="stylesheet" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" href="/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" href="/app-assets/css/themes/semi-dark-layout.css">
    <link rel="stylesheet" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        .dropdown-cart .dropdown-menu-media {
            max-height: 400px;
        }
        .dropdown-cart .scrollable-container {
            max-height: 300px;
            overflow-y: auto;
        }
        .dropdown-cart .media {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #ebe9f1;
            transition: background-color 0.2s;
        }
        .dropdown-cart a:hover .media {
            background-color: #f8f8f8;
        }
        .dropdown-cart .cart-item-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #5e5873;
        }
        .dropdown-cart .notification-text {
            color: #b9b9c3;
            font-size: 0.8rem;
        }
        .badge-up {
            position: absolute;
            top: -5px;
            right: -8px;
            min-width: 18px;
            min-height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        /* Toastr overrides - fully opaque */
        .toast {
            opacity: 1 !important;
            color: #fff !important;
            font-weight: 600;
        }
        .toast-success {
            background-color: #28c76f !important;
        }
        .toast-error {
            background-color: #ea5455 !important;
        }
        .toast-warning {
            background-color: #ff9f43 !important;
        }
        .toast-info {
            background-color: #00cfe8 !important;
        }
        .toast a {
            color: #fff;
            text-decoration: underline;
            font-weight: 600;
        }
        .toast a:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>

    @stack('styles')
</head>
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    @include('layouts.partials.navbar')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.partials.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @yield('content')
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.partials.footer')
    <!-- END: Footer-->

    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/assets/js/config.js"></script>

    @stack('scripts')

    <script>
        // Global toastr configuration - opaque, no transparency
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000,
            extendedTimeOut: 2000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        if (typeof feather !== 'undefined') feather.replace();

        // Authentication check and user display
        $(document).ready(function() {
            const user = UserManager.getUser();
            const token = UserManager.getToken();

            if (!token || !user) {
                window.location.href = '/';
                return;
            }

            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            if (!profile_picture || profile_picture === 'null') {
                profile_picture = '/assets/avatar-default.png';
            }else{
                profile_picture = '/uploads/profile_pictures/' + profile_picture;
            }
            $('.user-profile-picture').attr('src', profile_picture);

            $('.logout-button').on('click', function(e) {
                e.preventDefault();
                UserManager.logout(true);
            });

            // Show menu items based on user role
            if (UserManager.isSuperAdmin()) {
                // Superadmin sees everything
                $('.only_superadmin').removeClass('hidden');
                $('.admin_and_above').removeClass('hidden');
            } else if (UserManager.isAdmin()) {
                // Admin sees admin_and_above items only
                $('.admin_and_above').removeClass('hidden');
            }

            // Load cart preview in navbar
            loadNavbarCart();
        });

        // Function to load cart preview in navbar
        function loadNavbarCart() {
            const token = UserManager.getToken();
            if (!token) return;

            fetch(`${APP_CONFIG.API.BASE_URL}/v1/cart`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                if (!response.ok) throw new Error('Failed to load cart');
                return response.json();
            })
            .then(data => {
                console.log('Data:', data);
                const cart = data.cart || {};
                const items = cart.items || [];
                const itemCount = data.item_count || 0;

                // Update cart count badges
                $('.cart-item-count').text(itemCount);
                if (itemCount > 0) {
                    $('.badge-up.cart-item-count').show();
                } else {
                    $('.badge-up.cart-item-count').hide();
                }

                // Update cart dropdown
                const cartDropdown = $('#cart-dropdown-items');
                const locale = document.documentElement.lang || 'en';

                if (items.length === 0) {
                    cartDropdown.html(`
                        <div class="text-center p-2">
                            <i data-feather="shopping-cart" class="font-large-1 text-muted mb-1"></i>
                            <p class="text-muted">${locale === 'fr' ? 'Votre panier est vide' : 'Your cart is empty'}</p>
                        </div>
                    `);
                } else {
                    let html = '';
                    items.forEach(item => {
                        const product = item.product_model;
                        const imageUrl = product.primary_image || '/assets/images/product-placeholder.png';
                        const subtotal = (item.price_at_add * item.quantity).toFixed(2);

                        html += `
                            <a class="d-flex" href="/${locale === 'fr' ? 'fr/boutique' : 'en/shop'}/${product.slug}">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar bg-light-primary rounded" style="width: 50px; height: 50px;">
                                            <img src="${imageUrl}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="cart-item-title">${product.name}</h6>
                                        <small class="notification-text">${locale === 'fr' ? 'Qté' : 'Qty'}: ${item.quantity} × €${parseFloat(item.price_at_add).toFixed(2)}</small>
                                        <div><small class="font-weight-bold text-primary">€${subtotal}</small></div>
                                    </div>
                                </div>
                            </a>
                        `;
                    });

                    cartDropdown.html(html);
                }

                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(error => {
                console.error('Error loading cart:', error);
            });
        }

        // Refresh cart when returning to page
        $(document).on('visibilitychange', function() {
            if (!document.hidden) {
                loadNavbarCart();
            }
        });
    </script>
</body>
</html>
