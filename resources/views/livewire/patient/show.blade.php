<div>
    @if($patient)

    <livewire:patient.patient-head patient_id="{{$patient->id}}"/>

    <!-- Navigation Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}"
                            wire:click="switchTab('general')"
                            type="button" role="tab">
                        <i class="feather-user me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'dependents' ? 'active' : '' }}"
                            wire:click="switchTab('dependents')"
                            type="button" role="tab">
                        <i class="feather-users me-2"></i>Relaciones Familiares
                    </button>
                </li>
                @can('consultations.download_resumen')
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'conditions' ? 'active' : '' }}"
                            wire:click="switchTab('conditions')"
                            type="button" role="tab">
                        <i class="feather-activity me-2"></i>Condiciones
                    </button>
                </li>
                @endcan
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'insurance' ? 'active' : '' }}"
                            wire:click="switchTab('insurance')"
                            type="button" role="tab">
                        <i class="feather-shield me-2"></i>Seguros
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'appointments' ? 'active' : '' }}"
                            wire:click="switchTab('appointments')"
                            type="button" role="tab">
                        <i class="feather-calendar me-2"></i>Citas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'files' ? 'active' : '' }}"
                            wire:click="switchTab('files')"
                            type="button" role="tab">
                        <i class="feather-file-text me-2"></i>Archivos
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <!-- General Tab -->
                @if($activeTab === 'general')
                    <div class="tab-pane fade show active">
                        <livewire:patient.tabs.general :patient="$patient" />
                    </div>
                @endif

                <!-- Dependents Tab -->
                @if($activeTab === 'dependents')
                    <div class="tab-pane fade show active">
                        @if(in_array('dependents', $loadedTabs))
                            <livewire:patient.tabs.dependents :patient_id="$patient_id" />
                        @else
                            @include('livewire.patient.partials.skeleton-table')
                        @endif
                    </div>
                @endif
                @can('consultations.download_resumen')
                <!-- Conditions Tab -->
                @if($activeTab === 'conditions')
                    <div class="tab-pane fade show active">
                        @if(in_array('conditions', $loadedTabs))
                            <livewire:patient.tabs.conditions :patient_id="$patient_id" />
                        @else
                            @include('livewire.patient.partials.skeleton-table')
                        @endif
                    </div>
                @endif
                @endcan
                <!-- Insurance Tab -->
                @if($activeTab === 'insurance')
                    <div class="tab-pane fade show active">
                        @if(in_array('insurance', $loadedTabs))
                            <livewire:patient.tabs.insurance :patient_id="$patient_id" />
                        @else
                            @include('livewire.patient.partials.skeleton-table')
                        @endif
                    </div>
                @endif

                <!-- Appointments Tab -->
                @if($activeTab === 'appointments')
                    <div class="tab-pane fade show active">
                        @if(in_array('appointments', $loadedTabs))
                            <livewire:patient.tabs.appointments :patient_id="$patient_id" />
                        @else
                            @include('livewire.patient.partials.skeleton-table')
                        @endif
                    </div>
                @endif

                <!-- Files Tab -->
                @if($activeTab === 'files')
                    <div class="tab-pane fade show active">
                        @if(in_array('files', $loadedTabs))
                            <livewire:patient.tabs.files :patient_id="$patient_id" :key="'files-'.$patient_id" />
                        @else
                            @include('livewire.patient.partials.skeleton-table')
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    <script>

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrPatientShow', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });

    </script>
</div>
