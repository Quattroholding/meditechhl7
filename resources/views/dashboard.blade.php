<?php $page = 'index'; ?>
@extends('layout.mainlayout')
@section('content')
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>{{__('generic.hi')}}, <span>{{auth()->user()->full_name}}</span></h2>
                            <p>{{__('generic.welcome')}}</p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-01.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <livewire:dashboard.counter function="appointments" wire:key="counter_appointments"/>
                <livewire:dashboard.counter function="patients" wire:key="counter_patients"/>
                <livewire:dashboard.counter function="encounters" wire:key="counter_encounters"/>
                <livewire:dashboard.counter function="invoices" wire:key="counter_invoices"/>
            </div>
            {{--}}
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title patient-visit">
                                <h4>Patient Visit by Gender</h4>
                                <div>
                                    <ul class="nav chat-user-total">
                                        <li><i class="fa fa-circle current-users" aria-hidden="true"></i>Male 75%</li>
                                        <li><i class="fa fa-circle old-users" aria-hidden="true"></i> Female 25%</li>
                                    </ul>
                                </div>
                                <div class="form-group mb-0">
                                    @livewire('select-dashboard')
                                </div>
                            </div>
                            <div id="patient-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-6 col-xl-3 d-flex">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title">
                                <h4>Patient by Department</h4>
                            </div>
                            <div id="donut-chart-dash" class="chart-user-icon">
                                <img src="{{ URL::asset('/assets/img/icons/user-icon.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--}}
            <div class="row">
                <div class="col-12 col-md-12  col-xl-4">
                    {{--}}COMPONENTE DE TOP 5 DE ESPECIALIDADES {{--}}
                    @livewire('dashboard.top-specialties')
                    {{--}}COMPONENTE QUE MUESTRA LOS APPOINTMENST SEGÚN EL ROL DEL USUARIO {{--}}
                    @livewire('admin-dashboard-appointments')
                </div>
                @livewire('admin-dashboard-patinets')
            </div>
            @component('components.notification-box')
            @endcomponent
        </div>
@endsection
