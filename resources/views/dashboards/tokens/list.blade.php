<div class="page-content2">
    <img style="float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
    <!-- BEGIN BREADCRUMBS -->
    <div class="breadcrumbs">
        <h1>Denomination Details</h1>

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
            @if( session('Status'))
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
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" fa fa-paint-brush font-red"></i>
                        <span class="caption-subject font-red bold uppercase">Denomionation List</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Payment id </th>
                                <th> Denomination</th>
                                <th> Quantity </th>
                                <th> Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        @if (!empty($payments))
                        @foreach($payments as $logs)
                        <tr>
                            <td> {{ $logs->collection_id }} </td>
                            <td> {{ $logs->denomination }}</td>
                            <td> {{ $logs->quantity }}</td>
                            <td> {{ $logs->total }}</td>
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