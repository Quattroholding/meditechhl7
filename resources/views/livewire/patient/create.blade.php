<div>
    @section('scripts')
        <script src="{{ URL::asset('/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    @stop
    @include('partials.message')
    <form wire:submit="savePatient" id="form">
        @csrf
        <div class="row">
            <!-- ID TYPE NUMBER -->
            <div class="col-12 col-md-4 col-xl-4">
                <div class="input-block  local-forms">
                    <x-input-label for="id_type" :value="__('patient.id_type')" required="true"/>
                    <x-select-input wire:model.live="id_type" name="id_type" :options="\App\Models\Lista::documentType()" :selected="['CC']" class="block mt-1 w-full"/>
                    <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                </div>
            </div>
            <!-- ID NUMBER -->
            <div class="col-12 col-md-4 col-xl-4">
                <div class=" input-block  local-forms ">
                    <x-input-label for="id_number" :value="__('patient.full_id_number')" required="true"/>
                    <x-text-input wire:model.live="id_number" id="id_number" class="block mt-1 w-full" type="text" placeholder="{{ $this->getIdPlaceholder() }}" value="" autofocus/>
                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                    <small class="text-muted">{{ $this->getIdPlaceholder() }}</small>
                </div>
            </div>
            <!-- GENDER -->
            <div class="col-12 col-md-4 col-xl-4">
                <div class="input-block local-forms ">
                    <x-input-label for="gender" :value="__('patient.gender')" required="true"/>
                    <x-select-input wire:model="gender" name="gender" :options="\App\Models\Lista::gender()" :selected="[null]" class="block w-full"/>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>
            </div>
        </div>
        @if($patientExists)
        <div class="row">
            <p>Este usuario ya se encuentra registrado en el sistema, ¿Desea asociarlo a su empresa?</p>
            <a class="btn btn-primary cancel-form" id="associate-yes" wire:click.stop="asociar()">  Asociar </a>
        </div>
        @endif
        @if($patientDontExists)
        <div id="form-patient" >
            <div class="row ">
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- First Name -->
                    <div class="input-block local-forms">
                        <x-input-label for="first_name" :value="__('patient.first_name')" required="true"/>
                        <x-text-input wire:model="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- Last Name -->
                    <div class="input-block local-forms">
                        <x-input-label for="last_name" :value="__('patient.last_name')" required="true"/>
                        <x-text-input wire:model="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')"/>
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <!-- EMAIL -->
                    <div class="input-block local-forms">
                        <x-input-label for="email" value="{{__('patient.email').'/usuario'}}" required="true"/>
                        <x-text-input wire:model="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="blood_type" :value="__('patient.blood_type')" class="local-top"/>
                        <x-select-input wire:model="blood_type" name="blood_type" :options="\App\Models\Lista::bloodTypes()" :selected="[null]" class="block w-full"/>
                        <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                    </div>
                </div>
                <!-- BIRTHDATE -->
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms {{--}}cal-icon{{--}}">
                        <x-input-label for="birthdate" :value="__('patient.birthdate')" required="true"/>
                        <x-text-input wire:model="birthdate" id="birthdate" type="text" name="birthdate"  type="date" class="block mt-1 w-full" />
                        <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
                    </div>
                </div>

            </div>
            <div class="row">
                <!-- PHYSICAL ADDRESS -->
                <div class=" col-12 col-md-12 col-xl-12">
                    <div class="input-block local-forms">
                        <x-input-label for="physical_address" :value="__('patient.physical_address')" required/>
                        <x-textarea-input wire:model="physical_address" class="block mt-1 w-full" type="email" name="physical_address"/>
                        <x-input-error :messages="$errors->get('physical_address')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- COUNTRY -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="country_id" value="País"/>
                        <select wire:model.live="country_id" id="country_id" class="form-control">
                            <option value="">Seleccione un país...</option>
                            @foreach($countries as $country)
                                <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                    </div>
                </div>
                <!-- STATE -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="state_id" value="Provincia/Estado"/>
                        <select wire:model="state_id" id="state_id" class="form-control" @if(empty($states)) disabled @endif>
                            <option value="">Seleccione una provincia...</option>
                            @foreach($states as $state)
                                <option value="{{ $state['id'] }}">{{ $state['name'] }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state_id')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- PHONE -->
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="phone" :value="__('patient.phone')" required/>
                        <input   wire:model="phone" id="phone" class="block mt-1 w-full input-phone" type="tel" name="phone" value="{{old('phone')}}">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>
                <div class=" col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <x-input-label for="marital_status" :value="__('patient.marital_status')" required/>
                        <x-select-input wire:model="marital_status" name="marital_status" :options="\App\Models\Lista::maritalStatus()" :selected="[null]" class="block w-full"/>
                        <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="row">
                <div class="col-12">
                    <h6 class="mt-3 mb-2">Contacto de Emergencia (Opcional)</h6>
                </div>
            </div>
            <div class="row">
                <!-- CONTACT NAME -->
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="input-block local-forms">
                        <x-input-label for="contact_name" value="Nombre del Contacto"/>
                        <x-text-input wire:model="contact_name" id="contact_name" class="block mt-1 w-full" type="text" name="contact_name" :value="old('contact_name')"/>
                        <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                    </div>
                </div>
                <!-- CONTACT EMAIL -->
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="input-block local-forms">
                        <x-input-label for="contact_email" value="Email del Contacto"/>
                        <x-text-input wire:model="contact_email" id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email')"/>
                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                    </div>
                </div>
                <!-- CONTACT PHONE -->
                <div class="col-12 col-md-4 col-xl-4">
                    <div class="input-block local-forms">
                        <x-input-label for="contact_phone" value="Teléfono del Contacto"/>
                        <x-text-input wire:model="contact_phone" id="contact_phone" class="block mt-1 w-full" type="tel" name="contact_phone" :value="old('contact_phone')"/>
                        <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                    </div>
                </div>
            </div>
            <!-- File Upload Section -->
            <div class="row">
                <div class="col-12">
                    <div class="input-block local-forms">
                        <x-input-label for="archivos" value="Archivos del Paciente"/>
                        <input
                            type="file"
                            wire:model="archivos"
                            multiple
                            class="form-control @error('archivos.*') is-invalid @enderror"
                            id="archivos"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Puede subir múltiples archivos (máx. 1MB por archivo). Formatos permitidos: PDF, DOC, DOCX, JPG, PNG</small>
                        @error('archivos.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Indicador de carga -->
                        <div wire:loading wire:target="archivos" class="mt-2">
                            <span class="text-primary">
                                <i class="fas fa-spinner fa-spin"></i> Cargando archivos...
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista previa de archivos seleccionados -->
            @if($archivos)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Archivos seleccionados ({{ count($archivos) }})</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($archivos as $index => $archivo)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file me-2"></i>
                                                {{ $archivo->getClientOriginalName() }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                {{ number_format($archivo->getSize() / 1024, 2) }} KB
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{--}}
            <!-- Dependent Patient Section -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Relaciones Familiares</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input wire:model.live="is_dependent" class="form-check-input" type="checkbox" id="is_dependent">
                                <label class="form-check-label" for="is_dependent">
                                    Sí, este paciente es dependiente de otro paciente existente
                                </label>
                            </div>

                            @if($is_dependent)
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="primary_patient_id" value="Paciente Principal" required="true"/>
                                            <select wire:model.live="primary_patient_id" id="primary_patient_id" class="form-control">
                                                <option value="">Seleccione el paciente principal...</option>
                                                @foreach($primary_patients as $patient)
                                                    <option value="{{ $patient['id'] }}">{{ $patient['name'] }} ({{ $patient['identifier'] }})</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('primary_patient_id')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="relationship_type" value="Tipo de Relación" required="true"/>
                                            <select wire:model="relationship_type" id="relationship_type" class="form-control">
                                                <option value="">Seleccione la relación...</option>
                                                @foreach($this->getRelationshipOptions() as $code => $display)
                                                    <option value="{{ $code }}">{{ $display }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('relationship_type')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input wire:model="copy_insurance" class="form-check-input" type="checkbox" id="copy_insurance">
                                            <label class="form-check-label" for="copy_insurance">
                                                Copiar seguro médico del paciente principal
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input wire:model="is_emergency_contact" class="form-check-input" type="checkbox" id="is_emergency_contact">
                                            <label class="form-check-label" for="is_emergency_contact">
                                                Agregar como contacto de emergencia
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{--}}
            <div class="flex items-center justify-end mt-4">
                <div class="doctor-submit text-end">
                    <button type="submit" class="btn btn-primary me-2">     {{ __('button.register') }} </button>
                    <a class="btn btn-secondary me-2" href="{{ route('patient.index') }}">  {{ __('button.cancel') }}</a>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>
