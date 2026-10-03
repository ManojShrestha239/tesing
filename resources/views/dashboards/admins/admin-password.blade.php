@extends('dashboards.blank')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Change Password</h1>
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
                                    <span class="caption-subject font-red bold uppercase">Change Password</span>
                                </div>

                            </div>

                            <form method="POST" action="/adminpass/{{$user->id}}">
                                {{ method_field('PATCH') }}
                                @csrf

                                @if(count($errors) > 0)
                                @foreach($errors->all() as $error)
                                <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                                @endif
                                @if($user->id==Auth::user()->id)
                                <div class="form-group row">
                                    <label for="old_password" class="col-md-4 col-form-label text-right">{{ __('Old Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="old_password" type="password" class="form-control" name="old_password" required autofocus>

                                    </div>
                                </div>
                                @endif

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-right">{{ __('New Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password" required autofocus>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-right">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autofocus>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary" style="width: 200px;">
                                            {{ __('Update') }}
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