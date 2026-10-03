@extends('dashboards.blank')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/maps.css') }}">
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Dealer Location</h1>
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
                            @include('dashboards.dynamicNav.dealer')
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
                                        <span class="caption-subject font-red bold uppercase">Dealers Map</span>
                                    </div>

                                </div>
                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif
                                <!-- dealers maps  -->
                                <div class="maps">
                                    <div class="container-fluid map-container">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="province">Area</label>
                                                    <select onchange="dealerSelect()"
                                                        class="form-control fstdropdown-select selectpicker"
                                                        data-live-search='true' data-live-search-style="startsWith"
                                                        id="province">

                                                        <option value="">Select Areas</option>
                                                        @foreach ($dealers as $dealer)
                                                            <option value="{{ $dealer->areacode }}">{{ $dealer->area }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="district">Sub Area</label>
                                                    <select onchange="districtSelect()" class="form-control" id="district">
                                                        <option value="">Select Sub Area</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 maps-right">
                                            <div id="map-container">

                                            </div>
                                        </div>
                                    </div>

                                    <script async defer
                                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=initMap"></script>
                                    <script src="{{ asset('js/maps.js') }}"></script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
