<div class="page-content2 ">
    <img style="float:right;width:25px; height:25px;"
        onclick="document.getElementById('order-popup').style.display='none'"
        src={{ asset('assets/pages/img/quit.svg') }}>
    <!-- BEGIN BREADCRUMBS -->
    <div class="breadcrumbs">
        <h1>Order Details</h1>

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

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" fa fa-paint-brush font-red"></i>
                        <span class="caption-subject font-red bold uppercase">Order Details</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Order No. </th>
                                {{-- <th> Dealer Info </th> --}}
                                <th> Due Amount</th>
                                <th> Last Collection Date </th>
                                <th> Last Collection Amount </th>
                                <th> Tentative Delivery Time(Days) </th>
                                <th> Freight Charge(%) </th>
                                <th> Freight Amount(Rs.) </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> {{ $odr->id }}</td>
                                {{-- <td> {{ $dealers->dealer_name }}({{ $odr->dealer_code }})</td> --}}
                                <td> {{ $odr->due }}</td>
                                <td> {{ $odr->last_collection_date }}</td>
                                <td> {{ $odr->last_collection }}</td>
                                <td> {{ $odr->delivery_time }}</td>
                                <td> {{ $odr->delivery_cost }}</td>
                                <td> RS.{{ ($odr->delivery_cost * $odr->total_price) / 100 }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>

</div>
