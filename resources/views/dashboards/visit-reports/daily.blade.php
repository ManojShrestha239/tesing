@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Visit History</h1>
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
                                <span class="caption-subject font-red bold uppercase">Officer</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.report')

                        </nav>
                    </div>
                </div>

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <form method="get" class="page-content-container" action="/history">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label bolds">Officer</label>

                                <select class="form-control selectpicker" data-live-search="true"
                                    data-live-search-style="startsWith" name="user" id="user">
                                    <option value="" name=>select officer</option>
                                    @foreach ($users as $use)
                                        <option value="{{ $use->id }}"
                                            @if (isset($userid)) @if ($userid == $use->id) selected @endif
                                        @else @if (old('user') == $use->id) selected @endif @endif>{{ $use->name }}</option>
                                    @endforeach
                                </select>
                                <span class="field-validation-valid" data-valmsg-for="resCountry"
                                    data-valmsg-replace="true"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">From<span
                                        class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateF" name="start-date" autocomplete="off"
                                        placeholder="YYYY-MM-DD" value="{{ $from ?? old('start-date') }}"
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
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">To<span
                                        class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateFa" name="end-date" autocomplete="off"
                                        value="{{ $to ?? old('end-date') }}" placeholder="YYYY-MM-DD"
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
                                    <span class="caption-subject font-red bold uppercase">Total Visitors - @if (!empty($list)) {{ count($list) }}
                                        @endif
                                        &nbsp; &nbsp; &nbsp; &nbsp; Total Visits - {{ $s }}
                                </div>

                                <div style="float:right">
                                    <form action="/visit-export" method="post">
                                        @csrf
                                        <input type="hidden" name="user"
                                            value="@if (isset($userid)) {{ $userid }} @endif">
                                        <input type="hidden" name="status"
                                            value="@if (isset($status)) {{ $status }} @endif">
                                        <input type="hidden" name="from"
                                            value="@if (isset($from)) {{ $from }} @endif">
                                        <input type="hidden" name="to"
                                            value="@if (isset($to)) {{ $to }} @endif">

                                        <button class="btn btn-primary btn-xs"><i class="fa fa-download"
                                                style="padding-right: 5px;"></i>Excel</button>
                                    </form>
                                </div>
                                <div style="float:right; padding-right: 5px;">
                                    <form action="/mis-Report" method="post">
                                        @csrf
                                        <input type="hidden" name="user"
                                            value="@if (isset($userid)) {{ $userid }} @endif">
                                        <input type="hidden" name="status"
                                            value="@if (isset($status)) {{ $status }} @endif">
                                        <input type="hidden" name="from"
                                            value="@if (isset($from)) {{ $from }} @endif">
                                        <input type="hidden" name="to"
                                            value="@if (isset($to)) {{ $to }} @endif">
                                        <button class="btn btn-primary btn-xs"><i class="fa fa-download"
                                                style="padding-right: 5px;"></i>MIS Excel</button>
                                    </form>
                                </div>

                                <div class="uptable">
                                    <div class="row">
                                        @if (!empty($amt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($amt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif


                                        @if (!empty($discount))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($discount, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif


                                        @if (!empty($discount))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format(($discount * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($cashamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($cashamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif


                                        @if (!empty($discountcash))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($discountcash, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcash))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format(($discountcash * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($chequeamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($chequeamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcheque))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($discountcheque, 2, '.', ',') }} </td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcheque))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.
                                                            {{ number_format(($discountcheque * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($tokenamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($tokenamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif
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
                                                <th>Collection Amount</th>
                                                <th> Status </th>
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
                                                                {{ $purpose->purposes['purpose'] }}-
                                                                {{ $purpose->visit_count }} <br>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            {{ number_format($lis->collection, 2) }}
                                                        </td>
                                                        <td>
                                                            {{ $lis->status }}
                                                        </td>
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
                                                            <a href="/visithistory/{{ $lis->user_id }}?from={{ $from }}&to={{ $to }}"
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
