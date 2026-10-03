@extends('dashboards.sessions.layout')

@section('content')
    <style>
        #goback:hover {
            background: red;
            border: 2px solid #f48220;
            cursor: pointer;
            transition: 0.4s;
        }

        #goback:hover h3 {
            color: white;
        }

        #goback:hover img {
            color: white;
        }

        #goback {
            border: 2px solid #0d2444;
            width: 100px;
            border-radius: 32px;
        }

        #goback img {
            margin-top: 6px;
            margin-left: 4px;
            margin-right: 4px;
        }

        #goback h3 {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 600;
        }

        #ndp-nepali-box {
            top: 398px !important;
        }
    </style>
    <div class="page-content">
        <div id="goback" onclick="goBack()">
            <img style="float:left;width:25px; height:25px;" onclick="goBack()"
                src={{ asset('assets/global/img/left-arrow-key.svg') }}>
            <h3> Back</h3>
        </div>
        <script>
            function goBack() {
                window.history.back();
            }
        </script> <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">


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
        <!-- END BREADCRUMBS -->
        <!-- BEGIN SIDEBAR CONTENT LAYOUT -->

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" fa fa-paint-brush font-red"></i>
                            <span class="caption-subject font-red bold uppercase">EDIT NOTICE</span>
                        </div>

                    </div>

                    <form method="POST" action="/notice/{{ $notice->id }}" enctype="multipart/form-data">
                        {{ method_field('PATCH') }}
                        @csrf
                        <div class="form-group row">
                            <label for="user" class="col-md-3 col-form-label text-right">{{ __('User') }}</label>
                            <div class="col-md-6">
                                <select name="user" class="form-control">
                                    <option value="">All</option>
                                    @foreach ($users as $user)
                                        <option @if ($notice->user_id == $user->id) selected @endif
                                            value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-right">{{ __('Title') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="title" value="{{ $notice->title }}" class="form-control"
                                    required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user" class="col-md-3 col-form-label text-right">{{ __('Notice') }}</label>
                            <div class="col-md-6">
                                <textarea rows="10" class="col-md-12" name="notice">{{ $notice->notice }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="expiry_date"
                                class="col-md-3 col-form-label text-right">{{ __('Expiry Date') }}</label>

                            <div class="col-md-6">
                                <input type="text" id="nepaliDateFa"
                                    value="{{ $notice->expiry_date ?? old('expiry_date') }}" autocomplete="off"
                                    name="expiry_date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar"
                                    required>
                            </div>

                            <span class="field-validation-valid" data-valmsg-for="education"
                                data-valmsg-replace="true"></span>
                            @if ($errors->has('expiry_date'))
                                <span class="help-block required">
                                    <strong>{{ $errors->first('expiry_date') }}</strong>
                                </span>
                            @endif
                            @if (session('status'))
                                <span class="help-block required">
                                    <strong>{{ session('status') }}</strong>
                            @endif
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-right">{{ __('PDF/Image') }}</label>
                            <div class="col-md-6">
                                <input type="file" name="file[]"
                                    accept="application/pdf,image/jpeg,image/jpg,image/png,image/svg,image/webp"
                                    value="{{ $notice->file }}" class="form-control" autofocus multiple>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-primary" style="width: 200px;">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            @endsection
