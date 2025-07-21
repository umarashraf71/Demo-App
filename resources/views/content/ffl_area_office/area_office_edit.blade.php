@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Area Office')
@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('page-style')
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
                        <h4 class="card-title">Edit Area Office</h4>
                    </div>
                    <div class="card-body">
                        @if ($errorMessage = Session::get('errorMessage'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $errorMessage }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <form class="form" action="{{ route('area-office.update', $areaOffice->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $areaOffice->id }}">
                            <div class="row me-1">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-1">Area Office Code </label><span
                                            class="text-danger">*</span>
                                        <input type="text" id="column-ao-1" class="form-control" name="code" disabled
                                            value="{{ old('code', $areaOffice->code) }}" maxlength="7" />
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-2">Zone *</label>
                                        <select class="select2 form-select" id="column-ao-2" name="zone_id">
                                            <option value="" selected disabled>Select Zone</option>
                                            @foreach ($all_zone as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('zone_id', $areaOffice->zone_id) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('zone_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-3">Area Name *</label>
                                        <input type="text" id="column-ao-3" class="form-control" placeholder="Area Name"
                                            name="name" value="{{ old('name', $areaOffice->name) }}" />
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-3">Area Short Name *</label>
                                        <input type="text" id="column-ao-3" class="form-control"
                                            placeholder="Area Short Name" name="short_name"
                                            value="{{ old('short_name', $areaOffice->short_name) }}" />
                                        @error('short_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-5">Contact *</label>
                                        <input type="text" id="column-ao-5" class="form-control phone"
                                            placeholder="+92-3XX-XXXXXXX" name="contact"
                                            value="{{ old('contact', $areaOffice->contact) }}" />
                                        @error('contact')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="columnao5">WhatsApp #</label>
                                        <input type="text" id="columnao5" class="form-control phone"
                                            placeholder="+92-3XX-XXXXXXX" name="whatsapp"
                                            value="{{ old('whatsapp', $areaOffice->whatsapp) }}" />
                                        @error('whatsapp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <ul class="nav nav-tabs scroller mb-0" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#location-tab"
                                                role="tab" aria-controls="location-tab" aria-selected="true">Location
                                                Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="bank-dtl-tab" data-toggle="tab" href="#bank-tab"
                                                role="tab" aria-controls="bank-tab" aria-selected="true">Bank
                                                Details</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" id="agreement-info-tab" data-toggle="tab"
                                                href="#agreement-info" role="tab" aria-controls="agreement-info"
                                                aria-selected="false">Agreement</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="owner-dtls-tab" data-toggle="tab" href="#owner-tab"
                                                role="tab" aria-controls="owner-tab" aria-selected="false">Owner
                                                Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="nk-dtl-tab" data-toggle="tab" href="#next-kin-tab"
                                                role="tab" aria-controls="next-kin-tab" aria-selected="true">Next Of
                                                Kin</a>
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
                                                                    {{ $district->id == $areaOffice->district_id ? 'Selected' : '' }}>
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
                                                                    {{ $tehsil->_id == $areaOffice->tehsil_id ? 'Selected' : '' }}>
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
                                                            value="{{ old('latitude', $areaOffice->latitude) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Longitude</label>
                                                        <input type="text" id="longitude" class="form-control"
                                                            name="longitude" placeholder="Longitude" readonly
                                                            value="{{ old('longitude', $areaOffice->longitude) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Location *</label>
                                                        <input autocomplete="off" id="address" name="address"
                                                            class="form-control" type="text"
                                                            value="{{ old('address', $areaOffice->address) }}"
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
                                            <div class="row mt-1 bank_details">
                                                @include('content._partials._sections.edit_bank', [
                                                    'bank_detail' => $areaOffice,
                                                ])
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="agreement-info" role="tabpanel"
                                            aria-labelledby="agreement-info-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="row border-bottom pb-1">
                                                            <div class="col-10">
                                                            </div>
                                                            <div class="col-2 agree_add_btn">
                                                                <a class="add-new-btn btn btn-primary  mr_30px"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#addAgreementModal">Add</a>
                                                            </div>
                                                        </div>

                                                        <div class="card-body">
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
                                                                        @isset($areaOffice->agreements)
                                                                            @forelse($areaOffice->agreements as $key=>$agreement)
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
                                                    <a class="add-new-btn btn btn-primary mr_30px chiller" href="#"
                                                        data-bs-toggle="modal" data-bs-target="#addOwnerModal">Add</a>
                                                </div>
                                            </div>
                                            <table class="table" id="owner_details_table">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Name</th>
                                                        <th>Father Name</th>
                                                        <th>Cnic</th>
                                                        <th>Ntn</th>
                                                        <th>Contact</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @isset($areaOffice->owners)
                                                        @forelse($areaOffice->owners as $key=>$data)
                                                            <tr>
                                                                <td>{{ ++$key }}</td>
                                                                <td>{{ $data['name'] }}</td>
                                                                <td>{{ $data['father_name'] }}</td>
                                                                <td>{{ $data['cnic'] }}</td>
                                                                <td>{{ $data['ntn'] }}</td>
                                                                <td>{{ $data['contact'] }}</td>
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

                                        <div class="tab-pane fade" id="next-kin-tab" role="tabpanel"
                                            aria-labelledby="chiller-dtl-tab">
                                            <div class="row">

                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-10">Next of Kin - Name
                                                        </label><span class="text-danger">*</span>
                                                        <input type="text" id="column-own-10" class="form-control"
                                                            placeholder="Next of Kin - Name" name="next_of_kin_name"
                                                            value="{{ old('next_of_kin_name', $areaOffice->next_of_kin_name) }}" />
                                                        @error('next_of_kin_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-11">Father Name
                                                        </label><span class="text-danger">*</span>
                                                        <input type="text" id="column-own-11" class="form-control"
                                                            placeholder="Father Name" name="next_of_kin_father_name"
                                                            value="{{ old('next_of_kin_father_name', $areaOffice->next_of_kin_father_name) }}" />
                                                        @error('next_of_kin_father_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-12">Relation
                                                        </label><span class="text-danger">*</span>
                                                        <input type="text" id="column-own-12" class="form-control"
                                                            placeholder="Relation" name="relation"
                                                            value="{{ old('relation', $areaOffice->relation) }}" />
                                                        @error('relation')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-13">Contact *</label>
                                                        <input type="text" id="column-own-13"
                                                            class="form-control phone" placeholder="+92-3XX-XXXXXXX"
                                                            name="next_of_kin_contact"
                                                            value="{{ old('next_of_kin_contact', $areaOffice->next_of_kin_contact) }}" />
                                                        @error('next_of_kin_contact')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-1 submitButton">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                    <a class="btn btn-outline-secondary cancel-btn"
                                        href="{{ route('area-office.index') }}">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>



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
                                <input placeholder="Shop Owner Name" type="text" class=" form-control"
                                    id="owner_name">
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
                                <label class="form-label" for="column-own-13"> CNIC</label>
                                <input type="text" class="form-control cnic" placeholder="XXXXX-XXXXXX-X"
                                    id="owner_cnic" />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="column-own-3"> NTN#</label>
                                <input type="text" class="form-control ntn" placeholder="Ntn Number"
                                    id="owner_ntn" />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-1">
                                <label class="form-label" for="owner_contact">Contact Number</label>
                                <input type="text" class="form-control phone" placeholder="Contact Number"
                                    id="owner_contact" value="{{ old('owner_contact') }}" />
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

    <!-- Page js files -->
    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
@endsection
@section('page-script')
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
        });
        // initAutocomplete();
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
                $('.supplier').attr('disabled', true);
                $('.bank_details').hide();
            } else {
                document.getElementsByClassName("supplier")[0].removeAttribute("disabled");
                $('.bank_details').show();
            }
        }

        function haveChiller(haveChiller) {
            if (haveChiller == 1) {
                $('.chillers_section').show();
            } else {
                $('.chillers_section').hide();
            }
        }

        function addOwner() {
            if ($('#owner_name').val() == '') {
                showAlert('error', 'Owner field is required');
                return;
            }
            var fd = new FormData();
            fd.append('owner_name', $('#owner_name').val());
            fd.append('owner_father_name', $('#owner_father_name').val());
            fd.append('owner_cnic', $('#owner_cnic').val());
            fd.append('owner_ntn', $('#owner_ntn').val());
            fd.append('owner_contact', $('#owner_contact').val());
            fd.append('with_effective_date', $('#with_effective_date').val());
            fd.append('id', '{{ $areaOffice->id }}');
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.ao.owner') }}',
                success: function(response) {
                    if (response.success) {
                        let html = `<tr><td>${response.count}</td><td>${$('#owner_name').val()}</td><td>${$('#owner_father_name').val()}</td><td>${$('#owner_cnic').val()}</td>
                            <td>${$('#owner_ntn').val()}</td><td>${$('#owner_contact').val()}</td><td>${$('#with_effective_date').val()}</td>
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
                        $('#owner_name,#owner_ntn,#owner_cnic,#owner_father_name,#owner_contact,#with_effective_date')
                            .val('');
                        $('#addOwnerModal').modal('hide');
                        showAlert('success', response.message);
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
                    'id': '{{ $areaOffice->id }}',
                    'status': status,
                    'key': key
                },
                url: '{{ route('ao.owner.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }

        function addAggrement() {
            if ($('#agrement_from').val() == '') {
                showAlert('error', 'From date is required');
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
            fd.append('id', '{{ $areaOffice->id }}');
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.ao.agreement') }}',
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
                    'id': '{{ $areaOffice->id }}',
                    'status': status,
                    'key': key
                },
                url: '{{ route('ao.agreement.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }


        $(document).ready(function() {
            @if ($errors->any())
                showAlert('error', '{{ $errors->first() }}');
            @endif
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
        $('.phone,#column-own-4').inputmask({
            mask: '+\\92-999-9999999'
        });
        $('.ntn').inputmask({
            mask: '9999999-9'
        });
    </script>
@endsection
