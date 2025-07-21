@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Reset Password')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
  <link rel="stylesheet" href="{{ asset('css/base/pages/authentication.css') }}">
@endsection

@section('content')
<div class="auth-wrapper auth-cover">
  <div class="auth-inner row m-0 mt-2">
    <!-- Brand logo-->
    <a class="brand-logo" href="#">
      @if($configData['theme'] === 'dark')
      <img class="img-fluid" src="{{asset('images/logo/white-ffl.png')}}" alt="Login V2" width="90"/>
      @else
      <img class="img-fluid" src="{{asset('images/logo/ffl.png')}}" alt="Login V2" width="90"/>
      @endif
    </a>
    <!-- /Brand logo-->

    <!-- Left Text-->
    <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
      <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
        @if($configData['theme'] === 'dark')
         <img src="{{asset('images/pages/reset-password-v2-dark.svg')}}" class="img-fluid" alt="Register V2" />
        @else
         <img src="{{asset('images/pages/reset-password-v2.svg')}}" class="img-fluid" alt="Register V2" />
        @endif
      </div>
    </div>
    <!-- /Left Text-->

    <!-- Reset password-->
    <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
      <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
        @if ($message = Session::get('success'))
        <div class="demo-spacing-0 my-2">
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="alert-body">{{ $message }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="demo-spacing-0 my-2">
          <div class="alert alert-error alert-dismissible fade show" role="alert">
            <div class="alert-body">{{ $message }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
        @endif
        <h2 class="card-title fw-bold mb-1">Reset Password 🔒</h2>
        <form class="auth-reset-password-form mt-2" action="{{ route('password.change') }}" method="POST">
          @csrf
          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="email">Old Password</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input class="form-control @error('old_password') is-invalid @enderror" id="email" type="password" name="old_password" required autofocus/>
              @error('old_password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="reset-password-new">New Password</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input class="form-control form-control-merge  @error('password') is-invalid @enderror" id="reset-password-new" type="password" name="password" placeholder="············" aria-describedby="reset-password-new" autofocus="" tabindex="1" />
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="reset-password-confirm">Confirm Password</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input class="form-control form-control-merge" id="reset-password-confirm" type="password" placeholder="············" name="password_confirmation" required autocomplete="new-password"/>
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
          </div>
          <button class="btn btn-primary w-100" tabindex="3">Set New Password</button>
        </form>
        <p class="text-center mt-2">
          <a href="{{route('dashboard-analytics')}}">
            <i data-feather="chevron-left"></i> Back to Dashboard
          </a>
        </p>
      </div>
    </div>
    <!-- /Reset password-->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/scripts/pages/auth-reset-password.js')}}"></script>
@endsection
