<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"><head>
    <title>@yield('page-title', '') - {{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle' . (app()->getLocale() === 'ar' ? '.rtl' : '') . '.css') }}" rel="stylesheet" type="text/css" />    @stack('styles')
</head>
<body id="kt_body" class="bg-light">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid">

            <div class="header bg-white" id="kt_header">
                <div class="container-xxl d-flex align-items-center justify-content-between py-4">
                    <a href="{{ route('home') }}" class="d-flex align-items-center text-dark text-hover-primary">
                        <img src="{{ asset('assets/media/logos/logo-1-dark.svg') }}" alt="{{ config('app.name') }}" class="h-25px" />
                    </a>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Language Switcher -->
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-light' }} px-3 py-1">EN</a>
                            <span class="text-muted">|</span>
                            <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-light' }} px-3 py-1">AR</a>
                        </div>

                        @auth
                            <a href="{{ route('cart.index') }}" class="btn btn-light-primary btn-sm"><i class="ki-duotone ki-basket fs-2 me-1"><span class="path1"></span><span class="path2"></span></i>{{ __('store.cart') }}</a>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">{{ __('store.dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light-primary btn-sm">{{ __('user.login') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('user.create_account') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <main class="flex-column-fluid">
                <div class="container-xxl py-12">
                    @yield('content')
                </div>
            </main>

            <footer class="bg-white border-top py-8">
                <div class="container-xxl d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <span class="text-muted fw-semibold">{{ config('app.name', 'Laravel') }}</span>
                    <span class="text-muted fs-7">{{ __('store.footer_tagline') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
