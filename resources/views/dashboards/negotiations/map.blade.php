@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Unlisted Dealer Position</h1>
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
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        Unlisted Dealer Position
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="gmap" style="height: 70vh;" class="gmaps"> </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>

            </div>
        </div>
    </div>
    <script>
        var visit = <?php echo $visit; ?>;
        console.log(visit);

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

            var markerIcon = "{{ asset('assets/pages/img/map.png') }}";

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(visit.latitude, visit.longitude),
                icon: markerIcon,
                map: map
            });
            var infowindow = new google.maps.InfoWindow({});
            infowindow.setContent(visit.dealer_name);
            infowindow.open(map, marker);
        }
    </script>
    <script async defer
        src="https://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
@endsection
