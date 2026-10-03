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
                            <ul class="nav navbar-nav margin-bottom-35">

                                <li><a href="/dashboard/area">Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <li><a href="/dashboard/area-users-assign">Assign Users to Area </a></li>
                                <li class="active"><a href="/dashboard/area-users-assigned">Assigned Users</a></li>


                            </ul>

                        </nav>
                    </div>

                </div>

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
                        <span class="caption-subject font-red bold uppercase"> Area Assigned Users </span></div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                        <thead>
                        <tr>
                            <th> User </th>
                            <th> Area </th>
                            <th> Actions </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($user as $users)
                            <tr>
                                <td>{!! $users->mobileuser['name'] !!}</td>
                                <td>{!! $users->area['area'] !!}</td>
                                <td>

                                    <div class='btn-group'>
                                        <a href="/dashboard/user-reassign/{{ $users->id }}" class='btn green btn-outline btn-xs'>Edit</a>
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
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
            </div>
        </div>
    </div>
@endsection
