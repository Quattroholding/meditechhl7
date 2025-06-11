<div class="col-lg-8">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#account_settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link active" aria-selected="false" tabindex="-1" role="tab">
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
                <div class="tab-pane active" id="account_settings" role="tabpanel">
                    <form method="POST" action="{{ route('practitioner.update',$data->id) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{route('practitioner.profile',$data->id)}}">
                        @foreach($data->user->clients as $cli)
                            <input type="hidden" name="clients[]" value="{{$cli->id}}">
                        @endforeach
                        <div class="row">
                            <!-- ID NUMBER -->
                            <div class="col-6 col-md-6 col-xl-6">
                                <div class="input-block  local-forms">
                                    <x-input-label for="id_type" :value="__('doctor.id_type')" required="true"/>
                                    <x-select-input name="id_type" :options="\App\Models\Lista::documentType()" :selected="[$data->identifier_type]" class="block mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-xl-6">
                                <div class=" input-block  local-forms ">
                                    <x-input-label for="id_number" :value="__('doctor.full_id_number')" required="true"/>
                                    <x-text-input id="id_number" class="block mt-1 w-full" type="text" name="id_number" :value="$data->identifier" autofocus/>
                                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- First Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="first_name" :value="__('doctor.first_name')" required="true"/>
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="$data->given_name"  />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- Last Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="last_name" :value="__('doctor.last_name')" required="true"/>
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="$data->family_name" />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- GENDER -->
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms ">
                                    <x-input-label for="gender" :value="__('doctor.gender')" required="true"/>
                                    <x-select-input name="gender" :options="\App\Models\Lista::gender()" :selected="[$data->gender]" class="block w-full"/>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>
                            </div>
                            <!-- BIRTHDATE -->
                            <div class=" col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <div class="form-group local-forms cal-icon">
                                        <x-input-label for="birth_date" :value="__('doctor.birthdate')" required="true"/>
                                        <x-text-input id="birth_date" class="block mt-1 w-full datetimepicker" type="text" name="birth_date" :value="$data->birth_date"/>
                                        <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6 col-md-6 col-xl-6">
                                <!-- EMAIL -->
                                <div class="input-block local-forms">
                                    <x-input-label for="email" value="{{__('doctor.email').'/usuario'}}" required="true"/>
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$data->email" readonly/>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                            <!-- PHONE -->
                            <div class=" col-6 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="phone" :value="__('doctor.phone')" required="true" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="$data->phone"/>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- PHYSICAL ADDRESS -->
                            <div class=" col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="address" :value="__('doctor.physical_address')" required="true" />
                                    <x-textarea-input id="address" class="block mt-1 w-full" type="text" name="address" >{{$data->address}}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                            {{--}}<!-- BILLING ADDRESS-->
                            <div class=" col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="billing_address" :value="__('doctor.billing_address')" />
                                    <x-textarea-input id="billing_address" class="block mt-1 w-full" type="email" name="billing_address" :value="old('billing_address')"/>
                                    <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                                </div>
                            </div>{{--}}
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="security_settings" role="tabpanel">
                    <form method="POST" action="{{ route('patient.update',$data->id) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{route('patient.profile',$data->id)}}">
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
