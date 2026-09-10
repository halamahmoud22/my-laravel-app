<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('page-title', '') - {{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
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
                        @auth
                            <a href="{{ route('cart.index') }}" class="btn btn-light-primary btn-sm"><i class="ki-duotone ki-basket fs-2 me-1"><span class="path1"></span><span class="path2"></span></i>Cart</a>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light-primary btn-sm">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Create account</a>
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
                    <span class="text-muted fs-7">A considered collection for everyday life.</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
