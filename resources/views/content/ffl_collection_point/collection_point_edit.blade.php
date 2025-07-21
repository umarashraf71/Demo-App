@extends('layouts/contentLayoutMaster')

@section('title', 'Collection Points')
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-pickadate.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css"
        integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Edit Collection Point</h2>
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
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-12 mb-1">
                                            <label class="form-label" for="code">Code</label>
                                            <input type="text" id="code" class="form-control" placeholder="MCC Code"
                                                name="code" disabled value="{{ old('code', $collectionPoint->code) }}"
                                                maxlength="7" />
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12 mb-1 offset-lg-1 offset-md-1">
                                            <label class="form-label" for="code">Name</label>
                                            <input placeholder="Name" class="form-control" name="name"
                                                value="{{ old('name', $collectionPoint->name) }}" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-multiple">Categories *</label>
                                        <select class="select2 form-select" id="select2-multiple" name="category_id">
                                            <option value="" selected disabled>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->_id }}"
                                                    @if ($category->_id == $collectionPoint->category_id) {{ 'selected' }} @endif
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
                                                <option
                                                    {{ old('is_mcc', $collectionPoint->is_mcc) == 1 ? 'selected' : '' }}
                                                    value="1">Yes</option>
                                                <option
                                                    {{ old('is_mcc', $collectionPoint->is_mcc) == 0 ? 'selected' : '' }}
                                                    value="0">No</option>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-1 offset-lg-1 offset-md-1 is_chiller_ffl_owned_div"
                                            style="display: {{ $collectionPoint->is_mcc == 1 ? 'none' : '' }}">
                                            <label class="form-label" for="select2-multiple">Chiller FFL Owned?</label>
                                            <select id="is_chiller_ffl_owned" class="select2 form-select"
                                                onchange="haveChiller(this.value)" name="is_chiller_ffl_owned">
                                                <option value="" selected disabled>Choose option</option>
                                                <option
                                                    {{ old('is_chiller_ffl_owned', $collectionPoint->is_chiller_ffl_owned) == 1 ? 'selected' : '' }}
                                                    value="1">Yes</option>
                                                <option
                                                    {{ old('is_chiller_ffl_owned', $collectionPoint->is_chiller_ffl_owned) == 0 ? 'selected' : '' }}
                                                    value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{--                  <div class="col-md-6 col-12"> --}}
                                {{--                      <div class="mb-1"> --}}
                                {{--                          <label class="form-label" for="select2-multiple">Supplier *</label> --}}
                                {{--                          <select --}}
                                {{--                              class="select2 supplier form-select" {{old('is_mcc',$collectionPoint->is_mcc)==0 ?'':'disabled'}} --}}
                                {{--                          name="supplier"> --}}
                                {{--                              <option value="" selected disabled>Supplier</option> --}}
                                {{--                              @foreach ($suppliers as $supplier) --}}
                                {{--                                  <option {{old('supplier',$collectionPoint->supplier)==$supplier->_id?'selected':''}} value="{{$supplier->_id}}">{{$supplier->name}}</option> --}}
                                {{--                              @endforeach --}}
                                {{--                          </select> --}}
                                {{--                      </div> --}}
                                {{--                  </div> --}}
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-multiple">Area Office *</label>
                                        <select class="select2 form-select" id="select2-multiple" name="area_office_id">
                                            <option value="" selected disabled>Select Area Office</option>
                                            @foreach ($areas as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('area_office_id', $collectionPoint->area_office_id) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <ul class="nav nav-tabs scroller mb-0" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" id="location-dtl-tab"
                                                href="#location-tab" role="tab" aria-controls="location-tab"
                                                aria-selected="true">Location Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="bank-dtl-tab" data-toggle="tab" href="#bank-tab" role="tab"
                                                aria-controls="bank-tab" aria-selected="true">Bank Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_chiller_ffl_owned == 1 || $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="chiller-dtl-tab" data-toggle="tab" href="#chiller-tab"
                                                role="tab" aria-controls="chiller-tab" aria-selected="true">Chiller
                                                Details </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="em-dtls-tab" data-toggle="tab" href="#em-tab" role="tab"
                                                aria-controls="em-kin-tab" aria-selected="false">Electric Meter
                                                Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="agreement-info-tab" data-toggle="tab" href="#agreement-info"
                                                role="tab" aria-controls="agreement-info"
                                                aria-selected="false">Agreement</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="owner-dtls-tab" data-toggle="tab" href="#owner-tab" role="tab"
                                                aria-controls="owner-tab" aria-selected="false">Owner Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ $collectionPoint->is_mcc == 1 ? '' : 'disabled' }}"
                                                id="gen-tab" data-toggle="tab" href="#genset-tab" role="tab"
                                                aria-controls="genset-tab" aria-selected="false">Generators</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content tab-data" style="padding: 5px;" id="myTabContent">
                                        <div class="tab-pane fade show active" id="location-tab" role="tabpanel"
                                            aria-labelledby="bank-dtl-tab">
                                            <div class="row mt-1">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="district">District</label>
                                                        <select class="form-control select2" name="district_id" required
                                                            id="districtSelect">
                                                            <option value="">District</option>
                                                            @foreach ($districts as $district)
                                                                <option value="{{ $district->id }}"
                                                                    {{ $district->_id == $collectionPoint->district_id ? 'Selected' : '' }}>
                                                                    {{ $district->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('district_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="tehsil">Tehsil</label>
                                                        <select class="form-control select2" name="tehsil_id" required
                                                            id="tehsilSelect">
                                                            <option value="">Tehsil</option>
                                                            @foreach ($tehsils as $tehsil)
                                                                <option value="{{ $tehsil->id }}"
                                                                    {{ $tehsil->_id == $collectionPoint->tehsil_id ? 'Selected' : '' }}>
                                                                    {{ $tehsil->name }}</option>
                                                            @endforeach
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
                                                            name="latitude" placeholder="Latitude" readonly
                                                            value="{{ old('latitude', $collectionPoint->latitude) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Longitude</label>
                                                        <input type="text" id="longitude" class="form-control"
                                                            name="longitude" placeholder="Longitude" readonly
                                                            value="{{ old('longitude', $collectionPoint->longitude) }}" />

                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Location *</label>
                                                        <input autocomplete="off" id="address" name="address"
                                                            class="form-control" type="text"
                                                            value="{{ old('address', $collectionPoint->address) }}"
                                                            placeholder="Location">
                                                        <div class="invalid-feedback address"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div id="map" class="home-map">
                                                        <div id="map-canvas" class="map-canvas-event"
                                                            style="height: 260px; margin-bottom: 1%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="bank-tab" role="tabpanel"
                                            aria-labelledby="bank-dtl-tab">
                                            <div class="row mt-1 bank_details"
                                                style="display: {{ old('is_mcc', $collectionPoint->is_mcc) == 1 ? '' : 'none' }}">
                                                @include('content._partials._sections.edit_bank', [
                                                    'bank_detail' => $collectionPoint,
                                                ])
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="chiller-tab" role="tabpanel"
                                            aria-labelledby="chiller-dtl-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="row border-bottom pb-1">
                                                            <div class="col-10">
                                                                {{--                                                      <h4 class="card-title mt-1 my-auto">Chiller Details</h4> --}}
                                                            </div>
                                                            <div class="col-2 agreement-edit-btn">
                                                                <a class="add-new-btn btn btn-primary mr_30px "
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#addChillerModal">Add</a>
                                                            </div>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="card-datatable">
                                                                <table class="table" id="chiller_details_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>Name</th>
                                                                            <th>Installation Date</th>
                                                                            <th>Status</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if ($collectionPoint->chillers && count($collectionPoint->chillers) > 0)
                                                                            @foreach ($collectionPoint->chillers as $key => $data)
                                                                                @php
                                                                                    $inventory = isset($data['id']) ? App\Helpers\Helper::getInventoryById($data['id']) : '';
                                                                                    $i = 0;
                                                                                @endphp
                                                                                @if ($inventory)
                                                                                    <tr
                                                                                        id="chiller_row_{{ ++$i }}">
                                                                                        <td>{{ $i }}</td>
                                                                                        <td>{{ $inventory ? $inventory->name : '' }}
                                                                                        </td>
                                                                                        <td>{{ $data['installation_date'] }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <div
                                                                                                class="form-check form-switch form-check-primary">
                                                                                                <input type="checkbox"
                                                                                                    class="form-check-input"
                                                                                                    {{ isset($data['status']) && $data['status'] == 1 ? 'checked' : '' }}
                                                                                                    id="chiller_status_{{ $key }}"
                                                                                                    name="status"
                                                                                                    onclick="updateChillerStatus('{{ $key }}')"
                                                                                                    value="1" />
                                                                                                <label
                                                                                                    class="form-check-label "
                                                                                                    for="chiller_status_{{ $key }}">
                                                                                                    <span
                                                                                                        class="switch-icon-left"><i
                                                                                                            data-feather="check"></i></span>
                                                                                                    <span
                                                                                                        class="switch-icon-right"><i
                                                                                                            data-feather="x"></i></span>
                                                                                                </label>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <span
                                                                                                class="fa fa-trash text-danger cursor-pointer"
                                                                                                onclick="deleteChiller('{{ $key }}','{{ $i }}','{{ $inventory ? $inventory->id : '' }}','{{ $inventory ? $inventory->name : '' }}')"></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="5"
                                                                                    class="text-center empty">No data found
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
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
                                                        value="{{ old('meter_owner_name', $collectionPoint->meter_owner_name) }}"
                                                        min='1' max="25" type="text"
                                                        class=" form-control" name="meter_owner_name">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-1">
                                                    <label for="">Number Of Phases</label>
                                                    <input placeholder="Phase"
                                                        value="{{ old('phase', $collectionPoint->phase) }}"
                                                        min='0' type="number" class=" form-control"
                                                        name="phase">
                                                </div>
                                                <div class="col-lg-4 mb-1 ">
                                                    <label for="">Meter Number</label>
                                                    <input value="{{ old('meter_no', $collectionPoint->meter_no) }}"
                                                        placeholder="Meter#" type="text" class=" form-control"
                                                        name="meter_no">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="agreement-info" role="tabpanel"
                                            aria-labelledby="agreement-info-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="row border-bottom pb-1">
                                                            <div class="col-11">
                                                                {{--                                           <h4 class="card-title mt-1 my-auto">Agreement Details</h4> --}}
                                                            </div>
                                                            <div class="col-1  agree_add_btn ">
                                                                <a class="add-new-btn btn btn-primary " href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addAgreementModal">Add</a>
                                                            </div>
                                                        </div>

                                                        <div class="mt-1">
                                                            <div class="card-datatable">
                                                                <table class="table" id="agreement_details_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>Ref. #</th>
                                                                            <th>Rent</th>
                                                                            <th>From</th>
                                                                            <th>To</th>
                                                                            <th>w.e.f</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @isset($collectionPoint->agreements)
                                                                            @forelse($collectionPoint->agreements as $key=>$agreement)
                                                                                <tr>
                                                                                    <td>{{ ++$key }}</td>
                                                                                    <td>{{ @$agreement['refrence_no'] }}</td>
                                                                                    <td>{{ @$agreement['rent'] }}</td>
                                                                                    <td>{{ @$agreement['from'] }}</td>
                                                                                    <td>{{ @$agreement['to'] }}</td>
                                                                                    <td>{{ @$agreement['wef'] }}</td>
                                                                                    <td>
                                                                                        <div
                                                                                            class="form-check form-switch form-check-primary">
                                                                                            <input type="checkbox"
                                                                                                class="form-check-input"
                                                                                                {{ isset($agreement['status']) && $agreement['status'] == 1 ? 'checked' : '' }}
                                                                                                id="status_{{ $key }}"
                                                                                                name="status"
                                                                                                onclick="updateAgrementStatus('{{ $key }}')"
                                                                                                value="1" />
                                                                                            <label class="form-check-label"
                                                                                                for="status_{{ $key }}">
                                                                                                <span
                                                                                                    class="switch-icon-left"><i
                                                                                                        data-feather="check"></i></span>
                                                                                                <span
                                                                                                    class="switch-icon-right"><i
                                                                                                        data-feather="x"></i></span>
                                                                                            </label>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                        </tbody>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="7" class="text-center empty">No
                                                                                data found</td>
                                                                        </tr>
                                                                        @endforelse
                                                                    @endisset
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="owner-tab" role="tabpanel"
                                            aria-labelledby="owner-dtls-tab">

                                            <div class="row border-bottom pb-1">
                                                <div class="col-11"></div>
                                                <div class="col-1">
                                                    <a class="add-new-btn btn btn-primary" href="#"
                                                        data-bs-toggle="modal" data-bs-target="#addOwnerModal">Add</a>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table mt-1 mb-1  " id="owner_details_table">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Name</th>
                                                            <th>Father Name</th>
                                                            <th>Cnic</th>
                                                            <th>Ntn</th>
                                                            <th>Contact</th>
                                                            <th>WhatsApp</th>
                                                            <th>Date</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @isset($collectionPoint->owners)
                                                            @forelse($collectionPoint->owners as $key=>$data)
                                                                <tr>
                                                                    <td>{{ ++$key }}</td>
                                                                    <td>{{ $data['name'] }}</td>
                                                                    <td>{{ $data['father_name'] }}</td>
                                                                    <td>{{ $data['cnic'] }}</td>
                                                                    <td>{{ $data['ntn'] }}</td>
                                                                    <td>{{ $data['contact'] }}</td>
                                                                    <td>{{ $data['owner_whatsapp'] ?? 'NA' }}</td>
                                                                    <td>{{ $data['with_effective_date'] ?? 'NULL' }}</td>
                                                                    <td>
                                                                        <div class="form-check form-switch form-check-primary">
                                                                            <input type="checkbox" class="form-check-input"
                                                                                {{ isset($data['status']) && $data['status'] == 1 ? 'checked' : '' }}
                                                                                id="owner_status_{{ $key }}"
                                                                                name="status"
                                                                                onclick="updateOwnerStatus('{{ $key }}')"
                                                                                value="1" />
                                                                            <label class="form-check-label"
                                                                                for="owner_status_{{ $key }}">
                                                                                <span class="switch-icon-left"><i
                                                                                        data-feather="check"></i></span>
                                                                                <span class="switch-icon-right"><i
                                                                                        data-feather="x"></i></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                        </tbody>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center empty">No data found</td>
                                                        </tr>
                                                        @endforelse
                                                    @endisset
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="genset-tab" role="tabpanel"
                                            aria-labelledby="genset-tab">
                                            <div class="row border-bottom pb-1">
                                                <div class="col-1 offset-11">
                                                    <a class="btn btn-primary repeater-add-btn" data-bs-toggle="modal"
                                                        data-bs-target="#addGeneratorModal">
                                                        <span class="fa fa-plus"></span>
                                                    </a>
                                                </div>
                                            </div>

                                            <table class="table mt-1 mb-1" id="gen_details_table">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Name</th>
                                                        <th>Installation Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($collectionPoint->generators && count($collectionPoint->generators) > 0)
                                                        @foreach ($collectionPoint->generators as $key => $data)
                                                            @php
                                                                $inventory = isset($data['id']) ? App\Helpers\Helper::getInventoryById($data['id']) : '';
                                                                $i = 0;
                                                            @endphp
                                                            @if ($inventory)
                                                                <tr id="gen_row_{{ ++$i }}">
                                                                    <td>{{ $i }}</td>
                                                                    <td>{{ $inventory ? $inventory->name : '' }}</td>
                                                                    <td>{{ $data['installation_date'] }}</td>

                                                                    <td>
                                                                        <div
                                                                            class="form-check form-switch form-check-primary">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                {{ isset($data['status']) && $data['status'] == 1 ? 'checked' : '' }}
                                                                                id="generator_status_{{ $key }}"
                                                                                name="status"
                                                                                onclick="updateGeneratorStatus('{{ $key }}')"
                                                                                value="1" />
                                                                            <label class="form-check-label "
                                                                                for="generator_status_{{ $key }}">
                                                                                <span class="switch-icon-left"><i
                                                                                        data-feather="check"></i></span>
                                                                                <span class="switch-icon-right"><i
                                                                                        data-feather="x"></i></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="fa fa-trash text-danger cursor-pointer"
                                                                            onclick="deleteGenerator('{{ $key }}','{{ $i }}','{{ $inventory ? $inventory->id : '' }}','{{ $inventory ? $inventory->name : '' }}')"></span>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center empty">No data found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>

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
    </section>

    <div class="modal fade" id="addAgreementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Agreement Information</h1>
                    </div>
                    <div class="row">
                        {{-- <form id="agreement_form" > --}}
                        <div class="col-md-12">
                            <div class="col-md-12 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="ref_no">Reference #</label>
                                    <input type="text" id="ref_no" class="form-control" placeholder=" Reference #"
                                        name="ref_no" value="" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label">Rent</label>
                                    <input value="{{ old('rent') }}" placeholder="Rent" type="number"
                                        class=" form-control" id="rent" min="0" name="rent">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-12">
                                <label class="form-label" for="agrement_from">Agreement Duration (YYYY-MM-DD)</label>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="agrement_from"
                                            class="form-control flatpickr-basic" placeholder="From"
                                            name="agreement_period_from" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="agrement_to"
                                            class="form-control flatpickr-basic" placeholder="To"
                                            name="agreement_period_to" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="wef"
                                            class="form-control flatpickr-basic" placeholder="w.e.f"
                                            name="agreement_period_wef" />
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <a class="btn btn-primary mt-2 me-1" onclick="addAggrement()">Save</a>
                                <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
                        </div>
                        {{--                                                                </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOwnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Owner Information</h1>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-1">
                                <label class="form-label"> Name</label>
                                <input value="{{ old('shop_owner_name') }}" placeholder="Shop Owner Name" type="text"
                                    class=" form-control" id="shop_owner_name">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label">Father Name</label>
                                <input placeholder="Father Name" type="text" class=" form-control"
                                    id="owner_father_name">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label">With Effective Date</label>

                                <input type="text" required id="with_effective_date"
                                    class="form-control flatpickr-basic" placeholder="W.E.D"
                                    name="with_effective_date" />


                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="column-own-13"> CNIC</label>
                                <input type="text" class="form-control cnic" placeholder="XXXXX-XXXXXX-X"
                                    id="owner_cnic" />
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="column-own-3"> NTN#</label>
                                <input type="text" class="form-control ntn" placeholder="Ntn Number"
                                    id="owner_ntn" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-1">
                                <label class="form-label" for="owner_contact">Contact Number</label>
                                <input type="text" class="form-control phone" placeholder="Contact Number"
                                    id="owner_contact" value="{{ old('owner_contact') }}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-1">
                                <label class="form-label" for="owner_whatsapp">WhatsApp Number</label>
                                <input type="text" class="form-control phone" placeholder="Whatsapp Number"
                                    id="owner_whatsapp" value="{{ old('owner_whatsapp') }}" />
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <a class="btn btn-primary mt-2 me-1" onclick="addOwner()">Save</a>
                            <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
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
                        {{-- <form id="agreement_form" > --}}

                        <div class="col-md-12 ">
                            <label class="form-label" for="select2-multiple">Chillers</label>
                            <select data-placeholder="Select chiller" id="chiller_id" class="form-control select2 mb-2">
                                <option value="" disabled selected>Select chiller</option>
                                @foreach ($chillers as $chiller)
                                    <option value="{{ $chiller->id }}">{{ $chiller->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 ">
                            <label class="form-label" for="select2-multiple">Installation Date</label>
                            <input type="text" id="chiller_installation_date" class="form-control flatpickr-basic"
                                placeholder="Installation Date" />
                        </div>

                        <div class="col-md-12 text-center">
                            <a class="btn btn-primary mt-2 me-1" onclick="addChiller()">Save</a>
                            <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                        </div>

                        {{--                                                                </form> --}}
                    </div>
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
                            <input type="text" id="generator_installation_date" class="form-control flatpickr-basic"
                                placeholder="Installation Date" />
                        </div>
                        <div class="col-md-12 text-center">
                            <a class="btn btn-primary mt-2 me-1" onclick="addGenerator()">Save</a>
                            <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <input type="hidden" id="cp_id" value="{{ $collectionPoint->id }}">
@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

    <script src="{{ asset('vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

@endsection
@section('page-script')
    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"
        integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('/js/repeater.js') }}"></script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfh-Jh-Vn1Lf2TeP9g9cf5bzRbX1gnFZ4&libraries=places&callback=initAutocomplete&libraries=places"
        async defer></script>
    <script>
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

        $('.cnic').inputmask({
            mask: '99999-9999999-9'
        });
        $('.phone').inputmask({
            mask: '+\\92-399-9999999'
        });
        $('.ntn').inputmask({
            mask: '9999999-9'
        });
        $("#repeater").createRepeater({
            showFirstItemToDefault: true,
        });
        // script for shifting tabs
        $(document).ready(function() {
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        function initAutocomplete() {
            let zoom = 15;
            var lat = parseFloat($("#latitude").val());
            var long = parseFloat($("#longitude").val());
            const map = new google.maps.Map(document.getElementById("map-canvas"), {
                center: {
                    lat: lat,
                    lng: long
                },
                zoom: zoom,
                mapTypeControl: false,
            });

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
            const marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: long
                },
                map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function(evt) {
                $("#latitude").val(evt.latLng.lat().toFixed(8));
                $("#longitude").val(evt.latLng.lng().toFixed(8));
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
        }

        function isMcc(is_mcc) {
            if (is_mcc == 1) {

                $('#bank-dtl-tab,#em-dtls-tab,#agreement-info-tab,#chiller-dtl-tab,#owner-dtls-tab,#gen-tab').removeClass(
                    'disabled');
                $('.bank_details').show();
                $('.is_chiller_ffl_owned_div').hide();

            } else {
                $('.is_chiller_ffl_owned_div').show();
                if ($('#is_chiller_ffl_owned').val() == 1) {
                    $('#chiller-dtl-tab').removeClass('disabled')
                } else {
                    $('#chiller-dtl-tab').addClass('disabled')
                }

                $('.bank_details').hide();
                $('#bank-dtl-tab,#em-dtls-tab,#agreement-info-tab,#owner-dtls-tab,#gen-tab').addClass('disabled');

            }
            $('#location-dtl-tab').click();
        }

        function haveChiller(haveChiller) {
            if (haveChiller == 1) {

                $('#chiller-dtl-tab').removeClass('disabled');
                $('.chiller').show();
            } else {
                $('#chiller-dtl-tab').addClass('disabled');
                $('.chiller').hide();
            }
            $('#location-dtl-tab').click();
        }

        function addAggrement() {
            if ($('#agrement_from').val() == '') {
                showAlert('error', 'Atleast one field is required');
                return;
            }
            if ($('#agrement_agrement_to').val() == '') {
                showAlert('error', 'Agreement to field is required');
                return;
            }
            var fd = new FormData();
            fd.append('ref_no', $('#ref_no').val());
            fd.append('from', $('#agrement_from').val());
            fd.append('rent', $('#rent').val());
            fd.append('to', $('#agrement_to').val());
            fd.append('wef', $('#wef').val());
            fd.append('id', $('#cp_id').val());
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.cp.agreement') }}',
                success: function(response) {
                    if (response.success) {
                        let html = `<tr><td>${response.count}</td><td>${$('#ref_no').val()}</td><td>${$('#rent').val()}</td><td>${$('#agrement_from').val()}</td>
                            <td>${$('#agrement_to').val()}</td><td>${$('#wef').val()}</td>
                            <td>
                                <div class="form-check form-switch form-check-primary">
                                    <input type="checkbox" id="status_${response.count}" class="form-check-input" onclick="updateAgrementStatus('${response.count}')"  name="status" value="1" checked>
                                    <label class="form-check-label" for="status_${response.count}">
                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                    </label>
                                </div>
                            </td>
                            </tr>`;
                        $('.empty').html('')
                        $('#agreement_details_table > tbody:last-child').append(html)
                        $('#agrement_to,#ref_no,#wef,#agrement_from,#rent').val('');
                        $('#addAgreementModal').modal('hide');
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function addOwner() {
            if ($('#shop_owner_name').val() == '') {
                showAlert('error', 'Owner field is required');
                return;
            }
            var fd = new FormData();

            fd.append('shop_owner_name', $('#shop_owner_name').val());
            fd.append('owner_father_name', $('#owner_father_name').val());
            fd.append('with_effective_date', $('#with_effective_date').val());
            fd.append('owner_cnic', $('#owner_cnic').val());
            fd.append('owner_ntn', $('#owner_ntn').val());
            fd.append('owner_contact', $('#owner_contact').val());
            fd.append('owner_whatsapp', $('#owner_whatsapp').val());
            fd.append('id', $('#cp_id').val());
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.cp.owner') }}',
                success: function(response) {

                    if (response.success) {

                        let html = `<tr><td>${response.count}</td><td>${$('#shop_owner_name').val()}</td><td>${$('#owner_father_name').val()}</td><td>${$('#owner_cnic').val()}</td>
                            <td>${$('#owner_ntn').val()}</td><td>${$('#owner_contact').val()}</td><td>${$('#owner_whatsapp').val()}</td><td>${$('#with_effective_date').val()}</td>
                            <td>
                                <div class="form-check form-switch form-check-primary">
                                    <input type="checkbox" id="owner_status_${response.count}" class="form-check-input" onclick="updateOwnerStatus('${response.count}')"  name="status" value="1">
                                    <label class="form-check-label" for="owner_status_${response.count}">
                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                    </label>
                                </div>
                            </td>
                            </tr>`;
                        $('.empty').html('')
                        $('#owner_details_table > tbody:last-child').append(html)
                        $('#shop_owner_name,#owner_ntn,#owner_cnic,#owner_father_name,#owner_contact',
                            'owner_whatsapp', 'with_effective_date').val('');
                        $('#addOwnerModal').modal('hide');
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function addChiller() {
            if (!$('#chiller_id').val()) {
                showAlert('error', 'Chiller is required');
                return;
            }
            let chiller_id = $('#chiller_id').val();
            let chiller_text = $('#chiller_id option:selected').text();
            var fd = new FormData();
            fd.append('chiller_id', chiller_id);
            fd.append('installation_date', $('#chiller_installation_date').val());
            fd.append('id', $('#cp_id').val());
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.cp.chiller') }}',
                success: function(response) {
                    if (response.success) {
                        let html = `<tr id="chiller_row_${response.count}"><td>${response.count}</td><td>${chiller_text}</td><td>${$('#chiller_installation_date').val()}</td>
                                            <td>
                                                  <div class="form-check form-switch form-check-primary">
                                                      <input type="checkbox" class="form-check-input checked"
                                                    id="chiller_status_${response.index_key}" name="status" onclick="updateChillerStatus('${response.index_key}')" value="1"/>
                                                      <label class="form-check-label " for="chiller_status_${response.index_key}">
                                                          <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                          <span class="switch-icon-right"><i data-feather="x"></i></span>
                                                      </label>
                                                  </div>
                                              </td>
                                              <td>
                                                  <span class="fa fa-trash text-danger cursor-pointer" onclick="deleteChiller('${response.index_key}','${response.count}','${chiller_id}','${chiller_text}')"></span>
                                              </td>
                                    </tr>`;
                        $('.empty').html('')
                        $('#chiller_details_table > tbody:last-child').append(html)
                        $('#chiller_installation_date').val('');

                        $('#addChillerModal').modal('hide');
                        showAlert('success', response.message);
                        $('#chiller_id option:selected').remove();
                        $("#chiller_id").val('').trigger('change')
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function addGenerator() {
            if (!$('#generator_id').val()) {
                showAlert('error', 'Generator is required');
                return;
            }
            let generator_id = $('#generator_id').val();
            let generator_text = $('#generator_id option:selected').text();
            var fd = new FormData();
            fd.append('generator_id', generator_id);
            fd.append('installation_date', $('#generator_installation_date').val());
            fd.append('id', $('#cp_id').val());
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.cp.generator') }}',
                success: function(response) {
                    if (response.success) {
                        let html = `<tr id="gen_row_${response.count}"><td>${response.count}</td><td>${generator_text}</td><td>${$('#generator_installation_date').val()}</td>
                                            <td>
                                                  <div class="form-check form-switch form-check-primary">
                                                      <input type="checkbox" class="form-check-input checked"
                                                    id="generator_status_${response.index_key}" name="status" onclick="updateGeneratorStatus('${response.index_key}')" value="1"/>
                                                      <label class="form-check-label " for="generator_status_${response.index_key}">
                                                          <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                          <span class="switch-icon-right"><i data-feather="x"></i></span>
                                                      </label>
                                                  </div>
                                              </td>
                                              <td>
                                                  <span class="fa fa-trash text-danger cursor-pointer" onclick="deleteGenerator('${response.index_key}','${response.count}','${generator_id}','${generator_text}')"></span>
                                              </td>
                                    </tr>`;
                        $('.empty').html('')
                        $('#gen_details_table > tbody:last-child').append(html)
                        $('#generator_installation_date').val('');

                        $('#addGeneratorModal').modal('hide');
                        showAlert('success', response.message);
                        $('#generator_id option:selected').remove();
                        $("#generator_id").val('').trigger('change')
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function updateAgrementStatus(key) {
            let status = $('#status_' + key).is(':checked');
            if (status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'status': status,
                    'key': key
                },
                url: '{{ route('cp.agreement.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function updateGeneratorStatus(key) {
            let status = $('#generator_status_' + key).is(':checked');
            if (status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'status': status,
                    'key': key
                },
                url: '{{ route('cp.generator.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function updateChillerStatus(key) {
            let status = $('#chiller_status_' + key).is(':checked');
            if (status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'status': status,
                    'key': key
                },
                url: '{{ route('cp.chiller.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function deleteGenerator(key, row, id, name) {
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'key': key
                },
                url: '{{ route('cp.generator.delete') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $("#generator_id").append(new Option(name, id));
                        $('#gen_row_' + row).remove()
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function deleteChiller(key, row, id, name) {
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'key': key
                },
                url: '{{ route('cp.chiller.delete') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $("#chiller_id").append(new Option(name, id));
                        $('#chiller_row_' + row).remove()
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function updateOwnerStatus(key) {
            let status = $('#owner_status_' + key).is(':checked');
            if (status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "get",
                data: {
                    'id': $('#cp_id').val(),
                    'status': status,
                    'key': key
                },
                url: '{{ route('cp.owner.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }



        $(document).on('submit', '#createForm', function(e) {
            e.preventDefault();
            $('#save_button').removeClass('btn-primary')
            $('#save_button').addClass('btn-danger')

            let data = $(this).serialize();
            $.ajax({
                url: '{{ route('collection-point.update', $collectionPoint->id) }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $(window).scrollTop(20);
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')

                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                    } else {
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')

                        $.toast({
                            text: response.message,
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 7000,
                            // bgColor : 'red'
                        })
                        if (response.key) {
                            $("*[name='" + response.key + "']").focus();
                            $('#createForm').animate({
                                scrollTop: ($('input').offset().top - 10)
                            }, 1);
                            $("*[name='" + response.key + "']").css('border', '1px solid red')
                        }
                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                    }
                }
            });
        });
    </script>
@endsection
