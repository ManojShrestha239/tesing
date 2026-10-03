@extends('dashboards.blank')

@section('content')
<style>
.chkbox{
    height:18px !important;
    width:18px !important;
}
.chk-span {
    margin-top: 3px !important;
}

</style>

<link rel="stylesheet" href="{{ asset('css/subarea.css') }}">
<script src="{{asset('js/subarea.js')}}"></script>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Add new Area</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35 areas-class">

                            <li class="active"><a href="/dashboard/area">Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <!--<li><a href="/dashboard/area-users-assign">Assign Users to Area </a></li>-->
                                <!--<li><a href="/dashboard/area-users-assigned">Assigned Users</a></li>-->

                                <!-- <li class="active"><a>Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <li><a class = "users-assign">Assign Users to Area </a></li>
                                <script>
                                    $(document).ready(function(){
                                        $(".areas-class a").click(function(){
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
                                    <span class="caption-subject font-red bold uppercase">Add New Area</span>
                                </div>

                            </div>


    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/dashboard/subarea">
        @csrf

        <div class="form-group row">
            <label for="areacode" class="col-md-12 col-form-label">{{ __('Sub Area') }}</label>

            <div class="col-md-8">
                <input type="text" class="form-control" value="{{ old('areacode') }}" name="areacode" placeholder="area code" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="area" class="col-md-12 col-form-label">{{ __('Area') }}</label>

            <div class="col-md-8">
                <input type="text" class="form-control" value="{{ old('area') }}" name="area" placeholder="Area" required autofocus>
            </div>
        </div>
       
        
        <div class="form-group row">
        <label for="area" class="col-md-12 col-form-label">{{ __('Sub Area') }}</label>
        <div class="col-md-8" style="max-height:200px;overflow-y:auto;">
            @foreach($subareas as $sub)
            <div class="row" style="margin-left:0px;">
                <input type="checkbox" class="col-md-1 chkbox" value="{{$sub->id}}" name="subarea[]" autofocus>
                <span class="col-md-6 chk-span">{{$sub->subarea}}</span>
                </div>
            @endforeach
            </div>
        </div>
       

        <div class="form-group row">
            <div class="col-md-8">
                <button type="submit" class="btn btn-primary">
                    {{ __('Create') }}
                </button>
            </div>
        </div>
    </form> 
    <button class="btn add-new-btn" onclick="myfunction()">
    <i class="fas fa-plus-circle"></i>
    Add New Sub Areas
    </button>
    <form action="/dashboard/subarea" method="post">
        @csrf
        <div class="sub-area-container" id='sub-area-container'>

        </div>
        <div class="form-group row">
            <div class="col-md-8">
                <button type="submit" class="btn btn-primary">
                    {{ __('Add') }}
                </button>
            </div>
        </div>
    </form>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

@endsection
