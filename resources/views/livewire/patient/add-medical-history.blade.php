<div>
    <button wire:click="openModal()" class="btn-head btn-head-light">  📚 {{ __('patient.medical_history.title')}}</button>
    @if($showModal)
    <!-- Modal -->
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 600px;">
                <div class="modal-header">
                    <h2 class="modal-title" id="myLargeModalLabel" style="color: #000;">{{ __('patient.medical_history_form.add_modal_title') }} : {{ $patient->name }}</h2>
                    <button wire:click="$set('showModal', false)" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="input-block local-forms">
                    <x-input-label for="categoria" :value="__('patient.category')" required="true"/>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        @php
                            $categoryIcons = [
                                'medication' => '💊',
                                'allergy' => '⚠️',
                                'surgery' => '🏥',
                                'chronic-illness' => '🔴',
                                'hospitalization' => '🛏️',
                                'immunization' => '💉',
                                'family-history' => '👨‍👩‍👧',
                                'social-history' => '👤',
                                'other' => '📋',
                            ];
                        @endphp
                        @foreach(\App\Models\Lista::medicalHistoryCategory() as $key => $label)
                            @php
                                $isSelected = $category === $key;
                                $hasExisting = in_array($key, $existingCategories);
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('category', '{{ $key }}')"
                                class="relative group p-5 text-center rounded-3xl border-2 transition-all duration-300 transform
                                    {{ $isSelected
                                        ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg scale-105'
                                        : 'border-gray-200 bg-white hover:scale-110 hover:shadow-2xl hover:border-blue-400 hover:bg-blue-50'
                                    }}"
                                title="{{ $label }}"
                            >
                                <div class="text-4xl mb-2 transition-transform duration-300 group-hover:scale-125 group-hover:rotate-12">{{ $categoryIcons[$key] ?? '📌' }}</div>
                                <div class="text-xs font-bold text-gray-700 truncate group-hover:text-blue-600">{{ $label }}</div>

                                @if($hasExisting)
                                    <div class="absolute -top-3 -right-3 bg-gradient-to-br from-green-400 to-green-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold shadow-lg border-2 border-white">
                                        ✓
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('category')" class="mt-2"/>
                </div>
                <div class="input-block local-forms">
                    <div class="form-group local-forms">
                        <x-input-label for="titulo" :value="__('patient.history_title')" required="true"/>
                        <x-text-input wire:model="title" class="block mt-1 w-full datetimepicker" type="text" name="title" :placeholder="__('patient.medical_history_form.title_placeholder')"/>
                        <x-input-error :messages="$errors->get('title')"/>
                    </div>
                </div>
                <div class="input-block local-forms">
                    <div class="form-group local-forms">
                        <x-input-label for="fecha_occurencia" :value="__('patient.date')" required="true"/>
                        <x-text-input wire:model="occurrence_date" class="block mt-1 w-full datetimepicker" type="date" name="occurrence_date"/>
                        <x-input-error :messages="$errors->get('occurrence_date')"/>
                    </div>
                </div>
                <div class="input-block local-forms">
                    <x-input-label for="descripcion" :value="__('patient.description')" />
                    <x-textarea-input wire:model="description" class="block mt-1 w-full" name="description"/>
                    <x-input-error :messages="$errors->get('description')"/>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 15px;">
                    <button wire:click="save" class="btn btn-primary" style="flex: 1;">{{ __('generic.save') }}</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary">{{ __('generic.cancel') }}</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    @endif
    <script>

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrMedicalHistory', (event) => {
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
