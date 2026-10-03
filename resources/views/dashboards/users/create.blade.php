@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Assign Users to Area</h1>
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
                                <i class=" fa fa-user font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <ul class="nav navbar-nav margin-bottom-35 areas-class">


                                <li><a href="/dashboard/area">Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <li class="active"><a href="/dashboard/users-assign">Assign Users to Area </a></li>
                                <li><a href="/dashboard/area-users-assigned">Assigned Users</a></li>



                                <!-- <li><a class = "area">Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <li class="active"><a>Assign Users to Area </a></li>
                                
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
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Assign Users to Area</span>
                                    </div>

                                </div>

    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="{{ route('users.assign') }}">
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-right">{{ __('User Name :') }}</label>

            <div class="col-md-6">
                <select name="user_name" class="form-control" required>
                    <option value="">Select User</option>
                    @foreach( $user as $users)
                        <option value="{{ $users->id }}">{{ $users->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-4 col-form-label text-right">{{ __('Areas :') }}</label>
            <div class="col-md-6">
                <select name="user_area" class="form-control" required>
                    <option value="">Select area</option>
                    @foreach( $area as $areas)
                        <option value="{{ $areas->areacode }}">{{ $areas->area }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-8 col-md-offset-3">
                <button type="submit" class="btn btn-primary" style="width: 200px;">
                    {{ __('Assign') }}
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
