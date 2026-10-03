@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <!-- BEGIN BREADCRUMBS -->
        <!--<div class="breadcrumbs">-->
        <!--    <h1>Items Details</h1>-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li>-->
        <!--            <button class="btn btn-success print" onclick="printDiv('printMe')"-->
        <!--                style="margin-right: 16px;">Print</button>-->
        <!--                <a href="/challan/excelreport/1" class="btn btn-success excel">Excel</a>-->
        <!--            <script>-->
        <!--                function printDiv(divName) {-->
        <!--                    var printContents = document.getElementById(divName).innerHTML;-->
        <!--                    var originalContents = document.body.innerHTML;-->

        <!--                    document.body.innerHTML = printContents;-->
        <!--                    window.print();-->

        <!--                    document.body.innerHTML = originalContents;-->

        <!--                }-->
        <!--            </script>-->
        <!--        </li>-->
        <!--    </ol>-->
            <!-- Sidebar Toggle Button -->
        <!--    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">-->
        <!--        <span class="sr-only">Toggle navigation</span>-->
        <!--        <span class="toggle-icon">-->
        <!--            <span class="icon-bar"></span>-->
        <!--            <span class="icon-bar"></span>-->
        <!--            <span class="icon-bar"></span>-->
        <!--        </span>-->
        <!--    </button>-->
            <!-- Sidebar Toggle Button -->
        <!--</div>-->
        <div class="breadcrumbs">
            <h1>Items Details</h1>
            <ol class="breadcrumb">
                <li>
                    <button class="btn btn-success print" onclick="printDiv('printMe')" style="margin-right: 30px;">Print</button>
                    <script>
		function printDiv(divName){
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;

			document.body.innerHTML = printContents;
			window.print();

			document.body.innerHTML = originalContents;

		}
	</script>
                </li>
            </ol>
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
        <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
        <style>
            .printOnly {
                /* text-align: center; */
                margin-left: 3%;

            }

            .amount {
                font-size: 16px !important;
                margin-left: 71%;
                font-weight: 600;
            }

            .total {
                font-size: 20px !important;
                font-weight: bold;
                text-align: center;
                vertical-align: middle !important;

            }

        </style>
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered" id="printMe">
                    {{-- {{ dd($order->due); }} --}}

                    @if (!empty($order))
                        <div style="font-size: 24px;text-align: center;"> {{ env('COMPANY_NAME') }} </div>
                        <div style="font-size: 18px;text-align: center;">{{ env('COMPANY_ADDRESS') }}</div>
                        <p style="position:absolute;right:5%;"><?php echo 'Date: ' . date('Y-m-d'); ?></p>
                        <p class="printOnly" style="margin-top: 5%;"> Marketing Officer:{{ $mo }}</p>
                        <p class="printOnly"> Delaer : {{ $dealer->name }} </p>
                    @endif

                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class=" fa fa-paint-brush font-red"></i> -->
                            <span class="caption-subject font-red bold uppercase">Challan</span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                            <thead>
                                <tr>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2">S/N</th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2" scope="col"> Category </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2" scope="col"> Product </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2" scope="col"> Order No.</th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2" scope="col"> Ordered Date </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        colspan="4" scope="col"> Product Details </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        colspan="2" scope="col"> Delivery </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        colspan="3" scope="col"> Account </th>
                                    <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;"
                                        rowspan="2" scope="col"> Remarks </th>
                                </tr>
                                <tr>
                                    <th>Variant</th>
                                    <th>Rate(Rs.)</th>
                                    <th>Quantity</th>
                                    <th>Amount(Rs.)</th>
                                    <th>Tentative Delivery Time(Days)</th>
                                    <th>Freight Charge(%)</th>
                                    <th>Last Collection Date</th>
                                    <th>Due Amount</th>
                                    <th>Last Collection Amount</th>
                                    {{-- <th>Amount(Rs.)</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($order))
                                    <?php $amt = 0; ?>
                                    @foreach ($order as $ordr)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td> {{ $ordr['category_name'] }}</td>
                                            <td> {{ $ordr->items->product['name'] }}</td>
                                            <td> {{ $ordr['order_id'] }}</td>
                                            <!-- <td> {{ $ordr['dealer_name'] }} </td> -->
                                            <!-- <td> {{ $ordr['user_name'] }}</td> -->
                                            <td> {{ $ordr->order['date'] }}</td>



                                            <td> {{ $ordr->items->size['size'] }}</td>
                                            <td>{{ $ordr['product_rate'] }}</td>
                                            <td> {{ $ordr['quantity'] }}</td>
                                            <?php
                                            $amt = $amt + $ordr['quantity'] * $ordr['product_rate'];
                                            $devamt = ($amt * $ordr['delivery_cost']) / 100;
                                            $total_num = $loop->count;
                                            ?>
                                            <td>Rs. {{ $ordr['quantity'] * $ordr['product_rate'] }}</td>
                                            {{-- @continue --}}
                                            {{-- @if ($loop->iteration == 1) --}}
                                            @if ($loop->first)
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    {{ $ordr->order['delivery_time'] }} Days</td>
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    {{ $ordr->order['delivery_cost'] }} % </td>
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    {{ $ordr->order['last_collection_date'] }}</td>
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    RS.{{ $ordr->order['due'] }}</td>
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    RS.{{ $ordr->order['last_collection'] }}</td>
                                                <td rowspan="{{ $total_num }}"
                                                    style="font-size: 15px !important;font-weight: 500;text-align: center;vertical-align: middle !important;">
                                                    {{ $ordr->order['remarks'] }} </td>
                                            @else
                                            {{-- {{ dd($loop->first) }} --}}
                                                @continue;
                                            @endif
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan='2'
                                            style="font-size: 20px !important;font-weight: bold;text-align: center;vertical-align: middle !important;">
                                            Total</td>
                                        <td colspan="7">
                                            <p style="font-size: 16px !important;margin-left:57%;font-weight: 600;">
                                                Rs.<?php echo $amt; ?></p>
                                        </td>
                                        <td colspan="2">
                                            <p style="font-size: 16px !important;margin-left:57%;font-weight: 600;">
                                                Rs.{{ $devamt }}</p>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                @endif

                            </tbody>
                            <!-- <a href="" class="btn btn-success btn-sm ab" style="margin-bottom:5px;"> Create Challan</a> -->
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
@endsection
