@extends('layout.mainlayout')

@section('title', 'Tipos de Servicio')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title">Tipos de Servicio</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @livewire('service-type.data-table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('service_types.create')
        @livewire('service-type.modal-save')
    @endcan
</div>
@endsection
