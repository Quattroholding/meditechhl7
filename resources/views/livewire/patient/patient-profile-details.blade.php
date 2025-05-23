<div class="col-lg-9">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#clinica_history" data-bs-toggle="tab" aria-expanded="false" class="nav-link active" aria-selected="false" tabindex="-1" role="tab">
                       {{__('patient.clinical_history')}}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#account_settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link " aria-selected="true" role="tab">
                        {{__('patient.account_settings')}}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#security_settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link " aria-selected="true" role="tab">
                        {{__('patient.security_settings')}}
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane  show active" id="clinica_history" role="tabpanel">
                    <div class="doctor-personals-grp">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                                    @foreach($tabs2 as $tab)
                                        @isset($tab['title'])
                                            <li class="nav-item" role="presentation">
                                                <a href="#{{$tab['title']}}"   wire:click="changeActiveTab('{{$tab['title']}}')"
                                                   data-bs-toggle="tab"
                                                   aria-expanded="false"
                                                   class="nav-link {{$tab['active']}}"
                                                   aria-selected="false"
                                                   tabindex="-1"
                                                   role="tab">
                                                    {{__('patient.'.$tab['title'])}}
                                                    <span class="badge bg-danger ms-1">{{$tab['count']}}</span>
                                                </a>
                                            </li>
                                        @endisset
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @foreach($tabs2 as $tab)
                                        @isset($tab['title'])
                                            <div class="tab-pane {{$tab['active']}}" id="{{$tab['title']}}" role="tabpanel">
                                                @if($activeTab === $tab['title'])
                                                    <div wire:init>
                                                        @livewire($tab['component'], ['patient_id' => $patient_id], key($tab['title']))
                                                    </div>
                                                @endif
                                            </div>
                                        @endisset
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="account_settings" role="tabpanel">
                    <form method="POST" action="{{ route('patient.update',$patient->id) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="redirect" value="{{route('patient.profile',$patient->id)}}">
                        <div class="form-heading">
                            <h4>{{__('patient.account_settings')}}</h4>
                        </div>
                        <div class="row">
                            <!-- ID NUMBER -->
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="input-block  local-forms">
                                    <x-input-label for="id_type" :value="__('patient.id_type')" required="true"/>
                                    <x-select-input name="id_type" :options="\App\Models\Lista::documentType()" :selected="[$patient->identifier_type]" class="block mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-8">
                                <div class=" input-block  local-forms ">
                                    <x-input-label for="id_number" :value="__('patient.full_id_number')" required="true"/>
                                    <x-text-input id="id_number" class="block mt-1 w-full" type="number" name="id_number" value="{{$patient->identifier}}" autofocus/>
                                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- First Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="first_name" :value="__('patient.first_name')" required="true"/>
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="$patient->given_name" required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- Last Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="last_name" :value="__('patient.last_name')" required="true"/>
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="$patient->family_name" required/>
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- EMAIL -->
                                <div class="input-block local-forms">
                                    <x-input-label for="email" value="{{__('patient.email').'/usuario'}}" required="true"/>
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$patient->email"/>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- GENDER -->
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="gender" :value="__('patient.gender')" required="true"/>
                                    <x-select-input name="gender" :options="\App\Models\Lista::gender()" :selected="[$patient->gender]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>
                            </div>
                            <!-- BIRTHDATE -->
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="form-group local-forms cal-icon">
                                    <x-input-label for="birthdate" :value="__('patient.birthdate')" required="true"/>
                                    <x-text-input id="birthdate" type="text" name="text" :value="$patient->birthdate" class="block mt-1 w-full datetimepicker" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-6">
                                <!-- PHONE -->
                                <div class="input-block local-forms ">
                                    <x-input-label for="phone" :value="__('patient.phone')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="$patient->phone"/>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                            <div class=" col-12 col-md-6 col-xl-6">
                                <!-- BLOOD TYPE -->
                                <div class="input-block local-forms">
                                    <x-input-label for="blood_type" :value="__('patient.blood_type')" />
                                    <x-select-input name="blood_type" :options="\App\Models\Lista::bloodTypes()" :selected="[$patient->blood_type]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- PHYSICAL ADDRESS -->
                            <div class=" col-12 col-md-6 col-xl-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="physical_address" :value="__('patient.physical_address')" />
                                    <x-textarea-input id="physical_address" class="block mt-1 w-full" type="email" name="physical_address">{{$patient->address}}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                                <a href="{{route('patient.index')}}" class="btn btn-primary cancel-form">  {{ __('button.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="security_settings" role="tabpanel">
                    <form method="POST" action="{{ route('patient.update',$patient->id) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="redirect" value="{{route('patient.profile',$patient->id)}}">
                        <div class="form-heading">
                            <h4>{{__('patient.security_settings')}}</h4>
                            <div class="row">
                                <!-- PHYSICAL ADDRESS -->
                                <div class=" col-12 col-md-6 col-xl-12">
                                    <div class="input-block local-forms">
                                        <x-input-label for="current_password" :value="__('patient.current_password')" />
                                        <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password"/>
                                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- PHYSICAL ADDRESS -->
                                <div class=" col-12 col-md-6 col-xl-12">
                                    <div class="input-block local-forms">
                                        <x-input-label for="new_password" :value="__('patient.new_password')" />
                                        <x-text-input id="new_password" class="block mt-1 w-full" type="password" name="new_password"/>
                                        <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- PHYSICAL ADDRESS -->
                                <div class=" col-12 col-md-6 col-xl-12">
                                    <div class="input-block local-forms">
                                        <x-input-label for="confirm_password" :value="__('patient.confirm_password')" />
                                        <x-text-input id="confirm_password" class="block mt-1 w-full" type="password" name="confirm_password"/>
                                        <x-input-error :messages="$errors->get('confirm_password')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                                <a href="{{route('patient.index')}}" class="btn btn-primary cancel-form">  {{ __('button.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
