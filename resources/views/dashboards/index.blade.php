<!DOCTYPE html>
<html>
<!-- BEGIN HEAD -->

<head>
    @include('dashboards.partials.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />

    <script>

        var date = new Date();
        var cYear = date.getFullYear();
        var cMonth = date.getMonth();
        var cDate = date.getDate();

        $(document).ready(function () {
            $("#JoinDate").datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: new Date(cYear, cMonth, cDate)
            });
        });

        /*$('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });*/

    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        #mapCanvas {
            width: 775px;
            height: 500px;
            margin: 0 auto;
        }
        #mapLegend {
            background: #fdfdfd;
            color: #3c4750;
            padding: 0 10px 0 10px;
            margin: 10px;
            font-weight: bold;
            filter: alpha(opacity=80);
            opacity: 0.8;
            border: 2px solid #000;
        }
        #mapLegend div {
            height: 40px;
            line-height: 25px;
            font-size: 1.2em;
        }
        #mapLegend div img {
            float: left;
            margin-right: 10px;
        }
        #mapLegend h2 {
            text-align: center
        }
    </style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo">
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
        <div class="page-content">

            <div class="page-content-col">
                @if(session('Status'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <b>{{ session('Status') }}</b>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right form-inline" style="margin-bottom: 10px;">
                <label for="role"><b>Role </b></label>

                    <select name="role" id="role" class="form-control input-medium">
                        @foreach ($rol as $ro)
                            <option value="{{ $ro->id }}">{{ $ro->role }}</option>
                        @endforeach
                    </select>

                {{--<input class="datepicker" id="JoinDate" data-date-format="mm/dd/yyyy">--}}
                <div class="input-group date" data-provide="datepicker">
                    <input type="text" id="nepaliDateFa" name="date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
{{--                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>--}}
                </div>

                {{--<input type="text" name="end_date" id="end_date" class="filter-textfields" placeholder="End Date"/>--}}

{{--                <label for="date" id="history"><b> Date </b></label>
                    <input type="date" class="form-control input-medium">--}}

                <button id="view" class="btn btn-primary input-sm">View</button>
            </div>

            <script>
                $(document).ready(function(){

                    $('#nepaliDateF').nepaliDatePicker({
                        ndpEnglishInput: 'englishDateF'
                    });

                    $('#nepaliDateFa').nepaliDatePicker({
                        ndpEnglishInput: 'englishDateFa'
                    });

                    $(document).on("click", "#view", function(){
                        var options;
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            type: "POST",
                            url: "{!! URL::to('dashboard/testme') !!}",
                            data: {
                                role : $("#role").val(),
                                date : $("#JoinDate").val()
                            },
                            success: function (dat)
                            {
                                // alert(dat);
                            $('#customdiv').html(dat);
                            }
                        });
                    });
                });
            </script>

            {{--@include('dashboards.partials.side-menu')--}}

            <div>
                <div class="col-lg-2 col-md-2 hidden-sm hidden-xs pull-left" id="customdiv">
                    <b>User List</b>
                    <br>

                        {{--<a href="/dashboard/{id}/visithistory"></a>--}}
                    @foreach($users as $use)
                            <br> <a href="/dashboard/testme/{{ $use->user['id'] }}"> {{ $use->user['name'] }}</a>
                    @endforeach

                </div>

                <div id="mapContainer">
                    <div id="mapCanvas"></div>
                    {{--<div id="map" class="google-maps col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right" style="height: 450px;"></div>--}}
                    <script type="text/javascript">
                        var map;
                        {{--var locatio = {{ json_decode($currentLoc) }};--}}
                        var locations = <?php echo $currentLoc; ?>;
                        console.log(locations);
                        function drawMap() {
                            var centerMap = new google.maps.LatLng(27.679345, 84.410567);

                            var myOptions = {
                                zoom: 12,
                                center: centerMap,
                                mapTypeId: google.maps.MapTypeId.ROADMAP,
                                mapTypeControl: true,
                                fullscreenControl: false
                            }
                            map = new google.maps.Map(document.getElementById("mapCanvas"), myOptions);

                            setMarkers(locations);
                        }
                        function setMarkers(locations) {
                            for (i=0; i < locations.length; i++) {
                                var location = locations[i];
                                plotMarker(location)
                            }
                        }

                        function plotMarker(location) {

                            var markerIcon = (location.is_exact) ? 'map.png' : 'map.png';
                            var markerIcon = '{{{ asset('assets/pages/img/map.png') }}}'; //+ markerIcon;


                            var marker = new google.maps.Marker({
                                position: new google.maps.LatLng(location.lat,  location.lng),
                                icon: markerIcon,
                                title: location.name,
                                map: map
                            });
                        }

                    </script>
                    <script async defer src="http://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>


            {{--</div>--}}
        </div>
        </div>

    </div>

</div>
<!-- BEGIN FOOTER -->
{{--@include('dashboards.partials.footer')--}}
<!-- END FOOTER -->
<!-- END CONTAINER -->
<!-- BEGIN QUICK SIDEBAR -->
@include('dashboards.partials.quick-sidebar')
<!-- END QUICK SIDEBAR -->
@include('dashboards.partials.js')
{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=initMap"--}}
        {{--async defer></script>--}}
</div>
</body>

</html>
