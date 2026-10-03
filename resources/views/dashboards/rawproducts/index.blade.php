@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Products with Raw Materials</h1>
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
                                <li><a href="/raw">List Raw Materials </a></li>
                                <li><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
                                <li><a href="/raw-product/create">Assign Product with Raw Materials </a></li>
                                <li class="active"><a href="/raw-product"> Product List with Raw Materials</a></li>
                                <li><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/product-manufactures"> Product Manufactures List</a></li>
                                {{-- <li><a href="/inventory">Raw Materials Inventory</a></li> --}}




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
                                        <span class="caption-subject font-red bold uppercase"> Product List with Raw
                                            Material </span>
                                    </div>
                                    {{-- <a href="/exportrawproduct" class="btn btn-outline btn-xs btn-primary"
                                        style="float:right">.xlsx</a> --}}
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                                        <thead>
                                            <tr>
                                                <th>Product Image</th>
                                                <th> Product Name </th>
                                                <th>RawMaterial</th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rawproducts as $rawproduct)
                                                <tr>
                                                    <td>
                                                        @if ($rawproduct->product->image)
                                                            <img class="productImage"
                                                                src="{{ asset('storage/productImage/' . $rawproduct->product->image) }}"
                                                                srcset="">
                                                        @else
                                                            <img class="productImage"
                                                                src="{{ asset('/images/image_not_found.png') }}"
                                                                srcset="">
                                                        @endif

                                                    </td>

                                                    <td>
                                                        {!! $rawproduct->product->name ?? 'N/A' !!}
                                                    </td>

                                                    <td>
                                                        @php
                                                            // Convert "1::2" to an array [1, 2]
                                                            $ids = explode('::', $rawproduct->rawmaterial_id ?? '');

                                                            // Fetch raw materials by ID
                                                            $materials = \App\Models\RawMaterial::whereIn('id', $ids)
                                                                ->pluck('name')
                                                                ->toArray();
                                                        @endphp

                                                        {{ implode(', ', $materials) ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        {{-- <a href="/raw-product/{{ $rawproduct->id }}/edit"
                                                            class='btn green btn-outline btn-xs'
                                                            style="margin-right:5px;">Edit</a> --}}
                                                        <a id="{{ $rawproduct->id ?? 0 }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class='btn green btn-outline btn-xs order-view-popup'
                                                            style="margin-right:5px;">Edit</a>

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
                                        $(".order-view-popup").click(function() {
                                            var id = $(this).attr('id');
                                            $("#order-popup").load("/raw-product/" + id + "/edit", function() {
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
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
