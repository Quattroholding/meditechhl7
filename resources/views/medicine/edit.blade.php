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
                            <li class="breadcrumb-item active">Editar Medicamento</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Editar Medicamento</h4>
                        </div>
                        <div class="card-body">
                            <livewire:medicine.modal-save />
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Livewire.dispatch('editMedicineModal', {{ $medicine_id }});
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection