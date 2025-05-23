<?php $page = 'doctor-profile'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Doctors
                @endslot
                @slot('li_1')
                    Doctors Profile
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <livewire:doctor.head practitioner_id="{{$data->id}}"/>
                    <div class="row">
                        <livewire:doctor.profile-about practitioner_id="{{$data->id}}"/>
                        <livewire:doctor.profile-details practitioner_id="{{$data->id}}"/>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
