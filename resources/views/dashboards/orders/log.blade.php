<div class="page-content2 ">
    <img style="float:right;width:25px; height:25px;"
        onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg')
        }}>
    <!-- BEGIN BREADCRUMBS -->
    <div class="breadcrumbs">
        <h1>Order Log History</h1>

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
                        <span class="caption-subject font-red bold uppercase">LOG History</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Order No. </th>
                                <!-- <th> Order Id </th> -->
                                <th> Order Updated</th>
                                <th> Current Status </th>
                                <th> Previous Status </th>
                                <th> Changed by</th>
                                <th> Remarks </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($log))

                            @foreach($log as $logs)
                            <tr>
                                <td> {{ $logs['ordercoll_id'] }}</td>

                                <td> {{ $logs['date'] }}</td>
                                <td> {{ $logs['status'] }}</td>
                                <td> {{ $logs['previous_status'] }}</td>
                                <td> {{ $logs['admin'] }}</td>
                                <td> {{ $logs['remarks'] }}</td>
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
