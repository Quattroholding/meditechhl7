<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @if (config('app.env') === 'production')
    <link rel="icon" href="{{secure_url('images/favicon.ico')}}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @else
        <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @endif
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
                            <x-subscription-alert />
                            @include('layout.partials.sidebar')
                        @endif
                      {{$slot}}
                    </div>
            </div>
    </div>
    @component('components.modal-popup') @endcomponent
    <div class="sidebar-overlay" data-reff=""></div>

    <!-- Setup Reminder Panel - Outside main-wrapper for proper fixed positioning -->
    @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500']))
        <livewire:setup-reminder-panel />
    @endif

    @include('layout.partials.footer-scripts')

    @stack('scripts')
</body>
</html>
