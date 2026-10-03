@extends('dashboards.sessions.layout')

@section('content')
  <div class="page-content">
      <div class="breadcrumbs">
                        <h1>All Schemes/Offers</h1>
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
                                        
                                    <li><a href="/dashboard/ledger">Upload Ledger </a></li>
                                <li><a href="/dashboard/scheme">Add New Scheme </a></li>
                                <li><a href="/dashboard/schemes">View All Schemes </a></li>
                                <li><a href="/dashboard/advertise/add">Add New Avertisement </a></li>
                                <li  class="active"><a href="/dashboard/advertise">View All Avertisements </a></li>

                                        <!-- <li><a class = "dealer-create">Add New Dealer </a></li>
                                        <li class="active"><a  href="/dashboard/dealers">List Dealer </a></li>
                                        
                                        <script>
                                            $(document).ready(function(){
                                                $(".dealer-class a").click(function(){
                                                var holder = $(this).attr('class');
                                                $("body").load("/dashboard/"+ holder);
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
                                                    <span class="caption-subject font-red bold uppercase">Schemes List</span>
                                                </div>
                                                
                                            </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Scheme Title</th>
                            <th>Body Message</th>
                            <th>Scheme Start Date</th>
                            <th>Scheme End on</th>
                            <th>Updated on</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($schemes))
                        @foreach($schemes as $scheme)
                        
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$scheme->title}}</td>
                                <td>{{$scheme->content}}</td>
                                <td>{{$scheme->starting_from}}</td>
                                <td>{{$scheme->ending_on}}</td>
                                <td>{{$scheme->updated_at}}</td>
                                <td>

                                    <div class='btn'>
                                        @if($scheme->publish == '0')
                                        <a id = "{{ $scheme->id }}" onclick = "document.getElementById('order-popup').style.display='block'" class='btn green btn-outline btn-xs order-view-popup'>Edit</a>
                                        <?php $date =  date('Y-m-d') ?>
                                         @if($scheme->ending_on >= $date)
                                        <form action="/dashboard/scheme/publish/{{$scheme->id}}" method="post">
                                        @method('patch')
                                        @csrf
                                        <button type="submit" class='btn btn-primary btn-outline btn-xs' style="margin-top: 1px;"> Publish</button>
                                        </form>
                                        @endif
                                        @endif
                                        
                                        <form action="/dashboard/scheme/delete/{{$scheme->id}}" method="post">
                                        @method('PATCH')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                        </form>
                        
                                    </div>
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
                        $("#order-popup").load("/dashboard/scheme/"+ id + "/edit");
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
