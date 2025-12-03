<x-guest-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Dermatología') }}
                @endslot
                @slot('li_1')
                    {{ __('Seleccionar Paciente') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i>
                                Seleccione un paciente para acceder al módulo de dermatología y gestionar lesiones cutáneas.
                            </div>
                            @livewire('dermatology.patient-selector')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-guest-layout>
