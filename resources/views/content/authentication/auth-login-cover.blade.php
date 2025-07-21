@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/pages/authentication.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/extensions/ext-component-toastr.css') }}">
@endsection

@section('content')

    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row" style="margin-top: 10%">
            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-6 align-items-center p-5 ">

                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5 col-lg-10">
                    @if ($configData['theme'] === 'dark')
                        <img class="img-fluid" src="{{ asset('images/logo/white-ffl.png') }}" alt="Login V2" width="400"
                            style="margin-left: 40%;" />
                    @else
                        <img class="img-fluid" src="{{ asset('images/logo/ffl.png') }}" alt="Login V2" width="400"
                            style="margin-left:40%;" />
                    @endif
                </div>
                <!-- Center Line-->
                @if ($configData['theme'] === 'dark')
                    <div class="col-lg-2" style="border-right: 1px solid #FFFFFF; height: 100%; margin-left: -6%;">
                        <p></p>
                    </div>
                @else
                    <div class="col-lg-2" style="border-right: 1px solid #1D1761; height: 100%; margin-left: -6%;">
                        <p></p>
                    </div>
                @endif
                <!-- /Center Line-->
            </div>

            <!-- /Left Text-->

            <!-- Login-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5 offset-lg-2" style="margin-left: 0%;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    @if ($message = Session::get('error'))
                        <div class="demo-spacing-0 my-2">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="alert-body">{{ $message }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    <h2 class="card-title fw-bold mb-1">Welcome to MCAS
                        {{--            👋 --}}
                    </h2>
                    <p class="card-text mb-2">Please sign-in to your account</p>
                    <form class="auth-login-form mt-2" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="login-email">Email or Username</label>
                            <input
                                class="form-control @error('email') is-invalid @enderror @error('user_name') is-invalid @enderror"
                                id="login-email" type="text" name="email" placeholder="Email or Username"
                                aria-describedby="login-email" autofocus="" tabindex="1"
                                value="{{ old('user_name') ?: old('email') }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @error('user_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="login-password">Password</label>
                                <a href="{{ route('password.request') }}">
                                    <small>Forgot Password?</small>
                                </a>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input class="form-control form-control-merge @error('password') is-invalid @enderror"
                                    id="login-password" type="password" name="password" placeholder="············"
                                    aria-describedby="login-password" tabindex="2" />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input" name="remember" id="remember-me" type="checkbox" tabindex="3" />
                                <label class="form-check-label" for="remember-me"> Remember Me</label>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100" tabindex="4">Sign in</button>
                    </form>
                </div>
            </div>
            <!-- /Login-->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/toastr.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/scripts/pages/auth-login.js') }}"></script>
    <script>
        @if (Session::has('message'))
            toastr['success']('{{ Session::get('message') }}', 'Success!', {
                closeButton: true,
                tapToDismiss: false,
                rtl: false
            });
        @endif
    </script>
@endsection
@php Session::forget('message') @endphp
