@extends('dashboards.sessions.layout')

@section('content')
    <style>
        .dt-buttons {
            display: none;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Attendance Summary Report</h1>
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
            <div id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
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
                                                <span class="caption-subject font-red bold uppercase">Attendance Report
                                                    @if (isset($from))
                                                        @if ($from)
                                                            ({{ $from }} To {{ $to }}) @endif
                                                    @endif
                                                </span>
                                            </div>
                                            @if (count($attendances) > 0)
                                                <div class="caption" style="float:right;display:flex;">
                                                    <a href="/report/attendance/summary?user={{ $userinput }}&from={{ $from }}&to={{ $to }}"
                                                        class="btn btn-primary btn-sm"> Download </a>
                                                </div>
                                            @endif
                                        </div>

                                        <form method="GET" class="page-content-container" action="/monthlySummary">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <!-- <div class="row"> -->
                                                    <label class="control-label bolds">Officers<span
                                                            class="required">*</span></label>
                                                    <select class="form-control selectpicker" name="user"
                                                        data-live-search="true" data-live-search-style="startsWith"
                                                        id="user_id">
                                                        <option value="">Select User</option>
                                                        @foreach ($users as $user)
                                                            <option
                                                                @if (old('user') == $user->id) selected @else @if (isset($userinput)) @if ($userinput == $user->id) selected @endif
                                                                @endif
                                                        @endif
                                                        value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="field-validation-valid" data-valmsg-for="education"
                                                        data-valmsg-replace="true"></span>
                                                    @if ($errors->has('user'))
                                                        <span class="help-block required">
                                                            <strong>{{ $errors->first('user') }}</strong>
                                                        </span>
                                                    @ENDIF
                                                    @if (session('status'))
                                                        <span class="help-block required">
                                                            <strong>{{ session('status') }}</strong>
                                                    @endif
                                                    <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                        data-valmsg-replace="true"></span>
                                                    <!-- </div> -->
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="control-label bolds">from<span
                                                            class="required has-feedback{{ $errors->has('from') ? ' has-error' : '' }}">*</span></label>
                                                    <div class="date" data-provide="datepicker" class="form-control">
                                                        <input type="text" id="nepaliDateFa" autocomplete="off"
                                                            name="from"
                                                            value="{{ old('from', request()->get('from')) }}"
                                                            placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                                    </div>
                                                    <span class="field-validation-valid" data-valmsg-for="education"
                                                        data-valmsg-replace="true"></span>
                                                    @if ($errors->has('from'))
                                                        <span class="help-block required">
                                                            <strong>{{ $errors->first('from') }}</strong>
                                                        </span>
                                                    @endif
                                                    @if (session('status'))
                                                        <span class="help-block required">
                                                            <strong>{{ session('status') }}</strong>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="control-label bolds">To<span
                                                            class="required has-feedback{{ $errors->has('to') ? ' has-error' : '' }}">*</span></label>
                                                    <div class="date" data-provide="datepicker" class="form-control">
                                                        <input type="text" id="nepaliDateF" autocomplete="off"
                                                            name="to" value="{{ old('to', request()->get('to')) }}"
                                                            placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                                    </div>
                                                    <span class="field-validation-valid" data-valmsg-for="education"
                                                        data-valmsg-replace="true"></span>
                                                    @if ($errors->has('to'))
                                                        <span class="help-block required">
                                                            <strong>{{ $errors->first('to') }}</strong>
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
                                            @if (isset($attendances))
                                                <table class="table table-striped table-bordered table-hover"
                                                    id="">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2"></th>
                                                            <th colspan="2"> Checkin</th>
                                                            <th colspan="2">Checkout</th>
                                                        </tr>
                                                        <tr>
                                                            <th> S/N </th>
                                                            <th> USER </th>
                                                            <th> Early </th>
                                                            <th> LATE </th>
                                                            <th> EARLY </th>
                                                            <th> LATE </th>
                                                            <th> NO OF DAYS </th>
                                                            <th> TOTAL ATTENDANCE </th>
                                                            <th> ABSENT </th>
                                                            <th> DISTANCE TRAVEL </th>
                                                            <th> ACTION </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @if (!empty($attendances))
                                                            @foreach ($attendances as $attendance)
                                                                <tr>
                                                                    <td>{!! $loop->iteration !!}</td>
                                                                    <td>{{ $attendance->mobileuser['name'] }}-({{ $attendance->mobileuser['id'] }})
                                                                    </td>
                                                                    <td> {{ $attendance->early }} </td>
                                                                    <td> {{ $attendance->late }} </td>
                                                                    <td> {{ $attendance->earlycheckout }} </td>
                                                                    <td> {{ $attendance->latecheckout }} </td>
                                                                    <td> {{ $attendance->total_days }} </td>
                                                                    <td> {{ $attendance->total_present }} </td>
                                                                    <td> {{ $attendance->absent }} </td>
                                                                    <td> {{ round($attendance->final_km, 2) }} KM</td>
                                                                    <td>
                                                                        <?php
                                                                        if (isset($from)) {
                                                                            $from = $from;
                                                                        } else {
                                                                            $from = '';
                                                                        }
                                                                        if (isset($to)) {
                                                                            $to = $to;
                                                                        } else {
                                                                            $to = '';
                                                                        }
                                                                        ?>
                                                                        <a href="/monthlySummary/{{ $attendance->mobileuser_id }}?from={{ $from }}&to={{ $to }}"
                                                                            class="btn btn-primary btn-sm"> View </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif

                                                    </tbody>
                                                </table>
                                            @else
                                                Select Officer to view details..
                                            @endif
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
@endsection
