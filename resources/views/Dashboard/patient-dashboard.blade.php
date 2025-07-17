<?php $page = 'patient-dashboard'; ?>
<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    {{__('patient.title')}}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2><span class="typewriter-text">{{__('generic.hello')}} , {{auth()->user()->patient->name}}</span></h2>
                            <p class="typewriter-text">{{ auth()->user()->patient->identifier_type }}: {{ auth()->user()->patient->identifier }} {{--}}
                            @if(auth()->user()->patient->age) {{ auth()->user()->patient->age }} años • @endif
                                {{ ucfirst(__('patient.'.auth()->user()->patient->gender) ?? 'No especificado') }}{{--}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-03.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.message')
            <!-- Health Overview Stats -->
            <div class="dashboard-init">
                @livewire('patient.dashboard.overview')
                <div class="row mt-4">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <div class="row">
                            <!-- Upcoming Appointments -->
                            <div class="col-12 mb-4">
                                @livewire('patient.dashboard.upcoming-appointments', ['limit' => 3])
                            </div>

                            <!-- Recent Consultations -->
                            <div class="col-12 mb-4">
                                @livewire('patient.dashboard.recent-consultations', ['limit' => 3])
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <div class="row">
                            <!-- Outstanding Invoices -->
                            <div class="col-12 mb-4">
                                @livewire('patient.dashboard.outstanding-invoices', ['limit' => 3])
                            </div>

                            <!-- Medical Summary -->
                            <div class="col-12 mb-4">
                                @livewire('patient.dashboard.medical-summary')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
