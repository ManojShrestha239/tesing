@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <style>
        .dt-buttons {
            display: none;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Attendance</h1>
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
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.report')

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
                                        <span class="caption-subject font-red bold uppercase">Attendance @if (!$date && $flag == 'index') of The Day
                                            @else
                                                -{{ $date }} @endif
                                            @if ($name)
                                                -{{ $name }} @endif
                                        </span>
                                    </div>
                                    <div class="caption" style="float:right">
                                        <a href="/monthlySummary?user=&from={{ $today }}&to={{ $today }}"
                                            class="btn btn-sm btn-primary">Attendance Report</a>
                                    </div>

                                </div>



                                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->

                                <form method="GET" class="page-content-container" action="/attendance/filter">
                                    <!-- <div class="form-group row"> -->

                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <!-- <div class="row"> -->
                                            <label class="control-label bolds">Officers<span
                                                    class="required">*</span></label>

                                            <select class="form-control selectpicker" name="user" data-live-search="true"
                                                data-live-search-style="startsWith" id="user_id">
                                                <option value="">All</option>
                                                @foreach ($users as $user)
                                                    <option
                                                        @if (isset($userid)) @if ($userid == $user->id) selected @endif
                                                    @else @if (old('user') == $user->id) selected @endif
                                                        @endif value="{{ $user->id }}">
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                data-valmsg-replace="true"></span>
                                            @if (session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                            @endif
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Date<span
                                                    class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                            <div class="date" data-provide="datepicker" class="form-control">
                                                <input type="text" id="nepaliDateFa" autocomplete="off"
                                                    value="{{ old('date', $date) }}" name="date" placeholder="YYYY-MM-DD"
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
                                            <button type="submit" class="btn btn-primary"
                                                style="margin-top:24px;width: 100%;">View</button>
                                        </div>
                                    </div>
                                </form>



                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Users</th>
                                                <th>Date</th>
                                                <th>Checkin Time</th>
                                                <!-- <th>Checkin Time</th> -->
                                                <!-- <th>Checkout Time</th> -->
                                                <th>Distance Travel</th>
                                                <th>Status</th>
                                                <!--<th>Checkd in Date</th>-->

                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($lists))
                                                @foreach ($lists as $list)
                                                    <tr>
                                                        <td>{!! $loop->iteration !!}</td>
                                                        <td>{!! $list->name !!}</td>
                                                        <td>
                                                            @if ($list->daily_attendance)
                                                                {{ $helpers->convertDate($list->daily_attendance->date) }}
                                                            @else
                                                                {{ $date }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($list->daily_attendance)
                                                                {{ $list->daily_attendance->checkin_date }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($list->daily_attendance)
                                                                {{ round($list->daily_attendance->totalkm, 2) }} KM
                                                            @else
                                                                0 KM
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (!$list->daily_attendance)
                                                                Leave
                                                            @else
                                                                Present
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($list->daily_attendance)
                                                                <a class="attendance-view-popup"
                                                                    id="{{ $list->daily_attendance->id }}"
                                                                    date-attribute="{{ $date }}">
                                                                    <div class="btn btn-primary btn-sm"
                                                                        onclick="document.getElementById('order-popup').style.display='block'">
                                                                        Details</div>
                                                                </a>
                                                                <a href="/userposition/{{ $list->id }}/{{ $date }}"
                                                                    class="btn btn-primary btn-sm">View Map</a>
                                                                <a href="{{ route('attendanceimage', $list->daily_attendance->mobileuser_id) }}?date={{ $date }}"
                                                                    class="btn btn-primary btn-sm">View File</a>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                            {{-- pop up after clicking view --}}
                            <div id="order-popup">


                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".attendance-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        var date = $(this).attr('date-attribute');
                                        $("#order-popup").load("/attendance/view/" + id + "?date=" + date);
                                        $("#order-popup").css("display", "block");
                                        var modal = document.getElementById('order-popup');
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
