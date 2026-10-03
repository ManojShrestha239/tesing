@extends('dashboards.sessions.layout')
@section('content')

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Color Emporium</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35">

                            <li><a href="/dashboard/notice/emporium/create">Create Notice </a></li>
                                <li class="active"><a href="/dashboard/notice/emporium">Send Notices </a></li>

                                <!-- <li><a class= "send-notice">Create Notice </a></li>
                                <script>
                                    $(document).ready(function(){
                                        $(".send-notice").click(function(){
                                            $(".page-content").load("/dashboard/notice/send");
                                        });
                                    });
                                </script>
                                <li class="active"><a href="/dashboard/notice">Send Notices </a></li> -->

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
        <div class="col-md-12 notice-content">

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <form method="post" class="page-content-container" action="/dashboard/notice/search">
            @csrf
            <div class="row">

                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="control-label bolds">Dealer's User<span class="required">*</span></label>

                        <select class="form-control" name="user" id="user">
                            <option value="">Select Users</option>
                          @foreach($users as $user)
                            <option value="{{ $user->id }}">{{$user->full_name}}</option>
                           @endforeach
                        </select>
                        <span class="field-validation-valid" data-valmsg-for="resCountry" data-valmsg-replace="true"></span>
                    </div>
                </div>
                <div class="col-lg-5">
                <div class="form-group">
                        <label class="control-label bolds">Date<span class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                        <div class="date" data-provide="datepicker" class="form-control">
                            <input type="text" id="nepaliDateFa" autocomplete = "off" name="date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                        </div>
                        <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                        @if ($errors->has('date'))
                            <span class="help-block required">
                        <strong>{{ $errors->first('date') }}</strong>
                    </span>
                        @endif
                        @if(session('status'))
                        <span class="help-block required">
                        <strong>{{ session('status') }}</strong>
                        @endif
                    </div>
            </div>

                <div class="col-lg-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="margin-top:24px;width: 100%;">View</button>
                    </div>
                </div>
            </div>
        </form>
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
         
            <div class="portlet light portlet-fit">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-envelope font-red"></i>
                        <span class="caption-subject font-red bold uppercase">
                            @if(!empty($start))
                            
                            @if(empty($mr) && empty($from))
                            All Notices
                            @endif

                            @else

                            @if(empty($mr) && empty($from))
                            All General Notices
                            @elseif(!empty($mr) && empty($from))
                            All Notices of {{$mr}}
                            @elseif(empty($mr) && !empty($from))
                            All Notices list - ({{$from}})
                            @else
                            Notice list of {{$mr}} - ({{$from}})
                            @endif

                            @endif

                            </span></div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                        <tr>
                            {{(request()->segment(2))}}
                            <th> Notice No.</th>
                            <th> Send to </th>
                            <th> Title </th>
                            <th> Notice </th>
                            <th> Expiry Date </th>
                            <th> Actions </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (!empty($notices))

                        @foreach($notices as $notice)
                            <tr>
                                <td>{{$notice['id']}}</td>
                                <td>
                                @if ($notice['user_id']==null)

                                    All
                                    
                                @else
                                {{ $notice['user'] }}    
                                @endif
                                </td>
                                <td>{!! $notice['title'] !!}</td>
                              
                                <td>
                                <?php $str = $notice['notice'] ?>
                                    @if(strlen($notice['notice'])>250)
                                   <?php echo $trimstring = substr($str, 0, 250).'....';?>
                                   <a class = "order-view-popup" onclick = "document.getElementById('order-popup').style.display='block'" id="{{$notice['id']}}">
                                   <div class="btn btn-md" >read more</div>
                                </a>
                                   
                                    @else
                                    {!! $notice['notice'] !!}
                                    @endif

                                </td>
                               <td>
                                   {!! $notice['expiry_date'] !!}
                               </td>
                         
                                <td>

                                    <div class='btn-group'>
                                        <form action="/dashboard/notice/emporium/delete/{{$notice['id']}}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class='btn red btn-outline btn-xs'> Delete</button><br>
                                        </form>
                                    </div>
                                    <br>
                                    <div class="mt-1">
                                        @if($notice['publish'] == 0)
                                        <form action="/dashboard/noticePush/emporium/{{$notice['id']}}" method="post">
                                        @method('patch')
                                        @csrf
                                        @if($notice['expiry_date'] >=$notice['today'] )
                                        <button type="submit" class='btn btn-primary btn-outline btn-xs'> Publish</button><br>
                                        @endif
                                        </form>
                                        <a href="/dashboard/notice/emporium/{{ $notice['id'] }}/edit" class='btn green btn-outline btn-xs' style="margin-right:5px;">Edit</a>
                                        @endif
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
                    // alert(id);
                    $("#order-popup").load("/dashboard/notice/readmore/" +id);
                    $("#order-popup").css("display","block");
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