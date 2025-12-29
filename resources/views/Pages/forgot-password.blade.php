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
                                   <img src="{{url('images/logoSAMI.jpg')}}" alt="" style="margin: 0 auto;" width="60%">
                                </div>
                                <!-- Form -->
                                @include('partials.message')
                                <form method="POST" enctype="multipart/form-data" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="input-block local-forms">
                                        <label>Email <span class="login-danger">*</span></label>
                                        <x-text-input class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus/>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">{{__('Resetear Contraseña')}}</button>
                                    </div>
                                </form>
                                <!-- /Form -->
                                <div class="next-sign">
                                    <p class="account-subtitle">{{__('¿ Ya tiene una cuenta ?')}} <a href="{{ url('login') }}">{{__('Ingresar')}}</a></p>
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
