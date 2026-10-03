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
                                            <span class="caption-subject font-red bold uppercase">Attendance Report of
                                                {{ $username }}
                                                @if (isset($from))
                                                @if ($from)
                                                ({{ $from }} TO {{ $to }})
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        @if (isset($attendances))
                                        <div class="caption" style="float:right;display:flex;">
                                            <form action="/report/attendance/download" method="get">
                                                @if (isset($from))
                                                @if ($from)
                                                <input type="hidden" value="{{ $from }}" name="from">
                                                <input type="hidden" value="{{ $to }}" name="to">
                                                @endif
                                                @endif
                                                @if (count($attendances) > 0)
                                                <input type="hidden" value="{{ $userinput }}" name="user">
                                                @endif
                                                <button class="btn btn-sm btn-primary" style="margin-right:5px;">Download</button>

                                            </form>

                                            <!-- <form action="/dashboard/report/attendance/summary" method="get">
                                                    @if (isset($from))
    @if ($from)
    <input type="hidden" value="{{ $from }}" name="from">
                                                    <input type="hidden" value="{{ $to }}" name="to">
    @endif
                                                    <button class="btn btn-sm btn-primary">Summary</button>
    @endif
                                                </form> -->

                                        </div>

                                        <!-- <div class="caption" style="float:right">
                                        <a href="/dashboard/attendanceReport" class="btn btn-sm btn-primary">Generate Report</a>
                                    </div> -->
                                        @endif
                                    </div>



                                    <!-- BEGIN SIDEBAR CONTENT LAYOUT -->

                                    <div class="portlet-body">
                                        @if (isset($attendances))
                                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                                            <thead>
                                                <tr>
                                                    <th colspan="2"></th>
                                                    <th colspan="3"> CHECKIN</th>
                                                    <th colspan="3">CHECKOUT</th>
                                                    <th colspan="3"></th>
                                                </tr>
                                                <tr>
                                                    <th> S/N </th>
                                                    <th> USER </th>
                                                    <th> DATE </th>
                                                    <th> IN-TIME </th>
                                                    <th> EARLY </th>
                                                    <th> LATE </th>
                                                    <th> OUT-TIME </th>
                                                    <th> EARLY </th>
                                                    <th> LATE </th>
                                                    <th> LAST STATUS </th>
                                                    <th> PUNCH COUNT </th>
                                                    <th> DISTANCE TRAVEL </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if (!empty($attendances))
                                                @foreach ($attendances as $attendance)
                                                <tr>
                                                    <td>{!! $loop->iteration !!}</td>
                                                    <td>{{ $attendance->mobileuser['name'] }}-({{ $attendance->mobileuser['id'] }})
                                                    </td>
                                                    <td>{{ $helpers->convertDate($attendance->date) }}</td>
                                                    <td> {{ $attendance->time }} </td>
                                                    <td> {{ $attendance->early }} </td>
                                                    <td> {{ $attendance->late }} </td>
                                                    <td> {{ $attendance->checkout }} </td>
                                                    <td> {{ $attendance->earlycheckout }} </td>
                                                    <td> {{ $attendance->latecheckout }} </td>
                                                    <td> {{ $attendance->status }} </td>
                                                    <td> {{ $attendance->total_punch }} </td>
                                                    <td>@if($attendance->attendance) {{ round($attendance->attendance->totalkm,2) }} KM @endif </td>
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
                                {{-- pop up after clicking view --}}
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