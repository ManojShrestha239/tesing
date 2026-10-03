@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Product Inventory</h1>
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

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <ul class="nav navbar-nav margin-bottom-35 products-class">
                                @include('dashboards.dynamicNav.producinventory')
                            </ul>
                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{!! session('Status') !!}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Inventory</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="inventoryTable">
                                        <thead>
                                            {{-- <tr>
                                                <th>SN</th>
                                                <th> Dealer </th>
                                                <th> Product Dispatched </th>
                                                <th> Product Inventory </th>
                                                <th> Product Size </th>
                                                <th>Dispatched Quantity </th>
                                                <th>Available Product Quantity </th>
                                            <tr> --}}
                                            <th>SN</th>
                                            <th>Product</th>
                                            <th>Product Size</th>
                                            <th>Product Inventory</th>
                                            <th>Dispatched Quantity</th>
                                            <th>Available Product Quantity</th>
                                            {{-- <th>Dealers (Assigned Qty)</th> --}}
                                            </tr>

                                            {{-- <th> Status </th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inventory as $i => $item)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $item['product_name'] }}</td>
                                                    <td>{{ $item['size'] }}</td>
                                                    <td>{{ $item['total_qty'] }}</td>
                                                    <td>{{ $item['assigned'] }}</td>
                                                    <td>{{ $item['available'] }}</td>
                                                    {{-- <td>{{ $item['dealers'] }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable();
        });
    </script>
@endsection
