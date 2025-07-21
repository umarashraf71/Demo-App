@extends('layouts/contentLayoutMaster')

@section('title', 'Collection Points')
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css"
        integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/style.css') }}">
@endsection
@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Collection Point</h4>
                    </div>
                    <div class="card-body">
                        <div class="demo-spacing-0 my-2 alert_div d-none">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="alert-body msg"></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        <form class="form" id="createForm" method="POST">
                            <input type="hidden" name="generator_ids" value="" id="generator_ids">
                            <input type="hidden" name="chiller_ids" value="" id="chiller_ids">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="row">
                                        <div class="col-3">
                                            <label class="form-label" for="code">Code</label>
                                            <input type="text" id="code" class="form-control" placeholder="MCC Code"
                                                name="code" value="{{ old('code', $code) }}" disabled maxlength="7" />
                                        </div>
                                        <div class="col-8 offset-1">
                                            <label class="form-label" for="select2-multiple">Name</label>
                                            <input placeholder="Name" class="form-control" name="name"
                                                value="{{ old('name') }}" />
                                        </div>
                                    </div>
                                </div>



                                {{--                <div class="col-md-6 col-12"> --}}
                                {{--                    <div class="mb-1"> --}}
                                {{--                        <label class="form-label" for="select2-multiple">Supplier *</label> --}}
                                {{--                        <select --}}
                                {{--                            class="select2 supplier form-select" {{ old('is_mcc')==0 ?'':'disabled'}} --}}
                                {{--                            name="supplier"> --}}
                                {{--                            <option value="" selected disabled>Supplier</option> --}}
                                {{--                            @foreach ($suppliers as $supplier) --}}
                                {{--                                <option {{old('supplier')==$supplier->_id?'selected':''}} value="{{$supplier->_id}}">{{$supplier->name}}</option> --}}
                                {{--                             @endforeach --}}
                                {{--                        </select> --}}
                                {{--                  </div> --}}
                                {{--              </div> --}}
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-multiple">Categories *</label>
                                        <select class="select2 form-select" id="select2-multiple" name="category_id">
                                            <option value="" selected disabled>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->_id }}"
                                                    {{ Input::old('category_id') == $category->_id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="ffl_cp_dtls">
                                        <div class="col-lg-5 col-md-5 col-sm-12 mb-1">
                                            <label class="form-label" for="select2-multiple">FFL's Collection Point?</label>
                                            <select class="select2 form-select" name="is_mcc" onchange="isMcc(this.value)">
                                                <option value="" selected disabled>Type</option>
                                                <option {{ old('is_mcc') == 1 ? 'selected' : '' }} value="1">Yes
                                                </option>
                                                <option {{ old('is_mcc') == 0 ? 'selected' : '' }} value="0">No
                                                </option>
                                            </select>
                                        </div>
                                        <div
                                            class="col-lg-6 col-md-6 col-sm-12 mb-1 offset-lg-1 offset-md-1 is_chiller_ffl_owned_div">
                                            <label class="form-label" for="select2-multiple">Chiller FFL Owned?</label>
                                            <select id="is_chiller_ffl_owned" class="select2 form-select"
                                                onchange="haveChiller(this.value)" name="is_chiller_ffl_owned">
                                                <option value="" selected disabled>Choose option</option>
                                                <option {{ old('is_chiller_ffl_owned') == 1 ? 'selected' : '' }}
                                                    value="1">Yes</option>
                                                <option {{ old('is_chiller_ffl_owned') == 0 ? 'selected' : '' }}
                                                    value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-multiple">Area Office *</label>
                                        <select class="select2 form-select" id="select2-multiple" name="area_office_id">
                                            <option value="" selected disabled>Select Area Office</option>
                                            @foreach ($areas as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ Input::old('area_office_id') == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <ul class="nav nav-tabs scroller" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" id="location-dtl-tab"
                                                href="#location-tab" role="tab" aria-controls="location-tab"
                                                aria-selected="true">Location Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="bank-dtl-tab" data-toggle="tab"
                                                href="#bank-tab" role="tab" aria-controls="bank-tab"
                                                aria-selected="true">Bank Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="chiller-dtl-tab" data-toggle="tab"
                                                href="#chiller-tab" role="tab" aria-controls="chiller-tab"
                                                aria-selected="true">Chiller Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="em-dtls-tab" data-toggle="tab"
                                                href="#em-tab" role="tab" aria-controls="em-kin-tab"
                                                aria-selected="false">Electric Meter Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="agreement-info-tab" data-toggle="tab"
                                                href="#agreement-info" role="tab" aria-controls="agreement-info"
                                                aria-selected="false">Agreement</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="owner-dtls-tab" data-toggle="tab"
                                                href="#owner-tab" role="tab" aria-controls="owner-tab"
                                                aria-selected="false">Owner Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" id="gen-tab" data-toggle="tab"
                                                href="#genset-tab" role="tab" aria-controls="genset-tab"
                                                aria-selected="false">Generators</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content tab-data" style="padding: 5px;" id="myTabContent">
                                        <div class="tab-pane fade show active" id="location-tab" role="tabpanel"
                                            aria-labelledby="bank-dtl-tab">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="district">District</label>
                                                        <select class="form-control select2" name="district_id" required
                                                            id="districtSelect">
                                                            <option value="">District</option>
                                                            @foreach ($districts as $district)
                                                                <option value="{{ $district->id }}">
                                                                    {{ $district->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('district_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="tehsil">Tehsil</label>
                                                        <select class="form-control select2" name="tehsil_id" required
                                                            id="tehsilSelect">
                                                            <option value="">Tehsil</option>
                                                        </select>
                                                        @error('tehsil_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-latitude">Lattitude</label>
                                                        <input type="text" id="latitude" class="form-control"
                                                            name="latitude" placeholder="Latitude"
                                                            value="{{ old('latitude') }}" />
                                                        @error('latitude')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Longitude</label>
                                                        <input type="text" id="longitude" class="form-control"
                                                            name="longitude" placeholder="Longitude"
                                                            value="{{ old('longitude') }}" />
                                                        @error('longitude')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div>
                                                        <label class="form-label" for="plant-longitude">Location *</label>
                                                        <input autocomplete="off" id="address" name="address"
                                                            class="form-control" type="text"
                                                            value="{{ old('address') }}" placeholder="Location">
                                                        <div class="invalid-feedback address"></div>
                                                        @error('address')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <button id="current-location-button">Current Location
                                                    </button>
                                                </div>
                                                <div class="col-12">
                                                    <div id="map" class="home-map">
                                                        <div id="map-canvas" class="map-canvas-event"
                                                            style="height: 260px; margin-bottom: 1%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show " id="bank-tab" role="tabpanel"
                                            aria-labelledby="bank-dtl-tab">
                                            <div class="row bank_details"
                                                style="display: {{ old('is_mcc') == 1 ? '' : 'none' }}">
                                                @include('content._partials._sections.add_bank')
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="chiller-tab" role="tabpanel"
                                            aria-labelledby="chiller-dtl-tab">
                                            <div class="chiller">
                                                <div class="row mb-1">
                                                    <!-- Repeater Heading -->
                                                    <div class="col-1 offset-11">
                                                        <a class="btn btn-primary " data-bs-toggle="modal"
                                                            data-bs-target="#addChillerModal">
                                                            <span class="fa fa-plus"></span>
                                                        </a>
                                                    </div>
                                                    <!-- Repeater Items -->
                                                    <div>
                                                        <!-- Repeater Content -->
                                                        <div class="row">
                                                            <table class="table mt-1 mb-1" id="chiller_details_table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No.</th>
                                                                        <th>Name</th>
                                                                        <th>Capacity</th>
                                                                        <th>Installation Date</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="em-tab" role="tabpanel"
                                            aria-labelledby="em-tab">
                                            <div class="row mb-1">
                                                <div class="col-lg-6 col-sm-12 ">
                                                    <label for="">Meter Owner Name</label>
                                                    <input placeholder="Meter Owner Name"
                                                        value="{{ old('meter_owner_name') }}" min='1'
                                                        max="25" type="text" class=" form-control"
                                                        name="meter_owner_name">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-1">
                                                    <label for="">Number Of Phases</label>
                                                    <input placeholder="Phase" value="{{ old('phase') }}"
                                                        min='0' type="text" class=" form-control"
                                                        name="phase">
                                                </div>
                                                <div class="col-lg-4 mb-1 ">
                                                    <label for="">Meter Number</label>
                                                    <input value="{{ old('meter_no') }}" placeholder="Meter#"
                                                        type="text" class=" form-control" name="meter_no">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="agreement-info" role="tabpanel"
                                            aria-labelledby="agreement-info-tab">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-6">Ref #</label>
                                                        <input type="text" id="column-own-6" class="form-control"
                                                            placeholder="Enter Ref #" name="ref_no"
                                                            value="{{ old('ref_no') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="mb-1">
                                                        <label class="form-label">Monthly Rent</label>
                                                        <input value="{{ old('rent') }}" placeholder="Monthly Rent"
                                                            type="number" class=" form-control" min="0"
                                                            name="rent">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-row" style="display:flex; margin-top:20px;">
                                                        <div class="col">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="paymentOption" id="prepaid" value="prepaid">
                                                                <label class="form-check-label" for="prepaid">
                                                                    Prepaid
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="paymentOption" id="postpaid" value="postpaid">
                                                                <label class="form-check-label" for="postpaid">
                                                                    Postpaid
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-12">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                                            <label class="form-label" for="column-own-6">Agreement
                                                                From</label>
                                                            <input type="text" id="column-ao-7"
                                                                class="form-control flatpickr-basic"
                                                                placeholder="From: (YYYY-MM-DD)"
                                                                name="agreement_period_from"
                                                                value="{{ old('agreement_period_from') }}" />
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                                            <label class="form-label" for="column-own-6">Agreement
                                                                To</label>
                                                            <input type="text" id="column-ao-7"
                                                                class="form-control flatpickr-basic"
                                                                placeholder="To: (YYYY-MM-DD)" name="agreement_period_to"
                                                                value="{{ old('agreement_period_to') }}" />
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                                            <label class="form-label" for="column-own-6">Effective
                                                                From</label>
                                                            <input type="text" id="column-ao-7"
                                                                class="form-control flatpickr-basic"
                                                                placeholder="W.E.F: (YYYY-MM-DD)"
                                                                name="agreement_period_wef"
                                                                value="{{ old('agreement_period_wef') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="owner-tab" role="tabpanel"
                                            aria-labelledby="other-dtls-tab">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label"> Name</label>
                                                            <input value="{{ old('shop_owner_name') }}"
                                                                placeholder="Shop Owner Name" type="text"
                                                                class=" form-control" name="shop_owner_name">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label">Father Name</label>
                                                            <input value="{{ old('owner_father_name') }}"
                                                                placeholder="Father Name" type="text"
                                                                class=" form-control" name="owner_father_name">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column-own-13"> CNIC</label>
                                                            <input type="text" id="column-own-13"
                                                                class="form-control cnic" placeholder="XXXXX-XXXXXX-X"
                                                                name="owner_cnic" value="{{ old('owner_cnic') }}" />
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column-own-3"> NTN#</label>
                                                            <input type="text" id="column-own-3"
                                                                class="form-control ntn" placeholder="Ntn Number"
                                                                name="owner_ntn" value="{{ old('owner_ntn') }}" />
                                                            @error('owner_ntn')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column3">Contact
                                                                Number</label>
                                                            <input type="text" id="column3"
                                                                class="form-control phone" placeholder="Contact Number"
                                                                name="owner_contact"
                                                                value="{{ old('owner_contact') }}" />
                                                            @error('owner_contact')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column4">Whatsapp
                                                                Number</label>
                                                            <input type="text" id="column4"
                                                                class="form-control phone" placeholder="Whatsapp Number"
                                                                name="owner_whatsapp"
                                                                value="{{ old('owner_whatsapp') }}" />
                                                            @error('owner_whatsapp')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column5">With Effective
                                                                Date</label>
                                                            <input type="text" id="column-ao-7"
                                                                class="form-control flatpickr-basic"
                                                                placeholder="W.E.D: (YYYY-MM-DD)"
                                                                name="with_effective_date"
                                                                value="{{ old('with_effective_date') }}" />
                                                            @error('with_effective_date')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="genset-tab" role="tabpanel"
                                            aria-labelledby="gen-dtl-tab">
                                            <div id="generator_div" class="row mb-2">
                                                <!-- Repeater Heading -->
                                                <div class="col-1 offset-11">
                                                    <a class="btn btn-primary " data-bs-toggle="modal"
                                                        data-bs-target="#addGeneratorModal">
                                                        <span class="fa fa-plus"></span>
                                                    </a>
                                                </div>
                                                <div class="row">
                                                    <table class="table mt-1 mb-1" id="gen_details_table">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Name</th>
                                                                <th>Installation Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" id="save_button" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                <a class="btn btn-outline-secondary cancel-btn"
                                    href="{{ route('collection-point.index') }}">Cancel</a>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="addGeneratorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-sm-5 pb-5">
                        <div class="text-center mb-2">
                            <h1 class="mb-1">Add Generator</h1>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <label class="form-label" for="select2-multiple">Generators</label>
                                <select data-placeholder="Select generator" id="generator_id"
                                    class="form-control select2 mb-2">
                                    <option value="" disabled selected>Select generator</option>
                                    @foreach ($generators as $generator)
                                        <option value="{{ $generator->id }}">{{ $generator->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 ">
                                <label class="form-label" for="select2-multiple">Installation Date</label>
                                <input type="text" id="generator_installation_date"
                                    class="form-control flatpickr-basic" placeholder="Installation Date" />

                            </div>

                            <div class="col-md-12 text-center">
                                <a class="btn btn-primary mt-2 me-1" onclick="addGenerator()">Add</a>
                                <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addChillerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-sm-5 pb-5">
                        <div class="text-center mb-2">
                            <h1 class="mb-1">Add Chiller</h1>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <label class="form-label" for="select2-multiple">Chillers</label>
                                <select data-placeholder="Select chiller" id="chiller_id"
                                    class="form-control select2 mb-2">
                                    <option value="" disabled selected>Select chiller</option>
                                    @foreach ($chillers as $chiller)
                                        <option value="{{ $chiller->id }}">{{ $chiller->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 ">
                                <label class="form-label" for="select2-multiple">Installation Date</label>
                                <input type="text" id="chiller_installation_date" class="form-control flatpickr-basic"
                                    placeholder="Installation Date" />
                            </div>

                            <div class="col-md-12 text-center">
                                <a class="btn btn-primary mt-2 me-1" onclick="addChiller()">Add</a>
                                <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

    <script src="{{ asset('vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>

@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"
        integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfh-Jh-Vn1Lf2TeP9g9cf5bzRbX1gnFZ4&libraries=places&callback=initAutocomplete&libraries=places"
        async defer></script>
    <script>
        var lat = '';
        var long = '';
        var geocoder;
        var map;

        function initAutocomplete() {
            // let lat = parseFloat($("#latitude").val());
            // let long = parseFloat($("#longitude").val());
            map = new google.maps.Map(document.getElementById('map-canvas'), {
                center: {
                    lat: 12.971599,
                    lng: 77.594563
                },
                zoom: 15
            });

            infoWindow = new google.maps.InfoWindow;
            marker = new google.maps.Marker({
                position: {
                    lat: 30.3753,
                    lng: 69.3451,
                },
                map: map,
                draggable: true,
            });

            // Try HTML5 geolocation.
            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    // console.log(pos)
                    $("#latitude").val(position.coords.latitude);
                    $("#longitude").val(position.coords.longitude);
                    marker.setPosition(pos);
                    map.setCenter(pos);
                    geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': pos
                    }, function(results, status) {
                        if (status ==
                            google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $('#address').val(results[1].formatted_address);
                            }
                        }
                    });
                });

            }

            let zoom = 5;
            const input = document.getElementById("address");
            const options = {
                fields: ["formatted_address", "geometry", "name"],
                strictBounds: false,
                types: ["establishment"],
                componentRestrictions: {
                    country: "pk"
                },
            };

            const autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.bindTo("bounds", map);

            google.maps.event.addListener(marker, 'dragend', function(evt) {
                $("#latitude").val(evt.latLng.lat().toFixed(8));
                $("#longitude").val(evt.latLng.lng().toFixed(8));

                var pos = {
                    lat: parseFloat(evt.latLng.lat().toFixed(8)),
                    lng: parseFloat(evt.latLng.lng().toFixed(8))
                };

                geocoder.geocode({
                    'latLng': pos
                }, function(results, status) {
                    if (status ==
                        google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $('#address').val(results[1].formatted_address);
                        }
                    }
                });
            })
            autocomplete.addListener("place_changed", () => {
                marker.setVisible(false);
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    alert("No details available for input: '" + place.name + "'");
                    return;
                }
                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    $("#latitude").val(place.geometry.location.lat().toFixed(8));
                    $("#longitude").val(place.geometry.location.lng().toFixed(8));
                    // console.log(place.geometry.location.lng()+''+place.geometry.location.lat());
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                place.formatted_address;
            });

            var marker; // Define marker variable globally

            $("#current-location-button").click(function(e) {
                e.preventDefault();
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        $("#latitude").val(position.coords.latitude);
                        $("#longitude").val(position.coords.longitude);
                        updateMapMarker(pos); // Update the map marker position
                        updateAddress(pos); // Update the address based on the new position
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            });

            $("#latitude, #longitude").on('input', function() {
                var lat = parseFloat($("#latitude").val());
                var lng = parseFloat($("#longitude").val());
                var pos = {
                    lat: lat,
                    lng: lng
                };
                updateMapMarker(pos); // Update the map marker position
                updateAddress(pos); // Update the address based on the new position
            });

            function updateMapMarker(pos) {
                if (marker) {
                    marker.setPosition(pos);
                } else {
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map // Replace 'map' with your existing Google Map instance variable
                    });
                }
                map.setCenter(pos);
            }

            function updateAddress(pos) {
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({
                    'location': pos
                }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $('#address').val(results[0].formatted_address);
                        } else {
                            $('#address').val('');
                        }
                    } else {
                        $('#address').val('');
                    }
                });
            }
        }



        // script for shifting tabs
        $(document).ready(function() {
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        $('.cnic').inputmask({
            mask: '99999-9999999-9'
        });
        $('.phone').inputmask({
            mask: '+\\92-399-9999999'
        });
        $('.ntn').inputmask({
            mask: '9999999-9'
        });
        // $("#repeater").createRepeater({
        //     showFirstItemToDefault: true,
        // });
        // $("#generator_repeator").createRepeater({
        //     showFirstItemToDefault: true,
        // });

        // script for shifting tabs
        $(document).ready(function() {
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            $('#districtSelect').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '{{ route('get.tehsils', ':id') }}'.replace(':id', id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#tehsilSelect').empty();
                            $('#tehsilSelect').append(
                                '<option value="">Tehsil</option>');
                            $.each(data, function(key, value) {
                                $('#tehsilSelect').append('<option value="' + key +
                                    '">' +
                                    value + '</option>');
                            });
                        }
                    });
                }
            });
        });

        function isMcc(is_mcc) {
            if (is_mcc == 1) {
                // $('.supplier').attr('disabled',true);
                $('#bank-dtl-tab,#em-dtls-tab,#agreement-info-tab,#chiller-dtl-tab,#owner-dtls-tab,#gen-tab')
                    .removeClass(
                        'disabled');
                $('.bank_details').show();
                $('.is_chiller_ffl_owned_div').hide();
                // $('#is_chiller_ffl_owned').val(1);
                // $('#is_chiller_ffl_owned').trigger('change');
            } else {
                $('.is_chiller_ffl_owned_div').show();
                if ($('#is_chiller_ffl_owned').val() == 1) {
                    $('#chiller-dtl-tab').removeClass('disabled')
                } else {
                    $('#chiller-dtl-tab').addClass('disabled')
                }

                $('.bank_details').hide();
                $('#bank-dtl-tab,#em-dtls-tab,#agreement-info-tab,#owner-dtls-tab,#gen-tab').addClass('disabled');
                // document.getElementsByClassName("supplier")[0].removeAttribute("disabled");
            }
            $('#location-dtl-tab').click();
        }

        function haveChiller(haveChiller) {
            if (haveChiller == 1) {
                $('#chiller-dtl-tab').removeClass('disabled');
            } else {
                $('#chiller-dtl-tab').addClass('disabled');
            }
        }

        $(document).on('submit', '#createForm', function(e) {
            $('#save_button').removeClass('btn-primary')
            $('#save_button').addClass('btn-danger')
            e.preventDefault();
            let data = $(this).serialize();

            $.ajax({
                url: '{{ route('collection-point.store') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')
                        $('#createForm')[0].reset();
                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                        $('#code').val(response.code)
                        $('.alert_div').removeClass('d-none')
                        $('.msg').text(response.message)
                        $('.select2').val('').trigger('change');
                        $('#gen_details_table > tbody').html('');
                        $('#generator_ids').val('');
                        generator_ids = [];

                    } else {
                        $('.alert_div').addClass('d-none')
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')
                        $.toast({
                            text: response.message,
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 7000,
                        })
                        if (response.key) {
                            $(window).scrollTop(10);
                            // $('#createForm').animate({ scrollTop: ($('#createForm').offset().top - 10) }, 1);
                            // $("*[name='"+response.key+"']").focus();
                            $("*[name='" + response.key + "']").css('border', '1px solid red')
                            if (response.key == 'agreement_period_from' || response.key ==
                                'agreement_period_to') {
                                $('#agreement-info-tab').click();
                            } else if (response.key == 'bank_id') {
                                $('#bank-dtl-tab').click();
                            } else if (response.key == 'address') {
                                $('#location-dtl-tab').click();
                            }
                        }
                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                    }
                }
            });

        })

        var count = 1;
        let generator_ids = [];

        function addGenerator() {
            if (!$('#generator_id').val()) {
                showAlert('error', 'Generator is required');
                return;
            }
            let generator_text = $('#generator_id option:selected').text()
            let generator_id = $('#generator_id option:selected').val()
            let installation_date = $('#generator_installation_date').val()

            $('#generator_id option:selected').remove();
            let html = `<tr id="gen_row_${generator_id}"><td>${count++}</td><td>${generator_text}</td><td>${installation_date}</td>
<!--                    <td><span class="fa fa-trash text-danger cursor-pointer" onclick="deleteGenerator('${generator_id}','${generator_text}')"></span></td>-->
                        </tr>`;
            $('#gen_details_table > tbody:last-child').append(html)
            $("#generator_id").val('').trigger('change')
            let generator = {
                id: generator_id,
                installation_date: installation_date,
                status: 1
            };
            generator_ids.push(generator);
            $('#generator_ids').val(JSON.stringify(generator_ids))
            $('#addGeneratorModal').modal('hide');
            showAlert('success', 'Added');

        }


        var chiller_count = 1;
        let chiller_ids = [];

        function addChiller() {
            if (!$('#chiller_id').val()) {
                showAlert('error', 'Chiller is required');
                return;
            }
            let chiller_text = $('#chiller_id option:selected').text()
            let chiller_id = $('#chiller_id option:selected').val()
            let installation_date = $('#chiller_installation_date').val()
            $.ajax({
                url: '{{ route('get.chiller.detail', ['inventoryItem' => ':chiller_id']) }}'.replace(':chiller_id',
                    chiller_id),
                method: 'get',
                success: function(response) {
                    $('#chiller_id option:selected').remove();
                    let html = `<tr ><td>${count++}</td><td>${chiller_text}</td><td>${response.capacity}</td><td>${installation_date}</td>
                      </tr>`;
                    $('#chiller_details_table > tbody:last-child').append(html)
                    $("#chiller_id").val('').trigger('change')
                    let chiller = {
                        id: chiller_id,
                        installation_date: installation_date,
                        status: 1
                    };
                    chiller_ids.push(chiller);
                    $('#chiller_ids').val(JSON.stringify(chiller_ids))
                    $('#addChillerModal').modal('hide');
                    showAlert('success', 'Added');
                }
            });



        }

        // function deleteGenerator(id, name){
        //     let ids = $('#generator_ids').val()
        //     ids = ids.split(',');
        //     $("#generator_id").append(new Option(name, id));
        //     $('#gen_row_'+id).remove()
        //     console.log(ids)
        //     ids = ids.filter(function(item) {
        //         return item.id != id
        //     })
        //     // $('#cp_ids').val(ids.toString())
        //     $('#generator_ids').val(JSON.stringify(generator_ids))
        //     showAlert('success','Successfully deleted');
        // }
    </script>


@endsection
