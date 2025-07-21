@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Plant')
@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/style.css') }}">
@endsection
@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add New Plant Record</h4>
                    </div>
                    <div class="card-body">
                        @if ($success = Session::get('success'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $success }}</div>
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
                        <form class="form" action="{{ route('plant.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="plant-code-column">Plant Code</label>
                                        <input type="text" id="plant-code-column" class="form-control"
                                            placeholder="Plant Code" name="code" value="{{ old('code', $code) }}"
                                            maxlength="7" disabled />
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name-column">Name *</label>
                                        <input type="text" id="name-column" class="form-control" placeholder="Name"
                                            name="name" value="{{ old('name') }}" />
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{--              <div class="col-md-6 col-12"> --}}
                                {{--                <div class="mb-1"> --}}
                                {{--                  <label class="form-label" for="plant-address">Address *</label> --}}
                                {{--                  <input --}}
                                {{--                    type="text" --}}
                                {{--                    id="plant-address" --}}
                                {{--                    class="form-control" --}}
                                {{--                    placeholder="Address" --}}
                                {{--                    name="address" --}}
                                {{--                    value="{{ old('address') }}" --}}
                                {{--                  /> --}}
                                {{--                  @error('address') --}}
                                {{--                        <span class="text-danger">{{$message}}</span> --}}
                                {{--                  @enderror --}}
                                {{--                </div> --}}
                                {{--              </div> --}}
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="plant-latitude">Latitude *</label>
                                        <input type="text" id="latitude" class="form-control" name="latitude"
                                            placeholder="Latitude" value="{{ old('latitude') }}" />
                                        @error('latitude')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="plant-longitude">Longitude *</label>
                                        <input type="text" id="longitude" class="form-control" name="longitude"
                                            placeholder="Longitude" value="{{ old('longitude') }}" />
                                        @error('longitude')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label" for="plant-longitude">Location *</label>
                                        <input id="address" name="address" class="form-control" type="text"
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
                                <div class="col-12 col-sm-12">
                                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                    <a class="btn btn-outline-secondary cancel-btn"
                                        href="{{ route('plant.index') }}">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfh-Jh-Vn1Lf2TeP9g9cf5bzRbX1gnFZ4&libraries=places&callback=initAutocomplete&libraries=places"
        async defer></script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script> --}}
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

@endsection
