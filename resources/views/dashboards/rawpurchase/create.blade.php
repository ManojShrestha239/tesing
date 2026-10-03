@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Purchase Raw Material</h1>
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

                                {{-- <li><a href="/category/create">Add New Category </a></li>
                                <li><a href="/category">List Categories </a></li>
                                <li><a href="/size/create">Add New Variant </a></li>
                                <li><a href="/size">List Variants </a></li>
                                <li><a href="/raw/create">Add Raw Materials </a></li>
                                <li class=""><a href="/raw">List Raw Materials </a></li>
                                <li class="active"><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
                                <li><a href="/product/create">Add New Product </a></li>
                                <li><a href="/product">List Product </a></li>
                                <li><a href="/raw-product/create">Assign Product with Rawmaterials </a></li>
                                <li><a href="/raw-product"> Product List with Rawmaterials</a></li>
                                <li><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/final-product/create">Assign Product with Variants </a></li>
                                <li><a href="/final-product">Final Product List </a></li>
                                <li><a href="/inventory">Raw materials Inventory</a></li> --}}

                                <li><a href="/raw/create">Add Raw Materials </a></li>
                                <li><a href="/raw">List Raw Materials </a></li>
                                <li class="active"><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
                                <li><a href="/raw-product/create">Assign Product with Raw Materials </a></li>
                                <li><a href="/raw-product"> Product List with Raw Materials</a></li>
                                <li><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/product-manufactures"> Product Manufactures List</a></li>
                                {{-- <li><a href="/inventory">Raw Materials Inventory</a></li> --}}


                                <!-- <li><a class="category-create">Add New Category </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="categories">List Categories </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="product-size-create">Add New Variant </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <li><a class="products-size">List Variants </a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <li class = "active"><a>Add New Product </a></li>
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
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Purchase Raw Material</span>
                                    </div>

                                </div>


                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="{{ route('rawpurchase.store') }}">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="r_id" class="col-md-3 col-form-label text-right">Raw
                                            Material</label>
                                        <div class="col-md-6">
                                            <select name="r_id" class="form-control" required>
                                                <option value="">Select Raw Material</option>
                                                @foreach ($rawMaterials as $raw)
                                                    <option value="{{ $raw->id }}">{{ $raw->name }}
                                                        @if ($raw->rawSize && $raw->rawSize->unit)
                                                            ({{ $raw->rawSize->unit }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Batch Number</label>
                                        <div class="col-md-6">
                                            <input type="text" name="batch_number" class="form-control" required>

                                        </div>
                                    </div>

                                    <!-- Unit Field -->
                                    <!-- Unit Field -->





                                    <div class="form-group row">
                                        <label for="total_qty" class="col-md-3 col-form-label text-right">Total
                                            Quantity
                                        </label>
                                        <div class="col-md-6">
                                            <input type="number" name="total_qty" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="date" class="col-md-3 col-form-label text-right">Date</label>
                                        <div class="col-md-6">
                                            <input type="date" name="date" class="form-control"
                                                max="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                Add Inventory
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
