<!DOCTYPE html>
<html>
<!-- BEGIN HEAD -->

<head>
    @include('dashboards.partials.head')

    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/global/css/bootstrap-select.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css"
        rel="stylesheet">
    <link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .dropdown-menu {
            z-index: 1000 !important;
            max-height: 300px !important;
            min-width: 323px !important;
        }

        .dt-buttons {
            display: none;
        }
    </style>
    <script>
        $(document).ready(function() {
            // var id = $(".order-hover").attr('id');
            // window.alert(id);
            // var reference = [];
            $(".orders-class a").click(function() {
                var holder = $(this).attr('class');
                $("body").load("/dashboard/orders/" + holder);
                // reference.push(holder);
                // $("." + id).addClass('navActive');
            });
            //  $("." + class).addClass('navActive');
            //     function help(){
            //      window.onload = $(document).ready(function(){
            //         $("." + reference).addClass('navActive');
            //      });

            // }
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".cheque-collection").click(function() {
                $(".page-content").load("/dashboard/cheque-collection");
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".report-class a").click(function() {
                var holder = $(this).attr('class');
                $("body").load("/dashboard/" + holder);
            });
        });
    </script>
    <style>
        .arrow {
            display: none !important;
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

            @yield('content')

        </div>
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN QUICK NAV -->


    <!-- END QUICK NAV -->
    <!--[if lt IE 9]>
<script src="{{ URL::asset('assets/global/plugins/respond.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/excanvas.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/ie8.fix.min.js') }}" type="text/javascript"></script>
<![endif]-->
    <!-- BEGIN CORE PLUGINS -->

    <script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
        type="text/javascript"></script>
    <!-- <script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}" type="text/javascript"></script> -->

    <script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script>


    <!-- <script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}"></script> -->

    <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>



    <script>
        function closePopover() {
            $('.popover').css('display', 'none');
        }

        $('#nepaliDateF').nepaliDatePicker({
            ndpEnglishInput: 'englishDateF'
        });

        $('#nepaliDateFa').nepaliDatePicker({
            ndpEnglishInput: 'englishDateFa'
        });
    </script>

    @include('dashboards.partials.js')

</body>

</html>
