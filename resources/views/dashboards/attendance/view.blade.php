@extends('dashboards.sessions.layout')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Attendance</h1>
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
                        <ul class="nav navbar-nav margin-bottom-35 dealer-class">

                            <li><a href="/visithistory">Daily Visit </a></li>
                            <li><a href="/areavisit">Area Visit </a></li>
                            <li><a href="/dealervisit">Dealer Visit </a></li>
                            <li><a href="/negotiation">Unlisted Dealer Visit </a></li>
                            <li class="active"><a href="/attendance">Attendance List </a></li>



                            <!-- <li><a class = "dealer-create">Add New Dealer </a></li>
                                        <li class="active"><a  href="/dealers">List Dealer </a></li>
                                        
                                        <script>
                                            $(document).ready(function(){
                                                $(".dealer-class a").click(function(){
                                                var holder = $(this).attr('class');
                                                $("body").load("/"+ holder);
                                            });
                                            });
                                        </script> -->

                        </ul>

                    </nav>
                </div>

            </div>
            <div class="page-content-col">
                @if(session('Status'))

                <div class="alert alert-success alert-dismissible" role="alert">
                    <b>{{ session('Status') }}</b>
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
                                    <span class="caption-subject font-red bold uppercase">Attendance List - {{$mo->name}}</span>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>

                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>View</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($lists))
                                        @foreach($lists as $list)

                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$list->date}}</td>
                                            <td>{{$list->checkin_date}}</td>
                                            <td>{{$list->status}}</td>
                                            <td>
                                                <a class="attendance-view-popup" id="{{ $list->id }}">
                                                    <div class="btn btn-primary btn-sm" onclick="document.getElementById('order-popup').style.display='block'">Details</div>
                                                </a>
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
                                $(".attendance-view-popup").click(function() {
                                    var id = $(this).attr('id');
                                    $("#order-popup").load("/attendance/view/" + id);
                                    $("#order-popup").css("display", "block");
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