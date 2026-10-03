@extends('dashboards.sessions.layout')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Order</h1>

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
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-user font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Order Images</span>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Image preview</th>
                                            <th>Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($orerimages)>0)
                                        @foreach($orerimages as $orerimage)
                                        <tr>
                                            <td>
                                                <div style="height:100px;"> <img height="95px" src="{{$orerimage->url.'/'.$orerimage->image}}" alt="" srcset=""> </div>
                                            </td>
                                            <td>{{$orerimage->image}}</td>
                                            <td>
                                                <a href="{{$orerimage->url.'/'.$orerimage->image}}" target="_blank" class='btn green btn-outline btn-xs order-view-popup'>view</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        No Data Available.
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection