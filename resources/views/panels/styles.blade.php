<!-- BEGIN: Vendor CSS-->
@if ($configData['direction'] === 'rtl' && isset($configData['direction']))
  <link rel="stylesheet" href="{{ asset('vendors/css/vendors-rtl.min.css') }}" />
@else
  <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}" />
@endif

@yield('vendor-style')
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" href="{{ asset('css/core.css') }}" />
<link rel="stylesheet" href="{{ asset('css/base/themes/dark-layout.css') }}" />
<link rel="stylesheet" href="{{ asset('css/base/themes/bordered-layout.css') }}" />
<link rel="stylesheet" href="{{ asset('css/base/themes/semi-dark-layout.css') }}" />

{{--Toaster css--}}
<link rel="stylesheet" href="{{ asset('vendors/css/extensions/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/base/plugins/extensions/ext-component-toastr.css')}}">

@php $configData = Helper::applClasses(); @endphp

<!-- BEGIN: Page CSS-->
@if ($configData['mainLayoutType'] === 'horizontal')
  <link rel="stylesheet" href="{{ asset('css/base/core/menu/menu-types/horizontal-menu.css') }}" />
@else
  <link rel="stylesheet" href="{{ asset('css/base/core/menu/menu-types/vertical-menu.css') }}" />
@endif

{{-- Page Styles --}}
@yield('page-style')

<!-- laravel style -->
<link rel="stylesheet" href="{{ asset('css/overrides.css') }}" />

<!-- BEGIN: Custom CSS-->

@if ($configData['direction'] === 'rtl' && isset($configData['direction']))
  <link rel="stylesheet" href="{{ asset('css-rtl/custom-rtl.css') }}" />
  <link rel="stylesheet" href="{{ asset('css-rtl/style-rtl.css') }}" />

@else
  {{-- user custom styles --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
@endif
