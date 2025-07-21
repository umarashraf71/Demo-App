@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Maintenance')

@section('page-style')
<link rel="stylesheet" href="{{asset('css/base/pages/page-misc.css')}}">
@endsection

@section('content')
<!-- Under maintenance-->
<div class="misc-wrapper">
  <a class="brand-logo" href="#">
    <h2 class="brand-text text-primary ms-1" style="margin: 30px">FFL MCAS</h2>
  </a>
  <div class="misc-inner p-2 p-sm-3">
    <div class="w-100 text-center">
      <h2 class="mb-1">Under Maintenance 🛠</h2>
      <p class="mb-3">Sorry for the inconvenience but we're performing some maintenance at the moment</p>
      @if($configData['theme'] === 'dark')
      <img class="img-fluid" src="{{asset('images/pages/under-maintenance-dark.svg')}}" alt="Under maintenance page" />
      @else
      <img class="img-fluid" src="{{asset('images/pages/under-maintenance.svg')}}" alt="Under maintenance page" />
      @endif
    </div>
  </div>
@endsection
