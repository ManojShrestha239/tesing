@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

<style>
    #prev {
        height: 10px;
        width: 10px;
        background-image: url("/assets/pages/img/left-arrow.svg");
        margin-top: 3px;
    }

    .ndp-prev:hover {
        background: none;
    }

    #next {
        height: 10px;
        width: 10px;
        background-image: url("/assets/pages/img/right-arrow.svg") !important;
        margin-top: 3px;
    }

    .ndp-next:hover {
        background: none !important;
    }

    .ndp-next {
        right: 7px;
        background: none;
    }

    .bg-red {
        background: #ffafaf !important;
    }

    .bg-green {
        background: #dfffe9 !important;
    }

    .badge-success {
        background-color: #018800 !important;
    }

    .badge-danger {
        background-color: #ff0000 !important;
    }
</style>

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>User Location Records</h1>
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
                    <div class="col-md-8">
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        User Location Records
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="gmap" style="height: 70vh;" class="gmaps"> </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-info-circle font-blue"></i>
                                    <span class="caption-subject font-blue bold uppercase">Device Log Details</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th>GPS Status</th>
                                            <td>
                                                @if (isset($deviceLog->gps_status))
                                                    <span
                                                        class="badge badge-{{ $deviceLog->gps_status ? 'success' : 'danger' }}">
                                                        {{ $deviceLog->gps_status ? 'On' : 'Off' }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>WiFi Status</th>
                                            <td>
                                                @if (isset($deviceLog->wifi_status))
                                                    <span
                                                        class="badge badge-{{ $deviceLog->wifi_status ? 'success' : 'danger' }}">
                                                        {{ $deviceLog->wifi_status ? 'On' : 'Off' }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mobile Data Status</th>
                                            <td>
                                                @if (isset($deviceLog->mobile_data_status))
                                                    <span
                                                        class="badge badge-{{ $deviceLog->mobile_data_status ? 'success' : 'danger' }}">
                                                        {{ $deviceLog->mobile_data_status ? 'On' : 'Off' }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Battery Optimized</th>
                                            <td>
                                                @if (isset($deviceLog->is_battery_optimized))
                                                    <span
                                                        class="badge badge-{{ $deviceLog->is_battery_optimized ? 'success' : 'danger' }}">
                                                        {{ $deviceLog->is_battery_optimized ? 'Optimized' : 'Not Optimized' }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Is GPS Mocked</th>
                                            <td>
                                                @if (isset($deviceLog->is_gps_mocked))
                                                    <span
                                                        class="badge badge-{{ $deviceLog->is_gps_mocked ? 'success' : 'danger' }}">
                                                        {{ $deviceLog->is_gps_mocked ? 'Yes' : 'No' }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
        var deviceLog = @json($deviceLog);
        var mobileuser = @json($mobileuser);

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

            var userLatLng = new google.maps.LatLng(deviceLog.latitude, deviceLog.longitude);

            var marker = new google.maps.Marker({
                position: userLatLng,
                icon: markerIcon,
                map: map
            });
            var infowindow = new google.maps.InfoWindow({});
            var userInfoContent = mobileuser.name + "<br/>" + deviceLog.time;
            infowindow.setContent(userInfoContent);
            infowindow.open(map, marker);
            marker.addListener('click', function() {
                infowindow.setContent(userInfoContent);
                infowindow.open(map, marker);
            });

            var bounds = new google.maps.LatLngBounds();
            bounds.extend(userLatLng);
            map.fitBounds(bounds);
        }
    </script>
    <script async defer
        src="https://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
@endsection
