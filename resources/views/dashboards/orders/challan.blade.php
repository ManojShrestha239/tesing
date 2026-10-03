@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>Items Details</h1>
            <ol class="breadcrumb">
                <li>
                    <button class="btn btn-success print" onclick="printDiv('printMe')"
                        style="margin-right: 30px;">Print</button>
                </li>
                <li>
                    <button class="btn btn-success print" onclick="printDiv('printMe',false)" style="margin-right: 30px;">Print
                        With
                        Amount</button>
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


                    <div style="font-size: 24px;text-align: center;"> {{ env('COMPANY_NAME') }} </div>
                    <div style="font-size: 18px;text-align: center;">{{ env('COMPANY_ADDRESS') }}</div>
                    {{-- <p style="position:absolute;right:5%;">
                        Vat No.: {{ env('COMPANY_VAT') }} <br>
                        Date: {{ $helpers->convertDate(date('Y-m-d')) }} <br>
                        Phone No.: {{ env('COMPANY_PHONE') }} <br>
                    </p> --}}
                    <div style="width: 300px; float: right; margin-top: 20px; margin-right: 20px;">
                        <p style="line-height: 1.5;">
                            Vat No.: {{ env('COMPANY_VAT') }} <br>
                            Date: {{ $helpers->convertDate(date('Y-m-d')) }} <br>
                            Phone No.: {{ env('COMPANY_PHONE') }} <br>
                        </p>
                    </div>

                    @if (!empty($order))
                        @php
                            dd($mo);
                        @endphp
                    @endif


                    @if (!empty($orders))
                        @if ($mo)
                            <p class="printOnly" style="margin-top: 5%;"> M/S:{{ $mo }}</p>
                        @endif
                        <p class="printOnly"> Dealer : {{ $dealer->name }}<br>
                            Address : @if ($dealer->address)
                                {{ $dealer->address }}
                            @endif <br>
                            Contact : @if ($dealer->mobile)
                                {{ $dealer->mobile }}
                            @endif
                            @if ($dealer->phone)
                                ,{{ $dealer->phone }}
                            @endif
                            <br>
                            PAN / VAT : @if ($dealer->pan_vat_no)
                                {{ $dealer->pan_vat_no }}
                            @endif <br>
                        </p>
                    @endif

                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class=" fa fa-paint-brush font-red"></i> -->
                            <span class="caption-subject font-red bold uppercase">Challan</span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                            {{-- <thead>
                            <tr>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2">S/N</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> Particulars </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" scope="col"> 40 Kg </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" scope="col"> 20 Ltr </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" scope="col"> 10 Ltr </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" scope="col"> 4 Ltr </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" scope="col"> 1 Ltr </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> 500 ML</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> 200 ML </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> 100 ML </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> 50 ML </th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;" rowspan="2" scope="col"> Amount </th>
                            </tr>
                            <tr>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;">Bag</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;">20 Kg</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;">10 Kg</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;">5 Kg</th>
                                <th style=" font-weight: bold;text-align: center;vertical-align: middle !important;">1 Kg</th>
                            </tr>
                        </thead> --}}
                            <thead>
                                <tr>
                                    <th rowspan="2">S/N</th>
                                    <th rowspan="2">Particulars</th>
                                    @foreach ($sizes as $size)
                                        <th>{{ $size }}</th>
                                    @endforeach
                                    <th class="total-row" rowspan="2">Amount</th>
                                </tr>
                                <tr>
                                    @foreach ($sizes as $size)
                                        <th>Qty</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach ($orders as $index => $order)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $order['product'] }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sizes as $size)
                                            @php
                                                $qty = $order['sizes'][$size]['qty'] ?? 0;
                                                $amt = $order['sizes'][$size]['amt'] ?? 0;
                                                $rowTotal += $amt;
                                            @endphp
                                            <td>{{ $qty > 0 ? $qty : '' }}</td>
                                        @endforeach
                                        <td class="total-row">Rs. {{ $rowTotal }}</td>
                                        @php $grandTotal += $rowTotal; @endphp
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="{{ 2 + count($sizes) }}" style="font-weight: bold;">
                                        Total</td>
                                    <td> Rs. {{ $grandTotal }}</td>
                                </tr>
                            </tbody>

                            <!-- <a href="" class="btn btn-success btn-sm ab" style="margin-bottom:5px;"> Create Challan</a> -->
                        </table>

                        <style>
                            @media print {
                                #sample_2 {
                                    border-collapse: collapse;
                                    /* Merge borders */
                                    width: 100%;
                                }

                                #sample_2 th,
                                #sample_2 td {
                                    border: 2px solid black !important;
                                    /* Bold borders */
                                }

                                /* .total-row {
                                                                                                display: none;
                                                                                            } */
                            }
                        </style>

                        <div style="display:flex; justify-content: space-around;">
                            <span style="text-align: center;">
                                ......................................... <br>
                                Signature Of Customer
                            </span>
                            <span style="text-align: center;">
                                ................................ <br>
                                Order Booked By
                            </span>
                        </div>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>

    <script>
        function printDiv(divName, hideTotal = true) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            // Temporarily replace body with div content
            document.body.innerHTML = printContents;

            if (hideTotal) {
                // Hide all total-row elements in this div
                document.querySelectorAll('.total-row').forEach(function(el) {
                    el.style.display = 'none';
                });
            }

            window.print();

            // Restore original body content
            document.body.innerHTML = originalContents;
        }
    </script>

@endsection
