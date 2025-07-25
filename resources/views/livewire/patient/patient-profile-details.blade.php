<div class="col-lg-9">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                @if(auth()->user()->can('edit_profile',$patient))
                <li class="nav-item" role="presentation">
                    <a href="#account_settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link active" aria-selected="false" tabindex="-1" role="tab">
                        {{__('patient.account_settings')}}
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('edit_access',$patient))
                <li class="nav-item" role="presentation">
                    <a href="#security_settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link " aria-selected="true" role="tab">
                        {{__('patient.security_settings')}}
                    </a>
                </li>
                @endif
            </ul>
            <div class="tab-content">
                @if(auth()->user()->can('edit_profile',$patient))
                <div class="tab-pane active" id="account_settings" role="tabpanel">
                    <form method="POST" action="{{ route('patient.update',$patient->id) }}" id="form">
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
                                    <x-text-input  name="identifier" class="block mt-1 w-full" type="text" :value="$patient->identifier" />
                                    <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- First Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="first_name" :value="__('patient.first_name')" required="true"/>
                                    <x-text-input id="given_name" class="block mt-1 w-full" type="text" name="given_name" :value="$patient->given_name" required />
                                    <x-input-error :messages="$errors->get('given_name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- Last Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="last_name" :value="__('patient.last_name')" required="true"/>
                                    <x-text-input id="family_name" class="block mt-1 w-full" type="text" name="family_name" :value="$patient->family_name" required/>
                                    <x-input-error :messages="$errors->get('family_name')" class="mt-2" />
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
                                    <x-input-label for="birth_date" :value="__('patient.birthdate')" required="true"/>
                                    <x-text-input id="birth_date" type="text" name="birth_date" :value="$patient->birth_date" class="block mt-1 w-full datetimepicker" />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- PHYSICAL ADDRESS -->
                            <div class=" col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="physical_address" :value="__('patient.physical_address')" required/>
                                    <x-textarea-input id="physical_address" class="block mt-1 w-full" type="text" name="address">{{$patient->address}}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- PHONE -->
                            <div class=" col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="phone" :value="__('patient.phone')" required/>
                                    <x-text-input class="block mt-1 w-full" type="tel" id="phone" name="phone" :value="$patient->phone"/>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                            <div class=" col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="marital_status" :value="__('patient.marital_status')" required/>
                                    <x-select-input name="marital_status" :options="\App\Models\Lista::maritalStatus()" :selected="[$patient->marital_status]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-12 col-md-12 col-xl-12">
                                <!-- BLOOD TYPE -->
                                <div class="input-block local-forms">
                                    <x-input-label for="blood_type" :value="__('patient.blood_type')" />
                                    <x-select-input name="blood_type" :options="\App\Models\Lista::bloodTypes()" :selected="[$patient->blood_type]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        @can('patients.profile')
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                            </div>
                        </div>
                        @endcan
                    </form>
                </div>
                @endif
                @if(auth()->user()->can('edit_access',$patient))
                <div class="tab-pane" id="security_settings" role="tabpanel">
                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')


                        <div class="input-block local-forms">
                            <x-input-label for="update_password_password" :value="__('user.new_password')" required/>
                            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>

                        <div class="input-block local-forms">
                            <x-input-label for="update_password_password_confirmation" :value="__('user.confirm_password')" required/>
                            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
