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

    <div class="page-content1">
        <div class="breadcrumbs">
            <h1>Edit Admin</h1>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
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
                                        <span class="caption-subject font-red bold uppercase">Edit Admin Details</span>
                                    </div>

                                </div>

                                <form method="POST" action="{{ route('admin.update', $user->id) }}">
                                    @method('PATCH')
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text"
                                                class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                name="name" value="{{ $user->name }}" required autofocus>

                                            @if ($errors->has('name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('E-Mail Address') }}</label>

                                        <div class="col-md-6">
                                            <input type="email"
                                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                name="email" value="{{ $user->email }}" required autofocus>

                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ $user->phone }}"
                                                name="phone" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>
                                    @if (auth()->user()->id != $user->id)
                                        <div class="form-group row">
                                            <label for="user-type"
                                                class="col-md-3 col-form-label text-right">{{ __('Admin User Type') }}</label>
                                            <div class="col-md-6">
                                                <select name="user_type" class="form-control selectpicker"
                                                    data-live-search="true" id="admin_role" onchange="areaShow()" required>
                                                    <option value="">Select Admin User</option>
                                                    @foreach ($roles as $role)
                                                        <option @if ($role->name === $user->user_type) selected @endif
                                                            value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row adminarea areahidden">
                                        <label for=""
                                            class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>
                                        <div class="col-md-6">
                                            <input type="text" id="myInput" onkeyup="searchFunction()"
                                                placeholder="search here..." class="form-control">
                                            <div id="myDiv" class="adminarea-overflow">
                                                @foreach ($areas as $area)
                                                    <div id="searcharea-{{ $area->areacode }}">
                                                        <input id="area-{{ $area->areacode }}"
                                                            @if ($user->area) @foreach ($user->area as $s_area) @if ($s_area->areacode == $area->areacode) checked @endif
                                                            @endforeach
                                                @endif type="checkbox" value="{{ $area->areacode }}"
                                                name="area[]" autofocus> <label
                                                    for="area-{{ $area->areacode }}">{{ $area->area }}</label>
                                            </div>
                                            @endforeach
                                        </div>
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
    <script>
        window.onload = function() {
            areaShow();
        };

        function areaShow() {
            let role = document.getElementById('admin_role');
            console.log(role);
            if (role.value == ASM) {
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
