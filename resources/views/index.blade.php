<!DOCTYPE html>
<html>

<head>
    <title>Map Routing Example</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="map"></div>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9-sqImx-jkiMOPnaIAT0pYq_HdPmkmzM&libraries=geometry&callback=initMap"
        async defer></script>
    <script>
        var map;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 0,
                    lng: 0
                },
                zoom: 2
            });

            var locations = [{
                    lat: 31.5321493,
                    lng: 74.3246881
                },
                {
                    lat: 31.5385837,
                    lng: 74.3373794
                },
                {
                    lat: 31.4409518,
                    lng: 74.3977316
                }
            ];
console.log(locations)
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

                    console.log('Total distance: ' + distance + ' meters');
                } else {
                    console.log('Directions request failed. Status: ' + status);
                }
            });
        }
    </script>
</body>

</html>
