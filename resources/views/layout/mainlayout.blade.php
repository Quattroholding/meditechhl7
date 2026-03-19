<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @include('layout.partials.head')

    @yield('css')
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/652b8e06e9.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    @yield('scripts')
</head>
@if (!Route::is(['error-404', 'error-500']))

    <body>
@endif
@if (Route::is(['error-404', 'error-500']))

    <body class="error-pages">
@endif
@if (!Route::is(['change-password2', 'confirm-mail','error-404','error-500','forgot-password','login','lock-screen','register']))
    <div class="main-wrapper">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MP5P532M"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif
@if (Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register']))
    <div class="main-wrapper login-body">
@endif
@if(Route::is(['error-404','error-500']))
<div class="main-wrapper error-wrapper">
    @endif
    @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500']) && auth()->user())
        @include('layout.partials.header')
        @include('layout.partials.sidebar')
    @endif
    @yield('content')
</div>
@component('components.modal-popup')
@endcomponent
<div class="sidebar-overlay" data-reff=""></div>
@include('layout.partials.footer-scripts')
</body>

</html>
