@extends('dashboards.sessions.layout')
@section('content')
<?php
$url = request()->segment(count(request()->segments()));
?>
<div class="page-content">
    <div class="breadcrumbs">
        <h1>All Orders</h1>

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

            {{-- Sidebar --}}
            @include('dashboards.orders.sidenavbar')


            <div class="page-content-col">
                @if (session('Status'))
                <div class="m-heading-1 border-green m-bordered">
                    <div class="page-content-col">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                            <form method="post" class="page-content-container" action="/dashboard/order">
                                @csrf
                                <input type="hidden" name="filter_type" value="all">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="control-label bolds">Area<span class="required"></span></label>
                                            <select class="form-control selectpicker" name="areacode" data-live-search="true" id="area_code">
                                                <option value="">All Area</option>
                                                @foreach ($areas as $area)
                                                <option value="{{ $area->areacode }}" @if (isset($areacode)) @if ($areacode==$area->areacode) selected @endif
                                                    @else @if (old('area') == $area->areacode) selected @endif
                                                    @endif>{{ $area->areacode }}-{{ $area->area }}</option>
                                                @endforeach
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry" data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="control-label bolds">Dealers<span class="required"></span></label>
                                            <select class="form-control selectpicker" name="dealer" data-live-search="true" id="dealer">
                                                <option value="">All Dealer</option>
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry" data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label class="control-label bolds">From<span class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                            <div class="date" data-provide="datepicker" class="form-control">
                                                <input type="text" id="nepaliDateFa" autocomplete="off" name="start-date" placeholder="YYYY-MM-DD" value="@if(isset($from)){{$from}}@else{{old('start-date')}}@endif" class="form-control nepali-calendar" required>
                                            </div>
                                            <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
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
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label class="control-label bolds">To<span class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                            <div class="date" data-provide="datepicker" class="form-control">
                                                <input type="text" id="nepaliDateF" autocomplete="off" name="end-date" placeholder="YYYY-MM-DD" value="@if(isset($to)){{$to}}@else{{old('end-date')}}@endif" class="form-control nepali-calendar" required>
                                            </div>
                                            <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                            @if ($errors->has('end-date'))
                                            <span class="help-block required">
                                                <strong>{{ $errors->first('end-date') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary" style="margin-top:24px;width: 100%;">View</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-md-12">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light portlet-fit bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class=" fa fa-paint-brush font-red"></i>
                                                <span class="caption-subject font-red bold uppercase">
                                                    Orders @if ($de) of
                                                    {{ $de }} @endif - @if ($from)
                                                    ({{ $from }} TO {{ $to }})
                                                    @endif
                                                </span>
                                                <br>
                                                <br>


                                                {{-- <span style="font-weight:600">Total Order Amount </span> - {{number_format($total, 2, ".", ",")}} &nbsp; &nbsp;
                                                @isset($pending)
                                                <span style="font-weight:600">Total Pending Amount </span> - {{$number_format(pending, 2, ".", ",")}} &nbsp; &nbsp;
                                                @endisset
                                                @isset($cancelled)
                                                <span style="font-weight:600">Total Cancelled Amount </span> - {{$number_format(cancelled, 2, ".", ",")}}&nbsp; &nbsp;
                                                @endisset
                                                @isset($completed)
                                                <span style="font-weight:600">Total Completed Amount </span> - {{$number_format(completed, 2, ".", ",")}}&nbsp; &nbsp;
                                                @endisset --}}
                                            </div>
                                            <div class="uptable">
                                                <div class="row">
                                                    <div class="column">
                                                        <table class="up">
                                                            <tr>
                                                                <th>Total Order Amt.</th>
                                                                {{-- @isset($pending)<th>Total Pending Amt.</th>@endisset
                                                                    @isset($cancelled)<th>Total Cancelled Amt.</th>@endisset
                                                                    @isset($completed)<th>Total Completed Amt.</th>@endisset --}}
                                                            </tr>
                                                            <tr>
                                                                <td>Rs.{{ number_format($total, 2, '.', ',') }}</td>
                                                                {{-- @isset<td>Rs.{{ number_format($pending, 2, '.', ',') }}</td>@endisset
                                                                @isset<td>Rs.{{ number_format($cancelled, 2, '.', ',') }}</td>@endisset
                                                                @isset<td>Rs.{{ number_format($completed, 2, '.', ',') }}</td>@endisset --}}
                                                        </table>
                                                    </div>
                                                    @isset($pending)
                                                    <div class="column">
                                                        <table class="up">
                                                            <tr>
                                                                <th>Total Pending Amt.</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Rs.{{ number_format($pending, 2, '.', ',') }}
                                                                </td>
                                                        </table>
                                                    </div>
                                                    @endisset

                                                    @isset($ongoing)
                                                    <div class="column">
                                                        <table class="up">
                                                            <tr>
                                                                <th>Total Ongoing Amt.</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Rs.{{ number_format($ongoing, 2, '.', ',') }}
                                                                </td>
                                                        </table>
                                                    </div>
                                                    @endisset

                                                    @isset($cancelled)
                                                    <div class="column">
                                                        <table class="up">
                                                            <tr>
                                                                <th>Total Cancelled Amt.</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Rs.{{ number_format($cancelled, 2, '.', ',') }}
                                                                </td>
                                                        </table>
                                                    </div>
                                                    @endisset

                                                    @isset($completed)
                                                    <div class="column">
                                                        <table class="up">
                                                            <tr>
                                                                <th>Total Completed Amt.</th>
                                                            </tr>
                                                            <tr>
                                                                <td>Rs.{{ number_format($completed, 2, '.', ',') }}
                                                                </td>
                                                        </table>
                                                    </div>
                                                    @endisset
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8"></div>
                                                <div class="col-md-4">
                                                    <div style="display:flex;justify-content: space-between;">
                                                        <form action="/dashboard/orderReport" method="post">
                                                            @csrf
                                                            <input type="hidden" name="user" value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                            <input type="hidden" name="from" value="@if (isset($from)) {{ $from }} @endif">
                                                            <input type="hidden" name="to" value="@if (isset($to)) {{ $to }} @endif">

                                                            <button class="btn btn-primary btn-xs "><i class="fa fa-download" style="padding-right: 5px;"></i>Excel</button>
                                                        </form>

                                                        <form action="/dashboard/orderCollectionReport" method="post">
                                                            @csrf
                                                            <input type="hidden" name="user" value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                            <input type="hidden" name="from" value="@if (isset($from)) {{ $from }} @endif">
                                                            <input type="hidden" name="to" value="@if (isset($to)) {{ $to }} @endif">

                                                            <button class="btn btn-primary btn-xs"><i class="fa fa-download" style="padding-right: 5px;"></i>Order Colletion</button>
                                                        </form>

                                                        <form action="/dashboard/orderTrackingReport" method="post">
                                                            @csrf
                                                            <input type="hidden" name="user" value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                            <input type="hidden" name="from" value="@if (isset($from)) {{ $from }} @endif">
                                                            <input type="hidden" name="to" value="@if (isset($to)) {{ $to }} @endif">

                                                            <button class="btn btn-primary btn-xs "><i class="fa fa-download" style="padding-right: 5px;"></i>Order Tracking</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div> <a href="/dashboard/orderReport/orderReport?user=@if (isset($dealercode)) {{ $dealercode }} @endif&from=@if (isset($dealercode)) {{ $dealercode }} @endif"> download </a> </div> -->
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-bordered table-hover" id="sample_2">
                                                <thead>
                                                    <tr>
                                                        <th style="font-weight: 550;"> ORDER N0.</th>
                                                        <th style="font-weight: 550;"> DATE </th>
                                                        <th style="font-weight: 550;"> DEALER INFO </th>
                                                        <th style="font-weight: 550;"> ORDER BY: </th>
                                                        <th style="font-weight: 550;"> UPDATED DATE</th>
                                                        <th style="font-weight: 550;">ORDER AMOUNT</th>
                                                        <th style="font-weight: 550;"> ORDER STATUS </th>
                                                        <th style="font-weight: 550;"> REMARKS</th>
                                                        {{-- <th style="font-weight: 550;"> DUE</th>
                                                            <th style="font-weight: 550;"> LAST COLLECTION DATE</th>
                                                            <th style="font-weight: 550;"> LAST COLLECTION</th>
                                                         <th style="font-weight: 550;"> DETAILS </th>
                                                            <th style="font-weight: 550;"> VIEW LOG</th> --}}
                                                        <th style="font-weight: 550;">Action</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($order))

                                                    @foreach ($order as $ordr)
                                                    {{-- {{$id = $ordr['order_status']}} --}}
                                                    <tr @if ($ordr['risk_zone'])
                                                        @if($ordr['risk_zone']=="red")
                                                        style="background-color:rgba(255, 0, 0, 0.3)"
                                                        @elseif ($ordr['risk_zone'] == "blue")
                                                        style="background-color:rgba(0, 0, 255, 0.3)"
                                                        @elseif ($ordr['risk_zone'] == "green")
                                                        style="background-color:rgba(0, 255, 0, 0.3)"
                                                        @endif
                                                        @endif
                                                        >
                                                        <td> {{ $ordr['order_id'] }} </td>
                                                        <td> {{ $ordr['order_date'] }} </td>
                                                        <td> {{ $ordr['dealer_name'] }}({{ $ordr['dealer_code'] }})
                                                        </td>
                                                        <td> {{ $ordr['user_name'] }}</td>
                                                        <td> {{ $ordr['updated_at'] }} </td>
                                                        <td>Rs. {{ number_format(($ordr['orderamt']+$ordr['vat']-$ordr['discount']), 2, ".", ",") }} </td>
                                                        <input type="hidden" class="order-hover" id="{{ $ordr['order_status'] }}">
                                                        <script>
                                                            $(document).ready(function() {
                                                                var id = $(".order-hover").attr('id');
                                                                $("." + id).attr('id', 'quickNav');
                                                            });
                                                        </script>
                                                        <td>


                                                            <div>
                                                                <?php
                                                                $order_status = strtoupper($ordr['order_status']);
                                                                ?>
                                                                @if ($order_status == 'PENDING')
                                                                <button type="button" data-api-parameter="{{ $ordr['order_id'] }},{{ $ordr['order_status'] }},{{ $ordr['due'] }},{{ $ordr['remarks'] }},{{ $ordr['last_collection_date'] }},{{ $ordr['last_collection'] }},{{ $ordr['delivery_time'] }},{{ $ordr['delivery_cost'] }},{{$ordr['dealer_discount']}},{{ $ordr['vat'] }}" data-toggle="popover" class="btn btn-warning btn-sm ab">{{ $ordr['order_status'] }}</button>
                                                                @elseif ($order_status == 'APPROVED')
                                                                <button type="button" data-api-parameter="{{ $ordr['order_id'] }},{{ $ordr['order_status'] }},{{ $ordr['due'] }},{{ $ordr['remarks'] }},{{ $ordr['last_collection_date'] }},{{ $ordr['last_collection'] }},{{ $ordr['delivery_time'] }},{{ $ordr['delivery_cost'] }},{{$ordr['dealer_discount']}},{{ $ordr['vat'] }}" data-toggle="popover" class="btn btn-success btn-sm ab">{{ $ordr['order_status'] }}</button>
                                                                @elseif($order_status == 'DECLINED')
                                                                <button type="button" data-api-parameter="{{ $ordr['order_id'] }},{{ $ordr['order_status'] }},{{ $ordr['due'] }},{{ $ordr['remarks'] }},{{ $ordr['last_collection_date'] }},{{ $ordr['last_collection'] }},{{ $ordr['delivery_time'] }},{{ $ordr['delivery_cost'] }},{{$ordr['dealer_discount']}},{{ $ordr['vat'] }}" data-toggle="popover" class="btn btn-danger btn-sm ab">{{ $ordr['order_status'] }}</button>
                                                                @elseif($order_status == 'SENT TO DISPTACH')
                                                                <button type="button" data-api-parameter="{{ $ordr['order_id'] }},{{ $ordr['order_status'] }},{{ $ordr['due'] }},{{ $ordr['remarks'] }},{{ $ordr['last_collection_date'] }},{{ $ordr['last_collection'] }},{{ $ordr['delivery_time'] }},{{ $ordr['delivery_cost'] }},{{$ordr['dealer_discount']}},{{ $ordr['vat'] }}" data-toggle="popover" class="btn btn-info btn-sm ab">{{ $ordr['order_status'] }}</button>
                                                                @else
                                                                <button type="button" data-api-parameter="{{ $ordr['order_id'] }},{{ $ordr['order_status'] }},{{ $ordr['due'] }},{{ $ordr['remarks'] }},{{ $ordr['last_collection_date'] }},{{ $ordr['last_collection'] }},{{ $ordr['delivery_time'] }},{{ $ordr['delivery_cost'] }},{{$ordr['dealer_discount']}},{{ $ordr['vat'] }}" data-toggle="popover" class="btn btn-secondary btn-sm ab">{{ $ordr['order_status'] }}</button>
                                                                @endif
                                                            </div>
                                                            <div name="myForm" data-toggle="popover" class="hide popover-content"></div>
                                                        </td>
                                                        <td>{{ $ordr['remarks'] }}</td>
                                                        {{-- <td>
                                                                        @if (empty($ordr['due']))
                                                                            0
                                                                        @else
                                                                            {{ $ordr['due'] }}
                                                        @endif
                                                        </td>
                                                        <td>{{ $ordr['last_collection_date'] }}
                                                        </td>
                                                        <td>
                                                            @if (empty($ordr['last_collection']))
                                                            0
                                                            @else
                                                            {{ $ordr['last_collection'] }}
                                                            @endif
                                                        </td> --}}
                                                        {{-- <td>
                                                                        <a class="order-view-popup"
                                                                            id="{{ $ordr['order_id'] }}">
                                                        <div class="btn btn-primary btn-sm" onclick="document.getElementById('order-popup').style.display='block'">
                                                            View</div>
                                                        </a>
                                                        <!-- <a class = "allorder-view-popup" id = "{{ $ordr['order_id'] }}">
                                                                            <div class="btn btn-primary btn-sm" onclick = "document.getElementById('order-popup').style.display='block'">All Order</div>
                                                                        </a> -->
                                                        </td> --}}

                                                        <td>
                                                            <a class="order-view-popup-log" id="{{ $ordr['order_id'] }}">
                                                                <div class="btn btn-primary btn-sm" onclick="document.getElementById('order-popup').style.display='block'">
                                                                    View Log</div>
                                                            </a>
                                                            <a class="order-view-popup" id="{{ $ordr['order_id'] }}">
                                                                <div class="btn btn-primary btn-sm" onclick="document.getElementById('order-popup').style.display='block'">
                                                                    View</div>
                                                            </a>
                                                            <a class="orderdetail-view-popup" id="{{ $ordr['order_id'] }}">
                                                                <div class="btn btn-info btn-sm" onclick="document.getElementById('order-popup').style.display='block'">
                                                                    Detail</div>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- pop up after clicking view --}}
                                        <div id="order-popup">

                                            {{-- href="/dashboard/order/{{ $ordr->order_id }}" --}}

                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $(".order-view-popup").click(function() {
                                                    var id = $(this).attr('id');
                                                    $("#order-popup").load("/dashboard/order/" + id);
                                                    $("#order-pop").css("display", "block");
                                                    var modal = document.getElementById('order-popup');
                                                    window.onclick = function(event) {
                                                        if (event.target == modal) {
                                                            modal.style.display = "none";
                                                        }
                                                    }
                                                });

                                                $(".order-view-popup-log").click(function() {
                                                    var id = $(this).attr('id');
                                                    $("#order-popup").load("/dashboard/orderpop/" + id);
                                                    $("#order-pop").css("display", "block");
                                                    var modal = document.getElementById('order-popup');
                                                    window.onclick = function(event) {
                                                        if (event.target == modal) {
                                                            modal.style.display = "none";
                                                        }
                                                    }
                                                });
                                                $(".allorder-view-popup").click(function() {
                                                    var id = $(this).attr('id');
                                                    $("#order-popup").load("/dashboard/allorder/" + id);
                                                    $("#order-pop").css("display", "block");
                                                    var modal = document.getElementById('order-popup');
                                                    window.onclick = function(event) {
                                                        if (event.target == modal) {
                                                            modal.style.display = "none";
                                                        }
                                                    }
                                                });
                                                $(".orderdetail-view-popup").click(function() {
                                                    var id = $(this).attr('id');
                                                    $("#order-popup").load("/dashboard/orderdetails/" + id);
                                                    $("#order-pop").css("display", "block");
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
                                    <!-- END EXAMPLE TABLE PORTLET-->
                                </div>

                                <!-- END SIDEBAR CONTENT LAYOUT -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var order = <?= json_encode($order, true) ?>;
    $(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'click',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            placement: 'right',
            title: '<b>Change Order Status</b>',
            // title:' <button onclick="closePopover();" class="btn btn-danger">Close</button>',
            html: true
        }).on('shown.bs.popover', function() {

            var popover = $(this);

            var contentEl = popover.next(".popover").find(".popover-content");
            var myParameter = popover.data('api-parameter');
            var res = myParameter.split(",");

            // console.log(myParameter);
            // if (res[1] == "Pending") {
            //     var option = '<option value="Account">Account</option>' +
            //         // '<option value="Inventory">Inventory</option>' +
            //         '<option value="Declined">Declined</option></select><br>';
            //     var input = '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control" required><br>'
            // }
            // //  else if (res[1] == "Inventory") {
            // //     var option = '<option value="Account">Account</option>' +
            // //         '<option value="Declined">Declined</option></select><br>';
            // //     var input = '<label for="status">Remark:</label>' +
            // //         '<input type = "text" name="remark" class="form-control" required>'
            // // }
            //  else if (res[1] == "Account") {
            //     var option = '<option value="Approved">Approved</option>' +
            //         // '<option value="Sent to Dispatch">Sent to Dispatch</option>' +
            //         // '<option value="Dispatched">Goods Dispatched</option>' +
            //         '<option value="Declined">Declined</option></select>';
            //     var input = '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control" required>'
            // } else if (res[1] == "Approved") {
            //     var option = '<option value="Sent to Dispatch">Sent to Dispatch</option>' +
            //         // '<option value="Approved">Approved</option>' +
            //         // '<option value="Dispatched">Goods Dispatched</option>' +
            //         '<option value="Declined">Declined</option></select>';
            //     var input = '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control" required>'
            //         // '<label for="delivery_time" class="status-popup">Delivery Time:</label>' +
            //         // '<input type = "number" placeholder="Days" name="delivery_time" class="form-control status-popup" min="1" step="1"><br>' +
            //         // '<label for="delivery_cost" class="status-popup">Delivery Charge:</label>' +
            //         // '<input type = "number" placeholder="Rs." name="delivery_cost" class="form-control status-popup" min="0" step="1">'
            // } else if (res[1] == "Sent to Dispatch") {
            //     var option =
            //         // '<option value="Sent to Dispatch">Sent to Dispatch</option>' +
            //         // '<option value="Approved">Approved</option>' +
            //         '<option value="Dispatched">Goods Dispatched</option>' +
            //         '<option value="Declined">Declined</option></select>';
            //     var input = '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control" required><br>' +
            //         '<label for="delivery_time" class="status-popup">Delivery Time:</label>' +
            //         '<input type = "number" placeholder="Days" name="delivery_time" class="form-control status-popup" min="1" step="1"><br>' +
            //         '<label for="delivery_cost" class="status-popup">Delivery Charge:</label>' +
            //         '<input type = "number" placeholder="Rs." name="delivery_cost" class="form-control status-popup" min="0" step="1">'
            // } else if (res[1] == "Dispatched") {
            //     var option = '<option value="Approved">Approved</option>' +
            //         '<option value="Sent to Dispatch">Sent to Dispatch</option>' +
            //         // '<option value="Dispatched">Goods Dispatched</option>' +
            //         '<option value="Declined">Declined</option></select>';
            //     var input = '<label for="status">Remark:</label>' +
            //         '<input type = "text" name="remark" class="form-control" required><br>'
            //     // '<label for="delivery_time" class="status-popup">Delivery Time:</label>' +
            //     // '<input type = "number" placeholder="Days" name="delivery_time" class="form-control status-popup" min="1" step="1" required><br>'
            // } else {
            //     // return true;
            //     // var option = '<option value="Approved">Approved</option>' +
            //     //     '<option value="Sent to Dispatch">Sent to Dispatch</option>' +
            //     //     '<option value="Dispatched">Goods Dispatched</option>' +
            //     //     '<option value="Declined">Declined</option></select>';
            //     // var input = '<label for="status">Remark:</label>' +
            //     //     '<input type = "text" name="remark" class="form-control" required><br>' + '<br>'
            //     var option = '';
            //     var input = '';
            // }
            var pending = '';
            var accounted = '';
            var inventory = '';
            var approved = '';
            var ongoing = '';
            var declined = '';
            var dispatched = '';
            var account = '';
            // console.log(res[9]);
            if (res[1] == 'Pending') {
                pending = 'selected';
            }
            // else if (res[1] == 'Inventory') {
            //     inventory = 'selected';
            // }
            else if (res[1] == 'Sent To Account') {
                accounted = 'selected';
            } else if (res[1] == 'Account') {
                account = 'selected';
            } else if (res[1] == 'Approved') {
                approved = 'selected';
            } else if (res[1] == 'Sent to Dispatch') {
                ongoing = 'selected';
            } else if (res[1] == 'Dispatched') {
                dispatched = 'selected';
            } else if (res[1] == 'Declined') {
                var declined = 'selected';
            } else {
                var delivered = 'selected';
            }
            var option =
                '<option ' + pending + ' value="Pending">Pending/Inventory</option>' +
                // '<option ' + inventory + ' value="Inventory">Inventory</option>' +
                '<option ' + accounted + ' value="Sent To Account">Sent To Account</option>' +
                '<option ' + account + ' value="Account">Account</option>' +
                '<option ' + approved + ' value="Approved">Approved</option>' +
                '<option ' + ongoing + ' value="Sent to Dispatch">Sent to Dispatch</option>' +
                '<option ' + dispatched + ' value="Dispatched">Goods Dispatched</option>' +
                '<option ' + delivered + ' value="Delivered">Delivered</option>' +
                '<option ' + declined + ' value="Declined">Declined</option>';

            var input;
            // $.each(order, function(as, value) {
            //     console.log(value.order_id);
            //     if (value.order_id == res[0]) {
            input = '<label for="status">Remark:</label>' +
                '<input type = "text" name="remark" class="form-control" value="' + res[3] +
                '" required>' +
                '<label for="delivery_time" class="status-input">Tentative Delivery Time(Days):</label>' +
                '<input type ="number" placeholder="Days" name="delivery_time" value="' +
                res[6] +
                '" class="form-control status-input"  min="1" step="1">' +
                '<label for="delivery_cost" class="status-input">Freight Charge(%):</label>' +
                '<input type = "number" placeholder="0.00%" name="delivery_cost" value="' +
                res[7] +
                '" class="form-control status-input" min="0" step="0.01">' +
                '<label for="due" class="status-input">Due Amount:</label>' +
                '<input type = "number" placeholder="Rs." name="due" value="' + res[2] +
                '" class="form-control status-input" min="0" step="1">' +
                '<label for="last_collection_date" class="status-input">Last Collection Date:</label>' +
                '<input type = "date" placeholder="2022-05-17" name="last_collection_date" value="' +
                res[4] +
                '" class="form-control status-input" min="0" step="1">' +
                '<label for="last_collection" class="status-input">Last Collection Amount:</label>' +
                '<input type = "number" placeholder="Rs." name="last_collection" value="' +
                res[5] +
                '" class="form-control status-input" min="0" step="1">' +
                '<label for="riskzone" class="status-input">Risk Zone:</label>' +
                '<select  name="riskzone" id="riskzone" class="status-input form-control input-md" >' +
                '<option value="red">Red</option>' +
                '<option value="blue">Blue</option>' +
                '<option value="green">Green</option>' +
                ' </select>' +
                '<label for="dealer_discount" class="status-input">Dealer Discount:</label>' +
                '<input type = "number" disabled placeholder="Rs." name="dealer_discount" value="' +
                res[8] +
                '" class="form-control status-input" min="0" step="1">' +
                '<label for="vat_amt" class="status-input">Tax :</label>' +
                '<input type = "number" disabled placeholder="Rs." name="vat_amt" value="' +
                res[9] +
                '" class="form-control status-input" min="0" step="1">' +
                '<label for="need_correction?" class="status-input-declined">Need Correction</label>' +

                '<div class="status-input-declined "><input type="checkbox"  name="needcorrection" id="needcorrection" value=1 ><label for="needcorrection"> Yes</label></div>';
            //     }
            // });


            var result = '<form action="/dashboard/order/' + res[0] + '" id="popForm" method="post">' +
                '<input type="hidden" name="_method" value="PATCH">' +
                '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                '<input type="hidden" name="previous_status" value="' + res[1] + '">' +
                '<div><label for="status">Status:</label>' +
                '<select name="status" id="status" class="form-control input-md" required>' +
                option + '</select>' +
                input +
                '<button type="submit" class="btn btn-primary">Update</button>' +

                '</div></form>' +
                '<img class="closePopup" style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset("assets/pages/img/quit.svg") }}>';


            // contentEl.toggle();
            contentEl.html(result);
        });
    });

	$(document).on('change', '#status', function(e) {
		// var del_time = $("input[name='delivery_time']");
		// var del_cost = $("input[name='delivery_cost']");
		// console.log($(this).val());
		if ($(this).val() == "Account") {
			$('.status-input').css('display', 'block');
		} else {
			$('.status-input').css('display', 'none');
		}
		if ($(this).val() == "Declined") {
			$('.status-input-declined').css('display', 'block');
		} else {
			$('.status-input-declined').css('display', 'none');
		}
	})

    $(document).on('click', '#popoverClose', function(e) {
        console.log("abc");
        // setTimeout(() => {
        //     e.stopPropagation();
        // }, 200);
    });

    $(document).on('click', '.ab', function(e) {
        setTimeout(() => {
            $($("#status").trigger("change"));
        }, 200);
        // var del_time = $("input[name='delivery_time']");
        // var del_cost = $("input[name='delivery_cost']");
        // console.log($('#status').val);
        // return true;
        // if ($('#status').val() == "Sent To Account") {
        //     $('.status-input').css('display', 'block');
        // } else {
        //     $('.status-input').css('display', 'none');
        // }
    });
    $(document).on('change', '#status', function(e) {
        // var del_time = $("input[name='delivery_time']");
        // var del_cost = $("input[name='delivery_cost']");
        // console.log($(this).val());
        if ($(this).val() == "Account") {
            $('.status-input').css('display', 'block');
        } else {
            $('.status-input').css('display', 'none');
        }
    })
    $(document).on('click', '#popoverClose', function(e) { //,'#popoverClose'
        $('.popover').each(function() {
            var popover = $(this).data('bs.popover');
            // console.log(e.target);
            // console.log(popover);
            // return true;
            if (!popover.$element.is(e.target)) {
                popover.inState.click = false;
                popover.hide();
            }
        });
    });
    $(document).on('change', '#area_code', function(e) {
        let areacode = $(this).val();

        $.ajax({
            url: '/dashboard/dealerlist?areacode=' + areacode,
            type: 'get',
            dataType: 'json',
            success: function(data) {

                $('#dealer').empty();
                $("#dealer").append('<option value="">All Dealer</option>');
                data.dealers.forEach(function(e, index) {
                    $("#dealer").append('<option value="' + e.code + '">' + e.code + '-' + e.name + '</option>');
                });
                $("#dealer").selectpicker("refresh");
            }
        })
    });
    window.onload = function() {
        $("#area_code").trigger("change");
        // setTimeout(() => {

        // }, 200);
    }
</script>
@endsection