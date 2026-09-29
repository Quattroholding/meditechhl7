<?php $page = 'documents'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        @component('components.page-header')
            @slot('title')
                Subir Nuevo Documento
            @endslot
            @slot('li_1')
                Documentos
            @endslot
        @endcomponent
        <!-- /Page Header -->

        @include('partials.message')

        <!-- Upload Form -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Subir Documento (Factura, Comprobante, etc.)</h4>
                    </div>
                    <div class="card-body">
                        @livewire('documents.document-upload-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
