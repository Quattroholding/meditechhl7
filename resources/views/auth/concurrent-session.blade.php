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
                                </div>
                                @include('partials.message')

                                <!-- Warning Icon -->
                                <div class="text-center mb-4">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                                </div>

                                <h2 class="text-center mb-3">Sesión Activa Detectada</h2>

                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Atención:</strong> Detectamos que ya tienes {{ $sessionsCount }} {{ $sessionsCount == 1 ? 'sesión activa' : 'sesiones activas' }} en {{ $sessionsCount == 1 ? 'otro dispositivo o navegador' : 'otros dispositivos o navegadores' }}.
                                </div>

                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-question-circle text-primary me-2"></i>¿Qué deseas hacer?</h5>
                                        <p class="card-text">Por razones de seguridad, solo puedes tener una sesión activa a la vez.</p>

                                        <ul class="list-unstyled mt-3">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <strong>Cerrar sesión anterior:</strong> Iniciar sesión aquí y cerrar automáticamente {{ $sessionsCount == 1 ? 'la otra sesión' : 'las otras sesiones' }}
                                            </li>
                                            <li>
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                <strong>Cancelar:</strong> No iniciar sesión y mantener {{ $sessionsCount == 1 ? 'tu sesión activa' : 'tus sesiones activas' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <form action="{{ route('login') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ session('pending_login_email') }}">
                                            <input type="hidden" name="password" value="{{ session('pending_login_password') ? decrypt(session('pending_login_password')) : '' }}">
                                            <input type="hidden" name="force_login" value="1">
                                            <button class="btn btn-primary btn-block w-100" type="submit">
                                                <i class="fas fa-sign-in-alt me-2"></i>Cerrar Otra Sesión e Ingresar
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <form action="{{ route('login.cancel') }}" method="POST">
                                            @csrf
                                            <button class="btn btn-secondary btn-block w-100" type="submit">
                                                <i class="fas fa-ban me-2"></i>Cancelar Login
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4" role="alert">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <small>Esta medida de seguridad protege tu cuenta de accesos no autorizados.</small>
                                </div>

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

