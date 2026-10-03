@extends('dashboards.sessions.layout')
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Create New Notice</h1>
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

                            <li class="active"><a href="/dashboard/notice/emporium/create">Create Notice </a></li>
                                <li><a href="/dashboard/notice/emporium">Send Notices </a></li>
                                <!-- <li class="active"><a>Create Notice </a></li>
                                <li><a href="/dashboard/notice">Send Notices </a></li> -->
                                
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
                                        <i class=" fa fa-envelope font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Create Notice</span>
                                    </div>

                                </div>

                            @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/dashboard/notice/emporium/create">
        @csrf

        <div class="form-group row">
            <label for="user" class="col-md-3 col-form-label text-right">{{ __('User') }}</label>
            <div class="col-md-6">
                <select name="user" class="form-control">
                    <option value="">All</option>
                    @foreach( $users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="title" class="col-md-3 col-form-label text-right">{{ __('Title') }}</label>
            <div class="col-md-6">
                <input type="text" name="title" @if(session('title'))value="{{session('title') }}"@endif class="form-control" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="user" class="col-md-3 col-form-label text-right">{{ __('Notice') }}</label>
            <div class="col-md-6">
                <textarea rows="10" class="col-md-12" placeholder="Enter Notice here..." name="notice">@if(session('notice')) {{session('notice') }} @endif </textarea>
            </div>
        </div>

        <div class="form-group row">
                        <label  for="expiry_date" class="col-md-3 col-form-label text-right">{{ __('Expiry Date') }}<span class="required has-feedback{{ $errors->has('expiry_date') ? ' has-error' : '' }}">*</span></label>
                        <div class="date" data-provide="datepicker" class="form-control">
                        <div class="col-md-6">
                            <input type="text" id="nepaliDateFa" autocomplete = "off" name="expiry_date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                        </div>
                        </div>
                        <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                        @if ($errors->has('expiry_date'))
                            <span class="help-block required">
                        <strong>{{ $errors->first('expiry_date') }}</strong>
                    </span>
                        @endif
                        @if(session('status'))
                        <span class="help-block required">
                        <strong>{{ session('status') }}</strong>
                        @endif
        </div>
        <div class="form-group row mb-0">
            <div class="col-md-6 col-md-offset-3">
                <button type="submit" class="btn btn-primary" style="width: 200px;">
                    {{ __('Create') }}
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

