<x-app-layout>
    @section('css')
        <!-- Dashboard Animations V2 CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/assets/css/dashboard-animations-v2.css?time='.time()) }}">
        <!-- Dashboard Responsive CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/assets/css/dashboard-responsive.css?time='.time()) }}">
    @endsection
     <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard de Contabilidad
                @endslot
                @slot('li_1')
                    Suscripciones y Finanzas
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Welcome Block V2 - Componente Livewire Reutilizable -->
            @livewire('welcome-salute')

            <div class="dashboard-init-v2">
                <x-dashboard>
                    <!-- FIRST ROW -->
                    <x-dashboard-tile position="a1:c1" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.monthly-revenue-card')
                    </x-dashboard-tile>
                    <x-dashboard-tile position="d1:f1" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.pending-payments-card')
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g1:i1" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.overdue-invoices-card')
                    </x-dashboard-tile>
                    <x-dashboard-tile position="j1:l1" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.active-subscriptions-card')
                    </x-dashboard-tile>
                    <!-- SECOND ROW -->
                    <x-dashboard-tile position="a2:i2" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.revenue-chart')
                    </x-dashboard-tile>
                    <x-dashboard-tile position="j2:l2" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.payment-methods-chart')
                    </x-dashboard-tile>
                    <!-- THIRD ROW -->
                    <x-dashboard-tile position="a3:l3" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.invoice-status-chart')
                    </x-dashboard-tile>
                    <!-- FOUR ROW -->
                    <x-dashboard-tile position="a4:l4" :refresh-interval-in-seconds="120">
                        @livewire('dashboard.accounting.pending-payments-table')
                    </x-dashboard-tile>
                </x-dashboard>
            </div>

            @component('components.notification-box')
            @endcomponent
        </div>
     </div>
<script src="{{ URL::asset('/assets/js/dashboard-animations-v2-simple.js?time='.time()) }}"></script>
</x-app-layout>
