@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kml Files</h1>
                </div>
                <div>
                    @if (!empty($kmlContent))
                        <form action="{{ route('kmlFiles.delete') }}" method="POST">
                            @csrf
                            {!! Form::submit('Delete KML', ['class' => 'btn btn-danger ', 'style' => 'height:40px;margin:20px']) !!}
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div id="map" style="height: 500px;"></div>
        <div class="row">
            @include('kmlFiles.uploadFile')
        </div>
    </div>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIMBzi2LF8B9qIeLC4bH8D5SUaQTf1xLc&callback=initMap&v=weekly"
        defer></script>
    <script>
        function initMap() {
            @if (!empty($kmlContent))
                var kmlContent = `{!! $kmlContent !!}`;
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(kmlContent, 'text/xml');
                var placemarks = xmlDoc.getElementsByTagName('Placemark');
                var styles = xmlDoc.getElementsByTagName('Style');
                var coordinates = [];
                if (placemarks[0].querySelector('Point')) {
                    coordinates = placemarks[0].querySelector('Point coordinates').textContent.split(',');
                    var map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: parseFloat(coordinates[1]),
                            lng: parseFloat(coordinates[0])
                        },
                        zoom: 10
                    });
                } else if (placemarks[0].querySelector('Polygon')) {
                    var coords = placemarks[0].querySelectorAll('Polygon coordinates')[0].textContent;
                    coords = coords.split('\n              ').map(function(coord) {
                        var latLng = coord.split(',');
                        if (!isNaN(latLng[1]) && !isNaN(latLng[0])) {
                            coordinates.push({
                                lat: parseFloat(latLng[1]),
                                lng: parseFloat(latLng[0])
                            });
                        }
                    });
                    var map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: parseFloat(coordinates[0]['lat']),
                            lng: parseFloat(coordinates[0]['lng'])
                        },
                        zoom: 10
                    });
                } else if (placemarks[0].querySelector('LineString')) {
                    var coords = placemarks[0].querySelectorAll('LineString coordinates')[0].textContent;
                    coords = coords.split(' ').map(function(coord) {
                        var latLng = coord.split(',');
                        if (!isNaN(latLng[1]) && !isNaN(latLng[0])) {
                            coordinates.push({
                                lat: parseFloat(latLng[1]),
                                lng: parseFloat(latLng[0])
                            });
                        }
                    });
                    var map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: parseFloat(coordinates[0]['lat']),
                            lng: parseFloat(coordinates[0]['lng'])
                        },
                        zoom: 10
                    });
                }
                for (var i = 0; i < placemarks.length; i++) {
                    var placemark = placemarks[i];
                    var name = placemark.querySelector('name') ? placemark.querySelector('name').textContent : '';
                    var description = placemark.querySelector('description') ? placemark.querySelector('description')
                        .textContent : '';
                    var styleUrl = placemark.querySelector('styleUrl').textContent;
                    console.log(name, styleUrl);
                    var coordinates = [];
                    // Create markers, polygons, or polylines based on geometry type
                    if (placemark.querySelector('Point')) {
                        coordinates = placemark.querySelector('Point coordinates').textContent.split(',');
                        if (coordinates.length > 0) {
                            var icon = styles[styleUrl.substr(1)].querySelectorAll('IconStyle href')[0].textContent;
                            var marker = new google.maps.Marker({
                                position: {
                                    lat: parseFloat(coordinates[1]),
                                    lng: parseFloat(coordinates[0])
                                },
                                map: map,
                                title: name,
                                content: description,
                                icon: icon
                            });
                            var infowindow = new google.maps.InfoWindow({
                                content: description
                            });
                            marker.addListener('click', function() {
                                infowindow.open(map, marker);
                            });
                        }
                    } else if (placemark.querySelector('Polygon')) {
                        var coords = placemark.querySelectorAll('Polygon coordinates')[0].textContent;
                        coords = coords.split('\n              ').map(function(coord) {
                            var latLng = coord.split(',');
                            if (!isNaN(latLng[1]) && !isNaN(latLng[0])) {
                                coordinates.push({
                                    lat: parseFloat(latLng[1]),
                                    lng: parseFloat(latLng[0])
                                });
                            }
                        });
                        if (coordinates.length > 0) {
                            var color = styles[styleUrl.substr(1)].querySelectorAll('PolyStyle color')[0]
                                .textContent;
                            var width = styles[styleUrl.substr(1)].querySelectorAll('LineStyle width')[0]
                                .textContent;
                            var lineColor = styles[styleUrl.substr(1)].querySelectorAll('LineStyle color')[0]
                                .textContent;
                            var fill = styles[styleUrl.substr(1)].querySelectorAll('PolyStyle fill')[0].textContent;
                            var outline = styles[styleUrl.substr(1)].querySelectorAll('PolyStyle outline')[0]
                                .textContent;
                            var polygon = new google.maps.Polygon({
                                paths: coordinates,
                                map: map,
                                title: name,
                                description: description,
                                strokeColor: '#' + lineColor,
                                strokeWeight: width, // Customize polygon fill color
                                strokeOpacity: outline,
                                fillColor: '#' + color,
                                fillOpacity: fill
                            });
                        }
                    } else if (placemark.querySelector('LineString')) {
                        var coords = placemark.querySelectorAll('LineString coordinates')[0].textContent;
                        coords = coords.split(' ').map(function(coord) {
                            var latLng = coord.split(',');
                            if (!isNaN(latLng[1]) && !isNaN(latLng[0])) {
                                coordinates.push({
                                    lat: parseFloat(latLng[1]),
                                    lng: parseFloat(latLng[0])
                                });
                            }
                        });
                        if (coordinates.length > 0) {
                            var color = styles[styleUrl.substr(1)].querySelectorAll('LineStyle color')[0]
                                .textContent;
                            var width = styles[styleUrl.substr(1)].querySelectorAll('LineStyle width')[0]
                                .textContent;
                            var polyline = new google.maps.Polyline({
                                path: coordinates,
                                map: map,
                                title: name,
                                description: description,
                                strokeColor: '#' + color,
                                strokeWeight: width
                            });
                        }
                    }
                }
            @else
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: 40.758874,
                        lng: -73.984939
                    },
                    zoom: 12
                });
            @endif
        }
    </script>
@endsection
