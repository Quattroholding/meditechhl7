<div class="patient-selector-wrapper"
     x-data="{
         hideTimeout: null,
         hideDropdown() {
             this.hideTimeout = setTimeout(() => {
                 $wire.showDropdown = false;
             }, 200);
         },
         cancelHide() {
             if (this.hideTimeout) {
                 clearTimeout(this.hideTimeout);
                 this.hideTimeout = null;
             }
         }
     }">

    <!-- Hidden input for form submission -->
    <input type="hidden" wire:model="selectedPatientId" name="patient_id">

    <!-- Search input -->
    <div class="position-relative">
        <div class="input-group">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchTerm"
                @focus="$wire.showDropdown = true"
                @blur="hideDropdown()"
                class="form-control {{ $required && !$selectedPatientId ? 'is-invalid' : '' }}"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
            >

            @if($selectedPatientId && $selectedPatientName)
                <button
                    type="button"
                    wire:click="clearSelection"
                    class="btn btn-outline-secondary btn-sm"
                    style="border-left: none;"
                >
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <!-- Dropdown results -->
        @if($showDropdown && count($patients) > 0)
            <div
                class="patient-dropdown position-absolute w-100 bg-white border border-top-0 shadow-sm"
                style="z-index: 1050; max-height: 300px; overflow-y: auto;"
                @mouseenter="cancelHide()"
                @mouseleave="hideDropdown()"
            >
                @foreach($patients as $patient)
                    <div
                        wire:click="selectPatient({{ $patient->id }})"
                        class="patient-option p-3 border-bottom cursor-pointer hover:bg-gray-50"
                        style="cursor: pointer;"
                        @mousedown.prevent
                    >
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-dark">{{ $patient->name }}</div>
                                <div class="text-muted small">
                                    {{ $patient->identifier_type }}: {{ preg_replace('/-(SPOUSE|CHILD|SELF)$/', '', $patient->identifier) }}
                                </div>
                                @if($patient->phone)
                                    <div class="text-muted small">
                                        <i class="fas fa-phone me-1"></i>{{ $patient->phone }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                @if($patient->birth_date)
                                    <div class="text-muted small">
                                        {{ $patient->age }} años
                                    </div>
                                @endif
                                @if($patient->gender)
                                    <div class="text-muted small">
                                        {{ $patient->gender === 'male' ? 'Masculino' : 'Femenino' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- No results message -->
        @if($showDropdown && strlen($searchTerm) >= 2 && count($patients) === 0)
            <div
                class="position-absolute w-100 bg-white border border-top-0 p-3 text-center text-muted"
                style="z-index: 1050;"
                @mouseenter="cancelHide()"
            >
                No se encontraron pacientes con "{{ $searchTerm }}"
            </div>
        @endif
    </div>
    <style>
        .patient-selector-wrapper {
            position: relative;
        }

        .patient-dropdown {
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
        }

        .patient-option:hover {
            background-color: #f8f9fa !important;
        }

        .patient-option:last-child {
            border-bottom: none;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</div>

