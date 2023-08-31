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
                if (isKMLValid(kmlContent)) {
                    console.log('Valid KML file.');
                    var map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: {!! $defaultCoordinates['latitude'] !!},
                            lng: {!! $defaultCoordinates['longitude'] !!}
                        },
                        zoom: 11
                    });

                    var kmlLayer = new google.maps.KmlLayer({
                        url: 'data:application/xml;base64,' + btoa(),
                        map: map
                    });
                } else {
                    console.log('Invalid KML file.');
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

        function isKMLValid(kmlContent) {
            try {
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(kmlContent, 'text/xml');
                return xmlDoc.documentElement.nodeName === 'kml';
            } catch (error) {
                return false;
            }
        }
    </script>
@endsection
