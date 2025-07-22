<div>
    <div class="card">
        <style>
            .data-header {
                /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
                background: #003b62;
                color: white;
                border-radius: 20px;
                padding: 30px;
                /*margin-bottom: 30px;
                box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);*/
                position: relative;
                overflow: hidden;
            }

            .data-header::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 200px;
                height: 200px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(50%, -50%);
            }

            .data-info {
                display: grid;
                grid-template-columns: auto 1fr auto;
                gap: 30px;
                align-items: center;
                position: relative;
            }

            .data-avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                font-weight: 700;
                border: 4px solid rgba(255, 255, 255, 0.3);
            }

            .data-details h1 {
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .data-meta {
                font-size: 16px;
                opacity: 0.9;
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
            }

            .data-actions {
                display: flex;
                gap: 12px;
            }

            .btn-head {
                padding: 12px 24px;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                font-size: 14px;
            }

            .btn-head-light {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: 2px solid rgba(255, 255, 255, 0.3);
            }
        </style>
        <!-- Header del Paciente -->
        <div class="data-header">
            <div class="data-info">
                <div class="data-avatar">
                    <div class="profile-user-img">
                        @if($data->avatar())
                            <img src="{{url('storage/'.$data->avatar()->path)}}" style="border-radius: 50px">
                        @else
                            {{ strtoupper(substr($data->first_name, 0, 1) . substr($data->last_name, 0, 1)) }}
                        @endif


                        <div class="form-group doctor-up-files profile-edit-icon mb-0">
                            <div class="uplod d-flex">
                                <label class="file-upload profile-upbtn mb-0">
                                    <input type="file" wire:model="avatar">
                                    @error('avatar') <span class="error">{{ $message }}</span> @enderror
                                    <img src="{{ URL::asset('/assets/img/icons/camera-icon.svg') }}" alt="Profile">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="data-details">
                    <h1>{{ $data->full_name }}</h1>
                    <div class="data-meta">
                        <span>📧 {{ $data->email ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="data-actions">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    @can('users.profile')
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
                    @endcan
                </ul>
                <div class="tab-content">
                    @can('users.profile')
                        <div class="tab-pane active" id="account_settings" role="tabpanel">
                            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                @csrf
                            </form>

                            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                @csrf
                                @method('patch')

                                <div class="input-block local-forms">
                                    <x-input-label for="first_name" :value="__('user.first_name')" required/>
                                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('name', $data->first_name)" required autofocus autocomplete="first_name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <div class="input-block local-forms">
                                    <x-input-label for="last_name" :value="__('user.last_name')" required/>
                                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('name', $data->last_name)" required autofocus autocomplete="last_name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <div class="input-block local-forms">
                                    <x-input-label for="email" :value="__('user.email')" required/>
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $data->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                    @if ($data instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $data->hasVerifiedEmail())
                                        <div>
                                            <p class="text-sm mt-2 text-gray-800">
                                                {{ __('Your email address is unverified.') }}

                                                <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    {{ __('Click here to re-send the verification email.') }}
                                                </button>
                                            </p>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 font-medium text-sm text-green-600">
                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="input-block local-forms">
                                    <x-input-label for="client" :value="__('user.client')" required/>
                                    <x-select-input name="client" :options="$clients" :selected="$client_selected" class="block  w-full"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">  {{ __('button.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
