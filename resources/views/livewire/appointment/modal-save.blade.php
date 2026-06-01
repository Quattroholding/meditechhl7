<div>
    @if($showModal)
        @teleport('body')
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h2 class="modal-title">{{ $title }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <!-- MENSAJE DEL MODAL -->
                <div x-data="{ message: '' }"
                    x-on:cita-message.window="message = $event.detail.message; $store.debug.log('Evento recibido: ' + message)">
                   {{--}} <div x-show="message" x-text="message" class="mb-4"></div>{{--}}
                    <div class="alert alert-error alert-dismissible fade show mb-6" x-show="message" x-text="message" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <form wire:submit="saveAppointment">
                    @if(!auth()->user()->hasRole('paciente'))
                        <div class="input-block local-formss">
                            <x-input-label for="patient_id" :value="__('patient.title')" required/>
                            <livewire:components.patient-selector wire:model="patient_id" :required="true" />
                            <x-input-error :messages="$errors->get('patient_id')"/>
                        </div>
                    @else
                        <input type="hidden" wire:model="patient_id" value="{{$patient_id}}">
                    @endif
                    @if((!auth()->user()->hasRole('doctor')) or (auth()->user()->hasRole('doctor') && auth()->user()->practitioner->qualifications->count()>1))
                    <div class="input-block local-forms">
                        <x-input-label for="medical_speciality_id" :value="__('appointment.speciality')" required/>
                        <x-select-input wire:model="medical_speciality_id" wire:change="loadDoctors()" id="medical_speciality_id" name="medical_speciality_id" :options="$especialidades"  class="block w-full"/>
                        <x-input-error :messages="$errors->get('medical_speciality_id')"/>
                    </div>
                    @else
                        <input type="hidden" wire:model="medical_speciality_id" value="{{ auth()->user()->practitioner->qualifications->first->code}}" id="doctor_id" name="doctor_id">
                    @endif
                    @if(!auth()->user()->hasRole('doctor'))
                        <div class="input-block local-forms">
                            <x-input-label for="doctor_id" :value="__('doctor.title')" required/>
                            <x-select-input wire:model.live="doctor_id" id="doctor_id" name="doctor_id" :options="$practitioners"  class="block w-full"/>
                            <x-input-error :messages="$errors->get('doctor_id')"/>
                        </div>
                    @else
                        <input type="hidden" wire:model="doctor_id" value="{{$doctor_id}}" id="doctor_id" name="doctor_id">
                    @endif
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="Fecha" :value="__('appointment.date')" required/>
                            <input wire:model.live="appointment_date" type="date" class="form-control-full">
                            <x-input-error :messages="$errors->get('appointment_date')"/>
                        </div>
                        <div class="input-block local-forms">
                            <x-input-label for="appointment_time" :value="__('appointment.time')" required/>
                            <input wire:model="appointment_time" type="time" class="form-control-full">
                            <x-input-error :messages="$errors->get('appointment_time')"/>
                        </div>
                    </div>
                    @if($doctor_id && $appointment_date)
                        @php
                            $workingHour = $this->getWorkingHourForDate($appointment_date);
                        @endphp
                        @if($workingHour)
                            <div class="alert alert-info" style="margin-top: 10px;">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ __('appointment.working_hours') }}:</strong>
                                {{ __('appointment.doctor_works') }} {{ substr($workingHour->start_time, 0, 5) }} {{ __('appointment.to') }} {{ substr($workingHour->end_time, 0, 5) }}
                                {{ __('appointment.in') }} {{ $workingHour->consultingRoom->full_name_branch ?? 'N/A' }}
                            </div>
                        @elseif($this->isDoctorWorkingOnDate($appointment_date) === false)
                            <div class="alert alert-warning" style="margin-top: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>{{ __('appointment.attention') }}:</strong>
                                {{ __('appointment.doctor_no_schedule') }}
                            </div>
                        @endif
                    @endif
                    <div  class="input-block local-forms">
                        <x-input-label class="form-label" required>{{__('appointment.duration')}}</x-input-label>
                        <select wire:model="duration" class="form-control-full" required>
                            <option value="15">{{ __('appointment.duration_15') }}</option>
                            <option value="30">{{ __('appointment.duration_30') }}</option>
                            <option value="45">{{ __('appointment.duration_45') }}</option>
                            <option value="60">{{ __('appointment.duration_60') }}</option>
                            <option value="90">{{ __('appointment.duration_90') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('duration')"/>
                    </div>
                    {{--}}
                    <div class="input-block local-forms">
                        <x-input-label for="consulting_room_id" :value="__('appointment.type')" required/>
                        <select wire:model="consultation_type" class="form-control-full" required>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                        </select>
                        <x-input-error :messages="$errors->get('consultation_type')"/>
                    </div>
                    {{--}}
                    <div class="input-block local-forms">
                        <x-input-label for="consulting_room_id" :value="__('appointment.consulting_room')" required/>
                        <x-select-input  wire:model="consulting_room_id" name="consulting_room_id" :options="$consultorios"  class="block w-full"/>
                        <x-input-error :messages="$errors->get('consulting_room_id')"/>
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="service_type" :value="__('appointment.service_type')" required/>
                        <x-text-input wire:model="service_type" id="service_type" class="block mt-1 w-full" type="text" name="service_type" placeholder="{{ __('appointment.service_type_placeholder') }}"/>
                        <x-input-error :messages="$errors->get('service_type')"/>
                    </div>
                    <div class="input-block local-forms">
                        <label class="form-label">{{__('appointment.reason')}}</label>
                        <textarea wire:model="description" class="form-control-full" rows="3" placeholder="{{ __('appointment.reason_placeholder') }}">{{$description}}</textarea>
                        <x-input-error :messages="$errors->get('description')"/>
                    </div>
                    @if(!auth()->user()->canScheduleAppointments())
                        <x-subscription-alert :showFirstCol="false"/>
                    @endif
                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        @if(auth()->user()->can('edit',$appointment) or (!$appointment and auth()->user()->canScheduleAppointments()))
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                {{ $buttonSaveTitle }}
                            </button>
                        @endif
                        @if($appointment && auth()->user()->can('cancelled',$appointment))
                            <button type="button" wire:click="openCancelModal" class="btn btn-danger">
                                <i class="fas fa-times-circle"></i> {{ __('appointment.cancel_appointment') }}
                            </button>
                        @endif
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">{{__('generic.cancel')}}</button>
                        {{--}}
                        @if(auth()->user()->can('cancelled',$appointment))
                            <button type="button" wire:click="deleteAppointment({{ $appointment->id }})" class="btn" style="background: #dc3545; color: white;" wire:confirm="¿Está seguro de eliminar esta cita?">{{__('generic.delete')}}</button>
                        @endif
                        {{--}}
                    </div>
                </form>
            </div>
        </div>
        @endteleport
    @endif

    {{-- Modal de Cancelación --}}
    <x-appointment.cancellation-modal
        :show="$showCancelModal"
        :cancellation-reason="$cancellationReason"
        :custom-cancellation-reason="$customCancellationReason"
    />

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('debug', {
                    log(message) {
                        console.log(message);
                    }
                });
            });
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('showToastr', (event) => {
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

