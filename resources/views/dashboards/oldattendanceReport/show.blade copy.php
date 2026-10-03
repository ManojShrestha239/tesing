@extends('dashboards.sessions.layout')

@section('content')
<style>
    .dt-buttons {
        display: none;
    }
</style>
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Attendance Report</h1>
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
                        @if(session('Status'))

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
                                            <span class="caption-subject font-red bold uppercase">Attendance Report @if(isset($attendances)) @if(count($attendances)>0) - {{$username}} @endif
                                                @if(isset($from)) @if($from) ({{$from}}-{{$to}}) @endif @endif @endif </span>
                                        </div>
                                        @if(isset($attendances))
                                        <div class="caption" style="float:right;display:flex;">
                                            <form action="/dashboard/report/attendance/download" method="get">
                                                @if(isset($from))
                                                @if($from)
                                                <input type="hidden" value="{{$from}}" name="from">
                                                <input type="hidden" value="{{$to}}" name="to">
                                                @endif

                                                @endif
                                                @if(count($attendances)>0)
                                                <input type="hidden" value="{{$userinput}}" name="user">
                                                @endif
                                                <button class="btn btn-sm btn-primary" style="margin-right:5px;">Download</button>

                                            </form>

                                            <form action="/dashboard/report/attendance/summary" method="get">
                                                @if(isset($from))
                                                @if($from)
                                                <input type="hidden" value="{{$from}}" name="from">
                                                <input type="hidden" value="{{$to}}" name="to">
                                                @endif
                                                <button class="btn btn-sm btn-primary">Summary</button>
                                                @endif
                                            </form>

                                        </div>

                                        <!-- <div class="caption" style="float:right">
                                    <a href="/dashboard/attendanceReport" class="btn btn-sm btn-primary">Generate Report</a>
                                </div> -->
                                        @endif
                                    </div>



                                    <!-- BEGIN SIDEBAR CONTENT LAYOUT -->

                                    <form method="GET" class="page-content-container" action="/dashboard/report/attendance">
                                        <!-- <div class="form-group row"> -->

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <!-- <div class="row"> -->
                                                <label class="control-label bolds">Officers<span class="required">*</span></label>
                                                <select class="form-control selectpicker" name="user" data-live-search="true" data-live-search-style="startsWith" id="user_id">
                                                    <option value="">Select User</option>
                                                    @foreach ($users as $user)
                                                    <option @if(old('user')==$user->id) selected @else @if(isset($userinput)) @if($userinput == $user->id) selected @endif @endif @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                                @if ($errors->has('user'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('user') }}</strong>
                                                </span>
                                                @ENDIF
                                                @if(session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                                    @endif
                                                    <span class="field-validation-valid" data-valmsg-for="resCountry" data-valmsg-replace="true"></span>
                                                    <!-- </div> -->
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">from<span class="required has-feedback{{ $errors->has('from') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateFa" autocomplete="off" name="from" value="@if(isset($frominput))@if($frominput){{$frominput}}@endif @else{{old('from')}}@endif" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                                </div>
                                                <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                                @if ($errors->has('from'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('from') }}</strong>
                                                </span>
                                                @endif
                                                @if(session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">To<span class="required has-feedback{{ $errors->has('to') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateF" autocomplete="off" name="to" value="@if(isset($toinput))@if($toinput){{$toinput}}@endif @else{{old('to')}}@endif" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                                </div>
                                                <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                                @if ($errors->has('to'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('to') }}</strong>
                                                </span>
                                                @endif
                                                @if(session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" style="margin-top:24px;width: 100%;">View</button>
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </form>



                                    <div class="portlet-body">
                                        @if(isset($attendances))
                                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>User</th>
                                                    <th>Date</th>
                                                    <th>Checkin Time</th>
                                                    <th>Last Punch Time</th>
                                                    <th>Last status</th>
                                                    <th> Punch Count </th>
                                                    <!-- <th>Remarks</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if(!empty($attendances))
                                                @foreach($attendances as $attendance)

                                                <tr>
                                                    <td>{!! $loop->iteration !!}</td>
                                                    <td>{{$attendance->mobileuser['name']}}-({{$attendance->mobileuser['id']}})</td>
                                                    <td>{{$attendance->date}}</td>
                                                    <td> {{$attendance->checkin_date}} </td>
                                                    <td> @if($attendance->checkout_date){{$attendance->checkout_date}} @else - @endif </td>
                                                    <td> {{$attendance->status}} </td>
                                                    <td> {{$attendance->punch}} </td>
                                                    <!-- <td></td> -->
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
                                <!-- END EXAMPLE TABLE PORTLET-->
                                {{-- pop up after clicking view  --}}
                                <div id="order-popup">


                                </div>
                            </div>

                            <!-- END SIDEBAR CONTENT LAYOUT -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection