@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Product Manufacture</h1>
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
                                <li class="active"><a href="/product-manufactures/create">Product Manufactures</a></li>
                                <li><a href="/product-manufactures"> Product Manufactures List</a></li>
                                {{-- <li><a href="/inventory">Raw Materials Inventory</a></li> --}}



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
                                        <span class="caption-subject font-red bold uppercase">Product Manufacture</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="GET" action="{{ route('product-manufactures.create') }}">
                                    <div class="form-group row">
                                        <label for="product_id" class="col-md-3 col-form-label text-right">Select
                                            Product</label>
                                        <div class="col-md-6">
                                            <select name="product_id" id="product_id" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                @if (isset($rawMaterialsForProduct) && count($rawMaterialsForProduct) > 0)
                                    <form method="POST" action="{{ route('product-manufactures.store') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ request('product_id') }}">

                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label text-right">Raw Materials</label>
                                            <div class="col-md-6">
                                                @foreach ($rawMaterialsForProduct as $rawmaterial)
                                                    <div class="mb-2">
                                                        <label>{{ $rawmaterial->name }}</label>
                                                        {{-- <input type="hidden" name="rawmaterial_ids[]" --}}
                                                        {{-- value="{{ $rawmaterial->id }}"> --}}
                                                        <input type="text" class="form-control mb-1"
                                                            value="Available: {{ $rawmaterial->available_qty }}{{ $rawmaterial->rawStock->rawSize->unit ?? '' }} "
                                                            readonly>

                                                        <!-- Hidden ID -->
                                                        <input type="hidden" name="rawmaterial_ids[]"
                                                            value="{{ $rawmaterial->id }}">
                                                        <input type="hidden" name="rawsize_ids[]"
                                                            value="{{ $rawmaterial->rawsize_id ?? '' }}">


                                                        {{-- <input type="number" name="quantities[]" class="form-control"
                                                            placeholder="Enter Quantity Used" required> --}}
                                                        <input type="number" name="quantities[]" class="form-control"
                                                            placeholder="Enter Quantity Used in {{ $rawmaterial->rawStock->rawSize->unit ?? '' }}"
                                                            step="0.01" min="0" required>



                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="no_of_products" class="col-md-3 col-form-label text-right">No. of
                                                Products</label>
                                            <div class="col-md-6">
                                                <input type="number" name="no_of_products" id="no_of_products"
                                                    class="form-control"
                                                    placeholder="Enter number of products to manufacture" required>
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
                                                    {{ __('Create') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchRawmaterials() {
            var input, filter, ul, li, label, i, txtValue;
            input = document.getElementById("rawmaterials-search");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL-rawmaterials");
            li = ul.getElementsByTagName("li");

            for (i = 0; i < li.length; i++) {
                label = li[i].getElementsByTagName("label")[0];
                txtValue = label.textContent || label.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
    </script>
@endsection
