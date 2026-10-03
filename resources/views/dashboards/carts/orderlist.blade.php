<!-- BEGIN EXAMPLE TABLE PORTLET-->

<style>
  .link-danger {
    color: #ff3e1d;
    font-size: 15px;
  }

  .link-danger:hover,
  .link-danger:focus {
    color: #e6381a;
  }

  .link {
    font-size: 15px;
  }

  .me-1 {
    margin-right: 0.25rem !important;

  }
</style>

<div class="portlet-body">
  <table class="table table-striped table-bordered table-hover" id="sample_21">
    <thead>
      <tr>
        <th>S.N </th>
        {{-- <th>Dealer</th>
        <th>Mobile User</th> --}}
        <th>Product list </th>
        <th class="size-th">Size</th>
        <th> Price (Rs.) </th>
        <th class="qty-th"> Quantity </th>
        <th>Total Price</th>
        <th class="action-th">Action</th>

      </tr>
    </thead>
    <tbody class="tbody" id="cart_tbody">
      @foreach($carts as $key => $cart)
      <?php
          $cart->productSize->size->size == 'CUSTOM' ? $isCustom = true : $isCustom = false;
          ?>
      <tr>
        <td> {{ ++$key}} </td>
        {{-- <td>{{ $cart->dealer_code}}</td>
        <td>{{ $cart->mobileuser->name }}</td> --}}
        <td>{{ $cart->productSize->product->category->name }}-{{ $cart->productSize->product->name }}
          -{{ $cart->productSize->subproduct->subproduct }} @if($isCustom) CUSTOM @endif</td>
        <td class="size-td">
          <div class="row">
            <input type="text" name="cus_length[]" class="order-size cus-length form-control"
              value="{{ $cart->custom_length}}" @if(!$isCustom) readonly @endif> <span class="order-cross-mark">x</span>
            <input type="text" name="cus_width[]" class="order-size cus-width form-control"
              value="{{ $cart->custom_width }}" @if(!$isCustom) readonly @endif>
          </div>
        </td>
        <td class="price_row">{{ $cart->productSize->price }}</td>
        <td class="qty-td">
          <input type="number" name="cus_quantity[]" class="order-qty form-control" value="{{ $cart->quantity }}"
            min="1" data-oldvalue="{{$cart->quantity}}">

        </td>

        <td class="total-price">{{ number_format(($cart->productSize->price) * ($cart->quantity), 2) }}</td>
        <td class='action-td'>
          <a class="edit-save" style="display: block;" data-product-id="{{ $cart->id }}"><i
              class="fa fa-pencil-square-o me-1" aria-hidden="true"></i>Update</a>

          <a class="link-danger delete" data-deletecart-id="{{ $cart->id }}">
            <i class="fa fa-trash me-1" aria-hidden="true">Delete</i></a>

        </td>
      </tr>
      @endforeach
    </tbody>

  </table>
</div>



<!-- END EXAMPLE TABLE PORTLET-->