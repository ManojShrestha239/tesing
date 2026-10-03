<!DOCTYPE html>
<html>
<!-- BEGIN HEAD -->

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

        /*$('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });*/
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #gmap {
            height: 70vh;
        }

        .button {
            width: 80% !important;
            background-color: #f48220;
            border: 2px solid #f48220;
        }

        .button:hover {
            transition: 0.3s;
            background-color: #f48220;
            border: 2px solid #f48220;
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
                <!-- BEGIN BREADCRUMBS -->
                <div class="breadcrumbs">
                    <h1>User Positions</h1>
                    <ol class="breadcrumb">
                        <!--                            <li>
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#">Features</a>
                            </li>
                            <li class="active">Maps</li>-->
                    </ol>
                    <!-- Sidebar Toggle Button -->
                    {{-- <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="toggle-icon">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
                        </button> --}}
                    <!-- Sidebar Toggle Button -->
                </div>
                <!-- END BREADCRUMBS -->
                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <div class="page-content-container">
                    <form method="post" class="page-content-container" action="/dashboard/repmap">
                        @csrf
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="control-label bolds">Role of officer<span
                                            class="required">*</span></label>

                                    <select class="form-control" name="role" id="role">
                                        @foreach ($rol as $ro)
                                            <option value="{{ $ro->id }}">{{ $ro->role }}</option>
                                        @endforeach
                                    </select>
                                    <span class="field-validation-valid" data-valmsg-for="resCountry"
                                        data-valmsg-replace="true"></span>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="control-label bolds">Date<span
                                            class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                    <div class="date" data-provide="datepicker" class="form-control">
                                        <input type="text" id="nepaliDateFa" autocomplete="off" name="date"
                                            placeholder="YYYY-MM-DD" class="form-control nepali-calendar">

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
                                    <button name="view" class="btn btn-primary button"
                                        style="margin-top:24px;width: 100%;">View</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="page-content-row" id="replecable">
                        <!-- BEGIN PAGE SIDEBAR -->
                        <div class="page-sidebar">
                            <div class="portlet light portlet-fit">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <div style="margin-top:0;margin-left:0px;margin-bottom:10px;">
                                            <b>Date:</b>&nbsp;{{ $date }}
                                        </div>
                                        <!--<i class=" icon-users font-red"></i>-->
                                        <!--<span class="caption-subject font-red bold uppercase">Active Users</span>-->
                                    </div>

                                </div>
                                <nav class="navbar" role="navigation">
                                    <!-- Brand and toggle get grouped for better mobile display -->
                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <ul class="nav navbar-nav margin-bottom-35">
                                        <i class=" icon-users font-red"></i>
                                        <span class="caption-subject font-red bold uppercase margin-bottom-5">Active
                                            Users</span>
                                        @if (count($users) == 0)
                                            <li>
                                                <div class="alert alert-info">No Marketing Officers are Active</div>
                                            </li>
                                        @else
                                            <!--<li style="margin-top:0;margin-left:55px;margin-bottom:30px;">-->
                                            <!--    <b>Date:</b>&nbsp;{{ $date }}-->
                                            <!--</li>-->
                                            @foreach ($users as $use)
                                                <li>
                                                    <a
                                                        href="/dashboard/testme/{{ $use->user['id'] }}/{{ $date }}">
                                                        {{ $use->user['name'] }} </a>
                                                </li>
                                            @endforeach

                                        @endif
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




                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN BASIC PORTLET-->
                                    <div class="portlet light portlet-fit bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class=" icon-map font-red"></i>
                                                <span class="caption-subject font-red bold uppercase">Latest Position of
                                                    Users</span>
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div id="gmap" class="gmaps"> </div>
                                        </div>
                                    </div>
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
    <script type="text/javascript">
        var map;

        var locations = <?php echo $currentLoc; ?>;

        function drawMap() {
            var centerMap = new google.maps.LatLng(27.679345, 84.410567);

            var myOptions = {
                zoom: 7,
                center: centerMap,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                fullscreenControl: false
            }
            map = new google.maps.Map(document.getElementById("gmap"), myOptions);

            setMarkers(locations);




        }

        function setMarkers(locations) {
            for (i = 0; i < locations.length; i++) {
                var location = locations[i];
                plotMarker(location)
            }
        }

        function plotMarker(location) {

            var markerIcon = (location.is_exact) ? 'map.png' : 'map.png';
            var markerIcon = "{{ asset('assets/pages/img/map.png') }}"; //+ markerIcon;


            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(location.lat, location.lng),
                icon: markerIcon,
                title: location.name,
                map: map
            });
        }
    </script>
    <script async defer
        src="http://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
    <script>
        $(document).ready(function() {

            $('#nepaliDateF').nepaliDatePicker({
                ndpEnglishInput: 'englishDateF'
            });

            $('#nepaliDateFa').nepaliDatePicker({
                ndpEnglishInput: 'englishDateFa'
            });

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
                        // alert(dat);
                        $('#replecable').html(dat);


                    }


                });
            });
        });
    </script>
</body>

</html>
