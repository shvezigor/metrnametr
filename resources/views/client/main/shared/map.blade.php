<section class="map-box">

    <div class="background">
        <div class="layer" style="background: #f5f5f5;"></div>
    </div>

    <div class="container-fluid">
        <div class="row">

            <div class="col-xs-12">
                <div class="small-title black">Контакти</div>
            </div>
        </div>

        <div class="row wrap-maps">
            <div class="col-xs-12 map-canvas" id="map-canvas"
                 data-map-latitude="50.754183"
                 data-map-longitude="25.3356367"
                 data-cursor-latitude="50.754183"
                 data-cursor-longitude="25.3416367">
                @unless(config('common.google.map.key'))
                    <div class="map-fallback">
                        <strong>{{ \App\Models\Setting::getValue('address') }}</strong>
                        <span>{{ \App\Models\Setting::getValue('phones') }}</span>
                    </div>
                @endunless
            </div>

            <div class="col-xs-12 wrap-cont-box">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-1">

                            <div class="cont-box">
                                <div class="addr">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    {{ \App\Models\Setting::getValue('address') }}
                                </div>
                                <div class="num">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    @foreach(explode(',', \App\Models\Setting::getValue('phones')) as $item)
                                        <span>{{ $item }}</span>
                                    @endforeach
                                </div>
                                <div class="mail">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                    {{ \App\Models\Setting::getValue('email') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>

@if(config('common.google.map.key'))
    @section('scripts-for-map')
    <script>

        // Create the script tag, set the appropriate attributes
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("common.google.map.key") }}&callback=initializeMap';
        script.defer = true;
        script.async = true;

        const LAT = {{ config("common.google.map.lat") }};
        const LNG = {{ config("common.google.map.lng") }};

        window.initializeMap = () => {

            const styles = [
                {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
                {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
                {
                    featureType: 'administrative.locality',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#555555'}]
                },
                {
                    featureType: 'poi',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#555555'}]
                },
                {
                    featureType: 'poi.park',
                    elementType: 'geometry',
                    stylers: [{color: '#263c3f'}]
                },
                {
                    featureType: 'poi.park',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#6b9a76'}]
                },
                {
                    featureType: 'road',
                    elementType: 'geometry',
                    stylers: [{color: '#38414e'}]
                },
                {
                    featureType: 'road',
                    elementType: 'geometry.stroke',
                    stylers: [{color: '#212a37'}]
                },
                {
                    featureType: 'road',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#9ca5b3'}]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'geometry',
                    stylers: [{color: '#746855'}]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'geometry.stroke',
                    stylers: [{color: '#1f2835'}]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#f3d19c'}]
                },
                {
                    featureType: 'transit',
                    elementType: 'geometry',
                    stylers: [{color: '#2f3948'}]
                },
                {
                    featureType: 'transit.station',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#555555'}]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{color: '#17263c'}]
                },
                {
                    featureType: 'water',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#515c6d'}]
                },
                {
                    featureType: 'water',
                    elementType: 'labels.text.stroke',
                    stylers: [{color: '#17263c'}]
                }
            ];

            const center = {
                lat: 50.754183,
                lng: 25.3356367
            };

            const map = new google.maps.Map(
                document.getElementById('map-canvas'), {
                    center: center,
                    zoom: 16,
                    styles: styles,
                    disableDefaultUI: true,
                    mapTypeId:google.maps.MapTypeId.ROADMAP
                });

            const markerPosition = new google.maps.LatLng(LAT, LNG);
            const image = '/images/icons/marker.png';

            const marker = new google.maps.Marker({
                position: markerPosition,
                icon: image,
            });

            marker.setMap(map);
        }

        // Append the 'script' element to 'head'
        document.head.appendChild(script);

    </script>
    @endsection
@endif
