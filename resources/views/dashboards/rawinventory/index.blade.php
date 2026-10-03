@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Raw Material Inventory</h1>
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


                                <li class=""><a href="/raw/create">Add Raw Materials </a></li>
                                <li class=""><a href="/raw">List Raw Materials </a></li>
                                <li><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
                                <li><a href="/raw-product/create">Assign Product with Raw Materials </a></li>
                                <li><a href="/raw-product"> Product List with Raw Materials</a></li>
                                <li><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/product-manufactures"> Product Manufactures List</a></li>
                                <li class="active"><a href="/inventory">Raw materials Inventory</a></li>


                                <!-- <li><a class="category-create">Add New Category </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="categories">List Categories </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="product-size-create">Add New Variant </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="products-size">List Variants </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="product-create">Add New Product </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="products">List Product </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="productfinal-create">Assign Product with Variants </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li class="active"><a href="/productsfinal">Final Product List </a></li> -->


                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <style>
                        .productImage {
                            height: 100px;
                            width: 100px;
                            margin-left: 22%;
                        }
                    </style>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Raw Material Inventory
                                        </span>
                                    </div>
                                    {{-- <a href="/exportinventory" class="btn btn-outline btn-xs btn-primary"
                                        style="float:right">.xlsx</a> --}}
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th> Rawmaterials name </th>
                                                <th>Date</th>
                                                <th> Purchased Quantity (In Tons)</th>
                                                <th>Withdraw</th>
                                                <th>Available</th>

                                                {{-- <th> Status </th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($rawInventories as $inventory)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $inventory->rawmaterial->name ?? 'N/A' }}</td>
                                                    <td>{{ $inventory->date }}</td>
                                                    <td>{{ $inventory->total_qty }}</td>
                                                    <td>{{ $inventory->withheld }}</td>
                                                    <td>{{ $inventory->available }}</td>
                                                    {{-- <td>{{ $inventory->status }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
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
