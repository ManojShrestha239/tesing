@extends('dashboards.blank')

@section('content')

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>File Upload</h1>
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

                            <li><a href="/dashboard/create-bank">Add New Bank </a></li>
                                <li><a href="/dashboard/banks">List Banks </a></li>
                                <li><a href="/dashboard/uploadfile">Upload Due </a></li>
                                <li class="active"><a href="/dashboard/uploadstatement">Upload Statement </a></li>
                                <li><a href="/dashboard/distance">Set Distance</a></li>

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
                                        <i class="fa fa-upload font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Upload Statement</span>
                                    </div>

                                </div>

                                @if(count($errors) > 0)
                                    @foreach($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form action="/dashboard/uploadfile" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="fileupload" class="col-md-4 col-form-label text-right">Select the Excel file to upload</label>
                                        <input class="form-control-file" type="file" name="excel_file">
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Upload') }}
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