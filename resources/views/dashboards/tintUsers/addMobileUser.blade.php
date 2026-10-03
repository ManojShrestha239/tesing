@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Register New User</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35 user-class">

                            
                                <li><a href="/dashboard/admins/register">Add New Admin </a></li>
                                <li><a  href="/dashboard/admins">List Admin </a></li>
                                <li><a href="/dashboard/user-register">Add New User </a></li>
                                <li><a href="/dashboard/users">List Users </a></li>
                                <li class="active" ><a href="/dashboard/addMobileUser">Add Mobile User </a></li>
                                <li><a href="/dashboard/mobileUser">Mobile Users(Dealers) </a></li>
                                <li><a href="/dashboard/users">Mobile Users(Staff on Dealers) </a></li>

                                <!-- <li><a href =  "/dashboard/admin-register">Add New Admin </a></li>
                                <li><a href="/dashboard/admins">List Admin </a></li>
                                <li class="active"><a href="/dashboard/user-register">Add New User </a></li>
                                <li><a class="users">List Users </a></li>
                              
                              <script>
                                    $(document).ready(function(){
                                        $(".user-class a").click(function(){
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
                                        <span class="caption-subject font-red bold uppercase">Add New User</span>
                                    </div>

                                </div>

        @if(count($errors) > 0)
            @foreach($errors->all() as $error)
                <p class="alert alert-danger">{{ $error }}</p>
            @endforeach
        @endif

        <form method="POST" action="/dashboard/addMobileUser">
            @csrf

            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('First Name') }}</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" value="{{ old('fname') }}" name="fname" placeholder="First Name" required autofocus>
                </div>
            </div>
            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Last Name') }}</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" value="{{ old('lname') }}" name="lname" placeholder=" Last Name" required autofocus>
                </div>
            </div>
            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Organization/Dealer Name') }}</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" value="{{ old('dealer') }}" name="dealer" required autofocus>
                </div>
            </div>
            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Organization/Dealer code') }}</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" value="{{ old('code') }}" name="code" required autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Login User Id Code') }}</label>

                <div class="col-md-6">
                    <input id="text" type="text" class="form-control" name="user_code" value="{{ old('user_code') }}" required autofocus>

                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Complete Address') }}</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" value="{{ old('address') }}" name="address" required autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="phone" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Phone Number') }}</label>

                <div class="col-md-6">
                    <input id="phone" type="number" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="phone" required autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Password') }}</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control" name="password" placeholder="password" required autofocus>

                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('Confirm Password') }}</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="role_id" class="col-md-3 col-form-label " style="margin-left:10px;">{{ __('User Role') }}</label>
                <div class="col-md-6">
                    <select name="role" class="form-control">
                    
                        <option value="">Select User Role</option>
                        <option value="1">Dealer</option>
                        <option value="2">Staff on Dealer</option>
                        
                    </select>
                </div>
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
