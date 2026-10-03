@extends('dashboards.sessions.layout')

@section('content')
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
                                    <!-- Brand and toggle get grouped for better mobile display -->
                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <ul class="nav navbar-nav margin-bottom-35 dealer-class">
                                        
                                <li><a href="/dashboard/admins/register">Add New Admin </a></li>
                                <li><a  href="/dashboard/admins">List Admin </a></li>
                                <li><a href="/dashboard/user-register">Add New User </a></li>
                                <li><a href="/dashboard/users">List Users </a></li>
                                <li><a href="/dashboard/addMobileUser">Add Mobile User </a></li>
                                <li><a href="/dashboard/mobileUser">Mobile Users(Dealers) </a></li>
                                <li class="active"><a href="/dashboard/staffonDealer">Mobile Users(Staff on Dealers) </a></li>
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
                                                    <span class="caption-subject font-red bold uppercase">Users(Dealer Owner)</span>
                                                </div>
                                                
                                            </div>
 <style>

</style>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Full Name/Organization Name</th>
                            <th>Password</th>
                            <th>Address</th>
                            <th> Mobile Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($dealers))
                        @foreach($dealers as $dealer)
                        
                            <tr>
                                <td>{{ $loop->iteration}}</td>
                                <td>
                                    
                                User ID: <span style="font-weight:bold;"> {{ $dealer->user_code}}</span> <br>
                                Dealer Code: <span style=" font-weight:bold; "> {{$dealer->user_code}}</span> <br>
                                    {{ $dealer->full_name }} <br>
                                    {{$dealer->firm_name}}
                                </td>
                                <td><span style="font-weight: bold;">{{ $dealer->user_pass_o}}</span></td>                                <td>{{ $dealer->address }}</td>
                                <td>{{ $dealer->phone}}</td>
                                <td>
                                        
                                        <a href="/dashboard/mobileuser/resetstaff/{{ $dealer->id}}" class='btn btn-danger btn-xs' style="margin-right:5px;">
                                            Reset
                                        </a>
                                        <a id = "{{ $dealer->id}}" onclick = "document.getElementById('order-popup').style.display='block'" class='btn green btn-outline btn-xs order-view-popup' style="margin-right:5px;">Edit</a>
                                       <div>
                                        <a id = "{{ $dealer->id}}" onclick = "document.getElementById('order-popup').style.display='block'" 
                                        class='btn green btn-outline btn-xs password-view-popup' style="margin-right:5px; margin-top:3px">Change Password</a>
                                        </div>
                                        <form action="/dashboard/mobileuser/delete/{{$dealer->id}}" method="post">
                                        @csrf
                                        @method('PATCH')    
                                        <button type="submit" class="btn btn-danger btn-xs" > Delete </button>
                                        </form>
                                        {{--<a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-trash"></i></a>--}}
 
                                    
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
                        <div id = "order-popup" >
                        

                </div>
                <script>
                    $(document).ready(function(){
                        $(".order-view-popup").click(function(){
                        var id =  $(this).attr('id');
                        $("#order-popup").load("/dashboard/mobileuser/"+ id + "/edit");
                        $("#order-pop").css("display","block");
                        var modal = document.getElementById('order-popup');
                        window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                        }
                        });
                        $(".password-view-popup").click(function(){
            var id =  $(this).attr('id');
            $("#order-popup").load("/dashboard/userPassword/"+ id +"/edit");
            $("#order-pop").css("display","block");
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
