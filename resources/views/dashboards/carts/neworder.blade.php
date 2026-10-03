@extends('dashboards.sessions.layout')
@section('content')
    <div class="loading-icon" style="display:none;" id="loading-icon">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <?php
    $url = request()->segment(count(request()->segments()));
    ?>
    <style>
        #ndp-nepali-box {
            top: 398px !important;
        }

        .cmb-3 {
            margin-bottom: 15px;
        }

        .loading-icon {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            font-size: 48px;
        }

        .cross-mark,
        .equal-sign,
        .remove-btn {
            position: absolute;
            top: 30px;
        }

        .order-cross-mark {
            padding-left: 7px;
            padding-right: 7px;

        }

        #append_section {
            margin-top: 10px;
        }

        .dropdown-menu.open {
            width: 100% !important;
        }

        .custom-input {
            justify-content: center;
            /* margin-left: 240px; */
        }

        input[readonly] {
            color: black !important;
            background-color: #9999997a !important;
            cursor: not-allowed;
        }

        .size-td,
        .size-th {
            width: 15%;
        }

        .action-td,
        .action-th {
            width: 9%;
        }

        .qty-td,
        .qty-th {
            width: 7%;
        }

        .img-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .image {
            margin-right: 10px;
        }

        .dropdown-backdrop {
            position: unset !important;
        }

        .error-msg {}
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Add Order</h1>
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

                {{-- Sidebar --}}
                @include('dashboards.orders.sidenavbar')

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
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="has-error">{{ $error }}</div>
                                @endforeach
                            @endif
                            <div id="error-msg">
                            </div>

                            @php
                                $imgs = explode(' :: ', $mobileOrder->image);
                            @endphp
                            <div class="img-row">
                                @foreach ($imgs as $img)
                                    @if (!$img)
                                        <?php
                                        continue;
                                        ?>
                                    @endif
                                    <div class="image">
                                        <img src="{{ asset('storage/orderimage/' . $img) }}" width="250px"
                                            style="objeect-fit:contain;" />
                                    </div>
                                @endforeach
                            </div>
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-envelope font-red" id="error"></i>
                                        <span class="caption-subject font-red bold uppercase">Add Order</span>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <div class="form col-md-8">
                                        <form method="POST" id="submit-form" action="/addstoreOrder">
                                            @csrf
                                            <div class="form-group row">
                                                <label for="dealer"
                                                    class="col-md-3 col-form-label text-right">{{ __('Dealers') }}</label>
                                                <div class="col-md-7">
                                                    <input type="hidden" name="dealer" id="dealer"
                                                        value="{{ $mobileOrder->dealer_code }}">
                                                    <input type="text" class="form-control" readonly
                                                        value="{{ $mobileOrder->dealer_code }} - {{ $mobileOrder->dealer['name'] }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="user"
                                                    class="col-md-3 col-form-label text-right">{{ __('Mobile
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    User') }}</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" readonly
                                                        name="mobileuser_name" value="{{ $mobileOrder->mobileuser->name }}">
                                                    <input type="hidden" name="mobileuser" id="mobileuser"
                                                        value="{{ $mobileOrder->mobileuser_id }}"
                                                        data-info="{{ $mobileOrder->mobileuser->name }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label text-right">Product<span
                                                        class="required"></span></label>
                                                <div class="col-md-7">
                                                    <select class="form-control selectpicker" name="product"
                                                        data-live-search="true" id="product" style="overflow-y: auto">
                                                        <option value="" selected disabled>All Product</option>
                                                        @foreach ($category as $category)
                                                            <option value="{{ $category->id }}"
                                                                pcname="{{ $category->name }}">
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label text-right">Sub-Product<span
                                                        class="required"></span></label>

                                                <div class="col-md-7">
                                                    <select class="form-control selectpicker" name="subproduct"
                                                        data-live-search="true" id="subproduct" style="overflow-y: auto">
                                                        <option value="" selected disabled>All Sub-Product</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="order_id" value="{{ $order ? $order->id : null }}">
                                            <div class="no-custom form-group "
                                                style="display:none;height:400px;overflow-y:scroll;width:fit-content;text-align:center;">
                                                <div class="row">
                                                    <div id="add_section" class="col-md-12"></div>
                                                </div>
                                            </div>
                                            <div class="order-quantity-section" style="display: none;">
                                                <div class="order-quantity-section-title d-flex col-md-12">
                                                    <div class="title col-md-3 text-right cmb-3"
                                                        style="text-decoration: underline">Order Quantity</div>
                                                </div>
                                                <div class="order-quantity-section-body"></div>
                                            </div>
                                            <br>
                                            <div class="form-group row mb-0">
                                                <div class="col-md-6 col-md-offset-3">
                                                    <input type="submit" class="btn btn-primary submit"
                                                        style="width: 200px;" value="Add to List" id="add-to-list-btn"
                                                        disabled>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4 display-items">
                                        @php
                                            $totalQuantity = 0;
                                            $grandPrice = 0;
                                        @endphp
                                        @foreach ($orderItem as $index => $product)
                                            <h5><strong>{{ $index }} (
                                                    {{ $product[0]->items->product->category->name }} )</strong></h5>
                                            <table class="table table-bordered">
                                                <thead class="thead ">
                                                    <tr>
                                                        <th>Size</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tbody">
                                                    @php
                                                        $quantity = 0;
                                                        $total_price = 0;
                                                    @endphp
                                                    @foreach ($product as $key => $value)
                                                        @php
                                                            $quantity += $value->quantity;
                                                            $total_price += $value->total_price;
                                                            $totalQuantity += $value->quantity;
                                                            $grandPrice += $value->total_price;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $value->items->size->size }}</td>
                                                            <td>{{ $value->quantity }}</td>
                                                            <td>{{ $value->total_price }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Total Price</th>
                                                        <td>{{ $quantity }}</td>
                                                        <td>{{ $total_price }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        @endforeach
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Grand Total</th>
                                                <th>{{ $totalQuantity }}</th>
                                                <th>{{ $grandPrice }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Show cart table section if there is data --}}
                    <div class="carttable" id="carttable"
                        @if (count($carts) > 0) style="display:block" @else
                    style="display:none" @endif>
                        <div id="tbl-cart-data">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Order List </span>
                                    </div>

                                </div>

                                <form action="{{ route('store-order') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="orderreq" value="{{ $mobileOrder->id }}">
                                    <input type="hidden" name="user_id" value="{{ $mobileOrder->mobileuser_id }}">
                                    <input type="hidden" name="dealer_code" value="{{ $mobileOrder->dealer_code }}">
                                    @include('dashboards.carts.orderlist')
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
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
        $(function() {
            const $product = $('#product');
            const $subproduct = $('#subproduct');
            const $addBtn = $('#add-to-list-btn');
            const $orderQtySection = $('.order-quantity-section');
            const $orderQtyBody = $('.order-quantity-section-body');

            function toggleAddToListBtn() {
                $addBtn.prop('disabled', !($product.val() && $subproduct.val()));
            }

            function populateSubproducts(productId) {
                $.get('/productlist?category=' + productId, function(data) {
                    let options = '<option value="" selected disabled>All Sub-Product</option>';
                    data.dealers.forEach(product => {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    $subproduct.html(options).selectpicker('refresh');
                    toggleAddToListBtn();
                });
            }

            function populateProductSizes(subproductId) {
                $.get('/productsize?product=' + subproductId, function(data) {
                    $orderQtyBody.empty();
                    $orderQtySection.show().css('height', (data.dealers.length * 60) + 'px');
                    let division = '';
                    data.dealers.forEach(dat => {
                        division += `<div class="d-flex cmb-3 col-md-12">
                        <div class="col-md-3 text-right">${dat.size.size}</div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="quantity[]" />
                        </div>
                        <div class="col-md-2"></div>
                    </div>`;
                    });
                    $orderQtyBody.append(division);
                });
            }

            $product.on('change', function() {
                const productId = $(this).val();
                $subproduct.val('').selectpicker('refresh');
                populateSubproducts(productId);
                toggleAddToListBtn();
            });

            $subproduct.on('change', function() {
                const subproductId = $(this).val();
                populateProductSizes(subproductId);
                toggleAddToListBtn();
            });

            // Initial state
            toggleAddToListBtn();
        });
    </script>
@endsection
