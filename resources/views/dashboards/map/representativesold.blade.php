<!DOCTYPE html>
<html>
<!-- BEGIN HEAD -->

<head>
    @include('dashboards.partials.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
            background-color: #dd3234;
            border: 2px solid #dd3234;
        }

        .button:hover {
            transition: 0.3s;
            background-color: #dd3234;
            border: 2px solid #dd3234;
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

        .gm-style .gm-style-iw {
            font-size: 11px;
        }

        .gm-style .gm-style-iw-c {
            padding: 16px 6px 0px 6px;
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
                    <form method="get" class="page-content-container" action="/activeuser">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="control-label bolds">Role of officer<span
                                            class="required">*</span></label>

                                    <select class="form-control" name="role" id="role" required>
                                        <option value=""> Select Role </option>
                                        @foreach ($rol as $ro)
                                            <option
                                                @if (isset($role)) @if ($role == $ro->id) selected @endif
                                            @else @if (old('role') == $ro->id) selected @endif @endif value="{{ $ro->id }}">{{ $ro->role }}</option>
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
                                            placeholder="YYYY-MM-DD"
                                            value="@if (isset($date)) {{ $date }} @else @if (old('date')) {{ old('date') }} @endif @endif"
                                            class="form-control nepali-calendar">

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
                                    <button class="btn btn-primary button"
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
                                        <i class=" icon-users font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Active Users</span>
                                    </div>
                                    <input type="text" id="myInput" onkeyup="searchFunction()"
                                        placeholder="Search for names.." class="form-control" title="Type in a name">

                                </div>
                                <nav class="navbar" role="navigation">
                                    <!-- Brand and toggle get grouped for better mobile display -->
                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <ul class="nav navbar-nav margin-bottom-35" id="myUL"
                                        style="max-height:520px;overflow-y:auto;">
                                        @if (empty($users))
                                            <li>
                                                <div class="alert alert-info">No Marketing Officers are Active</div>
                                            </li>
                                        @else
                                            @foreach ($users as $use)
                                                <li>
                                                    <a
                                                        href="/userposition/{{ $use->user['id'] }}/{{ $date }}">
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


                            <style>
                                .span-info {
                                    font-size: 17px;
                                }
                            </style>

                            <div class="portlet light portlet-fit bordered">
                                <div class="row">
                                    <div class="col-md-12">


                                        <section class="home-section">
                                            <div class="home-content">
                                                @canany(['products', 'order', 'collection', 'reports'])
                                                    <div class="overview-boxes">
                                                        @can('product')
                                                            <div class="box">
                                                                <div class="right-side">
                                                                    <div class="box-topic">TOTAL PRODUCTS</div>
                                                                    <div class="number">{{ $total_product }}</div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                        @can('order')
                                                            <div class="box">
                                                                <div class="right-side">
                                                                    <div class="box-topic">TOTAL ORDER</div>
                                                                    <div class="number">
                                                                        RS.{{ number_format($total_order, 2, '.', ',') }}</div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                        @can('collection')
                                                            <div class="box">
                                                                <div class="right-side">
                                                                    <div class="box-topic">TOTAL COLLECTION</div>
                                                                    <div class="number">
                                                                        RS.{{ number_format($collection, 2, '.', ',') }}</div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                        @can('reports')
                                                            <div class="box">
                                                                <div class="right-side">
                                                                    <div class="box-topic">TOTAL VISIT</div>
                                                                    <div class="number">{{ $total_visit }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="box">
                                                                <div class="right-side">
                                                                    <div class="box-topic">TOTAL UNLISTED</div>
                                                                    <div class="number">{{ $unlisted }}</div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                @endcanany

                                                <div class="sales-boxes">
                                                    @canany(['reports', 'order', 'collection'])
                                                        <div class="portlet light portlet-fit bordered"
                                                            style="width: 80%">
                                                        @else
                                                            <div class="portlet light portlet-fit bordered"
                                                                style="width: 100%">
                                                            @endcanany
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class=" icon-map font-red"></i>
                                                                    <span
                                                                        class="caption-subject font-red bold uppercase">Latest
                                                                        Position of Users</span>
                                                                </div>
                                                                <div class="caption" style="float: right;">
                                                                    <div>
                                                                        <input type="checkbox" style="width: 20px;"
                                                                            id="dealer-checkbox"> <span
                                                                            class="span-info"> Show/Hide Dealer's
                                                                            Position </span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="span-info"> <img
                                                                                src="{{ asset('assets/pages/img/map-marker.png') }}"
                                                                                height="30px" width="20px"
                                                                                alt=""> Dealer</span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="span-info"> <img
                                                                                src="{{ asset('assets/pages/img/map.png') }}"
                                                                                height="25px" width="20px"
                                                                                alt="">Marketing Officer</span>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="portlet-body">
                                                                <div id="gmap" class="gmaps"> </div>
                                                            </div>
                                                        </div>
                                                        <!-- END BASIC PORTLET-->

                                                        @canany(['reports', 'order', 'collection'])
                                                            <div class="top-sales box">

                                                                <div class="bg-white"
                                                                    style="padding: 2px 15px; margin-left: -15px">
                                                                    <div class="title">
                                                                        <p> TODAYS
                                                                            <i class="far fa-calendar"></i>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="top-sales-details">
                                                                    @can('reports')
                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">ONLINE VISIT</span>
                                                                            </a>
                                                                        </li>

                                                                        <li>
                                                                            <span
                                                                                class="price">{{ $today_onlinevisit }}</span>
                                                                        </li>
                                                                        <!-- <li>
                                                                        <a href="#">
                                                                            <span class="product">OFFLINE VISIT</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <span class="price">1107</span>
                                                                    </li> -->


                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">UNLISTED VISIT</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <span class="price">{{ $today_unlisted }}</span>
                                                                        </li>


                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">TOTAL VIST</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <span
                                                                                class="price">{{ $today_totalvisit }}</span>
                                                                        </li>
                                                                    @endcan


                                                                    @can('order')
                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">ORDER</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <span class="price">RS.
                                                                                {{ $today_order }}</span>
                                                                        </li>
                                                                    @endcan

                                                                    @can('collection')
                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">COLLECTION</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <span class="price">RS.
                                                                                {{ $today_collection }}</span>
                                                                        </li>

                                                                        <li>
                                                                            <a href="#">
                                                                                <span class="product">Total Present</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <span class="price">{{ $present }}</span>
                                                                        </li>
                                                                    @endcan

                                                                </div>
                                                            </div>
                                                        @endcanany
                                                    </div>
                                                </div>
                                                <!-- End Box Section -->
                                        </section>
                                    </div>
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
        function searchFunction() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
        var map;
        // var infowindow;

        var locations = <?php echo $currentLoc; ?>;
        // console.log(locations);
        var dealers = <?php echo $dealers; ?>;

        // console.log(dealers);
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
            // var infowindow = new google.maps.InfoWindow();


            setMarkers(locations, dealers);




        }

        function setMarkers(locations, dealers) {
            for (i = 0; i < locations.length; i++) {
                var location = locations[i];
                plotMarker(location)
            }
            for (i = 0; i < dealers.length; i++) {
                var dealer = dealers[i];
                // console.log(dealer.latitude);
                if (dealer.latitude != null) {
                    plotstore(dealer)
                }
            }
        }

        function plotMarker(location) {

            var markerIcon = (location.is_exact) ? 'map.png' : 'map.png';
            var markerIcon = "{{ asset('assets/pages/img/map.png') }}"; //+ markerIcon;


            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(location.lat, location.lng),
                icon: markerIcon,
                // title: location.name,
                map: map
            });
            var infowindow = new google.maps.InfoWindow({});
            infowindow.setContent(location.name);
            infowindow.open(map, marker);
        }

        function plotstore(dealer) {


            var markerIcon = (dealer.is_exact) ? 'map.png' : 'map.png';
            var markerIcon = "{{ asset('assets/pages/img/map.png') }}"; //+ markerIcon;


            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(dealer.latitude, dealer.longitude),
                // icon: markerIcon,
                title: dealer.name,
                map: map
            });

            const checkb = document.getElementById("dealer-checkbox");

            checkb.addEventListener('click', function() {
                var name = dealer.name;
                var address = dealer.address;
                // console.log(checkb.checked);
                if (checkb.checked) {
                    // drawMap();

                    clickcheckboc(marker, name, address);
                } else {
                    // drawMap();
                    uncheck(marker, name, address);
                }
            });





            // var infowindow = new google.maps.InfoWindow({});
            //     infowindow.setContent(dealer.name+'<br>'+dealer.address);
            //     infowindow.open(map, marker);


            //     function infoShow(x,y){
            //     var infowindow = new google.maps.InfoWindow({});
            //     google.maps.event.addListener(marker, 'click', (function(marker) {
            //     return function() {
            //         console.log(dealer.name);
            //         infowindow.setContent(dealer.name);
            //         infowindow.open(map, marker);
            //     }
            // })(marker));
            //     }
        }

        function clickcheckboc(marker, name, address) {
            var infowindow = new google.maps.InfoWindow({});
            infowindow.setContent(name + '<br>' + address);
            infowindow.open(map, marker);
        }

        function uncheck(marker, name, address) {
            drawMap();
            // plotstore(dealer)
            // setMarkers(locations,dealers);

            //     google.maps.event.addListener(marker, 'click', (function(marker, i) {
            //     return function() {
            //     var infowindow = new google.maps.InfoWindow({});
            //     infowindow.setContent(name+'<br>'+address);
            //     infowindow.open(map, marker);
            //     }
            // })(marker, i));
        }
    </script>
    <script async defer
        src="https://maps.google.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=drawMap"></script>
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
