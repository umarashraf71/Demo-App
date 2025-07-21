@extends('layouts/contentLayoutMaster')
@section('title', 'Add Area Office')

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
                {{-- <h4 class="card-title my-2">Add Area Office</h4> --}}
                <div class="card">
                    <div class="card-header">
                        <h5>Add Area Office</h5>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $message }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        @if ($errorMessage = Session::get('errorMessage'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $errorMessage }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <form class="form" action="{{ route('area-office.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-1">Area Office Code *</label>
                                        <input type="text" disabled id="column-ao-1" class="form-control" name="code"
                                            value="{{ old('code', $code) }}" maxlength="7" />
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
                                                    {{ Input::old('zone_id') == $value->id ? 'selected' : '' }}>
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
                                        <label class="form-label" for="column-ao-3">Area Office Name *</label>
                                        <input type="text" id="column-ao-3" class="form-control" placeholder="Area Name"
                                            name="name" value="{{ old('name') }}" />
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-3">Area Short Name *</label>
                                        <input type="text" class="form-control" placeholder="Area Short Name"
                                            name="short_name" value="{{ old('short_name') }}" />
                                        @error('short_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao-5">Contact #</label><span
                                            class="text-danger">*</span>
                                        <input type="text" id="column-ao-5" class="form-control phone"
                                            placeholder="+92-3XX-XXXXXXX" name="contact" value="{{ old('contact') }}" />
                                        @error('contact')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-ao5">WhatsApp #</label>
                                        <input type="text" id="column-ao5" class="form-control phone"
                                            placeholder="+92-3XX-XXXXXXX" name="whatsapp"
                                            value="{{ old('whatsapp') }}" />
                                        @error('whatsapp')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <ul class="nav nav-tabs scroller" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#location-tab"
                                                    role="tab" aria-controls="location-tab"
                                                    aria-selected="true">Location Details</a>
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
                                                <a class="nav-link" id="owner-dtls-tab" data-toggle="tab"
                                                    href="#owner-tab" role="tab" aria-controls="owner-tab"
                                                    aria-selected="false">Owner Details</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="chiller-dtl-tab" data-toggle="tab"
                                                    href="#next-kin-tab" role="tab" aria-controls="next-kin-tab"
                                                    aria-selected="true">Next Of Kin</a>
                                            </li>

                                        </ul>
                                        <div class="tab-content tab-data" style="padding: 5px;" id="myTabContent">
                                            <div class="tab-pane fade show active" id="location-tab" role="tabpanel"
                                                aria-labelledby="bank-dtl-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h2>Location Details</h2>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="district">District</label>
                                                            <select class="form-control select2" name="district_id"
                                                                required id="districtSelect">
                                                                <option value="">District</option>
                                                                @foreach ($districts as $district)
                                                                    <option value="{{ $district->id }}"
                                                                        {{ old('district_id') == $district->id ? 'selected' : '' }}>
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
                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label"
                                                                for="plant-latitude">Lattitude</label>
                                                            <input type="text" id="latitude" class="form-control"
                                                                name="latitude" placeholder="Latitude"
                                                                value="{{ old('latitude') }}" />
                                                            @error('latitude')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label"
                                                                for="plant-longitude">Longitude</label>
                                                            <input type="text" id="longitude" class="form-control"
                                                                name="longitude" placeholder="Longitude"
                                                                value="{{ old('longitude') }}" />
                                                            @error('longitude')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="plant-longitude">Location
                                                                *</label>
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

                                            <div class="tab-pane fade show" id="bank-tab" role="tabpanel"
                                                aria-labelledby="bank-dtl-tab">
                                                <div class="row">
                                                    @include('content._partials._sections.add_bank')
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="next-kin-tab" role="tabpanel"
                                                aria-labelledby="chiller-dtl-tab">
                                                <div class="row">

                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column-own-10">Next of Kin -
                                                                Name </label><span class="text-danger">*</span>
                                                            <input type="text" id="column-own-10" class="form-control"
                                                                placeholder="Next of Kin - Name" name="next_of_kin_name"
                                                                value="{{ old('next_of_kin_name') }}" />
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
                                                                value="{{ old('next_of_kin_father_name') }}" />
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
                                                                value="{{ old('relation') }}" />
                                                            @error('relation')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-1">
                                                            <label class="form-label" for="column-own-13">Contact
                                                                *</label>
                                                            <input type="text" id="column-own-13"
                                                                class="form-control phone" placeholder="+92-3XX-XXXXXXX"
                                                                name="next_of_kin_contact"
                                                                value="{{ old('next_of_kin_contact') }}" />
                                                            @error('next_of_kin_contact')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
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
                                                            @error('rent')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-row" style="display:flex; margin-top:20px;">
                                                            <div class="col">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="paymentOption" id="prepaid"
                                                                        value="prepaid"
                                                                        {{ old('paymentOption') == 'prepaid' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" required
                                                                        for="prepaid">
                                                                        Prepaid
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        required name="paymentOption" id="postpaid"
                                                                        value="postpaid"
                                                                        {{ old('paymentOption') == 'postpaid' ? 'checked' : '' }}>
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
                                                                    placeholder="To: (YYYY-MM-DD)"
                                                                    name="agreement_period_to"
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
                                                                <input value="{{ old('owner_name') }}"
                                                                    placeholder="Shop Owner Name" type="text"
                                                                    class=" form-control" name="owner_name">
                                                                @error('owner_name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-12">
                                                            <div class="mb-1">
                                                                <label class="form-label">Father Name</label>
                                                                <input value="{{ old('owner_father_name') }}"
                                                                    placeholder="Father Name" type="text"
                                                                    class=" form-control" name="owner_father_name">
                                                                @error('owner_father_name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-12">
                                                            <div class="mb-1">
                                                                <label class="form-label" for="column-own-13">
                                                                    CNIC</label>
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
                                                                    class="form-control phone"
                                                                    placeholder="Contact Number" name="owner_contact"
                                                                    value="{{ old('owner_contact') }}" />
                                                                @error('owner_contact')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-12">
                                                            <div class="mb-1">
                                                                <label class="form-label">With Effective Date</label>

                                                                <input type="text" required id="with_effective_date"
                                                                    class="form-control flatpickr-basic"
                                                                    placeholder="W.E.D" name="with_effective_date"
                                                                    value="{{ old('with_effective_date') }}" />
                                                                @error('with_effective_date')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary me-1 submitButton">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                        <a class="btn btn-outline-secondary cancel-btn"
                                            href="{{ route('area-office.index') }}">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
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
            var marker = new google.maps.Marker({
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
    </script>


    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                showAlert('error', '{{ $errors->first() }}');
            @endif
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
    </script>
@endsection
