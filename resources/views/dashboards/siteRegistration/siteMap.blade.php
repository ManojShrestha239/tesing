@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Site Registration</h1>
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
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Site Registration</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            @include('dashboards.dynamicNav.siteRegistration')
                        </nav>
                    </div>
                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="m-heading-1 border-green m-bordered">
                            <div class="page-content-col">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <b>{{ session('Status') }}</b>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-user font-red"></i><span
                                            class="caption-subject font-red bold uppercase"> Site Maps </span>
                                    </div>
                                    <div class="tools"> </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="portlet-body">
                                        <div id="gmap" style="height: 70vh;" class="gmaps">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var sites = @json($values);
        var map;
        var sharedInfoWindow;

        function drawMap() {
            var centerMap = new google.maps.LatLng(27.679345, 84.410567);
            var myOptions = {
                zoom: 12,
                center: centerMap,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                fullscreenControl: false
            };
            map = new google.maps.Map(document.getElementById("gmap"), myOptions);
            sharedInfoWindow = new google.maps.InfoWindow();

            var userMarker = "{{ asset('assets/pages/img/map.png') }}";
            sites.forEach(function(site) {
                addMarker(site, userMarker);
            });
        }

        function addMarker(site, userMarker) {
            var position = new google.maps.LatLng(site.latitude, site.longitude);
            var marker = new google.maps.Marker({
                position: position,
                icon: userMarker,
                map: map
            });
            var infoContent = site.house_hold_name + "<br/> Submitted By: " + site.submitted_by.name +
                "<br/> Submitted At: " + site.date;
            marker.addListener('click', function() {
                sharedInfoWindow.setContent(infoContent);
                sharedInfoWindow.open(map, marker);
            });
        }
    </script>
    <script async defer
        src="https://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
@endsection
