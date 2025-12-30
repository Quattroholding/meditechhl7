<x-guest-layout>
    @if(config('app.env') === 'production')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <div class="container-fluid px-0">
        <div class="row">
            <!-- Login logo -->
            <div class="col-lg-6 login-wrap"
            style= "background-image: url('{{ URL::asset('/assets/img/img-front.png') }}');
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center; ">

            </div>
            <!-- /Login logo -->
            <!-- Login Content -->
            <div class="col-lg-6 login-wrap-bg">
                <div class="login-wrapper">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <div class="account-logo">
                                    <img src="{{url('images/logoSAMI.jpg')}}" alt="" style="margin: 0 auto;" width="60%">
                                    @php
                                        $trustedIps = array_merge(
                                            config('debug.trusted_ips', []),
                                            config('debug.additional_ips', [])
                                        );
                                    @endphp
                                    {{--}}
                                    @if(config('debug.enabled') && in_array(request()->ip(), $trustedIps))
                                        <div class="mt-3 p-3 border border-warning rounded" style="background-color: rgba(255, 193, 7, 0.1);">
                                            <p class="mb-2" style="font-size: 0.875rem;"><strong>Debug Mode</strong></p>
                                            <div class="d-flex flex-wrap mb-2" style="gap: 0.5rem;">
                                                <a href="{{route('autologin',['role'=>'admin'])}}" class="btn btn-sm btn-primary">Admin (Random)</a>
                                                <a href="{{route('autologin',['role'=>'admin client'])}}" class="btn btn-sm btn-primary">Admin Client (Random)</a>
                                                <a href="{{route('autologin',['role'=>'doctor'])}}" class="btn btn-sm btn-primary">Doctor (Random)</a>
                                                <a href="{{route('autologin',['role'=>'paciente'])}}" class="btn btn-sm btn-primary">Paciente (Random)</a>
                                                <a href="{{route('autologin',['role'=>'recepcionista'])}}" class="btn btn-sm btn-primary">Recepcionista (Random)</a>
                                            </div>
                                            <hr class="my-2">
                                            <a href="{{ route('debug.login') }}" class="btn btn-sm btn-success" style="width: 100%;">
                                                <i class="fas fa-user-cog"></i> Seleccionar Usuario Específico
                                            </a>
                                        </div>
                                    @endif
                                    {{--}}
                                </div>
                                @include('partials.message')

                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="input-block local-forms">
                                        <x-input-label for="email" :value="__('Email')" required="true" />
                                        @if(request()->has('username'))
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="request()->get('username')"/>
                                        @else
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"/>
                                        @endif

                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="password" :value="__('Contraseña')" required="true" />
                                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        <span class="profile-views feather-eye-off toggle-password"></span>
                                    </div>
                                    <div class="forgotpass">
                                        <div class="remember-me">
                                            <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> {{__('Recuérdame')}}
                                                <input type="checkbox" name="radio">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <a href="{{ url('forgot-password') }}" class="text-base">{{__('¿Se te olvidó la contraseña?')}}</a>
                                    </div>

                                    @if(config('app.env') === 'production')
                                    <div class="form-group">
                                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.public_key') }}"></div>
                                        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                                    </div>
                                    @endif

                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">{{__('Ingresar')}}</button>
                                    </div>
                                </form>
                                <!-- /Form -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Login Content -->
        </div>
    </div>
</x-guest-layout>
