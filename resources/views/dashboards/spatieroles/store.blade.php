@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Roles And Permission</h1>
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
        @include('dashboards.sessions.date')
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
                                @role('Super-Admin')
                                    <li><a href="/admin/create">Add New Admin </a></li>
                                    <li><a href="/admin">List Admin </a></li>
                                    <li class="active"><a href="/role-permission">Roles</a></li>
                                @endrole
                                <li><a href="/user/create">Add New User </a></li>
                                <li><a href="/user">List Users </a></li>

                                <!-- <li class="active"><a href =  "/dashboard/admin-register">Add New Admin </a></li>
                                            <li><a href="/dashboard/admins">List Admin </a></li>
                                            <li><a href="/dashboard/user-register">Add New User </a></li>
                                            <li><a class = "users">List Users </a></li>

                                            <script>
                                                $(document).ready(function() {
                                                    $(".user-class a").click(function() {
                                                        var holder = $(this).attr('class');
                                                        $("body").load("/dashboard/" + holder);
                                                    });
                                                });
                                            </script> -->

                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                @foreach ($errors->all() as $error)
                                    <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-7">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Roles</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                                        <thead>
                                            <tr>
                                                <th width="10%" style="text-align: center;"> SN </th>
                                                <th style="text-align: center;"> Name </th>
                                                <th style="text-align: center;"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                @if ($role->name !== 'Super-Admin')
                                                    <tr>
                                                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                                                        <td class="uppercase" style="text-align: center;">
                                                            {{ $role->name }}</td>
                                                        <td style="text-align: center;">
                                                            <a href="{{ route('role-permission.edit', $role->id) }}"
                                                                class='btn btn-outline btn-primary btn-xs'>Edit</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">User Role and
                                            permission</span>
                                    </div>

                                </div>
                                <form method="POST" action="{{ route('role-permission.store') }}">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="role"
                                            class="col-md-3 col-form-label text-right">{{ __('Role') }}</label>

                                        <div class="col-md-9">
                                            <input type="text" class="form-control" value="{{ old('role') }}"
                                                name="role" placeholder="Super-admin" autofocus>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row">
                                        <label for="user-permission"
                                            class="col-md-3 col-form-label text-right">{{ __('Permission') }}</label>

                                        <div class="col-md-9">
                                            <select name="user_permission[]" class="form-control text-uppercase"
                                                id="user_permission" style="height: 190px;" multiple required>
                                                @foreach ($permissions as $permission)
                                                    <option value="{{ $permission->id }}">{{ $permission->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="form-group row">
                                        <label for="user-permission"
                                            class="col-md-3 col-form-label text-right">Permission</label>
                                        <div class="col-md-9">
                                            <select name="user_permission[]" class="form-control text-uppercase"
                                                id="user_permission" style="height: 190px; color: #000;" multiple required>
                                                @foreach ($permissions as $permission)
                                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 col-md-offset-3">
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
    @endsection
