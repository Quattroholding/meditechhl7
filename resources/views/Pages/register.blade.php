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
                                @include('partials.message')
                                <form action="{{ route('patient.public.store') }}" method="POST" id="form">
                                    @csrf
                                    <div class="form-group">
                                        <label>{{__('user.first_name')}} <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" id="first_name"
                                            value="{{ old('name') }}" name="first_name">
                                        <div class="text-danger pt-2">
                                            @error('first_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('user.last_name')}} <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" id="last_name"
                                            value="{{ old('name') }}" name="last_name">
                                        <div class="text-danger pt-2">
                                            @error('last_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="login-danger">*</span></label>
                                        <input class="form-control" type="text" id="email" name="email">
                                        <div class="text-danger pt-2">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('user.phone')}}<span class="login-danger w-full">*</span></label>
                                        <input class="form-control" type="tel" id="phone" name="phone"
                                            required>
                                        <p id="message"></p>
                                        <div class="text-danger pt-2">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="passwordError" style="color: red;"></div>
                                    <div class="form-group">
                                        <label>{{__('user.password')}} <span class="login-danger">*</span></label>
                                        <input class="form-control pass-input" type="password" id="password"
                                            name="password" value="{{ old('password') }}">
                                        <span class="profile-views feather-eye-off toggle-password"></span>
                                        <div class="text-danger pt-2">
                                            @error('password')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('user.new_password')}} <span class="login-danger">*</span></label>
                                        <input class="form-control pass-input-confirm" type="password"
                                            id="confirm-password" name="password_confirmed">
                                        <span class="profile-views feather-eye-off confirm-password"></span>
                                        <div class="text-danger pt-2">
                                            @error('password_confirmed')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="forgotpass">
                                        <div class="remember-me">
                                            <label class="custom_check mr-2 mb-0 d-inline-flex remember-me">{{__('Estoy de acuerdo con los')}}
                                                <a href="javascript:;">&nbsp {{__('Terminos de Servicios')}} </a>&nbsp y <a
                                                    href="javascript:;">&nbsp {{__('Politicas de Privacidad')}} </a>
                                                <input type="checkbox" name="terms_and_privacy">
                                                <span class="checkmark"></span>
                                            </label>
                                            <div class="text-danger pt-2">
                                                @error('terms_and_privacy')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">{{__('Registrarse')}}</button>
                                    </div>
                                </form>
                                <!-- /Form -->

                                <div class="next-sign">
                                    <p class="account-subtitle">{{(__('¿Ya tienes una cuenta?'))}} <a  href="{{ url('login') }}">{{__('Ingresar')}}</a></p>
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
