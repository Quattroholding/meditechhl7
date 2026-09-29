<?php $page = 'documents'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Gestión de Documentos
                @endslot
                @slot('li_1')
                    Documentos
                @endslot
            @endcomponent
            <!-- /Page Header -->

            @include('partials.message')
            {{--}}
            <!-- Upload Form -->
            @can('documents.upload')
            <div class="row mb-4">
                <div class="col-12">
                    @livewire('documents.document-upload-form')
                </div>
            </div>
            @endcan
            {{--}}

            <!-- Document List -->
            <div class="row">
                <div class="col-12">
                    @livewire('documents.document-review-list')
                </div>
            </div>
        </div>
    </div>
@endsection
