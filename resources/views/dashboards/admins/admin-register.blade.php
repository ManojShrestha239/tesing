@extends('dashboards.blank')

@section('content')
    <style>
        .areashow {
            display: block;
        }

        .areahidden {
            display: none;
        }

        .adminarea-overflow {
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        #myInput {
            position: sticky;
            top: 0;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Register New Admin</h1>
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
                                    <li class="active"><a href="/admin/create">Add New Admin </a></li>
                                    <li><a href="/admin">List Admin </a></li>
                                    <li><a href="/role-permission">Roles</a></li>
                                @endrole
                                @can('user')
                                    <li><a href="/user/create">Add New User </a></li>
                                    <li><a href="/user">List Users </a></li>
                                @endcan
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
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New Admin</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/admin">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Full Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                name="name" placeholder="Full Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('E-Mail Address') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="E-mail Address" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('phone') }}"
                                                name="phone" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password"
                                            class="col-md-3 col-form-label text-right">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control" name="password"
                                                placeholder="password" required autofocus>

                                            {{-- @if ($errors->has('password')) --}}
                                            {{-- <span class="invalid-feedback" role="alert"> --}}
                                            {{-- <strong>{{ $errors->first('password') }}</strong> --}}
                                            {{-- </span> --}}
                                            {{-- @endif --}}
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password-confirm"
                                            class="col-md-3 col-form-label text-right">{{ __('Confirm Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password_confirmation" type="password" class="form-control"
                                                name="password_confirmation" placeholder="Confirm Password" required
                                                autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="user-type"
                                            class="col-md-3 col-form-label text-right">{{ __('Admin User Type') }}</label>

                                        <div class="col-md-6">
                                            <select name="user_type" class="form-control selectpicker"
                                                data-live-search="true" id="admin_role" onchange="areaShow()" required>
                                                <option value="">Select Admin User</option>
                                                @foreach ($roles as $role)
                                                    <option @if (old('user_type')) selected @endif
                                                        value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row adminarea areahidden">
                                        <label for=""
                                            class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" id="myInput" onkeyup="searchFunction()"
                                                placeholder="search here..." class="form-control">
                                            <div id="myDiv" class="adminarea-overflow">
                                                @foreach ($areas as $area)
                                                    <div id="searcharea-{{ $area->areacode }}">
                                                        <input id="area-{{ $area->areacode }}" type="checkbox"
                                                            value="{{ $area->areacode }}" name="area[]" autofocus>
                                                        <label
                                                            for="area-{{ $area->areacode }}">{{ $area->area }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
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
    <script>
        function areaShow() {
            let role = document.getElementById('admin_role');
            var roleText = role.options[role.selectedIndex].text;
            if (roleText == "ASM" || roleText == "ASM Admin" || roleText == "ASM" || roleText == "ASM" || roleText ==
                "ASM") {
                let area = document.querySelector('.adminarea');
                area.classList.remove('areahidden')
                area.classList.add('areashow')
            } else {
                let area = document.querySelector('.adminarea');
                area.classList.add('areahidden')
                area.classList.remove('areashow')
            }
            // console.log(role.value);
        }

        function searchFunction() {
            var input, filter, ul, divlist, label, i, labelValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            mydiv = document.getElementById("myDiv");
            divlist = mydiv.getElementsByTagName("div");
            for (i = 0; i < divlist.length; i++) {
                label = divlist[i].getElementsByTagName("label")[0];
                labelValue = label.innerText;
                if (labelValue.toUpperCase().indexOf(filter) > -1) {
                    label.parentElement.style.display = "";
                } else {
                    label.parentElement.style.display = "none";
                }
            }
        }
    </script>
@endsection
