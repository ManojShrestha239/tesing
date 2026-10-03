<!DOCTYPE html>
<html>

<head>
    @include('dashboards.partials.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css"
        rel="stylesheet">
    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script>

    <script>
        var date = new Date();
        var cYear = date.getFullYear();
        var cMonth = date.getMonth();
        var cDate = date.getDate();

        $(document).ready(function() {
            $("#JoinDate").datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: new Date(cYear, cMonth, cDate)
            });
        });
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #gmap {
            height: 500px;
        }
    </style>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&v=3"></script>
    <script type="text/javascript">
        var MapPoints = '<?php echo $currentLoc; ?>';

        var dealers = <?php echo $dealers; ?>;
        var MY_MAPTYPE_ID = 'custom_style';
        var directionsDisplay;
        var newd = [];
        var directionsService = new google.maps.DirectionsService();
        var map;
        var visits = <?php echo $visits; ?>;
        let dealermarker = '';
        var nlocations = jQuery.parseJSON(MapPoints);

        function initialize() {
            directionsDisplay = new google.maps.DirectionsRenderer({
                suppressMarkers: true
            });
            if (jQuery('#gmap').length > 0) {

                var locations = jQuery.parseJSON(MapPoints);

                if (location.length == 0) {

                } else {

                    var myOption = {
                        zoom: 12,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        scrollwheel: false
                    }

                    map = new google.maps.Map(document.getElementById('gmap'), myOption);
                    directionsDisplay.setMap(map);

                    var infowindow = new google.maps.InfoWindow();
                    var infowindows = new google.maps.InfoWindow();
                    var flightPlanCoordinates = [];
                    var bounds = new google.maps.LatLngBounds();

                    for (i = 0; i < locations.length; i++) {
                        if (i == 0) {
                            var markerIcon = {
                                url: '{{ asset('assets/pages/img/blue.png') }}',
                                scaledSize: new google.maps.Size(30, 30)
                            };

                        } else if (locations.length == i + 1) {
                            var markerIcon = {
                                url: '{{ asset('assets/pages/img/red.png') }}',
                                scaledSize: new google.maps.Size(30, 30)
                            };
                        } else {
                            var markerIcon = {
                                url: '{{ asset('assets/pages/img/marker_ico.png') }}',
                                scaledSize: new google.maps.Size(10, 10)
                            };
                        }

                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locations[i].address.lat, locations[i].address.lng),
                            icon: markerIcon,
                            map: map
                        });
                        flightPlanCoordinates.push(marker.getPosition());

                        bounds.extend(marker.position);


                        google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                                infowindow.setContent(locations[i].title);
                                infowindow.open(map, marker);
                            }
                        })(marker, i));

                    }
                    const mo = document.getElementById("mo");

                    mo.addEventListener('click', function() {
                        if (mo.checked) {
                            clickcheckboc(nlocations);
                        } else {
                            uncheck(nlocations);
                        }
                    });

                    function clickcheckboc(name) {

                        for (let k = 0; k < name.length; k++) {
                            if (k == 0) {
                                let markerIcon = {
                                    url: '{{ asset('assets/pages/img/blue.png') }}',
                                    scaledSize: new google.maps.Size(30, 30)
                                };
                            } else if (name.length == k + 1) {
                                let markerIcon = {
                                    url: '{{ asset('assets/pages/img/red.png') }}',
                                    scaledSize: new google.maps.Size(30, 30)
                                };
                            } else {
                                let markerIcon = {
                                    url: '{{ asset('assets/pages/img/marker_ico.png') }}',
                                    scaledSize: new google.maps.Size(10, 10)
                                };
                            }

                            marke = new google.maps.Marker({
                                position: new google.maps.LatLng(name[k].address.lat, name[k].address.lng),
                                icon: markerIcon,
                                map: map
                            });
                            let infowindow = new google.maps.InfoWindow({});
                            infowindow.setContent(name[k].title);
                            infowindow.open(map, marke);
                        }
                    }

                    function uncheck(name) {
                        initialize();
                    }
                    for (i = 0; i < dealers.length; i++) {
                        for (let j = 0; j < visits.length; j++) {
                            if (visits[j].dealer_code == dealers[i].code) {
                                dealermarker = {
                                    url: '{{ asset('assets/pages/img/placeholder (5).png') }}',
                                    scaledSize: new google.maps.Size(50, 50)
                                };
                            }
                        }
                        var marker = new google.maps.Marker({

                            position: new google.maps.LatLng(dealers[i].latitude, dealers[i].longitude),
                            icon: (dealermarker != "" ? dealermarker : ''),
                            title: location.name,
                            map: map
                        });

                        if (dealers[i].latitude === null) {

                        } else {
                            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                    infowindows.setContent(dealers[i].name);
                                    infowindows.open(map, marker);
                                }
                            })(marker, i));
                        }
                        dealermarker = '';
                    }


                    map.fitBounds(bounds);
                    var start = flightPlanCoordinates[0];
                    var end = flightPlanCoordinates[flightPlanCoordinates.length - 1];
                    var waypts = [];
                    for (var i = 1; i < flightPlanCoordinates.length - 1; i++) {
                        waypts.push({
                            location: flightPlanCoordinates[i],
                            stopover: true
                        });
                    }
                    calcRoute(start, end, waypts);
                }
            }
        }

        function calcRoute(start, end, waypts) {
            var request = {
                origin: start,
                destination: end,
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                    var route = response.routes[0];
                    var summaryPanel = document.getElementById('directions_panel');
                    summaryPanel.innerHTML = '';
                    for (var i = 0; i < route.legs.length; i++) {
                        var routeSegment = i + 1;
                        summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment + '</b><br>';
                        summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                        summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                    }
                }
            });
        }

        function goBack() {
            window.history.back();
        }
    </script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo" onload="initialize()">
    <!-- BEGIN CONTAINER -->
    <div class="wrapper">
        <!-- BEGIN HEADER -->
        <header class="page-header">
            <nav class="navbar mega-menu" role="navigation">
                @include('dashboards.partials.nav')
            </nav>
        </header>
        <!-- END HEADER -->
        <div class="container-fluid">
            <div class="page-content" id="replecable">
                <!-- BEGIN BREADCRUMBS -->
                <div class="breadcrumbs">
                    <h1 onclick="goBack()" style="cursor: pointer;width:auto;"><img
                            style="float:left;margin-top:3px;width:25px; height:25px;"
                            src="{{ asset('assets/global/img/left-arrow-key.svg') }}">MO Positions</h1>

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
                <!-- END BREADCRUMBS -->
                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <div class="page-content-container">
                    <div class="page-content-row">
                        <!-- BEGIN PAGE SIDEBAR -->
                        <div class="page-sidebar">
                            <div class="portlet light portlet-fit">
                                <div class="portlet-title">
                                    <div class="caption">

                                        <span class="caption-subject font-red bold uppercase">DATE :
                                            {{ $date }}</span>
                                    </div>

                                </div>
                                <nav class="navbar" role="navigation">
                                    <!-- Brand and toggle get grouped for better mobile display -->
                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <ul class="nav navbar-nav">
                                        <div class=" bold caption">

                                            <i class=" icon-users font-red"></i>
                                            MO DETAILS

                                        </div>

                                        @if (!empty($userinfo))

                                            <li>
                                                <b>Name: </b>{{ $userinfo->name }}
                                            </li>
                                            <li>
                                                <b>Email: </b>{{ $userinfo->email }}
                                            </li>
                                            <li>
                                                <b>Contact: </b>{{ $userinfo->phone }}
                                            </li>

                                            @foreach ($areas as $area)
                                                <li>
                                                    <b> Area of Marketing
                                                        Officer:</b>{{ implode(', ', $area['areas']) }}
                                                </li>
                                            @endforeach
                                        @else
                                            <li>
                                                <div class="alert alert-info">No Details available</div>
                                            </li>
                                        @endif
                                    </ul>
                                    <h3>VISIT HISTORY</h3>
                                    <ul class="nav navbar-nav" style="height: 450px;overflow-y: auto;">

                                        @foreach ($visits as $visit)
                                            <li style="padding: 8px;border-bottom: 2px solid #d5c4c4de;border-radius: 5%;"
                                                class="@if (strtolower($visit->status) == 'online') bg-green @else bg-red @endif">
                                                <label> <strong>Dealer:</strong> {{ $visit->dealer_code }} -
                                                    {{ $visit->dealer_name ? ucwords($visit->dealer_name) : $visit->dealer->name }}</label>
                                                <label> <strong> Visit Time:</strong> {{ $visit->start_time }} To
                                                    {{ $visit->end_time }}</label>
                                                <label> <strong>Visit Purpose:</strong>
                                                    @foreach ($visit->visits as $purpose)
                                                        {{ $purpose->purposes->purpose }},
                                                    @endforeach
                                                </label>
                                                <label> <strong>Visit Remarks:</strong> {{ $visit->remarks }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </nav>
                            </div>

                        </div>
                        <!-- END PAGE SIDEBAR -->
                        <div class="page-content-col">
                            <!-- BEGIN PAGE BASE CONTENT -->


                            @if (session('Status'))
                                <div class="m-heading-1 border-green m-bordered">
                                    <div class="page-content-col">
                                        <div class="alert alert-success alert-dismissible" role="alert">
                                            <b>{{ session('Status') }}</b>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="get" action="/userposition/{{ $userinfo->id }}">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Role of officer<span
                                                    class="required">*</span></label>

                                            <select class="form-control" name="role" id="role" required
                                                disabled>
                                                @foreach ($rol as $ro)
                                                    <option value="{{ $ro->id }}">{{ $ro->role }}</option>
                                                @endforeach
                                            </select>

                                            <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                data-valmsg-replace="true"></span>
                                            @if ($errors->has('role'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('role') }}</strong>
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Date<span
                                                    class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                            <div class="date" data-provide="datepicker" class="form-control">
                                                <input type="text" id="nepaliDateFa" autocomplete="off"
                                                    name="date" placeholder="YYYY-MM-DD"
                                                    value="{{ old('date', $date ?? '') }}"
                                                    class="form-control nepali-calendar" required>
                                            </div>
                                            <span class="field-validation-valid" data-valmsg-for="education"
                                                data-valmsg-replace="true"></span>
                                            @if ($errors->has('date'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('date') }}</strong>
                                                </span>
                                            @endif
                                            @if (session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button class="btn btn-primary"
                                                style="margin-top:24px;width: 100%;">View</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <style>
                                .span-info {
                                    font-size: 17px;
                                }
                            </style>

                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN BASIC PORTLET-->
                                    <div class="portlet light portlet-fit bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class=" icon-map font-red"></i>
                                                <span class="caption-subject font-red bold uppercase">Path Positions of
                                                    MO</span>
                                                <div>
                                                    <ul
                                                        style="list-style: none;padding: 0; margin-top:16px;font-size:15px;color:#262626;">
                                                        <li>
                                                            Checkin Time : @if ($attendance)
                                                                {{ $attendance->checkin_date }}
                                                            @endif
                                                        </li>
                                                        <li>
                                                            Checkin Location : {{ $checkinaddress }}
                                                        </li>
                                                        <li>
                                                            Last Synced On : {{ $last_synced_on }}
                                                        </li>
                                                        <li>
                                                            Last Synced Location : {{ $lastlocation }}
                                                        </li>
                                                        <li>
                                                            Odometer Distance :
                                                            @if ($attendance)
                                                                {{ $attendance->totalkm_odometer < 0 ? '0' : round($attendance->totalkm_odometer, 2) }}
                                                                KM
                                                            @endif
                                                            <i class="fa fa-info-circle text-muted"
                                                                style="cursor: pointer;" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Calculated from odometer readings entered at check-in and check-out. Shows 0 KM if the user hasn't completed both check-in and check-out."></i>
                                                        </li>
                                                        <li>
                                                            GPS Distance :
                                                            @if ($attendance)
                                                                {{ $attendance->totalkm_gps < 0 ? '0' : round($attendance->totalkm_gps, 2) }}
                                                                KM
                                                            @endif
                                                            <i class="fa fa-info-circle text-muted"
                                                                style="cursor: pointer;" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Calculated from GPS location points recorded as the user travels."></i>
                                                        </li>
                                                        <li>
                                                            Device Model : @if ($attendance)
                                                                {{ $attendance->device_model ?? 'N/A' }}
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="caption" style="float: right;">
                                                <div>
                                                    <span> <img src="{{ asset('assets/pages/img/map-marker.png') }}"
                                                            height="30px" width="20px" alt="">
                                                        Dealer</span>
                                                </div>
                                                <div>
                                                    <span> <img src="{{ asset('assets/pages/img/blue.png') }}"
                                                            height="10px" width="10px" alt=""> Start
                                                        Point</span>
                                                </div>
                                                <div>
                                                    <span> <img src="{{ asset('assets/pages/img/red.png') }}"
                                                            height="10px" width="10px" alt=""> End
                                                        Point</span>
                                                </div>
                                                <div>
                                                    <span> <input type="checkbox" name="mo"
                                                            id="mo"><span>Show/Hide MO's Positions</span>
                                                </div>
                                                <div>
                                                    <!-- <img src="{{ asset('assets/pages/img/map.png') }}" height="25px" width="20px" alt=""> <span>Marketing Officer</span> -->
                                                </div>
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div id="gmap" class="gmaps"> </div>
                                        </div>
                                    </div>
                                    @if (isset($deviceLogs) && !empty($deviceLogs))
                                        @php
                                            $page = request()->get('device_page', 1);
                                            $perPage = 10;
                                            usort($deviceLogs, function ($a, $b) {
                                                $timeA = strtotime($a['time'] ?? ($a['logged_at'] ?? ''));
                                                $timeB = strtotime($b['time'] ?? ($b['logged_at'] ?? ''));
                                                return $timeB <=> $timeA;
                                            });
                                            $total = count($deviceLogs);
                                            $deviceLogsPage = array_slice(
                                                $deviceLogs,
                                                ($page - 1) * $perPage,
                                                $perPage,
                                            );
                                            $lastPage = ceil($total / $perPage);
                                        @endphp
                                        <div class="card"
                                            style="margin: 24px 0; box-shadow: 0 2px 8px #eee; border-radius: 8px;">
                                            <div class="card-header"
                                                style="background: #f7f7f7; padding: 16px; border-bottom: 1px solid #eee;">
                                                <h4 style="margin: 0; font-weight: 600; color: #333;">User Device Logs
                                                </h4>
                                                <span style="font-size: 13px; color: #888;">Showing
                                                    {{ ($page - 1) * $perPage + 1 }} -
                                                    {{ min($page * $perPage, $total) }} of
                                                    {{ $total }}</span>
                                            </div>
                                            <div class="card-body" style="padding: 0;">
                                                <table class="table" style="margin-bottom: 0;">
                                                    <thead style="background: #fafafa;">
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">#</th>
                                                            <th class="text-center">Wifi</th>
                                                            <th class="text-center">GPS</th>
                                                            <th class="text-center">Mobile Data</th>
                                                            <th class="text-center">Battery</th>
                                                            <th class="text-center">Location</th>
                                                            <th class="text-center">Is GPS Faked</th>
                                                            <th class="text-center">Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($deviceLogsPage as $idx => $deviceLog)
                                                            <tr>
                                                                <td class="text-center">
                                                                    {{ ($page - 1) * $perPage + $idx + 1 }}</td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-{{ $deviceLog['wifi_status'] ? 'success' : 'danger' }}">
                                                                        <strong>{{ $deviceLog['wifi_status'] ? 'On' : 'Off' }}</strong>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-{{ $deviceLog['gps_status'] ? 'success' : 'danger' }}">
                                                                        <strong>{{ $deviceLog['gps_status'] ? 'On' : 'Off' }}</strong>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-{{ $deviceLog['mobile_data_status'] ? 'success' : 'danger' }}">
                                                                        <strong>{{ $deviceLog['mobile_data_status'] ? 'On' : 'Off' }}</strong>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge badge-{{ $deviceLog['is_battery_optimized'] ? 'success' : 'danger' }}">
                                                                        <strong>{{ $deviceLog['is_battery_optimized'] ? 'Optimized' : 'Not Optimized' }}</strong>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (!empty($deviceLog['latitude']) && !empty($deviceLog['longitude']))
                                                                        <form
                                                                            action="{{ route('user.location', ['id' => $userinfo->id]) }}"
                                                                            method="post" target="_blank">
                                                                            @csrf
                                                                            <input type="hidden" name="deviceLog"
                                                                                value="{{ json_encode($deviceLog) }}">
                                                                            <input type="hidden" name="lat"
                                                                                value="{{ $deviceLog['latitude'] }}">
                                                                            <input type="hidden" name="lng"
                                                                                value="{{ $deviceLog['longitude'] }}">
                                                                            <input type="hidden" name="time"
                                                                                value="{{ $deviceLog['time'] }}">
                                                                            <button type="submit"
                                                                                class="btn btn-primary btn-sm">View
                                                                                on Map</button>
                                                                        </form>
                                                                    @else
                                                                        <span class="text-muted">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $isMocked = $deviceLog['is_gps_mocked'] ?? 0;
                                                                    @endphp

                                                                    <span
                                                                        class="badge badge-{{ $isMocked == 1 ? 'success' : 'danger' }}">
                                                                        <strong>{{ $isMocked == 1 ? 'Yes' : 'No' }}</strong>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ $deviceLog['time'] ?? ($deviceLog['logged_at'] ?? 'N/A') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div
                                                    style="padding: 12px 16px; background: #f7f7f7; border-top: 1px solid #eee;">
                                                    <nav aria-label="Device log pagination">
                                                        <ul class="pagination pagination-sm mb-0"
                                                            style="justify-content: flex-end;">
                                                            @for ($i = 1; $i <= $lastPage; $i++)
                                                                <li
                                                                    class="page-item {{ $i == $page ? 'active' : '' }}">
                                                                    <a class="page-link"
                                                                        href="{{ request()->fullUrlWithQuery(['date' => $date, 'device_page' => $i]) }}">{{ $i }}</a>
                                                                </li>
                                                            @endfor
                                                        </ul>
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- END BASIC PORTLET-->
                                </div>

                            </div>

                            <!-- END PAGE BASE CONTENT -->
                        </div>
                    </div>
                </div>
                <!-- END SIDEBAR CONTENT LAYOUT -->
            </div>

        </div>
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN QUICK NAV -->


    <!-- END QUICK NAV -->
    <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script>
<script src="../assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    @include('dashboards.partials.js')

    <!-- <script>
        $(document).ready(function() {
            $(document).on("click", "#view", function() {
                var options;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{!! URL::to('dashboard/testme') !!}",
                    data: {
                        role: $("#role").val(),
                        date: $("#nepaliDateFa").val()
                    },
                    success: function(dat) {
                        $('#replecable').html(dat);
                    }
                });
            });
        });
    </script> -->
    <script>
        $(document).ready(function() {

            $('#nepaliDateF').nepaliDatePicker({
                ndpEnglishInput: 'englishDateF'
            });

            $('#nepaliDateFa').nepaliDatePicker({
                ndpEnglishInput: 'englishDateFa'
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>

</html>
