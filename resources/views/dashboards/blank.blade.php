<!DOCTYPE html>

<html lang="en">

<!-- BEGIN HEAD -->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css"
        rel="stylesheet">
    <link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}"
        rel="stylesheet" type="text/css" />
    <!-- <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" /> -->
    <!-- <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script> -->


    <script>
        var date = new Date();
        var cYear = date.getFullYear();
        var cMonth = date.getMonth();
        var cDate = date.getDate();

        // $(document).ready(function () {
        //     $("#from").datepicker({
        //         dateFormat: 'yy-mm-dd',
        //         maxDate: new Date(cYear, cMonth, cDate)
        //     });
        // });

        // $(document).ready(function () {
        //     $("#to").datepicker({
        //         dateFormat: 'yy-mm-dd',
        //         maxDate: new Date(cYear, cMonth, cDate)
        //     });
        // });

        /*$('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });*/
    </script>
    <!-- <script>
        $(document).ready(function() {
            $('#nepaliDateF').nepaliDatePicker({
                ndpEnglishInput: 'englishDateF'
            });

            $('#nepaliDateFa').nepaliDatePicker({
                ndpEnglishInput: 'englishDateFa'
            });

        });
    </script> -->

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

        .user-wrapper,
        .height {
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

        .color {
            color: black;
        }

        .message-wrapper {
            height: 490px;
            border: 2px solid #dddddd;
            overflow-y: auto;
            overflow-x: hidden;
            background-color: #dbdee1;
        }

        .messages .message {
            margin-bottom: 15px;
        }

        .messages .message:last-child {
            margin-bottom: 0;
        }

        .received,
        .sent {
            width: 45%;
            padding: 3px 10px;
            border-radius: 10px;
        }

        .received {
            background: #813696;
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

        .name {
            font-weight: bold;
            font-size: 15px;
            margin-left: 15px;
            margin-top: 0;
            color: black;
        }

        .chat-active {
            background: #dde2e4 !important;
        }

        .input-text {
            margin-top: 10px;
            position: relative;
            /* background-color:#813696; */
        }

        .input-text input[type=text] {
            height: 50px;
            padding: 12px 20px;
            display: inline-block;
            border-radius: 60px;
            box-sizing: border-box;
            outline: none;
            border: 3px solid #cccccc;
        }

        .input-text input[type=text]:Focus {
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

        .input-text button {
            height: 45px;
            width: 7%;
            position: top;
            top: 0;
            border-radius: 70px;
            box-sizing: border-box;
            outline: none;
            border: 3px solid #cccccc;

        }

        .input-text img {
            margin: 0;
            position: absolute;
            top: 50%;
            left: 96%;
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);

        }

        .input-text img:hover {
            background: #dde2e4;
            transition: 0.2s;
            width: 20px;
            border-radius: 50%;
        }

        /* input[type=text], select{
                border: 4px solid #a7a7a7;
            } */
        input[type=text]:Focus {
            border: 2px solid #a7a7a7;
        }

        .chat {
            text-align: right;
            margin-right: 10%;
        }

        #sendButton {
            background-image: url("../public/assets/pages/img/logo.svg");
            width: 25px;
            height: 25px;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            min-width: 300px;
            max-width: 400px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            position: relative;
        }

        .toast.toast-error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .toast.toast-success {
            background: #dcfce7;
            border-left: 4px solid #22c55e;
            color: #166534;
        }

        .toast.toast-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .toast-icon {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .toast-message {
            font-size: 13px;
            line-height: 1.4;
        }

        .toast-message ul {
            margin: 5px 0 0 0;
            padding-left: 18px;
        }

        .toast-message li {
            list-style: disc;
            margin-bottom: 3px;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
            padding: 0;
            line-height: 1;
        }

        .toast-close:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo">
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

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
    {{-- @include('dashboards.partials.footer') --}}
    <!-- END FOOTER -->
    <!-- END CONTAINER -->
    <!-- BEGIN QUICK SIDEBAR -->

    <!-- END QUICK SIDEBAR -->
    @include('dashboards.partials.js')

    <!--[if lt IE 9]>
        <script src="{{ URL::asset('assets/global/plugins/respond.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/excanvas.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/ie8.fix.min.js') }}" type="text/javascript"></script>
        <![endif]-->
    <!-- BEGIN CORE PLUGINS -->

    <!-- <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>

        <script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
            type="text/javascript"></script>
        <script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script> -->

    <script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
        type="text/javascript"></script>
    <!-- <script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}"></script> -->
    <script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>

    {{--        messages --}}
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
        $(document).ready(function() {
            $(".products-class a").click(function() {
                var holder = $(this).attr('class');
                $("body").load("/dashboard/" + holder);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".bank-class a").click(function() {
                var holder = $(this).attr('class');
                // alert(holder)
                $("body").load("/dashboard/" + holder);
            });
        });
    </script>



    <script>
        var receiver_id = '';
        var my_id = 0;
        $(document).ready(function() {
            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Enable pusher logging - don't include this in production
            // Pusher.logToConsole = true;
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

            $(document).on('click', '.user', function() {
                $(".user").not(this).removeClass('chat-active');
                $(this).addClass('chat-active');

                $(this).find('.pending').remove();

                type = $(this).attr('role');
                if (type == 'individual') {
                    receiver_id = $(this).attr('id');
                }
                if (type == 'group') {
                    receiverid = $(this).attr('id');
                    receiver_id = receiverid.split('_')[1];
                }
                // alert(receiver_id)
                $.ajax({
                    type: "get",
                    url: "chat/" + receiver_id, // need to create this route
                    data: {
                        'type': type
                    },
                    cache: false,
                    success: function(data) {
                        $('#messages').html(data);
                        scrollToBottomFunc();
                    }
                });
            });
            $('#search-icon').on('click', function() {
                let searchvalue = $('#search').val();
                if (searchvalue) {
                    search(searchvalue);
                }
            });
            $(document).on('keyup', '#search', function(e) {
                let searchvalue1 = $('#search').val();
                if (searchvalue1 == '') {
                    search(searchvalue1);
                }
                if (e.keyCode == 13) {
                    if (searchvalue1 != '') {
                        search(searchvalue1);
                    }
                }
            });

            function search(value) {

                // console.log(searchvalue);
                // if(searchvalue){
                $('#search').empty();

                $.ajax({
                    type: 'get',
                    url: '../chatuser/search',
                    data: {
                        search: value
                    },
                    cache: false,
                    success: function(data) {
                        $('.users').html(data);
                    }
                });
            }
            $(document).on('keyup', '.input-text input', function(e) {
                var message = $(this).val();
                let inputfile = [];
                var formData = new FormData();
                var file = document.getElementById('file-input');

                // Validate file count immediately
                if (file && file.files.length > 5) {
                    alert(
                        "You can only upload a maximum of 5 files at once. Please select 5 or fewer files."
                    );
                    file.value = ''; // Clear the file input
                    let preview = document.getElementById('gallery');
                    if (preview) preview.innerHTML = "";
                    return;
                }

                if (file.files.length > 0) {
                    for (let i = 0; file.files.length > i; i++) {
                        let fileExt = file.files[i].name.split('.').pop().toLowerCase();
                        if (!(fileExt == 'png' || fileExt == 'jpg' || fileExt == 'jpeg' ||
                                fileExt == 'tiff' || fileExt == 'webp')) {
                            alert("Sorry, ." + fileExt +
                                " is invalid. Allowed extensions are: png, jpeg, jpg, tiff, webp");
                            file.value = '';
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                            return;
                        }
                        if (file.files[i].size > 10485760) {
                            alert("Sorry, file '" + file.files[i].name + "' cannot be larger than 10MB");
                            file.value = '';
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                            return;
                        }

                        formData.append('files[]', file.files[i]);
                    }
                }
                // check if enter key is pressed and message is not null also receiver is selected
                if (e.keyCode == 13 && (message != '' || file.files.length > 0) && receiver_id != '' &&
                    type != '' && (type == 'group' || type == 'individual')) {
                    $(this).val('');
                    formData.append('message', message);
                    formData.append('receiver_id', receiver_id);
                    formData.append('type', type);
                    $.ajax({
                        type: "POST",
                        url: "chat", // need to create this post route
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        enctype: 'multipart/form-data',
                        success: function(data) {
                            // console.log(data);
                            file.value = ''; // Clear file input after successful send
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                        },
                        error: function(jqXHR, status, err) {
                            handleAjaxError(jqXHR, status, err);
                        },
                        complete: function() {
                            scrollToBottomFunc();
                        }
                    })
                }
            });

            $(document).on('click', '.input-text button', function(e) {
                let inputfile = [];
                var formData = new FormData();
                var message = $('.input-text input').val();
                // check if enter key is pressed and message is not null also receiver is selected
                var file = document.getElementById('file-input');

                // Validate file count immediately
                if (file && file.files.length > 5) {
                    alert(
                        "You can only upload a maximum of 5 files at once. Please select 5 or fewer files."
                    );
                    file.value = ''; // Clear the file input
                    let preview = document.getElementById('gallery');
                    if (preview) preview.innerHTML = "";
                    return;
                }

                if (file.files.length > 0) {
                    for (let i = 0; file.files.length > i; i++) {
                        let fileExt = file.files[i].type.split("/").pop().toLowerCase();
                        if (!(fileExt == 'png' || fileExt == 'jpg' || fileExt == 'jpeg' ||
                                fileExt == 'tiff' || fileExt == 'webp')) {
                            alert("Sorry, ." + fileExt +
                                " is invalid. Allowed extensions are: png, jpeg, jpg, tiff, webp");
                            file.value = '';
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                            return;
                        }
                        if (file.files[i].size > 10485760) {
                            alert("Sorry, file '" + file.files[i].name + "' cannot be larger than 10MB");
                            file.value = '';
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                            return;
                        }

                        formData.append('files[]', file.files[i]);
                    }
                }
                // console.log(inputfile);
                if ((message != '' || file.files.length > 0) && receiver_id != '' && type != '' && (type ==
                        'group' || type == 'individual')) {
                    formData.append('message', message);
                    formData.append('receiver_id', receiver_id);
                    formData.append('type', type);

                    $('.input-text input').val(''); // while pressed send button text box will be empty
                    // var datastr = "receiver_id=" + receiver_id + "&message=" + message + "&files=" + inputfile;
                    $.ajax({
                        type: "post",
                        url: "chat", // need to create this post route
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        enctype: 'multipart/form-data',
                        success: function(data) {
                            // console.log(data);
                            file.value = ''; // Clear file input after successful send
                            let preview = document.getElementById('gallery');
                            if (preview) preview.innerHTML = "";
                        },
                        error: function(jqXHR, status, err) {
                            handleAjaxError(jqXHR, status, err);
                        },
                        complete: function() {
                            scrollToBottomFunc();
                        }
                    })
                }
            });

        });

        // Toast notification function
        function showToast(type, title, message, duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            let icon = '';
            switch (type) {
                case 'error':
                    icon = '<i class="fa fa-times-circle"></i>';
                    break;
                case 'success':
                    icon = '<i class="fa fa-check-circle"></i>';
                    break;
                case 'warning':
                    icon = '<i class="fa fa-exclamation-triangle"></i>';
                    break;
                default:
                    icon = '<i class="fa fa-info-circle"></i>';
            }

            toast.innerHTML = `
                <span class="toast-icon">${icon}</span>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);

            // Auto remove after duration
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => toast.remove(), 300);
                }
            }, duration);
        }

        function handleAjaxError(jqXHR, status, err) {
            let errorMessage = '';

            if (jqXHR.status === 422) {
                // Laravel validation error
                const response = jqXHR.responseJSON;
                if (response && response.errors) {
                    let errorList = '<ul>';
                    $.each(response.errors, function(field, messages) {
                        messages.forEach(function(msg) {
                            errorList += `<li>${msg}</li>`;
                        });
                    });
                    errorList += '</ul>';
                    errorMessage = errorList;
                } else if (response && response.message) {
                    errorMessage = response.message;
                } else {
                    errorMessage = 'Validation failed. Please check your input.';
                }
                showToast('error', 'Validation Error', errorMessage);
            } else if (jqXHR.status === 413) {
                showToast('error', 'File Too Large', 'The uploaded file exceeds the maximum allowed size.');
            } else if (jqXHR.status === 419) {
                showToast('error', 'Session Expired', 'Your session has expired. Please refresh the page and try again.');
            } else if (jqXHR.status === 401) {
                showToast('error', 'Unauthorized', 'You are not authorized to perform this action. Please login again.');
            } else if (jqXHR.status === 403) {
                showToast('error', 'Forbidden', 'You do not have permission to perform this action.');
            } else if (jqXHR.status === 500) {
                showToast('error', 'Server Error', 'An internal server error occurred. Please try again later.');
            } else if (jqXHR.status === 0) {
                showToast('error', 'Connection Error',
                    'Unable to connect to the server. Please check your internet connection.');
            } else {
                errorMessage = err || 'An unexpected error occurred. Please try again.';
                showToast('error', 'Error', errorMessage);
            }
        }

        // make a function to scroll down auto
        function scrollToBottomFunc() {
            $('.message-wrapper').animate({
                scrollTop: $('.message-wrapper').get(0).scrollHeight
            }, 50);
        }
    </script>

</body>

</html>
