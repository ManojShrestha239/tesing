@extends('dashboards.sessions.layout')

@section('content')
    <style>
        .card {
            margin: 32px 0;
            border: 1px solid #f1f1f1;
        }

        .card-header {
            height: 50px;
            background-color: #f1f1f1;
            font-weight: 600;
        }

        .card-header .card-title {
            font-weight: 600;
            font-size: 24px;
            font-weight: 600;
        }

        .custom-row {
            padding: 0 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .card-body {
            display: none;
        }

        .img-hide {
            display: none;
        }

        .img-show {
            display: block;
        }

        .img-span {
            margin-left: 32px;
        }
    </style>
    <div class="page-content">
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1> Collections</h1>

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
        <!-- END BREADCRUMBS -->

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
                            <ul class="nav navbar-nav margin-bottom-35">

                                <li class="active"><a href="/collection-report">All Collection </a></li>
                                <li><a href="/collection">Cash Collection </a></li>
                                <li><a href="/cheque-collection">Cheque Collection</a></li>
                                <li><a href="/direct-collection">Direct</a></li>
                                <li><a href="/token">Token Collection</a></li>

                                <!-- <li class="active"><a href="/collection">Cash Collection </a></li>
                                                                                                                                                                                                                                                                                                                                                                        <li><a class = "cheque-collection">Cheque Collection</a></li> -->

                            </ul>

                        </nav>
                    </div>

                </div>

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <form method="get" class="page-content-container" action="/collection-report/filter">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">Users<span
                                        class="required has-feedback{{ $errors->has('user') ? ' has-error' : '' }}">*</span></label>

                                <select class="form-control selectpicker" data-live-search="true" name="user">
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($userid)) @if ($userid == $user->id) selected @endif
                                        @else @if (old('user') == $user->id) selected @endif @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('user'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">Collection Type<span
                                        class="required has-feedback{{ $errors->has('user') ? ' has-error' : '' }}">*</span></label>

                                <select class="form-control" name="collection_type">
                                    <option value="">Select Collection Type</option>
                                    <option value="cash"
                                        @if (isset($status)) @if ($status == 'cash') selected @endif
                                    @else @if (old('collection_type') == 'cash') selected @endif @endif
                                        >Cash Collection</option>
                                    <option value="cheque"
                                        @if (isset($status)) @if ($status == 'cheque') selected @endif
                                    @else @if (old('collection_type') == 'cheque') selected @endif @endif
                                        >Cheque Collection</option>
                                    <option value="direct"
                                        @if (isset($status)) @if ($status == 'direct') selected @endif
                                    @else @if (old('collection_type') == 'direct') selected @endif @endif>Direct</option>
                                    <option value="token"
                                        @if (isset($status)) @if ($status == 'token') selected @endif
                                    @else @if (old('collection_type') == 'token') selected @endif @endif>Token Collection</option>
                                </select>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('collection_type'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('collection_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="control-label bolds">From<span
                                        class="required has-feedback{{ $errors->has('start_date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateFa" autocomplete="off"
                                        value="{{ $from ?? old('start_date') }}" name="start_date" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar">
                                </div>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('start_date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('start_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="control-label bolds">To<span
                                        class="required has-feedback{{ $errors->has('end_date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input id="nepaliDateF" type="text" name="end_date"
                                        value="{{ $to ?? old('end_date') }}" autocomplete="off" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar">
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('end_date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('end_date') }}</strong>
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
                    </div>
                </form>
                {{--
                Validation foe loop
                --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('Status'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div>{{ session('Status') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="excel-upload">
                            <div class="card">
                                {{-- <div class="card-header custom-row">
                                    <span class="card-title"> Upload Excel File </span>
                                    <span class="row">
                                        <span>
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ asset('productImage/collection_upload_format.xlsx') }}"
                                                download>format.xlsx</a>
                                        </span>
                                        <span class="img-span" class="ml-3">
                                            <img src="{{ asset('assets/global/img/arrow-down-sign-to-navigate.png') }}"
                                                class="img-show" alt="">
                                            <img src="{{ asset('assets/global/img/up-arrow.png') }}" class="img-hide"
                                                alt="">
                                        </span>
                                    </span>
                                </div> --}}
                                <div class="card-body">
                                    <form action="/collection-report" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="excel_file"
                                                class="col-md-2 col-form-label">{{ __('Excel File') }}</label>
                                            <div class="col-md-4">
                                                <input type="file" name="excel_file" class="form-control" required>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('Upload') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption" style="width:100%;">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">

                                        {{ $header }}
                                        {{-- @if (count($list) > 0) --}}
                                        <div style="float:right">
                                            <form action="/collectionSummaryReport" method="post">
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
                                                        style="padding-right: 5px;"></i>summary</button>
                                            </form>
                                        </div>
                                        <div class="mr-3" style="float:right">
                                            <form action="/collectionReport" method="post">
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
                                        <div class="mr-3" style="float:right">
                                            <form action="/zeroReport" method="post">
                                                @csrf
                                                <input type="hidden" name="user"
                                                    value="@if (isset($userid)) {{ $userid }} @endif">
                                                <input type="hidden" name="from"
                                                    value="@if (isset($from)) {{ $from }} @endif">
                                                <input type="hidden" name="to"
                                                    value="@if (isset($to)) {{ $to }} @endif">

                                                <button class="btn btn-primary btn-xs"><i class="fa fa-download"
                                                        style="padding-right: 5px;"></i>Zero Report</button>
                                            </form>
                                        </div>
                                        {{-- @endif --}}
                                    </span>
                                    <br> <br>

                                    {{-- @if (isset($cash_coll))Cash Collection-{{$cash_coll}} &nbsp;&nbsp; Discount - {{$discount_cash_coll}} &nbsp;&nbsp; TDS - {{$tds_cash_coll}}
                                <br><br>
                                @endif
                                @if (isset($cheque_amount)) Cheque Collection-{{$cheque_amount}} &nbsp;&nbsp; Discount - {{$discount_cheque_amount}} &nbsp;&nbsp; TDS - {{$tds_cheque_amount}}
                                <br><br>
                                @endif
                                @if (isset($token_amount))Token Collection-{{$token_amount}} &nbsp;&nbsp; Discount - {{$discount_token_amount}} &nbsp;&nbsp; TDS - {{$tds_token_amount}}
                                <br><br>
                                @endif --}}
                                </div>

                                <div class="uptable">
                                    <div class="row">
                                        @isset($cash_coll)
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($cash_coll, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                            {{-- <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount_cash_coll, 2, '.', ',') }}</td>
                                    </table>
                                </div>

                                <div class="column">
                                    <table class="up">
                                        <tr>
                                            <th>TDS</th>
                                        </tr>
                                        <tr>
                                            <td>Rs.{{ number_format($tds_cash_coll, 2, '.', ',') }}</td>
                                    </table>
                                </div> --}}
                                        @endisset
                                    </div>

                                    <div class="row">
                                        @isset($cheque_amount)
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($cheque_amount, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                            {{-- <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount_cheque_amount, 2, '.', ',') }}</td>
                                </table>
                            </div>

                            <div class="column">
                                <table class="up">
                                    <tr>
                                        <th>TDS</th>
                                    </tr>
                                    <tr>
                                        <td>Rs.{{ number_format($tds_cheque_amount, 2, '.', ',') }}</td>
                                </table>
                            </div> --}}
                                        @endisset
                                    </div>

                                    {{-- <div class="row">
                                    @isset($token_amount)
                                    <div class="column">
                                        <table class="up">
                                            <tr>
                                                <th>Token Collection</th>
                                            </tr>
                                            <tr>
                                                <td>Rs.{{ number_format($token_amount, 2, '.', ',') }}</td>
                        </table>
                    </div>
                    <div class="column">
                        <table class="up">
                            <tr>
                                <th>Discount</th>
                            </tr>
                            <tr>
                                <td>Rs. N/A</td>
                        </table>
                    </div>

                    <div class="column">
                        <table class="up">
                            <tr>
                                <th>TDS</th>
                            </tr>
                            <tr>
                                <td>Rs. N/A</td>
                        </table>
                    </div>
                    @endisset

                </div> --}}
                                </div>
                            </div>

                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> Collection info</th>
                                            <th> Payment Type </th>
                                            <th> User / Dealer Info </th>
                                            <th> Bank </th>
                                            <th> Amount/Token (rs.) </th>
                                            <th> Previous Amount (rs.) </th>
                                            <th> Remarks </th>
                                            <th> Collection From </th>
                                            <th> Status </th>
                                            <th> Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($payment))

                                            @foreach ($payment as $lis)
                                                <tr>
                                                    <td> <span class="bold-content"> collection id :
                                                        </span>{{ $lis->id }} <br>
                                                        <span class="bold-content"> Receipt Number : </span>
                                                        {{ $lis->receipt_num }} <br>
                                                        <span class="bold-content">Collection Date :
                                                        </span>{{ $lis->collection_date }} <br>
                                                        <span class="bold-content">Payment Date : </span>
                                                        {{ $lis->payment_date }} <br>
                                                        <!-- <span class="bold-content">payment Type : </span>{{ $lis->payment_type }} <br> -->
                                                    </td>
                                                    <td> {{ $lis->payment_type }} </td>
                                                    <td><span class="bold-content">User : </span>
                                                        @if (!empty($lis->user))
                                                            {{ $lis->user->name ?? $lis->user }}
                                                        @endif
                                                        <br>
                                                        <span class="bold-content"> Dealer Code
                                                            :</span>{{ $lis->dealer_code }} <br>
                                                        <span class="bold-content">Dealer : </span>
                                                        @if (!empty($lis->dealer))
                                                            {{ $lis->dealer ?? 'N/A' }}
                                                        @endif
                                                        <br>
                                                        <span class="bold-content"> Dealer Address : </span>
                                                        @if (!empty($lis->dealer))
                                                            {{ $lis->dealer_address ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <!-- <td> {{ $lis->user }} </td> -->
                                                    <!-- <td> {{ $lis->collection_date }} </td> -->

                                                    <td>{{ $lis->bank ?? 'N/A' }}</td>
                                                    <td>{{ number_format($lis->amount, 2, '.', ',') }}</td>
                                                    <td>
                                                        @if (isset($lis->previous_amt))
                                                            {{ number_format($lis->previous_amt->amount, 2, '.', ',') }}
                                                        @endif
                                                    </td>
                                                    <!-- <td> {{ $lis->token }}</td> -->
                                                    <!-- <td> {{ number_format($lis->discount, 2, '.', ',') }} </td> -->
                                                    <!-- <td> {{ number_format($lis->TDS, 2, '.', ',') }}</td> -->
                                                    <!-- <td> {{ $lis->receipt_num }} </td> -->
                                                    <td> {{ $lis->mRemarks }} </td>
                                                    <td>
                                                        @if (isset($lis->lat_lng) && !empty($lis->lat_lng))
                                                            @php
                                                                [$lat, $lng] = explode(',', $lis->lat_lng);
                                                            @endphp
                                                            <form action="{{ route('origin', $lis->id) }}" method="post"
                                                                target="_blank">
                                                                @csrf
                                                                <input type="hidden" name="type" value="payment">
                                                                <button type="submit" class="btn btn-primary btn-sm">View
                                                                    on Map</button>
                                                            </form>
                                                        @else
                                                            {{ 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>
                                                            @if ($lis->payment_type == 'cash (regular)' || $lis->payment_type == 'cash (against)')
                                                                @if ($lis->status == 'Collected by MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-warning btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif ($lis->status == 'With MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-info btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif($lis->status == 'Blocked')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-danger btn-sm ab">{{ $lis->status }}</button>
                                                                @else
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-success btn-sm ab">{{ $lis->status }}</button>
                                                                @endif
                                                            @elseif(
                                                                $lis->payment_type == 'cheque (bearer)' ||
                                                                    $lis->payment_type == 'cash (account payee)' ||
                                                                    $lis->payment_type == 'cheque (payee)' ||
                                                                    $lis->payment_type == 'cheque' ||
                                                                    $lis->payment_type == 'PDC')
                                                                @if ($lis->status == 'Collected by MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-warning btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif ($lis->status == 'Bounced')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-danger btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif($lis->status == 'Encashed')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-info btn-sm ab">{{ $lis->status }}</button>
                                                                @else
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-success btn-sm ab">{{ $lis->status }}</button>
                                                                @endif
                                                            @elseif($lis->payment_type == 'token')
                                                                @if ($lis->status == 'Collected by MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-warning btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif ($lis->status == 'With MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-info btn-sm ab">{{ $lis->status }}</button>
                                                                @elseif($lis->status == 'Blocked')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-danger btn-sm ab">{{ $lis->status }}</button>
                                                                @else
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-success btn-sm ab">{{ $lis->status }}</button>
                                                                @endif
                                                            @elseif($lis->payment_type == 'direct')
                                                                @if ($lis->status == 'Collected by MO')
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-warning btn-sm ab">{{ $lis->status }}</button>
                                                                @else
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->status }},{{ $lis->payment_type }}"
                                                                        data-toggle="popoverchee"
                                                                        class="btn btn-success btn-sm ab">{{ $lis->status }}</button>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a class="order-view-popup" id="{{ $lis->id }}">
                                                            <div class="btn btn-primary btn-sm"
                                                                onclick="document.getElementById('order-popup').style.display='block'">
                                                                View Log</div>
                                                        </a>
                                                        <a class="btn btn-primary btn-sm"
                                                            href="/receiptimage/{{ $lis->id }}">
                                                            Receipt image
                                                        </a>
                                                        @if ($lis->is_edit === '0' || $lis->is_edit === null)
                                                            <div>
                                                                <div>
                                                                    <button type="button"
                                                                        data-api-parameter="{{ $lis->id }},{{ $lis->amount }}"
                                                                        data-toggle="popover-edit"
                                                                        class="btn btn-warning btn-sm ab">Edit
                                                                        Amount</button>
                                                                </div>
                                                            </div>
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
                    </div>
                    <div id="order-popup">



                    </div>
                    <script>
                        $(document).ready(function() {
                            $(".order-view-popup").click(function() {
                                var id = $(this).attr('id');
                                // alert(id);
                                $("#order-popup").load("/cashPopup/" + id);
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

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>

            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('[data-toggle="popoverchee"]').popover({
                trigger: 'click',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                placement: 'left',
                title: '<b>Change Cash Status</b>',
                html: true
            }).on('shown.bs.popover', function() {

                var popover = $(this);

                var contentElche = popover.next(".popover").find(".popover-content");
                var myParameterche = popover.data('api-parameter');
                var res = myParameterche.split(",");
                // console.log(myParameterche);
                var statusDirect =
                    '<option value="Deposited into Bank">Deposited into Bank</option><option value="Collected by MO">Collected by MO</option></select><br>';
                var statusCash =
                    '<option value="Collected by MO">Collected by MO</option><option value="sent to office">Sent to Office</option><option value="Received By Office">Received By Office</option></select><br>';
                var statusCheque =
                    '<option value="Collected by MO">Collected by MO</option><option value="Bounced">Bounced</option><option value="Encashed">Encashed</option><option value="Deposited into Bank">Deposited into Bank</option><option value="Sent to Office">Sent to Office</option></select><br>';
                var statusToken =
                    '<option value="Collected by MO">Collected by MO</option><option value="sent to office">Sent to Office</option><option value="Received By Office">Received By Office</option></select><br>';
                var selectOption = "";
                if (res[2] == 'direct') {
                    selectOption = statusDirect;
                } else if (res[2] == 'token') {
                    selectOption = statusToken;
                } else if (res[2] == 'cash (regular)' || res[2] == 'cash (against)') {
                    selectOption = statusCash;
                } else {
                    selectOption = statusCheque;
                }
                var resultche = '<form action="/cheque-collection/' + res[0] +
                    '" id="popForm" method="post">' +
                    '<input type="hidden" name="_method" value="PATCH">' +
                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                    '<input type="hidden" name="pre_status" value="' + res[1] + '">' +
                    '<div><label for="status">Status:</label>' +
                    '<select name="status" id="status" class="form-control input-md">' + selectOption +
                    '<label for="status">Remark:</label>' +
                    '<input type = "text" name="remark" class="form-control"><br>' +
                    '<button type="submit" class="btn btn-primary">Update</button>' +
                    '</div></form>' +
                    '<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';
                // contentEl.toggle();
                contentElche.html(resultche);
            });
            // $('[data-toggle="popoverche"]').popover({
            //     trigger: 'click',
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     placement: 'left',
            //     title: '<b>Change Cheque Status</b>',
            //     html: true
            // }).on('shown.bs.popover', function() {

            //     var popover = $(this);

            //     var contentElche = popover.next(".popover").find(".popover-content");
            //     var myParameterche = popover.data('api-parameter');
            //     var res = myParameterche.split(",");
            //     // console.log(myParameterche);
            //     var resultche = '<form action="/cheque-collection/' + res[0] + '" id="popForm" method="post">' +
            //         '<input type="hidden" name="_method" value="PATCH">' +
            //         '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
            //         '<input type="hidden" name="pre_status" value="' + res[1] + '">' +
            //         '<div><label for="status">Status:</label>' +
            //         '<select name="status" id="status" class="form-control input-md"><option value="Collected by MO">Collected by MO</option><option value="Bounced">Bounced</option><option value="Encashed">Encashed</option><option value="Deposited into Bank">Deposited into Bank</option><option value="Sent to Office">Sent to Office</option></select><br>' +
            //         '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control"><br>' +
            //         '<button type="submit" class="btn btn-primary">Update</button>' +
            //         '</div></form>' + '<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';



            //     // contentEl.toggle();
            //     contentElche.html(resultche);
            // });
            // $('[data-toggle="token-popover"]').popover({
            //     trigger: 'click',
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     placement: 'left',
            //     title: '<b>Change Cheque Status</b>',
            //     html: true
            // }).on('shown.bs.popover', function() {

            //     var popover = $(this);

            //     var contentElche = popover.next(".popover").find(".popover-content");
            //     var myParameterche = popover.data('api-parameter');
            //     var res = myParameterche.split(",");
            //     // console.log(myParameterche);
            //     var resultche = '<form action="/cheque-collection/' + res[0] + '" id="popForm" method="post">' +
            //         '<input type="hidden" name="_method" value="PATCH">' +
            //         '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
            //         '<input type="hidden" name="pre_status" value="' + res[1] + '">' +
            //         '<div><label for="status">Status:</label>' +
            //         '<select name="status" id="status" class="form-control input-md"><option value="Collected by MO">Collected by MO</option><option value="Bounced">Bounced</option><option value="Encashed">Encashed</option><option value="Deposited into Bank">Deposited into Bank</option><option value="Sent to Office">Sent to Office</option></select><br>' +
            //         '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control"><br>' +
            //         '<button type="submit" class="btn btn-primary">Update</button>' +
            //         '</div></form>' + '<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';



            //     // contentEl.toggle();
            //     contentElche.html(resultche);
            // });
        });
        $(document).on('click', '.img-span', function() {
            if ($('.card-body').css('display') == "none") {
                $('.card-body').css('display', 'block');
                $('.img-show').css('display', 'none');
                $('.img-hide').css('display', 'block');
            } else {
                $('.card-body').css('display', 'none');
                $('.img-show').css('display', 'block');
                $('.img-hide').css('display', 'none');
            }
        });

        //Colection Amount Edit
        $(function() {
            $('[data-toggle="popover-edit"]').popover({
                trigger: 'click',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                placement: 'left',
                title: '<b>Edit Collection Amount</b>',
                html: true
            }).on('shown.bs.popover', function() {

                var popover = $(this);

                var contentElche = popover.next(".popover").find(".popover-content");
                var myParameterche = popover.data('api-parameter');
                var res = myParameterche.split(",");
                console.log(myParameterche);
                var resultche = '<form action="/edit-collection/' + res[0] +
                    '" id="popForm" method="post">' +
                    '<input type="hidden" name="_method" value="PATCH">' +
                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                    '<input type="hidden" name="pre_amount" value="' + res[1] + '">' +
                    '<label for="amount">Amount:</label>' +
                    '<input type = "number" step="0.1" min="0" name="amount" class="form-control"><br>' +
                    '<label for="remarks">Remarks:</label>' +
                    '<input type = "text" name="remarks" class="form-control"><br>' +
                    '<button type="submit" class="btn btn-primary">Update</button>' +
                    '</div></form>' +
                    '<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';
                // contentEl.toggle();
                contentElche.html(resultche);
            });
        });
    </script>
@endsection
