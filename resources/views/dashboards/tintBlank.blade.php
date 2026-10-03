<!DOCTYPE html>

<html lang="en">

    <!-- BEGIN HEAD -->

    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        @include('dashboards.partials.head')
                <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="{{ URL::asset('assets/global/css/bootstrap-select.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
        <link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script> -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('assets/global/css/bootstrap-select.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script>


        <script>

            var date = new Date();
            var cYear = date.getFullYear();
            var cMonth = date.getMonth();
            var cDate = date.getDate();

            $(document).ready(function () {
                $("#from").datepicker({
                    dateFormat: 'yy-mm-dd',
                    maxDate: new Date(cYear, cMonth, cDate)
                });
            });

            $(document).ready(function () {
                $("#to").datepicker({
                    dateFormat: 'yy-mm-dd',
                    maxDate: new Date(cYear, cMonth, cDate)
                });
            });

            /*$('.datepicker').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '-3d'
            });*/

        </script>
        <script>
                $(document).ready(function ()
{
    $('#nepaliDateF').nepaliDatePicker({
        ndpEnglishInput: 'englishDateF'
    });

    $('#nepaliDateFa').nepaliDatePicker({
        ndpEnglishInput: 'englishDateFa'
    });

});
        </script>

<style>
            /* width */
            ::-webkit-scrollbar {
                width: 4px;
            }
            /* Track */
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            /* Handle */
            ::-webkit-scrollbar-thumb {
                background: #a7a7a7;
            }
            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
                background: #929292;
            }
            ul {
                margin: 0;
                padding: 0;
            }
            li {
                list-style: none;
            }

            .user-wrapper,.height{
                border: 1px solid #dddddd;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 10px;
                bottom: 0;
                height: 595px;
                background: #f1f7e9;
            }

            .user {
                border: 1px solid #dddddd;
                cursor: pointer;
                padding: 5px 0;
                position: relative;
            }
            .user:hover {
                background: #eeeeee;
            }
            .user:last-child {
                margin-bottom: 0;
            }
            .pending {
                position: absolute;
                left: 13px;
                top: 15px;
                background: #0d2444;
                margin: 0;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                line-height: 18px;
                padding-left: 5px;
                color: #ffffff;
                font-size: 12px;
            }
            .media-left {
                display: inline-block;
            }
            .media-left img {
                width: 64px;
                border-radius: 64px;
            }
            .media-body p {
                margin: 3px 0px 0px 12px;
            }
            .color{
                color: black;
            }
            .message-wrapper {
                height: 490px;
                border: 2px solid #dddddd;
                overflow-y: auto;
                overflow-x: hidden;
                background-color:#dbdee1;
            }
            .messages .message {
                margin-bottom: 15px;
            }
            .messages .message:last-child {
                margin-bottom: 0;
            }
            .received, .sent {
                width: 45%;
                padding: 3px 10px;
                border-radius: 10px;
            }
            .received {
                background: #f48220;
            }
            .sent {
                background: #0077ab;
                float: right;
                text-align: right;
            }
            .message p {
                margin: 5px 0;
            }
            .date {
                color: black;
                font-size: 12px;
            }
            .name{
                font-weight: bold;
                font-size: 15px;
                margin-left: 15px;
                margin-top: 0;
                color:black;
            }
            .chat-active {
                background: #dde2e4 !important;
            }
            .input-text{
                margin-top:10px;
                position:relative;
                /* background-color:#f48220; */
            }
            .input-text input[type=text]{
                width: 92%;
                height:50px;
                padding: 12px 20px;
                display: inline-block;
                border-radius: 60px;
                box-sizing: border-box;
                outline: none;
                border: 3px solid #cccccc;
            }
            .input-text input[type=text]:Focus{
                border: 4px solid #a7a7a7;
            }
            input[type=text] {
                width: 100%;
                padding: 12px 20px;
                display: inline-block;
                border-radius: 5px;
                box-sizing: border-box;
                outline: none;
                border: 1px solid #cccccc;
                
            }
            .input-text button{
                height:45px;
                width:7%;
                position:top;
                top:0;
                border-radius: 70px;
                box-sizing: border-box;
                outline: none;
                border: 3px solid #cccccc;

            }
            .input-text img{
                margin: 0;
                position: absolute;
                top: 50%;
                left:96%;
                -ms-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
              
            }
            .input-text img:hover{
                background:#dde2e4;
                transition:0.2s;
                width:20px;
                border-radius:50%;
            }
        
            /* input[type=text], select{
                border: 4px solid #a7a7a7;
            } */
            input[type=text]:Focus{
                border: 2px solid #a7a7a7;
            }
            .chat{
                text-align:right;
                margin-right: 10%;
            }
            #sendButton{
                background-image:url("../public/assets/pages/img/logo.svg");
                width:25px;
                height:25px;
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
                                <!-- END PAGE BASE CONTENT -->
                         
                    </div>
                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>
                <!-- BEGIN FOOTER -->
                {{--@include('dashboards.partials.footer')--}}
                <!-- END FOOTER -->
        <!-- END CONTAINER -->
        <!-- BEGIN QUICK SIDEBAR -->
       
        <!-- END QUICK SIDEBAR -->
    @include('dashboards.partials.js')

        <!--[if lt IE 9]>
        <script src="{{ URL::asset('assets/global/plugins/respond.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/excanvas.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/ie8.fix.min.js') }}" type="text/javascript" ></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->

        <!-- <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>

        <script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script> -->
       
        <script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}"></script>
        <script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>

        {{--        messages--}}
        <script src="https://js.pusher.com/5.1/pusher.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://kit.fontawesome.com/4a4ae7464d.js" crossorigin="anonymous"></script>

        {{-- <script>
            $(document).ready(function(){
                var path = window.location.pathname.split("/").pop();
                if (path == "dashboard"){
                    path == "";
                    var target = $('.navigation .classic-menu-dropdown a[href="/dashboard"]');
                }else{
                    var target = $('.navigation .classic-menu-dropdown a[href="/dashboard/' + path + '"]');
                }
              
                target.addClass('navActive');  
            });
        </script> --}}
        <script>
            $(document).ready(function(){
                $(".products-class a").click(function(){
                var holder = $(this).attr('class');
                $("body").load("/dashboard/"+ holder);
            });
            });
        </script>

        <script>
            $(document).ready(function(){
                $(".bank-class a").click(function(){
                var holder = $(this).attr('class');
                // alert(holder)
                $("body").load("/dashboard/"+ holder);
            });
            });
        </script>

      

        <script>
            var receiver_id = '';
            var my_id = 0;
            $(document).ready(function () {
                // ajax setup form csrf token
                $.ajaxSetup({
                    headers: {
                       
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // Enable pusher logging - don't include this in production
                Pusher.logToConsole = true;
                var pusher = new Pusher('fc383478031ec7b8921f', {
                    cluster: 'ap2',
                    forceTLS: true
                });
                var channel = pusher.subscribe('my-channel');
                channel.bind('my-event', function(data) {
                    // alert(JSON.stringify(data));
                    if (my_id == data.from) {
                        $('#' + data.to).click();
                    } else if (my_id == data.to) {
                        if (receiver_id == data.from) {
                            // if receiver is selected, reload the selected user ...
                            $('#' + data.from).click();
                        } else {
                            // if receiver is not seleted, add notification for that user
                            var pending = parseInt($('#' + data.from).find('.pending').html());
                            if (pending) {
                                $('#' + data.from).find('.pending').html(pending + 1);
                            } else {
                                $('#' + data.from).append('<span class="pending">1</span>');
                            }
                        }
                    }
                });

                $('.user').click(function () {
                    $(".user").not(this).removeClass('chat-active');
                    $(this).addClass('chat-active');
                   
                    $(this).find('.pending').remove();

                    receiver_id = $(this).attr('id');
                //     alert(receiver_id)
                    $.ajax({
                        type: "get",
                        url: "/dashboard/chats/emporium/" + receiver_id, // need to create this route
                        data: "",
                        cache: false,
                        success: function (data) {
                            $('#messages').html(data);
                            scrollToBottomFunc();
                        }
                    });
                });
                //     $(document).on('keyup', '.input-text input', function (e) {
                //     var message = $(this).val();
                //     // check if enter key is pressed and message is not null also receiver is selected
                //     if (e.keyCode == 13 && message != '' && receiver_id != '') {
                //         $(this).val(''); // while pressed enter text box will be empty
                //         var datastr = "receiver_id=" + receiver_id + "&message=" + message;
                //         $.ajax({
                //             type: "post",
                //             url: "message", // need to create this post route
                //             data: datastr,
                //             cache: false,
                //             success: function (data) {
                //             },
                //             error: function (jqXHR, status, err) {
                //             },
                //             complete: function () {
                //                 scrollToBottomFunc();
                //             }
                //         })
                //     }
                // });

                $(document).on('keyup', '.input-text input', function (e) {
                    var message = $(this).val();
                    
                    // check if enter key is pressed and message is not null also receiver is selected
                    if (e.keyCode == 13 && message != '' && receiver_id != '') {
                        $(this).val(''); // while pressed enter text box will be empty
                        // alert('message')
                        var datastr = "receiver_id=" + receiver_id + "&message=" + message;
                        // alert(datastr);
                        $.ajax({
                            type: "POST",
                            url: "/dashboard/emporium", // need to create this post route
                            data: datastr,
                            cache: false,
                            success: function (data) {
                                // alert(datastr);
                            },
                            error: function (jqXHR, status, err) {
                            },
                            complete: function () {
                                scrollToBottomFunc();
                            }
                        })
                    }
                  });

                  $(document).on('click', '.input-text button', function (e) {
                    var message = $('.input-text input').val();
                    // check if enter key is pressed and message is not null also receiver is selected
                    if (message != '' && receiver_id != '') {
                        $('.input-text input').val(''); // while pressed send button text box will be empty
                        var datastr = "receiver_id=" + receiver_id + "&message=" + message;
                        // alert(datastr);
                        $.ajax({
                            type: "post",
                            url: "/dashboard/emporium", // need to create this post route
                            data: datastr,
                            cache: false,
                            success: function (data) {
                                // alert(datastr);
                            },
                            error: function (jqXHR, status, err) {
                            },
                            complete: function () {
                                scrollToBottomFunc();
                            }
                        })
                    }
                  });
                
                });

            // make a function to scroll down auto
            function scrollToBottomFunc() {
                $('.message-wrapper').animate({
                    scrollTop: $('.message-wrapper').get(0).scrollHeight
                }, 50);
            }



        </script>

    </body>

</html>

