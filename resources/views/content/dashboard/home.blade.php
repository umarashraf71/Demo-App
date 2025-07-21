@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/extensions/ext-component-toastr.css') }}">
@endsection
@section('page-style')
@endsection
@php
    $configData = Helper::applClasses();
@endphp

@section('content')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <div class="col-12 text-center" style="margin-top: 15%;">
            <h1>
                Welcome to
            </h1>
            <h1>
              FFL - MCAS
            </h1>
            <h3>
                {{Auth::user()->name}} as {{Auth::user()->roles->pluck('name')->first()}}
            </h3>
            <h5>
                WE are happy to have you
            </h5>
        </div>
    </section>
    <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('page-script')
<script type="text/javascript">
    @if(Session::has('message'))
        toastr['success']('{{Session::get('message')}}', 'Success!', {
            closeButton: true,
            tapToDismiss: false,
            rtl: false
          });
    @endif
</script>
@endsection
