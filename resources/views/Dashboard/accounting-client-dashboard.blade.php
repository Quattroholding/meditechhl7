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
                    Dashboard Financiero
                @endslot
                @slot('li_1')
                    Contabilidad del Cliente
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Welcome Block V2 - Componente Livewire Reutilizable -->
            @livewire('welcome-salute')

            <div class="dashboard-init-v2">
                <x-dashboard>
                    <!-- FIRST ROW - KPIs PRINCIPALES -->
                    <x-dashboard-tile position="a1:c1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.total-assets-card />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="d1:f1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.total-liabilities-card />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g1:i1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.equity-card />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="j1:l1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.net-income-card />
                    </x-dashboard-tile>

                    <!-- SECOND ROW - CUENTAS POR COBRAR Y PAGAR -->
                    <x-dashboard-tile position="a2:f2" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.accounts-receivable-card />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g2:l2" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.accounts-payable-card />
                    </x-dashboard-tile>

                    <!-- THIRD ROW - GRÁFICOS DE INGRESOS Y GASTOS -->
                    <x-dashboard-tile position="a3:f3" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.revenue-expenses-chart />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g3:l3" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.balance-sheet-summary />
                    </x-dashboard-tile>

                    <!-- FOURTH ROW - ÚLTIMOS ASIENTOS Y CENTROS DE COSTO -->
                    <x-dashboard-tile position="a4:f4" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.recent-journal-entries />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g4:l4" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.cost-centers-distribution />
                    </x-dashboard-tile>

                    <!-- FIFTH ROW - TESORERÍA Y FLUJO DE CAJA -->
                    <x-dashboard-tile position="a5:f5" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.treasury-balance />
                    </x-dashboard-tile>
                    <x-dashboard-tile position="g5:l5" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.accounting-client.cash-flow-summary />
                    </x-dashboard-tile>
                </x-dashboard>
            </div>

            @component('components.notification-box')
            @endcomponent
        </div>
    </div>
    <script src="{{ URL::asset('/assets/js/dashboard-animations-v2-simple.js?time='.time()) }}"></script>
</x-app-layout>
