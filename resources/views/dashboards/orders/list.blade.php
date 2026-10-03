<div class="page-content2 ">
    <img style="float:right;width:25px; height:25px;"
        onclick="document.getElementById('order-popup').style.display='none'"
        src={{ asset('assets/pages/img/quit.svg') }}>
    <!-- BEGIN BREADCRUMBS -->
    <div class="breadcrumbs">
        <h1>Items Details</h1>
        <ol class="breadcrumb">
            <li>
                <button class="btn btn-primary print" onclick="printDiv('printMe')"
                    style="margin-right: 30px;">Print</button>
                <script>
                    function printDiv(divName) {
                        var printContents = document.getElementById(divName).innerHTML;
                        var originalContents = document.body.innerHTML;

                        document.body.innerHTML = printContents;
                        $('tr').children().eq(6).hide();
                        $('table tr').find('td:eq(6)').hide();
                        window.print();

                        document.body.innerHTML = originalContents;

                    }
                </script>
            </li>
        </ol>
        <!-- Sidebar Toggle Button -->
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </span>
        </button>
    </div>
    <style>
        .printOnly {
            margin-left: 2%;
            margin-top: 1%;
            font-weight: 600;
        }

        .print-div {
            display: flex;
            margin-top: 5%;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div style="position:relative;">
                <div style="position:absolute;right:2%;top: 16px;" class="challan">
                    <form action="/order/challan/{{ $o->id }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-success ">Create Challan </button>
                    </form>
                </div>
            </div>
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light portlet-fit bordered" id="printMe">
                <div class="print-div">
                    <div class="printOnly">Order No. : {{ $order[0]['order_id'] }}</div>
                    <div class="printOnly">Order Date: {{ $order[0]['order_date'] }}</div>
                    <div class="printOnly"> Marketing Officer:{{ $mo }}</div>
                    <div class="printOnly"> Dealer : {{ $dealer }} @if (!empty($order))
                            ({{ $order[0]['dealer_code'] }})
                        @endif
                    </div>
                    <div class="printOnly"> Order Status : @if (!empty($order))
                            {{ $order[0]['order_status'] }}
                        @endif
                    </div>
                </div>
                @if (!empty($order))
                @endif
                @foreach ($order as $ordr)
                    <?php $amt = 0;
                    foreach ($order as $ordr) {
                        $amt = $amt + $ordr['quantity'] * $ordr['price'];
                    }
                    ?>
                @endforeach
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" fa fa-paint-brush font-red"></i>
                        <span class="caption-subject font-red bold uppercase">Orders </span> <span
                            style="font-size: 15px;font-weight: 600;">Rs. {{ $amt }}</span>
                    </div>

                </div>
                @php
                    $status = ['delivered', 'cancelled', 'declined'];
                    $statusMap = array_flip($status);
                    $canNotUpdate = isset($statusMap[strtolower($order[0]['order_status'])]);
                @endphp
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th> Category </th>
                                <th> Product </th>
                                <th> Variant </th>
                                <th> Quantity </th>
                                <th> Cost </th>
                                <th> Order Remarks </th>
                                @if (!$canNotUpdate)
                                    <th> Action </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($order))
                                @foreach ($order as $ordr)
                                    <tr data-price="{{ $ordr['price'] }}">
                                        <td> {{ $ordr['category_name'] }}</td>
                                        <td> {{ $ordr['product_name'] }}</td>
                                        <td> {{ $ordr['size'] }}</td>
                                        <td>
                                            @if (!$canNotUpdate)
                                                <form method="POST"
                                                    action="/challan/edit/{{ $ordr['orderitems_id'] }}"
                                                    id="ordr{{ $ordr['orderitems_id'] }}"
                                                    onsubmit="submitOrderEdit(event,`ordr{{ $ordr['orderitems_id'] }}`)">
                                                    {{ method_field('PATCH') }}
                                                    @csrf
                                                    <input type="number" min="0" name="quantity"
                                                        value="{{ $ordr['quantity'] }}" class="form-control" required>
                                                    <button type="submit"
                                                        class="btn btn-primary btn-sm btn-none">Update</button>
                                                </form>
                                            @else
                                                {{ $ordr['quantity'] }}
                                            @endif
                                        </td>
                                        <td>Rs. {{ $ordr['quantity'] * $ordr['price'] }}</td>
                                        <td>{{ $ordr['remarks'] }}</td>
                                        @if (!$canNotUpdate)
                                            <td>
                                                <form action="/order/availability/{{ $ordr['orderitems_id'] }}"
                                                    method="post" id="avail{{ $ordr['orderitems_id'] }}"
                                                    onsubmit="submitAvailability(event, 'avail{{ $ordr['orderitems_id'] }}')">
                                                    @method('PATCH')
                                                    @csrf
                                                    @if ($ordr['status'] == '0')
                                                        <button type="submit"
                                                            class="btn btn-success btn-sm ab ">Available</button>
                                                    @else
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm ab ">Unavailable</button>
                                                    @endif
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function submitOrderEdit(event, formId) {
        event.preventDefault();
        var $form = $('#' + formId);
        var url = $form.attr('action');
        var data = $form.serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                var $row = $form.closest('tr');
                var newQuantity = parseInt($form.find('input[name="quantity"]').val());
                var price = parseFloat($row.data('price'));
                var newCost = newQuantity * price;

                $row.find('td').eq(4).text('Rs. ' + newCost);

                $('.portlet-title span[style*="font-size"]').text('Rs. ' + response.order_total_price);

                alert('Quantity updated successfully!');
            },
            error: function(xhr) {
                alert('Error updating quantity.');
            }
        });
    }

    function submitAvailability(event, formId) {
        event.preventDefault();
        var $form = $('#' + formId);
        var url = $form.attr('action');
        var data = $form.serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function(response) {
                var $button = $form.find('button[type="submit"]');
                var isAvailable = (response.status === 'available') || (response.order_status == 0);
                if (isAvailable) {
                    $button.text('Available')
                        .removeClass('btn-danger')
                        .addClass('btn-success');
                } else {
                    $button.text('Unavailable')
                        .removeClass('btn-success')
                        .addClass('btn-danger');
                }
                alert('Availability updated successfully!')
            },
            error: function(xhr) {
                alert('Error updating availability.');
            }
        });
    }
</script>
