@extends('dashboards.sessions.layout')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Product Dispatch </h1>
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
                                        <span class="caption-subject font-red bold uppercase">Dispatch Product </span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="{{ route('product-dealer.store') }}" id="dispatch-form"
                                    class="ml-3 custom-form">
                                    @csrf

                                    <!-- Dealer, Bill Number, and Date Row -->
                                    <div class="row align-items-end mb-4">
                                        <div class="col-md-3">
                                            <div class="form-group mb-0">
                                                <label>Dealer</label>
                                                <select name="dealer_id" id="dealer_select" class="form-control" required>
                                                    <option value="">Select dealers</option>
                                                    @foreach ($dealers as $dealer)
                                                        <option value="{{ $dealer->code }}">{{ $dealer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mb-0">
                                                <label>Bill Number</label>
                                                <input type="text" name="invoice_num" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            {{-- <div class="form-group mb-0">
                                                <label>Date</label>
                                                <input type="date" name="date" class="form-control"
                                                    max="{{ date('Y-m-d') }}" required>
                                            </div> --}}
                                            <div class="form-group">
                                                <label class="control-label bolds">Date<span
                                                        class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateF" name="date"
                                                        max="{{ date('Y-m-d') }}" autocomplete="off"
                                                        placeholder="YYYY-MM-DD" value=""
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
                                        </div>
                                    </div>

                                    <!-- Product Rows Container -->
                                    <div id="product-rows">
                                        <!-- First Product Row -->
                                        <div class="row align-items-center product-row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
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
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label>Size</label>
                                                    <select name="products[0][size_id]" class="form-control size-select"
                                                        required>
                                                        <option value="">Select Size</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label>Inventory</label>
                                                    <input type="number" name="products[0][available_qty]"
                                                        class="form-control available-qty" readonly required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label>Dispatch Qty</label>
                                                    <input type="number" name="products[0][assign_qty]"
                                                        class="form-control assign-qty" required min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex custom-button-div">
                                                <button type="button" class="btn btn-success add-row"
                                                    style="height: 38px;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4 ml-1">
                                        <button type="submit" class="btn btn-primary">
                                            Add Product Dispatch
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

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            const productStocks = @json($productStocks);
            let rowCount = 1;

            // Initialize Select2 on dropdowns
            function initSelect2(element) {
                $(element).select2({
                    allowClear: true,
                    placeholder: $(element).find('option:first').text()
                });
            }

            // Initialize Select2 for dealer
            initSelect2('#dealer_select');

            // Initialize Select2 for first row product and size selects
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="products[${rowCount}][product_id]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="products[${rowCount}][size_id]" class="form-control size-select" required>
                                <option value="">Select Size</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" name="products[${rowCount}][available_qty]" class="form-control available-qty" readonly required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" name="products[${rowCount}][assign_qty]" class="form-control assign-qty" required min="1">
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

            // Populate sizes when product changes
            $(document).on('change', '.product-select', function() {
                const productId = $(this).val();
                const row = $(this).closest('.product-row');
                const sizeSelect = row.find('.size-select');
                const availableQty = row.find('.available-qty');

                sizeSelect.empty().append('<option value="">Select Size</option>');
                availableQty.val('');

                if (!productId) return;

                const availableSizes = productStocks.filter(
                    stock => stock.product_id == productId && stock.available > 0
                );

                availableSizes.forEach(stock => {
                    sizeSelect.append(
                        `<option value="${stock.size_id}" data-available="${stock.available}">${stock.size.size}</option>`
                    );
                });

                // Refresh Select2
                sizeSelect.trigger('change');
            });

            // Show available quantity when size is selected
            $(document).on('change', '.size-select', function() {
                const selected = $(this).find('option:selected');
                const available = selected.data('available') || 0;
                $(this).closest('.product-row').find('.available-qty').val(available);
            });

            // Validate dispatch quantity
            $(document).on('change', '.assign-qty', function() {
                const row = $(this).closest('.product-row');
                const available = parseFloat(row.find('.available-qty').val()) || 0;
                let assigned = parseFloat($(this).val()) || 0;

                if (assigned > available) {
                    alert(`Cannot dispatch more than available (${available})`);
                    $(this).val(available);
                } else if (assigned < 1) {
                    $(this).val(1);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productDropdown = document.getElementById('product-dropdown');
            const sizeDropdown = document.getElementById('size-dropdown');
            const totalQtyInput = document.getElementById('total-qty');

            productDropdown.addEventListener('change', function() {
                const selectedProductId = parseInt(this.value);

                // Filter stock for this product
                const matchingStocks = productStocks.filter(stock => stock.product_id ===
                    selectedProductId);

                // Clear and repopulate size dropdown
                sizeDropdown.innerHTML = '<option value="">Select Size</option>';
                matchingStocks.forEach(stock => {
                    if (stock.size) {
                        sizeDropdown.innerHTML +=
                            `<option value="${stock.size_id}" data-total="${stock.available}">${stock.size.size}</option>`;
                    }
                });

                // Reset total qty when product changes
                totalQtyInput.value = '';
            });

            sizeDropdown.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const totalQty = selectedOption.getAttribute('data-total');
                totalQtyInput.value = totalQty ?? '';
            });
        });
    </script>



@endsection
