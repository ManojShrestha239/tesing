@extends('dashboards.sessions.layout')

@section('content')
    <style>
        .dealer-info {
            font-weight: bold;
            line-height: 1.7;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Dealers</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35 dealer-class">
                                <li><a href="/dealer/create">Add New Dealer </a></li>
                                <li class="active"><a href="/dealer">List Dealer </a></li>
                                <li><a href="/dealers-map">Dealers Map </a></li>

                            </ul>

                        </nav>
                    </div>

                </div>
                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Dealer List</span>
                                    </div>
                                    <a href="/dealerExport" class="btn btn-outline btn-xs btn-primary"
                                        style="float:right"><img
                                            src="https://img.icons8.com/windows/32/ffffff/download--v1.png" height="20px"
                                            width="20px" />Excel</a>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Dealer Info.</th>
                                                <th>Area</th>
                                                <th>Sub Area</th>
                                                <th>Order Limit</th>
                                                <th>Position Status</th>
                                                <th>Active Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($dealers))
                                                @foreach ($dealers as $dealer)
                                                    <tr>
                                                        <td>{!! $loop->iteration !!}</td>
                                                        <td>
                                                            @if ($dealer->mainDealer)
                                                                <span class="dealer-info">Main
                                                                    Dealer:</span>{{ $dealer->mainDealer->name }}
                                                                ({{ $dealer->mainDealer->code }})
                                                                <br>
                                                            @endif
                                                            <span
                                                                class="dealer-info">Code:</span>{!! $dealer->code !!}<br>
                                                            <span
                                                                class="dealer-info">Name:</span>{!! $dealer->name !!}<br>
                                                            <span
                                                                class="dealer-info">Address:</span>{!! $dealer->address !!}<br>
                                                            <span class="dealer-info">Email:</span>
                                                            {!! $dealer->email !!}<br>
                                                            <span
                                                                class="dealer-info">Contact1:</span>{!! $dealer->mobile !!}
                                                            <span
                                                                class="dealer-info">Contact2:</span>{{ $dealer->phone }}<br>
                                                        </td>
                                                        <td>
                                                            @if (isset($dealer->area['area']))
                                                                {!! $dealer->area['area'] !!}
                                                            @endif
                                                        </td>
                                                        <td> {!! $dealer->subarea !!} </td>
                                                        <td>{!! $dealer->order_limit !!}</td>
                                                        <td>{{ $dealer->status }}</td>
                                                        <td>
                                                            @if (isset($dealer->area['area']))
                                                                @if ($dealer->area['status'] == 'Published')
                                                                    {!! $dealer->active_status !!}
                                                                @else
                                                                    Inactive
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($dealer->area['area']))
                                                                @if ($dealer->area['status'] == 'Published')
                                                                    <a id="{{ $dealer->code }}"
                                                                        onclick="document.getElementById('order-popup').style.display='block'"
                                                                        class='btn green btn-outline btn-xs order-view-popup'>Edit</a>
                                                                    @if ($dealer->active_status == 'Active')
                                                                        <a href="" class='btn btn-danger btn-xs'
                                                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to disable this dealer?')){ document.getElementById('dealer{{ $dealer->code }}').submit(); }">Disable</a>
                                                                    @else
                                                                        <a href="" class='btn btn-primary btn-xs'
                                                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to enable this dealer?')){ document.getElementById('dealer{{ $dealer->code }}').submit(); }">Enable</a>
                                                                    @endif
                                                                    <form action="/dealer/{{ $dealer->code }}"
                                                                        method="post" id="dealer{{ $dealer->code }}">
                                                                        {{ method_field('DELETE') }}
                                                                        @csrf
                                                                    </form>
                                                                @else
                                                                @endif
                                                            @endif


                                                        </td>


                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                            {{-- pop up after clicking view  --}}
                            <div id="order-popup">


                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".order-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        $("#order-popup").load("/dealer/" + id + "/edit");
                                        $("#order-pop").css("display", "block");
                                        var modal = document.getElementById('order-popup');
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
