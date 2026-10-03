@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Add Raw Materials</h1>
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



                                <li class="active"><a href="/raw/create">Add Raw Materials </a></li>
                                <li class=""><a href="/raw">List Raw Materials </a></li>
                                <li><a href="/rawpurchase/create">Purchased Raw Materials </a></li>
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
                                        <span class="caption-subject font-red bold uppercase">Add Raw Materials</span>
                                    </div>

                                </div>


                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/raw" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Raw material Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                name="name" placeholder="Rawmaterial Name" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="rawsize_id"
                                            class="col-md-3 col-form-label text-right">{{ __('Raw Size') }}</label>
                                        <div class="col-md-6">
                                            <select name="rawsize_id" class="form-control selectpicker"
                                                data-live-search="true" required>
                                                <option value="">Select Size</option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    {{-- <div class="form-group row">
                                        <label for="size_id"
                                            class="col-md-3 col-form-label text-right">{{ __('Size') }}</label>
                                        <div class="col-md-6">
                                            <select name="size_id" class="form-control selectpicker" data-live-search="true"
                                                required>
                                                <option value="">Select Size</option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->size }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Create') }}
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
