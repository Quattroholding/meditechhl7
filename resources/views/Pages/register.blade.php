<x-guest-layout>
    <div class="container-fluid px-0">
        <div class="row">
            <!-- Login logo -->
            <div class="col-lg-6 login-wrap">
                <div class="login-sec">
                    <div class="log-img">
                        <img class="img-fluid" src="{{ URL::asset('/assets/img/login-02.png') }}" alt="Logo">
                    </div>
                </div>
            </div>
            <!-- /Login logo -->

            <!-- Login Content -->
            <div class="col-lg-6 login-wrap-bg">
                <div class="login-wrapper">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <div class="account-logo">

                                    <a href="{{ url('/') }}">
                                        {{--}}
                                        <img  src="{{ URL::asset('/assets/img/login-logo.png') }}" alt="">
                                        {{--}}
                                        <div class="logo">Meditech</div>
                                    </a>
                                </div>
                                <!-- Form -->
                                <form action="{{ route('patient.public.store') }}" method="POST" id="form">
                                    @csrf
                                    <div class="input-block local-forms">
                                        <x-input-label for="first_name" :value="__('user.first_name')" required/>
                                        <x-text-input  id="first_name" name="first_name"  class="block w-full" type="text"  value="{{ old('first_name') }}"/>
                                        <x-input-error :messages="$errors->get('first_name')"/>
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="last_name" :value="__('user.last_name')" required/>
                                        <x-text-input  id="last_name" name="last_name"  class="block w-full" type="text"  value="{{ old('last_name') }}"/>
                                        <x-input-error :messages="$errors->get('last_name')"/>
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="email" :value="__('user.email')" required/>
                                        <x-text-input  id="email" name="first_name"  class="block w-full" type="email"  value="{{ old('email') }}"/>
                                        <x-input-error :messages="$errors->get('email')"/>
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="phone" :value="__('user.phone')" required/>
                                        <x-text-input  id="phone" name="first_name"  class="block w-full" type="tel" value="{{ old('phone') }}"/>
                                        <x-input-error :messages="$errors->get('phone')"/>
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="password" :value="__('user.password')" required/>
                                        <x-text-input  id="password" name="first_name"  class="block w-full pass-input" type="password"/>
                                        <span class="profile-views feather-eye-off toggle-password"></span>
                                        <x-input-error :messages="$errors->get('password')"/>
                                    </div>
                                    <div class="input-block local-forms">
                                        <x-input-label for="password_confirmed" :value="__('user.new_password')" required/>
                                        <x-text-input  id="password_confirmed" name="password_confirmed"  class="block w-full pass-input-confirm" type="password"/>
                                        <x-input-error :messages="$errors->get('password_confirmed')"/>
                                    </div>
                                    <div class="forgotpass">
                                        <div class="remember-me">
                                            <x-input-label class="custom_check mr-2 mb-0 d-inline-flex remember-me">{{__('Estoy de acuerdo con los')}}
                                                <a href="javascript:;">&nbsp {{__('Terminos de Servicios')}} </a>&nbsp y <a
                                                    href="javascript:;">&nbsp {{__('Politicas de Privacidad')}} </a>
                                                <input type="checkbox" name="terms_and_privacy">
                                                <span class="checkmark"></span>
                                            </x-input-label>
                                            <x-input-error :messages="$errors->get('terms_and_privacy')"/>
                                        </div>
                                    </div>
                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">{{__('Registrarse')}}</button>
                                    </div>
                                </form>
                                <!-- /Form -->

                                <div class="next-sign">
                                    <p class="account-subtitle">{{(__('¿ Ya tienes una cuenta ?'))}} <a  href="{{ url('login') }}">{{__('Ingresar')}}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Login Content -->
        </div>
    </div>
    @vite(['resources/js/app.js'])
    <script>
        var password = document.getElementById('password');
        var passwordConfirmation = document.getElementById('confirm-password');
        var passwordError = document.getElementById('passwordError');

        function validatePasswords() {
            if (password.value !== passwordConfirmation.value) {
                passwordError.style.color = "red";
                passwordError.textContent = 'Password does not match.';
            } else {
                passwordError.style.color = "green";
                passwordError.textContent = 'Pasword match';
            }
        }

        password.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);

        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            if (password.value !== passwordConfirmation.value) {
                passwordError.textContent = 'Las contraseñas no coinciden.';
                event.preventDefault(); // Evita que el formulario se envíe
            } else {
                passwordError.textContent = '';
            }
        });
    </script>
</x-guest-layout>
