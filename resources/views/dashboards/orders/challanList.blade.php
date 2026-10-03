
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Challan</h1>


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

                                <li><a  class = "pending" href="/orders/pending">Pending </a></li>
                                <li><a  class = "ongoing" href="/orders/ongoing">Ongoing </a></li>
                                <li><a class = "completed" href="/orders/completed">Completed </a></li>
                                <li><a class = "cancelled" href="/orders/cancelled">Cancelled </a></li>
                                <li class="active"><a href="/order/challan">Challan </a></li>

                            <!-- <li><a class = "pending">Pending </a></li>
                                <li><a class = "ongoing" >Ongoing </a></li>
                                <li><a class = "completed">Completed </a></li>
                                <li><a class = "cancelled" >Cancelled </a></li> -->

                            </ul>

                        </nav>
                    </div>

                </div>



                <div class="page-content-col">
                    @if( session('Status'))
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
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
{{--                                <div class="portlet-title">--}}
{{--                                    <div class="caption">--}}
{{--                                        <i class=" fa fa-paint-brush font-red"></i>--}}
{{--                                        <span class="caption-subject font-red bold uppercase">All Orders</span>--}}
{{--                                    </div>--}}

{{--                                </div>--}}


        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" fa fa-paint-brush font-red"></i>
                            <span class="caption-subject font-red bold uppercase">
                           Challans
                            </span>
                        </div>


                        <style>
                .link{
                        position:absolute;
                        bottom: -3px ;
                        right:36px ;
                }

    .printOnly{
        display:none;
    }
</style>

        </style>
                    </div>
                    <div class="portlet-body" id="printMe" >
                        <table class="table table-striped table-bordered table-hover" id="sample_2"  >
                            <thead>
                            <tr>
                                <th> Group No.</th>
                                <!-- <th> Order id </th> -->
                                <th> Dealer </th>
                                <th> User </th>
                                <th> View </th>
                                <!-- <th>test</th> -->

                            </tr>
                            </thead>
                            <tbody>

                            @if (!empty($challans))

                                @foreach($challans as $challan)

                                    <tr>
                                        <td> {{$challan->group_id}} </td>
                                        <td > {{$challan->dealerid}}-{{ $challan->dealer_name }} </td>
                                        <!-- <td> {{ $challan->dealer_name }}</td> -->
                                        <td> {{ $challan->user_name }}</td>
                                        <td><a href="/order/challan/{{$challan->group_id}}" class="btn btn-primary btn-sm">Challan</a> </td>
                                    </tr>

                                @endforeach

                            @endif

                            </tbody>

                        </table>

                    </div>

</div>
                    {{-- pop up after clicking view  --}}
                    <div id = "order-popup" >



                    </div>
                    </div>


                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .form-popup{
            display:none;
        }
    </style>
    <div class="form-popup" id= "myForm" style="height:220px;width:200px;border:solid;" >
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                            <span class="caption-subject font-red bold uppercase">Change Order Status</span>
                                </div>

    <form method="POST" action="/status">
        @csrf

        <div class="form-group">
            <label for="name" class="col-md-12 col-form-label ">{{ __('Status') }}</label>

            <div class="col-md-12">
                <select name="" id="" class="form-control">
                <option value="">pending</option>
                <option value="">ongoing</option>
                <option value="">Cancelled</option>
                <option value="">completed</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-md-12 col-form-label ">{{ __('Remarks') }}</label>
            <div class="col-md-12">
                    <input type="text" class="form-control">
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 col-md-offset-1">
                <button type="submit" class="btn btn-primary" style="margin-top:5px" >
                    {{ __('Submit') }}
                </button>
            </div>
        </div>
    </form>

                            </div>
                        </div>
                    </div>
                    <script>
function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}
</script>

                    @endsection
