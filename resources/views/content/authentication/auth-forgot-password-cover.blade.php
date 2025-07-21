@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Forgot Password')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/pages/authentication.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/extensions/ext-component-toastr.css') }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0 mt-2">
            <!-- Brand logo-->
            <a class="brand-logo" href="#">
                @if ($configData['theme'] === 'dark')
                    <img class="img-fluid" src="{{ asset('images/logo/white-ffl.png') }}" alt="Login V2" width="90" />
                @else
                    <img class="img-fluid" src="{{ asset('images/logo/ffl.png') }}" alt="Login V2" width="90" />
                @endif
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    @if ($configData['theme'] === 'dark')
                        <img class="img-fluid" src="{{ asset('images/pages/forgot-password-v2-dark.svg') }}"
                            alt="Forgot password V2" />
                    @else
                        <img class="img-fluid" src="{{ asset('images/pages/forgot-password-v2.svg') }}"
                            alt="Forgot password V2" />
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Forgot password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    @if ($message = Session::get('status'))
                        <div class="demo-spacing-0 my-2">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="alert-body">{{ $message }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    <h2 class="card-title fw-bold mb-1">Forgot Password? 🔒</h2>
                    <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>
                    <form class="auth-forgot-password-form mt-2" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="forgot-password-email">Email</label>
                            <input class="form-control  @error('email') is-invalid @enderror" name="email"
                                id="forgot-password-email" type="email" name="forgot-password-email"
                                placeholder="john@example.com or john" aria-describedby="forgot-password-email"
                                tabindex="1" autocomplete="email" autofocus />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    @if ($message == 'passwords.throttled')
                                        <strong>Reset password link is already sent to email</strong>
                                    @else
                                        <strong>{{ $message }}</strong>
                                    @endif

                                </span>
                            @enderror
                        </div>
                        <button class="btn btn-primary w-100" tabindex="2">Send reset link</button>
                    </form>
                    <p class="text-center mt-2">
                        <a href="{{ route('login') }}">
                            <i data-feather="chevron-left"></i> Back to login
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Forgot password-->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/toastr.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/scripts/pages/auth-forgot-password.js') }}"></script>
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
