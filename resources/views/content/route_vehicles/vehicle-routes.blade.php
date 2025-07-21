@extends('layouts/contentLayoutMaster')

@section('title', 'Route Vehicles List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection
@section('content')
<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
    <!-- Column Search -->
    <section id="column-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Vehicles Visted Routes</h4>
                        <h4 class="card-title mt-20 mr_30px">Total Distance In KM: <span id="km-distance"></span></h4>
                       
                        {{--            @can('Create Route Vehicles') --}}
                       
                        {{--           @endcan --}}
                    </div>

                    <div class="card-datatable table-responsive">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
  

@endsection

@section('vendor-script')
    {{-- vendor files table data --}}
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    {{-- <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script> --}}

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9-sqImx-jkiMOPnaIAT0pYq_HdPmkmzM&libraries=geometry&callback=initMap"
    async defer></script>
    <script>
        var map;
        var newlocations   = JSON.parse('<?php echo  $jsonArray ;?>');
        var locations = newlocations.slice(0,27);
      
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 0,
                    lng: 0
                },
                zoom: 2
            });
            var directionsService = new google.maps.DirectionsService();

            var directionsRenderer = new google.maps.DirectionsRenderer({
                map: map
            });

            var waypoints = locations.slice(1, -1).map(function(location) {
                return {
                    location: location,
                    stopover: true
                };
            });

            directionsService.route({
                origin: locations[0],
                destination: locations[locations.length - 1],
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);

                    var distance = 0;
                    var legs = response.routes[0].legs;
                    for (var i = 0; i < legs.length; i++) {
                        distance += legs[i].distance.value;
                    }

                    var kmdistnce = distance/1000;
                    $("#km-distance").html(kmdistnce.toFixed(2))
                    console.log('Total distance: ' + distance + ' meters');
                } else {
                    console.log('Directions request failed. Status: ' + status);
                }
            });
        }
    </script>
@endsection
