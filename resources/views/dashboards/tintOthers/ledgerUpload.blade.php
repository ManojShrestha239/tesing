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
                                <ul class="nav navbar-nav margin-bottom-35 bank-class">
                                <li class="active"><a href="/dashboard/ledger">Upload Ledger </a></li>
                                <li><a href="/dashboard/scheme">Add New Scheme </a></li>
                                <li><a href="/dashboard/schemes">View All Schemes </a></li>
                                <li><a href="/dashboard/advertise/add">Add New Avertisement </a></li>
                                <li><a href="/dashboard/advertise">View All Avertisements </a></li>
                                
                                <!-- <li><a href="/dashboard/distance">Set Distance</a></li> -->

                                    <!-- <li><a class = "create-bank">Add New Bank </a></li>
                                    <li><a class = "banks" href="/dashboard/banks">List Banks </a></li>
                                    <li class="active"><a>Upload Due </a></li>
                                    <li><a class = "distance">Set Distance</a></li> -->

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
                                        <span class="caption-subject font-red bold uppercase">Upload Ledger</span>
                                    </div>

                                </div>  

                                @if(count($errors) > 0)
                                    @foreach($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

    <form action="/dashboard/uploadFile" method="post" enctype="multipart/form-data">
    {{ method_field('PATCH') }}
    @csrf

        <div class="form-group row">
            <label for="dealer" class="col-md-3 col-form-label text-right">{{ __('Dealers') }}</label>
            <div class="col-md-4">
                <select name="dealer" class="form-control">
                    @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}">({{$dealer->user_code}})&nbsp{{ $dealer->firm_name  }}- {{ $dealer->full_name}} </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="fileupload" class="col-md-3 col-form-label text-right">Pdf Ledger file</label>
            <div class="col-md-4">
            <input class="form-control" type="file" name="file">
            </div>
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