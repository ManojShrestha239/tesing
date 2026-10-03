@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <?php
    $url = request()->segment(count(request()->segments()));
    ?>
    <style>
        .active {
            color: white !important;
        }

        .status-input-declined {}
    </style>
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
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>
                            {{-- {{ dd($order) }} --}}
                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <ul class="nav navbar-nav margin-bottom-35 orders-class">
                                <li @if ($status == 'all' || $status == 'All') class="active" @endif><a href="/order">All Orders
                                    </a></li>
                                <li @if ($status == 'orderrequest' || $status == 'OrderRequest') class="active" @endif><a href="/orderRequest"> Order
                                        Request</a></li>
                                <li @if ($status == 'pending' || $status == 'Pending') class="active" @endif><a class="pending"
                                        href="/orders/Pending">Pending/Inventory </a></li>
                                <li @if ($status == 'Approved' || $status == 'approved') class="active" @endif><a class="approved"
                                        href="/orders/approved">Account/Approved </a></li>
                                <li @if ($status == 'Ongoing' || $status == 'ongoing') class="active" @endif><a class="ongoing"
                                        href="/orders/ongoing">Ongoing </a></li>
                                <li @if ($status == 'Completed' || $status == 'completed') class="active" @endif><a class="completed"
                                        href="/orders/completed">Completed </a></li>
                                <li @if ($status == 'Cancelled' || $status == 'cancelled') class="active" @endif><a class="cancelled"
                                        href="/orders/cancelled">Cancelled </a></li>
                                <li @if ($url == 'Draft' || $url == 'draft') class="active" @endif><a class="draft"
                                        href="/orders/draft">Draft </a></li>
                            </ul>

                        </nav>
                    </div>

                </div>

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
                                <form method="get" class="page-content-container" action="/orders/{{ $status }}">
                                    <input type="hidden" name="filter_type" value="{{ $status }}">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">Area<span
                                                        class="required"></span></label>
                                                <select class="form-control selectpicker" name="areacode"
                                                    data-live-search="true" id="area_code">
                                                    <option value="">All Area</option>
                                                    @foreach ($areas as $area)
                                                        <option value="{{ $area->areacode }}"
                                                            @if (isset($areacode)) @if ($areacode == $area->areacode)
													selected @endif
                                                        @else @if (old('area') == $area->areacode) selected @endif
                                                            @endif
                                                            >{{ $area->areacode }}-{{ $area->area }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                    data-valmsg-replace="true"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">Dealers<span
                                                        class="required">*</span></label>
                                                <select class="form-control selectpicker" name="dealer"
                                                    data-live-search="true" id="dealer">
                                                    <option value="">All</option>
                                                    @foreach ($dealers as $dealer)
                                                        <option value="{{ $dealer->code }}"
                                                            @if (isset($dealercode)) @if ($dealercode == $dealer->code)
													selected @endif
                                                        @else @if (old('dealer') == $dealer->code) selected @endif
                                                            @endif
                                                            >{{ $dealer->code }}-{{ $dealer->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                    data-valmsg-replace="true"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label bolds">From<span
                                                        class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateFa" autocomplete="off"
                                                        name="start-date" placeholder="YYYY-MM-DD"
                                                        value="{{ $from ?? old('start-date') }}"
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
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label bolds">To<span
                                                        class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateF" autocomplete="off"
                                                        name="end-date" placeholder="YYYY-MM-DD"
                                                        value="{{ $to ?? old('end-date') }}"
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
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="portlet light portlet-fit bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class=" fa fa-paint-brush font-red"></i>
                                                    <span class="caption-subject font-red bold uppercase">
                                                        @if (!empty($de))

                                                            @if (empty($from) && empty($to))
                                                                Orders of The Day - {{ $de }}
                                                            @elseif(!empty($from) && empty($to))
                                                                Orders of {{ $de }} - ({{ $from }}
                                                                to Today)
                                                            @else
                                                                Orders of {{ $de }} - ({{ $from }}
                                                                TO {{ $to }})
                                                            @endif
                                                        @else
                                                            @if (empty($from) && empty($to))
                                                                Orders of The Day
                                                            @elseif(!empty($from) && empty($to))
                                                                Orders - ({{ $from }} to Today)
                                                            @else
                                                                Orders - ({{ $from }} TO {{ $to }})
                                                            @endif

                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="uptable">
                                                    <div class="row">
                                                        <div class="column">
                                                            <table class="up">
                                                                <tr>
                                                                    <th>Total Order Amt.</th>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rs.{{ number_format($total, 2, '.', ',') }}</td>
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
                                                            <form action="/orderReport" method="post">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="{{ $url }}">
                                                                <input type="hidden" name="user"
                                                                    value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                                <input type="hidden" name="from"
                                                                    value="@if (isset($from)) {{ $from }} @endif">
                                                                <input type="hidden" name="to"
                                                                    value="@if (isset($to)) {{ $to }} @endif">

                                                                <button class="btn btn-primary btn-xs "><i
                                                                        class="fa fa-download"
                                                                        style="padding-right: 5px;"></i>Excel</button>
                                                            </form>

                                                            <form action="/orderCollectionReport" method="post">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="{{ $url }}">
                                                                <input type="hidden" name="user"
                                                                    value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                                <input type="hidden" name="from"
                                                                    value="@if (isset($from)) {{ $from }} @endif">
                                                                <input type="hidden" name="to"
                                                                    value="@if (isset($to)) {{ $to }} @endif">

                                                                <button class="btn btn-primary btn-xs"><i
                                                                        class="fa fa-download"
                                                                        style="padding-right: 5px;"></i>Order
                                                                    Colletion</button>
                                                            </form>

                                                            <form action="/orderTrackingReport" method="post">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="{{ $url }}">
                                                                <input type="hidden" name="user"
                                                                    value="@if (isset($dealercode)) {{ $dealercode }} @endif">
                                                                <input type="hidden" name="from"
                                                                    value="@if (isset($from)) {{ $from }} @endif">
                                                                <input type="hidden" name="to"
                                                                    value="@if (isset($to)) {{ $to }} @endif">

                                                                <button class="btn btn-primary btn-xs "><i
                                                                        class="fa fa-download"
                                                                        style="padding-right: 5px;"></i>Order
                                                                    Tracking</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <table class="table table-striped table-bordered table-hover"
                                                    id="sample_2">
                                                    <thead>
                                                        <tr>
                                                        <tr>
                                                            <th style="font-weight: 550;"> ORDER N0.</th>
                                                            <th style="font-weight: 550;"> DATE </th>
                                                            <th style="font-weight: 550;"> DEALER INFO </th>
                                                            <th style="font-weight: 550;"> ORDER BY: </th>
                                                            <th style="font-weight: 550;"> UPDATED DATE</th>
                                                            <th style="font-weight: 550;">ORDER AMOUNT</th>
                                                            <th style="font-weight: 550;"> ORDER STATUS </th>
                                                            <th style="font-weight: 550;"> REMARKS</th>
                                                            <th style="font-weight: 550;"> ORDER FROM</th>
                                                            <th style="font-weight: 550;"> Action </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($order as $ordr)
                                                            {{-- {{$id = $ordr->status}} --}}
                                                            <tr @if ($ordr->risk_zone) @if ($ordr->risk_zone == 'red')
														style="background-color:rgba(255, 0, 0, 0.3)"
														@elseif ($ordr->risk_zone == 'blue')
														style="background-color:rgba(0, 0, 255, 0.3)"
														@elseif ($ordr->risk_zone == 'green')
														style="background-color:rgba(0, 255, 0, 0.3)" @endif
                                                                @endif>
                                                                <td> {{ $ordr->id }} </td>
                                                                <td> {{ $helpers->convertDate($ordr->date) }} </td>
                                                                <td> {{ $ordr->dealer['name'] }} -
                                                                    ({{ $ordr->dealer_code }})
                                                                </td>
                                                                <td> {{ $ordr->user['name'] }}</td>
                                                                <td> {{ $helpers->convertDate(explode(' ', $ordr->updated_at)[0]) }}
                                                                    {{ explode(' ', $ordr->updated_at)[1] }}
                                                                </td>
                                                                <td>Rs.
                                                                    {{ number_format($ordr->total_price + $ordr->vat - $ordr->discount, 2, '.', ',') }}
                                                                </td>
                                                                <input type="hidden" class="order-hover"
                                                                    id="{{ $ordr->status }}">
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        var id = $(".order-hover").attr('id');
                                                                        $("." + id).attr('id', 'quickNav');
                                                                    });
                                                                </script>
                                                                <td>
                                                                    <div>
                                                                        @if ($ordr->status == 'Pending')
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                data-toggle="popover"
                                                                                class="btn btn-warning btn-sm ab">{{ $ordr->status }}</button>
                                                                        @elseif ($ordr->status == 'Approved')
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                data-toggle="popover"
                                                                                class="btn btn-success btn-sm ab">{{ $ordr->status }}</button>
                                                                        @elseif ($ordr->status == 'Declined')
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                class="btn btn-danger btn-sm"
                                                                                disabled>{{ $ordr->status }}</button>
                                                                        @elseif ($ordr->status == 'Delivered')
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                class="btn btn-success btn-sm"
                                                                                disabled>{{ $ordr->status }}</button>
                                                                        @elseif($ordr->status == 'Sent to Dispatch')
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                data-toggle="popover"
                                                                                class="btn btn-info btn-sm ab">{{ $ordr->status }}</button>
                                                                        @else
                                                                            <button type="button"
                                                                                data-api-parameter="{{ $ordr->id }},{{ $ordr->status }},{{ $ordr->due }},{{ $ordr->remarks }},{{ $ordr->last_collection_date }},{{ $ordr->last_collection }},{{ $ordr->delivery_time }},{{ $ordr->delivery_cost }},{{ $ordr->discount }},{{ $ordr->vat }}"
                                                                                data-toggle="popover"
                                                                                class="btn btn-success btn-sm ab">{{ $ordr->status }}</button>
                                                                        @endif
                                                                    </div>
                                                                    <div name="myForm" data-toggle="popover"
                                                                        class="hide popover-content"></div>
                                                                </td>
                                                                <td id="remarks1001">{{ $ordr->remarks }}</td>
                                                                <td>
                                                                    @if (isset($ordr->lat_lng) && !empty($ordr->lat_lng))
                                                                        @php
                                                                            [$lat, $lng] = explode(',', $ordr->lat_lng);
                                                                        @endphp
                                                                        <form action="{{ route('origin', $ordr->id) }}"
                                                                            method="post" target="_blank">
                                                                            @csrf
                                                                            <input type="hidden" name="type"
                                                                                value="order">
                                                                            <button type="submit"
                                                                                class="btn btn-primary btn-sm">View
                                                                                on Map</button>
                                                                        </form>
                                                                    @else
                                                                        {{ 'N/A' }}
                                                                    @endif
                                                                </td>
                                                                <td>

                                                                    <a class="order-view-popup"
                                                                        id="{{ $ordr->id }}">
                                                                        <div class="btn btn-primary btn-sm"
                                                                            style="margin-bottom: 3px;"
                                                                            onclick="document.getElementById('order-popup').style.display='block'">
                                                                            View</div>
                                                                    </a>
                                                                    <a class="order-view-popup-log"
                                                                        id="{{ $ordr->id }}">
                                                                        <div class="btn btn-primary btn-sm"
                                                                            onclick="document.getElementById('order-popup').style.display='block'">
                                                                            View Log</div>
                                                                    </a>
                                                                    <a class="orderdetail-view-popup"
                                                                        id="{{ $ordr['id'] }}">
                                                                        <div class="btn btn-info btn-sm"
                                                                            onclick="document.getElementById('order-popup').style.display='block'">
                                                                            Detail</div>
                                                                    </a>
                                                                    @if ($ordr->orderreceiveform)
                                                                        @if ($ordr->orderreceiveform->complaint_id)
                                                                            <a class="btn btn-info btn-sm"
                                                                                href={{ url('/viewcomplaint', $ordr->orderreceiveform->complaint_id) }}>Complaint</a>

                                                                        @endif


                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div id="order-popup">
                                            </div>

                                            <script>
                                                $(document).ready(function() {
                                                    // Open modal and load content
                                                    function openOrderModal(url) {
                                                        $("#order-popup").load(url, function() {
                                                            $("#order-popup").css("display", "block");
                                                        });
                                                    }

                                                    $(".order-view-popup").click(function(e) {
                                                        e.preventDefault();
                                                        var id = $(this).attr('id');
                                                        openOrderModal("/order/" + id);
                                                    });
                                                    $(".order-view-popup-log").click(function(e) {
                                                        e.preventDefault();
                                                        var id = $(this).attr('id');
                                                        openOrderModal("/orderpop/" + id);
                                                    });
                                                    $(".allorder-view-popup").click(function(e) {
                                                        e.preventDefault();
                                                        var id = $(this).attr('id');
                                                        openOrderModal("/allorder/" + id);
                                                    });
                                                    $(".orderdetail-view-popup").click(function(e) {
                                                        e.preventDefault();
                                                        var id = $(this).attr('id');
                                                        openOrderModal("/orderdetails/" + id);
                                                    });

                                                    // Close modal when clicking outside loaded content
                                                    $(document).on('mousedown', function(event) {
                                                        var $popup = $("#order-popup");
                                                        if ($popup.is(":visible") && !$popup.is(event.target) && $popup.has(event.target).length ===
                                                            0) {
                                                            $popup.hide();
                                                        }
                                                    });
                                                    // Optional: add a close button inside loaded content for better UX
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

        // Helper: Generate Status Options
        const getStatusOptions = (currentStatus) => {
            const statuses = [{
                    value: 'Pending',
                    label: 'Pending/Inventory'
                },
                {
                    value: 'Sent To Account',
                    label: 'Sent To Account'
                },
                {
                    value: 'Account',
                    label: 'Account'
                },
                {
                    value: 'Approved',
                    label: 'Approved'
                },
                {
                    value: 'Sent to Dispatch',
                    label: 'Sent to Dispatch'
                },
                {
                    value: 'Dispatched',
                    label: 'Goods Dispatched'
                },
                {
                    value: 'Delivered',
                    label: 'Delivered'
                },
                {
                    value: 'Declined',
                    label: 'Declined'
                }
            ];
            return statuses.map(s =>
                `<option value="${s.value}" ${s.value === currentStatus ? 'selected' : ''}>${s.label}</option>`
            ).join('');
        };

        // Helper: Generate Popover Form HTML
        const generatePopoverForm = (dataStr, csrfToken) => {
            const [
                id, status, due, remarks, lastCollectionDate,
                lastCollectionAmt, deliveryTime, deliveryCost,
                dealerDiscount, vatAmt
            ] = dataStr.split(",");

            const isAccount = status === 'Account';
            const isDeclined = status === 'Declined';
            const showIf = (condition) => condition ? 'block' : 'none';

            return `
                <form action="/order/${id}" method="post" class="popover-form">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="previous_status" value="${status}">

                    <div class="form-group">
                        <label for="status-${id}">Status:</label>
                        <select name="status" id="status-${id}" class="form-control input-md status-select" required>
                            ${getStatusOptions(status)}
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Remark:</label>
                        <input type="text" name="remark" class="form-control" value="${remarks || ''}" required>
                    </div>

                    <div class="status-input" style="display: ${showIf(isAccount)}">
                        <label>Tentative Delivery Time(Days):</label>
                        <input type="number" placeholder="Days" name="delivery_time" value="${deliveryTime || ''}" class="form-control" min="1" step="1">

                        <label>Freight Charge(%):</label>
                        <input type="number" placeholder="0.00%" name="delivery_cost" value="${deliveryCost || ''}" class="form-control" min="0" step="0.01">

                        <label>Due Amount:</label>
                        <input type="number" placeholder="Rs." name="due" value="${due || ''}" class="form-control" min="0" step="1">

                        <label>Last Collection Date:</label>
                        <input type="date" name="last_collection_date" value="${lastCollectionDate || ''}" class="form-control">

                        <label>Last Collection Amount:</label>
                        <input type="number" placeholder="Rs." name="last_collection" value="${lastCollectionAmt || ''}" class="form-control" min="0" step="1">

                        <label>Risk Zone:</label>
                        <select name="riskzone" class="form-control input-md">
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                        </select>

                        <label>Dealer Discount:</label>
                        <input type="number" disabled placeholder="Rs." name="dealer_discount" value="${dealerDiscount || ''}" class="form-control" min="0" step="1">

                        <label>Tax :</label>
                        <input type="number" disabled placeholder="Rs." name="vat_amt" value="${vatAmt || ''}" class="form-control" min="0" step="1">
                    </div>

                    <div class="status-input-declined" style="display: ${showIf(isDeclined)}">
                        <label>Need Correction</label>
                        <div>
                            <input type="checkbox" name="needcorrection" id="needcorrection-${id}" value="1">
                            <label for="needcorrection-${id}"> Yes</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">Update</button>
                </form>
                <img class="close-popover-btn" style="position:absolute;top:-8px; right:-8px;width:25px; height:25px; cursor:pointer;" src="{{ asset('assets/pages/img/quit.svg') }}">
            `;
        };

        $(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const $popovers = $('[data-toggle="popover"]');

            // Initialize Popovers
            $popovers.popover({
                trigger: 'manual',
                html: true,
                placement: 'right',
                container: 'body',
                title: '<b>Change Order Status</b>',
                content: 'Loading...' // Placeholder
            });

            // Handle Popover Toggle
            $(document).on('click', '[data-toggle="popover"]', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $trigger = $(this);
                // Close all others
                $popovers.not($trigger).each(function() {
                    if (typeof $(this).popover === 'function') {
                        var pop = $(this).data('bs.popover');
                        if (pop && pop.tip && typeof pop.tip === 'function') {
                            var tip = pop.tip();
                            if (tip && $(tip).hasClass('in')) {
                                $(this).popover('hide');
                            }
                        }
                    }
                });

                // Generate content dynamically and re-initialize popover
                if (typeof $trigger.popover === 'function') {
                    var pop = $trigger.data('bs.popover');
                    if (pop && pop.tip && typeof pop.tip === 'function') {
                        var tip = pop.tip();
                        if (tip && $(tip).hasClass('in')) {
                            $trigger.popover('hide');
                            return;
                        }
                        // Only destroy if destroy is available
                        if (typeof pop.destroy === 'function') {
                            $trigger.popover('destroy');
                        }
                    }
                    const data = $trigger.data('api-parameter');
                    $trigger.popover({
                        trigger: 'manual',
                        html: true,
                        placement: 'right',
                        container: 'body',
                        title: '<b>Change Order Status</b>',
                        content: generatePopoverForm(data, csrfToken)
                    });
                    $trigger.popover('show');
                }
            });

            // Close when clicking outside
            $(document).on('click', function(e) {
                const $target = $(e.target);
                if (!$target.closest('.popover').length && !$target.closest('[data-toggle="popover"]')
                    .length) {
                    $popovers.popover('hide');
                }
            });

            // Close button inside popover
            $(document).on('click', '.close-popover-btn', function() {
                $popovers.popover('hide');
            });

            // Handle Status Change inside Popover
            $(document).on('change', '.status-select', function() {
                const val = $(this).val();
                const $form = $(this).closest('form');
                $form.find('.status-input').toggle(val === 'Account');
                $form.find('.status-input-declined').toggle(val === 'Declined');
            });

            // Dealer List Update
            $('#area_code').on('change', function() {
                const areaCode = $(this).val();
                const currentDealerCode = "{{ $dealercode }}";

                $.ajax({
                    url: '/dealerlist',
                    type: 'GET',
                    data: {
                        areacode: areaCode
                    },
                    dataType: 'json',
                    success: (data) => {
                        const $dealerSelect = $('#dealer');
                        $dealerSelect.empty().append('<option value="">All Dealer</option>');

                        data.dealers.forEach(dealer => {
                            const isSelected = currentDealerCode === dealer.code ?
                                'selected' : '';
                            $dealerSelect.append(
                                `<option value="${dealer.code}" ${isSelected}>${dealer.code}-${dealer.name}</option>`
                            );
                        });
                        $dealerSelect.selectpicker("refresh");
                    }
                });
            });

            // Trigger change on load
            $("#area_code").trigger("change");

            // Order View Popups (Consolidated)
            const openModal = (url) => {
                $("#order-popup").load(url, function() {
                    $("#order-pop").css("display", "block");
                    // Close modal when clicking outside content
                    const modal = document.getElementById('order-popup');
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                });
            };

            $(".order-view-popup").click(function() {
                openModal("/order/" + this.id);
            });
            $(".order-view-popup-log").click(function() {
                openModal("/orderpop/" + this.id);
            });
            $(".allorder-view-popup").click(function() {
                openModal("/allorder/" + this.id);
            });
            $(".orderdetail-view-popup").click(function() {
                openModal("/orderdetails/" + this.id);
            });
        });
    </script>
@endsection
