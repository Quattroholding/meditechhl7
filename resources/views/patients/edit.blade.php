<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.title') }}
                @endslot
                @slot('li_1')
                      {{ __('generic.edit') }} {{ __('patient.titles') }}
                    @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.detail') }} {{ __('patient.title') }}</h4>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('patient.update',$data->id) }}" enctype="multipart/form-data" id="form">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- ID NUMBER -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block  local-forms">
                                            <x-input-label for="identifier_type" :value="__('patient.id_type')" required="true"/>
                                            <x-select-input name="identifier_type" :options="\App\Models\Lista::documentType()" :selected="[$data->identifier_type]" class="block mt-1 w-full"/>
                                            <x-input-error :messages="$errors->get('identifier_type')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-8">
                                        <div class=" input-block  local-forms ">
                                            <x-input-label for="identifier" :value="__('patient.full_id_number')" required="true"/>
                                            <x-text-input id="identifier" class="block mt-1 w-full" type="text" name="identifier" value="{{$data->identifier}}" autofocus/>
                                            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <!-- First Name -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="given_name" :value="__('patient.first_name')" required="true"/>
                                            <x-text-input id="given_name" class="block mt-1 w-full" type="text" name="given_name" :value="$data->given_name" required />
                                            <x-input-error :messages="$errors->get('given_name')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <!-- Last Name -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="family_name" :value="__('patient.last_name')" required="true"/>
                                            <x-text-input id="family_name" class="block mt-1 w-full" type="text" name="family_name" :value="$data->family_name" required/>
                                            <x-input-error :messages="$errors->get('family_name')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <!-- EMAIL -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="email" value="{{__('patient.email').'/usuario'}}" required="true"/>
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$data->email"/>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- GENDER -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="gender" :value="__('patient.gender')" required="true"/>
                                            <x-select-input name="gender" :options="\App\Models\Lista::gender()" :selected="[$data->gender]" class="block w-full"/>
                                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                        </div>
                                    </div>
                                    <!-- BIRTHDATE -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms cal-icon">
                                            <x-input-label for="birth_date" :value="__('patient.birthdate')" required="true"/>
                                            <x-text-input id="birth_date" type="text" name="birth_date" :value="$data->birth_date" class="block mt-1 w-full datetimepicker" />
                                            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- PHYSICAL ADDRESS -->
                                    <div class=" col-12 col-md-12 col-xl-12">
                                        <div class="input-block local-forms">
                                            <x-input-label for="address" :value="__('patient.physical_address')" />
                                            <x-textarea-input id="address" class="block mt-1 w-full" type="email" name="address">{{$data->address}}</x-textarea-input>
                                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                        </div>
                                    </div>
                                    {{--}}<div class="col-12 col-md-6 col-xl-6">
                                        <!-- BILLING ADDRESS -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="billing_address" :value="__('patient.billing_address')" />
                                            <x-textarea-input id="billing_address" class="block mt-1 w-full" type="email" name="billing_address">{{$data->billing_address}}</x-textarea-input>
                                            <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                                        </div>
                                    </div>{{--}}
                                </div>
                                <div class="row">
                                    <!-- PHONE -->
                                    <div class=" col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="phone" :value="__('patient.phone')" />
                                            <input   wire:model="phone" id="phone" class="block mt-1 w-full input-phone" type="tel" name="phone" value="{{$data->phone}}">
                                            @error('phone') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class=" col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="marital_status" :value="__('patient.marital_status')" />
                                            <x-select-input wire:model="marital_status" name="marital_status" :options="\App\Models\Lista::maritalStatus()" :selected="[$data->marital_status]" class="block w-full" required/>
                                            @error('marital_status') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class=" col-12 col-md-6 col-xl-6">
                                        <!-- BLOOD TYPE -->
                                        <div class="input-block local-forms">
                                            <x-input-label for="blood_type" :value="__('patient.blood_type')" />
                                            <x-select-input name="blood_type" :options="\App\Models\Lista::bloodTypes()" :selected="[$data->blood_type]" class="block w-full"/>
                                            <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-top-form">
                                            <label class="local-top">Avatar <span class="login-danger">*</span></label>
                                            <div class="settings-btn upload-files-avator">
                                                <input type="file" accept="image/*" name="image" id="file" onchange="loadFile(event)" class="hide-input">
                                                <label for="file" class="upload">{{__('generic.Choose File')}}</label>
                                            </div>
                                            <div class="upload-images upload-size">
                                                @if($data->avatar())
                                                    <img src="{{ url('storage/'.$data->avatar()->path) }}" alt="Image" id="preview">
                                                @else
                                                    <img src="{{ URL::asset('/assets/img/favicon.png')  }}" alt="Image" id="preview">
                                                @endif
                                                <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                    <i class="feather-x-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Patient Relationships Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Relaciones Familiares</h5>
                                            </div>
                                            <div class="card-body">
                                                @if($currentRelationship)
                                                    <div class="alert alert-info">
                                                        <strong>Relación Actual:</strong>
                                                        {{ $currentRelationship->relationship_display }} de {{ $currentRelationship->relatedPatient->name ?? 'Paciente eliminado' }}
                                                        @if($currentRelationship->is_emergency_contact)
                                                            <span class="badge bg-warning ms-2">Contacto de Emergencia</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="is_dependent" name="is_dependent"
                                                           value="1" {{ $currentRelationship ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_dependent">
                                                        Este paciente es dependiente de otro paciente
                                                    </label>
                                                </div>

                                                <div id="dependency_fields" style="{{ $currentRelationship ? 'display: block;' : 'display: none;' }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="input-block local-forms">
                                                                <label class="local-top">Paciente Principal <span class="login-danger">*</span></label>
                                                                <select name="primary_patient_id" id="primary_patient_id" class="form-control">
                                                                    <option value="">Seleccione el paciente principal...</option>
                                                                    @foreach($availablePatients as $patient)
                                                                        <option value="{{ $patient->id }}"
                                                                                {{ $currentRelationship && $currentRelationship->related_patient_id == $patient->id ? 'selected' : '' }}>
                                                                            {{ $patient->name }} ({{ $patient->identifier }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-block local-forms">
                                                                <label class="local-top">Tipo de Relación <span class="login-danger">*</span></label>
                                                                <select name="relationship_type" id="relationship_type" class="form-control">
                                                                    <option value="">Seleccione la relación...</option>
                                                                    @php
                                                                        $relationshipOptions = [
                                                                            'CHILD' => 'Hijo/a',
                                                                            'SPOUSE' => 'Cónyuge',
                                                                            'PARENT' => 'Padre/Madre',
                                                                            'SIBLING' => 'Hermano/a',
                                                                            'DOMPART' => 'Pareja',
                                                                            'GRANDCHILD' => 'Nieto/a',
                                                                            'GRANDPRN' => 'Abuelo/a',
                                                                        ];
                                                                    @endphp
                                                                    @foreach($relationshipOptions as $code => $display)
                                                                        <option value="{{ $code }}"
                                                                                {{ $currentRelationship && $currentRelationship->relationship_code == $code ? 'selected' : '' }}>
                                                                            {{ $display }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="is_emergency_contact"
                                                                       name="is_emergency_contact" value="1"
                                                                       {{ $currentRelationship && $currentRelationship->is_emergency_contact ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="is_emergency_contact">
                                                                    Contacto de emergencia
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($relationships->count() > 0)
                                                <div class="mt-4">
                                                    <h6>Todas las Relaciones:</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Paciente Relacionado</th>
                                                                    <th>Tipo de Relación</th>
                                                                    <th>Contacto de Emergencia</th>
                                                                    <th>Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($relationships as $rel)
                                                                <tr>
                                                                    <td>{{ $rel->relatedPatient->name ?? 'Paciente eliminado' }}</td>
                                                                    <td>{{ $rel->relationship_display }}</td>
                                                                    <td>
                                                                        @if($rel->is_emergency_contact)
                                                                            <span class="badge bg-success">Sí</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">No</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($rel->is_active)
                                                                            <span class="badge bg-success">Activo</span>
                                                                        @else
                                                                            <span class="badge bg-danger">Inactivo</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                                        <a href="{{route('patient.index')}}" class="btn btn-secondary cancel-form">  {{ __('button.cancel') }}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDependentCheckbox = document.getElementById('is_dependent');
            const dependencyFields = document.getElementById('dependency_fields');

            if (isDependentCheckbox) {
                isDependentCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        dependencyFields.style.display = 'block';
                    } else {
                        dependencyFields.style.display = 'none';
                        // Clear selections when unchecked
                        document.getElementById('primary_patient_id').value = '';
                        document.getElementById('relationship_type').value = '';
                        document.getElementById('is_emergency_contact').checked = false;
                    }
                });
            }

            // Debug form submission
            const form = document.getElementById('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form is being submitted');
                    console.log('Action:', form.action);
                    console.log('Method:', form.method);
                    // Don't prevent default, let it submit
                });
            }
        });
    </script>
</x-app-layout>
