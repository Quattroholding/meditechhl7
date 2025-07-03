@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('medicine.index') }}">Medicamentos</a></li>
                            <li class="breadcrumb-item active">Crear Medicamento</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('Crear Nuevo Medicamento') }}</h4>
                                </div>
                            </div>
                            <livewire:medicine.create-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
