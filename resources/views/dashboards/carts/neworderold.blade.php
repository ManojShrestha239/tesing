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
		.error-msg{
			
		}
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Register New Notice</h1>
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
                                        {{-- <img src="{{asset('productimage/1650283415.jpg')}}" alt="5" width="200px" height="100px"> --}}
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

                                <form method="POST" id="submit-form" action="/dashboard/addstoreOrder">
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
                                            <input type="text" class="form-control" readonly name="mobileuser_name"
                                                value="{{ $mobileOrder->mobileuser->name }}">
                                            <input type="hidden" name="mobileuser" id="mobileuser"
                                                value="{{ $mobileOrder->mobileuser_id }}"
                                                data-info="{{ $mobileOrder->mobileuser->name }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="category"
                                            class="col-md-3 col-form-label text-right">{{ __('Category') }}</label>
                                        {{-- <label for="product" class="col-md-3 col-form-label text-right">{{ __('Product list')
										}}</label> --}}
                                        <div class="col-md-7">
                                            <select name="category" id="category" class="form-control selectpicker"
                                                data-live-search="true" style="overflow-y: auto">
                                                <option value="" selected disabled>Select Category</option>
                                                @foreach ($category as $category)
                                                    <option value="{{ $category->id }}" pcname="{{ $category->name }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- <select name="product" id="product" class="form-control selectpicker" data-live-search="true">
											<option value="" selected disabled>Select Your Product</option>
											@foreach ($products as $key => $product)
											<option value="{{$product->id}}" price="{{$product->price}}" size="{{$product->size['size']}}"
												pname="{{$product->product['name']}}" sname="{{$product->subproduct['subproduct']}}"
												pcname="{{$product->product->category->name}}">
												{{$product->product->category->name}}-{{$product->product['name']}}
												-{{$product->subproduct['subproduct']}}-{{$product->size['size']}}
											</option>
											@endforeach
										</select> --}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-right">Product<span
                                                class="required"></span></label>

                                        <div class="col-md-7">
                                            <select class="form-control selectpicker" name="product" data-live-search="true"
                                                id="product" style="overflow-y: auto">
                                                <option value="" selected disabled>All Product</option>
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
                                    {{-- <div class="form-group row">
							<label class="col-md-3 col-form-label text-right">Size<span class="required"></span></label>

							<div class="col-md-7">
									<select class="form-control selectpicker" name="product-size" data-live-search="true" id="product-size" style="overflow-y: auto">
										<option value="" selected disabled>All Sizes</option>
									</select>
						</div>
					</div> --}}

                                    {{-- size hidden --}}
                                    <div>
                                        <input type="hidden" name="len" id="len">
                                        <input type="hidden" name="wid" id="wid">
                                    </div>
                                    {{-- Price --}}
                                    <div class="form-group row" id="product_price" style="display:none;">
                                        <label for="price"
                                            class="col-md-3 col-form-label text-right">{{ __('Price') }}</label>
                                        <div class="col-md-7">
                                            <input type="text" id="price_row" class=" form-control price"
                                                name="price" value="Rs 0" readonly>
                                        </div>
                                    </div>
                                    {{-- Price End --}}

                                    {{-- show this section when product size isnt custom --}}
                                    <div class="no-custom form-group "
                                        style="display:none;height:400px;overflow-y:scroll;width:fit-content;text-align:center;">
                                        <div class="row">
                                            <div id="add_section" class="col-md-12">
                                            </div>

                                            {{-- <div id="no_custom_qty">
                                            <div class="form-group row">
                                                <label for="user"
                                                    class="col-md-3 col-form-label text-right">{{ __('Quantity') }}</label>
                                                <div class="col-md-7">
                                                    <input type="number" min="0" name="quantity"
                                                        placeholder="Qty" class="form-control custom" data-row="0"
                                                        id="no_custom_quanity">
                                                    <span class="field-validation-valid" data-valmsg-for="quantity"
                                                        data-valmsg-replace="true"></span>
                                                    @if ($errors->has('quantity'))
                                                        <span class="help-block required">
                                                            <strong>{{ $errors->first('quantity') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div id="no-custom-total">
                                            <div class="form-group row">
                                                <label for="user"
                                                    class="col-md-3 col-form-label text-right">{{ __('Total Price') }}</label>
                                                <div class='col-md-7'>
                                                    <input type="text" name="totalprice"
                                                        class="total form-control custom" readonly
                                                        placeholder="Total Price" id="no_custom_total_price">
                                                </div>
                                            </div>
                                        </div> --}}
                                        </div>
                                        {{-- End of no-custom class --}}


                                    </div>

                                    <div class="checkbox" style="display:none;">
                                        <div class="row">
                                            <div id="add_checkbox" class="col-md-12">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Show this section when product size is custom --}}
                                    <div class="custom-size" style="display:none;">
                                        <div class="row">
                                            <i style="margin-left:500px;color:green; cursor:pointer;"
                                                class="fa fa-plus add-btn" aria-hidden="true">Add More</i>
                                        </div>
                                        <div id="append_section">
                                            {{-- Custom size Input field will be appened from js --}}
                                        </div>
                                    </div>
                                    {{-- End of custom class --}}






                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <input type="submit" class="btn btn-primary submit" style="width: 200px;"
                                                value="Add to List">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Show cart table section if there is data --}}
                    <div class="carttable" id="carttable"
                        @if (count($carts) > 0) style="display:block" @else style="display:none" @endif>
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
@endsection

@section('scripts')
    <script>
        var heading = `
	<div class="row">
	<div class="col-md-3"></div>
    	<div class="form-group col-md-9">
				<div class="col-md-12 row" >
											
												<div class="col-md-4">

													<label for="psize" class=" col-form-label text-right">{{ __('SIZE') }}</label>
													
												</div> 
											<div class="col-md-8 row" >
												<div class="col-md-6">
													<label for="quantity" class=" col-form-label text-right">{{ __('Quantity') }}</label>
													
												</div> 
												<div class="col-md-6">
													<label for="Total Price" class=" col-form-label text-right">{{ __('Total Price ') }}</label>
													
												</div>
												
											</div>
										
										</div></div>`;


        // Field that will be append when custom size product is selected
        var field = `
	<div class="row">
	<div class="col-md-3"></div>
    	<div class="form-group col-md-9">
				<div class="col-md-12">
											<div class="col-md-4">
												<div class="col-md-6">

													<label for="length" class=" col-form-label text-right">{{ __('Length') }}</label>
													<input type="number" min="0" name="length[]" class="form-control length" placeholder="L">
												</div> <span class="cross-mark">X</span>
												<div class="col-md-6">
													<label for="width" class=" col-form-label text-right">{{ __('Width') }}</label>
													<input type="number" min="0" name="width[]" class="form-control width" placeholder="W">
												</div>
											</div>
											<div class="col-md-8" >
												<div class="col-md-3">
													<label for="quantity" class=" col-form-label text-right">{{ __('Quantity') }}</label>
													<input type="number" min="0" name="quantity[]"  placeholder="Qty" class="form-control custom"
														data-row="0">
													<span class="field-validation-valid" data-valmsg-for="quantity"
														data-valmsg-replace="true"></span>
													@if ($errors->has('quantity'))
													<span class="help-block required">
														<strong>{{ $errors->first('quantity') }}</strong>
													</span>
													@endif
												</div> <span class="equal-sign">=</span>
												<div class="col-md-4">
													<label for="Total Price" class=" col-form-label text-right">{{ __('Total Price ') }}</label>
													<input type="text" name="totalprice[]" class="total form-control custom" readonly
														placeholder="Total Price">
												</div>
												<div class="col-md-2">
													<i style="color:red; cursor:pointer;" class="fa fa-minus remove-btn" aria-hidden="true"></i>
												</div>
											</div>
										</div>
										</div></div>`;




        $(document).ready(function() {
            $('#submit-form').on('submit', function(e) {
                e.preventDefault();
                // show the loading icon
                $('#loading-icon').show();
                let rowCount = $('#cart_tbody tr').length;
                let data = [];
                let cartData = [];

                let dealer = $('#dealer').val();
                let mobUser = $('#mobileuser').attr('data-info');
                let mobUserId = $('#mobileuser').val();
                let pname = $('#product').find('option:selected').attr('pname');
                let sname = $('#subproduct').find('option:selected').attr('sname');
                let pcname = $('#category').find('option:selected').attr('pcname');
                let product = `${pcname}-${pname}-${sname}`;
                // let productId = $('#product-size').val();

                // let qty = $('#no_custom_quanity').val();
                // let totalprice = $('#no_custom_total_price').val();


                // let isCustom = $("input[name='yesNo']:checked").val();

                // Post request body
                // console.log(isCustom);



                // let size = '';


                let size = $("input[name='psize[]']");
                console.log(size.length);
                let totprice = $("input[name='totprice[]']")
                let qty = $("input[name='qty[]']");
                let productId = $("input[name='pid[]']");
                let price = $("input[name='pprice[]']");

                for (let index = 0; index < size.length; index++) {
                    if (qty[index].value != 0) {
                        let bodyData = {
                            dealer: dealer,
                            mobUser: mobUserId,
                            mobUserInfo: mobUser,
                            product: productId[index].value,
                            productInfo: product,
                            price: price[index].value,
                            size: size[index].value,
                            qty: qty[index].value,
                            totalPrice: price[index].value * qty[index].value,
                            isCustom: false
                        }
                        // console.log(bodyData)
                        cartData.push(bodyData);
                    }
                }






                let custom_price = $("input[name='custom-p-price[]']").val();
                let custom_productId = $("input[name='custom-p-id[]']").val();
                let length = $("input[name='length[]']");

                let width = $("input[name='width[]']");
                let cusQty = $("input[name='quantity[]']");
                for (let indexes = 0; indexes < length.length; indexes++) {
                    if (cusQty[indexes].value != 0) {
                        let cusBodyData = {
                            dealer: dealer,
                            mobUser: mobUserId,
                            mobUserInfo: mobUser,
                            product: custom_productId,
                            productInfo: product,
                            price: custom_price,
                            size: null,
                            qty: null,
                            totalPrice: null,
                            isCustom: true
                        }

                        customSize = length[indexes].value + 'x' + width[indexes].value;
                        customQty = cusQty[indexes].value;
                        cusBodyData.size = customSize;
                        cusBodyData.qty = customQty;
                        cusBodyData.totalPrice = customQty * custom_price;
                        // Making data ready to send to save into cart table

                        cartData.push(cusBodyData);
                    }
                }




                const storeToCartUrl = "{{ route('store-to-cart') }}";
                // Making the ajax request to save data to cart table
                $.ajax({
                    url: storeToCartUrl,
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        cartData
                    },
                    success: function(response) {
                        console.log(response);

                        for (let index = 0; index < response.data.length; index++) {
                            const tbody = prepareRow(response.data[index], ++rowCount);
                            $('#cart_tbody').append(tbody);







                            if (isCustom = 'yes') {
                                // when custom size product is selected and add more is pressed
                                // and then switched to next product
                                // reseting all the state to initial phase
                                $('#append_section').empty();
                                $('#append_section').append(field);
                            } else {
                                // when the products are switched
                                // reset the quantity and total price to empty
                                $('#no_custom_quanity').val('');
                                $('#no_custom_total_price').val('');
                            }

                        }
                        $('#carttable').show();
                        $('#category').val('');
                        $('#category').selectpicker('refresh');
                        $('#product').val('');
                        $('#product').selectpicker('refresh');
                        $('#subproduct').val('');
                        $('#subproduct').selectpicker('refresh');
                        $('#product-size').val('');
                        $('#product-size').selectpicker('refresh');
                        $('#price_row').val('');
                        $('.custom-size').hide();
                        $('.no-custom').hide();
                        $('#product_price').hide()
                        $('#loading-icon').hide();

                    },
                    error: function(error) {
                        
                        $.each(error.responseJSON.message, function(index, value) {
                            // Do something with each element
							$("#error-msg").append(`<ul><li style="color:rgb(240, 126, 126)">` + value + `</li></ul>`);
                            
                        });
                        
                        $('#loading-icon').hide();

                    }
                });
            });
        });

        // Add an event listener to the add button using jQuery
        $(document).ready(function() {
            $('.add-btn').on('click', function() {
                // Append the element to the DOM
                $(`#append_section`).append(field);
            });
        });


        // Add an event listener to the remove-btn using jQuery
        $(document).on('click', '.remove-btn', function() {
            $(this).closest('.form-group').remove();
        });


        // $(document).ready(function() {
        //     $("#product").on('change', function() {
        //         // console.log('asda');
        //         let price = $(this).find('option:selected').attr('price');
        //         let size = $(this).find('option:selected').attr('psize').toLowerCase();

        //         $('#price_row').val(price);
        //         // Price will be shown once the product is selected
        //         $('#product_price').show();

        //         if (size != 'custom') {
        //             let lengthWidth = size.split("x");
        //             // console.log(lengthWidth);
        //             $('#len').val(lengthWidth[0]);
        //             $('#wid').val(lengthWidth[1]);
        //             // show/hide the non-custom and custom-size div section
        //             $('.no-custom').show();
        //             $('.custom-size').hide();
        //         } else {
        //             $('#len').val('');
        //             $('#wid').val('');
        //             // show/hide the non-custom and custom-size div section
        //             $('.no-custom').hide();
        //             $('.custom-size').show();
        //         }
        //         // when the products are switched
        //         // reset the quantity and total price to empty
        //         $('#no_custom_quanity').val('');
        //         $('#no_custom_total_price').val('');

        //         // when custom size product is selected and add more is pressed
        //         // and then switched to next product
        //         // reseting all the state to initial phase
        //         $('#append_section').empty();
        //         $('#append_section').append(field);
        //     });

        // });

        // Calcualte Total when the product is non custom size
        // $(document).on('input change', 'input[name="qty[]"]', function() {
        //     let quantity = $(this).val();
        //     var price = $("input[name='pprice[]']").val();
        //     var total = quantity * price;
        //     $(this).closest('.form-group').find('input[name="totprice[]"]').val(total.toFixed(2));
        // });
        $(document).on('input change', 'input[name="qty[]"]', function() {
            let quantity = $("input[name='qty[]']");
            let price = $("input[name='pprice[]']");
            let totprice = $("input[name='totprice[]']");
            quantity.change(function() {
                let qty = $(this).val();
                let index = quantity.index(this);
                let pprice = price[index].value;
                let totalprice = qty * pprice;
                totprice.eq(index).val(totalprice);
            });
        });

        // Calcualte Total when the product is custom size
        $(document).on('input change', 'input[name="quantity[]"]', function() {
            let quantity = $(this).val();
            var price = $('input[name="custom-p-price[]"]').val();
            var total = quantity * price;
            $(this).closest('.form-group').find('input[name="totalprice[]"]').val(total.toFixed(2));
        });


        // Update the total price when quantity is edited
        $(document).on('input', '.order-qty', function() {
            let quantity = $(this).val();
            let price = $(this).closest('tr').find('.price_row').text();
            let total = (quantity * price).toFixed(2);
            $(this).closest('tr').find('.total-price').text(total);
        });



        // save editable quantity, size ana totalprice

        $(document).on('click', '.edit-save', function() {
            $('#loading-icon').show();
            const cartId = $(this).data('product-id');
            let $that = $(this);
            let quantity = $(this).closest('tr').find('.order-qty').val();
            let length = $(this).closest('tr').find('.cus-length').val();
            let width = $(this).closest('tr').find('.cus-width').val();

            $.ajax({
                url: `/dashboard/store-tocart/${cartId}`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'PATCH',
                data: {
                    quantity,
                    length,
                    width
                },
                success: function(response) {
                    let inputField = $that.closest('tr').find('.order-qty');
                    $that.closest('tr').find('.order-size');
                    // inputField.attr('readonly', 'readonly');
                    $('#loading-icon').hide();
                },
                error: function(error) {
                    console.log(error);
                    $('#loading-icon').hide();
                }
            });
        });


        // Ajax request for delete

        $(document).on('click', '.delete', function() {
            $('#loading-icon').show();
            const delCartId = $(this).data('deletecart-id');
            let $that = $(this);

            $.ajax({
                url: `/dashboard/storetocart/${delCartId}`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'DELETE',
                success: function(response) {
                    $('#cart_tbody').empty();
                    let sn = 0;
                    for (let index = 0; index < response.data.length; index++) {
                        const tbody = prepareRow(response.data[index], ++sn);
                        $('#cart_tbody').append(tbody);
                    }
                    $('#loading-icon').hide();
                },
                error: function(error) {
                    console.log(error);
                    $('#loading-icon').hide();
                }
            });
        });


        // When Cart is saved then we use this response to populate the data in table (blade file)
        function prepareRow(data, sn) {
            return `
						<tr>
						<td>${sn}</td>
           
            <td>${data['productInfo']} ${data['isCustom'] == 'true' ? ' CUSTOM' : ''}</td>
						<td clas="size-td">
                <div class="row">
                    <div class="col">
                        <input type="text" name="cus_length[]" class="order-size cus-length form-control" value="${data['length']}"
                            ${data['isCustom'] == 'true' ? '' : 'readonly' }>
                    </div><span class="order-cross-mark">x</span>
                    <div class="col">
                        <input type="text" name="cus_width[]" class="order-size cus-width form-control" value="${data['width']}"
                            ${data['isCustom'] == 'true' ? '' : 'readonly' }>
                    </div>
                </div>
            </td>

            <td class="price_row">${data['price']}</td>

							<td class="qty-td">
						<input type="number" name="cus_quantity[]" class="order-qty form-control" value="${data['qty']}" min="1"
              data-oldvalue="${data['qty']}">
						</td>
            <td class="total-price">${data['totalPrice']}</td>
						<td class="action-td">
							<a class="edit-save" style="display: block;" data-product-id="${data['id']}"><i class="fa fa-pencil-square-o me-1"
                aria-hidden="true"></i>Update</a>

								<a class="link-danger delete" data-deletecart-id="${data['id']}><i class="fa fa-trash me-1" aria-hidden="true" ">Delete</i></a>
						</td>

					</tr>`;
        }




        $(document).on('change', '#category', function(e) {
            let category = $(this).val();


            $.ajax({
                url: '/dashboard/productlist?category=' + category,
                type: 'get',
                dataType: 'json',
                success: function(data) {

                    $('#product').empty();
                    $("#product").append('<option value="" >All Product </option>');
                    data.dealers.forEach(function(e, index) {
                        $("#product").append('<option value="' + e.id + '"pname="' + e['name'] +
                            '">' + e['name'] + '</option>');
                    });
                    $("#product").selectpicker("refresh");
                    $("#subproduct").empty();
                    $("#subproduct").selectpicker("refresh");
                    $('#append_section').empty();
                    $('#add_section').empty();
                    $(".custom-size").hide();
                    $(".no-custom").hide();
                }
            })
        });

        $(document).on('change', '#product', function(e) {
            let product = $(this).val();

            $.ajax({
                url: '/dashboard/sub-productlist?product=' + product,
                type: 'get',
                dataType: 'json',
                success: function(data) {


                    $('#subproduct').empty();
                    $("#subproduct").append('<option value="">All Sub-Product</option>');
                    data.dealers.forEach(function(e, index) {

                        $("#subproduct").append('<option value="' + e.id + '"sname="' + e[
                            'subproduct'] + '" >' + e['subproduct'] + '</option>');
                    });
                    $("#subproduct").selectpicker("refresh");
                    $('#append_section').empty();
                    $('#add_section').empty();
                    $(".custom-size").hide();
                    $(".no-custom").hide();



                }
            })
        });



        $(document).on('change', '#subproduct', function(e) {
            $(".custom-size").hide();
            $("#add_section").empty();
            $("#add_section").append(heading);
            $(".no-custom").hide()
            let subproduct = $(this).val();
            let product = $('#product').val();

            $.ajax({
                url: '/dashboard/productsize?subproduct=' + subproduct +'&product='+ product,
                type: 'get',

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(data) {

                    var status = data.dealers.forEach(function(e, index) {
                        var size = e['size']['size'];
                        var pprice = e['price'];
                        var pid = e['id'];
                        if (size == 'CUSTOM') {
                            var status = 1;
                            $('#append_section').empty();
                            $('#append_section').append(field);
                            $("#append_section").append(
                                `<input type="hidden"  name="custom-p-id[]" class="form-control pid" value="` +
                                pid + `" readonly hidden>
								<input type="hidden"  name="custom-p-price[]" class="form-control p-price" value="` + pprice +
                                `" readonly hidden>`);
                            $(".custom-size").show();
                            return;
                        } else {
                            $(".no-custom").show();
                        }


                        $("#add_section").append(`<div class="row">
	<div class="col-md-3"></div>
    	<div class="form-group col-md-9">
				<div class="col-md-12 row" >
											
												<div class="col-md-4" style="text-align:center">

													<input type="text" min="0" name="psize[]" class="form-control psize" value="` + size + `" readonly>
													<input type="hidden"  name="pid[]" class="form-control pid" value="` + pid + `" readonly hidden>
													<input type="hidden"  name="pprice[]" class="form-control pprice" value="` + pprice + `" readonly hidden>
												</div> 
											<div class="col-md-8 row" >
												<div class="col-md-6 row" style="text-align:center">
													<input type="number" min="0" name="qty[]"  placeholder="Qty" class="form-control custom"
														data-row="0">
													<span class="field-validation-valid" data-valmsg-for="quantity"
														data-valmsg-replace="true"></span>
													@if ($errors->has('quantity'))
													<span class="help-block required">
														<strong>{{ $errors->first('quantity') }}</strong>
													</span>
													@endif
													<div>
													
													</div>
												</div> 
												<div class="col-md-6" style="text-align:center">
													<input type="text" name="totprice[]" class="total form-control custom" readonly
														placeholder="Total Price">
												</div>
											</div>
										</div>
										</div></div>`);

                    });




                }
            })
        });

        // 	$(document).on('change', "input[name='yesNo']", function(e) {

        // 	var selectedValue = $("input[name='yesNo']:checked").val();
        // 	// Do something with the selected value
        // 			  if (selectedValue=="yes"){
        // 				$(".checkbox").hide();


        // 				$("#append_section").append(field);

        // 			  }
        // 			  else{
        // 				$(".checkbox").hide();
        // 				$(".no-custom").show();
        // 			  }
        //   });



        // $(document).on('change', '#product-size', function(e) {
        // 	let price = $(this).find('option:selected').attr('price');
        // 	$('#price_row').val(price);
        // 	$('.no-custom').show();
        // });
        window.onload = function() {
            $("#category").trigger("change");
            // setTimeout(() => {

            // }, 200);
        }
        window.onload = function() {
            $("#product").trigger("change");
            // setTimeout(() => {

            // }, 200);
        }
        window.onload = function() {
            $("#sub-product").trigger("change");
            // setTimeout(() => {

            // }, 200);
        }
    </script>
@endsection
