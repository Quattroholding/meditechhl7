<div>
    <div class="page-wrapper">
        <div class="content">
            <div class="col-md-10 col-sm-12" id="paciente">
                @include('partials.message')
                @include('consultations.partials.head',array('patient'=>$patient,'appointment'=>$appointment))
            </div>
            <div class="col-md-10 col-sm-12">
                <x-accordion>
                    @foreach($encounter_sections as $section)
                        <div x-data="{ loaded: false }"
                             x-intersect="setTimeout(() => { loaded = true }, {{ $section->id * 200 }})"
                        >
                            <x-accordion-item data-id="{{$section->id}}" title="{{$section->name_esp}}" :isOpen="false" >
                                <template x-if="loaded">
                                    <div x-transition:enter="transition ease-out duration-300">
                                        @livewire($section->livewire_component_name, ['encounter_id' => $encounter->id,'section_id'=>$section->id,'section_name'=>$section->name_esp, 'medical_specialty_id'=>$section->medical_speciality_id])
                                    </div>
                                </template>
                                <template x-if="!loaded">
                                    <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="animate-spin h-8 w-8 text-blue-500" ...></svg>
                                    </div>
                                </template>
                            </x-accordion-item>
                        </div>
                    @endforeach
                </x-accordion>
            </div>

            <div class="my-5">&nbsp;</div>
            <div class="my-5">&nbsp;</div>

        </div>

    </div>

    <!-- Botón Flotante de Licencia Médica -->
    @livewire('consultation.medical-leave-button', ['encounter_id' => $encounter_id])

    <!-- Botón Flotante de Dictado por Voz -->
    @livewire('consultation.voice-dictation-button', ['encounter_id' => $encounter_id])

    @include('consultations.partials.side_menu',array('appointment_id'=>$appointment->id,'patient_id'=>$patient->id,'encounter_id'=>$encounter_id))
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
    @if($appointment->isVirtual())
        <div class="my-3"></div>
        @livewire('consultation.virtual-consultation-room', [
        'appointment' => $appointment,
        'displayMode' => 'sidebar'
        ])

    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrConsultation', (event) => {
                // Livewire 3 passes array parameters as first argument
                const data = Array.isArray(event) ? event[0] : event;
                const type = data.type || 'info';
                const message = data.message || 'Operación completada';

                if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
                    toastr[type](message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                } else {
                    console.error('Toastr not available or invalid type:', type);
                }
            });
        });
    </script>
</div>
