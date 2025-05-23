<div class="col-lg-8">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#appointments" data-bs-toggle="tab" aria-expanded="false" class="nav-link active" aria-selected="false" tabindex="-1" role="tab">
                        {{__('appointment.titles')}}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#encounters" data-bs-toggle="tab" aria-expanded="true" class="nav-link" aria-selected="true"     role="tab">
                        {{__('encounter.titles')}}
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
                <div class="tab-pane show active" id="appointments" role="tabpanel">
                    <livewire:appointment.data-table practitioner_id="{{$data->id}}" pagination="5"/>
                </div>
                <div class="tab-pane" id="encounters" role="tabpanel">
                    <livewire:consultation.data-table practitioner_id="{{$data->id}}"/>
                </div>
                <div class="tab-pane" id="account_settings" role="tabpanel">
                    <form method="POST" action="{{ route('patient.update',$data->id) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{route('patient.profile',$data->id)}}">
                        <div class="form-heading">
                            <h4>{{__('patient.account_settings')}}</h4>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- First Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="first_name" :value="__('patient.first_name')" required="true"/>
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="given_name" :value="$data->given_name" required />
                                    <x-input-error :messages="$errors->get('given_name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-4">
                                <!-- Last Name -->
                                <div class="input-block local-forms">
                                    <x-input-label for="last_name" :value="__('patient.last_name')" required="true"/>
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="family_name" :value="$data->family_name" required/>
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
                                    <x-input-label for="birthdate" :value="__('patient.birthdate')" required="true"/>
                                    <x-text-input id="birthdate" type="text" name="text" :value="$data->birthdate" class="block mt-1 w-full datetimepicker" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-6">
                                <!-- PHONE -->
                                <div class="input-block local-forms ">
                                    <x-input-label for="phone" :value="__('patient.phone')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="$data->phone"/>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <!-- PHYSICAL ADDRESS -->
                            <div class=" col-12 col-md-6 col-xl-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="physical_address" :value="__('patient.physical_address')" />
                                    <x-textarea-input id="physical_address" class="block mt-1 w-full" type="email" name="physical_address">{{$data->address}}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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
