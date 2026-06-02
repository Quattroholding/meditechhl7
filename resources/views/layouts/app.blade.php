<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google Tag Manager - Data Layer -->
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            @auth
                'user_id': '{{ auth()->id() }}',
                'user_email_hash': '{{ md5(strtolower(trim(auth()->user()->email))) }}', // Hash para privacidad
                'user_role': '{{ auth()->user()->getRoleNames()->first() ?? "sin_rol" }}',
                'user_type': '{{ auth()->user()->getRoleNames()->first() ?? "sin_rol" }}',
                'login_status': 'logged_in',
                @if(auth()->user()->getCurrentClient())
                    'client_id': '{{ auth()->user()->getCurrentClient()->id }}',
                    'client_name': '{{ auth()->user()->getCurrentClient()->name }}',
                @endif
                @if(auth()->user()->branch_id)
                    'branch_id': '{{ auth()->user()->branch_id }}',
                @endif
            @else
                'login_status': 'logged_out',
            @endauth
            'page_type': '{{ Route::currentRouteName() }}',
            'environment': '{{ app()->environment() }}'
        });
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MP5P532M');</script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{url('images/iconoSAMI.ico')}}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @include('layout.partials.head')
    @yield('css')
    <!-- Responsive Table CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive-table.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/speciality-questions.css') }}">

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/652b8e06e9.js" crossorigin="anonymous"></script>
    @yield('scripts')
</head>
<body class="">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MP5P532M"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@if (!Route::is(['error-404', 'error-500']))
<body>
@endif
@if (Route::is(['error-404', 'error-500']))
 <body class="error-pages">
@endif
@if (!Route::is(['change-password2', 'confirm-mail','error-404','error-500','forgot-password','login','lock-screen','register']))
    <div class="main-wrapper">
        @endif
        @if (Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register']))
            <div class="main-wrapper login-body">
                @endif
                @if(Route::is(['error-404','error-500']))
                    <div class="main-wrapper error-wrapper">
                        @endif
                        @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500']))
                            @include('layout.partials.header')
                            @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500','two-factor.settings']))
                            <x-subscription-alert />
                            @endif
                            @include('layout.partials.sidebar')
                        @endif
                      {{$slot}}
                    </div>
            </div>
    </div>
    @component('components.modal-popup') @endcomponent
    <div class="sidebar-overlay" data-reff=""></div>

    <!-- Setup Reminder Panel - Outside main-wrapper for proper fixed positioning -->
    @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500','two-factor.settings','first-login.show','consultation.show']))
        <livewire:setup-reminder-panel />
    @endif

    @include('layout.partials.footer-scripts')

    @stack('scripts')
</body>
</html>
