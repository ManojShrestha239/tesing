
    <div class="page-content2">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <div class="breadcrumbs">
            <h1>Edit User</h1>
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
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Edit User</span>
                                    </div>

                                </div>

                            @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/dashboard/mobileuser/edit/{{ $dealer->id }}">
        {{ method_field('PATCH') }}
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-3 col-form-label text-right">{{ __('Full Name') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->full_name }}" name="name" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-3 col-form-label text-right">{{ __('Login User ID Code') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->user_code }}" name="user_code" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="next_phone" class="col-md-3 col-form-label text-right">{{ __('Address') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->address }}" name="address" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-md-3 col-form-label text-right">{{ __('Phone Number') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->phone }}" name="phone" required autofocus>
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


