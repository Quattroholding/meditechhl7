<x-guest-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Dermatología') }}
                @endslot
                @slot('li_1')
                    <a href="{{ route('dermatology.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a lista de pacientes
                    </a>
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Patient Info Card -->
            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">
                                        <i class="fas fa-user-circle text-primary"></i>
                                        {{ $patient->name }}
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">ID:</small>
                                            <strong>{{ $patient->id }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Género:</small>
                                            <strong>{{ ucfirst($patient->gender ?? 'N/A') }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Edad:</small>
                                            <strong>{{ $patient->age ?? 'N/A' }} años</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Identificación:</small>
                                            <strong>{{ $patient->identifier ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('patient.show', $patient->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Ver Ficha Completa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dermatology Component -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <livewire:consultation.dermatology
                                :patient_id="$patient->id"
                                wire:key="dermatology-{{ $patient->id }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-guest-layout>
