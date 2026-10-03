@extends('dashboards.blank')

@section('content')
<style>
    #goback:hover {
        /* background:red; */
        border: 2px solid #f48220;
        cursor: pointer;
        transition: 0.4s;
    }

    #goback:hover h3 {
        /* color:white; */
    }

    #goback:hover img {
        /* color:white; */
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

    .select-area {
        height: 150px !important;
        overflow: auto;
        width: 49.1%;
    }
</style>
<div class="page-content">

    <!-- <form action="" method="get"></form> -->
    <div id="goback" onclick="goBack()">
        <img style="float:left;width:25px; height:25px;" onclick="goBack()" src={{ asset('assets/global/img/left-arrow-key.svg') }}>
        <h3> Back</h3>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
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

    <div class="page-content-container">
        <div class="page-content-row" id="replecable">
            {{-- <div class="page-sidebar">
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

                                <li><a href="/dashboard/area">Add New Area </a></li>
                                <li><a href="/dashboard/areas">List Area </a></li>
                                <li class="active"><a>Sub-area Edit </a></li>


                            </ul>

                        </nav>
                    </div>

                </div> --}}

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
                                    <i class=" icon-map font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Edit Sub Area</span>
                                </div>

                            </div>

                            @if(count($errors) > 0)
                            @foreach($errors->all() as $error)
                            <p class="alert alert-danger">{{ $error }}</p>
                            @endforeach
                            @endif

                            <form method="POST" action="/subarea/{{ $subarea->id }}">
                                {{ method_field('PATCH') }}
                                @csrf

                                <div class="form-group row">
                                    <label for="name" class="col-md-3 col-form-label text-right">{{ __('Subarea') }}</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{ $subarea->subarea }}" name="subarea" required autofocus>
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
                            <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection