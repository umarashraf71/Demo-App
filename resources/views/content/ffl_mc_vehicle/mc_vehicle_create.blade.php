@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Milk Collection Vehicle')
@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add Milk Collection Vehicle</h4>
        </div>
        <div class="card-body">
            @if ($message = Session::get('success'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $message }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            @if ($errorMessage = Session::get('errorMessage'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $errorMessage }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
          <form class="form" action="{{ route('mc-vehicle.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="company">Vendor</label>*
                        <select name="company" id="company" class="select2 form-select" data-placeholder="Vendor">
                            <option value="" selected disabled>Vendor</option>
                             @foreach($venders as $vender)
                                    <option {{old('company')==$vender->id?'selected':''}} value="{{$vender->id}}">{{$vender->name}}</option>
                             @endforeach
                        </select>
                        @error('company')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>

              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="vehicle_number">Vehicle Number *</label>
                  <input
                    type="text"
                    id="vehicle_number"
                    class="form-control"
                    placeholder="Vehicle Number"
                    name="vehicle_number"
                    value="{{ old('vehicle_number') }}"
                  />
                    @error('vehicle_number')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="make">Make</label>
                        <input
                            type="text"
                            id="make"
                            class="form-control"
                            placeholder="Make"
                            name="make"
                            value="{{ old('make') }}"
                        />
                        @error('make')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="model">Model</label>
                  <input
                    min="1950" max="2040"
                    type="number"
                    id="model"
                    class="form-control"
                    placeholder="Model"
                    name="model"
                    value="{{ old('model') }}"
                  />
                    @error('model')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>

                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="company_name">Tanker's Capacity (in litres)</label>
                  <input
                    min="1" max="100000"
                    type="number"
                    id="tanker_capacity"
                    class="form-control"
                    placeholder="Tanker Capacity"
                    name="tanker_capacity"
                    value="{{ old('tanker_capacity') }}"
                  />
                    @error('tanker_capacity')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>

              <div class="col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="compartments">Compartments</label>*
                    <select name="compartments" id="compartments" class="select2 form-select" required>
                      <option value="1">Single Compartment</option>
                      <option value="2">Two Compartments</option>
                      <option value="3">Thrice Compartments</option>
                    </select>
                    @error('compartments')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>

                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="tag_no">(Assets)Tag #</label>
                  <input
                    min="1" max="10000"
                    type="text"
                    id="tag_no"
                    class="form-control"
                    placeholder="Tag no."
                    name="tag_no"
                    value="{{ old('tag_no') }}"
                  />
                    @error('tag_no')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>

              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="column-4">Status *</label>
                  <select
                  class="select2 form-select" data-placeholder="Status"
                  id="column-4"
                  name="status"
                  >
                      <option value="" selected disabled></option>
                    <option value="1" {{ old("status") == "1"  ? "selected":"" }}>Active</option>
                    <option value="0" {{old("status") == "0"  ? "selected":"" }}>In-Active</option>
                  </select>
                  @error('status')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
{{--              <div class="col-md-6 col-12">--}}
{{--                <div class="mb-1">--}}
{{--                  <label class="form-label" for="column-4">Owner</label>--}}
{{--                  <select--}}
{{--                  class="select2 form-select"--}}
{{--                  id="column-4"--}}
{{--                  name="mc_vehicle_owner"--}}
{{--                  >--}}
{{--                    @foreach ($mc_vehicle as $value)--}}
{{--                    <option--}}
{{--                    value="{{ $value->name }}"--}}
{{--                    {{ (Input::old("mc_vehicle_owner") == $value->name  ? "selected":"") }}--}}
{{--                    >{{ $value->name }}</option>--}}
{{--                    @endforeach--}}
{{--                  </select>--}}
{{--                  @error('mc_vehicle_owner')--}}
{{--                        <span class="text-danger">{{$message}}</span>--}}
{{--                  @enderror--}}
{{--                </div>--}}
{{--              </div>--}}
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                <a class="btn btn-outline-secondary cancel-btn" href="{{ route('mc-vehicle.index') }}" >Cancel</a>
              </div>
            </div>
              <br>
              <br>
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
<script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
<script>
  // $('#vehicle_number').inputmask({mask: 'AAA-9999'});
</script>
@endsection
