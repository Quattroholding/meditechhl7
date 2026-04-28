<x-guest-layout>
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

            <!-- 2FA Challenge Content -->
            <div class="col-lg-6 login-wrap-bg">
                <div class="login-wrapper">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <div class="account-logo">
                                    <img src="{{url('images/logoSAMI.jpg')}}" alt="" style="margin: 0 auto;" width="60%">
                                </div>

                                <h2><i class="fas fa-shield-alt me-2"></i>Verificación 2FA</h2>


                                @include('partials.message')

                                <form action="{{ route('two-factor.verify') }}" method="POST">
                                    @csrf

                                    <div class="input-block local-forms">
                                        <x-input-label for="code" value="Ingresa el código de tu aplicación de autenticación" required="true" />
                                        <x-text-input
                                            id="code"
                                            class="block mt-1 w-full"
                                            type="text"
                                            name="code"
                                            placeholder="000000"
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            autofocus
                                            autocomplete="one-time-code"
                                            required
                                        />
                                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                        <small class="form-text text-muted mt-2">
                                            Ingresa el código de 6 dígitos de tu aplicación
                                        </small>
                                    </div>

                                    <div class="input-block">
                                        <button class="btn btn-lg btn-block btn-primary w-100" type="submit">
                                            <i class="fas fa-check-circle me-2"></i> Verificar
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            ¿No puedes acceder a tu aplicación?<br>
                                            Puedes usar uno de tus códigos de recuperación
                                        </small>
                                    </div>

                                    <div class="text-center forgotpass mt-4">
                                        <a href="{{ route('login') }}">
                                            <i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /2FA Challenge Content -->
        </div>
    </div>
</x-guest-layout>
