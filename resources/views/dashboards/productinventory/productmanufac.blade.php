@extends('dashboards.sessions.layout')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                                        <span class="caption-subject font-red bold uppercase">Product Manufacture</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif


                                <div class="portlet-body form">
                                    <form method="POST" action="{{ route('productmanufac.store') }}" id="manufacture-form">
                                        @csrf

                                        <!-- Batch Number and Date Row -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Batch Number</label>
                                                    <input type="text" name="batch_number" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label bolds">Date<span
                                                            class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                                    <div class="date" data-provide="datepicker" class="form-control">
                                                        <input type="text" id="nepaliDateF" name="date"
                                                            autocomplete="off" placeholder="YYYY-MM-DD" value=""
                                                            class="form-control nepali-calendar" required>
                                                    </div>
                                                    <span class="field-validation-valid" data-valmsg-for="education"
                                                        data-valmsg-replace="true"></span>
                                                    @if ($errors->has('date'))
                                                        <span class="help-block required">
                                                            <strong>{{ $errors->first('date') }}</strong>
                                                        </span>
                                                    @endif

                                                    @if (session('status'))
                                                        <span class="help-block required">
                                                            <strong>{{ session('status') }}</strong>
                                                    @endif
                                                </div>
                                                {{-- <div class="form-group">
                                                    <label>Date</label>
                                                    <input type="date" name="date" class="form-control"
                                                        max="{{ date('Y-m-d') }}" required>
                                                </div> --}}
                                            </div>
                                        </div>



                                        <!-- Product Rows Container -->
                                        <div id="product-rows">
                                            <!-- First Product Row -->
                                            <div class="row product-row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Product</label>
                                                        <select name="products[0][product_id]"
                                                            class="form-control product-select" required>
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}">{{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Size</label>
                                                        <select name="products[0][size_id]" class="form-control size-select"
                                                            required>
                                                            <option value="">Select Size</option>
                                                            <!-- Sizes will be populated by JavaScript -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" name="products[0][total_qty]"
                                                            class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1" style="padding-top: 25px;">
                                                    <button type="button" class="btn btn-success add-row"
                                                        style="margin-top: 5px;">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-top: 20px;">
                                            <button type="submit" class="btn btn-primary">
                                                Add Product Manufacture
                                            </button>
                                        </div>
                                    </form>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Make products data accessible to JS
        const productsData = @json($products);

        $(document).ready(function() {
            let rowCount = 1;

            // Initialize Select2 on product and size selects
            function initSelect2(element) {
                $(element).select2({
                    allowClear: true,
                    placeholder: $(element).find('option:first').text()
                });
            }

            // Initialize Select2 for first row
            $('.product-select').each(function() {
                initSelect2(this);
            });
            $('.size-select').each(function() {
                initSelect2(this);
            });

            // Add new product row
            $(document).on('click', '.add-row', function() {
                const newRow = `
                <div class="row product-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="products[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="products[${rowCount}][size_id]" class="form-control size-select" required>
                                <option value="">Select Size</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="number" name="products[${rowCount}][total_qty]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-1" style="padding-top: 25px;">
                        <button type="button" class="btn btn-danger remove-row" style="margin-top: 5px;">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;

                $('#product-rows').append(newRow);

                // Initialize Select2 for newly added row
                const newProductSelect = $('select[name="products[' + rowCount + '][product_id]"]');
                const newSizeSelect = $('select[name="products[' + rowCount + '][size_id]"]');
                initSelect2(newProductSelect);
                initSelect2(newSizeSelect);

                rowCount++;
            });

            // Remove product row
            $(document).on('click', '.remove-row', function() {
                if ($('.product-row').length > 1) {
                    $(this).closest('.product-row').remove();
                }
            });

            // Handle product selection change to populate sizes
            $(document).on('change', '.product-select', function() {
                const productId = $(this).val();
                const sizeSelect = $(this).closest('.product-row').find('.size-select');

                // Clear previous sizes
                sizeSelect.empty().append('<option value="">Select Size</option>');

                if (!productId) return;

                // Find selected product
                const selectedProduct = productsData.find(p => p.id == productId);

                if (selectedProduct && selectedProduct.size.length > 0) {
                    selectedProduct.size.forEach(size => {
                        sizeSelect.append(`<option value="${size.id}">${size.size}</option>`);
                    });
                }

                // Refresh Select2
                sizeSelect.trigger('change');
            });
        });
    </script>

    <script>
        document.getElementById('product-dropdown').addEventListener('change', function() {
            const productId = this.value;
            const sizeDropdown = document.getElementById('size-dropdown');

            // Clear previous sizes
            sizeDropdown.innerHTML = '<option value="">Select Size</option>';

            if (productId === '') {
                return; // no product selected
            }

            // Find selected product from productsData
            const selectedProduct = productsData.find(p => p.id == productId);

            if (selectedProduct && selectedProduct.size.length > 0) {
                selectedProduct.size.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.id;
                    option.textContent = size.size;
                    sizeDropdown.appendChild(option);
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable();
        });
    </script>


@endsection
