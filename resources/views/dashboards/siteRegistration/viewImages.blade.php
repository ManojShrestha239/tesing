@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Visit History</h1>
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
                            @include('dashboards.dynamicNav.siteRegistration')
                        </nav>
                    </div>

                </div>

                <style>
                    .hostel-image-container {
                        height: 300px;
                        width: 100%;
                        position: relative;
                        margin-bottom: 64px;
                        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);

                    }

                    .hostel-image-container img {
                        position: absolute;
                        margin: auto;
                        width: 100%;
                        height: 100%;
                    }


                    .image-cards:hover .hostel-image-container .btn {
                        opacity: 1;
                        visibility: visible;
                        z-index: 5;
                    }

                    .image-cards:hover .image-overlay {
                        opacity: 0.63;
                        visibility: visible;
                    }

                    .hostel-image-container .btn {
                        opacity: 0;
                        transition: 350ms;
                        visibility: hidden;
                    }

                    .image-overlay {
                        background: black;
                        position: absolute;
                        top: 0;
                        height: 300px;
                        width: 100%;
                        opacity: 0;
                        visibility: none;
                        transition: 350ms;
                    }

                    .hostel-image-adding-container {
                        height: 300px;
                        width: 300px;
                        margin-bottom: 64px;
                        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    .hostel-image-adding-container img {
                        height: 100px;
                        width: 100px;

                    }
                </style>

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
                                        <i class="fa fa-paint-brush font-red"></i>
                                        <span
                                            class="caption-subject font-red bold uppercase">{{ $siteRegistration->house_hold_name }}
                                            House Images </span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        @if (!empty($siteRegistration->images) && $siteRegistration->images->count())
                                            @foreach ($siteRegistration->images as $image)
                                                <div
                                                    class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 image-cards">
                                                    <div class="hostel-image-container">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                                            alt="" srcset="">
                                                        <div class="image-overlay"></div>
                                                        <div
                                                            style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                                                            <a href="{{ asset('storage/' . $image->image_path) }}"
                                                                target="_blank" class="btn btn-primary">View</a>
                                                            <form
                                                                action="{{ route('site-registration.destroy', $image->id) }}"
                                                                method="POST" style="display:inline;">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Image Uploaded</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
