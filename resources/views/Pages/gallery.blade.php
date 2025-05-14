<?php $page = 'gallery'; ?>
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
                    Gallery
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div id="lightgallery" class="row">
                @php
                    $json = file_get_contents(public_path('../public/assets/json/gallery.json'));
                    $gallery = json_decode($json, true);
                @endphp
                @foreach ($gallery as $item)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 m-b-20">
                        <a href="{{ URL::asset('/assets/img/blog/' . $item['Image']) }}">
                            <img class="img-thumbnail" src="{{ URL::asset('/assets/img/blog/' . $item['Image']) }}"
                                alt="">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
