
    <div class="page-content2 ">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>Items Details</h1>
            <ol class="breadcrumb">
                <li>
                    <button class="btn btn-primary print" onclick="printDiv('printMe')" style="margin-right: 30px;">Print</button>
                    <script>
		function printDiv(divName){
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;
                        // $('table tr:first').hide();
			document.body.innerHTML = printContents;
            $('tr').children().eq(1).hide();
            $('tr').children().eq(2).hide();
            $('tr').children().eq(9).hide();
            $('tr').children().eq(10).hide();
            $('table tr').find('td:eq(1)').hide();
            $('table tr').find('td:eq(2)').hide();
            $('table tr').find('td:eq(9)').hide();
            $('table tr').find('td:eq(10)').hide();

			window.print()

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
            <!-- Sidebar Toggle Button -->
        </div>
        <!-- END BREADCRUMBS -->
        <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
<style>
    .printOnly{
        display:none;
    }
</style>
        <div class="row">
            <div class="col-md-12">
            <span style="position:absolute;right:5%;top:2%;" class="challan">
            <form action="/order/challan/{{$o->id}}" method="post">
            @csrf
            <button type="submit" class="btn btn-success " >Create Challan </button>
            </form></span>
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" fa fa-paint-brush font-red"></i>
                            <span class="caption-subject font-red bold uppercase">All Orders of Dealer {{ $dealer }} From {{$mo}}</span>

                        </div>
                    </div>
                    <div class="portlet-body" id= "printMe" >
                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                        @if (!empty($order))
                    <p class="printOnly"><?php echo date('Y-m-d'); ?></p>
                    <p class="printOnly"> Marketing Officer:{{ $mo}}</p>
                    <p class="printOnly"> Delaer : {{ $dealer }} </p>
                    <p class="printOnly">All Orders of Dealer {{ $dealer }} From {{$mo}}</p>

                    @endif
                            <thead>
                            <tr>
                                <th> Order No.</th>
                                <th> Dealer </th>
                                <th> User </th>
                                <th> Date </th>
                                <th> Category </th>
                                <th> Product </th>
                                <th> Variant </th>
                                <th> Quantity </th>
                                <th> Cost </th>
                                <th class="hideMe"> Order Status </th>
                                <th> Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($order))

                                @foreach($order as $ordr)
                                    <tr>
                                        <td> {{$ordr['order_id']}}</td>
                                        <td> {{ $ordr['dealer_name'] }} </td>
                                        <td> {{ $ordr['user_name'] }}</td>
                                        <td> {{ $ordr['order_date'] }}</td>
                                        <td> {{ $ordr['category_name'] }}</td>
                                        <td> {{ $ordr['product_name'] }}</td>

                                        <td> {{ $ordr['size'] }}</td>
                                        <td> {{ $ordr['quantity'] }}</td>
                                        <td>Rs. {{ ($ordr['quantity'])*($ordr['price']) }}</td>

                                        <td>
                                            @if ($ordr['order_status'] == 'pending')
                                                <button class="btn btn-warning btn-sm ab">{{ $ordr['order_status'] }}</button>
                                            @elseif ($ordr['order_status'] == 'cancelled')
                                                <button class="btn btn-danger btn-sm ab">{{ $ordr['order_status'] }}</button>
                                            @elseif($ordr['order_status'] == 'ongoing')
                                                <button class="btn btn-info btn-sm ab">{{ $ordr['order_status'] }}</button>
                                            @else
                                                <button class="btn btn-success btn-sm ab hideMe" >{{ $ordr['order_status'] }}</button>
                                            @endif

                                        </td>
                                        <td>
                                        @if($ordr['status']=='0')
                                        <a href="/challan/edit/{{$ordr['sizeid']}}" class="btn btn-primary btn-sm ab" style="margin-bottom:2px;">Edit</a>
                                        @endif
                                        <form action="/order/availability/{{$ordr['sizeid']}}" method="post">
                                        @method('PATCH')
                                        @csrf
                                        @if($ordr['status']=='0')
                                            <button type="submit" class="btn btn-success btn-sm ab ">Available</button>
                                            @else
                                            <button type="submit" class="btn btn-danger btn-sm ab ">Unavailable</button>
                                            @endif
                                        </form>
                                        </td>
                                    </tr>
                                @endforeach

                            @endif

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
