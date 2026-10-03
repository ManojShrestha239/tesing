@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Raw Materials</h1>
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


                                <li><a href="/raw/create">Add Raw Materials </a></li>
                                <li class="active"><a href="/raw">List Raw Materials </a></li>
                                <li><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
                                <li><a href="/raw-product/create">Assign Product with Raw Materials </a></li>
                                <li><a href="/raw-product"> Product List with Raw Materials</a></li>
                                <li><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/product-manufactures"> Product Manufactures List</a></li>
                                {{-- <li><a href="/inventory">Raw Materials Inventory</a></li> --}}




                                <!--
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class="category-create">Add New Category </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class="categories">List Categories </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class="product-size-create">Add New Variant </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li class = "active"><a>List Variants </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class = "product-create">Add New Product </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class = "products">List Product </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a class="productfinal-create">Assign Product with Variants </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <li><a href="/productsfinal">Final Product List </a></li> -->

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

                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Rawmaterials List </span>
                                    </div>
                                    <div class="tools"> </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>RawMaterials</th>
                                                {{-- <th>Batch Number</th> --}}
                                                <th>Purchased Quantity</th>
                                                <th>Withdraw</th>
                                                <th>Available</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rawMaterials as $rawmaterial)
                                                <tr>
                                                    <td>{{ $rawmaterial->name }}</td>
                                                    {{-- <td>{{ $rawmaterial->rawStock->batch_number ?? 0 }}</td> --}}
                                                    <td>{{ $rawmaterial->rawStock->total_qty ?? 0 }}
                                                        @if ($rawmaterial->rawStock && $rawmaterial->rawStock->rawSize)
                                                            <span>{{ $rawmaterial->rawStock->rawSize->unit }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $rawmaterial->rawStock->withdraw ?? 0 }}
                                                        @if ($rawmaterial->rawStock && $rawmaterial->rawStock->rawSize)
                                                            <span>{{ $rawmaterial->rawStock->rawSize->unit }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $rawmaterial->rawStock->available ?? 0 }}
                                                        @if ($rawmaterial->rawStock && $rawmaterial->rawStock->rawSize)
                                                            <span>{{ $rawmaterial->rawStock->rawSize->unit }}</span>
                                                        @endif
                                                    </td>


                                                    <td>
                                                        <a id="{{ $rawmaterial->id }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class='btn green btn-outline btn-xs order-view-popup'
                                                            style="margin-right:5px;">Edit</a>

                                                        <a href="{{ route('raw.show', $rawmaterial->id) }}"
                                                            class="btn green btn-outline btn-xs" style="margin-right:5px;">
                                                            Details
                                                        </a>


                                                        {{-- <form action="{{ route('raw.destroy', $rawmaterial->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-xs"
                                                                onclick="return confirm('Are you sure you want to delete this?')">Delete</button>
                                                        </form> --}}


                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- pop up after clicking view  --}}
                                <div id="order-popup">

                                </div>
                                <script>
                                    $(document).ready(function() {
                                        $(".order-view-popup").click(function() {
                                            var id = $(this).attr('id');
                                            $("#order-popup").load("/raw/" + id + "/edit", function() {
                                                $("#order-popup").css("display", "block");
                                            });

                                            // Close the popup when clicking outside
                                            window.onclick = function(event) {
                                                var modal = document.getElementById('order-popup');
                                                if (event.target === modal) {
                                                    modal.style.display = "none";
                                                }
                                            };
                                        });
                                    });
                                </script>
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
