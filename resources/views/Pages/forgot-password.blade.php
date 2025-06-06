<?php $page = 'forgot-password'; ?>
@extends('layout.mainlayout')
@section('content')
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
                                    <a href="{{ url('/') }}"><img src="{{ URL::asset('/assets/img/login-logo.png') }}"
                                            alt=""></a>
                                </div>
                                <h2>Reset Password</h2>
                                <!-- Form -->
                                <form method="POST" enctype="multipart/form-data" action="{{ route('password.email') }}">
                                    @csrf
                                    @if(session('message.success'))
                                        <div class="alert alert-success">
                                            {{ session('message.success') }}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label>Email <span class="login-danger">*</span></label>
                                        <x-text-input class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus/>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" /><p>&nbsp;</p>
                                    </div>
                                    <div class="form-group login-btn">
                                        <button class="btn btn-primary btn-block" type="submit">Reset Password</button>
                                    </div>
                                </form>
                                <!-- /Form -->

                                <div class="next-sign">
                                    <p class="account-subtitle">Need an account? <a href="{{ url('login') }}">Login</a></p>

                                    <!-- Social Login -->
                                    <div class="social-login">
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-01.svg') }}"
                                                alt=""></a>
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-02.svg') }}"
                                                alt=""></a>
                                        <a href="javascript:;"><img
                                                src="{{ URL::asset('/assets/img/icons/login-icon-03.svg') }}"
                                                alt=""></a>
                                    </div>
                                    <!-- /Social Login -->

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /Login Content -->

        </div>
    </div>
@endsection
