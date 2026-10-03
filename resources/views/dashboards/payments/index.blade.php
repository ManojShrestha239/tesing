@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>Cash Collections</h1>

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
                            <ul class="nav navbar-nav margin-bottom-35">
                                <li><a href="/collection-report">All Collection </a></li>
                                <li class="active"><a href="/collection">Cash Collection </a></li>
                                <li><a href="/cheque-collection">Cheque Collection</a></li>
                                <li><a href="/direct-collection">Direct</a></li>
                                <li><a href="/token">Token Collection</a></li>
                            </ul>

                        </nav>
                    </div>

                </div>

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <form method="post" class="page-content-container" action="/collection">
                    @csrf
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label bolds"> Officer<span
                                        class="required has-feedback{{ $errors->has('user') ? ' has-error' : '' }}">*</span></label>

                                <select class="form-control selectpicker" data-live-search="true"
                                    data-live-search-style="startsWith" name="user">
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
                                <label class="control-label bolds">From<span
                                        class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateFa" autocomplete="off" name="start-date"
                                        value="{{ $from ?? old('start-date') }}" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar">
                                </div>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('start-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('start-date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">

                                <label class="control-label bolds">To<span
                                        class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input id="nepaliDateF" type="text" name="end-date"
                                        value="{{ old('end-date', $to ?? '') }}" autocomplete="off" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar">
                                </div>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('end-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('end-date') }}</strong>
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
                @if (session('Status'))
                    <span class="help-block required">
                        <strong>{{ session('Status') }}</strong>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption" style="width:100%">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (empty($start))
                                            @if (!empty($mr))

                                                @if (empty($from) && empty($to))
                                                    Collection of The Day - {{ $mr }}
                                                @elseif(!empty($from) && empty($to))
                                                    Collection of {{ $mr }} - ({{ $from }} to Today)
                                                @else
                                                    Collection of {{ $mr }} - ({{ $from }} TO
                                                    {{ $to }})
                                                @endif
                                            @else
                                                @if (empty($from) && empty($to))
                                                    Collection of The Day
                                                @elseif(!empty($from) && empty($to))
                                                    Collection - ({{ $from }} to Today)
                                                @else
                                                    Collection- ({{ $from }} TO {{ $to }})
                                                @endif

                                            @endif
                                        @else
                                            Collection History
                                        @endif
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
                                                        style="padding-right: 5px;"></i>Summary</button>
                                            </form>
                                        </div>
                                        <div style="float:right" class="mr-3">
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
                                    </span>
                                </div>

                                <div class="uptable">
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($total, 2, '.', ',') }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discount * 15) / 100, 2, '.', ',') }}</td>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> Collection_id</th>
                                            <th> Dealer </th>
                                            <th> User </th>
                                            <th> Collection Date </th>
                                            <th> Payment Type </th>
                                            <th> Amount </th>
                                            <th> Previous Amount (rs.) </th>
                                            <th> Discount (rs.) </th>
                                            <th> TDS (rs.) </th>
                                            <th> Receipt Number </th>
                                            <th> Status</th>
                                            <th> Collection From</th>
                                            <th> Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($payment))

                                            @foreach ($payment as $lis)
                                                <tr>
                                                    <td> {{ $lis->id }}</td>
                                                    <td> {{ $lis->dealer }} </td>
                                                    <td> {{ $lis->user }} </td>
                                                    <td> {{ $lis->collection_date }} </td>
                                                    <td> {{ $lis->payment_type }} </td>
                                                    <td> {{ number_format($lis->amount, 2, '.', ',') }} </td>
                                                    <td>
                                                        @if (isset($lis->previous_amt))
                                                            {{ number_format($lis->previous_amt->amount, 2, '.', ',') }}
                                                        @endif
                                                    </td>
                                                    <td> {{ number_format($lis->discount, 2, '.', ',') }} </td>
                                                    <td> {{ number_format($lis->TDS, 2, '.', ',') }}</td>
                                                    <td> {{ $lis->receipt_num }} </td>
                                                    <td>
                                                        <div>
                                                            @if ($lis->status == 'Collected by MO')
                                                                <button type="button"
                                                                    data-api-parameter="{{ $lis->id }},{{ $lis->status }}"
                                                                    data-toggle="popoverchee"
                                                                    class="btn btn-warning btn-sm ab">{{ $lis->status }}</button>
                                                            @elseif ($lis->status == 'With MO')
                                                                <button type="button"
                                                                    data-api-parameter="{{ $lis->id }},{{ $lis->status }}"
                                                                    data-toggle="popoverchee"
                                                                    class="btn btn-info btn-sm ab">{{ $lis->status }}</button>
                                                            @elseif($lis->status == 'Blocked')
                                                                <button type="button"
                                                                    data-api-parameter="{{ $lis->id }},{{ $lis->status }}"
                                                                    data-toggle="popoverchee"
                                                                    class="btn btn-danger btn-sm ab">{{ $lis->status }}</button>
                                                            @else
                                                                <button type="button"
                                                                    data-api-parameter="{{ $lis->id }},{{ $lis->status }}"
                                                                    data-toggle="popoverchee"
                                                                    class="btn btn-success btn-sm ab">{{ $lis->status }}</button>
                                                            @endif
                                                        </div>
                                                        <div name="myFormche" data-toggle="popoverche"
                                                            class=" popover-content">
                                                        </div>
                                                    </td>
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
                var resultche = '<form action="/cheque-collection/' + res[0] +
                    '" id="popForm" method="post">' +
                    '<input type="hidden" name="_method" value="PATCH">' +
                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                    '<input type="hidden" name="pre_status" value="' + res[1] + '">' +
                    '<div><label for="status">Status:</label>' +
                    '<select name="status" id="status" class="form-control input-md"><option value="Collected by MO">Collected by MO</option><option value="sent to office">Sent to Office</option><option value="Received By Office">Received By Office</option></select><br>' +
                    '<label for="status">Remark:</label>' +
                    '<input type = "text" name="remark" class="form-control"><br>' +
                    '<button type="submit" class="btn btn-primary">Update</button>' +
                    '</div></form>' +
                    '<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';



                // contentEl.toggle();
                contentElche.html(resultche);
            });
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
