@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Department')
@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection
@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit Department</h4>
        </div>
        <div class="card-body">
            @if ($errorMessage = Session::get('errorMessage'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $errorMessage }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
          <form class="form" action="{{ route('dept.update',$department->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="mcas-code-column">Department Code *</label>
                    <input
                      type="text"
                      id="mcas-code-column"
                      class="form-control"
                      name="code"
                      value="{{ $department->code }}"
                      disabled
                    />
                      @error('code')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                      <label class="form-label" for="select2-multiple">Plant *</label>
                      <select
                          class="select2 form-select"
                          id="select2-multiple"
                          name="plant_id" data-placeholder="Select Plant"
                      >
                          <option value="" selected disabled></option>
                      @foreach ($all_plant as $value)
                        <option
                            value="{{ $value->id }}"
                            @if ($value->id === $department->plant_id)
                            {{ "selected" }}
                            @endif
                        >
                        {{ $value->name }}
                        </option>
                        @endforeach
                      </select>
                      @error('plant_id')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="name-column">Name *</label>
                    <input
                      type="text"
                      id="name-column"
                      class="form-control"
                      placeholder="Name"
                      name="name"
                      value="{{ $department->name }}"
                    />
                    @error('name')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="mcas-address">Address *</label>
                    <input
                      type="text"
                      id="mcas-address"
                      class="form-control"
                      placeholder="Address"
                      name="address"
                      value="{{ $department->address }}"
                    />
                    @error('address')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="mcas-latitude">Latitude *</label>
                    <input
                      type="text"
                      id="mcas-latitude"
                      class="form-control"
                      name="latitude"
                      placeholder="Latitude"
                      value="{{ $department->latitude }}"
                    />
                    @error('latitude')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="mcas-longitude">Longitude *</label>
                    <input
                      type="text"
                      id="mcas-longitude"
                      class="form-control"
                      name="longitude"
                      placeholder="Longitude"
                      value="{{ $department->latitude }}"
                    />
                    @error('longitude')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary me-1">Submit</button>
                  <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                  <a class="btn btn-outline-secondary cancel-btn" href="{{ route('dept.index') }}" >Cancel</a>
                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->
@endsection
@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
@endsection
