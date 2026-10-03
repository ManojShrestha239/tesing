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
                                        <span class="caption-subject font-red bold uppercase">Dispatch Product </span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="{{ route('subdealer-inventory.store') }}" id="dispatch-form"
                                    class="ml-3 custom-form">
                                    @csrf

                                    <!-- Dealer, Bill Number, and Date Row -->
                                    <div class="row align-items-end mb-4">
                                        <div class="col-md-3">
                                            <div class="form-group mb-0">
                                                <label>Sub Dealer</label>
                                                <select name="dealer_id" class="form-control" required>
                                                    <option value="">Select Sub-dealers</option>
                                                    @foreach ($dealers as $dealer)
                                                        <option value="{{ $dealer->code }}">{{ $dealer->name }}</option>
                                                    @endforeach
                                                </select>
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
                                                    <label>Add Qty</label>
                                                    <input type="number" name="products[0][assign_qty]"
                                                        class="form-control assign-qty" required min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex custom-button-div">
                                                <button type="button" class="btn btn-success add-row"
                                                    style="height: 38px;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                {{-- <button type="button" class="btn btn-danger remove-row ml-2"
                                                    style="height: 38px;">
                                                    <i class="fa fa-minus"></i>
                                                </button> --}}
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

    <script>
        (function() {
            // Base URL from Laravel helper to avoid subdirectory issues
            var BASE_URL = "{{ url('/') }}";

            // Utility: build endpoint for fetching sizes for a product
            function buildSizesUrl(productId) {
                // Per request, the API is sudealer-inventory/{id}
                return BASE_URL + "/subdealer-inventory/" + encodeURIComponent(productId);
            }

            // Reset a row's size select and related fields
            function resetRow($row) {
                var $size = $row.find('.size-select');
                var $available = $row.find('.available-qty');
                var $assign = $row.find('.assign-qty');

                $size.empty().append('<option value="">Select Size</option>').prop('disabled', false);
                $available.val('');
                // Do not force reset of assign if user already typed, but clear on product change
                if ($assign.length) $assign.val('');
            }

            // Handle product selection change (event delegation for dynamic rows)
            $(document).on('change', '.product-select', function() {
                var productId = $(this).val();
                var $row = $(this).closest('.product-row');
                var $size = $row.find('.size-select');
                var $available = $row.find('.available-qty');

                // Clear existing selections/values
                resetRow($row);

                if (!productId) {
                    return; // nothing selected
                }

                // Indicate loading state
                $size.prop('disabled', true)
                    .empty()
                    // .append('<option value="">Loading sizes…</option>');
                    .append('<option value="">Select Size</option>');

                // Fetch sizes for the selected product
                $.ajax({
                    url: buildSizesUrl(productId),
                    method: 'GET',
                    dataType: 'json'
                }).done(function(res) {
                    // Normalize possible payload shapes
                    var list = [];
                    if (Array.isArray(res)) {
                        list = res;
                    } else if (res && Array.isArray(res.sizes)) {
                        list = res.sizes;
                    } else if (res && Array.isArray(res.data)) {
                        list = res.data;
                    }

                    $size.empty();
                    $size.append('<option value="">Select Size</option>');

                    if (!list.length) {
                        $size.append('<option value="" disabled>No sizes available</option>');
                        $size.prop('disabled', false);
                        return;
                    }

                    // Build options; support nested size object per provided API shape
                    list.forEach(function(item) {
                        var value = item.size_id || item.id || item.value || '';
                        var nestedSizeLabel = (item.size && (item.size.size || item.size
                            .name)) ? (item.size.size || item.size.name) : '';
                        var label = nestedSizeLabel || item.size_name || item.name || item
                            .size || item.label || ('Size #' + value);
                        var available = item.available_qty || item.available || item.qty || '';

                        var $opt = $('<option>')
                            .val(value)
                            .text(label);

                        if (available !== '' && available !== null && available !== undefined) {
                            $opt.attr('data-available-qty', available);
                        }

                        $size.append($opt);
                    });

                    $size.prop('disabled', false);
                    // Clear available qty since size isn't chosen yet
                    $available.val('');
                }).fail(function(xhr) {
                    console.error('Failed to load sizes:', xhr && xhr.responseText ? xhr.responseText :
                        xhr);
                    $size.prop('disabled', false)
                        .empty()
                        .append('<option value="" disabled>Error loading sizes</option>');
                    alert('Unable to load sizes for the selected product. Please try again.');
                });
            });

            // When a size is selected, if option has available qty, populate the input
            $(document).on('change', '.size-select', function() {
                var $row = $(this).closest('.product-row');
                var $available = $row.find('.available-qty');
                var $selected = $(this).find('option:selected');
                var availableQty = $selected.data('available-qty');

                if (availableQty !== undefined) {
                    $available.val(availableQty);
                } else {
                    $available.val('');
                }
            });

            // ------- Dynamic rows: add/remove and reindex -------
            function reindexRows() {
                $('#product-rows .product-row').each(function(idx) {
                    var $row = $(this);
                    // Update name attributes to sequential indices
                    $row.find('.product-select').attr('name', 'products[' + idx + '][product_id]');
                    $row.find('.size-select').attr('name', 'products[' + idx + '][size_id]');
                    $row.find('.assign-qty').attr('name', 'products[' + idx + '][assign_qty]');
                });
            }

            function toggleRemoveButtons() {
                var $rows = $('#product-rows .product-row');
                if ($rows.length <= 1) {
                    $rows.find('.remove-row').prop('disabled', true).addClass('disabled');
                } else {
                    $rows.find('.remove-row').prop('disabled', false).removeClass('disabled');
                }
            }

            // Add new product row
            $(document).on('click', '.add-row', function() {
                var $rows = $('#product-rows .product-row');
                var nextIndex = $rows.length;
                // Copy product options from first row to keep list in sync
                var productOptionsHtml = $rows.first().find('.product-select').html();

                var rowHtml = '' +
                    '<div class="row align-items-center product-row mb-3">' +
                    '  <div class="col-md-3">' +
                    '    <div class="form-group mb-0">' +
                    '      <label>Product</label>' +
                    '      <select name="products[' + nextIndex +
                    '][product_id]" class="form-control product-select" required>' +
                    (productOptionsHtml || '<option value="">Select Product</option>') +
                    '      </select>' +
                    '    </div>' +
                    '  </div>' +
                    '  <div class="col-md-2">' +
                    '    <div class="form-group mb-0">' +
                    '      <label>Size</label>' +
                    '      <select name="products[' + nextIndex +
                    '][size_id]" class="form-control size-select" required>' +
                    '        <option value="">Select Size</option>' +
                    '      </select>' +
                    '    </div>' +
                    '  </div>' +
                    '  <div class="col-md-2">' +
                    '    <div class="form-group mb-0">' +
                    '      <label>Dispatch Qty</label>' +
                    '      <input type="number" name="products[' + nextIndex +
                    '][assign_qty]" class="form-control assign-qty" required min="1">' +
                    '    </div>' +
                    '  </div>' +
                    '  <div class="col-md-1 d-flex custom-button-div">' +
                    '    <button type="button" class="btn btn-danger remove-row ml-2" style="height: 38px;">' +
                    '      <i class="fa fa-minus"></i>' +
                    '    </button>' +
                    '  </div>' +
                    '</div>';

                $('#product-rows').append(rowHtml);
                // Reset selects/inputs in newly added row (product default empty)
                var $newRow = $('#product-rows .product-row').last();
                // Ensure first option is empty
                $newRow.find('.product-select').val('');
                $newRow.find('.size-select').empty().append('<option value="">Select Size</option>');
                $newRow.find('.assign-qty').val('');

                reindexRows();
                toggleRemoveButtons();
            });

            // Remove an existing product row (keep at least one)
            $(document).on('click', '.remove-row', function() {
                var $rows = $('#product-rows .product-row');
                if ($rows.length <= 1) {
                    return; // do not remove last row
                }
                $(this).closest('.product-row').remove();
                reindexRows();
                toggleRemoveButtons();
            });

            // Initialize remove button availability on load
            $(function() {
                toggleRemoveButtons();
            });
        })();
    </script>
@endsection
