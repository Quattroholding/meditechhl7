<x-app-layout>
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
            <div class="good-morning-blk">
                <div class="row" 
                style= "background-image: url('{{ URL::asset('/assets/img/banner2.png') }}');
                        background-size: cover;
                        background-repeat: no-repeat; 
                        background-position: center; "
                        >
                    <div class="col-md-6">
                        <div class="morning-user text-white">
                            <h2>
                                <span class="typewriter-text">{{__('generic.hello')}}, {{auth()->user()->full_name}}</span>
                            </h2>
                            <p><span class="typewriter-text text-white">{{__('generic.welcome')}}</span></p>

                        </div>
                    </div>
                    {{--}}<div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/banner2.png') }}" alt="">
                        </div>
                    </div>{{--}}
                </div>
            </div>
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

            @component('components.notification-box')
            @endcomponent
        </div>
</x-app-layout>
