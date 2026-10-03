@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Set Distance Limit</h1>

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
                            @include('dashboards.dynamicNav.miscellaneousNav')
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
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Set New Limit</span>
                                    </div>

                                </div>

                                <form method="POST" action="{{ route('checkout-time.store') }}">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="checkoutTime"
                                            class="col-md-3 col-form-label text-right">{{ __('Specific Time For Checkout') }}</label>

                                        <div class="col-md-4">
                                            <input type="time" @error('checkoutTime') is-invalid @enderror"
                                                id="checkoutTime" name="checkoutTime"
                                                value="{{ old('checkoutTime', $checkoutTime ?? '00:00') }}"
                                                aria-invalid="@error('checkoutTime') true @else false @enderror"
                                                aria-describedby="checkoutTimeHelp checkoutTimeError" required autofocus>
                                            <small id="checkoutTimeHelp" class="form-text text-muted">24-hour format
                                                (HH:MM)</small>
                                            @error('checkoutTime')
                                                <span id="checkoutTimeError" class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SIDEBAR CONTENT LAYOUT -->
@endsection
