@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Unlisted Dealer History</h1>
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

                <form method="get" class="page-content-container" action="/negotiation-report">
                    <div class="row">

                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="control-label bolds">From<span
                                        class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateFa" name="start-date"
                                        value="{{ $from ?? old('start-date') }}" autocomplete="off" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar" required>
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('start-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('start-date') }}</strong>
                                    </span>
                                @endif
                                @if (session('status'))
                                    <span class="help-block required">
                                        <strong>{{ session('status') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="control-label bolds">To<span
                                        class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateF" name="end-date"
                                        value="{{ $to ?? old('end-date') }}" autocomplete="off" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar" required>
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('end-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('end-date') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"
                                    style="margin-top:24px;width: 100%;">View</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (empty($from) && empty($to))
                                            Total Unlisted Dealer Visits
                                        @elseif(!empty($from) && empty($to))
                                            Unlisted Dealer Visit({{ $from }} TO Now)
                                        @elseif(empty($from) && !empty($to))
                                            Unlisted Dealer Visit (till {{ $to }})
                                        @else
                                            Unlisted Dealer visit ({{ $from }} TO {{ $to }})
                                        @endif
                                        - @if (!empty($list))
                                            {{ count($list) }}
                                        @else
                                            0 @endif &nbsp;&nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp;
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/unlisted-visit-export" method="post">
                                        @csrf
                                        <input type="hidden" name="from"
                                            @if (isset($from)) @if ($from) value="{{ $from }}" @endif
                                            @endif>
                                        <input type="hidden" name="to"
                                            @if (isset($to)) @if ($to) value="{{ $to }}" @endif
                                            @endif>

                                        <button class="btn btn-primary btn-xs"><i class="fa fa-download"
                                                style="padding-right: 5px;"></i>Excel</button>
                                    </form>
                                </div>

                            </div>


                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> S/N</th>
                                            <th> Staff no. </th>
                                            <th> Staff Name </th>
                                            <th> Total Visit </th>
                                            <th> Purpose Wise Visit </th>
                                            <th> Remarks </th>
                                            <th>Status</th>
                                            <th> Action </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($list))
                                            @foreach ($list as $lis)
                                                <tr>
                                                    <td> {{ $loop->iteration }} </td>
                                                    <td> {{ $lis->user_id }} </td>
                                                    <td> {{ $lis->user['name'] }}</td>
                                                    <td> {{ $lis->visit_count }} </td>
                                                    <td>
                                                        @foreach ($lis->purposes as $purpose)
                                                            {{ $purpose->purpose }}-
                                                            {{ $purpose->visit_count }} <br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $lis->remarks }}</td>
                                                    <td>{{ $lis->status }}</td>
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
                                                        <a href="/negotiation/{{ $lis->user_id }}?from={{ $from }}&to={{ $to }}"
                                                            class="btn btn-primary btn-sm"> View </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>

            </div>
        </div>
    </div>
@endsection
