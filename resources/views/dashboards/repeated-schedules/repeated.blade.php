@extends('dashboards.sessions.layout')
@section('content')
    <style>
        .checkbox-item {
            height: 20px;
            width: 20px;
        }

        .span-dealer {
            margin-left: 20px;
        }

        .chkbox-subarea {
            max-height: 200px;
            overflow: auto;
        }

        .chkbox-dealer {
            max-height: 200px;
            overflow: auto;
        }

        .col-md-6 {
            padding-right: 0;
        }

        .schedule-active {
            text-decoration: underline;
            font-weight: bold;
            color: #842029;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>ADD NEW VISIT SCHEDULE</h1>
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
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <ul class="nav navbar-nav margin-bottom-35">


                                <li><a href="/schedule/create">Add New visit Schedule</a></li>
                                <li><a href="/schedule">List visit schedule </a></li>
                                <li><a href="/repeated">Repeated Schedule</a></li>
                                <li class="active"><a href="/schedule/create">Add Repeated Schedule</a></li>
                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New Visit Schedule</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif


                                <form method="POST" action="/repeated">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="user_id"
                                            class="col-md-3 col-form-label text-right">{{ __('Marketing Officer') }}</label>
                                        <div class="col-md-6">
                                            <select name="user_id" class="form-control selectpicker" data-live-search="true"
                                                required>
                                                <option value="">Select Marketing Officer</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->id }}&nbsp;&nbsp;{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="day" class="col-md-3 col-form-label text-right">
                                            <span class="week schedule-active" id="week"><button
                                                    style="border:none; background:none;">Weekly</button></span>
                                            <span>/</span>
                                            <span class="month" id="month"><button
                                                    style="border: none; background:none;">Monthly</button></span>
                                        </label>
                                        <div class="col-md-6">
                                            <div class="weekly" id="weekly">
                                                <select name="day" class="form-control selectpicker"
                                                    data-live-search="true" id="day">
                                                    <option value="">Select Day</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6" style="display: none">
                                            <div class="monthly" style="display: none;" id="monthly">
                                                <input type="number" name="day" id="day" class="form-control"
                                                    placeholder="Enter the day of the month" />
                                            </div>
                                        </div> --}}
                                    </div>

                                    <div class="form-group row area-class" style="display:none">

                                        <label for="area"
                                            class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>
                                        <div class="col-md-6">
                                            <input type="text" id="area-search" onkeyup="searchArea()"
                                                placeholder="Search for area.." class="form-control">
                                            <div class="chkbox-area" id="myUl-area"
                                                style="list-style-type: none;overflow-y: auto;">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group row sub-area-class" style="display:none">

                                        <label for="subarea"
                                            class="col-md-3 col-form-label text-right">{{ __('Sub Area') }}</label>
                                        <div class="col-md-6">
                                            <input type="text" id="subarea-search" onkeyup="searchSubArea()"
                                                placeholder="Search for subarea.." class="form-control">
                                            <div class="chkbox-subarea" id="myUl-subarea"
                                                style="list-style-type: none;overflow-y: auto;">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group row dealer-class" style="display:none">

                                        <label for="dealer"
                                            class="col-md-3 col-form-label text-right">{{ __('Dealers') }}</label>
                                        <div class="col-md-6 chkbox-dealer" id="myUl-dealer"
                                            style="list-style-type: none;height:100px;overflow:auto;">
                                            <input type="text" id="dealer-search" onkeyup="searchDealer()"
                                                placeholder="Search for dealer.." class="form-control">
                                            <div class="btn-un-ckk">

                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="remarks"
                                            class="col-md-3 col-form-label text-right">{{ __('Remarks') }}</label>

                                        <div class="col-md-6">
                                            <textarea class="form-control" name="remarks" placeholder="Remarks" required autofocus></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row hidden">
                                        <input name="status" type="text" value="not-approved">
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            {{-- <button type="submit" class="btn btn-primary" style="width: 200px;">
                                            {{ __('Create') }}
                                        </button> --}}
                                            <input type="submit" value="Create" class="btn btn-primary"
                                                style="width: 200px;">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#month').click(function() {
                $('#weekly').css("display", "none");
                $('#monthly').css("display", "block");
                $('#week').removeClass("schedule-active");
                $(this).addClass("schedule-active");
            });
            $('#week').click(function() {
                $('#monthly').css("display", "none");
                $('#weekly').css("display", "block");
                $('#month').removeClass("schedule-active");
                $(this).addClass("schedule-active");
            });
        });

        function searchArea() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("area-search");
            // console.log(input.value);
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUl-area");
            // ul = document.querySelector(".chkbox-area");

            li = ul.getElementsByTagName("li");
            // console.log(li.length);

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

        function searchSubArea() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("subarea-search");
            // console.log(input.value);
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUl-subarea");
            // ul = document.querySelector(".chkbox-subarea");

            li = ul.getElementsByTagName("li");
            // console.log(li.length);

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

        function searchDealer() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("dealer-search");
            // console.log(input.value);
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUl-dealer");
            // ul = document.querySelector(".chkbox-area");
            li = ul.getElementsByTagName("li");
            console.log(li.length);

            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    console.log(filter);
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
        jQuery(document).ready(function() {
            jQuery('select[name="user_id"]').on('change', function() {
                var id = jQuery(this).val();
                if (id) {
                    jQuery.ajax({
                        url: "/schedule/area/" + id,
                        type: "get",
                        dataType: "json",
                        success: function(data) {
                            $('.chkbox-area').empty();
                            $(".area-class").css('display', 'block');
                            data.forEach(function(e) {
                                $('.chkbox-area').append(
                                    '<li><a style="text-decoration: none;color: black;"><input type="checkbox" class="checkbox-item area-data" value="' +
                                    String(e.areacode) +
                                    '" name="area[]" autofocus><span class="span-dealer">' +
                                    e.area + '</span></a></li>');
                            });
                        }
                    });
                }
            });
            $(document).on('click', '.checkbox-item', function(e) {
                var userId = $('select[name="user_id"]').val();
                var searchIDs = $("input.area-data:checked").map(function() {
                    return $(this).val();
                }).get();

                if (!searchIDs.length) {
                    $('.chkbox-subarea').empty();
                    $('.sub-area-class').css('display', 'none');
                    $('.chkbox-dealer').empty();
                    $('.dealer-class').css('display', 'none');
                    $('.btn-un-ckk').empty();
                    return;
                }

                $.ajax({
                    url: "/getsubarea",
                    type: "get",
                    data: {
                        data: searchIDs,
                        user_id: userId,
                    },

                    success: function(response) {
                        $('.sub-area-class').css('display', 'block');

                        var selectedSubareas = $("input.subarea-data:checked").map(function() {
                            return $(this).val();
                        }).get();

                        var selectedMap = {};
                        selectedSubareas.forEach(function(subarea) {
                            selectedMap[subarea] = true;
                        });

                        var rendered = {};
                        var html = '';

                        response.forEach(function(data) {
                            if (rendered[data.subarea]) {
                                return;
                            }

                            rendered[data.subarea] = true;

                            var checked = selectedMap[data.subarea] ? ' checked' : '';
                            html +=
                                '<li><a style="text-decoration: none;color: black;"><input type="checkbox" class="new-subarea subarea-data" name="subarea[]" value="' +
                                data.subarea + checked +
                                '"><label style="margin-left:5px;" class="new-subarea">' +
                                data.subarea + '</label></a></li>';
                        });

                        $('.chkbox-subarea').html(html);
                        $('.dealer-class').css('display', 'none');
                        $('.chkbox-dealer').empty();
                        $('.btn-un-ckk').empty();
                    },
                });

            });
            $(document).on('click', '.new-subarea', function(e) {
                $('.new-dealer').remove();
                var searchIDs = [];
                var data = $("input.subarea-data:checked").map(function() {
                    searchIDs.push($(this).val());
                    $.ajax({
                        url: "/getdealer",
                        type: "get",
                        data: {
                            data: searchIDs,
                        },

                        success: function(response) {
                            $('.dealer-class').css('display', 'block');
                            $('.new-dealer').remove();
                            $('#check-all').remove();
                            $('#uncheck-all').remove();
                            $('.btn-un-ckk').append(
                                '<input type="button" value="Check All" class="btn btn-sm btn-primary check" id="check-all" autofocus>'
                            );
                            $('.btn-un-ckk').append(
                                '<input type="button" value="Uncheck All" style="display:none;" class="btn btn-sm btn-primary uncheck" id="uncheck-all" autofocus>'
                            );
                            response.map(function(data) {
                                $('.chkbox-dealer').append(
                                    '<li><a style="text-decoration: none;color: black;"><input type="checkbox" class="new-dealer" id="dealer-data" name="dealer[]" value="' +
                                    data.code +
                                    '"><label style="margin-left:5px;" class="new-dealer">' +
                                    data.name + '</label> </a></li>')
                            });

                        },
                    });
                });
            });
            $(document).on('click', '#check-all', function() {

                $('[id=dealer-data]').attr('checked', 'checked');
                $('#uncheck-all').css('display', 'block');
                $('#check-all').css('display', 'none');
            });
            $(document).on('click', '#uncheck-all', function() {
                $('[id=dealer-data]').removeAttr('checked');
                $('#uncheck-all').css('display', 'none');
                $('#check-all').css('display', 'block');
            });
        });
    </script>
@endsection
