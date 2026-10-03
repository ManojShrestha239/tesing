@extends('dashboards.sessions.layout')
@section('content')
<?php
$url = request()->segment(count(request()->segments()));
?>
<div class="page-content">
  <div class="breadcrumbs">
    <h1>All Orders</h1>

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
            <ul class="nav navbar-nav margin-bottom-35 orders-class">

              <li><a href="/order">All Orders </a></li>
              <li class="active"><a href="/addorder">Add Order </a></li>
              <li @if ($url=='pending' || $url=='Pending' ) class="active" @endif><a class="pending" href="/orders/Pending">Pending/Inventory </a></li>
              <li @if ($url=='Approved' || $url=='approved' ) class="active" @endif><a class="pending" href="/orders/approved">Account/Approved </a></li>
              <li @if ($url=='Ongoing' || $url=='ongoing' ) class="active" @endif><a class="ongoing" href="/orders/ongoing">Ongoing </a></li>
              <li @if ($url=='Completed' || $url=='completed' ) class="active" @endif><a class="completed" href="/orders/completed">Completed </a></li>
              <li @if ($url=='Cancelled' || $url=='cancelled' ) class="active" @endif><a class="cancelled" href="/orders/cancelled">Cancelled </a></li>
              <li @if ($url=='Urgent Order' || $url=='Urgent Order' ) class="active" @endif><a class="Urgent Order" href="/orders/Urgent Order">Urgent Order </a></li>
              <!-- <li><a href="/order/challan">Challan </a></li> -->
            </ul>

          </nav>
        </div>

      </div>

      <div class="page-content-col">
        @if (session('Status'))
        <div class="m-heading-1 border-green m-bordered">
          <div class="page-content-col">
            <div class="alert alert-success alert-dismissible" role="alert">
              <b>{{ session('Status') }}</b>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-md-12">
            @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light portlet-fit bordered">
              <div class="portlet-title">
                <div class="caption">
                  <i class=" fa fa-paint-brush font-red"></i>
                  <span class="caption-subject font-red bold uppercase"> Order List </span>
                </div>
              </div>
              <form method="POST" action="/addstoreOrder">
                @csrf
                <input type="hidden" name="dealer_code" value="{{$dealer_code}}">
                <input type="hidden" name="user_id" value="{{$user_id}}">
                <input type="hidden" name="areacode" value="{{$areacode}}">
                <div class="portlet-body">
                  <table class="table table-striped table-bordered table-hover" id="sample_21">
                    <thead>
                      <tr>
                        <th>Category Details </th>
                        <th> Product </th>
                        <th> Sub-Product</th>
                        <th> Variant </th>
                        <th> Price (Rs.) </th>
                        <th> Quantity </th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($products as $key=> $product)
                      <tr>
                        <td>{!! $product->product->category->name !!}</td>
                        <td>{!! $product->product['name'] !!}</td>
                        <td> {!! $product->subproduct['subproduct'] !!} </td>
                        <td>{!! $product->size['size'] !!}
                          @if ( $product->size['size'] == "CUSTOM")
                          <i style=" margin-left:20px;" class="fa fa-plus add-btn" product-id="{{$product->id}}" aria-hidden="true" data-append-id="appending_div_{{$key}}" data-key="{{$key}}"></i>
                          @endif

                          <div id="appending_div_{{$key}}">
                            <br>
                            <div class="row col-sm-12" style="width:250px;display:none;">x
                              <div class="col-sm-3" style="margin:0px; padding:0px">
                                <input type="text" class="form-control" name="length[]" placeholder="L">
                              </div> X
                              <div class="col-sm-3" style="margin:0px; padding:0px">
                                <input type="text" class="form-control" name="width[]" placeholder="W">
                              </div>
                            </div>
                          </div>
                        </td>


                        <td id="price_row{{$key}}" class="price" name="price[]">Rs. {!! number_format($product->price, 2, ".", ",") !!}</td>
                        <td>

                          <input type="hidden" name="product_id[]" value="{{$product->id}}">
                          <input type="hidden" id="product_price_{{$key}}" value="{{$product->price}}">
                          <div class="append-qty" data-qty-id="qty_div_{{$key}}">
                            <div id="qty_div_{{$key}}">

                              @if ( $product->size['size'] !== "CUSTOM")
                              @php
                              $var = 'visible';
                              @endphp
                              @else
                              @php
                              $var = 'hidden';
                              @endphp
                              @endif

                              <div class="row col-sm-12">
                                <div class="col-sm-4">
                                  <input type="text" style="visibility:{{$var}}" name="quantity[]" placeholder="Qty" class="form-control" data-qty-key="{{$key}}" data-row="0">
                                  <span class="field-validation-valid" data-valmsg-for="quantity" data-valmsg-replace="true"></span>
                                  @if ($errors->has('quantity'))
                                  <span class="help-block required">
                                    <strong>{{ $errors->first('quantity') }}</strong>
                                  </span>
                                  @endif
                                </div>
                                <div class="col-sm-8">
                                  <input type="text" name="totalprice[]" class="total form-control" readonly placeholder="Total Price" id="total_price_{{$key}}_0">
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>

                      @endforeach
                    </tbody>

                  </table>
                  <button type="submit" class="btn btn-primary" style="width: 200px;">
                    {{ __('Submit') }}
                  </button>



                </div>
              </form>
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
@section('scripts')
<script>
  // Add an event listener to the add button using jQuery
  $(document).ready(function() {
    // Create a new input field element
    var i = 1;
    $('.add-btn').on('click', function() {
      let clickedRowId = $(this).data('append-id');
      let clickedKey = $(this).data('key');
      let productid = $(this).attr('product-id');
      // $(this).hide(); // hide the add button after it is clicked once
      // console.log(clickedRowId);
      var field = `<br>
      <input type="hidden" name="product_id[]" value=${productid}>
      <div class = 'row col-sm-12' style="width:250px">
      <div class='col-sm-3' style="margin:0px; padding:0px">
      <input type="text" class="form-control" name="length[]" placeholder='L'>
      </div> X
      <div class='col-sm-3' style="margin:0px; padding:0px">
      <input type="text" class="form-control" name="width[]" placeholder='W'>
      </div> `;
      $(`#${clickedRowId}`).append(field);

      let qtyField = `<div class="row col-sm-12">
      <div class="col-sm-4">
      <input type="text" name="quantity[]" placeholder="Qty" class="form-control" data-qty-key="${clickedKey}" data-row="${i}">
      </div>
      <div class="col-sm-8">
      <input type="text" name="totalprice[]" class="total form-control" readonly placeholder="Total Price" id="total_price_${clickedKey}_${i}">
      </div>
      </div> `;
      $(`#qty_div_${clickedKey}`).append(qtyField);
      i++;
    });
  });


  $(document).on('input change', 'input[name="quantity[]"]', function() {
    let quantity = $(this).val();
    let clickedKey = $(this).data('qty-key');
    let addedRow = $(this).data('row');
    let price = $(`#product_price_${clickedKey}`).val();
    var total = quantity * price;
    $(`#total_price_${clickedKey}_${addedRow}`).val(total.toFixed(2));
    // $(this).closest('.row').find('.add-btn').show(); // show the add button when the field is removed
  });
</script>
@endsection
