<?php $page = 'blank-page'; ?>
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
                    Blank Page
                @endslot
            @endcomponent
            <!-- /Page Header -->
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
