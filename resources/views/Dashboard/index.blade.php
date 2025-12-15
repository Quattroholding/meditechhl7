<x-app-layout>
    @section('css')
        <!-- Dashboard Animations V2 CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/assets/css/dashboard-animations-v2.css?time='.time()) }}">
    @endsection
     <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Admin Dashboard
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Welcome Block V2 - Componente Livewire Reutilizable -->
            @livewire('welcome-salute')

            <!-- Dashboard Content con clase v2 -->
            <div class="dashboard-init-v2">
                <x-dashboard>
                    {{-- Fila 1: Counters (4 tiles de 3 columnas cada uno) --}}
                    <x-dashboard-tile position="a1:c1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.counter function="appointments" wire:key="counter_appointments"/>
                    </x-dashboard-tile>

                    <x-dashboard-tile position="d1:f1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.counter function="patients" wire:key="counter_patients"/>
                    </x-dashboard-tile>

                    <x-dashboard-tile position="g1:i1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.counter function="encounters" wire:key="counter_encounters"/>
                    </x-dashboard-tile>

                    <x-dashboard-tile position="j1:l1" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.counter function="invoices" wire:key="counter_invoices"/>
                    </x-dashboard-tile>


                    <x-dashboard-tile position="a2:f2" :refresh-interval-in-seconds="60">
                        <livewire:dashboard.tiles.realtime-stats wire:key="tile_realtime_stats" />
                    </x-dashboard-tile>


                    <x-dashboard-tile position="g2:l2" :refresh-interval-in-seconds="120">
                        <livewire:dashboard.tiles.appointments-by-status wire:key="tile_appointments_status" />
                    </x-dashboard-tile>

                    <x-dashboard-tile position="a3:f3" :refresh-interval-in-seconds="300">
                        @livewire('dashboard.top-specialties')
                    </x-dashboard-tile>

                    <x-dashboard-tile position="g3:l4" :refresh-interval-in-seconds="300">
                        <livewire:dashboard.tiles.revenue-chart wire:key="tile_revenue_chart" />
                    </x-dashboard-tile>

                    {{-- Fila 4: Appointments by Source (Pie Chart) --}}
                    <x-dashboard-tile position="a4:f4" :refresh-interval-in-seconds="300">
                        <livewire:dashboard.tiles.appointments-by-source wire:key="tile_appointments_source" />
                    </x-dashboard-tile>

                    {{-- Fila 5: Appointments Table + Appointments by Specialties --}}
                    <x-dashboard-tile position="a5:f5" :refresh-interval-in-seconds="120">
                        @livewire('admin-dashboard-appointments')
                    </x-dashboard-tile>


                    <x-dashboard-tile position="g5:l5" :refresh-interval-in-seconds="300">
                        @livewire('admin.appointments-by-specialties')
                    </x-dashboard-tile>
                </x-dashboard>
            </div>

            @component('components.notification-box')
            @endcomponent
        </div>
     </div>
<script src="{{ URL::asset('/assets/js/dashboard-animations-v2-simple.js?time='.time()) }}"></script>
</x-app-layout>

