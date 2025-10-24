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
                                    <img src="{{url('images/logoFull.png')}}" alt="" style="margin: 0 auto;" width="60%">
                                    @if(config('app.env') === 'local' || request()->ip() =='200.12.208.98')
                                    <a href="{{route('autologin',['role'=>'admin'])}}" class="btn btn-primary">Admin</a>
                                    <a href="{{route('autologin',['role'=>'admin client'])}}" class="btn btn-primary">Admin Client</a>
                                    <a href="{{route('autologin',['role'=>'doctor'])}}" class="btn btn-primary">Doctor</a>
                                    <a href="{{route('autologin',['role'=>'paciente'])}}" class="btn btn-primary">Paciente</a>
                                    <a href="{{route('autologin',['role'=>'asistente'])}}" class="btn btn-primary">Asistente</a>
                                    @endif
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

                                <div class="next-sign">
                                    <p class="account-subtitle">{{__('¿Necesitas una cuenta?')}} <a href="{{ url('register') }}" class="text-base">{{__('Regístrate')}}</a></p>
                                    <!-- Social Login -->
                                    <div class="social-login">

                                        {{--}}
                                        <a href="javascript:;"><img src="{{ URL::asset('/assets/img/icons/login-icon-01.svg') }}" alt=""></a>
                                        <a href="javascript:;"><img  src="{{ URL::asset('/assets/img/icons/login-icon-02.svg') }}" alt=""></a>
                                        <a href="javascript:;"><img   src="{{ URL::asset('/assets/img/icons/login-icon-03.svg') }}" alt=""></a>
                                        {{--}}


                                    </div>
                                    <!-- /Social Login -->
                                    <br/>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Login Content -->
        </div>
    </div>
</x-guest-layout>
