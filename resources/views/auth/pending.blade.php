<!DOCTYPE html>
<html class="loading" lang="{{ app()->getLocale() }}" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Account Pending Approval') }} - Monsieur WiFi</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/mrwifi-assets/MrWifi.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/page-auth.css') }}">
</head>
<body class="vertical-layout vertical-menu-modern blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2">
                    <div class="auth-inner my-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="text-center mb-2">
                                    <img src="{{ asset('app-assets/mrwifi-assets/Mr-Wifi.PNG') }}" alt="Monsieur WiFi" style="max-height: 60px;">
                                </div>

                                <h2 class="card-title fw-bold mb-1 text-center">{{ __('Account Pending Approval') }}</h2>

                                <p class="text-center mb-2">
                                    {{ __('Your account is awaiting administrator approval. You will be notified once your access has been granted.') }}
                                </p>

                                @if(session('zitadel_user'))
                                    <div class="alert alert-secondary text-center" role="alert">
                                        <strong>{{ __('Signed in as:') }}</strong> {{ session('zitadel_user.email') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('zitadel.logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 mt-1">
                                        {{ __('Sign Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
