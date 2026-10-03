

    <div class="page-content2">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <div class="breadcrumbs">
            <h1>Sub Area List</h1>
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

                            @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif


    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-map font-red"></i>
                        <span class="caption-subject font-red bold uppercase"> Sub Area List</span> </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                        <tr>
                            <th> S/N </th>
                            <th> Name </th>
                            <th> Actions </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($subareas as $area)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{!! $area->subarea !!}</td>
                                <td>

                                    <div class='btn-group'>
                                        <a href="/subarea/{{$area->id}}/edit" class="btn green btn-outline btn-xs">Edit</a>
                                        <!-- <a id = "{{ $area->areacode }}" onclick = "document.getElementById('order-popup').style.display='block'" class='btn green btn-outline btn-xs order-view-popup'>Edit</a> -->
                                        {{--<a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-trash"></i></a>--}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach

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
                        $("#order-popup").load("/dashboard/area/"+ id + "/edit");
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
    </div>
            </div>
        </div>
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
