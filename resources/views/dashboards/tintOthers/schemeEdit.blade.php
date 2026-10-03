

    <div class="page-content2">
    <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
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
                                        <span class="caption-subject font-red bold uppercase">Add New Scheme</span>
                                    </div>

                                </div>

                                @if(count($errors) > 0)
                                    @foreach($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

    <form action="/dashboard/scheme/update/{{$scheme->id}}" method="post">
    {{ method_field('PATCH') }}
    @csrf

    <div class="form-group row">
            <label for="title" class="col-md-3 col-form-label" style="margin-left:10px;">{{ __('Title') }}</label>
            <div class="col-md-4">
             <input type="text" value="{{$scheme->title}}" name="title" class="form-control" Required autofocus>
            </div>
        </div>
        <div class="form-group row">
            <label for="body" class="col-md-3 col-form-label" style="margin-left:10px;">{{ __('Message Body') }}</label>
            <div class="col-md-4">
             <input type="text" value="{{$scheme->content}}" name="body" class="form-control" Required autofocus>
            </div>
        </div>
        <div class="form-group row">
            <label for="start_date" class="col-md-3 col-form-label" style="margin-left:10px;">{{ __('Scheme End Date') }}</label>
            <div class="col-md-4">
             <input type="date" name="start_date" value="{{$scheme->starting_from}}" class="form-control" Required autofocus>
            </div>
        </div>
        <div class="form-group row">
            <label for="end_date" class="col-md-3 col-form-label" style="margin-left:10px;">{{ __('Scheme End Date') }}</label>
            <div class="col-md-4">
             <input type="date" name="end_date" value="{{$scheme->ending_on}}" class="form-control" Required autofocus>
            </div>
        </div>
        <div class="form-group row mb-0">
            <div class="col-md-8 col-md-offset-3">
                <button type="submit" class="btn btn-primary" style="width: 200px;">
                    {{ __('Save') }}
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

