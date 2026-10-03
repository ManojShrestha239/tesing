@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Site Location Details</h1>
            <!-- Sidebar Toggle Button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
            <!-- Sidebar Toggle Button -->
        </div>
        <div class="page-content-container">
            <div class="page-content-row" id="replecable">

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        Site Location Details
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="gmap" style="height: 70vh;" class="gmaps"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var model = @json($model);

        function drawMap() {
            var centerMap = new google.maps.LatLng(27.679345, 84.410567);
            var myOptions = {
                zoom: 12,
                center: centerMap,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                fullscreenControl: false
            }
            map = new google.maps.Map(document.getElementById("gmap"), myOptions);
            var infowindow = new google.maps.InfoWindow();

            var userMarker = "{{ asset('assets/pages/img/map.png') }}";
            var dealerMarker = "{{ asset('assets/pages/img/dealerPin.png') }}";

            var userLatLng = new google.maps.LatLng(model.latitude, model.longitude);
            var marker = new google.maps.Marker({
                position: userLatLng,
                icon: userMarker,
                map: map
            });
            var infowindow = new google.maps.InfoWindow({});
            var userInfoContent = model.house_hold_name + "<br/> Submitted By: " + model.submitted_by.name +
                "<br/> Submitted At: " + model.date;
            infowindow.setContent(userInfoContent);
            infowindow.open(map, marker);
            marker.addListener('click', function() {
                infowindow.setContent(userInfoContent);
                infowindow.open(map, marker);
            });

            if (model.dealer.latitude != null && model.dealer.longitude != null) {
                var dealerLatLng = new google.maps.LatLng(model.dealer.latitude, model.dealer.longitude);
                var dealer = new google.maps.Marker({
                    position: dealerLatLng,
                    icon: {
                        url: dealerMarker,
                        scaledSize: new google.maps.Size(40, 40)
                    },
                    map: map
                });
                var dealerInfoWindow = new google.maps.InfoWindow({});
                var dealerInfoContent = model.dealer.name;
                dealerInfoWindow.setContent(dealerInfoContent);
                dealerInfoWindow.open(map, dealer);
                dealer.addListener('click', function() {
                    dealerInfoWindow.setContent(dealerInfoContent);
                    dealerInfoWindow.open(map, dealer);
                });

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(userLatLng);
                bounds.extend(dealerLatLng);
                map.fitBounds(bounds);
            } else {
                var bounds = new google.maps.LatLngBounds();
                bounds.extend(userLatLng);
                map.fitBounds(bounds);
            }

        }
    </script>
    <script async defer
        src="https://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
@endsection
