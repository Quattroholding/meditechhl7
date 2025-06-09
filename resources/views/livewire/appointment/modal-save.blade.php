<div>
    @include('partials.message')
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{ $title }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <form wire:submit="saveAppointment">
                    @if(!auth()->user()->hasRole('paciente'))
                        <div class="input-block local-forms">
                            <x-input-label for="patient_id" :value="__('patient.title')" required/>
                            <x-select-input  wire:model="patient_id" id="patient_id" name="patient_id" :options="\App\Models\Patient::get()->pluck('name','id')->toArray()"  class="block w-full"/>
                            <x-input-error :messages="$errors->get('patient_id')"/>
                        </div>
                    @else
                        <input type="hidden" wire:model="patient_id" value="{{$patient_id}}">
                    @endif
                    <div class="input-block local-forms">
                        <x-input-label for="medical_speciality_id" :value="__('appointment.speciality')" required/>
                        <x-select-input wire:model="medical_speciality_id" wire:change="changeSpeciality()" id="medical_speciality_id" name="medical_speciality_id" :options="$especialidades"  class="block w-full"/>
                        <x-input-error :messages="$errors->get('medical_speciality_id')"/>
                    </div>
                    @if(!auth()->user()->hasRole('doctor'))
                        <div class="input-block local-forms">
                            <x-input-label for="doctor_id" :value="__('doctor.title')" required/>
                            <x-select-input wire:change="changeDoctor()" wire:model="doctor_id" id="doctor_id" name="doctor_id" :options="$practitioners"  class="block w-full"/>
                            <x-input-error :messages="$errors->get('doctor_id')"/>
                        </div>
                    @else
                        <input type="hidden" wire:model="doctor_id" value="{{$doctor_id}}">
                    @endif
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="Fecha" :value="__('appointment.date')" required/>
                            <input wire:model="appointment_date" type="date" class="form-control-full">
                            <x-input-error :messages="$errors->get('appointment_date')"/>
                        </div>
                        <div class="input-block local-forms">
                            <x-input-label for="appointment_time" :value="__('appointment.time')" required/>
                            <input wire:model="appointment_time" type="time" class="form-control-full">
                            <x-input-error :messages="$errors->get('appointment_time')"/>
                        </div>
                    </div>
                    <div  class="input-block local-forms">
                        <x-input-label class="form-label" required>{{__('appointment.duration')}}</x-input-label>
                        <select wire:model="duration" class="form-control-full" required>
                            <option value="15">15 minutos</option>
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">60 minutos</option>
                            <option value="90">90 minutos</option>
                        </select>
                        <x-input-error :messages="$errors->get('duration')"/>
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="consulting_room_id" :value="__('appointment.consulting_room')" required/>
                        <x-select-input  wire:model="consulting_room_id" name="consulting_room_id" :options="$consultorios"  class="block w-full"/>
                        <x-input-error :messages="$errors->get('consulting_room_id')"/>
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="service_type" :value="__('appointment.service_type')" required/>
                        <x-text-input wire:model="service_type" id="service_type" class="block mt-1 w-full" type="text" name="service_type" placeholder="EJ:Consulta Especializada"/>
                        <x-input-error :messages="$errors->get('service_type')"/>
                    </div>
                    <div class="input-block local-forms">
                        <label class="form-label">{{__('appointment.reason')}}</label>
                        <textarea wire:model="description" class="form-control-full" rows="3" placeholder="Describir el motivo de la consulta">{{$description}}</textarea>
                        <x-input-error :messages="$errors->get('description')"/>
                    </div>
                    {{--}}
                    <div class="form-group">
                        <label class="form-label">Notas Adicionales</label>
                        <textarea wire:model="notes" class="form-control-full" rows="2" placeholder="Notas adicionales"></textarea>
                        @error('notes') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    @if($appointment)
                        <div class="form-group">
                            <label class="form-label">Estado de la Cita</label>
                            <div class="status-buttons">
                                <button type="button" wire:click="$set('status', 'booked')" class="status-btn appointment-booked {{ $status === 'booked' ? 'active' : '' }}">Programada</button>

                                <button type="button" wire:click="$set('status', 'arrived')" class="status-btn appointment-arrived {{ $status === 'arrived' ? 'active' : '' }}">Llegada</button>

                                <button type="button" wire:click="$set('status', 'checked-in')" class="status-btn appointment-checked-in {{ $status === 'checked-in' ? 'active' : '' }}">En Progreso</button>

                                <button type="button" wire:click="$set('status', 'fulfilled')" class="status-btn appointment-fulfilled {{ $status === 'fulfilled' ? 'active' : '' }}">Completada</button>
                                <button type="button" wire:click="$set('status', 'cancelled')" class="status-btn appointment-cancelled {{ $status === 'cancelled' ? 'active' : '' }}">Cancelada</button>
                                <button type="button" wire:click="$set('status', 'no-show')" class="status-btn appointment-noshow {{ $status === 'no-show' ? 'active' : '' }}">No Asistió</button>
                            </div>
                        </div>
                    @endif
                     {{--}}
                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        @if(auth()->user()->can('edit',$appointment) or !$appointment)
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                {{ $buttonSaveTitle }}
                            </button>
                        @endif
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                        @if(auth()->user()->can('delete',$appointment))
                            <button type="button" wire:click="deleteAppointment({{ $appointment->id }})" class="btn" style="background: #dc3545; color: white;" onclick="return confirm('¿Está seguro de eliminar esta cita?')">Eliminar</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

